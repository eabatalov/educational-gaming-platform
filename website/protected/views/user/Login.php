<div class="form">
    <div class="row">
        <label for="email">Email</label>
        <input type="text" name="email" id="input_email"/>
    </div>
    <div class="row">
        <label for="password">Password</label>
        <input type="password" name="password" id="input_password"/>
    </div>
</div>

<a class="btn" onclick="doLogin();">Login</a>

<script type="text/javascript">
    function doLogin() {
        var email = $("#input_email").val();
        var password = $("#input_password").val();
        LEARZ.auth.login(email, password, doLoginCallback);
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