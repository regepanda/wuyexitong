@extends("Admin.Manage.index")

@section("main")
    <script type="text/javascript">
        $(function(){
            //用户在添加权限到当前权限组的时候可以一键全选
            $("#CheckedAllPower").click(function(){
                if(this.checked)
                {
                    //$("input[name='newsletter']").attr("checked", true);
                    $("[name='power_id_array[]']").prop("checked",true);
                }
                else
                {
                    $("[name='power_id_array[]']").prop("checked",false);
                }
            });
            //用户在添加用户到当前权限组的时候可以一键全选
            $("#CheckedAllAdmin").click(function(){
                if(this.checked)
                {
                    $("[name='admin_id_array[]']").prop("checked",true);
                }
                else
                {
                    $("[name='admin_id_array[]']").prop("checked",false);
                }
            });
        });
    </script>

    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <h2 class="sub-header">当前权限组：{{$GroupData[0]->group_name}}</h2>
                <hr>

                <div class="col-sm-5 ">
                    <table class="table">

                        <thead>
                        <tr>
                            <th>当前权限</th>

                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($AdminPowerGroup as $data)
                            <tr>
                                <td>{{$data->power_name}}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#del_{{$data->power_id}}">
                                        移除
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="del_{{$data->power_id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">


                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">警告！</h4>

                                                </div>
                                                <div class="modal-body">
                                                    <form action="/admin_removePowerToAdminPowerGroup" method="post">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_token"
                                                               value="{{ csrf_token() }}">
                                                        <input type="hidden" name="relation_group_id"
                                                               value="{{$data->group_id}}">
                                                        <input type="hidden" name="relation_power_id"
                                                               value="{{$data->power_id}}">
                                                        将要移除该权限！
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-danger btn-sm" type="submit">移除</button>
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">返回
                                                    </button>
                                                </div>

                                                </form>

                                            </div>
                                        </div>

                                </td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>



                <div class="col-sm-5 ">
                    <table class="table">

                        <thead>
                        <tr>
                            <th>当前管理员</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                            @foreach ($articleAdmin as $data)
                                @if($data->admin_group!=NULL)
                                    <td>{{$data->admin_username}}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#del_admin_{{$data->admin_id}}">移除
                                        </button>
                                        <div class="modal fade" id="del_admin_{{$data->admin_id}}" tabindex="-1" role="dialog"
                                             aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title" id="myModalLabel">警告！</h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="/admin_removeAdminToAdminPowerGroup" method="post">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <input type="hidden" name="admin_id" value="{{ $data->admin_id }}">
                                                            将要移除该管理员！
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-danger btn-sm" type="submit">移除</button>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">返回
                                                        </button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                        </tr>
                        @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>

                </form>


                <div class="col-sm-2">
                    <button type="button" class="btn  btn-primary" data-toggle="modal"
                            data-target="#add_power_{{$GroupData[0]->group_id}}">添加权限
                    </button>
                    <div class="modal fade" id="add_power_{{$GroupData[0]->group_id}}" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">请选择要添加的权限</h4>
                                </div>
                                <form action="/admin_addPowerToAdminPowerGroup" method="post">
                                    <div class="modal-body">

                                        <input type="checkbox" id="CheckedAllPower"><b>全选/全不选</b>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="group_id" value="{{$GroupData[0]->group_id}}">
                                        @foreach($checkPower as $value1)
                                            @if(!in_array("$value1->power_id",$power_ids))
                                                <h4><input type="checkbox" name="power_id_array[]"
                                                           value="{{$value1->power_id}}">{{$value1->power_name}}</h4>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary " type="submit">添加</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#add_admin_{{$GroupData[0]->group_id}}">添加管理员
                    </button>
                    <div class="modal fade" id="add_admin_{{$GroupData[0]->group_id}}" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">请选择要添加的管理员</h4>
                                </div>
                                <form action="/admin_addAdminToAdminPowerGroup" method="post">
                                    <div class="modal-body">

                                        <input type="checkbox" id="CheckedAllAdmin"><b>全选/全不选</b>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="group_id" value="{{$GroupData[0]->group_id}}">
                                        @foreach($checkAdmin as $value2)
                                            @if($GroupData[0]->group_id != $value2->admin_group)
                                                <h4><input type="checkbox" value="{{$value2->admin_id}}"
                                                           name="admin_id_array[]"> {{$value2->admin_username}}</h4>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary " type="submit">添加</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


@stop