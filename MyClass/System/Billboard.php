<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/25
 * Time: 21:19
 */

namespace MyClass\System;

use Illuminate\Support\Facades\DB;

class Billboard
{
    public $billboard_id;
    public $info;
    /*
     * 构造函数
     * @param $message_id
     */
    public function __construct($billboard_id)
    {
        $this->billboard_id = $billboard_id;
        $this->getInfo();
    }

    /*
     * 当条公告牌信息
     * @param $message_id
     */
    public function getInfo()
    {
        $this->info = DB::table("billboard")->where("billboard_id","=",$this->billboard_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /*
    * select billboard
    * @param $queryLimit
    * @return $returnData
    */
    public static function select($queryLimit)
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
         * |-admin_community_group  根据小区查找【只显示当前小区的一些公告信息】
         * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构
         *
         */

        $query = DB::table("billboard");
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
                $query = $query->orderBy('billboard_id','desc');
            }
            else
            {
                $query = $query->orderBy('billboard_id');
            }
        }

        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("billboard_id","=",$queryLimit['id']);
        }

        //如果当前系统是以物管的角色登录，那查询的公告信息也应该是此物管所管理的小区的住户的
        $query = $query->leftJoin("community","billboard_community","=","community_id");
        if(isset($queryLimit['admin_community_group']))
        {
            $query = $query->where("community_group","=",$queryLimit["admin_community_group"]);
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
     * add billboard
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray['billboard_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['billboard_update_time'] = date("Y-m-d:H-i-s");
        if($id = DB::table("billboard")->insertGetId($dataArray) != false)
        {
            //添加公告成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加公告信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加公告信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $id;
        }
        else
        {
            //添加公告失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加公告信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "添加公告信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /*
     * update billboard
     * @access public
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $dataArray['billboard_update_time'] = date("Y-m-d:H-i-s");
        if(DB::table("billboard")->where("billboard_id","=",$this->billboard_id)->update($dataArray))
        {
            //更新公告成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新公告信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "更新公告信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            $this->getInfo();
            return true;
        }
        else
        {
            //更新公告失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."更新公告信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "更新公告信息";
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
        if(DB::table("billboard")->where("billboard_id","=",$this->billboard_id)->delete())
        {
            //删除公告成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除公告信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "删除公告信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //删除公告失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除公告信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "删除公告信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }
}