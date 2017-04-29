<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/26
 * Time: 11:35
 */

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use MyClass\User\Community;
use MyClass\System\DBLog;
use Illuminate\Support\Facades\Request;


class CommunityController extends Controller
{
    //getCommunityInfo

    public function getCommunityInfo()
    {
        /*
         *  标准查询
            |-key 可以按照小区名搜索

            返回信息
            标准结构 data中形式如下
            [
                {
                    community_id:id              小区id
                    community_name:以下全是string 小区名
                    community_address            小区地址
                    community_intro              小区介绍
                    community_create_time       创建时间
                    community_update_time       更新时间
                    community_city              所在城市
                    community_province          所在省份
                },
                {...},
                {...}
            ]
         *
         */

        try
        {
            $community = Request::all();
            if(isset($community["access_token"]))
            {
                unset($community["access_token"]);
            }

            $queryLimit = $community;
            /*if(isset($community["community_name"]))
            {
                $queryLimit["community_name"] = $community["community_name"];
            }*/

            $array = Community::select($queryLimit);
            if ($array["data"] != null)
            {
                $s = count($array["data"]);
                for($i = 0 ;$i < $s;$i ++)
                {
                    $data[$i]["community_id"] = $array["data"][$i]->community_id;
                    $data[$i]["community_name"] = $array["data"][$i]->community_name;
                    $data[$i]["community_address"] = $array["data"][$i]->community_address;
                    $data[$i]["community_intro"] = $array["data"][$i]->community_intro;
                    $data[$i]["community_create_time"] = $array["data"][$i]->community_create_time;
                    $data[$i]["community_update_time"] = $array["data"][$i]->community_update_time;
                    $data[$i]["community_city"] = $array["data"][$i]->community_city;
                    $data[$i]["community_province"] = $array["data"][$i]->community_province;
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            return response()->json(["status" => false, "message" => "通用错误", "data" => null, "result_code" => "-1"]);
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("小区:获取小区信息错误,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

}