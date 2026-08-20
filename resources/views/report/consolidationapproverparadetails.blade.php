@extends('index2')
@include('common.alert')

@php
$sessionchargedel = session('charge');

$instid = $data['instid'] ?? '';
$auditplanid = $data['auditplanid'] ?? '';
$instename = $data['instename'] ?? '';
$deptcode = $data['deptcode'] ?? optional($sessionchargedel)->deptcode ?? '';
$deptname = $data['deptname'] ?? '';
$catcode = $data['catcode'] ?? optional($sessionchargedel)->catcode ?? '';
$catname = $data['catname'] ?? '';
$subcatid = $data['subcatid'] ?? '';
$subcatname = $data['subcatname'] ?? '';
$regioncode = $data['regioncode'] ?? optional($sessionchargedel)->regioncode ?? '';
$regionname = $data['regionname'] ?? '';
$distcode = $data['distcode'] ?? optional($sessionchargedel)->distcode ?? '';
$distname = $data['distname'] ?? '';
$auditperiodid = $data['auditperiodid'] ?? '';
$audityear = $data['audityear'] ?? '';

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_district_disable = $distcode ? 'disabled' : '';
@endphp

@section('title', 'Approver Consolidation - ' . $instename)
@section('content')

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

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

    .card {
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        border: none;
        overflow: hidden;
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        padding: 12px 24px;
        font-weight: 500;
        color: var(--dark);
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid lightskyblue;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(0, 74, 173, 0.1);
        color: var(--primary);
    }

    .nav-pills .nav-link.active {
        color: var(--white);
        box-shadow: 0 4px 12px rgba(0, 74, 173, 0.3);
        transform: translateY(-1px);
    }

    .nav-pills .nav-link.active:hover {
        background: var(--primary-light);
        color: var(--white);
        cursor: default;
    }

    .nav-pills .badge {
        font-size: 0.7em;
        padding: 4px 8px;
        border-radius: 10px;
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

    #tableshow,
    #tableshow2 {
        overflow: hidden;
    }

    .loader {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    #selectedParasTable td.text-center,
    #unselectedParasTable td.text-center {
        vertical-align: middle;
    }

    #selectedParasTable .form-check,
    #unselectedParasTable .form-check {
        min-height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    #selectedParasTable .form-check-input,
    #unselectedParasTable .form-check-input {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }

    #selectedParasTable .form-check-input:disabled,
    #unselectedParasTable .form-check-input:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    #selectedParasTable tbody td,
    #unselectedParasTable tbody td {
        vertical-align: middle !important;
    }

    #selectedParasTable .row-checkbox:enabled,
    #unselectedParasTable .row-checkbox:enabled {
        cursor: pointer;
        border-color: #009951;
        background-color: #ffffff;
    }

    #selectedParasTable .row-checkbox:enabled:hover,
    #unselectedParasTable .row-checkbox:enabled:hover {
        box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .25);
    }

    #selectedParasTable .row-checkbox:enabled:checked,
    #unselectedParasTable .row-checkbox:enabled:checked {
        background-color: #0067ff;
        border-color: #00ff63;
    }

    #selectedParasTable .row-checkbox:disabled,
    #unselectedParasTable .row-checkbox:disabled {
        opacity: .45;
        cursor: not-allowed;
        background-color: #6a6b6c;
        border-color: #000000;
    }

    #viewedParaCheckbox {
        border-color: #0042ff;
    }

    .filter-panel {
        /* background-color: #f8f9fa; */
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

    #notificationNav {
        display: flex;
        gap: 20px;
    }

    #notificationNav .nav-link {
        width: 220px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 14px;
        border-radius: 10px;
    }

    #notificationNav .badge {
        font-size: 12px;
        padding: 4px 8px;
    }

    .table-responsive {
        overflow: unset;
    }

    #remarksModal .modal-body {
        min-height: 60vh;
        max-height: 70vh;
        overflow-y: auto;
    }

    .consolidation-modal .fw-bolder {
        color: #000000;
    }

    .form-check-input:checked {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
    }
</style>

