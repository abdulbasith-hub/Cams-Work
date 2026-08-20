@section('content')

@extends('index2')
@include('common.alert')

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">



<style>
    body {
        background-color: #f8f9fa;
    }

    .form-label {
        color: #42669c;
        /* background-color:#06163a; */
    }





.toggle-password {
    position: absolute;
    right: 10px;
    top: 7px; /* Fixed position from the top */
    cursor: pointer;
    pointer-events: auto;
}

    .toggle-password i {
        color: #5682b3;
        transition: color 0.3s ease;
    }

    .toggle-password.active i {
        color: #28a745;
    }

    .mar_top {
        margin-top: 170px;
    }
</style>



<div class="row mar_top">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color fs-4 lang" key="password_head">Change Password</div>
            <div class="card-body">
                <form id="passwordForm" name="passwordForm">
                    @csrf

                  

                    <div class="row">

                        <div class="mb-2">
                            
                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                         id="display_error"></div>

                    <!-- <div  id="display_error" class="text-danger form-error" style="display: none;"></div> -->


                            <label for="oldpassword" class="form-label mt-2 required lang" key="oldpassword">Old Password</label>
                            <div class="position-relative">



                                <input type="password" class="form-control" name="oldpassword" id="oldpassword"
                                data-placeholder-key="oldpassword">
                                <span class="toggle-password position-absolute" data-target="#oldpassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="oldpassword-error" class="text-danger form-error" style="display: none;"></div>
                        </div>
                        <div class="mb-2">
                            <label for="newpassword" class="form-label required lang newpassword_error" key="newpassword">New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" name="newpassword" id="newpassword"
                                data-placeholder-key="newpassword">
                                <span class="toggle-password position-absolute" data-target="#newpassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="newpassword-error" class="text-danger form-error" style="display: none;"></div>
                            <div id="newpassword_error" class="text-danger form-error" style="display: none;"></div>

                        </div>
                        <div class="mb-2">
                            <label for="confirmpassword" class="form-label required lang" key="confirmpassword">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirmpassword" id="confirmpassword"
                            data-placeholder-key="confirmpassword">

                            <div id="confirmpassword-error" class="text-danger form-error" style="display: none;"></div>
                        </div>

                    </div>

                  
                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />

                            <button class="btn button_save mt-3 lang" key="savebtn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clearbtn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>


        


<!-- Include jQuery and Bootstrap -->

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>



<!-- Download Button End -->

<script>




$(document).on('click', '.toggle-password', function() {
            const target = $($(this).data('target')); // Get the target input element
            const icon = $(this).find('i'); // Get the <i> inside the span
            if (target.attr('type') === 'password') {
                target.attr('type', 'text'); // Change input type to text
                icon.removeClass('fa-eye').addClass('fa-eye-slash'); // Change icon to eye-slash
            } else {
                target.attr('type', 'password'); // Change input type to password
                icon.removeClass('fa-eye-slash').addClass('fa-eye'); // Change icon to eye
            }
        });
   

    $(document).ready(function() {
        $('#passwordForm')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();



    });

    $('#translate').change(function() {
        var lang = getLanguage('Y');
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'passwordForm');
    });

    $(document).ready(function(){
    $('#oldpassword, #newpassword, #confirmpassword').on('input', function() {
        $('#' + this.id + '-error').hide();
    });
});




function validatePassword(inputSelector, errorSelector) {
    const password = $(inputSelector).val().trim();
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,20}$/;

    let lang = window.localStorage.getItem('lang') || 'en';
    let errorMessage = {
        en: "Password must be 8-20 characters, include uppercase, lowercase, a number, and a special character.",
        ta: "?????????? 8-20 ??????????? ?????? ????????, ????? ???????, ????? ???????, ??? ???, ??????? ??? ??????? ??????? ????????????? ?????? ????????."
    };

    // Always clear the error first
    $(errorSelector).hide().text("");

    // Check password validity
    if (!passwordRegex.test(password)) {
        $(errorSelector).html(errorMessage[lang]).show(); 
        return false; 
    }

    return true; 
}


    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#passwordForm").validate({

            rules: {
                oldpassword: {
                    required: true,
                },
               
                newpassword: {
                    required: true
                },
                confirmpassword: {
                    required: true
                }
               

            },

            messages: errorMessages[language], // Set initial messages


            
         
          
        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();

            let isFormValid = $("#passwordForm").valid(); // Step 1: Validate form

            if (!isFormValid) {
                return false; // Stop submission if form is invalid
            }

            let isPasswordValid = validatePassword("#newpassword", "#newpassword-error"); // Step 2: Validate password

            if (!isPasswordValid) {
                return false; // Stop submission if password is invalid
            }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#passwordForm').serializeArray();

                

             
                //console.log(formData);
                $.ajax({
                    url: "{{ route('dashboardchangepassword') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                           
                            //  table.ajax.reload();
                           


                        } else if (response.message) {
                          
                            //$('#display_error').show().text(response.message);

                            // Handle errors if needed
                            // console.log(response.error);
                        }
                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {
                        


                            getLabels_jsonlayout([
                                { id: response.old, key: response.old },
                                { id: response.oldandnew, key: response.oldandnew },
                                { id: response.newandconf, key: response.newandconf }
                            ], 'N').then((text) => {
                                $('#oldpassword-error').hide().text('');
                                $('#newpassword-error').hide().text('');
                                $('#confirmpassword-error').hide().text('');

                                if (response.old) {
                                    $('#oldpassword-error').show().text(text[response.old] || response.old);
                                }
                                if (response.oldandnew) {
                                    $('#newpassword-error').show().text(text[response.oldandnew] || response.oldandnew);
                                }
                                if (response.newandconf) {
                                    $('#confirmpassword-error').show().text(text[response.newandconf] || response.newandconf);
                                }
                            });
                                                
                        }
                    }
                });

          


        });
    

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    function reset_form() {
         $('#passwordForm')[0].reset();
      
        $("#oldpassword-error").hide();
        $("#newpassword-error").hide();
        $("#confirmpassword-error").hide();

        changeButtonAction('passwordForm', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
    
</script>




@endsection
