App可以以post或者get请求来交互数据，具体方法根据实际路由而定
# 1 统一结构的信息
## 1.1 返回信息
    返回信息在接口中是统一的，返回的数据是一个json格式打包的结构，所有的返回都会遵循这种格式，格式如下：
    
    json数据
        |-status 是否成功true/false
        |-message 描述
        |-data 需要的数据 （如果需要返回数据，比如像查询）
        |-result_code 结果码，可以对应版本的最新的表来查看_
        
    {status:true,message:"xxxxxxx",data:"xxxxxxx"}
    
## 1.2 查询信息
    app中会涉及到多种查询功能，大部分查询（没有特殊说明）的app_getXXXX接口都可以使用下面的筛选条件来进行限制：
     * POST/GET  Array
     * |-start  从哪一条起始（默认0）
     * |-num    需要多少条（默认数按照系统配置）
     * |-class  类别（如果该数据有类别字段的话，请填入相关数字，默认空）
     * |-sort   排序（如果需要排序，输入排序的字段，默认按照时间来排序）
     * |-desc   是否逆转排序即倒序(true倒序，false正序，默认是正序)
     * |-id     按照id获取一条记录（指定一个固定id，默认空）
     
     {start:0,num:10,class:5,sort:user_id,desc:true}
     
     返回json数据
     |-status 是否成功true/false
     |-message 描述
     |-data 需要的数据 （如果需要返回数据，比如像查询）
     |-result_code 结果码，可以对应版本的最新的表来查看
     与上面的1.1返回信息结构一样，这里着重说一下data，它是一个数组，大概结构如下：
     [{data1},{data2},{data3}]
     具体字段根据每一种类型表有所不同，见详细文档
     
     
     客户端只要传递一个这样的json结构，服务端就会根据请求，筛选返回所需要的数据。
     但注意对于有认证权限要求的接口，只会返回相关权限所能查找到的数据
     返回的数据将会存在data中，status来标识是否成功，message是提示信息
     
    
# 2 公共接口
    这些接口直接可以访问，无需权限
    
 
### 查看公告板:GET /app_getBillboard 2016/3/25
    /app_getBillboard
    符合标准查询
    
    返回信息
    标准结构
    标准结构 data中形式如下
    [
        {
            billboard_title:string 标题
            billboard_detail:string 细节
            billboard_create_time:string 创建时间
            billboard_update_time:string 更新时间
        },
        {...},
        {...}
    ]
### 查看首页图片 GET /app_getIndexImages   2016/3/29
    /app_getIndexImages
    无需传任何参数，不符合标准查询
    
    返回信息
    标准结构，data中有首页图片的相关id
   "data":["{'url','/getImage/1'}","{'url','/getImage/2'}"]
    
    
### 请求图片接口 GET /getImage/{id}    2016/3/29
    以一个image_id来请求图片，返回图片的二进制数据，http头中附带格式，可以直接使用
    图片id由其他接口提供，如果这个id不存在，那么会返回一张默认的图片
    不符合标准查询，标准返回结构
    例子
    我需要在App中使用id为1的图片，就请求 /getImage/1 即可

### 创建用户接口:POST /app_createUser
        |-password
        |-phone
        |-check_code_id //验证码id，客户端调用发送验证码接口后返回的结果
        |-check_code    //用户输入的验证码
        
      
        返回标准信息，data中有创建的用户信息
        data=
        {
            id //用户的id
            
        }
        如果有重名会返回相关提示信息
        
###  重置密码接口:GET /app_resetPassword
        |—phone     手机号码
        |-password  新的密码  
        |-check_code_id //验证码id，客户端调用发送验证码接口后返回的结果
        |-check_code    //用户输入的验证码
        
        返回标准信息

### 获取验证码接口：GET /app_getCheckCode
        |-phone
        
        返回标准信息，data中有创建的用户信息
        data = 
        {
            check_code_id //验证码的id，客户端在后面的请求中需要发送这个参数和客户输入的验证码。
        }

        
### 获取服务类型 GET /app_getServer 2016/4/6
        不符合标准查询

        符合标准返回
        data中有所有的服务类型
        data:{
            "class_name":string
            "class_id":int
        }


