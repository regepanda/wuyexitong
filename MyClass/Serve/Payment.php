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
use MyClass\Serve\Tax;
use MyClass\System\DBLog;
use MyClass\User\Account;

class Payment implements DatabaseModel
{
    public $payment_id;
    public $info;
    /*
     * 构造函数
     * @param $payment_id
     */
    public function __construct($payment_id)
    {
        $this->payment_id = $payment_id;
        $this->getInfo();
    }
    /*
     *获取基本信息
     * @return $info/bool
     */
    public function getInfo()
    {
        $this->info = DB::table("payment")->where("payment_id","=",$this->payment_id)->first();
        if(isset($this->info->payment_status))
        {
            $this->info->status_name = DB::table("status")->where("status_id","=",$this->info->payment_status)->first()->status_name;
        }
        if(isset($this->info->payment_class))
        {
            $this->info->class_name = DB::table("class")->where("class_id","=",$this->info->payment_class)->first()->class_name;

        }
        if($this->info!=null){return $this->info;}
        else{return false;}
    }
    /**
     * select payment
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
         * |-status 状态删选
         * |-sort   排序
         * |-search 搜索关键字（按照那边说）
         * |-user   用户筛选（如果涉及到用户），连account表
         * |-desc   是否逆转排序即倒序(默认正序)
         * |-paginate  分页（使用laravel自动分页，这里指定数值）
         * |-id       限制id（制定一个固定id）
         * |-payment_account 支付单所属账户
         * |*/

        /*
         * $returnData
         * |-status 是否成功
         * |-message 消息
         * |-num    数据总条数(不是当前数据的条数，是可按照此限制查出的所有数据)
         * |-data   数据 DB返回的二维结构
         *
         */
        $query = DB::table("payment");
        //Whether the sorting

        //According to the account for
        if(isset($queryLimit['payment_account']))
        {
            $query = $query->where("payment_account","=",$queryLimit['payment_account']);
        }

        //Keyword search
        if(isset($queryLimit['search']))
        {
            //key word can't use in this model
        }

        /*//According to the categories to find
        if(isset($queryLimit['payment_class']))
        {
            $query = $query->where("payment_class","=",$queryLimit['class']);
        }*/

        //select by id
        if(!empty($queryLimit['id']))
        {
            $query = $query->where("payment_id","=",$queryLimit['id']);
        }

        //select by class
        if(isset($queryLimit['class']))
        {
            $query = $query->where("request_class","=",$queryLimit['class']);
        }

        //select by status
        if(isset($queryLimit['status']))
        {
            $query = $query->where("payment_status","=",$queryLimit['status']);
        }

        if(isset($queryLimit['user']))
        {
            $query = $query -> leftJoin("account","payment_account","=","account_id")
                            -> where('account_user',"=",$queryLimit["user"]);
        }

        //Calculate the total number
        $num_query  = clone $query;//这里克隆一个，不用原来的了
        $returnData["total"] = $num_query->select(DB::raw('count(*) as num'))->first()->num  ;

