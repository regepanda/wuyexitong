<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/21
 * Time: 11:49
 */

namespace MyClass\OpenApi;


class AliPay
{
    public static $config = [
        "partner"=>"",//合作者id
        "charset"=>"utf-8",
        "notifyUrl" => "",//回调地址
        "sellerId"=>"",//买家支付宝账号
        "expireDate"=>"30m",//订单过期时间
        "extermToken"=>"",//授权令牌


        "appId" => "",//应用号


        "notifyUrl" => "",//回调地址
        "addPaymentUrl" =>"https://api.mch.weixin.qq.com/pay/unifiedorder",//请求下单链接

    ];
    public static function addPayment()
    {

    }

    public static function alreadyPay()
    {

    }

    public static function alreadyPayDatabaseOperate()
    {

    }

    public static function generateSign(&$data)
    {
        if(!is_array($data))
        {
            return false;
        }

        asort($data);//字典序，由小到大

        $i = 0;
        $signResult = "";
        foreach($data as $key => $value)
        {
            if($i != 0)
            {
                $signResult.="&";
            }
            $signResult.=$key."=".$value;
        }

    }

}