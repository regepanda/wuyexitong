<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/13
 * Time: 12:23
 */

namespace MyClass\Serve;


use Illuminate\Support\Facades\DB;

class Now
{
    //获取映射
    public static function getMapping($i)
    {
        //dump($i);
        $resultData = DB::table("now")->where("now_id","=",$i)->first();

        return $resultData->now_value;

    }

    //添加映射
    public static function addMapping($str)
    {
        $id = DB::table("now")->insertGetId($str);
        return $id;
    }

    //传入一个id 查看是否是当前月份
    public static function isNow($id)
    {
        $data = DB::table("now")->where("now_is_now","=",true)->where("now_id","=",$id)->first();
        if(empty($data))
        {
            return false;
        }
        else
        {
            return true;
        };
    }

    //获取当前月份
    public static function getNow()
    {
        $data = DB::table("now")->where("now_is_now","=",true)->first();
        if(!empty($data))
        {
            return $data->now_id;
        }
        return false;

    }

    //进入下一月
    public static function next()
    {
        $id = self::getNow();
        if($id == false){return false;}
        DB::beginTransaction();
        $r1 = DB::table("now")->where("now_is_now","=",true)->update(["now_is_now"=>false]);
        $r2 = DB::table("now")->where("now_id","=",$id+=1)->update(["now_is_now"=>true]);
        DB::commit();
        return ;
    }
}