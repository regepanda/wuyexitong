<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/19
 * Time: 20:52
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use MyClass\Base\GuiFunction;
use MyClass\User\Monitor;

class MonitorController extends Controller
{
    //显示页面
    public function sMonitor(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sMonitor");
        return view("Admin.Manage.sMonitor");
    }

    //查询所有的监控信息
    public function sAllMonitor()
    {
        $queryLimit = Request::all();
        $data = Monitor::select($queryLimit);
        return response()->json($data);
    }

    //添加
    public function addMonitor()
    {
        $addMonitorData = Request::all();
        if($addMonitorData['monitor_name'] != 'undefined' && $addMonitorData['monitor_device_id'] != 'undefined' && $addMonitorData['monitor_device_password'] != 'undefined' && $addMonitorData['monitor_device_area'] != 'undefined')
        {
            if(Monitor::add($addMonitorData))
            {
                return response()->json(["status"=>true,"message"=>"添加成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"添加失败"]);
            }
        }
        return response()->json(["status"=>false,"message"=>"所有字段都必须填写"]);
    }

    //修改
    public function updateMonitor()
    {
        $updateMonitorData = Request::all();
        $monitor = new Monitor($updateMonitorData['monitor_id']);
        if($monitor->update($updateMonitorData))
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

    //删除
    public function deleteMonitor()
    {
        $deleteMonitorData = Request::all();
        $monitor = new Monitor($deleteMonitorData['monitor_id']);
        if($monitor->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }
}