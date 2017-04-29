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
    
    查看公告板:GET /app_getBillboard
    创建用户接口:POST /app_createUser
        |-username
        |-password
        |-phone
        |-nickname
        |-sex
        |-birthday
    重置密码接口:GET /app_resetPassword
        |-username  用户名
        |—phone     手机号码（两者任意选择一个)
    验证手机接口:GET /app_verifyPhone
        |-phone     手机号码
    登录接口:GET    /app_login
        |-username
        |-password
    
    
    登出接口：GET /app_logout
    

# 3 请求登录与返回信息
    如果你需要访问登录权限的接口，首先你应该登录获得一个token  
    登录接口:
    'GET    /app_login  
            |-username
            |-password'
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
    
    
### 上传真实信息接口 GET  /app_getTrueInfo
        
        除了access_token 无需传递任何参数，不符合1.2中的标准查询接口
        
        返回标准结构
        其中data结构
         {
            info_id:int             系统内部id
            info_name:string,       真实姓名
            info_intro:string       身份证号
        }
        
        
### 上传真实信息接口 POST /app_addTrueInfo
       :
       需要发送的信息
       |-name
       |-ic_id
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
              
            },
            {...},
            {...}
        ]
    
    
### 添加房屋信息 POST  /app_addHouse
         
         
         需要传递下面的信息
            |-area(面积)
            |-address(地址)
            
         返回标准结构

### 修改房屋信息 POST /app_updateHouse
      
        
        需要传递下面的信息
            |-area(面积)
            |-address(地址)
            |-id(系统内部id)
        忽略字段表示不修改，但必须要id
        返回标准结构
### 删除房屋信息  GET  /app_delHouse
        需要传递下面的信息
         |-id      (系统内部id)
        
        返回标准结构


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
### 获取请求:GET    /app_getRequest
    符合标准格式，可以查询到用户请求的服务
    返回标准结构，其中data是一个数组
    [
        {//数据一
            request_id:int //系统内部id
            request_class:string,//类别码
            request_user_intro:string,//用户描述/介绍
            request_admin_intro:string,//客服描述
            request_status:int,         //状态码
            request_create_time:string,//创建时间
            request_update_time:string,//更新时间
            request_payment:int,      //支付单号码。如果没有则为0
            
          
        },
        {数据二...},
        {数据三...}
    ]
### 提交请求 POST  /app_addRequest
    发送一个请求到服务器
    需要以下数据
    |-class
    |-user_intro
    |-status
    
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
    
     返回标准结构，其中data是一个数组
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
                
                
              
            },
            {数据二...},
            {数据三...}
        ]

    
    
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
    待定
    具体逻辑需要商议支付流程
    需要提供支付单的id
    |-id
    
