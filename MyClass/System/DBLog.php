<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:39
 */

namespace MyClass\System;

use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\User\Account;

/**
 * Class DBLog
 * @package MyClass\System
 */
class DBLog implements  DatabaseModel
{


    /**
     * @var
     */
    private $log_id;
    public $info;
    /**
     * @param $log_id
     */

    const ERROR = 1;
    const WARNING = 2;
    const DEBUG = 3;
    const INFO = 4;
    const SYSTEMINFO = 5;


    public function __construct($log_id)
    {
        $this->log_id = $log_id;
        $this->getInfo();
    }

    /*
     *获取基本信息
     * @return $info/bool
     */
    public function getInfo()
    {
        $this->info = DB::table("log")->where("log_id","=",$this->log_id)->first();
        if($classData = DB::table("class")->where("class_id","=",$this->info->log_class)->first() != null)
        {
            $this->info->class_name = $classData->class_name;
        }

        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /**
     * 添加user相关数据
     * @param $message
     * @param $user
     * @param $level
     * @param null $otherData
     * @return bool
     */
    public static function userLog($message, $user, $level,$otherData = null)
    {
        $inputData["log_user"] = $user;
        $inputData["log_level"] = $level;
        $inputData["log_other_data"] = $otherData;
        $inputData["log_content"] = $message;
        $inputData["log_create_time"] = date('Y-m-d H:i:s');

        $return = DB::table("log")->insert($inputData);
        if($return)
        {
            return true;
        }
        return false;
    }

    /**
     * 添加admin相关信息
     * @param $message
     * @param $admin
     * @param $level
     * @param null $otherData
     * @return bool
     */
    public static function adminLog($message, $admin, $level,$otherData = null)
    {
        $inputData["log_admin"] = $admin;
        $inputData["log_level"] = $level;
        $inputData["log_other_data"] = $otherData;
        $inputData["log_content"] = $message;
        $inputData["log_create_time"] = date('Y-m-d H:i:s');

        $return = DB::table("log")->insert($inputData);
        if($return)
        {
            return true;
        }
        return false;

    }

    /**
     * 添加user,account相关数据
     * @param $message
     * @param $account
     * @param $level
     * @param null $otherData
     * @return bool
     */
    public static function accountLog($message,$account,$level,$otherData = null)
    {
        $accountObj = new Account($account);
        $inputData["log_user"] = $accountObj ->info->account_user;
        $inputData["log_account"] = $account;
        $inputData["log_level"] = $level;
        $inputData["log_other_data"] = $otherData;
        $inputData["log_content"] = $message;
        $inputData["log_create_time"] = date('Y-m-d H:i:s');

        $return = DB::table("log")->insert($inputData);
        if($return)
        {
            return true;
        }
        return false;
    }

    public static function SystemLog($message,$level,$otherData = null)
    {

        $inputData["log_level"] = $level;
        $inputData["log_other_data"] = $otherData;
        $inputData["log_content"] = $message;
        $inputData["log_create_time"] = date('Y-m-d H:i:s');

        $return = DB::table("log")->insert($inputData);
        if($return)
        {
            return true;
        }
        return false;
    }



    /*
  * 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
* 参数 $queryLimit
* |-start  起始条数
* |-num   每页条数
* |-class 传入类别查找此类别
*|-level  传入等级查找此等级
* |-sort   排序（根据自己的传入的字段，依照此字段排序）
* |-user   用户筛选（如果涉及到用户,传入用户id）
* |-admin  管理员筛选
* |-account 账户筛选
* |-desc   是否逆转排序即倒序(默认正序)
* |-paginate  分页（使用laravel自动分页，这里指定数值）
* |-id      按照字段id值，筛选某条记录
* |*/
    /*
    * $return_data
    * |-status 是否成功
    * |-message 消息
    * |-data  查询结果值
    * |-total 符合查询条件的总条数，不是数据库总条数

   /**
    * 筛选记录
    * @param $query_limit 数组
    */
    /**
     * @param $query_limit
     * @return mixed
     */
    public static function select($query_limit)
    {

        $query = DB::table("log");



        //按用户查找
        if(isset($query_limit["user"]))
        {
            $query = $query->where("log_user","=",$query_limit["user"]);
        }

        //按管理员查找
        if(isset($query_limit["admin"]))
        {
            $query = $query->where("log_admin","=",$query_limit["admin"]);
        }

        //按账户查找
        if(isset($query_limit["account"]))
        {
            $query = $query->where("log_account","=",$query_limit["account"]);
        }

        //筛选类别
        if ( isset($query_limit["class"])  )
        {
            $query = $query->where("log_class","=",$query_limit["class"]);
        }

        //按等级查找
        if(isset($query_limit['level']))
        {
            $query = $query->where("log_level","=",$query_limit['level']);
        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("log_id","=",$query_limit["id"]);
        }

        //符合查询条件的总条数，不是数据库总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;

        //排序
        if(  isset($query_limit["sort"])  )  //自定义字段排序
        {
            if(isset($query_limit["desc"]) && true == $query_limit["desc"])
            {
                $query = $query->orderBy($query_limit["sort"],"desc");
            }
            else
            {
                $query = $query->orderBy($query_limit["sort"]);
            }

        }
        else    //按id排序
        {
            if(isset($query_limit["desc"])  && true == $query_limit["desc"])
            {
                $query = $query->orderBy("log_id","desc");
            }
            else
            {
                $query = $query->orderBy("log_id");
            }
        }


        //起始条数
        if ( isset($query_limit["start"])  )
        { $query = $query->skip($query_limit["start"]);}

        //每页条数
        if(isset($query_limit["num"]))
        {
            if($query_limit["num"]==0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "查询到数据,但num设为了0";
                $return_data["data"] =  null;
                return $return_data;
            }

            $query = $query->take($query_limit["num"]);
        }
        else
        {
            $query = $query->take(config("my_config.default_num_page"));     //自己增加，默认5条
            $query_limit["num"] = config("my_config.default_num_page");     //
        }

        //是否分页
        if(isset($query_limit["paginate"]))
        {

            $data = $query->simplePaginate($query_limit["paginate"]);
        }
        else
        {
            $data = $query->get();
        }

        //Add class to query out of each record
        foreach($data as $key => $value)
        {
            if(isset($value->log_class))
            {
                $data[$key]->class_name = DB::table("class")->where("class_id","=",$value->log_class)->first()->class_name;

            }
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }

    /**
     * 添加记录
     * @param $inputData
     * @return bool
     */
    public static function add($inputData)
    {
        $inputData["log_create_time"] = date('Y-m-d H:i:s');
        $inputData["log_content"] = "我是一个好孩子，哈哈";
        $return = DB::table("log")->insert($inputData);
        if($return)
        {
            return true;
        }
        return false;

    }

    /**
     * 根据id删除记录
     * @return bool
     */
    public function delete()
    {
        $log_id = $this->log_id;
        //判断是否存在
        $record = DB::table('log')->where("log_id", "=", $log_id)->first();
        if ($record != null) {
            //存在，再删
            $return = DB::table('log')->where("log_id", "=", $log_id)->delete();
            if ($return) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 根据id更新记录
     * @param $inputData
     * @return bool
     */
    public function update($inputData)
    {
        $log_id = $this -> log_id;
        $inputData["log_create_time"] = date('Y-m-d H:i:s');
        $inputData["log_content"] = "hahha";
        $return = DB::table("log")->where("log_id", "=", $log_id)->update($inputData);
        if($return > 0)
        {
            return true;
        }
        return false;
    }

}