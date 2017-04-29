<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/4/7
 * Time: 10:44
 */


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
use MyClass\Serve\Serve;

class ServeController extends Controller
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


    public function sServe(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sServe");
        $queryLimit["paginate"] = 20;
        $queryLimit["desc"] = true;
        $queryLimit["newServe"] = true;
        $array = Serve::select($queryLimit);
        $outputData["output"] = $array["data"];
        return view("Admin.Manage.sServe",$outputData);
    }

    public function aServe(\MyClass\Base\GuiFunction $gui)
    {
        $input = Request::only('class_name', 'class_intro');
        $return = Serve::add($input);
        if($return)
        {
            $gui->setMessage(true,"添加服务成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"添加服务失败！");
            return redirect()->back();
        }

    }


    public function uServe(\MyClass\Base\GuiFunction $gui)
    {
        $classId = Request::input('class_id');

        $data["class_name"] = Request::input('class_name');
        $data["class_intro"] = Request::input('class_intro');

        $class = new Serve($classId);
        $return =  $class->update($data);
        if($return)
        {
            $gui->setMessage(true,"修改服务成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"修改服务失败！");
            return redirect()->back();
        }
    }

    public function dServe($class_id,\MyClass\Base\GuiFunction $gui)
    {
        $serve = new Serve($class_id);
        $return = $serve->delete();
        if($return)
        {
            $gui->setMessage(true,"删除服务成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"删除服务失败！");
            return redirect()->back();
        }
    }






}