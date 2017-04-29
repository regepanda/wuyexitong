<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/24
 * Time: 20:30
 */

namespace App\Http\Controllers\App;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use MyClass\System\DBLog;
use MyClass\User\User;

class LoginController extends Controller
{
    public function login()
    {
        /*
           如果你需要访问登录权限的接口，首先你应该登录获得一个token
           登录接口:
           GET    /app_login
                 |-username
                 |-password'
           如果用户名和密码正确，将会返回一个access_token,在data里面
         *
         */

        //获取前端用户名和密码

        try {
            $userData = Request::all();
            $model = User::login($userData["username"], $userData["password"]);

            if ($model == false) {
                return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
            }
            $token = User::setAccessToken($model);

            return response()->json(["status" => true, "message" => "正确", "data" => $token, "result_code" => "0"]);
        }
        catch(\Exception $e)
        {

            DBLog::SystemLog("用户:登录信息信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }

    }

}