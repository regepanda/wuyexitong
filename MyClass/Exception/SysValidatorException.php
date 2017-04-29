<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/24
 * Time: 14:38
 */

namespace MyClass\Exception;


use Exception;

class SysValidatorException extends Exception
{
    public $message;
    public $url;
    public function __construct($message,$url="/")
    {
        $this->message = $message;
        $this->url = $url;
    }
    public  function getInfo()
    {
        return $this->message;
    }
}