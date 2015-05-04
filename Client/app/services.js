var services = angular.module('travelblog.services', ['ngResource']);

services.factory('TravelsFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/travels', {}, {
            query: { method: "GET", isArray: false },
            create: { method: "POST", body: {'Content-Type': 'application/json'}  }
        });
}]);

services.factory('TravelFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/travels/:id', {}, {
            query: { method: "GET", params:{id:'@id'}, isArray: false },
            babupp: { method: "DELETE", params:{id:'@id'} }
        });
    }]);

services.factory('EntryFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/travels/:id/entries/:entryId', {}, {
            query: { method: "GET", params:{id:'@id', entryId:'@entryId'}, isArray: false },
            babupp: { method: "DELETE", params:{id:'@id', entryId:'@entryId'} }
        });
    }]);

services.factory('EntriesFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/travels/:id/entries', {}, {
            query: { method: "GET", params:{id:'@id'}, isArray: false },
            create: { method: "POST", params:{id:'@id'}, body: {'Content-Type': 'application/json'} }
        });
}]);

services.factory('AdminFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/users', {}, {
            query: { method: "GET", isArray: false }
        });
    }]);

services.factory('LoginFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/login', {}, {
            logout: { method: "GET", isArray: false },
            login: { method: "POST", body: {'Content-Type': 'application/json'} }
        });
    }]);

services.factory('RegisterFactory', ['$resource',
    function ($resource) {
        return $resource('/blog/register', {}, {
            register: { method: "POST", body: {'Content-Type': 'application/json'} }
        });
    }]);

services.factory('AuthenticationService', function() {
    var auth = {
        isAuthenticated: false,
        isAdmin: false
    }

    return auth;
});

services.factory('TokenInterceptor', function ($q, $window, $location, AuthenticationService) {
    return {
        request: function (config) {
            config.headers = config.headers || {};
            if ($window.sessionStorage.token) {
                config.headers.Authorization = $window.sessionStorage.token;
            }
            return config;
        },

        requestError: function(rejection) {
            return $q.reject(rejection);
        },

        /* Set Authentication.isAuthenticated to true if 200 received */
        response: function (response) {
            if (response != null && response.status == 200 && $window.sessionStorage.token && !AuthenticationService.isAuthenticated) {
                AuthenticationService.isAuthenticated = true;
            }
            return response || $q.when(response);
        },

        /* Revoke client authentication if 401 is received */
        responseError: function(rejection) {
            if (rejection != null && rejection.status === 401 && ($window.sessionStorage.token || AuthenticationService.isAuthenticated)) {
                $location.path("/login");
            }

            return $q.reject(rejection);
        }
    };
});

