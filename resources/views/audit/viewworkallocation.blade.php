@section('content')
@extends('index2')
@include('common.alert')
<style>
    .card_seperator {
        height: 10px;
        border: 0;
        box-shadow: 0 10px 10px -10px #8c8b8b inset;
    }

    .card-title {
        font-size: 15px;
    }

    .title-part-padding {
        background-color: #e3efff;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .dataTables_info {
        margin-bottom: 1rem !important;
    }

    #schedule_allocatedwork_wrapper {
        width: 100%;
        /* Allow full-width table */
        overflow-x: auto;
        /* Enable horizontal scrolling */
    }

    .dataTables_wrapper {
        overflow: hidden !important;
        /* Prevents extra scrolling */
        height: auto !important;
        /* Allows full display */
    }

    table.dataTable {
        width: 100% !important;
        table-layout: auto !important;
    }
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


<div class="card card_border">
    <div class="card-header card_header_color lang" key="allocated_work_det">Allocated Work Details</div>
    <div class="card-body">
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="schedule_allocatedwork"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        @csrf
                        <tr>
                            <th class="lang" key="s_no">S.No</th>
                            <th class="text-wrap lang" key="inst_title">Institute</th>
                            <th class="text-wrap lang" key="group_head">Group</th>
                            <th class="text-wrap lang" key="name">Name</th>

                            <th class="text-wrap lang" key="worktypes">Allocated Work</th>

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
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>


<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

{{-- data table --}}
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<script src="../assets/js/datatable/datatable-advanced.init.js"></script>
<script>
    $('#translate').change(function() {
        const lang = getLanguage('Y'); // Store language selection
        updateTableLanguage(
            lang); // Update the table with the new language by destroying and recreating it

    });
    let dataFromServer;
    $(document).ready(function() {
        const lang = getLanguage('')
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // if ($.fn.dataTable.isDataTable('#schedule_allocatedwork')) {
        //     $('#schedule_allocatedwork').DataTable().clear().destroy();
        // }
        const urlParams = new URLSearchParams(window.location.search);
        const scheduleid = urlParams.get('audit_scheduleid');

        $.ajax({
            url: "/audit/fetch_allocatedwork", // Your API route for fetching data
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            data: {
                scheduleid: scheduleid, // Add the scheduleid to the AJAX request
            },
            success: function(json) {
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#sschedule_allocatedwork_wrapper').show();
                    $('#no_data').hide(); // Hide custom "No Data" message
                    dataFromServer = json.data;

                    renderTable(lang, dataFromServer);
                } else {
                    $('#tableshow').hide();
                    $('#sschedule_allocatedwork_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function(xhr, status, error) {


                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {
                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.error ||
                        'An unknown error occurred';

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');


                }
            }



        });


        // updatedatatable(lang, "schedule_allocatedwork");

    });


    function renderTable(lang, datafromServer) {
        let worktypes = `${lang === 'ta' ?"worktypes_ta" : "worktypes_en"}`;
        let instname = `${lang === 'ta' ?"insttname" : "instename"}`;
        let groupname = `${lang === 'ta' ?"grouptname" : "groupename"}`;


        if ($.fn.dataTable.isDataTable('#schedule_allocatedwork')) {
            $('#schedule_allocatedwork').DataTable().clear().destroy();
        }
        var table = $('#schedule_allocatedwork').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,
            // "scrollY": false,
            //"autoWidth": false,
            "initComplete": function(settings, json) {
                $("#schedule_allocatedwork").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "data": datafromServer,
            columns: [

                {
                    data: "index",
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end'

                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        let instname = `${lang === 'ta' ?row.insttname : row.instename}`;

                        return instname || `- `;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        let groupname = `${lang === 'ta' ?row.grouptname : row.groupename}`;
                        return groupname || `-`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        let designame =
                            `${lang === 'ta' ? row.desigtlname : row.desigelname}`;
                        let name =
                            `${lang === 'ta' ? row.usertamilname : row.username}`;
                        return `${name} ( ${designame})`;
                    },
                    className: 'text-start text-wrap'
                },
                {
                    data: "null",
                    render: function(data, type, row, ) {
                        let worktypes =
                            `${lang === 'ta' ? row.worktypes_ta : row.worktypes_en}`;

                        return worktypes || '-';
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap"
                },
            ],


        });
        const mobileColumns = [instname, groupname, worktypes];
        setupMobileRowToggle(mobileColumns);

    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#mappallocationobj_table')) {
            $('#mappallocationobj_table').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }
</script>
@endsection