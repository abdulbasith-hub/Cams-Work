<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="../site/image/tn__logo.png" />


    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="../site/image/tn__logo.png" />

    <!-- Core Css -->
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <link rel="stylesheet" href="../common/custom.css" />

    <title>CAMS - Forget Password</title>
    <script src="../assets/js/jquery.js"></script>

    <style>
        body {
          font-family: 'Poppins', sans-serif; /* Apply the custom font */
          background-color: #f8f9fa; /* Light background */
        }
    h4 {
        font-family: 'Poppins', sans-serif; /* Apply the custom font */
        font-weight: 600;
         color: #333;
    }

    .underline{
      text-decoration: none;
    }
      label {
        font-weight: 200;
      }
      .btn-primary {
        font-weight: 600;
      }
      .card {
        border-radius: 12px;
      }
      .text-decoration-none:hover {
        text-decoration: underline;
      }
      .form-control {
      border: 2px solid #ddd;
      border-radius: 8px;
      /* padding: 12px; */
      font-size: 14px;
      box-shadow: none;
      transition: all 0.3s ease;
    }
    .form-control:focus {
      border-color: #77aeee;
      box-shadow: 0 0 8px rgba(28, 134, 255, 0.25);
    }
    .form-error{
      font-size: 14px;

    }

     /* Example of responsive CSS */
     @media (max-width: 454px) {
        .captcha-image {
            width: 100%;  /* Ensure it fills the screen width */
            max-width: 120px !important;  /* Limit the max width */
            height: auto;  /* Maintain aspect ratio */
        }
    }
    </style>
  