# 3 请求登录与返回信息
    如果你需要访问登录权限的接口，首先你应该登录获得一个token  
    登录接口:
    GET    /app_login  
            |-username
            |-password
    如果用户名和密码正确，将会返回一个access_token,在data里面
            
# 4 业务相关接口
## 4.1 用户信息
### 获取用户的基本信息  GET /app_getUserInfo
        不包含需要认证的信息（真实姓名，车，房）
        除了access_token 无需传递任何参数，不使用1.2中的标准查询
        返回标准结构
        其中data数据
        {
            user_id:int                 系统内部id
            user_username:string,       用户名
            user_nickname:string,       昵称
            user_sex:string,            性别
            user_phone:string,          电话
            user_birthday:string,       生日
            user_phone_backup:string    备用电话
            user_image:int              图片头像ID
            
        }
        
### 更改用户的基本信息 POST /app_updateUserInfo
        
        需要发送的数据如下：
        |-username
        |-phone
        |-nickname
        |-sex
        |-birthday
        忽略字段表示不修改
        返回标准结构
### 上传用户头像 POST /app_addUserHeadImage
       上传用户头像 
       上传用户头像，发送格式为，需要表单为多部数据的形式（<form enctype="multipart/form-data">）
       |-image_data 图片数据//在前端获取的文件
       返回标准结构
       如果需要修改头像，请再上传一次就行
    
    
### 获取真实信息接口 GET  /app_getTrueInfo
        
        除了access_token 无需传递任何参数，不符合1.2中的标准查询接口
        
        返回标准结构
        其中data结构
         {
            info_id:int             系统内部id
            info_name:string,       真实姓名
            info_age:int       
            info_sex:string
            info_ic_id              身份证号
        }
        
        
### 上传真实信息接口 POST /app_addTrueInfo
       :
       需要发送的信息
       |-name
       |-ic_id
       |-info_age:int       
       |-info_sex:string
       返回标准结构
       
### 删除真实信息接口 GET  /app_delTrueInfo
        :
        需要提供
        |-id (系统内部id)
        
## 4.2 房屋信息    
### 获取房屋信息的接口 GET   /app_getHouse
        
        符合标准查询接口
        
        返回标准结构，其中data是一个数组
        [
            {
                house_id:int //系统内部id
                house_area:float,//房屋大小
                house_address:string,//地址
                house_create_time:string,//创建时间
                house_update_time:string,//更新时间
                house_check:bool,//是否审核true/false
                house_community:id //小区id  
                house_cantax_time //剩余可缴费次数
                house_detail     json//缴费明细
                community_name      //小区名
                house_now   //当前缴费到多少月，月份游标跟踪缴费月份
                house_tax_total  //房屋每月缴费总价
                house_tax_unit    //单价
                
                //2016/4/22更新
                house_parent = [
                    {"id":1,"name":"大地小区"},
                    {"id":1,"name":"1号楼"},
                    {"id":3,"name":"二单元"},
                    {"id":15,"name":1088}
                ]
                //对应 小区，楼号，单元，房号
                
            }
            {...},
            {...}
        ]
    
    
### 用户录入房屋信息 POST  /app_addHouse
         
         
         需要传递下面的信息
            |-data_id 安卓这边在最后一个下拉选择后需把房号id过来【在选择最后一个房后下拉之后点击录入时传入】
         返回标准结构
            返回录入的这条房屋信息

### 修改房屋信息 POST /app_updateHouse
      
        
        需要传递新的房屋id
            |-data_id
        忽略字段表示不修改，但必须要id
        返回标准结构
### 删除房屋信息  GET  /app_delHouse
        需要传递下面的信息
         |-id      (系统内部id)
        
        返回标准结构
### 获取小区信息 GET　/app_getCommunityInfo 2016/3/25
    GET　/app_getCommunityInfo
    标准查询
    |-name 可以按照小区名搜索
    
    返回信息
    标准结构 data中形式如下
    [
        {
            community_id:id              小区id
            community_name:以下全是string 小区名
            community_address            小区地址
            community_intro              小区介绍
            community_create_time       创建时间
            community_update_time       更新时间
            community_city              所在城市
            community_province          所在省份
        },
        {...},
        {...}
    ]

