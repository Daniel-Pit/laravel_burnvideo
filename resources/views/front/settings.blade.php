@extends('front.layout')
@section('content')
    <div id="main-wrapper" class="container">
        <div class="row">
            <div class="col-md-8" style="margin-left: 200px">
                <div class="panel panel-white">
                    <div class="panel-heading clearfix">
                        <h4 class="panel-title">Settings</h4>
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

                        {{ Form::open( array('class' => 'form-horizontal')) }}
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-2 control-label">Password</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first_name" class="col-sm-2 control-label">First Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" value="{{ $user->first_name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="col-sm-2 control-label">Last Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" value="{{ $user->last_name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="street" class="col-sm-2 control-label">Street</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="street" id="street" placeholder="Street" value="{{ $user->street }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city" class="col-sm-2 control-label">City</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ $user->city }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state" class="col-sm-2 control-label">State</label>

                            <div class="col-sm-4">
                                <select class="form-control m-b-sm" name="state" id="state">
                                    @foreach($states as $state)
                                        <option <?php echo $state == $user->state ? 'selected' : '' ?>>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="zipcode" class="col-sm-2 control-label">Zip</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="Zip" value="{{ $user->zipcode }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success">Update</button>
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