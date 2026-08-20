@extends('index2')
@section('content')
@section('title', 'Performance Audit Report')
@include('common.alert')

<style>
    /* Your existing styles remain the same */
    .wrap-text-lwf {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 600px;
    }

    .ck-powered-by-balloon {
        display: none !important;
    }

    .equal-height {
        display: flex;
        flex-wrap: wrap;
    }

    .equal-height .card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .dd-list .dd-item {
        padding: 1px;
    }

    #pdf-preview iframe {
        width: 100%;
        height: 100%;
        max-height: 590px;
        background-color: white;
    }

    #previewmodel_content {
        max-height: 800px;
        height: 800px;
    }

    .draggable-item {
        cursor: move;
    }

    .PartBFinalized .draggable-item {
        cursor: default;
    }

    .step-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px;
    }

    .step-btn {
        padding: 10px 20px;
        font-size: clamp(10px, 1.2vw, 12px);
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        border-radius: 6px;
        border: 1px solid transparent;
        font-weight: 500;
    }

    .step-btn.btn-outline-primary {
        background-color: transparent;
        border-color: #007bff;
        color: #007bff;
    }

    .step-btn.btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }

    .step-btn.btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #ffffff;
    }

    .step-btn.btn-warning:hover {
        background-color: #e0a800;
        border-color: #e0a800;
        color: #ffffff;
    }

    .step-btn.btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .step-btn.btn-info:hover {
        background-color: #138496;
        border-color: #138496;
        color: white;
    }

    .step-btn.btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .step-btn.btn-success:hover {
        background-color: #218838;
        border-color: #218838;
        color: white;
    }

    .step-btn.active-step {
        position: relative;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-weight: 600;
    }

    .step-btn.btn-outline-primary.active-step {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .step-btn.btn-warning.active-step {
        background-color: #e0a800;
        border-color: #e0a800;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        transform: translateX(5px);
    }

    .step-btn.btn-info.active-step {
        background-color: #138496;
        border-color: #138496;
        color: white;
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
        transform: translateX(5px);
    }

    .step-btn.btn-success.active-step {
        background-color: #218838;
        border-color: #218838;
        color: white;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        transform: translateX(5px);
    }

    .step-header {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
    }

    .sub-step-header {
        font-size: 20px;
        font-weight: normal;
        text-align: center;
        color: #2a3547 !important;
    }

    .iframe-container {
        display: flex;
        justify-content: center;
        height: 100%;
        width: 100%;
    }

    iframe {
        width: 99%;
        height: 640px;
        max-height: 640px !important;
        border: none;
    }

    #partc_iframe {
        width: 80%;
    }

    .dd-handle {
        background-color: #ffffff !important;
    }

    .activatestep {
        background-color: #5d87ff !important;
    }

    .activatestep span {
        color: #ffffff !important;
    }

    .activatestep span b {
        color: #ffffff !important;
    }

    .part_b_dragula,
    #scrollablecontent {
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 650px;
        height: 680px;
        padding-right: 10px;
    }

    #scrollablecontent::-webkit-scrollbar,
    .part_b_dragula::-webkit-scrollbar {
        width: 4px;
    }

    #scrollablecontent::-webkit-scrollbar-track,
    .part_b_dragula::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #scrollablecontent::-webkit-scrollbar-thumb,
    .part_b_dragula::-webkit-scrollbar-thumb {
        background: #bbbcbc;
    }

    #scrollablecontent::-webkit-scrollbar-thumb:hover,
    .part_b_dragula::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    .ck-editor__editable_inline {
        min-height: 450px !important;
        width: 100% !important;
    }

    #lwf_ckeditor {
        width: 100%;
        min-height: 60px;
        height: 60px;
    }

    .file-preview {
        margin-right: 15px;
    }

    body {
        color: #222 !important;
    }

    .disabled-step {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #e7e7e7;
    }

    #pdf-preview {
        height: 550px;
        max-height: 550px !important;
    }

    #pdf-preview.full-height {
        height: 650px !important;
        max-height: 650px !important;
    }

    .enble-chkbox {
        border-color: rgb(62, 62, 63);
        cursor: pointer;
    }

    .ck-content {
        font-family: 'TAU-Marutham', sans-serif;
        line-height: 1.6;
    }

    .ck-content p {
        margin-bottom: 15px;
    }

    .ck-content h1,
    .ck-content h2,
    .ck-content h3 {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .ck-editor__editable {
        max-height: 450px;
        overflow-y: auto;
    }

    .ck-content ul,
    .ck-content ol {
        margin-bottom: 15px;
        padding-left: 20px;
    }

    .section-item {
        background: #ffffff;
        border-left: 4px solid #007bff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .section-item:hover {
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .item-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 10px;
    }

    .item-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        background: #007bff;
        color: white;
    }

    .item-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .item-content {
        font-size: 15px;
        line-height: 1.7;
        color: #444;
    }

    .item-content p {
        margin-bottom: 10px;
    }

    .item-meta {
        margin-top: 15px;
        padding-top: 10px;
        font-size: 13px;
        color: #666;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-draft {
        background: #ffc107;
        color: #856404;
    }

    .badge-finalized {
        background: #28a745;
        color: white;
    }

    .badge-saved {
        background: #17a2b8;
        color: white;
    }

    .no-data {
        text-align: center;
        padding: 50px;
        color: #999;
        font-style: italic;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .print-btn {
        border-radius: 50px;
        padding: 12px 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .print-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        color: white;
    }

    .back-btn {
        border-radius: 50px;
        padding: 12px 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        background: #6c757d;
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background: #5a6268;
        color: white;
    }

    .save-draft-btn {
        border-radius: 50px;
        padding: 12px 30px;
        background: #ffc107;
        border: none;
        color: #333;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    .save-draft-btn:hover {
        background: #e0a800;
        color: #333;
    }

    .finalize-btn {
        border-radius: 50px;
        padding: 12px 30px;
        background: #28a745;
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .finalize-btn:hover {
        background: #218838;
        color: white;
    }

    .finalize-all-btn {
        border-radius: 50px;
        padding: 12px 30px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(40, 167, 69, 0.3);
    }

    .finalize-all-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        color: white;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
        padding: 20px;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .report-container {
            padding: 0;
        }

        .section-item {
            break-inside: avoid;
        }

        .print-btn,
        .back-btn,
        .save-draft-btn,
        .finalize-btn,
        .finalize-all-btn {
            display: none;
        }
    }

    .loading-spinner {
        text-align: center;
        padding: 50px;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .ckeditor-container {
        position: relative;
        min-height: 450px;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .ckeditor-container textarea {
        display: none;
    }

    .ckeditor-container .ck-editor__editable_inline {
        min-height: 450px !important;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .ckeditor-loading {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .ckeditor-container.ckeditor-ready .ck-editor__editable_inline {
        opacity: 1;
    }

    .section-item {
        opacity: 0;
        transform: translateY(10px);
        animation: slideUp 0.3s ease forwards;
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-badge {
        margin-left: 10px;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .status-saved {
        background: #28a745;
        color: white;
    }

    .status-Y {
        background: #ffc107;
        color: #333;
    }

    .status-pending {
        background: #dc3545;
        color: white;
    }

    .status-F {
        background: #28a745;
        color: white;
    }

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

    .file-upload-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }

    .file-preview-area {
        margin-bottom: 20px;
        min-height: 100px;
    }

    .uploaded-files-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .file-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .file-item i {
        font-size: 20px;
        color: #007bff;
    }

    .file-name {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .file-size {
        font-size: 12px;
        color: #666;
    }

    .no-files {
        text-align: center;
        padding: 40px;
        color: #999;
        font-style: italic;
    }

    .upload-buttons {
        margin-top: 15px;
    }

    .upload-buttons .btn-primary {
        background: #007bff;
        border: none;
        padding: 8px 20px;
    }

    .upload-buttons .btn-primary:hover {
        background: #0056b3;
    }

    .file-clickable {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }


    .card-fixed-width {
        max-width: 100%;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    @media (max-width: 768px) {
        .file-item {
            flex-wrap: wrap;
        }

        .file-name {
            width: 100%;
            margin-bottom: 5px;
        }
    }
</style>

<style>
    .file-item {
        transition: all 0.2s ease;
    }

    .file-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateX(2px);
        border-color: #adb5bd !important;
    }

    .file-details:hover {
        opacity: 0.8;
    }

    .file-serial {
        font-size: 14px;
        user-select: none;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-actions .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
        transform: scale(1.05);
    }

    .file-actions .btn-outline-danger {
        transition: all 0.2s ease;
    }

    .file-list-container {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .file-list-container::-webkit-scrollbar {
        width: 6px;
    }

    .file-list-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .file-list-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .file-list-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
<div class="loading-overlay">
    <div class="cube"></div>
</div>
<div class="card card_border" style="border-color: #7198b9">
    <div class="card-header card_header_color lang" key="" style="padding:10px;">
        Performance Audit Report
    </div>
    <br>
    <div class="card-body card_border">
        <div class="row">
            <div class="col-12">
                <div class="card card_border">
                    <div class="card-body" style="border-color: #7198b9">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label lang" key="department">Department</label>
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label">:</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="label_lang" id="deptNameDisplay" data-en=""
                                    data-ta="">Loading...</label>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">Audit Title</label>
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label">:</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="label_lang" id="auditTitleDisplay" data-en=""
                                    data-ta="">Loading...</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Report Date</label>
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label">:</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label id="reportDate">{{ date('d-m-Y') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row equal-height">
            <div class="col-md-2">
                <div class="card card_border">
                    <div class="card-body">
                        <div class="step-buttons" id="stepButtonsContainer">
                            <div class="loading-spinner" style="padding: 20px;">
                                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                                <p class="mt-2">Loading sections...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10 col-xxl-10">
                <div class="" id="scrollablecontent1">
                    <div class="">
                        <div class="form-container">
                            <div id="loadingIndicator" class="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading audit report sections...</p>
                            </div>

                            <div id="errorMessage" class="alert alert-danger no-print" style="display: none;">
                                Failed to load report data. Please try again.
                            </div>

                            <div id="sectionsContainer"></div>

                            <div class="action-buttons no-print">
                                <button type="button" class="btn btn-success me-2" id="finalizeAllBtn"
                                    onclick="finalfinalization()" style="display: none;">
                                    Finalize
                                </button>

                                <button class="btn btn-primary generateReportBtn" type="button" id="generateReportBtn"
                                    style="display: none;">
                                    <i class="fas fa-file-alt me-1"></i>
                                    <span class="lang" data-english="Generate Report"
                                        data-tamil="அறிக்கையை உருவாக்கு">Generate Report</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pdf-preview" style="min-height: 600px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="downloadReportBtn">
                    Download PDF
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/forms/select2.init.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
    // Generate Report functionality
    $(document).on("click", ".generateReportBtn", function() {
        let praudittitleid = '{{ $praudittitleid }}';
        let deptcode = '{{ $deptcode }}';
        let catcode = '{{ $catcode }}';
        let subcatid = '{{ $subcatid ?? '' }}';
        let financialyear = '{{ $financialyear }}';
        let lang = getLanguage();

        if (!praudittitleid || !deptcode) {
            showAlert('Report data is missing!', 'error');
            return;
        }

        showLoading();

        $.ajax({
            url: "{{ route('performance.generateReport') }}",
            type: "POST",
            data: {
                praudittitleid: praudittitleid,
                deptcode: deptcode,
                catcode: catcode,
                subcatid: subcatid,
                financialyear: financialyear,
                lang: lang,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.html) {
                    var iframe = document.createElement('iframe');
                    iframe.style.width = "100%";
                    iframe.style.height = "600px";
                    iframe.style.border = "1px solid #ccc";
                    iframe.srcdoc = response.html;

                    var container = document.getElementById('pdf-preview');
                    container.innerHTML = '';
                    container.appendChild(iframe);

                    // Store encrypted data for download
                    $("#downloadReportBtn")
                        .data("praudittitleid", response.encrypted.praudittitleid)
                        .data("deptcode", response.encrypted.deptcode)
                        .data("catcode", response.encrypted.catcode)
                        .data("subcatid", response.encrypted.subcatid)
                        .data("financialyear", response.encrypted.financialyear);

                    var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
                    pdfModal.show();
                } else {
                    showAlert(response.error || "No report HTML returned!", 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                let errorMsg = "Error generating report!";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                showAlert(errorMsg, 'error');
            }
        });
    });

    $(document).on("click", "#downloadReportBtn", function() {
        let praudittitleid = $(this).data("praudittitleid");
        let deptcode = $(this).data("deptcode");
        let catcode = $(this).data("catcode");
        let subcatid = $(this).data("subcatid");
        let financialyear = $(this).data("financialyear");
        let lang = getLanguage();

        if (!praudittitleid || !deptcode) {
            showAlert("Report data is missing!", 'error');
            return;
        }

        showLoading();

        const loadingOverlay = document.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                <div class="cube"></div>
                <div style="margin-top: 20px; color: #004aad; font-size: 16px; font-weight: 500; background: white; padding: 10px 20px; border-radius: 8px;">
                    Downloading PDF, please wait...
                </div>

            </div>
        `;
        }

        const url =
            `/performance/download-report?praudittitleid=${praudittitleid}&deptcode=${deptcode}&catcode=${catcode}&subcatid=${subcatid}&financialyear=${financialyear}&lang=${lang}`;

        const modal = bootstrap.Modal.getInstance(document.getElementById('pdfModal'));
        if (modal) {
            modal.hide();
        }
        fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const statusEl = document.getElementById('downloadStatus');
                if (statusEl) statusEl.textContent = 'Processing response...';

                if (!response.ok) {
                    throw new Error(`Download failed with status ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                const statusEl = document.getElementById('downloadStatus');
                if (statusEl) statusEl.textContent = 'Preparing download...';

                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;

                const today = new Date();
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                const todayDate = `${day}-${month}-${year}`;

                let filename = `Performance_Audit_Report_${todayDate}.pdf`;


                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                window.URL.revokeObjectURL(downloadUrl);

                const modal = bootstrap.Modal.getInstance(document.getElementById('pdfModal'));
                if (modal) {
                    modal.hide();
                }

                setTimeout(() => {
                    hideLoading();
                    const overlay = document.querySelector('.loading-overlay');
                    if (overlay) {
                        overlay.innerHTML = '<div class="cube"></div>';
                    }
                }, 500);
            })
            .catch(error => {
                console.error('Download error:', error);
                hideLoading();
                const overlay = document.querySelector('.loading-overlay');
                if (overlay) {
                    overlay.innerHTML = '<div class="cube"></div>';
                }
                showAlert('Failed to download report. Please try again.', 'error');
            });
    });
</script>
<script>
    let ckEditorInstances = {};
    let allSections = [];
    let currentActiveSection = null;
    let sectionStatus = {};

    $(document).ready(function() {
        const praudittitleid = '{{ $praudittitleid }}';
        const deptcode = '{{ $deptcode }}';
        const catcode = '{{ $catcode }}';
        const subcatid = '{{ $subcatid ?? '' }}';

        loadAllSections(praudittitleid, deptcode, catcode, subcatid);
    });

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    function showAlert(message, type = 'success') {
        if (typeof window.passing_alert_value === 'function') {
            window.passing_alert_value(
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

    function validateContentLength(content, sectioncode) {
        const textOnly = content.replace(/<[^>]*>/g, '').trim();
        if (textOnly.length < 10) {
            const sectionTitle = getSectionTitle(sectioncode);
            showAlert(`Remarks for "${sectionTitle}" must be at least 10 characters long`, 'warning');
            return false;
        }
        return true;
    }

    function getSectionTitle(sectioncode) {
        const section = allSections.find(s => s.sectioncode === sectioncode);
        if (section) {
            return getLanguage() === 'ta' ? section.titletname : section.titleename;
        }
        return sectioncode;
    }

    function loadAllSections(praudittitleid, deptcode, catcode, subcatid) {
        showLoading();
        $('#loadingIndicator').show();
        $('#sectionsContainer').hide();
        $('#errorMessage').hide();
        $('#finalizeAllBtn').hide();

        $.ajax({
            url: "{{ route('performance.getAllSections') }}",
            type: "POST",
            data: {
                praudittitleid: praudittitleid,
                deptcode: deptcode,
                catcode: catcode,
                subcatid: subcatid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                $('#loadingIndicator').hide();

                if (response.success && response.sections && response.sections.length > 0) {
                    allSections = response.sections;
                    sectionStatus = response.status || {};

                    // Ensure has_data is properly set for file sections
                    allSections.forEach(section => {
                        if (section.type === 'F' && sectionStatus[section.sectioncode]) {
                            // Check if the section has files in the content
                            const content = response.contents[section.sectioncode];
                            let hasFiles = false;

                            if (content) {
                                if (Array.isArray(content) && content.length > 0) {
                                    hasFiles = true;
                                } else if (content.fileuploadid) {
                                    hasFiles = true;
                                } else if (typeof content === 'string') {
                                    try {
                                        const parsed = JSON.parse(content);
                                        if (Array.isArray(parsed) && parsed.length > 0) {
                                            hasFiles = true;
                                        } else if (parsed && parsed.fileuploadid) {
                                            hasFiles = true;
                                        }
                                    } catch (e) {
                                        hasFiles = false;
                                    }
                                }
                            }

                            sectionStatus[section.sectioncode].has_data = hasFiles;
                        }
                    });

                    renderNavigationButtons(response.sections);
                    renderAllSections(response.sections, response.contents);
                    updateFinalizeAllButton();
                    $('#finalizeAllBtn').show();

                    if (response.department) {
                        const lang = getLanguage();
                        if (lang === 'ta' && response.department.depttlname) {
                            $('#deptNameDisplay').text(response.department.depttlname);
                            $('#auditTitleDisplay').text(response.department.titletname);
                        } else {
                            $('#deptNameDisplay').text(response.department.deptelname);
                            $('#auditTitleDisplay').text(response.department.titleename);
                        }
                    }

                    if (response.sections.length > 0) {
                        showSection(response.sections[0].sectioncode);
                    }

                    $('#sectionsContainer').show();
                } else {
                    $('#sectionsContainer').html(
                        '<div class="no-data"><i class="fas fa-file-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i><p>No sections available for this audit report.</p></div>'
                    );
                    $('#sectionsContainer').show();
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                $('#loadingIndicator').hide();
                $('#errorMessage').show();
                console.error('Error loading sections:', error);
                showAlert('Failed to load sections: ' + error, 'error');
            }
        });
    }

    function updateFinalizeAllButton() {
        const allFinalized = allSections.every(section => {
            const sectionData = sectionStatus[section.sectioncode] || {};
            const status = sectionData.status;
            return status === 'A';
        });

        const finalizeBtn = $('#finalizeAllBtn');
        const generateReportBtn = $('#generateReportBtn');

        if (allFinalized) {
            finalizeBtn.prop('disabled', true);
            finalizeBtn.html('All Sections Finalized');
            finalizeBtn.removeClass('btn-success').addClass('btn-secondary');
            finalizeBtn.css('opacity', '0.6');

            // Show Generate Report button when all sections are finalized
            generateReportBtn.show();
        } else {
            finalizeBtn.prop('disabled', false);
            finalizeBtn.html('Finalize');
            finalizeBtn.removeClass('btn-secondary').addClass('btn-success');
            finalizeBtn.css('opacity', '1');

            // Hide Generate Report button when sections are not all finalized
            generateReportBtn.hide();
        }
    }

    function renderNavigationButtons(sections) {
        const container = $('#stepButtonsContainer');
        container.empty();

        sections.forEach((section, index) => {
            const sectionData = sectionStatus[section.sectioncode] || {};
            const statusFlag = sectionData.status || '';
            const hasData = sectionData.has_data || false;

            let buttonClass = 'btn step-btn btn-outline-primary';

            if (statusFlag === 'F') {
                buttonClass = 'btn step-btn btn-success';
            } else if (statusFlag === 'Y') {
                buttonClass = 'btn step-btn btn-warning';
            } else if (statusFlag === 'A') {
                buttonClass = 'btn step-btn btn-info';
            } else if (hasData) {
                buttonClass = 'btn step-btn btn-primary';
            }

            const button = $(`
                <button class="${buttonClass}"
                        data-sectioncode="${section.sectioncode}"
                        data-statusflag="${statusFlag}"
                        onclick="showSection('${section.sectioncode}')">
                    <span class="lang" key="${section.sectioncode}">
                        ${getLanguage() === 'ta' ? section.titletname : section.titleename}
                    </span>
                </button>
            `);
            container.append(button);
        });
    }

    let editorContentValue = '';

    function renderAllSections(sections, contents) {
        const container = $('#sectionsContainer');
        container.empty();

        sections.forEach((section, index) => {
            const content = contents[section.sectioncode] || '';
            const sectionData = sectionStatus[section.sectioncode] || {};
            const statusFlag = sectionData.status || '';
            const hasData = sectionData.has_data || false;
            const isFileUpload = section.type === 'F';

            let statusDisplay = 'Pending';
            let badgeClass = 'bg-danger';

            if (statusFlag === 'F' || statusFlag === 'A') {
                statusDisplay = 'Finalized';
                badgeClass = 'bg-success';
            } else if (statusFlag === 'Y') {
                statusDisplay = 'Draft';
                badgeClass = 'bg-warning';
            } else if (hasData) {
                statusDisplay = 'Saved';
                badgeClass = 'bg-info';
            }

            let buttonHtml = '';
            let contentHtml = '';

            // For file upload sections (type 'F')
            if (isFileUpload) {
                const isFinalMode = (statusFlag === 'F' || statusFlag === 'A');

                // Check if content has files (array of files or single file object)
                let hasFiles = false;
                let filesArray = [];

                if (content && Array.isArray(content) && content.length > 0) {
                    hasFiles = true;
                    filesArray = content;
                } else if (content && content.fileuploadid) {
                    // Single file object (backward compatibility)
                    hasFiles = true;
                    filesArray = [content];
                } else if (content && typeof content === 'string') {
                    try {
                        const parsed = JSON.parse(content);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            hasFiles = true;
                            filesArray = parsed;
                        } else if (parsed && parsed.fileuploadid) {
                            hasFiles = true;
                            filesArray = [parsed];
                        }
                    } catch (e) {
                        hasFiles = false;
                    }
                }

                let fileHtml = '';

                if (hasFiles && filesArray.length > 0) {
                    // Display multiple files in a clean list layout
                    fileHtml = '<div class="file-list-container">';

                    filesArray.forEach((fileInfo, fileIndex) => {
                        let filePath = fileInfo.filepath || '';
                        filePath = filePath.replace(/\\/g, '/');

                        // Get file icon based on mime type or extension
                        let fileIcon = 'fa-file-pdf';
                        let iconColor = 'text-danger';

                        if (fileInfo.mimetype) {
                            if (fileInfo.mimetype.includes('image')) {
                                fileIcon = 'fa-file-image';
                                iconColor = 'text-primary';
                            } else if (fileInfo.mimetype.includes('word')) {
                                fileIcon = 'fa-file-word';
                                iconColor = 'text-primary';
                            } else if (fileInfo.mimetype.includes('excel')) {
                                fileIcon = 'fa-file-excel';
                                iconColor = 'text-success';
                            } else if (fileInfo.mimetype.includes('pdf')) {
                                fileIcon = 'fa-file-pdf';
                                iconColor = 'text-danger';
                            }
                        }

                        const isFinalMode = (statusFlag === 'F' || statusFlag === 'A');

                        fileHtml += `
                        <div class="file-item d-flex align-items-center p-3 mb-2" style="border: 1px solid #dee2e6; border-radius: 8px; background: #fff; transition: all 0.3s ease;">
                            <div class="file-serial me-3" style="min-width: 30px; font-weight: 600; color: #6c757d;">
                                ${fileIndex + 1}.
                            </div>
                            <div class="file-icon-wrapper me-3" style="min-width: 40px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                                <i class="fas ${fileIcon} ${iconColor} fs-5"></i>
                            </div>
                            <div class="file-details flex-grow-1" style="cursor: pointer; min-width: 0;" onclick="window.open('/${filePath}', '_blank')">
                                <div class="d-flex flex-column">
                                    <span class="d-flex align-items-center gap-2 mt-1" style="font-size: 14px; max-width: 300px;" title="${escapeHtml(fileInfo.filename)}">
                                        ${escapeHtml(fileInfo.filename)}
                                    </span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size: 12px;">${formatFileSize(fileInfo.filesize)}</small>
                                        ${fileInfo.fileinreportflag === 'Y' ? '<span class="badge bg-success" style="font-size: 10px;">In Report</span>' : ''}
                                    </div>
                                </div>
                            </div>
                            ${!isFinalMode ? `
                            <div class="file-actions ms-3">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="event.stopPropagation(); removeSectionFile('${section.sectioncode}', '${fileInfo.fileuploadid}')"
                                        title="Remove File" style="border-radius: 6px; padding: 6px 10px; white-space: nowrap;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            ` : ''}
                        </div>
                        `;
                    });

                    fileHtml += '</div>';
                }

                // Store file IDs as JSON array
                const fileIdsArray = hasFiles ? filesArray.map(f => f.fileuploadid) : [];

                if (!isFinalMode) {
                    if (hasFiles) {
                        // Has files - show appropriate buttons
                        if (statusFlag === 'Y') {
                            buttonHtml = `
                                <div class="item-meta mt-3">
                                    <button type="button" class="btn button_save lang" onclick="saveFileSection('${section.sectioncode}', 'Y')">
                                        Update Draft
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="confirmAndSaveFile('${section.sectioncode}', 'F')">
                                        Finalize
                                    </button>
                                </div>
                            `;
                        } else {
                            buttonHtml = `
                                <div class="item-meta mt-3">
                                    <button type="button" class="btn button_save lang" onclick="saveFileSection('${section.sectioncode}', 'Y')">
                                        Save Draft
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="confirmAndSaveFile('${section.sectioncode}', 'F')">
                                        Finalize
                                    </button>
                                </div>
                            `;
                        }
                    } else {
                        // No files - only show Save Draft
                        buttonHtml = `
                            <div class="item-meta mt-3">
                                <button type="button" class="btn button_save lang" onclick="saveFileSection('${section.sectioncode}', 'Y')">
                                    Save Draft
                                </button>
                            </div>
                        `;
                    }
                } else {
                    buttonHtml = `<div class="item-meta mt-3"></div>`;
                }

                // Build upload buttons HTML
                let uploadButtonsHtml = '';
                if (!isFinalMode && !hasFiles) {
                    uploadButtonsHtml = `
                        <div class="upload-buttons mt-3">
                            <input type="file" id="file-input-${section.sectioncode}" style="display: none;" accept="application/pdf">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('file-input-${section.sectioncode}').click()">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Allowed file types: PDF, max size: 2MB
                        </small>
                    `;
                }

                contentHtml = `
                    <div class="file-upload-container">
                        <div id="file-upload-${section.sectioncode}" class="file-upload-area">
                            <div class="file-preview-area" id="file-preview-${section.sectioncode}">
                                ${hasFiles && filesArray.length > 0 ? fileHtml : '<div class="no-files text-center text-muted py-4">No files uploaded yet</div>'}
                            </div>
                            ${uploadButtonsHtml}
                            <input type="hidden" id="uploadid-${section.sectioncode}" name="uploadid" value='${JSON.stringify(fileIdsArray)}'>
                            <div class="text-danger small" id="file-error-${section.sectioncode}"></div>
                        </div>
                    </div>
                `;

                // Add file input change handler
                setTimeout(() => {
                    $(`#file-input-${section.sectioncode}`).off('change').on('change', function(e) {
                        const file = this.files[0];
                        if (file) {
                            uploadFile(section.sectioncode, this.files);
                        }
                        $(this).val('');
                    });
                }, 100);
            }
            // For editor sections (type 'E')
            else {
                const editorId = `editor_${section.sectioncode}`;


                // Handle content properly
                if (content && typeof content === 'string') {
                    editorContentValue = content;
                } else if (content && typeof content === 'object') {
                    editorContentValue = content.content || content.html || '';
                } else {
                    editorContentValue = '';
                }

                if (statusFlag === 'F' || statusFlag === 'A') {
                    buttonHtml = `<div class="item-meta mt-3"></div>`;
                } else if (statusFlag === 'Y') {
                    buttonHtml = `
                        <div class="item-meta mt-3">
                            <button type="button" class="btn button_save lang" onclick="saveSection('${section.sectioncode}', 'Y')">
                                Update Draft
                            </button>
                            <button type="button" class="btn btn-success" onclick="confirmAndSave('${section.sectioncode}', 'F')">
                                Finalize
                            </button>
                        </div>
                    `;
                } else {
                    buttonHtml = `
                        <div class="item-meta mt-3">
                            <button type="button" class="btn button_save lang" onclick="saveSection('${section.sectioncode}', 'Y')">
                                Save Draft
                            </button>
                        </div>
                    `;
                }

                contentHtml = `
                    <div class="ckeditor-container" id="container-${editorId}">
                        <textarea id="${editorId}" name="${editorId}" rows="10" cols="80">${escapeHtml(editorContentValue)}</textarea>
                    </div>
                `;
            }

            const sectionHtml = `
                <div class="section-item" id="section-${section.sectioncode}" data-sectioncode="${section.sectioncode}" style="display: none;">
                    <div class="item-header align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="item-number me-2">${index + 1}</div>
                            <div class="item-title">
                                ${getLanguage() === 'ta' ? section.titletname : section.titleename}
                            </div>
                        </div>
                        <span class="badge ${badgeClass}" id="status-${section.sectioncode}">
                            ${statusDisplay}
                        </span>
                    </div>
                    ${contentHtml}
                    ${buttonHtml}
                </div>
            `;

            container.append(sectionHtml);

            if (!isFileUpload) {
                const editorId = `editor_${section.sectioncode}`;
                const isReadOnly = (statusFlag === 'F' || statusFlag === 'A');
                initializeCKEditor(editorId, editorContentValue, isReadOnly);
            }
        });
    }

    function removeSectionFile(sectioncode, fileuploadid) {
        if (typeof window.passing_alert_value === 'function') {
            window.passing_alert_value(
                'Confirmation',
                'Are you sure you want to remove this file?',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            // Store the parameters for use in the confirmation callback
            window.pendingFileRemoval = {
                sectioncode: sectioncode,
                fileuploadid: fileuploadid
            };

            $(document).off('click', '#process_button').on('click', '#process_button', function() {
                $('#confirmation_alert').modal('hide');
                if (window.pendingFileRemoval) {
                    confirmRemoveFile(window.pendingFileRemoval.sectioncode, window.pendingFileRemoval
                        .fileuploadid);
                    window.pendingFileRemoval = null;
                }
            });
        } else {
            if (confirm('Are you sure you want to remove this file?')) {
                confirmRemoveFile(sectioncode, fileuploadid);
            }
        }
    }

    function confirmRemoveFile(sectioncode, fileuploadid) {
        showLoading();

        $.ajax({
            url: "{{ route('performance.removeSectionFile') }}",
            type: "POST",
            data: {
                praudittitleid: '{{ $praudittitleid }}',
                deptcode: '{{ $deptcode }}',
                catcode: '{{ $catcode }}',
                subcatid: '{{ $subcatid ?? '' }}',
                sectioncode: sectioncode,
                fileuploadid: fileuploadid,
                financialyear: '{{ $financialyear }}',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    // Update the UI with remaining files
                    refreshFilesAfterRemoval(sectioncode, response.remaining_files);

                    showAlert('File removed successfully', 'success');

                    // Update section status
                    if (sectionStatus[sectioncode]) {
                        const hasRemainingFiles = response.remaining_files && response.remaining_files
                            .length > 0;
                        sectionStatus[sectioncode].has_data = hasRemainingFiles;

                        if (!hasRemainingFiles && sectionStatus[sectioncode].status !== 'F' &&
                            sectionStatus[sectioncode].status !== 'A') {
                            // No files left and not finalized - reset status
                            sectionStatus[sectioncode].status = null;
                            updateNavigationButton(sectioncode, '');
                            updateFinalizeAllButton();

                            // Update action buttons to show only Save Draft
                            $(`#section-${sectioncode} .item-meta`).html(`
                            <button type="button" class="btn button_save lang" onclick="saveFileSection('${sectioncode}', 'Y')">
                                Save Draft
                            </button>
                        `);
                        }
                    }

                    // Update the hidden input with new file IDs
                    const uploadidInput = $(`#uploadid-${sectioncode}`);
                    if (uploadidInput.length) {
                        uploadidInput.val(JSON.stringify(response.remaining_files || []));
                    }
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                let errorMsg = 'Error removing file: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'error');
            }
        });
    }

    function refreshFilesAfterRemoval(sectioncode, remainingFileIds) {
        const previewContainer = $(`#file-preview-${sectioncode}`);
        const fileUploadArea = $(`#file-upload-${sectioncode}`);

        if (!previewContainer.length || !fileUploadArea.length) {
            console.error('Required containers not found for section:', sectioncode);
            return;
        }

        const sectionData = sectionStatus[sectioncode] || {};
        const isFinalMode = (sectionData.status === 'F' || sectionData.status === 'A');

        if (remainingFileIds && remainingFileIds.length > 0) {
            // Fetch details for remaining files
            $.ajax({
                url: "{{ route('Performance.getReportFileDetails') }}",
                type: "POST",
                data: {
                    fileuploadids: remainingFileIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        let fileHtml = '<div class="file-list-container">';

                        response.data.forEach((fileInfo, index) => {
                            let filePath = fileInfo.filepath || '';
                            filePath = filePath.replace(/\\/g, '/');

                            let fileIcon = 'fa-file-pdf';
                            let iconColor = 'text-danger';

                            if (fileInfo.mimetype) {
                                if (fileInfo.mimetype.includes('image')) {
                                    fileIcon = 'fa-file-image';
                                    iconColor = 'text-primary';
                                } else if (fileInfo.mimetype.includes('word')) {
                                    fileIcon = 'fa-file-word';
                                    iconColor = 'text-primary';
                                } else if (fileInfo.mimetype.includes('excel')) {
                                    fileIcon = 'fa-file-excel';
                                    iconColor = 'text-success';
                                } else if (fileInfo.mimetype.includes('pdf')) {
                                    fileIcon = 'fa-file-pdf';
                                    iconColor = 'text-danger';
                                }
                            }

                            fileHtml += `
                        <div class="file-item d-flex align-items-center p-3 mb-2" style="border: 1px solid #dee2e6; border-radius: 8px; background: #fff; transition: all 0.3s ease;">
                            <div class="file-serial me-3" style="min-width: 30px; font-weight: 600; color: #6c757d;">
                                ${index + 1}.
                            </div>
                            <div class="file-icon-wrapper me-3" style="min-width: 40px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                                <i class="fas ${fileIcon} ${iconColor} fs-5"></i>
                            </div>
                            <div class="file-details flex-grow-1" style="cursor: pointer; min-width: 0;" onclick="window.open('/${filePath}', '_blank')">
                                <div class="d-flex flex-column">
                                    <span class="d-flex align-items-center gap-2 mt-1" style="font-size: 14px; max-width: 300px;" title="${escapeHtml(fileInfo.filename)}">
                                        ${escapeHtml(fileInfo.filename)}
                                    </span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size: 12px;">${formatFileSize(fileInfo.filesize)}</small>
                                        ${fileInfo.fileinreportflag === 'Y' ? '<span class="badge bg-success" style="font-size: 10px;">In Report</span>' : ''}
                                    </div>
                                </div>
                            </div>
                            ${!isFinalMode ? `
                            <div class="file-actions ms-3">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="event.stopPropagation(); removeSectionFile('${sectioncode}', '${fileInfo.fileuploadid}')"
                                        title="Remove File" style="border-radius: 6px; padding: 6px 10px; white-space: nowrap;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            ` : ''}
                        </div>
                        `;
                        });

                        fileHtml += '</div>';
                        previewContainer.html(fileHtml);

                        // Remove any existing upload buttons since we have files
                        fileUploadArea.find('.upload-buttons').remove();
                        fileUploadArea.find('small.text-muted').remove();
                    }
                },
                error: function() {
                    previewContainer.html(
                        '<div class="no-files text-center text-muted py-4">Error loading file details</div>'
                    );
                }
            });
        } else {
            // No files remaining - show empty state and upload option
            previewContainer.html('<div class="no-files text-center text-muted py-4">No files uploaded yet</div>');

            // Show upload buttons if not in final mode
            if (!isFinalMode) {
                // Remove any existing upload buttons first to avoid duplicates
                fileUploadArea.find('.upload-buttons').remove();
                fileUploadArea.find('small.text-muted').remove();

                // Add the upload button area back
                const uploadHtml = `
                <div class="upload-buttons mt-3">
                    <input type="file" id="file-input-${sectioncode}" style="display: none;" accept="application/pdf">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('file-input-${sectioncode}').click()">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Allowed file types: PDF, max size: 2MB</small>
            `;

                fileUploadArea.append(uploadHtml);

                // Re-attach file input change handler
                setTimeout(() => {
                    $(`#file-input-${sectioncode}`).off('change').on('change', function(e) {
                        if (this.files.length > 0) {
                            uploadFile(sectioncode, this.files);
                        }
                        $(this).val('');
                    });
                }, 100);

                // Update the hidden input to empty array
                $(`#uploadid-${sectioncode}`).val('[]');
            }
        }
    }

    function saveFileSection(sectioncode, statusflag) {
        showLoading();

        const hasFile = sectionStatus[sectioncode]?.has_data || false;

        if (!hasFile) {
            hideLoading();
            showAlert('Please upload a file.', 'warning');
            return;
        }

        const praudittitleid = '{{ $praudittitleid }}';
        const deptcode = '{{ $deptcode }}';
        const catcode = '{{ $catcode }}';
        const subcatid = '{{ $subcatid ?? '' }}';

        $.ajax({
            url: "{{ route('performance.saveSectionContent') }}",
            type: "POST",
            data: {
                praudittitleid: praudittitleid,
                deptcode: deptcode,
                catcode: catcode,
                subcatid: subcatid,
                sectioncode: sectioncode,
                content: null,
                statusflag: statusflag,
                financialyear: '{{ $financialyear }}',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    const newStatusFlag = response.status;

                    // Update section status
                    if (!sectionStatus[sectioncode]) {
                        sectionStatus[sectioncode] = {};
                    }
                    sectionStatus[sectioncode].status = newStatusFlag;
                    sectionStatus[sectioncode].has_data = hasFile;

                    // Update badge
                    let statusDisplay = '';
                    let badgeClass = '';

                    if (newStatusFlag === 'F' || newStatusFlag === 'A') {
                        statusDisplay = 'Finalized';
                        badgeClass = 'bg-success';
                    } else if (newStatusFlag === 'Y') {
                        statusDisplay = 'Draft';
                        badgeClass = 'bg-warning';
                    }

                    $(`#status-${sectioncode}`).text(statusDisplay);
                    $(`#status-${sectioncode}`).removeClass('bg-danger bg-warning bg-success bg-info')
                        .addClass(badgeClass);

                    updateNavigationButton(sectioncode, newStatusFlag);
                    updateFinalizeAllButton();

                    const message = (newStatusFlag === 'F' || newStatusFlag === 'A') ?
                        'Section finalized successfully!' : 'Section saved as draft successfully!';
                    showAlert(message, 'success');

                    // Update buttons based on new status
                    if (newStatusFlag === 'F' || newStatusFlag === 'A') {
                        // Finalized - show disabled button
                        $(`#section-${sectioncode} .item-meta`).html(
                            '<button type="button" class="btn btn-success" disabled>Finalized</button>');

                        // Remove upload buttons
                        $(`#file-upload-${sectioncode} .upload-buttons`).remove();
                        $(`#file-upload-${sectioncode} small.text-muted`).remove();

                        // Remove delete/trash buttons from file items
                        $(`#file-preview-${sectioncode} .file-actions`).remove();

                        // Make file items non-interactive but allow viewing
                        $(`#file-preview-${sectioncode} .file-item`).css({
                            'cursor': 'default',
                            'pointer-events': 'none'
                        });
                        $(`#file-preview-${sectioncode} .file-details`).css('pointer-events', 'auto');

                    } else if (newStatusFlag === 'Y') {
                        // Draft - show Update Draft and Finalize buttons (only if files exist)
                        if (hasFile) {
                            $(`#section-${sectioncode} .item-meta`).html(`
                                <button type="button" class="btn button_save lang" onclick="saveFileSection('${sectioncode}', 'Y')">Update Draft</button>
                                <button type="button" class="btn btn-success" onclick="confirmAndSaveFile('${sectioncode}', 'F')">Finalize</button>
                            `);
                        } else {
                            $(`#section-${sectioncode} .item-meta`).html(`
                                <button type="button" class="btn button_save lang" onclick="saveFileSection('${sectioncode}', 'Y')">Save Draft</button>
                            `);
                        }
                    }
                } else {
                    showAlert('Failed to save section: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                let errorMsg = 'Error saving: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'error');
            }
        });
    }

    function confirmAndSaveFile(sectioncode, statusflag) {
        if (statusflag === 'F' || statusflag === 'Y') {
            const hasFile = sectionStatus[sectioncode]?.has_data || false;
            if (!hasFile) {
                showAlert('Please upload a file before finalizing this section.', 'warning');
                return;
            }
        }

        if (typeof window.passing_alert_value === 'function') {
            window.passing_alert_value(
                'Confirmation',
                `Do you want to finalize this section? This action cannot be undone.`,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );
            $(document).off('click', '#process_button').on('click', '#process_button', function() {
                $('#confirmation_alert').modal('hide');
                saveFileSection(sectioncode, statusflag);
            });
        } else {
            if (confirm('Do you want to finalize this section? This action cannot be undone.')) {
                saveFileSection(sectioncode, statusflag);
            }
        }
    }

    function uploadFile(sectioncode, files) {
        if (!files || files.length === 0) return;

        const file = files[0];

        if (file.type !== 'application/pdf') {
            showAlert('Only PDF files are allowed', 'warning');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showAlert('File size must be less than 2MB', 'warning');
            return;
        }

        showLoading();

        const formData = new FormData();
        formData.append('praudittitleid', '{{ $praudittitleid }}');
        formData.append('deptcode', '{{ $deptcode }}');
        formData.append('catcode', '{{ $catcode }}');
        formData.append('subcatid', '{{ $subcatid ?? '' }}');
        formData.append('sectioncode', sectioncode);
        formData.append('file', file);
        formData.append('financialyear', '{{ $financialyear }}');
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('performance.uploadSectionFile') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    // Ensure sectionStatus exists
                    if (!sectionStatus[sectioncode]) {
                        sectionStatus[sectioncode] = {};
                    }

                    // Set has_data to true since we just uploaded a file
                    sectionStatus[sectioncode].has_data = true;

                    // Set status to Y (draft) if not already set
                    if (!sectionStatus[sectioncode].status || sectionStatus[sectioncode].status === '') {
                        sectionStatus[sectioncode].status = 'Y';
                    }

                    // Update the UI
                    refreshFilePreview(sectioncode, response.file);
                    updateNavigationButton(sectioncode, sectionStatus[sectioncode].status);
                    updateFinalizeAllButton();

                    // Update action buttons to show Save Draft and Finalize
                    $(`#section-${sectioncode} .item-meta`).html(`
                    <button type="button" class="btn button_save lang" onclick="saveFileSection('${sectioncode}', 'Y')">
                        ${sectionStatus[sectioncode].status === 'Y' ? 'Update Draft' : 'Save Draft'}
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmAndSaveFile('${sectioncode}', 'F')">
                        Finalize
                    </button>
                `);

                    // Hide the upload button container after successful upload
                    $(`#file-upload-${sectioncode} .upload-buttons`).remove();
                    $(`#file-upload-${sectioncode} small.text-muted`).remove();

                    showAlert('File uploaded successfully', 'success');
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                let errorMsg = 'Error uploading file: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'error');
            }
        });
    }

    function removeSectionFile(sectioncode) {
        const lang = getLanguage();

        if (typeof window.passing_alert_value === 'function') {
            window.passing_alert_value(
                'Confirmation',
                'Are you sure you want to remove this file?',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );
            $(document).off('click', '#process_button').on('click', '#process_button', function() {
                $('#confirmation_alert').modal('hide');
                confirmRemoveFile(sectioncode);
            });
        } else {
            if (confirm('Are you sure you want to remove this file?')) {
                confirmRemoveFile(sectioncode);
            }
        }
    }

    function confirmRemoveFile(sectioncode) {
        showLoading();

        $.ajax({
            url: "{{ route('performance.clearSectionFile') }}",
            type: "POST",
            data: {
                praudittitleid: '{{ $praudittitleid }}',
                deptcode: '{{ $deptcode }}',
                catcode: '{{ $catcode }}',
                subcatid: '{{ $subcatid ?? '' }}',
                sectioncode: sectioncode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    refreshFilePreview(sectioncode, null);

                    // Show the upload button container again after file removal
                    $(`#section-${sectioncode} .upload-buttons`).show();

                    showAlert('File removed successfully', 'success');

                    if (sectionStatus[sectioncode]) {
                        sectionStatus[sectioncode].has_data = false;
                        if (sectionStatus[sectioncode].status !== 'F' && sectionStatus[sectioncode]
                            .status !== 'A') {
                            sectionStatus[sectioncode].status = null;
                            updateNavigationButton(sectioncode, '');
                            updateFinalizeAllButton();

                            // Update button HTML back to Save Draft only
                            if ($(`#section-${sectioncode} .item-meta`).html() && $(
                                    `#section-${sectioncode} .item-meta`).html().includes('Update Draft')) {
                                $(`#section-${sectioncode} .item-meta`).html(`
                                    <button type="button" class="btn button_save lang" onclick="saveFileSection('${sectioncode}', 'Y')">Save Draft</button>
                                `);
                            }
                        }
                    }
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                showAlert('Error removing file: ' + error, 'error');
            }
        });
    }

    function refreshFilePreview(sectioncode, fileData) {
        const previewContainer = $(`#file-preview-${sectioncode}`);
        const fileUploadArea = $(`#file-upload-${sectioncode}`);

        if (!previewContainer.length) return;

        const sectionData = sectionStatus[sectioncode] || {};
        const isFinalMode = (sectionData.status === 'F' || sectionData.status === 'A');

        if (fileData && fileData.fileuploadid) {
            // Get existing files to check if we're adding to existing array
            const uploadidInput = $(`#uploadid-${sectioncode}`);
            let existingFileIds = [];

            try {
                const currentValue = uploadidInput.val();
                if (currentValue && currentValue !== '[]') {
                    existingFileIds = JSON.parse(currentValue);
                }
            } catch (e) {
                existingFileIds = [];
            }

            // If this is a new file (not already in the array), we need to refresh all files
            if (!existingFileIds.includes(fileData.fileuploadid)) {
                existingFileIds.push(fileData.fileuploadid);
                uploadidInput.val(JSON.stringify(existingFileIds));
                refreshFilesAfterRemoval(sectioncode, existingFileIds);
                return;
            }

            let filePath = fileData.filepath || '';
            filePath = filePath.replace(/\\/g, '/');

            let fileIcon = 'fa-file-pdf';
            let iconColor = 'text-danger';

            if (fileData.mimetype) {
                if (fileData.mimetype.includes('image')) {
                    fileIcon = 'fa-file-image';
                    iconColor = 'text-primary';
                } else if (fileData.mimetype.includes('word')) {
                    fileIcon = 'fa-file-word';
                    iconColor = 'text-primary';
                } else if (fileData.mimetype.includes('excel')) {
                    fileIcon = 'fa-file-excel';
                    iconColor = 'text-success';
                }
            }

            // Build file item HTML - don't show delete button if finalized
            const deleteButtonHtml = !isFinalMode ? `
            <div class="file-actions ms-3">
                <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="event.stopPropagation(); removeSectionFile('${sectioncode}', '${fileData.fileuploadid}')"
                        title="Remove File" style="border-radius: 6px; padding: 6px 10px; white-space: nowrap;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        ` : '';

            previewContainer.html(`
        <div class="file-item d-flex align-items-center p-3 mb-2" style="border: 1px solid #dee2e6; border-radius: 8px; background: #fff; transition: all 0.3s ease; ${isFinalMode ? 'pointer-events: none; cursor: default;' : ''}">
            <div class="file-serial me-3" style="min-width: 30px; font-weight: 600; color: #6c757d;">
                1.
            </div>
            <div class="file-icon-wrapper me-3" style="min-width: 40px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                <i class="fas ${fileIcon} ${iconColor} fs-5"></i>
            </div>
            <div class="file-details flex-grow-1" style="cursor: pointer; min-width: 0; ${isFinalMode ? 'pointer-events: auto;' : ''}" onclick="window.open('/${filePath}', '_blank')">
                <div class="d-flex flex-column">
                    <span class="d-flex align-items-center gap-2 mt-1" style="font-size: 14px; max-width: 300px;" title="${escapeHtml(fileData.filename)}">
                        ${escapeHtml(fileData.filename)}
                    </span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <small class="text-muted" style="font-size: 12px;">${formatFileSize(fileData.filesize)}</small>
                    </div>
                </div>
            </div>
            ${deleteButtonHtml}
        </div>
        `);

            // Remove upload buttons when file exists
            if (fileUploadArea.length) {
                fileUploadArea.find('.upload-buttons').remove();
                fileUploadArea.find('small.text-muted').remove();
            }

        } else {
            // No file - show empty state
            previewContainer.html('<div class="no-files text-center text-muted py-4">No files uploaded yet</div>');

            // Show upload buttons if not in final mode
            if (!isFinalMode && fileUploadArea.length) {
                // Remove any existing upload buttons first to avoid duplicates
                fileUploadArea.find('.upload-buttons').remove();
                fileUploadArea.find('small.text-muted').remove();

                // Add the upload button area back
                const uploadHtml = `
                <div class="upload-buttons mt-3">
                    <input type="file" id="file-input-${sectioncode}" style="display: none;" accept="application/pdf">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('file-input-${sectioncode}').click()">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Allowed file types: PDF, max size: 2MB</small>
            `;

                fileUploadArea.append(uploadHtml);

                // Re-attach file input change handler
                setTimeout(() => {
                    $(`#file-input-${sectioncode}`).off('change').on('change', function(e) {
                        if (this.files.length > 0) {
                            uploadFile(sectioncode, this.files);
                        }
                        $(this).val('');
                    });
                }, 100);

                // Update the hidden input to empty array
                $(`#uploadid-${sectioncode}`).val('[]');
            }
        }
    }

    function formatFileSize(bytes) {
        if (!bytes) return '';
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        if (bytes === 0) return '0 Byte';
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }

    function confirmAndSave(sectioncode, statusflag) {
        if (typeof window.passing_alert_value === 'function') {
            window.passing_alert_value(
                'Confirmation',
                `Do you want to finalize this section? This action cannot be undone.`,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );
            $(document).off('click', '#process_button').on('click', '#process_button', function() {
                $('#confirmation_alert').modal('hide');
                saveSection(sectioncode, statusflag);
            });
        } else {
            if (confirm('Do you want to finalize this section? This action cannot be undone.')) {
                saveSection(sectioncode, statusflag);
            }
        }
    }

    function showSection(sectioncode) {
        $('.section-item').hide();
        $(`#section-${sectioncode}`).show();
        $('.step-buttons .btn').removeClass('active-step');
        $(`.step-buttons .btn[data-sectioncode="${sectioncode}"]`).addClass('active-step');
        currentActiveSection = sectioncode;
    }

    function saveSection(sectioncode, statusflag) {
        showLoading();

        const editorId = `editor_${sectioncode}`;
        const editor = ckEditorInstances[editorId];

        if (!editor) {
            hideLoading();
            showAlert('Editor not initialized', 'error');
            return;
        }

        const content = editor.getData();

        if (!validateContentLength(content, sectioncode)) {
            hideLoading();
            return;
        }

        if (!content.trim() && statusflag === 'F') {
            hideLoading();
            showAlert('Please add content before finalizing the section.', 'warning');
            return;
        }

        const praudittitleid = '{{ $praudittitleid }}';
        const deptcode = '{{ $deptcode }}';
        const catcode = '{{ $catcode }}';
        const subcatid = '{{ $subcatid ?? '' }}';

        $.ajax({
            url: "{{ route('performance.saveSectionContent') }}",
            type: "POST",
            data: {
                praudittitleid: praudittitleid,
                deptcode: deptcode,
                catcode: catcode,
                subcatid: subcatid,
                sectioncode: sectioncode,
                content: content,
                statusflag: statusflag,
                financialyear: '{{ $financialyear }}',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    const newStatusFlag = response.status;

                    if (!sectionStatus[sectioncode]) {
                        sectionStatus[sectioncode] = {};
                    }
                    sectionStatus[sectioncode].status = newStatusFlag;
                    sectionStatus[sectioncode].has_data = true;

                    let statusDisplay = '';
                    let badgeClass = '';

                    if (newStatusFlag === 'F') {
                        statusDisplay = 'Finalized';
                        badgeClass = 'bg-success';
                    } else if (newStatusFlag === 'Y') {
                        statusDisplay = 'Draft';
                        badgeClass = 'bg-warning';
                    }

                    $(`#status-${sectioncode}`).text(statusDisplay);
                    $(`#status-${sectioncode}`).removeClass('bg-danger bg-warning bg-success bg-info')
                        .addClass(badgeClass);

                    updateNavigationButton(sectioncode, newStatusFlag);
                    updateFinalizeAllButton();

                    const message = newStatusFlag === 'F' ? 'Section finalized successfully!' :
                        'Section saved as draft successfully!';
                    showAlert(message, 'success');

                    if (newStatusFlag === 'F' || newStatusFlag === 'A') {
                        if (ckEditorInstances[editorId]) {
                            ckEditorInstances[editorId].enableReadOnlyMode(`readOnly_${editorId}`);
                        }
                        $(`#section-${sectioncode} .item-meta`).html(
                            `<button type="button" class="btn btn-success" disabled>Finalized</button>`);
                    } else if (newStatusFlag === 'Y') {
                        $(`#section-${sectioncode} .item-meta`).html(`
                            <button type="button" class="btn button_save lang" onclick="saveSection('${sectioncode}', 'Y')">Update Draft</button>
                            <button type="button" class="btn btn-success" onclick="confirmAndSave('${sectioncode}', 'F')">Finalize</button>
                        `);
                    }
                } else {
                    showAlert('Failed to save section: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                let errorMsg = 'Error saving: ' + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert(errorMsg, 'error');
            }
        });
    }

    function finalfinalization() {
        const finalizeBtn = $('#finalizeAllBtn');
        if (finalizeBtn.prop('disabled')) {
            showAlert('All sections are already finalized!', 'info');
            return;
        }

        // Check if all sections are already in finalized status (F or A)
        const allSectionsFinalized = allSections.every(section => {
            const sectionData = sectionStatus[section.sectioncode] || {};
            return sectionData.status === 'F' || sectionData.status === 'A';
        });

        // If all sections are finalized, proceed with overall finalization
        if (allSectionsFinalized) {
            if (typeof window.passing_alert_value === 'function') {
                window.passing_alert_value(
                    'Confirmation',
                    'Do you want to finalize this report? This action cannot be undone.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );
                $(document).off('click.processButtonFinalize').on('click.processButtonFinalize', '#process_button',
                    function() {
                        $('#confirmation_alert').modal('hide');
                        $(document).off('click.processButtonFinalize');
                        processtoreport(allSections);
                    });
            } else {
                if (confirm('Do you want to finalize this report? This action cannot be undone.')) {
                    processtoreport(allSections);
                }
            }
            return;
        }

        // Check for sections that need attention
        const emptyFileSections = [];
        const emptySections = [];
        const invalidSections = [];

        allSections.forEach(section => {
            const sectionData = sectionStatus[section.sectioncode] || {};
            const status = sectionData.status;

            // Skip already finalized sections
            if (status === 'F' || status === 'A') {
                return;
            }

            if (section.type === 'F') {
                // Check file sections
                const hasFile = sectionData.has_data || false;
                if (!hasFile) {
                    emptyFileSections.push({
                        title: getLanguage() === 'ta' ? section.titletname : section.titleename
                    });
                }
            } else {
                // Check editor sections
                const editorId = `editor_${section.sectioncode}`;
                const editor = ckEditorInstances[editorId];
                if (editor) {
                    const content = editor.getData();
                    const textOnly = content.replace(/<[^>]*>/g, '').trim();
                    if (textOnly.length === 0) {
                        emptySections.push({
                            title: getLanguage() === 'ta' ? section.titletname : section.titleename
                        });
                    } else if (textOnly.length < 10) {
                        invalidSections.push({
                            title: getLanguage() === 'ta' ? section.titletname : section.titleename,
                            length: textOnly.length
                        });
                    }
                }
            }
        });

        // Check for file sections without files
        if (emptyFileSections.length > 0) {
            const sectionNames = emptyFileSections.map(s => `• ${s.title}`).join('<br>');
            showAlert(
                `Cannot proceed with finalization. The following sections require file uploads:<br><br>${sectionNames}<br><br>Please upload files to these sections first.`,
                'warning');
            return;
        }

        // Check for sections with empty content
        if (emptySections.length > 0) {
            const sectionNames = emptySections.map(s => `• ${s.title}`).join('<br>');
            showAlert(
                `Cannot proceed with finalization. The following sections have no content:<br><br>${sectionNames}<br><br>Please add content to these sections and save as draft first.`,
                'warning');
            return;
        }

        // Check for sections with insufficient content
        if (invalidSections.length > 0) {
            const sectionNames = invalidSections.map(s => `• ${s.title} (${s.length}/10 characters)`).join('<br>');
            showAlert(
                `Cannot proceed with finalization. The following sections require at least 10 characters:<br><br>${sectionNames}<br><br>Please add more content to these sections and save as draft first.`,
                'warning');
            return;
        }

        // All checks passed but some sections are in draft status
        const draftSections = allSections.filter(section => {
            const sectionData = sectionStatus[section.sectioncode] || {};
            return sectionData.status === 'Y';
        });

        if (draftSections.length > 0) {
            const sectionNames = draftSections.map(section => {
                return `• ${getLanguage() === 'ta' ? section.titletname : section.titleename}`;
            }).join('<br>');

            showAlert(
                `The following sections are in Draft status and need to be finalized first:<br><br>${sectionNames}<br><br>Please finalize each section individually using the "Finalize" button.`,
                'warning');
            return;
        }
    }

    function updateNavigationButton(sectioncode, statusFlag) {
        const button = $(`.step-buttons .btn[data-sectioncode="${sectioncode}"]`);
        button.removeClass('btn-outline-primary btn-warning btn-info btn-success');
        if (statusFlag === 'F') {
            button.addClass('btn-success');
        } else if (statusFlag === 'Y') {
            button.addClass('btn-warning');
        } else if (statusFlag === 'A') {
            button.addClass('btn-info');
        } else {
            button.addClass('btn-outline-primary');
        }
        button.data('statusflag', statusFlag);
        if (currentActiveSection === sectioncode) {
            button.addClass('active-step');
        }
    }

    function processtoreport(sectionsToProcess) {
        if (!sectionsToProcess || sectionsToProcess.length === 0) {
            hideLoading();
            showAlert('No sections to finalize.', 'info');
            return;
        }

        showLoading();
        let completed = 0;
        let errors = [];
        let totalToProcess = sectionsToProcess.length;

        sectionsToProcess.forEach(section => {
            if (section.type === 'F') {
                // For file sections, just update status
                $.ajax({
                    url: "{{ route('performance.saveSectionContent') }}",
                    type: "POST",
                    data: {
                        praudittitleid: '{{ $praudittitleid }}',
                        deptcode: '{{ $deptcode }}',
                        catcode: '{{ $catcode }}',
                        subcatid: '{{ $subcatid ?? '' }}',
                        sectioncode: section.sectioncode,
                        content: null,
                        statusflag: 'A',
                        financialyear: '{{ $financialyear }}',
                        _token: '{{ csrf_token() }}'
                    },
                    async: true,
                    success: function(response) {
                        completed++;
                        if (response.success) {
                            updateSectionAfterFinalize(section.sectioncode, response.status, null);
                            updateNavigationButton(section.sectioncode, response.status);
                            updateFinalizeAllButton();
                        } else {
                            errors.push(section.sectioncode);
                        }
                        if (completed === totalToProcess) {
                            if (errors.length === 0) {
                                showAlert('All sections have been finalized successfully!',
                                    'success');
                            } else {
                                showAlert(
                                    `Finalized ${completed - errors.length} sections. Failed: ${errors.join(', ')}`,
                                    'warning');
                            }
                            hideLoading();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(`Error finalizing section ${section.sectioncode}:`, error);
                        errors.push(section.sectioncode);
                        completed++;
                        if (completed === totalToProcess) {
                            showAlert(
                                `Finalized ${completed - errors.length} sections. Failed: ${errors.join(', ')}`,
                                'warning');
                            hideLoading();
                        }
                    }
                });
            } else {
                const editorId = `editor_${section.sectioncode}`;
                const editor = ckEditorInstances[editorId];
                if (editor) {
                    const content = editor.getData();
                    $.ajax({
                        url: "{{ route('performance.saveSectionContent') }}",
                        type: "POST",
                        data: {
                            praudittitleid: '{{ $praudittitleid }}',
                            deptcode: '{{ $deptcode }}',
                            catcode: '{{ $catcode }}',
                            subcatid: '{{ $subcatid ?? '' }}',
                            sectioncode: section.sectioncode,
                            content: content,
                            statusflag: 'A',
                            financialyear: '{{ $financialyear }}',
                            _token: '{{ csrf_token() }}'
                        },
                        async: true,
                        success: function(response) {
                            completed++;
                            if (response.success) {
                                updateSectionAfterFinalize(section.sectioncode, response.status,
                                    editorId);
                                updateNavigationButton(section.sectioncode, response.status);
                                updateFinalizeAllButton();
                            } else {
                                errors.push(section.sectioncode);
                            }
                            if (completed === totalToProcess) {
                                if (errors.length === 0) {
                                    showAlert('All sections have been finalized successfully!',
                                        'success');
                                } else {
                                    showAlert(
                                        `Finalized ${completed - errors.length} sections. Failed: ${errors.join(', ')}`,
                                        'warning');
                                }
                                hideLoading();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error finalizing section ${section.sectioncode}:`,
                                error);
                            errors.push(section.sectioncode);
                            completed++;
                            if (completed === totalToProcess) {
                                showAlert(
                                    `Finalized ${completed - errors.length} sections. Failed: ${errors.join(', ')}`,
                                    'warning');
                                hideLoading();
                            }
                        }
                    });
                } else {
                    console.warn(`Editor not found for section ${section.sectioncode}`);
                    errors.push(section.sectioncode);
                    completed++;
                    if (completed === totalToProcess) {
                        hideLoading();
                        showAlert(
                            `Finalized ${completed - errors.length} sections. Failed: ${errors.join(', ')}`,
                            'warning');
                    }
                }
            }
        });
    }

    function updateSectionAfterFinalize(sectioncode, statusFlag, editorId) {
        $(`#status-${sectioncode}`).text('Finalized');
        $(`#status-${sectioncode}`).removeClass('bg-danger bg-warning bg-info').addClass('bg-success');

        if (!sectionStatus[sectioncode]) {
            sectionStatus[sectioncode] = {};
        }
        sectionStatus[sectioncode].status = statusFlag;
        sectionStatus[sectioncode].has_data = true;

        if (editorId && ckEditorInstances[editorId]) {
            ckEditorInstances[editorId].enableReadOnlyMode(`readOnly_${editorId}`);
        }

        $(`#section-${sectioncode} .item-meta`).html(
            `<button type="button" class="btn btn-success" disabled>Finalized</button>`);

        // Remove upload buttons
        $(`#file-upload-${sectioncode} .upload-buttons`).remove();
        $(`#file-upload-${sectioncode} small.text-muted`).remove();

        // Remove delete/trash buttons from file items
        $(`#file-preview-${sectioncode} .file-actions`).remove();

        // Make file items non-interactive
        $(`#file-preview-${sectioncode} .file-item`).css({
            'cursor': 'default',
            'pointer-events': 'none'
        });

        // Re-enable pointer events for the file details (to allow viewing)
        $(`#file-preview-${sectioncode} .file-details`).css('pointer-events', 'auto');
    }

    function initializeCKEditor(editorId, content, readOnly = false) {
        const editorElement = document.getElementById(editorId);
        if (!editorElement) return;

        if (ckEditorInstances[editorId] && typeof ckEditorInstances[editorId].destroy === 'function') {
            ckEditorInstances[editorId].destroy();
        }

        const container = editorElement.closest('.ckeditor-container');
        const loadingOverlay = $(`
            <div class="ckeditor-loading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 4px;">
                <div style="text-align: center;">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">Loading editor...</p>
                </div>
            </div>
        `);

        if (container) {
            $(container).css('position', 'relative');
            $(container).append(loadingOverlay);
        }

        $(editorElement).hide();

        if (content) {
            editorElement.value = content;
        }

        CKEDITOR.ClassicEditor.create(editorElement, {
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
                    'insertTable', '|',
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
                'MultiLevelList', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        }).then(editor => {
            ckEditorInstances[editorId] = editor;
            setTimeout(() => {
                if (content) {
                    editor.setData(content);
                }
                if (container) {
                    $(container).find('.ckeditor-loading').fadeOut(300, function() {
                        $(this).remove();
                    });
                }
                const editorElementView = editor.ui.view.element;
                if (editorElementView) {
                    $(editorElementView).fadeIn(300);
                }
            }, 100);
            if (readOnly) {
                editor.enableReadOnlyMode(`readOnly_${editorId}`);
            }
        }).catch(error => {
            console.error("CKEditor Initialization Error:", error);
            if (container) {
                $(container).find('.ckeditor-loading').remove();
            }
            $(editorElement).show();
            editorElement.style.border = '1px solid #dc3545';
            editorElement.style.padding = '10px';
            editorElement.value = 'Error loading editor. Raw content:\n\n' + content;
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getLanguage() {
        return localStorage.getItem('lang') || 'en';
    }

    $(window).on('beforeunload', function() {
        Object.values(ckEditorInstances).forEach(editor => {
            if (editor && typeof editor.destroy === 'function') {
                editor.destroy();
            }
        });
    });
</script>
@endsection

