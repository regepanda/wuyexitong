<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 15:21
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Base\GuiFunction;
use MyClass\Serve\Payment;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class PaymentController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(4))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $guiFunc->setModule("Manage");
    }

    //展示支付单页面
    public function sPayment(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sPayment");
        return view("Admin.Manage.sPayment");
    }

    //查询支付单信息并分页，条件查找、排序
    public function sAllPayment()
    {
        $data = Payment::select(SysRequest::all());
        return response()->json($data);
    }

    //支付单详情
    public function paymentDetail()
    {
        $paymentDetailData = SysRequest::all();
        $payment = new Payment($paymentDetailData['payment_id']);
        return response()->json($payment->info);
    }

    //修改支付单【价格】
    public function paymentUpdate()
    {
            //接收修改数据
            $paymentUpdateData = SysRequest::all();
            $payment = new Payment($paymentUpdateData['payment_id']);
            //状态为1【未支付】时才可以修改价格
            if($paymentUpdateData['payment_status'] == 1)
            {
                //判断价格是否为空
                if(!empty($paymentUpdateData['payment_price']))
                {
                    //判断是否为数字
                    if(is_numeric($paymentUpdateData['payment_price']))
                    {
                        //开始修改
                        if($payment->updatePrice($paymentUpdateData['payment_price']))
                        {
                            return response()->json(["status"=>true,"message"=>"修改成功"]);
                        }
                        else
                        {
                            return response()->json(["status"=>false,"message"=>"修改失败"]);
                        }
                    }
                    else
                    {
                        return response()->json(["status"=>false,"message"=>"价格必须为数字"]);
                    }
                }
                else
                {
                    return response()->json(["status"=>false,"message"=>"价格不能为空"]);
                }
            }
            else
            {
                return response()->json(["status"=>3,"message"=>"支付状态只有在未支付时才可以修改价格"]);
            }
    }

    //删除支付单
    public function paymentDelete()
    {
        $paymentDeleteData = SysRequest::all();
        $payment = new Payment($paymentDeleteData['payment_id']);
        if($payment->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }

    //设定状态已经支付
    public function setStatusAlreadyPay()
    {
            $setStatusAlreadyPayData = SysRequest::all();
            $payment = new Payment($setStatusAlreadyPayData['payment_id']);
            if($payment->setStatusAlreadyPay() != false)
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //设定状态申请退款
    public function setStatusAskReturnPay()
    {
            $setStatusAskReturnPayData = SysRequest::all();
            $payment = new Payment($setStatusAskReturnPayData['payment_id']);
            if($payment->setStatusAskReturnPay())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //设定状态退款中
    public function setStatusInReturnPay()
    {
            $setStatusInReturnPayData = SysRequest::all();
            $payment = new Payment($setStatusInReturnPayData['payment_id']);
            if($payment->setStatusInReturnPay())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //设定状态退款完成
    public function setStatusAlreadyReturnPay()
    {
            $setStatusAlreadyReturnPayData = SysRequest::all();
            $payment = new Payment($setStatusAlreadyReturnPayData['payment_id']);
            if($payment->setStatusAlreadyReturnPay())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //设定状态取消支付
    public function setStatusCancelPay()
    {
        $setStatusCancelPayData = SysRequest::all();

        $payment = new Payment($setStatusCancelPayData['payment_id']);
        if($payment->setStatusCancelPay())
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

}