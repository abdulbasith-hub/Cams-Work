@extends('index2')

@section('title', 'Create Helpdesk Ticket')

@section('content')
@include('tickets.partials.app-theme')
<style>
    .helpdesk-app-theme {
        --primary-color: #5d87ff;
        --secondary-color: #7c3aed;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --dark-color: #1f2937;
        --light-color: #f3f4f6;
        color: var(--dark-color);
        font-family: 'Nunito', sans-serif;
    }

    .helpdesk-app-theme .helpdesk-main-content {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: var(--dark-color);
    }

    .helpdesk-app-theme .stat-card,
    .helpdesk-app-theme .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .stat-card {
        color: #fff;
        padding: 25px;
        margin-bottom: 20px;
    }

    .helpdesk-app-theme .stat-card h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .helpdesk-app-theme .stat-card p {
        margin: 0;
        opacity: 0.92;
    }

    .helpdesk-app-theme .stat-card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .helpdesk-app-theme .stat-card-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .helpdesk-app-theme .stat-card-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .helpdesk-app-theme .stat-card-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .helpdesk-app-theme .stat-card-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .helpdesk-app-theme .stat-card-dark {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .helpdesk-app-theme .card-header {
        /* background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); */
         background: var(--primary-color);
        color: #fff;
        border-radius: 15px 15px 0 0 !important;
        padding: 15px 20px;
    }

    .helpdesk-app-theme .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .helpdesk-app-theme .table thead {
        /* background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); */
         background: var(--primary-color);
        color: #fff;
    }

    .helpdesk-app-theme .table tbody tr:hover {
        background: var(--light-color);
    }

    .helpdesk-app-theme .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .helpdesk-app-theme .badge-priority-low {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-priority-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-priority-high {
        background: #fed7aa;
        color: #9a3412;
    }

    .helpdesk-app-theme .badge-priority-urgent {
        background: #fecaca;
        color: #991b1b;
    }

    .helpdesk-app-theme .badge-status-open {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-status-in_progress {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-status-resolved {
        background: #d1fae5;
        color: #065f46;
    }

    .helpdesk-app-theme .badge-status-closed {
        background: #e5e7eb;
        color: #374151;
    }
    .helpdesk-app-theme .table tbody tr {
        transition: background 0.3s ease;
    }

    .helpdesk-app-theme .form-control,
    .helpdesk-app-theme .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .helpdesk-app-theme .form-control:focus,
    .helpdesk-app-theme .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .helpdesk-app-theme .alert {
        border-radius: 10px;
        border: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<div class="helpdesk-app-theme">
    <div class="helpdesk-main-content">
        <div class="container-fluid px-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="page-title">
                    {{-- <i class="bi bi-plus-circle text-primary"></i> --}}
                     Create New Ticket
                </h3>
                {{-- <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-light">Back to Tickets</a> --}}
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-light">Ticket Details</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('helpdesk.tickets.store') }}" method="POST" enctype="multipart/form-data" id="ticket-create-form" data-plan-details-url="{{ route('helpdesk.tickets.plan-details') }}">
                                @csrf

                                @if ($canSelectDepartment ?? false)
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="deptcode" class="form-label">Department <span class="text-danger">*</span></label>
                                            <select class="form-select @error('deptcode') is-invalid @enderror" id="deptcode" name="deptcode" required>
                                                <option value="" selected disabled>Select department</option>
                                                @foreach (($departments ?? collect()) as $department)
                                                    <option value="{{ $department->deptcode }}" {{ (string) old('deptcode', $selectedDeptCode ?? '') === (string) $department->deptcode ? 'selected' : '' }}>
                                                        {{ $department->deptelname }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('deptcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="financial_year" class="form-label">Financial Year <span class="text-danger">*</span></label>
                                        <select class="form-select @error('financial_year') is-invalid @enderror" id="financial_year" name="financial_year" data-selected-value="{{ old('financial_year') }}" required>
                                            <option value="" selected disabled>Select financial year</option>
                                            @foreach (($financialYears ?? collect()) as $financialYear)
                                                <option value="{{ $financialYear['value'] }}" {{ old('financial_year') == $financialYear['value'] ? 'selected' : '' }}>{{ $financialYear['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('financial_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="audit_quarter" class="form-label">Audit Quarter <span class="text-danger">*</span></label>
                                        <select class="form-select @error('audit_quarter') is-invalid @enderror" id="audit_quarter" name="audit_quarter" data-selected-value="{{ old('audit_quarter') }}" required>
                                            <option value="" selected disabled>Select audit quarter</option>
                                            @foreach (($auditQuarters ?? collect()) as $auditQuarter)
                                                <option value="{{ $auditQuarter['value'] }}" data-quartercode="{{ $auditQuarter['quartercode'] }}" data-financialyear="{{ $auditQuarter['financialyearcode'] }}" {{ old('audit_quarter') == $auditQuarter['value'] ? 'selected' : '' }}>{{ $auditQuarter['label'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('audit_quarter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="request_type" class="form-label">Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('request_type') is-invalid @enderror"
                                            id="request_type" name="request_type" required>
                                            <option value="" selected disabled>Select type</option>
                                            @foreach ($requestTypes ?? \App\Models\HelpdeskTicket::REQUEST_TYPE_OPTIONS as $value => $label)
                                                <option value="{{ $value }}" {{ old('request_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('request_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority" name="priority" required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror"
                                            id="category" name="category" required>
                                            <option value="" selected disabled>Select a category</option>
                                            @foreach ($categories ?? \App\Models\HelpdeskTicket::CATEGORY_OPTIONS as $category)
                                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="institution" class="form-label">Institution</label><small class="text-muted"> eg:(District - Institution)</small>
                                        <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}" maxlength="200" pattern="^(?!.*-.*-)[A-Za-z0-9\s.,()\/&:-]*$" title="Institution can be up to 200 characters and contain only one hyphen.">
                                        @error('institution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                        id="subject" name="subject" value="{{ old('subject') }}" maxlength="200" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum 200 characters.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="6" maxlength="750" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum 750 characters. Special characters are allowed here for values like email IDs and passwords.</small>
                                </div>

                                @if ($isStateAdmin ?? false)
                                    {{-- <div class="alert alert-info">
                                        This ticket will be forwarded automatically to NIC Admin after creation.
                                    </div> --}}
                                @endif

                               <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments</label>
                                    <input type="file" class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                                        id="attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                    @error('attachments')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('attachments.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Supported: JPEG, PNG, PDF. Maximum 500KB per file.</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary text-dark">
                                        <i class="bi bi-check-circle"></i> Create Ticket
                                    </button>
                                    <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>Be specific:</strong> Provide clear details about your issue
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>No department needed:</strong> An admin will assign your ticket to the right department
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>Set appropriate priority:</strong> Use urgent only for critical issues
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>Attach files:</strong> Include screenshots or documents that help explain the issue
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>Include steps to reproduce:</strong> If applicable, describe how the issue occurs
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('ticket-create-form');
        const departmentSelect = document.getElementById('deptcode');
        const financialYearSelect = document.getElementById('financial_year');
        const auditQuarterSelect = document.getElementById('audit_quarter');
        const institutionInput = document.getElementById('institution');
        const subjectInput = document.getElementById('subject');
        const descriptionInput = document.getElementById('description');
        const attachmentsInput = document.getElementById('attachments');

        if (!form || !financialYearSelect || !auditQuarterSelect) {
            return;
        }

        let financialYearOptions = Array.from(financialYearSelect.querySelectorAll('option')).map((option) => ({
            value: option.value,
            text: option.textContent,
            disabled: option.disabled,
        }));

        let auditQuarterOptions = Array.from(auditQuarterSelect.querySelectorAll('option')).map((option) => ({
            value: option.value,
            text: option.textContent,
            disabled: option.disabled,
            quartercode: option.getAttribute('data-quartercode'),
            financialyear: option.getAttribute('data-financialyear'),
        }));

        const renderFinancialYearOptions = (selectedValue = '') => {
            const currentValue = selectedValue || financialYearSelect.dataset.selectedValue || financialYearSelect.value;

            financialYearSelect.innerHTML = '';

            financialYearOptions.forEach((optionData, index) => {
                const option = document.createElement('option');
                option.value = optionData.value;
                option.textContent = optionData.text;
                option.disabled = optionData.disabled;
                option.selected = currentValue ? currentValue === optionData.value : index === 0;
                financialYearSelect.appendChild(option);
            });

            if (!Array.from(financialYearSelect.options).some((option) => option.value === currentValue)) {
                financialYearSelect.selectedIndex = 0;
            }
        };

        const renderQuarterOptions = (selectedValue = '') => {
            const selectedFinancialYear = financialYearSelect.value;
            const currentQuarterValue = selectedValue || auditQuarterSelect.dataset.selectedValue || auditQuarterSelect.value;

            auditQuarterSelect.innerHTML = '';

            auditQuarterOptions.forEach((optionData) => {
                if (!optionData.value) {
                    const placeholder = document.createElement('option');
                    placeholder.value = optionData.value;
                    placeholder.textContent = optionData.text;
                    placeholder.disabled = true;
                    placeholder.selected = !selectedFinancialYear;
                    auditQuarterSelect.appendChild(placeholder);
                    return;
                }

                if (selectedFinancialYear && optionData.financialyear !== selectedFinancialYear) {
                    return;
                }

                const option = document.createElement('option');
                option.value = optionData.value;
                option.textContent = optionData.text;
                if (optionData.quartercode) {
                    option.setAttribute('data-quartercode', optionData.quartercode);
                }
                if (optionData.financialyear) {
                    option.setAttribute('data-financialyear', optionData.financialyear);
                }
                if (currentQuarterValue && currentQuarterValue === optionData.value) {
                    option.selected = true;
                }
                auditQuarterSelect.appendChild(option);
            });

            if (selectedFinancialYear && !Array.from(auditQuarterSelect.options).some((option) => option.value === currentQuarterValue)) {
                auditQuarterSelect.selectedIndex = 0;
            }
        };

        const replacePlanOptions = (payload) => {
            financialYearOptions = [
                { value: '', text: 'Select financial year', disabled: true },
                ...(payload.financialYears || []).map((option) => ({
                    value: String(option.value ?? ''),
                    text: option.label ?? '',
                    disabled: false,
                })),
            ];

            auditQuarterOptions = [
                { value: '', text: 'Select audit quarter', disabled: true, quartercode: '', financialyear: '' },
                ...(payload.auditQuarters || []).map((option) => ({
                    value: String(option.value ?? ''),
                    text: option.label ?? '',
                    disabled: false,
                    quartercode: option.quartercode ?? '',
                    financialyear: option.financialyearcode ?? '',
                })),
            ];
        };

        const fetchPlanDetails = async (deptCode) => {
            if (!deptCode || !departmentSelect) {
                return;
            }

            const response = await fetch(`${form.dataset.planDetailsUrl}?deptcode=${encodeURIComponent(deptCode)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to load plan details.');
            }

            return response.json();
        };

        const hasBlockedContent = (value) => /(<\s*script\b|<\/\s*script\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\btruncate\b|\balter\b|\bunion\b|\bexec\b|\bexecute\b|\bjavascript:|--|;)/i.test(value);
        const hasRepeatedCharacters = (value) => /([A-Za-z0-9])\1{4,}/.test(value);
        const hasInvalidCharacters = (value, allowNewLines = false) => {
            const pattern = allowNewLines
                ? /^[A-Za-z0-9\s.,:;'"\-()\/&\n\r]*$/
                : /^[A-Za-z0-9\s.,:;'"\-()\/&]*$/;

            return !pattern.test(value);
        };

        const validateTextField = (field, options = {}) => {
            if (!field) {
                return true;
            }

            const value = field.value.trim();

            field.setCustomValidity('');

            if (!value && !options.required) {
                return true;
            }

            if (value && hasBlockedContent(value)) {
                field.setCustomValidity('Script or SQL commands are not allowed.');
            } else if (!options.allowSpecialCharacters && value && hasInvalidCharacters(value, options.allowNewLines)) {
                field.setCustomValidity('Invalid special characters are not allowed.');
            } else if (options.blockRepeated && hasRepeatedCharacters(value)) {
                field.setCustomValidity('Same letter or number cannot repeat 5 times continuously.');
            } else if (options.singleHyphen && (value.match(/-/g) || []).length > 1) {
                field.setCustomValidity('Only one hyphen is allowed.');
            } else if (options.maxLength && value.length > options.maxLength) {
                field.setCustomValidity(`Maximum ${options.maxLength} characters allowed.`);
            }

            return field.checkValidity();
        };

        const validateAttachments = () => {
            if (!attachmentsInput) {
                return true;
            }

            attachmentsInput.setCustomValidity('');

            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const allowedExtensions = ['.jpeg', '.jpg', '.png', '.pdf'];

            for (const file of attachmentsInput.files) {
                const lowerName = file.name.toLowerCase();
                const hasValidExtension = allowedExtensions.some((extension) => lowerName.endsWith(extension));

                if (file.size > 512000) {
                    attachmentsInput.setCustomValidity('Each attachment must be 500KB or smaller.');
                    break;
                }

                if (!(allowedTypes.includes(file.type) || hasValidExtension)) {
                    attachmentsInput.setCustomValidity('Only JPEG, PNG, and PDF files are allowed.');
                    break;
                }
            }

            return attachmentsInput.checkValidity();
        };

        financialYearSelect.addEventListener('change', renderQuarterOptions);

        if (institutionInput) {
            institutionInput.addEventListener('input', function () {
                validateTextField(this, { maxLength: 200, singleHyphen: true });
            });
        }

        if (subjectInput) {
            subjectInput.addEventListener('input', function () {
                validateTextField(this, { required: true, maxLength: 200, blockRepeated: true });
            });
        }

        if (descriptionInput) {
            descriptionInput.addEventListener('input', function () {
                validateTextField(this, { required: true, maxLength: 750, blockRepeated: true, allowNewLines: true, allowSpecialCharacters: true });
            });
        }

        if (attachmentsInput) {
            attachmentsInput.addEventListener('change', validateAttachments);
        }

        if (departmentSelect) {
            departmentSelect.addEventListener('change', async function () {
                try {
                    const payload = await fetchPlanDetails(this.value);
                    replacePlanOptions(payload || {});
                    financialYearSelect.dataset.selectedValue = '';
                    auditQuarterSelect.dataset.selectedValue = '';
                    renderFinancialYearOptions();
                    renderQuarterOptions();
                } catch (error) {
                    replacePlanOptions({ financialYears: [], auditQuarters: [] });
                    renderFinancialYearOptions();
                    renderQuarterOptions();
                }
            });
        }

        form.addEventListener('submit', function (event) {
            const isInstitutionValid = validateTextField(institutionInput, { maxLength: 200, singleHyphen: true });
            const isSubjectValid = validateTextField(subjectInput, { required: true, maxLength: 200, blockRepeated: true });
            const isDescriptionValid = validateTextField(descriptionInput, { required: true, maxLength: 750, blockRepeated: true, allowNewLines: true, allowSpecialCharacters: true });
            const areAttachmentsValid = validateAttachments();

            if (!isInstitutionValid || !isSubjectValid || !isDescriptionValid || !areAttachmentsValid || !form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.reportValidity();
            }
        });

        renderFinancialYearOptions();
        renderQuarterOptions();
    });
</script>
@endsection
