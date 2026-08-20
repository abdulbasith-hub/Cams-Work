@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Audit Schedule Report')


@php

    $sessionchargedel = session('charge');
    //  print_r($sessionchargedel);
    // print_r($sessionchargedel->roletypecode);
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';
    $auditteamhead = $sessionchargedel->auditteamhead;
@endphp






<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<!-- Modal -->
<div class="modal fade" id="slipDetailsModal" tabindex="-1" aria-labelledby="slipDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center position-relative">
                <h5 class="modal-title fw-bold text-center flex-grow-1" id="slipDetailsModalLabel" style="font-size: 1.25rem;">
                   Legacy Para Details
                </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="modalInstitutionName"
                class="text-center mb-3 fw-bold"
                style="font-weight: 300; font-size: 1.5rem; color: #333;">
            </h4>
                <div class="d-flex justify-content-center">
                    <div style="width:80%;">
                        <table class="table table-bordered" id="slipDetailsTable">
                            <tbody>
                                <!-- Rows will be dynamically added -->
                            </tbody>
                        </table>

                        <!-- Remarks Section -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Remarks</h6>
                            <textarea id="remarksEditor" style="width:100%; height:120px;"></textarea>
                        </div>
                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>





<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Legacy - Audit Para Status</div>
        <div class="card-body">
            <form id="lagacyreport_form" name="lagacyreport_form" method='post'>
                <div class="row">
                 @csrf
                 <div class="col-md-4">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" onchange="onchange_region('audityear','audityearcode')"  multiple="multiple" <?php echo $make_dept_disable; ?> id="deptcode"
                            name="deptcode[]">

                            {{-- <option value="" data-name-en="Select Department" data-name-ta="???????? ?????????????????">Select Department</option> --}}


                            <option value="A" data-name-en="All" data-name-ta="???????"> All</option>


                            @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                    @if (
                                    (is_array(old('dept', $deptcode)) && in_array($department->deptcode, old('dept', $deptcode)))
                                    || (old('dept', $deptcode) == $department->deptcode)
                                )
                                    selected
                                @endif
                                        data-name-en="{{ $department->deptelname }}"
                                        data-name-ta="{{ $department->depttlname }}">
                                        {{ $department->deptelname }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="???? ???????? ?????????????">No Departments
                                    Available
                                </option>
                            @endif
                        </select>
                    </div>


                    <div class="col-md-4">
                    <label class="form-label required lang" for="audityearcode" key="audityearcode">Audit Year</label>
                    <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"
                        id="audityearcode" name="audityearcode[]">

                        {{-- <option value="" data-name-en="Select Audit Year" data-name-ta="??????? ??????? ?????????????????">
                        Select Audit Year
                    </option> --}}



                        <option value="A" data-name-en="All" data-name-ta="???????"> All</option>


                    </select>
                </div>








                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Submit </button>
                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="col-12">
    <div class="card card_border">
    <div class="card-header card_header_color lang" id="cardHeader" key="">Legacy - Audit Para Details</div>
    <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="lagacyreporttable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department</th>
                                <th class="lang" key="">Audit Year</th>
                                <th class="lang" key="">Total Data Entered</th>
                                <th class="lang" key="">Finalised</th>
                                <th class="lang" key="">Pending to Finalise</th>


                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-center fs-4">Total:</th>
                                <th></th> <!-- Data Entered total -->
                                <th></th> <!-- Finalised total -->
                                <th></th>


                            </tr>
                        </tfoot>
                    </table>


                    <div id="regionTableContainer" class="mt-4" style="display:none;">
                        <div class="card card_border">
                            <div class="card-header card_header_color position-relative text-center">
                                <span id="regionTableTitle" class="fw-bold">Region-wise details</span>

                                <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-2"
                                    aria-label="Close" id="closeRegionTable"></button>
                            </div>

                            <div class="card-body">
                                <table id="regionReportTable" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic text-center">
                                    <thead>
                                        <tr>
                                            <th> S.No </th>
                                            <th> Region </th>
                                            <th> Count </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-center fs-4">Total:</th>
                                            <th></th>



                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>





                <div id="districtTableContainer" class="mt-4" style="display:none;">
                    <div class="card card_border">
                        <div class="card-header card_header_color position-relative text-center">
                            <span id="districtTableTitle" class="fw-bold">District-wise details</span>

                            <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-2"
                                aria-label="Close" id="closeDistrictTable"></button>
                        </div>

                        <div class="card-body">
                            <table id="districtReportTable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic text-center">
                                <thead>
                                    <tr>
                                        <th> S.No </th>
                                        <th> District </th>
                                        <th> Count </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center fs-4">Total:</th>
                                        <th></th>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>



                <div id="InstitutionTableContainer" class="mt-4" style="display:none;">
                    <div class="card card_border">
                        <div class="card-header card_header_color position-relative text-center">
                            <span id="institutionTitle" class="fw-bold">Institution-wise details</span>
                            <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-2"
                                aria-label="Close" id="closeInstitutionTable"></button>
                        </div>

                        <div class="card-body">
                            <table id="institutionReportTable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic text-center">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Institution</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-ecenter">Total:</th>
                                        <th class=""></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>


                <div id="slipdetailsTableContainer" class="mt-4" style="display:none;">
                    <div class="card card_border">
                        <div class="card-header card_header_color position-relative text-center">
                            <span id="slipdetailsTitle" class="fw-bold">Slip Details</span>
                            <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-2"
                                aria-label="Close" id="closeSlipdetailsTable"></button>
                        </div>

                        <div class="card-body">
                            <table id="slipdetailsReportTable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic text-center">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Slip Details</th>
                                        <th>Audit Period</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>

                            </table>
                        </div>
                    </div>
                </div>








                </div>
            </div>
            <div id='no_data' class='hide_this'>
                <center>No Data Available</center>
            </div>
        </div>
    </div>
</div>


<style>
    #lagacyreporttable tfoot th,
#lagacyreporttable tfoot td,
#regionReportTable tfoot th,
#regionReportTable tfoot td,
#districtReportTable tfoot th,
#districtReportTable tfoot td,
#institutionReportTable tfoot th,
#institutionReportTable tfoot td{
    background-color: #fff !important;
    color: #000;
    font-weight: bold;
}

.wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* Adjust as needed */
    }


</style>

<script src="../assets/js/jquery.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>






<script>


document.addEventListener('DOMContentLoaded', function() {

const closeButtons = [
    { btnId: 'closeRegionTable', containerId: 'regionTableContainer' },
    { btnId: 'closeDistrictTable', containerId: 'districtTableContainer' }
];

closeButtons.forEach(({ btnId, containerId }) => {
    const button = document.getElementById(btnId);
    const container = document.getElementById(containerId);
    if (button && container) {
        button.addEventListener('click', () => {
            container.style.display = 'none';
        });
    }
});
});



function showRegionTable(deptname, regionData) {
    $('#regionTableContainer').show();

    $('#regionTableTitle').text(`Region-wise details for ${deptname}`);

    if ($.fn.DataTable.isDataTable('#regionReportTable')) {
        $('#regionReportTable').DataTable().clear().destroy();
    }

    $('#regionReportTable').DataTable({
        data: regionData,
        lengthChange: false,
        autoWidth: false,
        searching: false,
        paging: false,
        info: false,
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: 'text-center',
                title: 'S.No'
            },
            {
                data: 'regionename',
                title: 'Region Name',
                render: function (data) {
                    return data || '-';
                },
                className: 'text-center text-wrap'
            },
            {
                data: 'regioncount',
                title: 'Total Audit Para(s)',
                render: function (data, type, row) {
                    if (!data) return '-';
                    return `
                        <button type="button"
                            class="btn btn-success btn-sm show-district"
                            data-region="${row.regioncode}"
                            data-deptcode="${row.deptcode}"
                            data-regionname="${row.regionename}">
                            ${data}
                        </button>
                    `;
                },
                className: 'text-center',
            }


        ],

        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            let intVal = function (i) {
                if (typeof i === 'string') {
                    i = i.replace(/[^0-9.-]+/g, '');
                    return i ? parseFloat(i) : 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            let total = api
                .column(2, { page: 'current' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            $(api.column(2).footer()).html('<strong>' + total + '</strong>');
        }

    });
}

let currentAuditYear = null;

$(document).on('click', '.show-region', function () {
    currentAuditYear = $(this).data('auditperiod');
    currentstatusflag = $(this).data('status');

});



$(document).on('click', '.show-slipdetails', function () {
    const instid = $(this).data('instid');
    const deptcode = $(this).data('deptcode');
    const instename = $(this).data('instename');
    const districtName = $(this).data('district');

    const audityearcode = currentAuditYear;
    const statusflag = currentstatusflag;

    $.ajax({
        url: '/fetch_slipdetails',
        type: 'POST',
        data: { instid, deptcode, audityearcode, statusflag },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                showSlipdetailsTable(instename, response.data);
            } else {
                passing_alert_value(
                    'Info',
                    'No slip details found',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        },
        error: function (xhr) {
            console.error('Error fetching slipdetails data:', xhr.responseText);
        }
    });
});



function showSlipdetailsTable(instename, data) {
    $('#slipdetailsTableContainer').show();
    $('#slipdetailsTitle').text(`Para Details for ${instename}`);

    if ($.fn.DataTable.isDataTable('#slipdetailsReportTable')) {
        $('#slipdetailsReportTable').DataTable().clear().destroy();
    }

    $('#slipdetailsReportTable').DataTable({
        data: data,
        processing: true,
        serverSide: false,
        searching: true,
        info: true,
        ordering: false,
        autoWidth: false,
        responsive: true,
        paging: true,        // keep pagination
        lengthChange: false,
        initComplete: function () {
            $("#slipdetailsReportTable").wrap(
                "<div style='overflow:auto; width:100%;position:relative;'></div>"
            );
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: 'text-center'
            },
            {
                data: 'slipdetails',
                title: 'Para Details',
                render: function (data) {
                    return data ? data : '-';
                },
                className: 'text-start wrap-50'
            },
            {
                data: 'audit_period',
                title: 'Audit Period',
                className: 'text-center'
            },

            {
                data: null,
                title: 'Status',
                render: function (data, type, row) {
                    return `
                        <button type="button"
                            class="btn btn-sm btn-primary show-allslipdeptails"
                            data-instid="${row.instid}"
                            data-followupid="${row.followupid}"
                            title="View Details">
                            View Details
                        </button>
                    `;
                },
                className: 'text-center align-middle'
            }

        ],

    });

    $('#closeSlipdetailsTable').off('click').on('click', function () {
        $('#slipdetailsTableContainer').hide();
    });
}

