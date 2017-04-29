@extends("Admin.System.index")


@section("main")
    <div class="col-sm-10" >
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2>查看客户端版本 | <small>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addVersion">
                            添加版本
                        </button>

                       </small>
                </h2>
                <hr>
                <table class="table table-bordered table-responsive table-hover">
                    <tr>
                        <th>版本号</th>
                        <th>版本名</th>
                        <th>类型</th>
                        <th>路径</th>
                        <th>时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($version as $data)
                    <tr>
                        <td>{{$data->version_id}}</td>
                        <td>{{$data->version_name}}</td>
                        <td>{{$data->version_type}}</td>
                        <td>{{$data->version_path}}</td>
                        <td>{{$data->version_create_time}}</td>
                        <td><!-- Button trigger modal -->
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#del_{{$data->version_id}}">
                                删除
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="del_{{$data->version_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">删除一个版本</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h2>确认要删除这个版本</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">不要</button>
                                            <a href="/admin_dVersion/{{$data->version_id}}" class="btn btn-danger">是的，删除它</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addVersion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加客户端版本</h4>
                </div>
                <div class="modal-body">
                    <form action="/admin_aVersion" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label >版本名</label>
                            <input type="text" name="name" class="form-control" placeholder="例如 V1.02">
                        </div>

                        <div class="form-group" >
                            <select class="form-control" name="type">
                                <option value="Android">Android 平台</option>
                                <option value="IOS">IOS 苹果平台</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label >上载文件</label>
                            <input type="file" name="file">
                            <p class="help-block">上传时长请耐心等待</p>
                        </div>
                    {!! csrf_field() !!}



                </div>
                <div class="modal-footer">
                    <a class="btn btn-default" data-dismiss="modal">取消</a>
                    <button type="submit" class="btn btn-primary">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section("bottom")
    @include("lib.ng_lib")


@stop   