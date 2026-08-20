<?php

$sizeof_supercheck = sizeof($supercheckquestions);

if ($sizeof_supercheck > 0) {
    $supercheckquestions_json = json_encode($supercheckquestions, JSON_FORCE_OBJECT);
} else {
    $supercheckquestions_json = '[]';
}

$charge = session('charge');

?>

@section('content')
    @extends('index2')
    @include('common.alert')
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <style>
        .form-check-input {
            border: 1px solid #000 !important;
            /* Darker border */
            width: 18px;
            /* Adjust width if needed */
            height: 18px;
            /* Adjust height if needed */
        }

        .form-check {
            display: flex;
            align-items: center;
            /* Align checkbox and label to the middle */
            gap: 5px;
            /* Adds spacing between checkbox and label */
        }

        .superchecktable {
            border: 1px solid #7198b9 !important;
            /* Change border color */
        }

        .superchecktable th,
        .superchecktable td {
            border: 1px solid #7198b9 !important;
            /* Ensure all cells have the same border color */
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
    </style>


    <?php
$sessionchargedel = session('charge');
$deptcode = $sessionchargedel->deptcode;

$instdel = $results;


if ($nodata == 'N') {
    $datashow = '';
    $nodatashow = 'hide_this';
$Fromquarter = $Quarter['fromquarter'];
$Toquarter = $Quarter['toquarter'];
?>

    <div class="row">
        <div class="col-12">
            <div class="spinner-wrapper hide_this" id="loader" style="display:none">
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
            <div class="card" style="border-color: #7198b9">
                <div class="card-header card_header_color lang" key="fieldauditdel">Field Audit Details</div>
                <div class="card-body"><br>
                    <div class="datatables <?php echo $datashow; ?>">
                        <div class="table-responsive " id="tableshow">
                            <table id="usertable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th class="lang" key="inst_name">Audit Office</th>
                                        <th class="lang" key="from_date">From date</th>
                                        <th class="lang" key="to_date">To date</th>
                                        <th class="all lang" key="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <input type="hidden" class="indexdyn" />
                                    <input type="hidden" class="sizeoftable" value="{{ sizeof($instdel) }}" />
                                    @foreach ($instdel as $index => $item)
                                        <tr>
                                            <td class="text-end">{{ $index + 1 }}</td> <!-- S.No -->
                                            <!-- <td class="text-wrap"> {{ $item->instename }}</td> -->
                                            <td class="text-wrap"> {{ $item->instename }} @if ($item->auditmode == 'P')
                                                    <span class="badge bg-primary ms-2">
                                                        Performance Audit
                                                    </span>
                                                @elseif ($item->datafromapi == 'Y')
                                                    <span class="badge bg-primary ms-2">ERP</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->formatted_fromdate }}</td>
                                            <td>{{ $item->formatted_todate }}</td>
                                            <td>
                                                <!-- Edit Button -->

                                                <a data-index="{{ $index }}"
                                                    class="btn btn-sm btn-primary showentrymeet" id="showentrymeet">
                                                    <i class="fa fa-calendar s-4 me-2"></i>
                                                    <span class="lang">Entry Meeting</span>
                                                </a>

                                                <a id="auditslipshowbtn{{ $index }}"
                                                    class="btn btn-sm btn-success hide_this"
                                                    href="{{ route('audit_slip', [
                                                        'id' => $item->encrypted_auditscheduleid,
                                                        'instid' => $item->instid,
                                                        'spilloverflag' => $item->spilloverflag,
                                                        'api' => $item->datafromapi,
                                                    ]) }}">

                                                    <i class="ti ti-plus fs-4 me-2"></i>
                                                    <span key="addAuditSlip" class="lang">Audit Slip</span>
                                                </a>

                                                <a id="prf_auditshowbtn{{ $index }}"
                                                    class="btn btn-sm btn-success hide_this"
                                                    href="{{ route('prf_slip', ['id' => $item->encrypted_auditscheduleid, 'instid' => $item->instid, 'auditmode' => $item->auditmode, 'auditplanid' => $item->auditplanid]) }}">
                                                    <i class="ti ti-plus fs-4 me-2"></i>
                                                    <span key="prfauditslip" class="lang">Performance Audit</span>
                                                </a>
                                                <input type="hidden" id="auditmode{{ $index }}"
                                                    value="{{ $item->auditmode }}">
                                                <input type="hidden" id="titleename{{ $index }}"
                                                    value="{{ $item->titleename }}">
                                                <input type="hidden" id="prf_filedetails{{ $index }}"
                                                    value="{{ $item->prf_filedetails }}">

                                                <input type="hidden" id="schid_hidden{{ $index }}"
                                                    value="{{ $item->encrypted_auditscheduleid }}" />
                                                <input type="hidden" id="entrymeetdate_hidden{{ $index }}"
                                                    value="{{ $item->entrymeetdate }}" />
                                                <input type="hidden" id="exitmeetdate_hidden{{ $index }}"
                                                    value="{{ $item->exitmeetdate }}" />
                                                <input type="hidden" id="fromdate_hidden{{ $index }}"
                                                    value="{{ $item->formatted_fromdate }}" />
                                                <input type="hidden" id="todate_hidden{{ $index }}"
                                                    value="{{ $item->formatted_todate }}" />
                                                <input type="hidden" id="auditteamhead{{ $index }}"
                                                    value="{{ $item->auditteamhead }}" />
                                                <input type="hidden" id="auditscheduleid{{ $index }}"
                                                    value="{{ $item->auditscheduleid }}" readonly />
                                                <input type="hidden" id="diaryflag{{ $index }}"
                                                    value="{{ $item->diaryflag }}">
                                                <input type="hidden" id="auditplanid{{ $index }}"
                                                    value="{{ $item->auditplanid }}">
                                                <input type="hidden" id="exitmeetstatus{{ $index }}"
                                                    value="{{ $item->exitmeetstatus }}">
                                                <input type="hidden" id="proposedexitmeetdate{{ $index }}"
                                                    value="{{ $item->proposedexitmeetdate }}">
                                                <input type="hidden" id="apiforentrymeeting{{ $index }}"
                                                    value="{{ $item->apiforentrymeeting }}">
                                                <input type="hidden" id="apiforexitmeeting{{ $index }}"
                                                    value="{{ $item->apiforexitmeeting }}">
                                                <input type="hidden" id="inlogschedulestatus{{ $index }}"
                                                    value="{{ $item->inlogschedulestatus }}">


                                                <!-- working -->

                                                <input type="hidden" id="instid{{ $index }}"
                                                    value="{{ $item->instid }}">


                                                &nbsp;
                                                @if ($item->slipexists == 'Y' || ($item->performanceslipexists == 'Y' && $item->auditmode == 'P'))
                                                    <a data-index="{{ $index }}"
                                                        class="btn btn-sm btn-primary showexitmeet" id="showexitmeet">
                                                        <i class="fa fa-calendar s-4 me-2"></i>
                                                        <span class="lang">Exit Meeting</span>
                                                    </a>
                                                @endif


                                                <div id="exitMeetingOptionsWrapper"
                                                    class="d-flex justify-content-center mb-3">

                                                </div>



                                                <!-- working -->
                                                <input type="hidden" id="toquarter_hidden"
                                                    value="{{ $Quarter['toquarter'] }}" />
                                                <input type="hidden" id="carryforward_hidden{{ $loop->index }}"
                                                    value="{{ $item->spilloverflag ?? '' }}">
                                                <input type="hidden" id="spillover_hidden{{ $loop->index }}"
                                                    value="{{ $item->carryforwardflag ?? '' }}">



                                                <!-- end -->



                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id='no_data' class='<?php echo $nodatashow; ?>'>
                        <center>No Data Available</center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php

  } else {
    $datashow = 'hide_this';
    $nodatashow = '';
?>
    <div class="row">
        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
                <div class="card-header card_header_color lang" key="fieldauditdel">Field Audit Details</div>
                <div class="card-body"><br>


                    <div id='no_data' class='<?php echo $nodatashow; ?>'>
                        <center>No Data Available</center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php

}


