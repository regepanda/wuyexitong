@extends("base")

@section("body")
<div class="col-sm-12" ng-controller="test_payment">
    <button class="btn btn-default" ng-click="getPayment()">Button</button>
    @{{paymentData}}
</div>
@stop
@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
    <script src="/scripts/controllers/test/pl.js"></script>

@stop