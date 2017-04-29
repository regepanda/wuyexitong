<?php
/**
 * Created by PhpStorm.
 * User: RagPanda
 * Date: 2016/3/9
 * Time: 14:15
 */
Route::group(["middleware"=>["AdminLoginCheck"]],function(){
    Route::get("/admin_manage","Admin\BaseController@manageIndex");
    Route::get("/admin_sAccount","Admin\ManageController@sAccount");//�˻� zc
    Route::get("/admin_aAccount","Admin\AccountController@aAccount"); //zc �� ����
    Route::get("/admin_dAccount/{account_id}","Admin\AccountController@dAccount"); //zc ��  ɾ��
    Route::get("/admin_uAccount","Admin\AccountController@uAccount"); //zc ��  �޸�


    //2016/3/19 zc
    Route::get("/admin_sUserPowerGroup","Admin\UserPowerGroupController@sUserPowerGroup");  //zc
    Route::post("/admin_aUserPowerGroup","Admin\UserPowerGroupController@aUserPowerGroup");  //zc
    Route::post("/admin_uUserPowerGroup","Admin\UserPowerGroupController@uUserPowerGroup");  //zc
    Route::get("/admin_dUserPowerGroup/{group_id}","Admin\UserPowerGroupController@dUserPowerGroup");  //zc
    Route::get("/admin_moreUserPowerGroup/{group_id}","Admin\UserPowerGroupController@moreUserPowerGroup");  //zc

    //2016/3/19 zc
    Route::post("/admin_removeUserToUserPowerGroup","Admin\UserPowerGroupController@removeUserToUserPowerGroup");
    Route::post(" /admin_addPowerToUserPowerGroup","Admin\UserPowerGroupController@addPowerToUserPowerGroup");
    Route::post(" /admin_removePowerToUserPowerGroup","Admin\UserPowerGroupController@removePowerToUserPowerGroup");
    Route::post("/admin_addUserToUserPowerGroup","Admin\UserPowerGroupController@addUserToUserPowerGroup");

    //2016/3/19 zc
    Route::get("/admin_sAdminPowerGroup","Admin\AdminPowerGroupController@sAdminPowerGroup");  //zc
    Route::post("/admin_aAdminPowerGroup","Admin\AdminPowerGroupController@aAdminPowerGroup");  //zc
    Route::post("/admin_uAdminPowerGroup","Admin\AdminPowerGroupController@uAdminPowerGroup");  //zc
    Route::get("/admin_dAdminPowerGroup/{group_id}","Admin\AdminPowerGroupController@dAdminPowerGroup");  //zc
    Route::get("/admin_moreAdminPowerGroup/{group_id}","Admin\AdminPowerGroupController@moreAdminPowerGroup");  //zc

    //2016/3/19 zc
    Route::post("/admin_removeAdminToAdminPowerGroup","Admin\AdminPowerGroupController@removeAdminToAdminPowerGroup");
    Route::post("/admin_addPowerToAdminPowerGroup","Admin\AdminPowerGroupController@addPowerToAdminPowerGroup");
    Route::post("/admin_removePowerToAdminPowerGroup","Admin\AdminPowerGroupController@removePowerToAdminPowerGroup");
    Route::post("/admin_addAdminToAdminPowerGroup","Admin\AdminPowerGroupController@addAdminToAdminPowerGroup");

   // Route::get("/admin_sAdmin","Admin\RoleManageController@sAdmin");
    Route::get("/admin_sUser","Admin\UserController@sUser");  //�û� zc
    Route::get("/admin_aUser","Admin\RoleManageController@aUser");  //�û� zc �� ����
    Route::get("/admin_dUser/{user_id}","Admin\RoleManageController@dUser");  //�û� zc �� ɾ��

    //2016/3/20 zc
    Route::get("/admin_uUser","Admin\UserController@uUser");


    //2016/3/19 zc
    Route::get("/admin_sAdmin","Admin\AdminController@sAdmin");
    Route::post("/admin_aAdmin","Admin\AdminController@aAdmin");
    Route::post("/admin_uAdmin","Admin\AdminController@uAdmin");
    Route::get("/admin_dAdmin/{user_id}","Admin\AdminController@dAdmin");
    Route::post("/admin_addCommunityGroup","Admin\AdminController@addCommunityGroup");//为管理员指定物业公司

    //2016/4/7 zc 服务
    Route::get("/admin_sServe","Admin\ServeController@sServe");
    Route::post("/admin_aServe","Admin\ServeController@aServe");
    Route::post("/admin_uServe","Admin\ServeController@uServe");
    Route::get("/admin_dServe/{class_id}","Admin\ServeController@dServe");


    //2016/3/16 angularjs  zc
    Route::get("/admin_api_sUser","Admin\UserController@sAllUser");
    Route::get("/admin_getUserById","Admin\UserController@getUserById");
    Route::get("/admin_api_sAccount","Admin\ManageController@sAllAccount");
    Route::get("/admin_api_updateAccountIntegration","Admin\ManageController@updateAccountIntegration");

    Route::get("/manage_admin_api_sUserDetail","Admin\UserController@sUserDetail");

    //2016/3/16 zc
    Route::get("/manage_admin_api_sUserGroupDetail","Admin\UserController@sUserGroupDetail");

    Route::get("/manage_admin_api_sRequestDetail","Admin\UserController@sRequestDetail");

    Route::get("/manage_admin_api_sCarDetail","Admin\UserController@sCarDetail");
    Route::get("/manage_admin_api_sHouseDetail","Admin\UserController@sHouseDetail");
    Route::get("/manage_admin_api_sPaymentDetail","Admin\UserController@sPaymentDetail");
    Route::get("/manage_admin_api_sPaymentAccountDetail","Admin\ManageController@sPaymentAccountDetail");
    Route::get("/manage_admin_api_sTaxDetail","Admin\UserController@sTaxDetail");
    Route::get("/manage_admin_api_sAccountDetail","Admin\ManageController@sAccountDetail");
    Route::get("/manage_admin_api_sTrueinfoDetail","Admin\UserController@sTrueinfoDetail");



    Route::get("/admin_api_sImage","Admin\ImageController@sImage");
    Route::post("/admin_aImage","Admin\ImageController@aImage");
    Route::get("/admin_dImage/{image_id}","Admin\ImageController@dImage");


    //支付单模块相关接口
    Route::get("/admin_sPayment","Admin\PaymentController@sPayment");//查看页面

    Route::get("/admin_sAllPayment","Admin\PaymentController@sAllPayment");//查找所有数据，条件查找、排序
    Route::post("/api_paymentDetail","Admin\PaymentController@paymentDetail");//支付单详情
    Route::post("/api_paymentUpdate","Admin\PaymentController@paymentUpdate");//修改支付单
    Route::post("/api_paymentDelete","Admin\PaymentController@paymentDelete");//删除支付单
    Route::post("/api_setStatusAlreadyPay","Admin\PaymentController@setStatusAlreadyPay");//设定状态已经支付
    Route::post("/api_setStatusAskReturnPay","Admin\PaymentController@setStatusAskReturnPay");//设定状态申请退款
    Route::post("/api_setStatusInReturnPay","Admin\PaymentController@setStatusInReturnPay");//设定状态退款中
    Route::post("/api_setStatusAlreadyReturnPay","Admin\PaymentController@setStatusAlreadyReturnPay");//设定状态退款完成
    Route::post("/api_setStatusCancelPay","Admin\PaymentController@setStatusCancelPay");//设定状态取消支付


    //审核系统相关接口
    Route::get("/admin_sAudit","Admin\AuditController@sAudit");
    Route::get("/admin_api_sAudit","Admin\AuditController@apiSAudit");

    Route::get("/admin_sHouse","Admin\AuditController@sHouse");
    Route::post("/admin_updateHouse","Admin\AuditController@updateHouse");//修改房子信息
    Route::get("/admin_sCar","Admin\AuditController@sCar");
    Route::post("/admin_updateCar","Admin\AuditController@updateCar");//修改汽车信息
    Route::get("/admin_sTrueinfo","Admin\AuditController@sTrueinfo");
    Route::get("/admin_sCarPosition","Admin\AuditController@sCarPosition");//查找车位
    Route::get("/api_deletePosition","Admin\AuditController@deletePosition");//删除车位
    Route::post("/api_updateCarPositionCheck","Admin\AuditController@updateCarPositionCheck");//审核车位

    Route::post("/admin_api_setHouseChecked","Admin\AuditController@setHouseChecked");
    Route::post("/admin_api_setCarChecked","Admin\AuditController@setCarChecked");
    Route::post("/admin_api_setTrueinfoChecked","Admin\AuditController@setTrueinfoChecked");

    //小孩相关接口
    Route::get("/admin_sChild","Admin\ChildController@sChild");//显示页面

    Route::get("/Api_allChild","Admin\ChildController@allChild");//查找所有儿童数据，条件查找、排序
    Route::get("/admin_sChildDetail","Admin\ChildController@sChildDetail");//儿童详情
    Route::get("/admin_getCourse","Admin\ChildController@getCourse");//查询所有的课程信息
    Route::get("/admin_addChild","Admin\ChildController@addChild");//添加儿童信息
    Route::get("/admin_childUpdate","Admin\ChildController@childUpdate");//修改儿童信息
    Route::get("/admin_deleteUpdate","Admin\ChildController@deleteUpdate");//删除
    Route::get("/admin_distributionSchool","Admin\ChildController@distributionSchool");//给孩子分配学校


    //zc 小区
    Route::get("/admin_getCommunityGroup","Admin\CommunityController@getCommunityGroup");
    Route::get("/admin_sCommunity","Admin\CommunityController@sCommunity");
    Route::post("/admin_aCommunity","Admin\CommunityController@aCommunity");
    Route::post("/admin_uCommunity","Admin\CommunityController@uCommunity");
    Route::get("/admin_dCommunity","Admin\CommunityController@dCommunity");
    Route::get("/admin_getCommunityById","Admin\CommunityController@getCommunityById");

    Route::get("/admin_api_sCommunity","Admin\CommunityController@sAllCommunity");

    //学校
    Route::get("/admin_sCourse","Admin\CourseController@sCourse");//显示页面
    Route::get("/Api_sAllCourse","Admin\CourseController@sAllCourse");//查找所有课程数据，条件查找、排序
    Route::get("/Api_addCourse","Admin\CourseController@addCourse");//添加课程信息
    Route::get("/Api_updateCourse","Admin\CourseController@updateCourse");//修改课程信息
    Route::get("/Api_deleteCourse","Admin\CourseController@deleteCourse");//删除课程信息
    Route::get("/Api_getMonitor","Admin\CourseController@getMonitor");//获取所有的监控
    Route::get("/Api_appointMonitor","Admin\CourseController@appointMonitor");//为学校指定监控

    //物业公司
    Route::get("/admin_sCommunityGroup","Admin\CommunityGroupController@sCommunityGroup");//显示页面
    Route::get("/Api_sAllCommunityGroup","Admin\CommunityGroupController@sAllCommunityGroup");//查找所有课程数据，条件查找、排序
    Route::get("/Api_updateCommunityGroup","Admin\CommunityGroupController@updateCommunityGroup");//修改
    Route::get("/Api_deleteCommunityGroup","Admin\CommunityGroupController@deleteCommunityGroup");//删除
    Route::get("/Api_addCommunityGroup","Admin\CommunityGroupController@addCommunityGroup");//添加

    //监控
    Route::get("/admin_sMonitor","Admin\MonitorController@sMonitor");//显示页面
    Route::get("/admin_sAllMonitor","Admin\MonitorController@sAllMonitor");//查询所有的监控信息列表
    Route::get("/admin_addMonitor","Admin\MonitorController@addMonitor");//添加updateMonitor
    Route::get("/admin_updateMonitor","Admin\MonitorController@updateMonitor");//修改
    Route::get("/admin_deleteMonitor","Admin\MonitorController@deleteMonitor");//删除
});
