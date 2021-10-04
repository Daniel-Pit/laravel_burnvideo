@extends('landing.layout')
@section('content')
<div id="main" class="site-main">

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">


            <article id="post-204" class="post-204 page type-page status-publish hentry">
                <header class="entry-header">

                    <h1 class="entry-title">Policies</h1>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <div class="container ">
                        <div class="faq">
                            <div class="row ">
                                {!! $policies !!}
                            </div>
                        </div>
                    </div>
                </div><!-- .entry-content -->

                <footer class="entry-meta">
                </footer><!-- .entry-meta -->
            </article><!-- #post -->


        </div><!-- #content -->
    </div><!-- #primary -->


</div><!-- #main -->
@stop
