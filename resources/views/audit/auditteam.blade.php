@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', ' Audit Team')

<style>
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

    .dataTables_scrollBody thead tr {
        visibility: collapse;
    }

    .largemodal td {
        padding: 12px;
        /* Adds 10px of padding on all sides of each cell */
        border: 1px solid #ddd;
        /* Optional: Add a border for visibility */
    }
</style>

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
@php
    $sessioncharge = session('charge');
    $deptcode = $sessioncharge->deptcode;
    $regioncode = $sessioncharge->regioncode;
    $distcode = $sessioncharge->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

    //Fetch locally stored lang
    if (isset($_COOKIE['language'])) {
        $lang_val = $_COOKIE['language'];
        if ($lang_val == '' || $lang_val == null) {
            $lang_val = 'en';
        }
    } else {
        $lang_val = 'en';
    }

@endphp

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditteam_label">Audit Team</div>

            <div class="card-body">

                <form id="auditteam" name="auditteam">

                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="auditteamid" id="auditteamid" value="" />
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="department"for="validationDefault01">Department
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="deptcode" name="deptcode"
                                onchange="onchange_deptcode()" <?php echo $make_dept_disable; ?>>
                                <option value="">Select Department</option>
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                        @if (old('dept', $deptcode) == $department->deptcode) selected @endif
                                        data-name-en="{{ $department->deptelname }}"
                                        data-name-ta="{{ $department->depttlname }}">
                                        {{ $department->deptelname }}
                                        <!-- Display any field you need -->
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="region" for="validationDefault01">Region
                            </label>
                            <input type="hidden" value="1" />
                            <select class="select2 form-select mr-sm-2 lang-dropdown" name="regioncode" id="regioncode"
                                onchange="onchange_regioncode()" <?php echo $make_region_disable; ?>>
                                <option value="">Select Region</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3" id="district">
                            <label class="form-label required lang " key="district" for="validationDefault01">District
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="distcode" name="distcode"
                                onchange="get_auditor_details('', '', '')" <?php echo $make_dist_disable; ?>>
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">


                            <label class="form-label required lang" key="name_audit_team" for="validationDefault02">Name
                                of the
                                Audit
                                Team</label>
                            <input type="text" class="form-control" id="team_name" name="team_name"
                                placeholder="Team name" />
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4 ">
                            <label class="form-label lang" key="auditors_det">Auditor's Details </label>
                            <input class="form-control" id="myInput" type="text" placeholder="Search..">

                            <div class="comment-widgets scrollable mb-2 common-widget"
                                style="height: 160px; border: 1px solid #ccc; " data-simplebar="">
                                <ul class="list-group lang dropdown" id="username" style="min-height: 100px;">

                                </ul>
                            </div>

                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang" key="teamhead_label">Team Head</label>
                            <ul id="team-head" class="list-group"
                                style="min-height: 50px; border: 1px solid #ccc; padding: 5px;">
                                <!-- Placeholder for the Team Head -->
                            </ul>
                            <div id="member_error" class="text-danger mt-2" style="display:none;">
                                Team Head is required.
                            </div>
                            <label class="form-label mt-3 required lang" key="teammember_label">Team Members</label>
                            <div class="comment-widgets scrollable mb-2 common-widget"
                                style="height: 100px; border: 1px solid #ccc; " data-simplebar="">
                                <ul id="team-members" class="list-group" style="min-height: 100px;">
                                </ul>


                            </div>



                            <div id="members_error" class="text-danger mt-2" style="display:none;">
                                At least one Team Member is required.
                            </div>

                        </div>
                    </div>

                    <div class="row justify-content-center">

                        <div class="col-md-4 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" key="savedraft_btn" type="submit"
                                action="insert" id="buttonaction" name="buttonaction">Save Draft </button>
                            <button class="btn button_finalise mt-3 lang" key="final_btn" type="submit"
                                id="finalisebtn"> Finalize
                            </button>
                            <button type="button" class="btn btn-danger mt-3 lang" key ="clear_btn"
                                id="reset_button">Clear</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="col-12">

        <div class="card card-border">
            <div class="card-header card_header_color lang" key="audit_team_det">Audit Team Details</div>

            <div class="card-body">

                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditteamtable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang" key="department">Department</th>
                                    <th class="lang" key="region">Region</th>
                                    <th class="lang" key="district">District</th>
                                    <th class="lang" key="team_name_tab">Team Name</th>
                                    <th class="lang" key="teamhead_label">Team Head</th>
                                    <th class="lang" key="teammember_label">Team Members</th>
                                    <th class="all lang" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamically populated rows will go here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no_data" class="hide_this">
                        <center>No Data Available</center>
                    </div>
                </div>

            </div>
            <script>
                function handleColorTheme(e) {
                    document.documentElement.setAttribute("data-color-theme", e);
                }
                // script src = "https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js" >
            </script>
            {{-- </script> --}}

        </div>
        {{-- <div class="dark-transparent sidebartoggler"></div> --}}

    </div>
