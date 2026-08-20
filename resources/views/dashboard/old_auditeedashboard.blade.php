@extends('index2')
@section('content')
@section('title', ' Dashboard')
@include('common.alert')

   


<style>
       .modal-header {
           background-color: #3866ac;
           color: #fff;
           display: flex;
           justify-content: center;
           /* Centers content horizontally */
           align-items: center;
           /* border-radius: 20px; */

       }

       .modal-title {
           font-weight: bold;
           margin: 0 auto;
           /* Ensures it's centered within the modal-header */
           color: white;

       }

       .form-control:focus {
           border-color: none;
           */
           /* box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); */
       }

       .btn-primary {
           /* color: rgb(92, 57, 133); */
           background-color: #0d6efd;
           border: none;
       }

       .btn-primary:hover {
           background-color: #0b5ed7;
       }

       .content {

         
            border-radius: 20px; 
           overflow: hidden;
       }

       .form-label {
           color: #42669c;
       }

       .btn-close {
           border: none;
           /* Remove the default border */
           opacity: 1;
           /* Make it fully visible */
       }

       .btn-close:hover {
           color: #bea9a9;
       }

       .btn-close:focus {
           box-shadow: none;
           /* Remove focus outline if present */
       }

       .toggle-password {
               position: absolute;
               right: 10px;
               top: 10px; /* Fixed position from the top */
               cursor: pointer;
               pointer-events: auto;
}

       /* Default color for the eye icon */
       .toggle-password i {
           color: #5682b3;
           transition: color 0.3s ease;
       }

       /* Hover state */
       /* .toggle-password:hover i {
                       color:  #2f5286;
                   } */

       /* Active state when showing password */
       .toggle-password.active i {
           color: #28a745;
           /* Custom color (green) when password is visible */
       }
   </style>

<style>
    .card-body {
        padding: 1px 10px;
    }

    .card {
        margin-bottom: 2px;
    }


    .round-40 {
        width: 50px !important;
        height: 50px !important;
        background: transparent;
    }

    .card-title {
        font-size: 22px;
        font-weight: bold;
    }
</style>


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<?php
$totalCount = $intimationcount->first()->total_count;
// print_r($totalCount)

?>

<div class="modal fade rounded" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content content">
                <div class="modal-header ">
                    <h4 class="modal-title text-center lang" id="changePasswordModalLabel " key="password_head">Change Password</h4>
                    
                </div>
                <div class="modal-body">

                    <form id="passwordForm">
                    @csrf

                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                            id="display_error"></div>
                        <div class="mb-2">
                            <label for="oldpassword" class="form-label required lang" key="oldpassword">Old Password</label>

                            <div class="position-relative">

                                <input type="password" class="form-control" name="oldpassword" id="oldpassword"
                                data-placeholder-key="oldpassword">

                                <span class="toggle-password position-absolute" data-target="#oldpassword">
                                    <i class="fas fa-eye"></i>
                                </span>

                            </div>


                             <div id="oldpassword-error" class="text-danger form-error " style="display: none;"></div> 

                        </div>

                        <div class="mb-2">
                            <label for="newpassword" class="form-label required newpassword_error lang" key="newpassword">New Password</label>

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
                        <div class="mb-1">
                            <label for="confirmpassword" class="form-label required lang" key="confirmpassword">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirmpassword" id="confirmpassword"
                            data-placeholder-key="confirmpassword">

                             <div id="confirmpassword-error" class="text-danger form-error" style="display: none;"></div> 

                        </div>
                       
                        <div class="row ">
                        <div class="modal-footer">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button type="submit" class="btn btn-primary submit lang" action="insert" key="save_changes" id="buttonaction" name="buttonaction">Save Changes</button>

                           
                        </div>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <div class="row mt-4">
        <div class="card " id="view_Details" >
            <div class="tab-content pt-2 container-fluid">
                <div class="tab-pane fade show active" id="scehduled_plans_tab-pane" role="tabpanel"
                    aria-labelledby="scehduled_plans_tab">
                    <div  class="row justify-content-center">
                        <div class="col-lg-3 col-md-2 ">
                            <div class="card card-slip" style="background-color:rgb(10, 100, 173)">
                                <div class="card-body " style="cursor: <?php echo $countDetails[0]['inboxcount'] == 0 || empty($countDetails[0]['inboxcount']) ? 'default' : 'pointer'; ?>" <?php echo $countDetails[0]['inboxcount'] == 0 || empty($countDetails[0]['inboxcount']) ? '' : 'onclick="window.location.href=\'auditee_fieldaudit\';"'; ?>>
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="background: white !important; color: #007bff !important;">
                                            <i class="ti ti-inbox fs-7"></i> <!-- Changed to inbox icon -->
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="" class="card-title mb-1 text-white">
                                                {{ $countDetails[0]['inboxcount'] ?? 0 }}</h4>
                                            <p class="card-subtitle text-white">Inbox</p>
                                            <i
                                                class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                  <!--  <div class="slide-notification rounded-24 hide_this" id="notification" onclick="redirectToPage()">
    
    <div class="d-flex align-items-center"><span>Intimation...</span> 

        <i class="ti ti-bell fs-6" style="margin-right: 10px;"></i>
    </div>
