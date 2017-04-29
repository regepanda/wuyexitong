<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:32
 */

namespace MyClass\User;


use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;
use Validator;
use MyClass\Exception\SysValidatorException;

/**
 * Class PowerGroup
 * @package MyClass\User
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

    public $user_list;


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
        $query = DB::table("user_group");
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
            $group_power = DB::table("user_group_re_power")
                ->where("group_id","=",$value->group_id)
                ->get();
            $power_names = array();
            foreach ($group_power as $value)
            {
                $power_names[] = DB::table("user_power")->where("power_id","=",$value->power_id)->first()->power_id;
            }
            $data[$key]->power = $power_names;
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
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sUserPowerGroup");
        }

        $groupExisted = DB::table("user_group")
            ->where("group_name", "=", $dataArray['group_name'])
            ->first(); //查看数据库中是否存在此管理员组
        if ($groupExisted != null)
        {
            return false;
        };
        //不存在，则创建
        $dataArray["group_create_time"] = date("Y-m-d:H-i-s");
        $dataArray["group_update_time"] = date("Y-m-d:H-i-s");
        if (DB::table('user_group')->insert($dataArray))
        {
            //添加用户组成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功添加了一个用户权限组";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "管理员添加用户权限组";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return true;
        }
        else
        {
            //添加用户组失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员添加用户权限组失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "管理员添加用户权限组";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $first = DB::table("user_group")
            ->where("group_id", "=", $this->group_id)
            ->first();
        if ($first)
        {
            $delete =  DB::table("user_group")
                ->where("group_id", "=", $this->group_id)
                ->delete();
            if($delete)
            {
                //删除用户组成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功删除了一个用户权限组";
                $admin = session("admin_admin_id");
                $level = DBLog::INFO;
                $otherData = "管理员删除用户权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return true;
            }
            else
            {
                //删除用户组失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员删除用户权限组失败";
                $admin = session("admin_admin_id");
                $level = DBLog::ERROR;
                $otherData = "管理员删除用户权限组";
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
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sUserPowerGroup");
        }

        $dataArray["group_update_time"] = date("Y-m-d:H-i-s");
        $first = DB::table("user_group")
            ->where("group_id", "=",$this->group_id)
            ->first();
        if ($first)
        {
            $count =  DB::table("user_group")
                ->where("group_id", "=", $this->group_id)
                ->update($dataArray);
            if($count)
            {
                //修改用户组成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员成功修改了一个用户权限组";
                $admin = session("admin_admin_id");
                $level = DBLog::INFO;
                $otherData = "管理员修改用户权限组";
                DBLog::adminLog($message,$admin,$level,$otherData);
                return true;
            }
            else
            {
                //修改用户组失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员修改用户权限组失败";
                $admin = session("admin_admin_id");
                $level = DBLog::ERROR;
                $otherData = "管理员修改用户权限组";
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
        if (DB::table("user_group")
                ->where("group_id", "=", $group_id)
                ->first() == null)
        {
            return false;
        }

        //1.拿到该权限组的所有权限,把一个权限组对应多个权限，获取权限放入数组  $this->power_list[]
        $powerData = DB::table("user_group_re_power")
            ->where("group_id", "=", $group_id)
            ->get();

        foreach ($powerData as $value)
        {
            $this->power_list[] = $value->power_id;
        }

        //2.拿到该权限组的所有管理员  $this->user_list[]
        $userData = DB::table("user")
            ->where("user_group", "=", $group_id)
            ->get();
        foreach ($userData as $Data)
        {
            $this->user_list[] = $Data->user_id;
        }

        //3.拿到该权限组的基本信息,获取权限组表为group_id的记录   $this->info
        $this->info = DB::table("user_group")
            ->where("group_id", "=", $group_id)
            ->first();

    }

    /**
     * 向一个权限组中添加一个权限
     * @param $power_id_array
     * @return bool
     */
    public  function AddPowerToUserPowerGroup($power_id_array)
    {
        if($power_id_array== null)
        {
            return false;  //没勾选
        }
        foreach ($power_id_array as $data)
        {   //$data为user_id

            $relationExisted = DB::table("user_group_re_power")
                ->where("power_id", "=", $data)
                ->where("group_id", "=", $this->info->group_id)
                ->first();
            if ($relationExisted != null)
            {
                return false;  //已经有这个权限组对应的这个权限
            }

            $relation["power_id"] = $data;
            $relation["group_id"] = $this->info->group_id;
            if (DB::table("user_group_re_power")->insert($relation))
            {
              continue;
            }
        }
        //向一个权限组增加一个权限成功后添加日志
        $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员向一个用户权限组增加权限成功";
        $admin = session("admin_admin_id");
        $level = DBLog::INFO;
        $otherData = "向一个用户权限组增加权限";
        DBLog::adminLog($message,$admin,$level,$otherData);
        return true;
    }

    /**
     * 从一个权限组中移除一个权限
     * @param $power_id
     * @return bool
     */
    public function RemovePowerFromPowerGroup($power_id)
    {
        $relationExisted = DB::table("user_group_re_power")
            ->where("power_id", "=", $power_id)
            ->where("group_id", "=", $this->info->group_id)
            ->delete();
        if ($relationExisted == 0)
        {
            //从一个用户权限组移除一个权限失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个用户权限组移除一个权限失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "从一个用户权限组移除一个权限";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;  //该权限或该权限组不存在
        }
        else
        {
            $this->syncBaseInfo();
            //从一个用户权限组移除一个权限成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个用户权限组移除一个权限成功";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "从一个用户权限组移除一个权限";
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
        $data["GroupData"] = DB::table("user_group")->where("group_id", "=", $this->group_id)->get();
        //连表查询,获取该权限组的用户信息
        $data['articleAdmin'] = DB::table("user_group")
            ->leftJoin("user", "group_id", "=", "user_group")
            ->where("group_id", "=", $this->group_id)
            ->get();

        $data['checkUser'] = DB::table("user")->get();
        $data['checkPower'] = DB::table("user_power")->get();
        //连表查询，获取该权限组对应的所有权限
        $UserPowerGroup = DB::table("user_group_re_power")
            ->leftJoin("user_power", "user_group_re_power.power_id", "=", "user_power.power_id")
            ->where("group_id", "=", $this->group_id)
            ->get();
        $power_ids = array();
        foreach ($UserPowerGroup as $value) {
            $power_ids[] = $value->power_id;
        }
        $data['power_ids'] = $power_ids;  //权限id数组
        $data['UserPowerGroup'] = $UserPowerGroup;

        return $data;
    }

    /**
     * 从一个用户组中移除一个用户
     * @param $user_id
     * @return bool
     */
    static function removeUser($user_id)
    {
        $userExisted = DB::table("user")
            ->where("user_id", "=", $user_id)
            ->first();
        if ($userExisted == null) {
            return false;
        }
        if (DB::table("user")
            ->where("user_id", "=", $user_id)
            ->update(["user_group" => null])
        )
        {
            //从一个用户组中移除一个用户成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个用户组中移除一个用户成功";
            $admin = session("admin_admin_id");
            $level = DBLog::INFO;
            $otherData = "从一个用户组中移除一个用户";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return true;
        }
        else
        {
            //从一个用户组中移除一个用户失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员从一个用户组中移除一个用户失败";
            $admin = session("admin_admin_id");
            $level = DBLog::ERROR;
            $otherData = "从一个用户组中移除一个用户";
            DBLog::adminLog($message,$admin,$level,$otherData);
            return false;  //该管理员不存在
        }
    }

    /**
     * 向一个用户组添加一个用户
     * @param $user_id_array
     * @return bool
     */
    public function addUser($user_id_array)
    {
        if($user_id_array == null)
        {
            return false;  //没勾选
        }
        foreach ($user_id_array as $data) {   //$data为admin_id

            $userExisted = DB::table("user")
                ->where("user_id", "=", $data)
                ->first();
            if ($userExisted == null) {
                return false;
            }
            $userUpdate = DB::table("user")
                ->where("user_id", "=",$data)
                ->update(["user_group" => $this->info->group_id]);
            if ($userUpdate >= 0)
            {
              continue;
            }
        }
        //添加用户到一个用户组成功后添加日志
        $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员添加用户到一个用户组成功";
        $admin = session("admin_admin_id");
        $level = DBLog::INFO;
        $otherData = "添加用户到一个用户组";
        DBLog::adminLog($message,$admin,$level,$otherData);
        return true;
    }

}