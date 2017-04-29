<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/2
 * Time: 10:24
 */

namespace MyClass\User;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;


class Course
{
    public $course_id;
    public $info;
    /*
     * 构造函数
     * @param $course_id
     */
    public function __construct($course_id)
    {
        $this->course_id = $course_id;
        $this->getInfo();
    }

    /*
     * 当条课程信息
     *
     */
    public function getInfo()
    {
        $this->info = DB::table("course")->where("course_id","=",$this->course_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /*
    * select course
    * @param $queryLimit
    * @return $returnData
    */
    public static function select()
    {
        /*
         * $queryLimit
         * |-start  起始
         * |-num   每页条数
         * |-class  类别（如果有）
         * |-sort   排序
         * |-search 搜索关键字（按照那边说）（如果有）
         * |-user   用户筛选（如果涉及到用户）（如果有）
         * |-admin  管理员筛选（如果有）
         * |-desc   是否逆转排序即倒序(默认正序)
         * |-paginate  分页（使用laravel自动分页，这里指定数值）
         * |-id       限制id（制定一个固定id）
         * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构
         *
         */
        $query = DB::table("course");
        //是否排序
        if(isset($queryLimit['sort']))
        {
            if(isset($queryLimit['desc']) && $queryLimit['desc'] == true)
            {
                $query = $query->orderBy($queryLimit['desc'],'desc');
            }
            else
            {
                $query = $query->orderBy($queryLimit['sort']);
            }
        }
        else
        {
            if(isset($queryLimit['desc']) && $queryLimit['desc'] == true)
            {
                $query = $query->orderBy('course_id','desc');
            }
            else
            {
                $query = $query->orderBy('course_id');
            }
        }

        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("course_id","=",$queryLimit['id']);
        }

        //Calculate the total number
        $num_query  = clone $query;//这里克隆一个，不用原来的了
        $returnData["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;

        //The starting page number
        if ( isset($queryLimit["start"])  )
        {
            $query = $query->skip($queryLimit["start"]);
        }

        //Number each page
        if(isset($queryLimit["num"]))
        {
            if($queryLimit["num"]==0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "成功获取到数据";
                $return_data["data"] =  null;
                return $return_data;
            }

            $query = $query->take($queryLimit["num"]);
        }
        else
        {

            $query = $query->take(10);
            $queryLimit["num"] = 10;
        }
        $query = $query->leftJoin("monitor","course_monitor","=","monitor_id");
        //分页
        if(isset($queryLimit['paginate']))
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }

        //Return the data
        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * add course
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        if($id = DB::table("course")->insertGetId($dataArray))
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加课程信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return new Course($id);
        }
        else
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加课程信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "添加课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     * update course
     * @access public
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        if(DB::table("course")->where("course_id","=",$this->course_id)->update($dataArray))
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新课程信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "更新课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            //返回更新后的信息
            return $this->getInfo();
        }
        else
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新课程信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "更新课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     *delete course
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("course")->where("course_id","=",$this->course_id)->delete())
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除课程信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "删除课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除课程信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "删除课程信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }
}