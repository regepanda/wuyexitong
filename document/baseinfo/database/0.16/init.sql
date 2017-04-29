INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('1', '支付宝',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('2', '微信支付',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('3', '翼支付',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('4', '钟点工',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('5', '陪护工',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('6', '保洁服务',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('7', '月嫂',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('8', '管道疏通',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('9', '开锁服务',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('10', '水电修理',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('11', '家电修理',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('12', '房屋修理',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('13', '定制需求',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('14', '物业费',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('15', '系统账户',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('16', '四点半学校',true);
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('17', '投诉建议', '1');
INSERT INTO `wuyexitong`.`class` (`class_id`, `class_name`, `class_origin`) VALUES ('18', '车位费', '1');


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
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('11', '待付费');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('12', '已付费');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('13', '取消付费');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('14', '申请退款');
INSERT INTO `wuyexitong`.`status` (`status_id`, `status_name`) VALUES ('15', '已过期');

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


INSERT INTO `wuyexitong`.`account` (`account_id`, `account_user`, `account_class`, `account_update_time`, `account_create_time`, `account_check`) VALUES ('1', '1', '15', now(), now(), true);

INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('1', '2016年4月', '1');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('2', '2016年5月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('3', '2016年6月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('4', '2016年7月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('5', '2016年8月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('6', '2016年9月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('7', '2016年10月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('8', '2016年11月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('9', '2016年12月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('10', '2017年1月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('11', '2017年2月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('12', '2017年3月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('13', '2017年4月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('14', '2017年5月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('15', '2017年6月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('16', '2017年7月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('17', '2017年8月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('18', '2017年9月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('19', '2017年10月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('20', '2017年11月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('21', '2017年12月', '0');
INSERT INTO `wuyexitong`.`now` (`now_id`, `now_value`, `now_is_now`) VALUES ('22', '2018年1月', '0');

INSERT INTO `wuyexitong`.`monitor` (`monitor_name`) VALUES ('大河监控');
INSERT INTO `wuyexitong`.`monitor` (`monitor_name`) VALUES ('清水河监控');
INSERT INTO `wuyexitong`.`course` (`course_name`, `course_school`, `course_position`, `course_monitor`) VALUES ('大同路小区线下学校', '非常好的一所学校', '大同路12号', '1');
INSERT INTO `wuyexitong`.`course` (`course_name`, `course_school`, `course_position`, `course_monitor`) VALUES ('清水河小区学校', '牛逼', '地下路31号', '2');
INSERT INTO `wuyexitong`.`child` (`child_name`, `child_age`, `child_school`, `child_create_time`, `child_update_time`, `child_course`, `child_m_phone`, `child_interest`, `child_sex`, `child_start`, `child_end`) VALUES ('王麻子', now(), '', now(), now(), '1', '1452323256', '打人，摔跤，纵火', '男', '16:30', '19:31');

INSERT INTO `wuyexitong`.`community_group` (`group_name`, `group_intro`) VALUES ('东云物业', '很厉害的物业');
INSERT INTO `wuyexitong`.`community` (`community_id`, `community_name`, `community_address`, `community_intro`, `community_create_time`, `community_update_time`, `community_city`, `community_province`, `community_group`) VALUES ('1', '大地小区', '大地路18号', '专业小区', now(), now(), '都江堰市', '四川省', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_community`) VALUES ('1', '0322', '一栋', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`) VALUES ('2', '0622', '一单元', '1', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`) VALUES ('3', '0231', '二单元', '1', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`) VALUES ('4', '0254', '三单元', '1', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`) VALUES ('5', '0125', '101', '4', '1', '12.55', '{\"垃圾费\":15.06,\"花草费\":0.15}');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`) VALUES ('6', '1231', '102', '4', '1', '12.45', '{\"垃圾费\":15.06,\"花草费\":0.15}');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`) VALUES ('7', '4865', '202', '4', '1', '12.55', '{\"垃圾费\":15.06,\"花草费\":0.15}');
INSERT INTO `wuyexitong`.`input_car_position` (`position_id`, `position_intro`, `position_private`, `position_tax`, `position_use`, `position_community`, `position_detail`) VALUES ('1', '地下五层101', '1', '101.22', '0', '1', '黑兮兮的');
INSERT INTO `wuyexitong`.`input_car_position` (`position_id`, `position_intro`, `position_private`, `position_tax`, `position_use`, `position_community`, `position_detail`) VALUES ('2', '地下一层223', '1', '102.22', '0', '1', '爆炸性的');

INSERT INTO `wuyexitong`.`car_position` (`position_id`, `position_user`, `position_where`, `position_community`, `position_create_time`, `position_update_time`, `position_detail`, `position_cantax_time`, `position_now`, `position_tax`, `position_check`) VALUES ('1', '1', '1', '1', now(), now(), '{}', '1', '1', '101.22', '0');

INSERT INTO `wuyexitong`.`car_position` (`position_id`, `position_user`, `position_where`, `position_community`, `position_create_time`, `position_update_time`, `position_detail`, `position_cantax_time`, `position_now`, `position_tax`, `position_check`) VALUES ('2', '1', '1', '1', now(), now(), '{}', '1', '1', '101.22', '0');

INSERT INTO `wuyexitong`.`system_map` (`system_key`, `system_value`, `system_create_time`) VALUES ('SystemVersion', '[]', now());


UPDATE `wuyexitong`.`input_car_position` SET `position_class`='露天', `position_self_id`='D-12', `position_area`='东2区，湖畔' WHERE `position_id`='1';
UPDATE `wuyexitong`.`input_car_position` SET `position_class`='室内', `position_self_id`='E-33', `position_area`='东3区，广场' WHERE `position_id`='2';
UPDATE `wuyexitong`.`input_house_data` SET `data_use`='0' WHERE `data_id`='7';
UPDATE `wuyexitong`.`input_house_data` SET `data_use`='0' WHERE `data_id`='6';
UPDATE `wuyexitong`.`input_house_data` SET `data_use`='0' WHERE `data_id`='5';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='1' WHERE `data_id`='1';
UPDATE `wuyexitong`.`monitor` SET `monitor_device_id`='1', `monitor_device_password`='123' WHERE `monitor_id`='1';
UPDATE `wuyexitong`.`monitor` SET `monitor_device_id`='2', `monitor_device_password`='123' WHERE `monitor_id`='2';
INSERT INTO `wuyexitong`.`community_group` (`group_id`, `group_name`) VALUES ('2', '大家物业');


UPDATE `wuyexitong`.`child` SET `child_name`='王一', `child_m_phone_2`='152263562' WHERE `child_id`='1';
INSERT INTO `wuyexitong`.`child` (`child_id`, `child_name`, `child_age`, `child_create_time`, `child_update_time`, `child_course`, `child_interest`, `child_sex`, `child_start`, `child_end`, `child_m_phone`, `child_m_phone_2`) VALUES ('2', '张二', now(), now(), now(), '2', '化学武器', '男', '16:30', '22:22', '1561615', '1651615');
INSERT INTO `wuyexitong`.`user` (`user_id`, `user_username`, `user_password`, `user_nickname`, `user_sex`, `user_phone`, `user_birthday`, `user_create_time`, `user_update_time`, `user_group`, `user_del`, `user_phone_backup`) VALUES ('2', '12345', md5('123'), '测试用户', '男', '178552265', now(), now(), now(), '1', '0', '242444424242');


INSERT INTO `wuyexitong`.`message` (`message_id`, `message_to`, `message_create_time`, `message_read`, `message_data`) VALUES ('1', '1', now(), '0', '你已经成功注册');
INSERT INTO `wuyexitong`.`message` (`message_id`, `message_to`, `message_create_time`, `message_read`, `message_data`) VALUES ('2', '2', now(), '0', '你已经成功注册');

UPDATE `wuyexitong`.`child` SET `child_user`='2' WHERE `child_id`='1';
UPDATE `wuyexitong`.`child` SET `child_user`='2' WHERE `child_id`='2';
INSERT INTO `wuyexitong`.`car` (`car_id`, `car_user`, `car_name`, `car_brand`, `car_color`, `car_model`, `car_create_time`, `car_update_time`, `car_insurance_start_time`, `car_insurance_end_time`, `car_plate_id`, `car_check`) VALUES ('1', '2', '阿莫尔', '保时捷', '红', 'VBDZ-552', now(), now(), now(), now(), '川A-562351', '1');
INSERT INTO `wuyexitong`.`car` (`car_id`, `car_user`, `car_name`, `car_brand`, `car_color`, `car_model`, `car_create_time`, `car_update_time`, `car_insurance_start_time`, `car_insurance_end_time`, `car_plate_id`, `car_check`) VALUES ('2', '2', '洛林', '保时捷', '蓝', 'VB4-52', now(), now(), now(), now(), '川A-412101', '1');
UPDATE `wuyexitong`.`car_position` SET `position_user`='2' WHERE `position_id`='1';
UPDATE `wuyexitong`.`car_position` SET `position_user`='2' WHERE `position_id`='2';
INSERT INTO `wuyexitong`.`admin` (`admin_id`, `admin_username`, `admin_password`, `admin_group`, `admin_nickname`, `admin_create_time`, `admin_update_time`, `admin_community_group`) VALUES ('2', 'admin2', md5('123'), '1', '东云物业管理员', now(), now(), '1');
INSERT INTO `wuyexitong`.`community` (`community_id`, `community_name`, `community_address`, `community_intro`, `community_create_time`, `community_update_time`, `community_city`, `community_province`, `community_group`) VALUES ('2', '大家小区', '大家路15号', '专业小区', now(), now(), '都江堰市', '四川省', '2');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_community`, `data_top`) VALUES ('8', '2525', 'A座', '2', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_community`, `data_top`) VALUES ('9', '2566', 'B座', '2', '1');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_top`) VALUES ('10', '5615', '一单元', '9', '2', '0');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_top`) VALUES ('11', '2121', '二单元', '8', '2', '0');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`, `data_use`, `data_top`) VALUES ('12', '212122', '101', '11', '2', '12.1', '{\"垃圾费\":15.06,\"花草费\":0.15}', '0', '0');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`, `data_use`, `data_top`) VALUES ('13', '3321', '101', '10', '2', '21.00', '{\"垃圾费\":15.06,\"花草费\":0.15}', '0', '0');
INSERT INTO `wuyexitong`.`input_house_data` (`data_id`, `data_self_id`, `data_value`, `data_parent`, `data_community`, `data_tax`, `data_detail`, `data_use`, `data_top`) VALUES ('14', '2135', '102', '10', '2', '4.55', '{\"垃圾费\":15.06,\"花草费\":0.15}', '0', '0');
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='7';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='6';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='5';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='4';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='3';
UPDATE `wuyexitong`.`input_house_data` SET `data_top`='0' WHERE `data_id`='2';

INSERT INTO `wuyexitong`.`house` (`house_id`, `house_area`, `house_address`, `house_create_time`, `house_update_time`, `house_user`, `house_check`, `house_can_tax`, `house_tax`, `house_intro_tax`, `house_community`, `house_cantax_time`, `house_now`, `house_where`) VALUES ('5', '102.22', '大地小区住宅', now(), now(), '1', '1', '1', '15.03', '{\"垃圾费\":15.06,\"花草费\":0.15', '1', '3', '1', '5');
INSERT INTO `wuyexitong`.`user_community_group` (`re_user`, `re_community_group`) VALUES ('1', '1');
INSERT INTO `wuyexitong`.`user_community_group` (`re_user`, `re_community_group`) VALUES ('2', '2');
UPDATE `wuyexitong`.`house` SET `house_id`='1' WHERE `house_id`='5';
INSERT INTO `wuyexitong`.`house` (`house_id`, `house_area`, `house_address`, `house_create_time`, `house_update_time`, `house_user`, `house_check`, `house_can_tax`, `house_tax`, `house_intro_tax`, `house_community`, `house_cantax_time`, `house_now`, `house_where`) VALUES ('2', '105.22', '大家住宅小区', now(), now(), '2', '1', '1', '42.55', '{\"垃圾费\":15.06,\"花草费\":0.15', '2', '3', '1', '12');
UPDATE `wuyexitong`.`input_house_data` SET `data_use`='1' WHERE `data_id`='12';
UPDATE `wuyexitong`.`car_position` SET `position_user`='2' WHERE `position_id`='1';


UPDATE `wuyexitong`.`admin` SET `admin_username`='admin1' WHERE `admin_id`='2';
INSERT INTO `wuyexitong`.`admin` (`admin_id`, `admin_username`, `admin_password`, `admin_group`, `admin_nickname`, `admin_create_time`, `admin_update_time`, `admin_community_group`) VALUES ('3', 'admin2', md5('123'), '1', '大家物业管理员', now(), now(), '2');
INSERT INTO `wuyexitong`.`account` (`account_id`, `account_user`, `account_class`, `account_update_time`, `account_create_time`, `account_check`) VALUES ('2', '2', '15', '2016-05-06 09:38:17', '2016-05-06 09:38:17', '1');









