<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/21
 * Time: 11:49
 */

namespace MyClass\OpenApi;


use MyClass\Serve\Payment;
use MyClass\System\DBLog;

class WechatPay
{
    public static $config = [

        "appId" => "",//应用号
        "mchId" => "", //商户号
        "key" => "", //key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
        "addPaymentUrl" =>"https://api.mch.weixin.qq.com/pay/unifiedorder",//请求下单链接
        "notifyUrl" => "",//回调地址
    ];

    /*
     * 请求微信端，添加支付单
     * @param $fee        费用
     * @param $paymentId  内部支付单id
     * @param $ip         客户APP IP
     * @return array
     * []
     * |-status
     * |-message
     * 如果成功
     * |-prepay_id
     * |-trade_type
     * 只要通讯成功就会保存的数据
     * |-request_data
     * |-receive_data
     */
    public static function addPayment($fee,$paymentId,$ip)
    {
        //拼接数据
        $template = "<xml>
                       <appid>%s</appid>
                       <attach>%s</attach>
                       <body>%s</body>
                       <mch_id>%s</mch_id>
                       <nonce_str>%s</nonce_str>
                       <notify_url>%s</notify_url>
                       <out_trade_no>%s</out_trade_no>
                       <spbill_create_ip>%s</spbill_create_ip>
                       <total_fee>%s</total_fee>
                       <trade_type>%s</trade_type>
                       <sign>%s</sign>
                    </xml>";
        $arg["out_trade_no"] = $paymentId;//内部的订单号
        $arg["spbill_create_ip"] = $ip;     //终端App ip号
        $arg["total_fee"] = $fee;           //费用
        $arg["appid"] = WechatPay::$config["appId"];//应用id
        $arg["mch_id"] = WechatPay::$config["mchId"];//商户号
        $arg["notify_url"] = WechatPay::$config["notifyUrl"];//回调地址
        $arg["trade_type"] = "APP";         //类型
        $arg["nonce_str"]= md5(rand(0,999));//随机的一个字符串
        $arg["body"] = "";                  //商品描述
        $arg["attach"] = "";                //附加数据,用于商户携带自定义数据
        $sign = WechatPay::generateSign($arg);
        $arg["sign"] = $sign;
        $sendData = sprintf($template,$arg["appid"],$arg["attach"],$arg["body"],$arg["mch_id"],
            $arg["nonce_str"],$arg["notify_url"], $arg["out_trade_no"],$arg["spbill_create_ip"],
            $arg["total_fee"],$arg["trade_type"],$arg["sign"]);

        //curl请求
        $curlObject = curl_init(WechatPay::$config["addPaymentUrl"]);
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObject, CURLOPT_POST, 1);
        curl_setopt($curlObject, CURLOPT_POSTFIELDS,$sendData);
        $resultString= curl_exec($curlObject);
        curl_close($curlObject);
        $result = simplexml_load_string($resultString);

        //先看return_code
        if(!( isset($result->return_code)&& $result->return_code == "SUCCESS"))
        {
            $returnData["status"] = false;
            $returnData["message"] = $result->return_msg;
            return $returnData;
        }

        //通信成功，查看是否下单成功
        if(isset($result->result_code)&& $result->result_code == "SUCCESS")
        {
            $returnData["status"] = true;
            $returnData["message"] = "成功下单";


            $returnData["prepay_id"] = $result->prepay_id;
            $returnData["trade_type"] = $result->trade_type;


            //将请求原型转化为json记录下来，方便开发者存入数据库，
            $returnData["request_data"] = json_decode($arg);
            $returnData["receive_data"] = json_decode($result);
            return $returnData;

        }
        else
        {
            $returnData["status"] = false;
            $returnData["message"] = "失败，错误码：".$result->err_code." | 描述：".$result->err_code_des;

            //将请求原型转化为json记录下来，方便开发者存入数据库，
            $returnData["request_data"] = json_decode($arg);
            $returnData["receive_data"] = json_decode($result);
            return $returnData;
        }

    }



    /*
     * 微信服务器支付成功回调函数
     * @return mixed
     */
    public static function alreadyPay()
    {
        $receiveString = file_get_contents("php://input");
        $receiveObject = simplexml_load_string($receiveString);
        if(! (isset($receiveObject->return_code) && $receiveObject->return_code == "SUCCESS") )
        {
            $returnData["status"] = false;
            $returnData["message"] = $receiveObject->return_msg;
            return $returnData;
        }

        //通信成功，查看结果
        if(isset($receiveObject->result_code)&& $receiveObject->result_code == "SUCCESS"&&isset($receiveObject->out_trade_no))
        {
            $returnData["status"] = true;
            $returnData["message"] = "成功下单";
            WechatPay::alreadyPayDatabaseOperate($receiveObject);
            return $returnData;

        }
        else
        {
            $returnData["status"] = false;
            $returnData["message"] = "失败，错误码：".$receiveObject->err_code." | 描述：".$receiveObject->err_code_des;

            return $returnData;
        }


    }

    /*
     * 微信服务端回调后本应用服务器数据库相关工作在这里进行，函数会输出相关格式数据并结束本次请求
     * @param $receiveObject
     */
    public static function alreadyPayDatabaseOperate($receiveObject)
    {

        $paymentModel = new Payment($receiveObject->out_trade_no);
        DBLog::accountLog("成功支付，使用微信钱包",$paymentModel->payment_account,1,json_encode($receiveObject));
        if($paymentModel->setStatusAlreadyPay(2,json_encode($receiveObject),null))
        {
            echo "<xml>
                      <return_code><![CDATA[SUCCESS]]></return_code>
                      <return_msg><![CDATA[OK]]></return_msg>
                    </xml>";
        }
        else
        {
            echo "error";
        }
        exit(0);
    }

    /*
     * 用于生成微信签名，注意传入的data是引用会改变内部顺序到字典序
     * @param $data
     * @return bool|string
     */
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
        $signResult.="&key=".WechatPay::$config["key"];
        $signResult = md5($signResult);
        return $signResult;
    }

    /*
     * 验证签名是否有效
     * @param $sign
     * @param $data
     * @return bool
     */
    public static function checkSign($sign,$data)
    {
        $data = json_decode(json_encode($data),true);//转换维数组
        return $sign == WechatPay::generateSign($data);
    }


}