<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/22
 * Time: 14:17
 */

namespace App\Http\Controllers\OpenAPI;


use App\Http\Controllers\Controller;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Request;
use MyClass\Serve\Payment as MyPayment;
use MyClass\System\DBLog;

class WeChatPayController extends Controller
{
    public function addPayment()
    {
        try{
            $wechat = app("wechat");
            $paymentModel = new MyPayment(Request::input("payment_id"));
            if(empty($paymentModel))
            {
                return response()->json(["status"=>false,"data"=>[],"message"=>"非法的支付单","result_code"=>-1]);
            }
            $attributes = [
                'body'             => $paymentModel->info->payment_intro,
                'detail'           => $paymentModel->info->payment_intro,
                'out_trade_no'     => $paymentModel->info->payment_id,
                'total_fee'        => $paymentModel->info->payment_price,

            ];
            $order = new Order($attributes);
            $result = $wechat->prepare($order);

            if(!$paymentModel->setPrepayId($result->prepay_id))
            {
                throw new Exception("无法插入预支付单");
            }
            DBLog::SystemLog("微信支付:完成下单",DBLog::INFO,json_encode($result));
            //返回预支付单号
            return response()->json(["status"=>true,"data"=>$result->prepay_id,"message"=>"返回微信预支付单号","result_code"=>0]);


        }catch (\Exception $e)
        {
            DBLog::SystemLog("微信支付:下单时发生程序内部错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"下单时发生程序内部错误","result_code"=>-1]);

        }


    }
    public function alreadyPay()
    {
        $app = app("wechat");
        $response = $app->payment->handleNotify(function($notify, $successful){
            try
            {
                $payment = new MyPayment($notify->out_trade_no);
                // 如果订单不存在
                if($payment->info == NULL)
                {
                    return 'Order not exist.';
                }

                // 检查订单是否已经更新过支付状态
                if ($payment->isPay())
                {
                    return true;
                }

                // 用户是否支付成功
                if ($successful)
                {
                    if($payment->setStatusAlreadyPay(2,$notify->transaction_id,json_encode($notify)))
                    {
                        DBLog::SystemLog("微信支付:支付成功回调完成",DBLog::INFO,json_encode($notify));
                        return true;
                    }
                }
                return 'error server handle error'; // 返回处理完成

            }
            catch (\Exception $e)
            {
                DBLog::SystemLog("微信支付:回调接口程序内部错误,".$e->getMessage(),DBLog::ERROR);
                return "error server inside error";
            }

        });

        return $response;
    }
}