<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 15:23
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use MyClass\User\User;


class RoleManageController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("Manage");
    }



    public function sAdmin()
    {



        // return view("Admin.Manage.sAdmin");
    }
    public function aUser()
    {
        $inputData["user_username"] = "王军泰";
        $inputData["user_password"] = md5("123");
        $inputData["user_sex"] = "男";
        $inputData["user_phone"] = "13258387502";
        $inputData["user_birthday"] = date('Y-m-d H:i:s');
        $inputData["user_group"] = "1";
        $inputData["user_phone_backup"] = 11215454;
        //查询是否存在用户
        $return = User::add($inputData);  //add()正确

        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }
    public function dUser($user_id)
    {

      //  $inputData["user_id"] = 1;
        $class = new User($user_id);
        $return = $class -> delete();   //delete()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }

    }
    public function uUser()
    {

        $inputData["user_username"] = "吕亮";
        $inputData["user_sex"] = "女";
        $inputData["user_nickname"] = "亮仔";
        $inputData["user_phone"] = "13258548941";
        $inputData["user_birthday"] = date('Y-m-d H:i:s');
        $class = new User(1);
        $return = $class -> update($inputData); //update()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }


    }




}