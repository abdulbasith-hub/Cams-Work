@extends('index2')

@section('title', 'Task Management')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2">
        @include('helpdesk-v2.partials.flashes')

        <div class="hdv2-topbar hdv2-dashboard-head">
            <div>
                <div class="hdv2-eyebrow">Task Management</div>
                <h1><i class="ti ti-dashboard"></i> Helpdesk Dashboard</h1>
            </div>
            <div class="hdv2-actions">
                <div class="hdv2-dashboard-switch" role="tablist" aria-label="Helpdesk dashboard switch">
                    <a href="{{ route('helpdesk.dashboard.short') }}" role="tab">
                        <span></span>
                        <i class="ti ti-ticket"></i>
                        Tickets
                    </a>
                    <a href="{{ route('task-management.dashboard') }}" class="is-active" role="tab" aria-selected="true">
                        <span></span>
                        <i class="ti ti-clipboard-list"></i>
                        Tasks
                    </a>
                </div>
                @if ($canCreate)
                    <a href="{{ route('task-management.create') }}" class="btn btn-primary">Create Task</a>
                @endif
            </div>
        </div>

        @if (!empty($taskTabs))
            <nav class="hdv2-stage-tabs" aria-label="Senior Developer task tabs">
                @foreach ($taskTabs as $tabKey => $tabLabel)
                    <a href="{{ route('task-management.dashboard', ['tab' => $tabKey]) }}"
                       class="{{ $activeTaskTab === $tabKey ? 'is-active' : '' }}">
                        <span>{{ $tabLabel }}</span>
                    </a>
                @endforeach
            </nav>
        @endif

        @php
            $cards = [
                ['key' => 'total', 'label' => 'Total Tasks', 'hint' => 'All visible tasks', 'icon' => 'ti ti-list-check'],
                ['key' => 'in_progress', 'label' => 'In Progress', 'hint' => 'Developer working', 'icon' => 'ti ti-loader-2'],
                ['key' => 'pending', 'label' => 'Pending', 'hint' => 'Not started', 'icon' => 'ti ti-clock'],
                ['key' => 'overdue', 'label' => 'Overdue', 'hint' => 'Past expected date', 'icon' => 'ti ti-alert-triangle'],
                ['key' => 'completed', 'label' => 'Testing Stage', 'hint' => 'Developer completed', 'icon' => 'ti ti-circle-check'],
            ];
        @endphp

        <div class="hdv2-stat-grid hdv2-dashboard-stat-grid hdv2-task-dashboard-stat-grid">
            @foreach ($cards as $card)
                <button type="button"
                        class="hdv2-stat hdv2-stat-card hdv2-stat-{{ $card['key'] }} {{ $card['key'] === 'total' ? 'is-active' : '' }}"
                        data-hdv2-dashboard-filter="{{ $card['key'] }}"
                        data-hdv2-filter-target="task-dashboard"
                        data-hdv2-filter-title="{{ $card['label'] }}">
                    <span class="hdv2-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                    <span class="hdv2-stat-label">{{ $card['label'] }}</span>
                    <strong>{{ $stats[$card['key']] ?? 0 }}</strong>
                    <small>{{ $card['hint'] }}</small>
                </button>
            @endforeach
        </div>

        @if ($canCreate)
            <form method="GET" action="{{ route('task-management.dashboard') }}" class="hdv2-filter">
                @if (!empty($activeTaskTab) && $activeTaskTab !== 'all')
                    <input type="hidden" name="tab" value="{{ $activeTaskTab }}">
                @endif
                <select name="developer_userid">
                    <option value="">All developers</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->devuserid }}" @selected($selectedDeveloperId === (string) $developer->devuserid)>
                            {{ $developer->devename }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('task-management.dashboard') }}" class="btn btn-light">Reset</a>
            </form>
        @endif

        <section class="hdv2-panel hdv2-grid-panel hdv2-dashboard-table hdv2-task-dashboard-table" data-hdv2-grid data-hdv2-grid-name="task-dashboard">
            <div class="hdv2-grid-head">
                <div>
                    <span class="hdv2-eyebrow">{{ $roleLabel ?? $role }}</span>
                    <h2><span data-hdv2-filter-label>Total Tasks</span></h2>
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
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="task">Task</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="developer">Developer</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="type">Task Type</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="status">Status</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="assigned">Assigned On</button></th>
                            <th><button type="button" class="hdv2-sort-btn" data-hdv2-sort="expected">Expected On</button></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            @php
                                $assignedTimestamp = optional($task->assigned_on)->timestamp ?? 0;
                                $expectedTimestamp = optional($task->expected_date_to_complete)->timestamp ?? 0;
                                $taskStatusKey = $task->statusKey();
                            @endphp
                            <tr data-hdv2-row
                                data-hdv2-task-status="{{ $taskStatusKey }}"
                                data-hdv2-task-total="Y"
                                data-hdv2-task-pending="{{ $taskStatusKey === 'pending' ? 'Y' : 'N' }}"
                                data-hdv2-task-in-progress="{{ $taskStatusKey === 'in_progress' ? 'Y' : 'N' }}"
                                data-hdv2-task-overdue="{{ $taskStatusKey === 'overdue' ? 'Y' : 'N' }}"
                                data-hdv2-task-completed="{{ $taskStatusKey === 'completed' ? 'Y' : 'N' }}">
                                <td class="hdv2-subject-cell" data-hdv2-sort-value="task" data-hdv2-sort-raw="{{ $task->process_assigned }}">
                                    <strong>{{ $task->process_assigned }}</strong>
                                    <small>Assigned by {{ $task->assigned_by_name ?: '-' }}</small>
                                </td>
                                <td data-hdv2-sort-value="developer" data-hdv2-sort-raw="{{ $task->developer_name }}">{{ $task->developer_name ?: '-' }}</td>
                                <td data-hdv2-sort-value="type" data-hdv2-sort-raw="{{ $task->task_type }}">{{ ucfirst((string) $task->task_type) }}</td>
                                <td data-hdv2-sort-value="status" data-hdv2-sort-raw="{{ $task->statusLabel() }}">
                                    <span class="hdv2-badge hdv2-status-{{ $task->statusKey() }}">{{ $task->statusLabel() }}</span>
                                </td>
                                <td data-hdv2-sort-value="assigned" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $assignedTimestamp }}">
                                    {{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td data-hdv2-sort-value="expected" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $expectedTimestamp }}">
                                    {{ $task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('task-management.show', $task) }}" class="btn btn-primary btn-sm hdv2-view-sheet-btn">View Sheet</a>
                                </td>
                            </tr>
                        @empty
                            <tr data-hdv2-empty-row>
                                <td colspan="7" class="hdv2-empty">No tasks found.</td>
                            </tr>
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
