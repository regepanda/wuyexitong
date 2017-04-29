<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:38
 */

namespace MyClass\System;

use Illuminate\Support\Facades\DB;

/**
 * Class SystemMap
 * @package MyClass\System
 */
class SystemMap
{
    /**
     *
     * @param $key
     * @param null $value
     * @return bool
     */
    public static function  index($key,$value = null)
    {

        if($value == null)
        {

            $record =  DB::table('system_map')->where("system_key", "=", $key)->first();
            if($record == null)
            {
                return false;
            }
                return $record -> system_value;
        }
        else
        {

            $record =  DB::table('system_map')->where("system_key", "=", $key)->first();
            if($record == null)
            {
                $inputData["system_key"] = $key;
                $inputData["system_value"] = $value;
                $inputData["system_create_time"] = date('Y-m-d H:i:s');

                $return = DB::table("system_map")->insert($inputData);
                if($return)
                {
                    return true;
                }
                return false;
            }

            $record = DB::table("system_map")->where("system_key", "=", $key)
                                             ->where("system_value","=",$value)->first();
            if($record == null)
            {
                $inputData["system_value"] = $value;
                $inputData["system_create_time"] = date('Y-m-d H:i:s');
                $return =  DB::table("system_map")->where("system_key", "=", $key)->update($inputData);
                if($return > 0)
                {
                    return true;
                }
                return false;
            }
            return false;
        }

    }

    /**
     * ?
     * @param $key
     * @return bool
     */
    public static function del($key)
    {

        $record = DB::table('system_map')->where("system_key", "=", $key)->first();
        if ($record != null) {

            $return = DB::table('system_map')->where("system_key", "=", $key)->delete();
            if ($return)
            {
                //删除
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除了system_map表的一条记录";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "删除system_map";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;

    }

}