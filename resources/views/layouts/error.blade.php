<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Tata Motors Call Tracker" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <title>Tata Motors App</title>
    
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Arimo:400,700,400italic" id="style-resource-1">
    <script src="{{ URL::to('assets/js/jquery-1.11.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::to('assets/js/datatables/dataTables.bootstrap.css') }}" id="style-resource-1">
    <link rel="stylesheet" href="{{ URL::to('assets/css/fonts/linecons/css/linecons.css') }}" id="style-resource-2">
    <link rel="stylesheet" href="{{ URL::to('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}" id="style-resource-3">
    <link rel="stylesheet" href="{{ URL::to('assets/css/bootstrap.css') }}" id="style-resource-4">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-core.css') }}" id="style-resource-5">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-forms.css') }}" id="style-resource-6">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-components.css') }}" id="style-resource-7">
    <link rel="stylesheet" href="{{ URL::to('assets/css/xenon-skins.css') }}" id="style-resource-8">
    <link rel="stylesheet" href="{{ URL::to('assets/css/custom.css') }}" id="style-resource-9">
    <script src="{{ URL::to('assets/js/datatables/js/jquery.dataTables.min.js') }}" id="script-resource-7"></script>
    <script src="{{ URL::to('assets/js/datatables/dataTables.bootstrap.js') }}" id="script-resource-8"></script>
    <script src="{{ URL::to('assets/js/datatables/yadcf/jquery.dataTables.yadcf.js') }}" id="script-resource-9"></script>
    <script src="{{ URL::to('assets/js/datatables/tabletools/dataTables.tableTools.min.js') }}" id="script-resource-10"></script>
    <script src="{{ URL::to('assets/js/jquery-validate/jquery.validate.min.js') }}" id="script-resource-7"></script>
    <script src="{{ URL::to('assets/js/jquery-ui/jquery-ui.min.js') }}" id="script-resource-13"></script>
    <script src="{{ URL::to('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}" id="script-resource-14"></script>
    <script src="{{ URL::to('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}" id="script-resource-15"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]> <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script> <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script> <![endif]-->

</head>

<body class="page-body">
    

    <div class="page-container">
        <div class="main-content">

            @yield('content')

            <footer class="main-footer sticky footer-type-1">
                <div class="footer-inner">
                    <div class="footer-text">
                        &copy; 2017
                        <a href="http://waybeo.com/" target="_blank"><strong>Waybeo</strong></a></div>
                    <div class="go-up"> <a href="#" rel="go-top"> <i class="fa-angle-up"></i> </a></div>
                </div>
            </footer>
        </div>
    </div>
   
    <!-- Scripts -->
    <script src="{{ URL::to('assets/js/bootstrap.min.js') }}" id="script-resource-1"></script>
    <script src="{{ URL::to('assets/js/TweenMax.min.js') }}" id="script-resource-2"></script>
    <script src="{{ URL::to('assets/js/resizeable.js') }}" id="script-resource-3"></script>
    <script src="{{ URL::to('assets/js/joinable.js') }}" id="script-resource-4"></script>
    <script src="{{ URL::to('assets/js/xenon-api.js') }}" id="script-resource-5"></script>
    <script src="{{ URL::to('assets/js/xenon-toggles.js') }}" id="script-resource-6"></script>
    <script src="{{ URL::to('assets/js/xenon-custom.js') }}" id="script-resource-7"></script>
    
</body>


</html>