@extends('index2')
@section('content')
    @include('common.alert')
@section('title', 'Check list for Planning')
<style>
    #example-finalise.wizard>.steps>ul>li {
        width: 50%;
        float: left;
    }


    .count_style {
        color: #6e91e3 !important;
        font-weight: bolder;
    }

    .wizard>.steps>ul>li {
        width: 13%;
        float: left;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .userCountTable thead tr th,
    .instTable thead tr th {
        background-color: #707070 !important;
        /* Darker shade for header */
        color: #fff !important;
        /* Ensure text remains white */
        border: 1px solid #5e5c5c !important;
    }

    .userCountTable tbody tr td,
    .instTable tbody tr td {
        border: 1px solid #5e5c5c !important;
    }

    .hide-finish {
        display: none !important;

    }

    .alert_container {
        text-align: center;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 400px;
    }

    h1 {
        color: #dc3545;
    }

    p {
        font-size: 18px;
    }
</style>
@php
    $sessioncharge = session('charge');
    $sessiondeptcode = $sessioncharge->deptcode;

    $compact_details = json_decode($dist_details, true);

    $redo_time = $redo_time;
    $currentplanname = $currentplanname;

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">

<div class="col-12">
    <div class="spinner-wrapper d-none" id="loader">
        <div class="dot-spinner">
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
        </div>
    </div>
    <form id="checklist" name = "checklist">
        <div class="card card_border">
            <div class="card-header card_header_color lang">Auditplan Allocation </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 ">
                        <label class="form-label required lang" key="dept">Department</label>
                        <input type="text" class="form-control lang_value" id="deptcode" name="deptcode"
                            data-en="{{ $compact_details[0]['deptesname'] ?? '' }}"
                            data-ta="{{ $compact_details[0]['deptesname'] ?? '' }}"
                            value="{{ $compact_details[0]['deptesname'] ?? '' }}" disabled>
                    </div>



                    <div class="col-md-3 ">
                        <label class="form-label required lang" key="region">Region</label>
                        <select class="form-select" id="regioncode" name="regioncode" onchange = "onchange_region()">
                            <option value="">--Select Region--</option>
                            @foreach ($compact_details as $region)
                                <option value="{{ $region['regioncode'] }}" data-name-en="{{ $region['regionename'] }}"
                                    data-name-ta="{{ $region['regiontname'] }}">
                                    {{ $region['regionename'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 ">
                        <label class="form-label required lang" key="district">District</label>
                        <select class="form-select" id="distcode" name="distcode">
                            <option value="">--Select District--</option>

                        </select>
                    </div>
                </div>

                <div class="row justify-content-center mt-4" id="buttonset">
                    <div class="col-md-2 mx-auto">
                        <input type="hidden" name="action" id="action" value="" />
                        <button class="btn bg-primary text-light mt-3 lang" key="submit" id="buttonaction"
                            name="buttonaction">Submit</button>
                        <button class="btn bg-success bg-warning text-light lang mt-3" key="clear" id="clearbtn"
                            onclick = "reset_form()">
                            clear
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div id="checklist_div" class="hide_this">

        {{-- Not Completed QT - Alert Div --}}
        <div class="col-md-6 justify-content-center hide_this" id="alert_div">
            <div class="card justify-content-center">
                <div class="col-md-8 mx-auto p-3">
                    <div class="justify-content-center align-items-center">
                        <h1>Alert</h1>

                        <p>Please Complete the pending task</p>
                    </div>



                </div>
            </div>
        </div>
        {{-- Checklist Div to conduct the checklist- button --}}
        <div class="justify-content-center hide_this" id="assignteambtn_div">
            <div class="card card_border  ">
                <div class="card-header card_header_color lang" key="">Checklist for {{ $currentplanname }}
                </div>
                <div class="col-md-4 mx-auto p-3">
                    <button type="button" id="conduct_checklist" key=""
                        class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center lang">
                        Generate Checklist
                    </button>
                </div>
            </div>
        </div>
        {{-- Assigned Teams showing after team assigned with REDO --}}
        <div class="hide_this" id="data_teamassigned_div">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="">Auditplan Allocation for <span
                        id="planname_div"></span> </div>
                <div class="  mt-1 mb-2 p-2" id="count_det">
                </div>
                <div class="card-body" id="desig_countDetails">
                    <div class=" table-responsive rounded-2 border ">
                        <table class="table userCountTable">
                            <thead class="">
                                <tr>
                                    <th class="lang userCount_head" key="s_no">S No</th>
                                    <th class="lang userCount_head" key="designation">Designation Name
                                    </th>
                                    <th class="lang userCount_head" key="count">Count</th>
                                </tr>
                            </thead>
                            <tbody id="designationTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="card-body">
                    <div id="example-basic" class="mt-1 ">
                        <h3 class="lang" key="">Team Compositions<br>

                        </h3>
                        <section class="mb-2">
                            <div class="card card-border mb-2"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key=""> Team Compositions
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_teamdetails">
                                            <table id="auditteamdetailstable"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="team">Team Name</th>
                                                        <th class="lang" key="teamhead">Team Head</th>
                                                        <th class="lang" key="">Team Members</th>
                                                        <th class="lang" key="">Team Size</th>
                                                        <th class="lang" key="">Distance Category</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_teamdetails" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <h3 class="lang" key="">Allocation Details<br>

                        </h3>
                        <section class="mb-2">
                            <div class="card card-border mb-2"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key=""> Allocation Details
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_allocation">
                                            <table id="auditteamtable"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="inst">Institution</th>
                                                        <th class="lang" key="team">Team Name</th>
                                                        <!-- <th class="lang" key="teamhead">Team Head</th>
                                                    <th class="lang" key="">Team Members</th> -->
                                                        <th class="lang" key="">Assigned Date</th>
                                                        <th class="lang" key="">Team Size</th>
                                                        <th class="lang" key="">Distance Category</th>
                                                        <th class="lang" key="">Mandays</th>
                                                        <th class="lang" key="auditmode">Audit Mode</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_allocation" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h3 class="lang" key="">Allocation Details<br>
                            &nbsp; &nbsp; (Template Audit)
                        </h3>
                        <section class="mb-2">
                            <div class="card card-border mb-2"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key=""> Allocation Details
                                    (Template Audit)
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_tempdetails">
                                            <table id="allocdettemplate"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="">User</th>
                                                        <th class="lang" key="team">Period</th>
                                                        <th class="lang" key="mandays">Mandays</th>
                                                        <th class="lang" key="">Institution Count</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_templatedetails" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h3 class="lang" key=""> Auditor Status <br> (Performance Audit Period)</h3>
                        <section id ="per_audit_sec">
                            <div class="card card-border"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key="">Auditor Status
                                    (Performance
                                    Audit Period)
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_performanceauditors">
                                            <table id="performanceauditorstatus_table"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="">User Name
                                                        <th class="lang" key="">Engagement Period</th>
                                                        <th class="lang" key="">Status</th>
                                                        <th class="lang" key="">Total Audit Days</th>
                                                        <th class="lang" key="">Allotted Days</th>
                                                        <th class="lang" key="">Idle Days</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_performance_auditors" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h3 class="lang" key=""> Auditor Status <br> (Regular & Template)</h3>
                        <section>
                            <div class="card card-border"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key="">Auditor Status
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_idleauditors">
                                            <table id="idleauditorstable"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="">User Name
                                                        <th class="lang" key="">Engagement Period</th>
                                                        <th class="lang" key="">Status</th>
                                                        <th class="lang" key="">Total Audit Days</th>
                                                        <th class="lang" key="">Alloted Days</th>
                                                        <th class="lang" key="">Idle Days</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_idleauditors" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <h3 class="lang" key="">Idle Institution Details <br>
                            <center>(Regular Audit)</center>
                        </h3>
                        <section>
                            <div class="card card-border"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key="">Idle Institution
                                    Details
                                    (Regular Audit)
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_idleinstitution">
                                            <table id="idleinsttable"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="inst">Institution</th>
                                                        <th class="lang" key="Mandays">Mandays</th>
                                                        <th class="lang" key="">Distance Category</th>
                                                        <th class="lang" key="">Carry Forward</th>
                                                        <th class="lang" key="teamsize">Teamsize</th>
                                                        {{-- <th class="lang" key="">Rank Order</th> --}}

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Dynamically populated rows will go here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_idleinstitution" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h3 class="lang" key="">Idle Institution Details (Template Audit)</h3>
                        <section>
                            <div class="card card-border"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key="">Template Idle
                                    Institution
                                    Details
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_templateidleinstitution">
                                            <table id="templateidleinsttable"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="inst">Institution
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_templateidleinstitution" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">

                <div class="col-md-6 text-start">
                    <div id="lastrun_timer" class="text-primary fw-bold mt-2 p-2"></div>
                </div>
                <div class="col-md-6 text-end">
                    <div id="redo_timer" class="text-danger fw-bold mt-2 p-2"></div>
                </div>

                <!-- Right Side (Last Runtime) -->
                <div class="col-md-6 text-end">
                    <div id="lastrun_timer" class="text-primary fw-bold mt-2 p-2"></div>
                </div>

            </div>


            <div class="row justify-content-center text-center" id="buttonset">
                <div class="col-md-6 ">
                    <button class="btn btn-primary mt-3 lang" key="" type="button" id="redo_checklist"
                        name="redo_checklist"> Redo
                    </button>
                    <button class="btn btn-success mt-3 lang" key="" type="button" id="finalise_btn"
                        name="finalise_btn">Finalise
                    </button>

                </div>


            </div>
        </div>
        {{-- After Finalized Table Div --}}
        <div class="hide_this" id="finalised_plandiv">
            <div class="card card-botder">
                <div class="card-body">
                    <div id="example-finalise" class="mt-1 ">

                        <h3 class="lang" key="">Audit Team and Auditable institution Details (Regular Audit)
                        </h3>
                        <section class="mb-2">
                            <div class="card card_border mt-2 " id="finalised_plandiv_table">
                                <div class="card-header card_header_color lang" key="autoplan_head">Audit Team and
                                    Institiute Details
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow">
                                            <table id="team_Inst_Details"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <th class="lang text-center align-middle" key="s_no">S.No</th>
                                                    <th class="lang " key="inst_name">Institute Name </th>
                                                    <th class="lang " key="teamhead_label">Team Head</th>
                                                    <th class="lang " key="teammember_label">Total Members</th>
                                                    <th class="lang " key="teamsize">Total Size</th>
                                                    <th class="lang " key="">Spill over</th>
                                                    <th class="lang " key="mandays">Total Mandays</th>
                                                    <th class="lang " key="">Date</th>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id='no_data' class='hide_this'>
                                        <center>No Data Available</center>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <h3 class="lang" key="">Audit Team and Auditable institution Details (Template
                            Audit)
                        </h3>
                        <section class="mb-2">
                            <div class="card card-border mb-2"
                                style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                                <div class="card-header card_header_color lang" key=""> Allocation Details
                                    (Template Audit)
                                </div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive " id="tableshow_allocation">
                                            <table id="finalise_template"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="inst">Institution</th>
                                                        <th class="lang" key="team">Team Name</th>
                                                        <th class="lang" key="category">Category</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="no_data_allocation" class="hide_this">
                                            <center>No Data Available</center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>





                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/js/forms/form-wizard.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>
<script src="../assets/js/datatable/datatable-advanced.init.js"></script>


<script>
    //------------variable dclaration----------------//

    let totalteamDetails = '';
    let teamDetails = '';
    let idleAuditorsDetail = '';
    let idleInstitutionDetails = '';
    let totalInstitutionDetails = '';

    let templateInstitutionDetails
    let totalAuditorDetails = '';
    let auditors_det = '';
    let executingquartercode = '';
    let template_det = '';
    let performance_institution_list = '';
    let performanceAuditorlist = '';
    let plan_preferences = '';
    let plan_items = '';
    let plannedinst_det = '';
    let idleinst_det = '';

    let redo_time = '<?php echo $redo_time; ?>';
    let redoTimerInterval = null;

    let prioritycode = '';
    let verifiedplandetails = '';
    let planquarter = '';
    let planmappingid = '';






    //--------------------------------Change Language--------------------------------//

    $("#translate").change(function() {
        lang = getLanguage('Y');
        switchChecklistLanguage(lang)
    });

    function switchChecklistLanguage(lang) {

        $('.lang_value').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            $(this).val(lang === 'ta' ? ta : en);
        });
    }
    //----------------------------------Form validation----------------------------//
    $(document).ready(function() {

        // Validation
        $("#checklist").validate({
            rules: {
                distcode: {
                    required: true
                },
                regioncode: {
                    required: true
                }
            },
            messages: {
                distcode: {
                    required: "Please select District"
                },
                regioncode: {
                    required: "Please select Region"
                }
            },
            submitHandler: function(form) {

                getchecklistdata();
                return false;
            }
        });


    });

    function reset_form() {

        $("#distcode,#regioncode").val("");

        toggle_form('Y')

        $("#checklist").validate().resetForm();
        $("#distcode,#regioncode").removeClass("error");

        $("#checklist_div").hide()

        $("#deptcode").prop("disabled", true);
        $("#regioncode,#distcode").prop("disabled", false);

        if (redoTimerInterval) {
            clearInterval(redoTimerInterval);
            redoTimerInterval = null;
        }

        $('#redo_timer').empty();
        $('#lastrun_timer').empty();

        $('#redo_checklist, #finalise_btn')
            .prop('disabled', false)
            .removeClass('disabled');
        totalteamDetails = '';
        teamDetails = '';
        idleAuditorsDetail = '';
        idleInstitutionDetails = '';
        totalInstitutionDetails = '';

        templateInstitutionDetails
        totalAuditorDetails = '';
        auditors_det = '';
        executingquartercode = '';
        template_det = '';
        performance_institution_list = '';
        performanceAuditorlist = '';
        plan_preferences = '';
        plan_items = '';
        plannedinst_det = '';
        idleinst_det = '';

        redo_time = '<?php echo $redo_time; ?>';
        redoTimerInterval = null;

        prioritycode = '';
        verifiedplandetails = '';
        planquarter = '';
        planmappingid = '';

    }

    $(document).on('input', '.otp-input', function() {
        if (this.value.length === 1) {
            $(this).next('.otp-input').focus();
        }
    });

    $(document).on('keydown', '.otp-input', function(e) {
        // Allow: Backspace, Tab, Arrow keys
        if (['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            // Handle auto-focus on backspace
            if (e.key === 'Backspace' && !this.value) {
                $(this).prev('.otp-input').focus();
            }
            return;
        }

        // Block non-numeric keys
        if (!/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }
    });
    //-------------------------------------Button Click-----------------------------//

    $(document).on('click', '#conduct_checklist', function() {
        event.preventDefault();
        assignteams()
    });

    $(document).on('click', '#redo_checklist', function() {
        event.preventDefault();

        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();
            $('#confirmation_alert').modal('hide');
            assignteams()
        });

        passing_alert_value('Confirmation', 'Are you sure to regenerate the checklist?',
            'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');
    });

    $(document).on('click', '#finalise_btn', function() {
        event.preventDefault();
        checkexitmeetstatus();


    });

    // ------------------------------------fetch datas------------------------------//
    function getchecklistdata() {
        var deptcode = '<?php echo $sessiondeptcode; ?>';
        var distcode = $('#distcode').val();
        $('#loader').removeClass('d-none');
        const language = getLanguage();

        toggle_form('N')


        $.ajax({
            url: "checklistplan/checkisteamassigned",
            method: 'POST',
            data: {
                deptcode: deptcode,
                distcode: distcode
            },

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                if (!response.success) {
                    $("#checklist_div").hide()
                    passing_alert_value(
                        'Confirmation',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                    return;
                }

                $("#checklist_div").show()

                if (response.planstatus === 'F') {
                    toggle_checklistdiv(response.planstatus)
                    handleFinalizedPlan(response, language);
                    return;
                }

                startRedoTimer(
                    response.lastplanruntime,
                    redo_time
                );
                if (response.teamassignedstatus) {
                    toggle_checklistdiv('Y')
                    verifiedplandetails = response.verifiedplandetails;

                    verifiedplandetails != 'F' ? $('#redo_checklist').show() : $('#redo_checklist').hide()



                    loadAssignmentData(response, language, true);

                    if (
                        response.performance_idlelist?.length > 0 &&
                        response.performanceinstcount > 0
                    ) {



                        $('#tableshow_performanceauditors').show();
                        $('#no_data_performance_auditors').hide();

                        performanceAuditorlist = response.performance_idlelist;
                        renderPerformanceIdleAuditors(language);
                    } else {
                        toggleSection(
                            '#tableshow_performanceauditors',
                            '#no_data_performance_auditors',
                            response.performanceinstcount?.length > 0
                        );
                    }

                } else if (
                    response.pendinginststatus === 'Y' &&
                    response.allocdet_temp
                ) {
                    toggle_checklistdiv('Y')
                    loadAssignmentData(response, language, false);

                } else if (
                    response.pendinginststatus === 'Y'
                ) {
                    toggle_checklistdiv('P')


                } else {
                    toggle_checklistdiv('N')


                }


            },
            complete: function() {
                $('#loader').addClass('d-none');
            },
            error: function(xhr, status, error) {

                let code = xhr.status;
                let message = '';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    message = xhr.responseText;
                } else {
                    message = 'Something went wrong';
                }

                if (code == 403) {

                    $('#ok_button')
                        .off('click')
                        .on('click', function(event) {
                            event.preventDefault();
                            $('#confirmation_alert').modal('hide');

                            handleUnauthorizedError();
                        });

                    passing_alert_value(
                        'Confirmation',
                        message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                } else {
                    console.error('Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            }
        });
    }

    function assignteams() {
        var deptcode = '<?php echo $sessiondeptcode; ?>';
        var distcode = $('#distcode').val();

        $('#loader').removeClass('d-none');

        $.ajax({
            url: "checklistplan/assignteams",
            method: 'POST',
            data: {
                deptcode: deptcode,
                distcode: distcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content')
            },
            success: function(response) {

                if (!response.success) {
                    return;
                }

                const {
                    status,
                    message
                } = response.data;

                if (status !== 'Success') {
                    passing_alert_value(
                        'Alert',
                        message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                    return;
                }

                if (response.teamassignedstatus) {
                    passing_alert_value(
                        'Alert',
                        'Checklist has been generated successfully',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    getchecklistdata();

                } else {
                    passing_alert_value(
                        'Alert',
                        'Please check the Institution master. Distance / Plan type is not updated properly',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }

            },
            complete: function() {
                $('#loader').addClass('d-none');
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.code == 403) {

                    $('#ok_button')
                        .off('click')
                        .on('click', function(event) {
                            event.preventDefault();
                            $('#confirmation_alert').modal('hide');

                            handleUnauthorizedError();
                        });

                    passing_alert_value(
                        'Confirmation',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                } else {
                    passing_alert_value('Alert', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }

            }
        });
    }

    function onchange_region() {
        var deptcode = '<?php echo $sessiondeptcode; ?>';
        var regioncode = $('#regioncode').val();




        if (regioncode) {
            $('#distcode').empty();
            // $('#loader').removeClass('d-none');
            $.ajax({
                url: "checklistplan/chk_dist",
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    regioncode: regioncode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content')
                },
                success: function(response) {
                    $('#distcode').empty();
                    $('#distcode').append('<option value="">--Select District--</option>');

                    $.each(response, function(index, item) {
                        $('#distcode').append(
                            '<option value="' + item.distcode + '">' +
                            item.distename +
                            '</option>'
                        );
                    });

                },
                // complete: function() {
                //     $('#loader').addClass('d-none');
                // },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    passing_alert_value('Alert', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                }
            });
        }

    }

    //-------------------------------Render Table ----------------------------------//
    function renderPerformanceIdleAuditors(language) {
        //  const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#performanceauditorstatus_table')) {
            $('#performanceauditorstatus_table').DataTable().clear().destroy();
        }

        var table = $('#performanceauditorstatus_table').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": performanceAuditorlist,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: "username",
                    title: columnLabels?.["username"]?.[language] || 'User',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.username || '-';
                    }
                },
                {
                    data: "engagement_period",
                    title: columnLabels?.["engagement_period"]?.[language] || 'Engagement Period',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.engagement_period || '-';
                    }
                },
                {
                    data: "status",
                    title: columnLabels?.["status"]?.[language] || 'Status',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.status || '-';
                    }
                },
                {
                    data: "total_audit_days",
                    title: columnLabels?.["total_audit_days"]?.[language] || 'Total Mandays',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.total_audit_days || '-';
                    }
                },
                {
                    data: "allotted_days",
                    title: columnLabels?.["allotted_days"]?.[language] || 'Allotted Days',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.allotted_days || '-';
                    }
                },
                {
                    data: "null",
                    title: 'Idle Days',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (row.total_audit_days - row.allotted_days > 0)
                            return row.total_audit_days - row.allotted_days
                        else return '-';
                    }

                },





            ],
            "initComplete": function(settings, json) {
                $("#performanceauditorstatus_table").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
    }

    function fetchAuditorsTable(lang) {


        console.log('Before DataTable');
        if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
            $('#team_Inst_Details').DataTable().clear().destroy();
        }

        let tableData = auditors_det.map((group, index) => {

            const spillOver = group.spilloverflag === 'Y';

            const totalMandays = spillOver ?
                group.remainingmandays :
                group.mandays;

            const spillOverLabel = lang === 'ta' ?
                (spillOver ? 'ஆம்' : 'இல்லை') :
                (spillOver ? 'Yes' : 'No');

            const fromDate = group.fromdate ?
                ChangeDateFormat(group.fromdate) :
                '-';

            const toDate = group.todate ?
                ChangeDateFormat(group.todate) :
                '-';

            return {
                index: index + 1,
                instColumn: lang === 'ta' ?
                    group.insttname : group.instename,

                teamHead: lang === 'ta' ?
                    group.team_head_ta : group.team_head_en,

                members: lang === 'ta' ?
                    group.team_members_ta : group.team_members_en,

                teamsize: group.teamsize || '-',
                spillOver: spillOverLabel,
                mandays: totalMandays || '-',
                auditmode: group.auditmode || '-',

                date_range: fromDate === '-' && toDate === '-' ?
                    '-' : `${fromDate} to ${toDate}`
            };
        });



        if ($.fn.DataTable.isDataTable("#team_Inst_Details")) {
            $('#team_Inst_Details').DataTable().clear().rows.add(tableData).draw();
        } else {


            $("#user_detail_table_wrapper").hide();
            $('#schedule_allocatedwork_wrapper').show();

            $('#team_Inst_Details').DataTable({
                data: tableData,

                columns: [{
                        data: "index",
                        render: function(data, type, row, meta) {
                            return `<div>
                                 <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                             </div>`;
                        },
                        className: 'text-end fw-normal',
                        type: "num"
                    },
                    {
                        data: "instColumn",
                        render: function(data, type, row) {

                            let instName = data || '-';

                            console.log(row.auditmode);

                            if (row.auditmode === 'P') {
                                return instName +
                                    ' <span class="text-primary">(Performance Audit)</span>';
                            }

                            return instName;
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },

                    {
                        data: "teamHead",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },
                    {
                        data: "members",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },
                    {
                        data: "teamsize",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },
                    {
                        data: "spillOver",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },
                    {
                        data: "mandays",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },
                    {
                        data: "date_range",
                        render: function(data) {
                            return data || '-';
                        },
                        className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                    },

                ],
                "initComplete": function(settings, json) {
                    $("#audit_autoplan").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                }

            });
            console.log('After DataTable');

            const mobileColumns = ["instColumn", "teamHead", "members", "mandays"];
            setupMobileRowToggle(mobileColumns);

            updatedatatable(lang, "team_Inst_Details");
        }


    }

    function fetchTemplateTable(language) {

        const instColumn = language === 'ta' ? 'insttname' : 'instename';

        const username = language === 'ta' ? 'usertamilname' : 'username';
        const designame = language === 'ta' ? 'desigtlname' : 'desigelname';
        const catname = language === 'ta' ? 'cattname' : 'catename';

        if ($.fn.DataTable.isDataTable('#finalise_template')) {
            $('#finalise_template').DataTable().clear().destroy();
        }

        var table = $('#finalise_template').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": template_det,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                         </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: instColumn,
                    title: columnLabels?.["instColumn"]?.[language] || 'Institution',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';
                    }
                },
                {
                    data: "username",
                    title: columnLabels?.["username"]?.[language] || 'User',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const user = row[username] + ' - ' + row[designame]
                        return user || '-';
                    }
                },
                {
                    data: "catename",
                    title: columnLabels?.["catename"]?.[language] || 'Category',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row[catname] || '-';
                    }
                },


            ],
            "initComplete": function(settings, json) {
                $("#finalise_template").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["instename", "catename", "username"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "finalise_template");
    }

    function renderTotalTeamdetails(language) {

        if ($.fn.DataTable.isDataTable('#auditteamdetailstable')) {
            $('#auditteamdetailstable').DataTable().clear().destroy();
        }

        var table = $('#auditteamdetailstable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": totalteamDetails,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: "team_name",
                    title: columnLabels?.["team_name"]?.[language] || 'Team Name',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.team_name || '-';
                    }
                },
                {
                    data: "team_head",
                    title: columnLabels?.["team_head"]?.[language] || 'Team Head',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.team_head || '-';
                    }
                },
                {
                    data: "members",
                    title: columnLabels?.["members"]?.[language] || 'Team Members',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (!row.members) return '-';
                        return row.members
                            .split(',')
                            .map(member => member.trim())
                            .join('<br>');
                    },

                },

                {
                    data: "team_size",
                    title: columnLabels?.["team_size"]?.[language] || 'Team Size',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.team_size || '-';
                    }
                },
                {
                    data: null,
                    title: columnLabels?.["totalcount"]?.[language] || 'Allotment Details',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {

                        var isPerformance = false;
                        if ((row.applydistance == 'null' || row.applydistance == '' || row
                                .applydistance == '0' || row.applydistance == 'N')) {

                            isPerformance = true;
                        }

                        const allotment_details = ` <div><b>Total:</b> ${row.totalcount ?? row.team_size ?? '-'}</div>
                                <div><b>Short Distance : </b> ${row.a_count ?? 0}</div>
                                <div><b>Medium Distance : </b> ${row.b_count ?? 0}</div>
                               `;

                        // <div><b>Long Distance : </b> ${row.c_count ?? 0}</div>


                        return `
                               ${isPerformance?`-`:allotment_details}
                            `;
                    }
                }






            ],
            "initComplete": function(settings, json) {
                $("#auditteamdetailstable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        updatedatatable(language, "auditteamdetailstable");
    }

    function renderTeamdetails(language) {
        const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#auditteamtable')) {
            $('#auditteamtable').DataTable().clear().destroy();
        }

        var table = $('#auditteamtable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": teamDetails,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: instColumn,
                    title: columnLabels?.[instColumn]?.[language] || "Institution",
                    render: function(data, type, row) {
                        let isSpillover;
                        // if (row.spillover == 'Y') {
                        if (
                            row.spillover === 'Y' &&
                            (row.auditmode == null || row.auditmode === 'N')
                        ) {
                            isSpillover = true;
                        } else {
                            isSpillover = false;
                        }
                        return `${row[instColumn]}
                       ${isSpillover?'<small style="color:red"> (spill over) </small>':''}`;
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: "team_name",
                    title: columnLabels?.["team_name"]?.[language] || 'Team Name',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.team_name || '-';
                    }
                },

                {
                    data: 'from_date',
                    title: columnLabels?.['from_date']?.[language] || "Proposed Audit Period",
                    render: function(data, type, row) {
                        const isValidDate = (d) => {
                            const date = new Date(d);
                            return d && !isNaN(date);
                        };

                        const fromDate = isValidDate(row.from_date) ? new Date(row.from_date)
                            .toLocaleDateString('en-GB') : "N/A";
                        const toDate = isValidDate(row.to_date) ? new Date(row.to_date)
                            .toLocaleDateString('en-GB') : "N/A";

                        return `${fromDate} - ${toDate}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"

                },
                {
                    data: "team_size",
                    title: columnLabels?.["team_size"]?.[language] || 'Team Size',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.team_size || '-';
                    }
                },
                {
                    data: "mandays",
                    title: columnLabels?.["mandays"]?.[language] || 'Mandays',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.mandays || '-';
                    }
                },
                {
                    data: "distance",
                    title: columnLabels?.["distance"]?.[language] || 'Distance Category',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        // return row.distance || '-';
                        const mode = row.auditmode == 'P' ? '-' : row.distance
                        return mode || '-';
                    }
                },
                {
                    data: "auditmode",
                    title: columnLabels?.["auditmode"]?.[language] || 'Audit Mode',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const mode = row.auditmode == 'P' ? 'Performance Audit' : '-'
                        return mode || '-';
                    }
                },




            ],
            "initComplete": function(settings, json) {
                $("#auditteamtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        updatedatatable(language, "auditteamtable");
    }

    function renderIdleusersdetails(language) {
        //  const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#idleauditorstable')) {
            $('#idleauditorstable').DataTable().clear().destroy();
        }

        var table = $('#idleauditorstable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": idleAuditorsDetail,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: "username",
                    title: columnLabels?.["username"]?.[language] || 'User',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.username || '-';
                    }
                },
                {
                    data: "engagement_period",
                    title: columnLabels?.["engagement_period"]?.[language] || 'Engagement Period',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.engagement_period || '-';
                    }
                },
                {
                    data: "status",
                    title: columnLabels?.["status"]?.[language] || 'Status',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.status || '-';
                    }
                },
                {
                    data: "total_audit_days",
                    title: columnLabels?.["total_audit_days"]?.[language] || 'Total Mandays',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.total_audit_days || '-';
                    }
                },
                {
                    data: "allotted_days",
                    title: columnLabels?.["allotted_days"]?.[language] || 'Allotted Days',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.allotted_days || '-';
                    }
                },
                {
                    data: "null",
                    title: 'Idle Days',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (row.total_audit_days - row.allotted_days > 0)
                            return row.total_audit_days - row.allotted_days
                        else return '-';
                    }

                },





            ],
            "initComplete": function(settings, json) {
                $("#idleauditorstable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
    }

    function renderIdleInstdetails(language) {
        const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#idleinsttable')) {
            $('#idleinsttable').DataTable().clear().destroy();
        }

        var table = $('#idleinsttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": true,
            "data": idleInstitutionDetails,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: instColumn,
                    title: columnLabels?.["instColumn"]?.[language] || 'Institution',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';
                    }
                },
                // {
                //     data: "mandays",
                //     title: columnLabels?.["mandays"]?.[language] || 'Mandays',
                //     className: "d-none d-md-table-cell lang extra-column text-wrap",
                //     render: function(data, type, row) {
                //         return row.mandays || '-';
                //     }
                // },
                {
                    data: "mandays",
                    title: columnLabels?.["mandays"]?.[language] || 'Mandays',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.mandays || '-';
                    }
                },
                {
                    data: "distance",
                    title: columnLabels?.["distance"]?.[language] || 'Distance',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.distance || '-';
                    }
                },

                {
                    data: "carryforward",
                    title: columnLabels?.["carryforward"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.carryforward || '-';
                    }
                },
                {
                    data: "desigcodes",
                    title: columnLabels?.["total_audit_days"]?.[language] || 'Teamsize',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.desigcodes || '-';
                    }
                }

            ],
            "initComplete": function(settings, json) {
                $("#idleinsttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
    }

    function rendertemplateIdleInstdetails(language, data_temp) {
        const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#templateidleinsttable')) {
            $('#templateidleinsttable').DataTable().clear().destroy();
        }

        var table = $('#templateidleinsttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": data_temp,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: instColumn,
                    title: columnLabels?.["instColumn"]?.[language] || 'Institution',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';
                    }
                }


            ],
            "initComplete": function(settings, json) {
                $("#templateidleinsttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
    }
    //----------------------------------Finalize Check-------------------------------------------------//
    function ajaxRequest(url, data, successCallback, errorCallback = null) {

        $.ajax({
            url,
            type: 'POST',
            data,

            beforeSend() {
                $('#overlay-loader').show();
            },

            success: successCallback,

            error(xhr) {
                if (errorCallback) {
                    errorCallback(xhr);
                    return;
                }

                const msg =
                    xhr.responseJSON?.message ||
                    'Something went wrong.';

                showAlert('Alert', msg);
            },

            complete() {
                $('#overlay-loader').hide();
            }
        });
    }

    function showAlert(title, content, type = 'confirmation_alert') {

        passing_alert_value(
            title,
            content,
            type,
            'alert_header',
            'alert_body',
            type
        );
    }

    const csrfToken =
        $('meta[name="csrf-token"]').attr('content');

    function getRequestData() {
        return {
            _token: csrfToken,
            deptcode: '<?php echo $sessiondeptcode; ?>',
            distcode: $('#distcode').val()
        };
    }

    function getOtpHtml() {
        return `
        <div id="otp_div">

            <h5 class="text-center mb-3">
                <b>Verify Your OTP</b>
            </h5>

            <span class="text-center d-block mb-3">
                Enter 6-digit verification code sent to your mail
            </span>

            <div class="row justify-content-center">
                <div class="col-auto">

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        ${Array(6).fill(`
                            <input
                                type="text"
                                class="form-control text-center otp-input"
                                maxlength="1"
                                inputmode="numeric">
                        `).join('')}
                    </div>

                    <button
                        type="button"
                        id="verify_otp_button"
                        class="btn btn-primary w-100">
                        Verify OTP
                    </button>

                </div>
            </div>

            <small class="text-center d-block mt-3">
                Didn't receive the code?
                <b id="resend_otp_link"
                   style="cursor:pointer;color:#4f73d9">
                    Resend OTP
                </b>
            </small>

        </div>
    `;
    }

    // function sentfinaliseOTP() {

    //     $('#process_button')
    //         .prop('disabled', true);

    //     ajaxRequest(
    //         'checklistplan/sendOtp_allocateplan', {
    //             _token: csrfToken
    //         },

    //         function(response) {
    //             $('#confirmation_alert').hide();
    //             alert(response.status);

    //             console.log(response.status);
    //             console.log(typeof response.status);

    //             if (response.status !== 'success') {
    //                 console.log('Inside IF');
    //                 return;
    //             } else {
    //                 alert('afterid');



    //                 // // $('#confirmation_alert .modal-footer').hide();

    //                 // $('#process_button')
    //                 //     .text('Verify OTP')
    //                 //     .prop('disabled', false);

    //                 // console.log(getOtpHtml());

    //                 // showAlert(
    //                 //     'Confirmation',
    //                 //     getOtpHtml(),
    //                 //     'forward_alert'
    //                 // );

    //                 passing_alert_value(
    //                     'Confirmation',
    //                     'jo',
    //                     'confirmation_alert',
    //                     'alert_header',
    //                     'alert_body',
    //                     'forward_alert'
    //                 );

    //                 alert('hi');







    //             }
    //             // if (response.status !== 'success')
    //             //     return;





    //         }
    //     );
    // }



    function sentfinaliseOTP() {
        //alert('hi');
        $('#loader').show();
        $('#process_button').attr('disabled', true);
        $.ajax({
            url: 'checklistplan/sendOtp_allocateplan', // or use `{{ route('send.otp') }}` if blade
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function(response) {
                if (response.status === 'success') {
                    const otpcontent = `<div id="otp_div">
                <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mail</span><br>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        </div>
                        <button type="button" id="verify_otp_button" class="btn btn-primary w-100" >Verify OTP</button>
                    </div>
                </div><br>

                <small class="text-center mb-3 d-block">Didn't receive the code? <b  id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b></small><br>

            </div>`;
                    $('#confirmation_alert .modal-footer').hide();

                    passing_alert_value('Confirmation', otpcontent,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');

                    setTimeout(() => {
                        $('#loader').hide();
                        $('#confirmation_alert').modal('show');
                    }, 50);

                    $('#otp_div').css({
                        'text-align': 'center'
                    }).show();


                    $('#process_button').html("Verify OTP");



                }
            },

            complete: function() {

                $('#process_button').removeAttr('disabled');
            },
            error: function() {
                passing_alert_value('Alert', 'Failed to send OTP. Please try again.',
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

            }
        });

    }

    $(document).on('click', '#verify_otp_button', function() {

        const otp =
            $('.otp-input')
            .map(function() {
                return this.value;
            })
            .get()
            .join('');

        if (!/^\d{6}$/.test(otp)) {
            return alert('Enter valid OTP');
        }

        ajaxRequest(
            'checklistplan/verifyOtp_allocateplan', {
                _token: csrfToken,
                otp
            },

            function(response) {

                if (response.status !== 'success') {
                    alert(response.message);
                    return;
                }

                $('#confirmation_alert').modal('hide');

                finaliseplan();
            }
        );
    });

    $(document).on(
        'click',
        '#resend_otp_link',
        function(e) {

            e.preventDefault();

            ajaxRequest(
                'checklistplan/sendOtp_allocateplan', {
                    _token: csrfToken
                },

                () => alert('OTP resent successfully')
            );
        }
    );

    function checkexitmeetstatus() {

        ajaxRequest(
            'checklistplan/checkexitmeetstatus',
            getRequestData(),

            function(response) {

                if (response.status === 'success') {

                    check_finalise();
                }
            }
        );
    }

    function finaliseplan() {

        const language = getLanguage('');

        ajaxRequest(
            'checklistplan/finaliseplan', {
                ...getRequestData(),
                callfor: 'F'
            },

            function(response) {

                if (!response.success) {
                    return;
                }

                $('#data_teamassigned_div, #assignteambtn_div').hide();
                $('#finalised_plandiv').show();

                executingquartercode = response.executingquartercode;
                auditors_det = response.planned_auditors || [];
                template_det = response.templatedata || [];
                console.log('auditors_det', auditors_det);
                console.log($('#team_Inst_Details').length);
                setTimeout(() => {
                    fetchAuditorsTable(language);
                    fetchTemplateTable(language);

                }, 50);


                // toggle_checklistdiv('Y')
                // handleFinalizedPlan(response, language);
                // return;


            }
        );
    }

    function getFinaliseSummaryHtml(data) {

        const rows = [
            ['Users Allotted', data.usercount],
            ['Allotted Regular Audit Institutions', data.normalaudit_count],
            ['Allotted Template Audit Institutions', data.templateinst_count],
            ['Allotted Performance Audit Institutions', data.performanceaudit_count],
            ['Allotted Spillover Institutions', data.spilloverinst_count]
        ];

        return `
        <div class="col-md-12 mt-2">
            <div class="table-responsive">
                <table class="table table-bordered shadow-sm summary-table">
                    <tbody>
                        ${rows.map(([label, value]) => `
                            <tr>
                                <td class="fw-semibold">${label}</td>
                                <td class="text-end fw-bold count-text">${value}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>

        <span class="text-danger fs-3">
            * Once the plan is finalized, it cannot be revoked.
        </span>
    `;
    }

    function check_finalise() {

        ajaxRequest(
            'checklistplan/finaliseplan', {
                ...getRequestData(),
                callfor: 'C'
            },

            function(response) {

                if (!response.success) {
                    return;
                }
                $('#process_button')
                    .html('Send OTP')
                    .off('click')
                    .on('click', function(e) {

                        e.preventDefault();

                        //$('#confirmation_alert').modal('show');
                        // finaliseplan();
                        sentfinaliseOTP();
                    });
                passing_alert_value(
                    'Confirmation',
                    getFinaliseSummaryHtml(response.data),
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );


            },

            function(xhr) {

                const response = xhr.responseJSON || {};

                if (response.error == 403) {

                    $('#ok_button')
                        .off('click')
                        .on('click', function(e) {
                            e.preventDefault();
                            $('#confirmation_alert').modal('hide');
                            handleUnauthorizedError();
                        });

                    return passing_alert_value(
                        'Confirmation',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
                if (response.error == 601) {

                    const redirectUrl =
                        `data_verification/${
                            encodeURIComponent('<?php echo $sessiondeptcode; ?>')
                        }/${
                            encodeURIComponent($('#regioncode').val())
                        }/${
                            encodeURIComponent($('#distcode').val())
                        }/${
                            encodeURIComponent(planquarter)
                        }/${
                            encodeURIComponent(planmappingid)
                        }/${
                            encodeURIComponent(prioritycode)
                        }`;

                    showAcknowledgementAlert(
                        response.message,
                        redirectUrl
                    );

                    return;

                    $('#ok_button')
                        .off('click')
                        .on('click', function(e) {
                            e.preventDefault();
                            $('#confirmation_alert').modal('hide');

                        });

                    return passing_alert_value(
                        'Confirmation',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }

                passing_alert_value(
                    'Alert',
                    response.message || 'Something went wrong',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        );
    }

    function markChecklistPlanDetailsVerified(successCallback, errorCallback) {
        ajaxRequest(
            'checklistplan/mark-plan-details-verified', {
                ...getRequestData(),
                regioncode: $('#regioncode').val()
            },

            function(response) {
                if (!response.success) {
                    showAlert('Alert', response.message || 'Unable to verify checklist plan details.');
                    if (errorCallback) {
                        errorCallback();
                    }
                    return;
                }

                verifiedplandetails = 'F';
                successCallback();
            },
            function(xhr) {
                const response = xhr.responseJSON || {};
                showAlert('Alert', response.message || 'Unable to verify checklist plan details.');
                if (errorCallback) {
                    errorCallback();
                }
            }
        );
    }

    function showAcknowledgementAlert(message, redirectUrl) {

        const content = `
        <div class="mb-3">
            ${message}
        </div>

        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   id="acknowledge_checkbox">

            <label class="form-check-label" for="acknowledge_checkbox">
               Verified all the details given in the checklist.
            </label>
        </div>
     `;

        passing_alert_value(
            'Confirmation',
            content,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'confirmation_alert'
        );

        const $okButton = $('#ok_button');

        $okButton.prop('disabled', true);

        $(document)
            .off('change', '#acknowledge_checkbox')
            .on('change', '#acknowledge_checkbox', function() {
                $okButton.prop('disabled', !this.checked);
            });

        $okButton
            .off('click')
            .on('click', function(e) {
                e.preventDefault();

                $okButton.prop('disabled', true);

                markChecklistPlanDetailsVerified(
                    function() {
                        $('#confirmation_alert').modal('hide');
                        window.location.href = redirectUrl;
                    },
                    function() {
                        $okButton.prop('disabled', false);
                    }
                );
            });
    }
    //-----------------------------------------------------Helper Functions ------------------------------------------//

    function handleFinalizedPlan(response, language) {


        executingquartercode = response.executingquartercode;
        auditors_det = response.planned_auditors;
        template_det = response.templatedata;

        fetchAuditorsTable(language)
        fetchTemplateTable(language);

    }

    function loadAssignmentData(response, language, showTeamDetails = true) {

        plan_items = response.plan_items
        plan_preferences = response.preference_list
        planquarter = response.planquarter;
        planmappingid = response.planmappingid;
        prioritycode = response.prioritycode;

        $('#planname_div').html(response.planname || '');

        totalInstitutionDetails = response.inst_det || [];
        plannedinst_det = response.plannedinst_det || [];
        idleinst_det = response.idleinst_det || [];

        templateInstitutionDetails = response.template_instdet || [];
        totalAuditorDetails = response.users || [];

        // Team Details
        toggleSection(
            '#tableshow_teamdetails',
            '#no_data_teamdetails',
            showTeamDetails && !!response.teamdet
        );

        if (showTeamDetails && response.teamdet) {
            totalteamDetails = response.teamdet;
            renderTotalTeamdetails(language);
        }

        // Template Details
        toggleSection(
            '#tableshow_tempdetails',
            '#no_data_templatedetails',
            !!response.allocdet_temp
        );

        if (response.allocdet_temp) {
            renderTemplatedetails(language, response.allocdet_temp);
        }

        // Allocation
        toggleSection(
            '#tableshow_allocation',
            '#no_data_allocation',
            !!response.totalteamdetails
        );

        if (response.totalteamdetails) {
            teamDetails = response.totalteamdetails;
            renderTeamdetails(language);
        }

        // Idle Auditors
        toggleSection(
            '#tableshow_idleauditors',
            '#no_data_idleauditors',
            response.idelusers?.length > 0
        );

        if (response.idelusers?.length) {
            idleAuditorsDetail = response.idelusers;
            renderIdleusersdetails(language);
        }

        // Idle Institutions
        toggleSection(
            '#tableshow_idleinstitution',
            '#no_data_idleinstitution',
            response.idleinst?.length > 0
        );

        if (response.idleinst?.length) {
            idleInstitutionDetails = response.idleinst;
            renderIdleInstdetails(language);
        }

        // Template Idle Institutions
        toggleSection(
            '#tableshow_templateidleinstitution',
            '#no_data_templateidleinstitution',
            response.templateidelinstitutionlist?.length > 0
        );

        if (response.templateidelinstitutionlist?.length) {
            rendertemplateIdleInstdetails(
                language,
                response.templateidelinstitutionlist
            );
        }

        populateCountdetails(response);
    }


    function toggleSection(showId, noDataId, hasData) {
        $(showId).toggle(hasData);
        $(noDataId).toggle(!hasData);
    }

    function toggle_form(enable_form) {

        if (enable_form == 'N') {
            $('#buttonaction').prop('disabled', true);
            $('#distcode,#regioncode').prop('disabled', true);
        } else {
            $('#buttonaction').prop('disabled', false);
            $('#distcode,#regioncode').prop('disabled', false);
        }


    }

    function toggle_checklistdiv(checklist_status) {

        if (checklist_status == 'F') {
            $('#finalised_plandiv').show();
            $('#data_teamassigned_div,#assignteambtn_div,#alert_div').hide()

        } else if (checklist_status == 'Y') {

            $('#data_teamassigned_div').show();
            $('#assignteambtn_div,#finalised_plandiv,#alert_div').hide();

        } else if (checklist_status == 'P') {

            $('#assignteambtn_div').show();
            $('#data_teamassigned_div,#finalised_plandiv,#alert_div').hide();

        } else if (checklist_status == 'N') {

            $('#alert_div').show();
            $('#data_teamassigned_div,#assignteambtn_div,#finalised_plandiv').hide();

        } else {

            $('#checklist_div,#data_teamassigned_div,#finalised_plandiv,#alert_div,#assignteambtn_div').hide();
        }


    }

    function getGenderText(genderCode, lang = 'en') {

        const genderMap = {
            M: {
                en: 'Male',
                ta: 'ஆண்'
            },
            F: {
                en: 'Female',
                ta: 'பெண்'
            },
            T: {
                en: 'Transgender',
                ta: 'திருநர்'
            },
            O: {
                en: 'Others',
                ta: 'பிறர்'
            }
        };

        return genderMap[genderCode]?.[lang] || '-';
    }

    //-------------Populate Function ---------------------------------//

    const detailConfig = {
        inst: {
            title: 'Institution Details',
            callback: () => totalInstitutionDetails
        },
        auditors: {
            title: 'Auditor Details',
            callback: () => totalAuditorDetails
        },
        templateinst: {
            title: 'Template Institution Details',
            callback: () => templateInstitutionDetails
        },
        tempallocatedinst: {
            title: 'Template Institution Details',
            callback: () => []
        },
        performance_inst: {
            title: 'Performance Institution Details',
            callback: () => performance_institution_list
        },
        plan_item: {
            title: 'Audit Planning Criteria',
            callback: () => plan_items
        },
        plan_pref: {
            title: 'Audit Allocation Priority',
            callback: () => plan_preferences
        },

        plannedinst_det: {
            title: 'Planned Institutions',
            callback: () => plannedinst_det
        },
        idleinst_det: {
            title: 'Idle Institutions',
            callback: () => idleinst_det
        }

    };

    function showDetails(param, tempdata = null) {

        const config = detailConfig[param];

        if (!config) return;

        passing_extra_large_alert(
            config.title,
            getTableHtml(param),
            'extra_large_confirmation_alert',
            'extra_large_alert_header',
            'extra_large_alert_body',
            'forward_alert',
            'send_intimation_label'
        );

        setTimeout(() => {
            populateInstitutionTable(
                tempdata ?? config.callback(),
                param
            );
        }, 50);
    }

    function getTableHtml(param) {

        const lang = getLanguage();

        const serialno = lang === 'ta' ? 'வ.எண்' : 'S.No';

        const templates = {

            auditors: `
            <div class="table-responsive rounded-2 border"
                 style="max-height:750px;overflow-y:auto;width:98%;margin:0 auto;">
                <table class="table userCountTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'பெயர்' : 'Name'}</th>
                             <th>${lang === 'ta' ? 'பாலினம்' : 'Gender'}</th>
                            <th>${lang === 'ta' ? 'பதவி'  : 'Designation'}</th>
                            <th>${lang === 'ta' ? 'ஊனம்' : 'Disability'}</th>
                            <th>${lang === 'ta' ? 'ஊனத்தின் வகை' : 'Disability Type'}</th>

                        </tr>
                    </thead>
                    <tbody id="auditorsTableBody"></tbody>
                </table>
            </div>
        `,

            inst: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table userCountTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'வகை' : 'Category'}</th>
                            <th>${lang === 'ta' ? 'குழு அளவு' : 'Team Size'}</th>
                            <th>${lang === 'ta' ? 'மன்டேஸ்' : 'Mandays'}</th>
                             <th>${lang === 'ta' ? 'தூரம்' : 'Distance'}</th>
                            <th>${lang === 'ta' ? 'கேரி பார்வர்ட்' : 'Carry Forward'}</th>

                        </tr>
                    </thead>
                    <tbody id="instTableBody"></tbody>
                </table>
            </div>
        `,

            templateinst: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table templateTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'வகை' : 'Category'}</th>
                        </tr>
                    </thead>
                    <tbody id="tempTableBody"></tbody>
                </table>
            </div>
        `,

            tempallocatedinst: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table templateTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'வகை' : 'Category'}</th>
                        </tr>
                    </thead>
                    <tbody id="tempallocatedinst"></tbody>
                </table>
            </div>
        `,

            performance_inst: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table performanceInstdet_table">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'குழு அளவு' : 'Team Size'}</th>
                            <th>${lang === 'ta' ? 'மன்டேஸ்' : 'Mandays'}</th>
                        </tr>
                    </thead>
                    <tbody id="performanceTableBody"></tbody>
                </table>
            </div>
        `,

            plan_item: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table plan_item_table">
                    <thead>
                        <tr>
                            <th>${lang === 'ta'
                                ? 'தணிக்கை திட்டமிடல் அளவுகோல்கள்'
                                : 'List of Criteria'}</th>
                        </tr>
                    </thead>
                    <tbody id="plan_item_body"></tbody>
                </table>
            </div>
        `,

            plan_pref: `
            <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table plan_pref_table">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta'
                                ? 'தணிக்கை ஒதுக்கீட்டு முன்னுரிமை'
                                : 'Audit Allocation Priority'}</th>
                        </tr>
                    </thead>
                    <tbody id="plan_pref_body"></tbody>
                </table>
            </div>
        `,

            plannedinst_det: `
             <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table userCountTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'வகை' : 'Category'}</th>
                            <th>${lang === 'ta' ? 'குழு அளவு' : 'Team Size'}</th>
                            <th>${lang === 'ta' ? 'மன்டேஸ்' : 'Mandays'}</th>
                             <th>${lang === 'ta' ? 'தூரம்' : 'Distance'}</th>
                            <th>${lang === 'ta' ? 'கேரி பார்வர்ட்' : 'Carry Forward'}</th>

                        </tr>
                    </thead>
                    <tbody id="plannedinst_tbody"></tbody>
                </table>
            </div>
        `,


            idleinst_det: `
             <div class="table-responsive rounded-2 border"
                 style="max-width:750px;max-height:600px;overflow:auto;width:98%;margin:0 auto;">
                <table class="table userCountTable">
                    <thead>
                        <tr>
                            <th>${serialno}</th>
                            <th>${lang === 'ta' ? 'தணிக்கை நிறுவனம்' : 'Auditable Institution'}</th>
                            <th>${lang === 'ta' ? 'வகை' : 'Category'}</th>
                            <th>${lang === 'ta' ? 'குழு அளவு' : 'Team Size'}</th>
                            <th>${lang === 'ta' ? 'மன்டேஸ்' : 'Mandays'}</th>
                             <th>${lang === 'ta' ? 'தூரம்' : 'Distance'}</th>
                            <th>${lang === 'ta' ? 'கேரி பார்வர்ட்' : 'Carry Forward'}</th>

                        </tr>
                    </thead>
                    <tbody id="idleinst_tbody"></tbody>
                </table>
            </div>
        `
        };

        return templates[param] || '';
    }

    function populateInstitutionTable(det, param) {

        const lang = getLanguage();

        const tbodyMap = {
            auditors: 'auditorsTableBody',
            inst: 'instTableBody',
            performance_inst: 'performanceTableBody',
            templateinst: 'tempTableBody',
            tempallocatedinst: 'tempallocatedinst',
            plan_item: 'plan_item_body',
            plan_pref: 'plan_pref_body',
            plannedinst_det: 'plannedinst_tbody',
            idleinst_det: 'idleinst_tbody'
        };

        const renderers = {

            auditors: (item, index) => `
                <tr>
                    <td>${index + 1}</td>

                    <td>
                        ${lang === 'ta' ? item.usertamilname : item.username}

                    </td>
                    <td>
                        ${getGenderText(item.gendercode, lang)}
                    </td>
                    <td>
                        ${lang === 'ta'
                            ? item.desigtlname
                            : item.desigelname}
                    </td>

                    <td>
                        ${item.disability === 'Y'
                            ? (lang === 'ta' ? 'ஆம்' : 'Yes')
                            : (lang === 'ta' ? 'இல்லை' : 'No')}
                    </td>

                    <td>
                        ${item.disability === 'Y'
                            ? (
                                lang === 'ta'
                                    ? `
                                        <b>சதவீதம் :</b> ${item.disabilityrate}%<br>
                                        <b>வகை :</b> ${item.disability_tname || '-'}
                                      `
                                    : `
                                        <b>Rate :</b> ${item.disabilityrate}%<br>
                                        <b>Type :</b> ${item.disability_ename || '-'}
                                      `
                            )
                            : '-'}
                    </td>
                </tr>
            `,

            plan_item: (item) => `
            <tr>
                <td>${item}</td>
            </tr>
            `,

            plan_pref: (item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${item.planpreferenceelname}</td>
            </tr>
            `,

            templateinst: (item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                <td>${lang === 'ta' ? item.cattname : item.catename}</td>
            </tr>
            `,

            tempallocatedinst: (item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                <td>${lang === 'ta' ? item.cattname : item.catename}</td>
            </tr>
             `,

            performance_inst: (item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                <td>${item.teamsize}</td>
                <td>${item.mandays}</td>
            </tr>
            `,

            inst: (item, index) => {

                const spillOver = item.spillover === 'Y';

                const carryForward = lang === 'ta' ?
                    (spillOver ? 'ஆம்' : 'இல்லை') :
                    (spillOver ? 'Yes' : 'No');

                const workingDays = spillOver ?
                    item.remainingmandays :
                    item.mandays;

                return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                    <td>${lang === 'ta' ? item.cattname : item.catename}</td>
                    <td>${item.teamsize}</td>
                    <td>${workingDays}</td>
                    <td>${Number(item.inst_kms).toFixed(2)} Km</td>
                    <td>${carryForward}</td>

                </tr>
            `;
            },
            plannedinst_det: (item, index) => {

                const spillOver = item.spillover === 'Y';

                const carryForward = lang === 'ta' ?
                    (spillOver ? 'ஆம்' : 'இல்லை') :
                    (spillOver ? 'Yes' : 'No');

                const workingDays = spillOver ?
                    item.remainingmandays :
                    item.mandays;

                return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                    <td>${lang === 'ta' ? item.cattname : item.catename}</td>
                    <td>${item.teamsize}</td>
                    <td>${workingDays}</td>
                    <td>${Number(item.inst_kms).toFixed(2)} Km</td>
                    <td>${carryForward}</td>

                </tr>
            `;
            },

            idleinst_det: (item, index) => {

                const spillOver = item.spillover === 'Y';

                const carryForward = lang === 'ta' ?
                    (spillOver ? 'ஆம்' : 'இல்லை') :
                    (spillOver ? 'Yes' : 'No');

                const workingDays = spillOver ?
                    item.remainingmandays :
                    item.mandays;

                return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${lang === 'ta' ? item.insttname : item.instename}</td>
                    <td>${lang === 'ta' ? item.cattname : item.catename}</td>
                    <td>${item.teamsize}</td>
                    <td>${workingDays}</td>
                    <td>${Number(item.inst_kms).toFixed(2)} Km</td>
                    <td>${carryForward}</td>

                </tr>
            `;
            }
        };

        const tbody = document.getElementById(tbodyMap[param]);

        if (!tbody || !renderers[param]) {
            return;
        }

        tbody.innerHTML = (det || [])
            .map((item, index) => renderers[param](item, index))
            .join('');
    }

    function populateCountdetails(data) {

        const lang = getLanguage();

        const {
            performanceinstcount = 0,
                totalinstcount = 0,
                templateinstcount = 0,
                sumofinstmandays = 0,
                totalauditorscount = 0,
                totalworkingdays = 0,
                neededmandays = 0,
                allocatedmandays = 0,
                allocatedmandays_performance = 0,
                templateallocatedmandays = '-',
                designationDetails = [],
                plannedinst_count = 0,
                ideleinst_count = 0,
        } = data;

        const fromdate = data.quarterfromdate ?
            ChangeDateFormat(data.quarterfromdate) :
            '';

        const todate = data.quartertodate ?
            ChangeDateFormat(data.quartertodate) :
            '';

        const lastworkingdate = data.lastworkingdate ?
            ChangeDateFormat(data.lastworkingdate) :
            '';

        const templateInstClickable = Number(templateinstcount) > 0;

        const counthtml = `
        <div class="d-flex justify-content-between align-items-center flex-wrap border rounded p-3 bg-light">

            <div class="d-flex align-items-center flex-wrap gap-4">
                <div>
                    <b>Audit Quarter Period :</b>
                    <span class="text-primary">${fromdate} to ${todate}</span>
                </div>

                <div>
                    <b>Audit End Date :</b>
                    <span class="text-primary">${lastworkingdate}</span>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <button class="btn text-light px-3"
                    style="background:#469a9a"
                    onclick="showDetails('plan_pref')">
                    Audit Allocation Priority
                </button>

                <button class="btn text-light px-3"
                    style="background:#9fa45e"
                    onclick="showDetails('plan_item')">
                    Audit Planning Criteria
                </button>
            </div>
        </div>

        <hr>

        <div class="row text-center">

           ${performanceinstcount > 0 ? `
           <div class="col-sm-3 border-end">
            <label>Total Number of Institutes (Performance Audit)</label>
            <p onclick="showDetails('performance_inst')" class="count_style">
                ${performanceinstcount}
            </p>
            </div>
           ` : ''}

            <div class="col-sm-3 border-end">
                <label>Total Number of Institutes (Regular Audit)</label>
                <p onclick="showDetails('inst')" class="count_style">
                    ${totalinstcount}
                </p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Total Number of Planned Institutions(Regular Audit)</label>
                <p class="count_style"  onclick="showDetails('plannedinst_det')">${plannedinst_count}</p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Total Number of Idle Institutions(Regular Audit)</label>
                <p class="count_style"  onclick="showDetails('idleinst_det')" >${ideleinst_count}</p>
            </div>


            <div class="col-sm-3 border-end">
                <label>Total Number of Institutes (Template Audit)</label>
                <p   ${templateInstClickable ? `onclick="showDetails('templateinst')"` : ''}
                 style="cursor:${templateInstClickable ? 'pointer' : 'default'}"  class="count_style">
                    ${templateinstcount}
                </p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Mandays Assigned</label>
                <p class="count_style">${sumofinstmandays}</p>
            </div>



            <div class="col-sm-3">
                <label>Total Number of Auditors</label>
                <p onclick="showDetails('auditors')" class="count_style">
                    ${totalauditorscount}
                </p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Total Working Days</label>
                <p class="count_style">${totalworkingdays}</p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Available Mandays</label>
                <p class="count_style">${neededmandays}</p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Allocated Mandays (Regular Audit)</label>
                <p class="count_style">${allocatedmandays}</p>
            </div>



            <div class="col-sm-3 border-end">
                <label>Allocated Mandays (Template Audit)</label>
                <p class="count_style">${templateallocatedmandays}</p>
            </div>

            <div class="col-sm-3 border-end">
                <label>Allocated Mandays (Performance Audit)</label>
                <p class="count_style">${allocatedmandays_performance}</p>
            </div>


        </div>
        `;

        $('#count_det').html(counthtml);

        document.getElementById('designationTableBody').innerHTML =
            designationDetails.map((desig, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${lang === 'ta'
                    ? desig.desigtlname
                    : desig.desigelname}</td>
                <td>${desig.count}</td>
            </tr>
        `).join('');
    }

    //----------------------------redo Timer--------------------------//
    function formatDateTime(date) {

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');

        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12 || 12;

        return `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;
    }

    function startRedoTimer(lastRunTime, redo_time) {

        const now = new Date();
        const $buttons = $('#redo_checklist, #finalise_btn');

        let enableTime;

        if (lastRunTime && lastRunTime !== 'null') {

            const lastRunDate = new Date(lastRunTime);

            $('#lastrun_timer').html(`
            <span class="text-dark">Last Runtime :</span>
            <span class="text-primary">${formatDateTime(lastRunDate)}</span>
        `);

            enableTime = new Date(
                lastRunDate.getTime() + (redo_time * 1000)
            );

        } else {

            enableTime = new Date(
                now.getTime() + (redo_time * 1000)
            );
        }

        // Clear existing timer
        if (redoTimerInterval) {
            clearInterval(redoTimerInterval);
            redoTimerInterval = null;
        }

        // Already enabled
        if (now >= enableTime) {

            $buttons
                .prop('disabled', false)
                .removeClass('disabled');

            $('#redo_timer').empty();

            return;
        }

        $buttons
            .prop('disabled', true)
            .addClass('disabled');

        redoTimerInterval = setInterval(() => {

            const remaining = enableTime - new Date();

            if (remaining <= 0) {

                clearInterval(redoTimerInterval);
                redoTimerInterval = null;

                $buttons
                    .prop('disabled', false)
                    .removeClass('disabled');

                $('#redo_timer').empty();

                return;
            }

            const minutes = Math.floor(remaining / 60000);
            const seconds = String(
                Math.floor((remaining % 60000) / 1000)
            ).padStart(2, '0');

            $('#redo_timer').html(
                `Redo Checklist (Available in <b>${minutes}:${seconds}</b>)`
            );

        }, 1000);
    }

    function renderTemplatedetails(language, data) {
        const instColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#allocdettemplate')) {
            $('#allocdettemplate').DataTable().clear().destroy();
        }

        var table = $('#allocdettemplate').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": data,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: 'members',
                    title: columnLabels?.['members']?.[language] || "User",
                    render: function(data, type, row) {


                        return `${data}`;
                    },
                    className: 'text-wrap text-start'
                },

                {
                    data: 'fromdate',
                    title: columnLabels?.['from_date']?.[language] || "Proposed Audit Period",
                    render: function(data, type, row) {
                        const isValidDate = (d) => {
                            const date = new Date(d);
                            return d && !isNaN(date);
                        };

                        const fromDate = isValidDate(row.fromdate) ? new Date(row.fromdate)
                            .toLocaleDateString('en-GB') : "N/A";
                        const toDate = isValidDate(row.todate) ? new Date(row.todate)
                            .toLocaleDateString('en-GB') : "N/A";

                        return `${fromDate} - ${toDate}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"

                },

                {
                    data: 'mandays',
                    title: columnLabels?.['mandays']?.[language] || "Mandays",
                    render: function(data, type, row) {


                        return `${data}`;
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: 'instcount',
                    title: columnLabels?.['instcount']?.[language] || "Institution Count",
                    render: function(data, type, row) {


                        return `<div data-userid=${row.userid}  style="cursor:pointer" onclick ="get_userbasedtemp(${row.userid})">${data}</div>`;
                    },
                    className: 'text-wrap text-start'
                },

            ],
            "initComplete": function(settings, json) {
                $("#allocdettemplate").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        updatedatatable(language, "allocdettemplate");
    }
</script>

@endsection
