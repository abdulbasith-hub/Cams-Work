@section('content')
    @extends('index2')
    @include('common.alert')

    @php
      //  $sessionchargedel = session('user');
         $sessionchargedel = session('charge');
        // print_r($sessionchargedel);
    @endphp

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


     .toggle-password.active i {
           color: #28a745;
       }
   </style>


<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">


<div class="position-fixed top-20 start-50 translate-middle-x p-3" style="z-index: 900; width: 100%;">
    <div class="toast toast-onload align-items-center text-bg-primary border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body hstack align-items-start gap-6">
            <i class="ti ti-alert-circle fs-6"></i>
            <div>
                <h5 class="text-white fs-3 mb-1">Welcome to CAMS,</h5>
                <h6 class="text-white fs-2 mb-0">{{ $sessionchargedel->username ?? 'User' }}</h6>
            </div>
            <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>





<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content content">
                <div class="modal-header ">
                    <h4 class="modal-title text-center lang" id="changePasswordModalLabel" key="password_head">Change Password</h4>

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


                             <div id="oldpassword-error" class="text-danger form-error" style="display: none;"></div>

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
                            <button type="submit" class="btn btn-primary submit lang" key="save_changes" action="insert" id="buttonaction" name="buttonaction">Save Changes</button>


                        </div>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <script src="../assets/js/jquery_3.7.1.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="../common/ajaxfn.js"></script>


<script>





$(document).ready(function () {

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



//document.addEventListener("DOMContentLoaded", function() {
    @if ($profileUpdate === 'Y')
        var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        changePasswordModal.show();
    @endif
//});


$('#translate').change(function() {
var lang = getLanguage('Y');
changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn));
updateValidationMessages(getLanguage('Y'), 'passwordForm');
});

$(document).ready(function(){
    $('#oldpassword, #newpassword, #confirmpassword').on('input', function() {
        $('#' + this.id + '-error').hide();
    });
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
    $(errorSelector).html(errorMessage[lang]).show(); // Show error message
    return false; // ? Invalid password
}

return true; // ? Valid password
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
            //alert();


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





                    } else if (response.message) {

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



    updateSelectColorByValue(document.querySelectorAll(".form-select"));
}






@if ($profileUpdate === 'Y')
        var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        changePasswordModal.show();
    @endif



    window.addEventListener('DOMContentLoaded', (event) => {
        var toastEl = document.querySelector('.toast-onload');
        var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    });
</script>

@endsection
