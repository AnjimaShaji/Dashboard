@extends('layouts.master')

    @section('content')
        <?php
            use App\Http\Controllers\Admin\DealerController;
        ?>

        <div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Login Activity</h2>
                <p class="description"></p>
            </div>
        </div>

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
                <div class="form-group">
                    <div class="col-md-3 form-group">
                        <input type="text" value="{{ !empty($params['date_from']) ? $params['date_from']:""}}" name="date_from" id="date_from" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date From"/>
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="text" value="{{ !empty($params['date_to']) ? $params['date_to']:""}}" name="date_to" id="date_to" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date To"/>
                    </div>
                    <div class="col-md-3 form-group">
                        <button id="filter" class="btn btn-secondary btn-single">Filter</button>
                        <button id="export" class="btn btn-secondary btn-single">Export</button>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#dom" data-toggle="tab">
                    <span class="visible-xs"><i class="fa-home"></i></span> <span class="hidden-xs">Dom</span>
                </a>
            </li>
            <li>
                <a href="#rsm" data-toggle="tab">
                    <span class="visible-xs"><i class="fa-envelope-o"></i></span> <span class="hidden-xs">Rsm</span>
                </a>
            </li>
            <li>
                <a href="#dealer" data-toggle="tab">
                    <span class="visible-xs"><i class="fa-cog"></i></span> <span class="hidden-xs">Dealer</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="dom">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#domTable").dataTable();
                    });
                </script>
                <table class="table table-bordered table-striped" id="domTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <!-- <th>Action</th> -->
                            <th>Login Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logins as $login)
                            @if($login->role == 'DOM')
                                <tr>
                                    <td>{{ $login->name }}</td>
                                    <td>{{ $login->email }}</td>
                                    <td>{{ $login->count }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tab-pane" id="rsm">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#rsmTable").dataTable();
                    });
                </script>
                <table class="table table-bordered table-striped" id="rsmTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Login Count</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logins as $login)
                            @if($login->role == 'RSM')
                                <tr>
                                    <td>{{ $login->name }}</td>
                                    <td>{{ $login->email }}</td>
                                    <td>{{ $login->count }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tab-pane" id="dealer">
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#dealerTable").dataTable();
                    });
                </script>
                <table class="table table-bordered table-striped" id="dealerTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Panda code</th>
                            <th>Location</th>
                            <th>Login Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logins as $login)
                            @if($login->role == 'DEALER')
                                <tr>
                                    <td>{{ $login->name }}</td>
                                    <td>{{ $login->email }}</td>
                                    <td>{{ $login->panda_code }}</td>
                                    <td>{{ $login->location }}</td>
                                    <td>{{ $login->count }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script src="/libraries/xenon/js/datepicker/bootstrap-datepicker.js"></script>
        <script type="text/javascript">
            $('#filter').click(function() {
                var qString = '';
                if ($('#date_from').val() != '' && $('#date_to').val() != '') {
                    qString += '?date_from='+$('#date_from').val()+'&date_to='+$('#date_to').val();
                } else if($('#date_from').val() != '') {
                    qString += '?date_from='+$('#date_from').val()+'&date_to=';
                } else {
                    return;
                }
                window.location = '/{{ strtolower(Auth::user()->role) }}/activity-log' + qString;
            });
            $('#export').click(function() {
                var qString = '';
                if ($('#date_from').val() != '' && $('#date_to').val() != '') {
                    qString += '?date_from='+$('#date_from').val()+'&date_to='+$('#date_to').val();
                } else if($('#date_from').val() != '') {
                    qString += '?date_from='+$('#date_from').val()+'&date_to=';
                } else {
                    return;
                }
                window.location = '/{{ strtolower(Auth::user()->role) }}/activity-export' + qString;
            });
        </script>
    @endsection