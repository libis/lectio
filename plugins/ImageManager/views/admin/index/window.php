<?php     
    queue_css_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css');
    queue_css_file('theme');
    queue_css_file('elfinder.min');
    echo head_css();    
        
    queue_js_file('elFinder/js/elFinder.min');
    queue_js_file('vendor/tiny_mce/tiny_mce_popup');     
    queue_js_file('imageManager');    
    echo head_js();
?>
<script type="text/javascript">
    var FileBrowserDialogue = {
    init: function() {
      // Here goes your code for setting your custom things onLoad.
    },
    mySubmit: function (URL) {
      var win = tinyMCEPopup.getWindowArg('window');

      // pass selected file path to TinyMCE
      win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = URL;

      // are we an image browser?
      if (typeof(win.ImageDialog) != 'undefined') {
        // update image dimensions
        if (win.ImageDialog.getImageData) {
          win.ImageDialog.getImageData();
        }
        // update preview if necessary
        if (win.ImageDialog.showPreviewImage) {
          win.ImageDialog.showPreviewImage(URL);
        }
      }

      // close popup window
      tinyMCEPopup.close();
    }
  }

  jQuery().ready(function() {
    var elf = jQuery('#elfinder').elfinder({
      // set your elFinder options here
      url: '<?php echo url('/image-manager/connector') ?>',  // connector URL
      getFileCallback: function(file) { // editor callback
        // Require `commandsOptions.getfile.onlyURL = false` (default)
        FileBrowserDialogue.mySubmit(file.url); // pass selected file path to TinyMCE 
      }
    }).elfinder('instance');      
  });
</script>

<div id="elfinder"></div>