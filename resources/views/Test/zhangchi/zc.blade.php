@extends("base")

@section("body")
    <div class="col-sm-10" ng-controller="zczc">
                <div ng-view>


                </div>
    </div>
@stop

@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
    <script src="/scripts/controllers/test/zhangchi.js"></script>
@stop