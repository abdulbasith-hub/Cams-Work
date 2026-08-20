@section('content')
    @extends('index2')
    @include('common.alert')
    <style>
        /* Initially make the tabs visible */
        section {
            display: none;
        }

        .card_dark {
            border: 1px solid #7198b9;
        }

        .select2forcps+.select2-container .select2-selection__choice {
            display: inline-flex;
            align-items: center;
            margin: 2px 4px;
            width: 83px;
            white-space: nowrap;
        }

        .select2forcps+.select2-container .select2-selection {
            flex-wrap: wrap;
        }

.custom-accordion-btn {
    width: auto !important;
    padding: 0.25rem 0.5rem !important;
}

    .custom-accordion-btn:focus,
    .custom-accordion-btn:active {
        box-shadow: none !important;
        outline: none !important;
        border: none !important;
    }

    .accordion-button:focus {
        box-shadow: none !important;
    }

        .modal {
            z-index: 1055;
        }

        #confirmation_alert {
            z-index: 1075 !important;
        }

        .modal-backdrop.show:nth-of-type(2) {
            z-index: 1070;
        }


        .professional-table {
            width: 100%;
            border-collapse: collapse;
        }

        .professional-table th,
        .professional-table td {
            border: 1px solid #ccc8c8;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .professional-table thead th {
            background-color: #f2f4f7;
            color: #2c2c2c;
            font-weight: 600;

        }

        .professional-table tbody td:first-child {
            text-align: left;
        }

        .professional-table input,
        .professional-table select {
            padding: 4px 6px;
            box-sizing: border-box;
        }

        #cpsModal table th {
            font-size: 13px;
        }

        #cpsModal table td {
            font-size: 13px;
        }


        .is-invalid {
            border: 1px solid #fd1b13 !important;
            box-shadow: none !important;
        }

        .custom-modal-width {
            max-width: 1600px;
            width: 100%;
        }

        .accordion-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            flex-wrap: wrap;
        }

        /* Main title */
        .title-text {
            font-weight: 500;
            color: #1f2937;
        }

        /* Employee name */
        .emp-badge {
            background: #eaf2ff;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* CPS number */
        .cps-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            border: 1px solid #e2e8f0;
        }

        @media (min-width: 1200px) {
            #cpsModal {
                max-width: 100%;
                width: 100%;
                margin: 0 auto;
            }

            #cpsModal .card .card-body {
                width: 100%;
            }
        }

        @media (max-width: 1199px) {
            #cpsModal {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- <div class="card   .card_dark hide_this" id="normal_audit_div"> -->
        <div class="card   .card_dark " id="normal_audit_div">
        <div class="card-body wizard-content">

            <div action="#" class="validation-wizard wizard-circle ">
                <input type="hidden" class="form-control" id="auditeereponseget" name="auditeereponseget">
                <input type="hidden" class="form-control" id="sentintimation" name="sentintimation">
                <input type="hidden" class="form-control" id="status_officedet" name="status_officedet">
                <input type="hidden" class="form-control" id="auditscheduleid" name="auditscheduleid">
                <input type="hidden" class="form-control" id="auditscheduleid_cps" name="auditscheduleid_cps">

                <input type="hidden" class="form-control" id="instid_cps" name="instid">
                <input type="hidden" id="fromquarter" />
                <input type="hidden" id="toquarter" />
                <!-- Step 1 -->
                <h6><span class="lang" key="intimation_label">Intimation</span></h6>
                <section>
                      <div class="card card_border hide_this" id="epaccs_div">

        <div class="card-body mb-4">


            <div class="row ">

                <div id="" class="row  ">
                    <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                        <span class="lang" key="successmsg_auditee">Data has been submitted
                            successfully.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
                    <div class="card   card_dark">
                        <div class="card-header card_header_color lang" key="intimation_label">
                            Intimation
                        </div>
                        <div class="card-body">
                            <div class="card-header" id="inst_name"></div>
                            <input type="hidden" id="h_deptcode" />
                            <input type="hidden" id="h_catcode" />
                            <div class="row">
                                <div class="col-md-4 ">
                                    <label class="form-label lang" key="entrymeeting_date" for="validationDefault02">Entry
                                        Meeting
                                        date</label>
                                    <div class="input-group" onclick="datepicker('entry_date','')">
                                        <input type="text" class="form-control datepicker" id="entry_date"
                                            name="entry_date" placeholder="dd/mm/yyyy" disabled />
                                        <span class="input-group-text">
                                            <i class="ti ti-calendar fs-5"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label style="text-align:center;" key="proposed_date" align="center"
                                        class="form-label lang" for="validationDefault02">Proposed Date</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <label class="form-label " for="validationDefault02"><b class="lang"
                                                        key="fromdate_label">From
                                                        Date</b>&nbsp;&nbsp; : &nbsp;&nbsp;</label>
                                                <input type="text" class="form-control" id="start_date" name="start_date"
                                                    placeholder="dd/mm/yyyy" disabled />
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar fs-5"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <label class="form-label" for="validationDefault02"><b class="lang"
                                                        key="todate_label">To
                                                        Date</b>&nbsp;&nbsp; : &nbsp;&nbsp;</label>
                                                <input type="text" class="form-control" id="end_date" name="end_date"
                                                    placeholder="dd/mm/yyyy" disabled />
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar fs-5"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label lang" key="typeofaudit_label"
                                        for="validationDefault02">Audit
                                        Type</label>
                                    <input type="text" class="form-control" value="Financial" id="audit_type"
                                        name="audit_type" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label lang" key="audityear_label" for="validationDefault02">Audit
                                        Year</label>
                                    <input type="text" class="form-control" id="financial_year" name="financial_year"
                                        disabled />
                                </div>

                                <div id="annadhanamDiv" class="col-md-4">
                                    <label class="form-label lang" key="" for="validationDefault02">Annadhanam
                                        Year</label>
                                    <input type="text" class="form-control" id="annadhanam_year"
                                        name="annadhanam_year" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label lang" key="quarter_label"
                                        for="validationDefault02">Quarter</label>
                                    <input type="text" class="form-control"
                                        value="Quarter4 (January 2024- March 2024)" id="audit_period" name="audit_period"
                                        disabled />
                                </div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label lang" key="teamhead_label" for="validationDefault02">Audit
                                        Team
                                        Head</label>
                                    <select class="select2 form-control custom-select" multiple="multiple" id="tm_hid"
                                        name="tm_hid" aria-placeholder="Select Member" disabled>


                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label lang" key="teammember_label" for="validationDefault02">Audit
                                        Team
                                        Member</label>
                                    <select class="select2 form-control custom-select" multiple="multiple" id="tm_uid"
                                        name="tm_uid[]" aria-placeholder="Select Member" disabled>


                                    </select>
                                </div>
                            </div>
                            <div class="row hide_this" id="buttonsforacceptance">
                                <div class="col-md-3 mx-auto text-center">
                                    <!-- Adding text-center to center the content inside -->
                                    <button type="button " class="btn btn-success lang mt-4" data-bs-toggle="modal"
                                        id="accept" data-bs-target="#success-header-modal"
                                        onclick="acceptAndProceed()" key="accepted_btn">
                                        <span class="ms-2">
                                            Accept</span>

                                    </button>
                                </div>
                            </div>
                            <div id="statusmessage" class="row  hide_this">
                                <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                    <span class="lang" key="successmsg_auditee">Data has been submitted
                                        successfully.</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
                <!-- Step 2 -->
                <h6><span class="lang" key="records_label">Records/Details</span></h6>
                <section>
                    <div class="card   card_dark">
                        <div class="card-header card_header_color lang" key="records_label">
                            Records/Details
                        </div>
                        <div class="card-body">
                            <form id="callforrecords" name="callforrecords">
                                @csrf
                                <input type="hidden" class="form-control" id="audit_scheduleid" name="auditscheduleid">
                                <h5 class="mt-2 lang auditparticularlabel_audit" key="audit_particulars_label">Audit
                                    Particulars</h5>
                                <div class="table-responsive rounded-4">
                                    <table class="table table-bordered">
                                        <tbody id="part_details"></tbody>
                                    </table>
                                    <table class="table table-bordered">
                                        <tbody id="part_details_fetch"></tbody>
                                    </table>


                                </div>
                                <div id="details_tabletab_buttons" class="row">
                                    <div class="col-md-3  mx-auto">
                                        <button class="btn btn-success mt-3 lang" key="submit" type="submit"
                                            action="insert" id="buttonaccept" name="buttonaccept">
                                            Submit
                                        </button>


                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </section>
                <!-- Step 3 -->
                <h6><span class="lang" key="audituserdetails_label">Auditee Officers</span></h6>
                <section>
                    <div class="card  card_dark">
                        <div class="card-header card_header_color">
                            Auditee Officer Details
                        </div>
                        <div class="card-body">
                            <form id="auditee_officeusers" name="auditee_officeusers">
                                @csrf
                                <input type="hidden" name="auditscheduleid" id="officeuseraudit_scheduleid" />
                                <input type="hidden" name="auditee_ofcusercount" id="auditee_ofcusercount" />
                                <input type="hidden" class="form-control" id="auditeereponseget"
                                    name="auditeereponseget">

                                <div id="appendusers" class="single-note-item">
                                    <div id="addrowUsers">
                                        <div class="row">
                                            <div class="col-md-1">

                                            </div>

                                            <div class="col-md-3 ms-2">

                                            </div>

                                            <div class="col-md-2 ms-2">

                                            </div>

                                            <div class="col-md-4 ms-2">

                                                <center><b class="lang" key="serviceperiod">Service Period </b></center>
                                            </div>

                                            <div class="col-md-3">

                                            </div>
                                        </div>
                                        <div class="d-flex mt-2 work-row-insert" id="row0">
                                            <input type="hidden" name="officeuserid[1]" value="UserId_1">

                                            <div class="col-md-1">
                                                <label class="form-label lang" key="s_no"
                                                    for="validationDefaultUsername">S.No</label>
                                                <input type="text" class="form-control alpha_numeric" value="1"
                                                    disabled>
                                            </div>

                                            <div class="col-md-3 ms-2">
                                                <label class="form-label lang" key="name"
                                                    for="validationDefaultUsername">Name</label>
                                                <input type="text" class="form-control name " maxlength="50"
                                                    id="name0" data-placeholder-key="username"
                                                    name="officeusername[1]" value="" placeholder="Enter Name">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <label class="form-label lang" key="designation"
                                                    for="validationDefaultUsername">Designation</label>
                                                <input type="text" class="form-control name" maxlength="100"
                                                    data-placeholder-key="designation_ph" name="officeuserdesignation[1]"
                                                    id="designation0" value="" placeholder="Enter Designation">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <label class="form-label lang" for="validationDefaultUsername">From
                                                    Date</label>
                                                <div class="input-group" onclick="datepicker('from_date','','1')">
                                                    <input type="text" id="from_date1" class="form-control datepicker"
                                                        name="officeuserfromdate[1]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text">
                                                        <i class="ti ti-calendar fs-5"></i>
                                                    </span>
                                                </div>
                                            </div>


                                            <div class="col-md-2 ms-2">
                                                <label class="form-label lang" key="to_date"
                                                    for="validationDefaultUsername">To Date</label>
                                                <div class="input-group" onclick="datepicker('to_date','','1')">
                                                    <input type="text" class="form-control datepicker" id="to_date1"
                                                        name="officeusertodate[1]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text">
                                                        <i class="ti ti-calendar fs-5"></i>
                                                    </span>
                                                </div>

                                            </div>

                                            <div class="col-md-3 actionbtns">
                                                <label class="form-label lang" key="action"
                                                    for="validationDefaultUsername">Action</label><br>
                                                <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn"
                                                    onclick="addNewWorkRow(event,'insert')">
                                                    <i class="ti ti-circle-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="EditrowUsers">

                                </div>


                                <div id="statusmessage_auditeeusers" class="row  hide_this">
                                    <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                        <span class="lang" key="datasubmitsuccess_auditeeofc">Institution Official
                                            Details submitted successfully.</span>
                                    </div>
                                </div>
                                <br class="row actionbtns">
                                <hr class="row actionbtns">
                                <!--<span style="color:red;">Note: Have Priviledge to Add/Modify Institution Official Details till acceptance</span>-->
                                <div class="row actionbtns">
                                    <div class="col-md-3  mx-auto">
                                        <button class="btn btn-success mt-3 lang" key="submit" type="submit"
                                            id="officeuser_submit" onclick="insertAuditOfficersDetail()">Submit</button>

                                    </div>

                                </div><br class="row ">
                            </form>
                        </div>



                    </div>

                    <!-- </section> -->
                </section>
                @if ($cpsform == 'Y')
                    <h6><span class="lang" key="">CPS Details</span></h6>
                    <section class="cps-section">
                        <div class="container-fluid mt-3" id="cpsModal">

                            <div class="card  card_dark">
                                <div class="card-header card_header_color">
                                    CPS Details
                                </div>
                                <div class="card-body">

                                    <form class="cpsForm" id="cpsForm">
                                        @csrf




                                        <div id="cpsValidationAlert" class="alert alert-warning d-none mb-3">
                                            Please fill all required fields before proceeding.
                                        </div>

                                        <div id="cpsSetsContainer">

                                            <div class="accordion " id="cpsAccordion">
                                                <div class="accordion-item"
                                                    style="border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                                    <h2 class="accordion-header" id="headingCps1">
                                                        <div class="d-flex align-items-center w-100 p-2 mt-2 mb-2">
                                                            <!-- Left: Text -->
                                                            <span
                                                                style="font-size: 1rem; white-space: nowrap; flex-grow: 1;"
                                                                class="accordion-title">
                                                                CPS Set 1: Employee & Remittance Details
                                                            </span>

                                                            <!-- + Button -->
                                                            <button type="button"
                                                                class="btn btn-success btn-sm addRow fw-bold ms-2 ">
                                                                Add Employee
                                                            </button>

                                                            <button type="button"
                                                                class="btn btn-danger btn-sm deleteRow d-none fw-bold">
                                                                -
                                                            </button>

                                                            <!-- Accordion toggle arrow -->
                                                            <button class="accordion-button ms-2 custom-accordion-btn"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapseCps1" aria-expanded="true"
                                                                aria-controls="collapseCps1">
                                                            </button>
                                                        </div>
                                                    </h2>


                                                    <div id="collapseCps1" class="accordion-collapse collapse show"
                                                        aria-labelledby="headingCps1" data-bs-parent="#cpsAccordion">
                                                        <div class="accordion-body">

                                                            <!-- Original CPS Set Content Starts -->
                                                            <div class="cps-set border p-3 mb-4">

                                                                <input type="hidden" name="transcpsid[1]"
                                                                    class="transcpsid">
                                                                <input type="hidden" name="shortfallid[1]"
                                                                    class="shortfallid">


                                                                <div class="table-responsive mb-4 text-end">

                                                                    <table
                                                                        class="table professional-table  text-center align-middle ">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th colspan="14"
                                                                                    class="text-center  fs-6 "
                                                                                    style="font-family: 'Times New Roman', serif; font-weight: 600;">
                                                                                    i) Employees under CPS
                                                                                </th>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>S.No</th>
                                                                                {{-- <th class="required">Department Name</th> --}}
                                                                                <th class="">Treasury / Sub Treasury
                                                                                </th>
                                                                                <th class="">DDO Code</th>
                                                                                <th class="required">CPS No</th>

                                                                                <th class="required">Name of Employee</th>
                                                                                {{-- <th  class="required">DOB</th>
                                                                        <th  class="required">DOJ</th> --}}
                                                                                <th class="required">Designation</th>
                                                                                <th class="required">Current Pay Scale</th>
                                                                                <th class="required">Whether Employee
                                                                                    contribution is deducted Yes/No?(If no
                                                                                    give
                                                                                    details)</th>
                                                                                <th class="required">Whether Employer's
                                                                                    contribution is deducted Yes/No?(If no
                                                                                    give
                                                                                    details)</th>
                                                                                <th class="required">Is there any missing
                                                                                    credits Yes/No?</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="cpsTableBody">
                                                                            <tr>

                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm text-center sNo"
                                                                                        value="1" readonly>
                                                                                </td>


                                                                                {{-- <td>
                                                                            <input type="text" style="width: 130px;"  class="form-control form-control-sm nbew_text limit-paste" placeholder="Departmrnt Name" data-required="true" name="department_name[1]" maxlength="50">
                                                                        </td> --}}

                                                                                <td>
                                                                                    <input type="text"
                                                                                        style="width: 130px;"
                                                                                        class="form-control form-control-sm nbew_text limit-paste"
                                                                                        placeholder="Treasury / Sub Treasury"
                                                                                        name="treasury_cps[1]"
                                                                                        maxlength="100">
                                                                                </td>

                                                                                <td>
                                                                                    <input type="text"
                                                                                        style="width: 90px;"
                                                                                        class="form-control form-control-sm text-center nbew_number"
                                                                                        placeholder="Enter DDO Code"
                                                                                        name="ddocode_cps[1]"
                                                                                        maxlength="8">
                                                                                </td>






                                                                                <td>
                                                                                    <input type="text"
                                                                                        style="width: 90px;"
                                                                                        class="form-control form-control-sm text-center cpsNo nbew_number"
                                                                                        placeholder="CPS No"
                                                                                        data-required="true"
                                                                                        name="cpsNo[1]" maxlength="10"
                                                                                        data-type="cpsno">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        style="width: 110px;"
                                                                                        class="form-control form-control-sm nbew_text limit-paste"
                                                                                        placeholder="Name"
                                                                                        data-required="true"
                                                                                        name="name[1]" maxlength="50">
                                                                                </td>


                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm nbew_text limit-paste"
                                                                                        placeholder="Designation"
                                                                                        data-required="true"
                                                                                        name="designation[1]"
                                                                                        maxlength="100">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        style="width: 70px;"
                                                                                        class="form-control form-control-sm nbew_number"
                                                                                        placeholder="Pay Scale"
                                                                                        data-required="true"
                                                                                        name="payscale[1]" maxlength="6">
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="form-check form-check-inline">
                                                                                        <input
                                                                                            class="form-check-input yes-no-radio"
                                                                                            type="radio" name="q1[1]"
                                                                                            value="Y" checked>
                                                                                        <label
                                                                                            class="form-check-label">Yes</label>
                                                                                    </div>
                                                                                    <div
                                                                                        class="form-check form-check-inline">
                                                                                        <input
                                                                                            class="form-check-input yes-no-radio"
                                                                                            type="radio" name="q1[1]"
                                                                                            value="N">
                                                                                        <label
                                                                                            class="form-check-label">No</label>
                                                                                    </div>
                                                                                    <textarea class="form-control form-control-sm mt-1 d-none nbew_text" maxlength="255"
                                                                                        placeholder="Please provide details" data-required="true" name="details1[1]"></textarea>
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="form-check form-check-inline">
                                                                                        <input
                                                                                            class="form-check-input yes-no-radio"
                                                                                            type="radio" name="q2[1]"
                                                                                            value="Y" checked>
                                                                                        <label
                                                                                            class="form-check-label">Yes</label>
                                                                                    </div>
                                                                                    <div
                                                                                        class="form-check form-check-inline">
                                                                                        <input
                                                                                            class="form-check-input yes-no-radio"
                                                                                            type="radio" name="q2[1]"
                                                                                            value="N">
                                                                                        <label
                                                                                            class="form-check-label">No</label>
                                                                                    </div>
                                                                                    <textarea class="form-control form-control-sm mt-1 d-none nbew_text" maxlength="255"
                                                                                        placeholder="Please provide details" data-required="true" name="details2[1]"></textarea>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <div
                                                                                        class="d-flex justify-content-center align-items-center gap-4">
                                                                                        <label
                                                                                            class="d-flex align-items-center gap-1 m-0">
                                                                                            <input type="radio"
                                                                                                class="form-check-input yes-no-radio m-0"
                                                                                                name="score[1]"
                                                                                                value="Y" checked>
                                                                                            Yes
                                                                                        </label>

                                                                                        <label
                                                                                            class="d-flex align-items-center gap-1 m-0">
                                                                                            <input type="radio"
                                                                                                class="form-check-input yes-no-radio m-0"
                                                                                                name="score[1]"
                                                                                                value="N">
                                                                                            No
                                                                                        </label>
                                                                                    </div>
                                                                                </td>

                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <small class="text-danger d-none"
                                                                        id="rowLimitMsg">Maximum
                                                                        5 rows allowed</small>
                                                                </div>




                                                                <div class="table-responsive mt-4">
                                                                    <table
                                                                        class="table professional-table align-middle text-center">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="5"
                                                                                    class="text-center  fs-6 "
                                                                                    style="font-family: 'Times New Roman', serif; font-weight: 600;">
                                                                                    ii) Information on Remittance
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    a)
                                                                                </td>

                                                                                <!-- Label -->
                                                                                <td class="text-start required"
                                                                                    style="white-space: nowrap;;width: 40%;">
                                                                                    Total Employee Contribution for the FY
                                                                                </td>

                                                                                <!-- Dropdown -->
                                                                                <td class="text-start">
                                                                                    <select
                                                                                        class="form-select form-select-sm  lang-dropdown"
                                                                                        name="employeefinancialyear[1]"
                                                                                        data-required="true">
                                                                                        <option value="">Select Audit
                                                                                            Year</option>
                                                                                        @foreach ($financialyear as $year)
                                                                                            <option
                                                                                                value="{{ $year->auditperiodid }}">
                                                                                                {{ $year->auditperiod }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>

                                                                                <td style="width:400px;">
                                                                                    <select
                                                                                        class="select2forcps text-start"
                                                                                        name="cps_month_employee[1]"
                                                                                        multiple required>

                                                                                        <option value="01">Mar</option>
                                                                                        <option value="02">Apr</option>
                                                                                        <option value="03">May</option>
                                                                                        <option value="04">Jun</option>
                                                                                        <option value="05">Jul</option>
                                                                                        <option value="06">Aug</option>
                                                                                        <option value="07">Sep</option>
                                                                                        <option value="08">Oct</option>
                                                                                        <option value="09">Nov</option>
                                                                                        <option value="10">Dec</option>
                                                                                        <option value="11">Jan</option>
                                                                                        <option value="12">Feb</option>
                                                                                    </select>
                                                                                </td>




                                                                                <!-- Amount Input -->
                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>
                                                                                        <input type="text"
                                                                                            class="form-control form-control-sm amount nbew_number_decimal"
                                                                                            maxlength="12"
                                                                                            name="employee_contri[1]"
                                                                                            placeholder="0.00"
                                                                                            data-required="true">
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    b)
                                                                                </td>

                                                                                <td class="text-start required"
                                                                                    style="white-space: nowrap;">Total
                                                                                    Employer
                                                                                    Contribution for the FY</td>
                                                                                <td class="text-start"
                                                                                    style="width:400px;">

                                                                                    <select
                                                                                        class="form-select form-select-sm  lang-dropdown"
                                                                                        name="employerfinancialyear[1]"
                                                                                        data-required="true">
                                                                                        <option value="">Select Audit
                                                                                            Year</option>
                                                                                        @foreach ($financialyear as $year)
                                                                                            <option
                                                                                                value="{{ $year->auditperiodid }}">
                                                                                                {{ $year->auditperiod }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>

                                                                                </td>

                                                                                <td style="width:400px;">
                                                                                    <select class="select2forcps"
                                                                                        name="cps_month_employer[1]"
                                                                                        multiple>
                                                                                        <option value="" disabled>
                                                                                            Select
                                                                                            Month</option>
                                                                                        <option value="01">Mar</option>
                                                                                        <option value="02">Apr</option>
                                                                                        <option value="03">May</option>
                                                                                        <option value="04">Jun</option>
                                                                                        <option value="05">Jul</option>
                                                                                        <option value="06">Aug</option>
                                                                                        <option value="07">Sep</option>
                                                                                        <option value="08">Oct</option>
                                                                                        <option value="09">Nov</option>
                                                                                        <option value="10">Dec</option>
                                                                                        <option value="11">Jan</option>
                                                                                        <option value="12">Feb</option>
                                                                                    </select>
                                                                                </td>

                                                                                <td style="width: 500px;">
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>
                                                                                        <input type="text"
                                                                                            class="form-control form-control-sm amount nbew_number_decimal"
                                                                                            maxlength="12"
                                                                                            name="employer_contri[1]"
                                                                                            placeholder="0.00"
                                                                                            data-required="true">
                                                                                    </div>

                                                                                </td>




                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    c)
                                                                                </td>

                                                                                <td class="text-start required"
                                                                                    colspan="3">Total</td>
                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>
                                                                                        <input type="text"
                                                                                            class="form-control form-control-sm amount nbew_number_decimal"
                                                                                            maxlength="12"
                                                                                            name="total_cps[1]"
                                                                                            placeholder="0.00"
                                                                                            data-required="true" readonly>

                                                                                    </div>

                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    d)
                                                                                </td>

                                                                                <td class="text-start required"
                                                                                    colspan="3">Actual remittance as per
                                                                                    records</td>
                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>
                                                                                        <input type="text"
                                                                                            class="form-control form-control-sm amount nbew_number_decimal"
                                                                                            name='actual_remittance[1]'
                                                                                            maxlength="12"
                                                                                            placeholder="0.00"
                                                                                            data-required="true">
                                                                                    </div>

                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    e)
                                                                                </td>

                                                                                <td class="text-start required"
                                                                                    colspan="3">Whether amount is
                                                                                    credited
                                                                                    in appropriate Head of Accounts
                                                                                    (Yes/No).If
                                                                                    'No' Raise an audit slip and mention the
                                                                                    slip No.</td>
                                                                                <td>
                                                                                    <select
                                                                                        class="form-select form-select-sm mb-1 credited-select"
                                                                                        name="creditedin_appropriatehead[1]"
                                                                                        data-required="true">
                                                                                        <option value="">Select
                                                                                        </option>
                                                                                        <option value="Y">Yes</option>
                                                                                        <option value="N">No</option>
                                                                                    </select>

                                                                                    <textarea class="form-control form-control-sm mt-1 d-none credited_remarks nbew_specialchartextandnumber"
                                                                                        maxlength="11" data-required="true" placeholder="Please provide details" name="credited_remarks[1]"></textarea>

                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    f)
                                                                                </td>
                                                                                <td class="text-start required"
                                                                                    colspan="3">Shortfall in Remittance
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>
                                                                                        <input type="text"
                                                                                            maxlength="12"
                                                                                            class="form-control form-control-sm nbew_specialchartextandnumber"
                                                                                            name="shortfall_remittance[1]"
                                                                                            placeholder="0.00"
                                                                                            data-required="true" readonly>
                                                                                    </div>

                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-start" style="width: 3%;">
                                                                                    g)
                                                                                </td>
                                                                                <td class="text-start required"
                                                                                    colspan="3">Remittance in Excess
                                                                                </td>
                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">₹</span>

                                                                                        <input type="text"
                                                                                            maxlength="12"
                                                                                            class="form-control form-control-sm amount"
                                                                                            name="remittanceexcess_cps[1]"
                                                                                            id="remittanceexcess_cps"
                                                                                            placeholder="0.00"
                                                                                            data-required="true" readonly>



                                                                                    </div>

                                                                                    <textarea class="form-control form-control-sm mt-1 d-none remittanceexcess_remarks nbew_specialchartextandnumber"
                                                                                        maxlength="13" data-required="true" placeholder="Please provide details" name="remittanceexcess_remarks[1]"></textarea>

                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>




                                                                <div id="shortfall_wrap[1]" class="d-none">
                                                                    <table
                                                                        class="table table-bordered shortfall-table professional-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>S.No</th>
                                                                                <th>Shortfall Month</th>
                                                                                <th>Amount</th>
                                                                                <th>Head of Account</th>
                                                                                <th>Voucher Number</th>
                                                                                <th>Treasury / Sub Treasury</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="sno">1</td>




                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">
                                                                                            <i class="ti ti-calendar"></i>
                                                                                        </span>

                                                                                        <select
                                                                                            class="form-control form-control-sm shortfallMonth"
                                                                                            name="shortfallmonth[1]">
                                                                                            <option value="">Select
                                                                                                Month
                                                                                            </option>

                                                                                        </select>
                                                                                    </div>
                                                                                </td>



                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">
                                                                                            <i
                                                                                                class="ti ti-currency-rupee"></i>
                                                                                        </span>
                                                                                        <input type="text"
                                                                                            class="form-control nbew_number_decimal shortfallamount"
                                                                                            placeholder="0.00"
                                                                                            maxlength="12"
                                                                                            name="shortfallamount[1]">
                                                                                    </div>
                                                                                </td>



                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">
                                                                                            <i class="ti ti-book"></i>
                                                                                        </span>
                                                                                        <input type="text"
                                                                                            class="form-control nbew_specialchartextandnumber"
                                                                                            placeholder="Head of Account"
                                                                                            maxlength="16"
                                                                                            name="shortfallfeadofaccount[1]">
                                                                                    </div>
                                                                                </td>

                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">
                                                                                            <i class="ti ti-receipt"></i>
                                                                                        </span>
                                                                                        <input type="text"
                                                                                            class="form-control nbew_number"
                                                                                            placeholder="Voucher No."
                                                                                            name="shortfallvno[1]"
                                                                                            maxlength="14">
                                                                                    </div>
                                                                                </td>


                                                                                <td>
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text bg-light">
                                                                                            <i
                                                                                                class="ti ti-building-bank"></i>
                                                                                        </span>
                                                                                        <input type="text"
                                                                                            class="form-control nbew_text"
                                                                                            name="shortftreasury[1]"
                                                                                            placeholder="Treasury / Sub Treasury"
                                                                                            maxlength="100">
                                                                                    </div>
                                                                                </td>



                                                                                <td class="text-center">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-success addshortfall-row">+</button>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-danger removeshortfall-row d-none">−</button>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>

                                                                        <tfoot>
                                                                            <tr class="table-light fw-semibold">
                                                                                <td colspan="2" class="text-end">Total
                                                                                    Shortfall</td>
                                                                                <td class="text-end">
                                                                                    <div
                                                                                        class="input-group input-group-sm">
                                                                                        <span
                                                                                            class="input-group-text">₹</span>
                                                                                        <input type="text"
                                                                                            name="shortfall_total[1]"
                                                                                            value="0.00" readonly
                                                                                            class="form-control text-end shortfall_total">
                                                                                    </div>
                                                                                </td>
                                                                                <td colspan="4"></td>
                                                                            </tr>

                                                                        </tfoot>



                                                                    </table>
                                                                </div>



                                                            </div>






                                                            <!-- Original CPS Set Content Ends -->

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- END SET -->

                                        </div>

                                </div>

                                <!-- Footer Buttons -->
                                <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap mb-4">

                                    {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                        Close
                                    </a> --}}

                                    <button type="button" id= "btnSaveDraft"
                                        class="btn btnSaveDraft button_save button_saveforcps">
                                        <i class="bi bi-check-circle"></i> Save Draft
                                    </button>

                                    <button type="button" id= "btnUpdateCps"
                                        class="btn btnSaveDraft btn-primary button_saveforcps d-none">
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>

                                    <button type="button" id="finalizeCpsBtn" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Finalize
                                    </button>

                                </div>

                                </form>






                            </div>



                        </div>
            </div>

            </section>
            @endif

        </div>
    </div>
    </div>

   

    <script src="../assets/js/vendor.min.js"></script>
    <!-- Import Js Files -->



    <!-- solar icons -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> -->
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
    <script src="../assets/js/apps/notes.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>

    <!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
    <script>
        $(document).ready(function() {
            // fetchCpsData();
            restrictSpecialChars('.nbew_text');
            restrictSpecialChars('.nbew_number');
            restrictSpecialChars('.nbew_number_decimal');
            restrictSpecialChars('.nbew_specialchartextandnumber');


        });


        $(document).on(
            'input paste',
            'input[name^="employee_contri"], input[name^="employer_contri"]',
            function() {

                const match = this.name.match(/\[(\d+)\]/);
                if (!match) return;

                calculateTotal(match[1]);
            }
        );


        function cleanNumber(val) {
            return parseFloat(
                (val || '').toString().replace(/[^0-9.]/g, '')
            ) || 0;
        }


        function calculateTotal(index) {


            const employee = cleanNumber(
                $(`input[name="employee_contri[${index}]"]`).val()
            );

            const employer = cleanNumber(
                $(`input[name="employer_contri[${index}]"]`).val()
            );

            const total = employee + employer;

            const $output = $(`input[name="total_cps[${index}]"]`);
            if ($output.length) {
                $output.val(total.toFixed(2));
            }

            calculateShortfall(index);
            calculateExcess(index);

        }


        function calculateShortfall(index) {
            const total = cleanNumber($(`input[name="total_cps[${index}]"]`).val());
            const actual = cleanNumber($(`input[name="actual_remittance[${index}]"]`).val());
            // const shortfall = total - actual;
            const shortfall = Math.max(0, total - actual);

            const $shortfallInput = $(`input[name="shortfall_remittance[${index}]"]`);
            const $wrap = $(`#shortfall_wrap\\[${index}\\]`);

            $shortfallInput.val(shortfall.toFixed(2));

            if (shortfall > 0) {
                $wrap.removeClass('d-none');
                $wrap
                    .find('input, textarea, select')
                    .attr('data-required', 'true');
            } else {
                $wrap.addClass('d-none');
                $wrap
                    .find('input, textarea, select')
                    .removeAttr('data-required')
                    .removeClass('is-invalid')
                    .val('');
            }
        }



        $(document).on(
            'input paste',
            'input[name^="total_cps"], input[name^="actual_remittance"]',
            function() {

                const match = this.name.match(/\[(\d+)\]/);
                if (!match) return;

                calculateShortfall(match[1]);
            }
        );

        function calculateExcess(index) {
            const total = cleanNumber(
                $(`input[name="total_cps[${index}]"]`).val()
            );

            const actual = cleanNumber(
                $(`input[name="actual_remittance[${index}]"]`).val()
            );

            let excess = 0;

            if (actual > total) {
                excess = actual - total;
            }

            const $excessInput = $(
                `input[name="remittanceexcess_cps[${index}]"]`
            );

            const $remarks = $(
                `textarea[name="remittanceexcess_remarks[${index}]"]`
            );

            $excessInput.val(excess.toFixed(2));

            /* 🔹 SHOW / HIDE REMARKS */
            if (excess > 0) {
                $remarks.removeClass('d-none');

            } else {
                $remarks.addClass('d-none');
                $remarks.val('');

            }
        }

        $(document).on(
            'input paste',
            'input[name^="actual_remittance"], input[name^="total_cps"]',
            function() {

                const index = $(this)
                    .attr('name')
                    .match(/\[(\d+)\]/)?.[1];

                if (index !== undefined) {
                    calculateExcess(index);
                }
            }
        );

        $(document).on('click', '.addshortfall-row', function() {

            const $tbody = $(this).closest('table').find('tbody');
            const $lastRow = $tbody.find('tr:last');
            const $newRow = $lastRow.clone();

            // clear input values
            $newRow.find('input').val('');

            $newRow.find('.removeshortfall-row').removeClass('d-none');

            $tbody.append($newRow);

            // reapply restrictions
            restrictSpecialChars('.nbew_text');
            restrictSpecialChars('.nbew_number');
            restrictSpecialChars('.nbew_number_decimal');
            restrictSpecialChars('.nbew_specialchartextandnumber');

            // update S.No
            $tbody.find('tr').each(function(i) {
                $(this).find('.sno').text(i + 1);
            });
        });


        $(document).on('click', '.removeshortfall-row', function() {

            const $table = $(this).closest('table');
            const $tbody = $table.find('tbody');

            if ($tbody.find('tr').length === 1) return; // keep at least one row

            $(this).closest('tr').remove();

            $tbody.find('tr').each(function(i) {
                $(this).find('.sno').text(i + 1);
            });

            calculateShortfallFooter($table);
        });


        function calculateShortfallFooter($table) {
            let total = 0;

            // Sum row-wise shortfall values
            $table.find('tbody tr').each(function() {
                const val = cleanNumber(
                    $(this).find('input.shortfallamount').val() || 0
                );
                total += val;
            });

            total = total.toFixed(2);

            $table.find('tfoot input.shortfall_total').val(total);

        }


        // Trigger calculation on input or paste
        $(document).on('input paste', '.shortfallamount', function() {
            const $table = $(this).closest('table');
            calculateShortfallFooter($table);
        });

        $(document).on('keydown', 'input[readonly], textarea[readonly]', function(e) {
            const key = (e.key || '').toLowerCase();
            const isPasteShortcut =
                ((e.ctrlKey || e.metaKey) && key === 'v') ||
                (e.shiftKey && key === 'insert');

            if (isPasteShortcut) {
                e.preventDefault();
                return false;
            }
        });

        $(document).on('beforeinput paste drop', 'input[readonly], textarea[readonly]', function(e) {
            const originalEvent = e.originalEvent;

            if (e.type === 'beforeinput') {
                const inputType = originalEvent?.inputType || '';
                if (inputType !== 'insertFromPaste' && inputType !== 'insertFromDrop') {
                    return;
                }
            }

            e.preventDefault();
            return false;
        });


        function fillCpsSet(setNo, data) {
            // Decode JSON strings to array

            function formatDateDMY(dateStr) {
                if (!dateStr) return '';

                const parts = dateStr.split('-'); // yyyy-mm-dd
                if (parts.length !== 3) return '';

                return `${parts[2]}-${parts[1]}-${parts[0]}`; // dd-mm-yyyy
            }


            const set = document.querySelectorAll('.cps-set')[setNo - 1];
            if (!set) return;

            set.querySelector('.transcpsid').value = data.transcpsid || '';

            let cpsEmployeeMonths = [];
            let cpsEmployerMonths = [];

            try {
                cpsEmployeeMonths = data.cps_month_employee ? JSON.parse(data.cps_month_employee) : [];
            } catch (e) {
                cpsEmployeeMonths = [];
                console.warn('Invalid JSON for employee months:', data.cps_month_employee);
            }

            try {
                cpsEmployerMonths = data.cps_month_employer ? JSON.parse(data.cps_month_employer) : [];
            } catch (e) {
                cpsEmployerMonths = [];
                console.warn('Invalid JSON for employer months:', data.cps_month_employer);
            }



            // Employee months
            const empSelect = set.querySelector(`[name="cps_month_employee[${setNo}]"]`);
            if (empSelect) {
                $(empSelect).val(cpsEmployeeMonths).trigger('change');
            }

            // Employer months
            const empSelect2 = set.querySelector(`[name="cps_month_employer[${setNo}]"]`);
            if (empSelect2) {
                $(empSelect2).val(cpsEmployerMonths).trigger('change');
            }


            // set.querySelector(`[name="department_name[${setNo}]"]`).value = data.department_name || '';

            set.querySelector(`[name="treasury_cps[${setNo}]"]`).value = data.treasury_cps || '';
            set.querySelector(`[name="ddocode_cps[${setNo}]"]`).value = data.ddocode_cps || '';

            set.querySelector(`[name="cpsNo[${setNo}]"]`).value = data.cpsno || '';
            // set.querySelector(`[name="cps_year[${setNo}]"]`).value = data.cps_year || '';


            set.querySelector(`[name="name[${setNo}]"]`).value = data.emp_name || '';

            const empName = data.emp_name || 'Employee';
            const cpsNo = data.cpsno || '---';

            const accordionItem = set.closest('.accordion-item');


            if (accordionItem) {
                accordionItem.dataset.empName = empName;
                accordionItem.dataset.cpsNo = cpsNo;

                const titleEl = accordionItem.querySelector('.accordion-title');
                if (titleEl) {
                    titleEl.innerHTML = `
            <span class="title-text">
                CPS Set ${setNo}: Employee & Remittance Details
            </span>

            <span class="emp-badge">${empName}</span>

            <span class="cps-badge">
                CPS No: ${cpsNo}
            </span>
        `;
                }
            }
            set.querySelector(`[name="designation[${setNo}]"]`).value = data.designation || '';
            set.querySelector(`[name="payscale[${setNo}]"]`).value = data.payscale || '';
            // set.querySelector(`[name="departmentname[${setNo}]"]`).value = data.departmentname_cps || '';

            const scoreValue = data.missing_credits || 'Y';
            const scoreRadio = set.querySelector(`input[name="score[${setNo}]"][value="${scoreValue}"]`);
            if (scoreRadio) scoreRadio.checked = true;

            const q1Value = data.employee_cont || 'Y';
            const q2Value = data.employer_cont || 'Y';

            set.querySelector(`input[name="q1[${setNo}]"][value="${q1Value}"]`)?.click();
            set.querySelector(`input[name="q2[${setNo}]"][value="${q2Value}"]`)?.click();

            const details1 = set.querySelector(`[name="details1[${setNo}]"]`);
            const details2 = set.querySelector(`[name="details2[${setNo}]"]`);


            if (q1Value === 'N') {
                details1.value = data.employee_remarks || '';
                details1.classList.remove('d-none');
            } else {
                details1.value = '';
                details1.classList.add('d-none');
            }

            if (q2Value === 'N') {
                details2.value = data.employer_remarks || '';
                details2.classList.remove('d-none');
            } else {
                details2.value = '';
                details2.classList.add('d-none');
            }

            set.querySelector(
                `[name="employeefinancialyear[${setNo}]"]`
            ).value = String(data.employeefinancialyear);

            set.querySelector(
                `[name="employerfinancialyear[${setNo}]"]`
            ).value = String(data.employerfinancialyear);


            // Remittance fields

            set.querySelector(`[name="employee_contri[${setNo}]"]`).value = data.total_employee_cont || '';
            set.querySelector(`[name="employer_contri[${setNo}]"]`).value = data.total_employer_cont || '';
            set.querySelector(`[name="total_cps[${setNo}]"]`).value = data.total_cps || '';
            set.querySelector(`[name="actual_remittance[${setNo}]"]`).value = data.actual_remittance || '';

            // Select both credited dropdown and its remarks textarea
            const creditedSelect = set.querySelector(`[name="creditedin_appropriatehead[${setNo}]"]`);
            const creditedRemarks = set.querySelector(`[name="credited_remarks[${setNo}]"]`);

            // Show/hide and populate credited remarks
            if (data.creditedin_appropriatehead === 'N') {
                creditedSelect.value = 'N';
                creditedRemarks.value = data.approproatehead_remarks || '';
                creditedRemarks.classList.remove('d-none');

            } else {
                creditedSelect.value = 'Y';
                creditedRemarks.value = '';
                creditedRemarks.classList.add('d-none');

            }

            const shortfallValue = parseFloat(data.shortfall_remittance || 0);
            const excessInput = set.querySelector(`[name="remittanceexcess_cps[${setNo}]"]`);
            const remarksTextarea = set.querySelector(`[name="remittanceexcess_remarks[${setNo}]"]`);

            set.querySelector(`[name="shortfall_remittance[${setNo}]"]`).value = data.shortfall_remittance || '';
            set.querySelector(`[name="remittanceexcess_cps[${setNo}]"]`).value = data.remittanceexcess_cps || '';

            excessInput.value = data.remittanceexcess_cps || '0.00';

            // Show/hide textarea based on excess > 0
            if (parseFloat(excessInput.value) > 0) {
                remarksTextarea.classList.remove('d-none');
                remarksTextarea.value = data.remittanceexcess_remarks || '';
            } else {
                remarksTextarea.classList.add('d-none'); // hide if 0
                remarksTextarea.value = ''; // clear
            }


            if (shortfallValue > 0) {

                populateShortfalls(setNo, data.shortfalls);
                // At the very end of populateShortfalls function
                const $table = $(`#shortfall_wrap\\[${setNo}\\] table`);
                calculateShortfallFooter($table);


            }



            if (data.cps_completed === 'Y') {

                document
                    .querySelectorAll(
                        '.ddoandtreasury input, .ddoandtreasury textarea, .ddoandtreasury select, .ddoandtreasury button')
                    .forEach(el => el.disabled = true);

                set
                    .querySelectorAll('input, textarea, select, button')
                    .forEach(el => el.disabled = true);

                ['.addRow', '.deleteRow'].forEach(selector => {
                    document.querySelectorAll(selector).forEach(btn => {
                        btn.style.display = 'none';
                    });
                });


                const updateBtn = document.getElementById('btnUpdateCps');
                if (updateBtn) updateBtn.disabled = true;

                const finalizeBtn = document.getElementById('finalizeCpsBtn');
                if (finalizeBtn) {
                    finalizeBtn.disabled = true;
                    finalizeBtn.style.pointerEvents = 'none';
                    finalizeBtn.style.opacity = '0.6';
                }
            }

        }

        function formatMonthYear(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const options = {
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-US', options).replace(' ', '-');
        }


        $(document).on('change', '.shortfallMonth', function() {

            const current = $(this);
            const currentVal = current.val();
            if (!currentVal) return;

            const match = current.attr('name').match(/\[(\d+)\]/);
            if (!match) return;

            const setNo = match[1];

            const allSelects = $(`#shortfall_wrap\\[${setNo}\\] .shortfallMonth`);

            let duplicate = false;

            allSelects.each(function() {
                if (this !== current[0] && $(this).val() === currentVal) {
                    duplicate = true;
                    return false;
                }
            });

            if (duplicate) {
                alert('This month is already selected.');
                current.val('');
            }
        });



        function populateShortfalls(setNo, shortfalls = []) {
            const shortfallContainer = document.querySelector(`#shortfall_wrap\\[${setNo}\\] tbody`);
            if (!shortfallContainer) return;

            // Show the shortfall wrapper
            const shortfallWrap = document.querySelector(`#shortfall_wrap\\[${setNo}\\]`);
            if (shortfallWrap) shortfallWrap.classList.remove('d-none');

            // Clear existing rows
            shortfallContainer.innerHTML = '';

            if (Array.isArray(shortfalls) && shortfalls.length > 0) {
                shortfalls.forEach((sf, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td class="sno">${index + 1}</td>

                <td>
                    <input type="hidden" name="shortfallid[${setNo}]" value="${sf.shortfallid || ''}">


                    <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">
                        <i class="ti ti-calendar"></i>
                    </span>

                       <select class="form-control form-control-sm shortfallMonth"
                        name="shortfallmonth[${setNo}]">
                        <option value="">Select Month</option>
                    </select>
                </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-currency-rupee"></i>
                        </span>
                        <input type="text" class="form-control nbew_number_decimal shortfallamount"
                            name="shortfallamount[${setNo}]" value="${sf.shortfall_amount || 0}"
                            placeholder="0.00" maxlength="12" >
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-book"></i>
                        </span>
                        <input type="text" class="form-control nbew_specialchartextandnumber"
                            name="shortfallfeadofaccount[${setNo}]" value="${sf.shortfall_headofaccount || ''}"
                            placeholder="Head of Account" maxlength="16" >
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-receipt"></i>
                        </span>
                        <input type="text" class="form-control nbew_number"
                            name="shortfallvno[${setNo}]" value="${sf.shortfall_voucher || ''}"
                            placeholder="Voucher No." maxlength="14">
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-building-bank"></i>
                        </span>
                        <input type="text" class="form-control nbew_text"
                            name="shortftreasury[${setNo}]" value="${sf.shortfall_treasury || ''}"
                            placeholder="Treasury / Sub Treasury" maxlength="100" >
                    </div>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success addshortfall-row">+</button>
                    <button type="button" class="btn btn-sm btn-danger removeshortfall-row ${index === 0 ? 'd-none' : ''}">−</button>
                </td>
            `;
                    shortfallContainer.appendChild(row);

                    // Get selected employee months
                    const empSelect = document.querySelector(`[name="cps_month_employee[${setNo}]"]`);
                    const selectedMonths = $(empSelect).val() || [];

                    const shortfallSelect = row.querySelector('.shortfallMonth');

                    // Month name map
                    const monthMap = {
                        "01": "Mar",
                        "02": "Apr",
                        "03": "May",
                        "04": "Jun",
                        "05": "Jul",
                        "06": "Aug",
                        "07": "Sep",
                        "08": "Oct",
                        "09": "Nov",
                        "10": "Dec",
                        "11": "Jan",
                        "12": "Feb",
                    };

                    // Populate only selected employee months
                    selectedMonths.forEach(m => {
                        const option = document.createElement('option');
                        option.value = m;
                        option.textContent = monthMap[m];
                        if (sf.shortfall_month === m) {
                            option.selected = true;
                        }
                        shortfallSelect.appendChild(option);
                    });

                });
            } else {
                // No shortfalls, add one empty row
                document.querySelector(`#shortfall_wrap\\[${setNo}\\] .addshortfall-row`).click();
            }

            if (shortfallWrap) {
                applyRestrictions(shortfallWrap);
            }

            // Reapply S.No for all rows
            shortfallContainer.querySelectorAll('tr').forEach((tr, idx) => {
                tr.querySelector('.sno').textContent = idx + 1;
            });

        }

        function resetCpsAccordions() {
            const container = document.getElementById('cpsSetsContainer');
            if (!container) return;

            // ✅ Remove all accordion-items except the first
            const items = container.querySelectorAll('.accordion-item');
            items.forEach((item, index) => {
                if (index !== 0) item.remove();
            });

            // ✅ Reset counters
            cpsSetCounter = 1;
            cpsCounter = 1;
            fieldCounter = 1;

            // ✅ Reset inputs inside first CPS set ONLY
            const firstSet = container.querySelector('.accordion-item .cps-set');
            if (firstSet) {
                firstSet.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.type === 'radio') {
                        el.checked = false;
                        // Set first radio of group as default checked
                        const name = el.name;
                        const firstRadio = firstSet.querySelector(`input[name="${name}"]`);
                        if (firstRadio) firstRadio.checked = true;
                    } else {
                        el.value = '';
                    }

                    el.classList.remove('is-invalid');

                    if (el.tagName === 'TEXTAREA') {
                        el.classList.add('d-none');
                    }

                    el.disabled = false;
                });

                const sNoInput = firstSet.querySelector('.sNo');
                if (sNoInput) sNoInput.value = '1';


            }


        }

        var form = $(".validation-wizard").show();


        function populate_performanceAudit(audit_data) {

            $('#performance_audit_div').show();
            $('#normal_audit_div').hide();

            if (!audit_data || !audit_data.data || audit_data.data.length === 0) {
                $('#prf_form').html('No data available');
                return;
            }



            let records = audit_data.data;
            let first = records[0];

            let teamHead = '';
            let teamMembers = '';
            let titlename = first.titleename;
            //const file = first.prf_filedetails.split('-');
            //const fileUrl = '/storage/' + (file[1] || '').replace(/\\/g, '/');

            if (first.auditeeresponse == 'A') {

                $('#prf_audit_submit').hide()
                $('#prf_statusmessage').show()
            } else {
                $('#prf_audit_submit').show()
                $('#prf_statusmessage').hide()
            }
            records.forEach(function(item) {
                if (item.auditteamhead === 'Y') {
                    teamHead = item.username + ' - ' + item.desigelname;
                } else {
                    teamMembers += (teamMembers ? '<br>' : '') +
                        item.username + ' - ' + item.desigelname;
                }
            });

            let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <b>Institution :</b> ${first.instename}
                        </div>
                        <div class="col-md-6">
                            <b>Audit Year :</b> ${first.yearname}
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <b>Proposed From Date :</b> ${convertDateFormatYmd_ddmmyy(first.fromdate)}
                        </div>
                        <div class="col-md-6">
                            <b>Proposed To Date :</b> ${convertDateFormatYmd_ddmmyy(first.todate)}
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <b>Quarter :</b> ${first.auditquarter}
                        </div>
                        <div class="col-md-6">
                            <b>Type of Audit :</b> ${first.typeofauditename}
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <b>Team Head :</b><br> ${teamHead || '-'}
                        </div>
                        <div class="col-md-6">
                            <b>Team Member(s) :</b><br> ${teamMembers || '-'}
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <b>Title :</b><br> ${titlename || '-'}
                        </div>
                        

                    </div>

                `;

            $('#prf_form').html(html);
            $('#prf_auditscheduleid').val(first.encrypted_auditscheduleid);
        }

        $(document).on('click', '#prf_audit_submit', function() {
            passing_alert_value('Confirmation', 'Are you sure to accept the schedule?', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            // }
            // );
            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                accept_prfAudit();
            });


        });

        function accept_prfAudit() {
            $('#prf_audit_submit').prop('disabled', true);
            $("#process_button").prop("disabled", true);

            // if ($callforrecordsForm.valid()) {


            // Create the FormData object
            var auditscheduleid = $('#prf_auditscheduleid').val()

            $.ajax({
                url: 'auditee/prfaudit_accept', // Replace with your endpoint
                method: 'POST',
                data: {
                    auditscheduleid: auditscheduleid
                },

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Pass CSRF token in headers
                },
                success: function(response) {
                    $("#process_button").prop("disabled", false);
                    $('#prf_audit_submit').hide()
                    // var validator = $("#audit_schedule").validate();
                    // validator.resetForm();
                    if (response.success) {


                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    }
                    $('#prf_audit_submit').hide()
                    $('#prf_statusmessage').show()

                },




                complete: function() {
                    $('#prf_audit_submit').prop('disabled', false);
                    $('#process_button').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    $("#process_button").prop("disabled", false);

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {

                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                    }
                }
            });
        }


        $(".validation-wizard").steps({
            headerTag: "h6",
            bodyTag: "section",
            transitionEffect: "fade",
            titleTemplate: '<span class="step">#index#</span> #title#',
            labels: {
                finish: "Submit",
            },

            onStepChanging: function(event, currentIndex, newIndex) {
                // if (currentIndex > newIndex) return true;

                // Step 0 to Step 1 logic
                if (currentIndex === 0 && newIndex === 1) {

                    if (recordStatus === 'Y') {
                        var audit_scheduleidget = $('#auditscheduleid').val();
                        acceptstatus(audit_scheduleidget);
                        return true;
                    } else if (recordStatus === 'N') {
                        fetch_audit_particulars_detail();
                        return true;
                    } else {
                        alert("No information Found");
                        $('.actions a[href="#next"]').parent('li').addClass('disabled');

                        return false;
                    }
                }

                if (currentIndex === 2 && newIndex === 3) {

                    if (recordStatus === 'Y' && auditofficerdetailStatus === 'N') {
                        return true;
                    }

                    passing_alert_value(
                        'Confirmation',
                        'Please Submit Office Details before proceeding.',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    return false;
                }

                // ? Step 2 to Step 3 (last page)   Add your condition here
                if (currentIndex === 1 && newIndex === 2) {
                    // Replace with your condition
                    if (recordStatus === 'N' && auditofficerdetailStatus == 'N') {
                        const $callforrecordsForm = $("#callforrecords");

                        event.preventDefault(); // Prevent form submission
                        restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");
                        validateRadioButtons();
                        if ($callforrecordsForm.valid()) {

                            getLabels_jsonlayout([{
                                id: 'confirmation_submit',
                                key: 'confirmation_submit'
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'forward_alert');
                            });

                            $("#process_button").addClass("button_confirmation");
                            $('#process_button').removeAttr('data-bs-dismiss');
                            //  $('.button_confirmation').data('auditplanid', auditplanid);
                        }
                        return false;
                    } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'N') {
                        $('.actions a[href="#finish"]').parent('li').addClass('disabled');

                        ofcUserDetailsTab();
                        return true;
                    } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'Y') {
                        $('.actions a[href="#finish"]').parent('li').addClass('disabled');

                        ofcUserDetailsTab();
                        return true;
                    } else if (recordStatus === 'N' && auditofficerdetailStatus == 'Y') {
                        $('.actions a[href="#finish"]').parent('li').addClass('disabled');

                        ofcUserDetailsTab();
                        return true;
                    } else {
                        return true;
                    }
                }
                ////////////previous//////////////


                if (currentIndex > newIndex) {
                    if (currentIndex === 1 && newIndex === 0) {
                        if (recordStatus === 'Y') {
                            return true;
                        } else if (recordStatus === 'N') {
                            passing_alert_value('Confirmation', 'Data is not saved! Please Submit the Records',
                                'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                            return false;
                        } else {
                            alert("Unexpected status or no data.");
                            return false;
                        }
                    }

                    if (currentIndex === 2 && newIndex === 1) {
                        if (recordStatus === 'Y' && auditofficerdetailStatus == 'Y') {
                            $('.actions a[href="#finish"]').parent('li').addClass('disabled');


                            ofcUserDetailsTab();
                            return true;
                        } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'N') {
                            passing_alert_value('Confirmation', 'Data is not saved! Please Submit the Records',
                                'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                            return false;
                        } else {
                            return true;
                        }
                    }
                }



            },

            onStepChanged: function(event, currentIndex, priorIndex) {

                if (currentIndex === 2) {

                    fetchalldata(lang);
                }
            },
            onFinishing: function(event, currentIndex) {

                return $("#auditee_officeusers").valid(); // Validate only on final step
            },

            onFinished: function(event, currentIndex) {
                // insertAuditOfficersDetail()
            }
        });


        function acceptAndProceed() {
            // Your custom logic here if needed


            // Move to the next step of the wizard
            $(".validation-wizard").steps("next");
        }


        ///////////////////////////////////////////////////////////////////////wizard/////////////////////////////////////////////
        function datepicker(fieldType, setdate, rowCount) {
            const fromVal = $(`#from_date${rowCount}`).val();
            const toVal = $(`#to_date${rowCount}`).val();

            let minDate = null;
            let maxDate = new Date();

            // Only apply limits if the paired field has a value
            if (fieldType === 'to_date' && fromVal) {
                const parts = fromVal.split('/');
                const fromDate = new Date(parts[2], parts[1] - 1, parts[0]);
                fromDate.setDate(fromDate.getDate() + 1);
                minDate = new Date(fromDate); // to_date must be after from_date

            }

            if (fieldType === 'from_date' && toVal) {
                const parts = toVal.split('/');
                const toDate = new Date(parts[2], parts[1] - 1, parts[0]);
                toDate.setDate(toDate.getDate() - 1);
                maxDate = new Date(toDate); // from_date must be before to_date
            }

            let inputId = `${fieldType}${rowCount}`;
            let fromvalclr = `from_date${rowCount}`;
            let tovalclr = `to_date${rowCount}`;
            let form = 'cleardateform';

            init_datepicker(inputId, minDate, maxDate, setdate, form, fromvalclr, tovalclr, 'serviceperiod');
        }

        // Global //
        let audit_schedule;
        let recordStatus;
        let auditofficerdetailStatus;

        //////////////////////////////////////////auditee officer details/////////////////////////////////////////
        function insertAuditOfficersDetail() {
            event.preventDefault();
            // ? Copy logic from #officeuser_submit handler here
            var lang = getLanguage('Y');
            var allowedusers = $('#auditee_ofcusercount').val();

            for (var i = 1; i <= allowedusers; i++) {
                applyValidationToNewFields(`officeusername[${i}]`, errorMessages[lang]['username']);
                applyValidationToNewFields(`officeuserdesignation[${i}]`, errorMessages[lang]['designation_ph']);
                applyValidationToNewFields(`officeuserfromdate[${i}]`, errorMessages[lang]['from_date']);
                //  applyValidationToNewFields(`officeusertodate[${i}]`, errorMessages[lang]['to_date']);

            }

            if ($("#auditee_officeusers").valid()) {
                $('#officeuser_submit').prop('disabled', true);
                var formData = $('#auditee_officeusers').serializeArray();
                // console.log(formData);
                // return;
                $.ajax({
                    url: 'audit/store_auditeeofficeusers',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.message == 'datasubmitsuccess') {

                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(text)[0],
                                    'confirmation_alert',
                                    'alert_header', 'alert_body', 'confirmation_alert');
                            });


                        } else {


                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(text)[0],
                                    'confirmation_alert',
                                    'alert_header', 'alert_body', 'confirmation_alert');
                            });
                            auditofficerdetailStatus = 'Y';
                            $('#auditeereponseget').val('A');
                            $('#addrowUsers').hide();
                            ofcUserDetailsTab();
                            $('.actions a[href="#finish"]').parent('li').addClass('disabled');
                            fetchalldata(lang)
                        }
                    },
                    complete: function() {
                        $('#officeuser_submit').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] || "Error Occurred";
                                passing_alert_value('Confirmation', alertMessage, 'confirmation_alert',
                                    'alert_header', 'alert_body', 'confirmation_alert');
                            });
                        }
                    }
                });
            }
        }

        function ofcUserDetailsTab(formname = '') {

            var auditscheduleid = $('#officeuseraudit_scheduleid').val();
            var sendintimation = $('#sentintimation').val();
            var status_officedet = $('#status_officedet').val();


            $.ajax({
                url: 'audit/fetch_auditeeofficeusers', // Replace with your endpoint
                method: 'POST',
                data: {
                    auditscheduleid: auditscheduleid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Pass CSRF token in headers
                },
                success: function(response) {
                    var auditeereponseget = $('#auditeereponseget').val();
                    if (formname == 'intimationform') {

                        if (response.exists == 0) {
                            //passing_alert_value('Confirmation', 'Please add auditee user details before accept', 'confirmation_alert','alert_header', 'alert_body', 'forward_alert');


                            $('#audituserdetailstab a').addClass('active');

                            audituserdetailsdiv.style.display = 'block';
                            audituserdetailsdiv.classList.add('show',
                                'active'); // Make it "active" for Bootstrap tab



                            //ofcUserDetailsTab();            
                        } else {
                            return 'success';
                        }

                    } else {
                        if (response.exists == 1) {

                            $('#addrowUsers').hide();
                            $('#EditrowUsers').empty();
                            $('#EditrowUsers').show();

                            var tablehead = ` <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-3 ms-2"></div>

                                                        <div class="col-md-2 ms-2"></div>

                                                        <div class="col-md-4 ms-2">
                                                            <center><b class="lang" key="serviceperiod">Service Period</b></center>
                                                        </div>

                                                        <div class="col-md-3"></div>
                                                    </div>

                                                    <div class="d-flex mt-2 work-row" id="row0">
                                                        <div class="col-md-1">
                                                            <label class="form-label lang" key="s_no" for="validationDefaultUsername">S.No</label>
                                                        </div>

                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="name" for="validationDefaultUsername">Name</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="designation" for="validationDefaultUsername">Designation</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="from_date" for="validationDefaultUsername">From Date</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="to_date" for="validationDefaultUsername">To Date</label>
                                                        </div>
                                                    </div>
                                                `;
                            $('#EditrowUsers').append(tablehead);
                            translate();

                            var rowCount = 0; // Initialize row count

                            // Loop through the data and append to the HTML
                            $.each(response.fetch_auditeeofficeusers, function(index, item) {
                                rowCount++; // Increment rowCount for each iteration


                                // Create HTML template for a new row
                                var appendHTML = `
                                        <div class="d-flex mt-2 work-row-edit" id="row${rowCount}">
                                            <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                                            <div class="col-md-1">
                                                <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                                            </div>

                                            <div class="col-md-3 ms-2">
                                                <input type="text" class="form-control name" maxlength="50" name="officeusername[${rowCount}]" value="${item.ofc_username || ''}" placeholder="Enter Name">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <input type="text" class="form-control name"  maxlength="100" name="officeuserdesignation[${rowCount}]" id="designation${rowCount}" value="${item.ofc_designation || ''}" placeholder="Enter Designation">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <div class="input-group" onclick="datepicker('from_date','','1${rowCount}')">
                                                    <input type="text"  id="from_date1${rowCount}"  value="${item.converted_service_fromdate || ''}" class="form-control datepicker" 
                                                               name="officeuserfromdate[${rowCount}]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <div class="input-group" onclick="datepicker('to_date','','1${rowCount}')">
                                                    <input type="text"  value="${item.converted_service_todate || ''}" class="form-control datepicker" id="to_date1${rowCount}"
                                                               name="officeusertodate[${rowCount}]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-3 actionbtns">
                                                <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                                                    <i class="ti ti-circle-plus"></i> 
                                                </button>
                                                <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removeRow(this,'edit')">
                                                    <i class="ti ti-circle-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;



                                $('#EditrowUsers').append(appendHTML);
                            });
                            if (sendintimation == 'F') {
                                $('#EditrowUsers input').attr('disabled', true);
                                $('.actionbtns').hide();
                                $('#statusmessage_auditeeusers').show();


                            }


                        } else {

                            $('#addrowUsers').show();
                            $('#EditrowUsers').hide();

                            if (sendintimation == 'F') {
                                $('#addrowUsers input').attr('disabled', true);
                                $('.actionbtns').hide();

                            }

                        }

                    }


                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {

                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                    }
                }
            });
        }

        // Function to add a new row using jQuery
        function addNewWorkRow(event, action = '') {
            // Get the next row index based on existing rows
            const rowCount = $('.work-row-' + action + '').length + 1; // Start from 2, hence +1
            //var allowedusers ='<?php //echo $allowedusers;
            ?>';
            var allowedusers = $('#auditee_ofcusercount').val();
            if (rowCount > allowedusers) {
                getLabels_jsonlayout([{
                    id: 'alloweduserlimit',
                    key: 'alloweduserlimit'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', Object.values(
                            text)[0], 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                });
                return; // Prevent adding a new row
            }

            // HTML for the new row
            const newRowHtml = `
                <div class="d-flex mt-2 work-row-${action}" id="row${rowCount}">
                    <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name"  data-placeholder-key="username" name="officeusername[${rowCount}]"  value="" placeholder="Enter Name">
                    </div>

                    <div class="col-md-2 ms-2">
                        <input type="text" class="form-control name" data-placeholder-key="designation_ph" name="officeuserdesignation[${rowCount}]" id="designation${rowCount}" value="" placeholder="Enter Designation">
                    </div>

                    <div class="col-md-2 ms-2">
                        <div class="input-group" onclick="datepicker('from_date','','${rowCount}')">
                            <input type="text"  id="from_date${rowCount}" class="form-control datepicker"  name="officeuserfromdate[${rowCount}]" placeholder="dd/mm/yyyy" />
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div>

                    <div class="col-md-2 ms-2">
                        <div class="input-group" onclick="datepicker('to_date','','${rowCount}')">
                            <input type="text"  id="to_date${rowCount}"  class="form-control datepicker"  name="officeusertodate[${rowCount}]" placeholder="dd/mm/yyyy" />
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'${action}')">
                            <i class="ti ti-circle-plus"></i> 
                        </button>
                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removeRow(this,'${action}')">
                            <i class="ti ti-circle-minus"></i>
                        </button>
                    </div>
                </div>
            `;

            if (action == 'insert') {
                $('#addrowUsers').append(newRowHtml);

            } else {
                $('#EditrowUsers').append(newRowHtml);

            }
            var lang = getLanguage('Y');

            updatePlaceholders(lang);



            // Append the new row using jQuery to the container
        }



        function applyValidationToNewFields(inputName, message) {
            let $input = $("[name='" + inputName + "']"); // Select input by name
            // alert(inputName)
            if ($input.length) {

                let validator = $("#auditee_officeusers").data("validator"); // Get validator instance

                if (!validator) {

                    $("#auditee_officeusers").validate({ // Initialize validation if not already done
                        errorPlacement: function(error, element) {
                            // Check if the element has the 'datepicker' class
                            if (element.hasClass('datepicker')) {

                                // Insert the error message after the input-group, so it appears below the input and icon
                                //  error.insertAfter('.form-control');
                                element.closest('.input-group').parent().append(error);
                            } else {

                                // Default behavior: insert the error message after the input field
                                error.insertAfter(element);
                            }
                        }
                    });
                    validator = $("#auditee_officeusers").data("validator");
                }

                $input.rules("remove"); // Remove any existing validation rules

                // Ensure rules are applied only once
                $input.rules("add", {
                    required: true,
                    messages: {
                        required: message // Custom error message for the required rule
                    }
                });

                validator.element($input); // Validate the element

                // ? Ensure validation runs on change without removing existing messages
                $input.on("change", function() {
                    $(this).valid(); // Validate when the input changes
                });
            } else {
                //  console.error("? Element not found:", inputName); // Handle case if element is not found
            }
        }


        // Function to remove a row
        function removeRow(button, action) {
            // Find the closest row and remove it
            const rowToRemove = $(button).closest('.work-row-' + action + '');
            rowToRemove.remove();

            // Recalculate and update the S.NO values after removing a row
            //updateSerialNumbers();
        }


        /////////////////////////////////////////auditee officer details-END ////////////////////////////////////
        function fetchalldata(lang) {
            $.ajax({
                url: 'audit/audit_scheduledetails', 
                 data: {
                    auditmode: 'N'
                },// Replace with your endpoint
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Pass CSRF token in headers
                },
                success: function(response) {


                    $('#fromquarter').val(response.Quarter['fromquarter']);
                    $('#toquarter').val(response.Quarter['toquarter']);

                    audit_schedule = response.data;
                    const epaccsIds = [
    27590, 27502, 27623, 27674, 27640, 27421, 27657,
    27469, 27604, 27549, 27328, 27505, 27663, 27616,
    27461, 27630, 27561, 27607, 27655, 27556, 27338,
    27558, 27559, 27443, 27396, 27662, 27636, 27536
];

const is_epaccs = epaccsIds.includes(audit_schedule[0].auditscheduleid);

if (audit_schedule[0].datafromapi === 'Y' && !is_epaccs) {
                        $('#normal_audit_div').hide()
                        $('#epaccs_div').show();
                        return;
                    }
                    $auditmode = audit_schedule[0].auditmode;
                    // if ($auditmode == 'P') {

                    //     populate_performanceAudit(response)

                    // } else {
                        $('#normal_audit_div').show()
                        $('#performance_audit_div,#epaccs_div').hide();

                        $('#h_deptcode').val(audit_schedule[0].deptcode);
                        $('#h_catcode').val(audit_schedule[0].catcode);
                        $('#instid_cps').val(audit_schedule[0].encrypted_instid);
                        $('#auditscheduleid_cps').val(audit_schedule[0].encrypted_auditscheduleid);
                        // console.log('before:' + audit_schedule[0].encrypted_auditscheduleid)
                        const audit_period = response.auditperiod;

                        var concat = audit_period.from + ' - ' + audit_period.to;
                        // $('#financial_year').val(concat);
                        const audit_year = audit_schedule[0].yearname;
                        $('#financial_year').val(audit_year);



                        if (audit_schedule[0].deptcode == '01' && audit_schedule[0].annadhanam_only == 'Y') {
                            $('#annadhanamDiv').show();
                            const annadhanam_year = audit_schedule[0].annadhanamyear;
                            $('#annadhanam_year').val(annadhanam_year);
                        } else {
                            $('#annadhanamDiv').hide();
                        }



                        fetchCpsData();



                        if (audit_schedule && audit_schedule.length > 0) {
                            $('#sentintimation').val(audit_schedule[0].sendintimation);
                            $('#status_officedet').val(audit_schedule[0].status_officedet);

                            var sendintimation = $('#sentintimation').val();
                            var status_officedet = $('#status_officedet').val();
                            var auditeeresponse = audit_schedule[0].auditeeresponse;

                            if (sendintimation == 'F') {
                                $('#buttonsforacceptance').hide();
                                $('#statusmessage').show();
                                recordStatus = 'Y'
                                auditofficerdetailStatus = 'Y'


                            } else if (auditeeresponse === 'A' && (status_officedet !== 'Z' ||
                                    status_officedet === null)) {
                                recordStatus = 'N';
                                auditofficerdetailStatus = 'Y';

                            } else if (auditeeresponse === 'A' && (status_officedet === 'Z' ||
                                    status_officedet ==
                                    null || status_officedet ==
                                    '') && (sendintimation !==
                                    'F' || sendintimation == null || sendintimation == '')) {

                                $('#buttonsforacceptance').show();
                                $('#officeuser_submit').text('Update');

                                // Change button color: success → primary
                                $('#officeuser_submit')
                                    .removeClass('btn-success')
                                    .addClass('btn-primary');
                                // $('#statusmessage').show();
                                recordStatus = 'Y'
                                auditofficerdetailStatus = 'N'


                            } else if (auditeeresponse == 'R') {
                                passing_alert_value('Confirmation',
                                    'Auditee Officer Detail is not Submitted! Please fill the form.',
                                    'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                                $('#buttonsforacceptance').show();
                                // $('#statusmessage').show();
                                recordStatus = 'Y'
                                auditofficerdetailStatus = 'N'


                            } else {
                                $('#buttonsforacceptance').show();
                                $('#statusmessage').hide();
                                recordStatus = 'N'
                                auditofficerdetailStatus = 'N'
                            }



                            if (lang == 'ta') {
                                var instname = audit_schedule[0].insttname;
                                var typeofaudit = audit_schedule[0].typeofaudittname;
                                var auditquarter = audit_schedule[0].auditquartertamil;

                            } else {
                                var instname = audit_schedule[0].instename;
                                var typeofaudit = audit_schedule[0].typeofauditename;
                                var auditquarter = audit_schedule[0].auditquarter;

                            }

                            $('#inst_name').text(instname);
                            $('#audit_type').val(typeofaudit);
                            $('#audit_period').val(auditquarter);

                            $('#officeuseraudit_scheduleid').val(audit_schedule[0].encrypted_auditscheduleid);
                            $('#auditee_ofcusercount').val(audit_schedule[0].auditee_ofcusercount);

                            $('#audit_scheduleid').val(audit_schedule[0].encrypted_auditscheduleid);
                            $('#auditscheduleid').val(audit_schedule[0].encrypted_auditscheduleid);




                            $('#auditscheduleidNewforGet').val(audit_schedule[0].encrypted_auditscheduleid);

                            $('#auditeeresponsehidden').val(auditeeresponse);
                            $('#auditeereponseget').val(auditeeresponse);



                            $('#entry_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                                .fromdate));
                            $('#start_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                                .fromdate));
                            $('#end_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                                .todate));


                            $('#tm_hid').next('.select2').remove(); // Removes any extra Select2 container
                            $('#tm_uid').next('.select2').remove();
                            // Clear existing options in both dropdowns
                            $('#tm_uid, #tm_hid').empty();

                            // Get selected team members' user IDs
                            const selectedTeamMembers = audit_schedule.map(member => member.userid);

                            // If there are any selected team members
                            if (selectedTeamMembers.length > 0) {

                                // Iterate over the response data to append options dynamically
                                audit_schedule.forEach(member => {
                                    // Check if the member is in the selected list
                                    const isSelected = selectedTeamMembers.includes(member.userid);
                                    // Check if the member is a team member or a team head based on 'teamtype'
                                    if (member.auditteamhead === 'N') {
                                        // Create a new option element for team members
                                        let newOption = new Option(
                                            `${lang === 'ta' ?member.usertamilname +' - '+ member.desigtlname : member.username +'-'+member.desigelname}`, // Display text
                                            member.userid, // Option value
                                            isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
                                            isSelected // Mark as selected for Select2
                                        );

                                        // Append the new option to the Team Member dropdown
                                        $('#tm_uid').append(newOption);
                                    } else if (member.auditteamhead === 'Y') {
                                        // Create a new option element for team heads
                                        let newOption = new Option(
                                            `${lang === 'ta' ?member.usertamilname +' - '+ member.desigtlname : member.username +'-'+member.desigelname}`, // Display text
                                            member.userid, // Option value
                                            isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
                                            isSelected // Mark as selected for Select2
                                        );

                                        // Append the new option to the Team Head dropdown
                                        $('#tm_hid').append(newOption);
                                    }
                                });

                                // Re-initialize Select2 for both dropdowns
                                $('#tm_uid').select2({
                                    placeholder: "Select Team Member",
                                    allowClear: true
                                });

                                $('#tm_hid').select2({
                                    placeholder: "Select Team Head",
                                    allowClear: true
                                });

                                // Set selected values for both dropdowns
                                $('#tm_uid').val(selectedTeamMembers).trigger('change');
                                $('#tm_hid').val(selectedTeamMembers).trigger('change');
                            }


                       // }


                    }


                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        $('.actions a[href="#next"]').parent('li').addClass('disabled');
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                    }
                }
            });
        }
        //////////////////////////////Record////////////////////////////////////////////////
        function validateRadioButtons() {

            $("input[type='radio'][name$='-radio']").each(function() {
                let groupName = $(this).attr("name");
                $(`input[name="${groupName}"]`).rules("add", {
                    radioRequired: true
                });
            });
        }

        function getlanguagelc() {
            // Example: Retrieve from localStorage or set to 'en' if not available
            return window.localStorage.getItem('lang') || 'en';
        }
        loadJsonData(); // Ensure this function loads the necessary data correctly

        // Custom validation method for special characters
        $.validator.addMethod("noSpecialChars", function(value, element) {
            return this.optional(element) || /^[^{}\[\]:;",|~`'"#^]+$/.test(value);
    }, function() {
        // Get the current language and return the localized error message
        const language = getlanguagelc(); // Fetch current language (like 'en' or 'ta')
        return errorMessages[language]['specialChars']; // Return the localized message for special characters
    });

    // Custom validation for radio button selection
    $.validator.addMethod("radioRequired", function(value, element) {
        let radioName = $(element).attr("name");

        // Check if any radio button is selected
        return $(`input[name="${radioName}"]:checked`).length > 0;
    }, function() {
        // Get the current language and return the localized message for radio selection
        const language = getlanguagelc();
        return errorMessages[language]['yesorno']; // Return the localized message for Yes/No selection
    });

    // Custom validation method to check if file is required when 'Y' is selected
    $.validator.addMethod("fileRequiredIfYes", function(value, element) {
        let accountId = $(element).attr("id").split("-")[0]; // Extracts the ID prefix
        let selectedRadio = $(`input[name="${accountId}-radio"]:checked`).val();
        return selectedRadio === "Y" ? value !== "" : true; // File is required if 'Y' is selected
    }, function() {
        // Get the current language and return the localized error message
        const language = getlanguagelc();
        return errorMessages[language]['fileRequiredIfYes']; // Return localized message for file required
    });

    // Custom validation for file size limit (3MB)
    $.validator.addMethod("fileSizeLimit", function(value, element) {
        if (element.files.length > 0) {
            return element.files[0].size <= 1 * 1024 * 1024; // 1MB limit
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for file size
        const language = getlanguagelc();
        return errorMessages[language]['fileSizeLimit']; // Return localized message for file size limit
    });

    // Custom validation for valid file types (PNG, JPEG, PDF, Excel)
    $.validator.addMethod("validFileType", function(value, element) {
        if (value) {
            let allowedTypes = ["pdf", "png", "jpeg", "jpg", "xls", "xlsx"];
            let fileExtension = value.split(".").pop().toLowerCase();
            return allowedTypes.includes(fileExtension);
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for valid file type
        const language = getlanguagelc();
        return errorMessages[language]['validFileType']; // Return localized message for file type validation
    });
    $(document).on('click', '#buttonaccept', function(event) {

        event.preventDefault(); // Prevent form submission
        restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");
        validateRadioButtons();
        if ($callforrecordsForm.valid()) {
            getLabels_jsonlayout([{
                id: 'confirmation_submit',
                key: 'confirmation_submit'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', Object.values(
                        text)[0], 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            $("#process_button").addClass("button_confirmation");
            $('#process_button').removeAttr('data-bs-dismiss');
            // $('.button_confirmation').data('auditplanid', auditplanid);
        }
        //  else {
        //     alert("Please fill out all required fields correctly before submitting.");
        // }

    });
    $(document).on('click', '.button_confirmation', function() {
        $('#buttonaccept').prop('disabled', true);
        $("#process_button").prop("disabled", true);

        // if ($callforrecordsForm.valid()) {
        $('#callforrecords').append(
            `<input type="hidden" name="auditscheduleid" value="${$('#audit_scheduleid').val()}">`);

        // Create the FormData object
        var formData = new FormData($('#callforrecords')[0]);

        $.ajax({
            url: 'audit/auditee_accept', // Replace with your endpoint
            method: 'POST',
            data: formData,
            processData: false, // Disable automatic data processing
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {
                $("#process_button").prop("disabled", false);

                // var validator = $("#audit_schedule").validate();
                // validator.resetForm();
                if (response.success) {
                    recordStatus = 'Y';
                    if (response.message == 'movetonexttab') {

                        getLabels_jsonlayout([{
                            id: 'add_audituserdetails',
                            key: 'add_audituserdetails'
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        var auditscheduleid = $('#auditscheduleid').val();

                        acceptstatus(auditscheduleid)
                        $(".validation-wizard").steps("next");
                        // var movetonexttab = ofcUserDetailsTab('intimationform');
                        // //$('#details-tab').addClass('disabled');

                    } else {


                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        $('#auditeereponseget').val('A');
                        ofcUserDetailsTab();
                        // $('#details-tab').removeClass('disabled');
                        // $('#part_details').hide();

                        // const detailsTab = document.getElementById('details-tab');
                        // const detailsContent = document.getElementById('details-section');

                        // Disable the "Details" tab
                        /*detailsTab.classList.add('disabled');
                        detailsTab.removeAttribute('href');
                        detailsTab.removeAttribute('data-bs-toggle');*/

                        // Hide the content of the "Details" tab
                        // if (detailsContent) {

                        //     detailsContent.style.display = 'none';
                        // }
                        // $('.date_change').hide();
                        // $('.nav-link').removeClass('active');

                        // // Add 'active' class to #all-category
                        // $('#all-category').addClass('active');
                        // $('.all-category').show();
                        $('#buttonsforacceptance').hide();
                        $('#statusmessage').show();


                    }


                }



            },
            complete: function() {
                $('#buttonaccept').prop('disabled', false);
                $('#process_button').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $("#process_button").prop("disabled", false);

                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });

    });


    $("#callforrecords").validate({
        rules: {
            "textarea[name$='-cfrvalues'], textarea[name$='-accountvalues']": {
                required: true,
                minlength: 10,
                noSpecialChars: true,
            },

            "input[type='radio'][name$='-radio']": {
                radioRequired: true,
            },
            // Validate file upload only if 'Y' is selected
            "input[type='file'][name$='-accountfile']": {
                fileRequiredIfYes: true,
                fileSizeLimit: true, // File size limit
                validFileType: true, // File type validation
            },
            nodalname: {
                required: true,
                noSpecialChars: true,

            },
            nodalmobile: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10,
                noSpecialChars: true,
            },
            nodalemail: {
                required: true,
                email: true,
                noSpecialChars: true,
            },
            nodaldesignation: {
                required: true,
                noSpecialChars: true,
            },
            auditee_remarks: {
                required: true,
                minlength: 10,
                noSpecialChars: true,
            },
        },
        messages: {
            "input[type='radio'][name$='-radio']": {
                radioRequired: "Please select Yes or No",
            },
            "input[type='file'][name$='-accountfile']": {
                fileRequiredIfYes: "File is required ",
                fileSizeLimit: "File size must be less than 1MB",
                validFileType: "Allowed file types: PNG, JPEG, PDF, Excel",
            },

            "textarea[name$='-cfrvalues'], textarea[name$='-accountvalues']": {
                required: "Remarks are required",
                minlength: "Remarks must be at least 10 characters long",
                noSpecialChars: "Special characters are not allowed",
            },
            nodalname: {
                required: "Name is required",
                noSpecialChars: "Enter the valid Remarks"

            },
            nodalmobile: {
                required: "Mobile number is required",
                digits: "Enter a valid mobile number",
                minlength: "Must be 10 digits",
                maxlength: "Must be 10 digits",
                noSpecialChars: "Enter the valid Remarks"
            },
            nodalemail: {
                required: "Email is required",
                email: "Enter a valid email address",
                noSpecialChars: "Enter the valid Remarks"
            },
            nodaldesignation: {
                required: "Designation is required",
                noSpecialChars: "Enter the valid Remarks"
            },
            auditee_remarks: {
                required: "Remarks are required",
                minlength: "Remarks must be at least 10 characters long",
                noSpecialChars: "Enter the valid Remarks"
            },
        },
        errorPlacement: function(error, element) {
            // For datepicker fields inside input-group, place error below the input group
            if (element.hasClass('datepicker')) {
                // Insert the error message after the input-group, so it appears below the input and icon
                error.insertAfter(element.closest('.input-group'));
            } else if (element.attr("type") === "radio") {
                // Insert error message after the entire .col-md-12 container of radio buttons
                error.insertAfter(element.closest('.col-md-12'));
            } else {
                // For other elements, insert the error after the element itself
                error.insertAfter(element);
            }

        },
        invalidHandler: function(event, validator) {
            scrollToFirstError();
        }
    });



    const $callforrecordsForm = $("#callforrecords");

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $callforrecordsForm.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    // Event listener for file validation when 'Yes' is selected

    $(document).on("change", "input[type='radio'][name$='-radio']", function() {
        let accountId = $(this).attr("name").split("-")[0];
        $(`#${accountId}-attachment input[type="file"]`).valid();
    });


    function toggleAttachment(accountId, isRequired) {
        let fileInputContainer = $(`#${accountId}-attachment`);

        let fileInput = $(`#${accountId}-attachment input[type="file"]`);
        if (isRequired) {
            fileInputContainer.show();

            fileInput.rules("add", {
                // required: true,
                fileRequiredIfYes: true,
                fileSizeLimit: true,
                validFileType: true,
                messages: {
                    // required: "File is required ",
                    fileSizeLimit: "File size must be less than 1MB",
                    validFileType: "Allowed file types: PNG, JPEG, PDF, Excel",
                }
            });
        } else {
            fileInputContainer.hide();
            fileInput.val("");

            fileInput.rules("remove", "required fileRequiredIfYes fileSizeLimit validFileType");
        }
        fileInput.valid();
    }

    function preventPasteSpecialChars(event) {
        setTimeout(() => {
            let inputVal = $(this).val();
            let sanitizedVal = inputVal.replace(/["'|\#^`~]/g, ""); // Remove special characters
                if (inputVal !== sanitizedVal) {
                    alert("Pasting special characters is not allowed!");
                    $(this).val(sanitizedVal); // Set the sanitized value back
                }
            }, 100); // Timeout to ensure paste operation is complete
        }

        function restrictSpecialChars(selector) {

            $(selector)
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    if (/["'|\#^`~]/.test(char)) {
                    event.preventDefault(); // Block special characters on keypress
                }
            })
            .on("paste", preventPasteSpecialChars); // Block special characters on paste
    }

    // Apply restrictions to specific input fields
    restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");


    function acceptstatus(auditscheduleid) {

        $.ajax({
            url: 'audit/auditee_acceptdetails', // The route to call your controller method
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid // Passing the auditplanid from the button's id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                populateTableFetch(response)

            },
            error: function(xhr, status, error) {
                // Handle error
                console.log("AJAX error: " + error);
            }
        });
    }

    function fetch_audit_particulars_detail() {

        var catcode = $('#h_catcode').val();
        var deptcode = $('#h_deptcode').val();
        var scheduleid = $('#auditscheduleid').val();

        $.ajax({
            url: 'audit/audit_particulars', // Replace with your endpoint
            method: 'GET',
            data: {
                catcode: catcode, // Pass the 'catcode' to the controller
                deptcode: deptcode, // Pass the 'catcode' to the controller
                scheduleid: scheduleid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {
                if ((response.data && response.data.length > 0) &&
                    (response.account_particulars && response.account_particulars.length > 0)) {

                    populateTable(response);
                    //  $(".validation-wizard").steps("next");

                } else {
                    passing_alert_value('Confirmation', 'No Data Found',
                        'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                    // $(".validation-wizard").steps("previous");

                }

            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });
    }

    function populateTable(response) {
        $('#part_details').show();
        const tableBody = $('#part_details'); // Select the table's tbody
        tableBody.empty(); // Clear existing rows


        const accountParticulars = response.account_particulars.reduce((acc, item) => {
            if (!acc[item.accountparticularsename]) {
                acc[item.accountparticularsename] = [];
            }
            acc[item.accountparticularsename].push(item);
            return acc;
        }, {});

        // Calculate the total number of rows in the table
        //const totalRows = Object.values(groupedData).reduce((sum, group) => sum + group.length, 0);
        const accountTotalRows = Object.values(accountParticulars).reduce((sum, group) => sum + group.length, 0);

        // Start the table with "Call for Records" and "Account Particulars" as row headers
        let tableHTML = `<tr>
                                                                                                                <th rowspan="${accountTotalRows + 2}" class="lang availaccount" key="account_particulars_label">Availability of  Account Particulars</th>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <th class="callforrecords_th lang" key="type">Type</th>
                                                                                                                <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                                                                                                                <th class="callforrecords_th lang">
                                                                                                                    <div>
                                                                                                                        <label class="form-label required lang" key="file_upload" for="validationDefault01">File Upload&nbsp;&nbsp;<Label>
                                                                                                                        <span style="color:red;font-weight:300;">(&nbsp;&nbsp;File size must not exceed 1 MB&nbsp;&nbsp;)</span>
                                                                                                                    </div>
                                                                                                                </th>
                                                                                                                <th class="callforrecords_th">
                                                                                                                    <div>
                                                                                                                        <label class="form-label required lang" key="remarks" >Remarks</label>
                                                                                                                    </div>
                                                                                                                </th>
                                                                                                            </tr>`;
        var lang = getLanguage('Y');


        // Iterate over account particulars and create rows
        for (const [accountParticularsName, accountParts] of Object.entries(accountParticulars)) {
            const accountRowSpan = accountParts.length; // Number of subcategories under the account category

            accountParts.forEach((accountParticular, index) => {

                if (lang == 'ta') {
                    var accountparticularsname = accountParticular.accountparticularstname;
                } else {
                    var accountparticularsname = accountParticular.accountparticularsename;
                }
                tableHTML +=
                    `
                                                                                                                            <tr>

                                                                                                                            <td class="lang">${accountparticularsname}
                                                                                                                            <input type="hidden" id="${accountParticular.accountparticularsid}-accountcode" name="${accountParticular.accountparticularsid}-accountcode" value="${accountParticular.accountparticularsid}"></td> <!-- Account Item -->
                                                                                                                            <td>
                                                                                                                            <div class="col-md-12">
                                                                                                                                <div class="form-check form-check-inline">
                                                                                                                                    <input class="form-check-input" type="radio" name="${accountParticular.accountparticularsid}-radio"
                                                                                                                                        id="${accountParticular.accountparticularsid}-radio" value="Y"
                                                                                                                                        onclick="toggleAttachment('${accountParticular.accountparticularsid}', true)" />
                                                                                                                                    <label class="form-check-label lang"  key="avail_label" for="account-${accountParticular.accountparticularsid}-yes">Available</label>
                                                                                                                                </div>
                                                                                                                                <div class="form-check form-check-inline">
                                                                                                                                    <input class="form-check-input" type="radio" name="${accountParticular.accountparticularsid}-radio"
                                                                                                                                        id="${accountParticular.accountparticularsid}-radio" value="N"
                                                                                                                                        onclick="toggleAttachment('${accountParticular.accountparticularsid}', false)" />
                                                                                                                                    <label class="form-check-label lang"  key="notavail_label" for="${accountParticular.accountparticularsid}-no">Not Available</label>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            </td>
                                                                                                                                <td>
                                                                                                                                    <div id="${accountParticular.accountparticularsid}-attachment" name="${accountParticular.accountparticularsid}-file " style="padding:10px;">

                                                                                                                                        <input type="file" data-placeholder-key="remarks_ph" class="form-control"
                                                                                                                                            id="${accountParticular.accountparticularsid}-attachment" name="${accountParticular.accountparticularsid}-accountfile">

                                                                                                                                    </div>

                                                                                                                                </td>

                                                                                                                            <td style="padding:10px;">
                                                                                                                            <textarea id="account-${accountParticular.accountparticularsid}" data-placeholder-key="remarks_ph" name="${accountParticular.accountparticularsid}-accountvalues" class="form-control" placeholder="Enter remarks" style="height: 20px;"></textarea>
                                                                                                                            </td>
                                                                                                                            </tr>

                                                                                                                            `;
            });
        }

        // Call for Records Section
        tableHTML += `<tr style="height:30px;"></tr><tr>
                                                                                                             <th rowspan="${response.data.length+1}" class="lang" key="callforrecords_label">Call For Records</th>

                                                                                                             <th colspan="2" class="callforrecords_th lang" key="type">Type</th>
                                                                                                             <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                                                                                                             <th class="callforrecords_th lang" key="remarks">Remarks</th>
                                                                                                          </tr>`;

        $.each(response.data, function(index, record) {
            // Determine the value to display based on the language
            var callForRecordsName = (lang === 'en') ?
                record.callforrecordsename :
                record.callforrecordstname;

            tableHTML += `
                                                                                                                    <tr>
                                                                                                                        <td colspan="2" >${callForRecordsName}</td>
                                                                                                                        <td>
                                                                                                                            <input type="hidden" id="${record.callforrecordsid}-cfrcode" name="${record.callforrecordsid}-cfrcode" value="${record.callforrecordsid}">

                                                                                                                            <div class="col-md-12">
                                                                                                                                <div class="form-check form-check-inline">
                                                                                                                                    <input  checked class="form-check-input" type="radio" name="${record.callforrecordsid}-cfrradio"
                                                                                                                                        id="${record.callforrecordsid}-cfrradio" value="Y"
                                                                                                                                    />
                                                                                                                                    <label class="form-check-label lang"  key="avail_label" for="${record.callforrecordsid}-cfrradio">Available</label>
                                                                                                                                </div>
                                                                                                                                <div class="form-check form-check-inline">
                                                                                                                                    <input class="form-check-input" type="radio" name="${record.callforrecordsid}-cfrradio"
                                                                                                                                        id="${record.callforrecordsid}-cfrradio" value="N"
                                                                                                                                    />
                                                                                                                                    <label class="form-check-label lang"  key="notavail_label" for="${record.callforrecordsid}-radio-no">Not Available</label>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding:10px;">
                                                                                                                            <textarea id="${record.callforrecordsid}" data-placeholder-key="remarks_ph"
                                                                                                                                name="${record.callforrecordsid}-cfrvalues"
                                                                                                                                class="form-control"
                                                                                                                                placeholder="Enter remarks"
                                                                                                                                style="height: 20px;"></textarea>
                                                                                                                        </td>
                                                                                                                    </tr>`;
        });




        // Add Nodal Person and Remarks
        tableHTML += `
                                                                                            <tr style="height:30px;"></tr><tr>
                                                                                            <th class="lang" key="nodal_person">Nodal Person</th>
                                                                                            <td colspan="4">
                                                                                             <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                   <label class="form-label required lang"  key="name" for="nodal_name">Name</label>
                                                                                                   <input type="text" class="form-control name"  maxlength='50' id="nodalname" name="nodalname"  data-placeholder-key="username"  placeholder="Enter Name" value="${audit_schedule[0]?.nodalperson_ename ? audit_schedule[0].nodalperson_ename : ""}" />
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                   <label class="form-label required lang"  key="mobile" for="mobile">Mobile Number</label>
                                                                                                   <input type="text" class= "form-control only_numbers"  maxlength='10' data-placeholder-key="mobile"   id="nodalmobile" name="nodalmobile" value="${audit_schedule[0]?.mobile  ? audit_schedule[0].mobile : ""}"  placeholder="Enter Mobile Number" maxlength = 10  />
                                                                                                </div>
                                                                                            </div><br>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    <label class="form-label required lang"  key="Email" for="mobile">Email</label>
                                                                                                    <input type="text" class="form-control"  maxlength='50' id="nodalemail"  data-placeholder-key="email"  name="nodalemail" placeholder="Enter Email" value="${audit_schedule[0]?.email  ? audit_schedule[0].email : ""}"  />
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <label class="form-label required lang"  key="designation" for="mobile">Designation</label>
                                                                                                    <input type="text" class="form-control alpha_numeric"  maxlength='50' id="nodaldesignation" data-placeholder-key="designation_ph"  name="nodaldesignation"value="${audit_schedule[0]?.nodalperson_desigcode ? audit_schedule[0].nodalperson_desigcode : ""}"   placeholder="Enter Designation"  />
                                                                                                </div>
                                                                                            </div>
                                                                                            <br>
                                                                                            </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                            <th class="lang" key="remarks">Remarks</th>
                                                                                            <td colspan="4">
                                                                                            <div class="col-md-12">
                                                                                                <label class="form-label required lang" key="remarks" for="remarks">Remarks</label>
                                                                                                <textarea id="auditee_remarks"  maxlength='200' data-placeholder-key="remarks_ph" name="auditee_remarks"  class="form-control" placeholder="Enter remarks" style="height: 20px;"></textarea>
                                                                                                <div id="error-message" style="font-size: 12px; color: red; display: none;">Word limit reached! Maximum 250 words allowed.</div>
                                                                                            </div><br>
                                                                                            </td>
                                                                                            </tr>
                                                                                            `;

        // Append the generated table HTML to the table body
        tableBody.html(tableHTML);
        translate();
        updatePlaceholders(lang);

        $(document).on("keypress", ".only_numbers", function(event) {
            if (event.charCode >= 48 && event.charCode <= 57)
                return true;
            else return false;
        });

        $(".name").on("keypress", function(event) {
            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                event.charCode == 32
            )
                return true;
            else return false;
        });

        // Allow Alphabets and Numbers
        $(".alpha_numeric").on("keypress", function(event) {
            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                (event.charCode >= 48 && event.charCode <= 57) ||
                event.charCode == 32
            )
                return true; // let it happen, don't do anything
            else return false;
        });
        restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");



    }

    function populateTableFetch(response) {
        $('#part_details').hide();

        const tableBody = $('#part_details_fetch'); // Select the table's tbody
        tableBody.empty(); // Clear existing rows

        const data = response.data;
        const cfr = response.cfr;


        // Grouping data for Account Particulars
        const accountParticulars = data.reduce((acc, item) => {
            if (!acc[item.accountparticularsename]) {
                acc[item.accountparticularsename] = [];
            }
            acc[item.accountparticularsename].push(item);
            return acc;
        }, {});

        // Grouping data for Call for Records
        /*const callForRecords = cfr.reduce((acc, item) => {
            if (!acc[item.majorworkallocationtypeename]) {
                acc[item.majorworkallocationtypeename] = [];
            }
            acc[item.majorworkallocationtypeename].push(item);
            return acc;
        }, {});
        const totalRows = Object.values(callForRecords).reduce((sum, group) => sum + group.length, 0);*/
        const accountTotalRows = Object.values(accountParticulars).reduce((sum, group) => sum + group.length, 0);
        // Start building the table HTML
        let tableHTML = '';
        tableHTML += `<tr>
                                                                                                                        <th rowspan="${accountTotalRows + 2}" class="lang" key="account_particulars_label">Account Particulars</th>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                        <th class="callforrecords_th lang" key="type">Type</th>
                                                                                                                        <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                                                                                                                        <th class="callforrecords_th">
                                                                                                                            <div>
                                                                                                                                <label class="form-label lang" for="validationDefault01" key="file_upload">File Upload&nbsp;&nbsp;<Label>
                                                                                                                            </div>
                                                                                                                        </th>
                                                                                                                        <th class="callforrecords_th">
                                                                                                                            <div>
                                                                                                                                <label class="form-label lang" key="remarks">Remarks</label>
                                                                                                                            </div>
                                                                                                                        </th>
                                                                                                                    </tr>`;

        // Account Particulars Section
        tableHTML += `
                                                                                                            `;

        //var lang=$('#translate').val();
        var lang = getLanguage('Y');

        for (const [particularName, particulars] of Object.entries(accountParticulars)) {
            particulars.forEach((particular) => {
                const isFileUploaded = particular.fileuploadid !== 0;
                const fileDetailsString = particular.filedetails;
                const fileDetailsArray = fileDetailsString.split(
                    ',');

                const fileCardsHTML = fileDetailsArray.map((fileDetail, index) => {
                    const [name, path, size, fileuploadid] = fileDetail.split('-'); // Split by hyphen

                    const file = {
                        id: index + 1, // Use index+1 as unique ID for the file
                        name: name,
                        path: path,
                        size: size,
                        fileuploadid: fileuploadid,
                    };
                    const encodedPath = encodeURIComponent(file.path);
                    const fileUrl = `/download-file?filepath=${encodedPath}`;

                    const extension = file.name.split('.').pop().toLowerCase();

                    return isFileUploaded ?
                        `<div class="card overflow-hidden" id="file-card-${file.id}">
                                                                                                                <input type="hidden" id="fileuploadid_${file.id}" name="fileuploadid_${file.id}" value="${file.fileuploadid}">
                                                                                                                <div class="d-flex flex-row">
                                                                                                                    <div class="align-items-center">
                                                                                                                        <h3 class="text-danger box mb-0 round-56 p-2">
                                                                                                                            <i class="ti ti-file-text"></i>
                                                                                                                        </h3>
                                                                                                                    </div>
                                                                                                                    <div class="p-3">
                                                                                                                        <h3 class="text-dark mb-0 fs-3">
                                                                                                                            <a style="color:black" href="javascript:void(0);"  onclick="downloadAndPreview('${fileUrl}')">${file.name}</a>
                                                                                                                        </h3>

                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>` :
                        `<div class=""></div>`;
                }).join('');

                if (lang == 'ta') {
                    var accountparticularsname = particular.accountparticularstname;
                } else {
                    var accountparticularsname = particular.accountparticularsename;
                }
                tableHTML += `
                                                                                                        <tr>
                                                                                                            <td>${accountparticularsname}
                                                                                                                <input type="hidden" id="${particular.accountparticularsid}-cfrcode" name="${particular.accountparticularsid}-cfrcode" value="${particular.accountparticularsid}">
                                                                                                            </td>
                                                                                                            <td>${isFileUploaded ? '<span class="lang" key="avail_label"></span>' : '<span class="lang" key="notavail_label"></span>'}</td>


                                                                                                              <td>${fileCardsHTML}</td>
                                                                                                            <td>
                                                                                                                <textarea id="${particular.accountparticularsid}" name="${particular.accountparticularsid}-cfrvalues" class="form-control" data-placeholder-key="remarks_ph" placeholder="Enter remarks" disabled style="height: 20px;">${particular.remarks || ''}</textarea>
                                                                                                            </td>
                                                                                                        </tr>`;
            });
        }


        // Call for Records Section
        tableHTML +=
            `<tr style="height:30px;"></tr><tr>
                                                                                                                         <th rowspan="${cfr.length+ 2}" class="lang" key="callforrecords_label">Call For Records</th>
                                                                                                                      </tr>
                                                                                                                      <tr>
                                                                                                                         <th colspan="2" class="callforrecords_th lang" key="type">Type</th>
                                                                                                                         <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records </th>
                                                                                                                         <th class="callforrecords_th lang" key="remarks">Remarks</th>
                                                                                                                      </tr>`;

        $.each(cfr, function(index, record) {
            // Determine the value to display based on the language
            var callForRecordsName = (lang === 'en') ?
                record.callforrecordsename :
                record.callforrecordstname;

            const isReplyPending = record.replystatus !== 'Y';

            tableHTML += `
                                                                                                                    <tr>
                                                                                                                        <td colspan="2" >${callForRecordsName}</td>
                                                                                                                        <td>
                                                                                                                           ${isReplyPending ? '<span class="lang" key="notavail_label"></span>' : '<span class="lang" key="avail_label"></span>'}
                                                                                                                        </td>
                                                                                                                        <td style="padding:10px;">
                                                                                                                            <textarea id="${record.callforrecordsid}" name="${record.callforrecordsid}-cfrvalues" class="form-control" data-placeholder-key="remarks_ph" placeholder="Enter remarks" disabled style="height: 20px;">${record.cfr_remarks || ''}</textarea>

                                                                                                                        </td>
                                                                                                                    </tr>`;
        });



        // Nodal Person Section
        tableHTML += `
                                                                                                    <tr style="height:30px;"></tr><tr>
                                                                                                    <th class="lang" key="nodal_person">Nodal Person</th>
                                                                                                    <td colspan="4">
                                                                                                        <div class="row">
                                                                                                            <div class="col-md-6">
                                                                                                               <label class="form-label lang" key="name" for="nodal_name">Name</label>
                                                                                                               <input type="text" class="form-control " id="nodalname" data-placeholder-key="username" name="nodalname" value="${data[0].nodalname || ''}" disabled placeholder="Enter Name"  />
                                                                                                            </div>
                                                                                                            <div class="col-md-6">
                                                                                                               <label class="form-label lang" key="mobile" for="mobile">Mobile Number</label>
                                                                                                               <input type="text" class= "form-control only_numbers" data-placeholder-key="mobile" id="nodalmobile" value="${data[0].nodalmobile || ''}" disabled name="nodalmobile" placeholder="Enter Mobile Number" maxlength = 10 />
                                                                                                            </div>
                                                                                                        </div><br>
                                                                                                        <div class="row">
                                                                                                            <div class="col-md-6">
                                                                                                                <label class="form-label lang" key="Email"  for="mobile">Email</label>
                                                                                                                <input type="text" class="form-control" id="nodalemail"  data-placeholder-key="email" value="${data[0].nodalemail || ''}" disabled name="nodalemail" placeholder="Enter Email"  />
                                                                                                            </div>
                                                                                                            <div class="col-md-6">
                                                                                                                <label class="form-label lang" key="designation"  for="mobile">Designation</label>
                                                                                                                <input type="text" class="form-control" id="nodaldesignation" data-placeholder-key="designation_ph" value="${data[0].nodaldesignation || ''}" disabled  name="nodaldesignation" placeholder="Enter Designation"  />
                                                                                                            </div>
                                                                                                        </div><br>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <th class="lang" key="remarks">Remarks</th>
                                                                                                    <td colspan="4">
                                                                                                        <label class="form-label lang" key="remarks" for="auditee_remarks">Remarks</label>
                                                                                                        <textarea id="auditee_remarks" name="auditee_remarks" data-placeholder-key="remarks_ph" class="form-control" disabled style="height: 20px;">${data[0].auditeeremarks || ''}</textarea><br>
                                                                                                    </td>
                                                                                                </tr>`;

        // Append the HTML to the table body
        tableBody.append(tableHTML);
        translate();
        $('#details_tabletab_buttons').hide();
        updatePlaceholders(lang);

    }

    $(document).on('change', '#translate', function() {
        var lang = getLanguage('Y');
        updateValidationMessages(getLanguage('Y'), 'callforrecords');







        fetchalldata(lang);


    });

    $(document).ready(function() {
        var lang = getLanguage('')
        fetchalldata(lang);




    });

    function fetchCpsData() {
        // console.log('hi')
        // alert(cpsSetCounter);
        var auditscheduleid = $('#auditscheduleid_cps').val();
        var instid = $('#instid_cps').val();

        // console.log('scg: ' + auditscheduleid)


        $.ajax({
            url: "/cps/fetch",
            type: "post",
            data: {
                instid: instid,
                auditscheduleid: auditscheduleid,
                _token: $('meta[name="csrf-token"]').attr('content')

            },
            success: function(response) {

                resetCpsAccordions();

                if (!response || response.length === 0) {
                    $('#btnSaveDraft').removeClass('d-none');
                    $('#btnUpdateCps').addClass('d-none');
                    return;
                }

                // response has data
                $('#btnSaveDraft').addClass('d-none');
                $('#btnUpdateCps').removeClass('d-none');

                response.forEach((row, index) => {
                    const setNo = index + 1;
                    if (index === 0) {
                        // alert(index);

                        fillCpsSet(1, row);
                    } else {

                        addCpsSet('Y');
                        //  alert(cpsSetCounter);

                        fillCpsSet(setNo, row);
                    }
                });

            }
        });
    }


    $(document).on('input change', '#cpsModal input, #cpsModal textarea, #cpsModal select', function() {
        $('#cpsValidationAlert').addClass('d-none');
    });


    document.addEventListener('show.bs.modal', function(event) {
        const modal = event.target;

        // if confirmation alert is opening AND any modal is open
        if (modal.classList.contains('popupalertforcps') && document.body.classList.contains('modal-open')) {
            const cpsModal = document.querySelector('.cpsModal'); // select by class
            if (cpsModal) {
                cpsModal.appendChild(modal);
            }
        }
    });

    function resetAndCloseCpsModal() {

        // Reset AFTER modal is hidden
        modalEl.addEventListener('hidden.bs.modal', function() {

            // Remove extra CPS sets
            $('#cpsSetsContainer .accordion-item').not(':first').remove();
            $('#cpsValidationAlert').addClass('d-none');

            const $firstSet = $('#cpsSetsContainer .cps-set:first');
            $('#ddocode_cps').val('').removeClass('is-invalid');
            $('#treasury_cps').val('').removeClass('is-invalid');

            // Always hide and reset shortfall UI so stale unsaved rows do not reappear on reopen
            $firstSet.find('div[id^="shortfall_wrap"]').each(function() {
                const $wrap = $(this);
                const $tbody = $wrap.find('tbody');
                const $rows = $tbody.find('tr');

                if ($rows.length > 1) {
                    $rows.not(':first').remove();
                }

                const $firstRow = $tbody.find('tr:first');
                $firstRow.find('input').val('').removeClass('is-invalid');
                $firstRow.find('.removeshortfall-row').addClass('d-none');
                $firstRow.find('.sno').text('1');

                $wrap.find('tfoot input.shortfall_total').val('');
                $wrap
                    .addClass('d-none')
                    .find('input, textarea, select')
                    .removeAttr('data-required')
                    .removeClass('is-invalid');
            });

            // Reset all inputs
            $firstSet.find('input, select, textarea').each(function() {
                const $el = $(this);

                if ($el.is(':radio')) {
                    const name = $el.attr('name');
                    $(`input[name="${name}"]`).prop('checked', false);
                    $(`input[name="${name}"]:first`).prop('checked', true);
                } else {
                    $el.val('');
                }

                $el.removeClass('is-invalid');

                if ($el.is('textarea')) {
                    $el.addClass('d-none');
                }
            });

            $firstSet.find('.sNo').val('1');

            cpsCounter = 1;
            fieldCounter = 1;
            cpsSetCounter = 1;

            $('#rowLimitMsg').addClass('d-none');

            restrictSpecialChars('.nbew_text');
            restrictSpecialChars('.nbew_number');
            restrictSpecialChars('.nbew_number_decimal');
            restrictSpecialChars('.nbew_specialchartextandnumber');

            fetchCpsData();

        }, {
            once: true
        });

        modalInstance.hide();

    }

    $('#closeCpsModal').on('click', resetAndCloseCpsModal);



    function saveCps(callback) {
        if (!validateCpsTable()) {
            $('#cpsValidationAlert').removeClass('d-none');
            document.querySelector('#cpsModal').scrollTop = 0;
            if (callback) callback(false);
            return;
        }


        var auditscheduleid = $('#auditscheduleid_cps').val();
        var instid = $('#instid_cps').val();



        const cpsModalEl = document.getElementById('cpsModal');

        var form = document.getElementById('cpsForm');
        var formData = new FormData(form);

        // Collect employee data
        var employees = [];
        $('#cpsSetsContainer .cps-set').each(function(index, el) {
            var $el = $(el);
            var employeeData = {
                transcpsid: $el.find('[name^="transcpsid"]').val(),

                // department_name: $el.find('[name^="department_name"]').val(),
                treasury_cps: $el.find('[name^="treasury_cps"]').val(),
                ddocode_cps: $el.find('[name^="ddocode_cps"]').val(),
                cpsNo: $el.find('[name^="cpsNo"]').val(),

                // cps_year: $el.find('[name^="cps_year"]').val(),
                cps_month_employer: $el.find('[name^="cps_month_employer"]').val(),
                cps_month_employee: $el.find('[name^="cps_month_employee"]').val(),

                name: $el.find('[name^="name"]').val(),
                dob: $el.find('[name^="dob_cps"]').val(),
                doj: $el.find('[name^="doj_cps"]').val(),
                designation: $el.find('[name^="designation"]').val(),
                payscale: $el.find('[name^="payscale"]').val(),
                q1: $el.find('[name^="q1"]:checked').val(),
                details1: $el.find('[name^="details1"]').val(),
                q2: $el.find('[name^="q2"]:checked').val(),
                details2: $el.find('[name^="details2"]').val(),
                score: $el.find('[name^="score"]:checked').val(),


                employee: parseFloat($el.find('[name^="employee_contri"]').val()) || 0,
                employer: parseFloat($el.find('[name^="employer_contri"]').val()) || 0,

                employeefinancialyear: $el.find('[name^="employeefinancialyear"]').val() || 0,
                employerfinancialyear: $el.find('[name^="employerfinancialyear"]').val() || 0,



                total: parseFloat($el.find('[name^="total_cps"]').val()) || 0,
                actual: parseFloat($el.find('[name^="actual_remittance"]').val()) || 0,
                credited: $el.find('[name^="creditedin_appropriatehead"]').val(),
                credited_remarks: $el.find('[name^="credited_remarks"]').val(),
                shortfall: parseFloat($el.find('[name^="shortfall_remittance"]').val()) || 0,
                excess: parseFloat($el.find('[name^="remittanceexcess_cps"]').val()) || 0,
                remittanceexcess_remarks: $el.find('[name^="remittanceexcess_remarks"]').val(),


            };

            var $table = $el.find('div[id^="shortfall_wrap"] table');
            var totalShortfall = parseFloat($table.find('tfoot input[name^="shortfall_total"]').val()) || 0;

            var shortfalls = [];

            $table.find('tbody tr').each(function() {
                var $row = $(this);

                var month = $row.find('[name^="shortfallmonth"]').val();
                var amount = $row.find('[name^="shortfallamount"]').val();

                if (!month || amount === '' || amount === null) return;

                if (isNaN(amount)) amount = 0;

                shortfalls.push({
                    shortfall_month: month, // text like "june-2025"
                    shortfall_amount: parseFloat(amount),
                    shortfallid: $row.find('[name^="shortfallid"]').val() || null,
                    shortfall_headofaccount: $row.find('[name^="shortfallfeadofaccount"]')
                        .val() || null,
                    shortfall_voucher: $row.find('[name^="shortfallvno"]').val() || null,
                    shortfall_treasury: $row.find('[name^="shortftreasury"]').val() || null,
                    shortfall_total: totalShortfall // add the total in each row

                });
            });

            employeeData.shortfalls = shortfalls;
            employees.push(employeeData);



        });

        var payload = {
            instid: instid,
            auditscheduleid: auditscheduleid,
            employees: employees
        };

        $.ajax({
            url: '/cpsinsert',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btnSaveDraft, #btnUpdateCps, #finalizeCpsBtn').prop('disabled', true);
            },
            success: function(response) {
                $('#btnSaveDraft, #btnUpdateCps, #finalizeCpsBtn').prop('disabled', false);
                if (callback) callback(true, response);
            },
            error: function(xhr, status, error) {
                //  cpsModal.hide();

                $('#btnSaveDraft').prop('disabled', false);
                $('#btnUpdateCps').prop('disabled', false);
                $('#finalizeCpsBtn').prop('disabled', false);

                let message = 'Something went wrong. Please try again.';

                if (xhr.status === 422 && xhr.responseJSON.errors) { // Validation error
                    let errors = xhr.responseJSON.errors;
                    let messages = [];

                    $.each(errors, function(field, msgs) {
                        messages = messages.concat(msgs);
                    });

                    message = messages.join('<br>'); // Use <br> for multiple errors
                }

                setTimeout(function() {
                    passing_alert_value(
                        'Error',
                        message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }, 350);

                $("#ok_button").off('click').on('click', function() {

                    // cpsModal.show();
                });
                console.error(xhr.responseText);
            }
        });
    }



    $(document).on('click', '.btnSaveDraft', function(e) {
        e.preventDefault();

        if (!validateCpsTable()) {

            const alertBox = document.getElementById('cpsValidationAlert');

            // show alert
            alertBox.classList.remove('d-none');

            // scroll modal body to top so user sees it
            const modalBody = document.querySelector('#cpsModal');
            modalBody.scrollTop = 0;

            return;
        }



        const cpsModalEl = document.getElementById('cpsModal');
        saveCps(function(success) {
            if (success) {
                //   resetCpsAccordions();

                // if (cpsModal) cpsModal.hide();

                setTimeout(function() {
                    passing_alert_value(
                        'Confirmation',
                        'CPS Form Submitted / Updated Successfully',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }, 350);


                fetchCpsData();

            } else {

                let message = 'Something went wrong. Please try again.';
                //  if (cpsModal) cpsModal.hide();
                if (arguments.length > 1) {
                    const xhr = arguments[1];

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let messages = [];

                        $.each(xhr.responseJSON.errors, function(field, msgs) {
                            messages = messages.concat(msgs);
                        });

                        message = messages.join('<br>');
                    }
                }

                setTimeout(function() {
                    passing_alert_value(
                        'Error',
                        message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }, 350);

                $("#ok_button").off('click').on('click', function() {

                    // cpsModal.show();
                });
            }
        });
    });


    $(document).on('click', '#finalizeCpsBtn', function() {

        if (!validateCpsTable()) {

            const alertBox = document.getElementById('cpsValidationAlert');

            // show alert
            alertBox.classList.remove('d-none');

            // scroll modal body to top so user sees it
            const modalBody = document.querySelector('#cpsModal');
            modalBody.scrollTop = 0;

            return;
        }

        $('#finalizeCpsBtn').prop('disabled', true);



        $('#confirmation_alert #close_button').hide();

        passing_alert_value(
            'Alert',
            'Are you sure you want to finalize the CPS details?',
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        // removeAllEventListeners(document.getElementById("process_button"));


        $("#cancel_button").off('click').on('click', function() {

            $('#finalizeCpsBtn').prop('disabled', false);

        });

        $("#process_button").off('click.finalizeCps').on('click.finalizeCps', function() {

            saveCps(function(success) {
                if (!success) return;

                var auditscheduleid = $('#auditscheduleid_cps').val();
                var instid = $('#instid_cps').val();
                $.ajax({
                    url: '/finalize_cps',
                    method: 'POST',
                    data: {
                        instid: instid,
                        auditscheduleid: auditscheduleid,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {

                            setTimeout(function() {

                                passing_alert_value(
                                    'Confirmation',
                                    'CPS details Finalized Successfully',
                                    'confirmation_alert',
                                    'alert_header',
                                    'alert_body',
                                    'confirmation_alert'
                                );

                            }, 350);


                            $(document).on('click', '#ok_button', function() {
                                location.reload();
                            });


                        } else {
                            alert('Failed to finalize CPS details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // alert("1")
                        console.error(xhr.responseText);
                        alert('An error occurred.');
                    }
                });
            });
        });
    });








    function applyRestrictions(container) {
        restrictSpecialChars.call(container, '.nbew_text');
        restrictSpecialChars.call(container, '.nbew_number');
        restrictSpecialChars.call(container, '.nbew_number_decimal');
        restrictSpecialChars.call(container, '.nbew_specialchartextandnumber');


    }



    $(document).on('input', '#cpsModal input[data-required], #cpsModal textarea[data-required]', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
        }
    });

    $(document).on('change', '#cpsModal select[data-required]', function() {
        if ($(this).val() && $(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
        }
    });


    $(document).on('blur change', '.datepicker', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
        }
    });
    let cpsSetCounter = 1;

    function addCpsSet(update) {

        // alert(cpsSetCounter);

        if (!validateCpsTable()) {
            const alertBox = document.getElementById('cpsValidationAlert');
            alertBox.classList.remove('d-none');
            const modalBody = document.querySelector('#cpsModal .modal-body');
            modalBody.scrollTop = 0;

            return;
        }

        const container = document.getElementById('cpsSetsContainer');
        const maxSets = 50;

        if (!update) { // only block when NOT updating
            const currentSets = container.querySelectorAll('.accordion-item').length;
            if (currentSets >= maxSets) {
                alert(`Maximum of ${maxSets} sets allowed.`);
                return;
            }
        }

        container.querySelectorAll('.accordion-collapse.show').forEach(el => {
            el.classList.remove('show');

            const btn = el.closest('.accordion-item')
                ?.querySelector('.accordion-button');
            if (btn) btn.classList.add('collapsed');
        });


        cpsSetCounter++;

        const firstSet = container.querySelector('.cps-set');

        // Destroy Select2 in original before cloning
        $(firstSet).find('select[name^="cps_month_employee"], select[name^="cps_month_employer"]').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        //  $('.select2forcps').select2('destroy');

        const newSetContent = firstSet.cloneNode(true);


        newSetContent.querySelectorAll('.shortfall-table tbody').forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                rows.forEach((row, idx) => {
                    if (idx > 0) row.remove();
                });
            }
            const firstRow = tbody.querySelector('tr');
            if (firstRow) {
                firstRow.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
                const removeBtn = firstRow.querySelector('.removeshortfall-row');
                if (removeBtn) removeBtn.classList.add('d-none');
                const sno = firstRow.querySelector('.sno');
                if (sno) sno.textContent = '1';
            }
        });

        newSetContent.querySelectorAll('input.shortfall_total').forEach(input => {
            input.value = '0.00';
        });

        newSetContent.querySelectorAll('input, textarea, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${cpsSetCounter}]`);
            }

            if (input.type === 'text' || input.type === 'number') {
                input.value = input.value.includes('Rs') ? '0.00' : '';
            }

            if (input.type === 'radio') {
                input.checked = input.value === 'Y';
            }

            if (input.tagName.toLowerCase() === 'textarea') {
                input.value = '';
                input.classList.add('d-none');
            }

            if (input.tagName.toLowerCase() === 'select') {
                input.selectedIndex = 0;
            }
        });

        newSetContent.querySelectorAll('input, textarea, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${cpsSetCounter}]`);
            }

            if (input.type === 'radio') {
                input.checked = input.value === 'Y';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }

            if (input.tagName === 'TEXTAREA') {
                input.classList.add('d-none');
            }
        });



        const shortfallWrap = newSetContent.querySelector(`[id^="shortfall_wrap"]`);
        if (shortfallWrap) {
            // Set unique ID
            shortfallWrap.id = `shortfall_wrap[${cpsSetCounter}]`;

            // Always hide it initially
            shortfallWrap.classList.add('d-none');

            // Update total span ID dynamically with counter
            const totalSpan = shortfallWrap.querySelector(`[id^="shortfall_total"]`);
            if (totalSpan) totalSpan.id = `shortfall_total[${cpsSetCounter}]`;
        }




        newSetContent.querySelectorAll('.transcpsid').forEach(input => {
            input.value = '';
        });

        const sNoInputs = newSetContent.querySelectorAll('.sNo');
        sNoInputs.forEach((input, index) => {
            input.value = index + 1;
        });



        const accordion = document.createElement('div');
        accordion.classList.add('accordion', 'mb-3');
        const headingId = `headingCps${cpsSetCounter}`;
        const collapseId = `collapseCps${cpsSetCounter}`;

        accordion.innerHTML = `
                                                                                                <div class="accordion-item mt-3" style="border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                                                                                    <h2 class="accordion-header bordered-accordion" id="${headingId}">
                                                                                                        <div class="d-flex align-items-center w-100 p-2 mt-2 mb-2">
                                                                                                            <span style="font-size: 1rem; white-space: nowrap; flex-grow: 1;" class="accordion-title">
                                                                                                                CPS Set ${cpsSetCounter}: Employee & Remittance Details
                                                                                                            </span>

                                                                                                            <button type="button" class="btn btn-success btn-sm addRow gap-1 fw-bold me-2">
                                                                                                                Add Employee
                                                                                                            </button>

                                                                                                            <button type="button" class="btn btn-danger btn-sm deleteRow fw-bold">
                                                                                                                Remove
                                                                                                            </button>

                                                                                                            <button class="accordion-button  ms-2 custom-accordion-btn"
                                                                                                                    type="button"
                                                                                                                    data-bs-toggle="collapse"
                                                                                                                    data-bs-target="#${collapseId}"
                                                                                                                    aria-expanded="false"
                                                                                                                    aria-controls="${collapseId}">
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </h2>
                                                                                                    <div id="${collapseId}" class="accordion-collapse collapse show" aria-labelledby="${headingId}">
                                                                                                        <div class="accordion-body"></div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `;

        accordion.querySelector('.accordion-body').appendChild(newSetContent);


        container.appendChild(accordion);

        // Initialize Select2 for newly added selects in this set only
        // $(accordion).find('.select2forcps').select2({
        //     placeholder: "Select Month",
        //     allowClear: true,
        //     closeOnSelect: false
        // });

        // Reinitialize Select2 for ALL selects safely
        $(newSetContent).find('select.select2forcps').each(function() {
            $(this).val(null); // remove any selected "", etc.
            $(this).find('option:selected').prop('selected', false);
        });
        $('select[name^="cps_month_employee"], select[name^="cps_month_employer"]')
            .not('.select2-hidden-accessible')
            .select2({
                placeholder: "Select Month",
                allowClear: true,
                closeOnSelect: false
            });


        applyRestrictions(accordion);

    }


    $(document).ready(function() {

        $('select[name^="cps_month_employee[1]"]').not('.select2-hidden-accessible').select2({
            placeholder: "Select Month",
        });


        $('select[name^="cps_month_employer[1]"]').not('.select2-hidden-accessible').select2({
            placeholder: "Select Month",

        });

    });



    // $(document).on('change', 'select[name^="cps_month_employee"]', function () {

    //     let selectedMonths = $(this).val();

    //     let nameAttr = $(this).attr('name');

    //     let match = nameAttr.match(/\[(\d+)\]/);

    //     if (!match) return;

    //     let setNumber = match[1];

    //     let employerSelect = $(`select[name="cps_month_employer[${setNumber}]"]`);

    //     employerSelect.val(selectedMonths).trigger('change');

    //     employerSelect.on('select2:opening', function (e) {
    //         e.preventDefault();

    //     });

    // });

    $(document).on('change', 'select[name^="cps_month_employee"]', function() {
        let selectedMonths = $(this).val() || [];

        let match = $(this).attr('name').match(/\[(\d+)\]/);
        if (!match) return;
        let setNumber = match[1];

        // ===== Employer Sync =====
        let employerSelect = $(`select[name="cps_month_employer[${setNumber}]"]`);
        employerSelect.val(selectedMonths).trigger('change');

        if (!employerSelect.data('readonly-attached')) {
            // Prevent opening dropdown
            employerSelect.on('select2:opening', function(e) {
                e.preventDefault();
            });

            // Disable removing selected items
            employerSelect.on('select2:unselecting', function(e) {
                e.preventDefault(); // stop removing
            });

            employerSelect.data('readonly-attached', true);
        }

        // ===== Shortfall Month Populate =====
        let shortfallSelect = $(`select[name="shortfallmonth[${setNumber}]"]`);
        shortfallSelect.empty().append('<option value="">Select Month</option>');

        $(this).find('option:selected').each(function() {
            const v = $(this).val();
            if (!v) return; // ignore the empty placeholder
            shortfallSelect.append(`<option value="${v}">${$(this).text()}</option>`);
        });

    });


    document.addEventListener('click', function(e) {
        if (e.target.closest('.addRow')) {
            addCpsSet();
        }

        if (e.target.closest('.deleteRow')) {
            const accordionItem = e.target.closest('.accordion-item');
            if (accordionItem) accordionItem.remove();

            // Renumber all remaining sets
            renumberCpsSets();

            const container = document.getElementById('cpsSetsContainer');
            const allItems = container.querySelectorAll('.accordion-item');

            if (allItems.length) {
                const lastItem = allItems[allItems.length - 1]; // last remaining item
                const lastCollapse = lastItem.querySelector('.accordion-collapse');
                const lastButton = lastItem.querySelector('.accordion-button');

                if (lastButton) lastButton.classList.remove('collapsed'); // remove collapsed class
                if (lastCollapse) {
                    const instance = bootstrap.Collapse.getOrCreateInstance(lastCollapse, {
                        toggle: false
                    });
                    instance.show(); // open the last accordion
                }
            }
        }



    });




    function renumberCpsSets() {
        const items = document.querySelectorAll('#cpsSetsContainer .accordion-item');

        items.forEach((item, index) => {
            const newIndex = index + 1;

            const titleSpan = item.querySelector('.accordion-title');
            const empName = item.dataset.empName;
            const cpsNo = item.dataset.cpsNo;

            if (titleSpan) {
                if (empName && cpsNo) {
                    titleSpan.innerHTML =
                        `<span class="title-text">CPS Set ${newIndex}: Employee & Remittance Details</span>
                                                                                                             <span class="emp-badge">${empName}</span>
                                                                                                             <span class="cps-badge">CPS No: ${cpsNo}</span>`;
                } else {
                    titleSpan.textContent = `CPS Set ${newIndex}: Employee & Remittance Details`;
                }
            }

            const header = item.querySelector('.accordion-header');
            const button = item.querySelector('.accordion-button');
            const collapse = item.querySelector('.accordion-collapse');

            if (header) header.id = `headingCps${newIndex}`;
            if (collapse) {
                collapse.id = `collapseCps${newIndex}`;
                collapse.setAttribute('aria-labelledby', `headingCps${newIndex}`);
            }
            if (button) {
                button.setAttribute('data-bs-target', `#collapseCps${newIndex}`);
                button.setAttribute('aria-controls', `collapseCps${newIndex}`);
            }
        });

        cpsSetCounter = items.length;
    }
    //////////////end/////////////////////////////////////

    $(document).on('change', '.yes-no-radio', function() {
        const $radio = $(this);
        const radioName = $radio.attr('name'); // e.g., "q1[1]" or "q2[1]"

        if (!radioName) return;

        // Convert "q1[1]" → "details1[1]" for the corresponding textarea
        const detailName = radioName.replace(/^q/, 'details');

        // Find the textarea in the same row with that name
        const $textarea = $radio.closest('tr').find(`textarea[name="${detailName}"]`);

        if ($radio.val() === 'N') {
            $textarea.removeClass('d-none');
        } else {
            $textarea
                .addClass('d-none')
                .val('')
                .removeClass('is-invalid');
        }
    });

    $(document).on('change', '.credited-select', function() {
        const $td = $(this).closest('td');
        const $textarea = $td.find('.credited_remarks');

        if ($(this).val() === 'N') {
            $textarea
                .removeClass('d-none');
        } else {
            $textarea
                .addClass('d-none')
                .val('')
                .removeClass('is-invalid');
        }
    });





    function validateCpsTable() {
        let isValid = true;
        let $firstInvalidField = null;

        $('.is-invalid').removeClass('is-invalid');



        $('.ddoandtreasury')
            .find('input[data-required="true"], textarea[data-required="true"]')
            .each(function() {
                const $field = $(this);

                if ($field.is('[type="hidden"]')) return true;

                if (!$field.val() || !$field.val().trim()) {
                    isValid = false;
                    $field.addClass('is-invalid');

                    if (!$firstInvalidField) {
                        $firstInvalidField = $field;
                    }
                }
            });

        $('#cpsSetsContainer tr').each(function() {
            const $row = $(this);



            $row.find('input[data-required="true"], textarea[data-required="true"]').each(function() {
                const $field = $(this);
                if ($field.is('[type="hidden"]')) return true;
                if ($field.is('textarea') && $field.hasClass('d-none')) return true;

                if (!$field.val() || !$field.val().trim()) {
                    isValid = false;
                    $field.addClass('is-invalid');

                    if (!$firstInvalidField) { // ✅ ADDED
                        $firstInvalidField = $field;
                    }
                }
            });


            // Validation: add red border if nothing selected
            $('.select2forcps').each(function() {
                const $select = $(this);
                let val = $select.val();
                if (Array.isArray(val)) val = val.filter(Boolean);

                if (!val || val.length === 0) {
                    $select.next('.select2-container').find('.select2-selection')
                        .addClass('is-invalid')
                        .css('border-color', '#dc3545');
                } else {
                    $select.next('.select2-container').find('.select2-selection')
                        .removeClass('is-invalid')
                        .css('border-color', '');
                }
            });

            // Remove red border immediately on selection
            $('.select2forcps').on('change', function() {
                const $select = $(this);
                $select.next('.select2-container').find('.select2-selection')
                    .removeClass('is-invalid')
                    .css('border-color', '');
            });





            const q1 = $row.find('input[name^="q1"]:checked').val();
            if (q1 === 'N') {
                const $details1 = $row.find('textarea[name^="details1"]');
                if (!$details1.val().trim()) {
                    isValid = false;
                    $details1.removeClass('d-none').addClass('is-invalid');

                    if (!$firstInvalidField) {
                        $firstInvalidField = $details1;
                    }
                }
            }

            const q2 = $row.find('input[name^="q2"]:checked').val();
            if (q2 === 'N') {
                const $details2 = $row.find('textarea[name^="details2"]');
                if (!$details2.val().trim()) {
                    isValid = false;
                    $details2.removeClass('d-none').addClass('is-invalid');

                    if (!$firstInvalidField) {
                        $firstInvalidField = $details2;
                    }
                }
            }

            // f) Credited in appropriate head validation
            const credited = $row.find('select[name^="creditedin_appropriatehead"]').val();

            if (credited === 'N') {
                const $remarks = $row.find('textarea[name^="credited_remarks"]');

                if (!$remarks.val() || !$remarks.val().trim()) {
                    isValid = false;
                    $remarks.removeClass('d-none').addClass('is-invalid');

                    if (!$firstInvalidField) {
                        $firstInvalidField = $remarks;
                    }
                }
            }

            // SHORTFALL REMITTANCE > 0
            const shortfallName = $row.find('input[name^="shortfall_remittance"]').attr('name');
            if (shortfallName && shortfallName.startsWith('shortfall_remittance')) {
                const setNo = shortfallName.match(/\[(\d+)\]/)[1];
                const shortfallVal = parseFloat($row.find(`input[name="shortfall_remittance[${setNo}]"]`)
                    .val()) || 0;

                const $shortfallWrap = $(`#shortfall_wrap\\[${setNo}\\]`);
                if (shortfallVal > 0) {
                    $shortfallWrap.removeClass('d-none'); // show wrapper
                    applyRestrictions($shortfallWrap[0]);

                    $shortfallWrap.find('input, select').each(function() {
                        const $field = $(this);
                        if ($field.is('[type="hidden"]')) return true; // skip hidden fields

                        // Validation check
                        if (!$field.val() || !$field.val().toString().trim()) {
                            isValid = false;
                            $field.addClass('is-invalid');
                            if (!$firstInvalidField) $firstInvalidField = $field;

                            // Remove invalid on input/change
                            $field.off('input change').on('input change', function() {
                                if ($field.val() && $field.val().toString().trim()) {
                                    $field.removeClass('is-invalid');
                                }
                            });
                        } else {
                            $field.removeClass('is-invalid');
                        }
                    });



                    $shortfallWrap.find('input').each(function() {
                        const $field = $(this);
                        if ($field.is('[type="hidden"]')) return true;
                        if (!$field.val() || !$field.val().toString().trim()) {
                            isValid = false;
                            $field.addClass('is-invalid');
                            if (!$firstInvalidField) $firstInvalidField = $field;
                        } else {
                            $field.removeClass('is-invalid');
                        }
                    });
                } else {
                    $shortfallWrap.addClass('d-none'); // hide wrapper if 0
                    $shortfallWrap.find('input').removeClass('is-invalid'); // remove any previous error
                }
            }

            // REMITTANCE EXCESS > 0
            const remExcessName = $row.find('input[name^="remittanceexcess_cps"]').attr('name');
            if (remExcessName && remExcessName.startsWith('remittanceexcess_cps')) {
                const setNo = remExcessName.match(/\[(\d+)\]/)[1];
                const remExcessVal = parseFloat($row.find(`input[name="remittanceexcess_cps[${setNo}]"]`)
                    .val()) || 0;

                const $remarks = $row.find(`textarea[name="remittanceexcess_remarks[${setNo}]"]`);
                    if (remExcessVal > 0) {
                        $remarks.removeClass('d-none'); // show textarea
                        if (!$remarks.val() || !$remarks.val().toString().trim()) {
                            isValid = false;
                            $remarks.addClass('is-invalid');
                            if (!$firstInvalidField) $firstInvalidField = $remarks;
                        } else {
                            $remarks.removeClass('is-invalid');
                        }
                    } else {
                        $remarks.addClass('d-none'); // hide textarea
                        $remarks.removeClass('is-invalid'); // remove any previous error
                    }
                }




            });




            $('#cpsSetsContainer .table-responsive table tbody tr').each(function() {
                const $row = $(this);

                $row.find('input[name], select[name]').each(function() {
                    const $field = $(this);
                    if ($field.is('[type="hidden"]')) return true;
                    // SKIP Treasury / DDO
                    const name = $field.attr('name');
                    if (name && (name.startsWith('treasury_cps') || name.startsWith('ddocode_cps'))) {
                        return; // skip validation
                    }

                    if (!$field.val() || !$field.val().toString().trim()) {
                        isValid = false;
                        $field.addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $field;
                        }
                    }
                });


                $row.find('select').each(function() {
                    const $select = $(this);

                    // skip hidden selects (if any)
                    if ($select.is(':hidden')) return true;

                    const val = $select.val();

                    // check for empty value(s)
                    if (!val || (Array.isArray(val) && val.length === 0) || (typeof val === 'string' && !val
                            .trim())) {
                        isValid = false;
                        $select.addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $select;
                        }
                    }
                });

            });

            if (!isValid && $firstInvalidField) {
                // console.log($firstInvalidField);
                //  alert();
                const $collapse = $firstInvalidField.closest('.accordion-collapse');

                if ($collapse.length) {
                    const collapseEl = $collapse[0];
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, {
                        toggle: false
                    });
                    bsCollapse.show();

                    setTimeout(() => {
                        collapseEl.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        $firstInvalidField.focus();
                    }, 200);
                }
            } else {
                // runs only if isValid OR $firstInvalidField is null
                $('#cpsValidationAlert').addClass('d-none'); // hide if all valid
            }




            return isValid;
        }


        function restrictSpecialChars(selector) {
            const sqlKeywords = [
                'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE', 'UNION', 'MERGE', 'CALL'
            ];


            $(selector)
                .off("keypress paste")
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;

                    if (selector === '.nbew_text') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        // Only allow letters (English + Tamil Unicode) and space, enforce maxlength
                        if (!/^[a-zA-Z\u0B80-\u0BFF\s]$/.test(char) || value.length >= maxLength) {
                            event.preventDefault();
                        }

                        let newValue = value + char;

                        if (/([a-zA-Z\u0B80-\u0BFF])\1{3,}/.test(newValue)) {
                            event.preventDefault();
                            return false;
                        }


                        let upperValue = newValue.toUpperCase();
                        for (let kw of sqlKeywords) {
                            if (upperValue.includes(kw)) {
                                event.preventDefault();
                                this.value = '';
                                alert('SQL keywords are not allowed!');
                                return false;
                            }
                        }


                    } else if (selector === '.nbew_number') {

                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        let value = this.value;

                        // Only allow numeric input and respect max length
                        if (!/^[0-9]$/.test(char) || value.length >= maxLength) {
                            event.preventDefault();
                        }

                    } else if (selector === '.nbew_specialchartextandnumber') {

                        let fieldName = $(this).attr('name') || '';
                        let value = $(this).val();
                        let position = value.length; // current index

                        if (fieldName.startsWith('shortfallfeadofaccount')) {

                            let char = String.fromCharCode(event.which);

                            // Max 16 characters
                            if (position >= 16) {
                                event.preventDefault();
                                return;
                            }

                            // First 9 → numbers only
                            if (position < 9) {
                                if (!/^[0-9]$/.test(char)) {
                                    event.preventDefault();
                                }
                            } else if (position < 11) {

                                if (/^[a-z]$/.test(char)) {
                                    // convert lowercase to uppercase
                                    event.preventDefault();
                                    let upperChar = char.toUpperCase();
                                    $(this).val(value + upperChar);
                                } else if (!/^[A-Z]$/.test(char)) {
                                    event.preventDefault();
                                }
                            } else {
                                if (!/^[0-9]$/.test(char)) {
                                    event.preventDefault();
                                }
                            }

                        } else {

                            let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                            if (!/^[A-Za-z0-9#\- ]$/.test(char) || value.length >= maxLength) {
                                event.preventDefault();
                            }
                        }
                    } else if (selector === '.nbew_number_decimal') {

                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        let maxIntLength = maxLength - 3; // reserve ".00"

                        // allow digits and dot
                        if (!/[0-9.]/.test(char)) {
                            event.preventDefault();
                        }

                        // only one dot
                        if (char === '.' && value.includes('.')) {
                            event.preventDefault();
                        }

                        // dot cannot be first
                        if (char === '.' && value.length === 0) {
                            event.preventDefault();
                        }

                        // before decimal → limit digits
                        if (!value.includes('.') && value.length >= maxIntLength && char !== '.') {
                            event.preventDefault();
                        }

                        // after decimal → only 2 digits
                        if (value.includes('.')) {
                            let dotPos = value.indexOf('.');
                            let decimals = value.substring(dotPos + 1);

                            if (decimals.length >= 2 && this.selectionStart > dotPos) {
                                event.preventDefault();
                            }
                        }
                    }




                })
                .on("paste", function(e) {
                    e.preventDefault();
                    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                    let cleanData = '';

                    if (selector === '.nbew_text') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        cleanData = pasteData.replace(/[^a-zA-Z\u0B80-\u0BFF\s]/g, '');

                        let upperData = cleanData.toUpperCase();
                        for (let kw of sqlKeywords) {
                            if (upperData.includes(kw)) {
                                alert('SQL keywords are not allowed!');
                                cleanData = cleanData.replace(new RegExp(kw, 'gi'), '');
                            }
                        }

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;
                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);
                        input.value = currentValue.substring(0, start) + cleanData + currentValue.substring(end);
                        input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                        return;
                    } 
                    else if (selector === '.nbew_number') {
                    let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                    cleanData = pasteData.replace(/[^0-9]/g, '');

                    let input = e.target;
                    let start = input.selectionStart;
                    let end = input.selectionEnd;
                    let currentValue = input.value;

                    let allowedLength = maxLength - (currentValue.length - (end - start));
                    if (allowedLength <= 0) return;

                    cleanData = cleanData.substring(0, allowedLength);
                    input.value =
                        currentValue.substring(0, start) +
                        cleanData +
                        currentValue.substring(end);

                    input.setSelectionRange(
                        start + cleanData.length,
                        start + cleanData.length
                    );
                    return; // stop default insert
                  }
				 else if (selector === '.nbew_specialchartextandnumber') {

                        let fieldName = $(this).attr('name') || '';

                        if (fieldName.startsWith('shortfallfeadofaccount')) {

                            let input = e.target;

                            // Remove invalid characters
                            let cleanData = pasteData.replace(/[^0-9A-Za-z]/g, '').toUpperCase();

                            // If less than 9 digits, just allow digits only
                            if (cleanData.length <= 9) {
                                cleanData = cleanData.replace(/[^0-9]/g, '');
                            } else {

                                let first9 = cleanData.substring(0, 9).replace(/[^0-9]/g, '');

                                let next2 = cleanData.substring(9, 11).replace(/[^A-Z]/g, '');

                                // If 2 letters not present → stop at 9 digits
                                if (next2.length < 2) {
                                    cleanData = first9;
                                } else {

                                    let last5 = cleanData.substring(11, 16).replace(/[^0-9]/g, '');

                                    cleanData = first9 + next2 + last5;
                                }
                            }

                            input.value = cleanData.substring(0, 16);
                            input.setSelectionRange(input.value.length, input.value.length);

                            e.preventDefault();
                            return;
                        }



                        // ---- OTHER FIELDS NORMAL ----
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        cleanData = pasteData.replace(/[^A-Za-z0-9#\- ]/g, '');

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;

                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);

                        input.value =
                            currentValue.substring(0, start) +
                            cleanData +
                            currentValue.substring(end);

                        input.setSelectionRange(
                            start + cleanData.length,
                            start + cleanData.length
                        );

                        return;
                    } else if (selector === '.nbew_number_decimal') {

                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        // allow only digits, dot, minus
                        cleanData = pasteData.replace(/[^0-9.]/g, '');

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;

                        // respect maxlength only
                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);

                        input.value =
                            currentValue.substring(0, start) +
                            cleanData +
                            currentValue.substring(end);

                        input.setSelectionRange(
                            start + cleanData.length,
                            start + cleanData.length
                        );

                        return;
                    }


                    // Insert clean data at cursor position
                    let input = e.target;
                    let start = input.selectionStart;
                    let end = input.selectionEnd;
                    let original = input.value;
                    input.value = original.substring(0, start) + cleanData + original.substring(end);
                    input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                })
                .on("blur", function() {

                    if (selector !== '.nbew_number_decimal') return;

                    let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                    let maxIntLength = maxLength - 3;
                    let v = this.value;

                    if (v === '') return;

                    v = v.replace(/[^0-9.]/g, '');

                    if (!v.includes('.')) {
                        v = v.substring(0, maxIntLength) + '.00';
                    } else {
                        let [i, d] = v.split('.');
                        i = i.substring(0, maxIntLength);
                        d = (d || '').padEnd(2, '0').substring(0, 2);
                        v = i + '.' + d;
                    }

                    this.value = v;
                });

        }
    </script>
@endsection
