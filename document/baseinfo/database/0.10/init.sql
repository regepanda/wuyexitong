INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('1', '支付宝');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('2', '微信支付');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('3', '翼支付');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('4', '钟点工');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('5', '陪护工');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('6', '保洁服务');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('7', '月嫂');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('8', '管道疏通');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('9', '开锁服务');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('10', '水电修理');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('11', '家电修理');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('12', '房屋修理');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('13', '定制需求');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`) VALUES ('14', '物业费');

INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('1', '未支付');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('2', '已支付');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('3', '取消支付');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('4', '退款中');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('5', '退款完成');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('6', '已提交');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('7', '准备处理');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('8', '处理中');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('9', '处理完成');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('10', '取消请求');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('11', '代付费');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('12', '已付费');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('13', '取消付费');

/*管理员权限*/
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('1', '管理员管理控制');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('2', '用户管理控制');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('3', '审核信息');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('4', '管理支付交易');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('5', '系统信息及记录');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('6', '管理账户');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('7', '管理请求');
INSERT INTO `wuyexitong`.`admin_power` (`power_id`, `power_name`) VALUES ('8', '管理缴费');
/*用户权限*/
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('1', '登录');
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('2', '信息审核申请');
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('3', '请求服务');
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('4', '支付');
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('5', '缴费');
INSERT INTO `wuyexitong`.`user_power` (`power_id`, `power_name`) VALUES ('6', '绑定账户');

/*创建常用的权限组*/
INSERT INTO `wuyexitong`.`user_group` (`group_id`, `group_name`, `group_create_time`, `group_update_time`, `group_intro`) VALUES ('1', '正常权限用户组', now(), now(), '拥有所有用户权限');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('1', '1');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('2', '1');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('3', '1');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('4', '1');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('5', '1');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('6', '1');

INSERT INTO `wuyexitong`.`admin_group` (`group_id`, `group_name`, `group_create_time`, `group_update_time`, `group_intro`) VALUES ('1', '超级管理员组', now(),now(), '拥有所有权限');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('1', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('2', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('3', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('4', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('5', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('6', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('7', '1');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('8', '1');


INSERT INTO `wuyexitong`.`user_group` (`group_id`, `group_name`, `group_create_time`, `group_update_time`, `group_intro`) VALUES ('2', '未绑定账户用户', now(), now(), '未绑定支付账户，所有支付动作限制');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('1', '2');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('2', '2');
INSERT INTO `wuyexitong`.`user_group_re_power` (`power_id`, `group_id`) VALUES ('6', '2');


INSERT INTO `wuyexitong`.`admin_group` (`group_id`, `group_name`, `group_create_time`, `group_update_time`, `group_intro`) VALUES ('2', '客服工作人员', now(), now(), '拥有除了管理员控制，管理支付交易，管理账户以外的所有权限，适合一般工作人员');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('2', '2');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('3', '2');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('5', '2');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('7', '2');
INSERT INTO `wuyexitong`.`admin_group_re_power` (`power_id`, `group_id`) VALUES ('8', '2');

INSERT INTO `wuyexitong`.`admin` (`admin_id`, `admin_username`, `admin_password`, `admin_group`, `admin_nickname`, `admin_create_time`, `admin_update_time`) VALUES ('1', 'admin', md5('123'), '1', '默认管理员', now(), now());
INSERT INTO `wuyexitong`.`user` (`user_id`, `user_username`, `user_password`, `user_nickname`, `user_sex`, `user_phone`, `user_birthday`, `user_create_time`, `user_update_time`, `user_group`, `user_del`, `user_phone_backup`) VALUES ('1', 'user', md5('123'), '默认用户', '男', '1774457384', now(), now(), now(), '1', false, '154866555');
