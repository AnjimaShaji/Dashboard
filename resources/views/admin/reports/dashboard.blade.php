@extends('layouts.master')

    @section('content')
    	<style type="text/css">
    		.aggregates > .popover {
    			top: 85 !important;
    		}
    		.selected-widget {
			    margin-bottom: 0px !important;
			    height: 95px;
    		}
    	</style>
    	<div class="page-title">
	        <div class="title-env">
	            <h1 class="title">Dashboard</h1>
	        </div>
	    </div>
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<div class="col-sm-12">
						<div id="daterange-custom">
							<i class="fa-calendar"></i>
							<span id="date-select"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
    	<div class="row">
    		<div class="col-sm-6">
    			<div class="col-sm-12 panel panel-default">
    				<div class="panel-heading">
						Month wise Call status
					</div>
					<div  class="table-responsive" data-pattern="priority-columns" data-focus-btn-icon="fa-asterisk" data-sticky-table-header="false" data-add-display-all-btn="true" data-add-focus-btn="false">
		                <table id="month-wise-status-table" cellspacing="0" class="table table-small-font table-bordered table-striped cl-table">
		                    <thead>
		                        <tr style="text-align: center;">
		                            <th data-priority="1">Month</th>
		                            <th data-priority="1">Connected Calls</th>
		                            <th data-priority="1">Missed Calls</th>
		                            <th data-priority="1">IVR Drop Calls</th>
		                            <th data-priority="1">Offline Calls</th>
		                        </tr>
		                    </thead>
		                    <tbody id="month-wise-status">
		                    </tbody>
		                </table>
		            </div>
    			</div>
    			<div class="col-sm-12 panel panel-default">
    				<div class="panel-heading">
						Month wise number of calls
					</div>
					<div id="chart">
						
					</div>
    			</div>
    		</div>
			<div class="col-sm-6">
			
				<div class="panel panel-default">
					<div class="panel-heading">
						Missed Call - Stores
						<div  class="pull-right">
							<label style="color: grey;padding-right:10px;font-style:italic;font-size:0.8em">Total Missed </label>
							<span style="margin-right: 35px;color: green;font-weight: 600;" id="ans_total_calls">__</span>
						</div>
					</div>
					
					<div class="panel-body">
						<div class="row" style="max-height: 310px;overflow-y: scroll;display: block;">
							<table class="table table-condensed">
								<thead>
									<tr>
										<th>Store Code</th>
										<th>Location</th>
										<th style="text-align: right; min-width: 101px">Missed / Total</th>
									</tr>
								</thead>
								
								<tbody id="missed_call_users">
									<tr>
										<td>__</td>
										<td>__</td>
										<td style="text-align: right">
											<span style="font-weight: 600;font-size: 1.2em;color: green;">__&nbsp;</span>
											<span style="font-size: 1.2em;">/&nbsp;__</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- <div class="row">
							<div class="col-md-9" style="max-height: 310px;overflow-y: scroll;display: block;">
								<ul class="list-group list-group-minimal" id="missed_call_users">
									<li class="list-group-item">
										<span style="float: right;font-size: 1.2em;">/&nbsp;__</span>
										<span style="float: right;font-weight: 600;font-size: 1.2em;color: green;">__&nbsp;</span>
										__
									</li>
								</ul>
							</div>
							<div class="col-md-3">
								<div class="btn-group">
									<button type="button" class="btn btn-blue get-missed-stats get-missed-stats-main">SALES</button>
									<button type="button" class="btn btn-blue dropdown-toggle" data-toggle="dropdown">
										<i class="caret"></i>
									</button>
									
									<ul class="dropdown-menu" role="menu">
										<li>
											<a href="javascript:{}" class="get-missed-stats">SALES</a>
										</li>
										<li class="divider"></li>
										<li>
											<a href="javascript:{}" class="get-missed-stats">SERVICE</a>
										</li>
										<li class="divider"></li>
										<li>
											<a href="javascript:{}" class="get-missed-stats">OTHER</a>
										</li>
									</ul>
								</div>
							</div>
						</div> -->
					</div>
				</div>
			</div>
			
		</div>

		<!-- popover template -->
		<div  style="display: none;">
			<div id="missed_call_item">
				<tr>
					<td>Store Code</td>
					<td>Location</td>
					<td style="text-align: right">
						<span style="font-weight: 600;font-size: 1.2em;color: green;">MISSED&nbsp;</span>
						<span style="font-size: 1.2em;">/&nbsp;TOTAL</span>
					</td>
				</tr>
			</div>
		</div>
		<!-- popover template ends-->
	    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

		<script type="text/javascript">
			google.load('visualization', '1', {packages: ['corechart', 'line']});
			$(document).ready(function() {
				$('#daterange-custom').addClass('daterange');
				$('#daterange-custom').addClass('daterange-inline');
				$('#daterange-custom').daterangepicker({
		            "startDate": moment().subtract(29, 'days'),
		            "endDate": moment(),
		            "autoApply": true,
		            ranges: {
		                'Today': [moment(), moment()],
		                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		                'Last 30 Days': [moment().subtract(29, 'days'), moment()],                   
		                'This Month': [moment().startOf('month'), moment().endOf('month')],
		                'Last Month': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1, 'month').endOf('months')]                  
		            }
	            });
	            $('#date-select').text($('#daterange-custom').data('daterangepicker').startDate.format('MMMM DD,YYYY')+' - '+ $('#daterange-custom').data('daterangepicker').endDate.format('MMMM DD,YYYY'));
				getMissedUsers();
				getMonthWiseStatus();
				$('.daterange').on('apply.daterangepicker', function(ev, picker) {
					$('#date-select').text($('#daterange-custom').data('daterangepicker').startDate.format('MMMM DD,YYYY')+' - '+ $('#daterange-custom').data('daterangepicker').endDate.format('MMMM DD,YYYY'));
					getMissedUsers();
					getMonthWiseStatus();
				});
			});
			function getMissedUsers(dept) {
				var start=$('#daterange-custom').data('daterangepicker').startDate.format('YYYY-MM-DD'), end=$('#daterange-custom').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $.get("/admin/reports/store-missed-count?&from="+start+"&to="+end, function(data, status) {
                    var total_ans = 0;
                    $("#missed_call_users").html('');
                    if (data.length > 0) {
                        not_connected_call_item = '<tr><td>StoreCode</td><td>Location</td><td style="text-align: right"><span style="font-weight: 600;font-size: 1.2em;color: green;">MISSED&nbsp;</span><span style="font-size: 1.2em;">/&nbsp;TOTAL</span></td></tr>';
                        data.forEach(function(i){
                            $("#missed_call_users").append(not_connected_call_item.replace("TOTAL",i.total_calls).replace("MISSED",i.missed_count).replace("StoreCode",i.StoreCode).replace("Location",i.Location));
                            total_ans+=i.missed_count;
                        });
                    } else {
                        // $("#missed_call_users").append($("#missed_call_item").html().replace("TOTAL","__").replace("MISSED","__").replace("CLID","__"));
                        $("#missed_call_users").append(not_connected_call_item.replace("TOTAL",'__').replace("MISSED","__").replace("StoreCode","__").replace("Location","__"));
                    }
                    $("#ans_total_calls").text(total_ans);
                });
			}
			function getMonthWiseStatus()
			{
				var start=$('#daterange-custom').data('daterangepicker').startDate.format('YYYY-MM-DD'), end=$('#daterange-custom').data('daterangepicker').endDate.format('YYYY-MM-DD');
				console.log(start);
				console.log(end);
				$.get("/admin/reports/month-wise-status?&from="+start+"&to="+end, function(data, status) {
                    var total = connected = missed = ivrdrop = offline = total_connected_perc = total_missed_perc = total_ivrdrop_perc = total_offline_perc = 0;
                    if (data) {
                    	console.log(data);
                    	var list = document.getElementById("month-wise-status");
					    while (list.hasChildNodes()) {
					       list.removeChild(list.firstChild);
					    }
                    	var row = '<tr style="text-align:center"><td>MONTH</td><td>CONNECTED</td><td>MISSED</td><td>IVRDROP</td><td>OFFLINE</td></tr>';
                        data.forEach(function(i){
                        	var perc_connected = Math.round(i.connected/i.total*100);
                        	var perc_missed = Math.round(i.missed/i.total*100);
                        	var perc_ivrdrop = Math.round(i.ivrdrop/i.total*100);
                        	var perc_offline = Math.round(i.offline/i.total*100);
                        	
                            $("#month-wise-status").append(row.replace("MONTH",i.month+' - '+i.year).replace("CONNECTED",perc_connected+'%').replace("MISSED",perc_missed+'%').replace("IVRDROP",perc_ivrdrop+'%').replace("OFFLINE",perc_offline+'%'));
                            connected+= i.connected;
                            missed+= i.missed;
                            ivrdrop+= i.ivrdrop;
                            offline+= i.offline;
                            total+=i.total;
                        });
                        if(total !== 0) {
                        	total_connected_perc = Math.round(connected/total*100);
	                        total_missed_perc = Math.round(missed/total*100);
	                        total_ivrdrop_perc = Math.round(ivrdrop/total*100);
	                        total_offline_perc = Math.round(offline/total*100);
                        }
                        
                        $("#month-wise-status").append(row.replace("MONTH",'Grand Total').replace("CONNECTED",total_connected_perc+'%').replace("MISSED",total_missed_perc+'%').replace("IVRDROP",total_ivrdrop_perc+'%').replace("OFFLINE",total_offline_perc+'%'));
                        $('#month-wise-status tr:last-child').css('font-weight','bold');
						drawChart(data);
                    } else {
                        $("#month-wise-status").append('Nothing to show here');
                    }
                });
			}
			function drawChart(response)
			{
				var chartData = [];
				for (var i = 0; i < response.length; i++) {
					chartData[i] = [];
			        chartData[i].push(response[i].month+' - '+response[i].year);
			        chartData[i].push(response[i].total);
			    }
				var _data = new google.visualization.DataTable();
				_data.addColumn('string' , 'Month');
                _data.addColumn('number', 'Total Calls');
                var options = {
                        lineWidth : 4,
                        colors : [ '#335BFF'],
                        pointsVisible : true,
                        chartArea : {
                                left : 25, width : '95%'
                        },
                        curveType : 'function',
                        legend : {
                            position : 'bottom'
                        },
                        annotations : {
                            boxStyle : {
                                    stroke : '#888', strokeWidth : 5, rx : 10, ry : 10,
                                    gradient : {
                                            color1 : '#fbf6a7', color2 : '#33b679', x1 : '0%',
                                            y1 : '0%', x2 : '100%', y2 : '100%',
                                            useObjectBoundingBoxUnits : false
                                    }
                            }
                        },
                        vAxis: {viewWindowMode: "explicit", viewWindow:{ min: 0 }}
                };
                _data.addRows(chartData);
                new google.visualization.LineChart(document.getElementById('chart')).draw(_data, options);
			}
		</script>
    @endsection