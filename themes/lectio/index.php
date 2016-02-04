<?php echo head(array('bodyid'=>'home')); ?>

<div id="intro-text">
    <?php if (get_theme_option('Homepage Text')): ?>
    <?php echo get_theme_option('Homepage Text'); ?>
    <?php endif; ?>
</div>

<div class="news">
    <h2>News</h2>
    <?php echo libis_get_news('home');?>
    <p class="view-items-link"><a href='<?php echo url('solr-search?q=&facet=itemtype:"News"');?>'>read more</a></p> 

    <h2>Events</h2>
    <?php echo libis_get_events('home');?>
    <p class="view-items-link"><a href='<?php echo url('solr-search?q=&facet=itemtype:"Event"');?>'>read more</a></p> 
</div>

<?php echo libis_get_simple_page_content('home-icons');?>

<?php fire_plugin_hook('public_content_top', array('view'=>$this)); ?>

<?php fire_plugin_hook('public_home', array('view' => $this)); ?>

<?php echo foot(); ?>
