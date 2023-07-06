@extends('layouts.master')

    @section('content')                                
        <div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Call Details</h2>
                <p class="description">Detailed Call Report</p>
            </div>
        </div>


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Caller</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <col width="60">
                        <col width="80">
                        <tbody>
                            <tr>
                                <th>Customer Number</th>
                                <td>{{ $callDetails['CallerId'] }}</td>
                            </tr>
                            <tr>
                                <th>Region</th>
                                <td>{{ $callDetails['Region'] }}</td>
                            </tr>
                            <tr>
                                <th>Repeated Caller</th>
                                <td>{{ (empty($callDetails['RepeatedCaller'])?'Unique':'Repeat') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div><br><br>
                <div class="panel-heading">
                    <h3 class="panel-title">Dealer</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <col width="60">
                        <col width="80">
                        <tbody>
                            <tr>
                                <th>Dealer Name</th>
                                @if (!empty($callDetails['Dealer']))                                
                                    <td>{{ $callDetails['Dealer'] }}</td>                                
                                @else                                
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Panda Code</th>
                                @if (!empty($callDetails['PandaCode']))                                
                                    <td>{{ $callDetails['PandaCode'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Location</th>
                                @if (!empty($callDetails['Location']))                                
                                    <td>{{ $callDetails['Location'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Region</th>
                                @if (!empty($callDetails['Region']))                                
                                    <td>{{ $callDetails['Region'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>VN Type</th>
                                @if (!empty($callDetails['Type']))                                
                                    <td>{{ $callDetails['Type'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Virtual Number</th>
                                @if (!empty($callDetails['MaskedNumber']))                                
                                    <td>{{ $callDetails['MaskedNumber'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>RSM</th>
                                @if (!empty($callDetails['Rsm']))                                
                                    <td>{{ $callDetails['Rsm'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>DOM</th>
                                @if (!empty($callDetails['Dom']))                                
                                    <td>{{ $callDetails['Dom'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div><br><br>
                <div class="panel-heading">
                    <h3 class="panel-title">Call</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <col width="60">
                        <col width="80">
                        <tbody>
                            <tr>
                                <th>Call Id</th>
                                @if (!empty($callDetails['CallId']))                                
                                    <td>{{ $callDetails['CallId'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Call Start Time</th>
                                @if (!empty($callDetails['DateTime']))                                
                                    <td>{{ date('j/M/y, g:i:s A', strtotime($callDetails['DateTime'])) }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Call End Time</th>
                                <?php
                                $endTime = null;
                                if(!empty($callDetails['DateTime'])) {
                                    $date = strtotime($callDetails['DateTime']);
                                    $duration = empty($callDetails['TotalDuration'])? 0: $callDetails['TotalDuration'];
                                    $endTime = $date + $duration;
                                }
                                ?>
                                @if (!empty($callDetails['DateTime']) && $endTime)     
                                    <td>{{ date('j/M/y, g:i:s A', $endTime) }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Status</th>
                                @if (!empty($callDetails['Status']))                                
                                    <td>{{ $callDetails['Status'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Conversation Duration</th>
                                @if (!empty($callDetails['ConversationDuration']))                                
                                    <td>{{ $callDetails['ConversationDuration'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>IVR Duration</th>
                                @if (!empty($callDetails['IVRDuration']))                                
                                    <td>{{ $callDetails['IVRDuration'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Ring Duration</th>
                                @if (!empty($callDetails['RingDuration']))                                
                                    <td>{{ $callDetails['RingDuration'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Callee Leg Status</th>
                                @if (!empty($callDetails['CalleeLegStatus']))                                
                                    <td>{{ $callDetails['CalleeLegStatus'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Hangup Leg</th>
                                @if (!empty($callDetails['HangupLeg']))
                                <td>{{ $callDetails['HangupLeg'] }}</td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                            <tr>
                                <th>Busy Callees</th>
                                
                                @if (!empty($callDetails['BusyCallees']))
                                    <td>
                                    @foreach ($callDetails['BusyCallees'] as $callee)
                                    {{ $callee.', ' }}
                                    @endforeach
                                    </td>
                                @else
                                    <td> --- </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="panel-body">
                    <table class="table table-hover">
                        <col width="60">
                        <col width="80">
                        <tbody>
                                <tr>
                                    <td class="col-md-3 Bold">Call Recording</td>
                                    <td>
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <audio id="audio-dummyAudId" controls>
                                               <source src="{{ @$callDetails['CallRecordUrl'] }}" type="audio/x-wav"/>
                                            </audio>
                                        </div>
                                        <div class="form-group pull-right">
                                            <a href="{{ @$callDetails['CallRecordUrl'] }}" >
                                                <button class="btn btn-primary btn-icon btn-icon-standalone">
                                                    <i class="fa-download"></i> <span>Download File</span>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                <br><br>
            </div>
                                
    @endsection