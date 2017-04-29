<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 15:38
 */

namespace MyClass\User;

use Illuminate\Support\Facades\DB;

class InputHouseData
{
    public $data_id;
    public $info;

    /*
     * 构造函数
     * @param $child_id
     */
    public function __construct($data_id)
    {
        $this->data_id = $data_id;
        $this->getInfo();
    }

    /*
     * 当条小孩儿信息
     *
     */
    public function getInfo()
    {
        $this->info = DB::table("input_house_data")->where("data_id","=",$this->data_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /*select
     * @param $queryLimit
     * @return mixed
     */
    public static function select($queryLimit)
    {
        /*
        * $queryLimit
        * |-start  起始
        * |-num   每页条数
        * |-sort   排序
        * |-desc   是否逆转排序即倒序(默认正序)
        * |-data_parent  根据等级id查询楼、单元、房号
        * |-data_community  小区id
        * |-data_use  是否已经添加过
        * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-total    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构,第二位是jSON
         *
         */
        $query = DB::table("input_house_data");
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
                $query = $query->orderBy('data_id','desc');
            }
            else
            {
                $query = $query->orderBy('data_id');
            }
        }
        if(isset($queryLimit['data_top']))
        {
            $query = $query->where("data_top","=",$queryLimit['data_top']);
        }
        //根据等级id查找【$data_parent】
        if(isset($queryLimit['data_parent']))
        {
            $query = $query->where("data_parent","=",$queryLimit['data_parent']);
        }

        //小区限制
        if(isset($queryLimit['data_community']))
        {
            $query = $query->where("data_community","=",$queryLimit['data_community']);
        }

        //Calculate the total number
        $num_query  = clone $query;//这里克隆一个，不用原来的了
        $returnData["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num ;

        //The starting page number
        if (isset($queryLimit["start"]))
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
     * add
     * @access public
     * @return bool
     */
    public function add($data_self_id,$data_value,$data_parent,$data_community,$data_tax,$data_detail)
    {
        $dataArray['data_self_id'] = $data_self_id;
        $dataArray['data_value'] = $data_value;
        $dataArray['data_parent'] = $data_parent;
        $dataArray['data_community'] = $data_community;
        $dataArray['data_tax'] = $data_tax;
        $dataArray['data_detail'] = $data_detail;
        $dataArray['data_use'] = 0;
        //如果录入的是栋，他就为顶层记录，data_top设置为0
        if($data_parent == null)
        {
            $dataArray['data_top'] = 0;
        }
        $id = DB::table("input_house_data")->insertGetId($dataArray);
        if($id)
        {
            new InputHouseData($id);
            return $id;
        }
        else
        {
            return false;
        }
    }

    /*
     * 主要在用户选择房子录入后需要跟新position_use为已选择，设置为该房子ID
     * @access public
     * @return bool
     */
    public function update($dataArray)
    {
        if(DB::table("input_house_data")->where("data_id","=",$this->data_id)->update($dataArray))
        {
            $this->getInfo();
            return true;
        }
        return false;
    }
}