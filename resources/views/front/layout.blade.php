<!DOCTYPE html>
<html>
    <head>

        <!-- Title -->
        <title>Burn Video To Dvd</title>

        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="Burn Video" />
        <meta name="keywords" content="burn,video" />
        <meta name="author" content="burnvideo" />

        <!-- Styles -->
        {{--<link href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>--}}
        {{ Html::style('assets/frontend/fonts/googlefonts.css') }}
        {{ Html::style('assets/frontend/plugins/pace-master/themes/blue/pace-theme-flash.css') }}
        {{ Html::style('assets/frontend/plugins/uniform/css/uniform.default.min.css') }}
        {{ Html::style('assets/frontend/plugins/bootstrap/css/bootstrap.min.css') }}
        {{ Html::style('assets/frontend/plugins/fontawesome/css/font-awesome.css') }}
        {{ Html::style('assets/frontend/plugins/line-icons/simple-line-icons.css') }}
        {{ Html::style('assets/frontend/plugins/waves/waves.min.css') }}
        {{ Html::style('assets/frontend/plugins/switchery/switchery.min.css') }}
        {{ Html::style('assets/frontend/plugins/3d-bold-navigation/css/style.css') }}
        {{ Html::style('assets/frontend/plugins/slidepushmenus/css/component.css') }}
        {{ Html::style('assets/frontend/plugins/dropzone/dropzone.min.css') }}
        {{ Html::style('assets/frontend/plugins/datatables/css/jquery.datatables.min.css') }}

        <!-- Theme Styles -->
        {{ Html::style('assets/frontend/css/modern.min.css') }}
        {{ Html::style('assets/frontend/css/custom.css') }}

        {{ Html::script('assets/frontend/plugins/3d-bold-navigation/js/modernizr.js') }}


        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        {{ Html::script('assets/frontend/js/html5shiv.min.js') }}
        {{ Html::script('assets/frontend/js/respond.min.js') }}
        <![endif]-->

        {{ Html::script('assets/frontend/plugins/jquery/jquery-2.1.3.min.js') }}

    </head>
    <body class="page-header-fixed compact-menu page-horizontal-bar">
        <main class="page-content content-wrap">
            <div class="navbar">
                <div class="navbar-inner container">
                    <div class="logo-box">
                        <a href='javascript:void(0);' onclick='gotoPage("{{ URL::to('/') }}")' class="logo-text">
                            <img class="logo-image" src="burnvideologo.png"/>
                        </a>
                    </div><!-- Logo Box -->

                    <div class="topmenu-outer">
                        <div class="top-menu">
                            <ul class="nav navbar-nav cs-padding-left-20">
                                <!--<li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('how') }}')">How It Works</a></li>-->
                                @if (\Request::is('/'))  
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('/') }}')" class="menu-selected">HOME</a></li>
                                @else
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('/') }}')">HOME</a></li>
                                @endif
                                @if (\Request::is('about'))  
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('about') }}')" class="menu-selected">ABOUT</a></li>
                                @else
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('about') }}')">ABOUT</a></li>
                                @endif
                                @if (\Request::is('faq'))  
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('faq') }}')" class="menu-selected">FAQ</a></li>
                                @else
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('faq') }}')">FAQ</a></li>
                                @endif
                                @if (\Request::is('contact'))  
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('contact') }}')" class="menu-selected">CONTACT</a></li>
                                @else
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('contact') }}')">CONTACT</a></li>
                                @endif
                                @if (\Request::is('blog'))  
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('blog') }}')" class="menu-selected">BLOG</a></li>
                                @else
                                    <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('blog') }}')">BLOG</a></li>
                                @endif
                                
                                @if(!empty($user->email))
                                    @if (\Request::is('order'))  
                                        <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('order') }}')" class="menu-selected">ORDER</a></li>
                                    @else
                                        <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('order') }}')">ORDER</a></li>
                                    @endif
                                @endif
                                <!--<li style="width:140px;background-color: transparent;">
                                    <a id = "applelink" style="text-align:center;padding:30px 0px 10px 10px;background-color: transparent;border:none;" target="_blank" href="https://itunes.apple.com/us/app/burn-video-your-phones-videos/id1040524545?mt=8">
                                        <img style="width:100%;" src="assets/frontend/images/iOS_app.png">
                                    </a>
                                </li>-->
                            </ul><!-- Nav -->
                            <ul class="nav navbar-nav navbar-right">
                                @if(empty($user))
                                    @if (\Request::is('signup'))  
                                        <li><a href="{{ URL::to('signup') }}" class="menu-selected">SIGN UP</a></li>
                                    @else
                                        <li><a href="{{ URL::to('signup') }}">SIGN UP</a></li>
                                    @endif
                                    @if (\Request::is('login'))  
                                        <li><a href="{{ URL::to('login') }}" class="menu-selected">SIGN IN</a></li>
                                    @else
                                        <li><a href="{{ URL::to('login') }}">SIGN IN</a></li>
                                    @endif
                                @else
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic user-menu-class" data-toggle="dropdown">
                                        <span class="user-name">
                                            @if(!empty($user->first_name) || !empty($user->last_name))
                                            {{ !empty($user->first_name) ? $user->first_name : '' }} {{ !empty($user->last_name) ? $user->last_name : '' }}
                                            @else
                                            {{ $user->email }}
                                            @endif
                                            <i class="fa fa-angle-down"></i>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-list" role="menu">
                                        <li role="presentation"><a href="{{ URL::to('settings') }}"><i class="fa fa-sign-out m-r-xs"></i>Settings</a></li>
                                        <li role="presentation">
                                            <a href="{{ URL::to('register-card') }}">
                                                <i class="fa fa-sign-out m-r-xs"></i><?php echo!empty($user->customer_id) ? 'Update' : 'Register' ?> Payment Information
                                            </a>
                                        </li>
                                        <li role="presentation"><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('order-history') }}')"><i class="fa fa-sign-out m-r-xs"></i>Order History</a></li>
                                        <li role="presentation"><a href="javascript:void(0)" onclick="signout()"><i class="fa fa-sign-out m-r-xs"></i>Sign out</a></li>
                                    </ul>
                                </li>
                                @endif
                            </ul><!-- Nav -->
                        </div><!-- Top Menu -->
                    </div>
                    <a href="#menu" id="menuBar"><span></span></a>
                    <div id="menu">
                        <ul>
                            <!--<li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('how') }}')">How It Works</a></li>-->
                            <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('/') }}')">Home</a></li>
                            <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('/about') }}')">About</a></li>
                            <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('faq') }}')">FAQ</a></li>
                            <!--<li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('policies') }}')">Policies</a></li>-->
                            <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('contact') }}')">Contact</a></li>
							<li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('blog') }}')">Blog</a></li>
                            @if(!empty($user->email))
                            <li><a href="javascript:void(0)" onclick="gotoPage('{{ URL::to('order') }}')">Order</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div><!-- Navbar -->

            <div class="page-inner">

                @yield('content')

                <div class="page-footer site-footer">
                    <p>
                        <a href="/policies">PRIVACY POLICY</a>
                        &nbsp;|&nbsp;
                        <a href="/terms">TERMS OF USE</a>
                    </p>                    
                    <span >&copy; 2015. Burn Video, LLC. All rights reserved.</span>

                    <p>
                        <a href="https://www.facebook.com/BurnVideo?fref=ts" target="_blank"><img src="{{ asset('facebook.png') }}"></a>&nbsp;
                        <a href="https://twitter.com/burnvideo" target="_blank"><img src="{{ asset('twitter.png') }}"></a>&nbsp;
                        <a href="https://www.pinterest.com/burnvideo/" target="_blank"><img src="{{ asset('Pinterest.png') }}"></a>&nbsp;
                        <a href="https://instagram.com/burnvideo/" target="_blank"><img src="{{ asset('installgrame.png') }}"></a>&nbsp;
                        <a href="https://www.youtube.com/channel/UCbamYgfO_4EF82cPrpQcaZw" target="_blank"><img src="{{ asset('youtube-icon.png') }}"></a>&nbsp;
                        <a href="https://plus.google.com/110977866961443262021/posts" target="_blank"><img src="{{ asset('google-plus.jpg') }}"></a>
                    </p>

                </div>
            </div><!-- Page Inner -->
        </main><!-- Page Content -->
        <div class="cd-overlay"></div>

        <!-- Modal -->
        <div class="modal fade" id="signoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Are you sure you want to Sign-Out?</h4>
                    </div>
                    <div class="modal-body">
                        All of your uploaded media files will not be saved until you complete your order.
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success pull-left" onclick="doSignout();">Yes, Sign-Out</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">No, I want to complete my order.</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="waitUploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        Please be patient. Do not sign-out or exit this page until all of your media files are uploaded or this could affect your order.
                    </div>
                    <div class="modal-footer">
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>

        <script>
			function calc_selected_media_count() {
				var cnt = 0;
				for (var i = 0; i < 40; i++) {
					var id = '#box' + i;
					if ($(id).val() != '' && $(id).val() != undefined) {
						cnt += 1;
					}
				}
				return cnt;
			}

			function signout() {
				var cnt_files_selected = calc_selected_media_count();
				if (cnt_files_selected > 0) {
					if (g_upload_started) {
						$('#waitUploadModal').modal('show');
					} else {
						$('#signoutModal').modal('show');
					}
					return false;
				}else {
					location.href = '/logout';
				}
			}
			function doSignout() {
				location.href = '/logout';
			}

			function gotoPage(page) {
			//alert(page);
				var cnt_files_selected = calc_selected_media_count();
				if (cnt_files_selected > 0) {
					if (g_upload_status == 'uploading' || g_upload_status == 'paused') {
						$('#waitUploadModal').modal('show');
					} else {
						location.href = page;
					}
					return false;
				} else {
					location.href = page;
				}
			}
        </script>
        <!-- Javascripts -->
        {{ Html::script('assets/frontend/plugins/jquery-ui/jquery-ui.min.js') }}
        {{ Html::script('assets/frontend/plugins/pace-master/pace.min.js') }}
        {{ Html::script('assets/frontend/plugins/jquery-blockui/jquery.blockui.js') }}
        {{ Html::script('assets/frontend/plugins/bootstrap/js/bootstrap.min.js') }}
        {{ Html::script('assets/frontend/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}
        {{ Html::script('assets/frontend/plugins/switchery/switchery.min.js') }}
        {{ Html::script('assets/frontend/plugins/uniform/jquery.uniform.min.js') }}
        {{ Html::script('assets/frontend/plugins/classie/classie.js') }}
        {{ Html::script('assets/frontend/plugins/waves/waves.min.js') }}
        {{ Html::script('assets/frontend/plugins/3d-bold-navigation/js/main.js') }}
        {{ Html::script('assets/frontend/plugins/jquery-validation/jquery.validate.min.js') }}
        {{--{{ Html::script('assets/frontend/plugins/dropzone/dropzone.min.js') }}--}}
        {{ Html::script('assets/frontend/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}
        {{ Html::script('assets/frontend/plugins/datatables/js/jquery.datatables.min.js') }}

        {{ Html::script('assets/frontend/js/bootbox.min.js') }}
        {{ Html::script('assets/frontend/js/modern.min.js') }}
        {{ Html::script('assets/frontend/js/custom.js') }}

        <script>
			$(function () {
				$('[data-toggle="tooltip"]').tooltip()

					$("#zipcode, #friend_zipcode, #self_zipcode").keydown(function (e) {
						// Allow: backspace, delete, tab, escape, enter and .
						if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
							 // Allow: Ctrl+A
							(e.keyCode == 65 && e.ctrlKey === true) ||
							 // Allow: Ctrl+C
							(e.keyCode == 67 && e.ctrlKey === true) ||
							 // Allow: Ctrl+X
							(e.keyCode == 88 && e.ctrlKey === true) ||
							 // Allow: home, end, left, right
							(e.keyCode >= 35 && e.keyCode <= 39)) {
								 // let it happen, don't do anything
								 return;
						}
						// Ensure that it is a number and stop the keypress
						if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
							e.preventDefault();
						}
					});

					})
				var theToggle = document.getElementById('menuBar');
// based on Todd Motto functions
// http://toddmotto.com/labs/reusable-js/

// hasClass
		function hasClass(elem, className) {
			return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
		}
// addClass
		function addClass(elem, className) {
			if (!hasClass(elem, className)) {
				elem.className += ' ' + className;
			}
		}
// removeClass
		function removeClass(elem, className) {
			var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, ' ') + ' ';
			if (hasClass(elem, className)) {
				while (newClass.indexOf(' ' + className + ' ') >= 0) {
					newClass = newClass.replace(' ' + className + ' ', ' ');
				}
				elem.className = newClass.replace(/^\s+|\s+$/g, '');
			}	
		}
// toggleClass
		function toggleClass(elem, className) {
			var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, " ") + ' ';
			if (hasClass(elem, className)) {
				while (newClass.indexOf(" " + className + " ") >= 0) {
					newClass = newClass.replace(" " + className + " ", " ");
				}
				elem.className = newClass.replace(/^\s+|\s+$/g, '');
			} else {
				elem.className += ' ' + className;
			}
		}

		theToggle.onclick = function() {
			toggleClass(this, 'on');
			return false;
		}
        </script>

    </body>
</html>
