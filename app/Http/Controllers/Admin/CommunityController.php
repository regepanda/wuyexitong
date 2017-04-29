<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/26
 * Time: 14:17
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\User\Community;
use MyClass\Exception\PowerException;
use MyClass\Admin\CommunityGroup;

class CommunityController extends Controller
{
    //这里写一个方法，用于权限检查
    public function checkPermissions()
    {
        if(PowerGroup::checkAdminPower(1))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限");
        }
    }

    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("Manage");
    }

    public function sCommunity(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sCommunity");
        return view("Admin.Manage.sCommunity");
    }
    //获取所有的物业公司
    public function getCommunityGroup()
    {
        $queryLimit = null;
        $data = CommunityGroup::select($queryLimit);
        return response()->json($data);
    }
    //添加小区
    public function aCommunity()
    {
        $this->checkPermissions();

        $input = Request::only('community_name', 'community_address', 'community_intro', 'community_city',
            'community_province');
        $input['community_group'] = session("admin.community_group");
        $return = Community::Add($input);
        if($return)
        {
            return response()->json(["status"=>true,"message"=>"增加小区成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"增加小区失败"]);
        }
    }

    public function uCommunity()
    {
        $this->checkPermissions();
        $uCommunityData = Request::all();
        $uCommunityData['community_group'] = (int)substr($uCommunityData['community_group'],- 1);
        //dump($uCommunityData);
        $object = new Community($uCommunityData['community_id']);
        $return =  $object->update($uCommunityData);
        if($return)
        {
            return response()->json(["status"=>true,"message"=>"修改小区成功"]);
        }
        else
        {
             return response()->json(["status"=>false,"message"=>"修改小区失败"]);
        }
    }

    public function dCommunity()
    {
        $this->checkPermissions();

        $community = Request::all();
        $object = new Community($community["id"]);
        $return = $object->delete();
        if($return)
        {
            return response()->json(["status"=>true,"message"=>"删除小区成功"]);
        }
        else
        {
        return response()->json(["status"=>false,"message"=>"删除小区失败"]);
        }
    }


    public function sAllCommunity()
    {
        $this->checkPermissions();

        $queryLimit = Request::all();
        //如果当前系统是以物管的角色登录，那查询的小区也应该是此物管所管理的小区
        if(session("admin.community_group") != null)
        {
            $queryLimit['admin_community_group'] = session("admin.community_group");
        }
        $data = Community::select($queryLimit);
        return response()->json($data);
    }

    public function getCommunityById()
    {
        $requestData = Request::all();
        $queryLimit["id"] = $requestData["community_id"];
        $data = Community::select($queryLimit);
        return response()->json($data);
    }

}

