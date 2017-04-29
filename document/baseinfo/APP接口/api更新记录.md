# 新增记录
### 支付宝请求支付 2016/3/22

    GET /openApi_AliPay_addPayment
    |-payment_id 需要生成签名支付单的id
    对一个已存在的系统Payemnt生成支付宝签名，返回给客户端
    
    返回数据
    标准结构，在data中存储客户端需求的签名
    
### 添加缴费单 2016/3/22
    POST   /app_addTax
    |-tax_class 缴费类型
    |-tax_month 缴费月份
    |-house_id 需要交费的房屋
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
    tax_detail {"x":"xx","x":"xx"} //缴费详细信息
    house_detail {"x":"xx","x":"xx"} //添加此缴费单时返回该缴费房子的详细信息
    
### 微信支付 2016/3/23
    POST /openApi_WeChatPay_addPayment
    |-payment_id 支付单号
    对一个已存在的系统payment生成一个微信预支付单
    
    返回数据：标准结构，data中包含微信支付单的预支付id(prepay_id)
    
### 验证payment是否已经支付 2016/3/23
    GET /app_checkPaymentAlreadyPay
    |-payment_id 传入一个paymentid
    
    返回结构
    标准结构，data中是true，false表示是否已经支付_
    
   
    
    




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
### 查看自己孩子的信息（4点半学校): GET /app_getChild 2016/3/25
    符合标准查询
    
    返回信息
    标准结构
    [
        {
            child_id:int        id
            child_name:string   名字
            child_school:string 学校
            child_monitor:int   监控
            child_create_time:string    创建时间
            child_update_time:string    更新时间
            course_name  课程名
            course_school  课程学校
            course_position 上课所在位置
            course_date  课程日期
            course_monitor  课程监控
        },
        {...},
        {...}
    ]
### 添加孩子的信息 POST /app_addChild 2016/3/25
    提供数据
    |-child_name   
    |-child_school
    |-child_monitor
    
    返回标准结构，status表示是否成功
    data返回这一次插入的数据，结构是查询中的一条
 
### 更新孩子的信息   POST/app_updateChild 2016/3/25
    提供数据
    |-child_id_
    |-child_name  
    |-child_school
    |-child_monitor
    返回标准结构 data中存放更新的数据
    
### 删除孩纸的信息   GET/app_delChild 2016/3/25
    提供
    |-child_id
    
    返回标准结构
    
### 查看首页图片 GET /app_getIndexImages   2016/3/29
    /app_getIndexImages
    无需传任何参数，不符合标准查询
    
    返回信息
    标准结构，data中有首页图片的相关id
    data
    {
        id1,id2,id3
    }
    例子
    {1,2,3,4}
    
### 请求图片接口 GET /getImage/{id}    2016/3/29
    以一个image_id来请求图片，返回图片的二进制数据，http头中附带格式，可以直接使用
    图片id由其他接口提供，如果这个id不存在，那么会返回一张默认的图片
    不符合标准查询，标准返回结构
    例子
    我需要在App中使用id为1的图片，就请求 /getImage/1 即可
    
    
    
    
    
    
    
# 修改记录
    
### 获取真实信息接口 GET  /app_getTrueInfo
     2016/4/8修改
        除了access_token 无需传递任何参数，不符合1.2中的标准查询接口
        
        返回标准结构
        其中data结构
         {
            info_id:int             系统内部id
            info_name:string,       真实姓名
            info_intro:string       身份证号
            info_age:int       身份年龄  【新加】
            info_sex:string       身份性别【新加】
        }
        
### 上传真实信息接口 POST  /app_addTrueInfo
   2016/4/8修改
      
       需要发送的信息
       |-name
       |-ic_id
       |-age  身份年龄  【新加】age可以缺少
       |-sex 身份性别【新加】
       返回标准结构
    
### 房屋相关字段都加入了小区相关id
    

    