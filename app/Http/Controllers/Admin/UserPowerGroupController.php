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
use MyClass\User\PowerGroup;
use MyClass\Admin\PowerGroup as checkAdminPower;
use MyClass\Exception\PowerException;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request;

class UserPowerGroupController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sUser");
        if(checkAdminPower::checkAdminPower(2))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $guiFunc->setModule("Manage");
    }

    public function sUserPowerGroup(GuiFunction $guiFunc)
    {

        $guiFunc->setThirdModule("sUserPowerGroup");
        $queryLimit['desc'] = true;
        $outputData["data"] = PowerGroup::select($queryLimit);
        $outputData["data"] = $outputData["data"]["data"];
        return view("Admin.Manage.sUserPowerGroup",$outputData);
    }

    public function moreUserPowerGroup($group_id)
    {
        $user = new PowerGroup($group_id);
        $data = $user->moreAdminPowerGroup();

        return view("Admin.Manage.moreUserPowerGroup",$data);
    }

    public function removeUserToUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $return =  PowerGroup::removeUser(Request::input("admin_id"));
        if($return == true)
        {
            $gui->setMessage(true,"从此用户组移除用户成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"从此用户组移除用户失败！");
            return redirect()->back();
        }
    }


    public function addPowerToUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $postData = Request::only("group_id", "power_id_array");

        $admin = new PowerGroup($postData["group_id"]);
        $return =  $admin->AddPowerToUserPowerGroup($postData["power_id_array"]);
        if($return != false)
        {
            $gui->setMessage(true,"在此用户组添加权限成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此用户组添加权限失败!");
            return redirect()->back();
        }
    }

    public function addUserToUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $groupId = Request::input('group_id');
        $user_id_array = Request::input("user_id_array");
        $user = new PowerGroup($groupId);
        $return =  $user->addUser( $user_id_array);
        if($return != false)
        {
            $gui->setMessage(true,"在此用户组添加用户成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此用户组添加用户失败!");
            return redirect()->back();
        }
    }


    public function aUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $dataArray["group_name"] = $_POST["group_name"];
        $return =  PowerGroup::add($dataArray);
        if($return)
        {
            $gui->setMessage(true,"用户权限组添加成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"用户权限组添加失败！");
            return redirect()->back();
        }
    }

    public function uUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $dataArray["group_name"] = $_POST["group_name"];
        $powerGroup = new PowerGroup($_POST["group_id"]);
        $return = $powerGroup->update($dataArray);
        if($return)
        {
            $gui->setMessage(true,"用户权限组修改成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"用户权限组修改失败！");
            return redirect()->back();
        }
    }

    public function dUserPowerGroup($group_id,\MyClass\Base\GuiFunction $gui)
    {
        $powerGroup = new PowerGroup($group_id);
        $return = $powerGroup->delete();
        if($return)
        {
            $gui->setMessage(true,"用户权限组删除成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"用户权限组删除失败！");
            return redirect()->back();
        }
    }

    public function removePowerToUserPowerGroup(\MyClass\Base\GuiFunction $gui)
    {
        $powerId = $_POST["relation_power_id"];
        $groupId = $_POST["relation_group_id"];

        $admin = new PowerGroup($groupId);
        $return =  $admin-> RemovePowerFromPowerGroup($powerId);
        if($return)
        {
            $gui->setMessage(true,"在此用户组移除该权限成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"在此用户组移除该权限失败！");
            return redirect()->back();
        }
    }

}