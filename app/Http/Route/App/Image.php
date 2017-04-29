<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/4/7
 * Time: 17:23
 */

Route::group(["middleware"=>["AccessTokenCheck"]],function() {

   //上传用户头像 POST /app_addUserHeadImage
    Route::post("/app_addUserHeadImage", "App\ImageController@addUserHeadImage");



});