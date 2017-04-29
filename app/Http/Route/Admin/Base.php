<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 11:29
 */
Route::get("admin_login","Admin\BaseController@login");
Route::post("_admin_login","Admin\BaseController@_login");
Route::get("/admin_logout","Admin\BaseController@logout");
Route::group(["middleware"=>["AdminLoginCheck"]],function(){
    Route::get("admin_index","Admin\BaseController@index");

});
