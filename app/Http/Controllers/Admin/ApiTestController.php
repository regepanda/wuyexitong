<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/24
 * Time: 10:49
 */

namespace app\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use MyClass\Exception\SysValidatorException;

class ApiTestController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("System");
        $guiFunc->setSecondModule("sApiTest");
    }
    public function sApiTest()
    {
        //这里名字真的没写错！你改回sApiTest Git绝对出问题

        return view("Admin.System.ApiTest");
    }
}