<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/14
 * Time: 19:57
 */
/*
Route::group(["middleware"=>["AccessTokenCheck"]],function() {
Route::get("/sImage", "Test\ZhangchiController@sImage");
//上传图片
Route::any("/putImage", "Test\ZhangchiController@putImage");

Route::get("/zhangchi","Test\ZhangchiController@testGetUserInfo");

Route::get("/test_add_session","Test\ZhangchiController@addTestSession");


   // Route::get("/test_app_image","Test\ZhangchiController@appImage");

    Route::post("/test_addImage_app","App\ImageController@addUserHeadImage");


    });
*/
Route::get("/sImage", "Test\ZhangchiController@sImage");

Route::post("/test_addImage_app","App\ImageController@addUserHeadImage");