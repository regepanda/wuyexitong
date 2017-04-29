<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/25
 * Time: 21:08
 */

namespace MyClass\User;

use MyClass\Admin\CommunityGroup;
use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;


/**
 * Class Community
 * @package MyClass\Community
 */
class Community implements DatabaseModel
{
     /**
     * @var community表单的一条记录（即一个对象）
     */
    public $info;

    /**
     * @var 小区id
     */
    public $community_id;

    /**
     * 构造函数
     * @param $community_id
     */
    public function __construct($community_id)
    {
        $this->community_id = $community_id;
        $this->getInfo();
    }

    /**
     * @return bool|community表单的一条记录
     */
    public function getInfo()
    {
        $this->info = DB::table("community")->where("community_id","=",$this->community_id)->first();
        if($this->info!=null)
        {
            return $this->info;
        }
        else
        {
            return false;
        }
    }

    /* 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
   * 参数  $queryLimit
   * |-start  起始条数
   * |-num   每页条数
   * |-sort   排序,按照哪个字段排序
   * |-community_name 小区名筛选
   * |-desc   是否逆转排序即倒序(默认正序)
   * |-paginate  分页（使用laravel自动分页，这里指定数值），传入要想分页的条数
   * |-id      按照字段id查找
     */

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
        $query = DB::table("community");

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
                $query = $query->orderBy("community_id","desc");
            }
            else
            {
                $query = $query->orderBy("community_id");
            }
        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("community_id","=",$query_limit["id"]);
        }


        //按小区名查找
        if(!empty($query_limit["community_name"]))
        {
            $query = $query -> where("community_name","=",$query_limit["community_name"]);
        }

        if(isset($query_limit["joinUser"]) && $query_limit["joinUser"] == true)
        {
            $query = $query ->leftJoin('user', 'community.community_user', '=', 'user.user_id');

        }

        //如果当前系统是以物管的角色登录，那查询的小区也应该是此物管所管理的小区
        if(isset($query_limit['admin_community_group']))
        {
            $query = $query->leftJoin("community_group","community.community_group","=","community_group.group_id");
            $query = $query->where("community_group","=",$query_limit['admin_community_group']);
        }

        //计算出符合查询条件的总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;
        //起始条数
        if ( isset($query_limit["start"]))
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

        foreach($data as $key => $value)
        {
            if($value->community_group != null)
            {
                $communityGroup = new CommunityGroup($value->community_group);
                $data[$key]->group_name = $communityGroup->info->group_name;
            }
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }


    /**
     * 增加一个小区
     * @param $dataArray
     * @return bool
     */
    public static function  add($dataArray)
    {
        $dataArray["community_create_time"] = date('Y-m-d H:i:s');
        $dataArray["community_update_time"] = date('Y-m-d H:i:s');

          $return = DB::table("community")->insertGetId($dataArray);
            if($return)
            {
                return $return;
            }
            return false;
    }


    /**
     * 按id删除一个小区
     * @return bool
     */
    public function delete()
    {

        $community_id = $this->community_id;
        //判断是否存在
        $record = DB::table('community')->where("community_id", "=", $community_id)->first();
        if ($record) {
            //存在，再删
            $return = DB::table('community')->where("community_id", "=", $community_id)->delete();
            if ($return) {
                return true;
            }
            return false;
        }
        return false;
    }


    /**
     * 按id更新一个小区
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $community_id = $this -> community_id;
        $dataArray["community_update_time"] = date('Y-m-d H:i:s');
        $return = DB::table("community")->where("community_id", "=", $community_id)->update($dataArray);
        if($return >= 0)
        {
            //修改小区成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."修改小区成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "修改小区";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //修改小区失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."修改小区失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "修改小区";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }


}