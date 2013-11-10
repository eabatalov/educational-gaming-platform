<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id' => 'signup-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'focus' => array($model,'name'),
    'action' => $this->createUrl('Signup'),
)); ?>
 
    <?php echo $form->errorSummary($model); ?>
    <div class="row">
            <?php echo $form->labelEx($model, "name")?>
            <?php echo $form->textField($model, "name")?>
            <?php echo $form->error($model,'name'); ?>
    </div>
    <div class="row">
            <?php echo $form->labelEx($model, "surname")?>
            <?php echo $form->textField($model, "surname")?>
            <?php echo $form->error($model,'surname'); ?>
    </div>
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
<?php echo CHtml::submitButton('Sign Up'); ?>
</div>
 
<?php $this->endWidget(); ?>
</div><!-- form -->

<?php
$this->widget('application.modules.hybridauth.widgets.renderProviders');
?>