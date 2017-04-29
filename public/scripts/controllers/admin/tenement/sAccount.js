/**
 * Created by RagPanda on 2016/3/9.
 */
$admin_sAccountController = $app.controller("admin_sAccount",function($scope,$location,SelectPageService)
    {
        $scope.goIndex = function(){
            $location.path("/index")
        };
        $scope.goDetail = function($id){
            $location.path("/detail/"+$id);
        };
        $scope.selectPage = SelectPageService;

    });
$admin_sAccountController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/tenement/sAccount/index.html",
        controller:"admin_sAccount_index"
    }).when("/detail/:id",{
        templateUrl:"/views/admin/tenement/sAccount/detail.html",
        controller:"admin_sAccount_detail"
    }).otherwise({redirectTo:'/index'});

}]);

$admin_sAccountController.controller("admin_sAccount_index",function($scope){
    $scope.selectPage.getDataUrl="/admin_api_sAccount";
    $scope.selectPage.getData();

});

$admin_sAccountController.controller("admin_sAccount_detail",function($scope, $routeParams,$http){
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
        //账户详情
        var $accountDetaiLimit={id:$scope.id};
        var accountDetailUrl = $scope.buildUrlParam($accountDetaiLimit,"/manage_admin_api_sAccountDetail");
        $scope.detailAccountData = {};
        $http.get(accountDetailUrl).success(function(response) {
            if(response.status ==false)
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
            $scope.detailAccountData = response;
        });

        //与此账户相关的支付单详情
        var $paymentDetaiLimit={id:$scope.id};
        var paymentDetailUrl = $scope.buildUrlParam($paymentDetaiLimit,"/manage_admin_api_sPaymentAccountDetail");
        $scope.detailPaymentData = {};
        $http.get(paymentDetailUrl).success(function(response) {
            if(response.status ==false)
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
            }
            $scope.detailPaymentData = response;
        });
    };
    //进此页面时刷新下
    $scope.getData();

    //修改此账户的积分
    $scope.updateIntegration = function(){
        var $Limit={account_id:$scope.id,account_integration:$scope.detailAccountData.account_integration};
        var Url = $scope.buildUrlParam($Limit,"/admin_api_updateAccountIntegration");
        $http.get(Url).success(function(response) {
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
});