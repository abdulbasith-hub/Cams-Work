@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Template Audit Entry')

@php
    $sessionCharge = session('charge');
@endphp
<style>
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

    .tamil-text {
        font-family: 'Hind Madurai', sans-serif;
        font-size: 1.1rem;
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
        padding: 30px;
        margin-bottom: 30px;
    }

    .section-title {
        color: var(--primary);
        border-bottom: 3px solid var(--accent);
        padding-bottom: 10px;
        margin: 25px 0 20px 0;
        font-weight: 700;
    }

    .form-label {
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 8px;
    }

    .auto-field {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 4px solid var(--accent);
        font-weight: 500;
        margin-bottom: 15px;
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
    }

    .financial-table td {
        padding: 12px 15px;
        border: 1px solid #dee2e6;
    }

    .financial-table .total-row {
        background-color: #f8f9fa;
        font-weight: 700;
    }

    .notes-section {
        background-color: #fff3cd;
        border-left: 4px solid var(--warning);
        padding: 20px;
        border-radius: 6px;
        margin-top: 25px;
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

    /* Form control styling */
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px 12px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background: white;
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    /* Responsive adjustments */
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
</style>

<div class="my-4">
    @foreach ($templatesData as $templateData)
        @php $structure = $templateData['structure']; @endphp
        @if ($structure->deptcode === '01')
            <div class="audit-form">
                @if ($structure->formcode === 'HRIA01')
                    <div id="template1-content">
                        <h3 class="section-title lang" data-english="Basic Information" data-tamil="அடிப்படை தகவல்கள்"></h3>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label lang"
                                    data-english="Institution Name and Address"
                                    data-tamil="நிறுவனத்தின் பெயர் மற்றும் முகவரி"></label>
                                <div class="auto-field">{{ $template->instename }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label lang"
                                    data-english="Reference Number"
                                    data-tamil="குறிப்பு எண்"></label>
                                <div class="auto-field">{{ $template->erpno }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label lang"
                                    data-english="Audited Year"
                                    data-tamil="தணிக்கை செய்யப்பட்ட ஆண்டு"></label>
                                <div class="auto-field">{{ $template->audit_year }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label lang"
                                    data-english="Auditor Name and Position"
                                    data-tamil="தணிக்கையாளர் பெயர் மற்றும் பதவி"></label>
                                <div class="auto-field">{{ $template->auditor_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label lang"
                                    data-english="Audit Date"
                                    data-tamil="தணிக்கை செய்த நாள்"></label>
                                <div class="auto-field">{{ $template->audit_date }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label lang"
                                    data-english="Assistant Commissioner Zone"
                                    data-tamil="உதவி ஆணையர் சரகம்"></label>
                                <div class="auto-field">{{ $template->zone_en }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label lang"
                                    data-english="Inspector Circle"
                                    data-tamil="ஆய்வாளர் வட்டம்"></label>
                                <div class="auto-field">{{ $template->circle_en }}</div>
                            </div>
                        </div>

                        <h3 class="section-title lang" data-english="Entry Information" data-tamil="நுழைவுத் தகவல்கள்"></h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="Priest/Poojari Name"
                                        data-tamil="அர்ச்சகர்/பூசாரி பெயர்"></label>
                                    <input type="text" class="form-control lang"
                                        data-english-placeholder="Priest/Poojari Name"
                                        data-tamil-placeholder="அர்ச்சகர்/பூசாரி பெயர்">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="Phone Number"
                                        data-tamil="தொலைபேசி எண்"></label>
                                    <input type="tel" class="form-control lang"
                                        data-english-placeholder="Phone Number"
                                        data-tamil-placeholder="தொலைபேசி எண்">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="Bank Name"
                                        data-tamil="வங்கி பெயர்"></label>
                                    <input type="text" class="form-control lang"
                                        data-english-placeholder="Bank Name"
                                        data-tamil-placeholder="வங்கி பெயர்">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="Account Number"
                                        data-tamil="கணக்கு எண்"></label>
                                    <input type="text" class="form-control lang"
                                        data-english-placeholder="Account Number"
                                        data-tamil-placeholder="கணக்கு எண்">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="Branch Name"
                                        data-tamil="கிளை பெயர்"></label>
                                    <input type="text" class="form-control lang"
                                        data-english-placeholder="Branch Name"
                                        data-tamil-placeholder="கிளை பெயர்">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="entry-field">
                                    <label class="form-label lang"
                                        data-english="IFSC Code"
                                        data-tamil="IFSC குறியீடு"></label>
                                    <input type="text" class="form-control lang"
                                        data-english-placeholder="IFSC Code"
                                        data-tamil-placeholder="IFSC குறியீடு">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="financial-table">
                                <thead>
                                    <tr>
                                        @foreach ($structure->headers as $header)
                                            <th class="lang"
                                                data-english="{{ $header->lblename }}"
                                                data-tamil="{{ $header->lbltname }}">
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @elseif($structure->formcode === 'HRIA02')
                    <div id="template2-content">
                        <h3 class="section-title lang" data-english="Basic Information" data-tamil="அடிப்படை தகவல்கள்"></h3>
                        <div class="table-responsive">
                            <table class="financial-table">
                                <thead>
                                    <tr>
                                        @foreach ($structure->headers as $header)
                                            <th class="lang"
                                                data-english="{{ $header->lblename }}"
                                                data-tamil="{{ $header->lbltname }}">
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($structure->mainTypes as $mainType)
                                        <tr class="main-type-header">
                                            <td colspan="{{ $structure->headers->count() }}"
                                                style="background-color: #f0f0f0; font-weight: bold;"
                                                class="lang"
                                                data-english="{{ $mainType['maintypeename'] }}"
                                                data-tamil="{{ $mainType['maintypetname'] }}">
                                            </td>
                                        </tr>

                                        @foreach ($mainType['subtypes'] as $subtype)
                                            <tr>
                                                @if ($structure->headers->count() >= 2)
                                                    <td class="lang"
                                                        data-english="{{ $subtype['stypeename'] }}"
                                                        data-tamil="{{ $subtype['stypetname'] }}">
                                                    </td>

                                                    @if ($structure->headers->count() > 2)
                                                        @for ($i = 2; $i < $structure->headers->count(); $i++)
                                                            <td></td>
                                                        @endfor
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    <tr class="total-row">
                                        @foreach ($structure->headers as $index => $header)
                                            @if ($index === 0)
                                                <td class="lang"
                                                    data-english="Total"
                                                    data-tamil="மொத்தம்"></td>
                                            @elseif($index === 1)
                                                <td><input type="number" class="form-control total-amount" readonly></td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="notes-section">
                    <h4 class="lang" data-english="Remarks" data-tamil="தணிக்கை குறிப்புகள்"></h4>
                    <textarea id="audit-notes" class="form-control" rows="6"
                        data-english-placeholder="Enter audit notes here..."
                        data-tamil-placeholder="தணிக்கை குறிப்புகளை இங்கே..."></textarea>

                    <div class="mt-3">
                        <label class="form-label lang" data-english="Attachment" data-tamil="இணைப்பு"></label>
                        <input type="file" class="form-control" id="audit-attachment" multiple>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4 no-print">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <span class="lang" data-english="Save" data-tamil="சேமிக்க"></span>
                    </button>
                    <button class="btn btn-success" id="print-btn">
                        <i class="fas fa-print me-2"></i>
                        <span class="lang" data-english="Print" data-tamil="அச்சிடு"></span>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>



<div class="my-4">
    @if ($sessionCharge->deptcode === '04')
        <div class="audit-form">

            <h3 class="section-title" data-tamil="குறைவிடர் -குறைந்த நடவடிக்கை சங்கங்கள்"
                data-english="Low Risk/Low Transaction Societies">
                Low Risk/Low Transaction Societies
            </h3>

            <h3 class="section-title" data-tamil="கடன் கூட்டுறவு சங்கங்களுக்கான தணிக்கை சான்றிதழ்"
                data-english="AUDIT CERTIFICATE FOR CREDIT SOCIETIES (MODEL)">
                AUDIT CERTIFICATE FOR CREDIT SOCIETIES (MODEL)
            </h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" data-tamil="சங்கத்தின் பெயர்" data-english="Society Name">Society
                        Name</label>
                    <div class="auto-field">2912 Kottilpadu Fishermen Society</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" data-tamil="தணிக்கை ஆண்டு" data-english="Year of Audit">Year of
                        Audit</label>
                    <div class="auto-field">2021-2022</div>
                </div>
            </div>

            <h4 class="section-title" data-tamil="சங்கங்களின் சுய விவரம்(கடன்)"
                data-english="Progressive Profile of the Institutions (Credit)">
                Progressive Profile of the Institutions (Credit)
            </h4>

            <div class="table-responsive">
                <table class="financial-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th data-tamil="விவரங்கள்" data-english="PARTICULARS">PARTICULARS</th>
                            <th data-tamil="நடப்பாண்டு" data-english="CURRENT YEAR">CURRENT YEAR</th>
                            <th data-tamil="முந்தைய ஆண்டு" data-english="PREVIOUS YEAR">PREVIOUS YEAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- I.a. Receipts -->
                        <tr>
                            <td>I a.</td>
                            <td data-tamil="வரவுகள்" data-english="RECEIPTS">RECEIPTS</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நானாவித வரவுகள்" data-english="MIS.INCOME">MIS.INCOME</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="ஆரம்ப இருப்பு" data-english="OPENING BALANCE">OPENING BALANCE</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- I.b. Payments -->
                        <tr>
                            <td>I b.</td>
                            <td data-tamil="செலவுகள்" data-english="PAYMENTS">Payments</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="சாதல்வார் செலவினங்கள்" data-english="CONTINGENCIES">CONTINGENCIES</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="வங்கியிருப்பு" data-english="BANK DEPOSITS">BANK DEPOSITS</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="இறுதி இருப்பு" data-english="CLOSING BALANCE">CLOSING BALANCE</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- II.a. Liabilities -->
                        <tr>
                            <td>II a.</td>
                            <td data-tamil="பொறுப்புகள்" data-english="LIABILITIES">LIABILITIES</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="பங்கு மூலதனம்" data-english="Share Capital">Share Capital</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="மான்யம்" data-english="Grants and Subsidies">Grants and Subsidies</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="காப்புகள் மற்றும் உபரித்தொகை" data-english="Reserves & Surplus">Reserves &
                                Surplus</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="கடன் நிதிகள்" data-english="Loan Funds">Loan Funds</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="பற்பல கடனீந்தோர்" data-english="Sundry Creditors">Sundry Creditors</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="சட்டபூர்வமற்ற ஒதுக்கீடு" data-english="NON STATUTORY RESERVE">NON
                                STATUTORY RESERVE</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நிகர லாபம்" data-english="NET PROFIT">NET PROFIT</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- II.b. Assets -->
                        <tr>
                            <td>II b.</td>
                            <td data-tamil="சொத்துக்கள்" data-english="ASSETS">ASSETS</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="அசையா சொத்துக்கள்" data-english="FIXED ASSETS">Fixed Assets</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="வங்கியிருப்பு" data-english="CASH AT BANK">CASH AT BANK</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="முதலீடுகள்" data-english="INVESTMENTS">Investments</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நடப்பு சொத்துக்கள்" data-english="CURRENT ASSETS">Current Assets</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நிகர நட்டம்" data-english="NET LOSS">Net Loss</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- III.a. Expenditure -->
                        <tr>
                            <td>III a.</td>
                            <td data-tamil="செலவினங்கள்" data-english="EXPENDITURE">EXPENDITURE</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="பணியாளர் மற்றும் சாதல்வார் செலவுகள் நிலுவை"
                                data-english="Est. & Cont. Due">Est. & Cont. Due</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="பிற செலவினங்கள்" data-english="Other expenses">Other expenses</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நிகர லாபம், ஐந்தொகை பட்டியலுக்கு கொண்டு செல்லப்பட்டது"
                                data-english="Net Profit taken to B/S">Net Profit taken to B/S</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- III.b. Income -->
                        <tr>
                            <td>III b.</td>
                            <td data-tamil="வரவினங்கள்" data-english="INCOME">INCOME</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நானாவித வரவுகள் மற்றும் வரவேண்டிய நானாவித வரவுகள்"
                                data-english="Misc. Income Received and Due">Misc. Income Received and Due</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நிகர நட்டம், ஐந்தொகை பட்டியலுக்கு கொண்டு செல்லப்பட்டது"
                                data-english="Net LOSS taken to B/S">Net LOSS taken to B/S</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>

                        <!-- IV. Statement IIIA -->
                        <tr>
                            <td>IV</td>
                            <td data-tamil="அட்டவணை IIIA" data-english="STATEMENT IIIA">STATEMENT IIIA</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="முந்தைய ஆண்டிலிருந்து கொண்டு வரப்பட்ட நிகர நட்டம்"
                                data-english="Net Loss Brought Forward From Previous Year">Net Loss Brought Forward
                                From Previous Year</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நடப்பாண்டு நிகர நட்டம்" data-english="Net loss of the Current Year">Net
                                loss of the Current Year</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="நடப்பாண்டு இலாபம் நட்டக்கணக்கிலிருந்து கொண்டு வரப்பட்ட நிகர இலாபம்"
                                data-english="Net Profit Brought Forward From Current Year P & L">Net Profit Brought
                                Forward From Current Year P & L</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td data-tamil="ஆண்டு இறுதியில் உள்ள நிகர நட்டம்"
                                data-english="Net Loss at the End of the Year">Net Loss at the End of the Year</td>
                            <td><input type="number" class="form-control"></td>
                            <td><input type="number" class="form-control"></td>
                        </tr>
                        <tr class="total-row">
                            <td></td>
                            <td data-tamil="மொத்தம்" data-english="TOTAL">TOTAL</td>
                            <td><input type="number" class="form-control" readonly></td>
                            <td><input type="number" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>

                <div class="notes-section">
                    <h4 data-english="Remarks" data-tamil="தணிக்கை குறிப்புகள்">Remarks</h4>
                    <textarea id="auditnotes" class="form-control" rows="6" data-english-placeholder="Enter audit notes here..."
                        data-tamil-placeholder="தணிக்கை குறிப்புகளை இங்கே..."></textarea>

                    <div class="mt-3">
                        <label class="form-label" data-english="Attachment" data-tamil="இணைப்பு">Attachment</label>
                        <input type="file" class="form-control" id="audit-attachment" multiple>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4 no-print">
                <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save</button>
                <button class="btn btn-success" id="print-btn"><i class="fas fa-print me-2"></i>Print</button>
            </div>

        </div>
    @endif
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Utility Functions ---
        function restrictNumberInput(el) {
            el.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').slice(0,
                    9); // max 9 digits, allows decimals
                if (parseFloat(this.value) < 0) this.value = '';
            });
        }

        function sumByIds(ids) {
            return ids.reduce((sum, id) => sum + (parseFloat(document.getElementById(id)?.value) || 0), 0);
        }

        function sumInputs(inputs) {
            return Array.from(inputs).reduce((sum, el) => sum + (parseFloat(el.value) || 0), 0);
        }

        // --- Dept 02: Template Calculations ---
        if ("{{ $sessionCharge->deptcode }}" === '01') {

            // --- Template Switcher ---
            const pathParts = window.location.pathname.split('/');
            const templateNum = pathParts.includes('templateaudit') && pathParts[pathParts.indexOf(
                'templateaudit') + 2] === '2' ? '2' : '1';
            document.getElementById('template1-content').style.display = templateNum === '1' ? 'block' : 'none';
            document.getElementById('template2-content').style.display = templateNum === '2' ? 'block' : 'none';

            // --- Template 1 ---
            const incomeInputs = document.querySelectorAll('.income-input');
            const expenseInputs = document.querySelectorAll('.expense-input');
            const openingBalanceInput = document.querySelector('.opening-balance');
            const closingBalanceInput = document.querySelector('.closing-balance');

            function calculateTemplate1Totals() {
                let totalIncome = sumInputs(incomeInputs);
                let totalExpense = sumInputs(expenseInputs);
                let openingBalance = parseFloat(openingBalanceInput.value) || 0;
                let finalTotal = totalIncome + openingBalance;

                document.getElementById('total-income').value = totalIncome.toFixed(2);
                document.getElementById('total-expense').value = totalExpense.toFixed(2);
                document.getElementById('final-total-1').value = finalTotal.toFixed(2);
                document.getElementById('final-total-2').value = finalTotal.toFixed(2);

                closingBalanceInput.value = Math.max(0, finalTotal - totalExpense).toFixed(2);
                closingBalanceInput.readOnly = true;
            }

            [...incomeInputs, ...expenseInputs, openingBalanceInput].forEach(inp => {
                inp.addEventListener('input', calculateTemplate1Totals);
                restrictNumberInput(inp);
            });

            // --- Template 2 ---
            function calculateTemplate2() {
                const BEl = document.getElementById('B');
                const EEl = document.getElementById('E');
                const CEl = document.getElementById('C');
                const FEl = document.getElementById('F');
                const DEl = document.getElementById('D');
                const closingEl = document.getElementById('closing_balance');
                const DplusEl = document.getElementById('Dplus');
                const DrightEl = document.getElementById('Dright');

                if (!BEl || !EEl || !CEl || !FEl || !DEl || !closingEl || !DplusEl || !DrightEl) return;

                let B = sumByIds(['E11', 'E12', 'E13', 'E14', 'E15', 'E16', 'E17']);
                let E = sumByIds(['E24', 'E25', 'E26', 'E27', 'E28', 'E29', 'E30', 'E31', 'E32', 'E33', 'E34']);
                let C = B + sumByIds(['E18', 'E19', 'E20', 'E21', 'E22']);
                let F = E + sumByIds(['E35', 'E36', 'E37', 'E38', 'E39', 'E40']);
                let D = C + (parseFloat(document.getElementById('E23').value) || 0);
                let closing = Math.max(0, D - F);

                BEl.value = B.toFixed(2);
                EEl.value = E.toFixed(2);
                CEl.value = C.toFixed(2);
                FEl.value = F.toFixed(2);
                DEl.value = D.toFixed(2);
                closingEl.value = closing.toFixed(2);
                DplusEl.value = D.toFixed(2);
                DrightEl.value = closing.toFixed(2);
            }

            document.querySelectorAll('#template2-content input[type="number"]').forEach(inp => {
                inp.addEventListener('input', calculateTemplate2);
                restrictNumberInput(inp);
            });

            if (templateNum === '1') calculateTemplate1Totals();
            else calculateTemplate2();

        }

        // --- Dept 04: Financial Table ---
        if ("{{ $sessionCharge->deptcode }}" === '04') {
            const financialTable = document.querySelector('.financial-table');
            if (financialTable) {

                function calculateFinancialTotals() {
                    const rows = financialTable.querySelectorAll('tbody tr');
                    let currentTotals = [];
                    rows.forEach(row => {
                        if (!row.classList.contains('total-row')) {
                            row.querySelectorAll('input[type="number"]').forEach((input, colIndex) => {
                                let val = parseFloat(input.value) || 0;
                                currentTotals[colIndex] = (currentTotals[colIndex] || 0) + val;
                            });
                        } else {
                            row.querySelectorAll('input[type="number"]').forEach((input, colIndex) => {
                                input.value = currentTotals[colIndex] ? currentTotals[colIndex]
                                    .toFixed(2) : '0.00';
                            });
                            currentTotals = [];
                        }
                    });
                }

                financialTable.querySelectorAll('input[type="number"]').forEach(input => {
                    restrictNumberInput(input);
                    input.addEventListener('input', calculateFinancialTotals);
                });

                calculateFinancialTotals();
            }
        }

        // --- CKEditor ---
        if (document.getElementById("audit-notes")) {
            CKEDITOR.ClassicEditor.create(document.getElementById("audit-notes"), {
                toolbar: {
                    items: ['findAndReplace', 'selectAll', '|', 'heading', '|', 'bold', 'italic',
                        'underline', '|', 'numberedList', '|', 'outdent', 'indent', '|', 'undo',
                        'redo', 'fontSize', 'fontFamily', '|', 'alignment', '|', 'uploadImage',
                        'insertTable', '|'
                    ],
                    shouldNotGroupWhenFull: true
                },
                fontFamily: {
                    options: ['default', 'Marutham', 'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace', 'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: [10, 12, 14, 'default', 18, 20, 22],
                    supportAllValues: true
                },
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                removePlugins: ['AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                    'MultiLevelList', 'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory',
                    'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                    'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand',
                    'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                    'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            }).catch(console.error);
        }
        if (document.getElementById("auditnotes")) {
            CKEDITOR.ClassicEditor.create(document.getElementById("auditnotes"), {
                toolbar: {
                    items: ['findAndReplace', 'selectAll', '|', 'heading', '|', 'bold', 'italic',
                        'underline', '|', 'numberedList', '|', 'outdent', 'indent', '|', 'undo',
                        'redo', 'fontSize', 'fontFamily', '|', 'alignment', '|', 'uploadImage',
                        'insertTable', '|'
                    ],
                    shouldNotGroupWhenFull: true
                },
                fontFamily: {
                    options: ['default', 'Marutham', 'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace', 'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: [10, 12, 14, 'default', 18, 20, 22],
                    supportAllValues: true
                },
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                removePlugins: ['AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                    'MultiLevelList', 'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory',
                    'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                    'RevisionHistory', 'Pagination', 'WProofreader', 'MathType', 'SlashCommand',
                    'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                    'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            }).catch(console.error);
        }

        // --- Print Button ---
        const printBtn = document.getElementById('print-btn');
        if (printBtn) printBtn.addEventListener('click', () => window.print());

    });
</script>


@endsection
