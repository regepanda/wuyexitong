@extends("Admin.Manage.index")

@section("main")
    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2 class="sub-header">管理员权限组 | <button class="btn  btn-success "  data-toggle="modal" data-target="#aAdminPowerGroup" type="button">添加权限组</button></h2>
                <div class="modal fade" id="aAdminPowerGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">添加权限组</h4>
                            </div>
                            <div class="modal-body">
                                <form action="/admin_aAdminPowerGroup" method="post">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <h4>权限组名称</h4>
                                    <input type="text " id="inputText" class="form-control" name="group_name" placeholder="Group name">
                                    <div class="modal-footer">
                                        <button class="btn btn-sm btn-primary" type="submit">提交</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive" >
                <table  class="table table-striped" >
                    <thead>
                    <tr>
                        <th><span class="glyphicon glyphicon-tag">  ID</span></th>
                        <th><span class="glyphicon glyphicon-lock">  权限组</span></th>
                        <th><span class="glyphicon glyphicon-cog">  操作</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>

                    @foreach($data as $single)
                        <tr>
                            <td>{{$single -> group_id}} </td>
                            <td>{{$single -> group_name}} </td>
                            <td><!-- Button trigger modal -->
                                <a href="/admin_moreAdminPowerGroup/{{$single -> group_id}}" class="btn btn-info btn-sm">详情</a>


                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#upd_{{$single -> group_id}}"><span class="glyphicon glyphicon-edit">修改</span></button>
                                <div class="modal fade" id="upd_{{$single -> group_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">请输入新的权限组名称</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form action="/admin_uAdminPowerGroup" method="post">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="group_id" value="{{$single -> group_id}}">
                                                    <input type="text " id="inputText" class="form-control" name="group_name" placeholder="Group name" value="{{$single -> group_name}}">

                                            </div>
                                            <div class="modal-footer">
                                                <button  class="btn btn-warning btn-sm" type="submit">修改</button>
                                                </form>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#del_{{$single -> group_id}}"><span class="glyphicon glyphicon-trash">删除</span></button>
                                <div class="modal fade" id="del_{{$single -> group_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">警告！</h4>
                                            </div>
                                            <div class="modal-body">
                                                将要删除该权限组！
                                            </div>
                                            <div class="modal-footer">
                                                <a href="/admin_dAdminPowerGroup/{{$single -> group_id}}" class="btn btn-danger btn-sm">删除</a>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>

                        </tr>
                        @endforeach

                        </tr>

                    </tbody>
                </table>
                <!-- 分页 -->
            </div>
        </div>
    </div>
    </div>
@stop