@extends('index2')

@section('title', 'Super Admin Dashboard')

@section('content')
<style>
    .helpdesk-app-theme {
        --primary-color: #4f46e5;
        --secondary-color: #7c3aed;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --dark-color: #1f2937;
        --light-color: #f3f4f6;
        color: var(--dark-color);
        font-family: 'Nunito', sans-serif;
    }

    .helpdesk-app-theme .helpdesk-main-content {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: var(--dark-color);
    }

    .helpdesk-app-theme .stat-card,
    .helpdesk-app-theme .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .stat-card {
        color: #fff;
        padding: 25px;
        margin-bottom: 20px;
    }

    .helpdesk-app-theme .stat-card h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .helpdesk-app-theme .stat-card p {
        margin: 0;
        opacity: 0.92;
    }

    .helpdesk-app-theme .stat-card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .helpdesk-app-theme .stat-card-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .helpdesk-app-theme .stat-card-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .helpdesk-app-theme .stat-card-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .helpdesk-app-theme .stat-card-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .helpdesk-app-theme .stat-card-dark {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .helpdesk-app-theme .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        border-radius: 15px 15px 0 0 !important;
        padding: 15px 20px;
    }

    .helpdesk-app-theme .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .helpdesk-app-theme .table thead {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
    }

    .helpdesk-app-theme .table tbody tr:hover {
        background: var(--light-color);
    }

    .helpdesk-app-theme .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .helpdesk-app-theme .badge-priority-low {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-priority-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-priority-high {
        background: #fed7aa;
        color: #9a3412;
    }

    .helpdesk-app-theme .badge-priority-urgent {
        background: #fecaca;
        color: #991b1b;
    }

    .helpdesk-app-theme .badge-status-open {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-status-in_progress {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-status-resolved {
        background: #d1fae5;
        color: #065f46;
    }

    .helpdesk-app-theme .badge-status-closed {
        background: #e5e7eb;
        color: #374151;
    }

    .helpdesk-app-theme .dashboard-tabs {
        gap: 0;
        padding: 4px;
        border: 1px solid #1e4f82;
        border-radius: 999px;
        background: #143f68;
        box-shadow: 0 10px 22px rgba(20, 63, 104, 0.24);
    }

    .helpdesk-app-theme .dashboard-tab-btn {
        border: 0;
        border-radius: 999px;
        padding: 9px 18px;
        background: transparent;
        color: #dbeafe;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        min-width: 112px;
        justify-content: center;
        transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .helpdesk-app-theme .dashboard-tab-btn::before {
        content: '';
        width: 12px;
        height: 12px;
        border: 2px solid #2f86d3;
        border-radius: 999px;
        background: #ffffff;
        box-shadow: inset 0 0 0 3px #ffffff;
    }

    .helpdesk-app-theme .dashboard-tab-btn.is-active {
        background: #ffffff;
        color: #143f68;
        box-shadow: 0 8px 16px rgba(6, 22, 38, 0.24);
    }

    .helpdesk-app-theme .dashboard-tab-btn.is-active::before {
        background: #2f86d3;
    }

    .helpdesk-app-theme .dashboard-tab-btn:not(.is-active):hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    .helpdesk-app-theme .dashboard-pane {
        display: none;
    }

    .helpdesk-app-theme .dashboard-pane.is-active {
        display: block;
    }

    .helpdesk-app-theme .dashboard-card {
        width: 100%;
        min-height: 138px;
        padding: 18px 20px;
        border: 1px solid #cfe2f7;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(47, 128, 208, 0.08);
        text-align: left;
        cursor: pointer;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .helpdesk-app-theme .dashboard-card::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: #cbd5e1;
    }

    .helpdesk-app-theme .dashboard-card-assigned {
        background: linear-gradient(135deg, #ffffff 0%, #edf7ff 100%);
    }

    .helpdesk-app-theme .dashboard-card-pending {
        background: linear-gradient(135deg, #ffffff 0%, #fff7e6 100%);
    }

    .helpdesk-app-theme .dashboard-card-in-progress {
        background: linear-gradient(135deg, #ffffff 0%, #e7f9ff 100%);
    }

    .helpdesk-app-theme .dashboard-card-completed {
        background: linear-gradient(135deg, #ffffff 0%, #eafaf2 100%);
    }

    .helpdesk-app-theme .dashboard-card-overdue {
        background: linear-gradient(135deg, #ffffff 0%, #fff1f3 100%);
    }

    .helpdesk-app-theme .dashboard-card-closed {
        background: linear-gradient(135deg, #ffffff 0%, #eef2f7 100%);
    }

    .helpdesk-app-theme .dashboard-card-before-due {
        background: linear-gradient(135deg, #ffffff 0%, #f1f0ff 100%);
    }

    .helpdesk-app-theme .dashboard-card-assigned::before {
        background: #2f86d3;
    }

    .helpdesk-app-theme .dashboard-card-completed::before {
        background: #16885d;
    }

    .helpdesk-app-theme .dashboard-card-in-progress::before {
        background: #00a4d8;
    }

    .helpdesk-app-theme .dashboard-card-pending::before {
        background: #f59e0b;
    }

    .helpdesk-app-theme .dashboard-card-overdue::before {
        background: #d73a49;
    }

    .helpdesk-app-theme .dashboard-card-closed::before {
        background: #475569;
    }

    .helpdesk-app-theme .dashboard-card-before-due::before {
        background: #6366f1;
    }

    .helpdesk-app-theme .dashboard-card:hover,
    .helpdesk-app-theme .dashboard-card.is-active {
        transform: translateY(-3px);
        border-color: #2f86d3;
        background: #ffffff;
        box-shadow: 0 18px 36px rgba(47, 128, 208, 0.18);
    }

    .helpdesk-app-theme .dashboard-card-main {
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
    }

    .helpdesk-app-theme .dashboard-card-topline {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .helpdesk-app-theme .dashboard-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: #2f86d3;
        box-shadow: 0 10px 18px rgba(47, 128, 208, 0.2);
        flex: 0 0 auto;
    }

    .helpdesk-app-theme .dashboard-card-pending .dashboard-card-icon {
        background: #f59e0b;
        box-shadow: 0 10px 18px rgba(245, 158, 11, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-in-progress .dashboard-card-icon {
        background: #00a4d8;
        box-shadow: 0 10px 18px rgba(0, 164, 216, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-completed .dashboard-card-icon {
        background: #16885d;
        box-shadow: 0 10px 18px rgba(22, 136, 93, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-overdue .dashboard-card-icon {
        background: #d73a49;
        box-shadow: 0 10px 18px rgba(215, 58, 73, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-closed .dashboard-card-icon {
        background: #475569;
        box-shadow: 0 10px 18px rgba(71, 85, 105, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-before-due .dashboard-card-icon {
        background: #6366f1;
        box-shadow: 0 10px 18px rgba(99, 102, 241, 0.2);
    }

    .helpdesk-app-theme .dashboard-card-label {
        margin: 0;
        color: #375c84;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        line-height: 1.25;
    }

    .helpdesk-app-theme .dashboard-card-value {
        margin: 0;
        color: #0f172a;
        font-size: 2.45rem;
        line-height: 1;
        font-weight: 800;
    }

    .helpdesk-app-theme .dashboard-card-action {
        color: #2f6fae;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .helpdesk-app-theme .dashboard-card-accent {
        width: 82px;
        height: 82px;
        margin: 0;
        border-radius: 999px;
        background: #cbd5e1;
        position: absolute;
        right: -24px;
        bottom: -28px;
        opacity: 0.14;
    }

    .helpdesk-app-theme .dashboard-card-accent.assigned {
        background: #2f86d3;
    }

    .helpdesk-app-theme .dashboard-card-accent.completed {
        background: #16885d;
    }

    .helpdesk-app-theme .dashboard-card-accent.in-progress {
        background: #00a4d8;
    }

    .helpdesk-app-theme .dashboard-card-accent.pending {
        background: #f59e0b;
    }

    .helpdesk-app-theme .dashboard-card-accent.overdue {
        background: #d73a49;
    }

    .helpdesk-app-theme .dashboard-card-accent.closed {
        background: #475569;
    }

    .helpdesk-app-theme .dashboard-card-accent.before-due {
        background: #6366f1;
    }

    .helpdesk-app-theme .dashboard-card-group {
        border: 1px solid #e5eef9;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        padding: 22px;
        margin-bottom: 1.5rem;
    }

    .helpdesk-app-theme .dashboard-card-group-title {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .helpdesk-app-theme .ticket-card-grid {
        width: 100%;
        max-width: none;
    }

    .helpdesk-app-theme .dashboard-detail-section {
        border: 1px solid #e5eef9;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        scroll-margin-top: 90px;
    }

    .helpdesk-app-theme .dashboard-detail-section .dataTables_wrapper {
        width: 100% !important;
    }

    .helpdesk-app-theme .dashboard-detail-section .dashboard-table-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .helpdesk-app-theme .dashboard-detail-section .dashboard-table-actions .dt-buttons,
    .helpdesk-app-theme .dashboard-detail-section .dashboard-table-actions .dataTables_length {
        float: none;
        margin: 0;
    }

    .helpdesk-app-theme .dashboard-detail-section .dashboard-table-actions .dataTables_length label {
        margin: 0;
    }

    .helpdesk-app-theme .dashboard-detail-header {
        padding: 15px 22px;
        background: linear-gradient(135deg, #2f86d3 0%, #1769b5 100%);
        color: #ffffff;
    }

    .helpdesk-app-theme .dashboard-detail-title {
        margin: 0;
        color: #ffffff;
        font-weight: 800;
    }

    .helpdesk-app-theme .dashboard-detail-body {
        padding: 22px;
    }

    .helpdesk-app-theme .dashboard-detail-header .btn {
        background: #ffffff;
        border-color: #ffffff;
        color: #1769b5 !important;
        font-weight: 700;
    }

    .helpdesk-app-theme .dashboard-detail-section .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        margin: 0 !important;
    }

    .helpdesk-app-theme .dashboard-detail-section .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
        border-radius: 0.375rem;
        box-shadow: none;
    }

    .helpdesk-app-theme .department-count-btn {
        border: 0;
        border-radius: 999px;
        background: #5d87ff;
        color: #ffffff;
        min-width: 36px;
        padding: 5px 12px;
        font-size: 0.8rem;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
    }

    .helpdesk-app-theme .department-count-btn:hover {
        background: #2f6fae;
    }

    .helpdesk-app-theme .department-ticket-subheader {
        border-left: 4px solid #2f86d3;
        background: #eef6ff;
        color: #143f68;
        padding: 10px 14px;
        border-radius: 8px;
    }

    .helpdesk-app-theme #departmentTicketRowsTableWrap {
        scroll-margin-top: 90px;
    }

    .helpdesk-app-theme #ticketDashboardDetailTable .ticket-subject-column,
    .helpdesk-app-theme #departmentTicketDashboardDetailTable .ticket-subject-column {
        min-width: 320px;
        max-width: 620px;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .helpdesk-app-theme .dashboard-overview-section.is-hidden,
    .helpdesk-app-theme .dashboard-detail-section.is-hidden,
    .helpdesk-app-theme .dashboard-table.is-hidden,
    .helpdesk-app-theme .is-hidden {
        display: none;
    }

    .helpdesk-app-theme .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .helpdesk-app-theme .status-pill.pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .helpdesk-app-theme .status-pill.in-progress {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .helpdesk-app-theme .status-pill.completed {
        background: #dcfce7;
        color: #166534;
    }

    .helpdesk-app-theme .status-pill.overdue {
        background: #fee2e2;
        color: #b91c1c;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@php
    use Illuminate\Support\Str;

    $dashboardCards = $dashboardData['cards'] ?? [];
    $taskCategories = $dashboardData['categories'] ?? [];
    $testingTaskCards = $testingTaskDashboardData['cards'] ?? [];
    $testingTaskCategories = $testingTaskDashboardData['categories'] ?? [];
    $ticketCardIcons = [
        'total_tickets' => 'bi-ticket-detailed',
        'pending_tickets' => 'bi-hourglass-split',
        'in_progress_tickets' => 'bi-arrow-repeat',
        'resolved_tickets' => 'bi-check2-circle',
        'closed_tickets' => 'bi-lock',
        'departments' => 'bi-building',
    ];
    $taskCardIcons = [
        'assigned' => 'bi-list-task',
        'completed' => 'bi-check2-square',
        'in_progress' => 'bi-play-circle',
        'pending' => 'bi-clock-history',
        'overdue' => 'bi-exclamation-triangle',
        'completed_before_due' => 'bi-calendar-check',
    ];
@endphp
<div class="helpdesk-app-theme">
    <div class="helpdesk-main-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h1 class="page-title">
                <i class="bi bi-speedometer2 text-primary"></i> Helpdesk Dashboard
            </h1>
            <div class="dashboard-tabs d-flex ms-auto">
                <button type="button" class="dashboard-tab-btn is-active" data-dashboard-tab="tickets">
                    <i class="bi bi-ticket-perforated"></i> Tickets
                </button>
                @if($canViewTaskDashboard)
                    <button type="button" class="dashboard-tab-btn" data-dashboard-tab="tasks">
                        <i class="bi bi-kanban"></i> Tasks
                    </button>
                @endif
            </div>
        </div>

        <div class="dashboard-pane is-active" data-dashboard-pane="tickets">
            <div class="row g-3 mb-4 ticket-card-grid">
                @foreach ($ticketDashboardCards as $card)
                    <div class="col-md-6 col-xl-4">
                        <button type="button" class="dashboard-card dashboard-card-{{ $card['accent'] }} js-ticket-summary-card" data-category-key="{{ $card['key'] }}">
                            <div class="dashboard-card-main">
                                <div class="dashboard-card-topline">
                                    <span class="dashboard-card-icon">
                                        <i class="bi {{ $ticketCardIcons[$card['key']] ?? 'bi-ticket-perforated' }}"></i>
                                    </span>
                                    <span class="dashboard-card-label">{{ $card['label'] }}</span>
                                </div>
                                <div>
                                    <div class="dashboard-card-value">{{ $card['count'] }}</div>
                                    <div class="dashboard-card-action">View details</div>
                                </div>
                            </div>
                            <div class="dashboard-card-accent {{ $card['accent'] }}"></div>
                        </button>
                    </div>
                @endforeach
            </div>

            <div id="ticketDashboardDetailSection" class="dashboard-detail-section mb-4 is-hidden">
                <div class="dashboard-detail-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 id="ticketDashboardDetailTitle" class="dashboard-detail-title">Ticket Details</h5>
                    <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-sm">View All Tickets</a>
                </div>
                <div class="dashboard-detail-body">
                    <div id="ticketRowsTableWrap" class="table-responsive dashboard-table">
                        <table id="ticketDashboardDetailTable" class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">S.No</th>
                                    <th class="text-center text-nowrap">Ticket #</th>
                                    <th class="ticket-subject-column">Subject</th>
                                    <th class="text-center text-nowrap">User</th>
                                    <th class="text-center text-nowrap">Department</th>
                                    <th class="text-center text-nowrap">Priority</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Created</th>
                                </tr>
                            </thead>
                            <tbody id="ticketDashboardDetailBody"></tbody>
                        </table>
                    </div>
                    <div id="departmentRowsTableWrap" class="table-responsive dashboard-table is-hidden">
                        <table id="departmentDashboardDetailTable" class="table w-100 table-striped table-bordered display text-nowrap align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center">S.No</th>
                                    <th>Department</th>
                                    <th class="text-center">Tickets</th>
                                </tr>
                            </thead>
                            <tbody id="departmentDashboardDetailBody"></tbody>
                        </table>
                    </div>
                    <div id="departmentTicketRowsTableWrap" class="dashboard-table is-hidden mt-4">
                        <div class="department-ticket-subheader mb-3">
                            <h6 id="departmentTicketDashboardDetailTitle" class="mb-0 fw-bold">Department Ticket Details</h6>
                        </div>
                        <div class="table-responsive">
                            <table id="departmentTicketDashboardDetailTable" class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="text-center text-nowrap">S.No</th>
                                        <th class="text-center text-nowrap">Ticket #</th>
                                        <th class="ticket-subject-column">Subject</th>
                                        <th class="text-center text-nowrap">User</th>
                                        <th class="text-center text-nowrap">Department</th>
                                        <th class="text-center text-nowrap">Priority</th>
                                        <th class="text-center text-nowrap">Status</th>
                                        <th class="text-center text-nowrap">Created</th>
                                    </tr>
                                </thead>
                                <tbody id="departmentTicketDashboardDetailBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="ticketDashboardEmpty" class="text-center text-muted py-4 is-hidden">No details available.</div>
                </div>
            </div>

            <div class="row mb-4 dashboard-overview-section is-hidden">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-light"><i class="bi bi-bar-chart"></i> Tickets by Department</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Tickets</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ticketsByDepartment as $dept)
                                        <tr>
                                            <td>{{ $dept->name }}</td>
                                            <td><span class="badge bg-primary">{{ $dept->tickets_count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-light"><i class="bi bi-pie-chart"></i> Tickets by Priority</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Priority</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ticketsByPriority as $priority)
                                        <tr>
                                            <td>
                                                <span class="badge badge-priority-{{ $priority->priority }}">
                                                    {{ ucfirst($priority->priority) }}
                                                </span>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $priority->count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-overview-section is-hidden">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-light"><i class="bi bi-clock-history"></i> Recent Tickets</h5>
                    <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                            <div class="table-responsive">
                        <table class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center">Ticket #</th>
                                    <th class="lang align-middle text-center">Subject</th>
                                    <th class="lang align-middle text-center">User</th>
                                    <th class="lang align-middle text-center">Department</th>
                                    <th class="lang align-middle text-center">Priority</th>
                                    <th class="lang align-middle text-center">Status</th>
                                    <th class="lang align-middle text-center">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTickets->take(10) as $ticket)
                                <tr>
                                    <td class="lang align-middle text-center">
                                        <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-decoration-none" style="color:black">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="lang align-middle text-center">{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td class="lang align-middle text-center">{{ \App\Support\HelpdeskSession::normalizeUserName($ticket->user_name) }}</td>
                                    <td class="lang align-middle text-center">{{ $ticket->department_name ?: 'N/A' }}</td>
                                    <td class="lang align-middle text-center">
                                        <span class="badge badge-priority-{{ $ticket->priority }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $ticket->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="lang align-middle text-center">
                                        {{ $ticket->created_at->format('D, d/m/Y h:i A') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No recent tickets found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($canViewTaskDashboard)
            <div class="dashboard-pane" data-dashboard-pane="tasks">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('helpdesk.tasks.list') }}" class="btn btn-light">Back to Task List</a>
                </div>

                <div class="dashboard-card-group">
                    <h5 class="dashboard-card-group-title">Developer Task Summary</h5>
                    <div class="row g-3 mb-0">
                        @foreach ($dashboardCards as $card)
                            <div class="col-md-6 col-xl-4">
                                <button type="button" class="dashboard-card dashboard-card-{{ $card['accent'] }} js-task-summary-card" data-dashboard-type="task" data-category-key="{{ $card['key'] }}">
                                    <div class="dashboard-card-main">
                                        <div class="dashboard-card-topline">
                                            <span class="dashboard-card-icon">
                                                <i class="bi {{ $taskCardIcons[$card['key']] ?? 'bi-list-task' }}"></i>
                                            </span>
                                            <span class="dashboard-card-label">{{ $card['label'] }}</span>
                                        </div>
                                        <div>
                                            <div class="dashboard-card-value">{{ $card['count'] }}</div>
                                            <div class="dashboard-card-action">View details</div>
                                        </div>
                                    </div>
                                    <div class="dashboard-card-accent {{ $card['accent'] }}"></div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="dashboard-card-group">
                    <h5 class="dashboard-card-group-title">Testing Task Summary</h5>
                    <div class="row g-3 mb-0">
                        @foreach ($testingTaskCards as $card)
                            <div class="col-md-6 col-xl-4">
                                <button type="button" class="dashboard-card dashboard-card-{{ $card['accent'] }} js-task-summary-card" data-dashboard-type="testing" data-category-key="{{ $card['key'] }}">
                                    <div class="dashboard-card-main">
                                        <div class="dashboard-card-topline">
                                            <span class="dashboard-card-icon">
                                                <i class="bi {{ $taskCardIcons[$card['key']] ?? 'bi-list-task' }}"></i>
                                            </span>
                                            <span class="dashboard-card-label">{{ $card['label'] }}</span>
                                        </div>
                                        <div>
                                            <div class="dashboard-card-value">{{ $card['count'] }}</div>
                                            <div class="dashboard-card-action">View details</div>
                                        </div>
                                    </div>
                                    <div class="dashboard-card-accent {{ $card['accent'] }}"></div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="taskDashboardDetailSection" class="dashboard-detail-section is-hidden">
                    <div class="dashboard-detail-header">
                        <h5 id="taskDashboardDetailTitle" class="dashboard-detail-title">Task Details</h5>
                    </div>
                    <div class="dashboard-detail-body">
                        <div id="taskRowsTableWrap" class="table-responsive">
                            <table id="taskDashboardDetailTable" class="table w-100 table-striped table-bordered display align-middle text-nowrap datatables-basic">
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
                                <tbody id="taskDashboardDetailBody"></tbody>
                            </table>
                        </div>
                        <div id="taskDashboardEmpty" class="text-center text-muted py-4 is-hidden">No task details found.</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.print.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ticketDetails = @json($ticketDashboardDetails ?? []);
        const taskCategories = @json($taskCategories);
        const testingTaskCategories = @json($testingTaskCategories);
        const tabButtons = document.querySelectorAll('[data-dashboard-tab]');
        const tabPanes = document.querySelectorAll('[data-dashboard-pane]');
        const ticketCards = document.querySelectorAll('.js-ticket-summary-card');
        const taskCards = document.querySelectorAll('.js-task-summary-card');
        const ticketDetailSection = document.getElementById('ticketDashboardDetailSection');
        const ticketTitle = document.getElementById('ticketDashboardDetailTitle');
        const ticketBody = document.getElementById('ticketDashboardDetailBody');
        const departmentBody = document.getElementById('departmentDashboardDetailBody');
        const ticketTableWrap = document.getElementById('ticketRowsTableWrap');
        const departmentTableWrap = document.getElementById('departmentRowsTableWrap');
        const departmentTicketTableWrap = document.getElementById('departmentTicketRowsTableWrap');
        const departmentTicketTitle = document.getElementById('departmentTicketDashboardDetailTitle');
        const departmentTicketBody = document.getElementById('departmentTicketDashboardDetailBody');
        const ticketEmpty = document.getElementById('ticketDashboardEmpty');
        const taskDetailSection = document.getElementById('taskDashboardDetailSection');
        const taskTitle = document.getElementById('taskDashboardDetailTitle');
        const taskTableWrap = document.getElementById('taskRowsTableWrap');
        const taskBody = document.getElementById('taskDashboardDetailBody');
        const taskEmpty = document.getElementById('taskDashboardEmpty');
        const dashboardTables = {
            tickets: '#ticketDashboardDetailTable',
            departments: '#departmentDashboardDetailTable',
            departmentTickets: '#departmentTicketDashboardDetailTable',
            tasks: '#taskDashboardDetailTable',
        };
        let activeDepartmentRows = [];

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function hasDataTablePlugin() {
            return Boolean(window.jQuery && window.jQuery.fn && (window.jQuery.fn.DataTable || window.jQuery.fn.dataTable));
        }

        function isDashboardDataTable(tableSelector) {
            if (!hasDataTablePlugin()) {
                return false;
            }

            if (window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable) {
                return window.jQuery.fn.DataTable.isDataTable(tableSelector);
            }

            return Boolean(window.jQuery.fn.dataTable && window.jQuery.fn.dataTable.isDataTable && window.jQuery.fn.dataTable.isDataTable(tableSelector));
        }

        function destroyDashboardTable(tableSelector) {
            if (!hasDataTablePlugin()) {
                return;
            }

            const table = window.jQuery(tableSelector);
            if (table.length && isDashboardDataTable(tableSelector)) {
                table.DataTable().clear().destroy();
            }
        }

        function initializeDashboardTable(tableSelector, downloadTitle) {
            if (!hasDataTablePlugin()) {
                return;
            }

            const table = window.jQuery(tableSelector);
            if (!table.length || !table.find('tbody tr').length) {
                return;
            }

            if (isDashboardDataTable(tableSelector)) {
                table.DataTable().clear().destroy();
            }

            requestAnimationFrame(function () {
                const shouldScrollX = tableSelector === dashboardTables.tasks;
                const hasSubjectColumn = tableSelector === dashboardTables.tickets || tableSelector === dashboardTables.departmentTickets;
                const dataTable = table.DataTable({
                    dom: '<"row mb-3 align-items-center gy-2"<"col-sm-12 col-md-7 dashboard-table-actions"B l><"col-sm-12 col-md-5"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    lengthChange: true,
                    searching: true,
                    searchDelay: 250,
                    ordering: true,
                    paging: true,
                    info: true,
                    scrollX: shouldScrollX,
                    autoWidth: false,
                    columnDefs: hasSubjectColumn ? [
                        { targets: 2, className: 'ticket-subject-column' },
                    ] : [],
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="bi bi-download"></i> Download',
                            className: 'btn btn-success',
                            title: downloadTitle || 'Helpdesk Dashboard Details',
                            exportOptions: {
                                columns: ':visible',
                            },
                        },
                    ],
                });

                setTimeout(function () {
                    dataTable.columns.adjust().draw(false);
                }, 50);
            });
        }

        function destroyTicketTables() {
            destroyDashboardTable(dashboardTables.tickets);
            destroyDashboardTable(dashboardTables.departments);
            destroyDashboardTable(dashboardTables.departmentTickets);
        }

        function setActiveCard(cards, activeCard) {
            cards.forEach(function (card) {
                card.classList.toggle('is-active', card === activeCard);
            });
        }

        function activateTab(tabName) {
            tabButtons.forEach(function (button) {
                button.classList.toggle('is-active', button.dataset.dashboardTab === tabName);
            });
            tabPanes.forEach(function (pane) {
                pane.classList.toggle('is-active', pane.dataset.dashboardPane === tabName);
            });
        }

        function scrollToSection(section) {
            if (!section) {
                return;
            }

            setTimeout(function () {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }, 120);
        }

        function ticketRowsHtml(rows) {
            return rows.map(function (ticket, index) {
                return `
                    <tr>
                        <td class="text-center text-nowrap">${index + 1}</td>
                        <td class="text-center text-nowrap">
                            <a href="${escapeHtml(ticket.show_url || '#')}" class="text-decoration-none" style="color:black">${escapeHtml(ticket.ticket_number || '-')}</a>
                        </td>
                        <td class="ticket-subject-column">${escapeHtml(ticket.subject || '-')}</td>
                        <td class="text-nowrap">${escapeHtml(ticket.user_name || '-')}</td>
                        <td class="text-nowrap">${escapeHtml(ticket.department_name || '-')}</td>
                        <td class="text-center text-nowrap">${escapeHtml(ticket.priority || '-')}</td>
                        <td class="text-center text-nowrap">${escapeHtml(ticket.status || '-')}</td>
                        <td class="text-center text-nowrap">${escapeHtml(ticket.created_at || '-')}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderDepartmentTickets(departmentIndex) {
            const department = activeDepartmentRows[departmentIndex] || {};
            const rows = Array.isArray(department.tickets) ? department.tickets : [];

            destroyDashboardTable(dashboardTables.departmentTickets);
            if (departmentTicketTableWrap) {
                departmentTicketTableWrap.classList.toggle('is-hidden', rows.length === 0);
            }
            if (departmentTicketTitle) {
                departmentTicketTitle.textContent = (department.department || 'Department') + ' Ticket Details';
            }
            if (departmentTicketBody) {
                departmentTicketBody.innerHTML = ticketRowsHtml(rows);
            }
            initializeDashboardTable(dashboardTables.departmentTickets, (department.department || 'Department') + ' Ticket Details');
            scrollToSection(departmentTicketTableWrap);
        }

        function renderTicketDetails(categoryKey, activeCard) {
            const detail = ticketDetails[categoryKey] || { label: 'Ticket Details', type: 'tickets', rows: [] };
            const rows = Array.isArray(detail.rows) ? detail.rows : [];

            destroyTicketTables();
            setActiveCard(ticketCards, activeCard);
            if (ticketDetailSection) {
                ticketDetailSection.classList.remove('is-hidden');
            }
            ticketTitle.textContent = detail.label + ' Details';
            ticketEmpty.classList.toggle('is-hidden', rows.length > 0);
            ticketTableWrap.classList.toggle('is-hidden', detail.type !== 'tickets' || rows.length === 0);
            departmentTableWrap.classList.toggle('is-hidden', detail.type !== 'departments' || rows.length === 0);
            if (departmentTicketTableWrap) {
                departmentTicketTableWrap.classList.add('is-hidden');
            }
            if (departmentTicketBody) {
                departmentTicketBody.innerHTML = '';
            }

            if (detail.type === 'departments') {
                activeDepartmentRows = rows;
                ticketBody.innerHTML = '';
                departmentBody.innerHTML = rows.map(function (department, index) {
                    return `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${escapeHtml(department.department || '-')}</td>
                            <td class="text-center">
                                <button type="button" class="department-count-btn js-department-ticket-count" data-department-index="${index}">
                                    ${escapeHtml(department.tickets_count ?? 0)}
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
                initializeDashboardTable(dashboardTables.departments, detail.label + ' Details');
                scrollToSection(ticketDetailSection);
                return;
            }

            activeDepartmentRows = [];
            departmentBody.innerHTML = '';
            ticketBody.innerHTML = ticketRowsHtml(rows);
            initializeDashboardTable(dashboardTables.tickets, detail.label + ' Details');
            scrollToSection(ticketDetailSection);
        }

        function statusClass(status) {
            const normalized = String(status || '').trim().toLowerCase();

            if (normalized === 'completed') {
                return 'completed';
            }

            if (normalized === 'in progress') {
                return 'in-progress';
            }

            if (normalized === 'overdue') {
                return 'overdue';
            }

            return 'pending';
        }

        function flattenTasks(category) {
            const developers = Array.isArray(category && category.developers) ? category.developers : [];

            return developers.flatMap(function (developer) {
                return Array.isArray(developer.tasks) ? developer.tasks : [];
            });
        }

        function renderTaskDetails(dashboardType, categoryKey, activeCard) {
            const categories = dashboardType === 'testing' ? testingTaskCategories : taskCategories;
            const category = categories[categoryKey] || { label: 'Task Details', developers: [] };
            const rows = flattenTasks(category);

            destroyDashboardTable(dashboardTables.tasks);
            setActiveCard(taskCards, activeCard);
            if (taskDetailSection) {
                taskDetailSection.classList.remove('is-hidden');
            }
            taskTitle.textContent = category.label + ' Details';
            taskEmpty.classList.toggle('is-hidden', rows.length > 0);
            if (taskTableWrap) {
                taskTableWrap.classList.toggle('is-hidden', rows.length === 0);
            }
            taskBody.innerHTML = rows.map(function (task, index) {
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
                            <span class="status-pill ${statusClass(task.progress_status)}">${escapeHtml(task.progress_status || '-')}</span>
                        </td>
                        <td>${escapeHtml(task.schedule_status || '-')}</td>
                        <td>${escapeHtml(task.task_status_by_tester || '-')}</td>
                    </tr>
                `;
            }).join('');
            initializeDashboardTable(dashboardTables.tasks, category.label + ' Details');
            scrollToSection(taskDetailSection);
        }

        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activateTab(button.dataset.dashboardTab || 'tickets');
            });
        });

        ticketCards.forEach(function (card) {
            card.addEventListener('click', function (event) {
                event.preventDefault();
                renderTicketDetails(card.dataset.categoryKey || 'total_tickets', card);
            });
        });

        if (departmentTableWrap) {
            departmentTableWrap.addEventListener('click', function (event) {
                const button = event.target.closest('.js-department-ticket-count');

                if (!button) {
                    return;
                }

                event.preventDefault();
                renderDepartmentTickets(Number(button.dataset.departmentIndex || 0));
            });
        }

        taskCards.forEach(function (card) {
            card.addEventListener('click', function (event) {
                event.preventDefault();
                renderTaskDetails(card.dataset.dashboardType || 'task', card.dataset.categoryKey || 'assigned', card);
            });
        });

    });
</script>
@endsection
