var counter = 1;
$("#add_vn").click(function(e){
	counter++;
	var vn_div = '<div id="vn_'+counter+'" class="form-group vn_div"><div class="form-group col-sm-5"><label class="control-label">Sim Number</label><input type="number" minlength="10" maxlength="10" class="form-control" name="sim_number_'+counter+'" id="sim_number_'+counter+'" /></div><div class="form-group col-sm-5"><label class="control-label">Did Number</label><input type="number" minlength="10" maxlength="10" class="form-control" name="did_number_'+counter+'" id="did_number_'+counter+'" /></div><div class="form-group col-sm-2" style="padding-top: 24px;"><div id="remove_vn_'+counter+'" class="pull-right btn btn-red btn-icon btn-icon-standalone btn-sm remove_vn"><i class="fa-minus"></i><span>Remove Numbers</span></div></div></div>';

    $('#vn_container').append(vn_div);
    $('#vn_counter').attr('value',counter);
});

$('.panel-body').on('click', '.remove_vn', function () {
    $(this).parents(".vn_div").remove();
});

$("#new_vn").submit(function(e) {
    var vn_details = {};
    var url = "/admin/vn/insert"; // the script where you handle the form input.
    var opts = {"closeButton": true,
        "debug": false,
        "positionClass": "toast-bottom-right",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    var form_data = $("#new_vn").serializeArray();
    var empty_fields = 1;
    for (var i =0 ; i <= 2; i++) {
        if(form_data[i]['value']==''){
            toastr.error("Please fill up the atleast 1 set of numbers!", "Error", opts);
            return false;
            break;
        }else {
            empty_fields = 0;
        }
    }
    
    if(empty_fields==0){
        $.ajax({
              type: "POST",
              url: url,
              data: form_data,
              success: function(data)
              {
                if(data['success']==true){
                  $('#save').attr('disabled','');
                  $('#cancel').attr('disabled','');
                  toastr.success("Virtual numbers successfully inserted!", "Saved", opts);
                  setTimeout(function () {
                    document.location.href = '/admin/vn/';
                  }, 3000);
                }
              }
            });
        }

    e.preventDefault(); // avoid to execute the actual submit of the form.
});