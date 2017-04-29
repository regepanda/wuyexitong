<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/22
 * Time: 20:34
 */

Route::group(["middleware"=>["AccessTokenCheck"]],function() {

    Route::get("/app_getHouse", "App\HouseController@getHouse");
    Route::post("/app_addHouse", "App\HouseController@addHouse");
    Route::post("/app_updateHouse", "App\HouseController@updateHouse");
    Route::get("/app_delHouse", "App\HouseController@delHouse");

    Route::post("/app_getInputHouseData", "App\HouseController@getInputHouseData");//用户录入房屋
});