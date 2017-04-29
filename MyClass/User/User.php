<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:31
 */

namespace MyClass\User;


use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\Exception\SysValidatorException;
use MyClass\System\DBLog;

/**
 * Class User
 * @package MyClass\User
 */
class User implements DatabaseModel
{
    /**
     * @var user表单的一条记录（即一个对象）
     */
    public $info;

    /**
     * @var 用户id
     */
    public $user_id;

    /**
     * 构造函数
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->getInfo();
    }

    /**
     * @param $query_limit 数组
     * @return mixed数组
     */
    public static function select($query_limit)
    {
        /* 传入数组，进行条件查询，下面是参数为数组的各项的意思，返回值为数组的各项的意思
        * 参数  $queryLimit
        * |-start  起始条数
        * |-num   每页条数
        * |-sort   排序,按照哪个字段排序
        * |-user   用户id筛选（如果涉及到用户,传入用户id）
        * |-user_username 用户名筛选
        * |-admin_community_group   根据物业公司来筛选


        * |-desc   是否逆转排序即倒序(默认正序)
        * |-paginate  分页（使用laravel自动分页，这里指定数值），传入要想分页的条数
        * |-id      按照字段id查找
        * |-nickname  根据昵称和密码判断是否存在此用户
        * |-password
        *
          /*
          * 返回值 $return_data
          * |-status 是否成功
          * |-message 消息
          * |-data   查询的结果
          * |-total 符合查询条件的总条数，不是数据库总条数
       |*/
        $query = DB::table("user");

        //排序
        if(isset($query_limit["sort"]))  //自定义字段排序
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
                $query = $query->orderBy("user_id","desc");
            }
            else
            {
                $query = $query->orderBy("user_id");
            }
        }

        //按id查找某条记录
        if(isset($query_limit["id"]))
        {
            $query = $query->where("user_id","=",$query_limit["id"]);
        }

        //按用户id查找
        /*if(isset($query_limit["user"]))
        {
            $query = $query->where("user_id","=",$query_limit["user"]);
        }*/

        //按用户名查找
        if(!empty($query_limit["user_username"]))
        {
            $query = $query -> where("user_username","=",$query_limit["user_username"]);
        }

        //如果当前系统是以物管的角色登录，那查询出来的用户也应该是此物管所管理的小区的用户
        if(isset($query_limit["admin_community_group"]))
        {
            $query = $query->leftJoin("user_community_group","user_id","=","re_user");
            $query = $query->where("re_community_group","=",$query_limit["admin_community_group"]);
        }
        //计算出符合查询条件的总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $return_data["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num;

        //起始条数
        if ( isset($query_limit["start"]))
        {
            $query = $query->skip($query_limit["start"]);
        }

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
            $query = $query->take(config("my_config.default_num_page"));     //自己增加，默认5条
            $query_limit["num"] = config("my_config.default_num_page");     //
        }

        //是否分页
        if(isset($query_limit["paginate"]))
        {

            $data = $query->simplePaginate($query_limit["paginate"]);
        }
        else
        {
            $data = $query->get();
        }

        //返回结果
        $return_data["status"] = true;
        $return_data["message"] = "成功获取到数据";
        $return_data["data"] = $data;

        return $return_data;
    }

    /*
     * 添加一条记录
     * @param $dataArray 数组
     * @return bool
     */
    public static function  add($dataArray)
    {
        $errorInfo["required"] = ":attribute必填";
        //$errorInfo["digits_between"] = ":attribute应该在2-150字节之间";
        $validator = Validator::make($dataArray,[
            'user_username' => 'required',
            'user_phone' => 'required',
            'user_password' => 'required',
        ],$errorInfo);
        if($validator->fails()){
            $messages = $validator->errors();
            $errorStr = "";
            foreach ($messages->all() as $message) {
                $errorStr.=$message." | ";
            }
            throw new SysValidatorException("字段格式有错误！".$errorStr,"/");
        }

        $dataArray["user_create_time"] = date('Y-m-d H:i:s');
        $dataArray["user_update_time"] = date('Y-m-d H:i:s');
        //不能有相同的用户名（类似账号）
        $record = DB::table('user')->where("user_username","=",$dataArray["user_username"])
                  ->first();
        if(!$record)
        {
            $return = DB::table("user")->insertGetId($dataArray);
            if($return!=false)
            {
                return $return;
            }
            return false;
        }
        return false;
    }

    /**
     * 根据id删除一条记录
     * @return bool
     */
    public function delete()
    {

        $user_id = $this->user_id;
        //判断是否存在
        $record = DB::table('user')->where("user_id", "=", $user_id)->first();
        if ($record) {
            //存在，再删
            $return = DB::table('user')->where("user_id", "=", $user_id)->delete();
            if ($return) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 更新一条记录
     * @param $dataArray 数组
     * @return bool
     */
    public function update($dataArray)
    {
        $user_id = $this -> user_id;
        $dataArray["user_update_time"] = date('Y-m-d H:i:s');
        $return = DB::table("user")->where("user_id", "=", $user_id)->update($dataArray);
        if($return > 0)
        {
            //修改用户成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."修改用户成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "修改用户";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return true;
        }
        else
        {
            //修改用户失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin.nickname")."修改用户失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $otherData = "修改用户";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /**
     * 修改密码
     * @param $phone  用户名为手机号码
     * @param $password  用户新密码
     * @return bool
     */
    public static function updatePassword($phone,$password)
    {
        //根据用户名找到此条用户记录
        $userData = DB::table("user")->where("user_username","=",$phone)->first();
        if(!empty($userData))
        {
            //不为空，开始修改密码
            $user = new User($userData->user_id);
            if($user->update(["user_password"=>md5($password)]))
            {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 登录函数
     * @param $username
     * @param $password
     * @return bool|User
     */
    public static function login($username, $password)
    {
        $userData = DB::table("user")->where("user_username","=",$username)
            ->where("user_password","=",md5($password))
            ->first();
        if($userData != NULL)
        {
            $userModel =  new User($userData->user_id);
            $userModel->setSession();
            return $userModel;
        }
        else
        {
            return false;
        }
    }

    /*
     * 注册
     * @param $username 废弃
     * @param $password
     * @param $phone
     * @param $otherData
     * @return bool|User
     */
    public static function register($password,$phone,$otherData=[])
    {
        $dataArray["user_username"] = $phone;
        $dataArray["user_password"] = md5($password);
        $dataArray["user_phone"] = $phone;
        $dataArray["user_group"] = 2;
        if(isset($otherData["user_nickname"]))
        {
            $dataArray["user_nickname"] =  $otherData["user_nickname"];
        }
        else
        {
            $dataArray["user_nickname"] = "用户".date('YmdHis').rand(1,99);
        }

        if(isset($otherData["user_birthday"]))
        {
            $dataArray["user_birthday"]  = $otherData["user_birthday"];
        }
        if(isset($otherData["user_phone_backup"]))
        {
            $dataArray["user_phone_backup"]  = $otherData["user_phone_backup"];
        }
        $unionData = User::select(["user_username"=>$dataArray["user_username"]]);
        //dump($unionData);

        if(!empty($unionData["data"]))
        {
            return ["status"=>false, "message"=>"已存在该用户名/手机号,请换一个","data"=>[]];
        }

        $id = User::add($dataArray);

        if($id)
        {
            Account::addAccount($id);
            return ["status"=>true, "message"=>"注册完成","data"=>["id"=>$id],"result_code"=>0];

        }
        return false;


    }

    /**
     * @param User $userModel 一个用户模型
     * @return string         返回acsToken
     */
    public static function setAccessToken(User $userModel)
    {
        $user_id = $userModel->user_id;
        $tokenSource = rand(100000,999999).$user_id.date('YmdHis');
        $token = sha1($tokenSource);
        Redis::command("set",["wuyexitong:user:".$token, $user_id]);
        Redis::command("expire",["wuyexitong:user:".$token, 60*60*24*config("my_config.access_token_expired_date")]);

        return $token;
    }

    /**
     * @param $accessToken
     * @return array
     * |-status      状态
     * |-message     消息，文字说明
     * |-result_code 结果码
     * |-user_id     对应用户id（status = true 才会有）
     */
    public static function checkAccessToken($accessToken)
    {

        $returnData = Redis::command("get",["wuyexitong:user:".$accessToken]);
        if($returnData != null)
        {
            return ["status"=>true,"message"=>"valid token","result_code"=>0,"user_id" => $returnData];
        }
        else
        {
            return ["status"=>false,"message"=>"error token","result_code"=>2];
        }

    }

    /**
     * 将当前用户信息写入session
     * @return bool
     */
    public function setSession()
    {
        $sessionStruct["user_id"] = $this->user_id;
        $userData = DB::table("user")->where("user_id","=",$this->user_id)->first();
        if($userData == NULL){return false;}

        $sessionStruct["user_status"] = true;
        $sessionStruct["user_nickname"] = $userData->user_nickname;
        $sessionStruct["user_group"] = $userData->user_group;

        $powerGroup = new PowerGroup($sessionStruct["user_group"]);
        $sessionStruct["admin_power"] = $powerGroup->power_list;
        session(["user"=>$sessionStruct]);
        return true;
    }

    public function getInfo()
    {
        $this->info = DB::table("user")->where("user_id","=",$this->user_id)->first();
        if($this->info!=null){return $this->info;}
        else{return false;}
    }


    /**
     * 返回该用户的id
     * @return bool|int
     */
    public function getAccount()
    {
        $queryLimit['user'] =  $this->user_id;
        $data = Account::select($queryLimit);
        if($data["status"] == false)
        {
            return false;
        }
        return $data["data"][0]->account_id;
    }

    /*
     * 查询所有的用户组
     * @return $userGroup
     */
    public static function getAllUserGroup()
    {
        $userGroup = DB::table("user_group")->get();
        return $userGroup;
    }

    /*
     * 查询所有的用户
     * @return $userData
     */
    public static function getAllUser()
    {
        $userData = DB::table("user")->get();
        return $userData;
    }

    public function setHeadImage($imageId)
    {
        //dump($imageId);
        return $this->update(["user_image"=>$imageId]);
    }

}