@extends("Admin.Manage.index")

@section("main")
    @include("Admin.Manage.userThird")
    <input id="nowClass" value="{{$nowClass}}" type="hidden"/>
    <div class="col-sm-10" ng-controller="admin_sAudit">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <div id="pageView" ng-view>

                </div>
            </div>
        </div>
    </div>

@stop
@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
    <script src="/scripts/controllers/admin/manage/sAudit.js"></script>

@stop