@extends("Admin.index")

@section("second_nav")
    @include("Admin.Manage.second_nav")
@append
@section("left_nav")
    @include("Admin.Manage.left_nav")
@append

@section("main")
    <div class="col-sm-10">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2>管理主界面</h2>
            </div>
        </div>
    </div>
@stop