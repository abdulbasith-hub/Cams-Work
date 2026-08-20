@extends('index2')

@section('title', 'Task Details')

@section('content')
@php
    $minimumTaskDateTime = now('Asia/Kolkata')->format('Y-m-d H:i');
@endphp
@include('tickets.partials.app-theme')
<link rel="stylesheet" href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}">
<script src="../assets/js/jquery.js"></script>
<script src="{{ asset('assets/js/extra-libs/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/libs/daterangepicker/daterangepicker.js') }}"></script>
<div class="helpdesk-app-theme">
    <div class="helpdesk-main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title"><i class="ti ti-list-check text-primary"></i> Task Details</h1>
            <a href="{{ route('helpdesk.tasks.list') }}" class="btn btn-light">Back to Task List</a>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0 text-light">Task Update Form</h5></div>
            <div class="card-body">
                <form action="{{ route('helpdesk.tasks.update', $task) }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_testing_task" value="{{ $task->is_testing_task ? 1 : 0 }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Process Assigned</label>
                            <textarea name="process_assigned" rows="3" class="form-control" {{ $isDeveloper ? 'readonly' : '' }} required>{{ old('process_assigned', $task->process_assigned) }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">New/Existing</label>
                            <select name="task_type" class="form-select" {{ $isDeveloper ? 'disabled' : '' }} required>
                                <option value="new" {{ old('task_type', $task->task_type) === 'new' ? 'selected' : '' }}>New</option>
                                <option value="existing" {{ old('task_type', $task->task_type) === 'existing' ? 'selected' : '' }}>Existing</option>
                            </select>
                            @if ($isDeveloper)
                                <input type="hidden" name="task_type" value="{{ old('task_type', $task->task_type) }}">
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Task Category</label>
                            <input type="text" class="form-control" value="{{ $task->is_testing_task ? 'Testing Task' : 'Normal Task' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expected Date to Complete</label>
                            <input type="text" name="expected_date_to_complete" class="form-control js-datetime-display js-task-datetime-picker" data-min-datetime="{{ $minimumTaskDateTime }}" value="{{ old('expected_date_to_complete', optional($task->expected_date_to_complete)->format('d/m/Y H:i')) }}" {{ $isDeveloper ? 'readonly' : '' }} autocomplete="off" placeholder="DD/MM/YYYY HH:MM">
                            <div class="small text-muted mt-1 js-datetime-display-text">{{ old('expected_date_to_complete', optional($task->expected_date_to_complete)->format('d/m/Y H:i')) }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Assigned On</label>
                            <input type="text" class="form-control" value="{{ $task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-' }}" readonly>
                        </div>
	                        <div class="col-md-3">
	                            <label class="form-label">Started On</label>
	                            <input type="text" name="started_on" class="form-control js-datetime-display js-task-datetime-picker" value="{{ old('started_on', optional($task->started_on)->format('d/m/Y H:i')) }}" {{ $isNicAdmin ? 'readonly' : '' }} autocomplete="off" placeholder="DD/MM/YYYY HH:MM">
	                            <div class="small text-muted mt-1 js-datetime-display-text">{{ old('started_on', optional($task->started_on)->format('d/m/Y H:i')) }}</div>
	                        </div>
	                        <div class="col-md-3">
	                            <label class="form-label">Completed On</label>
	                            <input type="text" name="completed_on" class="form-control js-datetime-display js-task-datetime-picker" value="{{ old('completed_on', optional($task->completed_on)->format('d/m/Y H:i')) }}" {{ $isNicAdmin ? 'readonly' : '' }} autocomplete="off" placeholder="DD/MM/YYYY HH:MM">
	                            <div class="small text-muted mt-1 js-datetime-display-text">{{ old('completed_on', optional($task->completed_on)->format('d/m/Y H:i')) }}</div>
	                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Assigned To</label>
                            <input type="text" class="form-control" value="{{ $task->developer_name }}" readonly>
                        </div>

	                        <div class="col-md-6">
	                            <label class="form-label">Remarks by Developer</label>
	                            <textarea name="remarks_by_developer" rows="3" class="form-control" {{ $isNicAdmin ? 'readonly' : '' }}>{{ old('remarks_by_developer', $task->remarks_by_developer) }}</textarea>
	                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status of Task by the Tester</label>
                            <input type="text" name="task_status_by_tester" class="form-control" value="{{ old('task_status_by_tester', $task->task_status_by_tester) }}" {{ $isDeveloper ? 'readonly' : '' }}>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Remarks by the Project-Head</label>
                            <textarea name="remarks_by_project_head" rows="3" class="form-control" {{ $isDeveloper ? 'readonly' : '' }}>{{ old('remarks_by_project_head', $task->remarks_by_project_head) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Verifier Feedback</label>
                            <textarea name="verifier_feedback" rows="3" class="form-control" {{ $isDeveloper ? 'readonly' : '' }}>{{ old('verifier_feedback', $task->verifier_feedback) }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Verified By</label>
                            <input type="text" name="verified_by" class="form-control" value="{{ old('verified_by', $task->verified_by) }}" {{ $isDeveloper ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Verified On</label>
                            <input type="text" name="verified_on" class="form-control js-datetime-display js-task-datetime-picker" value="{{ old('verified_on', optional($task->verified_on)->format('d/m/Y H:i')) }}" {{ $isDeveloper ? 'readonly' : '' }} autocomplete="off" placeholder="DD/MM/YYYY HH:MM">
                            <div class="small text-muted mt-1 js-datetime-display-text">{{ old('verified_on', optional($task->verified_on)->format('d/m/Y H:i')) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks by Verifier</label>
                            <textarea name="remarks_by_verifier" rows="3" class="form-control" {{ $isDeveloper ? 'readonly' : '' }}>{{ old('remarks_by_verifier', $task->remarks_by_verifier) }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Approved By</label>
                            <input type="text" name="approved_by" class="form-control" value="{{ old('approved_by', $task->approved_by) }}" {{ $isDeveloper ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Approved On</label>
                            <input type="text" name="approved_on" class="form-control js-datetime-display js-task-datetime-picker" value="{{ old('approved_on', optional($task->approved_on)->format('d/m/Y H:i')) }}" {{ $isDeveloper ? 'readonly' : '' }} autocomplete="off" placeholder="DD/MM/YYYY HH:MM">
                            <div class="small text-muted mt-1 js-datetime-display-text">{{ old('approved_on', optional($task->approved_on)->format('d/m/Y H:i')) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">Hosted in Staging</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="hosted_in_staging" value="1" {{ old('hosted_in_staging', $task->hosted_in_staging) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">Deployed in Live Server</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="deployed_in_live_server" value="1" {{ old('deployed_in_live_server', $task->deployed_in_live_server) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary text-light">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && typeof $.fn.daterangepicker === 'function' && typeof moment !== 'undefined') {
            $('.js-task-datetime-picker').each(function () {
                const $input = $(this);
                if ($input.is('[readonly]') || $input.data('daterangepicker')) {
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
	                        format: 'DD/MM/YYYY HH:mm',
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
                const parsedExistingValue = existingValue
	                    ? moment(existingValue, ['DD/MM/YYYY HH:mm', 'DD/MM/YYYY HH:mm:A', 'DD/MM/YYYY HH:mm A', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'], true)
                    : null;

                if (parsedExistingValue && parsedExistingValue.isValid()) {
                    options.startDate = parsedExistingValue;
                }

                $input.daterangepicker(options);

                $input.on('apply.daterangepicker', function (event, picker) {
	                    $input.val(picker.startDate.format('DD/MM/YYYY HH:mm')).trigger('input');
                });

                $input.on('cancel.daterangepicker', function () {
                    $input.val('').trigger('input');
                });

                if (parsedExistingValue && parsedExistingValue.isValid()) {
	                    $input.val(parsedExistingValue.format('DD/MM/YYYY HH:mm'));
                }
            });
        }

        function formatDateTimeDisplay(value) {
            if (!value) {
                return '';
            }

            if (typeof moment !== 'undefined') {
	                const parsedValue = moment(value, ['DD/MM/YYYY HH:mm', 'DD/MM/YYYY HH:mm:A', 'DD/MM/YYYY HH:mm A', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'], true);
	                if (parsedValue.isValid()) {
	                    return parsedValue.format('DD/MM/YYYY HH:mm');
	                }
            }

            return String(value).replace('T', ' ');
        }

        document.querySelectorAll('.js-datetime-display').forEach(function (input) {
            const displayNode = input.parentElement.querySelector('.js-datetime-display-text');
            if (!displayNode) {
                return;
            }

            const syncDisplay = function () {
                displayNode.textContent = formatDateTimeDisplay(input.value);
            };

            input.addEventListener('input', syncDisplay);
            syncDisplay();
        });
    });
</script>
@endsection
