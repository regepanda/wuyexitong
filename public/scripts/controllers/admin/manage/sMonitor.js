/**
 * Created by Admin on 2016/4/19.
 */
$admin_sMonitorController = $app.controller("admin_sMonitor",function($scope,$location,SelectPageService)
{
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

    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.selectPage = SelectPageService;

});

$admin_sMonitorController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/manage/sMonitor/index.html",
        controller:"admin_sMonitor_index"
    }).otherwise({
        redirectTo:'/index'
    });
}]);

$admin_sMonitorController.controller("admin_sMonitor_index",function($scope,$http,$location){
    //查询所有的监控信息
    $scope.selectPage.getDataUrl="/admin_sAllMonitor";
    $scope.selectPage.getData();

    //添加监控
    $scope.addMonitor = function(){
        var limit = {
            monitor_name:$scope.monitorName,
            monitor_device_id:$scope.monitorDeviceId,
            monitor_device_password:$scope.monitorDevicePassword,
            monitor_device_area:$scope.monitorDeviceArea
        };
        var url = $scope.buildUrlParam(limit,"/admin_addMonitor");
        $http.get(url).success(function(response){
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

    //修改
    $scope.updateMonitor = function($monitor_id){
        var limit = {
            monitor_id:$monitor_id,
            monitor_name:$('#'+$monitor_id+'_monitor_name').val(),
            monitor_device_id:$('#'+$monitor_id+'_monitor_device_id').val(),
            monitor_device_password:$('#'+$monitor_id+'_monitor_device_password').val(),
            monitor_device_area:$('#'+$monitor_id+'_monitor_device_area').val()
        };
        var url = $scope.buildUrlParam(limit,"/admin_updateMonitor");
        $http.get(url).success(function(response){
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

    //删除
    $scope.deleteMonitor = function($monitor_id){
        var limit = {monitor_id:$monitor_id};
        var url = $scope.buildUrlParam(limit,"/admin_deleteMonitor");
        $http.get(url).success(function(response){
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
});