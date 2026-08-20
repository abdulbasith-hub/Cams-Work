@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Inspection Query')


@php

    $sessionchargedel = session('charge');
    // print_r($sessionchargedel);
    $sessionuser = session('user');
    // print_r($sessionuser);
    // print_r($sessionchargedel->roletypecode);

    $sessionuserid = $sessionuser->userid;
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $sessiondesig = $sessionchargedel->desigelname;
    $sessionuser = $sessionuser->username;
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

    $schedule = json_decode($scheduledel, true);
    $checkpointdet = json_decode($data, true);

    $auditSchedule_id = $encrypted_auditscheduleid;
    $formsessionid = $formsessionuserid;

    //  $auditteamhead = $sessionchargedel->auditteamhead;

    $schedule_details = $schedule[0];

    //print_r($schedule);

    $instename = $schedule_details['instename'] ?? '';

    $teamheaduserid = $schedule_details['teamhead_userid'];
    if ($teamheaduserid == $sessionuserid) {
        $auditteamhead = 'Y';
    } else {
        $auditteamhead = '';
    }

    $rejoinderlimit = $schedule_details['inspectionrejoinderlimit'] ?? '';

    $sessionuserdetails = $sessionuser . ' (' . $sessiondesig . ') :';
    // print_r($checkpointdet);
    //  print_r($formsessionid);
@endphp



