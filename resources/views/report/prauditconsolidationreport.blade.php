@section('content')
@section('title', 'Performance Audit Report')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = optional($sessionchargedel)->deptcode ?? '';
    $make_dept_disable = $deptcode ? 'disabled' : '';
@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<style>
    .ck-editor__editable[role="textbox"] {
        min-height: 400px !important;
    }

    .ck-editor__editable {
        max-height: 400px !important;
    }

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

    #remarksModal .modal-body {
        min-height: 60vh;
        max-height: 70vh;
        overflow-y: auto;
    }

    .alert-info {
        color: #ff0000;
    }

    .dt-length-info {
        font-size: 13px;
        color: #6c757d;
        padding: 5px 0;
    }

    .hide_this {
        display: none !important;
    }

    #tableshow {
        overflow: hidden;
    }

    .filter-panel {
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .auto-field {
        background-color: #e9f1f9;
        padding: 5px 10px;
        border-radius: 6px;
        border-left: 4px solid #3782ce;
        font-weight: 500;
        margin-bottom: 15px;
        color: black;
        font-size: 0.8rem;
    }

    .form-check-input:checked {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
    }

    .summary-table {
        margin-top: 20px;
        border: 1px solid #dee2e6;
    }

    .summary-table th {
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
    }

    .summary-table td {
        text-align: center;
        vertical-align: middle;
    }

    .clickable-count {
        cursor: pointer;
        color: #0d6efd;
        text-decoration: underline;
        font-weight: 600;
    }

    .clickable-count:hover {
        color: #0a58ca;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
    }

    .status-finalized {
        background-color: #d4edda;
        color: #155724;
    }

    .status-ongoing {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-pending {
        background-color: #f8d7da;
        color: #721c24;
    }

    .card-header[data-toggle="collapse"] {
        cursor: pointer;
    }

    .card-header[data-toggle="collapse"] i.fa-chevron-down {
        transition: transform 0.2s;
    }

    .card-header[data-toggle="collapse"][aria-expanded="true"] i.fa-chevron-down {
        transform: rotate(180deg);
    }

    #teamMembersList .list-group-item {
        padding: 0.5rem 0.75rem;
        border-left: none;
        border-right: none;
    }

    #teamMembersList .list-group-item:first-child {
        border-top: none;
    }

    #teamMembersList .list-group-item:last-child {
        border-bottom: none;
    }

    #teamMembersList .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .member-name {
        font-weight: 500;
        color: #2c3e50;
    }

    .team-head-badge {
        background-color: #ffc107;
        color: #212529;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
        font-weight: 600;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .remarks-content {
        background-color: white;
        font-size: 0.9rem;
    }

    .remarks-content p {
        margin-bottom: 0.5rem;
    }

    .remarks-content table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0.5rem;
    }

    .remarks-content table td,
    .remarks-content table th {
        border: 1px solid #dee2e6;
        padding: 0.3rem;
    }

    .ck-editor__editable {
        max-height: 800px;
        overflow-y: auto;
    }

    .ck-editor__editable[role="textbox"] {
        min-height: 400px;
    }

    .hide_this {
        display: none !important;
    }

    .btn-view-remarks:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .badge-custom {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 80px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        color: white !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        transition: 0.2s !important;
        cursor: pointer;
    }

    .badge-custom i {
        margin-right: 5px;
    }

    .dataTables_wrapper .dataTable td {
        vertical-align: middle !important;
    }

    .dataTables_wrapper .dataTable td.text-center {
        text-align: center !important;
    }

    .dataTables_length {
        display: block !important;
        margin-bottom: 10px !important;
    }

    .dataTables_length select {
        display: inline-block !important;
        width: auto !important;
        padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
    }

    .table-responsive {
        overflow: unset;
    }

    #lastSavedInfo {
        padding: 8px;
        background-color: #e8f5e9;
        border-radius: 4px;
        color: #2e7d32;
    }

    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        background-color: transparent;
        border: 1px solid transparent;
        border-bottom: none;
        margin-right: 4px;
        border-radius: 8px 8px 0 0;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        background-color: #f1f3f5;
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        background-color: #ffffff;
        color: #000;
        border: 1px solid #dee2e6;
        border-bottom: 2px solid #ffffff;
        font-weight: 500;
        position: relative;
        top: 2px;
    }

    #findings-tab.active {
        border-top: 3px solid #0d6efd;
    }

    #recommendations-tab.active {
        border-top: 3px solid #198754;
    }

    .file-preview {
        margin-top: 15px;
    }

    .file-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .file-preview iframe {
        width: 100%;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-group {
        gap: 5px;
    }

    .file-info .alert {
        padding: 10px 15px;
    }

    .file-preview .alert-warning {
        background-color: #fff3cd;
        border-color: #ffecb5;
        color: #856404;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        margin-top: 2px;
    }

    .form-check-input:checked {
        background-color: #3782ce;
        border-color: #3782ce;
        box-shadow: 0 0 0 2px rgba(55, 130, 206, 0.2);
    }

    .form-check-label {
        cursor: pointer;
        font-weight: 500;
        color: #2c3e50;
        margin-left: 8px;
    }

    .form-check-label i {
        margin-right: 5px;
    }

    .modal-footer .btn {
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #3782ce 0%, #2c6eb0 100%);
        border: none;
    }

    .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #2c6eb0 0%, #1e5a96 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(55, 130, 206, 0.3);
    }

    .modal-footer .btn-secondary {
        background: #6c757d;
        border: none;
    }

    .modal-footer .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }

    #confirmAddAttachmentBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    #attachmentsList {
        height: 450px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #attachments-tab.active {
        border-top: 3px solid #6f1987;
    }

    input#includeInReportCheckbox,
    input#verifyFileCheckbox,
    input#verifyRemarksCheckbox {
        display: block;
        margin: auto;
    }

    input#includeInReportCheckbox,
    input#verifyFileCheckbox,
    input#verifyRemarksCheckbox {
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(93, 135, 255, 0.25);
    }

    @supports (-webkit-appearance: none) and (not (-moz-appearance: none)) {
        .select2-container--default .select2-dropdown {
            margin-top: -22px;
        }
    }

    .file-link:hover {
        text-decoration: underline !important;
        color: #3782ce !important;
    }

    .file-link span {
        font-weight: 500;
    }

    .attachment-status-badge {
        font-size: 0.75rem;
        padding: 3px 8px;
    }

    .attachment-clickable {
        transition: background-color 0.2s ease;
        text-decoration: none;
    }

    .attachment-clickable:hover {
        background-color: #f8f9fa;
    }

    .attachment-clickable .file-name {
        text-decoration: none;
        color: #007bff;
    }

    .attachment-clickable:hover .file-name {
        text-decoration: none;
        color: #0056b3;
    }

    /* Remove underline from all links within attachments if any */
    .attachments-container a,
    .attachment-clickable a {
        text-decoration: none !important;
    }
