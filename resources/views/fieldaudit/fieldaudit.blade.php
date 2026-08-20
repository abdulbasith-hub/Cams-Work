@section('content')
    @extends('index2')
    @include('common.alert')


    <?php
    $instdel = json_decode($inst_details, true);
    $getmajorobjection = json_decode($get_majorobjection, true);

    //$supercheckquestions = json_decode($supercheckquestions, true);

    $teamhead = $instdel[0]['auditteamhead'];
    $instid = $instdel[0]['instid'];
    $teamheadid = $teamheadid;
    $auditscheduleid = $instdel[0]['auditscheduleid'];
    $schteammemberid = $instdel[0]['schteammemberid'];
    $auditplanid = $instdel[0]['auditplanid'];

    if ($teamhead == 'Y') {
        $buttonname = 'Approve';
    } else {
        $buttonname = 'Forward';
    }
    $entry_show_first_tab = '';
    $audit_show_first_tab = '';
    $show_tab = '';
    $entrytab = '';
    $worktab = '';

    if ($teamhead == 'Y') {
        // $workallocationslip = '2';
        $auditslip = '3';
        $view_auditslip = '4';
        $entry_show_first_tab = 'show active';
        $entrytab = 'active';
        $show_workform = '';
        $hidetablefield = '';
    } else {
        $audit_show_first_tab = 'show active';
        // $workallocationslip = '2';
        $auditslip = '3';
        $view_auditslip = '4';
        $show_tab = 'style="display:none"';
        $worktab = 'active';
        $show_workform = 'style="display:none"';
        $hidetablefield = 'style="display:none"';
    }
    ?>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <!-- {{-- <link rel="stylesheet" href="../assets/libs/daterangepicker/daterangepicker.css"> --}} -->
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <style>
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

        .scrolldiv {
            max-height: 420px;
            /* Set a max height for the body */
            overflow-y: auto;
            /* Enable vertical scrolling when content exceeds the height */
            height: 420px;
            padding: 20px;
        }

        /* Style for the entire scrollbar */
        .scrolldiv::-webkit-scrollbar {
            width: 6px;
            /* Adjust the width for vertical scrollbar */
            height: 12px;
            /* Adjust the height for horizontal scrollbar */
        }

        /* Style for the track (part the thumb slides within) */
        .scrolldiv::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* Light gray background */
            border-radius: 10px;
            /* Rounded corners */
        }

        /* Style for the thumb (draggable part) */
        .scrolldiv::-webkit-scrollbar-thumb {
            background: #888;
            /* Gray thumb */
            border-radius: 10px;
            /* Rounded corners */
        }

        /* Style for the thumb on hover */
        .scrolldiv::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* Darker gray on hover */
        }
    </style> <?php $fromdate = \Carbon\Carbon::parse($instdel[0]['fromdate'])->format('d-m-Y'); ?> <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Name</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['instename']; ?>" disabled> </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Category</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['catename']; ?>" disabled> </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Type of
                                Audit</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['typeofauditename']; ?>" disabled> </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Year of
                                Audit</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['yearname']; ?>" disabled> </div>
                        <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Total
                                Mandays</label> <input type="text" class="form-control" id="total_mandays"
                                name="total_mandays" value="<?php echo $instdel[0]['mandays']; ?>" disabled> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label required" for="validationDefault01">Select Action</label>
                            <select class=" form-control custom-select" name="form_sel" id="form_sel"
                                onchange="show_form()">
                                <option value="">--- Select Action ---</option>
                                <option value="E">Entry Meeting</option>
                                <option value="W">Work Allocation</option>
                                <?php if($teamhead == 'Y')
{?>

                                <option value="X">Exit Meeting</option>
                                <?php } ?>
                                <!-- <option value="S">Suspend</option> -->

                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
            </div>
            <!-- Step 1 -->
            <div class="card card_border hide_this" id="step1">
                <div class="card-header card_header_color">Entry Meeting</div>
                <div class="card-body">
                    <form id="entrymeetingform">
                        <div class="row">
                            <div class="col-md-3 mb-3"> 
                                <label class="form-label" for="validationDefault02">Proposed Date</label>
                                <div class="input-group" > 
                                <input type="text"
                                        class="form-control datepicker" id="from_date" name="from_date"
                                        placeholder="dd/mm/yyyy" value="<?php echo $fromdate; ?>" disabled /> <span
                                        class="input-group-text"> <i class="ti ti-calendar fs-5"></i> </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label" for="validationDefault02">Entry Meeting Date</label>

                                <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                    value="<?php echo $auditscheduleid; ?>">
                                <div class="input-group" onclick="datepicker('entry_date','')"> <input type="text"
                                        class="form-control datepicker" id="entry_date" name="entry_date"
                                        placeholder="dd/mm/yyyy" value="" />
                                    <span class="input-group-text"> <i class="ti ti-calendar fs-5"></i> </span>
                                </div>
                            </div>
                            <div class="col-md-3"> <label class="form-label" for="validationDefault02">Entry
                                    Meeting</label>
                                <div class="card overflow-hidden">
                                    <div class="d-flex flex-row">
                                        <div class="p-2  align-items-center">
                                            <h3 class="text-danger box mb-0 round-56 p-2"> <i class="ti ti-file-text "></i>
                                            </h3>
                                        </div>
                                        <div class="p-3">
                                            <h3 class="text-dark mb-0 fs-3">Entrymeeting.pdf</h3>
                                            <!--<span>size:
                                                                                                                                                                                                                                                                                                                                                    10 mb</span>-->
                                        </div>
                                        <div class="p-3 align-items-center ms-auto">
                                            <h3 class="text-primary box mb-0" onclick="downloadFile('entrymeeting')"> <i
                                                    class="ti ti-download"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3"> <label class="form-label" for="validationDefault02">Code
                                    of Ethics</label>
                                <div class="card overflow-hidden">
                                    <div class="d-flex flex-row">
                                        <div class="p-2  align-items-center">
                                            <h3 class="text-danger box mb-0 round-56 p-2"> <i
                                                    class="ti ti-file-text "></i> </h3>
                                        </div>
                                        <div class="p-3">
                                            <h3 class="text-dark mb-0 fs-3">codeofethics.pdf</h3>
                                            <!--<span>size:
                                                                                                                                                                                                                                                                                                                                                        10 mb</span>-->
                                        </div>
                                        <div class="p-3 align-items-center ms-auto">
                                            <h3 class="text-primary box mb-0" onclick="downloadFile('codeofethics')">
                                                <i class="ti ti-download"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="statusmessage_entrymeet" class="row  hide_this">
                            <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                Data has been submitted successfully.
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2 mx-auto " id="entrymeet_confirm_btnDIV">
                               <button class="btn btn-success mt-3 lang" key="submit" type="submit"  id="entrymeet_confirm_btn" >Submit</button>

                              <!--  <button type="button" id="entrymeet_confirm_btn" class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center ">
                                    <i class="ti ti-checks fs-4 me-2 "></i>
                                    Submit
                                </button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="card card_border hide_this" id="step2">
            <!-- <div class="card-header card_header_color">Work Allocation</div> -->
            <div class="card-body">
                <div class="justify-content-center hide_this" id="workallcstatusDiv">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="text-center align-middle lang">Work Allocation is not yet randomized</h4>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card mt-6" style="border-color: #7198b9" id="workallocTable">
                    <div class="card-header card_header_color">Work Allocation Details</div>
                    <div class="card-body">
                        <div class="datatables">
                            <div class="table-responsive hide_this" id="tableshow">
                                <table id="workallocationtable"
                                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                    <thead>
                                        <tr>
                                            <th class="lang" key="s_no">S.No</th>
                                            <th> <?php echo $hidetablefield; ?>User</th>
                                            <th>Audit Period</th>
                                            <th>Group</th>
                                            <th>Work Allocation</th>
                                            <!-- <th>Sub Work Allocation</th> -->
                                            <!-- <th <?php echo $hidetablefield; ?> class="all">Action</th> -->
                                        </tr>
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
            </div>
        </div>
            <!-- Step 5 -->
            <div class="card card_border hide_this" id="step5">
                <div class="card-header card_header_color">Exit Meeting</div>
                <div class="card-body">
                    <form name="exit_form" id="exit_form">
                        <div class="row">
                            <div class="col-md-4 mb-3"> <label class="form-label" for="validationDefault02">Exit
                                    Meeting
                                    Date</label>
                                <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                    value="<?php echo $auditscheduleid; ?>">
                                <div class="input-group" onclick="datepicker('exit_date','')"> <input type="text"
                                        class="form-control datepicker" id="exit_date" name="exit_date"
                                        placeholder="dd/mm/yyyy" value="" />
                                    <span class="input-group-text"> <i class="ti ti-calendar fs-5"></i> </span>
                                </div>
                            </div>
                            <div class="col-md-4"> <label class="form-label" for="validationDefault02">Exit
                                    Meeting</label>
                                <div class="card overflow-hidden" style="border-color: #7198b9">
                                    <div class="d-flex flex-row">
                                        <div class="p-2  align-items-center">
                                            <h3 class="text-danger box mb-0 round-56 p-2"> <i
                                                    class="ti ti-file-text "></i> </h3>
                                        </div>
                                        <div class="p-3">
                                            <h3 class="text-dark mb-0 fs-3">Exitmeeting.pdf</h3>
                                            <!--<span>size:
                                                                                                                                                                                                                                                                                                                                                    10 mb</span>-->
                                        </div>
                                        <div class="p-3 align-items-center ms-auto">
                                            <h3 class="text-primary box mb-0" onclick="downloadFile('exitmeeting')">
                                                <i class="ti ti-download"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="statusmessage" class="row  hide_this">
                            <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                Data has been submitted successfully.
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-2 mx-auto " id="cnfrm_btn">
                                <button class="btn btn-success mt-3 lang" key="submit" type="submit"  id="confirm_btn" >Submit</button>

                                <!--<button type="button" id="confirm_btn"
                                    class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center ">
                                    <i class="ti ti-checks fs-4 me-2 "></i>
                                    Submit
                                </button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card_border hide_this" id="step6">
                <div class="card-header card_header_color">Suspend Audit Schedule</div>
                <div class="card-body">
                    <form name="exit_form" id="exit_form">
                        <div class="row">
                            <div class="col-md-12 mb-12"> <label class="form-label" for="validationDefault02">Suspend
                                    Remarks</label>
                                <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                    value="<?php echo $auditscheduleid; ?>">
                                <textarea maxlength ="200" class="form-control" id="suspend_remarks" name="suspend_remarks" style="height:120px"></textarea>
                                <span id="suspension_errormsg" style="color:red">Suspension Remarks Required*</span>
                            </div>

                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2 mx-auto " id="">
                                <button type="button" id="suspend_btn"
                                    class="justify-content-center w-100 btn mb-1 btn-rounded btn-warning d-flex align-items-center ">
                                    <i class="ti ti-checks fs-4 me-2 "></i>
                                    Suspend
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </script>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/extra-libs/moment/moment.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- <script src="../assets/js/forms/daterangepicker-init.js"></script> -->
    <!--select 2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
    <!--chat-app-->
    <script src="../assets/js/apps/chat.js"></script>
    <!-- Form Wizard -->
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>


    <script>
        $('#suspension_errormsg').hide();



        $('#suspend_btn').on('click', function() {
            var suspend_remarks = $('#suspend_remarks').val(); // Get the value of the textarea


            if (suspend_remarks) {
                $('#suspension_errormsg').hide();

                var confirmation = 'Are you sure to suspend?';


                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                return;



            } else {
                $('#suspension_errormsg').show();

            }


        });

        $('#process_button').on('click', function() {
            var suspend_remarks = $('#suspend_remarks').val(); // Get the value of the textarea
            var auditschid = $('#auditscheduleid').val();
            var statusflag = 'S';
            $.ajax({
                url: '/audit/cancelorreschedule', // Replace with your endpoint
                method: 'POST',
                data: {
                    Remarks: suspend_remarks, // Pass the 'catcode' to the controller
                    scheduleid: auditschid,
                    statusflag: statusflag
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Pass CSRF token in headers
                },
                success: function(response) {
                    passing_alert_value('Alert', response, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                    window.location.href = '/dashboard';


                },
                error: function() {
                    alert("Failed to fetch team members!");
                }
            });

        });


        /***************************************************** View Form ******************************************************************* */
        function show_form() {

            const form_id = $('#form_sel').val();

            // alert();
            if (form_id == 'E') {
                $('#step1').show();
                $('#step2').hide();
                $('#step5').hide();
                $('#step6').hide();
                checkforentrymeet();

            } else if (form_id == 'W') {
                fetchallWorkdetail()
                $('#step2').show();
                $('#step1').hide();
                $('#step5').hide();
                $('#step6').hide();
            } else if (form_id == 'X') {
                $('#step5').show();
                $('#step2').hide();
                $('#step1').hide();
                $('#step6').hide();
                checkforexitmeet();
            } else if (form_id == 'S') {
                $('#step6').show();
                $('#step5').hide();
                $('#step2').hide();
                $('#step1').hide();
            } else if (form_id == '') {
                $('#step5').hide();
                $('#step2').hide();
                $('#step1').hide();
                $('#step6').hide();
            }

        }

        function checkforentrymeet() {
            let entry_dateField = document.getElementById('entry_date');
            
            // Display the value or check if the field exists
            if (entrydatefetch) {
                
                // Hide the confirmation button and show the status message
                $('#entrymeet_confirm_btnDIV').hide();
                $('#statusmessage_entrymeet').show();
                $('#entry_date').prop('disabled', true);

                // Initialize the date picker and disable the field
                datepicker('entry_date', convertDateFormatYmd_ddmmyy(entrydatefetch));
                
            } else {
                console.log("Entry date field not found.");

                // If the field doesn't exist, reset or enable it
                if (entry_dateField) {
                    entry_dateField.disabled = false;
                    entry_dateField.value = ""; // Optional: clear the field's value
                }
            }
        }



        function checkforexitmeet() {

            let exitdateField = document.getElementById('exit_date');
            if (exitdate) {

                $('#cnfrm_btn').hide();
                $('#statusmessage').show();

                $('#exit_date').prop('disabled', true);
                datepicker('exit_date', convertDateFormatYmd_ddmmyy(exitdate));
                // const formattedDates = convertDateFormat(exitdate);
                // $('#exit_date').val(formattedDates.ddmmyy);
                // If null or empty, disable the field and set its value

            } else {

                // alert();
                exitdateField.disabled = false;
                exitdateField.value = ""; // Optional: clear the field

            }
        }

        function convertDateFormat(dateInput) {
            // Create a Date object from the input
            const date = new Date(dateInput);

            // Extract day, month, and year
            const day = String(date.getDate()).padStart(2, '0'); // Ensure 2-digit format
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const year = date.getFullYear();

            // Format as dd-mm-yyyy
            const formattedDateDdmmyy = `${day}-${month}-${year}`;

            // Format as yyyy-mm-dd
            const formattedDateYmd = `${year}-${month}-${day}`;

            return {
                ddmmyy: formattedDateDdmmyy,
                ymd: formattedDateYmd
            };
        }





        $(document).ready(function() {

            // $(".daterange").daterangepicker({
            //     minDate: moment(), // Start from the current date
            //     autoApply: true, // Automatically apply the selected range
            //     locale: {
            //         format: 'DD-MM-YYYY' // Format of the date
            //     }
            // });

            // Dynamically adjust the date range based on #balance_mandays value
            const balanceMandaysInput = $("#balance_mandays");
            // const dateRangePickerInput = $(".daterange");

            // Function to update the max date based on the balance_mandays
            function updateDatePicker() {
                const balanceDays = parseInt(balanceMandaysInput.val()) || 0; // Get the value from #balance_mandays
                const maxDate = moment().add(balanceDays, 'days'); // Calculate the max date

                // Update the date range picker options
                // dateRangePickerInput.daterangepicker({
                //     minDate: moment(), // Current date
                //     maxDate: maxDate, // Max date based on balanceMandays
                //     autoApply: true, // Automatically apply the range
                //     locale: {
                //         format: 'DD-MM-YYYY' // Format
                //     }
                // });
            }

            // Initialize the picker with the current balance_mandays value
            updateDatePicker();

            // If #balance_mandays changes dynamically, reinitialize the picker
            balanceMandaysInput.on('change', function() {
                updateDatePicker();
            });
            // Initialize with different placeholders
            initSelect2("#user", "Select  User");
        });

        /********************************** Common Function ****************************************************/





        /*************************************************  Audit Tab Functions *********************************************/




        function previewAttachment(input, previewDivId) {
            // Ensure a file is selected
            if (!input.files || input.files.length === 0) return;

            const file = input.files[0];
            const previewDiv = document.getElementById(previewDivId);

            // Clear the preview area
            previewDiv.innerHTML = "";

            if (file) {
                const fileType = file.type;
                $('#upload_preview').show();

                // Check if the uploaded file is an image
                if (fileType.startsWith("image/")) {
                    const img = document.createElement("img");
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = "100%";
                    img.style.maxHeight = "400px";

                    previewDiv.appendChild(img);
                }
                // Check if the uploaded file is a PDF
                else if (fileType === "application/pdf") {
                    const iframe = document.createElement("iframe");
                    iframe.src = URL.createObjectURL(file);
                    iframe.style.width = "100%";
                    iframe.style.height = "400px";
                    iframe.setAttribute("frameborder", "0");
                    previewDiv.appendChild(iframe);
                }
                // Handle unsupported file types
                else {
                    const message = document.createElement("p");
                    message.textContent = "Unsupported file type. Please upload an image or a PDF.";
                    previewDiv.appendChild(message);
                }
            }
        }

        /***************************************************** Upload File - Preview *********************************/




     




        function show_view_card(firstItem) {
            $('#viewauditslipcard').show();
            $('#view_majorobjectioncode').val(firstItem.mainobjectionid);
            $('#view_amount_involved').val(firstItem.amtinvolved);
            $('#view_slipdetails').val(firstItem.slipdetails);
            $('#view_auditorremarks').val(firstItem.auditorremarks);
            $('#view_severityid').val(firstItem.severity);
            $('#view_minorobjectioncode').val(firstItem.subobjectionename);
            if (firstItem.liability == 'Y') {
                liability = 'Yes';

                $('#viewliabilityname_div').show();
                $('#view_liabilityname').val(firstItem.liabilityname);
            } else {
                liability = 'No';
                $('#viewliabilityname_div').hide();
                $('#view_liabilityname').val('');

            }
            $('input[name="view_liability"][value="' + liability + '"]').prop(
                'checked', true);
            const fileDetailsString = firstItem
                .filedetails_1; // Assuming this is the response field
            const fileDetailsArray = fileDetailsString.split(
                ','); // Split by comma for each file

            // alert(firstItem.filedetails_1);
            if (firstItem.filedetails_1) {
                $('#file-list-container').show()
            }


            const files = fileDetailsArray.map((fileDetail, index) => {
                const [name, path, size, fileuploadid] = fileDetail.split(
                    '-'); // Split by hyphen
                return {
                    id: index +
                        1, // Example ID (you can use an actual ID if available)
                    name: name,
                    path: path,
                    size: size,
                    fileuploadid: fileuploadid,
                };
            });
            view_files(files)

            const auditorremarksdata = JSON.parse(firstItem.auditorremarks);
            auditorremarks = auditorremarksdata.content;

            view_editor.setData(auditorremarks);
            // view_editor.isReadOnly = true;

            //                            view_majorobjectioncode



        }

        /*********************************************** Fetch Data *******************************************/




        function renderFileList(files) {
            const fileListContainer = $('#file-list-container');
            fileListContainer.empty(); // Clear previous file cards

            files.forEach(file => {
                $('#fileuploadid').val(file.fileuploadid);
                const fileCard = `
          <div class="card overflow-hidden mb-3" id="file-card-${file.id}">
          <input type="hidden" id="fileuploadid_${file.id}" name="fileuploadid_${file.id}" value="${file.fileuploadid}" >
              <div class="d-flex flex-row">
                  <div class="p-2 align-items-center">
                      <h3 class="text-danger box mb-0 round-56 p-2">
                          <i class="ti ti-file-text"></i>
                      </h3>
                  </div>
                  <div class="p-3">
                      <h3 class="text-dark mb-0 fs-4">
                          <!-- Add an anchor tag to open the file in a new tab -->
                          <a style="color:black;" href="/storage/${file.path}" target="_blank">${file.name}</a> </h3>
                  </div>

                  <div class="p-3 align-items-center ms-auto">
                      <button class="text-danger box mb-0" onclick="deleteFile(${file.id}, event)">
                          <i class="ti ti-trash"></i> Delete
                      </button>
                  </div>
              </div>
          </div>
      `;

                // <div class="p-3 align-items-center ms-auto">
                //     <a href="/files/download/${file.id}" class="text-primary box mb-0">
                //         <i class="ti ti-download"></i> Download
                //     </a>
                // </div>
                fileListContainer.append(fileCard); // Add the file card to the container
            });
        }


        function view_files(files) {


            const fileListContainer = $('#view_file-list-container');
            fileListContainer.empty(); // Clear previous file cards

            files.forEach(file => {
                $('#fileuploadid').val(file.fileuploadid);
                const fileCard = `
                 <label
                                                                        class="form-label required"
                                                                        for="validationDefaultUsername">Attachments</label>
          <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
              <div class="d-flex flex-row">
                  <div class="p-2 align-items-center">
                      <h3 class="text-danger box mb-0 round-56 p-2">
                          <i class="ti ti-file-text"></i>
                      </h3>
                  </div>
                  <div class="p-3">
                      <h3 class="text-dark mb-0 fs-4">
                          <!-- Add an anchor tag to open the file in a new tab -->
                          <a style="color:black;" href="/storage/${file.path}" target="_blank">${file.name}</a> </h3>
                  </div>


              </div>
          </div>
      `;

                // <div class="p-3 align-items-center ms-auto">
                //     <a href="/files/download/${file.id}" class="text-primary box mb-0">
                //         <i class="ti ti-download"></i> Download
                //     </a>
                // </div>
                fileListContainer.append(fileCard); // Add the file card to the container
            });
        }

        // Function to delete a file
        function deleteFile(fileId, event) {
            event.preventDefault(); // Prevents page refresh

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

            // // Optionally, remove the file ID from activefileid (if necessary)
            // var activeFileIds = $('#active_fileid').val().split(',');
            // activeFileIds = activeFileIds.filter(function(id) {
            //     return id != fileId;
            // });
            // $('#active_fileid').val(activeFileIds.join(','));


            // // Get the current deactivefileid value and ensure it is an array
            // var deactiveFileIds = $('#deactive_fileid').val().split(',').filter(function(id) {
            //     return id !== ''; // Remove empty values (in case there's a leading comma)
            // });

            // // Add the file ID to deactivefileid if not already present
            // if (!deactiveFileIds.includes(fileId.toString())) {
            //     deactiveFileIds.push(fileId);
            // }

            // // Join the array with commas and update the deactive_fileid hidden input field
            // $('#deactive_fileid').val(deactiveFileIds.join(','));
            $('#upload_file').show();
            $('#fileuploadstatus').val('Y');

        }

        /**************************************** Fit the upload files, delete upload file in edit **********************/


        /*************************************************  Audit Tab Functions *********************************************/

        /*************************************************  Work  All *********************************************/

        /*********************************************** Date Picker*******************************************/
        function datepicker(value, setdate) {
            var today = new Date();
            if (value == 'exit_date') 
            {
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);
                if(entry_date >= today)
                {
                    var minDate = new Date(entry_date);
                }else
                {
                    var minDate = today;   
                }
                
            }else if(value == 'entry_date')
            {
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);
                var minDate = today;   
            }



            // Format the dates to dd/mm/yyyy format
            var minDateString = formatDate(minDate); // Format date to dd/mm/yyyy
            var maxDateString = formatDate(maxDate); // Format date to dd/mm/yyyy

            init_datepicker(value, minDateString, maxDateString, setdate)
        }

        const $exit_form = $("#exit_form");

        // Validation rules and messages
        $exit_form.validate({
            rules: {
                exit_date: {
                    required: true,
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

            },
            messages: {
                exit_date: {
                    required: 'Select Exit Date'
                }
            }
        });

        function scrollToFirstError() {
            const firstError = $exit_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }

        const $entrymeetingform = $("#entrymeetingform");

        $entrymeetingform.validate({
            rules: {
                entry_date: {
                    required: true,
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

            },
            messages: {
                entry_date: {
                    required: 'Select Entry Meet Date'
                }
            }
        });
        

        $(document).on('click', '#entrymeet_confirm_btn', function(event) {
            event.preventDefault(); // Prevent form submission
            // Check if the error message is visible
            if ($('#display_error').is(':visible')) {
                return; // Exit the function to prevent form submission
            }


            if ($entrymeetingform.valid()) 
            {
                var formData = $entrymeetingform.serialize();

                $.ajax({
                    url: '/audit/update_entrymeet', // For creating a new user or updating an existing one
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {

                            passing_alert_value('Confirmation', response.success,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
                            $('#entrymeet_confirm_btnDIV').hide();
                            $('#statusmessage_entrymeet').show();
                            var data = response.data;
                            datepicker('entry_date', convertDateFormatYmd_ddmmyy(data.entrymeetdate));
                            $('#entry_date').prop('disabled', true);

                            // fetchAlldata();
                            //     reset_form();



                        } else if (response.error) {}
                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);

                        var errorMessage = response.error ||
                            'An unknown error occurred';

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');


                        // Optionally, log the error to console for debugging
                        console.error('Error details:', xhr, status, error);
                    }
                });
                

            } else {
                scrollToFirstError();
            }
        });


        $(document).on('click', '#confirm_btn', function(event) {
            event.preventDefault(); // Prevent form submission
            // Check if the error message is visible
            if ($('#display_error').is(':visible')) {
                return; // Exit the function to prevent form submission
            }


            if ($exit_form.valid()) {
                var confirmation = 'Are you sure to confirm the Exit Meeting Date?';
                document.getElementById("process_button").onclick = function() {
                   // update_schedule()
                };


                var superchecklists = '<?php echo $supercheckquestions; ?>';

                // Parse the superchecklists JSON string into a JavaScript object
                let parsedSuperchecklists = JSON.parse(superchecklists);

                // Group the data by part_no
                let groupedData = parsedSuperchecklists.reduce((acc, question) => {
                    if (!acc[question.part_no]) {
                        acc[question.part_no] = [];
                    }
                    acc[question.part_no].push(question);
                    return acc;
                }, {});

                var auditscheduleid = '<?php echo $auditscheduleid; ?>';
                var auditplanid = '<?php echo $auditplanid; ?>';

                let htmlContent =
                    '<div class="scrolldiv"><form id="quesans_form"><input type="hidden" name="auditscheduleid"  value="' +
                    auditscheduleid + '" class=""/><input type="hidden" name="auditplanid" value="' + auditplanid +
                    '" class=""/>';

                // Loop over the groupedData object
                for (let partNo in groupedData) {
                    if (groupedData.hasOwnProperty(partNo)) {
                        let partQuestions = groupedData[partNo];

                        // Create a new section for this part_no with its heading
                        htmlContent +=
                            `<div class="card card_border hide_this" id="step5" style="display: block;"><div class="card-header card_header_color">${partQuestions[0].heading_en}</div><div class="card-body">`;

                        // Start the table structure
                        htmlContent += `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">S.No</th>
                                    <th width="70%">Question</th>
                                    <th width="25%">Answer</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                        // Loop through all questions for the current part_no
                        partQuestions.forEach(function(question, index) {
                            // Start the row for each question
                            htmlContent += `<tr>`;

                            // Add the serial number and question
                            htmlContent += `
                            <td>${question.sl_no}</td>
                            <td>${question.checkpoint_en}</td>
                            <td>
                        `;

                            // Check question type and add the appropriate input for the answer
                            if (question.question_type === 'O') {
                                // For "Yes/No" question type, display radio buttons with Bootstrap styling
                                htmlContent += `
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" name="answer_remarks[${question.supercheckid}]" type="radio" checked name="question_${question.supercheckid}" id="yes_${question.supercheckid}" value="Yes">
                                    <label class="form-check-label" for="yes_${question.supercheckid}">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" name="answer_remarks[${question.supercheckid}]" type="radio" name="question_${question.supercheckid}" id="no_${question.supercheckid}" value="No">
                                    <label class="form-check-label" for="no_${question.supercheckid}">No</label>
                                </div>
                            `;
                            } else if (question.question_type === 'N') {
                                // For "Text" question type, display a text input box
                                htmlContent += `
                                <input type="number" name="answer_remarks[${question.supercheckid}]" class="form-control" name="question_${question.supercheckid}" placeholder="Enter answer">
                            `;
                            } else if (question.question_type === 'D') {
                                // For "Text Area" question type, display a text area
                                htmlContent += `
                                <textarea name="answer_remarks[${question.supercheckid}]" class="form-control" name="question_${question.supercheckid}" rows="4" style="height: 30px;" placeholder="Enter answer"></textarea>
                            `;
                            }

                            // Close the answer cell and row
                            htmlContent += `</td></tr>`;
                        });

                        // End the table
                        htmlContent += `
                            </tbody>
                        </table>
                    `;


                        htmlContent += `</div></div>`;
                    }
                }
                htmlContent += `</form></div>`;

                passing_large_alert('Confirmation', htmlContent, 'large_confirmation_alert',
                    'large_alert_header',
                    'large_alert_body', 'forward_alert');
                $("#large_modal_process_button").html("Submit");
                $("#large_modal_process_button").addClass("exitmeetbtn");
                $('#large_modal_process_button').removeAttr('data-bs-dismiss');
                return;
                /* passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                     'alert_body', 'forward_alert');*/

            } else {
                scrollToFirstError();
            }
        });

        $('#large_modal_process_button').on('click', function() {
            update_schedule();
            var formData = $('#quesans_form').serialize();

            $.ajax({
                url: '/Supercheck_QuesAns', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                success: function(response) {
                    passing_alert_value('Confirmation', 'Superchecklist added successfully!',
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');

                    $('#large_confirmation_alert').hide();
                    location.reload();
                }
            });


        });

        function update_schedule() {
            var formData = $('#exit_form').serialize();

            $.ajax({
                url: '/audit/update_exitmeet', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {

                        passing_alert_value('Confirmation', response.success,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        $('#cnfrm_btn').hide();
                        $('#statusmessage').show();
                        var data = response.data;
                        datepicker('exit_date', convertDateFormatYmd_ddmmyy(data.exitmeetdate));
                        $('#exit_date').prop('disabled', true);

                        // fetchAlldata();
                        //     reset_form();



                    } else if (response.error) {}
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.error ||
                        'An unknown error occurred';

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');


                    // Optionally, log the error to console for debugging
                    console.error('Error details:', xhr, status, error);
                }
            });
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        let entry_date;
        let exitdate;
        let entrydatefetch;

        function fetchallWorkdetail() {

if ($.fn.dataTable.isDataTable('#workallocationtable')) {
    $('#workallocationtable').DataTable().clear().destroy();
}


var teamhead = '<?php echo $teamhead; ?>';


$('#workallocationtable').DataTable({
    processing: true,
    serverSide: false,
    lengthChange: false,
    autoWidth: false,
    ajax: {
        url: "/fetchAllWorkData", // Your API route for fetching data
        type: "POST",
        data: {
            auditscheduleid: '<?php echo $auditscheduleid; ?>',
            teamhead: '<?php echo $teamhead; ?>',
            userid: '<?php echo $schteammemberid; ?>'
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
        },
        dataSrc: function(json) {
            if (json.workallc_status == 'Y') {
                $('#workallcstatusDiv').hide();
                if (json.data && json.data.length > 0) {

                    $('#workallocTable').show();
                    // Preprocess data: Group by username
                    entry_date = new Date(json.data[0].entrymeetdate);
                    exitdate = json.data[0].exitmeetdate;

                    // alert(entry_date);
                    let groupedData = Object.values(
                        json.data.reduce((acc, row) => {
                            if (!acc[row.username]) {
                                acc[row.username] = {
                                    username: row.username,
                                    auditPeriod: new Set(), // Use Set for unique periods
                                    groupName: row.groupename,
                                    workNames: new Set(), // Use Set for unique work names
                                    encrypted_schteammemberid: row
                                        .encrypted_schteammemberid,
                                    statusflag: row.statusflag,
                                    majorworkallocationtypeid: row
                                        .majorworkallocationtypeid,
                                };
                            }

                            // Add audit period
                            let fromdate = row.fromdate ?
                                new Date(row.fromdate).toLocaleDateString('en-GB') :
                                "N/A";
                            let todate = row.todate ?
                                new Date(row.todate).toLocaleDateString('en-GB') :
                                "N/A";
                            // Add the audit period to the Set
                            acc[row.username].auditPeriod.add(`${fromdate} - ${todate}`);

                            // Add the work name to the Set
                            acc[row.username].workNames.add(row.majorworkallocationtypeename);

                            return acc;
                        }, {})
                    );
                    groupedData = groupedData.map(item => ({
                        ...item,
                        auditPeriod: Array.from(item.auditPeriod),
                        groupName: item.groupName,
                        workNames: Array.from(item.workNames),
                    }));

                    $('#tableshow').show();
                    $('#workallocationtable_wrapper').show();
                    $('#no_data').hide(); // Hide custom "No Data" message
                    return groupedData;
                } else {
                    $('#workallcstatusDiv').hide();
                    $('#tableshow').hide();
                    $('#workallocationtable_wrapper').hide();
                    $('#no_data').show(); // Show custom "No Data" message
                    return [];
                }
            } else {
                $('#workallcstatusDiv').show();
                $('#workallocTable').hide();

            }
        },
    },
    columns: [{
            data: null, // Serial number column
            render: function(data, type, row, meta) {
                return meta.row + 1; // Serial number starts from 1
            }
        },
        {
            data: 'username', // Show username
            visible: teamhead !== 'N'
        },
        {
            data: 'auditPeriod', // Display audit periods
            render: function(data) {
                return data.map(period => `<span>${period}</span>`).join(
                    '<br>'); // Line-by-line display
            }
        },

        {
            data: 'groupName', // Display work names
            render: function(data) {
                return `<span>${data}</span>`;

            }
        },
        {
            data: 'workNames', // Display work names
            render: function(data) {
                return data.map(work => `<span>${work}</span>`).join(
                    '<br>'); // Line-by-line display
            }
        },

    ],
    columnDefs: [{
            targets: 0, // Serial number column
            width: "5%" // Adjust width as needed
        },
        {
            targets: 1, // Username column
            width: "20%" // Adjust width as needed
        },
        {
            targets: 2, // Audit period column
            width: "25%" // Adjust width as needed
        },
        {
            targets: 3, // Work names column
            width: "25%" // Adjust width as needed
        },
        {
            targets: 4, // Actions column
            width: "25%" // Adjust width as needed
        }
    ]
});

// var table = $('#workallocationtable').DataTable({
//     "processing": true,
//     "serverSide": false,
//     "lengthChange": false,
//     "ajax": {
//         "url": "/fetchAllWorkData", // Your API route for fetching data
//         "type": "POST",
//         "data": {
//             'auditscheduleid': '<?php echo $auditscheduleid; ?>',
//             'teamhead': '<?php echo $teamhead; ?>',
//             'userid': '<?php echo $schteammemberid; ?>'
//         },
//         "headers": {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
//         },
//         "dataSrc": function(json) {
//             if (json.data && json.data.length > 0) {
//                 $('#tableshow').show();
//                 $('#workallocationtable_wrapper').show();
//                 $('#no_data').hide(); // Hide custom "No Data" message
//                 return json.data;
//             } else {
//                 $('#tableshow').hide();
//                 $('#workallocationtable_wrapper').hide();
//                 $('#no_data').show(); // Show custom "No Data" message
//                 return [];
//             }
//         }
//     },
//     "columns": [{
//             "data": null, // Serial number column
//             "render": function(data, type, row, meta) {
//                 return meta.row + 1; // Serial number starts from 1
//             }
//         },
//         {
//             "data": 'username', // Serial number column
//             "visible": teamhead !== 'N'

//         },
//         {
//             "data": "null",
//             "render": function(data, type, row) {
//                 // Convert DOB to dd-mm-yyyy format
//                 let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
//                         'en-GB') :
//                     "N/A";
//                 let todate = row.todate ? new Date(row.todate).toLocaleDateString(
//                         'en-GB') :
//                     "N/A";

//                 return ` ${fromdate} - ${todate}`;
//             }
//         },
//         {
//             "data": "majorworkallocationtypeename", // Serial number column

//         },

//         {
//             "data": "encrypted_schteammemberid", // Use the encrypted deptuserid
//             "visible": teamhead !== 'N',
//             "render": function(data, type, row) {
//                 if (row.statusflag === 'Y') {
//                     // Check if statusflag is 'N'
//                     return `<center>
//     <a style="color:black;" class="btn editicon edit_btn" id="${data}" major_id="${row.majorworkallocationtypeid}">
//         <i class="ti ti-edit fs-4"></i>
//     </a>
// </center>`;
//                 } else {
//                     // Otherwise, show the Finalize button
//                     return `<center>
//     <button class="btn btn-primary finalize_btn" id="${data}">
//         Finalized
//     </button>
// </center>`;
//                 }
//             }
//         },

//     ]
// });

}
        /**Download PDF File */
        // function downloadFile(filename) {
        //     var language = window.localStorage.getItem('lang');
        //     // Add language as a query string parameter to the file URL
        //     let fileWithLanguage = '/' + filename + '?lang=' + language;
        //     // Trigger download by navigating to the URL
        //     window.location.href = fileWithLanguage;
        // }
        function downloadFile(filename) {
            var language = window.localStorage.getItem('lang');
            // Add language as a query string parameter to the file URL
            let fileWithLanguage = '/' + filename + '?lang=' + language + '&auditscheduleid=' + '<?php echo $auditscheduleid; ?>';
            // Trigger download by navigating to the URL
            window.location.href = fileWithLanguage;
        }
    </script>
@endsection
