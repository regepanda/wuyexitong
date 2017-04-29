@extends("base")

@section("body")
    <form action="/test_addImage_app" method="post"  enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <input type="hidden" name="image_url" value="">
        </div>
        <div class="form-group">
            <label for="exampleInputFile">File input</label>
            <input type="file" name="image_file">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>

@stop















