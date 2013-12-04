<div>
    <img src="/media/images/pl_team_lbl.png" alt="Img" height="342" width="368" style="float: right;">
    <h1><?php echo Yii::app()->name;?></h1>
    <br/>
    <h2>
            Multiplayer?
            Social?<br/>
            Attention catching?<br/>
            It's all about <b>education!</b>
    </h2>
    <br/>
    <?php echo CHtml::link("Signup", $this->createUrl("user/signup"), array("class" => "btn")); ?>
    <br/>
    <h3>Already a member?</h3>
    <?php echo CHtml::link("Login", $this->createUrl("user/login"), array("class" => "btn")); ?>
    <br/>
    <h3>Staying for too long? Try brand new functionality!</h3>
    <?php echo CHtml::link("Logout", $this->createUrl("user/logout"), array("class" => "btn")); ?>

    <p>
    <?php echo "User id: " . Yii::app()->user->id;?>
    </p>
    <p>
    <?php echo "User name: " . Yii::app()->user->name;
          echo var_dump(AuthUtils::authCustomer(), true);
          //phpinfo();
    ?>
    </p>
</div>