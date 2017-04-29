<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/13
 * Time: 10:46
 */

namespace App\Http\Controllers\Test;


use App\Http\Controllers\Controller;
use MyClass\Admin\Admin;
use MyClass\User\User;

class ApiController extends Controller
{
    public function testToken()
    {
        $token = User::setAccessToken(new User(1));
        echo $token;
        dump(User::checkAccessToken($token));

    }

    public function testAdminLogin()
    {
        $adminModel = new Admin(1);
        $adminModel->setSession();
        dump(session("admin"));
    }
}