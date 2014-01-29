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
 
 LearzingAngularExtensionsModule.factory('urlConverterService', function() {
    return {
        encode : urlEncode,
        decode : urlDecode
    };

    function urlEncode(target){
        return encodeURIComponent(target);
    }

    function urlDecode(target){
        return encodeURIComponent(target);
    }
});

var LearzingAngularDateModule = angular.module('LearzingAngularDateModule', []);
LearzingAngularDateModule.directive('learzDateMask', [ '$filter',
function($filter) {
    var dateRegexp = new RegExp("^([0-9]{2})/([0-9]{2})/([0-9]{4})$");

    function link(scope, element, attrs, ngModel) {
        function parser(value) {
            var match = dateRegexp.exec(value);
            if (match !== null) {
                var month = parseInt(match[1], 10);
                var day = parseInt(match[2], 10);
                var year = parseInt(match[3], 10);
                if (month !== NaN && day !== NaN && year !== NaN) {
                    var dateObj = new Date(year, month - 1, day);
                    if (dateObj.getFullYear() === year && dateObj.getMonth() === month - 1
                        && dateObj.getDate() === day) {
                        ngModel.$setValidity("value", true);
                        return new LEARZ.objs.Date(day, month, year);
                    }
                }
            }
            ngModel.$setValidity("value", false);
            return undefined;
        }

        function formatter(value) {
            return $filter('learzDateToString')(value);
        }

        element.inputmask("m/d/y",{ "placeholder": "mm/dd/yyyy" });
        ngModel.$parsers.push(parser);
        ngModel.$formatters.push(formatter);
    }

    return {
        restrict: 'A',
        require: 'ngModel',            
        link: link
    };
}]);

LearzingAngularDateModule.filter('learzDateToString', function() {
    function toStringWith2Digits(val) {
        str = val.toString();
        if (val < 10 && val >= 0)
            str = "0" + str;
        return str;
    }
    return function(value) {
        if (value !== null && value !== undefined) {
                var month = toStringWith2Digits(value.month);
                var day = toStringWith2Digits(value.day);
                var year = value.year.toString();
                return month + "/" + day + "/" + year;
            } else
                return "";
    };
});