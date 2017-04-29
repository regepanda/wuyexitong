<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/14
 * Time: 19:57
 */
Route::get("/test_wjt_requestStatus","Test\WangjuntaiTest@requestStatus");
Route::get("/test_wjt_taxStatus","Test\WangjuntaiTest@taxStatus");
Route::get("/test_wjt_insertData","Test\WangjuntaiTest@insertData");

Route::get("/test_wjt_PaymentReflectTaxAndRequest","Test\WangjuntaiTest@paymentReflectTaxAndRequest");

Route::get("/test_wjt_paymentCancelReflect","Test\WangjuntaiTest@paymentCancelReflect");

Route::get("/test_wjt_selectAuditData","Test\WangjuntaiTest@selectAuditData");

Route::get("/test_wjt_userLoginAcsToken","Test\WangjuntaiTest@userLoginAcsToken");


Route::get("/test_wjt_testImageUpload","Test\WangjuntaiTest@testImageUpload");

Route::get("/test_pl_wuye","Test\WangjuntaiTest@addWuye");//物业公司一条线测试

Route::get("/test_wjt_checkCodeTest","Test\WangjuntaiTest@checkCodeTest");