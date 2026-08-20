@section('content')
@section('title', 'Create User Report')

@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
@php

    $sessionchargedel = session('charge');
    $seesionroletypecode = $sessionchargedel->roletypecode;

    $isadmin = $seesionroletypecode == $DGA_roletypecode;

    if ($isadmin) {
        $buttonname = 'Save Draft';
    } else {
        $buttonname = 'Send Request';
    }

@endphp

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header card_header_color lang" key="schedulerequest">Audit Schedule Request</div>
            <div class="card-body">
                <form id="schedulerequest" name="schedulerequest">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="mandaysextensionid" id="mandaysextensionid" value="" />
                    <input type="hidden" name="entrymeetingdate" id="entrymeetingdate" value="" />
                    <input type="hidden" name="exitmeetingdate" id="exitmeetingdate" value="" />
                    <input type="hidden" name="username" id="username" value="" />
                    <input type="hidden" name="email" id="email" value="" />

                    <div class="row">



                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01" key="reason">Reason
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown " id="reasoncode" name="reasoncode"
                                onchange="getscheduledel()">
                                <option value='' data-name-en="Select the Reason"
                                    data-name-ta="காரணத்தை தேர்வு செய்யவும்">Request Reason</option>
                                @foreach ($request_option as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>

                        </div>
                        @if ($isadmin)

                            <div class="col-md-4 ">
                                <label class="form-label required lang" for="validationDefault01"
                                    key="dept">Department
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    onchange="getdeptbaseddetails('region','regioncode','','','','')">
                                    <option value='' data-name-en="Select the Department">Select the Department
                                    </option>
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 ">
                                <label class="form-label required lang" for="validationDefault01" key="region">Region
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="regioncode"
                                    name="regioncode" onchange="getdeptbaseddetails('district','distcode','','','','')">
                                    <option value='' data-name-en="Select the Region"
                                        data-name-ta="பிராந்தியத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option>


                                </select>
                            </div>
                            <div class="col-md-4 ">
                                <label class="form-label required lang" for="validationDefault01"
                                    key="district">District
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                    onchange="getdeptbaseddetails('inst','auditscheduleid','','','','')">
                                    <option value='' data-name-en="Select the District"
                                        data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">Select District</option>

                                </select>
                            </div>
                        @endif
                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01"
                                key="schdulename">Schedule </label>
                            <select class="form-select mySelect lang-dropdown select2" id="auditscheduleid"
                                name="auditscheduleid" onchange="getselectedscheduledel()">
                                <option value='' data-name-en="Select the Schedule"
                                    data-name-ta="அட்டவணையை தேர்வு செய்யவும்">Select Schedule</option>
                            </select>
                        </div>


                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01" key="oldmandys">Mandays
                            </label>
                            <input type="text" class="form-control only_numbers" id="mandays" name="mandays"
                                maxlength="11" disabled />
                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01" key="teamsize">Team size
                            </label>
                            <input type="text" class="form-control only_numbers" id="teamsize" name="teamsize"
                                maxlength="11" disabled />
                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01"
                                key="entrymeetingdate">EntryMeet Date </label>
                            <input type="text" class="form-control only_numbers" id="entrymeetdate"
                                name="entrymeetdate" maxlength="11" disabled />
                        </div>
                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01"
                                key="oldpurposedexitmeetingdate">Purposed Exitmeetdate </label>
                            <input type="text" class="form-control only_numbers" id="purposedexitmeetdate"
                                name="purposedexitmeetdate" maxlength="11" disabled />
                        </div>





                    </div>

                    <div class="row">

                        @if ($isadmin)
                            <div class="col-md-4 ">
                                <label class="form-label required lang " for="validationDefault01"
                                    key="extramandays">Extra Mandays Need
                                </label>
                                <input type="text" class="form-control only_numbers" id="extendmandays"
                                    name="extendmandays" maxlength="3" onchange="getnewexitmeetdate()" />
                            </div>

                            <div class="col-md-4 ">
                                <label class="form-label required lang " for="validationDefault01"
                                    key="exitmeetdatenew">Proposed Exitmeeting Date
                                </label>
                                <input type="text" class="form-control only_numbers" id="newproposed"
                                    name="newproposed" maxlength="3" disabled />
                            </div>
                        @endif

                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01"
                                key="remarkslabel">Remarks

                            </label>
                            <textarea id="remarks" class="form-control alpha_numeric" name="remarks" maxlength="200"></textarea>

                        </div>
                    </div>

                    <?php if(!($isadmin))
                                        {?>
                    <hr>

                    <?php

                    }?>

                    <div class="row hide_this" id="leavedetails">
                        <!-- <div class=" col-md-3"> </div> -->
                        <div class="col-md-6">
                            <label class="form-label  lang " for="validationDefault01" key="">Leavedetails

                            </label>

                            <div id="detailsDiv"></div>
                        </div>
                        <!-- <div class="col-md-3"> </div> -->
                    </div>

                    <div class="row mt-3 text-center">
                        <div class="col-md-6  mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />

                            <button type="button" name="buttonaction" action="insert" id="buttonaction"
                                class="btn button_save"><?php echo $buttonname; ?></button>
                            <button type="button" class="btn btn-danger" id="reset_button" onclick="resetForm()">
                                clear</button>

                            @if ($isadmin)
                                <button type="button" class="btn btn-success" id="finalisebtn">
                                    Finalise</button>
                            @endif;


                        </div>

                    </div>

                </form>
            </div>
        </div>


        <div class=" card ">
            <div class=" card-header card_header_color lang" key="schedulerequestdetails">List of Audit Schedule
                Requested Detail
            </div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this " id="tableshow">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang text-center align-middle" key="s_no">S.No</th>
                                    <th class="lang text-center align-middle" key="instname">Insitution Name</th>
                                    <th class="lang text-center align-middle" key="reasonlabel">Reason</th>
                                    </th>
                                    <th class="lang text-center align-middle" key="approveddel">Approved Details</th>
                                    <th class="lang text-center align-middle" key="remarkslabel">Remarks</th>
                                    <th class="lang text-center align-middle" key="statusflag">Status</th>
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

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>


<script>
        window.APP_CONFIG = {
            AES_SECRET_KEY: "{{ config('app.aes_key') }}",
            AES_IV: "{{ config('app.aes_iv') }}"
        };
    </script>



<script>
    $("#finalisebtn").on("click", function() {

        event.preventDefault();
        // Trigger the form validation
        if ($("#schedulerequest").valid()) {


            let mandays = parseInt($('#mandays').val()) || 0;
            let extramandays = parseInt($('#extendmandays').val()) || 0;
            let teamsize = parseInt($('#teamsize').val()) || 1;

            let totalmandays = extramandays;
            workingdayscount = totalmandays;
            workingdays = totalmandays / teamsize;

            exitmeetdate = $('#exitmeetingdate').val();
            newpurposedexitmeetdate = calculateToDate(exitmeetdate, workingdays, 'next');

            // dataHTML = 'Are you sure to give extra <b>' +
            //     extramandays +
            //     '</b> manday(s) with new proposed Exitmeeting date on <b></b>?';


            dataHTML =
                '<table style="width:100;%" class="table table-hover w-100 table-bordered display largemodal"><tbody><tr><td><b>Insitution Name</b></td><td>' +
                $("#auditscheduleid option:selected").text() + '</td></tr><tr><td><b>Old Mandays</b></td><td>' +
                $(
                    '#mandays').val() + '</td></tr><tr><td><b>Team size</b></td><td>' +
                $('#teamsize').val() + '</td></tr><tr><td><b>Entry Meet Date</b></td><td>' +
                $('#entrymeetdate').val() + '</td></tr><tr><td><b>Exit Meet Date</b></td><td>' +
                $('#purposedexitmeetdate').val() + '</td></tr><tr><td><b>Extra Mandays</b></td><td>' + $(
                    '#extendmandays').val() + '</td></tr><tr><td><b>New Exit Meet Date</b></td><td>' +
                $('#newproposed').val() +

                '</td></tr></tbody></table><b> Are you sure about the mandays extension approval?</b>';


            $('#large_modal_process_button').off('click').on('click', function(event) {
                event.preventDefault();
                $('#large_confirmation_alert').modal('hide');
                sendrequest('<?php echo $isadmin; ?>', 'F');
            });

            // Show confirmation modal with HTML version
            // passing_alert_value('Confirmation', dataHTML, 'confirmation_alert',
            //     'alert_header', 'alert_body', 'forward_alert');


            passing_large_alert('Confirmation', dataHTML, 'large_confirmation_alert',
                'large_alert_header',
                'large_alert_body', 'forward_alert');
            $("#large_modal_process_button").html("Ok");
            $("#large_modal_process_button").addClass("button_finalize");
            $('#large_modal_process_button').removeAttr('data-bs-dismiss');


        }
    });


    $("#buttonaction").on("click", function(event) {
        event.preventDefault();

        let data = ''; // Declare shared variables at the top
        let dataHTML = '';
        let workingdayscount = 0;
        let newpurposedexitmeetdate = '';
        let exitmeetdate = '';
        let workingdays = 0;

        if ($("#schedulerequest").valid()) {
            if ('<?php echo $isadmin; ?>') {
                let mandays = parseInt($('#mandays').val()) || 0;
                let extramandays = parseInt($('#extendmandays').val()) || 0;
                let teamsize = parseInt($('#teamsize').val()) || 1;

                let totalmandays = extramandays;
                workingdayscount = totalmandays;
                workingdays = totalmandays / teamsize;

                exitmeetdate = $('#exitmeetingdate').val();
                newpurposedexitmeetdate = calculateToDate(exitmeetdate, workingdays, 'next');

                dataHTML = 'Are you sure to give extra <b>' +
                    extramandays +
                    '</b> manday(s) with new proposed Exitmeeting date on <b>' +
                    newpurposedexitmeetdate + '</b>?';

            } else {
                exitmeetdate = $('#exitmeetingdate').val();
                let usercount = parseInt($('#usercount').val()) || 1;
                workingdayscount = parseInt($('#workingdayscount').val()) || 0;

                workingdays = workingdayscount / usercount;
                newpurposedexitmeetdate = calculateToDate(exitmeetdate, workingdays, 'next');

                dataHTML = 'Are you sure to send request for Mandays Extension ?';
            }

            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                $('#confirmation_alert').modal('hide');
                sendrequest('<?php echo $isadmin; ?>', '');
            });

            // Show confirmation modal with HTML version
            passing_alert_value('Confirmation', dataHTML, 'confirmation_alert',
                'alert_header', 'alert_body', 'forward_alert');
        }
    });






    function getdeptbaseddetails(valuefor, valueforid, selectedvalue, deptcode, regioncode, distcode) {


        const defaultOption = `
            <option value="" data-name-en="துறையைத் தேர்ந்தெடுக்கவும்" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'துறையைத் தேர்ந்தெடுக்கவும்' : 'Select Department'}
            </option>`;

        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        // return;
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()

        if (!(deptcode)) deptcode = $('#deptcode').val();
        if (!(regioncode)) regioncode = $('#regioncode').val();
        if (!(distcode)) distcode = $('#distcode').val();





        //alert(distcode)
        var lang = getLanguage();
        let placeholderTextEn = '',
            placeholderTextTa = '';

        switch (valuefor) {
            case 'region':
                placeholderTextEn = 'Select a Region';
                placeholderTextTa = 'பகுதியை தேர்வு செய்';
                break;
            case 'district':
                placeholderTextEn = 'Select a District';
                placeholderTextTa = 'மாவட்டத்தை தேர்ந்தெடுக்கவும்';
            case 'inst':
                placeholderTextEn = 'Select the schedule';
                placeholderTextTa = 'அட்டவணையைத் தேர்ந்தெடுக்கவும்';
                break;
            default:
                placeholderTextEn = 'Select an Option';
                placeholderTextTa = 'ஒரு விருப்பத்தை தேர்வு செய்';

        }





        $dropdown.html(`<option value="" data-name-en="${placeholderTextEn}" data-name-ta="${placeholderTextTa}">
        ${lang === 'ta' ? placeholderTextTa : placeholderTextEn}
            </option>`);
        if (valuefor == 'region') {
            $('#distcode').html(`<option value="" data-name-en="Select a District" data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'மாவட்டத்தை தேர்ந்தெடுக்கவும்' : 'Select a District'}
            </option>`);
        }



        $.ajax({
            url: '/schedulerequest/fetch_deptbaseddata',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                valuefor: valuefor,
                formname: 'checkschedulestatus'

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.success && Array.isArray(response.data)) {
                    // Map response data to options

                    //return;
                    const options = response.data.map(item => {



                        switch (valuefor) {
                            case 'region':
                                return `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}"  ${item.regioncode === selectedvalue ? "selected" : ""}>${item.regionename}</option>`;
                            case 'district':
                                return `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${item.distcode === selectedvalue ? "selected" : ""}>${item.distename}</option>`;
                            case 'inst':
                                return `<option value="${item.encrypted_auditscheduleid}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${item.auditscheduleid === selectedvalue ? "selected" : ""}>${item.instename}</option>`;
                            default:
                                return '';
                        }
                    }).join('');

                    // Append options or show fallback message
                    $dropdown.append(options || '<option value="">No data available</option>');

                } else {
                    console.error("Invalid response or no data:", response);
                    $dropdown.append('<option value="">No data available</option>');
                }
                // alert(distcode)
                // if (distcode) {
                //     alert()
                //     onchange_region('district', 'distcode', distcode, regioncode)
                // }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
        // } else {
        //     //  $('#deptcode').html(defaultOption);
        //     $('#distcode').select2('destroy');
        //     $('#distcode').select2(null);
        //     $('#distcode').select2();
        // }
    }

    function getnewexitmeetdate() {

        mandays = $('#mandays').val()
        extramandays = $('#extendmandays').val()

        totalmandys = parseInt(extramandays);
        teamsize = $('#teamsize').val();
        exitmeetdate = $('#exitmeetingdate').val();

        if (totalmandys && teamsize && exitmeetdate) {
            workingdays = totalmandys / teamsize;
            newpurposedexitmeetdate = calculateToDate(exitmeetdate, workingdays, 'next');
            $('#newproposed').val(convertDateFormatYmd_ddmmyy(newpurposedexitmeetdate))
        } else {
            $('#newproposed').val('');
        }

    }
    // Add custom method: value must be <= oldMandays
    $.validator.addMethod("notMoreThanTotalMandays", function(value, element) {
        const totalMandays = parseInt($('#mandays').val(), 10);
        return this.optional(element) || parseInt(value, 10) <= totalMandays;
    }, "Extra Mandays cannot exceed Total mandays.");


    const holidayList = @json($holidays);

    initializeDataTable(window.localStorage.getItem('lang'));



    function getscheduledel() {


        const defaultOption = "<option value=''>Select </option>";
        const $dropdown = $("#auditscheduleid");

        reasoncode = $('#reasoncode').val();

        if (((reasoncode) && (!('<?php echo $isadmin; ?>'))) || (('<?php echo $isadmin; ?>') && ($('#deptcode')
                .val()) && ($(
                '#regioncode').val()) && ($('#distcode').val()))) {
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getscheduledel',
                type: 'POST',
                data: {
                    userstatus: '<?php echo $userstatus; ?>',
                    reasoncode: reasoncode,
                    auditscheduleid: '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;
                        response.data.forEach(({
                            auditscheduleid: auditscheduleid,
                            instename: instename
                        }) => {
                            if (auditscheduleid && instename) {

                                options +=
                                    `<option value="${auditscheduleid}" >${instename}</option>`;
                            }
                        });
                        $dropdown.html(options);
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    let errorMessage = response.error;
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                }
            });

        } else {
            $dropdown.html(defaultOption);
        }
    }

    var usercount = 0;
    var workingdayscount = 0;

    function getselectedscheduledel() {

        $('#mandays').val('');
        $('#teamsize').val('');
        $('#entrymeetdate').val('');
        $('#purposedexitmeetdate').val('');
        $('#extendmandays').val('');
        $('#newproposed').val('');


        let auditscheduleid = $('#auditscheduleid').val();
        let reasoncode = $('#reasoncode').val();

        if (auditscheduleid && reasoncode) {
            $.ajax({
                url: '/getscheduledel',
                type: 'POST',
                data: {
                    userstatus: '<?php echo $userstatus; ?>',
                    reasoncode: reasoncode,
                    auditscheduleid: auditscheduleid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    // Populate your other fields
                    $('#mandays').val(response.data[0].mandays);
                    $('#teamsize').val(response.data[0].teamsize);

                    let entrymeetdate = convertDateFormatYmd_ddmmyy(response.data[0].entrymeetdate).split(
                        ' ')[0];
                    let proposedexitmeetdate = convertDateFormatYmd_ddmmyy(response.data[0]
                            .proposedexitmeetdate)
                        .split(' ')[0];
                    $('#entrymeetdate').val(entrymeetdate);
                    $('#entrymeetingdate').val(response.data[0].entrymeetdate);

                    $('#exitmeetingdate').val(response.data[0].proposedexitmeetdate);
                    $('#purposedexitmeetdate').val(proposedexitmeetdate);
                    $('#username').val(response.data[0].username);
                    $('#email').val(response.data[0].email);


                    if (response.success && Array.isArray(response.data) && response.data.length >
                        0 && (!('<?php echo $isadmin; ?>'))) {

                        $('#leavedetails').show()



                        // Build table
                        let html = `
                        <table style="width:100%; border-collapse:collapse; font-size:14px;">
                            <thead>
                                <tr style="background:#f5f5f5;">
                                    <th style="padding:6px; border:1px solid #ddd; text-align:left;">Username (Designation)</th>
                                    <th style="padding:6px; border:1px solid #ddd; text-align:center;">Leave Period</th>
                                    <th style="padding:6px; border:1px solid #ddd; text-align:right;">Working Days</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                        for (let i = 0; i < response.data.length; i++) {
                            let item = response.data[i];

                            let fromDate = change_dateformat(item.fromdate).split(' ')[0];
                            let toDate = change_dateformat(item.todate).split(' ')[0];

                            html += `
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    <strong>${item.username}</strong> (${item.desigesname})
                                </td>
                                <td style="padding:6px; border:1px solid #ddd; text-align:center;">
                                    ${fromDate} - ${toDate}
                                </td>
                                <td style="padding:6px; border:1px solid #ddd; text-align:right;">
                                    <strong>${item.working_days}</strong>
                                </td>
                            </tr>
                        `;

                            usercount++;
                            workingdayscount += parseInt(item.working_days) || 0;
                        }

                        // Footer row for totals
                        html += `
                            </tbody>
                            <tfoot>
                                <tr style="background:#f9f9f9; font-weight:bold;">
                                    <td style="padding:6px; border:1px solid #ddd;">Total Users: ${usercount}</td>
                                    <td style="padding:6px; border:1px solid #ddd; text-align:center;"></td>
                                    <td style="padding:6px; border:1px solid #ddd; text-align:right;">${workingdayscount} Days</td>
                                </tr>
                            </tfoot>
                        </table>
                    `;

                        $('#detailsDiv').html(html);

                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    let errorMessage = response.error;
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                }
            });
        }
    }

    $.validator.addMethod("alphanumericWithSpaces", function(value, element) {
        return this.optional(element) || /^[\u0B80-\u0BFFa-zA-Z0-9 ]+$/.test(value);
    }, "Only Tamil, English letters, numbers, and spaces are allowed.");


    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        $("#schedulerequest").validate({

            rules: {
                reasoncode: {
                    required: true
                },
                auditscheduleid: {
                    required: true
                },
                extendmandays: {
                    required: true,
                    notMoreThanTotalMandays: true
                },
                remarks: {
                    required: true,
                    alphanumericWithSpaces: true
                },
                deptcode: {
                    required: true,
                },
                regioncode: {
                    required: true,
                },
                distcode: {
                    required: true,
                }
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.closest(".input-group").length) {
                    // If the element is inside an input-group, place the error after the entire group
                    error.insertAfter(element.closest(".input-group"));
                } else {
                    // Otherwise, insert after the element itself
                    error.insertAfter(element);
                }
            },
            messages: errorMessages[language], // Set initial messages
            // submitHandler: function(form) {

            //     // console.log()


            // }
        });
        //reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    function resetForm() {
        $("#schedulerequest").validate().resetForm(); // Reset the validation errors
        $("#schedulerequest")[0].reset();

        const defaultOption = "<option value=''>Select </option>";
        const $dropdown = $("#auditscheduleid");
        // $dropdown.html(options);

        $dropdown.html(defaultOption);

        $('#leavedetails').hide()

        $('#detailsDiv').html('');

        changeButtonActionwithoutformrefresh('schedulerequest', 'action', 'buttonaction', 'reset_button',
            'display_error',
            @json($savebtn), @json($clearbtn), @json($insert));

    }

    function sendrequest(isadmin, finaliseflag) {



        var formData = $('#schedulerequest').serializeArray();
        formData = formData.filter(field => field.name !== 'email');

        formData.push({
            name: 'email',
            value: encryptPassword($('#email').val())
        });

        formData = formData.filter(field => field.name !== 'username');

        formData.push({
            name: 'username',
            value: encryptPassword($('#username').val())
        });

        formData.push({
            name: 'oldmandays',
            value: $('#mandays').val()
        });

        formData.push({
            name: 'finaliseflag',
            value: finaliseflag
        });

        formData.push({
            name: 'teamsize',
            value: $('#teamsize').val()
        });

        formData.push({
            name: 'requestedexitmeetdate',
            value: $('#newproposed').val()
        });

        // var schedulename = $('#auditscheduleid option:selected').text();

        formData.push({
            name: 'schedulename',
            value: $('#auditscheduleid option:selected').text()
        });



        $.ajax({
            url: '/schedulerequest_insertupdate',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#buttonaction').prop('disabled', true); // Disable before request
            },
            success: function(response) {
                if (response.success) {
                    resetForm();


                    // getLabels_jsonlayout([{
                    // 	id: response.success,
                    // 	key: response.success
                    // }], 'N').then((text) => {
                    // 	passing_alert_value('Confirmation', Object
                    // 		.values(text)[0], 'confirmation_alert',
                    // 		'alert_header', 'alert_body',
                    // 		'confirmation_alert');
                    // });
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                    initializeDataTable(window.localStorage.getItem('lang'));
                } else if (response.error) {
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');

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
                        passing_alert_value('Confirmation',
                            alertMessage, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                }
            },
            complete: function() {
                $('#buttonaction').prop('disabled', false); // Re-enable after complete
            }
        });
    }


    function calculateToDate(fromDateStr, totalWorkingDays, datetakenfrom) {
        const holidaySet = new Set(holidayList);
        let fromDate = new Date(fromDateStr);
        let startDate = new Date(fromDate); // Clone the date

        const isWorkingDay = (date) => {
            const day = date.getDay();
            const formatted = date.toISOString().split('T')[0];
            return day !== 0 && day !== 6 && !holidaySet.has(formatted);
        };

        // Adjust the start date based on datetakenfrom value
        if (datetakenfrom === 'next') {
            // Move to the next working day
            do {
                startDate.setDate(startDate.getDate() + 1);
            } while (!isWorkingDay(startDate));
        } else if (datetakenfrom === 'today') {
            // If today is not a working day, move to the next working day
            if (!isWorkingDay(startDate)) {
                do {
                    startDate.setDate(startDate.getDate() + 1);
                } while (!isWorkingDay(startDate));
            }
        }

        // Now calculate the final date after totalWorkingDays
        let count = 0;
        let currentDate = new Date(startDate); // Start from the adjusted startDate

        while (count < totalWorkingDays) {
            if (isWorkingDay(currentDate)) {
                count++;
            }
            if (count < totalWorkingDays) {
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }

        // Format the final date as dd-mm-yyyy
        const day = String(currentDate.getDate()).padStart(2, '0');
        const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const year = currentDate.getFullYear();

        return `${day}/${month}/${year}`;
    }


    function initializeDataTable(language) {
        $.ajax({
            url: "/fetchdata_schedulerequest",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(json) {
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            }
        });
    }

    isAdmin = '<?php echo $isadmin; ?>';

    function renderTable(language) {
        const instname = language === 'ta' ? 'insttname' : 'instename';

        if ($.fn.DataTable.isDataTable('#usertable')) {
            $('#usertable').DataTable().clear().destroy();
        }

        const table = $('#usertable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                        <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                    </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: instname,
                    title: columnLabels?.[instname]?.[language],
                    render: function(data, type, row) {
                        const instnameKey = language === 'ta' ? 'insttname' : 'instename';
                        const instName = row[instnameKey] || '-';
                        const oldMandays = row['oldmandays'] ?? '-';
                        const teamSize = row['teamsize'] ?? '-';

                        // Format dates to dd-mm-yyyy
                        const formatDate = (dateStr) => {
                            if (!dateStr) return '-';
                            const date = new Date(dateStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        };

                        const entryDate = formatDate(row['entrymeetdate']);
                        const exitDate = formatDate(row['oldpurposedexitmeetdate']);

                        return `
							${instName}<br>
							 Mandays - ${oldMandays}<br>
							Team Size - ${teamSize}<br>
							Entry Meeting Date - ${entryDate}<br>
							Proposed Exit Meeting Date - ${exitDate}
						`;
                    },

                    className: 'text-wrap text-start'
                },
                {
                    data: 'transactiontypelname',
                    title: columnLabels?.['transactiontypelname']?.[language],
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        return row.transactiontypelname || '-';
                    }
                },
                // {
                //     data: 'oldmandays',
                //     className: "d-none d-md-table-cell lang text-wrap",
                //     render: function(data, type, row) {
                //         return `
                //         Extramandays - ${row.extramandays ?? '-'}
                // 		Exitmeetdate - ${row.requstedexitmeetdate ?? '-'}<br>
                //     `;
                //     }
                // },
                {
                    data: 'newmandays',
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        // Format dates to dd-mm-yyyy
                        const formatDate = (dateStr) => {
                            if (!dateStr) return '-';
                            const date = new Date(dateStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}-${month}-${year}`;
                        };

                        const exitDate = formatDate(row['newpurposedexitmeetdate']);
                        if (row.newmandays) {
                            return `New Total mandays - ${row.newmandays}<br>
            Remarks - ${isAdmin ? row.remarks : row.approvedremarks}<br>
            New ProposedExitMeetingDate - ${exitDate}`;
                        } else {
                            return '<div class="text-center">-</div>';
                        }



                    }
                },
                {
                    data: "remarks",
                    title: columnLabels?.["email"]?.[language],
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        return row.remarks || '-';
                    }
                },
                {
                    data: "processcode", // <- bind to processcode, not processelname
                    title: columnLabels?.["mobilenumber"]?.[language],
                    className: "d-none d-md-table-cell lang text-wrap text-center",
                    render: function(data, type, row) {
                        if (row.processcode === 'F') {
                            return `<button type="button" class="btn btn-sm btn-primary">${row.processelname}</button>`;
                        } else if (row.processcode === 'P') {
                            return `<button type="button" class="btn btn-sm btn-success">${row.processelname}</button>`;
                        } else if (row.processcode === 'E') {
                            return `<center><a class="btn editicon edit_user" id=${row.mandaysextensionid}><i class="ti ti-edit fs-4"></i></a></center>`;
                        } else {
                            return row.processelname || '-';
                        }

                    }
                }


            ]
        });

        const mobileColumns = [
            instname, 'transactiontypelname', 'processelname',
            "oldmandays", "remarks"
        ];

        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "usertable");
    }


    $(document).on('click', '.edit_user', function() {
        var id = $(this).attr('id');
        if (id) {
            // reset_form();
            mandaysextenstiondel(id);
        }
    });



    function mandaysextenstiondel(mandaysextenstionid) {
        $.ajax({
            url: '/fetchdata_schedulerequest',
            method: 'POST',
            data: {
                mandaysextenstionid: mandaysextenstionid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // alert('jo');
                    $('#display_error').hide();
                    // validator.resetForm();
                    // change_button_as_update('createuser', 'action', 'buttonaction', 'reset_button','display_error', @json($updatebtn), @json($clearbtn));
                    changeButtonActionwithoutformrefresh('schedulerequest', 'action', 'buttonaction',
                        'reset_button',
                        'display_error', @json($updatebtn), @json($clearbtn),
                        @json($update))

                    var user = response.data[0];
                    $('#teamsize').val(user.teamsize);
                    // $('#auditorflag').val(user.auditorflag);
                    $('#reasoncode').val(user.transactiontypecode);
                    $('#mandaysextensionid').val(user.mandaysextensionid);
                    $('#remarks').val(user.remarks);
                    $('#extendmandays').val(user.extramandays);
                    $('#entrymeetdate').val(convertDateFormatYmd_ddmmyy(user
                        .entrymeetdate));

                    $('#entrymeetingdate').val(user.entrymeetdate);

                    $('#exitmeetingdate').val(user.oldpurposedexitmeetdate);

                    $('#mandays').val(user.oldmandays);


                    getdeptbaseddetails('region', 'regioncode', user.regioncode, user.deptcode, '')
                    getdeptbaseddetails('district', 'distcode', user.distcode, user.deptcode, user
                        .regioncode)
                    getdeptbaseddetails('inst', 'auditscheduleid', user.auditscheduleid, user.deptcode, user
                        .regioncode, user.distcode)




                    $('#purposedexitmeetdate').val(convertDateFormatYmd_ddmmyy(user
                        .oldpurposedexitmeetdate));

                    $('#newproposed').val(convertDateFormatYmd_ddmmyy(user
                        .newpurposedexitmeetdate));


                    $('#deptcode').select2('destroy');

                    // Clear the value
                    $('#deptcode').val(user.deptcode);

                    // Reinitialize the Select2 (no events will be triggered)
                    $('#deptcode').select2();


                    updateSelectColorByValue(document.querySelectorAll(".form-select"));
                } else {
                    alert('User not found');
                }

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
</script>


@endsection
