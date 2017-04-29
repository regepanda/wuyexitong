@extends("Admin.System.index")

@section("main")

    <div class="col-sm-10">
    <div class="panel panel-default">
    <div class="panel-body shadow_div">
    <div id="pageView">
    <h2>查看首页图片  |  <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#add_">增加首页图片</button></h2>



    <div class="modal fade" id="add_" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加首页图片</h4>
                </div>
                <div class="modal-body">
                    <form method="post" action="/Api_addIndexImage">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label >请输入图片ID</label>
                            <?php echo "<br/>"; ?>
                            <input type="text" class="form-control" name="image_id">
                        </div>
                        <div class="form-group">
                            <label >请输入图片URL</label>
                            <?php echo "<br/>"; ?>
                            <input type="text" class="form-control" name="image_url">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">提交</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($data != null)
    @foreach($data as $single)
        <div class="col-sm-3">
            <div class="thumbnail" >
                <img src="/getImage/{{ $single }}" class="img-rounded img-responsive" alt="..." style="width: 280px;height: 200px">
                <div class="caption">
                    <h3>ID:{{ $single }}</h3>
                    <h3><button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete{{ $single }}">
                        移除
                    </button></h3>
                </div>
            </div>
        </div>
        <!-- Modal 移除-->
        <div class="modal fade" id="delete{{ $single }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">移除图片</h4>
                    </div>
                    <div class="modal-body">
                        警告！此操作危险，确定继续？
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <a href="/Api_deleteIndexImage/{{$single}}" class="btn btn-danger btn-sm">移除</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @endif
    </div>
    </div>
    </div>
    </div>
@stop

