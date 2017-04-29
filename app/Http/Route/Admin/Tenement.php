<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 14:14
 */
Route::group(["middleware"=>["AdminLoginCheck"]],function(){
    Route::get("/admin_tenement","Admin\TenementController@index");


//用户请求【2016-3-11】彭亮
    Route::get("/admin_sRequest","Admin\TenementController@sRequest");

    Route::get("/test_pl_angularApiResTest","Admin\TenementController@angularApiResTest");//用户请求，查询请求信息并分页、条件查找、排序
    Route::post("/test_pl_angularDetailRes","Admin\TenementController@angularDetailRes");//详情
    Route::post("/api_updateRequest","Admin\TenementController@updateRequest");//修改请求【管理员描述】
    Route::post("/api_deleteRequest","Admin\TenementController@deleteRequest");//删除请求
    Route::post("/api_setStatusReadyHandle","Admin\TenementController@setStatusReadyHandle");//修改当前请求状态为准备处理
    Route::post("/api_setStatusInHandle","Admin\TenementController@setStatusInHandle");//设定状态到处理中，只有准备处理可以用
    Route::post("/api_setStatusHaveHandle","Admin\TenementController@setStatusHaveHandle");//设定状态到处理完成，只有处理中可以用
    Route::post("/api_setStatusCancel","Admin\TenementController@setStatusCancel");//设定状态到已取消,只有已提交和准备处理可以用
    Route::get("/api_addRequest","Admin\TenementController@addRequest");//手动添加请求

//缴费【2016-3-11】彭亮
    Route::get("/admin_sTax","Admin\TaxController@sTax");

    Route::get("/test_pl_angularApiTaxTest","Admin\TaxController@angularApiTaxTest");//缴费信息，查询并分页、条件查找、排序
    Route::post("/api_taxDetail","Admin\TaxController@taxDetail"); //缴费详情
    Route::post("/api_taxDelete","Admin\TaxController@taxDelete");//删除当前缴费信息
    Route::post("/api_taxUpdate","Admin\TaxController@taxUpdate");//修改当前缴费信息
    Route::post("/api_setStatusHavePay","Admin\TaxController@setStatusHavePay");//切换当前状态为以付费
    Route::post("/api_setStatusDrawBack","Admin\TaxController@setStatusDrawBack");//切换当前状态为申请退款
    Route::post("/api_setStatusCancelPayTax","Admin\TaxController@setStatusCancelPayTax");//切换状态到取消付费
    Route::post("/api_setStatusOutDate","Admin\TaxController@setStatusOutDate");//切换状态到已过期
    Route::get("/api_addTax","Admin\TaxController@addTax");//手动添加缴费
});