$(document).on('click', '.show-allslipdeptails', function () {
    const instid = $(this).data('instid');
    const followupid = $(this).data('followupid');

    $.ajax({
        url: '/fetch_allslipdetails',
        type: 'POST',
        data: { instid, followupid },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                const slip = response.data[0];
                $('#modalInstitutionName').text(slip.instename || '-');

                const $tbody = $('#slipDetailsTable tbody');
                $tbody.empty();

                $tbody.append(`
                    <tr><td style="background-color:#f0f0f0; width:30%;  border:1px solid #999;"><strong >Type of Para</strong></td><td  style="border:1px solid #999;">${slip.typeofparaename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>State of Para</strong></td><td style="border:1px solid #999;">${slip.stateofparaename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Para Number</strong></td><td style="border:1px solid #999;">${slip.paranumber || '-'}</td></tr>

                    ${(slip.lastactionyear && slip.lastactionmonth) ? `
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Last Action Year</strong>
                    </td>
                    <td style="border:1px solid #999;">${slip.lastactionyear}</td>
                    </tr>
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Last Action Month</strong>
                    </td>
                    <td style="border:1px solid #999;">${slip.lastactionmonth}</td>
                    </tr>
                    ` : `
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Last Action Year</strong>
                    </td>
                    <td style="border:1px solid #999;">-</td>
                    </tr>
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Last Action Month</strong>
                    </td>
                    <td style="border:1px solid #999;">-</td>
                    </tr>
                    `}


                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Main Objection</strong></td><td style="border:1px solid #999;">${slip.objectionename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Sub Objection</strong></td><td style="border:1px solid #999;">${slip.subobjectionename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Amount Involved</strong></td><td style="border:1px solid #999;">${slip.amtinvolved || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Severity</strong></td><td style="border:1px solid #999;"> ${slip.severitycode || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Schema Status</strong></td><td style="border:1px solid #999;">${slip.schemastatus || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Auditee Scheme</strong></td><td style="border:1px solid #999;">${slip.auditeeschemeelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities</strong></td><td style="border:1px solid #999;">${slip.irregularitieselname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Category</strong></td><td style="border:1px solid #999;">${slip.irregularitiescatelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Subcategory</strong></td><td style="border:1px solid #999;">${slip.irregularitiessubcatelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Slip Details</strong></td><td style="border:1px solid #999;">${slip.slipdetails || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Type of Audit</strong></td><td style="border:1px solid #999;">${slip.typeofauditename || '-'}</td></tr>

                  <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Auditor File Upload</strong>
                    </td>
                    <td>
                        ${
                        slip.auditorfileupload
                            ? slip.auditorfileupload
                                .split(',')
                                .map(file => {
                                const parts = file.split('-');
                                const fileName = parts[0] || 'Unnamed file';
                                const filePath = parts[1]
                                    ? `/${parts[1].replace(/\\/g, '/')}` // ? fix: proper public path
                                    : '#';
                                return `<a href="${filePath}" target="_blank" style="text-decoration:none; color:#007bff;">${fileName}</a>`;
                                })
                                .join('<br>')
                            : '-'
                        }
                    </td>
                    </tr>


                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Audit Year</strong></td><td style="border:1px solid #999;">${slip.auditperiod || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Created By</strong></td><td style="border:1px solid #999;">${slip.createdbyusername || '-'}</td></tr>
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Updated On</strong>
                    </td>
                    <td style="border:1px solid #999;">
                        ${
                        slip.updatedon
                            ? (() => {
                                const d = new Date(slip.updatedon);
                                const day = String(d.getDate()).padStart(2, '0');
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                const year = d.getFullYear();
                                return `${day}-${month}-${year}`;
                            })()
                            : '-'
                        }
                    </td>
                    </tr>



                          <tr>
                        <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                            <strong>Liability</strong>
                        </td>
                        <td style="border:1px solid #999;">${slip.liability === 'Y' ? 'Yes' : slip.liability === 'N' ? 'No' : '-'}</td>
                        </tr>


                 ${slip.liability === 'Y' ? `
                        <tr>
                        <td colspan="2" style="padding:0; border:none;">
                            <table class="table table-bordered mt-2 mb-2" style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr style="text-align:center;">
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Type</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Number</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Name</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Designation</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                slip.liabilitydel
                                    ? slip.liabilitydel.split(',').map(line => {
                                        const parts = line.trim().split('-');
                                        return `
                                        <tr>
                                            <td class="text-center" style="border:1px solid #dee2e6;">
                                            ${
                                                parts[0] === '01'
                                                ? 'EPF No'
                                                : parts[0] === '02'
                                                ? 'CPS No'
                                                : parts[0] === '03'
                                                ? 'IFHRMS No'
                                                : (parts[0] || '-')
                                            }
                                            </td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[1] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[2] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[3] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6; text-align:right;">${parts[4] || '-'}</td>
                                        </tr>`;
                                    }).join('')
                                    : `<tr><td colspan="5" style="text-align:center; border:1px solid #dee2e6;">No liability records available</td></tr>`
                                }
                            </tbody>
                            </table>
                        </td>
                        </tr>
                        ` : ''}



                `);

                let remarksContent = '';

                    try {
                        if (slip.remarks) {
                            const parsed = JSON.parse(slip.remarks);
                            remarksContent = parsed.content || '';
                        }
                    } catch (e) {
                        // Not JSON, use raw text
                        remarksContent = slip.remarks;
                    }

                    loadckeditorauditor(remarksContent, 'remarksEditor');



                // Show Modal
                var modal = new bootstrap.Modal(document.getElementById('slipDetailsModal'));
                modal.show();
            } else {
                alert('No institution data found');
            }
        },
        error: function (xhr) {
            console.error('Error fetching institution data:', xhr.responseText);
        }
    });
});


