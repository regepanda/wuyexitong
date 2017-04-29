/**
 * Created by pengliang on 2016/3/17.
 */
$admin_sMessageController = $app.controller("admin_sMessage",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($id){
        $location.path("/detail/"+$id);
    };
    $scope.sendMessage = function(){
        $location.path("/send");
    };
    $scope.selectPage = SelectPageService;
});

//加载配置
$admin_sMessageController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/system/sMessage/index.html',
        controller:'admin_sMessage_index'
    }).when("/detail/:id",{
        templateUrl:'/views/admin/system/sMessage/detail.html',
        controller:'admin_sMessage_detail'
    }).when("/send",{
        templateUrl:'/views/admin/system/sMessage/send.html',
        controller:'admin_sMessage_send'
    }).otherwise({
        redirectTo:'/index'
    });
}]);

//加载admin_sMessage_index控制器
$admin_sMessageController.controller("admin_sMessage_index",function($scope,$http,$location){
    //查询支付单信息并分页
    $scope.selectPage.getDataUrl="/Api_allMessage";
    //获取数据
    $scope.selectPage.getData();

    //批量删除系统消息
    $scope.messageGroup = [];
    $scope.opGroup = function(messageId){

        for (var i = 0; i < $scope.messageGroup.length; i++)
        {
            if ($scope.messageGroup[i] == messageId){
                $scope.messageGroup.splice(i,1);
                return;
            }
        }
        $scope.messageGroup.push(messageId);

    };
    //开始删除
    $scope.deleteGroup = function(){
        if($scope.messageGroup.length != 0)
        {
            $scope.status = {};
            for (var i = 0; i < $scope.messageGroup.length; i++)
            {
                $http.post("/Api_messageDelete", {message_id: $scope.messageGroup[i]}).success(function (response) {
                    $scope.status = response.status;
                    if(response.status == false)
                    {
                        __component_messageBar_setMessage(false,response.message);
                        __component_messageBar_open();
                    }
                    else
                    {
                        __component_messageBar_setMessage(true,"删除成功");
                        __component_messageBar_open();

                    }
                });
            }
            $scope.selectPage.getData();
        }
        else
        {
            __component_messageBar_setMessage(false,"删除失败，你还没有选择删除项");
            __component_messageBar_open();
            $scope.selectPage.getData();
        }

    };

});

//加载admin_sMessage_detail控制器
$admin_sMessageController.controller("admin_sMessage_detail",function($scope,$http,$location,$routeParams){

    //这里我们构造一个跳转的函数
    /*$scope.tiaoZhuan = function(){
        $http.post("/Api_messageDetail",{message_id:$scope.id}).success(function(response){
            $scope.detailData = response;
        });
    };*/
    $scope.getData = function(){
        $scope.id = $routeParams.id;
        //根据ID实例化一个Message对象，得到当条记录
        $scope.detailData = {};
        $http.post("/Api_messageDetail",{message_id:$scope.id}).success(function(response){
            $scope.detailData = response;
        });
    };
    $scope.getData();

    //删除一条系统信息
    $scope.messageDelete = function(){
        $http.post("/Api_messageDelete",{message_id:$scope.id}).success(function(response){
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
});

//加载admin_sMessage_send控制器【这个控制器里面只要是对对用户发送系统消息】
$admin_sMessageController.controller("admin_sMessage_send",function($scope,$http,$location){

    /**************这里获取到所有的用户组**************/
    $scope.groupData = {};
    //获取所有的用户组
    $scope.getAllUserGroup = function(){
        $http.post("/Api_getAllUserGroup").success(function(response){
            $scope.groupData = response;
        });
    };
    /*******************这里实现对用户发送系统信息的功能*******************/

    //向用户组发送消息
    $scope.sendGroup = function(){
        $http.post("/Api_sendMessageToGroup",{group_id:$scope.groupid,message_data:$scope.messageData}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"发送信息成功");
                __component_messageBar_open();
                $location.path("/index");
            }
            else
            {
                __component_messageBar_setMessage(false,response.message);
                __component_messageBar_open();
                $location.path("/index");
            }
        });
    };
    $scope.getData = function(){
        $scope.getAllUserGroup();
    };
    $scope.getData();
});