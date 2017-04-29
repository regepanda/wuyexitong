<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/23
 * Time: 11:23
 */

namespace MyClass\System;


use MyClass\User\Car;
use MyClass\User\House;
use MyClass\User\UserTrueInfo;
use MyClass\User\CarPosition;

class Audit
{
    const HOUSE = 1;
    const CAR = 2;
    const TRUEINFO = 3;
    const POSITION = 4;

    /**
     * 查询未审核
     * @param $class    Audit::HOUSE|Audit::CAR|Audit::TRUEINFO
     * @param int $start
     * @param int $num
     * @param bool|false $desc
     * @return array
     */
    public static function select($admin_community_group = null,$class,$start = 0,$num = 10,$desc=false,$check=true)
    {
        $queryLimit["start"] = $start;
        $queryLimit["num"] = $num;
        $queryLimit["check"] = $check;
        $queryLimit['admin_community_group'] = $admin_community_group;
        if($desc!=false){$queryLimit["desc"] = $desc;}

        if($class == Audit::HOUSE)
        {
            $houseData = House::select($queryLimit);
            $houseData["class"] = "1";
            return $houseData;
        }
        if($class == Audit::CAR)
        {
            $carData = Car::select($queryLimit);
            $carData["class"] = "2";
            return $carData;
        }
        if($class == Audit::TRUEINFO)
        {
            $trueInfoData = UserTrueInfo::select($queryLimit);
            $trueInfoData["class"] = "3";
            return  $trueInfoData;
        }
        if($class == Audit::POSITION)
        {
            $carPositionData = CarPosition::select($queryLimit);
            $carPositionData["class"] = "4";
            return  $carPositionData;
        }
    }
}