</head>
<body>

    @include('layouts.site_header')
    @include('common.alert')

   

    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
          <h4 class="text-center mb-4 lang" key="forget_head">Forget Password</h4>

          <form id="forget_password" name="forget_password" method="post" novalidate>
 	  @csrf
          <input type="hidden" id="user_type" name="user_type" value="{{$user}}">
            <div class="mb-3">
              <label for="email" class="form-label lang required" key="email">Email Address</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" >
              <div id="email-error" class="text-danger form-error" style="display: none;"></div>


            </div>

            <div class="mb-4">
                  <label for="captcha" class="form-label lang" key="captcha">Enter Captcha</label>

                  <!-- Row container -->
                  <div class="d-flex align-items-center gap-2 mb-2">
                       <!-- CAPTCHA Box -->
                      <div id="captcha-box" style="background-image: url('{{ asset('assets/images/backgrounds/captcha.jpg') }}');background-size: cover;width: 180px;height: 40px;display: flex;align-items: center;justify-content: center;font-size: 24px;font-weight: bold;letter-spacing: 4px; color: #2c2c2c;user-select: none;">
                      </div>

                      <!-- Reload Button -->
                      <button type="button" onclick="refreshCaptcha()" class="btn btn-primary" style="height: 40px;"><i class="fas fa-sync-alt"></i></button>
                  </div>

                  <!-- Input field -->
                  <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter captcha code">
                  <div id="captcha-error" class="text-danger form-error" style="display: none;"></div>

            </div>            
            <!-- Submit Button -->
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-primary submit lang" key="reset">Reset</button>
            </div>
          </form>
        </div>
      </div>

       <!-- Modal -->
       <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="responseModalLabel">Forget Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Success/Error message will be dynamically updated here -->
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
        </div>
    </div>
    
      <!-- Bootstrap JS Bundle -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      
      <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

      <script>
          $(document).ready(function () {
            // Email validation logic on input
            const messages = {
        en: {
            emptyEmail: "Enter an email",
            invalidEmail: "Please enter a valid email address.",
            captchaerror : "Enter Captcha code"
        },
        ta: {
            emptyEmail: "?????????? ??????????",
            invalidEmail: "?????? ?????????? ???????? ??????????.",
            captchaerror : "???????? ?????????? ??????????."

        }
    };

    function getLanguage() {
        return window.localStorage.getItem('lang') || 'en';
    }

    function updateValidationMessages() {
        const lang = getLanguage();
        const emailErrorDiv = $("#email-error");
        const captchaErrorDiv = $("#captcha-error");
        const email = $("#email").val();

        if (emailErrorDiv.is(":visible")) {
            if (!email) {
                emailErrorDiv.html(messages[lang].emptyEmail);
            } else if (!validateEmail(email)) {
                emailErrorDiv.html(messages[lang].invalidEmail);
            }else if (!captcha) {
              captchaErrorDiv.html(messages[lang].captchaerror);
            }
        }
    }

    $("#email").on("input", function () {
        const email = $(this).val();
        const emailErrorDiv = $("#email-error");
        const lang = getLanguage();

        if (!email) {
            emailErrorDiv.html(messages[lang].emptyEmail).show();
        } else if (!validateEmail(email)) {
            emailErrorDiv.html(messages[lang].invalidEmail).show();
        } else {
            emailErrorDiv.hide();
        }
    });

    $("#captcha").on("input", function () {
        const lang = getLanguage();

        const captcha = $("#captcha").val().trim();
        const captchaErrorDiv = $("#captcha-error");

        if (!captcha) {
          captchaErrorDiv.html(messages[lang].captchaerror).show();
        }  else {
          captchaErrorDiv.hide();
        }
    });

    $("#translate").on("change", function () {
    const lang = $(this).val();
    window.localStorage.setItem('lang', lang);
    
    const captchaErrorDiv = $("#captcha-error");
    if (captchaErrorDiv.is(":visible")) {
        captchaErrorDiv.html(messages[lang].captchaerror);
    }

    updateValidationMessages();
});



    // Ensure language persists on page reload
    $(window).on("load", function () {
        const savedLang = getLanguage();
        $("#translate").val(savedLang);
        updateValidationMessages();
    });
    
            // Email validation function
            function validateEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email regex pattern
                return emailPattern.test(email);
            }



            $("#captcha").on("input", function () {
                const captcha = $(this).val();
                const captchaErrorDiv = $("#captcha-error");
    
                if (!captcha) {
                  captchaErrorDiv.html("Enter Captcha code").show();
                } else {
                  // Hide error if input is valid
                  captchaErrorDiv.hide();
               }
            });

    
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            // Handle form submission
            $("#forget_password").on("submit", function (e) {
                e.preventDefault();
    
                const email = $("#email").val();
                const userType = $("#user_type").val(); // ? Get user type correctly
                const emailErrorDiv = $("#email-error");
                const captcha = $("#captcha").val().trim();
                const captchaErrorDiv = $("#captcha-error");
                const lang = getLanguage(); 
    
                emailErrorDiv.hide().html("");
    
              
                if (!email.trim()) { // Trim to avoid spaces being considered valid input
                  emailErrorDiv.html(messages[lang].emptyEmail).show();
                  return;
              }

              if (!validateEmail(email)) {
                  emailErrorDiv.html(messages[lang].invalidEmail).show();
                  return;
              }

              captchaErrorDiv.hide().html("");
                
                if (!captcha) {

                  captchaErrorDiv.html(messages[lang].captchaerror).show();

                      //captchaErrorDiv.html(text.captchaerror).show();
                  
                    event.preventDefault(); 
                    return;
                }
                $.ajax({
                    url: "{{ route('forgetpassword') }}", 
                    type: "POST",
                    data: { email: email,captcha:captcha,user: userType },
                    success: function (response) {
                        if (response.success) {
                              reset_form();
                            // Show success message in modal
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body','confirmation_alert');
                                  });

                            $('#ok_button').on('click', function () {
                                      setTimeout(function () {
                                        window.location.href = response.redirect;
                                      }, 400);
                                  });

                        }                 
   },
                    error: function (xhr) {
                        const lang = getLanguage();

                      let errorMessage = "An unexpected error occurred.";

                      if (xhr.responseJSON && xhr.responseJSON.message) {
                          if (xhr.responseJSON.message === 'validation.captcha') {
                              captchaErrorDiv.html('Invalid Captcha!').show();
                              refreshCaptcha();
                              return;
                          } else {
                              errorMessage = xhr.responseJSON.message;
                          }
                      }

                      getLabels_jsonlayout([
                          { id: errorMessage, key: errorMessage }
                      ], lang).then((text) => {
                          emailErrorDiv.show().text(text[errorMessage] || errorMessage);
                      });
                      refreshCaptcha();                  
		  }
                });
            });
        });

       /* $(document).ready(function() {
            // Reload CAPTCHA on button click
            $('#reload-captcha').click(function() 
            {
                reload_captchacode();
            });
        });

        function reload_captchacode()
        {
            $.ajax({
                    url: '{{ route('captcha.reload') }}', // Define the route to reload CAPTCHA
                    type: 'GET',
                    success: function(data) {
                        // Change the src of the image to reload it
                        $('#captcha-image').attr('src', data.captcha);
                    },
                    error: function() {
                        alert('Failed to reload CAPTCHA. Please try again.');
                    }
                });
            
        }*/

        function refreshCaptcha() 
        {
            fetch('/captcha-text')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('captcha-box').innerText = data.code;
                    $('#captcha').val('');
                });
        }

        window.onload = refreshCaptcha;



        function reset_form(){
          $('#email').val("");
        }

    </script>
    
    
  
    <script src="../common/custom.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    
</body>
</html>