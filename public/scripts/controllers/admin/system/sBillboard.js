$admin_sBillboardController = $app.controller("admin_sBillboard",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    //$scope.name = "dcd";
    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sBillboardController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/system/sBillboard/index.html',
        controller:'admin_sBillboard_index'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//加载$admin_sBillboardController控制器
$admin_sBillboardController.controller("admin_sBillboard_index",function($scope,$http,$location){
    //查询公告信息并分页
    $scope.selectPage.getDataUrl="/Api_allBillboard";
    //获取数据
    $scope.selectPage.getData();

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

    //修改公告信息
    $scope.updateBillboard = function($billBoard_id){
        var billboard_title = $('#'+$billBoard_id+'_billboard_title').val();
        var billboard_detail = $('#'+$billBoard_id+'_billboard_detail').val();
        var $limit={billboard_id:$billBoard_id,billboard_title:billboard_title,billboard_detail:billboard_detail};
        var url = $scope.buildUrlParam($limit,"/Api_updateBillboard");
        //发送求情修改公告信息
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

    $scope.deleteBillboard = function($billBoard_id){
        //alert($billBoard_id);
        var $limit={billboard_id:$billBoard_id};
        var url = $scope.buildUrlParam($limit,"/Api_deleteBillboard");
        //发送请求删除公告信息
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

    $scope.addBillboard = function(){
        var $limit={billboard_title:$scope.billboard_title,billboard_detail:$scope.billboard_detail};
        var url = $scope.buildUrlParam($limit,"/Api_addBillboard");
        //发送请求添加公告信息
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
});