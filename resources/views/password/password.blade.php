@extends('layouts.master')

    @section('content')
    <style type="text/css">
      .disabled-look {
          background: #cccccc !important;
          border: grey !important;
          pointer-events: "none" !important;
      }
      #cover-spin {
          position:fixed;
          width:100%;
          left:0;right:0;top:0;bottom:0;
          background-color: rgba(255,255,255,0.7);
          z-index:9999;
          display:none;
      }

      @-webkit-keyframes spin {
          from {-webkit-transform:rotate(0deg);}
          to {-webkit-transform:rotate(360deg);}
      }

      @keyframes spin {
          from {transform:rotate(0deg);}
          to {transform:rotate(360deg);}
      }

      #cover-spin::after {
          content:'';
          display:block;
          position:absolute;
          left:48%;top:40%;
          width:40px;height:40px;
          border-style:solid;
          border-color:black;
          border-top-color:transparent;
          border-width: 4px;
          border-radius:50%;
          -webkit-animation: spin .8s linear infinite;
          animation: spin .8s linear infinite;
      }
 </style>
<div id="cover-spin"></div>
    <div class="page-title">
        <div class="title-env">
            <h3 class="title">Change Password</h3>
        </div>
    </div>
    <!--CALL FILTER SECTION STARTS HERE-->
        <div class="panel-body">
                    <div class="col-sm-6 col-offset-3">
                        <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Change Password</h3>
                            
                    </div>
                    <div class="panel-body">
                        <form role="form">
                            <div class="form-group"> <label for="newPassword">New Password:</label> <input type="password" class="form-control" id="newPassword" placeholder="Enter new password"> </div>
                            <div class="form-group"> <label for="confirmPassword">Confirm Password:</label> <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password"> </div>
                           
                            <div class="form-group"> <button type="button" class="btn btn-info btn-single pull-right" id="changePassword">Change Password</button> </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <script src="/assets/js/toastr/toastr.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(document).on('keypress', '#newPassword,#confirmPassword', function(e){
            return !(e.keyCode == 32);
        });
    });

        $('#newPassword,#confirmPassword').change(function(){
            $('#newPassword,#confirmPassword').removeAttr( 'style' );
        });
        $('#changePassword').click(function(){
            $("#newPassword").css("border-color",'');
            $("#confirmPassword").css("border-color",'');
            $('#changePassword').prop('disabled', true);
           if($("#newPassword").val() && $("#confirmPassword").val() !== '') {
             if($("#newPassword").val() == $("#confirmPassword").val()) {
                newPassword = {"password": $("#newPassword").val(), "_token": "{{ csrf_token() }}"} ;
               $('#cover-spin').show();
                $.ajax({
                    type: "POST",
                    url: "/update-password",
                    data: newPassword,
                    success: function(status) {
                        if(status.status == true) {
                            setTimeout(function(){
                              $('#cover-spin').hide();
                              toastr.success("Password changed successfully");
                              window.location.href = '/change-password';
                            }, 500);
                        } 
                        else if(status.status == false) {
                            $("#newPassword").css("border-color",'red');
                            $("#confirmPassword").css("border-color",'red');
                            var html = '';
                            html = 'Password should be contains <ul><li>Minimum 10 characters</li><li>Atleast 1 upper case</li><li> Atleast 1 lower case</li><li> Atleast 1 number</li><li>Atleast 1 special character</li><li>Maximum 20 characters</li></ul>';
                            setTimeout(function(){
                              $('#cover-spin').hide();
                              toastr.error(html);
                              $('#changePassword').prop('disabled', false);
                            }, 500);
                        }
                        else {
                            
                           $('#changePassword').prop('disabled', false);
                        }
                    }
                });
            }else {
                toastr.error("New and Confirm password not matching");
                 $("#newPassword").css("border-color",'red');
                $('#changePassword').prop('disabled', false);
                $("#confirmPassword").css("border-color",'red');
            }
           }else {
             toastr.error("Password fields can't be empty");
             if($("#newPassword").val() == '') {
                $("#newPassword").css("border-color",'red');
             }
             if($("#confirmPassword").val() == '') {
                $("#confirmPassword").css("border-color",'red');
             }
             $('#changePassword').prop('disabled', false);

           }
        });
    </script>


@endsection

   
