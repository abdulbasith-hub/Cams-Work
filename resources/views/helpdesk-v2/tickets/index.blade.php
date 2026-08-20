@extends('index2')

@section('title', 'Helpdesk V2 Tickets')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2">
        @include('helpdesk-v2.partials.flashes')
        @include('helpdesk-v2.partials.nav', ['title' => 'Tickets'])
        @php
            $showTechTeamStatus = in_array($role, ['nic_admin', 'developer', 'layer_lead', 'watchlist'], true);
            $showAssignedBy = $role === 'nic_admin';
            $returnedStatuses = [
                \App\Models\HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                'returned_by_developer',
                \App\Models\HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
	                \App\Models\HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
	                'returned_to_developer',
	                \App\Models\HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
	                'returned_to_tester',
	                \App\Models\HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
	            ];
            $emptyColspan = 8 + ($showTechTeamStatus ? 1 : 0) + ($showAssignedBy ? 1 : 0);
        @endphp

        <form method="GET" action="{{ route('helpdesk-v2.tickets.index') }}" class="hdv2-filter">
            <input type="hidden" name="role" value="{{ $role }}">
            @if ($leadStage)
                <input type="hidden" name="stage" value="{{ $leadStage }}">
            @endif
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search ticket, subject, module, user">
            <select name="priority">
                <option value="">All priorities</option>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority->value }}" @selected(strtolower((string) request('priority')) === $priority->value)>{{ $priority->label }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(strtolower((string) request('status')) === $status->value)>{{ $status->label }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>

        @if ($role === 'layer_lead')
            @php
                $stageQuery = request()->except('stage');
                $allUrl = route('helpdesk-v2.tickets.index', array_merge($stageQuery, ['role' => $role]));
                $developmentUrl = route('helpdesk-v2.tickets.index', array_merge($stageQuery, ['role' => $role, 'stage' => 'development']));
                $testingUrl = route('helpdesk-v2.tickets.index', array_merge($stageQuery, ['role' => $role, 'stage' => 'testing']));
            @endphp
            <nav class="hdv2-stage-tabs" aria-label="Senior Developer ticket stages">
                <a href="{{ $allUrl }}" class="{{ $leadStage ? '' : 'is-active' }}">
                    <span>All</span>
                    <strong>{{ $leadStageCounts['all'] ?? $tickets->count() }}</strong>
                </a>
                <a href="{{ $developmentUrl }}" class="{{ $leadStage === 'development' ? 'is-active' : '' }}">
                    <span>Development</span>
                    <strong>{{ $leadStageCounts['development'] ?? 0 }}</strong>
                </a>
                <a href="{{ $testingUrl }}" class="{{ $leadStage === 'testing' ? 'is-active' : '' }}">
                    <span>Testing</span>
                    <strong>{{ $leadStageCounts['testing'] ?? 0 }}</strong>
                </a>
            </nav>
        @endif

        <section class="hdv2-panel hdv2-grid-panel" data-hdv2-grid>
            <div class="hdv2-grid-head">
                <div>
                    <span class="hdv2-eyebrow">Ticket Register</span>
                    <h2>{{ $tickets->count() }} Tickets</h2>
                </div>
                <div class="hdv2-grid-tools">
                    <button type="button" class="hdv2-download-btn" data-hdv2-download-grid>
                        <i class="ti ti-download"></i>
                        Download
                    </button>
                    <div class="hdv2-grid-length">
                        <label>
                            Show
                            <select data-hdv2-page-size>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                </div>
            </div>
            <div class="hdv2-table-wrap">
                <table class="hdv2-table">
                    <thead>
                        <tr>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="ticket">Ticket</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="subject">Subject</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="module">Module</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="priority">Priority</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="status">Current Status</button></th>
                            @if ($showTechTeamStatus)
                                <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="tech_status">Tech Team Status</button></th>
                            @endif
                            @if ($showAssignedBy)
                                <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="assigned_by">Assigned By</button></th>
                            @endif
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="current_on">Currently With</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="returned_on">Returned On</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="updated">Last Updated</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            @php
                                $currentOnMeta = $ticket->currentOnMetaForRole($role);
                                $isReturned = in_array($ticket->status, $returnedStatuses, true);
                                $returnedTimestamp = $isReturned ? (optional($ticket->updated_at)->timestamp ?? 0) : 0;
                                $updatedTimestamp = optional($ticket->updated_at)->timestamp ?? 0;
                            @endphp
                            <tr data-hdv2-row>
                                <td class="hdv2-ticket-cell" data-hdv2-sort-value="ticket" data-hdv2-sort-raw="{{ $ticket->ticket_number }}">
                                    <a href="{{ route('helpdesk-v2.tickets.show', ['ticket' => $ticket, 'role' => $role]) }}">{{ $ticket->ticket_number }}</a>
                                </td>
                                <td class="hdv2-subject-cell" data-hdv2-sort-value="subject" data-hdv2-sort-raw="{{ $ticket->subject }} {{ $ticket->created_by_name }}" title="{{ $ticket->subject }}">
                                    <strong>{{ $ticket->subject }}</strong>
                                    <small>{{ $ticket->created_by_name }}</small>
                                </td>
                                <td data-hdv2-sort-value="module" data-hdv2-sort-raw="{{ $ticket->request_type_label }}">{{ $ticket->request_type_label }}</td>
                                <td data-hdv2-sort-value="priority" data-hdv2-sort-raw="{{ strtolower((string) $ticket->priority) }}"><span class="hdv2-badge hdv2-priority-{{ strtolower((string) $ticket->priority) }}">{{ $ticket->priority }}</span></td>
                                <td data-hdv2-sort-value="status" data-hdv2-sort-raw="{{ $ticket->mainStatusLabel() }}"><span class="hdv2-badge hdv2-status-{{ $ticket->mainStatusKey() }}">{{ $ticket->mainStatusLabel() }}</span></td>
                                @if ($showTechTeamStatus)
                                    <td data-hdv2-sort-value="tech_status" data-hdv2-sort-raw="{{ $ticket->techTeamStatusLabel() }}">
                                        <span class="hdv2-badge hdv2-status-{{ $ticket->status }}">{{ $ticket->techTeamStatusLabel() }}</span>
                                    </td>
                                @endif
                                @if ($showAssignedBy)
                                    <td data-hdv2-sort-value="assigned_by" data-hdv2-sort-raw="{{ $ticket->assignedByLabel() }}">{{ $ticket->assignedByLabel() }}</td>
                                @endif
                                <td data-hdv2-sort-value="current_on" data-hdv2-sort-raw="{{ $ticket->currentOnLabelForRole($role) }} {{ $currentOnMeta }}">
                                    {{ $ticket->currentOnLabelForRole($role) }}
                                    @if ($currentOnMeta)
                                        <small>{{ $currentOnMeta }}</small>
                                    @endif
                                </td>
                                <td data-hdv2-sort-value="returned_on" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $returnedTimestamp }}">
                                    {{ $isReturned && $ticket->updated_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->updated_at) : '-' }}
                                </td>
                                <td data-hdv2-sort-value="updated" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $updatedTimestamp }}">{{ $ticket->updated_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->updated_at) : '-' }}</td>
                            </tr>
                        @empty
                            <tr data-hdv2-empty-row><td colspan="{{ $emptyColspan }}" class="hdv2-empty">No tickets match the current filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="hdv2-grid-footer">
                <div class="hdv2-grid-info" data-hdv2-page-info></div>
                <div class="hdv2-grid-pages" data-hdv2-page-buttons></div>
            </div>
        </section>
    </div>
@endsection
