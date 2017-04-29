<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:31
 */

namespace MyClass\User;

use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;

class Account implements DatabaseModel
{
    /**
     * @var 账户id
     */
    public $account_id;
    public $info;

    /**
     * 构造函数
     * @param $account_id
     */
    public function __construct($account_id)
    {
        $this->account_id = $account_id;
        $this->info = $this->getInfo();
    }
    /*
     *获取基本信息
     * @return $info/bool
     */
    public function getInfo()
    {
        $this->info = DB::table("account")->where("account_id","=",$this->account_id)->first();
        if(isset($this->info->account_user))
        {
            $userData = DB::table("user")->where("user_id","=",$this->info->account_user)->first();
            $this->info->user_username = $userData->user_username;
            $this->info->user_phone = $userData->user_phone;
        }
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /**
     * 添加一个账户
     * @param $user      账户所有者
     * @param int $class 类型，默认系统级账户
     * @return bool|Account
     */
    public static function addAccount($user,$integration=0,$class=15)
    {
        $addData["account_user"] = $user;
        $addData['account_integration'] = $integration;
        $addData["account_class"] = $class;
        $id = Account::add($addData);
        if($id != false)
        {
            return new Account($id);
        }
        else
        {
            return false;
        }
    }

    /*
     * 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
  * 参数 $queryLimit
  * |-start  起始条数
  * |-num   每页条数
  * |-class 传入类别查找此类别的记录
  * |-sort   排序（根据自己的传入的字段，依照此字段排序）
  * |-search 搜索关键字（字段account_key的模糊查询）
  * |-user   用户筛选（如果涉及到用户,传入用户id）
  * |-desc   是否逆转排序即倒序(默认正序)
  * |-paginate  分页（使用laravel自动分页，这里指定数值）
  * |-id      按照字段id值，筛选某条记录
  * |-joinUser 是否左连接user表
  * |*/
    /*
    * $return_data
    * |-status 是否成功
    * |-message 消息
    * |-data  查询结果值
    * |-total 符合查询条件的总条数，不是数据库总条数

   /**
    * 筛选账户
    * @param $query_limit 数组
    */
    public static function select($query_limit)
    {
        $query = DB::table("account");

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
            if(isset($query_limit["desc"])  && true==$query_limit["desc"])
            {
                $query = $query->orderBy("account_id","desc");
            }
            else
            {
                $query = $query->orderBy("account_id");
            }
        }

        //按用户查找
        if(isset($query_limit["user"]))
        {
            $query = $query->where("account_user","=",$query_limit["user"]);
        }

        //关键字
        if ( isset($query_limit["search"]))
        {
            $query = $query->where("account_key","like","%".$query_limit["search"]."%");
        }

        //筛选类别
        if ( isset($query_limit["class"])  )
        {
            $query = $query->where("account_class","=",$query_limit["class"]);
        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("account_id","=",$query_limit["id"]);
        }

        //符合查询条件的总条数，不是数据库总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;

        //起始条数
        if ( isset($query_limit["start"])  )
        {
            $query = $query->skip($query_limit["start"]);
        }

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

         if(isset($query_limit["joinUser"]) && $query_limit["joinUser"] == true)
         {
             $query = $query ->leftJoin('user', 'account.account_user', '=', 'user.user_id');
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

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;

    }
    /*
     * 添加一条记录
     * @param $id/false
     */
    public static function add($inputData)
    {
        $inputData["account_create_time"] = date('Y-m-d H:i:s');
        $inputData["account_update_time"] = date('Y-m-d H:i:s');
        $inputData["account_check"] = true;
        $return = DB::table("account")->insertGetId($inputData);
        if($return)
        {
            return $return;
        }
        return false;
    }
    /**
     * 根据id删除一条记录
     * @return bool
     */
    public function delete()
    {
        $account_id = $this->account_id;
        //判断是否存在
        $record = DB::table('account')->where("account_id", "=", $account_id)->first();
        if ($record != null) {
            //存在，再删
            $return = DB::table('account')->where("account_id", "=", $account_id)->delete();
            if ($return)
            {
                return true;
            }
            return false;
        }
        return false;
    }

    /*
     * 根据id更新一条记录
     * @param $input 数组
     */
    public function update($inputData)
    {
        $account_id = $this -> account_id;
        $inputData["account_update_time"] = date('Y-m-d H:i:s');
        $inputData["account_check"] = true;
        $return = DB::table("account")->where("account_id", "=", $account_id)->update($inputData);
        if($return > 0)
        {
            return true;
        }
        return false;
    }



}