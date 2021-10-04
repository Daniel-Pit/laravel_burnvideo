<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Burn Video</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        {{ Html::style('assets/backend/css/bootstrap.min.css') }}
        <!-- font Awesome -->
        {{ Html::style('assets/backend/css/font-awesome.min.css') }}
        <!-- Ionicons -->
        {{ Html::style('assets/backend/css/ionicons.min.css') }}
        <!-- Morris chart -->
        {{ Html::style('assets/backend/css/morris/morris.css') }}   
        <!-- jvectormap -->
        {{ Html::style('assets/backend/css/jvectormap/jquery-jvectormap-1.2.2.css') }}
        <!-- fullCalendar -->
        {{ Html::style('assets/backend/css/fullcalendar/fullcalendar.css') }}

        {{ Html::script('assets/backend/js/bootbox.min.js') }}
        <!-- Daterange picker -->
        {{ Html::style('assets/backend/css/daterangepicker/daterangepicker-bs3.css') }}
        <!-- bootstrap wysihtml5 - text editor -->
        {{ Html::style('assets/backend/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}
        <!-- Theme style -->
        {{ Html::style('assets/backend/css/AdminLTE.css') }}
        <!-- DatePicker-->
        {{ Html::style('assets/backend/css/bootstrap-datetimepicker-master/bootstrap-datetimepicker.css') }}

        <!-- Html5 Shim and Respond.js IE8 support of Html5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            {{ Html::script('assets/backend/js/html5shiv/html5shiv.js') }}
            {{ Html::script('assets/backend/js/respond/respond.min.js') }}
        <![endif]-->
        <style>
            .td_middle {vertical-align: middle !important;}
            .input_header { text-align:left;min-width:150px; }
            .error {text-align: center;}
            #add_error { display: none; }
            #edit_error { display: none; }
            #admin_error { display: none; }
        </style>
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="/" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                Burn Video
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span id="admin_email2">{{ $user->email }} <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img id="admin_image2" src="/uploads/admin/avatar{{$user->id}}" class="img-circle" alt="User Image" />
                                    <p>
                                        <span id="admin_fullname">{{ $user->first_name }} {{ $user->last_name }}</span> - Administrator                                
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="{{ URL::to('admin/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat" onclick="showAdminDialog();">Edit</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img  id="admin_image1" src="/uploads/admin/avatar{{$user->id}}" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hello, <span id="admin_first_name1">{{ $user->first_name }}</span></p>

                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>




                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="/admin/index">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/userList">
                                <i class="fa fa-user"></i> <span>Users</span> 
                            </a>
                        </li>
                        <!--
                        <li>
                            <a href="/admin/mediaList">
                                <i class="fa fa-bar-chart-o"></i> <span>Media</span> 
                            </a>
                        </li>
                        -->
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-check-square-o"></i>
                                <span>Order</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="/admin/orderList"><i class="fa fa-angle-double-right"></i> Order</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-usd"></i> <span>Transaction</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="/admin/transactionList"><i class="fa fa-angle-double-right"></i> Transaction</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bell-o"></i> <span>Push</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/notifyList"><i class="fa fa-angle-double-right"></i> SendByName</a>
                                </li>
                                <li>
                                    <a href="/admin/notifyListById"><i class="fa fa-angle-double-right"></i> SendByID</a>
                                </li>                                
                            </ul>                            
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-envelope"></i> <span>Message</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/messageList"><i class="fa fa-angle-double-right"></i> SendByName</a>
                                </li>
                                <li>
                                    <a href="/admin/messageListById"><i class="fa fa-angle-double-right"></i> SendByID</a>
                                </li>                                
                            </ul>                            
                        </li>
                        <li>
                            <a href="/admin/blog">
                                <i class="fa fa-book"></i> <span>Blog</span>
                            </a>
                        </li>                        
                        <li>
                            <a href="/admin/calendar">
                                <i class="fa fa-calendar"></i> <span>Calendar</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/setting">
                                <i class="fa fa-cog"></i> <span>Setting</span>
                            </a>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-check-square-o"></i>
                                <span>Promo Code</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="/admin/promocode"><i class="fa fa-angle-double-right"></i> PromoCode</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/admin/s3manage">
                                <i class="fa fa-hdd-o"></i> <span>S3-Manage</span>
                            </a>
                        </li> 
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Control panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-4 col-xs-12">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                        {{ $ordercount }}
                                    </h3>
                                    <p>
                                        New Orders
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="/admin/orderList" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->                
                        <div class="col-lg-4 col-xs-12">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        {{$usercount}}
                                    </h3>
                                    <p>
                                        User Registrations
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="/admin/userList" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-4 col-xs-12">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        ${{$earned}}
                                    </h3>
                                    <p>
                                        Earned Price
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="/admin/transactionList" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div><!-- /.row -->

                    <!-- top row -->
                    <div class="row">
                        <div class="col-xs-12 connectedSortable">

                        </div><!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-6 connectedSortable">
                            <!-- Custom tabs (Charts with tabs)-->
                            
                            <div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right">
                                    <li class="pull-left header"><i class="fa fa-shopping-cart"></i> Monthly Sales</li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="monthly-earned-chart" style="position: relative; height: 300px;"></div>
                                </div>
                            </div><!-- /.nav-tabs-custom -->
                            
                            <div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right">
                                    <li class="pull-left header"><i class="fa fa-inbox"></i> Sales</li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
                                </div>
                            </div><!-- /.nav-tabs-custom -->

                            <!-- Calendar -->
                            <div class="box box-warning">
                                <div class="box-header">
                                    <i class="fa fa-check-square-o"></i>
                                    <div class="box-title">New Order</div>

                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <!-- button with a dropdown -->
                                        <div class="btn-group">
                                            <button class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><a href="/admin/orderList">More</a></li>
                                            </ul>
                                        </div>
                                    </div><!-- /. tools -->
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <!--The calendar -->
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Time</th>
                                            <th>Order</th>
                                            <th>User</th>
                                        </tr>
                                        @foreach($orders as $idx=>$item)
                                        <tr>
                                            <td class="td_middle">{{ $item->inserttime }}</td>
                                            <td class="td_middle">{{ $item->ordertag }}</td>
                                            <td class="td_middle">{{ $item->first_name }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->


                        </section><!-- /.Left col -->
                        <!-- right col (We are only adding the ID to make the widgets sortable)-->
                        <section class="col-lg-6 connectedSortable">
                            
                            <div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right">
                                    <li class="pull-left header"><i class="fa fa-edit"></i> Daily Orders</li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="daily-order-chart" style="position: relative; height: 300px;"></div>
                                </div>
                            </div><!-- /.nav-tabs-custom -->
                            
                            <!-- Map box -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <i class="fa fa-briefcase"></i>
                                    <h3 class="box-title">
                                        Top {{ $topcount }} Zip Code
                                    </h3>
                                </div>
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                        <!-- .table - Uses sparkline charts-->
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Code</th>
                                                <th>Dvd's</th>
                                            </tr>
                                            @foreach($topzipcode as $idx=>$item)
                                            <tr>
                                                <td class="td_middle">{{ $item->ordertag }}</td>
                                                <td class="td_middle">{{ $item->dvdcount }}</td>                                            
                                            </tr>
                                            @endforeach
                                        </table><!-- /.table -->
                                    </div>
                                </div><!-- /.box-body-->

                            </div>
                            <!-- /.box -->
                            
                            <!-- Map box -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>         
                                    <h3 class="box-title">
                                        New User
                                    </h3>
                                </div>
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                        <!-- .table - Uses sparkline charts-->
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Name</th>
                                                <th>State</th>
                                                <th>Time</th>
                                            </tr>
                                            @foreach($newusers as $idx=>$item)
                                            <tr>
                                                <td class="td_middle">{{ $item->first_name . " " . $item->last_name }}</td>
                                                <td class="td_middle">{{ $item->state }}</td>
                                                <td class="td_middle">{{ date("m-d-Y", strtotime($item->created_at)) }}</td>                                            
                                            </tr>
                                            @endforeach
                                        </table><!-- /.table -->
                                    </div>
                                </div><!-- /.box-body-->

                            </div>
                            <!-- /.box -->


                        </section><!-- right col -->
                    </div><!-- /.row (main row) -->

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- add new calendar event modal -->


        <!-- jQuery 2.0.2 -->
        {{ Html::script('assets/backend/js/jquery-2.1.4.min.js') }}
        <!-- jQuery UI 1.10.3 -->
        {{ Html::script('assets/backend/js/jquery-ui-1.10.3.min.js') }}
        <!-- Bootstrap -->
        {{ Html::script('assets/backend/js/bootstrap.min.js') }}
        <!-- Morris.js charts -->
        {{ Html::script('assets/backend/js/raphael/raphael-min.js') }}
        {{ Html::script('assets/backend/js/plugins/morris/morris.min.js') }}
        <!-- Sparkline -->
        {{ Html::script('assets/backend/js/plugins/sparkline/jquery.sparkline.min.js') }}
        <!-- jvectormap -->
        {{ Html::script('assets/backend/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}
        {{ Html::script('assets/backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}
        <!-- fullCalendar -->
        {{ Html::script('assets/backend/js/plugins/fullcalendar/fullcalendar.min.js') }}
        <!-- jQuery Knob Chart -->
        {{ Html::script('assets/backend/js/plugins/jqueryKnob/jquery.knob.js') }}
        <!-- daterangepicker -->
        {{ Html::script('assets/backend/js/plugins/daterangepicker/daterangepicker.js') }}

        <!-- DatePicker -->
        {{ Html::script('assets/backend/js/plugins/bootstrap-datetimepicker-master/bootstrap-datetimepicker.js') }}

        <!-- Bootstrap WYSIHtml5 -->
        {{ Html::script('assets/backend/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}

        {{ Html::script('assets/backend/js/notify.min.js') }}

        <!-- iCheck -->
        {{ Html::script('assets/backend/js/plugins/iCheck/icheck.min.js') }}

        <!-- AdminLTE App -->
        {{ Html::script('assets/backend/js/AdminLTE/app.js') }}

        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        {{ Html::script('assets/backend/js/AdminLTE/dashboard.js') }}



        <script>
                    /* Morris.js Charts */
                    // Sales chart

                    var area = new Morris.Bar({
                    element: 'revenue-chart',
                            resize: true,
                            data: [
                            {y: 'PC', val: {{ is_null($earn_pc) ? 0 : $earn_pc }} },
                            {y: 'iOS', val: {{ is_null($earn_ios) ? 0 : $earn_ios }} },
                            {y: 'Android', val: {{ is_null($earn_android) ? 0 : $earn_android }} }
                            ],
                            xkey: 'y',
                            ykeys: ['val'],
                            labels: ['Sales'],
                            hideHover: 'auto',
                            barColors: function (row, series, type) {
                            if (row.label == "PC") return "#a0d0e0";
                                    else if (row.label == "iOS") return "#3c8dbc";
                                    else if (row.label == "Android") return "#f56954";
                            }
                    });
                    
                    var monthly_earned_area = new Morris.Line({
                      element: 'monthly-earned-chart',
                      data: {!!$earn_month_data!!},
                      xkey: 'm',
                      ykeys: ['total', 'iOS', 'pc', 'android'],
                      labels: ['Total', 'iOS', 'PC', 'Android'],
                      lineColors:['#27b363', '#3c8dbc','#a0d0e0', '#f56954']
                    });
                    
                    var daily_order_area = new Morris.Line({
                      element: 'daily-order-chart',
                      data: {!!$order_daily_data!!},
                      xkey: 'date',
                      ykeys: ['total', 'iOS', 'pc', 'android'],
                      labels: ['Total', 'iOS', 'PC', 'Android'],
                      lineColors:['#27b363', '#3c8dbc','#a0d0e0', '#f56954']
                    });
                    
                    //Fix for charts under tabs
                    $('.box ul.nav a').on('shown.bs.tab', function(e) {
                        area.redraw();
                        monthly_earned_area.redraw();
                        daily_order_area.redraw();
                    });
                    function hideAdminError()
                    {
                    $("#admin_error").hide();
                    }

            function showAdminError(message)
            {
            $("#admin_error").text(message);
                $("#admin_error").show();
            }

            function checkAdminParameter(email, password, password2, firstName, lastName)
            {
            if (email.length == 0)
            {
            showAdminError("Please input email address");
                    return false;
            }

            if (password.length == 0)
            {
            showAdminError("Please input password");
                    return false;
            }

            if (password != password2)
            {
            showAdminError("Error! password and retype password is different.");
                    return false;
            }

            if (firstName.length == 0)
            {
            showAdminError("Please input first name");
                    return false;
            }

            if (lastName.length == 0)
            {
            showAdminError("Please input last name");
                    return false;
            }
            return true;
            }


            function showAdminDialog()
            {
            $('#admin_image').val("");
                    $('#edit-admin').modal('show');
            }

            function onSubmitAdmin()
            {
            var email = $('#admin_email').val();
                    var password = $('#admin_password').val();
                    var password2 = $('#admin_password2').val();
                    var firstName = $('#admin_first_name').val();
                    var lastName = $('#admin_last_name').val();
                    var uid = $('#admin_uid').val();
                    var oldpassword = $('#admin_oldpassword').val();
                    if (checkAdminParameter(email, password, password2, firstName, lastName) == false)
            {
            return;
            }

            $("#edit-adminForm").submit();
            }

            $(document).ready(function (e) {

            $("#edit-adminForm").on('submit', (function(e) {
            e.preventDefault();
                    $.ajax({
                    url: "/admin/api_editAdmin",
                            type: "POST",
                            data:  new FormData(this),
                            contentType: false,
                            cache: false,
                            processData:false,
                            success: function(data) {
                            $("#admin_image1").attr("src", "");
                                    $("#admin_image2").attr("src", "");
                                    $("#admin_image1").attr("src", "/uploads/admin/avatar" + data.user.id);
                                    $("#admin_image2").attr("src", "/uploads/admin/avatar" + data.user.id);
                                    $("#admin_first_name1").text(data.user.first_name);
                                    $("#admin_email2").text(data.user.email);
                                    $("#admin_fullname").text(data.user.first_name + " " + data.user.last_name);
                                    $.notify("Success change user", { position: "bottom center", className: 'sucess' });
                                    $("#edit-admin").modal("hide");
                            },
                            error: function() {
                            $.notify("Fail change user", { position: "bottom center", className: 'error' });
                            }
                    });
            }));
            });</script>



        <!-- edit -->
        <div class="modal fade" id="edit-admin" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Edit Administrator</h4>
                    </div>
                    <form id="edit-adminForm" action="/admin/api_editAdmin" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">Email:</span>
                                    <input id="admin_email" name="admin_email" type="email" class="form-control" placeholder="Email" value="{{ $user->email }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">Password:</span>
                                    <input id="admin_password" name="admin_password" type="password" class="form-control" placeholder="Password" value="{{ $user->password }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">Retype Password:</span>
                                    <input id="admin_password2" name="admin_password2" type="password" class="form-control" placeholder="Retype password" value="{{ $user->password }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">First Name:</span>
                                    <input id="admin_first_name" name="admin_first_name" class="form-control" placeholder="First Name" value="{{ $user->first_name }}">                                            
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">Last Name:</span>
                                    <input id="admin_last_name" name="admin_last_name" class="form-control" placeholder="Last Name"  value="{{ $user->last_name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon input_header">Image:</span>
                                    <input id="admin_image" name="admin_image" type="file" class="form-control" placeholder="Select Image">
                                </div>
                            </div>
                            <div class="error">
                                <span id="admin_error" class="text-red">this is error</span>
                            </div>
                            <input type="hidden" id="admin_uid" name="admin_uid" value="{{ $user->id }}"> </input>
                            <input type="hidden" id="admin_oldpassword" name="admin_oldpassword"  value="{{ $user->password }}"> </input>
                        </div>
                        <div class="modal-footer clearfix">
                            <button id="admin_cancel_btn" type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button id="admin_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitAdmin();">OK</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </body>
</html>