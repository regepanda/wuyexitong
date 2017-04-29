/**
 * Created by pengliang on 2016/3/12.
 */
$admin_sTaxController = $app.controller("admin_sTax",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };
    $scope.aTax = function(){
        $location.path("/addTax")
    };

    $scope.selectPage = SelectPageService;
});

$admin_sTaxController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/tenement/sTax/index.html",
        controller:"admin_sTax_index"
    }).when("/detail/:id",{
        templateUrl:"/views/admin/tenement/sTax/detail.html",
        controller:"admin_sTax_detail"
    }).when("/addTax",{
        templateUrl:"/views/admin/tenement/sTax/add.html",
        controller:"admin_sTax_add"
    }).otherwise({redirectTo:'/index'});
}]);

//用于手动缴费的控制器
$admin_sTaxController.controller("admin_sTax_add",function($scope,$http,$location){
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
    //得到所有的缴费类型
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

    //开始添加缴费信息
    $scope.addTax = function() {
        var $limit={tax_user:$scope.taxUser,tax_class:$scope.taxClass,tax_time:$scope.taxTime,tax_price:$scope.taxPrice,tax_intro:$scope.taxIntro};
        var url = $scope.buildUrlParam($limit,"/api_addTax");
        $http.get(url).success(function(response) {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"添加缴费成功");
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

//查询缴费信息，分页在此控制器里面
$admin_sTaxController.controller("admin_sTax_index",function($scope){
    $scope.selectPage.getDataUrl="/test_pl_angularApiTaxTest";
    $scope.selectPage.getData();

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
    $scope.statusData =[
        {status_id:11,status_name:"待付费"},
        {status_id:12,status_name:"已付费"},
        {status_id:13,status_name:"取消付费"},
        {status_id:14,status_name:"申请退款"},
        {status_id:15,status_name:"以过期"}
    ];

});

//用来查看详情，更新、删除
$admin_sTaxController.controller("admin_sTax_detail",function($scope,$http,$location,$routeParams){

    //得到详情信息，并带刷新功能
    $scope.getData = function(){
        $scope.id = $routeParams.id;
        //根据ID实例化一个Tax对象，得到当条记录
        $scope.detailData = {};
        $http.post("/api_taxDetail",{tax_id:$scope.id}).success(function(response){
            $scope.detailData = response;
        });
    };
    $scope.getData();

    //删除当前缴费信息
    $scope.taxDelete = function(){
        $http.post("/api_taxDelete",{tax_id:$scope.id}).success(function(response){
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
                $scope.getData();
            }
        });
    };

    //修改当前缴费信息
    $scope.taxUpdate = function(){
        $http.post("/api_taxUpdate",{tax_payment:$scope.detailData.tax_payment,tax_price:$scope.tax_price}).success(function(response){
            if(response.status == true)
            {
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

    //修改当前状态为以付费
    $scope.setStatusHavePay = function(){
        $http.post("/api_setStatusHavePay",{tax_id:$scope.id,tax_status:$scope.detailData.tax_status}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"以成功将当前状态切换到以付费状态");
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

    //切换当前状态为申请退款
    /*$scope.setStatusDrawBack = function () {
        $http.post("/api_setStatusDrawBack",{tax_id:$scope.id,tax_status:$scope.detailData.tax_status}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"以成功将当前状态切换到申请退款");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,"切换状态失败");
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };*/

    //切换状态到取消付费
    $scope.setStatusCancelPay = function(){
        $http.post("/api_setStatusCancelPayTax",{tax_id:$scope.id,tax_status:$scope.detailData.tax_status}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"以成功将当前状态切换到取消付费");
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

    //切换状态到已过期
    $scope.setStatusOutDate = function(){
        $http.post("/api_setStatusOutDate",{tax_id:$scope.id,tax_status:$scope.detailData.tax_status}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"以成功将当前状态切换到以过期");
                __component_messageBar_open();
                $scope.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,"切换状态失败");
                __component_messageBar_open();
                $scope.getData();
            }
        });
    };

    //这里手动构造一个get请求
    /*$scope.changeMethod = function(){
        $limit = $scope.limit;

        $scope.promise={};

        //更换请求方法
        if($scope.getDataMethod == "GET")
        {
            var url = $scope.getDataUrl;
            url+="?";
            for(var i in $scope.limit)
            {
                url += "&" + i + "=";
                url += $scope.limit[i] ;//+ "&";

            };
            $scope.promise=$http.get(url);
        }
        if($scope.getDataMethod == "POST")
        {
            $scope.promise=$http.post($scope.getDataUrl,$limit);
        }
    };*/

});

