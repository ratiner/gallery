angular.module('directives')
    .directive('preview',[function() {
        return {
            restrict:'AE',
            scope:{
                list:'=list',
                index: '=index',
                visible: '=visible'
            },
            templateUrl: config.viewsPath + '/cmp/preview.tmpl.html',
            link:function($scope,$elem,$attrs) {
                $scope.visible = false;
                $scope.item = null;
                $scope.ImageZIndex = "10";

                var Minimized = {
                    Top: "20%",
                    Left: "25%",
                    Width: "50%",
                    Height: "60%"
                };

                var Maximized = {
                    Top: "0",
                    Left: "0",
                    Width: "100%",
                    Height: "100%"
                };

                $scope.WindowState = Minimized;


                $scope.$watch("index", function(index) {
                    if(index < 0 || !$scope.list || $scope.list.length == 0 || index > $scope.list.length || !$scope.list[index] || $scope.list[index].type == "folder")
                        return;

                    $scope.item = $scope.list[index];
                });

                $scope.ImageClick = function() {
                    $scope.ImageZIndex = ($scope.ImageZIndex == "10") ? "30" : "10";
                };

                $scope.Next = function()  {
                    $scope.index++;
                };

                $scope.Prev = function() {
                    $scope.index--;
                };

                $scope.OpenExternal = function (url) {
                    var w=window.open(url,'_blank');
                    w.focus();
                };

                $scope.ToggleTags = function() {
                    $scope.TagsVisible = !$scope.TagsVisible;
                };

                $scope.ToggleFullScreen = function(e) {
                    var isMax = (e.target.innerHTML == "Maximize");
                    $scope.WindowState = isMax ? Maximized : Minimized;
                    e.target.innerHTML = isMax ? "Minimize" : "Maximize";
                };
            }
        }
    }]);