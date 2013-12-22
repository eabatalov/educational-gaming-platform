<div class="form">
    <div class="row">
        <label for="email">Email</label>
        <input type="text" name="email" id="input_email"/>
    </div>
    <div class="row">
        <label for="name">Name</label>
        <input type="text" name="name" id="input_name"/>
    </div>
    <div class="row">
        <label for="surname">Surname</label>
        <input type="text" name="surname" id="input_surname"/>
    </div>
    <div class="row">
        <label for="password">Password</label>
        <input type="password" name="password" id="input_password"/>
    </div>
    <div class="row">
        <a class="btn" onclick="doSignup();">Signup</a>
    </div>
</div>

<script type="text/javascript">
    newUser = null;
    newUserPassword = null;

    function doSignup() {
        var email = $("#input_email").val();
        var password = $("#input_password").val();
        var name = $("#input_name").val();
        var surname = $("#input_surname").val();

        newUser = new User(email, name, surname);
        newUserPassword = password;

        LEARZ.user.register(newUser, newUserPassword, doSignupCallback);
    }

    function doSignupCallback(response) {
          if (response.status === LEARZING_STATUS_SUCCESS) {
              LEARZ.auth.login(newUser.email, newUserPassword, doLoginCallback);
          } else {
              alert("Errors have occured: \n" +
                response.texts.toString() + "\n" +
                "Learzing API status: " + response.status);
          }
    }

    function doLoginCallback(response) {
        if (response.status === LEARZING_STATUS_SUCCESS) {
              document.location = "<?php echo $this->createUrl('site/index'); ?>";
          } else {
              alert("Errors have occured: \n" +
                response.texts.toString() + "\n" +
                "Learzing API status: " + response.status);
          }
    }
</script>

<?php
//$this->widget('application.modules.hybridauth.widgets.renderProviders', array('action' => 'signup'));
?>