<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2016/3/23
 * Time: 15:28
 */

namespace MyClass\Exception;


use Exception;

class PowerException extends Exception
{
    public $powerErrorInfo ="";
    public function __construct($message,$url="/")
    {

        $this->powerErrorInfo = $message;
        $this->url = $url;
    }


}