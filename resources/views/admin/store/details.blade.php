@extends('layouts.master')

    @section('content')

        <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>

        <style type="text/css">
        .dom {
            cursor: pointer;
        }
        .dataTables_scrollBody thead tr[role="row"]{
               visibility: collapse !important;
            }
        </style>
        <div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Store</h2>
            </div>
        
            <div class="breadcrumb-env">
                <button class="btn btn-secondary btn-icon btn-icon-standalone" id="create_store" align="right" >  
                    <i class="fa-plus"></i> 
                    <span>Add Store</span>
                </button>
            </div>
        </div>
        <div class="">
            <div class="panel panel-default">
                <div class="panel-body">
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $("#example-1").dataTable({
                                lengthMenu: [
                                    [10, 25, 50, 100, -1],
                                    [10, 25, 50, 100, "All"]
                                ],
                                "oSearch": {"bSmart": false},
                                "scrollX": true,
                                 dom: 'Bfrtip',
                                buttons: ['csv'],
                                "fnDrawCallback": function( oSettings ) {
                                    $(".dataTables_scrollBody th").removeAttr('class');
                                }
                            });
                            $(".buttons-csv").text("Export");
                            $(".buttons-csv").css('background','green');
                            $(".buttons-csv").css('color','white');
                        });
                    </script>
                    <style>
                        table td a {
                            color: darkblue;
                            text-decoration: underline;
                        }
                    </style>
                    <table class="table table-bordered table-striped table-responsive" id="example-1">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;">Store Code</th>
                                <th style="min-width: 200px;">Store Name</th>
                                <th style="min-width: 120px;">Virtual Number</th>
                                <th style="min-width: 120px;">Store Number</th>
                                <th style="min-width: 190px;">Store Email</th>
                                <th style="min-width: 170px;">Address</th>
                                <th style="min-width: 140px;">Locality</th>
                                <th style="min-width: 140px;">City</th>
                                <th style="min-width: 140px;">State</th>
                                <th style="min-width: 140px;">Zone</th>
                                <th style="width:50px">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td class="scode">{{ $store->actual_store_id }}
                                        <input type="hidden" class="sid" value="{{ $store->id }}"></td>
                                    <td class="sname">{{ $store->store_name }}</td>
                                    <td class="vno">{{ substr($store->sim_number,-10) }}</td>
                                    <td class="sno">
                                        {{ str_replace('[','',str_replace(']','',str_replace('"','',$store->store_numbers))) }}
                                    </td>
                                    <td class="mname">{{ $store->store_email }}</td>
                                    <td class="address">{{ $store->address }}</td>
                                    <td class="locality">{{ $store->locality }}</td>
                                    <td class="city">{{ $store->city }}
                                        <input type="hidden" class="cid" value="{{ $store->cid }}"></td>
                                    <td class="state">{{ $store->state }}
                                        <input type="hidden" class="stid" value="{{ $store->sid }}"></td>
                                    <td class="zone">{{ $store->zone }}</td>
                                    <td style="font-size: 16px;">
                                        <a href="{{ url('/'.strtolower(Auth::user()->role).'/store/edit/' . $store->id) }}"> <i
                                                class="fa-edit"></i></a>
                                        <!-- <span style="margin-left: 5px;" class="fa-trash-o" onclick="javascript:del(this,{{ $store->id }})"></span> -->
                                    </td>
                                </tr>
                            @endforeach                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection

