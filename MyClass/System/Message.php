<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/14
 * Time: 20:06
 */

namespace MyClass\System;

use Illuminate\Support\Facades\DB;
use MyClass\DatabaseModel;
use MyClass\User\Account;
use MyClass\System\DBLog;

class Message
{
    public $message_id;
    public $info;
    /*
     * 构造函数
     * @param $message_id
     */
    public function __construct($message_id)
    {
        $this->message_id = $message_id;
        $this->getInfo();
    }
    /*
     * 当条记录信息
     * @param $message_id
     */
    public function getInfo()
    {
        $this->info = DB::table("message")->where("message_id","=",$this->message_id)->first();
        if($userData = DB::table("user")->where("user_id","=",$this->info->message_to)->first())
        {
            $this->info->user_username = $userData->user_username;
            $this->info->user_nickname = $userData->user_nickname;
        }
        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /*
     * select message
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
         * |-search 搜索关键字（按照那边说）
         * |-user   用户筛选（如果涉及到用户）
         * |-admin  管理员筛选
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
        $query = DB::table("message");
        //Whether the sorting
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
                $query = $query->orderBy('message_id','desc');
            }
            else
            {
                $query = $query->orderBy('message_id');
            }
        }

        //Keyword search
        if(isset($queryLimit['search']))
        {
            //key word can't use in this model
        }

        //select by id
        if(isset($queryLimit['id']))
        {
            $query = $query->where("message_id","=",$queryLimit['id']);
        }

        //select by user
        if(isset($queryLimit['user']))
        {
            $query = $query->where("message_to","=",$queryLimit['user']);
        }

        //select by admin
        if(isset($queryLimit['admin']))
        {
            $query = $query->where("message_admin","=",$queryLimit['admin']);
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

        foreach($data as $key => $value)
        {
            if(isset($value->message_to))
            {
                $data[$key]->user_username = DB::table("user")->where("user_id","=",$value->message_to)->first()->user_username;
            }
            if(isset($value->message_admin))
            {
                $data[$key]->admin_username = DB::table("admin")->where("admin_id","=",$value->message_admin)->first()->admin_username;
            }
        }
        //Return the data
        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * add message [send message]
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray['message_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['message_read'] = false;
        if(DB::table("message")->insert($dataArray))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*【这个方法在消息中一般不会调用，消息不会更新，这里还是写了】
     * update message
     * @access public
     * @return bool
     */
    public function update($dataArray)
    {
        $result = DB::table("message")
            ->where("message_id","=",$this->message_id)
            ->update($dataArray);
        if($result)
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
     *设置消息为已读
     * @access public
     * @return bool
     */
    public function setReadMessage()
    {
        if($this->update(["message_read"=>true]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     *delete message
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("message")
            ->where("message_id","=",$this->message_id)
            ->delete())
        {
            //删除信息，成功添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除信息成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "删除信息";
            DBLog::adminLog($message,$admin,$level,$logData);
            return true;
        }
        else
        {
            //删除信息，失败添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除信息失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "删除信息";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }

    /*
     *send message 【给一个用户发,这个接口不会用到】
     * @access public
     * @param $message_to,$message_data【要发送的用户和发送数据】
     * @return object
     */
    public static function sendMessageToUser($message_to,$message_data)
    {
        $result = Message::add([
            "message_to" => $message_to,
            "message_data" => $message_data,
        ]);
        return $result;
    }
    /*
     *send message 【给一个用户组发】
     * @access public
     * @param $group_id,$message_data【要发送的用户组和发送数据】
     * @return object
     */
    public static function sendMessageToUserGroup($group_id,$message_data)
    {
        //根据用户组ID查询所有的用户
        $userData = DB::table("user")->where("user_group","=",$group_id)->get();
        if($userData != null)
        {
            foreach($userData as $key => $value)
            {
                Message::sendMessageToUser($value->user_id,$message_data);
            }
            //给用户组发送信息
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."给用户组发送信息：".$message_data;
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "给用户组发送信息";
            DBLog::adminLog($message,$admin,$level,$logData);
            return true;
        }
        else
        {
            return false;
        }
    }
}