<style>
    .wizard-content .wizard.wizard-circle>.steps>ul>li::before,
    .wizard-content .wizard.wizard-circle>.steps>ul>li::after {
        top: 25px;
    }


    .card-body {
        padding: 1px 10px;
    }

    .wizard-content .wizard.wizard-circle>.steps>ul>li.current::after,
    .wizard-content .wizard.wizard-circle>.steps>ul>li.current~li::before {
        background-color: #b6dbff;
        top: 25px;
    }

    .wizard-content .wizard>.steps>ul>li a {
        margin-top: 0px;
    }

    /* .wizard-content .wizard.wizard-circle>.steps>ul>li.current~li::before {
        background-color: #b6dbff;
        top: 25px;
    } */

    .hide-finish {
        display: none !important;
    }

    .auditor_bg {
        background-color: #b5cafb !important;
    }

    .parent_bg {
        background-color: #f5c6c6 !important;
    }

    .ck-label.ck-voice-label {
        display: none !important;
    }

    .ck .ck-powered-by__label {
        display: none !important;
    }

    .ck-editor__editable {
        max-height: 300px;
        /* Set the max height as per your requirement */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .ck-editor__editable[role="textbox"] {
        min-height: 200px;
    }

    .ck-editor__editable {
        font-family: 'Marutham', sans-serif;
    }

    .checklist_head {
        background-color: #c2c2c2;
    }

    .checklist-table {
        border: 2px solid #a8b2bb;
        border-collapse: collapse;
    }

    .checklist-table th,
    .checklist-table td {
        border: 1px solid #a8b2bb !important;
        padding: 10px;
        vertical-align: middle;
    }

    .table-responsive {
        background-color: #fdfdfd;
        padding: 20px;
        border-radius: 8px;
    }



    .card_dark {
        border: 1px solidrgb(81, 110, 134);
    }

    /* .hide-finish {
        display: none !important;
    } */

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
    .work-types-container {
        max-height: 250px;
        overflow-y: auto;
        padding: 8px;
        border-radius: 6px;
        background-color: #f8f9fa;
    }

    .work-type-item {
        padding: 6px 10px;
        margin-bottom: 5px;
        background-color: white;
        border-left: 3px solid #0d6efd;
        border-radius: 4px;
        font-size: 0.9rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .work-type-item:last-child {
        margin-bottom: 0;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0d6efd;
    }

    .badge {
        font-size: 0.75em;
    }


    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .modal-header.work-allocation-modal-header {
        background-color: #3782ce;
    }

    .modal-title.work-allocation-modal-title {
        font-weight: 600;
        color: white;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        position: relative;
        background: transparent;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link:hover {
        border: none;
        color: #0d6efd;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
        border: 2px solid #e3f2fd;
        border-bottom: 2px solid white;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        box-shadow: 0 -2px 10px rgba(13, 110, 253, 0.1);
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #0d6efd, #0dcaf0);
    }

    .nav-tabs .badge {
        font-size: 0.6em;
        padding: 0.25em 0.5em;
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
        border: 1px solidrgb(90, 12, 12);
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
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<div class="card card_border">
    <div class="card-header card_header_color position-relative text-center lang" key="inspection_header">Inspection Audit
        Details
    </div>
    <div class="card-body">
        <div class="card card_border">
            <div class="card-header  position-relative text-center">
                <h6 class="mb-0 text-dark lang" key="institute_detail"><b>Institution Details</b></h6>
		
		<button type="button"
                    class="btn btn-primary position-absolute top-50 translate-middle-y me-2 btn  align-items-center justify-content-center"
                    style="right: 60px;" id="auditscheduleid" value="{{ $encrypted_auditscheduleid ?? '' }}"
                    data-instid="{{ $schedule_details['encrypted_instid'] ?? '' }}"
                    data-teamhead_userid="{{ $schedule_details['encrypted_teamhead_userid'] ?? '' }}"
                    data-team_members_userids="{{ $schedule_details['encrypted_team_members_userids'] ?? '' }}"
                    data-schteammemberid="{{ $schedule_details['encrypted_schteammemberid'] ?? '' }}">
                    Work Allocation & Diary Details
                </button>

                <button type="button" onclick="redirectToViewpage()"
                    class="position-absolute end-0 top-50 translate-middle-y me-2 btn  align-items-center justify-content-center">
                    <i class="fs-5 ti ti-home text-dark"></i>
                </button>
            </div>


            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="d-flex flex-wrap">
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="inst_name">Institution Name:</strong>
                                <div>{{ $scheduledel->first()->instename ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="category">Category:</strong>
                                <div>{{ $scheduledel->first()->catename ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="sub_head">Sub Category:</strong>
                                <div>{{ $scheduledel->first()->subcatename ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="proposed_date">Proposed Date:</strong>
                                <div>
                                    @if (isset($scheduledel->first()->fromdate, $scheduledel->first()->todate))
                                        {{ \Carbon\Carbon::parse($scheduledel->first()->fromdate)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($scheduledel->first()->todate)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="entrymeeting_label">Entry Meeting:</strong>
                                <div>
                                    {{ $scheduledel->first()->entrymeetdate ? \Carbon\Carbon::parse($scheduledel->first()->entrymeetdate)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="auditquarter">Audit Quarter:</strong>
                                <div>{{ $scheduledel->first()->auditquarter ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="mandays">Mandays:</strong>
                                <div>{{ $scheduledel->first()->mandays ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="teamheadname">Team Head: </strong>
                                <div>{{ $scheduledel->first()->team_head_en ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="lang" key="teammembername">Team Members: </strong>
                                <div>{{ $scheduledel->first()->team_members_en ?? '-' }}</div>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" class="form-control" value="{{ $encrypted_auditscheduleid ?? '' }}"
                        id="auditscheduleid" name="auditscheduleid" disabled />


                </div>

            </div>
        </div>
        <div class="card  card_border mt-2">
            <div class="card-body" id="loader">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <div class="card-body wizard-content hide_this">

                <div action="#" class="validation-wizard wizard-circle ">
                    <input type="hidden" class="form-control" id="auditeereponseget" name="auditeereponseget">
                    <input type="hidden" class="form-control" id="auditscheduleid" name="auditscheduleid">
                    <input type="hidden" id="fromquarter" />
                    <input type="hidden" id="toquarter" />
                    <!-- Step 1 -->
                    <h6><span class="lang" key="view_audit_sts">View Audit Status</span></h6>
                    <section>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header card_header_color lang" key="auditslipdetails">Audit Slip
                                    Details</div>
                                <div class="card-body">
                                    <div class="datatables"
                                        style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                        <div class="table-responsive hide_this usertable_detail_wrapper" id="tableshow">
                                            <table id="usertable_detail"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="slip_no">Slip No</th>
                                                        <th class="lang" key="objection">Objection</th>
                                                        <th class="lang" key="teamheadname">Team Head</th>
                                                        <th class="lang" key="slipcreatedby">Slip Created By</th>
                                                        <th class="lang" key="slipcreated">Slip Created On</th>
                                                        <!--<th>Rejoinder</th>-->
                                                        <th class="lang" key="slipsts_head">Slip Status</th>
                                                        <th class="lang" key="action">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <div id='no_data_details' class='hide_this'>
                                        <center>No Data Available</center>
                                    </div>
                                    <form id="slip_form" name="slip_form" method="post">
                                        <input type="hidden" class="form-control"
                                            value="{{ $encrypted_auditscheduleid ?? '' }}" id="slip_auditscheduleid"
                                            name="slip_auditscheduleid" />
                                        <input type="hidden" class="form-control"
                                            value="{{ $auditteamhead ?? '' }}" id="auditteamhead"
                                            name="auditteamhead" />
                                        <input type="hidden" class="form-control" value=""
                                            id="slip_auditinspectionid" name="slip_auditinspectionid" />
                                        <input type="hidden" class="form-control"
                                            value="{{ $scheduledel->first()->encrypted_planid ?? '-' }} "
                                            id="slip_auditplanid" name="slip_auditplanid" />
                                        <div id="slipGroupedContainer"></div>

                                        <div id="slipremarksaccordion"></div>
                                        <div class="col-md-12 p-3" id="slipremarks_div">
                                            <label class="form-label required lang" for="validationDefaultUsername"
                                                key="cmnts_slip" id="remarks_slip_label">Remarks on Audit Slip</label>
                                            <textarea id="slipremarks" class="form-control" placeholder="Enter remarks" name="slipremarks"></textarea>
                                        </div>
                                    </form>

                                    <div class="hide_this" id="cont_btn">
                                        <div class="col-md-8" id="check_msg">
                                            <div class="form-check ">
                                                <input class="form-check-input border-dark" type="checkbox"
                                                    name="exampleRadios" id="usercheck" onclick="togglecheck(this)"
                                                    value="option1">
                                                <label class="form-check-label lang" for="usercheck" key="">
                                                    <strong class="lang" key="verifyslipmsg">This is to confirm that
                                                        the audit slip(s) have been verified
                                                        by me. All necessary findings/improvements have been mentioned
                                                        in the Remarks.</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mx-auto" id="continue_div">
                                            <button type="button" id="check_status" key="cont_btn_label"
                                                class="justify-content-center w-100 btn mb-1 btn-rounded btn-success  align-items-center lang">
                                                Continue
                                            </button>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </section>
                    <!-- Step 2 -->
                    <h6><span class="lang" key="audit_ins_pts">Inspection Points</span></h6>
                    <section>
                        <div class="card   card_dark">
                            <div class="card-header card_header_color lang" key="audit_ins_pts">
                                Audit Inspection Points
                            </div>
                            <div class="card-body">
                                <div class="div hide_this" id="inspect_pts">
                                    <center>No Inspection Points Available</center>

                                </div>
                                <form class="fomr-control" method="post" id="inspectionform" name="inspectionform">
                                    <input type="hidden" class="form-control"
                                        value="{{ $encrypted_auditscheduleid ?? '' }}" id="auditscheduleid"
                                        name="auditscheduleid" />
                                    <input type="hidden" class="form-control" value="" id="forwardedby"
                                        name="forwardedby" />
                                    <input type="hidden" class="form-control" value="" id="auditinspectionid"
                                        name="auditinspectionid" />
                                    <input type="hidden" class="form-control"
                                        value="{{ $scheduledel->first()->encrypted_planid ?? '-' }} "
                                        id="auditplanid" name="auditplanid" />
                                    <input type="hidden" class="form-control" value="" id="rejoindercycle"
                                        name="rejoindercycle" />
                                    <input type="hidden" class="form-control" value="{{ $auditteamhead ?? '' }}"
                                        id="auditteamhead" name="auditteamhead" />
                                    <div id="auditorGroupedContainer"></div>
                                    <div id="auditorAccordionsContainer"></div>
                                    <div class="div" id="ins_label">
                                        <label class="form-label p-2 lang" for="validationDefaultUsername"
                                            key="" id="ins_label"><?php echo $sessionuserdetails; ?></label>
                                    </div>
                                    <div class="card checklist card_border" id="checklist_div">

                                    </div>

                                    <div class="col-md-12 p-3" id="auditorsremarks_div">
                                        <label class="form-label  lang" for="validationDefaultUsername"
                                            key="general_remarks" id="auditorremarks_label">General Remarks</label>
                                        <textarea id="auditorremarks" class="form-control" placeholder="Enter remarks" name="auditorremarks"></textarea>
                                    </div>
                                    <div class="row justify-content-center text-center" id="buttonset">
                                        <div class="col-md-6">
                                            <input type="hidden" name="action" id="action" value="insert" />

                                            <button class="btn button_save mt-3 lang" key="" type="submit"
                                                action="insert" id="buttonaction" name="buttonaction">Save Draft
                                            </button>
                                            <button class="btn bg-success button_finalise lang mt-3" key="submit"
                                                type="submit" id="finalisebtn" action="finalise">
                                                Submit
                                            </button>
                                            <button class="btn bg-success button_finalise lang mt-3 hide_this"
                                                key="rejoinder" type="submit" id="rejoinderbtn" action="finalise">
                                                Rejoinder
                                            </button>
                                            <button
                                                class="btn bg-primary button_finalise lang mt-3 hide_this text-light"
                                                key="final_btn" type="submit" id="completebtn" action="complete">
                                                Finalize
                                            </button>

                                            {{-- <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                                id="reset_button">Clear</button> --}}
                                        </div>

                                    </div>
                                </form>




                            </div>
                        </div>
                    </section>
                    <!-- Step 3 -->
                    {{-- <h6><span class="lang inspection_div" key="">Inspection </span></h6>
                    <section class="inspection_div">
                        <div class="card  card_dark">
                            <div class="card-header card_header_color">

                            </div>
                            <div class="card-body">
                                <div class="" id="comp_btn">
                                    <div class="col-md-12">
                                        <div class="col-md-12 p-3" id="auditorsremarks_div">
                                            <label class="form-label required lang" for="validationDefaultUsername"
                                                key="">Remarks</label>
                                            <textarea id="inspectionremarks" class="form-control" placeholder="Enter remarks" name="inspectionremarks"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-8 p-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exampleRadios"
                                                id="inspectcheck" onclick="toggleinspectcheck(this)" value="option1">
                                            <label class="form-check-label lang" for="usercheck" key="">
                                                <strong>The audit schedule has been reviewed in accordance with the defined
                                                    checkpoints. The inspection was found to be satisfactory.</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mx-auto">
                                        <button type="button" id="inspect_status" key=""
                                            class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center lang">
                                            Approve
                                        </button>
                                    </div>
                                </div>

                                <div class="hide_this" id="comp_status">
                                    <p>Audit Inspection was Completed</p>
                                </div>

                            </div>



                        </div>

                    </section> --}}

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
                        class="slipnodyn"></span><?php echo $instename; ?></h4>

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
                <div class="text-center mt-3" style="margin-top">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
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
<!-- Modal for Work Allocation -->
<div class="modal fade" id="workAllocationModal" tabindex="-1" aria-labelledby="workAllocationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 75%;">
        <div class="modal-content">
            <div class="modal-header work-allocation-modal-header">
                <h5 class="modal-title work-allocation-modal-title" id="workAllocationModalLabel">Work Allocation &
                    Diary Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tabs Section -->
            <div class="modal-body pt-3">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="workAllocationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center" id="allocation-tab"
                            data-bs-toggle="tab" data-bs-target="#allocation-tab-pane" type="button" role="tab"
                            aria-controls="allocation-tab-pane" aria-selected="true">
                            <i class="bi bi-clipboard-check me-2"></i>
                            <span>Work Allocation</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center" id="diary-tab" data-bs-toggle="tab"
                            data-bs-target="#diary-tab-pane" type="button" role="tab"
                            aria-controls="diary-tab-pane" aria-selected="false">
                            <i class="bi bi-journal-text me-2"></i>
                            <span>Diary</span>
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="workAllocationTabsContent">
                    <!-- Work Allocation Tab -->
                    <div class="tab-pane fade show active" id="allocation-tab-pane" role="tabpanel"
                        aria-labelledby="allocation-tab" tabindex="0">
                        <div class="mt-3">


                            <!-- Work Allocation Details in Accordion -->
                            <div class="accordion mt-3" id="workAllocationAccordion">
                                <!-- Accordion items will be dynamically added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Diary Tab -->
                    <div class="tab-pane fade" id="diary-tab-pane" role="tabpanel" aria-labelledby="diary-tab"
                        tabindex="0">
                        <div class="mt-3">
                            <div id="diaryContent">
                                <!-- Diary content will be loaded here -->
                                <p class="text-muted">Diary content will be displayed here.</p>
                            </div>
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


<script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/js/apps/notes.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
{{--
<script src="../assets/js/ckeditor.js"></script> --}}
<script>
    let checkedStatus = 'N';

    let InspectionStatus;

    let checkpointQuestions = <?php echo json_encode($checkpointdet); ?>;

    let teamheadFlag = '<?php echo $auditteamhead; ?>';

    let rejoinderlimit = '<?php echo $rejoinderlimit; ?>';

    let accesscredential;

    let formstatus;

    let inspectprocesscode;

    function redirectToViewpage() {
        window.location.href = '/inspectview';
    }


    $(document).ready(function() {

        var auditscheduleid = "<?php echo $auditSchedule_id; ?>";
        var filterapply = false;

        if (teamheadFlag === "Y") {
            $('.inspection_div').hide();
        }
        getpendingparadel(auditscheduleid, 'all', filterapply);

        const lang = getLanguage('');
    });

    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(lang);
        switchChecklistLanguage(lang)
    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#usertable_detail')) {
            $('#usertable_detail').DataTable().clear().destroy();
        }
        renderTable(language);
    }
    $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');

    var form = $(".validation-wizard").show();

    $(".validation-wizard").steps({
        headerTag: "h6",
        bodyTag: "section",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: "Submit",
        },
        // onInit: function(event, currentIndex) {
        //     if (teamheadFlag === 'Y') {
        //         $("#wizard > h6").eq(2).remove();
        //         $("#wizard > section").eq(2).remove();
        //     }
        // },
        onStepChanging: function(event, currentIndex, newIndex) {
            // if (currentIndex > newIndex) return true;

            // Step 0 to Step 1 logic

            if (currentIndex === 0 && newIndex === 1) {
                var auditteamhead = '<?php echo $auditteamhead; ?>';

                if (checkedStatus == 'Y' && auditteamhead != 'Y') {
                    //fetchchecklist()

                    getinspectionDetails()
                    return true;
                } else if ((checkedStatus == 'N' && auditteamhead == 'Y') || (checkedStatus == '' &&
                        auditteamhead == 'Y')) {

                    getinspectionDetails('Y')
                    $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');
                    return true;
                } else if ((checkedStatus == 'N' && auditteamhead != 'Y') || (checkedStatus == '' &&
                        auditteamhead != 'Y')) {
                    getLabels_jsonlayout([{
                        id: 'fieldauditnotverified',
                        key: 'fieldauditnotverified'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .fieldauditnotverified, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });
                    // Show alert and close modal without executing callback
                    // passing_alert_value('Confirmation', 'Please ensure the listed user details are correct!',
                    //     'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');

                    $("#process_button").off("click").on("click", function(event) {
                        event.preventDefault();
                        $('#confirmation_alert').modal('hide'); // Just close the modal
                    });
                    checkedStatus = 'N';
                    return false;
                } else if ((checkedStatus == 'NA' && auditteamhead == 'Y')) {

                    $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');
                    getinspectionDetails('Y')
                    return true;
                } else if ((checkedStatus == 'NA' && auditteamhead != 'Y')) {
                    $('.actions a[href="#next"]').parent('li').addClass('disabled');

                    getLabels_jsonlayout([{
                        id: 'slipnotavailable',
                        key: 'slipnotavailable'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .slipnotavailable, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });
                    // Show alert and close modal without executing callback
                    // passing_alert_value('Confirmation', 'Please ensure the listed user details are correct!',
                    //     'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');

                    $("#process_button").off("click").on("click", function(event) {
                        event.preventDefault();
                        $('#confirmation_alert').modal('hide'); // Just close the modal
                    });
                    checkedStatus = 'NA';

                    return false;
                }
                // if (auditteamhead === 'Y' && isLastStep) {
                // $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');

                // }

                if (auditteamhead == 'Y') {

                    $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');

                    getinspectionDetails('Y')
                    return true;
                }

                $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');

            }

            // ? Step 2 to Step 3 (last page)   Add your condition here
            if (currentIndex === 1 && newIndex === 2) {


                if (inspectprocesscode == 'H') {
                    $('.actions a[href="#finish"]').parent('li').addClass('hide-finish');
                    return true
                }
                if (inspectprocesscode == 'C') {
                    $('.actions a[href="#finish"]').parent('li').addClass('hide-finish');
                    $('#comp_status').show()
                    $('#comp_btn').hide()

                    return true
                } else {
                    return false;
                }
                // Replace with your condition

            }


            ////////////previous//////////////


            if (currentIndex > newIndex) {
                if (currentIndex === 1 && newIndex === 0) {
                    var auditscheduleid = "<?php echo $auditSchedule_id; ?>";
                    var filterapply = false;
                    getpendingparadel(auditscheduleid, 'all', filterapply);
                    return true;
                }

                if (currentIndex === 2 && newIndex === 1) {
                    return true;
                }
            }



        },


        onFinishing: function(event, currentIndex) {


        },

        onFinished: function(event, currentIndex) {

        }
    });
    /////////////////////view audit status///////////////////


    function filterslip() {
        // var quartercode = $('#quartercode').val();
        var slipsts = $('#slipsts').val();
        var filterapply = true;
        var auditscheduleid = $('#auditscheduleid').val();
        //  var instname = $('#instname').val();



        getpendingparadel(auditscheduleid, slipsts, filterapply);
        //alert(slipsts);alert(quartercode);
    }
    let dataFromServer;

    function getpendingparadel(auditscheduleid, slipsts = '', filterapply = '') {
        // Show the detailed audit view section
        var auditscheduleid = auditscheduleid || $('#auditscheduleid').val(auditscheduleid);
        var auditinspectionid = '<?php echo $encrypted_auditinspectionid; ?>';


        $('#usertable_detail tbody').empty();

        $.ajax({
            url: "/inspection/getpendingparadetails",
            type: "POST",
            data: {
                auditscheduleid: auditscheduleid,
                slipsts: slipsts,
                auditinspectionid: auditinspectionid,
                filterapply: filterapply
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(data, textStatus, jqXHR) {


                //   alert(data.data[auditteamhead]);
                // if (teamheadFlag == 'Y') {
                //     $('#cont_btn').hide();
                //     checkedstatus = 'Y';
                // } else {
                //     checkedstatus = 'N';
                //     $('#cont_btn').show();
                // }

                if (jqXHR.status === 200 && data.success) {
                    formstatus = '<?php echo $formstatus; ?>';
                    if (data.data.length <= 0) {

                        $('#cont_btn').hide();
                        checkedStatus = 'NA';
                        $('.actions a[href="#next"]').parent('li').addClass('disabled');
                        $('#usertable_detail tbody').append(
                            '<tr><td colspan="9" align="center">No Slips Available</td></tr>');
                        // If no data is returned, show the "no data" message and hide the table

                        $('#tableshow').removeClass('hide_this');
                        $('#no_data_details').addClass('hide_this');
                    }
                    //data.data-having slip details
                    else if (data.data.length > 0) {
                        //data.inspectdata-having inspection details
                        if (data.inspectdata.length > 0) {
                            inspectdata = data.inspectdata.find(item => item.activeinspection === 'A');
                            let mergedHistoryData = []
                            if (data.historydata.length > 0) {

                                //filtering completed inspectdetails
                                historydata = data.data.filter(item => item.activeinspection ===
                                    'I');
                                //array of complteded inspectionids
                                const historyInspectionIds = [...new Set(
                                    data.inspectdata
                                    .filter(item => item.inspectprocesscode === 'C' && item
                                        .activeinspection ===
                                        'I')
                                    .map(item => item.transactionno)
                                )];

                                mergedHistoryData = data.historydata.filter(item =>
                                    historyInspectionIds.includes(item.transactionno)
                                );
                                // console.log('Merged History Data:', mergedHistoryData);

                                // Step 3: Call populateGroupedData once
                                if (teamheadFlag != 'Y') {
                                    populateGroupedSlipData('', mergedHistoryData);
                                } else {
                                    if (formstatus == 'new') {
                                        $('#slipremarks_div').hide()
                                        $('.actions a[href="#next"]').show();
                                        $('#cont_btn').css({
                                            display: 'none',
                                            visibility: 'hidden'
                                        });
                                        $('#slipremarksaccordion').hide()
                                        populateGroupedSlipData('', mergedHistoryData);
                                    }

                                }

                            }

                            //merging current active inspection details



                            if (teamheadFlag != 'Y') {
                                $('.actions a[href="#next"]').hide();
                                // getpending
                            }
                            let mergedinspectiondata = [];
                            if (inspectdata && inspectdata.inspectprocesscode) {

                                inspectprocesscode = inspectdata.inspectprocesscode;
                                const activeinspectionIds = [...new Set(
                                    data.inspectdata
                                    .filter(item => item.activeinspection ===
                                        'A')
                                    .map(item => item.transactionno)
                                )];


                                // Step 2: Gather all records from historydata that match those inspection IDs
                                mergedinspectiondata = data.historydata
                                    .filter(item => item && activeinspectionIds.includes(item
                                        ?.transactionno));
                                // console.log('mer' +
                                //     mergedinspectiondata);


                                // var inspectdata = data.inspectdata[0];

                                if (data.inspectdata.inspectprocesscode != 'C') {
                                    if (formstatus != 'new') {
                                        $('#slip_auditinspectionid').val(inspectdata
                                            .encrypted_auditinspectionid)


                                        if (teamheadFlag != 'Y') {

                                            if (inspectprocesscode == 'O') {
                                                slipeditor.setData(inspectdata.slipremarks)
                                            } else if (
                                                inspectprocesscode === 'T' &&
                                                (inspectdata.rejoinderstatus === '' || inspectdata
                                                    .rejoinderstatus === null || inspectdata
                                                    .rejoinderstatus ===
                                                    'N' || inspectdata.rejoinderstatus === 'Y'
                                                )
                                            ) {

                                                populateslipremarks(mergedinspectiondata)

                                                $('#slipremarks_div').hide()
                                                $('.actions a[href="#next"]').show();
                                                $('#continue_div').css({
                                                    display: 'none',
                                                    visibility: 'hidden'
                                                });
                                            }
                                            // else if (inspectprocesscode == 'T' && (inspectdata.rejoinderstatus =
                                            //         'Y')) {

                                            //     populateslipremarks(mergedinspectiondata)

                                            //     $('.actions a[href="#next"]').hide();
                                            //     $('#slipremarks_div').show()
                                            //     slipeditor.setData(inspectdata.slipremarks)
                                            // }
                                            else if (inspectprocesscode == 'C') {
                                                populateslipremarks(mergedinspectiondata)
                                                $('.actions a[href="#next"]').hide();
                                                $('#slipremarks_div').show()
                                                slipeditor.setData(inspectdata.slipremarks)
                                            } else if (inspectprocesscode == 'H') {
                                                populateslipremarks(mergedinspectiondata)
                                                $('.actions a[href="#next"]').show();
                                                $('#slipremarks_div').show()
                                                // Select the label element by its ID
                                                const label = document.getElementById('remarks_slip_label');
                                                label.setAttribute('key', 'cmnt_on_replies');
                                                label.textContent = 'Comment on Replies';


                                                slipeditor.setData('')
                                            } else if (inspectprocesscode == 'E') {
                                                slipeditor.setData(inspectdata.slipremarks)
                                            }

                                            // slipeditor.setData(inspectdata.slipremarks);
                                        } else {
                                            $('.actions a[href="#next"]').hide();
                                            populateslipremarks(mergedinspectiondata)
                                            if (inspectprocesscode == 'H') {
                                                $('#slipremarks_div').hide()
                                                $('.actions a[href="#next"]').show();
                                                $('#continue_div').css({
                                                    display: 'none',
                                                    visibility: 'hidden'
                                                });

                                            } else if (inspectprocesscode == 'T') {
                                                $('#cont_btn').hide();
                                                $('#slipremarks_div').show()

                                                $('#cont_btn').css({
                                                    display: 'none',

                                                });
                                                const label = document.getElementById('remarks_slip_label');
                                                label.setAttribute('key', 'replies_to_ad_remarks');
                                                label.textContent = 'Replies to AD Remarks / Queries';


                                            } else if (inspectprocesscode == 'E') {
                                                const label = document.getElementById('remarks_slip_label');
                                                label.setAttribute('key', 'replies_to_ad_remarks');
                                                label.textContent = 'Replies to AD Remarks / Queries';
                                                slipeditor.setData(inspectdata.slipremarks)

                                            }




                                            // Make CKEditor read-only
                                            // slipeditor.enableReadOnlyMode('readonly-slip');

                                            // // Also disable the original <textarea> (optional)
                                            // document.getElementById('slipremarks').disabled = true;
                                        }
                                    } else {

                                        $('#slip_auditinspectionid').val('')
                                    }
                                } else {
                                    $('#slipremarks_div').hide()
                                    $('.actions a[href="#next"]').show();
                                    $('#continue_div').css({
                                        display: 'none',
                                        visibility: 'hidden'
                                    });
                                }
                            } else {
                                if (formstatus != 'new') {
                                    $('#slipremarks_div').hide()
                                    $('.actions a[href="#next"]').show();
                                    $('#cont_btn').css({
                                        display: 'none',
                                        visibility: 'hidden'
                                    });
                                    $('#slipremarksaccordion').hide()
                                    populateGroupedSlipData('', mergedHistoryData)
                                    checkedStatus = 'Y'
                                } else {
                                    if (teamheadFlag != 'Y') {
                                        populateGroupedSlipData('', mergedHistoryData)
                                        $('#slip_auditinspectionid').val('')
                                    }

                                }
                            }


                        } else {

                            inspectprocesscode = '';

                            $('.actions a[href="#next"]').hide(); // To hide


                        }


                        // if (teamheadFlag === 'Y') {
                        //     accesscredential === 'edit'
                        //     $(".validation-wizard").steps("next");
                        // } else {
                        //     const sameinspectionperson = data.data[0].inspectprocesscode != null ?
                        //         data.data[0].initiatedId === '<?php echo $sessionuserid; ?>' :
                        //         true;
                        //     accesscredential = sameinspectionperson ? 'edit' : 'view';
                        // }

                        if (teamheadFlag === 'Y') {

                            $('#cont_btn').show();
                            $('#check_msg').hide();

                            $('#continue_div').show();

                            //   $(".validation-wizard").steps("next");
                            //   getinspectionDetails();
                        }


                        // if (accesscredential == 'view') {

                        //     checkedStatus = 'Y';
                        //     $('#cont_btn').hide();
                        //     $(".validation-wizard").steps("next");
                        //     getinspectionDetails();
                        // } else {

                        // }
                        // alert(inspectdata.inspectprocesscode)
                        // inspectprocesscode = inspectdata.inspectprocesscode;
                        if (inspectprocesscode == 'C') {

                            $('#cont_btn').show();
                            checkedStatus = 'N';

                            // $('#cont_btn').hide();
                            // checkedStatus = 'Y';
                        } else if (

                            inspectprocesscode != null &&
                            inspectprocesscode !== '' &&
                            inspectprocesscode !== 'C' &&
                            teamheadFlag !== 'Y'
                        ) {

                            const checkbox = document.getElementById("usercheck");

                            checkbox.checked = true;
                            $('#usercheck').prop('disabled', true);
                            togglecheck(checkbox);
                        }


                        $('#view_Details').removeClass('hide_this');

                        $('.cardforslips').show();
                        $('#no_data_details').hide()
                        // Update the counts in the HTML


                        // Proceed with your table population logic if needed
                        $('#usertable_detail tbody').empty();

                        // Check if data.data.original.data is an array and has elements
                        if (Array.isArray(data.data) && data.data.length > 0) {
                            if (teamheadFlag != 'Y' && inspectprocesscode != 'C') {
                                $('#cont_btn').show();
                            }

                            // Show the table if there is data
                            $('#tableshow').removeClass('hide_this');
                            $('#no_data_details').addClass('hide_this');
                            dataFromServer = data.data


                            renderTable(data);
                        } else {

                            $('#cont_btn').hide();
                            checkedStatus = 'NA';
                            $('.actions a[href="#next"]').parent('li').addClass('disabled');
                            $('#usertable_detail tbody').append(
                                '<tr><td colspan="9" align="center">No Slip Available</td></tr>');
                            // If no data is returned, show the "no data" message and hide the table

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
                        $('#tableshow').removeClass('hide_this');
                        $('.cardforslips').hide();
                        $('#no_data_details').show();

                        $('#view_Details').removeClass('hide_this');

                    }
                } else {

                }

            },
            complete: function() {


                $('#loader').addClass('d-none');
                $('.wizard-content').show();
            },
            error: function(error) {
                $('#loader').addClass('d-none');
                $('.wizard-content').show();
                console.error("Error fetching data:", error);
            }
        });
    }

    function renderTable(language) {
        const headName = language === 'ta' ? 'teamheadname_ta' : 'teamheadname_en';
        const auditorName = language === 'ta' ? 'auditorname_ta' : 'auditorname_en';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#usertable_detail')) {
            $('#usertable_detail').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#usertable_detail').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#usertable_detail").wrap(
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
                    data: "mainslipnumber",
                    title: columnLabels?.['mainslipnumber']?.[language] || "Slip Number ",
                    render: function(data, type, row) {
                        return row.mainslipnumber || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: headName,
                    title: columnLabels?.[headName]?.[language] || "Team Head",
                    render: function(data, type, row) {
                        return row?.[headName] || '-';
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: auditorName,
                    title: columnLabels?.[auditorName]?.[language] || 'Slip Issued By',
                    render: function(data, type, row) {
                        return row?.[auditorName] || '-';

                        // return row[teamname] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'createddate',
                    title: columnLabels?.['createddate']?.[language] || "Slip Issued On",
                    render: function(data, type, row) {
                        return row.createddate || '-';
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.['entrymeetdate']?.[language] || "Slip Status",
                    render: function(data, type, row) {
                        return `
                            <span class="mb-1 badge text-bg-${row.processcode === 'A' ? 'success' : row.processcode === 'X' ? 'danger' : 'warning'}" style="font-size:13px;">
                                ${row.processcode === 'A' ? 'Dropped Slip' : row.processelname}
                            </span> `;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.['exitmeetdate']?.[language] || "Action",
                    render: function(data, type, row) {
                        return `
<div class="text-center">
        ${row.processcode !== 'E' ? `
            <button
                onclick="checkflow_model('${row.auditslipid}','${row.mainslipnumber}')"
                data-slipid="${row.auditslipid}"
                type="button"
                class="btn-sm btn btn-primary mx-1"
                data-bs-toggle="tooltip"
                title="Check Flow"
            >
                <i class="ti ti-history fs-4 me-2"></i>
            </button>` : ''}

        <button
            onclick="viewmodel('${row.auditslipid}','${row.mainslipnumber}')"
            data-slipid="${row.auditslipid}"
            type="button"
            class="btn-sm btn btn-secondary mx-1"
            data-bs-toggle="tooltip"
            title="View Details"
        >
            <i class="ti ti-eye fs-4 me-2"></i>
        </button></div>

`;

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },


            ]
        });

        // Mobile column handling
        // const mobileColumns = [
        //     categoryColumn, subcategoryColumn, groupColumn,
        //     callforrecColumn, majorworkColumn,
        //     subworkColumn, mainobjColumn, subobjColumn
        // ];
        // setupMobileRowToggle(mobileColumns);
        // updatedatatable(language, "mappallocationobj_table");

        // console.log("DataTable initialized successfully.");
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
            url: "/inspection/getsliphistorydetails",
            type: "POST",
            data: {
                slipid: slipid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(response, textStatus, jqXHR) {

                if (response.data.length > 0) {

                    var data = response.data;
                    // Calculate serial number dynamically in descending order
                    const totalRows = response.data.length;

                    // Populate table rows with response data
                    data.forEach((item, index) => {
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
                console.error("Error fetching data:", error);
            }
        });


    }

    function viewmodel(slipid, mainslipnumber) {
        $('#ViewSlipModel').modal('show');



        $.ajax({
            url: "/inspection/getslipdetails",
            type: "POST",
            data: {
                slipid: slipid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(response, textStatus, jqXHR) {

                var data = response.data;
                $('#auditsliptable').empty();
                var appenddata = $('#auditsliptable');
                var liabilitycheck = '';
                data.forEach(function(item, index) {
                    let uniqueTextareaId = "viewslip_auditorremarks_" + index;
                    let sno = index + 1;

                    // Set values based on forwardedbyusertypecode
                    let severity = item.severity;
                    let slipdetails = item.slipdetails;
                    let objectionename = item.objectionename;
                    let subobjectionename = item.subobjectionename;

                    if (item.forwardedbyusertypecode === 'I' && index > 0) {
                        // Use previous index data for severity and slipdetails
                        severity = data[index - 1].severity;
                        slipdetails = data[index - 1].slipdetails;
                        objectionename = data[index - 1].objectionename;
                        subobjectionename = data[index - 1].subobjectionename
                    }
                    let issuedBy = '';
                    if (item.forwardedbyusertypecode === 'A') {
                        if (item.auditteamhead === 'Y') {
                            issuedBy = item.username + ' (Team Head)';
                        } else {
                            issuedBy = item.username + ' (Team Member)';
                        }
                    } else if (item.forwardedbyusertypecode === 'I') {

                        issuedBy = 'Auditee';
                    }

                    let dataappend =
                        '<div style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">' +
                        '<h5><center><b>#Slip Details ' + sno + '</b></center></h5>' +
                        '<table class="auditor-table table_slip"><tbody>' +
                        '<tr><th>Objection Details</th><td>' +
                        '<p><b>Main Objection :</b> <span class="mainobj">' + objectionename +
                        '</span></p>' +
                        '<p><b>Sub Objection :</b> <span class="subobj">' + subobjectionename +
                        '</span></p></td></tr>' +
                        '<tr><th>Severity</th><td class="severity">' + severity + '</td></tr>' +
                        '<tr><th>Slip Details</th><td class="auditslipdetails">' + slipdetails +
                        '</td></tr>' +

                        '<tr><th>Issued By</th><td class="auditslipsts">' + issuedBy +
                        '</td></tr>' +
                        '<tr><th>Issued On</th><td class="auditslipsts">' + ChangeDateFormat(item
                            .forwardedon) +
                        '</td></tr>' +
                        '<tr><th>Status</th><td class="auditslipsts">' + item.processelname +
                        '</td></tr>' +
                        '</tbody></table>' +
                        '<label class="form-label lang" for="validationDefaultUsername" key="observation">Remarks</label>' +
                        '<textarea class="form-control" id="' + uniqueTextareaId +
                        '" placeholder="Enter remarks"></textarea>' +
                        '</div><br>';

                    appenddata.append(dataappend);

                    // Load CKEditor with existing remarks content if available
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

                    UploadedFileList(files, UploadedFileList_withaction, 'viewslip_auditorcontainer', '',
                        '')
                }
            },
            error: function(error) {
                console.error("Error fetching data:", error);
            }
        });
    }

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
    // $(document).ready(function() {
    //     var auditscheduleid = "<?php echo $auditSchedule_id; ?>";
    //     var filterapply = false;

    //     getpendingparadel(auditscheduleid, 'all', filterapply);

    //     var auditteamhead = "<?php echo $auditteamhead; ?>";

    //     if (auditteamhead === 'Y') {
    //         // Remove the third step (index 2, 0-based)
    //         $("#validation-wizard").steps("remove", 2);
    //     }

    // });

    $('#check_status').on("click", function(event) {
        event.preventDefault();

        var isChecked = $('#usercheck').prop('checked');
        isChecked = teamheadFlag == 'Y' ? true : $('#usercheck').prop('checked')
        if (isChecked) {

            checkedStatus = 'Y';
            if (teamheadFlag != 'Y') { // Only proceed if the user is NOT a team head
                if (inspectprocesscode == '' || inspectprocesscode == null || inspectprocesscode == 'E') {
                    if (!(slipeditor.getData())) {



                        getLabels_jsonlayout([{
                            id: 'enter_comments',
                            key: 'enter_comments'
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', text
                                .enter_comments, 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'forward_alert');
                        });
                        return;
                    }
                }
            } else {

                if (!(slipeditor.getData())) {
                    getLabels_jsonlayout([{
                        id: 'enter_comments',
                        key: 'enter_comments'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .enter_comments, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });
                    return;
                }
            }


            if ((slipeditor.getData())) {
                const content = slipeditor.getData();
                const plainText = content.replace(/<[^>]*>?/gm, '').trim(); // remove HTML tags

                if (plainText.length < 20) {
                    getLabels_jsonlayout([{
                        id: 'min_characters',
                        key: 'min_characters'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .min_characters, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;
                }

            }
            $('#check_status').prop('disabled', true);
            insertslipremarks()
            // $(".validation-wizard").steps("next");
            //  fetchchecklist();

        } else {
            if (!(slipeditor.getData())) {


                getLabels_jsonlayout([{
                    id: 'enter_comments',
                    key: 'enter_comments'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .enter_comments, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });
                return;
            }
            if ((slipeditor.getData())) {
                const content = slipeditor.getData();
                const plainText = content.replace(/<[^>]*>?/gm, '').trim(); // remove HTML tags

                if (plainText.length < 20) {


                    getLabels_jsonlayout([{
                        id: 'min_characters',
                        key: 'min_characters'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .min_characters, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;
                }

            }
            getLabels_jsonlayout([{
                id: 'fieldauditnotverified',
                key: 'fieldauditnotverified'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .fieldauditnotverified, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            // Show alert and close modal without executing callback
            // passing_alert_value('Confirmation', 'Please ensure the listed user details are correct!',
            //     'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');

            $("#process_button").off("click").on("click", function(event) {
                event.preventDefault();
                $('#confirmation_alert').modal('hide'); // Just close the modal
            });
            checkedStatus = 'N';
        }


        // handleConfirmation(isChecked, 'Are you sure to continue? Once verified cannot be revoked!',
        //     checkfordetails);
    });

    function insertslipremarks() {
        var formuserid = '<?php echo $formsessionid; ?>';
        // var slipremarks = $('#slipremarks').val();
        if (!(slipeditor.getData())) {

            $('#check_status').removeAttr('disabled');
            $(".validation-wizard").steps("next");
            getinspectionDetails()
            return;
        }
        var formData = $('#slip_form').serializeArray();
        formData.push({
            name: 'slipremarks',
            value: slipeditor.getData()
        });
        formData.push({
            name: 'formuserid',
            value: formuserid
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        $.ajax({
            url: '/inspection/insertslipremarks', // URL where the form data will be posted
            type: 'POST',
            data: formData
                // auditscheduleid: auditscheduleid,
                // slipremarks: slipremarks
                ,

            success: function(response) {
                if (response.success) {
                    // reset_form(); // Reset the form after successful submission


                    // get_severity('', 'severityid');
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    $('#comp_status').show()
                    $('#comp_btn').hide()


                    $('#slip_auditinspectionid').val(response.auditinspectionid)
                    $('#auditinspectionid').val(response.auditinspectionid)
                    $(".validation-wizard").steps("next");
                    //  $('#auditinspectionid').val(response.auditinspectionid)




                } else if (response.message) {
                    // Handle errors if needed
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
  		if(response.error=='402')
          	  {
               		  handleUnauthorizedError()
                
           		 }
                // if (response && response.message) {
                //     userMessage = response.message;
                // }
                let msg = xhr.responseJSON?.message || 'Unexpected error occurred.';

                // Displaying the error message
                passing_alert_value('Alert', msg, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired
                $('#check_status').removeAttr('disabled');
            }
        });

    }

    function togglecheck(element) {
        if (element.checked) {

            checkedStatus = 'Y'
        } else {

            checkedStatus = 'N'
        }
    }

    function toggleinspectcheck(element) {
        if (element.checked) {

            InspectionStatus = 'Y'
        } else {

            InspectionStatus = 'N'
        }
    }

    /////////////////////view audit status -END////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////Check List////////////////////////////////////////////////////////
    function fetchchecklist() {
        var auditscheduleid = auditscheduleid || '<?php echo $auditSchedule_id; ?>';
        $.ajax({
            url: "/inspection/getchecklistdetails",
            type: "POST",
            data: {
                auditscheduleid: auditscheduleid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(response, textStatus, jqXHR) {


                renderChecklist(response.data);
            },
            error: function(error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    function switchChecklistLanguage(lang) {
        // Update headings
        $('.heading-cell').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            $(this).text(lang === 'ta' ? ta : en);
        });

        // Update checkpoint text
        $('.checkpoint-cell').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            const prefix = $(this).text().split(')')[0]; // Preserve index number
            $(this).text(`${prefix}) ${lang === 'ta' ? ta : en}`);
        });

        // Update action radio labels
        $('.action-label').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            $(this).text(lang === 'ta' ? ta : en);
        });
    }

    function renderChecklist(data, inspectCheckpoints = [], teamHead = '', processcode = '', answers = '', remarks = '',
        teamHeadRemarks = '', rejoinderstatus = null) {
        // console.log('data:' + data)
        // console.log('checkpoints:' + inspectCheckpoints)
        // Parse inspectCheckpoints and others
        const lang = getLanguage('');
        if (typeof inspectCheckpoints === "string") {
            try {
                inspectCheckpoints = JSON.parse(inspectCheckpoints);
            } catch (e) {
                inspectCheckpoints = {};
            }
        }

        if (typeof answers === "string" || answers == null) {
            try {
                answers = JSON.parse(answers || '{}');
            } catch (e) {
                answers = {};
            }
        }

        if (typeof remarks === "string" || remarks == null) {
            try {
                remarks = JSON.parse(remarks || '{}');
            } catch (e) {
                remarks = {};
            }
        }

        if (typeof teamHeadRemarks === "string" || teamHeadRemarks == null) {
            try {
                teamHeadRemarks = JSON.parse(teamHeadRemarks || '{}');
            } catch (e) {
                teamHeadRemarks = {};
            }
        }

        const isProcessCodeEH = processcode === 'E' || processcode === 'H';
        const isProcessCodeT = processcode === 'T';

        // Use inspectCheckpoints as answers if applicable
        if ((isProcessCodeEH || isProcessCodeT) && typeof inspectCheckpoints === 'object') {
            answers = {
                ...inspectCheckpoints
            };
        }

        const grouped_en = {};
        const grouped_ta = {};
        data.forEach(item => {
            const heading_en = item.heading_en?.toUpperCase() || 'OTHERS';
            if (!grouped_en[heading_en]) grouped_en[heading_en] = [];
            grouped_en[heading_en].push(item);

            const heading_ta = item.heading_ta?.toUpperCase() || 'OTHERS';
            if (!grouped_ta[heading_ta]) grouped_ta[heading_ta] = [];
            grouped_ta[heading_ta].push(item);

        });

        let headingWidth = '10%';
        let checkpointWidth = '25%';
        let actionWidth = '25%';
        let remarksWidth = '20%';
        let teamHeadRemarksWidth = '20%';
        let headingSerial = 1;


        let html =
            ` <div class="table-responsive p-2" style="max-height: 400px; overflow-y: auto;width:98%;margin:0 auto;">
                    <table class="table table-bordered checklist-table w-100">
                    <thead class="text-center">
                    <tr>
                    <th class="lang" key="sl_no" style="background-color: #e6e8ea; width: 5%">S. No</th>
                    <th class="lang" key="heading_label" style="background-color: #e6e8ea; width: ${headingWidth}">Heading</th>
                    <th class="lang" key="ins_checkpt_label" style="background-color: #e6e8ea; width: ${checkpointWidth}">Checkpoint</th>
                    <th class="lang" key="action" style="background-color: #e6e8ea; width: ${actionWidth}">Action</th>
                    <th class="lang" key="remarks_by_desig" style="background-color: #e6e8ea; width: ${remarksWidth}">Remarks by DIR/RJD/AD</th>`;

        if (teamHead === 'Y' || (teamHead != 'Y' && (processcode == 'H' || processcode == 'C'))) {
            html +=
                `<th class="lang" key="remarks_head_label" style="background-color: #e6e8ea; width: ${teamHeadRemarksWidth}">Remarks by Team Head</th>`;
        }


        html += `</tr></thead><tbody>`;

        Object.keys(grouped = lang == 'ta' ? grouped_ta : grouped_en).forEach((head) => {
            const checkpoints = grouped[head];
            const firstItem = checkpoints[0];

            const head_en = (firstItem.heading_en || '').toUpperCase();
            const head_ta = (firstItem.heading_ta || '').toUpperCase();
            const displayHead = lang === 'ta' ? head_ta : head_en;


            checkpoints.forEach((item, index) => {
                const aifid = String(item.aifid);
                const answer = answers[aifid] || '';
                const remark = remarks[aifid] || '';
                const teamRemark = teamHeadRemarks[aifid] || '';

                // Determine if remarks are editable based on processcode and teamHead
                const isRemarksEditable = ((processcode === 'H' || processcode === '' || processcode ===
                    'E') && teamHead !== 'Y')
                const isDisabled = !isRemarksEditable && (teamHead === 'Y' || isProcessCodeT);

                html += `<tr>`;

                if (index === 0) {
                    html +=
                        `<td class="text-center align-middle" rowspan="${checkpoints.length}">${headingSerial++}</td>
                        <td class="text-center align-middle fw-bold text-uppercase heading-cell"  data-en="${head_en}"
    data-ta="${head_ta}" rowspan="${checkpoints.length}">${lang=='ta'?head_ta:head_en}</td>`;
                }

                html +=
                    `<td class=" heading-cell" data-en="${item.checkpoint_en}"   data-ta="${item.checkpoint_ta}" >${index + 1}) ${lang=="ta"?item.checkpoint_ta:item.checkpoint_en}</td>`;

                // Action Radio Buttons
                html += `<td class="text-center">`;

                ['Y', 'N'].forEach(val => {
                    const label_en = val === 'Y' ? 'Verified' : 'Need Clarification';
                    const label_ta = val === 'Y' ? 'சரிபார்ப்பு' : 'தெளிவு தேவை';
                    const checked = answer === val ? 'checked' : '';
                    const disabledAttr = isDisabled ? 'disabled' : '';
                    //      alert(answer)
                    html += `
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="action_${aifid}" value="${val}" ${checked} ${disabledAttr}>
                            <label class="form-check-label action-label" data-en="${label_en}"   data-ta="${label_ta}">${lang=='ta'?label_ta:label_en}</label>
                        </div>
                    `;
                });

                // Add hidden input for answer if disabled
                if (isDisabled) {
                    html += `<input type="hidden" name="action_${aifid}" value="${answer}">`;
                }

                html += `</td>`;

                // Remarks Textarea
                const remarksDisabledAttr = isRemarksEditable ? '' : 'disabled readonly';
                html +=
                    `<td>
            <textarea name="remarks_${aifid}" class="form-control remark-field" rows="1" maxlength="300" placeholder="Enter remarks..." ${remarksDisabledAttr}>${remark}</textarea>`;
                if (!isRemarksEditable) {
                    html += `<input type="hidden" name="remarks_${aifid}" value="${remark}">`;
                }

                html += `</td>`;

                // Team Head Remarks Textarea
                if (
                    (teamHead === 'Y' && (processcode === 'T' || processcode === 'E')) ||
                    processcode === 'H' ||
                    (rejoinderstatus === 'Y' && (processcode === 'T' || processcode === 'H' ||
                            processcode === 'C') &&
                        teamHead !== 'Y')
                ) {
                    const teamHeadEditable = ((processcode === 'T' || processcode === 'E') &&
                        teamHead === 'Y') ? '' : 'disabled readonly';
                    html +=
                        `<td>
                  <textarea name="teamhead_remarks_${aifid}" maxlength="300" class="form-control teamhead-remark-field" rows="1" placeholder="Team Head remarks..." ${teamHeadEditable}>${teamRemark}</textarea>`;

                    if (teamHeadEditable) {
                        html +=
                            `<input type="hidden" name="teamhead_remarks_${aifid}" value="${teamRemark}">`;
                    }

                    html += `</td>`;
                }

                html += `</tr>`;
            });
        });

        html += `</tbody></table></div>`;
        $('.checklist').html(html);
        translate();
        // $('input[type="radio"][name^="action_"]').on('change', function() {
        //     const totalCheckpoints = $('input[type="radio"][name^="action_"][value="Y"]').length;
        //     const checkedVerified = $('input[type="radio"][value="Y"]:checked').length;

        //     if (totalCheckpoints > 0 && totalCheckpoints === checkedVerified) {
        //         $('#completebtn').show()
        //         $('#auditorremarks_label').addClass('required');
        //     } else {


        //         $('#completebtn').hide()
        //         $('#auditorremarks_label').removeClass('required');

        //         // Example: $('#submit_button').prop('disabled', true);
        //     }
        // });
        if (teamHead === 'Y') {
            restrictSpecialChars(" textarea[name^='teamhead_remarks_']");
        } else {
            restrictSpecialChars(" textarea[name^='remarks_']");
        }
        // changeLanguage(lang)

    }










    function restrictSpecialChars(selector) {
        $(selector)
            .attr('maxlength', 300)
            .on("keypress", function(event) {
                let char = String.fromCharCode(event.which);
                let currentVal = $(this).val();

                if (!/^[a-zA-Z0-9 \-.\u0B80-\u0BFF]$/.test(char)) {
                    event.preventDefault();
                    return;
                }

                if (currentVal.length >= 300) {
                    event.preventDefault();
                }
            })
            .on("paste", function(e) {
                e.preventDefault();
                let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                let cleanData = pasteData.replace(/[^a-zA-Z0-9 \-.\u0B80-\u0BFF]/g, '');
                let currentVal = $(this).val();
                let allowedLength = 300 - currentVal.length;
                cleanData = cleanData.substring(0, allowedLength);

                document.execCommand("insertText", false, cleanData);
            })
        // .on("blur", function() {
        //     const $this = $(this);
        //     let length = $this.val().length;
        //     $this.next(".text-danger").remove(); // Remove existing error messages

        //     if (length < 10) {
        //         $this.addClass('error');
        //         $this.after('<span class="text-danger">Minimum 10 characters required</span>');
        //     } else {
        //         $this.removeClass('error');
        //     }
        // });
    }



    function validateChecklistForm() {
        let isValid = true;
        let errorMessages = [];

        // 1. Validate radio selection per group
        let radioGroups = new Set();
        $('.required-radio').each(function() {
            radioGroups.add($(this).attr('name'));
        });

        radioGroups.forEach(groupName => {
            if (!$(`input[name="${groupName}"]:checked`).length) {
                isValid = false;
                errorMessages.push(`Please select Yes/No for ${groupName.split('_')[1]}`);
            }
        });

        // 2. Validate remarks
        $('.remark-field').each(function() {
            const val = $(this).val().trim();
            const regex = /^[a-zA-Z0-9\s\-]+$/;
            if (val.length < 15 || val.length > 300 || !regex.test(val)) {
                isValid = false;
                const fieldName = $(this).attr('name').split('_')[1];
                errorMessages.push(
                    `Remark for ${fieldName} must be 15–300 characters and only A-Z, 0-9, spaces, hyphens.`);
            }
        });

        if (!isValid) {
            alert(errorMessages.join('\n'));
        }

        return isValid;

    }

    //////////////////////validation
    $.validator.addMethod("radioRequired", function(value, element) {
        let radioName = $(element).attr("name");
        return $(`input[name="${radioName}"]:checked`).length > 0;
    }, ""); // empty here; message added in rules()
    $.validator.addMethod("noSpecialChars", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\- ]+$/.test(value);
    }, function(params, element) {
        const language = getLanguage('');
        return errorMessages[language]['specialChars'];
    });

    $("#inspectionform").validate({
        rules: {
            "textarea[name^='remarks_']": {
                required: true,
                minlength: 10,
                noSpecialChars: true,
            },


            "input[type='radio'][name^='action_']": {
                radioRequired: true,
            }

            // Validate file upload only if 'Y' is selected

        },
        messages: {
            "input[type='radio'][name^='action_']": {
                radioRequired: "Please select Yes or No",
            },
            "textarea[name^='remarks_']": {
                required: "Remarks are required",
                minlength: "Remarks must be at least 10 characters long",
                noSpecialChars: "Special characters are not allowed",
            },

        },
        errorPlacement: function(error, element) {
            // For datepicker fields inside input-group, place error below the input group
            if (element.hasClass('datepicker')) {
                // Insert the error message after the input-group, so it appears below the input and icon
                error.insertAfter(element.closest('.input-group'));
            } else {
                // For other elements, insert the error after the element itself
                error.insertAfter(element);
            }
            if (element.attr("type") === "radio") {
                error.insertAfter(element.closest(".form-check-inline")
                    .last()); // Place error after last radio button
            } else {
                error.insertAfter(element); // Default error placement
            }

        },
        invalidHandler: function(event, validator) {
            scrollToFirstError();
        }
    });
    // Define the custom validator

    function scrollToFirstError() {

        setTimeout(() => {
            const firstError = $('.is-invalid:first');

            if (firstError.length) {
                const offsetTop = firstError.offset().top;
                // console.log("Scrolling to:", offsetTop, firstError); // Debug log

                $('html, body').animate({
                    scrollTop: offsetTop - 100
                }, 500);

                firstError.focus(); // Optional: bring cursor to it
            } else {
                console.log("No .is-invalid found");
            }
        }, 100);
    }



    function validateRemarks(remarksname, minlength = 10) {
        let isValid = true;

        $('.' + remarksname).each(function() {
            const $textarea = $(this);
            const textareaName = $textarea.attr('name'); // e.g., teamhead_remarks_123
            const aifid = textareaName.split('_').pop(); // get 123

            const selectedAction = $(`input[name="action_${aifid}"]:checked`).val();
            const isDisabled = $textarea.prop('disabled');
            const value = $textarea.val().trim();

            // Remove any old validation
            $textarea.removeClass('is-invalid');
            $textarea.next('.error-text').remove();

            // Live input cleanup
            $textarea.off('input.validate').on('input.validate', function() {
                const liveVal = $(this).val().trim();
                if (liveVal.length >= minlength) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-text').remove();
                }
            });

            // Only validate if "Need Clarification" is selected
            if (!isDisabled && selectedAction === 'N') {
                if (value === '' || value.length < minlength) {
                    $textarea.addClass('is-invalid');
                    const errorMessage = value === '' ?
                        'Please enter the remarks on Inspection Points' :
                        `Remarks must be at least ${minlength} characters long`;
                    $textarea.after(
                        `<div class="error-text text-danger small mt-1">${errorMessage}</div>`
                    );
                    isValid = false;
                }
            }
        });

        return isValid;
    }





    $('#buttonaction').on('click', function(e) {

        // Get the CKEditor instance content
        event.preventDefault();

        if (teamheadFlag != 'Y') {


            let allAnswered = true;
            let firstUnansweredInput = null;

            $('input[type="radio"][name^="action_"]').each(function() {
                const name = $(this).attr('name');

                if ($(`input[name="${name}"]:checked`).length === 0) {
                    allAnswered = false;

                    // Capture the first unselected radio group input
                    if (!firstUnansweredInput) {
                        firstUnansweredInput = $(this);
                    }
                }
            });

            // If not all answered, show error and scroll
            if (!allAnswered) {
                // Optional: Show a message (can use toastr, alert, or inline HTML message)

                getLabels_jsonlayout([{
                    id: 'verify_all_pts',
                    key: 'verify_all_pts'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .verify_all_pts, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });



                // Scroll to the first unanswered radio button
                $('html, body').animate({
                    scrollTop: firstUnansweredInput.offset().top - 100
                }, 500);

                // Optionally, add a visual highlight to the input group
                firstUnansweredInput.closest('td').css('border', '2px solid red');

                return false; // prevent form submit or next step
            }

            if (!validateRemarks('remark-field', '10')) {
                scrollToFirstError();
                return false;
            }



            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();

                // If validation passes, manually close the modal
                $('#confirmation_alert').modal('hide');


                inspectchecklist_insert('save', teamheadFlag, 'fresh');
            });



            getLabels_jsonlayout([{
                id: 'submit_ques',
                key: 'submit_ques'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .submit_ques, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
        } else if ((teamheadFlag == 'Y')) {
            restrictSpecialChars("textarea[name^='teamhead_remarks_']");
            if (!validateRemarks('teamhead-remark-field', '10')) {
                scrollToFirstError();
                return false;
            }

            if ($("#inspectionform").valid()) {

                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();

                    // If validation passes, manually close the modal
                    $('#confirmation_alert').modal('hide');
                    var action = 'fresh'

                    inspectchecklist_insert('save', teamheadFlag, action);
                });



                getLabels_jsonlayout([{
                    id: 'save_ques',
                    key: 'save_ques'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .save_ques, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });
            } else {
                scrollToFirstError();
            }


        }






    });
    $('#rejoinderbtn').on('click', function(e) {


        event.preventDefault();
        if (!(slipeditor.getData())) {
            passing_alert_value('Alert', 'Please enter remarks on Audit Slip',
                'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }
        if (teamheadFlag != 'Y') {

            let allAnswered = true;

            // Loop through each group of radio buttons by their name (action_aifid)
            $('input[type="radio"][name^="action_"]').each(function() {
                const name = $(this).attr('name');
                // Check if at least one radio button in this group is selected
                if ($(`input[name="${name}"]:checked`).length === 0) {
                    allAnswered = false;
                    return false; // Exit loop early
                }
            });

            if (!allAnswered) {


                getLabels_jsonlayout([{
                    id: 'verify_all_pts',
                    key: 'fieldaudverify_all_ptsitnotverified'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .verify_all_pts, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });


                return false;
            }
            if (!validateRemarks('remark-field', '10')) {
                scrollToFirstError();
                return false;
            }



            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();

                // If validation passes, manually close the modal
                $('#confirmation_alert').modal('hide');


                inspectchecklist_insert('finalise', teamheadFlag, 'rejoinder');
            });



            getLabels_jsonlayout([{
                id: 'rejoinder_ques',
                key: 'rejoinder_ques'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .rejoinder_ques, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
        } else if ((teamheadFlag == 'Y')) {

            if (!validateRemarks('teamhead-remark-field', '10')) {
                scrollToFirstError();
                return false;
            }
            if ($("#inspectionform").valid()) {
                const alert_content = 'Are you sure to save the details?'
                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();

                    // If validation passes, manually close the modal
                    $('#confirmation_alert').modal('hide');


                    inspectchecklist_insert('save', teamheadFlag, 'rejoinder');
                });



                passing_alert_value(
                    'Confirmation',
                    alert_content,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );
            } else {
                scrollToFirstError();
            }


        }
    });
    $('#finalisebtn').on('click', function(e) {


        // Get the CKEditor instance content
        event.preventDefault();


        if (teamheadFlag != 'Y') {

            let allAnswered = true;

            // Loop through each group of radio buttons by their name (action_aifid)
            $('input[type="radio"][name^="action_"]').each(function() {
                const name = $(this).attr('name');
                // Check if at least one radio button in this group is selected
                if ($(`input[name="${name}"]:checked`).length === 0) {
                    allAnswered = false;
                    return false; // Exit loop early
                }
            });

            if (!allAnswered) {
                passing_alert_value(
                    'Alert',
                    'Please verify the status of the Inspection points',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
                return false;
            }
            if (!validateRemarks('remark-field', '10')) {
                scrollToFirstError();
                return false;
            }

        } else {
            if (!validateRemarks('teamhead-remark-field', '10')) {
                scrollToFirstError();
                return false;
            }
        }

        const alert_content = teamheadFlag == 'Y' ? 'Are you sure to submit the details?' :
            'Are you sure to submit the details to the Team Head?';
        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();

            // If validation passes, manually close the modal
            $('#confirmation_alert').modal('hide');


            inspectchecklist_insert('finalise', teamheadFlag, 'fresh');
        });



        getLabels_jsonlayout([{
            id: teamheadFlag == 'Y' ? 'submit_ques' : 'submitto_head_ques',
            key: teamheadFlag == 'Y' ? 'submit_ques' : 'submitto_head_ques'
        }], 'N').then((text) => {
            passing_alert_value('Confirmation', teamheadFlag == 'Y' ? text.submit_ques : text
                .submitto_head_ques,
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
        });
    });
    $('#completebtn').on('click', function(e) {


        // Get the CKEditor instance content
        event.preventDefault();


        let allVerified = true;
        $('input[type=radio][name^="action_"]').each(function() {
            const name = $(this).attr('name');
            const selectedVal = $(`input[name="${name}"]:checked`).val();

            // If not already validated and current one isn't Y
            if (selectedVal !== 'Y') {
                allVerified = false;
                return false; // exit each loop early
            }
        });
        if (!validateRemarks('remark-field')) {
            scrollToFirstError();
            return false;
        }
        if (!(editor.getData())) {
            getLabels_jsonlayout([{
                id: 'verify_remarks_finalise',
                key: 'verify_remarks_finalise'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .verify_remarks_finalise, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });

            return;
        }
        if ((editor.getData())) {
            const content = editor.getData();
            const plainText = content.replace(/<[^>]*>?/gm, '').trim(); // remove HTML tags

            if (plainText.length < 20) {
                getLabels_jsonlayout([{
                    id: 'min_characters',
                    key: 'min_characters'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .min_characters, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });

                return;
            }

        }


        // if (!allVerified) {

        //     $('#process_button').off('click').on('click', function(event) {
        //         event.preventDefault();

        //         // If validation passes, manually close the modal
        //         $('#confirmation_alert').modal('hide');
        //         inspectchecklist_insert('finalise', teamheadFlag, 'complete');
        //     });

        //     passing_alert_value(
        //         'Alert',
        //         'Some of the checkpoints have not been verified. Do you want to proceed?',
        //         'confirmation_alert',
        //         'alert_header',
        //         'alert_body',
        //         'forward_alert'
        //     );

        // } else {

        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();

            // If validation passes, manually close the modal
            $('#confirmation_alert').modal('hide');
            inspectchecklist_insert('finalise', teamheadFlag, 'complete');
        });



        getLabels_jsonlayout([{
            id: 'finalise_ques',
            key: 'finalise_ques'
        }], 'N').then((text) => {
            passing_alert_value('Confirmation', text
                .finalise_ques, 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
        });
        //   }

        // if (!(editor.getData())) {
        //     passing_alert_value('Alert', 'Please enter remarks on verification of Inspection Points',
        //         'confirmation_alert',
        //         'alert_header', 'alert_body', 'confirmation_alert');
        //     return;
        // }





    });
    // $('#inspect_status').on('click', function(e) {
    //     var auditorrem = $('#inspectionremarks').val();

    //     // Get the CKEditor instance content
    //     event.preventDefault();

    //     if (InspectionStatus == 'Y') {
    //         if (auditorrem == '' || auditorrem == null) {
    //             passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
    //                 'alert_header', 'alert_body', 'confirmation_alert');
    //             return;
    //         }
    //         const alert_content = 'Are you sure to approve the inspection?'

    //         $('#process_button').off('click').on('click', function(event) {
    //             event.preventDefault();

    //             // If validation passes, manually close the modal
    //             $('#confirmation_alert').modal('hide');
    //             completeinspect()
    //         });



    //         passing_alert_value(
    //             'Confirmation',
    //             alert_content,
    //             'confirmation_alert',
    //             'alert_header',
    //             'alert_body',
    //             'forward_alert'
    //         );


    //     } else {
    //         const alert_content = 'Please ,Confirm with the inspection before submission'

    //         $('#process_button').off('click').on('click', function(event) {
    //             event.preventDefault();

    //             // If validation passes, manually close the modal
    //             $('#confirmation_alert').modal('hide');

    //         });



    //         passing_alert_value(
    //             'Confirmation',
    //             alert_content,
    //             'confirmation_alert',
    //             'alert_header',
    //             'alert_body',
    //             'forward_alert'
    //         );
    //     }



    // });

    function completeinspect() {
        var inspectionid = $('#auditinspectionid').val()
        var remarks = $('#inspectionremarks').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        $.ajax({
            url: '/inspection/completeinspect', // URL where the form data will be posted
            type: 'POST',
            data: {
                auditinspectionid: inspectionid,
                remarks: remarks
            },

            success: function(response) {
                if (response.success) {
                    // reset_form(); // Reset the form after successful submission


                    // get_severity('', 'severityid');
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    $('#comp_status').show()
                    $('#comp_btn').hide()

                    getinspectionDetails();


                } else if (response.message) {
                    // Handle errors if needed
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
                // if (response && response.message) {
                //     userMessage = response.message;
                // }
                let msg = xhr.responseJSON?.message || 'Unexpected error occurred.';

                // Displaying the error message
                passing_alert_value('Alert', msg, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired
                $('#process_button').removeAttr('disabled');
            }
        });

    }

    // function movetonext() {
    //     $(".validation-wizard").steps("next");
    // }

    function inspectchecklist_insert(action, teamheadFlag, actionfor) {
        //var formData = new FormData($('#inspectionform')[0]);
        var formData = $('#inspectionform').serializeArray();
 	 var formuserid = '<?php echo $formsessionid; ?>';

        if (teamheadFlag != 'Y') {
            formData.push({
                name: 'remarks',
                value: editor.getData()
            });
        }
        
	formData.push({
            name: 'formuserid',
            value: formuserid
        });
        formData.push({
            name: 'slipremarks',
            value: slipeditor.getData()
        });
        formData.push({
            name: 'actionfor',
            value: actionfor
        });
        formData.push({
            name: 'teamheadFlag',
            value: teamheadFlag
        });
        if (action === 'finalise') {
            finaliseflag = 'F';
            formData.push({
                name: 'finaliseflag',
                value: finaliseflag
            });
        } else {
            finaliseflag = 'Y';
            formData.push({
                name: 'finaliseflag',
                value: finaliseflag
            });
        }
        // formData.append('auditscheduleid', $('#auditscheduleid').val());
        // formData.append('auditplanid', $('#auditplanid').val());

        $('#process_button').attr('disabled', true);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        $.ajax({
            url: '/inspection/inspectchecklist_insert', // URL where the form data will be posted
            type: 'POST',
            data: formData,

            success: function(response) {
                if (response.success) {
                    // reset_form(); // Reset the form after successful submission


                    // get_severity('', 'severityid');
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');


                    getinspectionDetails();


                } else if (response.message) {
                    // Handle errors if needed
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
		if(response.error=='402')
            {
                 handleUnauthorizedError()
}
                // if (response && response.message) {
                //     userMessage = response.message;
                // }
                let msg = xhr.responseJSON?.message || 'Unexpected error occurred.';

                // Displaying the error message
                passing_alert_value('Alert', msg, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired
                $('#process_button').removeAttr('disabled');
            }
        });
    }

    function hideforpopulate() {

        $('#buttonset').hide();
        $('#auditorsremarks_div').hide()
        $('#checklist_div').hide()
        $('#ins_label').hide()

    }

    function changeLanguage(selectedLang) {

        $(".lang").each(function() {
            let key = $(this).attr("key");
            if (arrLang[lang][key] && arrLang[lang][key]) {
                $(this).text(arrLang[lang][key]);
            }
        });


    }

    function getinspectionDetails() {
        var auditscheduleid = auditscheduleid || '<?php echo $auditSchedule_id; ?>';


        $.ajax({
            url: "/inspection/getinspectionDetails", // URL where the form data will be posted
            type: 'POST',
            data: {
                auditscheduleid: auditscheduleid,

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(response) {

                $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');
                if (checkpointQuestions.length <= 0) {
                    $('#buttonset').hide();
                    $('#inspect_pts').show();

                    $('#auditorsremarks_div').hide()
                    $('#checklist_div').hide()
                    passing_alert_value('Alert', 'No Inspection Points are found for this Institution',
                        'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return;
                }
                if (response) {


                    let mergedHistoryData
                    if (response.historydata.length > 0) {

                        historydata = response.data.filter(item => item.activeinspectionflag ===
                            'I');



                        const historyInspectionIds = [...new Set(
                            response.data
                            .filter(item => item.processcode === 'C' && item.activeinspection ===
                                'I')
                            .map(item => item.transactionno)
                        )];
                        //console.log('Matching Inspection IDs:', historyInspectionIds);

                        // Step 2: Gather all records from historydata that match those inspection IDs
                        mergedHistoryData = response.historydata.filter(item =>
                            historyInspectionIds.includes(item.transactionno)
                        );
                        // console.log('Merged History Data:', mergedHistoryData);

                        // Step 3: Call populateGroupedData once
                        if (teamheadFlag != 'Y') {
                            populateGroupedData(checkpointQuestions, mergedHistoryData);
                        } else {
                            if (formstatus == 'new') {

                                populateGroupedData(checkpointQuestions, mergedHistoryData);
                                hideforpopulate()
                                return;
                            } else {
                                populateGroupedData(checkpointQuestions, mergedHistoryData);
                            }

                        }

                    }


                    let inspectdata = null;

                    if (response.data && response.data.length > 0) {
                        // Try to find item with activeinspectionflag === 'A'
                        inspectdata = response.data.find(item => item.activeinspection === 'A');
                        // console.log(inspectdata)

                        // If not found, try to find item with activeinspectionflag === 'FRESH'
                        if (inspectdata && inspectdata.processcode) {


                            const flowinspectionIds = [...new Set(
                                response.data
                                .filter(item => item.activeinspection ===
                                    'A')
                                .map(item => item.transactionno)
                            )];
                            //  console.log('Matching Inspection IDs:', flowinspectionIds);

                            // Step 2: Gather all records from historydata that match those inspection IDs
                            const mergedinspectiondata = response.historydata
                                .filter(item => item && flowinspectionIds.includes(item?.transactionno));

                            //  console.log('Merged History Data:', mergedinspectiondata);





                            inspectprocesscode = inspectdata.processcode;
                            $('#rejoindercycle').val(inspectdata.rejoindercycle);
                            $('#auditinspectionid').val(inspectdata.encrypted_auditinspectionid);
                            const processcode = inspectdata.processcode;
                            const isTeamHead = teamheadFlag === 'Y';
                            const notsame = inspectdata.createdby != inspectdata.updatedby;
                            const inspectcheckpoints = JSON.parse(inspectdata.inspectcheckpoints ||
                                '[]');

                            // if (accesscredential == 'view') {
                            //     populatedata(checkpointQuestions, response.historydata);
                            //     hideforpopulate()
                            //     return;
                            // }


                            if (inspectdata.processcode !== 'C') {
                                // alert(isTeamHead)
                                // alert(processcode)
                                // alert(notsame)
                                // alert(inspectdata.rejoinderstatus)


                                if ((isTeamHead && (processcode === 'E' || processcode === 'T')) ||
                                    (!isTeamHead && (processcode === 'E' || processcode === 'H') &&
                                        inspectdata
                                        .rejoindercycle > 0) ||
                                    (!isTeamHead && (processcode === 'E' || processcode === 'H') &&
                                        notsame &&
                                        inspectdata
                                        .rejoinderstatus == null) ||
                                    (!isTeamHead && (processcode === 'E' || processcode === 'H') && !
                                        notsame &&
                                        inspectdata.rejoinderstatus == null)) {


                                    renderChecklist(
                                        checkpointQuestions,
                                        inspectcheckpoints,
                                        teamheadFlag,
                                        processcode,
                                        inspectdata.inscheckpointsanswer,
                                        inspectdata.inscheckpointremarks,
                                        inspectdata.inscheckpointheadremarks,
                                        inspectdata.rejoinderstatus
                                    );
                                }
                            }



                            // if (teamheadFlag != 'Y') {
                            //     editor.setData(inspectdata.inspectremarks);
                            // }
                            if (inspectdata.processcode == 'E') {

                                if (teamheadFlag == 'Y') {
                                    $('#forwardedby').val(inspectdata.forwardedby);
                                    //  editor.setData(inspectdata.headremarks);
                                    $('#auditorsremarks_div').hide()
                                    populatedata(checkpointQuestions, mergedinspectiondata);
                                } else if (teamheadFlag != 'Y' && inspectdata.rejoindercycle < 1 && !
                                    notsame) {

                                    editor.setData(inspectdata.inspectremarks);

                                } else if (teamheadFlag != 'Y' && inspectdata.rejoindercycle != 0 &&
                                    notsame) {
                                    populatedata(checkpointQuestions, mergedinspectiondata);
                                    hideforpopulate()

                                } else {
                                    editor.setData(inspectdata.inspectremarks);
                                    $('#buttonset').hide();
                                }

                                // changeButtonAction('inspectionform', 'action', 'buttonaction',
                                //     'display_error', '', @json($updatebtn),
                                //     @json($clearbtn), @json($update));

                                // changeButtonAction('auditeedepartmentform', 'action', 'buttonaction',
                                //     'reset_button', 'display_error',
                                //     @json($savebtn), @json($clearbtn),
                                //     @json($insert))
                                change_button_as_update('inspectionform', 'action', 'buttonaction',
                                    'display_error', '', '');

                            }
                            // else if (!isTeamHead && (processcode === 'E' || processcode === 'H') && !
                            //     notsame && inspectdata.rejoinderstatus != null) {
                            //     alert()
                            //     populatedata(checkpointQuestions, mergedinspectiondata);
                            //     // hideforpopulate()
                            // }
                            else if (inspectdata.processcode == 'T' && teamheadFlag != 'Y') {

                                populatedata(checkpointQuestions, mergedinspectiondata);
                                hideforpopulate()


                            } else if (inspectdata.processcode == 'T' && teamheadFlag == 'Y') {
                                $('#forwardedby').val(inspectdata.forwardedby);
                                populatedata(checkpointQuestions, mergedinspectiondata);
                                $('#auditorsremarks_div').hide()
                            } else if (inspectdata.processcode == 'H' && teamheadFlag == 'Y') {
                                $('#forwardedby').val(inspectdata.forwardedby);

                                populatedata(checkpointQuestions, mergedinspectiondata);
                                hideforpopulate()
                                $('#auditorsremarks_div').hide()

                            } else if (inspectdata.processcode == 'C' && teamheadFlag == 'Y') {

                                $('#buttonset').hide();
                                populatedata(checkpointQuestions, mergedinspectiondata);
                                $('#auditorsremarks_div').hide()

                            } else if (inspectdata.processcode == 'H' && teamheadFlag != 'Y') {

                                $('#forwardedby').val(inspectdata.forwardedby);
                                // $('#buttonset').hide();
                                // changeButtonAction('inspectionform', 'buttonaction', '',
                                //     'display_error',
                                //     @json($updatebtn))
                                change_button_as_update('inspectionform', 'action', 'buttonaction',
                                    'display_error', '', '');
                                populatedata(checkpointQuestions, mergedinspectiondata);
                                //  $('#auditorsremarks_div').hide()
                                //$('#buttonaction').hide();
                                $('#completebtn').removeClass('hide_this').css({
                                    display: 'inline-block',
                                    visibility: 'visible'
                                });
                                if (inspectdata.rejoindercycle < rejoinderlimit) {
                                    $('#rejoinderbtn').show()
                                }

                                $('#buttonaction').hide()
                                $('#finalisebtn').hide()
                                // $('#finalisebtn').css({
                                //     display: 'none',
                                //     visibility: 'hidden'
                                // });





                            } else if (inspectdata.processcode == 'C' && teamheadFlag != 'Y') {

                                $('#forwardedby').val(inspectdata.forwardedby);
                                // $('#buttonset').hide();
                                $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');
                                populateGroupedData(checkpointQuestions, mergedinspectiondata)

                                if (formstatus == 'new') {
                                    renderChecklist(checkpointQuestions, '', teamheadFlag, '')
                                }

                                // populatedata(checkpointQuestions, mergedinspectiondata);
                                //   hideforpopulate()
                                // $('#auditorsremarks_div').hide()



                            } else if (inspectdata.processcode == 'O' && teamheadFlag != 'Y') {
                                $('#auditinspectionid').val(inspectdata.encrypted_auditinspectionid);
                                renderChecklist(checkpointQuestions, '', teamheadFlag, '')

                                $('#completebtn').hide()
                                $('#rejoinderbtn').hide()

                                $('#buttonaction').show()
                                $('#finalisebtn').show()
                                $('#auditorsremarks_div').hide()
                            }

                        } else {

                            if (formstatus == 'new') {
                                renderChecklist(checkpointQuestions, '', teamheadFlag, '')
                                $('#completebtn').hide()
                                $('#rejoinderbtn').hide()

                                $('#buttonaction').show()
                                $('#finalisebtn').show()
                                editor.setData('')
                                change_button_as_insert('inspectionform', 'action', 'buttonaction',
                                    'display_error', '', '');
                            } else {
                                //  alert()

                                //   populatedata(checkpointQuestions, mergedHistoryData);
                                $('#auditorAccordionsContainer').hide()

                                hideforpopulate()

                            }





                            // $('#finalisebtn').css({
                            //     display: 'block',
                            //     visibility: 'visible'
                            // });

                        }
                    } else {
                        $('#completebtn').hide()
                        $('#rejoinderbtn').hide()

                        $('#buttonaction').show()
                        $('#finalisebtn').show()
                        // $('#finalisebtn').css({
                        //     display: 'block',
                        //     visibility: 'visible'
                        // });
                        editor.setData('')
                        change_button_as_insert('inspectionform', 'action', 'buttonaction',
                            'display_error', '', '');
                        renderChecklist(checkpointQuestions, '', teamheadFlag, '')

                    }

                } else if (response.message) {

                    // Handle errors if needed
                    console.log(response.message);
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                // Displaying the error message
                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            },

        });
    }

    function populateGroupedData(checkpointQuestions, responseData) {
        if (!responseData || !Array.isArray(responseData)) {
            responseData = Array.from(responseData || []);
        }

        $('#auditorGroupedContainer').show();
        let container = document.getElementById("auditorGroupedContainer");
        container.innerHTML = "";

        let groupedData = {};

        // 1. Grouping data by auditscheduleid, inspectionid, transactionno
        responseData.forEach(data => {
            let groupKey =
                `${data.auditscheduleid}_${data.auditinspectionid}_${data.transactionno}`;
            if (!groupedData[groupKey]) groupedData[groupKey] = [];
            groupedData[groupKey].push(data);
        });

        let parentAccordionIndex = 0;
        for (let groupKey in groupedData) {
            let groupItems = groupedData[groupKey];

            let [scheduleId, inspectionId, txnNo] = groupKey.split("_");
            let parentAccordionId = `groupAccordion${parentAccordionIndex}`;
            let parentCollapseId = `groupCollapse${parentAccordionIndex}`;
            let parentHeaderId = `groupHeader${parentAccordionIndex}`;
            let parentinitiatedby = groupItems[0]?.username || '-';
            let parentdesignation = groupItems[0]?.desigelname || '-';

            const lang = getLanguage('');

            let editorIds = [];

            let parentAccordion = document.createElement("div");
            parentAccordion.classList.add("accordion", "mb-3");
            parentAccordion.id = parentAccordionId;

            let childContent = '';

            groupItems.forEach((data, index) => {
                let collapseId = `collapse${parentAccordionIndex}_${index}`;
                let headerId = `heading${parentAccordionIndex}_${index}`;
                let editorId = `editor${parentAccordionIndex}_${index}`;
                editorIds.push(editorId);
                if (teamheadFlag == 'Y' && (data.processcode || '') == 'C') {
                    return;
                }
                let forwardedBy = data.username || "-";
                let designation = data.desigelname || "-";
                let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";

                let verifiedMap = {},
                    remarksMap = {},
                    headRemarksMap = {};

                try {
                    verifiedMap = JSON.parse(data.inspectcheckpoints ?? '{}') || {};
                } catch {}

                try {
                    remarksMap = JSON.parse(data.inscheckpointremarks ?? '{}') || {};
                } catch {}

                try {
                    headRemarksMap = JSON.parse(data.inscheckpointheadremarks ?? '{}') || {};
                } catch {}

                // Group checkpoints by heading_en
                let grouped = {};
                checkpointQuestions.forEach(cp => {
                    if (!grouped[cp.heading_en]) grouped[cp.heading_en] = [];
                    grouped[cp.heading_en].push(cp);
                });

                let rejoinderCycleVal = (data.rejoindercycle || '').toUpperCase();
                let processCodeVal = (data.processcode || '').toUpperCase();
                let showHeadRemarks = !(["", "N", null].includes(rejoinderCycleVal) && processCodeVal === 'T');

                let tableHTML = `
                <div class="card card_border">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; width: 98%; margin: 0 auto;">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="lang" key="sl_no" style="background-color: #e6e8ea; width: 5%">S. NO</th>
                            <th class="lang" key="heading_label" style="background-color: #e6e8ea; width: 10%">Heading</th>
                            <th class="lang" key="ins_checkpt_label" style="background-color: #e6e8ea; width: 25%">Checkpoints</th>
                            <th class="lang" key="action" style="background-color: #e6e8ea; width: 20%">Action</th>
                            <th class="lang" key="remarks_by_desig" style="background-color: #e6e8ea; width: 20%">Remarks by AD/DIR/RJD</th>
                            ${showHeadRemarks ? `<th  class="lang" key="remarks_head_label" style="background-color: #e6e8ea; width: 20%">Remarks by Team Head</th>` : ''}
                        </tr>
                    </thead>
                    <tbody>`;

                let headingSerial = 1;
                Object.entries(grouped).forEach(([heading, cps]) => {
                    const firstCp = cps[0]; // Use first checkpoint item to get heading translations
                    const head_en = (firstCp.heading_en || '').toUpperCase();
                    const head_ta = (firstCp.heading_ta || '').toUpperCase();
                    const displayHead = lang === 'ta' ? head_ta : head_en;
                    cps.forEach((cp, i) => {
                        const aifid = cp.aifid;
                        const verified = verifiedMap[aifid] === 'Y' ? 'Y' : (verifiedMap[
                            aifid] === 'N' ? 'N' : '');
                        const inspectcheckremarks = remarksMap[aifid] || '';
                        const inspectionheadremarks = headRemarksMap[aifid] || '';

                        tableHTML += `
                    <tr>
                        ${i === 0 ? `<td rowspan="${cps.length}">${headingSerial++}</td>` : ''}
                        <td class="action-label" data-en="${head_en}" data-ta="${head_ta}">${i === 0 ? displayHead : ''}</td>
                        <td class="action-label" data-en="${cp.checkpoint_en}" data-ta="${cp.checkpoint_ta}">${i + 1}. ${lang=='ta'?cp.checkpoint_ta:cp.checkpoint_en}</td>
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" disabled ${verified === 'Y' ? 'checked' : ''}>
                                <label class="form-check-label lang" key="verified_label">Verified</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" disabled ${verified === 'N' ? 'checked' : ''}>
                                <label class="form-check-label lang" key="need_clarify">Need Clarification</label>
                            </div>
                        </td>
                        <td><textarea disabled readonly class="form-control">${inspectcheckremarks || '-'}</textarea></td>
                        ${showHeadRemarks ? `<td><textarea disabled readonly class="form-control">${inspectionheadremarks || '-'}</textarea></td>` : ''}
                    </tr>`;
                    });
                });

                tableHTML += `</tbody></table></div></div>`;

                childContent += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="${headerId}">
                    <button class="accordion-button collapsed auditor_bg" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                        ${forwardedBy} - ${designation} (${forwardedOn})
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                    <div class="accordion-body">
                        ${tableHTML}
                          ${(!data.auditteamhead || data.auditteamhead.trim() === '') && data.processcode !== 'T' ? `
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label><strong>${designation}'s Remarks:</strong></label>
                            <div class="editor-container">
                                <textarea id="${editorId}">${data.inspectremarks || "No remarks provided"}</textarea>
                            </div>
                        </div>
                    </div>
                ` : ''}
                    </div>
                </div>
            </div>
             <hr class="p-2">`;
            });

            parentAccordion.innerHTML = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="${parentHeaderId}">
                <button class="accordion-button fw-bold parent_bg" type="button" data-bs-toggle="collapse" data-bs-target="#${parentCollapseId}">
            ${parentAccordionIndex + 1} )   Inspection Completed by: ${parentinitiatedby} - ${parentdesignation}
                </button>
            </h2>
            <div id="${parentCollapseId}" class="accordion-collapse collapse" aria-labelledby="${parentHeaderId}">
                <div class="accordion-body">
                    <div class="accordion" id="innerAccordion${parentAccordionIndex}">
                        ${childContent}
                    </div>
                </div>
            </div>
        </div>
         <hr class="p-2">`;

            container.appendChild(parentAccordion);
            initializeEditors(editorIds);
            parentAccordionIndex++;
        }
    }

    function populateGroupedSlipData(checkpointQuestions, responseData) {
        if (!responseData || !Array.isArray(responseData)) {
            responseData = Array.from(responseData || []);
        }

        $('#slipGroupedContainer').show();
        let container = document.getElementById("slipGroupedContainer");
        container.innerHTML = "";

        let groupedData = {};

        // 1. Grouping data by auditscheduleid, inspectionid, transactionno
        responseData.forEach(data => {
            let groupKey =
                `${data.auditscheduleid}_${data.auditinspectionid}_${data.transactionno}`;
            if (!groupedData[groupKey]) groupedData[groupKey] = [];
            groupedData[groupKey].push(data);
        });

        let parentAccordionIndex = 0;
        for (let groupKey in groupedData) {

            let groupItems = groupedData[groupKey];

            let [scheduleId, inspectionId, txnNo] = groupKey.split("_");
            let parentAccordionId = `groupAccordion${parentAccordionIndex}`;
            let parentCollapseId = `groupCollapse${parentAccordionIndex}`;
            let parentHeaderId = `groupHeader${parentAccordionIndex}`;
            let parentinitiatedby = groupItems[0]?.username || '-';
            let parentdesignation = groupItems[0]?.desigelname || '-';



            let editorIds = [];

            let parentAccordion = document.createElement("div");
            parentAccordion.classList.add("accordion", "mb-3");
            parentAccordion.id = parentAccordionId;

            let childContent = '';

            groupItems.forEach((data, index) => {
                if (teamheadFlag == 'Y' && (data.processcode || '') == 'C') {
                    return;
                }
                let collapseId = `collapse${parentAccordionIndex}_${index}`;
                let headerId = `heading${parentAccordionIndex}_${index}`;
                let editorId = `slipgroupeditor${parentAccordionIndex}_${index}`;
                editorIds.push(editorId);

                let forwardedBy = data.username || "-";
                let designation = data.desigelname || "-";
                let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";

                let verifiedMap = {},
                    remarksMap = {},
                    headRemarksMap = {};

                try {
                    verifiedMap = JSON.parse(data.inspectcheckpoints ?? '{}') || {};
                } catch {}

                try {
                    remarksMap = JSON.parse(data.inscheckpointremarks ?? '{}') || {};
                } catch {}

                try {
                    headRemarksMap = JSON.parse(data.inscheckpointheadremarks ?? '{}') || {};
                } catch {}

                // Group checkpoints by heading_en


                let rejoinderCycleVal = (data.rejoindercycle || '').toUpperCase();
                let processCodeVal = (data.processcode || '').toUpperCase();
                let showHeadRemarks = !(["", "N", null].includes(rejoinderCycleVal) && processCodeVal === 'T');



                childContent += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="${headerId}">
                    <button class="accordion-button collapsed auditor_bg" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                        ${forwardedBy} - ${designation} (${forwardedOn})
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label><strong>${designation}'s Remarks:</strong></label>
                                <textarea id="${editorId}">${data.slipremarks || "No remarks provided"}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <hr class="p-2">`;
            });

            parentAccordion.innerHTML = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="${parentHeaderId}">
                <button class="accordion-button fw-bold parent_bg" type="button" data-bs-toggle="collapse" data-bs-target="#${parentCollapseId}">
            ${parentAccordionIndex + 1} )   Inspection Completed by: ${parentinitiatedby} - ${parentdesignation}
                </button>
            </h2>
            <div id="${parentCollapseId}" class="accordion-collapse collapse" aria-labelledby="${parentHeaderId}">
                <div class="accordion-body">
                    <div class="accordion" id="innerAccordion${parentAccordionIndex}">
                        ${childContent}
                    </div>
                </div>
            </div>
        </div>
         <hr class="p-2">`;

            container.appendChild(parentAccordion);
            initializeEditors(editorIds);
            parentAccordionIndex++;
        }
    }

    function populatedata(checkpointQuestions, responseData) {
        if (!responseData || !Array.isArray(responseData)) {
            responseData = Array.from(responseData || []);
        }
        const lang = getLanguage('');

        $('#auditorAccordionsContainer').show();
        let container = document.getElementById("auditorAccordionsContainer");
        container.innerHTML = "";

        let editorIds = [];
        let lastIndex = responseData.length - 1;

        if (responseData.length === 1) {

        }

        responseData.forEach((data, index) => {
            if (teamheadFlag == 'Y' && (data.processcode || '') == 'C') {
                return;
            }
            let accordionId = `remarkAccordion${index}`;
            let collapseId = `collapse${index}`;
            let headerId = `heading${index}`;
            let editorId = `editor${index}`;
            let showRemarksEditor = (!data.auditteamheadflag || data.auditteamheadflag.trim() === '');
            if (showRemarksEditor) {
                editorIds.push(editorId);
            }


            let forwardedBy = data.username || "-";
            let designation = data.desigelname || "-";
            let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";

            let accordion = document.createElement("div");
            accordion.classList.add("accordion", "my-2");
            accordion.id = accordionId;

            let verifiedMap = {};
            let remarksMap = {};
            let headRemarksMap = {};

            try {
                verifiedMap = JSON.parse(data.inspectcheckpoints ?? '{}') || {};
            } catch (e) {
                console.warn("Invalid inspectcheckpoints JSON:", data.inspectcheckpoints);
            }

            try {
                remarksMap = JSON.parse(data.inscheckpointremarks ?? '{}') || {};
            } catch (e) {
                console.warn("Invalid inscheckpointremarks JSON:", data.inscheckpointremarks);
            }

            try {
                headRemarksMap = JSON.parse(data.inscheckpointheadremarks ?? '{}') || {};
            } catch (e) {
                console.warn("Invalid inscheckpointheadremarks JSON:", data.inscheckpointheadremarks);
            }

            // Group checkpoints by heading_en
            let grouped = {};
            checkpointQuestions.forEach(cp => {
                if (!grouped[cp.heading_en]) grouped[cp.heading_en] = [];
                grouped[cp.heading_en].push(cp);
            });

            // Fix: More reliable check for Head Remarks visibility
            let rejoinderCycleVal = (data.rejoindercycle || '').toUpperCase();
            let processCodeVal = (data.processcode || '').toUpperCase();
            let showHeadRemarks = !(["", "N", null].includes(rejoinderCycleVal) && processCodeVal === 'T');

            let tableHTML = `
            <div class="card card_border">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; width: 98%; margin: 0 auto;">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                             <th class="lang" key="sl_no"  style="background-color: #e6e8ea; width: 5%">S. NO</th>
                            <th  class="lang" key="heading_label" style="background-color: #e6e8ea; width: 10%">Heading</th>
                            <th  class="lang" key="ins_checkpt_label" style="background-color: #e6e8ea; width: 25%">Checkpoints</th>
                            <th  class="lang" key="action" style="background-color: #e6e8ea; width: 20%">Action</th>
                            <th  class="lang" key="remarks_by_desig" style="background-color: #e6e8ea; width: 20%">Remarks by AD/DIR/RJD</th>
                            ${showHeadRemarks ? `<th class="lang" key="remarks_head_label" style="background-color: #e6e8ea; width: 20%">Remarks by Team Head</th>` : ''}
                        </tr>
                    </thead>
                    <tbody>`;
            let headingSerial = 1;
            Object.entries(grouped).forEach(([heading, cps]) => {
                const firstCp = cps[0]; // Use first checkpoint item to get heading translations
                const head_en = (firstCp.heading_en || '').toUpperCase();
                const head_ta = (firstCp.heading_ta || '').toUpperCase();
                const displayHead = lang === 'ta' ? head_ta : head_en;
                cps.forEach((cp, i) => {
                    const aifid = cp.aifid;
                    const verified = verifiedMap[aifid] === 'Y' ? 'Y' : (verifiedMap[aifid] ===
                        'N' ? 'N' : '');
                    const inspectcheckremarks = remarksMap[aifid] || '';
                    const inspectionheadremarks = headRemarksMap[aifid] || '';

                    tableHTML += `
                <tr>
                      ${i === 0 ? `<td rowspan="${cps.length}">${headingSerial++}</td>` : ''}
                    <td class="action-label" data-en="${head_en}" data-ta="${head_ta}" >${i === 0 ? displayHead : ''}</td>
                    <td class="action-label" data-en="${cp.checkpoint_en}" data-ta="${cp.checkpoint_ta}">${i + 1}. ${lang=="ta"? cp.checkpoint_ta:cp.checkpoint_en}</td>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled ${verified === 'Y' ? 'checked' : ''}>
                            <label class="form-check-label lang" key="verified_label">Verified</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled ${verified === 'N' ? 'checked' : ''}>
                            <label class="form-check-label lang" key="need_clarify">Need Clarification</label>
                        </div>
                    </td>
                    <td><textarea disabled readonly class="form-control">${inspectcheckremarks || '-'}</textarea></td>
                    ${showHeadRemarks ? `<td><textarea disabled readonly class="form-control">${inspectionheadremarks || '-'}</textarea></td>` : ''}
                </tr>`;
                });
            });

            tableHTML += `</tbody></table></div></div>`;

            accordion.innerHTML = `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="${headerId}">
                            <button style="height: 50px;" class="accordion-button auditor_bg collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${collapseId}"
                                    aria-expanded="false"
                                    aria-controls="${collapseId}">
                                <div class="d-flex flex-column w-100">
                                    <div class="d-none d-md-flex align-items-center w-100">
                                        <div class="text-truncate fw-bold">
                                            ${forwardedBy} - ${designation}
                                        </div>
                                        <div class="text-muted small ms-2" style="margin-left:20px;">
                                            ${forwardedOn}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center justify-content-between d-md-none" style="margin-left: -1.60rem !important;">
                                        <div class="text-start fw-bold">
                                            ${forwardedBy} - ${designation}
                                        </div>
                                        <div class="text-danger text-end me-2">
                                            ${forwardedOn}
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-1 d-md-none" style="margin-left: -1.60rem !important;">Remarks</div>
                                </div>
                            </button>
                        </h2>
                        <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                            <div class="accordion-body">
                                ${tableHTML}
                              ${(!data.auditteamhead || data.auditteamhead.trim() === '') && data.processcode !== 'T' ? `
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label><strong>${designation}'s Remarks:</strong></label>
                                        <div class="editor-container">
                                            <textarea id="${editorId}">${data.inspectremarks || "No remarks provided"}</textarea>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}

                            </div>
                        </div>
                    </div>
                    <hr class="p-2">`;

            container.appendChild(accordion);
        });

        initializeEditors(editorIds);
    }

    function populateslipremarks(responseData) {

        if (!responseData || !Array.isArray(responseData)) {
            responseData = Array.from(responseData || []);
        }

        $('#slipremarksaccordion').show();
        let container = document.getElementById("slipremarksaccordion");
        container.innerHTML = "";

        let editorIds = [];
        let lastIndex = responseData.length - 1;

        if (responseData.length === 1) {

        }

        responseData.forEach((data, index) => {


            let accordionId = `remarkAccordion${index}`;
            let collapseId = `collapse${index}`;
            let headerId = `heading${index}`;
            let editorId = `slipeditor${index}`;
            editorIds.push(editorId);

            let forwardedBy = data.username || "-";
            let designation = data.desigelname || "-";
            let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";

            let accordion = document.createElement("div");
            accordion.classList.add("accordion", "my-2");
            accordion.id = accordionId;

            let verifiedMap = {};
            let remarksMap = {};
            let headRemarksMap = {};



            // Group checkpoints by heading_en
            let grouped = {};
            checkpointQuestions.forEach(cp => {
                if (!grouped[cp.heading_en]) grouped[cp.heading_en] = [];
                grouped[cp.heading_en].push(cp);
            });

            // Fix: More reliable check for Head Remarks visibility
            let rejoinderCycleVal = (data.rejoindercycle || '').toUpperCase();
            let processCodeVal = (data.processcode || '').toUpperCase();
            let showHeadRemarks = !(["", "N", null].includes(rejoinderCycleVal) && processCodeVal === 'T');


            accordion.innerHTML = `
        <div class="accordion-item">
            <h2 class="accordion-header" id="${headerId}">
                <button style="height: 50px;" class="accordion-button auditor_bg collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#${collapseId}"
                        aria-expanded="false"
                        aria-controls="${collapseId}">
                    <div class="d-flex flex-column w-100">
                        <div class="d-none d-md-flex align-items-center w-100">
                            <div class="text-truncate fw-bold">
                                ${forwardedBy} - ${designation}
                            </div>
                            <div class="text-muted small ms-2" style="margin-left:20px;">
                                ${forwardedOn}
                            </div>
                        </div>
                        <div class="d-flex flex-row align-items-center justify-content-between d-md-none" style="margin-left: -1.60rem !important;">
                            <div class="text-start fw-bold">
                                ${forwardedBy} - ${designation}
                            </div>
                            <div class="text-danger text-end me-2">
                                ${forwardedOn}
                            </div>
                        </div>
                        <div class="text-muted small mt-1 d-md-none" style="margin-left: -1.60rem !important;">Remarks</div>
                    </div>
                </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                <div class="accordion-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label><strong>${designation}'s Remarks on Audit Slip:</strong></label>
                            <div class="editor-container">
                                <textarea id="${editorId}">${data.slipremarks || "No remarks provided"}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="p-2">`;

            container.appendChild(accordion);
        });

        initializeEditors(editorIds);
    }




    /////////////////////////////////////////////////////////////////////Check List End////////////////////////////////////////////////////

    /*************************************************  Ckeditor  *********************************************/
    function initializeEditors(editorIds) {

        if (!window.CKEDITOR || !window.CKEDITOR.ClassicEditor) {
            setTimeout(() => initializeEditors(editorIds), 100); // Retry if CKEditor is not yet loaded
            return;
        }

        editorIds.forEach(id => {
            let textarea = document.getElementById(id);
            if (textarea) {
                CKEDITOR.ClassicEditor.create(textarea, {
                        toolbar: {
                            items: [
                                'selectAll', '|',
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        placeholder: 'Write Your Audit Observation here',
                        fontFamily: {
                            options: [
                                'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                                'Courier New, Courier, monospace',
                                'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                'Tahoma, Geneva, sans-serif',
                                'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif',
                                'Verdana, Geneva, sans-serif'
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
                            'RealTimeCollaborativeRevisionHistory',
                            'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                            'RevisionHistory', 'Pagination',
                            'WProofreader',
                            'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                            'TableOfContents',
                            'PasteFromOfficeEnhanced', 'CaseChange'
                        ]
                    })
                    .then(editor => {
                        editor.enableReadOnlyMode('initial'); // Read-only mode

                        // Apply custom styling for scrolling
                        const editable = editor.ui.view.editable.element;
                        editable.style.maxHeight = 'auto'; // Set max height for the editable area
                        editable.style.overflowY = 'auto'; // Enable vertical scrolling
                    })
                    .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
            }
        });
    }


    let editor;

    CKEDITOR.ClassicEditor.create(document.getElementById("auditorremarks"), {
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
                    'uploadImage', 'insertTable',
                    '|',

                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'Enter General Comments ',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
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
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        })
        .then(e => {
            editor = e;
        })
        .catch(error => {
            console.error(error);
        });

    /*************************************************  Ckeditor  *********************************************/

    let slipeditor;

    CKEDITOR.ClassicEditor.create(document.getElementById("slipremarks"), {
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
                    'uploadImage', 'insertTable',
                    '|',

                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'Enter Remarks on Audit Slip',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
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
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        })
        .then(e => {
            slipeditor = e;
        })
        .catch(error => {
            console.error(error);
        });

    /*************************************************  Ckeditor  *********************************************/

/*************************************************  Inspection Workallocation & Diary  *********************************************/
    $(document).ready(function() {
        let currentScheduleId = '';
        let currentInstId = '';
        let currentTeamHeadUserId = '';
        let currentTeamMembersUserIds = '';
        let currentSchTeamMemberId = '';

        $('body').append(`
        <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="remarksModalLabel">Full Remarks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Remarks will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `);

        $('head').append(`
        <style>
        .diary-table {
            font-size: 0.875rem;
        }
        .diary-table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .diary-table td {
            vertical-align: middle;
        }
        .remarks-text {
            max-height: 60px;
            overflow-y: auto;
            white-space: normal;
            word-break: break-word;
            width: 300px;
        }

        .show-more-btn {
            font-size: 0.75rem;
            text-decoration: none;
        }
        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }
        .progress-bar {
            font-size: 0.7rem;
            line-height: 20px;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .badge {
            font-size: 0.75em;
        }
        .table-group-header {
            background-color: #f8f9fa !important;
        }
        .table-group-header td {
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #dee2e6;
        }
        .table-group-header .badge {
            font-size: 0.7em;
        }
        .table tbody tr:hover {
            background-color: rgba(0,0,0,.02);
        }
        </style>
    `);

        $('#auditscheduleid').on('click', function() {
            currentScheduleId = $(this).val();
            currentInstId = $(this).data('instid');
            currentTeamHeadUserId = $(this).data('teamhead_userid');
            currentTeamMembersUserIds = $(this).data('team_members_userids');
            currentSchTeamMemberId = $(this).data('schteammemberid');

            $('#workAllocationAccordion').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading work allocation data...</p>
            </div>
        `);

            $('#teamInfoSection').show();

            const modal = new bootstrap.Modal(document.getElementById('workAllocationModal'));
            modal.show();

            fetchWorkAllocationData(currentScheduleId, currentInstId, currentTeamHeadUserId,
                currentTeamMembersUserIds);
        });

        function fetchWorkAllocationData(scheduleId, instId, teamHeadUserId, teamMembersUserIds) {
            $.ajax({
                url: '{{ route('inspection.workallocationlist') }}',
                type: 'POST',
                data: {
                    scheduleid: scheduleId,
                    instid: instId,
                    teamhead_userid: teamHeadUserId,
                    team_members_userids: teamMembersUserIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        populateWorkAllocationData(response.data);
                    } else {
                        showError('Failed to load work allocation data.');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showError('An error occurred while fetching data.');
                }
            });
        }
        let diaryDataLoaded = false;


        function fetchDiaryData() {

            if (diaryDataLoaded || $('#diaryContent').find('.spinner-border').length > 0) {
                return;
            }

            $('#diaryContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading diary entries...</p>
            </div>
        `);

            $.ajax({
                url: '/auditdiary/fetchalluserdiary',
                type: 'POST',
                data: {
                    schedule: currentScheduleId,
                    member: currentSchTeamMemberId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    diaryDataLoaded = true;
                    if (response && response.data && response.data.length > 0) {
                        populateDiaryData(response.data);
                    } else {
                        $('#diaryContent').html(`
                        <div class="alert alert-info m-3">
                            No diary entries found.
                        </div>
                    `);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    $('#diaryContent').html(`
                    <div class="alert alert-danger m-3">
                        An error occurred while loading diary entries.
                    </div>
                `);
                }
            });
        }

        $('#workAllocationModal').on('hidden.bs.modal', function() {
            diaryDataLoaded = false;
        });

        function populateDiaryData(diaryData) {
            const diaryByUser = groupDiaryByUser(diaryData);

            if (diaryByUser && diaryByUser.length > 0) {
                $('#diaryContent').html(generateDiaryAccordion(diaryByUser));
            } else {
                $('#diaryContent').html(`
                <div class="alert alert-info m-3">
                    No diary entries found.
                </div>
            `);
            }
        }

        function groupDiaryByUser(diaryData) {
            const userMap = {};

            diaryData.forEach(entry => {
                if (!entry || typeof entry !== 'object') {
                    console.warn('Invalid diary entry:', entry);
                    return;
                }

                const userId = entry.username || 'N/A';
                const userName = entry.username || 'Unknown User';
                const userDesignation = entry.desigelname || '';
                const isTeamHead = entry.auditteamhead === 'Y';

                if (!userMap[userId]) {
                    userMap[userId] = {
                        userid: userId,
                        username: userName,
                        desigelname: userDesignation,
                        isTeamHead: isTeamHead,
                        diaryEntries: [],
                        groups: {}
                    };
                }

                const diaryEntry = {
                    diaryid: entry.diaryid || '',
                    groupename: entry.groupename || 'Uncategorized',
                    grouptname: entry.grouptname || '',
                    majorworkallocationtypeename: entry.majorworkallocationtypeename || '-',
                    fromdate: entry.fromdate ? formatDate(entry.fromdate) : '-',
                    remarks: entry.remarks || '-',
                    percentofcompletion: entry.percentofcompletion != null ? entry
                        .percentofcompletion : 0
                };

                userMap[userId].diaryEntries.push(diaryEntry);

                const groupKey = entry.groupename || 'Uncategorized';
                if (!userMap[userId].groups[groupKey]) {
                    userMap[userId].groups[groupKey] = {
                        groupename: entry.groupename || 'Uncategorized',
                        grouptname: entry.grouptname || '',
                        entries: []
                    };
                }
                userMap[userId].groups[groupKey].entries.push(diaryEntry);
            });

            const userArray = Object.values(userMap);
            userArray.sort((a, b) => {
                if (a.isTeamHead && !b.isTeamHead) return -1;
                if (!a.isTeamHead && b.isTeamHead) return 1;
                return a.username.localeCompare(b.username);
            });

            return userArray;
        }

        function generateDiaryAccordion(diaryByUser) {
            let accordionHTML = `
            <div class="accordion" id="diaryAccordion">
        `;

            diaryByUser.forEach((user, index) => {
                const isExpanded = index === 0;
                const entryCount = user.diaryEntries ? user.diaryEntries.length : 0;
                const teamHeadBadge = user.isTeamHead ?
                    '<span class="badge bg-warning ms-2">Team Head</span>' : '';


                accordionHTML += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="diaryHeading${index}">
                        <button class="accordion-button ${isExpanded ? '' : 'collapsed'}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#diaryCollapse${index}"
                                aria-expanded="${isExpanded}" aria-controls="diaryCollapse${index}">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <span>
                                    <strong>${user.username}</strong>
                                    ${user.desigelname ? `<small class="text-muted ms-2">(${user.desigelname})</small>` : ''}
                                    ${teamHeadBadge}
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="diaryCollapse${index}" class="accordion-collapse collapse ${isExpanded ? 'show' : ''}"
                         aria-labelledby="diaryHeading${index}" data-bs-parent="#diaryAccordion">
                        <div class="accordion-body">
                            ${entryCount > 0 ? generateGroupedTable(user.groups) : '<p class="text-muted">No diary entries</p>'}
                        </div>
                    </div>
                </div>
            `;
            });

            accordionHTML += `</div>`;
            return accordionHTML;
        }

        function generateGroupedTable(groups) {
            // Get group names and sort them
            const groupKeys = Object.keys(groups);
            groupKeys.sort(); // Sort groups alphabetically

            let tableHTML = `
        <div class="table-responsive diary-table-container" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-hover mb-0 diary-table" style="margin-bottom: 0;">
                <thead class="table-light" style="z-index: 10; background: #f8f9fa;">
                    <tr>
                        <th width="50" class="text-center" style="background: #f8f9fa;">S.No</th>
                        <th style="background: #f8f9fa;">Grouping</th>
                        <th style="background: #f8f9fa;">Allocated Work</th>
                        <th width="120" style="background: #f8f9fa;">Date</th>
                        <th style="background: #f8f9fa;">Remarks</th>
                        <th width="100" class="text-center" style="background: #f8f9fa;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
    `;

            let rowNumber = 1;
            let hasEntries = false;

            groupKeys.forEach((groupKey, groupIndex) => {
                const group = groups[groupKey];
                const entries = group.entries || [];

                if (entries.length > 0) {
                    hasEntries = true;

                    // Optional: Add group separator row (uncomment if you want it)
                    /*
                    tableHTML += `
                        <tr class="group-separator">
                            <td colspan="6" class="bg-light fw-semibold py-2">
                                ${group.groupename || 'Uncategorized'}
                                ${group.grouptname ? `<span class="text-muted small ms-2">(${group.grouptname})</span>` : ''}
                                <span class="badge bg-secondary float-end">${entries.length} entries</span>
                            </td>
                        </tr>
                    `;
                    */

                    entries.forEach((entry, entryIndex) => {
                        const progressPercentage = parseInt(entry.percentofcompletion) || 0;
                        const progressClass = getProgressClass(progressPercentage);
                        const remarks = entry.remarks || '-';
                        const shouldShowMore = remarks.length > 100;

                        // Alternate row colors for better readability
                        const rowClass = groupIndex % 2 === 0 ? 'group-even' : 'group-odd';

                        tableHTML += `
                    <tr class="${rowClass}">
                        <td class="text-center align-middle">${rowNumber++}</td>
                        <td class="align-middle">
                            <div class="fw-semibold">${entry.groupename || '-'}</div>
                            ${entry.grouptname ? `<div class="text-muted small">${entry.grouptname}</div>` : ''}
                        </td>
                        <td class="align-middle">${entry.majorworkallocationtypeename || '-'}</td>
                        <td class="align-middle">${entry.fromdate || '-'}</td>
                        <td class="align-middle">
                            <div class="remarks-text">
                                ${escapeHtml(remarks)}
                            </div>
                            ${shouldShowMore ?
                                `<button class="btn btn-sm btn-link p-0 show-more-btn" data-remarks="${escapeHtml(remarks)}">Show more</button>` :
                                ''}
                        </td>
                        <td class="text-center align-middle">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${progressClass}" role="progressbar"
                                     style="width: ${progressPercentage}%"
                                     aria-valuenow="${progressPercentage}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    ${progressPercentage}%
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                    });
                }
            });

            if (!hasEntries) {
                tableHTML += `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No diary entries available
                </td>
            </tr>
        `;
            }

            tableHTML += `
                </tbody>
            </table>
        </div>
    `;

            return tableHTML;
        }

        // Update your CSS styles
        const tableContainerStyles = `
<style>
.diary-table-container {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.diary-table {
    font-size: 0.875rem;
    margin-bottom: 0 !important;
}

.diary-table thead th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
}

.diary-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.group-separator {
    background-color: #e9ecef !important;
}

.group-separator td {
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-bottom: 2px solid #ced4da;
}

.group-even {
    background-color: #f8f9fa;
}

.group-odd {
    background-color: #ffffff;
}

.remarks-text {
    max-height: 60px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    line-height: 1.4;
}

.show-more-btn {
    font-size: 0.75rem;
    text-decoration: none;
    padding: 0;
    margin-top: 2px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
    min-width: 80px;
}

.progress-bar {
    font-size: 0.7rem;
    line-height: 20px;
    border-radius: 4px;
}

/* Custom scrollbar for table container */
.diary-table-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.diary-table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.diary-table-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.diary-table-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ensure table cells don't break layout */
.diary-table td, .diary-table th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.diary-table td:nth-child(2), /* Grouping column */
.diary-table td:nth-child(3), /* Allocated Work column */
.diary-table td:nth-child(5) { /* Remarks column */
    white-space: normal;
    max-width: 200px;
    min-width: 150px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .diary-table-container {
        max-height: 300px;
    }

    .diary-table td:nth-child(2),
    .diary-table td:nth-child(3),
    .diary-table td:nth-child(5) {
        max-width: 150px;
        min-width: 120px;
    }
}
</style>
`;

        // Append styles to head
        $(document).ready(function() {
            if (!$('#table-container-styles').length) {
                $('head').append(`<style id="table-container-styles">${tableContainerStyles}</style>`);
            }
        });

        // Helper functions that were missing
        function getProgressClass(percentage) {
            if (percentage >= 100) return 'bg-success';
            if (percentage >= 75) return 'bg-info';
            if (percentage >= 50) return 'bg-primary';
            if (percentage >= 25) return 'bg-warning';
            return 'bg-danger';
        }

        function formatDate(dateString) {
            if (!dateString || dateString === 'null' || dateString === 'undefined') return '-';

            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                return `${day}/${month}/${year}`;
            } catch (e) {
                console.warn('Date formatting error:', e, 'for date:', dateString);
                return dateString;
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        function populateWorkAllocationData(workData) {
            $('#workAllocationAccordion').empty();

            if (workData && workData.length > 0) {
                const {
                    teamHead,
                    teamMembers
                } = separateTeamHeadAndMembers(workData);

                populateTeamInfo(teamHead, teamMembers);

                if (teamHead || teamMembers.length > 0) {
                    $('#workAllocationAccordion').html(generateTeamAccordion(teamHead, teamMembers));
                } else {
                    $('#workAllocationAccordion').html(`
                    <div class="alert alert-info m-3">
                        No work allocation data found.
                    </div>
                `);
                }
            } else {
                $('#workAllocationAccordion').html(`
                <div class="alert alert-info m-3">
                    No work allocation data found.
                </div>
            `);
            }
        }

        function populateTeamInfo(teamHead, teamMembers) {
            if (teamHead) {
                $('#teamHeadName').html(`
                ${teamHead.username}
                ${teamHead.desigelname ? `<small class="text-muted">(${teamHead.desigelname})</small>` : ''}
            `);
            } else {
                $('#teamHeadName').text('-');
            }

            if (teamMembers.length > 0) {
                const memberList = teamMembers.map(member => `
                <div>${member.username}${member.desigelname ? ` <small class="text-muted">(${member.desigelname})</small>` : ''}</div>
            `).join('');
                $('#teamMembers').html(memberList);
            } else {
                $('#teamMembers').text('-');
            }

            $('#teamInfoSection').show();
        }

        function separateTeamHeadAndMembers(workData) {
            let teamHead = null;
            const teamMembers = [];

            workData.forEach(item => {
                const user = processUserData(item);

                if (item.isteamhead === 'Y') {
                    teamHead = user;
                } else {
                    teamMembers.push(user);
                }
            });

            teamMembers.sort((a, b) => a.username.localeCompare(b.username));

            return {
                teamHead,
                teamMembers
            };
        }

        function processUserData(item) {
            const user = {
                userid: item.userid,
                username: item.work && item.work.length > 0 ? item.work[0].username : 'Unknown',
                usertamilname: item.work && item.work.length > 0 ? item.work[0].usertamilname : '',
                desigelname: item.work && item.work.length > 0 ? item.work[0].desigelname : '',
                desigtlname: item.work && item.work.length > 0 ? item.work[0].desigtlname : '',
                isteamhead: item.isteamhead || 'N',
                groups: {}
            };

            if (item.work && Array.isArray(item.work)) {
                item.work.forEach(workItem => {
                    const groupKey = workItem.groupename || 'Uncategorized';
                    const workTypeKey = workItem.majorworkallocationtypeename || 'Unknown Work Type';

                    if (!user.groups[groupKey]) {
                        user.groups[groupKey] = {
                            name: groupKey,
                            tamilName: workItem.grouptname || '',
                            workTypes: new Set()
                        };
                    }

                    user.groups[groupKey].workTypes.add(workTypeKey);
                });
            }

            const groupsArray = Object.values(user.groups).map(group => ({
                ...group,
                workTypes: Array.from(group.workTypes)
            }));

            return {
                ...user,
                groups: groupsArray
            };
        }

        function generateTeamAccordion(teamHead, teamMembers) {
            let accordionHTML = '';

            if (teamHead) {
                accordionHTML += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="teamHeadHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#teamHeadCollapse" aria-expanded="true" aria-controls="teamHeadCollapse">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <span>
                                    <strong>${teamHead.username}</strong>
                                    ${teamHead.desigelname ? `<small class="text-muted ms-2">(${teamHead.desigelname})</small>` : ''}
                                    <span class="badge bg-warning ms-2">Team Head</span>
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="teamHeadCollapse" class="accordion-collapse collapse show" aria-labelledby="teamHeadHeading" data-bs-parent="#workAllocationAccordion">
                        <div class="accordion-body">
                            ${generateUserTable(teamHead)}
                        </div>
                    </div>
                </div>
            `;
            }

            if (teamMembers.length > 0) {
                teamMembers.forEach((member, index) => {
                    accordionHTML += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="memberHeading${index}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#memberCollapse${index}" aria-expanded="false" aria-controls="memberCollapse${index}">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <span>
                                        <strong>${member.username}</strong>
                                        ${member.desigelname ? `<small class="text-muted ms-2">(${member.desigelname})</small>` : ''}
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="memberCollapse${index}" class="accordion-collapse collapse" aria-labelledby="memberHeading${index}" data-bs-parent="#workAllocationAccordion">
                            <div class="accordion-body">
                                ${generateUserTable(member)}
                            </div>
                        </div>
                    </div>
                `;
                });
            }

            return accordionHTML;
        }

        function generateUserTable(user) {
            return `
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">S.No</th>
                            <th>Group</th>
                            <th>Work Allocation</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${generateGroupRows(user.groups)}
                    </tbody>
                </table>
            </div>
        `;
        }

        function generateGroupRows(groups) {
            let rows = '';
            let serialNumber = 1;

            groups.forEach(group => {
                const workTypesList = group.workTypes.map(workType =>
                    `<div class="work-type-item">${workType}</div>`
                ).join('');

                rows += `
                <tr>
                    <td class="text-center align-middle">${serialNumber++}</td>
                    <td class="align-middle">
                        <strong>${group.name}</strong>
                        ${group.tamilName ? `<div class="text-muted small">${group.tamilName}</div>` : ''}
                    </td>
                    <td>
                        <div class="work-types-container">
                            ${workTypesList}
                        </div>
                    </td>
                </tr>
            `;
            });

            return rows;
        }

        function showError(message) {
            $('#workAllocationAccordion').html(`
            <div class="alert alert-danger m-3">
                ${message}
            </div>
        `);
        }

        // Tab switching handler
        $(document).on('click', '#diary-tab', function() {
            if ($('#workAllocationModal').hasClass('show')) {
                fetchDiaryData();
            }
        });

        // Event delegation for dynamic elements
        $(document).on('click', '.show-more-btn', function() {
            const remarks = $(this).data('remarks');
            const modal = new bootstrap.Modal(document.getElementById('remarksModal'));
            $('#remarksModal .modal-body').html(`
            <div class="remarks-full-text" style="white-space: pre-wrap; max-height: 400px; overflow-y: auto;">
                ${remarks}
            </div>
        `);
            modal.show();
        });
    });
    /*************************************************  Inspection Workallocation & Diary  *********************************************/

</script>


@endsection
