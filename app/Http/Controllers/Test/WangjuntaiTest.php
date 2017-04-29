<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/14
 * Time: 19:56
 */

namespace App\Http\Controllers\Test;


use App\Http\Controllers\Controller;
use MyClass\Serve\Payment;
use MyClass\Serve\Request;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Serve\Tax;
use MyClass\System\Audit;
use MyClass\System\CheckCode;
use MyClass\System\DBLog;
use MyClass\System\Message;
use MyClass\User\Account;
use MyClass\User\Car;

use MyClass\User\Community;
use MyClass\User\House;
use MyClass\User\UCRelation;
use MyClass\User\User;
use MyClass\User\UserTrueInfo;
use MyClass\Admin\CommunityGroup;
use MyClass\Admin\Admin;

class WangjuntaiTest extends Controller
{
    public function requestStatus()
    {
        //$paymentModel = Payment::addPayment(1);
        //$paymentModel = new Payment(2);
        //$paymentModel->setStatusAlreadyPay(1,"用户支付宝","我的支付宝");

        //$paymentModel->updatePrice(24.55);
        //$paymentModel->setStatusAlreadyPay(1,"用户支付宝","我的支付宝");
        //$paymentModel->setStatusAskReturnPay("wo ri nima woyaotuikuan");
        //Account::addAccount(1);
        $request = Request::addRequest(1,4,"我要人陪护");
        dump($request->setStatusCommit("！要两个"));
        dump($request->setStatusReadyHandle("好的，安排两个"));
        $request->getInfo();
        dump($request->setStatusReadyHandle("dengdeng ada啊",15.22));
        $request->getInfo();
        dump($request->setStatusInHandle());
        $request->getInfo();
        $request->updateUserIntro("hahahah");
        $request->updateAdminIntro("hahahah");
        dump($request->setStatusHaveHandle());
    }

    public function taxStatus()
    {

        $taxModel = Tax::addTax(1,14,0,322.00,"保护费");
        //sleep(1);
        //dump($taxModel->setStatusHavePay());
        //dump($taxModel->setStatusOutDate());
        dump(Tax::checkAllOutDate());
    }


    public function insertData()
    {

        for($i=0;$i<10;$i++)
        {

            Tax::addTax(1,14,rand(0,10),19.99+rand(0,10),"保护费".rand(1000,9999));

            Request::addRequest(1,4+rand(0,9),"我要通厕所");

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
                "info_name" => "王尼玛".rand(0,99),
                "info_intro" => "我是王尼玛",
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

            House::addHouse(120,"洛克福德16号7-".rand(100,999),$i+1, $communityId );
            House::addHouse(170,"圣何塞16号楼2-".rand(100,999),$i+1, $communityId );

        }
    }
    public function addWuye()
    {
        $userId = User::add([
            "user_username" => "彭帅哥",
            "user_password" => "123",
            "user_nickname" => "彭帅",
            "user_sex" => "男",
            "user_phone" => "13890398520",
            "user_birthday" => "1994-1-1"
        ]);
        $communityGroupId = CommunityGroup::add([
            "group_name" => "彭帅物业公司",
            "group_intro" => "为老百姓服务的良心物业公司",
            "group_other_data" => "自己脑补"
        ]);
        UCRelation::addLink($userId,$communityGroupId);
        $communityId = Community::add([
            "community_name" => "亮帅小区",
            "community_address" => "四川省都江堰青城山",
            "community_intro" => "这个小区帅的很，是彭亮大帅哥一个人修的",
            "community_city" => "都江堰",
            "community_province" => "四川省",
            "community_group" => $communityGroupId
        ]);

        Admin::add([
            "admin_username" => "彭亮",
            "admin_password" => "123",
            "admin_group" => 2,
            "admin_nickname" => "亮帅",
            "admin_community_group" => $communityGroupId
        ]);
        $houseId = House::add([
            "house_area" => "都江堰青城山",
            "house_address" => "都江堰青城山365住宅",
            "house_user" => $userId,
            "house_community" => $communityId,
        ]);
    }

    public function paymentReflectTaxAndRequest()
    {
        $requesrtModel = Request::addRequest(1,4,"hahaha");
        $requesrtModel->setStatusReadyHandle("hahaha",16.22);
        $requesrtModel->getInfo();
        $paymentModel = new Payment($requesrtModel->info->request_payment);
        dump($paymentModel);
        dump($paymentModel->setStatusAlreadyPay(1,"siwate","hahaha"));
        $requesrtModel->getInfo();
        dump($requesrtModel->info);

        $taxModel = Tax::addTax(1,11,20,16.22);
        $paymentModel = new Payment($taxModel->info->tax_payment);
        $paymentModel->setStatusAlreadyPay(1,"das","das");
        dump($taxModel->info);



    }

    public function paymentCancelReflect()
    {
        $requesrtModel = Request::addRequest(1,4,"hahaha");
        $requesrtModel->setStatusReadyHandle("daasad",1662.02);
        $requesrtModel->getInfo();

        $paymentModel = new Payment($requesrtModel->info->request_payment);
        $paymentModel->setStatusCancelPay();

        $requesrtModel->getInfo();
        dump($requesrtModel->info);


        $taxModel = Tax::addTax(1,11,20,16.22);
        $paymentModel = new Payment($taxModel->info->tax_payment);
        //$paymentModel->setStatusAlreadyPay(1,"das","das");

        $paymentModel->setStatusCancelPay();
        $taxModel ->getInfo();
        dump($taxModel->info);



        $requestModel = Request::addRequest(1,4,"aaa");
        $requestModel->cancel();
        $requestModel->getInfo();
        dump($requestModel->info);

        $taxModel = Tax::addTax(1,14,30,19.1);
        $taxModel->cancel();
        $taxModel->getInfo();
        dump($taxModel->info);



    }

    public function selectAuditData()
    {
        $data = Audit::select(Audit::HOUSE);
        $houseModel = new House($data["data"][0]->house_id);
        $houseModel->setCheckAndTax(15.66,"你妈咋了");
        $houseModel->getInfo();
        dump($houseModel->info);
    }

    public function userLoginAcsToken()
    {

        $user  = new User(1);
        $str = User::setAccessToken($user);
        dump($str);
        dump(User::checkAccessToken($str));
    }

    public function testImageUpload()
    {
        return view("Test.wangjuntai.testImageUpload");
    }

    /**
     * @throws \Exception
     */
    public function checkCodeTest()
    {
        $phone = "17780708323";
        dump("$phone 是否可以发送？");
        dump(CheckCode::canSend("17780708323"));

        dump("加一个验证码,id是");
        dump($id = CheckCode::saveCode("123456",$phone));

        dump("17780708323是否可以发送？");
        dump(CheckCode::canSend($phone));

        dump("17780708323验证id和验证码？");
        dump(CheckCode::checkCode($id,"123456"));
    }

}