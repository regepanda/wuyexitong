@extends("Admin.Manage.index")


@section("main")

    <div class="col-sm-10">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2 class="sub-header">管理员用户 | <button class="btn  btn-success"  data-toggle="modal" data-target="#aAdmin" type="button">添加管理员</button></h2>
                <div class="modal fade" id="aAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">添加管理员用户</h4>
                            </div>
                            <div class="modal-body">
                                <form action="/admin_aAdmin" method="post">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <h4>管理员用户名</h4>
                                    <input type="text " id="inputText" class="form-control" name="admin_username" placeholder="Admin Username">

                                    <h4>管理员昵称</h4>
                                    <input type="text" id="inputText" class="form-control"  name="admin_nickname" placeholder="Admin Nickname">

                                    <h4>密码</h4>
                                    <input type="password" id="inputPassword" class="form-control" name="admin_password" placeholder="Admin Password">

                                    <h4>权限组</h4>
                                    <select class="form-control" name="admin_group">
                                        @foreach($groupData as $value)
                                            <option type="select" value="{{$value->group_id}}" selected="selected">{{$value->group_name}}</option>
                                        @endforeach
                                    </select>
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
                            <th><span class="glyphicon glyphicon-eye-open">  用户名</span></th>
                            <th><span class="glyphicon glyphicon-leaf">  昵称</span></th>
                            <th><span class="glyphicon glyphicon-lock">  所属权限组</span></th>
                            <th><span class="glyphicon glyphicon-leaf">  所属物业公司</span></th>
                            <th><span class="glyphicon glyphicon-cog">  操作</span></th>
                        </tr>
                        </thead>
                        <tbody class="container">

                        @foreach ($articleData as $data)
                            <tr class="container">
                                <td class="container">{{$data -> admin_id}}</td>
                                <td class="container">{{$data->admin_username}}</td>
                                <td class="container">{{$data->admin_nickname}}</td>
                                <td class="container">{{$data->group_name}}</td>
                                <td class="container">{{$data->admin_community}}</td>
                                <td class="container"><!-- Button trigger modal -->

                               <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#upd_{{$data->admin_id}}"><span class="glyphicon glyphicon-edit">修改</span></button>

                                    <div class="modal fade" id="upd_{{$data->admin_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">当前管理员ID：{{$data->admin_id}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="/admin_uAdmin" method="post">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <h4>请输入新的管理员用户名</h4>
                                                        <input type="hidden" name="admin_id" value="{{$data->admin_id}}">
                                                        <input type="text " id="inputText" class="form-control" name="admin_username" placeholder="Admin username" value="{{$data->admin_username}}">
                                                        <h4>请输入新的管理员昵称</h4>

                                                        <input type="text " id="inputText" class="form-control" name="admin_nickname" placeholder="Admin nickname" value="{{$data->admin_nickname}}">
                                                        <h4>请选择新的权限组</h4>

                                                        <select class="form-control" name="admin_group">
                                                            @foreach($groupData as $value)
                                                                @if($value -> group_id == $data->group_id)
                                                                <option type="select" value="{{$value->group_id}}" selected="selected">{{$value->group_name}}</option>
                                                                @else
                                                               <option type="select" value="{{$value->group_id}}">{{$value->group_name}}</option>
                                                               @endif
                                                            @endforeach
                                                        </select>

                                                </div>
                                                <div class="modal-footer">
                                                    <button  class="btn btn-danger btn-sm" type="submit">提交</button>
                                                        </form>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#del_{{$data->admin_id}}"><span class="glyphicon glyphicon-trash">删除</span></button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="del_{{$data->admin_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">警告！</h4>
                                                </div>
                                                <div class="modal-body">
                                                    将要删除该管理员用户！
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="/admin_dAdmin/{{$data->admin_id}}" class="btn btn-danger btn-sm">删除</a>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addCommunityGroup{{$data->admin_id}}">
                                        指定物业公司
                                    </button>
                                    <!-- Modal指定物业公司 -->
                                    <div class="modal fade" id="addCommunityGroup{{$data->admin_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">指定物业公司</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" action="/admin_addCommunityGroup" method="post">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="admin_id" value="{{$data->admin_id}}">

                                                        <input type="hidden" name="admin_username" value="{{$data->admin_username}}">
                                                        <input type="hidden" name="admin_nickname" value="{{$data->admin_nickname}}">
                                                        <input type="hidden" name="admin_group" value="{{$data->admin_group}}">

                                                        <select class="form-control" name="admin_community_group">
                                                            @foreach($communityGroup['data'] as $group)
                                                                @if($data->admin_community_group == $group->group_id)
                                                                <option selected="selected" value="{{$group->group_id}}">{{$group->group_name}}</option>
                                                                @else
                                                                <option value="{{$group->group_id}}">{{$group->group_name}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                                    <button type="submit" class="btn btn-info">提交</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>

                    <hr>

                    @if( $paginate == true)
                        <div class="col-sm-4 col-sm-offset-4">
                            <?php echo $articleData->render(); ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>





@stop