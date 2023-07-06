@extends('layouts.master')
@section('content')
    <style>
        .error {
            color: red;
        }

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
            <h1 class="title">Edit Store</h1>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="pull-right" style="padding-right: 30px;">
            <!-- <p>Email: <b><span id="login_email">{{ @$store->store_code }}</span>@sotc.com</b>, &nbsp; Password:
                <b>{{ 'sotc.store#waybeo@'.@$store->store_user_id.'!' }}</b>
            </p> -->
        </div>
        <div class="panel-body">

            <form role="form" id="update_store" method="post">
                <input type="hidden" value="{{ @$store->id }}" name="storeId" id="id" />
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="control-label">Store Code</label>
                        <input type="text" class="form-control" name="store_code" id="store_code"
                            value="{{ @$store->actual_store_id }}" disabled />
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ @$store->store_name }}" />
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label">Store Email</label>
                        <input type="text" class="form-control" name="email" id="email" value="{{ @$store->store_email }}" />
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label">Address</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ @$store->address }}" />
                    </div>
                  

                    <div class="form-group col-sm-6"> 
                        <label class="control-label">State</label>
                        <select id="state" class="form-control" name="state">
                            <option value="">-- select --</option>
                            @if(!empty($states))
                            @foreach ($states as $state)
                            <option value="{{ @$store->state_id  }}" 
                                {{ (@$store->state_id == $state->id?'selected': '') }}
                                >
                                {{ $state->state }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-sm-6"> 
                        <label class="control-label">City</label>
                        <select class="form-control form-control-sm validate" id="city">
                            @if(!empty($cities))
                            <option value="" selected>Select City</option>
                            @foreach ($cities as $city)
                            <option value="{{ @$store->city_id }}" 
                            {{ (@$store->city_id == $city->id?'selected': '') }}
                            >{{ $city->city }}</option>
                            @endforeach
                            @endif
                        </select>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="control-label">Location</label>
                        <input type="text" class="form-control" name="location" id="location" value="{{ @$store->location }}" />
                    </div>
                </div>

                <br>
                <hr><br>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Virtual Number</label>
                            <input type="text" class="form-control" id="sim_number" name="sim_number" value="{{ @$store->sim_number }}"
                                readonly="true" />
                        </div>

                    </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                            <label class="control-label" for="store_numbers">Store Numbers</label>
                             @php
                                
                                $sno = implode(',', json_decode(str_replace('+91', '', $store->store_numbers), TRUE));
                                
                            @endphp
                            <input type="text" class="form-control" id="store_numbers" name="store_numbers" value="{{ $sno }}" />
                        </div>
                    </div>

                </div>

                <!--  Working Hours -->
                    <div class="form-group" style="margin-top: 20px;">
                        <div class="row col-sm-12">
                            <label class="col-sm-4 control-label" style="padding-top: 0px;" for="field-1">Working Hours
                            </label>
                            <span style="margin-left: 15px;"><input class="" id="edit_all_days" type="radio" name="edithours" value="edit_all_days">All days</span>
                            <span style="margin-left: 20px;"><input class="" id="edit_custom_days" type="radio" name="edithours" value="edit_custom_days">Custom</span>
                        </div>
                        <div>
                            
                        </div>
                    </div>
                    <div id="editAllDays">
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px; margin-bottom: 25px;">
                                 <label class="col-sm-3 control-label" for="field-1">All</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="all_wf" placeholder="Enter Time" name="all_st" value="09:30">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="all_wt" placeholder="Enter Time" name="all_et" value="21:30">
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div id="editCustom" >
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                 <label class="col-sm-3 control-label" for="field-1">Sunday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="sun_wf" placeholder="Enter Time" name="s_st" disabled>
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="sun_wt" placeholder="Enter Time" name="s_et"  disabled>
                                </div>
                                <div class="col-sm-1  control-label">                                         
                                    <input id="sun_check" class="check" type="checkbox" value="1" class="form-control" name="s_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                <label class="col-sm-3 control-label" for="field-1">Monday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="mon_wf" placeholder="Enter Time" name="m_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="mon_wt" placeholder="Enter Time" name="m_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="mon_check" class="check" type="checkbox" value="1" class="form-control" name="m_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                <label class="col-sm-3 control-label" for="field-1">Tuesday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="tue_wf" placeholder="Enter Time" name="t_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="tue_wt" placeholder="Enter Time" name="t_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="tue_check" class="check" type="checkbox" value="1" class="form-control" name="t_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                <label class="col-sm-3 control-label" for="field-1">Wednesday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="wed_wf" placeholder="Enter Time" name="w_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="wed_wt" placeholder="Enter Time" name="w_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="wed_check" class="check" type="checkbox" value="1" class="form-control" name="w_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                <label class="col-sm-3 control-label" for="field-1">Thursday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="thu_wf" placeholder="Enter Time" name="th_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="thu_wt" placeholder="Enter Time" name="th_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="thu_check" class="check" type="checkbox" value="1" class="form-control" name="th_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px;">
                                <label class="col-sm-3 control-label" for="field-1">Friday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="fri_wf" placeholder="Enter Time" name="f_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="fri_wt" placeholder="Enter Time" name="f_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="fri_check" class="check" type="checkbox" value="1" class="form-control" name="f_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_val" style="margin-top: 20px; margin-bottom: 25px;">
                                <label class="col-sm-3 control-label" for="field-1">Saturday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val1 val" id="sat_wf" placeholder="Enter Time" name="sa_st" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control val2 val" id="sat_wt" placeholder="Enter Time" name="sa_et" disabled="">
                                </div>
                                <div class="col-sm-1  control-label"> 
                                    <input id="sat_check" class="check" type="checkbox" class="form-control" name="sa_check">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="{{ @$store->working_hours }}" id="workhrs">
                    
                </div>
        </div>

        <div class="row">

        </div>
        <div class="form-group">
            <button type="submit" id="update" class="btn btn-success">Update</button>
            <button type="button" id="cancel" id="cancel" class="btn btn-grey">Cancel</button>
            <!-- <button type="button" id="delete" id="cancel" class="btn btn-red pull-right">Delete</button> -->
            <button id="reset" type="reset" class="btn btn-primary btn-single pull-right">Reset</button>
            {{-- <button type="reset" id="reset" class="btn btn-white pull-right">Reset</button> --}}
        </div>

        </form>
    </div>
    </div>
    </div>

<script src="/assets/js/toastr/toastr.min.js"></script>
<link rel="stylesheet" href="/libraries/xenon/js/select2/select2.css" type="text/css">
<link rel="stylesheet" href="/libraries/xenon/js/select2/select2-bootstrap.css" type="text/css">
<script src="/libraries/xenon/js/select2/select2.min.js"></script>
<script>
    var url = "{{ '/' . strtolower(Auth::user()->role) . '/store' }}";
    $("#cancel").click(function() {
        window.location.href = url;
    });
    $.validator.addMethod("stValid", function(value, element) {
        status = parallel_number_validation(value);
        return ten_digit_check(status);
    }, "Please enter valid  10 digit numbers separated by ','");
    $.validator.addMethod("count", function(value, element) {
        return parallel_number_count(value);
    }, "Please enter 4 valid  10 digit numbers separated by ','");

    /* Working Hours */
    $(document).ready(function(){
        var wk = $("#workhrs").val();
        var w_hours =JSON.parse(wk);
            $.each(w_hours, function(key, value) {
                var workingFrom = value[0].working_from;
                var workingTo = value[0].working_to;
                var s_wf = workingFrom.replace(/\B(?=(\d{2})+(?!\d))/g, ":");
                var s_wt = workingTo.replace(/\B(?=(\d{2})+(?!\d))/g, ":");
                
                if(key == 'all'){
                    $('input:radio[name="edithours"]').filter('[value="edit_all_days"]').attr('checked', true);
                    $("#editAllDays").show();
                    $("#editCustom").hide();
                    $("#" + key +"_wf").val(s_wf);
                    $("#" + key +"_wt").val(s_wt);
                }else{
                    $('input:radio[name="edithours"]').filter('[value="edit_custom_days"]').attr('checked', true);
                    $("#editCustom").show();
                    $("#editAllDays").hide();
                    $("#" + key +"_wf").val(s_wf);
                    $("#" + key +"_wt").val(s_wt);
                    $("#" + key +"_wf").removeAttr("disabled");
                    $("#" + key +"_wt").removeAttr("disabled");
                    $("#" + key +"_check").prop("checked", true);
                }
            });
    });

    $("#all_days").on('click', function(event) {
        $("#allDays").show();
        $("#weekDays").hide();
    });

    $("#week_days").on('click', function(event) {
        $("#weekDays").show();
        $("#allDays").hide();
    });

    $("#edit_all_days").on('click', function(event) {
        $("#editAllDays").show();
        $("#editCustom").hide();
    });
    $("#edit_custom_days").on('click', function(event) {
        $("#editCustom").show();
        $("#editAllDays").hide();
    });

    jQuery(document).ready(function ($) {
        $("#state,#city").select2({
            allowClear: true
        });
    });


    $("#update").on('click',function(event) {
        event.preventDefault();

        var id = $('#id').val();
        var name = $('#name').val();
        var email = $('#email').val();
        var address = $('#address').val();
        var state = $('#state').val();
        var city = $('#city').val();
        var location = $('#location').val();
        var sim_number = $('#sim_number').val();
        var store_numbers = $('#store_numbers').val();

        var s_st = $('#s_st').val();
        var s_et = $('#s_et').val();
        var m_st = $('#m_st').val();
        var m_et = $('#m_et').val();

        var error = 0;

        if ($('#name').val() == '') {
            toastr.error("Please enter the store name");
            $("#name").css('border-color', 'red').focus();
            error++;
        }else{
            $("#name").css('border-color', '#e4e4e4').focus();
        }

        if ($('#email').val() == '') {
            toastr.error("Please enter store email");
            $("#email").css('border-color', 'red').focus();
            error++;
        }else{
            $("#email").css('border-color', '#e4e4e4').focus();
        }
        if ($('#address').val() == '') {
            toastr.error("Please enter store address");
            $("#address").css('border-color', 'red').focus();
            error++;
        }else{
            $("#address").css('border-color', '#e4e4e4').focus();
        }

        if ($('#state').val() == '') {
            toastr.error("Please select state");
            $("#state").css('border-color', 'red').focus();
            error++;
        }else{
            $("#state").css('border-color', '#e4e4e4').focus();
        }

        if ($('#city').val() == '') {
            toastr.error("Please select city");
            $("#city").css('border-color', 'red').focus();
            error++;
        }else{
            $("#city").css('border-color', '#e4e4e4').focus();
        }

        if ($('#location').val() == '') {
            toastr.error("Please enter location");
            $("#location").css('border-color', 'red').focus();
            error++;
        }else{
            $("#location").css('border-color', '#e4e4e4').focus();
        }


        if ($('#store_numbers').val() == '') {
            toastr.error("Please enter store number");
            $("#store_numbers").css('border-color', 'red').focus();
            error++;
        }else{
            $("#store_numbers").css('border-color', '#e4e4e4').focus();
            var storeNumbersSplit = store_numbers.replace(/\s/g, "").split(/,|;/);
            var storeNumbers = [];
            $.each(storeNumbersSplit, function(key,number) {
                if (number.length != 10){
                    storeNumbers.push(number);
                    error++;
                }
            });

            if(storeNumbers.length){
                 toastr.error(storeNumbers + " invalid store number");
                 $("#store_numbers").css('border-color', 'red').focus();
                setTimeout(function(){
                    $('.error_msg').fadeOut();
                },5000);
            } 
        }
        
        
        if(error) {
            $("#update").attr("disabled", false);
        } 

        if(!error){
            var w_hours = {};
            if($("input[name=edithours]:checked").val() == 'edit_all_days'){
                var aat = $("#all_wf").val();
                var sae = $("#all_wt").val();
                var all_st = aat.replace(/\:/g,'');
                var all_et = sae.replace(/\:/g,'');
                
                if(all_st == '' || all_et == ''){
                    all_st = '0000';
                    all_et = '0000';
                }
                var all = [{ 
                    working_from: all_st,
                    working_to: all_et
                }];               
                w_hours["all"] = all;
            }
            
            if($("input[name=edithours]:checked").val() == 'edit_custom_days'){
                if ($('#sun_check').is(':checked')) {
                    var st = $("#sun_wf").val();
                    var s_st = st.replace(/\:/g,'')
                    var se = $("#sun_wt").val();
                    var s_et = se.replace(/\:/g,'')
                    if(s_st == '' || s_et == ''){
                        s_st = '0000';
                        s_et = '0000';
                    }
                    var sunday = [{ 
                        working_from: s_st,
                        working_to: s_et
                    }];
                    w_hours["sun"] = sunday;
                }

                if ($('#mon_check').is(':checked')) {
                    var mt = $("#mon_wf").val();
                    var m_st = mt.replace(/\:/g,'')
                    var me = $("#mon_wt").val();
                    var m_et = me.replace(/\:/g,'')
                    if(m_st == '' || m_et == ''){
                        m_st = '0000';
                        m_et = '0000';
                    }
                    var monday = [{ 
                        working_from: m_st,
                        working_to: m_et
                    }];
                    
                    w_hours["mon"] = monday;
                }
                if ($('#tue_check').is(':checked')) {
                    var tt = $("#tue_wf").val();
                    var t_st = tt.replace(/\:/g,'')
                    var te = $("#tue_wt").val();
                    var t_et = te.replace(/\:/g,'')
                    if(t_st == '' || t_et == ''){
                        t_st = '0000';
                        t_et = '0000';
                    }
                    var tuesday = [{ 
                            working_from: t_st,
                            working_to: t_et
                    }];
                    w_hours["tue"] = tuesday;
                }
                if ($('#wed_check').is(':checked')) {
                    var wt = $("#wed_wf").val();
                    var w_st = wt.replace(/\:/g,'')
                    var we = $("#wed_wt").val();
                    var w_et = we.replace(/\:/g,'')
                    if(w_st == '' || w_et == ''){
                        w_st = '0000';
                        w_et = '0000';
                    }
                    var wednesday = [{ 
                        working_from: w_st,
                        working_to: w_et
                    }];               
                    w_hours["wed"] = wednesday;
                }
                if ($('#thu_check').is(':checked')) {
                    var th = $("#thu_wf").val();
                    var th_st = th.replace(/\:/g,'')
                    var the = $("#thu_wt").val();
                    var th_et = the.replace(/\:/g,'')
                    if(th_st == '' || th_et == ''){
                        th_st = '0000';
                        th_et = '0000';
                    }
                    var thursday =  [{ 
                        working_from: th_st,
                        working_to: th_et
                    }];
                    w_hours["thu"] = thursday;
                }
                if ($('#fri_check').is(':checked')) {
                    var ft = $("#fri_wf").val();
                    var f_st = ft.replace(/\:/g,'')
                    var fe = $("#fri_wt").val();
                    var f_et = fe.replace(/\:/g,'')
                    if(f_st == '' || f_et == ''){
                        f_st = '0000';
                        f_et = '0000';
                    }
                    var friday =  [{ 
                        working_from: f_st,
                        working_to: f_et
                    }];      
                    w_hours["fri"] = friday;
                }
                if ($('#sat_check').is(':checked')) {
                    var sat = $("#sat_wf").val();
                    var sa_st = sat.replace(/\:/g,'')
                    var sae = $("#sat_wt").val();
                    var sa_et = sae.replace(/\:/g,'')
                    if(sa_st == '' || sa_et == ''){
                        sa_st = '0000';
                        sa_et = '0000';
                    }
                    var saturday = [{ 
                        working_from: sa_st,
                        working_to: sa_et
                    }];               
                    w_hours["sat"] = saturday;
                }
            }    
            var w = JSON.stringify(w_hours);

            var data = { 
                id:id,
                name : $('#name').val(),
                email : $('#email').val(),
                address : $('#address').val(),
                state : $('#state').val(),
                city : $('#city').val(),
                location : $('#location').val(),
                sim_number : $('#sim_number').val(),
                actual_store_id : $('#store_code').val(),
                store_numbers : storeNumbersSplit,
                working_hours : w, 
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                url: '/admin/store/update',
                type: 'POST',
                data: data,
                success: function(data) {
                    console.log(data);
                    if (data.status == true) {
                        toastr.success("Update Successful","Update Successful");
                        setTimeout(function() {
                            window.location = window.location;
                        }, 2000);
                    } else {
                        toastr.error("Please try after some time", "Create Failed");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        }

    });




    function ten_digit_check(element) {
        if (element > 0) {
            return false
        } else {
            return true;
        }
    }

    function reInitializeFilters(filters) {
        $.each(filters, function(filterId, filterPlaceHolder) {
            $("#" + filterId).select2({
                placeholder: filterPlaceHolder,
                allowClear: false
            });
        });
    }

</script>
@endsection
