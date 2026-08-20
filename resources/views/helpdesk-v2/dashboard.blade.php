@extends('index2')

@section('title', 'Helpdesk V2 Dashboard')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2">
        @include('helpdesk-v2.partials.flashes')
        @include('helpdesk-v2.partials.nav', [
            'title' => $roleLabel . ' Dashboard',
            'showDashboardSwitch' => true,
            'canViewTaskDashboard' => $canViewTaskDashboard,
            'dashboardPane' => $dashboardPane ?? 'tickets',
        ])

        <div class="hdv2-role-tabs" role="tablist">
            @foreach ($roles as $roleKey => $label)
                <a class="{{ $role === $roleKey ? 'is-active' : '' }}"
                    href="{{ route('helpdesk-v2.dashboard', $roleKey) }}">{{ $label }}</a>
            @endforeach
        </div>

        @php
            $closedStatuses = [
                \App\Models\HelpdeskV2Ticket::STATUS_CLOSED,
                \App\Models\HelpdeskV2Ticket::STATUS_REJECTED,
                \App\Models\HelpdeskV2Ticket::STATUS_CANCELLED,
            ];
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
            $statCards = [
                [
                    'key' => 'total',
                    'label' => 'Total Tickets',
                    'hint' => 'All visible tickets',
                    'icon' => 'ti ti-ticket',
                ],
                [
                    'key' => 'in_progress',
                    'label' => 'In Progress',
                    'hint' => 'Currently working',
                    'icon' => 'ti ti-loader-2',
                ],
                [
                    'key' => 'urgent',
                    'label' => 'Urgent',
                    'hint' => 'Urgent priority queue',
                    'icon' => 'ti ti-alert-triangle',
                ],
                [
                    'key' => 'returned',
                    'label' => 'Returned',
                    'hint' => 'Returned for review',
                    'icon' => 'ti ti-arrow-back-up',
                ],
                [
                    'key' => 'resolved_closed',
                    'label' => 'Resolved / Closed',
                    'hint' => 'Completed tickets',
                    'icon' => 'ti ti-circle-check',
                ],
            ];
            $developerStatCards = [
                [
                    'key' => 'total',
                    'label' => 'Total Tickets',
                    'hint' => 'Developer tickets',
                    'icon' => 'ti ti-ticket',
                ],
                [
                    'key' => 'in_progress',
                    'label' => 'In Progress',
                    'hint' => 'Developer working',
                    'icon' => 'ti ti-loader-2',
                ],
                [
                    'key' => 'resolved',
                    'label' => 'Resolved',
                    'hint' => 'Developer completed',
                    'icon' => 'ti ti-circle-check',
                ],
                [
                    'key' => 'returned',
                    'label' => 'Returned Tickets',
                    'hint' => 'Returned at developer end',
                    'icon' => 'ti ti-arrow-back-up',
                ],
            ];
            $taskCards = [
                [
                    'key' => 'total',
                    'label' => 'Total Tasks',
                    'hint' => 'All visible tasks',
                    'icon' => 'ti ti-list-check',
                ],
                [
                    'key' => 'in_progress',
                    'label' => 'In Progress',
                    'hint' => 'Developer working',
                    'icon' => 'ti ti-loader-2',
                ],
                ['key' => 'pending', 'label' => 'Pending', 'hint' => 'Not started', 'icon' => 'ti ti-clock'],
                [
                    'key' => 'overdue',
                    'label' => 'Overdue',
                    'hint' => 'Past expected date',
                    'icon' => 'ti ti-alert-triangle',
                ],
                [
                    'key' => 'completed',
                    'label' => 'Testing Stage',
                    'hint' => 'Developer completed',
                    'icon' => 'ti ti-circle-check',
                ],
            ];
            $overviewStats = [
                ['label' => 'Total', 'value' => $stats['total'] ?? 0],
                ['label' => 'In Progress', 'value' => $stats['in_progress'] ?? 0],
                ['label' => 'Urgent', 'value' => $stats['urgent'] ?? 0],
                ['label' => 'Completed', 'value' => $stats['resolved_closed'] ?? 0],
            ];
        @endphp
        <section class="hdv2-dashboard-overview">
            <div class="hdv2-dashboard-overview-main">
                <span class="hdv2-eyebrow">{{ $roleLabel }}</span>
                <h2>Helpdesk Ticket Dashboard</h2>
                <p>Card wise ticket queue with DataTables search, length change, pagination, and download.</p>
            </div>
            <div class="hdv2-dashboard-overview-stats">
                @foreach ($overviewStats as $overviewStat)
                    <div>
                        <span>{{ $overviewStat['label'] }}</span>
                        <strong>{{ $overviewStat['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="hdv2-dashboard-pane {{ ($dashboardPane ?? 'tickets') === 'tickets' ? 'is-active' : '' }}"
            data-hdv2-dashboard-pane="tickets" @if (($dashboardPane ?? 'tickets') !== 'tickets') hidden @endif>
            <div class="hdv2-stat-grid hdv2-dashboard-stat-grid">
                @foreach ($statCards as $card)
                    <button type="button"
                        class="hdv2-stat hdv2-stat-card hdv2-stat-{{ $card['key'] }} {{ $card['key'] === 'in_progress' ? 'is-active' : '' }}"
                        data-hdv2-dashboard-filter="{{ $card['key'] }}"
                        data-hdv2-dashboard-filter-token="{{ \Illuminate\Support\Facades\Crypt::encryptString($card['key']) }}"
                        data-hdv2-filter-target="dashboard" data-hdv2-filter-title="{{ $card['label'] }}">
                        <span class="hdv2-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                        <span class="hdv2-stat-label">{{ $card['label'] }}</span>
                        <strong>{{ $stats[$card['key']] ?? 0 }}</strong>
                        <small>{{ $card['hint'] }}</small>
                    </button>
                @endforeach
            </div>

            <section class="hdv2-panel hdv2-grid-panel hdv2-dashboard-table" data-hdv2-grid data-hdv2-datatable
                data-hdv2-ajax-grid data-hdv2-ajax-type="tickets"
                data-hdv2-ajax-type-token="{{ \Illuminate\Support\Facades\Crypt::encryptString('tickets') }}"
                data-hdv2-ajax-url="{{ route('helpdesk-v2.dashboard.data', ['role' => $role]) }}" data-hdv2-autoload="true"
                data-hdv2-grid-name="dashboard">
                <div class="hdv2-grid-head">
                    <div>
                        <span class="hdv2-eyebrow">{{ $roleLabel }}</span>
                        <h2><span data-hdv2-filter-label>In Progress</span></h2>
                    </div>
                </div>
                <div class="hdv2-table-wrap table-responsive">
                    <table id="hdv2DashboardTicketsTable" class="table table-striped table-bordered hdv2-table">
                        <thead>
                            <tr>
                                <th>Ticket Number</th>
                                <th>Subject / Created By</th>
                                <th>Module</th>
                                <th>Priority</th>
                                <th>Current Status</th>
                                <th>Currently With</th>
                                <th>Ageing Days</th>
                                <th>Created On</th>
                                <th>Last Updated On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dashboardTickets as $ticket)
                                @php
                                    $currentOnMeta = $ticket->currentOnMetaForRole($role);
                                    $isClosed = in_array($ticket->status, $closedStatuses, true);
                                    $isReturned = in_array($ticket->status, $returnedStatuses, true);
                                    $createdTimestamp = optional($ticket->created_at)->timestamp ?? 0;
                                    $updatedTimestamp = optional($ticket->updated_at)->timestamp ?? 0;
                                @endphp
                                <tr data-hdv2-row data-hdv2-status="{{ $ticket->mainStatusKey() }}"
                                    data-hdv2-priority="{{ strtolower((string) $ticket->priority) }}"
                                    data-hdv2-pending="{{ $isClosed ? 'N' : 'Y' }}"
                                    data-hdv2-active="{{ $ticket->status !== 'resolved' && !$isClosed ? 'Y' : 'N' }}"
                                    data-hdv2-returned="{{ $isReturned ? 'Y' : 'N' }}"
                                    data-hdv2-resolved="{{ $ticket->status === 'resolved' ? 'Y' : 'N' }}"
                                    data-hdv2-closed="{{ $isClosed ? 'Y' : 'N' }}"
                                    data-hdv2-reopened="{{ $ticket->reopen_count > 0 ? 'Y' : 'N' }}">
                                    <td class="hdv2-ticket-cell" data-hdv2-sort-value="ticket"
                                        data-hdv2-sort-raw="{{ $ticket->ticket_number }}">
                                        <a
                                            href="{{ route('helpdesk-v2.tickets.show', ['ticket' => $ticket, 'role' => $role]) }}">{{ $ticket->ticket_number }}</a>
                                    </td>
                                    <td class="hdv2-subject-cell" data-hdv2-sort-value="subject"
                                        data-hdv2-sort-raw="{{ $ticket->subject }} {{ $ticket->created_by_name }}"
                                        title="{{ $ticket->subject }}">
                                        <strong>{{ $ticket->subject }}</strong>
                                        <small>{{ $ticket->created_by_name }}</small>
                                    </td>
                                    <td data-hdv2-sort-value="module"
                                        data-hdv2-sort-raw="{{ $ticket->request_type_label }}">
                                        {{ $ticket->request_type_label }}</td>
                                    <td data-hdv2-sort-value="priority"
                                        data-hdv2-sort-raw="{{ strtolower((string) $ticket->priority) }}"><span
                                            class="hdv2-badge hdv2-priority-{{ strtolower((string) $ticket->priority) }}">{{ $ticket->priority }}</span>
                                    </td>
                                    <td data-hdv2-sort-value="status"
                                        data-hdv2-sort-raw="{{ $ticket->mainStatusLabel() }}"><span
                                            class="hdv2-badge hdv2-status-{{ $ticket->mainStatusKey() }}">{{ $ticket->mainStatusLabel() }}</span>
                                    </td>
                                    <td data-hdv2-sort-value="current_on"
                                        data-hdv2-sort-raw="{{ $ticket->currentOnLabelForRole($role) }} {{ $currentOnMeta }}">
                                        {{ $ticket->currentOnLabelForRole($role) }}
                                        @if ($currentOnMeta)
                                            <small>{{ $currentOnMeta }}</small>
                                        @endif
                                    </td>
                                    <td data-hdv2-sort-value="age" data-hdv2-sort-type="number"
                                        data-hdv2-sort-raw="{{ $updatedTimestamp }}">
                                        {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-' }}
                                    </td>
                                    <td data-hdv2-sort-value="created" data-hdv2-sort-type="number"
                                        data-hdv2-sort-raw="{{ $createdTimestamp }}">
                                        {{ $ticket->created_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->created_at) : '-' }}
                                    </td>
                                    <td data-hdv2-sort-value="updated" data-hdv2-sort-type="number"
                                        data-hdv2-sort-raw="{{ $updatedTimestamp }}">
                                        {{ $ticket->updated_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->updated_at) : '-' }}
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($canViewDeveloperFilter)
                <section class="hdv2-panel hdv2-developer-filter-panel" data-hdv2-developer-filter
                    data-hdv2-ajax-url="{{ route('helpdesk-v2.dashboard.data', ['role' => $role]) }}"
                    data-hdv2-ajax-type-token="{{ \Illuminate\Support\Facades\Crypt::encryptString('developers') }}">
                    <div class="hdv2-panel-head">
                        <h2>Developer Filter</h2>
                    </div>
                    <div class="hdv2-developer-filter-control">
                        <label for="hdv2DeveloperFilter">Developer</label>
                        <select id="hdv2DeveloperFilter" class="form-select" data-hdv2-developer-filter-select disabled>
                            <option value="">Loading developers...</option>
                        </select>
                    </div>
                    <div class="hdv2-stat-grid hdv2-developer-stat-grid" data-hdv2-developer-stat-grid hidden>
                        @foreach ($developerStatCards as $card)
                            <button type="button"
                                class="hdv2-stat hdv2-stat-card hdv2-stat-{{ $card['key'] }}"
                                data-hdv2-developer-dashboard-card
                                data-hdv2-dashboard-stage="{{ $card['key'] }}"
                                data-hdv2-dashboard-filter=""
                                data-hdv2-dashboard-filter-token=""
                                data-hdv2-filter-target="developer-dashboard"
                                data-hdv2-filter-title="{{ $card['label'] }}" disabled>
                                <span class="hdv2-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                                <span class="hdv2-stat-label">{{ $card['label'] }}</span>
                                <strong data-hdv2-dashboard-count>0</strong>
                                <small>{{ $card['hint'] }}</small>
                            </button>
                        @endforeach
                    </div>
                </section>

                <section class="hdv2-panel hdv2-grid-panel hdv2-count-ticket-grid" data-hdv2-grid data-hdv2-datatable
                    data-hdv2-ajax-grid data-hdv2-ajax-type="tickets"
                    data-hdv2-ajax-type-token="{{ \Illuminate\Support\Facades\Crypt::encryptString('tickets') }}"
                    data-hdv2-ajax-url="{{ route('helpdesk-v2.dashboard.data', ['role' => $role]) }}"
                    data-hdv2-grid-name="developer-dashboard" data-hdv2-start-hidden="true" hidden>
                    <div class="hdv2-grid-head">
                        <div>
                            <span class="hdv2-eyebrow">Developer Tickets</span>
                            <h2><span data-hdv2-filter-label>Selected Developer</span></h2>
                        </div>
                    </div>
                    <div class="hdv2-table-wrap table-responsive">
                        <table id="hdv2DeveloperTicketsTable" class="table table-striped table-bordered hdv2-table">
                            <thead>
                                <tr>
                                    <th>Ticket Number</th>
                                    <th>Subject / Created By</th>
                                    <th>Module</th>
                                    <th>Priority</th>
                                    <th>Current Status</th>
                                    <th>Currently With</th>
                                    <th>Ageing Days</th>
                                    <th>Created On</th>
                                    <th>Last Updated On</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>

        @if ($canViewTaskDashboard)
            <div class="hdv2-dashboard-pane {{ ($dashboardPane ?? 'tickets') === 'tasks' ? 'is-active' : '' }}"
                data-hdv2-dashboard-pane="tasks" @if (($dashboardPane ?? 'tickets') !== 'tasks') hidden @endif>
                <div class="hdv2-stat-grid hdv2-dashboard-stat-grid">
                    @foreach ($taskCards as $card)
                        <button type="button"
                            class="hdv2-stat hdv2-stat-card hdv2-stat-{{ $card['key'] }} {{ $card['key'] === 'total' ? 'is-active' : '' }}"
                            data-hdv2-dashboard-filter="{{ $card['key'] }}"
                            data-hdv2-dashboard-filter-token="{{ \Illuminate\Support\Facades\Crypt::encryptString($card['key']) }}"
                            data-hdv2-filter-target="task-dashboard" data-hdv2-filter-title="{{ $card['label'] }}">
                            <span class="hdv2-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                            <span class="hdv2-stat-label">{{ $card['label'] }}</span>
                            <strong>{{ $taskStats[$card['key']] ?? 0 }}</strong>
                            <small>{{ $card['hint'] }}</small>
                        </button>
                    @endforeach
                </div>

                <section class="hdv2-panel hdv2-grid-panel hdv2-dashboard-table hdv2-task-dashboard-table" data-hdv2-grid
                    data-hdv2-datatable data-hdv2-ajax-grid data-hdv2-ajax-type="tasks"
                    data-hdv2-ajax-type-token="{{ \Illuminate\Support\Facades\Crypt::encryptString('tasks') }}"
                    data-hdv2-ajax-url="{{ route('helpdesk-v2.dashboard.data', ['role' => $role]) }}"
                    data-hdv2-grid-name="task-dashboard">
                    <div class="hdv2-grid-head">
                        <div>
                            <span class="hdv2-eyebrow">{{ $roleLabel }}</span>
                            <h2><span data-hdv2-filter-label>Total Tasks</span></h2>
                        </div>
                        <div class="hdv2-grid-actions">
                            <a href="{{ route('helpdesk-v2.taskdetails') }}" class="btn btn-light btn-sm">Task Details</a>
                        </div>
                    </div>
                    <div class="hdv2-table-wrap table-responsive">
                        <table id="hdv2DashboardTasksTable" class="table table-striped table-bordered hdv2-table">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Developer</th>
                                    <th>Task Type</th>
                                    <th>Status</th>
                                    <th>Assigned On</th>
                                    <th>Expected On</th>
                                    <th>Last Updated</th>
                                    <th class="no-sort no-export">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dashboardTasks as $task)
                                    @php
                                        $assignedTimestamp = optional($task->assigned_on)->timestamp ?? 0;
                                        $expectedTimestamp = optional($task->expected_date_to_complete)->timestamp ?? 0;
                                        $updatedTimestamp = optional($task->updated_at)->timestamp ?? 0;
                                        $taskStatusKey = $task->statusKey();
                                    @endphp
                                    <tr data-hdv2-row data-hdv2-task-status="{{ $taskStatusKey }}"
                                        data-hdv2-task-total="Y"
                                        data-hdv2-task-pending="{{ $taskStatusKey === 'pending' ? 'Y' : 'N' }}"
                                        data-hdv2-task-in-progress="{{ $taskStatusKey === 'in_progress' ? 'Y' : 'N' }}"
                                        data-hdv2-task-overdue="{{ $taskStatusKey === 'overdue' ? 'Y' : 'N' }}"
                                        data-hdv2-task-completed="{{ $taskStatusKey === 'completed' ? 'Y' : 'N' }}">
                                        <td class="hdv2-subject-cell" data-hdv2-sort-value="task"
                                            data-hdv2-sort-raw="{{ $task->process_assigned }} {{ $task->assigned_by_name }}">
                                            <strong>{{ $task->process_assigned }}</strong>
                                            <small>Assigned by {{ $task->assigned_by_name ?: '-' }}</small>
                                        </td>
                                        <td data-hdv2-sort-value="developer"
                                            data-hdv2-sort-raw="{{ $task->developer_name }}">
                                            {{ $task->developer_name ?: '-' }}</td>
                                        <td data-hdv2-sort-value="type" data-hdv2-sort-raw="{{ $task->task_type }}">
                                            {{ ucfirst((string) $task->task_type) }}</td>
                                        <td data-hdv2-sort-value="status"
                                            data-hdv2-sort-raw="{{ $task->statusLabel() }}">
                                            <span
                                                class="hdv2-badge hdv2-status-{{ $taskStatusKey }}">{{ $task->statusLabel() }}</span>
                                        </td>
                                        <td data-hdv2-sort-value="assigned" data-hdv2-sort-type="number"
                                            data-hdv2-sort-raw="{{ $assignedTimestamp }}">
                                            {{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}
                                        </td>
                                        <td data-hdv2-sort-value="expected" data-hdv2-sort-type="number"
                                            data-hdv2-sort-raw="{{ $expectedTimestamp }}">
                                            {{ $task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-' }}
                                        </td>
                                        <td data-hdv2-sort-value="updated" data-hdv2-sort-type="number"
                                            data-hdv2-sort-raw="{{ $updatedTimestamp }}">
                                            {{ $task->updated_at ? $task->updated_at->format('d/m/Y h:i A') : '-' }}
                                        </td>
                                        <td class="no-export">
                                            <a href="{{ route('task-management.show', $task) }}"
                                                class="btn btn-primary btn-sm hdv2-view-sheet-btn">View Sheet</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif
    </div>
@endsection