</style>
<style>
    /* Responsive Styles for Institution Details Section */
    @media (max-width: 768px) {
        .institution-details-container .row {
            flex-direction: column;
        }

        .institution-details-container [class*="col-"] {
            width: 100%;
            margin-bottom: 1rem;
        }

        /* Make team members list more mobile-friendly */
        #teamMembersList .list-group-item {
            padding: 1rem 0.75rem;
        }

        #teamMembersList .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }

        #teamMembersList .btn-group {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        #teamMembersList .btn-group .btn {
            flex: 1;
            min-width: auto;
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        /* Make badges stack on mobile */
        #teamMembersList .d-flex.align-items-center {
            flex-wrap: wrap;
            gap: 6px;
        }

        .member-name {
            font-size: 0.95rem;
        }

        .team-head-badge,
        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        /* Tab navigation responsive */
        .nav-tabs {
            flex-wrap: wrap;
        }

        .nav-tabs .nav-link {
            padding: 6px 12px;
            font-size: 0.85rem;
            flex: 1;
            text-align: center;
        }

        /* Modal responsive for mobile */
        .modal-dialog {
            margin: 1rem;
            max-width: calc(100% - 2rem) !important;
        }

        .modal-body {
            padding: 1rem;
        }

        /* Attachments list responsive */
        #attachmentsList .list-group-item {
            flex-direction: column;
            align-items: flex-start !important;
        }

        #attachmentsList .list-group-item .btn-group {
            margin-top: 8px;
            width: 100%;
        }

        #attachmentsList .list-group-item .btn-group button {
            width: 100%;
        }

        /* Button group responsive */
        .d-flex.justify-content-center {
            flex-wrap: wrap;
            gap: 10px;
        }

        .d-flex.justify-content-center .btn {
            width: auto;
            min-width: 120px;
        }
    }

    @media (max-width: 480px) {

        /* Extra small devices */
        .card-header h6 {
            font-size: 0.9rem;
        }

        #teamMembersList .btn-group {
            flex-direction: column;
        }

        #teamMembersList .btn-group .btn {
            width: 100%;
        }

        .badge-custom {
            min-width: 60px !important;
            font-size: 11px !important;
            padding: 5px 8px !important;
        }

        .status-badge,
        .team-head-badge {
            font-size: 0.65rem;
            padding: 3px 6px;
        }
    }

    /* Tablet styles */
    @media (min-width: 769px) and (max-width: 1024px) {
        .institution-details-container .row {
            display: flex;
            flex-wrap: wrap;
        }

        .institution-details-container .col-md-4,
        .institution-details-container .col-md-8 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        #teamMembersList .d-flex {
            flex-wrap: wrap;
        }

        #teamMembersList .btn-group {
            flex-wrap: wrap;
        }
    }

    /* Improve team members list item layout */
    #teamMembersList .list-group-item {
        transition: all 0.3s ease;
    }

    #teamMembersList .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    /* Better spacing for badges */
    #teamMembersList .d-flex.align-items-center {
        flex-wrap: wrap;
        gap: 6px;
    }

    /* Make clickable areas larger on mobile */
    @media (max-width: 768px) {

        .btn-view-remarks,
        .btn-view-file {
            min-height: 38px;
        }

        .attachment-clickable {
            padding: 12px !important;
        }

        /* Improve select2 on mobile */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        /* Make CKEditor toolbar responsive */
        .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
            flex-wrap: wrap;
        }

        .ck.ck-toolbar .ck-toolbar__items {
            flex-wrap: wrap;
        }

        .ck.ck-button {
            padding: 4px 8px !important;
        }
    }


    /* Responsive CKEditor toolbar */
    @media (max-width: 768px) {
        .ck.ck-editor__main>.ck-editor__editable {
            min-height: 250px !important;
            max-height: 350px !important;
        }

        .ck.ck-toolbar .ck-toolbar__items {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }

        .ck.ck-toolbar .ck-button {
            margin: 2px !important;
        }

        /* Improve scroll on mobile */
        .remarks-content {
            font-size: 0.85rem;
        }

        .remarks-content table {
            display: block;
            overflow-x: auto;
        }
    }

    /* Better modal responsiveness */
    @media (max-width: 768px) {
        .modal-dialog.modal-lg {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .modal-header h3 {
            font-size: 1.2rem;
        }

        .modal-footer {
            flex-direction: column;
            gap: 8px;
        }

        .modal-footer .btn {
            width: 100%;
            margin: 0;
        }
    }

    /* Smooth animations */
    .list-group-item {
        transition: all 0.3s ease;
    }

    .btn-group .btn {
        transition: all 0.2s ease;
    }

    /* Loading overlay responsive */
    @media (max-width: 768px) {
        .cube {
            width: 30px;
            height: 30px;
        }
    }
</style>

<div class="loading-overlay">
    <div class="cube"></div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color">
                Performance Audit Report
            </div>
            <div class="card-body">
                <form id="prauditreportForm" name="prauditreportForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Department</label>
                            <select class="form-select select2" id="deptcode" name="deptcode" {{ $make_dept_disable }}>
                                <option value="">---Select Department---</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->deptcode }}"
                                        {{ $deptcode == $department->deptcode ? 'selected' : '' }}>
                                        {{ $department->deptelname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Financial Year</label>
                            <select class="form-select select2" id="financialyear" name="financialyear">
                                <option value="">---Select Financial Year---</option>
                                @foreach ($financialyear as $financialyear)
                                    <option value="{{ $financialyear->financialyearcode }}">
                                        {{ $financialyear->financialyear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Performance Audit Title</label>
                            <select class="form-select select2" id="audit_title" name="audit_title">
                                <option value="">---Select Audit Title---</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="text-center mt-3">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <button class="btn btn-primary" type="submit" id="submitBtn">
                                    Submit
                                </button>
                                <button type="button" class="btn btn-danger" id="reset_button" onclick="reset_form()">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4" id="summarySection" style="display: none;">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color">
                Audit Progress Summary
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover datatables-basic"
                        style='overflow:auto; width:100%;position:relative;' id="summaryTable">
                        <thead>
                            <tr>
                                <th class="text-center">S. No</th>
                                <th class="text-center">Region</th>
                                <th class="text-center">District</th>
                                <th class="text-center">Total Institutions</th>
                                <th class="text-center">Institutions Verified</th>
                                <th class="text-center">Institutions Pending</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                        </tbody>
                        <tfoot>
                            <tr class="table-info fw-bold">
                                <td colspan="3" class="text-end">Total:</td>
                                <td id="totalInstitutions" class="text-center">0</td>
                                <td id="totalfinalized" class="text-center">0</td>
                                <td id="totalPending" class="text-center">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12" id="institutionDetailsSection" style="display: none;">
    <div class="card card_border">
        <div class="card-header card_header_color" id="selectedInstitutionName">
            Performance Audit Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Select Institution</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label required">Institution</label>
                                <select class="form-select" id="institution" name="institution">
                                    <option value="">Select Institution</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4" id="teamMembersSection" style="display: none;">
                                <div class="card">
                                    <div class="card-header bg-light py-2" id="teamMembersHeading"
                                        style="cursor: pointer;">
                                        <h6 class="mb-0">
                                            Audit Team Members
                                            <span class="badge bg-primary ms-2" id="teamMembersCount">0</span>
                                        </h6>
                                    </div>
                                    <div id="teamMembers-collapse" class="show"
                                        aria-labelledby="teamMembersHeading">
                                        <div class="card-body p-2">
                                            <div id="teamMembersList" class="list-group">
                                                <div class="text-center text-muted py-3">No team members found</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <ul class="nav nav-tabs px-2 card-header-tabs" id="remarksTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="findings-tab" data-bs-toggle="tab"
                                        data-bs-target="#findings" type="button" role="tab"
                                        aria-controls="findings" aria-selected="true">
                                        Findings
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="recommendations-tab" data-bs-toggle="tab"
                                        data-bs-target="#recommendations" type="button" role="tab"
                                        aria-controls="recommendations" aria-selected="false">
                                        Recommendations
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="attachments-tab" data-bs-toggle="tab"
                                        data-bs-target="#attachments" type="button" role="tab"
                                        aria-controls="attachments" aria-selected="false">
                                        Attachments
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="remarksTabsContent">
                                <div class="tab-pane fade show active" id="findings" role="tabpanel"
                                    aria-labelledby="findings-tab">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Enter audit findings here
                                        </small>
                                    </div>
                                    <textarea class="form-control" id="findings_remarks" name="findings_remarks" rows="4"
                                        placeholder="Enter findings..."></textarea>
                                    <small class="text-danger d-none" id="findings_error"></small>
                                </div>

                                <div class="tab-pane fade" id="recommendations" role="tabpanel"
                                    aria-labelledby="recommendations-tab">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Enter audit recommendations here
                                        </small>
                                    </div>
                                    <textarea class="form-control" id="recommendations_remarks" name="recommendations_remarks" rows="4"
                                        placeholder="Enter recommendations..."></textarea>
                                    <small class="text-danger d-none" id="recommendations_error"></small>
                                </div>

                                <div class="tab-pane fade" id="attachments" role="tabpanel"
                                    aria-labelledby="attachments-tab">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Choose files from the institution to include in this audit report.
                                        </small>
                                    </div>
                                    <div id="attachmentsList" class="mb-3">
                                        <div class="text-center text-muted py-3">No attachments selected</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary me-2" id="saveDraftBtn">
                                        Save Draft
                                    </button>
                                    <button type="button" class="btn btn-success me-2" id="finalizebtn">
                                        Finalize
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Remarks Modal -->
<div class="modal fade" id="memberRemarksModal" tabindex="-1" aria-labelledby="memberRemarksModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" style="max-width: 85%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#3782ce;">
                <h3 class="text-white" id="memberRemarksModalLabel">
                    <span id="modalMemberName"></span>'s Remarks
                    <span id="modalTeamHeadBadge" class="badge bg-warning ms-2" style="display: none;">Team
                        Head</span>
                </h3>
                <button type="button" id="close_member_remarks" class="btn-close bg-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card border-primary">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            Remarks
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="remarksContent" class="remarks-content p-3 bg-white border rounded"
                            style="min-height: 200px; max-height: 500px; overflow-y: auto;"></div>
                        <div id="noRemarksMessage" class="text-center text-muted py-4" style="display: none;">
                            <p class="mb-0">No remarks added by this member</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="verifyRemarksCheckbox"
                            style="cursor: pointer;" data-member-index="">
                        <label class="form-check-label" for="verifyRemarksCheckbox"
                            style="cursor: pointer; font-weight: 500;">
                            Verified these remarks
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmVerifyRemarksBtn">
                    Confirm
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- File View Modal -->
<div class="modal fade" id="fileViewModal" tabindex="-1" aria-labelledby="fileViewModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" style="max-width: 85%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#3782ce;">
                <h3 class="text-white" id="fileViewModalLabel">
                    <span id="fileModalMemberName"></span>'s File
                    <span id="fileModalTeamHeadBadge" class="badge bg-warning ms-2" style="display: none;">Team
                        Head</span>
                </h3>
                <button type="button" id="close_file_view" class="btn-close bg-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fileContent" class="file-content"></div>
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="verifyFileCheckbox"
                            style="cursor: pointer;">
                        <label class="form-check-label" for="verifyFileCheckbox"
                            style="cursor: pointer; font-weight: 500;">
                            Verified this file
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="includeInReportCheckbox"
                            style="cursor: pointer;">
                        <label class="form-check-label" for="includeInReportCheckbox"
                            style="cursor: pointer; font-weight: 500;">
                            Include this file in the audit report
                        </label>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmFileActionsBtn">
                    Confirm
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="current_user_id"
    value="{{ session('user') ? session('user')->userid : (session('user') ? session('user')->deptuserid : '') }}">

<script>
    let teamMembersData = [];
    let currentInstitutionId = null;
    let findingsEditor = null;
    let recommendationsEditor = null;
    let originalFindings = '';
    let originalRecommendations = '';
    let selectedAttachmentIds = [];
    let currentFilter = {
        deptcode: '',
        financialyear: '',
        praudittitleid: ''
    };
    let summaryDataTable = null;
    let currentFileMember = null;

    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        $('#institutionDetailsSection').hide();
        $('#summarySection').hide();

        initializeCKEditors();

        const deptcode = $('#deptcode').val();
        currentFilter.deptcode = deptcode;

        if (deptcode && $('#financialyear').val()) {
            loadAuditTitles(deptcode, $('#financialyear').val());
        }

        $('#deptcode').on('change', function() {
            const deptcode = $(this).val();
            currentFilter.deptcode = deptcode;

            if (deptcode && $('#financialyear').val()) {
                loadAuditTitles(deptcode, $('#financialyear').val());
            }
        });

        $('#financialyear').on('change', function() {
            const financialyear = $(this).val();
            currentFilter.financialyear = financialyear;

            let deptcode = $('#deptcode').val();
            if (!deptcode && $('#deptcode').prop('disabled')) {
                deptcode = $('#deptcode').val();
            }

            if (deptcode && financialyear) {
                loadAuditTitles(deptcode, financialyear);
            }
        });

        $('#audit_title').on('change', function() {
            const praudittitleid = $(this).val();
            currentFilter.praudittitleid = praudittitleid;

            if (praudittitleid) {
                loadPrauditReportRemarks(praudittitleid);
            } else {
                if (findingsEditor) findingsEditor.setData('');
                if (recommendationsEditor) recommendationsEditor.setData('');
                originalFindings = '';
                originalRecommendations = '';
            }
        });

        $('#prauditreportForm').validate({
            rules: {
                deptcode: {
                    required: true
                },
                financialyear: {
                    required: true
                },
                audit_title: {
                    required: true
                }
            },
            messages: {
                deptcode: "Please select department",
                financialyear: "Please select financial year",
                audit_title: "Please select audit title"
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.mb-3').append(error);
            },
            submitHandler: function(form) {
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
                loadSummaryData();
            }
        });

        $(document).on('change', '#institution', function(e) {
            e.stopPropagation();
            const institutionId = $(this).val();
            const auditTitle = $('#audit_title option:selected').text();
            const selectedOption = $(this).find('option:selected');

            if (institutionId) {
                currentInstitutionId = institutionId;
                $('#selectedInstitutionName').html(
                    `Performance Audit Details - ${auditTitle}`
                );

                loadInstitutionRemarks(institutionId);
            } else {
                resetInstitutionView();
            }
        });

        $('#saveDraftBtn').on('click', function() {
            saveRemarks();
        });


        $('#finalizebtn').on('click', function() {
            finalizeReport();
        });

        $(document).on('click', '#process_button', function() {
            const action = $(this).data('action');
            if (action === 'verify') {
                processVerification();
            } else if (action === 'finalize') {
                processFinalization();
            }
        });

        $(document).on('click', '#confirmAddAttachmentBtn', function() {
            const btn = $(this);

            if (!currentFileMember || !currentFileMember.fileuploadid) {
                showAlert('Invalid file data', 'Error');
                $('#fileViewModal').modal('hide');
                return;
            }

            if (selectedAttachmentIds.includes(currentFileMember.fileuploadid)) {
                showAlert('This file is already in the attachments list.', 'Warning');
                $('#fileViewModal').modal('hide');
                return;
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...');

            selectedAttachmentIds.push(currentFileMember.fileuploadid);

            updateFileInReportFlag(currentFileMember.fileuploadid, true, function(success) {
                if (success) {
                    displayAttachments(selectedAttachmentIds, window.attachmentFlags || {});
                    showAlert(
                        `File "${currentFileMember.filename}" has been added to the report attachments.`,
                        'Success');
                    $('#fileViewModal').modal('hide');
                    $('#attachments-tab').tab('show');
                } else {
                    selectedAttachmentIds = selectedAttachmentIds.filter(id => id !==
                        currentFileMember.fileuploadid);
                    showAlert('Error adding file to report', 'Error');
                }
                btn.prop('disabled', false).html('Confirm');
            });
        });

        $(document).on('hidden.bs.modal', '#fileViewModal', function() {
            $('#includeInReportCheckbox').prop('checked', false);
            $('#confirmAddAttachmentBtn').prop('disabled', true);
            currentFileMember = null;
        });

        $(document).on('change', '#includeInReportCheckbox', function() {
            const isChecked = $(this).prop('checked');
            $('#confirmAddAttachmentBtn').prop('disabled', !isChecked);
        });
    });

    function initializeCKEditors() {
        if (!document.getElementById("findings_remarks") || !document.getElementById("recommendations_remarks")) {
            console.error('Remarks elements not found');
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
                    checkForImages(data.content);
                    if (hasImage) {
                        setTimeout(() => {
                            showAlert('Images are not allowed in remarks.', 'Warning');
                        }, 100);
                    }
                }
            });
        }

        const editorConfig = {
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
            placeholder: 'Enter remarks here...',
            fontFamily: {
                options: [
                    'default', 'Arial, Helvetica, sans-serif',
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
            height: '400px',
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
                'MultiLevelList', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange', 'uploadImage'
            ],
            extraPlugins: [PasteHandlerPlugin]
        };

        CKEDITOR.ClassicEditor
            .create(document.getElementById("findings_remarks"), editorConfig)
            .then(editor => {
                findingsEditor = editor;
                editor.model.document.on('change:data', () => {
                    const content = editor.getData();
                    if (content.includes('<img')) {
                        const cleanedContent = content.replace(/<img[^>]*>/g, '');
                        editor.setData(cleanedContent);
                        showAlert('Images are not allowed in remarks', 'Warning');
                    }
                });
            })
            .catch(error => {
                console.error('Findings editor initialization error:', error);
            });

        CKEDITOR.ClassicEditor
            .create(document.getElementById("recommendations_remarks"), editorConfig)
            .then(editor => {
                recommendationsEditor = editor;
                editor.model.document.on('change:data', () => {
                    const content = editor.getData();
                    if (content.includes('<img')) {
                        const cleanedContent = content.replace(/<img[^>]*>/g, '');
                        editor.setData(cleanedContent);
                        showAlert('Images are not allowed in remarks', 'Warning');
                    }
                });
            })
            .catch(error => {
                console.error('Recommendations editor initialization error:', error);
            });
    }

    function loadAuditTitles(deptcode, financialyear) {
        if (!deptcode && $('#deptcode').prop('disabled')) {
            deptcode = $('#deptcode').val();
        }

        if (!deptcode) {
            return;
        }

        $.ajax({
            url: "{{ route('Performance.getpreportaudittitles') }}",
            type: "POST",
            data: {
                deptcode: deptcode,
                financialyear: financialyear,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const titleDropdown = $('#audit_title');
                titleDropdown.empty().append('<option value="">---Select Audit Title---</option>');

                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(title => {
                        titleDropdown.append(
                            `<option value="${title.praudittitleid}">${title.titleename}</option>`
                        );
                    });
                } else {
                    titleDropdown.append('<option disabled>No Audit Titles Available</option>');
                }
            },
            error: function() {
                $('#audit_title').empty().append('<option value="">---Select Audit Title---</option>');
                showAlert('Error loading audit titles', 'Error');
            }
        });
    }

    function loadPrauditReportRemarks(praudittitleid) {
        $.ajax({
            url: "{{ route('Performance.getPrauditReportRemarks') }}",
            type: "POST",
            data: {
                deptcode: currentFilter.deptcode,
                praudittitleid: praudittitleid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                let findings = '';
                let recommendations = '';
                let attachments = [];
                let attachmentFlags = {};
                let reportStatus = null;

                if (response.success && response.data) {
                    if (response.data.findings) {
                        findings = response.data.findings.remarks || '';
                    }
                    if (response.data.recommendations) {
                        recommendations = response.data.recommendations.remarks || '';
                    }
                    if (response.data.attachments && response.data.attachments.fileuploadid) {
                        attachments = Array.isArray(response.data.attachments.fileuploadid) ?
                            response.data.attachments.fileuploadid : [];

                        if (response.data.attachments.fileinreportflag) {
                            const flags = response.data.attachments.fileinreportflag;
                            if (typeof flags === 'object') {
                                attachmentFlags = flags;
                            } else if (typeof flags === 'string') {
                                try {
                                    attachmentFlags = JSON.parse(flags);
                                } catch (e) {
                                    attachmentFlags = {};
                                }
                            }
                        }
                    }
                    // status can be null now
                    reportStatus = response.data.status || null;
                }

                window.attachmentFlags = attachmentFlags;
                selectedAttachmentIds = attachments;

                const decodedFindings = decodeRemarks(findings);
                const decodedRecommendations = decodeRemarks(recommendations);

                const hasFindings = decodedFindings &&
                    decodedFindings !== '""' &&
                    decodedFindings.trim() !== '' &&
                    decodedFindings !== '<p></p>' &&
                    decodedFindings !== '<p>&nbsp;</p>';

                const hasRecommendations = decodedRecommendations &&
                    decodedRecommendations !== '""' &&
                    decodedRecommendations.trim() !== '' &&
                    decodedRecommendations !== '<p></p>' &&
                    decodedRecommendations !== '<p>&nbsp;</p>';

                if (findingsEditor) {
                    findingsEditor.setData(hasFindings ? decodedFindings : '');
                    originalFindings = findings;
                }

                if (recommendationsEditor) {
                    recommendationsEditor.setData(hasRecommendations ? decodedRecommendations : '');
                    originalRecommendations = recommendations;
                }

                displayAttachments(selectedAttachmentIds, attachmentFlags);
                updateRemarksSectionStatus(reportStatus, hasFindings || hasRecommendations);
            },
            error: function() {
                console.error('Error loading report remarks');
                if (findingsEditor) findingsEditor.setData('');
                if (recommendationsEditor) recommendationsEditor.setData('');
                selectedAttachmentIds = [];
                displayAttachments([]);
                originalFindings = '';
                originalRecommendations = '';
                updateRemarksSectionStatus(null, false);
            }
        });
    }

    function displayAttachments(attachmentIds, attachmentFlags = {}) {

        const container = $('#attachmentsList');

        const isReportFinalized =
            window.currentReportStatus === 'P' ||
            window.currentReportStatus === 'A' ||
            window.currentReportStatus === 'F';

        const isInstitutionVerified =
            $('#institution option:selected').data('verification-status') === 'F';

        const isReadOnly = isReportFinalized || isInstitutionVerified;


        if (!attachmentIds || attachmentIds.length === 0) {
            container.html(
                '<div class="text-center text-muted py-3">No attachments selected</div>'
            );
            return;
        }

        $.ajax({
            url: "{{ route('Performance.getFileDetails') }}",
            type: "POST",
            data: {
                fileuploadids: attachmentIds,
                _token: '{{ csrf_token() }}'
            },

            success: function(response) {

                if (response.success && response.data) {

                    let html = '<div class="list-group">';
                    let serialNumber = 1;

                    response.data.forEach((file) => {

                        const fileId = file.fileuploadid;

                        let fileUrl = '';

                        if (file.filepath && file.filepath.startsWith('http')) {
                            fileUrl = file.filepath;

                        } else if (file.filepath && file.filepath.startsWith('/')) {
                            fileUrl = file.filepath;

                        } else if (file.filepath) {
                            fileUrl = `/${file.filepath}`;

                        } else {
                            fileUrl = '#';
                        }

                        if (fileUrl !== '#') {
                            fileUrl +=
                                (fileUrl.includes('?') ? '&' : '?') +
                                't=' + new Date().getTime();
                        }

                        html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center
                            ${fileUrl !== '#' ? 'attachment-clickable' : ''}"

                            ${fileUrl !== '#'
                                ? `data-url="${fileUrl}"`
                                : ''}

                            style="cursor: ${fileUrl !== '#' ? 'pointer' : 'default'};">

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-3" style="min-width: 35px; color: #6c757d;">
                                        ${serialNumber}.
                                    </span>
                                    <i class="fas fa-file-${getFileIcon(file.filename)} me-2"></i>
                                    <span class="file-name">
                                        ${escapeHtml(file.filename)}
                                    </span>
                                    <small class="text-muted ms-2">
                                        (${formatFileSize(file.filesize)})
                                    </small>
                                </div>
                            </div>

                            ${!isReadOnly ? `
                                <div class="btn-group">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger remove-attachment"
                                            data-fileid="${fileId}"
                                            data-filename="${escapeHtml(file.filename)}"
                                            onclick="event.stopPropagation();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            ` : ''}

                        </div>
                    `;

                        serialNumber++; // Increment serial number for next item
                    });

                    html += '</div>';
                    container.html(html);

                    $('.attachment-clickable')
                        .off('click')
                        .on('click', function() {
                            const url = $(this).data('url');
                            if (url && url !== '#') {
                                window.open(url, '_blank');
                            }
                        });

                    if (!isReadOnly) {
                        $('.remove-attachment')
                            .off('click')
                            .on('click', function(e) {
                                e.stopPropagation();
                                const fileId = parseInt($(this).data('fileid'));
                                removeAttachment(fileId);
                            });
                    }

                } else {
                    container.html(
                        '<div class="text-center text-warning py-3">No file details found</div>'
                    );
                }
            },

            error: function() {
                container.html(
                    '<div class="text-center text-danger py-3">Error loading attachments</div>'
                );
            }
        });
    }

    function removeAttachment(fileId) {
        window.pendingRemoveFileId = fileId;

        passing_alert_value(
            'Confirmation',
            'Are you sure you want to remove this attachment from the report?',
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        $('#process_button')
            .off('click')
            .one('click', function(event) {

                const fileIdToRemove = window.pendingRemoveFileId;

                updateFileInReportFlag(fileIdToRemove, false, function(success) {

                    if (success) {

                        selectedAttachmentIds = selectedAttachmentIds.filter(
                            id => id !== fileIdToRemove
                        );

                        displayAttachments(
                            selectedAttachmentIds,
                            window.attachmentFlags || {}
                        );

                        if (currentInstitutionId) {
                            loadInstitutionRemarks(currentInstitutionId);
                        } else {
                            resetInstitutionView();
                        }

                        passing_alert_value(
                            'Success',
                            'Attachment removed from report successfully',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );

                    } else {

                        passing_alert_value(
                            'Error',
                            'Error removing file from report',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                });

                delete window.pendingRemoveFileId;
            });
    }

    function updateFileInReportFlag(fileId, includeInReport, callback) {
        const flag = includeInReport ? 'Y' : 'N';

        $.ajax({
            url: "{{ route('Performance.updateFileInReportFlag') }}",
            type: "POST",
            data: {
                fileuploadid: fileId,
                praudittitleid: currentFilter.praudittitleid,
                fileinreportflag: flag,
                financialyear: currentFilter.financialyear,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                if (response.success) {
                    window.attachmentFlags = response.data.all_flags || {};
                    // Update the local attachments array from response if needed
                    if (response.data.report_attachments) {
                        selectedAttachmentIds = response.data.report_attachments;
                    }
                    if (callback) callback(true);
                } else {
                    showAlert(response.message || 'Error updating file flag', 'Error');
                    if (callback) callback(false);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error updating file flag';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'Error');
                if (callback) callback(false);
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image';
        if (ext === 'pdf') return 'pdf';
        if (['doc', 'docx'].includes(ext)) return 'word';
        if (['xls', 'xlsx'].includes(ext)) return 'excel';
        return 'alt';
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 KB';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }


    function updateRemarksSectionStatus(status, hasContent = false) {
        const saveDraftBtn = $('#saveDraftBtn');
        const finalizeBtn = $('#finalizebtn');

        window.currentReportStatus = status;

        const READ_ONLY_LOCK_ID = 'finalized';

        saveDraftBtn.prop('disabled', false);
        finalizeBtn.prop('disabled', false);

        saveDraftBtn.removeClass('hide_this');
        finalizeBtn.removeClass('hide_this');

        if (findingsEditor) {
            try {
                findingsEditor.disableReadOnlyMode(READ_ONLY_LOCK_ID);
            } catch (e) {}
        }
        if (recommendationsEditor) {
            try {
                recommendationsEditor.disableReadOnlyMode(READ_ONLY_LOCK_ID);
            } catch (e) {}
        }

        if (status === null || status === undefined) {
            saveDraftBtn.html('Save Draft')
                .removeClass('btn-secondary btn-success')
                .addClass('btn-primary')
                .prop('disabled', false);


        } else if (status === 'Y') {
            saveDraftBtn.html('Update Draft')
                .addClass('btn-primary')
                .removeClass('btn-secondary btn-success')
                .prop('disabled', false);

            const hasFindings = findingsEditor && findingsEditor.getData() &&
                findingsEditor.getData().trim() !== '' &&
                findingsEditor.getData() !== '<p></p>';
            const hasRecommendations = recommendationsEditor && recommendationsEditor.getData() &&
                recommendationsEditor.getData().trim() !== '' &&
                recommendationsEditor.getData() !== '<p></p>';

            if (hasFindings || hasRecommendations) {
                finalizeBtn.html('Finalize')
                    .prop('disabled', false)
                    .removeClass('btn-secondary hide_this')
                    .addClass('btn-success');
            } else {
                finalizeBtn.html('Finalize')
                    .prop('disabled', true)
                    .addClass('btn-secondary')
                    .removeClass('btn-success hide_this');
            }
        } else if (status === 'P' || status === 'A' || status === 'F') {
            saveDraftBtn.html('Save Draft')
                .prop('disabled', true)
                .addClass('hide_this');

            finalizeBtn.html('Finalized')
                .prop('disabled', true)
                .addClass('btn-success')
                .removeClass('hide_this');

            if (findingsEditor) {
                findingsEditor.enableReadOnlyMode(READ_ONLY_LOCK_ID);
            }
            if (recommendationsEditor) {
                recommendationsEditor.enableReadOnlyMode(READ_ONLY_LOCK_ID);
            }

        }
    }

    function loadSummaryData() {
        showLoading();
        const deptcode = $('#deptcode').val();
        const financialyear = $('#financialyear').val();
        const praudittitleid = $('#audit_title').val();

        if (praudittitleid) {
            loadPrauditReportRemarks(praudittitleid);
        }

        $.ajax({
            url: "{{ route('Performance.getauditsummary') }}",
            type: "POST",
            data: {
                deptcode: deptcode,
                financialyear: financialyear,
                praudittitleid: praudittitleid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#submitBtn').prop('disabled', false).html('Submit');

                if (response.success && response.data && response.data.length > 0) {
                    displaySummaryTable(response.data);
                    $('#summarySection').show();
                    $('#institutionDetailsSection').hide();

                    $('html, body').animate({
                        scrollTop: $('#summarySection').offset().top - 20
                    }, 500);
                } else {
                    $('#summarySection').hide();
                    $('#institutionDetailsSection').hide();
                    showAlert('No data found for the selected option', 'Info');
                }
                hideLoading();
            },
            error: function() {
                $('#submitBtn').prop('disabled', false).html('Submit');
                showAlert('Error loading summary data', 'Error');
                hideLoading();
            }
        });
    }

    function displaySummaryTable(data) {
        if ($.fn.DataTable.isDataTable('#summaryTable')) {
            $('#summaryTable').DataTable().destroy();
        }

        const tbody = $('#summaryTableBody');
        tbody.empty();

        let totalInst = 0,
            totalFinalized = 0,
            totalPending = 0;

        data.forEach((item, index) => {
            const instCount = parseInt(item.total_inst) || 0;
            const finalized = parseInt(item.finalized) || 0;
            const pending = parseInt(item.pending) || 0;

            totalInst += instCount;
            totalFinalized += finalized;
            totalPending += pending;

            const instClickable = instCount > 0;
            const finalizedClickable = finalized > 0;
            const pendingClickable = pending > 0;

            const row = `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>${item.regionname || 'N/A'}</td>
                    <td>${item.districtname || 'N/A'}</td>
                    <td class="text-center">
                        <span class="badge bg-info"
                            style="cursor: ${instClickable ? 'pointer' : 'default'}; font-size: 1rem; padding: 8px 12px; ${!instClickable ? 'opacity: 0.7;' : ''}"
                            ${instClickable ? `onclick="loadInstitutions('${item.regioncode}', '${item.distcode}')"` : ''}
                            title="${instClickable ? 'Click to view institutions' : 'No institutions available'}">
                            ${instCount}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success"
                            style="cursor: ${finalizedClickable ? 'pointer' : 'default'}; font-size: 1rem; padding: 8px 12px; ${!finalizedClickable ? 'opacity: 0.7;' : ''}"
                            ${finalizedClickable ? `onclick="loadFinalizedInstitutions('${item.regioncode}', '${item.distcode}')"` : ''}
                            title="${finalizedClickable ? 'Click to view finalized institutions' : 'No finalized institutions available'}">
                            ${finalized}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning"
                            style="cursor: ${pendingClickable ? 'pointer' : 'default'}; font-size: 1rem; padding: 8px 12px; ${!pendingClickable ? 'opacity: 0.7;' : ''}"
                            ${pendingClickable ? `onclick="loadPendingInstitutions('${item.regioncode}', '${item.distcode}')"` : ''}
                            title="${pendingClickable ? 'Click to view pending institutions' : 'No pending institutions available'}">
                            ${pending}
                        </span>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#totalInstitutions').text(totalInst);
        $('#totalfinalized').text(totalFinalized);
        $('#totalPending').text(totalPending);

        setTimeout(function() {
            initializeSummaryDataTable();
        }, 100);
    }

    function initializeSummaryDataTable() {
        if ($('#summaryTable tbody tr').length > 0) {
            summaryDataTable = $('#summaryTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No data available in table"
                },
                columnDefs: [{
                    className: "text-center",
                    targets: [0, 1, 2, 3, 4, 5]
                }],
                order: [
                    [1, 'asc'],
                    [2, 'asc']
                ],
                destroy: true
            });
        }
    }

    function loadInstitutions(regioncode, districtcode) {
        const deptcode = $('#deptcode').val();
        const financialyear = $('#financialyear').val();
        const praudittitleid = $('#audit_title').val();
        const auditTitle = $('#audit_title option:selected').text();

        showLoading();

        if (praudittitleid) {
            loadPrauditReportRemarks(praudittitleid);
        }

        $.ajax({
            url: "{{ route('Performance.getinstitutionsforaudit') }}",
            type: "POST",
            data: {
                deptcode: deptcode,
                financialyear: financialyear,
                praudittitleid: praudittitleid,
                regioncode: regioncode,
                districtcode: districtcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const institutionDropdown = $('#institution');

                if (institutionDropdown.hasClass('select2-hidden-accessible')) {
                    institutionDropdown.select2('destroy');
                }

                institutionDropdown.empty();
                $('#teamMembersSection').hide();
                $('#finalizesectn').hide();
                $('#selectedInstitutionName').html(
                    `Performance Audit Details - ${auditTitle}`
                );

                if (response.success && response.data && response.data.length > 0) {
                    institutionDropdown.append('<option value="">-- Select Institution --</option>');

                    response.data.forEach(inst => {
                        let displayName = inst.inst_display_name || inst.instename ||
                            `Institution ${inst.instid}`;
                        let statusText = '';

                        if (inst.verification_status === 'F') {
                            statusText = ' (Verified)';
                        }
                        if (inst.verification_status === 'N' || inst.verification_status === null) {
                            statusText = ' (Pending)';
                        }

                        institutionDropdown.append(
                            `<option value="${inst.instid}"
                                data-regioncode="${regioncode}"
                                data-districtcode="${districtcode}"
                                data-verification-status="${inst.verification_status || 'N'}">
                                ${displayName}${statusText}
                            </option>`
                        );
                    });

                    institutionDropdown.prop('disabled', false);
                    $('#institutionDetailsSection').show();

                    $('html, body').animate({
                        scrollTop: $('#institutionDetailsSection').offset().top - 20
                    }, 500);
                } else {
                    institutionDropdown.append(
                        '<option value="" disabled>No Institutions Available</option>');
                    institutionDropdown.prop('disabled', true);
                    showAlert('No institutions found in this region/district', 'Info');
                }

                setTimeout(function() {
                    institutionDropdown.select2({
                        width: '100%',
                        placeholder: 'Select Institution',
                        allowClear: true,
                        templateSelection: formatInstitutionSelection
                    });
                }, 100);
                hideLoading();
            },
            error: function() {
                hideLoading();
                showAlert('Error loading institutions', 'Error');
            }
        });
    }

    function loadFinalizedInstitutions(regioncode, districtcode) {
        const deptcode = $('#deptcode').val();
        const financialyear = $('#financialyear').val();
        const praudittitleid = $('#audit_title').val();
        const auditTitle = $('#audit_title option:selected').text();

        showLoading();

        $.ajax({
            url: "{{ route('Performance.getinstitutionsforaudit') }}",
            type: "POST",
            data: {
                deptcode: deptcode,
                financialyear: financialyear,
                praudittitleid: praudittitleid,
                regioncode: regioncode,
                districtcode: districtcode,
                verification_status: 'F',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const institutionDropdown = $('#institution');

                if (institutionDropdown.hasClass('select2-hidden-accessible')) {
                    institutionDropdown.select2('destroy');
                }

                institutionDropdown.empty();
                $('#teamMembersSection').hide();
                $('#finalizesectn').hide();
                $('#selectedInstitutionName').html(
                    `Performance Audit Details - ${auditTitle} (Finalized Institutions)`
                );

                if (response.success && response.data && response.data.length > 0) {
                    institutionDropdown.append('<option value="">-- Select Institution --</option>');

                    response.data.forEach(inst => {
                        let displayName = inst.inst_display_name || inst.instename ||
                            `Institution ${inst.instid}`;

                        institutionDropdown.append(
                            `<option value="${inst.instid}"
                                data-regioncode="${regioncode}"
                                data-districtcode="${districtcode}"
                                data-verification-status="${inst.verification_status || 'F'}">
                                ${displayName} (Verified)
                            </option>`
                        );
                    });

                    institutionDropdown.prop('disabled', false);
                    $('#institutionDetailsSection').show();

                    $('html, body').animate({
                        scrollTop: $('#institutionDetailsSection').offset().top - 20
                    }, 500);
                } else {
                    institutionDropdown.append(
                        '<option value="" disabled>No Finalized Institutions Available</option>');
                    institutionDropdown.prop('disabled', true);
                    showAlert('No finalized institutions found in this region/district', 'Info');
                }

                setTimeout(function() {
                    institutionDropdown.select2({
                        width: '100%',
                        placeholder: 'Select Institution',
                        allowClear: true,
                        templateSelection: formatInstitutionSelection
                    });
                }, 100);
                hideLoading();
            },
            error: function() {
                hideLoading();
                showAlert('Error loading finalized institutions', 'Error');
            }
        });
    }

    function loadPendingInstitutions(regioncode, districtcode) {
        const deptcode = $('#deptcode').val();
        const financialyear = $('#financialyear').val();
        const praudittitleid = $('#audit_title').val();
        const auditTitle = $('#audit_title option:selected').text();

        showLoading();

        $.ajax({
            url: "{{ route('Performance.getinstitutionsforaudit') }}",
            type: "POST",
            data: {
                deptcode: deptcode,
                financialyear: financialyear,
                praudittitleid: praudittitleid,
                regioncode: regioncode,
                districtcode: districtcode,
                verification_status: 'pending',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const institutionDropdown = $('#institution');

                if (institutionDropdown.hasClass('select2-hidden-accessible')) {
                    institutionDropdown.select2('destroy');
                }

                institutionDropdown.empty();
                $('#teamMembersSection').hide();
                $('#finalizesectn').hide();
                $('#selectedInstitutionName').html(
                    `Performance Audit Details - ${auditTitle} (Pending Institutions)`
                );

                if (response.success && response.data && response.data.length > 0) {
                    institutionDropdown.append('<option value="">-- Select Institution --</option>');

                    response.data.forEach(inst => {
                        let displayName = inst.inst_display_name || inst.instename ||
                            `Institution ${inst.instid}`;

                        institutionDropdown.append(
                            `<option value="${inst.instid}"
                                data-regioncode="${regioncode}"
                                data-districtcode="${districtcode}"
                                data-verification-status="${inst.verification_status || 'N'}">
                                ${displayName} (Pending)
                            </option>`
                        );
                    });

                    institutionDropdown.prop('disabled', false);
                    $('#institutionDetailsSection').show();

                    $('html, body').animate({
                        scrollTop: $('#institutionDetailsSection').offset().top - 20
                    }, 500);
                } else {
                    institutionDropdown.append(
                        '<option value="" disabled>No Pending Institutions Available</option>');
                    institutionDropdown.prop('disabled', true);
                    showAlert('No pending institutions found in this region/district', 'Info');
                }

                setTimeout(function() {
                    institutionDropdown.select2({
                        width: '100%',
                        placeholder: 'Select Institution',
                        allowClear: true,
                        templateSelection: formatInstitutionSelection
                    });
                }, 100);
                hideLoading();
            },
            error: function() {
                hideLoading();
                showAlert('Error loading pending institutions', 'Error');
            }
        });
    }

    function formatInstitutionSelection(option) {
        return option.text;
    }

    function loadInstitutionRemarks(institutionId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('Performance.getinstitutionremarks') }}",
                type: "POST",
                data: {
                    instid: institutionId,
                    praudittitleid: currentFilter.praudittitleid,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        teamMembersData = response.data;
                        displayTeamMembers(response.data);
                        $('#teamMembersSection').show();
                        $('#finalizesectn').show();

                        if (response.file_flags) {
                            window.attachmentFlags = response.file_flags;
                        } else {
                            window.attachmentFlags = {};
                        }

                        window.currentPraudittransid = response.praudittransid;


                    } else {
                        teamMembersData = [];
                        $('#teamMembersList').html(
                            '<div class="text-center text-muted py-3">No team members found</div>'
                        );
                        $('#teamMembersCount').text('0');
                        $('#teamMembersSection').show();
                        $('#finalizesectn').show();
                        window.attachmentFlags = {};
                    }
                    resolve();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading remarks:', error);
                    teamMembersData = [];
                    $('#teamMembersList').html(
                        '<div class="text-center text-danger py-3">Error loading team members</div>'
                    );
                    $('#teamMembersCount').text('0');
                    $('#teamMembersSection').show();
                    $('#finalizesectn').show();
                    reject(error);
                }
            });
        });
    }

    function displayTeamMembers(members) {
        const listContainer = $('#teamMembersList');
        listContainer.empty();

        const isReportFinalized =
            window.currentReportStatus === 'P' ||
            window.currentReportStatus === 'A' ||
            window.currentReportStatus === 'F';

        const isInstitutionVerified =
            $('#institution option:selected').data('verification-status') === 'F';

        const isReadOnly = isReportFinalized || isInstitutionVerified;

        if (!members || members.length === 0) {
            listContainer.html('<div class="text-center text-muted py-3">No team members found</div>');
            $('#teamMembersCount').text('0');
            return;
        }

        $('#teamMembersCount').text(members.length);

        members.forEach((member, index) => {
            const decodedRemarks = decodeRemarks(member.remarks);

            const hasFile = !!(member.fileuploadid && member.filepath && member.filename);

            const isRemarksVerified = member.prremarksverifyflag === 'Y';
            const isFileVerified = member.prfileverifyflag === 'Y';
            const isFileInReport = member.fileinreportflag === 'Y';

            const isRemarksExists =
                decodedRemarks &&
                decodedRemarks !== '""' &&
                decodedRemarks !== '' &&
                decodedRemarks !== '<p></p>' &&
                decodedRemarks !== '<p>&nbsp;</p>';

            let remarksButtonText = 'View';
            let remarksButtonClass = 'btn-outline-primary';
            let remarksDisabled = false;

            if (!isRemarksExists) {
                remarksButtonText = 'No Remarks';
                remarksButtonClass = 'btn-secondary';
                remarksDisabled = true;
            }

            let statusBadges = '';

            const isFullyVerified =
                (
                    isRemarksExists ?
                    isRemarksVerified :
                    true
                ) &&
                (
                    hasFile ?
                    isFileVerified :
                    true
                );

            const hasAnyData = isRemarksExists || hasFile;

            if (hasAnyData && isFullyVerified) {
                statusBadges =
                    '<span class="badge bg-success ms-2"><i class="fas fa-check-circle"></i></span>';
            } else if (hasAnyData && !isReadOnly) {
                statusBadges =
                    '<span class="badge bg-warning ms-2"><i class="fas fa-clock"></i></span>';
            }

            let fileInReportBadge = '';
            if (hasFile && isFileInReport) {
                fileInReportBadge =
                    '<span class="badge bg-info ms-2">In Report</span>';
            }

            // If in read-only mode, disable all buttons and change text to "Verified"
            let viewButtonsHtml = '';

            if (isReadOnly) {

            } else {
                // Normal mode - show action buttons
                viewButtonsHtml = `
                <div class="btn-group">
                    <button class="btn btn-sm ${remarksButtonClass} btn-view-remarks"
                            onclick="viewMemberRemarks(${index})"
                            ${remarksDisabled ? 'disabled' : ''}
                            title="${isRemarksExists ? 'View Remarks' : 'No remarks'}">
                        <i class="fas fa-eye"></i> ${remarksButtonText}
                    </button>

                    ${hasFile ? `
                    <button class="btn btn-sm btn-outline-info btn-view-file"
                            onclick="viewMemberFile(${index})"
                            title="View File">
                        <i class="fas fa-paperclip"></i>
                    </button>` : ''}
                </div>
            `;
            }

            const memberItem = `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="member-name me-2">
                            ${escapeHtml(member.username || member.usertamilname || 'N/A')}
                        </span>

                        ${member.auditteamhead === 'Y'
                            ? '<span class="team-head-badge">Team Head</span>'
                            : ''}

                        ${statusBadges}
                        ${fileInReportBadge}
                    </div>

                    ${viewButtonsHtml}
                </div>
            </div>
        `;

            listContainer.append(memberItem);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }



    function showAlert(message, type = 'Success') {
        if (typeof passing_alert_value === 'function') {
            let header = '';
            switch (type) {
                case 'Error':
                    header = 'Error';
                    break;
                case 'Warning':
                    header = 'Warning';
                    break;
                case 'Success':
                default:
                    header = 'Success';
                    break;
            }

            passing_alert_value(
                header,
                message,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert_with_function'
            );
        } else {
            const alertDiv = $(`
            <div class="alert alert-${type === 'Error' ? 'danger' : type === 'Warning' ? 'warning' : 'success'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
                 style="z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000;" role="alert">
                <i class="fas fa-${type === 'Error' ? 'exclamation-circle' : type === 'Warning' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

            $('body').append(alertDiv);

            setTimeout(() => {
                alertDiv.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    }

    let currentRemarksMember = null;


    $(document).on('hidden.bs.modal', '#memberRemarksModal', function() {
        $('#verifyRemarksCheckbox').prop('checked', false);
        $('#confirmVerifyRemarksBtn').prop('disabled', true);
        currentRemarksMember = null;
    });

    $(document).on('hidden.bs.modal', '#fileViewModal', function() {
        $('#includeInReportCheckbox').prop('checked', false).prop('disabled', true);
        $('#verifyFileCheckbox').prop('checked', false).prop('disabled', false);
        $('#confirmFileActionsBtn').prop('disabled', true);
        currentFileMember = null;
    });

    $(document).on('change', '#verifyRemarksCheckbox', function() {
        const isChecked = $(this).prop('checked');
        $('#confirmVerifyRemarksBtn').prop('disabled', !isChecked);
    });

    $(document).on('change', '#verifyFileCheckbox', function() {

        const isVerifyChecked = $(this).prop('checked');

        const isReportFinalized =
            window.currentReportStatus === 'P' ||
            window.currentReportStatus === 'A' ||
            window.currentReportStatus === 'F';

        if (isReportFinalized) {

            $('#confirmFileActionsBtn')
                .prop('disabled', true);

            return;
        }


        if (isVerifyChecked) {

            $('#includeInReportCheckbox')
                .prop('disabled', false);

            $('#confirmFileActionsBtn')
                .prop('disabled', false);

        } else {

            $('#includeInReportCheckbox')
                .prop('disabled', true)
                .prop('checked', false);

            $('#confirmFileActionsBtn')
                .prop('disabled', true);
        }
    });

    $(document).on('change', '#includeInReportCheckbox', function() {

        const isChecked =
            $(this).prop('checked');

        const isVerifyChecked =
            $('#verifyFileCheckbox').prop('checked');

        if (isVerifyChecked || isChecked) {

            $('#confirmFileActionsBtn')
                .prop('disabled', false);

        } else {

            $('#confirmFileActionsBtn')
                .prop('disabled', true);
        }
    });

    $(document).on('click', '#confirmVerifyRemarksBtn', function() {
        const btn = $(this);
        if (!currentRemarksMember) {
            showAlert('Invalid member data', 'Error');
            $('#memberRemarksModal').modal('hide');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Verifying...');

        verifyMemberRemarks(currentRemarksMember.index, function(success) {
            if (success) {
                showAlert('Remarks verified successfully', 'Success');
                $('#memberRemarksModal').modal('hide');
                if (currentInstitutionId) {
                    loadInstitutionRemarks(currentInstitutionId);
                }
            } else {
                showAlert('Error verifying remarks', 'Error');
            }
            btn.prop('disabled', false).html('Confirm');
        });
    });

    $(document).on('click', '#confirmFileActionsBtn', function() {
        const btn = $(this);
        if (!currentFileMember) {
            showAlert('Invalid file data', 'Error');
            $('#fileViewModal').modal('hide');
            return;
        }

        const includeInReport = $('#includeInReportCheckbox').prop('checked');
        const verifyFile = $('#verifyFileCheckbox').prop('checked');

        if (!includeInReport && !verifyFile) {
            showAlert('Please select at least one action', 'Warning');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

        processFileActions(currentFileMember, includeInReport, verifyFile, function(success, message) {
            if (success) {
                showAlert(message || 'File actions completed successfully', 'Success');
                $('#fileViewModal').modal('hide');
                if (currentInstitutionId) {
                    loadInstitutionRemarks(currentInstitutionId);
                }
            } else {
                showAlert(message || 'Error processing file actions', 'Error');
            }
            btn.prop('disabled', false).html('Confirm');
        });
    });

    function viewMemberRemarks(index) {
        const member = teamMembersData[index];

        if (!member) {
            showAlert('Member data not found', 'Error');
            return;
        }

        currentRemarksMember = {
            index: index,
            member: member
        };

        $('#modalMemberName').text(member.username || 'Team Member');

        if (member.auditteamhead === 'Y') {
            $('#modalTeamHeadBadge').show();
        } else {
            $('#modalTeamHeadBadge').hide();
        }

        const decodedRemarks = decodeRemarks(member.remarks);
        const hasRemarks = decodedRemarks &&
            decodedRemarks !== '""' &&
            decodedRemarks !== '' &&
            decodedRemarks !== '<p></p>' &&
            decodedRemarks !== '<p>&nbsp;</p>';

        if (hasRemarks) {
            $('#remarksContent').html(decodedRemarks).show();
            $('#noRemarksMessage').hide();
        } else {
            $('#remarksContent').empty().hide();
            $('#noRemarksMessage').show();
        }

        const isReportFinalized = window.currentReportStatus === 'P' || window.currentReportStatus === 'A' || window
            .currentReportStatus === 'F';
        const isVerified = member.prremarksverifyflag === 'Y';

        if (isReportFinalized || isVerified || !hasRemarks) {
            $('#verifyRemarksCheckbox').prop('disabled', true);
            $('#confirmVerifyRemarksBtn').prop('disabled', true);
            if (isVerified) {
                $('#verifyRemarksCheckbox').prop('checked', true);
            }
        } else {
            $('#verifyRemarksCheckbox').prop('disabled', false).prop('checked', false);
            $('#confirmVerifyRemarksBtn').prop('disabled', true);
        }

        $('#verifyRemarksCheckbox').data('member-index', index);

        const modalElement = document.getElementById('memberRemarksModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

    function viewMemberFile(index) {
        const member = teamMembersData[index];

        if (!member) {
            showAlert('Member data not found', 'Error');
            return;
        }

        if (!member.fileuploadid || !member.filepath || !member.filename) {
            showAlert('No file attached for this member', 'Warning');
            return;
        }

        currentFileMember = {
            index: index,
            member: member
        };

        // Reset checkboxes
        $('#includeInReportCheckbox').prop('checked', false).prop('disabled', false);
        $('#verifyFileCheckbox').prop('checked', false).prop('disabled', false);
        $('#confirmFileActionsBtn').prop('disabled', true); // Changed from #confirmAddAttachmentBtn

        const isReportFinalized = window.currentReportStatus === 'P' || window.currentReportStatus === 'A' || window
            .currentReportStatus === 'F';

        if (isReportFinalized) {
            $('#includeInReportCheckbox').prop('disabled', true);
            $('#verifyFileCheckbox').prop('disabled', true);
            $('#confirmFileActionsBtn').prop('disabled', true);
        } else {
            if (member.prfileverifyflag === 'Y' && member.fileinreportflag === 'Y') {
                $('#includeInReportCheckbox').prop('checked', true).prop('disabled', true);
                $('#verifyFileCheckbox').prop('checked', true).prop('disabled', true);
                $('#confirmFileActionsBtn').prop('disabled', true);
            } else if (member.prfileverifyflag === 'Y' && member.fileinreportflag !== 'Y') {
                $('#verifyFileCheckbox').prop('checked', true).prop('disabled', true);
                $('#includeInReportCheckbox').prop('disabled', false).prop('checked', false);
                $('#confirmFileActionsBtn').prop('disabled', true);
            } else if (member.prfileverifyflag !== 'Y') {
                $('#verifyFileCheckbox').prop('checked', false).prop('disabled', false);
                $('#includeInReportCheckbox').prop('disabled', true).prop('checked', false);
                $('#confirmFileActionsBtn').prop('disabled', true);
            }
        }

        $('#fileContent').empty();

        const memberName = member.username || member.usertamilname || 'Unknown Member';
        $('#fileModalMemberName').text(memberName);

        if (member.auditteamhead === 'Y') {
            $('#fileModalTeamHeadBadge').show();
        } else {
            $('#fileModalTeamHeadBadge').hide();
        }

        const fileExtension = member.filename.split('.').pop().toLowerCase();

        let fileUrl = '';
        if (member.filepath.startsWith('http')) {
            fileUrl = member.filepath;
        } else if (member.filepath.startsWith('/')) {
            fileUrl = member.filepath;
        } else {
            fileUrl = `/${member.filepath}`;
        }

        fileUrl += (fileUrl.includes('?') ? '&' : '?') + 't=' + new Date().getTime();

        let fileContent = `
        <div class="file-info mb-3">
            <div class="alert alert-primary mb-0">
                <i class="fas fa-file me-2"></i>
                <strong>File Name:</strong> ${escapeHtml(member.filename)}
            </div>
        </div>
        <div class="file-preview">
        `;

        if (fileExtension === 'pdf') {
            fileContent += `
        <iframe src="${fileUrl}" style="width: 100%; height: 500px;" frameborder="0"></iframe>
        `;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
            fileContent += `
        <div class="text-center">
            <img src="${fileUrl}" alt="${escapeHtml(member.filename)}" class="img-fluid" style="max-height: 400px; cursor: pointer;" onclick="window.open('${fileUrl}', '_blank')">
            <p class="text-muted mt-2"><small>Click image to view full size</small></p>
        </div>
        `;
        } else {
            fileContent += `
        <div class="alert alert-warning text-center">
            <i class="fas fa-file fa-3x mb-3 d-block"></i>
            <p>This file type (${fileExtension.toUpperCase()}) cannot be previewed.</p>
            <p class="mb-0 small">You can still add it to the report attachments or verify it.</p>
        </div>
        `;
        }

        fileContent += `</div>`;

        $('#fileContent').html(fileContent);
        $('#fileViewModal').modal('show');
    }

    function verifyMemberRemarks(index, callback) {
        const member = teamMembersData[index];

        if (!member || !member.praudittransid) {
            if (callback) callback(false);
            return;
        }

        $.ajax({
            url: "{{ route('Performance.verifyMemberRemarks') }}",
            type: "POST",
            data: {
                praudittransid: member.praudittransid,
                verified: 'Y',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    member.prremarksverifyflag = 'Y';
                    if (callback) callback(true);
                } else {
                    if (callback) callback(false);
                }
            },
            error: function() {
                if (callback) callback(false);
            }
        });
    }

    function processFileActions(fileData, includeInReport, verifyFile, callback) {

        const member = fileData.member;

        if (!member || !member.praudittransid) {

            if (callback) {
                callback(false, 'Invalid file data');
            }

            return;
        }

        $.ajax({

            url: "{{ route('Performance.processFileActions') }}",

            type: "POST",

            data: {
                praudittransid: member.praudittransid,
                fileuploadid: member.fileuploadid,
                praudittitleid: currentFilter.praudittitleid,
                include_in_report: includeInReport ? 'Y' : 'N',
                verify_file: verifyFile ? 'Y' : 'N',
                financialyear: currentFilter.financialyear,
                _token: '{{ csrf_token() }}'
            },

            beforeSend: function() {
                showLoading();
            },

            success: function(response) {

                if (response.success) {

                    /*
                    |--------------------------------------------------------------------------
                    | Include in Report
                    |--------------------------------------------------------------------------
                    */

                    if (includeInReport) {

                        member.fileinreportflag = 'Y';

                        if (
                            member.fileuploadid &&
                            !selectedAttachmentIds.includes(member.fileuploadid)
                        ) {
                            selectedAttachmentIds.push(member.fileuploadid);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Verify File
                    |--------------------------------------------------------------------------
                    */

                    if (verifyFile) {

                        member.prfileverifyflag = 'Y';

                        /*
                        |--------------------------------------------------------------------------
                        | Recalculate local verification status
                        |--------------------------------------------------------------------------
                        */

                        const remarksVerified =
                            member.prremarksverifyflag === 'Y';

                        const fileVerified = !member.fileuploadid ||
                            member.prfileverifyflag === 'Y';

                        member.verification_status =
                            (remarksVerified && fileVerified) ?
                            'Verified' :
                            'Pending';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Attachments Tab
                    |--------------------------------------------------------------------------
                    */

                    if (includeInReport) {

                        const triggerEl =
                            document.querySelector('#attachments-tab');

                        if (triggerEl) {
                            bootstrap.Tab
                                .getOrCreateInstance(triggerEl)
                                .show();
                        }

                        displayAttachments(
                            selectedAttachmentIds,
                            window.attachmentFlags || {}
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Optional Table/UI Refresh
                    |--------------------------------------------------------------------------
                    */

                    // refreshInstitutionCounts();
                    // refreshVerificationBadges();
                    // reloadGrid();

                    if (callback) {
                        callback(true, response.message);
                    }

                } else {

                    if (callback) {
                        callback(false, response.message);
                    }
                }
            },

            error: function(xhr) {

                let errorMsg = 'Error processing file actions';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    errorMsg = xhr.responseJSON.message;
                }

                if (callback) {
                    callback(false, errorMsg);
                }
            },

            complete: function() {
                hideLoading();
            }
        });
    }


    function finalizeReport() {
        if (!currentFilter.praudittitleid) {
            showAlert('Please select an audit title first', 'Warning');
            return;
        }

        if (window.currentReportStatus === 'P') {
            showAlert('This report is already finalized', 'Info');
            return;
        }

        const findings = findingsEditor ? findingsEditor.getData() : '';
        const recommendations = recommendationsEditor ? recommendationsEditor.getData() : '';

        const tempDiv = document.createElement('div');

        tempDiv.innerHTML = findings;
        const findingsText = tempDiv.textContent || tempDiv.innerText || '';

        tempDiv.innerHTML = recommendations;
        const recommendationsText = tempDiv.textContent || tempDiv.innerText || '';

        const hasFindingsContent = findings &&
            findings !== '<p></p>' &&
            findings !== '<p>&nbsp;</p>' &&
            findingsText.trim().length > 0;

        const hasRecommendationsContent = recommendations &&
            recommendations !== '<p></p>' &&
            recommendations !== '<p>&nbsp;</p>' &&
            recommendationsText.trim().length > 0;

        if (!hasFindingsContent) {
            showAlert('Findings are required', 'Warning');
            return;
        }

        if (!hasRecommendationsContent) {
            showAlert('Recommendations are required', 'Warning');
            return;
        }

        if (findingsText.trim().length < 10) {
            showAlert('Findings must be at least 10 characters long before finalizing', 'Warning');
            return;
        }

        if (recommendationsText.trim().length < 10) {
            showAlert('Recommendations must be at least 10 characters long before finalizing', 'Warning');
            return;
        }

        $.ajax({
            url: "{{ route('Performance.getTitleFinalizationStatus') }}",
            type: "POST",
            data: {
                praudittitleid: currentFilter.praudittitleid,
                _token: '{{ csrf_token() }}'
            },
            success: function(statusResponse) {
                if (statusResponse.success) {
                    // Use the exact values from the response
                    const total = parseInt(statusResponse.data.total_institutions) || 0;
                    const unverified = parseInt(statusResponse.data.unverified_institutions) || 0;
                    const unverifiedList = statusResponse.data.unverified_list || [];


                    // Use the unverified count from the response, not from the list length
                    if (unverified > 0) {
                        // Pass both the list AND the exact count
                        showUnverifiedInstitutionsModal(unverifiedList, total, unverified);
                        return;
                    }

                    $('#process_button').data('action', 'finalize');

                    passing_alert_value(
                        'Confirmation',
                        'Are you sure you want to finalize this report? This cannot be undone.',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'forward_alert'
                    );
                } else {
                    showAlert('Error checking title finalization status', 'Error');
                }
            },
            error: function() {
                showAlert('Error checking title finalization status', 'Error');
            }
        });
    }

    function showUnverifiedInstitutionsModal(unverifiedList, totalCount, unverifiedCount = null) {
        let modal = $('#institutionVerificationModal');

        // Use the provided unverifiedCount if available, otherwise use list length
        const actualUnverifiedCount = unverifiedCount !== null ? unverifiedCount : unverifiedList.length;

        // Debug logging
        console.log('Modal - Total Count:', totalCount);
        console.log('Modal - Unverified Count:', actualUnverifiedCount);
        console.log('Modal - List Length:', unverifiedList.length);

        if (modal.length === 0) {
            const modalHtml = `
            <div class="modal fade" id="institutionVerificationModal" tabindex="-1"
                aria-labelledby="institutionVerificationModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-xl" style="max-width: 85%;">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#3782ce;">
                            <h3 class="text-white" id="institutionVerificationModalLabel">
                                Finalization - Verification Required
                            </h3>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>
                                    ${actualUnverifiedCount} out of ${totalCount} institution(s) need verification.
                                    Please verify all institutions before finalizing the report.
                                </strong>
                            </div>
<style>
    .stats-card {
        border: none;
        border-left: 5px solid;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .border-primary-left {
        border-left-color: #0d6efd;
    }

    .border-success-left {
        border-left-color: #198754;
    }

    .border-warning-left {
        border-left-color: #ffc107;
    }

    .stats-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #6c757d;
    }

    .stats-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 0;
    }
</style>

<div class="mb-3">
    <div class="row g-3">

        <div class="col-md-4">
            <div class="card stats-card border-primary-left">
                <div class="card-body">
                    <div class="stats-title">
                        Total Institutions
                    </div>

                    <h3 class="stats-value text-primary">
                        ${totalCount}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card border-success-left">
                <div class="card-body">
                    <div class="stats-title">
                        Verified
                    </div>

                    <h3 class="stats-value text-success">
                        ${totalCount - actualUnverifiedCount}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card border-warning-left">
                <div class="card-body">
                    <div class="stats-title">
                        Pending Verification
                    </div>

                    <h3 class="stats-value text-warning">
                        ${actualUnverifiedCount}
                    </h3>
                </div>
            </div>
        </div>

    </div>
</div>
                            <div id="institutionsWithUnverifiedParasSection">
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; position: relative;">
                                    <table class="table table-striped table-bordered w-100" id="institutionsWithUnverifiedParasTable" style="width:100%">
                                        <thead class="table-light"
                                            style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Institution Name</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
            $('body').append(modalHtml);
            modal = $('#institutionVerificationModal');
        } else {
            // Update the alert message if modal already exists
            modal.find('.alert-warning strong').html(
                `${actualUnverifiedCount} out of ${totalCount} institution(s) need verification. Please verify all institutions before finalizing the report.`
            );
            modal.find('.card-text.text-primary').text(totalCount);
            modal.find('.card-text.text-success').text(totalCount - actualUnverifiedCount);
            modal.find('.card-text.text-warning').text(actualUnverifiedCount);
        }

        const tbody = modal.find('#institutionsWithUnverifiedParasTable tbody');
        tbody.empty();

        if (unverifiedList.length === 0) {
            tbody.html(
                '<tr><td colspan="3" class="text-center text-success">All institutions are verified! You can finalize the report.</td></tr>'
                );
        } else {
            unverifiedList.forEach((inst, index) => {
                const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${escapeHtml(inst.instename || inst.inst_display_name || `Institution ${inst.instid}`)}</td>
                    <td>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i> Pending
                        </span>
                    </td>
                </tr>
            `;
                tbody.append(row);
            });
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#institutionsWithUnverifiedParasTable')) {
            $('#institutionsWithUnverifiedParasTable').DataTable().destroy();
        }

        // Initialize DataTable
        setTimeout(function() {
            $('#institutionsWithUnverifiedParasTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    emptyTable: "No unverified institutions found"
                },
                ordering: false,
                columnDefs: [{
                        targets: 0,
                        orderable: false
                    },
                    {
                        targets: 2,
                        orderable: false
                    }
                ]
            });
        }, 100);


        modal.modal('show');
    }


    function processFinalization() {
        $.ajax({
            url: "{{ route('Performance.finalizeReport') }}",
            type: "POST",
            data: {
                praudittitleid: currentFilter.praudittitleid,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#finalizebtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Finalizing...');
                $('#saveDraftBtn').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    updateRemarksSectionStatus('P', true);

                    passing_alert_value(
                        'Success',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    $('#institution').prop('disabled', true);

                    const selectedTitle = $('#audit_title option:selected');
                    selectedTitle.data('status', 'P');

                    loadSummaryData();
                } else {
                    showAlert(response.message || 'Error finalizing report', 'Error');
                    updateRemarksSectionStatus(window.currentReportStatus, true);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error finalizing report';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'Error');
                updateRemarksSectionStatus(window.currentReportStatus, true);
            },
            complete: function() {
                $('#finalizebtn').prop('disabled', false);
                $('#saveDraftBtn').prop('disabled', false);
            }
        });
    }


    function resetInstitutionDropdown() {
        const institutionDropdown = $('#institution');
        if (institutionDropdown.hasClass('select2-hidden-accessible')) {
            institutionDropdown.select2('destroy');
        }
        institutionDropdown.empty().append('<option value="">Select Institution</option>');
        setTimeout(function() {
            institutionDropdown.select2({
                width: '100%',
                placeholder: 'Select Institution',
                allowClear: true
            });
        }, 100);
    }

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    function saveRemarks() {
        if (!currentFilter.praudittitleid) {
            showAlert('Please select an audit title first', 'Warning');
            return;
        }

        if (window.currentReportStatus === 'P') {
            showAlert('This report is finalized and cannot be edited', 'Error');
            return;
        }

        if (!findingsEditor || !recommendationsEditor) {
            showAlert('Editors not initialized', 'Error');
            return;
        }

        const findings = findingsEditor.getData();
        const recommendations = recommendationsEditor.getData();

        const tempDiv = document.createElement('div');

        tempDiv.innerHTML = findings;
        const findingsText = tempDiv.textContent || tempDiv.innerText || '';

        tempDiv.innerHTML = recommendations;
        const recommendationsText = tempDiv.textContent || tempDiv.innerText || '';

        const hasFindingsContent = findings &&
            findings.trim() !== '' &&
            findings !== '<p></p>' &&
            findings !== '<p>&nbsp;</p>' &&
            findingsText.trim().length > 0;

        const hasRecommendationsContent = recommendations &&
            recommendations.trim() !== '' &&
            recommendations !== '<p></p>' &&
            recommendations !== '<p>&nbsp;</p>' &&
            recommendationsText.trim().length > 0;

        if (!hasFindingsContent && !hasRecommendationsContent) {
            showAlert('Please enter findings and recommendations', 'Error');
            return;
        }

        if (hasFindingsContent && findingsText.trim().length < 10) {
            showAlert('Findings must be at least 10 characters long', 'Error');
            return;
        }

        if (hasRecommendationsContent && recommendationsText.trim().length < 10) {
            showAlert('Recommendations must be at least 10 characters long', 'Error');
            return;
        }

        const currentAttachments = selectedAttachmentIds || [];

        $.ajax({
            url: "{{ route('Performance.saveinstitutionremarks') }}",
            type: "POST",
            data: {
                praudittitleid: currentFilter.praudittitleid,
                findings: findings,
                recommendations: recommendations,
                attachments: currentAttachments,
                financialyear: currentFilter.financialyear,
                instid: currentInstitutionId,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#saveDraftBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
            },
            success: function(response) {
                if (response.success) {
                    if (response.status) {
                        updateRemarksSectionStatus(response.status, true);
                    } else if (window.currentReportStatus === null) {
                        updateRemarksSectionStatus('Y', true);
                    }

                    showAlert('Remarks saved successfully', 'Success');
                } else {
                    showAlert(response.message || 'Error saving remarks', 'Error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error saving remarks';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.remarks) {
                        errorMsg = errors.remarks[0];
                    } else {
                        errorMsg = 'Validation failed: ' + JSON.stringify(errors);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'Error');
            },
            complete: function() {
                $('#saveDraftBtn').prop('disabled', false);
                if (window.currentReportStatus === 'Y') {
                    $('#saveDraftBtn').html('Update Draft');
                } else {
                    $('#saveDraftBtn').html('Save Draft');
                }
            }
        });
    }

    function decodeRemarks(remarks) {
        if (!remarks) return '';

        try {
            if (typeof remarks === 'string') {
                if (remarks.startsWith('"') && remarks.endsWith('"')) {
                    try {
                        return JSON.parse(remarks);
                    } catch (e) {
                        return remarks.replace(/^"|"$/g, '').replace(/\\"/g, '"');
                    }
                }
                return remarks.replace(/\\"/g, '"').replace(/\\n/g, '');
            }
            return remarks;
        } catch (e) {
            console.error('Error decoding remarks:', e);
            return remarks || '';
        }
    }

    function resetInstitutionView() {
        const auditTitle = $('#audit_title option:selected').text();

        if (auditTitle && auditTitle !== '---Select Audit Title---') {
            $('#selectedInstitutionName').html(
                `Performance Audit Details - ${auditTitle}`
            );
        } else {
            $('#selectedInstitutionName').html('Performance Audit Details');
        }

        $('#teamMembersSection').hide();
        $('#finalizesectn').hide();

        // Reset variables
        currentInstitutionId = null;
        teamMembersData = [];

        originalFindings = '';
        originalRecommendations = '';

        // Enable save draft button
        $('#saveDraftBtn').prop('disabled', false);
    }

    function reset_form() {
        const isDeptDisabled = $('#deptcode').prop('disabled');
        const currentDeptCode = $('#deptcode').val();

        if (!isDeptDisabled) {
            $('#deptcode').val('').trigger('change');
        }

        $('#financialyear').val('').trigger('change');
        $('#audit_title').empty().append('<option value="">---Select Audit Title---</option>');

        $('#summarySection').hide();
        $('#institutionDetailsSection').hide();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const validator = $('#prauditreportForm').validate();
        if (validator) {
            validator.resetForm();
        }

        currentInstitutionId = null;
        originalFindings = '';
        originalRecommendations = '';
        teamMembersData = [];
        selectedAttachmentIds = [];
        currentFilter = {
            deptcode: isDeptDisabled ? currentDeptCode : '',
            financialyear: '',
            praudittitleid: ''
        };

        if (findingsEditor) findingsEditor.setData('');
        if (recommendationsEditor) recommendationsEditor.setData('');

        resetInstitutionDropdown();
        $('#teamMembersSection').hide();
        $('#finalizesectn').hide();

        $('#finalizebtn').prop('disabled', true)
            .removeClass('btn-success')
            .addClass('btn-secondary');
    }
</script>
@endsection
