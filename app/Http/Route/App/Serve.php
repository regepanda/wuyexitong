<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/4/7
 * Time: 14:16
 */


Route::get("/app_getServer", "App\ServeController@getServer");
Route::group(["middleware"=>["AccessTokenCheck"]],function() {


});
