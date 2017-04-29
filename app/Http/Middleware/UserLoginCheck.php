<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:48
 */

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Request;

use MyClass\Base\GuiFunction;
use Closure;
class UserLoginCheck
{
    public function __construct(GuiFunction $baseFunc)
    {
        $this->baseFunc = $baseFunc;
    }
    public function handle($request, Closure $next)
    {
        if(session("user.user_status")==true)
        {

            return $next($request);
        }
        else
        {
            if(Request::ajax())
            {
                return response()->json(['status' => false, 'message' =>"operation need login"]);
            }
            else
            {
                $this->baseFunc->setMessage(false, "本操作需要登录");
                return redirect("/user_login");
            }
        }

    }
}