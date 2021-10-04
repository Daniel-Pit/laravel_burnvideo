<!DOCTYPE html>
<html class="bg-black">
<head>
    <meta charset="UTF-8">
    <title>Burn Video</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    {{ Html::style('assets/backend/css/bootstrap.min.css') }}
    <!-- font Awesome -->
    {{ Html::style('assets/backend/css/font-awesome.min.css') }}
    <!-- Theme style -->
    {{ Html::style('assets/backend/css/AdminLTE.css') }}

    <!-- Html5 Shim and Respond.js IE8 support of Html5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    {{ Html::script('assets/backend/js/html5shiv/html5shiv.js') }}
	{{ Html::script('assets/backend/js/respond/respond.min.js') }}
    <![endif]-->
</head>
<body class="bg-black">

<div class="form-box" id="login-box">
    <div class="header">Sign In</div>

    {{ Form::open( array('to' => 'login')) }}
        <div class="body bg-gray">
            <div class="form-group">
                <input type="text" name="email" class="form-control" placeholder="User ID"/>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password"/>
            </div>
            <div class="form-group">
                <input type="checkbox" name="rememberMe"/> Remember me
            </div>
        </div>
        <div class="footer">
            <button type="submit" class="btn bg-olive btn-block">Sign me in</button>

            <!--
            <p><a href="{{ URL::to('admin/forgot') }}">I forgot my password</a></p>
            -->
        </div>
    {{Form::close()}}
    
    @if( count($errors) > 0 )
        <div class="alert alert-error alert-danger" style="z-index: 50; position: fixed; margin-top:20px">
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

</div>


<!-- jQuery 2.0.2 -->
{{ Html::script('assets/backend/js/jquery-2.1.4.min.js') }}
<!-- Bootstrap -->
{{ Html::script('assets/backend/js/bootstrap.min.js') }}

</body>
</html>