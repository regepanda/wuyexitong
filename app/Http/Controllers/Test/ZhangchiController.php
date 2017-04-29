<?php
/**
 * Created by PhpStorm.
 * User: zc
 * Date: 2016/3/14
 * Time: 19:56
 */

namespace App\Http\Controllers\Test;

use MyClass\System\Image;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\User\Car;
use MyClass\User\House;
use MyClass\User\User;
use MyClass\User\Account;
use MyClass\User\UserTrueInfo;

class ZhangchiController extends Controller
{
    public function sImage()
    {

      //  $query_image_limit["paginate"] = 9;
      //  $query_image_limit["desc"] = true;
      //  $query_image_limit["paginate"] = 1;
     //   $query_image_limit["id"] = 5;
      //   $query_image_limit["start"] = 1;
       //    $query_image_limit["num"] = 1;
      //   $return = Image::select($query_image_limit);

       //  dump($return["data"]);
       //  dump($return["total"]);
     //    exit(1);

         return view("Test.testImage.sImage");
    }


    public function getImage($image_id = 0)
    {
        ob_end_clean();
        Image::getImage($image_id);
    }


    //app上传图片测试
    public function putImage()
    {
        $file = Request::file('upfile');



        /*
        dump(Request::all());
        dump($file);
        exit(1);
        */

    }


    public function testGetUserInfo()
    {
       // echo "dsadas";


        return view("Test.zhangchi.zc");
    }


    public function addTestSession()
    {


        $house["house_user"] = session("user.user_id");
        $house["house_area"] = "area";
        $house["house_address"] = "addres";
        $house["house_check"] = false;
        $house["house_tax"] = 10;
        $house["house_can_tax"] = true;
        House::add($house);

        $house2["house_user"] = session("user.user_id");
        $house2["house_area"] = "area2";
        $house2["house_address"] = "address2";
        $house2["house_check"] = false;
        $house2["house_tax"] = 10;
        $house2["house_can_tax"] = true;
        House::add($house2);


        $car["car_user"] = session("user.user_id");
        $car["car_name"] = "name";
        $car["car_brand"] = "brand";
        $car["car_model"] = "model";
        $car["car_color"] = "color";
        $car["car_plate_id"] = "110";
        $car["car_check"] = true;
        Car::add($car);

        $car2["car_user"] = session("user.user_id");
        $car2["car_name"] = "name";
        $car2["car_brand"] = "brand";
        $car2["car_model"] = "model";
        $car2["car_color"] = "color";
        $car2["car_plate_id"] = "120";
        $car2["car_check"] = true;
        Car::add($car2);

        $trueInfo["info_user"] = session("user.user_id");
        $trueInfo["info_name"] = "name";
        $trueInfo["info_ic_id"] = "ic_id";
        $trueInfo["info_intro"] = "data";
        UserTrueInfo::add($trueInfo);

        $trueInfo2["info_user"] = session("user.user_id");
        $trueInfo2["info_name"] = "name2";
        $trueInfo2["info_ic_id"] = "ic_id2";
        $trueInfo2["info_intro"] = "data";
        UserTrueInfo::add($trueInfo2);

    }




}