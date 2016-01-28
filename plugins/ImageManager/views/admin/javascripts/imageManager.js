jQuery(window).load(function() {   
    tinyMCE.init({
            // Assign TinyMCE a textarea:
            mode : 'exact',
            elements: 'simple-pages-text',
            // Add plugins:
            plugins: 'media,paste,inlinepopups',
            // Configure theme:
            theme: 'advanced',
            theme_advanced_toolbar_location: 'top',
            theme_advanced_toolbar_align: 'left',
            theme_advanced_buttons3_add : 'pastetext,pasteword,selectall',
            // Allow object embed. Used by media plugin
            // See http://www.tinymce.com/forum/viewtopic.php?id=24539
            media_strict: false,
            // General configuration:
            convert_urls: false, 
            file_browser_callback: elFinderBrowser            
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
});   