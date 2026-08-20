
  @php
        $quarter_det = json_decode(json_encode($quarter_det));
    @endphp


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
    <div class="col-12">
        <div class="card">
            <div class="card-header card_header_color lang" key="auditquarter">Audit Quarter</div>
            <div class="card-body">
                <div class="col-md-4 mb-1 mx-auto">
                    <label class="form-label lang required" key="auditquarter">Audit Quarter</label>

                    <select class="form-select mr-sm-2" id="quartercode" name="quartercode"
                        onchange="initializeDataTable()">


                      @foreach ($quarter_det as $quat)
                            @php
                                $fyText = $quat->planname . ' - ( ' . $quat->financialyear . ' )';
                            @endphp

                            <option value="{{ $quat->planmappingid }}" data-instename="{{ $fyText }}"
                                data-insttname="{{ $fyText }}">
                                {{ $fyText }}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="Auditplandet">Audit Plan Details</div>
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
                                <!-- <th class="lang text-wrap" key="instcat_label">Category</th> -->
                                <th class="lang text-wrap" key="instname_label">Institute</th>

                                <th class="text-wrap" key="audit_team_det">Team Details</th>
                                <th class="text-wrap" key="spillover">Spill Over</th>


                                <!-- <th class="lang text-wrap" key="teammember_label">Team Members</th> -->
                                <th class="lang text-wrap" key="total_mandays">Mandays</th>

                                <th class="lang" key="quarter_label">Quarter</th>
                                <th class="lang" key="proposed_date">Proposed Date</th>
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
        let filteredData;
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
            var quartercode = $('#quartercode').val();
            $.ajax({
                url: "/audit/audit_plandetails",
                type: "POST",
                data: {
                   planmappingid: quartercode,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#saudit_plandetails_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        dataFromServer = json.data;
                        // console.log(dataFromServer)
                        // renderTable(language);
                        let exitMeetNotNull = [];
                        let exitMeetNull = [];
                        let nullCounter = 0;

                        dataFromServer.forEach(row => {
                            if (row.exitmeetdate || (!row.exitmeetdate && row.auditmode == 'P')) {
                                exitMeetNotNull.push(row);
                            } else {
                                if (!row.exitmeetdate && row.parallelteamhead == 'Y' && row.auditmode !=
                                    'P')
                                    exitMeetNull.push(row);
                                if (nullCounter <= 2 && row.parallelteamhead == 'N' && row.auditmode !=
                                    'P') {
                                    exitMeetNull.push(row);
                                    nullCounter++;
                                }
                            }
                        });
                        // const exitMeetNotNull = dataFromServer.filter(r =>
                        //     r.exitmeetdate !== null &&
                        //     r.exitmeetdate !== '' &&
                        //     r.exitmeetdate !== undefined &&
                        //     r.exitmeetdate !== '0000-00-00'
                        // );

                        // const exitMeetNull = dataFromServer.filter(r =>
                        //     r.exitmeetdate === null ||
                        //     r.exitmeetdate === '' ||
                        //     r.exitmeetdate === undefined ||
                        //     r.exitmeetdate === '0000-00-00'
                        // ).slice(0, 1); // ONLY ONE NULL ROW

                        //filteredData = [...exitMeetNotNull, ...exitMeetNull];

                        // Merge the two lists → first all exitmeetdate rows, then up to 3 nulls
                        filteredData = [...exitMeetNotNull, ...exitMeetNull];

                        // Pass this clean list to your renderTable
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#saudit_plandetails_wrappers').hide();
                        $('#no_data').show();
                    }
                }
            });

        }

        // function renderTable(language) {
        //     const InstituteName = language === 'ta' ? 'insttname' : 'instename';
        //     const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
        //     const Category = language === 'ta' ? 'cattname' : 'catename';
        //     const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';

        //     const teamname = language === 'ta' ? 'team_head_ta' : 'team_head_en';
        //     const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';
        //     // const TypeofAudit = language === 'ta' ? 'typeofaudittname' : 'typeofauditename';
        //     const quarterName = language === 'ta' ? 'auditquartertname' : 'auditquarter';

        //     // const spillover = language === 'ta' ? 'spillover' : 'spillover';

        //     const hasIncompleteSpillover = dataFromServer.some(
        //         ap => ap.spilloverflag === 'Y' && ap.exitmeetdate === null
        //     );

        //     // console.log(teamHeadName);

        //     if ($.fn.dataTable.isDataTable('#audit_plandetails')) {
        //         $('#audit_plandetails').DataTable().clear().destroy();
        //     }

        //     var table = $('#audit_plandetails').DataTable({
        //         "processing": true,
        //         "serverSide": false,
        //         "lengthChange": false,
        //         "scrollX": true,
        //         "initComplete": function(settings, json) {
        //             $("#audit_plandetails").wrap(
        //                 "<div style='overflow:auto; width:100%;position:relative;'></div>");
        //         },
        //         "autoWidth": false,
        //         "responsive": true,
        //         "destroy": true, // Destroy and reinitialize
        //         "data": filteredData,
        //         columns: [{
        //                 data: null,
        //                 render: function(data, type, row, meta) {
        //                     return `<div>
        //                         <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
        //                     </div>`;
        //                 },
        //                 className: 'text-end',
        //                 type: "num"
        //             },
        //             {
        //                 data: Dept,
        //                 //title: columnLabels?.[Dept]?.[language] || 'Department',
        //                 render: function(data, type, row) {
        //                     return row[Dept] || '-';
        //                 },
        //                 className: 'text-wrap text-start'
        //             },
        //             // {
        //             //     data: Category,
        //             //     //title: columnLabels?.[Category]?.[language] || 'Category',
        //             //     render: function(data, type, row) {
        //             //         return row[Category] || '-';
        //             //     },
        //             //     className: 'd-none d-md-table-cell lang extra-column text-wrap'
        //             // },
        //             {
        //                 data: "null",
        //                 //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
        //                 render: function(data, type, row) {
        //                     const isperformance = row.auditmode == 'P';
        //                     return `<b>Institute:</b>${row[InstituteName]}<br><b>Category:</b>${row[Category]}<br>  ${row[SubCategory] ? `<b>Sub Category:</b> ${row[SubCategory]}<br>` : ""}${isperformance ? '<span class="btn bg-info text-light" style="font-size:10px; padding:2px">Performance Audit</span>' : ''}`;
        //                     // return row[InstituteName] || '-';
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column text-wrap'
        //             },
        //             // {
        //             //     data: TypeofAudit,
        //             //     // title: columnLabels?.[TypeofAudit]?.[language] || 'Plan Period',
        //             //     render: function(data, type, row) {
        //             //         return row[TypeofAudit] || '-';
        //             //     },
        //             //     className: 'd-none d-md-table-cell lang extra-column '
        //             // },
        //             {
        //                 data: teamname,
        //                 title: columnLabels?.["teamname"]?.[language] || 'Team head',
        //                 render: function(data, type, row) {
        //                     return `<b>Team Head:</b>${row[teamname]}<br><b>Team Members:</b>${row[teammembername]}`;

        //                     // return row[teamname] || '-';
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column text-wrap'
        //             },
        //             // {
        //             //     data: teammembername,
        //             //     //title: columnLabels?.["teamname"]?.[language] || 'Team Name',
        //             //     render: function(data, type, row) {
        //             //         return row[teammembername] || '-';
        //             //     },
        //             //     className: 'd-none d-md-table-cell lang extra-column'
        //             // },
        //             {
        //                 data: "spillover",
        //                 //title: columnLabels?.["teamname"]?.[language] || 'Team Name',
        //                 render: function(data, type, row) {
        //                     let spillover_en = '-';
        //                     let spillover_ta = '-';

        //                     if (row.spillover === 'Y') {
        //                         spillover_en = 'Yes';
        //                         spillover_ta = 'ஆம்';
        //                     } else {
        //                         spillover_en = 'No';
        //                         spillover_ta = 'இல்லை';
        //                     }

        //                     return language == 'ta' ? spillover_ta : spillover_en || '-';
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column'
        //             },
        //             {
        //                 data: "mandays",
        //                 //title: columnLabels?.["teamname"]?.[language] || 'Team Name',
        //                 render: function(data, type, row) {
        //                     let workingDays = '-';

        //                     if (row.spillover === 'Y') {
        //                         workingDays = row.remainingmandays;
        //                     } else {
        //                         workingDays = row.mandays;
        //                     }

        //                     return workingDays || '-';
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column'
        //             },
        //             {
        //                 data: quarterName,
        //                 //title: columnLabels?.["teamname"]?.[language] || 'Team Name',
        //                 render: function(data, type, row) {
        //                     return row[quarterName] || '-';
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column text-wrap'
        //             },
        //             {
        //                 data: null,
        //                 //title: columnLabels?.["teamname"]?.[language] || 'Team Name',
        //                 render: function(data, type, row) {
        //                     let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
        //                             'en-GB') :
        //                         "N/A";
        //                     let todate = row.todate ? new Date(row.todate).toLocaleDateString(
        //                             'en-GB') :
        //                         "N/A";

        //                     return ` ${fromdate} - ${todate}`;
        //                 },
        //                 className: 'd-none d-md-table-cell lang extra-column text-wrap'
        //             },
        //             {
        //                 data: "encrypted_auditplanid",
        //                 render: function(data, type, row) {
        //                     const userid = row.deptuserid;
        //                     const spilloverflag = row.spilloverflag;
        //                     const scheduleid = row.encrypted_auditscheduleid;
        //                     const planid = row.encrypted_auditplanid;
        //                     const instid = row.encrypted_instid;
        //                     const scheduleLabel = language === 'ta' ? 'திட்டமிட வேண்டும்' :
        //                         'To be Scheduled';
        //                     const scheduledText = language === 'ta' ? 'Scheduled' : 'Scheduled';
        //                     const auditcompleteText = language === 'ta' ? 'தணிக்கை முடிந்தது' :
        //                         'Audit Completed';
        //                     const DraftText = language === 'ta' ? 'வரைவு சேமிக்கப்பட்டது' : 'Draft Saved';
        //                     const spilloverText = language === 'ta' ? 'வரைவு சேமிக்கப்பட்டது' :
        //                         'Spill over';
        //                     let exitstatus = row.exitmeetdate !== null;
        //                     const parallelteamhead = row.parallelteamhead;

        //                     const schedule_freezedata = row.freezescheduledate;
        //                     const today = new Date();
        //                     const todayFormatted =
        //                         today.getFullYear() + '-' +
        //                         String(today.getMonth() + 1).padStart(2, '0') + '-' +
        //                         String(today.getDate()).padStart(2, '0');

        //                     const isfreezeschedule = (todayFormatted >= schedule_freezedata);

        //                     let isdisable = false;

        //                     let buttonStyle = '';
        //                     let buttonText = '';
        //                     let buttonClass = '';

        //                     if (row.exitmeetdate) {
        //                         buttonText = auditcompleteText;
        //                         buttonStyle =
        //                             'border:#795548 !important;background-color: #795548 !important;color:white !important;';
        //                     } else if (row.schedule_status === 'Y' && (row.activequartercode == row
        //                             .auditquartercode && (row.freezeschedule == 'Y'))) {

        //                         buttonClass = 'btn-warning';
        //                         buttonText = DraftText;
        //                         if (hasIncompleteSpillover && spilloverflag != 'Y') {
        //                             isdisable = true;
        //                         }
        //                     } else if (row.schedule_status === 'Y' && (row.activequartercode != row
        //                             .auditquartercode)) {
        //                         buttonClass = 'btn-warning';
        //                         buttonText = DraftText;

        //                         isdisable = true;


        //                     } else if (row.schedule_status === 'F' && (row.activequartercode == row
        //                             .auditquartercode) && (row.freezeschedule == 'Y')) {
        //                         buttonClass = 'btn-success';
        //                         schedule_label = spilloverflag == 'Y' ? spilloverText : scheduledText;
        //                         buttonText = schedule_label;
        //                         if (hasIncompleteSpillover && spilloverflag != 'Y') {
        //                             isdisable = true;
        //                         }
        //                     } else if (row.schedule_status === 'F' && (row.activequartercode != row
        //                             .auditquartercode)) {
        //                         buttonClass = 'btn-success';
        //                         schedule_label = spilloverflag == 'Y' ? spilloverText : scheduledText;
        //                         buttonText = schedule_label;
        //                         isdisable = true;

        //                     } else if (row.activequartercode != row.auditquartercode) {

        //                         schedule_label = spilloverflag == 'Y' ? spilloverText : scheduleLabel;
        //                         buttonText = schedule_label;
        //                         buttonClass = 'btn-primary';
        //                         if (hasIncompleteSpillover && spilloverflag != 'Y') {
        //                             isdisable = true;
        //                         }
        //                         isdisable = true;
        //                     } else {

        //                         schedule_label = spilloverflag == 'Y' ? spilloverText : scheduleLabel;
        //                         buttonText = schedule_label;
        //                         buttonClass = 'btn-primary';
        //                         if (hasIncompleteSpillover && spilloverflag != 'Y') {
        //                             isdisable = true;
        //                         }

        //                     }

        //                     if (hasIncompleteSpillover && spilloverflag != 'Y' && parallelteamhead == 'Y' &&
        //                         !row.exitmeetdate) {
        //                         isdisable = false;
        //                     }

        //                     if (isfreezeschedule) {
        //                         isdisable = true;
        //                     }

        //                     /*if ( spilloverflag == 'Y' ) {
        //                              isdisable = true;
        //                         }*/

        //                     const buttonHtml = `
        //                         <button class="btn btn-sm ${buttonClass} schedule_btn lang" data-schedule="${row.schedule_status}" style="${buttonStyle}" id="${data}" data-planid="${planid}" data-userid="${userid}" data-scheduleid="${scheduleid}" data-instid="${instid}" data-spillover="${spilloverflag}" ${isdisable ? 'disabled' : ''}>
        //                             ${buttonText}
        //                         </button>
        //                     `;

        //                     return `
        //                         <div class="d-flex justify-content-center align-items-center">
        //                             ${buttonHtml}
        //                         </div>
        //                     `;

        //                 },
        //                 className: "text-center text-wrap noExport"
        //             }
        //         ],

        //     });

        //     const mobileColumns = [Category, SubCategory, InstituteName, teamname, teammembername, "spillover",
        //         "mandays", quarterName
        //     ];
        //     setupMobileRowToggle(mobileColumns);
        //     updatedatatable(language, "audit_plandetails");
        // }
        function renderTable(language) {
            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
            const Category = language === 'ta' ? 'cattname' : 'catename';
            const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';

            const teamname = language === 'ta' ? 'team_head_ta' : 'team_head_en';
            const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';
            const quarterName = language === 'ta' ? 'auditquartertname' : 'auditquarter';
            //const hasIncompleteSpillover = dataFromServer.some(
             //   ap => ap.spilloverflag === 'Y' && ap.exitmeetdate === null
           // );

            const hasIncompleteSpillover = false;

            // console.log(teamHeadName);

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
                "data": filteredData,
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
                            const isperformance = row.auditmode == 'P';
                            return `<b>Institute:</b>${row[InstituteName]}${row.datafromapi === 'Y' ? '<span class="badge rounded-pill bg-success ms-2">ERP</span>' : ''}<br><b>Category:</b>${row[Category]}<br>  ${row[SubCategory] ? `<b>Sub Category:</b> ${row[SubCategory]}<br>` : ""}${isperformance ? '<span class="btn bg-info text-light" style="font-size:10px; padding:2px">Performance Audit</span>' : ''}`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },

                    {
                        data: teamname,
                        title: columnLabels?.["teamname"]?.[language] || 'Team head',
                        render: function(data, type, row) {
                            return `<b>Team Head:</b>${row[teamname]}<br><b>Team Members:</b>${row[teammembername]}`;

                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },

                    {
                        data: "spillover",
                        render: function(data, type, row) {
                            let spillover_en = '-';
                            let spillover_ta = '-';

                            if (row.spillover === 'Y') {
                                spillover_en = 'Yes';
                                spillover_ta = 'ஆம்';
                            } else {
                                spillover_en = 'No';
                                spillover_ta = 'இல்லை';
                            }

                            return language == 'ta' ? spillover_ta : spillover_en || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column'
                    },
                    {
                        data: "mandays",
                        render: function(data, type, row) {
                            let workingDays = '-';

                            if (row.spillover === 'Y') {
                                workingDays = row.remainingmandays;
                            } else {
                                workingDays = row.mandays;
                            }

                            return workingDays || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column'
                    },
                    {
                        data: "planname",
                        render: function(data, type, row) {
                            return row.planname || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
                                    'en-GB') :
                                "N/A";
                            let todate = row.todate ? new Date(row.todate).toLocaleDateString(
                                    'en-GB') :
                                "N/A";

                            return ` ${fromdate} - ${todate}`;
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "encrypted_auditplanid",
                        render: function(data, type, row) {
                            const userid = row.deptuserid;
                            const spilloverflag = row.spilloverflag;
                            const scheduleid = row.encrypted_auditscheduleid;
                            const planid = row.encrypted_auditplanid;
                            const instid = row.encrypted_instid;
                            const scheduleLabel = language === 'ta' ? 'திட்டமிட வேண்டும்' :
                                'To be Scheduled';
                            const scheduledText = language === 'ta' ? 'Scheduled' : 'Scheduled';
                            const auditcompleteText = language === 'ta' ? 'தணிக்கை முடிந்தது' :
                                'Audit Completed';
                            const DraftText = language === 'ta' ? 'வரைவு சேமிக்கப்பட்டது' : 'Draft Saved';
                            const spilloverText = language === 'ta' ? 'வரைவு சேமிக்கப்பட்டது' :
                                'Spill over';
                            let exitstatus = row.exitmeetdate !== null;
                            const parallelteamhead = row.parallelteamhead;

                            const schedule_freezedata = row.freezescheduledate;
                            const today = new Date();
                            const todayFormatted =
                                today.getFullYear() + '-' +
                                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                                String(today.getDate()).padStart(2, '0');

                            const isfreezeschedule = (todayFormatted >= schedule_freezedata);

                            let isdisable = false;

                            let buttonStyle = '';
                            let buttonText = '';
                            let buttonClass = 'btn-primary';

                            let isActivePlan = row.activeplan == row.planmappingid;
                            let isScheduledY = row.schedule_status === 'Y';
                            let isScheduledF = row.schedule_status === 'F';
                            let isExitMeet = !!row.exitmeetdate;
                            let isSpillover = spilloverflag == 'Y';

                            // default
                            buttonText = isSpillover ? spilloverText : scheduleLabel;

                            // 🔹 Exit meeting completed
                            if (isExitMeet) {
                                buttonText = auditcompleteText;
                                buttonStyle =
                                    'border:#795548 !important;background-color:#795548 !important;color:white !important;';
                            }

                            // 🔹 Draft (Schedule Y)
                            else if (isScheduledY) {
                                buttonClass = 'btn-warning';
                                buttonText = DraftText;

                                if (!isActivePlan) {
                                    isdisable = true;
                                }
                            }

                            // 🔹 Scheduled (Schedule F)
                            else if (isScheduledF) {
                                buttonClass = 'btn-success';
                                buttonText = isSpillover ? spilloverText : scheduledText;

                                if (!isActivePlan) {
                                    isdisable = true;
                                }
                            }

                            // 🔹 Not active plan
                            else if (!isActivePlan) {
                                isdisable = true;
                            }

                            // 🔹 Spillover incomplete restriction
                            if (hasIncompleteSpillover && !isSpillover) {
                                isdisable = true;
                            }

                            // 🔹 Parallel team head override
                            if (
                                hasIncompleteSpillover &&
                                !isSpillover &&
                                parallelteamhead == 'Y' &&
                                !isExitMeet
                            ) {
                                isdisable = false;
                            }

                            // 🔹 Freeze schedule (final override)
                            if (isfreezeschedule) {
                                isdisable = true;
                            }
// if(spilloverflag =='Y')
// {
// 
// }
//isdisable = true;
                            const buttonHtml = `
                               <button class="btn btn-sm ${buttonClass} schedule_btn lang"  data-api="${row.datafromapi}" data-schedule="${row.schedule_status}" style="${buttonStyle}" id="${data}" data-planid="${planid}" data-userid="${userid}" data-scheduleid="${scheduleid}" data-instid="${instid}" data-spillover="${spilloverflag}" ${isdisable ? 'disabled' : ''}> ${buttonText}
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

            const mobileColumns = [Category, SubCategory, InstituteName, teamname, teammembername, "spillover",
                "mandays", quarterName
            ];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "audit_plandetails");
        }

        $(document).on('click', '.schedule_btn', function() {
            var schedule_status = $(this).attr('data-schedule');
            var id = $(this).attr('id'); // Getting id of the clicked button (which is auditplanid)
            var userid = $(this).attr('data-userid');
            var scheduleid = $(this).attr('data-scheduleid');
            var spilloverflag = $(this).attr('data-spillover');
            var instid = $(this).attr('data-instid');
            var planid = $(this).attr('data-planid');
            var datafromapi = $(this).attr('data-api');

if (datafromapi == 'Y' && schedule_status != 'F') {
                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    $('#confirmation_alert').modal('hide');
                    $('#process_button').attr('disabled', true);
                    automateschedule(planid, instid, userid)
                });


                passing_alert_value('Confirmation', 'Are you sure to automate the Schedule ?',
                    'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');


            } else if ((spilloverflag == 'Y' && schedule_status == 'F') || (schedule_status == 'F' &&
                    datafromapi == 'Y')) {          
                        window.location.href = '/init_fieldaudit';

            } else if (spilloverflag == 'Y' && schedule_status != 'F') {

                // return;
                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    $('#confirmation_alert').modal('hide');
                    $('#process_button').attr('disabled', true);
                    window.location.href = '/spillover_schedule?id=' + instid + '&planid=' + planid;
                    //  updateschedule_details(scheduleid, id)
                });

                getLabels_jsonlayout([{
                    id: 'spillover_schedule',
                    key: 'spillover_schedule'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .spillover_schedule, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });



                //window.location.href = '/init_fieldaudit';
            } else {

                window.location.href = '/audit_datefixing?auditplanid=' + id + '&userid=' + userid;
            }

        });


        function automateschedule(planid, instid, userid) {

            console.log(planid, instid, userid);

            $('#process_button').attr('disabled', true);

            $.ajax({
                url: "/audit/automateschedule",
                type: "POST",
                data: {
                    auditplanid: planid,
                    instid: instid,
                    userid: userid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {

                    if (response.status === 'success') {
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
$('#ok_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            location.reload();
                        });

                    } else {
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    }
                },

                error: function(xhr) {
                    let msg = "Something went wrong";

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    passing_alert_value('Confirmation', msg,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                },

                complete: function() {
                    $('#process_button').removeAttr('disabled');
                }
            });
        }

        function updateschedule_details(scheduleid, planid) {
            $.ajax({
                url: "/audit/updateschedule_details",
                type: "POST",
                data: {
                    auditscheduleid: scheduleid,
                    auditplanid: planid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert()
                },
                complete: function() {
                    $('#process_button').removeAttr('disabled');
                }
            });
        }
    </script>
@endsection
