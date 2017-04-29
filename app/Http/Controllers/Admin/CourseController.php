<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/2
 * Time: 10:47
 */

namespace App\Http\Controllers\Admin;
use MyClass\Base\GuiFunction;
use App\Http\Controllers\Controller;
use MyClass\User\Course;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
use MyClass\User\Monitor;

class CourseController extends Controller
{
    public function __construct()
    {
        if(PowerGroup::checkAdminPower(5)){}
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
    }
    //显示页面
    public function sCourse(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sCourse");
        return view("Admin.Manage.sCourse");
    }

    //查找所有课程数据，条件查找、排序
    public function sAllCourse()
    {
        $data = Course::select(Request::all());
        return response()->json($data);
    }

    //添加课程信息
    public function addCourse()
    {
        $addCourseData = Request::all();
        if($addCourseData['course_name'] != "undefined" && $addCourseData['course_school'] != "undefined" && $addCourseData['course_position'] != "undefined" && $addCourseData['course_date'] != "undefined" && $addCourseData['course_monitor'] != "undefined")
        {
            if(Course::add($addCourseData))
            {
                return response()->json(["status"=>true,"message"=>"添加成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"添加失败"]);
            }
        }
        else
        {
            throw new PowerException("四个表单字段都不能为空！");
        }
    }

    //修改课程信息
    public function updateCourse()
    {
        $updateCourseData = Request::all();
        $updateCourseData['course_monitor'] = (int)substr($updateCourseData['course_monitor'],- 1);
        $course = new Course($updateCourseData['course_id']);
        if($course->update($updateCourseData))
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

    //删除课程信息
    public function deleteCourse()
    {
        $deleteCourseData = Request::all();
        $course = new Course($deleteCourseData['course_id']);
        if($course->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }

    //查询所有的监控
    public function getMonitor()
    {
        $data = Monitor::select($queryLimit = null);
        return response()->json($data);
    }

    //为学校指定监控
    public function appointMonitor()
    {
        $appointMonitorData = Request::all();
        $appointMonitorData['course_monitor'] = (int)substr($appointMonitorData['course_monitor'],- 1);
        $course = new Course($appointMonitorData['course_id']);
        if($course->update($appointMonitorData))
        {
            return response()->json(["status"=>true,"message"=>"设置监控成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"设置监控失败"]);
        }
    }
}