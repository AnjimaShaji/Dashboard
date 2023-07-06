<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="SOTC Call Tracker" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <title>PUREIT App</title>

    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Arimo:400,700,400italic" id="style-resource-1">
    <script src="{{ URL::to('assets/js/jquery-1.11.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::to('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/js/datatables/dataTables.bootstrap.css') }}"
        id="style-resource-1">
    <link rel="stylesheet" href="{{ URL::to('assets/css/fonts/linecons/css/linecons.css') }}" id="style-resource-2">
    <link rel="stylesheet" href="{{ URL::to('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}"
        id="style-resource-3">
    <link rel="stylesheet" href="{{ URL::to('assets/css/bootstrap.css') }}" id="style-resource-4">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-core.css') }}" id="style-resource-5">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-forms.css') }}" id="style-resource-6">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-components.css') }}" id="style-resource-7">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-skins.css') }}" id="style-resource-8">
    <link rel="stylesheet" href="{{ URL::to('assets/css/custom.css') }}" id="style-resource-9">
    <script src="{{ URL::to('assets/js/datatables/js/jquery.dataTables.min.js') }}" id="script-resource-7"></script>
    <script src="{{ URL::to('assets/js/datatables/dataTables.bootstrap.js') }}" id="script-resource-8"></script>
    <script src="{{ URL::to('assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}" id="script-resource-9"></script>
    <script src="{{ URL::to('assets/js/datatables/tabletools/dataTables.tableTools.min.js') }}" id="script-resource-10">
    </script>
    <script src="{{ URL::to('assets/js/jquery-validate/jquery.validate.min.js') }}" id="script-resource-7"></script>
    <script src="{{ URL::to('assets/js/jquery-ui/jquery-ui.min.js') }}" id="script-resource-13"></script>
    <script src="{{ URL::to('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}" id="script-resource-14"></script>
    <script src="{{ URL::to('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}" id="script-resource-15"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]> <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script> ___scripts_10___ <![endif]-->
    <!-- Scripts -->
    <script src="{{ URL::to('assets/js/moment.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::to('assets/js/bootstrap.min.js') }}" id="script-resource-1"></script>
    <script src="{{ URL::to('assets/js/TweenMax.min.js') }}" id="script-resource-2"></script>
    <script src="{{ URL::to('assets/js/resizeable.js') }}" id="script-resource-3"></script>
    <script src="{{ URL::to('assets/js/joinable.js') }}" id="script-resource-4"></script>
    <script src="{{ URL::to('assets/js/xenon-api.js') }}" id="script-resource-5"></script>
    <script src="{{ URL::to('assets/js/xenon-toggles.js') }}" id="script-resource-6"></script>
    <script src="{{ URL::to('assets/js/xenon-custom.js') }}" id="script-resource-7"></script>
</head>

<body class="page-body">
    <!--MODAL SECTION-->
    <div id="modalAjax" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 class="modal-title" align="center" style="color:darkgrey"></h4>
                </div>

                <div class="modal-body">

                    Content is loading...

                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-white" type="button">Close</button>
                    <button class="btn btn-info" type="button">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL SECTION ENDS-->
    <!-- <div style="color:blue;text-align: center;" class="navbar-fixed-top"><i><b>-- CONFIDENTIAL --</b></i></div> -->
    <nav class="navbar horizontal-menu navbar-fixed-top">
        <div class="navbar-inner">
            <div class="navbar-brand">
                @if (in_array(Auth::user()->role, ['ADMIN', 'ASM']))
                    <a href="{{ url('admin/reports') }}" class="logo">
                @endif
                <img src="{{ URL::to('assets/images/pureit.jpg') }}" width="100" alt="" class="hidden-xs" />
                <img src="{{ URL::to('assets/images/pureit.jpg') }}" width="100" alt="" class="visible-xs" />
                </a>
            </div>

            <!-- main menu -->
            <ul class="navbar-nav">
                @if (in_array(Auth::user()->role, ['ADMIN', 'STORE', 'RM', 'ASM']))
                    @if (in_array(Auth::user()->role, ['ADMIN']))
                    <li class="{{ (Request::segment(2) == 'dashboard'? 'active':'')}}"> 
                        <a href="{{ url(strtolower(Auth::user()->role).'/dashboard') }}">
                            <i class="fa-line-chart"></i>
                            <span class="title">Dashboard</span>
                        </a>
                    </li>
                    @endif
                    @if (!in_array(Auth::user()->role, ['STORE']))
                        <li class="{{ Request::segment(2) == 'store' ? 'active' : '' }}">
                            <a href="{{ url(strtolower(Auth::user()->role) . '/store') }}">
                                <i class="linecons-shop"></i>
                                <span class="title">Store</span>
                            </a>
                        </li>
                    @endif
                    <li class="{{ Request::segment(2) == 'reports' ? 'active' : '' }}">
                        <a href="{{ url(strtolower(Auth::user()->role) . '/reports') }}">
                            <i class="linecons-note"></i>
                            <span class="title">Reports</span>
                        </a>
                    </li>
                    @if (in_array(Auth::user()->role, ['ADMIN']))
                    <li class="{{ Request::segment(2) == 'zero-call-stores' ? 'active' : '' }}">
                        <a href="{{ url(strtolower(Auth::user()->role) . '/zero-call-stores') }}">
                            <i class="linecons-note"></i>
                            <span class="title">Zero Call Stores</span>
                        </a>
                    </li>
                    @endif
                @endif
            </ul>
            <ul class="nav nav-userinfo navbar-right">
                @if (Auth::guest())
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="dropdown user-profile">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <img src="{{ URL::to('assets/images/user.png') }}" alt="user-image"
                                class="img-circle img-inline userpic-32" width="28" />
                            @if (Auth::user()->role === 'DEALER')
                                Welcome
                            @endif
                            {{ Auth::user()->name }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu user-profile-menu list-unstyled">
                            <!-- <li> <a href="#settings"> <i class="fa-wrench"></i>Settings</a> </li> -->
                            <!-- <li>
                                <a href="/change-password">
                                    <i class="fa-key"></i>Change Password
                                </a>
                            </li> -->
                            <li class="last">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); 
                                document.getElementById('logout-form').submit();">
                                    <i class="fa-lock"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
    @yield('modals')
    <div class="modal" id="passwordModal" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Login Credentials</h4>
                </div>
                <div class="modal-body" id="passwordBody">
                    <!-- email & password -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="closeModal" class="btn btn-danger" data-dismiss="modal"
                        style="width: 55px;">No</button>
                    <button type="button" id="nextModal" class="btn btn-success" style="width: 55px;">Yes</button>
                    <button type="button" id="cancelModal" class="btn btn-info" style="width: 55px;display: none"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="page-container">
        <div class="main-content">

            @yield('content')

            <footer class="main-footer sticky footer-type-1">
                <div class="footer-inner">
                    <div class="footer-text">
                        &copy; {{ date('Y') }}
                        <a href="http://waybeo.com/" target="_blank"><strong>Waybeo</strong></a>
                    </div>
                    <div class="go-up"> <a href="#" rel="go-top"> <i class="fa-angle-up"></i> </a></div>
                </div>
            </footer>
        </div>
    </div>

    <!-- @todo remove this and add these lines from dealer creation balde to 'modals' section as done in reports page -->
    <div class="modal fade" id="login_details" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Login Credentials</h4>
                </div>
                <div class="modal-body" id="login_info">
                    <!-- email & password -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="finish" class="btn btn-info" data-dismiss="modal">Finish</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add models from respective view pages -->
    @yield('modals')
    
</body>

</html>
