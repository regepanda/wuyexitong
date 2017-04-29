<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/14
 * Time: 18:21
 */

namespace app\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use MyClass\Admin\Admin;
use MyClass\Serve\Request;
use MyClass\Serve\Tax;
use MyClass\System\Message;
use MyClass\User\Car;
use MyClass\User\Community;
use MyClass\User\House;
use MyClass\User\User;
use MyClass\User\UserTrueInfo;

class InitDataController extends Controller
{
    public function initData()
    {
        for($i=0;$i<10;$i++)
        {

            Tax::addTax(1,14,rand(0,10),19.99+rand(0,10),"保护费".rand(1000,9999));

            Request::addRequest(1,4+rand(0,9),"请求厕所通",["address"=>"皇家社会主义小区","phone"=>"123456"]);

            User::register("123".rand(11111,999999),time().rand(11111,999999));

            /*DBLog::accountLog("hahahah".rand(0,99999),1,1,rand(1,7));
            DBLog::userLog("dasda".rand(0,999),1,rand(1,7));
            DBLog::adminLog("nimanima",1,rand(1,7));*/

            Message::sendMessageToUserGroup(2,"我要完美的服务！！");

            Car::add([
                "car_user"=>$i+1,
                "car_name"=>"红色车，7成新",
                "car_brand"=>"BMW",
                "car_color"=>"红色",
                "car_model"=>"BMW-5232",
                "car_plate_id"=>"鲁B-552315",
                "car_insurance_start_time"=>date('Y-m-d H:i:s'),
                "car_insurance_end_time"=>date('Y-m-d H:i:s')
            ]);


            UserTrueInfo::add([
                "info_name" => "王陆".rand(0,99),
                "info_intro" => "一个人",
                "info_ic_id" => "51070315225".rand(1000,9999),
                "info_user" => $i+1
            ]);

            $communityId = Community::add([
                'community_name'=> '皇家社会主义小区'.rand(0,999)."号区",
                "community_address" => "领袖区共产主义路".rand(100,120)."号",
                "community_intro"=>"风景宜人的小区",
                "community_city" => "成都市",
                "community_province" => "四川省",
            ]);
            Admin::add(["admin_username"=>"admin".$i,"admin_password"=>"123","admin_group"=>1,"admin_community_group"=>1,"admin_nickname"=>"Nikle Adrew"]);

            House::addHouse(120,"洛克福德16号7-".rand(100,999),$i+1, $communityId );
            House::addHouse(170,"圣何塞16号楼2-".rand(100,999),$i+1, $communityId );

        }


    }
}