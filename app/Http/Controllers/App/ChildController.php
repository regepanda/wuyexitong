<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/26
 * Time: 15:46
 */

namespace App\Http\Controllers\App;
use App\Http\Controllers\Controller;
use MyClass\User\Child;
use Illuminate\Support\Facades\Request as SysRequest;

class ChildController extends Controller
{

    //查看自己孩子的信息（4点半学校): GET /app_getChild
    public function getChild()
    {
        try {
               /*
                返回信息
                标准结构
                [
                    {
                        child_id:int                id
                        child_name:string           名字
                        child_school:string         学校
                        child_age:int               年龄
                        child_interest:string       兴趣
                        child_sex:string            性别
                        child_m_phone               监护人电话
                        child_create_time:string    创建时间
                        child_update_time:string    更新时间
                        monitor_id                  孩子学校监控ID
                        monitor_name                孩子学校监控名字
                    },
                    {...},
                    {...}
                ]
                */
            $queryLimit = SysRequest::all();
            $queryLimit["user"] = session("user.user_id");
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }


            if ($childData = Child::select($queryLimit))
            {
                /*unset($childData['total']);
                $childData['result_code'] = 0;
                foreach ($childData['data'] as $key => $value)
                {
                    unset($childData['data'][$key]->child_user);
                }
                return response()->json($childData);*/
                $s = count($childData['data']);
                for($i = 0;$i<$s;$i++)
                {
                    $data[$i]["child_id"] = $childData["data"][$i]->child_id;
                    $data[$i]["child_name"] = $childData["data"][$i]->child_name;
                    $data[$i]["child_school"] = $childData["data"][$i]->child_school;
                    $data[$i]["child_age"] = $childData["data"][$i]->child_age;
                    $data[$i]["child_interest"] = $childData["data"][$i]->child_interest;
                    $data[$i]["child_sex"] = $childData["data"][$i]->child_sex;
                    $data[$i]["child_m_phone"] = $childData["data"][$i]->child_m_phone;
                    $data[$i]["child_m_phone_2"] = $childData["data"][$i]->child_m_phone_2;
                    $data[$i]["child_create_time"] = $childData["data"][$i]->child_create_time;
                    $data[$i]["child_update_time"] = $childData["data"][$i]->child_update_time;
                    $data[$i]["monitor_id"] = $childData["data"][$i]->monitor_id;
                    $data[$i]["monitor_name"] = $childData["data"][$i]->monitor_name;
                }
                return response()->json(["status" => true, "message" => "正确", "data" => $data, "result_code" => "0"]);
            }
            else
            {
                //查询失败的返回码
                $childData['result_code'] = -1;
                $childData['message'] = "查询失败";
                $childData['status'] = false;
                $childData['data'] = null;
                return response()->json($childData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //添加孩子的信息 POST /app_addChild
    public function addChild()
    {
        try {
            /*
                提供数据
                |-child_name
                |-child_school
                |-child_monitor

                |-child_age
                |-child_interest
                |-child_sex
                |-child_m_phone
                |-child_m_phone_2

                返回标准结构，status表示是否成功
                data返回这一次插入的数据，结构是查询中的一条
             */
            $dataArray = SysRequest::all();

            if(isset($dataArray["access_token"]))
            {
                unset($dataArray["access_token"]);
            }
            $dataArray["child_user"] = session("user.user_id");

            if ($childId = Child::add($dataArray))
            {
                $child = new Child($childId);
                $returnData['status'] = true;
                $returnData['message'] = '添加成功';
                $returnData['result_code'] = 0;
                $returnData['data'] = $child->info;
                return response()->json($returnData);
            }
            else {
                //添加孩子失败返回结果码
                $returnData['result_code'] = -1;
                $returnData['message'] = "添加失败";
                $returnData['status'] = false;
                $returnData['data'] = [];
                return response()->json($returnData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //更新孩子的信息 POST/app_updateChild
    public function updateChild()
    {
        try {
            /*
             * 提供数据
                |-child_id
                |-child_name

                |-child_monitor

                |-child_age
                |-child_interest
                |-child_sex
                |-child_m_phone
                |-child_m_phone2
                返回标准结构 data中存放更新的数据
             */
            $dataArray = SysRequest::all();
            if(isset($dataArray["access_token"]))
            {
                unset($dataArray["access_token"]);
            }
            $dataArray["child_user"] = session("user.user_id");

            $child = new Child($dataArray['child_id']);
            if($child->info->id != session("user.user_id"))
            {
                throw \Exception("不能操作其他用户的数据");
            }

            if ($updateChild = $child->update($dataArray))
            {
                $returnData['status'] = true;
                $returnData['message'] = '更新成功';
                $returnData['result_code'] = 0;
                $returnData['data'] = $updateChild;
                return response()->json($returnData);
            }
            else
            {
                //修改孩子失败返回结果码
                $returnData['result_code'] = -1;
                $returnData['message'] = "更新失败";
                $returnData['status'] = false;
                $returnData['data'] = [];
                return response()->json($returnData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //删除孩纸的信息 GET/app_delChild
    public function delChild()
    {
        try {
            /*
             * 提供
                |-child_id

                返回标准结构
             */
            $delChildData = SysRequest::all();
            $child = new Child($delChildData['child_id']);
            if($child->info->id != session("user.user_id"))
            {
                throw \Exception("不能操作其他用户的数据");
            }


            if ($child->delete()) {
                $returnData['status'] = true;
                $returnData['message'] = '删除成功';
                $returnData['result_code'] = 0;
                $returnData['data'] = [];
                return response()->json($returnData);
            } else {
                //删除孩子失败返回结果码
                $returnData['result_code'] = -1;
                $returnData['message'] = "删除失败";
                $returnData['status'] = false;
                $returnData['data'] = [];
                return response()->json($returnData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}