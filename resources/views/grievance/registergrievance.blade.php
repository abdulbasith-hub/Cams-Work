<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('common/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('site/css/cams-public.css') }}?v=20260727b" />


    <title>CAMS - Register Grievance</title>
    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
            background: #eef4f7;
        }

        .auth-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .auth-content {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: #eef4f7;
            margin-top: 0;
            padding: clamp(24px, 4vw, 46px) 12px;
        }

        .containertest {
            width: 100%;
        }

        .auth-card {
            width: 100%;
            max-width: 980px;
            margin: 12px auto 0;
        }

        .grievance-page-card {
            overflow: hidden;
            border: 1px solid #d8e4ed;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 16px 38px rgba(26, 49, 70, 0.16);
        }

        .grievance-page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 4px solid #0d9488;
            background: #075b93;
            color: #fff;
        }

        .grievance-header-icon {
            position: relative;
            flex: 0 0 auto;
            width: 54px;
            height: 54px;
            border-radius: 10px;
            background: #eef7e9;
            color: #d95252;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .grievance-header-icon .grievance-user-icon {
            color: #075b93;
        }

        .grievance-header-icon .grievance-alert-icon {
            position: absolute;
            right: 9px;
            top: 8px;
            color: #ef6b73;
            font-size: 17px;
        }

        .grievance-header-text {
            min-width: 0;
        }

        .grievance-page-kicker {
            display: inline-flex;
            margin-bottom: 4px;
            color: #d9f4ff;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .grievance-page-title {
            margin: 0;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .grievance-page-subtitle {
            margin: 4px 0 0;
            color: #eef7ff;
            font-size: 13px;
            line-height: 1.35;
        }

        .grievance-modal-close {
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.62);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 30px;
            line-height: 1;
            cursor: pointer;
        }

        .grievance-modal-close:hover,
        .grievance-modal-close:focus {
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
            outline: none;
            text-decoration: none;
        }

        .grievance-modal-close span {
            display: block;
            line-height: 30px;
            margin-top: -3px;
        }

        .grievance-form-shell {
            padding: 20px 22px 6px;
        }

        .grievance-intro {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding: 11px 13px;
            border: 1px solid #d7eceb;
            border-left: 4px solid #0d9488;
            border-radius: 6px;
            background: #f6fbfb;
            color: #31546a;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
        }

        .grievance-otp-box {
            margin-bottom: 18px;
            padding: 16px;
            border: 1px solid #dbeaf2;
            border-radius: 8px;
            background: #f8fbfd;
        }

        .grievance-otp-box.grievance-mobile-verified {
            border-color: #badfd5;
            background: #f7fcfa;
        }

        #grievanceForm label {
            display: block;
            margin-bottom: 6px;
            color: #233c55;
            font-size: 13px;
            font-weight: 700;
        }

        #grievanceForm label .grievance-required {
            display: inline;
            margin-left: 3px;
            color: #dc3545;
            font-weight: 800;
        }

        #grievanceForm .form-control,
        #grievanceForm .form-select {
            min-height: 40px;
            border: 1px solid #cfdce7;
            border-radius: 6px;
            background-color: #fff;
            color: #15293b;
            font-size: 14px;
            box-shadow: none;
            cursor: text;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #grievanceForm select.form-control,
        #grievanceForm .form-select,
        #grievanceForm input[type="file"] {
            cursor: default;
        }

        #grievanceForm textarea.form-control {
            min-height: 112px;
            resize: vertical;
            cursor: text;
        }

        #grievanceForm .form-control:focus,
        #grievanceForm .form-select:focus {
            border-color: #0067a8;
            box-shadow: 0 0 0 3px rgba(0, 103, 168, 0.13);
        }

        .grievance-mobile-inline,
        .grievance-otp-inline {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }

        .grievance-mobile-inline .form-control,
        .grievance-otp-inline .form-control {
            flex: 1 1 auto;
        }

        .grievance-icon-btn,
        .grievance-primary-btn {
            min-height: 38px;
            border: 0;
            border-radius: 5px;
            background: #0067a8;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .grievance-icon-btn {
            flex: 0 0 auto;
            width: 44px;
            min-width: 44px;
            padding: 0;
        }

        .grievance-primary-btn {
            min-width: 112px;
            padding: 0 16px;
        }

        .grievance-icon-btn:hover,
        .grievance-icon-btn:focus,
        .grievance-primary-btn:hover,
        .grievance-primary-btn:focus {
            background: #00558b;
            color: #fff;
            outline: none;
        }

        .grievance-icon-btn:disabled,
        .grievance-primary-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .grievance-verified-pill {
            display: none;
            flex: 0 0 auto;
            align-items: center;
            min-height: 38px;
            color: #087b5f;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .grievance-mobile-verified .grievance-verified-pill {
            display: inline-flex;
        }

        .grievance-mobile-verified .mb-3 {
            margin-bottom: 0 !important;
        }

        .grievance-otp-message {
            display: none;
            margin-top: 7px;
            font-size: 12px;
            font-weight: 700;
        }

        .grievance-otp-message:not(:empty) {
            display: block;
        }

        .grievance-otp-message.success {
            color: #087b5f;
        }

        .grievance-otp-message.error,
        #grievanceForm .text-danger {
            color: #dc3545 !important;
        }

        .grievance-otp-timer {
            display: none;
            margin-top: 6px;
            color: #8a5a00;
            font-size: 12px;
            font-weight: 800;
        }

        .grievance-otp-timer.show {
            display: block;
        }

        .grievance-otp-timer.expired {
            color: #c0392b;
        }

        .grievance-counter,
        .grievance-file-help {
            margin-top: 5px;
            color: #6a7f91;
            font-size: 12px;
            font-weight: 700;
        }

        .grievance-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 22px 18px;
            border-top: 1px solid #e8eef4;
            background: #fbfcfe;
        }

        #grievanceForm .form-control+span.text-danger,
        #grievanceForm .form-select+span.text-danger,
        #grievanceForm .text-danger.server-error {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            font-weight: 700;
        }

        .grievance-success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1085;
            align-items: center;
            justify-content: center;
            background: rgba(8, 25, 46, 0.22);
        }

        .grievance-success-overlay.show {
            display: flex;
        }

        .grievance-success-card {
            width: min(360px, calc(100vw - 32px));
            padding: 22px 20px 18px;
            border-radius: 8px;
            background: #fff;
            text-align: center;
            box-shadow: 0 18px 44px rgba(8, 25, 46, 0.28);
        }

        .grievance-success-icon {
            width: 46px;
            height: 46px;
            margin: 0 auto 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #087b5f;
            color: #fff;
            font-size: 20px;
        }

        .grievance-success-title {
            margin: 0 0 6px;
            color: #17324d;
            font-size: 18px;
            font-weight: 800;
        }

        .grievance-success-message {
            margin: 0 0 16px;
            color: #4d6175;
            font-size: 14px;
        }

        .toggle-password {
            top: 45%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .toggle-password i {
            color: #5682b3;
            transition: color 0.3s ease;
        }

        @media(max-width: 768px) {
            .auth-content {
                padding: 22px 10px 28px;
            }

            .grievance-page-header {
                align-items: flex-start;
                padding: 16px;
            }

            .grievance-header-icon {
                width: 48px;
                height: 48px;
            }

            .grievance-page-title {
                font-size: 20px;
            }

            .grievance-form-shell {
                padding: 16px 14px 4px;
            }

            .grievance-form-actions {
                padding: 12px 14px 16px;
            }
        }

        @media(max-width: 600px) {
            .grievance-mobile-inline,
            .grievance-otp-inline {
                display: block;
            }

            .grievance-icon-btn {
                width: 100%;
                margin-top: 8px;
            }

            .grievance-form-actions .grievance-primary-btn {
                flex: 1 1 0;
                min-width: 0;
            }

            .captcha-image {
                width: 100%;
                max-width: 250px !important;
                height: auto;
            }

        }

        /* Standalone grievance page style */
        .auth-content {
            padding: 34px 18px 36px;
            margin-top: 0;
            background: #edf3f6;
        }

        .auth-card {
            max-width: min(1480px, calc(100vw - 32px));
            margin-top: 0;
        }

        .grievance-page-card {
            overflow: hidden;
            border: 1px solid #c8d8e2;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(25, 44, 63, 0.14);
        }

        .grievance-page-header {
            position: relative;
            padding: 18px 22px;
            border-bottom: 4px solid #0d9488;
            background: #075b93;
            color: #fff;
        }

        .grievance-header-icon {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            background: #eef7fb;
            color: #075b93;
            font-size: 23px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.7);
        }

        .grievance-header-icon .grievance-alert-icon {
            color: #d44b4b;
        }

        .grievance-header-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .grievance-page-kicker {
            color: #d7f3ff;
            font-size: 11px;
            letter-spacing: 0;
        }

        .grievance-page-title {
            color: #fff;
            font-size: 23px;
            line-height: 1.2;
        }

        .grievance-page-subtitle {
            color: #e8f6fb;
        }

        .grievance-modal-close {
            border-color: #c8d6e0;
            background: #f7fafc;
            color: #51697c;
        }

        .grievance-modal-close:hover,
        .grievance-modal-close:focus {
            background: #e9f2f7;
            color: #183a53;
        }

        .grievance-form-shell {
            padding: 18px 20px 4px;
        }

        .grievance-intro {
            border-color: #d5e6ed;
            border-left-color: #075b93;
            background: #f7fbfd;
            color: #284b63;
        }

        .grievance-otp-box {
            padding: 18px;
            border-color: #cfdde7;
            border-radius: 6px;
            background: #ffffff;
        }

        .grievance-mobile-inline,
        .grievance-otp-inline {
            gap: 10px;
        }

        .grievance-icon-btn,
        .grievance-primary-btn {
            min-height: 40px;
            border-radius: 5px;
            background: #075b93;
        }

        .grievance-icon-btn {
            width: auto;
            min-width: 112px;
            padding: 0 14px;
            gap: 7px;
        }

        .grievance-primary-btn {
            min-width: 126px;
        }

        .grievance-icon-btn:hover,
        .grievance-icon-btn:focus,
        .grievance-primary-btn:hover,
        .grievance-primary-btn:focus {
            background: #064875;
        }

        #grievanceForm .form-control,
        #grievanceForm .form-select {
            min-height: 42px;
            border-color: #bdccd7;
        }

        .grievance-form-actions {
            padding: 14px 20px 18px;
            background: #f7fafc;
        }

        .grievance-verified-tabs {
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #d4e1e9;
            border-radius: 8px;
            background: #f4f8fb;
        }

        .grievance-tab-list {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            padding: 5px;
            border: 1px solid #d4e1e9;
            border-radius: 7px;
            background: #fff;
        }

        .grievance-tab-btn {
            flex: 1 1 0;
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 5px;
            background: transparent;
            color: #31546a;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        }

        .grievance-tab-btn.active {
            background: #075b93;
            color: #fff;
            box-shadow: 0 6px 14px rgba(7, 91, 147, 0.22);
        }

        .grievance-tab-pane {
            display: none;
            padding: 16px;
            border: 1px solid #dbe7ee;
            border-radius: 7px;
            background: #fff;
            animation: grievanceSlideIn 0.22s ease;
        }

        .grievance-tab-pane.active {
            display: block;
        }

        @keyframes grievanceSlideIn {
            from {
                opacity: 0;
                transform: translateX(14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .grievance-status-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .grievance-status-title {
            color: #17384f;
            font-size: 15px;
            font-weight: 800;
        }

        .grievance-light-btn {
            min-height: 36px;
            padding: 0 14px;
            border: 1px solid #b7cbd8;
            border-radius: 5px;
            background: #fff;
            color: #075b93;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .grievance-light-btn:hover,
        .grievance-light-btn:focus {
            background: #eef6fb;
            outline: none;
        }

        .grievance-status-table-wrap {
            overflow-x: auto;
            border: 1px solid #d5e1e9;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 18px rgba(25, 44, 63, 0.08);
        }

        .grievance-status-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            color: #263f52;
            font-size: 13px;
        }

        .grievance-status-table th {
            padding: 12px;
            background: #075b93;
            color: #fff;
            font-weight: 800;
            white-space: nowrap;
            border-right: 1px solid rgba(255, 255, 255, 0.22);
            text-align: left;
        }

        .grievance-status-table th:last-child {
            border-right: 0;
        }

        .grievance-status-table td {
            padding: 11px 12px;
            border-top: 1px solid #e2ebf0;
            vertical-align: top;
            background: #fff;
        }

        .grievance-status-table tbody tr:nth-child(even) td {
            background: #f7fafc;
        }

        .grievance-status-table tbody tr:hover td {
            background: #eef7fb;
        }

        .grievance-status-table .status-col-ticket {
            width: 14%;
        }

        .grievance-status-table .status-col-date {
            width: 16%;
        }

        .grievance-status-table .status-col-dept {
            width: 24%;
        }

        .grievance-status-table .status-col-category {
            width: 32%;
        }

        .grievance-status-table .status-col-status {
            width: 14%;
        }

        .grievance-status-ticket {
            color: #075b93;
            font-weight: 800;
        }

        .grievance-status-badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e9f7ef;
            color: #267348;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .grievance-status-empty {
            color: #6b7f8e;
            font-weight: 700;
            text-align: center;
        }

        @media(max-width: 768px) {
            .auth-content {
                padding: 22px 10px 26px;
            }

            .auth-card {
                max-width: calc(100vw - 24px);
            }

            .grievance-page-header {
                gap: 10px;
                padding: 14px;
            }

            .grievance-tab-list {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .grievance-tab-btn {
                padding: 0 10px;
            }
        }

        @media(max-width: 600px) {
            .grievance-icon-btn {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>

<body class="cams-public-page cams-grievance-page">

    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('site/image/tn__logo.png') }}" alt="loader" class="lds-ripple img-fluid" />
    </div>

    @include('layouts.site_header')
    @include('common.alert')

    <main id="main-wrapper" class="auth-customizer-none auth-wrapper">
        <div class="auth-content">

            <div class="d-flex align-items-start justify-content-center w-100 containertest">
                <div class="row justify-content-center w-100">
                    <div class="col-12 auth-card">

                        <div class="grievance-page-card">
                            <form id="grievanceForm" enctype="multipart/form-data">
                                @csrf

                                <div class="grievance-page-header">
                                    <span class="grievance-header-icon" aria-hidden="true">
                                        <i class="fas fa-user grievance-user-icon"></i>
                                        <i class="fas fa-exclamation-circle grievance-alert-icon"></i>
                                    </span>
                                    <div class="grievance-header-text">
                                        <span class="grievance-page-kicker">Comprehensive Audit Management System</span>
                                        <h4 class="grievance-page-title">Register Grievance</h4>
                                        {{-- <p class="grievance-page-subtitle">
                                            Verify your mobile number first, then submit your grievance details.
                                        </p> --}}
                                    </div>

                                </div>

                                <div class="grievance-form-shell">
                                        <div class="grievance-intro" id="grievanceIntro">
                                            <i class="fas fa-mobile-alt" aria-hidden="true"></i>
                                            <span>Verify Your Mobile Number.</span>
                                        </div>

                                        <div class="grievance-otp-box" id="grievanceOtpBox">
                                            <div class="row">
                                                <div class="col-md-6 mb-3 mb-md-0">
                                                    <label>Mobile Number <span class="grievance-required">*</span></label>
                                                    <div class="grievance-mobile-inline">
                                                        <input type="text" name="mobilenumber"
                                                            class="only_numbers form-control removesplchar_number"
                                                            id="grievanceMobileNumber" maxlength="10"
                                                            placeholder="Enter 10 digit mobile number" required>
                                                        <button type="button" class="grievance-icon-btn"
                                                            id="sendGrievanceOtpBtn" title="Send OTP"
                                                            aria-label="Send OTP">
                                                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                                                            <span>Send OTP</span>
                                                        </button>
                                                        <span class="grievance-verified-pill">Mobile number
                                                            verified</span>
                                                    </div>
                                                    <span class="grievance-otp-timer" id="grievanceOtpTimer"></span>
                                                    <input type="hidden" id="grievanceVerifiedMobile"
                                                        class="ignore-validation">
                                                </div>

                                                <div class="col-md-6 d-none" id="grievanceOtpVerifyRow">
                                                    <label>Enter OTP <span class="grievance-required">*</span></label>
                                                    <div class="grievance-otp-inline">
                                                        <input type="text" class="only_numbers form-control"
                                                            id="grievanceOtp" maxlength="6"
                                                            placeholder="Enter 6 digit OTP" required>
                                                        <button type="button" class="grievance-icon-btn"
                                                            id="verifyGrievanceOtpBtn" title="Verify OTP"
                                                            aria-label="Verify OTP">
                                                            <i class="fas fa-check" aria-hidden="true"></i>
                                                            <span>Verify OTP</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="grievance-otp-message" id="grievanceOtpMessage"></span>
                                        </div>

                                        <div class="grievance-verified-tabs d-none" id="grievanceVerifiedTabs">
                                            <div class="grievance-tab-list" role="tablist">
                                                <button type="button" class="grievance-tab-btn active"
                                                    data-grievance-tab="new" role="tab" aria-selected="true">
                                                    Register
                                                </button>
                                                {{-- <button type="button" class="grievance-tab-btn "
                                                    data-grievance-tab="status" role="tab" aria-selected="false">
                                                    Know Your Status
                                                </button> --}}
                                            </div>

                                            <div class="grievance-tab-pane active" id="grievanceNewRegisterPane">
                                                <div class="row" id="grievanceDetailsSection">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Name <span class="grievance-required">*</span></label>

                                                        <input type="text" name="name"
                                                            class="form-control removesplchar_text name"
                                                            placeholder="Enter your name" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Email</label>
                                                        <input type="text" name="email" id="email"
                                                            class="form-control" placeholder="Enter email address">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Department <span class="grievance-required">*</span></label>
                                                        <select name="deptcode" class="form-select form-control" required>
	                                                            <option value="">Select Department</option>
	                                                            @foreach ($departments ?? [] as $dept)
		                                                                <option value="{{ $dept->deptesname }}">
		                                                                    {{ $dept->deptelname }}
		                                                                </option>
	                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Category <span class="grievance-required">*</span></label>
                                                        <select name="category" id="category"
                                                            class="form-select form-control" required>
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories ?? [] as $category)
                                                                <option value="{{ $category->grievancecatid }}">
                                                                    {{ $category->grievancecatename }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Upload File</label>
                                                        <input type="file" name="file" id="file"
                                                            class="form-control">
                                                        <small class="grievance-file-help">
                                                            JPG or PDF (Maximum 200 KB)
                                                        </small>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label>Description <span class="grievance-required">*</span></label>
                                                        <textarea class="form-control" id="description" name="description" maxlength="750" rows="5"
                                                            placeholder="Briefly explain your grievance" required></textarea>
                                                        <div class="text-end grievance-counter">
                                                            <span id="charCount">0</span>/750
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grievance-tab-pane" id="grievanceStatusPane">
                                                <div class="grievance-status-tools">
                                                    <span class="grievance-status-title">Submitted Grievances</span>
                                                    <button type="button" class="grievance-light-btn"
                                                        id="refreshGrievanceStatusBtn">
                                                        Refresh
                                                    </button>
                                                </div>
                                                <div class="grievance-status-table-wrap">
                                                    <table class="grievance-status-table">
                                                        <thead>
                                                            <tr>
	                                                                <th class="status-col-ticket">Ticket No</th>
	                                                                <th class="status-col-date">Date</th>
	                                                                <th class="status-col-dept">Department</th>
	                                                                <th class="status-col-category">Category</th>
	                                                                <th class="status-col-status">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="grievanceStatusTableBody">
                                                            <tr>
                                                                <td colspan="5" class="grievance-status-empty">
                                                                    Verify mobile number to view status.
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="grievance-form-actions d-none" id="grievanceSubmitFooter">
                                    <button type="submit" id="btnSubmit"
                                        class="grievance-primary-btn grievance-submit-btn">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="grievance-success-overlay" id="grievanceSuccessPopup" role="dialog"
                            aria-modal="true" aria-labelledby="grievanceSuccessTitle">
                            <div class="grievance-success-card">
                                <span class="grievance-success-icon" aria-hidden="true">
                                    <i class="fas fa-check"></i>
                                </span>
                                <h5 class="grievance-success-title" id="grievanceSuccessTitle">Grievance Submitted successfully</h5>
                                <p class="grievance-success-message" id="grievanceSuccessMessage">
                                    Grievance submitted successfully.
                                </p>
                                <button type="button" class="grievance-primary-btn" id="grievanceSuccessOkBtn"
                                    formnovalidate>
                                    OK
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- <div id="ajax-loader">
            <div class="spinner"></div>
        </div> --}}
    </main>

    @include('layouts.site_footer')

    <script>
        function handleColorTheme(e) {
            document.documentElement.setAttribute("data-color-theme", e);
        }
    </script>


    <!-- Import Js Files -->
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="{{ asset('site/js/cams-public.js') }}"></script>

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


    <script>
        var grievanceRequest = null;
        var grievanceClosing = false;
        var grievanceStatusLoaded = false;
        var grievanceOtpTimer = null;
        var grievanceOtpRemainingSeconds = 0;
        var grievanceOtpExpired = true;
        var GRIEVANCE_OTP_VALID_SECONDS = 300;
        var grievanceMobileVerified = false;
        var grievanceVerificationIdleTimer = null;
        var GRIEVANCE_VERIFICATION_IDLE_MS = 180000; // 3 minutes



        $("#file").on("change", function() {

            var file = this.files[0];

            if (!file) {
                return;
            }

            var allowedExtensions = ['jpg', 'pdf'];
            var extension = file.name.split('.').pop().toLowerCase();

            if ($.inArray(extension, allowedExtensions) === -1) {

                alert("Only JPG and PDF files are allowed.");

                $(this).val("");

                return;
            }

            if (file.size > 204800) {

                alert("Maximum file size is 200 KB.");

                $(this).val("");

                return;
            }

        });


        // $("#grievanceForm").on("submit", function(e) {
        //     e.preventDefault();
        // });


        restrictSpecialChars(".removesplchar_text");
        restrictSpecialChars(".removesplchar_number");

        function restrictSpecialChars(selector) {

            // Common SQL keywords — if any of these appear as a full word, null out the whole field
            const sqlKeywords = [
                'select', 'insert', 'update', 'delete', 'drop', 'truncate',
                'alter', 'create', 'union', 'exec', 'execute', 'declare',
                'from', 'where', 'table', 'database', 'grant', 'revoke',
                'having', 'join', 'into', 'values', 'cursor', 'trigger',
                'procedure', 'shutdown', 'backup', 'restore', 'merge'
            ];

            const sqlKeywordRegex = new RegExp(
                '\\b(' + sqlKeywords.join('|') + ')\\b', 'gi'
            );

            $(selector)
                .off("keypress paste drop input")

                // Keep keypress for the char/length restrictions (fast feedback)
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;

                    if (selector === '.removesplchar_text') {

                        if (!/^[a-zA-Z\u0B80-\u0BFF ]$/.test(char)) {
                            event.preventDefault();
                            return;
                        }

                        if (value.length >= 70) {
                            event.preventDefault();
                            return;
                        }

                    } else if (selector === '.removesplchar_number') {

                        if (!/^[0-9]$/.test(char)) {
                            event.preventDefault();
                            return;
                        }

                        let newValue = value + char;

                        if (newValue.length === 1 && !/^[6-9]$/.test(char)) {
                            event.preventDefault();
                            return;
                        }

                        if (/(.)\1{5,}$/.test(newValue)) {
                            event.preventDefault();
                            return;
                        }

                        if (value.length >= 10) {
                            event.preventDefault();
                            return;
                        }
                    }
                })

                .on("input", function() {
                    let input = this;
                    let value = input.value;
                    let cleaned = value;

                    if (selector === '.removesplchar_text') {

                        cleaned = value.replace(/[^a-zA-Z\u0B80-\u0BFF ]/g, '');
                        cleaned = cleaned.substring(0, 70);

                        sqlKeywordRegex.lastIndex = 0;
                        if (sqlKeywordRegex.test(cleaned)) {
                            cleaned = '';
                        }

                    } else if (selector === '.removesplchar_number') {

                        cleaned = value.replace(/[^0-9]/g, '');

                        while (cleaned.length > 0 && !/^[6-9]/.test(cleaned)) {
                            cleaned = cleaned.substring(1);
                        }

                        while (/(.)\1{5,}/.test(cleaned)) {
                            cleaned = cleaned.slice(0, -1);
                        }

                        cleaned = cleaned.substring(0, 10);
                    }

                    if (cleaned !== value) {
                        input.value = cleaned;

                        input.setSelectionRange(cleaned.length, cleaned.length);
                    }
                })

                .on("drop", function(e) {
                    let dt = (e.originalEvent || e).dataTransfer;
                    if (dt && dt.types && dt.types.includes('text/plain')) {
                        return;
                    }
                    e.preventDefault();
                });
        }

        function resetGrievanceSubmitButton() {
            $("#btnSubmit").prop("disabled", false).text("Submit");
        }

        function setSendOtpButton(isSending) {
            $("#sendGrievanceOtpBtn")
                .prop("disabled", isSending)
                .html(isSending ? '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Sending</span>' :
                    '<i class="fas fa-paper-plane" aria-hidden="true"></i><span>Send OTP</span>');
        }

        function setVerifyOtpButton(isVerifying) {
            $("#verifyGrievanceOtpBtn")
                .prop("disabled", isVerifying)
                .html(isVerifying ? '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Verifying</span>' :
                    '<i class="fas fa-check" aria-hidden="true"></i><span>Verify OTP</span>');
        }

        function formatGrievanceOtpTime(totalSeconds) {
            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;

            return String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0");
        }

        function updateGrievanceOtpTimerDisplay() {
            $("#grievanceOtpTimer")
                .removeClass("expired")
                .addClass("show")
                .text("OTP expires in " + formatGrievanceOtpTime(grievanceOtpRemainingSeconds));
        }

        function clearGrievanceOtpTimer() {
            if (grievanceOtpTimer) {
                clearInterval(grievanceOtpTimer);
                grievanceOtpTimer = null;
            }

            grievanceOtpRemainingSeconds = 0;
            grievanceOtpExpired = true;
            $("#grievanceOtpTimer").removeClass("show expired").text("");
            $("#sendGrievanceOtpBtn").prop("disabled", false);
        }

        function expireGrievanceOtpTimer() {
            if (grievanceOtpTimer) {
                clearInterval(grievanceOtpTimer);
                grievanceOtpTimer = null;
            }

            grievanceOtpRemainingSeconds = 0;
            grievanceOtpExpired = true;
            $("#grievanceOtpTimer")
                .addClass("show expired")
                .text("OTP expired. Send OTP again.");
            setSendOtpButton(false);
        }

        function startGrievanceOtpTimer() {
            if (grievanceOtpTimer) {
                clearInterval(grievanceOtpTimer);
            }

            grievanceOtpRemainingSeconds = GRIEVANCE_OTP_VALID_SECONDS;
            grievanceOtpExpired = false;
            setSendOtpButton(false);
            $("#sendGrievanceOtpBtn").prop("disabled", true);
            updateGrievanceOtpTimerDisplay();

            grievanceOtpTimer = setInterval(function() {
                grievanceOtpRemainingSeconds--;

                if (grievanceOtpRemainingSeconds <= 0) {
                    expireGrievanceOtpTimer();
                    return;
                }

                updateGrievanceOtpTimerDisplay();
            }, 1000);
        }

        function setGrievanceOtpMessage(message, type) {
            $("#grievanceOtpMessage")
                .removeClass("success error")
                .addClass(type || "")
                .text(message || "");
        }

        function showGrievanceSuccessPopup(message) {
            $("#grievanceSuccessMessage").text(message || "Grievance submitted successfully.");
            $("#grievanceSuccessPopup").addClass("show");
            $("#grievanceSuccessOkBtn").focus();
        }

        function hideGrievanceSuccessPopup() {
            $("#grievanceSuccessPopup").removeClass("show");
        }

        function clearGrievanceVerificationSession() {
            $.ajax({
                url: "{{ route('grievance.clear_verification') }}",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
        }

        function stopGrievanceVerificationIdleTimer() {
            if (grievanceVerificationIdleTimer) {
                clearTimeout(grievanceVerificationIdleTimer);
                grievanceVerificationIdleTimer = null;
            }
        }

        function expireGrievanceVerification() {
            if (!grievanceMobileVerified) {
                return;
            }

            grievanceMobileVerified = false;
            stopGrievanceVerificationIdleTimer();
            clearGrievanceVerificationSession();
            resetGrievanceFormState();
            setGrievanceOtpMessage("Mobile number verification expired due to inactivity. Please verify again.", "error");
        }

        function startGrievanceVerificationIdleTimer() {
            if (!grievanceMobileVerified) {
                return;
            }

            stopGrievanceVerificationIdleTimer();
            grievanceVerificationIdleTimer = setTimeout(expireGrievanceVerification, GRIEVANCE_VERIFICATION_IDLE_MS);
        }

        function resetGrievanceFormState() {
            grievanceMobileVerified = false;
            stopGrievanceVerificationIdleTimer();

            if ($("#grievanceForm").length && $("#grievanceForm")[0]) {
                $("#grievanceForm")[0].reset();
            }

            var validator = $("#grievanceForm").data("validator");
            if (validator) {
                validator.resetForm();
            }

            $("#grievanceForm")
                .find(".form-control, .form-select")
                .removeClass("error text-danger is-invalid");

            $(".server-error").remove();
            clearGrievanceOtpTimer();
            $("#charCount").text("0");
            $("#grievanceMobileNumber").prop({
                readonly: false,
                disabled: false
            });
            $("#grievanceVerifiedMobile").val("");
            $("#sendGrievanceOtpBtn").removeClass("d-none").attr("title", "Send OTP").attr("aria-label", "Send OTP");
            $("#grievanceOtpVerifyRow").addClass("d-none");
            $("#grievanceVerifiedTabs").addClass("d-none");
            $("#grievanceDetailsSection").addClass("d-none");
            $("#grievanceSubmitFooter").addClass("d-none");
            $("#grievanceStatusTableBody").html(
                '<tr><td colspan="5" class="grievance-status-empty">Verify mobile number to view status.</td></tr>'
            );
            grievanceStatusLoaded = false;
            $("#grievanceOtpBox").removeClass("grievance-mobile-verified");
            $("#grievanceOtp").val("");
            setGrievanceOtpMessage("", "");
            setSendOtpButton(false);
            setVerifyOtpButton(false);
            resetGrievanceSubmitButton();
            $("#grievanceIntro").removeClass("d-none");
            switchGrievanceTab("new");
        }

        function resetGrievanceNewRegisterFields() {
            $("#grievanceNewRegisterPane")
                .find("input, select, textarea")
                .val("")
                .removeClass("error text-danger is-invalid");

            var validator = $("#grievanceForm").data("validator");
            if (validator) {
                validator.resetForm();
            }

            $(".server-error").remove();
            $("#charCount").text("0");
            setGrievanceOtpMessage("", "");
            resetGrievanceSubmitButton();
        }

        function showGrievanceDetailsAfterOtp() {
            var mobileNumber = $("#grievanceMobileNumber").val();
            grievanceMobileVerified = true;
            $("#grievanceMobileNumber").prop({
                readonly: true,
                disabled: false
            });
            $("#grievanceVerifiedMobile").val(mobileNumber);
            $("#sendGrievanceOtpBtn").addClass("d-none");
            $("#grievanceOtpVerifyRow").addClass("d-none");
            clearGrievanceOtpTimer();
            $("#grievanceVerifiedTabs").removeClass("d-none");
            $("#grievanceDetailsSection").removeClass("d-none");
            $("#grievanceSubmitFooter").removeClass("d-none");
            $("#grievanceOtpBox").addClass("grievance-mobile-verified");
            $("#grievanceIntro").addClass("d-none");
            grievanceStatusLoaded = false;
            switchGrievanceTab("new");
            setGrievanceOtpMessage("", "");
            startGrievanceVerificationIdleTimer();
        }

        function escapeGrievanceText(value) {
            return $("<div>").text(value == null || value === "" ? "-" : value).html();
        }

        function renderGrievanceStatusRows(rows) {
            if (!rows || !rows.length) {
                $("#grievanceStatusTableBody").html(
                    '<tr><td colspan="5" class="grievance-status-empty">No data found.</td></tr>'
                );
                return;
            }

            var html = rows.map(function(row) {
                return '<tr>' +
                    '<td class="grievance-status-ticket">' + escapeGrievanceText(row.tktno) + '</td>' +
                    '<td>' + escapeGrievanceText(row.createdon) + '</td>' +
                    '<td>' + escapeGrievanceText(row.department) + '</td>' +
                    '<td>' + escapeGrievanceText(row.categoryname) + '</td>' +
                    '<td><span class="grievance-status-badge">' + escapeGrievanceText(row.status_label) + '</span></td>' +
                    '</tr>';
            }).join("");

            $("#grievanceStatusTableBody").html(html);
        }

        function loadGrievanceStatus(forceReload) {
            if (grievanceStatusLoaded && !forceReload) {
                return;
            }

            $("#grievanceStatusTableBody").html(
                '<tr><td colspan="5" class="grievance-status-empty">Loading grievance status...</td></tr>'
            );

            $.ajax({
                url: "{{ route('grievance.status') }}",
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    if (response.status) {
                        grievanceStatusLoaded = true;
                        renderGrievanceStatusRows(response.data || []);
                    } else {
                        $("#grievanceStatusTableBody").html(
                            '<tr><td colspan="5" class="grievance-status-empty">' +
                            escapeGrievanceText(response.message || "Unable to load grievance status.") +
                            '</td></tr>'
                        );
                    }
                },
                error: function(xhr) {
                    var message = "Unable to load grievance status.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $("#grievanceStatusTableBody").html(
                        '<tr><td colspan="5" class="grievance-status-empty">' +
                        escapeGrievanceText(message) +
                        '</td></tr>'
                    );
                }
            });
        }

        function switchGrievanceTab(tabName) {
            $(".grievance-tab-btn").removeClass("active").attr("aria-selected", "false");
            $('.grievance-tab-btn[data-grievance-tab="' + tabName + '"]')
                .addClass("active")
                .attr("aria-selected", "true");

            $(".grievance-tab-pane").removeClass("active");

            if (tabName === "status") {
                $("#grievanceStatusPane").addClass("active");
                $("#grievanceSubmitFooter").addClass("d-none");
                loadGrievanceStatus(false);
                return;
            }

            $("#grievanceNewRegisterPane").addClass("active");
            if (!$("#grievanceVerifiedTabs").hasClass("d-none")) {
                $("#grievanceSubmitFooter").removeClass("d-none");
            }
        }

        $(document).on("click", "#grievanceSuccessOkBtn", function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            hideGrievanceSuccessPopup();
            resetGrievanceNewRegisterFields();
            grievanceStatusLoaded = false;
            switchGrievanceTab("status");
            return false;
        });

        $(document).on("mousemove keydown mousedown touchstart scroll input", function() {
            startGrievanceVerificationIdleTimer();
        });

        $(document).on("click", ".grievance-tab-btn", function() {
            switchGrievanceTab($(this).data("grievance-tab"));
        });

        $(document).on("click", "#refreshGrievanceStatusBtn", function() {
            loadGrievanceStatus(true);
        });

        $(".only_numbers").on("keypress", function(event) {
            return event.charCode >= 48 && event.charCode <= 57;
        });

        $(".name").on("keypress", function(event) {
            return (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                event.charCode == 32
            );
        });

        $(document).on("keypress", "#email", function(event) {
            var regex = /^[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~.@]+$/;
            var key = String.fromCharCode(event.which);
            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        $(document).ready(function() {
            $("#description").on("input", function() {
                $("#charCount").text($(this).val().length);
            });

            $.validator.addMethod("lettersOnly", function(value, element) {
                return this.optional(element) || /^[A-Za-z ]+$/.test(value);
            }, "Only letters are allowed.");

            $.validator.addMethod("emailRegex", function(value, element) {
                return this.optional(element) ||
                    /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
            }, "Enter valid email.");

            $.validator.addMethod("mobileNo", function(value, element) {
                return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
            }, "Enter valid mobile number.");


            $.validator.addMethod("safeDescription", function(value, element) {

                if (this.optional(element)) {
                    return true;
                }

                console.log("safeDescription:", value);

                // Block XSS & SQL keywords
                if (/(<\s*script\b|<\/\s*script\b|\bselect\s|\binsert\s|\bupdate\s|\bdelete\s|\bdrop\s|\btruncate\s|\balter\s|\bunion\b|\bexec\b|\bexecute\b|\bjavascript:|--|;)/i
                    .test(value)) {
                    return false;
                }


                // Prevent repeated characters
                if (/([A-Za-z0-9])\1{4,}/.test(value)) {
                    return false;
                }

                // Allow only valid characters
                if (!/^[A-Za-z0-9\s.,:;'"\-()\/&\r\n]*$/.test(value)) {
                    return false;
                }

                return true;

            }, "Description contains invalid or unsafe content.");

            $.validator.addMethod("filesize", function(value, element, param) {
                if (element.files.length == 0) {
                    return true;
                }

                return element.files[0].size <= param;
            }, "Maximum file size is 200 KB.");

            $.validator.addMethod("fileExtension", function(value, element) {
                if (element.files.length == 0) {
                    return true;
                }

                var fileName = element.files[0].name.toLowerCase();
                return /\.(jpg|pdf)$/.test(fileName);
            }, "Only JPG and PDF allowed.");

            $("#grievanceForm").validate({
                errorElement: "span",
                errorClass: "text-danger",
                ignore: ".ignore-validation",

                errorPlacement: function(error, element) {
                    if (element.attr("name") === "mobilenumber") {
                        setGrievanceOtpMessage(error.text(), "error");
                        return;
                    }

                    error.insertAfter(element);
                },

                success: function(label, element) {
                    if ($(element).attr("name") === "mobilenumber") {
                        setGrievanceOtpMessage("", "");
                    }
                },

                rules: {
                    name: {
                        required: true,
                        maxlength: 70,
                        lettersOnly: true
                    },
                    email: {
                        required: false,
                        maxlength: 30,
                        emailRegex: true
                    },
                    mobilenumber: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10,
                        mobileNo: true
                    },
                    deptcode: {
                        required: true
                    },
                    category: {
                        required: true
                    },
                    description: {
                        required: true,
                        maxlength: 750,
                        safeDescription: true
                    },
                    file: {
                        fileExtension: true,
                        filesize: 204800
                    }
                },

                messages: {
                    name: {
                        required: "Please Enter Name"
                    },
                    mobilenumber: {
                        required: "Please Enter Mobile Number",
                        minlength: "Enter valid 10 digit mobile number.",
                        maxlength: "Enter valid 10 digit mobile number."
                    },
                    deptcode: {
                        required: "Please select department"
                    },
                    category: {
                        required: "Please select category"
                    },
                    description: {
                        required: "Please Enter Description"
                    }
                },

                submitHandler: function(form) {
                    grievanceClosing = false;
                    $(".server-error").remove();

                    var formData = new FormData(form);

                    $("#btnSubmit")
                        .prop("disabled", true)
                        .text("Submitting...");

                    grievanceRequest = $.ajax({
                        url: "{{ route('save.grievance') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response) {
                            if (response.status) {
                                grievanceMobileVerified = false;
                                stopGrievanceVerificationIdleTimer();
                                showGrievanceSuccessPopup(response.message);
                            } else {
                                setGrievanceOtpMessage(response.message ||
                                    "Unable to submit grievance.", "error");
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 0 || grievanceClosing) {
                                return;
                            }

                            if (xhr.status == 422 && xhr.responseJSON && xhr.responseJSON
                                .errors) {
                                $(".server-error").remove();
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('[name="' + key + '"]').last().after(
                                        '<span class="text-danger server-error">' +
                                        value[0] + '</span>'
                                    );
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                setGrievanceOtpMessage(xhr.responseJSON.message, "error");
                            } else {
                                setGrievanceOtpMessage("Server Error", "error");
                            }
                        },
                        complete: function() {
                            grievanceRequest = null;
                            grievanceClosing = false;
                            resetGrievanceSubmitButton();
                        }
                    });

                    return false;
                }
            });
        });

        $(document).on("click", "#sendGrievanceOtpBtn", function() {
            $(".server-error").remove();

            if (!$("#grievanceForm").validate().element("#grievanceMobileNumber")) {
                return;
            }

            setSendOtpButton(true);
            setGrievanceOtpMessage("", "");

            $.ajax({
                url: "{{ route('grievance.send_otp') }}",
                type: "POST",
                data: {
                    mobilenumber: $("#grievanceMobileNumber").val()
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    if (response.status) {
                        $("#grievanceOtpVerifyRow").removeClass("d-none");
                        $("#sendGrievanceOtpBtn").attr("title", "Resend OTP").attr("aria-label",
                            "Resend OTP");
                        $("#grievanceOtp").val("");
                        startGrievanceOtpTimer();
                        setGrievanceOtpMessage(response.message || "OTP has been sent successfully.",
                            "success");
                        $("#grievanceOtp").focus();
                    } else {
                        setGrievanceOtpMessage(response.message || "Unable to send OTP.", "error");
                    }
                },
                error: function(xhr) {
                    if (xhr.status == 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        setGrievanceOtpMessage(xhr.responseJSON.errors.mobilenumber[0], "error");
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        setGrievanceOtpMessage(xhr.responseJSON.message, "error");
                    } else {
                        setGrievanceOtpMessage("Unable to send OTP.", "error");
                    }
                },
                complete: function() {
                    if (grievanceOtpExpired) {
                        setSendOtpButton(false);
                    } else {
                        $("#sendGrievanceOtpBtn").prop("disabled", true);
                    }
                }
            });
        });

        $(document).on("click", "#verifyGrievanceOtpBtn", function() {
            var mobileNumber = $("#grievanceMobileNumber").val();
            var otp = $("#grievanceOtp").val();

            if (!/^[6-9][0-9]{9}$/.test(mobileNumber)) {
                setGrievanceOtpMessage("Enter valid 10 digit mobile number.", "error");
                return;
            }

            if (!/^[0-9]{6}$/.test(otp)) {
                setGrievanceOtpMessage("Enter valid 6 digit OTP.", "error");
                return;
            }

            if (grievanceOtpExpired || grievanceOtpRemainingSeconds <= 0) {
                expireGrievanceOtpTimer();
                setGrievanceOtpMessage("OTP expired. Please send OTP again.", "error");
                return;
            }

            setVerifyOtpButton(true);
            setGrievanceOtpMessage("", "");

            $.ajax({
                url: "{{ route('grievance.verify_otp') }}",
                type: "POST",
                data: {
                    mobilenumber: mobileNumber,
                    otp: otp
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    if (response.status) {
                        showGrievanceDetailsAfterOtp();
                    } else {
                        setGrievanceOtpMessage(response.message || "Unable to verify OTP.", "error");
                    }
                },
                error: function(xhr) {
                    if (xhr.status == 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        setGrievanceOtpMessage((errors.otp || errors.mobilenumber)[0], "error");
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        setGrievanceOtpMessage(xhr.responseJSON.message, "error");
                    } else {
                        setGrievanceOtpMessage("Unable to verify OTP.", "error");
                    }
                },
                complete: function() {
                    setVerifyOtpButton(false);
                }
            });
        });
    </script>
</body>

</html>
