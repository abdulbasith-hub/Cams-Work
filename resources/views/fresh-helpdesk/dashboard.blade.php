@extends('index2')

@section('title', 'Helpdesk Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

@php
	    $fmt = function ($value) {
	        if (!$value) {
	            return '-';
	        }

        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y h:i A');
        } catch (\Throwable $e) {
            return $value;
	        }
	    };

		    $ageingDays = function ($value) {
			        if (!$value) {
			            return '-';
			        }

			        try {
			            return \Carbon\Carbon::parse($value, 'Asia/Kolkata')
			                ->diffForHumans(\Illuminate\Support\Facades\View::shared('get_nowtime'), \Carbon\CarbonInterface::DIFF_ABSOLUTE, false, 2).' ago';
			        } catch (\Throwable $e) {
			            return '-';
			        }
			    };

	    $dateOrderValue = function ($value) {
	        if (!$value) {
	            return 0;
	        }

	        try {
	            return \Carbon\Carbon::parse($value, 'Asia/Kolkata')->timestamp;
	        } catch (\Throwable $e) {
	            return 0;
	        }
	    };

	    $ticketStatus = fn ($status) => \App\Models\FreshHelpdesk::ticketStatusLabel($status);
	    $taskStatus = fn ($status) => $taskStatusLabels[$status] ?? \Illuminate\Support\Str::headline((string) $status);
	    $activePane = $activePane ?? 'tickets';
	    $canViewTaskDashboard = $canViewTaskDashboard ?? false;
	    $canViewDeveloperSummary = $canViewDeveloperSummary ?? false;
	    $activeTicketCard = $activeTicketCard ?? 'in_progress';
	    $activeTaskCard = $activeTaskCard ?? 'total';
	    $selectedDashboardDeveloperId = $selectedDashboardDeveloperId ?? '';
	    $selectedDashboardDeveloper = $selectedDashboardDeveloper ?? null;
	    $developerOptions = $developerOptions ?? collect();
	    $importantTickets = $importantTickets ?? collect();
	    $importantTicketsCount = $importantTicketsCount ?? 0;
	    $ticketDashboardStats = $ticketDashboardStats ?? [];
	    $taskDashboardStats = $taskDashboardStats ?? [];
	    $developerTicketDashboardStats = $developerTicketDashboardStats ?? [];
	    $developerTaskDashboardStats = $developerTaskDashboardStats ?? [];
	    $developerTickets = $developerTickets ?? collect();
	    $developerTasks = $developerTasks ?? collect();
	    $activeDeveloperTicketCard = $activeDeveloperTicketCard ?? 'total';
	    $activeDeveloperTaskCard = $activeDeveloperTaskCard ?? 'total';
	    $dashboardFilters = $dashboardFilters ?? ['search' => '', 'priority' => '', 'status' => '', 'developer_userid' => ''];
	    $dashboardTicketResultCount = (int) ($ticketDashboardStats[$activeTicketCard] ?? (isset($tickets)
	        ? (method_exists($tickets, 'total') ? $tickets->total() : $tickets->count())
	        : 0));
	    $dashboardStatusOptions = $dashboardStatusOptions ?? \App\Models\FreshHelpdesk::ticketStatusFilterLabels();
		    $priorityOptions = collect($priorityOptions ?? ['Low', 'Medium', 'High', 'Critical'])->filter()->values();
		    if ($priorityOptions->isEmpty()) {
		        $priorityOptions = collect(['Low', 'Medium', 'High', 'Critical']);
		    }

	    $ticketCards = [
	        ['key' => 'total', 'label' => 'Total Tickets', 'hint' => 'All visible tickets', 'icon' => 'ti ti-ticket', 'tone' => 'blue'],
	        ['key' => 'in_progress', 'label' => 'In Progress', 'hint' => 'Currently working', 'icon' => 'ti ti-loader-2', 'tone' => 'indigo'],
	        ['key' => 'urgent', 'label' => 'Urgent', 'hint' => 'Urgent priority queue', 'icon' => 'ti ti-alert-triangle', 'tone' => 'red'],
	        ['key' => 'returned', 'label' => 'Need Clarification', 'hint' => 'Clarification required', 'icon' => 'ti ti-arrow-back-up', 'tone' => 'violet'],
	        ['key' => 'resolved_closed', 'label' => 'Resolved / Closed', 'hint' => 'Completed tickets', 'icon' => 'ti ti-circle-check', 'tone' => 'green'],
	    ];
	    if (($role ?? null) === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN) {
	        array_splice($ticketCards, 3, 0, [[
	            'key' => 'developer_side',
	            'label' => 'In Developers',
	            'hint' => 'Pending at Developers',
	            'icon' => 'ti ti-code',
	            'tone' => 'violet',
	        ]]);
	    }
		    $taskCards = [
	        ['key' => 'total', 'label' => 'Total Tasks', 'hint' => 'All visible tasks', 'icon' => 'ti ti-list-check', 'tone' => 'blue'],
        ['key' => 'in_progress', 'label' => 'In Progress', 'hint' => 'Currently working', 'icon' => 'ti ti-loader-2', 'tone' => 'indigo'],
        ['key' => 'urgent', 'label' => 'Urgent', 'hint' => 'Past expected date', 'icon' => 'ti ti-alert-triangle', 'tone' => 'red'],
        ['key' => 'returned', 'label' => 'Returned', 'hint' => 'Returned for review', 'icon' => 'ti ti-arrow-back-up', 'tone' => 'violet'],
	        ['key' => 'resolved_closed', 'label' => 'Resolved / Closed', 'hint' => 'Completed tasks', 'icon' => 'ti ti-circle-check', 'tone' => 'green'],
	    ];
		    $ticketGridTitle = collect($ticketCards)->firstWhere('key', $activeTicketCard)['label'] ?? 'In Progress';
		    $taskGridTitle = collect($taskCards)->firstWhere('key', $activeTaskCard)['label'] ?? 'Total Tasks';
		    $dashboardUrl = route('fresh-helpdesk.dashboard');
		    $dashboardStateJson = function (array $state) {
		        $state = array_filter($state, fn ($value) => $value !== null && $value !== '');

		        return e(json_encode($state, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT));
		    };
		    $windowedPageNumbers = function ($paginator, int $edge = 3, int $window = 1) {
		        $lastPage = $paginator->lastPage();
		        $current = $paginator->currentPage();
		        $items = [];
		        $lastShown = 0;

		        for ($page = 1; $page <= $lastPage; $page++) {
		            if ($page <= $edge || $page > $lastPage - $edge || abs($page - $current) <= $window) {
		                if ($lastShown && $page - $lastShown > 1) {
		                    $items[] = '...';
		                }
		                $items[] = $page;
		                $lastShown = $page;
		            }
		        }

		        return $items;
		    };
		@endphp

