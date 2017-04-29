<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/9
 * Time: 16:53
 */

namespace MyClass\Admin;
use Illuminate\Support\Facades\DB;

class CommunityGroup
{
    public $group_id;
    public $info;

    /*
     * 构造函数
     * @param $group_id
     */
    public function __construct($group_id)
    {
        $this->group_id = $group_id;
        $this->getInfo();
    }
    /*
     * 当条community_group信息
     */
    public function getInfo()
    {
        $this->info = DB::table("community_group")->where("group_id","=",$this->group_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }
    /*
    * select community_group信息
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
         * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构
         *
         */
        $query = DB::table("community_group");
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
                $query = $query->orderBy('group_id','desc');
            }
            else
            {
                $query = $query->orderBy('group_id');
            }
        }
        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("group_id","=",$queryLimit['id']);
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
    * add community_group
    * @access public
    * @param $dataArray
    * @return bool
    */
    public static function add($dataArray)
    {
        if($id = DB::table("community_group")->insertGetId($dataArray))
        {
            new CommunityGroup($id);
            return $id;
        }
        else
        {
            return false;
        }
    }

    /*
     * update community_group
     * @access public
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        if(DB::table("community_group")->where("group_id","=",$this->group_id)->update($dataArray))
        {
            $this->getInfo();
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     *delete community_group
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("community_group")->where("group_id","=",$this->group_id)->delete())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}