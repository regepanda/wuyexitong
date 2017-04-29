/**
 * Created by zc on 2016/3/27.
 */
$admin_sCommunityController = $app.controller("admin_sCommunity",function($scope,$location,SelectPageService)
{
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.selectPage = SelectPageService;

});

$admin_sCommunityController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/manage/sCommunity/index.html",
        controller:"admin_sCommunity_index"
    }).otherwise({
        redirectTo:'/index'
    });
}]);

$admin_sCommunityController.controller("admin_sCommunity_index",function($scope,$http,$location){
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
    //获取所有的物业公司信息
    $scope.getCommunityGroup = function(){
        var $limit={};
        var url = $scope.buildUrlParam($limit,"/admin_getCommunityGroup");
        $scope.communityGroupData = {};
        $http.get(url).success(function(response) {
            $scope.communityGroupData = response;
        });
    };
    $scope.getCommunityGroup();
    //获取小区数据
    $scope.selectPage.getDataUrl="/admin_api_sCommunity";
    $scope.selectPage.getData();
    //添加小区
    $scope.addCommunity = function()
    {
        $scope.communityAddData={
            community_name:$scope.community_name,
            community_city:$scope.community_city,
            community_province: $scope.community_province,
            community_intro:$scope.community_intro,
            community_address:$scope.community_address
        };
        $http.post("/admin_aCommunity",$scope.communityAddData).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
        });
    };


    $scope.updateCommunity = function($community_id)
    {
        var community_name = $('#'+$community_id+'_community_name').val();
        var community_city = $('#'+$community_id+'_community_city').val();
        var community_province = $('#'+$community_id+'_community_province').val();
        var community_intro = $('#'+$community_id+'_community_intro').val();
        var community_address = $('#'+$community_id+'_community_address').val();
        var community_group = $('#'+$community_id+'_community_group').val();
        $scope.communityUpData={community_id:$community_id,community_name:community_name,community_city:community_city,community_province:
            community_province,community_intro:community_intro,community_address:community_address,community_group:community_group};
        $http.post("/admin_uCommunity",$scope.communityUpData).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
        });
    };


    $scope.delCommunity = function($community_id)
    {
        $scope.limit={id:$community_id};

            var $url = "/admin_dCommunity";
            $url = $scope.buildUrlParam($scope.limit,$url);
            $http.get($url).success(function(response)
            {
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $scope.selectPage.getData();
                $location.path("/index");
            }
        });
    }

});