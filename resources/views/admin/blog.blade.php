@extends('admin.layout')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Blog
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-envelope"></i> Home</a></li>
        <li class="active">Blog</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <h2>Categories</h2>
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th># of posts</th>
            {{--<th>Actions</th>--}}
        </tr>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->posts_num }}</td>
                <td>
{{--                    <a href="{{ action('AdminController@editCat', $category->id) }}" class="btn btn-primary">Edit</a>--}}
                </td>
            </tr>

        @endforeach
    </table>
    <input type="text" id="new_cat" name="new_cat" value="" /> 
    <button id="create_category" class="btn btn-success">Create Category</button>

    <hr />
    <h2>Posts</h2>

    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Published on</th>            
            <th>Actions</th>
        </tr>
    @foreach($posts as $post)
        <tr>
            <td>{{ $post->id }}</td>
            <td>{{ $post->title }}</td>
            <td>{{ $post->published_at }}</td>            
            <td>
                <a href="{{ action('AdminController@editPost', $post->id) }}" class="btn btn-primary">Edit</a>
                <button data-id="{{$post->id}}" class="btn btn-publish btn-success">Delete</button>
            </td>
        </tr>

    @endforeach
    </table>
    <a href="{{ action('AdminController@createPost') }}" class="btn btn-success">Create post</a>

    <hr />
</section><!-- /.content --> 

    <script>
    $().ready(function() {

        // categories

        $('#create_category').click(function(e) {
            e.preventDefault();
            $(this).addClass('disabled');
            var btn = $(this);

            $.post('/admin/blog/create_category', {
                _token: "{{ csrf_token() }}",
                category_name: $('#new_cat').val()

            }, function(data) {
                $(btn).removeClass('disabled');

                if (data.status == 'success') {
                    toastr.success('Category created.');

                    console.log(data.object);

                    // TODO add table row
					window.location.reload();
                } else {
                    toastr.error(data.error);
                }
            }, 'json');
        });

        // publishing
        $('.btn-publish').click(function(e) {
            e.preventDefault();
            $(this).addClass('disabled');

            var post_id = $(this).data('id');
            var btn = $(this);
            $(btn).removeClass('disabled');
            $.post('/admin/blog/delete_post', {

                _token: "{{ csrf_token() }}",
                post_id: post_id

            }, function(data) {

                window.location.reload();
            }, 'json');
        });

    });
    </script>

@stop