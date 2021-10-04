@extends('admin.layout')

@section('og_meta')
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="{{ config('blog.og_twitter }}">
        <meta name="twitter:creator" content="{{ config('blog.og_twitter }}">
        <meta name="twitter:title" content="{{ $post->title }}">
        <meta name="twitter:description" content="{{ strip_tags($post->chapo) }}">
        <meta name="twitter:image" content="{{ $post->image }}">

        <meta property="og:title" content="{{ $post->title }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ $post->image }}"/>
        <meta property="og:site_name" content="{{ config('blog.site_name') }}"/>
        <meta property="og:description" content="{{ strip_tags($post->chapo) }}" />
        <meta property="og:url" content="{{ URL::to('/') }}{{ $post->url }}" />
        <meta property="og:author" content="{{ config('blog.og_author') }}" />
        <meta property="og:author" content="{{ config('blog.og_publisher') }}" />
@endsection

@section('content')

    <div class="main">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="{{ config('blog.base_path') }}">{{ config('blog.base_name') }}</a></li>
                <li><a href="{{ $post->Category->url }}">{{ $post->Category->name }}</a></li>
                <li class="active">{{ $post->title }}</li>

            </ul>
            <div class="row margin-bottom-40">

                <div class="col-md-9 col-sm-9">
                    <div class="content-page">
                        <h1>{{ $post->title }}</h1>

                        @if ($post->image != '')
                        <p><img style="width:100%" src="{{ $post->image }}" alt="" class="img-responsive"></p>
                        @endif

                        <div class="post_header">
                            {!! $post->chapo !!}
                        </div>

                        <div class="post_content">
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>

                @include('blog::sidebar')

            </div>

        </div>
    </div>

@endsection