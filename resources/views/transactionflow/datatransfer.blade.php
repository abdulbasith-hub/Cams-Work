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

    .modal.fullscreen-modal .modal-dialog {
        max-width: 100%;
        height: 100%;
        margin: 0;
    }

    .modal.fullscreen-modal .modal-content {
        height: 100%;
        border: none;
        border-radius: 0;
    }
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<?php
$othertransdet = json_decode($othertrans, true);
$i = 1;


$auditplanIds = [];

if ($othertransdet[0]['inoutstatus'] == 'O') {
    $schedulependings = $pendingdel['schedulependings'];
    $planpendings = $pendingdel['planpenings'];
    if (!$planpendings->isEmpty()) {
        $auditplanIds = $planpendings->pluck('auditplanid')->implode(',');
    }
} else {
    $schedulependings = collect();
    $planpendings = collect();
}

?>
<div class="card mb-3 card_border" style="max-width: 100%; font-size: 14px;">
    <div class="card-header card_header_color ">
        User & Transaction Details
    </div>
    <div class="card-body py-2">
        <div class="row">
            <!-- Left: User Details -->
            <div class="col-md-6 border-end">
                <p class="mb-1"><strong>User Name:</strong> <?php echo $othertransdet[0]['username']; ?></p>
                <p class="mb-1"><strong>IFHRMS No:</strong> <?php echo $othertransdet[0]['ifhrmsno']; ?></p>
                <p class="mb-1"><strong>DOB:</strong> <?php echo date('d-m-Y', strtotime($othertransdet[0]['dob'])); ?></p>
                <p class="mb-1"><strong>DOR:</strong> <?php echo date('d-m-Y', strtotime($othertransdet[0]['dor'])); ?></p>
                <p class="mb-1"><strong>Charge Details:</strong><br><?php echo $othertransdet[0]['chargedel']; ?></p>

            </div>

            <!-- Right: Transaction Details -->
            <?php
