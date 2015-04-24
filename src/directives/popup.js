angular.module('directives')
    .directive('popup',[function() {
        return {
            restrict:'AE',
            transclude: true,
            scope:{
                visible:'=visible',
                top: '=top',
                left: '=left',
                width: '=width',
                height: '=height'
            },
            templateUrl: config.viewsPath + '/cmp/popup.tmpl.html',
            link:function($scope,$elem,$attrs) {

                $scope.CloseClick = function() {
                    $scope.visible = false;
                };


            }
        }
    }]);
