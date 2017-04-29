<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 20:58
 */

namespace MyClass\User;

use Illuminate\Support\Facades\DB;

class InputCarPosition
{
    public $position_id;
    //数据库的一条记录（一个对象）
    public $info;

    /*构造函数
     * @param $car_id
     */
    public function __construct($position_id)
    {
        $this->position_id = $position_id;
        $this -> getInfo();
    }

    //获取一条记录
    public function getInfo()
    {
        $this->info = DB::table("input_car_position")->where("position_id","=",$this->position_id)->first();
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
     * @param $queryLimit
     * @return mixed
     */
    public static function select($queryLimit)
    {
        $query = DB::table("input_car_position");
        //排序
        if(isset($queryLimit["sort"]))  //自定义字段排序
        {
            if(isset($queryLimit["desc"]) && true == $queryLimit["desc"])
            {
                $query = $query->orderBy($queryLimit["sort"],"desc");
            }
            else
            {
                $query = $query->orderBy($queryLimit["sort"]);
            }

        }
        else    //按id排序
        {
            if(isset($queryLimit["desc"])  && true==$queryLimit["desc"])
            {
                $query = $query->orderBy("position_id","desc");
            }
            else
            {
                $query = $query->orderBy("position_id");
            }
        }

        //按id查找某条记录
        if(isset($queryLimit["id"]))
        {
            $query = $query->where("position_id","=",$queryLimit["id"]);
        }

        //根据小区查找
        if(isset($queryLimit['community_id']))
        {
            $query = $query->where("position_community","=",$queryLimit['community_id']);
        }

        //计算出总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始条数
        if (isset($queryLimit["start"]))
        { $query = $query->skip($queryLimit["start"]);}

        //每页条数
        if(isset($queryLimit["num"]))
        {
            if($queryLimit["num"]==0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "查询到数据,但num设为了0";
                $return_data["data"] =  null;
                return $return_data;
            }

            $query = $query->take($queryLimit["num"]);
        }
        else
        {
            $query = $query->take(10);     //自己增加，默认5条
            $queryLimit["num"] = 10;     //
        }

        //是否分页
        if(isset($queryLimit["paginate"]))
        {

            $data = $query->simplePaginate($queryLimit["paginate"]);
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

    /*
     * add
     * @access public
     * @return bool
     */
    public function add($position_intro,$position_private,$position_tax,$position_community,$position_detail)
    {
        $dataArray['position_intro'] = $position_intro;
        $dataArray['position_private'] = $position_private;
        $dataArray['position_tax'] = $position_tax;
        $dataArray['position_use'] = 0;
        $dataArray['position_community'] = $position_community;
        $dataArray['position_detail'] = $position_detail;
        $id = DB::table("input_car_position")->insertGetId($dataArray);
        if($id)
        {
            new InputCarPosition($id);
            return $id;
        }
        else
        {
            return false;
        }
    }

    /*
     * 主要在用户选择车位录入后需要跟新position_use为已卖出，设置为0
     * @access public
     * @return bool
     */
    public function update($dataArray)
    {
        if(DB::table("input_car_position")->where("position_id","=",$this->position_id)->update($dataArray))
        {
            $this->getInfo();
            return true;
        }
        return false;
    }

}