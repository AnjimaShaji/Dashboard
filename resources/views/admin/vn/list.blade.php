@extends('layouts.master')

    @section('content')

        <div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Numbers</h2>
                <p class="description">Total:{{ $vn_count }} | Active:{{ $active_vn }} | Free:{{ $free_vn }}</p>
            </div>
            <div class="breadcrumb-env">
                <a href="/admin/vn/add/">
                <button class="btn btn-secondary btn-icon btn-icon-standalone" id="create_new">
                    <i class="fa-plus"></i> 
                    <span>Add Numbers</span>
                </button>
                </a>
            </div>
        </div>

        <div class="">
            <div class="panel panel-default">
                <div class="panel-body">
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $("#example-1").dataTable({
                                aLengthMenu: [
                                    [10, 25, 50, 100, -1],
                                    [10, 25, 50, 100, "All"]
                                ]
                            });
                        });
                    </script>
                    <table class="table table-bordered table-striped" id="example-1">
                        <thead>
                            <tr>
                                <th>Sim Number</th>
                                <th>Did Number</th>
                                <th>Panda Code</th>
                                <th>Type</th>
                                <th>Workshop Panda Code</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($numbers as $number)
                                <tr>
                                    <td>{{ (!empty($number->sim_number))?$number->sim_number:'--' }}</td>
                                    <td>{{ (!empty($number->did_number))?$number->did_number:'--' }}</td>
                                    <td>{{ (!empty($number->panda_code))?$number->panda_code:'--' }}</td>
                                    <td>{{ (!empty($number->type))?$number->type:'--' }}</td>
                                    <td>{{ (!empty($number->workshop_pandacode))?$number->workshop_pandacode:'--' }}</td>
                                    @if (!empty($number->panda_code))
                                    <td><div class="label label-secondary">Active</div></td>
                                    @else
                                    <td><div class="label label-info">Free</div></td>
                                    @endif
                                </tr>
                            @endforeach                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endsection