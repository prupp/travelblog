var controller = angular.module('travelblog.controllers', ['ngDialog']);

controller.controller('TravelsCtrl', ['$scope', 'TravelsFactory',
    function ($scope, TravelsFactory) {
        $scope.travels = TravelsFactory.query();
    }]);

controller.controller('EditTravelCtrl', ['$scope', '$routeParams','TravelFactory','TravelsFactory', '$route', '$location',
    function ($scope, $routeParams, TravelFactory, TravelsFactory, $route, $location) {

        $scope.deleteTravel = function deleteTravel(travelId) {
            TravelFactory.babupp({id: travelId}
                , function() {
                $route.reload();
            });
        }
        $scope.save = function save() {
            console.log($scope.travel);
            TravelsFactory.create($scope.travel,
                function (){
                $location.path("/travels");
            });
        }
    }]);

controller.controller('EditEntryCtrl', ['$scope', '$routeParams','EntryFactory','EntriesFactory', '$route', '$location',
    function ($scope, $routeParams, EntryFactory, EntriesFactory, $route, $location) {

        $scope.deleteEntry = function deleteTravel(entryId) {
            EntryFactory.babupp({id: $routeParams.id, entryId: entryId}
                , function() {
                    $route.reload();
                });
        }
        $scope.save = function save() {
            console.log($scope.entry);
            EntriesFactory.create({id: $routeParams.id}, $scope.entry
            , function (){
                    $location.path("/travels/" + $routeParams.id + "/entries");
                });
        }
    }]);

controller.controller('EntriesCtrl', ['$scope', '$routeParams', 'EntriesFactory','TravelFactory',
    function ($scope, $routeParams, EntriesFactory, TravelFactory) {
        $scope.entries = EntriesFactory.query({id: $routeParams.id});
        $scope.travel = TravelFactory.query({id: $routeParams.id});
    }]);

controller.controller('AdminCtrl', ['$scope', '$routeParams', 'AdminFactory',
    function ($scope, $routeParams, AdminFactory) {
        $scope.users = AdminFactory.query();
    }]);

controller.controller('AuthServiceCtrl', ['$scope','AuthenticationService', '$window',
    function ($scope, AuthenticationServcie, $window) {
        $scope.isAdmin = $window.sessionStorage.isAdmin;
        $scope.isAuthenticated = $window.sessionStorage.isAuthenticated;
    }
]);

controller.controller('AuthCtrl', ['$scope', '$location', '$window', 'RegisterFactory', 'LoginFactory', 'AuthenticationService', '$route',
    function ($scope, $location, $window, RegisterFactory, LoginFactory, AuthenticationService, $route) {

        $scope.login = function login() {
            console.log($scope.loginData);
            var username = $scope.loginData.username;
            var password = $scope.loginData.password;
            if (username != null && password != null) {

                LoginFactory.login($scope.loginData,
                function(data) {

                    if(data.isAdmin) {
                        $window.sessionStorage.isAdmin = true;
                    } else {
                        delete $window.sessionStorage.isAdmin;
                    }
                    $window.sessionStorage.isAuthenticated = true;
                    AuthenticationService.isAuthenticated = true;
                    AuthenticationService.isAdmin = data.isAdmin;
                    $window.sessionStorage.token = data.token;
                    $location.path("/");
                    $window.location.reload();
                    console.log(data);
                    console.log(data.token);
                });
            }
        }

        $scope.logout = function logout() {
            delete $window.sessionStorage.isAuthenticated;
            delete $window.sessionStorage.isAdmin;
            delete $window.sessionStorage.token;
            $window.location.reload();
        }

        $scope.register = function register() {
            console.log($scope.registerData);
                RegisterFactory.register($scope.registerData,
                function() {
                    $location.path("/login");
                });
        }
    }
]);
