@extends('index2')

@section('title', 'Spillover Details')

@section('content')

    @include('common.alert')

    {{-- DataTables CSS --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"> --}}
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


    @php
        $encryptedId = Crypt::encryptString($lastauditplanid);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card_header_color">
                    Spillover Details
                </div>

                <div class="card-body position-relative">

                    <div id="loader"
                        class="d-none position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 d-flex justify-content-center align-items-center"
                        style="z-index:10;padding-top: 120px;">

                        <div class="text-center">
                            <div class="spinner-border text-primary"></div>
                            <div class="mt-2">Loading...</div>
                        </div>

                    </div>

                    {{-- Table --}}

                    <div class="table-responsive" id="tableshow" style="display:none;">
                        <table id="spillovertable" class="table table-bordered table-striped w-100 datatables-basic">
                            {{-- class="table w-100 table-striped table-bordered display text-nowrap datatables-basic --}}
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Quarter</th>
                                    <th class="text-center">Main Slip No</th>
                                    <th class="text-center">Title/Categorization</th>

                                    <th class="text-center">Gist</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Severity</th>
                                    <th class="text-center">Liability</th>
                                    <th class="text-center">Status</th>

                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    {{-- No Data --}}
                    <div id="no_data" style="display:none;">
                        <center>No Data Available</center>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="HistoryModel" tabindex="-1" aria-labelledby="HistoryModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#ffffff !important;">
                    <h4 style="text-align:center !important;">Flow of Slip No <b id="slipnodyn"></b></h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div id="pdf-preview" style="width: 100%;">
                        <div class="datatables">
                            <div class="" id="tableshow">
                                <table id="slipHistoryTable"
                                    class="table w-100 table-striped table-bordered display nowrap datatables-basic">
                                    <thead>
                                        <tr>
                                            <th class="lang" key="s_no">S.No</th>
                                            <th>Forwarded By</th>
                                            <th>Forwarded To</th>
                                            <th>Slip Status</th>
                                            <th>Forwarded On</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                        </div>
                        <div id='his_no_data_details' class='hide_this' style="border:1px solid #ddd;padding:10px;">
                            <center>No Data Available</center>
                        </div>
                    </div>
                    <br><br>
                    <input type="text" id="filename" style="display: none;" />
                    <div class="text-center mt-3" style="margin-t">
                        <button id="downloadBtn" class="btn btn-info" style="display: none;">
                            <i class="fas fa-download"></i>&nbsp;&nbsp; Download Report
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="ViewSlipModel" tabindex="-1" aria-labelledby="ViewSlipModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#ffffff !important;">
                    <h4 style="text-align:center !important;font-weight:600;">Slip Details of <span
                            class="slipnodyn"></span></h4>

                    <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- The iframe will be inserted dynamically here -->
                    <div id="pdf-preview" style="width: 100%;">
                        <div>

                            <div class="table-container" id="auditsliptable">
                            </div>
                            <div class="liabilitydetails">
                                <h5>
                                    <center><b>Liability Details</b></center>
                                </h5>
                                <table id="liabilitiesTable" class="auditor-table">
                                    <thead>

                                        <tr>
                                            <th>Name</th>
                                            <th>Details</th>
                                            <th>Designation</th>
                                            <th>Amount Involved</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="auditorremarksdiv" style="display:none;">
                            <br>
                            <div
                                style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                                <h5>
                                    <center><b>Auditor Details</b></center>
                                </h5>
                                <div class="table-container">
                                    <table class="auditor-table table_slip">
                                        <tbody>
                                            <tr>
                                                <th>Auditor Name</th>
                                                <td class="auditorname"></td>
                                            </tr>


                                        </tbody>
                                    </table>
                                    <div class="accordion" id="auditor">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="auditor_head">
                                                <button class="accordion-button bg-primary-subtle   collapsed"
                                                    style="height:20px" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#auditor_acc" aria-expanded="false"
                                                    aria-controls="collapseOne">
                                                    <b>Auditor Observation /Remarks</b>
                                                </button>
                                            </h2>
                                            <div id="auditor_acc" class="accordion-collapse collapse"
                                                aria-labelledby="auditor_head" data-bs-parent="#auditor">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <label class="form-label lang" for="validationDefaultUsername"
                                                                key="observation">Auditor Remarks</label>
                                                            <textarea id="viewslip_auditorremarkscccz" class="form-control" placeholder="Enter remarks"
                                                                name="viewslip_auditorremarks"></textarea>

                                                        </div>
                                                        <!--<div class="col-md-4">

                                                                                                                                                                                                                                                <label class="form-label required"
                                                                                                                                                                                                                                                    for="validationDefaultUsername">Auditor
                                                                                                                                                                                                                                                    Attachment</label>
                                                                                                                                                                                                                                                <div class="container my-1"
                                                                                                                                                                                                                                                    id="viewslip_auditorcontainer"></div>

                                                                                                                                                                                                                                            </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="auditeeremarksdiv" style="display:none;">
                            <br>
                            <div
                                style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                                <h5>
                                    <center><b>Auditee Details</b></center>
                                </h5>
                                <div class="table-container">
                                    <table class="auditor-table table_slip">
                                        <tbody>
                                            <tr>
                                                <th>Auditee Name</th>
                                                <td class="auditeename"></td>
                                            </tr>

                                        </tbody>
                                    </table>

                                    <div class="accordion mt-3" id="auditee">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button bg-primary-subtle collapsed"
                                                    style="height:20px" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    <b>Auditee Reply</b>
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse"
                                                aria-labelledby="headingOne" data-bs-parent="#auditee">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label class="form-label lang"
                                                                for="validationDefaultUsername">Auditee
                                                                Reply</label>

                                                            <textarea id="viewslip_auditeeremarks" class="form-control" placeholder="Enter remarks"
                                                                name="viewslip_auditeeremarks"></textarea>

                                                        </div>
                                                        <!--<div class="col-md-4">
                                                                                                                                                                                                                                    <label class="form-label required"
                                                                                                                                                                                                                                        for="validationDefaultUsername">Auditee
                                                                                                                                                                                                                                        Attachment</label>


                                                                                                                                                                                                                                    <div class="container my-1"
                                                                                                                                                                                                                                        id="viewslip_auditeecontainer"></div>


                                                                                                                                                                                                                                </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="auditorreplydiv" style="display:none;">
                            <br>
                            <div class=""
                                style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                                <h5>
                                    <center><b>Auditor Reply</b></center>
                                </h5>
                                <div class="table-container">
                                    <table class="auditor-table table_slip">
                                        <tbody>
                                            <tr>
                                                <th>Auditor Reply</th>
                                                <td class="auditoreply_remarks"></td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="teamheaddiv" style="display:none;">
                            <br>
                            <div class="teamheaddiv"
                                style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                                <h5>
                                    <center><b>Team Head Details</b></center>
                                </h5>
                                <div class="table-container">
                                    <table class="auditor-table table_slip">
                                        <tbody>
                                            <tr>
                                                <th>Team Head Name</th>
                                                <td class="teamheadname"></td>
                                            </tr>
                                            <tr>
                                                <th>Team Head Final Remarks</th>
                                                <td class="finalremarks"></td>
                                            </tr>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <input type="text" id="filename" style="display: none;" />
                    <!-- Button container with flexbox for centering -->
                    <div class="text-center mt-3" style="margin-t">
                        <button id="downloadBtn" class="btn btn-info" style="display: none;">
                            <i class="fas fa-download"></i>&nbsp;&nbsp; Download Report
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>




    {{-- jQuery --}}
    <script src="{{ asset('assets/js/jquery.js') }}"></script>

    {{-- DataTables JS --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script> --}}

    {{-- <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<script src="../assets/js/datatable/datatable-advanced.init.js"></script> --}}

    <script>
        // let lastauditplanid = "{{ $encryptedId }}";

        $(document).ready(function() {
            loadSpilloverData();
        });

        function loadSpilloverData() {

            // ✅ Show loader
            $('#loader').removeClass('d-none');
            $('#tableshow').hide();
            $('#no_data').hide();

            $.ajax({
                url: "{{ route('getspilloverslipdetails') }}",
                type: "POST",
                data: {
                    lastauditplanid: "{{ $lastauditplanid }}",
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {

                    // ✅ Hide loader
                    $('#loader').addClass('d-none');

                    if (res.data && res.data.length > 0) {
                        $('#tableshow').show();
                        renderTable(res.data);
                    } else {
                        $('#no_data').show();
                    }
                },
                error: function() {

                    // ✅ Hide loader
                    $('#loader').addClass('d-none');

                    $('#no_data').show();
                    alert('Something went wrong');
                }
            });
        }

        function renderTable(data) {

            if ($.fn.DataTable.isDataTable('#spillovertable')) {
                $('#spillovertable').DataTable().destroy();
            }

            table = $('#spillovertable').DataTable({
                data: data,
                processing: true,
                responsive: true,
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1,
                        className: 'text-center'
                    },
                    {
                        data: "planname",
                        className: 'text-center'
                    },
                    {
                        data: "mainslipnumber",
                        className: 'text-center'
                    },
                    {
                        data: null,
                        className: 'text-start',
                        render: function(data, type, row) {
                            return `
            <b>${row.objectionename || '-'}</b><br>
            <small>${row.subobjectionename || '-'}</small>
        `;
                        }
                    },
                    {
                        data: "slipdetails",
                        className: 'text-start'
                    },
                    {
                        data: "amtinvolved",
                        className: 'text-end',
                        render: function(data) {
                            return data ? parseFloat(data).toLocaleString() : '-';
                        }
                    },
                    {
                        data: "severitycode",
                        className: 'text-center',
                        render: function(data) {
                            if (data === 'H') return '<span class="badge bg-danger">High</span>';
                            if (data === 'M') return '<span class="badge bg-warning">Medium</span>';
                            if (data === 'L') return '<span class="badge bg-success">Low</span>';
                            return '-';
                        }
                    },
                    {
                        data: "liability",
                        className: 'text-center',
                        render: function(data) {
                            return data === 'Y' ?
                                '<span class="badge bg-success">Yes</span>' :
                                '<span class="badge bg-danger">No</span>';
                        }
                    },
                    {
                        data: "processcode",
                        className: 'text-center',
                        render: function(data, type, row) {

                            return data === 'A' ?
                                `<span class="badge bg-success">${row.processelname || '-'}</span>` :
                                `<span class="badge bg-danger">${row.processelname || '-'}</span>`;
                        }
                    },

                    {
                        data: "tempslipnumber",
                        className: 'text-center',
                        render: function(data, type, row) {

                            let buttons = '';

                            if (row.processcode !== 'E') {
                                buttons += `
            <button onclick="Open_checkflow_model('${row.auditslipid}','${row.mainslipnumber}')" 
                class="btn btn-sm btn-primary">
                Check Flow
            </button><br>`;
                            }

                            buttons += `
        <button onclick="Open_viewmodel('${row.auditslipid}','${row.mainslipnumber}')" 
            class="btn btn-sm btn-secondary">
            View
        </button>
    `;

                            return buttons;
                        }
                    }
                ]
            });
        }
    </script>

@endsection
