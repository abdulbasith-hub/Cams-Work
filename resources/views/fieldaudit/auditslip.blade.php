@section('content')
    @extends('index2')
    @include('common.alert')
    <?php
    $instdel = json_decode($scheduledel, true);
    $getmajorobjection = json_decode($getMainobjection, true);
    
    // $teamhead = $instdel[0]['auditteamhead'];
    $instid = $instdel[0]['instid'];
    $auditscheduleid = $instdel[0]['auditscheduleid'];
    $schteammemberid = $instdel[0]['schteammemberid'];
    $auditplanid = $instdel[0]['auditplanid'];
    $teamheadid = $scheduleheadid;
    $teamhead = $sessionuserTeamheadOrNot;
    $exitmeetdate = $instdel[0]['exitmeetdate'];
    $todate_afterworking3days = $instdel[0]['todate_afterworking3days'];
    $today = date('Y-m-d');
    $catcode = $instdel[0]['catcode'];
    $subcatid = $instdel[0]['subcatid'];
    $financialyearcode = $instdel[0]['financialyearcode'];
    $spilloverflag = $spilloverflag;
    $annadhanam_only = $instdel[0]['annadhanam_only'];
    
    $rejoinderlimit = 1;
    $fileuploadcount = 2;
    $liabilitylimit = 5;
    
    if ($teamhead == 'Y') {
        $buttonname = 'Approve';
    } else {
        $buttonname = 'Forward';
    }
    
    $ensessionuserid = $session_userid;
    
    $sessiondel = session('user');
    $sessionuserid = $sessiondel->userid;
    
    // use Carbon\Carbon;
    
    // echo Carbon::now('Asia/Kolkata');
    
    // echo "System time: " . exec('date'); // Command for system time (works on UNIX systems)
    // echo "PHP time: " . date('Y-m-d H:i:s'); // PHP time
    // echo Carbon::now(); // This will print the current time in the set time zone
    
    ?>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


    <style>
        .ck-editor__editable {
            max-height: 300px;
            /* Set the max height as per your requirement */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }


        .ck-powered-by-balloon {
            display: none !important;
        }

        #container {
            width: 1000px;
            margin: 20px auto;
        }

        .ck-editor__editable[role="textbox"] {
            min-height: 200px;
        }

        .ck-editor__editable {
            font-family: 'Marutham', sans-serif;
        }

        .content-cell {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            /* Show only 2 lines */
            overflow: hidden;
            text-overflow: ellipsis;
            height: 40px;
            /* Adjust this based on your line height */
            line-height: 20px;
            /* Set this to match your text height */
            white-space: normal;
            /* Allow wrapping */
        }


        #container {
            width: 1000px;
            margin: 20px auto;
        }

        .card-fixed-width {
            width: 300px;
            /* Adjust to your preferred fixed width */
            max-width: 100%;
            /* Ensures it doesn't exceed screen width on smaller devices */
        }

        .ck-editor__editable[role="textbox"] {
            min-height: 200px;
        }

        .ck-editor__editable {
            font-family: 'Marutham', sans-serif;
        }

        .content-cell {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            /* Show only 2 lines */
            overflow: hidden;
            text-overflow: ellipsis;
            height: 40px;
            /* Adjust this based on your line height */
            line-height: 20px;
            /* Set this to match your text height */
            white-space: normal;
            /* Allow wrapping */
        }

        /* @font-face {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   font-family: 'Marutham';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   src: url('path/to/marutham.ttf') format('truetype');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   } */
        .card-body {
            padding: 15px 10px;
        }

        .card {
            margin-bottom: 10px;
        }

        .card-body {
            padding: 15px 10px;
        }

        .card {
            margin-bottom: 10px;
        }

        /* Step Circle Style */
        .step-circle {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 15px;
            text-align: center;
            border-radius: 50%;
            background-color: #fff;
            color: #0d6efd;
            font-weight: bold;
            /* position: absolute; */
            top: -10px;
            left: 10px;
            font-size: 14px;
            border: 2px solid #0d6efd;
        }

        /* Mobile View Adjustments */
        @media (max-width: 768px) {

            /* Make the navigation stack vertically on smaller screens */
            .nav-pills .nav-item {
                width: 100%;
                text-align: left;
                margin-bottom: 10px;
            }

            /* Adjust the .nav-link to display block on mobile */
            .nav-pills .nav-link {
                display: block;
                padding-left: 40px;
                /* Ensure the text doesn't overlap with the circle */
            }

            /* Adjust the circle position and size */
            .step-circle {
                position: relative;
                top: 0;
                left: 0;
                margin-right: 10px;
                font-size: 16px;
                display: inline-block;
            }

            /* Adjust the tab content for smaller screens */
            .tab-content {
                padding-left: 15px;
                padding-right: 15px;
            }

            /* Make the 3rd and 4th steps appear in separate rows */
            .tab-content .row {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .tab-pane .col-md-6 {
                width: 100%;
            }
        }

        /* Small screens, stack elements even more */
        @media (max-width: 576px) {
            .nav-pills .nav-item {
                width: 100%;
                text-align: left;
            }

            .nav-pills .nav-link {
                display: flex;
                align-items: center;
                padding-left: 40px;
                /* Keeps the circle alignment */
            }

            .step-circle {
                margin-right: 10px;
                font-size: 18px;
                top: 0;
                left: 0;
                display: inline-block;
            }

            /* Adjust the tab content padding for small screens */
            .tab-content {
                padding-left: 15px;
                padding-right: 15px;
            }

            /* Adjust rows in tab content to display properly on mobile */
            .tab-pane .row {
                display: flex;
                flex-direction: column;
            }

            .tab-pane .col-md-6 {
                width: 100%;
                /* Ensure full width for each column */
            }
        }

        /* For larger screens, keep the default horizontal nav-pills layout */
        @media (min-width: 992px) {
            .nav-pills .nav-item {
                width: auto;
                /* Revert the width to auto for large screens */
            }

            .nav-pills .nav-link {
                display: inline-block;
                /* Horizontal layout */
            }

            .step-circle {
                margin-right: 10px;
                font-size: 16px;
                top: -10px;
                left: 10px;
            }
        }

        .wizard .nav-link {
            font-weight: bold;
            border: 1px solid #7198b9;
            margin: 0 5px;
            border-radius: 5px;
        }

        .wizard .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }

        #audit_tab .tab-content {
            /* color: white;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   background-color: #428bca; */
            padding: 5px 15px;
        }

        .nav-tabs .nav-link.active {
            color: #0a58ca;
            background-color: #fff;
            border-color: #3782ce #3782ce #fff;
        }

        .nav-tabs {
            border-bottom: 1px solid #3782ce;
        }

        .nav-tabs,
        .nav-tabs .nav-link {
            border-radius: 2px;
        }

        .nav-tabs .nav-link {
            margin-bottom: -1px;
            background: 0 0;
            color: #111213;
            border: 2px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
        }

        .nav-link:hover {
            color: #111213;
        }



        .accordion-body {

            padding: 10px;
        }

        .viewstack {
            display: flex;
            flex-direction: row;
            align-items: center;
            align-self: stretch;
        }


        /* ---------------------------mobile view----------------------------------------------------- */
        /* .hstack {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                display: flex;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 flex-wrap: wrap;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                justify-content: flex-start;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                gap: 10px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } */

        .hstack .div {
            display: ;
            align-items: end;
            gap: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hstack .badge {
            min-width: 15px;
            min-height: 15px;
        }

        @media (max-width: 576px) {
            .hstack {
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 10px;

            }

            .hstack .div {
                flex-shrink: 1;
                max-width: unset;
            }

            .hstack .div:nth-child(3) {
                text-align: center;
            }
        }


        /* ✅ Default (Desktop View) */
        .filter-search-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ✅ Mobile View (Below 576px) - Show in Same Row */
        @media (max-width: 576px) {
            .filter-search-wrapper {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            /* ✅ Auto Expand Search Input */
            .filter-search-wrapper .search-input {
                flex-grow: 1;
            }

            .filter-search-wrapper .search-input input {
                width: 100%;
                padding: 5px 10px;
                font-size: 12px;
            }

            /* ✅ Keep Filter Button Fixed */
            .filter-search-wrapper .dropdown a {
                font-size: 15px !important;
                padding: 5px 10px;
                white-space: nowrap;
            }
        }


        /* ✅ Default View (Desktop) */
        .button-wrapper {
            margin-left: 39%;
        }

        /* ✅ Centering in Mobile View */
        @media (max-width: 576px) {
            .button-wrapper {
                margin-left: 0;
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .button-wrapper .d-flex {
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .button-wrapper button {
                flex-grow: 1;
                max-width: 120px;
            }
        }


        .chat-users {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        /* Desktop View - Show fully without scrolling */
        @media (min-width: 769px) {
            .chat-users {
                max-height: none;
                overflow: visible;
            }
        }

        /* Mobile View - Show only first 5 and make remaining scrollable */
        @media (max-width: 768px) {
            .chat-users {
                max-height: 230px;
                /* Height enough to show 5 slips */
                overflow-y: auto;
            }

            .chat-users li {
                border-bottom: 1px solid #e0e0e0;
            }
        }


        /* Mobile View - Keep Add and Delete button in same row */
        @media (max-width: 768px) {
            .action-row {
                display: flex;
                align-items: center;
            }

            .mar_left {
                margin-left: 8px;

            }

            .action-row .d-flex {
                flex-direction: row !important;
                gap: 10px;
            }

            .action-row button {
                flex-grow: 1;
            }
        }

        /* .button-container {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            display: flex;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            flex-wrap: wrap;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            justify-content: center;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            width: 100%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            max-width: 400px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                        @media (max-width: 768px) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            .button-container {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                max-width: 300px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                justify-content: center;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            .button-container button:nth-child(3) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                text-align: center;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */



        @media (max-width: 768px) {


            .button-container {

                margin-top: 5px;
            }
        }
    </style>

    <?php $fromdate = \Carbon\Carbon::parse($instdel[0]['fromdate'])->format('d-m-Y'); ?>
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

    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Name</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['instename']; ?>" disabled>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Category</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['catename']; ?>" disabled>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Type of
                                Audit</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['typeofauditename']; ?>" disabled>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Year of
                                Audit</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['yearname']; ?>" disabled>
                        </div>
                        @if ($instdel[0]['deptcode'] == '01' && $instdel[0]['annadhanam_only'] == 'Y')
                            <div class="col-md-2 mb-3"> <label class="form-label required"
                                    for="validationDefault01">Annadhanam Year</label> <input type="text"
                                    class="form-control" id="total_mandays" name="total_mandays" value="<?php echo $instdel[0]['annadhanamyear']; ?>"
                                    disabled>
                            </div>
                        @endif
                        <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Total
                                Mandays</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['mandays']; ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card card_border">
                <div class="card-body">
                    <div class="hstack mb-2">
                        <div class="div ms-2">
                            <span class="badge active_div">
                            </span>
                            <span class="">Selected slip</span>
                        </div>
                        <div class="div ms-2 ">
                            <span class="badge drop_div">
                            </span>
                            <span class="">Dropped</span>
                        </div>
                        <div class="div ms-2">
                            <span class="badge contopara_div">
                            </span>
                            <span class="">Converted to para</span>
                        </div>
@if ($spilloverflag == 'Y')
<div class="div ms-2">
    <a class="btn btn-sm btn-success"
        href="{{ route('spilloverdel', ['auditplanid' => Crypt::encryptString($auditplanid)]) }}"
        target="_blank" rel="noopener noreferrer">

        <i class="ti ti-plus fs-4 me-2"></i>
        <span class="lang">Previous Quarter Slips</span>
    </a>
</div>
@endif
                       </div>


                    <div class="row">
                        <div class="col-md-1 border-end user-chat-box">
                            <div class="d-lg-none  d-flex  mb-2">
                                <!-- Filter Button -->
                                <div class="dropdown me-2" style="width:90px;">
                                    <a class="text-light fs-4 nav-icon-hover btn btn-sm bg-info" href="javascript:void(0)"
                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter <i class="ti ti-adjustments"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="getSlipBasedOnfilter('A')">All</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="getSlipBasedOnfilter('F')">Forwarded</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="getSlipBasedOnfilter('P')">Pending</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="getSlipBasedOnfilter('C')">Completed</a></li>
                                    </ul>
                                </div>
                                <!-- Search Input -->
                                <div class="flex-grow-1 ">
                                    <input type="text" class="form-control search-chat py-2" id="text-srh"
                                        placeholder="Search" />
                                </div>
                            </div>

                            <!-- Desktop View (Filter Above Search) -->
                            <div class="d-none d-lg-block">
                                <div class="position-relative mb-2">
                                    <div class="dropdown">
                                        <a class="text-light fs-4 nav-icon-hover btn btn-sm bg-info"
                                            href="javascript:void(0)" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Filter <i class="ti ti-adjustments"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="getSlipBasedOnfilter('A')">All</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="getSlipBasedOnfilter('F')">Forwarded</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="getSlipBasedOnfilter('P')">Pending</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="getSlipBasedOnfilter('C')">Completed</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <div class="position-relative mb-4">
                                        <input type="text" class="form-control search-chat py-2" id="text-srh"
                                            placeholder="Search" />
                                    </div>
                                </div>
                            </div>

                            <div class="app-chat">
                                <div class="overflow-auto card mb-0 shadow-none border h-50">
                                    <ul class="chat-users mb-0 mh-n100" data-simplebar="" style="max-height:600px">
                                        <!-- Existing chat slip number items will go here -->
                                    </ul>
                                </div>
                            </div>
                        </div>



                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-3">
                                    <b><span id="forwardedby" class="text-end"></span></b>
                                </div>
                                <div class="col-md-3">
                                    <!-- Optional: You can place another item here if needed -->
                                    <b><span id="mainslipnumber" class="text-end"></span></b>
                                </div>
                                @if ($cpsform == 'Y')
                                    <div class="col-md-3 text-end mb-1">
                                        <a id="cpsDetailsBtn" class="btn btn-primary button_cps"
                                            href="{{ route('cps.details', ['instid' => $encrypted_instid, 'scheduleid' => $encrypted_auditscheduleid]) }}">
                                            CPS Details
                                        </a>
                                    </div>
                                @endif
                                @if ($api === 'Y' && !empty($paccId))
                                    <div class="col-md-4 mb-1 d-flex justify-content-end">

                                        <form action="{{ route('auditor.login.epacs') }}" method="POST" target="_blank"
                                            rel="noopener noreferrer">
                                            @csrf

                                            <input type="hidden" name="pacsid" value="{{ $paccId->erpno }}">
                                            <input type="hidden" name="dccbid" value="{{ $paccId->dccbid }}">
                                            <input type="hidden" name="schteammemberid" value="{{ $schteammemberid }}">

                                            <button type="submit" class="btn btn-danger epacs-login-btn">
                                                Login to ePACS
                                            </button>
                                        </form>

                                        <span class="badge bg-danger text-light rounded-pill blinking-badge"
                                            id="processname">
                                        </span>

                                    </div>
                                @endif




                                <div class="col-md-3 mb-1 d-flex justify-content-end">
                                    <span class="badge bg-danger text-light rounded-pill blinking-badge"
                                        id="processname"></span>
                                </div>
                            </div>
                            <div class="card" style="border-color: #7198b9">
                                <div class="card-body">
                                    <form id="auditslip" name="auditslip" class="">
                                        <input type="hidden" name="currentslipnumber" id="currentslipnumber"
                                            value=''>
                                        <input type="hidden" name="filter" id="filter" value='A'>
                                        <input type="hidden" name="ens" id="ens" value='<?php echo $ensessionuserid; ?>'>
                                        <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                                        <input type="hidden" name="active_fileid" id="active_fileid">
                                        <input type="hidden" name="seriesno" id="seriesno" value='1'>
                                        <input type="hidden" name="fileuploadstatus" id="fileuploadstatus"
                                            value=''>
                                        <input type="hidden" name="fileuploadid" id="fileuploadid" value=''>
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value='<?php echo $encrypted_auditscheduleid; ?>'>
                                        <input type="hidden" name="schteammemberid" id="schteammemberid"
                                            value='<?php echo $schteammemberid; ?>'>
                                        <input type="hidden" name="auditplanid" id="auditplanid"
                                            value='<?php echo $auditplanid; ?>'> <input type="hidden" name="auditslipid"
                                            id="auditslipid">
                                        <input type="hidden" name="rejoinderstatus" id="rejoinderstatus"
                                            value=''>
                                        <input type="hidden" name="auditquartercode" id="auditquartercode"
                                            value='Q1'>
                                        <input type="hidden" name="financialyear" id="financialyear" value='2026'>
                                        <input type="hidden" name="slipcreatedyear" id="slipcreatedyear">
                                        <input type="hidden" name="rejoindercount" id="rejoindercount" value=''>
                                        <input type="hidden" name="slipcreatedby" id="slipcreatedby" value=''>
                                        <input type="hidden" name="liabilityid" id="liabilityid" value=''>
                                        <input type="hidden" name="deleted_liabilityid" id="deleted_liabilityid"
                                            value=''>
                                        <input type="hidden" name="liabilitytype" id="liabilitytype" value=''>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label required lang" for="validationDefaultUsername"
                                                    key="major_obj">Title/Heading</label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="majorobjectioncode" name="majorobjectioncode"
                                                    onchange="getminorobjection('','','minorobjectioncode','majorobjectioncode')">
                                                    <option value='' data-name-en="Select Title/Heading"
                                                        data-name-ta="முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்"></option>
                                                    @if (!empty($getMainobjection) && count($getMainobjection) > 0)
                                                        @foreach ($getMainobjection as $m)
                                                            <option value="{{ $m->mainobjectionid }}"
                                                                data-name-en="{{ $m->objectionename }}"
                                                                data-name-ta="{{ $m->objectiontname }}">
                                                                {{ $m->objectionename }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option disabled data-name-en="No Objection Available"
                                                            data-name-ta="துறைகள் எதுவும் இல்லை">No Objection Available
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label required lang" for="validationDefaultUsername"
                                                    key="minor_obj">Categorization of Paras
                                                </label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="minorobjectioncode" name="minorobjectioncode">
                                                    <option value='' data-name-en="Select sub objection"
                                                        data-name-ta="துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்"></option>

                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-2"> <label class="form-label lang"
                                                    for="validationDefaultUsername" key="amount_involved">Amount
                                                    Involved</label> <input type="text"
                                                    class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                    id="amount_involved" name="amount_involved" maxlength="15">
                                            </div>

                                            <div class="col-md-2 mb-2">
                                                <label class="form-label required lang " for="validationDefaultUsername"
                                                    key="severity">Severity</label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="severityid" name="severityid">
                                                    <!-- <option value="">@lang('Select Severity')</option> -->
                                                    <option value='' data-name-en="Select Severity"
                                                        data-name-ta="தெரிவு கடைசியாக"></option>
                                                    @if (!empty($severitydel) && count($severitydel) > 0)
                                                        @foreach ($severitydel as $s)
                                                            <option value="{{ $s->severitycode }}"
                                                                data-name-en="{{ $s->severityelname }}"
                                                                data-name-ta="{{ $s->severitytlname }}">
                                                                {{ $s->severityelname }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option disabled data-name-en="No Severity Available"
                                                            data-name-ta="துறைகள் எதுவும் இல்லை">No Severity
                                                            Available
                                                        </option>
                                                    @endif

                                                </select>


                                            </div>


                                        </div>

                                        <div class="row mt-2 ">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-5 mb-2">
                                                        <label class="form-label required lang"
                                                            key="scheme">Scheme</label> <br>
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input success" type="radio"
                                                                    name="scheme" id="Y" value="Y"
                                                                    onchange="enable_schemename('Y')">
                                                                <label class="form-check-label lang" for="Y"
                                                                    key="yes">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input success" type="radio"
                                                                    name="scheme" id="N" value="N"
                                                                    onchange="enable_schemename('N')" checked>
                                                                <label class="form-check-label lang" for="N"
                                                                    key="no">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-sm-12 col-md-6 "id="">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Name</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            id="liabilityname" name="liabilityname"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            placeholder="Enter Liability name">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->
                                                    <div class="col-md-7 mb-2 hide_this" id="severityDiv">
                                                        <label class="form-label required lang" key="schemename">Scheme
                                                            Name</label>
                                                        <select class="select form-control custom-select lang-dropdown"
                                                            id="schemename" name="schemename">
                                                            <option value='' data-name-en="Select Scheme Name"
                                                                data-name-ta="தெரிவு கடைசியாக"></option>
                                                            @if (!empty($schemename) && count($schemename) > 0)
                                                                @foreach ($schemename as $s)
                                                                    <option value="{{ $s->auditeeschemecode }}"
                                                                        data-name-en="{{ $s->auditeeschemeelname }}"
                                                                        data-name-ta="{{ $s->auditeeschemetlname }}">
                                                                        {{ $s->auditeeschemeelname }}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option disabled data-name-en="No Scheme Available"
                                                                    data-name-ta="எதுவும் இல்லை">
                                                                    No Scheme Available
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>


                                                </div>

                                            </div>


                                            <div class="col-md-2 mb-2">
                                                <label class="form-label required lang " key="serious"
                                                    for="validationDefaultUsername" key="">Irregularities</label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="serious" name="serious"
                                                    onchange="getcategoryBasedOnSerious('','')">
                                                    <!-- <option value="">@lang('Select Severity')</option> -->
                                                    <option value='' data-name-en="Select Serious"
                                                        data-name-ta="தீவிரமானதைத் தேர்ந்தெடுக்கவும்">
                                                    </option>
                                                    @if (!empty($serious) && count($serious) > 0)
                                                        @foreach ($serious as $s)
                                                            <option value="{{ $s->irregularitiescode }}"
                                                                data-name-en="{{ $s->irregularitieselname }}"
                                                                data-name-ta="{{ $s->irregularitiestlname }}">
                                                                {{ $s->irregularitieselname }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option disabled data-name-en="No serious Available"
                                                            data-name-ta="">
                                                            No serious Available
                                                        </option>
                                                    @endif

                                                </select>


                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label required lang" key="category"
                                                    for="validationDefaultUsername">Category</label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="category" name="category"
                                                    onchange="getsubcategoryBasedOnCategory('','')">
                                                    <!-- <option value="">@lang('Select Severity')</option> -->
                                                    <option value="" data-name-en="Select Category"
                                                        data-name-ta="வகையைத் தேர்ந்தெடுக்கவும்">Select Category</option>

                                                    <option value="" disabled id=""
                                                        data-name-en="No Category Available"
                                                        data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>


                                                </select>


                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label required lang " for="validationDefaultUsername"
                                                    key="if_subcategory">Sub Category</label>
                                                <select class="select form-control custom-select lang-dropdown"
                                                    id="subcategory" name="subcategory">
                                                    <!-- <option value="">@lang('Select Severity')</option> -->
                                                    <option value="" data-name-en="Select SubCategory"
                                                        data-name-ta="உபவகை தேர்ந்தெடுக்கவும்">---Select SubCategory
                                                    </option>


                                                </select>


                                            </div>
                                        </div>




                                        <div class="row mt-2  ">
                                            <div class="col-md-4 mb-2"> <label class="form-label required lang"
                                                    for="validationDefaultUsername" key="slip_details">Slip
                                                    Details</label>
                                                <textarea id="slipdetails" maxlength="500" name="slipdetails" class="form-control" placeholder="Enter remarks"></textarea>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6">
                                                        <label class="form-label required lang"
                                                            for="validationDefaultUsername" key="liability">
                                                            Liablility</label> <br>
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input success" type="radio"
                                                                    name="liability" id="Y" value="Y"
                                                                    onchange="enable_liability('Y')">
                                                                <label class="form-check-label lang" for="all"
                                                                    key="yes">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input success" type="radio"
                                                                    name="liability" id="N" value="N"
                                                                    checked onchange="enable_liability('N')">
                                                                <label class="form-check-label lang" for="district"
                                                                    key="no">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-sm-12 col-md-6 hide_this"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="liabilityname_div">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Name</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            id="liabilityname" name="liabilityname"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            placeholder="Enter Liability name">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                                </div>


                                            </div>




                                            <div id="dynamicRowsContainer">

                                            </div>

                                            <!-- <div class="col-md-4 hide_this" id="liabilitygpfno_div">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="row">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="col-sm-12 col-md-6 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                GPF / CPF No</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                id="liabilitygpfno" name="liabilitygpfno"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                placeholder="Enter Liability GPFno">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="col-sm-12 col-md-6 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Designation</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                id="liabilitydesig" name="liabilitydesig"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                placeholder="Enter Liability Designation">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div> -->



                                        </div>
                                        <hr>

                                        <div id="auditorAccordionsContainer"></div>

                                        <div class="row hide_this" id="auditorremarksdiv">
                                            <div class="col-md-12 ">
                                                <label class="form-label required lang" for="validationDefaultUsername"
                                                    key="observation"> Auditor
                                                    Observation/Remarks</label>
                                                <textarea id="auditorremarks" class="form-control" placeholder="Enter remarks" name="auditorremarks"></textarea>
                                            </div>
                                            <div class="col-md-12 p-6">
                                                <label class="form-label  lang" for="validationDefaultUsername"
                                                    key="attachments">Attachments </label>
                                                <span style="color:#ff0000; font-size:10px;">(Max Size : 3 MB & File
                                                    Format
                                                    : Pdf,Excel )</span>
                                                <div class="row mb-2">


                                                    <div class="col-md-12 d-flex align-items-center">
                                                        <div class="file-input-container" id="file-input-container">
                                                            <div id="file-list" class="d-flex flex-wrap gap-2"></div>
                                                            <template id="file-preview">
                                                                <div class="position-relative text-center file-preview"
                                                                    style="width: 120px;">
                                                                    <div class="img-thumbnail">
                                                                        <img src="" height="100" />
                                                                        <div class="position-absolute text-white fs-1 text-wrap bg-dark p-1 small w-100"
                                                                            style="bottom: 0; left: 0; opacity: 0.8;">
                                                                            <span class="file-name">File Name</span> |
                                                                            <span class="file-size">0 KB</span>
                                                                        </div>
                                                                        <button style="top: 5px; right: 5px;"
                                                                            class="btn btn-sm btn-danger position-absolute"
                                                                            onclick="removeFilePreview(this)">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <input class="d-none" multiple="multiple"
                                                                        type="file" name="fileupload[]"
                                                                        accept=".pdf">
                                                                </div>
                                                            </template>
                                                        </div>


                                                        <label for="upload_input" id="add-file-btn"
                                                            class="btn btn-success btn-sm ms-2 ">
                                                            <i class="fs-6 ti ti-plus"></i>
                                                        </label>
                                                        <input id="upload_input" type="file" name="fileupload[]"
                                                            class="d-none" multiple="multiple"
                                                            onchange="window.breakIntoSeparateFiles(this, '#file-list', '#file-preview')" />
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row">
                                                    <div class="position-relative d-flex flex-row"
                                                        id="file-list-container">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="action" name="action" value="insert">

                                            <div class="row mt-1 hide_this" id="freshformbtn">

                                                <div class="" style="">
                                                    <div class=" text-center gap-6">
                                                        <button class="btn button_save" id="buttonaction"
                                                            name="buttonaction">Save Draft </button>
                                                        <button class="btn button_finalise text-center"
                                                            id="approvebtn"><?php echo $buttonname; ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-1 hide_this" id="forwardbtn">
                                                <div class="form-check form-check-inline">
                                                    <input class="viewslip_form-check-input success" type="radio"
                                                        name="verifiedClarificationRadiobtn" id="verified"
                                                        value="V">
                                                    <label class=""><b>Verified</b></label>
                                                </div>
                                                <div class="form-check form-check-inline hide_this"
                                                    id="needclarificationbtn">
                                                    <input class="viewslip_form-check-input success" type="radio"
                                                        name="verifiedClarificationRadiobtn" id="needclarification"
                                                        value="R">
                                                    <label class="" for="district"><b>Need more
                                                            Clarification</b></label>
                                                </div>
                                                <div class="col-md-6" style="margin-left: 39% !important;">
                                                    <div class="d-flex align-items-center gap-6">
                                                        <button class="btn button_finalise lang" key="forward"
                                                            id="memberReplyfwd" name="memberReplyfwd"> </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-1 hide_this" id="finalisebtn">
                                                <div class="form-check form-check-inline">
                                                    <input class="viewslip_form-check-input success" type="radio"
                                                        name="verifiedClarificationRadiobtn" id="verified"
                                                        value="V">
                                                    <label class=""><b>Verified</b></label>
                                                </div>
                                                <div class="col-md-12    mt-2">
                                                    <div class=" gap-2 text-center">
                                                        <button class="btn button_finalise lang" id="dropSlip"
                                                            key="dropbtn" name="dropSlip">Drop</button>
                                                        <button class="btn button_save lang" id="convertSlip"
                                                            key="converttoparabtn" name="convertSlip">Convertto
                                                            Para</button>
                                                        <button
                                                            class="btn button_rejoinder hide_this lang button-container"
                                                            id="rejoinderSlip" key="rejoinderbtn"
                                                            name="rejoinderSlip">Rejoinder</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </form>
                                    <div id="nodata_available" class="hide_this">
                                        <center> No Data Avaiable</center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <hr>
            </div>
        </div>
    </div>

    </script>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/apps/chat.js"></script>
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script>
        function enable_schemename(selectedOption) {
            if (selectedOption === 'Y') {

                $('#severityDiv').show();


            } else {
                $('#severityDiv').hide();
                $('#schemename').val('');

            }
        }





        function getcategoryBasedOnSerious(serious, selectedRegioncode = null) {
            // alert('te');
            const lang = getLanguage();

            // const districtDropdown = $('#district');
            const categoryDropdown = $('#category');
            // const institutionDropdown = $('#institution');

            categoryDropdown.html(`
            <option value="" data-name-en="Select Category Name" data-name-ta="வகை பெயரைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'வகை பெயரைத் தேர்ந்தெடுக்கவும்' : 'Select Category Name'}
            </option>
            `);

            if (serious == "") {
                var serious = $("#serious").val();
                // alert(deptcode);
            }
            if (!serious) {
                categoryDropdown.append(`
                <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                </option>
            `);


                return;
            }
            if (serious) {
                $.ajax({
                    url: "/getcategoryBasedOnSerious",
                    type: "POST",
                    data: {
                        serious: serious,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(catcode => {
                                categoryDropdown.append(
                                    `<option value="${catcode.irregularitiescatcode}"
                                     data-name-en="${catcode.irregularitiescatelname}"
                                      data-name-ta="${catcode.irregularitiescattlname}"
                                     ${catcode.irregularitiescatcode === selectedRegioncode ? 'selected' : ''}>
                                    ${lang === 'ta' ? catcode.irregularitiescattlname : catcode.irregularitiescatelname }</option>`
                                );
                            });
                        } else {
                            categoryDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
                        }
                    },
                    error: function() {
                        alert('Error fetching region. Please try again.');
                    }
                });
            }

        }




        function getsubcategoryBasedOnCategory(category, selectedRegioncode = null) {
            // alert('te');
            // const districtDropdown = $('#district');
            const subcategoryDropdown = $('#subcategory');
            // const institutionDropdown = $('#institution');

            const lang = getLanguage();
            subcategoryDropdown.html(`
                <option value="" data-name-en="Select SubCategory" data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select SubCategory'}
                </option>
            `);
            // districtDropdown.html('<option value="">Select District Name</option>');
            // institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (category == "") {
                var category = $("#category").val();
                // alert(deptcode);
            }
            if (!category) {
                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `); //  districtDropdown.append('<option value="" disabled>No District Available</option>');
                // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');


                return;
            }
            if (category) {
                $.ajax({
                    url: "/getsubcategoryBasedOnCategory",
                    type: "POST",
                    data: {
                        category: category,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(subcategory => {
                                subcategoryDropdown.append(
                                    `<option value="${subcategory.irregularitiessubcatcode}"
                                     data-name-en="${subcategory.irregularitiessubcatelname}"
                                    data-name-ta="${subcategory.irregularitiessubcattlname}"
                                    ${subcategory.irregularitiessubcatcode === selectedRegioncode ? 'selected' :''}>
                                    ${lang === 'ta' ? subcategory.irregularitiessubcattlname : subcategory.irregularitiessubcatelname }</option>`
                                );
                            });
                        } else {
                            subcategoryDropdown.append(`
                            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                            </option>
                        `);
                        }
                    },
                    error: function() {
                        // alert('Error fetching region. Please try again.');
                        subcategoryDropdown.append('<option disabled>No Subcategory Available</option>');

                    }
                });
            }

        }


        /***************************************************** File - upload template ******************************************************************* */


        var rowCount = 0; // To keep track of the number of rows added

        var fileCount = 0;

        function removeFilePreview(button) {
            // alert(fileCount);
            fileCount--;
            // alert(fileCount);
            $(button).closest('.file-preview').remove();
            if (fileCount >= '<?php echo $fileuploadcount; ?>') {
                $('#add-file-btn').hide();
            } else {
                $('#add-file-btn').show();
            }
        }
        (function(window, $) {
            var FILE_ICON_URL = '../assets/images/file.png';
            var ALLOWED_TYPES = ['application/pdf'];
            var MAX_FILE_SIZE_MB = 2;
            var MAX_FILE_COUNT = '<?php echo $fileuploadcount; ?>';

            function addFileToNewInput(file, newInput) {
                if (!newInput) {
                    return;
                }

                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                newInput.files = dataTransfer.files;
            }

            function setFileDetails(file, $previewElement) {
                // Populate file name
                $previewElement.find('.file-name').text(file.name);
                // Populate file size in KB with 2 decimal precision
                var fileSizeInKB = (file.size / 1024).toFixed(2) + ' KB';
                $previewElement.find('.file-size').text(fileSizeInKB);
            }

            function addSrcToPreview(file, preview) {
                if (!preview) {
                    return;
                }

                if (file.type.match(/image/)) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    $('.file-preview').show();
                } else {
                    preview.src = FILE_ICON_URL;
                    $('.file-preview').show();
                }

            }

            function showError(message, $previewElement) {
                var $errorElement = $previewElement.find('.file-error');
                if ($errorElement.length === 0) {
                    $errorElement = $('<div class="file-error" style="color: red; font-size: 12px;"></div>').appendTo(
                        $previewElement);
                }
                $errorElement.text(message);
            }

            // function breakIntoSeparateFiles(input, targetSelector, templateSelector) {

            //     $('#file-input-container').show();
            //     var $input = $(input);

            //     var templateHtml = $(templateSelector).html();

            //     if (!input.files) {
            //         return;
            //     }

            //     var existingFileCount = $(targetSelector).children().length; // Existing file previews
            //     var newFileCount = input.files.length; // New files being added
            //     fileCount += newFileCount;

            //     if (fileCount > MAX_FILE_COUNT) {
            //         alert('Only ' + MAX_FILE_COUNT + ' files are allowed.');
            //         return;
            //     }

            //     for (var file of input.files) {
            //         var $newFile = $(templateHtml).appendTo(targetSelector);
            //         var errorMessage = '';

            //         // Validate file type
            //         if (!ALLOWED_TYPES.includes(file.type)) {
            //             errorMessage = 'Invalid type';
            //         }

            //         // Validate file size
            //         if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
            //             alert('File Size Exceeds');
            //             // return;
            //         }

            //         // Ensure total files do not exceed the limit
            //         if (fileCount > MAX_FILE_COUNT) {
            //             errorMessage = 'Max files limit';
            //         }

            //         if (errorMessage) {
            //             setFileDetails(file, $newFile);
            //             showError(errorMessage, $newFile);
            //             $newFile.find('img').remove(); // Remove preview image since the file is invalid
            //             continue;
            //         }
            //         if (fileCount >= '<?php echo $fileuploadcount; ?>') {
            //             $('#add-file-btn').hide();
            //         } else {
            //             $('#add-file-btn').show();
            //         }
            //         addFileToNewInput(file, $newFile.find("input")[0]);
            //         addSrcToPreview(file, $newFile.find("img")[0]);
            //         setFileDetails(file, $newFile);

            //         // fileCount++;
            //     }

            //     $input.val([]);
            // }
            //         function breakIntoSeparateFiles(input, targetSelector, templateSelector) {
            //     $('#file-input-container').show();
            //     var $input = $(input);

            //     var templateHtml = $(templateSelector).html();

            //     if (!input.files) {
            //         return;
            //     }

            //     var existingFileCount = $(targetSelector).children().length; // Existing file previews
            //     var newFileCount = input.files.length; // New files being added


            //     if (fileCount > MAX_FILE_COUNT) {
            //         alert('Only ' + MAX_FILE_COUNT + ' files are allowed.');
            //         fileCount -= newFileCount;
            //         return;
            //     }

            //     for (var file of input.files) {


            //         // Validate file type
            //         if (!ALLOWED_TYPES.includes(file.type)) {
            //             alert('Invalid file type. Please upload PDF, Excel, JPG, JPEG, or PNG files only.');
            //             continue; // Skip adding this file
            //         }

            //         // Validate file size
            //         if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
            //             alert('File size exceeds the 3MB limit.');
            //             continue; // Skip adding this file
            //         }

            //         // Ensure total files do not exceed the limit
            //         if (fileCount > MAX_FILE_COUNT) {
            //             alert('Max file limit reached.');
            //             break; // Stop adding more files
            //         }

            //         var $newFile = $(templateHtml).appendTo(targetSelector);
            //         fileCount += newFileCount;

            //         if (fileCount >= '<?php echo $fileuploadcount; ?>') {

            //             $('#add-file-btn').hide();
            //         } else {
            //             $('#add-file-btn').show();

            //         }

            //         // addFileToNewInput(file, $newFile.find("input")[0]);
            //         // addSrcToPreview(file, $newFile.find("img")[0]);
            //         // setFileDetails(file, $newFile);

            // 	  processFile(file, $newFile);
            //                 // fileCount++;
            //             }
            //             async function processFile(file, $newFile) {
            //                 // Ensure this runs first
            //                 await addFileToNewInput(file, $newFile.find("input")[0]);
            //                 await addSrcToPreview(file, $newFile.find("img")[0]);
            //                 await setFileDetails(file, $newFile);
            //             }

            //     $input.val([]);
            // }

            // function breakIntoSeparateFiles(input, targetSelector, templateSelector) {
            //     $('#file-input-container').show();
            //     var $input = $(input);
            //     var templateHtml = $(templateSelector).html();

            //     if (!input.files) {
            //         return;
            //     }

            //     var existingFileCount = $(targetSelector).children().length; // Existing previews
            //     existingFileCount   =   existingFileCount   +   fileCount;

            //     var newFiles = Array.from(input.files); // Convert FileList to array for easier processing
            //     var validFiles = [];

            //     // Validate files before appending
            //     newFiles.forEach(file => {
            //         // Check file type
            //         if (!ALLOWED_TYPES.includes(file.type)) {
            //             alert('Invalid file type. Please upload PDF, Excel, JPG, JPEG, or PNG files only.');
            //             return;
            //         }

            //         // Check file size
            //         if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
            //             alert('File size exceeds the 3MB limit.');
            //             return;
            //         }

            //         // Ensure total files do not exceed the limit
            //         if (existingFileCount + validFiles.length >= MAX_FILE_COUNT) {
            //             alert('Max file limit reached.');
            //             return;
            //         }

            //         validFiles.push(file);
            //     });

            //     // If no valid files, exit
            //     if (validFiles.length === 0) {
            //         return;
            //     }

            //     validFiles.forEach(file => {
            //         var $newFile = $(templateHtml).appendTo(targetSelector);

            //         // Process the file (async handling)
            //         processFile(file, $newFile).then(() => {
            //             existingFileCount++; // Update count only after successful processing

            //             // Hide or show the "Add File" button based on the limit
            //             if (existingFileCount >= MAX_FILE_COUNT) {
            //                 $('#add-file-btn').hide();
            //             } else {
            //                 $('#add-file-btn').show();
            //             }
            //             fileCount++;
            //         });
            //     });

            //     // Clear input to allow re-selection of same files
            //     $input.val([]);
            // }

            function breakIntoSeparateFiles(input, targetSelector, templateSelector) {
                $('#file-input-container').show();
                var $input = $(input);
                var templateHtml = $(templateSelector).html();

                if (!input.files) {
                    return;
                }

                var existingFileCount = $(targetSelector).children().length; // Existing previews

                var newFiles = Array.from(input.files); // Convert FileList to array for easier processing
                var validFiles = [];
                var dataTransfer = new DataTransfer();

                // Validate files before appending
                newFiles.forEach(file => {
                    // Check file type
                    if (!ALLOWED_TYPES.includes(file.type)) {
                        // alert('Invalid file type. Please upload PDF files only.');
                        alert('Invalid file type. Please upload PDF files only.');
                        return; // Don't increment fileCount here
                    }

                    // Check file size
                    if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                        alert('File size exceeds the 2MB limit.');
                        return; // Don't increment fileCount here
                    }

                    // alert(fileCount);
                    // alert(existingFileCount);
                    // alert(validFiles.length);
                    // alert(existingFileCount + validFiles.length + fileCount);
                    // Ensure total files do not exceed the limit
                    if (validFiles.length + fileCount >= MAX_FILE_COUNT) {
                        alert('Max file limit reached.');
                        return; // Don't increment fileCount here
                    }

                    dataTransfer.items.add(file);

                    validFiles.push(file); // Only add to validFiles if passed validation
                });
                input.files = dataTransfer.files;
                // If no valid files, exit
                if (validFiles.length === 0) {
                    return;
                }

                // Append valid files and process them
                validFiles.forEach(file => {
                    var $newFile = $(templateHtml).appendTo(targetSelector);

                    // Process the file (async handling)
                    processFile(file, $newFile).then(() => {
                        existingFileCount++; // Update count only after successful processing

                        // Hide or show the "Add File" button based on the limit
                        if (validFiles.length + fileCount >= MAX_FILE_COUNT) {
                            $('#add-file-btn').hide();
                        } else {
                            $('#add-file-btn').show();
                        }

                        // Increment fileCount only after successful processing
                        fileCount++;
                        //alert('addfile');
                        //alert(fileCount);
                    });
                });
                //processFile(file, $newFile)
                ; // Clear input to allow re-selection of the same files
                $input.val([]);
            }


            // async function processFile(file, $newFile) {
            //     await addFileToNewInput(file, $newFile.find("input")[0]);
            //     await addSrcToPreview(file, $newFile.find("img")[0]);
            //     await setFileDetails(file, $newFile);
            // }

            async function processFile(file, $newFile) {
                let imgElement = $newFile.find("img")[0];
                let fileInfoDiv = $newFile.find(".position-absolute.text-white")[0];

                // Hide file info initially
                fileInfoDiv.style.display = "none";

                // Set preview first
                await addSrcToPreview(file, imgElement);

                // Ensure preview is loaded first
                await new Promise((resolve) => {
                    imgElement.onload = resolve;
                });

                // Now update the file input and details
                await addFileToNewInput(file, $newFile.find("input")[0]);
                await setFileDetails(file, $newFile);

                // Show file info after everything is set
                fileInfoDiv.style.display = "block";
            }


            window.breakIntoSeparateFiles = breakIntoSeparateFiles;
        })(window, jQuery);

        function appendFilePreview() {
            $('#file-input-container').append('<div id="file-list" class="mb-2 d-flex"></div>');
            $('#file-input-container').append(`
            <template id="file-preview">
                <div class="position-relative mr-3 text-center file-preview" style="width: min-content;">
                    <div class="img-thumbnail">
                        <img src="" height="70" />
                        <div class="position-absolute text-white fs-40 text-wrap bg-dark p-1 small w-100" style="bottom: 0; left: 0; opacity: 0.8;">
                            <span class="file-name fs-40">File Name</span> |
                            <span class="file-size fs-40">0 KB</span>
                        </div>
                        <button style="top: 5px; right: 5px;" class="btn btn-sm btn-danger position-absolute" onclick="removeFilePreview(this)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input class="d-none" multiple="multiple" type="file" name="fileupload[]">
                </div>
            </template>`);
        }

        /***************************************************** File - upload template ******************************************************************* */


        /*************************************************  Ckeditor  *********************************************/


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
                        'insertTable',
                        '|',

                    ],
                    shouldNotGroupWhenFull: true
                },
                placeholder: 'Welcome to CAMS... ',
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
                    'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange', 'uploadImage'
                ]
            })
            .then(e => {
                editor = e;
            })
            .catch(error => {
                console.error(error);
            });

        /*************************************************  Ckeditor  *********************************************/



        ////////////////////////////////////// Search Audit Slip Number //////////////////////////////////////

        $(".search-chat").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".chat-users li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        ////////////////////////////////////// Search Audit Slip Number //////////////////////////////////////






        function getSlipBasedOnfilter(filtervalue) {
            $('#filter').val(filtervalue);
            getauditslip('', '', 'fetch', 'Y', filtervalue);
        }

        $(document).ready(function() {

            getauditslip('', '', 'fetch', 'Y', $('#filter').val());

        });




        function enable_liability(selectedOption) {
            if (selectedOption === 'Y') {
                // Show the textbox when "Yes" is selected
                // $("#" + liablilitynamedivid).show(); //liabilityname_div
                // $("#" + liablilitygpfdivid).show(); // liabilitygpfno_div
                $('#dynamicRowsContainer').show();
                const container = document.getElementById('dynamicRowsContainer');
                container.innerHTML = ''; // Clear current rows
                rowCount = 0; // Reset row count
                // addRow(param); // Add the first row based on the selected parameter
                addNewWorkRow(event, 'entry', '', '', '', '', '', '', '');

            } else {
                $('#dynamicRowsContainer').hide();
                // Hide the textbox when "No" is selected
                // $("#" + liablilitynamedivid).hide(); //liabilityname_div
                // $("#" + liablilitygpfdivid).hide(); //liabilitygpfno_div
            }
        }


        /*********************************************** Jqury Form Validation *******************************************/

        $("#auditslip").validate({
            rules: {
                majorobjectioncode: {
                    required: true,
                },
                minorobjectioncode: {
                    required: true
                },
                slipno: {
                    required: true
                },
                slipdetails: {
                    required: true
                },
                auditorremarks: {
                    required: true
                },

                severityid: {
                    required: true
                },
                liability: {
                    required: true
                },
                liabilityname: {
                    required: true
                },
                liabilitygpfno: {
                    required: true
                },
                liabilitydesig: {
                    required: true
                },
                scheme: {
                    required: true
                },
                serious: {
                    required: true
                },
                category: {
                    required: true
                },
                subcategory: {
                    required: true
                },
                schemename: {
                    required: true
                },
            },
            messages: {
                majorobjectioncode: {
                    required: "Select a major objection type",
                    // number: "Please enter a valid number",
                    // minlength: "Slip number must be at least 5 digits long"
                },
                minorobjectioncode: {
                    required: "Select a minor objection type",
                },
                slipno: {
                    required: "Enter a slip number",
                },
                slipdetails: {
                    required: "Enter a slipdetails",
                },
                auditorremarks: {
                    required: "Enter a auditor remarks",
                },

                severityid: {
                    required: "Select Severity",
                },
                liability: {
                    required: "Select liablility",
                },
                liabilityname: {
                    required: "Select liability name",
                },
                "fileupload[]": {
                    required: "Please select a file.",
                    // acceptFile: "Only image files (.jpg, .jpeg, .png), PDF, or DOCX are allowed."
                },
                liabilitygpfno: {
                    required: "Select liability GPF/CPF no",
                },
                liabilitydesig: {
                    required: "Select liability Designation",
                },
                serious: {
                    required: "Select Serious",
                },
                serious: {
                    required: "Select Scheme",
                },
                category: {
                    required: "Select Category",
                },
                subcategory: {
                    required: "Select Subcategory",
                },
                schemename: {
                    required: "Select Scheme Name",
                },
            }
        });


        /*********************************************** Jqury Form Validation *******************************************/


        /*********************************************** Insert,update,finalise,reset *******************************************/

        $("#approvebtn").on("click", function() {

            event.preventDefault();
            // Trigger the form validation
            if ($("#auditslip").valid()) {
                // document.getElementById("process_button").onclick = function() {
                //     createslip('Y','','fresh')
                // };
                if (!(editor.getData())) {
                    passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return;
                }
                removeAllEventListeners(document.getElementById("process_button"));

                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();

                    // If validation passes, manually close the modal
                    $('#confirmation_alert').modal('hide');

                    createslip('Y', '', 'fresh')
                });


                if (('<?php echo $teamhead; ?>' == 'Y')) alert_content =
                    'Are you sure to forward the slip details to the institution?';
                if (('<?php echo $teamhead; ?>' == 'N')) alert_content =
                    'Are you sure to forward the slip details to team head?';


                // Show confirmation alert
                passing_alert_value('Confirmation', alert_content, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
            } else {}
        });

        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();
            if ($("#auditslip").valid()) {
                if (!(editor.getData())) {
                    passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return;
                } else {
                    createslip('N', '', 'fresh')

                }
            } else {

            }

        });



        // $("#memberReplyfwd").on("click", function(event) {
        //     event.preventDefault();
        //     const selectedRadio = document.querySelector('input[name="verifiedClarificationRadiobtn"]:checked');
        //     if (selectedRadio)
        //     {
        //         selectedValue = selectedRadio.value;
        //         let alert_content = `

    //             Are you sure you want to forward the slip details to the team head?
    //         `;
        //         passing_alert_value(
        //             'Confirmation',
        //             alert_content,
        //             'confirmation_alert',
        //             'alert_header',
        //             'alert_body',
        //             'forward_alert'
        //         );

        //         $('#confirmation_alert').modal({
        //             backdrop: 'static',
        //             keyboard: false
        //         });
        //         // $('#process_button').removeAttr('data-bs-dismiss');

        //         $('#process_button').on('click', function(event) {
        //             createslip('Y',selectedValue, 'memeberrejoinder')
        //         });

        //     } else {
        //         passing_alert_value(
        //             'Alert',
        //             'Select the Verified or Need clarification radio button',
        //             'confirmation_alert',
        //             'alert_header',
        //             'alert_body',
        //             'confirmation_alert'
        //         );
        //     }


        // });

        $("#memberReplyfwd").on("click", function(event) {
            event.preventDefault();
            const selectedRadio = document.querySelector('input[name="verifiedClarificationRadiobtn"]:checked');

            if (selectedRadio) {
                const selectedValue = selectedRadio.value;
                let alert_content = `
                    Are you sure you want to forward the slip details to the team head?
                `;
                passing_alert_value(
                    'Confirmation',
                    alert_content,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                $('#confirmation_alert').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                removeAllEventListeners(document.getElementById("process_button"));

                // Attach the click event handler to #process_button inside the modal only once
                $('#process_button').off('click').on('click', function(event) {
                    // Prevent the default behavior if necessary (optional)
                    event.preventDefault();
                    if (!(editor.getData())) {
                        passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        return;
                    }

                    // Execute the function to create the slip
                    createslip('Y', selectedValue, 'memeberrejoinder');

                    // Close the modal after the action
                    $('#confirmation_alert').modal('hide');
                });

            } else {
                passing_alert_value(
                    'Alert',
                    'Select the Verified or Need clarification radio button',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        });



        $("#rejoinderSlip").on("click", function(event) {
            event.preventDefault();
            if ($("#auditslip").valid()) {
                const selectedRadio = document.querySelector('input[name="verifiedClarificationRadiobtn"]:checked');
                if (selectedRadio) {
                    let alert_content =
                        `<p>Are you sure to forward the slip details to the institution for rejoinder?</p> `;

                    $('#process_button').removeAttr('data-bs-dismiss');
                    if (!(editor.getData())) {
                        passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        return;
                    }

                    removeAllEventListeners(document.getElementById("process_button"));

                    $('#process_button').off('click').on('click', function(event) {
                        event.preventDefault();

                        // If validation passes, manually close the modal
                        $('#confirmation_alert').modal('hide');


                        createslip('Y', '', 'rejoinder')
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
                    passing_alert_value(
                        'Alert',
                        'Select the Verified option button',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            } else {

            }
        });

        $('#convertSlip').on("click", function() {
            event.preventDefault();
            if ($("#auditslip").valid()) {

                const selectedRadio = document.querySelector('input[name="verifiedClarificationRadiobtn"]:checked');

                if (selectedRadio) {


                    let alert_content = `

                    <p>Are you sure to Convert to para?</p>`;

                    $('#process_button').removeAttr('data-bs-dismiss');

                    // Handle the 'process_button' click
                    // $('#process_button').on('click', function(event) {
                    //     // Prevent the default action of the button (which would close the modal)
                    //     event.preventDefault();


                    //     // If validation passes, manually close the modal
                    //     $('#confirmation_alert').modal('hide');

                    //     createslip('Y','', 'converttopara')

                    // });

                    if (!(editor.getData())) {
                        passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        return;
                    }

                    $('#process_button').off('click').on('click', function(event) {
                        event.preventDefault();

                        // If validation passes, manually close the modal
                        $('#confirmation_alert').modal('hide');

                        createslip('Y', '', 'converttopara')
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
                    passing_alert_value(
                        'Alert',
                        'Select the Verified option button',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            } else {

            }
        });


        $('#dropSlip').on("click", function() {

            event.preventDefault();
            if ($("#auditslip").valid()) {

                const selectedRadio = document.querySelector('input[name="verifiedClarificationRadiobtn"]:checked');

                if (selectedRadio) {
                    let alert_content = `
                    Are you sure to Drop?`;

                    $('#process_button').removeAttr('data-bs-dismiss');
                    if (!(editor.getData())) {
                        passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        return;
                    }
                    removeAllEventListeners(document.getElementById("process_button"));

                    // Handle the 'process_button' click
                    $('#process_button').off('click').on('click', function(event) {
                        // Prevent the default action of the button (which would close the modal)
                        event.preventDefault();

                        // If validation passes, manually close the modal
                        $('#confirmation_alert').modal('hide');

                        createslip('Y', '', 'drop')
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
                    passing_alert_value(
                        'Alert',
                        'Select the Verified option button',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            } else {

            }
        });






        function createslip(finalise, rejoindersuggestion, action) {

            var requestSent = false;
            fileCount = 0;

            if (!requestSent) {
                requestSent = true; // Set flag to true so the request won't be sent again

                // Disable the button so the user can't click it again
                $('#buttonaction').attr('disabled', true);
                $('#process_button').attr('disabled', true);


                var formData = new FormData($('#auditslip')[0]); // Serialize form data, including files

                let checkboxValues = [];

                // Loop through each checkbox with the name 'activestatus[]'
                $('input[name="activestatus[]"]').each(function() {
                    // If the checkbox is checked, push '1', otherwise push '0'
                    checkboxValues.push(this.checked ? '1' : '0');
                });

                // Append the checkbox values to formData
                checkboxValues.forEach((value, index) => {
                    formData.append(`activestatus[${index}]`, value); // Append each value with its index
                });

                $(".work-row").each(function(index) {
                    let rowId = $(this).attr('id').replace("row", ""); // Extract row number

                    formData.append(`liabilityid[]`, $(`#liabilityid${rowId}`).val());
                    formData.append(`notype[]`, $(`select[name='notype${rowId}']`).val());
                    formData.append(`name[]`, $(`#name${rowId}`).val());
                    formData.append(`gpfno[]`, $(`#gpfno${rowId}`).val());
                    formData.append(`designation[]`, $(`#designation${rowId}`).val());
                    formData.append(`amount[]`, $(`input[name='amount${rowId}']`).val());

                    // Handling checkbox values properly
                    // let isChecked = $(`input[name="activestatus[]"]`).eq(index).is(":checked") ? '1' : '0';
                    // formData.append(`activestatus[]`, isChecked);
                });

                // Append the remarks editor content to formData
                formData.append('remarks', editor.getData());
                formData.append('finaliseflag', finalise);
                formData.append('teamheadid', <?php echo $teamheadid; ?>);
                formData.append('teamhead', '<?php echo $teamhead; ?>');
                formData.append('rejoindersuggestion', (rejoindersuggestion === 'Y' ? 'R' : 'N'));
                formData.append('actionfor', action);
                formData.append('spilloverflag', '<?php echo $spilloverflag; ?>')
                formData.append('instid', '<?php echo $instid; ?>')
                formData.append('catcode', '<?php echo $catcode; ?>');
                formData.append('subcatid', '<?php echo $subcatid; ?>')
                formData.append('annadhanam_only', '<?php echo $annadhanam_only; ?>')



                if (action != 'fresh') {
                    formData.append('majorobjectioncode', $('#majorobjectioncode').val());
                    formData.append('minorobjectioncode', $('#minorobjectioncode').val());
                }


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        // 'Content-Type': 'application/x-www-form-urlencoded',
                    }
                });

                $.ajax({
                    url: '/audislip_insert', // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    processData: false, // Disable automatic data processing
                    contentType: false, // Let jQuery handle the content type for FormData
                    success: function(response) {
                        if (response.success) {
                            // reset_form(); // Reset the form after successful submission


                            // get_severity('', 'severityid');
                            passing_alert_value('Confirmation', response.message,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');


                            // table.ajax.reload(); // Reload the table with the new data
                            // if ($('#action').val() == 'insert') {
                            //     getauditslip(response.data, 'fetch', 'Y', $('#filter').val());
                            // } else
                            //     getauditslip(response.data, 'fetch', 'N', $('#filter').val());


                            getauditslip(response.data['slid'], response.data['auditslipnumber'],
                                'fetchwithdata', 'Y', $('#filter').val());



                        } else if (response.error) {
                            // Handle errors if needed
                            console.log(response.error);
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
                    complete: function() {
                        // Optionally, you can re-enable the button here if desired
                        $('#buttonaction').removeAttr('disabled');
                        $('#process_button').removeAttr('disabled');
                    }
                });
            } else {
                console.log('Request already sent.');
            }
        }




        function reset_form() {
            if ('<?php echo $teamhead; ?>' == 'Y') {
                buttonname = @json($approvebtn);
            } else {
                buttonname = @json($finalizebtn);
            }

            // changeButtonAction('auditslip', 'action', 'buttonaction', 'approvebtn', 'display_error',
            //@json($savedraftbtn), buttonname, 'insert')
            change_button_as_insert('auditslip', 'action', 'buttonaction', 'display_error', '');

            // Detect the selected language (default to English if not set)
            const lang = getLanguage('');

            // Set the default option based on language
            const optionText = lang === 'ta' ? 'துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்' : 'Select sub objection';

            $("#minorobjectioncode")
                .empty()
                .append(
                    `<option value='' data-name-en='Select sub objection' data-name-ta='துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்'>${optionText}</option>`
                );

            $('#severityid').val('');

            $('input[name="liability"][value="N"]').prop('checked', true);
            $('#dynamicRowsContainer').empty();


            $('#liabilityname_div').hide();
            $('#liabilitygpfno_div').hide();
            editor.setData('');
            appendFilePreview();

            $('#deactive_fileid').val('');
            $('#active_fileid').val('');

            $('#fileuploadid').val('');

            $('#file-input-container').hide()
            $('#file-input-container').empty();
            $('#add-file-btn').hide()

            //clear upload filecontainer
            $('#file-list-container').empty();
            $('#file-list-container').hide();

        }

        /*********************************************** Insert,update,finalise,reset *******************************************/




        /*********************************************** Fetch Data *******************************************/

        function getauditslip(slipid, slipnumber, action, createnewone, filter) {
            $('#loader').removeClass('d-none');

            fixslipid = slipid;
            reset_form();

            if (action == 'fetch') slipid = '';

            $.ajax({
                url: '/getauditslip', // Your API route to get user details
                method: 'POST',
                data: {
                    filter: filter,
                    auditslipid: slipid,
                    auditscheduleid: <?php echo $auditscheduleid; ?>,
                    action: action,
                    spilloverflag: '<?php echo $spilloverflag; ?>',
                    instid: '<?php echo $instid; ?>',
                    'teamhead': '<?php echo $teamhead; ?>'
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        // alert(response.data.historydel.length);
                        if ((((action == 'fetch') || (action == 'fetchwithdata')) && response.data.auditDetails
                                .length > 0) || ((action == 'edit') && (response.data.historydel.length > 0))) {
                            if ((action == 'fetch') || (action == 'fetchwithdata') && response.data.auditDetails
                                .length > 0) {
                                $('#seriesno').val(1);
                                const chatUsersList = document.querySelector(".chat-users");
                                chatUsersList.innerHTML = '';
                                firstItem_auditeedel = '';

                                if (fixslipid == '') //No Fixid provide
                                {
                                    seriesnumber = Number($('#seriesno').val());
                                    // firstItem = response.data.auditDetails[0];
                                    fixarrow = seriesnumber;
                                }

                                response.data.auditDetails.forEach(function(item) {
                                    addSlipNumber(item.tempslipnumber, item.encrypted_auditslipid, item
                                        .slipby, item.processcode, response.data.auditDetails
                                        .length, item.apicode
                                    );
                                    if (fixslipid) //If fix slipid present
                                    {
                                        if (slipnumber == item
                                            .tempslipnumber
                                        ) //compare fixslipid with item.tempslipnumber
                                        {
                                            fixarrow = $('#seriesno').val() - 1;
                                            // firstItem = item;
                                        }
                                    }
                                });

                                // alert('hi');


                                $('.hstack').removeClass("active_div");
                                $('#arrow_' + fixarrow).css("visibility", "visible");

                                $('#' + fixarrow).addClass("active_div");
                                // $('#buttonaction').hide();
                                // $('#approvebtn').hide();
                                // $('#auditorAccordionsContainer').hide();

                            } else if (action == 'edit') {
                                firstItem = response.data.historydel[0];
                            } else {}



                            firstItem = response.data.historydel[0];

                            // alert(firstItem);
                            if (firstItem) {


                                if (!(((firstItem.processcode == 'E') && (firstItem.createdby ==
                                            '<?php echo $sessionuserid; ?>')) ||
                                        (('<?php echo $teamhead; ?>' == 'Y') && (firstItem.processcode == 'T')))) {
                                    historydel = response.data.historydel;
                                    showremarks(historydel)
                                }
                                fix_formfield_values(firstItem, fixarrow);
                            }




                            if ('<?php echo $api; ?>' == 'Y') {

                            } else {

                                if ((createnewone == 'Y') && ((filter == 'P')) || (filter == 'A') && ((action ==
                                        'fetch') || (action == 'fetchwithdata'))) {
                                    addSlipNumber('', '', '', '', response.data.auditDetails.length);
                                }
                            }
                        } else {
                            for_newslip_resetform('');

                            const chatUsersList = document.querySelector(".chat-users");
                            chatUsersList.innerHTML = '';
                            seriesnumber = Number($('#seriesno').val());
                            $('#currentslipnumber').val($('#autoslipnumber').val())
                            appendFilePreview();
                            addSlipNumber('', '', '', '', response.data.auditDetails.length);
                            $('#arrow_' + seriesnumber).css("visibility", "visible");
                            $('#' + seriesnumber).addClass("active_div");

                        }
                    } else {
                        alert('User not found');
                    }
                },
                complete: function() {

                    $('#loader').addClass('d-none');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

        /*********************************************** Fetch Data *******************************************/


        /*********************************************** Show Remarks in accordion *******************************************/

        // function showremarks(responseData) {
        //     $('#auditorAccordionsContainer').show();
        //     let container = document.getElementById("auditorAccordionsContainer");
        //     container.innerHTML = ""; // Clear existing content

        //     let editorIds = []; // Store CKEditor IDs
        //     let lastIndex = responseData.length - 1; // Get the last index


        //     responseData.forEach((data, index) => {
        //         let accordionId = `auditorAccordion${index}`;
        //         let collapseId = `collapse${index}`;
        //         let headerId = `heading${index}`;
        //         let editorId = `editor${index}`;
        //         editorIds.push(editorId); // Store editor ID for CKEditor initialization


        //         let fileList = "<li>No attachments</li>"; // Default message if no files

        //         if (data.file_details) {
        //             fileList = Object.keys(data)
        //                 .filter(key => key.startsWith("file_details")) // Filter only file keys
        //                 .map(key => {
        //                     return data[key]
        //                         .split(",") // Split multiple files
        //                         .map(file => {
        //                             let parts = file.split('-');
        //                             if (parts.length < 2) return ""; // Skip invalid entries

        //                             let [name, path] = parts; // Extract only necessary parts
        //                             return `<li><a href="/storage/${path}" target="_blank" style="color:black">${name}</a></li>`;
        //                         })
        //                         .join(""); // Join all file links
        //                 })
        //                 .join("") || "<li>No attachments</li>"; // Default message if no files
        //         }




        //         // Dynamically get the value based on forwardedBy
        //         var values = {
        //             A: @json($A),
        //             I: @json($I)
        //         };
        //         var forwardedby = values[data.forwardedbyusertypecode] || "Unknown"; // Handle missing values
        //         var rejoinderstatus = data.rejoinderstatus;
        //         var rejoindercycle = data.rejoindercycle;
        //         var forwardby = data.username;
        //         var forwardedon = ChangeDateFormat(data.forwardedon)


        //         let isLast = index === lastIndex; // Check if it's the last item

        //         // Create accordion element
        //         let accordion = document.createElement("div");
        //         accordion.classList.add("accordion", "my-2");
        //         accordion.id = accordionId;
        //         accordion.innerHTML = `
    //             <div class="accordion-item">
    //                 <h2 class="accordion-header" id="${headerId}">
    //                     <button class="hrem accordion-button ${isLast ? "" : "collapsed"}
    //                      ${forwardedby == 'Auditee'?"bg-success-subtle":"bg-primary-subtle"}"
    //                             type="button"
    //                             data-bs-toggle="collapse"
    //                             data-bs-target="#${collapseId}"
    //                             aria-expanded="${isLast}"
    //                             aria-controls="${collapseId}">
    //                       <b> ${forwardby}</b> <small>( ${forwardedon}</small> )
    //                         ${rejoinderstatus === 'Y' ? `# Rejoinder ${rejoindercycle}` : ""}
    //                     </button>
    //                 </h2>
    //                 <div id="${collapseId}" class="accordion-collapse collapse ${isLast ? "show" : ""}" aria-labelledby="${headerId}">
    //                     <div class="accordion-body">
    //                         <div class="row">
    //                             <div class="col-md-8">
    //                                 <label><strong>Remarks:</strong></label>
    //                                 <div class="editor-container">
    //                                     <textarea id="${editorId}">${data.remarks || "No remarks provided"}</textarea>
    //                                 </div>
    //                             </div>
    //                             <div class="col-md-4">
    //                                 <label><strong>Attachments:</strong></label>
    //                                 <ul class="file-list" >${fileList}</ul>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>
    //             </div>
    //         `;

        //         container.appendChild(accordion); // Append dynamically created accordion
        //     });

        //     // Initialize CKEditor after elements are created
        //     initializeEditors(editorIds);
        // }


        function showremarks(responseData) {
            $('#auditorAccordionsContainer').show();
            let container = document.getElementById("auditorAccordionsContainer");
            container.innerHTML = ""; // Clear existing content

            let editorIds = []; // Store CKEditor IDs
            let lastIndex = responseData.length - 1; // Get the last index


            responseData.forEach((data, index) => {
                let accordionId = `auditorAccordion${index}`;
                let collapseId = `collapse${index}`;
                let headerId = `heading${index}`;
                let editorId = `editor${index}`;
                editorIds.push(editorId); // Store editor ID for CKEditor initialization


                let fileList = "<li>No attachments</li>"; // Default message if no files

                if (data.file_details) {
                    fileList = Object.keys(data)
                        .filter(key => key.startsWith("file_details")) // Filter only file keys
                        .map(key => {
                            return data[key]
                                .split(",") // Split multiple files
                                .map(file => {
                                    let parts = file.split('-');
                                    if (parts.length < 2) return ""; // Skip invalid entries

                                    let [name, path] = parts; // Extract only necessary parts
                                    return `<li><a href="/${path}" target="_blank" style="color:black">${name}</a></li>`;
                                })
                                .join(""); // Join all file links
                        })
                        .join("") || "<li>No attachments</li>"; // Default message if no files
                }




                // Dynamically get the value based on forwardedBy
                var values = {
                    A: @json($A),
                    I: @json($I)
                };
                var forwardedby = values[data.forwardedbyusertypecode] || "Unknown"; // Handle missing values
                var rejoinderstatus = data.tr_rejoinderstatus;
                var rejoindercycle = data.tr_rejoindercycle;
                var forwardby = data.username;
                var forwardedon = ChangeDateFormat(data.forwardedon)


                let isLast = index === lastIndex; // Check if it's the last item

                // Create accordion element
                let accordion = document.createElement("div");
                accordion.classList.add("accordion", "my-2");
                accordion.id = accordionId;

                accordion.innerHTML = `
    <div class="accordion-item">
        <h2 class="accordion-header" id="${headerId}">
            <button style="height: 50px;" class=" accordion-button ${isLast ? "" : "collapsed"}
            ${forwardedby == 'Auditee' ? "bg-success-subtle" : "bg-primary-subtle"}"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#${collapseId}"
                aria-expanded="${isLast}"
                aria-controls="${collapseId}">

                <!-- ✅ Parent Flex Container -->
                <div class="d-flex flex-column w-100" >

                    <!-- ✅ Desktop View: Name → Date → Rejoinder -->
                    <div class="d-none d-md-flex align-items-center w-100">
                        <div class="text-truncate">
                            <b>${forwardby}</b>
                        </div>
                        <div class="text-muted small ms-2" style="margin-left:20px;">
                            (${forwardedon})
                        </div>
                        <div class="text-danger text-end ms-auto me-3">
                            ${rejoinderstatus === 'Y' ? `# Rejoinder ${rejoindercycle}` : ""}
                        </div>
                    </div>


                    <!-- ✅ Mobile View: Name → Rejoinder -->
                    <div class="d-flex flex-row align-items-center justify-content-between d-md-none" style="margin-left: -1.60rem !important;">
                        <div class="text-start">
                            <b>${forwardby}</b>
                        </div>
                        <div class="text-danger text-end me-2">
                            ${rejoinderstatus === 'Y' ? `# Rejoinder ${rejoindercycle}` : ""}
                        </div>
                    </div>


                    <!-- ✅ Mobile View: Date (below Name + Rejoinder) -->
                    <div class="text-muted small mt-1 d-md-none" style="margin-left: -1.60rem !important;">
                        (${forwardedon})
                    </div>
                </div>
            </button>
        </h2>
        <div id="${collapseId}" class="accordion-collapse collapse ${isLast ? "show" : ""}" aria-labelledby="${headerId}">
            <div class="accordion-body">
                <div class="row">
                    <div class="col-md-8">
                        <label><strong>Remarks:</strong></label>
                        <div class="editor-container">
                            <textarea id="${editorId}">${data.historyremarks || "No remarks provided"}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label><strong>Attachments:</strong></label>
                        <ul class="file-list">${fileList}</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;



                container.appendChild(accordion); // Append dynamically created accordion
            });

            // Initialize CKEditor after elements are created
            initializeEditors(editorIds);
        }






        // function initializeEditors(editorIds)
        // {
        //     if (!window.CKEDITOR || !window.CKEDITOR.ClassicEditor) {
        //         setTimeout(() => initializeEditors(editorIds), 100); // Retry if CKEditor is not yet loaded
        //         return;
        //     }

        //     editorIds.forEach(id => {
        //         let textarea = document.getElementById(id);
        //         if (textarea) {
        //             CKEDITOR.ClassicEditor.create(textarea, {
        //                 toolbar: {
        //                     items: [
        //                         'selectAll', '|',
        //                         // 'findAndReplace', 'selectAll', '|',
        //                         // 'heading', '|',
        //                         // 'bold', 'italic', 'underline', '|',
        //                         // 'numberedList', '|',
        //                         // 'outdent', 'indent', '|',
        //                         // 'undo', 'redo',
        //                         // 'fontSize', 'fontFamily', '|',
        //                         // 'alignment', '|',
        //                         // 'uploadImage', 'insertTable',
        //                         // '|',
        //                     ],
        //                     shouldNotGroupWhenFull: true
        //                 },
        //                 placeholder: 'Write Your Audit Observation here',
        //                 fontFamily: {
        //                     options: [
        //                         'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
        //                         'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
        //                         'Tahoma, Geneva, sans-serif',
        //                         'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif',
        //                         'Verdana, Geneva, sans-serif'
        //                     ],
        //                     supportAllValues: true
        //                 },
        //                 fontSize: {
        //                     options: [10, 12, 14, 'default', 18, 20, 22],
        //                     supportAllValues: true
        //                 },
        //                 htmlSupport: {
        //                     allow: [{
        //                         name: /.*/,
        //                         attributes: true,
        //                         classes: true,
        //                         styles: true
        //                     }]
        //                 },
        //                 link: {
        //                     decorators: {
        //                         addTargetToExternalLinks: true,
        //                         defaultProtocol: 'https://',
        //                         toggleDownloadable: {
        //                             mode: 'manual',
        //                             label: 'Downloadable',
        //                             attributes: {
        //                                 download: 'file'
        //                             }
        //                         }
        //                     }
        //                 },
        //                 removePlugins: [
        //                     'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
        //                     'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
        //                     'RealTimeCollaborativeRevisionHistory',
        //                     'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
        //                     'WProofreader',
        //                     'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
        //                     'PasteFromOfficeEnhanced', 'CaseChange'
        //                 ]
        //             })
        //             .then(editor => {
        //                 editor.enableReadOnlyMode('initial'); // Read-only mode
        //             })
        //             .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
        //         }
        //     });
        // }

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
                            editable.style.maxHeight = '300px'; // Set max height for the editable area
                            editable.style.overflowY = 'auto'; // Enable vertical scrolling
                        })
                        .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
                }
            });
        }


        /*********************************************** Show Remarks in accordion *******************************************/



        /*********************************************** Get Main,sub Objections *******************************************/

        function getObjectionBasedOnSlip(slipid, selectedobjectionid, sessionuserid, majorobjectiontextboxid) {
            const lang = getLanguage('');

            const optionText = lang === 'ta' ? 'முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்' : 'Select Main objection';


            const defaultOption =
                "<option value=''  data-name-en='Select Main objection' data-name-ta='முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்' >" +
                optionText + "</option>";
            const $dropdown = $("#" + majorobjectiontextboxid);

            // Clear the dropdown and set the default option
            $dropdown.html(defaultOption);


            $.ajax({
                url: '/getObjectionBasedOnSlip',
                type: 'POST',
                data: {
                    createdby: sessionuserid,
                    financialyearcode: '<?php echo $financialyearcode; ?>',
                    datafromapi: '<?php echo $api; ?>',

                    auditscheduleid: <?php echo $auditscheduleid; ?>, // Ensure this is defined properly in PHP
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    appendFilePreview();
                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;

                        response.data.forEach(({
                            mainobjectionid,
                            objectionename,
                            objectiontname
                        }) => {
                            const isSelected = selectedobjectionid === mainobjectionid ? "selected" :
                                "";
                            const displayName = lang === "ta" ? objectiontname :
                                objectionename; // Choose name based on selected language
                            options += `<option value="${mainobjectionid}" ${isSelected}
                                            data-name-en="${objectionename}"
                                            data-name-ta="${objectiontname}">
                                            ${displayName}
                                        </option>`;
                        });

                        $dropdown.html(options); // Append options to dropdown
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching objections:", xhr);
                }
            });
        }

        function getminorobjection(mainobjectioncode, selectedsubobjectionid, textboxid, maintextboxid) {
            $('#loader').removeClass('d-none');

            appendFilePreview();
            lang = getLanguage('');

            const optionText = lang === 'ta' ? 'துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்' : 'Select sub objection';




            const defaultOption =
                "<option value='' data-name-en='Select sub objection' data-name-ta='துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்'>" +
                optionText + "</option>";
            subObjectionsData = {};

            mainobjectioncode = mainobjectioncode || $('#' + maintextboxid).val();
            lang = getLanguage()

            const $dropdown = $("#" + textboxid);
            $dropdown.html(defaultOption);
            let options = defaultOption;

            console.log('<?php echo $financialyearcode; ?>');

            if (mainobjectioncode) {
                $.ajax({
                    url: '/getsubobjection',
                    method: 'POST',
                    data: {
                        mainobjectioncode: mainobjectioncode,
                        catcode: '<?php echo $catcode; ?>',
                        subcatid: '<?php echo $subcatid; ?>',
                        financialyearcode: '<?php echo $financialyearcode; ?>',
                        annadhanam_only: '<?php echo $annadhanam_only; ?>',
                        datafromapi: '<?php echo $api; ?>'

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            response.data.forEach(({
                                subobjectionid,
                                subobjectionename,
                                subobjectiontname
                            }) => {
                                const isSelected = selectedsubobjectionid === subobjectionid ?
                                    "selected" : "";
                                const displayName = lang === "ta" ? subobjectiontname :
                                    subobjectionename; // Choose name based on selected language
                                options += `<option value="${subobjectionid}" ${isSelected}
                                            data-name-en="${subobjectionename}"
                                            data-name-ta="${subobjectiontname}">
                                            ${displayName}
                                        </option>`;
                            });
                        }
                        $dropdown.html(options);
                    },
                    complete: function() {

                        $('#loader').addClass('d-none');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        }


        /*********************************************** Get Main,sub Objections *******************************************/


        $('#translate').change(function() {
            if ('<?php echo $teamhead; ?>' == 'Y') {
                buttonname = @json($approvebtn);
            } else {
                buttonname = @json($finalizebtn);
            }
            // changeButtonText('action', 'buttonaction', 'approvebtn', @json($savedraftbtn),
            //     @json($updatebtn), buttonname);

            // change_button_as_update('auditslip', 'action', 'buttonaction', 'display_error', '', '');

        });

        function showforwardbtn() {
            // changeButtonAction('auditslip', 'action', 'memberReplyfwd', '', 'display_error', @json($forwardbtn),
            //     '', 'forward')
            change_button_as_update('auditslip', 'action', 'buttonaction', 'display_error', '', '');



            $('#finalisebtn').hide();
            $('#freshformbtn').hide();
            $('#forwardbtn').show();
        }


        function showfinalisebtn() {
            $('#action').val('update');
            $('#finalisebtn').show();
            $('#freshformbtn').hide();
            $('#forwardbtn').hide();
        }

        function showfreshformbtn() {
            $('#finalisebtn').hide();
            $('#freshformbtn').show();
            $('#forwardbtn').hide();
        }


        function fix_formfield_values(firstItem, seriesno) {

            // alert('fix');


            rowCount = 0;


            $('#dynamicRowsContainer').empty();

            if (!firstItem.encrypted_auditslipid) return;


            if (firstItem.processelname) $('#processname').html('Status : ' + firstItem.processelname);
            else $('#processname').html('')

            if (firstItem.mainslipnumber) $('#mainslipnumber').html('#' + firstItem.mainslipnumber);
            else $('#mainslipnumber').html('')

            if (firstItem.createdby == '<?php echo $sessionuserid; ?>') initiatedby = 'Initiated By Self';
            else initiatedby = `Initiated By Member ( ` + firstItem.createdbyusername + ` )`

            $('#forwardedby').html(initiatedby);

            if ('<?php echo $teamhead; ?>' == 'Y') {
                buttonname = @json($approvebtn);
            } else {
                buttonname = @json($finalizebtn);
            }


            // $('#auditorAccordionsContainer').show()


            if ((firstItem.processcode == 'E') || (('<?php echo $teamhead; ?>' == 'Y') && (firstItem.processcode == 'T'))) {
                showfreshformbtn()
                // changeButtonAction('auditslip', 'action', 'buttonaction', 'approvebtn', 'display_error',
                //     @json($updatebtn), buttonname, 'update')

                change_button_as_update('auditslip', 'action', 'buttonaction', 'display_error', '', '');

            } else if (('<?php echo $teamhead; ?>' == 'N') && (firstItem.processcode == 'M')) {
                if ('<?php echo $rejoinderlimit; ?>' == firstItem.rejoindercycle) $('#needclarificationbtn').hide();
                else $('#needclarificationbtn').show();
                showforwardbtn();
            } else if (('<?php echo $teamhead; ?>' == 'Y') && (firstItem.processcode == 'R')) {
                if ('<?php echo $rejoinderlimit; ?>' == firstItem.rejoindercycle) $('#rejoinderSlip').hide();
                else $('#rejoinderSlip').show();

                showfinalisebtn() //show drop converto para and rejoinder button
                editor.setData('');
            } else {
                editor.setData(firstItem.remarks);
                showfreshformbtn()
            }

            $('#rejoindercount').val('');
            $('#rejoinderstatus').val('')

            if (firstItem.rejoinderstatus) $('#rejoinderstatus').val(firstItem.rejoinderstatus);
            if (firstItem.rejoindercycle) $('#rejoindercount').val(firstItem.rejoindercycle);
            $('#slipcreatedby').val(firstItem.createdby);
            $('#slipcreatedyear').val(firstItem.financialyear);



            $('#file-list-container').toggle(!!firstItem.auditorfileupload);
            $('#currentslipnumber').val(firstItem.tempslipnumber);
            $('#auditslipid').val(firstItem.encrypted_auditslipid);
            $('#amount_involved').val(firstItem.amtinvolved);

            $('#serious').val(firstItem.irregularitiescode);
            $('#schemename').val(firstItem.auditeeschemecode);
            getcategoryBasedOnSerious(firstItem.irregularitiescode, firstItem.irregularitiescatcode);
            getsubcategoryBasedOnCategory(firstItem.irregularitiescatcode, firstItem.irregularitiessubcatcode);
            $('#auditslipid').val(firstItem.encrypted_auditslipid);



            $('#slipdetails').val(firstItem.slipdetails);
            $('#severityid').val(firstItem.severitycode);
            $('#fileuploadstatus').val('N');

            $('input[name="scheme"][value="' + firstItem.schemastatus + '"]').prop('checked', true);
            // alert(firstItem.schemastatus);


            if (firstItem.schemastatus === 'Y') {

                $('#severityDiv').show();


            } else {
                $('#severityDiv').hide();

            }


            //enable_liability(firstItem.liability, 'liabilityname_div', 'liabilitygpfno_div')
            $('input[name="liability"][value="' + firstItem.liability + '"]').prop('checked', true);
            if (firstItem.liability === 'Y') {

                // alert('if');

                $('#dynamicRowsContainer').show();

                if (firstItem.processcode == 'E') {
                    $liabilityaction = 'entry';
                } else if (((firstItem.processcode == 'T') && ('<?php echo $teamhead; ?>' == 'Y')) ||
                    ((firstItem.processcode == 'R') && ('<?php echo $teamhead; ?>' == 'Y'))) {
                    $liabilityaction = 'edit';
                } else {

                    $liabilityaction = 'view';
                }

                // Assuming you have this string (you can replace this with dynamic data from your backend or input)
                liabilitydel = firstItem.liabilitydel;

                if (liabilitydel) {
                    // Step 1: Split the liabilitydel string by commas
                    var liablityparts = liabilitydel.split(",");
                    liabilityid = '';
                    // Step 2: Iterate over the parts and split each by the dash (-)
                    for (var i = 0; i < liablityparts.length; i++) {
                        var liablity = liablityparts[i].split("-"); // Split by the dash (-)

                        let isLast = (i === liablityparts.length - 1);


                        addNewWorkRow(event, $liabilityaction, liablity[0], liablity[2], liablity[1], liablity[3], liablity[
                            4], liablity[5], isLast, liablity[6]);

                        console.log(liablity);
                        liabilityid += liablity[5] + ',';

                    }
                    if (liabilityid.endsWith(',')) {
                        liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
                    }
                    $('#liabilityid').val(liabilityid);
                }
            } else {
                $('#dynamicRowsContainer').hide()
            }






            if ('<?php echo $teamhead; ?>' == 'N') $('#majorobjectioncode').val(firstItem.mainobjectionid)
            else getObjectionBasedOnSlip('', firstItem.mainobjectionid, firstItem.createdby, 'majorobjectioncode')


            getminorobjection(firstItem.mainobjectionid, firstItem.subobjectionid, 'minorobjectioncode',
                'majorobjectioncode');

            fileviewid = 'file-list-container';
            fileidstore = '';
            fileupload_hiddenid = '';


            if (('<?php echo $teamhead; ?>' == 'Y')) {
                // if ((((firstItem.processcode == 'E') && (firstItem.createdby == '<?php echo $sessionuserid; ?>')) || (firstItem
                //         .processcode == 'T') || (firstItem.processcode == 'R'))) {
                if ((((firstItem.processcode == 'E') && (firstItem.createdby == '<?php echo $sessionuserid; ?>')) ||
                        (firstItem
                            .processcode == 'T') || (firstItem.processcode == 'R')) && (firstItem.apicode == null)) {
                    enableformfields(firstItem.processcode);
                    UploadedFileList_withaction = 'edit';
                    fileidstore = 'Y';
                    fileupload_hiddenid = 'fileuploadid';
                    if (((firstItem.processcode == 'E') && (firstItem.createdby == '<?php echo $sessionuserid; ?>')) || (firstItem
                            .processcode == 'T')) {
                        $('#auditorAccordionsContainer').hide();
                        editor.setData(firstItem.remarks);

                    } else {
                        editor.setData(firstItem.remarks);
                        $('#auditorAccordionsContainer').show();

                    }
                    $('#auditorremarksdiv').show();

                    if (firstItem.auditorfileupload) {
                        files = getfile(firstItem.auditorfileupload)
                        // alert(fileCount);

                        UploadedFileList(files, UploadedFileList_withaction, fileviewid, fileidstore, fileupload_hiddenid)
                        // alert('hi');
                        // alert(fileCount);

                        $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
                    } else {
                        $('#file-input-container').show()
                        $('#add-file-btn').show()

                    }




                } else {
                    disenableformfields();
                    $('#auditorAccordionsContainer').show();
                    $('#auditorremarksdiv').hide();
                }
            }


            if (('<?php echo $teamhead; ?>' == 'N')) {
                if ((((firstItem.processcode == 'E') && (firstItem.createdby == '<?php echo $sessionuserid; ?>')) || (firstItem
                        .processcode == 'M'))) {
                    enableformfields(firstItem.processcode);
                    UploadedFileList_withaction = 'edit';
                    fileidstore = 'Y';
                    fileupload_hiddenid = 'fileuploadid';

                    $('#auditorremarksdiv').show();

                    if (!(firstItem.processcode == 'E')) {
                        $('#auditorAccordionsContainer').show();
                        editor.setData('');
                    } else {
                        editor.setData(firstItem.remarks);
                        $('#auditorAccordionsContainer').hide();
                    }

                    if (firstItem.auditorfileupload) {
                        files = getfile(firstItem.auditorfileupload)
                        // alert(fileCount);

                        UploadedFileList(files, UploadedFileList_withaction, fileviewid, fileidstore, fileupload_hiddenid)

                        $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
                    } else {
                        $('#file-input-container').show()
                        $('#add-file-btn').show()

                    }
                    //$('#file-input-container').show()
                    //$('#add-file-btn').show()
                } else {
                    $('#auditorAccordionsContainer').show();
                    $('#auditorremarksdiv').hide();
                }
            }

            if ('<?php echo $api; ?>' == 'Y') {
                console.log('if');
                // $('#auditorAccordionsContainer').hide()
                $('#finalisebtn').hide();
                $('#freshformbtn').hide();
                $('#forwardbtn').hide();
            } else {
                console.log('else');

            }
        }


        function getfile(filearray) {
            return files = filearray.split(',').map((fileDetail, index) => {
                const [name, path, size, fileuploadid] = fileDetail.split('-');
                return {
                    id: index + 1,
                    name,
                    path,
                    size,
                    fileuploadid
                };
            });
        }


        function disenableformfields() {
            document.getElementById('majorobjectioncode').disabled = true;
            document.getElementById('minorobjectioncode').disabled = true;
            document.getElementById('amount_involved').disabled = true;
            document.getElementById('severityid').disabled = true;
            document.getElementById('slipdetails').disabled = true;
            document.getElementById('serious').disabled = true;
            document.getElementById('category').disabled = true;
            document.getElementById('subcategory').disabled = true;
            document.getElementById('schemename').disabled = true;


            if (editor) editor.enableReadOnlyMode('customLock'); // Provide a unique lock ID

            $('input[name="liability"]').prop('disabled', true);
            $('input[name="scheme"]').prop('disabled', true);

            // document.getElementById('liabilityname').disabled = true;
            // document.getElementById('liabilitygpfno').disabled = true;
            // document.getElementById('liabilitydesig').disabled = true;
            $('#buttonaction').hide();
            $('#approvebtn').hide();
        }

        function enableformfields(processcode) {

            if ((processcode == 'E') || (processcode == 'T')) {
                document.getElementById('majorobjectioncode').disabled = false;
                document.getElementById('minorobjectioncode').disabled = false;
            } else {
                document.getElementById('majorobjectioncode').disabled = true;
                document.getElementById('minorobjectioncode').disabled = true;
            }


            document.getElementById('subcategory').disabled = false;
            document.getElementById('schemename').disabled = false;



            document.getElementById('serious').disabled = false;
            document.getElementById('category').disabled = false;
            document.getElementById('severityid').disabled = false;
            document.getElementById('slipdetails').disabled = false;
            document.getElementById('amount_involved').disabled = false;

            if (editor) editor.disableReadOnlyMode('customLock'); // Use the same lock ID to disable

            $('input[name="liability"]').prop('disabled', false);
            $('input[name="scheme"]').prop('disabled', false);

            // document.getElementById('liabilityname').disabled = false;
            // document.getElementById('liabilitygpfno').disabled = false;
            // document.getElementById('liabilitydesig').disabled = false;

            $('#buttonaction').show();
            $('#approvebtn').show();
        }







        /*********************************************** Automatic Slip Add *******************************************/


        function addSlipNumber(slipNumber, id, slipby, processcode, countofslipnumber, apicode) {

            if ((countofslipnumber == 0) && (('<?php echo $exitmeetdate; ?>') || ('<?php echo $today; ?>' >=
                    '<?php echo $todate_afterworking3days; ?>'))) {
                $('#auditslip').hide();
                $('#nodata_available').show();
            } else {
                $('#nodata_available').hide();
                $('#auditslip').show();
            }

            if (
                (
                    (!slipNumber) &&
                    (
                        ('<?php echo $exitmeetdate; ?>' !== '') ||
                        ('<?php echo $today; ?>' >= '<?php echo $todate_afterworking3days; ?>')
                    )
                )
            ) {
                return;
            }



            if (!slipNumber && (('<?php echo $exitmeetdate; ?>' == '') || ('<?php echo $today; ?>' < '<?php echo $todate_afterworking3days; ?>'))) {
                slipNumber = 'NEW';
            } else {
                $('#nodata_available').hide();
                $('#auditslip').show();
                if (!id) id = '';
            }

            // if (apicode) tempslipnumber = apicode + '' + slipnumberatbox;
            // else tempslipnumber = slipnumberatbox;



            // Get the 'ul' element where the slip numbers are listed
            const chatUsersList = document.querySelector(".chat-users");

            // Create a new 'li' element for the new slip number
            const newListItem = document.createElement("li");

            seriesno = $('#seriesno').val();

            let slipnumberatbox = '';

            if (apicode) {
                slipnumberatbox += apicode + ' ';
            }

            if ('<?php echo $teamhead; ?>' === 'Y' && slipby === 'M') {
                slipnumberatbox += slipby + ' ';
            }

            slipnumberatbox += slipNumber;


            // Add the HTML content for the new 'li'
            newListItem.innerHTML = `
            <div class="hstack ${processcode =='A' ? 'drop_div' :processcode =='X' ? 'contopara_div':''}  p-2 bg-hover-light-black position-relative border-bottom " id="${seriesno}" onclick="setform_basedonslipnumber('${seriesno}')">
            <input type="hidden" id="slipid_${seriesno}" name="slipid" value="${id}">
            <input type="hidden" id="slipnumber_${seriesno}" name="slipnumber_${seriesno}" value='${slipNumber}'>

            <a style="color:black;" href="javascript:void(0)" class="stretched-link"></a>
            <div class="ms-2">
                <a style="color:black;" href="javascript:void(0)">
                    <i class="text-primary ri ri-clipboard-text fs-5"></i>
                </a>
            </div>
            <div class="ms-auto ">
                <h6 class="mb-0 fs-2">${slipnumberatbox}</h6>
            </div>
            <div class="ms-auto fs-2">
                <a style="color:black;" href="javascript:void(0)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"
                        class="icon icon-tabler icons-tabler-filled icon-tabler-arrow-big-right-lines slip-arrow" style="visibility:hidden" id="arrow_${seriesno}">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12.089 3.634a2 2 0 0 0 -1.089 1.78l-.001 2.585l-1.999 .001a1 1 0 0 0 -1 1v6l.007 .117a1 1 0 0 0 .993 .883l1.999 -.001l.001 2.587a2 2 0 0 0 3.414 1.414l6.586 -6.586a2 2 0 0 0 0 -2.828l-6.586 -6.586a2 2 0 0 0 -2.18 -.434l-.145 .068z" />
                        <path d="M3 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
                        <path d="M6 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
                    </svg>
                </a>
            </div>
            </div>`;


            // Append the new 'li' to the list
            chatUsersList.appendChild(newListItem);

            // Increment the slip number and series number
            // slipNumber = slipNumber + 1;
            seriesno = Number($('#seriesno').val()) + 1;
            $('#seriesno').val(seriesno);

            // Flag to check if the click handler has been triggered before
            let clickHandled = false;

        }

        function setform_basedonslipnumber(seriesno) {
            reset_form();

            const clickedId = seriesno; // Get the ID of the clicked element
            currentslipnumber = $('#slipnumber_' + clickedId).val();
            currentslipid = $('#slipid_' + clickedId).val();

            // alert(currentslipid);

            $('#currentslipnumber').val(currentslipnumber);
            $('#auditslipid').val(currentslipid);

            if (currentslipid) {
                getauditslip(currentslipid, '', 'edit', '', $('#filter').val());
            } else {
 
                for_newslip_resetform(clickedId)
            }
if ('<?php echo $api; ?>' == 'Y') {
}
else
{
 $('#finalisebtn').hide();
            $('#freshformbtn').show();
}


           

            $(".slip-arrow").css("visibility", "hidden");
            $('.hstack').removeClass("active_div");
            $('#arrow_' + clickedId).css("visibility", "visible");
            $('#' + clickedId).addClass("active_div");
            appendFilePreview();
        }

        function for_newslip_resetform(clickedId) {
console.log('if');

            fileCount = 0;

            $('#processname').html('')
            $('#processname').html('')
            $('#mainslipnumber').html('')
            $('#rejoindercount').val('');
            $('#rejoinderstatus').val('')
            $('#auditorremarksdiv').show();
            $('#auditorAccordionsContainer').hide();
            $('#forwardedby').html('Initiated By Self')

            $('#severityDiv').hide();



            if ('<?php echo $teamhead; ?>' == 'Y')
                getObjectionBasedOnSlip('', '', '', 'majorobjectioncode')

            enableformfields('E');
            showfreshformbtn()
            $('#file-input-container').show()
            $('#add-file-btn').show()
            $('#fileuploadstatus').val('Y');
            const lang = getLanguage('');

            // Set the default option based on language
            optionText = lang === 'ta' ? 'வகை பெயரைத் தேர்ந்தெடுக்கவும்' : 'Select Category Name';


            $("#category")
                .empty()
                .append(
                    `<option value='' data-name-en="Select Category Name" data-name-ta="வகை பெயரைத் தேர்ந்தெடுக்கவும்">${optionText}</option>`
                );

            optionText = lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select SubCategory ';


            $("#subcategory")
                .empty()
                .append(
                    `<option value='' data-name-en="Select SubCategory" data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">${optionText}</option>`
                );
            if ('<?php echo $api; ?>' == 'Y') {
                console.log('if');
                // $('#auditorAccordionsContainer').hide()
                $('#finalisebtn').hide();
                $('#freshformbtn').hide();
                $('#forwardbtn').hide();
            }
        }

        /*********************************************** Automatic Slip Add *******************************************/



        /**************************************** Fit the upload files, delete upload file in s **********************/
        function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
            const $container = $('#' + containerid).empty();


            files.forEach(file => {
                if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);

                const fileCard = `
            <div class="position-relative align-items-stretch ms-2" ${action === 'edit' ? `id="file-card-${file.fileuploadid}"` : ''}>
                <div class="card ms-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between ms-2">
                            ${action === 'edit' ? `<input type="hidden" id="fileuploadid_${file.fileuploadid}" name="fileuploadid_${file.fileuploadid}" value="${file.fileuploadid}">` : ''}
                            <div class="d-flex">
                                <div class="p-1 bg-primary-subtle rounded me-6 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-file-text text-primary fs-6"></i>
                                </div>
                                <div>
                                    <!-- Use JavaScript template literals to generate the correct URL -->
                                    <a class="fs-3 fw-semibold" style="color:black;" href="/${file.path}" target="_blank">${file.name}</a>
                                </div>
                            </div>
                            ${action === 'edit' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="bg-danger-subtle badge ms-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <span class="fs-5 text-danger fw-semibold mb-0"><i class="ti ti-trash" onclick="deleteFile(${file.fileuploadid}, event)"></i></span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}
                        </div>
                    </div>
                </div>
            </div>`;

                $container.append(fileCard);
                fileCount++;
            });


            if (uploadidstatus == 'Y') {
                if (fileCount < '<?php echo $fileuploadcount; ?>') {
                    $('#add-file-btn').show();
                    $('#file-input-container').show();
                }
            }
            // alert('end');
            // alert(fileCount);
        }

        function removeAllEventListeners(element) {
            var newElement = element.cloneNode(true); // Clone the element (deep clone)
            element.parentNode.replaceChild(newElement, element); // Replace old element with new cloned element
        }


        // Function to delete a file
        function deleteFile(fileId, event) {
            event.preventDefault(); // Prevents page refresh
            removeAllEventListeners(document.getElementById("process_button"));


            document.getElementById("process_button").onclick = null;


            // Set up the confirmation process
            document.getElementById("process_button").onclick = function() {
                deletefilefromview(fileId);
            };

            // Show confirmation alert
            passing_alert_value('Confirmation', "Are you sure you want to delete this file?", 'confirmation_alert',
                'alert_header', 'alert_body', 'forward_alert');
        }

        function deletefilefromview(fileId) {



            $('#file-card-' + fileId).hide();

            // Optionally, remove the file ID from activefileid (if necessary)
            var activeFileIds = $('#active_fileid').val().split(',');
            activeFileIds = activeFileIds.filter(function(id) {
                return id != fileId;
            });
            $('#active_fileid').val(activeFileIds.join(','));


            // Get the current deactivefileid value and ensure it is an array
            var deactiveFileIds = $('#deactive_fileid').val().split(',').filter(function(id) {
                return id !== ''; // Remove empty values (in case there's a leading comma)
            });

            // Add the file ID to deactivefihbleid if not already present
            if (!deactiveFileIds.includes(fileId.toString())) {
                deactiveFileIds.push(fileId);
            }

            //alert(fileId);
            if (fileId !== 1) { // Only allow removal of second and third file inputs
                $(`#fileupload_${fileId}`).remove();
                fileCount--;
            }

            // alert('delete from view');
            // alert(fileCount);



            if ((fileCount > 0) && (fileCount < '<?php echo $fileuploadcount; ?>')) {
                $('#file-input-container').show()
                $('#add-file-btn').show()
            }



            // Join the array with commas and update the deactive_fileid hidden input field
            $('#deactive_fileid').val(deactiveFileIds.join(','));
        }

        /**************************************** Fit the upload files, delete upload file in edit **********************/


        // Handle Add File Button Click
        $('#add-file-btn').click(function() {
            $('.file-input-container').show();
            // add_uploadfile()
        });

        // function add_uploadfile()
        // {
        //     if (fileCount < 3) {
        //         fileCount++;
        //         let newFileInput = `
    //             <div class="file-input" id="fileupload_${fileCount}">
    //                 <input type="file" name="fileupload[]" class="file-upload" required>
    //                 <button type="button" class="remove-file" data-id="${fileCount}">Remove</button></div>`;
        //             // if(fileCount > 1 )
        //             // {
        //             //     newFileInput    += `<button type="button" class="remove-file" data-id="${fileCount}">Remove</button>`;
        //             // }

        //             // newFileInput        +=   `</div>`;
        //             // alert(newFileInput)

        //         $('.file-input-container').append(newFileInput);

        //         // Revalidate the newly added input field (apply validation)
        //         // $(`#fileupload_${fileCount} input`).rules("add", {
        //         //     required: true,
        //         //     accept: "image/*,.pdf,.docx",
        //         //     messages: {
        //         //         required: "Please select a file.",
        //         //         accept: "Only image files, PDF, or DOCX are allowed."
        //         //     }
        //         // });
        //     } else {
        //         alert('You can only add up to 3 files.');
        //     }
        // }


        // Initialize fileCount

        function add_uploadfile() {
            if (fileCount < '<?php echo $fileuploadcount; ?>') {
                fileCount++;
                let newFileInput = `
                <div class="file-input ms-2" id="fileupload_${fileCount}">
                    <div class="col-md-12 ">
                        <div class="input-group ">
                            <input type="file" name="fileupload[]" class="file-upload form-control" required onchange="validateFileType(this)">
                            <label class="input-group-text text-bg-danger remove-file" data-id="${fileCount}" for="inputGroupFile02">Remove</label>
                        </div>
                    </div>
                    <span class="file-error-message" style="color: red;"></span>
                </div>`;
                $('.file-input-container').append(newFileInput);
            } else {
                alert('You can only add up to' + <?php echo $fileuploadcount; ?> + ' files.');
            }
        }

        // Validate the file type and size when a user selects a file
        // function validateFileType(input) {

        //     alert('jo');
        //     const file = input.files[0];
        //     const errorMessage = input.nextElementSibling; // The <span> element for displaying error messages

        //     // Allowed file types (image formats, PDF, Excel)
        //     const allowedExtensions = ['image/jpeg', 'image/png', 'application/pdf', 'application/vnd.ms-excel',
        //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        //     ];

        //     // Maximum file size (3MB in bytes)
        //     const maxFileSize = 3 * 1024 * 1024; // 3MB = 3 * 1024 * 1024 bytes

        //     // Check if file type is allowed
        //     if (!allowedExtensions.includes(file.type)) {
        //         errorMessage.textContent = 'Invalid file type. Please select a PNG, JPG, JPEG, PDF, or Excel file.';
        //          input.value = null; // Clear the input field
        //         return; // Stop further execution
        //     }

        //     // Check if file size exceeds the limit (3MB)
        //     if (file.size > maxFileSize) {
        //         errorMessage.textContent = 'File size exceeds 3MB. Please select a smaller file.';
        //         input.value = null; // Clear the input field
        //         return; // Stop further execution
        //     }

        //     // If file is valid, clear the error message
        //     errorMessage.textContent = '';
        // }

        function validateFileType(input) {
            const file = input.files[0]; // Get the selected file
            const allowedExtensions = [
                'application/pdf'
            ];

            const maxFileSize = 2 * 1024 * 1024; // 3MB in bytes

            if (file) { // Ensure a file is selected
                // Check if the file type is allowed
                if (!allowedExtensions.includes(file.type)) {
                    alert('Invalid file type. Please select a PDF, or Excel file.');
                    input.value = ''; // Clear the input field
                    return; // Stop further execution
                }

                // Check if the file size exceeds the limit (3MB)
                if (file.size > maxFileSize) {
                    alert('File size exceeds 2MB. Please select a smaller file.');
                    input.value = ''; // Clear the input field
                    return; // Stop further execution
                }
            } else {
                alert('No file selected. Please choose a valid file.');
                input.value = ''; // Clear the input field
                return;
            }


        }


        // Handle Remove Button Click
        $(document).on('click', '.remove-file', function() {
            const fileId = $(this).data('id');
            // if (fileId !== 1) {  // Only allow removal of second and third file inputs
            $(`#fileupload_${fileId}`).remove();
            fileCount--;
            //}
        });



        //     function addNewWorkRow(event,action,name,gpfno,designation) {
        //         event.preventDefault();
        //         let newWorkRow = `
    //         <div class="d-flex mt-4">
    //             <div class="col-md-2 ms-2">
    //                 <input type="text" class="form-control" id=""name="name[]" value="${name}" >
    //             </div>
    //             <div class="col-md-2 ms-2">
    //             <input type="text" class="form-control" id="" name="gpfno[]" value="${gpfno}" >
    //         </div>
    //         <div class="col-md-2 ms-2">
    //             <input type="text" class="form-control" id=""name="designation[]" value="${designation}" >
    //         </div>`;
        //         if(action == 'edit')
        //         {
        //             newWorkRow += `
    //             <div class="col-md-2 ms-2">
    //                 <input type="radio" name="edit_option[]" value="option1" class="form-check-input"> Option 1
    //             </div>
    //             <div class="col-md-2 ms-2">
    //                 <input type="radio" name="edit_option[]" value="option2" class="form-check-input"> Option 2
    //             </div>`;
        //         }
        //         else
        //         {
        //             newWorkRow  +=`<button class="btn bg-danger ms-2">Delete</button> `;
        //         }

        //         newWorkRow += `<button class="btn bg-success ms-2" onclick="addNewWorkRow(event)">Add</button>
    //     </div>
    //     `;


        //     let table = $(`#dynamicRowsContainer`);
        //         // ✅ Append the new row
        //         table.append(newWorkRow);







        // }
        // function change_activeStatus(checkbox) {
        //     let label = document.querySelector(
        //         `label[for="${checkbox.id}"]`); // Select the correct label            if (label) {
        //     label.textContent = checkbox.checked ? "Active" : "Inactive";
        // }

        let maxRow = 5;


        function applyValidationToNewFields(inputName, message) {
            let $input = $("[name='" + inputName + "']"); // Select input by name
            // console.log("Applying validation to:", inputName);
            if ($input.length) {
                console.log("Applying validation to:", inputName);

                let validator = $("#auditslip").data("validator"); // Get validator instance

                if (!validator) {

                    $("#auditslip").validate(); // Ensure validation is initialized
                    validator = $("#auditslip").data("validator");
                }
                $input.rules("remove");
                // Ensure rules are applied only once
                // if (!$input.rules()) {
                //     alert();
                $input.rules("add", {
                    required: true,
                    messages: {
                        required: message
                    }
                });
                // }

                validator.element($input);

                // ✅ Ensure validation runs on change without removing existing messages
                $input.on("change", function() {
                    $(this).valid();
                });
            } else {
                console.error("❌ Element not found:", inputName);
            }
        }
        // // Function to add a new row and apply validation
        // function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag) {
        //     event.preventDefault();

        //     let isChecked = (statusflag === 'Y') ? 'checked' : '';
        //     let isdisabled = '';

        //     if (rowCount >= maxRow) {
        //         alert("Maximum row limit reached!");
        //         return;
        //     }

        //     let selectedOption = "";
        //     if (notype === "01") {
        //         selectedOption = "01";  // GPF No
        //     } else if (notype === "02") {
        //         selectedOption = "02";  // CPF No
        //     } else if (notype === "03") {
        //         selectedOption = "03";  // IFHRMS No
        //     }

        //     // Create the new row with proper string interpolation for dynamic values
        //     let newWorkRow = `
    //         <div class="d-flex mt-4 work-row" id="row${rowCount}">
    //             <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">
    //             <div class="col-md-2">
    //                 <select class="form-select" name="notype${rowCount}" value="${name}" ${isdisabled}>
    //                     <option value="">---Select Type---</option>
    //                     <option value="01" ${selectedOption === "01" ? "selected" : ""}>GPF No</option>
    //                     <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPF No</option>
    //                     <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
    //                 </select>
    //             </div>
    //             <div class="col-md-2 ms-2">
    //                 <input type="text" class="form-control name" id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled}>
    //             </div>
    //             <div class="col-md-1 ms-2">
    //                 <input type="text" class="form-control" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="GPF Number" ${isdisabled}>
    //             </div>
    //             <div class="col-md-2 ms-2">
    //                 <input type="text" class="form-control name" name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
    //             </div>
    //             <div class="col-md-2 ms-2">
    //                 <input type="text" class="form-control numberswithdecimal" maxlength="10" name="amount${rowCount}" value="${amount}" placeholder="Amount" ${isdisabled}>
    //             </div>`;

        //     if ((action === 'edit') || (action === 'view')) {
        //         let isdisabled = (action === 'view') ? 'disabled' : '';
        //         newWorkRow += `
    //             <div class="col-md-1 ms-2 mt-2">
    //                 <div class="form-check form-check-inline">
    //                     <input type="checkbox" class="form-check-input warning" name="activestatus[]" ${isChecked} ${isdisabled}>
    //                     <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>
    //                 </div>
    //             </div>`;
        //     } else if(action === 'entry') {
        //         if (rowCount > 0) {
        //             newWorkRow += `
    //                 <button type="button" class="btn btn-danger fw-medium ms-2 deleteRowBtn" onclick="deleteRow(${rowCount})">
    //                     <i class="ti ti-trash"></i>
    //                 </button>`;
        //         }
        //     }

        //     if (action !== 'view') {
        //         newWorkRow += `
    //         <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '')">
    //             <i class="ti ti-circle-plus"></i>
    //         </button>
    //         </div>`;
        //     }

        //     // Append the new row

        //     $("#dynamicRowsContainer").append(newWorkRow);

        //     // Check if the row limit has been reached
        //     checkRowLimit(isLast);

        //     // Apply validation to the newly added row's fields
        //     applyValidationToNewFields(`notype${rowCount}`,'Select Number Type');
        //     applyValidationToNewFields(`name${rowCount}`,'Enter Name');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`gpfno${rowCount}`,'Enter GPF Number');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`designation${rowCount}`,'Enter Designation');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`amount${rowCount}`,'Enter Amount');  // Passing rowCount to target fields dynamically
        //     // Passing rowCount to target fields dynamically
        //     rowCount++;
        //     // Trigger validation immediately after adding the row
        //     // $("#auditslip").valid(); // Validate the entire form, including new rows
        // }


        //     function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast,statusflag) {
        //     event.preventDefault();
        //     let isChecked = (statusflag === 'Y') ? 'checked' : '';

        //     // isdisabled  =   '';

        //     let isdisabled = (action === 'view') ? 'disabled' : '';



        //     if (rowCount >= maxRow) {
        //         alert("Maximum row limit reached!");
        //         return;
        //     }

        //     let selectedOption = "";
        //     if (notype === "01") {
        //         selectedOption = "01";  // GPF No
        //     } else if (notype === "02") {
        //         selectedOption = "02";  // CPF No
        //     } else if (notype === "03") {
        //         selectedOption = "03";  // IFHRMS No
        //     }

        //     // Create the new row with proper string interpolation for dynamic values
        //     let newWorkRow = `
    //         <div class="d-flex mt-2 work-row" id="row${rowCount}">
    //             <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}" >
    //             <div class="col-md-2">`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     `
    //               <label class="form-label  lang" for="validationDefaultUsername" key="">Type</label>`;
        //             }
        //             newWorkRow += `

    //                 <select class="form-select"  id="notype${rowCount}" name="notype${rowCount}"  value="${name}"  ${isdisabled} >
    //                     <option value="">---Select Type---</option>
    //                     <option value="01" ${selectedOption === "01" ? "selected" : ""}>GPF No</option>
    //                     <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPF No</option>
    //                     <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
    //                 </select>
    //             </div>
    //              <div class="col-md-1 ms-2">`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     `
    //               <label class="form-label  lang" for="validationDefaultUsername" key="">GPF Number</label>`;
        //             }
        //             newWorkRow += `
    //                 <input type="text" class="form-control alpha_numeric" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="GPF Number" ${isdisabled}>
    //             </div>
    //             <div class="col-md-2 ms-2">`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     `
    //               <label class="form-label  lang" for="validationDefaultUsername" key="name">Name</label>`;
        //             }
        //             newWorkRow += `
    //                 <input type="text" class="form-control name" id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled} >
    //             </div>

    //             <div class="col-md-2 ms-2">`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     `
    //               <label class="form-label  lang" for="validationDefaultUsername" key="">Desgnation</label>`;
        //             }
        //             newWorkRow += `
    //                 <input type="text" class="form-control name" name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
    //             </div>
    //             <div class="col-md-2 ms-2">`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     `
    //               <label class="form-label  lang" for="validationDefaultUsername" key="">Amount</label>`;
        //             }
        //             newWorkRow += `
    //                 <input type="text" class="form-control numberswithdecimal"  maxlength="10" name="amount${rowCount}" value="${amount}" placeholder="Role" ${isdisabled}>
    //             </div>`;
        //             if (rowCount == 0) {
        //                 newWorkRow +=
        //                     ` <div class="col-md-1 ">
    //               <label class="form-label  lang" for="validationDefaultUsername" key="name">Action</label>`;
        //             }


        //     if ((action === 'edit') || (action === 'view')){

        //         let isdisabled = (action === 'view') ? 'disabled' : '';
        //         newWorkRow += `

    //                 <div class="form-check form-check-inline ms-2 ">
    //                     <input type="checkbox" class="form-check-input warning" name="activestatus[]"   ${isChecked} ${isdisabled}>
    //                     <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>
    //                 </div>
    //            `;
        //     } else if(action === 'entry') {
        //         if (rowCount > 0) {
        //             newWorkRow += `
    //                 <button type="button" class="btn btn-danger fw-medium ms-2 deleteRowBtn" onclick="deleteRow(${rowCount})">
    //                     <i class="ti ti-trash"></i>
    //                 </button></div> </div>`;
        //         }
        //     }

        //     if (action !== 'view') {
        //             newWorkRow += `
    //             <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '')">
    //                 <i class="ti ti-circle-plus"></i>
    //             </button>
    //             </div>`;
        //         }

        //       //  alert(isLast);


        //     $("#dynamicRowsContainer").append(newWorkRow);

        //     // Check if the row limit has been reached
        //     checkRowLimit(isLast);

        //     // Apply validation to the newly added row's fields
        //     applyValidationToNewFields(`notype${rowCount}`,'Select Number Type');
        //     applyValidationToNewFields(`name${rowCount}`,'Enter Name');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`gpfno${rowCount}`,'Enter GPF Number');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`designation${rowCount}`,'Enter Designation');  // Passing rowCount to target fields dynamically
        //     applyValidationToNewFields(`amount${rowCount}`,'Enter Amount');  // Passing rowCount to target fields dynamically
        //     // Passing rowCount to target fields dynamically
        //     rowCount++;
        // }


        //     function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag) {
        //         event.preventDefault();

        //         let isChecked = (statusflag === 'Y') ? 'checked' : '';
        //         let isdisabled = '';

        //         if (rowCount >= maxRow) {
        //             alert("Maximum row limit reached!");
        //             return;
        //         }

        //         let selectedOption = "";
        //         if (notype === "01") {
        //             selectedOption = "01"; // GPF No
        //         } else if (notype === "02") {
        //             selectedOption = "02"; // CPF No
        //         } else if (notype === "03") {
        //             selectedOption = "03"; // IFHRMS No
        //         }

        //         let newWorkRow = '';

        //         if (rowCount == 0) {
        //             newWorkRow += `<hr>`;
        //         }

        //         newWorkRow += `
    // <div class="row work-row p-2 border-bottom mb-3" id="row${rowCount}">
    //     <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">

    //     <!-- Type Column -->
    //     <div class="col-md-2 col-12 mb-2">
    //         <label class="form-label d-md-none d-block">Type</label>
    //         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Type</label>` : ''}
    //         <select class="form-select" name="notype${rowCount}" value="${name}" ${isdisabled}>
    //             <option value="">---Select Type---</option>
    //             <option value="01" ${selectedOption === "01" ? "selected" : ""}>GPF No</option>
    //             <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPF No</option>
    //             <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
    //         </select>
    //     </div>

    //     <!-- GPF Number Column -->
    //     <div class="col-md-2 col-12 mb-2">
    //         <label class="form-label d-md-none d-block">GPF Number</label>
    //         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">GPF Number</label>` : ''}
    //        <input type="text" class="form-control alpha_numeric" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="GPF Number" ${isdisabled}>
    //     </div>

    //     <!-- Name Column -->
    //     <div class="col-md-2 col-12 mb-2">
    //         <label class="form-label d-md-none d-block">Name</label>
    //         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Name</label>` : ''}
    //         <input type="text" class="form-control name" id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled}>
    //     </div>

    //     <!-- Designation Column -->
    //     <div class="col-md-2 col-12 mb-2">
    //         <label class="form-label d-md-none d-block">Designation</label>
    //         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Designation</label>` : ''}
    //         <input type="text" class="form-control name" name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
    //     </div>

    //     <!-- Amount Column -->
    //     <div class="col-md-2 col-12 mb-2">
    //         <label class="form-label d-md-none d-block">Amount</label>
    //         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Amount</label>` : ''}
    //         <input type="text" class="form-control numberswithdecimal" maxlength="10" name="amount${rowCount}" value="${amount}" placeholder="Amount" ${isdisabled}>
    //     </div>

    //     <!-- Action Column -->
    //    <div class="col-md-2 mb-2 action-row">
    //         ${rowCount == 0 ? `<label class="form-label d-md-block d-none">Action</label>` : ''}
    //         <div class="d-md-none">
    //             <label class="form-label d-block">Action</label>
    //         </div>
    //         <div class="d-flex flex-md-row flex-column gap-1">
    //             ${rowCount > 0 ? `
        //                                                                                                                                 <button type="button" class="mar_left btn btn-danger fw-medium deleteRowBtn" onclick="deleteRow(${rowCount})">
        //                                                                                                                                     <i class="ti ti-trash"></i>
        //                                                                                                                                 </button>` : ''}
    //             <button type="button" class=" mar_left btn btn-success fw-medium addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '')">
    //                 <i class="ti ti-circle-plus"></i>
    //             </button>
    //         </div>
    //     </div>




    // </div>
    // `;

        //         $("#dynamicRowsContainer").append(newWorkRow);

        //         // Hide the add button in the previous row
        //         if (rowCount > 0) {
        //             $(`#row${rowCount - 1} .addRowBtn`).hide();
        //         }

        //         // Check if the row limit has been reached
        //         checkRowLimit(isLast);

        //         // Apply validation to the newly added row's fields
        //         applyValidationToNewFields(`notype${rowCount}`, 'Select Number Type');
        //         applyValidationToNewFields(`name${rowCount}`, 'Enter Name');
        //         applyValidationToNewFields(`gpfno${rowCount}`, 'Enter GPF Number');
        //         applyValidationToNewFields(`designation${rowCount}`, 'Enter Designation');
        //         applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');

        //         rowCount++;
        //     }


        restrictSpecialChars(".removesplchar");
        restrictSpecialChars(".removesplchar_number");
        restrictSpecialChars(".removesplchar_numberwithdecimal");

        function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag) {
            event.preventDefault();



            let isChecked = (statusflag === 'Y') ? 'checked' : '';
            // let isdisabled = '';

            let isdisabled = (action === 'view') ? 'disabled' : '';

            if (rowCount >= maxRow) {
                alert("Maximum row limit reached!");
                return;
            }

            let selectedOption = "";
            if (notype === "01") {
                selectedOption = "01"; // GPF No
            } else if (notype === "02") {
                selectedOption = "02"; // CPF No
            } else if (notype === "03") {
                selectedOption = "03"; // IFHRMS No
            }

            let newWorkRow = '';

            if (rowCount == 0) {
                newWorkRow += `<hr>`;
            }

            newWorkRow += `
    <div class="row work-row p-2 border-bottom mb-3" id="row${rowCount}">
        <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">

        <!-- Type Column -->
        <div class="col-md-2 col-12 mb-2">
            <label class="form-label d-md-none d-block">Type</label>
            ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Type</label>` : ''}
            <select class="form-select" name="notype${rowCount}" value="${name}" ${isdisabled}>
                <option value="">---Select Type---</option>
                <option value="01" ${selectedOption === "01" ? "selected" : ""}>EPF No</option>
                <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPS No</option>
                <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
            </select>
        </div>

        <!-- GPF Number Column -->
        <div class="col-md-2 col-12 mb-2">
            <label class="form-label d-md-none d-block">Number</label>
            ${rowCount == 0 ? `<label class="form-label d-none d-md-block"> Number</label>` : ''}
           <input type="text" class="form-control removesplchar_number alpha_numeric" maxlength="20" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="Number" ${isdisabled}>
        </div>

        <!-- Name Column -->
        <div class="col-md-2 col-12 mb-2">
            <label class="form-label d-md-none d-block">Name</label>
            ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Name</label>` : ''}
            <input type="text" class="form-control removesplchar_text name" maxlength="50"  id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled}>
        </div>

        <!-- Designation Column -->
        <div class="col-md-2 col-12 mb-2">
            <label class="form-label d-md-none d-block">Designation</label>
            ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Designation</label>` : ''}
            <input type="text" class="form-control removesplchar_text name" maxlength="50"  name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
        </div>

        <!-- Amount Column -->
        <div class="col-md-2 col-12 mb-2">
            <label class="form-label d-md-none d-block">Amount</label>
            ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Amount</label>` : ''}
            <input type="text" class="form-control removesplchar_numberwithdecimal numberswithdecimal" maxlength="10" name="amount${rowCount}" value="${amount}" placeholder="Amount" ${isdisabled}>
        </div>

        <!-- Action Column -->
        <div class="col-md-2 mb-2 action-row">
            ${rowCount == 0  ? `<label class="form-label d-md-block d-none">Action</label>` : ''}
            <div class="d-md-none">
                <label class="form-label d-block">Action</label>
            </div>
            <div class="d-flex flex-md-row flex-column gap-1">
                ${rowCount > 0 && (action === 'entry') ? `
                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class="mar_left btn btn-danger fw-medium deleteRowBtn" onclick="deleteRow(${rowCount})">
                                                                                                                                                                                                                                                                                                                                                                                            <i class="ti ti-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                            </button>` : ''}


                 ${ ((action === 'view') ||  (action === 'edit'))? `
                                                                                                                                                                                                                                                                                                                                                                                           <input type="checkbox" class="form-check-input warning" name="activestatus[]"   ${isChecked} ${isdisabled}>
                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>` : ''}

                ${action !== 'view' && (action !== 'view') ? `
                                                                                                                                                                                                                                                                                                                                                                                            <button type="button" class=" mar_left btn btn-success fw-medium addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '')">
                                                                                                                                                                                                                                                                                                                                                                                                <i class="ti ti-circle-plus"></i>
                                                                                                                                                                                                                                                                                                                                                                                            </button>` : ''}
            </div>
        </div>
    </div>
    `;

            //Append new row to the container (assuming there's a container like #workRowContainer)
            //document.getElementById('workRowContainer').insertAdjacentHTML('beforeend', newWorkRow);




            $("#dynamicRowsContainer").append(newWorkRow);

            // Hide the add button in the previous row
            if (rowCount > 0) {
                $(`#row${rowCount - 1} .addRowBtn`).hide();
            }

            // Check if the row limit has been reached
            checkRowLimit(isLast);

            // Apply validation to the newly added row's fields
            applyValidationToNewFields(`notype${rowCount}`, 'Select Number Type');
            applyValidationToNewFields(`name${rowCount}`, 'Enter Name');
            applyValidationToNewFields(`gpfno${rowCount}`, 'Enter Number');
            applyValidationToNewFields(`designation${rowCount}`, 'Enter Designation');
            applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');

            rowCount++;

            restrictSpecialChars(".removesplchar_text");
            restrictSpecialChars(".removesplchar_number");
            restrictSpecialChars(".removesplchar_numberwithdecimal");
        }


        $(document).on('keypress', '.name', function(event) {
            var inputValue = $(this).val();
            var charCode = event.charCode;

            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                event.charCode == 32
            )
                return true;

            // Block non-numeric characters (excluding period)
            return false;
        });

        $(document).on('keypress', '.alpha_numeric', function(event) {
            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                (event.charCode >= 48 && event.charCode <= 57) ||
                event.charCode == 32
            )
                return true; // let it happen, don't do anything
            else return false;
        });

        // Event delegation for dynamic inputs
        $(document).on('keypress', '.numberswithdecimal', function(event) {
            var inputValue = $(this).val();
            var charCode = event.charCode;

            // Allow numeric characters (0-9)
            if (charCode >= 48 && charCode <= 57) {
                return true; // let it happen
            }

            // Allow a single period (.)
            if (charCode === 46 && inputValue.indexOf('.') === -1) {
                return true; // let it happen
            }

            // Block non-numeric characters (excluding period)
            return false;
        });

        $(document).on('input', '.numberswithdecimal', function() {
            var inputValue = $(this).val();
            var decimalIndex = inputValue.indexOf('.');

            if (decimalIndex !== -1) {
                // Limit digits after decimal to 2
                var integerPart = inputValue.substring(0, decimalIndex + 1);
                var decimalPart = inputValue.substring(decimalIndex + 1, decimalIndex + 3);
                $(this).val(integerPart + decimalPart);
            }
        });

        function checkRowLimit(isLast) {
            let rows = $(".work-row");


            $(".addRowBtn").hide();



            if (rowCount >= maxRow) {

                return; // Stop processing if max rows are reached
            }
            // alert(rows.length);
            if (rows.length > 0) {

                // Show plus button on last row
                $(rows[rows.length - 1]).find(".addRowBtn").show();
            }
        }

        function deleteRow(rowId) {
            $(`#row${rowId}`).remove();
            rowCount--;
        }


        function updateCheckboxValue(checkbox, rowIndex) {
            // let label = document.querySelector(
            //     `label[for="${checkbox.id}"]`); // Select the correct label            if (label) {
            // label.textContent = checkbox.checked ? "Active" : "Inactive";
            if (checkbox.checked) {
                // If checked, set value to 1
                $(`input[name="activestatus[${rowIndex}]"]`).val('1');
            } else {
                // If unchecked, set value to 0
                $(`input[name="activestatus[${rowIndex}]"]`).val('0');
            }
        }

        function deleteRow(rowId) {
            if ($("#liabilityid" + rowId).val()) {
                liabilityid = $("#liabilityid" + rowId).val();

                // Optionally, remove the file ID from activefileid (if necessary)
                var activeliability = $('#liabilityid').val().split(',');
                activeliability = activeliability.filter(function(id) {
                    return id != liabilityid;
                });
                $('#liabilityid').val(activeliability.join(','));


                // Get the current deactivefileid value and ensure it is an array
                var deactiveliabilityIds = $('#deleted_liabilityid').val().split(',').filter(function(id) {
                    return id !== ''; // Remove empty values (in case there's a leading comma)
                });


                // Add the file ID to deactivefileid if not already present
                if (!deactiveliabilityIds.includes(liabilityid.toString())) {
                    deactiveliabilityIds.push(liabilityid);
                }

                $('#deleted_liabilityid').val(deactiveliabilityIds.join(','));

            }
            $("#row" + rowId).remove();
            rowCount--;

            checkRowLimit();
        }

        // function enable_liability(selectedOption) {
        //     if (selectedOption === 'Y') {
        //         // Show the textbox when "Yes" is selected
        //         $('#dynamicRowsContainer').show();
        //         const container = document.getElementById('dynamicRowsContainer');
        //         container.innerHTML = ''; // Clear current rows
        //         rowCount = 0; // Reset row count
        //         addNewWorkRow(event, 'entry', '', '', ''); // Add the first row
        //     } else {
        //         // Hide the textbox when "No" is selected
        //         $('#dynamicRowsContainer').hide();
        //     }
        // }

        // Delete row function

        function restrictSpecialChars(selector) {
            $(selector)
                .off("keypress paste")
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;

                    if (selector === '.removesplchar_text') {
                        if (!/^[a-zA-Z\u0B80-\u0BFF]$/.test(char)) {
                            event.preventDefault();
                        }
                    } else if (selector === '.removesplchar_number') {
                        if (!/^[0-9]$/.test(char)) {
                            event.preventDefault();
                        }
                    } else if (selector === '.removesplchar_numberwithdecimal') {
                        // Allow digits, one dot, no leading dot, and only 2 digits after dot
                        if (!/[0-9.]/.test(char)) {
                            event.preventDefault();
                        }

                        // Prevent more than one dot
                        if (char === '.' && value.includes('.')) {
                            event.preventDefault();
                        }

                        // Prevent dot as the first character
                        if (char === '.' && value.length === 0) {
                            event.preventDefault();
                        }

                        // Prevent more than 2 digits after the decimal
                        if (value.includes('.')) {
                            let parts = value.split('.');
                            if (parts[1].length >= 2 && this.selectionStart > value.indexOf('.')) {
                                event.preventDefault();
                            }
                        }
                    }
                })
                .on("paste", function(e) {
                    e.preventDefault();
                    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                    let cleanData = '';

                    if (selector === '.removesplchar_text') {
                        cleanData = pasteData.replace(/[^a-zA-Z\u0B80-\u0BFF]/g, '');
                    } else if (selector === '.removesplchar_number') {
                        cleanData = pasteData.replace(/[^0-9]/g, '');
                    } else if (selector === '.removesplchar_numberwithdecimal') {
                        // Remove all but digits and dots
                        cleanData = pasteData.replace(/[^0-9.]/g, '');

                        // Allow only one dot
                        let dotIndex = cleanData.indexOf('.');
                        if (dotIndex !== -1) {
                            // Keep only the first dot and remove others
                            cleanData = cleanData.substring(0, dotIndex + 1) +
                                cleanData.substring(dotIndex + 1).replace(/\./g, '');
                        }

                        // Trim to 2 decimals max
                        if (cleanData.includes('.')) {
                            let [intPart, decPart] = cleanData.split('.');
                            decPart = decPart.substring(0, 2); // limit to 2 decimals
                            cleanData = intPart + '.' + decPart;
                        }
                    }

                    // Insert clean data at cursor position
                    let input = e.target;
                    let start = input.selectionStart;
                    let end = input.selectionEnd;
                    let original = input.value;
                    input.value = original.substring(0, start) + cleanData + original.substring(end);
                    input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                });
        }
    </script>
@endsection
