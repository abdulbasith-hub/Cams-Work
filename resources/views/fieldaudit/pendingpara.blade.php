@section('content')
@section('title', ' Audit Report')
@extends('index2')
@include('common.alert')

<style>
    .hidden {
        display: none;
    }

    .list-group-item:nth-child(odd) {
        background-color: white;
    }

    .list-group-item:nth-child(even) {
        background-color: #ebf3fe;
        /* Light grey */
    }

    .list-group-item:hover {
        background-color: #809fff;
        /* Light grey */
    }

    .list-group-item:hover {
        cursor: pointer;
        /* Light grey */
    }

    .card-body {
        padding: 10px 10px;
    }

    .card {
        margin-bottom: 2px;
    }

    #auditteamtable_wrapper {
        overflow: visible;
        /* Ensure the wrapper does not force scrolling */
    }

    #auditteamtable {
        width: 100%;
        /* Ensure the table takes full width of its container */
        table-layout: auto;
        /* Allow automatic adjustment of table layout */
        overflow: visible;
        /* Prevent overflow */
    }

    .largemodal td {
        padding: 12px;
        /* Adds 10px of padding on all sides of each cell */
        border: 1px solid #ddd;


        /* Optional: Add a border for visibility */
    }

    #viewslip_auditorremarks {
        width: 100%;
        height: 300px;
        /* Adjust as needed */
    }

    .card-slip {
        border: 1px solid #ebf1f6 !important;
        /* Light border */
        border-radius: 12px !important;
        /* Rounded corners */
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1) !important;
        /* Soft shadow */
        padding: 10px !important;
        background: white;
    }

    .round-40 {
        width: 50px !important;
        height: 50px !important;
        ;
    }

    .card-title {
        font-size: 22px;
        font-weight: bold;
    }

    #usertable_detail td,
    #usertable_detail th {
        word-wrap: break-word;
        white-space: normal;
    }
</style>
<style>
    .table_slip td {
        width: 60%;
    }

    .table_slip th {
        width: 40%;
    }

    /* General styling for the table */
    .auditor-table {
        width: 100%;
        border-collapse: collapse;
        /* Ensures no gaps between cells */
        font-family: Arial, sans-serif;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Subtle shadow for a lifted effect */
        margin: 20px 0;
        border-radius: 8px;
        background-color: #ffffff;
        /* White background */
        border: 1px solid #e1e1e1;
        /* Light border color */
        font-size: 14px;
    }

    /* Header styling */
    .auditor-table th {
        text-align: left;
        /* Align text to the left */
        background-color: #f2f2f2;
        /* Light gray background */
        color: #333;
        /* Dark text for contrast */
        padding: 12px 15px;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
        /* Adds separator line between rows */
    }

    /* Data cell styling */
    .auditor-table td {
        padding: 12px 15px;
        color: #555;
        /* Lighter gray text color */
        border-bottom: 1px solid #ddd;
        /* Border between rows */

    }

    /* Hover effect for rows */
    .auditor-table tr:hover {
        background-color: #fafafa;
        /* Light gray background when hovering */
    }

    /*.usertable_detail_wrapper {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
        }

        .usertable_detail {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
        }*/
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">



<?php
$instdel = json_decode($results, true);

if ($instdel) {
    $datashow = '';
    $nodatashow = 'hide_this';
} else {
    $datashow = 'hide_this';
    $nodatashow = '';
}

?>



