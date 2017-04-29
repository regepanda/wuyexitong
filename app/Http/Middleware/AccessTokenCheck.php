<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/13
 * Time: 11:29
 */

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Request;


use Closure;
use MyClass\User\User;

class AccessTokenCheck
{
    public function __construct()
    {

    }
    public function handle($request, Closure $next)
    {
        $userModel = new User(2);
        $userModel->setSession();
        return $next($request);
//        if($request->input("access_token") == null)
//        {
//            return response()->json(["status"=>false,"message"=>"需要access_token参数","data"=>[]]);
//        }
//
//        $result = User::checkAccessToken($request->input("access_token"));
//
//        if($result["status"]==true)
//        {
//            $userModel = new User($result["user_id"]);
//            $userModel->setSession();
//            return $next($request);
//        }
//        else
//        {
//            return response()->json($result);//json_encode($result);
//        }
    }
}