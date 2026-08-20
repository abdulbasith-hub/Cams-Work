@extends('index2')
@section('content')
    @include('common.alert')

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
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>



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

        @supports (-webkit-appearance: none) and (not (-moz-appearance: none)) {
            .select2-container--default .select2-dropdown {
                margin-top: -22px;
            }
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
    </style>
    <div class="col-12">
        <div class="card">
            <div class="card-header card_header_color lang" key="">List of Completed Performance Audit Report
                Process
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Department</label>
                        <select class="form-select select2" id="deptcode" name="deptcode" {{ $make_dept_disable }}>
                            <option value="">---Select Department---</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->deptcode }}"
                                    {{ $deptcode == $department->deptcode ? 'selected' : '' }}>
                                    {{ $department->deptelname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Performance Audit Title</label>
                        <select class="form-select select2" id="audit_title" name="audit_title">
                            <option value="">---Select Audit Title---</option>
                        </select>
                    </div>

                </div>
            </div>

        </div>
    </div>
    </div>

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">List of Completed Performance Audit Report Process
        </div>
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
                                <th lass="lang" key="title">Performance Audit Title</th>
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
    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-advanced.init.js') }}"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>


    <script>
        let dataFromServer;
        let currentFilter = {
            deptcode: '',
            auditperiodid: '',
            praudittitleid: ''
        };
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%'
            });

            var lang = getLanguage('');

            // Initialize with default values if any
            initializeDataTable();

            const deptcode = $('#deptcode').val();
            currentFilter.deptcode = deptcode;

            if (deptcode) {
                loadAuditTitles(deptcode);
            }

            $('#deptcode').on('change', function() {
                const deptcode = $(this).val();
                currentFilter.deptcode = deptcode;

                if (deptcode) {
                    loadAuditTitles(deptcode);
                }
            });
            $('#audit_title').on('change', function() {
                refreshDataTable();
            });

            // Handle department change (if no default deptcode)
            @if (!$deptcode)
                $('#deptcode').on('change', function() {
                    refreshDataTable();
                });
            @endif
        });

        function loadAuditTitles(deptcode) {
            if (!deptcode && $('#deptcode').prop('disabled')) {
                deptcode = $('#deptcode').val();
            }

            if (!deptcode) {
                return;
            }

            $('#audit_title').select2('destroy');

            $.ajax({
                url: "{{ route('Performance.getaudittitles') }}",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const titleDropdown = $('#audit_title');
                    titleDropdown.empty().append('<option value="">---Select Audit Title---</option>');

                    if (response.success && response.data && response.data.length > 0) {
                        response.data.forEach(title => {
                            titleDropdown.append(
                                `<option value="${title.praudittitleid}">${title.titleename}</option>`
                            );
                        });
                    } else {
                        titleDropdown.append('<option disabled>No Audit Titles Available</option>');
                    }

                    // Reinitialize Select2 after options are loaded
                    $('#audit_title').select2({
                        width: '100%'
                    });
                },
                error: function() {
                    $('#audit_title').empty().append('<option value="">---Select Audit Title---</option>');
                    // Reinitialize Select2 even on error
                    $('#audit_title').select2({
                        width: '100%'
                    });
                    showAlert('Error loading audit titles', 'Error');
                }
            });
        }

        function initializeDataTable() {
            const language = getLanguage('');

            var deptcode = $('#deptcode').val();
            var praudittitleid = $('#audit_title').val();

            $.ajax({
                url: "/getprconsolidationreport",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    praudittitleid: praudittitleid,
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
                title: language === 'ta' ? (item.titletname || '-') : (item.titleename || '-'),
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
                        data: "title",
                        className: 'text-wrap'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="openConsolidatedReport('${row.enc_praudittitleid}', '${row.enc_deptcode}', '${row.enc_catcode}','${row.enc_subcatid}','${row.enc_finyear}')">
                                    View Report
                                </button>
                            </div>`;
                        },
                        className: 'text-center'
                    }
                ]
            });

            updatedatatable(language, "audit_plandetails");
        }

        function openConsolidatedReport(praudittitleid, deptcode, catcode, subcatid, finyear) {
            // Using Laravel's route helper via JavaScript
            const url =
                "{{ route('performance.findingsRecommendations', ['praudittitleid' => ':praudittitleid', 'deptcode' => ':deptcode', 'catcode' => ':catcode', 'subcatid' => ':subcatid', 'finyear' => ':finyear']) }}"
                .replace(':praudittitleid', praudittitleid)
                .replace(':deptcode', deptcode)
                .replace(':catcode', catcode)
                .replace(':subcatid', subcatid)
                .replace(':finyear', finyear);

            // Open in the same tab/window
            window.location.href = url;
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
            const praudittitleid = $('#audit_title').val();

            // Optional: Skip if both are empty
            // if (!deptcode && !title) {
            //     showNoData();
            //     return;
            // }

            // Show loading
            $('#loading_indicator').show();
            $('#tableshow').hide();
            $('#audit_plandetails_wrapper').hide();
            $('#no_data').hide();

            $.ajax({
                url: "/getprconsolidationreport",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    praudittitleid: praudittitleid,
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
