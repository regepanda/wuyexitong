<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 14:15
 */
Route::group(["middleware"=>["AdminLoginCheck"]],function(){
    //消息【message】
    Route::get("/admin_system","Admin\BaseController@system");
    Route::get("/admin_sMessage","Admin\SystemController@sMessage");//显示系统信息页面，加载angularJs
    Route::get("/Api_allMessage","Admin\SystemController@allMessage");//查询系统信息并分页、排序、条件查找
    Route::post("/Api_messageDetail","Admin\SystemController@messageDetail");//系统信息详情
    Route::get("/Api_messageDelete/{message_id}","Admin\SystemController@messageDelete");//删除一条系统信息
    Route::post("/Api_messageDelete","Admin\SystemController@messageDelete");//删除一条系统信息
    Route::post("/Api_getAllUserGroup","Admin\SystemController@getAllUserGroup");//查询所有的用户组
    Route::post("/Api_sendMessageToGroup","Admin\SystemController@sendMessageToGroup");//向用户组发送系统消息


    //记录
    Route::get("/admin_sLog","Admin\LogController@sLog"); //显示页面
    Route::get("/admin_sAllLog","Admin\LogController@sAllLog"); //查询所有的记录信息并分页、排序、条件查找【等级】
    Route::get("/admin_sLogDetail","Admin\LogController@sLogDetail");//记录详情

    Route::get("/admin_sApiTest","Admin\ApiTestController@sApiTest");

    //公告牌
    Route::get("/admin_sBillboard","Admin\BillboardController@sBillboard"); //显示页面
    Route::get("/Api_allBillboard","Admin\BillboardController@allBillboard");//查询公告信息并分页、排序、条件查找
    Route::get("/Api_updateBillboard","Admin\BillboardController@updateBillboard");//修改公告信息
    Route::get("/Api_deleteBillboard","Admin\BillboardController@deleteBillboard");//删除公告
    Route::get("/Api_addBillboard","Admin\BillboardController@addBillboard");//添加公告牌信息

    //首页图片模块
    Route::get("/admin_sIndexImage","Admin\IndexImageController@sIndexImage"); //显示页面
    Route::post("/Api_addIndexImage","Admin\IndexImageController@addIndexImage");//添加首页图片信息
    Route::get("/Api_addImageToIndex/{image_id}","Admin\IndexImageController@addImageToIndex");//用于图片库添加图片到首页图片中用的接口
    Route::get("/Api_deleteIndexImage/{image_id}","Admin\IndexImageController@deleteIndexImage");//删除首页图片信息


    //系统更新版本
    Route::get("/admin_sVersion","Admin\SystemUpdateController@sVersion");
    Route::post("/admin_aVersion","Admin\SystemUpdateController@aVersion");
    Route::get("/admin_dVersion/{id}","Admin\SystemUpdateController@dVersion");
});
