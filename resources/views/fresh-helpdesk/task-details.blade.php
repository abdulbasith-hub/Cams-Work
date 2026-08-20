@extends('index2')

@section('title', 'Helpdesk Task Details')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
@include('common.alert')

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
	
		    $taskStatus = fn ($status) => $taskStatusLabels[$status] ?? \Illuminate\Support\Str::headline((string) $status);
		    $taskStatusClass = fn ($status) => 'fht-status-'.\Illuminate\Support\Str::slug(trim((string) ($status ?: 'empty')));
		    $taskActionUrl = fn ($task, $action) => route('fresh-helpdesk.tasks.action', [$task->id, $action]);
	    $taskFlowSteps = [];
	    $filters = $filters ?? ['search' => '', 'status' => '', 'task_scope' => '', 'task_type' => '', 'developer_userid' => ''];
	    $developerFilterOptions = $developerFilterOptions ?? collect();
	    $taskResultCount = isset($tasks)
	        ? (method_exists($tasks, 'total') ? $tasks->total() : $tasks->count())
	        : 0;
	    $taskDownloadParams = ['download' => 'tasks'] + array_filter($filters, fn ($value) => trim((string) $value) !== '');

	if ($selectedTask ?? null) {
	    $currentTaskStatus = strtolower(trim((string) $selectedTask->task_status_by_tester));
	    $taskCurrentWith = \App\Models\FreshHelpdesk::taskCurrentlyWith($selectedTask);
	    $taskCurrentWithNormalized = strtolower(trim($taskCurrentWith));
	    $isConfirmed = !empty($selectedTask->verified_by);
	    $isClosed = $currentTaskStatus === \App\Models\FreshHelpdesk::TASK_CLOSED;
	    $isResolved = $currentTaskStatus === \App\Models\FreshHelpdesk::TASK_RESOLVED;
	    $isWronglyAssigned = $currentTaskStatus === \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED;
	    $isSeniorReviewStatus = $isResolved || $isWronglyAssigned;
	    $currentStep = match (true) {
	        $isClosed => 3,
	        $taskCurrentWithNormalized === 'nic admin' => 3,
	        str_contains($taskCurrentWithNormalized, 'senior') || $isSeniorReviewStatus => 2,
	        default => 1,
	    };
        $stepState = function (int $index) use ($currentStep, $isClosed) {
            if ($isClosed || $index < $currentStep) {
                return 'done';
            }

            return $index === $currentStep ? 'active' : 'pending';
        };
        $stepTime = function ($value) use ($fmt) {
            $formatted = $fmt($value);

            return $formatted === '-' ? null : $formatted;
        };

        $seniorCaption = $isConfirmed
            ? ($selectedTask->verified_by ?: 'Senior Developer')
            : (($selectedTask->senior_name ?? null) ?: ($isSeniorReviewStatus ? 'Awaiting Senior Review' : 'Senior Developer'));
        $closeCaption = $isClosed
            ? (($selectedTask->approved_by ?? null) ?: 'NIC Admin')
            : ($isConfirmed ? 'NIC Admin' : 'Pending');

        $taskFlowSteps = [
            [
                'title' => 'Created',
                'caption' => $selectedTask->assigned_by_name ?: 'NIC Admin',
                'time' => $stepTime($selectedTask->assigned_on ?? null),
                'state' => $stepState(0),
            ],
            [
                'title' => 'Developer',
                'caption' => $selectedTask->developer_name ?: 'Developer',
                'time' => $stepTime(($selectedTask->completed_on ?? null) ?: ($selectedTask->started_on ?? null)),
                'state' => $stepState(1),
            ],
            [
                'title' => 'Senior Review',
                'caption' => $seniorCaption,
                'time' => $stepTime($selectedTask->verified_on ?? null),
                'state' => $stepState(2),
            ],
            [
                'title' => 'NIC Close',
                'caption' => $closeCaption,
                'time' => $stepTime($selectedTask->approved_on ?? null),
                'state' => $stepState(3),
            ],
        ];
    }
@endphp

