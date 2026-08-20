@extends('index2')
@include('common.alert')

@section('title', 'Template Audit Entry')

@section('content')
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
            margin: 20px 0;
        }

        .financial-table th {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .financial-table td {
            font-size: 0.8rem;
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
                $structure = $templateData['structure'];
                // dd($structure);
                $isEditMode = isset($lfaData) && $lfaData->statusflag === 'E';
                $isFinalMode = isset($lfaData) && $lfaData->statusflag === 'F';
                $instid = is_object($structure) ? $structure->instid ?? null : $structure['instid'] ?? null;
                $formcode = is_object($structure) ? $structure->formcode ?? null : $structure['formcode'] ?? null;
                $tempplanid = is_object($structure) ? $structure->tempplanid ?? null : $structure['tempplanid'] ?? null;
            $catcode = is_object($structure) ? $structure->catcode ?? null : $structure['catcode'] ?? null;

            @endphp
            @if ($structure && $structure->deptcode === '02' && $structure->formcode === 'LFA01')
                <form id="lfaForm" action="{{ route('templateaudit.lfa_insertUpdate') }}" method="POST"
                    enctype="multipart/form-data">
                    <div class="card" style="border-color: #7198b9">
                        <div class="card-header card_header_color lang" key="">
                            Template Audit Details - LFA
                        </div>
                        <div class="audit-form">
                            <div class="page-header">
                                @csrf
                                @if ($isEditMode || $isFinalMode)
                                    <input type="hidden" name="lfaid"
                                        value="{{ Crypt::encryptString($lfaData->lfa_details_id) }}">
                                @endif
                                <input type="hidden" name="tempplanid"
                                    value="{{ Crypt::encryptString($structure->tempplanid) }}">
                            </div>
                            <input type="hidden" name="action"
                                value="{{ $isEditMode || $isFinalMode ? 'update' : 'insert' }}">

                            <div id="template-content">

                                <div class="row g-3">
                                    <div class="col-md-6 col-lg-6">
                                        <label class="form-label lang" key="institution_name_address">Institution Name and
                                            Address</label>
                                        <div class="auto-field">{{ $basicinformation->instename }}</div>
                                    </div>

                                    <div class="col-md-6 col-lg-2">
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
                                        <label class="form-label lang" key="auditor_name_position">Auditor Name</label>
                                        <div class="auto-field">{{ $basicinformation->auditor_name_en }}</div>
                                    </div>

                                    <div class="col-md-6 col-lg-6">
                                        <label for="district_officer_name" class="form-label lang required"
                                            key="">District Library Officer</label>
                                        <input type="text" class="form-control lang" id="district_officer_name"
                                            name="district_officer_name" placeholder="Enter District Library Officer name"
                                            required maxlength="80" pattern="^[A-Za-z]{1,60}$" inputmode="text"
                                            value="{{ $isEditMode || $isFinalMode ? $lfaData->district_officer_name : old('district_officer_name') }}"
                                            {{ $isFinalMode ? 'readonly' : '' }} />
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <label class="form-label lang required" key="audityear_label">Audit Year</label>
                                        <select name="yearselected[]" id="yearselected"
                                            class="form-select mr-sm-2 lang-dropdown" multiple="multiple"
                                            data-placeholder-key="year_ph" {{ $isFinalMode ? 'disabled' : '' }} required>
                                            <option value="" disabled>Select Year</option>
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


                                <div class="table-responsive">
                                    <table class="financial-table table table-bordered">
                                        <thead>
                                            <tr>
                                                @foreach ($structure->headers as $header)
                                                    <th class="lang" data-english="{{ $header['lblename'] }}"
                                                        data-tamil="{{ $header['lbltname'] }}">
                                                        {{ $header['lblename'] }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $pairs = [];
                                                $headers = $structure->headers;

                                                for ($i = 0; $i < count($headers); $i++) {
                                                    if ($headers[$i]['inputtype'] === 'C') {
                                                        $pair = [
                                                            'category' => $headers[$i],
                                                            'amount_headers' => [],
                                                        ];

                                                        for ($j = $i + 1; $j < count($headers); $j++) {
                                                            if ($headers[$j]['inputtype'] === 'N') {
                                                                $pair['amount_headers'][] = $headers[$j];
                                                            } else {
                                                                break;
                                                            }
                                                        }
                                                        $pairs[] = $pair;
                                                    }
                                                }

                                                $max = 0;
                                                foreach ($pairs as &$pair) {
                                                    $pair['maintypes'] = array_values(
                                                        $pair['category']['maintypes'] ?? [],
                                                    );
                                                    $max = max($max, count($pair['maintypes']));
                                                }
                                                unset($pair);

                                                $alignedRows = [];
                                                $alignmentMap = [
                                                    'R' => 'E',
                                                    'L' => 'A',
                                                ];

                                                $maintypesArrays = [];
                                                foreach ($pairs as $pair) {
                                                    $maintypesArrays[] = $pair['maintypes'];
                                                }

                                                foreach ($alignmentMap as $leftFlag => $rightFlag) {
                                                    $indices = [];

                                                    foreach ($maintypesArrays as $arrayIndex => $maintypes) {
                                                        $leftIndex = collect($maintypes)->search(
                                                            fn($item) => ($item['maintypetotalflag'] ?? '') ===
                                                                $leftFlag,
                                                        );
                                                        $rightIndex = collect($maintypes)->search(
                                                            fn($item) => ($item['maintypetotalflag'] ?? '') ===
                                                                $rightFlag,
                                                        );

                                                        if ($leftIndex !== false) {
                                                            $indices[] = [
                                                                'pair' => $arrayIndex,
                                                                'index' => $leftIndex,
                                                                'flag' => $leftFlag,
                                                            ];
                                                        }
                                                        if ($rightIndex !== false) {
                                                            $indices[] = [
                                                                'pair' => $arrayIndex,
                                                                'index' => $rightIndex,
                                                                'flag' => $rightFlag,
                                                            ];
                                                        }
                                                    }

                                                    if (count($indices) >= 2) {
                                                        $maxIndex = max(array_column($indices, 'index'));

                                                        foreach ($indices as $item) {
                                                            $diff = $maxIndex - $item['index'];
                                                            if ($diff > 0) {
                                                                array_splice(
                                                                    $maintypesArrays[$item['pair']],
                                                                    $item['index'],
                                                                    0,
                                                                    array_fill(0, $diff, null),
                                                                );
                                                            }
                                                        }
                                                    }
                                                }

                                                foreach ($pairs as $index => &$pair) {
                                                    $pair['maintypes'] = $maintypesArrays[$index];
                                                }
                                                unset($pair);

                                                $maxlRows = max(
                                                    array_map(fn($pair) => count($pair['maintypes']), $pairs),
                                                );

                                                for ($row = 0; $row < $maxlRows; $row++) {
                                                    $rowData = [];
                                                    foreach ($pairs as $pairIndex => $pair) {
                                                        $item = $pair['maintypes'][$row] ?? null;
                                                        $rowData[$pairIndex] = $item;
                                                    }
                                                    $alignedRows[] = $rowData;
                                                }

                                                $inputIndex = 1;
                                                $amountValues = [];

                                                if ($isEditMode || $isFinalMode) {
                                                    // dd($lfaData->amounts);

                                                    foreach ($lfaData->amounts as $amount) {
                                                        $value = '';
                                                        switch ($amount->temptypeid) {
                                                            case 1:
                                                                $value = $amount->value_numeric;
                                                                break;
                                                            case 2:
                                                                $value = $amount->value_char;
                                                                break;
                                                            case 3:
                                                                $value = $amount->value_date;
                                                                break;
                                                            case 4:
                                                                $value = $amount->value_radio;
                                                                break;
                                                        }

                                                        $amountValues[$amount->maintypeid][
                                                            $amount->tmpauditheaderid
                                                        ] = $value;
                                                    }

                                                    // dd($amountValues);
                                                }
                                            @endphp

                                            @foreach ($alignedRows as $rowIndex => $rowData)
                                                <tr>
                                                    @foreach ($pairs as $pairIndex => $pair)
                                                        @php
                                                            $item = $rowData[$pairIndex] ?? null;
                                                            $categoryName = $pair['category']['lblename'] ?? '';
                                                            $className =
                                                                strtolower(
                                                                    preg_replace('/[^a-z0-9]+/i', '-', $categoryName),
                                                                ) . '-input';
                                                            $amountHeaders = $pair['amount_headers'] ?? [];

                                                            if ($item && !empty($item['maintypeid'])) {
                                                                $isRequired =
                                                                    empty($item['maintypetotalflag']) ||
                                                                    $item['maintypetotalflag'] === 'O';

                                                                $isHeading =
                                                                    !empty($item['maintypetotalflag']) &&
                                                                    $item['maintypetotalflag'] === 'H';
                                                            }
                                                        @endphp

                                                        <td
                                                            class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif @if ($isHeading ?? false) bg-light text-dark @endif">
                                                            @if (!empty($item))
                                                                <span class="lang"
                                                                    @if ($isRequired && !$isFinalMode && !$isHeading) aria-required="true" @endif
                                                                    data-english="{{ $item['maintypeename'] }}"
                                                                    data-tamil="{{ $item['maintypetname'] }}">
                                                                    {{ $item['maintypeename'] }}
                                                                </span>
                                                            @endif
                                                        </td>

                                                        @foreach ($amountHeaders as $amountHeaderIndex => $amountHeader)
                                                            @php
                                                                $headerId =
                                                                    $amountHeader['tmpauditheaderid'] ??
                                                                    $amountHeaderIndex;
                                                                $headerLabel = $amountHeader['lblename'] ?? '';
                                                                $yearClass = '';

                                                                if (
                                                                    stripos($headerLabel, 'current') !== false ||
                                                                    stripos($headerLabel, 'நடப்பு') !== false
                                                                ) {
                                                                    $yearClass = 'current-year-input';
                                                                } elseif (
                                                                    stripos($headerLabel, 'previous') !== false ||
                                                                    stripos($headerLabel, 'முந்தைய') !== false
                                                                ) {
                                                                    $yearClass = 'previous-year-input';
                                                                }

                                                                $className = "amount-input {$yearClass}";

                                                                $value = '';
                                                                if (!empty($item) && !empty($item['maintypeid'])) {
                                                                    $value =
                                                                        $amountValues[$item['maintypeid']][$headerId] ??
                                                                        '';

                                                                    if ($item['temptypeid'] == 1 && $value !== '') {
                                                                        $value = number_format($value, 2, '.', '');
                                                                    }
                                                                }
                                                            @endphp

                                                            <td
                                                                class="@if (!empty($item['maintypetotalflag'] ?? '')) table-secondary fw-bold @endif @if ($isHeading ?? false) bg-light text-dark @endif">
                                                                @if (!empty($item) && !empty($item['maintypeid']))
                                                                    @if ($isHeading)
                                                                        @if ($item['temptypeid'] == 1 && $value !== '')
                                                                            {{ number_format($value, 2) }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    @else
                                                                        @php
                                                                            $inputType = 'text';
                                                                            $step = '0.01';
                                                                            $maxlength = '80';
                                                                            $pattern = '.*';
                                                                            $placeholder = 'Enter value';

                                                                            switch ($item['temptypeid']) {
                                                                                case 1: // Numeric
                                                                                    $inputType = 'number';
                                                                                    $step = '0.01';
                                                                                    $maxlength = '';
                                                                                    $pattern = '^[0-9]*\.?[0-9]{0,2}$';
                                                                                    $placeholder = ' ';
                                                                                    break;
                                                                                case 2: // Character
                                                                                    $inputType = 'text';
                                                                                    $step = '';
                                                                                    $maxlength = '80';
                                                                                    $pattern = '';
                                                                                    $placeholder = 'Enter text';
                                                                                    break;
                                                                                case 3: // Date
                                                                                    $inputType = 'date';
                                                                                    $step = '';
                                                                                    $maxlength = '';
                                                                                    $pattern = '';
                                                                                    $placeholder = 'YYYY-MM-DD';
                                                                                    break;
                                                                                case 4: // Radio
                                                                                    $inputType = 'radio';
                                                                                    $step = '';
                                                                                    $maxlength = '';
                                                                                    $pattern = '';
                                                                                    $radioOptions = ['Yes', 'No'];
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
                                                                                            $value === $optionValue ||
                                                                                            $value === $option;
                                                                                        $radioId = "E{$inputIndex}_{$optionValue}";
                                                                                        $inputName = "amounts[{$item['maintypeid']}][{$headerId}]";
                                                                                    @endphp
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input"
                                                                                            id="{{ $radioId }}"
                                                                                            name="{{ $inputName }}"
                                                                                            value="{{ $optionValue }}"
                                                                                            {{ $isChecked ? 'checked' : '' }}
                                                                                            {{ $isFinalMode ? 'disabled' : '' }}>
                                                                                        <label class="form-check-label"
                                                                                            for="{{ $radioId }}">
                                                                                            {{ $option }}
                                                                                        </label>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            @if ($isRequired && !$isFinalMode)
                                                                                <div class="invalid-feedback">Please select
                                                                                    an option.</div>
                                                                            @endif
                                                                        @else
                                                                            @php
                                                                                $inputName = "amounts[{$item['maintypeid']}][{$headerId}]";
                                                                                $isReadOnly =
                                                                                    (!empty(
                                                                                        $item['maintypetotalflag']
                                                                                    ) &&
                                                                                        $item['maintypetotalflag'] !==
                                                                                            'O') ||
                                                                                    $isFinalMode;
                                                                                $isRequiredField =
                                                                                    $isRequired && !$isFinalMode;

                                                                                // echo "<!-- maintypeid: {$item['maintypeid']}, headerId: {$headerId}, value: '{$value}' -->";

                                                                            @endphp

                                                                            <input type="{{ $inputType }}"
                                                                                class="form-control text-end {{ $className }} @if ($inputType === 'number') amount-input @endif"
                                                                                id="E{{ $inputIndex }}"
                                                                                data-cell="E{{ $inputIndex }}"
                                                                                data-type="{{ $item['maintypetotalflag'] ?? 'value' }}"
                                                                                data-temptypeid="{{ $item['temptypeid'] }}"
                                                                                name="{{ $inputName }}"
                                                                                value="{{ $value }}"
                                                                                @if ($isReadOnly) readonly @endif
                                                                                @if ($isFinalMode) disabled @endif
                                                                                @if ($inputType === 'number') step="0.01" @endif
                                                                                @if ($maxlength) maxlength="{{ $maxlength }}" @endif
                                                                                placeholder="{{ $placeholder }}">
                                                                        @endif
                                                                    @endif

                                                                    @php $inputIndex++; @endphp
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                @php
                                                    $hasTotalRow = false;
                                                    foreach ($rowData as $item) {
                                                        if (
                                                            $item &&
                                                            in_array($item['maintypetotalflag'] ?? '', [
                                                                'R',
                                                                'E',
                                                            ])
                                                        ) {
                                                            $hasTotalRow = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp

                                                @if ($hasTotalRow)
                                                    <tr>
                                                        <td
                                                            colspan="{{ count($pairs) * (1 + count($pairs[0]['amount_headers'] ?? [])) }}">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>


                                <div class="notes-section">
                                    <div class="">
                                        <h4 class="section-title lang required" style="border: none;"
                                            key="audit_remarks">Audit Remarks</h4>

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
                                                    No remarks
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
                                                                        {{ !isset($existingRemarks[$i]) ? 'selected' : '' }}>
                                                                        Type</option>
                                                                    <option value="serious"
                                                                        {{ isset($existingRemarks[$i]) && (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->type : $existingRemarks[$i]['type']) == 'serious' ? 'selected' : '' }}>
                                                                        Serious</option>
                                                                    <option value="non-serious"
                                                                        {{ isset($existingRemarks[$i]) && (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->type : $existingRemarks[$i]['type']) == 'non-serious' ? 'selected' : '' }}>
                                                                        Non-Serious</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="position-relative">
                                                                    <textarea class="form-control remark-text" id="remarks" name="remarks[{{ $i }}][text]" rows="2"
                                                                        placeholder="Enter remark details..." required style="resize: vertical; overflow-y: auto;">{{ isset($existingRemarks[$i]) ? (is_object($existingRemarks[$i]) ? $existingRemarks[$i]->remark_text : $existingRemarks[$i]['text']) : '' }}</textarea>
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

                                            <div class="invalid-feedback">Please add at least one audit remark</div>
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
                                                @if ($isEditMode && $lfaData->file_id && isset($lfaData->fileDetails))
                                                    <div class="card overflow-hidden mb-3 bg-light card-fixed-width file-clickable"
                                                        id="viewfile-card-{{ $lfaData->file_id }}"
                                                        onclick="openFileInNewTab('{{ addslashes($lfaData->fileDetails->filepath) }}', '{{ addslashes($lfaData->fileDetails->filename) }}')"
                                                        style="cursor: pointer; transition: all 0.3s ease;">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="p-3 flex-grow-1">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-file-pdf text-danger me-3 fs-1"></i>
                                                                    <div>
                                                                        <p class="text-dark mb-1 fw-bold">
                                                                            {{ $lfaData->fileDetails->filename }}</p>
                                                                        <small class="text-muted">
                                                                            ({{ number_format($lfaData->fileDetails->filesize / 1024, 2) }}
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
                                            @if ($lfaData->file_id && isset($lfaData->fileDetails))
                                                <div class="card overflow-hidden mb-3 bg-light card-fixed-width file-clickable"
                                                    id="viewfile-card-{{ $lfaData->file_id }}"
                                                    onclick="openFileInNewTab('{{ addslashes($lfaData->fileDetails->filepath) }}', '{{ addslashes($lfaData->fileDetails->filename) }}')"
                                                    style="cursor: pointer; transition: all 0.3s ease;">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <div class="p-3 flex-grow-1">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-file-pdf text-danger me-3 fs-1"></i>
                                                                <div>
                                                                    <p class="text-dark mb-1 fw-bold">
                                                                        {{ $lfaData->fileDetails->filename }}</p>
                                                                    <small class="text-muted">
                                                                        ({{ number_format($lfaData->fileDetails->filesize / 1024, 2) }}
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
                                            value="{{ $isEditMode && $lfaData->file_id ? $lfaData->file_id : '' }}">

                                        <div class="text-danger small" id="file-error"></div>
                                        <small class="text-muted lang"
                                            data-english="Allowed file types: PDF, max size: 2MB"
                                            data-tamil="அனுமதிக்கப்பட்ட கோப்பு வகைகள்: PDF, அதிகபட்ச அளவு: 2MB">
                                            Allowed file types: PDF, max size: 2MB
                                        </small>
                                    </div>
                                    <div id="auditcertificateDiv" class="hideotherdiv">
                                        <h4 class="section-title lang required mb-3" style="border: none;"
                                            key="auditcertificate_label">Audit Certificate</h4>

                                        <div class="row mb-3 align-items-start">
                                            <div class="col-md-2 text-dark">
                                                <b class="lang" key="typeofcertificate">Type of Certificate</b>
                                            </div>

                                            @php
                                                $selectedType = $lfaData->cer_type_code ?? '01';
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
                                            <span class="lang">Save Draft</span>
                                        </button>
                                    @endif

                                    @if ($isEditMode)
                                        <button class="btn btn-primary me-2" type="submit" id="update-button">
                                            <i class="fas fa-save me-2"></i>
                                            <span class="lang">Update</span>
                                        </button>

                                        <button class="btn btn-success me-2" type="button" id="finalize-button">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span class="lang">Finalize</span>
                                        </button>
                                    @endif

                                    <button class="btn btn-secondary me-2" type="button" id="back-button">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        <span class="lang">Back</span>
                                    </button>

                                    @if ($isFinalMode)
                                        <button class="btn btn-primary btn-sm lang generateReportBtn" type="button"
                                            data-id="{{ $instid }}" data-formcode="{{ $formcode }}" 
                                            data-tempplanid ="{{ $tempplanid }}" data-catcode ="{{ $catcode }}">
                                            <i class="fas fa-file-alt me-1"></i> Generate Report
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


            @endif
        @endforeach
    </div>

    <script>
        function getlanguagelc() {
            return window.localStorage.getItem('lang') || 'en';
        }

        function initializeFileValidation() {
            $('#lfaForm').rules('add', {
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
                {{ $isEditMode && $lfaData->file_id && isset($lfaData->fileDetails) && $lfaData->fileDetails ? 'true' : 'false' }}
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
            let savedLang = getLanguage();
            $('#translate').val(savedLang);
            // console.log("Initial language:", savedLang);
        });


        $(document).on("click", ".generateReportBtn", function() {
            let instid = $(this).data("id");
            let formcode = $(this).data("formcode");
            let tempplanid = $(this).data("tempplanid");
            let catcode = $(this).data("catcode");

            let lang = getLanguage();
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
                    console.log(response.html);
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



        $(document).ready(function() {
            const certificateRemarks = JSON.parse('{!! addslashes(json_encode($Master_Auditcertificate)) !!}');
            const lang = localStorage.getItem('lang') || 'en';

            function toggleRemarks(typecode = null, remarks = '') {
                const selected = typecode || $('input[name="cer_typecode"]:checked').val();
                if (!selected) return;

                const selectedElement = $('input[name="cer_typecode"][value="' + selected + '"]');
                const labelHtml = selectedElement.next('label').text();

                $('#auditcertificateContentdiv').show();


                // Preload certificate content
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

            // Initialize on page load
            toggleRemarks();

            // Call on radio button change
            $('input[name="cer_typecode"]').change(function() {
                toggleRemarks();
            });
        });

        function showLoading() {
            document.querySelector('.loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.querySelector('.loading-overlay').style.display = 'none';
        }

        // Modal functions with accessibility fixes
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

            // remove close button completely
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

        function allowSingleZero(field) {
            field.addEventListener('input', function() {
                const value = this.value;

                // Allow: empty, single zero, or numbers without leading zero
                if (value === '' || value === '0' || !value.startsWith('0')) {
                    return;
                }

                // Remove leading zeros for numbers with multiple digits
                // But preserve single zero
                if (value.length > 1 && value.startsWith('0')) {
                    this.value = value.replace(/^0+/, '');

                    // If everything was removed and we had multiple zeros, keep single zero
                    if (this.value === '' && value.includes('0')) {
                        this.value = '0';
                    }
                }
            });

            field.addEventListener('blur', function() {
                // Validate on blur if needed
                if (this.value === '0') {
                    this.setCustomValidity(''); // Clear any validation errors
                }
            });
        }
        // Apply to all numeric input fields
        document.querySelectorAll('input[type="number"]').forEach(field => {
            allowSingleZero(field);
        });

        // Also apply to numeric fields that use text input but should be numbers
        document.querySelectorAll('input[data-temptypeid="1"]').forEach(field => {
            if (field.type !== 'number') {
                allowSingleZero(field);
            }
        });

        // Utility functions
        function sanitizeBranch(value) {
            return value.replace(/[<>;"'`|\\]/g, '').trim();
    }

    function countSpaces(text) {
        return (text.match(/ /g) || []).length;
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
            // Allow spaces by not filtering them out aggressively
            let filtered = this.value;

            // Only filter if value exceeds max length
            if (filtered.length > maxLen) {
                filtered = filtered.slice(0, maxLen);
            }

            this.value = filtered;
        });
    }

    function restrictNumberInput(el) {
        const temptypeid = parseInt(el.dataset.temptypeid);

        if (temptypeid !== 1) return;

        const maxLen = parseInt(el.dataset.maxlen || '13', 10);
        const decimals = parseInt(el.dataset.decimals || '2', 10);

        el.addEventListener('input', function() {
            let v = this.value.replace(/[^0-9.-]/g, '');

            if (v.includes('-')) {
                v = '-' + v.replace(/-/g, '');
            }
            const parts = v.split('.');
            if (parts.length > 2) {
                v = parts[0] + '.' + parts.slice(1).join('');
            }

            if (parts[0]) {
                parts[0] = parts[0].slice(0, maxLen);
            }

            if (parts[1]) {
                parts[1] = parts[1].slice(0, decimals);
            }

            v = parts[0] + (parts[1] ? '.' + parts[1] : '');

            if (v !== this.value) {
                this.value = v;
            }

            this.classList.remove('is-invalid');
            this.setCustomValidity('');

            setTimeout(() => {
                calculateEnhanced();
            }, 100);
        });

        el.addEventListener('blur', function() {
            validateNumericField(this);
            formatDecimalValue(this);
        });


    }

    function validateAllNumericFields() {
        const numericFields = document.querySelectorAll('input[data-temptypeid="1"]');
        let allValid = true;
        let firstInvalidField = null;

        numericFields.forEach(field => {
            const value = field.value.trim();

            // Skip empty optional fields
            if (value === '' && !field.hasAttribute('required')) {
                return;
            }

            const numValue = parseFloat(value);

            // Check if value is valid (allow negative values)
            if (value !== '' && isNaN(numValue)) {
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
        });

        // Focus on first invalid field if any
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

    function validateNumericField(field) {
        const value = field.value.trim();
        field.classList.remove('is-invalid', 'is-valid');

        if (field.hasAttribute('required') && value === '') {
            field.classList.add('is-invalid');
            field.setCustomValidity('This field is required');
            return false;
        }

        if (value !== '') {
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

            field.classList.add('is-valid');
            field.setCustomValidity('');
            return true;
        }

        field.setCustomValidity('');
        return true;
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

        newRow.innerHTML =
            `
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
        const charCount = rowElement.querySelector('.char-count');
        const addBtn = rowElement.querySelector('.add-row');
        const removeBtn = rowElement.querySelector('.remove-row');

        if (textarea) {
            textarea.addEventListener('input', function() {
                // Just remove invalid styling on input
                this.classList.remove('is-invalid');
            });
        }

        // Add row functionality
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                addRemarkRow();
            });
        }

        // Remove row functionality
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

    // Initialize existing rows on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.remark-row').forEach(row => {
            initializeRemarkRow(row);
        });
    });

    // Validation functions
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

    // In your form submission handling
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

        // Remove any existing hidden inputs
        document.querySelectorAll('input[name^="remarks"]').forEach(input => input.remove());

        // Add remarks as array format
        remarksData.forEach((remark, index) => {
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = `remarks[${index}][type]`;
            typeInput.value = remark.type;
            document.getElementById('lfaForm').appendChild(typeInput);

            const textInput = document.createElement('input');
            textInput.type = 'hidden';
            textInput.name = `remarks[${index}][text]`;
            textInput.value = remark.text;
            document.getElementById('lfaForm').appendChild(textInput);
        });
    }
    // Formatting functions
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

            if (value && value !== '') {
                // Remove any existing formatting but keep negative sign
                value = value.replace(/[^0-9.-]/g, '');

                // Handle cases where user just typed a decimal point
                if (value === '.' || value === '-.') {
                    field.value = value === '-.' ? '-0.00' : '0.00';
                    return;
                }

                // Split into whole and decimal parts
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
            }
        }
    }


    // Enhanced calculation function with column-wise processing
    function calculateEnhanced() {
        const table = document.querySelector('.financial-table');
        const rows = table.querySelectorAll('tbody tr');

        // Initialize totals for each column
        const revenueTotals = {
            30: 0,
            31: 0,
            32: 0
        }; // Header IDs for Revenue columns
        const expenditureTotals = {
            34: 0,
            35: 0,
            36: 0
        }; // Header IDs for Expenditure columns
        const liabilitiesTotals = {
            30: 0,
            31: 0,
            32: 0
        }; // Header IDs for Liabilities columns
        const assetsTotals = {
            34: 0,
            35: 0,
            36: 0
        }; // Header IDs for Assets columns

        let openingVal = 0;

        // Reset all total fields first
        document.querySelectorAll(
            'input[data-type="R"], input[data-type="E"], input[data-type="L"], input[data-type="A"]'
        ).forEach(input => {
            if (input.dataset.temptypeid === '1') {
                input.value = '0.00';
            }
        });

        rows.forEach(row => {
            const cells = Array.from(row.cells);

            // Skip empty rows
            if (cells.length < 8) return;

            const revenueLabel = cells[0]?.textContent?.trim().toLowerCase() || '';
            const expenditureLabel = cells[4]?.textContent?.trim().toLowerCase() || '';

            // Process Revenue/Liabilities side (columns 1-3)
            for (let i = 1; i <= 3; i++) {
                const input = cells[i]?.querySelector('input[data-temptypeid="1"]');
                if (input && !input.readOnly && !input.disabled) {
                    const value = parseFloat(input.value) || 0;
                    const dataType = input.dataset.type;
                    const headerId = getHeaderIdFromInput(input);

                    if (dataType === 'O') {
                        openingVal = value;
                    } else if (dataType === 'value') {
                        if (revenueLabel.includes('liabilities') || isLiabilitiesRow(revenueLabel)) {
                            liabilitiesTotals[headerId] += value;
                        } else {
                            revenueTotals[headerId] += value;
                        }
                    }
                }
            }

            // Process Expenditure/Assets side (columns 5-7)
            for (let i = 5; i <= 7; i++) {
                const input = cells[i]?.querySelector('input[data-temptypeid="1"]');
                if (input && !input.readOnly && !input.disabled && input.dataset.type === 'value') {
                    const value = parseFloat(input.value) || 0;
                    const headerId = getHeaderIdFromInput(input);

                    if (expenditureLabel.includes('assets') || isAssetsRow(expenditureLabel)) {
                        assetsTotals[headerId] += value;
                    } else {
                        expenditureTotals[headerId] += value;
                    }
                }
            }
        });

        // Set calculated totals
        setTotalValues('R', revenueTotals);
        setTotalValues('E', expenditureTotals);
        setTotalValues('L', liabilitiesTotals);
        setTotalValues('A', assetsTotals);

        // Calculate overall totals
        calculateOverallTotals(revenueTotals, expenditureTotals, openingVal);
    }

    // Helper function to extract header ID from input name
    function getHeaderIdFromInput(input) {
        const name = input.name;
        const matches = name.match(/amounts\[\d+\]\[(\d+)\]/);
        return matches ? parseInt(matches[1]) : 0;
    }

    // Helper function to set total values
    function setTotalValues(dataType, totals) {
        document.querySelectorAll(`input[data-type="${dataType}"]`).forEach(input => {
            if (parseInt(input.dataset.temptypeid) === 1) {
                const headerId = getHeaderIdFromInput(input);
                const totalValue = parseFloat(totals[headerId]);
                if (!isNaN(totalValue)) {
                    input.value = totalValue.toFixed(2);
                } else {
                    input.value = ''; // or 0 if you prefer
                }
            }
        });
    }


    // Calculate overall totals and closing balance
    function calculateOverallTotals(revenueTotals, expenditureTotals, openingVal) {
        // Calculate overall receipts (Revenue + Opening)
        const overallReceipts14 = (revenueTotals[14] || 0) + openingVal;
        const overallReceipts15 = (revenueTotals[15] || 0) + openingVal;
        const overallReceipts16 = (revenueTotals[16] || 0) + openingVal;

        // Calculate closing balance (Overall Receipts - Expenditure)
        const closingBalance14 = overallReceipts14 - (expenditureTotals[18] || 0);
        const closingBalance15 = overallReceipts15 - (expenditureTotals[19] || 0);
        const closingBalance16 = overallReceipts16 - (expenditureTotals[20] || 0);

        // Set overall totals (all columns get the same value for these)
        setOverallTotalValues('V', [overallReceipts14, overallReceipts15, overallReceipts16]);
        setOverallTotalValues('X', [overallReceipts14, overallReceipts15, overallReceipts16]);
        setOverallTotalValues('C', [closingBalance14, closingBalance15, closingBalance16]);
    }

    // Helper function to set overall total values
    function setOverallTotalValues(dataType, values) {
        const elements = document.querySelectorAll(`input[data-type="${dataType}"]`);
        elements.forEach((el, index) => {
            if (parseInt(el.dataset.temptypeid) === 1 && index < values.length) {
                el.value = values[index].toFixed(2);
            }
        });
    }

    // Helper functions to categorize rows
    function isLiabilitiesRow(label) {
        const liabilitiesKeywords = ['deposits', 'lapsed deposit', 'income tax payables', 'gst payables',
            'lwf payables', 'padhippalar nala nidhi payables', 'shortage of books', 'other payables',
            'liabilities', 'பொறுப்புகள்'
        ];
        return liabilitiesKeywords.some(keyword => label.includes(keyword));
    }

    function isAssetsRow(label) {
        const assetsKeywords = ['investments', 'advances', 'current assets', 'library cess balance receivable',
            'balance of other charges receivable', 'receivable of other lease', 'other receivables',
            'assets', 'சொத்துகள்'
        ];
        return assetsKeywords.some(keyword => label.includes(keyword));
    }

    // Event listener for input changes
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[data-temptypeid="1"]') && !e.target.readOnly && !e.target.disabled) {
            setTimeout(calculateEnhanced, 10);
        }
    });

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (!{{ $isEditMode ? 'true' : 'false' }} && !{{ $isFinalMode ? 'true' : 'false' }}) {
            setTimeout(calculateEnhanced, 100);
        }
    });

    // jQuery Validation Configuration
    function initializeFormValidation() {
        const isFinalMode = {{ $isFinalMode ? 'true' : 'false' }};
        if (isFinalMode) return; // Skip validation in final mode

        // Remove any existing validation
        if ($.validator) {
            const form = $('#lfaForm');
            if (form.data('validator')) {
                form.validate().destroy();
            }
        }


        $.validator.addMethod("amountField", function(value, element) {
            if ($(element).closest('.radio-group').length) {
                const radioName = $(element).attr('name');
                return $(`input[name="${radioName}"]:checked`).length > 0;
            }

            const temptypeid = $(element).data('temptypeid');
            if (temptypeid === 1) {
                if (value === '') return !$(element).prop('required');

                const numValue = parseFloat(value);
                return !isNaN(numValue) && numValue >= 0;
            }
            return true;
        }, "Please enter a valid positive amount");

        $.validator.addMethod("ifscCode", function(value, element) {
            return this.optional(element) || /^[A-Z]{4}0[A-Z0-9]{6}$/.test(value);
        }, "Please enter a valid IFSC code (e.g., SBIN0000123)");

        $.validator.addMethod("phoneIndia", function(value, element) {
            return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
        }, "Please enter a valid 10-digit Indian phone number starting with 6-9");

        $.validator.addMethod("accountNumber", function(value, element) {
            return this.optional(element) || /^[0-9]{9,16}$/.test(value);
        }, "Please enter a valid 9-16 digit account number");

        $.validator.addMethod("alphaSpace", function(value, element) {
            return this.optional(element) || /^[A-Za-z\s.'\-]{1,80}$/.test(value);
        }, "Please enter only letters, spaces, and basic punctuation");

        $.validator.addMethod("branchName", function(value, element) {
            return this.optional(element) || /^[A-Za-z\s.'\-]{1,60}$/.test(value);
        }, "Please enter a valid branch name");

        $.validator.addMethod("select2Required", function(value, element) {
            // For Select2 multiple select
            if ($(element).hasClass('select2-hidden-accessible')) {
                const selectedValues = $(element).val();
                return selectedValues !== null && selectedValues.length > 0 && selectedValues[0] !== '';
            }
            // For regular select
            return value !== null && value !== '' && value !== undefined;
        }, "This field is required");

        $.validator.addMethod("amountField", function(value, element) {
            if ($(element).closest('.radio-group').length) {
                // Radio button validation
                const radioName = $(element).attr('name');
                return $(`input[name="${radioName}"]:checked`).length > 0;
            }

            const temptypeid = $(element).data('temptypeid');
            if (temptypeid === 1) { // Numeric
                if (value === '') return true; // Allow empty if not required
                return /^[0-9]*\.?[0-9]{0,2}$/.test(value);
            }
            return true;
        }, "Please enter a valid amount");

        // Validation rules configuration
        $('#lfaForm').validate({
            rules: {
                'yearselected[]': {
                    select2Required: true
                },
                district_officer_name: {
                    required: true,
                    maxlength: 80
                },

                remarks: {
                    required: true,
                    // maxlength: 1000
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
                district_officer_name: {
                    required: "Please enter district library officer name",
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
                ifsc_code: {
                    required: "Please select IFSC code",
                    ifscCode: "Please enter a valid IFSC code (e.g., SBIN0000123)"
                },
                remarks: {
                    required: "Please enter remarks",
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

                // Special handling for radio groups
                if ($(element).attr('type') === 'radio') {
                    $(element).closest('.radio-group').addClass('is-invalid');
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');

                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2-container').removeClass('is-invalid').addClass('is-valid');

                    // Remove any existing error messages
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
            ignore: [], // Validate hidden fields (like Select2)
            onfocusout: function(element) {
                this.element(element);
            },
            invalidHandler: function(event, validator) {
                // Scroll to first error
                const firstError = $('.is-invalid').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);

                    // Focus on the first error field
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

                // Validate as user types (except for Enter)
                if ($(element).attr('type') !== 'password') {
                    this.element(element);
                }
            },

            onsubmit: true,
            submitHandler: function(form) {
                handleFormSubmission(form);
                return false; // Prevent default form submission
            },
        });

        $('.financial-table input[data-temptypeid]').each(function() {
            const field = $(this);
            const isRequired = field.prop('required');

            if (isRequired) {
                const fieldName = field.attr('name');

                $('#lfaForm').rules('add', {
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
                    calculateEnhanced();
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

        showLoading();

        formatNumericValues();
        formatRemarksBeforeSubmit();

        const formData = new FormData(form);

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
                        response.message,
                        response.message,
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
                    break;
                }
            }
        }

        return hasValidData;
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

        // Regular expression to find more than maxRepeat consecutive identical characters
        const regex = new RegExp(`(.)\\1{${maxRepeat},}`, 'g');

        if (regex.test(value)) {
            // Replace consecutive repeated characters beyond the limit
            let cleanedValue = value;
            let foundExcessiveRepeat = false;

            // Find and replace excessive repetitions
            cleanedValue = cleanedValue.replace(regex, function(match) {
                foundExcessiveRepeat = true;
                // Keep only maxRepeat + 1 characters (the original plus maxRepeat repetitions)
                return match.charAt(0).repeat(maxRepeat + 1);
            });

            if (foundExcessiveRepeat && cleanedValue !== value) {
                field.value = cleanedValue;

                // Show warning if needed
                const warningEl = field.nextElementSibling;
                if (warningEl && warningEl.classList.contains('invalid-feedback')) {
                    warningEl.textContent = `No more than ${maxRepeat} consecutive identical characters allowed`;
                    setTimeout(() => {
                        warningEl.textContent = '';
                    }, 3000);
                }

                return true; // Value was modified
            }
        }

        return false; // No modification needed
    }

    // Main execution
    document.addEventListener('DOMContentLoaded', function() {
        showLoading();

        window.addEventListener('load', function() {
            hideLoading();
        });

        initializeFileValidation();
        initializeExistingFile();
        addFileHoverEffects();

        conditionalValidator = setupConditionalRequiredValidation();

        // Initialize variables
        const isFinalMode = {{ $isFinalMode ? 'true' : 'false' }};
        const isEditMode = {{ $isEditMode ? 'true' : 'false' }};
        const NAME_ALLOWED_REGEX = "^[A-Za-z\\u0B80-\\u0BFF\\s.'-]*$"; // Fixed regex with spaces
        const MAX_NAME_LENGTH = 80;
        const MAX_BRANCH_LENGTH = 60;
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

        // Initialize Select2
        $(document).ready(function() {
            $('#yearselected').select2({
                placeholder: "Select Year",
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                // Trigger validation immediately
                $(this).trigger('blur');
            });

            // Force validation on Select2 close
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


            // Initialize form validation
            initializeFormValidation();
        });

        // Input restrictions (only if not in final mode)
        if (!isFinalMode) {
            const name = document.getElementById('district_officer_name');


            if (name) {
                // Enhanced validation for name field - NO NUMBERS
                name.addEventListener('input', function() {
                    // Allow only letters, spaces, and basic punctuation - NO NUMBERS
                    this.value = this.value.replace(/[^A-Za-z\s.'\-]/g, '');

                    // Limit length
                    if (this.value.length > MAX_NAME_LENGTH) {
                        this.value = this.value.slice(0, MAX_NAME_LENGTH);
                    }

                    preventSqlInjection(this);
                    preventRepeatedCharacters(this, 3);
                });

                name.addEventListener('blur', function() {
                    this.value = this.value.trim();
                    preventSqlInjection(this);
                    preventRepeatedCharacters(this, 3);
                });

                name.addEventListener('keypress', function(e) {
                    // Allow only letters, spaces, and basic punctuation - NO NUMBERS
                    const allowedChars = /[A-Za-z\s.'\-]/;
                    if (!allowedChars.test(e.key)) {
                        e.preventDefault();
                    }

                    // Prevent excessive repetition
                    const currentValue = this.value;
                    const newValue = currentValue + e.key;
                    const regex = new RegExp(`(.)\\1{3,}`, 'g');
                        if (regex.test(newValue)) {
                            e.preventDefault();
                        }
                    });

                    // Remove the restrictText call since we're handling it above
                    // restrictText(name, NAME_ALLOWED_REGEX, MAX_NAME_LENGTH);
                }
            }




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
                            calculateEnhanced(); // Recalculate when radio changes
                        });
                    } else {
                        // For other field types
                        field.addEventListener('blur', function() {
                            if (temptypeid === 1) {
                                formatDecimalValue(this);
                            }
                            calculateEnhanced(); // Recalculate on blur
                        });

                        field.addEventListener('input', function() {
                            this.classList.remove('is-invalid');
                            if (temptypeid === 1) {
                                formatDecimalInput(this);
                            }
                            // Don't calculate on every input - too heavy
                        });

                        field.addEventListener('focus', function() {
                            this.select();
                        });

                        // Keydown handlers for numeric fields only
                        if (temptypeid === 1) {
                            field.addEventListener('keydown', function(e) {
                                const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft',
                                    'ArrowRight', 'Tab', 'Enter', '.', '-'
                                ];

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

                // Remove the old calculate() calls and use calculateEnhanced instead
                document.querySelectorAll('.financial-table input[data-temptypeid="1"]').forEach(el => {
                    restrictNumberInput(el);
                    // Don't attach calculate on every input - use the event listener above
                });

                // Initial calculation only for new forms
                if (!{{ $isEditMode ? 'true' : 'false' }} && !{{ $isFinalMode ? 'true' : 'false' }}) {
                    calculateEnhanced();
                }
            }

            const notes = document.getElementById('remarks');
            if (notes && !isFinalMode) {
                notes.addEventListener('input', function() {
                    preventSqlInjection(this);
                });
            }

            // Button event handlers
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

                    const validator = $('#lfaForm').validate();

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
                            // Add finalize flag and submit
                            if (!$('input[name="finalize"]').length) {
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'finalize',
                                    value: '1'
                                }).appendTo('#lfaForm');
                            }

                            $('#lfaForm').trigger('submit');

                        }
                    );
                });
            }


        });
    </script>
@endsection
