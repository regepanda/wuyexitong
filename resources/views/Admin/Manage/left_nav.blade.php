<div class="col-sm-2" style="height: 800px">
    <div class="panel panel-default">
        <div class="panel-body shadow_div">

            <ul class="nav nav-sidebar">

                @if(in_array(2,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sUser")class="active"@endif ><a href="/admin_sUser">
                        <span class="glyphicon glyphicon-user">  用户</span></a></li>
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(6,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sAccount")class="active"@endif ><a href="/admin_sAccount">
                            <span class="glyphicon glyphicon-th-list">  账户</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(4,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sPayment")class="active"@endif ><a href="/admin_sPayment">
                            <span class="glyphicon glyphicon-usd">  支付单</span></a></li>
                    @endif
                @endif

                @if(in_array(1,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sImage")class="active"@endif ><a href="/admin_api_sImage">
                        <span class="glyphicon glyphicon-picture">  图片</span></a></li>
                @endif

                @if(in_array(1,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sCommunity")class="active"@endif ><a href="/admin_sCommunity">
                        <span class="glyphicon glyphicon-home">  小区</span></a></li>
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(5,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sCourse")class="active"@endif ><a href="/admin_sCourse">
                            <span class="glyphicon glyphicon-info-sign">  学校</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(1,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sAdmin")class="active"@endif ><a href="/admin_sAdmin">
                            <span class="glyphicon glyphicon-user">  管理员</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(1,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sServe")class="active"@endif ><a href="/admin_sServe">
                            <span class="glyphicon glyphicon-thumbs-up">  服务</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(1,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sAdminPowerGroup")class="active"@endif ><a href="/admin_sAdminPowerGroup">
                            <span class="glyphicon glyphicon-lock">  管理员权限组</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                @if(session("admin.community_group") == null)
                    @if(in_array(1,session("admin.admin_power")))
                    <li @if(session("other.nowSecondModule")=="sCommunityGroup")class="active"@endif ><a href="/admin_sCommunityGroup">
                            <span class="glyphicon glyphicon-map-marker">  物业公司</span></a></li>
                    @endif
                @endif

                <!--如果是物业管理员，就隐藏此链接-->
                <li @if(session("other.nowSecondModule")=="sMonitor")class="active"@endif ><a href="/admin_sMonitor">
                        <span class="glyphicon glyphicon-facetime-video">  监控</span></a></li>

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