<?php
/**
 * Created by PhpStorm.
 * User: Silence
 * Date: 2016/3/11
 * Time: 15:59
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use MyClass\Admin\Admin;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
use MyClass\Admin\CommunityGroup;

class AdminController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(1))
        {
            
        }
        else
        {
            throw new PowerException("你没有此操作权限");
        }
        $guiFunc->setModule("Manage");
    }

    public function sAdmin(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sAdmin");
        $paginate = true;  //要分页
        $data = Admin::getAdmin($paginate);
        if ($paginate)
        {
            $data["paginate"] = true;
        }
        else
        {
            $data["paginate"] = false;
        }
        //查找所有的物业公司
        $data['communityGroup'] = CommunityGroup::select($queryLimit = null);
        //dump($data);
        return view("Admin.Manage.sAdmin",$data);
    }

    public function aAdmin(\MyClass\Base\GuiFunction $gui)
    {
        $input = Request::only('admin_username', 'admin_nickname', 'admin_password', 'admin_group');
        if(!empty($input["admin_password"])){$input["admin_password"] = md5('admin_password');}

        $return = Admin::Add($input);

        if($return)
        {
            $gui->setMessage(true,"添加管理员成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"添加管理员失败！");
            return redirect()->back();
        }

    }

    public function uAdmin(\MyClass\Base\GuiFunction $gui)
    {
        $adminId = Request::input('admin_id');

        $data["admin_username"] = Request::input('admin_username');
        $data["admin_nickname"] = Request::input('admin_nickname');
        $data["admin_group"] = Request::input('admin_group');

        $admin = new Admin($adminId);
        $return =  $admin->update($data);
        if($return)
        {
            $gui->setMessage(true,"修改管理员成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"修改管理员失败！");
            return redirect()->back();
        }
    }

    public function dAdmin($admin_id,\MyClass\Base\GuiFunction $gui)
    {
        $admin = new Admin($admin_id);
        $return = $admin->delete();
        if($return)
        {
            $gui->setMessage(true,"删除管理员成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"删除管理员失败！");
            return redirect()->back();
        }
    }

    //为管理员指定物业公司
    public function addCommunityGroup(\MyClass\Base\GuiFunction $gui)
    {
        $postData = Request::only("admin_community_group","admin_id","admin_username","admin_nickname","admin_group");
        $admin = new Admin($postData['admin_id']);
        if($admin->update($postData))
        {
            $gui->setMessage(true,"添加物业公司成功");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"添加物业公司失败！");
            return redirect()->back();
        }
    }

}