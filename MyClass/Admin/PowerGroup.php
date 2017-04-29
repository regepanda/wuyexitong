<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:36
 */

namespace MyClass\Admin;


use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;
use Validator;
use MyClass\Exception\SysValidatorException;
/*
        * $queryLimit
        * |-start  起始
        * |-num   每页条数
        * |-class  类别（如果有）
        * |-sort   排序
        * |-search 搜索关键字（按照那边说）
        * |-user   用户筛选（如果涉及到用户）
        * |-desc   是否逆转排序即倒序(默认正序)
        * |-paginate  分页（使用laravel自动分页，这里指定数值）
        * |-id       限制id（制定一个固定id）
        * |*/

/*
 * $returnData
 * |-status 是否成功
 * |-message 消息
 * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
 * |-data[]   数据 DB返回的二维结构
 *      |-常见数据
 *      |-power[] 权限列表
 */

/**
 * Class PowerGroup
 * @package MyClass\Admin
 */
class PowerGroup implements DatabaseModel
{
    /**
     * @var
     */
    public $group_id;

    /**
     * @param $group_id
     */

    public $info;

    /**
     * @var
     */
    public $power_list;
    /**
     * @var 管理员数组
     */

    public $admin_list;


    /**
     * @param $group_id
     */
    public function __construct($group_id)
    {
        $this->group_id = $group_id;
        $this->syncBaseInfo();
    }

