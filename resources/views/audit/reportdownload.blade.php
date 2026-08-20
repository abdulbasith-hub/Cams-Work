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

        #financialyearcode,
        #financialyearcode option,
        #quartercode,
        #quartercode option {
            font-family: Consolas, "Courier New", monospace;
        }

        .disabledanch {
            pointer-events: none;
            /* disables click */
            opacity: 0.6;
            /* visual feedback */
            cursor: not-allowed;
        }

        .form-check-input {
            border: 1.25px solid #2d4e86;
        }

        /* From Uiverse.io by abrahamcalsin */
        .spinner-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            background-color: rgba(28, 28, 28, 0.18);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;

        }

        .dot-spinner {
            --uib-size: 2.8rem;
            --uib-speed: .9s;
            --uib-color: #183153;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: var(--uib-size);
            width: var(--uib-size);
        }

        .dot-spinner__dot {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            width: 100%;
        }

        .dot-spinner__dot::before {
            content: '';
            height: 20%;
            width: 20%;
            border-radius: 50%;
            background-color: var(--uib-color);
            transform: scale(0);
            opacity: 0.5;
            animation: pulse0112 calc(var(--uib-speed) * 1.111) ease-in-out infinite;
            box-shadow: 0 0 20px rgba(18, 31, 53, 0.3);
        }

        .dot-spinner__dot:nth-child(2) {
            transform: rotate(45deg);
        }

        .dot-spinner__dot:nth-child(2)::before {
            animation-delay: calc(var(--uib-speed) * -0.875);
        }

        .dot-spinner__dot:nth-child(3) {
            transform: rotate(90deg);
        }

        .dot-spinner__dot:nth-child(3)::before {
            animation-delay: calc(var(--uib-speed) * -0.75);
        }

        .dot-spinner__dot:nth-child(4) {
            transform: rotate(135deg);
        }

        .dot-spinner__dot:nth-child(4)::before {
            animation-delay: calc(var(--uib-speed) * -0.625);
        }

        .dot-spinner__dot:nth-child(5) {
            transform: rotate(180deg);
        }

        .dot-spinner__dot:nth-child(5)::before {
            animation-delay: calc(var(--uib-speed) * -0.5);
        }

        .dot-spinner__dot:nth-child(6) {
            transform: rotate(225deg);
        }

        .dot-spinner__dot:nth-child(6)::before {
            animation-delay: calc(var(--uib-speed) * -0.375);
        }

        .dot-spinner__dot:nth-child(7) {
            transform: rotate(270deg);
        }

        .dot-spinner__dot:nth-child(7)::before {
            animation-delay: calc(var(--uib-speed) * -0.25);
        }

        .dot-spinner__dot:nth-child(8) {
            transform: rotate(315deg);
        }

        .dot-spinner__dot:nth-child(8)::before {
            animation-delay: calc(var(--uib-speed) * -0.125);
        }

        @keyframes pulse0112 {

            0%,
            100% {
                transform: scale(0);
                opacity: 0.5;
            }

            50% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
    @php
        $session = session('charge');
        $usertypecode = $session->usertypecode;
        $maxQuarterLabelLength = collect($quatdetails)->map(function ($quat) {
            return mb_strlen($quat->planname ?? $quat->auditquarter ?? '');
        })->max() ?? 0;
        $financialYears = collect($quatdetails)
            ->pluck('financialyear')
            ->filter()
            ->unique()
            ->values();

    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <div class="spinner-wrapper d-none" id="loader">
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
    <div class="col-12">
        <div class="card">
            <div class="card-header card_header_color lang" key="auditquarter">Audit Filters</div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-1">
                        <label class="form-label required">Financial Year</label>
                        <select class="form-select mr-sm-2" id="financialyearcode" name="financialyearcode">
                            @foreach ($financialYears as $financialYear)
                                <option value="{{ $financialYear }}">{{ $financialYear }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-1">
                        <label class="form-label lang required" key="auditquarter">Audit Quarter</label>
                        <select class="form-select mr-sm-2" id="quartercode" name="quartercode"></select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">Download Audit Report</div>
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
        let dataFromServer;
        const quarterOptions = @json(collect($quatdetails)->values());

        $(document).ready(function() {
            var lang = getLanguage('');
            populateQuarterDropdown();
            initializeDataTable();
        });

        $('#financialyearcode').on('change', function() {
            populateQuarterDropdown();
            initializeDataTable();
        });

        $('#quartercode').on('change', function() {
            initializeDataTable();
        });

        function populateQuarterDropdown() {
            const selectedFinancialYear = $('#financialyearcode').val();
            const quarterDropdown = $('#quartercode');
            let filteredQuarters = quarterOptions
                .filter(item => item.financialyear === selectedFinancialYear);

            if (filteredQuarters.length > 4) {
                filteredQuarters = filteredQuarters.sort((a, b) => {
                    const planMappingA = parseInt(a.planmappingid ?? a.optionvalue ?? 0, 10) || 0;
                    const planMappingB = parseInt(b.planmappingid ?? b.optionvalue ?? 0, 10) || 0;
                    return planMappingA - planMappingB;
                });
            }

            quarterDropdown.empty();

            filteredQuarters.forEach(item => {
                const quarterLabel = item.planname ?? item.auditquarter ?? '';

                quarterDropdown.append(
                    $('<option>', {
                        value: item.optionvalue ?? item.planmappingid ?? item.auditquartercode,
                        text: quarterLabel,
                        'data-quartercode': item.auditquartercode,
                        'data-instename': item.planname ?? item.auditquarter,
                        'data-insttname': item.planname ?? item.auditquarter,
                    })
                );
            });
        }

        function initializeDataTable() {
            const language = getLanguage('');

            const selectedOption = $('#quartercode option:selected');
            var quartercode = selectedOption.data('quartercode');
            var auditplanmappingid = $('#quartercode').val();

            if (!quartercode || !auditplanmappingid) {
                $('#tableshow').hide();
                $('#audit_plandetails_wrapper').hide();
                $('#no_data').show();
                return;
            }

            $.ajax({
                url: "/report/get_listinstitututes",
                type: "POST",
                data: {
                    quartercode: quartercode,
                    auditplanmappingid: auditplanmappingid,
                    formname: 'downloadreport'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#audit_plandetails_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message

                        dataFromServer = json.data;
                        console.log(dataFromServer)
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#audit_plandetails_wrapper').hide();
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
                            const usertypecode = '<?php echo $usertypecode; ?>'; // this is okay, injected once
                            const isdisabled = row.issuedflag === 'Y'; // client-side check

                            let html = `

                              <center>
                                  <a class="btn btn-sm btn-primary"
                                     onclick="downloadreport('${row.encrypted_auditscheduleid}','${row.instid}','${row.financialyearcode}','${row.spilloverflag}','I','download')">
                                     <i class="ti ti-download fs-4 me-2"></i>
                                     Download Report
                                  </a>
                                </center>

                                <br>

                                <center>
                                  <a class="btn btn-sm btn-primary"
                                     onclick="downloadreport('${row.encrypted_auditscheduleid}','${row.instid}','${row.financialyearcode}','${row.spilloverflag}','II','download_attachments')">
                                     <i class="ti ti-download fs-4 me-2"></i>
                                     Download Attachments
                                  </a>
                                </center>
                             `;

                            if (usertypecode === 'A') {
                                html += `
                                     <br><center>   <a class="btn btn-sm btn-primary send-mail-btn ${isdisabled ? 'disabledanch' : ''}"
                                           data-id="${row.encrypted_auditscheduleid}" onclick="issuereport('${row.encrypted_auditscheduleid}')">
                                           <i class="fa fa-envelope fs-4 me-2"></i> Issue Report
                                        </a></center>
                                    `;
                            }

                            return html;
                        },

                        className: 'd-none d-md-table-cell lang extra-column'
                    }                ]
            });

            updatedatatable(language, "audit_plandetails");
        }


        function sentauditeemail(scheduleid) {

            // var isChecked = $('#usercheck').prop('checked');
            // if (isChecked) {
            $.ajax({
                url: '/sendauditeemail',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    schedule_id: scheduleid
                },
                success: function(response) {
                    if (response.success) {
                        passing_alert_value(
                            'Success',
                            response.message,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );

                        $('#ok_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            location.reload();
                        });
                    }


                },

                error: function(xhr, status, error) {


                    var response = JSON.parse(xhr.responseText);
                    var errorMessage = response.error || 'Error Occured';

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');

                    console.error('Error details:', xhr, status, error);
                }
            });
            // } else {

            //     $('#confirmation_alert').modal('show');
            //     passing_alert_value('Confirmation', 'Please verify the downlaod status', 'confirmation_alert',
            //         'alert_header', 'alert_body',
            //         'confirmation_alert');

            // }

        }




