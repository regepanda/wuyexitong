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
use MyClass\System\DBLog;

/**
 * Class  UserTrueInfo
 * @package MyClass\User
 */
class UserTrueInfo implements DatabaseModel
{
    /*
     * @var user_true_info表单的一条记录（即一个对象）
     */
    public $info;

    /*
     * @var 用户真实信息id
     */
    public $info_id;

    /*
     * 构造函数
     * @param $user_id
     */
    public function __construct($info_id)
    {
        $this->info_id = $info_id;
        $this->getInfo();
    }

    /*
     * @return bool|user_true_info表单的一条记录
     */
    public function getInfo()
    {
        $this->info = DB::table("user_trueinfo")->where("info_id","=",$this->info_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }


    /* 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
    * 参数  $queryLimit
    * |-start  起始条数
    * |-num   每页条数
    * |-sort   排序,按照哪个字段排序
    * |-user   用户筛选（如果涉及到用户,传入用户id）
    * |-check  审核筛选（传入true,为筛选审核，false为筛选未审核的）
    * |-desc   是否逆转排序即倒序(默认正序)
    * |-paginate  分页（使用laravel自动分页，这里指定数值），传入要想分页的条数
    * |-id      按照字段id查找
    * |-nickname  根据昵称和密码判断是否存在此用户
    * |-password
    * |-admin_community_group  根据物业公司筛选

      /*
      * 返回值 $return_data
      * |-status 是否成功
      * |-message 消息
      * |-data   查询的结果
      * |-total 符合查询条件的总条数，不是数据库总条数
   |*/
    /**
     * @param $query_limit 数组
     * @return mixed数组
     */
    public static function select($query_limit)
    {
        $query = DB::table("user_trueinfo");

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
                $query = $query->orderBy("info_id","desc");
            }
            else
            {
                $query = $query->orderBy("info_id");
            }
        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("info_id","=",$query_limit["id"]);
        }

        //按用户查找
        if(isset($query_limit["user"]))
        {
            $query = $query->where("info_user","=",$query_limit["user"]);
        }


        //审核筛选
        if(isset($query_limit["check"]))
        {
            if($query_limit["check"] == false)
            {

                $query = $query->where("info_check","=",false);
            }
            else
            {
                $query = $query->where("info_check","=",$query_limit["check"]);
            }

        }

        //如果当前系统是以物管的角色登录，那查询出来的用户真实信息也应该是此物管所管理的小区范围内的用户信息
        $query = $query->leftJoin("user","info_user","=","user_id");

        if(isset($query_limit['admin_community_group']))
        {
            $query = $query->leftJoin("user_community_group","user_id","=","re_user");
            $query = $query->where("re_community_group","=",$query_limit['admin_community_group']);
        }

        //计算出符合查询条件的总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始条数
        if ( isset($query_limit["start"]))
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

    /**
     * 添加一条记录
     * @param $dataArray 数组
     * @return bool
     */
    public static function  add($dataArray)
    {

        //dump($dataArray);
        //不能有相同的身份证号
        /*$record = DB::table('user_trueinfo')->where("info_ic_id","=",$dataArray["info_ic_id"])
            ->first();*/
        if(true)
        {
            $dataArray["info_create_time"] = date('Y-m-d H:i:s');
            $dataArray["info_update_time"] = date('Y-m-d H:i:s');
            $dataArray["info_check"] = false;
            $return = DB::table("user_trueinfo")->insertGetId($dataArray);
            if($return)
            {
                return $return;
            }
            return false;
        }
        return false;
    }

    /**
     * 根据id删除一条记录
     * @return bool
     */
    public function delete()
    {

        $info_id = $this->info_id;
        //判断是否存在
        $record = DB::table('user_trueinfo')->where("info_id", "=", $info_id)->first();
        if ($record) {
            //存在，再删
            $return = DB::table('user_trueinfo')->where("info_id", "=", $info_id)->delete();
            if ($return) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 更新一条记录
     * @param $dataArray 数组
     * @return bool
     */
    public function update($dataArray)
    {
        $info_id = $this -> info_id;
        $dataArray["info_update_time"] = date('Y-m-d H:i:s');
        $return = DB::table("user_trueinfo")->where("info_id", "=", $info_id)->update($dataArray);
        if($return >= 0)
        {
            return true;
        }
        return false;
    }

    /**
     * 设定已审查
     * @return bool
     */
    public function setChecked()
    {
        if($this->update(["info_check"=>true]))
        {
            //审查用户成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."审查用户成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "审查用户";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //审查用户失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."审查用户失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "审查用户";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

}