<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/20
 * Time: 20:11
 */

Route::group(["middleware"=>["AccessTokenCheck"]],function(){

    Route::get("/test_payment","App\PaymentController@payment");
    Route::get("/app_getPayment","App\PaymentController@getPayment");//2016/4/26发现并修改

});