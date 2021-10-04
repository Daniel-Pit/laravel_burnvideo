@extends('front.layout')
@section('content')
    <div id="main-wrapper" class="container">
        <div class="row">
            <div class="col-md-6" style="margin-left: 300px">
                <div class="panel panel-white">
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title" style="text-align: center;width: 100%;">Enter your e-mail address below to reset your password.</h4>
                    </div>
                    <div class="panel-body">

                        @if (Session::has('success') )
                            <div class="span6 alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif
                        @if(count($errors) > 0)
                            <div class="alert alert-error alert-danger" style="text-align: center;width: 100%;">
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

                        {{ Form::open( array('to' => 'forgot', 'class' => 'form-horizontal')) }}
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 text-left">
                                <a class="btn btn-warning" href="{{ URL::to('login') }}">Back to Log In</a>
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Row -->
    </div><!-- Main Wrapper -->
@stop