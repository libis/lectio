<?php echo head(array('bodyid'=>'home')); ?>

<div id="intro-text">
    <?php if (get_theme_option('Homepage Text')): ?>
    <?php echo get_theme_option('Homepage Text'); ?>
    <?php endif; ?>
</div>

<div class="news">
    <h2>News</h2>
    <?php echo libis_get_news('home');?>
    <p class="view-items-link"><a href="<?php echo url('solr-search?q=test&facet=itemtype:"News"');?>">read more</a></p> 

    <h2>Events</h2>
    <?php echo libis_get_events('home');?>
    <p class="view-items-link"><a href="<?php echo url('solr-search?q=test&facet=itemtype:"Event"');?>">read more</a></p> 
</div>

<div class="icon"><img src="<?php echo img('icon3.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('laboratory-for-text-editing');?>">Laboratory for Text Editing</a></h2></div></div>
<div class="icon"><img src="<?php echo img('icon1.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('conferences');?>">Conferences</a></h2></div></div>
<div class="icon"><img src="<?php echo img('icon1.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('lectio-chair');?>">Lectio Chair</a></h2></div></div>
<div class="icon"><img src="<?php echo img('icon2.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('magister-dixit-project');?>">Magister Dixit Project</a></h2></div></div>
<div class="icon"><img src="<?php echo img('icon2.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('lectures');?>">Lectures</a></h2></div></div>
<div class="icon"><img src="<?php echo img('icon2.jpeg');?>"><div class='icon-title-box'><h2><a href="<?php echo url('lectio-series');?>">Lectio Series</a></h2></div></div>

<?php fire_plugin_hook('public_content_top', array('view'=>$this)); ?>

<?php fire_plugin_hook('public_home', array('view' => $this)); ?>

<?php echo foot(); ?>
