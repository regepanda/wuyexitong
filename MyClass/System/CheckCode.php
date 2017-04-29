<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/16
 * Time: 16:46
 */

namespace MyClass\System;


use Illuminate\Support\Facades\DB;

class CheckCode
{
    /**
     * 查看本次是否可以发送
     * @param $phone
     * @return bool  是否能够发送
     */
    public static function canSend($phone)
    {
        $data = DB::table("check_code")->orderBy("check_id","desc")->where("check_phone","=",$phone)->first();

        if(empty($data))
        {
            return true;
        }
        $createTime = strtotime($data->check_time);
        /*dump($createTime);
        dump(time());
        dump(time() - $createTime);
        dump(config("my_config.check_code_divide"));*/
        if(time() - $createTime < config("my_config.check_code_divide"))
        {
            return false;
        }

        return true;
    }


    /**
     * 给出发送给服务器的验证码，返回该验证码的id
     * @param $code
     * @param $phone
     * @return int
     */
    public static function saveCode($code,$phone)
    {
        $id = DB::table("check_code")->insertGetId(["check_code"=>$code,"check_phone"=>$phone,"check_time"=>date('Y-m-d H:i:s'),"check_use"=>false]);
        if($id==false)
        {
            throw new \Exception("验证码录入数据库出错，请报告管理员");
        }
        return $id;
    }


    /**
     * 给出codeId，验证是否匹配
     * @param $codeId
     * @param $code
     * @return array
     * |-status     是否成功
     * |-message  说明
     *
     */
    public static function checkCode($codeId,$code)
    {
        $getData= DB::table("check_code")
            ->where("check_id","=",$codeId)
            ->where("check_code","=",$code)
            ->where("check_use","=",false)
            ->first();
        if(empty($getData))
        {
            return ["status"=>false,"message"=>"错误，没有匹配的验证码"];
        }


        $createTime = strtotime($getData->check_time);

        if(time() - $createTime > config("my_config.check_code_expire"))
        {

            return ["status"=>false,"message"=>"错误，验证码过期"];
        }
        DB::table("check_code")->where("check_id","=",$getData->check_id)->update(["check_use"=>true]);
        return ["status"=>true,"message"=>"验证码核对成功"];
    }
}