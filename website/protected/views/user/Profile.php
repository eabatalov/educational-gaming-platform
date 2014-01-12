<div ng-app="LearzingUserProfilePageModule" ng-controller="LearzingUserProfilePageController">
    <h1>
        Hello! This is profile of user with id <?php echo $userid; ?>.
    </h1>
    <br/>
    <div id="user" style="border-style: solid; padding: 12pt;">
        <img src="/media/images/pl_team_lbl.png" style="float: right; height: 120px;"/>
        <p><strong>User information</strong></p>
        <p>name: {{ user.name }}</p>
        <p>surname: {{ user.surname }}</p>
        <p ng-if="user.email !== undefined && user.email !== null">email: {{ user.email }}</p>
        <p>status: {{ user.is_online ? "online" : "offline" }}</p>
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
        ['LearzingSDKModule']);

    LearzingUserProfilePageModule.controller('LearzingUserProfilePageController', ['$scope', 'LEARZ',
        function($scope, LEARZ) {
            $scope.LEARZ = LEARZ;
            $scope.userId = <?php echo $userid; ?>;
            $scope.user = null;
            $scope.friends = null;
            $scope.skills = null;

            $scope.showUserProfile = function() {
                LEARZ.services.user.get($scope.userId, function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.user = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                });

                LEARZ.services.friends.get($scope.userId, function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.friends = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                });

                LEARZ.services.skills.get($scope.userId, function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        $scope.skills = apiResponse.data;
                        $scope.$digest();
                    } else {
                        alert("Error occured:\n" + apiResponse.texts.toString());
                    }    
                });
            };

            $scope.showUserProfile();
        }
    ]);
</script>