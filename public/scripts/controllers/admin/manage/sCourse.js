/**
 * Created by Admin on 2016/4/14.
 */
$admin_sCourseController = $app.controller("admin_sCourse",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };

    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sCourseController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/manage/sCourse/index.html',
        controller:'admin_sCourse_index'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//加载admin_sCourse_index控制器
$admin_sCourseController.controller("admin_sCourse_index",function($scope,$http,$location){
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

    //获取所有的监控信息
    $scope.getMonitor = function()
    {
        $scope.monitorData = {};
        var $limit={};
        var url = $scope.buildUrlParam($limit,"/Api_getMonitor");
        $http.get(url).success(function(response) {
            $scope.monitorData = response;
        });
    };
    $scope.getMonitor();

    //查询学校信息并分页
    $scope.selectPage.getDataUrl="/Api_sAllCourse";
    //获取数据
    $scope.selectPage.getData();

    //添加学校信息
    $scope.addCourse = function(){
        var $limit={
            course_name:$scope.courseName,
            course_school:$scope.courseSchool,
            course_position:$scope.coursePosition,
            course_date:$scope.courseDate,
            course_monitor:$scope.courseMonitor
        };
        var url = $scope.buildUrlParam($limit,"/Api_addCourse");
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

    //修改学校信息
    $scope.updateCourse = function($course_id){
        var course_name = $('#'+$course_id+'_course_name').val();
        var course_school = $('#'+$course_id+'_course_school').val();
        var course_position = $('#'+$course_id+'_course_position').val();
        var course_date = $('#'+$course_id+'_course_date').val();
        var course_monitor = $('#'+$course_id+'_course_monitor').val();
        var $limit={
            course_id:$course_id,
            course_name:course_name,
            course_school:course_school,
            course_position:course_position,
            course_date:course_date,
            course_monitor:course_monitor
        };
        var url = $scope.buildUrlParam($limit,"/Api_updateCourse");
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

    //删除学校
    $scope.deleteCourse = function($course_id){
        var $limit={
            course_id:$course_id
        };
        var url = $scope.buildUrlParam($limit,"/Api_deleteCourse");
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

    //指定视频监控
    $scope.appointMonitor = function($course_id){
        var courseMonitor = $('#'+$course_id+'_monitor').val();
        var $limit={
            course_id:$course_id,
            course_monitor:courseMonitor
        };
        var url = $scope.buildUrlParam($limit,"/Api_appointMonitor");
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
