 <?php 
    //$type = metadata($item, array('Dublin Core', 'Title');
    $tags = explode(',',tag_string($item,null));
    $class="";
    if(in_array('news',$tags)):
        $class = 'news-item';
    endif;
    if(in_array('event',$tags)):
        $class = 'event-item';
    endif;
 ?>
<div class="<?php echo $class;?>">
    <?php
    $title = metadata($item, array('Dublin Core', 'Title'));
    ?>
    
    <?php if($date = metadata($item, array('Dublin Core', 'Date'))):?>
        <span class="date"><?php echo $date; ?></span> - 
    <?php endif;?>
    
    <h3><?php echo link_to($item, 'show', strip_formatting($title)); ?></h3>
    

    	
</div>
