@extends('index2')

@section('title', 'Task Sheet')

@section('content')
    @include('common.alert')
    @include('helpdesk-v2.partials.assets')
    @php
        $assignedInput = $task->assigned_on ? $task->assigned_on->format('Y-m-d\TH:i') : '';
        $expectedInput = $task->expected_date_to_complete ? $task->expected_date_to_complete->format('Y-m-d\TH:i') : '';
        $developerStatus = $task->completed_on ? 'Completed' : ($task->started_on ? 'In Progress' : 'Pending');
        $developerStatusKey = $task->completed_on ? 'completed' : ($task->started_on ? 'in_progress' : 'pending');
        $testingStatus = $task->verified_on ? 'Sent to NIC Admin' : ($task->completed_on ? 'Senior Testing' : 'Waiting');
        $historyItems = collect($histories ?? []);
        $assignmentHistory = $historyItems->firstWhere('action_key', 'assigned');
        $rawAssignmentText = $assignmentHistory->comment
            ?? ('Assigned to '.($task->developer_name ?: 'Developer').' by '.($task->assigned_by_name ?: 'NIC Admin').'.');
        $assignmentLines = collect(preg_split('/\r\n|\r|\n/', (string) $rawAssignmentText))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();
        $assignmentText = $assignmentLines
            ->reject(fn ($line) => str_starts_with($line, 'Description added by '))
            ->first() ?: ('Assigned to '.($task->developer_name ?: 'Developer').' by '.($task->assigned_by_name ?: 'NIC Admin').'.');
        $legacyDescriptionItems = $assignmentLines
            ->filter(fn ($line) => str_starts_with($line, 'Description added by '))
            ->map(fn ($line) => (object) [
                'action_key' => 'description',
                'stage' => 'Description',
                'status' => 'Added',
                'comment' => $line,
                'performed_by_name' => null,
                'performed_by_role' => str_contains($line, '(NIC Admin)') ? 'NIC Admin' : null,
                'performed_at' => null,
            ]);
        $timelineItems = collect([$assignmentHistory ?: (object) [
                'action_key' => 'assigned',
                'stage' => 'Assignment',
                'status' => 'Completed',
                'comment' => $assignmentText,
                'performed_by_name' => $task->assigned_by_name ?: 'NIC Admin',
                'performed_by_role' => 'NIC Admin',
                'performed_at' => $task->assigned_on,
            ]])
            ->map(function ($history) use ($assignmentText) {
                if ($history && $history->action_key === 'assigned') {
                    $history->comment = $assignmentText;
                }

                return $history;
            })
            ->concat($legacyDescriptionItems)
            ->concat($historyItems->reject(fn ($history) => $history->action_key === 'assigned'))
            ->filter()
            ->values();
    @endphp

    <div class="hdv2 hdv2-detail-page hdv2-task-detail-page">
        @include('helpdesk-v2.partials.flashes', ['compactErrors' => true])

        <div class="hdv2-task-head">
            <div>
                <div class="hdv2-eyebrow">Task Management</div>
                <h1>Task Sheet</h1>
            </div>
            {{-- <div class="hdv2-actions">
                <a href="{{ route('helpdesk-v2.dashboard', ['role' => \App\Services\HelpdeskV2Session::role(), 'pane' => 'tasks']) }}" class="btn btn-light">Back to Task List</a>
            </div> --}}
        </div>

        <div class="hdv2-task-layout">
            <main class="hdv2-task-main">
                <section class="hdv2-task-section hdv2-task-record-section">
                    <div class="hdv2-task-section-head">
                        <h2>Task Information</h2>
                        @if ($canComplete)
                            <button type="button" class="btn btn-primary btn-sm" data-task-sheet-popup-open="#taskDeveloperStatusTemplate" data-task-sheet-popup-title="Update Status">Update Status</button>
                        @endif
                    </div>

                    <div class="hdv2-task-sheet">
                        <div class="hdv2-task-brief">
                            <div class="hdv2-task-title">
                                <span>{{ $task->developer_name ?: 'Developer' }}</span>
                                <h3>{{ $task->process_assigned }}</h3>
                            </div>
                            <div class="hdv2-task-state">
                                <span class="hdv2-badge hdv2-status-{{ $task->statusKey() }}">{{ $task->statusLabel() }}</span>
                                <small>{{ ucfirst((string) $task->task_type) }} Task</small>
                            </div>
                        </div>

                        <div class="hdv2-task-record-grid">
                            <div class="hdv2-task-record-item">
                                <span>Assigned Developer</span>
                                <strong>{{ $task->developer_name ?: '-' }}</strong>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Developer Status</span>
                                <strong>{{ $developerStatus }}</strong>
                                <small>{{ $task->completed_on ? $task->completed_on->format('d/m/Y h:i A') : 'Completion not marked' }}</small>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Assigned On</span>
                                <strong>{{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}</strong>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Expected On</span>
                                <strong>{{ $task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-' }}</strong>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Assigned By</span>
                                <strong>{{ $task->assigned_by_name ?: '-' }}</strong>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Last Updated</span>
                                <strong>{{ $task->updated_at ? $task->updated_at->format('d/m/Y h:i A') : '-' }}</strong>
                            </div>
                            <div class="hdv2-task-record-item">
                                <span>Testing</span>
                                <strong>{{ $testingStatus }}</strong>
                                <small>{{ $task->verified_on ? $task->verified_on->format('d/m/Y h:i A') : 'Not sent to NIC Admin' }}</small>
                            </div>
                        </div>

                        <div class="hdv2-task-detail-lines">
                            <section>
                                <h4>Description</h4>
                                <p>{{ $task->testing_task_description ?: '-' }}</p>
                            </section>
                            <section>
                                <h4>Latest Developer Note</h4>
                                <p>{{ $task->remarks_by_developer ?: 'No developer update yet.' }}</p>
                            </section>
                        </div>
                    </div>
                </section>

                <section class="hdv2-task-section hdv2-task-flow-section">
                    <div class="hdv2-task-section-head">
                        <h2>Task Flow</h2>
                        <div>
                            <span class="hdv2-flow-current">{{ $task->statusLabel() }}</span>
                        </div>
                    </div>

                    <div class="hdv2-flow-map hdv2-flow-map-compact">
                        <div class="hdv2-flow-step is-done" title="NIC Admin Assignment">
                            <span class="hdv2-flow-dot"></span>
                            <span class="hdv2-flow-number">1</span>
                            <strong>NIC Admin</strong>
                            <small>Assigned</small>
                            <em>{{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}</em>
                        </div>
                        <div class="hdv2-flow-step is-{{ $task->completed_on ? 'done' : 'active' }}" title="Developer Work">
                            <span class="hdv2-flow-dot"></span>
                            <span class="hdv2-flow-number">2</span>
                            <strong>{{ $task->developer_name ?: 'Developer' }}</strong>
                            <small>{{ $developerStatus }}</small>
                            <em>{{ $task->completed_on ? $task->completed_on->format('d/m/Y h:i A') : ($task->started_on ? $task->started_on->format('d/m/Y h:i A') : '-') }}</em>
                        </div>
                        <div class="hdv2-flow-step is-{{ $task->verified_on ? 'done' : ($task->completed_on ? 'active' : 'pending') }}" title="Testing Stage">
                            <span class="hdv2-flow-dot"></span>
                            <span class="hdv2-flow-number">3</span>
                            <strong>Senior Testing</strong>
                            <small>{{ $task->verified_on ? 'Verified' : ($task->completed_on ? 'Ready for Testing' : 'Waiting') }}</small>
                            <em>{{ $task->verified_on ? $task->verified_on->format('d/m/Y h:i A') : ($task->completed_on ? $task->completed_on->format('d/m/Y h:i A') : '-') }}</em>
                        </div>
                        <div class="hdv2-flow-step is-{{ $task->verified_on ? 'active' : 'pending' }}" title="NIC Admin Review">
                            <span class="hdv2-flow-dot"></span>
                            <span class="hdv2-flow-number">4</span>
                            <strong>NIC Admin</strong>
                            <small>{{ $task->verified_on ? 'Review Pending' : 'Waiting' }}</small>
                            <em>{{ $task->verified_on ? $task->verified_on->format('d/m/Y h:i A') : '-' }}</em>
                        </div>
                    </div>

                    <div class="hdv2-task-timeline">
                        <div class="hdv2-task-timeline-head">
                            <span>Timeline Details</span>
                            <div class="hdv2-task-timeline-actions">
                                <button type="button" class="btn btn-primary btn-sm" data-task-sheet-popup-open="#taskDescriptionTemplate" data-task-sheet-popup-title="Add Description">Add Description</button>
                                @if ($canEditSchedule)
                                    <button type="button" class="btn btn-light btn-sm" data-task-sheet-popup-open="#taskScheduleTemplate" data-task-sheet-popup-title="Update Sheet">Update Sheet</button>
                                @endif
                                <strong>{{ $timelineItems->count() }}</strong>
                            </div>
                        </div>
                        @if ($timelineItems->isNotEmpty())
                            <ol class="hdv2-task-timeline-list">
                                @foreach ($timelineItems as $history)
	                                    @php
	                                        $isNicAdminNote = $history->action_key === 'description'
	                                            && strcasecmp((string) $history->performed_by_role, 'NIC Admin') === 0;
                                            $visibleTo = collect(data_get($history, 'metadata.visible_to', []))
                                                ->filter()
                                                ->implode(', ');
	                                    @endphp
                                    <li class="{{ $isNicAdminNote ? 'is-nic-admin' : '' }}">
                                        <span class="hdv2-task-timeline-number">{{ $loop->iteration }}</span>
                                        <div class="hdv2-task-timeline-content">
                                            <div>
                                                <span>
                                                    {{ $history->stage ?: 'Task Update' }}
                                                    @if ($isNicAdminNote)
                                                        <b>NIC Admin Comment</b>
                                                    @endif
                                                </span>
                                                <strong>{{ $history->status ?: '-' }}</strong>
                                            </div>
                                            <p>{{ $history->comment ?: '-' }}</p>
                                            <small>
                                                {{ $history->performed_by_name ?: '-' }}
                                                @if ($history->performed_by_role)
                                                    ({{ $history->performed_by_role }})
                                                @endif
	                                            | {{ $history->performed_at ? $history->performed_at->format('d/m/Y h:i A') : '-' }}
	                                        </small>
                                            @if ($visibleTo !== '')
                                                <em class="hdv2-task-audience">Visible to {{ $visibleTo }}</em>
                                            @endif
	                                    </div>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="hdv2-task-timeline-empty">No timeline details added yet.</p>
                        @endif
                    </div>
                </section>
            </main>

            @if ($canVerifyTesting)
                <aside class="hdv2-task-action-dock">
                    <section class="hdv2-task-action-section hdv2-task-testing-action">
                        <h2>Senior Testing</h2>
                        <form method="POST" action="{{ route('task-management.verify-testing', $task) }}" class="hdv2-form hdv2-task-inline-form">
                            @csrf
                            <label>Testing Description <span class="hdv2-required">*</span>
                                <textarea name="testing_description" rows="4" placeholder="Add testing result and send to NIC Admin" required>{{ old('testing_description') }}</textarea>
                            </label>
                            <div class="hdv2-form-actions">
                                <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Send to NIC Admin</button>
                            </div>
                        </form>
                    </section>
                </aside>
            @endif
        </div>

        @if ($canComplete)
            <template id="taskDeveloperStatusTemplate">
                <div class="hdv2-task-popup-body">
                    <form method="POST" action="{{ route('task-management.complete', $task) }}" class="hdv2-form hdv2-task-inline-form">
                        @csrf
                        <label>Status
                            <select name="developer_status" required>
                                <option value="in_progress" @selected(old('developer_status', $developerStatusKey) === 'in_progress')>In Progress</option>
                                <option value="completed" @selected(old('developer_status') === 'completed')>Completed and Move to Testing</option>
                            </select>
                        </label>
                        <label>Status Description <span class="hdv2-required">*</span>
                            <textarea name="remarks_by_developer" rows="4" required>{{ old('remarks_by_developer', $task->remarks_by_developer) }}</textarea>
                        </label>
                        <div class="hdv2-form-actions">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Save Status</button>
                        </div>
                    </form>
                </div>
            </template>
        @endif

        <template id="taskDescriptionTemplate">
            <div class="hdv2-task-popup-body">
                <form method="POST" action="{{ route('task-management.descriptions.store', $task) }}" class="hdv2-form hdv2-task-inline-form">
                    @csrf
                    <label>Description <span class="hdv2-required">*</span>
                        <textarea name="description_note" rows="4" placeholder="Add task update, clarification, or note" required>{{ old('description_note') }}</textarea>
                    </label>
                    <div class="hdv2-form-actions">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Add Description</button>
                    </div>
                </form>
            </div>
        </template>

        @if ($canEditSchedule)
            <template id="taskScheduleTemplate">
                <div class="hdv2-task-popup-body">
                    <form method="POST" action="{{ route('task-management.schedule', $task) }}" class="hdv2-form hdv2-task-inline-form">
                        @csrf
                        <label>Assigned Date
                            <input type="datetime-local" name="assigned_on" value="{{ old('assigned_on', $assignedInput) }}" required>
                        </label>
                        <label>Expected Date
                            <input type="datetime-local" name="expected_date_to_complete" value="{{ old('expected_date_to_complete', $expectedInput) }}">
                        </label>
                        <label>Schedule Note
                            <textarea name="comment" rows="3" placeholder="Add schedule change reason">{{ old('comment') }}</textarea>
                        </label>
                        <div class="hdv2-form-actions">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Update Sheet</button>
                        </div>
                    </form>
                </div>
            </template>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showCommonLargeModal(title, html) {
                var body = document.getElementById('large_alert_body');
                var header = document.getElementById('large_alert_header');
                var modal = document.getElementById('large_confirmation_alert');

                if (!body || !header || !modal) {
                    return;
                }

                header.innerHTML = title || 'Task Update';
                body.innerHTML = html;

                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                }
            }

            function openTaskSheetPopup(selector, title) {
                var template = document.querySelector(selector);

                if (!template) {
                    return;
                }

                if (typeof passing_large_alert === 'function') {
                    try {
                        passing_large_alert(title || 'Task Update', template.innerHTML, 'large_confirmation_alert', 'large_alert_header', 'large_alert_body', 'forward_alert');
                    } catch (error) {
                        showCommonLargeModal(title, template.innerHTML);
                    }
                } else {
                    showCommonLargeModal(title, template.innerHTML);
                }

                document.querySelector('#large_confirmation_alert .modal-footer')?.style.setProperty('display', 'none');
                document.getElementById('large_confirmation_alert')?.classList.add('hdv2-task-modal');
                document.getElementById('large_alert_body')?.classList.add('hdv2-task-common-popup');
            }

            document.querySelectorAll('[data-task-sheet-popup-open]').forEach(function (button) {
                button.addEventListener('click', function () {
                    openTaskSheetPopup(
                        button.getAttribute('data-task-sheet-popup-open'),
                        button.getAttribute('data-task-sheet-popup-title')
                    );
                });
            });

            document.getElementById('large_confirmation_alert')?.addEventListener('hidden.bs.modal', function () {
                this.classList.remove('hdv2-task-modal');
                document.getElementById('large_alert_body')?.classList.remove('hdv2-task-common-popup');
            });
        });
    </script>
@endsection
