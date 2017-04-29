<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/29
 * Time: 15:23
 */

namespace App\Http\Controllers\App;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use MyClass\System\IndexImage;
use MyClass\System\DBLog;

class IndexImageController extends Controller
{
    public function getIndexImages()
    {
        try
        {
            //[{url:"xxx"},{url:"xxx"}]
            $indexImageData = IndexImage::appSelect();
            //dump($indexImageData);
            if($indexImageData!=null)
            {
                foreach($indexImageData as $key => $value)
                {
                    $indexImageData[$key]['imageId'] = '/getImage/'.$indexImageData[$key]['imageId'];
                }

            }

            if ($indexImageData != null)
            {
                $returnData['result_code'] = 0;
                $returnData['status'] = true;
                $returnData['message'] = "成功获取导数据";
                $returnData['data'] = $indexImageData;
                return response()->json($returnData);
            }
            else
            {
                $returnData['result_code'] = -1;
                $returnData['status'] = false;
                $returnData['message'] = "获取导数据失败,可能数据为空";
                $returnData['data'] = [];
                return response()->json($returnData);
            }
        }
        catch(\Exception $e)
        {
            DBLog::SystemLog("首页图片:获取信息失败,".$e->getMessage(),DBLog::ERROR);
            return response()->json(["status"=>false,"data"=>[],"message"=>"程序内部错误".$e->getMessage(),"result_code"=>-1]);
        }
    }
}