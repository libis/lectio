<?php
$key = get_option('alma_import_api_key');
$proxy = get_option('alma_import_proxy');
$view = get_view();
?>

<div class="field">
    <?php echo $view->formLabel('Alma API key', 'Alma API key'); ?>
    <div class="inputs">
        <?php echo $view->formText('key', $key, array('class' => 'textinput')); ?>
        <p class="explanation">
            You need an api key to communicate with the alma api.
        </p>
    </div>
</div>

<div class="field">
    <?php echo $view->formLabel('Proxy', 'Proxy'); ?>
    <div class="inputs">
        <?php echo $view->formText('proxy', $proxy, array('class' => 'textinput')); ?>
        <p class="explanation">
            Enter the proxy here if you need one.
        </p>
    </div>
</div>

