@extends("Admin.System.index")

@section("main")
    <div class="col-sm-10" ng-controller="admin_sApiTest">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <div class="form-group">
                    <label >发送链接</label>
                    <input type="text" class="form-control" ng-model="url" placeholder="/api_xxx">
                </div>
                <div class="form-group">
                    <label >发送数据：</label>
                    <textarea class="form-control" ng-model="inputData"  placeholder='POST写为json格式{"a":"as","b":12},GET为?key=value&key2=v2' rows="3"></textarea>
                </div>
                <button ng-click="getMethod()" class="btn btn-default btn-sm">GET</button>
                <button ng-click="postMethod()" class="btn btn-default btn-sm">POST</button>
            </div>
        </div>
        <div class="panel panel-default ">
            <div class="panel-body shadow_div" >
                <h2>返回数据 <small>URL : @{{ dstUrl }}</small></h2>
                <div class="form-group">
                    <label >缓存界面，你可以在这里放一些查询的结果：</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>
                @{{ returnData }}


            </div>
        </div>
    </div>
@stop

@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
    <script src="/scripts/controllers/admin/system/sApiTest.js"></script>

@stop