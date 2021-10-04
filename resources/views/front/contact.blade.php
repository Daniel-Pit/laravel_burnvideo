@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="entry-content">
        <div class="container ">
            <div class="panel panel-white">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="contact">
                            <h1>CONTACT US</h1>
                            <p>Feel free to email us directly at <a href="mailto:info@burnvideo.net">info@burnvideo.net</a> or
                            </br>contact us by filling out the form below.</p>
                            <p>We will get back to you within two working days.<br />
                            
                            <div class="panel-body">
            
                                @if (Session::has('success') )
                                <div class="span6 alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                                @endif
                                @if( count($errors) > 0 )
                                <div class="alert alert-error alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <h4>Error!</h4>
            
                                    <p>The following errors have occurred:</p>
                                    <ul id="form-errors">
                                        @foreach ($errors->all('<li>:message</li>') as $error)
                                        {!! $error !!}
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
            
                                {{ Form::open( array('class' => 'form-horizontal')) }}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <input type="email" name="email" id="email" value="{{ !empty($user->email) ? $user->email : '' }}" size="50" class="form-control" placeholder="Your Email (required)"/>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <input type="text" name="name" id="name" value="{{ !empty($user->first_name) ? $user->first_name : '' }} {{ !empty($user->last_name) ? $user->last_name : '' }}" size="50" class="form-control" placeholder="Your Name (required)"/>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="text" name="subject" id="subject" value="" size="100" class="form-control" placeholder="Subject"/>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <textarea name="message" id="message" cols="100" rows="8" class="form-control" placeholder="Your Message"></textarea>
                                        </div>

                                    </div>
                                </div>
            
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="cs-text-left">Please verify that you are human</p>
                                            @include('partials.captcha')
                                            <div class="send-btn-class">
                                                <input type="submit" value="Send" class="btn btn-success button-bottom"/>
                                            </div>
                                        </div>

                                    </div>
                                </div>
            
                                {{Form::close()}}
                            </div>
                            
                            
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>


            </div>
        </div>
    </div>
</div><!-- Main Wrapper -->
@stop