@extends('layouts.master')

@section('content')

    <style type="text/css">
        .select2-choice {
            background-color: #ffffff !important;
            height: 30px !important;
        }

        .select2-arrow {
            background-color: #ffffff !important;
            border-left-color: #ffffff !important;
        }

        .select2-chosen {
            line-height: 30px !important;
        }

    </style>

    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Reports</h1>
        </div>
    </div>
    <!-- Responsive Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter</h3>
                    <div class="panel-options">
                        <a href="#" data-toggle="panel">
                            <span class="collapse-icon">–</span>
                            <span class="expand-icon">+</span>
                        </a>
                    </div>
                </div>
                <div class="panel-body">

                    <!--<form  role="form" class="form-inline">-->
                    <div>
                        <div class="form-group">
                            @if (in_array(Auth::user()->role, ['ADMIN']))
                                <div class="col-xs-3 form-group">
                                    <select name="State" id="StateId" class=" SourcesSelect form-control">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                <div class="col-xs-3 form-group">
                                    <select name="City" id="CityId" class=" SourcesSelect form-control">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                <div class="col-xs-3 form-group">
                                    <select name="Location" id="Location" class=" SourcesSelect form-control">
                                        <option value="">Select Location</option>
                                    </select>
                                </div>

                            @endif


                            @if (in_array(Auth::user()->role, ['ADMIN']))
                                <div class="col-xs-3 form-group">
                                    <select name="StoreId" id="StoreId" class=" SourcesSelect form-control">
                                        <!-- {{ empty($stores) ? 'disabled="disabled"' : '' }} -->
                                        <option value="">Select Store</option>
                                    </select>
                                </div>
                            @endif

                            <div>
                                <div class="col-xs-3 form-group">
                                    <select name="Status" id="Status" class=" SourcesSelect form-control">
                                        <option value="0">Any Status</option>
                                        <option {{ @$params['Status'] == 'Connected' ? 'selected' : '' }}
                                            value="Connected">
                                            Connected</option>
                                        <option {{ @$params['Status'] == 'Missed' ? 'selected' : '' }} value="Missed">
                                            Missed</option>
                                        <option {{ @$params['Status'] == 'IVR Drop' ? 'selected' : '' }} value="IVR Drop">
                                            IVR Drop</option>
                                        <option {{ @$params['Status'] == 'Offline' ? 'selected' : '' }} value="Offline">
                                            Offline</option>
                                    </select>
                                </div>
                                <div class="col-xs-3 form-group">
                                    <input type="text"
                                        value="{{ !empty($params['date_from']) ? $params['date_from'] : '' }}"
                                        name="date_from" id="date_from" data-format="yyyy-mm-dd"
                                        class="form-control datepicker" size="15" data-end-date="+1"
                                        placeholder="Date From" />
                                </div>
                                <div class="col-xs-3 form-group">
                                    <input type="text" value="{{ !empty($params['date_to']) ? $params['date_to'] : '' }}"
                                        name="date_to" id="date_to" data-format="yyyy-mm-dd" class="form-control datepicker"
                                        size="15" data-end-date="+1" placeholder="Date To" />
                                </div>
                                <div class="col-xs-3 form-group">
                                    <input value="{{ !empty($params['CallerId']) ? $params['CallerId'] : '' }}"
                                        type="text" id="CallerId" name="CallerId" class="form-control" size="15"
                                        placeholder="Customer Number">
                                </div>
                                <div>

                                </div>
                            </div>


                            <!--</form>-->

                        </div>
                    </div>
                </div>
                <div class="form-group ">
                    <label class="cbr-inline">
                        <button id="reset" class="btn btn-primary btn-single">Reset Filter</button>
                    </label>
                    <div class="form-group pull-right" style="margin-top: 9px;">
                        <button id="filter" class="btn btn-secondary btn-single">Filter</button>
                        <button id="export" class="btn btn-secondary btn-single">Export</button>
                    </div>
                </div>
            </div>
            <!--CALL FILTER SECTION ENDS HERE-->

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Statistics</h3>
                            <div class="panel-options">
                                <a href="#" data-toggle="panel">
                                    <span class="collapse-icon">–</span>
                                    <span class="expand-icon">+</span>
                                </a>
                            </div>
                        </div>
                        <div class="panel-body" id="chart_div"></div>
                    </div>
                </div>
            </div>

            <div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="connected">
                            </strong> <span>Connected</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter-red">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="missed">
                            </strong> <span>Missed</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter-blue">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="ivr_drop">
                            </strong> <span>IVR Drop</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter">
                        <div class="xe-icon">
                            <i class="fa-phone" style="background-color: black"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="offline">
                            </strong> <span>Offline</span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row" id="avg_row" style="display:none;">
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter-yellow">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="total">
                            </strong> <span>Total</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter ">
                        <div class="xe-icon">
                            <i class="fa-clock-o" style="background-color: #11B2AA"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_conv_duration"></strong> <span>Avg.
                                Conversation Duration</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter xe-counter-yellow">
                        <div class="xe-icon">
                            <i class="fa-clock-o" style="background-color: #177C99"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_total_duration"></strong> <span>Avg.
                                Total Duration</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter xe-counter-yellow">
                        <div class="xe-icon">
                            <i class="fa-clock-o" style="background-color: #996b17"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_ring_duration"></strong> <span>Avg.
                                Ring Duration</span>
                        </div>
                    </div>
                </div>
            </div>

             <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Listing : Total calls {{ @$totalItems }}</h3>
                        <div class="panel-options">
                            <a href="#">
                            </a>
                        </div>
                    </div>
                    <div id="display" class="panel-body">
                        @if (empty($callLogs))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">No Records For This Selection</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive" data-pattern="priority-columns" data-focus-btn-icon="fa-asterisk"
                                data-sticky-table-header="false" data-add-display-all-btn="true" data-add-focus-btn="false">
                                <table id="callTable" cellspacing="0"
                                    class="table table-small-font table-bordered table-striped cl-table">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">Call Start Time</th>
                                            <th data-priority="1">Customer Number</th>
                                            <th data-priority="1">Virtual Number</th>
                                            <th data-priority="1">Store Code</th>
                                            <th data-priority="1">Store Name</th>
                                            <th data-priority="1">Status</th>
                                            <th data-priority="1">Agent Number</th>
                                            <th data-priority="1">IVRDuration</th>
                                            <th data-priority="1">Conversation Duration</th>
                                            <th data-priority="1">Ring Duration</th>
                                            <th data-priority="1">Busy Callees</th>
                                            <th data-priority="1">Location</th>
                                            <th data-priority="1">City</th>
                                            <th data-priority="1">State</th>
                                            <th data-priority="1">Zone</th>
                                            <th data-priority="1">Call Recording</th>
                                            <th data-priority="1">HangupLeg </th>
                                            <th data-priority="1">Call Type </th>
                                            <th data-priority="1">Call End Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($callLogs as $call_log)
                                            <tr class="callDtls" id="{{ @$call_log['_id'] }}">
                                                <td>
                                                    <?php
                                                    $date = !isset($call_log['CallStartTime']) ? null : $call_log['CallStartTime'];
                                                    ?>
                                                    <a
                                                        href="/{{ strtolower(Auth::user()->role) }}/reports/call/{{ @$call_log['_id'] }}">
                                                        {{ @date('j/M/y, g:i:s A', strtotime($date)) }}</a>
                                                </td>
                                                <td>{{ !empty($call_log['CustomerNumber']) ? $call_log['CustomerNumber'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['VirtualNumber']) ? $call_log['VirtualNumber'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['StoreCode']) ? $call_log['StoreCode'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['StoreName']) ? $call_log['StoreName'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['Status']) ? $call_log['Status'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['AgentNumber']) ? $call_log['AgentNumber'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['IVRDuration']) ? $call_log['IVRDuration'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['ConversationDuration']) ? $call_log['ConversationDuration'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['RingDuration']) ? $call_log['RingDuration'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['BusyCalleesStr']) ? $call_log['BusyCalleesStr'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['Location']) ? $call_log['Location'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['City']) ? $call_log['City'] : '--' }}</td>
                                                <td>{{ !empty($call_log['State']) ? $call_log['State'] : '--' }}</td>
                                                <td>{{ !empty($call_log['Zone']) ? $call_log['Zone'] : '--' }}</td>
                                                @if ($call_log['Status'] == 'Connected')
                                                    <td style="padding:0">
                                                        <audio src="{{ @$call_log['CallRecordUrl'] }}"
                                                            id="audio_{{ @$call_log['_id'] }}" preload="metadata"
                                                            controls>
                                                    </td>
                                                @else
                                                    <td>--</td>
                                                @endif
                                                {{-- <td style="padding-top: 10px;padding-bottom: 10px;">
                                                        @if (!empty($call_log['AgentRecords']))
                                                            <input type="hidden" class="agent-record-list"
                                                                value="{{ json_encode($call_log['AgentRecords']) }}" />
                                                            @foreach (array_unique($call_log['AgentRecords']) as $agentRecord => $agentNumber)
                                                                <a href="javascript:{};"
                                                                    class="busy-callees">{{ $agentNumber }}</a>,
                                                            @endforeach
                                                        @else
                                                            ---
                                                        @endif
                                                    </td> --}}



                                                <td>{{ !empty($call_log['HangupLeg']) ? $call_log['HangupLeg'] : '--' }}
                                                </td>
                                                <td>{{ !empty($call_log['CallType']) ? $call_log['CallType'] : '--' }}</td>
                                                <td>
                                                    @if (!empty($call_log['CallEndTime']))
                                                        {{ @date('j/M/y, g:i:s A', strtotime($call_log['CallEndTime'])) }}
                                                    @else
                                                        {{ '--' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if (!empty($callLogs))
                            <!--PAGINATION-->
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if (count($paginator['pagesInRange'])): ?>
                                    <div class="tPages pull-right">
                                        <ul class="pages pagination">
                                            <?php if (isset($paginator['previous'])): ?>
                                            <li class="prev pagelink"><a href="javascript:{};"><span
                                                        class="icon-arrow-14"></span>prev</a></li>
                                            <?php else: ?>
                                            <li class="prev disabled"><a class="disabled"><span
                                                        class="icon-arrow-14"></span>prev</a></li>
                                            <?php
                                    endif;
                                    foreach ($paginator['pagesInRange'] as $page):
                                        if ($page != $paginator['current']):
                                            ?>
                                            <li class="pagelink"><a href="javascript:{};"><?php echo trim($page); ?></a>
                                            </li>
                                            <?php else: ?>
                                            <li class="active"><a href="javascript:{};"><?php echo trim($page); ?></a>
                                            </li>
                                            <?php
                                        endif;
                                    endforeach;
                                    if (isset($paginator['next'])):
                                        ?>
                                            <li class="next pagelink"><a href="javascript:{};">next<span
                                                        class="icon-arrow-17"></span></a></li>
                                            <?php else: ?>
                                            <li class="next disabled"><a class="disabled">next<span
                                                        class="icon-arrow-17"></span></a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--PAGINATION-->
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- Imported scripts on this page -->

    <script type="text/javascript">
        // This JavaScript Will Replace Checkboxes in dropdown toggles
        $(document).ready(function($) {
            setTimeout(function() {
                $(".checkbox-row input").addClass('cbr');
                cbr_replace();
            }, 0);
        });

        var storePointer = $("#StoreId");
        var statePointer = $("#StateId");
        var locationPointer = $("#Location");
        var cityPointer = $("#CityId");

        var storeVal = '';
        var locationVal = '';
        var cityVal = '';
        var stateVal = '';

       

        @if (!empty($params['StoreId']))
        
            storeVal = '{{ $params['StoreId'] }}';
        @endif

        @if (!empty($params['Location']))
        
            locationVal = '{{ $params['Location'] }}';
        @endif


        @if(!empty($params['CityId']))

            cityVal = '{{ $params['CityId'] }}';
        @endif

        @if(!empty($params['StateId']))

            stateVal = '{{ $params['StateId'] }}';
        @endif


        $(document).ready(function($) {
            $('#StateId').on('change', function() {
                var state = $('#StateId').find(':selected').text();
                var stateVal = $('#StateId').find(':selected').val();
                if (state) {
                    getCitiesByState(stateVal);
                    getLocationByState(stateVal);
                    getStoresByState(stateVal);
                }
            });

            $('#CityId').on('change', function() {
                var city = $('#CityId').find(':selected').text();
                var cityVal = $('#CityId').find(':selected').val();
                if (city) {                       
                    getStoresByCity(cityVal);
                    getLocationByCity(cityVal);
                }
            });
        });


        function getCitiesByState(stateVal) {

            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-cities-by-state/' + stateVal,
                success: function(response) {

                    $('#CityId').children().remove();
                    $('#CityId').append('<option value="">Select City</option>');

                    $.each(response, function(i, value) {
                        if (i == cityVal) {

                            $('#CityId').append($('<option>', {

                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $('#CityId').append($('<option>', {

                                value: i,
                                text: value,
                            }));
                        }
                    });
                    $("#CityId").select2({
                        placeholder: "Select City",
                        allowClear: true
                    });
                }
            });
        }

        function getStoresByState(stateVal) {
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-stores-by-state/' + stateVal,
                success: function(response) {

                    $('#StoreId').children().remove();
                    $('#StoreId').append('<option value="">Select Store</option>');

                    $.each(response, function(i, value) {
                        if (i == cityVal) {

                            $('#StoreId').append($('<option>', {

                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $('#StoreId').append($('<option>', {

                                value: i,
                                text: value,
                            }));
                        }
                    });
                    $("#StoreId").select2({
                        placeholder: "Select Store",
                        allowClear: true
                    });
                }
            });
        }

        function getLocationByState(stateVal) {
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-location-by-state/' + stateVal,
                success: function(response) {

                    $('#Location').children().remove();
                    $('#Location').append('<option value="">Select Location</option>');

                    $.each(response, function(i, value) {
                        if (i == locationVal) {

                            $('#Location').append($('<option>', {

                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $('#Location').append($('<option>', {

                                value: i,
                                text: value,
                            }));
                        }
                    });
                    $("#Location").select2({
                        placeholder: "Select Location",
                        allowClear: true
                    });
                }
            });
        }

        function getStoresByCity(cityVal) {
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-stores-by-city/' + cityVal,
                success: function(response) {

                    $('#StoreId').children().remove();
                    $('#StoreId').append('<option value="">Select Store</option>');

                    $.each(response, function(i, value) {
                        if (i == storeVal) {

                            $('#StoreId').append($('<option>', {

                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $('#StoreId').append($('<option>', {

                                value: i,
                                text: value,
                            }));
                        }
                    });
                    $("#StoreId").select2({
                        placeholder: "Select Store",
                        allowClear: true
                    });
                }
            });
        }

        function getLocationByCity(cityVal) {
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-location-by-city/' + cityVal,
                success: function(response) {

                    $('#Location').children().remove();
                    $('#Location').append('<option value="">Select Location</option>');

                    $.each(response, function(i, value) {
                        if (i == locationVal) {

                            $('#Location').append($('<option>', {

                                value: value,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $('#Location').append($('<option>', {

                                value: value,
                                text: value,
                            }));
                        }
                    });
                    $("#Location").select2({
                        placeholder: "Select Location",
                        allowClear: true
                    });
                }
            });
        }



        function getFilterParams() {

            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/get-filter-params',
                success: function(response) {

                @if (in_array(Auth::user()->role, ['ADMIN','RM']))
                    $.each(response['states'],function(i, value) {
                        if (i == stateVal) {
                            $(statePointer).append($('<option>', {
                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {
                            $(statePointer).append($('<option>', {
                                value: i,
                                text: value
                            }));
                        }
                    });

                     $(statePointer).select2({
                            placeholder: "Select State",
                            allowClear: true
                        });

                    $.each(response['cities'],function(i, value) {
                        if (i == cityVal) {
                            $(cityPointer).append($('<option>', {
                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {
                            $(cityPointer).append($('<option>', {
                                value: i,
                                text: value
                            }));
                        }
                    });

                    $(cityPointer).select2({
                        placeholder: "Select City",
                        allowClear: true
                    });

                    $.each(response['locations'],function(i) {

                        if(response['locations'][i].locality == locationVal) {

                            $(locationPointer).append($('<option>',{

                                value : response['locations'][i].locality,
                                text : response['locations'][i].locality,
                                selected : true
                            }));
                        } else {

                            $(locationPointer).append($('<option>',{

                                value : response['locations'][i].locality,
                                text : response['locations'][i].locality,
                            }));
                        }
                    });

                    $(locationPointer).select2({
                        placeholder: "Select Location",
                        allowClear: true
                    });

                    $.each(response['stores'], function(i, value) {
                        if (i == storeVal) {

                            $(storePointer).append($('<option>', {

                                value: i,
                                text: value,
                                selected: true
                            }));
                        } else {

                            $(storePointer).append($('<option>', {

                                value: i,
                                text: value
                            }));
                        }
                    });
                    $("#StoreId").select2({
                        placeholder: "Select Store",
                        allowClear: true
                    });
                @endif


                }
            });
        }

    </script>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {
            packages: ['corechart', 'line']
        });
        google.setOnLoadCallback(drawChart);
        @if (in_array(Auth::user()->role, ['ADMIN', 'ASM', 'RM']))
            {
        
            getFilterParams();
            }
        @endif


        function drawChart() {
            var qString = _setupFilterQueryString();
            $.ajax({
                type: 'GET',
                url: "/{{ strtolower(Auth::user()->role) }}/reports/chart" + qString,

                success: function(response) {
                    var _data = new google.visualization.DataTable();
                    _data.addColumn('date', 'Time of Day');
                    _data.addColumn('number', 'Connected Calls');
                    _data.addColumn('number', 'Missed Calls');
                    _data.addColumn('number', 'Total Calls');
                    var options = {
                        // title : 'Call Status',
                        lineWidth: 4,
                        colors: ['#68b828', '#d5080f', '#fcd036'],
                        pointsVisible: true,
                        chartArea: {
                            left: 25,
                            width: '95%'
                        },
                        curveType: 'function',
                        legend: {
                            position: 'bottom'
                        },
                        annotations: {
                            boxStyle: {
                                stroke: '#888',
                                strokeWidth: 5,
                                rx: 10,
                                ry: 10,
                                gradient: {
                                    color1: '#fbf6a7',
                                    color2: '#33b679',
                                    x1: '0%',
                                    y1: '0%',
                                    x2: '100%',
                                    y2: '100%',
                                    useObjectBoundingBoxUnits: false
                                }
                            }
                        },
                        vAxis: {
                            viewWindowMode: "explicit",
                            viewWindow: {
                                min: 0
                            }
                        }
                    };
                    response.forEach(function(row) {
                        row[0] = new Date(row[0]);
                    });
                    _data.addRows(response);
                    new google.visualization.LineChart(document.getElementById('chart_div')).draw(_data,
                        options);
                }
            });
        }


        function _setupFilterQueryString() {
            var qString = '?',
                callerId;
            
            if (storeVal != '') {
                qString += '&StoreId=' + storeVal;
            } else {
                qString += '&StoreId=';
            }


            if ($('#StateId').length) {
                if ($('#StateId').val() != '')
                    qString += '&StateId=' + $('#StateId').val();
                else
                    qString += '&StateId=';
            }
            if ($('#CityId').length) {
                if ($('#CityId').val() != '')
                    qString += '&CityId=' + $('#CityId').val();
                else
                    qString += '&CityId=';
            }

            if ($('#Location').length) {
                if ($('#Location').val() != '')
                    qString += '&Location=' + $('#Location').val();
                else
                    qString += '&Location=';
            }

            if ($('#date_from').val() != '' && $('#date_to').val() != '')
                qString += '&date_from=' + $('#date_from').val() + '&date_to=' + $('#date_to').val();
            else if ($('#date_from').val() != '')
                qString += '&date_from=' + $('#date_from').val() + '&date_to=';
            else
                qString += '&date_from=&date_to=';
            if ($('#Status').length) {
                if ($('#Status').val() != '')
                    qString += '&Status=' + $('#Status').val();
                else
                    qString += '&Status=';
            }
            if ($('#CallerId').length) {
                if ($('#CallerId').val() != '') {
                    callerId = $.trim($('#CallerId').val());
                    callerId = encodeURIComponent(callerId);
                    qString += '&CallerId=' + callerId;
                } else {
                    qString += '&CallerId=';
                }
            }
            
            return qString;
        }

        function getUrlParams() {
            var vars = [],
                hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        function getCallDurationAverage() {
            var pageParams = getUrlParams();
            var currentString = '';
            for (var i = 1; i < pageParams.length; i++) {
                var paramVal = pageParams[i];
                if (paramVal || paramVal != '') {

                    currentString += '&' + pageParams[i] + '=' + pageParams[paramVal];
                }
            }
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/call-duration-average' + '?' + currentString,
                success: function(response) {
                    response = response[0];
                    if (response && response.Count) {
                        var avgTotalDuration = Math.round(response.TotalDuration / response.Count);
                        var totalRing = Math.round(response.Count - (response['IVR Drop'] + response[
                            'Offline']));
                        var avgRingDuration = Math.round(response.RingDuration/totalRing);
                        $('#avg_total_duration').text(avgTotalDuration + 's');
                        if (response.TotalAnsweredCalls) {
                            var avgConversationDuration = Math.round(response.ConversationDuration / response
                                .TotalAnsweredCalls);
                        } else {
                            var avgConversationDuration = '0';
                        }
                        $('#avg_conv_duration').text(avgConversationDuration + 's');
                        $('#missed').text(response.Missed);
                        $('#connected').text(response.TotalAnsweredCalls);
                        var avgIVRDuration = Math.round(response.IVRDuration / response.Count);
                        $('#avg_waiting_time').text(avgIVRDuration + 's');
                        $("#ivr_drop").text(response['IVR Drop']); //IVR Drop
                        $("#total").text(response.Count);
                        $("#offline").text(response['Offline']);
                        $("#avg_ring_duration").text(avgRingDuration + 's');
                    } else {
                        $('#avg_total_duration').text('0s');
                        $('#avg_conv_duration').text('0s');
                        $('#avg_waiting_time').text('0s');
                        $('#missed').text('0');
                        $('#connected').text('0');
                        $("#total").text('0');
                        $("#offline").text('0');
                        $("#avg_ring_duration").text('0s');
                    }
                    $('#avg_row').show();
                }
            });
        }
        getCallDurationAverage();
    </script>
    <link rel="stylesheet" href="/libraries/xenon/js/select2/select2.css" type="text/css">
    <link rel="stylesheet" href="/libraries/xenon/js/select2/select2-bootstrap.css" type="text/css">
    <script src="/libraries/xenon/js/select2/select2.min.js"></script>
    <script src="/js/custom/WF.setupPaginator.js"></script>
    <script src="/libraries/xenon/js/rwd-table/js/rwd-table.min.js"></script>
    <script src="/libraries/xenon/js/datepicker/bootstrap-datepicker.js"></script>
    <script src="/js/custom/admin/WF.reports.js?v=0.1"></script>
    <script type="text/javascript">
        WF.reports.initCalllog('{{ strtolower(Auth::user()->role) }}');
        if (window.localStorage.getItem('CustomFields') == null ||
            window.localStorage.getItem('CustomFields') == "") {
            WF.reports.setCustomFields("{{ $params['Fields'] }}");
            WF.reports.set('CustomResetFields', "{{ $params['Fields'] }}");
        } else {
            WF.reports.setCustomFields(window.localStorage.getItem('CustomFields'));
            WF.reports.set('CustomResetFields', window.localStorage.getItem('CustomFields'));
        }
        $(window).load(function() {
            window.setTimeout(WF.reports.showCustomTableData, 1000); // 5 seconds
        });
    </script>


@endsection

@section('modals')
    <div class="modal fade" id="callee_recodings_modal" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Dial Records</h4>
                </div>
                <div class="modal-body">
                    <!-- audio player -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="insights_modal" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Signals</h4>
                </div>
                <div class="modal-body" style="padding-bottom: 0px;">
                    <div class="row">
                        <div class="btn-group" data-toggle="buttons" style="margin-right: 0px;margin-left: 15px;">
                            <label class="btn btn-white active">
                                <input type="radio" name="wav_options" id="conversation_wave">Conversation
                            </label>
                            <label class="btn btn-white">
                                <input type="radio" name="wav_options" id="customer_wave">Customer
                            </label>
                            <label class="btn btn-white">
                                <input type="radio" name="wav_options" id="dealership_wave">Dealership
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-1" style="margin-top: 30px; margin-right: 10px;">
                            <button class="btn btn-icon btn-success" id="playPause">
                                <i class="fa-play"></i><i class="fa-pause"></i>
                                <input type="hidden" id="call_rec_url_modal">
                            </button>
                        </div>
                        <div class="col-sm-10">
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/2.0.6/wavesurfer.min.js"></script>
                            <div id="waveform"></div>
                            <script type="text/javascript">
                                wavesurfer = WaveSurfer.create({
                                    container: '#waveform',
                                    waveColor: '#40bbea',
                                    progressColor: '#0e62c7',
                                    barHeight: 1.5,
                                    barWidth: 3,
                                    height: 100
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">Cars</div>
                                <div class="panel-body" id="carTagDiv">
                                    <div class="vertical-top"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">Keywords</div>
                                <div class="panel-body" id="keywordTagDiv">
                                    <div class="vertical-top"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