<div class="loading-overlay">
    <div class="cube"></div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card_border">

            <div class="card-header card_header_color lang" key="">{{ $instename }} - Para Details</div>

            <div class="card-body">
                <a href="{{ route('approver.institution.list') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-arrow-left me-1"></i> Back to Institutions
                </a>
                <div class="filter-panel">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Department</label>
                            <div class="auto-field" id="display_deptname">{{ $deptname ?: '-' }}</div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Region</label>
                            <div class="auto-field" id="display_regionname">{{ $regionname ?: '-' }}</div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">District</label>
                            <div class="auto-field" id="display_districtname">{{ $distname ?: '-' }}</div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Category</label>
                            <div class="auto-field" id="display_catname">{{ $catname ?: '-' }}</div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">SubCategory</label>
                            <div class="auto-field" id="display_subcatname">{{ $subcatname ?: '-' }}</div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Audit Year</label>
                            <div class="auto-field" id="display_audityear">{{ $audityear ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row hide_this" id="paraOptionsSection">
                    <div class="col-md-12">
                        <div class="">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-3">Select Para Type to View</h5>
                                <div class="d-flex justify-content-center gap-3">
                                    <ul class="nav nav-pills justify-content-center" id="notificationNav"
                                        role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="viewSelectedBtn" data-bs-toggle="pill"
                                                data-bs-target="#inbox-content" type="button" role="tab"
                                                aria-controls="inbox-content" aria-selected="false" disabled>
                                                <i class="ri-inbox-line me-1"></i> Selected Paras
                                                <span id="selectedCountBadge" class="badge bg-success ms-1">0</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="viewUnselectedBtn" data-bs-toggle="pill"
                                                data-bs-target="#sent-content" type="button" role="tab"
                                                aria-controls="sent-content" aria-selected="false" disabled>
                                                <i class="ri-send-plane-line me-1"></i> Unselected Paras
                                                <span id="unselectedCountBadge" class="badge bg-warning ms-1">0</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Paras Table -->
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="selectedParasTable"
                            class="table table-striped table-bordered w-100 datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center">S. No</th>
                                    <th class="text-center">Para Number</th>
                                    <th class="text-center">Irregularities</th>
                                    <th class="text-center">Title / Heading</th>
                                    <th class="text-center">Gist of Observation</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Select Para</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-primary" id="buttonaction">Save Draft</button>
                            <button type="button" class="btn btn-success hide_this"
                                id="finalizeButton">Finalize</button>
                            {{-- <button type="button" class="btn btn-danger" onclick="goBack()">Back</button> --}}
                        </div>
                    </div>

                    <!-- Unselected Paras Table -->
                    <div class="table-responsive hide_this" id="tableshow2">
                        <table id="unselectedParasTable"
                            class="table table-striped table-bordered w-100 datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center">S. No</th>
                                    <th class="text-center">Para Number</th>
                                    <th class="text-center">Irregularities</th>
                                    <th class="text-center">Title / Heading</th>
                                    <th class="text-center">Gist of Observation</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Remarks</th>
                                    <th class="text-center">Select Para</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-primary" id="buttonaction2">Save to select</button>
                            {{-- <button type="button" class="btn btn-danger" onclick="goBack()">Back</button> --}}
                        </div>
                    </div>

                    <div id='no_data' class='lang text-center'>
                        <p>Loading data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="slipDetailsModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content consolidation-modal">
            <div class="modal-header position-relative">
                <h5 class="modal-title fw-bold mx-auto" id="slipDetailsModalLabel">
                    Audit Slip Details
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <h4 id="modalInstitutionName" class="text-center mb-4 fw-bold" style="font-size: 1.4rem; color: #333;">
                {{ $instename }}
            </h4>
            <div class="col-md-12 text-end">
                <div class="alert-info mt-2 p-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Review the details and tick <strong>"Verified"</strong> to enable the
                    <strong>Submit</strong> button.
                </div>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <div style="width:80%;">
                        <table class="table table-bordered table-sm" id="slipDetailsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Field</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Remarks</h6>
                            <textarea id="remarksEditor" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="px-4 py-2 bg-white d-flex align-items-center gap-2">
                            <input class="form-check-input fs-5" type="checkbox" id="viewedParaCheckbox">
                            <label class="fw-bolder mb-0" for="viewedParaCheckbox">Verified</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center">
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-primary px-4" id="saveRemarksBtn" disabled>
                        Submit
                    </button>
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Finalize Modal -->
<div class="modal fade" id="acknowledgeModal" tabindex="-1" aria-labelledby="acknowledgeModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" style="max-width: 85%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#3782ce;" id="alert_modalheader">
                <h3 class="text-white lang" id="acknowledgeModalLabel">
                    Confirm Finalization
                </h3>
                <button type="button" id="close_button" class="btn-close bg-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="mb-3">
                        <i class="fas fa-table me-2"></i>
                        Selected Paras Details
                    </h6>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table id="selectedParasTable"
                            class="table table-striped table-bordered w-100 datatables-basic">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" width="5%">S.No</th>
                                    <th class="text-center" width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllFinalize"
                                                disabled>
                                        </div>
                                    </th>
                                    <th class="text-center" width="6%">Para Number</th>
                                    <th class="text-center"> Institution Name </th>
                                    <th class="text-center">Irregularities</th>
                                    <th class="text-center">Title / Heading</th>
                                    <th class="text-center">Gist of Observation</th>
                                    <th class="text-center" width="10%">Amount</th>
                                    <th class="text-center" width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="selectedParasTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card border-primary">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-file-signature me-2 text-primary"></i>
                            Finalization Acknowledgment
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Important:</strong> Before Finalizing, please acknowledge the following:
                        </div>
                        <div class="acknowledge-content border rounded p-3 mb-3"
                            style="max-height: 200px; overflow-y: auto;">
                            <ol class="mb-0">
                                <li>All selected paras will be marked as approved</li>
                                <li>The report will be finalized for further processing</li>
                                <li>You cannot modify paras after finalization</li>
                                <li>This action is irreversible</li>
                            </ol>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acknowledgeCheckbox">
                            <label class="form-check-label fw-semibold" for="acknowledgeCheckbox">
                                I have verified the details of <span id="selectedCountLabel">0</span> selected paras
                                and approve them for finalization.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success lang" id="confirmFinalizeBtn" disabled>
                    Finalize
                </button>
                <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="institutionVerificationModal" tabindex="-1"
    aria-labelledby="institutionVerificationModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <!-- Keep existing institutionVerificationModal content -->
    <div class="modal-dialog modal-xl" style="max-width: 85%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#3782ce;" id="alert_modalheader">
                <h3 class="text-white lang" id="institutionVerificationModalLabel">
                    Finalization
                </h3>
                <button type="button" id="close_button" class="btn-close bg-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <strong>
                    The following institutions have unverified Serious Paras or Money Value Paras, or no paras selected.
                    Please verify all paras before finalizing.
                </strong>
                <br>
                <div id="institutionsWithUnverifiedParasSection">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto; position: relative;">
                        <table class="table table-striped table-bordered w-100 datatables-basic"
                            id="institutionsWithUnverifiedParasTable" style="width:100%">
                            <thead class="table-light"
                                style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                                <tr>
                                    <th>S.no</th>
                                    <th>Institution Name</th>
                                    <th>Quarter</th>
                                    <th>Audit Year</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Region</th>
                                    <th>District</th>
                                    <th>Total Selected Paras</th>
                                    <th>Serious Unverified</th>
                                    <th>Money Value Unverified</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table rows will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Password Confirmation Modal -->
<div class="modal fade" id="passwordConfirmModal" tabindex="-1" aria-labelledby="passwordConfirmModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#3782ce;">
                <h5 class="modal-title text-white" id="passwordConfirmModalLabel">
                    Confirm Your Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <p class="mb-1">Please enter your password</p>
                    <p class="text-muted small">to confirm finalization of the consolidation report.</p>
                </div>

                <div class="mb-3">
                    <label for="passwordInput" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password"
                            autocomplete="off" readonly onfocus="this.removeAttribute('readonly')"
                            onpaste="return false;" oncopy="return false;" oncut="return false;">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordError" class="alert alert-danger mt-2" style="display: none; padding: 0.5rem;">
                    </div>
                </div>

                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action is irreversible. All selected paras will be finalized.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmPasswordBtn" disabled>
                    Confirm & Finalize
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    let selectedTableDT = null;
    let unselectedTableDT = null;
    let institutionsDataTable = null;

    let allInstitutionsData = {};
    let selectedParas = new Map();
    let institutionTables = {};
    let verificationDataTable = null;

    $('#togglePassword').on('click', function() {
        const passwordInput = $('#passwordInput');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    window.pendingFinalizeData = null;
    window.selectedRowIds = new Set();
    window.isReportFinalized = false;
    window.hasSavedData = false;
    window.isForwarded = false;
    window.currentInstitutionId = '{{ $instid }}';
    window.enabledRows = new Set();
    window.paraCounts = {
        selected: 0,
        unselected: 0
    };
    window.viewingMode = 'selected';

    const institutionId = '{{ $instid }}';
    const institutionName = '{{ $instename }}';
    const auditplanid = '{{ $auditplanid }}';

    // Cache for performance
    let selectedParasCache = new Map();
    let unselectedParasCache = new Map();
    let cacheTimestamp = new Map();
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    $(document).ready(function() {
        loadAllInstitutions();
    });

    function loadAllInstitutions() {
        $('#institutionsTable tbody').html(`
            <tr>
                <td colspan="14" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading institutions...</p>
                </td>
            </tr>
        `);

        showLoading();
        $.ajax({
            url: '/check-all-selected-institutions-paras',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.data && response.data.institutions) {
                    window.allFilteredInstitutionIds = response.data.institutions.map(inst => inst.instid);

                    window.allInstitutionsData = response.data;
                }
                checkReportStatus(institutionId)
            },
            error: function(xhr, status, error) {
                console.error('Error loading institutions:', error);
                $('#institutionsTable tbody').html(`
                    <tr>
                        <td colspan="14" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading institutions. Please refresh the page.
                        </td>
                    </tr>
                `);
            }
        });
    }

    function checkReportStatus(institutionId) {
        showLoading();

        if (!institutionId) return;

        if (institutionId !== window.currentInstitutionId) {
            window.selectedRowIds.clear();
            updateSelectedCount();
        }

        window.currentInstitutionId = institutionId;
        window.hasCheckedStatus = true;

        showParaOptionsSection(institutionId);

        if (window.allFilteredInstitutionIds && window.allFilteredInstitutionIds.length > 0) {
            checkGlobalFinalizationStatus(window.allFilteredInstitutionIds)
                .then(globalResult => {
                    if (globalResult.any_finalized) {
                        window.isGlobalFinalized = true;
                        window.finalizedInstitutions = globalResult.finalized_institutions || [];
                        $.ajax({
                            url: '/check-finalized-para-status',
                            type: 'POST',
                            data: {
                                instid: institutionId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.isReportFinalized = response.isFinalized;
                                    window.hasSavedData = response.hasSavedData;

                                    if (window.isGlobalFinalized) {
                                        window.isReportFinalized = true;
                                    }
                                    if (response.selectedCount !== undefined) {
                                        window.paraCounts.selected = response.selectedCount;
                                    }
                                    if (response.unselectedCount !== undefined) {
                                        window.paraCounts.unselected = response.unselectedCount;
                                    }

                                    updateButtonCounts();
                                    updateUIForStatus();
                                    hideLoading();

                                    if (window.paraCounts.selected > 0) {
                                        const selectedBtn = document.getElementById('viewSelectedBtn');
                                        if (selectedBtn && !selectedBtn.disabled) {
                                            const tab = new bootstrap.Tab(selectedBtn);
                                            tab.show();
                                        }
                                    }
                                    if (window.paraCounts.selected > 0) {
                                        getSelectedParas(institutionId);
                                    }
                                }
                            },
                            error: function() {
                                console.error('Error checking report status');
                                getSelectedParas(institutionId);
                                updateButtonCounts();
                                hideLoading();
                            }
                        });
                    } else {
                        window.isGlobalFinalized = false;
                        checkSpecificInstitutionStatus(institutionId);
                        hideLoading();
                    }
                })
                .catch(error => {
                    console.error('Error checking global finalization:', error);
                    checkSpecificInstitutionStatus(institutionId);
                    hideLoading();
                });
        } else {
            checkSpecificInstitutionStatus(institutionId);
        }
    }

    function checkSpecificInstitutionStatus(institutionId) {
        showLoading();
        $.ajax({
            url: '/check-finalized-para-status',
            type: 'POST',
            data: {
                instid: institutionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    window.isReportFinalized = response.isFinalized;
                    window.hasSavedData = response.hasSavedData;

                    if (response.selectedCount !== undefined) {
                        window.paraCounts.selected = response.selectedCount;
                    }
                    if (response.unselectedCount !== undefined) {
                        window.paraCounts.unselected = response.unselectedCount;
                    }

                    updateButtonCounts();
                    updateUIForStatus();

                    if (window.paraCounts.selected > 0) {
                        getSelectedParas(institutionId);
                    }
                    hideLoading();
                } else {
                    getSelectedParas(institutionId);
                    hideLoading();
                }
            },
            error: function() {
                hideLoading();
                console.error('Error checking report status');
                getSelectedParas(institutionId);
                hideLoading();
            }
        });
    }

    function checkGlobalFinalizationStatus(institutionIds) {
        showLoading();

        return new Promise((resolve, reject) => {
            if (!institutionIds || institutionIds.length === 0) {
                resolve({
                    any_finalized: false,
                    finalized_institutions: [],
                    total_institutions: 0
                });
                return;
            }
            $.ajax({
                url: '/check-global-finalization-status',
                type: 'POST',
                data: {
                    institution_ids: institutionIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(response.message || 'Error checking finalization status');
                    }
                    hideLoading();
                },
                error: function(xhr, status, error) {
                    reject('Network error: ' + error);
                    hideLoading();
                }
            });
        });
    }

    function showParaOptionsSection(institutionId) {
        window.currentInstitutionForOptions = institutionId;

        $('#tableshow').addClass('hide_this');
        $('#tableshow2').addClass('hide_this');
        $('#no_data').addClass('hide_this');

        $('#paraOptionsSection').removeClass('hide_this');

        updateAllParaCounts(institutionId);
    }

    function updateAllParaCounts(institutionId) {
        showLoading();
        if (!institutionId) return;

        $.ajax({
            url: '/get-all-consolidated-para-counts',
            type: 'POST',
            data: {
                instid: institutionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.paraCounts.selected = response.selectedCount || 0;
                    window.paraCounts.unselected = response.unselectedCount || 0;

                    updateButtonCounts();
                }
                hideLoading();
            },
            error: function() {
                console.error('Error fetching para counts');
                window.paraCounts.selected = 0;
                window.paraCounts.unselected = 0;
                updateButtonCounts();
                hideLoading();
            }
        });
    }

    function updateButtonCounts() {
        showLoading();
        const selectedBadge = $('#selectedCountBadge');
        const unselectedBadge = $('#unselectedCountBadge');
        const viewSelectedBtn = $('#viewSelectedBtn');
        const viewUnselectedBtn = $('#viewUnselectedBtn');

        selectedBadge.text(window.paraCounts.selected);
        unselectedBadge.text(window.paraCounts.unselected);

        if (window.paraCounts.selected === 0) {
            selectedBadge.removeClass('bg-success').addClass('bg-secondary');
            viewSelectedBtn.prop('disabled', true).css('cursor', 'not-allowed');
            viewSelectedBtn.removeClass('active');

            if (window.paraCounts.unselected === 0) {
                unselectedBadge.removeClass('bg-warning').addClass('bg-danger');
                viewUnselectedBtn.prop('disabled', true).css('cursor', 'not-allowed');
                viewUnselectedBtn.removeClass('active');
            } else {
                unselectedBadge.removeClass('bg-danger').addClass('bg-warning');
                viewUnselectedBtn.prop('disabled', false).css('cursor', 'pointer');
            }
            hideLoading();
        } else {
            selectedBadge.removeClass('bg-secondary').addClass('bg-success');
            viewSelectedBtn.prop('disabled', false).css('cursor', 'pointer');

            if (window.paraCounts.unselected === 0) {
                unselectedBadge.removeClass('bg-warning').addClass('bg-danger');
                viewUnselectedBtn.prop('disabled', true).css('cursor', 'not-allowed');
                viewUnselectedBtn.removeClass('active');
            } else {
                unselectedBadge.removeClass('bg-danger').addClass('bg-warning');
                viewUnselectedBtn.prop('disabled', false).css('cursor', 'pointer');
            }
            hideLoading();

        }
    }

    $(document).on('click', '#viewSelectedBtn:not(:disabled)', function() {
        const institutionId = window.currentInstitutionForOptions;
        if (!institutionId) return;

        window.selectedRowIds.clear();
        window.enabledRows = new Set();
        window.viewingMode = 'selected';

        $('#viewSelectedBtn').addClass('active');
        $('#viewUnselectedBtn').removeClass('active');

        getSelectedParas(institutionId);
    });

    $(document).on('click', '#viewUnselectedBtn:not(:disabled)', function() {
        const institutionId = window.currentInstitutionForOptions;
        if (!institutionId) return;

        window.selectedRowIds.clear();
        window.enabledRows = new Set();
        window.viewingMode = 'unselected';

        $('#viewUnselectedBtn').addClass('active');
        $('#viewSelectedBtn').removeClass('active');

        getUnselectedParas(institutionId);
    });

    function getSelectedParas(institutionId) {
        if (!institutionId) return;

        if (selectedTableDT) {
            selectedTableDT.clear().destroy();
            selectedTableDT = null;
        }

        $('#tableshow').removeClass('hide_this');
        $('#tableshow2').addClass('hide_this');
        $('#no_data').addClass('hide_this');

        $('#selectedParasTable tbody').html(`
            <tr>
                <td colspan="8" class="text-center">
                    <p class="mt-2">Loading selected paras...</p>
                </td>
            </tr>
        `);

        window.selectedRowIds.clear();
        window.currentInstitutionId = institutionId;



        $.ajax({
            url: "/get-selected-consolidated-paras",
            type: "POST",
            data: {
                instid: institutionId,
		auditplanid: auditplanid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    selectedParasCache.set(institutionId, response.data);
                    cacheTimestamp.set(`selected_${institutionId}`, new Date().getTime());

                    window.paraCounts.selected = response.data.length;
                    updateButtonCounts();
                    renderSelectedParasTable(response.data, institutionId);

                    $('#viewSelectedBtn').addClass('active');
                    $('#viewUnselectedBtn').removeClass('active');
                hideLoading();

                } else {
                    selectedParasCache.set(institutionId, []);
                    cacheTimestamp.set(`selected_${institutionId}`, new Date().getTime());

                    window.paraCounts.selected = 0;
                    updateButtonCounts();
                    $('#tableshow').addClass('hide_this');
                    $('#no_data').removeClass('hide_this');
                    $('#no_data').html(`
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No selected paras found for this institution.
                        </div>
                    `);
                     hideLoading();
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error loading selected paras:', error);
                window.paraCounts.selected = 0;
                updateButtonCounts();
                $('#tableshow').addClass('hide_this');
                $('#no_data').removeClass('hide_this');
                $('#no_data').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading selected paras. Please try again.
                    </div>
                `);
            }
        });
    }

    function renderSelectedParasTable(selectedParas, institutionId) {
        let tbody = '';
        let sno = 1;

        if (selectedTableDT) {
            selectedTableDT.destroy();
        }

        window.selectedRowIds.clear();
        window.enabledRows = new Set();

        selectedParas.forEach(row => {
            let remarksCell = '-';
            if (row.remarks && row.remarks !== 'null') {
                remarksCell = `
                    <button type="button"
                        class="btn btn-sm ${row.approververifiedflag === 'Y' ? 'btn-success' : 'btn-primary'} remarks-btn"
                        data-auditslipid="${row.auditslipid}"
                        data-mainslipnumber="${row.mainslipnumber}"
                        data-table-type="selected">
                        ${row.approververifiedflag === 'Y' ?
                            '<i class="fas fa-check-circle me-1"></i> Verified' :
                            '<i class="fas fa-eye me-1"></i> View Remarks'}
                    </button>
                `;
            }

            const rowKey = `${row.mainslipnumber}`;
            const isChecked = window.selectedRowIds.has(rowKey);

            let disabledAttr = '';
            let cursorStyle = 'pointer';
            let titleText = 'Select this para';

            if (row.approververifiedflag === 'Y') {
                disabledAttr = '';
                cursorStyle = 'pointer';
                titleText = 'Select this para';
                window.enabledRows.add(row.mainslipnumber);
            } else if (row.approververifiedflag === 'N' || !row.approververifiedflag || row.approververifiedflag === '') {
                disabledAttr = 'disabled';
                cursorStyle = 'not-allowed';
            }

            function escapeHtmlAttribute(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            const checkbox = `
                <div class="form-check d-flex justify-content-center align-items-center" style="min-height: 40px;">
                    <input type="checkbox"
                        class="form-check-input row-checkbox"
                        title="${titleText}"
                        data-auditslipid="${row.auditslipid || ''}"
                        data-transactionno="${row.transactionno || ''}"
                        data-mainobjectionid="${row.mainobjectionid || ''}"
                        data-subobjectionid="${row.subobjectionid || ''}"
                        data-schteammemberid="${row.schteammemberid || ''}"
                        data-auditscheduleid="${row.auditscheduleid || ''}"
                        data-auditplanid="${row.auditplanid || ''}"
                        data-irregularitiescode="${row.irregularitiescode || ''}"
                        data-irregularitiescatcode="${row.irregularitiescatcode || ''}"
                        data-irregularitiessubcatcode="${row.irregularitiessubcatcode || ''}"
                        data-slipdetails="${escapeHtmlAttribute(row.slipdetails || '')}"
                        data-amountinvolved="${row.amountinvolved || null}"
                        data-liability="${(row.liability || '')}"
                        data-remarks="${escapeHtmlAttribute(row.remarks || '')}"
                        data-tempslipnumber="${row.tempslipnumber || ''}"
                        data-mainslipnumber="${row.mainslipnumber || ''}"
                        data-severitycode="${row.severitycode || ''}"
                        data-schemastatus="${row.schemastatus || ''}"
                        data-auditeeschemecode="${row.auditeeschemecode || ''}"
                        data-rejoinderstatus="${row.rejoinderstatus || ''}"
                        data-rejoindercycle="${row.rejoindercycle || null}"
                        data-createdby="${row.createdby || ''}"
                        data-forwardedto="${row.forwardedto || ''}"
                        data-processcode="${row.processcode || ''}"
                        data-paraorder="${row.paraorder || null}"
                        data-paraverifiedflag="${row.paraverifiedflag || 'N'}"
                        data-approververifiedflag="${row.approververifiedflag || 'N'}"
                        data-paraverifiedby="${row.paraverifiedby || ''}"
                        data-paraverifiedon="${row.paraverifiedon || ''}"
                        data-forwardedtousertypecode="${row.forwardedtousertypecode || ''}"
                        data-updatedby="${row.updatedby || ''}"
                        data-updatedbyusertypecode="${row.updatedbyusertypecode || ''}"
                        data-quartercode="${row.quartercode || ''}"
                        data-financialyear="${row.financialyear || ''}"
                        data-catcode="${row.catcode || ''}"
                        ${isChecked ? 'checked' : ''}
                        ${disabledAttr}
                        style="width: 20px; height: 20px; cursor: ${cursorStyle};">
                </div>
            `;

            tbody += `
                <tr>
                    <td class="text-center">${sno}</td>
                    <td class="text-center text-wrap">${row.paranumber || '-'}</td>
                    <td class="text-center text-wrap">${row.irregularitieselname || '-'}</td>
                    <td class="text-wrap">${row.objectionename || '-'}</td>
                    <td class="text-wrap">${row.slipdetails || '-'}</td>
                    <td class="text-wrap">${row.amountinvolved || '-'}</td>
                    <td class="text-center text-wrap">${remarksCell}</td>
                    <td class="text-center align-middle" style="vertical-align: middle;">${checkbox}</td>
                </tr>
            `;

            sno++;
        });

        $('#selectedParasTable tbody').html(tbody);

        if (!window.isReportFinalized || window.isReportFinalized ) {
            loadExistingConsolidatedData(institutionId);
        }


        selectedTableDT = $('#selectedParasTable').DataTable({
            responsive: true,
            destroy: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 mt-3'p>>",
            autoWidth: false,

            columnDefs: [{
                    orderable: false,
                    targets: [6],
                    className: 'text-center align-middle',
                    width: '120px'
                },
                {
                    orderable: false,
                    targets: [7],
                    className: 'text-center align-middle',
                    width: '80px'
                },
                {
                    className: 'text-center align-middle',
                    targets: [0],
                    width: '50px'
                },
                {
                    className: 'text-center text-wrap',
                    targets: [1],
                    width: '100px'
                },
                {
                    className: 'text-wrap',
                    targets: [2, 3, 4, 5],
                }
            ],

            initComplete: function() {
                const totalRecords = this.api().page.info().recordsTotal;

                const customHeader = `
                    <div class="row mb-3">
                        <div class="col-md-12 mt-2">
                            <div class="alert-success d-flex justify-content-between align-items-center flex-wrap">
                                <div class="mb-2 mb-md-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Selected / Forwarded Paras</strong>
                                    <span class="ms-2">(${totalRecords} paras)</span>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="alert-info mt-2 p-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> Click "View Remarks" button to verify each para
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#selectedParasTable').before(customHeader);
            },

            drawCallback: function(settings) {
                this.api().rows({ page: 'current' }).nodes().to$().find('.row-checkbox').each(function() {
                    const mainslipnumber = $(this).data('mainslipnumber');
                    const approververifiedflag = $(this).data('approververifiedflag');
                    const rowKey = `${mainslipnumber}`;

                    const wasEnabled = window.enabledRows && window.enabledRows.has(mainslipnumber);
                    const isSelected = window.selectedRowIds.has(rowKey);

                    if (wasEnabled || isSelected || approververifiedflag === 'Y') {
                        $(this).prop('disabled', false).css('cursor', 'pointer');
                    } else {
                        $(this).prop('disabled', true).css('cursor', 'not-allowed');
                    }
                });

                updateSelectedCount();
                updateSelectAllCheckbox();
                $('.remarks-btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const auditslipid = $(this).data('auditslipid');
                    const mainslipnumber = $(this).data('mainslipnumber');
                    const tableType = $(this).data('table-type') || 'selected';

                    fetchRemarksData(auditslipid, mainslipnumber, tableType);
                });

            }
        });

        $('#tableshow').removeClass('hide_this');
        $('#no_data').addClass('hide_this');

        updateButtonCounts();
        updateSelectedCount();
        updateUIForStatus();
    }

    function getUnselectedParas(institutionId) {
        showLoading();
        if (!institutionId) return;

        if (unselectedTableDT) {
            unselectedTableDT.clear().destroy();
            unselectedTableDT = null;
        }

        $('#tableshow').addClass('hide_this');
        $('#tableshow2').removeClass('hide_this');
        $('#no_data').addClass('hide_this');

        $('#unselectedParasTable tbody').html(`
            <tr>
                <td colspan="8" class="text-center">
                    <p class="mt-2">Loading data...</p>
                </td>
            </tr>
        `);

        window.viewingMode = 'unselected';
        window.currentInstitutionId = institutionId;

        $.ajax({
            url: "/get-unselected-consolidated-paras",
            type: "POST",
            data: {
                instid: institutionId,
		auditplanid: auditplanid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                unselectedParasCache.set(institutionId, response);
                cacheTimestamp.set(`unselected_${institutionId}`, new Date().getTime());
                renderUnselectedParasTable(response, institutionId);
                hideLoading();
            },
            error: function() {
                hideLoading();
                window.paraCounts.unselected = 0;
                updateButtonCounts();
                $('#tableshow2').addClass('hide_this');
                $('#no_data').removeClass('hide_this');
                $('#no_data').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading data. Please try again.
                    </div>
                `);
            }
        });
    }

    function renderUnselectedParasTable(response, institutionId) {
        let tbody = '';
        let sno = 1;

        if (unselectedTableDT) {
            unselectedTableDT.destroy();
        }

        if (Array.isArray(response) && response.length > 0) {
            window.paraCounts.unselected = response.length;
            updateButtonCounts();
            window.selectedRowIds.clear();

            response.forEach(row => {
                let remarksCell = '-';
                    if (row.remarks && row.remarks !== 'null') {
                        remarksCell = `
                            <button type="button"
                                class="btn btn-sm ${row.approververifiedflag === 'Y' ? 'btn-success' : 'btn-primary'} remarks-btn"
                                data-auditslipid="${row.auditslipid}"
                                data-mainslipnumber="${row.mainslipnumber}"
                                data-table-type="unselected">
                                ${row.approververifiedflag === 'Y' ?
                                    '<i class="fas fa-check-circle me-1"></i> Verified' :
                                    '<i class="fas fa-eye me-1"></i> View Remarks'}
                            </button>
                        `;
                    }

                const rowKey = `${row.mainslipnumber}`;
                const isChecked = window.selectedRowIds.has(rowKey);

                let disabledAttr = '';
                let cursorStyle = 'pointer';
                let titleText = 'Select this para';

                if (row.approververifiedflag === 'Y') {
                    disabledAttr = '';
                    cursorStyle = 'pointer';
                    titleText = 'Select this para';
                } else if (row.approververifiedflag === 'N' || !row.approververifiedflag || row.approververifiedflag === '') {
                    disabledAttr = 'disabled';
                    cursorStyle = 'not-allowed';
                    titleText = 'Para not approved - cannot be selected';
                }

                function escapeHtmlAttribute(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }

                const checkbox = `
                    <div class="form-check d-flex justify-content-center align-items-center" style="min-height: 40px;">
                        <input type="checkbox"
                            class="form-check-input row-checkbox"
                            title="${titleText}"
                            data-auditslipid="${row.auditslipid || ''}"
                            data-transactionno="${row.transactionno || ''}"
                            data-mainobjectionid="${row.mainobjectionid || ''}"
                            data-subobjectionid="${row.subobjectionid || ''}"
                            data-schteammemberid="${row.schteammemberid || ''}"
                            data-auditscheduleid="${row.auditscheduleid || ''}"
                            data-auditplanid="${row.auditplanid || ''}"
                            data-irregularitiescode="${row.irregularitiescode || ''}"
                            data-irregularitiescatcode="${row.irregularitiescatcode || ''}"
                            data-irregularitiessubcatcode="${row.irregularitiessubcatcode || ''}"
                            data-slipdetails="${escapeHtmlAttribute(row.slipdetails || '')}"
                            data-amountinvolved="${row.amountinvolved || null}"
                            data-liability="${row.liability || ''}"
                            data-remarks="${escapeHtmlAttribute(row.remarks || '')}"
                            data-tempslipnumber="${row.tempslipnumber || ''}"
                            data-mainslipnumber="${row.mainslipnumber || ''}"
                            data-severitycode="${row.severitycode || ''}"
                            data-schemastatus="${row.schemastatus || ''}"
                            data-auditeeschemecode="${row.auditeeschemecode || ''}"
                            data-rejoinderstatus="${row.rejoinderstatus || ''}"
                            data-rejoindercycle="${row.rejoindercycle || null}"
                            data-createdby="${row.createdby || ''}"
                            data-forwardedto="${row.forwardedto || ''}"
                            data-processcode="${row.processcode || ''}"
                            data-paraorder="${row.paraorder || null}"
                            data-paraverifiedflag="${row.paraverifiedflag || 'N'}"
                            data-approververifiedflag="${row.approververifiedflag || 'N'}"
                            data-paraverifiedby="${row.paraverifiedby || ''}"
                            data-paraverifiedon="${row.paraverifiedon || ''}"
                            data-forwardedtousertypecode="${row.forwardedtousertypecode || ''}"
                            data-updatedby="${row.updatedby || ''}"
                            data-updatedbyusertypecode="${row.updatedbyusertypecode || ''}"
                            data-quartercode="${row.quartercode || ''}"
                            data-financialyear="${row.financialyear || ''}"
                            data-catcode="${row.catcode || ''}"
                            ${isChecked ? 'checked' : ''}
                            ${disabledAttr}
                            style="width: 20px; height: 20px; cursor: ${cursorStyle};">
                    </div>
                `;

                tbody += `
                    <tr>
                        <td class="text-center">${sno}</td>
                        <td class="text-center text-wrap">${row.paranumber || '-'}</td>
                        <td class="text-center text-wrap">${row.irregularitieselname || '-'}</td>
                        <td class="text-wrap">${row.objectionename || '-'}</td>
                        <td class="text-wrap">${row.slipdetails || '-'}</td>
                        <td class="text-wrap">${row.amountinvolved || '-'}</td>
                        <td class="text-wrap">${remarksCell}</td>
                        <td class="text-center align-middle" style="vertical-align: middle;">${checkbox}</td>
                    </tr>
                `;

                sno++;
            });

            $('#unselectedParasTable tbody').html(tbody);

            unselectedTableDT = $('#unselectedParasTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 mt-3'p>>",
                autoWidth: false,

                columnDefs: [{
                        orderable: false,
                        targets: [6],
                        className: 'text-center align-middle',
                        width: '120px'
                    },
                    {
                        orderable: false,
                        targets: [7],
                        className: 'text-center align-middle',
                        width: '80px'
                    },
                    {
                        className: 'text-center align-middle',
                        targets: [0],
                        width: '50px'
                    },
                    {
                        className: 'text-center text-wrap',
                        targets: [1],
                        width: '100px'
                    },
                    {
                        className: 'text-wrap',
                        targets: [2, 3, 4, 5],
                    }
                ],

                initComplete: function() {
                    const totalRecords = this.api().page.info().recordsTotal;

                    const customHeader = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="alert-warning mt-2 p-2">
                                    <strong>Selected:</strong>
                                    <span id="selectedCount">0</span>
                                    <span class="text-muted"> rows</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="alert-info mt-2 p-2">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Click "View Remarks" button to approve each para
                                </div>
                            </div>
                        </div>
                    `;

                    $('#unselectedParasTable').before(customHeader);
                },

                drawCallback: function(settings) {
                    this.api().rows({ page: 'current' }).nodes().to$().find('.row-checkbox').each(function() {
                        const mainslipnumber = $(this).data('mainslipnumber');
                        const approververifiedflag = $(this).data('approververifiedflag');
                        const rowKey = `${mainslipnumber}`;

                        const wasEnabled = window.enabledRows && window.enabledRows.has(mainslipnumber);
                        const isSelected = window.selectedRowIds.has(rowKey);

                        if (wasEnabled || isSelected || approververifiedflag === 'Y') {
                            $(this).prop('disabled', false).css('cursor', 'pointer');
                        } else {
                            $(this).prop('disabled', true).css('cursor', 'not-allowed');
                        }
                    });

                    updateSelectedCount();

                    $('.remarks-btn').off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const auditslipid = $(this).data('auditslipid');
                        const mainslipnumber = $(this).data('mainslipnumber');
                        const tableType = $(this).data('table-type') || 'selected';

                        fetchRemarksData(auditslipid, mainslipnumber, tableType);
                    });
                }
            });

            $('#tableshow2').removeClass('hide_this');
            $('#no_data').addClass('hide_this');

        } else {
            window.paraCounts.unselected = 0;
            updateButtonCounts();
            $('#tableshow2').addClass('hide_this');
            $('#no_data').removeClass('hide_this');
            $('#no_data').html(`
                <div class="alert alert-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    No unselected paras found for this institution.
                </div>
            `);
        }

        updateButtonCounts();
    }

    function fetchRemarksData(auditslipid, mainslipnumber, tableType) {
        if (!auditslipid || !mainslipnumber) {
            passing_alert_value('Error', 'Invalid data',
                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        showLoading();

        $.ajax({
            url: '/get-approver-para-remarks-details',
            type: 'POST',
            data: {
                auditslipid: auditslipid,
                mainslipnumber: mainslipnumber,
                tableType: tableType,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.data) {
                    openRemarksPopupWithData(response.data, mainslipnumber, tableType);
                } else {
                    passing_alert_value('Error', response.message || 'Failed to fetch remarks data.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error fetching remarks:', error);
                let errorMessage = 'Failed to fetch remarks data. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                passing_alert_value('Error', errorMessage,
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            }
        });
    }

    function openRemarksPopupWithData(data, mainslipnumber, tableType) {
        const {
            paranumber = '',
            objectionename = '',
            slipdetails = '',
            amountinvolved = '',
            liabilityname = '',
            liabilitygpfno = '',
            liabilitydesignation = '',
            liabilityamount = '',
            remarks = '',
            approververifiedflag = 'N'
        } = data;

        let remarksContent = '';
        try {
            if (remarks) {
                const parsed = typeof remarks === 'string' ? JSON.parse(remarks) : remarks;
                remarksContent = parsed.content || '';
            }
        } catch (e) {
            remarksContent = remarks || '';
        }

        $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');

        const detailsHtml = `
            <tr>
                <td>Para Number</th>
                <td><strong>${escapeHtml(paranumber) || '-'}</strong></td>
            </tr>
            <tr>
                <th>Objection Name</th>
                <td>${escapeHtml(objectionename) || '-'}</td>
            </tr>
            <tr>
                <th>Gist of Observation</th>
                <td>${escapeHtml(slipdetails) || '-'}</td>
            </tr>
            <tr>
                <th>Amount Involved</th>
                <td>${escapeHtml(amountinvolved) || '-'}</td>
            </tr>
        `;

        const hasLiability = liabilityname || liabilitygpfno || liabilitydesignation || liabilityamount;

        let liabilityHtml = '';
        if (hasLiability) {
            liabilityHtml = `
                <tr>
                    <th>Liability</th>
                    <td><strong>Yes</strong></td>
                </tr>
                <tr>
                    <th>Liability Name</th>
                    <td>${escapeHtml(liabilityname) || '-'}</td>
                </tr>
                <tr>
                    <th>GPF No</th>
                    <td>${escapeHtml(liabilitygpfno) || '-'}</td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td>${escapeHtml(liabilitydesignation) || '-'}</td>
                </tr>
                <tr>
                    <th>Liability Amount</th>
                    <td>${escapeHtml(liabilityamount) || '-'}</td>
                </tr>
            `;
        } else {
            liabilityHtml = `
                <tr>
                    <th>Liability</th>
                    <td><strong>-</strong></td>
                </tr>
            `;
        }

        $('#slipDetailsTable tbody').html(detailsHtml + liabilityHtml);

        window.currentRemarksRow = mainslipnumber;
        window.currentRemarksTableType = tableType;

        const $checkbox = $(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);
        const paraVerifiedFlag = $checkbox.length ? $checkbox.data('approververifiedflag') : 'N';

        const shouldHideVerifiedCheckbox = paraVerifiedFlag === 'Y';

        loadckeditorauditor(remarksContent, 'remarksEditor');

        $('#remarksModal').data('mainslipnumber', mainslipnumber);

        if (shouldHideVerifiedCheckbox) {
            $('#viewedParaCheckbox').prop('checked', true).prop('disabled', true);
            $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');
        } else {
            $('#viewedParaCheckbox').prop('checked', false).prop('disabled', false);
        }

        $('#remarksModal').modal('show');

        $('#saveRemarksBtn').off('click').on('click', function() {
            const isApproved = $('#viewedParaCheckbox').is(':checked');

            if (isApproved && mainslipnumber) {
                saveParaApprovedFlag(mainslipnumber, tableType);
            } else {
                $('#remarksModal').modal('hide');
            }
        });
    }

    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function updateUIForStatus() {
        const saveButton = $('#buttonaction');
        const saveButton2 = $('#buttonaction2');
        const finalizeButton = $('#finalizeButton');
        const selectAllCheckbox = $('#selectAllRows');

        if (window.isGlobalFinalized) {
            saveButton.addClass('hide_this');
            saveButton2.addClass('hide_this');
            finalizeButton
                .removeClass('hide_this')
                .prop('disabled', true)
                .addClass('disabled')
                .text('Para(s) Finalized')
                .css({
                    'background-color': '#dc3545',
                    'color': 'white',
                    'border-color': '#dc3545'
                });

            selectAllCheckbox
                .prop('disabled', true)
                .css('cursor', 'not-allowed');

            $('.row-checkbox').prop('disabled', true).addClass('disabled');

            $('#selectedCount').parent()
                .addClass('alert-danger')
                .removeClass('alert-info alert-warning alert-success')
                .html(`
                    <i class="fas fa-ban me-2"></i>
                    <strong>Consolidation Finalized:</strong>
                    This consolidation has been finalized and cannot be modified.
                    ${window.finalizedInstitutions && window.finalizedInstitutions.length > 0 ?
                        ` (${window.finalizedInstitutions.length} institution(s) finalized)` : ''}
                `);

            updateSelectedCount();
            return;
        }

        if (window.isReportFinalized) {
            saveButton.addClass('hide_this');
            saveButton2.addClass('hide_this');

            finalizeButton
                .removeClass('hide_this')
                .prop('disabled', true)
                .addClass('disabled')
                .text('Finalized');
            selectAllCheckbox
                .prop('disabled', true)
                .css('cursor', 'not-allowed');

            $('.row-checkbox')
                .prop('disabled', true)
                .css('cursor', 'not-allowed');

            $('#selectedCount').parent()
                .addClass('alert-success')
                .removeClass('alert-info alert-warning');

            const selectedCount = window.selectedRowIds.size;
            $('#selectedCount').text(selectedCount + ' (Finalized)');

            if (selectedTableDT) {
                const allRows = selectedTableDT.rows({
                    search: 'applied'
                }).nodes();
                $(allRows).find('.row-checkbox').prop('disabled', true).css('cursor', 'not-allowed');
            }

            updateSelectedCount();
        } else if (window.isForwarded) {
            saveButton.addClass('hide_this');
            saveButton2.addClass('hide_this');

            finalizeButton
                .removeClass('hide_this')
                .prop('disabled', true)
                .addClass('disabled')
                .text('Forwarded');

            selectAllCheckbox.prop('disabled', true).css('cursor', 'not-allowed');

            $('#selectedCount').parent()
                .addClass('alert-success')
                .removeClass('alert-info alert-warning');

            const selectedCount = window.selectedRowIds.size;
            $('#selectedCount').text(selectedCount + ' (Finalized)');

            if (selectedTableDT) {
                const allRows = selectedTableDT.rows({
                    search: 'applied'
                }).nodes();
                $(allRows).find('.row-checkbox').prop('disabled', true).css('cursor', 'not-allowed');
            }

            updateSelectedCount();
        } else if (window.hasSavedData) {
            selectAllCheckbox.prop('disabled', false).show();
            saveButton.removeClass('hide_this').text('Update Draft');

            finalizeButton.removeClass('hide_this').text('Finalize').removeClass('disabled').prop('disabled', false);
            $('#selectedCount').parent()
                .addClass('alert-warning')
                .removeClass('alert-info alert-success');
            updateSelectedCount();
            refreshCheckboxesBasedOnVerification();
        } else {
            selectAllCheckbox.prop('disabled', false).show();
            saveButton.removeClass('hide_this').text('Save Draft');
            saveButton2.removeClass('hide_this').text('Save Draft');

            refreshCheckboxesBasedOnVerification();

            $('#selectedCount').parent()
                .removeClass('alert-warning alert-success')
                .addClass('alert-info');
        }

        updateSelectedCount();
    }

    function refreshCheckboxesBasedOnVerification() {
        if (!selectedTableDT) return;

        const allRows = selectedTableDT.rows({
            search: 'applied'
        }).nodes();

        $(allRows).find('.row-checkbox').each(function() {
            const paraVerifiedFlag = $(this).data('approververifiedflag');
            const isFinalized = window.isReportFinalized;

            if (isFinalized) {
                $(this).prop('disabled', true).css('cursor', 'not-allowed');
                $(this).attr('title', 'Already finalized/forwarded');
            } else if (paraVerifiedFlag === 'Y') {
                $(this).prop('disabled', false).css('cursor', 'pointer');
                $(this).attr('title', 'Select this para');
            } else {
                $(this).prop('disabled', true).css('cursor', 'not-allowed');
                $(this).attr('title', 'Para not verified - cannot be selected');
            }
        });

        const currentRows = selectedTableDT.rows({
            page: 'current'
        }).nodes().to$();
        currentRows.find('.row-checkbox').each(function() {
            const paraVerifiedFlag = $(this).data('approververifiedflag');
            const isFinalized = window.isReportFinalized;

            if (isFinalized) {
                $(this).prop('disabled', true).css('cursor', 'not-allowed');
                $(this).attr('title', 'Already finalized/forwarded');
            } else if (paraVerifiedFlag === 'Y') {
                $(this).prop('disabled', false).css('cursor', 'pointer');
                $(this).attr('title', 'Select this para');
            } else {
                $(this).prop('disabled', true).css('cursor', 'not-allowed');
                $(this).attr('title', 'Para not verified - cannot be selected');
            }
        });

        updateSelectAllCheckbox();
    }

    function updateSelectAllCheckbox() {
        let currentDT = null;

        if (!$('#tableshow').hasClass('hide_this') && selectedTableDT) {
            currentDT = selectedTableDT;
        } else if (!$('#tableshow2').hasClass('hide_this') && unselectedTableDT) {
            currentDT = unselectedTableDT;
        }

        if (!currentDT) return;

        const totalRows = currentDT.rows({
            search: 'applied'
        }).count();
        const selectedCount = window.selectedRowIds.size;
        const selectAllCheckbox = $('#selectAllRows');

        if (selectedCount === 0) {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', false);
        } else if (selectedCount === totalRows) {
            selectAllCheckbox.prop('checked', true);
            selectAllCheckbox.prop('indeterminate', false);
        } else {
            selectAllCheckbox.prop('checked', false);
            selectAllCheckbox.prop('indeterminate', true);
        }
    }

    function updateSelectedCount() {
        const filteredIds = Array.from(window.selectedRowIds).filter(key =>
            key && key !== 'undefined' && key !== 'null'
        );

        window.selectedRowIds = new Set(filteredIds);

        const count = window.selectedRowIds.size;

        let totalRows = 0;
        if (!$('#tableshow').hasClass('hide_this') && selectedTableDT) {
            totalRows = selectedTableDT.rows({
                search: 'applied'
            }).count();
        } else if (!$('#tableshow2').hasClass('hide_this') && unselectedTableDT) {
            totalRows = unselectedTableDT.rows({
                search: 'applied'
            }).count();
        }

        if (window.isReportFinalized) {
            $('#selectedCount').text(`${count} out of ${totalRows} (Finalized)`);
        } else {
            $('#selectedCount').text(`${count} out of ${totalRows}`);
        }

        updateSelectAllCheckbox();
    }



    function getSelectedRows() {
        let selected = [];

        let currentDT = null;
        const catcode = '{{ $catcode }}' || null;

        if (!$('#tableshow').hasClass('hide_this') && selectedTableDT) {
            currentDT = selectedTableDT;
        } else if (!$('#tableshow2').hasClass('hide_this') && unselectedTableDT) {
            currentDT = unselectedTableDT;
        }

        if (!currentDT) return selected;

        const allRows = currentDT.rows({ search: 'applied' }).nodes();

        $(allRows).find('.row-checkbox:checked').each(function(index) {
            const mainslipnumber = $(this).data('mainslipnumber');

            if (!mainslipnumber) return;

            const rowKey = `${mainslipnumber}`;
            window.selectedRowIds.add(rowKey);

            let slipdetails = $(this).data('slipdetails') || '';
            if (typeof slipdetails !== 'string') slipdetails = String(slipdetails);

            let remarks = $(this).data('remarks') || '';
            if (typeof remarks !== 'string') {
                try {
                    remarks = JSON.stringify(remarks);
                } catch (e) {
                    remarks = String(remarks);
                }
            }

            const rowDataObj = {
                auditslipid: $(this).data('auditslipid') || null,
                transactionno: $(this).data('transactionno') || '',
                mainobjectionid: $(this).data('mainobjectionid') || '',
                subobjectionid: $(this).data('subobjectionid') || '',
                auditscheduleid: $(this).data('auditscheduleid') || '',
                schteammemberid: $(this).data('schteammemberid') || null,
                auditplanid: $(this).data('auditplanid') || null,
                amountinvolved: parseFloat($(this).data('amountinvolved')) || null,
                tempslipnumber: $(this).data('tempslipnumber') || '',
                mainslipnumber: mainslipnumber,
                severitycode: $(this).data('severitycode') || '',
                liability: $(this).data('liability') || null,
                slipdetails: slipdetails,
                schemastatus: $(this).data('schemastatus') || '',
                auditeeschemecode: $(this).data('auditeeschemecode') || '',
                irregularitiescode: $(this).data('irregularitiescode') || '',
                irregularitiescatcode: $(this).data('irregularitiescatcode') || '',
                irregularitiessubcatcode: $(this).data('irregularitiessubcatcode') || '',
                processcode: $(this).data('processcode') || '',
                remarks: remarks,
                statusflag: 'Y',
                rejoinderstatus: $(this).data('rejoinderstatus') || '',
                rejoindercycle: parseInt($(this).data('rejoindercycle')) || null,
                createdby: $(this).data('createdby') || '',
                forwardedto: $(this).data('forwardedto') || '',
                forwardedtousertypecode: $(this).data('forwardedtousertypecode') || '',
                updatedby: $(this).data('updatedby') || '',
                updatedbyusertypecode: $(this).data('updatedbyusertypecode') || '',
                quartercode: $(this).data('quartercode') || '',
                financialyear: $(this).data('financialyear') || '',
                paraorder: $(this).data('paraorder') || null,
                paraverifiedflag: $(this).data('paraverifiedflag') || null,
                approververifiedflag: $(this).data('approververifiedflag') || null,
                paraverifiedby: $(this).data('paraverifiedby') || '',
                paraverifiedon: $(this).data('paraverifiedon') || '',
                catcode: $(this).data('catcode') || ''
            };

            Object.keys(rowDataObj).forEach(key => {
                if (rowDataObj[key] === undefined) rowDataObj[key] = null;
            });

            selected.push(rowDataObj);
        });

        return selected;
    }
    $('#buttonaction').on('click', function() {
        const selectedRows = getSelectedRows();
        const institutionId = window.currentInstitutionId;

        if (!institutionId) {
            passing_alert_value('Alert', 'Please select an institution first.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (selectedRows.length === 0) {
            passing_alert_value('Alert', 'Please select at least one record.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        const isUpdate = window.hasSavedData;
        const actionText = isUpdate ? 'update' : 'save';

        let totalRows = 0;
        if (selectedTableDT) {
            totalRows = selectedTableDT.rows({
                search: 'applied'
            }).count();
        }

        passing_alert_value(
            'Confirm',
            `You have selected <strong>${selectedRows.length}</strong> out of <strong>${totalRows}</strong> Para(s) to ${actionText}.<br><br>Do you want to proceed?`,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        window.pendingSaveData = {
            selectedRows: selectedRows,
            institutionId: institutionId,
            isUpdate: isUpdate,
            count: selectedRows.length,
            total: totalRows,
            mode: 'select'
        };
    });

    $('#buttonaction2').on('click', function() {
        const selectedRows = getSelectedRows();
        const institutionId = window.currentInstitutionId;

        if (!institutionId) {
            passing_alert_value('Alert', 'Please select an institution first.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (selectedRows.length === 0) {
            passing_alert_value('Alert', 'Please select at least one record.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        const isUpdate = window.hasSavedData;
        const actionText = isUpdate ? 'update' : 'save';

        let totalRows = 0;
        if (unselectedTableDT) {
            totalRows = unselectedTableDT.rows({
                search: 'applied'
            }).count();
        }

        passing_alert_value('Confirm',
            `Selected <strong>${selectedRows.length}</strong> Para(s) out of <strong>${totalRows}</strong> Para(s) to ${actionText}.<br><br>Do you want to proceed?`,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        window.pendingSaveData = {
            selectedRows: selectedRows,
            institutionId: institutionId,
            isUpdate: isUpdate,
            count: selectedRows.length,
            total: totalRows,
            mode: 'unselect'
        };
    });

    function saveApprovedParas(selectedRows, institutionId, mode) {
        const saveButton = (mode === 'select') ? $('#buttonaction') : $('#buttonaction2');
        const originalText = saveButton.html();
        saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

        const savedCount = selectedRows.length;

        showLoading();

        $.ajax({
            url: '/save-consolidated-finalized-paras',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token: '{{ csrf_token() }}',
                instid: institutionId,
                rows: selectedRows,
                mode: mode
            }),
            success: function(response) {
                if (response.success) {
                    // Clear cache
                    selectedParasCache.delete(institutionId);
                    unselectedParasCache.delete(institutionId);

                    window.hasSavedData = true;
                    window.isReportFinalized = false;

                    updateUIForStatus();


                    passing_alert_value('Success',
                        `<strong>${response.count}</strong> para(s) have been saved successfully!`,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    // Refresh data
                    if (selectedTableDT) {
                        selectedTableDT.destroy();
                        selectedTableDT = null;
                    }
                    if (unselectedTableDT) {
                        unselectedTableDT.destroy();
                        unselectedTableDT = null;
                    }

                    $('#selectedParasTable tbody').html('');
                    $('#unselectedParasTable tbody').html('');

                    window.selectedRowIds.clear();
                    if (window.enabledRows) window.enabledRows.clear();

                    updateAllParaCounts(institutionId);

                    setTimeout(() => {
                        getSelectedParas(institutionId);
                        $('#viewSelectedBtn').addClass('active');
                        $('#viewUnselectedBtn').removeClass('active');
                        updateSelectedCount();
                    }, 500);
                    hideLoading();


                } else {
                    passing_alert_value('Alert', response.message || 'Error saving data.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                    hideLoading();

                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error:', error);
                passing_alert_value('Alert', 'Network error. Please try again.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            },
            complete: function() {
                saveButton.prop('disabled', false).html(originalText);
                hideLoading();
            }
        });
    }

    function loadExistingConsolidatedData(institutionId) {
        showLoading();
        $.ajax({
            url: '/get-existing-forwarded-consolidated-para',
            type: 'POST',
            data: {
                instid: institutionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    if (selectedTableDT) {
                        const existingSlipNumbers = new Set(response.data.map(item => item.mainslipnumber));

                        window.selectedRowIds.clear();
                        const allRows = selectedTableDT.rows({
                            search: 'applied'
                        }).nodes();

                        $(allRows).find('.row-checkbox').each(function() {
                            const mainslipnumber = $(this).data('mainslipnumber');
                            const paraVerifiedFlag = $(this).data('approververifiedflag');
                            const isSaved = existingSlipNumbers.has(mainslipnumber);

                            if (window.isReportFinalized) {
                                if (isSaved) {
                                    const rowKey = `${mainslipnumber}`;
                                    window.selectedRowIds.add(rowKey);

                                    $(this)
                                        .prop('checked', true)
                                        .prop('disabled', true)
                                        .css('cursor', 'not-allowed')
                                        .attr('title', 'Forwarded - Cannot modify');
                                } else {
                                    $(this)
                                        .prop('checked', false)
                                        .prop('disabled', true)
                                        .css('cursor', 'not-allowed')
                                        .attr('title', 'Report forwarded - Cannot modify');
                                }
                            } else {
                                if (paraVerifiedFlag === 'Y') {
                                    $(this).prop('disabled', false).css('cursor', 'pointer');
                                    if (isSaved) {
                                        const rowKey = `${mainslipnumber}`;
                                        window.selectedRowIds.add(rowKey);
                                        $(this).prop('checked', true);
                                    }
                                } else {
                                    $(this).prop('disabled', true).css('cursor', 'not-allowed');
                                    $(this).prop('checked', false);
                                }
                            }
                        });

                        const currentRows = selectedTableDT.rows({
                            page: 'current'
                        }).nodes().to$();

                        currentRows.find('.row-checkbox').each(function() {
                            const mainslipnumber = $(this).data('mainslipnumber');
                            const paraVerifiedFlag = $(this).data('approververifiedflag');
                            const isSaved = existingSlipNumbers.has(mainslipnumber);

                            if (window.isReportFinalized) {
                                if (isSaved) {
                                    $(this)
                                        .prop('checked', true)
                                        .prop('disabled', true)
                                        .css('cursor', 'not-allowed');
                                } else {
                                    $(this)
                                        .prop('checked', false)
                                        .prop('disabled', true)
                                        .css('cursor', 'not-allowed');
                                }
                            } else {
                                if (paraVerifiedFlag === 'Y') {
                                    $(this).prop('disabled', false).css('cursor', 'pointer');
                                    if (isSaved) {
                                        $(this).prop('checked', true);
                                    }
                                } else {
                                    $(this).prop('disabled', true).css('cursor', 'not-allowed');
                                    $(this).prop('checked', false);
                                }
                            }
                        });

                        if (window.isReportFinalized) {
                            $('#selectAllRows')
                                .prop('disabled', true)
                                .css('cursor', 'not-allowed');
                        }

                        updateSelectAllCheckbox();
                        updateSelectedCount();

                        if (window.isReportFinalized) {
                            const selectedCount = window.selectedRowIds.size;
                            const totalRows = selectedTableDT.rows({
                                search: 'applied'
                            }).count();

                            $('#selectedCount').text(`${selectedCount} out of ${totalRows} (Forwarded)`);

                            $('#selectedCount').parent()
                                .removeClass('alert-info alert-warning alert-primary')
                                .html(`
                                    <strong>Para(s) Forwarded:</strong>
                                    ${selectedCount} para(s) have been forwarded to next level.
                                `);
                        }
                        hideLoading();

                    }
                    hideLoading();
                } else if (window.isReportFinalized) {
                    if (selectedTableDT) {
                        const allRows = selectedTableDT.rows({
                            search: 'applied'
                        }).nodes();

                        $(allRows).find('.row-checkbox')
                            .prop('checked', false)
                            .prop('disabled', true)
                            .css('cursor', 'not-allowed')
                            .attr('title', 'Report forwarded - Cannot modify');

                        $('#selectAllRows')
                            .prop('checked', false)
                            .prop('disabled', true)
                            .css('cursor', 'not-allowed');

                        $('#selectedCount').text('0 (Forwarded)');

                        $('#selectedCount').parent()
                            .removeClass('alert-info alert-warning alert-primary');
                    }
                     hideLoading();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading existing consolidated data:', error);

                if (window.isReportFinalized && selectedTableDT) {
                    const allRows = selectedTableDT.rows({
                        search: 'applied'
                    }).nodes();

                    $(allRows).find('.row-checkbox')
                        .prop('disabled', true)
                        .css('cursor', 'not-allowed');

                    $('#selectAllRows').prop('disabled', true);
                }
                 hideLoading();
            }
        });
    }

    $('#finalizeButton').on('click', function() {
                showLoading();

        const institutionId = window.currentInstitutionId;

        if (!institutionId) {
                        hideLoading();

            passing_alert_value('Alert', 'No institution selected.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (window.isReportFinalized) {
                        hideLoading();

            passing_alert_value('Alert', 'This report is already finalized and cannot be modified.',
                'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (window.isForwarded) {
                        hideLoading();

            passing_alert_value('Alert', 'This report is already forwarded and cannot be modified.',
                'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        const selectedRows = getSelectedRows();
        const selectedCount = selectedRows.length;

        if (selectedCount === 0) {
                        hideLoading();

            passing_alert_value('Alert', 'Please select at least one para to Finalize.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (window.allFilteredInstitutionIds && window.allFilteredInstitutionIds.length > 0) {
            $('#finalizeButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Finalizing...');

            checkAllInstitutionsHaveParas(window.allFilteredInstitutionIds)
                .then(result => {
                    $('#finalizeButton').prop('disabled', false).html('Finalize');

                    if (result.all_have_paras_selected_and_verified) {
                        proceedToFinalize(selectedRows, institutionId, selectedCount);
                        hideLoading();

                    } else {
                        displayInstitutionVerificationTable(result);
                        hideLoading();

                    }
                })
                .catch(error => {
                    $('#finalizeButton').prop('disabled', false).html('Finalize');
                    console.error('Error checking institutions:', error);
                    passing_alert_value('Error', 'Error checking institutions. Please try again.',
                        'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                                    hideLoading();

                });
        } else {
            proceedToFinalize(selectedRows, institutionId, selectedCount);
                        hideLoading();

        }

    });

    function checkAllInstitutionsHaveParas(institutionIds) {
        return new Promise((resolve, reject) => {
            if (!institutionIds || institutionIds.length === 0) {
                resolve({
                    all_have_paras_selected_and_verified: false,
                    institutions_without_paras: [],
                    total_institutions: 0
                });
                return;
            }

            $('#finalizeButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Finalizing...');

            $.ajax({
                url: '/check-all-selected-institutions-paras',
                type: 'POST',
                data: {
                    institution_ids: institutionIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                 $('#finalizeButton').prop('disabled', false).html('Finalize');

                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(response.message || 'Error checking institutions');
                    }
                },
                error: function(xhr, status, error) {
                    $('#finalizeButton').prop('disabled', false).html('Finalize');
                    reject('Network error: ' + error);
                }
            });
        });
    }

    function displayInstitutionVerificationTable(result) {

        if (verificationDataTable && $.fn.DataTable.isDataTable('#institutionsWithUnverifiedParasTable')) {
            verificationDataTable.destroy();
            $('#institutionsWithUnverifiedParasTable tbody').empty();
        }

        let tableData = [];
        let unverifiedCount = 0;

        if (result.institutions_with_issues && result.institutions_with_issues.length > 0) {
                unverifiedCount = result.institutions_with_issues.length;

                result.institutions_with_issues.forEach((institution, index) => {
                    const hasSelectedParas = (parseInt(institution.selected_serious_paras) + parseInt(institution.selected_nonserious_paras)) > 0;
                    const hasUnverifiedSerious = parseInt(institution.serious_unverified) > 0 && parseInt(institution.selected_serious_paras) > 0;
                    const hasUnverifiedMoneyValue = parseInt(institution.money_value_unverified_paras) > 0 && parseInt(institution.selected_nonserious_paras) > 0;

                    let issueType = '';
                    if (!hasSelectedParas) {
                        issueType = 'No paras selected';
                    } else if (hasUnverifiedSerious && hasUnverifiedMoneyValue) {
                        issueType = 'Unverified serious & money value paras';
                    } else if (hasUnverifiedSerious) {
                        issueType = 'Unverified serious paras';
                    } else if (hasUnverifiedMoneyValue) {
                        issueType = 'Unverified money value paras';
                    }

                    tableData.push({
                        index: index + 1,
                        instename: institution.instename || 'N/A',
                        quarter: institution.quarter || '-',
                        audityear: institution.audit_period || '-',
                        category: institution.catename || '-',
                        subcategory: institution.subcatename || '-',
                        region: institution.regionename || '-',
                        district: institution.distename || '-',
                        selectedSerious: parseInt(institution.selected_serious_paras) || 0,
                        selectedNonSerious: parseInt(institution.selected_nonserious_paras) || 0,
                        totalSelected: (parseInt(institution.selected_serious_paras) + parseInt(institution.selected_nonserious_paras)) || 0,
                        unverifiedSerious: parseInt(institution.serious_unverified) || 0,
                        totalparas: parseInt(institution.total_paras) || 0,
                        unverifiedMoneyValue: parseInt(institution.money_value_unverified_paras) || 0,
                        issueType: issueType,
                        status: hasSelectedParas ? 'Has Issues' : 'No Selected Paras'
                    });
                });
            }

        // Update count
        $('#unverifiedParasCount').text(unverifiedCount);
            if (unverifiedCount > 0) {
                $('#verificationMessage').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> The following institutions have issues that need to be resolved before finalization:
                        <ul class="mt-2 mb-0">
                            <li>Institutions with no selected paras</li>
                            <li>Institutions with unverified selected serious paras</li>
                            <li>Institutions with unverified selected money value paras</li>
                        </ul>
                    </div>
                `);
            }

        verificationDataTable = $('#institutionsWithUnverifiedParasTable').DataTable({
            data: tableData,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            responsive: true,
            destroy: true,
            autoWidth: false,
            scrollX: false,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 mt-3'p>>",
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ institutions",
                infoEmpty: "Showing 0 to 0 of 0 institutions",
                infoFiltered: "(filtered from _MAX_ total institutions)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            columns: [
                { data: 'index', className: 'text-center', width: '5%' },
                { data: 'instename', width: '30%' },
                { data: 'quarter', className: 'text-center', width: '5%' },
                { data: 'audityear', className: 'text-center', width: '10%' },
                { data: 'category', className: 'text-center', width: '10%' },
                { data: 'subcategory', className: 'text-center', width: '10%' },
                { data: 'region', className: 'text-center', width: '10%' },
                { data: 'district', className: 'text-center', width: '10%' },
                {
                    data: 'totalparas',
                    className: 'text-center',
                    width: '8%',
                    render: function(d, type, row) {
                        if (d === 0) {
                            return '<span class="badge bg-danger">0</span>';
                        }
                        return `<span class="badge bg-primary">${d}</span>`;
                    }
                },
                {
                    data: 'unverifiedSerious',
                    className: 'text-center',
                    width: '10%',
                    render: d => d > 0
                        ? `<span class="badge bg-danger">${d}</span>`
                        : `<span class="badge bg-success">0</span>`
                },
                {
                    data: 'unverifiedMoneyValue',
                    className: 'text-center',
                    width: '8%',
                    render: function(d, type, row) {
                        if (d > 0 ) {
                            return `<span class="badge bg-danger">${d}</span>`;
                        }
                        return '<span class="badge bg-success">0</span>';
                    }
                },
                {
                    data: 'issueType',
                    className: 'text-center',
                    width: '10%',
                    render: function(d, type, row) {
                        if (row.unverifiedSerious > 0 && row.unverifiedMoneyValue > 0) {
                            return '<span class="badge bg-danger">Unverified</span>';
                        } else if (row.unverifiedSerious > 0) {
                            return '<span class="badge bg-danger">Unverified</span>';
                        } else if (row.unverifiedMoneyValue > 0) {
                            return '<span class="badge bg-danger">Unverified</span>';
                        } else if (row.totalSelected === 0) {
                            return '<span class="badge bg-danger">No Paras Selected</span>';
                        }
                        return '<span class="badge bg-success">Verified</span>';
                    }
                }
            ],
            order: [[0, 'asc']]
        });

        $('#institutionVerificationModal').modal('show');
    }

    $('#institutionVerificationModal').on('hidden.bs.modal', function() {
        if (verificationDataTable && $.fn.DataTable.isDataTable('#institutionsWithUnverifiedParasTable')) {
            verificationDataTable.destroy();
            $('#institutionsWithUnverifiedParasTable tbody').empty();
        }
    });

    function proceedToFinalize(selectedRows, institutionId, selectedCount) {
        if (!selectedRows || selectedRows.length === 0) {
            passing_alert_value('Alert', 'No paras selected for finalization.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }


        $('#selectedParasTableBody').html(`
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Loading all saved paras...</span>
                </td>
            </tr>
        `);

        showLoading();

        $.ajax({
            url: "/get-finalized-paras-details",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.data && response.data.length > 0) {
                        window.pendingFinalizeData = {
                            selectedRows: response.data,
                            institutionId: institutionId,
                            count: response.data.length,
                            acknowledgeFlag: 'F'
                        };
                    displaySelectedParasInModal(response.data);
                } else {
                    $('#selectedParasTableBody').html(`
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No detailed information found for selected paras.
                            </td>
                        </tr>
                    `);
                }

            },
            error: function() {
                hideLoading();
                $('#selectedParasTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading para details. Please try again.
                        </td>
                    </tr>
                `);
            }
        });

        $('#acknowledgeModal').modal('show');
    }
        $('#confirmPasswordBtn').on('click', function() {
            const password = $('#passwordInput').val().trim();
            const $btn = $(this);
            const $errorDiv = $('#passwordError');

            if (!password) {
                return;
            }

            // Hide any previous error
            $errorDiv.hide().empty();

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Verifying...');

            $.ajax({
                url: '/verify-user-password',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    password: password
                },
                success: function(response) {
                    if (response.success) {
                        $('#passwordConfirmModal').modal('hide');

                        if (window.pendingFinalizeData) {
                            finalizeConsolidatedReport(
                                window.pendingFinalizeData.selectedRows,
                                window.pendingFinalizeData.institutionId,
                                window.pendingFinalizeData.acknowledgeFlag
                            );
                            window.pendingFinalizeData = null;
                        } else {
                            console.error('No pending finalize data found');
                            passing_alert_value('Error', 'No data to finalize. Please try again.',
                                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                        }
                    } else {
                        // Password incorrect - show error
                        $errorDiv.html('<i class="fas fa-exclamation-circle me-2"></i>' + (response.message || 'Invalid password. Please try again.')).show();
                        $('#passwordInput').val('').focus();
                        $btn.prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error('Password verification error:', xhr);
                    let errorMessage = 'Failed to verify password. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $errorDiv.html('<i class="fas fa-exclamation-circle me-2"></i>' + errorMessage).show();
                    $('#passwordInput').val('').focus();
                    $btn.prop('disabled', true);
                },
                complete: function() {
                    $('#finalizeButton').prop('disabled', false).html('Finalized');
                }
            });
        });

        // Reset and enable button when user types
        $('#passwordInput').on('input', function() {
            const password = $(this).val().trim();
            $('#confirmPasswordBtn').prop('disabled', password.length === 0);
            $('#passwordError').hide().empty();
        });

        // Handle Enter key
        $('#passwordInput').on('keypress', function(e) {
            if (e.which === 13 && !$('#confirmPasswordBtn').prop('disabled')) {
                $('#confirmPasswordBtn').click();
            }
        });

        // Reset modal when hidden
        $('#passwordConfirmModal').on('hidden.bs.modal', function() {
            $('#passwordInput').val('');
            $('#passwordError').hide().empty();
            $('#confirmPasswordBtn').prop('disabled', true).html('<i class="fas fa-check-circle me-2"></i>Confirm & Finalize');
        });

        $(document).on('click', '#process_button', function() {

            if (window.pendingSaveData) {
                const { selectedRows, institutionId, mode } = window.pendingSaveData;
                saveApprovedParas(selectedRows, institutionId, mode);
                window.pendingSaveData = null;
            } else if (window.pendingFinalizeData) {
                $('#passwordConfirmModal').modal('show');
            }
        });


        // Fix the confirmFinalizeBtn click handler
        $('#confirmFinalizeBtn').on('click', function() {
            if (!$('#acknowledgeCheckbox').is(':checked')) {
                return;
            }

        const selectedCount = window.pendingFinalizeData ? window.pendingFinalizeData.count : 0;

            if (selectedCount === 0) {
                passing_alert_value('Alert', 'No paras available for finalization.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                return;
            }

            passing_alert_value(
                'Confirm Finalization',
                `You are about to finalize <strong>${selectedCount}</strong> para(s).<br><br>
                This action will:<br>
                1. Finalize all paras<br>
                2. Cannot be undone<br><br>
                Are you sure you want to proceed?`,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            // Data is already stored in window.pendingFinalizeData from finalizeButton click
            $('#acknowledgeModal').modal('hide');
        });

        function finalizeConsolidatedReport(selectedRows, institutionId, acknowledgeFlag) {
            const finalizeButton = $('#finalizeButton');
            const originalText = finalizeButton.html();
            finalizeButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Finalizing...');

            // Use the data from window.pendingFinalizeData which contains the backend data
            const rowsToFinalize = window.pendingFinalizeData ? window.pendingFinalizeData.selectedRows : selectedRows;
            const count = rowsToFinalize ? rowsToFinalize.length : 0;

            showLoading();

            $.ajax({
                url: '/finalize-consolidated-selected-paras',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    instid: institutionId,
                    acknowledge_flag: acknowledgeFlag,
                    rows: rowsToFinalize,
                    finalize_all: true
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        window.isForwarded = true;
                        window.isGlobalFinalized = true;
                        updateUIForStatus();

                        passing_alert_value('Success',
                            `<strong>${count}</strong> para(s) have been Finalized successfully!<br><br>
                            <strong>Important:</strong> This consolidation is now finalized for All institutions and cannot be modified.`,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );

                        // Clear any pending data
                        window.pendingFinalizeData = null;
                    } else {
                        passing_alert_value('Error', response.message || 'Error finalizing report.',
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Finalize error:', error, xhr.responseText);
                    hideLoading();
                    passing_alert_value('Error', 'Something went wrong. Please try again.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                },
                complete: function() {
                    finalizeButton.prop('disabled', false).html(originalText);
                }
            });
        }

        $('#passwordInput').on('focus', function() {
            $(this).removeAttr('readonly');
            $(this).attr('autocomplete', 'new-password');
        });

        $(document).on('focus', '.dataTables_filter input', function() {
            $(this).removeAttr('readonly');
        });
    function displaySelectedParasInModal(paras) {
        showLoading();
        let tbody = '';
        let checkedCount = 0;
        let totalCount = paras.length;

        paras.forEach((para, index) => {
            const isApproved = para.approververifiedflag === 'Y';

            const isChecked = true;
            if (isChecked) checkedCount++;

            const slipDetails = para.slipdetails || '-';
            const truncatedDetails = slipDetails.length > 100 ?
                slipDetails.substring(0, 100) + '...' :
                slipDetails;

            const statusBadge = isApproved ?
                '<span class="badge bg-success">Verified</span>' :
                '<span class="badge bg-warning">Pending</span>';

            const formattedAmount = para.amountinvolved ?
                formatCurrency(para.amountinvolved) :
                '-';

        tbody += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center">
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input para-finalize-checkbox"
                            type="checkbox"
                            data-auditslipid="${para.auditslipid || ''}"
                            data-mainslipnumber="${para.mainslipnumber || ''}"
                            data-paranumber="${para.paranumber || ''}"
                            data-objectionename="${para.objectionename || ''}"
                            data-slipdetails="${para.slipdetails || ''}"
                            data-amountinvolved="${para.amountinvolved || ''}"
                            data-irrregularity="${para.irrregularity || ''}"
                            data-institution-name="${para.institution_name || ''}"
                            data-approververifiedflag="${para.approververifiedflag || 'N'}"
                            data-paraverifiedflag="${para.paraverifiedflag || 'N'}"
                            checked
                            disabled>
                    </div>
                </td>
                <td class="text-center fw-semibold">${para.paranumber || '-'}</td>
                <td class="text-center fw-semibold">${para.institution_name || '-'}</td>
                <td class="text-center fw-semibold">${para.irrregularity || '-'}</td>
                <td>${para.objectionename || para.mainobjectionid || '-'}</td>
                <td>${truncatedDetails}</td>
                <td class="text-end">${formattedAmount}</td>
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
        });

        hideLoading();
        $('#selectedCountLabel').text(checkedCount);
        $('#selectedParasTableBody').html(tbody);

        $('#selectAllFinalize').prop('checked', true)
                          .prop('disabled', true).addClass('disabled')
                          .prop('indeterminate', false);

        const unverifiedCount = paras.filter(p => p.paraverifiedflag !== 'Y').length;
        if (unverifiedCount > 0) {
            $('#acknowledgeModal .alert-warning').html(`
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> ${unverifiedCount} para(s) are not verified. It is recommended to verify all paras before finalization.
            `).removeClass('alert-warning').addClass('alert-danger');
        }

        attachCheckboxHandlers();
    }

    function attachCheckboxHandlers() {
        $('.para-finalize-checkbox').off('change').on('change', function() {
            updateSelectedCountAndAcknowledge();
        });

        $('#selectAllFinalize').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.para-finalize-checkbox').prop('checked', isChecked);
            updateSelectedCountAndAcknowledge();
        });
    }

    function updateSelectedCountAndAcknowledge() {
        const checkedCount = $('.para-finalize-checkbox:checked').length;
        $('#selectedCountLabel').text(checkedCount);

        const anySelected = checkedCount > 0;
        $('#acknowledgeCheckbox').prop('disabled', !anySelected);

        if (!anySelected && $('#acknowledgeCheckbox').is(':checked')) {
            $('#acknowledgeCheckbox').prop('checked', false);
        }

        const totalCount = $('.para-finalize-checkbox').length;
        if (checkedCount === totalCount && totalCount > 0) {
            $('#selectAllFinalize').prop('checked', true).prop('indeterminate', false);
        } else if (checkedCount > 0) {
            $('#selectAllFinalize').prop('checked', false).prop('indeterminate', true);
        } else {
            $('#selectAllFinalize').prop('checked', false).prop('indeterminate', false);
        }

        updateConfirmButtonState();
    }

    $('#acknowledgeCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('#confirmFinalizeBtn').prop('disabled', !isChecked);
    });

    function updateConfirmButtonState() {
        const isAcknowledged = $('#acknowledgeCheckbox').is(':checked');
        const hasSelectedParas = $('.para-finalize-checkbox:checked').length > 0;
        $('#confirmFinalizeBtn').prop('disabled', !(isAcknowledged && hasSelectedParas));
    }


     $(document).on('click', '.row-checkbox', function(e) {
        // Prevent any changes if report is finalized
        if (window.isReportFinalized) {
            $(this).prop('checked', true);
            passing_alert_value(
                'Cannot Modify',
                'This report has been finalized. No modifications are allowed.',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert'
            );
            e.preventDefault();
            return false;
        }

        const $checkbox = $(this);
        const mainslipnumber = $checkbox.data('mainslipnumber');
        const willBeChecked = !$checkbox.is(':checked'); // This is what the checkbox WILL be after the click
        const isCurrentlyChecked = $checkbox.is(':unchecked'); // This is what the checkbox IS now
        const row = $checkbox.closest('tr');
        const paraNumber = row.find('td:nth-child(2)').text().trim();
        const objectionName = row.find('td:nth-child(4)').text().trim();

        if (!mainslipnumber) {
            console.warn('Checkbox with empty mainslipnumber detected, skipping');
            return false;
        }

        const rowKey = `${mainslipnumber}`;

        // If UNCHECKING (currently checked, will be unchecked after click)
        if (isCurrentlyChecked) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Show confirmation dialog
            passing_alert_value(
                'Confirm Unselection',
                `Are you sure you want to remove this para from selection?<br><br>
                <strong>Para No:</strong> ${paraNumber || 'N/A'}<br>
                <strong>Objection:</strong> ${objectionName || 'N/A'}`,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert'
            );

            // Wait for user response
            const confirmModal = $('#confirmation_alert');

            confirmModal.off('click', '#ok_button');

            confirmModal.on('click', '#ok_button', function() {
                // User confirmed - uncheck the checkbox
                window.selectedRowIds.delete(rowKey);
                $checkbox.prop('checked', false);
                updateSelectedCount();
                updateSelectAllCheckbox();

                // Hide the modal
                confirmModal.modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                // Also trigger the change event for DataTable compatibility
                $checkbox.trigger('change');
            });

            confirmModal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
                // If user cancels, do nothing (checkbox stays checked)
                // No need to manually set it back since we prevented the click
            });

            return false;
        }
        // If CHECKING (currently unchecked, will be checked after click)
        else if (!isCurrentlyChecked && willBeChecked) {
            // Allow the checkbox to be checked normally
            window.selectedRowIds.add(rowKey);
            if (!window.enabledRows) window.enabledRows = new Set();
            window.enabledRows.add(mainslipnumber);
            updateSelectedCount();
            updateSelectAllCheckbox();
        }
    });




    function formatCurrency(amount) {
        if (!amount) return '₹0';
        return '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // CKEditor functions
    function loadckeditorauditor(auditorreply, textareaId) {
        if (window[textareaId] && typeof window[textareaId].destroy === 'function') {
            window[textareaId].destroy();
        }

        const editorElement = document.getElementById(textareaId);
        if (editorElement) {
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
                        'uploadImage', 'insertTable', '|',
                    ],
                    shouldNotGroupWhenFull: true
                },
                placeholder: 'Approver Remarks',
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
                removePlugins: [
                    'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                    'MultiLevelList', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                    'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                    'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                    'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            }).then(editor => {
                window[textareaId] = editor;
                editor.setData(auditorreply);
                window[textareaId].enableReadOnlyMode('initial');
            }).catch(error => {
                console.error("CKEditor Initialization Error:", error);
            });
        }
    }

    // Remarks popup functions
    function openRemarksPopup(
        encodedRemarks,
        mainslipnumber,
        auditslipid,
        encodedParanumber,
        encodedObjectionename,
        encodedSlipdetails,
        encodedAmountinvolved,
        encodedliabilityname,
        encodedliabilitygpfno,
        encodedliabilitydesignation,
        encodedliabilityamount
    ) {
        const paranumber = decodeURIComponent(encodedParanumber || '');
        const objectionename = decodeURIComponent(encodedObjectionename || '');
        const slipdetails = decodeURIComponent(encodedSlipdetails || '');
        const amountinvolved = decodeURIComponent(encodedAmountinvolved || '');

        const liabilityname = decodeURIComponent(encodedliabilityname || '');
        const liabilitygpfno = decodeURIComponent(encodedliabilitygpfno || '');
        const liabilitydesignation = decodeURIComponent(encodedliabilitydesignation || '');
        const liabilityamount = decodeURIComponent(encodedliabilityamount || '');

        let remarksContent = '';

        try {
            if (encodedRemarks) {
                const decoded = decodeURIComponent(encodedRemarks);
                const parsed = JSON.parse(decoded);
                remarksContent = parsed.content || '';
            }
        } catch (e) {
            try {
                remarksContent = decodeURIComponent(encodedRemarks);
            } catch (e2) {
                remarksContent = encodedRemarks || '';
            }
        }

        $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');

        const detailsHtml = `
            <tr>
                <td>Para Number</td>
                <td><strong>${paranumber || '-'}</strong></td>
            </tr>
            <tr>
                <td>Objection Name</td>
                <td>${objectionename || '-'}</td>
            </tr>
            <tr>
                <td>Gist of Observation</td>
                <td>${slipdetails || '-'}</td>
            </tr>
            <tr>
                <td>Amount Involved</td>
                <td>${amountinvolved || '-'}</td>
            </tr>
        `;

        const hasLiability = liabilityname || liabilitygpfno || liabilitydesignation || liabilityamount;

        let liabilityHtml = '';

        if (hasLiability) {
            liabilityHtml = `
                <tr>
                    <td>Liability</td>
                    <td><strong>Yes</strong></td>
                </tr>
                <tr>
                    <td>Liability Name</td>
                    <td>${liabilityname || '-'}</td>
                </tr>
                <tr>
                    <td>GPF No</td>
                    <td>${liabilitygpfno || '-'}</td>
                </tr>
                <tr>
                    <td>Designation</td>
                    <td>${liabilitydesignation || '-'}</td>
                </tr>
                <tr>
                    <td>Liability Amount</td>
                    <td>${liabilityamount || '-'}</td>
                </tr>
            `;
        } else {
            liabilityHtml = `
                <tr>
                    <td>Liability</td>
                    <td><strong>-</strong></td>
                </tr>
            `;
        }

        $('#slipDetailsTable tbody').html(detailsHtml + liabilityHtml);

        window.currentRemarksRow = mainslipnumber;

        const $checkbox = $(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);
        const paraVerifiedFlag = $checkbox.length ? $checkbox.data('approververifiedflag') : 'N';

        const shouldHideVerifiedCheckbox = paraVerifiedFlag === 'Y';

        loadckeditorauditor(remarksContent, 'remarksEditor');

        $('#remarksModal').data('mainslipnumber', mainslipnumber);

        if (shouldHideVerifiedCheckbox) {
            $('#viewedParaCheckbox').prop('checked', true).prop('disabled', true);
            $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');
        } else {
            $('#viewedParaCheckbox').prop('checked', false).prop('disabled', false);
        }

        $('#remarksModal').modal('show');

        $('#saveRemarksBtn').off('click').on('click', function() {
            const isApproved = $('#viewedParaCheckbox').is(':checked');

            if (isApproved && mainslipnumber) {
                saveParaApprovedFlag(mainslipnumber, auditslipid);
            } else {
                $('#remarksModal').modal('hide');
            }
        });
    }

    function openSelectedRemarksPopup(
        encodedRemarks,
        mainslipnumber,
        auditslipid,
        encodedParanumber,
        encodedObjectionename,
        encodedSlipdetails,
        encodedAmountinvolved,
        encodedliabilityname,
        encodedliabilitygpfno,
        encodedliabilitydesignation,
        encodedliabilityamount
    ) {
        const paranumber = decodeURIComponent(encodedParanumber || '');
        const objectionename = decodeURIComponent(encodedObjectionename || '');
        const slipdetails = decodeURIComponent(encodedSlipdetails || '');
        const amountinvolved = decodeURIComponent(encodedAmountinvolved || '');

        const liabilityname = decodeURIComponent(encodedliabilityname || '');
        const liabilitygpfno = decodeURIComponent(encodedliabilitygpfno || '');
        const liabilitydesignation = decodeURIComponent(encodedliabilitydesignation || '');
        const liabilityamount = decodeURIComponent(encodedliabilityamount || '');

        let remarksContent = '';

        try {
            if (encodedRemarks) {
                const decoded = decodeURIComponent(encodedRemarks);
                const parsed = JSON.parse(decoded);
                remarksContent = parsed.content || '';
            }
        } catch (e) {
            console.error('Remarks parse error:', e);
            try {
                remarksContent = decodeURIComponent(encodedRemarks);
            } catch (e2) {
                remarksContent = encodedRemarks || '';
            }
        }

        $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');

        const detailsHtml = `
            <tr>
                <td>Para Number</td>
                <td><strong>${paranumber || '-'}</strong></td>
            </tr>
            <tr>
                <td>Objection Name</td>
                <td>${objectionename || '-'}</td>
            </tr>
            <tr>
                <td>Gist of Observation</td>
                <td>${slipdetails || '-'}</td>
            </tr>
            <tr>
                <td>Amount Involved</td>
                <td>${amountinvolved || '-'}</td>
            </tr>
        `;

        const hasLiability =
            liabilityname || liabilitygpfno || liabilitydesignation || liabilityamount;

        let liabilityHtml = '';

        if (hasLiability) {
            liabilityHtml = `
                <tr>
                    <td>Liability</td>
                    <td><strong>Yes</strong></td>
                </tr>
                <tr>
                    <td>Liability Name</td>
                    <td>${liabilityname || '-'}</td>
                </tr>
                <tr>
                    <td>GPF No</td>
                    <td>${liabilitygpfno || '-'}</td>
                </tr>
                <tr>
                    <td>Designation</td>
                    <td>${liabilitydesignation || '-'}</td>
                </tr>
                <tr>
                    <td>Liability Amount</td>
                    <td>${liabilityamount || '-'}</td>
                </tr>
            `;
        } else {
            liabilityHtml = `
                <tr>
                    <td>Liability</td>
                    <td><strong>-</strong></td>
                </tr>
            `;
        }

        $('#slipDetailsTable tbody').html(detailsHtml + liabilityHtml);

        window.currentRemarksRow = mainslipnumber;

        const $checkbox = $(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);
        const paraVerifiedFlag = $checkbox.length ? $checkbox.data('approververifiedflag') : 'N';

        const shouldHideVerifiedCheckbox = paraVerifiedFlag === 'Y';

        loadckeditorauditor(remarksContent, 'remarksEditor');

        $('#remarksModal').data('mainslipnumber', mainslipnumber);

        if (shouldHideVerifiedCheckbox) {
            $('#viewedParaCheckbox').prop('checked', true).prop('disabled', true);
            $('#saveRemarksBtn').prop('disabled', true).addClass('disabled');
        } else {
            $('#viewedParaCheckbox').prop('checked', false).prop('disabled', false);
        }

        $('#remarksModal').modal('show');

        $('#saveRemarksBtn').off('click').on('click', function() {
            const isVerified = $('#viewedParaCheckbox').is(':checked');

            if (isVerified && mainslipnumber) {
                saveSelectedParaVerifiedFlag(mainslipnumber, auditslipid);
            } else {
                $('#remarksModal').modal('hide');
            }
        });
    }

    function saveParaApprovedFlag(mainslipnumber, tableType) {
        const $checkbox = $(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);
        const auditslipid = $checkbox.length ? $checkbox.data('auditslipid') : '';

        if (!auditslipid) {
            passing_alert_value('Error', 'Could not find audit slip details.',
                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        const saveData = {
            mainslipnumber: mainslipnumber,
            auditslipid: auditslipid,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/save-para-approved',
            type: 'POST',
            data: saveData,
            beforeSend: function() {
                $('#saveRemarksBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function(response) {
                if (response.success) {
                    if ($checkbox.length) {
                        $checkbox
                            .data('approververifiedflag', 'Y')
                            .prop('disabled', false)
                            .css('cursor', 'pointer');

                        if (tableType === 'selected') {
                            updateselectedparaCheckboxInDataTable(mainslipnumber, true);
                        } else {
                            updateCheckboxInDataTable(mainslipnumber, true);
                        }
                    }

                    const dt = tableType === 'selected' ?
                        $('#selectedParasTable').DataTable() :
                        $('#unselectedParasTable').DataTable();

                    let foundRow = null;
                    if (dt) {
                        dt.rows().every(function() {
                            const rowNode = this.node();
                            if ($(rowNode).find(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`).length) {
                                foundRow = rowNode;
                                return false;
                            }
                        });
                    }

                    if (foundRow) {
                        const $remarksBtn = $(foundRow).find('.remarks-btn');
                        if ($remarksBtn.length) {
                            $remarksBtn.removeClass('btn-primary').addClass('btn-success');
                            $remarksBtn.html('<i class="fas fa-check-circle me-1"></i> Verified');
                        }
                    }

                    passing_alert_value('Success', 'Para marked as verified successfully!',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    $('#remarksModal').modal('hide');
                } else {
                    passing_alert_value('Error', response.message || 'Failed to mark para as verified.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            },
            error: function(xhr, status, error) {
                passing_alert_value('Error', 'Failed to save. Please try again.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            },
            complete: function() {
                $('#saveRemarksBtn').prop('disabled', false).text('Submit');
            }
        });
    }

    function saveSelectedParaVerifiedFlag(mainslipnumber) {
        const $checkbox = $(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);
        const auditslipid = $checkbox.length ? $checkbox.data('auditslipid') : '';

        if (!auditslipid) {
            passing_alert_value('Error', 'Could not find audit slip details.',
                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        const saveData = {
            mainslipnumber: mainslipnumber,
            auditslipid: auditslipid,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/save-selected-para-approved',
            type: 'POST',
            data: saveData,
            beforeSend: function() {
                $('#saveRemarksBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function(response) {
                if (response.success) {
                    if ($checkbox.length) {
                        $checkbox
                            .data('approververifiedflag', 'Y')
                            .prop('disabled', false)
                            .css('cursor', 'pointer');

                        updateselectedparaCheckboxInDataTable(mainslipnumber, true);
                    }

                    const dt = $('#selectedParasTable').DataTable();
                    let foundRow = null;

                    dt.rows().every(function() {
                        const rowNode = this.node();

                        if ($(rowNode).find(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`).length) {
                            foundRow = rowNode;
                            return false;
                        }
                    });

                    if (foundRow) {
                        const $remarksBtn = $(foundRow).find('.remarks-btn');
                        if ($remarksBtn.length) {
                            $remarksBtn.removeClass('btn-primary').addClass('btn-success');
                            $remarksBtn.html('<i class="fas fa-check-circle me-1"></i> Verified');
                        }
                    }

                    passing_alert_value('Success', 'Para marked as verified successfully!',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    $('#remarksModal').modal('hide');
                } else {
                    passing_alert_value('Error', response.message || 'Failed to mark para as verified.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            },
            error: function(xhr, status, error) {
                passing_alert_value('Error', 'Failed to save. Please try again.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            },
            complete: function() {
                $('#saveRemarksBtn').prop('disabled', false).text('Submit');
            }
        });
    }

    function updateselectedparaCheckboxInDataTable(mainslipnumber, enabled = true) {
        const dt = $('#selectedParasTable').DataTable();
        if (!dt || !$.fn.DataTable.isDataTable('#selectedParasTable')) return;

        dt.rows().every(function() {
            const rowNode = this.node();
            const $checkbox = $(rowNode).find(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);

            if ($checkbox.length) {
                $checkbox.data('approververifiedflag', enabled ? 'Y' : 'N');
                $checkbox.attr('data-approververifiedflag', enabled ? 'Y' : 'N');

                if (enabled) {
                    $checkbox.prop('disabled', false).css('cursor', 'pointer');
                } else {
                    $checkbox.prop('disabled', true).css('cursor', 'not-allowed');
                }

                const cell = this.cell($checkbox.closest('td'));
                const currentData = cell.data();

                if (currentData && typeof currentData === 'string') {
                    const updatedData = enabled ?
                        currentData
                        .replace('data-approververifiedflag="N"', 'data-approververifiedflag="Y"')
                        .replace('disabled', '')
                        .replace('cursor: not-allowed;', 'cursor: pointer;') :
                        currentData
                        .replace('data-approververifiedflag="Y"', 'data-approververifiedflag="N"')
                        .replace('cursor: pointer;', 'cursor: not-allowed;');

                    cell.data(updatedData).draw(false);
                }
            }
        });
    }

    function updateCheckboxInDataTable(mainslipnumber, enabled = true) {
        const dt = $('#unselectedParasTable').DataTable();
        if (!dt || !$.fn.DataTable.isDataTable('#unselectedParasTable')) return;

        dt.rows().every(function() {
            const rowNode = this.node();
            const $checkbox = $(rowNode).find(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);

            if ($checkbox.length) {
                $checkbox.data('approververifiedflag', enabled ? 'Y' : 'N');
                $checkbox.attr('data-approververifiedflag', enabled ? 'Y' : 'N');

                if (enabled) {
                    $checkbox.prop('disabled', false).css('cursor', 'pointer');
                } else {
                    $checkbox.prop('disabled', true).css('cursor', 'not-allowed');
                }

                 const cell = this.cell($checkbox.closest('td'));
                const currentData = cell.data();

                if (currentData && typeof currentData === 'string') {
                    const updatedData = enabled ?
                        currentData
                        .replace('data-approververifiedflag="N"', 'data-approververifiedflag="Y"')
                        .replace('disabled', '')
                        .replace('cursor: not-allowed;', 'cursor: pointer;') :
                        currentData
                        .replace('data-approververifiedflag="Y"', 'data-approververifiedflag="N"')
                        .replace('cursor: pointer;', 'cursor: not-allowed;');

                    cell.data(updatedData).draw(false);
                }
            }
        });
    }

    $('#viewedParaCheckbox').on('change', function() {
        $('#saveRemarksBtn')
            .prop('disabled', !this.checked)
            .toggleClass('disabled', !this.checked);
    });

    function goBack() {
        window.location.href = '{{ route("approver.institution.list") }}';
    }

    function clearAllCaches() {
        selectedParasCache.clear();
        unselectedParasCache.clear();
        cacheTimestamp.clear();
    }
</script>
@endsection
