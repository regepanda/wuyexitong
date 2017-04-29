$admin_testTaxController = $app.controller("test_payment",function($scope,$http,$location){
    $scope.paymentData = {};
    $scope.getPayment = function(){
        $data = {child_id:4,child_name:"pl",child_school:"pk","child_monitor":6};
        $http.post("/app_updateChild",$data).success(function(response){
            $scope.paymentData = response;
        });
    };

    /*$scope.getPayment = function(){
        $data = {start:0,num:10};
        $http.post("/app_getPayment",$data).success(function(response){
            $scope.paymentData = response;
        });
    };

    $scope.getRequest = function()
    {
        $data = {start:0,num:10};
        $http.post("/app_getRequest",$data).success(function(response){
            $scope.requestData = response;
        });
    };

    $scope.addRequest = function () {
        $data = {request_class:4,request_user_intro:"hello",request_status:6};
        $http.post("/app_addRequest",$data).success(function(response){
            $scope.requestData = response;
        });
    };

    $scope.updateRequestUserIntro = function(){
        $data = {request_id:26,request_user_intro:"跟新了"};
        $http.post("/app_updateRequestUserIntro",$data).success(function(response){
            $scope.requestData = response;
        });
    };

    $scope.cancelRequest = function(){
        $data = {request_id:26};
        $http.post("/app_cancelRequest",$data).success(function(response){
            $scope.requestData = response;
        });
    };

    //缴费部分测试
    $scope.getTax = function(){
        $data = {start:0,num:10};
        $http.post("/app_getTax",$data).success(function(response){
            $scope.taxData = response;
        });
    };*/
});