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
    </style>
    @php

    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="para_det">Para Management Details</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="audit_plandetails"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            @csrf
                            <tr>
                                <th class="lang text-wrap" key="s_no">S.No</th>
                                <th class="lang text-wrap" key="dept">Department</th>

                                {{-- <th class="lang text-wrap" key="institute_detail">Institute Details</th> --}}
                                <th class="text-wrap" key="cat_det">Category Details</th>
                                <th class="text-wrap" key="typeofaudit">Audit type</th>
                                <th class="lang text-wrap" key="Forwarded Details">Forwarded Details</th>
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
        $(document).ready(function() {


            var lang = getLanguage('') // Default to 'en' if no language is set
            initializeDataTable();

        });

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

        function initializeDataTable() {

            const language = getLanguage('');

            $.ajax({
                url: "/legacy/fetch_instparadetails",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {

                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#saudit_plandetails_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        dataFromServer = json.data;
                        console.log(dataFromServer)
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#saudit_plandetails_wrappers').hide();
                        $('#no_data').show();
                    }
                }
            });

        }

        function renderTable(language) {
            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
            const Category = language === 'ta' ? 'cattname' : 'catename';
            const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';
            const TypeofAudit = language === 'ta' ? 'typeofaudittname' : 'typeofauditename';
            const region = language === 'ta' ? 'regiontname' : 'regionename';
            const district = language === 'ta' ? 'disttname' : 'distename';
            const username = language === 'ta' ? 'usertamilname' : 'username';



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
                "destroy": true, // Destroy and reinitialize
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
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            return row[Dept] || '-';
                        },
                        className: 'text-wrap text-start'
                    },
                    // {
                    //     data: "null",
                    //     //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                    //     render: function(data, type, row) {
                    //         return `<b>Institute:</b>${row[InstituteName]}<br><b>Region:</b>${row[region]}<br>  ${row[SubCategory] ? `<b>District:</b> ${row[district]}<br>` : ""}`;
                    //         // return row[InstituteName] || '-';
                    //     },
                    //     className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    // },
                    {
                        data: "null",
                        //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                        render: function(data, type, row) {
                            return `<b>Category:</b>${row[Category]}<br>  ${row[SubCategory] ? `<b>Sub Category:</b> ${row[SubCategory]}<br>` : ""}`;
                            // return row[InstituteName] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: TypeofAudit,
                        // title: columnLabels?.[TypeofAudit]?.[language] || 'Plan Period',
                        render: function(data, type, row) {

                            return `${row[TypeofAudit]}<br><b>Para Number:</b>${row.paranumber}`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column '
                    },
                    {
                        data: null,

                        render: function(data, type, row) {
                            const actroleaction = row.actroleactioncode;
                            console.log(actroleaction)
                            const roleMap = {
                                'I': 'Auditee',
                                'A': 'PSA Auditor',
                                'AD': 'PSA AD'
                            };

                            const actroleactioncode = roleMap[actroleaction] || '-';

                            let forwardeddate = row.updatedon ? new Date(row.updatedon).toLocaleDateString(
                                    'en-GB') :
                                "N/A";
                            return `<b>Forwarded by:</b>${actroleactioncode}<br><b>Forwarded on:</b>${forwardeddate}`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },

                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {

                            const buttonHtml = `
                                <button class="btn btn-sm bg-success  text-light reply_btn lang"  data-para="${row.encrypted_paraid}"  data-paratype = "${row.paratype}" data-followupid="${row.encrypted_followupid}"  data-instid = "${row.instid}">
                                    Action
                                </button>
                            `;

                            return `
                                <div class="d-flex justify-content-center align-items-center">
                                    ${buttonHtml}
                                </div>
                            `;

                        },
                        className: "text-center text-wrap noExport"
                    }
                ],

            });

            const mobileColumns = [Category, SubCategory];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "audit_plandetails");
        }
        $(document).on('click', '.reply_btn', function() {

            var paraid = $(this).attr('data-para');
            var paratype = $(this).attr('data-paratype');
            var followupid = $(this).attr('data-followupid');
            var instid = $(this).attr('data-instid');
            window.location.href = '/parafollowup/' + paraid + '/' + instid + '/' + paratype + '/' + followupid;


        });
    </script>
@endsection
