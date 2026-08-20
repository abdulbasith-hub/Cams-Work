@extends('index2')

@section('title', 'Helpdesk Ticket Details')

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

	    $ticketStatus = fn ($status) => \App\Models\FreshHelpdesk::ticketStatusLabel($status);
	    $ticketResultCount = isset($tickets)
	        ? (method_exists($tickets, 'total') ? $tickets->total() : $tickets->count())
	        : 0;
	    $ticketDisplayText = function ($value) {
        $text = trim((string) $value);

        if ($text === '') {
            return '-';
        }

        $humanize = function ($line) {
            $line = trim((string) $line);
            $replacements = [
                '/\bFORWARD_TO_STATE\b/i' => 'State Admin',
                '/\bPENDING_STATE_ADMIN_REVIEW\b/i' => 'Pending State Review',
                '/\bPENDING_STATE_REVIEW\b/i' => 'Pending State Review',
                '/\bPENDING_STATE_ADMIN\b/i' => 'Pending State Review',
                '/\bUPDATE_NIC_STATUS\b/i' => '',
                '/\bUPDATE_DEVELOPER_STATUS\b/i' => '',
                '/\bUPDATE_SENIOR_STATUS\b/i' => '',
                '/\bUPDATE_STATE_STATUS\b/i' => '',
                '/\bUPDATE_USER_STATUS\b/i' => '',
                '/\bPENDING_NIC_ADMIN\b/i' => 'Pending NIC Admin',
                '/\bPENDING_DEVELOPER\b/i' => 'Pending Developer',
                '/\bPENDING_SENIOR_DEV\b/i' => 'Pending Senior Developer',
                '/\bRETURNED_NIC_ADMIN\b/i' => 'Returned NIC Admin',
                '/\bRETURNED_SENIOR_DEV\b/i' => 'Returned Senior Developer',
                '/\bRETURNED_STATE_ADMIN\b/i' => 'Returned State Admin',
                '/\bRETURNED_USER\b/i' => 'Returned User',
                '/\bstateadmin\b/i' => 'State Admin',
                '/\bnicadmin\b/i' => 'NIC Admin',
                '/\bnic_admin\b/i' => 'NIC Admin',
                '/\bsenior_developer\b/i' => 'Senior Developer',
                '/\bdeveloper\b/i' => 'Developer',
            ];

            foreach ($replacements as $pattern => $replacement) {
                $line = preg_replace($pattern, $replacement, $line) ?: $line;
            }

            $line = preg_replace('/\s+/', ' ', $line) ?: $line;

            return trim($line);
        };

        if (str_contains(strtolower($text), 'ticket reopened')) {
            $remarks = '';

            if (preg_match('/\bRemarks:\s*(.+)$/is', $text, $matches)) {
                $remarks = $humanize($matches[1]);
            }

            return 'Ticket reopened and forwarded to State Admin.'.($remarks !== '' ? ' Remarks: '.$remarks : '');
        }

        if (preg_match('/\b((?:Forwarded|Returned|Assigned|Closed)[^.]*\.)\s*Remarks:\s*(.+)$/is', $text, $matches)) {
            $actionText = $humanize($matches[1]);
            $remarks = $humanize($matches[2]);

            return $actionText.($remarks !== '' ? ' Remarks: '.$remarks : '');
        }

        if (preg_match('/\bRemarks:\s*(.+)$/is', $text, $matches)) {
            $remarks = $humanize($matches[1]);
            $message = trim(preg_replace('/\bRemarks:\s*.+$/is', '', $text) ?: '');
            $message = preg_replace('/^\[[^\]]+\]\s*/', '', $message) ?: $message;
            $message = preg_replace('/\s+/', ' ', $message) ?: $message;
            $message = $humanize($message);

            if ($message !== '') {
                return rtrim($message, '.').'.'.($remarks !== '' ? ' Remarks: '.$remarks : '');
            }

            return $remarks !== '' ? $remarks : '-';
        }

        $text = preg_replace('/\[[^\]]+\]\s*/', '', $text) ?: $text;
        $text = preg_replace('/^[^.]*\s+->\s+[^.]*\.\s*/', '', $text) ?: $text;

        if (preg_match('/\bAssigned to:\s*([^\.]+)\.?/i', $text, $matches)) {
            $assignee = $humanize($matches[1]);

            return $assignee !== '' ? 'Assigned to '.$assignee.'.' : 'Assigned.';
        }

        if (preg_match('/\bforward(?:ed)?\s+to\s+([^\.]+)\.?/i', $text, $matches)) {
            $forwardedTo = $humanize($matches[1]);

            return $forwardedTo !== '' ? 'Forwarded to '.$forwardedTo.'.' : 'Forwarded.';
        }

        $text = $humanize($text);

        return $text !== '' ? $text : '-';
	    };
	    $ticketActionUrl = fn ($ticket, $action) => route('fresh-helpdesk.tickets.action', [$ticket->id, $action]);
	    $ticketStatusClass = function ($status) {
	        $statusKey = \App\Models\FreshHelpdesk::ticketStatusFilterKey($status) ?: \App\Models\FreshHelpdesk::normalizedTicketStatus($status);

	        return 'fh-status-'.\Illuminate\Support\Str::slug($statusKey !== '' ? $statusKey : 'empty');
	    };
	    $ticketPriorityClass = fn ($priority) => 'fh-priority-'.\Illuminate\Support\Str::slug(trim((string) ($priority ?: 'medium')));
	    $priorityOptions = collect($priorities ?? [])->filter()->values();
    $statusOptions = $statusOptions ?? $ticketStatusLabels;
    $filters = $filters ?? ['search' => '', 'priority' => '', 'status' => ''];
    $ticketDownloadParams = ['download' => 'tickets'] + array_filter($filters, fn ($value) => trim((string) $value) !== '');
@endphp