## 4.3 车辆信息
### 获取策车辆信息接口 GET  /app_getCar
    符合标准查询接口
    返回标准结构，其中data是一个数组
        [
            {
                car_id:int //系统内部id
                car_name:string,//房屋大小
                car_brand:string,//品牌
                car_color:string,//颜色
                car_model:string,//型号
                car_create_time:string,//创建时间
                car_update_time:string,//更新时间
                car_insurance_start_time:string,//保险开始时间
                car_insurance_end_time:string,//保险结束日期
                car_plate_id:string, //车牌
                car_check:bool,//是否审核
            },
            {...},
            {...}
        ]
        
### 上传车辆信息接口 POST /app_addCar
    
    需要发送如下数据：
    car_name:string,//房屋大小
    car_brand:string,//品牌
    car_color:string,//颜色
    car_model:string,//型号
    car_plate_id:string, //车牌
    
    返回标准结构
    
### 删除车辆信息接口 GET  /app_delCar
    需要发送车辆id
    id:int  内部id
    
    
    返回标准结构
    
    
### 修改车辆信息接口:POST  /app_updateCar
    需要发送如下数据：
    car_id:int      //内部id_
    car_name:string,//房屋大小
    car_brand:string,//品牌
    car_color:string,//颜色
    car_model:string,//型号
    car_plate_id:string, //车牌
    忽略字段表示不修改，但必须要id
    
    返回标准结构
            
            
## 4.4 请求服务
### 获取请求:GET  /app_getRequest
    符合标准查询格式，可以查询到用户请求的服务
    返回标准结构，其中data是一个数组
       [
           {//数据一
               request_id:int //系统内部id
               request_class:string,//类别码
               request_user_intro:string,//用户描述/介绍，是一个json形式的数据
               request_admin_intro:string,//客服描述 是一个json形式的数据
               request_status:int,         //状态码
               request_create_time:string,//创建时间
               request_update_time:string,//更新时间
               request_payment:int,      //支付单号码。如果没有则为0
               request_phone:string        //手机号
               request_address:string      //地址
               request_images:array        //上传图片的id数组
               },
           {数据二...},
           {数据三...}
       ]
   

### 提交请求 POST  /app_addRequest 2016/4/7
    发送一个请求到服务器
    需要以下数据
    |-class
    |-user_intro
    |-phone
    |-address
    |-image_0 图片二进制数据
    |-image_1
    |-image_2
    ....可以添加多张图片 后面的int递增
    返回标准结构
    在data中返回提交请求后生成请求记录的id号，可以根据其进行后续操作
    {id:156}
    
### 更新请求的用户描述: POST /app_updateRequestUserIntro
    需要发送
    |-id  要修改的请求id
    |-user_intro
    
    返回标准结构

### 取消请求: POST /app_cancelRequest
    需要发送一个id
    |-id 要取消的请求id
    
    返回标准结构
 
## 4.5 缴费
### 获取缴费单 GET  /app_getTax
    符合标准查询结构
    需要参数
    |-tax_house  【如果是传入的是房屋】
    |-tax_car_position  【如果是传入的是车位】
    返回数据，标准结构，data中包含有这个缴费单的详细数据
    [
        {//数据一
                tax_id:int //系统内部id
                tax_class:int,//类别码
                tax_payment:int,//支付单
                tax_user:int ,//关联用户
                tax_status:int,         //状态码
                tax_create_time:string,//创建时间
                tax_update_time:string,//更新时间
                tax_deadline:string,//截止日期
                tax_pay_time:string,//支付日期
                tax_intro:string //介绍
                tax_price:string //缴费总金额
                tax_car_position / tax_house  //根据缴费清单是车位还是物业来动态显示
               
                tax_detail   //json 缴费详情
                house_detail        //json 房子详情 【如果查询的是房屋缴费单】
                {
                    house_id
                    house_create_time
                    house_update_time
                    house_user
                    house_other_data
                    house_check
                    house_can_tax
                    house_intro_tax
                    house_where
                    house_address  //住宅地址
                    house_area   //住宅面积
                    house_tax_total  //总计物业费
                    house_tax_unit  //单价 【物业缴费标准】
                }
                position_detail   //json  车位详情  【如果查询的是车位缴费单】
                {
                    position_id  int 
                    position_user  int
                    position_where  int
                    position_community  int
                    position_community_address  string  车位所在小区地址
                    position_create_time  string
                    position_update_time  string
                    position_cantax_time  int  剩余缴费次数
                    position_now  string  缴费月份跟踪
                    position_tax  每月车位费
                    position_check
                    position_input
                    position_tax_total  停车费总金额 【可能连续缴了几个月的费用】
                }
        },
        {数据二...},
        {数据三...}
    ]