<style>
    .fh-dashboard-page {
        max-width: 100%;
    }

    .fh-dashboard-head {
        align-items: center;
        background: #fff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        display: flex;
        gap: 14px;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 14px 16px;
    }

    .fh-dashboard-head span,
    .fh-grid-title span {
        color: #4b5f80;
        display: block;
        font-size: 12px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .fh-dashboard-head h4 {
        color: #06122f;
        font-size: 19px;
        font-weight: 900;
        margin: 2px 0 0;
    }

    .fh-switch {
        background: #174c78;
        border-radius: 999px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
        display: inline-flex;
        gap: 5px;
        padding: 5px;
    }

    .fh-switch a {
        align-items: center;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 13px;
        font-weight: 850;
        gap: 7px;
        min-height: 36px;
        padding: 0 18px;
        text-decoration: none;
    }

    .fh-switch a.is-active {
        background: #fff;
        color: #06122f;
    }

    .fh-switch a.is-disabled {
        opacity: .45;
        pointer-events: none;
    }

    .fh-bell-btn {
        align-items: center;
        background: #fff;
        border: 1px solid #d8e2f0;
        border-radius: 999px;
        color: #4b5f80;
        display: inline-flex;
        font-size: 18px;
        height: 40px;
        justify-content: center;
        position: relative;
        width: 40px;
    }

    .fh-bell-btn:hover {
        background: #f4f8ff;
        color: #2f6ee8;
    }

    .fh-bell-badge {
        align-items: center;
        background: #b4233a;
        border: 2px solid #fff;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 10px;
        font-weight: 850;
        height: 19px;
        justify-content: center;
        min-width: 19px;
        padding: 0 4px;
        position: absolute;
        right: -4px;
        top: -4px;
    }

    .fh-bell-menu {
        border: 1px solid #d8e2f0;
        border-radius: 10px;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.14);
        max-height: 380px;
        overflow-y: auto;
        padding: 0;
        width: 340px;
    }

    .fh-bell-menu-header {
        border-bottom: 1px solid #eef1f6;
        color: #06122f;
        font-size: 13px;
        font-weight: 850;
        padding: 12px 14px;
        position: sticky;
        top: 0;
        background: #fff;
    }

    .fh-bell-item {
        border-bottom: 1px solid #eef1f6;
        color: inherit;
        display: block;
        padding: 10px 14px;
        text-decoration: none;
    }

    .fh-bell-item:last-child {
        border-bottom: none;
    }

    .fh-bell-item:hover {
        background: #f8fbff;
    }

    .fh-bell-item-title {
        color: #06122f;
        display: block;
        font-size: 13px;
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fh-bell-item-meta {
        color: #64748b;
        display: block;
        font-size: 11.5px;
        margin-top: 2px;
    }

    .fh-bell-empty {
        color: #64748b;
        font-size: 12.5px;
        padding: 18px 14px;
        text-align: center;
    }

    .fh-task-link {
        color: #06122f !important;
        text-decoration: none;
    }

    .fh-task-link:hover {
        color: #174ea6 !important;
        text-decoration: underline;
    }

    .fh-pane[hidden] {
        display: none !important;
    }

    .fh-filter-card {
        background: #f8fbff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        margin-bottom: 16px;
        padding: 14px;
    }

    .fh-filter-card .form-control,
    .fh-filter-card .form-select {
        border-color: #d6e1ef;
        border-radius: 7px;
        font-size: 13px;
        min-height: 42px;
    }

	    .fh-filter-card .btn {
	        border-radius: 7px;
	        font-weight: 800;
	        min-height: 42px;
	        white-space: nowrap;
	    }

	    .fh-filter-count {
	        align-items: center;
	        background: rgba(255, 255, 255, 0.92);
	        border-radius: 999px;
	        color: #1d4ed8;
	        display: inline-flex;
	        font-size: 11px;
	        font-weight: 800;
	        justify-content: center;
	        line-height: 1;
	        margin-left: 6px;
	        min-height: 20px;
	        min-width: 24px;
	        padding: 0 6px;
	        white-space: nowrap;
	    }

    .fh-card-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(5, minmax(170px, 1fr));
        margin-bottom: 16px;
    }

    .fh-card-grid.is-nic-ticket-grid {
        grid-template-columns: repeat(6, minmax(145px, 1fr));
    }

	    .fh-stat-card {
	        background: #fff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        min-height: 126px;
        padding: 15px 16px;
	        position: relative;
	        text-align: left;
	        text-decoration: none;
	        width: 100%;
	    }

	    .fh-stat-card:hover {
	        color: inherit;
	        text-decoration: none;
	    }

    .fh-stat-card.is-active {
        border-color: #2f6ee8;
        box-shadow: 0 12px 28px rgba(47, 110, 232, 0.18);
        outline: 2px solid #2f6ee8;
        outline-offset: -2px;
    }

    .fh-stat-card label {
        color: #0b2a55;
        display: block;
        font-size: 13px;
        font-weight: 900;
        margin: 0;
        padding-right: 46px;
        text-transform: uppercase;
    }

    .fh-stat-card strong {
        color: #06122f;
        display: block;
        font-size: 34px;
        font-weight: 900;
        line-height: 1;
        margin-top: 16px;
    }

    .fh-stat-card small {
        color: #4b5f80;
        display: block;
        font-size: 12px;
        font-weight: 750;
        margin-top: 13px;
    }

    .fh-stat-icon {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        position: absolute;
        right: 14px;
        top: 12px;
        width: 38px;
    }

    .fh-tone-blue .fh-stat-icon,
    .fh-tone-indigo .fh-stat-icon {
        background: #eaf2ff;
        color: #2f6ee8;
    }

    .fh-tone-red .fh-stat-icon {
        background: #ffe4e6;
        color: #dc2626;
    }

    .fh-tone-violet .fh-stat-icon {
        background: #eee7ff;
        color: #7c3aed;
    }

    .fh-tone-green .fh-stat-icon {
        background: #dcfce7;
        color: #059669;
    }

    .fh-active-pill {
        background: #2f6ee8;
        border-radius: 999px;
        bottom: 52px;
        color: #fff;
        display: none;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
        padding: 6px 8px;
        position: absolute;
        right: 14px;
    }

    .fh-stat-card.is-active .fh-active-pill {
        display: inline-flex;
    }

	    .fh-grid-panel {
	        background: #fff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 16px;
	        padding: 14px;
	    }

	    .fh-developer-panel {
	        background: #fff;
	        border: 1px solid #d8e2f0;
	        border-radius: 8px;
	        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
	        margin-bottom: 16px;
	        padding: 14px;
	    }

	    .fh-developer-panel.is-loading,
	    .fh-grid-panel.is-loading {
	        opacity: .65;
	        pointer-events: none;
	    }

	    .fh-developer-head {
	        align-items: center;
	        display: flex;
	        flex-wrap: wrap;
	        gap: 12px;
	        justify-content: space-between;
	        margin-bottom: 12px;
	    }

	    .fh-developer-head h5 {
	        color: #06122f;
	        font-size: 17px;
	        font-weight: 900;
	        margin: 0;
	    }

	    .fh-developer-head span {
	        color: #4b5f80;
	        display: block;
	        font-size: 12px;
	        font-weight: 850;
	        text-transform: uppercase;
	    }

	    .fh-developer-select {
	        min-width: 260px;
	    }

	    .fh-developer-stats {
	        display: grid;
	        gap: 10px;
	        grid-template-columns: repeat(5, minmax(120px, 1fr));
	    }

	    .fh-dev-stat {
	        border: 1px solid #d8e2f0;
	        border-radius: 8px;
	        color: inherit;
	        display: block;
	        min-height: 76px;
	        padding: 11px 12px;
	        text-decoration: none;
	    }

	    .fh-dev-stat:hover {
	        background: #eef5ff;
	        color: inherit;
	        text-decoration: none;
	    }

	    .fh-dev-stat.is-active {
	        border-color: #2f6ee8;
	        box-shadow: inset 0 3px 0 #2f6ee8;
	    }

	    .fh-dev-stat span {
	        color: #4b5f80;
	        display: block;
	        font-size: 11px;
	        font-weight: 850;
	        text-transform: uppercase;
	    }

	    .fh-dev-stat strong {
	        color: #06122f;
	        display: block;
	        font-size: 26px;
	        font-weight: 900;
	        line-height: 1;
	        margin-top: 10px;
	    }

    .fh-grid-title {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .fh-grid-title h5 {
        color: #06122f;
        font-size: 18px;
        font-weight: 900;
        margin: 0;
    }

    .fh-download-row {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 10px;
    }

    .fh-table {
        table-layout: fixed;
        width: 100% !important;
    }

    .fh-table th,
    .fh-table td {
        line-height: 1.35;
        overflow-wrap: anywhere;
        vertical-align: middle;
        white-space: normal;
        word-break: normal;
    }

    .fh-table thead th {
        background: #6b6b6b !important;
        border-color: #d8e2f0 !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 850;
        line-height: 1.25;
    }

    .fh-table strong {
        display: block;
        max-width: 100%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(1),
    #freshHelpdeskDashboardTicketsTable td:nth-child(1) {
        width: 11%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(2),
    #freshHelpdeskDashboardTicketsTable td:nth-child(2) {
        width: 21%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(3),
    #freshHelpdeskDashboardTicketsTable td:nth-child(3) {
        width: 12%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(4),
    #freshHelpdeskDashboardTicketsTable td:nth-child(4) {
        width: 8%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(5),
    #freshHelpdeskDashboardTicketsTable td:nth-child(5) {
        width: 12%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(6),
    #freshHelpdeskDashboardTicketsTable td:nth-child(6) {
        width: 12%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(7),
    #freshHelpdeskDashboardTicketsTable td:nth-child(7) {
        width: 8%;
    }

    #freshHelpdeskDashboardTicketsTable th:nth-child(8),
    #freshHelpdeskDashboardTicketsTable td:nth-child(8),
    #freshHelpdeskDashboardTicketsTable th:nth-child(9),
    #freshHelpdeskDashboardTicketsTable td:nth-child(9) {
        width: 8%;
    }

    .fh-ticket-no {
        color: #164c96;
        font-weight: 850;
        white-space: nowrap;
    }

	    .fh-reopened-badge {
	        align-items: center;
	        background: #fff3cd;
	        border-radius: 999px;
        color: #925700;
        display: inline-flex;
        font-size: 10px;
        font-weight: 850;
        line-height: 1;
        margin-left: 6px;
        padding: 4px 7px;
        vertical-align: middle;
	        white-space: nowrap;
	    }

	    .fh-important-badge {
	        align-items: center;
	        background: #fff0d6;
	        border: 1px solid #ffd38a;
	        border-radius: 999px;
	        color: #b45309;
	        display: inline-flex;
	        font-size: 12px;
	        height: 22px;
	        justify-content: center;
	        line-height: 1;
	        width: 22px;
	        vertical-align: middle;
	        white-space: nowrap;
	    }

	    .fh-table td .fh-ticket-no {
	        align-items: flex-start;
	        display: inline-flex;
	        flex-direction: column;
        gap: 4px;
        max-width: 100%;
        white-space: normal;
	        word-break: break-word;
	    }

	    .fh-ticket-number-line {
	        align-items: center;
	        display: inline-flex;
	        gap: 6px;
	        max-width: 100%;
	    }

    .fh-table td .fh-ticket-no .fh-reopened-badge {
        margin-left: 0;
    }

    .fh-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .fh-badge {
        align-items: center;
        border-radius: 999px;
        display: inline-flex;
        font-size: 11px;
        font-weight: 850;
        line-height: 1;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .fh-status {
        background: #eaf2ff;
        color: #2454a6;
    }

    .fh-priority-low {
        background: #eef8f1;
        color: #23784a;
    }

    .fh-priority-medium {
        background: #fff7df;
        color: #8a5b05;
    }

    .fh-priority-high,
    .fh-priority-critical,
    .fh-priority-urgent {
        background: #fff1f2;
        color: #b4233a;
    }

    .fh-dt-toolbar,
    .fh-dt-footer {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
    }

    .fh-dt-toolbar {
        margin-bottom: 10px;
        width: 100%;
    }

    .fh-dt-left {
        margin-right: auto;
    }

    .fh-dt-right {
        margin-left: auto;
    }

	    .fh-dt-footer {
	        padding-top: 12px;
	    }

	    .fh-dashboard-page .dataTables_info,
	    .fh-dashboard-page .dataTables_paginate,
	    .fh-dashboard-page .dataTables_filter {
	        display: none !important;
	    }

	    .fh-laravel-pagination {
	        align-items: center;
	        display: flex;
	        flex-wrap: wrap;
	        gap: 8px;
	        justify-content: flex-end;
	        padding-top: 12px;
	    }

	    .fh-page-link,
	    .fh-page-current {
	        align-items: center;
	        border: 1px solid #d8e2f0;
	        border-radius: 7px;
	        display: inline-flex;
	        font-size: 13px;
	        font-weight: 800;
	        justify-content: center;
	        min-height: 34px;
	        min-width: 34px;
	        padding: 0 10px;
	        text-decoration: none;
	    }

	    .fh-page-link {
	        background: #fff;
	        color: #2454a6;
	    }

	    .fh-page-link:hover {
	        background: #eef5ff;
	        color: #174c78;
	        text-decoration: none;
	    }

	    .fh-page-link.is-disabled {
	        color: #94a3b8;
	        pointer-events: none;
	    }

	    .fh-page-current {
	        background: #5b7df0;
	        border-color: #5b7df0;
	        color: #fff;
	    }

	    .fh-page-ellipsis {
	        align-items: center;
	        color: #94a3b8;
	        display: inline-flex;
	        font-size: 13px;
	        font-weight: 800;
	        justify-content: center;
	        min-height: 34px;
	        min-width: 20px;
	        padding: 0 2px;
	    }

    .dataTables_filter label {
        align-items: center;
        color: #06122f;
        /* display: flex; */
        font-size: 13px;
        font-weight: 800;
        gap: 8px;
        margin: 0;
    }

    .fh-dt-toolbar .dataTables_filter {
        margin-left: auto;
        text-align: right;
    }

    .dataTables_filter input,
    .dataTables_length select {
        border: 1px solid #9ebcf2;
        border-radius: 6px;
        min-height: 34px;
        padding: 5px 8px;
    }

    .fh-dt-toolbar .dataTables_filter input {
        width: 220px;
    }

    .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-right: auto;
    }

    .dt-buttons .dt-button,
    .dt-buttons .btn {
        background: #2f6ee8 !important;
        border: 1px solid #2f6ee8 !important;
        border-radius: 7px !important;
        color: #fff !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        padding: 7px 12px !important;
    }

    .fh-dashboard-page .dt-buttons {
        display: none !important;
    }

    @media (max-width: 767.98px) {
        .fh-dashboard-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .fh-card-grid {
            grid-template-columns: 1fr;
        }

        .dataTables_filter,
        .dataTables_filter label,
	        .dataTables_filter input {
	            width: 100%;
	        }

	        .fh-developer-head {
	            align-items: flex-start;
	            flex-direction: column;
	        }

	        .fh-developer-head form,
	        .fh-developer-select {
	            width: 100%;
	        }

	        .fh-developer-stats {
	            grid-template-columns: 1fr;
	        }
	    }

	    @media (min-width: 768px) and (max-width: 1199.98px) {
	        .fh-card-grid {
	            grid-template-columns: repeat(2, minmax(0, 1fr));
	        }

	        .fh-developer-stats {
	            grid-template-columns: repeat(2, minmax(0, 1fr));
	        }
	    }
