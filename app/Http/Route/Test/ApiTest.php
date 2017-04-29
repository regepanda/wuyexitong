<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/13
 * Time: 10:47
 */
Route::get("/test_testToken",['uses'=>"Test\ApiController@testToken",'middleware'=>"AccessTokenCheck"]);
Route::get("/test_testAdminLogin","Test\ApiController@testAdminLogin");