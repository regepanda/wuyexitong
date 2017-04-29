<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 14:23
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


class TenementController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(7))
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

    public function sRequest(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sRequest");
        return view("Admin.Tenement.sRequest");
    }

    //用户请求，查询请求信息并分页、条件查找、排序
    public function angularApiResTest()
    {
        $queryLimit = SysRequest::all();
        //如果当前系统是以物管的角色登录，那查询的用户请求也应该是此物管所管理的小区的用户的请求
        if(session("admin.community_group") != null)
        {
            $queryLimit["admin_community_group"] = session("admin.community_group");
        }
        $data = Request::select($queryLimit);
        return response()->json($data);
    }

    //详情
    public function angularDetailRes()
    {
        $angularDetailResData = SysRequest::all();
        $request = new Request($angularDetailResData['request_id']);
        $detailData = $request->info;
        $detailData->request_user_intro = \GuzzleHttp\json_decode($detailData->request_user_intro);
        $detailData->request_admin_intro = \GuzzleHttp\json_decode($detailData->request_admin_intro);
        $detailData->community_group = session("admin.community_group");
        return response()->json($detailData);
    }

    //修改请求【管理员描述】
    public function updateRequest()
    {
        $requestUpdateData = SysRequest::all();
        $request = new Request($requestUpdateData['request_id']);
        if(!empty($requestUpdateData['request_admin_intro']))
        {
            if($request->updateAdminIntro($requestUpdateData['request_admin_intro']))
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
            throw new PowerException("字段为必填");
        }
    }

    //删除请求
    public function deleteRequest()
    {
            $deleteRequestData = SysRequest::all();
            $request = new Request($deleteRequestData['request_id']);
            if($request->delete())
            {
                return response()->json(["status"=>true,"message"=>"删除成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"删除失败"]);
            }
    }

     //修改当前请求状态为准备处理
    public function setStatusReadyHandle()
    {
        $setStatusReadyHandleData = SysRequest::all();

        $request = new Request($setStatusReadyHandleData['request_id']);
        if(!empty($setStatusReadyHandleData['request_admin_intro']) && !empty($setStatusReadyHandleData['request_price']))
        {
            if($request->setStatusReadyHandle($setStatusReadyHandleData['request_admin_intro'],$setStatusReadyHandleData['request_price']))
            {
                return response()->json(["status"=>true,"message"=>"修改状态成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改状态失败"]);
            }
        }
        else
        {
            throw new PowerException("request_admin_intro和request_price字段为必填");
        }
    }

    //设定状态到处理中，只有准备处理可以用
    public function setStatusInHandle()
    {
            $setStatusInHandleData = SysRequest::all();
            $request = new Request($setStatusInHandleData['request_id']);
            if($data = $request->setStatusInHandle())
            {
                return response()->json(["status"=>true,"message"=>$data]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改状态成功"]);
            }
    }

    //设定状态到处理完成，只有处理中可以用
    public function setStatusHaveHandle()
    {
            $setStatusHaveHandleData = SysRequest::all();
            $request = new Request($setStatusHaveHandleData['request_id']);
            if($request->setStatusHaveHandle())
            {
                return response()->json(["status"=>true,"message"=>"修改状态成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改状态失败"]);
            }
    }

    ////设定状态到已取消,只有已提交和准备处理可以用
    public function setStatusCancel()
    {
            $setStatusCancelData = SysRequest::all();
            $request = new Request($setStatusCancelData['request_id']);
            if($request->cancel())
            {
                return response()->json(["status"=>true,"message"=>"修改状态成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改状态失败"]);
            }
    }

    //手动添加请求
    public function addRequest()
    {
        $addRequestData = SysRequest::all();
        //判断字段是否为空
        if($addRequestData['request_user'] != "undefined" && $addRequestData['request_class'] != "undefined" && $addRequestData['request_user_intro'] != "undefined")
        //if(!empty($addRequestData['request_user']) && !empty($addRequestData['request_class']) && !empty($addRequestData['request_user_intro']))
        {
            if(DB::table("user")->where("user_id","=",$addRequestData['request_user'])->first())
            {
                if(Request::addRequest($addRequestData['request_user'],$addRequestData['request_class'],$addRequestData['request_user_intro']) != false)
                {
                    return response()->json(["status"=>true,"message"=>"添加请求成功"]);
                }
                else
                {
                    return response()->json(["status"=>false,"message"=>"添加请求失败"]);
                }
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"添加请求失败"]);
            }
        }
        else
        {
            throw new PowerException("三个表单字段都为必填！");
        }
    }

}