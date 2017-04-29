<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 14:23
 */


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use MyClass\User\Car;
use MyClass\User\House;
use MyClass\System\DBLog;
use MyClass\System\SystemMap;

class TestController extends Controller
{

    public function sCar()
    {

        $query_limit["desc"] = true;  //倒序正确
       //  $query_limit["user"] = 1; //未测试
       //  $query_limit["account"] = 1;
        // $query_limit["start"] = 2; //起始条数正确
       //    $query_limit["num"] = 2; //条数正确
        // $query_limit["paginate"] = 2; //分页正确

         $data = Car::select($query_limit);

      // dump($data["total"]); //计算总条数正确
        dump($data);
        exit(1);

        //return view("Admin.Manage.sUser");
    }

    public function aCar()
    {
        $inputData["car_name"] = "的接口是否";
       // $inputData["car_user"] = "2";
        $inputData["car_brand"] = "的方法";
        $inputData["car_color"] = "发的";
        $inputData["car_model"] = "对的";
        $inputData["car_user"] = "1";
        $inputData["car_check"] = true;
        //查询是否存在用户
        $return = Car::add($inputData);  //add()正确

        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }

    public function dCar($car_id)
    {

        //  $inputData["user_id"] = 1;
        $class = new Car($car_id);
        $return = $class -> delete();   //delete()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }

    }
    public function uCar()
    {

        $inputData["car_name"] = "hah";
        $inputData["car_brand"] = "hah";
        $inputData["car_color"] = "dfd";
        $inputData["car_model"] = "dfd";
        $inputData["car_check"] = false;
        $class = new Car(21);
        $return = $class -> update($inputData); //update()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }


    }


    public function sHouse()
    {
        $query_limit["desc"] = true;  //倒序正确
        //  $query_limit["user"] = 1; //未测试
        // $query_limit["start"] = 1; //起始条数正确
      //  $query_limit["num"] = 1; //条数正确
        //  $query_limit["paginate"] = 2; //分页正确
            $query_limit["id"] = "3";
        $data = House::select($query_limit);

       dump($data["total"]); //计算总条数正确
        dump($data);
        exit(1);

    }

    public function aHouse()
    {
        $inputData["house_area"] = "1";
         $inputData["house_user"] = "1";
        $inputData["house_address"] = "泸州";
        $inputData["house_other_data"] = "你是";
        $inputData["house_check"] = true;
        //查询是否存在用户
        $return = House::add($inputData);  //add()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }

    public function dHouse($house_id)
    {
        $class = new House($house_id);
        $return = $class -> delete();   //delete()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }

    }

    public function uHouse()
    {

        $inputData["house_area"] = "1";
        $inputData["house_user"] = "1";
        $inputData["house_address"] = "成都";
        $inputData["house_other_data"] = "hah";
        $inputData["house_check"] = true;
        $class = new House(2);
        $return = $class -> update($inputData); //update()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }
    }


    public function sLog()
    {
         // $query_limit["desc"] = true;  //倒序不正确
         // $query_limit["user"] = 1; //未测试
        //  $query_limit["account"] = 1;
       //  $query_limit["start"] = 1; //起始条数正确
        //  $query_limit["num"] = 1; //条数正确
      //    $query_limit["paginate"] = 2; //分页正确
            $query_limit["id"] = "3";
        $data = DBLog::select($query_limit);

        dump($data["total"]); //计算总条数正确
        dump($data);
        exit(1);
    }


    public function aLog()
    {
        $inputData["log_user"] = "1";
        $inputData["log_content"] = "sdfdfj";
        $inputData["log_class"] = "ss";
        $inputData["log_level"] = "h";
        //查询是否存在用户
        $return = DBLog::add($inputData);  //add()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);
    }

    public function dLog($log_id)
    {
        $class = new DBLog($log_id);
        $return = $class -> delete();   //delete()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }

    }

    public function uLog()
    {
        $inputData["log_user"] = "1";
        $inputData["log_content"] = "sfsd";
        $inputData["log_class"] = "jj";
        $inputData["log_level"] = "j";
        $class = new DBLog(2);
        $return = $class -> update($inputData); //update()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        else
        {
            echo "no";
            exit(1);
        }

    }

    public function aLogUser()
    {

        $message = "haha";
        $user = 1;
        $level = "jj";
        //查询是否存在用户
        $return = DBLog::userLog($message, $user, $level,$otherData = null);  //add()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }

    public function aLogAdmin()
    {

        $message = "haha";
        $admin = 1;
        $level = "jj";
        //查询是否存在用户
        $return = DBLog::adminLog($message, $admin, $level,$otherData = null);  //add()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }

    public function aLogUserAccount()
    {

        $message = "haha";
        $user = "1";
        $account = "1";
        $level = "jj";
        //查询是否存在用户
        $return = DBLog::accountLog($message,$user,$account,$level,$otherData = null);  //add()正确
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);

    }

    public function index()
    {
        $key = "ss";
        $value = "yy";
        $return = SystemMap::index($key,$value);
        if($return != false)
        {
          //  echo $return;
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);
    }

    public function del()
    {
        $key = "nmnm";
        $return = SystemMap::del($key);
        if($return)
        {
            echo "yes";
            exit(1);
        }
        echo "no";
        exit(1);
    }

}