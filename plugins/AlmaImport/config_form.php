<?php
$key = get_option('alma_import_api_key');

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

