<?php

/**
* @package omeka
* @subpackage rosetta plugin
* @copyright 2014 Libis.be
*/
define('ROSETTA_DIR', dirname(__FILE__));

//HELPERS
require_once ROSETTA_DIR.'/helpers/RosettaPluginFunctions.php';
require_once ROSETTA_DIR.'/libraries/Rosetta/File/Derivative/Strategy/Rosetta.php';
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
        'before_save_file',
        'define_acl',
        'admin_items_form_files'
    );

    protected $_filters = array(
        'api_resources',
        'api_import_omeka_adapters',
        'api_extend_items'
    );


    function hookInstall()
    {
        set_option('rosetta_proxy','');
        set_option('rosetta_resolver','');
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall(){
        delete_option('rosetta_proxy');
        delete_option('rosetta_resolver');
    }

    /**
     * Display the config form.
     */
    public function hookConfigForm() {
        require dirname(__FILE__) .'/config_form.php';
    }

    /**
     * process the config form
     */
    public function hookConfig() {
        //get the POST variables from config_form and set them in the DB
        set_option('rosetta_proxy',$_POST['proxy']);
        $url = $_POST['resolver'];
        $url = substr($str, -1) == '/' ? '' : '/';
        set_option('rosetta_resolver',$url);
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

    /**
     * use pid to download file to tmp and save file information
     *
     * @param type $args
     * @return type
     */
    public function hookAfterSaveItem($args){

        $item = $args['record'];
        $pids=array();

        if($post = $args['post']):
            $post = $args['post'];

            if($post['known-pid']):
                $pids[] = $post['known-pid'];
            elseif($post['pid']):
                $pids[] = $post['pid'];
            endif;

            if(!empty($pids)):
                foreach($pids as $pid):
                    $obj = rosetta_download_image(get_option('rosetta_resolver').'/'.$pid);

                    file_put_contents('/tmp/'.$pid.'_resolver',$obj);

                    $file = new File();
                    $file->item_id = $item->id;
                    $file->filename = $pid.'_resolver';
                    $file->has_derivative_image = 1;
                    $file->mime_type = rosetta_get_mime_type($obj);
                    $file->original_filename = $pid;
                    $file->metadata = "";
                    $file->save();

                    //delete the tmp file
                    unlink('/tmp/'.$pid.'_resolver');
                endforeach;
            else:
                return false;
            endif;
        endif;
    }

    /**
     * load and save metadata of a rosetta object/file
     *
     * @param type $args
     * @return type
     */
    public function hookBeforeSaveFile($args){
        //only get metadata on file insert
        if (!$args['insert']) {
            return;
        }

        $file = $args['record'];

        $base_url = get_option('rosetta_resolver');
        $url = $base_url."/".$file->original_filename."/metadata";

        //insert metadata
        if($metadata = rosetta_get_metadata($url)):
            foreach($metadata as $key => $value):
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', ucfirst($key));

                if($element != null):
                    if(!$file->hasElementText('Dublin Core', ucfirst($key))):
                        $element = $file->getElement('Dublin Core', ucfirst($key));
                        if(is_array($value)):
                            foreach($value as $text):
                                $file->addTextForElement($element, $text);
                            endforeach;
                        else:
                            $file->addTextForElement($element, $value);
                        endif;
                    endif;
                endif;
            endforeach;
        endif;
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
    * add rosetta form to file form
    *
    * @param type $args
    */
    public function hookAdminItemsFormFiles($args){
        $item = $args['item'];
        echo rosetta_admin_form($item);
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
