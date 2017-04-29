/**
 * Created by RagPanda on 2016/3/9.
 */
$admin_sRequestController =
    $app.controller("admin_sRequest",function($scope,$location,SelectPageService)
{
    $scope.goIndex = function(){
        $location.path("/index")
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };
    $scope.aRequest = function(){
        $location.path("/addRequest")
    };

    $scope.selectPage = SelectPageService;


    /*
     $scope.cache = new cacheManager;
    $scope.getObj = function(){
        $scope.selectPage = $scope.cache.getObj();

    };
    $scope.selectPage.successCallback=function(){
        if($scope.selectPage.getTime>1)
        {
            $scope.cache.putObj($scope.selectPage);
        }
    };*/

    //$scope.cache.putObj($scope.selectPage);


});
$admin_sRequestController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/tenement/sRequest/index.html",
        controller:"admin_sRequest_index"
    }).when("/detail/:id",{
        templateUrl:"/views/admin/tenement/sRequest/detail.html",
        controller:"admin_sRequest_detail"
    }).when("/addRequest",{
        templateUrl:"/views/admin/tenement/sRequest/add.html",
        controller:"admin_sRequest_add"
    }).otherwise({redirectTo:'/index'});

}]);

//用于手动创建请求的控制器
$admin_sRequestController.controller("admin_sRequest_add",function($scope,$http,$location){
    //构建get请求路由，路由后面跟变量值
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
    //得到所有的请求类型
    $scope.classData = [
        {class_id:4,class_name:"钟点工"},
        {class_id:5,class_name:"陪护工"},
        {class_id:6,class_name:"保洁服务"},
        {class_id:7,class_name:"月嫂"},
        {class_id:8,class_name:"管道疏通"},
        {class_id:9,class_name:"开锁服务"},
        {class_id:10,class_name:"水电修理"},
        {class_id:11,class_name:"家店修理"},
        {class_id:12,class_name:"房屋修理"},
        {class_id:13,class_name:"定制需求"},
        {class_id:16,class_name:"四点半学校"}

    ];


    //开始添加请求
    $scope.addRequest = function(){
        var $limit={request_user:$scope.requestUser,request_class:$scope.requestClass,request_user_intro:$scope.requestUserIntro};
        var url = $scope.buildUrlParam($limit,"/api_addRequest");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"添加请求成功");
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

$admin_sRequestController.controller("admin_sRequest_index",function($scope,$http,$location){
    //查看用户请求数据并分页、条件查找、排序
    $scope.selectPage.getDataUrl="/test_pl_angularApiResTest";
    $scope.selectPage.getData();


    //得到所有的请求类型
    $scope.classData = [
        {class_id:4,class_name:"钟点工"},
        {class_id:5,class_name:"陪护工"},
        {class_id:6,class_name:"保洁服务"},
        {class_id:7,class_name:"月嫂"},
        {class_id:8,class_name:"管道疏通"},
        {class_id:9,class_name:"开锁服务"},
        {class_id:10,class_name:"水电修理"},
        {class_id:11,class_name:"家店修理"},
        {class_id:12,class_name:"房屋修理"},
        {class_id:13,class_name:"定制需求"},
        {class_id:16,class_name:"四点半学校"}

    ];
    //得到请求状态
    $scope.statusData =[
        {status_id:6,status_name:"已提交"},
        {status_id:7,status_name:"准备处理"},
        {status_id:8,status_name:"处理中"},
        {status_id:9,status_name:"处理完成"},
        {status_id:10,status_name:"取消请求"}
    ];

    //修改当前请求状态为准备处理[adminIntro,price=null]
    $scope.setStatusReadyHandle = function($request_id){
        var request_admin_intro = $('#'+$request_id+'_request_admin_intro').val();
        var request_price = $('#'+$request_id+'_request_price').val();
        $http.post("/api_setStatusReadyHandle",{request_id:$request_id,request_price:request_price,request_admin_intro:request_admin_intro}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把提交状态修改为准备处理");
                __component_messageBar_open();
                //$location.path("/detail/"+$scope.id);
                //$location.path("/index");
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
        });
    };

    //设定状态到处理中，只有准备处理可以用
    $scope.setStatusInHandle = function($request_id){

        $http.post("/api_setStatusInHandle",{request_id:$request_id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把准备处理状态修改为处理中");
                __component_messageBar_open();
                //$location.path("/index");
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
        });
    };

    //设定状态到处理完成，只有处理中可以用
    $scope.setStatusHaveHandle = function($request_id){

        $http.post("/api_setStatusHaveHandle",{request_id:$request_id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把处理中状态成功修改为完成处理");
                __component_messageBar_open();
                //$location.path("/index");
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
        });
    };

    //删除当前请求
    $scope.requestDelete = function($request_id){
        $http.post("/api_deleteRequest",{request_id:$request_id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"删除成功");
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
        });
    };
});

$admin_sRequestController.controller("admin_sRequest_detail",function($scope,$http,$location,$routeParams){

    //得到详情信息，并带刷新功能
    $scope.getData = function(){
        $scope.id = $routeParams.id;
        //根据ID实例化一个Request对象，得到当条记录
        $scope.detailData = {};
        $http.post("/test_pl_angularDetailRes",{request_id:$scope.id}).success(function(response){
            $scope.detailData = response;
        });
    };
    $scope.getData();


    //修改请求【管理员描述】
    $scope.formSubmit = function(){

        $http.post("/api_updateRequest",{request_id:$scope.id,request_admin_intro:$scope.updateData}).success(function(response){
            if(response.status == true)
            {
                //$location.path("/detail/"+$scope.id);
                __component_messageBar_setMessage(true,"修改成功");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

//修改当前请求状态为准备处理[adminIntro,price=null]
    $scope.setStatusReadyHandle = function(){
        var request_admin_intro = $scope.request_admin_intro;
        var request_price = $scope.request_price;
        var request_id = $scope.id;
        $http.post("/api_setStatusReadyHandle",{request_id:request_id,request_price:request_price,request_admin_intro:request_admin_intro}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把提交状态修改为准备处理");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

    //设定状态到处理中，只有准备处理可以用
    $scope.setStatusInHandle = function(){

        $http.post("/api_setStatusInHandle",{request_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把准备处理状态修改为处理中");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

    //设定状态到处理完成，只有处理中可以用
    $scope.setStatusHaveHandle = function(){

        $http.post("/api_setStatusHaveHandle",{request_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已把处理中状态成功修改为完成处理");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

    //设定状态到已取消,只有已提交和准备处理可以用
    $scope.setStatusCancel = function () {
        $http.post("/api_setStatusCancel",{request_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"已成功切换到取消请求状态");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

    //$scope.selectPage.testFunc();
});