@extends('index2')
@section('content')
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

        .row {
            justify-content: center;
        }
    </style>
    @php
        $sessionchargedel = session('charge');

        $deptcode = optional($sessionchargedel)->deptcode ?? '';
        $region = optional($sessionchargedel)->regioncode ?? '';
        $district = optional($sessionchargedel)->distcode ?? '';
        $catcode = optional($sessionchargedel)->catcode ?? '';

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_region_disable = $region ? 'disabled' : '';
        $make_district_disable = $district ? 'disabled' : '';

    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <div class="col-12">
        <div class="card">
            <div class="card-header card_header_color lang" key="">List of Completed Consolidation Audit Report Process</div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="form-label lang required" key="department">Department</label>
                        @if ($deptcode)
                            <input type="hidden" id="deptcode_hidden" value="{{ $deptcode }}">

                            <select class="form-select select2" id="deptcode" name="deptcode">
                                <option value="{{ $deptcode }}" selected>
                                    {{ $departments->where('deptcode', $deptcode)->first()->deptelname ?? 'Department' }}
                                </option>
                            </select>
                        @else
                            <select class="form-select select2" id="deptcode" name="deptcode"
                               >
                                @foreach ($departments as $department)
                                    <option value="{{ $department->deptcode }}">
                                        {{ $department->deptelname }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label required lang">Category</label>
                        <select class="form-select select2" id="catcode" name="catcode" >
                            @if ($catcode)
                                <option value="{{ $catcode }}" selected>
                                    {{ $Category->where('catcode', $catcode)->first()->catename ?? 'Category' }}
                                </option>
                            @else
                                @foreach ($Category as $category)
                                    <option value="{{ $category->catcode }}">
                                        {{ $category->catename }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>


                </div>
            </div>

        </div>
    </div>
    </div>

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">List of Completed Consolidation Audit Report Process</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="audit_plandetails"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            @csrf
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="text-wrap lang" key="department">Department</th>
                                <th lass="lang" key="category">Category</th>
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
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/jquery.js') }}"></script> --}}
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>


    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    {{-- data table --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-advanced.init.js') }}"></script>

    <script>
        let dataFromServer;
        $(document).ready(function() {
            var lang = getLanguage('');

            // Initialize with default values if any
            initializeDataTable();

            // Handle category change
            $('#catcode').on('change', function() {
                refreshDataTable();
            });

            // Handle department change (if no default deptcode)
            @if (!$deptcode)
                $('#deptcode').on('change', function() {
                    refreshDataTable();
                });
            @endif
        });

        function initializeDataTable() {
            const language = getLanguage('');

            var deptcode = $('#deptcode').val();
            var catcode = $('#catcode').val();

            $.ajax({
                url: "/getconsolidationreport",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    catcode: catcode,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {
                    if (json.data && json.data !== null) {
                        $('#tableshow').show();
                        $('#audit_plandetails_wrapper').show();
                        $('#no_data').hide();

                        // Convert single object to array for DataTable
                        dataFromServer = [json.data];
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#audit_plandetails_wrapper').hide();
                        $('#no_data').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    $('#tableshow').hide();
                    $('#audit_plandetails_wrapper').hide();
                    $('#no_data').show();
                }
            });
        }

        function renderTable(language) {
            if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                $('#audit_plandetails').DataTable().clear().destroy();
            }

            // Enhance the data with default values for missing fields
            const enhancedData = dataFromServer.map(item => ({
                ...item,
                deptname_display: language === 'ta' ? (item.depttlname || '-') : (item.deptelname || '-'),
                // Category name based on language
                catname_display: language === 'ta' ? (item.cattname || '-') : (item.catename || '-'),
                encrypted_instid: item.instid || '',
                encrypted_financialyearcode: item.financialyearcode || '',
                encrypted_auditscheduleid: item.auditscheduleid || ''
            }));

            $('#audit_plandetails').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                scrollX: true,
                autoWidth: false,
                responsive: true,
                destroy: true,
                data: enhancedData,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        className: 'text-end'
                    },
                    {
                        data: "deptname_display",
                        className: 'text-wrap'
                    },
                    {
                        data: "catname_display",
                        className: 'text-wrap'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            // Check if required IDs exist before generating link
                            if (row.encrypted_instid && row.encrypted_auditscheduleid && row.catcode && row
                                .statusflag === 'A') {
                                return `<a class="btn btn-sm btn-primary"
                            href="/consalidation_auditslip?id=${row.encrypted_instid}&financialyear=${row.encrypted_financialyearcode}&auditscheduleid=${row.encrypted_auditscheduleid}">
                            <i class="ti ti-eye fs-4 me-2"></i> View Report
                        </a>`;
                            } else {
                                return `<button class="btn btn-sm btn-secondary" disabled>
                            <i class="ti ti-eye fs-4 me-2"></i> Not Finalized
                        </button>`;
                            }
                        },
                        className: 'text-center'
                    }
                ]
            });

            updatedatatable(language, "audit_plandetails");
        }

        $('#translate').change(function() {
            const lang = getLanguage('Y');
            updateTableLanguage(lang);
        });

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                $('#audit_plandetails').DataTable().clear().destroy();
            }
            renderTable(language);
        }

function refreshDataTable() {
    const deptcode = $('#deptcode').val();
    const catcode = $('#catcode').val();

    // Optional: Skip if both are empty
    // if (!deptcode && !catcode) {
    //     showNoData();
    //     return;
    // }

    // Show loading
    $('#loading_indicator').show();
    $('#tableshow').hide();
    $('#audit_plandetails_wrapper').hide();
    $('#no_data').hide();

    $.ajax({
        url: "/getconsolidationreport",
        type: "POST",
        data: {
            deptcode: deptcode,
            catcode: catcode,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(json) {
            $('#loading_indicator').hide();

            if (json.data) {
                dataFromServer = Array.isArray(json.data) ? json.data : [json.data];

                if (dataFromServer.length > 0) {
                    $('#tableshow').show();
                    $('#audit_plandetails_wrapper').show();
                    $('#no_data').hide();

                    const language = getLanguage('');
                    renderTable(language);
                } else {
                    showNoData();
                }
            } else {
                showNoData();
            }
        },
        error: function(xhr, status, error) {
            $('#loading_indicator').hide();
            console.error("AJAX Error:", error);
            showNoData();
        }
    });
}
        function showNoData() {
            $('#tableshow').hide();
            $('#audit_plandetails_wrapper').hide();
            $('#no_data').show();

            // Destroy DataTable if it exists
            if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                $('#audit_plandetails').DataTable().clear().destroy();
            }
        }
    </script>
@endsection
