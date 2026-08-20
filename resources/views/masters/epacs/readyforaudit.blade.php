@extends('index2')
@section('content')
    @include('common.alert')
    <style>
        .card-title {
            font-size: 15px;
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

        table.dataTable td,
        table.dataTable th {
            word-wrap: break-word;
            white-space: normal;
        }
        .quarter-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .quarter-select.quarter-saved {
            border-color: #0d6efd;
        }
    </style>

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">

    @php
        $rows = collect($forauditinstitutions ?? []);
        $formatCell = function ($value) {
            if ($value === null || $value === '') {
                return '-';
            }
            if (is_scalar($value)) {
                return $value;
            }
            return json_encode($value);
        };
        $getInstitution = function ($row) use ($formatCell) {
            $rowArray = (array) $row;
            return $formatCell(
                $rowArray['instename']
                    ?? $rowArray['insttname']
                    ?? $rowArray['institutionname']
                    ?? $rowArray['institution']
                    ?? null
            );
        };
        $getInstId = function ($row) {
            $rowArray = (array) $row;
            return $rowArray['instid'] ?? $rowArray['inst_id'] ?? $rowArray['institutionid'] ?? null;
        };
        $getQuarter = function ($row) use ($formatCell) {
            $rowArray = (array) $row;
            return $formatCell(
                $rowArray['auditquarter']
                    ?? $rowArray['auditquartercode']
                    ?? $rowArray['quarter']
                    ?? $rowArray['quartercode']
                    ?? null
            );
        };
        $normalizeQuarter = function ($value) {
            if (!$value || $value === '-') {
                return '';
            }
            $val = strtoupper(trim((string) $value));
            if (in_array($val, ['Q1', 'Q2', 'Q3', 'Q4'], true)) {
                return $val;
            }
            if (preg_match('/^Q?0?([1-4])$/', $val, $m)) {
                return 'Q' . $m[1];
            }
            return '';
        };
        $getFinancialYear = function ($row) use ($formatCell) {
            $rowArray = (array) $row;
            return $formatCell(
                $rowArray['financialyear']
                    ?? $rowArray['financialyearcode']
                    ?? $rowArray['audityear']
                    ?? $rowArray['reportfinancialyear']
                    ?? null
            );
        };
        $getRemarks = function ($row) use ($formatCell) {
            $rowArray = (array) $row;
            return $formatCell(
                $rowArray['remarks']
                    ?? $rowArray['remark']
                    ?? $rowArray['remarks_text']
                    ?? null
            );
        };
    @endphp

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">Ready For Audit - DCA</div>
        <div class="card-body">
            @if ($rows->isNotEmpty())

                <div class="datatables">
                    <div class="table-responsive">
                        <table id="readyforaudit_table"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="text-wrap">Institution</th>
                                    <th class="text-wrap">Quarter</th>
                                    {{-- <th class="text-wrap">Financial Year</th> --}}
                                    {{-- <th class="text-wrap">Remarks</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $index => $row)
                                    @php
                                        $rowInstId = $getInstId($row);
                                    @endphp
                                    <tr>
                                        <td class="text-end">{{ $index + 1 }}</td>
                                        <td class="text-wrap">{{ $getInstitution($row) }}</td>
                                        <td class="text-wrap" data-instid="{{ $rowInstId }}">
                                            @php
                                                // $currentQuarter = $normalizeQuarter($getQuarter($row));
                                            @endphp
                                            <select class="form-select form-select-sm quarter-select" name="quarter[]"

                                                data-instid="{{ $rowInstId }}">
                                                <option value="">Select</option>
                                                <option value="Q1">Q1</option>
                                                <option value="Q2">Q2</option>
                                                <option value="Q3">Q3</option>
                                                <option value="Q4">Q4</option>
                                            </select>
                                        </td>

                                        {{-- <td class="text-wrap">{{ $getFinancialYear($row) }}</td> --}}
                                        {{-- <td class="text-wrap">{{ $getRemarks($row) }}</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                 <div class="row mt-4" id="button_shown">
                <div class="col-md-12 text-center">

                    {{-- <button type="button" class="btn btn-danger me-2" id="reset_button">
                        Clear
                    </button> --}}

                    <button type="button" class="btn button_save me-2" id="save_draft_btn">
                        Save Draft
                    </button>

                    <button type="button" class="btn btn-success" id="finalize_btn" disabled>
                        <i class="fas fa-check-circle me-1"></i> Finalize
                    </button>

                </div>
            </div>

            @else
                <div id="no_data">
                    <center>No Data Available</center>
                </div>


            @endif

        </div>
        <div id="ajax-loader">
        <div class="spinner"></div>
    </div>
    </div>
<style>
       #ajax-loader {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        #ajax-loader .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 123, 255, .3);
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
</style>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script>


function showLoader() {
            $('#ajax-loader').css('display', 'flex');
        }

        function hideLoader() {
            $('#ajax-loader').hide();
        }

        function setButtonsBusy(busy) {
            $('#save_draft_btn').prop('disabled', busy);
            const hasSelected = typeof hasSelectedQuarter === 'function' ? hasSelectedQuarter() : false;
            const shouldDisableFinalize = busy || !hasSelected;
            $('#finalize_btn').prop('disabled', shouldDisableFinalize);
        }

        $(document).ready(function() {
            let isSaving = false;
            let isFinalizing = false;
            const pendingQuarterUpdates = new Map();
            const selectionMap = new Map();
            let readyForAuditTable = null;

            function normalizeQuarterJs(value) {
                if (!value) return '';
                const val = String(value).trim().toUpperCase();
                if (['Q1', 'Q2', 'Q3', 'Q4'].includes(val)) return val;
                const match = val.match(/^Q?0?([1-4])$/);
                return match ? `Q${match[1]}` : '';
            }

            function getAllTableRowNodes() {
                if (readyForAuditTable) {
                    return readyForAuditTable.rows().nodes().toArray();
                }
                return $('#readyforaudit_table tbody tr').toArray();
            }

            function collectSelectionsFromTable() {
                const rows = [];
                const nodes = getAllTableRowNodes();
                nodes.forEach(function(node) {
                    const $row = $(node);
                    const $select = $row.find('.quarter-select');
                    if (!$select.length) return;
                    const instid = $select.data('instid');
                    const quarter = $select.val();
                    if (instid && quarter) {
                        rows.push({
                            instid: instid,
                            auditquartercode: quarter
                        });
                    }
                });
                return rows;
            }

            function syncSelectionMap() {
                selectionMap.clear();
                const rows = collectSelectionsFromTable();
                rows.forEach(function(item) {
                    selectionMap.set(item.instid, item.auditquartercode);
                });
            }

            function hasSelectedQuarter() {
                syncSelectionMap();
                return selectionMap.size > 0;
            }

            function updateFinalizeState() {
                if (isSaving || isFinalizing) {
                    setButtonsBusy(true);
                    return;
                }
                $('#finalize_btn').prop('disabled', !hasSelectedQuarter());
            }

            if ($.fn.DataTable && $('#readyforaudit_table').length) {
                readyForAuditTable = $('#readyforaudit_table').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    scrollX: true,
                    autoWidth: false,
                    responsive: true,
                });
            }

            updateFinalizeState();

            $(document).on('change', '.quarter-select', function() {
                const instid = $(this).data('instid');
                const value = $(this).val();
                const current = $(this).data('current') || '';

                if (!instid) {
                    return;
                }

                if (value && value !== current) {
                    pendingQuarterUpdates.set(instid, value);
                } else {
                    pendingQuarterUpdates.delete(instid);
                }

                if (value) {
                    selectionMap.set(instid, value);
                } else {
                    selectionMap.delete(instid);
                }

                updateFinalizeState();
            });

            // Ensure dropdown reflects saved value on load
            $('.quarter-select').each(function() {
                const current = $(this).data('current') || '';
                if (current) {
                    $(this).val(current);
                    $(this).addClass('quarter-saved');
                } else {
                    $(this).removeClass('quarter-saved');
                }
            });

            function applySavedQuarters(rows) {
                rows.forEach(function(item) {
                    const instid = item.instid || item.instId || item.inst_id;
                    const quarterValue = normalizeQuarterJs(item.auditquartercode || item.auditQuarterCode || item
                        .quarter);
                    const $select = $('.quarter-select[data-instid="' + instid + '"]');
                    if (quarterValue) {
                        $select.val(quarterValue);
                        $select.data('current', quarterValue);
                        $select.addClass('quarter-saved');
                        selectionMap.set(instid, quarterValue);
                    }
                });
                updateFinalizeState();
            }
                 showLoader();

            $.ajax({
                url: "/readyforaudit/quarters",
                type: "GET",
                success: function(response) {
                     hideLoader();
                    if (response?.rows) {
                        applySavedQuarters(response.rows);
                    }
                },
                error: function() {
                    hideLoader();
                    $('#process_button').off('click').on('click', function() {
                        updateFinalizeState();
                    });
                    passing_alert_value('Alert', 'Failed to load saved quarters.', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    $("#process_button").show().html("Ok").attr("key", "ok");
                    $("#ok_button").hide();
                    $("#cancel_button").hide();
                    if (typeof translate === 'function') {
                        translate();
                    }
                }
            });

            function showError(message, onOk) {
                $('#process_button').off('click').on('click', function() {
                    if (typeof onOk === 'function') {
                        onOk();
                    }
                });
                passing_alert_value('Alert', message, 'confirmation_alert', 'alert_header', 'alert_body',
                    'confirmation_alert');
                $("#process_button").show().html("Ok").attr("key", "ok");
                $("#ok_button").hide();
                $("#cancel_button").hide();
                if (typeof translate === 'function') {
                    translate();
                }
            }

            function showSuccess(message, onOk) {
                $('#process_button').off('click').on('click', function() {
                    if (typeof onOk === 'function') {
                        onOk();
                    }
                });
                passing_alert_value('Confirmation', message, 'confirmation_alert', 'alert_header', 'alert_body',
                    'confirmation_alert');
                $("#process_button").show().html("Ok").attr("key", "ok");
                $("#ok_button").hide();
                $("#cancel_button").hide();
                if (typeof translate === 'function') {
                    translate();
                }
            }

            function handleSaveDraft() {
                if (isSaving || isFinalizing) return;

                const rows = collectSelectionsFromTable();
                const selectedCount = rows.length;
                let missingInstId = false;
                const nodes = getAllTableRowNodes();
                nodes.forEach(function(node) {
                    const $row = $(node);
                    const $select = $row.find('.quarter-select');
                    if (!$select.length) return;
                    const instid = $select.data('instid');
                    const quarter = $select.val();
                    if (quarter && !instid) {
                        missingInstId = true;
                    }
                });

                if (selectedCount === 0) {
                    showError('Please Select Atleast one Instiution for a Audit.', function() {
                        setButtonsBusy(false);
                    });
                    return;
                }

                if (missingInstId) {
                    showError('Institution ID is missing. Please refresh the page.', function() {
                        setButtonsBusy(false);
                    });
                    return;
                }

                if (rows.length === 0) {
                    setButtonsBusy(false);
                    return;
                }

                isSaving = true;
                setButtonsBusy(true);
                showLoader();
                $.ajax({
                    url: "/readyforaudit/action",
                    type: "POST",
                    data: {
                        action: "save",
                        rows: rows
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        hideLoader();
                        const responseRows = response?.rows || [];
                        const rowsToApply = responseRows.length ? responseRows : rows;

                        rowsToApply.forEach(function(item) {
                            const instid = item.instid || item.instId || item.inst_id;
                            const quarterValue = normalizeQuarterJs(item.auditquartercode || item
                                .auditQuarterCode || item.quarter);
                            const $select = $('.quarter-select[data-instid="' + instid + '"]');
                            if (quarterValue) {
                                $select.val(quarterValue);
                                $select.data('current', quarterValue);
                                $select.addClass('quarter-saved');
                            }
                            pendingQuarterUpdates.delete(instid);
                        });

                        isSaving = false;
                        setButtonsBusy(false);
                        updateFinalizeState();
                        showSuccess('Saved successfully.');
                    },
                    error: function(xhr) {
                        hideLoader();
                        showError(xhr.responseJSON?.message || 'Save failed.', function() {
                            isSaving = false;
                            setButtonsBusy(false);
                            updateFinalizeState();
                        });
                    }
                });
            }

            function handleFinalize() {
                if ($('#finalize_btn').prop('disabled') || isSaving || isFinalizing) return;

                $('#process_button').off('click').on('click', function() {
                    sentfinaliseOTP();
                });
                passing_alert_value('Confirmation', 'Are you sure to finalize?', 'confirmation_alert',
                    'alert_header', 'alert_body', 'forward_alert');
                $("#process_button").show().html("Ok").attr("key", "ok");
                $("#ok_button").hide();
                $("#cancel_button").show();
                if (typeof translate === 'function') {
                    translate();
                }
            }

            $('#save_draft_btn').on('click', handleSaveDraft);
            $('#finalize_btn').on('click', handleFinalize);
        });




         function renderOtpPopup() {
            $('#close_button').hide();
            const otpcontent = `       <div id="otp_div">
                                        <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                                        <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mail</span><br>
                                        <div class="row justify-content-center">
                                            <div class="col-auto">
                                                <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                    <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                                                </div>
                                                <button type="button" id="verify_otp_button" class="btn btn-primary w-100" id="verify_otp_btn">Verify OTP</button>
                                            </div>
                                        </div><br>

                                        <small class="text-center mb-3 d-block">Didn't receive the code? <b  id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b></small><br>

                                    </div>`;


            $('#otp_div').css({
                'text-align': 'center'
            }).show();

            $('#confirmation_alert .modal-footer').hide();
            $('#process_button').html("Verify OTP");

            passing_alert_value('Confirmation', otpcontent,
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            $('#confirmation_alert').modal('show');

            $(document).off('click', '#verify_otp_button').on('click', '#verify_otp_button', function () {
                if ($(this).prop('disabled')) return;
                $(this).prop('disabled', true);
                handleVerifyOtp();
            });
            $(document).off('click', '#resend_otp_link').on('click', '#resend_otp_link', function (e) {
                e.preventDefault();
                handleResendOtp();
            });
            $(document).off('input', '.otp-input').on('input', '.otp-input', function () {
                const val = $(this).val().replace(/[^0-9]/g, '').slice(0, 1);
                $(this).val(val);
                if (val) {
                    $(this).next('.otp-input').focus();
                }
            });
            $(document).off('keydown', '.otp-input').on('keydown', '.otp-input', function (e) {
                if (e.key === 'Backspace' && !$(this).val()) {
                    $(this).prev('.otp-input').focus();
                }
            });


        }



function sentfinaliseOTP() {

            $('#process_button').attr('disabled', true);
            showLoader();

            $.ajax({
                url: 'readyforaudit/sendOtp',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },


                success: function(response) {
                    const ok = response && (response.status === 'success' || response.success === true);
                    if (ok) {
                        // Keep loader visible during delay
                        setTimeout(function() {
                            renderOtpPopup();
                            hideLoader();
                            $('#process_button').removeAttr('disabled');
                        }, 1000); // 1 second delay
                    } else {
                        hideLoader();
                        $('#process_button').removeAttr('disabled');
                    }
                },

                error: function() {
                    hideLoader();
                    $('#process_button').removeAttr('disabled');
                    alert('Failed to send OTP.');
                }
            });
        }
let isFinalizing = false;
        function submitFinalizeRequest() {
            if (isFinalizing) return;
            isFinalizing = true;
            setButtonsBusy(true);
            showLoader();
            $.ajax({
                url: "/readyforaudit/action",
                type: "POST",
                data: {
                    action: "finalize"
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    hideLoader();
                    $('#confirmation_alert .modal-footer').show();
                    $('#process_button').off('click').on('click', function () {
                        isFinalizing = false;
                        location.reload();
                    });
                    passing_alert_value('Confirmation', 'Finalized successfully.', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    $("#process_button").show().html("Ok").attr("key", "ok");
                    $("#ok_button").hide();
                    $("#cancel_button").hide();
                    if (typeof translate === 'function') {
                        translate();
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    $('#confirmation_alert .modal-footer').show();
                    showError(xhr.responseJSON?.message || 'Finalize failed.', function() {
                        isFinalizing = false;
                        setButtonsBusy(false);
                        updateFinalizeState();
                    });
                }
            });
        }

function handleVerifyOtp() {

            let otp = '';
            document.querySelectorAll('.otp-input').forEach(input => {
                otp += input.value;
            });

        if (!/^\d{6}$/.test(otp)) {
            alert('Please enter a valid 6-digit OTP.');
            $('#verify_otp_button').prop('disabled', false);
            return;
        }

            showLoader();
            $('#process_button').attr('disabled', true);

            $.ajax({
                url: 'readyforaudit/verifyOtp',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    otp: otp
                },

                success: function(response) {
                    hideLoader();
                    if (response.status === 'success') {
                        submitFinalizeRequest();
                        // $('#confirmation_alert .modal-footer').show();
                        //  getLabels_jsonlayout([{
                        //     id: response.message,
                        //     key: response.message
                        // }], 'N').then((t) => {
                        //     passing_alert_value('Confirmation', Object.values(t)[0],
                        //         'confirmation_alert', 'alert_header', 'alert_body',
                        //         'confirmation_alert');
                        //     $('#ok_button').off('click').on('click', function () {
                        //          $('#confirmation_alert').modal('hide');
                        //     // if (!finalizeBtnRef || !finalizeBtnRef.length) {
                        //     //     finalizeBtnRef = $('#finalizebuttonaction');
                        //     // }

                        //     });
                        // });


                    } else {
                        alert(response.message || 'Incorrect OTP.');
                        $('#verify_otp_button').prop('disabled', false);
                    }
                },

                complete: function() {
                    hideLoader();
                    $('#process_button').removeAttr('disabled');
                },

                error: function() {

                    alert('OTP verification failed.');
                    hideLoader();
                    $('#verify_otp_button').prop('disabled', false);
                }
            });
        }

        function handleResendOtp() {

            $('#confirmation_alert').modal('hide');
            showLoader();
            $.ajax({
                url: 'readyforaudit/sendOtp',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function() {
                    setTimeout(() => {
                        renderOtpPopup();
                    }, 100);

                },

                complete: function() {
                    hideLoader();
                },

                error: function() {
                    alert('Failed to resend OTP.');
                    hideLoader();
                }
            });
        }
    </script>
@endsection

