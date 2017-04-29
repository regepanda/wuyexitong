<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/14
 * Time: 16:49
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function() {
    Route::get("/app_getMonitor","App\MonitorController@getMonitor");//获取消息 GET /app_getMonitor
});