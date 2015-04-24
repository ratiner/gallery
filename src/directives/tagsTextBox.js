angular.module('directives')
    .directive('tagsTextBox',['TagService',function(TagService) {
        return {
            restrict:'AE',
            scope:{
                selectedTags:'=model',
                onchanged: '&onchanged',
                ontype: '&ontype'
            },
            templateUrl: config.viewsPath + '/cmp/tags-text-box.tmpl.html',
            link:function(scope,elem,attrs) {

                scope.suggestions = [];

                if (!scope.selectedTags)
                    scope.selectedTags = [];

                scope.selectedIndex = -1;

                scope.removeTag = function (index) {
                    scope.selectedTags.splice(index, 1);
                    onChangeEvent();
                };

                scope.search = function () {
                    TagService.search(scope.searchText).then(function (data) {
                        var tags = new Array();
                        angular.forEach(data, function (value, key) {
                            tags.push(value.name);
                        });


                        if (tags.indexOf(scope.searchText) === -1) {
                            tags.unshift(scope.searchText);
                        }
                        scope.suggestions = tags;
                        scope.selectedIndex = -1;

                        onTypeEvent(scope.searchText);
                    });
                };

                scope.addToSelectedTags = function (index) {
                    var tag = "";
                    if(!isNaN(index) && index > -1)
                        tag = scope.suggestions[index];
                    else
                        tag = scope.searchText;

                    if (scope.selectedTags.indexOf(tag) === -1) {
                        scope.selectedTags.push(tag);
                        scope.searchText = '';
                        scope.suggestions = [];

                        onChangeEvent();
                    }

                };

                scope.checkKeyDown = function (event) {
                    if (event.keyCode === 40) {
                        event.preventDefault();
                        if (scope.selectedIndex + 1 !== scope.suggestions.length) {
                            scope.selectedIndex++;
                        }
                    }
                    else if (event.keyCode === 38) {
                        event.preventDefault();
                        if (scope.selectedIndex - 1 !== -1) {
                            scope.selectedIndex--;
                        }
                    }
                    else if (event.keyCode === 13) {
                        scope.addToSelectedTags(scope.selectedIndex);
                    }
                };

                scope.$watch('selectedIndex', function (val) {
                    if (val !== -1) {
                        scope.searchText = scope.suggestions[scope.selectedIndex];
                    }
                });

                onChangeEvent = function () {
                    if(!scope.onchanged)
                        return;

                    var onchangeHandler = scope.onchanged();

                    if(isFunction(onchangeHandler)) {
                        onchangeHandler();
                    }
                };

                onTypeEvent = function(text) {
                    if(!scope.ontype)
                        return;

                    var ontypeHandler = scope.ontype();
                    if(isFunction(ontypeHandler))
                        ontypeHandler(text);
                };

                isFunction = function(functionToCheck) {
                    var getType = {};
                    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
                };

            }
        }
    }]);
