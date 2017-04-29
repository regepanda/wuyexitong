<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 8:45
 */

namespace MyClass;


interface DatabaseModel
{
    public static function select($queryLimit);
    public static function add($dataArray);
    public function delete();
    public function update($dataArray);


}