### 添加缴费单 POST /app_addTax 2016/3/22
    POST  
    |-tax_class 缴费类型
    |-house_id:int //如果缴费的是房屋，那么传入房屋id
    |-position_id //如果缴费的是车位，那么传入车位id
    |-tax_time  int  //缴费次数，也就是一次要缴多少个月的费用
    通知系统添加一个缴费单
    
    返回数据，标准结构，data中包含有这个缴费单的详细数据
     tax_id:int //系统内部id
     tax_class:int,//类别码
     tax_payment:int,//支付单
     tax_user:int ,//关联用户
     tax_status:int,         //状态码
     tax_create_time:string,//创建时间
     tax_update_time:string,//更新时间
     tax_deadline:string,//截止日期
     tax_pay_time:string,//支付日期
     tax_intro:string //介绍
    
    tax_detail {"x":"xx","x":"xx"}  //缴费详情
     house_detail {"x":"xx","x":"xx"} //添加此缴费单时返回该缴费房子的详细信息 【如果是交物业费】
     position_detail {"x":"xx","x":"xx"} //添加此缴费单时返回该缴费车位的详细信息 【如果是交停车费】
    
## 4.6 支付单  
### 支付单获取 GET /app_getPayment
    满足标准接口
    
     返回标准结构，其中data是一个数组
    [
        {//数据一
            payment_id:int //系统内部id
            payment_class:int, //类型码
            payment_create_time:string,//创建时间
            payment_update_time:string,//更新时间
            payment_pay_time:string,//支付时间
            payment_price:float,    //支付金额
            payment_status:int,     //状态    
    
        },
        {数据二...},
        {数据三...}
    ]

### 支付
    POST /openApi_AliPay_addPayment
    |-payment_id 需要生成签名支付单的id
    对一个已存在的系统Payemnt生成支付宝签名，返回给客户端
    
    返回数据
    标准结构，在data中存储客户端需求的签名
    
### 微信支付  POST /openApi_WeChatPay_addPayment  2016/3/23
    POST /openApi_WeChatPay_addPayment
    |-payment_id 支付单号
    对一个已存在的系统payment生成一个微信预支付单
    
    返回数据：标准结构，data中包含微信支付单的预支付id(prepay_id)
    
    
    
## 4.6 4点半学校，孩子相关的接口
    

### 查看自己孩子的信息（4点半学校): GET /app_getChild 2016/3/25
    符合标准查询
    
    返回信息
    标准结构
    [
        {
            child_id:int                id
            child_name:string           名字
            child_school:string         学校
            child_monitor:int           监控
            child_age:int               年龄
            child_interest:string       兴趣
            child_sex:string            性别
            child_m_phone               监护人电话
            child_create_time:string    创建时间
            child_update_time:string    更新时间
            monitor_id                  孩子学校监控ID
            monitor_name                孩子学校监控名字
        },
        {...},
        {...}
    ]
### 添加孩子的信息 POST /app_addChild 2016/4/7
    提供数据
    |-child_name   
    |-child_school
    |-child_monitor
    
    |-child_age
    |-child_interest
    |-child_sex
    |-child_m_phone
    |-child_m_phone_2
    
    返回标准结构，status表示是否成功
    data返回这一次插入的数据，结构是查询中的一条
 
### 更新孩子的信息   /app_updateChild 2016/3/25
    提供数据
    |-child_id
    |-child_name  
    
    |-child_monitor
    
    |-child_age
    |-child_interest
    |-child_sex
    |-child_m_phone
    |-child_m_phone2
    返回标准结构 data中存放更新的数据
    
