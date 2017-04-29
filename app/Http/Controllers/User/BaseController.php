<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:56
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use MyClass\User\PowerGroup;
use MyClass\Base\GuiFunction;

class BaseController extends Controller
{
    public function __construct(GuiFunction $guiFunc)
    {
        $guiFunc->setModule("Manage");
    }

    public function sUserGroup()
    {
        $queryLimit['group_id'] = 1;
        $data = PowerGroup::select($queryLimit);
        dump($data);
    }

    public function aUserGroup()
    {
        $dataArray["group_name"] = "pengliang";
        $dataArray["group_intro"] = "shisb";
        if(PowerGroup::add($dataArray))
        {
            echo"OK!";
        }
        else
        {
            echo"NO!";
        }
    }

    public function uUserGroup()
    {
        $dataArray["group_name"] = "pengliangqweq";
        $dataArray["group_intro"] = "shisbewqeqw";
        $powerGroup = new PowerGroup(3);
        if($powerGroup->update($dataArray))
        {
            echo"OK!";
        }
        else
        {
            echo"NO!";
        }
    }

    public function dUserGroup()
    {
        $powerGroup = new PowerGroup(3);
        if($powerGroup->delete())
        {
            echo"OK!";
        }
        else
        {
            echo"NO!";
        }
    }

    public function addPowerToPowerGroup()
    {
        $user = new PowerGroup(4);
        $return =  $user->AddPowerToUserPowerGroup([2]);
        if($return != false )
        {
            echo"OK!";
        }
        else
        {
            echo"NO!";
        }
    }

    public function removePowerFromPowerGroup()
    {
        $user = new PowerGroup(4);
        $return = $user->RemovePowerFromPowerGroup([2]);
        if($return != false )
        {
            echo"OK!";
        }
        else
        {
            echo"NO!";
        }
    }
}