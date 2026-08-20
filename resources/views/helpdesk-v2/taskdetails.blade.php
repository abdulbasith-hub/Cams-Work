@extends('index2')

@section('title', 'Task Details')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2 hdv2-taskdetails-page">
        @include('helpdesk-v2.partials.flashes')

        <div class="hdv2-topbar hdv2-dashboard-head">
            <div>
                <div class="hdv2-eyebrow">{{ $roleLabel }}</div>
                <h1><i class="ti ti-clipboard-list"></i> Task Details</h1>
            </div>
            <div class="hdv2-actions">
                <a href="{{ route('helpdesk-v2.dashboard', ['role' => $role, 'pane' => 'tasks']) }}" class="btn btn-light">Dashboard</a>
                @if ($canCreate)
                    <a href="{{ route('task-management.create') }}" class="btn btn-primary">Create Task</a>
                @endif
            </div>
        </div>

        @php
            $activeCard = $filters['status'] !== '' ? $filters['status'] : 'total';
            $cards = [
                ['key' => 'total', 'label' => 'Total Tasks', 'hint' => 'All filtered tasks', 'icon' => 'ti ti-list-check'],
                ['key' => 'in_progress', 'label' => 'In Progress', 'hint' => 'Developer working', 'icon' => 'ti ti-loader-2'],
                ['key' => 'pending', 'label' => 'Pending', 'hint' => 'Not started', 'icon' => 'ti ti-clock'],
                ['key' => 'overdue', 'label' => 'Overdue', 'hint' => 'Past expected date', 'icon' => 'ti ti-alert-triangle'],
                ['key' => 'completed', 'label' => 'Testing Stage', 'hint' => 'Developer completed', 'icon' => 'ti ti-circle-check'],
            ];
        @endphp

        <div class="hdv2-stat-grid hdv2-dashboard-stat-grid hdv2-taskdetails-stat-grid">
            @foreach ($cards as $card)
                <button type="button"
                        class="hdv2-stat hdv2-stat-card hdv2-stat-{{ $card['key'] }} {{ $activeCard === $card['key'] ? 'is-active' : '' }}"
                        data-hdv2-dashboard-filter="{{ $card['key'] }}"
                        data-hdv2-filter-target="taskdetails"
                        data-hdv2-filter-title="{{ $card['label'] }}">
                    <span class="hdv2-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                    <span class="hdv2-stat-label">{{ $card['label'] }}</span>
                    <strong>{{ $stats[$card['key']] ?? 0 }}</strong>
                    <small>{{ $card['hint'] }}</small>
                </button>
            @endforeach
        </div>

        <form method="GET" action="{{ route('helpdesk-v2.taskdetails') }}" class="hdv2-filter hdv2-taskdetails-filter">
            @if ($canFilterDeveloper)
                <label>
                    Developer
                    <select name="developer_userid">
                        <option value="">All developers</option>
                        @foreach ($developers as $developer)
                            <option value="{{ $developer->devuserid }}" @selected($filters['developer_userid'] === (string) $developer->devuserid)>
                                {{ $developer->devename }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif
            <label>
                Status
                <select name="status">
                    <option value="">All status</option>
                    @foreach ($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($filters['status'] === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Task Type
                <select name="task_type">
                    <option value="">All types</option>
                    @foreach ($taskTypeOptions as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" @selected($filters['task_type'] === $typeKey)>{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Assigned From
                <input type="date" name="assigned_from" value="{{ $filters['assigned_from'] }}">
            </label>
            <label>
                Assigned To
                <input type="date" name="assigned_to" value="{{ $filters['assigned_to'] }}">
            </label>
            <div class="hdv2-filter-actions">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('helpdesk-v2.taskdetails') }}" class="btn btn-light">Reset</a>
            </div>
        </form>

        <section class="hdv2-panel hdv2-grid-panel hdv2-taskdetails-table"
                 data-hdv2-grid
                 data-hdv2-datatable
                 data-hdv2-ajax-type="tasks"
                 data-hdv2-grid-name="taskdetails">
            <div class="hdv2-grid-head">
                <div>
                    <span class="hdv2-eyebrow">Task Details</span>
                    <h2><span data-hdv2-filter-label>{{ $activeCard === 'total' ? 'Total Tasks' : ($statusOptions[$activeCard] ?? 'Task Details') }}</span></h2>
                </div>
            </div>
            <div class="hdv2-table-wrap table-responsive">
                <table id="hdv2TaskDetailsTable" class="table table-striped table-bordered hdv2-table">
                    <thead>
                        <tr>
                            <th>Task / Assigned By</th>
                            <th>Developer</th>
                            <th>Task Type</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Developer Note</th>
                            <th>Assigned On</th>
                            <th>Expected On</th>
                            <th>Completed On</th>
                            <th>Testing</th>
                            <th>Last Updated</th>
                            <th class="no-export no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            @php
                                $taskStatusKey = $task->statusKey();
                                $assignedTimestamp = optional($task->assigned_on)->timestamp ?? 0;
                                $expectedTimestamp = optional($task->expected_date_to_complete)->timestamp ?? 0;
                                $completedTimestamp = optional($task->completed_on)->timestamp ?? 0;
                                $updatedTimestamp = optional($task->updated_at)->timestamp ?? 0;
                            @endphp
                            <tr data-hdv2-row
                                data-hdv2-task-status="{{ $taskStatusKey }}"
                                data-hdv2-task-total="Y"
                                data-hdv2-task-pending="{{ $taskStatusKey === 'pending' ? 'Y' : 'N' }}"
                                data-hdv2-task-in-progress="{{ $taskStatusKey === 'in_progress' ? 'Y' : 'N' }}"
                                data-hdv2-task-overdue="{{ $taskStatusKey === 'overdue' ? 'Y' : 'N' }}"
                                data-hdv2-task-completed="{{ $taskStatusKey === 'completed' ? 'Y' : 'N' }}">
                                <td class="hdv2-task-name-cell" data-hdv2-sort-value="task" data-hdv2-sort-raw="{{ $task->process_assigned }} {{ $task->assigned_by_name }}">
                                    <strong>{{ $task->process_assigned }}</strong>
                                    <small>Assigned by {{ $task->assigned_by_name ?: '-' }}</small>
                                </td>
                                <td data-hdv2-sort-value="developer" data-hdv2-sort-raw="{{ $task->developer_name }}">{{ $task->developer_name ?: '-' }}</td>
                                <td data-hdv2-sort-value="type" data-hdv2-sort-raw="{{ $task->task_type }}">{{ ucfirst((string) $task->task_type) }}</td>
                                <td data-hdv2-sort-value="status" data-hdv2-sort-raw="{{ $task->statusLabel() }}">
                                    <span class="hdv2-badge hdv2-status-{{ $taskStatusKey }}">{{ $task->statusLabel() }}</span>
                                </td>
                                <td>{{ $task->testing_task_description ?: '-' }}</td>
                                <td>{{ $task->remarks_by_developer ?: '-' }}</td>
                                <td data-hdv2-sort-value="assigned" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $assignedTimestamp }}">
                                    {{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td data-hdv2-sort-value="expected" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $expectedTimestamp }}">
                                    {{ $task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td data-hdv2-sort-value="completed" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $completedTimestamp }}">
                                    {{ $task->completed_on ? $task->completed_on->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $task->verified_on ? 'Sent to NIC Admin' : ($task->completed_on ? 'Senior Testing' : 'Waiting') }}</strong>
                                    <small>{{ $task->verified_on ? $task->verified_on->format('d/m/Y h:i A') : '-' }}</small>
                                </td>
                                <td data-hdv2-sort-value="updated" data-hdv2-sort-type="number" data-hdv2-sort-raw="{{ $updatedTimestamp }}">
                                    {{ $task->updated_at ? $task->updated_at->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('task-management.show', $task) }}" class="btn btn-primary btn-sm hdv2-view-sheet-btn">View Sheet</a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
