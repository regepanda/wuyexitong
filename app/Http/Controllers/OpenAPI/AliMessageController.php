<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/15
 * Time: 20:13
 */

namespace App\Http\Controllers\OpenAPI;

use Illuminate\Support\Facades\Request;
use MyClass\OpenApi\AliMessage\AliMessage;
use App\Http\Controllers\Controller;

class AliMessageController extends Controller
{
    const TYPE_REGISTER = 1; //表示注册
    const TYPE_FIND = 2;  //表示找回密码

    public function getCheckCode()
    {
        /*
         |-phone
         |-type   //要获取验证码的类型【1表示注册，2表示找回密码】

         返回标准信息，data中有创建的用户信息
         data =
         {
            check_code_id //验证码的id，客户端在后面的请求中需要发送这个参数和客户输入的验证码。
         }
         */
        $getData = Request::all();
        $userPhone = $getData['phone'];
        $type = $getData['type'];
        $aliMessage = new AliMessage();
        //判断请求验证码的类型
        if($type == AliMessageController::TYPE_REGISTER)
        {
            //注册类型
            if($result = $aliMessage->sendMesRegister($userPhone))
            {
                $data['check_code_id'] = $result['codeId'];
                if($result['result']['success'] == true)
                {
                    //发送验证码成功
                    return response()->json(["status"=>true,"message"=>"成功获取到注册验证码","data"=>$data,"result_code"=>0]);
                }
                return response()->json(["status"=>false,"message"=>"获取注册验证码失败","data"=>[],"result_code"=>-1]);
            }
            return response()->json(["status"=>false,"message"=>"获取注册验证码失败","data"=>[],"result_code"=>-1]);
        }
        if($type == AliMessageController::TYPE_FIND)
        {
            //找回密码类型
            if($result = $aliMessage->sendMesFindPassword($userPhone))
            {
                $data['check_code_id'] = $result['codeId'];
                if($result['result']['success'] == true)
                {
                    //发送验证码成功
                    return response()->json(["status"=>true,"message"=>"成功获取找回密码验证码","data"=>$data,"result_code"=>0]);
                }
                return response()->json(["status"=>false,"message"=>"获取找回密码验证码失败","data"=>[],"result_code"=>-1]);
            }
            return response()->json(["status"=>false,"message"=>"获取找回密码验证码失败","data"=>[],"result_code"=>-1]);
        }
    }
}