<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:56
 */

namespace App\Http\Controllers\App;


use App\Http\Controllers\Controller;
use MyClass\User\Car;
use Illuminate\Support\Facades\Request;

class CarController extends Controller
{
    public function getCar()
    {
        /*
         *   符合标准查询接口
    返回标准结构，其中data是一个数组
        [
            {
                car_id:int //系统内部id
                car_name:string,//房屋大小
                car_brand:string,//品牌
                car_color:string,//颜色
                car_model:string,//型号
                car_create_time:string,//创建时间
                car_update_time:string,//更新时间
                car_insurance_start_time:string,//保险开始时间
                car_insurance_end_time:string,//保险结束日期
                car_plate_id:string, //车牌
                car_check:bool,//是否审核
            },
            {...},
            {...}
        ]
         */

        try {
            $queryLimit["user"] = session("user.user_id");
            $array = Car::select($queryLimit);


            if ($array["data"] != null)
            {
                $s = count($array["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    $data[$i]["car_id"] = $array["data"][$i] -> car_id;
                    $data[$i]["car_name"] = $array["data"][$i] -> car_name;
                    $data[$i]["car_brand"] = $array["data"][$i] -> car_brand;
                    $data[$i]["car_color"] = $array["data"][$i] -> car_color;
                    $data[$i]["car_model"] = $array["data"][$i] -> car_model;
                    $data[$i]["car_create_time"] = $array["data"][$i] -> car_create_time;
                    $data[$i]["car_update_time"] = $array["data"][$i] -> car_update_time;
                    $data[$i]["car_insurance_start_time"] = $array["data"][$i] -> car_insurance_start_time;
                    $data[$i]["car_insurance_end_time"] = $array["data"][$i] -> car_insurance_end_time;
                    $data[$i]["car_plate_id"] = $array["data"][$i] -> car_plate_id;
                    $data[$i]["car_check"] = $array["data"][$i] -> car_check;
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"通用错误","data"=>null,"result_code"=>"-1"]);

            }
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("汽车:请求汽车信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }


    public function updateCar()
    {
        /*
         *
        需要发送如下数据：
        car_id:int      //内部id_
        car_name:string,//房屋大小
        car_brand:string,//品牌
        car_color:string,//颜色
        car_model:string,//型号
        car_plate_id:string, //车牌
        忽略字段表示不修改，但必须要id
        返回标准结构
         */
        try
        {
            $carData = Request::all();
            if(isset($carData["access_token"]))
            {
                unset($carData["access_token"]);
            }
            $carData["car_user"] = session("user.user_id");

            $queryLimit["id"] = $carData["car_id"];
            $class = new Car($carData["car_id"]);
            $array = Car::select($queryLimit);
            if($class->info->car_user == session("user.user_id"))
            {
                if ($array["data"] != null)
                {
                    $return = $class->update($carData);
                    if ($return)
                    {
                        $record = $class ->getInfo();
                        return response()->json(["status" => true, "message" => "正确", "data" => $record, "result_code" => "0"]);
                    }
                }
                return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("汽车修改汽车信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    public function addCar()
    {
        /*
     需要发送如下数据：
     car_name:string,//房屋大小
     car_brand:string,//品牌
     car_color:string,//颜色
     car_model:string,//型号
     car_plate_id:string, //车牌
       */
        try
        {
            $carData = Request::all();
            if(isset($carData["access_token"]))
            {
                unset($carData["access_token"]);
            }

            $carData["car_user"] = session("user.user_id");
            $carData["car_check"] = false;
            $carId = Car::add($carData);
            if ($carId != false)
            {
                $class = new Car($carId);
                $record = $class->getInfo();
                if ($record != false)
                {
                    return response()->json(["status" => true, "message" => "正确", "data" => $record, "result_code" => "0"]);
                }
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("汽车:添加汽车信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }


    public function delCar()
    {
        /*
      需要发送车辆id
      id:int  内部id
       */
        try
        {
            $carData = Request::all();
            if(isset($carData["access_token"]))
            {
                unset($carData["access_token"]);
            }
            $carData["car_user"] = session("user.user_id");

            $queryLimit["id"] = $carData["id"];
            $class = new Car($carData["id"]);
            $array = Car::select($queryLimit);
            if($class->info->car_user == session("user.user_id"))
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
        catch(\Exception $e)
        {
            DBLog::SystemLog("汽车:删除汽车信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }



}