<div class="row">
    <div class="col-12">
        <div id="instdetailslist" class="card card_border">
            <div class="card-header card_header_color">Allocated Institute Details</div>
            <div class="card-body"><br>
                <div class="datatables <?php echo $datashow; ?>">
                    <div class="table-responsive ">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th>Audit Office</th>
                                    <th>Audit From date</th>
                                    <th>Audit To date</th>
                                    <th class="all">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($instdel as $index => $item)
                                    <tr>
                                        <td class="text-end">{{ $index + 1 }}</td> <!-- S.No -->
                                        <td>{{ $item['instename'] }}</td>
                                        <td>{{ $item['formatted_fromdate'] }}</td>
                                        <td>{{ $item['formatted_todate'] }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button id="fieldauditbtn" class="btn btn-sm btn-primary"
                                                onclick="toggleAuditPanel('{{ $item['encrypted_auditscheduleid'] }}', '{{ $item['instename'] }}')">
                                                View
                                                Field Audit</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div id='no_data' class='<?php echo $nodatashow; ?>'>
                                                                                <center>No Data Available</center>
                                                                            </div> -->
            </div>
        </div>

        <br>

        <div class="card card_border  hide_this" id="view_Details">
            <div class="card-header card_header_color">Audit Slip Details of <span class="showinstname"></span>
            </div>
            <div class="card-body">

                <br>
                <div class="cardforslips">
                    <div style="width:80%;margin:0 auto;" class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-file-stack fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_total" class="card-title mb-1">0</h4>
                                            <p class="card-subtitle">Total Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-success">
                                            <i class="ti ti-swipe fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_dropped" class="card-title mb-1">0</h4>
                                            <p class="card-subtitle">Dropped Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-danger">
                                            <i class="ti ti-transform fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_converted" class="card-title mb-1">3</h4>
                                            <p class="card-subtitle">Converted to Paras</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-warning">
                                            <i class="ti ti-clipboard-text fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_pending" class="card-title mb-1">3</h4>
                                            <p class="card-subtitle">Pending Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <!-- <hr style="border-top: var(--bs-border-width) solid #ebf1f6 !important;"> -->
                    <div style="width:50%;margin:0 auto;" class="slipdiv">
                        <br>
                        <input type="hidden" id="instschid_hidden" />
                        <input type="hidden" id="instname" />
                        <div class="row ">
                            <div class="col-md-4 mb-3"> <label class="form-label required"
                                    for="validationDefault01">Quarter</label>
                                <select class="form-select mr-sm-2" id="quartercode" name="quartercode">
                                    <option value='all'>All</option>
                                    <option value="Q1">Quarter 1</option>
                                    <option value="Q2">Quarter 2</option>
                                    <option value="Q3">Quarter 3</option>
                                    <option value="Q4">Quarter 4</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3"> <label class="form-label required"
                                    for="validationDefault01">Select
                                    Slip Status</label>
                                <select class="form-select mr-sm-2" id="slipsts" name="slipsts">
                                    <option value='all'>All</option>
                                    <option value="P">Pending Slips</option>
                                    <option value="A">Dropped Slips</option>
                                    <option value="X">Converted to Paras</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success lang btn-md" key="" type="submit"
                                    action="insert" onclick="filterslip()" id="buttonaction"
                                    style="margin-top: 1.8rem !important;" name="buttonaction">Show Details</button>
                            </div>

                        </div>
                    </div>
                    <div class="datatables">
                        <div class="table-responsive hide_this usertable_detail_wrapper" id="tableshow">
                            <table id="usertable_detail"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th>Slip No</th>
                                        <th>Objection</th>
                                        <th>Team Head</th>
                                        <th>Auditor Name</th>
                                        <th>Slip Created On</th>
                                        <!--<th>Rejoinder</th>-->
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id='no_data_details' class='hide_this'>
                    <center>No Data Available</center>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="HistoryModel" tabindex="-1" aria-labelledby="HistoryModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="text-align:center !important;">Flow of Slip No <b id="slipnodyn"></b></h4>

                <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div class="datatables">
                        <div class="table-responsive" id="tableshow">
                            <table id="slipHistoryTable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
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
                <!-- Button container with flexbox for centering -->
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="ViewSlipModel" tabindex="-1" aria-labelledby="ViewSlipModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
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
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@endsection

<!-- <script>
    function getpendingparadel(auditschedulingid) {
        // Hide previous data table if it exists
        $('#view_Details').removeClass('hide_this');

        // Clear previous DataTable if it exists
        // if ($.fn.DataTable.isDataTable('#usertable_details')) {
        //     $('#usertable_details').DataTable().clear().destroy();
        // }


        alert(auditschedulingid);
        //Initialize DataTable with new data
        var table = $('#usertable_details').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "ajax": {
                "url": "/getpendingparadetails", // Your API route for fetching data
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
                },
                "data": {
                    auditschedulingid: auditschedulingid
                },
                "dataSrc": function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#no_data_details').hide(); // Hide custom "No Data" message
                        return json.data;
                    } else {
                        $('#no_data_details').show(); // Show custom "No Data" message
                        return [];
                    }
                }
            },
            "columns": [{
                    "data": null, // Serial number column
                    "render": function(data, type, row, meta) {
                        return meta.row + 1; // Serial number starts from 1
                    }
                },
                {
                    "data": "objectionename"
                },
                {
                    "data": "subobjectionename"
                },
                {
                    "data": "amtinvolved"
                },
                {
                    "data": "slipdetails"
                },
                {
                    "data": "liability"
                },
                {
                    "data": "auditorremarks"
                },
                {
                    "data": "status"
                }
            ]
        });

    }
