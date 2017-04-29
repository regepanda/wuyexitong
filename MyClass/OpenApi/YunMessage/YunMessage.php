<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/20
 * Time: 15:22
 */
namespace MyClass\OpenApi\YunMessage;

use MyClass\System\CheckCode;
require_once __DIR__."/yunpian/YunpianAutoload.php";

class YunMessage
{
    public function sendMessage($user_phone)
    {
        $code = "";
        for($i=0;$i<6;$i++)
        {
            $code.= rand(1,9);
        }
        //这里验证一下是否可以发送，免得拉们紧到发
        if(CheckCode::canSend($user_phone))
        {
            //这里随即把生成的验证码存入数据库中
            $codeId = CheckCode::saveCode($code,$user_phone);
            //dump($codeId);

            // 发送单条短信
            $smsOperator = new \SmsOperator();
            $data['mobile'] = $user_phone;
            $data['text'] = "【沁生活物业】您的验证码是：#$code#，过期时间为10分钟。";
            //$data['text'] = '【蜂巢生活】尊敬的顾客：您好，您于#tdate#提交的订单“汶川车厘子”，预计将于#fdate#发货，请注意查收。在此期间请保持电话畅通，以免给您和您的家人带来不便。祝您购物愉快！';
            $result = $smsOperator->single_send($data);
            //转化为数组
            $result = (array)$result;
            //返回id
            $result['codeId'] = $codeId;
            return $result;
        }
        return false;
    }
}