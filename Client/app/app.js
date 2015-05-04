var app = angular.module('travelblog', ['ngRoute', 'ngCookies', 'travelblog.controllers' , 'travelblog.services']);

app.config(['$routeProvider', function ($routeProvider) {

    $routeProvider.when('/', {templateUrl: 'templates/travels.html', controller: 'TravelsCtrl'});
    $routeProvider.when('/travels', {templateUrl: 'templates/travels.html', controller: 'TravelsCtrl'});
    $routeProvider.when('/travels/create', {templateUrl: 'templates/createtravel.html', controller: 'EditTravelCtrl'});
    $routeProvider.when('/travels/:id/entries', {templateUrl: 'templates/entries.html', controller: 'EntriesCtrl'});
    $routeProvider.when('/login', {templateUrl: 'templates/login.html', controller: 'AuthCtrl'});
    $routeProvider.when('/register', {templateUrl: 'templates/register.html', controller: 'AuthCtrl'});
    $routeProvider.when('/admin', {templateUrl: 'templates/admin.html', controller: 'AdminCtrl'});
    $routeProvider.when('/travels/:id/entries/create', {templateUrl: 'templates/createpost.html', controller: 'EditEntryCtrl'});
    $routeProvider.otherwise({redirectTo: '/'});

}]);

app.config(function ($httpProvider) {
    $httpProvider.interceptors.push('TokenInterceptor');
});

app.filter('datetime', function($filter)
{
    return function(input)
    {
        if(input == null){ return ""; }

        var _date = $filter('date')(new Date(input),
            'dd. MMM yyyy - HH:mm:ss');

        return _date;

    };
});

app.filter('dateFormat', function($filter)
{
    return function(input)
    {
        if(input == null){ return ""; }

        var _date = $filter('date')(new Date(input), 'dd. MMM yyyy');

        return _date;

    };
});

app.directive('repeatPassword', function() {
    return {
        require: 'ngModel',
        link: function(scope, elem, attrs, ctrl) {
            var otherInput = elem.inheritedData("$formController")[attrs.repeatPassword];

            ctrl.$parsers.push(function(value) {
                if (value === otherInput.$viewValue) {
                    ctrl.$setValidity('repeat', true);
                    return value;
                }
                ctrl.$setValidity('repeat', false);
            });

            otherInput.$parsers.push(function(value) {
                ctrl.$setValidity('repeat', value === ctrl.$viewValue);
                return value;
            });
        }
    };
});