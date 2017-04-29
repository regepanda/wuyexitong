/**
 * Created by Admin on 2016/4/14.
 */
$admin_sCommunityGroupController = $app.controller("admin_sCommunityGroup",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };

    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sCommunityGroupController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/manage/sCommunityGroup/index.html',
        controller:'admin_sCommunityGroup_index'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//加载admin_sChild_index控制器
$admin_sCommunityGroupController.controller("admin_sCommunityGroup_index",function($scope,$http,$location){
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

    //查询物业公司信息并分页
    $scope.selectPage.getDataUrl="/Api_sAllCommunityGroup";
    //获取数据
    $scope.selectPage.getData();

    //添加物业公司
    $scope.addCommunityGroup = function(){
        var $limit={
            group_name:$scope.group_name,
            group_intro:$scope.group_intro
        };
        var url = $scope.buildUrlParam($limit,"/Api_addCommunityGroup");
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

    //修改物业公司
    $scope.updateCommunityGroup = function($group_id){
        var groupName = $('#'+$group_id+'_group_name').val();
        var groupIntro = $('#'+$group_id+'_group_intro').val();
        var $limit={
            group_id:$group_id,
            group_name:groupName,
            group_intro:groupIntro
        };
        var url = $scope.buildUrlParam($limit,"/Api_updateCommunityGroup");
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

    //删除物业公司
    $scope.deleteCommunityGroup = function($group_id){
        var $limit={
            group_id:$group_id
        };
        var url = $scope.buildUrlParam($limit,"/Api_deleteCommunityGroup");
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
