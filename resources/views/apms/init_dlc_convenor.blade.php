<? ?>
@section('content')
    @extends('index2')
    @include('common.alert')
    @include('apms.apmsalert')

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

        .dataTables_wrapper {
            width: 100%;
        }

        table.dataTable {
            width: 100% !important;
        }

        #parafull_det tr td {
            border: 0.5px solid #dededf;
            vertical-align: top;
            padding: 5px;
            color: #020202;
        }

        .wrap-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #para_details td,
        #para_details th {
            white-space: normal !important;
            /* allow wrapping */
            word-wrap: break-word;
            word-break: break-word;
        }

        .dt-row-error {
            background-color: #ffe6e6 !important;
        }

        .action-error {
            color: red;
            font-size: 12px;
        }

        /* Limit Select2 dropdown height */
        .select2-container--default .select2-results__options {
            max-height: 140px;
            /* ~4 items (adjust if needed) */
            overflow-y: auto;

        }

        .select2-container--default .select2-selection--multiple {
            max-height: 80px;
            overflow-y: auto;
        }

        /* checkbox */
        /* enabled */
        .custom-checkbox:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        /* Enabled but unchecked */
        .custom-checkbox:not(:disabled) {
            border-color: var(--bs-primary);
        }

        /* Disabled checkbox – dark ash */
        .custom-checkbox:disabled {
            background-color: #dde4ea;
            border-color: #969a9d;
            opacity: 1;
            cursor: not-allowed;
        }

        /* Disabled & checked */
        .custom-checkbox:disabled:checked {
            background-color: #dde4ea;
            border-color: #969a9d;
        }
    </style>
    @php
        $sessionchargedel = session('charge');

        $deptcode = $sessionchargedel->deptcode;

        $sessionroletypecode = $sessionchargedel->roletypecode;
        $dehc_roletypecode = $dehc_roletypecode;
        $shlc_roletypecode = $shlc_roletypecode;

        $roleTypeCode = $sessionchargedel->roletypecode;

        $deptcode = $sessionchargedel->deptcode;
        $regioncode = $sessionchargedel->regioncode;
        $distcode = $sessionchargedel->distcode;

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_region_disable = $regioncode ? 'disabled' : '';
        $make_dist_disable = $distcode ? 'disabled' : '';

    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <div class="col-12">
        <div class="spinner-wrapper " id="loader">
            <div class="dot-spinner">
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
            </div>
        </div>
        <div class="card mt-2" style="border-color: #7198b9">
            <div class="card-header card_header_color lang" key="">List of Para for Approval</div>
            <div class="card-body">
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">

                        <table id="para_details"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">

                            <thead>
                                @csrf
                                <tr>
                                    <th class="lang text-wrap" key="s_no">S.No</th>
                                    <th class="text-wrap" key="dept_details">Department Details</th>
                                    <th class="text-wrap" key="category">Category</th>
                                    <th class="text-wrap" key="Subcategory">Sub Category</th>
                                    <th class="text-wrap" key="mom_details">Minutes of Meeting Details</th>
                                    <th class="lang " key="para_count">Para Count</th>
                                    <th class="all lang" key="action">Action</th>
                                </tr>

                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class=''>
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

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>


    <script>
        //--------------------------on-load--------------------------//
        window.addEventListener('load', function() {

            const loader = document.getElementById('loader');
            if (loader) {
                $('#loader').addClass('d-none');
            }

            var lang = getLanguage('')
            fetch_init_dlcparas(lang)

        });

        //---------------------------Language---------------------//
        $('#translate').change(function() {
            const lang = getLanguage('Y') // Store language selection

        });


        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#para_details')) {
                $('#para_details').DataTable().clear().destroy();
            }
            renderTable(language);
        }

        //----------------------Render Table -------------------------//

        function renderTable(language) {


            const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
            const Category = language === 'ta' ? 'cattname' : 'catename';
            const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';
            const region = language === 'ta' ? 'regiontname' : 'regionename';
            const district = language === 'ta' ? 'disttname' : 'distename';



            if ($.fn.dataTable.isDataTable('#para_details')) {
                $('#para_details').DataTable().clear().destroy();
            }

            dataFromServer.forEach(r => {
                r.selected_action = r.selected_action || '';
            });
            var table = $('#para_details').DataTable({

                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "scrollX": true,
                "initComplete": function(settings, json) {
                    $("#para_details").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                "autoWidth": false,
                "responsive": true,
                "destroy": true, // Destroy and reinitialize
                "data": dataFromServer,


                columns: [{
                        data: "encrypted_paraid",
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                            </div>`;
                        },
                        className: 'text-end',
                        type: "num"
                    },


                    {
                        data: "null",
                        //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                        render: function(data, type, row) {
                            return `<b>Department:</b>${row[Dept]}<br><b>Region:</b>${row[region]}<br><b>District:</b>${row[district]}<br> `;

                            // return row[InstituteName] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "catename",
                        render: function(data, type, row) {

                            return row[Category] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column '
                    },
                    {
                        data: "subcattname",
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            return row[SubCategory] || '-';
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: "null",
                        //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                        render: function(data, type, row) {
                            let mom_date = row.mom_date ? new Date(row.mom_date).toLocaleDateString(
                                    'en-GB') :
                                "N/A";
                            return `<b>Committee Level:</b>${row.roleactionelname}<br><b>Meeting Date:</b>${mom_date}`;

                            // return row[InstituteName] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },


                    {
                        data: "para_count",
                        render: function(data, type, row, meta) {

                            return `<span class="btn bg-primary mt-2 text-light " style="font-size:15px; padding:2px">${row.para_count}</span>`;

                        },
                        className: "text-center text-wrap noExport"
                    },
                    {
                        data: "encrypted_apms_hlcid",
                        render: function(data, type, row, meta) {

                            let url = "{{ route('para_approve', ':id') }}";
                            url = url.replace(':id', row.encrypted_apms_hlcid);

                            return `<a class="btn btn-sm btn-success" href="${url}">
                                         <i class="ti ti-eye fs-4 me-2"></i>
                                         <span key="" class="lang">View</span>
                                     </a>`;
                        },
                        className: "text-center text-wrap noExport"
                    }
                ],

            });


            const mobileColumns = [Dept, region, district, Category, SubCategory, "mom_date", "roleactionelname",
                "para_count"
            ];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "para_details");
        }

        //------------------------------FETCH-------------------------//
        function fetch_init_dlcparas(language) {


            $.ajax({
                url: '/apms/fetch_init_dlcparas', // For creating a new user or updating an existing one
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // data: {},
                success: function(json) {

                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#spara_details_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        dataFromServer = json.data;
                        $('#button_set').show();

                        renderTable(language);
                    } else {
                        $('#tableshow,#button_set').hide();
                        $('#spara_details_wrappers').hide();
                        $('#no_data').show();
                    }

                },
                complete: function() {
                    $('#loader').hide();

                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');

                }

            });
        }
    </script>
@endsection
