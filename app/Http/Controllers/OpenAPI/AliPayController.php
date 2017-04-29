<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/22
 * Time: 14:16
 */

namespace App\Http\Controllers\OpenAPI;



use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use MyClass\Serve\Payment;
use MyClass\System\DBLog;

class AliPayController extends Controller
{

    //客户端准备为一个payment进行支付宝支付了，发送一个payment_id过来，服务器返回签名串
    public function addPayment()
    {
        try{

            $paymentId = Request::input("payment_id");


            $paymentModel = new Payment($paymentId);

            if($paymentModel->info==NULL||1 != $paymentModel->info->payment_status)
            {
                return response()->json(["status"=>false,"data"=>[],"message"=>"非法的支付单","result_code"=>-1]);
            }

            // 创建支付单。
            $alipay = app('alipay.mobile');
            $alipay->setOutTradeNo($paymentModel->info->payment_id);
            $alipay->setTotalFee($paymentModel->info->payment_price);
            $alipay->setSubject("沁生活支付单:".$paymentModel->info->payment_id);
            $alipay->setBody('沁生活支付单');

            // 返回签名后的支付参数给支付宝移动端的SDK。
            $returnData = $alipay->getPayPara();

            return  response()->json(["status"=>true,"data"=>$returnData,"message"=>"生成签名串，可以开始请求支付","result_code"=>0]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("支付宝:请求下单程序内部错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getLine().$e->getMessage(),"result_code"=>-1]);
        }

    }


    /**
     *支付宝支付成功的回调口
     * @return string
     */
    public function alreadyPay()
    {
        $originData = json_encode(Request::all());
        // 验证请求。
        if (! app('alipay.mobile')->verify()) {

            DBLog::SystemLog("支付宝:回调有非法的签名参数",DBLog::ERROR,$originData);
            return 'fail';
        }

        // 判断通知类型。
        if(Input::get('trade_status') == 'TRADE_SUCCESS'||Input::get('trade_status') == 'TRADE_FINISHED')
        {
            try
            {

                $paymentModel = Payment(Input::get('out_trade_no'));
                //是否有内部订单
                if(empty($paymentModel->info))
                {
                    DBLog::SystemLog("支付宝:回调找不到相应的内部订单号",DBLog::ERROR,$originData);
                    return 'fail';
                }
                if($paymentModel->isPay())
                {
                    return 'success';
                }

                //是否已支付转换成功
                if($paymentModel->setStatusAlreadyPay(2,Request::input("trade_no"),$originData))
                {
                    DBLog::SystemLog("支付宝:回调完成了支付",DBLog::INFO,$originData);
                    return 'success';
                }
                else
                {
                    DBLog::SystemLog("支付宝:回调无法成功转换payment状态",DBLog::ERROR,$originData);
                    return 'fail';
                };

            }
            catch (\Exception $e)
            {
                DBLog::SystemLog("支付宝:回调程序内部错误,".$e->getMessage(),DBLog::ERROR,$originData);
                return 'fail';

            }
        }

    }
}