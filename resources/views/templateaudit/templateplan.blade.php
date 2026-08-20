@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Template Audit')

<style>
    .card_seperator {
        height: 10px;
        border: 0;
        box-shadow: 0 10px 10px -10px #8c8b8b inset;
    }

    .card-title {
        font-size: 15px;
    }

    .title-part-padding {
        background-color: #e3efff;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .dataTables_info {
        margin-bottom: 1rem !important;
    }

    table.dataTable td,
    table.dataTable th {
        word-wrap: break-word;
        white-space: normal;
    }

    /* Base button */
    .btn-template {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #fff;
        padding: 6px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    /* Primary variant */
    .btn-template.btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .btn-template.btn-primary:hover {
        background: linear-gradient(135deg, #2980b9, #2c3e50);
    }

    /* Warning variant */
    .btn-template.btn-warning {
        background: linear-gradient(135deg, #f1c40f, #e67e22);
        color: #000;
    }

    .btn-template.btn-warning:hover {
        background: linear-gradient(135deg, #e67e22, #d35400);
        color: #fff;
    }

    /* Success variant */
    .btn-template.btn-success {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    .btn-template.btn-success:hover {
        background: linear-gradient(135deg, #2ecc71, #16a085);
    }

    /* Active / pressed state */
    .btn-template:active {
        transform: scale(0.98);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Disabled state */
    .btn-template:disabled,
    .btn-template.disabled {
        background: #95a5a6 !important;
        cursor: not-allowed;
        color: #ecf0f1;
        opacity: 0.7;
    }

    /* Small variant */
    .btn-template.btn-sm {
        padding: 4px 12px;
        font-size: 0.75rem;
        border-radius: 4px;
    }

    /* Icon spacing */
    .btn-template i {
        margin-right: 6px;
    }

    /* Override Bootstrap if present */
    .btn-template,
    .btn-template:hover,
    .btn-template:active,
    .btn-template:disabled {
        border: none !important;
    }




    .btn-report {
        background: linear-gradient(135deg, #27ae60, #219653);
        border: none;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 500;
    }

    .btn-report:hover {
        background: linear-gradient(135deg, #219653, #1e8449);
        color: white;
    }
</style>
@php
    $sessioncharge = session('charge');

@endphp
<link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

<div class="col-12">
    <div class="card">
        <div class="card-header card_header_color lang" key="">Audit Plan Details For Template Audit</div>
        <div class="card-body">
            <div class="col-md-4 mb-1 mx-auto">
                <label class="form-label lang required" key="auditquarter">Audit Quarter</label>

             
                <select class="form-select mr-sm-2" id="quartercode" name="quartercode"
                    onchange="initializeDataTable()">
                    @forelse ($quarter_det ?? [] as $quat)
                        <option value="{{ $quat->planmappingid }}" {{ isset($selected_quartercode) && $selected_quartercode == $quat->planmappingid ? 'selected' : '' }}>
                            {{ $quat->planname }} - {{ $quat->financialyear }}
                        </option>
                    @empty
                        <option value="">No Quarter Available</option>
                    @endforelse
                </select>
 

            </div>
        </div>
    </div>
</div>



<div class="card mt-2" style="border-color: #7198b9">
    <div class="card-header card_header_color lang" key=" Institute-wise Audit Plan Details For Template Audit">
        Institute-wise Audit Plan Details For Template Audit</div>
    <div class="card-body">
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="audit_plandetails"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        @csrf
                        <tr>
                            <th class="lang text-wrap" key="s_no">S.No</th>
                            <th class="lang text-wrap" key="department">Department</th>
                            <th class="lang text-wrap" key="instname_label">Institute</th>
                            <th class="text-wrap" key="auditors_det">Auditor Details</th>
                            <th class="lang" key="quarter_label">Quarter</th>
                            <th class="all lang" key="action">Action</th>
                        </tr>

                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id='no_data' class='hide_this'>
            <center>No Data Available</center>
        </div>
    </div>
</div>

<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/js/datatable/datatable-advanced.init.js"></script>

<script>
    var dataFromServer = [];

    $(document).ready(function() {


        var lang = getLanguage('') // Default to 'en' if no language is set
        initializeDataTable();

    });

    $('#translate').change(function() {
        const lang = getLanguage('Y') // Store language selection
        updateTableLanguage(lang); // Update the table with the new language by destroying and recreating it

    });


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
            $('#audit_plandetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function initializeDataTable() {
        const language = getLanguage('');
        var quartercode = $('#quartercode').val();

        $.ajax({
            // url: "/audit/audit_plandetails",
            url: "/templateaudit/plandetails",
            type: "POST",
            data: {
                quartercode: quartercode,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(json) {

                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#saudit_plandetails_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    // console.log(dataFromServer);
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#saudit_plandetails_wrappers').hide();
                    $('#no_data').show();

                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
            }
        });

    }

    function renderTable(language) {

        const InstituteName = language === 'ta' ? 'insttname' : 'instename';
        const Dept = language === 'ta' ? 'deptesname' : 'deptesname';
        const Category = language === 'ta' ? 'cattname' : 'catename';
        const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';

        const username = language === 'ta' ? 'usertamilname' : 'username';
        const userdesignation = language === 'ta' ? 'desigtlname' : 'desigelname';
        const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';

        const quarterName = language === 'ta' ? 'auditquarter' : 'auditquarter';
        const quartercode = 'auditquartercode';

        if ($.fn.dataTable.isDataTable('#audit_plandetails')) {
            $('#audit_plandetails').DataTable().clear().destroy();
        }

        var table = $('#audit_plandetails').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "scrollX": true,
            "initComplete": function(settings, json) {
                $("#audit_plandetails").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "autoWidth": false,
            "responsive": true,
            "destroy": true,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: Dept,
                    render: function(data, type, row) {
                        return row[Dept] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: "null",
                    render: function(data, type, row) {
                        return `<b>Institute:</b> ${row[InstituteName]}<br><b>Category:</b>${row[Category]}<br>  ${row[SubCategory] ? `<b>Sub Category:</b> ${row[SubCategory]}<br>` : ""}`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },

                {
                    data: username,
                    title: 'Auditor Details',
                    render: function(data, type, row) {
                        return `<b>Auditor:</b> ${row[username]} <br>
                <b>Designation:</b> ${row[userdesignation]}`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },

                {
                    data: quarterName,
                    render: function(data, type, row) {
                        return row[quarterName] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        const {
                            encrypted_deptcode: deptcode = '',
                            encrypted_catcode: catcode = '',
 			            encrypted_subcatcode: subcatcode = '',
                            encrypted_formcode: formcode = '',
                            encrypted_instid: instid = '',
                            encrypted_tempplanid: tempplanid = '',

deptcode: deptcodeflag = '',
                            auditquartercode: auditquartercode = '',
                            encrypted_prioritycode: prioritycode = ''                        
} = row;

                        // Complete department configuration for all 4 users
                        const deptConfig = {
                            '01': {
                                route: '/templateaudit/start',
                                statusField: 'okp_status',
                                name: 'OKP'
                            },
                            '02': {
                                route: '/templateaudit/start-lfa',
                                statusField: 'lfa_status',
                                name: 'LFA'
                            },
                            '04': {
                                route: '/templateaudit/start-dca',
                                statusField: 'dca_status',
                                name: 'DCA'
                            },
                            '05': {
                                route: '/templateaudit/start-milk',
                                statusField: 'milk_status',
                                name: 'Milk'
                            }
                        };

                        // Status configuration for all audit types
                        const statusConfig = {
                            null: {
                                class: 'btn-primary',
                                en: 'Start Template Audit',
                                ta: 'டெம்ப்ளேட் தணிக்கையைத் தொடங்கவும்'
                            },
                            'E': {
                                class: 'btn-warning',
                                en: 'In Draft',
                                ta: 'வரைவில்'
                            },
                            'F': {
                                class: 'btn-success',
                                en: 'Finalized',
                                ta: 'முடிக்கப்பட்டது'
                            }
                        };

                        // Check if required fields are present
                        if (!deptcode || !formcode || !instid || !tempplanid) {
                            const message = language === 'ta' ? 'டெம்ப்ளேட் இல்லை' :
                                'No Template Available';
                            return `
                <div class="text-center">
                    <span class="text-muted lang">${message}</span>
                </div>
            `;
                        }

                        // Get department configuration
                        const config = deptConfig[deptcodeflag];
                        if (!config) {
                            const message = language === 'ta' ? 'பிரிவு கிடைக்கவில்லை' :
                                'Department not found';
                            return `<div class="text-center"><span class="text-muted lang">${message}</span></div>`;
                        }

                        // Get status value based on department
                        const statusValue = row[config.statusField];

                        // Get status configuration
                        const status = statusConfig[statusValue] || statusConfig.null;
                        const buttonText = status[language] || status.en;

                        return `
            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="${config.route}/${deptcode}/${formcode}/${instid}/${tempplanid}/${catcode}/${subcatcode}/${auditquartercode}/${prioritycode}"                 
class="btn-template btn-sm ${status.class} lang"
                   title="${buttonText} (${config.name})">
                    <i class="fas fa-play-circle me-1"></i> ${buttonText}
                </a>
            </div>
        `;
                    },
                    className: "text-center text-wrap noExport"
                }

            ],

        });

        const mobileColumns = [Category, SubCategory, InstituteName, username, quarterName];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "audit_plandetails");
    }
</script>
@endsection
