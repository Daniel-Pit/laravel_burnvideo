@extends('front.layout')
@section('content')
    <div id="main-wrapper" class="container">
        <div class="row">
            <div class="col-md-6" style="margin-left: 300px">
                <div class="panel panel-white">
                    <div class="panel-heading clearfix">
                        <h3 class="panel-title"><?php echo !empty($user->customer_id) ? 'Change Payment Information' : 'Register Payment Information' ?>
                            (Paypal or Credit card)
                        </h3>
                    </div>
                    <div class="panel-body">

                        @if (Session::has('success') )
                            <div class="span6 alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif
                        @if(count($errors) > 0)
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

                        {{ Form::open( array('to' => 'register-card', 'class' => 'form-horizontal')) }}
                            <div id="payment-form"></div>
                            <br>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success pull-right">
                                    <?php echo !empty($user->customer_id) ? 'Change' : 'Register' ?>
                                </button>
                            </div>
                        {{Form::close()}}

                        <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
                        <script>
                            var clientToken = "{{ $clientToken }}";

                            braintree.setup(clientToken, "dropin", {
                                container: "payment-form"
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row -->
    </div><!-- Main Wrapper -->
@stop