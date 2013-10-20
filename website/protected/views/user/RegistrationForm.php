<div class="form">
<?php echo CHtml::beginForm(); ?>
 
    <div>
        <p>
            <?php echo CHtml::label("name: ", "name")?>
            <?php echo CHtml::textField("name")?>
        </p>
        <p>
            <?php echo CHtml::label("password: ", "pass")?>
            <?php echo CHtml::passwordField("pass")?>
        </p>
    </div>
 
<div class="row submit">
<?php echo CHtml::submitButton('Finish'); ?>
</div>
 
<?php echo CHtml::endForm(); ?>
</div><!-- form -->