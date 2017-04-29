<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/14
 * Time: 16:49
 */

namespace MyClass\User;

use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;

class Monitor
{
    public $monitor_id;
    public $info;
    /*
     * 构造函数
     * @param $monitor_id
     */
    public function __construct($monitor_id)
    {
        $this->monitor_id = $monitor_id;
        $this->getInfo();
    }

    /*
     * 当条监控信息
     *
     */
    public function getInfo()
    {
        $this->info = DB::table("monitor")->where("monitor_id","=",$this->monitor_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }
    /*
    * select monitor
    * @param $queryLimit
    * @return $returnData
    */
    public static function select($queryLimit)
    {
        /*
         * $queryLimit
         * |-start  起始
         * |-num   每页条数
         * |-sort   排序
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
         */
        $query = DB::table("monitor");
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
                $query = $query->orderBy('monitor_id','desc');
            }
            else
            {
                $query = $query->orderBy('monitor_id');
            }
        }

        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("monitor_id","=",$queryLimit['id']);
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

        //Return the data
        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * add monitor
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray['monitor_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['monitor_update_time'] = date("Y-m-d:H-i-s");
        if($id = DB::table("monitor")->insertGetId($dataArray))
        {
            //添加儿童信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加监控成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添监控息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            new Monitor($id);
            return $id;
        }
        else
        {
            //添加儿童信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加监控失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "添监控息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     * update monitor
     * @access public
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $dataArray['monitor_update_time'] = date("Y-m-d:H-i-s");
        if(DB::table("monitor")->where("monitor_id","=",$this->monitor_id)->update($dataArray))
        {
            //更新监控信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新监控成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "更新监控";
            DBLog::adminLog($message, $admin, $level,$otherData);
            //返回更新后的信息
            return $this->getInfo();
        }
        else
        {
            //更新监控信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新监控失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "更新监控";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }
    /*
     *delete monitor
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("monitor")->where("monitor_id","=",$this->monitor_id)->delete())
        {
            //删除儿童信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除监控成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "删除监控";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //删除儿童信息失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除监控失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "删除监控";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

}