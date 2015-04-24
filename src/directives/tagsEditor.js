angular.module('directives')
    .directive('tagsEditor',['TagService', function(TagService) {
        return {
            restrict:'AE',
            scope:{
                visible: "=visible",
                item: "=item"
            },
            templateUrl: config.viewsPath + '/cmp/tags-editor.tmpl.html',
            link:function($scope,$elem,$attrs) {
                $scope.Selecected = [];

                $scope.$watch("item", function(item) {
                    if(!item)
                        return;

                    $scope.Selecected.length = 0;

                    angular.forEach(item.db_tags, function(tag, key) {
                        if($scope.Selecected.indexOf(tag.tag_name) < 0)
                           $scope.Selecected.push(tag.tag_name);
                    });
                });


                $scope.Save = function() {
                    TagService.saveOne($scope.item.path, $scope.Selecected).then(function(data) {
                        //update
                        TagService.getFileTags($scope.item.path).then(function(updatedTags) {
                            $scope.item.db_tags = updatedTags;
                        });
                    });
                };



            }
        }
    }]);