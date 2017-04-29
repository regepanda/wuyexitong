/**
 * Created by pengliang on 2016/3/26.
 */
$admin_sChildController = $app.controller("admin_sChild",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };

    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sChildController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/manage/sChild/index.html',
        controller:'admin_sChild_index'
    }).when("/detail/:id",{
        templateUrl:'/views/admin/manage/sChild/detail.html',
        controller:'admin_sChild_detail'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//加载admin_sChild_index控制器
$admin_sChildController.controller("admin_sChild_index",function($scope,$http,$location){
    //构建get路由，路由后面跟变量值
    $scope.buildUrlParam = function($limit,$url)
    {
        var url = $url;
        url+="?";
        for(var i in $limit)
        {
            url += "&" + i + "=";
            url += $limit[i] ;//+ "&";

        };
        return url;
    };
    //获取所有的学校信息
    $scope.getCourse = function(){
        var $limit={};
        var url = $scope.buildUrlParam($limit,"/admin_getCourse");
        $scope.courseData = {};
        $http.get(url).success(function(response) {
            $scope.courseData = response;
        });
    };
    $scope.getCourse();
    //查询儿童信息并分页
    $scope.selectPage.getDataUrl="/Api_allChild";
    //获取数据
    $scope.selectPage.getData();

    //添加儿童信息
    $scope.addChild = function(){
        var $limit={
            child_name:$scope.childName,
            child_age:$scope.childAge,
            child_user:$scope.childUser,
            child_start:$scope.childStart,
            child_end:$scope.childEnd,
            child_course:$scope.childCourse
        };
        var url = $scope.buildUrlParam($limit,"/admin_addChild");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
        });
    };

    //给儿童分配学校
    $scope.distributionSchool = function($child_id){
        var childCourse = $('#'+$child_id+'_school').val();
        var $limit={
            child_id:$child_id,
            child_course:childCourse
        };
        var url = $scope.buildUrlParam($limit,"/admin_distributionSchool");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
        });
    }
});

//加载admin_sChild_detail控制器
$admin_sChildController.controller("admin_sChild_detail",function($scope,$http,$location,$routeParams){
    $scope.id = $routeParams.id;
    //构建get路由，路由后面跟变量值
    $scope.buildUrlParam = function($limit,$url)
    {
        var url = $url;
        url+="?";
        for(var i in $limit)
        {
            url += "&" + i + "=";
            url += $limit[i] ;//+ "&";

        };
        return url;
    };

    //这里写一个函数，得到详情信息，点击详情时加载，负载刷新功能
    $scope.getData = function(){
        var $limit={child_id:$scope.id};
        var url = $scope.buildUrlParam($limit,"/admin_sChildDetail");
        $scope.detailData = {};
        $http.get(url).success(function(response) {
            $scope.detailData = response;
        });
    };
    //获取所有的课程信息
    $scope.getCourse = function(){
        var $limit={};
        var url = $scope.buildUrlParam($limit,"/admin_getCourse");
        $scope.courseData = {};
        $http.get(url).success(function(response) {
            $scope.courseData = response;
        });
    };
    $scope.getCourse();
    $scope.getData();

    //修改儿童信息
    $scope.childUpdate = function(){
        var $limit={child_id:$scope.id,
                    child_name:$scope.detailData.child_name,
                    child_age:$scope.detailData.child_age,
                    child_course:$scope.detailData.child_course
        };
        var url = $scope.buildUrlParam($limit,"/admin_childUpdate");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
        });
    };

    //删除
    $scope.childDelete = function(){
        var $limit={child_id:$scope.id};
        var url = $scope.buildUrlParam($limit,"/admin_deleteUpdate");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
        });
    };
});