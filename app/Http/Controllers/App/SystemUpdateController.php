<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/15
 * Time: 9:06
 */

namespace app\Http\Controllers\App;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use MyClass\System\SystemUpdate;

class SystemUpdateController extends Controller
{

    public function getVersionList()
    {
        $type = Request::input("type");
        $data = SystemUpdate::getVersion();
        $returnData = [];
        foreach($data as $key =>$value)
        {
            if($value->version_type != $type)
            {
                continue;
            }

            $single["version"] = $value->version_id;
            $single["name"] = $value->version_name;
            $single["date"] = $value->version_create_time;
            $single["type"] = $value->version_type;
            $returnData[] = $single;
        }

        return response()->json(["status"=>true,"message"=>"成功获取版本记录数据","data"=>$returnData,"result_code"=>"0"]);
    }

    public function getInstaller()
    {
        $versionId = Request::input("version");
        $data = SystemUpdate::getVersion();
        foreach ($data  as  $k => $v)
        {
            if($v->version_id == $versionId )
            {
                //dump($v);
                return response()->download($_SERVER['DOCUMENT_ROOT'].$v->version_path,$v->version_type."_".$v->version_name);
            }
        }

        throw new \Exception("无法找到指定版本软件包");


    }


}