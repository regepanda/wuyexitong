<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/26
 * Time: 15:45
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function(){
    Route::get("/kan","App\ChildController@kan");
    Route::get("/app_getChild","App\ChildController@getChild");//查看自己孩子的信息（4点半学校): GET /app_getChild
    Route::post("/app_addChild","App\ChildController@addChild");//添加孩子的信息 POST /app_addChild
    Route::post("/app_updateChild","App\ChildController@updateChild");//更新孩子的信息 POST/app_updateChild
    Route::get("/app_delChild","App\ChildController@delChild");//删除孩纸的信息 GET/app_delChild
});