<style>
    .fh-details-page {
        max-width: 100%;
    }

    .fh-page-card {
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    .fh-page-header {
        background: #5b7df0;
        color: #fff;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fh-page-header h4 {
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }

    .fh-page-header p {
        color: rgba(255, 255, 255, 0.86);
        font-size: 12px;
        margin: 2px 0 0;
    }

    .fh-page-body {
        padding: 16px 18px 18px;
    }

    .fh-filter-card {
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        background: #f8fbff;
        padding: 14px;
        margin-bottom: 16px;
    }

    .fh-filter-card .form-control,
    .fh-filter-card .form-select {
        min-height: 42px;
        border-color: #d6e1ef;
        border-radius: 7px;
        font-size: 13px;
    }

	    .fh-filter-card .btn {
	        min-height: 42px;
	        font-weight: 700;
	        border-radius: 7px;
	        white-space: nowrap;
	    }

	    .fh-filter-count {
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

	    .fh-submit-spinner {
	        display: inline-block;
	        width: 0.95rem;
	        height: 0.95rem;
	        border: 2px solid rgba(255, 255, 255, 0.45);
	        border-top-color: #fff;
	        border-radius: 50%;
	        animation: fh-spin 0.75s linear infinite;
	        vertical-align: -2px;
	    }

	    .btn-outline-primary .fh-submit-spinner,
	    .btn-outline-danger .fh-submit-spinner,
	    .btn-light .fh-submit-spinner {
	        border-color: rgba(91, 125, 240, 0.25);
	        border-top-color: #5b7df0;
	    }

	    .fh-submit-button.is-submitting {
	        cursor: wait;
	        opacity: 0.84;
	    }

	    .fh-action-form.is-submitting textarea,
	    .fh-action-form.is-submitting select,
	    .fh-action-form.is-submitting input {
	        pointer-events: none;
	    }

	    @keyframes fh-spin {
	        to {
	            transform: rotate(360deg);
	        }
	    }

    .fh-register-summary {
        border: 1px solid #d8e2f0;
        border-bottom: 0;
        border-radius: 8px 8px 0 0;
        background: #fff;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fh-register-summary span {
        display: block;
        color: #4b5f80;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .fh-register-summary strong {
        color: #06122f;
        font-size: 20px;
        line-height: 1.2;
    }

    .fh-table th {
        white-space: normal;
        vertical-align: middle;
        text-align: left;
    }

    .fh-table {
        table-layout: fixed;
        min-width: 0;
    }

    .fh-table td {
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
    }

    .fh-ticket-no {
        color: #164c96;
        font-weight: 800;
        white-space: nowrap;
    }

	    .fh-reopened-badge {
	        display: inline-flex;
	        align-items: center;
	        border-radius: 999px;
        background: #fff3cd;
        color: #925700;
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
	        margin-left: 6px;
	        width: 22px;
	        vertical-align: middle;
	        white-space: nowrap;
	    }

	    .fh-action-watchlist {
	        align-items: flex-start;
	        background: #f8fbff;
	        border: 1px solid #d8e2f0;
	        border-radius: 7px;
	        display: flex;
	        gap: 8px;
	        margin-bottom: 10px;
	        padding: 10px 11px;
	    }

	    .fh-action-watchlist .form-check-input {
	        margin-left: 0;
	        margin-top: 3px;
	    }

	    .fh-action-watchlist .form-check-label {
	        color: #06122f;
	        font-size: 13px;
	        font-weight: 800;
	    }

    .fh-ticket-created {
        min-width: 0;
    }

	    .fh-ticket-created .fh-ticket-no {
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

	    .fh-ticket-number-line .fh-important-badge {
	        margin-left: 0;
	    }

	    .fh-ticket-created .fh-reopened-badge {
	        margin-left: 0;
	    }

    .fh-cell-muted {
        color: #64748b;
        font-size: 12px;
    }

	    .fh-status-badge,
	    .fh-priority-badge,
	    .fh-dev-status-badge {
	        display: inline-flex;
	        align-items: center;
	        border-radius: 999px;
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 800;
        line-height: 1.2;
    }

	    .fh-status-badge {
	        background: #eaf2ff;
	        color: #2454a6;
        max-width: 100%;
        min-width: 0;
        white-space: normal;
	        word-break: break-word;
	    }

	    .fh-status-in-progress,
	    .fh-status-pending-nic-admin,
	    .fh-status-pending-senior-dev,
	    .fh-status-pending-developer,
	    .fh-status-pending-state-admin,
	    .fh-status-pending-state-review,
	    .fh-status-pending-state-admin-review {
	        background: #eaf2ff;
	        color: #1d4ed8;
	    }

	    .fh-status-need-clarification,
	    .fh-status-returned-nic-admin,
	    .fh-status-returned-senior-dev,
	    .fh-status-returned-state-admin,
	    .fh-status-returned-user {
	        background: #fff7df;
	        color: #9a5b00;
	    }

	    .fh-status-resolved,
	    .fh-status-closed {
	        background: #eaf7ef;
	        color: #15803d;
	    }

	    .fh-status-empty {
	        background: #f1f5f9;
	        color: #64748b;
	    }

	    td .fh-status-badge {
	        display: inline-block;
	    }

	    .fh-dev-status-in-progress {
	        background: #eaf2ff;
	        color: #2454a6;
	    }

	    .fh-dev-status-need-clarification {
	        background: #fff7df;
	        color: #8a5b05;
	    }

	    .fh-dev-status-completed {
	        background: #eef8f1;
	        color: #23784a;
	    }

	    .fh-dev-status-empty {
	        background: #f1f5f9;
	        color: #64748b;
	    }

	    .fh-priority-low {
	        background: #eef8f1;
	        color: #15803d;
	    }

	    .fh-priority-medium {
	        background: #fff7df;
	        color: #9a5b00;
	    }

	    .fh-priority-high {
	        background: #fff1e8;
	        color: #c2410c;
	    }

	    .fh-priority-critical,
	    .fh-priority-urgent {
	        background: #fff1f2;
	        color: #b4233a;
	    }

    .fh-grouped-meta {
        min-width: 0;
        max-width: 100%;
        white-space: normal;
    }

    .fh-grouped-meta .fh-meta-line {
        display: flex;
        align-items: flex-start;
        gap: 7px;
    }

    .fh-grouped-meta .fh-meta-line + .fh-meta-line {
        margin-top: 6px;
    }

    .fh-meta-label {
        flex: 0 0 64px;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .fh-meta-value {
        color: #1f2f46;
        font-size: 12px;
        font-weight: 700;
    }

    .fh-subject {
        min-width: 0;
        max-width: 100%;
        white-space: normal;
        font-weight: 700;
        color: #1f2f46;
    }

    .fh-description {
        min-width: 260px;
        max-width: 420px;
        white-space: normal;
        color: #42536c;
    }

    .fh-action-btn {
        white-space: nowrap;
    }

    .fh-sheet-hero {
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        background: #fff;
        padding: 16px 18px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fh-sheet-hero span {
        color: #4b5f80;
        display: block;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .fh-sheet-hero h3 {
        color: #06122f;
        font-size: 24px;
        font-weight: 850;
        margin: 2px 0 0;
    }

    .fh-sheet-card {
        border: 1px solid #d8e2f0;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
    }

    .fh-sheet-card-accent {
        border-top: 4px solid #2f6ee8;
    }

    .fh-sheet-card-header {
        color: #06122f;
        font-size: 18px;
        font-weight: 850;
        padding: 13px 16px 10px;
        border-bottom: 1px solid #d8e2f0;
    }

    .fh-sheet-card-body {
        padding: 14px 16px;
    }

    .fh-ticket-summary {
        border: 1px solid #d8e2f0;
        border-left: 4px solid #2f6ee8;
        border-radius: 8px;
        background: #f8fbff;
        padding: 14px 16px;
        margin-bottom: 12px;
    }

    .fh-ticket-summary small {
        color: #4b5f80;
        display: block;
        font-size: 11px;
        font-weight: 850;
        text-transform: uppercase;
    }

    .fh-ticket-summary h4 {
        color: #06122f;
        font-size: 20px;
        font-weight: 850;
        margin: 5px 0 0;
        word-break: break-word;
    }

    .fh-ticket-summary .fh-status-badge {
        margin-left: auto;
    }

    .fh-info-grid {
        display: grid;
        gap: 0 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-left: 0;
        margin-right: 0;
    }

    .fh-info-grid > [class*="col-"] {
        max-width: none;
        padding-left: 0;
        padding-right: 0;
        width: auto;
    }

    .fh-info-box,
    .fh-note-box {
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

    .fh-note-box {
        min-height: 74px;
    }

    .fh-info-box p,
    .fh-note-box p {
        grid-column: 2 / -1;
    }

    .fh-info-box small,
    .fh-note-box small {
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

    .fh-info-icon {
        color: #2f6ee8;
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 12px;
        line-height: 1.4;
    }

    .fh-info-box strong,
    .fh-note-box strong {
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

    .fh-note-box strong {
        margin-top: 2px;
    }

    .fh-note-box p {
        color: #1f2f46;
        font-size: 13.5px;
        font-weight: 500;
        line-height: 1.45;
        margin: 0;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .fh-info-box .fh-cell-muted {
        display: block;
        grid-column: 2;
        margin-left: 0;
        min-width: 0;
        overflow-wrap: anywhere;
        white-space: normal;
    }

    .fh-note-box > div,
    .fh-note-box > p {
        min-width: 0;
    }

    .fh-note-box > div:not(.modal):not(.dropdown-menu) {
        grid-column: 2 / -1;
        margin-top: 0 !important;
    }

    @media (max-width: 575.98px) {
        .fh-info-grid {
            grid-template-columns: 1fr;
        }

        .fh-info-box,
        .fh-note-box {
            grid-template-columns: 1fr;
        }

        .fh-info-box small,
        .fh-note-box small,
        .fh-info-box strong,
        .fh-note-box strong,
        .fh-info-box .fh-cell-muted,
        .fh-note-box p,
        .fh-note-box > div:not(.modal):not(.dropdown-menu) {
            grid-column: 1;
            min-width: 0;
        }
    }

    .fh-flow-track {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0;
        margin: 6px 0 8px;
        padding: 14px 0 8px;
        position: relative;
    }

    .fh-flow-track::before {
        display: none;
    }

    .fh-flow-step {
        position: relative;
        z-index: 1;
        min-width: 0;
        padding: 0 8px;
        text-align: center;
    }

    .fh-flow-step:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 9px;
        left: 50%;
        right: -50%;
        height: 4px;
        border-radius: 999px;
        background: #d8e2f0;
        z-index: -1;
    }

    .fh-flow-step.is-complete:not(:last-child)::after {
        background: #16a34a;
    }

    .fh-flow-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #d8e2f0;
        display: grid;
        place-items: center;
        margin: 0 auto;
        color: #2454a6;
        font-size: 10px;
        font-weight: 850;
        box-shadow: 0 0 0 4px #fff;
    }

    .fh-flow-step.is-complete .fh-flow-dot {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }

    .fh-flow-step.is-active .fh-flow-dot {
        background: #2f6ee8;
        border-color: #2f6ee8;
        color: #fff;
    }

    .fh-flow-step.is-complete .fh-flow-dot::before {
        content: "✓";
        line-height: 1;
    }

    .fh-flow-step.is-active .fh-flow-dot::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        display: block;
    }

    .fh-flow-step.is-complete .fh-flow-dot::before {
        content: "";
        width: 7px;
        height: 4px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
        transform: translateY(-1px) rotate(-45deg);
    }

    .fh-flow-step.is-active .fh-flow-dot::before {
        display: block;
        margin: 0;
    }

    .fh-flow-step .fh-flow-dot {
        position: relative;
    }

    .fh-flow-step.is-complete .fh-flow-dot::before {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 5px;
        height: 9px;
        border-left: 0;
        border-right: 2px solid #fff;
        border-bottom: 2px solid #fff;
        transform: translate(-50%, -58%) rotate(45deg);
        transform-origin: center;
    }

    .fh-flow-step.is-active .fh-flow-dot::before {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 8px;
        height: 8px;
        transform: translate(-50%, -50%);
    }

    .fh-flow-step.is-complete strong {
        color: #126c35;
    }

    .fh-flow-step.is-active strong {
        color: #1d4ed8;
    }

    .fh-flow-step.is-origin .fh-flow-dot {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }

    .fh-flow-step.is-origin.is-active .fh-flow-dot {
        background: #2f6ee8;
        border-color: #2f6ee8;
    }

    .fh-flow-step.is-origin.is-complete:not(:last-child)::after {
        background: #16a34a;
    }

    .fh-flow-step strong {
        display: block;
        color: #06122f;
        font-size: 12px;
        font-weight: 850;
        margin-top: 8px;
        line-height: 1.25;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .fh-flow-step span {
        color: #64748b;
        display: block;
        font-size: 11px;
        margin-top: 2px;
        line-height: 1.25;
        word-break: break-word;
    }

    .fh-flow-time {
        color: #64748b;
        display: block;
        font-size: 11px;
        line-height: 1.25;
        margin-top: 3px;
    }

    .fh-workflow-title {
        color: #06122f;
        font-size: 18px;
        font-weight: 850;
        margin: 0;
    }

    .fh-workflow-action {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .fh-comment-form {
        border-top: 1px solid #d8e2f0;
        margin-top: 12px;
        padding-top: 10px;
    }

    .fh-comment-form.is-hidden {
        display: none;
    }

    .fh-comment-form textarea,
    .fh-comment-form select {
        border-color: #d6e1ef;
        border-radius: 7px;
    }

    .fh-timeline-list {
        border-top: 1px solid #d8e2f0;
        margin-top: 12px;
        padding-top: 12px;
    }

    .fh-timeline-list .fh-timeline-item {
        align-items: flex-start;
        display: flex;
        gap: 10px;
        position: relative;
        width: 100%;
    }

    .fh-timeline-list .fh-timeline-item + .fh-timeline-item {
        margin-top: 10px;
    }

    .fh-timeline-list .fh-timeline-item:not(:last-child)::before {
        content: "";
        position: absolute;
        left: 13px;
        top: 28px;
        bottom: -12px;
        width: 2px;
        background: #cfe0f5;
    }

    .fh-timeline-list .fh-timeline-number {
        flex: 0 0 26px;
        align-items: center;
        background: #2f6ee8;
        border-radius: 50%;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 850;
        height: 26px;
        justify-content: center;
        position: relative;
        width: 26px;
        z-index: 1;
    }

    .fh-timeline-list .fh-timeline-content {
        flex: 1 1 auto;
        min-width: 0;
        background: #f8fbff;
        border: 1px solid #d8e2f0;
        border-left: 4px solid #7c3aed;
        border-radius: 7px;
        min-height: 66px;
        padding: 12px 15px;
        position: relative;
    }

    .fh-timeline-list .fh-timeline-content small {
        color: #164c96;
        display: block;
        font-size: 11px;
        font-weight: 850;
        text-transform: uppercase;
        padding-right: 170px;
    }

    .fh-timeline-list .fh-timeline-content p {
        color: #06122f;
        font-weight: 700;
        margin: 8px 0 0;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .fh-timeline-list .fh-timeline-date {
        background: #eaf2ff;
        border-radius: 7px;
        color: #155eef;
        font-size: 11px;
        font-weight: 850;
        padding: 5px 9px;
        position: absolute;
        right: 15px;
        top: 12px;
        white-space: nowrap;
    }

    .fh-side-panel {
        border: 1px solid #d8e2f0;
        border-top: 4px solid #2f6ee8;
        border-radius: 8px;
        background: #fff;
        padding: 14px;
        margin-bottom: 12px;
    }

    .fh-side-panel h5 {
        color: #06122f;
        font-size: 15px;
        font-weight: 850;
        margin: 0 0 12px;
    }

    .fh-side-panel textarea,
    .fh-side-panel select {
        border-color: #d6e1ef;
        border-radius: 7px;
    }

    .fh-table th:nth-child(1),
    .fh-table td:nth-child(1),
    .fh-table th:nth-child(4),
    .fh-table td:nth-child(4),
    .fh-table th:nth-child(7),
    .fh-table td:nth-child(7),
    .fh-table th:nth-child(10),
    .fh-table td:nth-child(10),
    .fh-table th:nth-child(11),
    .fh-table td:nth-child(11) {
        white-space: nowrap;
    }

    .dataTables_wrapper {
        width: 100%;
    }

	    .fh-dt-toolbar {
	        display: flex;
	        align-items: center;
	        justify-content: space-between;
	        gap: 10px;
	        margin-bottom: 10px;
	        flex-wrap: wrap;
	    }

	    .fh-dt-left {
	        align-items: center;
	        display: flex;
	        flex-wrap: wrap;
	        gap: 8px;
	    }

	    .fh-result-count {
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

	    .fh-dt-footer {
	        display: flex;
	        align-items: center;
	        justify-content: space-between;
	        gap: 12px;
	        flex-wrap: wrap;
	        padding-top: 12px;
	    }

	    .fh-details-page .dataTables_info,
	    .fh-details-page .dataTables_paginate {
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

	    .fh-dt-footer .dataTables_paginate {
	        margin-left: auto;
	    }

    .fh-dt-footer .dataTables_paginate .pagination {
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .fh-dt-footer .dataTables_paginate .page-item {
        margin: 0;
    }

    .fh-dt-footer .dataTables_paginate .page-link {
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d7e1ef !important;
        border-radius: 6px !important;
        background: #fff !important;
        color: #174ea6 !important;
        box-shadow: none !important;
        font-size: 13px;
        font-weight: 700;
        padding: 6px 10px;
    }

    .fh-dt-footer .dataTables_paginate .page-item.previous .page-link,
    .fh-dt-footer .dataTables_paginate .page-item.next .page-link {
        min-width: 76px;
    }

    .fh-dt-footer .dataTables_paginate .page-item.active .page-link {
        background: #2f6ee8 !important;
        border-color: #2f6ee8 !important;
        color: #fff !important;
    }

    .fh-dt-footer .dataTables_paginate .page-item.disabled .page-link {
        background: #f3f6fb !important;
        border-color: #d7e1ef !important;
        color: #95a3b8 !important;
        cursor: not-allowed;
    }

    .fh-dt-footer .dataTables_paginate .page-item .page-link:focus,
    .fh-dt-footer .dataTables_paginate .page-item .page-link:hover {
        background: #eef4ff !important;
        border-color: #9fbcfb !important;
        color: #174ea6 !important;
        box-shadow: none !important;
    }

    .fh-dt-footer .dataTables_paginate .page-item.active .page-link:hover {
        background: #2f6ee8 !important;
        color: #fff !important;
    }

    .dataTables_info {
        color: #64748b;
        font-size: 13px;
        padding-top: 0 !important;
    }

    .dataTables_length {
        color: #1f2f46;
        font-size: 13px;
        font-weight: 700;
    }

    .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .dataTables_length select {
        min-height: 36px;
        min-width: 74px;
        border: 1px solid #d6e1ef;
        border-radius: 7px;
        color: #071330;
        font-weight: 700;
        box-shadow: none;
    }

    .dataTables_filter {
        display: none !important;
    }

    .dt-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dt-buttons .dt-button,
    .dt-buttons .btn {
        border: 1px solid #2f6ee8 !important;
        border-radius: 7px !important;
        background: #2f6ee8 !important;
        color: #fff !important;
        padding: 7px 12px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        box-shadow: 0 8px 16px rgba(47, 110, 232, 0.18) !important;
    }

    .dt-buttons .dt-button:hover,
    .dt-buttons .btn:hover {
        background: #245ad0 !important;
        border-color: #245ad0 !important;
        color: #fff !important;
    }

    .dataTables_scrollHead {
        overflow: hidden !important;
        background: #e5e7eb;
        border: 1px solid #d8e2f0;
        border-bottom: 0;
    }

    .dataTables_scrollHeadInner,
    .dataTables_scrollHeadInner table {
        min-width: 100% !important;
    }

    .fh-table thead,
    .fh-table thead tr,
    #freshHelpdeskTicketTable thead,
    #freshHelpdeskTicketTable thead tr,
    .dataTables_scrollHead thead,
    .dataTables_scrollHead thead tr,
    .dataTables_scrollHeadInner table thead,
    .dataTables_scrollHeadInner table thead tr {
        background: #d9dde4 !important;
        background-color: #d9dde4 !important;
    }

    .fh-table thead th,
    #freshHelpdeskTicketTable thead th,
    table#freshHelpdeskTicketTable.dataTable thead th,
    .dataTables_scrollHead thead th,
    .dataTables_scrollHeadInner table thead th {
        background: #d9dde4 !important;
        background-color: #6b6b6b !important;
        background-image: none !important;
        color: #f6f6f7 !important;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        border-color: #c7ced8 !important;
        box-shadow: inset 0 -1px 0 #c7ced8;
    }

    #freshHelpdeskTicketTable.table-bordered thead th {
        border-color: #c7ced8 !important;
    }

    .dataTables_scrollBody {
        border-top: 0 !important;
    }

    .table-responsive {
        overflow-x: visible;
    }

    #freshHelpdeskTicketTable th:nth-child(1),
    #freshHelpdeskTicketTable td:nth-child(1) {
        width: 5% !important;
        text-align: center;
    }

    #freshHelpdeskTicketTable th:nth-child(2),
    #freshHelpdeskTicketTable td:nth-child(2) {
        width: 13% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(3),
    #freshHelpdeskTicketTable td:nth-child(3) {
        width: 16% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(4),
    #freshHelpdeskTicketTable td:nth-child(4) {
        width: 8% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(5),
    #freshHelpdeskTicketTable td:nth-child(5) {
        width: 8% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(6),
    #freshHelpdeskTicketTable td:nth-child(6) {
        width: 18% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(7),
    #freshHelpdeskTicketTable td:nth-child(7) {
        width: 15% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(8),
    #freshHelpdeskTicketTable td:nth-child(8) {
        width: 9% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(9),
    #freshHelpdeskTicketTable td:nth-child(9) {
        width: 10% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(10),
    #freshHelpdeskTicketTable td:nth-child(10) {
        width: 10% !important;
    }

    #freshHelpdeskTicketTable th:nth-child(11),
    #freshHelpdeskTicketTable td:nth-child(11) {
        width: 7% !important;
        text-align: center;
    }

	    #freshHelpdeskTicketTable th:nth-child(12),
	    #freshHelpdeskTicketTable td:nth-child(12) {
	        width: 9% !important;
	        text-align: center;
	    }

	    #freshHelpdeskTicketTable .fh-col-dev-status {
	        text-align: center;
	        width: 9% !important;
	    }

	    #freshHelpdeskTicketTable .fh-col-updated {
	        text-align: left !important;
	        width: 10% !important;
	    }

	    #freshHelpdeskTicketTable .fh-col-action {
	        text-align: center;
	        width: 7% !important;
	    }

    table.dataTable {
        width: 100% !important;
    }

    table.dataTable td,
    table.dataTable th {
        word-wrap: break-word;
    }

    @media (max-width: 767.98px) {
        .fh-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .fh-page-header .btn {
            width: 100%;
        }

        .fh-filter-card .btn,
        .fh-filter-card .form-control,
        .fh-filter-card .form-select {
            width: 100%;
        }

        .fh-dt-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .dataTables_filter,
        .dataTables_filter label,
        .dataTables_filter input {
            width: 100%;
        }
    }

</style>

@if (!$selectedTicket)
<div class="fh-details-page">
<div class="fh-page-card">
    <div class="fh-page-header">
        <div>
            <h4>Ticket Details</h4>
            <p>{{ $roleLabel }} dashboard</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- <div>
                <span class="badge bg-light text-primary">Total Tickets: {{ $tickets->count() }}</span>
            </div> --}}
            @if (in_array($role, [\App\Models\FreshHelpdesk::ROLE_USER, \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN, \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN], true))
                {{-- <a href="{{ route('fresh-helpdesk.create') }}" class="btn btn-light">
                    <i class="ti ti-plus me-1"></i> Create Ticket
                </a> --}}
            @endif
	            <a href="{{ route('fresh-helpdesk.ticket-details', ['clear_filters' => 1]) }}" class="btn btn-outline-light">
	                <i class="ti ti-refresh me-1"></i> Refresh
	            </a>
        </div>
    </div>
    <div class="fh-page-body">

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

	        @php
	            $fhCanViewDevStatus = \App\Models\FreshHelpdesk::canViewDeveloperStatus($role);
	            $fhExtraFilterSlots = 0;
	            if ($role === \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN) {
	                $fhExtraFilterSlots = 2;
	            } elseif ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN) {
	                $fhExtraFilterSlots = $fhCanViewDevStatus ? 4 : 2;
	            } elseif ($fhCanViewDevStatus) {
	                $fhExtraFilterSlots = 2;
	            }
	            $fhSearchColWidth = max(2, 12 - 6 - $fhExtraFilterSlots);
	        @endphp
	        <form class="fh-filter-card" id="freshTicketFilterForm" method="POST" action="{{ route('fresh-helpdesk.ticket-details.filter') }}">
	            @csrf
	            <div class="row g-2 align-items-center">
	                <div class="col-lg-{{ $fhSearchColWidth }} col-md-12">
	                    <input type="text" name="search" id="freshTicketSearch" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search ticket, subject, module, user">
	                </div>
	                <div class="col-lg-2 col-md-4">
                    <select name="priority" id="freshTicketPriority" class="form-select">
                        <option value="">All priorities</option>
                        @foreach ($priorityOptions as $priority)
                            <option value="{{ $priority }}" @selected(strtolower((string) ($filters['priority'] ?? '')) === strtolower((string) $priority))>{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="status" id="freshTicketStatus" class="form-select">
                        <option value="">All statuses</option>
                        @foreach ($statusOptions as $status => $label)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $label }}</option>
	                        @endforeach
	                    </select>
		                </div>
		                @if ($role === \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN)
		                    <div class="col-lg-2 col-md-4">
			                        <select name="created_user" id="freshTicketCreatedUser" class="form-select">
			                            <option value="">All created users</option>
			                            @foreach (($stateCreatorOptions ?? collect()) as $creator)
			                                <option value="{{ $creator->deptuserid }}" @selected((string) ($filters['created_user'] ?? '') === (string) $creator->deptuserid)>
			                                    {{ $creator->username }}
			                                </option>
			                            @endforeach
			                        </select>
		                    </div>
		                @elseif ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
		                    <div class="col-lg-2 col-md-4">
			                        <select name="developer_userid" id="freshTicketDeveloper" class="form-select">
			                            <option value="">All developers</option>
			                            @foreach (($developerFilterOptions ?? collect()) as $developer)
			                                <option value="{{ $developer->devuserid }}" @selected((string) ($filters['developer_userid'] ?? '') === (string) $developer->devuserid)>
			                                    {{ $developer->devename }}
			                                </option>
			                            @endforeach
			                        </select>
		                    </div>
		                @endif
		                @if ($fhCanViewDevStatus)
		                    <div class="col-lg-2 col-md-4">
		                        <select name="developer_status" id="freshTicketDevStatus" class="form-select">
		                            <option value="">All dev statuses</option>
		                            @foreach (\App\Models\FreshHelpdesk::developerStatusLabels() as $devStatusValue => $devStatusLabel)
		                                <option value="{{ $devStatusValue }}" @selected(($filters['developer_status'] ?? '') === $devStatusValue)>{{ $devStatusLabel }}</option>
		                            @endforeach
		                        </select>
		                    </div>
		                @endif
		                <div class="col-lg-1 col-md-4">
		                    <select name="ticket_scope" id="freshTicketScope" class="form-select" title="Assigned / Forwarded">
			                        <option value="">All</option>
			                        <option value="on_me" @selected(($filters['ticket_scope'] ?? '') === 'on_me')>Assigned / Forwarded</option>
			                        @if ($role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
			                            <option value="developer_side" @selected(($filters['ticket_scope'] ?? '') === 'developer_side')>Developer Side</option>
			                        @endif
			                        @if ($role === \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN)
			                            <option value="returned_from_nic" @selected(($filters['ticket_scope'] ?? '') === 'returned_from_nic')>Returned From NIC</option>
			                        @endif
			                        @if (in_array($role, [\App\Models\FreshHelpdesk::ROLE_NIC_ADMIN, \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN], true))
			                            <option value="important" @selected(($filters['ticket_scope'] ?? '') === 'important')>Important</option>
			                        @endif
			                    </select>
		                </div>
			                <div class="col-lg-1 col-md-4">
			                    <button type="submit" id="freshTicketFilterBtn" class="btn btn-primary w-100 px-2">
			                        Filter <span class="fh-filter-count">{{ number_format($ticketResultCount) }}</span>
			                    </button>
			                </div>
	            </div>
	        </form>

	        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
	            <a href="{{ route('fresh-helpdesk.ticket-details', $ticketDownloadParams) }}" id="freshTicketDownloadBtn" class="btn btn-primary">
	                <i class="ti ti-download me-1"></i> Download
	            </a>
	        </div>

        <div class="datatables" id="freshTicketTableWrap">
        <div class="table-responsive">
            <table id="freshHelpdeskTicketTable" class="table w-100 table-striped table-bordered display align-middle fh-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Ticket / Created By</th>
                        <th>Department-Category</th>
                        <th>Priority</th>
                        <th>Type</th>
                        <th>Subject</th>
	                        <th>Status</th>
	                        <th class="text-wrap">Currently With</th>
	                        <th class="text-wrap">Created On</th>
	                        @if ($fhCanViewDevStatus)
	                            <th class="text-wrap fh-col-dev-status">Dev Status</th>
	                        @endif
	                        <th class="text-wrap fh-col-updated">Updated On</th>
	                        <th class="text-wrap fh-col-action">Action</th>
	                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr>
	                            <td class="text-center">{{ method_exists($tickets, 'firstItem') ? ($tickets->firstItem() + $loop->index) : $loop->iteration }}</td>
                            <td>
                                <div class="fh-ticket-created">
	                                    <div class="fh-ticket-no">
	                                        <span class="fh-ticket-number-line">
	                                            {{ $ticket->ticket_number ?: '#'.$ticket->id }}
	                                            @if (strtoupper((string) ($ticket->importflag ?? '')) === 'Y')
	                                                <span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>
	                                            @endif
	                                        </span>
	                                        @if (!empty($ticket->is_reopened))
	                                            <span class="fh-reopened-badge">Reopened</span>
	                                        @endif
                                    </div>
                                    <div class="fh-cell-muted">{{ $ticket->user_name ?: '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="fh-grouped-meta">
                                    <div class="fh-meta-line">
                                        <span class="fh-meta-value">{{ $ticket->department_name ?: '-' }}</span> - <span class="fh-meta-value">{{ $ticket->category ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
	                                <span class="fh-priority-badge {{ $ticketPriorityClass($ticket->priority) }}">
	                                    {{ $ticket->priority ?: '-' }}
	                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::headline((string) $ticket->request_type) }}</td>
                            <td class="text-wrap"><div class="fh-subject">{{ $ticket->subject ?: '-' }}</div></td>
		                            <td><span class="fh-status-badge {{ $ticketStatusClass($ticket->status) }}">{{ $ticketStatus($ticket->status) }}</span></td>
		                            <td class="text-wrap">{{ \App\Models\FreshHelpdesk::dashboardCurrentWith($ticket, $role) }}</td>
		                            <td class="text-wrap" data-order="{{ $dateOrderValue($ticket->created_at) }}"><span class="fh-cell-muted">{{ $fmt($ticket->created_at) }}</span></td>
	                            @if ($fhCanViewDevStatus)
	                                @php
	                                    $devStatusValue = (string) ($ticket->developer_status ?? '');
	                                    $devStatusClass = $devStatusValue !== '' ? \Illuminate\Support\Str::slug($devStatusValue) : 'empty';
	                                @endphp
	                                <td class="text-wrap fh-col-dev-status">
	                                    <span class="fh-dev-status-badge fh-dev-status-{{ $devStatusClass }}">
	                                        {{ \App\Models\FreshHelpdesk::developerStatusLabel($ticket->developer_status ?? null) }}
	                                    </span>
	                                </td>
	                            @endif
	                            <td class="text-wrap fh-col-updated"><span class="fh-cell-muted">{{ $fmt($ticket->updated_at) }}</span></td>
	                            <td class="text-wrap fh-col-action" >
	                                <a href="{{ route('fresh-helpdesk.ticket-details', ['ticket' => \App\Models\FreshHelpdesk::ticketUrlToken($ticket->id)]) }}" class="btn btn-sm btn-outline-primary fh-action-btn">
	                                    <i class="ti ti-eye me-1"></i> View
	                                </a>
	                            </td>
	                        </tr>
                    @endforeach
                </tbody>
            </table>
	        </div>
	        <div id="freshTicketPaginationWrap">
	        @if (method_exists($tickets, 'hasPages') && $tickets->hasPages())
	            @php
	                $fhEdge = 3;
	                $fhWindow = 1;
	                $fhLast = $tickets->lastPage();
	                $fhCurrent = $tickets->currentPage();
	                $fhPageItems = [];
	                $fhLastShown = 0;

	                for ($fhPage = 1; $fhPage <= $fhLast; $fhPage++) {
	                    if ($fhPage <= $fhEdge || $fhPage > $fhLast - $fhEdge || abs($fhPage - $fhCurrent) <= $fhWindow) {
	                        if ($fhLastShown && $fhPage - $fhLastShown > 1) {
	                            $fhPageItems[] = '...';
	                        }
	                        $fhPageItems[] = $fhPage;
	                        $fhLastShown = $fhPage;
	                    }
	                }
	            @endphp
	            <div class="fh-laravel-pagination">
	                <a class="fh-page-link {{ $tickets->onFirstPage() ? 'is-disabled' : '' }}" href="{{ $tickets->previousPageUrl() ?: '#' }}">Previous</a>
	                @foreach ($fhPageItems as $page)
	                    @if ($page === '...')
	                        <span class="fh-page-ellipsis">&hellip;</span>
	                    @elseif ($page === $fhCurrent)
	                        <span class="fh-page-current">{{ $page }}</span>
	                    @else
	                        <a class="fh-page-link" href="{{ $tickets->url($page) }}">{{ $page }}</a>
	                    @endif
	                @endforeach
	                <a class="fh-page-link {{ $tickets->hasMorePages() ? '' : 'is-disabled' }}" href="{{ $tickets->nextPageUrl() ?: '#' }}">Next</a>
	            </div>
	        @endif
	        </div>
	        </div>
	    </div>
	</div>
@endif

@if ($selectedTicket)
    @php
	        $attachments = json_decode((string) ($selectedTicket->attachments ?? '[]'), true);
	        $attachments = is_array($attachments) ? $attachments : [];
	        $latestComment = $ticketComments->last();
	        $ticketStatusValue = (string) $selectedTicket->status;
	        $reopenComment = $ticketComments->reverse()->first(function ($comment) {
	            return str_contains(strtolower((string) ($comment->comment ?? '')), 'ticket reopened');
	        });
		        $isTicketReopened = !empty($selectedTicket->is_reopened) || (bool) $reopenComment;
		        $reopenCommentTime = null;
	        if ($reopenComment && !empty($reopenComment->created_at)) {
	            try {
	                $reopenCommentTime = \Carbon\Carbon::parse($reopenComment->created_at)->timestamp;
	            } catch (\Throwable $e) {
	                $reopenCommentTime = null;
	            }
	        }
	        $flowComments = $reopenComment
	            ? $ticketComments->filter(function ($comment) use ($reopenCommentTime) {
	                if ($reopenCommentTime === null || empty($comment->created_at)) {
	                    return false;
	                }

	                try {
	                    return \Carbon\Carbon::parse($comment->created_at)->timestamp >= $reopenCommentTime;
	                } catch (\Throwable $e) {
	                    return false;
	                }
	            })->values()
	            : $ticketComments;
	        $creationEventComment = $reopenComment ?: $ticketComments->first();
	        if ($creationEventComment) {
	            $flowComments = $flowComments->reject(fn ($comment) => $comment->id === $creationEventComment->id)->values();
	        }
	        $findRoleComment = function (array $roleNeedles, bool $latest = false) use ($flowComments) {
	            $source = $latest ? $flowComments->reverse() : $flowComments;

	            return $source->first(function ($comment) use ($roleNeedles) {
	                $roleText = strtolower((string) ($comment->user_role ?? ''));

	                foreach ($roleNeedles as $needle) {
	                    if ($needle !== '' && str_contains($roleText, strtolower($needle))) {
                        return true;
                    }
                }
	                return false;
	            });
	        };
	        $findDeveloperComment = function (bool $latest = false) use ($flowComments) {
	            $source = $latest ? $flowComments->reverse() : $flowComments;

	            return $source->first(function ($comment) {
	                $roleText = \App\Models\FreshHelpdesk::normalizedTicketStatus((string) ($comment->user_role ?? ''));

	                return str_contains($roleText, 'developer')
	                    && !str_contains($roleText, 'senior')
	                    && !str_contains($roleText, 'lead')
	                    && !str_contains($roleText, 'tech team');
	            });
	        };
	        $findTextComment = function (array $needles, bool $latest = false, array $roleNeedles = []) use ($flowComments) {
	            $source = $latest ? $flowComments->reverse() : $flowComments;

	            return $source->first(function ($comment) use ($needles, $roleNeedles) {
	                $commentText = strtolower((string) ($comment->comment ?? ''));
	                $roleText = strtolower((string) ($comment->user_role ?? '').' '.(string) ($comment->user_name ?? ''));

	                if (!empty($roleNeedles)) {
	                    $roleMatched = false;
	                    foreach ($roleNeedles as $roleNeedle) {
	                        if ($roleNeedle !== '' && str_contains($roleText, strtolower($roleNeedle))) {
	                            $roleMatched = true;
	                            break;
	                        }
	                    }

	                    if (!$roleMatched) {
	                        return false;
	                    }
	                }

	                foreach ($needles as $needle) {
	                    if ($needle !== '' && str_contains($commentText, strtolower($needle))) {
	                        return true;
	                    }
	                }

	                return false;
	            });
	        };
	        $commentTimestamp = function ($comment) {
	            if (!$comment || empty($comment->created_at)) {
	                return null;
	            }

	            try {
	                return \Carbon\Carbon::parse($comment->created_at)->timestamp;
	            } catch (\Throwable $e) {
	                return null;
	            }
	        };
	        $createdComment = $reopenComment ?: $ticketComments->first();
	        $flowStartedAt = $reopenComment->created_at ?? $selectedTicket->created_at;
	        $flowStartedMeta = $reopenComment ? 'Ticket reopened' : 'Ticket created';
	        $flowStartedAtTimestamp = null;
	        if (!empty($flowStartedAt)) {
	            try {
	                $flowStartedAtTimestamp = \Carbon\Carbon::parse($flowStartedAt)->timestamp;
	            } catch (\Throwable $e) {
	                $flowStartedAtTimestamp = null;
	            }
	        }
		        $ticketAssignmentHistory = \Illuminate\Support\Facades\DB::table('audit.helpdesk_ticket_assignments')
		            ->where('ticket_id', $selectedTicket->id)
		            ->where(function ($query) {
		                $query->whereNull('status')->orWhere('status', '!=', 'watchlist');
		            })
		            ->orderBy('assigned_at')
		            ->orderBy('id')
		            ->get();
	        $assignmentsInCurrentFlow = $flowStartedAtTimestamp === null
	            ? $ticketAssignmentHistory
	            : $ticketAssignmentHistory->filter(function ($assignment) use ($flowStartedAtTimestamp) {
	                if (empty($assignment->assigned_at)) {
	                    return false;
	                }
	                try {
	                    return \Carbon\Carbon::parse($assignment->assigned_at)->timestamp >= $flowStartedAtTimestamp;
	                } catch (\Throwable $e) {
	                    return false;
	                }
	            })->values();
	        $seniorAssignedAt = optional($assignmentsInCurrentFlow->firstWhere('status', 'senior_developer'))->assigned_at;
	        $developerAssignedAt = optional($assignmentsInCurrentFlow->where('status', 'developer')->last())->assigned_at;
	        $stateComment = $findRoleComment(['state']);
	        $nicComment = $findRoleComment(['nic']);
	        $seniorComment = $findRoleComment(['senior']);
	        $developerComment = $findDeveloperComment();
	        $stateReturnComment = $findRoleComment(['state'], true);
	        $nicReturnComment = $findRoleComment(['nic'], true);
	        $seniorReturnComment = $findRoleComment(['senior'], true);
		        $developerForwardComment = $findTextComment(['forwarded to developer', 'assigned to developer', 'assigned directly to developer', 'forward to developer', 'developer work'], true, ['nic', 'senior']);
		        $seniorForwardComment = $findTextComment(['forwarded to senior', 'assigned to senior', 'senior developer'], true, ['nic']);
		        $nicForwardComment = $findTextComment(['forwarded to nic', 'forwarded nic admin', 'forwarded to nic admin', 'auto forwarded to nic admin'], true, ['state']);
		        $stateFinalForwardComment = $findTextComment(['forwarded to state admin', 'forward to state admin', 'returned to state admin', 'final confirmation'], true, ['nic']);
		        $userReturnComment = $findTextComment(['returned to user', 'return to user'], true, ['state']);
	        $flowStartTime = $commentTimestamp($createdComment);
	        $latestAssignmentTime = null;
	        if (!empty($selectedTicket->latest_assignment_at)) {
	            try {
	                $latestAssignmentTime = \Carbon\Carbon::parse($selectedTicket->latest_assignment_at)->timestamp;
	            } catch (\Throwable $e) {
	                $latestAssignmentTime = null;
	            }
	        }
	        $hasLatestAssignmentInCurrentFlow = !$reopenComment
	            || ($latestAssignmentTime !== null && $flowStartTime !== null && $latestAssignmentTime >= $flowStartTime);
	        $normalizeTicketStatus = function ($status) {
	            return \App\Models\FreshHelpdesk::normalizedTicketStatus($status);
	        };
            $latestAssignmentStatus = $normalizeTicketStatus($selectedTicket->latest_assignment_status ?? '');
            $latestAssignmentHasAssignee = $hasLatestAssignmentInCurrentFlow
                && (
                    !empty($selectedTicket->latest_assignment_userid)
                    || !empty($selectedTicket->latest_assignment_name)
                    || !empty($selectedTicket->latest_assignment_dev_name)
                );
            $latestAssignmentIsSenior = str_contains($latestAssignmentStatus, 'senior') || str_contains($latestAssignmentStatus, 'lead');
            $latestAssignmentIsDeveloper = $latestAssignmentHasAssignee
                && !$latestAssignmentIsSenior
                && str_contains($latestAssignmentStatus, 'developer');
            $assignedDevelopmentRole = $normalizeTicketStatus($selectedTicket->forwarded_to_role ?? '');
            $hasDevelopmentTeamCommentTarget = (
                    $latestAssignmentHasAssignee
                    && ($latestAssignmentIsSenior || str_contains($latestAssignmentStatus, 'developer'))
                )
                || (
                    !empty($selectedTicket->assigned_to_userid)
                    && in_array($assignedDevelopmentRole, ['developer', 'senior developer', 'senior_developer'], true)
                );
			        $assignedName = $selectedTicket->assigned_to_name
			            ?: ($selectedTicket->assigned_user_name ?? null)
			            ?: ($selectedTicket->forwarded_user_name ?? null)
			            ?: ($hasLatestAssignmentInCurrentFlow ? ($selectedTicket->latest_assignment_name ?? null) : null)
			            ?: ($hasLatestAssignmentInCurrentFlow ? ($selectedTicket->latest_assignment_dev_name ?? null) : null)
			            ?: ($selectedTicket->latest_dev_comment_user_name ?? null);
            $ticketHasDeveloperOwner = (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER
                || in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER, \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR], true)
                || $latestAssignmentIsDeveloper
                || $developerForwardComment
                || $developerComment;
            $developerAssignedName = $ticketHasDeveloperOwner
                ? (
                    ($developerComment->user_name ?? null)
                    ?: ($latestAssignmentIsDeveloper ? ($selectedTicket->latest_assignment_name ?? null) : null)
                    ?: ($latestAssignmentIsDeveloper ? ($selectedTicket->latest_assignment_dev_name ?? null) : null)
                    ?: ((string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER ? ($selectedTicket->assigned_to_name ?? null) : null)
                    ?: ((string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER ? ($selectedTicket->assigned_user_name ?? null) : null)
                    ?: ((string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER ? ($selectedTicket->forwarded_user_name ?? null) : null)
                )
                : null;
		        $concernedDeveloperName = $developerAssignedName ?: $assignedName ?: null;
		        $developerDisplayName = $developerAssignedName ?: 'Developer';
		        $canShowConcernedDeveloper = (bool) $concernedDeveloperName
		            && !in_array($role, [\App\Models\FreshHelpdesk::ROLE_STATE_ADMIN, \App\Models\FreshHelpdesk::ROLE_USER], true);
	        $step = fn ($name, $roleName, $meta, $time = null, $comment = null) => [
	            'name' => $name,
	            'role' => $roleName,
	            'meta' => $meta,
	            'time' => $time,
	            'comment' => $comment,
	        ];
	        $stepTitle = function (array $step) {
	            $name = trim((string) ($step['name'] ?? ''));
	            $roleName = trim((string) ($step['role'] ?? ''));

	            if ($name === '') {
	                return $roleName ?: '-';
	            }

	            if ($roleName === '') {
	                return $name;
	            }

	            return $name.' ('.$roleName.')';
	        };
	        $publicFlow = in_array($role, [\App\Models\FreshHelpdesk::ROLE_USER, \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN], true);
	        $nicFullFlow = in_array($role, [
	            \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN,
	            \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER,
	            \App\Models\FreshHelpdesk::ROLE_DEVELOPER,
	        ], true);
            $canChooseCommentVisibility = \App\Models\FreshHelpdesk::canCreateInternalTicketComments($role)
                && $hasDevelopmentTeamCommentTarget;
				        $forwardedRoleValue = (string) $selectedTicket->forwarded_to_role;
				        $normalizedTicketStatusValue = $normalizeTicketStatus($ticketStatusValue);
				        $normalizedForwardedRoleValue = $normalizeTicketStatus($forwardedRoleValue);
				        $isResolvedStatus = \App\Models\FreshHelpdesk::ticketStatusFilterKey($ticketStatusValue) === \App\Models\FreshHelpdesk::TICKET_RESOLVED;
				        $isStatusOnlyWorkflowStatus = in_array($ticketStatusValue, [
				            \App\Models\FreshHelpdesk::TICKET_IN_PROGRESS,
				            \App\Models\FreshHelpdesk::TICKET_NEED_CLARIFICATION,
				            \App\Models\FreshHelpdesk::TICKET_RESOLVED,
				            \App\Models\FreshHelpdesk::TICKET_CLOSED,
				        ], true);
				        $pendingStateStatuses = [
				            \App\Models\FreshHelpdesk::TICKET_PENDING_STATE,
				            \App\Models\FreshHelpdesk::TICKET_PENDING_STATE_REVIEW,
				            \App\Models\FreshHelpdesk::TICKET_PENDING_STATE_ADMIN_REVIEW,
				        ];
			        $isForwardToStateStatus = \App\Models\FreshHelpdesk::isLegacyStateForwardStatus($ticketStatusValue)
			            || \App\Models\FreshHelpdesk::isLegacyStateForwardStatus($forwardedRoleValue);
				        $isLegacyActiveStatus = in_array($normalizedTicketStatusValue, ['in progress', 'inprogress', 'open', 'pending', 'need clarification', 'needclarification'], true)
				            || $ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_NEED_CLARIFICATION
				            || $isForwardToStateStatus;
			        $isStateForwardedRole = in_array($forwardedRoleValue, ['superadmin', 'stateadmin'], true)
			            || in_array($normalizedForwardedRoleValue, ['superadmin', 'stateadmin', 'state admin'], true)
			            || $isForwardToStateStatus
			            || \App\Models\FreshHelpdesk::isLegacyStateForwardStatus($forwardedRoleValue);
				        $isStateStageTicket = $isStateForwardedRole
				            && (in_array($ticketStatusValue, array_merge($pendingStateStatuses, [\App\Models\FreshHelpdesk::TICKET_RETURNED_STATE, \App\Models\FreshHelpdesk::TICKET_RESOLVED]), true) || $isLegacyActiveStatus);
				        $isNicStageTicket = in_array($forwardedRoleValue, ['nicadmin', 'nic_admin', 'nic_admn', \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN], true)
				            && $ticketStatusValue !== \App\Models\FreshHelpdesk::TICKET_CLOSED;
			        $isNicForwardedRole = in_array($forwardedRoleValue, ['nicadmin', 'nic_admin', 'nic_admn', \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN], true);
				        $isSeniorForwardedRole = in_array($forwardedRoleValue, [\App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER, 'senior_developer'], true);
					        $isDeveloperForwardedRole = in_array($forwardedRoleValue, [\App\Models\FreshHelpdesk::ROLE_DEVELOPER, 'developer'], true);
						        $isUserForwardedRole = in_array($normalizedForwardedRoleValue, ['user', 'ticket owner', 'owner'], true);
						        $ticketCreatorIsStateAdmin = \App\Models\FreshHelpdesk::ticketCreatorIsStateAdmin($selectedTicket);
						        $stateOwnerStepName = $ticketCreatorIsStateAdmin
						            ? ($selectedTicket->user_name ?: 'State Admin')
						            : ($stateReturnComment->user_name ?? 'State Admin');
						        $stateOwnerStepRole = $ticketCreatorIsStateAdmin ? 'StateAdmin' : 'State Admin';
						        $isStateAdminCreatorClosed = $ticketCreatorIsStateAdmin
						            && $ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_CLOSED;
						        $isReturnedToUserFlow = $ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_USER
						            || ($isUserForwardedRole && !$isResolvedStatus);
			        $usesSeniorPath = $seniorComment
		            || $seniorForwardComment
		            || in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR, \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR], true)
		            || (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER;
            $hasActualDeveloperStep = $developerAssignedName
		            || $developerForwardComment
                || $developerComment
                || $latestAssignmentIsDeveloper
		            || in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER, \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR], true)
		            || (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER;
		        $hasDeveloperProgress = $hasActualDeveloperStep;
			        $hasSeniorProgress = $seniorComment
			            || $seniorForwardComment
			            || in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR, \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR], true)
			            || (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER;
			        $hasPreDeveloperSeniorStep = $usesSeniorPath
			            && (
			                $seniorForwardComment
			                || (!$hasActualDeveloperStep && (
			                    $ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR
			                    || (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER
			                ))
			            );
		        $hasNicProgress = $nicComment
		            || $nicForwardComment
		            || $hasSeniorProgress
		            || $hasDeveloperProgress
		            || $isNicStageTicket
		            || in_array($ticketStatusValue, [
		                \App\Models\FreshHelpdesk::TICKET_PENDING_NIC,
		                \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
		                \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
		                \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER,
		            ], true);
				        $hasStateFinalProgress = $stateFinalForwardComment
				            || $ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE
				            || $isStateAdminCreatorClosed;
		        $stateFinalTime = $commentTimestamp($stateFinalForwardComment);
		        $nicForwardTime = $commentTimestamp($nicForwardComment);
		        $hasNicReforwardAfterState = $hasStateFinalProgress
		            && $nicForwardComment
		            && $stateFinalTime !== null
		            && $nicForwardTime !== null
		            && $nicForwardTime > $stateFinalTime;
		        if ($hasStateFinalProgress && !$isResolvedStatus && !$hasNicReforwardAfterState) {
		            $hasDeveloperProgress = false;
		            $hasSeniorProgress = false;
		        }

	        if ($nicFullFlow) {
	            $creatorRoleNormalized = \App\Models\FreshHelpdesk::normalizedTicketStatus((string) ($createdComment->user_role ?? ''));
		            $skipRedundantStateStep = $creatorRoleNormalized === 'state admin' || $ticketCreatorIsStateAdmin;
	            $idxOffset = $skipRedundantStateStep ? 1 : 0;

	            $flowSteps = [
	                $step(
	                    $selectedTicket->user_name ?: 'User',
	                    trim((string) ($createdComment->user_role ?? '')) ?: 'User',
	                    $flowStartedMeta,
	                    $flowStartedAt,
	                    $createdComment->comment ?? null
	                ),
	            ];

	            if (!$skipRedundantStateStep) {
	                $flowSteps[] = $step(
	                    $stateComment->user_name ?? 'State Admin',
	                    'State Admin',
	                    'State Admin review',
	                    $stateComment->created_at ?? ($isForwardToStateStatus ? $selectedTicket->forwarded_at : null),
	                    $stateComment->comment ?? null
	                );
	            }

	            $flowSteps[] = $step(
	                    $nicComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'NIC Admin review',
		                    $nicComment->created_at ?? (in_array($ticketStatusValue, [
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_NIC,
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER,
		                    ], true) || $isNicStageTicket || $hasNicProgress ? ($nicForwardComment->created_at ?? $selectedTicket->forwarded_at) : null),
	                    $nicComment->comment ?? ($nicForwardComment->comment ?? null)
	                );

		            $seniorReviewIndex = null;
		            $seniorReturnIndex = null;
		            if ($hasPreDeveloperSeniorStep) {
		                $seniorReviewIndex = count($flowSteps);
		                $flowSteps[] = $step(
		                    $seniorComment->user_name ?? ($selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER ? ($assignedName ?: 'Senior Developer') : 'Senior Developer'),
		                    'Senior Developer',
	                    'Senior review',
	                    $seniorAssignedAt ?? ($seniorComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR ? $selectedTicket->forwarded_at : null)),
	                    $seniorComment->comment ?? null
	                );
	            }

	            $developerIndex = null;
	            if ($hasActualDeveloperStep) {
	                $developerIndex = count($flowSteps);
	                $flowSteps[] = $step(
	                    $developerDisplayName,
	                    'Developer',
	                    $usesSeniorPath ? 'Developer work' : 'Direct developer work',
	                    $developerComment->created_at ?? ($developerAssignedAt ?? ($developerForwardComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER ? $selectedTicket->forwarded_at : null))),
	                    $developerComment->comment ?? (($latestAssignmentIsDeveloper ? ($selectedTicket->latest_assignment_notes ?? null) : null) ?? ($developerForwardComment->comment ?? null))
	                );
	            }

		            if (!$isStateAdminCreatorClosed && in_array($ticketStatusValue, [
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE,
	                \App\Models\FreshHelpdesk::TICKET_CLOSED,
	                \App\Models\FreshHelpdesk::TICKET_RESOLVED,
	            ], true)) {
		                if ($usesSeniorPath) {
		                    $seniorReturnIndex = count($flowSteps);
		                    $flowSteps[] = $step(
		                        $seniorReturnComment->user_name ?? ($selectedTicket->assigned_to_name ?? 'Senior Developer'),
		                        'Senior Developer',
		                        'Developer returned',
		                        $seniorReturnComment->created_at ?? ($seniorAssignedAt ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR ? $selectedTicket->forwarded_at : null)),
		                        $seniorReturnComment->comment ?? null
		                    );
		                }

	                $flowSteps[] = $step(
	                    $nicReturnComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'NIC return review',
	                    $nicReturnComment->created_at ?? (in_array($ticketStatusValue, [
	                        \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
	                        \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE,
	                        \App\Models\FreshHelpdesk::TICKET_CLOSED,
	                        \App\Models\FreshHelpdesk::TICKET_RESOLVED,
	                    ], true) ? $selectedTicket->forwarded_at : null),
	                    $nicReturnComment->comment ?? null
	                );
	            }

	            $stateFinalIndex = null;
	            $nicReforwardIndex = null;
	            if ($hasStateFinalProgress) {
	                $stateFinalIndex = count($flowSteps);
		                $flowSteps[] = $step(
			                    $stateOwnerStepName,
			                    $stateOwnerStepRole,
		                    $isStateAdminCreatorClosed ? 'Closed' : 'State final review',
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->created_at ?? ($selectedTicket->resolved_at ?? $selectedTicket->updated_at))
		                        : ($stateFinalForwardComment->created_at ?? ($stateReturnComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE ? $selectedTicket->forwarded_at : null))),
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->comment ?? null)
		                        : ($stateFinalForwardComment->comment ?? ($stateReturnComment->comment ?? null))
		                );
		            }

	            if ($hasNicReforwardAfterState) {
	                $nicReforwardIndex = count($flowSteps);
	                $flowSteps[] = $step(
	                    $nicForwardComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'Re-forwarded to NIC Admin',
	                    $nicForwardComment->created_at ?? $selectedTicket->forwarded_at,
	                    $nicForwardComment->comment ?? null
	                );
	            }

			            if (!$isStateAdminCreatorClosed && in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_CLOSED, \App\Models\FreshHelpdesk::TICKET_RESOLVED], true) && $isUserForwardedRole) {
	                $flowSteps[] = $step(
	                    $selectedTicket->user_name ?: 'User',
	                    'User',
	                    'Closure',
	                    $selectedTicket->resolved_at ?? $selectedTicket->updated_at,
	                    $latestComment->comment ?? null
	                );
	            }

			            $activeIndex = match ($ticketStatusValue) {
			                \App\Models\FreshHelpdesk::TICKET_PENDING_STATE,
			                \App\Models\FreshHelpdesk::TICKET_PENDING_STATE_REVIEW,
			                \App\Models\FreshHelpdesk::TICKET_PENDING_STATE_ADMIN_REVIEW => max(0, 1 - $idxOffset),
			                \App\Models\FreshHelpdesk::TICKET_PENDING_NIC => 2 - $idxOffset,
			                \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR => $seniorReviewIndex ?? count($flowSteps) - 1,
		                \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER => $developerIndex ?? count($flowSteps) - 1,
		                \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR => $seniorReturnIndex ?? ($developerIndex ?? count($flowSteps) - 1),
		                \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC => $developerIndex !== null ? ($seniorReturnIndex !== null ? $seniorReturnIndex + 1 : $developerIndex + 1) : count($flowSteps) - 1,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE => $stateFinalIndex ?? count($flowSteps) - 1,
		                \App\Models\FreshHelpdesk::TICKET_CLOSED,
		                \App\Models\FreshHelpdesk::TICKET_RESOLVED => count($flowSteps),
		                default => match ($forwardedRoleValue) {
		                    \App\Models\FreshHelpdesk::ROLE_DEVELOPER, 'developer' => $developerIndex ?? count($flowSteps) - 1,
			                    \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER, 'senior_developer' => $seniorReviewIndex ?? ($seniorReturnIndex ?? count($flowSteps) - 1),
		                    'nicadmin', 'nic_admin', 'nic_admn', \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN => 2 - $idxOffset,
		                    'stateadmin', 'superadmin', \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN => $stateFinalIndex ?? max(0, 1 - $idxOffset),
		                    'user' => count($flowSteps) - 1,
			                    default => $hasDeveloperProgress ? ($developerIndex ?? count($flowSteps) - 1) : ($hasSeniorProgress ? ($seniorReviewIndex ?? ($seniorReturnIndex ?? (2 - $idxOffset))) : 0),
		                },
		            };
		            if ($isLegacyActiveStatus) {
		                if ($hasNicReforwardAfterState && !$isResolvedStatus) {
		                    $activeIndex = $nicReforwardIndex ?? count($flowSteps) - 1;
		                } elseif ($hasStateFinalProgress && !$isResolvedStatus) {
		                    $activeIndex = $stateFinalIndex ?? count($flowSteps) - 1;
		                } elseif ($hasDeveloperProgress) {
		                    $activeIndex = $developerIndex ?? count($flowSteps) - 1;
			                } elseif ($hasSeniorProgress) {
			                    $activeIndex = $seniorReviewIndex ?? ($seniorReturnIndex ?? $activeIndex);
		                } elseif ($hasNicProgress || $isNicStageTicket) {
		                    $activeIndex = 2 - $idxOffset;
		                } elseif ($isStateStageTicket) {
		                    $activeIndex = max(0, 1 - $idxOffset);
		                }
		            }
			            if ($isStatusOnlyWorkflowStatus && $isStateForwardedRole) {
			                $activeIndex = max(0, 1 - $idxOffset);
			            } elseif ($isStatusOnlyWorkflowStatus && $isNicForwardedRole) {
			                $activeIndex = 2 - $idxOffset;
				            } elseif ($isStatusOnlyWorkflowStatus && $isSeniorForwardedRole) {
				                $activeIndex = $seniorReviewIndex ?? ($seniorReturnIndex ?? count($flowSteps) - 1);
			            } elseif ($isStatusOnlyWorkflowStatus && $isDeveloperForwardedRole) {
			                $activeIndex = $developerIndex ?? count($flowSteps) - 1;
			            } elseif ($hasNicReforwardAfterState && !$isResolvedStatus) {
			                $activeIndex = $nicReforwardIndex ?? count($flowSteps) - 1;
			            }
		        } elseif ($publicFlow) {
	            $creatorRoleNormalized = \App\Models\FreshHelpdesk::normalizedTicketStatus((string) ($createdComment->user_role ?? ''));
	            $skipRedundantStateStep = $creatorRoleNormalized === 'state admin' || $ticketCreatorIsStateAdmin;
	            $idxOffset = $skipRedundantStateStep ? 1 : 0;

	            $flowSteps = [
	                $step(
	                    $selectedTicket->user_name ?: 'User',
	                    trim((string) ($createdComment->user_role ?? '')) ?: 'User',
	                    $flowStartedMeta,
	                    $flowStartedAt,
	                    $createdComment->comment ?? null
	                ),
	            ];

	            if (!$skipRedundantStateStep) {
	                $flowSteps[] = $step(
	                    $stateComment->user_name ?? 'State Admin',
	                    'State Admin',
	                    'State Admin review',
	                    $stateComment->created_at ?? ((in_array($ticketStatusValue, $pendingStateStatuses, true) || $isForwardToStateStatus) ? $selectedTicket->forwarded_at : null),
	                    $stateComment->comment ?? null
	                );
	            }

		            $publicNicIndex = null;
		            if (!$isReturnedToUserFlow || $hasNicProgress || $isNicStageTicket || $hasStateFinalProgress || $hasNicReforwardAfterState) {
		                $publicNicIndex = count($flowSteps);
		                $flowSteps[] = $step(
		                        $nicComment->user_name ?? 'NIC Admin',
		                        'NIC Admin',
		                        'NIC Admin review',
				                    $nicComment->created_at ?? (in_array($ticketStatusValue, [
				                        \App\Models\FreshHelpdesk::TICKET_PENDING_NIC,
				                        \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
				                        \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER,
				                        \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR,
				                        \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
				                    ], true) || $isNicStageTicket || $hasNicProgress ? ($nicForwardComment->created_at ?? $selectedTicket->forwarded_at) : null),
		                        $nicComment->comment ?? ($nicForwardComment->comment ?? null)
		                    );
		            }

	            $publicStateFinalIndex = null;
	            $publicNicReforwardIndex = null;
	            if ($hasStateFinalProgress) {
	                $publicStateFinalIndex = count($flowSteps);
		                $flowSteps[] = $step(
			                    $stateOwnerStepName,
			                    $stateOwnerStepRole,
		                    $isStateAdminCreatorClosed ? 'Closed' : 'Final review',
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->created_at ?? ($selectedTicket->resolved_at ?? $selectedTicket->updated_at))
		                        : ($stateFinalForwardComment->created_at ?? ($stateReturnComment->created_at ?? $selectedTicket->forwarded_at)),
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->comment ?? null)
		                        : ($stateFinalForwardComment->comment ?? ($stateReturnComment->comment ?? null))
		                );
		            }

	            if ($hasNicReforwardAfterState) {
	                $publicNicReforwardIndex = count($flowSteps);
	                $flowSteps[] = $step(
	                    $nicForwardComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'Re-forwarded to NIC Admin',
	                    $nicForwardComment->created_at ?? $selectedTicket->forwarded_at,
	                    $nicForwardComment->comment ?? null
	                );
	            }

		            $publicUserIndex = null;
			            if ($isReturnedToUserFlow || (!$isStateAdminCreatorClosed && in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_CLOSED, \App\Models\FreshHelpdesk::TICKET_RESOLVED], true) && $isUserForwardedRole)) {
		                $publicUserIndex = count($flowSteps);
		                $flowSteps[] = $step(
		                    $selectedTicket->user_name ?: 'User',
		                    'User',
		                    $isReturnedToUserFlow ? 'Returned to User' : 'Closure',
		                    $userReturnComment->created_at ?? ($selectedTicket->resolved_at ?? $selectedTicket->updated_at),
		                    $userReturnComment->comment ?? ($latestComment->comment ?? null)
		                );
		            }

		            if ($isLegacyActiveStatus && $hasNicReforwardAfterState && !$isResolvedStatus) {
		                $activeIndex = $publicNicReforwardIndex ?? count($flowSteps) - 1;
		            } elseif ($isLegacyActiveStatus && $hasStateFinalProgress && !$isResolvedStatus) {
		                $activeIndex = $publicStateFinalIndex ?? count($flowSteps) - 1;
			            } elseif ($isReturnedToUserFlow) {
			                $activeIndex = $publicUserIndex ?? count($flowSteps) - 1;
			            } elseif ($isLegacyActiveStatus && ($hasDeveloperProgress || $hasSeniorProgress || $hasNicProgress || $isNicStageTicket)) {
			                $activeIndex = $publicNicIndex ?? (2 - $idxOffset);
		            } elseif ($isLegacyActiveStatus && $isStateStageTicket) {
		                $activeIndex = max(0, 1 - $idxOffset);
		            } elseif (in_array($ticketStatusValue, $pendingStateStatuses, true) || $isForwardToStateStatus) {
			                $activeIndex = max(0, 1 - $idxOffset);
		            } elseif ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE) {
		                $activeIndex = 3 - $idxOffset;
			            } elseif ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_USER) {
			                $activeIndex = $publicUserIndex ?? count($flowSteps) - 1;
	            } elseif (in_array($ticketStatusValue, [\App\Models\FreshHelpdesk::TICKET_CLOSED, \App\Models\FreshHelpdesk::TICKET_RESOLVED], true)) {
	                $activeIndex = $publicUserIndex ?? count($flowSteps);
	            } else {
	                $activeIndex = 2 - $idxOffset;
	            }
		            if ($isReturnedToUserFlow) {
		                $activeIndex = $publicUserIndex ?? count($flowSteps) - 1;
		            } elseif ($isStatusOnlyWorkflowStatus && $isStateForwardedRole) {
		                $activeIndex = max(0, 1 - $idxOffset);
		            } elseif ($isStatusOnlyWorkflowStatus && $isNicForwardedRole) {
		                $activeIndex = $publicNicIndex ?? (2 - $idxOffset);
		            } elseif ($hasNicReforwardAfterState && !$isResolvedStatus) {
		                $activeIndex = $publicNicReforwardIndex ?? count($flowSteps) - 1;
		            }
	        } else {
	            $flowSteps = [
	                $step(
	                    $nicComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'NIC Admin review',
		                    $nicComment->created_at ?? (in_array($ticketStatusValue, [
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_NIC,
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
		                        \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER,
		                    ], true) || $isNicStageTicket || $hasNicProgress ? ($nicForwardComment->created_at ?? $selectedTicket->forwarded_at) : null),
	                    $nicComment->comment ?? ($nicForwardComment->comment ?? null)
	                ),
	                $step(
	                    $seniorComment->user_name ?? ($selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER ? ($assignedName ?: 'Senior Developer') : 'Senior Developer'),
	                    'Senior Developer',
	                    'Senior review',
	                    $seniorComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR ? $selectedTicket->forwarded_at : null),
	                    $seniorComment->comment ?? null
	                ),
	            ];

                $developerSideIndex = null;
                if ($hasActualDeveloperStep) {
                    $developerSideIndex = count($flowSteps);
                    $flowSteps[] = $step(
                        $developerDisplayName,
                        'Developer',
                        'Developer work',
                        $developerComment->created_at ?? (($latestAssignmentIsDeveloper ? ($selectedTicket->latest_assignment_at ?? null) : null) ?? ($developerForwardComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER ? $selectedTicket->forwarded_at : null))),
                        $developerComment->comment ?? (($latestAssignmentIsDeveloper ? ($selectedTicket->latest_assignment_notes ?? null) : null) ?? ($developerForwardComment->comment ?? null))
                    );
                }

		            if (!$isStateAdminCreatorClosed && in_array($ticketStatusValue, [
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE,
	                \App\Models\FreshHelpdesk::TICKET_CLOSED,
	                \App\Models\FreshHelpdesk::TICKET_RESOLVED,
	            ], true)) {
	                $flowSteps[] = $step(
	                    $seniorReturnComment->user_name ?? 'Senior Developer',
	                    'Senior Developer',
	                    'Return review',
	                    $seniorReturnComment->created_at ?? ($ticketStatusValue === \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR ? $selectedTicket->forwarded_at : null),
	                    $seniorReturnComment->comment ?? null
	                );
	                $flowSteps[] = $step(
	                    $nicReturnComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'Return review',
	                    $nicReturnComment->created_at ?? (in_array($ticketStatusValue, [
	                        \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
	                        \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE,
	                        \App\Models\FreshHelpdesk::TICKET_CLOSED,
	                        \App\Models\FreshHelpdesk::TICKET_RESOLVED,
	                    ], true) ? $selectedTicket->forwarded_at : null),
	                    $nicReturnComment->comment ?? null
	                );
	            }

	            $compactStateFinalIndex = null;
	            $compactNicReforwardIndex = null;
	            if ($hasStateFinalProgress) {
	                $compactStateFinalIndex = count($flowSteps);
		                $flowSteps[] = $step(
			                    $stateOwnerStepName,
			                    $stateOwnerStepRole,
		                    $isStateAdminCreatorClosed ? 'Closed' : 'State final review',
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->created_at ?? ($selectedTicket->resolved_at ?? $selectedTicket->updated_at))
		                        : ($stateFinalForwardComment->created_at ?? ($stateReturnComment->created_at ?? $selectedTicket->forwarded_at)),
		                    $isStateAdminCreatorClosed
		                        ? ($latestComment->comment ?? null)
		                        : ($stateFinalForwardComment->comment ?? ($stateReturnComment->comment ?? null))
		                );
		            }

	            if ($hasNicReforwardAfterState) {
	                $compactNicReforwardIndex = count($flowSteps);
	                $flowSteps[] = $step(
	                    $nicForwardComment->user_name ?? 'NIC Admin',
	                    'NIC Admin',
	                    'Re-forwarded to NIC Admin',
	                    $nicForwardComment->created_at ?? $selectedTicket->forwarded_at,
	                    $nicForwardComment->comment ?? null
	                );
	            }

		            $activeIndex = match ($ticketStatusValue) {
		                \App\Models\FreshHelpdesk::TICKET_PENDING_NIC => 0,
	                \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR => 1,
	                \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER => $developerSideIndex ?? count($flowSteps) - 1,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR => $developerSideIndex !== null ? $developerSideIndex + 1 : count($flowSteps) - 1,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_NIC,
	                \App\Models\FreshHelpdesk::TICKET_RETURNED_STATE => 4,
		                \App\Models\FreshHelpdesk::TICKET_CLOSED,
		                \App\Models\FreshHelpdesk::TICKET_RESOLVED => count($flowSteps),
		                default => match ($forwardedRoleValue) {
		                    \App\Models\FreshHelpdesk::ROLE_DEVELOPER, 'developer' => $developerSideIndex ?? count($flowSteps) - 1,
		                    \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER, 'senior_developer' => 1,
		                    'nicadmin', 'nic_admin', 'nic_admn', \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN => 0,
		                    'stateadmin', 'superadmin', \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN => $compactStateFinalIndex ?? count($flowSteps) - 1,
		                    default => $hasDeveloperProgress ? ($developerSideIndex ?? count($flowSteps) - 1) : ($hasSeniorProgress ? 1 : 0),
		                },
		            };
		            if ($isLegacyActiveStatus) {
		                if ($hasNicReforwardAfterState && !$isResolvedStatus) {
		                    $activeIndex = $compactNicReforwardIndex ?? count($flowSteps) - 1;
		                } elseif ($hasStateFinalProgress && !$isResolvedStatus) {
		                    $activeIndex = $compactStateFinalIndex ?? count($flowSteps) - 1;
		                } elseif ($hasDeveloperProgress) {
		                    $activeIndex = $developerSideIndex ?? count($flowSteps) - 1;
		                } elseif ($hasSeniorProgress) {
		                    $activeIndex = 1;
		                } elseif ($hasNicProgress) {
		                    $activeIndex = 0;
		                } else {
		                    $activeIndex = match ($forwardedRoleValue) {
		                        \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER => 1,
		                        \App\Models\FreshHelpdesk::ROLE_DEVELOPER, 'developer' => $developerSideIndex ?? count($flowSteps) - 1,
		                        default => 0,
		                    };
		                }
		            }
			            if ($isStatusOnlyWorkflowStatus && $isNicForwardedRole) {
			                $activeIndex = 0;
			            } elseif ($isStatusOnlyWorkflowStatus && $isSeniorForwardedRole) {
			                $activeIndex = 1;
			            } elseif ($isStatusOnlyWorkflowStatus && $isDeveloperForwardedRole) {
			                $activeIndex = $developerSideIndex ?? count($flowSteps) - 1;
			            } elseif ($hasNicReforwardAfterState && !$isResolvedStatus) {
			                $activeIndex = $compactNicReforwardIndex ?? count($flowSteps) - 1;
			            }
		        }
			        if ($isResolvedStatus) {
			            $activeIndex = count($flowSteps);
			        }
			        $canReopenTicket = (string) ($selectedTicket->cams_userid ?? '') === (string) \App\Models\FreshHelpdesk::userId()
			            && \App\Models\FreshHelpdesk::ticketStatusFilterKey($selectedTicket->status) === \App\Models\FreshHelpdesk::TICKET_RESOLVED;
			        $canUserReturnToState = $role === \App\Models\FreshHelpdesk::ROLE_USER
			            && (string) ($selectedTicket->cams_userid ?? '') === (string) \App\Models\FreshHelpdesk::userId()
			            && \App\Models\FreshHelpdesk::normalizedTicketStatus($selectedTicket->forwarded_to_role ?? '') === 'user'
			            && (string) ($selectedTicket->status ?? '') === \App\Models\FreshHelpdesk::TICKET_RETURNED_USER;
			        $isCreatorTicketWithUser = $role === \App\Models\FreshHelpdesk::ROLE_USER
		            && (string) ($selectedTicket->cams_userid ?? '') === (string) \App\Models\FreshHelpdesk::userId()
		            && (
		                \App\Models\FreshHelpdesk::normalizedTicketStatus($selectedTicket->forwarded_to_role ?? '') === 'user'
		                || \App\Models\FreshHelpdesk::ticketStatusFilterKey($selectedTicket->status) === \App\Models\FreshHelpdesk::TICKET_RESOLVED
		            );
				        $canStateAdminActOnCurrentTicket = $isStateStageTicket
				            && \App\Models\FreshHelpdesk::currentStateAdminCanActOnTicket($selectedTicket);
				        $isStateAdminCreatorTicket = \App\Models\FreshHelpdesk::ticketCreatorIsStateAdmin($selectedTicket);
				        $canActOnCurrentStage = match ($role) {
			            \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN => $canStateAdminActOnCurrentTicket,
		            \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN => $isNicStageTicket,
		            \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER => (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER
		                && (string) $selectedTicket->assigned_to_userid === \App\Models\FreshHelpdesk::userId()
				                && in_array($ticketStatusValue, [
				                    \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
				                    \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR,
				                    \App\Models\FreshHelpdesk::TICKET_NEED_CLARIFICATION,
				                ], true),
	            default => false,
	        };
			        $isTicketWithMeAsDeveloper = $role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER
			            && (string) $selectedTicket->forwarded_to_role === \App\Models\FreshHelpdesk::ROLE_DEVELOPER
			            && (string) $selectedTicket->assigned_to_userid === \App\Models\FreshHelpdesk::userId();
			        $canDeveloperReturnToSenior = $isTicketWithMeAsDeveloper
			            && $ticketStatusValue !== \App\Models\FreshHelpdesk::TICKET_CLOSED;
		        $canSeniorUpdateAssignedDeveloperTicket = $role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER
		            && (string) ($selectedTicket->forwarded_to_role ?? '') === \App\Models\FreshHelpdesk::ROLE_DEVELOPER
		            && in_array((string) ($selectedTicket->status ?? ''), [
		                \App\Models\FreshHelpdesk::TICKET_PENDING_DEVELOPER,
		                \App\Models\FreshHelpdesk::TICKET_NEED_CLARIFICATION,
		            ], true)
		            && (string) ($selectedTicket->latest_assigned_by_userid ?? '') === (string) \App\Models\FreshHelpdesk::userId();
				        $canNicAdminUpdateTicketStatus = $role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN
				            && $isNicStageTicket;
				        $showStatusUpdateForm = !$canReopenTicket && ($canActOnCurrentStage || $canSeniorUpdateAssignedDeveloperTicket || $canNicAdminUpdateTicketStatus || $isCreatorTicketWithUser);
		        $statusUpdateValues = $isCreatorTicketWithUser
		            ? [\App\Models\FreshHelpdesk::TICKET_RESOLVED]
		            : array_keys(\App\Models\FreshHelpdesk::ticketStatusFilterLabels());
	    @endphp
    <div class="fh-details-page">
        <div class="fh-sheet-hero">
            <div>
                <span>Helpdesk</span>
                <h3>Ticket Details</h3>
            </div>
	            <a href="{{ route('fresh-helpdesk.ticket-details', ['clear_filters' => 1]) }}" class="btn btn-light">
	                <i class="ti ti-arrow-left me-1"></i> Back to Ticket List
	            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row g-3">
            <div class="col-xl-9 col-lg-8">
                <div class="fh-sheet-card fh-sheet-card-accent mb-3">
                    <div class="fh-sheet-card-header">Ticket Information</div>
                    <div class="fh-sheet-card-body">
                        <div class="fh-ticket-summary">
                            <div class="d-flex align-items-start gap-2 flex-wrap">
                                <div>
		                                    <small>
		                                        {{ $selectedTicket->ticket_number ?: '#'.$selectedTicket->id }}
		                                        @if (strtoupper((string) ($selectedTicket->importflag ?? '')) === 'Y')
		                                            <span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>
		                                        @endif
		                                        @if ($isTicketReopened)
		                                            <span class="fh-reopened-badge">Reopened</span>
		                                        @endif
	                                    </small>
                                    <h4>{{ $selectedTicket->subject ?: '-' }}</h4>
                                </div>
	                                <span class="fh-status-badge {{ $ticketStatusClass($selectedTicket->status) }}">{{ $ticketStatus($selectedTicket->status) }}</span>
                            </div>
                        </div>

                        <div class="row g-2 fh-info-grid">
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-user fh-info-icon"></i> Created By</small>
                                    <strong>{{ $selectedTicket->user_name ?: '-' }}</strong>
                                    <span class="fh-cell-muted">- {{ $selectedTicket->ticket_number ?: '#'.$selectedTicket->id }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-building fh-info-icon"></i> Department</small>
                                    <strong>{{ $selectedTicket->department_name ?: '-' }}</strong>
                                    <span class="fh-cell-muted">- {{ $selectedTicket->category ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-flag fh-info-icon"></i> Priority</small>
                                    <strong>
	                                        <span class="fh-priority-badge {{ $ticketPriorityClass($selectedTicket->priority) }}">
	                                            {{ $selectedTicket->priority ?: '-' }}
	                                        </span>
                                    </strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-category fh-info-icon"></i> Type</small>
                                    <strong>{{ \Illuminate\Support\Str::headline((string) ($selectedTicket->request_type ?: '-')) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-affiliate fh-info-icon"></i> Ticket Scope</small>
                                    <strong>{{ \App\Models\FreshHelpdesk::ticketScopeTypeLabel($selectedTicket->ticket_scope_type ?? null) }}</strong>
                                </div>
                            </div>
	                            <div class="col-md-4">
	                                <div class="fh-info-box">
	                                    <small><i class="ti ti-user-check fh-info-icon"></i> Currently With</small>
	                                    <strong>{{ \App\Models\FreshHelpdesk::dashboardCurrentWith($selectedTicket, $role) }}</strong>
	                                    <span class="fh-cell-muted">- {{ $fmt($selectedTicket->forwarded_at) }}</span>
	                                </div>
	                            </div>
	                            @if ($canShowConcernedDeveloper)
	                                <div class="col-md-4">
	                                    <div class="fh-info-box">
	                                        <small><i class="ti ti-code fh-info-icon"></i> Concerned Developer</small>
	                                        <strong>{{ $concernedDeveloperName }}</strong>
	                                        <span class="fh-cell-muted">- {{ $fmt($selectedTicket->latest_assignment_at ?? null) }}</span>
	                                    </div>
	                                </div>
	                            @endif
		                            @if (\App\Models\FreshHelpdesk::canViewDeveloperStatus($role))
		                                <div class="col-md-4">
		                                    <div class="fh-info-box">
		                                        <small><i class="ti ti-progress fh-info-icon"></i> Developer Status</small>
		                                        <strong>{{ \App\Models\FreshHelpdesk::developerStatusLabel($selectedTicket->developer_status ?? null) }}</strong>
		                                    </div>
		                                </div>
			                                <div class="col-md-4">
			                                    <div class="fh-info-box">
			                                        <small><i class="ti ti-player-play fh-info-icon"></i> Started On</small>
			                                        <strong>{{ $fmt($selectedTicket->developer_started_on ?? null) }}</strong>
			                                    </div>
			                                </div>
			                            @endif
	                            <div class="col-md-4">
	                                <div class="fh-info-box">
	                                    <small><i class="ti ti-calendar-plus fh-info-icon"></i> Created On</small>
                                    <strong>{{ $fmt($selectedTicket->created_at) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-calendar-stats fh-info-icon"></i> Financial Year</small>
                                    <strong>{{ $selectedTicket->financialyear ?: $selectedTicket->financialyearcode ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-calendar-event fh-info-icon"></i> Audit Quarter</small>
                                    <strong>{{ $selectedTicket->planname ?: ($selectedTicket->auditquarter ?: $selectedTicket->auditquartercode ?: '-') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fh-info-box">
                                    <small><i class="ti ti-refresh fh-info-icon"></i> Last Updated</small>
                                    <strong>{{ $fmt($selectedTicket->updated_at) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="fh-note-box h-100">
                                    <small><i class="ti ti-building-bank fh-info-icon"></i> Institution</small>
                                    <p>{{ $selectedTicket->institution ?: '-' }}</p>
                                </div>
                            </div>
	                            <div class="col-md-6">
	                                <div class="fh-note-box h-100">
	                                    <small><i class="ti ti-paperclip fh-info-icon"></i> Attachments</small>
	                                    <div class="mt-2">
	                                        @forelse ($attachments as $file)
	                                            <a href="{{ asset('storage/'.($file['path'] ?? '')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2 mb-2">
	                                                <i class="ti ti-paperclip me-1"></i> {{ $file['name'] ?? 'Attachment' }}
	                                            </a>
	                                        @empty
	                                            <p class="mb-0 text-muted">No attachment uploaded.</p>
	                                        @endforelse
	                                    </div>
	                                </div>
	                            </div>
                            <div class="col-md-6">
                                <div class="fh-note-box h-100">
                                    <small><i class="ti ti-message-circle fh-info-icon"></i> Latest Note</small>
                                    <p>{{ $latestComment ? $ticketDisplayText($latestComment->comment) : 'No update yet.' }}</p>
                                </div>
                            </div>
	                            <div class="col-md-6">
	                                <div class="fh-note-box h-100">
	                                    <small><i class="ti ti-file-description fh-info-icon"></i> Description</small>
	                                    <p>{{ $selectedTicket->description ?: '-' }}</p>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="fh-sheet-card">
	                    <div class="fh-sheet-card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
		                        <h5 class="fh-workflow-title">Workflow Timeline</h5>
		                        <div class="fh-workflow-action">
		                            <span class="fw-bold text-muted small">{{ \App\Models\FreshHelpdesk::roleLabel() }}</span>
		                            <button class="btn btn-primary btn-sm" type="button" id="toggleTicketCommentForm">Add Comment</button>
		                        </div>
	                    </div>
	                    <div class="fh-sheet-card-body">
		                        <div class="fh-flow-track" style="grid-template-columns: repeat({{ max(count($flowSteps), 1) }}, minmax(0, 1fr));">
		                            @foreach ($flowSteps as $index => $step)
		                                <div class="fh-flow-step {{ $index === 0 ? 'is-origin' : '' }} {{ $index < $activeIndex ? 'is-complete' : '' }} {{ $index === $activeIndex ? 'is-active' : '' }}">
		                                    <span class="fh-flow-dot"></span>
		                                    <strong>{{ $stepTitle($step) }}</strong>
		                                    <em class="fh-flow-time">{{ $fmt($step['time']) }}</em>
		                                </div>
		                            @endforeach
		                            </div>

		                        <form id="ticketTimelineCommentForm" class="fh-comment-form is-hidden" method="POST" action="{{ $ticketActionUrl($selectedTicket, 'comment') }}">
		                            @csrf
		                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Add timeline comment" required></textarea>
		                            @if ($canChooseCommentVisibility)
		                                <select name="visibility" class="form-select mb-2">
		                                    <option value="public">Public - Ticket</option>
		                                    <option value="internal">Internal - Development Team</option>
		                                </select>
		                            @endif
		                            <button class="btn btn-primary btn-sm" type="submit">
		                                <i class="ti ti-send me-1"></i> Save Comment
		                            </button>
		                        </form>

		                        <div class="fh-timeline-list">
	                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
	                                <small class="fw-bold text-uppercase text-muted">Timeline Details</small>
	                                <span class="badge bg-primary">{{ $ticketComments->count() }}</span>
	                            </div>
	                            @php
	                                $ticketTimelineDisplay = $ticketComments
	                                    ->sortByDesc(function ($comment) {
	                                        $createdAt = $comment->created_at ?? null;
	                                        $timestamp = 0;

	                                        if ($createdAt) {
	                                            try {
	                                                $timestamp = \Carbon\Carbon::parse($createdAt)->timestamp;
	                                            } catch (\Throwable $exception) {
	                                                $timestamp = 0;
	                                            }
	                                        }

	                                        return sprintf('%010d|%010d', $timestamp, (int) ($comment->id ?? 0));
	                                    })
	                                    ->values();
	                                $ticketTimelineCount = $ticketTimelineDisplay->count();
	                            @endphp
		                            @forelse ($ticketTimelineDisplay as $comment)
		                                <div class="fh-timeline-item">
		                                    <span class="fh-timeline-number">{{ $ticketTimelineCount - $loop->index }}</span>
		                                    <div class="fh-timeline-content">
	                                        <small>{{ $comment->user_name ?: '-' }} ({{ $ticketDisplayText($comment->user_role ?: 'Update') }})</small>
	                                        <span class="fh-timeline-date">{{ $fmt($comment->created_at) }}</span>
	                                        <p>{{ $ticketDisplayText($comment->comment) }}</p>
	                                    </div>
	                                </div>
                            @empty
                                <div class="text-muted text-center py-3">No timeline found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

	            <div class="col-xl-3 col-lg-4">
	                <div class="fh-side-panel">
	                    <h5>Ticket Action</h5>
	                    @if ($showStatusUpdateForm && !empty($statusUpdateValues))
	                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'status_update') }}" class="mb-3">
	                            @csrf
	                            <label class="form-label fw-bold">Status Update</label>
	                            <select name="ticket_status" class="form-select mb-2" required>
	                                @foreach ($statusUpdateValues as $statusValue)
	                                    <option value="{{ $statusValue }}" @selected(\App\Models\FreshHelpdesk::ticketStatusFilterKey($selectedTicket->status) === $statusValue)>
	                                        {{ $isCreatorTicketWithUser && $statusValue === \App\Models\FreshHelpdesk::TICKET_RESOLVED ? 'Closed' : $ticketStatus($statusValue) }}
	                                    </option>
	                                @endforeach
	                            </select>
	                            <textarea name="remarks" rows="2" class="form-control mb-2" placeholder="Status remarks" required></textarea>
	                            <button class="btn btn-outline-primary w-100" type="submit"><i class="ti ti-refresh me-1"></i> Update Status</button>
		                        </form>
		                    @endif

		                    @if ($canUserReturnToState)
		                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'user_to_state') }}" class="mb-3">
		                            @csrf
		                            <label class="form-label fw-bold">Return Ticket</label>
		                            <select class="form-select mb-2" disabled>
		                                <option>Return to State Admin</option>
		                            </select>
		                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Return comments" required></textarea>
		                            <button class="btn btn-primary w-100" type="submit"><i class="ti ti-arrow-back-up me-1"></i> Return to State Admin</button>
		                        </form>
		                    @endif

		                    @if ($canReopenTicket)
		                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'reopen') }}" class="mb-3">
	                            @csrf
	                            <label class="form-label fw-bold">Reopen Ticket</label>
		                            <select class="form-select mb-2" disabled>
		                                <option>Forward to State Admin</option>
		                            </select>
		                            <div class="form-check fh-action-watchlist">
		                                <input type="checkbox" name="watchlist_notify" value="1" id="freshWatchlistReopen" class="form-check-input" @checked(strtoupper((string) ($selectedTicket->importflag ?? '')) === 'Y')>
		                                <label class="form-check-label" for="freshWatchlistReopen">Important: Notify NIC Admin</label>
		                            </div>
		                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Reopen comments" required></textarea>
		                            <button class="btn btn-primary w-100" type="submit"><i class="ti ti-refresh me-1"></i> Reopen and Forward</button>
	                        </form>
	                    @endif

		                    @if ($canActOnCurrentStage && $role === \App\Models\FreshHelpdesk::ROLE_STATE_ADMIN)
		                        @if (in_array((string) $selectedTicket->status, [\App\Models\FreshHelpdesk::TICKET_RETURNED_STATE, \App\Models\FreshHelpdesk::TICKET_RESOLVED], true))
			                            <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'state_close') }}" class="mb-3">
			                                @csrf
			                                <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="{{ $isStateAdminCreatorTicket ? 'Closure remarks' : 'Resolved remarks' }}" required></textarea>
			                                <button class="btn btn-success w-100" type="submit"><i class="ti ti-check me-1"></i> {{ $isStateAdminCreatorTicket ? 'Close Ticket' : 'Resolve and Return to User' }}</button>
			                            </form>
		                        @endif
	                        @if ($isStateStageTicket)
	                            <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'state_to_user') }}" class="mb-3">
	                                @csrf
	                                <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Return remarks" required></textarea>
	                                <button class="btn btn-outline-primary w-100" type="submit"><i class="ti ti-arrow-back-up me-1"></i> Return to User</button>
	                            </form>
	                        @endif
		                        @if ($isStateStageTicket)
		                            <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'state_to_nic') }}">
		                                @csrf
		                                <div class="form-check fh-action-watchlist">
		                                    <input type="checkbox" name="watchlist_notify" value="1" id="freshWatchlistStateToNic" class="form-check-input" @checked(strtoupper((string) ($selectedTicket->importflag ?? '')) === 'Y')>
		                                    <label class="form-check-label" for="freshWatchlistStateToNic">Important: Notify NIC Admin</label>
		                                </div>
		                                <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Forward remarks" required></textarea>
		                                <button class="btn btn-primary w-100" type="submit"><i class="ti ti-arrow-right me-1"></i> Forward to NIC Admin</button>
	                            </form>
	                        @endif
		                    @elseif ($canActOnCurrentStage && $role === \App\Models\FreshHelpdesk::ROLE_NIC_ADMIN)
	                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'nic_to_state') }}" class="mb-3">
	                            @csrf
	                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Return remarks" required></textarea>
	                            <button class="btn btn-success w-100" type="submit"><i class="ti ti-arrow-back-up me-1"></i> Return to State Admin</button>
	                        </form>
	                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'nic_to_senior') }}">
	                            @csrf
	                            <select name="senior_userid" class="form-select mb-2" required>
	                                <option value="">Select senior developer</option>
                                @foreach ($seniorDevelopers as $person)
                                    <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                @endforeach
                            </select>
	                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Forward remarks" required></textarea>
	                            <button class="btn btn-primary w-100" type="submit"><i class="ti ti-arrow-right me-1"></i> Forward to Senior Developer</button>
	                        </form>
	                        <div class="text-center text-muted small my-2">or</div>
	                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'nic_to_developer') }}">
	                            @csrf
	                            <select name="developer_userid" class="form-select mb-2" required>
	                                <option value="">Select developer</option>
	                                @foreach ($developers as $person)
	                                    <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
	                                @endforeach
	                            </select>
	                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Direct assign remarks" required></textarea>
	                            <button class="btn btn-outline-primary w-100" type="submit"><i class="ti ti-user-check me-1"></i> Assign Directly to Developer</button>
	                        </form>
			                    @elseif ($canActOnCurrentStage && $role === \App\Models\FreshHelpdesk::ROLE_SENIOR_DEVELOPER)
	                        @if (in_array($selectedTicket->status, [
			                            \App\Models\FreshHelpdesk::TICKET_PENDING_SENIOR,
			                            \App\Models\FreshHelpdesk::TICKET_RETURNED_SENIOR,
			                            \App\Models\FreshHelpdesk::TICKET_NEED_CLARIFICATION,
			                        ], true))
	                            <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'senior_to_nic') }}" class="mb-3">
	                                @csrf
	                                <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Return remarks" required></textarea>
                                <button class="btn btn-success w-100" type="submit"><i class="ti ti-arrow-back-up me-1"></i> Return to NIC Admin</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'senior_to_developer') }}">
                            @csrf
                            <select name="developer_userid" class="form-select mb-2" required>
                                <option value="">Select developer</option>
                                @foreach ($developers as $person)
                                    <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                @endforeach
                            </select>
                            <textarea name="remarks" rows="3" class="form-control mb-2" placeholder="Assign remarks" required></textarea>
                            <button class="btn btn-primary w-100" type="submit"><i class="ti ti-user-check me-1"></i> Assign Developer</button>
                        </form>
					                    @elseif ($canDeveloperReturnToSenior)
			                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'developer_status_update') }}" class="mb-3">
			                            @csrf
			                            <label class="form-label fw-bold">Developer Status</label>
			                            <select name="developer_status" class="form-select mb-2" required>
		                                @foreach (\App\Models\FreshHelpdesk::developerStatusLabels() as $devStatusValue => $devStatusLabel)
		                                    <option value="{{ $devStatusValue }}" @selected(($selectedTicket->developer_status ?? \App\Models\FreshHelpdesk::DEV_STATUS_IN_PROGRESS) === $devStatusValue)>{{ $devStatusLabel }}</option>
		                                @endforeach
				                            </select>
				                            <label class="form-label fw-bold">Started On</label>
					                            @php
					                                $developerStartedSaved = !empty($selectedTicket->developer_started_on);
					                                $developerStartedValue = $developerStartedSaved
					                                    ? \Carbon\Carbon::parse($selectedTicket->developer_started_on, 'Asia/Kolkata')
					                                    : now('Asia/Kolkata');
					                                $developerStartedInputValue = $developerStartedValue->format('Y-m-d\TH:i');
					                            @endphp
					                            <input type="datetime-local" name="started_on" class="form-control mb-2"
					                                value="{{ $developerStartedInputValue }}"
					                                min="{{ $developerStartedInputValue }}"
					                                max="{{ $developerStartedInputValue }}"
					                                readonly>
			                            <textarea name="remarks" rows="2" class="form-control mb-2" placeholder="Status remarks" required></textarea>
		                            <button class="btn btn-outline-primary w-100" type="submit"><i class="ti ti-refresh me-1"></i> Update Developer Status</button>
		                        </form>
	                        <form method="POST" action="{{ $ticketActionUrl($selectedTicket, 'developer_to_senior') }}">
		                            @csrf
		                            @php
		                                $canForwardTicketToSenior = (string) ($selectedTicket->developer_status ?? '') === \App\Models\FreshHelpdesk::DEV_STATUS_COMPLETED;
		                            @endphp
		                            @unless ($canForwardTicketToSenior)
		                                <div class="alert alert-warning py-2 mb-2">Update developer status to Completed before returning to Senior Developer.</div>
		                            @endunless
		                            <textarea name="remarks" rows="4" class="form-control mb-2" placeholder="Completion / return remarks" required></textarea>
				                            <button class="btn btn-success w-100" type="submit" @disabled(!$canForwardTicketToSenior)>
			                                <i class="ti ti-arrow-back-up me-1"></i>
				                                Return to Senior Developer
				                            </button>
		                        </form>
	                    @else
	                        @if (!$showStatusUpdateForm && !$canUserReturnToState && !$canReopenTicket)
	                            <p class="text-muted mb-0">No action pending for your role.</p>
	                        @endif
	                    @endif
                </div>

	            </div>
        </div>
    </div>