$orderdate = '';
$today = '';
$fromdate = '';

            $transactionType = $othertransdet[0]['transactiontypelname'];
             $transactionTypecode = $othertransdet[0]['transactiontypecode'];
            $inoutstatus = $othertransdet[0]['inoutstatus'];

            if (in_array($othertransdet[0]['transactiontypecode'], ['05', '06', '07'])) {
                $inoutflag = $inoutstatus === 'O' ? 'Out' : 'In';
                $transactionType .= " ($inoutflag)";
            }
            ?>
            <div class="col-md-6">
                <p class="mb-1"><strong>Transaction Name:</strong> <?php echo $transactionType; ?></p>

                <?php if ($othertransdet[0]['transactiontypecode'] != '01') {

                    $orderdate = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime($othertransdet[0]['orderdate'])));
        $today = DateTime::createFromFormat('d-m-Y', date('d-m-Y'));





                ?>


                    <p class="mb-1"><strong>Order Date:</strong> <?php echo date('d-m-Y', strtotime($othertransdet[0]['orderdate'])); ?></p>
                    <p class="mb-1"><strong>Order No:</strong> <?php echo $othertransdet[0]['orderno']; ?></p><?php

                                                                                                                if ((($othertransdet[0]['transactiontypecode'] === $diversionTransactiontypecode) || ($othertransdet[0]['transactiontypecode'] === $transfercode)) && ($inoutstatus == 'O')) {
                                                                                                                    $todetails = $othertransdet[0]['div_deptesname'] . '-' . $othertransdet[0]['to_regionename'] . '-' . $othertransdet[0]['to_distename'] . '-' . $othertransdet[0]['instename'] ?>
                        <strong>To Details :</strong> <?php echo $todetails;
                                                                                                                } else if ((($othertransdet[0]['transactiontypecode'] === $diversionTransactiontypecode) || ($othertransdet[0]['transactiontypecode'] === $transfercode) && ($inoutstatus == 'I'))) {
                                                                                                                    $todetails = $othertransdet[0]['deptesname'] . '-' . $othertransdet[0]['regionename'] . '-' . $othertransdet[0]['distename'] . '-' . $othertransdet[0]['instename'] ?>
                        <strong>From Details :</strong> <?php echo $todetails;
                                                                                                                }
                                                                                                                // Split file name and URL
                                                                                                                $fileInfo = explode('-', $othertransdet[0]['filedetails']);
                                                                                                                // print_r($fileInfo);

                                                                                                                $filename = $fileInfo[0] ?? 'Download File';
                                                                                                                $fileurl = $fileInfo[1] ?? '#';
                                                        ?>
                    <p class="mb-1"><strong>File Info:</strong>
                        <a href="<?php echo htmlspecialchars($fileurl); ?>" style="color:blue;" target="_blank" rel="noopener noreferrer">
                        <?php echo htmlspecialchars($filename);
                    } else { 
if (!empty($othertransdet[0]['fromdate'])) {
            $dateObj = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime($othertransdet[0]['fromdate'])));
            if ($dateObj !== false) {
                $fromdate = $dateObj->format('d-m-Y'); // now it's a string, not an object
            }
        } ?>                            <p class="mb-1"><strong>From Date:</strong> <?php echo date('d-m-Y', strtotime($othertransdet[0]['fromdate'])); ?></p>
                            <p class="mb-1"><strong>To Date:</strong> <?php echo date('d-m-Y', strtotime($othertransdet[0]['todate'])); ?></p>
                            <p class="mb-1"><strong>Reason:</strong> <?php echo $othertransdet[0]['reason'] ?></p>
                        <?php
                    } ?>

                        </a>

                    </p>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">
                Data Transfer
            </div>
            <div class="card-body collapse show">
                <form id="othertrans_form" name="othertrans_form">

                    <?php if ($othertransdet[0]['transactiontypecode'] != '01') { ?>
                        <input type="hidden" id="othertransid" name="othertransid"
                            value="<?php echo $othertrans[0]->othertransid; ?>"><?php
                                                                            } else { ?>
                        <input type="hidden" id="othertransid" name="othertransid"
                            value="<?php echo $othertrans[0]->leaveid; ?>"><?php


                                                                            } ?> <?php if (!$planpendings->isEmpty()) {
                                                                                        foreach ($planpendings->pluck('auditplanid') as $id): ?>
                            <input type="hidden" name="auditplanid[]" value="<?php echo $id; ?>">
                    <?php endforeach;
                                                                                    } ?>

                    {{-- <input type="hidden" id="auditplanid" name="auditplanid" value="<?php echo $auditplanIds; ?>"> --}}
                    <input type="hidden" id="inoutstatus" name="inoutstatus" value="<?php echo $othertrans[0]->inoutstatus; ?>">
                    <input type="hidden" id="userid" name="userid" value="<?php echo $othertrans[0]->userid; ?>">

                    <input type="hidden" id="transactiontypecode" name="transactiontypecode"
                        value="<?php echo $othertrans[0]->transactiontypecode; ?>">

                    @csrf

                    <!-- <div class="card">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="card-body card-border">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="container-fluid mt-4"> -->
                    <!-- Schedule Pendings -->
                    <?php if ($othertransdet[0]['inoutstatus'] == 'O') {
                        if (!$schedulependings->isEmpty()) {
                            $i = 1; ?>
                            <div class="card mb-4 card_border">
                                <div class="card-header card_header_color lang" key="">
                                    Pending Schedule
                                </div>
                                <div class="card-body p-0">
                                    <!-- <h5 class="p-3 mb-0">Schedule Pendings</h5> -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 text-center align-middle">
                                            <colgroup>
                                                <col style="width: 5%;">
                                                <col style="width: 20%;">
                                                <col style="width: 7%;">
                                                <col style="width: 5%;">
                                                <col style="width: 7%;">
                                                <col style="width: 7%;">
                                                <col style="width: 20%;">
                                                <col style="width: 15%;">
                                            </colgroup>
                                            <thead>
                                                <tr style="vertical-align:middle">
                                                    <th>S.No</th>
                                                    <th>Institute Name</th>
                                                    <th>Work</th>
                                                    <th>Entry Date</th>
                                                    <th>Exit Date</th>
                                                    <th>Slip Count</th>
                                                    <th>Select</th>
                                                    <th>To User</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($schedulependings as $item) { ?>
                                                    <tr>
                                                        <td><?= $i ?></td>
                                                        <input type="hidden" class="auditscheduleid" name="auditscheduleid[]"
                                                            value="<?php echo $item->auditscheduleid; ?>">
                                                        <td>
                                                            <span class="badge bg-success text-light px-2 py-1"
                                                                style="cursor: pointer;"
                                                                onclick="institutiondel(<?= $item->auditscheduleid ?>, event)">
                                                                <?php echo htmlspecialchars($item->instename); ?>
                                                            </span>
                                                        </td>

                                                        <td>
                                                            <?php if ($item->workallocationflag == 'Y') { ?>
                                                                <span class="badge bg-success text-light px-2 py-1"
                                                                    style="cursor: pointer;"
                                                                    onclick="getworkallocationdel(<?= $item->auditscheduleid ?>, <?= $item->schteammemberid ?>, event)">
                                                                    View Work
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">No Work</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?= $item->entrymeetdate ? \Carbon\Carbon::parse($item->entrymeetdate)->format('d-m-Y') : '-' ?>
                                                        </td>
                                                        <td>
                                                            <?= $item->exitmeetdate ? \Carbon\Carbon::parse($item->exitmeetdate)->format('d-m-Y') : '-' ?>
                                                        </td>


                                                        <td>
                                                            <?php if ($item->slipcount > 0) { ?>
                                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                                    onclick="getslipallocationdel(<?= $item->auditscheduleid ?>, <?= $item->schteammemberid ?>, event)">
                                                                    <?= $item->slipcount ?>
                                                                </button>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">No Slip</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm datatransfercode"
                                                                name="datatransfercode[]">
                                                                <option value="">---Select---</option>
                                                                <option value="CD">Assign Task</option>
<option value="NC">No Change</option>
<?php

                                                                                                        if (($item->auditteamhead == 'Y')) { ?>
                                                                    <option value="MH">Member Act as Head</option>
                                                                    <option value="AH">Another Team head</option>
                                                                    <?php

                                                                                                        } else if (($item->auditteamhead == 'N')) {
                                                                                                            if ($item->workallocationflag == 'Y') {
                                                                                                                if ($item->membercount > 1) { ?>
                                                                            <option value="WA">Distribute Task</option><?php
                                                                                                                    } else { ?>
                                                                            <option value="WA">Head Do Member Work</option>
                                                                <?php
                                                                                                                    }
                                                                                                                }
                                                                                                            } ?>




                                                            </select>

                                                        </td>
                                                        <td>
                                                            <div class="touser-dropdown hide_this">
                                                                <select class="form-select form-select-sm transuser"
                                                                    name="transuser[]">
                                                                    <option value="">---Select User---</option>
                                                                    @foreach ($touserdata as $transuser)
                                                                    <option value="{{ $transuser->deptuserid }}">
                                                                        {{ $transuser->username . ' (' . $transuser->desigesname . ') (' . $transuser->distename . ')' }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>



                                                            <div class="toteamhead-dropdown hide_this ">
                                                                <select name="otherteamhead[]" class="form-select otherteamhead">
                                                                    <option value="">--- Select User ---</option>
                                                                    @foreach ($otherteamhead as $transuser)
                                                                    <option value="{{ $transuser->userid }}">
                                                                        {{ $transuser->username . ' (' . $transuser->desigesname . ')' }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>

                                                            </div>


                                                            <div class="othermembers-dropdown hide_this">
                                                                <select class="form-select form-select-sm othermembers"
                                                                    name="othermembers[]">
                                                                    <option value="">---Select User---</option>
                                                                    @foreach ($othermembers[$item->auditscheduleid] as $transuser)
                                                                    <option value="{{ $transuser->userid }}">
                                                                        {{ $transuser->username . ' (' . $transuser->desigesname . ')' }}
                                                                    </option>
                                                                    @endforeach

                                                                </select>
                                                            </div>


                                                        </td>
                                                    </tr>
                                                <?php $i++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } elseif (($othertransdet[0]['transactiontypecode'] == '01' && $schedulependings->isEmpty()) || (($othertransdet[0]['transactiontypecode'] != '01') && (($planpendings->isEmpty()) && ($schedulependings->isEmpty())))) { ?>

                            <div class="col-md-3 mb-1 mt-2">
                                <label class="form-label lang required" key="" for="validationDefault01">Data Change
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="datatransfercode"
                                    name="datatransfercode" onchange="onchange_datatransfer('')">
                                    <option value=""
                                        data-name-en="---Select Data
                                                 Change---"
                                        data-name-ta="---???? ??????????? ?????????????????--">---Select Data
                                        Change---
                                    </option>
                                    <option value="NC">No Change

                                    </option>


                                </select>
                            </div>

                        <?php
                        } ?>

                        <!-- Plan Pendings -->
                        <?php if ($othertransdet[0]['transactiontypecode'] != '01' && (!$planpendings->isEmpty())) { ?>
                            <div class="card card_border">
                                <div class="card-header card_header_color lang" key="">
                                    Plan Pendings
                                </div>
                                <div class="card-body p-0">
                                    <!-- <h5 class="p-3 mb-0">Plan Pendings</h5> -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 text-center align-middle">
                                            <colgroup>
                                                <col style="width: 5%;">
                                                <col style="width: 30%;">
                                                <col style="width: 30%;">
                                                <col style="width: 35%;">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Plan Name</th>
                                                    <th>Select</th>
                                                    <th>To User</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?= $i ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" id="pendingpopup">View Pending
                                                            Plan Details</button>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm plandatatransfercode"
                                                            name="plandatatransfercode[]">
                                                            <option value="">---Select---</option>
                                                            <option value="CD">Assign Task</option>
                                                            <!-- <option value="MV">Make Vacant</option> -->
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="touser-dropdown hide_this">
                                                            <select class="form-select form-select-sm plantransuser"
                                                                name="plantransuser[]">
                                                                <option value="">---Select User---</option>
                                                                @foreach ($touserdata as $transuser)
                                                                <option value="{{ $transuser->deptuserid }}">
                                                                    {{ $transuser->username . ' (' . $transuser->desigesname . ')' . ' (' . $transuser->distename . ')' }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else if ($othertransdet[0]['inoutstatus'] == 'I') { ?>

                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="" for="validationDefault01">Data Change
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="datatransfercode"
                                name="datatransfercode" onchange="onchange_datatransfer('')">
                                <option value=""
                                    data-name-en="---Select Data
                                                 Change---"
                                    data-name-ta="---???? ??????????? ?????????????????--">---Select Data
                                    Change---
                                </option>
                                <option value="MUI">Make user In

                                </option>


                            </select>
                        </div><?php

                            } ?>

            </div>

            <!-- </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div> -->
            <div class="row justify-content-center">
                <div class="col-md-2 mx-auto"><?php

//if ((($orderdate <= $today) && ($transactionTypecode !='01' ) && ($inoutstatus=='O' ) )|| ($transactionTypecode=='01' ) || ($inoutstatus=='I' )) { ?>
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mb-3" type="submit" action="insert" id="buttonaction"
                            name="buttonaction">Submit</button>
                    <?php
                                              //  } ?>
                    <button type="button" class="btn btn-danger mb-3" id="reset_button">Clear</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>




<?php if ($othertransdet[0]['inoutstatus'] == 'O') { ?>

    <div class="modal bd-example-modal-lg" id="exampleModalFullscreen" tabindex="-1"
        aria-labelledby="exampleModalFullscreenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="text-white ">
                <div class="modal-header" style="background-color:#007bff;justify-content: center; text-white">
                    <h3 id="exampleModalFullscreenLabel" class="text-white lang" style="text-align:center;">Pending
                        Details</h3>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php if (($othertransdet[0]['transactiontypecode'] != '01') && (!$planpendings->isEmpty())) {
                        $i = 1; ?>
                        <h5>Plan Pendings</h5>
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-striped">
                                <thead class="">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Institute Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planpendings as $item) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $item->instename ?></td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div><?php
        }

            ?>




<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
{{-- data table --}}
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/js/datatable/datatable-advanced.init.js"></script>

<!-- <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script> -->
<script>
    $(document).ready(function() {

        // Show/Hide To User based on Data Transfer selection
        $(document).on('change', 'select[name="datatransfercode[]"]', function() {
            const value = $(this).val();
            const row = $(this).closest('tr');
            const rowIndex = row.index()

            if (value === "CD") {
                row.find('.touser-dropdown').removeClass('hide_this');
                row.find('.othermembers-dropdown').addClass('hide_this');
                row.find('.toteamhead-dropdown').addClass('hide_this');
            } else if (value === "AH") {
                row.find('.toteamhead-dropdown').removeClass('hide_this');
                row.find('.touser-dropdown').addClass('hide_this');
                row.find('.othermembers-dropdown').addClass('hide_this');
            } else if (value === "MH") {
                row.find('.othermembers-dropdown').removeClass('hide_this');
                row.find('.touser-dropdown').addClass('hide_this');
                row.find('.toteamhead-dropdown').addClass('hide_this');
            } else {
                row.find('.touser-dropdown').addClass('hide_this');
                row.find('select[name="transuser[]"]').val('').removeClass('is-invalid');
                row.find('.text-danger').remove();
            }

            // Remove error on valid selection
            if (value) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.text-danger').remove();
            }
        });

        // Remove error when "To User" is selected
        $(document).on('change', 'select[name="transuser[]"]', function() {
            const value = $(this).val();
            if (value) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.text-danger').remove();
            }
        });

        // Show/Hide To User based on Data Transfer selection
        $(document).on('change', 'select[name="plandatatransfercode[]"]', function() {
            const value = $(this).val();
            const row = $(this).closest('tr');

            if (value === "CD") {
                row.find('.touser-dropdown').removeClass('hide_this');
            } else if (value === "AH") {
                row.find('.toteamhead-dropdown').removeClass('hide_this');
            } else {
                row.find('.touser-dropdown').addClass('hide_this');
                row.find('select[name="plantransuser[]"]').val('').removeClass('is-invalid');
                row.find('.text-danger').remove();
            }

            // Remove error on valid selection
            if (value) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.text-danger').remove();
            }
        });

        // Remove error when "To User" is selected
        $(document).on('change', 'select[name="plantransuser[]"]', function() {
            const value = $(this).val();
            if (value) {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.text-danger').remove();
            }
        });


        // Form submit validation
        $('#othertrans_form').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            // Validate datatransfercode[]
            $('select[name="datatransfercode[]"]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    if (!$(this).parent().find('.text-danger').length) {
                        $(this).parent().append(
                            '<div class="text-danger mt-1">Please select a data change option.</div>'
                        );
                    }
                    isValid = false; // ? ADD THIS
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).parent().find('.text-danger').remove();
                }
            });

            // Validate transuser[] only if visible
            $('select[name="datatransfercode[]"]').each(function() {
                const row = $(this).closest('tr');
                const toUserSelect = row.find('select[name="transuser[]"]');

                const tootherteamhead = row.find('select[name="otherteamhead[]"]');
                const toothermembers = row.find('select[name="othermembers[]"]');

                if ($(this).val() === "CD" && toUserSelect.is(':visible') && !toUserSelect
                    .val()) {
                    toUserSelect.addClass('is-invalid');
                    if (!toUserSelect.parent().find('.text-danger').length) {
                        toUserSelect.parent().append(
                            '<div class="text-danger mt-1">Please select a user for Distribute Task.</div>'
                        );
                    }
                    isValid = false; // ? ADD THIS TOO
                } else {
                    toUserSelect.removeClass('is-invalid');
                    toUserSelect.parent().find('.text-danger').remove();
                }
                if ($(this).val() === "AH" && tootherteamhead.is(':visible') && !tootherteamhead
                    .val()) {
                    tootherteamhead.addClass('is-invalid');
                    if (!tootherteamhead.parent().find('.text-danger').length) {
                        tootherteamhead.parent().append(
                            '<div class="text-danger mt-1">Please select a user for Distribute Task.</div>'
                        );
                    }
                    isValid = false;
                } else {
                    tootherteamhead.removeClass('is-invalid');
                    tootherteamhead.parent().find('.text-danger').remove();
                }

                if ($(this).val() === "MH" && toothermembers.is(':visible') && !toothermembers
                    .val()) {
                    toothermembers.addClass('is-invalid');
                    if (!toothermembers.parent().find('.text-danger').length) {
                        toothermembers.parent().append(
                            '<div class="text-danger mt-1">Please select a user for Distribute Task.</div>'
                        );
                    }
                    isValid = false;
                } else {
                    toothermembers.removeClass('is-invalid');
                    toothermembers.parent().find('.text-danger').remove();
                }
            });

            // Validate datatransfercode[]
            $('select[name="plandatatransfercode[]"]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    if (!$(this).parent().find('.text-danger').length) {
                        $(this).parent().append(
                            '<div class="text-danger mt-1">Please select a data change option.</div>'
                        );
                    }
                    isValid = false; // ? ADD THIS
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).parent().find('.text-danger').remove();
                }
            });

            // Validate transuser[] only if visible
            $('select[name="plandatatransfercode[]"]').each(function() {
                const row = $(this).closest('tr');
                const toUserSelect = row.find('select[name="plantransuser[]"]');

                if ($(this).val() === "CD" && toUserSelect.is(':visible') && !toUserSelect
                    .val()) {
                    toUserSelect.addClass('is-invalid');
                    if (!toUserSelect.parent().find('.text-danger').length) {
                        toUserSelect.parent().append(
                            '<div class="text-danger mt-1">Please select a user for Distribute Task.</div>'
                        );
                    }
                    isValid = false; // ? ADD THIS TOO
                } else {
                    toUserSelect.removeClass('is-invalid');
                    toUserSelect.parent().find('.text-danger').remove();
                }
            });

            if (('<?php echo $othertrans[0]->inoutstatus; ?>' == 'I') || ('<?php echo $othertransdet[0]['transactiontypecode']; ?>' == '01' &&
                    '<?php echo $schedulependings->isEmpty(); ?>')) {
                if ($('#datatransfercode').val() == '') {
                    isValid = false;
                    passing_alert_value('Alert', 'Select the Data Change', 'confirmation_alert',
                        'alert_header',
                        'alert_body', 'confirmation_alert');
                }



            }




            if (isValid) {
                $('#process_button').off('click').on('click', function(event) {
                    // Prevent the default behavior if necessary (optional)
                    $('#confirmation_alert').modal('hide');
                    insert_datatransfer();

                });

                passing_alert_value('Confirmation', 'Are you sure to submit the details',
                    'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
            }



            function insert_datatransfer() {

                var formData = $('#othertrans_form').serializeArray();
if ('<?php echo $fromdate ?>' != '') {
formData.push({
name: 'fromdate',
value: '<?php echo $fromdate ?>'
});
}
                let selectedUserIds = [];
                $('tr').each(function() {
                    const row = $(this);
                    const transferCode = row.find('select[name="datatransfercode[]"]').val();

                    if (transferCode) {
                        // transferCodes.push(transferCode);

                        let selectedId = null;
                        if (transferCode === 'CD') {
                            selectedId = row.find('select[name="transuser[]"]').val();
                        } else if (transferCode === 'AH') {
                            selectedId = row.find('select[name="otherteamhead[]"]').val();
                        } else if (transferCode === 'MH') {
                            selectedId = row.find('select[name="othermembers[]"]').val();
                        }

                        selectedUserIds.push(selectedId ||
                            null); // Push null if nothing selected
                    }
                });


                selectedUserIds.forEach(function(id) {
                    formData.push({
                        name: 'selectedUserIds[]',
                        value: id
                    });
                });

                $.ajax({
                    url: '/insert_datatransfer',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {

                            $('#close_button').hide();

                            $('#ok_button').off('click').on('click', function(event) {
                                // Prevent the default behavior if necessary (optional)
                                window.location.href = '/transactionflow';
                            });

                            passing_alert_value('Confirmation', response.message,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');




                        } else if (response.error) {

                            passing_alert_value('Alert', response.message,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');

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



                    }
                });
            }

        });
    });









    // $(document).on('change', 'select[name="datatransfercode[]"]', function() {
    //     const value = $(this).val();
    //     const row = $(this).closest('tr');

    //     if (value === "CD") {
    //         row.find('.touser-dropdown').removeClass('hide_this');
    //     } else if (value === "AH") {
    //         row.find('.toteamhead-dropdown').removeClass('hide_this');
    //     } else {
    //         row.find('.touser-dropdown').addClass('hide_this');
    //         row.find('select[name="transuser[]"]').val('').removeClass('is-invalid');
    //         row.find('.text-danger').remove();
    //     }

    //     // Remove error on valid selection
    //     if (value) {
    //         $(this).removeClass('is-invalid');
    //         $(this).parent().find('.text-danger').remove();
    //     }
    // });



    function institutiondel(auditscheduleid, event) {
        if (event) event.preventDefault(); //
        $.ajax({
            url: '/getinstitutiondel',
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {


                function formatDate(inputDate) {
                    if (inputDate) {
                        const date = new Date(inputDate);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                        const year = String(date.getFullYear()).slice(-2); // Get last two digits
                        return `${day}-${month}-${year}`;
                    }
                    return ' - ';
                }


                if (response.success && response.data.length > 0) {
                    let tableHTML = `
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Institution Name</th>
                    <th>Team Head</th>
                    <th>Team Members</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>ManDays</th>
                    <th>Entry Date</th>
                    <th>Exit Date</th>
                </tr>
            </thead>
            <tbody>
`;

                    response.data.forEach((item, index) => {
                        tableHTML += `
        <tr>
            <td>${index + 1}</td>
            <td>${item.instename}</td>
            <td>${item.teamhead}</td>
            <td>${item.memberdel}</td>
            <td>${formatDate(item.fromdate)}</td>
            <td>${formatDate(item.todate)}</td>
            <td>${item.mandays}</td>
          <td>${formatDate(item.entrymeetdate)}</td>
        <td>${formatDate(item.exitmeetdate)}</td>
        </tr>
    `;
                    });

                    tableHTML += `
            </tbody>
        </table>
    </div>
`;


                    // Pass it to your custom alert/modal system
                    passing_large_alert(
                        'Institution Details',
                        tableHTML, // this becomes the body
                        'large_confirmation_alert',
                        'large_alert_header',
                        'large_alert_body',
                        'forward_alert'
                    );

                    $("#large_modal_process_button").html("Ok");
                    $("#large_modal_process_button").removeAttr("button_finalize");
                    $('#large_modal_process_button').addClass('data-bs-dismiss');
                } else {
                    alert('No objections found.');
                }
            },

            error: function(xhr) {
                console.error('Error:', xhr.responseText || 'Unknown error');
            }
        });
    }

    // function getothermemberdel(auditscheduleid, row) {

    //     alert(row);

    //     if (auditscheduleid) {
    //         alert('if');
    //         $.ajax({
    //             url: '/getothermembers',
    //             method: 'POST',
    //             data: {
    //                 scheduleid: auditscheduleid
    //             },
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function(response) {
    //                 var userdata = response.data; // your users data

    //                 var userdata = response.data;

    //                 var $wrapper = $(row).find('.othermembers-dropdown');
    //                 var $select = $wrapper.find('.othermembers');

    //                 console.log('row:', row);
    //                 console.log('row html:', $(row).html());
    //                 console.log('find .othermembers-dropdown length:', $(row).find('.othermembers-dropdown')
    //                     .length);
    //                 console.log('find .othermembers length:', $(row).find('.othermembers').length);

    //                 // Try to find globally:
    //                 console.log('global .othermembers-dropdown length:', $('.othermembers-dropdown')
    //                     .length);
    //                 console.log('global .othermembers length:', $('.othermembers').length);


    //                 $select.empty();
    //                 $select.append('<option value="">---Select User---</option>');

    //                 if (!userdata || userdata.length === 0) {
    //                     alert('No users available');
    //                     $select.append('<option value="">No user available</option>');
    //                     $wrapper.hide();
    //                 } else {
    //                     $wrapper.show();

    //                     $.each(userdata, function(index, user) {
    //                         var optionText = user.username + ' (' + (user.desigesname || '') + ')';
    //                         $select.append('<option value="' + user.deptuserid + '">' + optionText +
    //                             '</option>');
    //                     });
    //                 }
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error('Error:', error);
    //             }
    //         });
    //     }
    // }


    function getworkallocationdel(auditscheduleid, schememberid, event) {
        if (event) event.preventDefault();

        $.ajax({
            url: '/getworkalloactionbasedonSchedulemember',
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid,
                schememberid: schememberid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    const data = response.data;

                    const language = getLanguage();


                    const groupCol = language === 'ta' ? 'grouptname' : 'groupename';
                    const workCol = language === 'ta' ? 'majorworkallocationtypeename' :
                        'majorworkallocationtypeename';
                    const groupLabel = language === 'ta' ? '????' : 'Group';
                    const workLabel = language === 'ta' ? '??????? ???' : 'Work Allocation';

                    // Group the data
                    const grouped = {};
                    data.forEach(item => {
                        const group = item[groupCol] || '-';
                        const work = item[workCol] || '-';
                        if (!grouped[group]) grouped[group] = new Set();
                        grouped[group].add(work);
                    });

                    // Generate table HTML as a string
                    let tableHTML = `
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>S.No</th>
									<th>${groupLabel}</th>
									<th>${workLabel}</th>
								</tr>
							</thead>
							<tbody>
					`;

                    let index = 1;
                    for (const [group, works] of Object.entries(grouped)) {
                        const workList = Array.from(works).map(w => `<span>${w}</span>`).join('<br>');
                        tableHTML += `
							<tr>
								<td>${index++}</td>
								<td>${group}</td>
								<td>${workList}</td>
							</tr>
						`;
                    }

                    tableHTML += `
							</tbody>
						</table>
					`;

                    // Pass it to your custom alert/modal system
                    passing_large_alert(
                        'Alloted Work',
                        tableHTML, // this becomes the body
                        'large_confirmation_alert',
                        'large_alert_header',
                        'large_alert_body',
                        'forward_alert'
                    );


                    $("#large_modal_process_button").html("Ok");
                    $("#large_modal_process_button").addClass("data-bs-dismiss");
                    $('#large_modal_process_button').removeAttr('button_finalize');

                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                $('#workallocation_container').html(
                    `<p class="text-danger">${language === 'ta' ? '???? ?????????.' : 'An error occurred.'}</p>`
                );
            }
        });
    }


    function getslipallocationdel(auditscheduleid, schememberid, event) {
        if (event) event.preventDefault();

        $.ajax({
            url: '/getslipdetailsbasedon_schedulemember',
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid,
                schememberid: schememberid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (response.success && response.data.length > 0) {
                        let tableHTML = `
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>S.No</th>
									<th>Main Objection (English)</th>
									<th>Sub Objection </th>
									<th>MainSlip Number </th>
										<th>Severity </th>
										<th>Status </th>
								</tr>
							</thead>
							<tbody>
					`;

                        response.data.forEach((item, index) => {
                            tableHTML += `
							<tr>
								<td>${index + 1}</td>
								<td>${item.objectionename}</td>
								<td>${item.subobjectionename}</td>
								<td>${item.mainslipnumber}</td>
								<td>${item.severityelname}</td>
								<td>${item.processelname}</td>


							</tr>
						`;
                        });

                        tableHTML += `
							</tbody>
						</table>
					`;

                        // Pass it to your custom alert/modal system
                        passing_large_alert(
                            'Slip Details',
                            tableHTML, // this becomes the body
                            'large_confirmation_alert',
                            'large_alert_header',
                            'large_alert_body',
                            'forward_alert'
                        );

                        $("#large_modal_process_button").html("Ok");
                        $("#large_modal_process_button").addClass("button_finalize");
                        $('#large_modal_process_button').removeAttr('data-bs-dismiss');
                    } else {
                        alert('No objections found.');
                    }
                } else {
                    alert('Charge not found');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText || 'Unknown error');
            }
        });
    }


    $('#pendingpopup').on('click', function() {
        event.preventDefault();
        $('#exampleModalFullscreen').modal('show');
    });



    function view_files(file) {
        const fileListContainer = $('#view_file-list-container');
        $('#file').hide();
        fileListContainer.empty(); // Clear previous file cards

        $('#file').val('');
        // Set the fileuploadid directly since it's a single file
        $('#uploadid').val(file.fileuploadid);

        const fileCard = `

                <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
                    <div class="d-flex flex-row">

                        <div class="p-3 mb-1">
                            <h3 class="text-dark mb-0 fs-2">
                                <a style="color:black;" href="/${file.path}" target="_blank">${file.name}</a>
                            </h3>
                        </div>
                        <div class="p-1 align-items-center mt-2 "  onclick="remove_file()">
                            <h5 class="text-danger box mb-0 round-40 p-1">
                                <i class="ti ti-trash"></i>
                            </h5>
                        </div>
                    </div>
                </div>
            `;

        fileListContainer.append(fileCard); // Add the file card to the container
    }
</script>
@endsection