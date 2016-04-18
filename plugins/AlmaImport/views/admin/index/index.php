<?php echo head(); ?>
<div id="primary">
    <h1>Alma Import</h1>
    <div id="alma-import-form">
        <div id="form-instructions">
            <p>Insert a list of id's below, seperated by pipe.</p>
            <p>For example 9983524100101488|9983524100101488</p>
        </div>
        <?php echo flash(); ?>
        <form name="contact_form" id="contact-form"  method="post" enctype="multipart/form-data" accept-charset="utf-8">
            <?php echo $status;?>    
            <div class="field">
              <?php echo $this->formLabel('ids', 'A list of ids: '); ?>
              <div class='inputs'>
              <?php echo $this->formTextarea('ids', $ids, array('class'=>'textinput', 'rows' => '10')); ?>
              </div>
            </div>      

            <div class="field">
              <?php echo $this->formSubmit('send', 'Insert'); ?>
            </div>

        </form>
    </div>
</div>
<?php echo foot();
