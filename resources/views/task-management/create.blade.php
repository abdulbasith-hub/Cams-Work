@extends('index2')

@section('title', 'Create Task')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2 hdv2-create-page">
        @include('helpdesk-v2.partials.flashes', ['compactErrors' => true])

        <div class="hdv2-create-header">
            <div>
                <div class="hdv2-eyebrow">Task Management</div>
                <h1>Create Task</h1>
            </div>
            {{-- <div class="hdv2-actions">
                <a href="{{ route('helpdesk-v2.dashboard', ['role' => \App\Services\HelpdeskV2Session::role(), 'pane' => 'tasks']) }}" class="btn btn-light">Task List</a>
            </div> --}}
        </div>

        <div class="hdv2-ticket-layout">
            <form method="POST" action="{{ route('task-management.store') }}" class="hdv2-panel hdv2-form hdv2-create-card hdv2-create-form">
                @csrf

                <div class="hdv2-panel-head"><h2>Task Details</h2></div>

                <div class="hdv2-form-grid">
                    <label>Assign To <span class="hdv2-required">*</span>
                        <select name="developer_userid" @class(['is-invalid' => $errors->has('developer_userid')]) required>
                            <option value="" selected disabled>Select developer</option>
                            @foreach ($developers as $developer)
                                <option value="{{ $developer->devuserid }}" @selected(old('developer_userid') == $developer->devuserid)>
                                    {{ $developer->devename }}{{ $developer->email ? ' - '.$developer->email : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('developer_userid')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Task Type <span class="hdv2-required">*</span>
                        <select name="task_type" id="task_type" @class(['is-invalid' => $errors->has('task_type')]) required>
                            <option value="existing" @selected(old('task_type', 'existing') === 'existing')>Existing</option>
                            <option value="new" @selected(old('task_type') === 'new')>New</option>
                        </select>
                        @error('task_type')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label id="existingModuleWrap" class="hdv2-span-2">Existing Module <span class="hdv2-required">*</span>
                        <select name="existing_module" id="existing_module" @class(['is-invalid' => $errors->has('existing_module')])>
                            <option value="" selected disabled>Select module</option>
                            @foreach ($moduleOptions as $module)
                                <option value="{{ $module }}" @selected(old('existing_module') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                        @error('existing_module')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label id="newModuleWrap" class="hdv2-span-2">New Module / Task <span class="hdv2-required">*</span>
                        <input type="text" name="new_module" id="new_module" value="{{ old('new_module') }}" maxlength="1000" @class(['is-invalid' => $errors->has('new_module')])>
                        @error('new_module')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Assigned On <span class="hdv2-required">*</span>
                        <input type="datetime-local" name="assigned_on" value="{{ old('assigned_on', $minimumTaskDateTime) }}" @class(['is-invalid' => $errors->has('assigned_on')]) required>
                        @error('assigned_on')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Expected On
                        <input type="datetime-local" name="expected_date_to_complete" value="{{ old('expected_date_to_complete') }}" @class(['is-invalid' => $errors->has('expected_date_to_complete')])>
                        @error('expected_date_to_complete')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="hdv2-span-2">Description
                        <textarea name="description" rows="5" maxlength="2000" @class(['is-invalid' => $errors->has('description')])>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="hdv2-form-actions">
                    <a href="{{ route('helpdesk-v2.dashboard', ['role' => \App\Services\HelpdeskV2Session::role(), 'pane' => 'tasks']) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Assign Task</button>
                </div>
            </form>

            <div class="hdv2-panel hdv2-tips-panel hdv2-create-card">
                <div class="hdv2-panel-head"><h2><i class="ti ti-info-circle"></i> Task Notes</h2></div>
                <ul class="hdv2-tips-list">
                    <li><i class="ti ti-circle-check"></i><span><strong>Assign developer:</strong> Select the concerned developer for this task.</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>Existing/New:</strong> Pick an existing module or type a new task/module name.</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>Expected date:</strong> Use this to track overdue tasks in the dashboard.</span></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var taskType = document.getElementById('task_type');
            var existingWrap = document.getElementById('existingModuleWrap');
            var newWrap = document.getElementById('newModuleWrap');
            var existingModule = document.getElementById('existing_module');
            var newModule = document.getElementById('new_module');

            function syncTaskType() {
                if (!taskType || !existingWrap || !newWrap || !existingModule || !newModule) {
                    return;
                }

                var isNew = taskType.value === 'new';
                existingWrap.hidden = isNew;
                newWrap.hidden = !isNew;
                existingModule.required = !isNew;
                newModule.required = isNew;
            }

            if (taskType) {
                taskType.addEventListener('change', syncTaskType);
                syncTaskType();
            }
        });
    </script>
@endsection