function loadckeditorauditor(auditorreply, textareaId) {
    let viewslip_auditorremarks;

    // Destroy the existing CKEditor instance if it exists
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



$(document).on('click', '.show-institution', function () {
    const regioncode = $(this).data('region');
    const distcode = $(this).data('distcode');
    const deptcode = $(this).data('deptcode');
    const districtName = $(this).data('district');
    const departmentName = $(this).data('department');
    const regioneName = $(this).data('regionname'); // ? Make sure this is in your button
    const audityearcode = currentAuditYear;
    const statusflag = currentstatusflag;




    $.ajax({
        url: '/fetch_institutionwise',
        type: 'POST',
        data: { regioncode, deptcode, distcode, audityearcode, statusflag },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            console.log('Institution data:', response.data);
            if (response.status === 'success' && response.data.length > 0) {
                showInstitutionTable(departmentName,regioneName,districtName, response.data);
            } else {
                passing_alert_value('Info', 'No institution data found', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
            $('#slipdetailsTableContainer').hide();
        },
        error: function (xhr) {
            console.error('Error fetching institution data:', xhr.responseText);
        }
    });
});



function showInstitutionTable(departmentName,regioneName,districtName, data) {
    $('#InstitutionTableContainer').show();
    $('#institutionTitle').text(`Institution-wise details for ${departmentName}, ${regioneName}, ${districtName}`);

    if ($.fn.DataTable.isDataTable('#institutionReportTable')) {
        $('#institutionReportTable').DataTable().clear().destroy();
    }

    $('#institutionReportTable').DataTable({
        data: data,
        processing: true,
        serverSide: false,
        searching: true,
        info: true,
        ordering: false,
        autoWidth: false,
        responsive: true,
        paging: true,        // keep pagination
        lengthChange: false,
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            },
            className: 'text-center'
        },
        { data: 'instename', title: 'Institution', className: 'text-start' },
        {
            data: 'institutioncount',
            title: 'Total Audit Para(s)',
            render: function (data, type, row) {
                if (!data || data <= 0) {
                    return `<button type="button" class="btn btn-secondary btn-sm" disabled>0</button>`;
                }
                return `<button type="button"
                                class="btn btn-success btn-sm show-slipdetails"
                                data-instid="${row.instid}"
                                data-instename="${row.instename}">
                                ${data}
                            </button>`;
            },
            className: 'text-center'
        }
    ],
    footerCallback: function (row, data, start, end, display) {
        let api = this.api();
        let intVal = function (i) {
            if (typeof i === 'string') {
                i = i.replace(/[^0-9.-]+/g, '');
                return i ? parseFloat(i) : 0;
            }
            return typeof i === 'number' ? i : 0;
        };

        // Correct column index = 2
        let total = api
            .column(2, { page: 'current' })
            .data()
            .reduce((a, b) => intVal(a) + intVal(b), 0);

        $(api.column(2).footer()).html('<strong>' + total + '</strong>');
    }
});


    // Optional close button handler
    $('#closeInstitutionTable').off('click').on('click', function () {
        $('#InstitutionTableContainer').hide();
    });
}




