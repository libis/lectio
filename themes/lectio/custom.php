<?php

function libis_get_news($tag){
    $items = get_records('Item',array('type'=>'news','tag'=>'home'),3);
    if(!$items):
        return "<p>There is no recent news.</p>";
    endif;
    foreach($items as $item):?>
        <div class="news-item">	
            <span class="date"><?php echo metadata($item,array('Dublin Core','Date'));?></span> - <h3><?php echo link_to($item, 'show', strip_formatting(metadata($item,array('Dublin Core','Title')))); ?></h3>
        </div>        
    <?php endforeach;
}

function libis_get_events($tag){
    $items = get_records('Item',array('type'=>'event','tag'=>'home'),3);
    if(!$items):
        return "<p>There is no recent event.</p>";
    endif;
    foreach($items as $item):?>
        <div class="event-item">	
            <span class="date"><?php echo metadata($item,array('Dublin Core','Date'));?></span> - <h3><?php echo link_to($item, 'show', strip_formatting(metadata($item,array('Dublin Core','Title')))); ?></h3>
        </div>        
    <?php endforeach;
}

