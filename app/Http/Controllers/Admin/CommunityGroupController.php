<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/9
 * Time: 17:10
 */

namespace App\Http\Controllers\Admin;

use MyClass\Base\GuiFunction;
use App\Http\Controllers\Controller;
use MyClass\Admin\CommunityGroup;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class CommunityGroupController extends Controller
{
    //显示页面
    public function sCommunityGroup(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sCommunityGroup");
        return view("Admin.Manage.sCommunityGroup");
    }
    //查找所有物业公司数据，条件查找、排序
    public function sAllCommunityGroup()
    {
        $data = CommunityGroup::select(Request::all());
        return response()->json($data);
    }
    //修改
    public function updateCommunityGroup()
    {
        $updateCommunityGroupData = Request::all();
        $communityGroup = new CommunityGroup($updateCommunityGroupData['group_id']);
        if($communityGroup->update($updateCommunityGroupData))
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }
    //删除
    public function deleteCommunityGroup()
    {
        $deleteCommunityGroupData = Request::all();
        $communityGroup = new CommunityGroup($deleteCommunityGroupData['group_id']);
        if($communityGroup->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }
    //添加
    public function addCommunityGroup()
    {
        $addCommunityGroupData = Request::all();
        if($addCommunityGroupData['group_name'] != "undefined" && $addCommunityGroupData['group_intro'] != "undefined")
        {
            if(CommunityGroup::add($addCommunityGroupData))
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
            return response()->json(["status"=>false,"message"=>"添加失败,字段不能为空！"]);
        }
    }
}