### 删除孩纸的信息   /app_delChild 2016/3/25
    提供
    |-child_id
    
    返回标准结构

## 4.7 消息
### 获取消息 GET /app_getMessage 
    符合标准查询
    返回标准结构，data里面有数据
    [
        {
            message_read,是否已读
            message_data,数据
            message_create_time 创建时间
        },
    ]
    
    
### 设置消息为已读 POST /app_setReadMessage 
    将一条消息设置为已读
    传递参数
    |-message_id  消息id
    
    返回标准结构，data为空
    
## 4.8 信息录入后的客户端获取接口（房屋和车位）
### 用户选择房屋的分级菜单 POST /app_getInputHouseData 
    符合标准查询
    
    传递参数
    |-community_id  小区id
    |-parent_id  id等级，根据它可以确定查询的是楼还是单元还是房号
   
    
    返回标准结构，data里面有数据
    [
        {//数据一
            data_id:int //系统内部id 
            data_self_id
            data_value //
            data_parent //id等级，根据它可以确定查询的是楼还是单元还是房号
            data_community //所属小区
            data_tax  //每月缴费金额
            data_detail //缴费明细
        },
        {数据二...},
        {数据三...}
    ]
### 获取车位 GET /app_getCarPosition 【input_car_position表】
    必须要传入一个小区id，不符合标准查询
    |-community_id

    返回标准结构，data里面有数据
    [
         {//数据一
             position_id:int //系统内部id 
             position_intro     //介绍
             position_private //是否是私有车位
             position_tax //每月车位缴费金额
             position_user //车位所属用户
             position_community  //车位所属小区
             position_detail //车位缴费明细
         },
         {数据二...},
         {数据三...}
     ]
### 得到自己的车位列表 GET /app_getPosition 【car_position表】2016/4/28
    不需要参数

    返回标准结构，data里面有数据
    [
         {//数据一
             position_id:int //系统内部id 
             position_user     //车位所属用户
             user_username    //车位所属用户名字
             position_community //车位所在小区
             community_name //车位所在小区名字
             position_community_address //车位所在小区地址
             position_tax //每月车位缴费金额
             position_cantax_time  //车位缴费次数
             position_now   //追踪缴费月份，也就是缴费缴到了多少月、【值为映射出来的年月份】
             position_check  //是否审核
             position_intro  //车位位置信息
             position_area   //车位区域信息
             position_input  //对应input_car_position表里面的车位号
         },
         {数据二...},
         {数据三...}
     ]
### 删除用户自己的车位   GET /app_delCarPosition 2016/4/28
     提供
     |-position_id
     
     返回标准结构
### 申请车位 POST /app_addCarPosition
    需要传递下面的信息
        |-position_id //车位id
    返回标准结构
        返回录入的这条车位信息
        
## 4.9 监控接口
### 获取所有的监控 GET /app_getMonitor
    这里指的实现下学校的监控列表
    返回标准结构，data里面有数据
    [
         {//数据一
             monitor_id:int //系统内部id 
             monitor_name  //监控名称
             monitor_create_time  //监控创建时间
             monitor_update_time  //监控更新时间
             monitor_device_id    //监控设备id
             monitor_device_password //密码
             monitor_device_area        //区域
         },
         {数据二...},
         {数据三...}
     ]
        
## 4.10 系统版本更新
### 获取版本号列表 get /app_getVersionList
    不满足标准查询，无需传入token,需要指定你的平台类型
    |-type="Android" or "IOS"
    标准返回
    data中返回版本列表
    data=
    [
        {
            version:12,//这个就是版本id
            name:"V1.15",
            date:"2015-05-26 19:55:23",
            type:"Android" or "IOS"
        },
        {
            version:11,
            name:"V1.12",
            date:"2015-05-1 08:21:53",
            type:"Android" or "IOS"
        }
    ]
### 获取指定版本app安装包 get /app_getInstaller
    获取指定版本的APP，需要传入版本号,不符合标准查询,需要传入一个指定版本id
    |-version
    会返回一个二进制文件数据


