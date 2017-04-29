<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/20
 * Time: 15:33
 */

namespace App\Http\Controllers\OpenAPI;

use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use MyClass\OpenApi\YunMessage\YunMessage;

class YunMessageController extends Controller
{

    public function getCheckCode()
    {
        /*
         |-phone

         返回标准信息，data中有创建的用户信息
         data =
         {
            check_code_id //验证码的id，客户端在后面的请求中需要发送这个参数和客户输入的验证码。
         }
         */
        $getData = Request::all();
        $userPhone = $getData['phone'];
        $yunMessage = new YunMessage();
        //注册类型
        if($result = $yunMessage->sendMessage($userPhone))
        {
            $data['check_code_id'] = $result['codeId'];
            if($result['success'] == true)
            {
                //发送验证码成功
                return response()->json(["status"=>true,"message"=>"成功获取到注册验证码","data"=>$data,"result_code"=>0]);
            }
            return response()->json(["status"=>false,"message"=>"获取注册验证码失败","data"=>[],"result_code"=>-1]);
        }
        return response()->json(["status"=>false,"message"=>"获取注册验证码失败","data"=>[],"result_code"=>-1]);
    }
}