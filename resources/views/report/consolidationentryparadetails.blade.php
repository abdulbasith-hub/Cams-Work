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

@section('title', 'Consolidation Para Check - ' . $instename)
@section('content')

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

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

    #consoleidationtable td.text-center {
        vertical-align: middle;
    }

    #consoleidationtable .form-check {
        min-height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    #consoleidationtable .form-check-input {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }

    #consoleidationtable .form-check-input:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }

    #consoleidationtable tbody td {
        vertical-align: middle !important;
    }

    #consoleidationtable .row-checkbox:enabled {
        cursor: pointer;
        border-color: #009951;
        background-color: #ffffff;
    }

    #consoleidationtable .row-checkbox:disabled {
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
                <a href="{{ route('consolidationentry.institution.list') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-arrow-left me-1"></i> Back to Institutions
                </a>
                <div class="filter-panel">
                <div class="row">

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">Department</label>
                        <div class="auto-field" id="display_deptname">{{ $deptname ?: '-' }}</div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">Region</label>
                        <div class="auto-field" id="display_regionname">{{ $regionname ?: '-' }}</div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">District</label>
                        <div class="auto-field" id="display_districtname">{{ $distname ?: '-' }}</div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">Category</label>
                        <div class="auto-field" id="display_catname">{{ $catname ?: '-' }}</div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">SubCategory</label>
                        <div class="auto-field" id="display_subcatname">{{ $subcatname ?: '-' }}</div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                        <label class="form-label">Audit Year</label>
                        <div class="auto-field" id="display_audityear">{{ $audityear ?: '-' }}</div>
                    </div>

                </div>

                </div>

                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="consoleidationtable"
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
                                    <th class="text-center">
                                        Select Para to be included
                                    </th>
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
                    {{-- <div id='no_data' class='lang text-center'>
                        <p>Loading data...</p>
                    </div> --}}
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

<!-- Acknowledge Modal -->
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
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="checkbox" id="selectAllFinalize"
                                                    disabled>
                                            </div>
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acknowledgeCheckbox">
                            <label class="form-check-label fw-semibold" for="acknowledgeCheckbox">
                                I have verified the details of <span id="selectedCountLabel">0</span> selected paras
                                and found them to be correct.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success lang" id="confirmFinalizeBtn" disabled>
                    Forward to Next Level
                </button>
                <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Institution Verification Modal -->
<div class="modal fade" id="institutionVerificationModal" tabindex="-1"
    aria-labelledby="institutionVerificationModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                    <th>Total Paras</th>
                                    <th>Serious Unverified</th>
                                    <th>Money Value Unverified</th>
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
                <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal">
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
    let consolidationDT = null;
    let selectedParas = new Map();
    let verificationDataTable = null;

    window.selectedRowIds = new Set();
    window.isReportFinalized = false;
    window.hasSavedData = false;
    window.isForwarded = false;
    window.currentInstitutionId = '{{ $instid }}';
    window.enabledRows = new Set();
    window.allFilteredInstitutionIds = [];
    window.pendingFinalizeData = null;
    window.pendingSaveData = null;
    const institutionId = '{{ $instid }}';
