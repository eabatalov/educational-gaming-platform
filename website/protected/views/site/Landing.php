<div ng-app="LearzingLandingPageModule">
    <h1 style="text-align: center;">Under development</h1>
    <br/>
    <br/>
    <img src="/media/images/pl_team_lbl.png" alt="Img" height="342" width="368" style="float: right;">
    <h2>
            Multiplayer?
            Social?<br/>
            Attention catching?<br/>
            It's all about <b>education!</b>
    </h2>
    <br/>
    <br/>
    <?php if(!LearzingAuth::isGuest()) { ?>
        <h3>Play that educational games!</h3>
        <?php echo CHtml::link("The Games", $this->createUrl("games/catalog"),
                array("class" => "btn"));?>
        <br/>
        <h3>Have a look at your profile here</h3>
        <?php echo CHtml::link("Profile",
                $this->createUrl("user/ShowUserProfile", array("userid" => NULL)),
                array("class" => "btn"));?>
        <br/>
        <h3>Our tiny Google</h3>
        <?php echo CHtml::link("Search", $this->createUrl("Search"), array("class" => "btn")); ?>
        <br/>
        <div  ng-controller="LearzingLogoutController">
            <h3>Staying for too long? Try brand new functionality!</h3>
            <a class="btn" href="" ng-click="doLogout()">Logout</a>
        </div>
    <?php } else { ?>
        <h2>Signup to checkout some cool stuff!</h2>
        <?php echo CHtml::link("Signup", $this->createUrl("user/signup"), array("class" => "btn")); ?>
        <br/>
        <br/>
        <h3>Already a member?</h3>
        <?php echo CHtml::link("Login", $this->createUrl("user/login"), array("class" => "btn")); ?>
    <?php
    }
    ?>
</div>

<script type="text/javascript">
    var LearzingLandingPageModule =
        angular.module('LearzingLandingPageModule', ['LearzingLogoutModule']);
</script>