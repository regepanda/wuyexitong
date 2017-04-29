/**
 * Created by pengliang on 2016/3/12.
 */
$admin_sPaymentController = $app.controller("admin_sPayment",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };
    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sPaymentController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/manage/sPayment/index.html',
        controller:'admin_sPayment_index'
    }).when("/detail/:id",{
        templateUrl:'/views/admin/manage/sPayment/detail.html',
        controller:'admin_sPayment_detail'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//查询支付单信息，分页在此控制器里面
$admin_sPaymentController.controller("admin_sPayment_index",function($scope){

        //查询支付单信息并分页
        $scope.selectPage.getDataUrl="/admin_sAllPayment";

        //$scope.selectPage.changeLimit("desc",true);
        $scope.selectPage.getData();


    //得到类别信息
    $scope.classData = [
        {class_id:1,class_name:"支付宝"},
        {class_id:2,class_name:"微信"},
        {class_id:3,class_name:"翼支付"}


    ];
    //得到状态信息
    $scope.statusData =[
        {status_id:1,status_name:"未支付"},
        {status_id:2,status_name:"已支付"},
        {status_id:3,status_name:"取消支付"},
        {status_id:4,status_name:"退款中"},
        {status_id:5,status_name:"退款完成"}
    ];
});

//查询支付单详情,更新、删除
$admin_sPaymentController.controller("admin_sPayment_detail",function($scope,$http,$location,$routeParams){

    //得到详情信息，并有刷新功能
    $scope.getData = function(){
        $scope.id = $routeParams.id;
        //根据ID实例化一个Request对象，得到当条记录
        $scope.detailData = {};
        $http.post("/api_paymentDetail",{payment_id:$scope.id}).success(function(response){
            $scope.detailData = response;
        });
    };
    $scope.getData();

    //修改该条支付单【价格】
    $scope.paymentUpdate = function(){
        $http.post("/api_paymentUpdate",{payment_id:$scope.id,payment_status:$scope.detailData.payment_status,payment_price:$scope.detailData.payment_price}).success(function(response){
            if(response.status == 3)
            {
                __component_messageBar_setMessage(false,"失败，支付状态只有在未支付时才可以修改价格");
                __component_messageBar_open();
                $scope.getData();
            }
            else if(response.status == true)
            {
                __component_messageBar_setMessage(true,"修改成功");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //删除该条支付单
    $scope.paymentDelete = function(){
        $http.post("/api_paymentDelete",{payment_id:$scope.id,payment_status:$scope.detailData.payment_status}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"删除成功");
                __component_messageBar_open();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //设定状态已经支付
    $scope.setStatusAlreadyPay = function () {
        $http.post("/api_setStatusAlreadyPay",{payment_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"成功切换到已支付状态");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //设定状态申请退款
    $scope.setStatusAskReturnPay = function(){
        $http.post("/api_setStatusAskReturnPay",{payment_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"成功切换到申请退款状态");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //设定状态退款中
    $scope.setStatusInReturnPay = function(){
        $http.post("/api_setStatusInReturnPay",{payment_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"成功切换到退款中状态");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //设定状态退款完成
    $scope.setStatusAlreadyReturnPay = function(){
        $http.post("/api_setStatusAlreadyReturnPay",{payment_id:$scope.id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"成功切换到完成退款状态");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                //$scope.getData();
            }
        });
    };

    //设定状态取消支付
    $scope.setStatusCancelPay = function(){
        $http.post("/api_setStatusCancelPay",{payment_id:$scope.id}).success(function(response){
           if(response.status == true)
           {
               __component_messageBar_setMessage(true,"成功切换到取消支付状态");
               __component_messageBar_open();
               $scope.getData();
           }
            else
           {
               __component_messageBar_setMessage(false,response.message);
               __component_messageBar_open();
               //$scope.getData();
           }
        });
    };
});

