<div ng-app="LearzingLoginFormModule" ng-controller="LearzingLoginController">
    <form name="login_form" novalidate ng-submit="doLogin()">
        <fieldset>
        <legend>Login</legend>
            <div class="validation-errors">
                <!-- TODO Make unified validation errors rendering -->
                {{ user.validationErrors | json }}
            </div>
            <label for="email">Email</label>
            <div class="row">
                <input type="email" name="email" ng-model="user.email" placeholder="Email" required/>
            </div>

            <label for="password">Password</label>
            <div class="row">
                <input type="password" name="password"
                    ng-model="user.password" ng-minlength="{6}" ng-maxlength="{100}"
                    placeholder="Password" required/>
            </div>

            <button type="submit" class="button radius"
                ng-disabled="login_form.$invalid || isUnchanged(user)">Login</button>
        </fieldset>
    </form>
</div>

<script type="text/javascript">
    var LearzingLoginFormModule = angular.module('LearzingLoginFormModule', ['LearzingSDKModule']);

    LearzingLoginFormModule.controller('LearzingLoginController', ['$scope', 'LEARZ',
        function($scope, LEARZ) {

            $scope.reset = function() {
                $scope.user = { email : "", password : "" };
            };

            $scope.reset();

            $scope.doLogin = function() {
                LEARZ.auth.login($scope.user.email, $scope.user.password, $scope._doLoginCallback);
            };

            $scope._doLoginCallback = function(response) {
                if (response.status === LEARZING_STATUS_SUCCESS) {
                    document.location = "<?php echo $this->createUrl('site/index'); ?>";
                } else {
                    /*alert("Errors have occured: \n" +
                      response.texts.toString() + "\n" +
                      "Learzing API status: " + response.status);*/
                    $scope.user.validationErrors = response.texts;                    
                }
            };
        }
    ]);
</script>