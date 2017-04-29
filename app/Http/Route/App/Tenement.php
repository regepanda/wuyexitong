<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/20
 * Time: 20:10
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function(){

    //Route::get("/test_request","App\RequestController@request");//得到页面，测试
    Route::get("/app_getRequest","App\RequestController@getRequest");//获取请求:GET /app_getRequest
    Route::post("/app_addRequest","App\RequestController@addRequest");//提交请求 POST /app_addRequest
    Route::post("/app_updateRequestUserIntro","App\RequestController@updateRequestUserIntro");//更新请求的用户描述: POST /app_updateRequestUserIntro
    Route::post("/app_cancelRequest","App\RequestController@cancelRequest");//取消请求: POST /app_cancelRequest
});