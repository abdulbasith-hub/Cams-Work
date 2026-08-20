@extends('index2')

@section('title', 'Developer Task Dashboard')

@section('content')
@php
    $dashboardCards = $dashboardData['cards'] ?? [];
    $ticketDashboardData = $ticketDashboardData ?? ['cards' => [], 'categories' => []];
    $ticketCards = $ticketDashboardData['cards'] ?? [];
    $testingTaskDashboardData = $testingTaskDashboardData ?? ['cards' => [], 'categories' => []];
    $testingTaskCards = $testingTaskDashboardData['cards'] ?? [];
@endphp
@include('tickets.partials.app-theme')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<style>
    .task-dashboard-shell .stat-card {
        width: 100%;
        border: 1px solid #e6edf5;
        border-radius: 20px;
        padding: 20px 22px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: left;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;
    }

    .task-dashboard-shell .stat-card.is-static {
        cursor: default;
        opacity: 0.9;
    }

    .task-dashboard-shell .stat-card:hover,
    .task-dashboard-shell .stat-card.is-active {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .task-dashboard-shell .stat-card:hover .stat-card-label,
    .task-dashboard-shell .stat-card.is-active .stat-card-label {
        color: #1d4ed8;
        font-weight: 700;
    }

    .task-dashboard-shell .stat-card:hover .stat-card-value,
    .task-dashboard-shell .stat-card.is-active .stat-card-value {
        color: #1e3a8a;
    }

    .task-dashboard-shell .stat-card.is-static:hover {
        transform: none;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        border-color: #e6edf5;
        background: #ffffff;
    }

    .task-dashboard-shell .dashboard-card-group-title {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 700;
    }

    .task-dashboard-shell .dashboard-card-group {
        border: 1px solid #e5eef9;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        padding: 22px;
        margin-bottom: 1.5rem;
    }

    .task-dashboard-shell .dashboard-card-group.ticket-group {
        border-color: #cfe2ff;
        background: #f8fbff;
    }

    .task-dashboard-shell .dashboard-card-group.task-group {
        border-color: #d1fae5;
        background: #f5fffa;
    }

    .task-dashboard-shell .dashboard-card-group.testing-group {
        border-color: #e9d5ff;
        background: #fbf5ff;
    }

    .task-dashboard-shell .dashboard-card-group-title {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5eef9;
    }

    .task-dashboard-shell .dashboard-card-group.ticket-group .dashboard-card-group-title {
        color: #1d4ed8;
    }

    .task-dashboard-shell .dashboard-card-group.task-group .dashboard-card-group-title {
        color: #15803d;
    }

    .task-dashboard-shell .dashboard-card-group.testing-group .dashboard-card-group-title {
        color: #7c3aed;
    }

    .task-dashboard-shell .dashboard-card-group .dashboard-card-group-title::before {
        content: '';
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: currentColor;
    }

    .task-dashboard-shell .stat-card-label {
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .task-dashboard-shell .stat-card-value {
        font-size: 2.2rem;
        line-height: 1;
        font-weight: 700;
        color: #111827;
        margin-bottom: 10px;
    }

    .task-dashboard-shell .stat-card-accent {
        width: 52px;
        height: 4px;
        border-radius: 999px;
        background: #cbd5e1;
    }

    .task-dashboard-shell .stat-card-accent.assigned {
        background: #2563eb;
    }

    .task-dashboard-shell .stat-card-accent.completed {
        background: #16a34a;
    }

    .task-dashboard-shell .stat-card-accent.in-progress {
        background: #0ea5e9;
    }

    .task-dashboard-shell .stat-card-accent.pending {
        background: #f59e0b;
    }

    .task-dashboard-shell .stat-card-accent.overdue {
        background: #dc2626;
    }

    .task-dashboard-shell .stat-card-accent.before-due {
        background: #7c3aed;
    }

    /* Ensure DataTable controls are visible inside this shell */
    .task-dashboard-shell .dataTables_wrapper {
        width: 100% !important;
        display: block !important;
    }

    .task-dashboard-shell .dataTables_wrapper .dataTables_length,
    .task-dashboard-shell .dataTables_wrapper .dataTables_filter,
    .task-dashboard-shell .dataTables_wrapper .dataTables_info,
    .task-dashboard-shell .dataTables_wrapper .dataTables_paginate {
        display: block !important;
    }

    .task-dashboard-shell #dashboardDeveloperSummaryTable,
    .task-dashboard-shell #dashboardDeveloperSummaryTable thead,
    .task-dashboard-shell #dashboardDeveloperSummaryTable thead tr,
    .task-dashboard-shell #dashboardDeveloperSummaryTable thead th {
        visibility: visible !important;
    }

    .task-dashboard-shell #dashboardDeveloperSummaryTable thead {
        display: table-header-group !important;
    }

    .task-dashboard-shell #dashboardDeveloperSummaryTable thead tr {
        display: table-row !important;
    }

    .task-dashboard-shell #dashboardDeveloperSummaryTable thead th {
        display: table-cell !important;
    }

    .task-dashboard-shell .dashboard-section {
        border: 1px solid #e5eef9;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        padding: 22px;
    }
 .table-responsive .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 3px;
        background-color: #fff;
        color: #007bff;
        cursor: pointer;
        font-size: 14px;
    }

    .table-responsive .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    .task-dashboard-shell .dashboard-section.is-hidden,
    .task-dashboard-shell .dashboard-empty-state.is-hidden {
        display: none;
    }

    .task-dashboard-shell .dashboard-section-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .task-dashboard-shell .dashboard-section-subtitle {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .task-dashboard-shell .dashboard-count-link {
        border: 0;
        border-radius: 999px;
        padding: 6px 14px;
        min-width: 44px;
        text-align: center;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .task-dashboard-shell .dashboard-count-link.assigned {
        background: #dbeafe;
        font-weight: 700;
        color: #1d4ed8;
    }

    .task-dashboard-shell .dashboard-count-link.completed {
        background: #dcfce7;
        color: #166534;
    }

    .task-dashboard-shell .dashboard-count-link.in-progress {
        background: #e0f2fe;
        color: #0369a1;
    }

    .task-dashboard-shell .dashboard-count-link.pending {
        background: #fef3c7;
        color: #b45309;
    }

    .task-dashboard-shell .dashboard-count-link.overdue {
        background: #fee2e2;
        color: #b91c1c;
    }

    .task-dashboard-shell .dashboard-count-link.completed-before-due {
        background: #ede9fe;
        color: #6d28d9;
    }

    .task-dashboard-shell .dashboard-count-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
    }

    .task-dashboard-shell .dashboard-empty-state {
        padding: 24px 12px;
        text-align: center;
        color: #64748b;
    }

    .task-dashboard-shell .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .task-dashboard-shell .status-pill.pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .task-dashboard-shell .status-pill.in-progress {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .task-dashboard-shell .status-pill.completed {
        background: #dcfce7;
        color: #166534;
    }

    .task-dashboard-shell .status-pill.overdue {
        background: #fee2e2;
        color: #b91c1c;
    }
</style>

<div class="helpdesk-app-theme task-dashboard-shell">
    <div class="helpdesk-main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title"><i class="ti ti-layout-dashboard text-primary"></i> Developer Task Dashboard</h1>
            <a href="{{ route('helpdesk.tasks.list') }}" class="btn btn-light">Back to Task List</a>
        </div>

        <div class="dashboard-card-group ticket-group">
            <h5 class="dashboard-card-group-title">Ticket Summary</h5>
            <div class="row g-3 mb-0">
                @foreach ($ticketCards as $card)
                    <div class="col-md-6 col-xl-4">
                        <button type="button" class="stat-card js-ticket-dashboard-card" data-category-key="{{ $card['key'] }}">
                            <div class="stat-card-label">{{ $card['label'] }}</div>
                            <div class="stat-card-value">{{ $card['count'] }}</div>
                            <div class="stat-card-accent {{ $card['accent'] }}"></div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dashboard-card-group task-group">
            <h5 class="dashboard-card-group-title">Developer Task Summary</h5>
            <div class="row g-3 mb-0">
                @foreach ($dashboardCards as $card)
                    <div class="col-md-6 col-xl-4">
                        <button type="button" class="stat-card js-dashboard-card" data-category-key="{{ $card['key'] }}">
                            <div class="stat-card-label">{{ $card['label'] }}</div>
                            <div class="stat-card-value">{{ $card['count'] }}</div>
                            <div class="stat-card-accent {{ $card['accent'] }}"></div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dashboard-card-group testing-group">
            <h5 class="dashboard-card-group-title">Testing Task Summary</h5>
            <div class="row g-3 mb-0">
                @foreach ($testingTaskCards as $card)
                    <div class="col-md-6 col-xl-4">
                        <button type="button" class="stat-card js-testing-dashboard-card" data-category-key="{{ $card['key'] }}">
                            <div class="stat-card-label">{{ $card['label'] }}</div>
                            <div class="stat-card-value">{{ $card['count'] }}</div>
                            <div class="stat-card-accent {{ $card['accent'] }}"></div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="dashboardDeveloperSummarySection" class="dashboard-section mb-4 is-hidden">
            <div class="mb-3">
                <h5 id="dashboardDeveloperSummaryTitle" class="dashboard-section-title">Developer Summary</h5>
                <p id="dashboardDeveloperSummarySubtitle" class="dashboard-section-subtitle"></p>
            </div>
            <div class="table-responsive">
                <table id="dashboardDeveloperSummaryTable" class="table w-100 table-striped table-bordered display text-nowrap task-table datatables-basic">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Developer</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardDeveloperSummaryBody"></tbody>
                </table>
            </div>
            <div id="dashboardDeveloperSummaryEmpty" class="dashboard-empty-state is-hidden">No records found for this card.</div>
        </div>

        <div id="dashboardTaskDetailsSection" class="dashboard-section is-hidden">
            <div class="mb-3">
                <h5 id="dashboardTaskDetailsTitle" class="dashboard-section-title">Task Details</h5>
                <p id="dashboardTaskDetailsSubtitle" class="dashboard-section-subtitle"></p>
            </div>
            <div class="table-responsive">
                <table id="dashboardTaskDetailsTable" class="table w-100 table-striped table-bordered display text-nowrap task-table datatables-basic">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Process Assigned</th>
                            <th class="text-center">Developer</th>
                            <th class="text-center">Task Type</th>
                            <th class="text-center">Created On</th>
                            <th class="text-center">Last Updated On</th>
                            <th class="text-center">Assigned On</th>
                            <th class="text-center">Expected On</th>
                            <th class="text-center">Started On</th>
                            <th class="text-center">Completed On</th>
                            <th class="text-center">Progress Status</th>
                            <th class="text-center">Schedule Status</th>
                            <th class="text-center">Tester Status</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardTaskDetailsBody"></tbody>
                </table>
            </div>
            <div id="dashboardTaskDetailsEmpty" class="dashboard-empty-state is-hidden">No task details found.</div>
        </div>

        <div id="dashboardTicketDetailsSection" class="dashboard-section is-hidden">
            <div class="mb-3">
                <h5 id="dashboardTicketDetailsTitle" class="dashboard-section-title">Ticket Details</h5>
                <p id="dashboardTicketDetailsSubtitle" class="dashboard-section-subtitle"></p>
            </div>
            <div class="table-responsive">
                <table id="dashboardTicketDetailsTable" class="table w-100 table-striped table-bordered display text-nowrap task-table datatables-basic">
                    <thead>
                        <tr>
                            <th class="text-center">S.No</th>
                            <th class="text-center">Ticket No</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Developer</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Priority</th>
                            <th class="text-center">Ticket Status</th>
                            {{-- <th class="text-center">Assignment Status</th> --}}
                            <th class="text-center">Created On</th>
                            <th class="text-center">Last Updated On</th>
                            <th class="text-center">Assigned On</th>
                            <th class="text-center">Completed On</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardTicketDetailsBody"></tbody>
                </table>
            </div>
            <div id="dashboardTicketDetailsEmpty" class="dashboard-empty-state is-hidden">No ticket details found.</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dashboardDataUrl = @json(route('helpdesk.tasks.dashboard.data'));
        const dashboardCards = document.querySelectorAll('.js-dashboard-card');
        const ticketDashboardCards = document.querySelectorAll('.js-ticket-dashboard-card');
        const testingDashboardCards = document.querySelectorAll('.js-testing-dashboard-card');
        const dashboardDeveloperSummarySection = document.getElementById('dashboardDeveloperSummarySection');
        const dashboardDeveloperSummaryTitle = document.getElementById('dashboardDeveloperSummaryTitle');
        const dashboardDeveloperSummarySubtitle = document.getElementById('dashboardDeveloperSummarySubtitle');
        const dashboardDeveloperSummaryBody = document.getElementById('dashboardDeveloperSummaryBody');
        const dashboardDeveloperSummaryEmpty = document.getElementById('dashboardDeveloperSummaryEmpty');
        const dashboardTaskDetailsSection = document.getElementById('dashboardTaskDetailsSection');
        const dashboardTaskDetailsTitle = document.getElementById('dashboardTaskDetailsTitle');
        const dashboardTaskDetailsSubtitle = document.getElementById('dashboardTaskDetailsSubtitle');
        const dashboardTaskDetailsBody = document.getElementById('dashboardTaskDetailsBody');
        const dashboardTaskDetailsEmpty = document.getElementById('dashboardTaskDetailsEmpty');
        const dashboardTicketDetailsSection = document.getElementById('dashboardTicketDetailsSection');
        const dashboardTicketDetailsTitle = document.getElementById('dashboardTicketDetailsTitle');
        const dashboardTicketDetailsSubtitle = document.getElementById('dashboardTicketDetailsSubtitle');
        const dashboardTicketDetailsBody = document.getElementById('dashboardTicketDetailsBody');
        const dashboardTicketDetailsEmpty = document.getElementById('dashboardTicketDetailsEmpty');
        const dashboardTableIds = {
            summary: '#dashboardDeveloperSummaryTable',
            tasks: '#dashboardTaskDetailsTable',
            tickets: '#dashboardTicketDetailsTable',
        };

        function hasDataTablePlugin() {
            return Boolean(window.jQuery && window.jQuery.fn && (window.jQuery.fn.DataTable || window.jQuery.fn.dataTable));
        }

        function isDashboardDataTable(tableSelector) {
            if (!hasDataTablePlugin()) {
                return false;
            }

            if (window.jQuery.fn.DataTable?.isDataTable) {
                return window.jQuery.fn.DataTable.isDataTable(tableSelector);
            }

            return Boolean(window.jQuery.fn.dataTable?.isDataTable?.(tableSelector));
        }

        function getDashboardDataTable($table) {
            return window.jQuery.fn.DataTable ? $table.DataTable() : $table.dataTable().api();
        }

        function destroyDashboardTable(tableSelector) {
            if (!hasDataTablePlugin()) {
                return;
            }

            const $table = window.jQuery(tableSelector);
            if (isDashboardDataTable(tableSelector)) {
                getDashboardDataTable($table).clear().destroy();
            }
        }

        function initializeDashboardTable(tableSelector, retryCount = 0) {
            if (!hasDataTablePlugin()) {
                if (retryCount < 10) {
                    setTimeout(function () {
                        initializeDashboardTable(tableSelector, retryCount + 1);
                    }, 100);
                }

                return;
            }

            const $table = window.jQuery(tableSelector);
            if (!$table.length) {
                return;
            }

            // If already initialized, destroy first (mirrors tickets initialization pattern)
            try {
                if (window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable(tableSelector)) {
                    window.jQuery(tableSelector).DataTable().clear().destroy();
                }
            } catch (e) {
                // ignore
            }

            const dataTableOptions = {
                dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                lengthChange: true,
                searching: true,
                searchDelay: 250,
                ordering: true,
                paging: true,
                info: true,
                responsive: true,
                scrollX: true,
                autoWidth: false,
            };

            // Initialize DataTable and ensure wrapper shows
            const api = (window.jQuery.fn.DataTable) ? $table.DataTable(dataTableOptions) : $table.dataTable(dataTableOptions).api();
            try {
                const wrapperId = ($table.attr('id') || '').replace(/[^a-zA-Z0-9_-]/g, '') + '_wrapper';
                if (wrapperId) {
                    const $wrap = window.jQuery('#' + wrapperId);
                    if ($wrap.length) {
                        $wrap.css('display', 'block');
                    }
                }
            } catch (e) {
                // ignore
            }

            return api;
        }

        function initializeVisibleDashboardTable(tableSelector) {
            requestAnimationFrame(function () {
                const api = initializeDashboardTable(tableSelector);

                if (!api) {
                    return;
                }

                setTimeout(function () {
                    if (tableSelector === dashboardTableIds.summary) {
                        window.jQuery(tableSelector).find('thead, thead tr, thead th').css({
                            display: '',
                            visibility: 'visible',
                        });
                    }

                    if (api.columns && api.columns.adjust) {
                        api.columns.adjust();
                    }

                    if (api.responsive && api.responsive.recalc) {
                        api.responsive.recalc();
                    }

                    if (api.draw) {
                        api.draw(false);
                    }
                }, 50);
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getStatusClass(status) {
            const normalizedStatus = String(status || '').trim().toLowerCase();

            if (normalizedStatus === 'completed') {
                return 'completed';
            }

            if (normalizedStatus === 'returned') {
                return 'completed';
            }

            if (normalizedStatus === 'assigned') {
                return 'pending';
            }

            if (normalizedStatus === 'in progress') {
                return 'in-progress';
            }

            if (normalizedStatus === 'reassigned') {
                return 'in-progress';
            }

            if (normalizedStatus === 'overdue') {
                return 'overdue';
            }

            return 'pending';
        }

        function getCountBadgeClass(categoryKey) {
            const normalizedKey = String(categoryKey || '').trim().toLowerCase();

            if (normalizedKey === 'completed_before_due') {
                return 'completed-before-due';
            }

            return normalizedKey.replace(/_/g, '-');
        }

        function clearDetailSections() {
            destroyDashboardTable(dashboardTableIds.tasks);
            destroyDashboardTable(dashboardTableIds.tickets);
            dashboardTaskDetailsSection.classList.add('is-hidden');
            dashboardTicketDetailsSection.classList.add('is-hidden');
            dashboardTaskDetailsBody.innerHTML = '';
            dashboardTicketDetailsBody.innerHTML = '';
        }

        function fetchDashboardData(params) {
            const url = new URL(dashboardDataUrl, window.location.origin);
            Object.entries(params).forEach(function ([key, value]) {
                if (value !== undefined && value !== null && String(value) !== '') {
                    url.searchParams.set(key, value);
                }
            });

            return fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok || payload.success === false) {
                        throw new Error(payload.message || 'Unable to fetch dashboard data.');
                    }

                    return payload;
                });
            });
        }

        function setActiveDashboardCard(dashboardType, categoryKey) {
            dashboardCards.forEach(function (card) {
                card.classList.toggle('is-active', dashboardType === 'task' && card.dataset.categoryKey === categoryKey);
            });
            ticketDashboardCards.forEach(function (card) {
                card.classList.toggle('is-active', dashboardType === 'ticket' && card.dataset.categoryKey === categoryKey);
            });
            testingDashboardCards.forEach(function (card) {
                card.classList.toggle('is-active', dashboardType === 'testing' && card.dataset.categoryKey === categoryKey);
            });
        }

        function renderSummaryRows(dashboardType, categoryKey, category) {
            if (!dashboardDeveloperSummarySection || !dashboardDeveloperSummaryBody || !category) {
                return;
            }

            const developers = Array.isArray(category.developers) ? category.developers : [];

            dashboardDeveloperSummaryTitle.textContent = `${category.label} - Developer Count`;
            dashboardDeveloperSummarySubtitle.textContent = '';
            dashboardDeveloperSummarySection.classList.remove('is-hidden');
            destroyDashboardTable(dashboardTableIds.summary);
            clearDetailSections();

            if (!developers.length) {
                dashboardDeveloperSummaryBody.innerHTML = '';
                dashboardDeveloperSummaryEmpty.textContent = 'No records found for this card.';
                dashboardDeveloperSummaryEmpty.classList.remove('is-hidden');
                return;
            }

            dashboardDeveloperSummaryEmpty.classList.add('is-hidden');
            dashboardDeveloperSummaryBody.innerHTML = developers.map(function (developer, index) {
                return `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${escapeHtml(developer.developer_name || '-')}</td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="dashboard-count-link js-dashboard-count-link ${getCountBadgeClass(categoryKey)}"
                                data-dashboard-type="${escapeHtml(dashboardType)}"
                                data-category-key="${escapeHtml(categoryKey)}"
                                data-developer-userid="${escapeHtml(developer.developer_userid || '')}"
                            >
                                ${escapeHtml(developer.count ?? 0)}
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            initializeVisibleDashboardTable(dashboardTableIds.summary);
            dashboardDeveloperSummarySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function renderDeveloperSummary(dashboardType, categoryKey) {
            if (!dashboardDeveloperSummarySection || !dashboardDeveloperSummaryBody) {
                return;
            }

            setActiveDashboardCard(dashboardType, categoryKey);
            dashboardDeveloperSummaryTitle.textContent = 'Loading Developer Count...';
            dashboardDeveloperSummarySubtitle.textContent = '';
            dashboardDeveloperSummarySection.classList.remove('is-hidden');
            dashboardDeveloperSummaryEmpty.classList.add('is-hidden');
            destroyDashboardTable(dashboardTableIds.summary);
            clearDetailSections();
            dashboardDeveloperSummaryBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

            fetchDashboardData({
                dashboard_type: dashboardType,
                category_key: categoryKey,
            }).then(function (payload) {
                renderSummaryRows(dashboardType, categoryKey, payload.category);
            }).catch(function (error) {
                dashboardDeveloperSummaryTitle.textContent = 'Developer Summary';
                dashboardDeveloperSummaryBody.innerHTML = '';
                dashboardDeveloperSummaryEmpty.textContent = error.message;
                dashboardDeveloperSummaryEmpty.classList.remove('is-hidden');
            });
        }

        function renderTaskDetails(category, developer, tasks) {
            tasks = Array.isArray(tasks) ? tasks : [];
            dashboardTaskDetailsTitle.textContent = `${category.label} - ${developer?.developer_name || 'Developer'} Tasks`;
            dashboardTaskDetailsSubtitle.textContent = '';
            dashboardTaskDetailsSection.classList.remove('is-hidden');
            destroyDashboardTable(dashboardTableIds.tasks);
            destroyDashboardTable(dashboardTableIds.tickets);
            dashboardTicketDetailsSection.classList.add('is-hidden');

            if (!tasks.length) {
                dashboardTaskDetailsBody.innerHTML = '';
                dashboardTaskDetailsEmpty.textContent = 'No task details found.';
                dashboardTaskDetailsEmpty.classList.remove('is-hidden');
                return;
            }

            dashboardTaskDetailsEmpty.classList.add('is-hidden');
            dashboardTaskDetailsBody.innerHTML = tasks.map(function (task, index) {
                return `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${escapeHtml(task.process_assigned || '-')}</td>
                        <td>${escapeHtml(task.developer_name || '-')}</td>
                        <td class="text-center">${escapeHtml(task.task_type || '-')}</td>
                        <td class="text-center">${escapeHtml(task.created_on || '-')}</td>
                        <td class="text-center">${escapeHtml(task.updated_on || '-')}</td>
                        <td class="text-center">${escapeHtml(task.assigned_on || '-')}</td>
                        <td class="text-center">${escapeHtml(task.expected_date_to_complete || '-')}</td>
                        <td class="text-center">${escapeHtml(task.started_on || '-')}</td>
                        <td class="text-center">${escapeHtml(task.completed_on || '-')}</td>
                        <td class="text-center">
                            <span class="status-pill ${getStatusClass(task.progress_status)}">${escapeHtml(task.progress_status || '-')}</span>
                        </td>
                        <td>${escapeHtml(task.schedule_status || '-')}</td>
                        <td>${escapeHtml(task.task_status_by_tester || '-')}</td>
                    </tr>
                `;
            }).join('');

            initializeVisibleDashboardTable(dashboardTableIds.tasks);
            dashboardTaskDetailsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function renderTicketDetails(category, developer, tickets) {
            tickets = Array.isArray(tickets) ? tickets : [];
            dashboardTicketDetailsTitle.textContent = `${category.label} - ${developer?.developer_name || 'Developer'} Tickets`;
            dashboardTicketDetailsSubtitle.textContent = '';
            dashboardTicketDetailsSection.classList.remove('is-hidden');
            destroyDashboardTable(dashboardTableIds.tickets);
            destroyDashboardTable(dashboardTableIds.tasks);
            dashboardTaskDetailsSection.classList.add('is-hidden');

            if (!tickets.length) {
                dashboardTicketDetailsBody.innerHTML = '';
                dashboardTicketDetailsEmpty.textContent = 'No ticket details found.';
                dashboardTicketDetailsEmpty.classList.remove('is-hidden');
                return;
            }

            dashboardTicketDetailsEmpty.classList.add('is-hidden');
            dashboardTicketDetailsBody.innerHTML = tickets.map(function (ticket, index) {
                return `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${escapeHtml(ticket.ticket_number || '-')}</td>
                        <td class="text-wrap">${escapeHtml(ticket.subject || '-')}</td>
                        <td>${escapeHtml(ticket.developer_name || '-')}</td>
                        <td>${escapeHtml(ticket.department_name || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.category || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.priority || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.ticket_status || '-')}</td>

                        <td class="text-center">${escapeHtml(ticket.created_on || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.updated_on || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.assigned_at || '-')}</td>
                        <td class="text-center">${escapeHtml(ticket.released_at || '-')}</td>
                    </tr>
                `;
            }).join('');

            initializeVisibleDashboardTable(dashboardTableIds.tickets);
            dashboardTicketDetailsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function renderDeveloperDetails(dashboardType, categoryKey, developerUserId) {
            clearDetailSections();

            if (dashboardType === 'ticket') {
                dashboardTicketDetailsTitle.textContent = 'Loading Ticket Details...';
                dashboardTicketDetailsSection.classList.remove('is-hidden');
                dashboardTicketDetailsEmpty.classList.add('is-hidden');
                dashboardTicketDetailsBody.innerHTML = '<tr><td colspan="12" class="text-center">Loading...</td></tr>';
            } else {
                dashboardTaskDetailsTitle.textContent = 'Loading Task Details...';
                dashboardTaskDetailsSection.classList.remove('is-hidden');
                dashboardTaskDetailsEmpty.classList.add('is-hidden');
                dashboardTaskDetailsBody.innerHTML = '<tr><td colspan="13" class="text-center">Loading...</td></tr>';
            }

            fetchDashboardData({
                dashboard_type: dashboardType,
                category_key: categoryKey,
                developer_userid: developerUserId,
            }).then(function (payload) {
                if (dashboardType === 'ticket') {
                    renderTicketDetails(payload.category, payload.developer, payload.tickets);
                    return;
                }

                renderTaskDetails(payload.category, payload.developer, payload.tasks);
            }).catch(function (error) {
                if (dashboardType === 'ticket') {
                    dashboardTicketDetailsBody.innerHTML = '';
                    dashboardTicketDetailsEmpty.textContent = error.message;
                    dashboardTicketDetailsEmpty.classList.remove('is-hidden');
                    return;
                }

                dashboardTaskDetailsBody.innerHTML = '';
                dashboardTaskDetailsEmpty.textContent = error.message;
                dashboardTaskDetailsEmpty.classList.remove('is-hidden');
            });
        }

        dashboardCards.forEach(function (card) {
            card.addEventListener('click', function () {
                renderDeveloperSummary('task', card.dataset.categoryKey || '');
            });
        });

            ticketDashboardCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    renderDeveloperSummary('ticket', card.dataset.categoryKey || '');
                });
            });

        testingDashboardCards.forEach(function (card) {
            card.addEventListener('click', function () {
                renderDeveloperSummary('testing', card.dataset.categoryKey || '');
            });
        });

        if (dashboardDeveloperSummaryBody) {
            dashboardDeveloperSummaryBody.addEventListener('click', function (event) {
                const button = event.target.closest('.js-dashboard-count-link');
                if (!button) {
                    return;
                }

                renderDeveloperDetails(button.dataset.dashboardType || 'task', button.dataset.categoryKey || '', button.dataset.developerUserid || '');
            });
        }

        if (ticketDashboardCards.length) {
            renderDeveloperSummary('ticket', ticketDashboardCards[0].dataset.categoryKey || '');
        } else if (dashboardCards.length) {
            renderDeveloperSummary('task', dashboardCards[0].dataset.categoryKey || '');
        }
    });
</script>
@endsection
