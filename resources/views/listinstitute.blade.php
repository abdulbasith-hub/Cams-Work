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
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


<div class="card mt-2"  style="border-color: #7198b9">
    <div class="card-header card_header_color">Audit Plan Details</div>
    <div class="card-body">
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="audit_plandetails"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        @csrf
                        <tr>
                            <th class="lang" key="s_no">S.No</th>
                            <th>Department</th>
                            <th>Category</th>

                            <th>Institute</th>
                            <th>Plan Period</th>
                            <th>Audit Team</th>
                            <th>Quarter</th>
                            <th class="all">Action</th>
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
    $(document).ready(function() {
        if ($.fn.dataTable.isDataTable('#audit_plandetails')) {
            $('#audit_plandetails').DataTable().clear().destroy();
        }
        var table = $('#audit_plandetails').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "scrollX": true,
            "ajax": {
                "url": "/audit/audit_plandetails", // Your API route for fetching data
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Pass CSRF token in headers
                },
                "dataSrc": function(json) {

                    if (json.data && json.data.length > 0) {

                        $('#tableshow').show();
                        $('#saudit_plandetails_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        return json.data;
                    } else {
                        $('#tableshow').hide();
                        $('#audit_plandetails_wrapper').hide();
                        $('#no_data').show(); // Show custom "No Data" message
                        return [];
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
                    "data": "deptesname"
                },
                {
                    "data": "catename"
                },

                {
                    "data": "instename"
                },
                {
                    "data": "typeofauditename"
                },
                {
                    "data": "teamname"
                },
                {
                    "data": "auditquarter",

                },

                {
                    "data": "encrypted_auditplanid", // Use the encrypted deptuserid
                    "render": function(data, type, row) {
                        let userid = row.userid
                        // Otherwise, show the Finalize button
                        return `<center>
                        <button class="btn btn-primary schedule_btn" id="${data}" data-userid="${userid}">
                            Schedule
                        </button>
                    </center>`;

                    }
                }
            ]
        });
    });


    $(document).on('click', '.schedule_btn', function() {
        var id = $(this).attr('id'); // Getting id of the clicked button (which is auditplanid)
        var userid = $(this).attr('data-userid');

        window.location.href = '/audit_datefixing?auditplanid=' + id + '&userid=' + userid;
        // if (id) {user
        //     $.ajax({
        //         url: '/audit_datefixing', // The route to call your controller method
        //         method: 'POST',
        //         data: {
        //             auditplanid: id // Passing the auditplanid from the button's id
        //         },
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
        //                 'content') // CSRF token for security
        //         },
        //         success: function(response) {
        //             alert(resaponse);
        //             if (response.success) {
        //                 // Handle the success case (you can redirect, update UI, etc.)
        //                 alert("Data loaded successfully.");
        //                 // You can use response data to update your UI dynamically
        //             } else {
        //                 alert("Failed to load data.");
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             // Handle error
        //             console.log("AJAX error: " + error);
        //         }
        //     });
        // }
    });
</script>
@endsection
