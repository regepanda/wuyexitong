/**
 * Created by pengliang on 2016/3/19.
 */
$admin_sLogController = $app.controller("admin_sLog",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };
    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sLogController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/system/sLog/index.html',
        controller:'admin_sLog_index'
    }).when("/detail/:id",{
        templateUrl:'/views/admin/system/sLog/detail.html',
        controller:'admin_sLog_detail'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//查询支付单信息，分页在此控制器里面
$admin_sLogController.controller("admin_sLog_index",function($scope){
    //查询支付单信息并分页
    $scope.selectPage.getDataUrl="/admin_sAllLog";

    $scope.selectPage.getData();
    //得到类别信息
    $scope.classData = [
        {class_id:1,class_name:"支付宝"},
        {class_id:2,class_name:"微信"},
        {class_id:3,class_name:"翼支付"},
        {class_id:15,class_name:"系统账户"},
        {class_id:4,class_name:"钟点工"},
        {class_id:5,class_name:"陪护工"},
        {class_id:6,class_name:"保洁服务"},
        {class_id:7,class_name:"月嫂"},
        {class_id:8,class_name:"管道疏通"},
        {class_id:9,class_name:"开锁服务"},
        {class_id:10,class_name:"水电修理"},
        {class_id:11,class_name:"家电修理"},
        {class_id:12,class_name:"房屋修理"},
        {class_id:13,class_name:"定制需求"},
        {class_id:16,class_name:"四点半学校"},
        {class_id:14,class_name:"物业费"}
    ];
    //构建等级信息
    $scope.levelData = [
        {level_id:1,level_name:1},
        {level_id:2,level_name:2},
        {level_id:3,level_name:3},
        {level_id:4,level_name:4},
        {level_id:5,level_name:5},
        {level_id:6,level_name:6},
        {level_id:7,level_name:7}
    ]
});

//详情
$admin_sLogController.controller("admin_sLog_detail",function($scope, $routeParams,$http){
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
    //这里我们写一个函数，用于获取详情数据，并且有刷新功能
    $scope.getData = function(){
        //记录详情
        var $limit={id:$scope.id};
        var url = $scope.buildUrlParam($limit,"/admin_sLogDetail");
        //根据ID实例化一个DBLog对象，得到当条记录
        $scope.detailData = {};
        $http.get(url).success(function(response) {
            $scope.detailData = response;
        });
    };
    $scope.getData();
});