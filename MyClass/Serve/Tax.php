<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:29
 */

namespace MyClass\Serve;


use MyClass\DatabaseModel;
use Illuminate\Support\Facades\DB;
use MyClass\User\Account;
use MyClass\User\House;
use MyClass\User\InputHouseData;
use MyClass\User\User;
use MyClass\Serve\Payment;
use MyClass\System\DBLog;
use MyClass\User\CarPosition;

class Tax implements DatabaseModel
{
    public $tax_id;
    public $info;

    public function __construct($tax_id)
    {
        $this->tax_id = $tax_id;
        $this->getInfo();
    }

    public function getInfo()
    {
        $this->info = DB::table("tax")->where("tax_id","=",$this->tax_id)->first();
        if(isset($this->info->tax_status))
        {
            $this->info->status_name = DB::table("status")->where("status_id","=",$this->info->tax_status)->first()->status_name;
        }

        if(isset($this->info->tax_class))
        {
            $this->info->class_name = DB::table("class")->where("class_id","=",$this->info->tax_class)->first()->class_name;
        }

        if($userData = DB::table("user")->where("user_id","=",$this->info->tax_user)->first())
        {
            $this->info->user_username = $userData->user_username;
            $this->info->user_nickname = $userData->user_nickname;
        }
        if($this->info->tax_payment != null)
        {
            $payment = new Payment($this->info->tax_payment);
            $this->info->payment_id = $payment->info->payment_id;
            $this->info->payment_create_time = $payment->info->payment_create_time;
            $this->info->payment_update_time = $payment->info->payment_update_time;
            $this->info->payment_status = $payment->info->status_name;
            $this->info->tax_price = $payment->info->payment_price;
        }

        if($this->info!=null){return $this->info;}
        else{return false;}
    }