const auditplanid = '{{ $auditplanid }}';
    // const institutionName = '{{ $instename }}';

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
            url: '/check-all-institutions-paras',
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
                    checkReportStatus(institutionId);

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

        window.currentInstitutionId = institutionId;

        $.ajax({
            url: '/check-consolidated-report-status',
            type: 'POST',
            data: {
                inst_id: institutionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();

                if (response.success) {
                    window.isReportFinalized = response.isFinalized;
                    window.hasSavedData = response.hasSavedData;
                    window.isForwarded = response.isForwarded;
                    updateUIForStatus();
                }
                getDataAfterStatusCheck(institutionId);
            },
            error: function() {
                hideLoading();

                console.error('Error checking report status');
                getDataAfterStatusCheck(institutionId);
            }
        });
    }

    function getDataAfterStatusCheck(institutionId) {
        showLoading();
        $.ajax({
            url: "/getslipdetailsbasedoninst",
            type: "POST",
            data: {
                instid: institutionId,
		auditplanid: auditplanid,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#consoleidationtable tbody').html(`
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="loader"></div>
                            <p class="mt-2">Loading data...</p>
                        </td>
                    </tr>
                `);
            },
            success: function(response) {

                let tbody = '';
                let sno = 1;

                if ($.fn.DataTable && $.fn.DataTable.fnIsDataTable('#consoleidationtable')) {
                    $('#consoleidationtable').DataTable().clear().destroy();
                }

                if (Array.isArray(response) && response.length > 0) {
                    window.selectedRowIds.clear();

                    response.forEach(row => {
                        // In the response.forEach loop, replace the remarksCell creation with:
                        let remarksCell = '-';
                        if (row.remarks && row.remarks !== 'null') {
                            remarksCell = `
                                <button type="button"
                                    class="btn btn-sm ${row.paraverifiedflag === 'Y' ? 'btn-success' : 'btn-primary'} remarks-btn"
                                    data-auditslipid="${row.auditslipid}"
                                    data-mainslipnumber="${row.mainslipnumber}"
                                    data-paraverifiedflag="${row.paraverifiedflag}">
                                    ${row.paraverifiedflag === 'Y' ?
                                        '<i class="fas fa-check-circle me-1"></i> Verified' :
                                        '<i class="fas fa-eye me-1"></i> View Remarks'}
                                </button>
                            `;
                        }
                        const rowKey = `${row.mainslipnumber}`;
                        const isFinalized = window.isForwarded;
                        const isChecked = isFinalized ? window.selectedRowIds.has(rowKey) : false;
                        const isParaVerified = row.paraverifiedflag === 'Y';

                        let disabledAttr = 'disabled';
                        let cursorStyle = 'not-allowed';
                        let titleText = 'Para not verified - cannot be selected';

                        if (isParaVerified) {
                            if (window.isForwarded || window.isReportFinalized) {
                                disabledAttr = 'disabled';
                                cursorStyle = 'not-allowed';
                                titleText = 'Already finalized/forwarded';
                            } else {
                                disabledAttr = '';
                                cursorStyle = 'pointer';
                                titleText = 'Select this para';
                            }
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
                                    data-forwardedtousertypecode="${row.forwardedtousertypecode || ''}"
                                    data-updatedby="${row.updatedby || ''}"
                                    data-updatedbyusertypecode="${row.updatedbyusertypecode || ''}"
                                    data-quartercode="${row.quartercode || ''}"
                                    data-financialyear="${row.financialyear || ''}"
                                    data-paraverifiedflag="${row.paraverifiedflag || 'N'}"
                                    data-paraverifiedby="${row.paraverifiedby || ''}"
                                    data-paraverifiedon="${row.paraverifiedon || ''}"
                                    data-catcode="${row.catcode || ''}"
                                    ${disabledAttr}
                                    style="width: 20px; height: 20px; cursor: ${cursorStyle};"
                                    ${isChecked ? 'checked' : ''}>
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

                    $('#consoleidationtable tbody').html(tbody);

                    consolidationDT = $('#consoleidationtable').DataTable({
                        responsive: true,
                        destroy: true,
                        pageLength: 10,
                        autoWidth: false,
                        scrollX: false,
                        lengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 mt-3'p>>",

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

                        language: {
                            emptyTable: "No data available"
                        },

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
                                            <strong>Note:</strong> Click "View Remarks" button to enable selection for each para
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#consoleidationtable').before(customHeader);
                        },

                        drawCallback: function(settings) {
                            updateSelectedCount();
                             $('.remarks-btn').off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();

                                const auditslipid = $(this).data('auditslipid');
                                const mainslipnumber = $(this).data('mainslipnumber');

                                // Fetch remarks data via AJAX
                                fetchRemarksData(auditslipid, mainslipnumber);
                            });

                            $('.row-checkbox').off('change').on('change', function() {
                                const checkbox = $(this);
                                const rowKey = checkbox.data('mainslipnumber');

                                if (checkbox.prop('checked')) {
                                    window.selectedRowIds.add(rowKey);
                                } else {
                                    window.selectedRowIds.delete(rowKey);
                                }

                                updateSelectedCount();
                            });
                        }
                    });

                    $('#tableshow').removeClass('hide_this');

                    if (window.isForwarded || window.hasSavedData) {
                        loadExistingConsolidatedData(institutionId);
                    }
                    hideLoading();

                } else {
                    $('#tableshow').addClass('hide_this');
                    $('#no_data').removeClass('hide_this');
                    $('#no_data').html('<p>No data available for selected institution</p>');
                    hideLoading();

                }
            },
            error: function() {
                hideLoading();
                $('#tableshow').addClass('hide_this');
                $('#no_data').removeClass('hide_this');
                $('#no_data').html('<p>Error loading data. Please try again.</p>');
            }
        });
    }

    function fetchRemarksData(auditslipid, mainslipnumber) {
        showLoading();

        $.ajax({
            url: '/get-para-remarks-details',
            type: 'POST',
            data: {
                auditslipid: auditslipid,
                mainslipnumber: mainslipnumber,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    openRemarksPopupWithData(response.data, mainslipnumber);
                } else {
                    passing_alert_value('Error', 'Failed to fetch remarks data.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error fetching remarks:', error);
                passing_alert_value('Error', 'Failed to fetch remarks data. Please try again.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            }
        });
    }

    function openRemarksPopupWithData(data, mainslipnumber) {
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
            paraverifiedflag = 'N'
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
        const paraVerifiedFlag = $checkbox.length ? $checkbox.data('paraverifiedflag') : 'N';

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
                saveParaVerifiedFlag(mainslipnumber);
            } else {
                $('#remarksModal').modal('hide');
            }
        });
    }

    function updateUIForStatus() {
        const saveButton = $('#buttonaction');
        const finalizeButton = $('#finalizeButton');

        if (window.isReportFinalized || window.isForwarded) {
            saveButton.addClass('hide_this');
            finalizeButton
                .removeClass('hide_this')
                .prop('disabled', true)
                .addClass('disabled')
                .text(window.isForwarded ? 'Forwarded' : 'Forwarded');

            $('.row-checkbox').prop('disabled', true).css('cursor', 'not-allowed');

            $('#selectedCount').parent()
                .addClass('alert-success')
                .removeClass('alert-info alert-warning');

            const selectedCount = window.selectedRowIds.size;
            $('#selectedCount').text(selectedCount + ' (Forwarded)');
        } else if (window.hasSavedData) {
            saveButton.removeClass('hide_this').text('Update Draft');
            finalizeButton.removeClass('hide_this').text('Finalize').removeClass('disabled').prop('disabled', false);
            $('#selectedCount').parent()
                .addClass('alert-warning')
                .removeClass('alert-info alert-success');
            refreshCheckboxesBasedOnVerification();
        } else {
            saveButton.removeClass('hide_this').text('Save Draft');
            refreshCheckboxesBasedOnVerification();
            $('#selectedCount').parent()
                .removeClass('alert-warning alert-success')
                .addClass('alert-info');
        }

        updateSelectedCount();
    }

    function refreshCheckboxesBasedOnVerification() {
        if (!consolidationDT) return;

        const allRows = consolidationDT.rows({
            search: 'applied'
        }).nodes();

        $(allRows).find('.row-checkbox').each(function() {
            const paraVerifiedFlag = $(this).data('paraverifiedflag');
            const isFinalized = window.isForwarded || window.isReportFinalized;

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
    }

    function updateSelectedCount() {
        const filteredIds = Array.from(window.selectedRowIds).filter(key =>
            key && key !== 'undefined' && key !== 'null'
        );

        window.selectedRowIds = new Set(filteredIds);

        const count = window.selectedRowIds.size;

        let totalRows = 0;
        if (consolidationDT) {
            totalRows = consolidationDT.rows({
                search: 'applied'
            }).count();
        }

        if (window.isReportFinalized || window.isForwarded) {
            $('#selectedCount').text(`${count} out of ${totalRows} (Forwarded)`);
        } else {
            $('#selectedCount').text(`${count} out of ${totalRows}`);
        }
    }

    function loadExistingConsolidatedData(institutionId) {
        $.ajax({
            url: '/get-existing-consolidated',
            type: 'POST',
            data: {
                instid: institutionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    if (consolidationDT) {
                        const existingSlipNumbers = new Set(response.data.map(item => item.mainslipnumber));

                        window.selectedRowIds.clear();
                        const allRows = consolidationDT.rows({
                            search: 'applied'
                        }).nodes();

                        $(allRows).find('.row-checkbox').each(function() {
                            const mainslipnumber = $(this).data('mainslipnumber');
                            const paraVerifiedFlag = $(this).data('paraverifiedflag');
                            const isSaved = existingSlipNumbers.has(mainslipnumber);

                            if (window.isForwarded ) {
                                if (isSaved) {
                                    const rowKey = `${mainslipnumber}`;
                                    window.selectedRowIds.add(rowKey);
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

                        updateSelectedCount();

                        if (window.isForwarded) {
                            const selectedCount = window.selectedRowIds.size;
                            const totalRows = consolidationDT.rows({
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
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading existing consolidated data:', error);
            }
        });
    }

    function getSelectedRows() {
        let selected = [];
        const catcode = '{{ $catcode }}' || null;

        if (!consolidationDT) {
            console.error('DataTable not initialized');
            return selected;
        }

        const allRows = consolidationDT.rows({
            search: 'applied'
        }).nodes();

        $(allRows).find('.row-checkbox:checked').each(function(index) {
            const mainslipnumber = $(this).data('mainslipnumber');

            if (!mainslipnumber) {
                console.warn('Skipping row with empty mainslipnumber at index:', index);
                return;
            }

            const rowKey = `${mainslipnumber}`;
            window.selectedRowIds.add(rowKey);

            let slipdetails = $(this).data('slipdetails') || '';
            if (typeof slipdetails !== 'string') {
                slipdetails = String(slipdetails);
            }

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
                amtinvolved: ($(this).data('amountinvolved')) || null,
                amountinvolved: ($(this).data('amountinvolved')) || null,
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
                paraverifiedby: $(this).data('paraverifiedby') || '',
                paraverifiedon: $(this).data('paraverifiedon') || '',
                catcode: $(this).data('catcode') || '' || '',
            };

            Object.keys(rowDataObj).forEach(key => {
                if (rowDataObj[key] === undefined) {
                    rowDataObj[key] = null;
                }
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
        if (consolidationDT) {
            totalRows = consolidationDT.rows({
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
            total: totalRows
        };
    });

    function saveConsolidatedReport(selectedRows, institutionId) {
        showLoading();
        const saveButton = $('#buttonaction');
        const originalText = saveButton.html();
        saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');

        const savedCount = selectedRows.length;

        $.ajax({
            url: '/save-consolidated-report',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token: '{{ csrf_token() }}',
                instid: institutionId,
                rows: selectedRows
            }),
            success: function(response) {
                if (response.success) {
                    window.hasSavedData = true;
                    window.isReportFinalized = false;
                    window.isForwarded = false;

                    updateUIForStatus();

                    passing_alert_value('Success',
                        `<strong>${savedCount}</strong> para(s) have been saved in draft.`,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    loadExistingConsolidatedData(institutionId);

                    hideLoading();
                } else {
                    passing_alert_value('Alert', response.message, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    hideLoading();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                passing_alert_value('Alert', 'Something went wrong. Please try again.',
                    'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                      hideLoading();
            },
            complete: function() {
                saveButton.prop('disabled', false).html(originalText);
                  hideLoading();
            }
        });
    }



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
                url: '/check-all-institutions-paras',
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

    $('#finalizeButton').on('click', function() {
        showLoading();
        const institutionId = window.currentInstitutionId;

        if (!institutionId) {
            hideLoading();
            passing_alert_value('Alert', 'Please select an institution first.', 'confirmation_alert',
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
        // Initialize DataTable
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
                    width: '8%',
                    render: function(d, type, row) {
                        if (d > 0 ) {
                            return `<span class="badge bg-danger">${d}</span>`;
                        }
                        return '<span class="badge bg-success">0</span>';
                    }
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
                order: [[0, 'asc']],
                createdRow: function(row, data, dataIndex) {

                },
                initComplete: function() {
                    const api = this.api();
                    const totalRecords = api.data().count();
                }
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
        showLoading();
        if (!selectedRows || selectedRows.length === 0) {
            passing_alert_value('Alert', 'No paras selected for finalization.', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        $('#selectedParasTableBody').html(`
            <tr>
                <td colspan="9" class="text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Loading all saved paras...</span>
                </td>
            </tr>
        `);

        $.ajax({
            url: "/get-selected-paras-details",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    window.pendingFinalizeData = {
                        selectedRows: response.data,
                        institutionId: institutionId,
                        count: response.data.length,
                    };

                    displaySelectedParasInModal(response.data);
                } else {
                    $('#selectedParasTableBody').html(`
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No detailed information found for selected paras.
                            </td>
                        </tr>
                    `);
                }
                hideLoading();
            },
            error: function() {
                $('#selectedParasTableBody').html(`
                    <tr>
                        <td colspan="8" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading para details. Please try again.
                        </td>
                    </tr>
                `);
                hideLoading();
            }
        });

        $('#acknowledgeModal').modal('show');
    }

    function displaySelectedParasInModal(paras) {
        let tbody = '';
        let checkedCount = 0;
        let totalCount = paras.length;

        paras.forEach((para, index) => {
            const isVerified = para.paraverifiedflag === 'Y';

            const isChecked = true;
            if (isChecked) checkedCount++;

            const slipDetails = para.slipdetails || '-';
            const truncatedDetails = slipDetails.length > 100
                ? slipDetails.substring(0, 100) + '...'
                : slipDetails;

            const statusBadge = isVerified
                ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Verified</span>'
                : '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Not Verified</span>';

            const formattedAmount = para.amountinvolved
                ? formatCurrency(para.amountinvolved)
                : '-';

            tbody += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="text-center form-check-input para-finalize-checkbox"
                                type="checkbox"
                                data-auditslipid="${para.auditslipid || ''}"
                                data-mainslipnumber="${para.mainslipnumber || ''}"
                                ${isChecked ? 'checked' : ''} disabled>
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

        $('#selectedCountLabel').text(checkedCount);
        $('#selectedParasTableBody').html(tbody);

        if (checkedCount === totalCount && totalCount > 0) {
            $('#selectAllFinalize').prop('checked', true).prop('indeterminate', false);
        } else if (checkedCount > 0) {
            $('#selectAllFinalize').prop('checked', false).prop('indeterminate', true);
        } else {
            $('#selectAllFinalize').prop('checked', false).prop('indeterminate', false);
        }

        const unverifiedCount = paras.filter(p => p.paraverifiedflag !== 'Y').length;
        if (unverifiedCount > 0) {
            $('#acknowledgeModal .alert-warning').html(`
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> ${unverifiedCount} serious para(s) are not verified. It is recommended to verify all paras before finalization.
            `).removeClass('alert-warning').addClass('alert-danger');
        }

        updateConfirmButtonState();
    }

    $('#acknowledgeCheckbox').on('change', function() {
        updateConfirmButtonState();
    });

    function updateConfirmButtonState() {
        const isAcknowledged = $('#acknowledgeCheckbox').is(':checked');
        const hasSelectedParas = $('.para-finalize-checkbox:checked').length > 0;
        $('#confirmFinalizeBtn').prop('disabled', !(isAcknowledged && hasSelectedParas));
    }

    $('#confirmFinalizeBtn').on('click', function() {
        if (!$('#acknowledgeCheckbox').is(':checked')) {
            return;
        }

        $('#acknowledgeModal').modal('hide');

        const selectedCount = window.pendingFinalizeData ? window.pendingFinalizeData.count : 0;

        if (selectedCount === 0) {
            passing_alert_value('Alert', 'No paras available for finalization.',
                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

       passing_alert_value(
            'Confirm Finalization',
            `You are about to finalize <strong>${selectedCount}</strong> para(s).<br><br>
            Once finalized:<br>
            • The selected paras will be locked<br>
            • They will be forwarded to higher authorities<br>
            • This action cannot be reversed<br><br>
            Do you want to continue?`,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

    });

 function finalizeConsolidatedReport(selectedRows, institutionId, acknowledgeFlag) {
        showLoading();
        const finalizeButton = $('#finalizeButton');
        const originalText = finalizeButton.html();
        finalizeButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Finalizing...');

        const rowsToFinalize = window.pendingFinalizeData ? window.pendingFinalizeData.selectedRows : selectedRows;
        const count = rowsToFinalize ? rowsToFinalize.length : 0;

        $.ajax({
            url: '/finalize-consolidated-report',
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
                    updateUIForStatus();

                    passing_alert_value('Success',
                        `<strong>${count}</strong> para(s) have been forwarded successfully!`,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    window.pendingFinalizeData = null;
                } else {
                    passing_alert_value('Error', response.message || 'Error forwarding report.',
                        'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                passing_alert_value('Error', 'Something went wrong. Please try again.',
                    'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                hideLoading();
            },
            complete: function() {
                finalizeButton.prop('disabled', false).html('Forwarded');
                hideLoading();
            }
        });
    }

    // Handle confirmation from alert modal
    $(document).on('click', '#process_button', function() {
        if (window.pendingSaveData) {
            const {
                selectedRows,
                institutionId
            } = window.pendingSaveData;
            saveConsolidatedReport(selectedRows, institutionId);
            window.pendingSaveData = null;
        } else if (window.pendingFinalizeData) {
            const {
                selectedRows,
                institutionId,
                acknowledgeFlag
            } = window.pendingFinalizeData;
            finalizeConsolidatedReport(selectedRows, institutionId, acknowledgeFlag || 'F');
            window.pendingFinalizeData = null;
        }
    });

    function formatCurrency(amount) {
        if (!amount) return '₹0';
        return '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

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
        const paraVerifiedFlag = $checkbox.length ? $checkbox.data('paraverifiedflag') : 'N';

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
                saveParaVerifiedFlag(mainslipnumber, auditslipid);
            } else {
                $('#remarksModal').modal('hide');
            }
        });
    }

    function saveParaVerifiedFlag(mainslipnumber) {
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
            url: '/save-para-verified',
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
                            .data('paraverifiedflag', 'Y')
                            .prop('disabled', false)
                            .css('cursor', 'pointer');

                        updateCheckboxInDataTable(mainslipnumber, true);
                    }

                    const dt = $('#consoleidationtable').DataTable();
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

    function updateCheckboxInDataTable(mainslipnumber, enabled = true) {
        const dt = $('#consoleidationtable').DataTable();
        if (!dt || !$.fn.DataTable.isDataTable('#consoleidationtable')) return;

        dt.rows().every(function() {
            const rowNode = this.node();
            const $checkbox = $(rowNode).find(`.row-checkbox[data-mainslipnumber="${mainslipnumber}"]`);

            if ($checkbox.length) {
                $checkbox.data('paraverifiedflag', enabled ? 'Y' : 'N');
                $checkbox.attr('data-paraverifiedflag', enabled ? 'Y' : 'N');

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
                        .replace('data-paraverifiedflag="N"', 'data-paraverifiedflag="Y"')
                        .replace('disabled', '')
                        .replace('cursor: not-allowed;', 'cursor: pointer;') :
                        currentData
                        .replace('data-paraverifiedflag="Y"', 'data-paraverifiedflag="N"')
                        .replace('cursor: pointer;', 'cursor: not-allowed;');

                    cell.data(updatedData).draw(false);
                }
            }
        });
    }

    $('#viewedParaCheckbox').on('change', function () {
        $('#saveRemarksBtn')
            .prop('disabled', !this.checked)
            .toggleClass('disabled', !this.checked);
    });

    function loadckeditorauditor(auditorreply, textareaId) {
        let viewslip_auditorremarks;

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
                placeholder: 'General Remarks',
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
                    'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                    'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            }).then(editor => {
                viewslip_auditorremarks = editor;
                window[textareaId] = editor;
                editor.setData(auditorreply);
                window[textareaId].enableReadOnlyMode('initial');
            }).catch(error => {
                console.error("CKEditor Initialization Error:", error);
            });
        } else {
            console.error("Editor element not found:", textareaId);
        }
    }

    function goBack() {
        window.location.href = '{{ route("consolidationentry.institution.list") }}';
    }
</script>
@endsection