?>





    <!-- <script src="../assets/js/vendor.min.js"></script>  -->

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script>
        function entrymeetingPopup(auditschid, entrymeetdate, formatted_fromdate, auditteamhead, dataindex, spillover,
            exitmeetstatus, inlogschedulestatus, auditmode, performance_auditdet, apiforentrymeeting) {
            var showDocs = entrymeetdate !== null && entrymeetdate !== '' && entrymeetdate !== undefined;
            // if (inlogschedulestatus != 'Y' && exitmeetstatus == 'N' && auditmode != 'P' && (entrymeetdate == null ||
            //         entrymeetdate == '')) {
            //     passing_alert_value(
            //         'Alert',
            //         'Exit Meeting was not completed for the previous schedule. You cannot initiate a new Entry Meeting until it is completed.',
            //         'confirmation_alert',
            //         'alert_header',
            //         'alert_body',
            //         'confirmation_alert'
            //     );
            //     return;
            // }
            var datacontent = '<form id="entrymeetingform" novalidate="novalidate">' +
                '<input name="auditscheduleid" type="hidden" id="auditsch_dwnid" value="' + auditschid + '"/>' +
                '<input name="apientrymeeting" type="hidden" id="apientrymeeting" value="' + apiforentrymeeting + '" />' +
                '<input name="auditmode" type="hidden" id="auditmode" value="' + auditmode + '"/>' +
                '<input name="prf_titleename" type="hidden" id="prf_titleename" value="' + performance_auditdet.titlename +
                '"/>' +
                '<input name="prf_file" type="hidden" id="prf_file" value="' + performance_auditdet.prf_filedetails +
                '"/>' +
                '<div class="row">' +
                '<div class="col-md-12 mb-3">' +
                '<label class="form-label" for="validationDefault02">Proposed Date</label>' +
                '<div class="input-group">' +
                '<input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="dd/mm/yyyy" value="' +
                formatted_fromdate + '" disabled="">' +
                '<span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>' +
                '</div>' +
                '</div>' +

                '<div id="entrymeetdiv" class="col-md-12 mb-3">' +
                '<label class="form-label" for="validationDefault02">Entry Meeting Date</label>' +
                '<div class="input-group">' +
                '<input type="text" class="form-control datepicker" readonly value="' + entrymeetdate +
                '" id="entry_date" onclick="datepicker(\'entry_date\', \'today\', ' + dataindex +
                ')" name="entry_date" placeholder="dd/mm/yyyy">' +
                '<span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>' +
                '</div>' +
                '<label id="from_date-error" class="error hide_this" for="from_date">Entry Meeting Date is required</label>' +
                '</div>';

            //  Conditionally show documents if entrymeetdate is provided
            if (showDocs && spillover === 'N') {
                datacontent +=
                    '<div class="col-md-6">' +
                    '<label class="form-label" for="validationDefault02">Entry Meeting</label>' +
                    '<div class="card overflow-hidden">' +
                    '<div class="d-flex flex-row">' +
                    '<div class="p-2 align-items-center">' +
                    '<h3 class="text-danger box mb-0 round-56 p-2"><i class="ti ti-file-text"></i></h3>' +
                    '</div>' +
                    '<div class="p-3"><h3 class="text-dark mb-0 fs-3">Entrymeeting.pdf</h3></div>' +
                    '<div class="p-3 align-items-center ms-auto">' +
                    '<h3 class="text-primary box mb-0" onclick="downloadFile(1)"><i class="ti ti-download"></i></h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '<div class="col-md-6">' +
                    '<label class="form-label" for="validationDefault02">Code of Ethics</label>' +
                    '<div class="card overflow-hidden">' +
                    '<div class="d-flex flex-row">' +
                    '<div class="p-2 align-items-center">' +
                    '<h3 class="text-danger box mb-0 round-56 p-2"><i class="ti ti-file-text"></i></h3>' +
                    '</div>' +
                    '<div class="p-3"><h3 class="text-dark mb-0 fs-3">codeofethics.pdf</h3></div>' +
                    '<div class="p-3 align-items-center ms-auto">' +
                    '<h3 class="text-primary box mb-0" onclick="downloadFile(2)"><i class="ti ti-download"></i></h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }

            datacontent += '</div><div class="row mt-2"></form>';


            passing_large_alert('Entry Meeting', datacontent, 'large_confirmation_alert',
                'large_alert_header',
                'large_alert_body', 'forward_alert', 'send_intimation_label');


            $('#entrymeetdiv').show();

            if (entrymeetdate) {

                $("#large_modal_process_button").hide();
                $("#large_modal_cancel_button").hide();
                $('#entry_date').prop('disabled', true);
            } else if (auditteamhead == 'N') {
                $("#large_modal_process_button").hide();
                $("#large_modal_cancel_button").hide();
                $('#entry_date').prop('disabled', true);
                $('#entrymeetdiv').hide();
                if (entrymeetdate) {
                    $('#entrymeetdiv').show();
                }

            } else {

                $("#large_modal_process_button").show();
                $("#large_modal_cancel_button").show();
                $('#entry_date').prop('enabled', true);

                $("#large_modal_process_button").html('Submit Entry Meeting');
                $("#large_modal_process_button").addClass("entrymeetbtn_popup");
            }

            $('#large_modal_process_button').removeAttr('data-bs-dismiss');


        }


        $(document).on('click', '.showexitmeet', function(event) {
            var dataindex = $(this).data('index');
            var auditmode = $('#auditmode' + dataindex + '').val();
            var scheduleId = $('#schid_hidden' + dataindex + '').val();
            var instid = $('#instid' + dataindex).val();
            var entryMeetDate = $('#entrymeetdate_hidden' + dataindex + '').val();
            var exitMeetDate = $('#exitmeetdate_hidden' + dataindex + '').val();
            var auditteamhead = $('#auditteamhead' + dataindex + '').val();
            var apiforexitmeeting = $('#apiforexitmeeting' + dataindex + '').val();
            $('.indexdyn').val(dataindex);
            exitmeetingPopup(scheduleId, exitMeetDate, entryMeetDate, '', auditteamhead, dataindex, instid, '',
                auditmode, apiforexitmeeting)

        });






        $(document).on('click', '#submitExitChoice', function() {
            const auditscheduleid = $('#modal_auditscheduleid').val();
            const instid = $('#modal_instid').val();
            const exitmeetdate = $('#modal_exitMeetDate').val();
            const nextQuarterDate = $('#nextQuarterDate').val();
            $('#extra_large_confirmation_alert').modal('hide');



            const dateStr = $('#nextQuarterDate').val();

            if (dateStr) {
                const [day, month, year] = dateStr.split('/');
                const selectedDate = new Date(year, month - 1, day);
                const dayOfWeek = selectedDate.getDay();

                if (dayOfWeek === 0 || dayOfWeek === 6) {

                    passing_alert_value(
                        'Alert',
                        'Submission not allowed on Saturday and Sunday.',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                    return;
                }
            }

            passing_alert_value(
                'Confirmation',
                "Do you want to proceed with a partial Exit Meeting?",
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            setTimeout(() => {
                $('#cancel_button').html('Cancel').show();

            }, 50);



            $(document).off('click', '#process_button').on('click', '#process_button', function(event) {
                event.preventDefault();
                // $('#confirmation_alert').modal('hide');
                const dataToSend = {
                    auditscheduleid: auditscheduleid,
                    instid: instid,
                    exitmeetdate: exitmeetdate,
                    diaryflag: 'Y',
                    nextQuarterDate: nextQuarterDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                $.ajax({
                    url: "{{ route('FiedAudit.confirmationdiary') }}",
                    type: 'POST',
                    data: dataToSend,
                    success: function(response) {
                        if (response.success) {
                            setTimeout(() => {

                                getLabels_jsonlayout([{
                                    id: response.message,
                                    key: response.message
                                }], 'N').then((text) => {
                                    passing_alert_value(
                                        'Confirmation',
                                        'Process Completed/Updated Successfully',
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );
                                }, 500);


                                setTimeout(() => {
                                    $("#ok_button").html('Ok');
                                }, 50);




                                $("#ok_button").off('click').on('click', function() {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 50);
                                });

                            });
                        } else {
                            passing_alert_value(
                                'Alert',
                                response.message || 'Submission failed.',
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'confirmation_alert'
                            );
                        }
                    },
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);

                        if (response.message === 'not_all_statusflags_are_Y') {
                            getLabels_jsonlayout([{
                                id: 'notcomplete',
                                key: 'notcomplete'
                            }], 'N').then((text) => {
                                const heading = Object.values(text)[0] ||
                                    'Audit Diary Not Completed These Users';
                                const userLines = (response.pending_userids || []).map(
                                    name => `• ${name}`).join('<br>');


                                setTimeout(() => {

                                    passing_alert_value(
                                        'Alert',
                                        `${heading}<br><br>${userLines}`,
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );

                                }, 200);

                                setTimeout(() => {
                                    $("#ok_button").html('Ok');
                                }, 200);



                                $("#ok_button").off('click').on('click', function() {
                                    $("#extra_large_confirmation_alert").hide();

                                    setTimeout(() => {
                                        location.reload();
                                    }, 200);


                                });
                            });
                        } else {
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] ||
                                    "Error Occurred";
                                passing_alert_value(
                                    'Confirmation',
                                    alertMessage,
                                    'confirmation_alert',
                                    'alert_header',
                                    'alert_body',
                                    'confirmation_alert'
                                );
                            });
                        }
                    },

                });
            });
        });



        ///////////////////////////////////////////////End///////////////////////////////////////////////////////////////




        function exitmeetingPopup(auditschid, exitmeetdate, entrymeetdate, showtype = '', auditteamhead, dataindex,
            instid, auditplanid, auditmode, apiforexitmeeting) {

            var showDocs = exitmeetdate !== null && exitmeetdate !== '' && exitmeetdate !== undefined;
            const spillover = $('#spillover_hidden' + dataindex).val();

            var datacontent = '<form name="exit_form" id="exit_form">' +
                '<div class="row">' +
                '<div id="exitmeetdiv" class="col-md-12 mb-3">' +
                '<label class="form-label" for="validationDefault02">Exit Meeting Date</label>' +
                '<input type="hidden" name="auditscheduleid" id="auditsch_dwnid" value="' + auditschid + '">' +
                '<input type="hidden" name="auditmode" id="auditmode" value="' + auditmode + '">' +
                '<input type="hidden" name="entrymeetdate_hidden" id="entrymeetdate_hidden" value="' + entrymeetdate +
                '">' +
                '<input type="hidden" name="spillover" id="spillover_value" value="' + spillover + '">' +
                '<input type="hidden" name="auditplanid" id="auditplanid" value="' + auditplanid + '">' +
                '<input type="hidden" name="apiforexitmeeting" id="apiforexitmeeting" value="' + apiforexitmeeting + '">' +
                '<input type="hidden" name="instid" id="instid" value="' + instid + '">' +
                '<div class="input-group">' +
                '<input type="text" class="form-control datepicker" id="exit_date"   readonly name="exit_date" onclick="datepicker(\'exit_date\', \'today\', ' +
                dataindex + ')" placeholder="dd/mm/yyyy" value="' + exitmeetdate + '" />' +

                '<span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>' +
                '</div>' +
                '<label id="exit_date-error" class="error hide_this" for="exit_date">Exit Meeting Date is required</label>' +
                '</div>';

            if (showDocs) {
                datacontent += '<div class="col-md-6">' +
                    '<label class="form-label" for="validationDefault02">Exit Meeting</label>' +
                    '<div class="card overflow-hidden" style="border-color: #7198b9">' +
                    '<div class="d-flex flex-row">' +
                    '<div class="p-2 align-items-center">' +
                    '<h3 class="text-danger box mb-0 round-56 p-2"><i class="ti ti-file-text"></i></h3>' +
                    '</div>' +
                    '<div class="p-3">' +
                    '<h3 class="text-dark mb-0 fs-3">Exitmeeting.pdf</h3>' +
                    '</div>' +
                    '<div class="p-3 align-items-center ms-auto">' +
                    '<h3 class="text-primary box mb-0" onclick="downloadFile(3)"><i class="ti ti-download"></i></h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }

            datacontent += '</div></form>';
            //return datacontent;




            if (onchange != 'Y') {

                passing_extra_large_alert('Exit Meeting', datacontent, 'extra_large_confirmation_alert',
                    'extra_large_alert_header',
                    'extra_large_alert_body', 'forward_alert', 'send_intimation_label');

                $('#exitmeetdiv').show();

            }


            if (exitmeetdate) {

                $("#extra_large_modal_process_button").hide();
                $("#extra_large_modal_cancel_button").hide();
                $('#exit_date').prop('disabled', true);

            } else if (auditteamhead == 'N') {
                $("#extra_large_modal_process_button").hide();
                $("#extra_large_modal_cancel_button").hide();
                $('#exitmeetdiv').hide();
                if (exitmeetdate) {
                    $('#exitmeetdiv').show();
                    $('#exit_date').prop('disabled', true);
                }

            } else {

                $("#extra_large_modal_process_button").show();
                $("#extra_large_modal_cancel_button").show();
                $('#exit_date').prop('enabled', true);
                $("#extra_large_modal_process_button")
                    .removeClass("exitmeetbtn_popup")
                    .addClass("exitmeetbtn_popup")
                    .data("index", dataindex)
                    .html('Submit Exit Meeting')
                    .show();
                // $("#extra_large_modal_process_button").html('Submit Exit Meeting');
                // $("#extra_large_modal_process_button").addClass("exitmeetbtn_popup").attr("data-index", dataindex);
            }
            $('#extra_large_modal_process_button').removeAttr('data-bs-dismiss');

            if (onchange == 'Y') {

                return datacontent;


            }




        }

        function datepicker(value, setdate = null, index = null) {
            var today = new Date();


            var todatehidden = $('#todate_hidden' + index + '').val(); // Get the value (e.g., "29/04/2025")
            var parts = todatehidden.split('/');
            var todate = new Date(parts[2], parts[1] - 1, parts[0]);


            if (value == 'entry_date') {
                var minDate = new Date();
                var maxDate = new Date();

            } else {
                var minDate = new Date();
                var maxDate = new Date();

            }
            var formname = 'entryandexitmeetform';

            init_datepicker(value, minDate, maxDate, setdate, formname)
        }

        let holidaysget = [];

        // Fetch holidays (assumes Laravel returns array of objects with 'date' in 'dd/mm/yyyy' format)
        $.ajax({
            url: '/fetch-holidays',
            method: 'GET',
            async: false,
            success: function(data) {
                holidaysget = data.map(item => {
                    const [dd, mm, yyyy] = item.date.split('/');
                    return `${yyyy}-${mm}-${dd}`; // convert to 'yyyy-mm-dd'
                });
                //console.log("Formatted Holidays:", holidaysget);
            },
            error: function() {
                console.error("Failed to fetch holidays.");
            }
        });

        // Format a Date object to 'yyyy-mm-dd' string
        function formatISODate(date) {
            const yyyy = date.getFullYear();
            const mm = ('0' + (date.getMonth() + 1)).slice(-2);
            const dd = ('0' + date.getDate()).slice(-2);
            return `${yyyy}-${mm}-${dd}`;
        }

        // Check if a date is a business day (not weekend and not holiday)
        function isBusinessDay(date) {
            const day = date.getDay();
            const formattedDate = formatISODate(date);
            return day !== 0 && day !== 6 && !holidaysget.includes(formattedDate);
        }

        // Subtract business days
        function subtractBusinessDays(startDate, daysToSubtract) {
            let result = new Date(startDate);
            while (daysToSubtract > 0) {
                result.setDate(result.getDate() - 1);
                if (isBusinessDay(result)) {
                    daysToSubtract--;
                }
            }
            return result;
        }

        // Add business days
        function addBusinessDays(startDate, daysToAdd) {
            let result = new Date(startDate);
            while (daysToAdd > 0) {
                result.setDate(result.getDate() + 1);
                if (isBusinessDay(result)) {
                    daysToAdd--;
                }
            }
            return result;
        }

        function downloadFile(filename) {
            if (filename == 1) {
                var filename = 'entrymeeting';
            } else if (filename == 2) {
                var filename = 'codeofethics';
            } else {
                var filename = 'exitmeeting';
            }
            var auditsch_dwnid = $('#auditsch_dwnid').val();
            var language = window.localStorage.getItem('lang');
            let fileWithLanguage = '/' + filename + '?lang=' + language + '&auditscheduleid=' + auditsch_dwnid;
            window.location.href = fileWithLanguage;

        }


        $(document).on('click', '.exitmeetbtn_popup', function(event)

            {

                var exitdatefield = $('#exit_date').val();

                if (exitdatefield) {


                    const dataindex = $(this).data('index');
                    const auditmode = $('#auditmode' + dataindex).val();

                    const auditplanid = $('#auditplanid' + dataindex).val();
                    const auditscheduleid = $('#auditscheduleid' + dataindex).val();

                    const instid = $('#instid' + dataindex).val();
                    const exitmeetdate = $('#exitmeetdate_hidden' + dataindex).val();
                    const nextQuarterDate = $('#nextQuarterDate').val();
                    const cauditscheduleid = auditscheduleid;



                    const dataToSend = {
                        auditscheduleid: auditscheduleid,
                        instid: instid,
                        auditmode: auditmode,
                        exitmeetdate: exitmeetdate,
                        diaryflag: 'Y',
                        nextQuarterDate: nextQuarterDate,
                        _token: $('meta[name="csrf-token"]').attr('content')

                    };
                    $.ajax({
                        url: "{{ route('FiedAudit.pendingusersfornotspillover') }}",
                        type: 'POST',
                        data: dataToSend,
                        success: function(response) {
                            if (response.success) {

                                if (auditmode == 'P') {
                                    const exitmmeet_state = update_perfomance_schedule();
                                    if (exitmmeet_state) {
                                        passing_alert_value('Confirmation',
                                            'Exit Meeting Date was updated successfully',
                                            'confirmation_alert', 'alert_header', 'alert_body',
                                            'confirmation_alert');
                                        $('#confirmation_alert').css('z-index', 10000000);
                                        $("#large_confirmation_alert").modal("hide");
                                        $("#extra_large_confirmation_alert").modal("hide");
                                        //location.reload();

                                        setTimeout(() => {
                                            $('#ok_button').html('Ok').show();

                                        }, 50);
                                        $("#ok_button").off('click').on('click', function() {

                                            setTimeout(() => {
                                                location.reload();
                                            }, 200);

                                            $("#extra_large_confirmation_alert").modal("hide");

                                            var indexdyn = $('.indexdyn').val();
                                        });
                                    }
                                } else {




                                    $('#exit_date-error').hide();

                                    let superchecklists = JSON.parse(@json($supercheckquestions_json));

                                    // Use auditscheduleid as key (make sure it's a string)
                                    let key = cauditscheduleid.toString();

                                    let parsedSuperchecklists = [];

                                    if (superchecklists && superchecklists[key]) {
                                        parsedSuperchecklists = superchecklists[key];
                                        // console.log('Data found:', parsedSuperchecklists);
                                    } else {
                                        // console.log('No data found for auditscheduleid:', cauditscheduleid);
                                    }

                                    // console.log(parsedSuperchecklists);

                                    let parsedSuperchecklistsArray = Object.values(parsedSuperchecklists);

                                    // Now you can use reduce
                                    let groupedData = parsedSuperchecklistsArray.reduce((acc, question) => {
                                        if (!acc[question.part_no]) {
                                            acc[question.part_no] = [];
                                        }
                                        acc[question.part_no].push(question);
                                        return acc;
                                    }, {});

                                    var auditscheduleid = $('#auditscheduleid' + dataindex).val();
                                    const auditplanid = $('#auditplanid' + dataindex).val();

                                    let htmlContent =
                                        '<div class="scrolldiv"><form id="quesans_form"><input type="hidden" name="auditscheduleid"  value="' +
                                        auditscheduleid +
                                        '" class=""/><input type="hidden" name="auditplanid" value="' +
                                        auditplanid +
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
                                            <table class="table table-bordered superchecktable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%" style="text-align:center;">S.No</th>
                                                        <th width="70%" style="text-align:center;">Question</th>
                                                        <th width="25%" style="text-align:center;">Answer</th>
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
                                            <td style="text-align:center;">${question.sl_no}</td>
                                            <td>${question.checkpoint_en}</td>
                                            <td style="text-align:center;">
                                        `;

                                                // Check question type and add the appropriate input for the answer
                                                if (question.question_type === 'O') {
                                                    // For "Yes/No" question type, display radio buttons with Bootstrap styling
                                                    htmlContent += `<div >
                                                            <input class="form-check-input"  name="answer_remarks[${question.supercheckid}][]" type="checkbox" id="yes_${question.supercheckid}" value="Yes">
                                                            <label class="form-check-label" for="yes_${question.supercheckid}"></label>
                                                        </div>`;
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
                                    htmlContent +=
                                        `<span style="color:red;">* All fields are mandatory</span></form></div>`;
                                    $("#large_modal_process_button").show();
                                    $("#large_modal_cancel_button").show();
                                    passing_large_alert('Super Check List', htmlContent,
                                        'large_confirmation_alert',
                                        'large_alert_header',
                                        'large_alert_body', 'forward_alert');
                                    $("#large_modal_process_button").html("Submit");
                                    $("#large_modal_process_button").addClass("supercheckbtn");
                                    $('#large_modal_process_button').removeAttr('data-bs-dismiss');
                                    $('#large_confirmation_alert').css('z-index', 100000);
                                    // $("#extra_large_modal_process_button").hide();




                                    // setTimeout(() => {
                                    //             location.reload();
                                    // }, 200);
                                }



                            } else {

                                console.log('Supercheck not Found')


                            }
                        },

                        error: function(xhr) {
                            const response = JSON.parse(xhr.responseText);
                            $("#extra_large_confirmation_alert").hide();

                            if (response.message === 'not_all_statusflags_are_Y') {
                                getLabels_jsonlayout([{
                                    id: 'notcomplete',
                                    key: 'notcomplete'
                                }], 'N').then((text) => {
                                    const heading = Object.values(text)[0] ||
                                        'Audit Diary Not Completed These Users';
                                    const userLines = (response.pending_userids || []).map(name =>
                                        `• ${name}`).join('<br>');


                                    setTimeout(() => {

                                        passing_alert_value(
                                            'Alert',
                                            `${heading}<br><br>${userLines}`,
                                            'confirmation_alert',
                                            'alert_header',
                                            'alert_body',
                                            'confirmation_alert'
                                        );

                                    }, 200);

                                    $("#ok_button").html('Ok');

                                    $("#extra_large_confirmation_alert").modal('hide');

                                    $("#ok_button").off('click').on('click', function() {
                                        $("#extra_large_confirmation_alert").modal('hide');

                                        setTimeout(() => {
                                            location.reload();
                                        }, 200);


                                    });
                                });
                            } else if (response.message === 'cps_not_finalized') {
                                getLabels_jsonlayout([{
                                    id: 'cpsnotfinalized',
                                    key: 'cpsnotfinalized'
                                }], 'N').then((text) => {

                                    const message = Object.values(text)[0] ||
                                        'The Auditee has not finalized the CPS details. Please complete the CPS process before proceeding.';

                                    passing_alert_value(
                                        'Alert',
                                        message,
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );

                                    $("#ok_button").html('Ok');

                                    $("#ok_button").off('click').on('click', function() {

                                        $("#extra_large_confirmation_alert").modal('hide');

                                    });

                                });
                            } else if (response.message === 'not_replytoauditee') {
                                let tableHtml = `
		<p><b>The following users have not processed the auditee’s reply for the slip numbers.</b></p>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>User Name</th>
					<th>Slip Numbers</th>
				</tr>
			</thead>
			<tbody>
	`;

                                response.not_reply_users.forEach(user => {
                                    tableHtml += `
			<tr>
				<td>${user.username} - ${user.desigesname}</td>
				<td>${user.slip_numbers}</td>
			</tr>
		`;
                                });

                                tableHtml += `
			</tbody>
		</table>
		<p><b>The exit meeting can be conducted only after the completion of these pending actions.</b></p>
	`;

                                passing_alert_value(
                                    'Alert',
                                    tableHtml,
                                    'confirmation_alert',
                                    'alert_header',
                                    'alert_body',
                                    'confirmation_alert'
                                );

                                $("#extra_large_confirmation_alert").modal('hide');


                                $("#ok_button").off('click').on('click', function() {
                                    $("#extra_large_confirmation_alert").modal('hide');
                                    setTimeout(() => {
                                        location.reload();
                                    }, 200);


                                });


                                return;

                            } else {
                                getLabels_jsonlayout([{
                                    id: response.message,
                                    key: response.message
                                }], 'N').then((text) => {
                                    let alertMessage = Object.values(text)[0] || "Error Occurred";
                                    passing_alert_value(
                                        'Confirmation',
                                        alertMessage,
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );
                                });
                            }
                        },

                    });
                } else {
                    $('#exit_date-error').show();

                }

            });



        $(document).on('click', '.supercheckbtn', function(event) {
            let allChecked = $('.form-check-input[type="checkbox"]').length === $(
                '.form-check-input[type="checkbox"]:checked').length;
            let validTextArea = true;
            let validNumber = true;


            $('textarea.form-control').each(function() {
                let errorSpan = $(this).next('.error-message'); // Check if error span exists
                let textValue = $(this).val().trim(); // Get the trimmed value

                if (errorSpan.length === 0) {
                    $(this).after(
                        '<span class="error-message" style="color: red; font-size: 12px;"></span>');
                    errorSpan = $(this).next('.error-message'); // Reassign after adding
                }

                if (textValue.length === 0) {
                    errorSpan.text('Enter details');
                    validTextArea = false;
                } else if (textValue.length < 10) {
                    errorSpan.text('Enter at least 10 characters');
                    validTextArea = false;
                } else if (textValue.length > 150) {
                    errorSpan.text('Enter a maximum of 150 characters');
                    validTextArea = false;
                } else {
                    errorSpan.text(''); // Clear error message if valid
                }
            });

            // Stop form submission if invalid
            if (!validTextArea) {
                return;
            }

            // Proceed if validation passes
            console.log('Form is valid!');

            // Validate numerical inputs (must contain a value)
            $('input[type="number"].form-control').each(function() {
                if ($(this).val().trim() === '') {
                    validNumber = false;
                    // $(this).css('border', '1px solid red'); // Highlight invalid fields
                } else {
                    //$(this).css('border', ''); // Reset border if valid
                }
            });

            if (allChecked && validTextArea && validNumber) {
                var confirmation =
                    '<input type="hidden" id="whichtype" value="exitmeet"/>Do you want to exit the meeting?';
                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                $("#process_button").html("Yes");
                $("#cancel_button").html("No");


            } else {
                var confirmation = "Please ensure all the points are verified and give proper feedback.";
                passing_alert_value('Validation Error', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');
            }

            $('#confirmation_alert').css('z-index', 10000000);
        });


        $(document).on('click', '.showentrymeet', function(event) {
            var dataindex = $(this).data('index');
            var titlename = $('#titleename' + dataindex + '').val();
            var prf_filedetails = $('#prf_filedetails' + dataindex + '').val();
            var auditmode = $('#auditmode' + dataindex + '').val();
            const performance_auditdet = {
                titlename,
                prf_filedetails
            };

            var exitmeetstatus = $('#exitmeetstatus' + dataindex + '').val();
            const spillover = $('#spillover_hidden' + dataindex).val(); // Get carryforward value

            var scheduleId = $('#schid_hidden' + dataindex + '').val();
            var entryMeetDate = $('#entrymeetdate_hidden' + dataindex + '').val();
            var Fromdate = $('#fromdate_hidden' + dataindex + '').val();
            var auditteamhead = $('#auditteamhead' + dataindex + '').val();
            var apiforentrymeeting = $('#apiforentrymeeting' + dataindex + '').val();
            var inlogschedulestatus = $('#inlogschedulestatus' + dataindex + '').val();
            $('.indexdyn').val(dataindex);
            entrymeetingPopup(scheduleId, entryMeetDate, Fromdate, auditteamhead, dataindex, spillover,
                exitmeetstatus, inlogschedulestatus, auditmode, performance_auditdet, apiforentrymeeting)

        });

        var sizeoftable = $('.sizeoftable').val();
        for (var i = 0; i < sizeoftable; i++) {
            var entryMeetDate = $('#entrymeetdate_hidden' + i + '').val();
            var audit_mod_val = $('#auditmode' + i + '').val();
            if (entryMeetDate && audit_mod_val == 'P') {
                $('#prf_auditshowbtn' + i + '').show();

                $('#auditslipshowbtn' + i + '').hide();
            }
            if (entryMeetDate && audit_mod_val != 'P') {
                $('#auditslipshowbtn' + i + '').show();
                $('#prf_auditshowbtn' + i + '').hide();
            }


        }



        function update_perfomance_schedule() {
            var formData = $('#exit_form').serialize();
            var lang = getLanguage('Y');

            formData += '&lang=' + encodeURIComponent(lang);


            try {
                let response = $.ajax({
                    url: '/audit/update_exitmeet',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData
                });

                return response;
            } catch (xhr) {
                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'An unknown error occurred';
                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header', 'alert_body',
                    'confirmation_alert');
                console.error('Error details:', xhr);
                return null; // Return null on error
            }
        }






        $(document).on('click', '.entrymeetbtn_popup', function(event) {
            var entrymeetfield = $('#entry_date').val();
            var auditmode = $('#auditmode').val();

            if (entrymeetfield) {
                $('#from_date-error').hide();
                if (auditmode == 'P') {

                    // Get values (adjust source if needed)
                    var pop_titleename = $('#prf_titleename').val();
                    var prf_filedetails = $('#prf_file').val();

                    if (prf_filedetails && prf_filedetails !== '-') {

                        var fileParts = prf_filedetails.split('-');

                        var filename = fileParts[0];
                        var filepath = fileParts[1];

                        // Fix slashes for browser
                        filepath = filepath.replace(/\\/g, '/');

                        var fileUrl = filepath; // adjust base path if needed

                        var popupContent = `
                                      <div style="padding:10px;">
                                          <h5><b>Title : </b>${pop_titleename}</h5>
                                          <hr>
                                           <h5><b> Audit Design Matrix</h5>
                                          <iframe
                                              src="${fileUrl}"
                                              width="100%"
                                              height="500px"
                                              style="border:1px solid #ccc;">
                                          </iframe>
                                      </div>
                                  `;
                        passing_alert_value('Performance Audit Details', popupContent,
                            'extra_large_confirmation_alert',
                            'extra_large_alert_header',
                            'extra_large_alert_body', 'forward_alert');
                        $("#extra_large_modal_process_button").html('Submit Entry Meeting');
                        $("#extra_large_modal_process_button").addClass("entrymeetbtn_performance_popup");
                        $('#extra_large_confirmation_alert')
                            .modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                        $('#confirmation_alert').css('z-index', 100000);
                    }
                } else {


                    var confirmation =
                        '<input type="hidden" id="whichtype" value="entrymeet"/>Are you sure to submit?';
                    passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                        'alert_body', 'forward_alert');
                    $('#confirmation_alert').css('z-index', 100000);
                }

            } else {
                $('#from_date-error').show();

            }


        });

        async function update_schedule() {
            var formData = $('#exit_form').serialize();
            var lang = getLanguage('Y');

            formData += '&lang=' + encodeURIComponent(lang);


            try {
                let response = await $.ajax({
                    url: '/audit/update_exitmeet',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData
                });

                return response; // Now this returns properly because we used await
            } catch (xhr) {
                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'An unknown error occurred';
                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header', 'alert_body',
                    'confirmation_alert');
                console.error('Error details:', xhr);
                return null; // Return null on error
            }
        }


        $(document).off('click', '.entrymeetbtn_performance_popup')
            .on('click', '.entrymeetbtn_performance_popup', function() {

                var entry_date = $('#entry_date').val();
                var auditmode = $('#auditmode').val();
                var auditscheduleid = $('#auditscheduleid').val();
                var formData = $("#entrymeetingform").serialize();
                $.ajax({
                    url: '/audit/update_entrymeet',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,

                    beforeSend: function() {
                        $('#extra_large_modal_process_button')
                            .prop('disabled', true)
                            .html('Processing...');
                    },

                    success: function(response) {
                        if (response.success) {
                            $('#entrymeetbtn_popup').modal('hide');

                            $('#extra_large_confirmation_alert').modal('hide');

                            // Optional success alert
                            passing_alert_value(
                                'Success',
                                'Entry Meeting Submitted Successfully',
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'forward_alert'
                            );
                            $('#process_button').addClass('per_enrty_ok');
                            $('.per_enrty_ok').off('click').on('click', function(event) {
                                event.preventDefault();
                                var indexdyn = $('.indexdyn').val();

                                var scheduleId = $('#schid_hidden' + indexdyn + '').val(response
                                    .data['encrypted_auditscheduleid']);
                                var entryMeetDate = $('#entrymeetdate_hidden' + indexdyn + '')
                                    .val(
                                        response.data['entrymeetdate']);
                                var FromDate = $('#fromdate_hidden' + indexdyn + '').val(
                                    response
                                    .data['fromdate']);

                                if (entryMeetDate) {
                                    $('#auditslipshowbtn' + indexdyn + '').hide();
                                    $('#prf_auditshowbtn' + indexdyn + '').show();

                                }


                                setTimeout(() => {
                                    location.reload();
                                }, 200);



                            });


                        }
                    },
                    error: function(xhr) {

                        alert('Something went wrong');

                        $('#extra_large_modal_process_button')
                            .prop('disabled', false)
                            .html('Submit Entry Meeting');
                    }
                });

            });
        // Use async/await when calling update_schedule()
        $(document).on('click', '#process_button', async function(event) {
            event.preventDefault();


            var whichtype = $('#whichtype').val();
            var entryorexitform;
            var $FromtoSerialize;
            let exitmeetres = null; // ? Declare exitmeetres outside to make it available globally

            if (whichtype === 'entrymeet') {
                $FromtoSerialize = $("#entrymeetingform");
                entryorexitform = '/audit/update_entrymeet';
            } else {
                $FromtoSerialize = $("#quesans_form");
                entryorexitform = '/Supercheck_QuesAns';

                exitmeetres = await update_schedule(); // ? Now update_schedule() runs before AJAX
                if (!exitmeetres) return; // ? Stop if there was an error
            }

            if ($FromtoSerialize.valid()) {
                //      $('#loader').removeClass('d-none');
                // $('#loader').css({
                // display: 'flex',
                // zIndex: 99999
                // });
                $('.dot-spinner').css('z-index', 100000);
                var formData = $FromtoSerialize.serialize();
                $.ajax({
                    url: entryorexitform,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            passing_alert_value('Confirmation', response.success,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
                            $('#confirmation_alert').css('z-index', 10000000);
                            $("#large_confirmation_alert").modal("hide");
                            $("#extra_large_confirmation_alert").modal("hide");
                            //location.reload();

                            setTimeout(() => {
                                $('#ok_button').html('Ok').show();

                            }, 50);

                            $("#extra_large_confirmation_alert").modal("hide");

                            var indexdyn = $('.indexdyn').val();

                            if (whichtype === 'entrymeet') {
                                var scheduleId = $('#schid_hidden' + indexdyn + '').val(response
                                    .data['encrypted_auditscheduleid']);
                                var entryMeetDate = $('#entrymeetdate_hidden' + indexdyn + '').val(
                                    response.data['entrymeetdate']);
                                var FromDate = $('#fromdate_hidden' + indexdyn + '').val(response
                                    .data['fromdate']);
                            } else if (exitmeetres) { // ? Now exitmeetres is accessible!
                                var scheduleId = $('#schid_hidden' + indexdyn + '').val(exitmeetres
                                    .data['encrypted_auditscheduleid']);
                                var entryMeetDate = $('#entrymeetdate_hidden' + indexdyn + '').val(
                                    exitmeetres.data['entrymeetdate']);
                                var exitMeetDate = $('#exitmeetdate_hidden' + indexdyn + '').val(
                                    exitmeetres.data['exitmeetdate']);
                                var FromDate = $('#fromdate_hidden' + indexdyn + '').val(exitmeetres
                                    .data['fromdate']);

                            }

                            if (entryMeetDate) {
                                $('#auditslipshowbtn' + indexdyn + '').show();

                            }
                            $("#ok_button").off('click').on('click', function() {

                                setTimeout(() => {
                                    location.reload();
                                }, 200);


                            });
                        }
                    },
                    complete: function() {


                        $('#loader').fadeOut(200, function() {
                            $(this).addClass('d-none').css('display', 'flex');
                        });

                    },
                    error: function(xhr) {
                        var response = JSON.parse(xhr.responseText);
                        var errorMessage = response.message || 'An unknown error occurred';
                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        console.error('Error details:', xhr);
                    }
                });
            } else {
                scrollToFirstError();
            }
        });


        function isHolidayOrWeekend(date) {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            const formattedDate = `${yyyy}-${mm}-${dd}`;

            const isWeekend = (date.getDay() === 0 || date.getDay() === 6); // Sunday or Saturday
            return isWeekend || holidaysget.includes(formattedDate);
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0]; // yyyy-mm-dd
        }

        function parseDate(dateStr) {
            var parts = dateStr.split('/');
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }
    </script>
@endsection
