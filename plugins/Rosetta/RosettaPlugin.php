<?php

/**
* @package omeka
* @subpackage rosetta plugin
* @copyright 2014 Libis.be
*/
define('ROSETTA_DIR', dirname(__FILE__));

//HELPERS
require_once ROSETTA_DIR.'/helpers/RosettaPluginFunctions.php';

class RosettaPlugin extends Omeka_Plugin_AbstractPlugin
{
    //'admin_items_show_sidebar',
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_routes',
        'config_form',
        'config',        
        'after_save_item',
        'define_acl',
        'admin_head',
        'admin_items_show', 
        'public_items_show'
    );

    protected $_filters = array(
        'admin_items_form_tabs',
        'api_resources',
        'api_import_omeka_adapters',
        'api_extend_items'
    );


    function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS $db->RosettaObject (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `item_id` BIGINT UNSIGNED NOT NULL ,
        `pid` VARCHAR(100) NOT NULL ,
        `label` VARCHAR(25)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        set_option('rosetta_proxy','');
        set_option('rosetta_resolver','');
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall(){
        // Drop the url table.
        $db = get_db();
        $db->query("DROP TABLE $db->RosettaObject");

        delete_option('rosetta_proxy');
        delete_option('rosetta_resolver');
    }
    //link to config_form.php
    public function hookConfigForm() {        
        require dirname(__FILE__) .'/config_form.php';
    }
    //process the config_form
    public function hookConfig() {
        //get the POST variables from config_form and set them in the DB
        set_option('rosetta_proxy',$_POST['proxy']);

        set_option('rosetta_cgi',$_POST['cgi']);

        set_option('rosetta_resolver',$_POST['resolver']);
    }

    /**
    * rosetta define_routes hook
    */
    public function hookDefineRoutes($args){

        $router = $args['router'];
        $router->addRoute(
            'rosettaActionRoute',
            new Zend_Controller_Router_Route(
                'rosetta/index/:action/:id',
                array(
                    'module'        => 'rosetta',
                    'controller'    => 'index'
                    ),
                array('id'          => '\d+')
             )
         );
         $router->addRoute(
            'rosettaIndexRoute',
            new Zend_Controller_Router_Route(
                'rosetta/index/:id',
                array(
                    'module'        => 'rosetta'
                    ),
                array('id'          => '\d+')
             )
         );
    }

    public function hookAfterSaveItem($args){

        if (!($post = $args['post'])) {
            return;
        }
        
        $item = $args['record'];
        $post = $_POST;
        
        if ($post['known-pid']) {
            //save to db
            $obj = new RosettaObject;
            $obj->item_id = $item->id;
            $obj->pid = $post['known-pid'];
            $obj->save();
        
        }
        if ($post['pid']) {
            //save to db
            $obj = new RosettaObject;
            $obj->item_id = $item->id;
            $obj->pid = $post['pid'];
            $obj->save();
        }
        
    }
    
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Rosetta_RosettaObjects');
        
        $acl->allow(null, 'Rosetta_RosettaObjects',
        array('show', 'summary', 'showitem', 'browse', 'tags'));

        // Allow contributors everything but editAll and deleteAll.
        $acl->allow('contributor', 'Rosetta_RosettaObjects',
        array('add', 'add-page', 'delete-page', 'delete-confirm', 'edit-page-content',
            'edit-page-metadata', 'item-container', 'theme-config',
            'editSelf', 'deleteSelf', 'showSelfNotPublic'));

        $acl->allow(null, 'Rosetta_RosettaObjects', array('edit', 'delete'),
        new Omeka_Acl_Assert_Ownership);
    }
    
    /**
     * Render the object on admin show page.
     */
    public function hookAdminItemsShow($args)
    {
        $images = rosetta_get_images($args['item']);
        if(!$images):
            return;
        endif;

        $html = '<div id="rosetta_objects" class="element">';
        foreach($images as $image):
            $html .= '<img src="'.$image.'">';
        endforeach;
        $html .= '</div>';
        echo $html;
    }
    
    /**
     * Render the object on public show page.
     */
    public function hookPublicItemsShow($args)
    {
        if (array_key_exists('item', $args)) {
            $objects = rosetta_get_rosetta_objects($args['item']);
            if(!$objects):
                return;
            endif;
            
            $html = '<div id="rosetta_objects" class="element">';
            foreach($objects as $object):
                if(isset($args['IE-link'])):
                    $html .= '<a href="'.$args['IE-link'].'"><img src="'.$object->get_thumb().'"></a>';
                else:                    
                    $html .= '<a href="'.$object->get_viewer().'"><img src="'.$object->get_thumb().'"></a>';
                endif;
            endforeach;
            $html .= '</div>';
            echo $html;
        }
    }

    public function hookAdminHead($args)
    {
        $view = $args['view'];
        queue_css_file(array('pagination', 'style'));
        queue_js_file('jquery.pagination'); 
    }

    /**
    * Add a tab to the edit item page
    * @return array
    **/
    public function filterAdminItemsFormTabs($tabs,$args){
        $item = $args['item'];
        $tabs['Rosetta'] = rosetta_admin_form($item);     
        
        return $tabs;
    }


    public function filterApiResources($apiResources)
    {
        $apiResources['rosetta_objects'] = array(
            'record_type' => 'RosettaObject', 
            'actions' => array('get','index','put','post','delete')
        );       

        return $apiResources;    
    }
    
    
    /**
    * Add rosetta urls to item API representations.
    *
    * @param array $extend
    * @param array $args
    * @return array
    */
    public function filterApiExtendItems($extend, $args)
    {
        $item = $args['record'];
        $objects = $this->_db->getTable('RosettaObject')->findBy(array('item_id' => $item->id));
        if (!$objects) {
            return $extend;
        }
        $object = $objects[0];
        $i=1;
        foreach($objects as $object):
            $extend['rosetta_objects'] = array(
                'count'=>$i,        
                'url' => Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl("/rosetta_objects/{$object->id}"),
                'resource' => 'rosetta_objects',
                'pid' => $object->id,
                'item_id' => $item->id
            );
            $i++;
        endforeach;
        return $extend;
    }
    
    public function filterApiImportOmekaAdapters($adapters, $args)
    {
        $adapter = new ApiImport_ResponseAdapter_Omeka_GenericAdapter(null, $args['endpointUri'], 'RosettaObject');
        $adapter->setResourceProperties(array('item' => 'Item'));
        $adapters['rosetta_objects'] = $adapter;
        return $adapters;
    }
}
?>