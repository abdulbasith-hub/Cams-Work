@extends('index2')

@section('title', 'Developer Task Tracker')

@section('content')
    @php
        $session = session('charge');
        // print_r($session);
        $minimumTaskDateTime = now('Asia/Kolkata')->format('Y-m-d H:i');
    @endphp
    @include('tickets.partials.app-theme')
    <link rel="stylesheet" href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <script src="../assets/js/jquery.js"></script>
    <script src="{{ asset('assets/js/extra-libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/daterangepicker.js') }}"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <style>
        .task-shell .stat-card {
            border: 1px solid #e6edf5;
            border-radius: 20px;
            padding: 20px 22px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            min-height: 132px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .task-shell .stat-card-label {
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .task-shell .stat-card-value {
            font-size: 2.2rem;
            line-height: 1;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .task-shell .stat-card-accent {
            width: 52px;
            height: 4px;
            border-radius: 999px;
            background: #cbd5e1;
        }

        .task-shell .stat-card-accent.assigned {
            background: #2563eb;
        }

        .task-shell .stat-card-accent.pending {
            background: #f59e0b;
        }

        .task-shell .stat-card-accent.completed {
            background: #16a34a;
        }

        .task-shell .developer-select-wrap {
            max-width: 340px;
        }

        .task-shell .admin-form-card {
            height: 100%;
        }

        .task-shell .developer-preview-card {
            border: 1px solid #d8e6fb;
            border-radius: 18px;
            padding: 16px 18px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            min-height: 112px;
        }

        .task-shell .developer-preview-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 1px minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
        }

        .task-shell .developer-preview-divider {
            width: 1px;
            background: linear-gradient(180deg, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0.35) 50%, rgba(37, 99, 235, 0.08) 100%);
            align-self: stretch;
        }

        .task-shell .developer-preview-section-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }

        .task-shell .developer-preview-meta-grid {
            display: grid;
            gap: 6px;
        }

        .task-shell .developer-preview-card.is-hidden {
            display: none;
        }

        .task-shell .developer-preview-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .task-shell .developer-preview-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2a37;
            margin-bottom: 0;
        }

        .task-shell .developer-preview-email {
            font-weight: 400;
            opacity: 0.65;
        }

        .task-shell .developer-preview-meta {
            color: #4b5563;
            margin-bottom: 4px;
        }

        .task-shell .developer-preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .task-shell .developer-preview-header .developer-preview-meta {
            margin-bottom: 0;
        }

        @media (max-width: 991.98px) {
            .task-shell .developer-preview-layout {
                grid-template-columns: 1fr;
            }

            .task-shell .developer-preview-divider {
                width: 100%;
                height: 1px;
            }
        }

        .task-shell .table-responsive {
            overflow-x: auto;
        }

        .task-shell .task-table {
            min-width: 100%;
            table-layout: fixed;
        }

        .task-shell .task-table th,
        .task-shell .task-table td {
            color: #000;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .task-shell .sortable-column {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 20px !important;
        }

        .task-shell .sortable-column::after {
            content: '';
        }

        .task-shell .testing-task-modal .modal-dialog {
            max-width: 1180px;
        }

        .task-shell .testing-task-empty {
            padding: 28px 16px;
            text-align: center;
            color: #6b7280;
        }

        .task-shell .dataTables_wrapper .dataTables_length,
        .task-shell .dataTables_wrapper .dataTables_filter,
        .task-shell .dataTables_wrapper .dataTables_info,
        .task-shell .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        .task-shell .pagination-wrap {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .task-shell .table-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .task-shell .custom-length-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.16);
            padding: 6px 10px;
            border-radius: 10px;
        }

        .task-shell .custom-length-label {
            color: #111010;

        }

        .task-shell .custom-length-select {
            min-width: 7px;
            border: 1px solid rgba(12, 10, 10, 0.45);
            border-radius: 4px;
            padding: 0pc;
            background: #fff;
            color: #111827;
            font-size: 0.85rem;
        }

        .task-shell .testing-table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
            margin-bottom: 14px;
            padding: 0 50px;
        }

        .task-shell .testing-search-input {
            width: 250px;
            min-width: 250px;
        }

        .task-shell .testing-search-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            flex: 0 0 auto;
        }

        .task-shell .testing-search-label {
            color: #111010;
            white-space: nowrap;
        }

        .task-shell .testing-table-toolbar .custom-length-wrap {
            flex: 0 0 auto;
            margin-right: auto;
        }

        @media (max-width: 767.98px) {
            .task-shell .testing-table-toolbar {
                flex-wrap: wrap;
                padding: 0;
            }

            .task-shell .testing-search-wrap {
                margin-left: 0;
            }
        }

        .task-shell .task-table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .task-shell .task-search-input {
            width: 250px;
            min-width: 250px;
        }

        .task-shell .task-search-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .task-shell .task-search-label {
            color: #111010;
            white-space: nowrap;
        }

        .task-shell #testingTasksTable {
            min-width: 1800px;
        }

        .task-shell #testingTasksTable th,
        .task-shell #testingTasksTable td {
            min-width: 120px;
            white-space: normal;
        }

        .task-shell #testingTasksTable .tester-status-col {
            min-width: 250px;
            width: 300px;
        }

        .task-shell .task-datetime-input,
        .task-shell #testingTasksTable .js-inline-datetime-picker {
            width: 100%;
            min-width: 185px;
        }

        .task-shell .datetime-display-note,
        .helpdesk-app-theme .js-datetime-display-text {
            white-space: nowrap;
            line-height: 1.25;
        }

        .task-shell .inline-field-error {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .task-shell .js-save-testing-cell.is-saved {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .task-shell .process-assigned-cell {
            display: grid;
            gap: 6px;
        }

        .task-shell .task-completed-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 0.75rem;
            font-weight: 700;
            width: fit-content;
            margin: 0 auto;
        }
    </style>
    <div class="helpdesk-app-theme task-shell">
        <div class="helpdesk-main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title"><i class="ti ti-layout-kanban text-primary"></i> Developer Task Tracker</h1>
                <a href="{{ route('helpdesk.dashboard.short') }}" class="btn btn-light">Back to Dashboard</a>
            </div>

            @if ($isNicAdmin)
                <div class="row g-5 mb-4">
                    <div class="col-lg-8">
                        <div class="card admin-form-card">
                            <div class="card-header ">
                                <h5 class="mb-0 text-light">Assign New Task</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('helpdesk.tasks.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="is_testing_task" value="0">
                                    <div class="row g-3 align-items-start mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Choose Developer</label>
                                            <div class="developer-select-wrap">
                                                <select name="developer_userid" id="developer_userid" class="form-select"
                                                    required>
                                                    <option value="">Select Developer</option>
                                                    @foreach ($developers as $developer)
                                                        <option value="{{ $developer->devuserid }}"
                                                            data-name="{{ $developer->devename }}"
                                                            data-email="{{ $developer->email }}"
                                                            data-count="{{ $developer->assigned_tasks_count }}"
                                                            data-pending-count="{{ $developer->pending_tasks_count }}"
                                                            data-completed-count="{{ $developer->completed_tasks_count }}"
                                                            data-ticket-count="{{ $developer->assigned_tickets_count }}"
                                                            data-ticket-pending-count="{{ $developer->pending_tickets_count }}"
                                                            data-ticket-completed-count="{{ $developer->completed_tickets_count }}"
                                                            {{ old('developer_userid') == $developer->devuserid ? 'selected' : '' }}>
                                                            {{ $developer->devename }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('developer_userid')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5 w-50">
                                            <div id="developerPreviewCard" class="developer-preview-card is-hidden">
                                                <center>
                                                    <div class="developer-preview-label"><b><u>Selected Developer</u></b>
                                                    </div>
                                                </center>
                                                <div class="developer-preview-header">
                                                    <div class="developer-preview-name"><span
                                                            id="developerPreviewName">-</span><span
                                                            class="developer-preview-email"
                                                            id="developerPreviewEmail"></span></div>
                                                </div>
                                                <div class="developer-preview-layout">
                                                    <div>

                                                        <div class="developer-preview-section-title">Task Details</div>
                                                        <div class="developer-preview-meta-grid">
                                                            <div class="developer-preview-meta"><strong>Assigned
                                                                    Tasks:</strong> <span
                                                                    id="developerPreviewCount">0</span></div>
                                                            <div class="developer-preview-meta"><strong>Pending
                                                                    Tasks:</strong> <span
                                                                    id="developerPreviewPendingCount">0</span></div>
                                                            <div class="developer-preview-meta"><strong>Completed
                                                                    Tasks:</strong> <span
                                                                    id="developerPreviewCompletedCount">0</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="developer-preview-divider" aria-hidden="true"></div>
                                                    <div>
                                                        <div class="developer-preview-section-title">Ticket Details</div>
                                                        <div class="developer-preview-meta-grid">
                                                            <div class="developer-preview-meta"><strong>Assigned
                                                                    Tickets:</strong> <span
                                                                    id="developerPreviewTicketCount">0</span></div>
                                                            <div class="developer-preview-meta"><strong>Pending
                                                                    Tickets:</strong> <span
                                                                    id="developerPreviewTicketPendingCount">0</span></div>
                                                            <div class="developer-preview-meta"><strong>Completed
                                                                    Tickets:</strong> <span
                                                                    id="developerPreviewTicketCompletedCount">0</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Process Assigned</label>
                                            <textarea name="process_assigned" rows="1" class="form-control" required>{{ old('process_assigned') }}</textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">New/Existing</label>
                                            <select name="task_type" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="new" {{ old('task_type') === 'new' ? 'selected' : '' }}>
                                                    New</option>
                                                <option value="existing"
                                                    {{ old('task_type') === 'existing' ? 'selected' : '' }}>Existing
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Expected Date</label>
                                            <input type="text" name="expected_date_to_complete"
                                                class="form-control task-datetime-input js-task-datetime-picker"
                                                data-min-datetime="{{ $minimumTaskDateTime }}"
                                                value="{{ old('expected_date_to_complete') }}" autocomplete="off"
                                                placeholder="DD/MM/YYYY HH:MM AM">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary text-light">Assign Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card admin-form-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-light">Assign Testing Task</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('helpdesk.tasks.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="is_testing_task" value="1">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Choose Developer</label>
                                            <div class="developer-select-wrap">
                                                <select name="developer_userid" class="form-select" required>
                                                    <option value="">Select Developer</option>
                                                    @foreach ($developers as $developer)
                                                        <option value="{{ $developer->devuserid }}">
                                                            {{ $developer->devename }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Process Assigned</label>
                                            <select name="process_assigned" class="form-select" required>
                                                <option value="">Select Pending Task</option>
                                                @foreach ($pendingTestingTasks as $pendingTestingTask)
                                                    <option value="{{ $pendingTestingTask->process_assigned }}">
                                                        {{ $pendingTestingTask->process_assigned }} -
                                                        {{ $pendingTestingTask->developer_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Task Type</label>
                                            <select name="task_type" class="form-select" required>
                                                <option value="existing" selected>Existing</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Expected Date</label>
                                            <input type="text" name="expected_date_to_complete"
                                                class="form-control task-datetime-input js-task-datetime-picker"
                                                data-min-datetime="{{ $minimumTaskDateTime }}"
                                                value="{{ old('expected_date_to_complete') }}" autocomplete="off"
                                                placeholder="DD/MM/YYYY HH:MM AM">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="testing_task_description" rows="2" class="form-control"
                                                placeholder="Enter testing task description">{{ old('testing_task_description') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary text-light">Assign Testing
                                            Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-light">{{ $isDeveloper ? 'Task List Assigned to Me' : 'Task List' }}</h5>
                    <div class="table-toolbar">
                        <a href="{{ route('helpdesk.tasks.dashboard') }}" class="btn btn-sm btn-primary text-light">Task
                            Dashboard</a>

                        @if ($isNicAdmin || $testingTasksCount > 0)
                            <button type="button" class="btn btn-sm btn-light position-relative"
                                id="openTestingTasksModal" data-bs-toggle="modal" data-bs-target="#testingTasksModal">
                                Testing Tasks
                                @if ($testingTasksCount > 0)
                                    <span class="badge rounded-pill bg-danger ms-1">{{ $testingTasksCount }}</span>
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="task-table-toolbar mb-3"
                        style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                        <div class="custom-length-wrap" style="display:flex; align-items:center; gap:8px;">
                            <label class="custom-length-label" for="developerTasksLength">Show</label>
                            <select id="developerTasksLength" class="form-select form-select-sm custom-length-select"
                                style="width:80px;">
                                @foreach ([10, 25, 50, 100] as $perPageOption)
                                    <option value="{{ $perPageOption }}"
                                        {{ (int) $taskPerPage === $perPageOption ? 'selected' : '' }}>{{ $perPageOption }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="custom-length-label">entries</span>
                        </div>
                        <div class="task-search-wrap">
                            <label class="task-search-label" for="developerTasksSearch">Search:</label>
                            <input type="search" id="developerTasksSearch"
                                class="form-control form-control-sm task-search-input" placeholder="Search.."
                                style="width:200px;">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="developerTasksTable"
                            class="table w-100 table-striped table-bordered display task-table developerTasksTable datatables-basic">

                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="0">S.No</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="1">Process Assigned</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="2">New/Existing</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="3">Assigned To</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="4">Assigned On</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="5">Expected Date to Complete</th>
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="6">Started On</th>
	                                    <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="7">Completed On</th> --}}
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="8">Remarks by Developer</th> --}}
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="9">Status of Task by the Tester</th> --}}
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="10">Remarks by the Project-Head</th>
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="11">Verifier Feedback</th> --}}
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="12">Verified By</th> --}}
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="13">Verified On</th>
                                    {{-- <th class="lang align-middle text-center text-wrap sortable-column" data-column-index="14">Remarks by Verifier</th> --}}
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="15">Approved By</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="16">Approved On</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="17">Hosted in Staging</th>
                                    <th class="lang align-middle text-center sortable-column" data-column-index="18">
                                        Deployed in Live Server</th>
                                    <th class="lang align-middle text-center text-wrap">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($tasks as $task)
                                    <tr>
                                        <td class="lang align-middle text-center text-wrap">{{ $loop->iteration }}</td>
                                        <td class="lang align-middletext-wrap">
                                            <div class="process-assigned-cell">
                                                <span>{{ $task->process_assigned }}</span>
                                                @if ($task->completed_on)
                                                    <span class="task-completed-badge">Completed</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ ucfirst($task->task_type) }}</td>
                                        <td class="lang align-middle text-center">{{ $task->developer_name }}</td>
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}</td>
                                        <td class="lang align-middle text-center ">
                                            {{ $task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-' }}
                                        </td>
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->started_on ? $task->started_on->format('d/m/Y h:i A') : '-' }}</td>
                                    <td class="lang align-middle text-center text-wrap">{{ $task->completed_on ? $task->completed_on->format('d/m/Y h:i A') : '-' }}</td> --}}
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->remarks_by_developer ?: '-' }}</td> --}}
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->task_status_by_tester ?: '-' }}</td> --}}
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->remarks_by_project_head ?: '-' }}</td>
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->verifier_feedback ?: '-' }}</td> --}}
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->verified_by ?: '-' }}</td> --}}
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->verified_on ? $task->verified_on->format('d/m/Y h:i A') : '-' }}</td>
                                        {{-- <td class="lang align-middle text-center text-wrap">{{ $task->remarks_by_verifier ?: '-' }}</td> --}}
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->approved_by ?: '-' }}</td>
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->approved_on ? $task->approved_on->format('d/m/Y h:i A') : '-' }}</td>
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->hosted_in_staging ? 'Yes' : 'No' }}</td>
                                        <td class="lang align-middle text-center text-wrap">
                                            {{ $task->deployed_in_live_server ? 'Yes' : 'No' }}</td>
                                        <td class="text-nowrap text-center">
                                            <a href="{{ route('helpdesk.tasks.show', $task) }}"
                                                class="btn btn-sm btn-outline-primary">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    {{ $tasks->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade testing-task-modal" id="testingTasksModal" tabindex="-1"
        aria-labelledby="testingTasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95%; width: 75%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testingTasksModalLabel">Testing Tasks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="testing-table-toolbar"
                        style="display:flex; justify-content:space-between; align-items:center; width:100%;">

                        <!-- Left Corner -->
                        <div class="custom-length-wrap" style="display:flex; align-items:center; gap:8px;">

                            <label class="custom-length-label" for="testingTasksLength">
                                Show
                            </label>

                            <select id="testingTasksLength" class="form-select form-select-sm custom-length-select"
                                style="width:80px;">

                                @foreach ([10, 25, 50, 100] as $perPageOption)
                                    <option value="{{ $perPageOption }}"
                                        {{ (int) $testingTaskPerPage === $perPageOption ? 'selected' : '' }}>
                                        {{ $perPageOption }}
                                    </option>
                                @endforeach

                            </select>

                            <span class="custom-length-label">entries</span>
                        </div>

                        <!-- Right Corner -->
                        <div class="testing-search-wrap" style="display:flex; align-items:center; gap:8px;">

                            <label class="testing-search-label" for="testingTasksSearch">
                                Search:
                            </label>

                            <input type="search" id="testingTasksSearch"
                                class="form-control form-control-sm testing-search-input" placeholder="Search.."
                                style="width:200px;">
                        </div>

                    </div>
                    <br>
                    <div class="table-responsive">
                        <table id="testingTasksTable"
                            class="table w-100 table-striped table-bordered display task-table testingTasksTable datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="0">S.No</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="1">Process Assigned</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="2">Description</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="3">Assigned To</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="4">Task Type</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="5">Assigned On</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="6">Expected Date</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="7">Started On</th>
                                    <th class="lang align-middle text-center text-wrap sortable-column"
                                        data-column-index="8">Completed On</th>
                                    <th class="lang align-middle text-center tester-status-col sortable-column"
                                        data-column-index="9">Tester Status</th>
                                    {{-- <th class="lang align-middle text-center text-wrap">Developer Remarks</th> --}}
                                </tr>
                            </thead>
                            <tbody id="testingTasksModalBody"></tbody>
                        </table>
                    </div>
                    <div id="testingTasksPagination" class="mt-3 d-flex justify-content-end"></div>
                </div>

            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = @json(csrf_token());
            const isDeveloperUser = @json($isDeveloper);
            const isNicAdminUser = @json($isNicAdmin);
            const testingTasksListUrl = @json(route('helpdesk.tasks.testing.list'));
            const defaultTestingTaskPerPage = @json($testingTaskPerPage);
            const minimumTaskDateTime = @json($minimumTaskDateTime);
            const developerTasksTable = document.getElementById('developerTasksTable');
            const developerTasksLength = document.getElementById('developerTasksLength');
            const developerTasksSearch = document.getElementById('developerTasksSearch');
            const testingTasksLength = document.getElementById('testingTasksLength');
            const testingTasksSearch = document.getElementById('testingTasksSearch');
            const testingTasksPagination = document.getElementById('testingTasksPagination');
            const developerSelect = document.getElementById('developer_userid');
            const previewCard = document.getElementById('developerPreviewCard');
            const previewName = document.getElementById('developerPreviewName');
            const previewEmail = document.getElementById('developerPreviewEmail');
            const previewCount = document.getElementById('developerPreviewCount');
            const previewPendingCount = document.getElementById('developerPreviewPendingCount');
            const previewCompletedCount = document.getElementById('developerPreviewCompletedCount');
            const previewTicketCount = document.getElementById('developerPreviewTicketCount');
            const previewTicketPendingCount = document.getElementById('developerPreviewTicketPendingCount');
            const previewTicketCompletedCount = document.getElementById('developerPreviewTicketCompletedCount');

            function filterTableRows(rows, query) {
                const normalizedQuery = query.trim().toLowerCase();

                rows.forEach(function(row) {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = normalizedQuery === '' || rowText.includes(normalizedQuery) ? '' :
                        'none';
                });
            }

            function applyDeveloperTaskFilters() {
                if (!developerTasksTable) {
                    return;
                }

                filterTableRows(
                    developerTasksTable.querySelectorAll('tbody tr'),
                    developerTasksSearch ? developerTasksSearch.value : ''
                );
            }

            function applyTestingTaskFilters() {
                filterTableRows(
                    document.querySelectorAll('#testingTasksTable tbody tr'),
                    testingTasksSearch ? testingTasksSearch.value : ''
                );
            }

            function normalizeSortValue(value) {
                const trimmedValue = String(value || '').trim();
                const numericValue = Number(trimmedValue.replace(/[^0-9.-]/g, ''));

                if (trimmedValue !== '' && !Number.isNaN(numericValue) && /^-?[0-9./:\sAPMYesNo]+$/i.test(
                        trimmedValue)) {
                    return numericValue;
                }

                const parsedDate = Date.parse(trimmedValue);
                if (!Number.isNaN(parsedDate)) {
                    return parsedDate;
                }

                return trimmedValue.toLowerCase();
            }

            function sortTable(table, columnIndex, direction) {
                if (!table) {
                    return;
                }

                const tbody = table.querySelector('tbody');
                if (!tbody) {
                    return;
                }

                const rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function(rowA, rowB) {
                    const valueA = normalizeSortValue(rowA.cells[columnIndex]?.textContent || '');
                    const valueB = normalizeSortValue(rowB.cells[columnIndex]?.textContent || '');

                    if (valueA < valueB) {
                        return direction === 'asc' ? -1 : 1;
                    }

                    if (valueA > valueB) {
                        return direction === 'asc' ? 1 : -1;
                    }

                    return 0;
                });

                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            }

            function bindSortableHeaders(tableSelector) {
                const table = document.querySelector(tableSelector);
                if (!table) {
                    return;
                }

                table.querySelectorAll('.sortable-column').forEach(function(header) {
                    header.addEventListener('click', function() {
                        const columnIndex = Number(header.dataset.columnIndex || '-1');
                        if (columnIndex < 0) {
                            return;
                        }

                        const currentDirection = header.dataset.sortDirection === 'asc' ? 'asc' :
                            'desc';
                        const nextDirection = currentDirection === 'asc' ? 'desc' : 'asc';

                        table.querySelectorAll('.sortable-column').forEach(function(otherHeader) {
                            if (otherHeader !== header) {
                                otherHeader.removeAttribute('data-sort-direction');
                            }
                        });

                        header.dataset.sortDirection = nextDirection;
                        sortTable(table, columnIndex, nextDirection);

                        if (tableSelector === '#developerTasksTable') {
                            applyDeveloperTaskFilters();
                        } else {
                            applyTestingTaskFilters();
                        }
                    });
                });
            }

            function updateQueryParameter(url, key, value, pageKey = null) {
                const nextUrl = new URL(url, window.location.origin);
                nextUrl.searchParams.set(key, value);

                if (pageKey) {
                    nextUrl.searchParams.set(pageKey, '1');
                }

                return nextUrl.toString();
            }

            if (developerTasksLength) {
                developerTasksLength.addEventListener('change', function() {
                    window.location.href = updateQueryParameter(window.location.href, 'task_per_page', this
                        .value, 'task_page');
                });
            }

            if (developerTasksSearch) {
                developerTasksSearch.addEventListener('input', applyDeveloperTaskFilters);
            }
            bindSortableHeaders('#developerTasksTable');

            function updateDeveloperPreview() {
                if (!developerSelect || !previewCard || !previewName || !previewEmail || !previewCount || !
                    previewPendingCount || !previewCompletedCount || !previewTicketCount || !
                    previewTicketPendingCount || !previewTicketCompletedCount) {
                    return;
                }

                const selectedOption = developerSelect.options[developerSelect.selectedIndex];
                const developerId = selectedOption ? selectedOption.value : '';

                if (!developerId) {
                    previewCard.classList.add('is-hidden');
                    previewName.textContent = '-';
                    previewEmail.textContent = '';
                    previewCount.textContent = '0';
                    previewPendingCount.textContent = '0';
                    previewCompletedCount.textContent = '0';
                    previewTicketCount.textContent = '0';
                    previewTicketPendingCount.textContent = '0';
                    previewTicketCompletedCount.textContent = '0';
                    return;
                }

                const developerName = selectedOption.dataset.name || '-';
                const developerEmail = selectedOption.dataset.email || '';
                previewName.textContent = developerName;
                previewEmail.textContent = developerEmail ? ` (${developerEmail})` : '';
                previewCount.textContent = selectedOption.dataset.count || '0';
                previewPendingCount.textContent = selectedOption.dataset.pendingCount || '0';
                previewCompletedCount.textContent = selectedOption.dataset.completedCount || '0';
                previewTicketCount.textContent = selectedOption.dataset.ticketCount || '0';
                previewTicketPendingCount.textContent = selectedOption.dataset.ticketPendingCount || '0';
                previewTicketCompletedCount.textContent = selectedOption.dataset.ticketCompletedCount || '0';
                previewCard.classList.remove('is-hidden');
            }

            if (developerSelect) {
                developerSelect.addEventListener('change', updateDeveloperPreview);
            }
            updateDeveloperPreview();

            const openTestingTasksModal = document.getElementById('openTestingTasksModal');
            const testingTasksModal = document.getElementById('testingTasksModal');
            const testingTasksModalBody = document.getElementById('testingTasksModalBody');

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function initializeTaskDateTimePickers(scope) {
                if (typeof $ === 'undefined' || typeof $.fn.daterangepicker !== 'function' || typeof moment ===
                    'undefined') {
                    return;
                }

                $(scope).find('.js-task-datetime-picker, .js-inline-datetime-picker').each(function() {
                    const $input = $(this);

                    if ($input.data('daterangepicker')) {
                        return;
                    }

                    const options = {
                        singleDatePicker: true,
                        timePicker: true,
                        timePicker24Hour: true,
                        timePickerIncrement: 1,
                        autoUpdateInput: false,
                        autoApply: true,
                        locale: {
                            format: 'DD/MM/YYYY HH:mm:A',
                            cancelLabel: 'Clear',
                        },
                    };

                    const minDateValue = $input.attr('data-min-datetime');
                    if (minDateValue) {
                        const parsedMinDate = moment(minDateValue, 'YYYY-MM-DD HH:mm', true);
                        if (parsedMinDate.isValid()) {
                            options.minDate = parsedMinDate;
                        }
                    }

                    const existingValue = $input.val();
                    const parsedExistingValue = existingValue ?
                        moment(existingValue, ['DD/MM/YYYY HH:mm:A', 'DD/MM/YYYY HH:mm A',
                            'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'
                        ], true) :
                        null;

                    if (parsedExistingValue && parsedExistingValue.isValid()) {
                        options.startDate = parsedExistingValue;
                    }

                    $input.daterangepicker(options);

                    $input.on('apply.daterangepicker', function(event, picker) {
                        $input.val(picker.startDate.format('DD/MM/YYYY HH:mm:A')).trigger('input');
                    });

                    $input.on('cancel.daterangepicker', function() {
                        $input.val('').trigger('input');
                    });

                    if (parsedExistingValue && parsedExistingValue.isValid()) {
                        $input.val(parsedExistingValue.format('DD/MM/YYYY HH:mm:A'));
                    }
                });
            }

            function renderReadonlyBlock(value) {
                return `<div class="small">${escapeHtml(value)}</div>`;
            }

            function formatPickerValue(value) {
                if (!value) {
                    return '';
                }

                if (typeof moment !== 'undefined') {
                    const parsedValue = moment(value, ['DD/MM/YYYY HH:mm:A', 'DD/MM/YYYY HH:mm A',
                        'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'
                    ], true);
                    if (parsedValue.isValid()) {
                        return parsedValue.format('DD/MM/YYYY HH:mm:A');
                    }
                }

                return String(value).replace('T', ' ');
            }

            function formatDateTimeDisplay(value) {
                if (!value) {
                    return '-';
                }

                if (typeof moment !== 'undefined') {
                    const parsedValue = moment(value, ['DD/MM/YYYY HH:mm:A', 'DD/MM/YYYY HH:mm A',
                        'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'
                    ], true);
                    if (parsedValue.isValid()) {
                        return parsedValue.format('DD/MM/YYYY HH:mm:A');
                    }
                }

                return String(value).replace('T', ' ');
            }

            function renderReadonlyInput(value, type = 'text') {
                if (type === 'textarea') {
                    return `<textarea class="form-control form-control-sm" rows="2" disabled>${escapeHtml(value)}</textarea>`;
                }

                return `<input type="${type}" class="form-control form-control-sm" value="${escapeHtml(value)}" disabled>`;
            }

            function renderDateDisplayNote(value) {
                const formattedValue = formatDateTimeDisplay(value);
                if (!value || formattedValue === '-') {
                    return '';
                }

                return `<div class="small text-muted mt-1 datetime-display-note">${escapeHtml(formattedValue)}</div>`;
            }

            function getInlineFieldSelector(saveField) {
                const selectorMap = {
                    expected_date_to_complete: '.js-inline-expected-date',
                    started_on: '.js-inline-started-on',
                    completed_on: '.js-inline-completed-on',
                    task_status_by_tester: '.js-inline-tester-status',
                };

                return selectorMap[saveField] || null;
            }

            function clearInlineFieldError(field) {
                if (!field) {
                    return;
                }

                field.classList.remove('is-invalid');
                const wrapper = field.closest('.d-flex');
                const errorNode = wrapper?.parentElement?.querySelector('.inline-field-error');
                if (errorNode) {
                    errorNode.remove();
                }
            }

            function showInlineFieldError(field, message) {
                if (!field) {
                    return;
                }

                clearInlineFieldError(field);
                field.classList.add('is-invalid');

                const wrapper = field.closest('.d-flex');
                if (!wrapper || !wrapper.parentElement) {
                    return;
                }

                const errorNode = document.createElement('div');
                errorNode.className = 'inline-field-error';
                errorNode.textContent = message;
                wrapper.parentElement.appendChild(errorNode);
            }

            function validateInlineSaveField(row, saveField) {
                const selector = getInlineFieldSelector(saveField);
                if (!selector) {
                    return true;
                }

                const field = row.querySelector(selector);
                if (!field) {
                    return true;
                }

                const value = field.value.trim();
                if (value === '') {
                    showInlineFieldError(field, 'This field is required.');
                    return false;
                }

                clearInlineFieldError(field);
                return true;
            }

            function renderEditableCell(fieldClass, fieldValue, fieldType, canEdit, saveField) {
                if (!canEdit) {
                    if (fieldType === 'datetime-local') {
                        return renderReadonlyInput(formatDateTimeDisplay(fieldValue), 'text');
                    }

                    return renderReadonlyInput(fieldValue, fieldType === 'datetime-local' ? 'text' : fieldType);
                }

                if (fieldType === 'textarea') {
                    return `
	                                <div class="d-flex align-items-start gap-2">
	                                    <textarea class="form-control form-control-sm ${fieldClass}" rows="2">${escapeHtml(fieldValue)}</textarea>
	                                    <button type="button" class="btn btn-sm btn-outline-success js-save-testing-cell" data-field="${saveField}" title="Save">
	                                        <i class="ti ti-check"></i>
	                                    </button>
	                                </div>
	                            `;
                }

                return `
		                            <div class="d-flex align-items-center gap-2">
		                                <input type="${fieldType === 'datetime-local' ? 'text' : fieldType}" class="form-control form-control-sm ${fieldClass} ${fieldType === 'datetime-local' ? 'js-inline-datetime-picker' : ''}" value="${escapeHtml(fieldValue ? formatPickerValue(fieldValue) : '')}" ${fieldType === 'datetime-local' ? `${saveField === 'expected_date_to_complete' ? `data-min-datetime="${escapeHtml(minimumTaskDateTime)}"` : ''} autocomplete="off" placeholder="DD/MM/YYYY HH:MM AM"` : ''}>
		                                <button type="button" class="btn btn-sm btn-outline-success js-save-testing-cell" data-field="${saveField}" title="Save">
		                                    <i class="ti ti-check"></i>
		                                </button>
		                            </div>
		                                ${fieldType === 'datetime-local' ? renderDateDisplayNote(fieldValue) : ''}
		                        `;
            }

            function renderTestingTaskRows(tasks) {
                if (!tasks.length) {
                    testingTasksModalBody.innerHTML = '';
                    return;
                }

                testingTasksModalBody.innerHTML = tasks.map(function(task, index) {
                    const canEditAdminFields = isNicAdminUser;
                    const canEditDeveloperFields = isDeveloperUser;

                    const processField = renderReadonlyBlock(task.process_assigned);
                    const descriptionField = renderReadonlyBlock(task.testing_task_description);
                    const taskTypeField = renderReadonlyBlock(task.task_type);
                    const expectedDateField = renderEditableCell('js-inline-expected-date', task
                        .expected_date_to_complete_value, 'datetime-local', canEditAdminFields,
                        'expected_date_to_complete');
                    const startedOnField = renderEditableCell('js-inline-started-on', task.started_on_value,
                        'datetime-local', canEditDeveloperFields, 'started_on');
                    const completedOnField = renderEditableCell('js-inline-completed-on', task
                        .completed_on_value, 'datetime-local', canEditDeveloperFields, 'completed_on');
                    const testerStatusField = renderEditableCell('js-inline-tester-status', task
                        .task_status_by_tester_value, 'textarea', canEditDeveloperFields,
                        'task_status_by_tester');
                    const developerRemarksField = renderReadonlyBlock(task.remarks_by_developer);

                    return `
		                        <tr
                                    data-update-url="${escapeHtml(task.update_url)}"
                                    data-remarks-by-project-head="${escapeHtml(task.remarks_by_project_head_value)}"
                                    data-verifier-feedback="${escapeHtml(task.verifier_feedback_value)}"
                                    data-verified-by="${escapeHtml(task.verified_by_value)}"
                                    data-verified-on="${escapeHtml(task.verified_on_value)}"
                                    data-remarks-by-verifier="${escapeHtml(task.remarks_by_verifier_value)}"
                                    data-approved-by="${escapeHtml(task.approved_by_value)}"
                                    data-approved-on="${escapeHtml(task.approved_on_value)}"
                                    data-hosted-in-staging="${task.hosted_in_staging_value ? '1' : '0'}"
	                                    data-deployed-in-live-server="${task.deployed_in_live_server_value ? '1' : '0'}"
                                        data-process-assigned="${escapeHtml(task.process_assigned)}"
                                        data-task-type="${escapeHtml(task.task_type_value)}"
                                        data-testing-task-description="${escapeHtml(task.testing_task_description_value)}"
                                        data-remarks-by-developer="${escapeHtml(task.remarks_by_developer_value)}"
	                                >
				                            <td class="align-middle text-center text-wrap">${index + 1}</td>
		                            <td class="align-middle text-center text-wrap">${processField}</td>
		                            <td class="align-middle text-center text-wrap">${descriptionField}</td>
		                            <td class="align-middle text-center text-wrap">${escapeHtml(task.developer_name)}</td>
		                            <td class="align-middle text-center text-wrap">${taskTypeField}</td>
	                            <td class="align-middle text-center text-wrap">${escapeHtml(task.assigned_on)}</td>
	                            <td class="align-middle text-center text-wrap">${expectedDateField}</td>
	                            <td class="align-middle text-center text-wrap">${startedOnField}</td>
	                            <td class="align-middle text-center text-wrap">${completedOnField}</td>
	                            <td class="align-middle text-center tester-status-col text-wrap">${testerStatusField}</td>
		                        </tr>
		                    `;
                }).join('');

                initializeTaskDateTimePickers(testingTasksModalBody);

            }

            function bindTestingTableSearch() {
                if (!testingTasksSearch) {
                    return;
                }

                testingTasksSearch.oninput = applyTestingTaskFilters;
            }

            function loadTestingTasks(url = null) {
                testingTasksModalBody.innerHTML = '';
                if (testingTasksPagination) {
                    testingTasksPagination.innerHTML = '';
                }

                const requestUrl = new URL(url || testingTasksListUrl, window.location.origin);
                requestUrl.searchParams.set('testing_task_per_page', testingTasksLength ? testingTasksLength.value :
                    defaultTestingTaskPerPage);

                return fetch(requestUrl.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(async function(response) {
                        if (!response.ok) {
                            throw new Error('Unable to load testing tasks.');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        renderTestingTaskRows(Array.isArray(data.tasks) ? data.tasks : []);
                        if (testingTasksPagination) {
                            testingTasksPagination.innerHTML = data.pagination || '';
                        }
                        bindTestingTableSearch();
                        bindSortableHeaders('#testingTasksTable');
                        applyTestingTaskFilters();
                    })
                    .catch(function() {
                        testingTasksModalBody.innerHTML = '';
                        if (testingTasksPagination) {
                            testingTasksPagination.innerHTML = '';
                        }
                    });
            }

            if (testingTasksModal) {
                testingTasksModal.addEventListener('show.bs.modal', function() {
                    loadTestingTasks();
                });
            }

            initializeTaskDateTimePickers(document);

            if (testingTasksLength) {
                testingTasksLength.addEventListener('change', function() {
                    loadTestingTasks();
                });
            }

            if (testingTasksPagination) {
                testingTasksPagination.addEventListener('click', function(event) {
                    const link = event.target.closest('a');
                    if (!link) {
                        return;
                    }

                    event.preventDefault();
                    loadTestingTasks(link.href);
                });
            }

            testingTasksModalBody.addEventListener('click', function(event) {
                const saveButton = event.target.closest('.js-save-testing-cell');

                if (!saveButton) {
                    return;
                }

                const row = saveButton.closest('tr');
                const updateUrl = row?.dataset.updateUrl;

                if (!row || !updateUrl) {
                    return;
                }

                if (!validateInlineSaveField(row, saveButton.dataset.field || '')) {
                    return;
                }

                const payload = new URLSearchParams({
                    _token: csrfToken,
                    process_assigned: row.dataset.processAssigned || '',
                    task_type: row.dataset.taskType || 'existing',
                    is_testing_task: '1',
                    testing_task_description: row.dataset.testingTaskDescription || '',
                    expected_date_to_complete: row.querySelector('.js-inline-expected-date')
                        ?.value ?? '',
                    started_on: row.querySelector('.js-inline-started-on')?.value ?? '',
                    completed_on: row.querySelector('.js-inline-completed-on')?.value ?? '',
                    task_status_by_tester: row.querySelector('.js-inline-tester-status')?.value ??
                        '',
                    remarks_by_developer: row.dataset.remarksByDeveloper || '',
                    remarks_by_project_head: row.dataset.remarksByProjectHead || '',
                    verifier_feedback: row.dataset.verifierFeedback || '',
                    verified_by: row.dataset.verifiedBy || '',
                    verified_on: row.dataset.verifiedOn || '',
                    remarks_by_verifier: row.dataset.remarksByVerifier || '',
                    approved_by: row.dataset.approvedBy || '',
                    approved_on: row.dataset.approvedOn || '',
                    hosted_in_staging: row.dataset.hostedInStaging === '1' ? '1' : '0',
                    deployed_in_live_server: row.dataset.deployedInLiveServer === '1' ? '1' : '0',
                });

                saveButton.disabled = true;
                const icon = saveButton.innerHTML;
                saveButton.innerHTML = '...';

                fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: payload.toString(),
                    })
                    .then(async function(response) {
                        if (!response.ok) {
                            const errorData = await response.json().catch(function() {
                                return {};
                            });

                            const firstError = errorData?.errors ?
                                Object.values(errorData.errors)[0]?.[0] :
                                'Unable to update testing task.';

                            throw new Error(firstError || 'Unable to update testing task.');
                        }

                        saveButton.classList.remove('btn-outline-success');
                        saveButton.classList.add('is-saved');
                        saveButton.innerHTML = '<i class="ti ti-checks"></i>';
                        setTimeout(function() {
                            saveButton.disabled = false;
                            saveButton.classList.remove('is-saved');
                            saveButton.classList.add('btn-outline-success');
                            saveButton.innerHTML = icon;
                        }, 1200);
                    })
                    .catch(function(error) {
                        saveButton.disabled = false;
                        saveButton.classList.remove('is-saved');
                        saveButton.classList.add('btn-outline-success');
                        saveButton.innerHTML = icon;
                        alert(error.message);
                    });
            });

            testingTasksModalBody.addEventListener('input', function(event) {
                const input = event.target.closest(
                    '.js-inline-expected-date, .js-inline-started-on, .js-inline-completed-on, .js-inline-tester-status'
                );
                if (!input) {
                    return;
                }

                clearInlineFieldError(input);

                const container = input.closest('td');
                const displayNote = container ? container.querySelector('.datetime-display-note') : null;
                if (!displayNote || input.tagName !== 'INPUT') {
                    return;
                }

                displayNote.textContent = input.value ? formatDateTimeDisplay(input.value) : '';
            });

        });
    </script>
@endsection
