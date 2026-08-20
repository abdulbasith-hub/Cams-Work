<?php

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
$getapproveddetails = json_decode($getapproveddetails, true);

if ($getapproveddetails) {
    $datashow = '';
    $nodatashow = 'hide_this';
} else {
    $datashow = 'hide_this';
    $nodatashow = '';
}

?>



<div class="row">
    <div class="col-12">
        <div class="card" style="border-color: #7198b9">
            <div class="card-header card_header_color lang" key="approveddel">Approved Details</div>
            <div class="card-body"><br>
                <div class="datatables <?php echo $datashow; ?>">
                    <div class="table-responsive " id="tableshow">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang" key="userdel">User Details</th>
                                    <th class="lang" key="transactionname">Transaction Type</th>
                                    <th class="lang" key="transactiondel">Transaction Detail</th>
                                    <th class="all lang" key="approveddel">Approved Details</th>
                                    <th class="all lang" key="approveddel">View</th>
                                </tr>
                            </thead>
                            <tbody>


                                @php
                                use Carbon\Carbon;
                                @endphp

                                @foreach ($getapproveddetails as $index => $item)
                                <tr>
                                    <td class="text-end">{{ $index + 1 }}</td> <!-- S.No -->

                                    <td>
                                        <strong>Name:</strong> {{ $item['username'] }} <br>
                                        <strong>Ifhrms no:</strong> {{ $item['ifhrmsno'] ?? '-' }} <br>
                                        <strong>Dob:</strong>
                                        {{ $item['dob'] ? Carbon::parse($item['dob'])->format('d-m-Y') : '-' }} <br>
                                        <strong>DoR:</strong>
                                        {{ $item['dor'] ? Carbon::parse($item['dor'])->format('d-m-Y') : '-' }}
                                    </td>

                                    <td>{{ $item['transactiontypelname'] }}</td>

                                    <td>
                                        @if ($item['transactiontypecode'] != '01')
                                        <strong>Order Date:</strong>
                                        {{ $item['orderdate'] ? Carbon::parse($item['orderdate'])->format('d-m-Y') : '-' }}
                                        <br>
                                        <strong>Order No:</strong> {{ $item['orderno'] ?? '-' }} <br>
                                        <strong>From Details:</strong> {{ $item['fromdeptename'] ?? '-' }} -
                                        {{ $item['fromregionename'] ?? '-' }} -
                                        {{ $item['fromdistename'] ?? '-' }} <br>
                                        {{ $item['frominstename'] ?? '-' }} <br>
                                        <strong>To Details:</strong> {{ $item['todeptename'] ?? '-' }} -
                                        {{ $item['toregionename'] ?? '-' }} -
                                        {{ $item['todistename'] ?? '-' }} <br>
                                        {{ $item['toinstename'] ?? '-' }}
                                        @else
                                        <strong>From Date:</strong>
                                        {{ $item['fromdate'] ? Carbon::parse($item['fromdate'])->format('d-m-Y') : '-' }}
                                        <br>
                                        <strong>To Date:</strong>
                                        {{ $item['todate'] ? Carbon::parse($item['todate'])->format('d-m-Y') : '-' }}
                                        @endif
                                    </td>

                                    <td>
                                        {{ $item['approvedby_username'] ?? '-' }}
                                        ({{ $item['desigesname'] ?? '-' }})
                                        <br>
                                        {{ $item['chargedescription'] ?? '-' }} <br>
                                        {{ $item['updatedon'] ? Carbon::parse($item['updatedon'])->format('d-m-Y h:i:s A') : '-' }}
                                    </td>
                                    <td>

                                        <?php if (($item['schedulechange'] == 1) or ($item['planchange'] == 1)) { ?>
                                            <button type="button" class="btn btn-primary btn-sm view-btn"
                                                data-othertransid="{{ $item['id'] }}"
                                                data-transactiontypecode="{{ $item['transactiontypecode'] }}">
                                                View
                                            </button> <?php
                                                    } else {
                                                        echo 'No Change';
                                                    } ?>

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
<!-- <script src="../assets/js/vendor.min.js"></script>  -->


<!-- Modal HTML -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Data Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>