$(document).on('click', '.show-district', function () {
    const regioncode = $(this).data('region');
    const regionname = $(this).data('regionname');
    const deptcode = $(this).data('deptcode');
    const audityearcode = currentAuditYear;
    const statusflag = currentstatusflag;



    $.ajax({
        url: '/fetch_districtwise',
        type: 'POST',
        data: {
            regioncode: regioncode,
            deptcode: deptcode,
            audityearcode : audityearcode,
            statusflag : statusflag

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                showDistrictTable(regionname, response.data);
            } else {
                passing_alert_value('Info', 'No district data found', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
            $('#InstitutionTableContainer').hide();
            $('#slipdetailsTableContainer').hide();
        },
        error: function (xhr) {
            console.error('Error fetching district data:', xhr.responseText);
            passing_alert_value('Error', 'Failed to fetch district data', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
        }
    });
});


function showDistrictTable(regionname, data) {
    $('#districtTableContainer').show();
    $('#districtTableTitle').text(`District-wise details for ${regionname}`);

    if ($.fn.DataTable.isDataTable('#districtReportTable')) {
        $('#districtReportTable').DataTable().clear().destroy();
    }
    $('#districtReportTable').DataTable({
        data: data,
        paging: false,
        searching: false,
        info: false,
        lengthChange: false,
        ordering: false,
        autoWidth: false,        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: 'text-center'
            },
            {
                data: 'distename',
                title: 'District',
                className: 'text-center'
            },

            {
                data: 'districtcount',
                title: 'Total Audit Para(s)',
                render: function (data, type, row) {
                    if (!data) return '-';
                    return `
                        <button type="button"
                            class="btn btn-success btn-sm show-institution"
                            data-region="${row.regioncode}"
                            data-deptcode="${row.deptcode}"
                            data-distcode="${row.distcode}"
                            data-department="${row.deptesname}"
                            data-district="${row.distename}"
                            data-regionname="${row.regionename}">
                            ${data}
                        </button>
                    `;
                },
                className: 'text-center',
            }


            // {
            //     data: 'districtcount',
            //     title: 'Total Audit Para(s)',
            //     render: function (data, type, row) {
            //         if (!data || data === 0) {
            //             return `<span class="badge bg-secondary">0</span>`;
            //         }

            //         return `<span class="badge bg-success">${data}</span>`;
            //     },
            //     className: 'text-center d-none d-md-table-cell extra-column noExport'
            // }



        ],
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            // Helper to parse numeric values safely
            let intVal = function (i) {
                if (typeof i === 'string') {
                    i = i.replace(/[^0-9.-]+/g, '');
                    return i ? parseFloat(i) : 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            // Calculate total for the 'Count' column (index 2)
            let total = api
                .column(2, { page: 'current' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            // Update footer cell
            $(api.column(2).footer()).html('<strong>' + total + '</strong>');
        }

    });
}





    let dataFromServer;

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function () {

    function updateSelect($select) {
        let selectedValues = $select.val() || [];

        if (selectedValues.includes('A')) {
            // If "All" is selected, keep only A
            $select.val(['A']).trigger('change.select2');
            $select.find('option').not('[value="A"]').prop('disabled', true);

        } else {
            if (selectedValues.includes('')) {
                selectedValues = selectedValues.filter(v => v !== '');
                $select.val(selectedValues).select2();
            }

            $select.find('option').prop('disabled', false);
        }
    }

    function handleAllOption($select) {
        $select.on('change', function () {
            updateSelect($select);
        });

        if (!$select.val() || $select.val().length === 0) {
            $select.val(['']).select2();
        }

        updateSelect($select);
    }

    ['#deptcode','#audityearcode']
        .forEach(function (selector) {
            handleAllOption($(selector));
        });
});





let isRegionLoading = false;

function onchange_region(valuefor, valueforid) {
    if (isRegionLoading) return; // prevent duplicate calls
    isRegionLoading = true;

    const audityear5Dropdown = $('#audityearcode');

    const defaultOption = `
        <option value="" data-name-en="???????? ?????????????????" data-name-ta="???????? ?????????????????">
            ${lang === 'ta' ? '???????? ?????????????????' : 'Select Department'}
        </option>`;

    const $dropdown = $("#" + valueforid);
    $dropdown.empty()
    // return;
    $dropdown.select2('destroy')
    $dropdown.select2(null)
    $dropdown.select2()



    var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();


     var lang = getLanguage();


     if (!Array.isArray(deptcode)) {
        deptcode = deptcode ? [deptcode] : [];
    }

    if (deptcode.length === 0 || deptcode.includes("")) {
        isRegionLoading = false;
        return;
    }


    $.ajax({
        url: '/report/fetch_deptbaseddata',
        type: 'POST',
        data: {
            deptcode: deptcode,
            valuefor: valuefor,
            formname: 'lagacystatus'

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {


            if (response.success && Array.isArray(response.data)) {

                isRegionLoading = false;

                $dropdown.empty();

                if (!audityearcode || audityearcode.length === 0) {
                    audityearcode = [''];
                    }

                //     const placeholders = {
                //     audityear:      { en: "Select Audit Year", ta: "??????? ??????? ?????????????????" },
                // };

                // if (placeholders[valuefor]) {
                //     const text = lang === 'ta' ? placeholders[valuefor].ta : placeholders[valuefor].en;
                //     $dropdown.append(`
                //         <option value="" selected data-name-en="${placeholders[valuefor].en}" data-name-ta="${placeholders[valuefor].ta}">
                //             ${text}
                //         </option>
                //     `);
                // }

                const options = response.data.map(item => {
                    switch (valuefor) {

                        case 'audityear':
                        return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${audityearcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                            : `<option value="${item.auditperiodid}" data-name-en="${item.audit_period}" data-name-ta="${item.audit_period}" ${audityearcode.includes(item.auditperiodid) ? "selected" : ""}>${item.audit_period}</option>`;



                        default:
                            return '';
                    }
                }).join('');

                $dropdown.append(options || '<option value="">No data available</option>');

                if ($dropdown.hasClass('select2-hidden-accessible')) {
                $dropdown.trigger('change.select2');
              }

            } else {
                console.error("Invalid response or no data:", response);
                $dropdown.append('<option value="">No data available</option>');
            }

        },
        error: function(xhr, status, error) {

            var response = JSON.parse(xhr.responseText);

            var errorMessage = response.message ||
                'An unknown error occurred';

            passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');

            console.error('Error details:', xhr, status, error);
        }
    });

}





jsonLoadedPromise.then(() => {

            const language = window.localStorage.getItem('lang') || 'en';

        $.validator.addMethod("selectRequired", function(value, element) {
            let selected = $(element).val();
            return selected && selected.length > 0 && !selected.includes('');
        }, "Please select at least one option.");
        var validator = $("#lagacyreport_form").validate({
            ignore: [],
        rules: {
            "deptcode[]": { selectRequired: true },
            "audityearcode[]": { selectRequired: true },

        },
        messages: {
            "deptcode[]": "Select a Department",
            "audityearcode[]": "Select a Audit Quarter",

        },

        errorPlacement: function(error, element) {
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element);
            }
        }

        });

    $('#buttonaction').on('click', function (event) {
        event.preventDefault();
        var form = $('#lagacyreport_form');
        $('#regionTableContainer').hide();
        $('#districtTableContainer').hide();
        $('#InstitutionTableContainer').hide();
        $('#slipdetailsTableContainer').hide();



        if ($("#lagacyreport_form").valid()) {


        var lang = 'en';
        getInstData(lang);
    } else {

    }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});




$(document).on('click', '.show-region', function () {
    const deptcode = $(this).data('dept');
    const deptname = $(this).data('deptname');
    const audityearcode = $(this).data('auditperiod');
    const statusflag = $(this).data('status');





    $('#districtTableContainer').hide();
    $('#InstitutionTableContainer').hide();
    $('#slipdetailsTableContainer').hide();

    $.ajax({
        url: '/fetch_regionwise',
        type: 'POST',
        data: {
            deptcode: deptcode,
            audityearcode: audityearcode,
            statusflag: statusflag,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                showRegionTable(deptname, response.data);
            } else {
                passing_alert_value('Info', 'No region data found', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
        },
        error: function () {
            passing_alert_value('Error', 'Failed to fetch region data', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
        }
    });
});




    function getInstData(lang) {


        var deptcode =  $('#deptcode').val() || 'A';
        var audityearcode =  $('#audityearcode').val() || 'A';
        $.ajax({
            url: '/fetch_lagacyreport',
            type: 'POST',
            data: {
                deptcode: deptcode,
                audityearcode: audityearcode


            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.data && response.data.length > 0) {

                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = response.data;

                    renderTable(lang);
                } else {

                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                if (xhr.status === 404) {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                console.error('Error details:', xhr, status, error);
            }
        });
    }
    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(
            lang);

    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#lagacyreporttable')) {
            $('#lagacyreporttable').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {


       const regionColumn = language === 'ta' ? 'regionename' : 'regionename';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        if ($.fn.DataTable.isDataTable('#lagacyreporttable')) {
            $('#lagacyreporttable').DataTable().clear().destroy();
        }

        table = $('#lagacyreporttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#lagacyreporttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: 'deptesname',
                    title: columnLabels?.["deptesname"]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row.deptesname || '-';
                    },
                    className: 'text-center text-wrap text-start'
                },
                    {
                        data: "audit_period",
                        title: columnLabels?.["audit_period"]?.[language],
                        render: function(data, type, row) {
                            if (data === 'A') {
                            return 'All';
                        }else{
                            return row?.audit_period || '-';

                        }

                        },
                        className: "text-center d-none d-md-table-cell extra-column text-wrap"
                    },


                {
                    data: 'totalcount',
                    title: columnLabels?.["totalcount"]?.[language],
                    render: function (data, type, row) {
                        if (!data || data === 0) {
                            return `<span class="badge bg-secondary">0</span>`; // gray badge when no data
                        }

                        return `
                            <span class="btn btn-success btn-sm show-region"
                                data-dept="${row.deptcode}"
                                data-deptname="${row.deptesname}"
                                data-auditperiod="${row.audityear}" data-status="ALL">
                                ${data}
                            </span>
                        `;
                    },
                    className: 'text-end d-none d-md-table-cell extra-column'
                },


                {
                    data: 'legacycount',
                    title: columnLabels?.["legacycount"]?.[language],
                    render: function (data, type, row) {
                        if (!data || data === 0) {
                            return `<span class="badge bg-secondary">0</span>`; // gray badge when no data
                        }

                        return `
                            <span class="btn btn-success btn-sm show-region"
                                data-dept="${row.deptcode}"
                                data-deptname="${row.deptesname}"
                                data-auditperiod="${row.audityear}" data-status="F">
                                ${data}
                            </span>
                        `;
                    },
                    className: 'text-end d-none d-md-table-cell extra-column'
                },

                {
                    data: 'entrycount',
                    title: columnLabels?.["entrycount"]?.[language],
                    render: function (data, type, row) {
                        if (!data || data === 0) {
                            return `<span class="badge bg-secondary">0</span>`; // gray badge when no data
                        }

                        return `
                            <span class="btn btn-success btn-sm show-region"
                                data-dept="${row.deptcode}"
                                data-deptname="${row.deptesname}"
                                data-auditperiod="${row.audityear}"
                                data-status="Y">
                                ${data}
                            </span>
                        `;
                    },
                    className: 'text-end d-none d-md-table-cell extra-column'
                }







            ],
            footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            // Function to parse numeric values
            let intVal = function (i) {
                if (typeof i === 'string') {
                    i = i.replace(/[^0-9.-]+/g, '');
                    return i ? parseFloat(i) : 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            // Sum totals
            let dataEnteredTotal = api.column(3, { page: 'current' }).data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let finalizedTotal = api.column(4, { page: 'current' }).data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            // Pending = Data Entered - Finalised
            let pendingTotal = dataEnteredTotal - finalizedTotal;

            // Update footer cells
            $(api.column(3).footer()).html(dataEnteredTotal);
            $(api.column(4).footer()).html(finalizedTotal);
            $(api.column(5).footer()).html('<strong>' + pendingTotal + '</strong>');
        }





        });
           const mobileColumns = [
            "department", "auditCompleted", "reportFinalised",
            "issuedReport"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "lagacyreporttable");
        // console.log("DataTable initialized successfully.");
    }







    $(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        var distcode = '<?php echo $distcode; ?>'
        var regioncode = '<?php echo $regioncode; ?>'
        var deptcode = '<?php echo $deptcode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {
            onchange_region('audityear','audityearcode');
        } else if (sessionrole == regionrole) {
            onchange_region('audityear','audityearcode');

        } else if (sessionrole == headofficerole) {
            onchange_region('audityear','audityearcode');

        } else if (sessionrole == dgarole) {
            $('#deptcode').val('').select2();
            $('#audityearcode').val('').select2();
        } else if (sessionrole == adminrole) {

           // getInstData(lang);
        }


    });








    function reset_form() {
        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#lagacyreport_form')[0].reset();
        $('#audityearcode').val(['']).select2();

        var form = $('#lagacyreport_form');
            form[0].reset();

        // Reset validation messages
        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

            $('#tableshow').hide();
            $('#usertable_wrapper').show();
            $('#no_data').show();


        if (sessionrole == distrole) {
            getInstData(lang);

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
           // onchange_region('district', 'distcode')

        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            onchange_region('audityear','audityearcode');

        } else if (sessionrole == dgarole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            $('#deptcode').val('').select2();



        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


          }

    }
</script>


@endsection