    /**
     * select tax
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

        $query = DB::table("tax");
        //Whether the sorting

        //Keyword search
        if(isset($queryLimit['search']))
        {

        }

        //select by user
        if(isset($queryLimit['user']))
        {
            $query = $query->where("tax_user","=",$queryLimit['user']);
        }

        //According to the categories to find
        if(isset($queryLimit['class']))
        {
            $query = $query->where("tax_class","=",$queryLimit['class']);
        }

        //select by status
        if(isset($queryLimit['status']))
        {
            $query = $query->where("tax_status","=",$queryLimit['status']);
        }

        //select by id
        if(!empty($queryLimit['id']))
        {
            $query = $query->where("tax_id","=",$queryLimit['id']);
        }

        if(isset($queryLimit['tax_house']))
        {
            $query = $query->where("tax_house","=",$queryLimit['tax_house']);
        }

        if(isset($queryLimit['tax_car_position']))
        {
            $query = $query->where("tax_car_position","=",$queryLimit['tax_car_position']);
        }

        if(isset($queryLimit["payment"]))
        {
            $query = $query->where("tax_payment","=",$queryLimit['payment']);
        }

        //Associated with the category
        $query = $query->leftJoin("class","tax_class","=","class_id");

        //如果当前系统是以物管的角色登录，那查询的用户缴费信息也应该是此物管所管理的小区的用户的缴费信息
        $query = $query->leftJoin("user","tax_user","=","user_id");
        $query = $query->leftJoin("payment","tax_payment","=","payment_id");

        if(isset($queryLimit["admin_community_group"]))
        {
            $query = $query->leftJoin("user_community_group","user_id","=","re_user");
            $query = $query->where("re_community_group","=",$queryLimit["admin_community_group"]);
        }

        //Calculate the total number
        $num_query  = clone $query;
        $returnData["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;
        if(isset($queryLimit['sort']) && $queryLimit['sort'] == true)
        {
            if(isset($queryLimit['desc']) && $queryLimit['desc'] == true)
            {
                $query = $query->orderBy($queryLimit['sort'],"desc");
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
                $query = $query->orderBy("tax_id","desc");
            }
            else
            {
                $query = $query->orderBy("tax_id");
            }
        }
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
                $return_data["message"] = "查询num设置为0，不返回数据";
                $return_data["data"] =  [];
                return $return_data;
            }

            $query = $query->take($queryLimit["num"]);
        }
        else
        {

            $query = $query->take(10);
            $queryLimit["num"] = 10;
        }
        if(isset($queryLimit['paginate']))
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }

        //Add status and user to query out of each record
        foreach($data as $key => $value)
        {
            if($statusData = DB::table("status")->where("status_id","=",$value->tax_status)->first())
            {
                $data[$key]->status = $statusData->status_name;
            }
        }
        foreach($data as $key => $value)
        {
            $userData = DB::table("user")->where("user_id","=",$value->tax_user)->first();
            if($userData)
            {
                $data[$key]->user_username = $userData->user_username;
                $data[$key]->user_nickname = $userData->user_nickname;
                $data[$key]->user_phone = $userData->user_phone;
            }
        }
        foreach($data as $key => $value)
        {
            if($paymentData = DB::table("payment")->where("payment_id","=",$value->tax_payment)->first())
            {
                $data[$key]->payment_price = $paymentData->payment_price;
            }
        }

        //Return the data
        $returnData['status'] = true;
        $returnData['message'] = '成功获得数据';
        $returnData['data'] = $data;
        return $returnData;
    }

    /*
     * add tax
     * @access public
     * @param $dataArray
     * @return bool
     */
    public static function add($dataArray)
    {
        $dataArray['tax_status'] = 11;
        $dataArray['tax_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['tax_update_time'] = date("Y-m-d:H-i-s");

        if($result = DB::table("tax")->insertGetId($dataArray))
        {
            return $result;
        }
        else
        {
            return false;
        }

    }
    /*
     * update tax
     * @access public
     * @return bool
     */
    public function update($dataArray)
    {
        $dataArray['tax_update_time'] = date("Y-m-d:H-i-s");
        if(DB::table("tax")->where("tax_id","=",$this->tax_id)->update($dataArray))
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
     * delete tax
     * @access public
     * @return bool
     */
    public function delete()
    {
        if(DB::table("tax")->where("tax_id","=",$this->tax_id)->delete())
        {
            //删除缴费单，成功时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除缴费单成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $logData = "删除缴费单";
            DBLog::adminLog($message,$admin,$level,$logData);
            return true;
        }
        else
        {
            //删除缴费单，失败时添加日志
            $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员删除缴费单失败";
            $admin = session("admin.admin_id");
            $level = DBLog::ERROR;
            $logData = "删除缴费单";
            DBLog::adminLog($message,$admin,$level,$logData);
            return false;
        }
    }

    /**
     * 添加一个支付单
     * @param $user 用户
     * @param $class 分类
     * @param $time 有效天数
     * @param $price 价格
     * @param null $intro 介绍
     * @return bool
     */
    public static function addTax($user,$class,$time,$price,$intro=null)
    {
        $newData["tax_user"] = $user;
        $newData["tax_class"] = $class;
        $newData["tax_deadline"] = date('Y-m-d H:i:s',time()+$time*60*60*24);
        $newData["tax_intro"] = $intro;
        $userModel = new User($user);
        $accountId = $userModel->getAccount();


        DB::beginTransaction();
        $paymentModel = Payment::addPayment($accountId,$price);
        $newData["tax_payment"] = $paymentModel->payment_id;
        $r1 = Tax::add($newData);

        //添加缴费单
        $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."管理员手动添加了一个缴费单";
        $admin = session("admin.admin_id");
        $level = DBLog::INFO;
        $logData = "添加缴费单";
        DBLog::adminLog($message,$admin,$level,$logData);

        if($r1)
        {
            DB::commit();
            return new Tax($r1);
        }
        DB::rollback();
        return false;

    }


    /**
 * 另外一种缴费单生成方式，用于用户主动请求缴费的情况 【物业费】
 * 默认有效期为一天
 * @param $user
 * @param $class
 * @param $price
 * @param $month
 * @param null $intro
 * @return bool|Tax
 */
    public static function addTaxByUserAsk($user,$class,$price,$time,$house_id,$intro=null)
    {
        $house = new House($house_id);
        $inputHouse = new InputHouseData($house->info->house_where);
        $newData["tax_user"] = $user;
        $newData["tax_class"] = $class;
        $newData["tax_deadline"] = date('Y-m-d H:i:s',time()+1*60*60*24);
        $newData["tax_intro"] = $intro;
        //这里根据缴费次数把缴费的月份全部查到，定位月份游标
        $content = "";
        for($i=0;$i<$time;$i++)
        {
            $content.= Now::getMapping($inputHouse->info->house_now + $i).",";
        }
        $newData["tax_month"] = $content;
        $newData["tax_house"] = $house_id;

        //判断剩余缴费次数够不
        if($inputHouse->info->house_cantax_time - $time < 0)
        {
            return false;
        }
        //跟新house表里面的house_cantax_time,每交费一次减少一次
        $house_cantax_time = (int)($inputHouse->info->house_cantax_time - $time);
        $house_now = (int)($inputHouse->info->house_now + $time);
        $inputHouse->update(["house_cantax_time"=>$house_cantax_time,"house_now"=>$house_now]);

        $userModel = new User($user);
        $accountId = $userModel->getAccount();
        DB::beginTransaction();
        $paymentModel = Payment::addPayment($accountId,$price);
        $newData["tax_payment"] = $paymentModel->payment_id;
        $r1 = Tax::add($newData);

        if($r1)
        {
            DB::commit();
            return new Tax($r1);
        }
        DB::rollback();
        return false;
    }

    /**
     * 另外一种缴费单生成方式，用于用户主动请求缴费的情况  【停车费】
     * 默认有效期为一天
     * @param $user
     * @param $class
     * @param $price
     * @param $month
     * @param null $intro
     * @return bool|Tax
     */
    public static function addPositionTaxByUserAsk($user,$class,$price,$time,$position_id,$intro=null)
    {
        $carPosition = new CarPosition($position_id);
        $newData["tax_user"] = $user;
        $newData["tax_class"] = $class;
        $newData["tax_deadline"] = date('Y-m-d H:i:s',time()+1*60*60*24);
        $newData["tax_intro"] = $intro;
        //这里根据缴费次数把缴费的月份全部查到，定位月份游标
        $content = "";
        for($i=0;$i<$time;$i++)
        {
            $content.= Now::getMapping($carPosition->info->position_now + $i).",";
        }
        $newData["tax_month"] = $content;
        $newData["tax_car_position"] = $position_id;
        //判断剩余缴费次数够不
        if($carPosition->info->position_cantax_time - $time < 0)
        {
            return false;
        }

        //跟新car_position表里面的position_cantax_time,没交费一次减少一次
        $position_cantax_time = (int)($carPosition->info->position_cantax_time - $time);
        $position_now = (int)($carPosition->info->position_now + $time);
        $carPosition->update(["position_cantax_time"=>$position_cantax_time,"position_now"=>$position_now]);

        $userModel = new User($user);
        $accountId = $userModel->getAccount();
        DB::beginTransaction();
        $paymentModel = Payment::addPayment($accountId,$price);
        $newData["tax_payment"] = $paymentModel->payment_id;
        $r1 = Tax::add($newData);

        if($r1)
        {
            DB::commit();
            return new Tax($r1);
        }
        DB::rollback();
        return false;
    }

    /**
     * 切换状态到已支付
     * @return bool
     */
    public function setStatusHavePay()
    {
        if($this->info->tax_status == 11)
        {
            $updateData["tax_status"] = 12;
            if($this->update($updateData))
            {
                //切换已支付成功后随即给此缴费用户的账户加积分
                if(isset($this->info->tax_payment))
                {
                    $payment = new Payment($this->info->tax_payment);
                    $inputData['account_integration'] = (int)$payment->info->payment_price;
                    $account = new Account($payment->info->payment_account);
                    if($account->info->account_integration != null)
                    {
                        $inputData['account_integration'] = $inputData['account_integration']+$account->info->account_integration;
                    }
                    $account->update($inputData);
                }
                //切换状态到已支付，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."成功将缴费状态切换到已支付";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "切换状态到已支付";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //切换状态到已支付，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."将缴费状态切换到已支付失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "切换状态到已支付出错";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }
        return false;
    }
    /**
     * 切换状态到取消付费
     * @return bool
     */
    public function setStatusCancelPay()
    {
        if($this->info->tax_status == 11||$this->info->tax_status == 15)
        {
            $updateData["tax_status"] = 13;

            if($this->update($updateData))
            {
                //切换状态到取消付费，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."成功将缴费状态切换到取消付费";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "切换状态到取消付费";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //切换状态到取消付费，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."将缴费状态切换到取消付费失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "切换状态到取消付费";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }
        return false;
    }

    /**
     * 取消订单，相关联的支付单也要转变状态
     * @return bool
     */

    public function cancel()
    {
        if($this->info->tax_status == 11 || $this->info->tax_status == 15)
        {
            if(isset($this->info->tax_payment))
            {
                $paymentModel = new Payment($this->info->tax_payment);
                return $paymentModel->setStatusCancelPay();
            }
            return $this->setStatusCancelPay();

        }
        return false;
    }


    /**
     * 切换状态到已过期
     * @return bool
     */
    public function setStatusOutDate()
    {
        if($this->info->tax_status == 11)
        {
            $updateData["tax_status"] = 15;
            if($this->update($updateData))
            {
                //切换状态到取已过期，成功时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."成功将缴费状态切换到已过期";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $logData = "切换状态到已过期";
                DBLog::adminLog($message,$admin,$level,$logData);
                return true;
            }
            else
            {
                //切换状态到取已过期，失败时添加日志
                $message = date("Y-m-d H-i-s").session("admin.admin_nickname")."将缴费状态切换到已过期失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $logData = "切换状态到已过期";
                DBLog::adminLog($message,$admin,$level,$logData);
                return false;
            }
        }
        return false;
    }

    /**
     * 检查改条目是否过期
     * @return bool
     */
    public function checkOutDate()
    {
        if(time()-$this->info->tax_deadline>0)
        {
            if($this->info->tax_status == 11)
            {
                $updateData["tax_status"] = 15;
                return $this->update($updateData);
            }
            return false;
        }
        return false;
    }

    /**
     * 检查所有可能过期的tax，将其数据库状态改变
     * @return int
     */
    public static function checkAllOutDate()
    {
        $i = 0;
        $data = DB::table("tax")->where("tax_status","=","11")->get();
        foreach($data as $value)
        {
            $model = new Tax($value->tax_id);
            if($model->checkOutDate())
            {
                ++$i;
            }
        }
        return $i;
    }

}