@extends('index2')

@section('title', 'Create Helpdesk Ticket')

@section('content')
<style>
    .fh-page {
        max-width: 1120px;
        margin: 0 auto;
    }

    .fh-card {
        border: 1px solid #dbe5f2;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    }

    .fh-card-header {
        background: linear-gradient(135deg, #476ee8 0%, #2f8f9d 100%);
        color: #fff;
        padding: 10px 16px;
    }

    .fh-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .fh-title-row h4 {
        color: #fff;
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .fh-title-row p {
        color: rgba(255, 255, 255, 0.84);
        margin: 2px 0 0;
        font-size: 12px;
    }

    .fh-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .fh-header-actions .btn {
        border-color: rgba(255, 255, 255, 0.45);
        color: #fff;
        background: rgba(255, 255, 255, 0.12);
        min-height: 32px;
        padding: 5px 11px;
    }

    .fh-header-actions .btn:hover {
        background: #fff;
        border-color: #fff;
        color: #1f4f96;
    }

    .fh-form {
        padding: 16px 18px;
        background:
            linear-gradient(180deg, #f8fbff 0, #fff 120px),
            #fff;
    }

    .fh-form .form-label {
        color: #06122f;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .fh-form .required::after {
        content: " *";
        color: #dc2626;
    }

    .fh-form .form-control,
    .fh-form .form-select {
        border: 1px solid #d6e1ef;
        border-radius: 7px;
        min-height: 40px;
        color: #071330;
        font-size: 13px;
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .fh-form .form-control:focus,
    .fh-form .form-select:focus {
        border-color: #2f8f9d;
        box-shadow: 0 0 0 0.18rem rgba(47, 143, 157, 0.16);
    }

    .fh-form .form-control[readonly] {
        background: #eef5fb;
        color: #31445f;
        font-weight: 600;
    }

    .fh-field {
        min-width: 0;
    }

    .fh-help {
        color: #47607f;
        font-size: 12px;
        margin-top: 3px;
    }

    .fh-actions {
        border-top: 1px solid #edf2f7;
        padding-top: 12px;
    }

    .fh-actions .btn {
        min-width: 112px;
        min-height: 38px;
        font-weight: 700;
        padding: 6px 14px;
    }

    .fh-tips {
        border: 1px solid #d7e2f0;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        position: sticky;
        top: 14px;
    }

    .fh-side-stack {
        position: sticky;
        top: 14px;
    }

    .fh-side-stack .fh-tips {
        position: static;
    }

    .fh-watchlist {
        border: 1px solid #d7e2f0;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        margin-bottom: 14px;
    }

    .fh-watchlist-header {
        background: #5b7dee;
        color: #fff;
        padding: 12px 18px;
        font-size: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fh-watchlist-body {
        padding: 16px 18px;
    }

    .fh-watchlist .form-check {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 0;
    }

    .fh-watchlist .form-check-input {
        flex: 0 0 auto;
        width: 18px;
        height: 18px;
        margin: 2px 0 0;
    }

    .fh-watchlist .form-check-label {
        color: #273b59;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
    }

    .fh-watchlist small {
        display: block;
        color: #47607f;
        font-size: 12px;
        font-weight: 500;
        margin-top: 5px;
    }

    .fh-tips-header {
        background: #5b7dee;
        color: #fff;
        padding: 12px 18px;
        font-size: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fh-tips ul {
        color: #31425f;
        font-size: 14px;
        line-height: 1.35;
        list-style: none;
        margin: 0;
        padding: 16px 20px 18px 24px;
    }

    .fh-tips li {
        display: flex;
        align-items: flex-start;
        gap: 9px;
    }

    .fh-tips li + li {
        margin-top: 13px;
    }

    .fh-tips li i {
        color: #007f86;
        font-size: 14px;
        margin-top: 2px;
        flex: 0 0 auto;
    }

    .fh-tips strong {
        color: #273b59;
    }

    .fh-actions .btn-primary {
        background: #2f6ee8;
        border-color: #2f6ee8;
        box-shadow: 0 10px 20px rgba(47, 110, 232, 0.18);
    }

    .fh-actions .btn-light {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #334155;
    }

    .fh-submit-loader {
        position: fixed;
        inset: 0;
        z-index: 1080;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.32);
        backdrop-filter: blur(2px);
    }

    .fh-submit-loader.is-active {
        display: flex;
    }

    .fh-loader-box {
        min-width: 220px;
        border-radius: 10px;
        background: #fff;
        padding: 22px 26px;
        text-align: center;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .fh-loader-spinner {
        width: 42px;
        height: 42px;
        border: 4px solid #dbeafe;
        border-top-color: #2f6ee8;
        border-radius: 50%;
        margin: 0 auto 12px;
        animation: fhSpin 0.8s linear infinite;
    }

    .fh-loader-text {
        color: #273b59;
        font-size: 14px;
        font-weight: 800;
    }

    @keyframes fhSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 767.98px) {
        .fh-card {
            border-radius: 8px;
        }

        .fh-title-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .fh-header-actions,
        .fh-header-actions .btn,
        .fh-actions .btn {
            width: 100%;
        }

        .fh-form {
            padding: 14px;
        }

        .fh-tips {
            position: static;
        }

        .fh-side-stack {
            position: static;
        }
    }
</style>

<div class="fh-page">
<div class="fh-card">
    <div class="fh-card-header">
        <div class="fh-title-row">
            <div>
                <h4>Create Ticket</h4>
                <p>{{ $isAdminCreator ? '' : ($department->deptesname ?? 'Department not found in session') }}</p>
            </div>
            {{-- <div class="fh-header-actions">
                <a href="{{ route('fresh-helpdesk.ticket-details') }}" class="btn">
                    <i class="ti ti-table me-1"></i> Ticket Details
                </a>
            </div> --}}
        </div>
    </div>
    <div class="fh-form">
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('fresh-helpdesk.tickets.store') }}" enctype="multipart/form-data" id="freshHelpdeskCreateForm">
            @csrf
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-12 fh-field">
                            <label class="form-label required">Department</label>
                            @if ($isAdminCreator)
                                <select name="deptcode" id="freshHelpdeskDepartment" class="form-select" required>
                                    <option value="ALL" @selected(old('deptcode', 'ALL') === 'ALL')>All</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->deptcode }}" @selected(old('deptcode') == $dept->deptcode)>
                                            {{ $dept->deptesname }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="deptcode" id="freshHelpdeskDepartment" value="{{ $department->deptcode ?? '' }}">
                                <input type="text" class="form-control" value="{{ $department->deptesname ?? 'Department not found in session' }}" readonly>
                            @endif
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Financial Year</label>
                            <select name="financialyearcode" id="freshHelpdeskFinancialYear" class="form-select" required>
                                <option value="">Select financial year</option>
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Audit Quarter</label>
                            <select name="planmappingid" id="freshHelpdeskAuditQuarter" class="form-select" required>
                                <option value="">Select audit quarter</option>
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Type</label>
                            <select name="request_type" class="form-select" required>
                                <option value="">Select type</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('request_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Ticket Scope</label>
                            <select name="ticket_scope_type" class="form-select" required>
                                @foreach (($ticketScopeTypes ?? \App\Models\FreshHelpdesk::ticketScopeTypeLabels()) as $value => $label)
                                    <option value="{{ $value }}" @selected(old('ticket_scope_type', \App\Models\FreshHelpdesk::TICKET_SCOPE_SPECIFIED) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Priority</label>
                            <select name="priority" class="form-select" required>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', 'Medium') === $priority)>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label required">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 fh-field">
                            <label class="form-label">Institution <span class="fw-normal text-muted">eg: District - Institution</span></label>
                            <input type="text" name="institution" class="form-control js-first-capital" value="{{ old('institution') }}" autocapitalize="sentences">
                        </div>

                        <div class="col-md-12 fh-field">
                            <label class="form-label required">Subject</label>
                            <input type="text" name="subject" maxlength="200" class="form-control js-first-capital" value="{{ old('subject') }}" autocapitalize="sentences" required>
                            <div class="fh-help">Maximum 200 characters.</div>
                        </div>

                        <div class="col-md-12 fh-field">
                            <label class="form-label required">Description</label>
                            <textarea name="description" rows="4" maxlength="750" class="form-control js-first-capital" autocapitalize="sentences" required>{{ old('description') }}</textarea>
                            <div class="fh-help">Maximum 750 characters. Add exact error, user ID, and screen name.</div>
                        </div>

                        <div class="col-md-12 fh-field">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                            <div class="fh-help">JPEG, PNG, PDF. Maximum 500KB per file.</div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-1 fh-actions">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary" id="freshHelpdeskSubmitBtn">
                                <i class="ti ti-send me-1"></i> Create Ticket
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="fh-side-stack">
	                        <div class="fh-watchlist">
	                            <div class="fh-watchlist-header">
	                                <i class="ti ti-eye-check"></i>
	                                <span>Watchlist</span>
	                            </div>
	                            <div class="fh-watchlist-body">
	                                <div class="form-check">
	                                    <input type="checkbox" name="watchlist_notify" value="1" id="freshHelpdeskWatchlist" class="form-check-input" @checked(old('watchlist_notify'))>
	                                    <label for="freshHelpdeskWatchlist" class="form-check-label">
	                                        Important: Notify NIC Admin
	                                        {{-- <small>Ticket will be auto forwarded to NIC Admin.</small> --}}
	                                    </label>
	                                </div>
	                            </div>
	                        </div>

                        <div class="fh-tips">
                            <div class="fh-tips-header">
                                <i class="ti ti-info-circle"></i>
                                <span>Tips</span>
                            </div>
                            <ul>
                                <li><i class="ti ti-circle-check"></i><span><strong>Be specific:</strong> Provide clear details about your issue</span></li>
                                <li><i class="ti ti-circle-check"></i><span><strong>Select department:</strong> Choose the department first to load financial years and audit quarters</span></li>
                                <li><i class="ti ti-circle-check"></i><span><strong>Set appropriate priority:</strong> Use urgent only for critical issues</span></li>
                                <li><i class="ti ti-circle-check"></i><span><strong>Attach files:</strong> Include screenshots or documents that help explain the issue</span></li>
                                <li><i class="ti ti-circle-check"></i><span><strong>Include steps to reproduce:</strong> If applicable, describe how the issue occurs</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<div class="fh-submit-loader" id="freshHelpdeskSubmitLoader">
    <div class="fh-loader-box">
        <div class="fh-loader-spinner"></div>
        <div class="fh-loader-text">Submitting ticket...</div>
    </div>
</div>
<script>
    $(function () {
        var planOptions = @json($planOptions);
        var selectedDept = @json(old('deptcode', $isAdminCreator ? 'ALL' : ($department->deptcode ?? '')));
        var selectedYear = @json(old('financialyearcode'));
        var selectedQuarter = @json(old('planmappingid'));
        var isAdminCreator = @json($isAdminCreator);
        var successMessage = @json(session('success'));
        var $department = $('#freshHelpdeskDepartment');
        var $financialYear = $('#freshHelpdeskFinancialYear');
        var $quarter = $('#freshHelpdeskAuditQuarter');
        var $form = $('#freshHelpdeskCreateForm');
        var $loader = $('#freshHelpdeskSubmitLoader');
        var $submitButton = $('#freshHelpdeskSubmitBtn');

        function capitalizeFirstTypedLetter(element) {
            var value = element.value;
            var updated = value.replace(/^(\s*)([a-z])/, function (match, spaces, firstLetter) {
                return spaces + firstLetter.toUpperCase();
            });

            if (value === updated) {
                return;
            }

            var start = element.selectionStart;
            var end = element.selectionEnd;
            element.value = updated;

            if (typeof start === 'number' && typeof end === 'number') {
                element.setSelectionRange(start, end);
            }
        }

        function matchesDepartment(option) {
            var departmentCode = isAdminCreator ? $department.val() : selectedDept;

            return !departmentCode || departmentCode === 'ALL' || String(option.deptcode) === String(departmentCode);
        }

        function populateFinancialYears() {
            var seen = {};
            $financialYear.empty().append(new Option('Select financial year', ''));

            planOptions.forEach(function (item) {
                if (!matchesDepartment(item) || seen[item.financialyearcode]) {
                    return;
                }

                seen[item.financialyearcode] = true;
                $financialYear.append(new Option(item.financialyear, item.financialyearcode));
            });

            if (selectedYear && $financialYear.find('option[value="' + selectedYear + '"]').length) {
                $financialYear.val(selectedYear);
            }
        }

        function populateQuarters() {
            var financialYearCode = $financialYear.val();
            var seen = {};
            $quarter.empty().append(new Option('Select audit quarter', ''));

            planOptions.forEach(function (item) {
                if (!financialYearCode || !matchesDepartment(item) || String(item.financialyearcode) !== String(financialYearCode)) {
                    return;
                }

                var key = String(item.planname);
                if (seen[key]) {
                    return;
                }

                seen[key] = true;
                $quarter.append(new Option(item.planname, item.planmappingid));
            });

            if (selectedQuarter && $quarter.find('option[value="' + selectedQuarter + '"]').length) {
                $quarter.val(selectedQuarter);
            }
        }

        if (isAdminCreator && selectedDept) {
            $department.val(selectedDept);
        }

        $department.on('change', function () {
            selectedYear = null;
            selectedQuarter = null;
            populateFinancialYears();
            populateQuarters();
        });

        $financialYear.on('change', function () {
            selectedQuarter = null;
            populateQuarters();
        });

        populateFinancialYears();
        populateQuarters();

        $('.js-first-capital').each(function () {
            capitalizeFirstTypedLetter(this);
        }).on('input change', function () {
            capitalizeFirstTypedLetter(this);
        });

        $form.on('submit', function () {
            $('.js-first-capital').each(function () {
                capitalizeFirstTypedLetter(this);
            });

            $loader.addClass('is-active');
            $submitButton.prop('disabled', true).html('<i class="ti ti-loader-2 me-1"></i> Submitting...');
        });

        if (successMessage) {
            if (typeof passing_alert_value === 'function') {
                passing_alert_value('Confirmation', successMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
            } else {
                alert(successMessage);
            }
        }
    });
</script>
@endsection
