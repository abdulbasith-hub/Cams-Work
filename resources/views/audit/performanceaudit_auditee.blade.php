@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Performance Audit - Auditee')

<style>
    /* Initially make the tabs visible */
    section {
        display: none;
    }

    .card_dark {
        border: 1px solid #7198b9;
    }
</style>

<div class="card card_border " id="performance_audit_div">
    <div class="card-header card_header_color">Performance Audit Details</div>
    <div class="card-body mb-4">
        <form name="prf_form" id="prf_form"></form>
        <input type="hidden" name="prf_auditscheduleid" id="prf_auditscheduleid" />
        <div class="row ">
            <div class="col-md-2  mx-auto">
                <button class="btn btn-success mt-3 lang hide_this" key="submit" type=""
                    id="prf_audit_submit">Accept</button>

            </div>

        </div>
        <div id="prf_statusmessage" class="row  hide_this">
            <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                <span class="lang" key="successmsg_auditee">Data has been submitted
                    successfully.</span>
            </div>
        </div>

        <div id='no_data' class='hide_this lang text-center' key="no_data">
            <center class="lang" key="no_data">No Data Available</center>

        </div>
    </div>
</div>

<script src="../assets/js/vendor.min.js"></script>
<!-- Import Js Files -->



<!-- solar icons -->
<!-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> -->
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>

<script>
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

    function fetchalldata(lang) {
        $.ajax({
            url: 'audit/audit_scheduledetails',
            data: {
                auditmode: 'P'
            },
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {


                audit_schedule = response.data;
                $auditmode = audit_schedule[0].auditmode;
                if ($auditmode == 'P') {
                    $('#no_data').hide()
                    populate_performanceAudit(response)

                } else {
                    $('#no_data').show()
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

                    $('#no_data').show()
                }
            }
        });
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
</script>

@endsection