    /**
     * @param $query_limit
     * @return mixed
     */
    public static function select($query_limit)
    {

        $query = DB::table("admin_group");
        //$data['readPower'] = DB::table("admin_power")->where("power_id", "=", 1)->get();

        //排序
        if(  isset($query_limit["sort"])  )  //自定义字段排序
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
                $query = $query->orderBy("group_id","desc");
            }
            else
            {
                $query = $query->orderBy("group_id");
            }
        }

        //计算出总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;

        //起始条数
        if ( isset($query_limit["start"])  )
        { $query = $query->skip($query_limit["start"]);}

        //每页条数
        if(isset($query_limit["num"]))
        {
            if($query_limit["num"]==0)
            {
                $return_data["status"] = true;
                $return_data["message"] = "查询到数据,但num设为了0";
                $return_data["data"] =  null;
                return $return_data;
            }
            $query = $query->take($query_limit["num"]);
        }
        else
        {

            $query = $query->take(config("my_config.default_num_page"));
            $query_limit["num"] = config("my_config.default_num_page");
        }

        if (isset($queryLimit['paginate']))
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }
        foreach($data as $key => $value)
        {
            $group_power = DB::table("admin_group_re_power")
                ->where("group_id","=",$value->group_id)
                ->get();
            $power = [];
            foreach ($group_power as $value)
            {
                //存id而不是存名字
                $power[] = DB::table("admin_power")->where("power_id","=",$value->power_id)->first()->power_id;
            }
            $data[$key]->power = $power;
            //字段叫什么名字在文档上要体现出来
        }

        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        //验证字段
        $errorInfo["required"] = ":attribute必填";
        $errorInfo["max"] = ":attribute不应大于20字节";

        $validator = Validator::make($dataArray,[
            'group_name' => 'required|max:20',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sAdminPowerGroup");
        }

        $groupExisted = DB::table("admin_group")
            ->where("group_name", "=", $dataArray['group_name'])
            ->first(); //查看数据库中是否存在此管理员组
        if ($groupExisted != null)
        {
            return false;
        };
        //不存在，则创建
        //$dataArray["group_name"] = $dataArray['group_name'];
        $dataArray["group_create_time"] = date("Y-m-d:H-i-s");
        $dataArray["group_update_time"] = date("Y-m-d:H-i-s");
        if (DB::table('admin_group')->insert($dataArray))
        {
            //添加管理员组成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功添加了一个管理员权限组";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "管理员添加管理员权限组";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return true;
        }
        else
        {
            //添加管理员组失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员添加管理员权限组失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "管理员添加管理员权限组";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $first = DB::table("admin_group")
            ->where("group_id", "=", $this->group_id)
            ->first();
        if ($first)
        {
            $delete =  DB::table("admin_group")
                ->where("group_id", "=", $this->group_id)
                ->delete();
            if($delete)
            {
                //删除管理员组成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功删除了一个管理员权限组";
                $admin = session("admin_admin_id");
                $level = DBLog::INFO;
                $otherData = "管理员删除管理员权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return true;
            }
            else
            {
                //删除管理员组失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员删除管理员权限组失败";
                $admin = session("admin_admin_id");
                $level = DBLog::ERROR;
                $otherData = "管理员删除管理员权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return false;
            }
        }
        else
        {
            return false;  //不存在该管理员组
        }
    }

    /*
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {
        //验证字段
        $errorInfo["required"] = ":attribute必填";
        $errorInfo["max"] = ":attribute不应大于20字节";

        $validator = Validator::make($dataArray,[
            'group_name' => 'required|max:20',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sAdminPowerGroup");
        }

        $dataArray["group_update_time"] = date("Y-m-d:H-i-s");
        $first = DB::table("admin_group")
            ->where("group_id", "=",$this->group_id)
            ->first();
        if ($first)
        {
            $count =  DB::table("admin_group")
                ->where("group_id", "=", $this->group_id)
                ->update($dataArray);
            if($count)
            {
                //修改管理员组成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功修改了一个管理员权限组";
                $admin = session("admin_admin_id");
                $level = DBLog::INFO;
                $otherData = "管理员修改管理员权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return true;
            }
            else
            {
                //修改管理员组失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员修改管理员权限组失败";
                $admin = session("admin_admin_id");
                $level = DBLog::ERROR;
                $otherData = "管理员修改管理员权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return false;
            }
        }
        else
        {
            return false;  //不存在该管理员组
        }
    }

    /**
     * @return bool
     */
    public function syncBaseInfo()
    {
        $group_id = $this->group_id;
        if (DB::table("admin_group")
                ->where("group_id", "=", $group_id)
                ->first() == null)
        {
            return false;
        }

        //1.拿到该权限组的所有权限,把一个权限组对应多个权限，获取权限放入数组  $this->power_list[]
        $powerData = DB::table("admin_group_re_power")
            ->where("group_id", "=", $group_id)
            ->get();

        foreach ($powerData as $value)
        {
            $this->power_list[] = $value->power_id;
        }

        //2.拿到该权限组的所有管理员  $this->admin_list[]
        $adminData = DB::table("admin")
            ->where("admin_group", "=", $group_id)
            ->get();
        foreach ($adminData as $Data)
        {
            $this->admin_list[] = $Data->admin_id;
        }

        //3.拿到该权限组的基本信息,获取权限组表为group_id的记录   $this->info
        $this->info = DB::table("admin_group")
            ->where("group_id", "=", $group_id)
            ->first();

    }

    /**
     * 向一个权限组增加一个权限
     * @param $power_id_array
     * @return bool
     */
    public function AddPowerToPowerGroup($power_id_array)
    {

        if($power_id_array== null)
        {
            return false;  //没勾选
        }
        foreach ($power_id_array as $data)
        {   //$data为admin_id
            $relationExisted = DB::table("admin_group_re_power")
                ->where("power_id", "=", $data)
                ->where("group_id", "=", $this->info->group_id)
                ->first();
            if ($relationExisted != null)
            {
                return false;  //已经有这个权限组对应的这个权限
            }

            $relation["power_id"] = $data;
            $relation["group_id"] = $this->info->group_id;

            if (DB::table("admin_group_re_power")->insert($relation))
            {
              //  $this->syncBaseInfo();
                //return true;
                continue;
            }
        }
        //向一个权限组增加一个权限成功后添加日志
        $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员向一个权限组增加权限成功";
        $admin = session("admin_admin_id");
        $level = DBLog::INFO;
        $otherData = "向一个权限组增加权限";
        DBLog::adminLog($message,$admin,$level,$otherData);
        return true;
    }

    /**
     * 从一个权限组移除一个权限
     * @param $power_id
     * @return bool
     */
    public function RemovePowerFromPowerGroup($power_id)
    {
        $relationExisted = DB::table("admin_group_re_power")
            ->where("power_id", "=", $power_id)
            ->where("group_id", "=", $this->info->group_id)
            ->delete();
        if ($relationExisted == 0)
        {
            //从一个权限组移除一个权限失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个权限组移除一个权限失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "从一个权限组移除一个权限";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;  //该权限或该权限组不存在
        }
        else
        {
            $this->syncBaseInfo();
            //从一个权限组移除一个权限成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个权限组移除一个权限成功";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "从一个权限组移除一个权限";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return true;
        }
    }


    /**
     * 查询管理权限组详情
     * @return mixed
     */
    public function moreAdminPowerGroup()
    {


        //获取该权限组信息
        $data["GroupData"] = DB::table("admin_group")->where("group_id", "=", $this->group_id)->get();
        //连表查询,获取该权限组的用户信息
        $data['articleAdmin'] = DB::table("admin_group")
            ->leftJoin("admin", "group_id", "=", "admin_group")
            ->where("group_id", "=", $this->group_id)
            ->get();

        $data['checkAdmin'] = DB::table("admin")->get();
        $data['checkPower'] = DB::table("admin_power")->get();
        //连表查询，获取该权限组对应的所有权限
        $AdminPowerGroup = DB::table("admin_group_re_power")
            ->leftJoin("admin_power", "admin_group_re_power.power_id", "=", "admin_power.power_id")
            ->where("group_id", "=", $this->group_id)
            ->get();
        $power_ids = array();
        foreach ($AdminPowerGroup as $value) {
            $power_ids[] = $value->power_id;
        }
        $data['power_ids'] = $power_ids;  //权限id数组
        $data['AdminPowerGroup'] = $AdminPowerGroup;

        return $data;
    }

    /**
     * 从一个管理员组中移除一个管理员
     * @param $admin_id
     * @return bool
     */
    static function removeAdmin($admin_id)
    {
        $adminExisted = DB::table("admin")
            ->where("admin_id", "=", $admin_id)
            ->first();
        if ($adminExisted == null) {
            return false;
        }
        if (DB::table("admin")
            ->where("admin_id", "=", $admin_id)
            ->update(["admin_group" => null])
        )
        {
            //从一个管理员组中移除一个管理员成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个管理员组中移除一个管理员成功";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "从一个管理员组中移除一个管理员";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return true;
        }
        else
        {
            //从一个管理员组中移除一个管理员失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个管理员组中移除一个管理员失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "从一个管理员组中移除一个管理员";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;  //该管理员不存在
        }
    }

    /**
     * 添加管理员到一个管理员组
     * @param $admin_id_array
     * @return bool
     */
    public function addAdmin($admin_id_array)
    {
        if($admin_id_array == null)
        {
            return false;  //没勾选
        }
        foreach ($admin_id_array as $data) {   //$data为admin_id

            $adminExisted = DB::table("admin")
                ->where("admin_id", "=", $data)
                ->first();
            if ($adminExisted == null) {
                return false;
            }

            $adminUpdate = DB::table("admin")
                ->where("admin_id", "=",$data)
                ->update(["admin_group" => $this->info->group_id]);

            if ($adminUpdate >= 0)
            {
               continue;
            }
        }
        //添加管理员到一个管理员组成功后添加日志
        $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员添加管理员到一个管理员组成功";
        $admin = session("admin_admin_id");
        $level = DBLog::INFO;
        $otherData = "添加管理员到一个管理员组";
        DBLog::adminLog($message,$admin,$level,$otherData);
        return true;
    }

    /**
     * 管理员权限获取
     * @access public
     * @return Array 返回权限数组
     */
    public function loadAdminPowerToSession()
    {

        $powerData = DB::table("admin_group_re_power")->where("group_id", "=", $this->info->group_id)->get();

        $returnData = [];
        foreach ($powerData as $data) {
            $returnData[] = $data->power_id;
        }
        session(['admin.admin_power'=>$returnData]);



        return $returnData;
    }

    /*
     * 管理员权限检查
     *
     *
     * @access public
     * @param int powerId 权限ID
     *
     * @return 若成功，返回用户信息，失败返回false；
     */
    public static function checkAdminPower($powerId)
    {
        $powerData = session("admin.admin_power");
        if ($powerData == NULL)
        {
            return false;
        }
        foreach ($powerData as $data)
        {
            if ($data == $powerId)
            {
                return true;
            }
        }
        return false;
    }


}