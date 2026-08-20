@section('content')
@section('title', 'Create User Report')

@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">




        <div class=" card ">
            <div class=" card-header card_header_color lang" key="">List of Leave-IN Details
                Leave-In Detail
            </div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this " id="tableshow">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang text-center align-middle" key="s_no">S.No</th>
                                    <th class="lang text-center align-middle">Leave Details</th>
                                    <th class="lang text-center align-middle" key="reasonlabel">Reason</th>
                                    <th class="lang text-center align-middle" key="statusflag">leave - IN</th>
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


<script>
    $(document).ready(function() {
        // $('#createuser')[0].reset();
        // getbuttontext()
        // reset_form()
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        // Load the initial language and initialize the DataTable
        const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en' if no language is set
        initializeDataTable(lang);

        // Change event for language selection dropdown
        $('#translate').change(function() {
            updateTableLanguage(getLanguage(
                'Y')); // Update the table with the new language by destroying and recreating it
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'createuser');
        });
    });


    function initializeDataTable(language) {
        $.ajax({
            url: "/leaveindetails",
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

    function renderTable(language) {
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
                }, {
                    data: "fromdate",
                    render: function(data, type, row) {
                        let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
                            'en-GB') : "N/A";
                        let todate = row.todate ? new Date(row.todate).toLocaleDateString('en-GB') :
                            "N/A";
                        return `${fromdate} - ${todate} </small><br>`;
                    },
                    className: "d-none d-md-table-cell lang"
                },
                {
                    data: "reason",
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        return row.reason || '-';
                    }
                },
                {
                    data: "leavein",
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        const todate = row.todate ? new Date(row.todate) : null;
                        const currentdate = new Date();


                        if ((row.leavein === null || row.leavein === 'N') && todate && currentdate >
                            todate) {
                            console.log('Inside IF condition');
                            if (row.processcode == null) {
                                return `<center><a class="btn btn-danger leavein" id="${row.leaveid}" userid="${row.userid}">Rejoining</a></center>`;
                            } else {
                                return `<center><a class="btn btn-success">${row.processelname}</a></center>`;
                            }
                        } else if (row.leavein === 'Y') {
                            return `<center><a class="btn btn-success">Completed</a></center>`;
                        } else {
                            console.log('Going to ELSE block');
                            return `<center>Currently on leave</center>`;
                        }
                    }

                }


            ],
        });

        const mobileColumns = ["fromdate", "reason", "leavein"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "usertable");
    }

    $(document).on('click', '.leavein', function() {
        var id = $(this).attr('id');
        const userid = $(this).attr('userid');

        if (id) {
            const confirmation = 'Are you sure to sent request for Leave-In?';
            // Always unbind old handlers to avoid multiple AJAX calls
            $('#process_button').off('click').on('click', function() {
                $('#confirmation_alert').modal('hide');
                forwardleavein(id, userid);
            });
            passing_alert_value(
                'Confirmation',
                confirmation,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );
        }
    });



    function forwardleavein(leaveid, userid) {
        if (leaveid && userid) {

            $.ajax({
                url: '/transaction/forward_application', // Your API route to get user details
                method: 'POST',
                data: {
                    // userid: userid,
                    // userchargeid: forwardto_userchargeid,
                    id: leaveid,
                    transactiontypecode: '11',
                    action: 'first',
                    userid: userid


                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.status == 'success') {
                        // $('#display_error').hide();
                        // change_button_as_update('othertrans_form', 'action', 'buttonaction',
                        //     'display_error', '', '');
                        // // validator.resetForm();

                        // passing_alert_value('Confirmation', response.message,
                        //     'confirmation_alert', 'alert_header', 'alert_body',
                        //     'confirmation_alert');

                        // reset_form();
                        // fetchAlldata(lang);
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');

                        reset_form();
                        fetchAlldata();
                        table.ajax.reload();


                    } else {
                        alert(response.message);

                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            passing_alert_value(
                'Confirmation',
                'Some Values not correctly passed',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert'
            );
        }
    }
</script>


@endsection