<style>
    .fht-page {
        max-width: 100%;
    }

    .fht-card,
    .fht-sheet,
    .fht-side-panel {
        background: #fff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
    }

    .fht-head {
        align-items: center;
        background: #5b7df0;
        border-radius: 8px 8px 0 0;
        color: #fff;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        padding: 12px 16px;
    }

    .fht-head h4 {
        color: #fff;
        font-size: 18px;
        font-weight: 850;
        margin: 0;
    }

    .fht-head p {
        color: rgba(255, 255, 255, .84);
        font-size: 12px;
        margin: 2px 0 0;
    }

    .fht-head-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .fht-head-actions .btn {
        font-weight: 750;
    }

    .fht-body {
        padding: 16px;
    }

    #freshHelpdeskTaskDetailsTable_wrapper .dataTables_info,
    #freshHelpdeskTaskDetailsTable_wrapper .dataTables_paginate,
    #freshHelpdeskTaskDetailsTable_wrapper .dataTables_length,
    #freshHelpdeskTaskDetailsTable_wrapper .dataTables_filter {
        display: none !important;
    }

    .fht-filter-card {
        background: #f8fbff;
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        margin-bottom: 16px;
        padding: 14px;
    }

    .fht-filter-card .form-control,
    .fht-filter-card .form-select {
        border-color: #d6e1ef;
        border-radius: 7px;
        font-size: 13px;
        min-height: 42px;
    }

	    .fht-filter-card .btn {
	        border-radius: 7px;
	        font-weight: 800;
	        min-height: 42px;
	        white-space: nowrap;
	    }

	    .fht-filter-count {
	        display: inline-flex;
	        align-items: center;
	        justify-content: center;
	        min-width: 24px;
	        min-height: 20px;
	        padding: 0 6px;
	        border-radius: 999px;
	        margin-left: 6px;
	        background: rgba(255, 255, 255, 0.92);
	        color: #1d4ed8;
	        font-size: 11px;
	        font-weight: 800;
	        line-height: 1;
	        white-space: nowrap;
	    }

	    .fht-page .dt-buttons {
	        display: none !important;
	    }

	    .fht-submit-spinner {
	        display: inline-block;
	        width: 0.95rem;
	        height: 0.95rem;
	        border: 2px solid rgba(255, 255, 255, 0.45);
	        border-top-color: #fff;
	        border-radius: 50%;
	        animation: fht-spin 0.75s linear infinite;
	        vertical-align: -2px;
	    }

	    .btn-outline-primary .fht-submit-spinner,
	    .btn-outline-danger .fht-submit-spinner,
	    .btn-light .fht-submit-spinner {
	        border-color: rgba(91, 125, 240, 0.25);
	        border-top-color: #5b7df0;
	    }

	    .fht-submit-button.is-submitting {
	        cursor: wait;
	        opacity: 0.84;
	    }

	    .fht-action-form.is-submitting textarea,
	    .fht-action-form.is-submitting select,
	    .fht-action-form.is-submitting input {
	        pointer-events: none;
	    }

	    @keyframes fht-spin {
	        to {
	            transform: rotate(360deg);
	        }
	    }

	    .fht-table {
        border-collapse: collapse !important;
        table-layout: fixed;
        width: 100% !important;
    }

    .fht-table th,
    .fht-table td {
        line-height: 1.35;
        overflow-wrap: anywhere !important;
        vertical-align: middle;
        white-space: normal !important;
        word-break: normal;
    }

    .fht-table thead th {
        background: #6b6b6b !important;
        border-color: #d8e2f0 !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 850;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(1),
    #freshHelpdeskTaskDetailsTable td:nth-child(1) {
        width: 6% !important;
        text-align: center;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(2),
    #freshHelpdeskTaskDetailsTable td:nth-child(2) {
        width: 22% !important;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(3),
    #freshHelpdeskTaskDetailsTable td:nth-child(3) {
        width: 8% !important;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(4),
    #freshHelpdeskTaskDetailsTable td:nth-child(4),
    #freshHelpdeskTaskDetailsTable th:nth-child(5),
    #freshHelpdeskTaskDetailsTable td:nth-child(5) {
        width: 10% !important;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(6),
    #freshHelpdeskTaskDetailsTable td:nth-child(6),
    #freshHelpdeskTaskDetailsTable th:nth-child(7),
    #freshHelpdeskTaskDetailsTable td:nth-child(7),
    #freshHelpdeskTaskDetailsTable th:nth-child(8),
    #freshHelpdeskTaskDetailsTable td:nth-child(8) {
        width: 12% !important;
    }

    #freshHelpdeskTaskDetailsTable th:nth-child(9),
    #freshHelpdeskTaskDetailsTable td:nth-child(9) {
        width: 8% !important;
        text-align: center;
    }

    .fht-task-no {
        color: #164c96;
        display: inline-block;
        font-weight: 850;
        overflow-wrap: anywhere;
        white-space: normal;
    }

    .fht-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .fht-cell-muted {
        color: #64748b;
        flex: 0 0 auto;
        font-size: 12px;
    }

	    .fht-badge {
	        align-items: center;
	        background: #eaf2ff;
        border-radius: 999px;
        color: #2454a6;
        display: inline-flex;
        font-size: 11px;
        font-weight: 850;
        line-height: 1;
        padding: 6px 10px;
	        white-space: nowrap;
	    }
	
	    .fht-status-not-started {
	        background: #f1f5f9;
	        color: #475569;
	    }
	
	    .fht-status-in-progress {
	        background: #eaf2ff;
	        color: #1d4ed8;
	    }
	
	    .fht-status-need-clarification {
	        background: #fff7df;
	        color: #9a5b00;
	    }
	
	    .fht-status-wrongly-assigned {
	        background: #fff1f2;
	        color: #b4233a;
	    }
	
	    .fht-status-resolved {
	        background: #eaf7ef;
	        color: #15803d;
	    }
	
	    .fht-status-closed {
	        background: #eef2f7;
	        color: #334155;
	    }
	
	    .fht-status-empty {
	        background: #f1f5f9;
	        color: #64748b;
	    }

	    .fht-flow {
	        display: grid;
	        grid-template-columns: repeat(4, minmax(0, 1fr));
	        padding: 14px 6px 4px;
	    }

	    .fht-flow-step {
	        min-height: 92px;
	        padding: 0 10px;
	        position: relative;
	        text-align: center;
	    }

	    .fht-flow-step::after {
	        background: #d8e2f0;
	        content: "";
	        height: 4px;
	        left: calc(50% + 16px);
	        position: absolute;
	        top: 14px;
	        width: calc(100% - 32px);
	        z-index: 0;
	    }

	    .fht-flow-step:last-child::after {
	        display: none;
	    }

	    .fht-flow-step.is-done::after {
	        background: #16a34a;
	    }

	    .fht-flow-step.is-active::after {
	        background: linear-gradient(90deg, #2f6ee8 0%, #2f6ee8 45%, #d8e2f0 45%, #d8e2f0 100%);
	    }

	    .fht-flow-icon {
	        align-items: center;
	        background: #fff;
	        border: 3px solid #d8e2f0;
	        border-radius: 999px;
	        color: #8ba0bd;
	        display: inline-flex;
	        font-size: 14px;
	        font-weight: 850;
	        height: 32px;
	        justify-content: center;
	        line-height: 1;
	        margin: 0 auto 10px;
	        position: relative;
	        width: 32px;
	        z-index: 1;
	    }

	    .fht-flow-step.is-done .fht-flow-icon {
	        background: #16a34a;
	        border-color: #16a34a;
	        color: #fff;
	    }

	    .fht-flow-step.is-active .fht-flow-icon {
	        background: #2f6ee8;
	        border-color: #2f6ee8;
	        color: #fff;
	    }

	    .fht-flow-step strong {
	        color: #64748b;
	        display: block;
	        font-size: 13px;
	        font-weight: 850;
	    }

	    .fht-flow-step.is-done strong {
	        color: #107739;
	    }

	    .fht-flow-step.is-active strong {
	        color: #0b52d6;
	    }

	    .fht-flow-step span {
	        color: #64748b;
	        display: block;
	        font-size: 12px;
	        margin-top: 4px;
	        overflow-wrap: anywhere;
	    }

	    .fht-flow-time {
	        color: #7b8da8;
	        display: block;
	        font-size: 11px;
	        font-style: italic;
	        margin-top: 3px;
	    }

    .fht-sheet-hero {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 14px 16px;
    }

    .fht-sheet-hero span {
        color: #4b5f80;
        display: block;
        font-size: 11px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .fht-sheet-hero h3 {
        color: #06122f;
        font-size: 22px;
        font-weight: 900;
        margin: 3px 0 0;
    }

    .fht-sheet-header {
        border-bottom: 1px solid #d8e2f0;
        color: #06122f;
        font-size: 17px;
        font-weight: 850;
        padding: 13px 16px;
    }

	    .fht-sheet-body {
	        padding: 14px 16px;
	    }

	    .fht-info-grid {
	        display: grid;
	        gap: 0 12px;
	        grid-template-columns: repeat(3, minmax(0, 1fr));
	        margin-left: 0;
	        margin-right: 0;
	    }

	    .fht-info-grid > [class*="col-"] {
	        max-width: none;
	        padding-left: 0;
	        padding-right: 0;
	        width: auto;
	    }

		    .fht-info,
	    .fht-note {
	        align-items: start;
	        border: none;
	        border-bottom: 1px solid #eef1f6;
	        border-radius: 0;
	        background: transparent;
	        box-sizing: border-box;
	        column-gap: 16px;
	        display: grid;
		        grid-template-columns: 154px minmax(0, 1fr);
	        height: 100%;
	        min-height: unset;
	        padding: 11px 0;
	        row-gap: 3px;
	    }

    .fht-note {
        min-height: 74px;
    }

	    .fht-info small,
	    .fht-note small {
	        color: #2f6ee8;
	        display: inline-flex;
	        font-size: 12px;
	        font-weight: 800;
	        gap: 5px;
	        grid-column: 1;
	        min-width: 0;
	        line-height: 1.35;
	        margin: 0;
	        text-transform: uppercase;
	        white-space: normal;
	    }

    .fht-info-icon {
        color: #2f6ee8;
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 12px;
        line-height: 1.4;
    }

    .fht-info p,
    .fht-note p {
        grid-column: 2 / -1;
    }

	    .fht-info strong,
	    .fht-note strong {
	        color: #06122f;
	        display: block;
	        font-size: 14px;
	        font-weight: 800;
	        grid-column: 2;
	        line-height: 1.35;
	        min-width: 0;
	        opacity: 1;
	        overflow-wrap: anywhere;
	        text-align: left;
	    }

    .fht-note strong {
        margin-top: 2px;
    }

    .fht-note p {
        color: #1f2f46;
        font-size: 13.5px;
        font-weight: 500;
        line-height: 1.45;
        margin: 0;
        white-space: pre-wrap;
        word-break: break-word;
    }

	    .fht-info .fht-cell-muted {
	        display: block;
	        grid-column: 2;
	        margin-left: 0;
	        min-width: 0;
	        overflow-wrap: anywhere;
	        white-space: normal;
	    }

	    @media (max-width: 575.98px) {
	        .fht-info-grid {
	            grid-template-columns: 1fr;
	        }

	        .fht-info,
	        .fht-note {
            grid-template-columns: 1fr;
        }

        .fht-info small,
        .fht-note small,
        .fht-info strong,
        .fht-note strong,
        .fht-info .fht-cell-muted,
        .fht-note p {
            grid-column: 1;
            min-width: 0;
        }
    }

	    .fht-timeline-item {
	        border: 1px solid #d8e2f0;
	        border-left: 4px solid #2f6ee8;
	        border-radius: 8px;
	        padding: 12px 14px;
	    }

	    .fht-timeline-item.is-hosted {
	        background: #fffdf2;
	        border-color: #facc15;
	        border-left-color: #eab308;
	        box-shadow: inset 0 0 0 1px rgba(250, 204, 21, 0.18);
	    }

	    .fht-timeline-item.is-hosted small {
	        color: #a16207;
	    }

    .fht-timeline-item + .fht-timeline-item {
        margin-top: 10px;
    }

    .fht-timeline-item small {
        color: #2454a6;
        display: block;
        font-size: 11px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .fht-timeline-item p {
        margin: 6px 0 0;
        white-space: pre-wrap;
    }

    .fht-side-panel {
        border-top: 4px solid #2f6ee8;
        margin-bottom: 12px;
        padding: 14px;
    }

    .fht-side-panel h5 {
        color: #06122f;
        font-size: 15px;
        font-weight: 850;
        margin: 0 0 12px;
    }

    .fht-dt-toolbar,
    .fht-dt-footer {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
    }

	    .fht-dt-toolbar {
	        margin-bottom: 10px;
	        width: 100%;
	    }

	    .fht-result-count {
	        align-items: center;
	        background: #eef5ff;
	        border: 1px solid #cfe0ff;
	        border-radius: 7px;
	        color: #164c96;
	        display: inline-flex;
	        font-size: 13px;
	        font-weight: 850;
	        min-height: 36px;
	        padding: 7px 12px;
	    }

	    .fht-dt-left {
	        align-items: center;
	        display: flex;
	        flex-wrap: wrap;
	        gap: 8px;
	        margin-right: auto;
	    }

    .fht-dt-right {
        margin-left: auto;
    }

    .fht-dt-footer {
        padding-top: 12px;
    }

    .fht-pagination {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .fht-page-link,
    .fht-page-current {
        align-items: center !important;
        border: 1px solid #d8e2f0 !important;
        border-radius: 7px !important;
        display: inline-flex !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        justify-content: center !important;
        line-height: 1 !important;
        min-height: 34px !important;
        min-width: 34px !important;
        padding: 0 10px !important;
        text-decoration: none !important;
    }

    .fht-page-link {
        background: #fff !important;
        color: #2454a6 !important;
    }

    .fht-page-link:hover {
        background: #eef5ff !important;
        color: #174c78 !important;
        text-decoration: none !important;
    }

    .fht-page-link.is-disabled {
        color: #94a3b8 !important;
        pointer-events: none !important;
    }

    .fht-page-current {
        background: #5b7df0 !important;
        border-color: #5b7df0 !important;
        color: #fff !important;
    }

    .fht-page-ellipsis {
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

    .fht-dt-toolbar .dataTables_filter {
        margin-left: auto;
        text-align: right;
    }

    .fht-dt-toolbar .dt-buttons {
        margin-right: auto;
    }

    .dataTables_filter input {
        border: 1px solid #9ebcf2;
        border-radius: 6px;
        min-height: 34px;
        padding: 5px 8px;
    }

    .fht-dt-toolbar .dataTables_filter input {
        width: 220px;
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

	    @media (max-width: 767.98px) {
        .fht-head,
        .fht-sheet-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .fht-head-actions,
        .fht-head-actions .btn,
        .fht-filter-card .btn,
        .fht-filter-card .form-control,
        .fht-filter-card .form-select,
        .dataTables_filter,
        .dataTables_filter label,
	        .dataTables_filter input {
	            width: 100%;
	        }

	        .fht-flow {
	            gap: 0;
	            grid-template-columns: 1fr;
	            padding-left: 8px;
	        }

	        .fht-flow-step {
	            min-height: auto;
	            padding: 0 0 18px 44px;
	            text-align: left;
	        }

	        .fht-flow-step::after {
	            height: calc(100% - 30px);
	            left: 14px;
	            top: 32px;
	            width: 4px;
	        }

	        .fht-flow-step.is-active::after {
	            background: linear-gradient(180deg, #2f6ee8 0%, #2f6ee8 45%, #d8e2f0 45%, #d8e2f0 100%);
	        }

	        .fht-flow-icon {
	            left: 0;
	            margin: 0;
	            position: absolute;
	            top: 0;
	        }
	    }
	</style>

@if (!$selectedTask)
    <div class="fht-page">
        <div class="fht-card">
            <div class="fht-head">
                <div>
                    <h4>Task Details</h4>
                    <p>{{ $roleLabel }} task management</p>
                </div>
                <div class="fht-head-actions">
                    @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
                        <a href="{{ route('fresh-helpdesk.assign-task') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-plus me-1"></i> Assign Task
                        </a>
                    @endif
                    <a href="{{ route('fresh-helpdesk.dashboard', ['pane' => 'tasks']) }}" class="btn btn-outline-light btn-sm">
                        <i class="ti ti-layout-dashboard me-1"></i> Dashboard
                    </a>
                </div>
            </div>
	            <div class="fht-body">
		                @if ($errors->any())
		                    <div class="alert alert-danger">{{ $errors->first() }}</div>
		                @endif
	
		                @php
		                    $taskFilterRouteParams = [];
		                    if ($selectedTask ?? null) {
		                        $taskFilterRouteParams['task'] = \App\Models\FreshHelpdesk::taskUrlToken($selectedTask->id);
		                    }
		                    $taskFilterResetParams = $taskFilterRouteParams + ['clear_filters' => 1];
		                @endphp
	                <form class="fht-filter-card" id="freshTaskFilterForm" method="POST" action="{{ route('fresh-helpdesk.task-details.filter') }}">
	                    @csrf
	                    <div class="row g-2 align-items-center">
		                        <div class="col-lg-2 col-md-12">
		                            <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search task, developer, assigned by">
		                        </div>
	                        <div class="col-lg-2 col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                @foreach ($taskStatusLabels as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" @selected(($filters['status'] ?? '') === $statusKey)>{{ $statusLabel }}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                        <div class="col-lg-2 col-md-4">
	                            <select name="task_scope" class="form-select">
	                                <option value="" @selected(($filters['task_scope'] ?? '') === '')>All</option>
	                                <option value="currently_me" @selected(($filters['task_scope'] ?? '') === 'currently_me')>Currently Me</option>
	                            </select>
	                        </div>
	                        <div class="col-lg-2 col-md-4">
	                            <select name="task_type" class="form-select">
                                <option value="">All types</option>
                                <option value="new" @selected(($filters['task_type'] ?? '') === 'new')>New</option>
                                <option value="existing" @selected(($filters['task_type'] ?? '') === 'existing')>Existing</option>
                            </select>
                        </div>
	                        @if (in_array($role, [\App\Models\FreshHelpdesk::ROLE_NIC_ADMIN, \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER], true))
	                            <div class="col-lg-2 col-md-4">
	                                <select name="developer_userid" class="form-select">
	                                    <option value="">All developers</option>
	                                    @foreach ($developerFilterOptions as $developer)
                                        <option value="{{ $developer->devuserid }}" @selected((string) ($filters['developer_userid'] ?? '') === (string) $developer->devuserid)>
                                            {{ $developer->devename }}
	                                        </option>
	                                    @endforeach
	                                </select>
	                            </div>
	                        @endif
	                        <div class="col-lg-2 col-md-8">
	                            <div class="d-flex gap-2">
		                                <button type="submit" id="freshTaskFilterBtn" class="btn btn-primary flex-fill">
		                                    Filter <span class="fht-filter-count">{{ number_format($taskResultCount) }}</span>
		                                </button>
	                                <a href="{{ route('fresh-helpdesk.task-details', $taskFilterRouteParams) }}" class="btn btn-outline-secondary" title="Refresh filters" aria-label="Refresh filters">
	                                    <i class="ti ti-refresh"></i>
	                                </a>
	                                <a href="{{ route('fresh-helpdesk.task-details', $taskFilterResetParams) }}" class="btn btn-outline-danger" title="Reset filters" aria-label="Reset filters">
	                                    <i class="ti ti-rotate-clockwise"></i>
	                                </a>
	                            </div>
	                        </div>
	                    </div>
	                </form>

	                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
	                    <a href="{{ route('fresh-helpdesk.task-details', $taskDownloadParams) }}" id="freshTaskDownloadBtn" class="btn btn-primary btn-sm">
	                        <i class="ti ti-download me-1"></i> Download
	                    </a>
	                </div>

                <div class="table-responsive">
                    <table id="freshHelpdeskTaskDetailsTable" class="table table-striped table-bordered align-middle fht-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Task / Assigned By</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Currently With</th>
                                <th>Assigned On</th>
                                <th>Expected On</th>
                                <th>Updated On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
	                            @foreach ($tasks as $task)
	                                <tr>
	                                    <td>{{ $tasks->firstItem() + $loop->index }}</td>
	                                    <td>
                                        <strong class="fht-task-no">{{ $task->process_assigned ?: '#'.$task->id }}</strong>
                                        <span class="fht-muted">Assigned by {{ $task->assigned_by_name ?: '-' }}</span>
                                    </td>
                                    <td>{{ ucfirst((string) $task->task_type) }}</td>
	                                    <td><span class="fht-badge {{ $taskStatusClass($task->task_status_by_tester) }}">{{ $taskStatus($task->task_status_by_tester) }}</span></td>
                                    <td>{{ \App\Models\FreshHelpdesk::taskCurrentlyWith($task) }}</td>
	                                    <td data-order="{{ $dateOrderValue($task->assigned_on ?: $task->created_at) }}">{{ $fmt($task->assigned_on) }}</td>
                                    <td>{{ $fmt($task->expected_date_to_complete) }}</td>
                                    <td>{{ $fmt($task->updated_at) }}</td>
                                    <td>
                                        <a href="{{ route('fresh-helpdesk.task-details', ['task' => \App\Models\FreshHelpdesk::taskUrlToken($task->id)]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye me-1"></i> View
                                        </a>
	                                    </td>
	                                </tr>
	                            @endforeach
	                        </tbody>
	                    </table>
	                </div>
	                @if ($tasks->count() === 0)
	                    <div id="freshTaskEmptyState" class="text-center text-muted py-3">No tasks found.</div>
	                @else
	                    <div id="freshTaskEmptyState" class="text-center text-muted py-3 d-none">No tasks found.</div>
	                @endif

	                <div id="freshTaskPaginationWrap">
	                @if ($tasks->hasPages())
                    @php
                        $fhtEdge = 3;
                        $fhtWindow = 1;
                        $fhtLast = $tasks->lastPage();
                        $fhtCurrent = $tasks->currentPage();
                        $fhtPageItems = [];
                        $fhtLastShown = 0;

                        for ($fhtPage = 1; $fhtPage <= $fhtLast; $fhtPage++) {
                            if ($fhtPage <= $fhtEdge || $fhtPage > $fhtLast - $fhtEdge || abs($fhtPage - $fhtCurrent) <= $fhtWindow) {
                                if ($fhtLastShown && $fhtPage - $fhtLastShown > 1) {
                                    $fhtPageItems[] = '...';
                                }
                                $fhtPageItems[] = $fhtPage;
                                $fhtLastShown = $fhtPage;
                            }
                        }
                    @endphp
                    <div class="fht-dt-footer">
                        <span class="text-muted">
                            Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} entries
                        </span>
                        <div class="fht-pagination">
                            <a class="fht-page-link {{ $tasks->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $tasks->previousPageUrl() ?: '#' }}">Previous</a>
                            @foreach ($fhtPageItems as $fhtItem)
                                @if ($fhtItem === '...')
                                    <span class="fht-page-ellipsis">&hellip;</span>
                                @elseif ($fhtItem === $fhtCurrent)
                                    <span class="fht-page-current">{{ $fhtItem }}</span>
	                                @else
                                    <a class="fht-page-link" href="{{ $tasks->url($fhtItem) }}">{{ $fhtItem }}</a>
                                @endif
                            @endforeach
                            <a class="fht-page-link {{ $tasks->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $tasks->nextPageUrl() ?: '#' }}">Next</a>
                        </div>
                    </div>
                @endif
	                </div>
            </div>
        </div>
    </div>
@else
    <div class="fht-page">
        <div class="fht-sheet fht-sheet-hero">
            <div>
                <span>Task Management</span>
                <h3>Task Sheet</h3>
            </div>
            <div class="fht-head-actions">
                <a href="{{ route('fresh-helpdesk.task-details') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left me-1"></i> Back to Task List
                </a>
                @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
                    <a href="{{ route('fresh-helpdesk.assign-task') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Assign Task
                    </a>
                @endif
            </div>
        </div>

	        @if ($errors->any())
	            <div class="alert alert-danger">{{ $errors->first() }}</div>
	        @endif

	        <div class="row g-3">
	            <div class="col-xl-9 col-lg-8">
	                <div class="fht-sheet mb-3">
	                    <div class="fht-sheet-header">Flow Map</div>
	                    <div class="fht-sheet-body">
	                        <div class="fht-flow">
	                            @foreach ($taskFlowSteps as $flowStep)
	                                <div class="fht-flow-step is-{{ $flowStep['state'] }}">
	                                    <div class="fht-flow-icon">
	                                        @if ($flowStep['state'] === 'done')
	                                            <span aria-hidden="true">&check;</span>
	                                        @elseif ($flowStep['state'] === 'active')
	                                            <span aria-hidden="true">&bull;</span>
	                                        @else
	                                            <span aria-hidden="true"></span>
	                                        @endif
	                                    </div>
	                                    <strong>{{ $flowStep['title'] }}</strong>
	                                    <span>{{ $flowStep['caption'] }}</span>
	                                    <em class="fht-flow-time">{{ $flowStep['time'] ?: '-' }}</em>
	                                </div>
	                            @endforeach
	                        </div>
	                    </div>
	                </div>

	                <div class="fht-sheet mb-3">
	                    <div class="fht-sheet-header">Task Information</div>
                    <div class="fht-sheet-body">
	                        <div class="row g-2 fht-info-grid">
	                            <div class="col-md-4">
                                <div class="fht-info">
                                    <small><i class="ti ti-subtask fht-info-icon"></i> Task</small>
                                    <strong>{{ $selectedTask->process_assigned ?: '#'.$selectedTask->id }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fht-info">
                                    <small><i class="ti ti-category fht-info-icon"></i> Type</small>
                                    <strong>{{ ucfirst((string) $selectedTask->task_type) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fht-info">
                                    <small><i class="ti ti-activity fht-info-icon"></i> Status</small>
	                                    <strong><span class="fht-badge {{ $taskStatusClass($selectedTask->task_status_by_tester) }}">{{ $taskStatus($selectedTask->task_status_by_tester) }}</span></strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-info">
                                    <small><i class="ti ti-user fht-info-icon"></i> Assigned By</small>
                                    <strong>{{ $selectedTask->assigned_by_name ?: '-' }}</strong>
                                    <span class="fht-cell-muted">- {{ $fmt($selectedTask->assigned_on) }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
	                                <div class="fht-info">
	                                    <small><i class="ti ti-user-check fht-info-icon"></i> Currently With</small>
	                                    <strong>{{ $taskCurrentWith }}</strong>
	                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-info">
                                    <small><i class="ti ti-shield-check fht-info-icon"></i> Senior Review</small>
                                    <strong>{{ $selectedTask->verified_by ?: ($selectedTask->task_status_by_tester === \App\Models\FreshHelpdesk::TASK_RESOLVED ? 'Awaiting confirmation' : '-') }}</strong>
                                    {{-- <span class="fht-cell-muted">- Any Senior Developer can review</span> --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-info">
                                    <small><i class="ti ti-calendar-time fht-info-icon"></i> Expected On</small>
                                    <strong>{{ $fmt($selectedTask->expected_date_to_complete) }}</strong>
                                </div>
                            </div>
	                            <div class="col-md-4">
	                                <div class="fht-info">
	                                    <small><i class="ti ti-player-play fht-info-icon"></i> Started On</small>
	                                    <strong>{{ $fmt($selectedTask->started_on) }}</strong>
	                                </div>
	                            </div>
		                            <div class="col-md-4">
		                                <div class="fht-info">
		                                    @php
		                                        $hasHostedStaging = !empty($selectedTask->hosted_in_staging)
		                                            || data_get($selectedTask, 'hosted_in_staging_by')
		                                            || data_get($selectedTask, 'hosted_in_staging_on');
		                                    @endphp
		                                    <small><i class="ti ti-server fht-info-icon"></i> Hosted In Staging</small>
		                                    <strong>{{ $hasHostedStaging ? 'Yes' : '' }}</strong>
		                                    @if (data_get($selectedTask, 'hosted_in_staging_by') || data_get($selectedTask, 'hosted_in_staging_on'))
		                                        <span class="fht-cell-muted">
		                                            {{ data_get($selectedTask, 'hosted_in_staging_by') ?: '-' }}{{ data_get($selectedTask, 'hosted_in_staging_on') ? ' | '.$fmt(data_get($selectedTask, 'hosted_in_staging_on')) : '' }}
		                                        </span>
		                                    @endif
		                                </div>
		                            </div>
		                            <div class="col-md-4">
		                                <div class="fht-info">
		                                    @php
		                                        $hasHostedProduction = !empty($selectedTask->deployed_in_live_server)
		                                            || data_get($selectedTask, 'hosted_in_production_by')
		                                            || data_get($selectedTask, 'hosted_in_production_on');
		                                    @endphp
		                                    <small><i class="ti ti-cloud-upload fht-info-icon"></i> Hosted In Production</small>
		                                    <strong>{{ $hasHostedProduction ? 'Yes' : '' }}</strong>
		                                    @if (data_get($selectedTask, 'hosted_in_production_by') || data_get($selectedTask, 'hosted_in_production_on'))
		                                        <span class="fht-cell-muted">
		                                            {{ data_get($selectedTask, 'hosted_in_production_by') ?: '-' }}{{ data_get($selectedTask, 'hosted_in_production_on') ? ' | '.$fmt(data_get($selectedTask, 'hosted_in_production_on')) : '' }}
		                                        </span>
		                                    @endif
		                                </div>
		                            </div>
	                            <div class="col-md-4">
	                                <div class="fht-info">
                                    <small><i class="ti ti-checks fht-info-icon"></i> Completed On</small>
                                    <strong>{{ $fmt($selectedTask->completed_on) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-info">
                                    <small><i class="ti ti-refresh fht-info-icon"></i> Updated On</small>
                                    <strong>{{ $fmt($selectedTask->updated_at) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="fht-note h-100">
                                    <small><i class="ti ti-file-description fht-info-icon"></i> Description</small>
                                    <p>{{ $selectedTask->testing_task_description ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-note h-100">
                                    <small><i class="ti ti-code fht-info-icon"></i> Developer Remarks</small>
                                    <p>{{ $selectedTask->remarks_by_developer ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fht-note h-100">
                                    <small><i class="ti ti-message-circle fht-info-icon"></i> Senior Remarks</small>
                                    <p>{{ $selectedTask->remarks_by_verifier ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="fht-note">
                                    <small><i class="ti ti-briefcase fht-info-icon"></i> Project Head Remarks</small>
                                    <p>{{ $selectedTask->remarks_by_project_head ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fht-sheet">
	                    <div class="fht-sheet-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
	                        <span>Task Timeline</span>
	                        <span class="fht-badge">{{ $taskHistories->count() }}</span>
	                    </div>
		                    <div class="fht-sheet-body">
		                        @php
		                            $taskTimelineDisplay = $taskHistories
		                                ->sortByDesc(function ($history) {
		                                    $performedAt = $history->performed_at ?? null;
		                                    $timestamp = 0;

		                                    if ($performedAt) {
		                                        try {
		                                            $timestamp = \Carbon\Carbon::parse($performedAt)->timestamp;
		                                        } catch (\Throwable $exception) {
		                                            $timestamp = 0;
		                                        }
		                                    }

		                                    return sprintf('%010d|%010d', $timestamp, (int) ($history->id ?? 0));
		                                })
		                                ->values();
		                            $taskTimelineCount = $taskTimelineDisplay->count();
		                        @endphp
		                        @forelse ($taskTimelineDisplay as $history)
		                            <div class="fht-timeline-item @if(\Illuminate\Support\Str::startsWith((string) ($history->action_key ?? ''), 'hosted_in_')) is-hosted @endif">
		                                <small>{{ $taskTimelineCount - $loop->index }}. {{ $history->stage ?: ($history->performed_by_role ?: 'Update') }}</small>
	                                <p>{{ $history->comment ?: '-' }}</p>
                                <span class="fht-muted">
                                    {{ $history->performed_by_name ?: '-' }} | {{ $fmt($history->performed_at) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-muted text-center py-3">No timeline found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

		            <div class="col-xl-3 col-lg-4">
			                @if (!($role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER && in_array((string) ($selectedTask->task_status_by_tester ?? ''), [\App\Models\FreshHelpdesk::TASK_RESOLVED, \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED, \App\Models\FreshHelpdesk::TASK_CLOSED], true)))
		                <div class="fht-side-panel">
	                    <h5>Task Action</h5>
	                    @php
	                        $viewerId = (string) \App\Models\FreshHelpdesk::developerUserId();
			                        $isSenior = $role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER;
			                        $isDeveloper = \App\Models\FreshHelpdesk::taskAssignedToCurrentDeveloper($selectedTask, $viewerId);
				                        $isResolved = $selectedTask->task_status_by_tester === \App\Models\FreshHelpdesk::TASK_RESOLVED;
			                        $isWronglyAssigned = $selectedTask->task_status_by_tester === \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED;
			                        $isSeniorReviewStatus = $isResolved || $isWronglyAssigned;
		                        $isClosed = $selectedTask->task_status_by_tester === \App\Models\FreshHelpdesk::TASK_CLOSED;
		                        $isConfirmed = !empty($selectedTask->verified_by);
				                        $selectedTaskStatus = in_array((string) ($selectedTask->task_status_by_tester ?? ''), [
					                            \App\Models\FreshHelpdesk::TASK_NOT_STARTED,
					                            \App\Models\FreshHelpdesk::TASK_IN_PROGRESS,
					                            \App\Models\FreshHelpdesk::TASK_NEED_CLARIFICATION,
					                            \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED,
					                            \App\Models\FreshHelpdesk::TASK_RESOLVED,
				                        ], true)
			                            ? (string) $selectedTask->task_status_by_tester
			                            : \App\Models\FreshHelpdesk::TASK_NOT_STARTED;
			                        $canDeveloperUpdateTask = in_array($role, [\App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER, \App\Models\FreshHelpdesk::ROLE_DEVELOPER], true)
			                            && $isDeveloper
				                            && !$isClosed
				                            && !$isResolved
				                            && !$isWronglyAssigned
				                            && !$isConfirmed
			                            && empty($selectedTask->approved_by);
			                        $canAddTaskComment = !$isClosed && empty($selectedTask->approved_by);
			                        $hostedOptions = \App\Models\FreshHelpdesk::hostedInLabels();
			                    @endphp
			                    @if ($canDeveloperUpdateTask)
			                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'developer_resolve') }}">
			                            @csrf
			                            <label class="form-label fw-bold">Developer Status</label>
			                            <select name="task_status" class="form-select mb-2 js-task-status-select" required>
			                                @if ($selectedTaskStatus === \App\Models\FreshHelpdesk::TASK_NOT_STARTED)
			                                    <option value="" selected disabled>{{ $taskStatus(\App\Models\FreshHelpdesk::TASK_NOT_STARTED) }}</option>
			                                @endif
					                                @foreach ([\App\Models\FreshHelpdesk::TASK_IN_PROGRESS, \App\Models\FreshHelpdesk::TASK_NEED_CLARIFICATION, \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED, \App\Models\FreshHelpdesk::TASK_RESOLVED] as $taskStatusValue)
				                                    <option value="{{ $taskStatusValue }}" @selected($selectedTaskStatus === $taskStatusValue)>
				                                        {{ $taskStatus($taskStatusValue) }}
				                                    </option>
			                                @endforeach
			                            </select>
		                            <label class="form-label fw-bold">Started On</label>
		                            @php
		                                $taskStartedSaved = !empty($selectedTask->started_on);
		                                $taskStartedValue = $taskStartedSaved
		                                    ? \Carbon\Carbon::parse($selectedTask->started_on, 'Asia/Kolkata')
		                                    : now('Asia/Kolkata');
		                                $taskStartedInputValue = $taskStartedValue->format('Y-m-d\TH:i');
		                            @endphp
		                            <input type="datetime-local" name="started_on" class="form-control mb-2"
		                                value="{{ $taskStartedInputValue }}"
		                                min="{{ $taskStartedInputValue }}"
		                                max="{{ $taskStartedInputValue }}"
		                                readonly>
		                            @if ($role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER)
		                                <label class="form-label fw-bold">Hosted In</label>
		                                <select name="hosted_in" class="form-select mb-2">
		                                    <option value="">Select hosted environment</option>
		                                    @foreach ($hostedOptions as $hostedValue => $hostedLabel)
		                                        <option value="{{ $hostedValue }}" @selected(\App\Models\FreshHelpdesk::taskHostedInValue($selectedTask) === $hostedValue)>{{ $hostedLabel }}</option>
		                                    @endforeach
		                                </select>
		                            @endif
			                            <textarea name="remarks" rows="4" class="form-control mb-2" placeholder="Developer remarks" required></textarea>
		                            <button class="btn btn-outline-primary w-100 mb-2" type="submit">
		                                <i class="ti ti-refresh me-1"></i> Update Status
		                            </button>
			                            <button class="btn btn-success w-100 js-task-forward-senior" type="submit" name="forward_to_senior" value="1">
			                                <i class="ti ti-send me-1"></i> Forward to Senior Developer
			                            </button>
		                        </form>
		                    @elseif ($role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER && $isSenior && $isSeniorReviewStatus && !$isConfirmed)
		                        @if ($isResolved)
		                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'senior_confirm') }}" class="mb-2">
	                            @csrf
	                            <label class="form-label fw-bold">Hosted In</label>
	                            <select name="hosted_in" class="form-select mb-2">
	                                <option value="">Select hosted environment</option>
	                                @foreach ($hostedOptions as $hostedValue => $hostedLabel)
	                                    <option value="{{ $hostedValue }}" @selected(\App\Models\FreshHelpdesk::taskHostedInValue($selectedTask) === $hostedValue)>{{ $hostedLabel }}</option>
	                                @endforeach
	                            </select>
	                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Confirmation remarks (optional)"></textarea>
	                            <button class="btn btn-success w-100" type="submit">
	                                <i class="ti ti-shield-check me-1"></i> Confirm Resolution
                            </button>
	                        </form>
	                        @endif
                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'senior_return') }}" class="mb-2">
                            @csrf
                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Reason for sending back" required></textarea>
                            <button class="btn btn-outline-danger w-100" type="submit">
                                <i class="ti ti-arrow-back-up me-1"></i> Send Back to Developer
                            </button>
                        </form>
                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'senior_reassign') }}">
                            @csrf
                            <select name="developer_userid" class="form-select mb-2" required>
                                <option value="">Select developer</option>
                                @foreach ($developers as $person)
                                    <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                @endforeach
                            </select>
                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Reassignment remarks" required></textarea>
                            <button class="btn btn-outline-primary w-100" type="submit">
                                <i class="ti ti-user-check me-1"></i> Assign to Another Developer
                            </button>
                        </form>
	                    @elseif ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN && $isResolved && $isConfirmed)
	                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'nic_close') }}">
	                            @csrf
	                            <label class="form-label fw-bold">Hosted In</label>
	                            <select name="hosted_in" class="form-select mb-2">
	                                <option value="">Select hosted environment</option>
	                                @foreach ($hostedOptions as $hostedValue => $hostedLabel)
	                                    <option value="{{ $hostedValue }}" @selected(\App\Models\FreshHelpdesk::taskHostedInValue($selectedTask) === $hostedValue)>{{ $hostedLabel }}</option>
	                                @endforeach
	                            </select>
	                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Closure remarks"></textarea>
	                            <button class="btn btn-success w-100" type="submit">
	                                <i class="ti ti-check me-1"></i> Close Task
                            </button>
                        </form>
                    @elseif ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN && $isClosed)
                        <form method="POST" action="{{ $taskActionUrl($selectedTask, 'reopen') }}">
                            @csrf
                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Reason for reopening" required></textarea>
                            <button class="btn btn-outline-warning w-100" type="submit">
                                <i class="ti ti-refresh me-1"></i> Reopen for Senior Review
                            </button>
                        </form>
		                    @elseif ($isDeveloper && $isSeniorReviewStatus)
		                        @if ($isWronglyAssigned)
		                            <p class="text-muted mb-0">Wrongly assigned and locked - awaiting Senior Developer review.</p>
		                        @else
                        <p class="text-muted mb-0">Resolved and locked — awaiting {{ $isConfirmed ? 'NIC Admin closure' : 'Senior Developer review' }}.</p>
			                        @endif
	                    @else
	                        <p class="text-muted mb-0">No action pending for your role.</p>
	                    @endif
		                </div>
		                @endif

		                @if (!in_array((string) ($selectedTask->task_status_by_tester ?? ''), [\App\Models\FreshHelpdesk::TASK_CLOSED], true) && empty($selectedTask->approved_by))
		                <div class="fht-side-panel">
		                    <h5>Add Comment</h5>
		                    <form method="POST" action="{{ $taskActionUrl($selectedTask, 'comment') }}">
		                        @csrf
		                        <textarea name="remarks" rows="4" class="form-control mb-2" placeholder="Enter task comment" required></textarea>
		                        <button class="btn btn-primary w-100" type="submit">
	                            <i class="ti ti-message-circle me-1"></i> Add Comment
		                        </button>
		                    </form>
		                </div>
		                @endif
		            </div>
	        </div>
    </div>
@endif

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
		<script>
		    $(function () {
		        var successMessage = @json(session('success'));
		        var tableId = '#freshHelpdeskTaskDetailsTable';
		        function initTaskDataTable() {
		            if (!$(tableId).length) {
		                return;
		            }

		            if ($.fn.DataTable.isDataTable(tableId)) {
		                $(tableId).DataTable().clear().destroy();
		            }

		            $(tableId).DataTable({
		                processing: true,
		                serverSide: false,
		                lengthChange: false,
		                paging: false,
		                info: false,
		                searching: false,
		                autoWidth: false,
		                dom: 'rt',
		                order: [[5, 'desc']]
		            });
		        }

		        initTaskDataTable();

		        $('#freshTaskFilterForm').on('submit', function (event) {
		            event.preventDefault();

		            var $form = $(this);
		            var $button = $('#freshTaskFilterBtn');

		            if ($form.data('loading')) {
		                return;
		            }

		            $form.data('loading', true);
		            $button.prop('disabled', true);

		            $.ajax({
		                url: $form.attr('action'),
		                method: 'POST',
		                data: $form.serialize(),
		                headers: { 'X-Requested-With': 'XMLHttpRequest' }
		            }).done(function (response) {
		                if (!response || !response.success) {
		                    return;
		                }

		                if ($.fn.DataTable.isDataTable(tableId)) {
		                    $(tableId).DataTable().clear().destroy();
		                }

		                $(tableId + ' tbody').html(response.rows_html || '');
		                $('#freshTaskPaginationWrap').html(response.pagination_html || '');
		                $('#freshTaskEmptyState').toggleClass('d-none', !response.empty);
		                $('.fht-filter-count').text(response.count || 0);
		                if (response.download_url) {
		                    $('#freshTaskDownloadBtn').attr('href', response.download_url);
		                }
		                initTaskDataTable();
		            }).fail(function () {
		                alert('Unable to filter tasks. Please try again.');
		            }).always(function () {
		                $form.data('loading', false);
		                $button.prop('disabled', false);
		            });
		        });
	
			        var forwardableTaskStatuses = @json([
			            \App\Models\FreshHelpdesk::TASK_WRONGLY_ASSIGNED,
			            \App\Models\FreshHelpdesk::TASK_RESOLVED,
			        ]);
			        var $taskStatusSelect = $('.js-task-status-select');
			        var $forwardSeniorButton = $('.js-task-forward-senior');
			        function syncForwardSeniorButton() {
			            var selectedStatus = $taskStatusSelect.val();
			            var canForward = forwardableTaskStatuses.indexOf(selectedStatus) !== -1;
			            $forwardSeniorButton
			                .prop('disabled', !canForward)
			                .toggleClass('disabled', !canForward)
			                .attr('title', canForward ? 'Forward to Senior Developer' : 'Only Resolved or Wrongly Assigned can be forwarded to Senior Developer');
			        }
				        if ($taskStatusSelect.length && $forwardSeniorButton.length) {
				            $taskStatusSelect.on('change', syncForwardSeniorButton);
				            syncForwardSeniorButton();
				        }

				        function initFreshTaskActionLoaders(selector) {
				            $(document).on('submit', selector, function (event) {
				                var form = this;
				                var $form = $(form);

				                if ($form.data('submitting')) {
				                    event.preventDefault();
				                    return false;
				                }

				                var submitter = event.originalEvent && event.originalEvent.submitter
				                    ? event.originalEvent.submitter
				                    : document.activeElement;
				                var $submitButton = $(submitter).is(':submit')
				                    ? $(submitter)
				                    : $form.find(':submit:enabled').first();

				                if ($submitButton.attr('name') && !$form.find('input[type="hidden"][data-fht-submit-copy="' + $submitButton.attr('name') + '"]').length) {
				                    $('<input>', {
				                        type: 'hidden',
				                        name: $submitButton.attr('name'),
				                        value: $submitButton.val(),
				                        'data-fht-submit-copy': $submitButton.attr('name')
				                    }).appendTo($form);
				                }

				                var loadingText = $submitButton.data('loadingText') || 'Sending...';
				                $form
				                    .data('submitting', true)
				                    .addClass('fht-action-form is-submitting');

				                $form.find(':submit').prop('disabled', true);
				                $submitButton
				                    .addClass('fht-submit-button is-submitting')
				                    .html('<span class="fht-submit-spinner me-1" aria-hidden="true"></span>' + loadingText);
				            });
				        }

				        initFreshTaskActionLoaders('.fht-side-panel form');
		
				        if (successMessage) {
			            if (typeof passing_alert_value === 'function') {
			                passing_alert_value('Confirmation', successMessage, 'confirmation_alert', 'confirmation_alertmodal', 'alert_body', 'confirmation_alert');
		            } else {
		                alert(successMessage);
		            }
		        }

			    });
		</script>
@endsection
