@section('content')
    @extends('index2')
    @include('common.alert')

    <style>
        #data_verification_tabs .nav-tabs {
            border-bottom: 0;
            gap: 10px;
        }

        #data_verification_tabs .nav-tabs .nav-link {
            border: 2px solid #5a6174;
            color: #5a6174;
            font-weight: bold;
            margin: 0 5px;
            min-width: 180px;
            text-align: center;
        }

        #data_verification_tabs .nav-tabs .nav-link.active {
            border: 2px solid #4973e8;
            color: #fff;
            background-color: #4973e8;
        }

        #dataVerificationTabContent {
            border: 0 !important;
        }

        #institution_details_table .institution-details-cell {
            white-space: normal;
            word-break: break-word;
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_filter {
            float: none !important;
            text-align: right !important;
            display: flex !important;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_filter label {
            margin-bottom: 0;
            margin-left: 0;
            align-items: center;
            gap: 6px;
        }

        .dt-toolbar-left {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-height: 38px;
        }

        .dt-download-btn {
            background: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
            font-weight: 600;
            border-radius: 7px;
            padding: 10px 15px;
            line-height: 1.2;
            font-size: 14px;
        }

        .dt-download-icon::before {
            content: "\f019";
            font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", FontAwesome;
            font-weight: 900;
            margin-right: 6px;
        }

        .download-required-note {
            color: #dc3545;
            font-size: 13px;
            font-weight: 700;
            margin-top: 8px;
        }

        #pending_transaction_details_table thead th {
            background-color: #e9ecef !important;
            color: #212529;
        }

        .verification-confirm-counts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .verification-confirm-count {
            border: 1px solid #d7dce5;
            background: #f8f9fb;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
        }

        .verification-confirm-count strong {
            display: block;
            color: #495057;
            margin-bottom: 4px;
        }

        .verification-confirm-count span {
            font-size: 22px;
            font-weight: 700;
            color: #0d6efd;
        }

        .verification-confirm-step {
            border: 1px solid #d7dce5;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #fff;
        }

        .verification-confirm-step.form-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding-left: 14px;
        }

        .verification-confirm-step .form-check-input {
            margin-left: 0;
            margin-top: 3px;
            flex: 0 0 auto;
        }

        .verification-confirm-step .form-check-label {
            margin: 0;
        }

        .verification-confirm-step.final-warning {
            border-color: #dc3545;
            background: #fff5f5;
        }

        #data_verification_verified_wrap {
            border: 1px solid #badbcc;
            background: #f8fff9;
            border-radius: 6px;
            padding: 14px 18px;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
        }

        .verified-summary-row {
            padding: 5px 0;
            color: #2f3b45;
            font-size: 15px;
        }

        .verified-summary-row strong {
            color: #198754;
            margin-right: 6px;
        }

        .verified-summary-row span {
            font-weight: 600;
        }

        .verified-status-row {
            color: #198754;
            font-size: 17px;
            font-weight: 700;
        }

        .verified-proceed-btn {
            margin-top: 6px;
            min-width: 190px;
        }

        .finalize-action-card {
            border: 1px solid #b6d4fe;
            border-left: 6px solid #0d6efd;
            background: #f4f8ff;
            border-radius: 8px;
            padding: 16px 18px;
            width: 100%;
            max-width: none;
            margin-left: 0;
            margin-right: auto;
        }

        .finalize-action-card-title {
            color: #0b5ed7;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: left;
        }

        .finalize-action-card .form-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            text-align: left;
            margin: 0;
        }

        .finalize-action-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .finalize-action-card .form-check-input {
            margin-left: 0;
            margin-top: 3px;
            flex: 0 0 auto;
            cursor: pointer;
        }

        .finalize-action-card .form-check-label {
            font-weight: 600;
            color: #344054;
            /* cursor: pointer; */
        }

        .finalize-action-card .form-check-label small {
            display: block;
            font-weight: 500;
            color: #667085;
            margin-top: 3px;
        }

        .finalize-action-card .btn {
            min-width: 130px;
            flex: 0 0 auto;
        }



    </style>
 <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <div class="container-fluid">
        <div class="card card_border">
            <div class="card-header card_header_color">
                Data Verification{{ !empty($dataVerificationFilters['planname'] ?? '') ? ' - ' . $dataVerificationFilters['planname'] : '' }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="verification_deptcode">Department</label>
                        <select class="form-select select2" id="verification_deptcode" name="deptcode">
                            <option value="{{ $dataVerificationFilters['deptcode'] ?? '' }}">
                                {{ $dataVerificationOptions['department']->deptelname ?? ($dataVerificationFilters['deptcode'] ?? 'Select Department') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="verification_regioncode">Region</label>
                        <select class="form-select select2" id="verification_regioncode" name="regioncode">
                            <option value="{{ $dataVerificationFilters['regioncode'] ?? '' }}">
                                {{ $dataVerificationOptions['region']->regionename ?? ($dataVerificationFilters['regioncode'] ?? 'Select Region') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="verification_distcode">District</label>
                        <select class="form-select select2" id="verification_distcode" name="distcode">
                            <option value="{{ $dataVerificationFilters['distcode'] ?? '' }}">
                                {{ $dataVerificationOptions['district']->distename ?? ($dataVerificationFilters['distcode'] ?? 'Select District') }}
                            </option>
                        </select>
                    </div>
                </div>

                <input type="hidden" id="verification_planmappingid"
                    value="{{ $dataVerificationFilters['planmappingid'] ?? '' }}">
                <input type="hidden" id="verification_planname" value="{{ $dataVerificationFilters['planname'] ?? '' }}">
                <input type="hidden" id="verification_quartercode"
                    value="{{ $dataVerificationFilters['quartercode'] ?? '' }}">
                <input type="hidden" id="verification_prioritycode"
                    value="{{ $dataVerificationFilters['prioritycode'] ?? '' }}">

                <div id="data_verification_tabs" class="mt-3" style="display:none;">
                    <ul class="nav nav-tabs justify-content-center" id="dataVerificationTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user_details_tab" data-bs-toggle="tab"
                                data-bs-target="#user_details_panel" type="button" role="tab">
                                User Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="institution_details_tab" data-bs-toggle="tab"
                                data-bs-target="#institution_details_panel" type="button" role="tab">
                                Institution Details
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="dataVerificationTabContent">
                        <div class="tab-pane fade show active" id="user_details_panel" role="tabpanel">
                            <table class="table w-100 table-striped table-bordered display datatables-basic"
                                id="user_details_table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Field Audit</th>
                                        <th>Designation</th>
                                        <th>Disability Details</th>
                                    </tr>
	                                </thead>
	                                <tbody></tbody>
	                            </table>
                                <div class="download-required-note" id="user_details_download_note"
                                    style="display:none;">
                                    * Please download User Details before Submit.
                                </div>
	                        </div>
                        <div class="tab-pane fade" id="institution_details_panel" role="tabpanel">
                            @php
                                $dataVerificationPriorityCode = trim((string) ($dataVerificationFilters['prioritycode'] ?? ''));
                                $showDataVerificationPriority = $dataVerificationPriorityCode !== '' &&
                                    !in_array(strtolower($dataVerificationPriorityCode), ['null', 'undefined', "''"], true);
                            @endphp
                            <table class="table w-100 table-striped table-bordered display datatables-basic"
                                id="institution_details_table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Institution Details</th>
                                        <th>Team Size</th>
                                        <th>Mandays</th>
                                        <th>Distance</th>
                                        @if ($showDataVerificationPriority)
                                            <th>Priority</th>
                                        @endif
                                        <th>Quarter</th>
                                        <th>Audit Mode</th>
                                    </tr>
	                                </thead>
	                                <tbody></tbody>
	                            </table>
                                <div class="download-required-note" id="institution_details_download_note"
                                    style="display:none;">
                                    * Please download Institution Details before Submit.
                                </div>
	                        </div>
	                    </div>
		                    <div class="text-center mt-3" id="data_verification_verified_wrap" style="display:none;">
		                        <div class="verified-summary-row verified-status-row">&#10003; Data Verified</div>
		                        <div class="verified-summary-row"><strong>Verified By:</strong><span id="data_verification_verified_by">-</span></div>
		                        <div class="verified-summary-row"><strong>Verified On:</strong><span id="data_verification_verified_on">-</span></div>
                                <div class="verified-summary-row">
                                <a href="/verification" class="btn btn-primary verified-proceed-btn">
                                    Proceed to Auditplan
                                </a>
                                </div>
		                    </div>
		                    <div class="text-center mt-3" id="data_verification_action_wrap">
		                        <button type="button" class="btn btn-danger" id="save_user_draft_btn">
		                            Submit
		                        </button>
		                        <button type="button" class="btn btn-danger" id="save_institution_draft_btn"
		                            style="display:none;">
		                            Submit
		                        </button>
		                        <div class="finalize-action-card mt-3" id="data_verification_ready_wrap"
		                            style="display:none !important;">
		                            <div class="finalize-action-card-title">Data Confirmation</div>
		                            <div class="finalize-action-content">
		                                <div class="form-check">
		                                    <input class="form-check-input border border-dark cursor-pointer" type="checkbox"
		                                        id="data_verification_ready_checkbox">
		                                    <label class="form-check-label" for="data_verification_ready_checkbox">
		                                        Verified both User  and Institution Details and Downloaded the Report.
		                                        {{-- <small>Please confirm the saved draft data before finalizing.</small> --}}
		                                    </label>
		                                </div>
		                                <button type="button" class="btn btn-success" id="data_verification_finalize_btn"
		                                    style="display:none;">
		                                    Finalize
		                                </button>
		                            </div>
		                        </div>
		                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let userTable = null;
            let institutionTable = null;
            let userDraftSaved = false;
            let institutionDraftSaved = false;
            const downloadedTables = {
                users: false,
                institutions: false,
                pendingTransactions: false
            };
            const dataVerificationFrozen = @json($dataVerificationFrozen ?? false);
            const dataVerificationFilters = @json($dataVerificationFilters ?? []);
            const priorityCode = String(dataVerificationFilters.prioritycode ?? '').trim();
            const showPriorityColumn = !!priorityCode && !['null', 'undefined', "''"].includes(priorityCode.toLowerCase());

            $('.select2').select2({
                width: '100%'
            });

            if (dataVerificationFrozen) {
                $('#verification_deptcode, #verification_regioncode, #verification_distcode')
                    .prop('disabled', true)
                    .trigger('change.select2');
            }

            function esc(value) {
                return $('<div>').text(value ?? '').html();
            }

            function cellValue(value) {
                if (value === null || value === undefined || String(value).trim() === '') {
                    return '-';
                }

                return esc(value);
            }

            function showDataVerificationAlert(title, message) {
                const redoPlanMessage = String(message || '').trim();
                const shouldRedirectToChecklist =
                    redoPlanMessage === 'Details are updated, so please redo the plan.' ||
                    redoPlanMessage === 'Data Updated Please Redo the Plan.';

                if (typeof passing_alert_value === 'function') {
                    passing_alert_value(title, message, 'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');

                    if (shouldRedirectToChecklist) {
                        $('#close_button, #cancel_button, #process_button').hide();
                        $('#ok_button')
                            .html('OK')
                            .show()
                            .off('click.dataVerificationRedo')
                            .on('click.dataVerificationRedo', function(e) {
                                e.preventDefault();
                                window.location.href = '/checklistplan';
                            });
                    }
                    return;
                }

                alert(message);
                if (shouldRedirectToChecklist) {
                    window.location.href = '/checklistplan';
                }
            }

            function showDataVerificationLoader() {
                if (typeof showLoader === 'function') {
                    showLoader();
                }
            }

            function hideDataVerificationLoader() {
                if (typeof hideLoader === 'function') {
                    hideLoader();
                }
            }

            function updateFinalizeButton(status) {
                if (status) {
                    userDraftSaved = !!status.users;
                    institutionDraftSaved = !!status.institutions;
                }

                if (userDraftSaved && institutionDraftSaved) {
                    $('#data_verification_ready_wrap').attr('style', '');
                } else {
                    $('#data_verification_ready_wrap').attr('style', 'display:none !important;');
                    $('#data_verification_ready_checkbox').prop('checked', false);
                    $('#data_verification_finalize_btn').hide();
                }
            }

            function updateActiveDraftButton() {
                if ($('#data_verification_verified_wrap').is(':visible')) {
                    $('#data_verification_action_wrap').hide();
                    return;
                }

                $('#data_verification_action_wrap').show();

                if ($('#institution_details_panel').hasClass('active')) {
                    $('#save_user_draft_btn').hide();
                    $('#save_institution_draft_btn').show();
                } else {
                    $('#save_institution_draft_btn').hide();
                    $('#save_user_draft_btn').show();
                }

                updateFinalizeButton();
            }

            function updateDataVerificationVerifiedState(isFinalized, meta = {}) {
                if (isFinalized) {
                    $('#data_verification_action_wrap').hide();
                    $('#data_verification_verified_by').text(meta.verifiedby_name || meta.verifiedby || '-');
                    $('#data_verification_verified_on').text(meta.verifiedon || '-');
                    $('#data_verification_verified_wrap').show();
                    return;
                }

                $('#data_verification_verified_wrap').hide();
                updateActiveDraftButton();
            }

            function fillDropdown(dropdown, placeholder, rows, valueKey, textKey, selectedValue) {
                let options = `<option value="">${esc(placeholder)}</option>`;

                (rows || []).forEach(function(row) {
                    const value = row[valueKey] || '';
                    const selected = String(value) === String(selectedValue || '') ? 'selected' : '';
                    options += `<option value="${esc(value)}" ${selected}>${esc(row[textKey] || '')}</option>`;
                });

                dropdown.html(options).trigger('change.select2');
            }

            function setDropdownLocked(dropdown, locked) {
                dropdown.prop('disabled', !!locked).trigger('change.select2');
            }

            function fetchVerificationField(field, payload = {}) {
                return $.ajax({
                    url: "{{ route('instchange.fieldloader') }}",
                    type: 'POST',
                    data: Object.assign({
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        field: field
                    }, payload)
                });
            }

            function destroyTable(tableId, tableInstance) {
                if (tableInstance && $.fn.DataTable.isDataTable(tableId)) {
                    tableInstance.destroy();
                }
                $(tableId + ' tbody').empty();
                return null;
            }

            function exportDataTableToExcel(tableSelector, fileBaseName) {
                if (!tableSelector || !$(tableSelector).length) return false;
                const hasDataTable = $.fn.DataTable.isDataTable(tableSelector);
                const dt = hasDataTable ? $(tableSelector).DataTable() : null;
                const headerCells = [];
                $(`${tableSelector} thead th`).each(function() {
                    headerCells.push($(this).text().trim());
                });

                const csvEscape = (value) => {
                    const text = String(value ?? '').replace(/"/g, '""');
                    return `"${text}"`;
                };

                const csvRows = [];
                csvRows.push(headerCells.map(csvEscape).join(','));

                const appendRow = function(rowNode) {
                    const rowCells = [];
                    $(rowNode).find('td').each(function() {
                        rowCells.push($(this).text().trim());
                    });
                    csvRows.push(rowCells.map(csvEscape).join(','));
                };

                if (hasDataTable) {
                    dt.rows({
                        search: 'applied'
                    }).every(function() {
                        appendRow(this.node());
                    });
                } else {
                    $(`${tableSelector} tbody tr`).each(function() {
                        appendRow(this);
                    });
                }

                const csvContent = '\ufeff' + csvRows.join('\r\n');
                const anchor = document.createElement('a');
                anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
                anchor.download = `${fileBaseName}.csv`;
                document.body.appendChild(anchor);
                anchor.click();
                document.body.removeChild(anchor);
                return true;
            }

            function attachDownloadButtonToFilter(tableSelector, btnId, fileBaseName, retry = 0) {
                const wrapper = $(tableSelector).closest('.dataTables_wrapper');
                const filter = wrapper.find('.dataTables_filter');
                const topRow = wrapper.children('.row').first();

                if (!filter.length || !wrapper.length || !topRow.length) {
                    if (retry < 8) {
                        setTimeout(function() {
                            attachDownloadButtonToFilter(tableSelector, btnId, fileBaseName, retry + 1);
                        }, 120);
                    }
                    return;
                }

                if (wrapper.find(`#${btnId}`).length) return;

                const btn =
                    `<button type="button" class="btn btn-sm dt-download-btn" id="${btnId}" data-table="${tableSelector}" data-file="${fileBaseName}"><span class="dt-download-icon"></span>Download</button>`;

                let toolbarLeft = wrapper.find('.dt-toolbar-left').first();
                if (!toolbarLeft.length) {
                    toolbarLeft = $('<div class="dt-toolbar-left"></div>');
                    topRow.children('div').first().empty().append(toolbarLeft);
                }

                toolbarLeft.prepend(btn);
            }

            function hideDownloadNotes() {
                $('#user_details_download_note, #institution_details_download_note, #pending_transaction_download_note')
                    .hide();
            }

            function resetDownloadValidation() {
                downloadedTables.users = false;
                downloadedTables.institutions = false;
                downloadedTables.pendingTransactions = false;
                hideDownloadNotes();
            }

            function markTableDownloaded(tableSelector) {
                if (tableSelector === '#user_details_table') {
                    downloadedTables.users = true;
                    $('#user_details_download_note').hide();
                } else if (tableSelector === '#institution_details_table') {
                    downloadedTables.institutions = true;
                    $('#institution_details_download_note').hide();
                } else if (tableSelector === '#pending_transaction_details_table') {
                    downloadedTables.pendingTransactions = true;
                    $('#pending_transaction_download_note').hide();
                }
            }

            function validateDraftDownload(saveType) {
                if (saveType === 'users' && !downloadedTables.users) {
                    $('#user_details_download_note').show();
                    return false;
                }

                if (saveType === 'institutions' && !downloadedTables.institutions) {
                    $('#institution_details_download_note').show();
                    return false;
                }

                return true;
            }

            function restoreLargeModalCloseButtons() {
                $('#large_modal_cancel_button, #large_confirmation_button_close')
                    .attr('data-bs-dismiss', 'modal')
                    .off('click.pendingDownload');
            }

            function resetDetails() {
                $('#data_verification_tabs').hide();
                userTable = destroyTable('#user_details_table', userTable);
                institutionTable = destroyTable('#institution_details_table', institutionTable);
                resetDownloadValidation();
            }

            function resetRegions() {
                fillDropdown($('#verification_regioncode'), 'Select Region', [], 'regioncode', 'regionename', '');
                setDropdownLocked($('#verification_regioncode'), false);
                resetDistricts();
            }

            function resetDistricts() {
                fillDropdown($('#verification_distcode'), 'Select District', [], 'distcode', 'distename', '');
                setDropdownLocked($('#verification_distcode'), false);
                resetDetails();
            }

            function renderUserRows(rows) {
                const body = $('#user_details_table tbody');
                body.empty();

                function disabilityCell(row) {
                    if (row.disability !== 'Y') {
                        return 'No';
                    }

                    const rate = row.disabilityrate !== null && row.disabilityrate !== undefined &&
                        row.disabilityrate !== '' ? `${esc(row.disabilityrate)}%` : '-';
                    const type = cellValue(row.disability_ename);

                    return `<div>Yes</div>
                        <div><strong>Rate:</strong> ${rate}</div>
                        <div><strong>Type:</strong> ${type}</div>`;
                }

                (rows || []).forEach(function(row, index) {
	                    body.append(`
	                        <tr data-deptuserid="${esc(row.deptuserid)}">
	                            <td>${index + 1}</td>
	                            <td>${cellValue(row.username)}</td>
	                            <td>${cellValue(row.gender_label)}</td>
	                            <td>${cellValue(row.reservelist_label)}</td>
	                            <td>${cellValue(row.desigelname)}</td>
	                            <td>${disabilityCell(row)}</td>
	                        </tr>
                    `);
                });
            }

            function renderInstitutionRows(rows) {
                const body = $('#institution_details_table tbody');
                body.empty();

                function institutionCell(row) {
                    const spilloverBadge = row.spillover === 'Y'
                        ? ' <span class="badge bg-warning text-dark ms-1">Spillover</span>'
                        : '';

                    return `<div><strong>Institution:</strong> ${cellValue(row.instename)}${spilloverBadge}</div>
                        <div><strong>Category:</strong> ${cellValue(row.catename)}</div>
                        <div><strong>Subcategory:</strong> ${cellValue(row.subcatename)}</div>`;
                }

                (rows || []).forEach(function(row, index) {
                    const priorityColumn = showPriorityColumn ?
                        `<td>${cellValue(row.priority_label)}</td>` : '';

	                    body.append(`
	                        <tr data-instid="${esc(row.instid)}">
	                            <td>${index + 1}</td>
		                            <td class="institution-details-cell">${institutionCell(row)}</td>
	                            <td>${cellValue(row.teamsize)}</td>
                            <td>${cellValue(row.mandays)}</td>
                            <td>${cellValue(row.distance)}</td>
                            ${priorityColumn}
                            <td>${cellValue(row.quartercode)}</td>
                            <td>${cellValue(row.auditmode_label)}</td>
                        </tr>
                    `);
                });
            }

            function loadDataVerificationDetails() {
                const deptcode = $('#verification_deptcode').val();
                const regioncode = $('#verification_regioncode').val();
                const distcode = $('#verification_distcode').val();

                resetDetails();

                if (!deptcode || !regioncode || !distcode) {
                    return;
                }

                $.ajax({
                    url: "{{ route('data.verification.details') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        deptcode: deptcode,
                        regioncode: regioncode,
                        distcode: distcode,
                        planmappingid: $('#verification_planmappingid').val(),
                        planname: $('#verification_planname').val(),
                        quartercode: $('#verification_quartercode').val(),
                        prioritycode: $('#verification_prioritycode').val()
                    },
                    success: function(response) {
                        renderUserRows(response.users || []);
                        renderInstitutionRows(response.institutions || []);
                        updateFinalizeButton(response.draft_status || {});
                        updateDataVerificationVerifiedState(response.is_finalized, response.verification_meta || {});

                        $('#data_verification_tabs').show();
                        userTable = $('#user_details_table').DataTable({
                            paging: true,
                            pagingType: 'simple_numbers',
	                            searching: true,
		                            info: true,
		                            lengthChange: false,
		                            pageLength: 10,
		                            ordering: true,
                                    order: [],
		                            autoWidth: false,
		                            initComplete: function() {
		                                attachDownloadButtonToFilter('#user_details_table',
		                                    'user_details_download_btn', 'User Details');
		                            }
	                        });
	                        institutionTable = $('#institution_details_table').DataTable({
	                            paging: true,
	                            pagingType: 'simple_numbers',
	                            searching: true,
		                            info: true,
		                            lengthChange: false,
		                            pageLength: 10,
		                            ordering: true,
                                    order: [],
		                            autoWidth: false,
		                            initComplete: function() {
		                                attachDownloadButtonToFilter('#institution_details_table',
		                                    'institution_details_download_btn', 'Institution Details');
	                            }
	                        });
                    }
                });
            }

            function saveDataVerificationDraft(saveType, button) {
                if (!validateDraftDownload(saveType)) {
                    return;
                }

                const btn = $(button);
                const originalText = btn.text();

                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('data.verification.save_draft') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        save_type: saveType,
                        deptcode: $('#verification_deptcode').val(),
                        regioncode: $('#verification_regioncode').val(),
                        distcode: $('#verification_distcode').val(),
                        planmappingid: $('#verification_planmappingid').val(),
                        planname: $('#verification_planname').val(),
                        quartercode: $('#verification_quartercode').val(),
                        prioritycode: $('#verification_prioritycode').val()
                    },
                    success: function(response) {
                        updateFinalizeButton(response.draft_status || {});
                        showDataVerificationAlert('Confirmation', response.message ||
                            'Draft saved successfully.');
                    },
                    error: function(xhr) {
                        showDataVerificationAlert('Confirmation', xhr.responseJSON?.message || xhr
                            .responseText || 'Draft save failed.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text(originalText);
                    }
                });
            }

            $('#save_user_draft_btn').on('click', function() {
                saveDataVerificationDraft('users', this);
            });

            $('#save_institution_draft_btn').on('click', function() {
                saveDataVerificationDraft('institutions', this);
            });

            $('#data_verification_ready_checkbox').on('change', function() {
                $('#data_verification_finalize_btn').toggle(this.checked);
            });

            $('#data_verification_finalize_btn').on('click', function() {
                showDataVerificationPendingTransactions();
            });

            function showDataVerificationPendingTransactions() {
                showDataVerificationLoader();

                $.ajax({
                    url: "{{ route('data.verification.pending_transactions') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        deptcode: dataVerificationFilters.deptcode || '',
                        regioncode: dataVerificationFilters.regioncode || '',
                        distcode: dataVerificationFilters.distcode || '',
                        planmappingid: dataVerificationFilters.planmappingid || '',
                        quartercode: dataVerificationFilters.quartercode || '',
                        prioritycode: dataVerificationFilters.prioritycode || ''
                    },
                    success: function(response) {
                        if ((response.transactions || []).length) {
                            renderDataVerificationPendingTransactionsPopup(response.transactions || []);
                            return;
                        }

                        renderDataVerificationFinalizeConfirmPopup(response.draft_status || {});
                    },
                    error: function(xhr) {
                        showDataVerificationAlert('Confirmation', xhr.responseJSON?.message || xhr
                            .responseText || 'Unable to fetch pending transaction details.');
                    },
                    complete: function() {
                        hideDataVerificationLoader();
                    }
                });
            }

            function renderDataVerificationPendingTransactionsPopup(rows) {
                const tableRows = (rows || []).map(function(row, index) {
                    return `<tr>
                        <td>${index + 1}</td>
                        <td>${cellValue(row.username)} (${cellValue(row.designation)})</td>

                        <td>${cellValue(row.ifhrmsno)}</td>
                        <td>${cellValue(row.fromdepartment)}</td>
                        <td>${cellValue(row.todepartment)}</td>
                        <td>${cellValue(row.fromdistrict)}</td>
                        <td>${cellValue(row.todistrict)}</td>
                        <td>${cellValue(row.transactiontype)}</td>
                        <td class="text-nowrap">${cellValue(row.orderdate)}</td>
                        <td>${cellValue(row.orderno)}</td>
                        <td>${cellValue(row.pending_at)}</td>

                        <td>${cellValue(row.status)}</td>

                    </tr>`;
                }).join('');

                const content =
                    `<div>
                        <table class="table table-striped table-bordered w-100 display datatables-basic" id="pending_transaction_details_table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Username</th>

                                    <th>Ifhrmsno</th>
                                    <th>From Department</th>
                                    <th>To Department</th>
                                    <th>From District</th>
                                    <th>To District</th>
                                    <th>Transaction Type</th>
                                    <th class="text-nowrap">Order Date</th>
                                    <th>Order No</th>
                                    <th>Pending At</th>

                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                    <div class="alert alert-danger border border-danger text-center fw-bold mt-3 mb-0">
                        Please make transaction details nil before finalize.
                    </div>
                    <div class="download-required-note text-center" id="pending_transaction_download_note"
                        style="display:none;">
                        * Please download Pending Transaction Details before Close.
                    </div>`;

                downloadedTables.pendingTransactions = false;
                passing_large_alert('Pending Transaction Details', content, 'large_confirmation_alert',
                    'large_alert_header', 'large_alert_body', 'forward_alert');
                $('#large_confirmation_alert .modal-dialog').css({
                    'max-width': '1000px',
                    'width': '78%'
                });
                $('#large_modal_process_button').off('click').hide();
                $('#large_modal_cancel_button')
                    .html('Close')
                    .show()
                    .removeAttr('data-bs-dismiss')
                    .off('click.pendingDownload')
                    .on('click.pendingDownload', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (!downloadedTables.pendingTransactions) {
                            $('#pending_transaction_download_note').show();
                            return;
                        }

                        restoreLargeModalCloseButtons();
                        $('#large_confirmation_alert').modal('hide');
                    });
                $('#large_confirmation_button_close')
                    .removeAttr('data-bs-dismiss')
                    .off('click.pendingDownload')
                    .on('click.pendingDownload', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (!downloadedTables.pendingTransactions) {
                            $('#pending_transaction_download_note').show();
                            return;
                        }

                        restoreLargeModalCloseButtons();
                        $('#large_confirmation_alert').modal('hide');
                    });

                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#pending_transaction_details_table')) {
                        $('#pending_transaction_details_table').DataTable().destroy();
                    }

                    $('#pending_transaction_details_table').DataTable({
                        paging: true,
                        pagingType: 'simple_numbers',
                        searching: true,
                        info: true,
                        lengthChange: false,
                        pageLength: 10,
                        ordering: true,
                        order: [],
                        columnDefs: [{
                            targets: 0,
                            orderable: false
                        }],
                        autoWidth: false,
                        scrollX: true,
                        initComplete: function() {
                            attachDownloadButtonToFilter('#pending_transaction_details_table',
                                'pending_transaction_download_btn', 'Pending Transaction Details');
                        }
                    });
                }, 200);
            }

            function renderDataVerificationFinalizeConfirmPopup(status) {
                const userCount = status.user_saved_count ?? status.user_expected_count ?? 0;
                const institutionCount = status.institution_saved_count ?? status.institution_expected_count ?? 0;
                const content = `
                    <div class="verification-confirm-counts">
                        <div class="verification-confirm-count">
                            <strong>User Details</strong>
                            <span>${cellValue(userCount)}</span>
                        </div>
                        <div class="verification-confirm-count">
                            <strong>Institution Details</strong>
                            <span>${cellValue(institutionCount)}</span>
                        </div>
                    </div>
                    <div class="form-check verification-confirm-step">
                        <input class="form-check-input data-verification-popup-check ml-3 border border-dark cursor-pointer" type="checkbox"
                            id="popup_user_details_verified">
                        <label class="form-check-label" for="popup_user_details_verified">
                            I have verified the User Details being used in Auditplan.
                        </label>
                    </div>
                    <div class="form-check verification-confirm-step">
                        <input class="form-check-input data-verification-popup-check border border-dark cursor-pointer" type="checkbox"
                            id="popup_institution_details_verified">
                        <label class="form-check-label" for="popup_institution_details_verified">
                            I have verified the Institution Details being used in Auditplan.
                        </label>
                    </div>
                    <div class="form-check verification-confirm-step final-warning mb-0">
                        <input class="form-check-input border border-dark cursor-pointer" type="checkbox"
                            id="popup_finalize_not_revokable" disabled>
                        <label class="form-check-label" for="popup_finalize_not_revokable">
                            I understand that details cannot be updated after the finalization.
                        </label>
                    </div>`;

                passing_large_alert('Data Verification', content, 'large_confirmation_alert',
                    'large_alert_header', 'large_alert_body', 'forward_alert');
                $('#large_confirmation_alert .modal-dialog').css({
                    'max-width': '720px',
                    'width': '95%'
                });
                $('#large_modal_process_button')
                    .html('Finalize')
                    .prop('disabled', true)
                    .show()
                    .removeAttr('data-bs-dismiss');
                $('#large_modal_cancel_button')
                    .html('Cancel')
                    .show()
                    .attr('data-bs-dismiss', 'modal')
                    .off('click.pendingDownload');
                $('#large_confirmation_button_close')
                    .attr('data-bs-dismiss', 'modal')
                    .off('click.pendingDownload');

                $(document).off('change', '.data-verification-popup-check').on('change',
                    '.data-verification-popup-check',
                    function() {
                        const bothChecked = $('#popup_user_details_verified').is(':checked') &&
                            $('#popup_institution_details_verified').is(':checked');
                        $('#popup_finalize_not_revokable')
                            .prop('disabled', !bothChecked)
                            .prop('checked', false);
                        $('#large_modal_process_button').prop('disabled', true);
                    });

                $(document).off('change', '#popup_finalize_not_revokable').on('change',
                    '#popup_finalize_not_revokable',
                    function() {
                        $('#large_modal_process_button').prop('disabled', !this.checked);
                    });

                $('#large_modal_process_button').off('click').on('click', function(e) {
                    e.preventDefault();
                    $('#large_confirmation_alert').modal('hide');
                    sendDataVerificationFinalizeOtp();
                });
            }

            function sendDataVerificationFinalizeOtp() {
                $('#process_button').prop('disabled', true);
                showDataVerificationLoader();

                $.ajax({
                    url: "{{ route('data.verification.send_otp') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const ok = response && (response.status === 'success' || response.success === true);

                        if (ok) {
                            renderDataVerificationOtpPopup();
                            return;
                        }

                        $('#process_button').prop('disabled', false);
                        showDataVerificationAlert('Confirmation', response.message ||
                            'Failed to send OTP. Please try again later.');
                    },
                    error: function() {
                        $('#process_button').prop('disabled', false);
                        showDataVerificationAlert('Confirmation', 'Failed to send OTP.');
                    },
                    complete: function() {
                        hideDataVerificationLoader();
                    }
                });
            }

            function renderDataVerificationOtpPopup() {
                const otpContent = `
                    <div id="otp_div" class="text-center">
                        <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                        <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mail</span>
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
                                <button type="button" id="verify_data_verification_otp_button" class="btn btn-primary w-100">Verify OTP</button>
                            </div>
                        </div>
                        <br>
                        <small class="text-center mb-3 d-block">Didn't receive the code?
                            <b id="resend_data_verification_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b>
                        </small>
                    </div>`;

                passing_alert_value('Confirmation', otpContent, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                $('#process_button, #ok_button').hide();
                $('#cancel_button, #close_button').show();

                setTimeout(function() {
                    $('.otp-input').first().focus();
                }, 200);

                $(document).off('click', '#verify_data_verification_otp_button').on('click',
                    '#verify_data_verification_otp_button',
                    function() {
                        if ($(this).prop('disabled')) return;
                        $(this).prop('disabled', true);
                        verifyDataVerificationFinalizeOtp();
                    });

                $(document).off('click', '#resend_data_verification_otp_link').on('click',
                    '#resend_data_verification_otp_link',
                    function(e) {
                        e.preventDefault();
                        sendDataVerificationFinalizeOtp();
                    });

                $(document).off('input', '.otp-input').on('input', '.otp-input', function() {
                    const val = $(this).val().replace(/[^0-9]/g, '').slice(0, 1);
                    $(this).val(val);
                    if (val) {
                        $(this).next('.otp-input').focus();
                    }
                });

                $(document).off('keydown', '.otp-input').on('keydown', '.otp-input', function(e) {
                    if (e.key === 'Backspace' && !$(this).val()) {
                        $(this).prev('.otp-input').focus();
                    }
                });
            }

            function verifyDataVerificationFinalizeOtp() {
                let otp = '';
                $('.otp-input').each(function() {
                    otp += $(this).val();
                });

                if (!/^\d{6}$/.test(otp)) {
                    alert('Please enter a valid 6-digit OTP.');
                    $('#verify_data_verification_otp_button').prop('disabled', false);
                    return;
                }

                showDataVerificationLoader();

                $.ajax({
                    url: "{{ route('data.verification.verify_otp') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        otp: otp
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            finalizeDataVerificationRows();
                            return;
                        }

                        alert(response.message || 'Incorrect OTP.');
                        $('#verify_data_verification_otp_button').prop('disabled', false);
                    },
                    error: function() {
                        alert('OTP verification failed.');
                        $('#verify_data_verification_otp_button').prop('disabled', false);
                    },
                    complete: function() {
                        hideDataVerificationLoader();
                    }
                });
            }

            function finalizeDataVerificationRows() {
                showDataVerificationLoader();

                $.ajax({
                    url: "{{ route('data.verification.finalize') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        deptcode: $('#verification_deptcode').val(),
                        regioncode: $('#verification_regioncode').val(),
                        distcode: $('#verification_distcode').val(),
                        planmappingid: $('#verification_planmappingid').val(),
                        planname: $('#verification_planname').val(),
                        quartercode: $('#verification_quartercode').val(),
                        prioritycode: $('#verification_prioritycode').val()
                    },
                    success: function(response) {
                        userDraftSaved = false;
                        institutionDraftSaved = false;
                        updateFinalizeButton({
                            users: false,
                            institutions: false
                        });
                        loadDataVerificationDetails();
                        showDataVerificationAlert('Confirmation', response.message ||
                            'Data finalized successfully.');
                    },
                    error: function(xhr) {
                        showDataVerificationAlert('Confirmation', xhr.responseJSON?.message || xhr
                            .responseText || 'Finalize failed.');
                        $('#verify_data_verification_otp_button').prop('disabled', false);
                    },
                    complete: function() {
                        hideDataVerificationLoader();
                    }
                });
            }

            function loadDepartments() {
                return fetchVerificationField('department').then(function(response) {
                    const selected = response.selected || {};
                    const locked = response.locked || {};

                    fillDropdown($('#verification_deptcode'), 'Select Department', response.data || [], 'deptcode',
                        'deptelname', selected.deptcode);
                    setDropdownLocked($('#verification_deptcode'), locked.deptcode);

                    if ($('#verification_deptcode').val()) {
                        return loadRegions(selected.regioncode, selected.distcode);
                    }
                });
            }

            function loadRegions(selectedRegionCode = '', selectedDistCode = '') {
                const deptcode = $('#verification_deptcode').val();
                resetRegions();

                if (!deptcode) {
                    return $.Deferred().resolve().promise();
                }

                return fetchVerificationField('region', {
                    deptcode: deptcode
                }).then(function(response) {
                    const locked = response.locked || {};
                    const regionRows = response.data || [];
                    selectedRegionCode = selectedRegionCode || response.selected?.regioncode || '';
                    if (!selectedRegionCode && regionRows.length === 1) {
                        selectedRegionCode = regionRows[0].regioncode || '';
                    }

                    fillDropdown($('#verification_regioncode'), 'Select Region', regionRows, 'regioncode',
                        'regionename', selectedRegionCode);
                    setDropdownLocked($('#verification_regioncode'), locked.regioncode);

                    if ($('#verification_regioncode').val()) {
                        return loadDistricts(selectedDistCode);
                    }
                });
            }

            function loadDistricts(selectedDistCode = '') {
                const deptcode = $('#verification_deptcode').val();
                const regioncode = $('#verification_regioncode').val();
                resetDistricts();

                if (!deptcode || !regioncode) {
                    return $.Deferred().resolve().promise();
                }

                return fetchVerificationField('district', {
                    deptcode: deptcode,
                    regioncode: regioncode
                }).then(function(response) {
                    const locked = response.locked || {};

                    fillDropdown($('#verification_distcode'), 'Select District', response.data || [], 'distcode',
                        'distename', selectedDistCode);
                    setDropdownLocked($('#verification_distcode'), locked.distcode);

                    if ($('#verification_distcode').val()) {
                        loadDataVerificationDetails();
                    }
                });
            }

            $('#verification_deptcode').on('change', function() {
                loadRegions();
            });

            $('#verification_regioncode').on('change', function() {
                loadDistricts();
            });

            $('#verification_distcode').on('change', function() {
                loadDataVerificationDetails();
            });

            $(document).on('click', '.dt-download-btn', function(e) {
                e.preventDefault();
                const tableSelector = $(this).attr('data-table') || $(this).data('table');
                const fileBaseName = $(this).attr('data-file') || $(this).data('file');
                markTableDownloaded(tableSelector);
                if (exportDataTableToExcel(tableSelector, fileBaseName)) {
                    markTableDownloaded(tableSelector);
                }
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
                updateActiveDraftButton();
            });

            if (dataVerificationFrozen) {
                loadDataVerificationDetails();
            } else {
                loadDepartments();
            }
        });
    </script>
@endsection
