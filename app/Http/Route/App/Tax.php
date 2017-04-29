<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/20
 * Time: 20:11
 */
Route::group(["middleware"=>["AccessTokenCheck"]],function(){

    Route::get("/test_tax","App\TaxController@tax");//测试用
    Route::get("/app_getTax","App\TaxController@getTax");//获取缴费单 GET /app_getTax
    Route::post("/app_addTax","App\TaxController@addTax");//添加缴费单
});