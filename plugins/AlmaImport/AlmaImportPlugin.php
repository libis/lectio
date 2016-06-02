<?php
/**
 * AlmaImport plugin class
 *
 * @copyright Libis,2016
 * @package AlmaImport
 */


class AlmaImportPlugin extends Omeka_Plugin_AbstractPlugin
{
    // Define Hooks
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_routes',
        'config_form',
        'config'
    );

    //Add filters
    protected $_filters = array(
        'admin_navigation_main'
    );

   public function hookInstall()
    {
        set_option('alma_import_api_key', '');
        set_option('alma_import_proxy', '');
    }

    public function hookUninstall()
    {
        delete_option('alma_import_api_key');
        delete_option('alma_import_proxy');
    }

    /**
     * Adds routes.
     **/
    function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'alma-import', 
            new Zend_Controller_Router_Route(
                'alma-import/', 
                array('module' => 'alma-import')
            )
        );

    }

    public function hookConfigForm() 
    {
        include 'config_form.php';
    }

    public function hookConfig($args)
    {
        $post = $args['post'];
        set_option('alma_import_api_key', $post['key']);
        set_option('alma_import_proxy', $post['proxy']);
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Alma import'),
            'uri' => url('alma-import')            
        );
        return $nav;
    }  
}
