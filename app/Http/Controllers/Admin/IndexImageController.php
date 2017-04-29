<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/29
 * Time: 10:48
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request;
use MyClass\System\IndexImage;

class IndexImageController extends Controller
{
    //查询所有的图片
    public function sIndexImage(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("indexImage");
        $imageData['data'] = IndexImage::select();
        return view("Admin.System.sIndexImage",$imageData);
    }

    public function addIndexImage(GuiFunction $guiFunc)
    {
        $addIndexImageData = Request::all();
        $addIndexImageData['image_id'] = (int)$addIndexImageData['image_id'];
        if(IndexImage::add($addIndexImageData['image_id'],$addIndexImageData['image_url']))
        {
            $guiFunc->setMessage(true,"添加成功");
            return redirect()->back();
        }
        else
        {
            $guiFunc->setMessage(false,"添加失败，已有此图片");
            return redirect()->back();
        }
    }

    public function deleteIndexImage($image_id,GuiFunction $guiFunc)
    {
        if(IndexImage::delete($image_id))
        {
            $guiFunc->setMessage(true,"移除成功");
            return redirect()->back();
        }
        else
        {
            $guiFunc->setMessage(false,"移除失败");
            return redirect()->back();
        }
    }

    //用于图片库添加图片到首页图片中用的接口
    public function addImageToIndex($image_id,GuiFunction $guiFunc)
    {
        $image_id = (int)$image_id;
        if(IndexImage::add($image_id))
        {
            $guiFunc->setMessage(true,"添加成功");
            return redirect()->back();
        }
        else
        {
            $guiFunc->setMessage(false,"首页图片库已有此图片");
            return redirect()->back();
        }
    }
}