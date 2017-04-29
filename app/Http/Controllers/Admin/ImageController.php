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
use MyClass\System\Image;
use Illuminate\Support\Facades\Request;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;
/**
 * Class ImageController
 * @package App\Http\Controllers\Admin
 */
class ImageController extends Controller  //zc
{
    /*
     * @param GuiFunction $guiFunc
     */
    public function __construct(GuiFunction $guiFunc)
    {
        if(PowerGroup::checkAdminPower(1))
        {

        }
        else
        {
            throw new PowerException("你没有此权限！");
        }
        $guiFunc->setModule("Manage");
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sImage(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sImage");
        $query_limit["desc"] = true;
        $query_limit["paginate"] = 8;
        $query_limit["num"] = 12;
        $output["data"] = Image::select($query_limit);
        return view("Admin.Manage.sImage",$output["data"]);
    }

    public function aImage(\MyClass\Base\GuiFunction $gui)
    {
        if (Request::hasFile('image_file'))
        {
            $imageFile = Request::file('image_file');
            $input["image_name"] = $_POST["image_name"];
            $return = Image::putImage($input,$imageFile);
            if($return)
            {
                $gui->setMessage(true,"添加图片成功！");
                return redirect()->back();
            }
            else
            {
                $gui->setMessage(true,"添加图片失败！");
                return redirect()->back();
            }
        }
        else
        {
            throw new PowerException("请选择图片","/admin_api_sImage");
        }
    }


    public function dImage($image_id,\MyClass\Base\GuiFunction $gui)
    {
        $image = new Image($image_id);
        $return = $image->delete();
        if($return)
        {
            $gui->setMessage(true,"删除图片成功！");
            return redirect()->back();
        }
        else
        {
            $gui->setMessage(false,"删除图片失败！");
            return redirect()->back();
        }

    }

}