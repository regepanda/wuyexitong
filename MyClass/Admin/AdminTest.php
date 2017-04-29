<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/4/11
 * Time: 11:37
 */

namespace MyClass\Admin;

use MyClass\ModelDatabase;

class AdminTest extends ModelDatabase
{
    public $info; //一条记录信息
    public $id; //一条记录的id号
    public $isNull; //控制器实例化对象的时候看看传过来的id是否有效
    public $prefix;//表的主键
    public $table = "admin";

    /*
     * 在select查询之前会调用这个函数，在这里添加一些每个类自定义的操作
     * 不要返回值，这里传的引用
     * @param $queryLimit
     * @param $cursor
     */
    public function selectExtra(&$queryLimit)
    {
        $queryLimit['id'] = 1;
    }
}