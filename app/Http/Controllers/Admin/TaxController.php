<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/24
 * Time: 8:27
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Exception;
use MyClass\Base\GuiFunction;
use MyClass\Exception\PowerException;
use MyClass\Admin\PowerGroup;
use MyClass\Serve\Request;
use MyClass\Serve\Tax;
use Illuminate\Support\Facades\Request as SysRequest;
use Illuminate\Support\Facades\DB;
use MyClass\Serve\Payment;

class TaxController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(8))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $guiFunc->setModule("Tenement");
        //dump(session("other"));

    }
    public function index(GuiFunction $guiFunc)
    {
        return view("Admin.Tenement.index");

    }

    /************************缴费部分**********************************/
    public function sTax(GuiFunction $guiFunc)
    {

        $guiFunc->setSecondModule("sTax");
        return view("Admin.Tenement.sTax");
    }

    //查询缴费信息并分页、条件查找、排序
    public function angularApiTaxTest()
    {
        //如果当前系统是以物管的角色登录，那查询的用户缴费信息也应该是此物管所管理的小区的用户的缴费信息
        $queryLimit = SysRequest::all();
        if(session("admin.community_group") != null)
        {
            $queryLimit["admin_community_group"] = session("admin.community_group");
        }
        $data = Tax::select($queryLimit);
        return response()->json($data);
    }

    //缴费详情
    public function taxDetail()
    {
        $taxDetailData = SysRequest::all();
        $tax = new Tax($taxDetailData['tax_id']);
        $taxDetailData = $tax->info;
        return response()->json($taxDetailData);
    }

    //删除当前缴费信息
    public function taxDelete()
    {
        $taxDeleteData = SysRequest::all();
        $tax = new Tax($taxDeleteData['tax_id']);
        if($tax->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }

    //修改当前缴费信息
    public function taxUpdate()
    {
        //得到修改的数据，包括tax_id、tax_price
        $updateData = SysRequest::all();
        //$tax = new Tax($updateData['tax_id']);
        $payment = new Payment($updateData['tax_payment']);
        //判断一下传过来的price是否为空
        if(!empty($updateData['tax_price']))
        {
            //判断一下传过来的price是否是整数
            if(is_numeric($updateData['tax_price']))
            {
                if($payment->updatePrice($updateData['tax_price']))
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
                return response()->json(["status"=>false,"message"=>"填写的价格必须为数字"]);
            }
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"价格为空，请填写价格"]);
        }
    }

    //切换当前状态为以付费
    public function setStatusHavePay()
    {
            $setStatusHavePayData = SysRequest::all();
            $tax = new Tax($setStatusHavePayData['tax_id']);
            if($tax->setStatusHavePay())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //切换当前状态为申请退款
    public function setStatusDrawBack()
    {
            $setStatusDrawBackData = SysRequest::all();
            $tax = new Tax($setStatusDrawBackData['tax_id']);
            if($tax->setStatusDrawBack())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //切换状态到取消付费
    public function setStatusCancelPayTax()
    {
            $setStatusCancelPayData = SysRequest::all();
            $tax = new Tax($setStatusCancelPayData['tax_id']);
            if($tax->cancel())
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
    }

    //切换状态到已过期
    public function setStatusOutDate()
    {
        $setStatusOutDateData = SysRequest::all();
        $tax = new Tax($setStatusOutDateData['tax_id']);
        if($tax->setStatusOutDate())
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

    //手动添加缴费
    public function addTax()
    {
            $addTaxData = SysRequest::all();
            if ($addTaxData['tax_user'] != "undefined" && $addTaxData['tax_class'] != "undefined" && $addTaxData['tax_time'] != "undefined" && $addTaxData['tax_price'] != "undefined" && $addTaxData['tax_intro'] != "undefined")
            {
                if(DB::table("user")->where("user_id","=",$addTaxData['tax_user'])->first())
                {
                    if(Tax::addTax($addTaxData['tax_user'],$addTaxData['tax_class'],$addTaxData['tax_time'],$addTaxData['tax_price'],$addTaxData['tax_intro']) != false)
                    {
                        return response()->json(["status"=>true,"message"=>"添加成功"]);
                    }
                    else
                    {
                        return response()->json(["status"=>false,"message"=>"添加失败"]);
                    }
                }
                else
                {
                    return response()->json(["status"=>false,"message"=>"添加失败"]);
                }
            }
            else
            {
                throw new PowerException("五个表单字段都为必填！");
                //return response()->json(["status"=>false,"message"=>"添加失败"]);
            }
    }
}