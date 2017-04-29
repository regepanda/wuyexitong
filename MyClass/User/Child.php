<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/25
 * Time: 21:10
 */

namespace MyClass\User;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;
use MyClass\User\Course;

class Child
{
    public $child_id;
    public $info;
    /*
     * 构造函数
     * @param $child_id
     */
    public function __construct($child_id)
    {
        $this->child_id = $child_id;
        $this->getInfo();
    }

    /*
     * 当条小孩儿信息
     *
     */
    public function getInfo()
    {
        $this->info = DB::table("child")->where("child_id","=",$this->child_id)->first();
        if(isset($this->info->child_course))
        {
            $course = new Course($this->info->child_course);
            $this->info->course_id = $course->info->course_id;
            $this->info->course_name = $course->info->course_name;
            $this->info->course_school = $course->info->course_school;
            $this->info->course_position = $course->info->course_position;
            $this->info->course_date = $course->info->course_date;
            $this->info->course_monitor = $course->info->course_monitor;
        }
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /*
    * select child
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
        $query = DB::table("child");
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
                $query = $query->orderBy('child_id','desc');
            }
            else
            {
                $query = $query->orderBy('child_id');
            }
        }

        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("child_id","=",$queryLimit['id']);
        }
        //关联到用户、房子、小区【如果是物业公司管理员登录，只能显示本物业公司下面的孩子信息】
        $query = $query->leftJoin("user","child_user","=","user_id")
                       ->leftJoin("user_community_group","user_id","=","re_user");
        if(isset($queryLimit["admin_community_group"]))
        {
            $query = $query->where("re_community_group","=",$queryLimit["admin_community_group"]);
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

        //分页
        if(isset($queryLimit['paginate']))
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }

        //关联到课程
        foreach($data as $key => $value)
        {
            $courseData = DB::table("course")->where("course_id","=",$value->child_course)->first();
            if($courseData != null)
            {
                $data[$key]->course_name = $courseData->course_name;
                $data[$key]->course_school = $courseData->course_school;
                $data[$key]->course_position = $courseData->course_position;
                $data[$key]->course_date = $courseData->course_date;
                $data[$key]->course_monitor = $courseData->course_monitor;
                if(isset($courseData->course_monitor))
                {
                    $monitor = new Monitor($courseData->course_monitor);
                    $data[$key]->monitor_id = $monitor->info->monitor_id;
                    $data[$key]->monitor_name = $monitor->info->monitor_name;
                }
            }
        }
        //Return the data
        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * add child
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray['child_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['child_update_time'] = date("Y-m-d:H-i-s");
        if($id = DB::table("child")->insertGetId($dataArray))
        {
            //添加儿童信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加儿童信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $id;
        }
        else
        {
            //添加儿童信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加儿童信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "添加儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     * update child
     * @access public
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $dataArray['child_update_time'] = date("Y-m-d:H-i-s");
        if(DB::table("child")->where("child_id","=",$this->child_id)->update($dataArray))
        {
            //更新儿童信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新儿童信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "更新儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            //返回更新后的信息
            return $this->getInfo();
        }
        else
        {
            //更新儿童信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新儿童信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "更新儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     *delete billboard
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("child")->where("child_id","=",$this->child_id)->delete())
        {
            //删除儿童信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除儿童信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "删除儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //删除儿童信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除儿童信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "删除儿童信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }
}