function downloadreport(scheduleid, instid, financialyearcode, spilloverflag, part, action) {

            $('#loader').removeClass('d-none');

            var audit_scheduleid = scheduleid
            var lang = getLanguage('Y');
            $.ajax({
                url: '{{ route('download.report') }}',
                method: 'POST',
                data: {
                    audit_scheduleid: audit_scheduleid,
                    lang: lang,
                    _token: '{{ csrf_token() }}',
                    instid: instid,
                    part: part,
                    action: action,
                    financialyearcode: financialyearcode,
                    spilloverflag: spilloverflag
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    var responseType = xhr.getResponseHeader('Content-Type') || 'application/octet-stream';
                    if (xhr.status === 202 || responseType.indexOf('application/json') !== -1) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            let response = {};
                            try {
                                response = JSON.parse(reader.result || '{}');
                            } catch (e) {}

                            passing_alert_value(
                                'Confirmation',
                                response.message || 'Attachment PDF is being prepared. Please wait...',
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'confirmation_alert'
                            );

                            setTimeout(function() {
                                downloadreport(scheduleid, instid, financialyearcode, spilloverflag,
                                    part, action);
                            }, 10000);
                        };
                        reader.readAsText(data);
                        return;
                    }

                    var blob = new Blob([data], {
                        type: responseType
                    });
                    var downloadUrl = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = downloadUrl;

                    // Optional: dynamic filename from content-disposition header
                    var filename = "AuditReport.pdf";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        filename = disposition.split('filename=')[1].replace(/"/g, '');
                    }

                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(downloadUrl);
                    passing_alert_value('Confirmation', 'Audit Report Downloaded Successfully!',
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                },
                complete: function() {
                    $('#loader').addClass('d-none');
                },
                error: function(xhr) {
                    $('#loader').addClass('d-none');
                    if (xhr.status === 404) {
                        passing_alert_value('Confirmation', 'File not found', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    } else {
                        passing_alert_value('Confirmation',
                            'An error occurred while downloading the report.', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    }
                }
            });
        }


        function issuereport(scheduleid) {
            var scheduleId = scheduleid;
            content = `<div class="div" id="verify_btn">
                       <div class="col-md-12 mt-4 ">
                         <p id="error_msg" class="hide_this"style="font-weight:normal; color:red"></p>
                           <div class="form-check">
                               <input class="form-check-input" type="checkbox" name="exampleRadios" id="usercheck"
                                   value="option1">


                               <label class="form-check-label lang" for="exampleRadios1">
                                   <b>I hereby downloaded the Audit Report and i verified all the details are correct, and the attachments are properly available
                                       <b>

                               </label>
                           </div>
                       </div>
                    </div>`;

            passing_alert_value('Confirmation', content, 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            $('#process_button').removeAttr('data-bs-dismiss');
            //  $('#process_button').off('click').on('click', function(event) {
            $('#process_button').on('click', function() {
                event.preventDefault();
                $('#confirmation_alert').modal('show'); // Ensure modal is visible if it was hidden
                $('#process_button').removeAttr('data-bs-dismiss', 'modal');

                var isChecked = $('#usercheck').prop('checked');
                if (isChecked) {
                    $('#confirmation_alert').modal('hide'); // Ensure modal is visible if it was hidden
                    $('#process_button').attr('data-bs-dismiss', 'modal');
                    // $('#confirmation_alert').modal('hide');
                    sentauditeemail(scheduleId)
                } else {

                    $('#error_msg').show();
                    $('#error_msg').html('Please verify the downlaod status');

                }
            });
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
