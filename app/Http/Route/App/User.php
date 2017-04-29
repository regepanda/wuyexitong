<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/20
 * Time: 20:10
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function() {

    Route::get("/app_getUserInfo", "App\UserController@getUserInfo");  //获取用户的基本信息
    Route::post("/app_updateUserInfo", "App\UserController@updateUserInfo");
    Route::get("/app_getTrueInfo", "App\UserController@getTrueInfo");
    Route::post("/app_addTrueInfo", "App\UserController@addTrueInfo");
    Route::get("/app_delTrueInfo", "App\UserController@delTrueInfo");

});
Route::post("/app_createUser", "App\UserController@createUser");
Route::get("/app_resetPassword", "App\UserController@resetPassword");//重置密码