<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 20:35
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function() {
    Route::get("/app_getCarPosition","App\CarPositionController@getCarPosition");//获取车位 GET /app_getCarPosition
    Route::post("/app_addCarPosition","App\CarPositionController@addCarPosition");//申请车位 POST /app_addCarPosition
    Route::get("/app_getPosition","App\CarPositionController@getPosition");//得到自己的车位列表 GET /app_getPosition 【car_position表】 2016/4/28
    Route::get("/app_delCarPosition","App\CarPositionController@delCarPosition");//删除用户自己的车位   GET /app_delCarPosition 2016/4/28
});