<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MyClass\Base\GuiFunction;
use MyClass\Exception\PowerException;
use MyClass\Exception\SysValidatorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        //权限错误处理
       if ($e instanceof PowerException) {

           if($request->is('app*')||$request->ajax()||
               $request->header("X-XSRF-TOKEN",null)!=null)
           {
               return response()->json(["status"=>false,
                   "message"=>$e->powerErrorInfo,"data"=>[],"result_code"=>3]);
           }
           else
           {
               $gui = new GuiFunction();
               $gui->setMessage(false,$e->powerErrorInfo);
               return redirect($e->url);
           }


        }
        //验证字段错误处理
        if ($e instanceof SysValidatorException)
        {

            if($request->is('app*')||$request->ajax()||
                $request->header("X-XSRF-TOKEN",null)!=null)
            {
                return response()->json(["status"=>false,
                    "message"=>$e->getInfo(),"data"=>[],"result_code"=>4]);
            }
            else
            {
                $gui = new GuiFunction();
                $gui->setMessage(false,$e->getInfo());
                return redirect($e->url);
            }


        }

        //其他错误处理
        if($request->is('app*')||$request->ajax()||
            $request->header("X-XSRF-TOKEN",null)!=null)
        {
            return response()->json(["status"=>false,
                "message"=>$e->getMessage().$e->getFile().":".$e->getLine(),"data"=>[],"result_code"=>-1]);
        }
        else
        {
            return parent::render($request, $e);
        }


    }
}
