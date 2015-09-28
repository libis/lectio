<div class="element-set">
<?php $exceptions = array('Source','Type','Coverage'); ?>
<?php foreach ($elementsForDisplay as $setName => $setElements): ?>
    <?php if ($showElementSetHeadings): ?>
    <h2><?php echo html_escape(__($setName)); ?></h2>
    <?php endif; ?>
    <?php foreach ($setElements as $elementName => $elementInfo): ?>
    <div id="<?php echo text_to_id(html_escape("$setName $elementName")); ?>" class="element">
        <h3><?php echo html_escape(__($elementName)); ?></h3>
        <?php if(!in_array(html_escape(__($elementName)),$exceptions)):?>
        <?php foreach ($elementInfo['texts'] as $text): ?>
            <div class="element-text"><?php echo $text; ?></div>
        <?php endforeach; ?>
        <?php else:?>
            <div class="element-text">
            <?php echo implode("; ", $elementInfo['texts']); ?>
            </div>    
        <?php endif;?>    
    </div><!-- end element -->
    <?php endforeach; ?>

<?php endforeach;?>
</div><!-- end element-set -->

