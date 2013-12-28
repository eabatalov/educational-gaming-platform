<div ng-app="LearzingSignupFormModule" ng-controller="LearzingSignupController">
    <form name="signupForm" novalidate ng-submit="signupFormSubmit()">
        <fieldset>
            <!--<legend>Signup</legend>-->
            <div class="validation-errors" ng-hide="validationErrors.length === 0">
                <div>Please fix the following errors:</div>
                <ul>
                <li ng-repeat="validationError in validationErrors">
                    {{ validationError }}
                </li>
                </ul>
            </div>

            <label for="uEmail">Email</label>
            <div class="row">
                <input type="email" name="uEmail" ng-model="user.email" placeholder="Email"
                    required ng-maxlength="50"/>
                <ul ng-show="showSignupFormValidationErrors && signupForm.uEmail.$invalid">
                    <li ng-show="signupForm.uEmail.$error.required">Please fill your email</li>
                    <li ng-show="signupForm.uEmail.$error.email">This email is not valid</li>
                    <li ng-show="signupForm.uEmail.$error.maxlength">Email length should be less or equal 50 characters</li>
                </ul>
            </div>

            <label for="uName">Name</label>
            <div class="row">
                <input type="text" name="uName" ng-model="user.name" placeholder="Name"
                    required ng-maxlength="50"/>
                <ul ng-show="showSignupFormValidationErrors && signupForm.uName.$invalid">
                    <li ng-show="signupForm.uName.$error.required">Please fill your name</li>
                    <li ng-show="signupForm.uName.$error.maxlength">Name length should be less or equal 50 characters</li>
                </ul>
            </div>

            <label for="uSurname">Surname</label>
            <div class="row">
                <input type="text" name="uSurname" ng-model="user.surname" placeholder="Surname"
                    required ng-maxlength="50"/>
                <ul ng-show="showSignupFormValidationErrors && signupForm.uSurname.$invalid">
                    <li ng-show="signupForm.uSurname.$error.required">Please fill your surname</li>
                    <li ng-show="signupForm.uSurname.$error.maxlength">Surname length should be less or equal 50 characters</li>
                </ul>
            </div>

            <label for="uPassword">Password</label>
            <div class="row">
                <input type="password" name="uPassword" ng-model="userPassword" placeholder="Password"
                    ng-minlength="6" ng-maxlength="100" required/>
                <ul ng-show="showSignupFormValidationErrors && signupForm.uPassword.$invalid">
                    <li ng-show="signupForm.uPassword.$error.required">Please fill your password</li>
                    <li ng-show="signupForm.uPassword.$error.minlength">Password length should be at least 6 characters</li>
                    <li ng-show="signupForm.uPassword.$error.maxlength">Password length should be less or equal 100 characters</li>
                </ul>
            </div>

            <div class="row">
                <button type="submit" class="button radius">Signup</button>
            </div>

        </fieldset>
    </form>
</div>

<script type="text/javascript">
    var LearzingSignupFormModule = angular.module('LearzingSignupFormModule', ['LearzingSDKModule']);

    LearzingSignupFormModule.controller('LearzingSignupController',
        ['$scope', 'LEARZ', function($scope, LEARZ) {

            $scope.reset = function() {
                $scope.user = new LEARZ.objs.User("", "", "");
                $scope.userPassword = "";
                $scope.validationErrors = [];
                $scope.showSignupFormValidationErrors = false;
            };

            $scope.reset();

            $scope.signupFormSubmit = function() {
                if ($scope.signupForm.$valid) {
                    $scope.doSignup();
                } else {
                    $scope.showSignupFormValidationErrors = true;
                }
            };

            $scope.doSignup = function() {
                LEARZ.objs.user.register($scope.user, $scope.userPassword, $scope.doSignupCallback);
            };

            $scope.doSignupCallback = function(response) {
                if (response.status === LEARZING_STATUS_SUCCESS) {
                    LEARZ.services.auth.login($scope.user.email, $scope.userPassword, $scope.doLoginCallback);
                } else {
                    $scope.validationErrors = response.texts;
                    $scope.$digest();
                }
            };

            $scope.doLoginCallback = function(response) {
                if (response.status === LEARZING_STATUS_SUCCESS) {
                    document.location = "<?php echo $this->createUrl('site/index'); ?>";
                } else {
                    alert("Couldn't login after new user registration: \n" +
                        response.texts.toString() + "\n" +
                    "Learzing API status: " + response.status + "\n" +
                    "Please contact Learzing support for help");
                }
            };
    }]);
</script>

<?php
//$this->widget('application.modules.hybridauth.widgets.renderProviders', array('action' => 'signup'));
?>