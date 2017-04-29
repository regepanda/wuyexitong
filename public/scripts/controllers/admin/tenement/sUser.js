/**
 * Created by RagPanda on 2016/3/9.
 */
$admin_sUserController =
    $app.controller("admin_sUser",function($scope,$location,SelectPageService)
    {
        $scope.goIndex = function(){
            $location.path("/index");
        };
        $scope.goDetail = function($id){
            $location.path("/detail/"+$id);
        };
        $scope.selectPage = SelectPageService;

    });
$admin_sUserController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/tenement/sUser/index.html",
        controller:"admin_sUser_index"
    }).when("/detail/:id",{
        templateUrl:"/views/admin/tenement/sUser/detail.html",
        controller:"admin_sUser_detail"
    }).otherwise({redirectTo:'/index'});

}]);

$admin_sUserController.controller("admin_sUser_index",function($scope){
    $scope.selectPage.getDataUrl="/admin_api_sUser";
    $scope.selectPage.getData();
});

$admin_sUserController.controller("admin_sUser_detail",function($scope, $routeParams,$http){
    $scope.id = $routeParams.id;

    //路由后面跟变量值
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

    $scope.getData = function()
    {
        //用户详情
        var $limit={id:$scope.id};
        var url = $scope.buildUrlParam($limit,"/manage_admin_api_sUserDetail");
        //根据ID实例化一个Request对象，得到当条记录
        $scope.detailUserData = {};
        $http.get(url).success(function(response) {
            $scope.detailUserData = response;
        });

        //关于此用户的请求
        var $requestDetailLimit = {id:$scope.id};
        var requestDetailUrl = $scope.buildUrlParam($requestDetailLimit,"/manage_admin_api_sRequestDetail");
        $scope.detailRequestData = {};
        $http.get(requestDetailUrl).success(function(response) {
            $scope.detailRequestData = response;
        });

        //关于此用户的车子
        var $carDetaiLimit={id:$scope.id};
        var carDetailUrl = $scope.buildUrlParam($carDetaiLimit,"/manage_admin_api_sCarDetail");
        $scope.detailCarData = {};
        $http.get(carDetailUrl).success(function(response) {
            $scope.detailCarData = response;
        });

        //关于此用户的房子
        var $houseDetaiLimit={id:$scope.id};
        var houseDetailUrl = $scope.buildUrlParam($houseDetaiLimit,"/manage_admin_api_sHouseDetail");
        $scope.detailHouseData = {};
        $http.get(houseDetailUrl).success(function(response) {
            $scope.detailHouseData = response;
        });

        //关于此用户的支付记录,需要连表查询
        var $paymentDetaiLimit={id:$scope.id};
        var paymentDetailUrl = $scope.buildUrlParam($paymentDetaiLimit,"/manage_admin_api_sPaymentDetail");
        $scope.detailPaymentData = {};
        $http.get(paymentDetailUrl).success(function(response) {
            $scope.detailPaymentData = response;
        });

        //关于此用户的缴费记录
        var $taxDetaiLimit={id:$scope.id};
        var taxDetailUrl = $scope.buildUrlParam($taxDetaiLimit,"/manage_admin_api_sTaxDetail");
        $scope.detailTaxData = {};
        $http.get(taxDetailUrl).success(function(response) {
            $scope.detailTaxData = response;
        });

        //用户组信息
        $scope.detailUserGroupData = {};
        $http.get("/manage_admin_api_sUserGroupDetail").success(function(response) {
            $scope.detailUserGroupData = response;
        });


    //    $scope.stopShow = 1;   //点击修改按钮让模态框隐藏
    };
    //进此页面时刷新下
    $scope.getData();

   $scope.updateUser = function()
   {
       var $limit={user_id:$scope.detailUserData.user_id,user_username:$scope.detailUserData.user_username,user_nickname:$scope.detailUserData.user_nickname,
           user_phone:$scope.detailUserData.user_phone,user_phone_backup:$scope.detailUserData.user_phone_backup,
           user_group:$scope.detailUserData.user_group,user_sex:$scope.detailUserData.user_sex};
       var url = $scope.buildUrlParam($limit,"/admin_uUser");
       $http.get(url).success(function(response){
           if(response.status == true)
           {
            //   $scope.stopShow = 0; //点击修改按钮让模态框隐藏
               __component_messageBar_setMessage(true,"修改用户成功！");
               __component_messageBar_open();
               $location.path("/index");
           }
           else
           {
            //   $scope.stopShow = 0; //点击修改按钮让模态框隐藏
               __component_messageBar_setMessage(false,response.message);
               __component_messageBar_open();
           }
    });

   };

});