@endif

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
		    $(function () {
		        var successMessage = @json(session('success'));
		        var tableId = '#freshHelpdeskTicketTable';
		        var table = null;
		        var watchedTicketToken = @json($selectedTicket ? \App\Models\FreshHelpdesk::ticketUrlToken($selectedTicket->id) : null);

	        function checkAutoForwardStaleTickets() {
	            $.ajax({
	                url: '{{ route('fresh-helpdesk.tickets.auto-forward-check') }}',
	                method: 'POST',
	                data: watchedTicketToken ? { ticket: watchedTicketToken } : {},
	                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	            }).done(function (response) {
	                if (response && response.current_ticket_forwarded) {
	                    window.location.reload();
	                }
	            });
	        }
		        // Auto-forward is handled by the scheduler; avoid moving tickets immediately after a status-only update.
		        // setInterval(checkAutoForwardStaleTickets, 30000);

	        function adjustTicketTable() {
	            if (!table) {
	                return;
	            }

	            setTimeout(function () {
	                table.columns.adjust();
	            }, 30);
            setTimeout(function () {
	                table.columns.adjust();
	            }, 180);
	        }

		        function initTicketDataTable() {
		            if (!$(tableId).length) {
		                return;
		            }

		            if ($.fn.DataTable.isDataTable(tableId)) {
		                $(tableId).DataTable().clear().destroy();
		            }

	            table = $(tableId).DataTable({
	                processing: true,
		                serverSide: false,
		                lengthChange: false,
		                paging: false,
		                info: false,
		                pageLength: 10,
		                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
		                autoWidth: false,
		                dom: 'rt',
		                order: [[8, 'desc']],
	                scrollX: false,
	                scrollCollapse: false,
	                initComplete: function () {
	                    adjustTicketTable();
	                },
			                drawCallback: function () {
			                    adjustTicketTable();
			                }
			            });
			        }

		        initTicketDataTable();

		        $('#freshTicketFilterForm').on('submit', function (event) {
		            event.preventDefault();

		            var $form = $(this);
		            var $button = $('#freshTicketFilterBtn');

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
		                $('#freshTicketPaginationWrap').html(response.pagination_html || '');
		                $('.fh-filter-count').text(response.count || 0);
		                if (response.download_url) {
		                    $('#freshTicketDownloadBtn').attr('href', response.download_url);
		                }
		                initTicketDataTable();
		            }).fail(function () {
		                alert('Unable to filter tickets. Please try again.');
		            }).always(function () {
		                $form.data('loading', false);
		                $button.prop('disabled', false);
		            });
		        });

		        $('#toggleTicketCommentForm').on('click', function () {
		            var form = $('#ticketTimelineCommentForm');
		            form.toggleClass('is-hidden');
		            if (!form.hasClass('is-hidden')) {
		                form.find('textarea[name="remarks"]').trigger('focus');
		            }
		        });

		        function initFreshActionLoaders(selector) {
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

		                if ($submitButton.attr('name') && !$form.find('input[type="hidden"][data-fh-submit-copy="' + $submitButton.attr('name') + '"]').length) {
		                    $('<input>', {
		                        type: 'hidden',
		                        name: $submitButton.attr('name'),
		                        value: $submitButton.val(),
		                        'data-fh-submit-copy': $submitButton.attr('name')
		                    }).appendTo($form);
		                }

		                var loadingText = $submitButton.data('loadingText') || 'Sending...';
		                $form
		                    .data('submitting', true)
		                    .addClass('fh-action-form is-submitting');

		                $form.find(':submit').prop('disabled', true);
		                $submitButton
		                    .addClass('fh-submit-button is-submitting')
		                    .html('<span class="fh-submit-spinner me-1" aria-hidden="true"></span>' + loadingText);
		            });
		        }

		        initFreshActionLoaders('#ticketTimelineCommentForm, .fh-side-panel form');

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
