<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Dashboard by Waybeo - Tata Motors</title>
		
		<!-- Global stylesheets -->
		<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
		<!-- <link href="/global_assets/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css"> -->
		<!-- <link href="/global_assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css"> -->
		{{-- <link rel="stylesheet" href="{{ URL::to('assets/css/bootstrap.css') }}" id="style-resource-4"> --}}
		<link href="/outbound_assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="/outbound_assets/css/bootstrap_limitless.min.css" rel="stylesheet" type="text/css">
		<link href="/outbound_assets/css/layout.min.css" rel="stylesheet" type="text/css">
		<link href="/outbound_assets/css/components.min.css" rel="stylesheet" type="text/css">
		<link href="/outbound_assets/css/colors.min.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="{{ URL::to('assets/css/xenon-core.css') }}" id="style-resource-5">
		<link rel="stylesheet" href="{{ URL::to('assets/css/fonts/linecons/css/linecons.css') }}" id="style-resource-2">
	    <link rel="stylesheet" href="{{ URL::to('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}" id="style-resource-3">
		<!-- /global stylesheets -->
		
		<!-- Core JS files -->
		<script src="/global_assets/js/main/jquery.min.js"></script>
		<script src="/global_assets/js/main/bootstrap.bundle.min.js"></script>
		
		<!-- /core JS files -->
		
		<!-- Theme JS files -->
		<script src="/global_assets/js/plugins/visualization/d3/d3.min.js"></script>
		<script src="/global_assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
		
		
		<script src="/outbound_assets/js/app.js"></script>
		<script src="/global_assets/js/demo_charts/pages/dashboard/light/bars.js"></script>
		<script src="/global_assets/js/demo_charts/d3/bars/bars_basic_vertical.js?v=1"></script>
		<script src="/global_assets/js/demo_charts/d3/bars/bars_basic_vertical_right.js?v=1"></script>
		
		
		
		<style>
			/* width */
			::-webkit-scrollbar {
			width: 5px;
			}
			
			<!-- /* Track */ -->
			<!-- ::-webkit-scrollbar-track { -->
			<!-- background: #ddd;  -->
			<!-- } -->
			
			/* Handle */
			::-webkit-scrollbar-thumb {
			background: #ddd; 
			}
			
			/* Handle on hover */
			::-webkit-scrollbar-thumb:hover {
			background: #999; 
			}
		
			</style>
		
	</head>
	
	<body>
		@auth
			<input id="role" type="hidden" value="{{url('/'.strtolower(Auth::user()->role))}}">
		@endauth
		<!-- Page content -->
		<div class="page-content pt-0">
			
			<!-- Main content -->
			<div class="content-wrapper">
				
				<!-- Content area -->
				<div class="content">
					@yield('content')
					<!-- Footer -->
					<div class="navbar navbar-expand-lg navbar-light" style="position: fixed; bottom: 0; width: 100%">
						<div class="text-center d-lg-none w-100">
							<button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
								<i class="icon-unfold mr-2"></i>
								Footer
							</button>
						</div>
						
						<div class="navbar-collapse collapse" id="navbar-footer">
							<span class="navbar-text">
								&copy; 2021. <a href="www.waybeo.com">Waybeo Technology Solutions Pvt Ltd.</a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
