<div class="col-sm-2" style="height: 800px">
    <div class="panel panel-default">
        <div class="panel-body  shadow_div">

            <ul class="nav nav-sidebar">

                @if(in_array(5,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sMessage")class="active"@endif ><a href="/admin_sMessage">
                        <span class="glyphicon glyphicon-comment">  系统信息</span></a></li>
                @endif

                @if(in_array(5,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sLog")class="active"@endif ><a href="/admin_sLog">
                        <span class="glyphicon glyphicon-calendar">  记录</span></a></li>
                @endif

                @if(in_array(5,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sBillboard")class="active"@endif ><a href="/admin_sBillboard">
                        <span class="glyphicon glyphicon-credit-card">  公告牌</span></a></li>
                @endif

                @if(in_array(5,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="indexImage")class="active"@endif ><a href="/admin_sIndexImage">
                        <span class="glyphicon glyphicon-picture">  首页图片</span></a></li>
                @endif
                @if(in_array(5,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="SystemUpdate")class="active"@endif ><a href="/admin_sVersion">
                            <span class="glyphicon glyphicon-download-alt">  客户端版本</span></a></li>
                @endif

                <li @if(session("other.nowSecondModule")=="sApiTest")class="active"@endif ><a href="/admin_sApiTest">测试接口</a></li>
            </ul>


        </div>
    </div>
</div>
<style>
    /* Sidebar navigation */
    .nav-sidebar {
        margin-right: -21px; /* 20px padding + 1px border */
        margin-bottom: 20px;
        margin-left: -20px;

    }
    .nav-sidebar > li > a {
        padding-right: 20px;
        padding-left: 20px;
    }
    .nav-sidebar > .active > a,
    .nav-sidebar > .active > a:hover,
    .nav-sidebar > .active > a:focus {
        color: #fff;
        background-color: #428bca;
    }

</style>