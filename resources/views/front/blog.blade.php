@extends('front.layout')
@section('content')
    <div id="main" class="site-main">
        <div class="container ">
            <div class="blog">
                <div id="primary" class="content-area">

                    <div id="content" class="site-content" role="main">


                        <div class="leftbox">


                            <div class="blog_innner_box">

								<div class="row margin-bottom-40">
									<div class="col-md-12 col-sm-12">
										<div class="blog_innner_box">
										@if(!empty($post))
											<div class="row">
												<div class="col-md-12">
													<h1 ><a href="{{$post->url}}" class="blog-title">{{ $post->title }}</a></h1>
													<h2 class="blog-date"><i>{{ date('F d, Y', strtotime($post->published_at)) }}</i></h2>
													<p>{!! $post->content !!}</p>
												</div>
											</div>

											<hr />
										@endif
										</div>
									</div>

								</div>

							</div>

                            <!-- <nav class="navigation paging-navigation" role="navigation">
                                <h1 class="screen-reader-text">Posts navigation</h1>
                                <div class="nav-links">
                            
                                    <div class="nav-previous"><a href="https://www.burnvideo.net/blog/page/2/" ><span class="meta-nav">&larr;</span> Older posts</a></div>
                            
                            
                                </div> --><!-- .nav-links -->
                            <!-- </nav> --><!-- .navigation -->

                        </div>



                        <div class="right_recent_post" >



                            <!-- <h2><span>Search</span></h2>
                            
                            <p>
                            <form role="search" method="get" class="search-form" action="https://www.burnvideo.net/">
                                <label>
                                    <span class="screen-reader-text">Search for:</span>
                                    <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="s" title="Search for:" />
                                </label>
                                <input type="submit" class="search-submit" value="Search" />
                            </form>
                            
                            
                            
                            
                            </p> -->



                            <h2><span>Recent posts</span></h2>
                            <ul>
                                @foreach($recent_posts as $eachpost)
                                    <li><a href="{{ $eachpost->url }}" style="font-size: 16px; color: #888;">{{ $eachpost->title }}</a> </li> 
                                @endforeach                            
                            </ul>




                            <p>
                            <h2><span>Twitter</span></h2>
                            <a class="twitter-timeline"  href="https://twitter.com/burnvideo" data-widget-id="609919440587616256">Tweets by @burnvideo</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                            </p>


                            <p>
                            <h2><span>Facebook</span></h2>
                            <!-- <section id="facebook-likebox-2" class="widget widget_facebook_likebox">
                            							<div class="widget-wrap"> -->
							<!-- <h4 class="widget-title widgettitle"><a href="https://www.facebook.com/BurnVideo">Facebook</a></h4> -->
                            <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fburnvideo&tabs=timeline&width=280&height=500&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" width="280" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
							<!-- </div>
							</section> -->
                            </p>


                            <p>
                            <h2><span>Instagram</span></h2>
                            <!-- SnapWidget -->

                            <script src="https://snapwidget.com/js/snapwidget.js"></script>
                            <iframe src="https://snapwidget.com/embed/401761" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:100%; "></iframe>                            
                            </p>












                        </div>

                        <div style="clear:both">








                        </div><!-- #content -->


                    </div><!-- #primary -->

                </div></div>

        </div><!-- #main -->
@stop
