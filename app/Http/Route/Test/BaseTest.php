<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 9:26
 */
Route::get("/test",function(\MyClass\Base\GuiFunction $aa)
{

    \Illuminate\Support\Facades\Redis::set('name', 'Taylor');
    $aa->setMessage(true,"程序已经启动");
    echo \Illuminate\Support\Facades\Redis::get("name");
    return view("base");

});



//管理员权限组测试【2016-3-11】王宇飞
Route::get("/admin_sPowerGroup","Admin\PowerGroupController@sPowerGroup");
Route::get("/admin_aPowerGroup","Admin\PowerGroupController@addPowerGroup");
Route::get("/admin_uPowerGroup","Admin\PowerGroupController@updatePowerGroup");
Route::get("/admin_dPowerGroup","Admin\PowerGroupController@delPowerGroup");
Route::get("/admin_addPowerToPowerGroup","Admin\PowerGroupController@addPowerToPowerGroup");
Route::get("/admin_removePowerFromPowerGroup","Admin\PowerGroupController@removePowerFromPowerGroup");


