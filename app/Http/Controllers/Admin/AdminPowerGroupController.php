<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 15:22
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MyClass\Admin\MorePowerGroup;
use MyClass\Admin\PowerGroup;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request;
use MyClass\Exception\PowerException;

class AdminPowerGroupController extends Controller
{

    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(1))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $guiFunc->setModule("Manage");
    }
    public function sAdminPowerGroup(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sAdminPowerGroup");
        $queryLimit['desc'] = true;
        $outputData["data"] = PowerGroup::select($queryLimit);
        $outputData["data"] = $outputData["data"]["data"];

        return view("Admin.Manage.sAdminPowerGroup",$outputData);
    }

    public function aAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $dataArray["group_name"] = $_POST["group_name"];
        $return =  PowerGroup::add($dataArray);

        if($return)
        {
            $gui->setMessage(true,"添加管理员权限组成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"添加管理员权限组失败！");
            return redirect()->back();
        }
    }

    public function uAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {

        $dataArray["group_name"] = $_POST["group_name"];
        $powerGroup = new PowerGroup($_POST["group_id"]);
        $return = $powerGroup->update($dataArray);
        if($return)
        {
            $gui->setMessage(true,"管理员权限组修改成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"管理员权限组修改失败！");
            return redirect()->back();
        }
    }

    public function dAdminPowerGroup($group_id,\MyClass\Base\GuiFunction $gui)
    {
        $powerGroup = new PowerGroup($group_id);
        $return = $powerGroup->delete();
        if($return)
        {
            $gui->setMessage(true,"管理员权限组删除成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"管理员权限组删除失败！");
            return redirect()->back();
        }
    }


    public function moreAdminPowerGroup($group_id)
    {
        $admin = new PowerGroup($group_id);
        $data = $admin->moreAdminPowerGroup();

        return view("Admin.Manage.moreAdminPowerGroup",$data);
    }

    public function removeAdminToAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $return =  PowerGroup::removeAdmin(Request::input("admin_id"));
        if($return)
        {
            $gui->setMessage(true,"从此管理员组移除管理员成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"从此管理员组移除管理员失败！");
            return redirect()->back();
        }
    }

    public function addPowerToAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $postData = Request::only("group_id", "power_id_array");
        $admin = new PowerGroup($postData["group_id"]);
        $return = $admin->AddPowerToPowerGroup($postData["power_id_array"]);
        if($return)
        {
            $gui->setMessage(true,"在此管理员组添加权限成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此管理员组添加权限失败!");
            return redirect()->back();
        }
    }

    public function addAdminToAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $groupId = Request::input('group_id');
        $admin_id_array = Request::input("admin_id_array");
        $admin = new PowerGroup($groupId);
        $return =  $admin->addAdmin( $admin_id_array);
        if($return)
        {
            $gui->setMessage(true,"在此管理员组添加管理员成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此管理员组添加管理员失败!");
            return redirect()->back();
        }

    }

    public function removePowerToAdminPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $powerId = $_POST["relation_power_id"];
        $groupId = $_POST["relation_group_id"];
        $admin = new PowerGroup($groupId);
        $return =  $admin-> RemovePowerFromPowerGroup($powerId);
        if($return)
        {
            $gui->setMessage(true,"在此管理员组中移除该权限成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此管理员组中移除该权限失败！");
            return redirect()->back();
        }
    }

}