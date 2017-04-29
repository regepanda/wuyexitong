<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:36
 */

namespace MyClass\Admin;

use Validator;
use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\System\DBLog;
use MyClass\Exception\SysValidatorException;
use MyClass\Admin\CommunityGroup;

/**
 * Class Admin
 * @package MyClass\Admin
 */
class Admin implements DatabaseModel
{
    /**
     * @var
     */
    public $admin_id;

    /**
     * @param $admin_id
     */
    public  function  __construct($admin_id)
    {
        $this -> admin_id = $admin_id;
    }

    /**
     * @param $query_limit
     * @return mixed
     */
    public static function select($query_limit)
    {
        $query = DB::table("admin");
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
                $query = $query->orderBy("admin_id","desc");
            }
            else
            {
                $query = $query->orderBy("admin_id");
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

        if (isset($queryLimit['paginate']) && $queryLimit['paginate'] == true)
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
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
        $errorInfo["max"] = ":attribute不应大于150字节";

        $validator = Validator::make($dataArray,[
            'admin_username' => 'required|max:150',
            'admin_nickname' => 'required|max:150',
            'admin_password' => 'required|max:150',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sAdmin");
        }

        $groupExisted = DB::table("admin")
            ->where("admin_username", "=", $dataArray['admin_username'])
            ->first(); //查看数据库中是否存在此管理员
        if ($groupExisted != null)
        {
            return false;
        };
        //不存在，则创建

        if(!isset($dataArray["admin_group"]))
        {
            $dataArray["admin_group"] = 2;
        }
        $dataArray["admin_create_time"] = date("Y-m-d:H-i-s");
        $dataArray["admin_update_time"] = date("Y-m-d:H-i-s");
        if (DB::table('admin')->insert($dataArray))
        {
            //添加管理员成功后插入日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."添加一个管理员成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "添加管理员";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //添加管理员失败后插入日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."添加一个管理员失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "添加管理员";
            DBLog::adminLog($message, $admin, $level,$otherData);
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        //判断是否存在该管理员
        if (DB::table("admin")->where("admin_id", "=", $this->admin_id)->first())
        {
            if(DB::table("admin")->where("admin_id", "=", $this->admin_id)->delete())
            {
                //删除管理员成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."删除管理员成功";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "删除管理员";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //删除管理员失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."删除管理员失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "删除管理员";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        else
        {
            return false;  //不存在该管理员
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
            'admin_username' => 'required|max:20',
            'admin_nickname' => 'required|max:20',
            'admin_group' => 'required',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/admin_sAdmin");
        }

        $dataArray["admin_update_time"] = date("Y-m-d:H-i-s");
        $first = DB::table("admin")
            ->where("admin_id", "=",$this->admin_id)
            ->first();
        if ($first)
        {
            $count =  DB::table("admin")
                ->where("admin_id", "=", $this->admin_id)
                ->update($dataArray);
            if($count)
            {
                //修改管理员成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."修改管理员成功";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "修改管理员";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //修改管理员失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin.nickname")."修改管理员成功";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "修改管理员";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        else
        {
            return false;  //不存在该管理员
        }
    }

    /**
     * @param $username admin用户名
     * @param $password admin密码
     * @return Admin/false
     */
    public static function login($username, $password)
    {
        $result = DB::table("admin")->where("admin_username","=",$username)
            ->where("admin_password","=",md5($password))->first();
        if($result!=NULL)
        {
            $adminModel = new Admin($result->admin_id);
            $adminModel->setSession();
            //管理员登录成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."管理员登录成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "管理员登录";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return $adminModel;
        }
        else
        {
            //管理员登录失败后添加日志
            $message = date("Y-m-d H:i:s")."管理员登录失败";
            $admin = null;
            $level = DBLog::ERROR;
            $otherData = "管理员登录";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }

    }
    /**
     * 将当前管理员信息写入session
     * @return bool
     */
    public function setSession()
    {
        $sessionStruct["admin_id"] = $this->admin_id;
        $adminData = DB::table("admin")->where("admin_id","=",$this->admin_id)->first();
        if($adminData == NULL){return false;}

        $sessionStruct["admin_status"] = true;
        $sessionStruct["admin_nickname"] = $adminData->admin_nickname;
        $sessionStruct["admin_group"] = $adminData->admin_group;
        //如果当前管理员是以物管的角色登录的
        if($adminData->admin_community_group != null)
        {
            $sessionStruct["community_group"] = $adminData->admin_community_group;
            $communityGroup = new CommunityGroup($adminData->admin_community_group);
            $sessionStruct['group_name'] = $communityGroup->info->group_name;
        }

        $powerGroup = new PowerGroup($sessionStruct["admin_group"]);
        $sessionStruct["admin_power"] = $powerGroup->power_list;
        session(["admin"=>$sessionStruct]);
        return true;
    }

    /**
     * 获取管理员信息（包括其所属的权限组）和所有权限组信息
     * @param bool|false $page
     * @return mixed
     */
    static function getAdmin($page = false)
    {
        $base_admin = DB::table("admin")->leftJoin("admin_group", "admin_group", "=", "group_id");
        if ($page)  //判断是否分页
        {
            $data['articleData'] = $base_admin->paginate(5);
            $data['groupData'] = DB::table("admin_group")->get();
            foreach($data['articleData'] as $key => $value)
            {
                if(isset($value->admin_community_group))
                {
                    $data['articleData'][$key]->admin_community = DB::table("community_group")->where("group_id","=",$value->admin_community_group)->first()->group_name;
                }
                else
                {
                    $data['articleData'][$key]->admin_community = null;
                }
            }

            return $data;
        }
        else
        {
            $data['articleData'] = $base_admin->get();
            $data['groupData'] = DB::table("admin_group")->get();
            return $data;
        }
    }

}