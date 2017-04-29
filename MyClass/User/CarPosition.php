<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 20:09
 */

namespace MyClass\User;

use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;
use MyClass\User\InputCarPosition;
use MyClass\Serve\Now;

class CarPosition
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
        $this->info = DB::table("car_position")->where("position_id","=",$this->position_id)->first();
        if(isset($this->info->position_community))
        {
            $community = new Community($this->info->position_community);
            $this->info->position_comunity_address = $community->info->community_address;
        }
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
        * |-admin_community_group   按照物业公司筛选
        * |*/
        /*
        * 返回值 $return_data
        * |-status 是否成功
        * |-message 消息
        * |-data  查询结果值
        * |-total 符合查询条件的总条数，不是数据库总条数
        */
        $query = DB::table("car_position");

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

        //按用户查找
        if(isset($queryLimit["user"]))
        {
            $query = $query->where("position_user","=",$queryLimit["user"]);
        }

        //按是否审核查找
        if(isset($queryLimit["check"]))
        {
            if($queryLimit["check"] == false)
            {

                $query = $query->where("position_check","=",false);
            }
            else
            {
                $query = $query->where("position_check","=",$queryLimit["check"]);
            }
        }

        //按id查找某条记录
        if(isset($queryLimit["id"]))
        {
            $query = $query->where("position_id","=",$queryLimit["id"]);
        }
        //只允许显示本物业公司的车位信息
        $query = $query->leftJoin("community","position_community","=","community_id");
        if(isset($queryLimit['admin_community_group']))
        {
            $query = $query->where("community_group", "=", $queryLimit['admin_community_group']);
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

        foreach($data as $key => $value)
        {
            if($userData = DB::table("user")->where("user_id","=",$value->position_user)->first())
            {
                $data[$key]->username = $userData->user_username;
            }
        }
        foreach($data as $key => $value)
        {
            if($communityData = DB::table("community")->where("community_id","=",$value->position_community)->first())
            {
                $data[$key]->community_name = $communityData->community_name;
                $data[$key]->community_address = $communityData->community_address;
            }
        }
        //遍历数组，把里面的now字段转化为日期
        foreach($data as $key => $value)
        {
            if(isset($data[$key]->position_now))
            {
                $data[$key]->position_now = Now::getMapping($data[$key]->position_now);
            }
        }

        /*foreach($data as $key => $value)
        {
            if(isset($data[$key]->position_input))
            {
                $inputPosition = new InputCarPosition($data[$key]->position_input);
                $data[$key]->position_intro = $inputPosition->info->position_intro;
            }
        }*/

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }

    /*
     * 增加车位
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray["position_create_time"] = date('Y-m-d H:i:s');
        $dataArray["position_update_time"] = date('Y-m-d H:i:s');

        $id = DB::table("car_position")->insertGetId($dataArray);  //insert()正确
        if($id)
        {
            //添加车辆信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加车位信息";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加车位信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $id;
        }
        return false;
    }

    /*
     * 根据id删除
     * @return bool
     */
    public function delete()
    {

        $position_id = $this->position_id;
        //判断是否存在
        $record = DB::table('car_position')->where("position_id", "=", $position_id)->first();
        if($record) {
            //存在，再删
            $return = DB::table('car_position')->where("position_id", "=", $position_id)->delete();
            if ($return)
            {
                //删除车辆信息成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除车位信息";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "删除车位信息";
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

    /*
     * 根据id更新
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $position_id = $this -> position_id;
        $dataArray["position_update_time"] = date('Y-m-d H:i:s');
        $return = DB::table("car_position")->where("position_id", "=", $position_id)->update($dataArray);
        if($return > 0)
        {
            $this->getInfo();
            return true;
        }
        return false;
    }

    /*
     * 用户选择车位后录入
     * @param $position_id
     * @return bool
     */
    public static function inputCar($position_id)
    {
        //根据传过来的车位ID查询该车位的详细信息，然后将重要的信息拷贝到car_position表中
        $inputCarPosition = new InputCarPosition($position_id);
        //判断一下该车位是否已经卖出，【也就是被用户占用】
        if($inputCarPosition->info->position_use == 0)
        {
            $addData['position_tax'] = $inputCarPosition->info->position_tax;
            $addData['position_community'] = $inputCarPosition->info->position_community;
            $addData['position_cantax_time'] = config("my_config.house_cantax_time");
            $addData['position_now'] = Now::getNow();
            $addData['position_detail'] = $inputCarPosition->info->position_detail;
            //根据小区id找到小区名字再拷贝到position_where字段里面
            $community = new Community($inputCarPosition->info->position_community);
            $addData['position_where'] = $community->info->community_id;
            $addData['position_user'] = session("user.user_id");
            $addData['position_input'] = $inputCarPosition->info->position_id;
            //开始插入车位信息
            if($id = CarPosition::add($addData))
            {
                //如果插入成功，就把input_car_position表中的position_use跟新为已卖出，设置为该车位的ID
                if($inputCarPosition->update(["position_use"=>$id]))
                {
                    return new CarPosition($id);
                }
                else
                {
                    return false;
                }
            }
            return false;
        }
        else
        {
            return false;
        }
    }

    //审核车位
    public function setCarPositionChecked()
    {
        if($this->update(["position_check"=>true]))
        {
            return true;
        }
        return false;
    }

    //这里根据position_input连表查询得到相关两个地段
    public static function getInputByPositionId($position_input)
    {
        $res = DB::table("input_car_position")->where("position_id","=",$position_input)->first();
        if($res)
        {
            return $res;
        }
        else
        {
            return false;
        }
    }
}