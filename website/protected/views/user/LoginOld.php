<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'focus' => array($model,'email'),
        'action' => $this->createUrl('Login'),
    )); ?>
    <?php echo $form->error($model, ModelObject::FATAL_ERROR_FIELD_NAME); ?>
    <div class="row">
            <?php echo $form->labelEx($model, "email")?>
            <?php echo $form->textField($model, "email")?>
            <?php echo $form->error($model,'email'); ?>
    </div>
    <div class="row">
            <?php echo $form->labelEx($model, "password")?>
            <?php echo $form->passwordField($model, "password")?>
            <?php echo $form->error($model,'password'); ?>
    </div>
 
<div class="row submit">
<?php echo CHtml::submitButton('Login'); ?>
</div>
 
<?php $this->endWidget(); ?>
</div><!-- form -->

<?php
$this->widget('application.modules.hybridauth.widgets.renderProviders', array('action' => 'login'));
?>