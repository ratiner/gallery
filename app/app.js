var config={theme:"standard",viewsPath:"app/views",max_title_length:20,apiPath:"http://dev.home.il/mgallery/api"};angular.module("mgallery",["ui.router","controllers","services","directives"]).config(["$stateProvider","$urlRouterProvider","$locationProvider",function(e,t,n){n.html5Mode(!0).hashPrefix("!"),t.otherwise("/error/404"),e.state("home",{url:"/",templateUrl:config.viewsPath+"/home.tmpl.html"}).state("filesMain",{url:"/files",templateUrl:config.viewsPath+"/files.tmpl.html"}).state("filesMain2",{url:"/files/",templateUrl:config.viewsPath+"/files.tmpl.html"}).state("files",{url:"/files/{path:.+}",templateUrl:config.viewsPath+"/files.tmpl.html"}).state("404",{url:"/error/:errorNumber",templateUrl:config.viewsPath+"/error.tmpl.html",controller:"errorCtrl"})}]).controller("AppCtrl",["$scope",function(e){e.layout="app/themes/"+config.theme+"/_layout.html"}]).run(["$state",function(e){}]),angular.module("controllers",[]),angular.module("services",[]),angular.module("directives",[]),window.console||(window.console={log:function(){},info:function(){},warn:function(){},error:function(){}}),angular.module("controllers").controller("errorCtrl",["$scope","$stateParams",function(e,t){e.error=t.errorNumber}]),angular.module("controllers").controller("filesCtrl",["$scope","$stateParams","$location","FileService","TagService",function(e,t,n,i,r){function a(){var n=t.path?t.path.split("/"):[];e.path=[];var i="";for(var r in n)i+=r>0?"/"+n[r]:n[r],e.path.push({label:n[r],url:i})}a(),e.InitialList=null,i.getList(t.path).then(function(t){e.InitialList=t,e.list=e.InitialList.slice()}),e.Search={SearchTags:[],typingTimer:null,typingTimeout:400,Fetch:function(t){this.typingTimer=setTimeout(function(){var n=e.Search.SearchTags,r=n.slice();return t&&t.length>3&&-1===r.indexOf(t)&&r.push(t),0==r.length?void(e.list=e.InitialList.slice()):void i.search(r).then(function(t){e.list=t})},this.typingTimeout)}},e.Preview={IsVisible:!1,Open:function(t){var n=e.list[t];return e.selection?e.ToggleCheck(n):(e.Preview.Index=t,void(e.Preview.Visible=!0))}},e.SelectedItems=new Array,e.ToggleCheck=function(t,n){t.selected=!t.selected,t.selected?e.SelectedItems.push(t):e.SelectedItems.splice(e.SelectedItems.indexOf(t),1),n&&n.stopPropagation()},e.FolderClick=function(e){n.path("/files/"+e)},e.btSelection=function(){e.selection=!e.selection}}]),angular.module("controllers").controller("homeCtrl",["$scope","$stateParams",function(e,t){e.test=new Array,e.TestVisible=!0}]),angular.module("directives").directive("popup",[function(){return{restrict:"AE",transclude:!0,scope:{visible:"=visible",top:"=top",left:"=left",width:"=width",height:"=height"},templateUrl:config.viewsPath+"/cmp/popup.tmpl.html",link:function(e,t,n){e.CloseClick=function(){e.visible=!1}}}}]),angular.module("directives").directive("preview",[function(){return{restrict:"AE",scope:{list:"=list",index:"=index",visible:"=visible"},templateUrl:config.viewsPath+"/cmp/preview.tmpl.html",link:function(e,t,n){e.visible=!1,e.item=null,e.ImageZIndex="10";var i={Top:"20%",Left:"25%",Width:"50%",Height:"60%"},r={Top:"0",Left:"0",Width:"100%",Height:"100%"};e.WindowState=i,e.$watch("index",function(t){0>t||!e.list||0==e.list.length||t>e.list.length||!e.list[t]||"folder"==e.list[t].type||(e.item=e.list[t])}),e.ImageClick=function(){e.ImageZIndex="10"==e.ImageZIndex?"30":"10"},e.Next=function(){e.index++},e.Prev=function(){e.index--},e.OpenExternal=function(e){var t=window.open(e,"_blank");t.focus()},e.ToggleTags=function(){e.TagsVisible=!e.TagsVisible},e.ToggleFullScreen=function(t){var n="Maximize"==t.target.innerHTML;e.WindowState=n?r:i,t.target.innerHTML=n?"Minimize":"Maximize"}}}}]),angular.module("directives").directive("resizeableImage",["$window",function(e){return{scope:{source:"=source"},link:function(t,n,i){function r(){if(h.style.left=0,h.style.top=0,l=u[0].offsetWidth-4,o=u[0].offsetHeight-4,!(1>l)){var e=c/s;c-l>s-o?(h.width=l,h.height=h.width/e):(h.height=o,h.width=h.height*e),a()}}function a(){l-h.width>0&&(h.style.left=(l-h.width)/2+"px"),o-h.height>0&&(h.style.top=(o-h.height)/2+"px")}var l,o,c,s,u=n.parent().parent().parent().parent(),h=n[0];angular.element(e).bind("resize",r),t.$watch(function(){return u.attr("style")},r),t.$watch(function(){return n.attr("style")},r),t.$watch("source",function(e){var t=new Image;t.onload=function(){c=this.width,s=this.height,h.src=this.src,h.width=c,h.height=s,r()},t.src=e})}}}]),angular.module("directives").directive("tagsEditor",["TagService",function(e){return{restrict:"AE",scope:{visible:"=visible",item:"=item"},templateUrl:config.viewsPath+"/cmp/tags-editor.tmpl.html",link:function(t,n,i){t.Selecected=[],t.$watch("item",function(e){e&&(t.Selecected.length=0,angular.forEach(e.db_tags,function(e,n){t.Selecected.indexOf(e.tag_name)<0&&t.Selecected.push(e.tag_name)}))}),t.Save=function(){e.saveOne(t.item.path,t.Selecected).then(function(n){e.getFileTags(t.item.path).then(function(e){t.item.db_tags=e})})}}}}]),angular.module("directives").directive("tagsTextBox",["TagService",function(e){return{restrict:"AE",scope:{selectedTags:"=model",onchanged:"&onchanged",ontype:"&ontype"},templateUrl:config.viewsPath+"/cmp/tags-text-box.tmpl.html",link:function(t,n,i){t.suggestions=[],t.selectedTags||(t.selectedTags=[]),t.selectedIndex=-1,t.removeTag=function(e){t.selectedTags.splice(e,1),onChangeEvent()},t.search=function(){e.search(t.searchText).then(function(e){var n=new Array;angular.forEach(e,function(e,t){n.push(e.name)}),-1===n.indexOf(t.searchText)&&n.unshift(t.searchText),t.suggestions=n,t.selectedIndex=-1,onTypeEvent(t.searchText)})},t.addToSelectedTags=function(e){var n="";n=!isNaN(e)&&e>-1?t.suggestions[e]:t.searchText,-1===t.selectedTags.indexOf(n)&&(t.selectedTags.push(n),t.searchText="",t.suggestions=[],onChangeEvent())},t.checkKeyDown=function(e){40===e.keyCode?(e.preventDefault(),t.selectedIndex+1!==t.suggestions.length&&t.selectedIndex++):38===e.keyCode?(e.preventDefault(),t.selectedIndex-1!==-1&&t.selectedIndex--):13===e.keyCode&&t.addToSelectedTags(t.selectedIndex)},t.$watch("selectedIndex",function(e){-1!==e&&(t.searchText=t.suggestions[t.selectedIndex])}),onChangeEvent=function(){if(t.onchanged){var e=t.onchanged();isFunction(e)&&e()}},onTypeEvent=function(e){if(t.ontype){var n=t.ontype();isFunction(n)&&n(e)}},isFunction=function(e){var t={};return e&&"[object Function]"===t.toString.call(e)}}}}]),angular.module("services").factory("FileService",["$http",function(e){function t(t){return t=t?t:"",e.get(config.apiPath+"/list/"+t,{cache:!0}).then(function(e){return i(e.data)})}function n(t){return e.post(config.apiPath+"/search",t).then(function(e){return i(e.data)})}function i(e){return angular.forEach(e,function(e,t){"folder"!=e.type&&(e.thumbnail=config.apiPath+"/thumb/256/"+e.path,e.full=config.apiPath+"/thumb/full/"+e.path,e.preview=config.apiPath+"/thumb/700/"+e.path),e.title=e.name.length<config.max_title_length?e.name:e.name.substring(0,config.max_title_length)+"..."}),e}return{getList:t,search:n}}]),angular.module("services").factory("TagService",["$http",function(e){function t(t,n){return e.post(config.apiPath+"/tag/one/"+t,n).then(function(e){return e.data})}function n(t){return e.get(config.apiPath+"/tag/one/"+t,{cache:!1}).then(function(e){return e.data})}function i(t){return t&&t.length>1?e.get(config.apiPath+"/tag/search/"+t,{cache:!1}).then(function(e){return e.data}):{then:function(){return new Array}}}return{saveOne:t,getFileTags:n,search:i}}]);
//# sourceMappingURL=/app.js.map