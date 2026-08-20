@section('content')
@section('title', 'Create User Report')

@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">


<head>

    <title>API Schedule User Update</title>

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">


</head>

<body>

    <div class="card ">
        <div class="card-header card_header_color lang" key="">Change of Auditor details at ERP</div>
        <div class="card-body"><br>
            <div class="datatables">
                <div class="table-responsive "">


                    <div id="noDataMessage" style="display:none; text-align:center; padding:10px;">
                        No Details found
                    </div>



                    <table id="scheduleTable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic hide_this">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Institution</th>
                                <th>Auditor in ERP </th>
                                <th>Auditor in CAMS</th>
                                <th>Leave Details</th>

                                <th>Schedule details</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>

                </div>
            </div>
        </div>
    </div>


    <!-- JQuery -->

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


    <script>
        $(document).ready(function() {

            get_upatechangeuserdel();

            // =========================================================
            // UPDATE BUTTON CLICK
            // =========================================================
            $(document).on('click', '.updateBtn', function() {

                let leaveid = $(this).data('leaveid');
                let othertransid = $(this).data('othertransid');

                var confirmation = `Are you sure to forward the details?`;

                document.getElementById("process_button").onclick = function() {
                    sentthepayloadtoerp(leaveid, othertransid)
                };
                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');


            });

        });

        function sentthepayloadtoerp(leaveid, othertransid) {

            $.ajax({
                url: '/updateuserdel',
                type: 'POST',

                data: {
                    _token: '{{ csrf_token() }}',
                    leaveid: leaveid,
                    othertransactionid: othertransid
                },

                beforeSend: function() {

                    $('.updateBtn').prop('disabled', true);

                },

                success: function(response) {

                    passing_alert_value(
                        'Alert',
                        response.message || 'Success',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    get_upatechangeuserdel();
                    //$('.updateBtn').prop('disabled', false);
                },

                error: function(xhr) {

                    let res = xhr.responseJSON;

                    passing_alert_value(
                        'Error',
                        response.message || 'Server Error',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    console.log(xhr.responseText);

                    $('.updateBtn').prop('disabled', false);
                }
            });
        }


        // =========================================================
        // LOAD DATATABLE
        // =========================================================
        function get_upatechangeuserdel() {

            if ($.fn.DataTable.isDataTable('#scheduleTable')) {
                $('#scheduleTable').DataTable().destroy();
            }

            $.ajax({
                url: "{{ route('get_api_changeuserdel') }}",
                type: "GET",

                success: function(json) {

                    let data = json.data || [];

                    // =========================
                    // NO DATA
                    // =========================
                    if (data.length === 0) {

                        $('#scheduleTable').hide();
                        $('#noDataMessage').show();

                        return;
                    }

                    // =========================
                    // DATA EXISTS
                    // =========================
                    $('#noDataMessage').hide();
                    $('#scheduleTable').show();

                    $('#scheduleTable').DataTable({

                        processing: true,
                        data: data,

                        // =========================
                        // FIT TABLE INSIDE PAGE
                        // =========================
                        responsive: true,
                        autoWidth: false,
                        scrollX: false,
                        paging: true,
                        searching: true,
                        ordering: true,

                        columnDefs: [{
                            targets: '_all',
                            className: 'text-wrap'
                        }],

                        columns: [

                            {
                                data: null,
                                width: "5%",
                                render: (d, t, r, m) => m.row + 1
                            },

                            {
                                data: 'instename',
                                width: "20%"
                            },

                            {
                                data: null,
                                width: "15%",
                                render: function(data, type, row) {
                                    return `
                                <b>${row.username}</b><br>
                                <small class="text-primary">
                                    (${row.desigesname})
                                </small>
                            `;
                                }
                            },

                            {
                                data: null,
                                width: "15%",
                                render: function(data, type, row) {
                                    return `
                                <b>${row.tousername}</b><br>
                                <small class="text-primary">
                                    (${row.todesignesname})
                                </small>
                            `;
                                }
                            },

                            {
                                data: null,
                                width: "25%",
                                render: function(data, type, row) {
                                    return `
                                    <b>From:</b> ${ChangeDateFormat(row.fromdate)}<br>
                                    <b>To:</b> ${ChangeDateFormat(row.todate)}<br>
                                    <b>Reason:</b> ${row.reason || ''}<br>

                                    <b>Approved By:</b> ${row.approvedbyusername || ''} 
                                    (${row.approvedbydesignation || ''})<br>

                                    <b>Approved On:</b> ${ChangeDateFormat(row.updatedon)}
                                `;
                                }
                            }, {
                                data: null,
                                width: "15%",
                                render: function(data, type, row) {
                                    return `
                                <b>Entry:</b> ${ChangeDateFormat(row.entrymeetdate)}<br>
                                <b>Exit:</b> ${ChangeDateFormat(row.proposedexitmeetdate)}
                            `;
                                }
                            },

                            {
                                data: null,
                                width: "10%",
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return `
                                <button class="btn btn-sm btn-primary updateBtn"
                                    data-leaveid="${row.leaveid || ''}"
                                    data-othertransid="${row.othertransid || ''}">
                                    Share details to ERP
                                </button>
                            `;
                                }
                            }
                        ]
                    });
                }
            });
        }
    </script>

</body>
@endsection
