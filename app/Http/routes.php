<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect("/admin_index");
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(["middleware"=>['api']],function(){
    require __DIR__."/Route/apiLoad.php";

    //openapi使用的路由
    require __DIR__."/Route/openApiLoad.php";
});
Route::group(['middleware' => ['web']], function () {
    require __DIR__.'/Route/webLoad.php';
});
//测试数据不会使用中间件
require __DIR__."/Route/Test/load.php";




