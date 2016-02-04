jQuery(window).load(function() {  
    
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
