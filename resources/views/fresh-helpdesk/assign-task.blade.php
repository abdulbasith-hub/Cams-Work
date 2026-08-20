@extends('index2')

@section('title', 'Assign Helpdesk Task')

@section('content')
<style>
    .fh-page {
        margin: 0 auto;
        max-width: 1440px;
    }

    .fh-card {
        background: #fff;
        border: 1px solid #dbe5f2;
        border-radius: 8px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .fh-card-header {
        background: #5b7df0;
        color: #fff;
        padding: 12px 16px;
    }

    .fh-title-row {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: space-between;
    }

    .fh-title-row h4 {
        color: #fff;
        font-size: 18px;
        font-weight: 850;
        margin: 0;
    }

    .fh-title-row p {
        color: rgba(255, 255, 255, .84);
        font-size: 12px;
        margin: 2px 0 0;
    }

    .fh-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .fh-header-actions .btn {
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .48);
        color: #fff;
        font-weight: 750;
    }

    .fh-form {
        background: linear-gradient(180deg, #f8fbff 0, #fff 130px), #fff;
        padding: 18px;
    }

    .fh-form .form-label {
        color: #06122f;
        font-size: 13px;
        font-weight: 800;
    }

    .fh-form .required::after {
        color: #dc2626;
        content: " *";
    }

    .fh-form .form-control,
    .fh-form .form-select {
        border: 1px solid #d6e1ef;
        border-radius: 7px;
        font-size: 13px;
        min-height: 42px;
    }

	    .fh-actions {
	        border-top: 1px solid #edf2f7;
	        margin-top: 4px;
	        padding-top: 14px;
	    }

	    .fh-form [hidden] {
	        display: none !important;
	    }

    @media (max-width: 767.98px) {
        .fh-title-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .fh-header-actions,
        .fh-header-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="fh-page">
    <div class="fh-card">
        <div class="fh-card-header">
            <div class="fh-title-row">
                <div>
                    <h4>Assign Task</h4>
                    <p>{{ $roleLabel }} task management</p>
                </div>
                <div class="fh-header-actions">
                    <a href="{{ route('fresh-helpdesk.task-details') }}" class="btn btn-sm">
                        <i class="ti ti-list-details me-1"></i> Task Details
                    </a>
                    <a href="{{ route('fresh-helpdesk.dashboard', ['pane' => 'tasks']) }}" class="btn btn-sm">
                        <i class="ti ti-layout-dashboard me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('fresh-helpdesk.tasks.store') }}" class="fh-form">
            @csrf

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label required">Developer</label>
                    <select name="developer_userid" class="form-select" required>
                        <option value="">Select developer</option>
                        @foreach ($developers as $person)
                            <option value="{{ $person->devuserid }}" @selected(old('developer_userid') == $person->devuserid)>
                                {{ $person->devename }}{{ $person->email ? ' - '.$person->email : '' }}
                            </option>
                        @endforeach
                    </select>
                    {{-- <div class="form-text">Any Senior Developer can review the task once it's marked Resolved.</div> --}}
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label required">Task Type</label>
                    <select name="task_type" class="form-select" required>
                        <option value="new" @selected(old('task_type', 'new') === 'new')>New</option>
                        <option value="existing" @selected(old('task_type') === 'existing')>Existing</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label required">Assigned On</label>
                    <div class="input-group" data-dt-picker="assigned_on">
                        <input type="date" class="form-control" data-dt-date>
                        <select class="form-select" data-dt-hour style="max-width: 110px;">
                            @foreach (range(0, 23) as $hour)
                                <option value="{{ $hour }}">{{ \Carbon\Carbon::createFromTime($hour, 0)->format('h A') }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-dt-set>Set</button>
                    </div>
                    <div class="form-text" data-dt-display>&nbsp;</div>
                    <input type="hidden" name="assigned_on" data-dt-hidden value="{{ old('assigned_on') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label required">Expected On</label>
                    <div class="input-group" data-dt-picker="expected_date_to_complete">
                        <input type="date" class="form-control" data-dt-date>
                        <select class="form-select" data-dt-hour style="max-width: 110px;">
                            @foreach (range(0, 23) as $hour)
                                <option value="{{ $hour }}">{{ \Carbon\Carbon::createFromTime($hour, 0)->format('h A') }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-dt-set>Set</button>
                    </div>
                    <div class="form-text" data-dt-display>&nbsp;</div>
                    <input type="hidden" name="expected_date_to_complete" data-dt-hidden value="{{ old('expected_date_to_complete') }}">
                </div>
                 <div class="col-6" data-task-existing-field hidden>
                    <label class="form-label required">Module</label>
                    <select name="module_category_id" class="form-select">
                        <option value="">Select module</option>
                        @foreach ($moduleCategories as $category)
                            <option value="{{ $category->categoryid }}" @selected((string) old('module_category_id') === (string) $category->categoryid)>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label required">Description</label>
                    <textarea name="new_task_description" rows="5" class="form-control" maxlength="2000" required>{{ old('new_task_description') }}</textarea>
                </div>

            </div>

            <div class="fh-actions d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('fresh-helpdesk.task-details') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-send me-1"></i> Forward Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var taskType = document.querySelector('select[name="task_type"]');
        var existingField = document.querySelector('[data-task-existing-field]');
        var moduleSelect = document.querySelector('select[name="module_category_id"]');

        function toggleTaskFields() {
            var isExisting = taskType && taskType.value === 'existing';

            if (existingField) {
                existingField.hidden = !isExisting;
            }
            if (moduleSelect) {
                moduleSelect.required = isExisting;
                moduleSelect.disabled = !isExisting;
            }
        }

        if (taskType) {
            taskType.addEventListener('change', toggleTaskFields);
            toggleTaskFields();
        }

        var MIN_GAP_MS = 2 * 60 * 60 * 1000;

        function pad(value) {
            return String(value).padStart(2, '0');
        }

        function toTimestamp(value) {
            return new Date(value.replace(' ', 'T')).getTime();
        }

        function formatDisplay(dateValue, hour) {
            var parts = dateValue.split('-');
            var d = new Date(parts[0], parts[1] - 1, parts[2], hour);
            var hour12 = hour % 12 === 0 ? 12 : hour % 12;
            var ampm = hour < 12 ? 'AM' : 'PM';

            return 'Set to ' + pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(hour12) + ':00 ' + ampm;
        }

        function initPicker(wrapper) {
            var field = wrapper.getAttribute('data-dt-picker');
            var dateInput = wrapper.querySelector('[data-dt-date]');
            var hourSelect = wrapper.querySelector('[data-dt-hour]');
            var setBtn = wrapper.querySelector('[data-dt-set]');
            var hidden = document.querySelector('input[type="hidden"][name="' + field + '"]');
            var display = wrapper.parentElement.querySelector('[data-dt-display]');

            function applyFromHidden() {
                if (!hidden.value) {
                    return false;
                }

                var m = hidden.value.match(/^(\d{4}-\d{2}-\d{2})[ T](\d{2})/);
                if (!m) {
                    return false;
                }

                dateInput.value = m[1];
                hourSelect.value = String(parseInt(m[2], 10));
                display.textContent = formatDisplay(m[1], parseInt(m[2], 10));

                return true;
            }

            function setValue() {
                if (!dateInput.value) {
                    dateInput.focus();
                    return;
                }

                var hour = parseInt(hourSelect.value, 10);
                var candidate = dateInput.value + ' ' + pad(hour) + ':00:00';

                if (field === 'expected_date_to_complete') {
                    var assignedHidden = document.querySelector('input[type="hidden"][name="assigned_on"]');
                    if (assignedHidden && assignedHidden.value && toTimestamp(candidate) - toTimestamp(assignedHidden.value) < MIN_GAP_MS) {
                        alert('Expected On must be at least 2 hours after Assigned On (same date is fine, just not the same time).');
                        return;
                    }
                }

                hidden.value = candidate;
                display.textContent = formatDisplay(dateInput.value, hour);
                wrapper.dispatchEvent(new CustomEvent('dtset', { bubbles: true, detail: { field: field, value: hidden.value, date: dateInput.value, hour: hour } }));
            }

            setBtn.addEventListener('click', setValue);

            if (!applyFromHidden()) {
                var now = new Date();
                dateInput.value = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
                hourSelect.value = String(now.getHours());

                if (field === 'assigned_on') {
                    setValue();
                }
            }

            return { field: field, dateInput: dateInput, hidden: hidden, display: display };
        }

        var pickers = Array.prototype.map.call(document.querySelectorAll('[data-dt-picker]'), initPicker);
        var assignedPicker = pickers.find(function (p) { return p.field === 'assigned_on'; });
        var expectedPicker = pickers.find(function (p) { return p.field === 'expected_date_to_complete'; });

        if (assignedPicker && expectedPicker) {
            document.addEventListener('dtset', function (event) {
                if (event.detail.field === 'assigned_on') {
                    expectedPicker.dateInput.min = event.detail.date;

                    if (expectedPicker.hidden.value && toTimestamp(expectedPicker.hidden.value) - toTimestamp(event.detail.value) < MIN_GAP_MS) {
                        expectedPicker.hidden.value = '';
                        expectedPicker.display.textContent = 'Please re-set — must be at least 2 hours after Assigned On.';
                    }
                }
            });
            if (assignedPicker.dateInput.value) {
                expectedPicker.dateInput.min = assignedPicker.dateInput.value;
            }
        }

        document.querySelector('.fh-form').addEventListener('submit', function (event) {
            var missing = Array.prototype.filter.call(document.querySelectorAll('[data-dt-hidden]'), function (input) {
                return !input.value;
            });

            if (missing.length) {
                event.preventDefault();
                alert('Please pick a date and hour, then click Set for both Assigned On and Expected On.');
            }
        });
    })();
</script>
@endsection
