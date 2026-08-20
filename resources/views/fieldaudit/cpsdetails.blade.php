@section('content')
    @extends('index2')
    @include('common.alert')




    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">

    <style>
        .custom-accordion-btn {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            width: 20px;
            height: 20px;
        }




        .modal {
            z-index: 1055;
        }

        #confirmation_alert {
            z-index: 1075 !important;
        }

        .modal-backdrop.show:nth-of-type(2) {
            z-index: 1070;
        }


        .professional-table {
            width: 100%;
            border-collapse: collapse;
        }

        .professional-table th,
        .professional-table td {
            border: 1px solid #ccc8c8;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .professional-table thead th {
            background-color: #f2f4f7;
            color: #2c2c2c;
            font-weight: 600;

        }

        .professional-table tbody td:first-child {
            text-align: left;
        }

        .professional-table input,
        .professional-table select {
            padding: 4px 6px;
            box-sizing: border-box;
        }

        .is-invalid {
            border: 1px solid #fd1b13 !important;
            box-shadow: none !important;
        }

        .custom-modal-width {
            max-width: 1600px;
            width: 100%;
        }

        .accordion-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            flex-wrap: wrap;
        }

        /* Main title */
        .title-text {
            font-weight: 500;
            color: #1f2937;
        }

        /* Employee name */
        .emp-badge {
            background: #eaf2ff;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* CPS number */
        .cps-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            border: 1px solid #e2e8f0;
        }
    </style>

    <div class="container-fluid mt-3" id="cpsModal">
        <div class="card shadow-sm">

            <!-- Header -->
            <div class="card-header bg-primary text-center">
                <h5 class="mb-0 text-white">CPS Details</h5>
            </div>

            <!-- Body -->
            <div class="card-body mt-4">
                <input type="hidden" name="cps_completed" class="cps_completed">

                <form class="cpsForm" id="cpsForm">
                    @csrf
                    <input type="hidden" id="instid" name="instid" value="{{ $instid }}">
                    <input type="hidden" id="auditscheduleid" name="auditscheduleid" value="{{ $scheduleid }}">

                    <div id="cpsValidationAlert" class="alert alert-warning d-none mb-3">
                        Please fill all required fields before proceeding.
                    </div>

                    <div id="cpsSetsContainer">

                        <div class="accordion " id="cpsAccordion">
                            <div class="accordion-item" style="border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                <h2 class="accordion-header" id="headingCps1">
                                    <div class="d-flex align-items-center w-100 p-2 mt-2 mb-2">
                                        <!-- Left: Text -->
                                        <span style="font-size: 1rem; white-space: nowrap; flex-grow: 1;"
                                            class="accordion-title">
                                            CPS Set 1: Employee & Remittance Details
                                        </span>

                                        <!-- + Button -->
                                        <button type="button" class="btn btn-success btn-sm addRow fw-bold ms-2 ">
                                            Add Employee
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm deleteRow d-none fw-bold">
                                            -
                                        </button>

                                        <!-- Accordion toggle arrow -->
                                        <button class="accordion-button ms-2 custom-accordion-btn" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseCps1" aria-expanded="true"
                                            aria-controls="collapseCps1">
                                        </button>
                                    </div>
                                </h2>


                                <div id="collapseCps1" class="accordion-collapse collapse show"
                                    aria-labelledby="headingCps1" data-bs-parent="#cpsAccordion">
                                    <div class="accordion-body">

                                        <!-- Original CPS Set Content Starts -->
                                        <div class="cps-set border p-3 mb-4">

                                            <input type="hidden" name="transcpsid[1]" class="transcpsid">
                                            <input type="hidden" name="shortfallid[1]" class="shortfallid">


                                            <div class="table-responsive mb-4 text-end">

                                                <table class="table professional-table  text-center align-middle ">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th colspan="14" class="text-center  fs-6 "
                                                                style="font-family: 'Times New Roman', serif; font-weight: 600;">
                                                                i) Employees under CPS
                                                            </th>
                                                        </tr>


                                                        <tr>
                                                            <th>S.No</th>
                                                            {{-- <th class="required">Department Name</th> --}}
                                                            <th class="">Treasury / Sub Treasury</th>
                                                            <th class="">DDO Code</th>

                                                            {{-- <th class="">Year</th>
                                                        <th class="">Month</th> --}}
                                                            <th class="required">CPS No</th>



                                                            <th class="required">Name of Employee</th>
                                                            {{-- <th  class="required">DOB</th>
                                                        <th  class="required">DOJ</th> --}}
                                                            <th class="required">Designation</th>
                                                            <th class="required">Current Pay Scale</th>
                                                            <th class="required">Whether Employee contribution is deducted
                                                                Yes/No?(If no give details)</th>
                                                            <th class="required">Whether Employer's contribution is deducted
                                                                Yes/No?(If no give details)</th>
                                                            <th class="required">Is there any missing credits Yes/No?</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cpsTableBody">
                                                        <tr>

                                                            <td>
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center sNo"
                                                                    value="1" readonly>
                                                            </td>


                                                            {{-- <td>
                                                            <input type="text" style="width: 130px;"  class="form-control form-control-sm nbew_text limit-paste" placeholder="Departmrnt Name" data-required="true" name="department_name[1]" maxlength="50">
                                                        </td> --}}

                                                            <td>
                                                                <input type="text" style="width: 130px;"
                                                                    class="form-control form-control-sm nbew_text limit-paste"
                                                                    placeholder="Treasury / Sub Treasury"
                                                                    name="treasury_cps[1]" maxlength="100">
                                                            </td>

                                                            <td>
                                                                <input type="text" style="width: 130px;"
                                                                    class="form-control form-control-sm text-center nbew_number"
                                                                    placeholder="Enter DDO Code" name="ddocode_cps[1]"
                                                                    maxlength="8">
                                                            </td>




                                                            {{-- <td>
                                                            <!-- Year picker -->
                                                            <input type="text" style="width: 100px;" class="form-control form-control-sm text-center yearpicker"
                                                            placeholder="YYYY" name="cps_year[1]"
                                                            onclick="openDatePicker(this, 'year')">
                                                            </td>


                                                            <td>
                                                                <!-- Month dropdown -->
                                                                <select style="width: 100px;" class="form-control form-control-sm text-center" name="cps_month[1]">
                                                                    <option value="">Select Month</option>
                                                                    <option value="01">Jan</option>
                                                                    <option value="02">Feb</option>
                                                                    <option value="03">Mar</option>
                                                                    <option value="04">Apr</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">Jun</option>
                                                                    <option value="07">Jul</option>
                                                                    <option value="08">Aug</option>
                                                                    <option value="09">Sep</option>
                                                                    <option value="10">Oct</option>
                                                                    <option value="11">Nov</option>
                                                                    <option value="12">Dec</option>
                                                                </select>
                                                            </td>

 --}}



                                                            <td>
                                                                <input type="text" style="width: 90px;"
                                                                    class="form-control form-control-sm text-center cpsNo nbew_number"
                                                                    placeholder="CPS No" data-required="true"
                                                                    name="cpsNo[1]" maxlength="10" data-type="cpsno">
                                                            </td>




                                                            <td>
                                                                <input type="text" style="width: 90px;"
                                                                    class="form-control form-control-sm nbew_text limit-paste"
                                                                    placeholder="Name" data-required="true"
                                                                    name="name[1]" maxlength="50">
                                                            </td>




                                                            <td>
                                                                <input type="text"
                                                                    class="form-control form-control-sm nbew_text limit-paste"
                                                                    placeholder="Designation" data-required="true"
                                                                    name="designation[1]" maxlength="100">
                                                            </td>
                                                            <td>
                                                                <input type="text" style="width: 130px;"
                                                                    class="form-control form-control-sm nbew_number"
                                                                    placeholder="Pay Scale" data-required="true"
                                                                    name="payscale[1]" maxlength="6">
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input yes-no-radio"
                                                                        type="radio" name="q1[1]" value="Y"
                                                                        checked>
                                                                    <label class="form-check-label">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input yes-no-radio"
                                                                        type="radio" name="q1[1]" value="N">
                                                                    <label class="form-check-label">No</label>
                                                                </div>
                                                                <textarea class="form-control form-control-sm mt-1 d-none nbew_text" maxlength="255"
                                                                    placeholder="Please provide details" data-required="true" name="details1[1]"></textarea>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input yes-no-radio"
                                                                        type="radio" name="q2[1]" value="Y"
                                                                        checked>
                                                                    <label class="form-check-label">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input yes-no-radio"
                                                                        type="radio" name="q2[1]" value="N">
                                                                    <label class="form-check-label">No</label>
                                                                </div>
                                                                <textarea class="form-control form-control-sm mt-1 d-none nbew_text" maxlength="255"
                                                                    placeholder="Please provide details" data-required="true" name="details2[1]"></textarea>
                                                            </td>
                                                            <td class="text-center">
                                                                <div
                                                                    class="d-flex justify-content-center align-items-center gap-4">
                                                                    <label class="d-flex align-items-center gap-1 m-0">
                                                                        <input type="radio"
                                                                            class="form-check-input yes-no-radio m-0"
                                                                            name="score[1]" value="Y" checked>
                                                                        Yes
                                                                    </label>

                                                                    <label class="d-flex align-items-center gap-1 m-0">
                                                                        <input type="radio"
                                                                            class="form-check-input yes-no-radio m-0"
                                                                            name="score[1]" value="N">
                                                                        No
                                                                    </label>
                                                                </div>
                                                            </td>

                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <small class="text-danger d-none" id="rowLimitMsg">Maximum 5 rows
                                                    allowed</small>
                                            </div>




                                            <div class="table-responsive mt-4">
                                                <table class="table professional-table align-middle text-center">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="5" class="text-center  fs-6 "
                                                                style="font-family: 'Times New Roman', serif; font-weight: 600;">
                                                                ii) Information on Remittance
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">a)</td>

                                                            <!-- Label -->
                                                            <td class="text-start required"
                                                                style="white-space: nowrap;;width: 40%;">
                                                                Total Employee Contribution for the FY
                                                            </td>

                                                            <!-- Dropdown -->
                                                            <td class="text-start">
                                                                <select class="form-select form-select-sm  lang-dropdown"
                                                                    name="employeefinancialyear[1]" data-required="true">
                                                                    <option value="">Select Audit Year</option>
                                                                    @foreach ($financialyear as $year)
                                                                        <option value="{{ $year->auditperiodid }}">
                                                                            {{ $year->auditperiod }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            </td>

                                                            <td style="width:400px;">
                                                                <select class="select2forcps text-start"
                                                                    name="cps_month_employee[1]" multiple>

                                                                    <option value="01">Mar</option>
                                                                    <option value="02">Apr</option>
                                                                    <option value="03">May</option>
                                                                    <option value="04">Jun</option>
                                                                    <option value="05">Jul</option>
                                                                    <option value="06">Aug</option>
                                                                    <option value="07">Sep</option>
                                                                    <option value="08">Oct</option>
                                                                    <option value="09">Nov</option>
                                                                    <option value="10">Dec</option>
                                                                    <option value="11">Jan</option>
                                                                    <option value="12">Feb</option>
                                                                </select>
                                                            </td>




                                                            <!-- Amount Input -->
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm amount nbew_number_decimal"
                                                                        maxlength="12" name="employee_contri[1]"
                                                                        placeholder="0.00" data-required="true">
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">b)</td>

                                                            <td class="text-start required" style="white-space: nowrap;">
                                                                Total Employer Contribution for the FY</td>
                                                            <td class="text-start" style="width:400px;">

                                                                <select class="form-select form-select-sm  lang-dropdown"
                                                                    name="employerfinancialyear[1]" data-required="true">
                                                                    <option value="">Select Audit Year</option>
                                                                    @foreach ($financialyear as $year)
                                                                        <option value="{{ $year->auditperiodid }}">
                                                                            {{ $year->auditperiod }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>

                                                            </td>

                                                            <td style="width:400px;">
                                                                <select class="select2forcps" name="cps_month_employer[1]"
                                                                    multiple>
                                                                    <option value="" disabled>Select Month</option>


                                                                    <option value="01">Mar</option>
                                                                    <option value="02">Apr</option>
                                                                    <option value="03">May</option>
                                                                    <option value="04">Jun</option>
                                                                    <option value="05">Jul</option>
                                                                    <option value="06">Aug</option>
                                                                    <option value="07">Sep</option>
                                                                    <option value="08">Oct</option>
                                                                    <option value="09">Nov</option>
                                                                    <option value="10">Dec</option>
                                                                    <option value="11">Jan</option>
                                                                    <option value="12">Feb</option>
                                                                </select>
                                                            </td>

                                                            <td style="width: 500px;">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm amount nbew_number_decimal"
                                                                        maxlength="12" name="employer_contri[1]"
                                                                        placeholder="0.00" data-required="true">
                                                                </div>

                                                            </td>




                                                        </tr>
                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">c)</td>

                                                            <td class="text-start required" colspan="3">Total</td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm amount nbew_number_decimal"
                                                                        maxlength="12" name="total_cps[1]"
                                                                        placeholder="0.00" data-required="true" readonly>

                                                                </div>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">d)</td>

                                                            <td class="text-start required" colspan="3">Actual
                                                                remittance as per records</td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm amount nbew_number_decimal"
                                                                        name='actual_remittance[1]' maxlength="12"
                                                                        placeholder="0.00" data-required="true">
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">e)</td>

                                                            <td class="text-start required" colspan="3">Whether amount
                                                                is credited in appropriate Head of Accounts (Yes/No).If 'No'
                                                                Raise an audit slip and mention the slip No.</td>
                                                            <td>
                                                                <select
                                                                    class="form-select form-select-sm mb-1 credited-select"
                                                                    name="creditedin_appropriatehead[1]"
                                                                    data-required="true">
                                                                    <option value="">Select</option>
                                                                    <option value="Y">Yes</option>
                                                                    <option value="N">No</option>
                                                                </select>

                                                                <textarea class="form-control form-control-sm mt-1 d-none credited_remarks nbew_specialchartextandnumber"
                                                                    maxlength="11" data-required="true" placeholder="Please provide details" name="credited_remarks[1]"></textarea>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">f)</td>
                                                            <td class="text-start required" colspan="3">Shortfall in
                                                                Remittance</td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>
                                                                    <input type="text" maxlength="12"
                                                                        class="form-control form-control-sm nbew_specialchartextandnumber"
                                                                        name="shortfall_remittance[1]" placeholder="0.00"
                                                                        data-required="true" readonly>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start" style="width: 3%;">g)</td>
                                                            <td class="text-start required" colspan="3">Remittance in
                                                                Excess</td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">₹</span>

                                                                    <input type="text" maxlength="12"
                                                                        class="form-control form-control-sm amount"
                                                                        name="remittanceexcess_cps[1]"
                                                                        id="remittanceexcess_cps" placeholder="0.00"
                                                                        data-required="true" readonly>



                                                                </div>

                                                                <textarea class="form-control form-control-sm mt-1 d-none remittanceexcess_remarks nbew_specialchartextandnumber"
                                                                    maxlength="13" data-required="true" placeholder="Please provide details" name="remittanceexcess_remarks[1]"></textarea>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>




                                            <div id="shortfall_wrap[1]" class="d-none">
                                                <table class="table table-bordered shortfall-table professional-table">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th>Shortfall Month</th>
                                                            <th>Amount</th>
                                                            <th>Head of Account</th>
                                                            <th>Voucher Number</th>
                                                            <th>Treasury / Sub Treasury</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="sno">1</td>

                                                            {{-- <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-light">
                                                                    <i class="ti ti-calendar"></i>
                                                                </span>
                                                                <input
                                                                    type="text"
                                                                    class="form-control nbew_specialchartextandnumber"
                                                                    placeholder="Eg.june-2025" name="shortfallmonth[1]"
                                                                   maxlength="15">
                                                            </div>
                                                        </td> --}}

                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">
                                                                        <i class="ti ti-calendar"></i>
                                                                    </span>

                                                                    <select
                                                                        class="form-control form-control-sm shortfallMonth"
                                                                        name="shortfallmonth[1]">
                                                                        <option value="">Select Month</option>

                                                                    </select>
                                                                </div>
                                                            </td>





                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">
                                                                        <i class="ti ti-currency-rupee"></i>
                                                                    </span>
                                                                    <input type="text"
                                                                        class="form-control nbew_number_decimal shortfallamount"
                                                                        placeholder="0.00" maxlength="12"
                                                                        name="shortfallamount[1]">
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">
                                                                        <i class="ti ti-book"></i>
                                                                    </span>
                                                                    <input type="text"
                                                                        class="form-control nbew_specialchartextandnumber"
                                                                        placeholder="Head of Account" maxlength="16"
                                                                        name="shortfallfeadofaccount[1]">
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">
                                                                        <i class="ti ti-receipt"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control nbew_number"
                                                                        placeholder="Voucher No." name="shortfallvno[1]"
                                                                        maxlength="14">
                                                                </div>
                                                            </td>


                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-light">
                                                                        <i class="ti ti-building-bank"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control nbew_text"
                                                                        name="shortftreasury[1]"
                                                                        placeholder="Treasury / Sub Treasury"
                                                                        maxlength="100">
                                                                </div>
                                                            </td>



                                                            <td class="text-center">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success addshortfall-row">+</button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger removeshortfall-row d-none">−</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                    <tfoot>
                                                        <tr class="table-light fw-semibold">
                                                            <td colspan="2" class="text-end">Total Shortfall</td>
                                                            <td class="text-end">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="text" name="shortfall_total[1]"
                                                                        value="0.00" readonly
                                                                        class="form-control text-end shortfall_total">
                                                                </div>
                                                            </td>
                                                            <td colspan="4"></td>
                                                        </tr>

                                                    </tfoot>



                                                </table>
                                            </div>



                                        </div>






                                        <!-- Original CPS Set Content Ends -->

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- END SET -->

                    </div>

            </div>

            <!-- Footer Buttons -->
            <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap mb-4">

                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    Close
                </a>

                <button type="submit" id= "btnSaveDraft" class="btn button_save button_saveforcps">
                    <i class="bi bi-check-circle"></i> Save Draft
                </button>

                <button type="submit" id= "btnUpdateCps" class="btn btn-primary button_saveforcps d-none">
                    <i class="bi bi-check-circle"></i> Update
                </button>
                {{--
                    <button type="button" id="finalizeCpsBtn" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Finalize
                    </button> --}}

            </div>

            </form>

        </div>
    </div>
    </div>






    </div>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/apps/chat.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script> --}}
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>

    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/forms/select2.init.js') }}"></script>


    {{-- <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script> --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('#btnSaveDraft').addClass('d-none');

            function toggleCpsButtons() {
                const cpsCompleted = $('.cps_completed').val();

                if (cpsCompleted === 'Y') {
                    $('#btnUpdateCps').removeClass('d-none');

                } else {
                    $('#btnSaveDraft').addClass('d-none');
                }
            }

            setTimeout(toggleCpsButtons, 500);
            $('.cps_completed').on('change', toggleCpsButtons);
        });


        function openMonthYearPicker(el) {

            if (!$(el).data('datepicker')) {
                $(el).datepicker({
                    format: "MM-yyyy",
                    startView: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            }

            $(el).datepicker('show');
        }



        $(document).ready(function() {
            fetchCpsData();
            restrictSpecialChars('.nbew_text');
            restrictSpecialChars('.nbew_number');
            restrictSpecialChars('.nbew_number_decimal');
            restrictSpecialChars('.nbew_specialchartextandnumber');


        });




        // function initDatePicker(el, type) {

        // // Restrict manual typing
        // el.addEventListener('input', function () {
        //     this.value = this.value.replace(/[^0-9-]/g, '');
        //     if (this.value.length === 2 || this.value.length === 5) {
        //         if (!this.value.endsWith('-')) this.value += '-';
        //     }
        //     if (this.value.length > 10) this.value = this.value.slice(0, 10);
        // });

        // // 🔥 auto clear invalid dates like 14-06-45
        // el.addEventListener('blur', function () {
        //     validateDateValue(el, type);
        // });

        // let endDate = new Date();
        // let defaultViewDate = null;

        // if (type === 'dob') {
        //     const today = new Date();
        //     endDate = new Date(
        //         today.getFullYear() - 18,
        //         today.getMonth(),
        //         today.getDate()
        //     );
        //     defaultViewDate = endDate;
        // }

        // $(el)
        //     .datepicker('destroy')
        //     .datepicker({
        //         format: 'dd-mm-yyyy',
        //         autoclose: true,
        //         todayHighlight: true,
        //         startDate: '01-01-1900',
        //         endDate: endDate,
        //         defaultViewDate: defaultViewDate
        //     })
        //     .on('changeDate', function () {

        //         validateDateValue(el, type);

        //         el.dispatchEvent(new Event('input', { bubbles: true }));
        //         el.dispatchEvent(new Event('change', { bubbles: true }));

        //         el.classList.remove('is-invalid');
        //     })
        //     .datepicker('show');
        // }


        function validateDateValue(el, type) {
            const val = el.value;
            if (!val || val.length !== 10) return;

            const [dd, mm, yyyy] = val.split('-').map(Number);

            if (yyyy < 1900) {
                el.value = '';
                el.classList.add('is-invalid');
                return;
            }

            const date = new Date(yyyy, mm - 1, dd);

            if (
                date.getFullYear() !== yyyy ||
                date.getMonth() !== mm - 1 ||
                date.getDate() !== dd
            ) {
                el.value = '';
                return;
            }

            if (type === 'dob') {
                const today = new Date();
                const minDob = new Date(
                    today.getFullYear() - 18,
                    today.getMonth(),
                    today.getDate()
                );

                if (date > minDob) {
                    el.value = '';
                }
            }
        }



        //////////////////////////////////////////////////////////////


        $(document).on(
            'input paste',
            'input[name^="employee_contri"], input[name^="employer_contri"]',
            function() {

                const match = this.name.match(/\[(\d+)\]/);
                if (!match) return;

                calculateTotal(match[1]);
            }
        );


        function cleanNumber(val) {
            return parseFloat(
                (val || '').toString().replace(/[^0-9.]/g, '')
            ) || 0;
        }


        function calculateTotal(index) {


            const employee = cleanNumber(
                $(`input[name="employee_contri[${index}]"]`).val()
            );

            const employer = cleanNumber(
                $(`input[name="employer_contri[${index}]"]`).val()
            );

            const total = employee + employer;

            const $output = $(`input[name="total_cps[${index}]"]`);
            if ($output.length) {
                $output.val(total.toFixed(2));
            }

            calculateShortfall(index);
            calculateExcess(index);

        }


        function calculateShortfall(index) {
            const total = cleanNumber($(`input[name="total_cps[${index}]"]`).val());
            const actual = cleanNumber($(`input[name="actual_remittance[${index}]"]`).val());
            //const shortfall = total - actual;
            const shortfall = Math.max(0, total - actual);
            const $shortfallInput = $(`input[name="shortfall_remittance[${index}]"]`);
            const $wrap = $(`#shortfall_wrap\\[${index}\\]`);

            $shortfallInput.val(shortfall.toFixed(2));

            if (shortfall > 0) {
                $wrap.removeClass('d-none');
                $wrap
                    .find('input, textarea, select')
                    .attr('data-required', 'true');
            } else {
                $wrap.addClass('d-none');
                $wrap
                    .find('input, textarea, select')
                    .removeAttr('data-required')
                    .removeClass('is-invalid')
                    .val('');
            }
        }



        $(document).on(
            'input paste',
            'input[name^="total_cps"], input[name^="actual_remittance"]',
            function() {

                const match = this.name.match(/\[(\d+)\]/);
                if (!match) return;

                calculateShortfall(match[1]);
            }
        );

        function calculateExcess(index) {
            const total = cleanNumber(
                $(`input[name="total_cps[${index}]"]`).val()
            );

            const actual = cleanNumber(
                $(`input[name="actual_remittance[${index}]"]`).val()
            );

            let excess = 0;

            if (actual > total) {
                excess = actual - total;
            }

            const $excessInput = $(
                `input[name="remittanceexcess_cps[${index}]"]`
            );

            const $remarks = $(
                `textarea[name="remittanceexcess_remarks[${index}]"]`
            );

            $excessInput.val(excess.toFixed(2));

            /* 🔹 SHOW / HIDE REMARKS */
            if (excess > 0) {
                $remarks.removeClass('d-none');

            } else {
                $remarks.addClass('d-none');
                $remarks.val('');

            }
        }

        $(document).on(
            'input paste',
            'input[name^="actual_remittance"], input[name^="total_cps"]',
            function() {

                const index = $(this)
                    .attr('name')
                    .match(/\[(\d+)\]/)?.[1];

                if (index !== undefined) {
                    calculateExcess(index);
                }
            }
        );



        ////////////////////////////////////////



        $(document).on('click', '.addshortfall-row', function() {

            const $tbody = $(this).closest('table').find('tbody');
            const $lastRow = $tbody.find('tr:last');
            const $newRow = $lastRow.clone();

            // clear input values
            $newRow.find('input').val('');

            // ✅ show remove button ONLY for new row
            $newRow.find('.removeshortfall-row').removeClass('d-none');

            $tbody.append($newRow);

            // reapply restrictions
            restrictSpecialChars('.nbew_text');
            restrictSpecialChars('.nbew_number');
            restrictSpecialChars('.nbew_number_decimal');
            restrictSpecialChars('.nbew_specialchartextandnumber');

            // update S.No
            $tbody.find('tr').each(function(i) {
                $(this).find('.sno').text(i + 1);
            });
        });


        $(document).on('click', '.removeshortfall-row', function() {

            const $table = $(this).closest('table');
            const $tbody = $table.find('tbody');

            if ($tbody.find('tr').length === 1) return; // keep at least one row

            $(this).closest('tr').remove();

            $tbody.find('tr').each(function(i) {
                $(this).find('.sno').text(i + 1);
            });

            calculateShortfallFooter($table);
        });


        function calculateShortfallFooter($table) {
            let total = 0;

            // Sum row-wise shortfall values
            $table.find('tbody tr').each(function() {
                const val = cleanNumber(
                    $(this).find('input.shortfallamount').val() || 0
                );
                total += val;
            });

            total = total.toFixed(2);

            $table.find('tfoot input.shortfall_total').val(total);

        }


        // Trigger calculation on input or paste
        $(document).on('input paste', '.shortfallamount', function() {
            const $table = $(this).closest('table');
            calculateShortfallFooter($table);
        });

        $(document).on('keydown', 'input[readonly], textarea[readonly]', function(e) {
            const key = (e.key || '').toLowerCase();
            const isPasteShortcut =
                ((e.ctrlKey || e.metaKey) && key === 'v') ||
                (e.shiftKey && key === 'insert');

            if (isPasteShortcut) {
                e.preventDefault();
                return false;
            }
        });

        $(document).on('beforeinput paste drop', 'input[readonly], textarea[readonly]', function(e) {
            const originalEvent = e.originalEvent;

            if (e.type === 'beforeinput') {
                const inputType = originalEvent?.inputType || '';
                if (inputType !== 'insertFromPaste' && inputType !== 'insertFromDrop') {
                    return;
                }
            }

            e.preventDefault();
            return false;
        });




        ///////////////////////////////////////////////////


        function fillCpsSet(setNo, data) {
            document.querySelector('.cps_completed').value = data.cps_completed || '';



            function formatDateDMY(dateStr) {
                if (!dateStr) return '';

                const parts = dateStr.split('-'); // yyyy-mm-dd
                if (parts.length !== 3) return '';

                return `${parts[2]}-${parts[1]}-${parts[0]}`; // dd-mm-yyyy
            }


            const set = document.querySelectorAll('.cps-set')[setNo - 1];
            if (!set) return;




            if (!data ||
                (Array.isArray(data) && data.length === 0) ||
                data.cps_completed !== 'Y' || data.exitmeetdate !== null) {

                document
                    .querySelectorAll(
                        '.ddoandtreasury input, .ddoandtreasury textarea, .ddoandtreasury select, .ddoandtreasury button')
                    .forEach(el => el.disabled = true);

                set
                    .querySelectorAll('input, textarea, select, button')
                    .forEach(el => el.disabled = true);

                ['.addRow', '.deleteRow'].forEach(selector => {
                    document.querySelectorAll(selector).forEach(btn => {
                        btn.style.display = 'none';
                    });
                });



                const updateBtn = document.getElementById('btnUpdateCps');
                if (updateBtn) updateBtn.disabled = true;

                const finalizeBtn = document.getElementById('finalizeCpsBtn');
                if (finalizeBtn) {
                    finalizeBtn.disabled = true;
                    finalizeBtn.style.pointerEvents = 'none';
                    finalizeBtn.style.opacity = '0.6';
                }
            }


            let cpsEmployeeMonths = [];
            let cpsEmployerMonths = [];

            try {
                cpsEmployeeMonths = data.cps_month_employee ? JSON.parse(data.cps_month_employee) : [];
            } catch (e) {
                cpsEmployeeMonths = [];
                console.warn('Invalid JSON for employee months:', data.cps_month_employee);
            }

            try {
                cpsEmployerMonths = data.cps_month_employer ? JSON.parse(data.cps_month_employer) : [];
            } catch (e) {
                cpsEmployerMonths = [];
                console.warn('Invalid JSON for employer months:', data.cps_month_employer);
            }



            // Employee months
            const empSelect = set.querySelector(`[name="cps_month_employee[${setNo}]"]`);
            if (empSelect) {
                $(empSelect).val(cpsEmployeeMonths).trigger('change');
            }

            // Employer months
            const empSelect2 = set.querySelector(`[name="cps_month_employer[${setNo}]"]`);
            if (empSelect2) {
                $(empSelect2).val(cpsEmployerMonths).trigger('change');
            }




            set.querySelector('.transcpsid').value = data.transcpsid || '';



            // set.querySelector(`[name="department_name[${setNo}]"]`).value = data.department_name || '';
            set.querySelector(`[name="treasury_cps[${setNo}]"]`).value = data.treasury_cps || '';
            set.querySelector(`[name="ddocode_cps[${setNo}]"]`).value = data.ddocode_cps || '';

            set.querySelector(`[name="cpsNo[${setNo}]"]`).value = data.cpsno || '';

            // set.querySelector(`[name="cps_year[${setNo}]"]`).value = data.cps_year || '';
            // set.querySelector(`[name="cps_month[${setNo}]"]`).value = data.cps_month || '';


            set.querySelector(`[name="name[${setNo}]"]`).value = data.emp_name || '';

            const empName = data.emp_name;
            const cpsNo = data.cpsno;

            const accordionItem = set.closest('.accordion-item');


            if (accordionItem) {
                accordionItem.dataset.empName = empName;
                accordionItem.dataset.cpsNo = cpsNo;

                const titleEl = accordionItem.querySelector('.accordion-title');
                if (titleEl) {
                    titleEl.innerHTML = `
    <span class="title-text">
        CPS Set ${setNo}: Employee & Remittance Details
    </span>
    ${empName ? `<span class="emp-badge">${empName}</span>` : ''}
    ${cpsNo ? `<span class="cps-badge">CPS No: ${cpsNo}</span>` : ''}
`;

                }
            }

            set.querySelector(`[name="designation[${setNo}]"]`).value = data.designation || '';
            set.querySelector(`[name="payscale[${setNo}]"]`).value = data.payscale || '';
            // set.querySelector(`[name="departmentname[${setNo}]"]`).value = data.departmentname_cps || '';

            const scoreValue = data.missing_credits || 'Y';
            const scoreRadio = set.querySelector(`input[name="score[${setNo}]"][value="${scoreValue}"]`);
            if (scoreRadio) scoreRadio.checked = true;

            const q1Value = data.employee_cont || 'Y';
            const q2Value = data.employer_cont || 'Y';

            set.querySelector(`input[name="q1[${setNo}]"][value="${q1Value}"]`)?.click();
            set.querySelector(`input[name="q2[${setNo}]"][value="${q2Value}"]`)?.click();

            const details1 = set.querySelector(`[name="details1[${setNo}]"]`);
            const details2 = set.querySelector(`[name="details2[${setNo}]"]`);


            if (q1Value === 'N') {
                details1.value = data.employee_remarks || '';
                details1.classList.remove('d-none');
            } else {
                details1.value = '';
                details1.classList.add('d-none');
            }

            if (q2Value === 'N') {
                details2.value = data.employer_remarks || '';
                details2.classList.remove('d-none');
            } else {
                details2.value = '';
                details2.classList.add('d-none');
            }

            set.querySelector(
                `[name="employeefinancialyear[${setNo}]"]`
            ).value = String(data.employeefinancialyear);

            set.querySelector(
                `[name="employerfinancialyear[${setNo}]"]`
            ).value = String(data.employerfinancialyear);


            // Remittance fields

            set.querySelector(`[name="employee_contri[${setNo}]"]`).value = data.total_employee_cont || '';
            set.querySelector(`[name="employer_contri[${setNo}]"]`).value = data.total_employer_cont || '';
            set.querySelector(`[name="total_cps[${setNo}]"]`).value = data.total_cps || '';
            set.querySelector(`[name="actual_remittance[${setNo}]"]`).value = data.actual_remittance || '';

            const creditedSelect = set.querySelector(`[name="creditedin_appropriatehead[${setNo}]"]`);
            const creditedRemarks = set.querySelector(`[name="credited_remarks[${setNo}]"]`);

          if (data.creditedin_appropriatehead === 'N') {
        creditedSelect.value = 'N';
        creditedRemarks.value = data.approproatehead_remarks || '';
        creditedRemarks.classList.remove('d-none');

    } else {
        creditedSelect.value = 'Y';
        creditedRemarks.value = '';
        creditedRemarks.classList.add('d-none');

    }

            const shortfallValue = parseFloat(data.shortfall_remittance || 0);
            const excessInput = set.querySelector(`[name="remittanceexcess_cps[${setNo}]"]`);
            const remarksTextarea = set.querySelector(`[name="remittanceexcess_remarks[${setNo}]"]`);

            set.querySelector(`[name="shortfall_remittance[${setNo}]"]`).value = data.shortfall_remittance || '';
            set.querySelector(`[name="remittanceexcess_cps[${setNo}]"]`).value = data.remittanceexcess_cps || '';

            excessInput.value = data.remittanceexcess_cps || '';

            // Show/hide textarea based on excess > 0
            if (parseFloat(excessInput.value) > 0) {
                remarksTextarea.classList.remove('d-none');
                remarksTextarea.value = data.remittanceexcess_remarks || '';
            } else {
                remarksTextarea.classList.add('d-none');
                remarksTextarea.value = '';
            }


            if (shortfallValue > 0) {

                populateShortfalls(setNo, data.shortfalls, data.cps_completed);
                const $table = $(`#shortfall_wrap\\[${setNo}\\] table`);
                calculateShortfallFooter($table);
            }

        }

        $(document).on('change', '.shortfallMonth', function() {

            const current = $(this);
            const currentVal = current.val();
            if (!currentVal) return;

            const match = current.attr('name').match(/\[(\d+)\]/);
            if (!match) return;

            const setNo = match[1];

            const allSelects = $(`#shortfall_wrap\\[${setNo}\\] .shortfallMonth`);

            let duplicate = false;

            allSelects.each(function() {
                if (this !== current[0] && $(this).val() === currentVal) {
                    duplicate = true;
                    return false;
                }
            });

            if (duplicate) {
                alert('This month is already selected.');
                current.val('');
            }
        });



        function formatMonthYear(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const options = {
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-US', options).replace(' ', '-');
        }


        function populateShortfalls(setNo, shortfalls = [], cps_completed) {
            const shortfallContainer = document.querySelector(`#shortfall_wrap\\[${setNo}\\] tbody`);
            if (!shortfallContainer) return;

            const shortfallWrap = document.querySelector(`#shortfall_wrap\\[${setNo}\\]`);
            if (shortfallWrap) shortfallWrap.classList.remove('d-none');

            // Clear existing rows
            shortfallContainer.innerHTML = '';

            if (Array.isArray(shortfalls) && shortfalls.length > 0) {
                shortfalls.forEach((sf, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td class="sno">${index + 1}</td>

                <td>
                    <input type="hidden" name="shortfallid[${setNo}]" value="${sf.shortfallid || ''}">


                      <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">
                        <i class="ti ti-calendar"></i>
                    </span>

                       <select class="form-control form-control-sm shortfallMonth"
                        name="shortfallmonth[${setNo}]">
                        <option value="">Select Month</option>
                    </select>
                </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-currency-rupee"></i>
                        </span>
                        <input type="text" class="form-control nbew_number_decimal shortfallamount"
                            name="shortfallamount[${setNo}]" value="${sf.shortfall_amount || 0}"
                            placeholder="0.00" maxlength="12" >
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-book"></i>
                        </span>
                        <input type="text" class="form-control nbew_specialchartextandnumber"
                            name="shortfallfeadofaccount[${setNo}]" value="${sf.shortfall_headofaccount || ''}"
                            placeholder="Head of Account" maxlength="16" >
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-receipt"></i>
                        </span>
                        <input type="text" class="form-control nbew_number"
                            name="shortfallvno[${setNo}]" value="${sf.shortfall_voucher || ''}"
                            placeholder="Voucher No." maxlength="14">
                    </div>
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">
                            <i class="ti ti-building-bank"></i>
                        </span>
                        <input type="text" class="form-control nbew_text"
                            name="shortftreasury[${setNo}]" value="${sf.shortfall_treasury || ''}"
                            placeholder="Treasury / Sub Treasury" maxlength="100" >
                    </div>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success addshortfall-row">+</button>
                    <button type="button" class="btn btn-sm btn-danger removeshortfall-row ${index === 0 ? 'd-none' : ''}">−</button>
                </td>
            `;
                    shortfallContainer.appendChild(row);

                    const empSelect = document.querySelector(`[name="cps_month_employee[${setNo}]"]`);
                    const selectedMonths = $(empSelect).val() || [];

                    const shortfallSelect = row.querySelector('.shortfallMonth');

                    // Month name map
                    const monthMap = {
                        "01": "Mar",
                        "02": "Apr",
                        "03": "May",
                        "04": "Jun",
                        "05": "Jul",
                        "06": "Aug",
                        "07": "Sep",
                        "08": "Oct",
                        "09": "Nov",
                        "10": "Dec",
                        "11": "Jan",
                        "12": "Feb",
                    };

                    // Populate only selected employee months
                    selectedMonths.forEach(m => {
                        const option = document.createElement('option');
                        option.value = m;
                        option.textContent = monthMap[m];
                        if (sf.shortfall_month === m) {
                            option.selected = true;
                        }
                        shortfallSelect.appendChild(option);
                    });



                    if (cps_completed !== 'Y') {
                        row.querySelectorAll('input, select, textarea, button').forEach(el => {
                            el.disabled = true;
                            if (el.classList.contains('addshortfall-row') || el.classList.contains(
                                    'removeshortfall-row')) {
                                el.style.display = 'none';
                            }
                        });
                    }


                });
            } else {
                document.querySelector(`#shortfall_wrap\\[${setNo}\\] .addshortfall-row`).click();
            }

            if (shortfallWrap) {
                applyRestrictions(shortfallWrap);
            }

            // Reapply S.No for all rows
            shortfallContainer.querySelectorAll('tr').forEach((tr, idx) => {
                tr.querySelector('.sno').textContent = idx + 1;
            });
        }



        ///////////////////////reset accordian/////////////////

        // function resetCpsAccordions() {
        //     const container = document.getElementById('cpsSetsContainer');

        //     // keep only first accordion-item
        //     container.querySelectorAll('.accordion-item').forEach((item, index) => {
        //         if (index !== 0) item.remove();
        //     });

        //     // reset counter
        //     cpsSetCounter = 1;

        //     // reset first set inputs
        //     const firstSet = container.querySelector('.cps-set');
        //     firstSet.querySelectorAll('input, textarea, select').forEach(el => {
        //         if (el.type === 'radio') el.checked = false;
        //         else el.value = '';
        //         el.disabled = false;
        //     });
        // }

        function resetCpsAccordions() {
            const container = document.getElementById('cpsSetsContainer');
            if (!container) return;

            // ✅ Remove all accordion-items except the first
            const items = container.querySelectorAll('.accordion-item');
            items.forEach((item, index) => {
                if (index !== 0) item.remove();
            });

            // ✅ Reset counters
            cpsSetCounter = 1;
            cpsCounter = 1;
            fieldCounter = 1;

            // ✅ Reset inputs inside first CPS set ONLY
            const firstSet = container.querySelector('.accordion-item .cps-set');
            if (firstSet) {
                firstSet.querySelectorAll('input, select, textarea').forEach(el => {
                    if (el.type === 'radio') {
                        el.checked = false;
                        // Set first radio of group as default checked
                        const name = el.name;
                        const firstRadio = firstSet.querySelector(`input[name="${name}"]`);
                        if (firstRadio) firstRadio.checked = true;
                    } else {
                        el.value = '';
                    }

                    el.classList.remove('is-invalid');

                    if (el.tagName === 'TEXTAREA') {
                        el.classList.add('d-none');
                    }

                    el.disabled = false;
                });

                const sNoInput = firstSet.querySelector('.sNo');
                if (sNoInput) sNoInput.value = '1';


            }


        }

        //////////////////////////////////////

        function fetchCpsData() {
            // alert(cpsSetCounter);
            var auditscheduleid = $('#auditscheduleid').val();
            var instid = $('#instid').val();

            $.ajax({
                url: "/cps/fetch",
                type: "post",
                data: {
                    instid: instid,
                    auditscheduleid: auditscheduleid,
                    _token: $('meta[name="csrf-token"]').attr('content')

                },
                success: function(response) {

                    resetCpsAccordions();
                    let firstRow = Array.isArray(response) ? response[0] : response;

                    if (firstRow && firstRow.cps_completed === "Y") {
                        $('#btnSaveDraft').addClass('d-none');
                        $('#btnUpdateCps').removeClass('d-none');
                    } else {
                        $('#btnSaveDraft').removeClass('d-none');
                        $('#btnUpdateCps').addClass('d-none');
                    }

                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach((row, index) => {
                            const setNo = index + 1;
                            if (index === 0) {
                                fillCpsSet(1, row);
                            } else {

                                addCpsSet('Y');
                                fillCpsSet(setNo, row);
                            }
                        });
                    } else {

                        fillCpsSet(1, response);
                    }
                }
            });
        }


        $(document).on('input change', '#cpsModal input, #cpsModal textarea, #cpsModal select', function() {
            $('#cpsValidationAlert').addClass('d-none');
        });


        document.addEventListener('show.bs.modal', function(event) {
            const modal = event.target;

            // if confirmation alert is opening AND any modal is open
            if (modal.classList.contains('popupalertforcps') && document.body.classList.contains('modal-open')) {
                const cpsModal = document.querySelector('.cpsModal'); // select by class
                if (cpsModal) {
                    cpsModal.appendChild(modal);
                }
            }
        });


        /////////////////////////////////////close//////////////////////////////

        function resetAndCloseCpsModal() {
            const modalEl = document.getElementById('cpsModal');
            // const modalInstance = bootstrap.Modal.getInstance(modalEl);

            if (modalInstance) {
                // Reset AFTER modal is hidden
                modalEl.addEventListener('hidden.bs.modal', function() {

                    // Remove extra CPS sets
                    $('#cpsSetsContainer .accordion-item').not(':first').remove();
                    $('#cpsValidationAlert').addClass('d-none');

                    const $firstSet = $('#cpsSetsContainer .cps-set:first');
                    $('#ddocode_cps').val('').removeClass('is-invalid');
                    $('#treasury_cps').val('').removeClass('is-invalid');

                    // Always hide and reset shortfall UI so stale unsaved rows do not reappear on reopen
                    $firstSet.find('div[id^="shortfall_wrap"]').each(function() {
                        const $wrap = $(this);
                        const $tbody = $wrap.find('tbody');
                        const $rows = $tbody.find('tr');

                        if ($rows.length > 1) {
                            $rows.not(':first').remove();
                        }

                        const $firstRow = $tbody.find('tr:first');
                        $firstRow.find('input').val('').removeClass('is-invalid');
                        $firstRow.find('.removeshortfall-row').addClass('d-none');
                        $firstRow.find('.sno').text('1');

                        $wrap.find('tfoot input.shortfall_total').val('');
                        $wrap
                            .addClass('d-none')
                            .find('input, textarea, select')
                            .removeAttr('data-required')
                            .removeClass('is-invalid');
                    });

                    // Reset all inputs
                    $firstSet.find('input, select, textarea').each(function() {
                        const $el = $(this);

                        if ($el.is(':radio')) {
                            const name = $el.attr('name');
                            $(`input[name="${name}"]`).prop('checked', false);
                            $(`input[name="${name}"]:first`).prop('checked', true);
                        } else {
                            $el.val('');
                        }

                        $el.removeClass('is-invalid');

                        if ($el.is('textarea')) {
                            $el.addClass('d-none');
                        }
                    });

                    $firstSet.find('.sNo').val('1');

                    cpsCounter = 1;
                    fieldCounter = 1;
                    cpsSetCounter = 1;

                    $('#rowLimitMsg').addClass('d-none');

                    restrictSpecialChars('.nbew_text');
                    restrictSpecialChars('.nbew_number');
                    restrictSpecialChars('.nbew_number_decimal');
                    restrictSpecialChars('.nbew_specialchartextandnumber');

                    fetchCpsData();

                }, {
                    once: true
                });

                modalInstance.hide();
            }
        }

        $('#closeCpsModal').on('click', resetAndCloseCpsModal);



        function saveCps(callback) {
            if (!validateCpsTable()) {
                $('#cpsValidationAlert').removeClass('d-none');
                document.querySelector('#cpsModal .modal-body').scrollTop = 0;
                if (callback) callback(false);
                return;
            }

            var auditscheduleid = $('#auditscheduleid').val();
            var instid = $('#instid').val();

            const cpsModalEl = document.getElementById('cpsModal');
            const cpsModal = bootstrap.Modal.getInstance(cpsModalEl);

            var form = document.getElementById('cpsForm');
            var formData = new FormData(form);

            // Collect employee data
            var employees = [];
            $('#cpsSetsContainer .cps-set').each(function(index, el) {
                var $el = $(el);
                var employeeData = {
                    transcpsid: $el.find('[name^="transcpsid"]').val(),

                    // department_name: $el.find('[name^="department_name"]').val(),
                    treasury_cps: $el.find('[name^="treasury_cps"]').val(),
                    ddocode_cps: $el.find('[name^="ddocode_cps"]').val(),
                    cpsNo: $el.find('[name^="cpsNo"]').val(),

                    cps_month_employer: $el.find('[name^="cps_month_employer"]').val(),
                    cps_month_employee: $el.find('[name^="cps_month_employee"]').val(),


                    name: $el.find('[name^="name"]').val(),
                    dob: $el.find('[name^="dob_cps"]').val(),
                    doj: $el.find('[name^="doj_cps"]').val(),
                    designation: $el.find('[name^="designation"]').val(),
                    payscale: $el.find('[name^="payscale"]').val(),
                    q1: $el.find('[name^="q1"]:checked').val(),
                    details1: $el.find('[name^="details1"]').val(),
                    q2: $el.find('[name^="q2"]:checked').val(),
                    details2: $el.find('[name^="details2"]').val(),
                    score: $el.find('[name^="score"]:checked').val(),


                    employee: parseFloat($el.find('[name^="employee_contri"]').val()) || 0,
                    employer: parseFloat($el.find('[name^="employer_contri"]').val()) || 0,

                    employeefinancialyear: $el.find('[name^="employeefinancialyear"]').val() || 0,
                    employerfinancialyear: $el.find('[name^="employerfinancialyear"]').val() || 0,



                    total: parseFloat($el.find('[name^="total_cps"]').val()) || 0,
                    actual: parseFloat($el.find('[name^="actual_remittance"]').val()) || 0,
                    credited: $el.find('[name^="creditedin_appropriatehead"]').val(),
                    credited_remarks: $el.find('[name^="credited_remarks"]').val(),
                    shortfall: parseFloat($el.find('[name^="shortfall_remittance"]').val()) || 0,
                    excess: parseFloat($el.find('[name^="remittanceexcess_cps"]').val()) || 0,
                    remittanceexcess_remarks: $el.find('[name^="remittanceexcess_remarks"]').val(),


                };

                var $table = $el.find('div[id^="shortfall_wrap"] table');
                var totalShortfall = parseFloat($table.find('tfoot input[name^="shortfall_total"]').val()) || 0;

                var shortfalls = [];

                $table.find('tbody tr').each(function() {
                    var $row = $(this);

                    var month = $row.find('[name^="shortfallmonth"]').val();
                    var amount = $row.find('[name^="shortfallamount"]').val();

                    if (!month || amount === '' || amount === null) return;

                    if (isNaN(amount)) amount = 0;

                    shortfalls.push({
                        shortfall_month: month, // text like "june-2025"
                        shortfall_amount: parseFloat(amount),
                        shortfallid: $row.find('[name^="shortfallid"]').val() || null,
                        shortfall_headofaccount: $row.find('[name^="shortfallfeadofaccount"]')
                            .val() || null,
                        shortfall_voucher: $row.find('[name^="shortfallvno"]').val() || null,
                        shortfall_treasury: $row.find('[name^="shortftreasury"]').val() || null,
                        shortfall_total: totalShortfall // add the total in each row

                    });
                });

                employeeData.shortfalls = shortfalls;
                employees.push(employeeData);



            });

            var payload = {
                instid: instid,
                auditscheduleid: auditscheduleid,
                employees: employees
            };

            $.ajax({
                url: '/cpsinsert',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#btnSaveDraft, #btnUpdateCps, #finalizeCpsBtn').prop('disabled', true);
                },
                success: function(response) {
                    $('#btnSaveDraft, #btnUpdateCps, #finalizeCpsBtn').prop('disabled', false);
                    if (callback) callback(true, response);
                },
                error: function(xhr, status, error) {
                    //  cpsModal.hide();

                    $('#btnSaveDraft').prop('disabled', false);
                    $('#btnUpdateCps').prop('disabled', false);
                    $('#finalizeCpsBtn').prop('disabled', false);

                    let message = 'Something went wrong. Please try again.';

                    if (xhr.status === 422 && xhr.responseJSON.errors) { // Validation error
                        let errors = xhr.responseJSON.errors;
                        let messages = [];

                        $.each(errors, function(field, msgs) {
                            messages = messages.concat(msgs);
                        });

                        message = messages.join('<br>'); // Use <br> for multiple errors
                    }

                    setTimeout(function() {
                        passing_alert_value(
                            'Error',
                            message,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }, 350);

                    $("#ok_button").off('click').on('click', function() {

                        // cpsModal.show();
                    });
                    console.error(xhr.responseText);
                }
            });
        }



        $(document).on('submit', '#cpsForm', function(e) {
            e.preventDefault();

            if (!validateCpsTable()) {

                const alertBox = document.getElementById('cpsValidationAlert');

                // show alert
                alertBox.classList.remove('d-none');

                // scroll modal body to top so user sees it
                const modalBody = document.querySelector('#cpsModal .modal-body');
                modalBody.scrollTop = 0;

                return;
            }



            const cpsModalEl = document.getElementById('cpsModal');
            const cpsModal = bootstrap.Modal.getInstance(cpsModalEl);
            saveCps(function(success) {
                if (success) {
                    //   resetCpsAccordions();

                    // if (cpsModal) cpsModal.hide();

                    setTimeout(function() {
                        passing_alert_value(
                            'Confirmation',
                            'CPS Form Submitted / Updated Successfully',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }, 350);


                    fetchCpsData();

                } else {

                    let message = 'Something went wrong. Please try again.';
                    //  if (cpsModal) cpsModal.hide();
                    if (arguments.length > 1) {
                        const xhr = arguments[1];

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let messages = [];

                            $.each(xhr.responseJSON.errors, function(field, msgs) {
                                messages = messages.concat(msgs);
                            });

                            message = messages.join('<br>');
                        }
                    }

                    setTimeout(function() {
                        passing_alert_value(
                            'Error',
                            message,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }, 350);

                    $("#ok_button").off('click').on('click', function() {

                        // cpsModal.show();
                    });
                }
            });
        });


        $(document).on('click', '#finalizeCpsBtn', function() {

            if (!validateCpsTable()) {

                const alertBox = document.getElementById('cpsValidationAlert');

                // show alert
                alertBox.classList.remove('d-none');

                // scroll modal body to top so user sees it
                const modalBody = document.querySelector('#cpsModal .modal-body');
                modalBody.scrollTop = 0;

                return;
            }

            $('#finalizeCpsBtn').prop('disabled', true);



            $('#confirmation_alert #close_button').hide();

            passing_alert_value(
                'Alert',
                'Are you sure you want to finalize the CPS details?',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            removeAllEventListeners(document.getElementById("process_button"));


            $("#cancel_button").off('click').on('click', function() {

                $('#finalizeCpsBtn').prop('disabled', false);

            });

            $("#process_button").off('click.finalizeCps').on('click.finalizeCps', function() {

                saveCps(function(success) {
                    if (!success) return;

                    var auditscheduleid = $('#auditscheduleid').val();
                    var instid = $('#instid').val();
                    $.ajax({
                        url: '/finalize_cps',
                        method: 'POST',
                        data: {
                            instid: instid,
                            auditscheduleid: auditscheduleid,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {

                                setTimeout(function() {

                                    passing_alert_value(
                                        'Confirmation',
                                        'CPS details Finalized Successfully',
                                        'confirmation_alert',
                                        'alert_header',
                                        'alert_body',
                                        'confirmation_alert'
                                    );

                                }, 350);


                                $(document).on('click', '#ok_button', function() {
                                    location.reload();
                                });


                            } else {
                                alert('Failed to finalize CPS details.');
                            }
                        },
                        error: function(xhr, status, error) {
                            // alert("1")
                            console.error(xhr.responseText);
                            alert('An error occurred.');
                        }
                    });
                });
            });
        });








        function applyRestrictions(container) {
            restrictSpecialChars.call(container, '.nbew_text');
            restrictSpecialChars.call(container, '.nbew_number');
            restrictSpecialChars.call(container, '.nbew_number_decimal');
            restrictSpecialChars.call(container, '.nbew_specialchartextandnumber');


        }



        $(document).on('input', '#cpsModal input[data-required], #cpsModal textarea[data-required]', function() {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('change', '#cpsModal select[data-required]', function() {
            if ($(this).val() && $(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
            }
        });


        $(document).on('blur change', '.datepicker', function() {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        //////////////////////////////////add new acccordian////////////////////////////////////

        let cpsSetCounter = 1;

        function addCpsSet(update) {

            //alert(cpsSetCounter);

            if (!validateCpsTable()) {
                const alertBox = document.getElementById('cpsValidationAlert');
                alertBox.classList.remove('d-none');
                const modalBody = document.querySelector('#cpsModal .modal-body');
                modalBody.scrollTop = 0;

                return;
            }

            const container = document.getElementById('cpsSetsContainer');
            const maxSets = 50;

            if (!update) { // only block when NOT updating
                const currentSets = container.querySelectorAll('.accordion-item').length;
                if (currentSets >= maxSets) {
                    alert(`Maximum of ${maxSets} sets allowed.`);
                    return;
                }
            }

            container.querySelectorAll('.accordion-collapse.show').forEach(el => {
                el.classList.remove('show');

                const btn = el.closest('.accordion-item')
                    ?.querySelector('.accordion-button');
                if (btn) btn.classList.add('collapsed');
            });


            cpsSetCounter++;

            const firstSet = container.querySelector('.cps-set');

            $(firstSet).find('select[name^="cps_month_employee"], select[name^="cps_month_employer"]').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });


            const newSetContent = firstSet.cloneNode(true);

            // Reset shortfall rows to a single empty row for the new set
            newSetContent.querySelectorAll('.shortfall-table tbody').forEach(tbody => {
                const rows = tbody.querySelectorAll('tr');
                if (rows.length > 1) {
                    rows.forEach((row, idx) => {
                        if (idx > 0) row.remove();
                    });
                }
                const firstRow = tbody.querySelector('tr');
                if (firstRow) {
                    firstRow.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                    const removeBtn = firstRow.querySelector('.removeshortfall-row');
                    if (removeBtn) removeBtn.classList.add('d-none');
                    const sno = firstRow.querySelector('.sno');
                    if (sno) sno.textContent = '1';
                }
            });

            newSetContent.querySelectorAll('input.shortfall_total').forEach(input => {
                input.value = '0.00';
            });

            newSetContent.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${cpsSetCounter}]`);
                }

                if (input.type === 'text' || input.type === 'number') {
                    input.value = input.value.includes('Rs') ? '0.00' : '';
                }

                if (input.type === 'radio') {
                    input.checked = input.value === 'Y';
                }

                if (input.tagName.toLowerCase() === 'textarea') {
                    input.value = '';
                    input.classList.add('d-none');
                }

                if (input.tagName.toLowerCase() === 'select') {
                    input.selectedIndex = 0;
                }
            });

            newSetContent.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${cpsSetCounter}]`);
                }

                if (input.type === 'radio') {
                    input.checked = input.value === 'Y';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = '';
                }

                if (input.tagName === 'TEXTAREA') {
                    input.classList.add('d-none');
                }
            });



            const shortfallWrap = newSetContent.querySelector(`[id^="shortfall_wrap"]`);
            if (shortfallWrap) {
                // Set unique ID
                shortfallWrap.id = `shortfall_wrap[${cpsSetCounter}]`;

                // Always hide it initially
                shortfallWrap.classList.add('d-none');

                // Update total span ID dynamically with counter
                const totalSpan = shortfallWrap.querySelector(`[id^="shortfall_total"]`);
                if (totalSpan) totalSpan.id = `shortfall_total[${cpsSetCounter}]`;
            }



            newSetContent.querySelectorAll('.transcpsid').forEach(input => {
                input.value = '';
            });

            const sNoInputs = newSetContent.querySelectorAll('.sNo');
            sNoInputs.forEach((input, index) => {
                input.value = index + 1;
            });



            const accordion = document.createElement('div');
            accordion.classList.add('accordion', 'mb-3');
            const headingId = `headingCps${cpsSetCounter}`;
            const collapseId = `collapseCps${cpsSetCounter}`;

            accordion.innerHTML = `
        <div class="accordion-item mt-3" style="border: 1px solid #dee2e6; border-radius: 0.25rem;">
            <h2 class="accordion-header bordered-accordion" id="${headingId}">
                <div class="d-flex align-items-center w-100 p-2 mt-2 mb-2">
                    <span style="font-size: 1rem; white-space: nowrap; flex-grow: 1;" class="accordion-title">
                        CPS Set ${cpsSetCounter}: Employee & Remittance Details
                    </span>

                    <button type="button" class="btn btn-success btn-sm addRow gap-1 fw-bold me-2">
                        Add Employee
                    </button>

                    <button type="button" class="btn btn-danger btn-sm deleteRow fw-bold">
                        Remove
                    </button>

                    <button class="accordion-button  ms-2 custom-accordion-btn"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="false"
                            aria-controls="${collapseId}">
                    </button>
                </div>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse show" aria-labelledby="${headingId}">
                <div class="accordion-body"></div>
            </div>
        </div>
    `;

            accordion.querySelector('.accordion-body').appendChild(newSetContent);


            container.appendChild(accordion);


            $(newSetContent).find('select.select2forcps').each(function() {
                $(this).val(null); // remove any selected "", etc.
                $(this).find('option:selected').prop('selected', false);
            });
            $('select[name^="cps_month_employee"], select[name^="cps_month_employer"]')
                .not('.select2-hidden-accessible')
                .select2({
                    placeholder: "Select Month",
                    allowClear: true,
                    closeOnSelect: false
                });


            applyRestrictions(accordion);


        }

        $(document).ready(function() {

            $('select[name^="cps_month_employee[1]"]').not('.select2-hidden-accessible').select2({
                placeholder: "Select Month",
            });


            $('select[name^="cps_month_employer[1]"]').not('.select2-hidden-accessible').select2({
                placeholder: "Select Month",

            });

        });



        $(document).on('change', 'select[name^="cps_month_employee"]', function() {
            let selectedMonths = $(this).val() || [];

            let match = $(this).attr('name').match(/\[(\d+)\]/);
            if (!match) return;
            let setNumber = match[1];

            let employerSelect = $(`select[name="cps_month_employer[${setNumber}]"]`);
            employerSelect.val(selectedMonths).trigger('change');

            if (!employerSelect.data('readonly-attached')) {
                employerSelect.on('select2:opening', function(e) {
                    e.preventDefault();
                });

                employerSelect.on('select2:unselecting', function(e) {
                    e.preventDefault();
                });

                employerSelect.data('readonly-attached', true);
            }

            let shortfallSelect = $(`select[name="shortfallmonth[${setNumber}]"]`);
            shortfallSelect.empty().append('<option value="">Select Month</option>');

            $(this).find('option:selected').each(function() {
                const v = $(this).val();
                if (!v) return;
                shortfallSelect.append(`<option value="${v}">${$(this).text()}</option>`);
            });

        });





        ////////////////////////////////////////add and delete row////////////////////////////////

        document.addEventListener('click', function(e) {
            if (e.target.closest('.addRow')) {
                addCpsSet();
            }

            if (e.target.closest('.deleteRow')) {
                const accordionItem = e.target.closest('.accordion-item');
                if (accordionItem) accordionItem.remove();

                // Renumber all remaining sets
                renumberCpsSets();

                // 🔥 Open the last remaining accordion after deletion
                const container = document.getElementById('cpsSetsContainer');
                const allItems = container.querySelectorAll('.accordion-item');

                if (allItems.length) {
                    const lastItem = allItems[allItems.length - 1]; // last remaining item
                    const lastCollapse = lastItem.querySelector('.accordion-collapse');
                    const lastButton = lastItem.querySelector('.accordion-button');

                    if (lastButton) lastButton.classList.remove('collapsed'); // remove collapsed class
                    if (lastCollapse) {
                        const instance = bootstrap.Collapse.getOrCreateInstance(lastCollapse, {
                            toggle: false
                        });
                        instance.show(); // open the last accordion
                    }
                }
            }

            // if (e.target.classList.contains('yes-no-radio')) {
            //     const td = e.target.closest('td');
            //     const textarea = td.querySelector('textarea');
            //     if (!textarea) return;

            //     if (e.target.value === 'N') {
            //         textarea.classList.remove('d-none');
            //     } else {
            //         textarea.classList.add('d-none');
            //         textarea.value = '';
            //     }
            // }

        });




        function renumberCpsSets() {
            const items = document.querySelectorAll('#cpsSetsContainer .accordion-item');

            items.forEach((item, index) => {
                const newIndex = index + 1;

                const titleSpan = item.querySelector('.accordion-title');
                const empName = item.dataset.empName;
                const cpsNo = item.dataset.cpsNo;

                if (titleSpan) {
                    if (empName && cpsNo) {
                        titleSpan.innerHTML =
                            `<span class="title-text">CPS Set ${newIndex}: Employee & Remittance Details</span>
                     <span class="emp-badge">${empName}</span>
                     <span class="cps-badge">CPS No: ${cpsNo}</span>`;
                    } else {
                        titleSpan.textContent = `CPS Set ${newIndex}: Employee & Remittance Details`;
                    }
                }

                const header = item.querySelector('.accordion-header');
                const button = item.querySelector('.accordion-button');
                const collapse = item.querySelector('.accordion-collapse');

                if (header) header.id = `headingCps${newIndex}`;
                if (collapse) {
                    collapse.id = `collapseCps${newIndex}`;
                    collapse.setAttribute('aria-labelledby', `headingCps${newIndex}`);
                }
                if (button) {
                    button.setAttribute('data-bs-target', `#collapseCps${newIndex}`);
                    button.setAttribute('aria-controls', `collapseCps${newIndex}`);
                }
            });

            cpsSetCounter = items.length;
        }
        //////////////end/////////////////////////////////////

        $(document).on('change', '.yes-no-radio', function() {
            const $radio = $(this);
            const radioName = $radio.attr('name'); // e.g., "q1[1]" or "q2[1]"

            if (!radioName) return;

            // Convert "q1[1]" → "details1[1]" for the corresponding textarea
            const detailName = radioName.replace(/^q/, 'details');

            // Find the textarea in the same row with that name
            const $textarea = $radio.closest('tr').find(`textarea[name="${detailName}"]`);

            if ($radio.val() === 'N') {
                $textarea.removeClass('d-none');
            } else {
                $textarea
                    .addClass('d-none')
                    .val('')
                    .removeClass('is-invalid');
            }
        });

        $(document).on('change', '.credited-select', function() {
            const $td = $(this).closest('td');
            const $textarea = $td.find('.credited_remarks');

            if ($(this).val() === 'N') {
                $textarea
                    .removeClass('d-none');
            } else {
                $textarea
                    .addClass('d-none')
                    .val('')
                    .removeClass('is-invalid');
            }
        });


        $(document).ready(function() {
            var cpsModalEl = document.getElementById('cpsModal');
            var cpsModal = new bootstrap.Modal(cpsModalEl);

            $('#cpsFormBtn').click(function() {
                cpsModal.show();
            });
        });




        function validateCpsTable() {
            let isValid = true;
            let $firstInvalidField = null;

            $('.is-invalid').removeClass('is-invalid');



            $('.ddoandtreasury')
                .find('input[data-required="true"], textarea[data-required="true"]')
                .each(function() {
                    const $field = $(this);

                    if ($field.is('[type="hidden"]')) return true;

                    if (!$field.val() || !$field.val().trim()) {
                        isValid = false;
                        $field.addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $field;
                        }
                    }
                });

            $('#cpsSetsContainer tr').each(function() {
                const $row = $(this);




                $row.find('input[data-required="true"], textarea[data-required="true"]').each(function() {
                    const $field = $(this);
                    if ($field.is('[type="hidden"]')) return true;
                    if ($field.is('textarea') && $field.hasClass('d-none')) return true;

                    if (!$field.val() || !$field.val().trim()) {
                        isValid = false;
                        $field.addClass('is-invalid');

                        if (!$firstInvalidField) { // ✅ ADDED
                            $firstInvalidField = $field;
                        }
                    }
                });

                $('.select2forcps').each(function() {
                    const $select = $(this);
                    let val = $select.val();
                    if (Array.isArray(val)) val = val.filter(Boolean);

                    if (!val || val.length === 0) {
                        $select.next('.select2-container').find('.select2-selection')
                            .addClass('is-invalid')
                            .css('border-color', '#dc3545');
                    } else {
                        $select.next('.select2-container').find('.select2-selection')
                            .removeClass('is-invalid')
                            .css('border-color', '');
                    }
                });

                const q1 = $row.find('input[name^="q1"]:checked').val();
                if (q1 === 'N') {
                    const $details1 = $row.find('textarea[name^="details1"]');
                    if (!$details1.val().trim()) {
                        isValid = false;
                        $details1.removeClass('d-none').addClass('is-invalid');

                        if (!$firstInvalidField) { // ✅ ADDED
                            $firstInvalidField = $details1;
                        }
                    }
                }

                const q2 = $row.find('input[name^="q2"]:checked').val();
                if (q2 === 'N') {
                    const $details2 = $row.find('textarea[name^="details2"]');
                    if (!$details2.val().trim()) {
                        isValid = false;
                        $details2.removeClass('d-none').addClass('is-invalid');

                        if (!$firstInvalidField) { // ✅ ADDED
                            $firstInvalidField = $details2;
                        }
                    }
                }

                // f) Credited in appropriate head validation
                const credited = $row.find('select[name^="creditedin_appropriatehead"]').val();

                if (credited === 'N') {
                    const $remarks = $row.find('textarea[name^="credited_remarks"]');

                    if (!$remarks.val() || !$remarks.val().trim()) {
                        isValid = false;
                        $remarks.removeClass('d-none').addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $remarks;
                        }
                    }
                }

                // SHORTFALL REMITTANCE > 0
                const shortfallName = $row.find('input[name^="shortfall_remittance"]').attr('name');
                if (shortfallName && shortfallName.startsWith('shortfall_remittance')) {
                    const setNo = shortfallName.match(/\[(\d+)\]/)[1];
                    const shortfallVal = parseFloat($row.find(`input[name="shortfall_remittance[${setNo}]"]`)
                        .val()) || 0;

                    const $shortfallWrap = $(`#shortfall_wrap\\[${setNo}\\]`);
                    if (shortfallVal > 0) {
                        $shortfallWrap.removeClass('d-none'); // show wrapper
                        applyRestrictions($shortfallWrap[0]);

                        $shortfallWrap.find('input, select').each(function() {
                            const $field = $(this);
                            if ($field.is('[type="hidden"]')) return true; // skip hidden fields

                            // Validation check
                            if (!$field.val() || !$field.val().toString().trim()) {
                                isValid = false;
                                $field.addClass('is-invalid');
                                if (!$firstInvalidField) $firstInvalidField = $field;

                                // Remove invalid on input/change
                                $field.off('input change').on('input change', function() {
                                    if ($field.val() && $field.val().toString().trim()) {
                                        $field.removeClass('is-invalid');
                                    }
                                });
                            } else {
                                $field.removeClass('is-invalid');
                            }
                        });



                        $shortfallWrap.find('input').each(function() {
                            const $field = $(this);
                            if ($field.is('[type="hidden"]')) return true;
                            if (!$field.val() || !$field.val().toString().trim()) {
                                isValid = false;
                                $field.addClass('is-invalid');
                                if (!$firstInvalidField) $firstInvalidField = $field;
                            } else {
                                $field.removeClass('is-invalid');
                            }
                        });
                    } else {
                        $shortfallWrap.addClass('d-none'); // hide wrapper if 0
                        $shortfallWrap.find('input').removeClass('is-invalid'); // remove any previous error
                    }
                }

                // REMITTANCE EXCESS > 0
                const remExcessName = $row.find('input[name^="remittanceexcess_cps"]').attr('name');
                if (remExcessName && remExcessName.startsWith('remittanceexcess_cps')) {
                    const setNo = remExcessName.match(/\[(\d+)\]/)[1];
                    const remExcessVal = parseFloat($row.find(`input[name="remittanceexcess_cps[${setNo}]"]`)
                        .val()) || 0;

                    const $remarks = $row.find(`textarea[name="remittanceexcess_remarks[${setNo}]"]`);
                    if (remExcessVal > 0) {
                        $remarks.removeClass('d-none'); // show textarea
                        if (!$remarks.val() || !$remarks.val().toString().trim()) {
                            isValid = false;
                            $remarks.addClass('is-invalid');
                            if (!$firstInvalidField) $firstInvalidField = $remarks;
                        } else {
                            $remarks.removeClass('is-invalid');
                        }
                    } else {
                        $remarks.addClass('d-none'); // hide textarea
                        $remarks.removeClass('is-invalid'); // remove any previous error
                    }
                }




            });




            $('#cpsSetsContainer .table-responsive table tbody tr').each(function() {
                const $row = $(this);

                $row.find('input[name], select[name]').each(function() {
                    const $field = $(this);
                    if ($field.is('[type="hidden"]')) return true;
                    // SKIP Treasury / DDO
                    const name = $field.attr('name');
                    if (name && (name.startsWith('treasury_cps') || name.startsWith('ddocode_cps'))) {
                        return; // skip validation
                    }

                    if (!$field.val() || !$field.val().toString().trim()) {
                        isValid = false;
                        $field.addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $field;
                        }
                    }
                });


                $row.find('select').each(function() {
                    const $select = $(this);
                    if ($select.is('[type="hidden"]')) return true;

                    let val = $select.val();

                    // If multiple select, filter out empty values
                    if (Array.isArray(val)) val = val.filter(v => v); // remove null/empty

                    // Check if empty
                    const isEmpty = !val || (Array.isArray(val) ? val.length === 0 : !val.toString()
                        .trim());

                    if (isEmpty) {
                        isValid = false;
                        $select.addClass('is-invalid');

                        if (!$firstInvalidField) {
                            $firstInvalidField = $select;
                        }
                    } else {
                        $select.removeClass('is-invalid');
                    }
                });

            });

            if (!isValid && $firstInvalidField) {
                console.log($firstInvalidField);
                //  alert();
                const $collapse = $firstInvalidField.closest('.accordion-collapse');

                if ($collapse.length) {
                    const collapseEl = $collapse[0];
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, {
                        toggle: false
                    });
                    bsCollapse.show();

                    setTimeout(() => {
                        collapseEl.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        $firstInvalidField.focus();
                    }, 200);
                }
            } else {
                // runs only if isValid OR $firstInvalidField is null
                $('#cpsValidationAlert').addClass('d-none'); // hide if all valid
            }




            return isValid;
        }



        function restrictSpecialChars(selector) {
            const sqlKeywords = [
                'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE', 'UNION', 'MERGE', 'CALL'
            ];


            $(selector)
                .off("keypress paste")
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;

                    if (selector === '.nbew_text') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        // Only allow letters (English + Tamil Unicode) and space, enforce maxlength
                        if (!/^[a-zA-Z\u0B80-\u0BFF\s]$/.test(char) || value.length >= maxLength) {
                            event.preventDefault();
                        }

                        let newValue = value + char;

                        if (/([a-zA-Z\u0B80-\u0BFF])\1{3,}/.test(newValue)) {
                            event.preventDefault();
                            return false;
                        }


                        let upperValue = newValue.toUpperCase();
                        for (let kw of sqlKeywords) {
                            if (upperValue.includes(kw)) {
                                event.preventDefault();
                                this.value = '';
                                alert('SQL keywords are not allowed!');
                                return false;
                            }
                        }


                    } else if (selector === '.nbew_number') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        if (!/^[0-9]$/.test(char) || value.length >= maxLength) {
                            event.preventDefault();
                        }


                    } else if (selector === '.nbew_specialchartextandnumber') {

                        let fieldName = $(this).attr('name') || '';
                        let value = $(this).val();
                        let position = value.length; // current index

                        if (fieldName.startsWith('shortfallfeadofaccount')) {

                            let char = String.fromCharCode(event.which);

                            // Max 16 characters
                            if (position >= 16) {
                                event.preventDefault();
                                return;
                            }

                            // First 9 → numbers only
                            if (position < 9) {
                                if (!/^[0-9]$/.test(char)) {
                                    event.preventDefault();
                                }
                            } else if (position < 11) {

                                if (/^[a-z]$/.test(char)) {
                                    // convert lowercase to uppercase
                                    event.preventDefault();
                                    let upperChar = char.toUpperCase();
                                    $(this).val(value + upperChar);
                                } else if (!/^[A-Z]$/.test(char)) {
                                    event.preventDefault();
                                }
                            } else {
                                if (!/^[0-9]$/.test(char)) {
                                    event.preventDefault();
                                }
                            }

                        } else {

                            let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                            if (!/^[A-Za-z0-9#\- ]$/.test(char) || value.length >= maxLength) {
                                event.preventDefault();
                            }
                        }
                    } else if (selector === '.nbew_number_decimal') {

                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        let maxIntLength = maxLength - 3; // reserve ".00"

                        // allow digits and dot
                        if (!/[0-9.]/.test(char)) {
                            event.preventDefault();
                        }

                        // only one dot
                        if (char === '.' && value.includes('.')) {
                            event.preventDefault();
                        }

                        // dot cannot be first
                        if (char === '.' && value.length === 0) {
                            event.preventDefault();
                        }

                        // before decimal → limit digits
                        if (!value.includes('.') && value.length >= maxIntLength && char !== '.') {
                            event.preventDefault();
                        }

                        // after decimal → only 2 digits
                        if (value.includes('.')) {
                            let dotPos = value.indexOf('.');
                            let decimals = value.substring(dotPos + 1);

                            if (decimals.length >= 2 && this.selectionStart > dotPos) {
                                event.preventDefault();
                            }
                        }
                    }




                })
                .on("paste", function(e) {
                    e.preventDefault();
                    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                    let cleanData = '';

                    if (selector === '.nbew_text') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                        cleanData = pasteData.replace(/[^a-zA-Z\u0B80-\u0BFF\s]/g, '');

                        let upperData = cleanData.toUpperCase();
                        for (let kw of sqlKeywords) {
                            if (upperData.includes(kw)) {
                                alert('SQL keywords are not allowed!');
                                cleanData = cleanData.replace(new RegExp(kw, 'gi'), '');
                            }
                        }

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;
                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);
                        input.value = currentValue.substring(0, start) + cleanData + currentValue.substring(end);
                        input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                        return;
                    } else if (selector === '.nbew_number') {
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        cleanData = pasteData.replace(/[^0-9]/g, '');

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;

                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);
                        input.value =
                            currentValue.substring(0, start) +
                            cleanData +
                            currentValue.substring(end);

                        input.setSelectionRange(
                            start + cleanData.length,
                            start + cleanData.length
                        );
                        return; // stop default insert
                    } else if (selector === '.nbew_specialchartextandnumber') {

                        let fieldName = $(this).attr('name') || '';

                        if (fieldName.startsWith('shortfallfeadofaccount')) {

                            let input = e.target;

                            // Remove invalid characters
                            let cleanData = pasteData.replace(/[^0-9A-Za-z]/g, '').toUpperCase();

                            // If less than 9 digits, just allow digits only
                            if (cleanData.length <= 9) {
                                cleanData = cleanData.replace(/[^0-9]/g, '');
                            } else {

                                let first9 = cleanData.substring(0, 9).replace(/[^0-9]/g, '');

                                let next2 = cleanData.substring(9, 11).replace(/[^A-Z]/g, '');

                                // If 2 letters not present → stop at 9 digits
                                if (next2.length < 2) {
                                    cleanData = first9;
                                } else {

                                    let last5 = cleanData.substring(11, 16).replace(/[^0-9]/g, '');

                                    cleanData = first9 + next2 + last5;
                                }
                            }

                            input.value = cleanData.substring(0, 16);
                            input.setSelectionRange(input.value.length, input.value.length);

                            e.preventDefault();
                            return;
                        }



                        // ---- OTHER FIELDS NORMAL ----
                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        cleanData = pasteData.replace(/[^A-Za-z0-9#\- ]/g, '');

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;

                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);

                        input.value =
                            currentValue.substring(0, start) +
                            cleanData +
                            currentValue.substring(end);

                        input.setSelectionRange(
                            start + cleanData.length,
                            start + cleanData.length
                        );

                        return;
                    } else if (selector === '.nbew_number_decimal') {

                        let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;

                        // allow only digits, dot, minus
                        cleanData = pasteData.replace(/[^0-9.]/g, '');

                        let input = e.target;
                        let start = input.selectionStart;
                        let end = input.selectionEnd;
                        let currentValue = input.value;

                        // respect maxlength only
                        let allowedLength = maxLength - (currentValue.length - (end - start));
                        if (allowedLength <= 0) return;

                        cleanData = cleanData.substring(0, allowedLength);

                        input.value =
                            currentValue.substring(0, start) +
                            cleanData +
                            currentValue.substring(end);

                        input.setSelectionRange(
                            start + cleanData.length,
                            start + cleanData.length
                        );

                        return;
                    }


                    // Insert clean data at cursor position
                    let input = e.target;
                    let start = input.selectionStart;
                    let end = input.selectionEnd;
                    let original = input.value;
                    input.value = original.substring(0, start) + cleanData + original.substring(end);
                    input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                })
                .on("blur", function() {

                    if (selector !== '.nbew_number_decimal') return;

                    let maxLength = parseInt($(this).attr('maxlength'), 10) || Infinity;
                    let maxIntLength = maxLength - 3;
                    let v = this.value;

                    if (v === '') return;

                    v = v.replace(/[^0-9.]/g, '');

                    if (!v.includes('.')) {
                        v = v.substring(0, maxIntLength) + '.00';
                    } else {
                        let [i, d] = v.split('.');
                        i = i.substring(0, maxIntLength);
                        d = (d || '').padEnd(2, '0').substring(0, 2);
                        v = i + '.' + d;
                    }

                    this.value = v;
                });

        }
    </script>
@endsection
