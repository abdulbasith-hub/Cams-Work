@section('content')
    @extends('index2')
    @include('common.alert')
    @include('apms.apmsalert')
    {{-- @include('apms.retirement') --}}


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
    <style>
        /* Ensure retirement para checkboxes use red (Bootstrap danger) instead of default blue */
        input.para-checkbox {
            accent-color: #1a46be;
            /* modern browsers */
        }

        /* Fallback styling for older browsers */
        input.para-checkbox:checked {
            background-color: #1a46be;
            border-color: #1a46be;
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">



    <div class="card mt-3 border border-info">
        <div class="card-header card_header_color">
            Retirement Para Details
        </div>

        <div class="card-body">

            <!-- TABLE -->
            <div class="table-responsive hide_this" id="tableshow">
                <table id="audit_plandetails" class="table table-striped table-bordered w-100 text-nowrap datatables-basic">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th class="text-center">Select Para</th>
                            <th class="w-50">Slip Details</th>
                            <th>Para Number</th>
                            <th>Liabilty</th>
			    <th>Received on</th>
                            <th>Action</th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- NO DATA -->
            <div id="no_data" class="text-center text-muted py-2 d-none">

                No Data Available
            </div>

            <!-- BUTTONS -->
            <div class="row mt-4 d-none" id="button_shown">
                <div class="col-md-12 text-center">

                    {{-- <button type="button" class="btn btn-danger me-2" id="reset_button">
                        Clear
                    </button> --}}

                    <button type="button" class="btn button_save me-2" id="save_draft_btn">
                        Save Draft
                    </button>

                    <button type="button" class="btn btn-success" id="finalize_btn">
                        <i class="fas fa-check-circle me-1"></i> Finalize
                    </button>

                </div>
            </div>

        </div>


        <div id="ajax-loader">
            <div class="spinner"></div>
        </div>



        <style>
            /* Overlay */
            #ajax-loader {
                display: none;
                /* Hidden by default */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.6);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }

            /* Spinner */
            #ajax-loader .spinner {
                width: 50px;
                height: 50px;
                border: 5px solid rgba(0, 123, 255, 0.3);
                /* Light blue border */
                border-top: 5px solid #007bff;
                /* Solid blue top */
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
        <style>
            .field-error {
                color: #dc3545;
                font-size: 11px;
                margin-top: 4px;
                display: block;
            }

            .has-error select,
            .has-error input {
                border-color: #dc3545;
            }
        </style>



        <script src="../assets/js/vendor.min.js"></script>
        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- solar icons -->
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        {{-- data table --}}
        <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


        <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
        <script>
            let paraActions = []; // global


            $(document).ready(function () {
                fetch_paradetails();
            });

            function fetch_paradetails() {

                $.ajax({
                    url: '/retirementparas',
                    type: 'GET',
                    success: function (json) {

                        if (json.success && Array.isArray(json.data) && json.data.length > 0) {

                            paraActions = json.actions || [];
                            paraRejoinderLimit = json.rejoinderLimit;
                            // ✅ SHOW table + buttons
                            $('#tableshow').removeClass('d-none').show();
                            $('#button_shown').removeClass('d-none').show();

                            // ❌ HIDE no data
                            $('#no_data').addClass('d-none').hide();
                            console.log(json.data);
                            // console.log('Rejoinder limit:', paraRejoinderLimit);

                            loadRetirementParaTable(json.data);

                        } else {

                            // ❌ HIDE table + buttons
                            $('#tableshow').addClass('d-none').hide();
                            $('#button_shown').addClass('d-none').hide();

                            // ✅ SHOW no data
                            $('#no_data').removeClass('d-none').show();
                        }
                    },
                    error: function () {
                        alert('Server error');
                    }
                });
            }






            function validateParaRows() {

                let valid = true;

                $('#audit_plandetails tbody tr').each(function () {

                    const row = $(this);
                    const checkbox = row.find('.para-checkbox');
                    const action = row.find('.para-action');

                    // reset errors
                    row.find('.field-error').addClass('d-none');
                    row.removeClass('has-error');

                    // if checkbox checked → action required
                    if (checkbox.is(':checked')) {

                        if (!action.val()) {
                            action.closest('.cell-wrap')
                                .find('.action-error')
                                .removeClass('d-none');

                            row.addClass('has-error');
                            valid = false;
                        }
                    }
                });

                return valid;
            }
            $(document).on('change', '.para-checkbox, .para-action', function () {
                const row = $(this).closest('tr');
                row.find('.field-error').addClass('d-none');
                row.removeClass('has-error');
            });
            $(document).on('input', '#finalize_comments', function () {

                let value = $(this).val();

                // ❌ Remove all special characters
                value = value.replace(/[^a-zA-Z0-9\s]/g, '');

                // ❌ Limit to 500 characters
                if (value.length > 500) {
                    value = value.substring(0, 500);
                }

                $(this).val(value);

                // 🔔 Error message handling
                if (value.length === 0) {
                    $('#remarks_error')
                        .removeClass('d-none')
                        .text('Remarks are required');
                } else if (value.length === 500) {
                    $('#remarks_error')
                        .removeClass('d-none')
                        .text('Maximum 500 characters allowed');
                } else {
                    $('#remarks_error').addClass('d-none');
                }
            });


            function loadRetirementParaTable(parasData) {

                if (!parasData || parasData.length === 0) {
                    $('#tableshow').hide();
                    $('#no_data').show();
                    return;
                }

                $('#tableshow').show();
                $('#no_data').hide();

                if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                    $('#audit_plandetails').DataTable().clear().destroy();
                }

                $('#audit_plandetails').DataTable({
                    rowId: 'paraid',
                    data: parasData,
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    scrollX: true,
                    columns: [
                        // S.No
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        // Checkbox column
                        {
                            data: 'encrypted_paraid',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                const checkedAttr = row.com_action ? 'checked' : '';
                                return `<input type="checkbox" class="para-checkbox" value="${data}" ${checkedAttr}>
                                                                    <span class="field-error checkbox-error d-none">Please select para</span>`;
                            }
                        },
                        // Slip details
                        {
                            data: "slipdetails",
                            // title: columnLabels?.["slipdetails"]?.[language],
                            className: "d-none d-md-table-cell lang extra-column text-wrap",
                            render: function (data) {
                                return data || '-';
                            }
                        },
                        // Para number
                        {
                            data: "parano",
                            // title: columnLabels?.["paranumber"]?.[language],
                            className: "d-none d-md-table-cell lang extra-column text-wrap",
                            render: function (data) {
                                return data || '-';
                            }
                        },
                        {
                            data: "auditee_liability",
                            // title: columnLabels?.["paranumber"]?.[language],
                            className: "d-none d-md-table-cell lang extra-column text-wrap",
                            render: function (data) {
                                return data || '-';
                            }
                        },
			{
                                    data: 'retirement_para_date',
                                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                                    render: function (data, type) {
                                        if (!data) {
                                            return type === 'sort' ? '' : '-';
                                        }
                                        if (type === 'sort' || type === 'type') {
                                            return data;
                                        }
                                        const date = new Date(data);
                                        if (Number.isNaN(date.getTime())) {
                                            return data;
                                        }
                                        const day = String(date.getDate()).padStart(2, '0');
                                        const month = String(date.getMonth() + 1).padStart(2, '0');
                                        const year = date.getFullYear();
                                        return `${day}/${month}/${year}`;
                                    }
                                },
                        // Dropdown column
                        {
                            data: 'encrypted_paraid',
                            title: 'Action',
                            className: "d-none d-md-table-cell lang extra-column text-wrap ",
                            render: function (data, type, row) {

                                // ✅ get cycle safely here
                                const rejoinderCycle = row.rejoindercycle ?? 0;

                                return buildActionDropdown(
                                    row.encrypted_paraid,
                                    row.actioncode,
                                    rejoinderCycle
                                );
                            }
                        },

                        // View Details
                        {
                            data: "encrypted_paraid",
                            render: function (data) {
                                return `<div class="d-flex justify-content-center gap-1">
                                                                                <button type="button" class="btn btn-sm btn-info enable-row"
                                                                                        onclick="get_para_historydetails(this,'view_para')"
                                                                                        data-para="${data}" style="font-size:10px">
                                                                                    <i class="ti ti-clipboard-data fs-2 me-2"></i>Para Details
                                                                                </button>
                                                                                <button type="button" class="btn btn-sm btn-primary liability-btn d-none"
                                                                                        onclick="get_liabilty_details(this,'view_para')"
                                                                                        data-para="${data}" style="font-size:10px">
                                                                                    <i class="ti ti-list-details fs-2 me-2"></i>Liability
                                                                                </button>
                                                                            </div>`;
                            }
                        }
                    ],
                    drawCallback: function () {
                        $('.para-action').each(function () {
                            toggleLiabilityButton(this);
                        });
                    }

                });
            }

            function toggleLiabilityButton(selectElement) {

                const selectedValue = $(selectElement).val();
                const liabilityBtn = $(selectElement).closest('tr').find('.liability-btn');

                if (selectedValue === '11') {
                    liabilityBtn.removeClass('d-none');
                } else {
                    liabilityBtn.addClass('d-none');
                }
            }

            $(document).on('change', '.para-action', function () {
                toggleLiabilityButton(this);
            });



            function buildActionDropdown(encrypted_paraid, selectedActionCode, rejoinderCycle) {

                selectedActionCode = selectedActionCode ? String(selectedActionCode).trim() : '';
                rejoinderCycle = parseInt(rejoinderCycle, 10) || 0;
                paraRejoinderLimit = parseInt(paraRejoinderLimit, 10) || 0;

                // console.log('Cycle:', rejoinderCycle, 'Limit:', paraRejoinderLimit);

                let options = `<option value="">Select Action</option>`;

                paraActions.forEach(act => {

                    const actionCode = String(act.actioncode).trim();

                    // 🚫 Hide "Need Clarification" (03)
                    // ONLY when this para reached its limit
                    if (actionCode === '03' && rejoinderCycle >= paraRejoinderLimit) {
                        return;
                    }

                    const selected = actionCode === selectedActionCode ? 'selected' : '';

                    options += `<option value="${actionCode}" ${selected}>
                                                    ${act.actionename}
                                                </option>`;
                });

                return `
                                    <select class="form-select form-select-sm para-action "
                                            data-paraid="${encrypted_paraid}" disabled>
                                        ${options}
                                    </select>
                                    <span class="field-error action-error d-none">
                                        Please select action
                                    </span>
                                `;
            }
            $(document).on('click', '#verified_btn', function () {

                $('.para-action').prop('disabled', false);

            });




            $(document).on('click', '#save_draft_btn', function () {
                submitParaAction('draft');
            });

            function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
                const $container = $('#' + containerid).empty();

                files.forEach(file => {
                    if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);

                    const fileCard = `
                                        <div class="position-relative ms-2" style="max-width: 320px;">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-center gap-2">

                                                        <div class="p-2 bg-primary-subtle rounded d-flex align-items-center justify-content-center"
                                                             style="width:36px;height:36px;">
                                                            <i class="ti ti-file-text text-primary fs-6"></i>
                                                        </div>

                                                        <div class="text-truncate" style="max-width: 240px;">
                                                            <a class="fw-semibold text-dark text-decoration-none text-truncate d-block"
                                                               href="${file.path || '#'}"
                                                               target="_blank"
                                                               title="${file.name}">
                                                                ${file.name}
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;

                    $container.append(fileCard);
                });
            }



            function getSelectedParaActions() {

                let selected = [];

                $('.para-checkbox:checked').each(function () {

                    const row = $(this).closest('tr');

                    selected.push({
                        paraid: $(this).val(),
                        actionid: row.find('.para-action').val()
                    });
                });

                return selected;
            }




            // let finalizeBtnRef = null;

            // $("#finalizebuttonaction").on("click", function(event) {
            //     event.preventDefault();

            //     finalizeBtnRef = $(this);

            //     passing_alert_value(
            //         'Confirmation',
            //         'Are you sure to finalize Spillover and Pending Institutions?',
            //         'confirmation_alert',
            //         'alert_header',
            //         'alert_body',
            //         'forward_alert'
            //     );

            //     $("#process_button").off("click").on("click", function() {
            //         $("#confirmation_alert").modal("hide");
            //         sentfinaliseOTP();
            //     });

            //     $("#process_button").html("Send OTP");
            // });
            // -----------------------------
            // 1️⃣ FINALIZE BUTTON CLICK
            // -----------------------------
            $(document).on('click', '#finalize_btn', function () {

                // Show first confirmation popup
                passing_alert_value(
                    'Confirmation',
                    'Are you sure to finalize the selected Paras? Once finalized, changes cannot be reverted.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Process button inside first confirmation
                $(document).off('click', '#process_button').on('click', '#process_button', function () {

                    $('#confirmation_alert').modal('hide');

                    // Show EXTRA LARGE popup
                    passing_extra_large_alert(
                        'MOM Fields',
                        `
                                                <div class="container-fluid">

                                                    <!-- ROW 1 : REMARKS -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                Remarks <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea id="finalize_comments"
                                                                class="form-control para-remarks"
                                                                rows="3"
                                                                placeholder="Enter remarks"></textarea>
                                                            <small id="remarks_error" class="text-danger d-none">
                                                                Remarks are required
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- ROW 2 : DATE + FILE -->
                                                    <div class="row g-3">

                                                        <!-- Meeting Date -->
                                                        <div class="col-md-6">
                                                            <label class="form-label">
                                                                Meeting Date <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group" onclick="datepicker('mom_date','')">
                                                                <input type="text"
                                                                    class="form-control datepicker"
                                                                    id="mom_date"
                                                                    name="mom_date"
                                                                    placeholder="dd/mm/yyyy">
                                                                <span class="input-group-text">
                                                                    <i class="ti ti-calendar fs-5"></i>
                                                                </span>
                                                            </div>
                                                            <small id="date_error" class="text-danger d-none">
                                                                Meeting date is required
                                                            </small>
                                                        </div>

                                                        <!-- File Upload -->
                                                        <div class="col-md-6">
                                                            <label class="form-label">
                                                                MOM Document (only PDF max 2MB) <span class="text-danger">*</span>
                                                            </label>

                                                            <!-- hidden input -->
                                                            <input type="file" id="finalize_file" accept="application/pdf" class="d-none">

                                                            <!-- upload button -->
                                                            <button type="button" class="btn btn-outline-primary w-100" id="add-file-btn">
                                                                <i class="ti ti-upload"></i> Upload PDF
                                                            </button>

                                                            <!-- container to show uploaded file -->
                                                            <div id="uploaded_file_container" class="d-flex mt-2"></div>

                                                            <small id="file_error" class="text-danger d-none">
                                                                Please upload a valid PDF file (max 2MB)
                                                            </small>
                                                        </div>

                                                    </div>
                                                </div>
                                                `,
                        'extra_large_confirmation_alert',
                        'extra_large_alert_header',
                        'extra_large_alert_body',
                        'extra_large_confirmation_alert',
                        'send_intimation_label'
                    );

                    // Prevent modal from closing automatically
                    $('#extra_large_confirmation_alert')
                        .off('hide.bs.modal')
                        .on('hide.bs.modal', function (e) {
                            if ($(this).data('block-close') === true) e.preventDefault();
                        });
                    $('#extra_large_modal_cancel_button').data('block-close', true);
                    $('#extra_large_confirmation_alert').modal('hide');
                    $('#large_confirmation_button_close').hide();
                    $('#extra_large_confirmation_alert').data('block-close', true);
                });

                // 🔹 Open file dialog
                $(document).on('click', '#add-file-btn', function () {
                    $('#finalize_file').click();
                });

                // 🔹 Show uploaded file using UploadedFileList()
                $(document).on('change', '#finalize_file', function () {
                    const file = this.files[0];
                    const $container = $('#uploaded_file_container').empty();

                    if (!file) return;

                    // validate PDF + size
                    if (file.type !== 'application/pdf' || file.size > 2 * 1024 * 1024) {
                        $('#file_error').removeClass('d-none');
                        this.value = '';
                        return;
                    } else {
                        $('#file_error').addClass('d-none');
                    }

                    // use UploadedFileList to show file card
                    UploadedFileList([{
                        name: file.name,
                        path: '#', // placeholder path
                        fileuploadid: 0
                    }], 'view', 'uploaded_file_container', 'N', '');
                });

                // ✅ Process button
                $(document).off('click', '#extra_large_modal_process_button').on('click',
                    '#extra_large_modal_process_button',
                    function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const remarks = $('#finalize_comments').val().trim();
                        const date = $('#mom_date').val().trim();
                        const file = $('#finalize_file')[0].files[0];

                        let valid = true;

                        if (!remarks) {
                            $('#remarks_error').removeClass('d-none');
                            valid = false;
                        } else $('#remarks_error').addClass('d-none');

                        if (!date) {
                            $('#date_error').removeClass('d-none');
                            valid = false;
                        } else $('#date_error').addClass('d-none');

                        if (!file) {
                            $('#file_error').removeClass('d-none');
                            valid = false;
                        } else $('#file_error').addClass('d-none');

                        if (!valid) return;

                        const modal = $('#extra_large_confirmation_alert');
                        modal.data('block-close', false);
                        modal.modal('hide');

                        submitParaAction('finalize', remarks, file, date);
                    });

            });

            // -----------------------------
            // CANCEL BUTTON
            // -----------------------------
            $(document).off('click', '#extra_large_modal_cancel_button')
                .on('click', '#extra_large_modal_cancel_button', function () {
                    const modal = $('#extra_large_confirmation_alert');
                    modal.data('block-close', false); // allow closing
                    modal.modal('hide');
                });

            // -----------------------------
            // PROCESS BUTTON
            // -----------------------------
            $(document).off('click', '#extra_large_modal_process_button')
                .on('click', '#extra_large_modal_process_button', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const remarks = $('#finalize_comments').val().trim();
                    const date = $('#mom_date').val().trim();

                    let valid = true;

                    // ✅ Remarks validation
                    if (!remarks) {
                        $('#remarks_error').removeClass('d-none');
                        valid = false;
                    } else {
                        $('#remarks_error').addClass('d-none');
                    }

                    // ✅ Date validation
                    if (!date) {
                        $('#date_error').removeClass('d-none');
                        valid = false;
                    } else {
                        $('#date_error').addClass('d-none');
                    }

                    // ✅ File validation
                    const uploadedFiles = [];
                    $('#uploaded_files_container input[type="hidden"]').each(function () {
                        uploadedFiles.push($(this).val());
                    });

                    if (uploadedFiles.length === 0) {
                        $('#file_error').removeClass('d-none');
                        valid = false;
                    } else {
                        $('#file_error').addClass('d-none');
                    }

                    // ❌ keep popup open if invalid
                    if (!valid) return;

                    // ✅ allow modal close
                    const modal = $('#extra_large_confirmation_alert');
                    modal.data('block-close', false);
                    modal.modal('hide');

                    // ✅ Submit data
                    submitParaAction('finalize', remarks, uploadedFiles, date);
                });


            function showLoader() {
                document.getElementById('ajax-loader').style.display = 'flex';
            }

            function hideLoader() {
                document.getElementById('ajax-loader').style.display = 'none';
            }


            function submitParaAction(actionType, remarks = '', file = null, date = '') {

                if (!validateParaRows()) return;

                const paraData = getSelectedParaActions();
                if (paraData.length === 0) {
                    alert('Please select at least one para');
                    return;
                }

                const formData = new FormData();
                formData.append('action', actionType);
                formData.append('date', date);

                paraData.forEach((row, index) => {
                    formData.append(`paras[${index}][paraid]`, row.paraid);
                    formData.append(`paras[${index}][actionid]`, row.actionid);
                    formData.append(`paras[${index}][remarks]`, remarks);
                });

                if (file) {
                    formData.append('file', file);
                }
                showLoader();
                // $('#extra_large_modal_process_button').attr('disabled', true);
                $.ajax({
                    url: '/save_retirement_paras',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        hideLoader();
                        if (response.success) {

                            passing_alert_value(
                                'Confirmation',
                                response.message,
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'confirmation_alert'
                            );

                            // ✅ ONLY RELOAD DATA (NOT PAGE)
                            $("#ok_button").off('click').on('click', function () {

                                // optional cleanup
                                $('#finalize_comments').val('');
                                $('input[type=checkbox]').prop('checked', false);

                                fetch_paradetails();
                            });
                        }
                    },
                    error: function (xhr) {

                        hideLoader();
                        // $('#extra_large_modal_process_button').prop('disabled', false);
                        alert(xhr.responseJSON?.message || 'Server error');
                    }
                });
            }




            function get_para_historydetails(id, param) {
                const paraid = id.dataset.para;
                // console.log(para);

                $.ajax({
                    url: "/apms/fetch_para_datas",
                    type: "POST",


                    data: {
                        paraid: paraid,
                        param: param
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var para_det = response.data;
                        var prarahistory_det = response.parahistory;
                        var liability_det = response.liability;

                        // console.log('API response', prarahistory_det);

                        // If server responded with an error message but still returned liability array, allow processing.
                        if (response.success === false && (!liability_det || liability_det.length === 0)) {
                            alert(response.message || 'Server error');
                            return;
                        }

                        if (param == 'view_flow') {

                            var designation = lang == 'ta' ? 'பதவி' : 'Designation';
                            var status = lang == 'ta' ? 'நிலை' : 'Status';
                            var serialno = lang == 'ta' ? 'எஸ்.எண்' : 'S.No';
                            var actiontakenon = lang == 'ta' ? 'அனுப்பிய விவரங்கள்' :
                                'Forwarded On';

                            var datacontent = `<div class=" table-responsive rounded-2 border " style="max-width: 750px;overflow-x: auto;max-height: 600px; overflow-y: auto;width:98%;margin:0 auto;">
                                                                                               <table class="table userCountTable">
                                                                                                   <thead class="">
                                                                                                       <tr>
                                                                                                           <th class="lang userCount_head" key="audit_office">${serialno}
                                                                                                           </th>
                                                                                                           <th class="lang userCount_head" key="">${designation}
                                                                                                           </th>
                                                                                                           <th class="lang userCount_head" key="">${status}</th>
                                                                                                            <th class="lang userCount_head" key="">${actiontakenon}</th>
                                                                                                       </tr>
                                                                                                   </thead>
                                                                                                   <tbody id="instTableBody">

                                                                                                   </tbody>
                                                                                               </table>
                                                                                           </div>`;
                            passing_extra_large_alert('Para Flow', datacontent,
                                'extra_large_confirmation_alert',
                                'extra_large_alert_header',
                                'extra_large_alert_body', 'confirmation_alert', 'send_intimation_label');
                            setTimeout(() => {
                                populatTable(para_det);
                            }, 100);

                        } else if (param == 'view_para') {
                            // console.log(para_det);

                            populate_parafulldetails(lang, para_det, prarahistory_det, 'parafull_details',
                                'parafull_det',
                                'parafull_dettable')

                        }

                    }
                });
            }
            function get_liabilty_details(id, param) {

                const paraid = id.dataset.para;

                $.ajax({
                    url: "/apms/fetch_para_datas",
                    type: "POST",
                    data: {
                        paraid: paraid,
                        param: 'view_para'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {

                        if (!response || response.success === false) {
                            alert(response?.message || 'Failed to fetch liability details');
                            return;
                        }

                        let liability_det = [];
                        const result = response.data;

                        if (result && result.liabilitydel && result.liabilitydel.trim() !== '') {

                            liability_det = result.liabilitydel.split(',').map(item => {

                                const parts = item.split('~');

                                return {
                                    notype: parts[0] || '',
                                    liabilitygpfno: parts[1] || '',
                                    liabilityname: parts[2] || '',
                                    liabilitydesignation: parts[3] || '',
                                    liabilityamount: parts[4] || '',
                                    retirementyear: parts[5] || '',
                                    retirementmonth: parts[6] || '',
                                    retiredflag: parts[7] || '',
                                    statusflag: parts[8] || '',  // ✅ USE THIS
                                    followupliabilityid: parts[9] || ''
                                };
                            });
                        }

                        console.log(liability_det);

                        const noTypeMap = {
                            '01': 'EPF No',
                            '02': 'CPS No',
                            '03': 'IFHRMS No'
                        };

                        let tableRows = '';

                        liability_det.forEach((item, index) => {

                            const noTypeKey = String(item.notype ?? '').padStart(2, '0');
                            const noTypeLabel = noTypeMap[noTypeKey] || '-';

                            const amount = item.liabilityamount
                                ? `₹ ${Number(item.liabilityamount).toLocaleString('en-IN')}`
                                : '-';

                            const retiredFlag = item.retiredflag === 'L'
                                ? 'Less than 3 Months'
                                : item.retiredflag === 'M'
                                    ? '3 to 6 Months'
                                    : 'More than 6 Months';


                            const retirementDisplay = (item.retirementmonth || item.retirementyear)
                                ? `${String(item.retirementmonth).padStart(2, '0')}/${item.retirementyear}`
                                : '-';

                            tableRows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${noTypeLabel}</td>
                                    <td>${item.liabilitygpfno}</td>
                                    <td>${item.liabilityname || '-'}</td>
                                    <td>${item.liabilitydesignation || '-'}</td>
                                    <td class="text-end">${amount}</td>
                                    <td>${retirementDisplay}</td>
                                    <td>${retiredFlag}</td>
                                    <td>
                                        <select class="form-select form-select-sm liability-status"
                                            data-followupliabilityid="${item.followupliabilityid}"
                                            data-index="${index}">
                                            <option value="Y" ${item.statusflag === 'Y' ? 'selected' : ''}>Retain Responsibility</option>
                                            <option value="C" ${item.statusflag === 'C' ? 'selected' : ''}>Remove Responsibility</option>
                                        </select>
                                    </td>
                                </tr>
                            `;
                        });

                        const popupContent = `
                            <div class="table-responsive liability-page">
                                <table class="table table-bordered table-striped" id ='liability_table'>
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>ID-Type</th>
                                            <th>Number</th>
                                            <th>Name</th>
                                            <th>Designation</th>

                                            <th class="text-end">Amount</th>
                                            <th>Retirement</th>
                                            <th>Status</th>
                                            <th>Responsibilty Removal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRows}
                                    </tbody>
                                </table>

                            </div>
                            <div class="mt-3">

                <p style = 'color:red; font-weight:500;'>
                   *Please confirm before proceeding with the responsibility Remove.
                </p>
            </div>
                        `;
                        // $("#" + alert_header_id).html(alert_header);
                        // $('#alert_header').text('Submit');
                        $("#alert_header").hide();
                        passing_large_alert('Liability Details <small>(Responsibility Removal)</small>', popupContent, 'large_confirmation_alert',
                            'large_alert_header',
                            'large_alert_body', 'forward_alert', 'send_intimation_label');
                        // passing_alert_value(
                        //     'Liability Details',
                        //     popupContent,
                        //     'confirmation_alert',
                        //     'alert_header',
                        //     'alert_body',
                        //     'forward_alert'
                        // );


                        $("#large_modal_process_button").off("click").on("click", function () {
                            update_liability_status(null, 'liability_stage');
                        });

                        setTimeout(function () {
                            $('#large_confirmation_alert .modal-dialog').css({
                                'max-width': '65%',
                                'width': '65%'
                            });
                        }, 150);
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON?.message || 'Server error');
                    }
                });
            }

            function update_liability_status(followupliabilityid, state) {

                let statusData = [];

                $('.liability-status').each(function () {
                    statusData.push({
                        followupliabilityid: $(this).data('followupliabilityid'),
                        statusflag: $(this).val(),
                        index: $(this).data('index')
                    });
                });

                $.ajax({
                    url: "/save_retirement_paras",
                    type: "POST",
                    data: {
                        followupliabilityid: followupliabilityid,
                        statuses: statusData,
                        action: state
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N')
                                .then((text) => {
                                    $('#close_button').hide();
                                    passing_alert_value(
                                        'Confirmation',
                                        Object.values(text)[0],
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );
                                    // if (methodName === "pendingdatainsert") {
                                    $("#ok_button").off('click').on('click', function () {

                                        $('#confirmation_alert').modal('hide');

                                        // Reload only table
                                        // $('#liability_table').DataTable().ajax.reload(null, false);

                                    });

                                    // }
                                });
                        }
                    },
                    error: function (xhr) {
                        alert("Error: " + xhr.responseText);
                    },
                    complete: function () {
                        //$button.prop("disabled", false);
                    }
                });
            }




            // function populate_liabilitydetails(liability_data) {

            //     const tbody = $('#liability_body');
            //     tbody.empty();

            //     if (!liability_data || liability_data.length === 0) {
            //         $('#liability_section').hide();
            //         return;
            //     }

            //     // 🔹 No Type mapping
            //     const noTypeMap = {
            //         '01': 'EPF No',
            //         '02': 'CPS No',
            //         '03': 'IFHRMS No'
            //     };

            //     liability_data.forEach((item, index) => {

            //         let amount = item.liabilityamount ?
            //             `₹ ${Number(item.liabilityamount).toLocaleString('en-IN')}` :
            //             '-';

            //         // ✅ normalize notype (1 → 01)
            //         let noTypeKey = String(item.notype ?? '').padStart(2, '0');
            //         let noTypeLabel = noTypeMap[noTypeKey] ?? '-';

            //         let liabilityName = item.liabilityname ?? '-';
            //         let liabilityDesignation = item.liabilitydesignation ?? '-';
            //         let retirementMonth = item.retirementmonth ?? '';
            //         let retirementYear = item.retirementyear ?? '';
            //         let retirementDisplay = '-';

            //         if (retirementMonth || retirementYear) {
            //             retirementDisplay =
            //                 `${retirementMonth}${retirementMonth && retirementYear ? ' - ' : ''}${retirementYear}`;
            //         }
            //         let retiredFlag = item.retiredflag === 'Y' ? 'Most Urgent Retirement' : 'Normal';

            //         tbody.append(`
            //                 <tr>
            //                     <td>${index + 1}</td>
            //                     <td>${noTypeLabel}</td>
            //                     <td>${liabilityName}</td>
            //                     <td>${liabilityDesignation}</td>
            //                     <td class="text-end">${amount}</td>
            //                    <td>${retirementDisplay}</td>
            //                     <td>${retiredFlag}</td>
            //                 </tr>
            //             `);
            //     });

            //     $('#liability_section').show();
            // }




            function datepicker(value, setdate) {
                var today = new Date();

                var maxDate = new Date(today);

                var minDate = new Date(today);
                var workingDays = 0;

                while (workingDays < 10) {
                    minDate.setDate(minDate.getDate() - 1);

                    var day = minDate.getDay();
                    if (day !== 0 && day !== 6) {
                        workingDays++;
                    }
                }

                var minDateString = formatDate(minDate);
                var maxDateString = formatDate(maxDate);

                init_datepicker(value, minDateString, maxDateString, setdate);
            }
        </script>
@endsection
