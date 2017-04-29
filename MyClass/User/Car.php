<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:34
 */

namespace MyClass\User;

use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;

/*
 * Class Car
 * @package MyClass\User
 */
class Car implements DatabaseModel
{
    /*
     * @var
     */
    private $car_id;

    //数据库的一条记录（一个对象）
    public $info;

    /*
     * @param $car_id
     */
    public function __construct($car_id)
    {
        $this->car_id = $car_id;
        $this -> getInfo();
    }

    //获取一条记录
    public function getInfo()
    {
        $this->info = DB::table("car")->where("car_id","=",$this->car_id)->first();
        if($this->info!=null)
        {
            return $this->info;
        }
        else
        {
            return false;
        }
    }

    /*
     * @param $query_limit
     * @return mixed
     */

    public static function select($query_limit)
    {
        /*
         * 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
        * 参数 $queryLimit
        * |-start  起始条数
        * |-num   每页条数
        * |-sort   排序（根据自己的传入的字段，依照此字段排序）
        * |-user   用户筛选（如果涉及到用户,传入用户id）
        * |-check  审核筛选（如果审核，传true,没审核，传false）
        * |-desc   是否逆转排序即倒序(默认正序)
        * |-paginate  分页（使用laravel自动分页，这里指定数值）
        * |-id      按照字段id值，筛选某条记录
        * |-admin_community_group  根据物业公司筛选
        * |*/
        /*
        * 返回值 $return_data
        * |-status 是否成功
        * |-message 消息
        * |-data  查询结果值
        * |-total 符合查询条件的总条数，不是数据库总条数
        */
        $query = DB::table("car");

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
                $query = $query->orderBy("car_id","desc");
            }
            else
            {
                $query = $query->orderBy("car_id");
            }
        }

        //按用户查找
        if(isset($query_limit["user"]))
        {
            $query = $query->where("car_user","=",$query_limit["user"]);
        }

        //按是否审核查找
        if(isset($query_limit["check"]))
        {
            if($query_limit["check"] == false)
            {

                $query = $query->where("car_check","=",false);
            }
            else
            {
                $query = $query->where("car_check","=",$query_limit["check"]);
            }

        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("car_id","=",$query_limit["id"]);
        }

        //如果当前系统是以物管的角色登录，那查询出来的车辆信息也应该是此物管所管理的小区范围内的车辆
        $query = $query->leftJoin("user","car_user","=","user_id");

        if(isset($query_limit['admin_community_group']))
        {
            $query = $query->leftJoin("user_community_group","user_id","=","re_user");
            $query = $query->where("re_community_group","=",$query_limit['admin_community_group']);
        }
        //计算出总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始条数
        if (isset($query_limit["start"]))
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
            $query = $query->take(10);    //自己增加，默认5条
            $query_limit["num"] = 10;     //
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
     * 增加一条记录
     * @param $inputData
     * @return bool
     */
    public static function add($inputData)
    {

        $inputData["car_create_time"] = date('Y-m-d H:i:s');
        $inputData["car_update_time"] = date('Y-m-d H:i:s');
        $inputData["car_check"] = false;

        $id = DB::table("car")->insertGetId($inputData);  //insert()正确
        if($id)
        {
            //添加车辆信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加车辆信息";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加车辆信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $id;
        }
        return false;
    }

    /**
     * 根据id删除一条记录
     * @return bool
     */
    public function delete()
    {

        $car_id = $this->car_id;
        //判断是否存在
        $record = DB::table('car')->where("car_id", "=", $car_id)->first();
        if ($record) {
            //存在，再删
            $return = DB::table('car')->where("car_id", "=", $car_id)->delete();
            if ($return)
            {
                //删除车辆信息成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除车辆信息";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "删除车辆信息";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

    /**
     * 根据id更新一条记录
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $car_id = $this -> car_id;
        $dataArray["car_update_time"] = date('Y-m-d H:i:s');
        $return = DB::table("car")->where("car_id", "=", $car_id)->update($dataArray);
        if($return > 0)
        {
            return true;
        }
        return false;

    }

    /**
     * 设定已审核
     * @return bool
     */
    public function setCarChecked()
    {
        if($this->update(["car_check"=>true]))
        {
            //审核成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."审核车辆成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "车辆审核";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //审核失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."审核车辆失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "车辆审核";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }



}