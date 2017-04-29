<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 16:06
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use MyClass\Base\GuiFunction;
use Illuminate\Support\Facades\Request as SysRequest;
use MyClass\Admin\PowerGroup as checkAdminPower;
use MyClass\Exception\PowerException;
use MyClass\User\Account;
use MyClass\Serve\Payment;

class ManageController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        if(checkAdminPower::checkAdminPower(6))
        {

        }
        else
        {
            throw new PowerException("你没有此操作权限！");
        }

    }
    public function index()
    {
        return view("Admin.Manage.index");
    }

    public function sAccount(GuiFunction $guiFunc)
    {
        $guiFunc->setSecondModule("sAccount");
        return view("Admin.Manage.sAccount");
    }

    public function sAllAccount()
    {
        $queryLimit = SysRequest::all();
        $queryLimit["joinUser"] = true;
        $data = Account::select($queryLimit);
        return response()->json($data);
    }

    public function sAccountDetail()
    {
            $user = SysRequest::all();//取得所有发出请求时的输入数据
            $userRecord = new Account($user["id"]);
            $detailData = $userRecord->info;
            return response()->json($detailData);
    }

    public function sPaymentAccountDetail()
    {
        //连表查询，该用户的
        $user = SysRequest::all();//取得所有发出请求时的输入数据
        $queryLimit["user"] = $user["id"];
        $array = Payment::select($queryLimit);
        $detailData = json_encode($array);
        return $detailData;
    }

    public function updateAccountIntegration()
    {
        $updateAccountIntegrationData = SysRequest::all();
        if($updateAccountIntegrationData['account_integration'] != null)
        {
            $account = new Account($updateAccountIntegrationData['account_id']);
            if($account->update($updateAccountIntegrationData))
            {
                return response()->json(["status"=>true,"message"=>"修改成功"]);
            }
            else
            {
                return response()->json(["status"=>false,"message"=>"修改失败"]);
            }
        }
        else
        {
            throw new PowerException("积分不能为空！");
        }
    }
}