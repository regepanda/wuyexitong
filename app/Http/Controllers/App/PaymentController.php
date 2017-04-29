<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/21
 * Time: 19:45
 */

namespace App\Http\Controllers\App;
use App\Http\Controllers\Controller;
use MyClass\Serve\Payment;
use Illuminate\Support\Facades\Request as SysRequest;

class PaymentController extends Controller
{
    public function getPayment()
    {
        try {
            /*
             * 返回标准结构，其中data是一个数组
                [
                    {//数据一
                        request_id:int //系统内部id
                        request_class:string,//类别码
                        request_user_intro:string,//用户描述/介绍
                        request_admin_intro:string,//客服描述
                        request_status:int,         //状态码
                        request_create_time:string,//创建时间
                        request_update_time:string,//更新时间
                        request_payment:int,      //支付单号码。如果没有则为0


                    },
                    {数据二...},
                    {数据三...}
                ]
             */
            $queryLimit = SysRequest::all();
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }

            if ($paymentData = Payment::select($queryLimit))
            {
                unset($paymentData['total']);
                $paymentData['result_code'] = 0;
                foreach ($paymentData['data'] as $key => $value)
                {
                    unset($paymentData['data'][$key]->payment_account);
                    unset($paymentData['data'][$key]->payment_user_data);
                    unset($paymentData['data'][$key]->payment_by);
                    unset($paymentData['data'][$key]->payment_to);
                    unset($paymentData['data'][$key]->payment_other_data);
                    unset($paymentData['data'][$key]->class_id);
                    unset($paymentData['data'][$key]->class_name);
                    unset($paymentData['data'][$key]->class_intro);
                    unset($paymentData['data'][$key]->status);
                }
                return response()->json($paymentData);
            }
            else
            {
                //查询失败的返回码
                $paymentData['result_code'] = -1;
                $paymentData['message'] = "查询失败";
                $paymentData['status'] = false;
                $paymentData['data'] = null;
                return response()->json($paymentData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}