<div ng-app="LearzingUserProfilePageModule" ng-controller="LearzingUserProfilePageController">
    <h1>
        Hello! This is profile of user with id <?php echo $userid; ?>.
    </h1>
    <br/>
    <div id="divUser" style="border-style: solid; padding: 12pt;">

        <div id="avatar" style="float: right;">
            <img ng-src="{{ avatarURL(user.avatar) }}" style="max-height: 400px; max-width: 400px;"/>
            <div id="avatar_change" ng-if="isCurrentUser">
                <p>
                    <label for="fileAvatar">Change avatar</label>
                    <img id="gifLoading" src="/media/images/loading.gif" style="display:none;" alt='loading'>
                </p>
                <p>
                    <input ng-file-select="changeAvatar($files)" type="file" id="fileAvatar"
                           title="Click to upload new avatar" accept="image/*"/>
                </p>
            </div>
        </div>

        <div id="divUserShow" ng-if="!edit.isInEditMode">
            <p><strong>User information</strong></p>
            <p>name: {{ user.name }}</p>
            <p>surname: {{ user.surname }}</p>
            <p ng-if="user.email !== undefined && user.email !== null">email: {{ user.email }}</p>
            <p>birth date: {{ user.birthdate | learzDateToString }}</p>
            <p>gender: {{ user.gender }}</p>
            <p>status: {{ user.is_online ? "online" : "offline" }}</p>
            <div ng-if="isCurrentUser">
                <input type="button" value="edit" style="min-width: 50pt;" ng-click="editUser()"/>
            </div>
        </div>

        <div id="divUserEdit" ng-if="edit.isInEditMode">
            <form name="userEditForm" novalidate ng-submit="saveUser(userEditForm)">
                <fieldset>
                    <div class="validation-errors" ng-hide="edit.validationErrors.length === 0">
                        <div>Please fix the following errors:</div>
                        <ul>
                        <li ng-repeat="validationError in edit.validationErrors">
                            {{ validationError }}
                        </li>
                        </ul>
                    </div>

                    <div class="validation-errors" ng-hide="edit.otherErrors.length === 0">
                        <ul>
                        <li ng-repeat="otherError in edit.otherErrors">
                            {{ otherError }}
                        </li>
                        </ul>
                    </div>

                    <label for="uName">Name</label>
                    <div class="row">
                        <input type="text" name="uName" ng-model="user.name" placeholder="Name"
                            required ng-maxlength="50" autofocus/>
                        <ul ng-show="edit.showEditFormErrors && userEditForm.uName.$invalid">
                            <li ng-show="userEditForm.uName.$error.required">Please fill your name</li>
                            <li ng-show="userEditForm.uName.$error.maxlength">Name length should be less or equal 50 characters</li>
                        </ul>
                    </div>

                    <label for="uSurname">Surname</label>
                    <div class="row">
                        <input type="text" name="uSurname" ng-model="user.surname" placeholder="Surname"
                            required ng-maxlength="50"/>
                        <ul ng-show="edit.showEditFormErrors && userEditForm.uSurname.$invalid">
                            <li ng-show="userEditForm.uSurname.$error.required">Please fill your surname</li>
                            <li ng-show="userEditForm.uSurname.$error.maxlength">Surname length should be less or equal 50 characters</li>
                        </ul>
                    </div>

                    <label for="uGender">Gender</label>
                    <div class="row">
                        <select ng-model="user.gender" name="uGender"/>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <label for="uBirthDate">Birth date</label>
                    <div class="row">
                        <input type="text" name="uBirthDate" ng-model="user.birthdate" learz-date-mask/>
                        <ul ng-show="edit.showEditFormErrors && userEditForm.uBirthDate.$invalid">
                            <li>Please enter valid date</li>
                        </ul>
                    </div>

                    <label for="uEmail">Email</label>
                    <div class="row">
                        <input type="email" name="uEmail" ng-model="user.email" placeholder="Email"/><!--required ng-maxlength="50"-->
                        <ul ng-show="edit.showEditFormErrors && userEditForm.uEmail.$invalid">
                            <li ng-show="userEditForm.uEmail.$error.required">Please fill your email</li>
                            <li ng-show="userEditForm.uEmail.$error.email">This email is not valid</li>
                            <li ng-show="userEditForm.uEmail.$error.maxlength">Email length should be less or equal 50 characters</li>
                        </ul>
                    </div>

                    <label for="uOldPassword">Change password</label>
                    <div class="row">
                        <input type="password" name="uOldPassword" ng-model="edit.passChange.old" placeholder="Old password"
                            ng-minlength="6" ng-maxlength="100" ng-required="edit.passChange.new !== null"/>
                        <input type="password" name="uNewPassword" ng-model="edit.passChange.new" placeholder="New password"
                            ng-minlength="6" ng-maxlength="100"/>

                        <ul ng-show="edit.showEditFormErrors && (userEditForm.uOldPassword.$invalid ||
                                userEditForm.uNewPassword.$invalid)">
                            <li ng-show="userEditForm.uOldPassword.$error.required">
                                 To set new password you need to supply old password.
                            </li>
                            <li ng-show="userEditForm.uOldPassword.$error.minlength || userEditForm.uNewPassword.$error.minlength">
                                Password length should be at least 6 characters</li>
                            <li ng-show="userEditForm.uOldPassword.$error.maxlength || userEditForm.uNewPassword.$error.maxlength">
                                Password length should be less or equal 100 characters</li>
                        </ul>
                    </div>

                    <div class="row">
                        <button type="submit" style="min-width: 50pt;">Save</button>
                    </div>

                </fieldset>
            </form>
        </div>

        <div style="clear: both"></div>
    </div>
    <br/>
    <div id="friends" style="border-style: solid; padding: 12pt;">
        <p><strong>Friends</strong></p>
        <ul>
            <li ng-repeat="friend in friends">
                <p>name: {{ user.name }}</p>
                <p>surname: {{ user.surname }}</p>
                <p ng-if="user.email !== undefined && user.email !== null">email: {{ user.email }}</p>
                <p>status: {{ user.is_online ? "online" : "offline" }}</p>
            </li>
        </ul>    
    </div>
    <br/>
    <div id="skills" style="border-style: solid; padding: 12pt;">
        <p><strong>skills</strong></p>
        <ul>
            <li ng-repeat="userSkill in skills">
                <p>skill: {{ LEARZ.consts.SKILLS[userSkill.skill_id].name }}</p>
                <p>level: {{ userSkill.value }}</p>
            </li>
        </ul>    
    </div>
