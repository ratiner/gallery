angular
	.module('controllers')
	.controller('errorCtrl', ['$scope', '$stateParams', function($scope, $stateParams) {
		
		$scope.error = $stateParams.errorNumber;
		
	}]);