@extends('index2')

@section('title', 'Helpdesk Tickets')

@section('content')
@include('tickets.partials.app-theme')


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>




    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

@php

$sessionchargedel = session('charge');
	$sessionuserdel = session('user');
	 //print_r($sessionchargedel);
		//print_r($sessionuserdel);
			    $pageHeading = $isDeveloper ? 'Tickets Assigned to Me' : 'Tickets';
			    $listHeading = $isDeveloper ? 'Tickets Assigned to Me' : 'Ticket List';
        $isPendingTicket = fn ($ticket) => !in_array($ticket->status, ['resolved', 'closed'], true);
	        $isPendingAtNic = fn ($ticket) => ($isSuperAdmin
	            ? in_array($ticket->normalizedForwardedRole(), ['nicadmin', 'developer'], true)
	            : $ticket->normalizedForwardedRole() === 'nicadmin') && $isPendingTicket($ticket);
	        $isPendingAtDeveloper = fn ($ticket) => $ticket->normalizedForwardedRole() === 'developer' && $isPendingTicket($ticket);
	        $isPendingAtStateAdmin = fn ($ticket) => $ticket->normalizedForwardedRole() === 'stateadmin' && $isPendingTicket($ticket);
	        $isPendingAtOther = fn ($ticket) => !in_array($ticket->normalizedForwardedRole(), ['nicadmin', 'developer', 'stateadmin'], true) && $isPendingTicket($ticket);
        $ticketList = collect($tickets);
        $allTicketsCount = $ticketList->count();
		        $pendingAtNicCount = ($isNicAdmin || $isSuperAdmin)
		            ? $ticketList->filter($isPendingAtNic)->count()
		            : 0;
	        $pendingAtDeveloperCount = $isNicAdmin
	            ? $ticketList->filter($isPendingAtDeveloper)->count()
	            : 0;
        $pendingAtStateAdminCount = ($isNicAdmin || $isSuperAdmin)
            ? $ticketList->filter($isPendingAtStateAdmin)->count()
            : 0;
	        $pendingAtOtherCount = ($isNicAdmin || $isSuperAdmin)
	            ? $ticketList->filter($isPendingAtOther)->count()
	            : 0;
	        $resolvedTicketsCount = ($isNicAdmin || $isSuperAdmin)
	            ? $ticketList->where('status', 'resolved')->count()
	            : 0;
	        $closedTicketsCount = ($isNicAdmin || $isSuperAdmin)
	            ? $ticketList->where('status', 'closed')->count()
	            : 0;
	        $completedTicketsCount = $resolvedTicketsCount + $closedTicketsCount;
			@endphp
	<style>
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

    .helpdesk-app-theme .badge-assigned-developer {
        background: #f8fafc;
        color: #0f172a;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.14);
    }

    .helpdesk-app-theme .tickets-table {
        table-layout: auto;
        width: 100%;
    }

	    .helpdesk-app-theme .tickets-table th,
	    .helpdesk-app-theme .tickets-table td {
	        white-space: normal;
	        /* word-break: break-word; */
	        vertical-align: middle;
	    }

	    .helpdesk-app-theme .tickets-cell-stack {
	        display: flex;
	        flex-direction: column;
	        align-items: center;
	        gap: 4px;
	        min-width: 110px;
	    }

	    .helpdesk-app-theme .tickets-cell-main {
	        color: #0f172a;
	        font-weight: 700;
	        line-height: 1.25;
	    }

	    .helpdesk-app-theme .tickets-cell-sub {
	        color: #64748b;
	        font-size: 0.82rem;
	        line-height: 1.25;
	    }

	    .helpdesk-app-theme .badge-ticket-return {
	        background: #ffe4e6;
	        color: #be123c;
	        font-size: 0.72rem;
	        font-weight: 700;
	        line-height: 1;
	    }

	    .helpdesk-app-theme .badge-ticket-watchlist {
	        background: #174a7c;
	        color: #ffffff;
	        border: 1px solid #174a7c;
	        font-size: 0.72rem;
	        font-weight: 800;
	        line-height: 1;
	    }

    .helpdesk-app-theme .dataTables_wrapper .row:first-child,
    .helpdesk-app-theme .dataTables_wrapper .row:last-child {
        margin: 0;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_length,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_filter {
        margin-bottom: 14px;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_length {
        display: none !important;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_length label,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_filter label,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_info {
        color: #495057;
        font-size: 0.95rem;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_filter input,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_length select {
        min-height: 38px;
        border-radius: 0.375rem;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_paginate {
        margin-top: 14px;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
        border-radius: 0.375rem;
    }

    .helpdesk-app-theme .tickets-table-toolbar {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .helpdesk-app-theme .tickets-table-toolbar label {
        margin-bottom: 0;
        color: #495057;
        font-size: 0.95rem;
        flex: 0 0 auto;
    }

    .helpdesk-app-theme .tickets-filter-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        flex-wrap: wrap;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .helpdesk-app-theme .tickets-filter-title {
        margin: 0;
        color: #334155;
        font-size: 0.92rem;
        font-weight: 700;
    }

    .helpdesk-app-theme .tickets-status-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .helpdesk-app-theme .tickets-status-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 38px;
        padding: 0.4rem 0.85rem;
        border: 1px solid #c7d2fe;
        border-radius: 8px;
        background: #ffffff;
        color: #3454f5;
        font-weight: 700;
        font-size: 0.86rem;
        line-height: 1.1;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
    }

    .helpdesk-app-theme .tickets-status-tab:hover {
        border-color: #5b7cfa;
        background: #eef2ff;
        color: #2447ea;
    }

    .helpdesk-app-theme .tickets-status-tab.is-active {
        border-color: #5476f7;
        background: #5a7df7;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(84, 118, 247, 0.28);
    }

    .helpdesk-app-theme .tickets-status-tab .badge {
        min-width: 28px;
        padding: 0.32rem 0.5rem;
        border-radius: 999px;
        background: #5a7df7 !important;
        color: #ffffff !important;
        font-size: 0.76rem;
    }

    .helpdesk-app-theme .tickets-status-tab.is-active .badge {
        background: #ffffff !important;
        color: #4f70f6 !important;
    }

    .helpdesk-app-theme .tickets-length-select {
        width: 90px;
        min-width: 90px;
        max-width: 90px;
        min-height: 38px;
        border-radius: 0.375rem;
        flex: 0 0 90px;
    }

    .helpdesk-app-theme .tickets-list-shell {
        position: relative;
    }

    .helpdesk-app-theme .tickets-list-shell.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Sorting arrow indicators (muted) positioned at right corner */
    .helpdesk-app-theme table thead th {
        position: relative;
        padding-right: 22px; /* space for arrow */
    }

    .helpdesk-app-theme table thead th.sorting-asc::after,
    .helpdesk-app-theme table thead th.sorting-desc::after {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: #797f85; /* muted */
        font-size: 0.75em;
        line-height: 1;
        display: inline-block;
    }

    .helpdesk-app-theme table thead th.sorting-asc::after {
        content: '\25B2'; /* ▲ */
    }

    .helpdesk-app-theme table thead th.sorting-desc::after {
        content: '\25BC'; /* ▼ */
    }
    .tickets-index-redesign .helpdesk-main-content {
        background: #eef4fa;
        border-radius: 14px;
        padding: 18px;
        box-shadow: none;
    }

    .tickets-index-redesign .tickets-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
        padding: 14px 16px;
        border: 1px solid #cfe0f2;
        border-left: 8px solid var(--primary-color);
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(32, 78, 125, 0.06);
    }

    .tickets-index-redesign .page-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #10233f;
        font-size: 1.35rem;
        font-weight: 900;
    }

    .tickets-index-redesign .page-title i {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #e8f3ff;
        color: var(--primary-color) !important;
        font-size: 1.1rem;
    }

    .tickets-index-redesign .tickets-header-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tickets-index-redesign .tickets-header-actions .btn {
        min-height: 36px;
        padding: 7px 13px;
        border-radius: 8px;
        font-weight: 800;
    }

    .tickets-index-redesign .tickets-list-panel {
        border: 1px solid #d4e3f2;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(32, 78, 125, 0.06);
        overflow: hidden;
    }

    .tickets-index-redesign .tickets-list-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--primary-color);
        border-bottom: 1px solid #dbe7f3;
    }

    .tickets-index-redesign .tickets-list-panel-header h5 {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 900;
    }

    .tickets-index-redesign .tickets-list-panel-body {
        padding: 14px 16px 16px;
    }

    .tickets-index-redesign .tickets-filter-panel {
        display: block;
        margin-bottom: 12px;
        padding: 12px;
        border: 1px solid #d4e3f2;
        border-radius: 10px;
        background: #f8fbff;
        box-shadow: none;
    }

    .tickets-index-redesign .tickets-filter-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 10px;
        color: #10233f;
        font-size: 0.9rem;
        font-weight: 900;
    }

    .tickets-index-redesign .tickets-filter-title i {
        color: var(--primary-color);
    }

    .tickets-index-redesign .tickets-status-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 8px;
    }

    .tickets-index-redesign .tickets-status-tab {
        justify-content: space-between;
        min-height: 42px;
        padding: 8px 10px;
        border: 1px solid #cfe0f2;
        border-radius: 8px;
        background: #ffffff;
        color: #24496d;
        box-shadow: none;
    }

    .tickets-index-redesign .tickets-status-tab:hover {
        border-color: #7eb4e5;
        background: #eef7ff;
        color: var(--primary-color);
    }

    .tickets-index-redesign .tickets-status-tab.is-active {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: #ffffff;
        box-shadow: 0 8px 16px rgba(22, 63, 104, 0.18);
    }

    .tickets-index-redesign .tickets-filter-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
        text-align: left;
    }

    .tickets-index-redesign .tickets-status-tab .badge {
        min-width: 30px;
        padding: 5px 8px;
        border-radius: 999px;
        background: #e8f3ff !important;
        color: var(--primary-color) !important;
    }

    .tickets-index-redesign .tickets-status-tab.is-active .badge {
        background: #ffffff !important;
        color: var(--primary-color) !important;
    }

    .tickets-index-redesign .tickets-table-toolbar {
        margin-bottom: 10px;
        padding: 9px 10px;
        border: 1px solid #dbe7f3;
        border-radius: 8px;
        background: #ffffff;
    }

    .tickets-index-redesign .tickets-length-select {
        width: 78px;
        min-width: 78px;
        max-width: 78px;
        min-height: 34px;
        border-radius: 8px;
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .tickets-index-redesign .dataTables_wrapper .row:first-child {
        align-items: center;
        margin: 0 0 10px;
    }

    .tickets-index-redesign .dataTables_wrapper .row:first-child > [class*="col-"] {
        width: auto;
        flex: 0 0 auto;
    }

    .tickets-index-redesign .dataTables_wrapper .row:first-child > [class*="col-"]:last-child {
        margin-left: auto;
    }

    .tickets-index-redesign .dataTables_wrapper .dataTables_filter {
        margin: 0;
    }

    .tickets-index-redesign .dataTables_wrapper .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        font-size: 0.9rem;
        font-weight: 800;
    }

    .tickets-index-redesign .dataTables_wrapper .dataTables_filter input {
        min-height: 34px;
        width: 230px;
        margin-left: 0;
        border: 1px solid #b9d0e7;
        border-radius: 8px;
        background: #ffffff;
    }

    .tickets-index-redesign .tickets-list-shell {
        border: 1px solid #d4e3f2;
        border-radius: 8px;
        background: #ffffff;
        overflow: hidden;
    }

    .tickets-index-redesign .tickets-list-shell .table-responsive {
        margin-top: 0;
    }

    .tickets-index-redesign .tickets-table {
        margin-bottom: 0 !important;
        border: 0;
        border-radius: 0;
    }

    .tickets-index-redesign .tickets-table thead {
        background: #6f6f6f;
    }

    .tickets-index-redesign .tickets-table thead th {
        padding: 10px 8px;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 900;
        white-space: normal;
    }

    .tickets-index-redesign .tickets-table tbody td {
        padding: 9px 8px;
        font-size: 0.84rem;
    }

    .tickets-index-redesign .tickets-table tbody tr:nth-child(even) {
        background: #f4f8fc;
    }

    .tickets-index-redesign .tickets-table tbody tr:hover {
        background: #edf6ff;
    }

    .tickets-index-redesign .tickets-cell-stack {
        align-items: flex-start;
        text-align: left;
    }

    .tickets-index-redesign .tickets-cell-main {
        font-size: 0.86rem;
    }

    .tickets-index-redesign .tickets-cell-sub {
        font-size: 0.76rem;
    }

    .tickets-index-redesign .dataTables_wrapper .dataTables_info {
        color: #475569;
        font-size: 0.88rem;
    }

    .tickets-index-redesign .dataTables_wrapper .page-item .page-link {
        min-width: 34px;
        padding: 5px 9px;
        border-radius: 6px;
        border-color: #d1dce8;
        background: #ffffff;
        color: var(--primary-color);
        font-weight: 800;
    }

    .tickets-index-redesign .dataTables_wrapper .page-item.active .page-link {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .tickets-index-redesign .tickets-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .tickets-index-redesign .tickets-header-actions {
            justify-content: flex-start;
        }

        .tickets-index-redesign .tickets-status-tabs {
            grid-template-columns: 1fr;
        }

        .tickets-index-redesign .dataTables_wrapper .dataTables_filter input {
            width: 100%;
        }
    }
