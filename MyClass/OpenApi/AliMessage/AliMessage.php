<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/15
 * Time: 19:57
 */

namespace MyClass\OpenApi\AliMessage;

use MyClass\System\CheckCode;
require_once __DIR__."/SMS/TopSdk.php";
class AliMessage
{

    private $AppKey = "23347087";
    private  $SecretKey = "848941a15b8598a12058f692d9e5d1c0";

    /*
     * 拼接参数，发送短信息,用于注册发送短信获取验证码
     * @param $user_phone 用户电话号码
     */
    public function sendMesRegister($user_phone)
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

            $c = new \TopClient();
            $c->appkey = $this->AppKey;
            $c->secretKey = $this->SecretKey;
            $c->format = "json";
            $req = new \AlibabaAliqinFcSmsNumSendRequest();
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("注册验证");
            $req->setSmsParam("{\"code\":\"".$code."\",\"product\":\"物业系统\",\"item\":\"物业系统\"}");
            $req->setRecNum($user_phone);
            $req->setSmsTemplateCode("SMS_7775561");
            $resp = $c->execute($req);
            $arr = AliMessage::objectArray($resp);
            //返回id
            $arr['codeId'] = $codeId;
            return $arr;
        }
        return false;
    }

    /*
     * 拼接参数，发送短信息,用于找回密码时发送短息获取验证码
     * @param $user_phone 用户电话号码
     */
    public function sendMesFindPassword($user_phone)
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

            $c = new \TopClient();
            $c->appkey = $this->AppKey;
            $c->secretKey = $this->SecretKey;
            $c->format = "json";
            $req = new \AlibabaAliqinFcSmsNumSendRequest();
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("变更验证");
            $req->setSmsParam("{\"code\":\"".$code."\",\"product\":\"物业系统\",\"item\":\"物业系统\"}");
            $req->setRecNum($user_phone);
            $req->setSmsTemplateCode("SMS_7775559");
            $resp = $c->execute($req);
            $arr = AliMessage::objectArray($resp);
            //返回id
            $arr['codeId'] = $codeId;
            return $arr;
        }
        return false;
    }

    /*
     * 对象 转 数组
     * @param $array
     */
    public static function objectArray($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = AliMessage::objectArray($value);
            }
        }
        return $array;
    }
}