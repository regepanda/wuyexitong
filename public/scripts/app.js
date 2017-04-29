var $app = angular.module("wuyexitong",['ngAnimate','ngRoute']);
$app.run(function($rootScope){

    //$rootScope.cache = new cacheManager();
    $rootScope.keywordClassMap = {
        1:"支付宝",
        2:"微信支付",
        3:"翼支付",
        15:"系统账户",
        4:"钟点工",
        5:"陪护工",
        6:"保洁服务",
        7:"月嫂",
        8:"管道疏通",
        9:"开锁服务",
        10:"水电修理",
        11:"家电修理",
        12:"房屋修理",
        13:"定制需求",
        16:"四点半学校",
        14:"物业费"
    };
    $rootScope.keywordStatusMap = {
        1:"未支付",
        2:"已支付",
        3:"取消支付",
        4:"退款中",
        5:"退款完成",
        14:"申请退款",
        6:"已提交",
        7:"准备处理",
        8:"处理中",
        9:"处理完成",
        10:"取消请求",
        11:"待付费",
        12:"已付费",
        13:"取消付费",
        15:"已过期"
    };
    $rootScope.keywordCheckMap = {
        1:"是",
        0:"否"
    };
    $rootScope.keywordUserGroupMap = {
        1:"正常权限用户组",
        2:"未绑定账户用户"
    };
    $rootScope.buildUrlParam = function($limit,$url)
    {
        var url = $url;
        url+="?";
        for(var i in $limit)
        {
            url += "&" + i + "=";
            url += $limit[i] ;//+ "&";

        };
        return url;
    };
});

/*
var cacheManager =function()
{
    this.cache = [];
    this.cacheLength = 1;
    this.putObj = function(obj)
    {
        var newObj = jQuery.extend(true,{},obj);
        this.cache.push(newObj);
        if(this.cache.length > this.cacheLength)
        {
            this.cache.splice(-1,0);
        }
    };
    this.getObj = function()
    {
        if(this.cache.length <= 0)
        {
            return false;
        }
        return this.cache.pop();
    };

}*/
/*
var pageStack =function()
{
    this.stack = {};
    this.addPage = function()
    {

    }
}*/