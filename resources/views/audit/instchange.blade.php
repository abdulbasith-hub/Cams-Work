@extends('index2')
@section('content')
    @include('common.alert')

    @php
        $pendingStatus = $pendinginstcheck['institutions']->first()->pendinginststatus ?? null;
        $currentquarter = $quarterInfo->current['auditquartercode'] ?? ($quarterInfo->currentquarter ?? 'Q4');
        $nextquarter = $quarterInfo->next['auditquartercode'] ?? ($quarterInfo->nextquarter ?? 'Q1');
        $nextPriorityLabel = $quarterInfo->nextPriorityLabel ?? '';
        $nextQuarterLabel = $nextPriorityLabel ? "{$nextquarter} ({$nextPriorityLabel})" : $nextquarter;
        $spillovermasterCount = (int) ($spillovermasterCount ?? 0);
        // dd($spillovermasterCount);
        $pendingmastercount = (int) ($pendingmastercount ?? 0);
        $pendingTempCount = (int) ($pendingTempCount ?? 0);
        $templatemastercount = (int) ($templateInstCount ?? 0);
        $templateTempCount = (int) ($templateTempCount ?? 0);
        $instChangeFinancialMeta = $instChangeFinancialMeta ?? [];
        $currentFincode = $instChangeFinancialMeta['currentfincode'] ?? null;
        $currentFinancialYear = $instChangeFinancialMeta['currentFinancialYear'] ?? null;
        $currentFinancialYearLabel = $instChangeFinancialMeta['currentFinancialYearLabel'] ?? '';
        $toFincode = $instChangeFinancialMeta['tofincode'] ?? null;
        $toFinancialYear = $instChangeFinancialMeta['toFinancialYear'] ?? null;
        $toFinancialYearLabel = $instChangeFinancialMeta['toFinancialYearLabel'] ?? '';
        $moveToOptions = $instChangeFinancialMeta['moveToOptions'] ?? [];
        $phaseLabel = $quarterInfo->phaseLabel ?? '';
    @endphp

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <script src="../assets/js/jquery.js"></script>

    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <script src="../assets/js/download-button/custom.xl.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <style>
        .nav-tabs .nav-link {
            border: 2px solid #5a6174;
            color: #5a6174;
            font-weight: bold;
            margin: 0 5px;
        }

        .nav-tabs .nav-link.active {
            border: 2px solid #4973e8;
            color: #fff;
            background-color: #4973e8;
        }

        .saved-border {
            border: 2px solid #007bff !important;
        }

        .instchange-fit-table {
            width: 100% !important;
            table-layout: fixed;
        }

        .instchange-fit-table th,
        .instchange-fit-table td {
            white-space: normal !important;
            word-break: break-word;

            vertical-align: top;
        }

        .instchange-fit-table {
            white-space: nowrap !important;
            width: 70px;
            text-align: left !important;
        }

        .sno-col {
            white-space: nowrap !important;
            width: 70px;
            text-align: right !important;
        }

        div.dataTables_wrapper {
            width: 100%;
        }

        #pending_table th:nth-child(1),
        #pending_table td:nth-child(1),
        #template_table th:nth-child(1),
        #template_table td:nth-child(1) {
            width: 70px;
        }

        #pending_table th:nth-child(3),
        #pending_table td:nth-child(3),
        #template_table th:nth-child(3),
        #template_table td:nth-child(3) {
            width: 170px;
            white-space: nowrap;
            text-align: center;
        }

        #pending_table th:nth-child(4),
        #pending_table td:nth-child(4),
        #template_table th:nth-child(4),
        #template_table td:nth-child(4) {
            width: 190px;
            white-space: nowrap;
            text-align: center;
        }

        #pending_table .quarterchange .form-select,
        #template_table .quarterchange .form-select {
            max-width: 140px;
            margin: 0 auto;
        }

        .quarter-year-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .quarter-year-wrap .quarter-select {
            min-width: 100px;
        }

        .quarter-year-wrap .fy-select {
            min-width: 170px;
            background: #f4f6fa;
            color: #5e6677;
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

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 10px !important;
        }

        .dataTables_wrapper .dataTables_paginate .pagination {
            gap: 6px;
            margin: 0;
        }

        .dataTables_wrapper .dataTables_paginate .page-item .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button a {
            border-radius: 6px !important;
            border: 1px solid #b9d2ff !important;
            background: var(--bs-primary) !important;
            color: #fff !important;
            min-width: 36px;
            text-align: center;
            font-weight: 600;
            box-shadow: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button a:hover {
            background: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current a,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled .page-link,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled a,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: var(--bs-primary) !important;
            border-color: #fff !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            border: none !important;
            background: var(--bs-primary) !important;
            color: #0b4fb8 !important;
            min-width: 36px;
            text-align: center;
            font-weight: 600;
            margin-left: 4px !important;
        }

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

        .tab-pane.tab-slide-enter {
            animation: tab-slide-in .35s ease;
        }

        @keyframes tab-slide-in {
            from {
                opacity: 0;
                transform: translateX(32px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

        <input type="hidden" id="pendinginstmappingstatus" value="{{ $pendingStatus }}">
      <div class="row">
    <div class="col-12">
        <div class="card card_border">

            <div class="card-header card_header_color lang" key="">
               Quarter Transition - Spillover and Pending Institutions
            </div>

            <div class="card-body">

                <!-- Dropdown Row -->
                <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required" for="instchange_deptcode">Department</label>
                            <select class="form-select select2" id="instchange_deptcode" name="deptcode">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required" for="instchange_regioncode">Region</label>
                            <select class="form-select select2" id="instchange_regioncode" name="regioncode">
                                <option value="">Select Region</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required" for="instchange_distcode">District</label>
                            <select class="form-select select2" id="instchange_distcode" name="distcode">
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="instchange_result_section" style="display:none;">
                <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                    <li class="nav-item" id="scheduled_plans_tab_item"><button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#scheduled_plans" type="button">Carried Forward</button></li>
                    <li class="nav-item" id="slip_details_tab_item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#slip_details"
                            type="button">Pending in {{ $currentquarter }} @if ($phaseLabel)
                                <small>({{ $phaseLabel }})</small>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" id="template_details_tab_item"><button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#template_details" type="button">Template Audit Pending in
                            {{ $currentquarter }}
                            @if ($phaseLabel)
                                <small>({{ $phaseLabel }})</small>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-3" id="instchange_tab_content" style="display:none;">
            <div class="tab-pane fade show active" id="scheduled_plans">
                <div id="scheduled_plans_content"></div>
            </div>
            <div class="tab-pane fade" id="slip_details">
                <div id="slip_details_content"></div>
            </div>
            <div class="tab-pane fade" id="template_details">
                <div id="template_details_content"></div>
            </div>
        </div>
        <center class="mt-4" id="instchange_finalize_wrap" style="display:none;"><button
                class="btn bg-success button_finalise w-20" id="finalizebuttonaction"
                style="display:none;">Finalize</button>
        </center>
        <div id="instchange_empty_finalize" style="display:none;">
            <div class="form-check d-flex justify-content-center align-items-center gap-2">
                <input type="checkbox" id="confirm_check" class="form-check-input border-dark"
                    style="width:17px;height:17px;cursor:pointer;" />
                <label class="form-check-label mb-0">
                    <h4 class="mb-0" id="instchange_empty_finalize_message">You have No Spillover, Pending, and Template Audit Pending Institutions for {{ $currentquarter }} @if ($phaseLabel)
                            <small>({{ $phaseLabel }})</small>
                        @endif
                    </h4>
                </label>
            </div>
            <center><button class="btn w-20 bg-success button_finalise mt-3" type="button" id="finalize_empty"
                    disabled>Finalize</button></center>
        </div>
        <div class="container mt-4" id="instchange_completed_section" style="display:none;">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 id="instchange_completed_message">All the Spillover and Pending institutions have successfully completed for {{ $currentquarter }}
                        @if ($phaseLabel)
                            <small>({{ $phaseLabel }})</small>
                        @endif
                    </h4>
                </div>
            </div>
        </div>

    <div id="ajax-loader">
        <div class="spinner"></div>
    </div>

    <script>
        const meta = {
            spill: {{ $spillovermasterCount }},
            pending: {{ $pendingmastercount }},
            pendingTemp: {{ $pendingTempCount }},
            template: {{ $templatemastercount }},
            templateTemp: {{ $templateTempCount }},
            currentquarter: @json($currentquarter),
            nextquarter: @json($nextquarter),
            nextquarterLabel: @json($nextQuarterLabel),
            nextPriorityLabel: @json($nextPriorityLabel),
            phaseLabel: @json($phaseLabel),
            currentFincode: @json($currentFincode),
            currentFinancialYear: @json($currentFinancialYear),
            currentFinancialYearLabel: @json($currentFinancialYearLabel),
            toFincode: @json($toFincode),
            toFinancialYear: @json($toFinancialYear),
            toFinancialYearLabel: @json($toFinancialYearLabel),
            moveToOptions: @json($moveToOptions),
            hasTemplate: {{ $templatemastercount > 0 ? 'true' : 'false' }}
        };
        let tabLoaded = {
            spillover: false,
            pending: false,
            template: false
        };
        let requestInProgress = false;
        let finalizeBtnRef = null;

        function setActionButtonsDisabled(disabled) {
            $('#spilloverbuttonaction, #pendingbuttonaction, #templateaction, #finalizebuttonaction, #finalize_empty')
                .prop('disabled', disabled);
        }

        function showLoader() {
            $('#ajax-loader').css('display', 'flex');
        }

        function hideLoader() {
            $('#ajax-loader').hide();
        }

        function findMoveToOption(selectedValue) {
            if (!Array.isArray(meta.moveToOptions) || meta.moveToOptions.length === 0) {
                return null;
            }
            if (selectedValue) {
                const normalizedSelectedValue = String(selectedValue);
                const exact = meta.moveToOptions.find((option) => String(option.optionvalue || '') ===
                    normalizedSelectedValue);
                if (exact) return exact;
                const byQuarter = meta.moveToOptions.find((option) => String(option.auditquartercode || '') ===
                    normalizedSelectedValue);
                if (byQuarter) return byQuarter;
            }
            return meta.moveToOptions[0] || null;
        }

        function getQuarterOptionsHtml(selectedQuarter) {
            if (!Array.isArray(meta.moveToOptions) || meta.moveToOptions.length === 0) {
                return '';
            }
            const activeOption = findMoveToOption(selectedQuarter);
            const activeValue = activeOption ? String(activeOption.optionvalue || '') : '';

            return meta.moveToOptions.map((option) => {
                const value = String(option.optionvalue || option.auditquartercode || '');
                const label = option.optionlabel || option.auditquartercode || '';
                return `<option value="${esc(value)}" ${value === activeValue ? 'selected' : ''}>${esc(label)}</option>`;
            }).join('');
        }

        function getFinancialYearOptionHtml(selectedQuarter) {
            const option = findMoveToOption(selectedQuarter);
            const value = option?.financialyearcode || meta.toFincode || '';
            const label = option?.financialyear || meta.toFinancialYearLabel || meta.toFinancialYear || value || '-';
            return `<option value="${esc(value)}">${esc(label)}</option>`;
        }

        function updateFinalizeVisibility() {
            const btn = $('#finalizebuttonaction');
            if (!btn.length) return;
            const pendingMatched = Number(meta.pending) === Number(meta.pendingTemp);
            const templateMatched = Number(meta.template) === Number(meta.templateTemp);
            btn.toggle(pendingMatched && templateMatched);
        }

        function esc(v) {
            return String(v ?? '').replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m];
            });
        }

        function exportDataTableToExcel(tableSelector, fileBaseName) {
            if (!$.fn.DataTable.isDataTable(tableSelector)) return;
            const dt = $(tableSelector).DataTable();
            const headerCells = [];
            $(`${tableSelector} thead th`).each(function() {
                headerCells.push($(this).text().trim());
            });

            const csvEscape = (v) => {
                const s = String(v ?? '').replace(/"/g, '""');
                return `"${s}"`;
            };

            let csvRows = [];
            csvRows.push(headerCells.map(csvEscape).join(','));

            dt.rows({
                search: 'applied'
            }).every(function() {
                const row = $(this.node());
                let rowCells = [];
                row.find('td').each(function() {
                    const select = $(this).find('select');
                    let val = '';
                    if (select.length) {
                        val = select.find('option:selected').first().text().trim();
                    } else {
                        val = $(this).text().replace(/\s+/g, ' ').trim();
                    }
                    rowCells.push(csvEscape(val));
                });
                csvRows.push(rowCells.join(','));
            });

            const csvContent = '\ufeff' + csvRows.join('\r\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${fileBaseName}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function attachDownloadButtonToFilter(tableSelector, btnId, fileBaseName, retry = 0) {
            const wrapper = $(tableSelector).closest('.dataTables_wrapper');
            const filter = wrapper.find('.dataTables_filter');
            const topRow = wrapper.children('.row').first();
            if (!filter.length || !topRow.length) {
                if (retry < 8) {
                    setTimeout(function() {
                        attachDownloadButtonToFilter(tableSelector, btnId, fileBaseName, retry + 1);
                    }, 120);
                }
                return;
            }

            if (wrapper.find(`#${btnId}`).length) return;

            const leftCol = topRow.children('div').first();
            if (!leftCol.length) return;

            let toolbarLeft = leftCol.find('.dt-toolbar-left');
            if (!toolbarLeft.length) {
                toolbarLeft = $('<div class="dt-toolbar-left"></div>');
                leftCol.prepend(toolbarLeft);
            }

            const btn =
                `<button type="button" class="btn btn-sm dt-download-btn" id="${btnId}" data-table="${tableSelector}" data-file="${fileBaseName}"><span class="dt-download-icon"></span>Download</button>`;
            toolbarLeft.prepend(btn);
        }

        function fDate(v) {
            if (!v || v === '0000-00-00') return '-';
            const d = new Date(v);
            if (isNaN(d)) return '-';
            return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
        }

        function renderNoData(id, msg) {
            $(`#${id}`).html(
                `<div class="d-flex justify-content-center align-items-center" style="height:60vh;"><h4>${esc(msg)}</h4></div>`
            );
        }

        function selectedInstChangeDistrictName() {
            const text = $('#instchange_distcode option:selected').text().trim();
            return text && text !== 'Select District' ? text : 'selected district';
        }

        function noDistrictTabDataMessage(label) {
            return `No ${label} Institutions are available for ${selectedInstChangeDistrictName()}.`;
        }

        function setInstChangeEmptyFinalizeMessage() {
            const districtName = selectedInstChangeDistrictName();
            const phaseTag = meta.phaseLabel ? ` (${meta.phaseLabel})` : '';
            $('#instchange_empty_finalize_message').text(
                `You have No Spillover, Pending, and Template Audit Pending Institutions for ${districtName} in ${meta.currentquarter}${phaseTag}`
            );
        }

        function setInstChangeCompletedMessage() {
            const districtName = selectedInstChangeDistrictName();
            const phaseTag = meta.phaseLabel ? ` (${meta.phaseLabel})` : '';
            $('#instchange_completed_message').text(
                `All the Spillover and Pending institutions have successfully completed for ${districtName} in ${meta.currentquarter}${phaseTag}`
            );
        }

        function getInstChangeFilters() {
            if (!$('#instchange_deptcode').length) {
                return {};
            }

            return {
                filtermode: $('#instchange_deptcode').length ? 'Y' : '',
                deptcode: $('#instchange_deptcode').val() || '',
                regioncode: $('#instchange_regioncode').val() || '',
                distcode: $('#instchange_distcode').val() || ''
            };
        }

        function getActiveInstChangeTab() {
            const target = $('#myTab .nav-link.active').data('bs-target') || '#scheduled_plans';
            if (target === '#slip_details') return 'pending';
            if (target === '#template_details') return 'template';
            return 'spillover';
        }

        function resetInstChangeTabLoads() {
            tabLoaded = {
                spillover: false,
                pending: false,
                template: false
            };
        }

        function reloadActiveInstChangeTab() {
            resetInstChangeTabLoads();
            loadTab(getActiveInstChangeTab());
        }

        function resetInstChangeResultDisplay() {
            $('#instchange_result_section, #instchange_tab_content, #instchange_finalize_wrap, #instchange_empty_finalize, #instchange_completed_section')
                .hide();
            $('#finalizebuttonaction').hide();
            $('#confirm_check').prop('checked', false);
            $('#finalize_empty').prop('disabled', true);
            $('#scheduled_plans_content, #slip_details_content, #template_details_content').empty();
            $('#scheduled_plans_tab_item, #slip_details_tab_item, #template_details_tab_item').hide();
            resetInstChangeTabLoads();
        }

        function setInstChangeActiveTab(tab) {
            const targetByTab = {
                spillover: '#scheduled_plans',
                pending: '#slip_details',
                template: '#template_details'
            };
            const target = targetByTab[tab] || '#scheduled_plans';
            $('#myTab .nav-link').removeClass('active');
            $('#instchange_tab_content .tab-pane').removeClass('show active');
            $(`#myTab .nav-link[data-bs-target="${target}"]`).addClass('active');
            $(target).addClass('show active');
            animateTabPane(target);
        }

        function requestInstChangeTab(tab) {
            return $.ajax({
                url: "{{ url('/instchange/tab-data') }}",
                type: 'POST',
                data: Object.assign({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tab: tab
                }, getInstChangeFilters())
            });
        }

        function tabHasInstChangeData(response) {
            return Number((response && response.masterCount) || 0) > 0 ||
                (Array.isArray(response && response.rows) && response.rows.length > 0);
        }

        function renderInstChangeTab(tab, response) {
            if (tab === 'spillover') renderSpillover(response);
            if (tab === 'pending') {
                meta.pending = Number(response.masterCount ?? meta.pending) || 0;
                meta.pendingTemp = Number(response.tempCount ?? meta.pendingTemp) || 0;
                renderPending(response);
            }
            if (tab === 'template') {
                meta.template = Number(response.masterCount ?? meta.template) || 0;
                meta.templateTemp = Number(response.tempCount ?? meta.templateTemp) || 0;
                renderTemplate(response);
            }
            tabLoaded[tab] = true;
        }

        function loadInstChangeTabsForDistrict() {
            if (!$('#instchange_distcode').val()) {
                resetInstChangeResultDisplay();
                return;
            }

            resetInstChangeResultDisplay();
            showLoader();

            const tabs = ['spillover', 'pending', 'template'];
            const requests = tabs.map((tab) => requestInstChangeTab(tab)
                .then((response) => ({ tab, response }))
            );

            $.when.apply($, requests)
                .done(function() {
                    const results = Array.prototype.slice.call(arguments);
                    results.forEach((result) => {
                        if (!result.response || !result.response.success) return;
                        if (result.tab === 'pending') {
                            meta.pending = Number(result.response.masterCount || 0);
                            meta.pendingTemp = Number(result.response.tempCount || 0);
                        }
                        if (result.tab === 'template') {
                            meta.template = Number(result.response.masterCount || 0);
                            meta.templateTemp = Number(result.response.tempCount || 0);
                        }
                    });

                    const successfulTabs = results.filter((result) => result.response && result.response.success);
                    const finalizedDistrict = successfulTabs.find((result) =>
                        String(result.response.pendingStatus || '') === 'Y');
                    if (finalizedDistrict) {
                        setInstChangeCompletedMessage();
                        $('#instchange_completed_section').show();
                        return;
                    }

                    const dataTabs = successfulTabs.filter((result) =>
                        tabHasInstChangeData(result.response));

                    if (!dataTabs.length) {
                        setInstChangeEmptyFinalizeMessage();
                        $('#instchange_empty_finalize').show();
                        return;
                    }

                    successfulTabs.forEach((result) => {
                        const itemId = result.tab === 'spillover' ? '#scheduled_plans_tab_item' :
                            result.tab === 'pending' ? '#slip_details_tab_item' : '#template_details_tab_item';
                        $(itemId).show();
                        renderInstChangeTab(result.tab, result.response);
                    });

                    $('#instchange_result_section, #instchange_tab_content, #instchange_finalize_wrap').show();
                    setInstChangeActiveTab(dataTabs[0].tab);
                    updateFinalizeVisibility();
                })
                .fail(function(xhr) {
                    alert(xhr.responseJSON?.message || xhr.responseText || 'Request failed');
                })
                .always(hideLoader);
        }

        function fillDropdown(dropdown, placeholder, rows, valueKey, textKey, selectedValue) {
            let options = `<option value="">${esc(placeholder)}</option>`;
            (rows || []).forEach((row) => {
                const value = String(row[valueKey] ?? '');
                const selected = selectedValue && String(selectedValue) === value ? 'selected' : '';
                options += `<option value="${esc(value)}" ${selected}>${esc(row[textKey] ?? '')}</option>`;
            });
            dropdown.html(options).trigger('change.select2');
        }

        function setInstChangeDropdownLocked(dropdown, locked) {
            dropdown.prop('disabled', !!locked).trigger('change.select2');
        }

        function resetInstChangeDistrict() {
            fillDropdown($('#instchange_distcode'), 'Select District', [], 'distcode', 'distename', '');
            setInstChangeDropdownLocked($('#instchange_distcode'), false);
        }

        function fetchInstChangeField(field, payload = {}) {
            return $.ajax({
                url: "{{ url('/instchange/field-loader') }}",
                type: 'POST',
                data: Object.assign({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    field: field
                }, payload)
            });
        }

        function fetchInstChangeDepartments() {
            return fetchInstChangeField('department').then(function(response) {
                const selected = response.selected || {};
                const locked = response.locked || {};
                fillDropdown($('#instchange_deptcode'), 'Select Department', response.data || [], 'deptcode',
                    'deptelname', selected.deptcode);
                setInstChangeDropdownLocked($('#instchange_deptcode'), locked.deptcode);

                if (selected.deptcode) {
                    return fetchInstChangeRegions(selected.regioncode, selected.distcode);
                }
            });
        }

        function fetchInstChangeDistricts(selectedDistCode = '') {
            const deptcode = $('#instchange_deptcode').val();
            const regioncode = $('#instchange_regioncode').val();
            resetInstChangeDistrict();
            if (!deptcode || !regioncode) return $.Deferred().resolve().promise();

            return fetchInstChangeField('district', {
                deptcode: deptcode,
                regioncode: regioncode
            }).then(function(response) {
                const locked = response.locked || {};
                fillDropdown($('#instchange_distcode'), 'Select District', response.data || [], 'distcode',
                    'distename', selectedDistCode);
                setInstChangeDropdownLocked($('#instchange_distcode'), locked.distcode);
            });
        }

        function fetchInstChangeRegions(selectedRegionCode = '', selectedDistCode = '') {
            const deptcode = $('#instchange_deptcode').val();
            fillDropdown($('#instchange_regioncode'), 'Select Region', [], 'regioncode', 'regionename', '');
            setInstChangeDropdownLocked($('#instchange_regioncode'), false);
            resetInstChangeDistrict();
            if (!deptcode) return $.Deferred().resolve().promise();

            return fetchInstChangeField('region', {
                deptcode: deptcode
            }).then(function(response) {
                if (response && response.success) {
                    const locked = response.locked || {};
                    const regionRows = response.data || [];
                    selectedRegionCode = selectedRegionCode || response.selected?.regioncode || '';
                    if (!selectedRegionCode && regionRows.length === 1) {
                        selectedRegionCode = regionRows[0].regioncode || '';
                    }

                    fillDropdown($('#instchange_regioncode'), 'Select Region', regionRows, 'regioncode',
                        'regionename', selectedRegionCode);
                    setInstChangeDropdownLocked($('#instchange_regioncode'), locked.regioncode);
                    if ($('#instchange_regioncode').val()) {
                        return fetchInstChangeDistricts(selectedDistCode);
                    }
                }
            });
        }

        function toggleRemarks(instid, val) {
            const t = document.getElementById('remarks_' + instid);
            if (!t) return;
            if (val === 'Yes') {
                t.disabled = true;
                t.required = false;
                t.value = '';
                t.classList.add('bg-light');
                t.classList.remove('bg-white');
            } else {
                t.disabled = false;
                t.required = true;
                t.classList.add('bg-white');
                t.classList.remove('bg-light');
                if (!t.value) t.value = t.getAttribute('data-saved') || '';
            }
        }

        function loadTab(tab) {
            if (tabLoaded[tab]) return $.Deferred().resolve().promise();
            return $.ajax({
                url: "{{ url('/instchange/tab-data') }}",
                type: 'POST',
                data: Object.assign({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tab: tab
                }, getInstChangeFilters()),
                beforeSend: showLoader,
                complete: hideLoader,
                success: function(r) {
                    if (!r.success) return;
                    if (tab === 'spillover') renderSpillover(r);
                    if (tab === 'pending') {
                        meta.pending = Number(r.masterCount ?? meta.pending) || 0;
                        meta.pendingTemp = Number(r.tempCount ?? meta.pendingTemp) || 0;
                        renderPending(r);
                    }
                    if (tab === 'template') {
                        meta.template = Number(r.masterCount ?? meta.template) || 0;
                        meta.templateTemp = Number(r.tempCount ?? meta.templateTemp) || 0;
                        renderTemplate(r);
                    }
                    updateFinalizeVisibility();
                    tabLoaded[tab] = true;
                }
            });
        }

        function animateTabPane(targetSelector) {
            const pane = document.querySelector(targetSelector);
            if (!pane) return;
            pane.classList.remove('tab-slide-enter');
            void pane.offsetWidth;
            pane.classList.add('tab-slide-enter');
        }

        function renderSpillover(r) {
            if (!tabHasInstChangeData(r)) {
                renderNoData('scheduled_plans_content', noDistrictTabDataMessage('Carried forward'));
                return;
            }
            let rows = (r.rows || []).map((x, i) => {
                const toNumber = (v) => {
                    const n = Number(v);
                    return Number.isFinite(n) ? n : 0;
                };
                const remainingMandays = toNumber(x.remaining_mandays);
                const hasUpdatedRemaining = x.new_remainingmandays !== null && x.new_remainingmandays !== '';
                const updatedRemainingMandays = hasUpdatedRemaining ? toNumber(x.new_remainingmandays) : 0;
                const totalRemainingMandays = remainingMandays + updatedRemainingMandays;
                const sa = x.saved_action || '';
                const sr = x.saved_remarks || '';
                const yes = (sa === '' || sa === 'Y') ? 'checked' : '';
                const no = sa === 'N' ? 'checked' : '';
                const dis = (sa === '' || sa === 'Y') ? 'disabled' : '';
                return `<tr data-instid="${esc(x.instid)}">
                        <td class="sno-col">${i + 1}</td>
                        <td><b>Institution:</b> ${esc(x.instename)}<br>
                        <b>Team Size:</b> <span class="teamsize">${esc(x.team_member_count)}</span><br>
                        <b>Mandays:</b> <span class="mandays">${esc(x.mandays)}</span></td>
                        <td><b>Head:</b> ${esc(x.team_head_en)}<br>
                        <b>Members:</b> ${esc(x.team_members_en)}</td>
                        <td>${fDate(x.createdon)}</td>
                        <td>${fDate(x.entrymeetdate)}</td>
                        <td>${fDate(x.proposedexitmeetdate)}</td>
                        <td>${fDate(x.exitmeetdate)}</td>
                        <td><b>Completed Mandays:</b> <span class="completedmandays">${esc(x.completed_mandays)}</span><br>

                        ${hasUpdatedRemaining ? `<b>Updated Remaining Mandays:</b> <span class="new_remainingmandays">${esc(updatedRemainingMandays)}</span> <span class="text-muted">(${esc(x.auditquarter || '-')})</span><br>` : ''}
                        <b>Total Remaining Mandays:</b> <span class="total_remainingmandays">${esc(totalRemainingMandays)}</span><br>
                        </td>

                    </tr>`;
            }).join('');

            $('#scheduled_plans_content').html(
                `<div class="card card_border"><div class="card-header card_header_color">List of Institutions Carry Forwarded to ${esc(meta.nextquarterLabel || meta.nextquarter)}${(!meta.nextquarterLabel && meta.nextPriorityLabel) ? ` (${esc(meta.nextPriorityLabel)})` : ''} - ${esc(meta.toFinancialYear || '-')}</div><div class="card-body">
                    <table id="spoillover_table" class="table w-100 table-striped table-bordered display datatables-basic instchange-fit-table">
                        <thead>
                            <tr>
                                <th class="sno-col">S.No</th>
                                <th>Institution Details</th>
                                <th>Team Details</th>
                                <th>Date Of Intimation</th>
                                <th>Entry Meeting Date </th>
                                <th>Proposed Exit Meeting Date</th>
                                <th>Actual Exit Meeting Date</th>
                                <th>Mandays Details</th>

                                </thead><tbody>${rows}</tbody></table>
                    </div></div>`
            );

            $('#spoillover_table').DataTable({
                paging: true,
                pagingType: 'simple_numbers',
                searching: true,
                info: true,
                lengthChange: false,
                pageLength: 10,
                ordering: false,
                autoWidth: false,
                scrollX: false,
                initComplete: function() {
                    attachDownloadButtonToFilter('#spoillover_table', 'spillover_download_btn',
                        'Spillover Institutions');
                }
            });
        }

        function renderPending(r) {
            if (!tabHasInstChangeData(r)) {
                renderNoData('slip_details_content', noDistrictTabDataMessage('Pending institution'));
                return;
            }
            const currentFincode = esc(r.currentfincode || meta.currentFincode || '');
            const currentFinancialYear = esc(r.currentFinancialYear || meta.currentFinancialYear || '');
            const currentFinancialYearLabel = currentFinancialYear ? `${currentFinancialYear}` : (currentFincode || '-');
            const phaseLabel = esc(r.phaseLabel || meta.phaseLabel || '');
            const phaseTag = phaseLabel ? ` (${phaseLabel})` : '';
            const toFincode = esc(r.tofincode || meta.toFincode || '');
            const toFinancialYear = esc(r.toFinancialYear || meta.toFinancialYear || '');
            const toFinancialYearLabel = toFinancialYear ? `${toFinancialYear}` : (toFincode || '-');
            if (Array.isArray(r.moveToOptions) && r.moveToOptions.length) {
                meta.moveToOptions = r.moveToOptions;
            }
            let rows = (r.rows || []).map((x, i) => {
                const selectedQuarter = x.selected_quarter || '';
                return `<tr data-instid="${esc(x.instid)}">
                        <td class="sno-col">${i + 1}</td>
                        <td class="instname">${esc(x.instename)}</td>
                        <td class="quarterchange">
                            <select class="form-select quarter-select ${x.is_saved ? 'saved-border' : ''}">
                                ${getQuarterOptionsHtml(selectedQuarter)}
                            </select>
                        </td>
                        <td class="financialyearchange">
                            <select class="form-select fy-select" disabled>
                                ${getFinancialYearOptionHtml(selectedQuarter)}
                            </select>
                        </td></tr>`;
            }).join('');
            $('#slip_details_content').html(
                `<input type="hidden" id="currentquarter_pending" value="${esc(r.currentquarter || meta.currentquarter)}"><div class="card card_border">
                        <div class="card-header card_header_color">List of Pending Institutions in ${esc(meta.currentquarter)}${phaseTag} - ${currentFinancialYearLabel}</div>
                        <div class="card-body">
                        <table id="pending_table" class="table w-100 table-striped table-bordered display datatables-basic">
                        <thead><tr>
                        <th class="sno-col">S.No</th>
                        <th>Name of the Institution</th>
                        <th>Quarter</th>
                        <th>Financial Year</th>
                        </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                        </table>
                        <center>
                            <button class="btn button_save mt-3" id="pendingbuttonaction">Save Draft</button></center>
                            </div></div>`
            );
            $('#pending_table').DataTable({
                paging: true,
                pagingType: 'simple_numbers',
                searching: true,
                info: true,
                lengthChange: false,
                pageLength: 10,
                ordering: false,
                autoWidth: false,
                scrollX: false,
                initComplete: function() {
                    attachDownloadButtonToFilter('#pending_table', 'pending_download_btn',
                        'Pending Institutions');
                }
            });
        }

        function renderTemplate(r) {
            if (!tabHasInstChangeData(r)) {
                renderNoData('template_details_content', noDistrictTabDataMessage('Template audit pending'));
                return;
            }
            const currentFincode = esc(r.currentfincode || meta.currentFincode || '');
            const currentFinancialYear = esc(r.currentFinancialYear || meta.currentFinancialYear || '');
            const currentFinancialYearLabel = currentFinancialYear ? `${currentFinancialYear}` : (currentFincode || '-');
            const phaseLabel = esc(r.phaseLabel || meta.phaseLabel || '');
            const phaseTag = phaseLabel ? ` (${phaseLabel})` : '';
            const toFincode = esc(r.tofincode || meta.toFincode || '');
            const toFinancialYear = esc(r.toFinancialYear || meta.toFinancialYear || '');
            const toFinancialYearLabel = toFinancialYear ? `${toFinancialYear}` : (toFincode || '-');
            if (Array.isArray(r.moveToOptions) && r.moveToOptions.length) {
                meta.moveToOptions = r.moveToOptions;
            }
            let rows = (r.rows || []).map((x, i) => {
                const selectedQuarter = x.selected_quarter || '';
                return `<tr data-instid="${esc(x.instid)}">
                    <td class="sno-col">${i + 1}</td>
                    <td class="instname">${esc(x.instename)}</td>
                    <td class="quarterchange">
                        <select class="form-select quarter-select ${x.is_saved ? 'saved-border' : ''}">
                            ${getQuarterOptionsHtml(selectedQuarter)}
                        </select>
                    </td>
                    <td class="financialyearchange">
                        <select class="form-select fy-select" disabled>
                            ${getFinancialYearOptionHtml(selectedQuarter)}
                        </select>
                    </td></tr>`;
            }).join('');
            $('#template_details_content').html(
                `<input type="hidden" id="currentquarter_template" value="${esc(meta.currentquarter)}">
                    <div class="card card_border">
                    <div class="card-header card_header_color">List of Pending Template Audit Institutions in ${esc(meta.currentquarter)}${phaseTag} - ${currentFinancialYearLabel}</div>
                    <div class="card-body">
                    <table id="template_table" class="table w-100 table-striped table-bordered display datatables-basic instchange-fit-table">
                    <thead><tr>
                    <th class="sno-col">S.No</th>
                    <th>Name of the Institution</th>
                    <th>Quarter</th>
                    <th>Financial Year</th></tr></thead>
                    <tbody>${rows}</tbody></table>
                    <center><button class="btn button_save mt-3" id="templateaction">Save Draft</button>
                    </center></div></div>`
            );
            $('#template_table').DataTable({
                paging: true,
                pagingType: 'simple_numbers',
                searching: true,
                info: true,
                lengthChange: false,
                pageLength: 10,
                ordering: false,
                autoWidth: false,
                scrollX: false,
                initComplete: function() {
                    attachDownloadButtonToFilter('#template_table', 'template_download_btn',
                        'Template Audit Pending institutions');
                }
            });
        }

        function collectSpilloverData() {
            let rows = [],
                ok = true;
            if (!$.fn.DataTable.isDataTable('#spoillover_table')) return {
                valid: true,
                rows: []
            };
            $('#spoillover_table').DataTable().rows().every(function() {
                const n = $(this.node()),
                    id = n.data('instid'),
                    r = $('#remarks_' + id),
                    f = $(`input[name='action_${id}']:checked`).val() || '',
                    rm = (r.val() || '').trim();
                r.next('.remarks-error').remove();
                if (f === 'N' && rm === '') {
                    ok = false;
                    r.after(
                        '<div class="remarks-error" style="color:red;font-size:12px">Please enter remarks.</div>'
                    );
                }
                rows.push({
                    instid: id,
                    teamsize: n.find('.teamsize').text().trim(),
                    mandays: n.find('.mandays').text().trim(),
                    completedmandays: n.find('.completedmandays').text().trim(),
                    remainingmandays: n.find('.remainingmandays').text().trim(),
                    remainingworkingdays: n.find('.remainingworkingdays').text().trim(),
                    new_remainingmandays: n.find('.new_remainingmandays').text().trim(),
                    auditquarter: n.find('.auditquarter').text().trim(),
                    confirmflag: f,
                    remarks: rm
                });
            });
            return {
                valid: ok,
                rows: rows
            };
        }

        function collectPendingData() {
            let rows = [],
                cq = $('#currentquarter_pending').val() || meta.currentquarter;
            if (!$.fn.DataTable.isDataTable('#pending_table')) return {
                rows: []
            };
            $('#pending_table').DataTable().rows().every(function() {
                const n = $(this.node());
                rows.push({
                    instid: n.data('instid'),
                    instname: n.find('.instname').text().trim(),
                    currentquarter: cq,
                    quarterchange: n.find('.quarterchange .quarter-select').val()
                });
            });
            return {
                rows: rows
            };
        }

        function templatePendingData() {
            let rows = [],
                cq = $('#currentquarter_template').val() || meta.currentquarter;
            if (!$.fn.DataTable.isDataTable('#template_table')) return {
                rows: []
            };
            $('#template_table').DataTable().rows().every(function() {
                const n = $(this.node());
                rows.push({
                    instid: n.data('instid'),
                    instname: n.find('.instname').text().trim(),
                    currentquarter: cq,
                    quarterchange: n.find('.quarterchange .quarter-select').val()
                });
            });
            return {
                rows: rows
            };
        }

        function reloadTabOnly(action) {
            const map = {
                spilldatainsert: 'spillover',
                pendingdatainsert: 'pending',
                templatedatainsert: 'template'
            };
            const tab = map[action];
            if (!tab) {
                location.reload();
                return;
            }
            tabLoaded[tab] = false;
            loadTab(tab);
        }

        $(document).on('change', '.quarter-select', function() {
            const row = $(this).closest('tr');
            row.find('.financialyearchange .fy-select').html(getFinancialYearOptionHtml($(this).val()));
        });

        function submitQTForm(action) {
            if (requestInProgress) return;
            let payloadRows = [],
                status = null;
            if (action === 'spilldatainsert') {
                const r = collectSpilloverData();
                if (!r.valid) return;
                payloadRows = r.rows;
            } else if (action === 'pendingdatainsert') payloadRows = collectPendingData().rows;
            else if (action === 'templatedatainsert') payloadRows = templatePendingData().rows;
            else if (action === 'finalize_empty') status = $('#pendinginstmappingstatus').val();

            requestInProgress = true;
            setActionButtonsDisabled(true);
            showLoader();

            $.ajax({
                url: "{{ url('/instchange/penidninstUpdation') }}",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(Object.assign({
                    _token: "{{ csrf_token() }}",
                    action: action,
                    spillous: payloadRows,
                    status: status
                }, getInstChangeFilters())),
                complete: function() {
                    requestInProgress = false;
                },
                success: function(res) {
                    hideLoader();
                    if (res.success) {
                        getLabels_jsonlayout([{
                            id: res.message,
                            key: res.message
                        }], 'N').then((t) => {
                            passing_alert_value('Confirmation', Object.values(t)[0],
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
                            $('#ok_button').off('click').on('click', function() {
                                //  showLoader();
                                setActionButtonsDisabled(false);
                                if (action === 'finalize_empty') {
                                    loadInstChangeTabsForDistrict();
                                    return;
                                }
                                reloadTabOnly(action);
                            });
                        });
                    } else {
                        hideLoader();
                        setActionButtonsDisabled(false);
                    }
                },
                error: function(x) {
                    hideLoader();
                    setActionButtonsDisabled(false);
                    alert(x.responseJSON?.message || x.responseText || 'Request failed');
                }
            });
        }

        function ensureAllLoaded() {
            const req = [];
            if (meta.spill > 0 && !tabLoaded.spillover) req.push(loadTab('spillover'));
            if (meta.pending > 0 && !tabLoaded.pending) req.push(loadTab('pending'));
            if (meta.hasTemplate && meta.template > 0 && !tabLoaded.template) req.push(loadTab('template'));
            return req.length ? $.when.apply($, req) : $.Deferred().resolve().promise();
        }

        function submitFinalize(btn) {
            if (btn.prop('disabled') || requestInProgress) return;
            ensureAllLoaded().done(function() {
                const all = {
                    spillover: collectSpilloverData(),
                    pending: collectPendingData(),
                    template: templatePendingData()
                };
                requestInProgress = true;
                setActionButtonsDisabled(true);
                showLoader();

                $.ajax({
                    url: "{{ url('/instchange/penidninstUpdation') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(Object.assign({
                        _token: "{{ csrf_token() }}",
                        action: 'finalize',
                        spillover: all.spillover,
                        pending: all.pending,
                        template: all.template
                    }, getInstChangeFilters())),
                    success: function(r) {
                        hideLoader();
                        if (!r.success) {
                            requestInProgress = false;
                            setActionButtonsDisabled(false);
                            return;
                        }
                        $('#confirmation_alert .modal-footer').show();
                        getLabels_jsonlayout([{
                            id: r.message,
                            key: r.message
                        }], 'N').then((t) => {
                            passing_alert_value('Confirmation', Object.values(t)[0],
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
                            $('#ok_button').off('click').on('click', function() {
                                requestInProgress = false;
                                setActionButtonsDisabled(false);
                                loadInstChangeTabsForDistrict();
                            });
                        });
                    },
                    error: function() {
                        hideLoader();
                        requestInProgress = false;
                        setActionButtonsDisabled(false);
                    }
                });
            });
        }

        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').select2();
            }

            const initialFilterLoad = fetchInstChangeDepartments();
            resetInstChangeResultDisplay();

            $('#instchange_deptcode').on('change', function() {
                resetInstChangeResultDisplay();
                fetchInstChangeRegions();
            });

            $('#instchange_regioncode').on('change', function() {
                resetInstChangeResultDisplay();
                fetchInstChangeDistricts();
            });

            $('#instchange_distcode').on('change', function() {
                if ($(this).val()) {
                    loadInstChangeTabsForDistrict();
                } else {
                    resetInstChangeResultDisplay();
                }
            });

            updateFinalizeVisibility();
            $.when(initialFilterLoad).always(function() {
                resetInstChangeResultDisplay();
            });

            const confirmCheck = document.getElementById('confirm_check');
            if (confirmCheck) {
                confirmCheck.addEventListener('change', function() {
                    $('#finalize_empty').prop('disabled', !this.checked);
                });
            }

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const t = $(e.target).data('bs-target');
                if (t === '#slip_details' && !tabLoaded.pending) loadTab('pending');
                if (t === '#template_details' && !tabLoaded.template) loadTab('template');
                animateTabPane(t);
            });

            $(document).on('click', '#spilloverbuttonaction', function(e) {
                e.preventDefault();
                submitQTForm('spilldatainsert');
            });
            $(document).on('click', '#pendingbuttonaction', function(e) {
                e.preventDefault();
                submitQTForm('pendingdatainsert');
            });
            $(document).on('click', '#templateaction', function(e) {
                e.preventDefault();
                submitQTForm('templatedatainsert');
            });
            $(document).on('click', '.dt-download-btn', function(e) {
                e.preventDefault();
                const tableSelector = $(this).data('table');
                const fileBaseName = $(this).data('file');
                exportDataTableToExcel(tableSelector, fileBaseName);
            });

            $(document).on('click', '#finalize_empty', function(e) {
                e.preventDefault();
                $('#process_button').prop('disabled', false);
                passing_alert_value('Confirmation',
                    'Are you sure to finalize Spillover and Pending Institutions?',
                    'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');
                $('#process_button').off('click').on('click', function(ev) {
                    ev.preventDefault();
                    $(this).prop('disabled', true);
                    $('#confirmation_alert').modal('hide');
                    submitQTForm('finalize_empty');
                });
            });

            $(document).on('click', '#finalizebuttonaction', function(e) {
                e.preventDefault();
                const btn = $(this);
                finalizeBtnRef = btn;
                $('#process_button').prop('disabled', false);
                passing_alert_value('Confirmation',
                    'Are you sure to finalize Spillover and Pending Institutions?',
                    'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert');
                $('#process_button').off('click').on('click', function() {
                    $(this).prop('disabled', true);
                    $('#confirmation_alert').modal('hide');
                    sentfinaliseOTP();
                });
            });
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

            $(document).off('click', '#verify_otp_button').on('click', '#verify_otp_button', function() {
                if ($(this).prop('disabled')) return;
                $(this).prop('disabled', true);
                handleVerifyOtp();
            });
            $(document).off('click', '#resend_otp_link').on('click', '#resend_otp_link', function(e) {
                e.preventDefault();
                handleResendOtp();
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



        function sentfinaliseOTP() {

            $('#process_button').attr('disabled', true);
            showLoader();

            $.ajax({
                url: '/instchange/sendOtp_QT',
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

        function handleVerifyOtp() {

            let otp = '';
            document.querySelectorAll('.otp-input').forEach(input => {
                otp += input.value;
            });

            if (!/^\d{6}$/.test(otp)) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }

            showLoader();
            $('#process_button').attr('disabled', true);

            $.ajax({
                url: 'instchange/verifyOtp_QT',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    otp: otp
                },

                success: function(response) {
                    hideLoader();
                    if (response.status === 'success') {
                        submitFinalize(finalizeBtnRef);
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
                url: 'instchange/sendOtp_QT',
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
