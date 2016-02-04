<?php
/**
 * Image Manager
 *
 * @copyright Libis (libis.be)
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

//require_once dirname(__FILE__) . '/helpers/ImageManagerFunctions.php';

/**
 * Image Manager plugin.
 */
class ImageManagerPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install', 'uninstall','initialize',
        'define_routes', 'config_form', 'config','admin_head','admin_footer');

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main');

     /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {        
        
    }


    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'ImageManager_View_Helper_');
    }

     /**
     * Add the routes for accessing simple pages by slug.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        // Add custom routes based on the page slug.
        $router = $args['router'];
	$router->addRoute(
	    'image-manager', 
	    new Zend_Controller_Router_Route(
	       "image-manager/", 
	        array('module' => 'image-manager')
	    )
	);
        
        $router = $args['router'];
	$router->addRoute(
	    'image-manager/connector', 
	    new Zend_Controller_Router_Route(
	       "image-manager/connector", 
	        array('module' => 'image-manager',
                      'controller' => 'index',
                      'action' => 'connector'
                    )
	    )
	);
        
        $router = $args['router'];
	$router->addRoute(
	    'image-manager/window', 
	    new Zend_Controller_Router_Route(
	       "image-manager/window", 
	        array('module' => 'image-manager',
                      'controller' => 'index',
                      'action' => 'window'
                    )
	    )
	);
    }
    
    public function hookAdminHead(){        
        //css
        queue_css_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css');
        queue_css_file('theme');
        queue_css_file('elfinder.min');
        //js
        //queue_js_url("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.js");
        queue_js_file('elFinder/js/elFinder.min');
        
        if (strpos('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],'/simple-pages/index/edit/') !== false) {
            queue_js_file('imageManager');
        }       
    }
    
    public function hookAdminFooter(){
        if (strpos('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],'/simple-pages/index/edit/') !== false) {
            $page = get_current_record('SimplePagesPage');
            $tiny_mce = $page->use_tiny_mce;
        }
        else{
            $tiny_mce =0;
        }
        
        ?>
        <script>
            jQuery(window).load(function(){
                tinyMCE.init({
                    // Assign TinyMCE a textarea:
                    mode : 'exact',
                    elements: '<?php if ($tiny_mce) echo 'simple-pages-text'; ?>',
                    // Add plugins:
                    plugins: 'media,paste,inlinepopups',
                    // Configure theme:
                    theme: 'advanced',
                    height : "480",
                    theme_advanced_toolbar_location: 'top',
                    theme_advanced_toolbar_align: 'left',
                    theme_advanced_buttons3_add : 'pastetext,pasteword,selectall',
                    // Allow object embed. Used by media plugin
                    // See http://www.tinymce.com/forum/viewtopic.php?id=24539
                    media_strict: false,
                    // General configuration:
                    convert_urls: false, 
                    file_browser_callback: elFinderBrowser,
                    init_instance_callback : myCustomInitInstance

                });

                // Add or remove TinyMCE control.
                jQuery('#simple-pages-use-tiny-mce').click(function() {
                    if (jQuery(this).is(':checked')) {
                        tinyMCE.execCommand('mceAddControl', true, 'simple-pages-text');
                        jQuery('#simple-pages-text').hide();

                    } else {
                        tinyMCE.execCommand('mceRemoveControl', true, 'simple-pages-text');
                        jQuery('#simple-pages-text').show();
                        jQuery('#simple-pages-text').css('visibility','visible');
                    }
                });
    
                function elFinderBrowser (field_name, url, type, win) {
                    var current_url = window.location.href; 
                    var split_url = current_url.split("/admin/");

                    tinyMCE.activeEditor.windowManager.open({

                      file: split_url[0] + '/admin/image-manager/window',// use an absolute path!
                      title: 'File Browser',
                      width: 900,  
                      height: 420,
                      inline: 'yes',    // This parameter only has an effect if you use the inlinepopups plugin!
                      popup_css: false, // Disable TinyMCE's default popup CSS
                      close_previous: 'no'

                    }, {
                       window: win,
                       input: field_name
                    });
                    return false;
                }
                function myCustomInitInstance(inst) {
                    jQuery('.mceEditor:last').hide();
                    jQuery('.mceEditor:first').show();
                }
        
            });
        </script>
        <?php
    }

    /**
     * Display the plugin config form.
     */
    public function hookConfigForm()
    {
        require dirname(__FILE__) . '/config_form.php';
    }

    /**
     * Set the options from the config form input.
     */
    public function hookConfig()
    {
        set_option('simple_pages_filter_page_content', (int)(boolean)$_POST['simple_pages_filter_page_content']);
    }

   
    /**
     * Add the Image Manager link to the admin main navigation.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Image Manager'),
            'uri' => url('image-manager')            
        );
        return $nav;
    }    
}
