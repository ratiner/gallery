angular.module('directives')
    .directive('resizeableImage',['$window', function($window) {
        return {
            scope: {
                source: "=source"
            },
            link:function($scope,$elem,$attrs) {

                var contentDiv = $elem.parent().parent().parent().parent();
                var contentWidth, contentHeight;
                var img = $elem[0];
                var oWidth, oHeight;


                angular.element($window).bind("resize", resize);


                $scope.$watch(function() {return contentDiv.attr('style'); }, resize);
                $scope.$watch(function() {return $elem.attr('style'); }, resize);


                $scope.$watch("source", function(src) {
                    var tImg = new Image();
                    tImg.onload =  function() {
                        oWidth = this.width;
                        oHeight = this.height;
                        img.src = this.src;
                        img.width = oWidth;
                        img.height = oHeight;
                        resize();
                    };
                    tImg.src = src;
                });

                function resize() {

                    img.style.left =0;
                    img.style.top = 0;

                    contentWidth = contentDiv[0].offsetWidth-4;
                    contentHeight = contentDiv[0].offsetHeight-4;
                    if(contentWidth < 1)
                        return;

                    var ratio = oWidth / oHeight;

                    if(oWidth - contentWidth > oHeight - contentHeight)
                    {
                        img.width = contentWidth;
                        img.height = img.width / ratio;
                    }
                    else {
                        img.height = contentHeight;
                        img.width = img.height * ratio;
                    }

                    alignCenter();
                }

                function alignCenter()
                {
                    if(contentWidth - img.width > 0)
                        img.style.left = (contentWidth-img.width) /2 + "px";

                    if(contentHeight - img.height > 0)
                        img.style.top = (contentHeight-img.height) /2 + "px";
                }




            }
        }
    }]);