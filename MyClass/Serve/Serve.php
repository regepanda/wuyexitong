<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/6
 * Time: 22:18
 */

namespace MyClass\Serve;

use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;

/**
 * Class Serve
 * @package MyClass\Serve
 */
class Serve implements  DatabaseModel
{
    /**
     *服务Id
     * @var
     */
    public $class_id;
    /**
     * 服务的一条记录
     * @var
     */
    public $info;

    /**
     * 服务的构造函数
     * @param $class_id
     */
    public function __construct($class_id)
    {
        $this->class_id = $class_id;
        $this->getInfo();
    }

    /**
     * 获取服务的一条记录，初始化成员变量$info
     * @return bool
     */
    public function getInfo()
    {
        $this->info = DB::table("class")->where("class_id","=",$this->class_id)->first();
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
    * 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
 * 参数 $queryLimit
 * |-start  起始条数
 * |-num   每页条数
 * |-sort   排序（根据自己的传入的字段，依照此字段排序）
 * |-desc   是否逆转排序即倒序(默认正序)
 * |-paginate  分页（使用laravel自动分页，这里指定数值）
 * |-id      按照字段id值，筛选某条记录
 * |-newServe 为true，筛选新增服务
 * |*/
    /*
    * $return_data
    * |-status 是否成功
    * |-message 消息
    * |-data  查询结果值
    * |-total 符合查询条件的总条数，不是数据库总条数
     */
    /**
     * @param $query_limit
     * @return mixed
     */
    public static function select($query_limit)
    {
        $query = DB::table("class");


        //排序
        if(isset($query_limit["sort"])  )  //自定义字段排序
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
                $query = $query->orderBy("class_id","desc");
            }
            else
            {
                $query = $query->orderBy("class_id");
            }
        }


        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("class_id","=",$query_limit["id"]);
        }

        //按新增服务查找
        if(isset($query_limit["newServe"]) && $query_limit["newServe"] == true)
        {
            $query = $query->where("class_origin","=",false);
        }

        //计算出总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;


        //是否分页
        if(isset($query_limit["paginate"]))
        {
            $data = $query->paginate($query_limit["paginate"]);
        }
        else
        {
            $data = $query->get();
        }

        $originServer = [
            ["class_id"=>"4","class_name"=>"钟点工"],
            ["class_id"=>"5","class_name"=>"陪护工"],
            ["class_id"=>"6","class_name"=>"保洁服务"],
            ["class_id"=>"7","class_name"=>"月嫂"],
            ["class_id"=>"8","class_name"=>"管道疏通"],
            ["class_id"=>"9","class_name"=>"开锁服务"],
            ["class_id"=>"10","class_name"=>"水电修理"],
            ["class_id"=>"11","class_name"=>"家电修理"],
            ["class_id"=>"12","class_name"=>"房屋修理"],
            ["class_id"=>"13","class_name"=>"定制需求"],
            ["class_id"=>"16","class_name"=>"四点半学校"],
            ["class_id"=>"17","class_name"=>"投诉建议"]
        ];
        foreach($originServer as $originSingle)
        {
            $originSingle["class_intro"] = null;
            $originSingle["class_origin"] = true;
            $originSingle = json_encode($originSingle);
            $originSingle = json_decode($originSingle);
            $data[] = $originSingle;
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }

    /**
     * 新增服务（默认添加class_origin为false）
     * @param $inputData
     * @return bool
     */
    public static function add($inputData)
    {
        $inputData["class_origin"] = false;

        $id = DB::table("class")->insertGetId($inputData);
        if($id)
        {
            //添加服务信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加服务信息";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加服务信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $id;
        }
        return false;
    }

    /**
     * 更改服务（只能更改新增的服务）
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        //只能改新增加的服务(即class_origin=false)
         if($this->info->class_origin == true)
        {
            return false;
        }
        $class_id = $this -> class_id;
        $return = DB::table("class")->where("class_id", "=", $class_id)->update($dataArray);
        if($return > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * 删除服务（只能删除新增的服务）
     * @return bool
     */
    public function delete()
    {
        $class_id = $this->class_id;
        //判断是否存在
        $record = DB::table('class')->where("class_id", "=", $class_id)->first();
        if ($record) {
            //只能删新增的
            if($record -> class_origin == false) {
                //存在，再删
                $return = DB::table('class')->where("class_id", "=", $class_id)->delete();
                if ($return) {
                    //删除服务信息成功后添加日志
                    $message = date("Y-m-d H:i:s") . session("admin_admin_nickname") . "删除服务信息";
                    $admin = session("admin.admin_id");
                    $level = DBLog::INFO;
                    $otherData = "删除服务信息";
                    DBLog::adminLog($message, $admin, $level, $otherData);
                    return true;
                }
            }
        }
        return false;
    }

}