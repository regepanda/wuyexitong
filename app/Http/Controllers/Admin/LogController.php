<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/18
 * Time: 21:31
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MyClass\System\DBLog;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Base\GuiFunction;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class LogController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(5))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }

    }
    //展示页面
    public function sLog(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sLog");
        return view("Admin.System.sLog");
    }

    //查询所有的记录信息并分页、排序、条件查找【等级】
    public function sAllLog()
    {
        $data = DBLog::select(SysRequest::all());
        return response()->json($data);
    }

    //记录详情
    public function sLogDetail()
    {
            $sLogDetailData = SysRequest::all();
            $log = new DBLog($sLogDetailData['id']);
            return response()->json($log->info);
    }
}