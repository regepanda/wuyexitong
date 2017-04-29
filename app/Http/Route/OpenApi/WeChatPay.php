<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/23
 * Time: 9:16
 */
//客户端准备为一个payment进行微信支付，发送一个预支付id
Route::post("/openApi_WeChatPay_addPayment",
    ["middleware"=>"AccessTokenCheck",
    "uses"=>"OpenAPI\WeChatPayController@addPayment"]);
//已支付后的回调接口
Route::any("/openApi_WeChatPay_alreadyPay","OpenAPI\WeChatPayController@alreadyPay");