<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/4/7
 * Time: 14:15
 */
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use MyClass\System\DBLog;
use MyClass\Serve\Serve;

class ServeController extends Controller
{
    public function getServer()
    {
        /*
         * 获取服务类型 GET /app_getServer
        不符合标准查询
        符合标准返回
        data中有所有的服务类型
        data:{
            "class_name":string
            "class_id":
        }
         */

        try {
           $queryLimit["desc"] = true;
            $queryLimit["newServe"] = true;
            $array = Serve::select($queryLimit);

            if ($array["data"] != null) {
                $s = count($array["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    $data[$i]["class_id"] = $array["data"][$i] -> class_id;
                    $data[$i]["class_name"] = $array["data"][$i] -> class_name;
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            else {
                return response()->json(["status"=>false,"message"=>"通用错误","data"=>null,"result_code"=>"-1"]);
            }
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("服务:请求服务信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }




}