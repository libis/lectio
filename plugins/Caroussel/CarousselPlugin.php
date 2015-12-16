<?php
/**
* @version $Id$
*
* @package Caroussel
*/
/**
* Caroussel plugin class
**/
// Define Constants.
define('CAROUSSEL_PLUGIN_DIR', dirname(__FILE__));
define('CAROUSSEL_IMAGE_DIR', PUBLIC_THEME_DIR."/".get_option("public_theme").'/images/caroussel');
define('CAROUSSEL_IMAGE_WEB', WEB_THEME."/".get_option("public_theme").'/images/caroussel');

require_once CAROUSSEL_PLUGIN_DIR . '/helpers/CarousselFunctions.php';

class CarousselPlugin extends Omeka_Plugin_AbstractPlugin
{
    // Define Hooks
    protected $_hooks = array(
    'install',
    'uninstall',
    'initialize',
    'public_head'
    );

    public function hookInstall(){
        //create a directory for the images
        if (!file_exists(CAROUSSEL_IMAGE_DIR)) {
            mkdir(CAROUSSEL_IMAGE_DIR, 0777, true);
        }          
    }

    public function hookUninstall(){
        
    }
    
    public function hookPublicHead($args){
        queue_js_file('jquery.cycle2.min');
    }
    
    public function hookInitialize()
    {
        get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'Caroussel_View_Helper_');
    }
}