<div class="blog_innner_box">
	<div class="row">
		<div class="col-md-12">
            <h1><a href="{{$post->url}}" style="color: #ca3c08;">{{ $post->title }}</a></h1>
			<h2 style="color: #858c96;"><i>{{ date('F d, Y', strtotime($post->published_at)) }}</i></h2>
			<p>{!! $post->content !!}</p>
		</div>
	</div>

	<hr />
</div>