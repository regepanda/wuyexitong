<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/26
 * Time: 16:20
 */

namespace App\Http\Controllers\Admin;
use MyClass\Base\GuiFunction;
use App\Http\Controllers\Controller;
use MyClass\User\Child;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
use MyClass\User\Course;

class ChildController extends Controller
{
    public function __construct()
    {
        if(PowerGroup::checkAdminPower(2))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
    }

    //显示页面
    public function sChild(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sUser");
        $guiFunc->setThirdModule("sChild");
        return view("Admin.Manage.sChild");
    }

    //查找所有儿童数据，条件查找、排序
    public function allChild()
    {
        $queryLimit = Request::all();
        if(session("admin.community_group") != null)
        {
            $queryLimit['admin_community_group'] = session("admin.community_group");
        }
        $data = Child::select($queryLimit);
        return response()->json($data);
    }

    //儿童详情
    public function sChildDetail()
    {
        $sChildDetailData = Request::all();
        $child = new Child($sChildDetailData['child_id']);
        return response()->json($child->info);
    }

    //得到所有的课程信息
    public function getCourse()
    {
        $data = Course::select();
        return response()->json($data);
    }

    //添加儿童信息
    public function addChild()
    {
        $addChildData = Request::all();
        if
        (
            $addChildData['child_name'] != "undefined"
            && $addChildData['child_age'] != "undefined"
            && $addChildData['child_user'] != "undefined"
            && $addChildData['child_start'] != "undefined"
            && $addChildData['child_end'] != "undefined"
            && $addChildData['child_course'] != "undefined"
        )
        {
            if(is_numeric($addChildData['child_user']))
            {
                if(Child::add($addChildData))
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
                return response()->json(["status"=>false,"message"=>"添加失败,用户必须为整数"]);
            }
        }
        return response()->json(["status"=>false,"message"=>"添加失败,所有字段都不能为空！"]);
    }

    //修改儿童信息
    public function childUpdate()
    {
        $childUpdateData = Request::all();
        if(!empty($childUpdateData['child_name']) || !empty($childUpdateData['child_age']) || !empty($childUpdateData['child_course']))
        {
            $child = new Child($childUpdateData['child_id']);
            if($child->update($childUpdateData) != false)
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
        }
        else
        {
            throw new PowerException("三个表单字段都不能全为空");
        }
    }

    //删除
    public function deleteUpdate()
    {
        $deleteUpdateData = Request::all();
        $child = new Child($deleteUpdateData['child_id']);
        if($child->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>true,"message"=>"删除失败"]);
        }
    }

    //给孩子分配学校
    public  function distributionSchool()
    {
        $distributionSchoolData = Request::all();
        $distributionSchoolData['child_course'] = (int)substr($distributionSchoolData['child_course'],- 1);
        $child = new Child($distributionSchoolData['child_id']);
        if($child->update($distributionSchoolData) != false)
        {
            return response()->json(["status"=>true,"message"=>"分配学校成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"分配学校失败"]);
        }
    }
}