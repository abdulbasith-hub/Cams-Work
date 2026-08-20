@extends('index2')

@section('title', 'Create Helpdesk V2 Ticket')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2 hdv2-create-page">
        @include('helpdesk-v2.partials.flashes', ['compactErrors' => true])
        <div class="hdv2-create-header">
            <h1>Create New Ticket</h1>
            {{-- <div class="hdv2-actions">
                <a href="{{ route('helpdesk-v2.dashboard.default') }}" class="btn btn-light">Dashboard</a>
                <a href="{{ route('helpdesk-v2.tickets.index') }}" class="btn btn-light">Tickets</a>
            </div> --}}
        </div>

        <div class="hdv2-ticket-layout">
            <form method="POST" action="{{ route('helpdesk-v2.tickets.store') }}" enctype="multipart/form-data" class="hdv2-panel hdv2-form hdv2-create-card hdv2-create-form" id="hdv2TicketCreateForm">
                @csrf
                <input type="hidden" name="submission_token" value="{{ old('submission_token', $submissionToken) }}">

                <div class="hdv2-panel-head"><h2>Ticket Details</h2></div>

                <div class="hdv2-form-grid">
                    @if ($showDepartmentSelect)
                        <label class="hdv2-span-2">Department <span class="hdv2-required">*</span>
                            <select name="deptcode" id="deptcode" @class(['is-invalid' => $errors->has('deptcode')]) required>
                                <option value="" selected disabled>Select department</option>
                                @foreach (($departmentOptions ?? collect()) as $department)
                                    <option value="{{ $department->deptcode }}" @selected(old('deptcode', $selectedDeptCode ?? '') == $department->deptcode)>{{ $department->deptesname }}</option>
                                @endforeach
                            </select>
                            @error('deptcode')
                                <span class="hdv2-field-error">{{ $message }}</span>
                            @enderror
                        </label>
                    @endif

                    <label>Financial Year <span class="hdv2-required">*</span>
                        <select name="financial_year" id="financial_year" data-selected-value="{{ old('financial_year') }}" @class(['is-invalid' => $errors->has('financial_year')]) required>
                            <option value="" selected disabled>Select financial year</option>
                            @foreach (($financialYears ?? collect()) as $financialYear)
                                <option value="{{ $financialYear['value'] }}" @selected(old('financial_year') == $financialYear['value'])>{{ $financialYear['label'] }}</option>
                            @endforeach
                        </select>
                        @error('financial_year')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Audit Quarter <span class="hdv2-required">*</span>
                        <select name="audit_quarter" id="audit_quarter" data-selected-value="{{ old('audit_quarter') }}" @class(['is-invalid' => $errors->has('audit_quarter')]) required>
                            <option value="" selected disabled>Select audit quarter</option>
                            @foreach (($auditQuarters ?? collect()) as $auditQuarter)
                                <option value="{{ $auditQuarter['value'] }}"
                                        data-quartercode="{{ $auditQuarter['quartercode'] }}"
                                        data-financialyear="{{ $auditQuarter['financialyearcode'] }}"
                                        @selected(old('audit_quarter') == $auditQuarter['value'])>{{ $auditQuarter['label'] }}</option>
                            @endforeach
                        </select>
                        @error('audit_quarter')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Type <span class="hdv2-required">*</span>
                        <select name="request_type" @class(['is-invalid' => $errors->has('request_type')]) required>
                            <option value="" selected disabled>Select type</option>
                            @foreach ($requestTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('request_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('request_type')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Priority <span class="hdv2-required">*</span>
                        <select name="priority" @class(['is-invalid' => $errors->has('priority')]) required>
                            @foreach ($priorities as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Category <span class="hdv2-required">*</span>
                        <select name="category" @class(['is-invalid' => $errors->has('category')]) required>
                            <option value="" selected disabled>Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>Institution <small>eg: District - Institution</small>
                        <input type="text" name="institution" value="{{ old('institution') }}" maxlength="200" @class(['is-invalid' => $errors->has('institution')])>
                        @error('institution')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="hdv2-span-2">Subject <span class="hdv2-required">*</span>
                        <input type="text" name="subject" value="{{ old('subject') }}" maxlength="200" @class(['is-invalid' => $errors->has('subject')]) required>
                        <small>Maximum 200 characters.</small>
                        @error('subject')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="hdv2-span-2">Description <span class="hdv2-required">*</span>
                        <textarea name="description" rows="6" maxlength="750" @class(['is-invalid' => $errors->has('description')]) required>{{ old('description') }}</textarea>
                        <small>Maximum 750 characters. Special characters are allowed here for values like email IDs and passwords.</small>
                        @error('description')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="hdv2-span-2">Attachments
                        <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png" @class(['is-invalid' => $errors->has('attachments') || $errors->has('attachments.*')])>
                        <small>Supported: JPEG, PNG, PDF. Maximum 500KB per file.</small>
                        @error('attachments')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                        @error('attachments.*')
                            <span class="hdv2-field-error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="hdv2-form-actions">
                    <a href="{{ route('helpdesk-v2.dashboard.default') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary" data-hdv2-submit-once>Create Ticket</button>
                </div>
            </form>

            <div class="hdv2-panel hdv2-tips-panel hdv2-create-card">
                <div class="hdv2-panel-head"><h2><i class="ti ti-info-circle"></i> Tips</h2></div>
                <ul class="hdv2-tips-list">
                    <li><i class="ti ti-circle-check"></i><span><strong>Be specific:</strong> Provide clear details about your issue</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>{{ $showDepartmentSelect ? 'Select department:' : 'No department needed:' }}</strong> {{ $showDepartmentSelect ? 'Choose the department first to load financial years and audit quarters' : 'An admin will assign your ticket to the right department' }}</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>Set appropriate priority:</strong> Use urgent only for critical issues</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>Attach files:</strong> Include screenshots or documents that help explain the issue</span></li>
                    <li><i class="ti ti-circle-check"></i><span><strong>Include steps to reproduce:</strong> If applicable, describe how the issue occurs</span></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const departmentSelect = document.getElementById('deptcode');
            const financialYearSelect = document.getElementById('financial_year');
            const auditQuarterSelect = document.getElementById('audit_quarter');
            const planDetailsUrl = @json(route('helpdesk-v2.tickets.plan-details'));

            if (!financialYearSelect || !auditQuarterSelect) {
                return;
            }

            let financialYearOptions = Array.from(financialYearSelect.querySelectorAll('option')).map((option) => ({
                value: option.value,
                text: option.textContent,
            }));
            let quarterOptions = Array.from(auditQuarterSelect.querySelectorAll('option')).map((option) => ({
                value: option.value,
                text: option.textContent,
                quartercode: option.getAttribute('data-quartercode') || '',
                financialyear: option.getAttribute('data-financialyear') || '',
            }));

            const addPlaceholder = (select, text) => {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = text;
                option.disabled = true;
                option.selected = true;
                select.appendChild(option);
            };

            const renderFinancialYearOptions = () => {
                const selectedFinancialYear = financialYearSelect.dataset.selectedValue || financialYearSelect.value;

                financialYearSelect.innerHTML = '';
                addPlaceholder(financialYearSelect, 'Select financial year');

                financialYearOptions.forEach((optionData) => {
                    if (!optionData.value) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    option.selected = selectedFinancialYear === optionData.value;
                    financialYearSelect.appendChild(option);
                });

                if (!Array.from(financialYearSelect.options).some((option) => option.selected && option.value)) {
                    financialYearSelect.selectedIndex = 0;
                }
            };

            const renderQuarterOptions = () => {
                const selectedFinancialYear = financialYearSelect.value;
                const selectedQuarter = auditQuarterSelect.dataset.selectedValue || auditQuarterSelect.value;

                auditQuarterSelect.innerHTML = '';
                addPlaceholder(auditQuarterSelect, 'Select audit quarter');

                quarterOptions.forEach((optionData) => {
                    if (!optionData.value || !selectedFinancialYear || optionData.financialyear !== selectedFinancialYear) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    option.selected = selectedQuarter === optionData.value;

                    if (optionData.quartercode) {
                        option.setAttribute('data-quartercode', optionData.quartercode);
                    }

                    if (optionData.financialyear) {
                        option.setAttribute('data-financialyear', optionData.financialyear);
                    }

                    auditQuarterSelect.appendChild(option);
                });

                if (!Array.from(auditQuarterSelect.options).some((option) => option.selected && option.value)) {
                    auditQuarterSelect.selectedIndex = 0;
                }
            };

            const resetPlanSelection = () => {
                financialYearOptions = [];
                quarterOptions = [];
                financialYearSelect.dataset.selectedValue = '';
                auditQuarterSelect.dataset.selectedValue = '';
                renderFinancialYearOptions();
                renderQuarterOptions();
            };

            const loadPlanDetails = async (deptCode) => {
                resetPlanSelection();

                if (!deptCode) {
                    return;
                }

                financialYearSelect.disabled = true;
                auditQuarterSelect.disabled = true;

                try {
                    const url = new URL(planDetailsUrl, window.location.origin);
                    url.searchParams.set('deptcode', deptCode);

                    const response = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) {
                        throw new Error('Unable to load plan details');
                    }

                    const data = await response.json();
                    financialYearOptions = (data.financialYears || []).map((item) => ({
                        value: String(item.value || ''),
                        text: item.label || '',
                    }));
                    quarterOptions = (data.auditQuarters || []).map((item) => ({
                        value: String(item.value || ''),
                        text: item.label || '',
                        quartercode: item.quartercode || '',
                        financialyear: String(item.financialyearcode || ''),
                    }));
                } catch (error) {
                    resetPlanSelection();
                } finally {
                    financialYearSelect.disabled = false;
                    auditQuarterSelect.disabled = false;
                    renderFinancialYearOptions();
                    renderQuarterOptions();
                }
            };

            financialYearSelect.addEventListener('change', () => {
                auditQuarterSelect.dataset.selectedValue = '';
                renderQuarterOptions();
            });

            if (departmentSelect) {
                departmentSelect.addEventListener('change', () => loadPlanDetails(departmentSelect.value));
            }

            renderFinancialYearOptions();
            renderQuarterOptions();
        });
    </script>
@endsection
