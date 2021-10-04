@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="panel panel-white">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="panel-title cs-page-title">SIGN IN</h3>
                    </div>
                </div>
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

                    {{ Form::open( array('to' => 'login', 'class' => 'form-horizontal')) }}
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-10">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">Password</label>

                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <a href="{{ URL::to('forgot') }}">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <p>Please verify that you are human</p>
                            @include('partials.captcha')
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn btn-success btn-green">Sign In</button>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
    <!-- Row -->
</div><!-- Main Wrapper -->
<span id="siteseal" style="float: right"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=6HMczIXqJnLRD7WnFvmXziD1rLvgx8VW5lcercqeOLNtcavKMWRTZkTa55ud"></script></span>
@stop