<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Burn Video</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- bootstrap 3.0.2 -->
        {{ Html::style('assets/backend/css/datatables/dataTables.bootstrap.css') }}
        {{ Html::style('assets/backend/css/bootstrap.min.css') }}
        <!-- jvectormap -->
        {{ Html::style('assets/backend/css/jvectormap/jquery-jvectormap-1.2.2.css') }}
        <!-- fullCalendar -->
        {{ Html::style('assets/backend/css/fullcalendar/fullcalendar.css') }}
        <!-- Daterange picker -->
        {{ Html::style('assets/backend/css/daterangepicker/daterangepicker-bs3.css') }}
        <!-- bootstrap wysihtml5 - text editor -->
        {{ Html::style('assets/backend/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}
        <!-- font Awesome -->
        {{ Html::style('assets/backend/css/font-awesome.min.css') }}
        <!-- Ionicons -->
        {{ Html::style('assets/backend/css/ionicons.min.css') }}
        <!-- Theme style -->
        {{ Html::style('assets/backend/css/AdminLTE.css') }}
        <!-- DatePicker-->
        {{ Html::style('assets/backend/css/bootstrap-datetimepicker-master/bootstrap-datetimepicker.css') }}
        {{ Html::style('assets/backend/css/summernote/summernote.css') }}
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

        <!-- jQuery 2.0.2 -->
        {{ Html::script('assets/backend/js/jquery-2.1.4.min.js') }}
        
        <!-- Datepicker Jquery -->
        {{ Html::script('assets/backend/js/plugins/bootstrap-datetimepicker-master/jquery/jquery-1.8.3.min.js') }}
        
        <!-- jQuery UI 1.10.3 -->
        {{ Html::script('assets/backend/js/jquery-ui-1.10.3.min.js') }}
        <!-- Bootstrap -->
        {{ Html::script('assets/backend/js/bootstrap.min.js') }}
        {{ Html::script('assets/backend/js/bootbox.min.js') }}

        <!-- fullCalendar -->
        {{ Html::script('assets/backend/js/plugins/fullcalendar/fullcalendar.min.js') }}

        <!-- DatePicker -->
        {{ Html::script('assets/backend/js/plugins/bootstrap-datetimepicker-master/bootstrap-datetimepicker.js') }}

        <!-- AdminLTE App -->
        {{ Html::script('assets/backend/js/AdminLTE/app.js') }}

        {{ Html::script('assets/backend/js/plugins/datatables/jquery.dataTables.js') }}
        {{ Html::script('assets/backend/js/plugins/datatables/dataTables.bootstrap.js') }}

        {{ Html::script('assets/backend/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}

        {{ Html::script('assets/backend/js/notify.min.js') }}
		{{ Html::script('assets/backend/js/summernote.min.js') }}

		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->
		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css"> -->
		<!-- <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script> -->
		<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> -->

		<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />

	  
        <script>
            $(document).ready(function () {
                $('#objectTable').dataTable({
                    "pagingType": "full_numbers"
                });
            });

            function onClickAdd()
            {
                hideError();
                $('#add-modal').modal('show');
            }

            function onClickDelete(id)
            {
                bootbox.confirm("Are you sure?", function (result) {
                    if (result)
                    {
                        onSubmitDelete(id);
                    }
                });
            }

            function hideError()
            {
                $("#add_error").hide();
                $("#edit_error").hide();
            }

            function showError(message, isAdd)
            {
                if (isAdd)
                {
                    $("#add_error").text(message);
                    $("#add_error").show();
                }
                else
                {
                    $("#edit_error").text(message);
                    $("#edit_error").show();
                }
            }




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

                $("#edit-adminForm").on('submit', (function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: "/admin/api_editAdmin",
                        type: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (data) {
                            $("#admin_image1").attr("src", "");
                            $("#admin_image2").attr("src", "");

                            $("#admin_image1").attr("src", "/uploads/admin/avatar" + data.user.id);
                            $("#admin_image2").attr("src", "/uploads/admin/avatar" + data.user.id);
                            $("#admin_first_name1").text(data.user.first_name);
                            $("#admin_email2").text(data.user.email);
                            $("#admin_fullname").text(data.user.first_name + " " + data.user.last_name);
                            $.notify("Success change user", {position: "bottom center", className: 'sucess'});
                            $("#edit-admin").modal("hide");
                        },
                        error: function () {
                            $.notify("Fail change user", {position: "bottom center", className: 'error'});
                        }
                    });
                }));
            });
            
            $(function () {
        $('.form_datetime').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1
        });
        $('.form_date').datetimepicker({
            //language: 'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
        $('.form_time').datetimepicker({
            //language: 'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0
        });
    })

        </script>
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
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li>
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
                        <li class="active">
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
                                <li>
                                    <a href="/admin/transactionList"><i class="fa fa-angle-double-right"></i> Transaction</a>
                                </li>
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
                                <li>
                                    <a href="/admin/messageListByIds"><i class="fa fa-angle-double-right"></i> SendByIDs</a>
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

                @yield('content')               

            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->



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