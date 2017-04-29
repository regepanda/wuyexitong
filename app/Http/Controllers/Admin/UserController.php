<?php
/**
 * Created by PhpStorm.
 * User: Silence
 * Date: 2016/3/11
 * Time: 15:59
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Serve\Tax;
use MyClass\User\Car;
use MyClass\User\House;
use MyClass\User\PowerGroup;
use MyClass\Admin\PowerGroup as checkAdminPower;
use MyClass\Exception\PowerException;
use MyClass\User\User;
use MyClass\Serve\Request;
use MyClass\Serve\Payment;
use MyClass\User\UserTrueInfo;

class UserController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {

        if(checkAdminPower::checkAdminPower(2))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $guiFunc->setModule("Manage");
    }
    //显示界面
    public function sUser(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sUser");
        $guiFunc->setThirdModule("sUser");
        return view("Admin.Manage.sUser");
    }
    //查询所有的用户
    public function sAllUser()
    {
        $queryLimit = SysRequest::all();
        //如果当前系统是以物管的角色登录，那查询出来的用户也应该是此物管所管理的小区的用户
        if(session("admin.community_group") != null)
        {
            $queryLimit['admin_community_group'] = session("admin.community_group");
        }
        $data = User::select($queryLimit);
        return response()->json($data);
    }

    public function sUserDetail()
    {
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $userRecord = new User($user["id"]);
        $detailData = $userRecord->info;
        return response()->json($detailData);
    }

    public function sRequestDetail()
    {
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["user"] = $user["id"];
        $array = Request::select($queryLimit);
        $detailData = json_encode($array);
        return $detailData;
    }

    public function sCarDetail()
    {
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["id"] = $user["id"];
        $array = Car::select($queryLimit);
        $detailData = json_encode($array);
        return $detailData;
    }
    public function sHouseDetail()
    {
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["id"] = $user["id"];
        $array = House::select($queryLimit);
        //dump($array);
        $array['data'][0]->house_other_data = json_decode($array['data'][0]->data_detail);
        $detailData = json_encode($array);
        return $detailData;
    }

    public function sPaymentDetail()
    {
        //连表查询，该用户的
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["user"] = $user["id"];
        $array = Payment::select($queryLimit);
        $detailData = json_encode($array);
        return $detailData;
    }

    public function sTaxDetail()
    {
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["user"] = $user["id"];
        $array = Tax::select($queryLimit);
        $detailData = json_encode($array);
        return $detailData;
    }


    public function sUserGroupDetail()
    {
        $queryLimit["desc"] = true;
        $array = PowerGroup::select($queryLimit);
        return response()->json( $array["data"]);
    }

    public function uUser()
    {
        $userData = SysRequest::all();
        $user = new User($userData["user_id"]);
        $return = $user ->update($userData);
        if($return)
        {
            return response()->json(["status"=>true,"message"=>"修改用户成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改用户失败"]);
        }
    }

    public function sTrueinfoDetail()
    {
        $user = SysRequest::all();
        $queryLimit['id'] = $user["id"];
        $data = UserTrueInfo::select($queryLimit);
        return response()->json($data);
    }
    //根据指定id查找用户信息
    public function getUserById()
    {
        $requestData = SysRequest::all();
        $queryLimit["id"] = $requestData["user_id"];
        $data = User::select($queryLimit);
        return response()->json($data);
    }

}