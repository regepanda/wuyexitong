<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:56
 */

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use MyClass\Serve\Now;
use MyClass\System\DBLog;
use MyClass\User\House;
use Illuminate\Support\Facades\Request;
use MyClass\User\InputHouseData;

class HouseController extends Controller
{
    public function getHouse()
    {
        /*
         */

        try {
            $queryLimit["user"] = session("user.user_id");
            $array = House::select($queryLimit);
            //dump($array);
            if ($array["data"] != null)
            {
                $s = count($array["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    $data[$i]["house_id"] = $array["data"][$i]->house_id;
                    $data[$i]["house_area"] = $array["data"][$i]->house_area;
                    $data[$i]["house_address"] = $array["data"][$i]->house_address;
                    $data[$i]["house_create_time"] = $array["data"][$i]->house_create_time;
                    $data[$i]["house_update_time"] = $array["data"][$i]->house_update_time;
                    $data[$i]["house_check"] = $array["data"][$i]->house_check;
                    $data[$i]["house_cantax_time"] = $array["data"][$i]->house_cantax_time;
                    $data[$i]["house_community"] = $array["data"][$i]->house_community;
                    $data[$i]["community_name"] = $array["data"][$i]->community_name;//2016/4/13 wjt增加
                    //$data[$i]["house_detail"] = $array["data"][$i]->data_detail;//2016/4/13 wjt增加
                    $data[$i]["house_now"] = Now::getMapping($array["data"][$i]->house_now);
                    $data[$i]["house_tax_total"] = $array["data"][$i]->data_tax;
                    $data[$i]["house_tax_unit"] = sprintf("%.2f", $array["data"][$i]->data_tax/$array["data"][$i]->house_area);

                    if(isset($array["data"][$i]->data_detail))
                    {
                        $house_detail = json_decode($array["data"][$i]->data_detail);
                        foreach($house_detail as $key => $value)
                        {
                            $data[$i]["house_detail"][][$key] = $value;
                        }
                    }
                    else
                    {
                        $data[$i]["house_detail"][] = null;
                    }
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "没有数据", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("房子:获取房子信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    /*public function updateHouse()
    {

        //2016/4/13，房屋改为录入后，前端应该不能调用它了
        /*
         *
         需要传递下面的信息
            |-area(面积)
            |-address(地址)
            |-id(房屋id)
            |-community(小区id)
        忽略字段表示不修改，但必须要id
        房屋修改信息的app接口修改为一旦修改信息，审核(house_check)设为false
        返回标准结构


        try
        {
            $houseData = Request::all();
            $updateData["house_id"] = $houseData["id"];
            $updateData["house_address"] = $houseData["address"];
            $updateData["house_area"] = $houseData["area"];
            $updateData["house_community"] = $houseData["community"];
            $updateData["house_cantax_time"] = (int)$houseData["cantax_time"];
            $updateData["house_check"] = false;
            $queryLimit["id"] = $houseData["id"];
            $class = new House($houseData["id"]);
            $array = House::select($queryLimit);
            if($class->info->house_user == session("user.user_id"))
            {
                if ($array["data"] != null)
                {
                    $update =  $class ->update($updateData);
                    if ($update)
                    {
                        $record = $class ->getInfo();
                        if($record)
                        {
                            return response()->json(["status" => true, "message" => "正确", "data" => $record, "result_code" => "0"]);
                        }
                    }
                }
                return response()->json(["status" => false, "message" => "通用错误", "data" => [], "result_code" => "-1"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => [], "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("房子:修改房子信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }

    }*/
    /*public function addHouse()
    {
        /*
         需要传递下面的信息
            |-data_id 安卓这边在最后一个下拉选择后需把房号id过来【在选择最后一个房后下拉之后点击录入时传入】
         返回标准结构


        try
        {
            $postData = Request::only("data_id");
            //这里对前端传过来的data_id进行查询，拼接一个完整的房子地址
            if(isset($postData['data_id']))
            {
                $data_id = $postData['data_id'];
                if($model = House::inputHouse($data_id))
                {
                    $model->setHouseChecked();
                    return response()->json(["status" => true, "message" => "正确", "data" => $model->info, "result_code" => "0"]);
                }
                else
                {
                    return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
                }
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("房子:添加房子信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }*/

    public function addHouse()
    {
        /*
         需要传递下面的信息
         |-data_id 安卓这边在最后一个下拉选择后需把房号id过来【在选择最后一个房后下拉之后点击录入时传入】
         返回标准结构
        */
        try
        {
            $postData = Request::only("data_id");
            if(isset($postData['data_id']))
            {
                $data_id = $postData['data_id'];
                if($model = House::updateInputHouse($data_id))
                {
                    $model->setHouseChecked();
                    return response()->json(["status" => true, "message" => "正确", "data" => $model->info, "result_code" => "0"]);
                }
                else
                {
                    return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
                }
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("房子:添加房子信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    public function delHouse()
    {
        /*

        删除房屋信息  GET  /app_delHouse
        需要传递下面的信息
        |-id      (系统内部id)

        返回标准结构
        */
        try
        {
            $data = Request::all();
            $queryLimit["id"] = $data["id"];
            $class = new House($data["id"]);
            $array = House::select($queryLimit);
            if($class->info->house_user == session("user.user_id"))
            {
                if ($array["data"] != null)
                {
                    $return = $class->delete();
                    if ($return)
                    {
                        return response()->json(["status" => true, "message" => "正确", "data" => [], "result_code" => "0"]);
                    }
                }
                return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch (\Exception $e)
        {
            DBLog::SystemLog("房子:删除房子信息错误," . $e->getMessage(), DBLog::ERROR);
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

    public function getInputHouseData()
    {
        /*
        传递参数
        |-community_id  小区id
        |-parent_id  id等级，根据它可以确定查询的是楼还是单元还是房号
        */
        try
        {
            $postData = Request::only("community_id","parent_id");

            if($postData['parent_id'] == 0)
            {
                $queryLimit['data_top'] = 1;
            }
            else
            {
                $queryLimit['data_parent'] = $postData['parent_id'];
            }
            $queryLimit['data_community'] = $postData['community_id'];
            $returnData = InputHouseData::select($queryLimit);
            if($returnData['data'] != null)
            {
                /*foreach($returnData['data'] as $key => $value)
                {
                    if($returnData['data'][$key]->data_use != 0)
                    {
                        unset($returnData['data'][$key]);
                    }
                }*/
                return response()->json(["status"=>true,"data"=>$returnData['data'],"message"=>"正确","result_code"=>0]);
            }
            return response()->json(["status"=>false,"data"=>[],"message"=>"错误","result_code"=>-1]);
        }
        catch (\Exception $e)
        {
            return response()->json(["status" => false, "data" => [], "message" => "程序内部错误" . $e->getMessage(), "result_code" => -1]);
        }
    }

}