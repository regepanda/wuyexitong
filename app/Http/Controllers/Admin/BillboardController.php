<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/26
 * Time: 11:23
 */

namespace App\Http\Controllers\Admin;
use MyClass\Base\GuiFunction;
use App\Http\Controllers\Controller;
use MyClass\System\Billboard;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class BillboardController extends Controller
{
    public function __construct()
    {
        if(PowerGroup::checkAdminPower(5)){}
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
    }
    //展示页面
    public function sBillboard(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sBillboard");
        return view("Admin.System.sBillboard");
    }

    //查询公告信息并分页、排序、条件查找
    public function allBillboard()
    {
        $queryLimit = Request::all();
        //如果当前系统是以物管的角色登录，那查询的公告信息也应该是此物管所管理的小区的住户的
        if(session("admin.community_group") != null)
        {
            $queryLimit['admin_community_group'] = session("admin.community_group");
        }
        $data = Billboard::select($queryLimit);
        return response()->json($data);
    }

    //修改公告信息
    public function updateBillboard()
    {
        $updateBillboardData = Request::all();
        if(!empty($updateBillboardData['billboard_title']) || !empty($updateBillboardData['billboard_detail']))
        {
            $billboard = new Billboard($updateBillboardData['billboard_id']);
            if($billboard->update($updateBillboardData))
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
            throw new PowerException("两个表单字段不能全为空！");
        }
    }

    //删除公告
    public function deleteBillboard()
    {
        $deleteBillboardData = Request::all();
        $billboard = new Billboard($deleteBillboardData['billboard_id']);
        if($billboard->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }

    //添加公告牌信息
    public function addBillboard()
    {
        $addBillboardData = Request::all();
        if($addBillboardData['billboard_title'] != "undefined" && $addBillboardData['billboard_detail'] != "undefined")
        {
            if(Billboard::add($addBillboardData))
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
            throw new PowerException("两个表单字段都为必填！");
        }
    }
}