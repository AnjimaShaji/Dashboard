@extends('layouts.master')

@section('content')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
    <style type="text/css">
        th,
        td {
            white-space: nowrap;
        }

        .panel-body {
            overflow: hidden;
        }

        #create_new {
            margin-bottom: 0;
            margin-top: 15px;
            margin-left: 1000px;
        }

        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 40%;
            width: 40px;
            height: 40px;
            border-style: solid;
            border-color: black;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

    </style>
    <div class="page-title">
        <div class="title-env">
            <h2 class="title">stores</h2>

        </div>
    </div>


    <div class="">
        <div class="panel panel-default">
            <div class="breadcrumb-env">
                <button type="button" class="btn btn-secondary btn-icon btn-icon-standalone"
                    onclick="javascript:_showEditModal()" id="create_new">
                    <i class="fa fa-clock-o"></i>
                    <span>Master Working Hours</span>
                </button>

            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped" id="example-1">
                    <thead>
                        <tr>
                            <th>Store Code</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Cluster</th>
                            <th style="width:150px;">Region</th>
                            <th>Asm</th>
                            <th>Rm</th>
                            <th style="width:150px;">Back End Number</th>
                            <th>Parallel Numbers</th>
                            <th style="width:150px;">Escalation Number1</th>
                            <th style="width:150px;">Escalation Number2</th>
                            @if (Auth::user()->role == 'ADMIN'  && in_array(Auth::user()->id ,[1,87,88]))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stores as $store)
                            <tr class="store" id="{{ $store->id }}">
                                <td>{{ $store->store_code }}</td>
                                <td>{{ $store->type }}</td>
                                <td>{{ $store->location }}</td>
                                <td>{{ $store->cluster }}</td>
                                <td style="width:150px;">{{ $store->region }}</td>
                                <td>{{ $store->asm }}</td>
                                <td>{{ $store->rm }}</td>
                                <td style="width:100px;">{{ $store->vn }}</td>
                                <td>{{ @$store->p1_number }} @if (!empty($store->p2_number))
                                        ,{{ @$store->p2_number }}
                                        @endif @if (!empty($store->p3_number))
                                            ,{{ @$store->p3_number }}
                                            @endif @if (!empty($store->p4_number))
                                                ,{{ @$store->p4_number }}
                                            @endif
                                </td>
                                <td style="width:150px;">{{ $store->e1_number }}</td>
                                <td style="width:150px;">{{ $store->e2_number }}</td>



                                @if (Auth::user()->role == 'ADMIN'  && in_array(Auth::user()->id ,[1,87,88]))
                                    <td><a href="{{ url('/'.strtolower(Auth::user()->role).'/store/edit/' . $store->id) }}"> <i
                                                class="fa-edit"></i></a>
                                @endif
                                {{-- <div class="btn-group"> 
                                            <button type="button" class="btn btn-blue dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Action <span class="caret"></span> 
                                            </button> 
                                            <ul class="dropdown-menu dropdown-blue" role="menu"> 
                                                <li> <a href="#">Update</a> </li> 
                                                <li> <a href="#">Deactivate</a> </li>
                                                <li class="divider"></li>
                                                <li> <a href="#">Delete</a> </li> 
                                            </ul>  
                                        </div> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" id="rsm_id" value="">
    <script src="/assets/js/toastr/toastr.min.js"></script>
    <script type="text/javascript">
        var keys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        $(document).ready(function($) {

            $("#example-1").dataTable({
                aLengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: 'Bfrtip',
                scrollX: true,
                buttons: [{
                        extend: 'csv',
                        footer: true,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 6, 7, 8, 9, 10]
                        }
                    }

                ],
                "scrollX": true,
                "fnDrawCallback": function(oSettings) {
                    $(".dataTables_scrollBody th").removeAttr('class');
                }
            });
            $(".dataTables_scrollBody th").removeAttr('class');
            $("div.toolbar").html(
                '<button id="export" class="btn btn-blue btn-single pull-right" style="margin-left:10px;">Export</button>'
            );

            $(".buttons-csv").text("Export");
            $(".buttons-csv").css('background', 'green');
            $(".buttons-csv").css('color', 'white');
            $(".dataTables_scrollBody th").removeAttr('class');
            $('#example-1_length').addClass('pull-left');
            $('#example-1_filter').addClass('pull-right');
        });
        $("#edit_all_days").on('click', function(event) {
            $("#editAllDays").show();
            $("#editCustom").hide();
        });

        $("#edit_custom_days").on('click', function(event) {
            $("#editCustom").show();
            $("#editAllDays").hide();
        });

        function _showEditModal() {
            $('#modalWorkingHours').modal('show', {
                backdrop: 'static'
            });
            $("#all_wf,#all_wt,#sun_wf,#sun_wt,#mon_wf,#mon_wt,#tue_wt,#tue_wf,#wed_wf,#wed_wt,#thu_wf,#thu_wt,#fri_wf,#fri_wt,#sat_wf,#sat_wt")
                .val(null);
            $('.checkk').prop('checked', false);
            $('.va').css("border", "");
            $('.vali').css("border", "");
            $('input:radio[name="edithours"]').filter('[value="edit_all_days"]').attr(
                'checked', true);
            $("#editAllDays").show();
            $("#editCustom").hide();
            $("#edit_all_days").prop("checked", true);
            $("#edit_custom_days").prop("checked", false);

        }
        $('input:radio[name="edithours"]').change(function() {
            let val = $(this).val();

            if (val == "edit_all_days") {
                $("#editAllDays").show();
                $("#editCustom").hide();
            } else if (val == "edit_custom_days") {
                $("#editAllDays").hide();
                keys.forEach(key => {
                    console.log(key);
                    // $("#" + key + "_wf").removeAttr("disabled");
                    // $("#" + key + "_wt").removeAttr("disabled");
                    $("#" + key + "_wf").attr("disabled", true);
                    $("#" + key + "_wt").attr("disabled", true);
                    // $("#" + key + "_check").prop("checked", false);
                });

                $("#edit_custom_days").prop("checked", true);
                $("#editCustom").show();

            }
            // alert($(this).val());
        });
        $('.checkk').on('change', function() {
            if ($(this).is(":checked")) {
                $(this).parent().parent().children().find('.vali1').attr("disabled", false);
                $(this).parent().parent().children().find('.vali2').attr("disabled", false);
            } else if (!this.checked) {
                $(this).parent().parent().children().find('.vali1').val('') && $(this).parent().parent().children()
                    .find('.vali1').attr("disabled", true);
                $(this).parent().parent().children().find('.vali2').val('') && $(this).parent().parent().children()
                    .find('.vali2').attr("disabled", true);
            }

        });

        $('#editChanges').click(function() {
            var error = 0;
            if ($('input[class="ewhours"]:checked').length == 0) {
                toastr.error("Please Fill up the Working Hours");
                error++;
            }

            if ($('input[class="ewhours"]:checked').val() == 'edit_all_days') {
                if ($("#all_wf").val() >= $("#all_wt").val()) {
                    toastr.error("Please enter a valid time");
                    error++;

                } else {
                    $("#all_wf,#all_wt").css("border-color", '');

                }
            }

            $(".time_vali").each(function() {
                if (($(this).find('.vali1').val() >= $(this).find('.vali2').val()) && $(this).find(
                        '.checkk').is(":checked")) {
                    $(this).find('.vali1,.vali2').css("border-color", 'red');
                    toastr.error("Please enter a valid time");
                    error++;

                } else {
                    $(this).find('.vali1,.vali2').css("border-color", '');

                }
            });

            if (!error) {
                var w_hours = {};

                if ($("input[name=edithours]:checked").val() == 'edit_all_days') {
                    var aat = $("#all_wf").val();
                    var sae = $("#all_wt").val();
                    var all_st = aat.replace(/\:/g, '');
                    var all_et = sae.replace(/\:/g, '');

                    if (all_st == '' || all_et == '') {
                        all_st = '0000';
                        all_et = '0000';
                    }
                    var all = [{
                        working_from: all_st,
                        working_to: all_et
                    }];
                    w_hours["all"] = all;
                }

                if ($("input[name=edithours]:checked").val() == 'edit_custom_days') {
                    keys.forEach(key => {
                        if ($('#' + key + '_check').is(':checked')) {
                            var st = $("#sun_wf").val();
                            var s_st = st.replace(/\:/g, '')
                            var se = $("#" + key + "_wt").val();
                            var s_et = se.replace(/\:/g, '')
                            if (s_st == '' || s_et == '') {
                                s_st = '0000';
                                s_et = '0000';
                            }
                            var day = [{
                                working_from: s_st,
                                working_to: s_et
                            }];
                            w_hours[key] = day;

                        }
                    });
                }

                var w = JSON.stringify(w_hours);

                var data = {
                    working_hours: w,
                    "_token": "{{ csrf_token() }}"
                };
                $.ajax({
                    url: "/{{strtolower(Auth::user()->role)}}/store/work-hours",
                    type: 'POST',
                    data: data,
                    beforeSend: function() {
                        $(".loader").show();
                    },
                    success: function(response) {
                        if (response.status === true) {

                            toastr.success("Update Successful", "Update Successful");
                            setTimeout(function() {
                                window.location = window.location;
                            }, 2000);
                        } else {
                            toastr.error("Please try after some time", "Update Failed");
                        }
                    },
                    complete: function(data) {
                        $('#cover-spin').hide();
                    }
                });
            } else {
                $('#cover-spin').hide();
            }

        });
    </script>
