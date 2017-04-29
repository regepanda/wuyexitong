<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/4/15
 * Time: 17:45
 */
Route::get("/app_getVersionList","App\SystemUpdateController@getVersionList");
Route::get("/app_getInstaller","App\SystemUpdateController@getInstaller");