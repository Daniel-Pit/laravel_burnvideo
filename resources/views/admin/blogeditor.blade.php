@extends('admin.layout')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    @if ($post_id > 0)
		<h1>
			Edit a Post
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-envelope"></i> Home</a></li>
			<li class="active">Blog</li>
		</ol>
    @else
		<h1>
			Create a Post
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-envelope"></i> Home</a></li>
			<li class="active">Blog</li>
		</ol>
    @endif
</section>

<!-- Main content -->
<section class="content">
<div class="row">
        <div class="col-xs-1">
            &nbsp;
        </div>
        <div class="col-xs-10">
			<div class="box" style="padding-top: 10px;">
				<form class="form-horizontal" >
					<div class="form-group">
						<label class="col-sm-2 control-label" for="title">Title</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="title">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="slug">Slug</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="slug" name="slug">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="chapo">Category</label>
						<div class="col-sm-10">
							<?php $category = isset($post) ? $post->category_id : null; ?>
							{!! Form::select('category_id', $categories, $category, ["id"=>"category_id", "class" => "form-control"] ) !!}
						</div>
					</div>


					<div class="form-group">
						<label class="col-sm-2 control-label" for="content">Content</label>
						<div class="col-sm-10">
							<textarea id="content" name="content" class="ckeditor form-control" style="width:100%;height:600px;resize:none"></textarea>
						</div>
					</div>

					<input type="hidden" id="post_id" value="{{ $post_id }}" />

					<button id="btn_save_post" type="submit" class="col-md-offset-2 btn btn-primary">Save post</button>


				</form>    
			</div>
		</div>
        <div class="col-xs-1">
            &nbsp;
        </div>        
	</div>

</section><!-- /.content --> 

<script>

    function showPost(data) {
        $('#title').val(data.title);
        $('#slug').val(data.slug);
        $("#content").summernote('code', data.content);        
        $('#published_at').val(data.published_at);
        $('#category_id').val(data.category_id);
    }

    $().ready(function() {

        $('#content').summernote({
            minHeight: 400,             // set minimum height of editor
            maxHeight: 800,             
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']], // Still buggy
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']]
            ],
            fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '72']
        });         
        // loading
        var post_id = $('#post_id').val();
        if (post_id > 0) {
            $.post('/admin/blog/load_post', {
                _token: "{{ csrf_token() }}",

                post_id: post_id
            }, function(data) {
                showPost(data);

            }, 'json');
        }

        // saving
        $('#btn_save_post').click(function(e) {
            e.preventDefault();

            $(this).addClass('disabled');

            console.log('cat_id : '+$('#category_id').val());


            $.post('/admin/blog/save_post', {
                _token: "{{ csrf_token() }}",

                title: $('#title').val(),
                slug: $('#slug').val(),
                //chapo: CKEDITOR.instances['chapo'].getData(),
                content: $('#content').summernote('code'),
                //published_at: $('#published_at').val(),
                category_id: $('#category_id').val(),

                post_id: $('#post_id').val()
            }, function(data) {

                $('#btn_save_post').removeClass('disabled');
                $('#post_id').val(data.id);

                toastr.success('Post saved.');

                showPost(data);
            }, 'json');
        });

        // publishing
        $('#btn_publish_post').click(function(e) {
            e.preventDefault();

            $(this).addClass('disabled');

            $.post('/admin/blog/publish_post', {
                _token: "{{ csrf_token() }}",

                post_id: $('#post_id').val()
            }, function(data) {

                $('#btn_publish_post').removeClass('disabled');

            }, 'json');
        });
    });
</script>
@stop