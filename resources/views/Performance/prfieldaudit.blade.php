@section('content')
@section('title', 'Performance Field Audit')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';
    $instdel = json_decode($scheduledel, true);
@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<style>
    .ck-editor__editable_disabled {
        background-color: #e9ecef !important;
        opacity: 0.65;
        cursor: not-allowed;
    }

    .ck-editor__editable_disabled .ck-content {
        background-color: #e9ecef !important;
        color: #6c757d;
    }

    /* Disabled state for the entire editor */
    .ck-editor__editable[contenteditable="false"] {
        background-color: #e9ecef !important;
        opacity: 0.65;
    }

    /* Style for disabled buttons */
    .btn.disabled,
    .btn:disabled,
    button.disabled,
    button:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        pointer-events: none;
    }

    .ck-editor__editable {
        max-height: 300px;
        /* Set the max height as per your requirement */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }


    .ck-powered-by-balloon {
        display: none !important;
    }

    #container {
        width: 1000px;
        margin: 20px auto;
    }

    .ck-editor__editable[role="textbox"] {
        min-height: 200px;
    }

    .ck-editor__editable {
        font-family: 'Marutham', sans-serif;
    }

    .content-cell {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        /* Show only 2 lines */
        overflow: hidden;
        text-overflow: ellipsis;
        height: 40px;
        /* Adjust this based on your line height */
        line-height: 20px;
        /* Set this to match your text height */
        white-space: normal;
        /* Allow wrapping */
    }


    #container {
        width: 1000px;
        margin: 20px auto;
    }

    .card-fixed-width {
        width: 300px;
        /* Adjust to your preferred fixed width */
        max-width: 100%;
        /* Ensures it doesn't exceed screen width on smaller devices */
    }

    .ck-editor__editable[role="textbox"] {
        min-height: 200px;
    }

    .ck-editor__editable {
        font-family: 'Marutham', sans-serif;
    }

    .content-cell {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        /* Show only 2 lines */
        overflow: hidden;
        text-overflow: ellipsis;
        height: 40px;
        /* Adjust this based on your line height */
        line-height: 20px;
        /* Set this to match your text height */
        white-space: normal;
        /* Allow wrapping */
    }

    /* @font-face {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   font-family: 'Marutham';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   src: url('path/to/marutham.ttf') format('truetype');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   } */
    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    /* Step Circle Style */
    .step-circle {
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 15px;
        text-align: center;
        border-radius: 50%;
        background-color: #fff;
        color: #0d6efd;
        font-weight: bold;
        /* position: absolute; */
        top: -10px;
        left: 10px;
        font-size: 14px;
        border: 2px solid #0d6efd;
    }

    /* Mobile View Adjustments */
    @media (max-width: 768px) {

        /* Make the navigation stack vertically on smaller screens */
        .nav-pills .nav-item {
            width: 100%;
            text-align: left;
            margin-bottom: 10px;
        }

        /* Adjust the .nav-link to display block on mobile */
        .nav-pills .nav-link {
            display: block;
            padding-left: 40px;
            /* Ensure the text doesn't overlap with the circle */
        }

        /* Adjust the circle position and size */
        .step-circle {
            position: relative;
            top: 0;
            left: 0;
            margin-right: 10px;
            font-size: 16px;
            display: inline-block;
        }

        /* Adjust the tab content for smaller screens */
        .tab-content {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Make the 3rd and 4th steps appear in separate rows */
        .tab-content .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .tab-pane .col-md-6 {
            width: 100%;
        }
    }

    /* Small screens, stack elements even more */
    @media (max-width: 576px) {
        .nav-pills .nav-item {
            width: 100%;
            text-align: left;
        }

        .nav-pills .nav-link {
            display: flex;
            align-items: center;
            padding-left: 40px;
            /* Keeps the circle alignment */
        }

        .step-circle {
            margin-right: 10px;
            font-size: 18px;
            top: 0;
            left: 0;
            display: inline-block;
        }

        /* Adjust the tab content padding for small screens */
        .tab-content {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Adjust rows in tab content to display properly on mobile */
        .tab-pane .row {
            display: flex;
            flex-direction: column;
        }

        .tab-pane .col-md-6 {
            width: 100%;
            /* Ensure full width for each column */
        }
    }

    /* For larger screens, keep the default horizontal nav-pills layout */
    @media (min-width: 992px) {
        .nav-pills .nav-item {
            width: auto;
            /* Revert the width to auto for large screens */
        }

        .nav-pills .nav-link {
            display: inline-block;
            /* Horizontal layout */
        }

        .step-circle {
            margin-right: 10px;
            font-size: 16px;
            top: -10px;
            left: 10px;
        }
    }

    .wizard .nav-link {
        font-weight: bold;
        border: 1px solid #7198b9;
        margin: 0 5px;
        border-radius: 5px;
    }

    .wizard .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
    }

    #audit_tab .tab-content {
        /* color: white;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   background-color: #428bca; */
        padding: 5px 15px;
    }

    .nav-tabs .nav-link.active {
        color: #0a58ca;
        background-color: #fff;
        border-color: #3782ce #3782ce #fff;
    }

    .nav-tabs {
        border-bottom: 1px solid #3782ce;
    }

    .nav-tabs,
    .nav-tabs .nav-link {
        border-radius: 2px;
    }

    .nav-tabs .nav-link {
        margin-bottom: -1px;
        background: 0 0;
        color: #111213;
        border: 2px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }

    .nav-link:hover {
        color: #111213;
    }



    .accordion-body {

        padding: 10px;
    }

    .viewstack {
        display: flex;
        flex-direction: row;
        align-items: center;
        align-self: stretch;
    }


    /* ---------------------------mobile view----------------------------------------------------- */
    /* .hstack {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                display: flex;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 flex-wrap: wrap;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                justify-content: flex-start;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                gap: 10px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } */

    .hstack .div {
        display: ;
        align-items: end;
        gap: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hstack .badge {
        min-width: 15px;
        min-height: 15px;
    }

    @media (max-width: 576px) {
        .hstack {
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 10px;

        }

        .hstack .div {
            flex-shrink: 1;
            max-width: unset;
        }

        .hstack .div:nth-child(3) {
            text-align: center;
        }
    }


    /* ? Default (Desktop View) */
    .filter-search-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* ? Mobile View (Below 576px) - Show in Same Row */
    @media (max-width: 576px) {
        .filter-search-wrapper {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        /* ? Auto Expand Search Input */
        .filter-search-wrapper .search-input {
            flex-grow: 1;
        }

        .filter-search-wrapper .search-input input {
            width: 100%;
            padding: 5px 10px;
            font-size: 12px;
        }

        /* ? Keep Filter Button Fixed */
        .filter-search-wrapper .dropdown a {
            font-size: 15px !important;
            padding: 5px 10px;
            white-space: nowrap;
        }
    }


    /* ? Default View (Desktop) */
    .button-wrapper {
        margin-left: 39%;
    }

    /* ? Centering in Mobile View */
    @media (max-width: 576px) {
        .button-wrapper {
            margin-left: 0;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .button-wrapper .d-flex {
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button-wrapper button {
            flex-grow: 1;
            max-width: 120px;
        }
    }


    .chat-users {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    /* Desktop View - Show fully without scrolling */
    @media (min-width: 769px) {
        .chat-users {
            max-height: none;
            overflow: visible;
        }
    }

    /* Mobile View - Show only first 5 and make remaining scrollable */
    @media (max-width: 768px) {
        .chat-users {
            max-height: 230px;
            /* Height enough to show 5 slips */
            overflow-y: auto;
        }

        .chat-users li {
            border-bottom: 1px solid #e0e0e0;
        }
    }


    /* Mobile View - Keep Add and Delete button in same row */
    @media (max-width: 768px) {
        .action-row {
            display: flex;
            align-items: center;
        }

        .mar_left {
            margin-left: 8px;

        }

        .action-row .d-flex {
            flex-direction: row !important;
            gap: 10px;
        }

        .action-row button {
            flex-grow: 1;
        }
    }


    @media (max-width: 768px) {


        .button-container {

            margin-top: 5px;
        }
    }

    /* File Upload Container Styles */
    .file-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 5px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .file-preview:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }

    .file-preview i {
        font-size: 1.2rem;
        color: #0d6efd;
        margin-right: 10px;
    }

    .file-preview .file-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .file-preview .file-name {
        font-weight: 500;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .file-preview .file-size {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .file-preview .file-actions {
        display: flex;
        gap: 5px;
    }

    .file-preview .btn-sm {
        padding: 2px 8px;
        font-size: 0.75rem;
    }

    .file-preview .btn-view {
        color: white;
        border: none;
    }

    .file-preview .btn-remove {
        color: white;
        border: none;
    }

    .file-preview .btn-remove:hover {
        background-color: #bb2d3b;
    }

    /* Multiple files container */
    #uploaded_file_container {
        max-height: 200px;
        overflow-y: auto;
        padding: 5px;
        border: 1px dashed #dee2e6;
        border-radius: 4px;
        background-color: #fff;
    }

    /* Empty state */
    #uploaded_file_container:empty::before {
        content: 'No file uploaded';
        display: block;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 10px;
    }

    /* Upload button styles */
    #add-file-btn {
        transition: all 0.3s ease;
    }

    #add-file-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #add-file-btn i {
        margin-right: 5px;
    }

    /* File details container from existing data */
    .fileDetailsContainer {
        max-height: 150px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background-color: #f8f9fa;
    }

    .file-item {
        padding: 5px 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .file-item:last-child {
        border-bottom: none;
    }

    .file-item a {
        display: flex;
        align-items: center;
        text-decoration: none !important;
        color: #0d6efd;
    }

    .file-item a:hover {
        text-decoration: underline;
    }

    .file-item i {
        margin-right: 5px;
    }
</style>

<div class="card card_border">
    <div class="col-12">
        <div class="card-header card_header_color lang" key="field_audit_title" style="padding:8px;">Performance Field
            Audit
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                            Name</label> <input type="text" class="form-control" id="total_mandays"
                            name="total_mandays" value="<?php echo $instdel[0]['instename']; ?>" disabled>
                    </div>
                    <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                            Category</label> <input type="text" class="form-control" id="total_mandays"
                            name="total_mandays" value="<?php echo $instdel[0]['catename']; ?>" disabled>
                    </div>
                    <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Type of
                            Audit</label> <input type="text" class="form-control" id="total_mandays"
                            name="total_mandays" value="<?php echo $instdel[0]['typeofauditename']; ?>" disabled>
                    </div>
                    <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Year of
                            Audit</label> <input type="text" class="form-control" id="total_mandays"
                            name="total_mandays" value="<?php echo $instdel[0]['yearname']; ?>" disabled>
                    </div>
                    @if ($instdel[0]['deptcode'] == '01' && $instdel[0]['annadhanam_only'] == 'Y')
                        <div class="col-md-2 mb-3"> <label class="form-label required"
                                for="validationDefault01">Annadhanam
                                Year</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['annadhanamyear']; ?>" disabled>
                        </div>
                    @endif
                    <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Total
                            Mandays</label> <input type="text" class="form-control" id="total_mandays"
                            name="total_mandays" value="<?php echo $instdel[0]['mandays']; ?>" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body card_border">
            <div class="row">
                <div class="card-body">

                    <form id="fieldAuditForm" name="fieldAuditForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                            value="{{ $auditscheduleid ?? '' }}">
                        <input type="hidden" name="auditplanid" id="auditplanid" value="{{ $auditplanid ?? '' }}">
                        <input type="hidden" name="uploadid" id="uploadid" value="{{ $fileuploadid ?? '' }}">
                        <input type="hidden" name="existing_uploadid" id="existing_uploadid"
                            value="{{ $fileuploadid ?? '' }}">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <input type="hidden" name="praudittransid" id="praudittransid" value="">

                        @if (isset($titleDetails) && !$titleDetails->isEmpty())
                            <div class="row">

                                @foreach ($titleDetails as $detail)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label lang">Title</label>
                                        <input type="text" class="form-control" value="{{ $detail->titleename }}"
                                            readonly disabled>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label lang">ADM</label>
                                        <div class="fileDetailsContainer">
                                            @php
                                                $fileDetails = explode(',', $detail->filedetails);
                                            @endphp

                                            @foreach ($fileDetails as $fileDetail)
                                                @if ($fileDetail != '-')
                                                    @php
                                                        $parts = explode('-', $fileDetail);
                                                        $filename = $parts[0] ?? '';
                                                        $filepath = $parts[1] ?? '';
                                                        $filesize = $parts[2] ?? 0;
                                                        $fileuploadid = $parts[3] ?? '';
                                                    @endphp

                                                    @if (!empty($filename))
                                                        <div class="file-item">
                                                            <a href="{{ asset('/' . $filepath) }}" target="_blank"
                                                                class="text-primary">
                                                                <i class="ti ti-file-text"></i>
                                                                {{ $filename }}
                                                                ({{ number_format($filesize / 1024, 2) }} KB)
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="row">
                            <input type="hidden" class="form-control" id="auditscheduleid_display"
                                value="{{ $auditscheduleid ?? '' }}" readonly disabled>

                            <input type="hidden" class="form-control" id="auditplanid_display"
                                value="{{ $auditplanid ?? '' }}" readonly disabled>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required lang" key="remarks" for="remarks">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder='Enter Remarks'></textarea>

                                <small class="text-danger d-none" id="remarks_error"></small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="field_audit_file"
                                    for="field_audit_file">
                                    File Upload
                                </label>
                                <small style="color: red;">(only PDF max 2MB)</small>
                                <input type="file" id="field_audit_file" name="field_audit_file"
                                    accept="application/pdf" data-msg-required="Please upload a document"
                                    class="d-none">

                                <button type="button" class="btn btn-outline-primary w-100" id="add-file-btn">
                                    <i class="ti ti-upload"></i> Upload PDF
                                </button>

                                <div id="view_file-list-container">
                                    @if (isset($fileUploads) && $fileUploads)
                                        @php
                                            $fileUrl =
                                                $fileUploads->full_url ??
                                                asset('/' . str_replace('\\', '/', $fileUploads->filepath));
                                            $fileName = $fileUploads->filename ?? 'Uploaded File';
                                            $fileSize = $fileUploads->filesize ?? 0;
                                            $formattedSize = $fileSize
                                                ? number_format($fileSize / 1024, 2) . ' KB'
                                                : '';
                                        @endphp

                                        <div class="card overflow-hidden mb-3 bg-light" id="viewfile-card-existing">
                                            <div class="d-flex flex-row">
                                                <div class="p-3 mb-1">
                                                    <h3 class="text-dark mb-0 fs-2">
                                                        <a style="color:black;" href="{{ $fileUrl }}"
                                                            target="_blank" rel="noopener noreferrer">
                                                            {{ $fileName }}
                                                        </a>
                                                        @if ($formattedSize)
                                                            <br><small class="text-muted">{{ $formattedSize }}</small>
                                                        @endif
                                                    </h3>
                                                </div>
                                                <div class="p-1 align-items-center mt-2 remove-file-btn"
                                                    onclick="removeFile()">
                                                    <h5 class="text-danger box mb-0 round-40 p-1">
                                                        <i class="ti ti-trash"></i>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#add-file-btn').addClass('d-none');
                                            });
                                        </script>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="statusflag" id="statusflag" value="E">
                        </div>

                        <div class="row text-center">
                            <div class="col-md-4 mx-auto">
                                <button class="btn btn-primary mt-3 lang" type="button" id="saveDraftBtn">
                                    Save Draft
                                </button>

                                <button class="btn btn-success mt-3 lang d-none" type="button" id="finalizeBtn">
                                    Finalize
                                </button>

                                <div class="spinner-border text-primary d-none" id="loadingSpinner" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/apps/chat.js"></script>
<script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script>
    let fieldAuditValidator;
    const auditscheduleid = '{{ $auditscheduleid ?? '' }}';
    const auditplanid = '{{ $auditplanid ?? '' }}';
    let editorInitialized = false;
    let currentStatus = 'E';
    let hasExistingData = false;

    $(document).ready(function() {
        $('#add-file-btn').click(function() {
            $('#field_audit_file').trigger('click');
        });

        $('#field_audit_file').change(function() {
            handleFileUpload(this);
        });

        $(document).on('click', '#view_file-list-container .remove-file-btn', function() {
            removeFile();
        });

        $('#saveDraftBtn').click(function() {
            $('#statusflag').val('E');
            submitForm('E');
        });

        $('#finalizeBtn').click(function() {
            if (!validateForm('F')) {
                return;
            }

            passing_alert_value(
                'Confirmation',
                'Are you sure you want to finalize? This action cannot be undone.',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            $('#confirmation_alert').off('click', '#process_button').on('click', '#process_button',
                function() {
                    $('#statusflag').val('F');
                    submitForm('F');
                    $('#confirmation_alert').modal('hide');
                });
        });

        initializeCKEditor();

        checkExistingRecordStatus();
    });

    function checkExistingRecordStatus() {


        $.ajax({
            url: '/prauditfieldauditcheck',
            type: 'POST',
            data: {
                auditscheduleid: auditscheduleid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.exists) {
                    hasExistingData = true;
                    currentStatus = response.statusflag;

                    if (currentStatus === 'F') {
                        disableAllFields(true);
                        $('#saveDraftBtn').prop('disabled', true).addClass('d-none');
                        $('#finalizeBtn').prop('disabled', true).text('Finalized').addClass('disabled');

                        $('#add-file-btn').prop('disabled', true).addClass('disabled');

                        $('.remove-file-btn').hide();
                    } else {
                        $('#saveDraftBtn').text('Update Draft');
                        $('#finalizeBtn').removeClass('d-none').prop('disabled', false);
                        loadExistingData();
                    }
                } else {
                    $('#finalizeBtn').addClass('d-none');
                    $('#saveDraftBtn').text('Save Draft');
                }

                setTimeout(function() {
                    if (fieldAuditValidator) {
                        fieldAuditValidator.form();
                    }
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error checking status:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    function disableAllFields(disabled = true) {

        if (window.editor) {
            try {
                if (disabled) {
                    if (typeof window.editor.enableReadOnlyMode === 'function') {
                        window.editor.enableReadOnlyMode('manual');
                    } else {
                        window.editor.isReadOnly = true;
                    }
                } else {
                    if (typeof window.editor.disableReadOnlyMode === 'function') {
                        window.editor.disableReadOnlyMode('manual');
                    } else {
                        window.editor.isReadOnly = false;
                    }
                }

                if (disabled) {
                    $('.ck-editor__editable').addClass('ck-editor__editable_disabled');
                } else {
                    $('.ck-editor__editable').removeClass('ck-editor__editable_disabled');
                }
            } catch (e) {
                console.error('Error setting editor readonly:', e);

                if (disabled) {
                    $('#remarks').prop('disabled', true);
                    $('.ck-editor__editable').attr('contenteditable', 'false').css({
                        'background-color': '#e9ecef',
                        'opacity': '0.65',
                        'pointer-events': 'none'
                    });
                } else {
                    $('#remarks').prop('disabled', false);
                    $('.ck-editor__editable').attr('contenteditable', 'true').css({
                        'background-color': '',
                        'opacity': '',
                        'pointer-events': ''
                    });
                }
            }
        }

        $('#add-file-btn').prop('disabled', disabled);
        if (disabled) {
            $('#add-file-btn').addClass('disabled').css('pointer-events', 'none');
        } else {
            $('#add-file-btn').removeClass('disabled').css('pointer-events', 'auto');
        }

        $('#field_audit_file').prop('disabled', disabled);

        $('.remove-file-btn, .remove-uploaded-file').prop('disabled', disabled);
        if (disabled) {
            $('.remove-file-btn, .remove-uploaded-file').addClass('disabled').hide();
        } else {
            $('.remove-file-btn, .remove-uploaded-file').removeClass('disabled').show();
        }

        if (!window.editor) {
            $('#remarks').prop('disabled', disabled);
        }

    }

    function showAlert(message, type = 'Confirmation') {
        passing_alert_value(
            type,
            message,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'confirmation_alert'
        );
    }

    function initializeCKEditor() {
        if (document.getElementById("remarks")) {
            CKEDITOR.ClassicEditor.create(document.getElementById("remarks"), {
                    toolbar: {
                        items: [
                            'findAndReplace', 'selectAll', '|',
                            'heading', '|',
                            'bold', 'italic', 'underline', '|',
                            'numberedList', '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            'fontSize', 'fontFamily', '|',
                            'alignment', '|',
                            'insertTable',
                            '|',
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    placeholder: 'Enter remarks here...',
                    fontFamily: {
                        options: [
                            'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                        ],
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    htmlSupport: {
                        allow: [{
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }]
                    },
                    link: {
                        decorators: {
                            addTargetToExternalLinks: true,
                            defaultProtocol: 'https://',
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    removePlugins: [
                        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                        'MultiLevelList',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                        'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                        'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                        'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange', 'uploadImage'
                    ]
                })
                .then(editor => {
                    window.editor = editor;
                    editorInitialized = true;

                    editor.model.document.on('change:data', () => {
                        if (fieldAuditValidator) {
                            fieldAuditValidator.element('#remarks');
                        }
                    });

                    loadExistingData();
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    editorInitialized = false;
                    showAlert('Failed to initialize editor. Please refresh the page.', 'Error');
                });
        } else {
            console.error('Remarks element not found');
        }
    }

    function loadExistingData() {
        @if (isset($remarks) && !empty($remarks))
            setTimeout(function() {
                if (window.editor) {
                    let remarks = `{!! addslashes($remarks) !!}`;
                    window.editor.setData(remarks);

                    setTimeout(function() {
                        if (fieldAuditValidator) {
                            fieldAuditValidator.element('#remarks');
                        }
                    }, 500);
                } else {
                    $('#remarks').val(`{!! addslashes($remarks) !!}`);
                }
            }, 500);
        @endif

        @if (isset($fileUploads) && $fileUploads)
            $('#view_file-list-container').show();
        @endif
    }
    fieldAuditValidator = $("#fieldAuditForm").validate({
        rules: {
            remarks: {
                required: true,
                minlength: 1
            },
            field_audit_file: {
                required: true
            }
        },
        messages: {
            remarks: {
                required: 'Please enter remarks',
                minlength: 'Please enter remarks'
            },
            field_audit_file: {
                required: 'Please upload a file'
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'field_audit_file') {
                error.insertAfter('#view_file-list-container');
                error.addClass('text-danger mt-1');
            } else {
                error.insertAfter(element);
                error.addClass('text-danger mt-1');
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
            if ($(element).attr('id') === 'field_audit_file') {}
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
            if ($(element).attr('id') === 'field_audit_file') {
                $('#view_file-list-container').removeClass('border border-danger');
            }
        },
        invalidHandler: function(event, validator) {
            let errors = validator.numberOfInvalids();
            if (errors > 0) {
                let messages = [];

                if (validator.errorMap.remarks) {
                    messages.push('Remarks is required');
                }

                if (validator.errorMap.field_audit_file) {
                    messages.push('File upload is required');
                }

                let errorMessage = messages.join(',\n');
                showAlert(errorMessage, 'Validation Error');
            }
        }
    });

    $.validator.addMethod("editorRequired", function(value, element) {
        if (window.editor) {
            const data = window.editor.getData();
            return data && data.trim().length > 0;
        }
        return value && value.trim().length > 0;
    }, "Please enter remarks");

    $("#fieldAuditForm").validate({
        rules: {
            remarks: {
                required: true,
                editorRequired: true
            },
            field_audit_file: {
                required: true
            }
        },
        messages: {
            remarks: {
                required: 'Please enter remarks',
                editorRequired: 'Please enter remarks'
            },
            field_audit_file: {
                required: 'Please upload a file'
            }
        }
    });

    function submitForm(status) {
        if (!validateForm(status)) {
            return;
        }

        $('#loadingSpinner').removeClass('d-none');
        $('#saveDraftBtn').prop('disabled', true);
        $('#finalizeBtn').prop('disabled', true);

        let formData = new FormData(document.getElementById('fieldAuditForm'));

        formData.append('auditscheduleid', auditscheduleid);
        formData.append('auditplanid', auditplanid);
        formData.append('statusflag', status);

        const uploadid = $('#uploadid').val();
        const existing_uploadid = $('#existing_uploadid').val();

        if (uploadid) {
            formData.append('uploadid', uploadid);
        }
        if (existing_uploadid) {
            formData.append('existing_uploadid', existing_uploadid);
        }

        let remarksContent = '';

        if (window.editor) {
            remarksContent = window.editor.getData();
        } else {
            remarksContent = $('#remarks').val();
        }

        formData.append('remarks', remarksContent);

        const action = hasExistingData ? 'update' : 'insert';
        formData.append('action', action);

        $.ajax({
            url: '/prauditfieldauditsave',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingSpinner').addClass('d-none');
                $('#saveDraftBtn').prop('disabled', false);
                $('#finalizeBtn').prop('disabled', false);

                if (response.success) {
                    showAlert(response.message, 'Success');

                    if (status === 'F') {
                        disableAllFields(true);
                        $('#saveDraftBtn').prop('disabled', true).addClass('d-none');
                        $('#finalizeBtn').prop('disabled', true).text('Finalized').addClass('disabled');

                        currentStatus = 'F';
                        hasExistingData = true;
                    } else {
                        $('#saveDraftBtn').prop('disabled', false);
                        $('#finalizeBtn').prop('disabled', false).removeClass('d-none');
                        $('#saveDraftBtn').text('Update Draft');

                        disableAllFields(false);

                        currentStatus = 'E';
                        hasExistingData = true;
                    }
                } else {
                    showAlert(response.message || 'Unknown error occurred', 'Error');
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').addClass('d-none');
                $('#saveDraftBtn').prop('disabled', false);
                $('#finalizeBtn').prop('disabled', false);

                let message = 'An error occurred';
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join(', ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 401) {
                    message = 'Session expired. Please login again.';
                    setTimeout(function() {
                        window.location.href = '/login';
                    }, 2000);
                }

                showAlert(message, 'Error');
                console.error('AJAX Error:', xhr);
            }
        });
    }

    function validateForm(status) {
        // Check if remarks is empty
        let remarksValid = false;
        if (window.editor) {
            const remarks = window.editor.getData();
            remarksValid = remarks && remarks.trim().length > 0;
        } else {
            remarksValid = $('#remarks').val() && $('#remarks').val().trim().length > 0;
        }

        // Check if file is uploaded
        const fileValid = $('#uploadid').val() || $('#existing_uploadid').val() ||
            ($('#field_audit_file')[0] && $('#field_audit_file')[0].files.length > 0);

        if (!remarksValid || !fileValid) {
            let messages = [];

            if (!remarksValid) {
                messages.push('Remarks is required');
            }

            if (!fileValid) {
                messages.push('File upload is required');
            }

            let errorMessage = messages.join(',\n');
            showAlert(errorMessage, 'Validation Error');

            if (!remarksValid) {
                $('.ck-editor__editable').addClass('border border-danger');
            } else {
                $('.ck-editor__editable').removeClass('border border-danger');
            }

            return false;
        }

        return true;
    }

    function handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            showAlert('Please upload a PDF file', 'Error');
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showAlert('File size must not exceed 2MB', 'Error');
            input.value = '';
            return;
        }

        // Create a temporary file object
        const fileObj = {
            id: 'temp_' + Date.now(),
            name: file.name,
            path: file,
            size: file.size,
            fileuploadid: ''
        };

        view_files(fileObj);

        $('#add-file-btn').addClass('d-none');
        $('#uploadid').val('');
        $('#existing_uploadid').val('');

    }

    function normalizeFilePath(dbFilePath) {
        if (!dbFilePath) return '';

        let normalizedPath = dbFilePath.replace(/\\/g, '/');

        if (normalizedPath.startsWith('uploads/')) {
            normalizedPath = '/' + normalizedPath;
        } else if (normalizedPath.includes('templateaudit')) {
            const pattern = /(uploads\/.*)/i;
            const match = normalizedPath.match(pattern);

            if (match) {
                normalizedPath = '/' + match[1];
            } else {
                const parts = normalizedPath.split('/');
                const uploadsIndex = parts.findIndex(part => part === 'uploads');
                if (uploadsIndex !== -1) {
                    normalizedPath = '/' + parts.slice(uploadsIndex).join('/');
                } else {
                    normalizedPath = '/uploads/' + normalizedPath;
                }
            }
        }

        normalizedPath = normalizedPath.replace(/[^\x20-\x7E]/g, '');
        normalizedPath = normalizedPath.replace(/\/+/g, '/');

        return normalizedPath;
    }

    function view_files(file) {
        const fileListContainer = $('#view_file-list-container');
        fileListContainer.show();
        fileListContainer.empty();

        $('#uploadid').val(file.fileuploadid);

        const fileSize = file.size ? formatFileSize(file.size) : '';

        let filePath = file.path;

        if (file.path instanceof File) {
            filePath = URL.createObjectURL(file.path);
        } else if (filePath && !filePath.startsWith('http')) {
            filePath = normalizeFilePath(filePath);
            filePath = window.location.origin + filePath;
        }

        const displayName = file.name;

        const fileCard = `
            <div class="card overflow-hidden mb-3 bg-light" id="viewfile-card-${file.id || Date.now()}">
                <div class="d-flex flex-row">
                    <div class="p-3 mb-1">
                        <h3 class="text-dark mb-0 fs-2">
                            <a style="color:black;" href="${filePath}" target="_blank" rel="noopener noreferrer">${displayName}</a>
                            ${fileSize ? `<br><small class="text-muted">${fileSize}</small>` : ''}
                        </h3>
                    </div>
                    <div class="p-1 align-items-center mt-2 remove-file-btn" onclick="removeFile()">
                        <h5 class="text-danger box mb-0 round-40 p-1">
                            <i class="ti ti-trash"></i>
                        </h5>
                    </div>

                </div>
            </div>
        `;

        fileListContainer.append(fileCard);

        if (filePath && typeof filePath === 'string' && filePath.startsWith('http')) {
            fetch(filePath, {
                    method: 'HEAD'
                })
                .then(response => {
                    if (!response.ok) {
                        console.warn('File may not be accessible:', filePath, 'Status:', response.status);
                    } else {
                        // console.log('File is accessible:', filePath);
                    }
                })
                .catch(error => {
                    console.error('Error checking file accessibility:', error);
                });
        }
    }

    function removeFile() {
        const fileuploadid = $('#uploadid').val() || $('#existing_uploadid').val();
        const auditscheduleid = $('#auditscheduleid').val();

        if (!fileuploadid) {
            clearFileUI();
            return;
        }

        passing_alert_value(
            'Confirmation',
            'Are you sure you want to remove this file?',
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        $('#confirmation_alert').off('click', '#process_button').on('click', '#process_button', function() {
            $('#loadingSpinner').removeClass('d-none');

            $.ajax({
                url: '/prauditfieldauditremovefile',
                type: 'POST',
                data: {
                    fileuploadid: fileuploadid,
                    auditscheduleid: auditscheduleid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loadingSpinner').addClass('d-none');

                    if (response.success) {
                        clearFileUI();

                        showAlert(response.message, 'Success');

                        if (fieldAuditValidator) {
                            fieldAuditValidator.element('#field_audit_file');
                        }
                    } else {
                        showAlert(response.message || 'Error removing file', 'Error');
                    }

                    $('#confirmation_alert').modal('hide');
                },
                error: function(xhr) {
                    $('#loadingSpinner').addClass('d-none');

                    let message = 'Error removing file';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    showAlert(message, 'Error');
                    $('#confirmation_alert').modal('hide');

                    console.error('AJAX Error:', xhr);
                }
            });
        });
    }

    function clearFileUI() {
        $('#view_file-list-container').empty().hide();
        $('#field_audit_file').val('');
        $('#uploadid').val('');
        $('#existing_uploadid').val('');
        $('#add-file-btn').removeClass('d-none');

        $('#field_audit_file-error').remove();
        $('#view_file-list-container').removeClass('border border-danger');

        if (currentStatus !== 'F') {
            formChanged = true;
        }
    }


    function formatFileSize(bytes) {
        if (!bytes) return '';
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function removeUploadedFile() {
        removeFile();
    }

    $('#translate').change(function() {
        if (window.editor) {
            window.editor.destroy();
            initializeCKEditor();
        }
    });

    let formChanged = false;
    $('#fieldAuditForm input, #fieldAuditForm textarea, #field_audit_file').change(function() {
        if (currentStatus !== 'F') {
            formChanged = true;
        }
    });

    if (window.editor) {
        window.editor.model.document.on('change:data', () => {
            if (currentStatus !== 'F') {
                formChanged = true;
                if (window.editor.getData().trim().length > 0) {
                    $('.ck-editor__editable').removeClass('border border-danger');
                }
            }
        });
    }

    window.addEventListener('beforeunload', function(e) {
        if (formChanged && currentStatus !== 'F') {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
@endsection
