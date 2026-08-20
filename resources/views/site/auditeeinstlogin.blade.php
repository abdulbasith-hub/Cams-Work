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
        /* Example of responsive CSS */
        @media (max-width: 454px) {
            .containertest {
                margin-top: 20%;
            }

            .captcha-image {
                width: 100%;
                /* Ensure it fills the screen width */
                max-width: 120px !important;
                /* Limit the max width */
                height: auto;
                /* Maintain aspect ratio */
            }
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

</head>

<body>

    <!-- Preloader -->
    <div class="preloader">
        <img src="../site/image/tn__logo.png" alt="loader" class="lds-ripple img-fluid" />
    </div>

    @include('layouts.site_header')

    <div id="main-wrapper" class="auth-customizer-none">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center border border-grey "
            style="padding-top: calc(70px + 5rem);"> <!-- Adjust based on your navbar height -->
            <div class="d-flex align-items-center justify-content-center w-100 containertest">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a class="text-nowrap logo-img text-center d-block mb-1 w-100">
                                    <h2>Auditee HOD Login </h2>
                                </a>


                                <div id="display_error"class="alert alert-danger alert-dismissible fade show hide_this"
                                    role="alert"style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">
                                    <button type="button"
                                        class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
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
                                                <div id="captcha-box"
                                                    style="background-image: url('{{ asset('assets/images/backgrounds/captcha.jpg') }}');background-size: cover;width: 180px;height: 40px;display: flex;align-items: center;justify-content: center;font-size: 24px;font-weight: bold;letter-spacing: 4px; color: #2c2c2c;user-select: none;">
                                                </div>

                                                <!-- Reload Button -->
                                                <button type="button" onclick="refreshCaptcha()"
                                                    class="btn btn-primary" style="height: 40px;"><i
                                                        class="fas fa-sync-alt"></i></button>
                                            </div>

                                            <!-- Input field -->
                                            <input type="text" class="form-control" id="captcha" name="captcha"
                                                placeholder="Enter captcha code">
                                        </div>




                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check">
                                            </div>
                                            <a href="{{ url('/forgetpassword?user=hodlogin') }}"
                                                class="text-primary fw-medium">Forgot
                                                Password ?</a>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Sign
                                            In</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

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
                url: "{{ route('auditeeinst_validatelogin') }}",
                type: "POST",
                data: {
                    username: $('#username').val(),
                    encryptedPassword: encryptedPassword,
                    captcha: $('#captcha').val()
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
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


        function refreshCaptcha() {
            fetch('/captcha-text')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('captcha-box').innerText = data.code;
                    $('#captcha').val('');
                });

        }

        window.onload = refreshCaptcha;






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
                        required: 'Enter Captcha',
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

</body>

</html>
