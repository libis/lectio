<?php echo head(array('title' => metadata('item', array('Dublin Core', 'Title')),'bodyclass' => 'items show')); ?>

<h1><?php echo metadata('item', array('Dublin Core', 'Title')); ?></h1>
<!-- If the item belongs to a collection, the following creates a link to that collection. -->
    <?php if (metadata('item', 'Collection Name')): ?>
    <div id="collection" class="element">
    <!-- voorlopig link_to_collection_for_item() verwijdert tot issue 28 is opgelost -->
    <h2><?php echo __('Collection'); ?>, <a href='<?php echo url('/solr-search?q=&facet=collection:"'.metadata('item', 'Collection Name').'"'); ?>'><?php echo metadata('item', 'Collection Name');?></a></h2>
    </div>
    <?php endif; ?>
<aside id="sidebar">
    <!-- The following returns all of the files associated with an item. -->
    <?php if (metadata('item', 'has files')): ?>
    <div id="itemfiles" class="element">
        <div class="element-text"><?php echo files_for_item(); ?></div>
    </div>
    <?php endif; ?>

    <!-- rosetta items and other plugin output -->
    <?php if ($text = metadata('item', array('Item Type Metadata', 'Images'))): ?>
        <?php fire_plugin_hook('public_items_show', array('view' => $this, 'item' => $item, 'IE-link'=> $text)); ?>
    <?php else: ?>
        <?php fire_plugin_hook('public_items_show', array('view' => $this, 'item' => $item)); ?>
    <?php endif;?>

</aside>

<div id="primary">

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

        <?php if ($text = metadata('item', array('Item Type Metadata', 'Professor'),array('delimiter'=>'; '))): ?>
        <h3>Professor</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Dublin Core', 'Contributor'),array('delimiter'=>'<br/>'))): ?>
        <h3>Contributor</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Dublin Core', 'Provenance'),array('delimiter'=>'; '))): ?>
        <h3>Provenance</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Item Type Metadata', 'Content'),array('delimiter'=>'<br/>'))): ?>
        <h3>Content</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Dublin Core', 'Table Of Contents'),array('delimiter'=>'<br />'))): ?>
        <h3>Details</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Dublin Core', 'Alternative Title'),array('delimiter'=>'<br />'))): ?>
        <h3>Other titles</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Dublin Core', 'Description'),array('delimiter'=>'<br />'))): ?>
        <h3>Description</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Item Type Metadata', 'Illustrations'),array('delimiter'=>'<br />'))): ?>
        <h3>Illustrations</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Item Type Metadata', 'Notes'),array('delimiter'=>'<br />'))): ?>
        <h3>Notes</h3>
        <div class="element-text"><?php echo $text; ?></div>
        <?php endif; ?>

        <?php if ($files = $item->Files): ?>
            <a class='element-link' href=" <?php echo file_display_url($files[0], 'original');?>">&raquo; IMAGES</a><br>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Item Type Metadata', 'PDF'))): ?>
        <a class='element-link' href="<?php echo $text;?>">&raquo; PDF</a><br>
        <?php endif; ?>

        <?php if ($text = metadata('item', array('Item Type Metadata', 'LIMO'))): ?>
        <a class='element-link' href="<?php echo $text;?>">&raquo; LIMO</a><br>
        <?php endif; ?>

    </div><!-- end element -->

</div><!-- end element-set -->

</div><!-- end primary -->



<ul class="item-pagination navigation">
    <li id="previous-item" class="previous"><?php echo link_to_previous_item_show(); ?></li>
    <li id="next-item" class="next"><?php echo link_to_next_item_show(); ?></li>
</ul>

<?php echo foot(); ?>