</div>

<script type="text/javascript">
    var LearzingUserProfilePageModule = angular.module('LearzingUserProfilePageModule',
        ['LearzingSDKModule', 'angularFileUpload', 'LearzingAngularDateModule']);

    LearzingUserProfilePageModule.controller('LearzingUserProfilePageController',
        ['$scope', 'LEARZ', '$upload',
        function($scope, LEARZ, $upload) {
            $scope.isCurrentUser = "<?php echo $isCurrentUser; ?>";
            $scope.userId = "<?php echo $userid; ?>";
            $scope.user = null;
            $scope.friends = null;
            $scope.skills = null;

            $scope.fillUserProfile = function() {
                LEARZ.services.user.get(function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.user = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                }, $scope.userId);

                LEARZ.services.friends.get($scope.userId, function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.friends = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                });

                LEARZ.services.skills.getAllUserSkills(function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.skills = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                }, $scope.userId);
            };

            $scope.fillUserProfile();

            $scope.avatarURL = function(avatar) {
                if (avatar !== null)
                    return '/media/images/uploads/' + avatar;
                else
                    return "";
            };

            $scope.changeAvatar = function($files) {
                //$files: an array of files selected, each file has name, size, and type.
                if ($files.length <= 0)
                    return;

                var loading = $("#gifLoading");
                loading.show();

                var $file = $files[0];
                $upload.upload({
                        url: "<?php echo $this->createUrl('api/private/PApiUpload/Avatar'); ?>",
                        method: 'POST',
                        headers: LEARZ._services.api._mkRequestHeaders(),
                        data : {},
                        file: $file,
                        fileFormDataName: 'avatar'
                    }).progress(function(evt) {
                        console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                    }).success(function(apiResult, status, headers, config) {
                        loading.hide();
                        console.log(angular.toJson(apiResult));
                        if (apiResult.status !== LEARZING_STATUS_SUCCESS) {
                            alert("Can't load avatar. Error has occured");
                            return;
                        };
                        $scope.user.avatar = apiResult.data.new_avatar;
                    }).error(function() {
                        loading.hide();
                        alert("Can't load avatar. Error has occured");
                    });
            };

            $scope.editReset = function() {
                $scope.edit = {
                    passChange : new LEARZ.objs.PassChange(null, null),
                    showEditFormErrors : false,
                    validationErrors : [],
                    otherErrors : [],
                    isInEditMode : false
                };
            };
            $scope.editReset();

            $scope.editUser = function() {
                $scope.edit.isInEditMode = true;
            };

            $scope.saveUser = function(userEditForm) {
                $scope.edit.showEditFormErrors = true;
                if (!userEditForm.$valid) {                    
                    return;
                }

                var passwordChange = null;
                if ($scope.edit.passChange.new !== null)
                    passwordChange = $scope.edit.passChange;

                LEARZ.services.user.update($scope.user, function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.editReset();
                        $scope.$digest();
                    } else if (apiResponse.status === LEARZING_STATUS_INVALID_ARGUMENT) {
                        $scope.edit.validationErrors = apiResponse.texts;
                        $scope.$digest();
                    } else {
                        $scope.edit.otherErrors = apiResponse.texts;
                        $scope.$digest();
                    }
                }, passwordChange);
            };
        }
    ]);
</script>