        //The starting page number
        if ( isset($queryLimit["start"])  )
        {
            $query = $query->skip($queryLimit["start"]);
        }
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
                $query = $query->orderBy('payment_id','desc');
            }
            else
            {
                $query = $query->orderBy('payment_id');
            }
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

        //Associated with the category
        $query = $query->leftJoin("class","payment_class","=","class_id");

        if(isset($queryLimit['paginate']))
        {
            $data = $query->simplePaginate($queryLimit['paginate']);
        }
        else
        {
            $data = $query->get();
        }

        //Add status to query out of each record
        foreach($data as $key => $value)
        {
            if($statusData = DB::table("status")->where("status_id","=",$value->payment_status)->first())
            {
                $data[$key]->status = $statusData->status_name;
            }
        }
        foreach($data as $key => $value)
        {
            if(isset($data[$key]->payment_account))
            {
                $account = new Account($data[$key]->payment_account);
                $data[$key]->user_username = $account->info->user_username;
                $data[$key]->user_phone = $account->info->user_phone;
            }
        }

        //Return the data

        $returnData['status'] = true;
        $returnData['message'] = "成功获取到数据";
        $returnData['data'] = $data;
        return $returnData;
    }

    /**
     *delete payment
     * @access public
     * @return bool
     */
    public function delete()
    {
        return DB::table("payment")
            ->where("payment_id","=",$this->payment_id)
            ->delete();
    }

    /*
     * update payment
     * @access public
     * @return bool
     */
    public function update($dataArray)
    {
        $dataArray['payment_update_time'] = date("Y-m-d:H-i-s");
        $result = DB::table("payment")
            ->where("payment_id","=",$this->payment_id)
            ->update($dataArray);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /*
     * add payment
     * @access public
     * @param $dataArray
     * @return $id/false
     */
    public static function add($dataArray)
    {
        $dataArray['payment_create_time'] = date("Y-m-d:H-i-s");
        $dataArray['payment_update_time'] = date("Y-m-d:H-i-s");
        $dataArray['payment_status'] = 1;
        if($id = DB::table("payment")->insertGetId($dataArray) )
        {
            return $id;
        }
        else
        {
            return false;
        }
    }

    /**
     * 修改支付单价格，更新价格的记录会留存在other_data的logList中
     * @param $price
     * @return bool
     */
    public function updatePrice($price)
    {
        if($this->info->payment_status == 1)
        {
            $otherData = json_decode($this->info->payment_other_data,true);
            $otherData["logList"][]="修改价格，时间：".date("Y-m-d:H-i-s")." 原金额：".$this->info->payment_price.
                " 新的金额：$price";
            $updateDate["payment_other_data"] = json_encode($otherData);
            $updateDate["payment_price"] = $price;
            if($this->update($updateDate))
            {
                //管理员修改支付单价格成功后添加日志
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员修改支付单价格成功";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "管理员修改支付单价格";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //管理员修改支付单价格失败后添加日志
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员修改支付单价格失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "管理员修改支付单价格";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        return false;


    }

    /**
     * 添加一个payment
     * @return Payment/false
     */
    public static function addPayment($accountId,$price,$intro=null,$class=null)
    {
        $id = Payment::add([
            "payment_account" => $accountId,
            "payment_price"=> $price,
            "payment_class"=> $class,
            "payment_intro"=>$intro
        ]);
        if($id)
        {
            //管理员添加支付单成功后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员手动添加支付单成功";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "手动添加支付单";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return new Payment($id);
        }
        else
        {
            //管理员添加支付单失败后添加日志
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员手动添加支付单失败";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "手动添加支付单";
            DBLog::adminLog($message, $admin, $level,$otherData);
            return false;
        }
    }

    /**
     * 设定状态已经支付,
     * 这个函数会自动去查找相关请求和缴费，将其设置为下一个状态
     * 函数会尝试去改变Request Tax状态
     * @return bool
     */
    public function setStatusAlreadyPay($class=null,$prepayId=null,$originData = null)
    {
        if($this->info->payment_status == 1)
        {
            $updateData["payment_pay_time"] = date("Y-m-d:H-i-s");

            $updateData["payment_origin_data"] = $originData;
            $updateData["payment_prepay_id"] = $prepayId;
            $updateData["payment_status"] = 2;
            if(isset($class))
            {
                $updateData["payment_class"] = $class;
            }


            $requestData= Request::select(["payment"=>$this->info->payment_id]);
            DB::beginTransaction();
            if($requestData["status"] == true &&isset($requestData["data"][0]))
            {
                $requestModel = new Request($requestData["data"][0]->request_id);
                $r1 = $requestModel->setStatusInHandle();
            }
            $taxData = Tax::select(["payment"=>$this->info->payment_id]);
            if($taxData["status"] == true && isset($taxData ["data"][0]))
            {
                $taxModel = new Tax($taxData["data"][0]->tax_id);
                $r1 = $taxModel -> setStatusHavePay();
            }


            $r2 = $this->update($updateData);
            //管理员设定支付单支付状态为已经支付
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员成功设定支付单支付状态为已经支付";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "设定支付单支付状态为已经支付";
            DBLog::adminLog($message, $admin, $level,$otherData);

            if($r1 && $r2 )
            {
                DB::commit();
                return true;
            }
            else
            {
                DB::rollback();
                return false;
            }

        }
        if($this->info->payment_status == 14)
        {
            $updateData["payment_status"] = 2;
            return $this->update($updateData);
        }
        return false;

    }

    /**
     * 设定状态取消支付,它会取消掉相关的请求和缴费
     * @return bool
     */
    public function setStatusCancelPay()
    {
        if($this->info->payment_status == 1) {

            $updateData["payment_status"] = 3;
            $requestData = Request::select(["payment" => $this->info->payment_id]);
            DB::beginTransaction();
            if ($requestData["status"] == true && isset($requestData["data"][0])) {
                $requestModel = new Request($requestData["data"][0]->request_id);
                $r1 = $requestModel->setStatusCancel();
            }
            $taxData = Tax::select(["payment" => $this->info->payment_id]);
            if ($taxData["status"] == true && isset($taxData ["data"][0])) {
                $taxModel = new Tax($taxData["data"][0]->tax_id);
                $r1 = $taxModel->setStatusCancelPay();
            }

            $r2 = $this->update($updateData);
            //管理员设定支付单支付状态为取消支付
            $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员设定支付单支付状态为取消支付";
            $admin = session("admin.admin_id");
            $level = DBLog::INFO;
            $otherData = "设定支付单支付状态为取消支付";
            DBLog::adminLog($message, $admin, $level,$otherData);
            if ($r1 && $r2)
            {
                DB::commit();
                return true;
            }
            else
            {
                DB::rollback();
                return false;
            }
        }
        return false;
    }
    /**
     * 设定状态申请退款
     * @return bool
     */
    public function setStatusAskReturnPay($because=null)
    {
        if($this->info->payment_status == 2)
        {
            if(!empty($because))
            {
                $otherData = json_decode($this->info->payment_other_data,true);
                $otherData["returnPayIntro"] = $because;
                $updateData["payment_other_data"] = json_encode($otherData);
            }
            $updateData["payment_status"] =14;
            if($this->update($updateData))
            {
                //管理员设定支付单支付状态为申请退款成功
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员成功将支付单支付状态设定为申请退款";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "设定支付单支付状态为申请退款";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //管理员设定支付单支付状态为申请退款失败
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员将支付单支付状态设定为申请退款失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "设定支付单支付状态为申请退款";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        return false;
    }
    /**
     * 设定状态退款中
     * @return bool
     */
    public function setStatusInReturnPay()
    {
        if($this->info->payment_status == 14)
        {
            $updateData["payment_status"] =4;
            if($this->update($updateData))
            {
                //管理员设定支付单支付状态为退款中成功
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员成功将支付单支付状态设定为退款中";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "设定支付单支付状态为退款中";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //管理员设定支付单支付状态为退款中失败
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员将支付单支付状态设定为退款中失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "设定支付单支付状态为退款中";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        return false;
    }

    /**
     * 设定状态退款完成
     * @return bool
     */
    public function setStatusAlreadyReturnPay()
    {
        if($this->info->payment_status == 4)
        {
            $updateData["payment_status"] =5;
            if($this->update($updateData))
            {
                //管理员设定支付单支付状态为退款完成成功
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员成功将支付单支付状态设定为退款完成";
                $admin = session("admin.admin_id");
                $level = DBLog::INFO;
                $otherData = "设定支付单支付状态为退款完成";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return true;
            }
            else
            {
                //管理员设定支付单支付状态为退款完成失败
                $message = date("Y-m-d H:i:s").session("admin_admin_nickname")."管理员将支付单支付状态设定为退款完成失败";
                $admin = session("admin.admin_id");
                $level = DBLog::ERROR;
                $otherData = "设定支付单支付状态为退款完成";
                DBLog::adminLog($message, $admin, $level,$otherData);
                return false;
            }
        }
        return false;
    }


    /**
     * 检查是否已经支付
     * @return bool
     */
    public function isPay()
    {
        $this->getInfo();
        return $this->info->payment_status == 2;
    }

    /**
     * 设定预支付单号
     * @param $prepayId
     * @return bool
     */
    public function setPrepayId($prepayId)
    {
       return $this->update(["payment_prepay_id"=>$prepayId]);
    }



}