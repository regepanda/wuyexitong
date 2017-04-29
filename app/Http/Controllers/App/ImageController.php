<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/4/7
 * Time: 17:26
 */

namespace App\Http\Controllers\App;

use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use MyClass\System\DBLog;
use MyClass\System\Image;
use MyClass\User\User;


class ImageController extends Controller
{

    /*
     上传用户头像 POST /app_addUserHeadImage
    上传用户头像，发送格式为，需要表单为多部数据的形式（<form enctype="multipart/form-data">）
    |-image_data 图片数据//在前端获取的文件
    返回标准结构
    开发注意：图片由image表来记录(image_user为当前用户id)  用户表只留存image_id
     */

    public function addUserHeadImage()
    {
        try
        {

            if (Request::hasFile('image_data'))
            {
                $image_data = Request::file('image_data');
                $input["image_name"] = "上传的图片";
                $input["image_user"] = session("user.user_id");

                $return = Image::putImage($input, $image_data);
                if ($return)
                {
                    $user = new User(session("user.user_id"));


                    if($user->user_id == null)
                    {
                        throw new \Exception("请设置用户，APP接口设置AccessToken，测试端开启模拟权限代码");
                    }

                    if(!$user->setHeadImage($return))
                    {
                        throw new \Exception("设置头像失败");
                    }

                    return response()->json(["status" => true, "message" => "正确", "data" => null, "result_code" => "0"]);
                }
                return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("图片:上传图片信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }

  }






}