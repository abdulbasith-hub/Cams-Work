<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAMS - Login</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/cams-public.css') }}">
</head>

<body class="cams-login-page">
    @include('layouts.site_header')
    @include('common.alert')

    <main class="cams-login-shell">
        <div class="container-fluid cams-container">
            <section class="cams-login-grid" aria-label="CAMS login">
                <aside class="cams-login-visual">
                    <div class="cams-login-copy-slider">
                        <div class="cams-login-copy-item is-active" data-login-panel="auditor" aria-hidden="false">
                            <span class="cams-login-badge">
                                <i class="fas fa-user-secret" aria-hidden="true"></i>
                                Auditor Workspace
                            </span>
                            <h1>Plan, execute and close audits with clarity.</h1>
                            <p>
                                Continue audit plans, inspections, slips, evidence review and report approval from a secure official workspace.
                            </p>
                        </div>
                        <div class="cams-login-copy-item" data-login-panel="auditee" aria-hidden="true">
                            <span class="cams-login-badge">
                                <i class="fas fa-building" aria-hidden="true"></i>
                                Auditee Access
                            </span>
                            <h1>Respond to audit observations on time.</h1>
                            <p>
                                View audit communication, submit replies, upload supporting records and follow compliance progress.
                            </p>
                        </div>
                        <div class="cams-login-copy-item" data-login-panel="hod" aria-hidden="true">
                            <span class="cams-login-badge">
                                <i class="fas fa-university" aria-hidden="true"></i>
                                HOD Review
                            </span>
                            <h1>Monitor institution replies and approvals.</h1>
                            <p>
                                Review department-level audit responses, pending actions and status movement across assigned institutions.
                            </p>
                        </div>
                    </div>
                    <div class="cams-login-steps" aria-hidden="true">
                        <span><strong>Plan</strong>Manage audit schedules and assignments.</span>
                        <span><strong>Execute</strong>Record audit observations and evidence.</span>
                        <span><strong>Report</strong>Track reports, replies and approvals.</span>
                    </div>
                </aside>

                <div class="cams-login-panel">
                    <div class="cams-login-card">
                        <div class="login-card-header">
                            {{-- <span class="login-card-icon">
                                <i class="fas fa-key" aria-hidden="true"></i>
                            </span> --}}
                            <h3 class="lang" key="auditorlogin">Auditor Login</h3>
                            {{-- <p>Use your CAMS credentials and captcha verification to enter the auditor workspace.</p> --}}
                        </div>

                        <nav class="login-role-switch" aria-label="Choose login type">
                            <button type="button" class="login-role-link active" data-login-role="auditor" aria-pressed="true">
                                <i class="fas fa-user-secret" aria-hidden="true"></i>
                                <span>Auditor</span>
                            </button>
                            <button type="button" class="login-role-link" data-login-role="auditee" aria-pressed="false">
                                <i class="fas fa-building" aria-hidden="true"></i>
                                <span>Auditee</span>
                            </button>
                            <button type="button" class="login-role-link" data-login-role="hod" aria-pressed="false">
                                <i class="fas fa-university" aria-hidden="true"></i>
                                <span>HOD</span>
                            </button>
                        </nav>

                        <form id="login-form" name="login-form" method="post" novalidate>
                            @csrf
                            <div class="cams-field">
                                <label for="username" class="form-label">Username</label>
                                <div class="cams-input-shell">
                                    <span class="cams-input-icon" aria-hidden="true">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="email" class="form-control" id="username" name="username" autocomplete="username" placeholder="Enter username">
                                </div>
                            </div>

                            <div class="cams-field">
                                <label for="password" class="form-label">Password</label>
                                <div class="cams-input-shell">
                                    <span class="cams-input-icon" aria-hidden="true">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" placeholder="Enter password">
                                    <button type="button" class="toggle-password" data-target="#password" aria-label="Show password">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="cams-field">
                                <label for="captcha" class="form-label">Captcha</label>
                                <div class="cams-captcha-row">
                                    <div id="captcha-box" aria-live="polite"></div>
                                    <button type="button" onclick="refreshCaptcha()" class="captcha-refresh-btn" aria-label="Refresh captcha">
                                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="cams-input-shell">
                                    <span class="cams-input-icon" aria-hidden="true">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter captcha code" autocomplete="off">
                                </div>
                            </div>

                            <div class="cams-login-actions">
                                <a href="{{ url('/forgetpassword?user=auditor') }}" class="cams-forgot-link">Forgot Password?</a>
                            </div>

                            <button type="submit" class="cams-submit-btn">Login</button>
                        </form>

                        <div id="display_error" class="cams-login-note cams-login-message" role="status" data-default-message="For security, keep your credentials confidential and complete OTP verification when prompted.">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            <span>For security, keep your credentials confidential and complete OTP verification when prompted.</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('layouts.site_footer')

    <div id="ajax-loader" aria-hidden="true">
        <div class="spinner"></div>
    </div>

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/captch-crypto.min.js') }}"></script>
    <script>
        window.APP_CONFIG = {
            AES_SECRET_KEY: "{{ config('app.aes_key') }}",
            AES_IV: "{{ config('app.aes_iv') }}"
        };

        window.CAMS_PUBLIC_LOGIN = {
            currentRole: "auditor",
            defaultVerifyUrl: "{{ route('verifyOtp_login') }}",
            roles: {
                auditor: {
                    title: "Auditor Login",
                    // copy: "Use your CAMS credentials and captcha verification to enter the auditor workspace.",
                    loginUrl: "{{ route('login') }}",
                    verifyUrl: "{{ route('verifyOtp_login') }}",
                    forgotUrl: "{{ url('/forgetpassword?user=auditor') }}"
                },
                auditee: {
                    title: "Auditee Login",
                    // copy: "Use institution credentials to continue replies, compliance and audit communication.",
                    loginUrl: "{{ route('auditee_validatelogin') }}",
                    verifyUrl: "{{ route('verifyOtp_auditeelogin') }}",
                    forgotUrl: "{{ url('/forgetpassword?user=auditee') }}"
                },
                hod: {
                    title: "Auditee HOD Login",
                    // copy: "Use HOD credentials to monitor department audit responses and follow-up work.",
                    loginUrl: "{{ route('auditeeinst_validatelogin') }}",
                    verifyUrl: null,
                    forgotUrl: "{{ url('/forgetpassword?user=hodlogin') }}"
                }
            }
        };
    </script>
    <script src="{{ asset('common/custom.js') }}"></script>
    <script src="{{ asset('site/js/cams-public.js') }}"></script>
</body>

</html>
