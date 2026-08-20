@extends('index2')
@section('content')
@section('title', 'Dashboard')

@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

    $sessionroletypecode = $sessionchargedel->roletypecode;
    $showSection = $sessionroletypecode == view()->shared('Dist_roletypecode');
@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<style>
    /* Ensure search box does not cause horizontal scrolling */
    #sentdetails_wrapper .dataTables_filter {
        overflow: visible !important;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color">Sent Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive" id="tableshow">
                        <table id="sentdetails"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th class="text-center">Slip Number <br>(Audit Office)</th>
                                    <th class="text-center text-wrap">Slip Details</th>
                                    <th class="text-center">Forwarded By / On</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sentDetails as $index => $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->mainslipnumber }} <br>{{ $detail->instename }}</td>
                                        <td class="text-wrap">
                                            <b>Amount Invloved :</b> {{ $detail->amtinvolved }}<br>
                                            <b>Objection Name:</b> {{ $detail->objectionename }}<br>
                                            <b>SubObjection Name:</b> {{ $detail->subobjectionename }}<br>
                                            <b>Severity:</b> {{ $detail->severityelname }}<br>
                                            @if ($detail->liability === 'Y')
                                                <b>Liability:</b> {{ $detail->liability }} (<b>Name :
                                                </b>{{ $detail->liability }} <b>GPF / CPF No :
                                                </b>{{ $detail->liability }} <b>Designation :
                                                </b>{{ $detail->liability }})<br>
                                            @endif
                                            <b>Slip Details:</b> {{ $detail->slipdetails }}
                                        </td>
                                        <td>{{ $detail->forwardedtouser }}<br>{{ $detail->forwardedon }}</td>
                                        <td>{{ $detail->processelname }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (count($sentDetails) === 0)
                    <div id='no_data'>
                        <center>No Data Available</center>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../common/ajaxfn.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.dataTable.isDataTable('#sentdetails')) {
            $('#sentdetails').DataTable().clear().destroy();
        }

        var table = $('#sentdetails').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,  // Only horizontal scrolling enabled
            // "scrollCollapse": true, // Collapse table when no scroll is needed
            // "autoWidth": false, // Disable automatic column width adjustment
        });
    });
</script>

@endsection