<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('.view-btn').click(function() {
            var othertransid = $(this).data('othertransid');
            var transactiontypecode = $(this).data('transactiontypecode');

            $.ajax({
                url: "{{ route('viewdatatransferdel') }}",
                type: 'GET',
                data: {
                    othertransid: othertransid,
                    transactiontypecode: transactiontypecode
                },
                success: function(response) {
                    let html = '';

                    // Table 3

                    $username = '';


                     if (response.result3.length > 0) {

                        username = response.result3[0].fromuser;

                        html += '<h5>Schedule Details</h5>';
                        html += `
                        <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr style="vertical-align: middle; text-align: center;">
                                    <th style="width: 35%;">Institution</th>
                                    <th style="width: 10%;">Data Transfer Type</th>
                                    <th style="width: 20%;">From User</th>
                                    <th style="width: 20%;">To User</th>
                                    <th style="width: 25%;">Work Allocation Done</th>
                                    <th style="width: 25%;">Slip Count</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;

                        response.result3.forEach(function(row) {
                            let transferType = "";
                            if (row.datatransfertypecode === 'CD') {
                                transferType = 'Assign Task';
                            } else if (row.datatransfertypecode === 'MH') {
                                transferType = 'Member Act as Head';
                            } else if (row.datatransfertypecode === 'WA') {
                                transferType = 'Work Allocation Distribution';
                            } else if (row.datatransfertypecode === 'AH') {
                                transferType = 'Another Team Head';
                            }else if (row.datatransfertypecode === 'HM') {
                                transferType = 'Head Can do Memeber';
                            }
                            else if (row.datatransfertypecode === 'NC') {
                                transferType = 'No Change';
                            }

                            let toUserCell = (row.datatransfertypecode === 'WA') ? '-' : row.touser;

                            html += `
                            <tr style="vertical-align: middle; text-align: center;">
                                <td style="text-align: left;">${row.instename}</td>
                                <td>${transferType}</td>
                                <td>${row.fromuser}</td>
                                 <td>${toUserCell ? toUserCell : '-'}</td>
                                <td style="text-align: left;">${row.workallocationstatus}</td>
                                <td>${row.slipcount ? row.slipcount : '0'}</td>

                            </tr>`;
                        });

                        html += '</tbody></table>';
                    }


                    // Table 2 (Grouped Data with From/To Users)
                    const data2 = response.result2 || [];

                    if (data2.length > 0) {
                        const language = getLanguage();

                        const groupCol = language === 'ta' ? 'grouptname' : 'groupename';
                        const workCol = language === 'ta' ? 'majorworkallocationtypetname' :
                            'majorworkallocationtypeename';
                        const combinedLabel = language === 'ta' ?
                            'குழு மற்றும் பணியின் வகை' : 'Group & Work Allocation';
                        const workLabel = language === 'ta' ? 'பணியின் வகை' :
                            'Work Allocation';
                        const fromUserLabel = language === 'ta' ? 'இருந்த பயனர்' :
                            'From User';
                        const toUserLabel = language === 'ta' ? 'செல்லும் பயனர்' :
                            'To User';

                        // Group the data by group name
                        const grouped = {};

                        data2.forEach(item => {
                            const group = item[groupCol] || '-';
                            const work = item[workCol] || '-';
                            const fromUser = item.fromuser || '-';
                            const toUser = item.touser || '-';

                            if (!grouped[group]) {
                                grouped[group] = {
                                    works: new Set(),
                                    fromUsers: new Set(),
                                    toUsers: new Set()
                                };
                            }

                            grouped[group].works.add(work);
                            grouped[group].fromUsers.add(fromUser);
                            grouped[group].toUsers.add(toUser);
                        });

                        // Generate table HTML
                        html += `<h5>Work Allocation</h5>`;
                        html += `
                        <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr style="vertical-align: middle; text-align: center;">
                                    <th style="width: 5%;">S.No</th>
                                    <th style="width: 25%; vertical-align: middle;">${combinedLabel}</th>
                                    <th style="width: 25%; vertical-align: middle;">${workLabel}</th>
                                    <th style="width: 22%; vertical-align: middle;">${fromUserLabel}</th>
                                    <th style="width: 23%; vertical-align: middle;">${toUserLabel}</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                        let index = 1;
                        for (const [group, sets] of Object.entries(grouped)) {
                            const worksList = Array.from(sets.works).join('<br>');
                            const fromUsersList = Array.from(sets.fromUsers).join('<br>');
                            const toUsersList = Array.from(sets.toUsers).join('<br>');

                            html += `
                            <tr style="vertical-align: middle; text-align: center;">
                                <td style="vertical-align: middle;">${index++}</td>
                                <td style="vertical-align: middle; text-align: left;">${group}</td>
                                <td style="vertical-align: middle; text-align: left;">${worksList}</td>
                                <td style="vertical-align: middle; text-align: left;">${fromUsersList}</td>
                                <td style="vertical-align: middle; text-align: left;">${toUsersList}</td>
                            </tr>
                        `;
                        }

                        html += `
                            </tbody>
                        </table>
                    `;
                    }

                    // Table 1
                    if (response.result1.length > 0) {
                        username = response.result1[0].from_user_details;
                        html +=
                            `<h5>Plan Details of ${response.result1[0].from_user_details}</h5>`;

                        html += `
                                <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr style="vertical-align: middle; text-align: center;">
                                            <th style="width: 40%;">Institution</th>`;

                        if (response.result1[0].to_user_details) {
                            html += `
                                            <th style="width: 30%;">Transfer To User</th>`;
                        } else {
                            html += `
                                            <th style="width: 60%;">Status</th>`;
                        }

                        html += `
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                        response.result1.forEach(function(row) {
                            html += `<tr style="vertical-align: middle; text-align: center;">
                    <td style="text-align: left;">${row.instename}</td>`;

                            if (row.to_user_details) {
                                html += `
                    <td style="text-align: left;">${row.to_user_details}</td>`;
                            } else {
                                html += `
                    <td colspan="2" style="text-align: left;">Vacant</td>`;
                            }

                            html += `</tr>`;
                        });

                        html += `
            </tbody>
        </table>`;
                    }

                    console.log(response.result3[0]);


                    // Populate the modal body
                    $('#viewModalBody').html(html);
                    $('#viewModalLabel').html('Data Transfer Detail of ' + username);



                    // Show the modal
                    $('#viewModal').modal('show');
                },
                error: function() {
                    $('#viewModalBody').html(
                        '<p class="text-danger">Something went wrong while loading details.</p>'
                    );
                    $('#viewModal').modal('show');
                }
            });
        });
    });
</script>
@endsection