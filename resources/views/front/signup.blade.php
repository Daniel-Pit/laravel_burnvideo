@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div class="panel panel-white">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="panel-title cs-page-title">SIGN UP</h3>
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

                    {{ Form::open( array('to' => 'signup', 'class' => 'form-horizontal')) }}
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>

                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ Input::old('email') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_email" class="col-sm-3 control-label">Confirm Email</label>

                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="confirm_email" id="confirm_email" placeholder="Confirm Email" value="{{ Input::old('confirm_email') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>

                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="{{ Input::old('password') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>

                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" value="{{ Input::old('confirm_password') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="first_name" class="col-sm-3 control-label">First Name</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" value="{{ Input::old('first_name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="col-sm-3 control-label">Last Name</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" value="{{ Input::old('last_name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="street" class="col-sm-3 control-label">Street</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="street" id="street" placeholder="Street" value="{{ Input::old('street') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-3 control-label">City</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ Input::old('city') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="state" class="col-sm-3 control-label">State</label>

                        <div class="col-sm-4">
                            <select class="form-control m-b-sm" name="state" id="state">
                                <option value> Please select state</option>
                                @foreach($states as $state)
                                <option <?php echo $state == Input::old('state') ? 'selected' : '' ?>>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zipcode" class="col-sm-3 control-label">Zip</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="Zip" value="{{ Input::old('zipcode') }}" maxlength='5'>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-9">
                            <p>Please verify that you are human </p>
                            @include('partials.captcha')
                        </div>

                    </div>

                    <div class="form-group" style="display:none;">
                        <div class="col-sm-offset-2 col-sm-9">
                            <label>
                                <input type="checkbox" name="agree" id="agree" checked="checked"> I have read and accept Policies and Terms of Use
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-9">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn btn-success btn-green">Sign up</button>
                        </div>
                    </div>

                    {{Form::close()}}
                  
 
                </div>
            </div>
        </div>
    </div>
    <!-- Row -->
</div><!-- Main Wrapper -->
<span id="siteseal" style="float: right">
    <script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=6HMczIXqJnLRD7WnFvmXziD1rLvgx8VW5lcercqeOLNtcavKMWRTZkTa55ud">
    </script>
</span>
@stop
