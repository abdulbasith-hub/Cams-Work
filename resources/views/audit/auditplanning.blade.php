@extends('index2')
@section('content')
@section('title', ' Audit Planning')

<style>
    .hiddenbtns {
        display: none;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .largemodal td {
        padding: 12px;
        /* Adds 10px of padding on all sides of each cell */
        border: 1px solid #ddd;
        /* Optional: Add a border for visibility */
    }


    #schedule_allocatedwork_wrapper {
        width: 100%;
        /* Allow full-width table */
        overflow-x: auto;
        /* Enable horizontal scrolling */
    }

    #user_detail_table_wrapper {
        width: 100%;
        /* Allow full-width table */
        overflow-x: auto;
        /* Enable horizontal scrolling */
    }

    /* .table th {
        background-color: #6b6b6c;
        color: #fff;

    } */
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
</style>

@include('common.alert')

@php

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<form id="user_detailform" name=user_detailform>
    {{-- @csrf --}}
    <input type='hidden' value="{{ $dept_det->deptcode ?? '' }}" id="deptcode" name="deptcode">
    <input type='hidden' value="{{ $dist_det->distcode ?? '' }}" id="distcode" name="distcode">
    <input type='hidden' value="{{ $quarter_det[0]->auditquartercode ?? '' }}" id="quarter_code" name="quarter_code">
</form>

