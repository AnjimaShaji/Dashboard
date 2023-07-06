  $("#fileInput").change(function (e){
    var fileName = e.target.files[0].name;
    var div = getUpload(fileName);
    $("#filestatus").append(div);
    
    $(document).on('click', '#clear_attachment' , function() {
            $("#fileInput").val('');
            $('.alert-white').remove();

        });

    });

    function getUpload(name) {

    var div = '<div class="alert alert-white"><i class="linecons-attach"></i><button type="button" id="clear_attachment" class="close" data-dismiss="alert"> <span aria-hidden="true">×</span> <span class="sr-only">Close</span> </button> <strong>'+name+'</strong> </div>';
    return div;
    }

    var workshop_flag = 0;
    var number_flag = 0;
    var ford_website_flag = 0;

    $("#create_ticket").submit(function(e) {
        $('#save').attr('disabled','disabled');
        e.preventDefault();
        var datastring = new FormData(document.getElementById("create_ticket"));
        datastring.append('description',CKEDITOR.instances.description.getData());      
        var flag = 0;
        var flag_1,flag_2,flag_3,flag_4,flag_5,flag_6,flag_7,ck_flag,n_flag_1,n_flag_5,n_flag_6;
        $('#mobile_no').css("border-color",'');
        $('#from').css("border-color",'');
        $('#number_input').css("border-color",'');
        $('#select_subject').css("border-color",'');
        $('#edit_number').css("border-color",'');
        $('#cke_description').css("border-color",'');
        $('#select_subject').css("border-color",'');
        $('#select_no_type').css("border-color",'');
        $('#call_type').css("border-color",'');
        $(".new_no_validation").css("border-color",'');
         $(".action").css("border-color",'');
        var change_count = $('#count_nos').val();
        for(var bc = 0; bc <change_count; bc++) {
            $('#replace_'+bc).css("border-color",'');
        }

        if($('#from').val() == '') {
            flag_1 = 1;
        }
        if($('#mobile_no').val() == '') {
            flag_3 = 1;
        } else {
            if (isNaN($('#mobile_no').val())|| $('#mobile_no').val().length != 10) {
                flag_3 = 1;
            }
        }
        if($('#select_subject').val() == '') {
            flag_4 = 1;
        }

      

         if(1) { 
            var ck_value = CKEDITOR.instances.description.getData();
            if(ck_value.length == 0) {
                ck_flag =1;
                $('#cke_description').css("border-color",'red');
            }
           
         }

        console.log(ck_value);


        if(flag_1 == 1 || flag_2 == 1 || flag_4 == 1 || ck_flag ==1 || n_flag_1 == 1) {
            toastr.error("Please fill up the fields");
            flag = 1;
        }

        if(flag_1 == 1) {
            $('#from').css("border-color",'red');
        }
        if(flag_2 == 1) {
            $('#mobile_no').css("border-color",'red');
        }
        if(flag_3 ==1) {
            toastr.error("Please enter a 10 digit mobile number");
            flag = 1;
            $('#mobile_no').css("border-color",'red');
        }
        if(flag_4 == 1) {
            $('#select_subject').css("border-color",'red');
        }

    
        

        if (flag == 0) {
            show_loading_bar(97);
            $.ajax({
            type: "POST",
            url: "/"+$("#user_role").val().toLowerCase()+"/new-ticket",
            data: datastring,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data.status == true) {
                    window.location.href = "/"+$("#user_role").val().toLowerCase()+"/view-ticket?id=" + data.id;            
                } else if(data.status == false){
                    toastr.error("Please choose a valid image or doc or excel file");
                    $('#save').prop('disabled', false);
                 
                }else{
                    window.location.href = "/"+$("#user_role").val().toLowerCase()+"/create-ticket" ;
                }
            }
        });

        } else {
            $('#save').prop('disabled', false);
            hide_loading_bar();
            return false;
        }
        
    });
