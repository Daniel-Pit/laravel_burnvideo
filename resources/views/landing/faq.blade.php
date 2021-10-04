@extends('landing.layout')
@section('content')
<div id="main" class="site-main">

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">


            <article id="post-8" class="post-8 page type-page status-publish hentry">
                <header class="entry-header">

                    <h1 class="entry-title">FAQ's</h1>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <div class="container ">
                        <div class="faq">
                            <h1>FAQ</h1>
                            <div class="row ">

                                {!! $faq !!}

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
