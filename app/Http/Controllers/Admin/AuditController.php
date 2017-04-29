<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/23
 * Time: 11:11
 */

namespace app\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use MyClass\Base\GuiFunction;
use MyClass\System\Audit;
use MyClass\User\Car;
use MyClass\User\CarPosition;
use MyClass\User\House;
use MyClass\User\InputHouseData;
use MyClass\User\UserTrueInfo;
use MyClass\Admin\PowerGroup;
use MyClass\Exception\PowerException;

class AuditController extends Controller
{
    public function __construct(GuiFunction $gui)
    {
        if(PowerGroup::checkAdminPower(3)){}
        else
        {
            throw new PowerException("你没有此操作权限！");
        }
        $gui->setModule("Manage");
        $gui->setSecondModule("sUser");
    }
    public function sAudit()
    {
        return view("Admin.Manage.sAudit");
    }
    public function apiSAudit()
    {
        //如果当前系统是以物管的角色登录，那查询出来的车辆或者真实信息或者房屋信息都应该是此物业公司管辖范围内的
        $inputData = Request::all();
        $inputData['admin_community_group'] = null;
        if(session("admin.community_group") != null)
        {
            $inputData['admin_community_group'] = session("admin.community_group");
        }
        $data = Audit::select($inputData['admin_community_group'],$inputData["class"],$inputData["start"],$inputData["num"],isset($inputData["desc"])?true:null
        ,isset($inputData["check"])?false:true);
        //dump($data);
        return response()->json($data);
    }

    //设定房子为已经审核
    public function setHouseChecked()
    {
        $inputData = Request::only("data_tax","house_check","house_id","data_detail");
        $houseModel = new House($inputData["house_id"]);
        $houseInput = new InputHouseData($houseModel->info->house_where);
        if(empty($inputData["data_tax"]))
        {
            if($houseModel->setCheckAndTax())
            {
                //把房子缴费明细更新进去
                $dataArray['data_detail'] = json_encode($inputData['house_other_data']);
                $houseInput->update($dataArray);
                $data["status"] = true;
                $data["message"] = "成功审核房屋数据";
                return response()->json($data);
            }

        }
        else
        {
            $inputData['data_detail'] = json_encode($inputData['data_detail']);
            if($houseModel->setCheckAndTax($inputData["data_tax"],$inputData["data_detail"]))
            {
                $data["status"] =  true;
                $data["message"] = "成功审核";
                return response()->json($data);
            }

        }
        return response()->json(["status"=>false,"message"=>"错误，无法审核"]);
    }

    public function setCarChecked()
    {
        $id = Request::input("car_id");
        $carModel = new Car($id);
        if($carModel->setCarChecked())
        {
            $data["status"]=true;
            $data["message"]="审核完成";
            return response()->json($data);
        }
        $data["status"]=false;
        $data["message"]="审核失败";
        return response()->json($data);
    }
    public function setTrueinfoChecked()
    {
        $id = Request::input("info_id");
        $trueInfoModel = new UserTrueInfo($id);
        if($trueInfoModel->setChecked())
        {
            $data["status"]=true;
            $data["message"]="审核完成";
            return response()->json($data);
        }
        $data["status"]=false;
        $data["message"]="审核失败";
        return response()->json($data);
    }


    public function sHouse(GuiFunction $guiFunc)
    {
        $guiFunc->setThirdModule("sHouse");
        $displayData["nowClass"] = 1;

        return view("Admin.Manage.sAudit",$displayData);
    }

    //修改房子信息
    public function updateHouse()
    {
        $updateHouseData = Request::all();
        $updateData['house_id'] = $updateHouseData['id'];
        $updateInputData['house_area'] = $updateHouseData['area'];
        $updateInputData['house_address'] = $updateHouseData['address'];
        $updateInputData['data_community'] = $updateHouseData['community'];
        $updateInputData['house_cantax_time'] = $updateHouseData['cantax_timel'];
        $house = new House($updateData['house_id']);
        $inputHouse = new InputHouseData($house->info->house_where);
        if($inputHouse->update($updateInputData))
        {
            $house->update($updateData);
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

    public function sCar(GuiFunction $guiFunc)
    {
        $guiFunc->setThirdModule("sCar");
        $displayData["nowClass"] = 2;

        return view("Admin.Manage.sAudit",$displayData);
    }

    //修改汽车信息
    public function updateCar()
    {
        $updateData = Request::all();
        $car = new Car($updateData['car_id']);
        if($car->update($updateData))
        {
            return response()->json(["status"=>true,"message"=>"修改成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"修改失败"]);
        }
    }

    public function sTrueinfo(GuiFunction $guiFunc)
    {
        $guiFunc->setThirdModule("sTrueinfo");
        $displayData["nowClass"] = 3;
        return view("Admin.Manage.sAudit",$displayData);
    }
    //获取车位信息
    public function sCarPosition(GuiFunction $guiFunc)
    {
        $guiFunc->setThirdModule("sCarPosition");
        $displayData["nowClass"] = 4;
        return view("Admin.Manage.sAudit",$displayData);
    }
    //删除车位
    public function deletePosition()
    {
        $deletePositionData = Request::all();
        $carPosition = new CarPosition($deletePositionData['position_id']);
        if($carPosition->delete())
        {
            return response()->json(["status"=>true,"message"=>"删除成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"删除失败"]);
        }
    }
    //审核车位
    public function updateCarPositionCheck()
    {
        $updateCarPositionCheckData = Request::all();
        $carPosition = new CarPosition($updateCarPositionCheckData['position_id']);
        if($carPosition->setCarPositionChecked())
        {
            return response()->json(["status"=>true,"message"=>"审核成功"]);
        }
        else
        {
            return response()->json(["status"=>false,"message"=>"审核失败"]);
        }
    }
}