</style>
<div class="helpdesk-app-theme tickets-index-redesign">
    <div class="helpdesk-main-content">
        <div class="container-fluid px-0">
	            <div class="tickets-page-header">
	                <h1 class="page-title">
	                    <i class="bi bi-ticket-perforated text-primary"></i> {{ $pageHeading }}
	                </h1>
	                <div class="tickets-header-actions">
                    @if ($isSuperAdmin)
                        <a href="{{ route('helpdesk.tickets.index', ['view' => 'all']) }}" class="btn btn-light">All</a>
                        <a href="{{ route('helpdesk.tickets.index', ['view' => 'forwarded']) }}" class="btn btn-outline-secondary">Returned From NIC <span class="badge bg-danger ms-1">{{ $forwardedCount }}</span></a>
                    @endif
                    @if ($isNicAdmin || $isDeveloper)
                        <a href="{{ route('helpdesk.tasks.dashboard') }}" class="btn btn-outline-primary">Task Tracker</a>
                    @endif
                    @unless ($isDeveloper)
                        <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-primary text-light">
                            <i class="bi bi-plus-circle"></i> Create Ticket
                        </a>
                    @endunless
                </div>
	            </div>

	            <div class="tickets-list-panel">
	                <div class="tickets-list-panel-header">
	                    <h5 class="mb-0 text-light">{{ $listHeading }}</h5>
	                </div>
			                <div class="tickets-list-panel-body">
		                    @if ($isNicAdmin || $isSuperAdmin)
		                                <div class="tickets-filter-panel">
		                                    <p class="tickets-filter-title"><i class="bi bi-funnel"></i>Ticket Filters</p>
		                                    <div class="tickets-status-tabs" role="tablist" aria-label="Ticket filters">
			                                        <button type="button" class="tickets-status-tab is-active" data-ticket-filter="all">
			                                            <span class="tickets-filter-label"><i class="bi bi-collection"></i>Total Tickets</span>
			                                            <span class="badge">{{ $allTicketsCount }}</span>
			                                        </button>
			                                        <button type="button" class="tickets-status-tab" data-ticket-filter="pending-nic">
			                                            <span class="tickets-filter-label"><i class="bi bi-person-check"></i>Pending at NICAdmin</span>
			                                            <span class="badge">{{ $pendingAtNicCount }}</span>
			                                        </button>
			                                        {{-- <button type="button" class="tickets-status-tab" data-ticket-filter="pending-other">
			                                            Other
			                                            <span class="badge">{{ $pendingAtOtherCount }}</span>
			                                        </button> --}}
			                                            @if ($isSuperAdmin)
					                                                <button type="button" class="tickets-status-tab" data-ticket-filter="pending-stateadmin">
					                                                    <span class="tickets-filter-label"><i class="bi bi-person-badge"></i>Pending at StateAdmin</span>
					                                                    <span class="badge">{{ $pendingAtStateAdminCount }}</span>
				                                                </button>
			                                                <button type="button" class="tickets-status-tab" data-ticket-filter="completed">
			                                                    <span class="tickets-filter-label"><i class="bi bi-check2-circle"></i>Resolved / Closed</span>
			                                                    <span class="badge">{{ $completedTicketsCount }}</span>
		                                                    </button>
	                                            @else
		                                        <button type="button" class="tickets-status-tab" data-ticket-filter="pending-developer">
		                                            <span class="tickets-filter-label"><i class="bi bi-code-slash"></i>Pending at Developer</span>
		                                            <span class="badge">{{ $pendingAtDeveloperCount }}</span>
		                                        </button>
			                                        <button type="button" class="tickets-status-tab" data-ticket-filter="pending-stateadmin">
			                                            <span class="tickets-filter-label"><i class="bi bi-person-badge"></i>Pending at StateAdmin</span>
			                                            <span class="badge">{{ $pendingAtStateAdminCount }}</span>
			                                        </button>
				                                        <button type="button" class="tickets-status-tab" data-ticket-filter="completed">
				                                            <span class="tickets-filter-label"><i class="bi bi-check2-circle"></i>Resolved / Closed</span>
				                                            <span class="badge">{{ $completedTicketsCount }}</span>
				                                        </button>
	                                            @endif
	                                    </div>
	                                </div>
			                    @endif
	                    <div class="tickets-table-toolbar">
                        <label for="ticketsLengthControl">Show</label>
                        <select id="ticketsLengthControl" class="form-select form-select-sm tickets-length-select mb-0">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label for="ticketsLengthControl">entries</label>
                    </div>
                    <div id="ticketsListShell" class="tickets-list-shell">
                        <div class="table-responsive">
                            <table id="ticketsTable" class="table w-100 table-striped table-bordered display tickets-table datatables-basic">
                                <thead>
	                                    <tr>
	                                        <th class="lang align-middle text-center">S.No</th>
		                                        <th class="lang align-middle text-center text-wrap">Ticket / Date of Creation</th>
		                                        <th class="lang align-middle text-center text-wrap">Reopened On</th>
		                                        <th class="lang align-middle text-center text-wrap">Department / Type</th>
		                                        <th class="lang align-middle text-center">Category</th>
		                                        <th class="lang align-middle text-center text-wrap">Subject</th>
		                                        <th class="lang align-middle text-center">Created By</th>
		                                        <th class="lang align-middle text-center text-wrap">Currently with On</th>
		                                        <th class="lang align-middle text-center">Priority</th>
		                                        @if ($isNicAdmin || $isDeveloper)
		                                            <th class="lang align-middle text-center text-wrap">Tech Team Status</th>
		                                        @endif
		                                        <th class="lang align-middle text-center">Current Status</th>
				                                        @if ($isNicAdmin)
		                                            <th class="lang align-middle text-center text-wrap">Assign To Developer</th>
		                                            <th class="lang align-middle text-center">Done By</th>
		                                        @endif
		                                        <th class="lang align-middle text-center text-wrap">Last Updated On</th>
		                                        <th class="lang align-middle text-center">Action</th>
	                                    </tr>
                                </thead>
                                <tbody>
		                                    @foreach ($tickets as $ticket)
	                                                @php
	                                                    $reopenedOn = $ticket->reopenedOn();
	                                                    $currentHolderLabel = $ticket->currentHolderLabel();
	                                                    $assignedDeveloperLabel = $ticket->assignedDeveloperLabel();
	                                                    $currentHolderDisplay = $currentHolderLabel;

	                                                    if (($isNicAdmin || $isDeveloper) && $assignedDeveloperLabel !== '-') {
	                                                        $currentHolderDisplay = $currentHolderLabel.' ('.$assignedDeveloperLabel.')';
	                                                    }

	                                                    $isWatchlistedTicket = $ticket->assignments->contains(function ($assignment) use ($isNicAdmin, $isDeveloper) {
	                                                        if (($assignment->status ?? null) !== 'watchlist') {
	                                                            return false;
	                                                        }

	                                                        if ($isNicAdmin) {
	                                                            return true;
	                                                        }

	                                                        return $isDeveloper
	                                                            && (string) $assignment->developer_userid === (string) \App\Support\HelpdeskSession::userId();
	                                                    });
	                                                @endphp
				                                        <tr
			                                                data-current-holder="{{ $currentHolderDisplay }}"
                                                    data-pending-role="{{ $ticket->normalizedForwardedRole() }}"
                                                    data-ticket-status="{{ $ticket->status }}"
	                                                data-tech-team-status="{{ $ticket->tech_team_status }}"
	                                            >
	                                            <td class="lang align-middle text-center text-wrap">{{ $loop->iteration }}</td>
		                                            <td class="lang align-middle text-center text-wrap" data-order="{{ $ticket->created_at?->timestamp ?? 0 }}">
		                                                <div class="tickets-cell-stack">
			                                                    <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="tickets-cell-main text-decoration-none">
			                                                        {{ $ticket->ticket_number }}
			                                                    </a>
				                                                    @if (($isNicAdmin || $isDeveloper) && $ticket->isReturnedDeveloperReassignment())
				                                                        <span class="badge badge-ticket-return">Return</span>
				                                                    @endif
				                                                    @if ($isWatchlistedTicket)
				                                                        <span class="badge badge-ticket-watchlist">Watchlist</span>
				                                                    @endif
			                                                    <span class="tickets-cell-sub">{{ $ticket->created_at?->format('d/n/Y h:i A') ?? '-' }}</span>
			                                                </div>
		                                            </td>
		                                            <td class="lang align-middle text-center text-wrap" data-order="{{ $reopenedOn?->timestamp ?? 0 }}">
		                                                {{ $reopenedOn?->format('d/n/Y h:i A') ?? '-' }}
		                                            </td>
			                                            <td class="lang align-middle text-center text-wrap">
			                                                <div class="tickets-cell-stack">
			                                                    <span class="tickets-cell-main">{{ $ticket->department_name ?: '-' }}</span>
			                                                    <span class="tickets-cell-sub">{{ $ticket->requestTypeLabel() }}</span>
			                                                </div>
			                                            </td>
		                                            <td class="lang align-middle text-center text-wrap">{{ $ticket->category ?: '-' }}</td>
		                                            <td class="lang align-middle text-center text-wrap">{{ $ticket->subject ?: '-' }}</td>
		                                            <td class="lang align-middle text-center text-wrap">{{ \App\Support\HelpdeskSession::normalizeUserName($ticket->user_name) ?: '-' }}</td>
			                                            <td class="lang align-middle text-center text-wrap">
			                                                {{ $currentHolderDisplay }}
			                                            </td>
		                                            <td class="lang align-middle text-center">
		                                                <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
	                                            </td>
                                            @if ($isNicAdmin || $isDeveloper)
                                                <td class="lang align-middle text-center text-wrap">
                                                    @if ($ticket->tech_team_status !== '-')
                                                        <span class="badge badge-status-{{ $ticket->tech_team_status }}">
                                                            {{ ucfirst(str_replace('_', ' ', $ticket->tech_team_status)) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
		                                                </td>
		                                            @endif
		                                            <td class="lang align-middle text-center text-wrap">
		                                                <span class="badge badge-status-{{ $ticket->status }}">
		                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
		                                                </span>
		                                            </td>
		                                            @if ($isNicAdmin)
                                                <td class="lang align-middle text-center text-wrap">
	                                                    <span class="badge badge-assigned-developer">{{ $assignedDeveloperLabel }}</span>
                                                </td>
                                                <td class="lang align-middle text-center text-wrap">{{ $ticket->completedByLabel() }}</td>
	                                            @endif
				                                            <td class="lang align-middle text-center text-wrap" data-order="{{ $ticket->updated_at?->timestamp ?? 0 }}">
				                                                {{ $ticket->updated_at?->format('d/n/Y h:i A') ?? '-' }}
				                                            </td>
			                                            <td class="lang align-middle text-center text-wrap">
	                                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="btn text-dark" title="View Ticket">
	                                                    <i class="bi bi-eye fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(document).ready(function () {
            const tableSelector = '#ticketsTable';
            const tableBodySelector = tableSelector + ' tbody';
            const lengthControl = $('#ticketsLengthControl');
            const allTicketsFilter = 'all';
            const allTicketRows = $(tableBodySelector + ' tr').toArray();
            let selectedPageLength = parseInt(lengthControl.val(), 10) || 10;
            let activeTicketFilter = allTicketsFilter;
            let ticketsTable = null;
            const nonSortableColumns = [
	                0,
	                @if ($isNicAdmin)
	                11,
	                @endif
	                -1
            ];

            const setTicketFilterTabState = function (filterName) {
                $('.tickets-status-tab').each(function () {
                    const isActive = $(this).data('ticket-filter') === filterName;

                    $(this).toggleClass('is-active', isActive);
                });
            };

            const getRowsForFilter = function (filterName) {
                return allTicketRows.filter(function (row) {
                    const rowData = $(row).data();

                    if (filterName === allTicketsFilter) {
                        return true;
                    }

			                    if (filterName === 'pending-nic') {
			                        @if ($isSuperAdmin)
			                            return ['nicadmin', 'developer'].includes(rowData.pendingRole) && !['resolved', 'closed'].includes(rowData.ticketStatus);
			                        @else
			                            return rowData.pendingRole === 'nicadmin' && !['resolved', 'closed'].includes(rowData.ticketStatus);
			                        @endif
			                    }

	                    if (filterName === 'pending-developer') {
	                        return rowData.pendingRole === 'developer' && !['resolved', 'closed'].includes(rowData.ticketStatus);
	                    }

		                    if (filterName === 'pending-stateadmin') {
		                        return rowData.pendingRole === 'stateadmin' && !['resolved', 'closed'].includes(rowData.ticketStatus);
		                    }

		                    if (filterName === 'pending-other') {
		                        return !['nicadmin', 'developer', 'stateadmin'].includes(rowData.pendingRole) && !['resolved', 'closed'].includes(rowData.ticketStatus);
		                    }

	                    if (filterName === 'completed') {
	                        return ['resolved', 'closed'].includes(rowData.ticketStatus);
	                    }

                    return true;
                });
            };

            const buildTicketsTable = function (rows) {
                if ($.fn.DataTable.isDataTable(tableSelector)) {
                    $(tableSelector).DataTable().destroy();
                }

                $(tableBodySelector).empty().append(rows);

                ticketsTable = $(tableSelector).DataTable({
                    autoWidth: false,
                    responsive: false,
                    pageLength: selectedPageLength,
                    lengthChange: true,
                    searching: true,
                    info: true,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [],
                    columnDefs: [
                        {
                            targets: '_all',
                            className: 'align-middle text-center'
                        },
                        {
                            targets: nonSortableColumns,
                            orderable: false
                        }
                    ],
                    language: {
                        emptyTable: 'No tickets found.'
                    },
                    infoCallback: function (settings, start, end, max, total) {
                        const firstRow = total === 0 ? 0 : start;

                        return 'Showing ' + firstRow + ' to ' + end + ' of ' + total + ' entries';
                    },
                    initComplete: function () {
                        const api = this.api();

                        if (lengthControl.length) {
                            selectedPageLength = api.page.len();
                            lengthControl.val(String(api.page.len()));
                        }
                    },
                    drawCallback: function () {
                        const api = this.api();
                        $('#ticketsTable_wrapper .pagination').addClass('justify-content-end');

                        if (lengthControl.length) {
                            lengthControl.val(String(api.page.len()));
                        }
                    }
                });

                if (lengthControl.length) {
                    lengthControl.off('change.ticketsLength').on('change.ticketsLength', function () {
                        selectedPageLength = parseInt($(this).val(), 10) || 10;
                        ticketsTable.page.len(selectedPageLength).draw(false);
                    });
                }
            };

            const applyTicketFilter = function (filterName) {
                activeTicketFilter = filterName;
                buildTicketsTable(getRowsForFilter(activeTicketFilter));
                setTicketFilterTabState(activeTicketFilter);
            };

            if (!$(tableSelector).length) {
                return;
            }

            buildTicketsTable(getRowsForFilter(activeTicketFilter));

	            @if ($isNicAdmin || $isSuperAdmin)
	                setTicketFilterTabState(allTicketsFilter);

	                $('.tickets-status-tab').off('click.ticketsFilter').on('click.ticketsFilter', function () {
                    applyTicketFilter($(this).data('ticket-filter'));
                });
            @endif
        });
    </script>
@endsection
