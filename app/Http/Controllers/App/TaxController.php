<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/21
 * Time: 22:34
 */

namespace App\Http\Controllers\App;
use App\Http\Controllers\Controller;
use MyClass\Serve\Tax;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\User\House;
use MyClass\User\CarPosition;
use MyClass\Serve\Now;
use MyClass\User\InputHouseData;

class TaxController extends Controller
{

    /*public function getTax()
    {
        try{

            /*
                 * 符合标准查询结构
                    需要参数
                    |-tax_house  【如果是传入的是房屋】
                    |-tax_car_position  【如果是传入的是车位】
                   返回标准结构，其中data是一个数组
                    [
                        {//数据一
                                tax_id:int //系统内部id
                                tax_class:int,//类别码
                                tax_payment:int,//支付单
                                tax_user:int ,//关联用户
                                tax_status:int,         //状态码
                                tax_create_time:string,//创建时间
                                tax_update_time:string,//更新时间
                                tax_deadline:string,//截止日期
                                tax_pay_time:string,//支付日期
                                tax_intro:string //介绍
                                house_detail        //json 房子详情
                                tax_detail          //json 缴费明细
                        },
                        {数据二...},
                        {数据三...}
                    ]

            $postData = SysRequest::all();
            if(isset($postData['access_token']))
            {
                unset($postData["access_token"]);
            }

            if(session("user.user_id"))
            {
                $postData['user'] = session("user.user_id");
            }

            if($taxData = Tax::select($postData))
            {
                //$data = null;
                //移除一些不需要的字段
                $s = count($taxData["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    //只查询缴费成功的
                    if($taxData["data"][$i]->payment_status == 2)
                    {
                        $data[$i]["tax_id"] = $taxData["data"][$i]->tax_id;
                        $data[$i]["tax_class"] = $taxData["data"][$i]->tax_class;
                        $data[$i]["tax_payment"] = $taxData["data"][$i]->tax_payment;
                        $data[$i]["tax_user"] = $taxData["data"][$i]->tax_user;
                        $data[$i]["tax_status"] = $taxData["data"][$i]->tax_status;
                        $data[$i]["tax_create_time"] = $taxData["data"][$i]->tax_create_time;
                        $data[$i]["tax_update_time"] = $taxData["data"][$i]->tax_update_time;
                        $data[$i]["tax_deadline"] = $taxData["data"][$i]->tax_deadline;
                        $data[$i]["tax_pay_time"] = $taxData["data"][$i]->tax_pay_time;
                        $data[$i]["tax_intro"] = $taxData["data"][$i]->tax_intro;
                        $data[$i]["tax_price"] = $taxData["data"][$i]->payment_price;

                        if(isset($taxData["data"][$i]->tax_other_data))
                        {
                            $taxOtherData = json_decode($taxData["data"][$i]->tax_other_data);
                            foreach($taxOtherData as $key => $value)
                            {
                                $data[$i]["tax_detail"][$key] = $value;
                            }
                        }
                        else
                        {
                            $data[$i]["tax_detail"] = null;
                        }
                        if(isset($taxData['data'][$i]->tax_house))
                        {
                            //如果请求的是房屋缴费单,为每条缴费记录返回房子信息
                            $house = new House($taxData['data'][$i]->tax_house);
                            $data[$i]["house_detail"] = $house->info;
                            $data[$i]["house_detail"]->house_tax_total = $taxData["data"][$i]->payment_price;
                            $data[$i]["house_detail"]->house_tax_unit = sprintf("%.2f", $taxData["data"][$i]->payment_price/$house->info->house_area);
                            $data[$i]["tax_house"] = $taxData["data"][$i]->tax_house;
                        }
                        if(isset($taxData['data'][$i]->tax_car_position))
                        {
                            //如果请求的是车位缴费单,为每条缴费记录返回车位信息
                            $carPosition = new CarPosition($taxData['data'][$i]->tax_car_position);
                            $data[$i]["position_detail"] = $carPosition->info;
                            $data[$i]["position_detail"]->position_comunity_address = $carPosition->info->position_comunity_address;
                            $data[$i]["position_detail"]->position_now = Now::getMapping($carPosition->info->position_now);
                            $data[$i]["position_detail"]->position_tax_total = $taxData["data"][$i]->payment_price;
                            $data[$i]["tax_car_position"] = $taxData["data"][$i]->tax_car_position;
                        }
                    }
                }
                if($data == null)
                {
                    return response()->json(["status" => false, "message" => "查询失败，没有相匹配的数据", "data" => $data, "result_code" => "-1"]);
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            else
            {
                //查询失败返回码
                $taxData['result_code'] = -1;
                $taxData['message'] = "查询失败，没有相匹配的数据";
                $taxData['status'] = false;
                $taxData['data'] = null;
                return response()->json($taxData);
            }

        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }*/

