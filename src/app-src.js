angular
	.module('mgallery', ['ui.router', 'controllers', 'services', 'directives'])
	.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider) {
		
		$locationProvider.html5Mode(true).hashPrefix('!');
	    $urlRouterProvider.otherwise('/error/404');
	    
	    $stateProvider
		.state('home', {
			url: '/',
			templateUrl: config.viewsPath + '/home.tmpl.html'
	    })

		.state('filesMain', {
			url: '/files',
			templateUrl: config.viewsPath + '/files.tmpl.html'
		})

	    .state('filesMain2', {
			url: '/files/',
			templateUrl: config.viewsPath + '/files.tmpl.html'
	    })

		.state('files', {
			url: '/files/{path:.+}',
			templateUrl: config.viewsPath + '/files.tmpl.html'
		})

	    .state('404', {
	    	url: '/error/:errorNumber',
	    	templateUrl: config.viewsPath + '/error.tmpl.html',
	    	controller: 'errorCtrl'
	    });
	    
	}])
	
	.controller('AppCtrl', ['$scope', function ($scope){
		$scope.layout = 'app/themes/'+config.theme+'/_layout.html';
	}])
	
	.run(['$state', function($state){}]);

angular.module('controllers', []);
angular.module('services', []);
angular.module('directives', []);

if(!window.console){ window.console = {log: function(){}, info: function(){}, warn: function(){}, error: function(){} }; }