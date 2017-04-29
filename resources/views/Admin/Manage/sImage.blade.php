@extends("Admin.Manage.index")
@section("main")

    <div class="col-sm-10">
        <div class="panel panel-default">
            <div class="panel-body shadow_div">
                <div id="pageView">
                    <h2>查看图片 |
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_">
                            增加图片
                        </button>
                    </h2>

                    <div class="modal fade" id="add_" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">添加图片</h4>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="/admin_aImage" enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="form-group">
                                            <label>请选择你要上传的图片文件</label>
                                            <input type="file" id="exampleInputFile" name="image_file">
                                        </div>
                                        <div class="form-group">
                                            <label>请输入图片名</label>
                                            <?php echo "<br/>"; ?>
                                            <input type="text" class="form-control" name="image_name">
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">提交</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-10">
                        @foreach($data as $single)
                            <div class="col-sm-3">
                                <div class="thumbnail">
                                    <img src="/getImage/{{ $single -> image_id }}" class="img-rounded img-responsive"
                                         alt="..." style="width: 280px;height: 200px">

                                    <div class="caption">
                                        <h3>图片id:{{$single-> image_id}}</h3>
                                        <h3>图片名：{{ $single -> image_name }}</h3>
                                        <div>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#del_{{$single->image_id}}"><span class="glyphicon glyphicon-trash">删除</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal -->
                            <div class="modal fade" id="del_{{$single->image_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">警告！</h4>
                                        </div>
                                        <div class="modal-body">
                                            将要删除该图片！
                                        </div>
                                        <div class="modal-footer">
                                            <a href="/admin_dImage/{{$single->image_id}}" class="btn btn-danger btn-sm">删除</a>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        @endforeach
                    </div>

                    <?php echo $data->render(); ?>
                </div>
            </div>
        </div>
    </div>




@stop

@section("bottom")
    @include("lib.ng_lib")
    <link rel="stylesheet" href="/style/viewAnimate.css">
@stop


