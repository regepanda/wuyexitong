<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/16
 * Time: 13:46
 */

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use MyClass\Admin\AdminTest;
use Illuminate\Support\Facades\DB;

class PengLiangController extends Controller
{
    public function pl()
    {
        $adminTest = new AdminTest();

        $result = $adminTest->select($queryLimit = null);
        dump($result);
    }

    public function getAllHouse()
    {
        $houseData = DB::table("input_house_data")->get();
        $data = $this->tree($houseData);
        dump($data);
    }

    public function tree($houseData, $parent_id = null)
    {
        static $res = array();
        foreach($houseData as $key => $value)
        {
            if($value->data_parent == $parent_id)
            {
                dump($value->data_id);
                $res[] = $value;
                $this->tree($houseData, $value->data_id);
            }
        }
        return $res;
    }
}