</div>
</div>
</body>
{{-- <script src="../assets/js/theme/sidebarmenu.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="../assets/js/theme/app.init.js"></script>
    <script src="../assets/js/theme/theme.js"></script>
    <script src="../assets/js/theme/app.min.js"></script> --}}
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/plugins/toastr-init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <script src="../assets/libs/select2/dist/js/select2.min.js"></script> -->
<!-- <script src="../assets/js/forms/select2.init.js"></script> -->
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/datatable/datatable-advanced.init.js"></script>

<script>
    //Language Change
    var lang = window.localStorage.getItem('lang');
    change_lang_for_page(lang);

    $("#translate").change(function() {
        lang = $(this).val();
        change_lang_for_page(lang);

    });

    function change_lang_for_page(lang) {
        // Get all dropdowns with the class 'lang-dropdown'
        const dropdowns = document.querySelectorAll('.lang-dropdown');

        // Loop through each dropdown
        dropdowns.forEach((dropdown) => {
            const options = dropdown.options;

            // Loop through each option in the current dropdown
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const nameEn = option.getAttribute('data-name-en'); // English name
                const nameTa = option.getAttribute('data-name-ta'); // Tamil name

                // Update the visible text based on the selected language
                if (lang === 'en' && nameEn) {
                    option.textContent = nameEn;
                } else if (lang === 'ta' && nameTa) {
                    option.textContent = nameTa;
                }
            }
        });
    }


    // if (lang === 'en') {
    //     option.textContent = nameEn;

    // } else {
    //     option.textContent = nameEn;
    // }

    // }



    // Initialize Sortable for Team Head
    const teamHead = document.getElementById('team-head');
    const teamMembers = document.getElementById('team-members');
    const userName = document.getElementById('username');

    Sortable.create(teamHead, {
        group: 'shared',
        animation: 150,
        onAdd: function(evt) {
            // Ensure only one item in Team Head
            $('#member_error').hide();
            if (teamHead.children.length > 1) {
                toastr.error("Team can have only one Head", " ", {
                    progressBar: true,
                });
                //                 toastr.warning("Team can have only one Head", "  ", {
                //   closeButton: true,
                // });

                evt.from.appendChild(evt.item);

            }

        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    // Initialize Sortable for Team Members
    Sortable.create(teamMembers, {
        group: 'shared',
        animation: 150
    });
    Sortable.create(userName, {
        group: 'shared',
        animation: 150
    });

    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#username li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });








    /******************************* Based on the Radio button getauditors ************************************/

    document.getElementById('distcode').addEventListener('change', function() {
        let distcode = this.value;
        get_auditor_details(distcode, '', '')
    });

    var deptcode_session = '<?php echo $deptcode; ?>';
    var regioncode_session = '<?php echo $regioncode; ?>';
    var distcode_session = '<?php echo $distcode; ?>';

    onchange_deptcode(regioncode_session);
    onchange_regioncode(regioncode_session, distcode_session);
    if (distcode_session) {
        get_auditor_details(distcode_session, '', deptcode_session);

    }

    function onchange_deptcode(regioncode_session = '') {
        reset_auditor_datas();
        var deptcode = $('#deptcode').val();
        $.ajax({
            url: '/audit_team/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var data = response;
                $('#regioncode').empty();
                $('#regioncode').append('<option value="">Select Region</option>');

                // $.each(data, function(index, region) {
                //     var isSelected = region.regioncode === regioncode_session ? 'selected' : '';
                //     $('#regioncode').append('<option value="' + region.regioncode + '" ' +
                //         isSelected + '>' + region.regionename + '</option>');
                // });
                $.each(data, function(index, region) {
                    var isSelected = region.regioncode === regioncode_session ? 'selected' : '';
                    $('#regioncode').append('<option value="' + region.regioncode + '" ' +
                        isSelected +
                        ' data-name-en="' + region.regionename + '"' +
                        ' data-name-ta="' + region.regiontname + '">' +
                        (lang === "en" ? region.regionename : region.regiontname) +
                        '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function onchange_regioncode(regioncode_session = '', distcode_session = '') {
        reset_auditor_datas();

        var deptcode = regioncode_session ? (deptcode_session || $('#deptcode').val()) : $('#deptcode').val();
        var regioncode = regioncode_session || $('#regioncode').val();
        $.ajax({
            url: '/audit_team/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var data = response;
                $('#distcode').empty();
                $('#distcode').append('<option value="">Select District</option>');

                $.each(data, function(index, district) {
                    var isSelected = district.distcode === distcode_session ? 'selected' : '';
                    $('#distcode').append('<option value="' + district.distcode + '" ' +
                        isSelected +
                        ' data-name-en="' + district.distename + '"' +
                        ' data-name-ta="' + district.disttname + '">' +
                        (lang === "en" ? district.distename : district.disttname) +
                        '</option>');
                });


                // $.each(data, function(index, district) {
                //     var isSelected = district.distcode === distcode_session ? 'selected' : '';
                //     $('#distcode').append('<option value="' + district.distcode + '" ' +
                //         isSelected + '>' + district.distename + '</option>');
                // });

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }





    // get_auditor_details('A', '');

    function reset_auditor_datas() {
        $('#username').empty();
        $('#team-head').empty();
        $('#team-members').empty();

    }


    function get_auditor_details(distcode, auditteamid, deptcode) {

        reset_auditor_datas();
        if (!(deptcode)) deptcode = $('#deptcode').val();

        // if (!(distcode)) {
        //     distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;
        // }
        $.ajax({
            url: '/get-auditors', // Adjust URL accordingly
            type: 'POST',
            dataType: 'json',
            data: {
                distcode: distcode,
                auditteamid: auditteamid,
                deptcode: deptcode,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(data) {
                const usernameList = $('#username'); // jQuery selector for the ul element
                usernameList.empty(); // Clear the current list
                if (data.success) {
                    if (data.auditor.length > 0) {
                        // Loop through the data and append list items
                        data.auditor.forEach(function(auditor) {
                            var listItem = $('<li></li>'); // Create the list item
                            listItem.addClass('list-group-item'); // Add class to the list item
                            listItem.attr('draggable', 'true');
                            listItem.attr('data-userid', auditor.deptuserid);

                            // Append the HTML content for each auditor
                            listItem.html(`
                                        <div class="row">
                                            <div class="d-flex flex-row comment-row">
                                                <div class="col-md-12 ms-4">
                                                    <h6 class="fw-medium">${auditor.username} - ${auditor.desigelname} (${auditor.distename})</h6>
                                                </div>
                                            </div>
                                        </div>
                                    `);

                            // Append the list item to the username list
                            usernameList.append(listItem);
                        });
                    } else {
                        // If no auditors, show a message
                        var noAuditorMessage = $('<li></li>');
                        noAuditorMessage.addClass('list-group-item text-center');
                        noAuditorMessage.text('No auditor details available');
                        usernameList.append(noAuditorMessage);
                    }
                } else {
                    // If success is false, handle this scenario
                    console.error("Failed to fetch auditors.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching auditors:", error);
            }
        });
    }

    function show_div(selectedDiv) {
        if (selectedDiv === "district") {
            $('#team-members').empty(); // Removes all child list items (<li>)
            $('#team-head').empty(); // Removes all child list items (<li>)
        }
    }




    /******************************* Based on the Dropdown getauditors ************************************/


    /*********************************** Reset Form **********************************************/
    function reset_form() {
        $('#display_error').hide();
        change_button_as_insert('auditteam', 'action', 'buttonaction', 'display_error', '', '');
        updateSelectColorByValue(document.querySelectorAll(".form-select"));



        if (distcode_session) {
            $('#team_name').val('');

        } else if (regioncode_session) {
            $('#distcode, #team_name').val('');

        } else if (deptcode_session) {
            $('#regioncode ,#distcode, #team_name').val('');
        } else {
            $("#auditteam").validate().resetForm(); // Reset the validation errors
            $("#auditteam")[0].reset(); // Optionally reset the form fields as well
            $('#deptcode , #regioncode ,#distcode, #team_name').val('');

        }

        $('#team-members').empty(); // Removes all child list items (<li>)
        $('#team-head').empty(); // Removes all child list items (<li>)
        //get_auditor_details('A', '', '');
    }

    /*********************************** Reset Form **********************************************/



    /***********************************Jquery Form Validation **********************************************/

    const $auditteamForm = $("#auditteam");

    // Validation rules and messages
    $auditteamForm.validate({
        rules: {
            deptcode: {
                required: true
            },
            regioncode: {
                required: true
            },
            distcode: {
                required: true
            },
            team_name: {
                required: true
            }
        },
        messages: {
            deptcode: {
                required: "Select department name"
            },
            regioncode: {
                required: "Select Region"
            },
            distcode: {
                required: "Select District"
            },
            team_name: {
                required: "Enter Team Name"
            }
        },
        errorPlacement: function(error, element) {
            // For datepicker fields inside input-group, place error below the input group
            if (element.hasClass('datepicker')) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },
    });




    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $auditteamForm.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    /***********************************Jquery Form Validation **********************************************/


    /*********************************** Insert,update,Finalise,Reset **********************************************/

    // $(document).on('click', '#buttonaction', function()
    // {
    //     event.preventDefault();

    //     if ($("#auditteam").valid())
    //     {
    //         get_insertdata('insert')
    //     } else {
    //         // If the form is not valid, show an alert
    //         // alert("Form is not valid. Please fix the errors.");
    //     }
    // });

    $(document).on('click', '#buttonaction', function(event) {
        event.preventDefault(); // Prevent form submission

        if ($auditteamForm.valid()) {


            $('#member_error').hide();
            $('#members_error').hide();

            // Validate Team Head
            if (teamHead.children.length === 0) {
                $('#member_error').show(); // Show error message if no Team Head is assigned
                return false; // Prevent form submission
            }

            // Validate Team Members
            if (teamMembers.children.length === 0) {
                $('#members_error').show(); // Show error message if no Team Members
                return false; // Prevent form submission
            }



            if ((teamHead.children.length > 0) && (teamMembers.children.length > 0)) {
                get_insertdata('insert')
            }

            // If form is valid, handle form submission (can be an AJAX call or standard submission)
            // For example, submitting the form:
            // $auditteamForm.submit(); // or handle submission via AJAX here
        } else {
            // Optionally, scroll to the first error
            scrollToFirstError();
        }
    });

    $(document).on('click', '#finalisebtn', function() {

        // Prevent form submission (this stops the page from refreshing)
        event.preventDefault();
        //Trigger the form validation
        if ($("#auditteam").valid()) {

            $('#member_error').hide();
            $('#members_error').hide();

            // Validate Team Head
            if (teamHead.children.length === 0) {
                $('#member_error').show(); // Show error message if no Team Head is assigned
                return false; // Prevent form submission
            }

            // Validate Team Members
            if (teamMembers.children.length === 0) {
                $('#members_error').show(); // Show error message if no Team Members
                return false; // Prevent form submission
            }

            if ((teamHead.children.length > 0) && (teamMembers.children.length > 0)) {
                var selectedValues = [];


                var TeamHead = $('#team-head li');

                // Extract the name (inside <strong>) and role (the rest of the text)
                var TeamHeadname = TeamHead.find('strong').text(); // Get the name inside <strong>
                var TeamHeadrole = TeamHead.text().replace(TeamHeadname, '')
                    .trim(); // Get the role by removing the name part

                // Merge name and role
                var TeamHeadmergedValue = TeamHeadname + ' ' + TeamHeadrole;


                $('#team-members li').each(function() {
                    // Extract the name and role from each <li>
                    var name = $(this).find('strong').text() || $(this).find('h6').text().split(' - ')[
                        0]; // Name can be inside <strong> or <h6>
                    var role = $(this).text().replace(name, '')
                        .trim(); // Extract the role by removing the name part

                    // Display the values in the console or append to a div
                    var mergedValue = name + ' ' + role;

                    // Add the merged value to the array
                    selectedValues.push(mergedValue);
                    console.log('Name: ' + name);
                    console.log('Role: ' + role);
                });


                data =
                    '<table style="width:100;%" class="table table-hover w-100 table-bordered display largemodal"><tbody><tr><td><b>Department</b></td><td>' +
                    $("#deptcode option:selected").text() + '</td></tr><tr><td><b>Team Name</b></td><td>' + $(
                        '#team_name').val() + '</td></tr><tr><td><b>Team Head</b></td><td>' +
                    TeamHeadmergedValue + '</td></tr><tr><td><b>Team Members</b></td><td>' + selectedValues +
                    '</td></tr></tbody></table>';
                // '<table style="width:100;%" class="table table-bordered view_detail largemodal"><tbody><tr><th><span class="lang" key="invoice_no">Department Name</span></th><td>' +
                // $("#deptcode option:selected").text() + '</td></tr><tr><th>Team Name</th><td>' + $(
                //     '#team_name').val() + '</td></tr><tr><th>Team Head</th><td>' + TeamHeadmergedValue +
                // '</td></tr><tr><th>Team Members</th><td>' + selectedValues;

                content = '';

                passing_large_alert('Confirmation', data, 'large_confirmation_alert',
                    'large_alert_header',
                    'large_alert_body', 'forward_alert');
                $("#large_modal_process_button").html("Ok");
                $("#large_modal_process_button").addClass("button_finalize");
                $('#large_modal_process_button').removeAttr('data-bs-dismiss');

                //
            } else {
                // If the form is not valid, show an alert
                // alert("Form is not valid. Please fix the errors.");
            }

        }
    });

    $('#large_modal_process_button').on('click', function() {
        var confirmation = 'Are you sure to finalize?';
        $('#large_confirmation_alert .modal-content').addClass('blurred');

        passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
            'alert_body', 'forward_alert');
        $('#confirmation_alert').css('z-index', 100000);
        $("#process_button").html("Ok");

    });

    /**Finalizing Process */
    $('#process_button').on('click', function() {
        $("#large_confirmation_alert").modal("hide");
        get_insertdata('finalise')

    });



    $('#reset_button').on('click', function() {
        reset_form(); // Call the reset_form function
    });


    function get_insertdata(action) {



        // distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;



        var distcode = $('#distcode').val($('#distcode').val());


        var formData = $('#auditteam').serializeArray();
        const {
            teamHeadId,
            teamMemberIds
        } = collectUserIds(); // Corrected call

        // Append additional values to formData array
        formData.push({
            name: 'teamHeadId',
            value: teamHeadId
        });

        formData.push({
            name: 'teamMemberIds',
            value: JSON.stringify(teamMemberIds)
        });

        formData.push({
            name: 'auditordiststatus',
            value: distcode
        });

        // Conditionally append finaliseflag based on action
        if (action === 'finalise') {
            formData.push({
                name: 'finaliseflag',
                value: 'Y'
            });
        } else if (action === 'insert') {
            formData.push({
                name: 'finaliseflag',
                value: 'N'
            });
        }

        // // AJAX request
        $.ajax({
            url: '/audit/createAuditTeam', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    reset_form();

                    $('#confirmation_alert').modal('show');
                    if (action === 'finalise') {
                        var responsefinal = 'Data Finalized Successfully';

                    } else {
                        var responsefinal = response.message;

                    }

                    passing_alert_value('Confirmation', responsefinal, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');

                    table.ajax.reload(); // Reload the table
                } else if (response.error) {}
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');

                console.error('Error details:', xhr, status, error);
            }
        });
    }








    function collectUserIds() {
        const teamHead = document.querySelector('#team-head .list-group-item');
        const teamMembers = document.querySelectorAll('#team-members .list-group-item');

        // Collect the `userid` for the Team Head (if present)
        const teamHeadId = teamHead ? teamHead.getAttribute('data-userid') : null;

        // Collect the `userid` values for all Team Members
        const teamMemberIds = [];
        teamMembers.forEach(member => {
            teamMemberIds.push(member.getAttribute('data-userid'));
        });

        // Correctly return the object with teamHeadId and teamMemberIds
        return {
            teamHeadId,
            teamMemberIds
        };
    }


    /*********************************** Insert,update,Finalise,Reset **********************************************/


    /********************************************** Fetch Data ********************************************** */

    var table = $('#auditteamtable').DataTable({
        "processing": true,
        "serverSide": false,
        "lengthChange": false,
        "scrollX": true,

        "ajax": {
            "url": "/audit/fetchAllData", // Your API route for fetching data
            "type": "POST",
            "headers": {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            "dataSrc": function(json) {
                // Log the response for debugging
                // Check if there is data
                if (json.data && json.data.length > 0) {
                    // Data exists, show the table and hide the "No Data" message
                    $('#tableshow').show();
                    $('#auditteamtable_wrapper').show(); // Show the DataTable
                    $('#no_data').hide(); // Hide custom "No Data" message

                    return json.data; // Return data for DataTable rendering
                } else {
                    $('#tableshow').hide();
                    // No data, hide the table and show the "No Data" message
                    $('#auditteamtable_wrapper').hide(); // Hide the DataTable
                    $('#no_data').show(); // Show custom "No Data" message

                    return []; // Return an empty array to prevent rendering
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
                "data": "deptelname"
            },
            {
                "data": "regionename",
            },
            {
                "data": "distename",
            },
            {
                "data": "teamname"
            },
            {
                "data": "teamhead_details" // This will be the column we are working with

            },

            {
                "data": "members", // The "members" column
                "render": function(data, type, row, meta) {
                    // Check if the 'members' field is a string and has data
                    if (data && typeof data === 'string') {
                        // Replace the commas with line breaks (<br>) to display each member on a new line
                        return data.split(',').join('<br>');
                    }
                    return ''; // If not a string, return an empty string
                }
            },
            {
                "data": "encrypted_auditteamid",
                "render": function(data, type, row) {
                    // Check the statusflag value
                    if (row.statusflag === 'Y') {
                        return `<center>
                                                <a class="btn editicon edit_btn" id="${data}">
                                                    <i class="ti ti-edit fs-4"></i>
                                                </a>
                                            </center>`;
                    } else if (row.statusflag === 'F') {
                        return `<center>
                                                <span class="badge bg-success fs-2">Finalized</span>
                                            </center>`;
                    }
                    return ''; // In case there is no matching statusflag
                }

            }
        ]
    });


    /********************************************** Fetch Data ********************************************** */



    /********************************************** Edit - Data ********************************************** */

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here
        var id = $(this).attr('id'); //Getting id of user clicked edit button.
        if (id) {
            $('#auditteamid').val(id);
            reset_form();
            getTeamdetail(id);

        }
    });

    function getTeamdetail(auditteamid) {
        $.ajax({
            url: '/audit/fetchTeamData', // Your API route to get user details
            method: 'POST',
            data: {
                auditteamid: auditteamid
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                if (response.success) {

                    change_button_as_update('auditteam', 'action', 'buttonaction',
                        'display_error', '', '');
                    $("#auditteam").validate().resetForm(); // Reset the validation errors
                    const teamData = response.data; // Get the team data array

                    // Populate the department code
                    if (teamData.length > 0) {
                        const firstRecord = teamData[0]; // Use the first record for shared values

                        get_auditor_details(firstRecord.distcode, $('#auditteamid').val(), firstRecord
                            .deptcode)
                        $('#deptcode').val(firstRecord.deptcode); // Set department name in #deptcode
                        $('#team_name').val(firstRecord.teamname);
                        onchange_deptcode(firstRecord.regioncode);
                        onchange_regioncode(firstRecord.regioncode, firstRecord.distcode);


                        //$('#teamcode').val(firstRecord.teamcode);

                        // Select all radio buttons with the name 'radio-solid-success'
                        let radios = document.querySelectorAll('input[name="radio-solid-success"]');

                        // Iterate through the radio buttons
                        radios.forEach((radio) => {
                            if (radio.value === firstRecord.auditordiststatus) {
                                radio.checked = true; // Mark this radio button as checked
                            }
                        });

                        var memberDetails = firstRecord.members.split(',').map(function(member) {
                            return member.trim().split(
                                " - "); // Ensure any leading/trailing spaces are removed
                        });

                        // Log the result of memberDetails for debugging
                        console.log(memberDetails); // Check if the array has the expected structure

                        // Loop through the team data and add team members
                        memberDetails.forEach(detail => {
                            // Check if there's a valid TeamMember ID (you might need to adjust the index if necessary)
                            if (detail.length > 3 && detail[
                                    3
                                ]) { // Assuming member details might have 4 parts and [3] is the ID
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item';

                                // Set the data-userid attribute dynamically
                                listItem.setAttribute('data-userid', detail[
                                    3]); // Assuming detail[3] contains the TeamMember ID

                                // Set inner HTML for the list item
                                listItem.innerHTML = `
                                            <strong>${detail[0]}</strong> - ${detail[1]} (${detail[2]})
                                        `;

                                // Append the list item to the teamMembersDiv
                                teamMembersDiv.appendChild(listItem);
                            } else {
                                // Log error if member details don't have enough parts
                                console.log("Invalid member detail format:", detail);
                            }
                        });


                    } else {
                        // Handle empty data (e.g., no team data found)
                        alert('No team details found for the given team code.');
                    }

                } else {
                    alert('User not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    }

    /********************************************** Edit - Data ********************************************** */
</script>
@endsection
