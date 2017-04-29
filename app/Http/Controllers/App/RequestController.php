<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/21
 * Time: 20:57
 */

namespace App\Http\Controllers\App;
use App\Http\Controllers\Controller;
use MyClass\Serve\Request;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\System\Image;

class RequestController extends Controller
{
    public function request()
    {
        return view("Test.pl.pl");
    }

    //获取请求:GET /app_getRequest
    public function getRequest()
    {
        try
        {
            /*
             * 符合标准格式，可以查询到用户请求的服务
                返回标准结构，其中data是一个数组
                [
                    {//数据一
                        request_id:int //系统内部id
                        request_class:string,//类别码
                        request_user_intro:string,//用户描述/介绍
                        request_admin_intro:string,//客服描述
                        request_status:int,         //状态码
                        request_create_time:string,//创建时间
                        request_update_time:string,//更新时间
                        request_payment:int,      //支付单号码。如果没有则为0
                        request_phone:string        //手机号
                        request_address:string      //地址
                        request_images:array        //上传图片的id数组
                        },
                    {数据二...},
                    {数据三...}
                ]
             */
            $queryLimit = SysRequest::all();
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }

            if(session("user.user_id") != null)
            {
                $queryLimit["user"] = session("user.user_id");
            }

            //开始查询
            if ($requestData = Request::select($queryLimit))
            {
                $returnData = [];

                unset($requestData['total']);
                $requestData['result_code'] = 0;
                foreach ($requestData['data'] as $key => $value)
                {
                    $newData["request_id"] = $requestData['data'][$key]->request_id;
                    $newData["request_class"] = $requestData['data'][$key]->request_class;
                    $newData["request_user_intro"] = \GuzzleHttp\json_decode($requestData['data'][$key]->request_user_intro);
                    $newData["request_admin_intro"] = $requestData['data'][$key]->request_admin_intro;
                    $newData["request_status"] = $requestData['data'][$key]->request_status;
                    $newData["request_create_time"] = $requestData['data'][$key]->request_create_time;
                    $newData["request_update_time"] = $requestData['data'][$key]->request_update_time;
                    $newData["request_payment"] = $requestData['data'][$key]->request_payment;
                    $jsonData = json_decode($requestData['data'][$key]->request_other_data,true);
                    if($jsonData!=null)
                    {
                        $newData["request_phone"] = $jsonData["phone"];
                        $newData["request_address"] = $jsonData["address"];
                        $newData["request_images"] = isset($jsonData["images"])?$jsonData["images"]:null;
                    }
                    $returnData[] = $newData;
                }
                $requestData["data"] = $returnData;
                return response()->json($requestData);
            }
            else
            {
                //查询失败返回码
                $requestData['result_code'] = -1;
                $requestData['message'] = "查询失败";
                $requestData['status'] = false;
                $requestData['data'] = null;
                return response()->json($requestData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //提交请求 POST /app_addRequest
    public function addRequest()
    {
        try {
            /*
             * 发送一个请求到服务器
                需要以下数据
                |-class
                |-user_intro
                |-phone
                |-address

                返回标准结构
                在data中返回提交请求后生成请求记录的id号，可以根据其进行后续操作
                {id:156}
             */

            $inputData = SysRequest::all();
            if(isset($inputData["access_token"]))
            {
                unset($inputData["access_token"]);
            }

            $r = Request::addRequest(session("user.user_id"),$inputData["class"],$inputData["user_intro"],$inputData);

            $i = 0;
            while(true)
            {
                if(SysRequest::hasFile('image_'.$i))
                {
                    $imageFile = SysRequest::file('image_'.$i);
                    $input["image_name"] = session("user.user_id")." 用户服务提供图片";
                    $input["image_user"] =  session("user.user_id");
                    $return = Image::putImage($input,$imageFile);
                    $r->addImage($return);
                }
                else
                {
                    break;
                }
                $i++;
            }


            if ($r != false)
            {
                $returnData['status'] = true;
                $returnData['message'] = '提交请求成功';
                $returnData['result_code'] = 0;
                $returnData['data'] = $r->info->request_id;
                return response()->json($returnData);
            } else {
                //提交请求失败返回结果码
                $returnData['result_code'] = -1;
                $returnData['message'] = "提交请求失败";
                $returnData['status'] = false;
                $returnData['data'] = null;
                return response()->json($returnData);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //更新请求的用户描述: POST /app_updateRequestUserIntro
    public function updateRequestUserIntro()
    {
        try
        {
            /*
             * 需要发送
                |-id  要修改的请求id
                |-user_intro

                返回标准结构
             */
            $postData = SysRequest::all();
            if(isset($postData["access_token"]))
            {
                unset($postData["access_token"]);
            }

            $request = new Request($postData['id']);
            if($request->info->request_user == session("user.user_id"))
            {
                if ($request->updateUserIntro($postData['user_intro']))
                {
                    $returnData['status'] = true;
                    $returnData['message'] = '更新请求的用户描述成功';
                    $returnData['result_code'] = 0;
                    $returnData['data'] = [];
                    return response()->json($returnData);
                }
                else
                {
                    //更新请求的用户描述失败后的返回码
                    $returnData['result_code'] = -1;
                    $returnData['message'] = "更新请求的用户描述失败";
                    $returnData['status'] = false;
                    $returnData['data'] = null;
                    return response()->json($returnData);
                }
            }
            return response()->json(["status"=>false,"data"=>[],"message"=>"更新请求的用户描述失败","result_code"=>-1]);
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

    //取消请求: POST /app_cancelRequest
    public function cancelRequest()
    {
        try
        {
            /*
             * 需要发送一个id
                |-id 要取消的请求id

                返回标准结构
             */
            $postData = SysRequest::all();
            $request = new Request($postData['id']);
            if($request->info->request_user == session("user.user_id"))
            {
                if ($request->cancel())
                {
                    $returnData['status'] = true;
                    $returnData['message'] = '取消请求成功';
                    $returnData['result_code'] = 0;
                    $returnData['data'] = null;
                    return response()->json($returnData);
                }
                else
                {
                    //取消请求失败后的返回码
                    $returnData['result_code'] = -1;
                    $returnData['message'] = "取消请求失败";
                    $returnData['status'] = false;
                    $returnData['data'] = null;
                    return response()->json($returnData);
                }
            }
            return response()->json(["status"=>false,"data"=>[],"message"=>"取消请求失败,你正在取消别人的请求","result_code"=>-1]);
        }
        catch(\Exception $e)
        {
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }

}