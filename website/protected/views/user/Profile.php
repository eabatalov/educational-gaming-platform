<div ng-app="LearzingUserProfilePageModule" ng-controller="LearzingUserProfilePageController">
    <h1>
        Hello! This is profile of user with id <?php echo $userid; ?>.
    </h1>
    <br/>
    <div id="user" style="border-style: solid; padding: 12pt;">

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

        <p><strong>User information</strong></p>
        <p>name: {{ user.name }}</p>
        <p>surname: {{ user.surname }}</p>
        <p ng-if="user.email !== undefined && user.email !== null">email: {{ user.email }}</p>
        <p>birth date: {{ user.birthdate | json }}</p>
        <p>gender: {{ user.gender | json }}</p>
        <p>status: {{ user.is_online ? "online" : "offline" }}</p>

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
        ['LearzingSDKModule', 'angularFileUpload']);

    LearzingUserProfilePageModule.controller('LearzingUserProfilePageController',
        ['$scope', 'LEARZ', '$upload',
        function($scope, LEARZ, $upload) {
            $scope.isCurrentUser = "<?php echo $isCurrentUser; ?>";
            $scope.userId = "<?php echo $userid; ?>";
            $scope.user = null;
            $scope.friends = null;
            $scope.skills = null;

            $scope.showUserProfile = function() {
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

            $scope.showUserProfile();

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
        }
    ]);
</script>