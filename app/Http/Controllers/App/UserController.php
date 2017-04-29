<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:56
 */

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use MyClass\System\DBLog;
use MyClass\User\User;
use Illuminate\Support\Facades\Request;
use MyClass\User\UserTrueInfo;
use MyClass\System\CheckCode;

class UserController extends Controller
{

    public function getUserInfo()
    {
        /*
         * 获取用户的基本信息
         *
         *  不包含需要认证的信息（真实姓名，车，房）
        除了access_token 无需传递任何参数，不使用1.2中的标准查询

        返回标准结构

        json数据
        |-status 是否成功true/false
        |-message 描述
        |-data 需要的数据 （如果需要返回数据，比如像查询）
        |-result_code 结果码，可以对应版本的最新的表来查看_
        -1 通用错误
       0 正确
       1 accesstoken 过期，需要从新获取
        2 accesstoken 错误
        其中data数据
        {
            user_id:int                 系统内部id
            user_username:string,       用户名
            user_nickname:string,       昵称
            user_sex:string,            性别
            user_phone:string,          电话
            user_birthday:string,       生日
            user_phone_backup:string    备用电话
            user_image:int              头像图片的id

        }
         */
        try
        {
            $userModel = new User(session("user.user_id"));



            $data["user_id"] = session("user.user_id");
            $data["user_username"] =$userModel->info->user_username;
            $data["user_nickname"] = session("user.user_nickname");
            $data["user_sex"] = $userModel->info->user_sex;
            $data["user_phone"] = $userModel->info->user_phone;
            $data["user_birthday"] = $userModel->info->user_birthday;
            $data["user_phone_backup"] = $userModel->info->user_phone_backup;
            $data["user_image"] = $userModel->info->user_image;
            

            return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
        } catch (\Exception $e) {
            DBLog::SystemLog("用户:请求用户信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

    public function updateUserInfo()
    {
        /*
         *
         *   需要发送的数据如下：
        |-username
        |-phone
        |-nickname
        |-sex
        |-birthday
        忽略字段表示不修改
        返回标准结构
         */

        //从前端获取数据

        try
        {
            $userData = Request::all();
            $user = new User(session("user.user_id"));
            $updateData["user_username"] = $userData["username"];
            $updateData["user_phone"] = $userData["phone"];
            $updateData["user_nickname"] = $userData["nickname"];
            $updateData["user_sex"] = isset($userData["sex"])?$userData["sex"]:null;
            $updateData["user_birthday"] = $userData["birthday"];
            $return = $user->update($updateData);
            if ($return)
            {
                $getRecord = $user->getInfo();
                return response()->json(["status" => true, "message" => "正确", "data" => $getRecord, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => $user->info, "result_code" => "-1"]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("用户:修改用户信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

    /*************用户真实身份部分*************/
    public function getTrueInfo()
    {
        /*
         * 获取真实信息接口
         *   返回标准结构
            其中data结构
             {
                info_id:int             系统内部id
                info_name:string,       真实姓名
                info_ic_id:string       身份证号
                info_age:int       身份年龄
                info_sex:string       身份性别

            }
         */

        try
        {
            $queryLimit["user"] = session("user.user_id");
            $array = UserTrueInfo::select($queryLimit);
            if ($array["data"] != null)
            {
                $data["info_id"] = $array["data"][0]->info_id;
                $data["info_name"] = $array["data"][0]->info_name;
                $data["info_ic_id"] = $array["data"][0]->info_ic_id;
                $data["info_age"] = $array["data"][0]->info_age;
                $data["info_sex"] = $array["data"][0]->info_sex;
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "没有数据", "data" => null, "result_code" => "-1"]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("用户:获取用户真实信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

    public function addTrueInfo()
    {

        /*
     需要发送的信息
     |-name
     |-ic_id
    |-info_age
    |-info_sex
     返回标准结构
   */
        try
        {
            $requestData = Request::all();

            $userTrueInfoData["info_name"] = $requestData["name"];
            $userTrueInfoData["info_ic_id"] = $requestData["ic_id"];
            $userTrueInfoData["info_user"] = session("user.user_id");
            $userTrueInfoData["info_check"] = false;
            if(isset($requestData['info_age']))
            {
                $userTrueInfoData["info_age"] = $requestData["info_age"];
            }
            $userTrueInfoData["info_sex"] = $requestData["info_sex"];
            $userTrueInfoId = UserTrueInfo::add($userTrueInfoData);
            $user = new UserTrueInfo($userTrueInfoId);
            $record = $user->getInfo();

            if ($userTrueInfoId != false)
            {
                return response()->json(["status" => true, "message" => "正确", "data" => $record, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "错误，不能有重复的身份证号", "data" => $record, "result_code" => "-1"]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("用户:添加用户真实信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }


    public function delTrueInfo()
    {
        /*
   *  需要提供
    |-id (系统内部id)
     */
        try
        {
            $data = Request::all();
            if(isset($data["access_token"]))
            {
                unset($data["access_token"]);
            }

            $queryLimit["id"] = $data["id"];
            $array = UserTrueInfo::select($queryLimit);

            if ($array["data"] != null)
            {
                $userTrueClass = new UserTrueInfo($data["id"]);
                if($userTrueClass->info->info_user == session("user.user_id"))
                {
                    if ($userTrueClass != false)
                    {
                        $return = $userTrueClass->delete();
                        if ($return)
                        {
                            return response()->json(["status" => true, "message" => "正确", "data" => null, "result_code" => "0"]);
                        }
                    }
                }
                return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("用户:删除用户真实信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

    public function createUser()
    {
        /*
        |-username
        |-password
        |-phone
        |-nickname
        |-sex
        |-birthday
        |-check_code_id //验证码id，客户端调用发送验证码接口后返回的结果
        |-check_code    //用户输入的验证码
        返回标准信息，data中有创建的用户信息
        data={
               id
               username
               usernickname
              }
         */

        try
        {
            $userData = Request::all();
            //判断返回的验证码是否和短信上的验证码[数据库中]是否一致
            if(CheckCode::checkCode($userData['check_code_id'],$userData['check_code']))
            {
                //验证码通过
                $addData["user_username"] = $userData["phone"];//$userData["username"];
                $addData["user_password"] = $userData["password"];
                $addData["user_phone"] = $userData["phone"];
                $addData["user_nickname"] = isset($userData["nickname"])?$userData["nickname"]:null;
                $addData["user_sex"] = isset($userData["sex"])?$userData["sex"]:null;
                $addData["user_birthday"] = isset($userData["birthday"])?$userData["birthday"]:null;
                $addReturn =  User::register($addData["user_password"],$addData["user_phone"],$addData);
                if ($addReturn["status"] != false)
                {
                    return response()->json($addReturn);
                }
                return response()->json($addReturn);
            }
            return response()->json(["status" => false, "data" => [], "message" => "验证码输入错误！", "result_code" => -1]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("用户:创建用户信息(注册)错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "错误！" . $e->getMessage(), "result_code" => -1]);
        }
    }

    public function resetPassword()
    {
        /*
         |—phone     手机号码（两者任意选择一个)
         |-password  新的密码
         |-check_code_id //验证码id，客户端调用发送验证码接口后返回的结果
         |-check_code    //用户输入的验证码

         返回标准信息
         */
        try
        {
            $resetData = Request::all();
            //判断返回的验证码是否和短信上的验证码[数据库中]是否一致
            if(CheckCode::checkCode($resetData['check_code_id'],$resetData['check_code']))
            {
                //验证码通过
                //修改密码
                if(User::updatePassword($resetData['phone'],$resetData['password']))
                {
                    //如果修改成功
                    return response()->json(["status" => true, "data" => [], "message" => "修改密码成功", "result_code" => 0]);
                }
                return response()->json(["status" => false, "data" => [], "message" => "修改密码失败", "result_code" => -1]);
            }
            return response()->json(["status" => false, "data" => [], "message" => "验证码输入错误！", "result_code" => -1]);
        }
        catch (\Exception $e)
        {
            return response()->json(["status" => false, "data" => [], "message" => "错误！" . $e->getMessage(), "result_code" => -1]);
        }
    }
}

