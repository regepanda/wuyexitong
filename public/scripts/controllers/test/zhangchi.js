/**
 * Created by zc on 2016/3/21.
 */
/**
 * Created by RagPanda on 2016/3/9.
 */
$a =
    $app.controller("zczc",function($scope,$location)
    {
        //zc
        $scope.getUserInfo = function()
        {
            $location.path("/zc");

        };


    });
$a.config(["$routeProvider",function($routeProvider){
    /*
    $routeProvider.when("/index",{
        templateUrl:"/views/admin/tenement/sUser/index.html",
        controller:"admin_sUser_index"
    }).when("/detail/:id",{
        templateUrl:"/views/admin/tenement/sUser/detail.html",
        controller:"admin_sUser_detail"
    }).otherwise({redirectTo:'/index'});
    */

    //zc
    $routeProvider.when("/zc",{
        templateUrl:"/views/test/zhangchi.html",
        controller:"zc"
    }).otherwise({redirectTo:'/zc'});

}]);

//zc
$a.controller("zc",function($scope, $routeParams,$http){



    /*
     Route::get("/app_getHouse", "App\HouseController@getHouse");
     Route::post("/app_addHouse", "App\HouseController@addHouse");
     Route::post("/app_updateHouse", "App\HouseController@updateHouse");
     Route::get("/app_delHouse", "App\HouseController@delHouse");
     */

    $scope.getZc = function()
    {
        //基本用户
        $scope.getUserInfoData = {};
        $http.get("/app_getUserInfo").success(function(response) {
            $scope.getUserInfoData = response;
        });

        //用户真实
        $scope.getTrueInfoData = {};
        $http.get("/app_getTrueInfo").success(function(response) {
            $scope.getTrueInfoData = response;
        });

        //房子
        $scope.getHouseData = {};
        $http.get("/app_getHouse").success(function(response) {
            $scope.getHouseData = response;
        });

        //car
        $scope.getCarData = {};
        $http.get("/app_getCar").success(function(response) {
            $scope.getCarData = response;
        });
    };
    $scope.getZc();


    //用户基本
    $scope.updateUserInfo = function()
    {
        $scope.updateUserData = {};
        $http.post("/app_updateUserInfo",{user_username:$scope.username,user_nickname:$scope.nickname,user_sex:$scope.sex,user_birthday:
        $scope.birthday,user_phone:$scope.phone}).success(function(response){
            $scope.updateUserData = response;
        });
    };




    //房子
    $scope.updateHouse = function()
    {
        $scope.updateHouseData = {};
        $http.post("/app_updateHouse",{house_id:$scope.id,house_area:$scope.house_area,house_address:$scope.house_address}).success(function(response){
            $scope.updateHouseData = response;
        });
    };

    //房子
    $scope.addHouse = function() {
        $scope.addHouseData = {};
        $http.post("/app_addHouse",{house_area: $scope.area, house_address: $scope.address}).success(function(response){
            $scope.addHouseData = response;
        });
    };


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



    $scope.delHouse = function() {
        //房子
        var $houseDelLimit={house_id:$scope.dd};
        var houseDetailUrl = $scope.buildUrlParam($houseDelLimit,"/app_delHouse");
        $scope.delHouseData = {};
        $http.get(houseDetailUrl).success(function(response){
            $scope.delHouseData = response;
        });
    };

    /*
     //关于此用户的房子
     var $houseDetaiLimit={id:$scope.id};
     var houseDetailUrl = $scope.buildUrlParam($houseDetaiLimit,"/manage_admin_api_sHouseDetail");
     $scope.detailHouseData = {};
     $http.get(houseDetailUrl).success(function(response) {
     $scope.detailHouseData = response;
     });

     */


    //car
    $scope.updateCar = function()
    {
        $scope.updateCarData = {};
        $http.post("/app_updateCar",{car_id:$scope.car_id,car_name:$scope.car_name,car_brand:$scope.car_brand,car_color:$scope.car_color,car_plate_id:$scope.car_plate_id,
        car_model:$scope.car_model}).success(function(response){
            $scope.updateCarData = response;
        });
    };

    //car
    $scope.addCar = function() {
        $scope.addCarData = {};
        $http.post("/app_addCar",{car_name:$scope.car_name,car_brand:$scope.car_brand,car_color:$scope.car_color,car_plate_id:$scope.car_plate_id,
            car_model:$scope.car_model}).success(function(response){
            $scope.addCarData = response;
        });
    };

    //car
    $scope.delCar = function() {
        $scope.delCarData = {};
        $http.get("/app_delCar").success(function(response){
            $scope.delCarData = response;
        });
    };


    $scope.addTrueInfo = function() {
        $http.post("/app_addTrueInfo",{info_name: $scope.name, info_ic_id: $scope.ic_id}).success(function(response){
            $scope.userTrueInfoData = response;
        });
    };

    $scope.delTrueInfo = function() {
        $http.get("/app_delTrueInfo").success(function(response){
            $scope.userTrueDelData = response;
        });
    };






});












