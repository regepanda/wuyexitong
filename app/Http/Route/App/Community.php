<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/26
 * Time: 10:49
 */

Route::group(["middleware"=>["AccessTokenCheck"]],function() {

    Route::get("/app_getCommunityInfo", "App\CommunityController@getCommunityInfo");
});