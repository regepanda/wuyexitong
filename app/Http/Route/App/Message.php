<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/10
 * Time: 10:29
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function() {
    Route::get("/app_getMessage","App\MessageController@getMessage");//获取消息 GET /app_getMessage
    Route::post("/app_setReadMessage","App\MessageController@setReadMessage");//设置消息为已读 POST /app_setReadMessage
});