</script> -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


<script>
    function filterslip() {
        var quartercode = $('#quartercode').val();
        var slipsts = $('#slipsts').val();
        var filterapply = true;
        var auditscheduleid = $('#instschid_hidden').val();
        var instname = $('#instname').val();



        getpendingparadel(auditscheduleid, quartercode, slipsts, filterapply, instname);
        //alert(slipsts);alert(quartercode);
    }



    function toggleAuditPanel(auditscheduleid, instname) {

        getpendingparadel(auditscheduleid, '', '', '', instname);

    }

    let isFieldAuditBtnClicked = false;

    document.addEventListener("click", (event) => {
        // Check if the click is inside #fieldauditbtn, #view_Details, or the modal popup
        if (
            $(event.target).closest("#fieldauditbtn").length ||
            $(event.target).closest("#view_Details").length ||
            $(event.target).closest(".modal").length ||
            $(event.target).closest('.paginate_button').length
        ) {
            isFieldAuditBtnClicked = true;
        } else {
            isFieldAuditBtnClicked = false;
        }

        // Hide the panel only if the click is outside all defined elements
        if (!isFieldAuditBtnClicked) {
            $('#view_Details').addClass('hide_this'); // Hide the audit details div
            $(".modal").modal("hide"); // Hide any open modal popups
        }

    });

    function getpendingparadel(auditscheduleid, quartercode = '', slipsts = '', filterapply = '', instname = '') {
        // Show the detailed audit view section
        $('#instschid_hidden').val(auditscheduleid);
        $('#instname').val(instname);
        $('.showinstname').html(instname);

        $('#usertable_detail tbody').empty();

        $.ajax({
            url: "/getpendingparadetails",
            type: "POST",
            data: {
                auditscheduleid: auditscheduleid,
                quartercode: quartercode,
                slipsts: slipsts,
                filterapply: filterapply
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(data, textStatus, jqXHR) {


                if (jqXHR.status === 200 && data.success) {
                    if (data.data && data.data) {
                        // Assuming your response contains the counts directly, like this:
                        // Update the counts in the HTML
                        $('#view_Details').removeClass('hide_this');

                        $('#cnt_total').text(0);
                        $('#cnt_dropped').text(0);
                        $('#cnt_converted').text(0);
                        $('#cnt_pending').text(0);

                        var totalSlips = data.data.totalslips; // Example: 10
                        var droppedSlips = data.data.droppedslips; // Example: 4
                        var convertedSlips = data.data.convertedslips; // Example: 3
                        var pendingSlips = data.data.pendingSlips; // Example: 3
                        $('.cardforslips').show();
                        $('#no_data_details').hide()
                        // Update the counts in the HTML
                        $('#cnt_total').text(totalSlips);
                        $('#cnt_dropped').text(droppedSlips);
                        $('#cnt_converted').text(convertedSlips);
                        $('#cnt_pending').text(pendingSlips);

                        // Proceed with your table population logic if needed
                        $('#usertable_detail tbody').empty();

                        // Check if data.data.original.data is an array and has elements
                        if (Array.isArray(data.data.data) && data.data.data.length > 0) {

                            // Show the table if there is data
                            $('#tableshow').removeClass('hide_this');
                            $('#no_data_details').addClass('hide_this');
                            renderTable(data);
                        } else {
                            // If no data is returned, show the "no data" message and hide the table
                            $('#usertable_detail tbody').append(
                                '<tr><td colspan="9" align="center">No Slip Available</td></tr>');
                            $('#tableshow').removeClass('hide_this');
                            $('#no_data_details').addClass('hide_this');
                        }
                    } else if (data.message === "No auditslips found") {
                        // Handle the "No auditslips found" case
                        $('#tableshow').removeClass('hide_this');
                        $('.cardforslips').hide();
                        $('#no_data_details').show();

                        $('#view_Details').removeClass('hide_this');

                        /*$('#usertable_detail tbody').append(
                            '<tr><td colspan="9" align="center">No Slip Available</td></tr>');*/
                    } else {
                        // Handle any other unexpected success response
                        alert("Unexpected response: " + data.message);
                    }
                } else {
                    alert('else');
                }
            },
            error: function(error) {
                console.error("Error fetching data:", error);
            }
        });
    }


    function renderTable(data) {
        var download = 'Download Report';

        // Clear any existing table and destroy it
        if ($.fn.dataTable.isDataTable('#usertable_detail')) {
            $('#usertable_detail').DataTable().clear().destroy();
        }

        // Initialize DataTable with dynamic rows based on the response data
        var table = $('#usertable_detail').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "scrollX": true,
            "autoWidth": false,
            "responsive": true,
           "scrollX":true,
            "destroy": true, // Allow reinitialization
            "data": data.data.data, // Use the data array directly
            dom: 'Bfrtip',
            buttons: [{
                extend: "excel",
                text: download,
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude the last column (adjust index accordingly)
                }
            }],
            "columns": [{
                    "data": "mainslipnumber"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `<p><b>Main Objection: </b>${row.objectionename}</p><p><b>Sub Objection: </b>${row.subobjectionename}</p>`;
                    }
                },
                {
                    "data": "teamheadname"
                },
                {
                    "data": "auditorname"
                },
                {
                    "data": "createddate"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <span class="mb-1 badge text-bg-${row.processcode === 'A' ? 'success' : row.processcode === 'X' ? 'danger' : 'warning'}" style="font-size:11px;">
                                ${row.processcode === 'A' ? 'Dropped Slip' : row.processelname}
                            </span>
                            <span class="mb-1 badge text-bg-${row.auditquartercode === 'Q4' ? 'primary' : row.auditquartercode === 'Q3' ? 'secondary' : row.auditquartercode === 'Q2' ? 'info' : 'secondary'}" style="font-size:11px;">
                                ${row.auditquartercode}
                            </span>`;
                    }
                },
                {
                    "data": null,
                    "className": "noExport", // This column will be excluded from export
                    "render": function(data, type, row) {
                        return `
                            ${row.processcode !== 'E' ?
                                `<button onclick="checkflow_model('${row.auditslipid}','${row.mainslipnumber}')" data-slipid="${row.auditslipid}" type="button" class="btn-sm btn btn-primary"><i class="ti ti-history fs-4 me-2"></i> Check Flow</button><div style="height:5px;"></div>`
                                : ''}
                            <button onclick="viewmodel('${row.auditslipid}','${row.mainslipnumber}')" data-slipid="${row.auditslipid}" type="button" class="btn-sm btn btn-secondary"><i class="ti ti-eye fs-4 me-2"></i>View Details</button>
                        `;
                    }
                }
            ],
            "columnDefs": [{
                    "width": "50px",
                    "targets": 0
                },
                {
                    "width": "400px",
                    "targets": 1
                },
                {
                    "width": "150px",
                    "targets": 2
                },
                {
                    "width": "150px",
                    "targets": 3
                },
                {
                    "width": "150px",
                    "targets": 4
                },
                {
                    "width": "200px",
                    "targets": 5
                },
                {
                    "width": "100px",
                    "targets": 6,
                    "className": "noExport"
                } // Ensure this column is not exported
            ],
            "language": {
                "search": "Search :",
                "info": "Showing _START_ to _END_ of _TOTAL_ records"
            }
        });

        // Adjust columns after DataTable initialization
        table.columns.adjust().draw();

        $(".dt-button").addClass("btn btn-primary lang").text(download);
    }


    function checkflow_model(slipid, mainslipnumber) {
        $('#HistoryModel').modal('show');

        $('#slipnodyn').text(`#${mainslipnumber}`);
        $('#slipHistoryTable').show();

        // Clear previous table content
        $('#slipHistoryTable tbody').empty();
        // $('#no_data_details').hide();
        $('#his_no_data_details').addClass('hide_this');

        $.ajax({
            url: `/get-sliphistory-details/${slipid}`, // Replace with your actual endpoint
            method: 'GET',
            success: function(response) {
                // Populate modal content with fetched data
                if (response.status === 'success' && response.data.length > 0) {
                    // Calculate serial number dynamically in descending order
                    const totalRows = response.data.length;

                    // Populate table rows with response data
                    response.data.forEach((item, index) => {
                        const serialNumber = totalRows -
                            index; // Calculate serial number in descending order
                        const row = `
                                <tr>
                                    <td>${serialNumber}</td> <!-- Serial Number -->
                                    <td>${item.forwardedby_username || 'N/A'}</td>
                                    <td>${item.forwardedto_username || 'N/A'}</td>
                                    <td>${item.processelname || 'N/A'}</td>
                                    <td>${item.forwardedon || 'N/A'}</td>
                                </tr>
                            `;
                        $('#slipHistoryTable tbody').append(row);
                    });
                } else {
                    // Show "No data" message if the response is empty
                    $('#slipHistoryTable').hide();
                    $('#his_no_data_details').removeClass('hide_this');
                }
            },
            error: function(error) {
                // Handle errors
                $('#slipDetailsContent').text('Failed to load slip details.');
            }
        });

    }


    function viewmodel(slipid, mainslipnumber) {
        $('#ViewSlipModel').modal('show');

        var instname = $('.showinstname').html();

        $('.slipnodyn').text(instname);

        $.ajax({
            url: `/get-slip-details/${slipid}`, // Replace with your actual endpoint
            method: 'GET',
            success: function(response) {
                //alert(slipid);
                var data = response.data;
                $('#auditsliptable').empty();
                var appenddata = $('#auditsliptable');
                var liabilitycheck = '';
                data.forEach(function(item, index) {
                    let uniqueTextareaId = "viewslip_auditorremarks_" + index;
                    let sno = index + 1;
                    let dataappend =
                        '<div style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; "><h5><center><b>#History ' +
                        sno +
                        '</b></center></h5><table id="" class="auditor-table table_slip"><tbody><tr><th>Objection Details</th><td><p><b>Main Objection :</b><span class="mainobj">' +
                        item.objectionename +
                        '</span></p><p><b>Sub Objection :</b><span class="subobj">' + item
                        .subobjectionename +
                        '</span></p></td></tr><tr><th>Severity</th><td class="severity">' + item
                        .severity +
                        '</td></tr><tr><th>Slip Details</th><td class="auditslipdetails">' + item
                        .slipdetails + '</td></tr><tr><th>Status</th><td class="auditslipsts">' +
                        item.processelname +
                        '</td></tr></tbody></table><label class="form-label lang" for="validationDefaultUsername" key="observation">Remarks</label><textarea  class="form-control" id="' +
                        uniqueTextareaId + '" placeholder="Enter remarks" ></textarea></div><br>';
                    appenddata.append(dataappend);

                    loadckeditorauditor(item.remarks ? JSON.parse(item.remarks).content : '',
                        uniqueTextareaId);

                    if (index === 0) {
                        liabilitycheck = item.liability;
                    }

                });


                $('.liabilitydetails').hide();
                if (liabilitycheck == 'Yes') {
                    $('.liabilitydetails').show();
                    var liabilitydata = response.liability;


                    let tableBody = $('#liabilitiesTable tbody');
                    tableBody.empty();

                    liabilitydata.forEach(function(item) {

                        if (item.notype == '01') {
                            liabilityLabel = 'GPF No';
                        } else if (item.notype == '02') {
                            liabilityLabel = 'CPF No';
                        } else if (item.notype == '03') {
                            liabilityLabel = 'IFHRMS No';
                        }

                        let row = `<tr>
                                            <td>${item.liabilityname}</td>
                                            <td><b>${liabilityLabel}</b><br> ${item.liabilitygpfno}</td>
                                            <td>${item.liabilitydesignation}</td>
                                            <td>${item.liabilityamount}</td>
                                        </tr>`;
                        tableBody.append(row);
                    });
                }


                /*$('.slipnodyn').text(data.instename);
                $('.slipno').text(data.mainslipnumber);
                $('.mainobj').text(data.objectionename ? data.objectionename : '-');
                $('.subobj').text(data.subobjectionename ? data.subobjectionename : '-');
                $('.amtinvolved').text(data.amtinvolved ? data.amtinvolved : '-');
                $('.severity').text(data.severity ? data.severity : '-');
                $('.auditeename').text(data.username ? data.username : '-');
                $('.liabilitysts').text(data.liability ? data.liability : '-');
                $('.auditslipdetails').text(data.slipdetails ? data.slipdetails : '-');
                $('.auditslipsts').text(data.processelname);*/

                /*if (data.memberrejoinderremarks !== null) {
                    $('.auditorreplydiv').show();
                    $('.auditoreply_remarks').text(data.memberrejoinderremarks);
                }

                if (data.finalremarks !== null) {
                    $('.teamheaddiv').show();
                    $('.teamheadname').text(response.teamheadname);
                    $('.finalremarks').text(data.finalremarks);

                }*/



                $('.auditorname').text(data.auditorname + ' - ' + data.auditordesig);
                // Check if auditorremarks and auditeeremarks are not null or undefined
                if (response.data.auditorremarks !== null && response.data.auditorremarks !== undefined &&
                    response.data.auditorremarks !== '') {
                    $('.auditorremarksdiv').show();
                    loadckeditorauditor(response.data.auditorremarks ? JSON.parse(response.data
                        .auditorremarks).content : '');
                } else {
                    //loadckeditorauditor('');
                    $('.auditorremarksdiv').hide();

                }

                if (response.data.auditeeremarks !== null && response.data.auditeeremarks !== undefined &&
                    response.data.auditeeremarks !== '') {
                    loadckeditorauditee(response.data.auditeeremarks ? JSON.parse(response.data
                        .auditeeremarks).content : '');
                    $('.auditeeremarksdiv').show();
                } else {
                    //loadckeditorauditee('');
                    $('.auditeeremarksdiv').hide();
                }

                //for attachments
                if (data.auditorfileupload) {
                    var files = getfile(data.auditorfileupload);
                    alert(files);
                    UploadedFileList(files, UploadedFileList_withaction, 'viewslip_auditorcontainer', '',
                        '')
                }
            },
            error: function(error) {
                // Handle errors
                $('#slipDetailsContent').text('Failed to load slip details.');
            }
        });
    }

    // Modified loadckeditorauditor function to accept unique ID

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
                    'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                    'SlashCommand', 'Template',
                    'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                    'CaseChange'
                ]
            }).then(editor => {
                viewslip_auditorremarks = editor;
                window[textareaId] = editor; // Store the instance globally with a unique key
                editor.setData(auditorreply); // Set data (empty if auditorreply is empty)
                window[textareaId].enableReadOnlyMode('initial');
            }).catch(error => {
                console.error("CKEditor Initialization Error:", error);
            });
        } else {
            console.error("Editor element not found.");
        }
    }






    function loadckeditorauditee(auditeereply) {

        let viewslip_auditeeremarks;


        if (window.viewslip_auditeeremarks && typeof window.viewslip_auditeeremarks.destroy === 'function') {
            window.viewslip_auditeeremarks.destroy();
        }

        // Initialize the CKEditor for auditee remarks
        CKEDITOR.ClassicEditor.create(document.getElementById("viewslip_auditeeremarks"), {
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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData',
                'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template',
                'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                'CaseChange'
            ]
        }).then(editor => {
            viewslip_auditeeremarks = editor;
            window.viewslip_auditeeremarks = editor; // Store the instance globally
            editor.setData(auditeereply); // Set data after initialization
            viewslip_auditeeremarks.enableReadOnlyMode('initial');
        }).catch(error => console.error("CKEditor Initialization Error:", error));
    }

    function closebtn() {
        // Get all the accordion button and accordion collapse elements
        const accordionButtons = document.querySelectorAll('.accordion-button');
        const accordionCollapses = document.querySelectorAll('.accordion-collapse');

        // Loop through each accordion-collapse
        accordionCollapses.forEach((collapse, index) => {
            // Check if the collapse has the 'show' class and remove it
            if (collapse.classList.contains('show')) {
                collapse.classList.remove('show');
            }

            // Add the 'collapsed' class to the corresponding accordion button
            if (!accordionButtons[index].classList.contains('collapsed')) {
                accordionButtons[index].classList.add('collapsed');
            }
        });
    }



    function loadckeditorold(auditorreply, auditeereply) {


        let viewslip_auditorremarks, viewslip_auditeeremarks;

        CKEDITOR.ClassicEditor.create(document.getElementById("viewslip_auditorremarks"), {
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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData',
                'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template',
                'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                'CaseChange'
            ]
        }).then(editor => {
            viewslip_auditorremarks = editor;
            editor.setData(auditorreply); // ? Set data after initialization
        }).catch(error => console.error(error));

        CKEDITOR.ClassicEditor.create(document.getElementById("viewslip_auditeeremarks"), {
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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData',
                'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template',
                'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                'CaseChange'
            ]
        }).then(editor => {
            viewslip_auditeeremarks = editor;
            editor.setData(auditeereply); // ? Set data after initialization
            viewslip_auditeeremarks.enableReadOnlyMode('initial');
        }).catch(error => console.error(error));

        alert(auditorreply); // Debugging: Check if data is received
    }
</script>
