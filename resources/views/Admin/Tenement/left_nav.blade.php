<div class="col-sm-2" style="height: 800px">
    <div class="panel panel-default ">
        <div class="panel-body shadow_div">

            <ul class="nav nav-sidebar">

                @if(in_array(7,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sRequest")class="active"@endif ><a href="/admin_sRequest">
                        <span class="glyphicon glyphicon-hand-up">  客户请求</span></a></li>
                @endif

                @if(in_array(8,session("admin.admin_power")))
                <li @if(session("other.nowSecondModule")=="sTax")class="active"@endif ><a href="/admin_sTax">
                        <span class="glyphicon glyphicon-usd">  缴费</span></a></li>
                @endif

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