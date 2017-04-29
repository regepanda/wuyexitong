<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/22
 * Time: 20:34
 */

Route::group(["middleware"=>["AccessTokenCheck"]],function() {

    Route::get("/app_getCar", "App\CarController@getCar");
    Route::post("/app_addCar", "App\CarController@addCar");
    Route::post("/app_updateCar", "App\CarController@updateCar");
    Route::get("/app_delCar", "App\CarController@delCar");



});