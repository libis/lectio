<div class="element-set">
<?php $exceptions = array('Source','Type','Coverage'); ?>
    
    <div  class="element">
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Title'),array('delimiter'=>'; '))): ?>
        <h3>Title</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Source'),array('delimiter'=>'; '))): ?>
        <h3>Source</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Item Type Metadata', 'Call number'),array('delimiter'=>'; '))): ?>
        <h3>Call number</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Date'),array('delimiter'=>'; '))): ?>
        <h3>Date</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Creator'),array('delimiter'=>'; '))): ?>
        <h3>Student</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Mediator'),array('delimiter'=>'; '))): ?>
        <h3>Professor</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Contributor'),array('delimiter'=>'; '))): ?>
        <h3>Contributor</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Provenance'),array('delimiter'=>'; '))): ?>
        <h3>Provenance</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Item Type Metadata', 'Content'),array('delimiter'=>'; '))): ?>
        <h3>Content</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Table Of Contents'),array('delimiter'=>'; '))): ?>
        <h3>Details</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Alternative Title'),array('delimiter'=>'; '))): ?>
        <h3>Other titles</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Description'),array('delimiter'=>'; '))): ?>
        <h3>Description</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>        
        
        <?php if ($text = metadata('item', array('Item Type Metadata', 'Illustrations'),array('delimiter'=>'; '))): ?>
        <h3>Illustrations</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Dublin Core', 'Abstract'),array('delimiter'=>'; '))): ?>
        <h3>Notes</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Item Type Metadata', 'Images'))): ?>
        <a class='element-link' href="<?php echo $text;?>">&raquo; IMAGES</a><br>
        <?php endif; ?>
 
        <?php if ($text = metadata('item', array('Item Type Metadata', 'PDF'))): ?>
        <a class='element-link' href="<?php echo $text;?>">&raquo; PDF</a><br>
        <?php endif; ?>
        
        <?php if ($text = metadata('item', array('Item Type Metadata', 'LIMO'))): ?>
        <a class='element-link' href="<?php echo $text;?>">&raquo; LIMO</a><br>
        <?php endif; ?>
        
    </div><!-- end element -->
    
</div><!-- end element-set -->

