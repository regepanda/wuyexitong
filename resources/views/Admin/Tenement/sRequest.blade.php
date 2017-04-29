@extends("Admin.Tenement.index")


@section("main")
    <div class="col-sm-10" ng-controller="admin_sRequest">
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
    <script src="/scripts/controllers/admin/tenement/sRequest.js"></script>

@stop
