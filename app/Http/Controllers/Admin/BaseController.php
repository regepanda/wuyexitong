<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:56
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\Admin;
use MyClass\Base\GuiFunction;


class BaseController extends Controller
{
    //显示管理界面
    public function manageIndex(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("Manage");
        return view("Admin.Manage.index");
    }

    //显示系统界面
    public function system(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("System");
        return view("Admin.System.index");
    }
    //显示登录页面
    public function login()
    {
        return view("Admin.login");
    }
    public function _login(GuiFunction $guiFunction)
    {
        $data = Request::all();
        if(Admin::login($data['user_username'],$data['user_password']) != false)
        {
            $guiFunction->setMessage(true, "登陆成功");
            return redirect("admin_index");
        }
        else
        {
            $guiFunction->setMessage(false, "用户名不存在或密码错误");
            return redirect("admin_login");
        }
    }
    public function index()
    {
        return view("Admin.index");
    }
    public function logout()
    {
        Session::flush();
        return redirect("admin_login");
    }

}