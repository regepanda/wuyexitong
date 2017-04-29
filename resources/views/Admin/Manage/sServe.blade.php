@extends("Admin.Manage.index")


@section("main")

    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2 class="sub-header">服务 | <button class="btn  btn-success"  data-toggle="modal" data-target="#aAdmin" type="button">添加服务</button></h2>
                <div class="modal fade" id="aAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">添加服务</h4>
                            </div>
                            <div class="modal-body">
                                <form action="/admin_aServe" method="post">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <h4>服务名</h4>
                                    <input type="text " id="inputText" class="form-control" name="class_name">
                                    <h4>服务说明</h4>
                                    <input type="text" id="inputText" class="form-control"  name="class_intro">
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>



                <div class="table-responsive">

                    <table class="table table-striped" class="table table-hover" >
                        <thead class="container">
                        <tr class="container">
                            <th><span class="glyphicon glyphicon-tag">  ID</span></th>
                            <th><span class="glyphicon glyphicon-eye-open">  服务名</span></th>
                            <th><span class="glyphicon glyphicon-book"> 服务说明</span></th>
                            <th><span class="glyphicon glyphicon-cog">  操作</span></th>
                        </tr>
                        </thead>
                        <tbody class="container">

                        @foreach ($output as $data)
                            <tr class="container">
                                <td class="container">{{$data -> class_id}}</td>
                                <td class="container">{{$data-> class_name}}</td>
                                <td class="container">{{$data-> class_intro}}</td>
                                <td class="container"><!-- Button trigger modal -->

                                    @if($data->class_origin === 0)
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#upd_{{$data->class_id}}"><span class="glyphicon glyphicon-edit">修改</span></button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#del_{{$data->class_id}}"><span class="glyphicon glyphicon-trash">删除</span></button>
                                    @endif
                                    <div class="modal fade" id="upd_{{$data->class_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">当前服务ID：{{$data->class_id}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="/admin_uServe" method="post">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <h4>请输入新的服务名</h4>
                                                        <input type="hidden" name="class_id" value="{{$data->class_id}}">
                                                        <input type="text " id="inputText" class="form-control" name="class_name"  value="{{$data->class_name}}">
                                                        <h4>请输入新的服务介绍</h4>
                                                        <input type="text " id="inputText" class="form-control" name="class_intro" value="{{$data->class_intro}}">
                                                </div>
                                                <div class="modal-footer">
                                                    <button  class="btn btn-danger btn-sm" type="submit">提交</button>
                                                    </form>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Modal -->
                                    <div class="modal fade" id="del_{{$data->class_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">警告！</h4>
                                                </div>
                                                <div class="modal-body">
                                                    将要删除该服务！
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="/admin_dServe/{{$data->class_id}}" class="btn btn-danger btn-sm">删除</a>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                    <hr>

                    <div class="col-sm-4 col-sm-offset-4">
                        <?php echo $output->render(); ?>
                    </div>


                </div>
            </div>
        </div>
    </div>
    </div>
    </div>





@stop