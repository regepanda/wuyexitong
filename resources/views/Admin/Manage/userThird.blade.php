<div class="col-sm-10 ">
    <div class="panel panel-default">
        <div class="panel-body normal_third_item shadow_div">
            <div class="col-sm-12" style="height: 10px"></div>

            @if(in_array(2,session("admin.admin_power")))
            <div class="col-sm-1  @if(session("other.nowThirdModule")=="sUser") active_third_item @endif">
                <div class="col-sm-12" style="text-align: center">
                    <span class="glyphicon glyphicon-user" aria-hidden="true" style="font-size: 300%;">

                    </span>
                </div>
                <div class="col-sm-12" style="text-align: center">
                    <h4><a href="/admin_sUser">用户</a></h4>
                </div>
            </div>
            @endif

            @if(in_array(3,session("admin.admin_power")))
            <div class="col-sm-1 @if(session("other.nowThirdModule")=="sHouse") active_third_item @endif">
                <div class="col-sm-12" style="text-align: center">
                    <span class="glyphicon glyphicon-home " aria-hidden="true" style="font-size: 300%;">

                    </span>
                </div>
                <div class="col-sm-12 " style="text-align: center">
                    <h4><a href="/admin_sHouse">房屋</a></h4>
                </div>
            </div>
            @endif

            @if(in_array(3,session("admin.admin_power")))
            <div class="col-sm-1 @if(session("other.nowThirdModule")=="sCar") active_third_item @endif">
                <div class="col-sm-12 " style="text-align: center">
                    <span class="glyphicon glyphicon-road " aria-hidden="true" style="font-size: 300%;">

                    </span>
                </div>
                <div class="col-sm-12" style="text-align: center">
                    <h4><a href="/admin_sCar">汽车</a></h4>
                </div>
            </div>
            @endif

            @if(in_array(3,session("admin.admin_power")))
                <div class="col-sm-1 @if(session("other.nowThirdModule")=="sCarPosition") active_third_item @endif">
                    <div class="col-sm-12 " style="text-align: center">
                    <span class="glyphicon glyphicon-header" aria-hidden="true" style="font-size: 300%;">

                    </span>
                    </div>
                    <div class="col-sm-12" style="text-align: center">
                        <h4><a href="/admin_sCarPosition">停车位</a></h4>
                    </div>
                </div>
            @endif

            @if(in_array(3,session("admin.admin_power")))
            <div class="col-sm-1 @if(session("other.nowThirdModule")=="sTrueinfo") active_third_item @endif">
                <div class="col-sm-12 " style="text-align: center">
                    <span class="glyphicon glyphicon-align-justify" aria-hidden="true" style="font-size: 300%;">

                    </span>
                </div>
                <div class="col-sm-12" style="text-align: center">
                    <h4><a href="/admin_sTrueinfo">身份</a></h4>
                </div>
            </div>
            @endif

            @if(in_array(2,session("admin.admin_power")))
            <div class="col-sm-1  @if(session("other.nowThirdModule")=="sChild") active_third_item @endif">
                    <div class="col-sm-12" style="text-align: center">
                    <span class="glyphicon glyphicon-heart-empty " aria-hidden="true" style="font-size: 300%;">

                    </span>
                </div>
                <div class="col-sm-12" style="text-align: center">
                    <h4><a href="/admin_sChild">儿童</a></h4>
                </div>
            </div>
            @endif

            <!--如果是物业管理员，就隐藏此链接-->
            @if(session("admin.community_group") == null)
                @if(in_array(2,session("admin.admin_power")))
                <div class="col-sm-1 @if(session("other.nowThirdModule")=="sUserPowerGroup") active_third_item @endif ">
                    <div class="col-sm-12" style="text-align: center">
                        <span class="glyphicon glyphicon glyphicon-ok-sign " aria-hidden="true" style="font-size: 300%;">

                        </span>
                    </div>
                    <div class="col-sm-12 " style="text-align: center">
                        <h4><a href="/admin_sUserPowerGroup">权限组</a></h4>
                    </div>
                </div>
                @endif
            @endif

        </div>
    </div>
</div>