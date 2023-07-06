@extends('layouts.master')

    @section('content')
    
    
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
                    <div class="row col-sm-12">
                        <div class="form-group">
                            <div class="col-xs-3 form-group">
                                <input type="text" value="{{ !empty($params['date_from']) ? $params['date_from']:""}}" name="date_from" id="date_from" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date From"/>
                            </div>
                            <div class="col-xs-3 form-group">
                                <input type="text" value="{{ !empty($params['date_to']) ? $params['date_to']:""}}" name="date_to" id="date_to" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date To"/>
                            </div>
                            <div class="col-xs-3 form-group">
<!--                                <select name="Type" id="Type" class=" SourcesSelect form-control">
                                    <option value=''>Select Virtual Number Type</option>
                                    <option {{ (!empty($params['Type'])?$params['Type']:'') == "SC_DBM" ? "selected":""}} value='SC_DBM'>SC_DBM</option>
                                    <option {{ (!empty($params['Type'])?$params['Type']:'') == "SC_VSERVE" ? "selected":""}} value='SC_VSERVE'>SC_VSERVE</option>
                                </select>-->
                            </div>
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
            
            <!--CHART DIV-->
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
            <!--CHART DIV-->
            
            <!--COUNT DIV-->
            <div class="row">
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num"><?php echo $count['Call Answered']; ?>
                                    <!-- <small style="color:grey;font-size: 15px;">(<?php echo ($count['Total']>0?round((($count['Call Answered']/$count['Total'])*100),2):0); ?>%)</small> -->
                            </strong> <span>Answered</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter-red">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num"><?php echo $count['Missed Call']; ?>
                                    <!-- <small style="color:grey;font-size: 15px;">(<?php echo ($count['Total']>0?round((($count['Missed Call']/$count['Total'])*100),2):0); ?>%)</small> -->
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
                           <strong class="num"><?php echo $count['Call Abandoned']; ?>
                                    <!-- <small style="color:grey;font-size: 15px;">(<?php echo ($count['Total']>0?round((($count['Call Abandoned']/$count['Total'])*100),2):0); ?>%)</small> -->
                            </strong> <span>Abandoned</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="xe-widget xe-counter xe-counter-yellow">
                        <div class="xe-icon">
                            <i class="fa-phone"></i>
                        </div>
                        <div class="xe-label">
                           <strong class="num"><?php echo $count['Total']; ?>
                                    <!-- <small style="color:grey;font-size: 15px;">(<?php echo ($count['Total']>0?round((($count['Call Abandoned']/$count['Total'])*100),2):0); ?>%)</small> -->
                            </strong> <span>Total</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--COUNT DIV-->
            
            <!--DURATION DIV-->
            <div class="row" id="avg_row" style="display:none;">
                <div class="col-md-3">
                    <div class="xe-widget xe-counter ">
                        <div class="xe-icon">
                            <i class="fa-clock-o"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_conv_duration"></strong> <span>Avg.
                            Conversation Duration</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter xe-counter-red" data-count=".num">
                        <div class="xe-icon">
                            <i class="fa-clock-o"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_ring_duration"></strong> <span>Avg.
                                Ring Duration</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter xe-counter-blue" data-count=".num">
                        <div class="xe-icon">
                            <i class="fa-clock-o"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_waiting_time"></strong> <span>Avg.
                                IVR Duration</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="xe-widget xe-counter xe-counter-yellow">
                        <div class="xe-icon">
                            <i class="fa-clock-o"></i>
                        </div>
                        <div class="xe-label">
                            <strong class="num" id="avg_total_duration"></strong> <span>Avg.
                                Total Duration</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--DURATION DIV-->
            
            
            <!--CALLLOGS SECTION-->
            <div class="row">
                <div class="col-md-12">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Listing : Total calls <?php echo $totalItems; ?></h3>
                            <div class="panel-options">
                                <a href="#">
                                </a>                   
                            </div>
                        </div>
                        <div id="display" class="panel-body">
                            @if(empty($callLogs))
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
                            <div  class="table-responsive" data-pattern="priority-columns" data-focus-btn-icon="fa-asterisk" data-sticky-table-header="false" data-add-display-all-btn="true" data-add-focus-btn="false">
                                <table id="callTable"cellspacing="0" class="table table-small-font table-bordered table-striped cl-table">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">Call Start Time</th>
                                            <th data-priority="1">Dealer</th>
                                            <th data-priority="1">Location</th>
                                            <th data-priority="1">Panda Code</th>
                                            <th data-priority="1">Workshop Panda Code</th>
                                            <th data-priority="1">Special Panda Code</th>
                                            <th data-priority="1">Call Recording</th>
                                            <th data-priority="1">Dial Recording</th>
                                            <th data-priority="1">Busy Callees</th>
                                            <th data-priority="1">Conversation Duration</th>
                                            <th data-priority="1">VN Type</th>
                                            <th data-priority="1">Call Type</th>
                                            <th data-priority="1">Status</th>
                                            <th data-priority="1">Customer Number</th>
                                            <th data-priority="1">Virtual Number</th>
                                            <th data-priority="1">Connected To</th>
                                            <th data-priority="1">IVR Duration</th>
                                            <th data-priority="1">Ring Duration</th>
                                            <th data-priority="1">Callee Leg Status</th>
                                            <th data-priority="1">Hangup Leg</th>
                                            <th data-priority="1">Repeated Caller</th>
                                            <th data-priority="1">RSM</th>
                                            <th data-priority="1">DOM</th>
                                            <th data-priority="1">Region</th>
                                            <th data-priority="1">State</th>
                                            <th data-priority="1">Call End Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($callLogs as $call_log)
                                        <tr class="callDtls" id="{{ @$call_log['_id'] }}">
                                            <td style="padding-top: 10px;padding-bottom: 10px;">
                                                <?php 
                                                $date = !isset($call_log['DateTime']) ? null : $call_log['DateTime'];
                                                ?>
                                                <a href="/{{ strtolower(Auth::user()->role) }}/reports/call/{{ @$call_log['_id'] }}">{{ @date("j/M/y, g:i:s A",strtotime($date)) }}</a>
                                            </td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Dealer'])?$call_log['Dealer']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Location'])?$call_log['Location']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['PandaCode'])?$call_log['PandaCode']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['WorkshopPandaCode'])?$call_log['WorkshopPandaCode']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ 
                                            (!empty($call_log['SpecialCampaignPandaCode'])?$call_log['SpecialCampaignPandaCode']:'--')}}</td>
                                            <td style="padding:0">
                                                <audio src="{{ @$call_log['CallRecordUrl'] }}" id="audio_{{ @$call_log['_id'] }}" preload="metadata" controls>
                                                @if (!isset($call_log['AgentRecordNames']) && !isset($call_log['AgentRecords']))
                                                    <script type="text/javascript">
                                                        $(window).load(function() {
                                                            if({{ @$call_log['IVRDuration'] }}) {
                                                                var audio = document.getElementById("audio_{{ @$call_log['_id'] }}");
                                                                var seek_time = {{ @$call_log['IVRDuration'] }} + {{ @$call_log['RingDuration'] }};
                                                                audio.currentTime = seek_time;
                                                            }
                                                        });
                                                    </script>
                                                @endif
                                            </td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">
                                                @if (!empty($call_log['AgentRecords']))
                                                    <input type="hidden" class="agent-record-list" value="{{ json_encode($call_log['AgentRecords']) }}" />
                                                    @foreach (array_unique($call_log['AgentRecords']) as $agentRecord => $agentNumber)
                                                        <a href="javascript:{};" class="busy-callees">{{ $agentNumber }}</a>, 
                                                    @endforeach
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">
                                                @if (!empty($call_log['BusyCallees']))
                                                    {{ implode(', ', $call_log['BusyCallees']) }}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['ConversationDuration'])?$call_log['ConversationDuration']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Type'])?$call_log['Type']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">
                                                @if (!empty(@$call_log['IvrLog']['0']))
                                                    {{ current($call_log['IvrLog']['0']) }}
                                                @else 
                                                    {{ '--' }}
                                                @endif
                                            </td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Status'])?$call_log['Status']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['CallerId'])?$call_log['CallerId']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['MaskedNumber'])?$call_log['MaskedNumber']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['AgentNumber'])?$call_log['AgentNumber']:'') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['IVRDuration'])?$call_log['IVRDuration']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['RingDuration'])?$call_log['RingDuration']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['CalleeLegStatus'])?$call_log['CalleeLegStatus']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['HangupLeg'])?$call_log['HangupLeg']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (empty($callDetails['RepeatedCaller'])?'Unique':'Repeat') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Rsm'])?$call_log['Rsm']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Dom'])?$call_log['Dom']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['Region'])?$call_log['Region']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">{{ (!empty($call_log['State'])?$call_log['State']:'--') }}</td>
                                            <td style="padding-top: 10px;padding-bottom: 10px;">
                                                <?php
                                                if (!isset($call_log['DateTime'])) {
                                                    echo '--';
                                                } else {
                                                    $duration = empty($call_log['TotalDuration'])? 0: $call_log['TotalDuration'];
                                                }
                                                ?>
                                                {{ @date("j/M/y, g:i:s A",strtotime($call_log['DateTime']) + $call_log['TotalDuration']) }}
                                            </td>

                                        </tr> 

                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                            @endif

                            <!--PAGINATION-->
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if (count($paginator['pagesInRange'])): ?>
                                    <div class="tPages pull-right">
                                        <ul class="pages pagination">
                                            <?php if (isset($paginator['previous'])): ?>
                                                <li class="prev"><a href="javascript:{};"><span class="icon-arrow-14"></span>prev</a></li>
                                            <?php else: ?>
                                                <li class="prev disabled"><a class="disabled"><span class="icon-arrow-14"></span>prev</a></li>
                                            <?php
                                            endif;
                                            foreach ($paginator['pagesInRange'] as $page):
                                                if ($page != $paginator['current']):
                                                    ?>
                                                    <li><a href="javascript:{};"><?php echo trim($page); ?></a></li>
                                                <?php else: ?>
                                                    <li class="active"><a href="javascript:{};"><?php echo trim($page); ?></a></li>
                                                <?php
                                                endif;
                                            endforeach;
                                            if (isset($paginator['next'])):
                                                ?>
                                                <li class="next"><a href="javascript:{};">next<span class="icon-arrow-17"></span></a></li>
                                            <?php else: ?>
                                                <li class="next disabled"><a class="disabled">next<span class="icon-arrow-17"></span></a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    </div>
                                <input type="hidden" id="routing_design" value=""/>
                                <input type="hidden" id="campaign_type" value=""/>
                            </div>
                            <!--PAGINATION-->

                        </div>

                    </div>
                </div>
            </div>
            <!--CALLLOG SECTION-->
            
        </div>
    </div>
    
    <!--SCRIPT-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load('visualization', '1', {packages: ['corechart', 'line']});
        google.setOnLoadCallback(drawChart);

        function drawChart() {
            var qString = _setupFilterQueryString();
            $.ajax({
                type: 'GET',
                url: "/{{ strtolower(Auth::user()->role) }}/reports/chart" + qString,

                success: function(response) {
                    var _data = new google.visualization.DataTable();
                    _data.addColumn('date', 'Time of Day');
                    _data.addColumn('number', 'Answered Calls');
                    _data.addColumn('number', 'Missed Calls');
                    _data.addColumn('number', 'Abandoned Calls');
                    _data.addColumn('number', 'Total Calls');
                    var options = {
                            // title : 'Call Status',
                            lineWidth : 4,
                            colors : [ '#68b828', '#d5080f', '#0e62c7', '#fcd036' ],
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
                    response.forEach(function(row){
                        row[0] = new Date(row[0]);
                    });
                    _data.addRows(response);
                    new google.visualization.LineChart(document.getElementById('chart_div')).draw(_data, options);
                }
            });
        }
        
        
         function _setupFilterQueryString() {
            var qString = '?', callerId;
            if($('#date_from').val() != '' && $('#date_to').val() != '')
                qString += '&date_from='+$('#date_from').val()+'&date_to='+$('#date_to').val();
            else if($('#date_from').val() != '')
                qString += '&date_from='+$('#date_from').val()+'&date_to=';
            else
                qString += '&date_from=&date_to=';
            if($('#Status').length) {
                if($('#Status').val() != '')
                    qString += '&Status='+$('#Status').val();
                else
                    qString += '&Status=';
            }
            if($('#Type').length) {
                if($('#Type').val() != '')
                    qString += '&Type='+$('#Type').val();
                else
                    qString += '&Type=';
            }
            if($('#callType').length) {
                if($('#callType').val() != '')
                    qString += '&callType='+$('#callType').val();
                else
                    qString += '&callType=';
            }
            return qString;
        }

        function getCallDurationAverage() {
            var qString = _setupFilterQueryString();
            $.ajax({
                type: 'GET',
                url: '/{{ strtolower(Auth::user()->role) }}/reports/call-duration-average' + qString,
                success: function(response) {
                    response = response[0];
                    if (response.Count) {
                        var avgTotalDuration = Math.round(response.TotalDuration/response.Count);
                        $('#avg_total_duration').text(avgTotalDuration + 's');
                        var avgConversationDuration = Math.round(response.ConversationDuration/response.Count);
                        $('#avg_conv_duration').text(avgConversationDuration + 's');
                        var avgIVRDuration = Math.round((response.IVRDuration + response.RingDuration)/response.Count);
                        $('#avg_waiting_time').text(avgIVRDuration + 's');
                        var avgRingDuration = Math.round(response.RingDuration/response.Count);
                        $('#avg_ring_duration').text(avgRingDuration + 's');
                    } else {
                        $('#avg_total_duration').text('0s');
                        $('#avg_conv_duration').text('0s');
                        $('#avg_waiting_time').text('0s');
                        $('#avg_ring_duration').text('0s');
                    }
                    $('#avg_row').show();
                }
            });
        }
        getCallDurationAverage();

    </script>

    <script src="/js/custom/WF.setupPaginator.js"></script>
    <script src="/libraries/xenon/js/rwd-table/js/rwd-table.min.js"></script>
    <script src="/libraries/xenon/js/datepicker/bootstrap-datepicker.js"></script>
    <script src="/js/custom/admin/WF.reports.js"></script>
    <script type="text/javascript">
        WF.reports.initCalllog('{{ strtolower(Auth::user()->role) }}');
        if(window.localStorage.getItem('CustomFields') == null || 
            window.localStorage.getItem('CustomFields') == ""    ){
            WF.reports.setCustomFields("{{ $params['Fields'] }}");
            WF.reports.set('CustomResetFields', "{{ $params['Fields'] }}");
        } else {
            WF.reports.setCustomFields(window.localStorage.getItem('CustomFields'));
            WF.reports.set('CustomResetFields', window.localStorage.getItem('CustomFields'));
        }
        $(window).load(function(){
           window.setTimeout( WF.reports.showCustomTableData, 1000 ); // 5 seconds
        });
    </script>
    <!--SCRIPT-->
    
    
    @endsection
