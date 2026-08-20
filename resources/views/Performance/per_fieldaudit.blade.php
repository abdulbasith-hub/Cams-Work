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

    :root {
        --primary: #004aad;
        --primary-light: #336ed4;
        --primary-dark: #00307a;
        --secondary: #007e33;
        --secondary-light: #00b44b;
        --info: #0088cc;
        --warning: #ffa500;
        --danger: #d62828;
        --light: #f0f2f5;
        --dark: #1c1c1c;
        --gray: #6e6e6e;
        --white: #ffffff;

        --primary-gradient: linear-gradient(135deg, #006dad 0%, #336ed4 100%);
        --success-gradient: linear-gradient(135deg, #007e33 0%, #00b44b 100%);
        --warning-gradient: linear-gradient(135deg, #ffa500 0%, #ffcb29 100%);
        --info-gradient: linear-gradient(135deg, #0088cc 0%, #00d4ff 100%);
        --danger-gradient: linear-gradient(135deg, #d62828 0%, #ff4d6d 100%);

        --card-bg: #ffffff;
        --card-shadow: 0 3px 14px rgba(0, 0, 0, 0.08);
    }


    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.312);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }

    .cube {
        width: 40px;
        height: 40px;
        background: var(--primary);
        animation: cubeFlip 1.2s infinite ease-in-out;
        transform-style: preserve-3d;
    }

    @keyframes cubeFlip {
        0% {
            transform: rotateX(0) rotateY(0);
        }

        25% {
            transform: rotateX(180deg) rotateY(0);
        }

        50% {
            transform: rotateX(180deg) rotateY(180deg);
        }

        75% {
            transform: rotateX(0) rotateY(180deg);
        }

        100% {
            transform: rotateX(0) rotateY(0);
        }
    }
</style>

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

<div class="loading-overlay">
    <div class="cube"></div>
</div>


<div class="card card_border">
    <div class="col-12">
        <div class="card-header card_header_color lang" key="field_audit_title" style="padding:8px;">Performance Field
            Audit
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                            Name</label> <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                            value="<?php echo $instdel[0]['instename']; ?>" disabled>
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
                    <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Annadhanam
                            Year</label> <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                            value="<?php echo $instdel[0]['annadhanamyear']; ?>" disabled>
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
                                <input type="text" class="form-control" value="{{ $detail->titleename }}" readonly
                                    disabled>
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
                                <textarea class="form-control" id="remarks" name="remarks" rows="4"
                                    placeholder='Enter Remarks'></textarea>

                                <small class="text-danger d-none" id="remarks_error"></small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label lang" key="field_audit_file" for="field_audit_file">
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
                                                    <a style="color:black;" href="{{ $fileUrl }}" target="_blank"
                                                        rel="noopener noreferrer">
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
            if (!validateFormForSubmission('finalize')) {
                return;
            }

            showConfirmationDialog(
                'Confirmation',
                'Are you sure you want to finalize? This action cannot be undone.',
                function() {
                    $('#statusflag').val('F');
                    submitForm('F');
                }
            );
        });

        $('#translate').change(function() {
            if (window.editor) {
                window.editor.destroy();
                initializeCKEditor();
            }
        });

        $('#fieldAuditForm input, #fieldAuditForm textarea, #field_audit_file').change(function() {
            if (currentStatus !== 'F') {
                formChanged = true;
            }
        });

        initializeCKEditor();

        setTimeout(function() {
            checkExistingRecordStatus();
        }, 100);
    });

    // ============================================
    // GLOBAL VARIABLES
    // ============================================
    let fieldAuditValidator;
    let editorInitialized = false;
    let formChanged = false;
    let hasExistingData = false;
    let currentStatus = '';
    const auditscheduleid = $('#auditscheduleid').val();
    const auditplanid = $('#auditplanid').val();
    const schteammemberid = $('#schteammemberid').val();

    // ============================================
    // LOADING OVERLAY FUNCTIONS
    // ============================================
    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    // ============================================
    // CENTRALIZED REMARKS FUNCTIONS
    // ============================================
    function hasImagesInRemarks() {
        if (window.editor && editorInitialized) {
            const content = window.editor.getData();
            return content.includes('<img');
        }
        const remarksContent = $('#remarks').val();
        return remarksContent && remarksContent.includes('<img');
    }

    function getRemarksContent() {
        if (window.editor && editorInitialized) {
            return window.editor.getData();
        }
        return $('#remarks').val();
    }

    function getRemarksPlainText() {
        const html = getRemarksContent();
        if (!html) return '';
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        return tempDiv.textContent || tempDiv.innerText || '';
    }

    function cleanImagesFromRemarks() {
        if (window.editor && editorInitialized) {
            const content = window.editor.getData();
            if (content.includes('<img')) {
                const cleanedContent = content.replace(/<img[^>]*>/g, '');
                window.editor.setData(cleanedContent);
                return true;
            }
        } else {
            const content = $('#remarks').val();
            if (content && content.includes('<img')) {
                const cleanedContent = content.replace(/<img[^>]*>/g, '');
                $('#remarks').val(cleanedContent);
                return true;
            }
        }
        return false;
    }

    // ============================================
    // ENHANCED VALIDATION METHODS
    // ============================================
    function validateRemarks(operation = 'save') {
        let messages = [];
        let isValid = true;

        if (hasImagesInRemarks()) {
            messages.push('Images are not allowed in remarks. Please remove them.');
            isValid = false;

            if (cleanImagesFromRemarks()) {
                showAlert('Images are not allowed in remarks.', 'Warning');
            }
        }

        const plainText = getRemarksPlainText();

        if (plainText.length === 0) {
            messages.push('Remarks is required');
            isValid = false;
        } else if (plainText.length < 10) {
            messages.push('Remarks must be at least 10 characters');
            isValid = false;
        }

        return {
            isValid: isValid,
            messages: messages
        };
    }

    function validateFormForSubmission(operation = 'save') {
        let isValid = true;
        let allMessages = [];

        if (fieldAuditValidator && !fieldAuditValidator.form()) {
            isValid = false;
        }

        const remarksValidation = validateRemarks(operation);
        if (!remarksValidation.isValid) {
            isValid = false;
            allMessages = allMessages.concat(remarksValidation.messages);
        }

        if (!isValid && allMessages.length > 0) {
            showAlert(allMessages.join('\n'), 'Validation Error');
        }

        return isValid;
    }

    function validateForFinalize() {
        return validateFormForSubmission('finalize');
    }

    function validateForm(status) {
        return validateFormForSubmission(status === 'F' ? 'finalize' : 'save');
    }

    // ============================================
    // CKEDITOR INITIALIZATION
    // ============================================
    function initializeCKEditor() {
        if (!document.getElementById("remarks")) {
            console.error('Remarks element not found');
            return;
        }

        function PasteHandlerPlugin(editor) {
            const clipboard = editor.plugins.get('ClipboardPipeline');

            clipboard.on('inputTransformation', (evt, data) => {
                if (data.content) {
                    let hasImage = false;

                    function checkForImages(node) {
                        if (node.is && node.is('element', 'img')) {
                            hasImage = true;
                            return true;
                        }
                        if (node.getChildren) {
                            for (const child of node.getChildren()) {
                                if (checkForImages(child)) return true;
                            }
                        }
                        return false;
                    }

                    if (hasImage) {
                        setTimeout(() => {
                            showAlert('Images are not allowed in remarks.', 'Warning');
                            formChanged = true;
                        }, 100);
                    }
                }
            });
        }

        CKEDITOR.ClassicEditor
            .create(document.getElementById("remarks"), {
                toolbar: {
                    items: [
                        'findAndReplace', 'selectAll', '|',
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'numberedList', 'bulletedList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        'fontSize', 'fontFamily', '|',
                        'alignment', '|',
                        'insertTable', '|',
                        'removeFormat'
                    ],
                    shouldNotGroupWhenFull: true
                },
                placeholder: 'Welcome to CAMS... ',
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
                    disallow: [{
                        name: 'img'
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
                    ],
                extraPlugins: [PasteHandlerPlugin]
            })
            .then(editor => {
                window.editor = editor;
                editorInitialized = true;

                editor.model.document.on('change:data', () => {
                    const content = editor.getData();
                    if (content.includes('<img')) {
                        const cleanedContent = content.replace(/<img[^>]*>/g, '');
                        editor.setData(cleanedContent);
                        showAlert('Images are not allowed in remarks ', 'Warning');
                        formChanged = true;
                    }

                    if (currentStatus !== 'F') {
                        formChanged = true;
                    }

                    if (fieldAuditValidator) {
                        fieldAuditValidator.element('#remarks');
                    }
                });

                loadExistingData();
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                editorInitialized = false;
                $('#remarks').show();
                showAlert('Failed to initialize rich text editor. Using basic text input.', 'Warning');
            });
    }

    // ============================================
    // ALERT AND DIALOG FUNCTIONS
    // ============================================
    function showAlert(message, type = 'Success') {
        if (typeof passing_alert_value === 'function') {
            passing_alert_value(
                type,
                message,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert'
            );
        } else {
            alert(message);
        }
    }

    function showConfirmationDialog(title, message, callback) {
        if (typeof passing_alert_value === 'function') {
            passing_alert_value(
                title,
                message,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            $('#confirmation_alert').off('click', '#process_button').on('click', '#process_button', function() {
                callback();
                $('#confirmation_alert').modal('hide');
            });
        } else {
            if (confirm(message)) {
                callback();
            }
        }
    }

    // ============================================
    // DATA LOADING FUNCTIONS
    // ============================================
    function loadExistingData() {
        @if (isset($remarks) && !empty($remarks))
            setTimeout(function() {
                if (window.editor && editorInitialized) {
                    let remarks = {!! json_encode($remarks) !!};
                    window.editor.setData(remarks);
                } else {
                    $('#remarks').val({!! json_encode($remarks) !!});
                }
            }, 500);
        @endif

        @if (isset($fileUploads) && $fileUploads)
            $('#view_file-list-container').show();
        @endif
    }

    function checkExistingRecordStatus() {
        showLoading();
        $.ajax({
            url: '/prauditfieldauditcheck',
            type: 'POST',
            data: {
                auditscheduleid: auditscheduleid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
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
                    }
                } else {
                    $('#finalizeBtn').addClass('d-none');
                    $('#saveDraftBtn').text('Save Draft');
                }
            },
            error: function(xhr) {
                hideLoading();
                console.error('Error checking status:', xhr);
                showAlert('Error checking record status', 'Error');
            }
        });
    }

    // ============================================
    // JQUERY VALIDATOR METHODS
    // ============================================
    $.validator.addMethod("noImages", function(value, element) {
        return !hasImagesInRemarks();
    }, "Images are not allowed in remarks");

    $.validator.addMethod("editorMinLength", function(value, element, minLength) {
        const plainText = getRemarksPlainText();
        return plainText.trim().length >= minLength;
    }, "Remarks must be at least 10 characters");

    // Initialize validation
    fieldAuditValidator = $("#fieldAuditForm").validate({
        rules: {
            remarks: {
                required: true,
                editorMinLength: 10,
                noImages: true
            }
        },
        messages: {
            remarks: {
                required: 'Please enter remarks',
                editorMinLength: 'Remarks must be at least 10 characters',
                noImages: 'Images are not allowed in remarks'
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger mt-1',
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'field_audit_file') {
                error.insertAfter('#view_file-list-container');
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
            if ($(element).attr('id') === 'field_audit_file') {
                $('#view_file-list-container').addClass('border border-danger');
            }
            if ($(element).attr('id') === 'remarks') {
                $('.ck-editor__editable').addClass('border border-danger');
            }
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
            if ($(element).attr('id') === 'field_audit_file') {
                $('#view_file-list-container').removeClass('border border-danger');
            }
            if ($(element).attr('id') === 'remarks') {
                $('.ck-editor__editable').removeClass('border border-danger');
            }
        }
    });

    // ============================================
    // FORM SUBMISSION
    // ============================================
    function submitForm(status) {
        // Determine operation type
        const operation = status === 'F' ? 'finalize' : 'save';

        // Validate based on operation
        if (!validateFormForSubmission(operation)) {
            return; // Stop if validation fails
        }

        showLoading();
        $('#saveDraftBtn').prop('disabled', true);
        $('#finalizeBtn').prop('disabled', true);

        let formData = new FormData(document.getElementById('fieldAuditForm'));

        formData.append('auditscheduleid', auditscheduleid);
        formData.append('auditplanid', auditplanid);
        formData.append('statusflag', status);
        formData.append('schteammemberid', schteammemberid);

        const uploadid = $('#uploadid').val();
        const existing_uploadid = $('#existing_uploadid').val();

        if (uploadid) {
            formData.append('uploadid', uploadid);
        }
        if (existing_uploadid) {
            formData.append('existing_uploadid', existing_uploadid);
        }

        formData.append('remarks', getRemarksContent());

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
                hideLoading();
                $('#saveDraftBtn').prop('disabled', false);
                $('#finalizeBtn').prop('disabled', false);

                if (response.success) {
                    showAlert(response.message, 'Success');
                    formChanged = false;

                    if (status === 'F') {
                        disableAllFields(true);
                        $('#saveDraftBtn').prop('disabled', true).addClass('d-none');
                        $('#finalizeBtn').prop('disabled', true).text('Finalized').addClass('disabled');
                        currentStatus = 'F';
                        hasExistingData = true;
                    } else {
                        $('#saveDraftBtn').text('Update Draft');
                        $('#finalizeBtn').removeClass('d-none').prop('disabled', false);
                        disableAllFields(false);
                        currentStatus = 'E';
                        hasExistingData = true;
                    }

                    if (response.uploadid) {
                        $('#uploadid').val(response.uploadid);
                        $('#existing_uploadid').val(response.uploadid);
                    }
                } else {
                    showAlert(response.message || 'Unknown error occurred', 'Error');
                }
            },
            error: function(xhr) {
                hideLoading();
                $('#saveDraftBtn').prop('disabled', false);
                $('#finalizeBtn').prop('disabled', false);

                let message = 'An error occurred';
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join('\n');
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

    // ============================================
    // FILE HANDLING FUNCTIONS
    // ============================================
    function handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            showAlert('Please upload a PDF file only', 'Error');
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showAlert('File size must not exceed 2MB', 'Error');
            input.value = '';
            return;
        }

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

        if (fieldAuditValidator) {
            fieldAuditValidator.element('#field_audit_file');
        }

        formChanged = true;
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

        const displayName = file.name || 'Uploaded File';
        const fileId = file.id || 'file_' + Date.now();

        const fileCard = `
            <div class="card overflow-hidden mb-3 bg-light" id="viewfile-card-${fileId}">
                <div class="d-flex flex-row">
                    <div class="p-3 mb-1">
                        <h3 class="text-dark mb-0 fs-2">
                            <a style="color:black;" href="${filePath}" target="_blank" rel="noopener noreferrer">
                                <i class="ti ti-file-text me-2"></i>${displayName}
                            </a>
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
    }

    function removeFile() {
        const fileuploadid = $('#uploadid').val() || $('#existing_uploadid').val();
        const auditscheduleid = $('#auditscheduleid').val();

        if (!fileuploadid) {
            clearFileUI();
            return;
        }

        showConfirmationDialog(
            'Confirmation',
            'Are you sure you want to remove this file?',
            function() {
                showLoading();

                $.ajax({
                    url: '/prauditfieldauditremovefile',
                    type: 'POST',
                    data: {
                        fileuploadid: fileuploadid,
                        auditscheduleid: auditscheduleid,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        hideLoading();

                        if (response.success) {
                            clearFileUI();
                            showAlert(response.message, 'Success');

                            if (fieldAuditValidator) {
                                fieldAuditValidator.element('#field_audit_file');
                            }
                        } else {
                            showAlert(response.message || 'Error removing file', 'Error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        let message = 'Error removing file';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showAlert(message, 'Error');
                        console.error('AJAX Error:', xhr);
                    }
                });
            }
        );
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

    function formatFileSize(bytes) {
        if (!bytes) return '';
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ============================================
    // UI STATE FUNCTIONS
    // ============================================
    function disableAllFields(disabled = true) {
        // Handle CKEditor
        if (window.editor) {
            try {
                if (disabled) {
                    if (typeof window.editor.enableReadOnlyMode === 'function') {
                        window.editor.enableReadOnlyMode('manual');
                    } else {
                        window.editor.isReadOnly = true;
                    }
                    $('.ck-editor__editable').addClass('ck-editor__editable_disabled');
                } else {
                    if (typeof window.editor.disableReadOnlyMode === 'function') {
                        window.editor.disableReadOnlyMode('manual');
                    } else {
                        window.editor.isReadOnly = false;
                    }
                    $('.ck-editor__editable').removeClass('ck-editor__editable_disabled');
                }
            } catch (e) {
                console.error('Error setting editor readonly:', e);
                // Fallback
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

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    function stripHtmlTags(html) {
        if (!html) return '';
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        return tempDiv.textContent || tempDiv.innerText || '';
    }

    // ============================================
    // WINDOW EVENT HANDLERS
    // ============================================
    window.addEventListener('beforeunload', function(e) {
        if (formChanged && currentStatus !== 'F') {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Make functions globally available
    window.removeFile = removeFile;
    window.removeUploadedFile = removeFile;
    window.showAlert = showAlert;
</script>

@endsection
