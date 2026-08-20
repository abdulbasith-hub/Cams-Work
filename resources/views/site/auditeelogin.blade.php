@include('common.alert')
<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="../site/image/tn__logo.png" />

    <!-- Core Css -->
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <link rel="stylesheet" href="../common/custom.css" />


    <title>CAMS - Login</title>
     <style>
       html, body {
    height: 100%;
    margin: 0;
    overflow-x: hidden;
}

.auth-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.auth-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.auth-card {
    width: 100%;
    max-width: 400px;
    margin-top:10%;
}

@media (max-width: 576px) {
    .auth-card {
        max-width: 90%;
        margin-top:60%;
    }
    .captcha-image {
        width: 100%;
        max-width: 250px !important;
        height: auto;
    }
    #pageFooter {
    padding: 0.4rem 0;
}

#pageFooter img {
    width: 60px;
    height: 60px;
}

#pageFooter p {
    font-size: 0.75rem;
    margin: 0rem 0;
}

#pageFooter a {
    font-size: 0.75rem;
}
}



#pageFooter {
    padding: 0.5rem 0;
}

#pageFooter img {
    width: 80px;
    height: 50px;
}

#pageFooter p {
    font-size: 1rem;
    margin: 1rem 0;
}

#pageFooter a {
    font-size: 1rem;
}
.toggle-password {
            top: 45%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        /* Default color for the eye icon */
        .toggle-password i {
            color: #5682b3;
            transition: color 0.3s ease;
        }

    </style>
    <style>
     /* Example of responsive CSS */
    @media (max-width: 454px) {
        .containertest{
            margin-top:20%;
        }
        .captcha-image {
            width: 100%;  /* Ensure it fills the screen width */
            max-width: 120px !important;  /* Limit the max width */
            height: auto;  /* Maintain aspect ratio */
        }
    }

    </style>
</head>

<body>

    <!-- Preloader -->
    <div class="preloader">
        <img src="../site/image/tn__logo.png" alt="loader" class="lds-ripple img-fluid" />
    </div>

    @include('layouts.site_header')


    <div id="main-wrapper" class="auth-customizer-none auth-wrapper">
  <div class="auth-content radial-gradient border border-grey">

    <div class="d-flex align-items-center justify-content-center w-100 containertest">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                <div class="card mb-0">
                    <div class="card-body">
                                <span class="text-nowrap logo-img text-center d-block mb-1 w-100">
                                    <!-- <img src="../assets/images/logos/dark-logo.svg" class="dark-logo" alt="Logo-Dark" />
                    <img src="../assets/images/logos/light-logo.svg" class="light-logo" alt="Logo-light" /> -->
                                    <h2>Auditee Login </h2>
                                </span>

                                <!-- <div class="row">
                    <div class="col-6 mb-2 mb-sm-0">
                        <a class="btn text-dark border fw-normal d-flex align-items-center justify-content-center rounded-2 py-8" href="javascript:void(0)" role="button">
                        <img src="../assets/images/svgs/google-icon.svg" alt="modernize-img" class="img-fluid me-2" width="18" height="18">
                        <span class="flex-shrink-0">with Google</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a class="btn text-dark border fw-normal d-flex align-items-center justify-content-center rounded-2 py-8" href="javascript:void(0)" role="button">
                        <img src="../assets/images/svgs/facebook-icon.svg" alt="modernize-img" class="img-fluid me-2" width="18" height="18">
                        <span class="flex-shrink-0">with FB</span>
                        </a>
                    </div>
                    </div> -->
                                <!-- <div class="position-relative text-center my-4">
                    <p class="mb-0 fs-4 px-3 d-inline-block bg-body text-dark z-index-5 position-relative">or sign in with
                    </p>
                    <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                    </div> -->
                    <div id="display_error"class="alert alert-danger alert-dismissible fade show hide_this"
                                    role="alert"style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">
                                    <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <form id="login-form" name="login-form" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Username</label>
                                        <input type="email" class="form-control" id="username" name="username"
                                            aria-describedby="emailHelp">
                                    </div>
                                    <div class="mb-4">
                                        <label for="exampleInputPassword1" class="form-label">Password</label>
                                        <div class="position-relative">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <span class="toggle-password position-absolute" data-target="#password">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <br>
                                    <div class="mb-4">
                                        <label for="captcha" class="form-label">Enter Captcha</label>

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
                                    </div>




                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <!-- <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                        <label class="form-check-label text-dark" for="flexCheckChecked">
                            Remeber this Device
                        </label> -->
                                        </div>
                                        <a href="{{ url('/forgetpassword?user=auditee') }}" class="text-primary fw-medium">Forgot
                                            Password ?</a>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Sign
                                        In</button>
                                    <!-- <div class="d-flex align-items-center justify-content-center">
                        <p class="fs-4 mb-0 fw-medium">New to Modernize?</p>
                        <a class="text-primary fw-medium ms-2" href="../main/authentication-register.html">Create an
                        account</a>
                    </div> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <footer id="pageFooter" class="text-center pt-1 pb-2 "
        style="background-color: rgba(6, 39, 95, 0.8); color: white; font-size: 14px;">
        <img src="{{ asset('site/image/niclogo.png') }}" alt="NIC Logo" width="150">

        <div class="container font_div">

            <p>
                <span class="lang" key="develope">Designed and Developed by</span> -
                <span>NIC</span> &copy; 2026.
                <span class="lang" key="rights">All Rights Reserved</span>
            </p>

            <p>
                <span class="lang" key="queries">Queries/Comments regarding the content on this site may be sent to
                    cams[dot]dga[at]tn[dot]gov[dot]in</span>
            </p>

            <p><a href="disclaimer" class="lang" key="disclaimer">Disclaimer</a> |


                <a href="privacy" class="lang" key="privacycopy">Privacy & Copyright Policy</a> |


                <a href="terms" class="lang" key="term">Terms & Conditions</a> |

                <a href="/" class="lang" key="home">Home</a>
                <br class="d-md-none">

            </p>
        </div>
    </footer>
    <script>
        function handleColorTheme(e) {
            document.documentElement.setAttribute("data-color-theme", e);
        }
    </script>


    <!-- Import Js Files -->
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme/theme.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<script>
        window.APP_CONFIG = {
            AES_SECRET_KEY: "{{ config('app.aes_key') }}",
            AES_IV: "{{ config('app.aes_iv') }}"
        };
    </script>

        <script src="{{ asset('common/custom.js') }}"></script>



    <script>


 async function loginuser() {
            let password = document.getElementById("password").value;
            let encryptedPassword = await encryptPassword(password); // ✅ Await the encryption

            $.ajax({
                url: "{{ route('auditee_validatelogin') }}",
                type: "POST",
                data: {
                    username: $('#username').val(),
                    encryptedPassword: encryptedPassword,
                    captcha: $('#captcha').val()
                },
                success: function(response) {
                    if (response.success && response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else if (response.status == 'success' && response.message ==
                        'OTP has been sent successfully.') {
                        const otpcontent = `                        <div id="otp_div">
                <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mobile</span><br>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        </div>

                        <div id="otp_error" class="text-danger mt-1 mb-1" style="font-size:12px;"></div>

                        <button type="button" id="verify_otp_button_login" class="btn btn-primary w-100" >Verify OTP</button>
                    </div>
                </div><br>

                <small class="text-center mb-3 d-block">Didn't receive the code? <b  id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b></small><br>

            </div>`;


                        $('#otp_div').css({
                            'text-align': 'center'
                        }).show();

                        $('#confirmation_alert .modal-footer').hide();
                        $('#process_button').html("Verify OTP");

                        passing_alert_value('Verify OTP', otpcontent,
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                        $('#confirmation_alert').modal('show');

                        // Disable resend button initially
                        $('#resend_otp_link').css({
                            "pointer-events": "none",
                            "opacity": "0.5"
                        }).text("Resend OTP in (30)"); // Initial timer display

                        let timer = 30; // seconds
                        let interval = setInterval(() => {
                            timer--;
                            $('#resend_otp_link').text(`Resend OTP (${timer})`);

                            if (timer <= 0) {
                                clearInterval(interval);
                                $('#resend_otp_link').css({
                                    "pointer-events": "auto",
                                    "opacity": "1"
                                }).text("Resend OTP");
                            }
                        }, 1000);
                    } else {
                        $('#display_error').html(response.message);
                        $('#display_error').removeClass('fade hide_this').addClass('show');
                        refreshCaptcha();
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';

                    $.each(errors, function(key, value) {
                        if (value == 'validation.captcha') {
                            errorMessage += 'Invalid Captcha!';
                            refreshCaptcha();
                            $('#captcha').val(' ');

                        } else {
                            errorMessage += Array.isArray(value) ? value.join('<br>') : value +
                                '<br>';

                        }
                    });
                    $('#display_error').html(errorMessage);
                    $('#display_error').removeClass('fade hide_this').addClass('show');
                    refreshCaptcha();
                }
            });

        }

$(document).on('click', '#resend_otp_link', async function(e) {
            $('#otp_error').hide();
            let password = document.getElementById("password").value;
            let encryptedPassword = await encryptPassword(password); // ✅ Await the encryption
            e.preventDefault();
            $.ajax({
                url: "{{ route('auditee_validatelogin') }}",
                type: 'POST',
                data: {
                    username: $('#username').val(),
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    encryptedPassword: encryptedPassword, // ✅ Send properly encoded password
                    captcha: $('#captcha').val()
                },
                success: function(response) {
                    if (response.status == 'success' && response.message ==
                        'OTP has been sent successfully.') {
                        alert('OTP has been resent successfully.');
                        $('.otp-input').val();
                        // Disable resend button initially
                        $('#resend_otp_link').css({
                            "pointer-events": "none",
                            "opacity": "0.5"
                        }).text("Resend OTP in (30)"); // Initial timer display

                        let timer = 30; // seconds
                        let interval = setInterval(() => {
                            timer--;
                            $('#resend_otp_link').text(`Resend OTP (${timer})`);

                            if (timer <= 0) {
                                clearInterval(interval);
                                $('#resend_otp_link').css({
                                    "pointer-events": "auto",
                                    "opacity": "1"
                                }).text("Resend OTP");
                            }
                        }, 1000);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to resend OTP. Please try again.');
                }
            });
        });
        $(document).on('input', '.otp-input', function() {
            const $this = $(this);
            const value = $this.val();

            // Allow only digits
            const numericValue = value.replace(/\D/g, '');
            $this.val(numericValue);

            // Move to next input only if a valid number is entered
            if (numericValue.length === 1 && /^\d$/.test(numericValue)) {
                $this.next('.otp-input').focus();
            }
        });

        $(document).on('keydown', '.otp-input', function(e) {
            if (e.key === 'Backspace' && $(this).val() === '') {
                // Move to the previous input
                $(this).prev('.otp-input').focus();
            }
        });
        $(document).on('click', '#verify_otp_button_login', function() {
            admin_loggin();
        });


        async function admin_loggin()

        {
            let otp = '';
            $('.otp-input').each(function() {
                otp += $(this).val();
            });

            if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
                $('#otp_error').html('Please enter a valid 6-digit OTP.');
                return;
            }
            $('#process_button').attr('disabled', true);

            // alert(otp);
            let password = document.getElementById("password").value;
            let encryptedPassword = await encryptPassword(password); // ✅ Await the encryption

            $.ajax({
                url: 'login/verifyOtp_auditeelogin', // Your route to verify OTP
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    otp: otp,
                    username: $('#username').val(),
                    encryptedPassword: encryptedPassword

                },
                // beforeSend: function() {
                //     $('#overlay-loader').show();
                // },
                success: function(response) {
                    if (response.success && response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        $('#otp_error').html(response.message || 'Incorrect OTP.');
                        //alert(response.message || 'Incorrect OTP.');
                    }
                },
                // complete: function() {
                //     $('#overlay-loader').hide(); // Always hide loader
                //     $('#process_button').removeAttr('disabled');
                // },
                error: function(xhr) {
                    let errorMsg = 'OTP verification failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        }



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










         /*$(document).ready(function() {
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

          $('.toggle-password').click(function() {
    var target = $(this).data('target');
    var input = $(target);
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        $(this).html('<i class="fas fa-eye-slash"></i>');
    } else {
        input.attr('type', 'password');
        $(this).html('<i class="fas fa-eye"></i>');
    }
});

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#login-form").validate({
                rules: {
                    username: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },
                    captcha: {
                        required: true,
                    }
                },
                messages: {
                    username: {
                        required: "Enter username",
                    },
                    password: {
                        required: "Enter password",
                    },
                    captcha: {
                        required:'Enter Captcha',
                    }
                },
                submitHandler: function(form) {
                    // You can handle the form submission here (e.g., Ajax submission)
                    // form.submit();
                    loginuser()

                }
            });

        });
    </script>
       </script>
       <style>
   .toggle-password {
            top: 45%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        /* Default color for the eye icon */
        .toggle-password i {
            color: #5682b3;
            transition: color 0.3s ease;
        }
    </style>
</body>

</html>
