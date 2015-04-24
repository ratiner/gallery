angular
    .module('services')
    .factory('TagService', ['$http', function($http){


        return {
            // Return public API.
            saveOne: saveOne,
            getFileTags: getFileTags,
            search: search
        };


        // ---
        // PUBLIC METHODS.
        // ---
        function saveOne($path, $tags) {
            return $http.post(config.apiPath+ '/tag/one/'+$path, $tags ).then(function(result) {
                return result.data;
            });
        }

        function getFileTags($path) {
            return $http.get(config.apiPath+ '/tag/one/'+$path, {cache: false}).then(function(result) {
                return result.data;
            });
        }

        function search($text) {
            if($text && $text.length > 1) {
                return $http.get(config.apiPath + '/tag/search/' + $text, {cache: false}).then(function (result) {
                    return result.data;
                });
            }
            else
            {
                return { then: function() {return new Array(); }};
            }
        }

    }]);

//http://www.bennadel.com/blog/2612-using-the-http-service-in-angularjs-to-make-ajax-requests.htm