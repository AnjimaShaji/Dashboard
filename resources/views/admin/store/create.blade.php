@extends('layouts.master')

    @section('content')
        <script type="text/javascript">
            var workshop_vns={!!$workshop_vn!!}.reverse();
        </script>
        <div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Create Dealer</h2>
            </div>
        </div>
   
        <div class="panel panel-default">
            <div class="panel-body">
                <form role="form" id="new_dealer" method="post" class="validate" onsubmit="return false;">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">Name</label> 
                            <input type="text" class="form-control" name="name"/>
                        </div>
                        <!-- <div class="form-group col-sm-6"> 
                            <label class="control-label">Email</label> 
                            <input type="email" class="form-control" name="email"/> 
                        </div> -->
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">Panda Code</label> 
                            <input type="text" class="form-control" name="panda_code"/> 
                        </div>
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">RSM</label>
                            <select class="form-control" id="rsm" name="rsm"> 
                                <option value="">Select an RSM</option>
                                @if(!empty($rsms))
                                @foreach ($rsms as $rsm)
                                <option value="{{ $rsm->id }}">{{ $rsm->name }}</option>
                                @endforeach
                                @endif
                            </select> 
                        </div>
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">State</label>
                            <input list="states" id="state" class="form-control" name="state">
                            <datalist id="states">
                                @if(!empty($states))
                                @foreach ($states as $state)
                                <option value="{{ $state->state }}">
                                @endforeach
                                @endif
                            </datalist>
                        </div>
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">Location</label>
                            <input list="locations" id="location" class="form-control" name="location">
                            <datalist id="locations">
                                @if(!empty($locations))
                                @foreach ($locations as $location)
                                <option value="{{ $location->location }}">
                                @endforeach
                                @endif
                            </datalist>
                        </div>
                        <div class="form-group col-sm-6"> 
                            <label class="control-label">Region</label>
                            <select class="form-control" id="region" name="region"> 
                                <option value="">Select a region</option>
                                <option value="North">North</option>
                                <option value="South">South</option>
                                <option value="East">East</option>
                                <option value="West">West</option>
                            </select> 
                        </div>
                    </div>
                    <br><hr><br>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group"> 
                                <label class="control-label">Print Virtual Number</label>
                                <input type="text" class="form-control" id="print" name="print" value="{{ $print_vn }}" readonly="true" />
                            </div>
                            <div class="form-group"> 
                                <label class="control-label" for="print_sales_numbers">Sales Number</label> 
                                <input type="text" class="form-control" id="print_sales_numbers" name="print_sales_numbers" value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" /> 
                            </div>
                            <div class="form-group"> 
                                <label class="control-label" for="print_service_numbers">Service Number</label> 
                                <input type="text" class="form-control" id="print_service_numbers" name="print_service_numbers"  value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" /> 
                            </div>
                            <div class="form-group"> 
                                <label class="control-label" for="print_others_numbers">Other Number</label> 
                                <input type="text" class="form-control" id="print_others_numbers" name="print_others_numbers" value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" /> 
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group"> 
                                <label class="control-label">Web Virtual Number</label>
                                <input type="text" class="form-control" id="web" name="web" value="{{ $web_vn }}" readonly="true" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="web_sales_numbers">Sales Number</label>
                                <input type="text" class="form-control" id="web_sales_numbers" name="web_sales_numbers" value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="web_service_numbers">Service Number</label>
                                <input type="text" class="form-control" id="web_service_numbers" name="web_service_numbers" value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="web_others_numbers">Other Number</label>
                                <input type="text" class="form-control" id="web_others_numbers" name="web_others_numbers" value="" placeholder="Enter 10 digit agent numbers seperated by comma(,) Eg:8767895342,9088767890" />
                            </div>
                        </div>
                    </div>
                    <br><hr><br>
                    <div class="row" id="workshop_container">
                    </div>
                    <div class="col-sm-12">
                        <div id="add_workshop" class="pull-left btn btn-blue btn-icon btn-icon-standalone btn-sm">
                            <i class="fa-plus"></i>
                            <span>Add Workshop</span>
                        </div>
                        <input type="number" name="workshop_counter" id="workshop_counter" value="1" hidden="true">
                    </div>
                    <br><hr><br>
                    <div class="form-group"> 
                        <button type="submit" id='save' class="btn btn-success">Save</button>
                        <button type="button" id="cancel" class="btn btn-grey">Cancel</button>
                        <button type="reset" id="reset" class="btn btn-white pull-right">Reset</button> 
                    </div>
                </form>
            </div>
        </div>
        
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("select","#location","#locations").selectBoxIt().on('open', function() {
                    // Adding Custom Scrollbar
                    $(this).data('selectBoxSelectBoxIt').list.perfectScrollbar();
                });
            });
        </script>

        <script src="../../assets/js/toastr/toastr.min.js"></script>
        <script src="/js/custom/create_dealer.js"></script>

    @endsection