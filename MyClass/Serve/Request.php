<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:29
 */

namespace MyClass\Serve;
use Illuminate\Support\Facades\DB;
use MyClass\DatabaseModel;
use MyClass\User\Account;
use MyClass\Serve\Payment;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
use MyClass\System\DBLog;
use Validator;
use MyClass\Exception\SysValidatorException;

class Request implements DatabaseModel
{
    public $request_id;
    public $info;
    /**
     * 构造函数
     * @param $request_id
     */
    public function __construct($request_id)
    {
        $this->request_id = $request_id;
        $this->getInfo();
    }
    /**
     * 查询用户服务请求信息
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
         * |-status 状态筛选
         * |-sort   排序
         * |-search 搜索关键字（按照那边说）
         * |-user   用户筛选（如果涉及到用户）
         * |-desc   是否逆转排序即倒序(默认正序)
         * |-paginate  分页（使用laravel自动分页，这里指定数值）
         * |-id       限制id（制定一个固定id）
         * |-status     限制状态
         * |-payment    根据payment_id来查询
         * |-admin_community_group    根据admin_community_group来查询【对应小区表里面的community_group】
         * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构
         *
         */
        $query = DB::table("request");


        
        //按照用户查找用户的服务请求[可能用户需要查询自己的请求记录]
        if(isset($queryLimit['user']))
        {
            $query = $query->where("request_user","=",$queryLimit['user']);
        }

        //关键字查找
        if(isset($queryLimit['search']))
        {

        }

        //根据类别查找
        if(isset($queryLimit['class']))
        {
            $query = $query->where("request_class","=",$queryLimit['class']);
        }

        //按照状态查找
        if(isset($queryLimit['status']))
        {
            $query = $query->where("request_status","=",$queryLimit['status']);
        }

        //根据ID查找
        if(!empty($queryLimit['id']))
        {
            $query = $query->where("request_id","=",$queryLimit['id']);
        }
        if(isset($queryLimit["payment"]))
        {
            $query = $query->where("request_payment","=",$queryLimit["payment"]);
        }

        //将用户申请的服务请求关联到类
        $query = $query->leftJoin("class","request_class","=","class_id");
        $query = $query->leftJoin("status","request_status","=","status_id");

        //如果当前系统是以物管的角色登录，那查询的用户请求也应该是此物管所管理的小区的用户的请求
        $query = $query->leftJoin("user","request_user","=","user_id");

        if(isset($queryLimit["admin_community_group"]))
        {
            $query = $query->leftJoin("user_community_group","user_id","=","re_user");
            $query = $query->where("re_community_group","=",$queryLimit["admin_community_group"]);
        }

