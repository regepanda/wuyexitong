<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/26
 * Time: 11:03
 */

namespace App\Http\Controllers\App;
use MyClass\System\Billboard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\System\DBLog;

class BillboardController extends Controller
{
    //查看公告板:GET /app_getBillboard
    public function getBillboard()
    {
        try
        {
            /*
             * /app_getBillboard
                符合标准查询

                返回信息
                标准结构
                标准结构 data中形式如下
                [
                    {
                        billboard_title:string 标题
                        billboard_detail:string 细节
                        billboard_create_time:string 创建时间
                        billboard_update_time:string 更新时间
                    },
                    {...},
                    {...}
                ]
             */
            $queryLimit = SysRequest::all();
            if(isset($queryLimit["access_token"]))
            {
                unset($queryLimit["access_token"]);
            }
            //开始查询
            if ($billboardData = Billboard::select($queryLimit))
            {
                unset($billboardData['total']);
                $billboardData['result_code'] = 0;
                foreach ($billboardData['data'] as $key => $value)
                {
                    unset($billboardData['data'][$key]->billboard_id);
                }
                return response()->json($billboardData);
            }
            else
            {
                //查询失败的返回码
                $billboardData['result_code'] = -1;
                $billboardData['message'] = "查询失败";
                $billboardData['status'] = false;
                $billboardData['data'] = null;
                return response()->json($billboardData);
            }
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("公告牌:查询公告牌信息失败,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }


}