</div>-->
        </div>
        

    </div>

    <style>
        /* Slide-in notification styles */
        .slide-notification {
            position: fixed;
            top: 100px;
            right: -60px;
            /* Initially hidden outside */
            background-color:rgb(83, 126, 245);
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: right 0.5s ease-in-out;
            cursor: pointer;
          
            border-radius: 10px;
        }

        /* On hover, change background color */
        .slide-notification:hover {
            //background-color: #45a049;
        }
        @media (max-width: 768px) {
    .slide-notification {
        position: fixed;
        top: 320px; /* You can adjust this as needed */
        right: -500px; /* Initially hidden */
        background-color: rgb(83, 126, 245);
        color: white;
        
        border-radius: 5px; /* Rounded corners */
        font-size: 13px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: right 0.5s ease-in-out;
        cursor: pointer;
        height: 20px; /* Increased height for better content display */
        display: flex; /* Use flexbox to align content */
        align-items: center; /* Vertically center the text */
        justify-content: center; /* Horizontally center the content */
        z-index: 9999; /* Ensure it stays on top */
    }
}

    </style>




    <script>
     document.addEventListener("DOMContentLoaded", function() {
        // Check if the screen size is mobile (max width of 768px)
        if (window.innerWidth <= 768) {
            // For mobile view, slide the notification in from the right
            document.getElementById('notification').style.right = '20px';
        } else {
            // For larger screens, adjust accordingly
            document.getElementById('notification').style.right = '50px';
        }
    });
        // Redirect to another page when notification is clicked
        function redirectToPage() {
            window.location.href = "auditee"; // Replace with the desired URL
        }
    </script>


<script>

 document.addEventListener("DOMContentLoaded", function() {
    @if ($profileUpdate === 'Y')
        var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        changePasswordModal.show();
    @endif
});



$(document).ready(function () {

    var intimationcount = <?php echo $totalCount; ?>;

if (intimationcount > 0) {
    $('#notification').show();
} else {
    $('#notification').hide();
}

    $(document).on('click', '.toggle-password', function () {
    const target = $($(this).data('target')); 
    const icon = $(this).find('i');
    if (target.attr('type') === 'password') {
      target.attr('type', 'text'); 
      icon.removeClass('fa-eye').addClass('fa-eye-slash'); 
    } else {
      target.attr('type', 'password'); 
      icon.removeClass('fa-eye-slash').addClass('fa-eye'); 
    }
   });

   


    $('#translate').change(function() {
    var lang = getLanguage('Y');
    changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
        @json($updatebtn), @json($clearbtn));
    updateValidationMessages(getLanguage('Y'), 'passwordForm');
    });

    var lang = getLanguage();

    $(document).ready(function(){
        $('#oldpassword, #newpassword, #confirmpassword').on('input', function() {
            $('#' + this.id + '-error').hide();
        });
    });





// // Trigger validation only when user types
// $("#newpassword").on("input", function() {
//     validatePassword("#newpassword", "#newpassword_error");
// });


});

function validatePassword(inputSelector, errorSelector) {
    const password = $(inputSelector).val().trim();
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,20}$/;

    let lang = window.localStorage.getItem('lang') || 'en';
    let errorMessage = {
        en: "Password must be 8-20 characters, include uppercase, lowercase, a number, and a special character.",
        ta: "கடவுச்சொல் 8-20 எழுத்துகளாக இருக்க வேண்டும், பெரிய எழுத்து, சிறிய எழுத்து, ஒரு எண், மற்றும் ஒரு சிறப்பு எழுத்து உள்ளடக்கியதாக இருக்க வேண்டும்."
    };

    // Always clear the error first
    $(errorSelector).hide().text("");

    // Check password validity
    if (!passwordRegex.test(password)) {
        $(errorSelector).html(errorMessage[lang]).show(); // Show error message
        return false; // ❌ Invalid password
    }

    return true; // ✅ Valid password
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
                },
                

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
                    url: "{{ route('changepassword') }}", // URL where the form data will be posted
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

                            $("#changePasswordModal").modal("hide");

                           
                            //  table.ajax.reload();
                           


                        } else if (response.message) {
                           // alert();
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
            @json($savebtn))
            

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

            </script>

@endsection
