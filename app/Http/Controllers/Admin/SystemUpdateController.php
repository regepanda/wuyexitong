<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/15
 * Time: 9:03
 */

namespace app\Http\Controllers\Admin;


use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use MyClass\Base\GuiFunction;
use MyClass\System\SystemUpdate;

class SystemUpdateController extends Controller
{
    public function __construct(GuiFunction $guiFunction)
    {
        $guiFunction->setModule("System");
        $guiFunction->setSecondModule("SystemUpdate");

    }
    public function sVersion()
    {

        $viewData["version"] = SystemUpdate::getVersion();
        rsort($viewData["version"]);
        //dump($viewData);
        return view("Admin.System.sVersion",$viewData);
    }



    public function aVersion(Request $request,GuiFunction $guiFunction)
    {
        if ($request -> hasFile('file'))
        {
            $file = $request -> file('file');
            $input=$request -> only("version","name","type");

            if(SystemUpdate::addVersion($input["name"],$input["type"],$file))
            {
                $guiFunction->setMessage(true,"成功");
                return redirect()->back();
            }
            throw new \Exception("文件上传错误");

        }
        else
        {
            throw new \Exception("没有文件 来自于/admin_api_sImage");
        }
    }

    public function dVersion($id, GuiFunction $guiFunction)
    {

        if(SystemUpdate::delVersion($id))
        {
            $guiFunction->setMessage(true,"删除成功");
            return redirect()->back();
        }
        else
        {
            $guiFunction->setMessage(false,"删除失败");
            return redirect()->back();
        };
    }


}