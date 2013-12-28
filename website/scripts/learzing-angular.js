var LearzingSDKModule = angular.module('LearzingSDKModule', [])
  .factory('LEARZ', function() {
        LEARZ.init({
            clientId : "8FbuxX7wMSjOJtp4hniVL7QimO7X9r"
        });
        return LEARZ;
   });

var LearzingLogoutModule = angular.module('LearzingLogoutModule', ['LearzingSDKModule']);
LearzingLogoutModule.controller('LearzingLogoutController', ['$scope', 'LEARZ',
    function($scope, LEARZ) {

        $scope.doLogout = function() {
            LEARZ.services.auth.logout($scope._doLogoutCallback);
        };

        $scope._doLogoutCallback = function(response) {
            if (response.status === LEARZING_STATUS_SUCCESS) {
                document.location = "/site/index";
            } else {
                alert("Errors have occured: \n" +
                  response.texts.toString() + "\n" +
                  "Learzing API status: " + response.status);
            }
        };
    }
]);

var LearzingAngularExtensionsModule = angular.module('LearzingAngularExtensionsModule', []);
LearzingAngularExtensionsModule.directive('ngFocus', [function() {
    var FOCUS_CLASS = "ng-focused";
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl) {
            ctrl.$focused = false;
            element.bind('focus', function(evt) {
                element.addClass(FOCUS_CLASS);
                scope.$apply(function() {ctrl.$focused = true;});
            }).bind('blur', function(evt) {
                element.removeClass(FOCUS_CLASS);
                scope.$apply(function() {ctrl.$focused = false;});
            });
        }
    };
 }]);