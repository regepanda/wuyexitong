<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/14
 * Time: 16:47
 */

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use MyClass\User\Monitor;
use Illuminate\Support\Facades\Request;

class MonitorController extends Controller
{
    //获取所有的监控 GET /app_getMonitor
    public function getMonitor()
    {
        /*
        返回标准结构，data里面有数据
        [
             {//数据一
                 monitor_id:int //系统内部id
                 monitor_name  //监控名称
                 monitor_create_time  //监控创建时间
                 monitor_update_time  //监控更新时间
                 monitor_device_id    //监控设备id
                 monitor_device_password //密码
                 monitor_device_area        //区域
             },
             {数据二...},
             {数据三...}
         ]
         */
        try
        {
            $queryLimit = Request::all();
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }
            $monitorData = Monitor::select($queryLimit);
            if($monitorData['data'] != null)
            {
                $s = count($monitorData['data']);
                for($i=0;$i<$s;$i++)
                {
                    $data[$i]['monitor_id'] = $monitorData['data'][$i]->monitor_id;
                    $data[$i]['monitor_name'] = $monitorData['data'][$i]->monitor_name;
                    $data[$i]['monitor_create_time'] = $monitorData['data'][$i]->monitor_create_time;
                    $data[$i]['monitor_update_time'] = $monitorData['data'][$i]->monitor_update_time;
                    $data[$i]['monitor_device_id'] = $monitorData['data'][$i]->monitor_device_id;
                    $data[$i]['monitor_device_password'] = $monitorData['data'][$i]->monitor_device_password;
                    $data[$i]['monitor_device_area'] = $monitorData['data'][$i]->monitor_device_area;
                }
                return response()->json(["status"=>true,"message"=>"成功获取到数据","result_code"=>0,"data"=>$data]);
            }
            else
            {
                return response()->json(["status"=>true,"message"=>"通用错误！","result_code"=>-1,"data"=>[]]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}