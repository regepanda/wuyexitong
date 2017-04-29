<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 15:21
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;


/**
 * Class AccountController
 * @package App\Http\Controllers\Admin
 */
class AccountController extends Controller  //zc
{
    /**
     * @param GuiFunction $guiFunc
     */
    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("Manage");
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sAccount(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sAccount");
        return view("Admin.Manage.sAccount");
    }








}