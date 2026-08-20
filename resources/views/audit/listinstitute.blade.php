<? ?>
@section('content')
    @extends('index2')
    @include('common.alert')
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

        #quartercode,
        #quartercode option {
            font-family: Consolas, "Courier New", monospace;
        }
    </style>
    @php
    // dd($quatdetails);
        $maxQuarterLabelLength = collect($quatdetails)->map(function ($quat) {
            return mb_strlen($quat->planname ?? $quat->auditquarter ?? '');
        })->max() ?? 0;
    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <div class="col-12">
        <div class="card">
            <div class="card-header card_header_color lang" key="">List of Institutes Completed Audit Process</div>
            <div class="card-body">
                <div class="col-md-4 mb-1 mx-auto">
                    <label class="form-label lang required" key="auditquarter">Audit Quarter</label>

                    <select class="form-select mr-sm-2" id="quartercode" name="quartercode"
                        onchange="initializeDataTable()">
                        @foreach ($quatdetails as $quat)
                            @php
                                $quarterLabel = $quat->planname ?? $quat->auditquarter ?? '';
                                $financialYear = $quat->financialyear ?? '';
                                $paddingSpaces = max(3, $maxQuarterLabelLength - mb_strlen($quarterLabel) + 3);
                            @endphp
                            <option value="{{ $quat->optionvalue ?? $quat->planmappingid ?? $quat->auditquartercode }}"
                                data-quartercode="{{ $quat->auditquartercode }}"
                                data-instename="{{ $quat->planname ?? $quat->auditquarter }}"
                                data-insttname="{{ $quat->planname ?? $quat->auditquarter }}">
                                {!! e($quarterLabel) !!}{!! !empty($financialYear) ? str_repeat('&nbsp;', $paddingSpaces) . e($financialYear) : '' !!}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">List of Institutes Completed Audit Process</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="audit_plandetails"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            @csrf
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="text-wrap lang" key="inst">Institution Details</th>
                                <th lass="lang" key="audit_team_det">>Team Details</th>
                                <th lass="lang" key="mandays">ManDays</th>
                                <th lass="lang" key="from_date">From date</th>
                                <th lass="lang" key="to_date">To date</th>
                                <th lass="lang" key="entrymeeting_date">Entry Meeting date</th>
                                <th lass="lang" key="exitmeetingdate">Exit Meeting date</th>
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
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>


    <!-- solar icons -->

    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    {{-- data table --}}
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
    <script>
        let dataFromServer
        $(document).ready(function() {


            var lang = getLanguage('') // Default to 'en' if no language is set
            initializeDataTable();

        });

        function initializeDataTable() {
            const language = getLanguage('');

            const selectedOption = $('#quartercode option:selected');
            var quartercode = selectedOption.data('quartercode');
            var auditplanmappingid = $('#quartercode').val();
            $.ajax({
                url: "/report/get_listinstitututes",
                type: "POST",
                data: {
                    quartercode: quartercode,
                    auditplanmappingid: auditplanmappingid,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#listinstitutetable_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message

                        dataFromServer = json.data;
                        console.log(dataFromServer)
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#listinstitutetable_wrapper').hide();
                        $('#no_data').show();
                    }
                }
            });

        }

        function renderTable(language) {
            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Regname = language === 'ta' ? 'regiontname' : 'regionename';
            const distname = language === 'ta' ? 'disttname' : 'distename';
            const team_head = language === 'ta' ? 'team_head_ta' : 'team_head_en';
            const team_mem = language === 'ta' ? 'team_members_ta' : 'team_members_en';

            // destroy old
            if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                $('#audit_plandetails').DataTable().clear().destroy();
            }

            $('#audit_plandetails').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                scrollX: true,
                autoWidth: false,
                responsive: true,
                destroy: true,
                data: dataFromServer,
                initComplete: function() {
                    $("#audit_plandetails").wrap(
                        "<div style='overflow:auto; width:100%; position:relative;'></div>"
                    );
                },
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
                        data: null,
                        render: function(data, type, row) {
                            return `<b>Institution:</b> ${row[InstituteName]}<br>
                            <b>Region:</b> ${row[Regname]}<br>
                            <b>District:</b> ${row[distname]}`;
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: null,
                        title: columnLabels?.["teamname"]?.[language] || 'Team Details',
                        render: function(data, type, row) {
                            return `<b>Team Head:</b> ${row[team_head] || '-'}<br>
                            <b>Team Members:</b> ${row[team_mem] || '-'}`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "mandays",
                        render: function(data, type, row) {
                            return row["mandays"] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "formatted_fromdate",
                        render: function(data, type, row) {
                            return row["formatted_fromdate"] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "formatted_todate",
                        render: function(data, type, row) {
                            return row["formatted_todate"] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "formatted_entrydate",
                        render: function(data, type, row) {
                            return row["formatted_entrydate"] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "formatted_exitdate",
                        render: function(data, type, row) {
                            return row["formatted_exitdate"] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<a class="btn btn-sm btn-primary"
                             href="/trans_auditslip?id=${row.encrypted_instid}&financialyear=${row.encrypted_financialyearcode}&auditscheduleid=${row.encrypted_auditscheduleid}">
                             <i class="ti ti-eye fs-4 me-2"></i> View Audit Report
                         </a>`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column'
                    }
                ]


            });
            updatedatatable(language, "audit_plandetails");
        }
        $('#translate').change(function() {
            const lang = getLanguage('Y') // Store language selection
            updateTableLanguage(
                lang); // Update the table with the new language by destroying and recreating it

        });

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                $('#audit_plandetails').DataTable().clear().destroy();
            }
            renderTable(language);
        }
    </script>
@endsection
