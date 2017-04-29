<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/15
 * Time: 9:31
 */

namespace MyClass\System;


use Illuminate\Support\Facades\DB;

class SystemUpdate
{

    const IOS = "IOS";
    const ANDROID = "Android";

    /**
     * 返回一个包含所有版本列表的数据
     * @return []
     */
    public static function getVersion()
    {
        /*$versionList = json_decode(SystemMap::index("SystemVersion"),true);
        if(empty($versionList))
        {return [];}
        return $versionList;*/
        $versionList = DB::table("version")->get();
        if(empty($versionList))
        {return [];}
        return $versionList;
    }




    /**
     * 添加一个版本
     * @param $name
     * @param int $type
     * @param $file
     * @return bool
     */
    public static function addVersion($versionName,$type = self::ANDROID,$file)
    {
        //1.文件移动
        /*$storage_path = config("my_config.client_version_binary_dir");  //存贮文件的相对路径
        $path = $_SERVER['DOCUMENT_ROOT'].$storage_path;  //存贮文件的绝对路径
        $name = date('YmdHis').rand(1000, 9999).".".$file->getClientOriginalExtension();  //自动生成路径


        DB::beginTransaction();
        $insertData["version"] = self::getMaxId()+1;
        $insertData["name"] = $versionName;
        $insertData["date"] = date('Y-m-d H:i:s');
        $insertData["path"] = $storage_path.$name;
        $insertData["type"] = $type;

        $versionData = json_decode(SystemMap::index("SystemVersion"),true);
        $versionData[] =  $insertData;

        if (SystemMap::index("SystemVersion",json_encode($versionData)))
        {
            $moveReturn = $file->move($path, $name);  //移动文件到指定目录
            if($moveReturn)
            {
                DB::commit();  //若移动文件或添加进数据库失败，则事务回滚
                return true;
            }
            return false;
        }
        else
        {
            //上传图片，失败时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."更新版本失败了";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "上传版本";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }*/
        //1.文件移动
        $storage_path = config("my_config.client_version_binary_dir");  //存贮文件的相对路径
        $path = $_SERVER['DOCUMENT_ROOT'].$storage_path;  //存贮文件的绝对路径
        $name = date('YmdHis').rand(1000, 9999).".".$file->getClientOriginalExtension();  //自动生成路径

        DB::beginTransaction();
        $insertData["version_id"] = self::getMaxId()+1;
        $insertData["version_name"] = $versionName;
        $insertData["version_create_time"] = date('Y-m-d H:i:s');
        $insertData["version_path"] = $storage_path.$name;
        $insertData["version_type"] = $type;

        if (DB::table("version")->insert($insertData))
        {
            $moveReturn = $file->move($path, $name);  //移动文件到指定目录
            if($moveReturn)
            {
                DB::commit();  //若移动文件或添加进数据库失败，则事务回滚
                return true;
            }
            return false;
        }
        else
        {
            //上传图片，失败时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."更新版本失败了";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "上传版本";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }


    /**
     * 删除一个版本
     * @param $id
     * @return bool
     */
    public static function delVersion($id)
    {
        /*$versionData = json_decode(SystemMap::index("SystemVersion"),true);
        if(empty($versionData))
        {
            return false;
        }

        foreach($versionData as $key => $data)
        {
            if($data["version"] == $id)
            {
                unset($versionData[$key]);
                return SystemMap::index("SystemVersion",json_encode($versionData));

            }
        }
        return false;*/
        if(DB::table("version")->where("version_id","=",$id)->delete())
        {
            return true;
        }
        return false;
    }

    /**
     * 获取当前最大id
     * @return int
     */
    public static function getMaxId()
    {
        $versionData = DB::table("version")->get();
        $max = 0;
        if(empty($versionData))
        {
            return 0;
        }

        foreach($versionData as $key => $data)
        {
            if($data->version_id > $max)
            {
                $max = $data->version_id;
            }
        }
        return $max;
    }
}