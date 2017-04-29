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
use MyClass\Serve\Now;
use MyClass\System\DBLog;
use MyClass\User\Community;
use MyClass\User\InputHouseData;
use MyClass\User\UCRelation;

/**
 * Class House
 * @package MyClass\User
 */
class House implements DatabaseModel
{
    //数据库的一条记录
    public $info;
    /**
     * @var
     */
    private $house_id;

    /**
     * @param $house_id
     */
    public function __construct($house_id)
    {
        $this->house_id = $house_id;
        $this -> getInfo();
    }

    //获取一条记录
    public function getInfo()
    {
        $this->info = DB::table("house")->where("house_id","=",$this->house_id)->first();
        //关联到input_house_data表
        if($this->info->house_where != null)
        {
            $inputHouse = new InputHouseData($this->info->house_where);
            $this->info->house_address = $inputHouse->info->house_address;
            $this->info->house_area = $inputHouse->info->house_area;
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

    /**
     * 快速添加
     * @param $area
     * @param $address
     * @param $user
     * @return bool|House
     */
    public static function addHouse($area,$address,$user,$community = null)
    {
        $model = House::add([
            "house_area"=>$area,
            "house_address"=>$address,
            "house_user"=>$user,
            "house_community" => $community
        ]);
        if($model!=false)
        {
            //添加房产信息成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."添加房产信息";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加房产信息";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return new House($model);
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
      * |-user   用户筛选（如果涉及到用户,传入用户id）
      * |-desc   是否逆转排序即倒序(默认正序)
      * |-paginate  分页（使用laravel自动分页，这里指定数值）
      * |-id      按照字段id值，筛选某条记录
      * |-admin_community_group   根据物业公司筛选
      * |*/
    /*
    * 返回值 $return_data
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
        $query = DB::table("house");
        //排序
        if (isset($query_limit["sort"]))
        {
            if (isset($query_limit["desc"]) && true == $query_limit["desc"])
            {
                $query = $query->orderBy($query_limit["sort"], "desc");
            }
            else
            {
                $query = $query->orderBy($query_limit["sort"]);
            }

        }
        else
        {
            if (isset($query_limit["desc"]) && true == $query_limit["desc"]) {
                $query = $query->orderBy("house_id", "desc");
            }
            else
            {
                $query = $query->orderBy("house_id");
            }
        }

        //用户查找
        if (isset($query_limit["user"]))
        {
            $query = $query->where("house_user", "=", $query_limit["user"]);
        }

        //按照house_where查找
        if(isset($query_limit["house_where"]))
        {
            $query = $query->where("house_where","=",$query_limit["house_where"]);
        }

        //按是否审核查找
        if(isset($query_limit["check"]))
        {
            if($query_limit["check"] == false)
            {

                $query = $query->where("house_check","=",false);
            }
            else
            {
                $query = $query->where("house_check","=",$query_limit["check"]);
            }

        }

        //按照id查找
        if (isset($query_limit["id"]))
        {
            $query = $query->where("house_id", "=", $query_limit["id"]);
        }

        //连接到用户
        if (isset($query_limit["joinUser"]) && $query_limit["joinUser"] == true)
        {
            $query = $query->leftJoin("user", "house_user", "=", "user_id");
        }

        //关联到input_house_data
        $query = $query->leftJoin("input_house_data","house_where","=","data_id");

        //如果当前系统是以物管的角色登录，那查询出来的房屋信息也应该是此物管所管理的小区范围内的房屋
        $query = $query->leftJoin("user", "house_user", "=", "user_id");

        if(isset($query_limit['admin_community_group']))
        {
            $query = $query->leftJoin("user_community_group", "user_id", "=", "re_user");
            $query = $query->where("re_community_group","=",$query_limit['admin_community_group']);
        }
        //总页数
        $num_query = clone $query;
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始页
        if (isset($query_limit["start"]))
        {
            $query = $query->skip($query_limit["start"]);
        }

        //每页多少条记录
        if (isset($query_limit["num"]))
        {
            if ($query_limit["num"] == 0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "获取信息失败";
                $return_data["data"] = null;
                return $return_data;
            }

            $query = $query->take($query_limit["num"]);
        }
        else
        {
            $query = $query->take(config("my_config.default_num_page"));     //鑷繁澧炲姞锛岄粯璁?5鏉?
            $query_limit["num"] = config("my_config.default_num_page");     //
        }
        if (isset($query_limit["paginate"]))
        {

            $data = $query->simplePaginate($query_limit["paginate"]);
        }
        else
        {
            $data = $query->get();
        }

        foreach($data as $key => $value)
        {
            if($data[$key]->data_community != null)
            {
                $community = new Community($data[$key]->data_community);
                $data[$key]->community_name = $community->info->community_name;
                $data[$key]->house_community = $community->info->community_id;
            }
            else
            {
                $data[$key]->community_name = null;
                $data[$key]->house_community = null;
            }
        }

        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;
        return $return_data;
    }

    /*
     * 这个函数用来去除重复的数据
     * @param $data  传过来的$data
     */
    /*public static function onlyHouse(&$data)
    {
        //每丢一次数据到$newData中，就把该条数据的$house_id放入$houseIds中
        $houseIds = [];
        //遍历数组，判断每次遍历到的house_id在数组$houseIds里面有没有，没有就把数据加入$newData
        $newData = [];
        foreach($data as $key => $value)
        {
            if(!in_array($value->house_id,$houseIds))
            {
                $newData[] = $value;
                $houseIds[] = $value->house_id;
            }
        }
        return $newData;
    }*/

    /**
     * 增加一条记录
     * @param $inputData
     * @return bool
     */
    public static function add($inputData)
    {
        $inputData["house_create_time"] = date('Y-m-d H:i:s');
        $inputData["house_update_time"] = date('Y-m-d H:i:s');
        $inputData["house_check"] = false;

        $id = DB::table("house")->insertGetId($inputData);  //insert()正确
        if ($id) {
            return $id;
        }
        return false;
    }

    /**
     * 按照id删除一条记录
     * @return bool
     */
    public function delete()
    {

        $house_id = $this->house_id;
        //判断是否存在
        $record = DB::table('house')->where("house_id", "=", $house_id)->first();
        if ($record) {
            //存在，再删
            $return = DB::table('house')->where("house_id", "=", $house_id)->delete();
            if ($return)
            {
                //删除房产信息成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."删除房产信息";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "删除房产信息";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        $house_id = $this -> house_id;
        $dataArray["house_id"] = $house_id;
        $dataArray["house_update_time"] = date('Y-m-d H:i:s');

        $return = DB::table("house")->where("house_id", "=", $house_id)->update($dataArray);
        if($return > 0)
        {
            $this->getInfo();
            return true;
        }
        return false;
    }

    /**
     * 是否核查过
     * @return bool
     */
    public function isCheck()
    {
        return $this->info->house_check == true;
    }


    /**
     * 是否能缴费
     * @return bool
     */
    public function isCanTax()
    {
        return $this->info->house_can_tax == true;
    }

    /*
     * 设定已检查并且设定每个月的物业费
     * @param null $taxPrice
     * @param null $taxIntro 介绍
     * @return bool
     */
    public function setCheckAndTax($taxPrice = null, $otherData = null)
    {
        $updateData["house_check"] = true;
        if($taxPrice != null)
        {
            $updateData["house_can_tax"] = true;
            $updateInputData["data_tax"] = $taxPrice;
            $updateInputData["data_detail"] = $otherData;

            $inputHouse = new InputHouseData($this->info->house_where);
            $inputHouse->update($updateInputData);
        }
        $r1 = $this->update($updateData);
        if($r1){return true;}else{return false;}
    }

    /*
     * 用户下拉选择房子详细信息后录入
     * @param $data_id //房号id
     * @return
     */
    public static function updateInputHouse($data_id)
    {
        //这里先判断一下此用户是否多次选则同一个房屋
        $result = House::select(["user"=>session("user.user_id"),"house_where"=>$data_id]);
        if($result['data'] != null)
        {
            return false;
        }

        $inputHouseDataFang = new InputHouseData($data_id);
        $fangId = $inputHouseDataFang->info->data_id;

        $houseData['house_where'] = $fangId;
        $houseData['house_user'] = session("user.user_id");
        //找到小区所对应的物业公司建立连接关系
        DB::beginTransaction();
        if($id = House::add($houseData))
        {
            $house = new House($id);
            //绑定物业公司
            $community = new Community($inputHouseDataFang->info->data_community);
            if(UCRelation::userInCommunity(session("user.user_id"),$community->info->community_group) == false)
            {
                UCRelation::addLink(session("user.user_id"),$community->info->community_group);
            }
            DB::commit();
            return $house;
        }
        else
        {
            DB::rollback();
            return false;
        }
    }

    public static function inputHouse($data_id)
    {
        $inputHouseDataFang = new InputHouseData($data_id);
        $houseData['house_tax'] = $inputHouseDataFang->info->data_tax;
        $houseData['house_other_data'] = $inputHouseDataFang->info->data_detail;
        //这里要获取小区的地址community_address
        $community = new Community($inputHouseDataFang->info->data_community);
        //这里获取小区信息如{"id":1,"name":"大地小区"}
        /*$communityData['id'] = $community->info->community_id;
        $communityData['name'] = $community->info->community_name;*/

        $communityAddress = $community->info->community_address;
        $fangValue = $inputHouseDataFang->info->data_value; //获取房名
        $fangId = $inputHouseDataFang->info->data_id;
        //这里获取房子信息如{"id":15,"name":1088}
        /*$fangData['id'] =  $inputHouseDataFang->info->data_id;
        $fangData['name'] = $inputHouseDataFang->info->data_value;*/

        $danYuanId = $inputHouseDataFang->info->data_parent; //获取房子的父id，也就是单元id
        $inputHouseDataDan = new InputHouseData($danYuanId);
        $danYuanValue = $inputHouseDataDan->info->data_value;//获取单元名
        //这里获取单元信息如{"id":3,"name":"二单元"}
        /*$danData['id'] = $inputHouseDataDan->info->data_id;
        $danData['name'] = $inputHouseDataDan->info->data_value;*/

        $louId = $inputHouseDataDan->info->data_parent;//获取单元的父id，也就是楼id
        $inputHouseDataLou = new InputHouseData($louId);
        $louValue = $inputHouseDataLou->info->data_value;
        //这里获取楼栋信息如{"id":1,"name":"1号楼"}
        /*$louData['id'] = $inputHouseDataLou->info->data_id;
        $louData['name'] = $inputHouseDataLou->info->data_value;*/

        //拼接完整地址
        $houseData['house_address'] = $communityAddress.$louValue.$danYuanValue.$fangValue;
        $houseData['house_community'] = $community->info->community_id;
        $houseData['house_cantax_time'] = config("my_config.house_cantax_time");
        $houseData['house_now'] = Now::getNow();//应该映射
        $houseData['house_where'] = $fangId;
        $houseData['house_user'] = session("user.user_id");

        //找到小区所对应的物业公司建立连接关系
        DB::beginTransaction();
        if($id = House::add($houseData))
        {
            /*$returnData[] = $communityData;
            $returnData[] = $fangData;
            $returnData[] = $louData;
            $returnData[] = $danData;*/

            //如果插入成功，就把input_house_data表中的data_use跟新为已卖出，设置为该房子的ID
            $house = new House($id);
            $inputHouseDataFang->update(["data_use"=>$id]);
            //绑定物业公司
            $community = new Community($house->info->house_community);
            if(UCRelation::userInCommunity(session("user.user_id"),$community->info->community_group) == false)
            {
                UCRelation::addLink(session("user.user_id"),$community->info->community_group);
            }
            DB::commit();
            return $house;
        }
        else
        {
            DB::rollback();
            return false;
        }
    }

    //审核房子
    public function setHouseChecked()
    {
        if($this->update(["house_check"=>true]))
        {
            return true;
        }
        return false;
    }

    //在获取房屋的时候需要获取该房屋的小区、楼栋、单元，房号信息
    public function getInputParentData($house_where)
    {
        $inputDataFang = new InputHouseData($house_where);
        $fangData['id'] = $inputDataFang->info->data_id;
        $fangData['name'] = $inputDataFang->info->data_value;

        //获取单元信息
        $danYuanId = $inputDataFang->info->data_parent;
        $inputDataDan = new InputHouseData($danYuanId);
        $danData['id'] = $inputDataDan->info->data_id;
        $danData['name'] = $inputDataDan->info->data_value;

        //获取楼栋信息
        $louId = $inputDataDan->info->data_parent;
        $inputDataLou = new InputHouseData($louId);
        $louData['id'] = $inputDataLou->info->data_id;
        $louData['name'] = $inputDataLou->info->data_value;

        //获取小区信息
        $communityId = $inputDataFang->info->data_community;
        $community = new Community($communityId);
        $communityData['id'] = $community->info->community_id;
        $communityData['name'] = $community->info->community_name;

        $returnData[] = $fangData;
        $returnData[] = $danData;
        $returnData[] = $louData;
        $returnData[] = $communityData;
        return $returnData;
    }
}