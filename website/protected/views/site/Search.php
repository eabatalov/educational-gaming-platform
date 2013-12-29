<div ng-app="LearzingSearchFormModule" ng-controller="LearzingSearchFormController">
    <h1 style="text-align: center">Our tiny search engine</h1>
    <div id='formContainer' style='margin-top: 10pt; margin-bottom: 10pt;'>
        <form name="searchForm" novalidate ng-submit="searchFormSubmit()">
            <input style="width: 90%" ng-model="searchRequest.query" autofocus/>
            <input type="submit" style="width: 9%; max-width: 100px;" value="Search">
        </form>
        <div>
            <div ng-repeat="searchResult in searchResults" style="margin-bottom: 10px; margin-top: 10px;">
                <div ng-switch on="searchResult.object_type">
                    <div ng-switch-when="user" style="border-width: 1px; border-style: solid;">
                        <p>
                            User 
                            <a href="{{appendUserIdToProfileLink(searchResult.object.id,
                                '<?php echo $this->createUrl("/user/ShowUserProfile", array("userid" => "userid")); ?>')}}">
                            Profile</a>
                        </p>
                        <p>name: {{ searchResult.object.name }}</p>
                        <p>surname: {{ searchResult.object.surname }}</p>
                        <p>status: {{ searchResult.object.is_online ? "online" : "offline" }}</p>
                    </div>
                    <div ng-switch-default>
                        Unknown type of search result, displaying raw data:
                        <p>
                            {{ searchResult | json }}
                        </p>
                    </div>
                </div>
                <!--{{ searchResults | json }}-->
        </div>
        </div>
    </div>    

<script type="text/javascript">
    var LearzingSearchFormModule = angular.module('LearzingSearchFormModule',
        ['LearzingSDKModule', 'LearzingAngularExtensionsModule']);

    LearzingSearchFormModule.controller('LearzingSearchFormController', ['$scope', 'LEARZ', 'urlConverterService',
        function($scope, LEARZ, urlConverterService) {
            $scope.LEARZ = LEARZ;

            $scope.searchRequest = new LEARZ.objs.SearchRequest("", LEARZ.objs.SearchObjectTypes.all);
            $scope.paging = new LEARZ.objs.Paging(0, 200);
            $scope.searchResults = [];

            $scope.appendUserIdToProfileLink = function(userId, profileUrl) {
                return profileUrl.replace('userid', urlConverterService.encode(userId.toString()));
            };

            $scope.searchFormSubmit = function() {
                $scope._doSearch();
            };

            $scope._doSearch = function() {
                LEARZ.services.search.get($scope.searchRequest, $scope._doSearchCallback, $scope.paging);
            };

            $scope._doSearchCallback = function(response) {
                if (response.status === LEARZING_STATUS_SUCCESS) {
                    $scope.searchResults = response.data;
                    $scope.$digest();
                } else {
                    alert("Errors occured:\n" + response.texts.toString());
                }
            };
        }
    ]);
</script>