<div class="col-12">
    <div class="justify-content-center hide_this" id="user_det">

        <div class="card card_border  ">
            <ul class="nav nav-pills nav-fill mt-4 m-2 p-2" role="tablist" style="border: 1px solid #7198b9;">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#navpill-111" role="tab">
                        <span>
                            <i class="ti ti-user fs-4"></i>
                        </span>
                        <span class="lang" key="userdetails">User Details</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#navpill-222" role="tab">
                        <span>
                            <i class="ti ti-building fs-4"></i>
                        </span>
                        <span class="lang" key="audit_office">Audit Office</span>
                    </a>
                </li>

            </ul>
            <!-- Tab panes -->
            <div class="tab-content border mt-2">
                <div class="tab-pane active p-3" id="navpill-111" role="tabpanel">
                    <div class="row">
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
                        <hr class="mt-2 mb-2">
                        <div class="datatables">
                            <div class="table-responsive " id="tableshow">
                                <table id="user_detail_table"
                                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                    <thead class="">
                                        <th class="lang text-center align-middle" key="s_no">S.No</th>
                                        <th class="lang " key="dept">Department</th>
                                        <th class="lang " key="username">User Name</th>
                                        <th class="lang " key="designation">Designation</th>
                                    </thead>
                                    <tbody></tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane p-3" id="navpill-222" role="tabpanel">
                    <div class="col-md-6 mx-end">
                        <div class="form-group row">
                            <label class="form-label text-end col-md-3 lang" key5="audit_office_count">Institute
                                Count:</label>
                            <div class="col-md-9">
                                <p id="instcount"></p>
                            </div>
                        </div>
                    </div>


                    <div class="card-body" id="inst_Details">
                        <div class=" table-responsive rounded-2 border ">
                            <table class="table instTable">
                                <thead class="">
                                    <tr>
                                        <th class="lang userCount_head" key="s_no">S No</th>
                                        <th class="lang userCount_head" key="audit_office">Institute
                                        </th>
                                        <th class="lang userCount_head" key="">Category</th>
                                    </tr>
                                </thead>
                                <tbody id="instTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="div" id="verify_btn">
            <div class="col-md-8 mt-4 ">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="exampleRadios" id="usercheck"
                        value="option1">
                    <label class="form-check-label lang" for="exampleRadios1" key="userverify_tcs">
                        <b> The users listed above are available for allocation in the plan.<b>
                    </label>
                </div>
            </div>
            <div class="col-md-2 mx-auto d-flex ">
                <button type="button" id="check_user" key="verify_btn"
                    class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center lang">
                    Verify
                </button>
            </div>
        </div>
    </div>











    <div class="col-12 align-items-center">
        <div class="card card_border hide_this " id="automate">
            <div class="card-header card_header_color lang " key="automate_plan">Automate Audit Plan</div>
            <div class="card-body min-vh-50 align-items-center">
                <div class="col-md-2 mx-auto d-flex ">
                    <button type="button" id="plan_btn" key="automate_plan_btn"
                        class="justify-content-center w-100 btn mb-1 btn-rounded btn-warning d-flex align-items-center lang">
                        <i class="ti ti-settings-automation fs-4 me-2 "></i>
                        Audit Plan
                    </button>
                </div>

                {{-- <button type="button" class="btn btn-success me-1 mb-1 px-2" onclick="importData()">
                    <i class="ti ti-paperclip fs-7"></i>
                </button> --}}



            </div>
        </div>
    </div>
    <div class="div">

        <div id="autoplan_modal" class="modal fade" tabindex="-1" aria-labelledby="primary-header-modalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary" style=" justify-content: center;">
                        <h4 class="modal-title text-white lang large_alert_header" id="primary-header-modalLabel"
                            key="confirmation">

                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form name="audit_autoplan" id="audit_autoplan">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="dept" for="validationDefault01">
                                        Department
                                    </label>
                                    {{-- <input type="hidden" class="form-control" id="dept_code" name="dept_code"
                                        value="{{ $dept_det->deptcode ?? '0' }}" /> --}}

                                    <select class="form-select mr-sm-2 lang-dropdown" id="dept_code" name="dept_code"
                                        disabled>
                                        <option value=" {{ $dept_det->deptcode }}" selected
                                            data-name-en="{{ $dept_det->deptelname }}"
                                            data-name-ta="{{ $dept_det->depttlname }}"></option>
                                    </select>


                                </div>
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="district" for="validationDefault01">

                                    </label>
                                    {{-- <input type="hidden" class="form-control" id="dist_code" name="dist_code"
                                        value="{{ $dist_det->distcode ?? '0' }}" /> --}}
                                    {{-- <input type="text" class="form-control" id="dist_name" name="dist_name"
                                        value="<?php echo $dist_det->disttname; ?>"required disabled /> --}}
                                    <select class="form-select mr-sm-2 lang-dropdown" id="dist_code" name="dist_code"
                                        disabled>
                                        <option value=" {{ $dist_det->distcode }}" selected
                                            data-name-en="{{ $dist_det->distename }}"
                                            data-name-ta="{{ $dist_det->disttname }}"></option>
                                    </select>

                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required lang" for="validationDefault01"
                                        key="typeofauditename">

                                    </label>
                                    <select class="form-select mr-sm-2 lang-dropdown" id="quarter_codee"
                                        name="quarter_codee">
                                        <option value="" data-name-en="--Select Quarter--"
                                            data-name-ta="--காலாண்டைத் தேர்ந்தெடுக்கவும்--">
                                        </option>
                                        @foreach ($quarter_det as $quarter)
                                        <option value=" {{ $quarter->auditquartercode }}"
                                            @if (old('quarter_det', $quarter)==$quarter->auditquartercode) selected @endif
                                            data-name-en="{{ $quarter->auditquarter }}"
                                            data-name-ta="{{ $quarter->auditquartertname }}">


                                            <!-- Display any field you need -->
                                        </option>
                                        @endforeach
                                    </select>
                                    <div id="auditquarter-error" class="text-danger form-error"
                                        style="display: none;">
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success lang" key="ok"
                            id="large_modal_process_button" onclick=" checkforvalidation()"></button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            id="large_modal_cancel_button"><span class="lang" key='cancel'>Cancel</span></button>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <div class="col-12">
        <div class="card card_border mt-2 hide_this team_Inst_Details_table" id="team_Inst_Details_table">
            <div class="card-header card_header_color lang" key="autoplan_head">Audit Team and Institiute Details
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
        <div class="col-md-2 mx-auto   team_Inst_Details_div hide_this">
            <button type="button" id="finalize_plan" key="final_btn"
                class="justify-content-center w-100 btn mb-1 btn-rounded btn-warning d-flex align-items-center lang">
                <i class="ti ti-checks fs-4 me-2 "></i>
                Finalise
            </button>
        </div>
    </div>



    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
    <script>
        var lang = window.localStorage.getItem('lang');

        function changeLanguage(selectedLang) {

            $(".lang").each(function() {
                let key = $(this).attr("key");
                if (arrLang[lang][key] && arrLang[lang][key]) {
                    $(this).text(arrLang[lang][key]);
                }
            });


        }



        $("#translate").change(function() {
            lang = $(this).val();
            updateTableLanguage(lang);
            // updateValidationMessages(getLanguage('Y'), 'audit_autoplan');


        });


        // function handleConfirmation(isChecked, message, callback) {
        //     if (isChecked) {
        //         // Show confirmation alert
        //         passing_alert_value('Confirmation', message, 'confirmation_alert', 'alert_header',
        //             'alert_body', 'forward_alert');

        //         // Ensure button click is handled only once
        //         $("#process_button").off("click").on("click", function(event) {
        //             event.preventDefault(); // Prevent form submission if inside a form
        //             $('#confirmation_alert').modal('hide'); // Close modal if needed

        //             // Execute the callback
        //             if (typeof callback === "function") {
        //                 callback();
        //             }
        //         });
        //     } else {
        //         // Show alert and close modal without executing callback
        //         passing_alert_value('Confirmation', 'Please ensure the listed user details are correct!',
        //             'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');

        //         $("#process_button").off("click").on("click", function(event) {
        //             event.preventDefault();
        //             $('#confirmation_alert').modal('hide'); // Just close the modal
        //         });
        //     }
        // }

        // Ensure event is passed explicitly
        $('#check_user').on("click", function(event) {
            event.preventDefault();

            var isChecked = $('#usercheck').prop('checked');
            if (isChecked) {

                $("#process_button").off("click").on("click", function(event) {
                    event.preventDefault(); // Prevent form submission if inside a form


                    checkfordetails();

                    $('#confirmation_alert').modal('hide'); // Close modal if needed


                });

                getLabels_jsonlayout([{
                    id: 'userverified',
                    key: 'userverified'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .userverified, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });
                // passing_alert_value('Confirmation', message, 'confirmation_alert', 'alert_header',
                //     'alert_body', 'forward_alert');

                // Ensure button click is handled only once

            } else {
                getLabels_jsonlayout([{
                    id: 'usernotverified',
                    key: 'usernotverified'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .usernotverified, 'confirmation_alert',
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
            }
            // handleConfirmation(isChecked, 'Are you sure to continue? Once verified cannot be revoked!',
            //     checkfordetails);
        });


        function checkfordetails() {

            $('#user_det').hide();

            var formData = $('#user_detailform').serializeArray();

            $.ajax({
                url: '/audit/checkfordetails', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,

                success: function(response) {
                    if (response.success) {
                        var audituserpla_status = response.audit_plan_status[0];
                        var userStatus = audituserpla_status.userverified;

                        if (userStatus == 'Y') {
                            $("#user_det").hide();
                            $("#automate").show();
                            $("#no_data").removeClass("hide_this"); // Show 'No Data Available' message
                            $("#tableshow").addClass("hide_this");
                        } else {

                            $('#user_det').show()
                            let rowDiv = document.getElementById("automate");
                            rowDiv.classList.add("hide_this");
                            renderTable(lang, auditors_det)


                        }

                    } else if (response.error) {}
                },
                error: function(xhr, status, error) {

                    $("#user_det").show();
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        var response = JSON.parse(xhr.responseText);

                        var errorMessage = response.error ||
                            'An unknown error occurred';

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');

                        // getLabels_jsonlayout([{
                        //     id: response.message,
                        //     key: response.message
                        // }], 'N').then((text) => {
                        //     let alertMessage = Object.values(text)[0] ||
                        //         "Error Occured";
                        //     passing_alert_value('Confirmation', alertMessage,
                        //         'confirmation_alert', 'alert_header',
                        //         'alert_body', 'confirmation_alert');
                        // });
                    }
                }
            });


        }


        $('#plan_btn').on("click", function() {

            $("#autoplan_modal").modal("show");
            $("#auditquarter-error").html('').hide();

        });


        function checkforvalidation() {
            const lang = getLanguage('')
            event.preventDefault();
            const quarter_code = $("#quarter_codee").val();

            if (!quarter_code) {
                let msg = `${lang=='ta'?'காலாண்டைத் தேர்ந்தெடுக்கவும்':'Please Select Quarter'}`;
                $("#auditquarter-error").html(msg).show();
                return;
            } else {
                $("#autoplan_modal").modal("hide");
                callforautomateplan()
            }

            // }
        }
        // }).catch(error => {
        //     console.error("Failed to load JSON data:", error);
        // });
        // const $audit_autoplan = $("#audit_autoplan");



        function scrollToFirstError() {
            const firstError = $audit_autoplan.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }

        function callforautomateplan() {




            $("#process_button").off("click").on("click", function(event) {
                event.preventDefault(); // Prevent form submission if inside a form
                $("#large_confirmation_alert").modal("hide");

                // Execute the callback
                get_insertdata('insert');
            });


            getLabels_jsonlayout([{
                id: 'automate_ques',
                key: 'automate_ques'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .automate_ques, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            // $("#large_modal_process_button").html(ok);

        }
        // }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let auditors_det;

        function get_insertdata(action) {

            document.getElementById("process_button").disabled = true;
            var formData = $('#user_detailform').serializeArray();
            // console.log(formData);
            // return;


            $.ajax({
                url: '/audit/automate_plan', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,

                success: function(response) {
                    if (response.success) {
                        document.getElementById("process_button").disabled = false;


                        getLabels_jsonlayout([{
                            id: response.success,
                            key: response.success
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        // passing_alert_value('Confirmation', response.success,
                        //     'confirmation_alert', 'alert_header', 'alert_body',
                        //     'confirmation_alert');

                        auditors_det = response.auditors;
                        // table.ajax.reload(); // Reload the table
                        // Populate table rows dynamically
                        if (auditors_det.length > 0) {

                            $('#user_det').hide();
                            $('#automate').hide();
                            $("#no_data").addClass("hide_this"); // Hide 'No Data Available' message
                            $("#tableshow").removeClass("hide_this"); // Show the table

                            let elements = document.querySelectorAll('.team_Inst_Details_table');
                            elements.forEach(function(element) {
                                element.classList.remove('hide_this');
                            });

                            let btn = document.querySelectorAll('.team_Inst_Details_div');
                            btn.forEach(function(element) {
                                element.classList.remove('hide_this');
                            });



                            // let tableDiv = document.getElementById("team_Inst_Details_div");
                            // tableDiv.classList.remove("hide_this");
                            // Initialize or destroy and reinitialize DataTable
                            fetchAuditorsTable(lang);

                        } else {
                            $("#automate").show();
                            $("#no_data").removeClass("hide_this"); // Show 'No Data Available' message
                            $("#tableshow").addClass("hide_this"); // Hide the table
                        }

                    } else if (response.error) {
                        document.getElementById("process_button").disabled = false;
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        var errorMessage = response.error ||
                            'An unknown error occurred';
                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    }




                }
            });
        }
        $(document).on('click', '#finalize_plan', function() {




            // var confirmation = 'Are you sure to Finalize the Randomized detail?';
            // document.getElementById("process_button").onclick = function() {
            //     finalise();

            // };
            $("#process_button").off("click").on("click", function(event) {
                event.preventDefault(); // Prevent form submission if inside a form
                $('#confirmation_alert').modal('hide');

                // Execute the callback
                finalise();
            });

            getLabels_jsonlayout([{
                id: 'finalise_random',
                key: 'finalise_random'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .finalise_random, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            // passing_alert_value(confirmation, final_msg, 'confirmation_alert', 'alert_header',
            //     'alert_body', 'forward_alert');
            // $("#process_button").html(ok);
        });

        function finalise() {
            document.getElementById("process_button").disabled = true;

            var formData = $('#user_detailform').serializeArray();
            $.ajax({
                url: '/audit/finalize_data', // Your API route to get user details
                method: 'POST',
                data: formData, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        document.getElementById("process_button").disabled = false;

                        $('.team_Inst_Details_div').hide();

                        getLabels_jsonlayout([{
                            id: response.success,
                            key: response.success
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        // passing_alert_value('Confirmation', response.success,
                        //     'confirmation_alert', 'alert_header', 'alert_body',
                        //     'confirmation_alert');
                        fetchAlldata(lang);

                        // $('#display_error').hide();
                        // change_button_as_update('audit_schedule', 'action', 'buttonaction',
                        //     'display_error', '', '', 'update');


                        // validator.resetForm();

                        const inst = response.data; // The array of schedule data



                    } else {
                        document.getElementById("process_button").disabled = false;

                        alert('Finalise Data not found');
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        var errorMessage = response.error ||
                            'An unknown error occurred';

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    }


                }
            });
        }

        function renderTable(language, dataFromServer) {
            const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
            const userColumn = language === 'ta' ? 'usertamilname' : 'username';
            const desigColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

            // Ensure dataFromServer exists
            if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
                console.error("No data available for DataTable.");
                return;
            }

            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#user_detail_table')) {
                $('#user_detail_table').DataTable().clear().destroy();
            }

            // Initialize DataTable
            table = $('#user_detail_table').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                autoWidth: false,
                data: dataFromServer,
                initComplete: function() {
                    $("#user_detail_table").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                        },
                        className: 'text-end',
                        type: "num"
                    },
                    {
                        data: null,
                        title: columnLabels?.[departmentColumn]?.[language],
                        render: function(data, type, row) {
                            return row?.[departmentColumn] || '-';
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: userColumn,
                        title: columnLabels?.[userColumn]?.[language],
                        render: function(data, type, row) {
                            return row?.[userColumn] || '-';
                        },

                        className: "text-start text-wrap"
                    },
                    {
                        data: null,
                        title: columnLabels?.[desigColumn]?.[language],
                        render: function(data, type, row) {
                            return row?.[desigColumn] || '-';
                        },

                        className: "text-start d-none d-md-table-cell extra-column text-wrap"
                    },

                ]
            });

            // Mobile column handling
            const mobileColumns = [
                userColumn, desigColumn
            ];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "user_detail_table");


        }

        function fetchAlldata(lang) {
            // alert();
            // alert(auditors_det);
            // alert();
            $('#user_det').hide();
            // const lang = getLanguage('');
            // var checkparam = checkparam;
            $.ajax({
                url: '/audit/fetchall_automatedata', // For creating a new user or updating an existing one
                type: 'POST',
                // data: {
                //     checkparam: checkparam
                // },

                success: function(response) {
                    if (response.success) {
                        auditors_det = response.planned_auditors;
                        audit_plan_status = response.audit_plan_status;
                        var planStatus = audit_plan_status[0].autoplanstatus;
                        var userFlag = audit_plan_status[0].userverified;
                        const lang = getLanguage(' ')
                        if (userFlag == 'N' || userFlag == '' || userFlag == null) {

                            $('#user_det').show()
                            let rowDiv = document.getElementById("automate");
                            rowDiv.classList.add("hide_this");


                            auditors_det = auditors_det.users;
                            // Extract designation counts from the response


                            let institute_det = response.planned_auditors.inst_det;

                            let instList = document.getElementById("instcount");

                            instList.innerHTML = "";
                            instList.innerHTML =
                                institute_det.length

                            // institute_det.forEach(inst => {
                            //     let li = document.createElement("li");
                            //     li.className = "list-group-item inst_list-item";
                            //     li.textContent = lang == 'ta' ? inst.insttname : inst.instename;
                            //     instList.appendChild(li);
                            // });

                            let designationCounts = response.planned_auditors.designation_counts;
                            // Get the table body reference
                            let tableBody = document.getElementById("designationTableBody");



                            // Clear previous content
                            tableBody.innerHTML = "";

                            // Loop through designationCounts and add rows dynamically
                            designationCounts.forEach((desig, index) => {
                                let row = document.createElement("tr");
                                row.innerHTML = `
                             <td>${index + 1}</td>
                             <td class="lang">${lang=='ta'?desig.desigtlname:desig.desigelname}</td>
                             <td>${desig.count}</td>
                         `;
                                tableBody.appendChild(row);
                            });



                            let inst_tableBody = document.getElementById("instTableBody");
                            //  inst_tableBody.innerHTML = "";

                            institute_det.forEach((inste, index) => {
                                let row = document.createElement("tr");
                                row.innerHTML = `
                             <td>${index + 1}</td>
                             <td class="lang">${lang=='ta'?inste.insttname:inste.instename}</td>
                             <td>${lang=='ta'?inste.cattname:inste.catename}</td>
                         `;
                                inst_tableBody.appendChild(row);
                            });

                            renderTable(lang, auditors_det)

                        } else if ((planStatus == '' || planStatus == null || planStatus == 'N') && userFlag ==
                            'Y') {

                            $('#automate').show()
                            $("#no_data").addClass("hide_this"); // Hide 'No Data Available' message
                            $("#tableshow").removeClass("hide_this"); // Show the table
                        } else if (planStatus == 'Y' && userFlag == 'Y') {

                            $('#user_det').hide();
                            let btn = document.querySelectorAll('.team_Inst_Details_div');
                            btn.forEach(function(element) {
                                element.classList.remove('hide_this');
                            });
                            let rowDiv = document.getElementById("automate");
                            rowDiv.classList.add("hide_this");
                            $("#no_data").addClass("hide_this"); // Hide 'No Data Available' message
                            $("#tableshow").removeClass("hide_this"); // Show the table
                            let table = document.querySelectorAll('.team_Inst_Details_table');
                            table.forEach(function(element) {
                                element.classList.remove('hide_this');
                            });

                            // Prepare data for DataTable
                            fetchAuditorsTable(lang);
                        } else {

                            $('#user_det').hide();
                            $('.team_Inst_Details_div').hide();
                            let rowDiv = document.getElementById("automate");
                            rowDiv.classList.add("hide_this");
                            $("#no_data").addClass("hide_this"); // Hide 'No Data Available' message
                            $("#tableshow").removeClass("hide_this"); // Show the table
                            let table = document.querySelectorAll('.team_Inst_Details_table');
                            table.forEach(function(element) {
                                element.classList.remove('hide_this');
                            });

                            // Prepare data for DataTable
                            fetchAuditorsTable(lang);
                        }


                    } else if (response.error) {}
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        var errorMessage = response.error ||
                            'An unknown error occurred';

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');


                        // Optionally, log the error to console for debugging
                        console.error('Error details:', xhr, status, error);
                    }

                }
            });
        }

        function fetchAuditorsTable(lang) {



            if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
                $('#team_Inst_Details').DataTable().clear().destroy();
            }

            // Group auditors by their institution ID and separate team head and members
            let groupedAuditors = auditors_det.reduce((acc, auditor) => {
                if (!acc[auditor.instid]) {
                    acc[auditor.instid] = {
                        instid: auditor.instid,
                        instename: lang === 'ta' ? auditor.insttname : auditor
                            .instename, // Assuming instename is available
                        teamHead: null, // Placeholder for the team head
                        members: [] // Array for team members
                    };
                }

                // Check if the current user is the team head (orderid = 1)
                if (auditor.teamhead === 'Y') {
                    acc[auditor.instid].teamHead = auditor;
                } else {
                    acc[auditor.instid].members.push(auditor);
                }

                return acc;
            }, {});

            // Convert grouped data into rows for the table
            let tableData = Object.values(groupedAuditors).map((group, index) => {
                let teamHead = group.teamHead;
                let members = group.members;

                // Construct a string for team members
                let membersString = members
                    .map(member =>
                        `<span>${lang === 'ta'?member.usertamilname:member.username} (${lang === 'ta' ? member.desigtlname:member.desigelname || 'No Designation'})</span>`
                    )
                    .join("<br>");
                let teamHead_des = teamHead ?
                    `<span>${lang === 'ta'?teamHead.usertamilname:teamHead.username}(${lang === 'ta' ?teamHead.desigtlname:teamHead.desigelname})</span>` :
                    "No Team Head";

                return {
                    index: index + 1, // S.No
                    instename: group.instename || "No Institute Name", // Institute name
                    teamHead: teamHead_des, // Team head username
                    members: membersString || "No Members" // Team members
                };
            });

            // Display table if data exists



            // Check if DataTable already initialized
            if ($.fn.DataTable.isDataTable("#team_Inst_Details")) {
                // Clear existing table and re-add new data
                $('#team_Inst_Details').DataTable().clear().rows.add(tableData).draw();
            } else {
                // Initialize DataTable
                $("#user_detail_table_wrapper").hide();
                $('#schedule_allocatedwork_wrapper').show();

                $('#team_Inst_Details').DataTable({
                    data: tableData,

                    columns: [{
                            data: "index",
                            render: function(data, type, row, meta) {
                                return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                            },
                            className: 'text-end fw-normal',
                            type: "num"
                        },
                        {
                            data: "instename",
                            render: function(data) {
                                return data || '-';
                            },
                            className: 'text-wrap text-start fw-normal'
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
                        }
                    ],
                    "initComplete": function(settings, json) {
                        $("#audit_autoplan").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                    }

                });
                const mobileColumns = ["instename", "teamHead", "members"];
                setupMobileRowToggle(mobileColumns);

                updatedatatable(lang, "team_Inst_Details");
                // $(".dt-button").addClass("btn btn-primary lang").text(download);
            }
        }

        function updateTableLanguage(lang) {
            if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
                $('#team_Inst_Details').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable('#user_detail_table')) {
                $('#user_detail_table').DataTable().clear().destroy();
            }
            fetchAuditorsTable(lang);
            fetchAlldata(lang);
        }


        function setLanguage(language) {
            localStorage.setItem('lang', language);
            lang = language;
            updateTableLanguage(lang);
        }

        $(document).ready(function() {
            // Initialize language from local storage
            lang = localStorage.getItem('lang') || 'en';
            $('#translate').val(lang); // Set dropdown to stored language
            fetchAlldata(lang);
        });

        // function updateTableLanguage(lang) {
        //     if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
        //         $('#team_Inst_Details').DataTable().clear().destroy();
        //     }
        //     fetchAuditorsTable(lang);
        // }
        // $(document).ready(function() {
        //     // alert();
        //     var lang = getLanguage('')
        //     fetchAlldata(lang);
        // });
    </script>
    @endsection