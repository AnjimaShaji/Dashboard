@extends('layouts.master')

    @section('content')
    	<link rel="stylesheet" href="{{asset('libraries/xenon/js/select2/select2.css')}}"  type="text/css">
	    <link rel="stylesheet" href="{{asset('libraries/xenon/js/select2/select2-bootstrap.css')}}"  type="text/css">
        <style>
            table td a {
                color: darkblue;
                text-decoration: underline;
                font-size: 12px;
                color: grey;
            }
            th {
                text-align: center;
            }
            .no-data {
                text-align: center;
            }
        </style>
     <div class="page-title">
        <div class="title-env">
            <h1 class="title">Zero Call Stores</h1>
        </div>
    </div>               

                   
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
                    <div class="form-group">
                        <div class="col-xs-3 form-group">
                            <input type="text" value="{{ !empty($params['date_from']) ? $params['date_from']:''}}" name="date_from" id="date_from" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date From"/>
                        </div>
                        <div class="col-xs-3 form-group">
                            <input type="text" value="{{ !empty($params['date_to']) ? $params['date_to']:""}}" name="date_to" id="date_to" data-format="yyyy-mm-dd" class="form-control datepicker" size="15" data-end-date="+1" placeholder="Date To"/>
                        </div>
                    </div>
                </div>
                <div class="form-group ">
                    <label class="cbr-inline">
                        <button id="reset" class="btn btn-primary btn-single">Reset Filter</button>
                    </label>
                    <div class="form-group pull-right" style="margin-top: 9px;">
                        <button id="filter" class="btn btn-secondary btn-single">Filter</button>
                        <button class="btn btn-secondary btn-single" id="export">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Listing</h3>
            <div class="panel-options">                  
            </div>
        </div>
        <div class="panel-body">
            <div  class="table-responsive" data-pattern="priority-columns">
                <table class="table table-small-font table-bordered table-striped cl-table">
                <thead>
                    <tr>
                        <th>Store Code</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Virtual Number </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stores as $item)
                        <tr>
                            <td style="text-align: center;">{{$item->store_code}}</td>
                            <td style="text-align: center;">{{$item->store_name}}</td>
                            <td style="text-align: center;">{{$item->location}}</td>
                            <td style="text-align: center;">{{$item->sim_number}}</td>
                        </tr> 
                    @empty
                        <tr class="no-data">
                            <td colspan="12">No Data Available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <!--PAGINATION-->
        <div class="row">
            <div class="col-sm-12">
                <div class="tPages pull-right">
                    {{ $stores->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
        <!--PAGINATION-->
    </div>
    <script src="{{asset('libraries/xenon/js/datepicker/bootstrap-datepicker.js')}}"></script>
    <script>
        var url = '{{url('/'.strtolower(Auth::user()->role))}}';
        function queryString(){
            var pageParams = getUrlParams();
            var currentString = '';
            for (var i=0; i<pageParams.length; i++)
                {
                var paramVal = pageParams[i];
                if(paramVal || paramVal != '') {

                currentString += '&' + pageParams[i] + '=' + pageParams[paramVal];
                }
            }
            if(pageParams.length <1){
                var currentString = '';
            }else{
                currentString = '?'+currentString;
            }
            return currentString;
        }
        function getUrlParams()
        {
            var vars = [], pair;
            let urlString =  window.location.href;
            let paramString = urlString.split('?')[1];
            let queryString = new URLSearchParams(paramString);
            for (let pair of queryString.entries()) {
                vars.push(pair[0]);
                vars[pair[0]] = pair[1];
            }
            return vars;
        }
        function getFilterValues(){
            var qString = '?';
            if($('#date_from').length) {
                if($('#date_from').val() != '')
                    qString += '&date_from='+$('#date_from').val();
            }
            if($('#date_to').length) {
                if($('#date_to').val() != '')
                    qString += '&date_to='+$('#date_to').val();
            }
            return qString;
        }
        function filter(){
            window.location.href = url +'/zero-call-stores/'+ getFilterValues();
        }
        function resetFilter(){
            window.location.href = url +'/zero-call-stores';
        }
        function vnexport(){
            window.location.href = url +'/zero-call-stores/export' + getFilterValues();
        }
        $("#filter").click(filter);
        $("#reset").click(resetFilter);
        $("#export").click(vnexport);
    </script>
    @endsection

