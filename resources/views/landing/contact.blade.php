@extends('landing.layout')
@section('content')
<div id="main" class="site-main">

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">


            <article id="post-11" class="post-11 page type-page status-publish hentry">
                <header class="entry-header">
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <div class="container ">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="contact">
                                    <h1>CONTACT US</h1>
                                    <p>Feel free to email us directly at <a href="mailto:info@burnvideo.net">info@burnvideo.net</a> or
                                    </br>contact us by filling out the form below.</p>
                                    <p>We will get back to you within two working days.<br />
                                    <div class="wpcf7" id="wpcf7-f25-p11-o1" dir="ltr">
                                        <div class="screen-reader-response"></div>
        
                                        @if (Session::has('success') )
                                        <div class="span6 alert alert-success">
                                            {{ Session::get('success') }}
                                        </div>
                                        @endif
                                        @if( count($errors) > 0 )
                                        <div class="alert alert-error alert-danger">
                                            <h4>Error!</h4>
        
                                            <p>The following errors have occurred:</p>
                                            <ul id="form-errors">
                                                @foreach ($errors->all('<li>:message</li>') as $error)
                                                {!! $error !!}
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
        
                                        {{ Form::open( array('class' => 'wpcf7-form')) }}
                                        
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 wpcf7-form-control-wrap your-email">
                                                <input type="email" name="email" value="" size="50" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false" placeholder="Your Email (required)"/>
                                                </div>
                                                
                                                <div class="col-md-6 wpcf7-form-control-wrap your-name">
                                                    <input type="text" name="name" value="" size="50" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Your Name (required)"/>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 wpcf7-form-control-wrap your-subject">
                                                    <input type="text" name="subject" value="" size="100" class="wpcf7-form-control wpcf7-text" aria-invalid="false" placeholder="Subject"/>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12 wpcf7-form-control-wrap your-message">
                                                    <textarea name="message" cols="100" rows="8" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false" placeholder="Your Message"></textarea>
                                                </div>

                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <p class="cs-text-left">Please verify that you are human</p>
                                                    @include('partials.captcha')
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="submit" value="Send" class="button-bottom"/>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        {{Form::close()}}
                                    </div>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 cs-padding-tb-40">
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
