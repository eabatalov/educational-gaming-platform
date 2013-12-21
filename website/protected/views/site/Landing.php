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
    <a class="btn" onclick="doLogout();">Logout</a>

    <p>
        <?php echo var_dump(LearzingAuth::getCurrentAccessToken(), true); ?>
    </p>
</div>

<script type="text/javascript">
    function doLogout() {
        LEARZ.auth.logout(doLogoutCallback);
    }

    function doLogoutCallback(response) {
        //TODO
        if (response.status === LEARZING_STATUS_SUCCESS) {
            document.location = "<?php echo $this->createUrl('site/index'); ?>";
        } else {
            alert("Errors have occured: \n" +
              response.texts.toString() + "\n" +
              "Learzing API status: " + response.status);
        }
    }
</script>