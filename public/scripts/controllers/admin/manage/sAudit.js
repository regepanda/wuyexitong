/**
 * Created by RagPanda on 2016/3/23.
 */
$admin_sAuditController = $app.controller("admin_sAudit",function($scope,$location,SelectPageService){
    $scope.goIndex = function(){
        $location.path("/index");
    };
    $scope.goDetail = function($class,$id){
        if($class == 1)
        {
            $location.path("/houseDetail/"+$id);
        }
        if($class == 2)
        {
            $location.path("/carDetail/"+$id);
        }
        if($class == 3)
        {
            $location.path("/trueinfoDetail/"+$id);
        }

    };
    $scope.selectPage = SelectPageService;
    $scope.auditClass={
        1:"房屋",
        2:"车辆",
        3:"真实信息",
        4:"车位"
    };
    //$scope.selectPage.changeLimit("class",1);
});
//加载配置
$admin_sAuditController.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/index",{
        templateUrl:'/views/admin/manage/sAudit/index.html',
        controller:'admin_sAudit_index'
    }).when("/houseDetail/:id",{
        templateUrl:'/views/admin/manage/sAudit/houseDetail.html',
        controller:'admin_sAudit_houseDetail'
    }).when("/trueinfoDetail/:id",{
        templateUrl:'/views/admin/manage/sAudit/trueinfoDetail.html',
        controller:'admin_sAudit_trueinfoDetail'
    }).when("/carDetail/:id",{
        templateUrl:'/views/admin/manage/sAudit/carDetail.html',
        controller:'admin_sAudit_carDetail'
    }).otherwise({
        redirectTo:'/index'
    });
}]);
$admin_sAuditController.controller("admin_sAudit_index",function($scope,$http){
    $scope.selectPage.getDataUrl="/admin_api_sAudit";
    $scope.selectPage.limit.class = $("#nowClass").val();
    $scope.selectPage.getData();

    //删除车位
    $scope.deletePosition = function($position_id){
        $http.post("/api_deletePosition",{"position_id":$position_id}).success(function(response){
            if(response.status == true)
            {
                __component_messageBar_setMessage(true,"删除成功");
                __component_messageBar_open();
                $scope.selectPage.getData();
            }
            else
            {
                __component_messageBar_setMessage(false,"删除失败");
                __component_messageBar_open();
            }
        });
    };

    //审核车位
    $scope.updateCarPositionCheck = function($position_id){
        $http.post("/api_updateCarPositionCheck",{"position_id":$position_id}).success(function(response){
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
$admin_sAuditController.controller("admin_sAudit_houseDetail",function($scope,$http,$routeParams,$location){


    $scope.id = $routeParams.id;
    $scope.limit={id:$scope.id};
    $scope.isCanTax = false;
    $scope.houseUpdateData={};
    $scope.getData = function()
    {
        var $url = "/manage_admin_api_sHouseDetail";
        $url = $scope.buildUrlParam($scope.limit,$url);
        $http.get($url).success(function(response){
            $scope.houseData = response.data;
            if(response.status == true)
            {
                //小区详情
                var limitCommunity={community_id:$scope.houseData[0].house_community};
                var url = $scope.buildUrlParam(limitCommunity,"/admin_getCommunityById");
                $scope.detailCommunityData = {};
                $http.get(url).success(function(response) {
                    $scope.detailCommunityData = response.data;
                });
                //用户详情
                var limitUser={user_id:$scope.houseData[0].house_user};
                var url = $scope.buildUrlParam(limitUser,"/admin_getUserById");
                $scope.detailUserData = {};
                $http.get(url).success(function(response) {
                    $scope.detailUserData = response.data;
                });
            }
        });




    };
    $scope.updateData = function()
    {
        $scope.otherData = {};
        $i = 1;
        while(true)
        {
            $scope.otherData[$("#name_"+$i).val()] = $("#content_"+$i).val();
            if($("#name_"+$i).length == 0){break;}
            $i++;
        }

        $scope.houseUpdateData["house_check"] = true;
        $scope.houseUpdateData["house_id"] = $scope.id;
        $scope.houseUpdateData["data_tax"] = $scope.houseUpdateData.house_tax;
        $scope.houseUpdateData["data_detail"] = $scope.otherData;
        $http.post("/admin_api_setHouseChecked",$scope.houseUpdateData).success(
            function(response){
                if(response.status == true)
                {
                    __component_messageBar_setMessage(true,"审核成功");
                    __component_messageBar_open();
                    $location.path("/index");
                }
                else
                {
                    __component_messageBar_setMessage(false,"审核失败"+response.message);
                    __component_messageBar_open();
                    $scope.getData();
                }

        });
    };


    //zc 修改房子
    $scope.updateHouse = function()
    {
        $scope.updateHouseInfo = {
            area:$scope.houseData[0].house_area,
            address:$scope.houseData[0].house_address,
            id:$scope.houseData[0].house_id,
            community:$scope.houseData[0].house_community,
            cantax_timel:$scope.houseData[0].house_cantax_time
        };

        $http.post("/admin_updateHouse",$scope.updateHouseInfo).success(
            function(response){
                if(response.status == true)
                {
                    __component_messageBar_setMessage(true,"修改房子信息成功");
                    __component_messageBar_open();
                    $scope.getData();
                }
                else
                {
                    __component_messageBar_setMessage(false,"修改汽车房子失败"+response.message);
                    __component_messageBar_open();
                    $scope.getData();
                }
            });
    };



    $scope.toggleIsCanTax = function(){$scope.isCanTax=!$scope.isCanTax;};
    $scope.getData();
});
$admin_sAuditController.controller("admin_sAudit_carDetail",function($scope,$http,$routeParams,$location){
    $scope.id = $routeParams.id;
    $scope.limit={id:$scope.id};

    $scope.carUpdateData={};
    $scope.getData = function()
    {
        var $url = "/manage_admin_api_sCarDetail";
        $url = $scope.buildUrlParam($scope.limit,$url);
        $http.get($url).success(function(response)
        {
            $scope.carData = response.data;
        });
    };
    $scope.updateData = function()
    {
        $scope.carUpdateData["car_id"] = $scope.id;
        $http.post("/admin_api_setCarChecked",$scope.carUpdateData).success(
            function(response){
                if(response.status == true)
                {
                    __component_messageBar_setMessage(true,"审核成功");
                    __component_messageBar_open();
                    $location.path("/index");
                }
                else
                {
                    __component_messageBar_setMessage(false,"审核失败"+response.message);
                    __component_messageBar_open();
                    $scope.getData();
                }

            });
    };

    //zc 修改汽车
    $scope.updateCar = function()
    {
        $scope.updateCarInfo = {car_id:$scope.carData[0].car_id,car_name:$scope.carData[0].car_name,
            car_brand:$scope.carData[0].car_brand,car_color:$scope.carData[0].car_color,car_model:$scope.carData[0].car_model
        ,car_plate_id:$scope.carData[0].car_plate_id};

        $http.post("/app_updateCar",$scope.updateCarInfo).success(
            function(response){
                if(response.status == true)
                {
                    __component_messageBar_setMessage(true,"修改汽车信息成功");
                    __component_messageBar_open();

                    $scope.getData();
                }
                else
                {
                    __component_messageBar_setMessage(false,"修改汽车信息失败"+response.message);
                    __component_messageBar_open();
                    $scope.getData();
                }
            });
    };

    $scope.toggleIsCanTax = function(){$scope.isCanTax=!$scope.isCanTax;};
    $scope.getData();
});

$admin_sAuditController.controller("admin_sAudit_trueinfoDetail",function($scope,$http,$routeParams,$location){
    $scope.id = $routeParams.id;
    $scope.limit={id:$scope.id};

    $scope.trueinfoUpdateData={};
    $scope.getData = function()
    {
        var $url = "/manage_admin_api_sTrueinfoDetail";
        $url = $scope.buildUrlParam($scope.limit,$url);
        $http.get($url).success(function(response)
        {
            $scope.trueinfoData = response.data;

        });
    };
    $scope.updateData = function()
    {

        $scope.trueinfoUpdateData["info_id"] = $scope.id;
        $http.post("/admin_api_setTrueinfoChecked",$scope.trueinfoUpdateData).success(
            function(response){
                if(response.status == true)
                {
                    __component_messageBar_setMessage(true,"审核成功");
                    __component_messageBar_open();
                    $location.path("/index");
                }
                else
                {
                    __component_messageBar_setMessage(false,"审核失败"+response.message);
                    __component_messageBar_open();
                    $scope.getData();
                }

            });
    };

    $scope.getData();
});


