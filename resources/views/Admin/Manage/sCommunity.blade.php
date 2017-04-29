@extends("Admin.Manage.index")

@section("main")
    <input type="hidden" id="flag" value="{{session("admin.community_group")==null?0:session("admin.community_group")}}">
    <div class="col-sm-10" ng-controller="admin_sCommunity">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <div id="pageView"  ng-view>


                </div>
            </div>
        </div>
    </div>
@stop

@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
    <script src="/scripts/controllers/admin/manage/sCommunity.js"></script>
@stop