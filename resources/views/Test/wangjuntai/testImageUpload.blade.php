@extends("base")

@section("body")
    <form action="/app_addRequest" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label >用户介绍</label>
            <input type="text" class="form-control" name="user_intro" >

            <input type="hidden"  name="phone" value="12134536">
            <input type="hidden"  name="address" value="爆破路">
            <input type="hidden"  name="class" value="1">
            <input type="hidden"  name="access_token" value="f320bcf148f4fc0655f3ace22083ce5e60afe339">
        </div>

        <div class="form-group">
            <label for="exampleInputFile">图片1</label>
            <input type="file" name="image_0" >
            <p class="help-block">Example block-level help text here.</p>
            <label for="exampleInputFile">图片2</label>
            <input type="file" name="image_1" >
            <p class="help-block">Example block-level help text here.</p>
            <label for="exampleInputFile">图片3</label>
            <input type="file" name="image_2" >
            <p class="help-block">Example block-level help text here.</p>
        </div>

        <button type="submit" class="btn btn-default">Submit</button>
    </form>
@append