@extends('index2')

@section('title', 'Helpdesk Ticket')

@section('content')
    @include('tickets.partials.app-theme')
    @include('tickets.partials.styles')
    @include('common.alert')
    @if (session('warning'))
        <div class="alert alert-warning mx-3 mt-3 mb-0">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mx-3 mt-3 mb-0">
            {{ session('error') }}
        </div>
    @endif
    @php
        $visibleComments = $ticket->comments->where('is_internal', false);
        $adminInternalComments = ($canViewAdminInternalNotes ?? false)
            ? $ticket->comments
                ->where('is_internal', true)
                ->filter(function ($comment) {
                    return in_array(($comment->user_role ?? null), ['StateAdmin', 'NIC Admin'], true);
                })
                ->map(function ($comment) {
                    $comment->comment_stream_badge = ($comment->user_role ?? null) === 'NIC Admin'
                        ? 'NIC Admin'
                        : 'DG Admin';

                    return $comment;
                })
            : collect();
        $visibleComments = $visibleComments
            ->concat($adminInternalComments)
            ->sortByDesc('created_at')
            ->values();
        $visibleDevComments = $canViewInternalNotes ? $ticket->devComments : collect();
        $developerInternalComments = $visibleDevComments
            ->filter(function ($comment) {
                if (($comment->user_role ?? null) === 'NIC Admin') {
                    return true;
                }

                return ($comment->user_role ?? null) === 'Tech Team';
	            })
	            ->map(function ($comment) {
	                $isWatchlistComment = \Illuminate\Support\Str::startsWith((string) $comment->comment, '[Watchlist] ');
	                $comment->internal_filter_role = 'developer';
	                $comment->internal_filter_badge = $isWatchlistComment
	                    ? 'Watchlist'
	                    : (($comment->user_role ?? null) === 'NIC Admin'
	                    ? 'NIC Admin'
	                    : 'Developer');
	                $comment->display_comment = $isWatchlistComment
	                    ? \Illuminate\Support\Str::after((string) $comment->comment, '[Watchlist] ')
	                    : $comment->comment;

	                return $comment;
	            });
        $filteredInternalComments = $developerInternalComments
            ->sortByDesc('created_at')
            ->values();
        $isOwner = \App\Support\HelpdeskSession::userId() === $ticket->cams_userid;
        $isStaff = \App\Support\HelpdeskSession::isStaff();
        $isDeveloper = \App\Support\HelpdeskSession::isDeveloper();
	        $isDepartmentAdmin = \App\Support\HelpdeskSession::isDepartmentAdmin();
	        $isSuperAdmin = \App\Support\HelpdeskSession::isSuperAdmin();
	        $isNicAdmin = \App\Support\HelpdeskSession::isNicAdmin();
	        $isAdditionalLayerDeveloper = $isAdditionalLayerDeveloper ?? false;
        $isAdditionalLayerHandler = $isAdditionalLayerHandler ?? false;
        $canAssignDeveloper = $canAssignDeveloper ?? $isNicAdmin;
        $sendBackTargetLabel = $sendBackTargetLabel ?? 'NIC Admin';
	        $additionalLayerDevelopers = $additionalLayerDevelopers ?? collect();
	        $watchlistDevelopers = $watchlistDevelopers ?? collect();
	        $ticketWatchers = $ticketWatchers ?? collect();
	        $assignedDeveloperVisible = $isNicAdmin || ($isAssignedDeveloper ?? false) || $isAdditionalLayerHandler;
	        $isWatchedDeveloper = $isWatchedDeveloper ?? false;
	        $canViewInternalConversation = $isNicAdmin || ($isAssignedDeveloper ?? false) || $isAdditionalLayerHandler || $isWatchedDeveloper;
	        $assignmentHistoryRows = $ticket->assignments
	            ->reject(function ($assignment) {
	                return ($assignment->status ?? null) === 'watchlist';
	            })
	            ->values();
	        $assignmentHistoryVisible = $assignedDeveloperVisible && $assignmentHistoryRows->isNotEmpty();
        $activityCount = $visibleComments->count()
            + ($canViewInternalConversation ? $filteredInternalComments->count() : 0)
            + ($assignmentHistoryVisible ? $ticket->assignments->count() : 0);
	        $ticketAssignedToDeveloper = !empty($ticket->assigned_to_userid);
	        $normalizedForwardedRole = $ticket->normalizedForwardedRole();
        $currentRole = \App\Support\HelpdeskSession::role();
        $pendingWithLabel = $ticket->currentHolderLabel();
        $stateAdminWaitingForReturn = $isSuperAdmin && !empty($normalizedForwardedRole) && $normalizedForwardedRole !== 'stateadmin';
        $nicAdminWaitingForReturn = $isNicAdmin && $normalizedForwardedRole !== 'nicadmin';
        $canUpdateStatus = !$stateAdminWaitingForReturn && !($isDeveloper && $developerStatusLocked);
        $showStatusCard = $isStaff
            && !in_array($ticket->status, ['closed', 'resolved'])
            && !($isDeveloper && (!$ticket->isDeveloperStage() || $ticket->assigned_to_userid !== \App\Support\HelpdeskSession::userId()))
            && !($isNicAdmin && $nicAdminWaitingForReturn);
        $canForwardTicket = !($isSuperAdmin && $stateAdminWaitingForReturn);
	        $nicAdminAssignmentLocked = $isNicAdmin && !empty($ticket->assigned_to_userid) && $ticket->isDeveloperStage();
	        $assignToDeveloperLocked = $nicAdminAssignmentLocked;
        $statusDisplayValue = $ticket->status;
        if (($isNicAdmin || $isDeveloper) && !empty($techTeamStatus) && $techTeamStatus !== '-') {
            $statusDisplayValue = $techTeamStatus;
        }
        if ($isDeveloper && $developerStatusLocked) {
            $statusDisplayValue = $lockedStatusSelection;
        }
        $canComment = !in_array($ticket->status, ['resolved', 'closed'], true);
        $priorityDescriptions = [
            'low' => 'Best for minor issues or general requests that do not block your work.',
            'medium' => 'Use this for normal issues that should be handled soon but are not urgent.',
            'high' => 'Choose this when the issue is affecting important work and needs quick attention.',
            'urgent' => 'Use only for critical problems that stop work completely or need immediate action.',
        ];
        $priorityDescription = $priorityDescriptions[$ticket->priority] ?? 'Priority helps the team understand how quickly this ticket needs attention.';

        // if (empty($ticket->forwarded_to_role)) {
        //     $canComment = $isOwner || $isStaff;
        // } elseif ($ticket->forwarded_to_role === 'superadmin') {
        //     $canComment = $isSuperAdmin;
        // } elseif ($ticket->forwarded_to_role === 'developer') {
        //     $canComment = $isDeveloper;
        // } elseif ($ticket->forwarded_to_role === 'department_admin') {
        //     $canComment = $isDepartmentAdmin;
        // } else {
        //     $canComment = $isOwner && !$isStaff;
        // }
    @endphp
    <style>
        .helpdesk-ticket-overview {
            background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 18px 20px;
            margin-bottom: 22px;
        }

        .helpdesk-ticket-overview h6 {
            margin: 0 0 6px;
            color: #1e3a8a;
            font-size: 1rem;
            font-weight: 700;
        }

        .helpdesk-ticket-overview p {
            margin: 0;
            color: #475569;
        }

        .helpdesk-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .helpdesk-detail-item {
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
        }

        .helpdesk-detail-label {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .helpdesk-ticket-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .helpdesk-ticket-header h5 {
            color: #fff;
        }

        .helpdesk-ticket-header-right {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .helpdesk-ticket-header-right .helpdesk-detail-label {
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.85);
        }

        .helpdesk-detail-value {
            color: #0f172a;
            font-weight: 600;
        }

        .helpdesk-priority-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 22px;
        }

        .helpdesk-priority-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .helpdesk-priority-description {
            margin: 0;
            color: #475569;
        }

        .helpdesk-description-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #fcfdff;
            padding: 18px;
        }

        .helpdesk-description-card strong {
            display: block;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .helpdesk-description-card p {
            margin: 0;
            color: #334155;
            line-height: 1.65;
            white-space: pre-line;
        }

        .ticket-focus-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .ticket-focus-item {
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #f8fbff;
            padding: 13px 15px;
        }

        .ticket-focus-label {
            display: block;
            margin-bottom: 5px;
            color: #5b728c;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .ticket-focus-value {
            color: #0f2f52;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .ticket-detail-card {
            min-height: 76px;
            border-color: #d9e3ef;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .ticket-conversation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .conversation-card {
            position: relative;
            border: 1px solid #d9e3ef;
            border-left: 4px solid #2f86d3;
            border-radius: 12px;
            background: #ffffff;
            padding: 14px 16px 14px 58px;
            margin-bottom: 12px;
        }

        .conversation-card.internal {
            border-color: #fde68a;
            border-left-color: #f59e0b;
            background: #fffdf5;
        }

        .conversation-number {
            position: absolute;
            top: 14px;
            left: 14px;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2f86d3;
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .conversation-card.internal .conversation-number {
            background: #f59e0b;
            color: #111827;
        }

        .conversation-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .conversation-message {
            margin-top: 10px;
            color: #1f2937;
            line-height: 1.55;
            white-space: pre-line;
        }

	        .assignment-helper {
	            margin: -4px 0 12px;
	            color: #64748b;
	            font-size: 0.85rem;
	        }

	        .helpdesk-app-theme .helpdesk-main-content {
	            background: #eef4fa;
	            border-radius: 14px;
	            box-shadow: none;
	            padding: 18px;
	        }

	        .helpdesk-shell {
	            padding: 0;
	        }

	        .helpdesk-hero {
	            position: relative;
	            overflow: hidden;
	            background: #ffffff;
	            color: #10233f;
	            border: 1px solid #d8e4f0;
	            border-left: 8px solid #1d73be;
	            border-radius: 10px;
	            padding: 20px 24px;
	            box-shadow: 0 10px 24px rgba(32, 78, 125, 0.08);
	        }

	        .helpdesk-hero::after {
	            content: '';
	            position: absolute;
	            top: 0;
	            right: 0;
	            width: 32%;
	            height: 100%;
	            background: linear-gradient(135deg, rgba(29, 115, 190, 0.10), rgba(27, 162, 198, 0.18));
	            pointer-events: none;
	        }

	        .helpdesk-kicker {
	            background: #163f68;
	            color: #ffffff;
	            border-radius: 6px;
	            letter-spacing: 0;
	            text-transform: none;
	        }

	        .helpdesk-hero h2 {
	            color: #10233f;
	            font-size: 1.55rem;
	            line-height: 1.25;
	        }

	        .helpdesk-hero p {
	            color: #4d6480;
	            opacity: 1;
	            font-weight: 700;
	        }

	        .helpdesk-actions {
	            position: relative;
	            z-index: 1;
	        }

	        .helpdesk-actions .btn {
	            border: 1px solid #cbd9e8;
	            color: #163f68;
	            font-weight: 800;
	            box-shadow: 0 6px 14px rgba(15, 45, 78, 0.08);
	        }

	        .ticket-focus-strip {
	            grid-template-columns: repeat(4, minmax(0, 1fr));
	            gap: 0;
	            overflow: hidden;
	            border: 1px solid #163f68;
	            border-radius: 10px;
	            background: #163f68;
	            box-shadow: 0 10px 24px rgba(22, 63, 104, 0.14);
	        }

	        .ticket-focus-item {
	            border: 0;
	            border-right: 1px solid rgba(255, 255, 255, 0.15);
	            border-radius: 0;
	            background: transparent;
	            padding: 16px 18px;
	        }

	        .ticket-focus-item:last-child {
	            border-right: 0;
	        }

	        .ticket-focus-label {
	            color: #b9d6ef;
	            letter-spacing: 0;
	        }

	        .ticket-focus-value {
	            color: #ffffff;
	        }

	        .ticket-case-layout {
	            display: grid;
	            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
	            align-items: flex-start;
	        }

	        .ticket-case-layout > [class*="col-"] {
	            width: auto;
	            max-width: none;
	        }

	        .ticket-action-column {
	            position: sticky;
	            top: 12px;
	        }

	        .ticket-detail-panel,
	        .helpdesk-card {
	            border: 1px solid #d8e4f0;
	            border-radius: 10px;
	            box-shadow: 0 8px 18px rgba(32, 78, 125, 0.07);
	            overflow: hidden;
	        }

	        .ticket-detail-panel > .card-header,
	        .helpdesk-card .card-header {
	            background: #ffffff;
	            border-bottom: 1px solid #d8e4f0;
	            padding: 14px 18px;
	        }

	        .ticket-detail-panel .card-body,
	        .helpdesk-card .card-body {
	            padding: 18px;
	        }

	        .helpdesk-ticket-header h5,
	        .helpdesk-card .card-header h5,
	        .helpdesk-card .card-header h6 {
	            color: #10233f;
	            font-weight: 800;
	        }

	        .helpdesk-ticket-header-right {
	            background: #fff5e6;
	            border: 1px solid #ffd699;
	            border-radius: 999px;
	            padding: 5px 10px;
	        }

	        .helpdesk-ticket-header-right .helpdesk-detail-label {
	            color: #915b00;
	            font-size: 0.72rem;
	        }

	        .helpdesk-detail-grid {
	            grid-template-columns: repeat(3, minmax(0, 1fr));
	            gap: 10px;
	        }

	        .ticket-detail-card {
	            min-height: 86px;
	            border-radius: 8px;
	            border-color: #d8e4f0;
	            background: #ffffff;
	            box-shadow: inset 4px 0 0 #d8e4f0;
	        }

	        .ticket-detail-card:hover {
	            border-color: #9dc5e8;
	            box-shadow: inset 4px 0 0 #2f86d3;
	        }

	        .helpdesk-detail-label {
	            letter-spacing: 0;
	            color: #5e748f;
	            font-size: 0.76rem;
	        }

	        .helpdesk-description-card {
	            border-radius: 8px;
	            background: #f7fbff;
	            border-color: #cfe0f2;
	        }

	        .conversation-card {
	            margin-left: 15px;
	            border: 0;
	            border-left: 2px solid #b8d4ee;
	            border-radius: 0 10px 10px 0;
	            background: #f9fcff;
	            padding: 14px 16px 14px 42px;
	        }

	        .conversation-card.internal {
	            border-left-color: #f59e0b;
	            background: #fffaf0;
	        }

	        .conversation-number {
	            left: -16px;
	            top: 13px;
	            border: 3px solid #ffffff;
	            box-shadow: 0 4px 12px rgba(47, 134, 211, 0.22);
	        }

	        .internal-filter-bar {
	            border-radius: 8px;
	            background: #fff8eb;
	        }

	        .ticket-action-column .helpdesk-card {
	            border-radius: 10px;
	        }

	        .ticket-action-column .btn {
	            border-radius: 8px;
	            font-weight: 800;
	        }

		        .conversation-launch-grid {
		            display: grid;
		            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
		            gap: 14px;
		            margin-bottom: 18px;
		        }

	        .conversation-launch-card {
	            border: 1px solid #d8e4f0;
	            border-radius: 10px;
	            background: #f8fbff;
	            padding: 16px;
	        }

		        .conversation-launch-card.internal {
		            border-color: #fde2a3;
		            background: #fffaf0;
		        }

		        .conversation-launch-card.assignment {
		            border-color: #cbd5e1;
		            background: #f8fafc;
		        }

	        .conversation-launch-count {
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            min-width: 34px;
	            height: 28px;
	            border-radius: 999px;
	            background: #163f68;
	            color: #ffffff;
	            font-weight: 800;
	        }

		        .conversation-launch-card.internal .conversation-launch-count {
		            background: #f59e0b;
		            color: #111827;
		        }

		        .conversation-launch-card.assignment .conversation-launch-count,
		        .conversation-card.assignment-history .conversation-number {
		            background: #475569;
		            color: #ffffff;
		        }

		        .conversation-card.assignment-history {
		            border-left-color: #475569;
		            background: #ffffff;
		        }

	        .conversation-modal .modal-dialog {
	            max-width: 920px;
	        }

	        .conversation-modal {
	            z-index: 10000001;
	        }

	        .modal-backdrop {
	            z-index: 10000000;
	        }

	        .conversation-modal .modal-content {
	            border: 0;
	            border-radius: 12px;
	            overflow: hidden;
	        }

	        .conversation-modal .modal-header {
	            background: #163f68;
	            color: #ffffff;
	        }

	        .conversation-modal .modal-title {
	            color: #ffffff;
	            font-weight: 800;
	        }

	        .conversation-modal .btn-close {
	            filter: invert(1);
	        }

	        .conversation-modal .modal-body {
	            max-height: 68vh;
	            overflow-y: auto;
	            background: #f3f7fb;
	            padding: 20px;
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

        .internal-filter-bar {
            display: flex;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
            padding: 12px 14px;
            margin-bottom: 16px;
            border: 1px solid #fde68a;
            border-radius: 14px;
            background: #fffdf5;
        }

        .internal-filter-hint {
            margin: 0 0 14px;
            color: #92400e;
            font-size: 0.92rem;
        }

        .internal-comment-card[hidden] {
            display: none !important;
        }

	        @media (max-width: 768px) {
	            .helpdesk-detail-grid {
	                grid-template-columns: 1fr;
	            }

	            .ticket-focus-strip {
	                grid-template-columns: 1fr;
	            }

	            .ticket-focus-item {
	                border-right: 0;
	                border-bottom: 1px solid rgba(255, 255, 255, 0.15);
	            }

	            .ticket-case-layout {
	                display: block;
	            }

		            .ticket-action-column {
		                position: static;
		            }
		        }

	        .ticket-redesign {
	            color: #10233f;
	        }

	        .ticket-redesign .helpdesk-main-content {
	            background: #eef4fa;
	            border-radius: 14px;
	            padding: 18px;
	        }

	        .ticket-redesign .helpdesk-shell {
	            padding: 0;
	        }

	        .ticket-redesign .ticket-page-hero {
	            display: grid;
	            grid-template-columns: minmax(0, 1fr) auto;
	            gap: 18px;
	            align-items: center;
	            padding: 18px 20px;
	            margin-bottom: 16px;
	            border: 1px solid #cfe0f2;
	            border-left: 8px solid #1d73be;
	            border-radius: 10px;
	            background: linear-gradient(135deg, #ffffff 0%, #f3f9ff 100%);
	            box-shadow: 0 10px 24px rgba(32, 78, 125, 0.08);
	        }

	        .ticket-redesign .ticket-page-hero h2 {
	            margin: 8px 0 10px;
	            color: #10233f;
	            font-size: 1.45rem;
	            line-height: 1.28;
	            font-weight: 900;
	        }

	        .ticket-redesign .ticket-page-hero p {
	            margin: 0;
	            color: #49637f;
	            font-weight: 700;
	        }

	        .ticket-redesign .ticket-hero-meta {
	            display: flex;
	            align-items: center;
	            gap: 8px;
	            flex-wrap: wrap;
	        }

	        .ticket-redesign .ticket-kicker {
	            display: inline-flex;
	            align-items: center;
	            gap: 6px;
	            border-radius: 999px;
	            background: #163f68;
	            color: #ffffff;
	            padding: 6px 12px;
	            font-size: 0.78rem;
	            font-weight: 900;
	        }

	        .ticket-redesign .ticket-chip {
	            display: inline-flex;
	            align-items: center;
	            gap: 6px;
	            border-radius: 999px;
	            padding: 6px 10px;
	            border: 1px solid #cfe0f2;
	            background: #ffffff;
	            color: #24496d;
	            font-size: 0.78rem;
	            font-weight: 800;
	        }

	        .ticket-redesign .ticket-chip.status {
	            background: #e8f3ff;
	            color: #165b97;
	            border-color: #b9d9f5;
	        }

	        .ticket-redesign .ticket-chip.watchlist {
	            background: #174a7c;
	            color: #ffffff;
	            border-color: #174a7c;
	        }

	        .ticket-redesign .ticket-back-btn {
	            border: 1px solid #bcd3e8;
	            background: #ffffff;
	            color: #163f68;
	            border-radius: 8px;
	            font-weight: 900;
	            padding: 9px 16px;
	        }

	        .ticket-redesign .ticket-summary-grid {
	            display: grid;
	            grid-template-columns: repeat(4, minmax(0, 1fr));
	            gap: 12px;
	            margin-bottom: 16px;
	        }

	        .ticket-redesign .ticket-summary-card {
	            position: relative;
	            min-height: 96px;
	            padding: 16px 16px 14px 18px;
	            border: 1px solid #d4e3f2;
	            border-radius: 10px;
	            background: #ffffff;
	            box-shadow: 0 8px 18px rgba(32, 78, 125, 0.06);
	            overflow: hidden;
	        }

	        .ticket-redesign .ticket-summary-card::before {
	            content: '';
	            position: absolute;
	            inset: 0 auto 0 0;
	            width: 5px;
	            background: #2f86d3;
	        }

	        .ticket-redesign .ticket-summary-card.priority::before {
	            background: #f59e0b;
	        }

	        .ticket-redesign .ticket-summary-card.assignment::before {
	            background: #475569;
	        }

	        .ticket-redesign .ticket-summary-card.time::before {
	            background: #0f9f7a;
	        }

	        .ticket-redesign .ticket-summary-icon {
	            width: 34px;
	            height: 34px;
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            margin-bottom: 10px;
	            border-radius: 8px;
	            background: #e8f3ff;
	            color: #1d73be;
	            font-size: 1rem;
	        }

	        .ticket-redesign .ticket-summary-label {
	            display: block;
	            color: #617892;
	            font-size: 0.74rem;
	            font-weight: 900;
	            text-transform: uppercase;
	        }

	        .ticket-redesign .ticket-summary-value {
	            margin-top: 5px;
	            color: #0f172a;
	            font-size: 0.98rem;
	            font-weight: 900;
	            overflow-wrap: anywhere;
	        }

	        .ticket-redesign .ticket-workspace {
	            display: grid;
	            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
	            gap: 16px;
	            align-items: flex-start;
	        }

	        .ticket-redesign .ticket-main-column,
	        .ticket-redesign .ticket-action-column {
	            min-width: 0;
	        }

	        .ticket-redesign .ticket-action-column {
	            position: sticky;
	            top: 12px;
	        }

	        .ticket-redesign .ticket-panel {
	            border: 1px solid #d4e3f2;
	            border-radius: 10px;
	            background: #ffffff;
	            box-shadow: 0 8px 18px rgba(32, 78, 125, 0.06);
	            overflow: hidden;
	        }

	        .ticket-redesign .ticket-panel + .ticket-panel,
	        .ticket-redesign .ticket-panel + .alert,
	        .ticket-redesign .alert + .ticket-panel {
	            margin-top: 14px;
	        }

	        .ticket-redesign .ticket-panel-header {
	            display: flex;
	            align-items: center;
	            justify-content: space-between;
	            gap: 12px;
	            padding: 14px 18px;
	            border-bottom: 1px solid #dbe7f3;
	            background: #f8fbff;
	        }

	        .ticket-redesign .ticket-panel-header h5 {
	            margin: 0;
	            color: #10233f;
	            font-size: 1rem;
	            font-weight: 900;
	        }

	        .ticket-redesign .ticket-panel-body {
	            padding: 18px;
	        }

	        .ticket-redesign .helpdesk-detail-grid {
	            grid-template-columns: repeat(3, minmax(0, 1fr));
	            gap: 0;
	            margin-bottom: 14px;
	            border: 1px solid #d7e4f2;
	            border-radius: 8px;
	            background: #ffffff;
	            overflow: hidden;
	        }

	        .ticket-redesign .ticket-detail-card {
	            min-height: 0;
	            padding: 10px 12px;
	            border: 0;
	            border-right: 1px solid #d7e4f2;
	            border-bottom: 1px solid #d7e4f2;
	            border-radius: 0;
	            background: #ffffff;
	            box-shadow: none;
	        }

	        .ticket-redesign .ticket-detail-card:nth-child(3n) {
	            border-right: 0;
	        }

	        .ticket-redesign .ticket-detail-card:nth-last-child(-n + 3) {
	            border-bottom: 0;
	        }

	        .ticket-redesign .helpdesk-detail-label {
	            margin-bottom: 4px;
	            letter-spacing: 0;
	            color: #617892;
	            font-size: 0.7rem;
	            font-weight: 900;
	        }

	        .ticket-redesign .helpdesk-detail-value {
	            color: #0f172a;
	            font-size: 0.88rem;
	            font-weight: 800;
	            overflow-wrap: anywhere;
	        }

	        .ticket-redesign .helpdesk-description-card {
	            padding: 12px 14px;
	            border-radius: 8px;
	            border-color: #d7e4f2;
	            background: #ffffff;
	        }

	        .ticket-redesign .helpdesk-description-card strong {
	            margin-bottom: 6px;
	            font-size: 0.92rem;
	        }

	        .ticket-redesign .helpdesk-description-card p {
	            font-size: 0.9rem;
	            line-height: 1.5;
	        }

	        .ticket-redesign .attachment-list {
	            margin-bottom: 0;
	        }

	        .ticket-redesign .activity-grid {
	            display: grid;
	            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
	            gap: 12px;
	            margin-bottom: 16px;
	        }

	        .ticket-redesign .activity-card {
	            display: flex;
	            flex-direction: column;
	            gap: 12px;
	            min-height: 138px;
	            padding: 14px;
	            border: 1px solid #d8e4f0;
	            border-radius: 10px;
	            background: #f8fbff;
	        }

	        .ticket-redesign .activity-card.internal {
	            border-color: #f6d58f;
	            background: #fffaf0;
	        }

	        .ticket-redesign .activity-card.assignment {
	            border-color: #cbd5e1;
	            background: #f8fafc;
	        }

	        .ticket-redesign .activity-top {
	            display: flex;
	            align-items: center;
	            justify-content: space-between;
	            gap: 10px;
	        }

	        .ticket-redesign .activity-title {
	            display: flex;
	            align-items: center;
	            gap: 8px;
	            color: #10233f;
	            font-weight: 900;
	        }

	        .ticket-redesign .activity-icon {
	            width: 32px;
	            height: 32px;
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            border-radius: 8px;
	            background: #e8f3ff;
	            color: #1d73be;
	        }

	        .ticket-redesign .activity-card.internal .activity-icon {
	            background: #fff0c7;
	            color: #a15c00;
	        }

	        .ticket-redesign .activity-card.assignment .activity-icon {
	            background: #e2e8f0;
	            color: #475569;
	        }

	        .ticket-redesign .activity-count {
	            min-width: 32px;
	            height: 28px;
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            border-radius: 999px;
	            background: #163f68;
	            color: #ffffff;
	            font-weight: 900;
	        }

	        .ticket-redesign .activity-card.internal .activity-count {
	            background: #f59e0b;
	            color: #111827;
	        }

	        .ticket-redesign .activity-card.assignment .activity-count {
	            background: #475569;
	        }

	        .ticket-redesign .activity-card .btn {
	            margin-top: auto;
	            border-radius: 8px;
	            font-weight: 900;
	        }

	        .ticket-redesign .ticket-reply-panel textarea {
	            min-height: 110px;
	        }

	        .ticket-redesign .ticket-reply-panel {
	            padding-top: 16px;
	            border-top: 1px solid #dbe7f3;
	        }

	        .ticket-redesign .ticket-attachments {
	            margin-top: 12px;
	        }

	        .ticket-redesign .ticket-attachments strong {
	            display: block;
	            margin-bottom: 8px;
	            color: #10233f;
	            font-size: 0.92rem;
	            font-weight: 900;
	        }

	        .ticket-redesign .watchlist-badges {
	            display: flex;
	            flex-wrap: wrap;
	            gap: 6px;
	            margin-bottom: 12px;
	        }

	        .ticket-redesign .watchlist-badge {
	            display: inline-flex;
	            align-items: center;
	            gap: 5px;
	            padding: 5px 6px 5px 9px;
	            border-radius: 999px;
	            border: 1px solid #174a7c;
	            background: #174a7c;
	            color: #ffffff;
	            font-size: 0.78rem;
	            font-weight: 900;
	            box-shadow: 0 6px 14px rgba(23, 74, 124, 0.2);
	        }

	        .ticket-redesign .watchlist-remove-form {
	            display: inline-flex;
	            margin: 0;
	        }

	        .ticket-redesign .watchlist-remove-btn {
	            width: 19px;
	            height: 19px;
	            display: inline-flex;
	            align-items: center;
	            justify-content: center;
	            border: 0;
	            border-radius: 999px;
	            background: rgba(255, 255, 255, 0.22);
	            color: #ffffff;
	            font-size: 0.8rem;
	            line-height: 1;
	            padding: 0;
	        }

	        .ticket-redesign .watchlist-remove-btn:hover {
	            background: #ffffff;
	            color: #174a7c;
	        }

	        .ticket-redesign .watchlist-comment-badge {
	            display: inline-flex;
	            align-items: center;
	            gap: 4px;
	            width: fit-content;
	            padding: 5px 9px;
	            border: 1px solid #174a7c;
	            border-radius: 999px;
	            background: #174a7c;
	            color: #ffffff;
	            font-size: 0.7rem;
	            font-weight: 900;
	            letter-spacing: 0;
	            line-height: 1;
	            box-shadow: 0 6px 14px rgba(23, 74, 124, 0.24);
	        }

	        .ticket-redesign .internal-comment-badge {
	            display: inline-flex;
	            align-items: center;
	            width: fit-content;
	            padding: 5px 9px;
	            border-radius: 999px;
	            font-size: 0.7rem;
	            font-weight: 900;
	            letter-spacing: 0;
	            line-height: 1;
	        }

	        .ticket-redesign .conversation-author-line {
	            display: flex;
	            align-items: center;
	            gap: 8px;
	            flex-wrap: wrap;
	        }

	        .ticket-redesign .attachment-link {
	            padding: 9px 12px;
	            border-radius: 8px;
	        }

	        .ticket-redesign .ticket-action-column .ticket-panel-header {
	            background: #163f68;
	            color: #ffffff;
	        }

	        .ticket-redesign .ticket-action-column .ticket-panel-header h5 {
	            color: #ffffff;
	        }

	        .ticket-redesign .ticket-action-column .ticket-panel-body {
	            padding: 16px;
	        }

	        .ticket-redesign .ticket-action-column .form-select,
	        .ticket-redesign .ticket-action-column .form-control,
	        .ticket-redesign .ticket-reply-panel .form-control {
	            border-radius: 8px;
	            border-color: #cbd9e8;
	        }

	        .ticket-redesign .ticket-action-column .btn,
	        .ticket-redesign .ticket-reply-panel .btn {
	            border-radius: 8px;
	            font-weight: 900;
	        }

	        .ticket-redesign .conversation-modal .modal-content {
	            border-radius: 10px;
	        }

	        .ticket-redesign .conversation-modal:not(.show) {
	            display: none !important;
	        }

	        .ticket-redesign .conversation-modal.show {
	            display: block;
	            background: rgba(15, 23, 42, 0.45);
	        }

	        .ticket-redesign .conversation-modal .modal-header {
	            background: #163f68;
	        }

	        .ticket-redesign .conversation-modal .modal-body {
	            background: #f3f7fb;
	            padding: 16px 18px;
	        }

	        .ticket-redesign .internal-filter-bar {
	            display: flex;
	            justify-content: flex-end;
	            gap: 8px;
	            padding: 0;
	            margin: 0 0 14px;
	            border: 0;
	            border-radius: 0;
	            background: transparent;
	        }

	        .ticket-redesign .internal-filter-chip {
	            position: relative;
	            display: inline-flex;
	            align-items: center;
	            margin: 0;
	            cursor: pointer;
	        }

	        .ticket-redesign .internal-filter-chip input {
	            position: absolute;
	            opacity: 0;
	            pointer-events: none;
	        }

	        .ticket-redesign .internal-filter-chip span {
	            display: inline-flex;
	            align-items: center;
	            gap: 7px;
	            min-height: 34px;
	            padding: 7px 12px;
	            border: 1px solid #cbd9e8;
	            border-radius: 999px;
	            background: #ffffff;
	            color: #163f68;
	            font-size: 0.82rem;
	            font-weight: 900;
	        }

	        .ticket-redesign .internal-filter-chip span::before {
	            content: '';
	            width: 8px;
	            height: 8px;
	            border-radius: 999px;
	            background: #94a3b8;
	        }

	        .ticket-redesign .internal-filter-chip input:checked + span {
	            border-color: #163f68;
	            background: #163f68;
	            color: #ffffff;
	        }

	        .ticket-redesign .internal-filter-chip input:checked + span::before {
	            background: #f59e0b;
	        }

	        .ticket-redesign .internal-filter-hint {
	            margin: 0 0 14px;
	            padding: 9px 12px;
	            border: 1px solid #dbe7f3;
	            border-radius: 8px;
	            background: #ffffff;
	            color: #64748b;
	            font-size: 0.84rem;
	        }

	        .ticket-redesign .conversation-card {
	            border: 0;
	            border-left: 3px solid #2f86d3;
	            border-radius: 0 10px 10px 0;
	            background: #ffffff;
	            margin-left: 15px;
	            padding: 14px 16px 14px 42px;
	        }

	        .ticket-redesign .conversation-card.internal {
	            border-left-color: #f59e0b;
	            background: #fffaf0;
	        }

	        .ticket-redesign .conversation-card.assignment-history {
	            border-left-color: #475569;
	            background: #ffffff;
	        }

	        @media (max-width: 1200px) {
	            .ticket-redesign .ticket-summary-grid {
	                grid-template-columns: repeat(2, minmax(0, 1fr));
	            }

	            .ticket-redesign .ticket-workspace {
	                grid-template-columns: 1fr;
	            }

	            .ticket-redesign .ticket-action-column {
	                position: static;
	            }

	            .ticket-redesign .helpdesk-detail-grid {
	                grid-template-columns: repeat(2, minmax(0, 1fr));
	            }

	            .ticket-redesign .ticket-detail-card:nth-child(3n) {
	                border-right: 1px solid #d7e4f2;
	            }

	            .ticket-redesign .ticket-detail-card:nth-child(2n) {
	                border-right: 0;
	            }
	        }

	        @media (max-width: 768px) {
	            .ticket-redesign .ticket-page-hero {
	                grid-template-columns: 1fr;
	            }

	            .ticket-redesign .ticket-summary-grid,
	            .ticket-redesign .helpdesk-detail-grid {
	                grid-template-columns: 1fr;
	            }

	            .ticket-redesign .ticket-detail-card,
	            .ticket-redesign .ticket-detail-card:nth-child(2n),
	            .ticket-redesign .ticket-detail-card:nth-child(3n) {
	                border-right: 0;
	                border-bottom: 1px solid #d7e4f2;
	            }
	        }
	    </style>
	    <div class="helpdesk-app-theme ticket-redesign">
        <div class="helpdesk-main-content">
    <div class="helpdesk-shell">
	        <div class="ticket-page-hero">
	            <div>
	                <div class="ticket-hero-meta">
	                    <span class="ticket-kicker"><i class="bi bi-ticket-detailed"></i>{{ $ticket->ticket_number }}</span>
	                    <span class="ticket-chip status"><i class="bi bi-circle-fill"></i>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
	                    <span class="ticket-chip"><i class="bi bi-building"></i>{{ $ticket->department_name ?: 'No department' }}</span>
	                    @if ($isWatchedDeveloper || ($isNicAdmin && $ticketWatchers->isNotEmpty()))
	                        <span class="ticket-chip watchlist"><i class="bi bi-eye"></i>Watchlist</span>
	                    @endif
	                </div>
	                <h2>{{ $ticket->subject }}</h2>
	                <p>{{ \App\Support\HelpdeskSession::normalizeUserName($ticket->user_name) }} | {{ $ticket->created_at->format('d/m/Y h:i A') }}</p>
	            </div>
	            <div class="helpdesk-actions">
	                <a href="{{ route('helpdesk.tickets.index') }}" class="btn ticket-back-btn"><i class="bi bi-arrow-left me-1"></i>Back</a>
	            </div>
	        </div>

	        <div class="ticket-workspace">
	            <div class="ticket-main-column">
		                <div class="ticket-panel ticket-detail-panel">
		                    <div class="ticket-panel-header">
	                                <div class="helpdesk-ticket-header">
	                                    <h5 class="mb-0">Ticket Details</h5>
	                                </div>
	                            </div>
		                    <div class="ticket-panel-body">
                            {{-- <div class="helpdesk-ticket-overview">
                                <h6>Ticket summary</h6>
                                <p>This section shows the key details, current priority, and the original issue description for this ticket.</p>
                            </div> --}}
		                        <div class="helpdesk-detail-grid">
		                                <div class="helpdesk-detail-item ticket-detail-card">
		                                    <span class="helpdesk-detail-label">Priority</span>
		                                    <div class="helpdesk-detail-value">
		                                        <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
		                                    </div>
		                                </div>
		                                <div class="helpdesk-detail-item ticket-detail-card">
		                                    <span class="helpdesk-detail-label">Pending With</span>
		                                    <div class="helpdesk-detail-value">{{ $pendingWithLabel }}</div>
		                                </div>
		                                <div class="helpdesk-detail-item ticket-detail-card">
		                                    <span class="helpdesk-detail-label">Assigned Developer</span>
		                                    <div class="helpdesk-detail-value">{{ $ticket->assigned_to_name ?: '-' }}</div>
		                                </div>
		                                <div class="helpdesk-detail-item ticket-detail-card">
	                                    <span class="helpdesk-detail-label">Created By</span>
	                                    <div class="helpdesk-detail-value">{{ \App\Support\HelpdeskSession::normalizeUserName($ticket->user_name) }}</div>
                                </div>
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Financial Year</span>
                                    <div class="helpdesk-detail-value">{{ $financialYearLabel ?: ($ticket->financialyearcode ?: '-') }}</div>
                                </div>
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Audit Quarter</span>
                                    <div class="helpdesk-detail-value">{{ $planName ?: ($ticket->planmappingid ?: '-') }}</div>
                                </div>
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Institution</span>
                                    <div class="helpdesk-detail-value">{{ $ticket->institution ?: '-' }}</div>
                                </div>
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Category</span>
                                    <div class="helpdesk-detail-value">{{ $ticket->category }}</div>
                                </div>
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Request Type</span>
                                    <div class="helpdesk-detail-value">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ticket->request_type ?? '-')) }}</div>
                                </div>
		                                <div class="helpdesk-detail-item ticket-detail-card">
	                                    <span class="helpdesk-detail-label">Date of Creation</span>
	                                    <div class="helpdesk-detail-value">{{ $ticket->created_at->format('d/m/Y h:i A') }}</div>
	                                </div>
		                                <div class="helpdesk-detail-item ticket-detail-card">
		                                    <span class="helpdesk-detail-label">Last Updated</span>
		                                    <div class="helpdesk-detail-value">{{ $ticket->updated_at->format('d/m/Y h:i A') }}</div>
		                                </div>
			                            @if ($isNicAdmin || $isDeveloper)
	                                    <div class="helpdesk-detail-item ticket-detail-card">
	                                        <span class="helpdesk-detail-label">Tech Team Status</span>
                                        <div class="helpdesk-detail-value">{{ $techTeamStatus !== '-' ? ucfirst(str_replace('_', ' ', $techTeamStatus)) : '-' }}</div>
                                    </div>
	                            @endif
	                                <div class="helpdesk-detail-item ticket-detail-card">
                                    <span class="helpdesk-detail-label">Forwarded To</span>
                                    <div class="helpdesk-detail-value">{{ match($normalizedForwardedRole) {
                                        'stateadmin' => 'StateAdmin',
                                        'nicadmin' => 'NIC Admin',
                                        'developer' => 'Tech Team',
                                        default => $normalizedForwardedRole ? ucfirst($normalizedForwardedRole) : '-',
                                    } }}</div>
                                </div>
		                        </div>
	                            {{-- <div class="helpdesk-priority-card">
                                <div class="helpdesk-priority-meta">

                                </div>
                                <p class="helpdesk-priority-description">{{ $priorityDescription }}</p>
                            </div> --}}
	                        <div class="helpdesk-description-card">
	                            <strong>Description</strong>
	                            <p>{{ $ticket->description }}</p>
	                        </div>
		                        @if ($ticket->forward_notes)
		                            <div class="alert alert-warning">{{ $ticket->forward_notes }}</div>
		                        @endif
	                        @if (!empty($ticket->attachments))
	                            <div class="ticket-attachments">
                                <strong>Attachments</strong>
                                <ul class="attachment-list">
                                    @foreach ($ticket->attachments as $attachment)
                                        <li>
                                            <a class="attachment-link" href="{{ asset($attachment['path']) }}" target="_blank">
                                                <span class="attachment-meta">
                                                    <span class="attachment-icon">
                                                        <i class="bi bi-paperclip"></i>
                                                    </span>
                                                    <span class="attachment-name">{{ $attachment['name'] }}</span>
                                                </span>
                                                {{-- <span class="text-muted small">Open</span> --}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
	                    </div>
	                </div>

			                <div class="ticket-panel ticket-activity-panel">
			                    <div class="ticket-panel-header">
		                        <div class="ticket-conversation-header mb-0">
		                            <h5 class="mb-0 text-dark">Ticket Activity</h5>
		                            <span class="badge text-bg-primary">{{ $activityCount }}</span>
		                        </div>
			                    </div>
		                    <div class="ticket-panel-body">
			                        <div class="activity-grid">
			                            <div class="activity-card">
			                                <div class="activity-top">
			                                    <span class="activity-title"><span class="activity-icon"><i class="bi bi-chat-left-text"></i></span>Public Conversation</span>
			                                    <span class="activity-count">{{ $visibleComments->count() }}</span>
			                                </div>
		                                <button type="button" class="btn btn-outline-primary w-100 js-conversation-modal-open" data-conversation-modal="#publicConversationModal">
		                                    View Conversation
		                                </button>
		                            </div>
			                            @if ($canViewInternalConversation)
			                                <div class="activity-card internal">
			                                    <div class="activity-top">
			                                        <span class="activity-title"><span class="activity-icon"><i class="bi bi-shield-lock"></i></span>Internal Conversation</span>
			                                        <span class="activity-count">{{ $filteredInternalComments->count() }}</span>
			                                    </div>
		                                    <button type="button" class="btn btn-outline-warning w-100 js-conversation-modal-open" data-conversation-modal="#internalConversationModal">
		                                        View Internal
		                                    </button>
			                                </div>
			                            @endif
				                            @if ($assignmentHistoryVisible)
				                                <div class="activity-card assignment">
				                                    <div class="activity-top">
				                                        <span class="activity-title"><span class="activity-icon"><i class="bi bi-diagram-3"></i></span>Assignment History</span>
				                                        <span class="activity-count">{{ $assignmentHistoryRows->count() }}</span>
				                                    </div>
			                                    <button type="button" class="btn btn-outline-secondary w-100 js-conversation-modal-open" data-conversation-modal="#assignmentHistoryModal">
			                                        View History
			                                    </button>
			                                </div>
			                            @endif
			                        </div>

	                        @if ($canComment)
		                            <form action="{{ route('helpdesk.tickets.comments.store', $ticket) }}" method="POST" id="helpdesk-comment-form" class="ticket-reply-panel">
                                @csrf
                                <input type="hidden" name="comment_visibility" id="comment_visibility" value="{{ $isNicAdmin ? 'dg_internal' : 'public' }}">
                                <div class="mb-3">
                                    <label class="form-label">Add Comment</label>
                                    <textarea name="comment" rows="4" class="form-control" required></textarea>
                                </div>
                                @if ($isSuperAdmin)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="dg_admin_internal_note">
                                        <label class="form-check-label" for="dg_admin_internal_note">
                                            Internal note
                                        </label>
                                    </div>
                                @endif
                                    {{-- @if ($isDeveloper && (string) $ticket->assigned_to_userid === (string) \App\Support\HelpdeskSession::userId())
                                        <div class="alert alert-info py-2 px-3 small">
                                            Your reply will be visible only to NIC Admin by default.
                                        </div>
                                    @endif --}}
                                <button type="submit" class="btn btn-primary text-dark js-submit-loader" id="helpdesk-comment-submit" data-loading-text="Posting...">
                                    <span class="comment-submit-text">Post Comment</span>
                                    <span class="comment-submit-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                                        Posting...
                                    </span>
                                </button>
                            </form>
                        @else
                            <div class="text-muted">Ticket is {{ str_replace('_', ' ', $ticket->status) }}.</div>
                        @endif
                    </div>
                </div>

            </div>

		            <div class="ticket-action-column">
                @if ($showStatusCard)
	                    <div class="ticket-panel">
	                        <div class="ticket-panel-header"><h5 class="mb-0">Status</h5></div>
	                        <div class="ticket-panel-body">
                            @if ($canUpdateStatus)
                                <form action="{{ route('helpdesk.tickets.status', $ticket) }}" method="POST">
                                    @csrf
                            @endif
                                <div class="mb-3">
		                                    <select id="helpdesk-status-select" name="status" class="form-select" {{ $canUpdateStatus ? '' : 'disabled' }}>
	                                        @if ($canUpdateStatus)
	                                            @if ($isDeveloper)
	                                                <option value="in_progress" {{ $statusDisplayValue !== 'resolved' ? 'selected' : '' }}>In Progress</option>
	                                                <option value="resolved" {{ $statusDisplayValue === 'resolved' ? 'selected' : '' }}>Resolved</option>
	                                            @else
	                                                <option value="open" {{ $statusDisplayValue === 'open' ? 'selected' : '' }}>Open</option>
	                                                <option value="in_progress" {{ $statusDisplayValue === 'in_progress' ? 'selected' : '' }}>In Progress</option>
	                                                @if ($isNicAdmin)
	                                                    <option value="resolved" {{ $statusDisplayValue === 'resolved' ? 'selected' : '' }}>Resolved</option>
	                                                @else
	                                                    <option value="closed" {{ $statusDisplayValue === 'closed' ? 'selected' : '' }}>Closed</option>
	                                                @endif
	                                            @endif
	                                        @else
                                            <option value="{{ $statusDisplayValue }}" selected>{{ ucfirst(str_replace('_', ' ', $statusDisplayValue)) }}</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($isNicAdmin || $isDeveloper)
                                    <div class="alert alert-info py-2 px-3 small">
                                        Working-stage resolution will stay separate until StateAdmin confirms the final ticket status.
                                    </div>
                                @endif
	                                @if ($canUpdateStatus && !$isNicAdmin && !$isDeveloper)
	                                    <button type="submit" class="btn btn-primary text-dark w-100 js-submit-loader" data-loading-text="Updating...">Update Status</button>
	                                @elseif (!$canUpdateStatus)
	                                    <button type="button" class="btn btn-secondary w-100" disabled>Update Status</button>
	                                @endif
                            @if ($canUpdateStatus)
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($isSuperAdmin && !$canUpdateStatus && !in_array($ticket->status, ['closed', 'resolved']))
                    <div class="alert alert-info">
                        StateAdmin can change status or forward this ticket again only while the ticket is at the StateAdmin stage.
                    </div>
                @endif

	                @if ($isNicAdmin && $nicAdminWaitingForReturn && !in_array($ticket->status, ['closed', 'resolved']))
	                    <div class="alert alert-info">
	                        NIC Admin can update status or send this ticket back to StateAdmin only after the developer returns it to the NIC Admin stage.
	                    </div>
	                @endif

	                @if ($isNicAdmin && !in_array($ticket->status, ['closed', 'resolved']))
	                    <div class="ticket-panel">
	                        <div class="ticket-panel-header"><h5 class="mb-0">Watchlist</h5></div>
	                        <div class="ticket-panel-body">
	                            @if ($ticketWatchers->isNotEmpty())
	                                <div class="watchlist-badges">
	                                    @foreach ($ticketWatchers as $watcher)
	                                        <div class="watchlist-badge">
	                                            <i class="bi bi-eye"></i>{{ $watcher->developer_name ?: '-' }}
	                                            <form action="{{ route('helpdesk.tickets.watchlist.remove', $ticket) }}" method="POST" class="watchlist-remove-form">
	                                                @csrf
	                                                <input type="hidden" name="watchlist_userid" value="{{ $watcher->developer_userid }}">
	                                                <button type="submit" class="watchlist-remove-btn js-submit-loader" data-loading-text="..." title="Remove from watchlist" aria-label="Remove {{ $watcher->developer_name ?: 'developer' }} from watchlist">
	                                                    <i class="bi bi-x"></i>
	                                                </button>
	                                            </form>
	                                        </div>
	                                    @endforeach
	                                </div>
	                            @endif
	                            <form action="{{ route('helpdesk.tickets.watchlist', $ticket) }}" method="POST">
	                                @csrf
	                                <div class="mb-3">
	                                    <label class="form-label">Developer</label>
	                                    <select name="watchlist_userid" class="form-select" required {{ $watchlistDevelopers->isEmpty() ? 'disabled' : '' }}>
	                                        <option value="">Select Developer</option>
	                                        @foreach ($watchlistDevelopers as $developer)
	                                            <option value="{{ $developer->devuserid }}" {{ old('watchlist_userid') == $developer->devuserid ? 'selected' : '' }}>{{ $developer->devename }}{{ $developer->email ? ' - '.$developer->email : '' }}</option>
	                                        @endforeach
	                                    </select>
	                                    @if ($watchlistDevelopers->isEmpty())
	                                        <div class="text-muted small mt-2">No developer available for watchlist.</div>
	                                    @endif
	                                    @error('watchlist_userid')
	                                        <div class="text-danger small mt-1">{{ $message }}</div>
	                                    @enderror
	                                </div>
	                                <button type="submit" class="btn btn-outline-primary w-100 js-submit-loader" data-loading-text="Adding..." {{ $watchlistDevelopers->isEmpty() ? 'disabled' : '' }}>Add to Watchlist</button>
	                            </form>
	                        </div>
	                    </div>
	                @endif

		                @if (($isDepartmentAdmin || ($isSuperAdmin && $canForwardTicket) || ($canAssignDeveloper && ($normalizedForwardedRole === 'nicadmin' || ($isAdditionalLayerDeveloper && $ticket->isDeveloperStage())))) && !in_array($ticket->status, ['closed', 'resolved']))
			                    <div class="ticket-panel">
		                        <div class="ticket-panel-header"><h5 class="mb-0">{{ $canAssignDeveloper ? 'Assign to Developer' : 'Forward' }}</h5></div>
		                        <div class="ticket-panel-body">
	                            <form action="{{ route('helpdesk.tickets.forward', $ticket) }}" method="POST">
	                                @csrf
	                                @if ($canUpdateStatus)
	                                    <input type="hidden" name="status" class="js-forward-status-value" value="{{ old('status', $statusDisplayValue) }}">
	                                @endif
	                                @if ($isDepartmentAdmin || $isSuperAdmin)
                                    <div class="mb-3">
                                        <select name="forward_to" class="form-select" required>
                                            @if ($isDepartmentAdmin)
                                                <option value="stateadmin">StateAdmin</option>
                                            @endif
                                            @if ($isSuperAdmin)
                                                <option value="nicadmin">NIC Admin</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="forward_notes" class="form-label">Description (Optional)</label>
                                        <textarea
                                            id="forward_notes"
                                            name="forward_notes"
                                            rows="3"
                                            class="form-control @error('forward_notes') is-invalid @enderror"
                                            placeholder="Add a note for this forward action">{{ old('forward_notes') }}</textarea>
                                        @error('forward_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
	                                @if ($canAssignDeveloper)
	                                    <input type="hidden" name="forward_to" value="developer">
	                                    @if ($isNicAdmin && $additionalLayerDevelopers->isNotEmpty())
	                                        <div class="mb-3">
	                                            <label class="form-label">Additional Layer (Optional)</label>
	                                            <select name="addition_layer_userid" class="form-select" id="addition_layer_userid">
	                                                <option value="">Direct assign to developer</option>
	                                                @foreach ($additionalLayerDevelopers as $layerDeveloper)
	                                                    <option value="{{ $layerDeveloper->devuserid }}" {{ old('addition_layer_userid') == $layerDeveloper->devuserid ? 'selected' : '' }}>{{ $layerDeveloper->devename }}{{ $layerDeveloper->email ? ' - '.$layerDeveloper->email : '' }}</option>
	                                                @endforeach
	                                            </select>
	                                            {{-- <p class="assignment-helper">When selected, this ticket first goes to the additional layer. That layer can assign it to the final developer.</p> --}}
	                                            @error('addition_layer_userid')
	                                                <div class="text-danger small mt-1">{{ $message }}</div>
	                                            @enderror
	                                        </div>
	                                    @endif
	                                    <div class="mb-3">
	                                        <label class="form-label">Developer</label>
	                                        <select name="developer_userid" class="form-select" id="developer_userid" {{ $assignToDeveloperLocked ? 'disabled' : '' }}>
	                                            <option value="">Select Developer{{ $isNicAdmin && $additionalLayerDevelopers->isNotEmpty() ? ' or use additional layer above' : '' }}</option>
	                                            @foreach ($developers as $developer)
	                                                <option value="{{ $developer->devuserid }}" {{ (($assignToDeveloperLocked && (string) $ticket->assigned_to_userid === (string) $developer->devuserid) || old('developer_userid') == $developer->devuserid) ? 'selected' : '' }}>{{ $developer->devename }}{{ $developer->email ? ' - '.$developer->email : '' }}</option>
	                                            @endforeach
	                                        </select>
                                        @error('developer_userid')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
	                                @if (!$assignToDeveloperLocked)
	                                    <button type="submit" class="btn btn-warning text-dark w-100 js-submit-loader" data-loading-text="{{ $canAssignDeveloper ? 'Assigning...' : 'Forwarding...' }}">{{ $canAssignDeveloper ? 'Assign to Developer' : 'Forward Ticket' }}</button>
	                                @elseif ($canAssignDeveloper)
	                                    <div class="alert alert-info mb-0">
	                                        This ticket is already assigned to the selected developer.
	                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isNicAdmin && $normalizedForwardedRole === 'nicadmin' && !in_array($ticket->status, ['closed', 'resolved']))
                    <div class="ticket-panel">
                        <div class="ticket-panel-header"><h5 class="mb-0">Forward to StateAdmin</h5></div>
                        <div class="ticket-panel-body">
	                            <form action="{{ route('helpdesk.tickets.forward', $ticket) }}" method="POST">
	                                @csrf
	                                @if ($canUpdateStatus)
	                                    <input type="hidden" name="status" class="js-forward-status-value" value="{{ old('status', $statusDisplayValue) }}">
	                                @endif
	                                <div class="mb-3">
                                    <select name="forward_to" class="form-select" required>
                                        <option value="stateadmin">StateAdmin</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 js-submit-loader" data-loading-text="Forwarding...">Forward to StateAdmin</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isDeveloper && ($isAssignedDeveloper ?? false) && $ticket->isDeveloperStage() && !in_array($ticket->status, ['closed', 'resolved']))
                    <div class="ticket-panel">
                        <div class="ticket-panel-header"><h5 class="mb-0">Send Back</h5></div>
                        <div class="ticket-panel-body">
	                            <form action="{{ route('helpdesk.tickets.send-back', $ticket) }}" method="POST">
	                                @csrf
	                                @if ($canUpdateStatus)
	                                    <input type="hidden" name="status" class="js-forward-status-value" value="{{ old('status', $statusDisplayValue) }}">
	                                @endif
	                                <div class="mb-3">
                                    <textarea name="send_back_message" rows="3" class="form-control" placeholder="Optional note"></textarea>
                                </div>
	                                <button type="submit" class="btn btn-danger w-100 js-submit-loader" data-loading-text="Sending...">Send Back to {{ $sendBackTargetLabel }}</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isOwner && in_array($ticket->status, ['closed', 'resolved']))
	                    <div class="ticket-panel">
	                        <div class="ticket-panel-header"><h5 class="mb-0">Reopen</h5></div>
	                        <div class="ticket-panel-body">
                            <form action="{{ route('helpdesk.tickets.reopen', $ticket) }}" method="POST">
                                @csrf
	                                <button type="submit" class="btn btn-outline-primary w-100 js-submit-loader" data-loading-text="Reopening...">Reopen Ticket</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
	    </div>
	        </div>
	    </div>

	    <div class="modal fade conversation-modal" id="publicConversationModal" tabindex="-1" aria-labelledby="publicConversationModalLabel" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="publicConversationModalLabel">Public Conversation</h5>
	                    <button type="button" class="btn-close js-conversation-modal-close" data-conversation-modal="#publicConversationModal" aria-label="Close"></button>
	                </div>
	                <div class="modal-body">
	                    @forelse ($visibleComments as $comment)
	                        <div class="conversation-card">
	                            <span class="conversation-number">{{ $loop->iteration }}</span>
	                            <div class="conversation-meta">
	                                <div><strong>{{ \App\Support\HelpdeskSession::normalizeUserName($comment->user_name) }}</strong> <span class="text-muted">({{ $comment->user_role }})</span></div>
	                                <small class="text-muted">{{ $comment->created_at->format('d/m/Y h:i A') }}</small>
	                            </div>
	                            @if (!empty($comment->comment_stream_badge))
	                                <span class="badge text-bg-warning mt-2">{{ $comment->comment_stream_badge }}</span>
	                            @endif
	                            <div class="conversation-message">{{ $comment->comment }}</div>
	                        </div>
	                    @empty
	                        <div class="text-muted">No comments yet.</div>
	                    @endforelse
	                </div>
	            </div>
	        </div>
	    </div>

		    @if ($canViewInternalConversation)
		        <div class="modal fade conversation-modal" id="internalConversationModal" tabindex="-1" aria-labelledby="internalConversationModalLabel" aria-hidden="true">
	            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
	                <div class="modal-content">
	                    <div class="modal-header">
	                        <h5 class="modal-title" id="internalConversationModalLabel">Internal Conversation</h5>
	                        <button type="button" class="btn-close js-conversation-modal-close" data-conversation-modal="#internalConversationModal" aria-label="Close"></button>
	                    </div>
	                    <div class="modal-body">
		                        @if ($isNicAdmin && $ticketAssignedToDeveloper)
		                            <div class="internal-filter-bar">
		                                <label class="internal-filter-chip" for="filter_developer_internal">
		                                    <input
		                                        class="js-internal-filter"
		                                        type="checkbox"
		                                        id="filter_developer_internal"
		                                        data-filter-role="developer"
		                                    >
		                                    <span><i class="bi bi-shield-lock"></i>Developer Internal</span>
		                                </label>
		                            </div>
		                        @elseif ($isNicAdmin)
		                            <p class="internal-filter-hint">
		                                Developer internal comments can be filtered only after this ticket is assigned to a developer.
		                            </p>
		                        @endif
	                        @forelse ($filteredInternalComments as $comment)
	                            <div class="conversation-card internal internal-comment-card" data-internal-role="{{ $comment->internal_filter_role }}">
	                                <span class="conversation-number">{{ $loop->iteration }}</span>
	                                <div class="conversation-meta">
	                                    <div class="conversation-author-line">
	                                        <span><strong>{{ \App\Support\HelpdeskSession::normalizeUserName($comment->user_name) }}</strong> <span class="text-muted">({{ $comment->user_role }})</span></span>
	                                        @if (($comment->internal_filter_badge ?? null) === 'Watchlist')
	                                            <span class="internal-comment-badge watchlist-comment-badge">
	                                                <i class="bi bi-eye"></i>
	                                                Watchlist
	                                            </span>
	                                        @else
	                                            <span class="badge internal-comment-badge text-bg-warning">
	                                                {{ $comment->internal_filter_badge }}
	                                            </span>
	                                        @endif
	                                    </div>
	                                    <small class="text-muted">{{ $comment->created_at->format('d/m/Y h:i A') }}</small>
	                                </div>
	                                <div class="conversation-message">{{ $comment->display_comment ?? $comment->comment }}</div>
	                            </div>
	                        @empty
	                            <div class="text-muted">No internal comments found for the selected roles.</div>
	                        @endforelse
	                    </div>
	                </div>
	            </div>
		        </div>
		    @endif

		    @if ($assignmentHistoryVisible)
		        <div class="modal fade conversation-modal" id="assignmentHistoryModal" tabindex="-1" aria-labelledby="assignmentHistoryModalLabel" aria-hidden="true">
		            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		                <div class="modal-content">
		                    <div class="modal-header">
		                        <h5 class="modal-title" id="assignmentHistoryModalLabel">Developer Assignment History</h5>
		                        <button type="button" class="btn-close js-conversation-modal-close" data-conversation-modal="#assignmentHistoryModal" aria-label="Close"></button>
		                    </div>
		                    <div class="modal-body">
		                        @foreach ($assignmentHistoryRows as $assignment)
		                            <div class="conversation-card assignment-history">
		                                <span class="conversation-number">{{ $loop->iteration }}</span>
		                                <div class="conversation-meta">
		                                    <div>
		                                        <strong>{{ $assignment->developer_name ?: '-' }}</strong>
		                                        <span class="text-muted">assigned developer</span>
		                                    </div>
		                                    <span class="badge text-bg-secondary">{{ ucfirst($assignment->status ?: '-') }}</span>
		                                </div>
		                                <div class="conversation-message">
		                                    <div><strong>Assigned By:</strong> {{ $assignment->assigned_by_name ?: '-' }}</div>
		                                    <div><strong>Assigned On:</strong> {{ $assignment->assigned_at ? $assignment->assigned_at->format('d/m/Y h:i A') : '-' }}</div>
		                                    <div><strong>Released On:</strong> {{ $assignment->released_at ? $assignment->released_at->format('d/m/Y h:i A') : '-' }}</div>
		                                    <div><strong>Notes:</strong> {{ $assignment->notes ?: '-' }}</div>
		                                </div>
		                            </div>
		                        @endforeach
		                    </div>
		                </div>
		            </div>
		        </div>
		    @endif

		    @php
		        $ticketPopupMessage = $errors->first('forward_to')
		            ?: $errors->first('developer_userid')
	            ?: $errors->first('addition_layer_userid')
	            ?: $errors->first('watchlist_userid')
	            ?: $errors->first('forward_notes')
	            ?: $errors->first('send_back_message');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const internalFilters = document.querySelectorAll('.js-internal-filter');
	            const internalCommentCards = document.querySelectorAll('.internal-comment-card');
	            const commentVisibilityInput = document.getElementById('comment_visibility');
		            const dgAdminInternalNote = document.getElementById('dg_admin_internal_note');
		            const statusSelect = document.getElementById('helpdesk-status-select');
		            const forwardStatusInputs = document.querySelectorAll('.js-forward-status-value');
		            const additionLayerSelect = document.getElementById('addition_layer_userid');
		            const developerSelect = document.getElementById('developer_userid');

	            const syncForwardStatusInputs = function () {
	                if (!statusSelect) {
	                    return;
	                }

		                forwardStatusInputs.forEach(function (input) {
		                    input.value = statusSelect.value;
		                });
		            };

		            const syncAssignmentDropdowns = function () {
		                if (!additionLayerSelect || !developerSelect) {
		                    return;
		                }

		                const hasLayer = additionLayerSelect.value !== '';
		                developerSelect.disabled = hasLayer;
		                if (hasLayer) {
		                    developerSelect.value = '';
		                }
		            };

            const applyInternalCommentFilter = function () {
                const checkedFilters = Array.from(internalFilters).filter(function (filter) {
                    return filter.checked;
                });

                let activeRole = null;
                if (checkedFilters.length > 0) {
                    activeRole = checkedFilters[checkedFilters.length - 1].dataset.filterRole;
                }

                internalFilters.forEach(function (filter) {
                    if (activeRole && filter.dataset.filterRole !== activeRole) {
                        filter.checked = false;
                    }
                });

                internalCommentCards.forEach(function (card) {
                    if (!activeRole) {
                        card.hidden = false;
                        return;
                    }

                    card.hidden = card.dataset.internalRole !== activeRole;
                });

                if (commentVisibilityInput) {
                    if (activeRole === 'developer') {
                        commentVisibilityInput.value = 'developer_internal';
                    } else {
                        commentVisibilityInput.value = @json($isNicAdmin ? 'dg_internal' : 'public');
                    }
                }
            };

            const applyDgAdminInternalNote = function () {
                if (!commentVisibilityInput || !dgAdminInternalNote) {
                    return;
                }

                commentVisibilityInput.value = dgAdminInternalNote.checked ? 'dg_internal' : 'public';
            };

            internalFilters.forEach(function (filter) {
                filter.addEventListener('change', applyInternalCommentFilter);
            });

            applyInternalCommentFilter();

	            if (dgAdminInternalNote) {
	                dgAdminInternalNote.addEventListener('change', applyDgAdminInternalNote);
	                applyDgAdminInternalNote();
	            }

		            if (statusSelect) {
		                statusSelect.addEventListener('change', syncForwardStatusInputs);
		                syncForwardStatusInputs();
		            }

            if (additionLayerSelect) {
                additionLayerSelect.addEventListener('change', syncAssignmentDropdowns);
                syncAssignmentDropdowns();
            }

            const conversationModalElement = function (button) {
                const selector = button.dataset.conversationModal || '';

                return selector ? document.querySelector(selector) : null;
            };

            const showConversationModal = function (modalElement) {
                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    return;
                }

                if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery(modalElement).modal('show');
                    return;
                }

                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                modalElement.removeAttribute('aria-hidden');
                modalElement.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');
            };

            const hideConversationModal = function (modalElement) {
                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                    return;
                }

                if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                    window.jQuery(modalElement).modal('hide');
                    return;
                }

                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
                document.body.classList.remove('modal-open');
            };

            document.querySelectorAll('.js-conversation-modal-open').forEach(function (button) {
                button.addEventListener('click', function () {
                    const modalElement = conversationModalElement(button);

                    if (!modalElement) {
                        return;
                    }

                    showConversationModal(modalElement);
                });
            });

            document.querySelectorAll('.js-conversation-modal-close').forEach(function (button) {
                button.addEventListener('click', function () {
                    const modalElement = conversationModalElement(button);

                    if (!modalElement) {
                        return;
                    }

                    hideConversationModal(modalElement);
                });
            });

            document.querySelectorAll('form').forEach(function (form) {
	                form.addEventListener('submit', function () {
	                    syncForwardStatusInputs();

	                    if (!form.checkValidity()) {
	                        return;
                    }

                    const submitButton = form.querySelector('.js-submit-loader[type="submit"]');

                    if (!submitButton || submitButton.disabled) {
                        return;
                    }

                    const loadingText = submitButton.dataset.loadingText || 'Processing...';
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>' + loadingText;
                });
            });

            @if ($ticketPopupMessage)
                (function () {
                    const modalElement = document.getElementById('confirmation_alert');
                    const titleElement = document.getElementById('confirmation_alertmodal');
                    const bodyElement = document.getElementById('alert_body');
                    const okButton = document.getElementById('ok_button');
                    const processButton = document.getElementById('process_button');
                    const cancelButton = document.getElementById('cancel_button');

                    if (!modalElement || !titleElement || !bodyElement) {
                        window.alert(@json($ticketPopupMessage));
                        return;
                    }

                    titleElement.textContent = 'Alert';
                    bodyElement.textContent = @json($ticketPopupMessage);

                    if (okButton) {
                        okButton.style.display = 'inline-block';
                    }

                    if (processButton) {
                        processButton.style.display = 'none';
                    }

                    if (cancelButton) {
                        cancelButton.style.display = 'none';
                    }

                    modalElement.style.zIndex = '10000000';

                    if (window.bootstrap && window.bootstrap.Modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                        return;
                    }

                    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                        window.jQuery(modalElement).modal('show');
                        return;
                    }

                    window.alert(@json($ticketPopupMessage));
                })();
            @endif
        });
    </script>
@endsection