        //计算出总条数
        $num_query  = clone $query;//克隆出来不适用原来的对象
        $returnData["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;
        //排序
        if(isset($queryLimit['sort']))
        {
            if(isset($queryLimit['desc']) && $queryLimit['desc'] == true)
            {
                $query = $query->orderBy($queryLimit['sort'],'desc');

            }
            else
            {
                $query = $query->orderBy($queryLimit['sort']);
            }
        }
        else
        {
            if(isset($queryLimit['desc']) && true == $queryLimit['desc'])
            {
                $query = $query->orderBy('request_id','desc');
            }
            else
            {
                $query = $query->orderBy('request_id');
            }
        }

        //起始条数
        if ( isset($queryLimit["start"])  )
        {
            $query = $query->skip($queryLimit["start"]);
        }


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

            $query = $query->take(10);
            $queryLimit["num"] = 10;
        }
        //分页
        if(isset($queryLimit['paginate']) && $queryLimit['paginate'])
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }

        //往查询出来的数据依次每天插入相对应的状态还有用户信息
        foreach($data as $key => $value)
        {
            if($statusData = DB::table("status")->where("status_id","=",$value->request_status)->first())
            {
                $data[$key]->status = $statusData->status_name;
            }
        }
        foreach($data as $key => $value)
        {
            $userData = DB::table("user")->where("user_id","=",$value->request_user)->first();
            if($userData != null)
            {
                $data[$key]->user_username = $userData->user_username;
                $data[$key]->user_nickname = $userData->user_nickname;
                $data[$key]->user_phone = $userData->user_phone;
            }
        }

        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /**
     * 添加
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {

        $dataArray['request_status'] = 6;
        $dataArray['request_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['request_update_time'] = date("Y-m-d:H-i-s");
        if(($id = DB::table("request")->insertGetId($dataArray)) != false)
        {
            return $id;
        }
        else
        {
            return false;
        }
    }
    /**
     * 删除
     * delete request
     * @access public
     *
     */
    public function delete()
    {
        if(DB::table("request")->where("request_id","=",$this->request_id)->delete())
        {
            //删除请求，成功时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除请求成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "删除请求";
            DBLog::adminLog($message,$admin,$level,$logData);
            return true;
        }
        else
        {
            //删除请求，失败时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除请求失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "删除请求";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }
    /*
     * 更新
     * @param $dataArray
     * @return bool
     */
    public function update($dataArray)
    {

        $dataArray['request_update_time'] = date("Y-m-d:H-i-s");
        $result = DB::table("request")
            ->where("request_id","=",$this->request_id)
            ->update($dataArray);
        //随即更新此条支付单到当前类的$info成员变量中
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取数据库信息
     * @return bool
     */
    public function getInfo()
    {
        $this->info = DB::table("request")->where("request_id","=",$this->request_id)->first();
        if(isset($this->info->request_status))
        {
            $this->info->status_name = DB::table("status")->where("status_id","=",$this->info->request_status)->first()->status_name;
        }
        if(isset($this->info->request_class))
        {
            $this->info->class_name = DB::table("class")->where("class_id","=",$this->info->request_class)->first()->class_name;
        }

        if($userData = DB::table("user")->where("user_id","=",$this->info->request_user)->first())
        {
            $this->info->user_username = $userData->user_username;
            $this->info->user_phone = $userData->user_phone;
        }
        if($this->info->request_payment != null)
        {
            $payment = new Payment($this->info->request_payment);
            $this->info->payment_id = $payment->info->payment_id;
            $this->info->payment_create_time = $payment->info->payment_create_time;
            $this->info->payment_update_time = $payment->info->payment_update_time;
            $this->info->request_price = $payment->info->payment_price;
            $this->info->payment_status = $payment->info->status_name;
        }

        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /**
     * 将状态设为已提交，通常创建后就会自动设定，也可以通过他来修改一些用户描述
     * @param $userIntro
     * @return bool
     */
    public function setStatusCommit($userIntro)
    {
        if($this->info->request_status == 6||$this->info->request_status == null)
        {
            if($this->update(["request_user_intro"=>$userIntro]))
            {
                //将请求状态设置为已提交，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员成功将请求状态设置为已提交";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "将请求状态设置为已提交";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //将请求状态设置为已提交，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员将请求状态设置为已提交失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "将请求状态设置为已提交";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 添加请求封装版函数
     * @param $requestUser
     * @param $requestClass
     * @param $userIntro
     * @return bool|Request
     */
    public static function addRequest($requestUser,$requestClass,$userIntro,$otherData=null)
    {
        $userIntroJson = json_encode([0=>["date"=>date("Y-m-d:H-i-s"),"intro"=>$userIntro]]);

        $insertData = [];
        $insertData["request_user"] = $requestUser;
        $insertData["request_class"] = $requestClass;
        $insertData["request_user_intro"] = $userIntroJson;


        if($otherData != null)
        {
            $insertData["request_other_data"] = json_encode([
                "phone"=>$otherData["phone"],
                "address"=>$otherData["address"]
            ]);
        }


        $result = Request::add($insertData);
        if($result!=false)
        {
            //手动添加请求，成功时添加日志
            $message = date("Y-m-d H-i-s")."添加请求成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "手动添加请求";
            DBLog::adminLog($message,$admin,$level,$logData);
            return new Request($result);
        }
        else
        {
            //手动添加请求，失败时添加日志
            $message = date("Y-m-d H-i-s")."添加请求失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "手动添加请求";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }
    /**
     * 更新用户请求信息
     * @param $userIntro
     * @return bool
     */
    public function updateUserIntro($userIntro)
    {
        //更新用户请求信息
        $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员更新过用户请求信息";
        $admin = session("admin.admin_id");
        $level = DBLog::INFO;
        $logData = "更新用户请求信息";

        $this->getInfo();
        $oldUserIntro = json_decode($this->info->request_user_intro,true);
        $oldUserIntro[] = ["date"=>date("Y-m-d:H-i-s"),"intro"=>$userIntro];
        $newUserIntro = json_encode($oldUserIntro);
        DBLog::adminLog($message,$admin,$level,$logData);
        return $this->update(["request_user_intro" =>
            $newUserIntro]);
    }

    /**
     * 更新客服描述
     * @param $adminIntro
     * @return bool
     */
    public function updateAdminIntro($adminIntro)
    {
        //更新客服描述
        $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员更新过客服描述";
        $admin = session("admin.admin_id");
        $level = DBLog::INFO;
        $logData = "更新客服描述";
        DBLog::adminLog($message,$admin,$level,$logData);

        $this->getInfo();
        $oldAdminIntro = json_decode($this->info->request_admin_intro,true);
        $oldAdminIntro[] =  ["date"=>date("Y-m-d:H-i-s"),"intro"=>$adminIntro];
        $newAdminIntro = json_encode($oldAdminIntro);
        return $this->update(["request_admin_intro" => $newAdminIntro ]);
    }

    /**
     * 设定状态到准备处理，可以多次设定，补充条款，修改价格，如果不设定价格，则只会更改描述
     * @param $adminIntro  客户端批注
     * @param null $price 创建，或者修改价格
     * @return bool|Payment
     */
    public function setStatusReadyHandle($adminIntro,$price=null)
    {
        if($this->info->request_status == 6||$this->info->request_status == 7)
        {
            $data["request_status"] = 7;
           // $data["request_admin_intro"]=$this->info->request_admin_intro."\n更新于".date("Y-m-d:H-i-s")."\n".$adminIntro;
            //if have price ,so need update price or create payment
            if($price!=NULL)
            {
                //update price
                if(!empty($this->info->request_payment))
                {
                    $payment = new Payment($this->request_payment);
                    DB::beginTransaction();
                    $r1 = $payment->updatePrice($price);
                    $r2 = $this->update($data);
                    $r3 = $this->updateAdminIntro($adminIntro);
                    if($r1 && $r2 && $r3)
                    {
                        DB::commit();
                        return true;
                    }
                    DB::rollback();
                    return false;

                }
                //create payment
                else
                {
                    //find user system account
                    $account = Account::select(["user"=>$this->info->request_user]);
                    if($account["status"]!=true&&$account["data"]!=null)
                    {
                        //if not have account
                        return false;
                    }
                    //if have account
                    //create payment
                    $accountId = $account["data"][0]->account_id;
                    DB::beginTransaction();
                    $r1 = Payment::addPayment($accountId,$price);
                    $data["request_payment"] = $r1->info->payment_id;
                    $r2 = $this->update($data);
                    $r3 = $this->updateAdminIntro($adminIntro);
                    if($r1 && $r2 && $r3)
                    {
                        DB::commit();
                        return true;
                    }
                    DB::rollback();
                    return false;

                }

            }
            //将请求状态设置为准备处理
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员将请求状态设置为准备处理";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "将请求状态设置为准备处理";
            DBLog::adminLog($message,$admin,$level,$logData);
            return $this->update($data);

        }
        return false;

    }


    /**设定状态到处理中，只有准备处理可以用
     * @return bool
     */
    public function setStatusInHandle()
    {
        if($this->info->request_status == 7)
        {
            $updateData["request_status"] = 8;
            if($this->update($updateData))
            {
                //将请求状态设置为处理中，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员成功将请求状态设置为处理中";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "将请求状态设置为处理中";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //将请求状态设置为处理中，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员将请求状态设置为处理中失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "将请求状态设置为处理中";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }

        return false;

    }

    /**设定状态到处理完成，只有处理中可以用
     * @return bool
     */
    public function setStatusHaveHandle()
    {
        if($this->info->request_status == 8)
        {
            $updateData["request_status"] = 9;
            if($this->update($updateData))
            {
                //将请求状态设置为处理完成，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员成功将请求状态设置为处理完成";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "将请求状态设置为处理完成";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //将请求状态设置为处理完成，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员将请求状态设置为处理完成失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "将请求状态设置为处理完成";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }
        return false;

    }

    /**设定状态到已取消,只有已提交和准备处理可以用
     * @return bool
     */
    public function setStatusCancel()
    {
        if($this->info->request_status == 6||$this->info->request_status == 7)
        {
            $updateData["request_status"] = 10;
            return $this->update($updateData);

        }
        return false;
    }


    /**
     * 取消请求，联通关联的支付单
     * @return bool
     */
    public function cancel()
    {
        if($this->info->request_status == 6||$this->info->request_status == 7)
        {
            if(isset($this->info->request_payment))
            {
                $paymentModel = new Payment($this->info->request_payment);
                if($paymentModel ->setStatusCancelPay())
                {
                    //将请求状态设置为取消请求，成功时添加日志
                    $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员成功将请求状态设置为取消请求";
                    $admin = session("admin.admin_id");
                    $level = DBLog::INFO;
                    $logData = "将请求状态设置为取消请求";
                    DBLog::adminLog($message,$admin,$level,$logData);
                    return true;
                }
                else
                {
                    //将请求状态设置为取消请求，失败时添加日志
                    $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员将请求状态设置为取消请求失败";
                    $admin = session("admin.admin_id");
                    $level = DBLog::ERROR;
                    $logData = "将请求状态设置为取消请求";
                    DBLog::adminLog($message,$admin,$level,$logData);
                    return false;
                }
            }
            return $this->setStatusCancel();

        }
        return false;
    }


    /**
     * 添加一张描述图片
     * @param $image_id
     * @return bool
     */
    public function addImage($image_id)
    {
        $this->getInfo();
        $jsonData = json_decode($this->info->request_other_data,true);
        $jsonData["images"][] = $image_id;
        $jsonData = json_encode($jsonData);
        return $this->update(["request_other_data"=>$jsonData]);
    }

}