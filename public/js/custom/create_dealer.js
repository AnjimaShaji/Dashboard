var counter = 0;
$("#add_workshop").click(function(e){
    workshop_vn = workshop_vns.pop();
    if (!workshop_vn) {
        alert("Free numbers unavailable!");
        return;
    }
    counter++;
    var workshop_div = '<div id="workshop_'+counter+'" class="form-group workshop_div"><div class="form-group col-sm-6"><label class="control-label">Workshop Virtual Number</label><input type="text" class="form-control" id="workshop_vn_'+counter+'" name="workshop_vn_'+counter+'" value="'+workshop_vn+'" readonly="true" /></div><div class="col-sm-12 form-group"><div class="form-group col-sm-4"><label class="control-label">Panda Code</label><input type="text" class="form-control" name="workshop_panda_code_'+counter+'" placeholder="" /></div><div class="form-group col-sm-6"><label class="control-label" for="workshop_numbers">Numbers</label><input type="text" class="form-control workshop_numbers_'+counter+'" name="workshop_numbers_'+counter+'" data-role="tagsinput" value="" placeholder="Enter 10 digit workshop numbers seperated by comma(,) Eg:8767895342,9088767890" /></div><div class="form-group col-sm-2" style="padding-top: 24px;"><div id="remove_workshop_'+counter+'" class="pull-right btn btn-red btn-icon btn-icon-standalone btn-sm remove_workshop"><i class="fa-minus"></i><span>Remove Workshop</span></div></div></div>';
    $('#workshop_container').append(workshop_div);
    $('.workshop_numbers_'+counter).tagsinput();
    $('#workshop_counter').attr('value',counter);
});

$('.panel-body').on('click', '.remove_workshop', function () {
    $(this).parents(".workshop_div").remove();
});

$('#cancel').click(function(){
    $(location).attr('href', '/admin/dealer')
});

$("#new_dealer").submit(function(e) {
    var dealer_details = {};
    var url = "/admin/dealer/save"; // the script where you handle the form input.
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
    var form_data = $("#new_dealer").serializeArray();
    var empty_fields = 1;
    for (var i =0 ; i <= 15; i++) {
        if(form_data[i]['value']==''){
            toastr.error("Please fill up the required fields!", "Error", opts);
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
                  $('#reset').attr('disabled','');
                  toastr.success("Dealer successfully inserted!", "Saved", opts);
                  $('#login_info').text("Email:"+data['email']+" Password:"+data['password']);
                  $('#login_details').modal('show', {backdrop: 'static'});
                  $('#finish').click(function () {
                    document.location.href = '/admin/dealer/';
                  });
                }
              }
            });
        }

    e.preventDefault(); // avoid to execute the actual submit of the form.
});