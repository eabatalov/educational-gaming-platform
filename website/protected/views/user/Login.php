<div ng-app="LearzingLoginFormModule" ng-controller="LearzingLoginController">
    <form name="loginForm" novalidate ng-submit="loginFormSubmit()">
        <fieldset>
        <legend>Login</legend>
            <div class="validation-errors" ng-hide="user.validationErrors.length === 0">
                <div>Please fix the following errors:</div>
                <ul>
                <li ng-repeat="validationError in user.validationErrors">
                    {{ validationError }}
                </li>
                </ul>
            </div>
            <label for="uEmail">Email</label>
            <div class="row">
                <input type="email" name="uEmail" ng-model="user.email" placeholder="Email" required/>
                <ul ng-show="showLoginFormValidationErrors && loginForm.uEmail.$invalid">
                    <li ng-show="loginForm.uEmail.$error.required">Please fill your email</li>
                    <li ng-show="loginForm.uEmail.$error.email">This email is not valid</li>
                </ul>
            </div>

            <label>Password</label>
            <div class="row">
                <input type="password" name="uPassword"
                    ng-model="user.password" ng-minlength="6" ng-maxlength="100"
                    placeholder="Password" required/>
                <ul ng-show="showLoginFormValidationErrors && loginForm.uPassword.$invalid">
                    <li ng-show="loginForm.uPassword.$error.required">Please enter your password</li>
                    <li ng-show="loginForm.uPassword.$error.minlength">Password length should be at least 6 characters</li>
                    <li ng-show="loginForm.uPassword.$error.maxlength">Password length should be less or equal 100 characters</li>
                </ul>
            </div>

            <button type="submit" class="button radius">Login</button>
        </fieldset>
    </form>
</div>

<script type="text/javascript">
    var LearzingLoginFormModule = angular.module('LearzingLoginFormModule', ['LearzingSDKModule']);

    LearzingLoginFormModule.controller('LearzingLoginController', ['$scope', 'LEARZ',
        function($scope, LEARZ) {

            $scope.reset = function() {
                $scope.user = { email : "", password : "", validationErrors : [] };
                $scope.showLoginFormValidationErrors = false;
            };

            $scope.reset();

            $scope.loginFormSubmit = function() {
                if ($scope.loginForm.$valid) {
                    $scope.doLogin();
                } else {
                    $scope.showLoginFormValidationErrors = true;
                }
            }

            $scope.doLogin = function() {
                LEARZ.auth.login($scope.user.email, $scope.user.password, $scope._doLoginCallback);
            };

            $scope._doLoginCallback = function(response) {
                if (response.status === LEARZING_STATUS_SUCCESS) {
                    document.location = "<?php echo $this->createUrl('site/index'); ?>";
                } else {
                    $scope.user.validationErrors = response.texts;                    
                }
            };
        }
    ]);
</script>