</style>

<div class="fh-dashboard-page">
	    <div class="fh-dashboard-head">
        <div>
            <span>{{ $roleLabel }}</span>
            <h4>Helpdesk Dashboard</h4>
        </div>
	        <div class="d-flex flex-wrap align-items-center gap-2">
	            @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
	                <div class="dropdown">
	                    <button class="fh-bell-btn" type="button" id="freshHelpdeskBellBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Important tickets">
	                        <i class="ti ti-bell"></i>
	                        @if ($importantTicketsCount > 0)
	                            <span class="fh-bell-badge" id="freshHelpdeskBellBadge">{{ $importantTicketsCount > 99 ? '99+' : $importantTicketsCount }}</span>
	                        @endif
	                    </button>
	                    <div class="dropdown-menu dropdown-menu-end fh-bell-menu p-0">
	                        <div class="fh-bell-menu-header">Important Tickets{{ $importantTicketsCount > 0 ? ' ('.$importantTicketsCount.')' : '' }}</div>
	                        @forelse ($importantTickets as $importantTicket)
	                            <a href="{{ route('fresh-helpdesk.ticket-details', ['ticket' => \App\Models\FreshHelpdesk::ticketUrlToken($importantTicket->id)]) }}" class="fh-bell-item">
	                                <span class="fh-bell-item-title">{{ $importantTicket->subject ?: '-' }}</span>
	                                <span class="fh-bell-item-meta">{{ $importantTicket->ticket_number ?: '#'.$importantTicket->id }} &middot; {{ $importantTicket->user_name ?: '-' }}</span>
	                            </a>
	                        @empty
	                            <div class="fh-bell-empty">No important tickets right now.</div>
	                        @endforelse
	                    </div>
	                </div>
	            @endif
	            <nav class="fh-switch" aria-label="Helpdesk switch">
			                <a href="{{ route('fresh-helpdesk.dashboard', array_filter(['pane' => 'tickets', 'developer_id' => $selectedDashboardDeveloperId ?: null])) }}"
			                    data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
				                    class="{{ $activePane === 'tickets' ? 'is-active' : '' }}" data-fh-switch="tickets">
		                    <i class="ti ti-ticket"></i> Tickets
		                </a>
		                @if ($canViewTaskDashboard)
				                    <a href="{{ route('fresh-helpdesk.dashboard', array_filter(['pane' => 'tasks', 'developer_id' => $selectedDashboardDeveloperId ?: null])) }}"
				                        data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
			                        class="{{ $activePane === 'tasks' ? 'is-active' : '' }}"
		                        data-fh-switch="tasks">
	                        <i class="ti ti-clipboard-list"></i> Tasks
	                    </a>
	                @endif
	            </nav>
            @if (in_array($role, [\App\Models\FreshHelpdesk::ROLE_USER, \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN, \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN], true))
                <a href="{{ route('fresh-helpdesk.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Ticket
                </a>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

	    <section class="fh-pane" data-fh-pane="tickets" @if ($activePane !== 'tickets') hidden @endif>
	        <div class="fh-card-grid {{ $role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN ? 'is-nic-ticket-grid' : '' }}">
	            @foreach ($ticketCards as $card)
					                <a href="{{ $dashboardUrl }}"
			                    data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_card' => $card['key'], 'ticket_page' => 1, 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
		                    data-fh-pane-card="tickets" data-fh-card="{{ $card['key'] }}"
		                    data-fh-card-count="{{ (int) ($ticketDashboardStats[$card['key']] ?? 0) }}"
		                    class="fh-stat-card fh-tone-{{ $card['tone'] }} {{ $card['key'] === $activeTicketCard ? 'is-active' : '' }}">
	                    <span class="fh-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
	                    <label>{{ $card['label'] }}</label>
		                    <strong>{{ number_format((int) ($ticketDashboardStats[$card['key']] ?? 0)) }}</strong>
	                    <small>{{ $card['hint'] }}</small>
	                    <span class="fh-active-pill">ACTIVE</span>
	                </a>
		            @endforeach
		        </div>

	        <form class="fh-filter-card" method="POST" action="{{ route('fresh-helpdesk.dashboard.state') }}">
	            @csrf
	            <input type="hidden" name="pane" value="tickets">
	            <input type="hidden" name="ticket_card" value="{{ $activeTicketCard }}">
	            <input type="hidden" name="dev_ticket_card" value="{{ $activeDeveloperTicketCard }}">
	            <input type="hidden" name="developer_id" value="{{ $selectedDashboardDeveloperId }}">
	            <input type="hidden" name="ticket_page" value="1">
	            <div class="row g-2 align-items-center">
		                <div class="col-lg-5 col-md-12">
	                    <input type="text" name="search" class="form-control" value="{{ $dashboardFilters['search'] ?? '' }}" placeholder="Search ticket, subject, module, user">
	                </div>
	                <div class="col-lg-2 col-md-4">
	                    <select name="priority" class="form-select">
	                        <option value="">All priorities</option>
	                        @foreach ($priorityOptions as $priority)
	                            <option value="{{ $priority }}" @selected(strtolower((string) ($dashboardFilters['priority'] ?? '')) === strtolower((string) $priority))>{{ $priority }}</option>
	                        @endforeach
	                    </select>
	                </div>
	                <div class="col-lg-2 col-md-4">
	                    <select name="status" class="form-select">
	                        <option value="">All statuses</option>
	                        @foreach ($dashboardStatusOptions as $status => $label)
	                            <option value="{{ $status }}" @selected(($dashboardFilters['status'] ?? '') === $status)>{{ $label }}</option>
	                        @endforeach
	                    </select>
	                </div>
		                <div class="col-lg-1 col-md-4">
		                    <select name="ticket_scope" class="form-select" title="Assigned / Forwarded">
		                        <option value="">All</option>
		                        <option value="on_me" @selected(($dashboardFilters['ticket_scope'] ?? '') === 'on_me')>Assigned / Forwarded</option>
		                        @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
		                            <option value="developer_side" @selected(($dashboardFilters['ticket_scope'] ?? '') === 'developer_side')>Developer Side</option>
		                        @endif
		                        @if (in_array($role, [\App\Models\FreshHelpdesk::ROLE_NIC_ADMIN, \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN], true))
		                            <option value="important" @selected(($dashboardFilters['ticket_scope'] ?? '') === 'important')>Important</option>
		                        @endif
	                    </select>
	                </div>
	                <div class="col-lg-1 col-md-2">
		                    <button type="submit" class="btn btn-primary w-100 px-2">
		                        Filter <span class="fh-filter-count" data-fh-filter-count="tickets">{{ number_format($dashboardTicketResultCount) }}</span>
		                    </button>
	                </div>
	                <div class="col-lg-1 col-md-2">
		                    <a class="btn btn-light w-100" href="{{ $dashboardUrl }}" data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_card' => $activeTicketCard, 'dev_ticket_card' => $activeDeveloperTicketCard, 'developer_id' => $selectedDashboardDeveloperId ?: null, 'clear_filters' => 1, 'ticket_page' => 1]) !!}">Clear</a>
	                </div>
	            </div>
	        </form>

	        <div class="fh-grid-panel" data-fh-grid="tickets">
	            <div class="fh-grid-title">
		                <div>
		                    <span>{{ $roleLabel }}</span>
		                    <h5>{{ $ticketGridTitle }}</h5>
		                </div>
	            </div>
                <div class="fh-download-row">
                    <a href="{{ route('fresh-helpdesk.dashboard', ['download' => 'tickets']) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-download me-1"></i> Download
                    </a>
                </div>
	            <div class="table-responsive">
                <table id="freshHelpdeskDashboardTicketsTable" class="table table-striped table-bordered align-middle fh-table">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th class="text-wrap">Subject / Created By</th>
                            <th class="text-wrap">Module</th>
                            <th class="text-wrap">Priority</th>
                            <th class="text-wrap">Current Status</th>
                            <th class="text-wrap">Currently With</th>
                            <th class="text-wrap">Ageing Days</th>
                            <th class="text-wrap">Created On</th>
                            <th class="text-wrap">Last Updated On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            @php
                                $isInProgress = \App\Models\FreshHelpdesk::isDashboardTicketInProgress($ticket);
                                $isUrgent = \App\Models\FreshHelpdesk::isDashboardTicketUrgent($ticket);
                                $isReturned = \App\Models\FreshHelpdesk::isDashboardTicketReturned($ticket);
                                $isClosed = \App\Models\FreshHelpdesk::isDashboardTicketResolvedClosed($ticket);
                            @endphp
                            <tr data-fh-row
                                data-fh-total="Y"
                                data-fh-in-progress="{{ $isInProgress ? 'Y' : 'N' }}"
                                data-fh-urgent="{{ $isUrgent ? 'Y' : 'N' }}"
                                data-fh-returned="{{ $isReturned ? 'Y' : 'N' }}"
                                data-fh-resolved-closed="{{ $isClosed ? 'Y' : 'N' }}">
	                                <td>
		                                    <a class="fh-ticket-no" href="{{ route('fresh-helpdesk.ticket-details', ['ticket' => \App\Models\FreshHelpdesk::ticketUrlToken($ticket->id)]) }}">
		                                        <span class="fh-ticket-number-line">
		                                            {{ $ticket->ticket_number ?: '#'.$ticket->id }}
		                                            @if (strtoupper((string) ($ticket->importflag ?? '')) === 'Y')
		                                                <span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>
		                                            @endif
		                                        </span>
		                                        @if (!empty($ticket->is_reopened))
		                                            <span class="fh-reopened-badge">Reopened</span>
		                                        @endif
	                                    </a>
	                                </td>
                                <td class="text-wrap">
                                    <strong>{{ $ticket->subject ?: '-' }}</strong>
                                    <span class="fh-muted">{{ $ticket->user_name ?: '-' }}</span>
                                </td>
                                <td class="text-wrap">{{ $ticket->category ?: ($ticket->request_type ? \Illuminate\Support\Str::headline((string) $ticket->request_type) : '-') }}</td>
                                <td><span class="fh-badge fh-priority-{{ \Illuminate\Support\Str::slug((string) ($ticket->priority ?: 'medium')) }}">{{ $ticket->priority ?: '-' }}</span></td>
                                <td><span class="fh-badge fh-status">{{ $ticketStatus($ticket->status) }}</span></td>
                                <td class="text-wrap">
                                    {{ \App\Models\FreshHelpdesk::dashboardCurrentWith($ticket, $role) }}
                                    @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN && !empty($ticket->latest_assignment_status) && empty($ticket->is_reopened))
                                        <span class="fh-muted">{{ \Illuminate\Support\Str::headline((string) $ticket->latest_assignment_status) }}</span>
                                    @endif
                                </td>
			                                <td class="text-wrap">{{ $ageingDays($ticket->created_at) }}</td>
	                                <td class="text-wrap" data-order="{{ $dateOrderValue($ticket->created_at) }}">{{ $fmt($ticket->created_at) }}</td>
                                <td class="text-wrap">{{ $fmt($ticket->updated_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
	                </table>
	            </div>
			            <div data-fh-pane-pagination="tickets">
			            @if ($tickets->hasPages())
			                @php $fhTicketPageItems = $windowedPageNumbers($tickets); @endphp
			                <div class="fh-laravel-pagination">
				                    <a class="fh-page-link {{ $tickets->onFirstPage() ? 'is-disabled' : '' }}"
				                        href="{{ $dashboardUrl }}"
				                        data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_page' => max(1, $tickets->currentPage() - 1)]) !!}">Previous</a>
			                    @foreach ($fhTicketPageItems as $page)
			                        @if ($page === '...')
			                            <span class="fh-page-ellipsis">&hellip;</span>
			                        @elseif ($page === $tickets->currentPage())
			                            <span class="fh-page-current">{{ $page }}</span>
			                        @else
				                            <a class="fh-page-link" href="{{ $dashboardUrl }}" data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_page' => $page]) !!}">{{ $page }}</a>
			                        @endif
			                    @endforeach
				                    <a class="fh-page-link {{ $tickets->hasMorePages() ? '' : 'is-disabled' }}"
				                        href="{{ $dashboardUrl }}"
				                        data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_page' => min($tickets->lastPage(), $tickets->currentPage() + 1)]) !!}">Next</a>
			                </div>
			            @endif
			            </div>
		        </div>

		        @if ($canViewDeveloperSummary)
			            <div class="fh-developer-panel" data-fh-developer-panel="tickets" data-fh-developer-url="{{ route('fresh-helpdesk.dashboard.developer-summary') }}" data-active-card="{{ $activeDeveloperTicketCard }}">
			                <div class="fh-developer-head">
			                    <div>
			                        <span>Developer Wise</span>
			                        <h5 data-fh-developer-heading>Tickets - {{ $selectedDashboardDeveloper->devename ?? 'Select Developer' }}</h5>
			                    </div>
			                    @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
				                        <form method="POST" action="{{ route('fresh-helpdesk.dashboard.state') }}" data-fh-developer-form>
				                            @csrf
			                            <input type="hidden" name="pane" value="tickets">
			                            <input type="hidden" name="ticket_card" value="{{ $activeTicketCard }}">
			                            <input type="hidden" name="dev_ticket_card" value="{{ $activeDeveloperTicketCard }}">
			                            <select name="developer_id" class="form-select fh-developer-select" data-fh-developer-select>
			                                @foreach ($developerOptions as $developer)
			                                    <option value="{{ $developer->devuserid }}" @selected((string) $selectedDashboardDeveloperId === (string) $developer->devuserid)>
		                                        {{ $developer->devename }}{{ $developer->email ? ' - '.$developer->email : '' }}
		                                    </option>
		                                @endforeach
		                            </select>
		                        </form>
		                    @endif
		                </div>
			                <div class="fh-developer-stats">
			                    @foreach ($ticketCards as $card)
				                        <a class="fh-dev-stat {{ $card['key'] === $activeDeveloperTicketCard ? 'is-active' : '' }}"
					                            href="{{ $dashboardUrl }}"
					                            data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tickets', 'ticket_card' => $activeTicketCard, 'dev_ticket_card' => $card['key'], 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
					                            data-fh-dev-card="{{ $card['key'] }}">
			                            <span>{{ $card['label'] }}</span>
			                            <strong>{{ $developerTicketDashboardStats[$card['key']] ?? 0 }}</strong>
			                        </a>
		                    @endforeach
		                </div>
		                <div class="mt-3">
				                    <div class="fh-grid-title">
				                        <div>
				                            <span data-fh-developer-name>{{ $selectedDashboardDeveloper->devename ?? 'Developer' }}</span>
				                            <h5 data-fh-developer-detail-title>{{ collect($ticketCards)->firstWhere('key', $activeDeveloperTicketCard)['label'] ?? 'Total Tickets' }} Ticket Details</h5>
				                        </div>
				                    </div>
                                <div class="fh-download-row">
                                    <a href="{{ route('fresh-helpdesk.dashboard', ['download' => 'developer_tickets']) }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-download me-1"></i> Download
                                    </a>
                                </div>
			                    <div class="table-responsive">
		                        <table id="freshHelpdeskDashboardDeveloperTicketsTable" class="table table-striped table-bordered align-middle fh-table">
		                            <thead>
		                                <tr>
		                                    <th>Ticket Number</th>
		                                    <th class="text-wrap">Subject / Created By</th>
		                                    <th class="text-wrap">Module</th>
		                                    <th class="text-wrap">Priority</th>
		                                    <th class="text-wrap">Current Status</th>
		                                    <th class="text-wrap">Created On</th>
		                                    <th class="text-wrap">Last Updated On</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                @foreach ($developerTickets as $ticket)
		                                    <tr>
		                                        <td>
			                                            <a class="fh-ticket-no" href="{{ route('fresh-helpdesk.ticket-details', ['ticket' => \App\Models\FreshHelpdesk::ticketUrlToken($ticket->id)]) }}">
			                                                <span class="fh-ticket-number-line">
			                                                    {{ $ticket->ticket_number ?: '#'.$ticket->id }}
			                                                    @if (strtoupper((string) ($ticket->importflag ?? '')) === 'Y')
			                                                        <span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>
			                                                    @endif
			                                                </span>
			                                                @if (!empty($ticket->is_reopened))
			                                                    <span class="fh-reopened-badge">Reopened</span>
			                                                @endif
		                                            </a>
		                                        </td>
		                                        <td class="text-wrap">
		                                            <strong>{{ $ticket->subject ?: '-' }}</strong>
		                                            <span class="fh-muted">{{ $ticket->user_name ?: '-' }}</span>
		                                        </td>
		                                        <td class="text-wrap">{{ $ticket->category ?: ($ticket->request_type ? \Illuminate\Support\Str::headline((string) $ticket->request_type) : '-') }}</td>
		                                        <td><span class="fh-badge fh-priority-{{ \Illuminate\Support\Str::slug((string) ($ticket->priority ?: 'medium')) }}">{{ $ticket->priority ?: '-' }}</span></td>
		                                        <td><span class="fh-badge fh-status">{{ $ticketStatus($ticket->status) }}</span></td>
			                                        <td class="text-wrap" data-order="{{ $dateOrderValue($ticket->created_at) }}">{{ $fmt($ticket->created_at) }}</td>
		                                        <td class="text-wrap">{{ $fmt($ticket->updated_at) }}</td>
		                                    </tr>
		                                @endforeach
		                            </tbody>
		                        </table>
		                    </div>
			                    <div data-fh-dev-pagination>
			                        @if ($developerTickets->hasPages())
			                            <div class="fh-laravel-pagination">
			                                <a class="fh-page-link {{ $developerTickets->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $developerTickets->previousPageUrl() ?: '#' }}">Previous</a>
			                                @foreach ($windowedPageNumbers($developerTickets) as $page)
			                                    @if ($page === '...')
			                                        <span class="fh-page-ellipsis">&hellip;</span>
			                                    @elseif ($page === $developerTickets->currentPage())
			                                        <span class="fh-page-current">{{ $page }}</span>
			                                    @else
			                                        <a class="fh-page-link" href="{{ $developerTickets->url($page) }}">{{ $page }}</a>
			                                    @endif
			                                @endforeach
			                                <a class="fh-page-link {{ $developerTickets->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $developerTickets->nextPageUrl() ?: '#' }}">Next</a>
			                            </div>
			                        @endif
			                    </div>
		                </div>
		            </div>
		        @endif
		    </section>

    @if ($canViewTaskDashboard)
        <section class="fh-pane" data-fh-pane="tasks" @if ($activePane !== 'tasks') hidden @endif>
	            <div class="fh-card-grid">
	                @foreach ($taskCards as $card)
				                    <a href="{{ $dashboardUrl }}"
			                    data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'task_card' => $card['key'], 'task_page' => 1, 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
		                    data-fh-pane-card="tasks" data-fh-card="{{ $card['key'] }}"
		                    data-fh-card-count="{{ (int) ($taskDashboardStats[$card['key']] ?? 0) }}"
		                    class="fh-stat-card fh-tone-{{ $card['tone'] }} {{ $card['key'] === $activeTaskCard ? 'is-active' : '' }}">
	                        <span class="fh-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
	                        <label>{{ $card['label'] }}</label>
		                        <strong>{{ number_format((int) ($taskDashboardStats[$card['key']] ?? 0)) }}</strong>
	                        <small>{{ $card['hint'] }}</small>
	                        <span class="fh-active-pill">ACTIVE</span>
	                    </a>
	                @endforeach
	            </div>

            <div class="fh-grid-panel" data-fh-grid="tasks">
	                <div class="fh-grid-title">
		                    <div>
		                        <span>{{ $roleLabel }}</span>
		                        <h5>{{ $taskGridTitle }}</h5>
		                    </div>
		                    <div class="d-flex flex-wrap gap-2">
		                        <a href="{{ route('fresh-helpdesk.task-details') }}" class="btn btn-light btn-sm">Task Details</a>
		                        @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
	                            <a href="{{ route('fresh-helpdesk.assign-task') }}" class="btn btn-primary btn-sm">Assign Task</a>
	                        @endif
	                    </div>
	                </div>
                    <div class="fh-download-row">
                        <a href="{{ route('fresh-helpdesk.dashboard', ['download' => 'tasks']) }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-download me-1"></i> Download
                        </a>
                    </div>
	                <div class="table-responsive">
                    <table id="freshHelpdeskDashboardTasksTable" class="table table-striped table-bordered align-middle fh-table">
                        <thead>
                            <tr>
                                <th class="text-wrap">Task</th>
                                <th class="text-wrap">Developer</th>
                                <th class="text-wrap">Task Type</th>
                                <th class="text-wrap">Status</th>
                                <th class="text-wrap">Assigned On</th>
                                <th class="text-wrap">Expected On</th>
                                <th class="text-wrap">Completed On</th>
                                <th class="text-wrap">Last Updated On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                @php
                                    $isInProgress = \App\Models\FreshHelpdesk::isDashboardTaskInProgress($task);
                                    $isUrgent = \App\Models\FreshHelpdesk::isDashboardTaskUrgent($task);
                                    $isReturned = \App\Models\FreshHelpdesk::isDashboardTaskReturned($task);
                                    $isClosed = \App\Models\FreshHelpdesk::isDashboardTaskResolvedClosed($task);
                                @endphp
                                <tr data-fh-row
                                    data-fh-total="Y"
                                    data-fh-in-progress="{{ $isInProgress ? 'Y' : 'N' }}"
                                    data-fh-urgent="{{ $isUrgent ? 'Y' : 'N' }}"
	                                    data-fh-returned="{{ $isReturned ? 'Y' : 'N' }}"
	                                    data-fh-resolved-closed="{{ $isClosed ? 'Y' : 'N' }}">
	                                    <td class="text-wrap">
	                                        <strong>
	                                                <a href="{{ route('fresh-helpdesk.task-details', ['task' => \App\Models\FreshHelpdesk::taskUrlToken($task->id)]) }}" class="fh-task-link">
                                                    {{ $task->process_assigned ?: '#'.$task->id }}
                                                </a>
                                            </strong>
	                                        <span class="fh-muted">Assigned by {{ $task->assigned_by_name ?: '-' }}</span>
	                                    </td>
                                    <td class="text-wrap">{{ \App\Models\FreshHelpdesk::taskCurrentlyWith($task) }}</td>
                                    <td class="text-wrap">{{ ucfirst((string) $task->task_type) }}</td>
                                    <td class="text-wrap"><span class="fh-badge fh-status">{{ $taskStatus($task->task_status_by_tester) }}</span></td>
	                                    <td class="text-wrap" data-order="{{ $dateOrderValue($task->assigned_on ?: $task->created_at) }}">{{ $fmt($task->assigned_on) }}</td>
                                    <td class="text-wrap">{{ $fmt($task->expected_date_to_complete) }}</td>
                                    <td class="text-wrap">{{ $fmt($task->completed_on) }}</td>
                                    <td class="text-wrap">{{ $fmt($task->updated_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
	                    </table>
	                </div>
				                <div data-fh-pane-pagination="tasks">
				                @if ($tasks->hasPages())
				                    <div class="fh-laravel-pagination">
					                        <a class="fh-page-link {{ $tasks->onFirstPage() ? 'is-disabled' : '' }}"
					                            href="{{ $dashboardUrl }}"
					                            data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'task_page' => max(1, $tasks->currentPage() - 1)]) !!}">Previous</a>
			                        @foreach ($windowedPageNumbers($tasks) as $page)
			                            @if ($page === '...')
			                                <span class="fh-page-ellipsis">&hellip;</span>
			                            @elseif ($page === $tasks->currentPage())
			                                <span class="fh-page-current">{{ $page }}</span>
			                            @else
				                                <a class="fh-page-link" href="{{ $dashboardUrl }}" data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'task_page' => $page]) !!}">{{ $page }}</a>
			                            @endif
			                        @endforeach
				                        <a class="fh-page-link {{ $tasks->hasMorePages() ? '' : 'is-disabled' }}"
				                            href="{{ $dashboardUrl }}"
				                            data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'task_page' => min($tasks->lastPage(), $tasks->currentPage() + 1)]) !!}">Next</a>
				                    </div>
				                @endif
				                </div>
		            </div>

		            @if ($canViewDeveloperSummary)
			                <div class="fh-developer-panel" data-fh-developer-panel="tasks" data-fh-developer-url="{{ route('fresh-helpdesk.dashboard.developer-summary') }}" data-active-card="{{ $activeDeveloperTaskCard }}">
			                    <div class="fh-developer-head">
			                        <div>
			                            <span>Developer Wise</span>
			                            <h5 data-fh-developer-heading>Tasks - {{ $selectedDashboardDeveloper->devename ?? 'Select Developer' }}</h5>
			                        </div>
			                        @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
			                            <form method="POST" action="{{ route('fresh-helpdesk.dashboard.state') }}" data-fh-developer-form>
			                                @csrf
			                                <input type="hidden" name="pane" value="tasks">
			                                <input type="hidden" name="task_card" value="{{ $activeTaskCard }}">
			                                <input type="hidden" name="dev_task_card" value="{{ $activeDeveloperTaskCard }}">
			                                <select name="developer_id" class="form-select fh-developer-select" data-fh-developer-select>
			                                    @foreach ($developerOptions as $developer)
			                                        <option value="{{ $developer->devuserid }}" @selected((string) $selectedDashboardDeveloperId === (string) $developer->devuserid)>
		                                            {{ $developer->devename }}{{ $developer->email ? ' - '.$developer->email : '' }}
		                                        </option>
		                                    @endforeach
		                                </select>
		                            </form>
		                        @endif
		                    </div>
			                    <div class="fh-developer-stats">
			                        @foreach ($taskCards as $card)
				                            <a class="fh-dev-stat {{ $card['key'] === $activeDeveloperTaskCard ? 'is-active' : '' }}"
					                                href="{{ $dashboardUrl }}"
					                                data-fh-dashboard-state="{!! $dashboardStateJson(['pane' => 'tasks', 'task_card' => $activeTaskCard, 'dev_task_card' => $card['key'], 'developer_id' => $selectedDashboardDeveloperId ?: null]) !!}"
					                                data-fh-dev-card="{{ $card['key'] }}">
			                                <span>{{ $card['label'] }}</span>
			                                <strong>{{ $developerTaskDashboardStats[$card['key']] ?? 0 }}</strong>
			                            </a>
		                        @endforeach
		                    </div>
		                    <div class="mt-3">
					                        <div class="fh-grid-title">
					                            <div>
					                                <span data-fh-developer-name>{{ $selectedDashboardDeveloper->devename ?? 'Developer' }}</span>
					                                <h5 data-fh-developer-detail-title>{{ collect($taskCards)->firstWhere('key', $activeDeveloperTaskCard)['label'] ?? 'Total Tasks' }} Task Details</h5>
					                            </div>
					                        </div>
                                    <div class="fh-download-row">
                                        <a href="{{ route('fresh-helpdesk.dashboard', ['download' => 'developer_tasks']) }}" class="btn btn-primary btn-sm">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                    </div>
			                        <div class="table-responsive">
		                            <table id="freshHelpdeskDashboardDeveloperTasksTable" class="table table-striped table-bordered align-middle fh-table">
		                                <thead>
		                                    <tr>
		                                        <th class="text-wrap">Task</th>
		                                        <th class="text-wrap">Type</th>
		                                        <th class="text-wrap">Status</th>
		                                        <th class="text-wrap">Assigned On</th>
		                                        <th class="text-wrap">Expected On</th>
		                                        <th class="text-wrap">Completed On</th>
		                                        <th class="text-wrap">Last Updated On</th>
		                                    </tr>
		                                </thead>
		                                <tbody>
		                                    @foreach ($developerTasks as $task)
		                                        <tr>
		                                            <td class="text-wrap">
		                                                <strong>
		                                                    <a href="{{ route('fresh-helpdesk.task-details', ['task' => \App\Models\FreshHelpdesk::taskUrlToken($task->id)]) }}" class="fh-task-link">
		                                                        {{ $task->process_assigned ?: '#'.$task->id }}
		                                                    </a>
		                                                </strong>
		                                                <span class="fh-muted">Assigned by {{ $task->assigned_by_name ?: '-' }}</span>
		                                            </td>
		                                            <td class="text-wrap">{{ ucfirst((string) $task->task_type) }}</td>
		                                            <td class="text-wrap"><span class="fh-badge fh-status">{{ $taskStatus($task->task_status_by_tester) }}</span></td>
			                                            <td class="text-wrap" data-order="{{ $dateOrderValue($task->assigned_on ?: $task->created_at) }}">{{ $fmt($task->assigned_on) }}</td>
		                                            <td class="text-wrap">{{ $fmt($task->expected_date_to_complete) }}</td>
		                                            <td class="text-wrap">{{ $fmt($task->completed_on) }}</td>
		                                            <td class="text-wrap">{{ $fmt($task->updated_at) }}</td>
		                                        </tr>
		                                    @endforeach
		                                </tbody>
		                            </table>
		                        </div>
			                        <div data-fh-dev-pagination>
			                            @if ($developerTasks->hasPages())
			                                <div class="fh-laravel-pagination">
			                                    <a class="fh-page-link {{ $developerTasks->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $developerTasks->previousPageUrl() ?: '#' }}">Previous</a>
			                                    @foreach ($windowedPageNumbers($developerTasks) as $page)
			                                        @if ($page === '...')
			                                            <span class="fh-page-ellipsis">&hellip;</span>
			                                        @elseif ($page === $developerTasks->currentPage())
			                                            <span class="fh-page-current">{{ $page }}</span>
			                                        @else
			                                            <a class="fh-page-link" href="{{ $developerTasks->url($page) }}">{{ $page }}</a>
			                                        @endif
			                                    @endforeach
			                                    <a class="fh-page-link {{ $developerTasks->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $developerTasks->nextPageUrl() ?: '#' }}">Next</a>
			                                </div>
			                            @endif
			                        </div>
		                    </div>
		                </div>
		            @endif
		        </section>
    @endif