@endsection
@section('modals')
    <div id="modalWorkingHours" class="modal fade">
        <div id="cover-spin"></div>
        <div class="modal-dialog" style="width:50%">
            <div class="modal-content">
                <div class="modal-header">
                    <span></span> Create Region
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                </div>
                <form id="addregion_form" method="post" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="row col-sm-12">
                            <label class="col-sm-4 control-label" style="padding-top: 0px;" for="field-1">Working
                                Hours</label>
                            <span style="margin-left: 15px;"><input class="ewhours" id="edit_all_days" type="radio"
                                    name="edithours" value="edit_all_days">All days</span>
                            <span style="margin-left: 20px;"><input class="ewhours" id="edit_custom_days"
                                    type="radio" name="edithours" value="edit_custom_days">Custom</span>
                        </div>
                        <div>

                        </div>
                    </div>
                    {{-- <div class="form-group-separator"></div> --}}
                    <div id="editAllDays">
                        <div class="form-group">
                            <div class="row col-sm-12 time_alli">
                                <label class="col-sm-3 control-label" for="field-1">All</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vai1 va val" id="all_wf" placeholder="Enter Time"
                                        name="all_st">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vai2 va val" id="all_wt" placeholder="Enter Time"
                                        name="all_et">
                                </div>
                                <!-- <div class="col-sm-1  control-label">
                                                <input id="all_check" class="check" type="checkbox" value="1" class="form-control" name="all_check">
                                            </div> -->
                            </div>
                        </div>
                    </div>
                    <div id="editCustom">
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Sunday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="sun_wf" placeholder="Enter Time"
                                        name="sun_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="sun_wt" placeholder="Enter Time"
                                        name="sun_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="sun_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="sun_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Monday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="mon_wf" placeholder="Enter Time"
                                        name="mon_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="mon_wt" placeholder="Enter Time"
                                        name="mon_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="mon_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="mon_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Tuesday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="tue_wf" placeholder="Enter Time"
                                        name="tue_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="tue_wt" placeholder="Enter Time"
                                        name="tue_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="tue_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="tue_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Wednesday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="wed_wf" placeholder="Enter Time"
                                        name="wed_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="wed_wt" placeholder="Enter Time"
                                        name="wed_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="wed_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="wed_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Thursday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="thu_wf" placeholder="Enter Time"
                                        name="thu_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="thu_wt" placeholder="Enter Time"
                                        name="thu_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="thu_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="thu_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Friday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="fri_wf"
                                        placeholder="Enter Time" name="fri_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="fri_wt"
                                        placeholder="Enter Time" name="fri_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="fri_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="fri_check">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-separator"></div>
                        <div class="form-group">
                            <div class="row col-sm-12 time_vali">
                                <label class="col-sm-3 control-label" for="field-1">Saturday</label>
                                <label class="col-sm-1 control-label" for="field-1">From</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali1 va wf" id="sat_wf"
                                        placeholder="Enter Time" name="sat_wf" disabled="">
                                </div>
                                <label class="col-sm-1 control-label" for="field-1">To</label>
                                <div class="col-sm-3">
                                    <input type="time" class="form-control vali2 va wt" id="sat_wt"
                                        placeholder="Enter Time" name="sat_wt" disabled="">
                                </div>
                                <div class="col-sm-1  control-label">
                                    <input id="sat_check" class="checkk" type="checkbox" value="1"
                                        class="form-control" name="sat_check">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding-top: 10px; border-top: 0;padding-right:60%;">
                        <button type="button" class="btn btn-info" name="editChanges" id="editChanges">Save
                            changes</button>
                        <button data-dismiss="modal" class="btn btn-white" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
