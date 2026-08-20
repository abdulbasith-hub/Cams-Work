@extends('index2')
@section('content')
    @include('common.alert')
@section('title', 'Template Audit Entry')

@php
    $sessionCharge = session('charge');
    $deptcode = $sessionCharge->deptcode;
@endphp
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/forms/select2.init.js') }}"></script>

<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<style>
    .file-input-container.mb-3 {
        color: black;
    }

    input[type="file"]::file-selector-button {
        font-weight: bold;
        color: #000000b8;
    }


    .text-muted {
        color: #000948 !important;
    }

    .modal-footer {
        align-items: normal;
    }

    :root {
        --primary: #2c3e50;
        --secondary: #34495e;
        --accent: #3498db;
        --light: #ecf0f1;
        --success: #27ae60;
        --warning: #f39c12;
        --danger: #e74c3c;
    }

    body {
        font-family: 'Hind Madurai', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
    }


    .header {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .audit-form {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        padding: 10px 20px;
    }

    .section-title {
        color: var(--primary);
        border-bottom: 3px solid var(--accent);
        padding-bottom: 10px;
        margin: 15px 0 15px 0;
        font-weight: 700;
    }

    .auto-field {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 4px solid var(--accent);
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: black;
        font-size: 0.9rem;
    }

    .entry-field {
        background-color: #e8f4fc;
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 4px solid var(--success);
        margin-bottom: 15px;
    }

    .financial-table {
        width: 100%;
        border-collapse: collapse;
    }

    .financial-table th {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        color: white;
        padding: 15px;
        text-align: center;
        font-weight: 600;
    }

    .financial-table td {
        padding: 6px 12px;
        border: 1px solid #dee2e6;
    }

    .financial-table .total-row {
        background-color: #f8f9fa;
        font-weight: 700;
    }

    .notes-section {
        background-color: #fff3cd;
        border-left: 4px solid var(--warning);
        padding: 15px;
        border-radius: 6px;
        margin-top: 12px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent), #2980b9);
        border: none;
        border-radius: 6px;
        padding: 12px 25px;
        font-weight: 600;
        margin-right: 10px;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #219653);
        border: none;
        border-radius: 6px;
        padding: 12px 25px;
        font-weight: 600;
    }

    .language-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .print-only {
        display: none;
    }

    .template-btn.active {
        background: linear-gradient(135deg, var(--secondary), var(--primary)) !important;
    }


    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    @media (max-width: 768px) {
        .audit-form {
            padding: 20px;
        }

        .header {
            padding: 15px;
        }

        .financial-table th,
        .financial-table td {
            padding: 8px 10px;
        }

        .btn-primary,
        .btn-success {
            padding: 10px 15px;
            margin-bottom: 10px;
        }
    }

    @media print {
        .no-print {
            display: none;
        }

        .print-only {
            display: block;
        }

        body {
            font-size: 14px;
            line-height: 1.4;
        }

        .audit-form {
            box-shadow: none;
            padding: 0;
        }

        .header {
            box-shadow: none;
        }

        .btn-primary,
        .btn-success {
            display: none;
        }

    }

    .page-header {
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: center;
    }

    .page-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0;
    }

    /* Enhanced validation styles */

    .is-valid {
        border-color: #198754 !important;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .select2-container.is-valid .select2-selection {
        border-color: #198754 !important;
    }

    .radio-group.is-invalid {
        border: 1px solid #dc3545;
        padding: 10px;
        border-radius: 0.375rem;
    }

    .radio-group.is-valid {
        border: 1px solid #198754;
        padding: 10px;
        border-radius: 0.375rem;
    }

    /* Invalid feedback positioning */
    .invalid-feedback {
        color: #dc3545;
    }

    .select2-search__field {
        display: none !important;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.312);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }

    .cube {
        width: 40px;
        height: 40px;
        background: var(--primary);
        animation: cubeFlip 1.2s infinite ease-in-out;
        transform-style: preserve-3d;
    }

    @keyframes cubeFlip {
        0% {
            transform: rotateX(0) rotateY(0);
        }

        25% {
            transform: rotateX(180deg) rotateY(0);
        }

        50% {
            transform: rotateX(180deg) rotateY(180deg);
        }

        75% {
            transform: rotateX(0) rotateY(180deg);
        }

        100% {
            transform: rotateX(0) rotateY(0);
        }
    }

    .file-input-container {
        position: relative;
    }

    .file-display-overlay {
        position: absolute;
        top: 50%;
        right: 40px;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: none;
    }

    .filename-text {
        max-width: 200px;
        font-size: 0.9em;
    }

    .clear-file-btn {
        pointer-events: auto;
        /* Allow clicking the button */
        padding: 2px 6px;
        font-size: 0.8em;
    }

    /* .file-input-container input[type="file"]::file-selector-button {
                                display: none;
                            } */

    .file-input-container.mb-3,
    .form-control.is-valid {
        color: black;
        background: none;
    }



    .radio-group {
        margin: 0.5rem 0;
    }

    .radio-group .form-check {
        margin-bottom: 0;
    }

    .radio-group.is-invalid {
        border: 1px solid #dc3545;
        border-radius: 0.375rem;
        padding: 0.5rem;
        background-color: #f8d7da;
    }

    .radio-group.is-invalid .form-check-label {
        color: #dc3545;
    }

    .select2-selection.is-invalid {
        border: 1px solid #dc3545 !important;
        border-radius: .375rem;
    }

    .select2-container.is-invalid .select2-selection {
        border-color: #dc3545 !important;
    }

    .select2-container {
        width: 100% !important;
        /* Match Bootstrap form-select width */
    }

    .select2-container--default .select2-selection--multiple {
        min-height: calc(2.25rem + 2px);
        border: 1px solid #ced4da;
        border-radius: .375rem;
        padding: 0.375rem 0.75rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border: none;
        border-radius: .25rem;
        color: #fff;
        padding: 0.1rem 0.5rem;
        margin: 2px 2px 0 0;
        font-size: 0.85rem;
    }

    .accordion-button {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
        border-color: #86b7fe;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
    }

    .accordion-item {
        border: 1px solid #dee2e6;
        margin-bottom: 10px;
        border-radius: 5px;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .accordion-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .accordion-body {
        padding: 1.5rem;
        background-color: #fff;
        transition: background-color 0.3s ease;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }

    .financial-table {
        margin-bottom: 0;
    }

    .financial-table th {
        background-color: #f1f5f9 !important;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .financial-table input[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }



    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
        border-color: #86b7fe;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
    }

    .accordion-collapse {
        transition: height 0.35s ease;
    }

    /* Remove the max-height transition if it causes issues */
    .accordion-collapse:not(.show) {
        display: none;
    }


    .remark-row {
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    .char-count {
        display: none;
        font-size: 0.75em;
        position: absolute;

        right: 10px;
        bottom: 5px;
        background: white;
        padding: 0 5px;
    }

    .char-count.warning {
        color: #ffc107;
        font-weight: bold;
    }

    .char-count.danger {
        color: #dc3545;
        font-weight: bold;
    }

    .add-row,
    .remove-row {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .remark-text {
        padding-right: 60px;
    }

    /* Hide text-muted elements */
    .text-muted.d-none {
        display: none !important;
    }

    /* Fixed height textarea with scroll */
    .remark-text {
        height: 80px !important;
        resize: vertical;
        overflow-y: auto;
        min-height: 80px;
        max-height: 120px;
    }

    /* Optional: Better styling for the textarea */
    .remark-text:focus {
        border-color: #7198b9;
        box-shadow: 0 0 0 0.2rem rgba(113, 152, 185, 0.25);
    }

    .sub-table-input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .sub-table-input.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    .accordion-button.has-required-fields {
        border-left: 4px solid #69d900;
    }

    .required-badge {
        font-size: 0.7em;
    }


    /* Highlight accordions with errors */
    .accordion-item:has(.is-invalid) .accordion-button {
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    #auditcertificateContentdiv {
        background: white !important;
    }

    ul#select2-yearselected-container {
        margin: 0 !important;
    }

    span.select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding: 14px !important;
    }
</style>




<div class="loading-overlay">
    <div class="cube"></div>
</div>

<div class="my-4">
    @foreach ($templatesData as $templateData)
        @php
            $structure = $templateData['structure'] ?? null;
            $isEditMode = isset($hriaData) && $hriaData->statusflag === 'E';
            $isFinalMode = isset($hriaData) && $hriaData->statusflag === 'F';
            $instid = is_object($structure) ? $structure->instid ?? null : $structure['instid'] ?? null;
            $formcode = is_object($structure) ? $structure->formcode ?? null : $structure['formcode'] ?? null;
            $tempplanid = is_object($structure) ? $structure->tempplanid ?? null : $structure['tempplanid'] ?? null;
            $catcode = is_object($structure) ? $structure->catcode ?? null : $structure['catcode'] ?? null;

            $subTables = [];

            if ($structure && isset($structure->headers) && is_array($structure->headers)) {
                foreach ($structure->headers as $header) {
                    if (!empty($header['subtableflag'])) {
                        $subTableFlag = $header['subtableflag'];
                        if (!isset($subTables[$subTableFlag])) {
                            $subTables[$subTableFlag] = [];
                        }
                        $subTables[$subTableFlag][] = $header;
                    }
                }
            }
        @endphp

        @if ($structure && $structure->deptcode === '01' && $structure->formcode === 'HRIA02')
            <form id="hriaOkpForm2" action="{{ route('templateaudit.insertUpdate') }}" method="POST"
                enctype="multipart/form-data">
                <div class="card" style="border-color: #7198b9">
                    <div class="card-header card_header_color" data-english="Template Audit Details - HRIA"
                        data-tamil="டெம்ப்ளேட் தணிக்கை விவரங்கள் - HRIA">
                        Template Audit Details - HRIA
                    </div>
                    <div class="audit-form">
                        <div class="page-header">
                            @csrf
                            @if ($isEditMode || $isFinalMode)
                                <input type="hidden" name="okpid"
                                    value="{{ Crypt::encryptString($hriaData->okpsdetails_id) }}">
                            @endif
                            <input type="hidden" name="tempplanid"
                                value="{{ Crypt::encryptString($structure->tempplanid) }}">
                        </div>
                        <input type="hidden" name="action"
                            value="{{ $isEditMode || $isFinalMode ? 'update' : 'insert' }}">

                        <div id="template-content">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-6">
                                    <label class="form-label" data-english="Institution Name and Address"
                                        data-tamil="நிறுவனத்தின் பெயர் மற்றும் முகவரி">
                                        Institution Name and Address
                                    </label>
                                    <div class="auto-field">{{ $basicinformation->instename }}</div>
                                </div>

                                <div class="col-md-6 col-lg-2">
                                    <label class="form-label" data-english="Reference Number" data-tamil="குறிப்பு எண்">
                                        Reference Number
                                    </label>
                                    <div class="auto-field">{{ $basicinformation->erpno }}</div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    @if ($isFinalMode)
                                        <label class="form-label lang" data-english="Audit End Date"
                                            data-tamil="தணிக்கை முடிவு தேதி">
                                            Audit End Date
                                        </label>
                                    @else
                                        <label class="form-label lang" data-english="Audit Date"
                                            data-tamil="தணிக்கை தேதி">
                                            Audit Date
                                        </label>
                                    @endif
                                    <div class="auto-field">
                                        @if ($isFinalMode)
                                            {{ $basicinformation->audit_end_date }}
                                        @else
                                            {{ now()->format('d-m-Y') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label" data-english="Auditor Name"
                                        data-tamil="தணிக்கையாளர் பெயர்">
                                        Auditor Name
                                    </label>
                                    <div class="auto-field">{{ $basicinformation->auditor_name_en }}</div>
                                </div>

                                @if(!empty($basicinformation->zoneename))
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label" data-english="Assistant Commissioner Zone"
                                        data-tamil="உதவி ஆணையர் மண்டலம்">
                                        Assistant Commissioner Zone
                                    </label>
                                    <div class="auto-field">{{ $basicinformation->zoneename }}</div>
                                </div>
                                @endif

                                @if(!empty($basicinformation->circleename))
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label" data-english="Inspector Circle"
                                        data-tamil="ஆய்வாளர் வட்டம்">
                                        Inspector Circle
                                    </label>
                                    <div class="auto-field">{{ $basicinformation->circleename }}</div>
                                </div>
                                @endif
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label required" data-english="Audit Year"
                                        data-tamil="தணிக்கை ஆண்டு">
                                        Audit Year
                                    </label>
                                    <select name="yearselected[]" id="yearselected" placeholder="Select Year"
                                        class="form-select mr-sm-2" multiple="multiple"
                                        {{ $isFinalMode ? 'disabled' : '' }} required>
                                        <option value="" disabled data-english="Select Year"
                                            data-tamil="ஆண்டைத் தேர்ந்தெடுக்கவும்">
                                            Select Year
                                        </option>
                                        @foreach ($auditperiod as $aud_period)
                                            <option value="{{ $aud_period->auditperiodid }}"
                                                {{ is_array($selectedYears) && in_array($aud_period->auditperiodid, $selectedYears) ? 'selected' : '' }}>
                                                {{ $aud_period->audit_period }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($isFinalMode && !empty($selectedYears))
                                        @foreach ($selectedYears as $yearId)
                                            <input type="hidden" name="yearselected[]" value="{{ $yearId }}">
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="priest_name" class="form-label required"
                                        data-english="Priest/Poojari Name" data-tamil="பூசாரி பெயர்">
                                        Priest/Poojari Name
                                    </label>
                                    <input type="text" class="form-control" id="priest_name" name="priest_name"
                                        placeholder="Enter Priest/Poojari name" required maxlength="80"
                                        pattern="^[A-Za-z]{1,60}$" inputmode="text"
                                        value="{{ $isEditMode || $isFinalMode ? $hriaData->poosari_name : old('priest_name') }}"
                                        {{ $isFinalMode ? 'readonly' : '' }} />
                                </div>

                                <div class="col-md-4">
                                    <label for="phone_number" class="form-label required" data-english="Phone Number"
                                        data-tamil="தொலைபேசி எண்">
                                        Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                        placeholder="Enter Phone number" required maxlength="10" inputmode="numeric"
                                        pattern="^[6-9][0-9]{9}$"
                                        value="{{ $isEditMode || $isFinalMode ? $hriaData->phone_number : old('phone_number') }}"
                                        {{ $isFinalMode ? 'readonly' : '' }} />
                                </div>

                                <div class="col-md-4">
                                    <label for="bank_name" class="form-label lang required"
                                        data-english="Bank Name"
                                        data-tamil="வங்கி பெயர்">
                                        Bank Name
                                    </label>

                                    <select class="form-select mr-sm-2 lang-dropdown"
                                        id="bank_name"
                                        name="bank_name"
                                        required {{ $isFinalMode ? 'disabled' : '' }}>

                                        <option value="" selected
                                            data-english="Select Bank"
                                            data-tamil="வங்கியைத் தேர்ந்தெடுக்கவும்">
                                            Select Bank
                                        </option>

                                        @foreach ($banksGrouped as $bank)
                                            <option value="{{ $bank->bankid }}"
                                                {{ ($isEditMode || $isFinalMode) && data_get($hriaData, 'bankid') == $bank->bankid ? 'selected' : '' }} data-bankname ="{{ $bank->bank_name }}">
                                                {{ $bank->bank_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="bankname" id="bankname">


                                    @if ($isFinalMode)
                                        <input type="hidden" name="bank_name" value="{{ data_get($hriaData, 'bankid') }}">
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label for="branch_name" class="form-label required" data-english="Branch Name"
                                        data-tamil="கிளை பெயர்">
                                        Branch Name
                                    </label>
                                    <input type="text" class="form-control" id="branch_name" name="branch_name"
                                        placeholder="Enter Branch name" required maxlength="60"
                                        pattern="^[A-Za-z .'\-]{1,60}$"
                                        value="{{ $isEditMode || $isFinalMode ? $hriaData->branch_name : old('branch_name') }}"
                                        {{ $isFinalMode ? 'readonly' : '' }} />
                                </div>

                                <div class="col-md-4">
                                    <label for="account_number" class="form-label required"
                                        data-english="Account Number" data-tamil="கணக்கு எண்">
                                        Account Number
                                    </label>
                                    <input type="text" class="form-control" id="account_number"
                                        name="account_number" placeholder="Enter Account number" required
                                        maxlength="18" inputmode="numeric" pattern="^[0-9]{9,16}$"
                                        value="{{ $isEditMode || $isFinalMode ? $hriaData->account_no : old('account_number') }}"
                                        {{ $isFinalMode ? 'readonly' : '' }} />
                                </div>

 {{-- <div class="col-md-4">
                                <label class="form-label required">IFSC Code</label>

                                <input type="text" class="form-control" id="ifsc_prefix"
                                    value="{{ data_get($hriaData,'ifsc_code') ? substr(strtoupper(data_get($hriaData,'ifsc_code')),0,4) : '' }}"
                                    />

                            </div> --}}
                            <input type="hidden" id="ifsc_code" name="ifsc_code"
                                value="{{ data_get($hriaData,'ifsc_code') ? strtoupper(data_get($hriaData,'ifsc_code')) : '' }}">


                            </div>

                            <div class="row mt-4">
                                @php
                                    $debugSubTables = [];
                                    if ($structure && isset($structure->headers) && is_array($structure->headers)) {
                                        foreach ($structure->headers as $header) {
                                            if (!empty($header['subtableflag'])) {
                                                $subTableFlag = $header['subtableflag'];
                                                if (!isset($debugSubTables[$subTableFlag])) {
                                                    $debugSubTables[$subTableFlag] = [];
                                                }
                                                $debugSubTables[$subTableFlag][] = $header;
                                            }
                                        }
                                    }

                                    $subTables = $debugSubTables;

                                    $subTableAmountValues = [];
                                    if ($isEditMode || $isFinalMode) {
                                        foreach ($hriaData->amounts as $amount) {
                                            $value = match ($amount->temptypeid) {
                                                1 => $amount->value_numeric,
                                                2 => $amount->value_char,
                                                3 => $amount->value_date,
                                                4 => $amount->value_radio,
                                                default => '',
                                            };
                                            $subTableAmountValues[$amount->maintypeid] = $value;
                                        }
                                    }

                                    $accordionTitles = [
                                        'closing_balance' => [
                                            'english' => 'Capital accounts balance details at the end of the audit',
                                            'tamil' => 'தணிக்கையின் முடிவில் மூலதன கணக்குகள் இருப்பு விபரம்',
                                        ],
                                        'jewel_valuation' => [
                                            'english' => 'Value of jewelry according to appraisal report',
                                            'tamil' => 'மதிப்பீடு அறிக்கையின் படி நகைகளின் மதிப்பு',
                                        ],
                                    ];
                                @endphp

                                @if (count($subTables) > 0)
                                    <div class="col-12">
                                        <div class="accordion" id="subTablesAccordion">
                                            @foreach ($subTables as $subTableFlag => $subheaders)
                                                @php
                                                    $firstHeader = $subheaders[0] ?? null;
                                                    $maintypes = $firstHeader['maintypes'] ?? [];
                                                    $accordionId = "accordion_{$subTableFlag}_{$loop->index}";
                                                    $collapseId = "collapse_{$subTableFlag}_{$loop->index}";

                                                    $title = $accordionTitles[$subTableFlag] ?? [
                                                        'english' => ucfirst(str_replace('_', ' ', $subTableFlag)),
                                                        'tamil' => ucfirst(str_replace('_', ' ', $subTableFlag)),
                                                    ];

                                                    $hasRequiredFields = !empty($maintypes);
                                                @endphp

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="{{ $accordionId }}_header">
                                                        <button
                                                            class="accordion-button collapsed @if ($hasRequiredFields) has-required-fields @endif"
                                                            type="button" aria-expanded="false"
                                                            aria-controls="{{ $collapseId }}"
                                                            data-required="{{ $hasRequiredFields ? 'true' : 'false' }}">
                                                            <i class="fas fa-table me-2"></i>
                                                            <span data-english="{{ $title['english'] }}"
                                                                data-tamil="{{ $title['tamil'] }}">
                                                                {{ $title['english'] }}
                                                            </span>
                                                        </button>
                                                    </h2>

                                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                                        aria-labelledby="{{ $accordionId }}_header">
                                                        <div class="accordion-body p-0">
                                                            <div class="table-responsive">
                                                                <table
                                                                    class="financial-table table table-bordered mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            @foreach ($subheaders as $header)
                                                                                <th class="text-center"
                                                                                    data-english="{{ $header['lblename'] ?? 'N/A' }}"
                                                                                    data-tamil="{{ $header['lbltname'] ?? 'N/A' }}">
                                                                                    {{ $header['lblename'] ?? 'N/A' }}
                                                                                </th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if (!empty($maintypes))
                                                                            @foreach ($maintypes as $maintype)
                                                                                @php
                                                                                    $maintypeId =
                                                                                        $maintype['maintypeid'];
                                                                                    $value =
                                                                                        $subTableAmountValues[
                                                                                            $maintypeId
                                                                                        ] ?? '';
                                                                                    $isRequired =
                                                                                        empty(
                                                                                            $maintype[
                                                                                                'maintypetotalflag'
                                                                                            ]
                                                                                        ) ||
                                                                                        $maintype[
                                                                                            'maintypetotalflag'
                                                                                        ] !== 'T';
                                                                                    $temptypeid =
                                                                                        $maintype['temptypeid'] ?? 1;
                                                                                    $isTotal =
                                                                                        !empty(
                                                                                            $maintype[
                                                                                                'maintypetotalflag'
                                                                                            ]
                                                                                        ) &&
                                                                                        $maintype[
                                                                                            'maintypetotalflag'
                                                                                        ] === 'T';
                                                                                @endphp

                                                                                <tr
                                                                                    class="@if ($isTotal) table-secondary fw-bold @endif">
                                                                                    <td class="fw-medium"
                                                                                        data-english="{{ $maintype['maintypeename'] }}"
                                                                                        data-tamil="{{ $maintype['maintypetname'] }}">
                                                                                        {{ $maintype['maintypeename'] }}
                                                                                        @if ($isRequired && !$isFinalMode)
                                                                                            <span
                                                                                                class="text-danger">*</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    @for ($i = 1; $i < count($subheaders); $i++)
                                                                                        <td>
                                                                                            @if ($i === 1)
                                                                                                @php
                                                                                                    $inputType =
                                                                                                        'number';
                                                                                                    $step = '0.01';
                                                                                                    $pattern =
                                                                                                        '^[0-9]*\.?[0-9]{0,2}$';
                                                                                                    $placeholder =
                                                                                                        '0.00';
                                                                                                    $maxlength = '';
                                                                                                    $radioOptions = [
                                                                                                        'Yes',
                                                                                                        'No',
                                                                                                    ];

                                                                                                    switch (
                                                                                                        $temptypeid
                                                                                                    ) {
                                                                                                        case 1: // Numeric
                                                                                                            $inputType =
                                                                                                                'number';
                                                                                                            $step =
                                                                                                                '0.01';
                                                                                                            $pattern =
                                                                                                                '^[0-9]*\.?[0-9]{0,2}$';
                                                                                                            $placeholder =
                                                                                                                '';
                                                                                                            break;
                                                                                                        case 2: // Character
                                                                                                            $inputType =
                                                                                                                'text';
                                                                                                            $step = '';
                                                                                                            $pattern =
                                                                                                                '';
                                                                                                            $placeholder =
                                                                                                                'Enter text';
                                                                                                            $maxlength =
                                                                                                                '80';
                                                                                                            break;
                                                                                                        case 3: // Date
                                                                                                            $inputType =
                                                                                                                'date';
                                                                                                            $step = '';
                                                                                                            $pattern =
                                                                                                                '';
                                                                                                            $placeholder =
                                                                                                                'YYYY-MM-DD';
                                                                                                            break;
                                                                                                        case 4: // Radio
                                                                                                            $inputType =
                                                                                                                'radio';
                                                                                                            $step = '';
                                                                                                            $pattern =
                                                                                                                '';
                                                                                                            $placeholder =
                                                                                                                '';
                                                                                                            break;
                                                                                                        default:
                                                                                                            $inputType =
                                                                                                                'text';
                                                                                                            $step = '';
                                                                                                            $pattern =
                                                                                                                '';
                                                                                                            $placeholder =
                                                                                                                'Enter value';
                                                                                                            break;
                                                                                                    }
                                                                                                @endphp

                                                                                                @if ($inputType === 'radio')
                                                                                                    <div class="radio-group d-flex gap-3 justify-content-end"
                                                                                                        data-required="{{ $isRequired && !$isFinalMode ? 'true' : 'false' }}">
                                                                                                        @foreach ($radioOptions as $option)
                                                                                                            @php
                                                                                                                $optionValue = strtolower(
                                                                                                                    $option,
                                                                                                                );
                                                                                                                $isChecked =
                                                                                                                    $value ===
                                                                                                                        $optionValue ||
                                                                                                                    $value ===
                                                                                                                        $option;
                                                                                                                $radioId = "{$subTableFlag}_{$maintypeId}_{$optionValue}";
                                                                                                            @endphp
                                                                                                            <div
                                                                                                                class="form-check">
                                                                                                                <input
                                                                                                                    type="radio"
                                                                                                                    class="form-check-input"
                                                                                                                    id="{{ $radioId }}"
                                                                                                                    name="amount[{{ $subTableFlag }}][{{ $maintypeId }}]"
                                                                                                                    value="{{ $optionValue }}"
                                                                                                                    {{ $isChecked ? 'checked' : '' }}
                                                                                                                    {{ $isFinalMode || $isTotal ? 'disabled' : '' }}>
                                                                                                                <label
                                                                                                                    class="form-check-label"
                                                                                                                    for="{{ $radioId }}">{{ $option }}</label>
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    </div>
                                                                                                @else
                                                                                                    <input
                                                                                                        type="{{ $inputType }}"
                                                                                                        class="form-control text-end {{ $subTableFlag }}-field @if ($isTotal) {{ $subTableFlag }}-total @else sub-table-input @endif"
                                                                                                        @if ($isTotal) id="{{ $subTableFlag }}Total" @endif
                                                                                                        name="amount[{{ $subTableFlag }}][{{ $maintypeId }}]"
                                                                                                        {{-- name="amount[pair{{ $pairIndex }}][{{ $pair['category']['tmpauditheaderid'] }}][{{ $item['maintypeid'] }}]" --}}

                                                                                                        data-maintypeid="{{ $maintypeId }}"
                                                                                                        data-subtable="{{ $subTableFlag }}"
                                                                                                        data-temptypeid="{{ $temptypeid }}"
                                                                                                        data-is-total="{{ $isTotal ? 'true' : 'false' }}"
                                                                                                        value="{{ $value }}"
                                                                                                        placeholder="{{ $placeholder }}"
                                                                                                        {{ $isFinalMode || $isTotal ? 'readonly' : '' }}
                                                                                                        @if (!$isTotal) oninput="updateSubTableTotal('{{ $subTableFlag }}')"
                                                                                                                onblur="updateSubTableTotal('{{ $subTableFlag }}')" @endif>
                                                                                                @endif
                                                                                            @else
                                                                                                <span
                                                                                                    class="text-muted">-</span>
                                                                                            @endif
                                                                                        </td>
                                                                                    @endfor
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="{{ count($subheaders) }}"
                                                                                    class="text-center text-muted">
                                                                                    No data available
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No sub-tables available for this section.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="table-responsive">
                                <h4 class="section-title" data-english="Audit Details"
                                    data-tamil="தணிக்கை விவரங்கள்">
                                    Audit Details
                                </h4>
                                <table class="financial-table table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach ($structure->headers as $header)
                                                @if (!isset($header['subtableflag']) || $header['subtableflag'] === null)
                                                    <th data-english="{{ $header['lblename'] ?? '' }}"
                                                        data-tamil="{{ $header['lbltname'] ?? '' }}">
                                                        {{ $header['lblename'] ?? '' }}
                                                    </th>
                                                @endif
                                            @endforeach
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @php
                                            $pairs = [];
                                            $headers = $structure->headers;
                                            $headers = array_values(
                                                array_filter(
                                                    $structure->headers,
                                                    fn($h) => !isset($h['subtableflag']) || $h['subtableflag'] === null,
                                                ),
                                            );

                                            for ($i = 0; $i < count($headers); $i++) {
                                                if (
                                                    $headers[$i]['inputtype'] === 'C' &&
                                                    isset($headers[$i + 1]) &&
                                                    $headers[$i + 1]['inputtype'] === 'N'
                                                ) {
                                                    $pairs[] = [
                                                        'category' => $headers[$i],
                                                        'amount' => $headers[$i + 1],
                                                    ];
                                                    $i++;
                                                }
                                            }

                                            $inputIndex = 1;
                                            $amountValues = [];

                                            if ($isEditMode || $isFinalMode) {
                                                foreach ($hriaData->amounts as $amount) {
                                                    $value = match ($amount->temptypeid) {
                                                        1 => $amount->value_numeric,
                                                        2 => $amount->value_char,
                                                        3 => $amount->value_date,
                                                        4 => $amount->value_radio,
                                                        default => '',
                                                    };
                                                    $amountValues[$amount->maintypeid] = $value;
                                                }
                                            }

                                            $leftPairs = [];
                                            $rightPairs = [];
                                            foreach ($pairs as $i => $pair) {
                                                if ($i % 2 === 0) {
                                                    $leftPairs[] = $pair;
                                                } else {
                                                    $rightPairs[] = $pair;
                                                }
                                            }

                                            foreach ($leftPairs as &$pair) {
                                                $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
                                            }
                                            unset($pair);
                                            foreach ($rightPairs as &$pair) {
                                                $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
                                            }
                                            unset($pair);

                                            $alignmentMap = [
                                                'I' => 'G',
                                                'R' => 'E',
                                                'O' => 'C',
                                                'V' => 'X',
                                            ];

                                            foreach ($alignmentMap as $leftFlag => $rightFlag) {
                                                $lIndex = null;
                                                $rIndex = null;
                                                foreach ($leftPairs as $lpIndex => $pair) {
                                                    $idx = collect($pair['maintypes'])->search(
                                                        fn($item) => isset($item['maintypetotalflag']) &&
                                                            $item['maintypetotalflag'] === $leftFlag,
                                                    );
                                                    if ($idx !== false) {
                                                        $lIndex = [$lpIndex, $idx];
                                                        break;
                                                    }
                                                }
                                                foreach ($rightPairs as $rpIndex => $pair) {
                                                    $idx = collect($pair['maintypes'])->search(
                                                        fn($item) => isset($item['maintypetotalflag']) &&
                                                            $item['maintypetotalflag'] === $rightFlag,
                                                    );
                                                    if ($idx !== false) {
                                                        $rIndex = [$rpIndex, $idx];
                                                        break;
                                                    }
                                                }

                                                if ($lIndex && $rIndex) {
                                                    $diff = $rIndex[1] - $lIndex[1];
                                                    if ($diff > 0) {
                                                        array_splice(
                                                            $leftPairs[$lIndex[0]]['maintypes'],
                                                            $lIndex[1],
                                                            0,
                                                            array_fill(0, $diff, null),
                                                        );
                                                    } elseif ($diff < 0) {
                                                        array_splice(
                                                            $rightPairs[$rIndex[0]]['maintypes'],
                                                            $rIndex[1],
                                                            0,
                                                            array_fill(0, abs($diff), null),
                                                        );
                                                    }
                                                }
                                            }

                                            $maxLeft = max(
                                                array_map(fn($p) => count($p['maintypes']), $leftPairs) ?: [0],
                                            );
                                            $maxRight = max(
                                                array_map(fn($p) => count($p['maintypes']), $rightPairs) ?: [0],
                                            );
                                            $totalRows = max($maxLeft, $maxRight);
                                        @endphp

                                        @for ($row = 0; $row < $totalRows; $row++)
                                            <tr>
                                                @foreach ($leftPairs as $pairIndex => $pair)
                                                    @php
                                                        $item = $pair['maintypes'][$row] ?? null;
                                                        $className = $isRequired = $value = '';
                                                        if ($item) {
                                                            $className =
                                                                strtolower(
                                                                    preg_replace(
                                                                        '/[^a-z0-9]+/i',
                                                                        '-',
                                                                        $pair['category']['lblename'] ?? '',
                                                                    ),
                                                                ) . '-input';
                                                            $isRequired =
                                                                empty($item['maintypetotalflag'] ?? '') ||
                                                                ($item['maintypetotalflag'] ?? '') === 'O';
                                                            $value = $amountValues[$item['maintypeid']] ?? '';
                                                            if (($item['temptypeid'] ?? 0) === 1 && $value !== '') {
                                                                $value = number_format($value, 2, '.', '');
                                                            }
                                                        }
                                                    @endphp

                                                    <td
                                                        class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif">
                                                        @if ($item)
                                                            <span
                                                                @if ($isRequired && !$isFinalMode) aria-required="true" @endif
                                                                data-english="{{ $item['maintypeename'] ?? '' }}"
                                                                data-tamil="{{ $item['maintypetname'] ?? '' }}">
                                                                {{ $item['maintypeename'] ?? '' }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td
                                                        class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif">
                                                        @if ($item && !empty($item['maintypeid']))
                                                            @php
                                                                $inputType = 'text';
                                                                $step = '0.01';
                                                                $maxlength = '80';
                                                                $pattern = '.*';
                                                                $radioOptions = ['Yes', 'No'];
                                                                switch ($item['temptypeid'] ?? 1) {
                                                                    case 1:
                                                                        $inputType = 'number';
                                                                        $step = '0.01';
                                                                        $maxlength = '';
                                                                        $pattern = '^[0-9]*\.?[0-9]{0,2}$';
                                                                        break;
                                                                    case 2:
                                                                        $inputType = 'text';
                                                                        $step = '';
                                                                        $maxlength = '80';
                                                                        $pattern = '';
                                                                        break;
                                                                    case 3:
                                                                        $inputType = 'date';
                                                                        $step = '';
                                                                        $maxlength = '';
                                                                        $pattern = '';
                                                                        break;
                                                                    case 4:
                                                                        $inputType = 'radio';
                                                                        $step = '';
                                                                        $maxlength = '';
                                                                        $pattern = '';
                                                                        break;
                                                                    default:
                                                                        $inputType = 'text';
                                                                        $step = '';
                                                                        $maxlength = '80';
                                                                        $pattern = '';
                                                                        break;
                                                                }
                                                            @endphp

                                                            @if ($inputType === 'radio')
                                                                <div class="radio-group d-flex gap-3 justify-content-end"
                                                                    data-required="{{ $isRequired && !$isFinalMode ? 'true' : 'false' }}">
                                                                    @foreach ($radioOptions as $option)
                                                                        @php
                                                                            $optionValue = strtolower($option);
                                                                            $isChecked =
                                                                                $value === $optionValue ||
                                                                                $value === $option;
                                                                            $radioId = "E{$inputIndex}_{$optionValue}";
                                                                        @endphp
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                id="{{ $radioId }}"
                                                                                name="amount[left][pair{{ $pairIndex }}][{{ $item['maintypeid'] }}]"
                                                                                value="{{ $optionValue }}"
                                                                                {{ $isChecked ? 'checked' : '' }}
                                                                                {{ $isFinalMode ? 'disabled' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="{{ $radioId }}">{{ $option }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <input type="{{ $inputType }}"
                                                                    class="form-control text-end {{ $className }} @if ($inputType === 'number') amount-input @endif"
                                                                    id="E{{ $inputIndex }}"
                                                                    data-cell="E{{ $inputIndex }}"
                                                                    data-type="{{ $item['maintypetotalflag'] ?? 'value' }}"
                                                                    data-temptypeid="{{ $item['temptypeid'] ?? 1 }}"
                                                                    name="amount[left][pair{{ $pairIndex }}][{{ $item['maintypeid'] }}]"
                                                                    @if (!empty($item['maintypetotalflag']) && $item['maintypetotalflag'] !== 'O') readonly @endif
                                                                    @if ($isFinalMode) readonly @endif
                                                                    @if ($inputType === 'number') step="{{ $step }}" @endif
                                                                    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                                                                    @if ($pattern) pattern="{{ $pattern }}" @endif
                                                                    placeholder="@if ($inputType === 'number') @elseif($inputType === 'date') YYYY-MM-DD @else Enter value @endif"
                                                                    value="{{ $value }}">
                                                            @endif
                                                            @php $inputIndex++; @endphp
                                                        @endif
                                                    </td>
                                                @endforeach

                                                @foreach ($rightPairs as $pairIndex => $pair)
                                                    @php
                                                        $item = $pair['maintypes'][$row] ?? null;
                                                        $className = $isRequired = $value = '';
                                                        if ($item) {
                                                            $className =
                                                                strtolower(
                                                                    preg_replace(
                                                                        '/[^a-z0-9]+/i',
                                                                        '-',
                                                                        $pair['category']['lblename'] ?? '',
                                                                    ),
                                                                ) . '-input';
                                                            $isRequired =
                                                                empty($item['maintypetotalflag'] ?? '') ||
                                                                ($item['maintypetotalflag'] ?? '') === 'O';
                                                            $value = $amountValues[$item['maintypeid']] ?? '';
                                                            if (($item['temptypeid'] ?? 0) === 1 && $value !== '') {
                                                                $value = number_format($value, 2, '.', '');
                                                            }
                                                        }
                                                    @endphp

                                                    <td
                                                        class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif">
                                                        @if ($item)
                                                            <span
                                                                @if ($isRequired && !$isFinalMode) aria-required="true" @endif
                                                                data-english="{{ $item['maintypeename'] ?? '' }}"
                                                                data-tamil="{{ $item['maintypetname'] ?? '' }}">
                                                                {{ $item['maintypeename'] ?? '' }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td
                                                        class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif">
                                                        @if ($item && !empty($item['maintypeid']))
                                                            @php
                                                                $inputType = 'text';
                                                                $step = '0.01';
                                                                $maxlength = '80';
                                                                $pattern = '.*';
                                                                $radioOptions = ['Yes', 'No'];
                                                                switch ($item['temptypeid'] ?? 1) {
                                                                    case 1:
                                                                        $inputType = 'number';
                                                                        $step = '0.01';
                                                                        $maxlength = '';
                                                                        $pattern = '^[0-9]*\.?[0-9]{0,2}$';
                                                                        break;
                                                                    case 2:
                                                                        $inputType = 'text';
                                                                        $step = '';
                                                                        $maxlength = '80';
                                                                        $pattern = '';
                                                                        break;
                                                                    case 3:
                                                                        $inputType = 'date';
                                                                        $step = '';
                                                                        $maxlength = '';
                                                                        $pattern = '';
                                                                        break;
                                                                    case 4:
                                                                        $inputType = 'radio';
                                                                        $step = '';
                                                                        $maxlength = '';
                                                                        $pattern = '';
                                                                        break;
                                                                    default:
                                                                        $inputType = 'text';
                                                                        $step = '';
                                                                        $maxlength = '80';
                                                                        $pattern = '';
                                                                        break;
                                                                }
                                                            @endphp

                                                            @if ($inputType === 'radio')
                                                                <div class="radio-group d-flex gap-3 justify-content-end"
                                                                    data-required="{{ $isRequired && !$isFinalMode ? 'true' : 'false' }}">
                                                                    @foreach ($radioOptions as $option)
                                                                        @php
                                                                            $optionValue = strtolower($option);
                                                                            $isChecked =
                                                                                $value === $optionValue ||
                                                                                $value === $option;
                                                                            $radioId = "E{$inputIndex}_{$optionValue}";
                                                                        @endphp
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                id="{{ $radioId }}"
                                                                                name="amount[right][pair{{ $pairIndex }}][{{ $item['maintypeid'] }}]"
                                                                                value="{{ $optionValue }}"
                                                                                {{ $isChecked ? 'checked' : '' }}
                                                                                {{ $isFinalMode ? 'disabled' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="{{ $radioId }}">{{ $option }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <input type="{{ $inputType }}"
                                                                    class="form-control text-end {{ $className }} @if ($inputType === 'number') amount-input @endif"
                                                                    id="E{{ $inputIndex }}"
                                                                    data-cell="E{{ $inputIndex }}"
                                                                    data-type="{{ $item['maintypetotalflag'] ?? 'value' }}"
                                                                    data-temptypeid="{{ $item['temptypeid'] ?? 1 }}"
                                                                    name="amount[right][pair{{ $pairIndex }}][{{ $item['maintypeid'] }}]"
                                                                    @if (!empty($item['maintypetotalflag']) && $item['maintypetotalflag'] !== 'O') readonly @endif
                                                                    @if ($isFinalMode) readonly @endif
                                                                    @if ($inputType === 'number') step="{{ $step }}" min="0" @endif
                                                                    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                                                                    @if ($pattern) pattern="{{ $pattern }}" @endif
                                                                    placeholder="@if ($inputType === 'number') @elseif($inputType === 'date') YYYY-MM-DD @else Enter value @endif"
                                                                    value="{{ $value }}">
                                                            @endif
                                                            @php $inputIndex++; @endphp
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <div class="notes-section">
                                <div class="">
                                    <h4 class="section-title required" style="border: none;"
                                        data-english="Audit Remarks" data-tamil="தணிக்கை கருத்துகள்">
                                        Audit Remarks
                                    </h4>

                                    @if ($isFinalMode)
                                        <div class="form-control bg-light"
                                            style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6;">
                                            @if (!empty($existingRemarks))
                                                @foreach ($existingRemarks as $index => $remark)
                                                    <div class="mb-3 p-2 border-bottom">
                                                        <strong>{{ $index + 1 }}.</strong>
                                                        <span
                                                            class="badge {{ (is_object($remark) ? $remark->type : $remark['type']) == 'serious' ? 'bg-danger' : 'bg-warning' }} text-capitalize ms-2">
                                                            {{ is_object($remark) ? $remark->type : $remark['type'] }}
                                                        </span>
                                                        <div class="mt-1">
                                                            {{ is_object($remark) ? $remark->remark_text : $remark['text'] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span data-english="No remarks" data-tamil="கருத்துகள் இல்லை">No
                                                    remarks</span>
                                            @endif
                                        </div>
                                    @else
                                        <div id="dynamicRowsContainer">
                                            @for ($i = 0; $i < $currentRows; $i++)
                                                <div class="remark-row mb-3 p-3 border rounded"
                                                    data-index="{{ $i }}">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-1">
                                                            <label
                                                                class="form-label fw-bold">{{ $i + 1 }}.</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-select remark-type"
                                                                name="remarks[{{ $i }}][type]" required>
                                                                <option value="" disabled
                                                                    {{ !isset($existingRemarks[$i]) ? 'selected' : '' }}
                                                                    data-english="Type" data-tamil="வகை">Type</option>
                                                                <option value="serious"
                                                                    {{ isset($existingRemarks[$i]) && (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->type : $existingRemarks[$i]['type']) == 'serious' ? 'selected' : '' }}
                                                                    data-english="Serious" data-tamil="தீவிரமான">
                                                                    Serious</option>
                                                                <option value="non-serious"
                                                                    {{ isset($existingRemarks[$i]) && (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->type : $existingRemarks[$i]['type']) == 'non-serious' ? 'selected' : '' }}
                                                                    data-english="Non-Serious" data-tamil="தீவிரமற்ற">
                                                                    Non-Serious</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative">
                                                                <textarea class="form-control remark-text" id="remarks" name="remarks[{{ $i }}][text]" rows="2"
                                                                    placeholder="Enter remark details..."  required style="resize: vertical; overflow-y: auto;">{{ isset($existingRemarks[$i]) ? (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->remark_text : $existingRemarks[$i]['text']) : '' }}</textarea>
                                                                <small class="text-muted d-none"><span
                                                                        class="char-count">0</span>/500</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            @if ($i === $currentRows - 1 && $currentRows < $maxRows)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-success add-row"
                                                                    title="Add new remark">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            @endif
                                                            @if ($i > 0)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger remove-row mt-1"
                                                                    title="Remove this remark">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>

                                        <div class="invalid-feedback"
                                            data-english="Please add at least one audit remark"
                                            data-tamil="குறைந்தது ஒரு தணிக்கை கருத்தைச் சேர்க்கவும்">
                                            Please add at least one audit remark
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-1 mt-2">
                                    <h5 class="lang mb-3" data-english="Attachment" data-tamil="இணைப்பு">
                                        Attachment
                                    </h5>

                                    @if (!$isFinalMode)
                                        <div class="file-input-container mb-3">
                                            <input type="file" class="form-control" id="file" name="file"
                                                accept="application/pdf">
                                        </div>

                                        <div id="view_file-list-container" class="mt-2">
                                            @if ($isEditMode && $hriaData->file_id && isset($hriaData->fileDetails))
                                                <div class="card overflow-hidden mb-3 bg-light card-fixed-width file-clickable"
                                                    id="viewfile-card-{{ $hriaData->file_id }}"
                                                    onclick="openFileInNewTab('{{ addslashes($hriaData->fileDetails->filepath) }}', '{{ addslashes($hriaData->fileDetails->filename) }}')"
                                                    style="cursor: pointer; transition: all 0.3s ease;">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <div class="p-3 flex-grow-1">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-file-pdf text-danger me-3 fs-1"></i>
                                                                <div>
                                                                    <p class="text-dark mb-1 fw-bold">
                                                                        {{ $hriaData->fileDetails->filename }}</p>
                                                                    <small class="text-muted">
                                                                        ({{ number_format($hriaData->fileDetails->filesize / 1024, 2) }}
                                                                        KB)
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="p-3"
                                                            onclick="event.stopPropagation(); remove_file();"
                                                            style="cursor: pointer;" title="Remove File">
                                                            <i class="ti ti-trash text-danger fs-5"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        @if ($hriaData->file_id && isset($hriaData->fileDetails))
                                            <div class="card overflow-hidden mb-3 bg-light card-fixed-width file-clickable"
                                                id="viewfile-card-{{ $hriaData->file_id }}"
                                                onclick="openFileInNewTab('{{ addslashes($hriaData->fileDetails->filepath) }}', '{{ addslashes($hriaData->fileDetails->filename) }}')"
                                                style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="d-flex flex-row align-items-center">
                                                    <div class="p-3 flex-grow-1">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-pdf text-danger me-3 fs-1"></i>
                                                            <div>
                                                                <p class="text-dark mb-1 fw-bold">
                                                                    {{ $hriaData->fileDetails->filename }}</p>
                                                                <small class="text-muted">
                                                                    ({{ number_format($hriaData->fileDetails->filesize / 1024, 2) }}
                                                                    KB)
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <input type="hidden" id="uploadid" name="uploadid"
                                        value="{{ $isEditMode && $hriaData->file_id ? $hriaData->file_id : '' }}">

                                    <div class="text-danger small" id="file-error"></div>
                                    <small class="text-muted lang"
                                        data-english="Allowed file types: PDF, max size: 2MB"
                                        data-tamil="அனுமதிக்கப்பட்ட கோப்பு வகைகள்: PDF, அதிகபட்ச அளவு: 2MB">
                                        Allowed file types: PDF, max size: 2MB
                                    </small>
                                </div>

                                <div id="auditcertificateDiv" class="hideotherdiv">
                                    <h4 class="section-title required mb-3" style="border: none;"
                                        data-english="Audit Certificate" data-tamil="தணிக்கை சான்றிதழ்">
                                        Audit Certificate
                                    </h4>

                                    <div class="row mb-3 align-items-start">
                                        <div class="col-md-2 text-dark">
                                            <b data-english="Type of Certificate" data-tamil="சான்றிதழ் வகை">
                                                Type of Certificate
                                            </b>
                                        </div>

                                        @php
                                            $selectedType = $hriaData->cer_type_code ?? '01';
                                        @endphp

                                        <div class="col-md-9 mb-2">
                                            @foreach (['01' => 'UnQualified', '02' => 'Qualified', '03' => 'Adverse', '04' => 'Disclaimer'] as $code => $label)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input cer-radio" type="radio"
                                                        name="cer_typecode" value="{{ $code }}"
                                                        id="radio{{ $code }}"
                                                        {{ $selectedType == $code ? 'checked' : '' }}
                                                        {{ $isFinalMode ? 'disabled' : '' }}>
                                                    <label class="form-check-label lang text-dark"
                                                        for="radio{{ $code }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div id="auditcertificateContentdiv"
                                                class="p-3 border rounded bg-light shadow-sm"
                                                style="min-height: 120px; max-height: 300px; overflow-y: auto;">
                                                <p id="cer_preload_conent" class="mb-2"></p>
                                                <p class="certificate_remarks fw-bold mb-1"></p>
                                                <span class="certificate_remarks_span fst-italic text-muted"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-center mt-4 no-print">
                                @if (!$isEditMode && !$isFinalMode)
                                    <button class="btn btn-primary me-2" type="submit" id="save-button">
                                        <i class="fas fa-save me-2"></i>
                                        <span class="lang" data-english="Save Draft" data-tamil="சேமிக்கவும்">Save
                                            Draft</span>
                                    </button>
                                @endif

                                @if ($isEditMode)
                                    <button class="btn btn-primary me-2" type="submit" id="update-button">
                                        <i class="fas fa-save me-2"></i>
                                        <span class="lang" data-english="Update"
                                            data-tamil="புதுப்பிக்கவும்">Update</span>
                                    </button>

                                    <button class="btn btn-success me-2" type="button" id="finalize-button">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span class="lang" data-english="Finalize"
                                            data-tamil="இறுதிப்படுத்து">Finalize</span>
                                    </button>
                                @endif

                                <button class="btn btn-secondary me-2" type="button" id="back-button">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    <span class="lang" data-english="Back" data-tamil="பின்செல்">Back</span>
                                </button>

                                @if ($isFinalMode)
                                    <button class="btn btn-primary btn-sm lang generateReportBtn" type="button"
                                        data-id="{{ $instid }}" data-formcode="{{ $formcode }}"  data-catcode="{{ $catcode }}"
                                        data-tempplanid ="{{ $tempplanid }}">
                                        <i class="fas fa-file-alt me-1"></i>
                                        <span class="lang" data-english="Generate Report"
                                            data-tamil="அறிக்கையை உருவாக்கு">Generate Report</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">

                <div class="col-md-4 mb-3" id="deptdiv">


                    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel"
                        aria-hidden="true" style="margin-top:60px;">
                        <div class="modal-dialog modal-xl" style="max-width:40%;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center" id="pdfModalLabel"> Report Preview</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="pdf-preview" style="width:100%; height:100%; border:1px solid #ccc;">
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button id="downloadReportBtn" data-catcode ="{{ $catcode }}" class="btn btn-primary">
                                        Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        @endif
    @endforeach
</div>

<script>
    function getlanguagelc() {
        return window.localStorage.getItem('lang') || 'en';
    }

    function initializeFileValidation() {
        $('#hriaOkpForm').rules('add', {
            rules: {
                file: {
                    required: false,
                    validFileType: true,
                    fileSizeLimit: true,
                }
            },
            messages: {
                file: {
                    validFileType: function() {
                        const language = getlanguagelc();
                        return language === 'ta' ? 'அனுமதிக்கப்பட்ட கோப்பு வகை PDF மட்டுமே' :
                            'Allowed file type is PDF only';
                    },
                    fileSizeLimit: function() {
                        const language = getlanguagelc();
                        return language === 'ta' ? 'கோப்பு அளவு 2MB ஐ தாண்டக்கூடாது' :
                            'File size should not exceed 2MB';
                    }
                }
            }
        });
    }

    function normalizeFilePath(dbFilePath) {
        if (!dbFilePath) return '';


        let normalizedPath = dbFilePath.replace(/\\/g, '/');

        if (normalizedPath.startsWith('uploads/')) {
            normalizedPath = '/' + normalizedPath;
        } else if (normalizedPath.includes('templateaudit')) {
            const pattern = /(uploads\/.*)/i;
            const match = normalizedPath.match(pattern);

            if (match) {
                normalizedPath = '/' + match[1];
            } else {
                const parts = normalizedPath.split('/');
                const uploadsIndex = parts.findIndex(part => part === 'uploads');
                if (uploadsIndex !== -1) {
                    normalizedPath = '/' + parts.slice(uploadsIndex).join('/');
                } else {
                    normalizedPath = '/uploads/' + normalizedPath;
                }
            }
        }

        normalizedPath = normalizedPath.replace(/[^\x20-\x7E]/g, '');

        normalizedPath = normalizedPath.replace(/\/+/g, '/');

        return normalizedPath;
    }

    function openFileInNewTab(filePath, fileName) {
        if (!filePath || filePath === '#') {
            const language = getlanguagelc();
            const message = language === 'ta' ?
                'கோப்பு இன்னும் பதிவேற்றம் செய்யப்படவில்லை' :
                'File not uploaded yet';
            showAlertModal('Info', 'தகவல்', message, message, 'en');
            return;
        }

        if (filePath.startsWith('blob:')) {
            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${fileName}</title>
                <style>
                    body { margin: 0; padding: 20px; background: #f5f5f5; }
                    .pdf-container { max-width: 100%; margin: 0 auto; }
                    embed, iframe { width: 100%; height: 95vh; border: 1px solid #ddd; border-radius: 5px; }
                    .header { text-align: center; margin-bottom: 20px; color: #333; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>${fileName}</h2>
                    <p>PDF Document</p>
                </div>
                <div class="pdf-container">
                    <embed src="${filePath}" type="application/pdf">
                </div>
            </body>
            </html>
        `);
        } else {
            let relativePath = normalizeFilePath(filePath);

            const baseUrl = window.location.origin;
            const fullUrl = baseUrl + relativePath;

            window.open(fullUrl, '_blank');
        }
    }

    function getFileUrl(file) {
        return URL.createObjectURL(file);
    }

    function displaySelectedFile(file) {
        const fileListContainer = $('#view_file-list-container');

        $('#file').hide();

        fileListContainer.empty();

        const fileObj = {
            id: 'temp-' + Date.now(),
            name: file.name,
            path: getFileUrl(file),
            size: (file.size / 1024).toFixed(2) + ' KB',
            fileuploadid: ''
        };

        view_files(fileObj);

        const language = getlanguagelc();
        $('#file-error').text(
            language === 'ta' ?
            'கோப்பு தேர்ந்தெடுக்கப்பட்டது. படிவத்தை சமர்ப்பிக்கும் போது பதிவேற்றம் செய்யப்படும்.' :
            'File selected. It will be uploaded when you submit the form.'
        ).addClass('text-success');
    }

    function view_files(file) {
        const fileListContainer = $('#view_file-list-container');

        fileListContainer.empty();

        if (file.fileuploadid) {
            $('#uploadid').val(file.fileuploadid);
        }

        const safeFilePath = file.path ?
            (file.path.startsWith('blob:') ? file.path : normalizeFilePath(file.path)) :
            '#';
        const safeFileName = file.name || 'Unknown File';

        const fileCard = `
        <div class="card overflow-hidden mb-3 bg-light card-fixed-width file-clickable"
             id="viewfile-card-${file.id}"
             onclick="openFileInNewTab('${safeFilePath}', '${safeFileName}')"
             style="cursor: pointer; transition: all 0.3s ease;">
            <div class="d-flex flex-row align-items-center">
                <div class="p-3 flex-grow-1">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger me-3 fs-1"></i>
                        <div>
                            <p class="text-dark mb-1 fw-bold">${safeFileName}</p>
                            <small class="text-muted">${file.size || '0 KB'}</small>
                        </div>
                    </div>
                </div>
                <div class="p-3" onclick="event.stopPropagation(); remove_file();"
                     style="cursor: pointer;" title="Remove File">
                    <i class="ti ti-trash text-danger fs-5"></i>
                </div>
            </div>
        </div>
    `;

        fileListContainer.append(fileCard).show();
    }

    function remove_file() {
        const fileListContainer = $('#view_file-list-container');
        const uploadId = $('#uploadid').val();
        const lang = document.documentElement.lang || 'en';
        showModalGlobal(
            'confirmation_alert',
            'Remove File',
            'கோப்பை நீக்கு',
            'Are you sure you want to remove this file?',
            'இந்த கோப்பை நீக்க விரும்புகிறீர்களா?',
            lang,
            function() {
                fileListContainer.hide().empty();
                $('#file').val('').show();
                $('#uploadid').val('');
                $('#file-error').text('').removeClass('text-success text-danger');

                const language = getlanguagelc();
                $('#file-error').text(
                    language === 'ta' ?
                    'கோப்பு வெற்றிகரமாக நீக்கப்பட்டது' :
                    'File removed successfully'
                ).addClass('text-success');

                setTimeout(() => {
                    $('#file-error').text('').removeClass('text-success');
                }, 3000);
            }
        );
    }

    function initializeExistingFile() {
        const uploadId = $('#uploadid').val();
        const fileListContainer = $('#view_file-list-container');

        if (uploadId &&
            {{ $isEditMode && $hriaData->file_id && isset($hriaData->fileDetails) && $hriaData->fileDetails ? 'true' : 'false' }}
        ) {
            $('#file').hide();
            fileListContainer.show();

            const fileCards = $('[onclick*="openFileInNewTab"]');
            fileCards.each(function() {
                const onclickAttr = $(this).attr('onclick');
                if (onclickAttr && onclickAttr.includes('openFileInNewTab')) {
                    const match = onclickAttr.match(/openFileInNewTab\('([^']+)',\s*'([^']*)'\)/);
                    if (match && match[1]) {
                        const originalPath = match[1];
                        const fileName = match[2] || 'Unknown File';
                        const normalizedPath = normalizeFilePath(originalPath);

                        // Update the onclick attribute with normalized path
                        if (normalizedPath !== originalPath) {
                            const newOnclick = `openFileInNewTab('${normalizedPath}', '${fileName}')`;
                            $(this).attr('onclick', newOnclick);

                        }
                    }
                }
            });
        } else {
            $('#file').show();
            fileListContainer.hide();
        }
    }

    if (!$.validator.methods.fileSizeLimit) {
        $.validator.addMethod("fileSizeLimit", function(value, element) {
            if (element.files.length > 0) {
                return element.files[0].size <= 2 * 1024 * 1024;
            }
            return true;
        }, function() {
            const language = getlanguagelc();
            return language === 'ta' ? 'கோப்பு அளவு 2MB ஐ தாண்டக்கூடாது' : 'File size should not exceed 2MB';
        });
    }

    if (!$.validator.methods.validFileType) {
        $.validator.addMethod("validFileType", function(value, element) {
            if (value) {
                let allowedTypes = ["pdf"];
                let fileExtension = value.split(".").pop().toLowerCase();
                return allowedTypes.includes(fileExtension);
            }
            return true;
        }, function() {
            const language = getlanguagelc();
            return language === 'ta' ? 'அனுமதிக்கப்பட்ட கோப்பு வகை PDF மட்டுமே' :
                'Allowed file type is PDF only';
        });
    }

    $('#file').on('change', function() {
        const file = this.files[0];
        const fileError = $('#file-error');

        fileError.text('').removeClass('text-success text-danger');

        if (file) {
            if (!file.type.includes('pdf') && !file.name.toLowerCase().endsWith('.pdf')) {
                const language = getlanguagelc();
                fileError.text(language === 'ta' ?
                    'அனுமதிக்கப்பட்ட கோப்பு வகை PDF மட்டுமே' :
                    'Allowed file type is PDF only'
                ).addClass('text-danger');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                const language = getlanguagelc();
                fileError.text(language === 'ta' ?
                    'கோப்பு அளவு 2MB ஐ தாண்டக்கூடாது' :
                    'File size should not exceed 2MB'
                ).addClass('text-danger');
                this.value = '';
                return;
            }

            const language = getlanguagelc();
            fileError.text(language === 'ta' ?
                'கோப்பு சரியாக தேர்ந்தெடுக்கப்பட்டது' :
                'File selected successfully'
            ).addClass('text-success');

            displaySelectedFile(file);

            $(this).valid();
        }
    });

    function addFileHoverEffects() {
        // Add CSS for hover effects
        if (!$('#file-hover-styles').length) {
            const style = document.createElement('style');
            style.id = 'file-hover-styles';
            style.textContent = `
            .file-clickable:hover {
                background-color: #f8f9fa !important;
                border-color: #007bff !important;
                transition: all 0.3s ease;
            }
            .file-clickable h3:hover {
                color: #007bff !important;
            }
            .card-fixed-width {
                max-width: 100%;
                transition: transform 0.2s ease;
            }
            .card-fixed-width:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
        `;
            document.head.appendChild(style);
        }
    }


  $(document).on("click", "#downloadReportBtn", function() {
        let instid = $(this).data("id");
        let formcode = $(this).data("formcode");
        let lang = getLanguage('Y');
        let tempplanid = $(this).data("tempplanid");
        let catcode = $(this).data("catcode");

        if (!instid || !formcode) {
            alert("Report data is missing!");
            return;
        }

        $.ajax({
            url: '/download-template-report',
            method: 'GET',
            data: { instid, formcode, lang, tempplanid,catcode },
            success: function () {
                window.open(
                    `/download-template-report?instid=${instid}&formcode=${formcode}&lang=${lang}&tempplanid=${tempplanid}`,
                    '_blank'
                );
            },
            error: function (xhr) {
                try {
                    let msg = xhr.responseJSON?.error || xhr.responseText;
                    if (msg.startsWith("error:")) msg = msg.replace("error:", "");
                    console.log(msg || "Failed to generate report.");
                } catch (e) {
                    console.log("An unexpected error occurred.");
                }
            }
        });
    });




    $("#translate").change(function() {
        let lang = getLanguage('Y');
        // console.log("Language changed:", lang);

    });


    $(document).ready(function() {
        let savedLang = 'ta';
        $('#translate').val(savedLang);
        // console.log("Initial language:", savedLang);
    });


    $(document).on("click", ".generateReportBtn", function() {
        let instid = $(this).data("id");
        let formcode = $(this).data("formcode");
        let tempplanid = $(this).data("tempplanid");
        let catcode = $(this).data("catcode");

        let lang = 'ta';
        // alert(lang);
        $.ajax({
            url: "/generate-report",
            type: "POST",
            data: {
                instid: instid,
                formcode: formcode,
                catcode: catcode,
                tempplanid: tempplanid,
                lang: lang,
                _token: $("meta[name='csrf-token']").attr("content")
            },
            success: function(response) {
                if (response.html) {
                    var iframe = document.createElement('iframe');
                    iframe.style.width = "100%";
                    iframe.style.height = "600px";
                    iframe.style.border = "1px solid #ccc";
                    iframe.srcdoc = response.html; // HTML content from backend

                    var container = document.getElementById('pdf-preview');
                    container.innerHTML = '';
                    container.appendChild(iframe);

                    $("#downloadReportBtn")
                        .data("id", response.encrypted.instid)
                        .data("formcode", response.encrypted.formcode)
                        .data("tempplanid", response.encrypted.tempplanid);

                    var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
                    pdfModal.show();
                } else {
                    alert("No report HTML returned!");
                }
            },
            error: function() {
                alert("Error generating report!");
            }
        });
    });



       function setupIFSCAutoSuggest() {
        const bankSelect = document.getElementById('bank_name');
        const ifscInput = document.getElementById('ifsc_code');
        const banks = @json($banksGrouped);
        const hiddenBank = document.getElementById('bankname');

        if (bankSelect && hiddenBank) {
            const opt = bankSelect.options[bankSelect.selectedIndex];
            hiddenBank.value = opt ? opt.dataset.bankname || '' : '';
        }

        document.getElementById('bank_name').addEventListener('change', function () {
            const bankName = this.options[this.selectedIndex].dataset.bankname || '';
            document.getElementById('bankname').value = bankName;
        });

        if (!bankSelect || !ifscInput || {{ $isFinalMode ? 'true' : 'false' }}) return;

        const prefixInput = document.createElement('input');
        prefixInput.type = 'hidden';
        prefixInput.id = 'ifsc_prefix';
        ifscInput.parentNode.insertBefore(prefixInput, ifscInput);

        function getBankPrefix(bankName) {
            if (!bankName) return '';


            if (Array.isArray(banks)) {
                const normalizedSearch = bankName.toUpperCase().trim();

                const foundBank = banks.find(bank =>
                    bank.bank_name && bank.bank_name.toUpperCase() === normalizedSearch
                );

                if (foundBank && foundBank.ifsc_code) {
                    return foundBank.ifsc_code.substring(0, 4);
                }

                for (const bank of banks) {
                    if (bank.bank_name) {
                        const bankNameUpper = bank.bank_name.toUpperCase();
                        if (bankNameUpper.includes(normalizedSearch) ||
                            normalizedSearch.includes(bankNameUpper)) {
                            return bank.ifsc_code.substring(0, 4);
                        }
                    }
                }

                return '';
            }

            if (banks[bankName]) {
                const firstIFSC = banks[bankName][0];
                return firstIFSC.substring(0, 4);
            }

            for (const [key, ifscCodes] of Object.entries(banks)) {
                if (key.toUpperCase() === bankName.toUpperCase()) {
                    const firstIFSC = ifscCodes[0];
                    return firstIFSC.substring(0, 4);
                }
            }

            const normalizedBankName = bankName.toUpperCase().trim();
            for (const [key, ifscCodes] of Object.entries(banks)) {
                if (key.toUpperCase().includes(normalizedBankName) ||
                    normalizedBankName.includes(key.toUpperCase())) {
                    const firstIFSC = ifscCodes[0];
                    return firstIFSC.substring(0, 4);
                }
            }

            return '';
        }

        function isValidIFSCForBank(bankName, ifscCode) {
            if (!bankName || !ifscCode || ifscCode.length !== 11) return false;

            if (Array.isArray(banks)) {
                const normalizedSearch = bankName.toUpperCase().trim();
                const foundBank = banks.find(bank =>
                    bank.bank_name && bank.bank_name.toUpperCase() === normalizedSearch
                );

                if (foundBank) {
                    if (foundBank.ifsc_code === ifscCode) {
                        return true;
                    }

                    if (Array.isArray(foundBank.ifsc_code)) {
                        return foundBank.ifsc_code.includes(ifscCode);
                    }
                }

                return false;
            }

            if (banks[bankName]) {
                return banks[bankName].includes(ifscCode);
            }

            for (const [key, ifscCodes] of Object.entries(banks)) {
                if (key.toUpperCase() === bankName.toUpperCase()) {
                    return ifscCodes.includes(ifscCode);
                }
            }

            return false;
        }

        function setPrefixReadOnly(prefix) {
            ifscInput.dataset.prefix = prefix;
            prefixInput.value = prefix;

            const currentValue = ifscInput.value;
            if (!currentValue || !currentValue.startsWith(prefix)) {
                ifscInput.value = prefix;
            }

            ifscInput.placeholder = `Enter next 7 digits after ${prefix}`;
        }

        bankSelect.addEventListener('change', function() {
            const bank = this.options[this.selectedIndex].dataset.bankname;;
            const prefix = getBankPrefix(bank);


            if (prefix) {
                setPrefixReadOnly(prefix);

                ifscInput.focus();
                setTimeout(() => {
                    ifscInput.setSelectionRange(prefix.length, prefix.length);
                }, 0);
            } else {
                ifscInput.dataset.prefix = '';
                prefixInput.value = '';
                ifscInput.value = '';
                ifscInput.placeholder = 'Enter 11-character IFSC code';
            }
        });

        ifscInput.addEventListener('input', function () {
            let raw = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            const prefix = this.dataset.prefix || prefixInput.value || '';
            let letters = '';
            let digits = '';

            if (prefix && raw.startsWith(prefix)) {
                letters = prefix;
                digits = raw.slice(prefix.length).replace(/\D/g, '').slice(0, 7);
            } else if (prefix && raw.length < prefix.length) {
                letters = prefix;
                digits = raw.slice(prefix.length).replace(/\D/g, '').slice(0, 7);
            } else if (!prefix) {
                letters = raw.replace(/[^A-Z]/g, '').slice(0, 4);
                digits = raw.slice(letters.length).replace(/\D/g, '').slice(0, 7);
            }

            const newValue = letters + digits;
            this.value = newValue;

            if (prefix && !newValue.startsWith(prefix)) {
                this.value = prefix + digits;
            }

            // if (letters.length === 4 && digits.length === 7) {
            //     const bankName = bankSelect.value;
            //     const isValidForBank = isValidIFSCForBank(bankName, this.value);

            //     if (isValidForBank) {
            //         this.classList.add('is-valid');
            //         this.classList.remove('is-invalid');
            //     } else {
            //         this.classList.add('is-valid');
            //         this.classList.remove('is-invalid');
            //     }
            // } else if (this.value.length > 0) {
            //     this.classList.add('is-invalid');
            //     this.classList.remove('is-valid');
            // } else {
            //     this.classList.remove('is-valid', 'is-invalid');
            // }

            if (prefix && this.selectionStart < prefix.length) {
                setTimeout(() => {
                    this.setSelectionRange(prefix.length, prefix.length);
                }, 0);
            }
        });

        ifscInput.addEventListener('keydown', function (e) {
            const prefix = this.dataset.prefix || prefixInput.value;

            if (prefix) {
                const cursorPos = this.selectionStart;
                const selectionEnd = this.selectionEnd;

                // Check if selection includes any part of the prefix
                const isSelectionInPrefix = cursorPos < prefix.length || selectionEnd < prefix.length;

                if (isSelectionInPrefix) {
                    // Allow navigation keys
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
                        e.key === 'Home' || e.key === 'End' ||
                        e.key === 'Tab' || e.key === 'Enter') {
                        return;
                    }

                    // Allow selection with Shift/Ctrl/Cmd
                    if (e.key === 'Shift' || e.metaKey || e.ctrlKey) {
                        return;
                    }

                    // Prevent any editing within the prefix area
                    if (e.key === 'Backspace' || e.key === 'Delete') {
                        e.preventDefault();

                        // If backspace at start of editable area, just move cursor to start of editable area
                        if (cursorPos === prefix.length && e.key === 'Backspace') {
                            setTimeout(() => {
                                this.setSelectionRange(prefix.length, prefix.length);
                            }, 0);
                        }
                        return;
                    }

                    e.preventDefault();

                    if (cursorPos < prefix.length) {
                        setTimeout(() => {
                            this.setSelectionRange(prefix.length, prefix.length);
                        }, 0);
                    }
                }
            }
        });

        ifscInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const cleaned = pastedText.toUpperCase().replace(/[^A-Z0-9]/g, '');

            const prefix = this.dataset.prefix || prefixInput.value;
            const cursorPos = this.selectionStart;

            if (prefix && cursorPos <= prefix.length) {
                const currentValue = this.value;
                const digitsPart = currentValue.slice(prefix.length);

                const newDigits = (digitsPart.slice(0, cursorPos - prefix.length) +
                                cleaned.replace(/\D/g, '') +
                                digitsPart.slice(cursorPos - prefix.length))
                                .slice(0, 7);

                this.value = prefix + newDigits;
                this.dispatchEvent(new Event('input'));

                const newCursorPos = Math.min(prefix.length + (cursorPos - prefix.length) + cleaned.length, this.value.length);
                setTimeout(() => {
                    this.setSelectionRange(newCursorPos, newCursorPos);
                }, 0);
            } else {
                const startPos = this.selectionStart;
                const endPos = this.selectionEnd;
                const currentValue = this.value;

                const newValue = currentValue.substring(0, startPos) + cleaned + currentValue.substring(endPos);
                this.value = newValue;

                this.dispatchEvent(new Event('input'));

                setTimeout(() => {
                    this.setSelectionRange(startPos + cleaned.length, startPos + cleaned.length);
                }, 0);
            }
        });

        ifscInput.addEventListener('focus', function () {
            const prefix = this.dataset.prefix || prefixInput.value;

            if (prefix) {
                setTimeout(() => {
                    if (this.selectionStart < prefix.length) {
                        this.setSelectionRange(prefix.length, prefix.length);
                    }
                }, 0);
            }
        });

        ifscInput.addEventListener('blur', function () {
            if (this.value && (this.value.length !== 11 || !this.classList.contains('is-valid'))) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        });

        function initializeIFSCField() {
            const bank = bankSelect.options[bankSelect.selectedIndex].dataset.bankname;
            const currentIFSC = ifscInput.value;

            if (bank) {
                const prefix = getBankPrefix(bank);

                if (prefix) {
                    setPrefixReadOnly(prefix);

                    if (currentIFSC && currentIFSC.startsWith(prefix)) {
                        ifscInput.value = currentIFSC;
                    } else if (!currentIFSC) {
                        ifscInput.value = prefix;
                    }

                    ifscInput.dispatchEvent(new Event('input'));
                }
            }
        }

        setTimeout(initializeIFSCField, 100);
    }

    $(document).ready(function() {
        const certificateRemarks = JSON.parse('{!! addslashes(json_encode($Master_Auditcertificate)) !!}');

        function toggleRemarks(typecode = null, remarks = '') {
            const selected = typecode || $('input[name="cer_typecode"]:checked').val();
            if (!selected) return;

            const selectedElement = $('input[name="cer_typecode"][value="' + selected + '"]');
            const labelHtml = selectedElement.next('label').text();

            $('#auditcertificateContentdiv').show();

            const matched = certificateRemarks.find(cert => cert.cer_type_code === selected);
            if (matched) {
                let parsedContent = '';
                try {
                    const json = JSON.parse(matched.cer_content);
                    parsedContent = json.content;
                } catch (e) {
                    parsedContent = matched.cer_content;
                }
                $('#cer_preload_conent').html(parsedContent);
            } else {
                $('#cer_preload_conent').html('');
            }
        }

        toggleRemarks();

        $('input[name="cer_typecode"]').change(function() {
            toggleRemarks();
        });

        let deptcode = "{{ $deptcode ?? '' }}";
        let lang = (deptcode === "01") ? "ta" : "en";

        function applyLanguage(lang) {
            ["#username", "#designation"].forEach(function(selector) {
                let elem = $(selector);
                if (elem.length) {
                    elem.text(lang === "ta" ? elem.data("tamil") : elem.data("english"));
                }
            });

            $("[data-english]").each(function() {
                let text = lang === "ta" ? $(this).data("tamil") : $(this).data("english");
                $(this).text(text);
            });
        }

        applyLanguage(lang);

        setupIFSCAutoSuggest();
    });

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    function showAlertModal(titleEn, titleTa, msgEn, msgTa, lang, callback) {
        const modalEl = document.getElementById('confirmation_alert');
        const modal = new bootstrap.Modal(modalEl);

        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');

        const titleEl = modalEl.querySelector('#confirmation_alertmodal');
        const bodyEl = modalEl.querySelector('#alert_body');

        if (titleEl) titleEl.innerText = lang === 'ta' ? titleTa : titleEn;
        if (bodyEl) bodyEl.innerText = lang === 'ta' ? msgTa : msgEn;

        const okBtn = modalEl.querySelector('#ok_button');
        const cancelBtn = modalEl.querySelector('#cancel_button');

        if (cancelBtn) cancelBtn.style.display = 'none';
        if (okBtn) okBtn.style.display = 'inline-block';

        const closeBtn = modalEl.querySelector('.btn-close');
        if (closeBtn) closeBtn.style.display = 'none';

        okBtn.replaceWith(okBtn.cloneNode(true));
        const newOkBtn = modalEl.querySelector('#ok_button');

        if (newOkBtn) {
            newOkBtn.addEventListener('click', function() {
                modal.hide();
                if (callback) {
                    callback();
                }
            });
        }

        const handleHidden = function() {
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            modalEl.removeEventListener('hidden.bs.modal', handleHidden);

            if (closeBtn) closeBtn.style.display = 'block';
        };

        modalEl.addEventListener('hidden.bs.modal', handleHidden);

        modal.show();

        setTimeout(() => {
            if (newOkBtn) newOkBtn.focus();
        }, 100);
    }

    function allowSingleZero(field) {
        field.addEventListener('input', function() {
            const value = this.value;

            if (value === '' || value === '0' || !value.startsWith('0')) {
                return;
            }


            if (value.length > 1 && value.startsWith('0')) {
                this.value = value.replace(/^0+/, '');

                if (this.value === '' && value.includes('0')) {
                    this.value = '0';
                }
            }
        });

        field.addEventListener('blur', function() {
            if (this.value === '0') {
                this.setCustomValidity('');
            }
        });
    }
    document.querySelectorAll('input[type="number"]').forEach(field => {
        allowSingleZero(field);
    });

    document.querySelectorAll('input[data-temptypeid="1"]').forEach(field => {
        if (field.type !== 'number') {
            allowSingleZero(field);
        }
    });




    function showModalGlobal(modalId, titleEn, titleTa, msgEn, msgTa, lang, callback) {
        const modalEl = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalEl);

        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');

        const titleEl = modalEl.querySelector('#confirmation_alertmodal');
        const bodyEl = modalEl.querySelector('#alert_body');

        if (titleEl) titleEl.innerText = lang === 'ta' ? titleTa : titleEn;
        if (bodyEl) bodyEl.innerText = lang === 'ta' ? msgTa : msgEn;

        const okBtn = modalEl.querySelector('#ok_button');
        const cancelBtn = modalEl.querySelector('#cancel_button');
        if (okBtn) okBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';

        const closeBtn = modalEl.querySelector('.btn-close');
        if (closeBtn) closeBtn.remove();

        if (okBtn) okBtn.replaceWith(okBtn.cloneNode(true));
        if (cancelBtn) cancelBtn.replaceWith(cancelBtn.cloneNode(true));

        const newOkBtn = modalEl.querySelector('#ok_button');
        const newCancelBtn = modalEl.querySelector('#cancel_button');

        let callbackExecuted = false;

        const executeCallback = function() {
            if (!callbackExecuted && callback) {
                callbackExecuted = true;
                callback();
            }
        };

        const handleClose = function() {
            modal.hide();
        };

        if (newOkBtn) {
            newOkBtn.addEventListener('click', function() {
                handleClose();
                executeCallback();
            });
        }

        if (newCancelBtn) {
            newCancelBtn.addEventListener('click', function() {
                handleClose();
            });
        }

        const handleHidden = function() {
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            modalEl.removeEventListener('hidden.bs.modal', handleHidden);
            callbackExecuted = false;
        };

        modalEl.addEventListener('hidden.bs.modal', handleHidden);

        modal.show();

        setTimeout(() => {
            if (newOkBtn) newOkBtn.focus();
        }, 100);
    }

    function sanitizeBranch(value) {
        return value.replace(/[<>;"'`|\\]/g, '').trim();
    }


    function containsSqlInjection(text) {
        const sqlKeywords = [
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION', 'OR', 'AND',
            'WHERE', 'FROM', 'TABLE', 'DATABASE', 'ALTER', 'CREATE', 'EXEC',
            'TRUNCATE', 'JOIN', 'SCRIPT', 'JAVASCRIPT', 'ONLOAD', 'ONERROR',
            'ALERT', 'DOCUMENT', 'WINDOW', 'EVAL', 'FUNCTION', 'SCRIPT',
            '1=1', '1=0', 'OR 1=1', 'AND 1=1', ';', '--', '/*', '*/', 'XP_',
            'CHAR(', 'BENCHMARK(', 'SLEEP(', 'WAITFOR', 'DELAY'
        ];

        const upperValue = text.toUpperCase();
        return sqlKeywords.some(keyword => upperValue.includes(keyword.toUpperCase()));
    }

    function restrictToDigits(el, minLen, maxLen) {
        el.addEventListener('input', function() {
            let val = this.value.replace(/\D+/g, '').slice(0, maxLen);

            val = val.replace(/(\d)\1{6,}/g, '$1'.repeat(6));

            this.value = val;
        });

        el.addEventListener('blur', function() {
            if (this.value.length < minLen) {
                this.setCustomValidity('Invalid length');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    function restrictNonZeroStart(el, minLen = 1, maxLen = 10) {
        el.addEventListener('input', function() {
            let val = this.value.replace(/\D+/g, '').slice(0, maxLen);

            if (val.startsWith('0')) {
                val = val.replace(/^0+/, '');
            }

            this.value = val;
        });

        el.addEventListener('blur', function() {
            if (this.value.length < minLen) {
                this.setCustomValidity('Invalid length');
            } else {
                this.setCustomValidity('');
            }
        });
    }


    function restrictPhoneNumber(el) {
        el.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            val = val.slice(0, 10);
            val = val.replace(/(\d)\1{3,}/g, '$1$1$1');

            if (val && /^[0-5]/.test(val)) {
                val = val.slice(1);
            }

            this.value = val;

            if (!/^[6-9]/.test(val)) {
                this.setCustomValidity('First digit must be 6-9.');
            } else if (val.length !== 10) {
                this.setCustomValidity('Phone number must be exactly 10 digits.');
            } else if (/(\d)\1{3,}/.test(val)) {
                this.setCustomValidity('No more than 3 consecutive same digits allowed.');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    function restrictText(el, allowedRegex, maxLen) {
        const allowed = new RegExp(allowedRegex, 'u');
        el.addEventListener('input', function() {
            let filtered = this.value;

            if (filtered.length > maxLen) {
                filtered = filtered.slice(0, maxLen);
            }

            this.value = filtered;
        });
    }

    function restrictNumberInput(el) {
        const temptypeid = parseInt(el.dataset.temptypeid);

        if (temptypeid !== 1) return;

        const maxLen = parseInt(el.dataset.maxlen || '8', 10);
        const decimals = parseInt(el.dataset.decimals || '2', 10);

        el.addEventListener('input', function() {
            let v = this.value.replace(/[^0-9.-]/g, '');

            // Handle multiple minus signs - keep only the first one at beginning
            if ((v.match(/-/g) || []).length > 1) {
                v = '-' + v.replace(/-/g, '');
            }

            // Ensure minus is only at the beginning
            if (v.includes('-') && v.indexOf('-') > 0) {
                v = v.replace(/-/g, '');
                v = '-' + v;
            }

            // Handle cases where user just types minus
            if (v === '-' || v === '-.') {
                // Allow minus sign for negative numbers
                return;
            }

            const parts = v.split('.');
            if (parts.length > 2) {
                v = parts[0] + '.' + parts.slice(1).join('');
            }

            // Handle negative numbers in parts
            let wholePart = parts[0] || '';
            let decimalPart = parts[1] || '';

            // For negative numbers, adjust the whole part
            if (wholePart.startsWith('-')) {
                const negativeWhole = wholePart.substring(1);
                wholePart = '-' + negativeWhole.slice(0, maxLen);
            } else {
                wholePart = wholePart.slice(0, maxLen);
            }

            if (decimalPart) {
                decimalPart = decimalPart.slice(0, decimals);
            }

            v = wholePart + (decimalPart ? '.' + decimalPart : '');

            if (v !== this.value) {
                this.value = v;
            }

            this.classList.remove('is-invalid');
            this.setCustomValidity('');

            setTimeout(() => {
                calculate();
            }, 100);
        });

        el.addEventListener('blur', function() {
            validateNumericField(this);
            formatDecimalValue(this);
        });
    }

    function validateNumericField(field) {
        const value = field.value.trim();
        field.classList.remove('is-invalid', 'is-valid');

        if (field.hasAttribute('required') && value === '') {
            field.classList.add('is-invalid');
            field.setCustomValidity('This field is required');
            return false;
        }

        if (value !== '' && value !== '-') { // Allow minus sign
            // Test for valid number format (including negative)
            const numRegex = /^-?\d*\.?\d*$/;
            if (!numRegex.test(value)) {
                field.classList.add('is-invalid');
                field.setCustomValidity('Please enter a valid number');
                return false;
            }

            // If it's a complete number (not just minus sign), validate further
            if (value !== '-' && value !== '-.') {
                const numValue = parseFloat(value);

                if (isNaN(numValue)) {
                    field.classList.add('is-invalid');
                    field.setCustomValidity('Please enter a valid number');
                    return false;
                }

                // Check decimal places
                if (value.includes('.')) {
                    const decimalPlaces = value.split('.')[1].length;
                    if (decimalPlaces > 2) {
                        field.classList.add('is-invalid');
                        field.setCustomValidity('Maximum 2 decimal places allowed');
                        return false;
                    }
                }
            }

            field.classList.add('is-valid');
            field.setCustomValidity('');
            return true;
        }

        field.setCustomValidity('');
        return true;
    }

    function validateAllNumericFields() {
        const numericFields = document.querySelectorAll('input[data-temptypeid="1"]');
        let allValid = true;
        let firstInvalidField = null;

        numericFields.forEach(field => {
            const value = field.value.trim();

            if (value === '' && !field.hasAttribute('required')) {
                return;
            }

            // Allow negative values - remove the negative value check
            if (value !== '' && value !== '-' && value !== '-.') {
                const numValue = parseFloat(value);
                if (isNaN(numValue)) {
                    field.classList.add('is-invalid');
                    field.setCustomValidity('Please enter a valid number');
                    allValid = false;

                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('is-invalid');
                    field.setCustomValidity('');
                }
            }
        });

        if (firstInvalidField) {
            firstInvalidField.focus();

            showAlertModal(
                'Validation Error',
                'சரிபார்ப்பு பிழை',
                'Please ensure all amount fields contain valid numbers.',
                'அனைத்து தொகை புலங்களும் சரியான எண்களைக் கொண்டிருப்பதை உறுதிப்படுத்தவும்.',
                'en'
            );
        }

        return allValid;
    }

    function preventSqlInjection(field) {
        if (!field || !field.value) return;

        const dangerousPatterns = [
            /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
            /javascript:/gi,
            /on\w+=/gi,
            /;/g,
            /--/g,
            /\/\*/g,
            /\*\//g
        ];

        let cleanedValue = field.value;
        const originalValue = cleanedValue;

        // Simple string matching for SQL keywords (case insensitive)
        const sqlKeywords = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION', 'WHERE', 'FROM'];

        sqlKeywords.forEach(keyword => {
            const regex = new RegExp(keyword, 'gi');
            cleanedValue = cleanedValue.replace(regex, '');
        });

        // Remove other dangerous patterns
        dangerousPatterns.forEach(pattern => {
            cleanedValue = cleanedValue.replace(pattern, '');
        });

        if (cleanedValue !== originalValue) {
            field.value = cleanedValue;
            console.log('⚠️ Suspicious content removed from input');
        }
    }


    function handleSubTableInput(input, subTableFlag) {
        const temptypeid = parseInt(input.getAttribute('data-temptypeid'));

        switch (temptypeid) {
            case 1: // Numeric
                formatNumericInput(input);
                calculateSubTableTotal(subTableFlag);
                break;
            case 2: // Character
                formatTextInput(input);
                break;
            case 3: // Date
                validateDateInput(input);
                break;
            case 4: // Radio - handled separately
                break;
            default:
                break;
        }

        validateSubTableInput(input);
    }

    function calculateSubTableTotal(subTableFlag) {
        let total = 0;

        document.querySelectorAll(`.${subTableFlag}-field`).forEach(input => {
            const temptypeid = parseInt(input.getAttribute('data-temptypeid'));

            // Only include numeric fields in total calculation
            if (temptypeid === 1) {
                const value = parseFloat(input.value) || 0;
                total += value;
            }
        });

        const totalInput = document.getElementById(`${subTableFlag}Total`);
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
    }

    function formatNumericInput(input) {
        let value = input.value;

        // Allow negative signs by including them in the regex
        value = value.replace(/[^\d.\-]/g, '');

        // Handle multiple minus signs - keep only the first one at the beginning
        const minusCount = (value.match(/-/g) || []).length;
        if (minusCount > 1) {
            value = '-' + value.replace(/-/g, '');
        }

        // Ensure minus is only at the beginning
        if (value.includes('-') && value.indexOf('-') > 0) {
            value = value.replace(/-/g, '');
            value = '-' + value;
        }

        const decimalCount = (value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            const parts = value.split('.');
            value = parts[0] + '.' + parts.slice(1).join('').replace(/\./g, '');
        }

        if (value.includes('.')) {
            const parts = value.split('.');
            if (parts[1].length > 2) {
                parts[1] = parts[1].substring(0, 2);
                value = parts[0] + '.' + parts[1];
            }
        }

        input.value = value;
    }

    function formatTextInput(input) {
        let value = input.value.replace(/[<>;"'`|\\]/g, '');

        const maxlength = input.getAttribute('maxlength');
        if (maxlength && value.length > maxlength) {
            value = value.substring(0, maxlength);
        }

        input.value = value;
    }

    function validateDateInput(input) {
        const value = input.value;
        if (value) {
            const date = new Date(value);
            if (isNaN(date.getTime())) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }
    }

    function validateSubTableInput(input) {
        const temptypeid = parseInt(input.getAttribute('data-temptypeid'));
        const value = input.value.trim();

        input.classList.remove('is-invalid', 'is-valid');

        if (input.hasAttribute('required') && value === '') {
            input.classList.add('is-invalid');
            return false;
        }

        switch (temptypeid) {
            case 1: // Numeric
                if (value !== '' && (isNaN(parseFloat(value)))) {
                    input.classList.add('is-invalid');
                    return false;
                }
                break;
            case 2: // Character
                if (value.length > 80) {
                    input.classList.add('is-invalid');
                    return false;
                }
                break;
            case 3: // Date
                if (value !== '') {
                    const date = new Date(value);
                    if (isNaN(date.getTime())) {
                        input.classList.add('is-invalid');
                        return false;
                    }
                }
                break;
            case 4: // Radio
                break;
        }

        if (value !== '') {
            input.classList.add('is-valid');
        }

        return true;
    }

    function validateSubTables() {
        let isValid = true;
        const errorFields = [];

        // Validate all sub-table fields
        document.querySelectorAll('.sub-table-input').forEach(input => {
            if (input.hasAttribute('required')) {
                const value = input.value.trim();
                // REMOVE THE parseFloat(value) < 0 CHECK
                if (value === '' || isNaN(parseFloat(value))) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    errorFields.push(input);
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            }
        });

        return {
            isValid: isValid,
            errorFields: errorFields
        };
    }

    // Function to auto-open accordions with errors
    function openAccordionsWithErrors() {
        const errorInputs = document.querySelectorAll('.sub-table-input.is-invalid');
        const accordionsToOpen = new Set();

        errorInputs.forEach(input => {
            const accordionItem = input.closest('.accordion-item');
            if (accordionItem) {
                const button = accordionItem.querySelector('.accordion-button');
                const collapse = accordionItem.querySelector('.accordion-collapse');

                if (button && collapse && button.classList.contains('collapsed')) {
                    accordionsToOpen.add(accordionItem);
                }
            }
        });

        // Open all accordions with errors
        accordionsToOpen.forEach(accordionItem => {
            const button = accordionItem.querySelector('.accordion-button');
            const collapse = accordionItem.querySelector('.accordion-collapse');

            if (button && collapse) {
                button.classList.remove('collapsed');
                button.setAttribute('aria-expanded', 'true');
                collapse.classList.add('show');
            }
        });

        // Scroll to first error if any
        if (errorInputs.length > 0) {
            const firstError = errorInputs[0];
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            firstError.focus();
        }
    }


    const maxRows = {{ $maxRows }};
    let currentRows = {{ $currentRows }};

    function addRemarkRow() {
        if (currentRows >= maxRows) {
            showAlertModal(
                'Maximum Limit Reached',
                'அதிகபட்ச வரம்பு',
                `You can add maximum ${maxRows} remarks only.`,
                `நீங்கள் அதிகபட்சம் ${maxRows} கருத்துகளை மட்டுமே சேர்க்க முடியும்.`,
                'en'
            );
            return;
        }

        const container = document.getElementById('dynamicRowsContainer');

        // Remove add button from previous last row
        const lastRow = container.querySelector('.remark-row:last-child');
        if (lastRow) {
            const lastAddBtn = lastRow.querySelector('.add-row');
            if (lastAddBtn) {
                lastAddBtn.remove();
            }
        }

        const newRow = document.createElement('div');
        newRow.className = 'remark-row mb-3 p-3 border rounded';
        newRow.setAttribute('data-index', currentRows);

        newRow.innerHTML = `
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <label class="form-label fw-bold">${currentRows + 1}.</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select remark-type" name="remarks[${currentRows}][type]" required>
                                    <option value="" disabled selected>Type</option>
                                    <option value="serious">Serious</option>
                                    <option value="non-serious">Non-Serious</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="position-relative">
                                    <textarea class="form-control remark-text" name="remarks[${currentRows}][text]"
                                        rows="2" placeholder="Enter remark details..."  required></textarea>
                                    <small class="text-muted d-none"><span class="char-count">0</span>/500</small>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                ${currentRows + 1 < maxRows ? `
                                    <button type="button" class="btn btn-sm btn-outline-success add-row" title="Add new remark">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                ` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row mt-1" title="Remove this remark">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;

        container.appendChild(newRow);
        initializeRemarkRow(newRow);
        currentRows++;
    }

    function removeRemarkRow(rowElement) {
        const hasContent = checkRowHasContent(rowElement);

        if (hasContent) {
            const lang = document.documentElement.lang || 'en';
            showModalGlobal(
                'confirmation_alert',
                'Remove Remark',
                'கருத்தை நீக்கு',
                'This remark contains content. Are you sure you want to remove it?',
                'இந்த கருத்து உள்ளடக்கத்தைக் கொண்டுள்ளது. அதை நீக்க விரும்புகிறீர்களா?',
                lang,
                function() {
                    performRowRemoval(rowElement);
                }
            );
        } else {
            performRowRemoval(rowElement);
        }
    }

    function performRowRemoval(rowElement) {
        rowElement.remove();
        currentRows--;
        renumberPoints();
    }

    function checkRowHasContent(rowElement) {
        const typeSelect = rowElement.querySelector('.remark-type');
        const textTextarea = rowElement.querySelector('.remark-text');

        return (typeSelect && typeSelect.value !== '') ||
            (textTextarea && textTextarea.value.trim() !== '');
    }

    function initializeRemarkRow(rowElement) {
        const textarea = rowElement.querySelector('.remark-text');
        // const charCount = rowElement.querySelector('.char-count');
        const addBtn = rowElement.querySelector('.add-row');
        const removeBtn = rowElement.querySelector('.remove-row');

            if (textarea) {
        textarea.addEventListener('input', function() {
            // Just remove invalid styling on input
            this.classList.remove('is-invalid');
        });
    }


        if (addBtn) {
            addBtn.addEventListener('click', function() {
                addRemarkRow();
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                removeRemarkRow(rowElement);
            });
        }
    }

    function renumberPoints() {
        const rows = document.querySelectorAll('.remark-row');

        rows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            const label = row.querySelector('.form-label');
            const selects = row.querySelectorAll('select[name^="remarks"]');
            const textareas = row.querySelectorAll('textarea[name^="remarks"]');

            if (label) label.textContent = `${index + 1}.`;

            // Update select names
            selects.forEach(select => {
                select.name = select.name.replace(/remarks\[\d+\]/, `remarks[${index}]`);
            });

            // Update textarea names
            textareas.forEach(textarea => {
                textarea.name = textarea.name.replace(/remarks\[\d+\]/,
                    `remarks[${index}]`);
            });

            // Update add button visibility
            const addBtn = row.querySelector('.add-row');
            const removeBtn = row.querySelector('.remove-row');

            // Show add button only on last row if under max limit
            if (index === rows.length - 1 && index < maxRows - 1) {
                if (!addBtn) {
                    const buttonContainer = row.querySelector('.col-md-1.text-center');
                    if (buttonContainer) {
                        const newAddBtn = document.createElement('button');
                        newAddBtn.type = 'button';
                        newAddBtn.className = 'btn btn-sm btn-outline-success add-row';
                        newAddBtn.title = 'Add new remark';
                        newAddBtn.innerHTML = '<i class="fas fa-plus"></i>';
                        newAddBtn.addEventListener('click', function() {
                            addRemarkRow();
                        });
                        buttonContainer.prepend(newAddBtn);
                    }
                }
            } else if (addBtn) {
                addBtn.remove();
            }

            // Show remove button only for rows beyond first
            if (index === 0 && removeBtn) {
                removeBtn.remove();
            } else if (index > 0 && !removeBtn) {
                const buttonContainer = row.querySelector('.col-md-1.text-center');
                if (buttonContainer) {
                    const newRemoveBtn = document.createElement('button');
                    newRemoveBtn.type = 'button';
                    newRemoveBtn.className = 'btn btn-sm btn-outline-danger remove-row mt-1';
                    newRemoveBtn.title = 'Remove this remark';
                    newRemoveBtn.innerHTML = '<i class="fas fa-times"></i>';
                    newRemoveBtn.addEventListener('click', function() {
                        removeRemarkRow(row);
                    });
                    buttonContainer.appendChild(newRemoveBtn);
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.remark-row').forEach(row => {
            initializeRemarkRow(row);
        });
    });

    function validateRemarks() {
    const rows = document.querySelectorAll('.remark-row');

    // Check if there's at least one remark with content
    let hasAtLeastOneRemark = false;

    rows.forEach(row => {
        const typeSelect = row.querySelector('.remark-type');
        const textTextarea = row.querySelector('.remark-text');

        // Remove any existing validation styling
        typeSelect?.classList.remove('is-invalid');
        textTextarea?.classList.remove('is-invalid');

        const hasType = typeSelect && typeSelect.value;
        const hasText = textTextarea && textTextarea.value.trim();

        // If both type and text are present, it's a valid remark
        if (hasType && hasText) {
            hasAtLeastOneRemark = true;
        }

        // If either field has content but not both, show error
        if ((hasType && !hasText) || (!hasType && hasText)) {
            if (!hasType) typeSelect?.classList.add('is-invalid');
            if (!hasText) textTextarea?.classList.add('is-invalid');
        }
    });

    return hasAtLeastOneRemark;
    }

    function formatRemarksBeforeSubmit() {
        const remarksData = [];
        document.querySelectorAll('.remark-row').forEach(row => {
            const typeSelect = row.querySelector('.remark-type');
            const textTextarea = row.querySelector('.remark-text');

            if (typeSelect && textTextarea && typeSelect.value && textTextarea.value.trim()) {
                remarksData.push({
                    type: typeSelect.value,
                    text: textTextarea.value.trim()
                });
            }
        });

        document.querySelectorAll('input[name^="remarks"]').forEach(input => input.remove());

        remarksData.forEach((remark, index) => {
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = `remarks[${index}][type]`;
            typeInput.value = remark.type;
            document.getElementById('hriaOkpForm2').appendChild(typeInput);

            const textInput = document.createElement('input');
            textInput.type = 'hidden';
            textInput.name = `remarks[${index}][text]`;
            textInput.value = remark.text;
            document.getElementById('hriaOkpForm2').appendChild(textInput);
        });
    }

    function formatDecimalInput(field) {
        const temptypeid = field.dataset.temptypeid;
        // const subtemptypeid = field.dataset.subtemptypeid;

        if (parseInt(temptypeid) === 1) {
            let value = field.value;

            // Allow negative sign and digits/decimal point
            value = value.replace(/[^0-9.-]/g, '');

            // Handle multiple minus signs - keep only the first one
            const minusCount = (value.match(/-/g) || []).length;
            if (minusCount > 1) {
                value = '-' + value.replace(/-/g, '');
            }

            // Handle multiple decimal points
            const decimalCount = (value.match(/\./g) || []).length;
            if (decimalCount > 1) {
                const parts = value.split('.');
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Handle leading decimal point
            if (value.startsWith('.')) {
                value = '0' + value;
            }
            if (value.startsWith('-.')) {
                value = '-0.' + value.substring(2);
            }

            // Limit to 2 decimal places without truncating
            if (value.includes('.') && !value.includes('-.')) {
                const parts = value.split('.');
                if (parts[1].length > 2) {
                    const roundedValue = Math.round(parseFloat(value) * 100) / 100;
                    value = roundedValue.toFixed(2);
                }
            } else if (value.includes('-.')) {
                const parts = value.split('.');
                if (parts[1].length > 2) {
                    const roundedValue = Math.round(parseFloat(value) * 100) / 100;
                    value = roundedValue.toFixed(2);
                }
            }

            if (value !== field.value) {
                field.value = value;
            }

            this.classList.remove('is-invalid');
            this.setCustomValidity('');

            setTimeout(() => {
                calculate();
            }, 100);
        }
    }

    function formatDecimalValue(field) {
        const temptypeid = parseInt(field.dataset.temptypeid);
        const fieldType = field.type;

        if (temptypeid === 1 && (fieldType === 'text' || fieldType === 'number')) {
            let value = field.value.trim();

            if (value && value !== '' && value !== '-' && value !== '-.') {
                // Remove any existing formatting but keep negative sign
                value = value.replace(/[^0-9.-]/g, '');

                // Handle cases where user just typed a decimal point
                if (value === '.' || value === '-.') {
                    field.value = value === '-.' ? '-0.00' : '0.00';
                    return;
                }

                // Split into whole and decimal parts, handling negative numbers
                const isNegative = value.startsWith('-');
                const absoluteValue = isNegative ? value.substring(1) : value;
                const parts = absoluteValue.split('.');
                let wholePart = parts[0] || '0';
                let decimalPart = parts[1] || '00';

                // Ensure whole part is not empty
                if (wholePart === '') {
                    wholePart = '0';
                }

                // Format decimal part to exactly 2 digits
                if (decimalPart.length === 0) {
                    decimalPart = '00';
                } else if (decimalPart.length === 1) {
                    decimalPart = decimalPart + '0';
                } else if (decimalPart.length > 2) {
                    decimalPart = decimalPart.substring(0, 2);
                }

                // Combine and set the value (preserve negative sign)
                const formattedValue = (isNegative ? '-' : '') + wholePart + '.' + decimalPart;

                // Only update if the value actually changed
                if (formattedValue !== field.value) {
                    field.value = formattedValue;
                }
            } else if (value === '' && field.hasAttribute('required')) {
                // If field is required and empty, don't format
                return;
            } else if (value === '-' || value === '-.') {
                // Keep minus sign for user to complete the number
                return;
            }
        }
    }

    function calculate() {
        try {
            const allInputs = document.querySelectorAll('input[data-type]');
            let totalIncome = 0;
            let totalExpense = 0;
            let openingVal = 0;
            let iGeneral = 0;
            let eGeneral = 0;

            allInputs.forEach(el => {
                const type = el.dataset.type;
                const temptypeid = parseInt(el.dataset.temptypeid);

                if (temptypeid === 1) {
                    const value = parseFloat(el.value) || 0;

                    if (!el.readOnly) {
                        switch (type) {
                            case 'O':
                                openingVal = value;
                                break;
                            case 'I':
                                iGeneral = value;
                                break;
                            case 'E':
                                eGeneral = value;
                                break;
                            default:
                                if (type && !type.includes('T')) {
                                    if (el.classList.contains('receipts-input')) {
                                        totalIncome += value;
                                    } else if (el.classList.contains('expenditures-input')) {
                                        totalExpense += value;
                                    }
                                }
                                break;
                        }
                    }
                }
            });

            const totalReceipts = totalIncome + iGeneral;
            const totalExpenditures = totalExpense + eGeneral;

            const closingBalance = openingVal + totalReceipts - totalExpenditures;

            const overallReceiptsTotal = openingVal + totalReceipts;
            const overallExpendituresTotal = totalExpenditures + closingBalance;

    const setValue = (selector, value) => {
        const element = document.querySelector(selector);
        if (element && parseInt(element.dataset.temptypeid) === 1) {
            element.value = value.toFixed(2); // Just format, don't force positive
        }
    };
            const getPreviousAllValues = (selector, stopType) => {
                const el = document.querySelector(selector);
                if (el) {
                    const row = el.closest('tr');
                    let sum = 0;
                    let prevRow = row?.previousElementSibling;

                    while (prevRow) {
                        const prevInput = prevRow.querySelector(
                            'input[data-type][data-temptypeid="1"].receipts-input');
                        if (prevInput) {
                            const prevType = prevInput.dataset.type;

                            if (prevType === stopType) break;

                            if (
                                prevType !== 'I' &&
                                !prevType.includes('T')
                            ) {
                                sum += parseFloat(prevInput.value) || 0;
                            }
                        }
                        prevRow = prevRow.previousElementSibling;
                    }

                    return sum;
                }
                return 0;
            };

            const getPreviousEGeneralAllValues = (selector) => {
                const el = document.querySelector(selector);
                if (!el) return 0;

                let sum = 0;
                let row = el.closest('tr').previousElementSibling;

                while (row) {
                    const input = row.querySelector('input[data-temptypeid="1"].expenditures-input');
                    if (input) {
                        sum += parseFloat(input.value) || 0;
                    }
                    row = row.previousElementSibling;
                }

                return sum;
            };

            // Update separately
            setValue('[data-type="I"]', getPreviousAllValues('[data-type="I"]', 'E'));
            setValue('[data-type="E"]', getPreviousEGeneralAllValues('[data-type="E"]'));

            setValue('[data-type="R"]', totalReceipts);
            setValue('[data-type="E"]', totalExpenditures);
            setValue('[data-type="V"]', overallReceiptsTotal);
            setValue('[data-type="X"]', overallExpendituresTotal);
            setValue('[data-type="C"]', closingBalance);

        } catch (error) {
            console.error('Error in calculate function:', error);
            return {
                isValid: false,
                error: error.message
            };
        }
    }


    function addSubTableValidation() {
        const isFinalMode = {{ $isFinalMode ? 'true' : 'false' }};
        if (isFinalMode) return;

        // Add validation for all amount fields in sub-tables
        $('input[class*="-field"]').each(function() {
            const fieldName = $(this).attr('name');

            if (fieldName && $(this).prop('required')) {
                $('#hriaOkpForm2').rules('add', {
                    rules: {
                        [fieldName]: {
                            required: true,
                            positiveNumber: true
                        }
                    },
                    messages: {
                        [fieldName]: {
                            required: "This field is required",
                            positiveNumber: "Please enter a valid amount"
                        }
                    }
                });
            }
        });
    }

    function initializeFormValidation() {
        const isFinalMode = {{ $isFinalMode ? 'true' : 'false' }};
        if (isFinalMode) return;

        if ($.validator) {
            const form = $('#hriaOkpForm2');
            if (form.data('validator')) {
                form.validate().destroy();
            }
        }


        $.validator.addMethod("amountField", function(value, element) {
            if ($(element).closest('.radio-group').length) {
                const radioName = $(element).attr('name');
                return $(`input[name="${radioName}"]:checked`).length > 0;
            }

            const temptypeid = $(element).data('temptypeid')
            const subtemptypeid = $(element).data('subtemptypeid');
            if (temptypeid === 1) {
                if (value === '') return true;
                return /^[0-9]*\.?[0-9]{0,2}$/.test(value);
            }
            return true;
        }, "Please enter a valid amount");

       // $.validator.addMethod("ifscCode", function(value, element) {
        //    return this.optional(element) || /^[A-Z]{4}0[A-Z0-9]{6}$/.test(value);
       // }, "Please enter a valid IFSC code (e.g., SBIN0000123)");

        $.validator.addMethod("phoneIndia", function(value, element) {
            return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
        }, "Please enter a valid 10-digit Indian phone number starting with 6-9");

        $.validator.addMethod("accountNumber", function(value, element) {
            return this.optional(element) || /^[0-9]{9,16}$/.test(value);
        }, "Please enter a valid 9-16 digit account number");

        $.validator.addMethod("alphaSpace", function(value, element) {
            return this.optional(element) || /^[A-Za-z\s.'\-]{1,80}$/.test(value);
        }, "Please enter only letters, spaces, and basic punctuation");

        // Add custom validation method
        $.validator.addMethod("branchName", function(value, element) {
            return this.optional(element) || /^[\u0B80-\u0BFFA-Za-z\s.'\-]+$/.test(value);
        }, "Branch name can only contain letters (including Tamil), spaces, and basic punctuation");

        $.validator.addMethod("select2Required", function(value, element) {
            if ($(element).hasClass('select2-hidden-accessible')) {
                const selectedValues = $(element).val();
                return selectedValues !== null && selectedValues.length > 0 && selectedValues[0] !== '';
            }
            return value !== null && value !== '' && value !== undefined;
        }, "This field is required");

        $.validator.addMethod("amountField", function(value, element) {
            if ($(element).closest('.radio-group').length) {
                const radioName = $(element).attr('name');
                return $(`input[name="${radioName}"]:checked`).length > 0;
            }

            const temptypeid = $(element).data('temptypeid');
            if (temptypeid === 1) {
                if (value === '') return true;
                return /^[0-9]*\.?[0-9]{0,2}$/.test(value);
            }
            return true;
        }, "Please enter a valid amount");

        $('#hriaOkpForm2').validate({
            rules: {
                'yearselected[]': {
                    select2Required: true
                },
                priest_name: {
                    required: true,
                    maxlength: 80
                },
                phone_number: {
                    required: true,
                    phoneIndia: true
                },
                bank_name: {
                    required: true
                },
                branch_name: {
                    required: true,
                    branchName: true,
                    maxlength: 60
                },
                account_number: {
                    required: true,
                    accountNumber: true
                },
               // ifsc_code: {
                //    required: true,
               //     ifscCode: true
               // },
                remarks: {
                    // maxlength: 1000,
                    required: true
                },
                file: {
                    required: false,
                    validFileType: true,
                    fileSizeLimit: true,
                }
            },
            messages: {
                'yearselected[]': {
                    select2Required: "Please select at least one audit year"
                },
                priest_name: {
                    required: "Please enter priest/poojari name",
                    alphaSpace: "Name can only contain letters, spaces, and basic punctuation"
                },
                phone_number: {
                    required: "Please enter phone number",
                    phoneIndia: "Please enter a valid 10-digit phone number starting with 6-9"
                },
                bank_name: {
                    required: "Please select a bank"
                },

                branch_name: {
                    required: "Please enter branch name",
                    branchName: "Branch name can only contain letters, spaces, and basic punctuation"
                },
                account_number: {
                    required: "Please enter account number",
                    accountNumber: "Account number must be 9-16 digits"
                },
              //  ifsc_code: {
               //     required: "Please select IFSC code",
               //     ifscCode: "Please enter a valid IFSC code (e.g., SBIN0000123)"
             //   },
                remarks: {
                    required: "Please enter audit remarks",
                    // maxlength: "Remarks cannot exceed 1000 characters"
                },
                file: {
                    validFileType: function() {
                        const language = getlanguagelc();
                        return language === 'ta' ? 'அனுமதிக்கப்பட்ட கோப்பு வகை PDF மட்டுமே' :
                            'Allowed file type is PDF only';
                    },
                    fileSizeLimit: function() {
                        const language = getlanguagelc();
                        return language === 'ta' ? 'கோப்பு அளவு 2MB ஐ தாண்டக்கூடாது' :
                            'File size should not exceed 2MB';
                    }
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');

                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2-container').addClass('is-invalid');
                }

                if ($(element).attr('type') === 'radio') {
                    $(element).closest('.radio-group').addClass('is-invalid');
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');

                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2-container').removeClass('is-invalid').addClass('is-valid');

                    $(element).next('.select2-container').next('.invalid-feedback').remove();
                }

                if ($(element).attr('type') === 'radio') {
                    $(element).closest('.radio-group').removeClass('is-invalid');
                }
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.attr('type') === 'radio') {
                    error.insertAfter(element.closest('.radio-group'));
                } else {
                    error.insertAfter(element);
                }
            },
            ignore: [],
            onfocusout: function(element) {
                this.element(element);
            },
            invalidHandler: function(event, validator) {
                const firstError = $('.is-invalid').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);

                    if (firstError.hasClass('select2-container')) {
                        firstError.find('.select2-selection').focus();
                    } else {
                        firstError.focus();
                    }
                }
            },
            onkeyup: function(element, event) {
                if (event.which === 13) {
                    return;
                }

                if ($(element).attr('type') !== 'password') {
                    this.element(element);
                }
            },

            onsubmit: true,
            submitHandler: function(form) {
                handleFormSubmission(form);
                return false;
            },
        });
        addSubTableValidation();

        $('.financial-table input[data-temptypeid]').each(function() {
            const field = $(this);
            const isRequired = field.prop('required');

            if (isRequired) {
                const fieldName = field.attr('name');

                $('#hriaOkpForm2').rules('add', {
                    rules: {
                        [fieldName]: {
                            amountField: true
                        }
                    },
                    messages: {
                        [fieldName]: {
                            amountField: "This field is required and must contain a valid value"
                        }
                    }
                });
            }
        });
    }

    let conditionalValidator;

    function setupConditionalRequiredValidation() {
        const financialTable = document.querySelector('.financial-table');
        if (!financialTable) return;

        const rowsWithInput = new Set();

        function shouldValidateRow(row) {
            const inputs = row.querySelectorAll('input[data-temptypeid]:not([readonly]):not([data-istotal="true"])');

            const hasOnlyEmptyCells = Array.from(row.querySelectorAll('td')).every(cell => {
                return cell.querySelector('span.text-muted') && !cell.querySelector('input');
            });

            if (hasOnlyEmptyCells) return false;

            const isHeaderRow = row.querySelector('td.bg-light.fw-bold') && inputs.length === 0;
            if (isHeaderRow) return false;

            return inputs.length > 0;
        }

        function checkRowHasInput(row) {
            if (!shouldValidateRow(row)) return false;

            const inputs = row.querySelectorAll('input[data-temptypeid]:not([readonly]):not([data-istotal="true"])');

            for (const input of inputs) {
                if (input.type === 'radio') {
                    const radioName = input.name;
                    const checkedRadio = row.querySelector(`input[name="${radioName}"]:checked`);
                    if (checkedRadio) return true;
                } else if (input.value && input.value.trim() !== '' && input.value !== '') {
                    return true;
                }
            }
            return false;
        }

        function getRowDescription(row) {
            const firstCell = row.querySelector('td:first-child');
            if (!firstCell) return 'Some field';

            let span = firstCell.querySelector('[data-english]');
            if (!span) span = firstCell.querySelector('span.lang');
            if (!span) {
                for (const node of firstCell.childNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE && node.hasAttribute && node.hasAttribute('data-english')) {
                        span = node;
                        break;
                    }
                }
            }
            if (!span) {
                const html = firstCell.innerHTML || '';
                if (html.includes('data-english=')) {
                    const tmp = document.createElement('div');
                    tmp.innerHTML = html;
                    span = tmp.querySelector('[data-english]');
                }
            }

            if (span) {
                const englishName = span.getAttribute('data-english');
                const tamilName = span.getAttribute('data-tamil');
                if (englishName && englishName.trim() !== '') {
                    const lang = document.documentElement.lang || 'en';
                    return lang === 'ta' ? (tamilName || englishName) : englishName;
                }
            }

            const textContent = firstCell.textContent.trim();
            // alert(textContent);
            if (textContent) return textContent;
            return 'Some field';
        }

        function shouldValidateCell(cell) {
            if (!cell) return false;

            if (cell.querySelector('span.text-muted') && !cell.querySelector('input')) {
                return false;
            }

            const input = cell.querySelector('input[data-temptypeid]:not([readonly]):not([data-istotal="true"])');
            if (!input) return false;

            if (input.disabled) return false;

            return true;
        }

        function validateRow(row) {
            // Skip rows that shouldn't be validated
            if (!shouldValidateRow(row)) {
                return {
                    isValid: true,
                    rowDescription: getRowDescription(row),
                    missingFields: [],
                    rowTotal: 0,
                    hasInput: false
                };
            }

            const hasInput = checkRowHasInput(row);
            const cells = row.querySelectorAll('td');
            let isValid = true;
            let rowTotal = 0;
            let missingFields = [];
            let rowDescription = getRowDescription(row);

            // Check if this is an Opening Balance row
            const isOpeningBalanceRow = row.querySelector('input[data-type="O"]');

            if (hasInput) {
                rowsWithInput.add(row);

                // Check each cell that should be validated
                cells.forEach((cell, cellIndex) => {
                    if (!shouldValidateCell(cell)) return;

                    const input = cell.querySelector(
                        'input[data-temptypeid]:not([readonly]):not([data-istotal="true"])');
                    if (!input) return;

                    const isRequired = !input.disabled && input.type !== 'radio';
                    const isRadioGroup = input.type === 'radio';

                    if (isRequired && (!input.value || input.value.trim() === '')) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        missingFields.push(`amount field`);
                    } else if (isRadioGroup) {
                        const radioName = input.name;
                        const checkedRadio = row.querySelector(`input[name="${radioName}"]:checked`);
                        if (!checkedRadio) {
                            const radioGroup = input.closest('.radio-group');
                            if (radioGroup) radioGroup.classList.add('is-invalid');
                            isValid = false;
                            missingFields.push('selection');
                        } else {
                            const radioGroup = input.closest('.radio-group');
                            if (radioGroup) radioGroup.classList.remove('is-invalid');
                        }
                    } else {
                        input.classList.remove('is-invalid');

                        // Calculate total for numeric fields (exclude Opening Balance fields)
                        if (input.dataset.temptypeid === '1' && input.value &&
                            input.dataset.type !== 'O' && !isOpeningBalanceRow) {
                            const value = parseFloat(input.value) || 0;
                            rowTotal += value;
                        }
                    }
                });

                // DON'T show zero total message for Opening Balance rows
                if (rowTotal <= 0 && !isOpeningBalanceRow) {
                    // Remove any existing error styling
                    row.classList.remove('row-zero-total');
                    const existingErrors = row.querySelectorAll('.row-total-error');
                    existingErrors.forEach(error => error.remove());

                    // Add informational message
                    const firstCell = row.querySelector('td:first-child');
                    if (firstCell && !firstCell.querySelector('.row-total-info')) {
                        const infoDiv = document.createElement('div');
                        infoDiv.className = 'row-total-info text-muted small mt-1';
                        infoDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>Zero entered</span>
                    </div>
                `;
                        firstCell.appendChild(infoDiv);
                    }
                } else {
                    // Remove informational messages when total becomes positive or for Opening Balance
                    row.classList.remove('row-zero-total');
                    const existingInfos = row.querySelectorAll('.row-total-info');
                    existingInfos.forEach(info => info.remove());
                }

            } else {
                // No input in this row, remove from tracking and clear validation
                rowsWithInput.delete(row);
                cells.forEach(cell => {
                    if (!shouldValidateCell(cell)) return;

                    const input = cell.querySelector('input[data-temptypeid]');
                    if (input) {
                        input.classList.remove('is-invalid');
                        if (input.type === 'radio') {
                            const radioGroup = input.closest('.radio-group');
                            if (radioGroup) radioGroup.classList.remove('is-invalid');
                        }
                    }
                });

                // Remove all messages
                row.classList.remove('row-zero-total');
                const existingErrors = row.querySelectorAll('.row-total-error');
                existingErrors.forEach(error => error.remove());
                const existingInfos = row.querySelectorAll('.row-total-info');
                existingInfos.forEach(info => info.remove());
            }

            return {
                isValid,
                rowDescription,
                missingFields,
                rowTotal,
                hasInput
            };
        }

        function validateAllRowsWithInput() {
            let allValid = true;
            const rows = financialTable.querySelectorAll('tbody tr');
            const invalidRows = [];

            rows.forEach(row => {
                const validationResult = validateRow(row);
                if (!validationResult.isValid) {
                    allValid = false;
                    invalidRows.push(validationResult);
                }
            });

            return {
                allValid,
                invalidRows
            };
        }

        const allInputs = financialTable.querySelectorAll(
            'input[data-temptypeid]:not([readonly]):not([data-istotal="true"])');
        allInputs.forEach(input => {
            if (!shouldValidateCell(input.closest('td'))) return;

            input.addEventListener('input', function() {
                const row = this.closest('tr');
                if (row) {
                    validateRow(row);
                    calculate();
                }
            });

            input.addEventListener('blur', function() {
                const row = this.closest('tr');
                if (row) {
                    validateRow(row);
                }
            });
        });

        const radioGroups = financialTable.querySelectorAll('.radio-group');
        radioGroups.forEach(group => {
            if (!shouldValidateCell(group.closest('td'))) return;

            const radios = group.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (row) {
                        validateRow(row);
                    }
                });
            });
        });

        return {
            validateAll: validateAllRowsWithInput,
            validateRow: validateRow
        };
    }

    function handleFormSubmission(form) {
        if ($(form).data('submitting')) return;

        if (!validateAllNumericFields()) {
            return;
        }

        if (!validateRemarks()) {
            showAlertModal(
                'Incomplete Remarks',
                'முழுமையற்ற கருத்துகள்',
                'Please check your audit remarks. Each point needs both a type (Serious/Non-Serious) and description.',
                'உங்கள் தணிக்கை கருத்துகளைச் சரிபார்க்கவும். ஒவ்வொரு புள்ளியும் ஒரு வகை (தீவிரம்/தீவிரம் அல்லாத) மற்றும் விளக்கத்தைக்க வேண்டும்.',
                'en'
            );
            return;
        }


        if (!validateFinancialData()) {
            showAlertModal(
                'Data Required',
                'நிதி தரவு தேவை',
                'Please fill at least one amount field in the financial table to generate totals.',
                'மொத்தங்களை உருவாக்க நிதி அட்டவணையில் குறைந்தபட்சம் ஒரு தொகை புலத்தை நிரப்பவும்.',
                'en'
            );
            return;
        }

        const subTableValidation = validateSubTables();
        if (!subTableValidation.isValid) {
            showAlertModal(
                'Validation Error',
                'சரிபார்ப்பு பிழை',
                'Please check all required fields in the accordion tables.',
                'அக்கார்டியன் அட்டவணைகளில் அனைத்து தேவையான புலங்களையும் சரிபார்க்கவும்.',
                'en'
            );
            openAccordionsWithErrors();
            return;
        }

        if (conditionalValidator) {
            const validationResult = conditionalValidator.validateAll();

            if (!validationResult.allValid) {
                const invalidRowCount = validationResult.invalidRows.length;

                let englishMessage = '';
                let tamilMessage = '';

                if (invalidRowCount === 1) {
                    const invalidRow = validationResult.invalidRows[0];
                    englishMessage = `Please check "${invalidRow.rowDescription}":\n\n`;
                    tamilMessage = `"${invalidRow.rowDescription}"-ஐ சரிபார்க்கவும்:\n\n`;

                    if (invalidRow.missingFields.length > 0 && invalidRow.rowTotal <= 0) {
                        englishMessage +=
                            '• Data not entered in some fields';
                        tamilMessage +=
                            '• சில தொகைகள் காணவில்லை\n• பூஜ்ஜியம் உள்ளிடப்பட்டது\n\n அனைத்து தொகை நெடுவரிசைகளையும் நிரப்பவும் அல்லது வரிசையை அழிக்கவும்';
                    } else if (invalidRow.missingFields.length > 0) {
                        englishMessage +=
                            '• Data not entered in some fields\n\n Please complete all amount columns in this row';
                        tamilMessage +=
                            '• சில தொகைகள் காணவில்லை\n\n இந்த வரிசையில் அனைத்து தொகை நெடுவரிசைகளையும் நிரப்பவும்';
                    } else if (invalidRow.rowTotal <= 0) {
                        englishMessage += '• Zero entered\n\n Please enter positive values to get a valid total';
                        tamilMessage +=
                            '• பூஜ்ஜியம் உள்ளிடப்பட்டது\n\n சரியான மொத்த தொகைக்கு நேர்மறை மதிப்புகளை உள்ளிடவும்';
                    }
                } else {
                    englishMessage = `Please complete these ${invalidRowCount} sections:\n\n`;
                    tamilMessage = `இந்த ${invalidRowCount} பிரிவுகளை நிரப்பவும்:\n\n`;

                    validationResult.invalidRows.forEach((row, index) => {
                        englishMessage += `${index + 1}. ${row.rowDescription} - `;
                        tamilMessage += `${index + 1}. ${row.rowDescription} - `;

                        if (row.missingFields.length > 0 && row.rowTotal <= 0) {
                            englishMessage += 'Incomplete amounts\n';
                            tamilMessage += 'முழுமையற்ற தொகைகள்\n';
                        } else if (row.missingFields.length > 0) {
                            englishMessage += 'Missing some amounts\n';
                            tamilMessage += 'சில தொகைகள் காணவில்லை\n';
                        } else if (row.rowTotal <= 0) {
                            englishMessage += 'Zero entered\n';
                            tamilMessage += 'பூஜ்ஜியம் உள்ளிடப்பட்டது\n';
                        }
                    });

                    englishMessage += '\n For each section: Fill both amount columns or clear incomplete rows';
                    tamilMessage +=
                        '\n ஒவ்வொரு பிரிவிற்கும்: இரு தொகை நெடுவரிசைகளையும் நிரப்பவும் அல்லது முழுமையற்ற வரிசைகளை அழிக்கவும்';
                }
                showAlertModal(
                    'Check list',
                    'நிதி விவரங்களை நிரப்புக',
                    englishMessage,
                    tamilMessage,
                    'en'
                );

                const firstInvalidRow = document.querySelector('.financial-table tr:has(.is-invalid)');
                if (firstInvalidRow) {
                    firstInvalidRow.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalidRow.classList.add('highlight-error');
                    setTimeout(() => firstInvalidRow.classList.remove('highlight-error'), 3000);
                }
                return;
            }
        }

        $(form).data('submitting', true);
        if (!$('#hriaOkpForm2').valid()) {
            hideLoading();
            $(form).data('submitting', false);

            const subTableValidation = validateSubTables();
            if (!subTableValidation.isValid) {
                openAccordionsWithErrors();
            }
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
            return;
        }

        showLoading();

        formatNumericValues();
        formatSubTableValues();
        formatRemarksBeforeSubmit();

        const formData = new FormData(form);
        collectSubTableData(formData);

        $.ajax({
            url: $(form).attr('action'),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                $(form).data('submitting', false);

                if (response.success) {
                    showAlertModal(
                        'Confirmation',
                        'உறுதிப்படுத்தல்',
                        response.message || 'Operation completed successfully',
                        response.message || 'செயல்பாடு வெற்றிகரமாக முடிந்தது',
                        'en',
                        function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        }
                    );
                } else {
                    showAlertModal(
                        'Error',
                        'பிழை',
                        response.message || 'An error occurred',
                        response.message || 'ஒரு பிழை ஏற்பட்டது',
                        'en'
                    );
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                $(form).data('submitting', false);

                let errorMessage = 'An unknown error occurred';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.error || errorMessage;
                } catch (e) {
                    console.error('Error parsing response:', e);
                }

                showAlertModal(
                    'Error',
                    'பிழை',
                    errorMessage,
                    errorMessage,
                    'en'
                );
            }
        });
    }


    function validateAtLeastOneFinancialField() {

        const financialInputs = document.querySelectorAll(
            '.financial-table input[type="number"]:not([readonly]):not(.total-field)'
        );

        let hasAtLeastOneFieldFilled = false;

        financialInputs.forEach((input, index) => {
            const value = input.value.trim();
            const numericValue = parseFloat(value);


            if (value !== '' && !isNaN(numericValue) && numericValue > 0) {
                hasAtLeastOneFieldFilled = true;
                console.log(`✓ Valid data found: ${input.name} = ${value}`);
            }
        });

        return hasAtLeastOneFieldFilled;
    }

    function validateFinancialData() {

        const inputs = document.querySelectorAll(
            '.financial-table input[type="number"]:not([readonly]):not(.total-field)');
        let hasValidData = false;

        for (let input of inputs) {
            const value = input.value.trim();
            if (value !== '' && value !== '0' && value !== '0.00') {
                const numValue = parseFloat(value);
                if (!isNaN(numValue) && numValue > 0) {
                    hasValidData = true;
                    console.log(`✓ Valid financial data found: ${input.name} = ${value}`);
                    break;
                }
            }
        }

        return hasValidData;
    }



    function formatSubTableValues() {
        document.querySelectorAll('.sub-table-input').forEach(input => {
            const temptypeid = parseInt(input.getAttribute('data-temptypeid'));
            let value = input.value.trim();

            if (value && value !== '') {
                switch (temptypeid) {
                    case 1: // Numeric
                        if (!value.includes('.')) {
                            input.value = value + '.00';
                        } else {
                            const parts = value.split('.');
                            if (parts[1].length === 0) {
                                input.value = parts[0] + '.00';
                            } else if (parts[1].length === 1) {
                                input.value = parts[0] + '.' + parts[1] + '0';
                            }
                        }
                        break;
                    case 2: // Character - trim and sanitize
                        input.value = value.trim();
                        break;
                    case 3: // Date - ensure proper format
                        if (value) {
                            const date = new Date(value);
                            if (!isNaN(date.getTime())) {
                                input.value = date.toISOString().split('T')[0];
                            }
                        }
                        break;
                        // case 4: Radio values are already formatted
                }
            }
        });
    }

    function initializeSubTableValidation() {
        document.querySelectorAll('.sub-table-input').forEach(input => {
            input.addEventListener('blur', function() {
                validateSubTableInput(this);

                const subTableFlag = this.getAttribute('data-subtable');
                const temptypeid = parseInt(this.getAttribute('data-temptypeid'));

                if (subTableFlag && temptypeid === 1) {
                    calculateSubTableTotal(subTableFlag);
                }
            });

            input.addEventListener('input', function() {
                this.classList.remove('is-invalid', 'is-valid');

                const subTableFlag = this.getAttribute('data-subtable');
                const temptypeid = parseInt(this.getAttribute('data-temptypeid'));

                if (subTableFlag && temptypeid === 1) {
                    calculateSubTableTotal(subTableFlag);
                }
            });
        });

        // Initialize radio group validation
        document.querySelectorAll('.radio-group input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const radioGroup = this.closest('.radio-group');
                if (radioGroup) {
                    radioGroup.classList.remove('is-invalid');
                }
            });
        });
    }

    function updateSubTableTotal(subTableFlag) {
        let total = 0;


        const inputs = document.querySelectorAll(`.${subTableFlag}-field[data-is-total="false"]`);


        inputs.forEach(input => {
            const temptypeid = parseInt(input.getAttribute('data-temptypeid'));
            const value = input.value.trim();

            if (temptypeid === 1 && value !== '') {
                const numValue = parseFloat(value) || 0;
                total += numValue;
            }
        });

        const totalInputs = document.querySelectorAll(`.${subTableFlag}-total[data-is-total="true"]`);

        totalInputs.forEach(totalInput => {
            totalInput.value = total.toFixed(2);
        });
    }

    function initializeSubTableTotals() {
        const subTables = ['closing_balance', 'jewel_valuation'];

        subTables.forEach(table => {

            updateSubTableTotal(table);

            if (!{{ $isFinalMode ? 'true' : 'false' }}) {
                const inputs = document.querySelectorAll(`.${table}-field[data-is-total="false"]`);
                inputs.forEach(input => {
                    input.removeEventListener('input', () => updateSubTableTotal(table));
                    input.removeEventListener('blur', () => updateSubTableTotal(table));

                    input.addEventListener('input', () => updateSubTableTotal(table));
                    input.addEventListener('blur', () => updateSubTableTotal(table));
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeSubTableTotals();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const subTables = ['closing_balance', 'jewel_valuation'];
        subTables.forEach(table => {
            updateSubTableTotal(table);
        });
    });

    function collectSubTableData(formData) {
        $('input[class*="-field"]').each(function() {
            const fieldName = $(this).attr('name');
            const fieldValue = $(this).val();

            if (fieldName && fieldValue !== '') {
                formData.append(fieldName, fieldValue);
            }
        });
    }



    function formatNumericValues() {
        $('.financial-table input[data-temptypeid="1"]').each(function() {
            const field = this;
            const type = field.type;

            // Apply to both text and number fields
            if (type === 'text' || type === 'number') {
                let value = $(field).val().trim();

                if (value && value !== '') {
                    // Remove any non-numeric characters except decimal point and minus
                    value = value.replace(/[^\d.\-]/g, '');

                    // Check if it's a negative number
                    const isNegative = value.startsWith('-');
                    const absoluteValue = isNegative ? value.substring(1) : value;

                    const parts = absoluteValue.split('.');
                    let wholePart = parts[0] || '0';
                    let decimalPart = parts[1] || '00';

                    // Ensure whole part is not empty
                    if (wholePart === '') {
                        wholePart = '0';
                    }

                    // Format decimal part
                    if (decimalPart.length === 0) {
                        decimalPart = '00';
                    } else if (decimalPart.length === 1) {
                        decimalPart = decimalPart + '0';
                    } else if (decimalPart.length > 2) {
                        decimalPart = decimalPart.substring(0, 2);
                    }

                    // Reconstruct the value with proper negative sign
                    const formattedValue = (isNegative ? '-' : '') + wholePart + '.' + decimalPart;

                    $(field).val(formattedValue);

                    // Debug log
                    if (isNegative) {
                        // console.log('Formatted negative value:', formattedValue, 'for field:', field.name);
                    }
                }
            }
        });

        const notes = document.getElementById('remarks');
        if (notes) {
            notes.value = notes.value.replace(/<[^>]*>/g, '')
                .replace(/https?:\/\/\S+/gi, '')
                .replace(/javascript:/gi, '');
        }
    }


    function preventRepeatedCharacters(field, maxRepeat = 3) {
        const value = field.value;

        const regex = new RegExp(`(.)\\1{${maxRepeat},}`, 'g');

        if (regex.test(value)) {
            let cleanedValue = value;
            let foundExcessiveRepeat = false;

            cleanedValue = cleanedValue.replace(regex, function(match) {
                foundExcessiveRepeat = true;
                return match.charAt(0).repeat(maxRepeat + 1);
            });

            if (foundExcessiveRepeat && cleanedValue !== value) {
                field.value = cleanedValue;

                const warningEl = field.nextElementSibling;
                if (warningEl && warningEl.classList.contains('invalid-feedback')) {
                    warningEl.textContent = `No more than ${maxRepeat} consecutive identical characters allowed`;
                    setTimeout(() => {
                        warningEl.textContent = '';
                    }, 3000);
                }

                return true;
            }
        }

        return false;
    }
    document.addEventListener('DOMContentLoaded', function() {
        showLoading();

        window.addEventListener('load', function() {
            hideLoading();
        });

        initializeFileValidation();
        initializeExistingFile();
        addFileHoverEffects();
        conditionalValidator = setupConditionalRequiredValidation();

        const isFinalMode = {{ $isFinalMode ? 'true' : 'false' }};
        const isEditMode = {{ $isEditMode ? 'true' : 'false' }};
        const NAME_ALLOWED_REGEX = "^[A-Za-z\\u0B80-\\u0BFF\\s.'-]*$";
        const MAX_NAME_LENGTH = 80;
        const MAX_BRANCH_LENGTH = 60;
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

        $(document).ready(function() {

            $('.select2-hidden-accessible').each(function() {
                $(this).removeAttr('aria-hidden');
                $(this).addClass('visually-hidden-select2');
            });
            $('#yearselected').select2({
                placeholder: "Select Year",
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                $(this).trigger('blur');
            });

            $('#yearselected').on('select2:close', function() {
                $(this).valid();
            });

            $('#yearselected').on('select2:select select2:unselect', function(e) {
                let val = $(this).val()
                if (val && val.length > 0) {
                    $('.select2-selection').css('color', '#000')
                } else {
                    $('.select2-selection').css('color', '#6c757d')
                }
            })
            initializeSubTableValidation();
            initializeSubTableTotals();

            // Initialize form validation
            initializeFormValidation();
        });

        // Input restrictions (only if not in final mode)
        if (!isFinalMode) {
            const phone = document.getElementById('phone_number');
            const acc = document.getElementById('account_number');
            const branch = document.getElementById('branch_name');
            const name = document.getElementById('priest_name');

            if (phone) restrictPhoneNumber(phone);
            if (acc) restrictToDigits(acc, 9, 16);

            if (branch) {
                branch.addEventListener('input', function() {
                    // Allow Tamil letters, English letters, spaces, and basic punctuation
                    this.value = this.value.replace(/[^\u0B80-\u0BFFA-Za-z\s.'\-]/g, '');

                    if (this.value.length > MAX_BRANCH_LENGTH) {
                        this.value = this.value.slice(0, MAX_BRANCH_LENGTH);
                    }

                    // Prevent all types of repetition
                    preventRepeatedCharacters(this, 3); // Max 3 repeated characters
                    preventRepeatedWords(this);
                    preventRepeatedPatterns(this);
                });

                branch.addEventListener('blur', function() {
                    this.value = this.value.trim();
                    preventSqlInjection(this);
                    preventRepeatedCharacters(this, 3);
                    preventRepeatedWords(this);
                    preventRepeatedPatterns(this);
                });
            }

            if (name) {
                name.addEventListener('input', function() {
                    this.value = this.value.replace(/[^\u0B80-\u0BFFA-Za-z\s.'\-]/g, '');

                    if (this.value.length > MAX_NAME_LENGTH) {
                        this.value = this.value.slice(0, MAX_NAME_LENGTH);
                    }

                    preventSqlInjection(this);
                    preventRepeatedCharacters(this, 3);
                    preventRepeatedWords(this);
                    preventRepeatedPatterns(this);
                });

                name.addEventListener('blur', function() {
                    this.value = this.value.trim();
                    preventSqlInjection(this);
                    preventRepeatedCharacters(this, 3);
                    preventRepeatedWords(this);
                    preventRepeatedPatterns(this);
                });

                name.addEventListener('keypress', function(e) {
                    const allowedChars = /[\u0B80-\u0BFFA-Za-z\s.'\-]/;
                    if (!allowedChars.test(e.key)) {
                        e.preventDefault();
                    }

                    const currentValue = this.value;
                    const newValue = currentValue + e.key;

                    const charRegex = /(.)\1{3,}/;
                    if (charRegex.test(newValue)) {
                        e.preventDefault();
                    }

                    if (hasRepeatedPatterns(newValue)) {
                        e.preventDefault();
                    }
                });
            }
        }

        function preventRepeatedCharacters(inputElement, maxRepeat = 3) {
            let value = inputElement.value;

            const charRegex = new RegExp(`(.)\\1{${maxRepeat},}`, 'g');
            value = value.replace(charRegex, (match) => {
                return match.charAt(0).repeat(maxRepeat);
            });

            inputElement.value = value;
        }

        function preventRepeatedWords(inputElement) {
            let value = inputElement.value.trim();

            const words = value.split(/\s+/);
            const uniqueWords = [];

            for (let i = 0; i < words.length; i++) {
                if (i === 0 || words[i] !== words[i - 1]) {
                    uniqueWords.push(words[i]);
                }
            }

            const newValue = uniqueWords.join(' ');

            if (newValue !== value) {
                inputElement.value = newValue;
            }
        }

        function preventRepeatedPatterns(inputElement) {
            let value = inputElement.value;

            const patternRegex = /(\b\w+\b)(?:\s*\1)+/gi;
            value = value.replace(patternRegex, '$1');

            const tamilPatternRegex = /([\u0B80-\u0BFF]+)(?:\s*\1)+/g;
            value = value.replace(tamilPatternRegex, '$1');

            inputElement.value = value;
        }

        function hasRepeatedPatterns(value) {
            if (/(.)\1{3,}/.test(value)) {
                return true;
            }

            if (/(\b\w+\b)(?:\s*\1)+/i.test(value)) {
                return true;
            }

            if (/([\u0B80-\u0BFF]+)(?:\s*\1)+/.test(value)) {
                return true;
            }

            return false;
        }


        const accordionButtons = document.querySelectorAll('.accordion-button');

        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = '#' + this.getAttribute('aria-controls');
                const targetCollapse = document.querySelector(targetId);

                if (targetCollapse) {
                    const isCollapsed = this.classList.contains('collapsed');

                    if (isCollapsed) {
                        this.classList.remove('collapsed');
                        this.setAttribute('aria-expanded', 'true');
                        targetCollapse.classList.add('show');
                    } else {
                        this.classList.add('collapsed');
                        this.setAttribute('aria-expanded', 'false');
                        targetCollapse.classList.remove('show');
                    }
                }
            });
        });



        // Amount fields handling
        if (!isFinalMode) {
            document.querySelectorAll('.financial-table input[data-temptypeid]').forEach(field => {
                const temptypeid = parseInt(field.dataset.temptypeid);

                // For radio buttons
                if (temptypeid === 4) {
                    field.addEventListener('change', function() {
                        const radioGroup = this.closest('.radio-group');
                        if (radioGroup) {
                            radioGroup.classList.remove('is-invalid');
                        }
                    });
                } else {
                    // For other field types
                    field.addEventListener('blur', function() {
                        if (temptypeid === 1) {
                            formatDecimalValue(this);
                        }
                    });

                    field.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                        if (temptypeid === 1) {
                            formatDecimalInput(this);
                        }
                    });

                    field.addEventListener('focus', function() {
                        this.select();
                    });

                    // Keydown handlers for numeric fields only
                    // In the amount fields handling section, update the keydown handler:
                    if (temptypeid === 1) {
                        field.addEventListener('keydown', function(e) {
                            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft',
                                                'ArrowRight', 'Tab', 'Enter', '.', '-'];

                            // Allow minus sign only at the beginning when no text is selected
                            if (e.key === '-') {
                                if (this.value.includes('-') && e.key === '-') {
                                    e.preventDefault();
                                }
                                return;
                            }

                            if (!/[\d.\-]/.test(e.key) && !allowedKeys.includes(e.key) && !e
                                .ctrlKey && !e.metaKey) {
                                e.preventDefault();
                            }

                            if (e.key === '.' && this.value.includes('.')) {
                                e.preventDefault();
                            }
                        });
                    }
                }
            });

            document.querySelectorAll('.financial-table input[data-temptypeid]').forEach(el => {
                const temptypeid = parseInt(el.dataset.temptypeid);

                if (temptypeid === 1) {
                    restrictNumberInput(el);
                    el.addEventListener('input', calculate);
                }
            });
            calculate();
        }


        const notes = document.getElementById('remarks');
        if (notes && !isFinalMode) {
            notes.addEventListener('input', function() {
                preventSqlInjection(this);
            });
        }

        const printBtn = document.getElementById('print-btn');
        if (printBtn) {
            printBtn.addEventListener('click', () => window.print());
        }

        const backBtn = document.getElementById('back-button');
        if (backBtn) {
            backBtn.addEventListener('click', () => {
                window.location.href = "{{ route('templateaudit.index') }}";
            });
        }

        // Finalize button handler
        const finalizeBtn = document.getElementById('finalize-button');
        if (finalizeBtn && !isFinalMode) {
            finalizeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const validator = $('#hriaOkpForm2').validate();

                // Validate all fields
                if (!validator.form()) {
                    return;
                }

                const lang = document.documentElement.lang || 'en';
                showModalGlobal(
                    'confirmation_alert',
                    'Confirmation',
                    'இறுதிசெய்தல் உறுதிப்படுத்தல்',
                    'Once finalized, the details cannot be modified. Are you sure you want to finalize the entry details?',
                    'இந்த தணிக்கையை இறுதி செய்ய விரும்புகிறீர்களா? இறுதி செய்த பிறகு, மேலும் மாற்றங்களைச் செய்ய முடியாது.',
                    lang,
                    function() {
                        if (!$('input[name="finalize"]').length) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'finalize',
                                value: '1'
                            }).appendTo('#hriaOkpForm2');
                        }

                        // Remove validation classes before submission
                        $('#hriaOkpForm2').find('.is-valid, .is-invalid').removeClass(
                            'is-valid is-invalid');

                        // Submit the form programmatically
                        $('#hriaOkpForm2').trigger('submit');

                    }
                );
            });
        }

    });
</script>
@endsection