    public function getTax()
    {
        try{

            /*
                 * 符合标准查询结构
                    需要参数
                    |-tax_house  【如果是传入的是房屋】
                    |-tax_car_position  【如果是传入的是车位】
                   返回标准结构，其中data是一个数组
                    [
                        {//数据一
                                tax_id:int //系统内部id
                                tax_class:int,//类别码
                                tax_payment:int,//支付单
                                tax_user:int ,//关联用户
                                tax_status:int,         //状态码
                                tax_create_time:string,//创建时间
                                tax_update_time:string,//更新时间
                                tax_deadline:string,//截止日期
                                tax_pay_time:string,//支付日期
                                tax_intro:string //介绍
                                house_detail        //json 房子详情
                                tax_detail          //json 缴费明细
                        },
                        {数据二...},
                        {数据三...}
                    ]
                 */
            $postData = SysRequest::all();
            if(isset($postData['access_token']))
            {
                unset($postData["access_token"]);
            }

            if(session("user.user_id"))
            {
                $postData['user'] = session("user.user_id");
            }
            $taxData = Tax::select($postData);
            if($taxData['data'] != null)
            {
                //把没有支付成功的缴费单记录过滤掉
                foreach($taxData['data'] as $key => $value)
                {
                    if($value->payment_status != 2)
                    {
                        unset($taxData['data'][$key]);
                    }
                }
                //dump($taxData);
                //移除一些不需要的字段
                $i =0;
                foreach($taxData['data'] as $key => $value)
                {
                    $data[$i]["tax_id"] = $taxData['data'][$key]->tax_id;
                    $data[$i]["tax_class"] = $taxData['data'][$key]->tax_class;
                    $data[$i]["tax_payment"] = $taxData['data'][$key]->tax_payment;
                    $data[$i]["tax_user"] = $taxData['data'][$key]->tax_user;
                    $data[$i]["tax_status"] = $taxData['data'][$key]->tax_status;
                    $data[$i]["tax_create_time"] = $taxData['data'][$key]->tax_create_time;
                    $data[$i]["tax_update_time"] = $taxData['data'][$key]->tax_update_time;
                    $data[$i]["tax_deadline"] = $taxData['data'][$key]->tax_deadline;
                    $data[$i]["tax_pay_time"] = $taxData['data'][$key]->tax_pay_time;
                    $data[$i]["tax_intro"] = $taxData['data'][$key]->tax_intro;
                    $data[$i]["tax_price"] = $taxData['data'][$key]->payment_price;

                    //如果请求的是房屋缴费单,为每条缴费记录返回房子信息
                    if(isset($taxData['data'][$key]->tax_house))
                    {
                        $house = new House($taxData['data'][$key]->tax_house);
                        $data[$i]["house_detail"] = $house->info;
                        $data[$i]["house_detail"]->house_tax_total = $taxData['data'][$key]->payment_price;
                        $data[$i]["house_detail"]->house_tax_unit = sprintf("%.2f", $taxData['data'][$key]->payment_price/$house->info->house_area);
                        $data[$i]["tax_house"] = $taxData['data'][$key]->tax_house;
                    }
                    //如果请求的是车位缴费单,为每条缴费记录返回车位信息
                    if(isset($taxData['data'][$key]->tax_car_position))
                    {
                        $carPosition = new CarPosition($taxData['data'][$key]->tax_car_position);
                        $data[$i]["position_detail"] = $carPosition->info;
                        $data[$i]["position_detail"]->position_comunity_address = $carPosition->info->position_comunity_address;
                        $data[$i]["position_detail"]->position_now = Now::getMapping($carPosition->info->position_now);
                        $data[$i]["position_detail"]->position_tax_total = $taxData['data'][$key]->payment_price;
                        $data[$i]["tax_car_position"] = $taxData['data'][$key]->tax_car_position;
                    }
                    //缴费详情
                    if(isset($taxData['data'][$key]->tax_other_data))
                    {

                        $taxOtherData = json_decode($taxData['data'][$key]->tax_other_data);
                        foreach($taxOtherData as $key => $value)
                        {
                            $data[$i]["tax_detail"][][$key] = $value;
                        }
                    }
                    else
                    {
                        $data[$i]["tax_detail"][] = null;
                    }
                    $i++;
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            else
            {
                //查询失败返回码
                return response()->json(["status" => false, "message" => "查询失败,没有匹配的数据", "data" => [], "result_code" => "-1"]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //添加缴费单
    public function addTax()
    {
        /*
             POST
            |-tax_class 缴费类型
            |-house_id:int //如果缴费的是房屋，那么传入房屋id
            |-position_id //如果缴费的是车位，那么传入车位id
            |-tax_time  int  //缴费次数
             通知系统添加一个缴费单

             返回数据，标准结构，data中包含有这个缴费单的详细数据
             tax_id:int //系统内部id
             tax_class:int,//类别码
             tax_payment:int,//支付单
             tax_user:int ,//关联用户
             tax_status:int,         //状态码
             tax_create_time:string,//创建时间
             tax_update_time:string,//更新时间
             tax_deadline:string,//截止日期
             tax_pay_time:string,//支付日期
             tax_intro:string //介绍

             tax_detail {"x":"xx","x":"xx"} //缴费详细信息
             house_detail {"x":"xx","x":"xx"} //添加此缴费单时返回该缴费房子的详细信息 【如果是交物业费】
             position_detail {"x":"xx","x":"xx"} //添加此缴费单时返回该缴费车位的详细信息 【如果是交停车费】
             */
        try
        {

            $postData = SysRequest::all();
            if(isset($postData['access_token']))
            {
                unset($postData["access_token"]);
            }
            //如果没有传入tax_time的话，默认缴费一次
            if(!isset($postData['tax_time']))
            {
                $postData['tax_time'] = 1;
            }
            //判断缴费类型
            if($postData['tax_class'] == 14)
            {
                //交物业费  tax_class  tax_month  house_id
                $hose =new House($postData['house_id']);
                $inputHouse = new InputHouseData($hose->info->house_where);
                $price = $inputHouse->info->data_tax * $postData['tax_time'];
                if ($tax = Tax::addTaxByUserAsk(session("user.user_id"), $postData['tax_class'], $price, $postData['tax_time'],$postData['house_id']))
                {
                    //缴费成功后把缴费明细写入该条缴费单里
                    $house = new House($postData['house_id']);
                    $inputDataHouse = new InputHouseData($house->info->house_where);
                    $dataArray['tax_other_data'] = $inputDataHouse->info->data_detail;
                    $tax->update($dataArray);
                    unset($tax->info->status_name);
                    unset($tax->info->class_name);
                    unset($tax->info->user_username);
                    unset($tax->info->user_nickname);
                    unset($tax->info->payment_id);
                    unset($tax->info->payment_create_time);
                    unset($tax->info->payment_update_time);
                    unset($tax->info->payment_status);
                    //unset($tax->info->tax_other_data);
                    //返回缴费房子的详细信息
                    $tax->info->house_detail["house_area"] = $inputDataHouse->info->house_area;
                    $tax->info->house_detail["house_address"] = $inputDataHouse->info->house_address;
                    $tax->info->house_detail["house_user"] = $house->info->house_user;
                    $tax->info->house_detail["house_check"] = $house->info->house_check;
                    $tax->info->house_detail["house_tax"] = $inputDataHouse->info->data_tax * $postData['tax_time'];
                    $tax->info->house_detail["house_now"] = Now::getMapping($inputDataHouse->info->house_now);

                    //返回缴费信息
                    if(isset($tax->info->tax_other_data))
                    {
                        $tax_detail = json_decode($tax->info->tax_other_data);
                        foreach($tax_detail as $key => $value)
                        {
                            $tax->info->tax_detail[$key] = $value;
                        }
                    }
                    else
                    {
                        $tax->info->tax_detail = null;
                    }

                    $returnData['result_code'] = 0;
                    $returnData['status'] = true;
                    $returnData['data'] = $tax->info;
                    $returnData['message'] = "缴费成功";
                    return response()->json($returnData);
                }
                else
                {
                    //添加失败返回码
                    $returnData['result_code'] = -1;
                    $returnData['message'] = "缴费失败";
                    $returnData['status'] = false;
                    $returnData['data'] = [];
                    return response()->json($returnData);
                }
            }
            if($postData['tax_class'] == 18)
            {
                //交停车费  tax_class  tax_month  position_id
                $Position =new CarPosition($postData['position_id']);
                $price = $Position->info->position_tax * $postData['tax_time'];
                if ($tax = Tax::addPositionTaxByUserAsk(session("user.user_id"), $postData['tax_class'], $price, $postData['tax_time'],$postData['position_id']))
                {
                    $carPosition =new CarPosition($postData['position_id']);
                    //缴费成功后把缴费明细写入该条缴费单里
                    $dataArray['tax_other_data'] = $carPosition->info->position_detail;
                    $tax->update($dataArray);
                    unset($tax->info->status_name);
                    unset($tax->info->class_name);
                    unset($tax->info->user_username);
                    unset($tax->info->user_nickname);
                    unset($tax->info->payment_id);
                    unset($tax->info->payment_create_time);
                    unset($tax->info->payment_update_time);
                    unset($tax->info->payment_status);
                    unset($tax->info->tax_price);
                    //返回缴费车位的详细信息
                    $tax->info->position_detail["position_user"] = $carPosition->info->position_user;
                    $tax->info->position_detail["position_community"] = $carPosition->info->position_community;
                    $tax->info->position_detail["position_tax"] = $carPosition->info->position_tax;
                    $tax->info->position_detail["position_check"] = $carPosition->info->position_check;
                    $tax->info->position_detail["position_cantax_time"] = $carPosition->info->position_cantax_time;
                    $tax->info->position_detail["position_now"] = Now::getMapping($carPosition->info->position_now);
                    //返回缴费信息
                    if(isset($tax->info->tax_other_data))
                    {
                        $tax_detail = json_decode($tax->info->tax_other_data);
                        foreach($tax_detail as $key => $value)
                        {
                            $tax->info->tax_detail[$key] = $value;
                        }
                    }
                    else
                    {
                        $tax->info->tax_detail = null;
                    }
                    $returnData['result_code'] = 0;
                    $returnData['status'] = true;
                    $returnData['data'] = $tax->info;
                    $returnData['message'] = "缴费成功";
                    return response()->json($returnData);
                }
                else
                {
                    //添加失败返回码
                    $returnData['result_code'] = -1;
                    $returnData['message'] = "你的缴费次数已不足";
                    $returnData['status'] = false;
                    $returnData['data'] = [];
                    return response()->json($returnData);
                }
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}
