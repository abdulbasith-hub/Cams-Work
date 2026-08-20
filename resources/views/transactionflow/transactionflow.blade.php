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

    a .title-part-padding {
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

<?php $session_detail = session('charge');

?>

<div class="card card_border mt-2">
    <div class="card-header card_header_color">Forwarded Application Details</div>
    <div class="card-body">
        <form id="transaction" name=transaction>
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="app_Details" class="table w-100 table-striped table-bordered display datatables-basic">

                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th>Details</th>
                                <th>Transaction Type</th>
                                <!-- <th>Date</th> -->
                                <th>Forwarded Details</th>
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
        </form>
    </div>
</div>
</div>

<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
{{-- data table --}}
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/js/datatable/datatable-advanced.init.js"></script>
<script>
    $(document).ready(function() {
        fetchAlldata();
    });


    function fetchAlldata() {

        if ($.fn.dataTable.isDataTable('#app_Details')) {
            $('#app_Details').DataTable().clear().destroy();
        }

        var table = $('#app_Details').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
  		"pageLength": 50, 

            "ajax": {
                "url": "/transaction/fetchall_transflowdata",
                "type": "POST",
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "dataSrc": function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#leavedetTable_wrapper').show();
                        $('#no_data').hide();
                        return json.data;
                    } else {
                        $('#tableshow').hide();
                        $('#leavedetTable_wrapper').hide();
                        $('#no_data').show();
                        return [];
                    }
                },
                "error": function(xhr, error, code) {
                    console.error("Error fetching data: ", error);
                }
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    "data": "null",
                    "render": function(data, type, row) {

                        if (row.transactiontypecode === '09') {


                            return ` <b>Schedule: </b>${row.instename}
                              
                                  `;


                        } else {

                            return ` <b>Name: </b>${row.username} (${row.desigesname})
                             <br><b>IFHRMS No :</b>${row.ifhrmsno}
                             <br><b>Designation :</b>${row.desigelname}
                                    <br><b>Charge Details:</b>${row.chargedel}
                                  `;
                        }


                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (row.transactiontypecode === '05' || row.transactiontypecode === '06' || row.transactiontypecode === '07') {
                            let inoutflag = (row.inoutstatus === 'O') ? 'Out' : 'In';
                            return `${row.transactiontypelname} (${inoutflag})`;
                        }
                        return row.transactiontypelname || '';
                    }
                },

                {
                    "data": null,
                    "render": function(data, type, row) {
                        let updatedDateTime = row.updatedon ?
                            (() => {
                                let d = new Date(row.updatedon);
                                let day = String(d.getDate()).padStart(2, '0');
                                let month = String(d.getMonth() + 1).padStart(2, '0');
                                let year = d.getFullYear();
                                let hours = String(d.getHours()).padStart(2, '0');
                                let minutes = String(d.getMinutes()).padStart(2, '0');
                                let seconds = String(d.getSeconds()).padStart(2, '0');

                                let ampm = hours >= 12 ? 'PM' : 'AM';
                                hours = hours % 12;
                                hours = hours ? hours : 12; // convert 0 to 12
                                hours = String(hours).padStart(2, '0');

                                return `${day}-${month}-${year} ${hours}:${minutes}:${seconds} ${ampm}`;
                            })() :
                            "N/A";

                        return `${row.fbdu_username} (${row.fbde_desigesname})<br>${updatedDateTime}<br>${row.processelname}`;
                    }
                },


                {
                    "data": "null",
                    "render": function(data, type, row) {
                        let html = `<center>`;

                        if (row.forwardto === null) {
                            let id;

                            if (row.transactiontypecode === '01') {
                                id = row.leaveid;
                            } else if (row.transactiontypecode === '09') {
                                id = row.mandaysextensionid;
                            } 
else if (row.transactiontypecode === '11') {
                                    id = row.leaveinid;
                                }
else {
                                id = row.othertransid;
                            }

                            html += `<button type="button" class="btn btn-outline-primary fwd_btn" trans_action="Approve" transid="${row.historytransid}" id="${id}" transtypecode="${row.transactiontypecode}" inoutstatus="${row.inoutstatus}" trans_userid="${row.trans_userid}" forwardto="${row.forwardto}"><i class="ti ti-clipboard"></i> Approve</button>`;

                            if (row.transactiontypecode === '01') {
                                html += `<button type="button" class="reject_btn btn btn-outline-danger" data='${JSON.stringify(row)}' id="${row.leaveid}" transtypecode="${row.transactiontypecode}" trans_userid="${row.trans_userid}"><i class="ti ti-copy"></i> Reject</button><br>`;
                            }
                        } else if (row.processcode === 'A') {
                            html += `<button class="btn btn-primary finalize_btn" id="${data}">Accepted</button>`;
                        } else if (row.processcode === 'X') {
                            html += `<button class="btn btn-warning finalize_btn" id="${data}">Rejected</button>`;
                        }
                        html += `</center>`;
                        return html;
                    }
                }
            ]
        });

    }



    $(document).on('click', '.reject_btn', function() {
        const rowData = $(this).attr('data');
        const data = JSON.parse(rowData);
        const id = data.leaveid;
        const transid = data.historytransid;
        const remarks = $('#remarks_' + transid).val();
        const transtype = $(this).attr('transtypecode');
        const userid = $(this).attr('trans_userid');

        if (id) {
            const confirmation = 'Are you sure to reject the leave application?';
            // Always unbind old handlers to avoid multiple AJAX calls
            $('#process_button').off('click').on('click', function() {
                $('#confirmation_alert').modal('hide');
                reject_application(id, transtype, userid);
            });

            // Show confirmation modal
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

    function reject_application(leaveid, transtype, userid) {
        if (leaveid && transtype && userid) {
            $.ajax({
                url: 'transaction/reject_application',
                method: 'POST',
                data: {
                    leaveid: leaveid,
                    transtype: transtype,
                    userid: userid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Rebind OK button inside modal to refresh data
                        $('#ok_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            $('#confirmation_alert').modal('hide');
                            fetchAlldata();
                        });

                        passing_alert_value(
                            'Confirmation',
                            response.message,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    } else {
                        passing_alert_value(
                            'Alert',
                            response.message,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);

                    let message = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    passing_alert_value(
                        'Alert',
                        message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
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



    $(document).on('click', '.fwd_btn', function() {

        var id = $(this).attr('id'); //Getting id of user clicked edit button.
        var action = $(this).attr('trans_action');
        var transid = $(this).attr('transid');
        var roleactioncode = $(this).attr('roleactioncode');
        var remarks = $('#' + 'remarks_' + transid).val();

        var inoutstatus = $(this).attr('inoutstatus');
        var transtype = $(this).attr('transtypecode');
        var userid = $(this).attr('trans_userid');
        var forwardto = $(this).attr('forwardto');

        // window.location.href = '/datatransfer?id=' + encodeURIComponent(id) + '&transtype=' +
        //     encodeURIComponent(transtype) + '&inoutstatus=' + encodeURIComponent(inoutstatus) + '&userid=' +
        //     encodeURIComponent(userid) + '&forwardto=' +
        //     encodeURIComponent(forwardto);


        if (transtype == '09') {

            window.location.href = '/schedulerequestdata?id=' + encodeURIComponent(id) + '&transtype=' +
                encodeURIComponent(transtype) + '&inoutstatus=' + encodeURIComponent(inoutstatus) + '&userid=' +
                encodeURIComponent(userid) + '&forwardto=' +
                encodeURIComponent(forwardto);

        }else if (transtype == '11') {

                window.location.href = '/leaveindata?id=' + encodeURIComponent(id) + '&transtype=' +
                    encodeURIComponent(transtype) + '&inoutstatus=' + encodeURIComponent(inoutstatus) + '&userid=' +
                    encodeURIComponent(userid) + '&forwardto=' +
                    encodeURIComponent(forwardto);

            } else {

            window.location.href = '/datatransfer?id=' + encodeURIComponent(id) + '&transtype=' +
                encodeURIComponent(transtype) + '&inoutstatus=' + encodeURIComponent(inoutstatus) + '&userid=' +
                encodeURIComponent(userid) + '&forwardto=' +
                encodeURIComponent(forwardto);
        }


    });

    function getPendingRecordsforuser(transid) {
        $.ajax({
            url: 'transaction/getPendingRecordsforuser', // Your API route to get user details
            method: 'POST',
            data: {
                transid: transid,
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {
                    // $('#display_error').hide();
                    // change_button_as_update('leave_form', 'action', 'buttonaction',
                    //     'display_error', '', '');
                    // validator.resetForm();
                    passing_alert_value('Confirmation', response.success,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    fetchAlldata();
                    // const teamdet = response.data[0]; // The array of schedule data
                    // var teamhead_userid = teamdet.userid;
                    // var teamhead_userchargeid = teamdet.userchargeid;

                    // if (teamdet) {

                    //     forward_application(teamhead_userid, teamhead_userchargeid, leaveid);
                    // }


                } else {
                    alert(' Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }








    function get_frwdDetail(leaveid, transid, remarks, roleactioncode, action, transtype) {

        var leaveid = leaveid;
        var roleactioncode = roleactioncode;
        var remarks = remarks;
        var action = action;
        var transtype = transtype;
        $.ajax({
            url: 'transaction/get_frwduserDetail', // Your API route to get user details
            method: 'POST',
            data: {
                leaveid: leaveid,
                transid: transid,
                remarks: remarks,
                roleactioncode: roleactioncode,
                action: action
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {


                    const frwd_userdet = response.data; // The array of schedule data

                    // if (roleactioncode === 'A') {
                    //     var userid = frwd_userdet.userid;
                    // } else {
                    //     var userid = frwd_userdet.deptuserid;
                    // }
                    var userid = frwd_userdet.userid;
                    var userchargeid = frwd_userdet.userchargeid;

                    if (frwd_userdet) {

                        forward_application(userid, userchargeid, leaveid, roleactioncode, remarks,
                            action, transtype);
                    }


                } else {
                    alert(' Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function forward_application(userid, userchargeid, leaveid, roleactioncode, remarks, action, transtype) {

        $.ajax({
            url: '/transaction/forward_application', // Your API route to get user details
            method: 'POST',
            data: {
                userid: userid,
                userchargeid: userchargeid,
                id: leaveid,
                roleactioncode: roleactioncode,
                remarks: remarks,
                action: action,
                transactiontypecode: transtype

            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {


                    passing_alert_value('Confirmation', response.success,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');

                    fetchAlldata();

                } else {
                    alert(' Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
</script>
@endsection