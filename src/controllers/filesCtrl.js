angular
	.module('controllers')
	.controller('filesCtrl', ['$scope', '$stateParams', '$location', 'FileService', 'TagService', function($scope, $args, $location, FileService, TagService) {

        generatePath();

        $scope.InitialList = null;

        FileService.getList($args.path).then(function(data) {
            $scope.InitialList = data;
            $scope.list = $scope.InitialList.slice();
        });


        $scope.Search = {
            SearchTags: [],
            typingTimer: null,
            typingTimeout: 400,
            Fetch: function(text) {
                this.typingTimer = setTimeout(function() {
                    var searchTags = $scope.Search.SearchTags;
                    var searchArr = searchTags.slice();

                    if(text && text.length > 3 && searchArr.indexOf(text) === -1)
                        searchArr.push(text);

                    if(searchArr.length == 0) {
                        $scope.list = $scope.InitialList.slice();
                        return;
                    }

                    FileService.search(searchArr).then(function(data) {
                        $scope.list = data;
                    });

                }, this.typingTimeout);
            }
        };



        $scope.Preview = {
            IsVisible: false,
            Open: function(index) {
                var item = $scope.list[index];
                if($scope.selection)
                    return $scope.ToggleCheck(item);

                $scope.Preview.Index = index;
                $scope.Preview.Visible = true;
            }
        };


        $scope.SelectedItems = new Array();







        $scope.ToggleCheck = function(item, e) {
            item.selected = !item.selected;



            if(item.selected)
                $scope.SelectedItems.push(item);
            else
                $scope.SelectedItems.splice($scope.SelectedItems.indexOf(item), 1);

            if(e)
                e.stopPropagation();
        };


        $scope.FolderClick = function(path){
            $location.path('/files/' + path);
        };



        $scope.btSelection = function() {
            $scope.selection = !$scope.selection;
        };

        function generatePath() {
            var p = ($args.path) ? $args.path.split('/') : [];
            $scope.path = [];
            var url = "";
            for(var i in p){
                url+= (i > 0) ? "/"+p[i] : p[i];
                $scope.path.push({
                    label: p[i],
                    url: url
                });
            }
        }
	}]);

		
