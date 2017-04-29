<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/22
 * Time: 16:50
 */


Route::group(["middleware"=>["AccessTokenCheck"]],function() {
    //客户端准备为一个payment进行支付宝支付了，客户端发送一个payment_id过来，服务器返回签名数据
    Route::post("/openApi_AliPay_addPayment",
            "OpenAPI\AliPayController@addPayment");

});

//支付宝支付成功的回调口
Route::any("/openApi_AliPay_alreadyPay","OpenAPI\AliPayController@alreadyPay");