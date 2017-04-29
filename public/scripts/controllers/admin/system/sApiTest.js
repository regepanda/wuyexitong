/**
 * Created by RagPanda on 2016/3/24.
 */
$admin_sApiTestController = $app.controller("admin_sApiTest",function($scope,$http,$location,SelectPageService){
    try
    {
        $scope.inputData = "";
        $scope.url = "";
        $scope.returnData="";
        $scope.postMethod =function(){
            $scope.dstUrl = $scope.url;
            $http.post($scope.url,$.parseJSON($scope.inputData)).success(function(response){
                $scope.returnData = response;
            });

        };
        $scope.getMethod = function()
        {
            $scope.dstUrl=$scope.url+$scope.inputData;
            $http.get($scope.dstUrl).success(function(response){
                $scope.returnData = response;
            });
        }
    }
    catch($e)
    {
        for(var $v in $e)
        {
            __component_messageBar_setMessage(false,$v.message);
            __component_messageBar_open();
        }

    }

});
