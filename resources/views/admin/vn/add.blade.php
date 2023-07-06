@extends('layouts.master')

    @section('content')

    	<div class="page-title"> 
            <div class="title-env">
                <h2 class="title">Add Numbers</h2>
            </div>
        </div>
   
        <div class="panel panel-default">
            <div class="panel-body">
                <form role="form" id="new_vn" method="post" class="validate" onsubmit="return false;">
                    {{ csrf_field() }}
                    <div class="row" id="vn_container">
                        <div id="vn_1" class="form-group vn_div">
                            <div class="form-group col-sm-5"> 
                                <label class="control-label">Sim Number</label> 
                                <input type="text" minlength="10" maxlength="10" class="form-control" name="sim_number_1" id="sim_number_1" />
                            </div>
                            <div class="form-group col-sm-5"> 
                                <label class="control-label">Did Number</label> 
                                <input type="text" minlength="10" maxlength="10" class="form-control" name="did_number_1" id="did_number_1" />
                            </div>
                            <div class="form-group col-sm-2" style="padding-top: 24px;">
                                <div id="remove_vn_1" class="pull-right btn btn-red btn-icon btn-icon-standalone btn-sm remove_vn">
                                    <i class="fa-minus"></i>
                                    <span>Remove Numbers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div id="add_vn" class="pull-right btn btn-blue btn-icon btn-icon-standalone btn-sm">
                            <i class="fa-plus"></i>
                            <span>Add Numbers</span>
                            <input type="number" name="vn_counter" id="vn_counter" value="1" hidden="true">
                        </div>  
                    </div>
                    <br><hr><br>
                    <div class="form-group"> 
                        <button type="submit" id='save' class="btn btn-success">Save</button>
                        <button type="button" id="cancel" class="btn btn-grey">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="/assets/js/toastr/toastr.min.js"></script>
        <script src="/js/custom/add_vn.js"></script>

    @endsection