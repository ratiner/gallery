angular
	.module('services')
	.factory('FileService', ['$http', function($http){

		return {
			// Return public API.
			getList: getList,
            search: search
		};
		
		
		// ---
		// PUBLIC METHODS.
		// ---
		function getList($path) {
			$path = ($path) ? $path : '';
			return $http.get(config.apiPath+ '/list/'+$path, { cache: true}).then(function(result) {
				return addData(result.data);
			});	
		}

        function search($tags) {
            return $http.post(config.apiPath+ '/search', $tags).then(function(result) {
                return addData(result.data);
            });
        }

        function addData(list) {
            angular.forEach(list, function(value, key) {
                if(value.type != 'folder') {
                    value.thumbnail = config.apiPath + '/thumb/256/' + value.path;
                    value.full = config.apiPath + '/thumb/full/' + value.path;
                    value.preview = config.apiPath + '/thumb/700/' + value.path;
                }
                value.title = (value.name.length < config.max_title_length) ? value.name : value.name.substring(0, config.max_title_length) + '...';
            });
            return list;
        }
}]);

//http://www.bennadel.com/blog/2612-using-the-http-service-in-angularjs-to-make-ajax-requests.htm