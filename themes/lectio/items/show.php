<?php echo head(array('title' => metadata('item', array('Dublin Core', 'Title')),'bodyclass' => 'items show')); ?>

<h1><?php echo metadata('item', array('Dublin Core', 'Title')); ?></h1>
<!-- If the item belongs to a collection, the following creates a link to that collection. -->
    <?php if (metadata('item', 'Collection Name')): ?>
    <div id="collection" class="element">
    <!-- voorlopig link_to_collection_for_item() verwijdert tot issue 28 is opgelost -->
        <h2><?php echo __('Collection'); ?>, <?php echo metadata('item', 'Collection Name'); ?></h2>
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

    <?php if ((get_theme_option('Item FileGallery') == 0) && metadata('item', 'has files')): ?>
    <?php echo files_for_item(array('imageSize' => 'fullsize')); ?>
    <?php endif; ?>
    
    <?php echo all_element_texts('item'); ?> 

</div><!-- end primary -->



<ul class="item-pagination navigation">
    <li id="previous-item" class="previous"><?php echo link_to_previous_item_show(); ?></li>
    <li id="next-item" class="next"><?php echo link_to_next_item_show(); ?></li>
</ul>

<?php echo foot(); ?>