</div>

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/custom.xl.min.js') }}"></script>
		<script>
			    $(function () {
	        var tableMap = {};
			        var navigationEntry = window.performance && window.performance.getEntriesByType
			            ? window.performance.getEntriesByType('navigation')[0]
			            : null;
			        var isReload = navigationEntry
			            ? navigationEntry.type === 'reload'
			            : window.performance && window.performance.navigation && window.performance.navigation.type === 1;

			        if (isReload && !new URLSearchParams(window.location.search).has('clear_filters')) {
			            var cleanUrl = new URL(window.location.href);
			            cleanUrl.search = '';
			            cleanUrl.searchParams.set('clear_filters', '1');
			            window.location.replace(cleanUrl.toString());
			            return;
			        }

		        var bellBtn = $('#freshHelpdeskBellBtn');
	        if (bellBtn.length) {
	            bellBtn.one('click', function () {
	                $.ajax({
	                    url: '{{ route('fresh-helpdesk.dashboard.important-tickets.verify') }}',
	                    method: 'POST',
	                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	                }).done(function () {
	                    $('#freshHelpdeskBellBadge').remove();
	                });
	            });
	        }

	        function checkAutoForwardStaleTickets() {
	            $.ajax({
	                url: '{{ route('fresh-helpdesk.tickets.auto-forward-check') }}',
	                method: 'POST',
	                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	            });
	        }
		        // Auto-forward is handled by the scheduler; do not move tickets just because the dashboard opened.
		        // setInterval(checkAutoForwardStaleTickets, 30000);

	        function initTable(selector, pane, title, order) {
	            if (!$(selector).length) {
	                return;
	            }

	            if ($.fn.DataTable.isDataTable(selector)) {
	                $(selector).DataTable().clear().destroy();
		                $(selector).closest('.dataTables_wrapper').find('.dataTables_info, .dataTables_paginate, .dataTables_filter').remove();
	            }

	            tableMap[pane] = $(selector).DataTable({
	                processing: true,
	                serverSide: false,
	                lengthChange: false,
	                paging: false,
	                bPaginate: false,
		                info: false,
		                stateSave: false,
		                searching: false,
		                autoWidth: false,
			                dom: 'rt',
				                order: order || (pane === 'tickets' ? [[7, 'desc']] : [[4, 'desc']])
			            });

		            $(selector).closest('.dataTables_wrapper').find('.dataTables_info, .dataTables_paginate, .dataTables_filter').remove();
	        }

	        function showPane(pane) {
            $('[data-fh-pane]').attr('hidden', true);
            $('[data-fh-pane="' + pane + '"]').removeAttr('hidden');
            $('[data-fh-switch]').removeClass('is-active');
            $('[data-fh-switch="' + pane + '"]').addClass('is-active');

		            setTimeout(function () {
		                var selector = mainTableSelector(pane);
		                if ($.fn.DataTable.isDataTable(selector)) {
		                    $(selector).DataTable().columns.adjust().draw(false);
		                }
		            }, 80);
		        }

			        initTable('#freshHelpdeskDashboardTicketsTable', 'tickets', 'Fresh Helpdesk Tickets', [[7, 'desc']]);
			        initTable('#freshHelpdeskDashboardTasksTable', 'tasks', 'Fresh Helpdesk Tasks', [[4, 'desc']]);
				        initTable('#freshHelpdeskDashboardDeveloperTicketsTable', 'developerTickets', 'Developer Ticket Details', [[5, 'desc']]);
				        initTable('#freshHelpdeskDashboardDeveloperTasksTable', 'developerTasks', 'Developer Task Details', [[3, 'desc']]);

			        var paneSummaryUrl = '{{ route("fresh-helpdesk.dashboard.pane-summary") }}';

			        function formatDashboardCount(value) {
			            var number = parseInt(value, 10);

			            if (isNaN(number)) {
			                number = 0;
			            }

			            return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			        }

	        function updateDashboardFilterCount(pane, count) {
	            $('[data-fh-filter-count="' + pane + '"]').text(formatDashboardCount(count));
	        }

	        function dashboardCardCount(card) {
	            var $card = $(card);
	            var count = $card.attr('data-fh-card-count');

	            if (count === undefined || count === null || count === '') {
	                count = $card.find('strong').first().text();
	            }

	            return count || 0;
	        }

	        function syncDashboardFilterCountFromActiveCard(pane) {
	            updateDashboardFilterCount(
	                pane,
	                dashboardCardCount($('[data-fh-pane-card="' + pane + '"].is-active').first())
	            );
	        }

	        function activeDashboardCard(pane) {
	            var activeCard = $('[data-fh-pane-card="' + pane + '"].is-active').first().data('fh-card');

	            return activeCard || (pane === 'tasks' ? 'total' : 'in_progress');
	        }

	        function dashboardFilterForm(pane) {
	            return $('.fh-filter-card').filter(function () {
	                return ($(this).find('[name="pane"]').val() || 'tickets') === pane;
	            }).first();
	        }

	        function syncDashboardFormCard(pane, card) {
	            var form = dashboardFilterForm(pane);
	            var fieldName = pane === 'tasks' ? 'task_card' : 'ticket_card';

	            if (form.length && form.find('[name="' + fieldName + '"]').length) {
	                form.find('[name="' + fieldName + '"]').val(card);
	            }
	        }

	        syncDashboardFilterCountFromActiveCard('tickets');
	        syncDashboardFilterCountFromActiveCard('tasks');

	        function mainTableSelector(pane) {
		            return pane === 'tasks'
		                ? '#freshHelpdeskDashboardTasksTable'
		                : '#freshHelpdeskDashboardTicketsTable';
		        }

			        function refreshMainPane(pane, extraParams) {
			            pane = pane === 'tasks' ? 'tasks' : 'tickets';

		            var params = $.extend({}, extraParams || {}, { pane: pane });
		            var selector = mainTableSelector(pane);
		            var $table = $(selector);
		            var $panel = $table.closest('[data-fh-grid]');

		            $panel.addClass('is-loading');

		            $.getJSON(paneSummaryUrl, params)
		                .done(function (response) {
		                    if ($.fn.DataTable.isDataTable(selector)) {
		                        $(selector).DataTable().clear().destroy();
		                    }

		                    $table.find('tbody').html(response.rows_html || '');
		                    $('[data-fh-pane-pagination="' + pane + '"]').html(response.pagination_html || '');

			                    $('[data-fh-pane-card="' + pane + '"]').each(function () {
			                        var card = $(this).data('fh-card');
			                        var cardCount = (response.stats && response.stats[card] !== undefined) ? response.stats[card] : 0;
			                        $(this).toggleClass('is-active', card === response.active_card);
			                        $(this).attr('data-fh-card-count', cardCount);
			                        $(this).find('strong').text(formatDashboardCount(cardCount));
			                    });
			                    syncDashboardFormCard(pane, response.active_card || activeDashboardCard(pane));

			                    updateDashboardFilterCount(
			                        pane,
			                        response.result_count !== undefined
			                            ? response.result_count
			                            : ($('[data-fh-pane-card="' + pane + '"].is-active').attr('data-fh-card-count') || 0)
			                    );

			                    initTable(
		                        selector,
		                        pane,
		                        pane === 'tasks' ? 'Fresh Helpdesk Tasks' : 'Fresh Helpdesk Tickets',
			                        pane === 'tasks' ? [[4, 'desc']] : [[7, 'desc']]
		                    );
		                })
		                .always(function () {
		                    $panel.removeClass('is-loading');
			                });
			        }

	        function resetVisibleDashboardFilters(pane) {
	            var form = dashboardFilterForm(pane);

			            if (!form.length) {
			                return;
			            }

			            form.find('[name="search"], [name="priority"], [name="status"], [name="developer_userid"], [name="ticket_scope"]').val('');
			        }

		        function developerTableSelector(pane) {
		            return pane === 'tasks'
		                ? '#freshHelpdeskDashboardDeveloperTasksTable'
		                : '#freshHelpdeskDashboardDeveloperTicketsTable';
		        }

		        function developerTableKey(pane) {
		            return pane === 'tasks' ? 'developerTasks' : 'developerTickets';
		        }

		        function refreshDeveloperPanel(panel, extraParams) {
		            var pane = panel.data('fh-developer-panel');
		            var selector = developerTableSelector(pane);
		            var tableKey = developerTableKey(pane);
		            var params = $.extend({}, extraParams || {}, {
		                pane: pane,
		                developer_id: panel.find('[name="developer_id"]').val(),
		                dev_ticket_card: pane === 'tickets' ? panel.attr('data-active-card') : undefined,
		                dev_task_card: pane === 'tasks' ? panel.attr('data-active-card') : undefined
		            });

		            panel.addClass('is-loading');

		            $.getJSON(panel.data('fh-developer-url'), params)
		                .done(function (response) {
		                    if ($.fn.DataTable.isDataTable(selector)) {
		                        $(selector).DataTable().clear().destroy();
		                    }

		                    $(selector).find('tbody').html(response.rows_html || '');
		                    panel.find('[data-fh-developer-heading]').text((pane === 'tasks' ? 'Tasks - ' : 'Tickets - ') + response.developer_name);
		                    panel.find('[data-fh-developer-name]').text(response.developer_name);
		                    panel.find('[data-fh-developer-detail-title]').text(response.title);
		                    panel.find('[data-fh-dev-pagination]').html(response.pagination_html || '');
		                    panel.find('[name="developer_id"]').val(response.developer_id);
		                    panel.attr('data-active-card', response.active_card);
		                    panel.find('[name="dev_ticket_card"], [name="dev_task_card"]').val(response.active_card);

		                    panel.find('[data-fh-dev-card]').each(function () {
		                        var card = $(this).data('fh-dev-card');
		                        $(this).toggleClass('is-active', card === response.active_card);
		                        $(this).find('strong').text((response.stats && response.stats[card]) ? response.stats[card] : 0);
		                    });

			                    initTable(selector, tableKey, pane === 'tasks' ? 'Developer Task Details' : 'Developer Ticket Details', pane === 'tasks' ? [[3, 'desc']] : [[5, 'desc']]);
		                })
		                .always(function () {
		                    panel.removeClass('is-loading');
		                });
		        }

		        $(document).on('change', '[data-fh-developer-select]', function () {
		            var panel = $(this).closest('[data-fh-developer-panel]');
		            refreshDeveloperPanel(panel);
		        });

		        $(document).on('submit', '[data-fh-developer-form]', function (event) {
		            event.preventDefault();
		            refreshDeveloperPanel($(this).closest('[data-fh-developer-panel]'));
		        });

			        $(document).on('click', '[data-fh-developer-panel] [data-fh-dev-card]', function (event) {
			            event.preventDefault();
			            event.stopPropagation();
			            var panel = $(this).closest('[data-fh-developer-panel]');
			            panel.attr('data-active-card', $(this).data('fh-dev-card'));
			            refreshDeveloperPanel(panel);
			        });

			        $(document).on('click', '[data-fh-developer-panel] [data-fh-dev-pagination] a.fh-page-link', function (event) {
			            if ($(this).hasClass('is-disabled') || $(this).attr('href') === '#') {
			                event.preventDefault();
			                return;
		            }

		            event.preventDefault();
		            var panel = $(this).closest('[data-fh-developer-panel]');
		            var params = {};
		            var url = new URL($(this).attr('href'), window.location.origin);
		            url.searchParams.forEach(function (value, key) {
		                params[key] = value;
		            });
			            refreshDeveloperPanel(panel, params);
			        });

			        $(document).on('click', '[data-fh-pane-pagination] a.fh-page-link', function (event) {
			            event.preventDefault();

			            if ($(this).hasClass('is-disabled') || $(this).attr('href') === '#') {
			                return;
			            }

			            var pane = $(this).closest('[data-fh-pane-pagination]').data('fh-pane-pagination') || 'tickets';
			            var params = {};
			            var href = $(this).attr('href') || '';

			            try {
			                var url = new URL(href, window.location.origin);
			                url.searchParams.forEach(function (value, key) {
			                    params[key] = value;
			                });
			            } catch (e) {
			                params = $(this).data('fh-dashboard-state') || {};
			            }

			            params.pane = params.pane || pane;
			            refreshMainPane(params.pane, params);
			        });

			        $(document).on('click', '[data-fh-dashboard-state]:not([data-fh-dev-card]):not([data-fh-switch])', function (event) {
			            if ($(this).closest('[data-fh-pane-pagination]').length) {
			                return;
			            }

			            if ($(this).hasClass('is-disabled')) {
			                event.preventDefault();
			                return;
		            }

			            event.preventDefault();
				            var state = $(this).data('fh-dashboard-state') || {};
				            var pane = state.pane || 'tickets';
				            if ($(this).is('[data-fh-pane-card]')) {
				                var clickedCard = $(this).data('fh-card') || activeDashboardCard(pane);
				                state[pane === 'tasks' ? 'task_card' : 'ticket_card'] = clickedCard;
				                syncDashboardFormCard(pane, clickedCard);
				                updateDashboardFilterCount(pane, dashboardCardCount(this));
				            } else {
				                state[pane === 'tasks' ? 'task_card' : 'ticket_card'] = activeDashboardCard(pane);
				                syncDashboardFormCard(pane, state[pane === 'tasks' ? 'task_card' : 'ticket_card']);
				                syncDashboardFilterCountFromActiveCard(pane);
				            }
				            if (state.clear_filters) {
				                resetVisibleDashboardFilters(pane);
				            }
				            refreshMainPane(pane, state);
				        });

		        $(document).on('submit', '.fh-filter-card', function (event) {
		            event.preventDefault();

		            var params = {};
		            $.each($(this).serializeArray(), function (_, field) {
		                params[field.name] = field.value;
		            });

		            var pane = params.pane || 'tickets';
		            params[pane === 'tasks' ? 'task_card' : 'ticket_card'] = activeDashboardCard(pane);
		            syncDashboardFormCard(pane, params[pane === 'tasks' ? 'task_card' : 'ticket_card']);
		            refreshMainPane(pane, params);
		        });

		        $('[data-fh-switch]').on('click', function (event) {
		            if ($(this).hasClass('is-disabled')) {
		                event.preventDefault();
		                return;
		            }

		            event.preventDefault();
		            var state = $(this).data('fh-dashboard-state') || {};
		            var pane = state.pane || $(this).data('fh-switch') || 'tickets';
		            showPane(pane);
		            refreshMainPane(pane, state);
		        });

        showPane(@json($activePane));
    });
</script>
@endsection
