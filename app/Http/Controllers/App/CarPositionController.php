<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 20:35
 */

namespace App\Http\Controllers\App;

use MyClass\Serve\Now;
use MyClass\User\CarPosition;
use MyClass\User\InputCarPosition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use MyClass\System\DBLog;

class CarPositionController extends Controller
{
    public function getCarPosition()
    {
        try
        {
            /*
             * 必须传入一个小区id，不符合标准查询
                |-community_id

                返回标准结构，data里面有数据
                [
                     {//数据一
                         position_id:int //系统内部id
                         position_intro
                         position_private
                         position_tax //每月车位缴费金额
                         position_user //车位所属用户
                         position_community  //车位所属小区
                         position_detail //车位缴费明细
                     },
                     {数据二...},
                     {数据三...}
                 ]
             */
            $queryLimit = Request::all();
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }
            $inputCarPositionData = InputCarPosition::select($queryLimit);
            if($inputCarPositionData['data'] != null)
            {
                foreach($inputCarPositionData['data'] as $key => $value)
                {
                    if($inputCarPositionData['data'][$key]->position_use != 0)
                    {
                        unset($inputCarPositionData['data'][$key]);
                    }
                }
                return response()->json(["status"=>true,"data"=>$inputCarPositionData['data'],"message"=>"正确","result_code"=>0]);
            }
            return response()->json(["status"=>false,"data"=>[],"message"=>"错误","result_code"=>-1]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("获取车位信息失败,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    public function addCarPosition()
    {
        try
        {
            /*
             需要传递下面的信息
                    |-position_id //车位id
             返回标准结构
                返回录入的这条车位信息
            */
            $postData = Request::all();
            if(isset($postData['position_id']))
            {
                $position_id = $postData['position_id'];
                if($model = CarPosition::inputCar($position_id))
                {
                    //dump($model->info);
                    //如果用户购买车位插入成功，随即审核
                    $model->setCarPositionChecked();
                    return response()->json(["status" => true, "message" => "正确", "data" =>$model->info, "result_code" => "0"]);
                }
                else
                {
                    return response()->json(["status" => false, "message" => "错误,此车为已卖出", "data" => null, "result_code" => "-1"]);
                }
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("房子:添加车位信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    public function getPosition()
    {
        /*
         得到自己的车位列表 GET /app_getPosition

         返回标准结构，data里面有数据
         [
             {//数据一
                 position_id:int //系统内部id
                 position_user     //车位所属用户
                 username    //车位所属用户名字
                 position_community //车位所在小区
                 community_name //车位所在小区名字
                 position_tax //每月车位缴费金额
                 position_cantax_time  //车位缴费次数
                 position_now   //追踪缴费月份，也就是缴费缴到了多少月
                 position_check  //是否审核
                 position_intro  //车位位置信息
                 position_area   //车位区域信息
             },
             {数据二...},
             {数据三...}
         ]
         */
        try
        {
            $queryLimit = null;
            if(session("user.user_id") != null)
            {
                $queryLimit['user'] = session("user.user_id");
            }
            $positionData = CarPosition::select($queryLimit);
            if($positionData['data'] != null)
            {
                //移除一些不需要的字段
                $s = count($positionData["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    $data[$i]["position_id"] = $positionData["data"][$i]->position_id;
                    $data[$i]["position_user"] = $positionData["data"][$i]->position_user;
                    $data[$i]["username"] = $positionData["data"][$i]->username;
                    $data[$i]["position_community"] = $positionData["data"][$i]->position_community;
                    $data[$i]["community_name"] = $positionData["data"][$i]->community_name;
                    $data[$i]["position_tax"] = $positionData["data"][$i]->position_tax;
                    $data[$i]["position_cantax_time"] = $positionData["data"][$i]->position_cantax_time;
                    $data[$i]["position_now"] = $positionData["data"][$i]->position_now;
                    $data[$i]["position_check"] = $positionData["data"][$i]->position_check;
                    $data[$i]["position_community_address"] = $positionData["data"][$i]->community_address;
                    $data[$i]["position_input"] = $positionData["data"][$i]->position_input;
                    //$data[$i]["position_intro"] = $positionData["data"][$i]->position_intro;

                    //这里根据position_input连表查询得到相关两个地段
                    $inputData = CarPosition::getInputByPositionId($positionData["data"][$i]->position_input);
                    if($inputData)
                    {
                        $data[$i]["position_intro"] = $inputData->position_intro;
                        $data[$i]["position_area"] = $inputData->position_area;
                    }
                    else
                    {
                        $data[$i]["position_intro"] = null;
                        $data[$i]["position_area"] = null;
                    }
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "错误,没有匹配的数据", "data" => [], "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    public function delCarPosition()
    {
        /*
         删除用户自己的车位   GET /app_delCarPosition

         提供
         |-position_id

         返回标准结构
         */
        try
        {
            $getData = Request::all();
            $carPosition = new CarPosition($getData['position_id']);
            if($carPosition->delete())
            {
                return response()->json(["status" => true, "message" => "正确", "data" => [], "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "错误", "data" => [], "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("车位:删除车位错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}