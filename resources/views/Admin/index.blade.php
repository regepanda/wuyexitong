@extends("base")
@section("body")
    <div class="bgi_div">
        <img class="full_image" src="/images/index_bg.jpg">
    </div>
    @section("nav")
        <nav class="navbar navbar-default shadow_div">
            <div class="container ">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">后台管理 v{{config("my_config.version")}}</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li @if(session("other.nowModule")=="Tenement")class="active"@endif ><a href="/admin_tenement" class="full_div_a"><span class="glyphicon glyphicon-home"></span>  物业 <span class="sr-only">(current)</span></a></li>
                        <li @if(session("other.nowModule")=="Manage")class="active"@endif ><a  href="/admin_manage" class="full_div_a"><span class="glyphicon glyphicon-cog"></span>  管理</a></li>
                        @if(session("admin.community_group") == null)
                        <li @if(session("other.nowModule")=="System")class="active"@endif ><a href="/admin_system" class="full_div_a"><span class="glyphicon glyphicon-th-large"></span>  系统</a></li>
                        @endIf
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a  href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-cog"></span>  操作 <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">管理员信息</a></li>
                                <li><a href="/admin_logout">登出</a></li>
                            </ul>
                        </li>
                        <li>
                            @if(session("admin.community_group") == null)
                                <a  href="" class="dropdown-toggle">当前登录：<font color="#a52a2a"><?php echo session("admin.admin_nickname"); ?></font></a>
                            @endif
                            @if(session("admin.community_group") != null)
                                <a  href="" class="dropdown-toggle">当前登录：<font color="#a52a2a"><?php echo session("admin.group_name"); ?></font></a>
                            @endif
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>



    @show

    @section("second_nav")

    @show
    @section("left_nav")

    @show

    @section("main")

    @show




@append