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

        $sessionuserchargeid = $sessionchargedel->userchargeid;

        $deptcode = $sessionchargedel->deptcode;

        $sessionroletypecode = $sessionchargedel->roletypecode;
        $district_hlc_roletypecode = $district_hlc_roletypecode;
        $rtd_committee_roleaction = $rtd_committee_roleaction;
        $dehc_roletypecode = $dehc_roletypecode;
        $shlc_roletypecode = $shlc_roletypecode;
        $roleTypeCode = $sessionchargedel->roletypecode;
        $sessionroleactioncode = $sessionchargedel->roleactioncode;

        $deptcode = $sessionchargedel->deptcode;
        $regioncode = $sessionchargedel->regioncode;
        $distcode = $sessionchargedel->distcode;
        $sessioncatcode = $sessionchargedel->catcode ?? null;
        $sessionsubcatid = $sessionchargedel->subcatid ?? null;

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_region_disable = $regioncode ? 'disabled' : '';
        $make_dist_disable = $distcode ? 'disabled' : '';
        $make_cat_disable = $sessioncatcode ? 'disabled' : '';
        $make_subcat_disable = $sessionsubcatid ? 'disabled' : '';

        $apmshlc_data = $apmshlc_data;
        $apmshlc_data_json = json_decode($apmshlc_data, true);

        $subcatid = $apmshlc_data_json[0]['subcatid'];
        $hide_subcat = $subcatid ? `` : 'hide_this';

        $fileData = $apmshlc_data->first()->filedetails ?? '';
        $parts = explode('-', $fileData);
        $filename = $parts[0] ?? '';
        $filepath = $parts[1] ?? '';

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
        <div class="card">

            <div class="card-header lang card_header_color" key=""> DLC Committee Approver</div>

            <div class="card-body">
                <div class="row">
                    <form id="init_approver" name="init_approver">
                        @csrf
                        <input type="hidden" id="apms_hlcid" name="apms_hlcid"
                            value="{{ $apmshlc_data->first()->encrypted_hlcid ?? '' }}">
                        <input type="hidden" id="processcode" name="processcode"
                            value="{{ $apmshlc_data->first()->processcode ?? '' }}">
                        <input type="hidden" id="mom_date_unformatted" name="mom_date_unformatted"
                            value="{{ $apmshlc_data->first()->mom_date ?? '' }}">

                        <input type="hidden" id="rejected_para" name="rejected_para">
                        <input type="hidden" id="approved_para" name="approved_para">

                        <div class="row">

                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="department"
                                    for="validationDefault01">Department</label>
                                <input class="form-control" id="deptcode" name="deptcode" disabled
                                    data-name-en="{{ $apmshlc_data->first()->deptesname ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->deptesname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->deptesname ?? '' }}">

                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <input class="form-control" id="regioncode" name="regioncode" disabled
                                    data-name-en="{{ $apmshlc_data->first()->regionename ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->regiontname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->regionename ?? '' }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <input class="form-control" id="distcode" name="distcode" disabled
                                    data-name-en="{{ $apmshlc_data->first()->distename ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->disttname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->distename ?? '' }}">
                            </div>


                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="category"
                                    for="validationDefault01">Category</label>

                                <input class="form-control" id="catcode" name="catcode" disabled
                                    data-name-en="{{ $apmshlc_data->first()->catename ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->cattname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->catename ?? '' }}">
                            </div>


                            <div class="col-md-4 mb-2 subcatdiv <?php echo $hide_subcat; ?>">
                                <label class="form-label lang required" key="subcategoryColumn"
                                    for="subcatid">SubCategory</label>

                                <input class="form-control" id="subcatid" name="subcatid" disabled
                                    data-name-en="{{ $apmshlc_data->first()->subcatename ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->subcattname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->subcatename ?? '' }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="Convenor Level" for="institution">Convenor
                                    Level</label>
                                <input class="form-control" id="committee_level_name" name="committee_level_name" disabled
                                    data-name-en="{{ $apmshlc_data->first()->roleactionelname ?? '' }}"
                                    data-name-ta="{{ $apmshlc_data->first()->roleactionelname ?? '' }}"
                                    value="{{ $apmshlc_data->first()->roleactionelname ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="mom_date" for="institution">Meeting
                                    Date</label>
                                <input class="form-control" id="mom_date" name="mom_date" disabled
                                    value="{{ $apmshlc_data->first()->mom_date ? \Carbon\Carbon::parse($apmshlc_data->first()->mom_date)->format('d/m/Y') : '' }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="meet_doc" for="institution">Minutes
                                Document</label>
                            <div class="position-relative align-items-stretch ms-2">
                                <div class="card ms-2">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between ms-2">

                                            <div class="d-flex">
                                                <div
                                                    class="p-1 bg-primary-subtle rounded me-6 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-file-text text-primary fs-6"></i>
                                                </div>
                                                <div>
                                                    <!-- Use JavaScript template literals to generate the correct URL -->
                                                    <a class="fs-3 fw-semibold" style="color:black;"
                                                        href="{{ asset($filepath) }}" target="_blank">
                                                        {{ $filename }}
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>



    </div>
    <hr>
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
                                <th class="text-wrap" key="institute_detail">Institute Details</th>
                                <th class="text-wrap" key="audityear">Audit Year</th>
                                <th class="text-wrap" key="para_no">Para Number</th>
                                <th class="lang " key="para_details">View Para Details</th>
                                <th class="lang " key="select_para">Select Para</th>
                                <th class="lang " key="remarks">Remarks</th>
                                <th class="lang " key="action">Action</th>

                            </tr>

                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id='no_data' class='hide_this'>
                <center>No Data Available</center>
            </div>

            <div id="button_set" class="hide_this">
                <div class="row mt-1">
                    <div class="col-md-6" style="margin-left: 39% !important;">
                        <div class="d-flex align-items-center gap-6" id="button_set">
                            <input type="hidden" id="action" name="action" value="insert">
                            <button class="btn button_save" id="buttonaction" name="buttonaction">Save Draft
                            </button>
                            <button class="btn button_finalise" id="finalisebtn">Finalise</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </form>
    </div>



    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    {{-- data table --}}
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
    <script>
        let maxSize = 2;
        var sessiondeptcode = ' <?php echo $deptcode; ?>';
        var sessiondistcode = ' <?php echo $distcode; ?>';
        var sessionregioncode = ' <?php echo $regioncode; ?>';
        let sessioncatcode = ' <?php echo $sessioncatcode; ?>';

        let sessionsubcatid = ' <?php echo $sessionsubcatid; ?>';
        let sessionuserchargeid = ' <?php echo $sessionuserchargeid; ?>';
        let apms_hlc_data = @json($apmshlc_data)


        //---------------------------Language---------------------//
        $('#translate').change(function() {
            const lang = getLanguage('Y') // Store language selection
            // updateTableLanguage(
            //     lang); // Update the table with the new language by destroying and recreating it

            updateValidationMessages(lang, 'init_hlc');

        });


        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#para_details')) {
                $('#para_details').DataTable().clear().destroy();
            }
            renderTable(language);
        }
        //---------------------------Language---------------------//



        //------------------------------Data Table----------------------//


        function renderTable(language) {

            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Irregularities = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';
            const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
            const Category = language === 'ta' ? 'cattname' : 'catename';
            const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';
            const TypeofAudit = language === 'ta' ? 'typeofaudittname' : 'typeofauditename';
            const region = language === 'ta' ? 'regiontname' : 'regionename';
            const district = language === 'ta' ? 'disttname' : 'distename';
            const username = language === 'ta' ? 'usertamilname' : 'username';
            const mainobjectionname = language === 'ta' ? 'objectiontname' : 'objectionename';



            if ($.fn.dataTable.isDataTable('#para_details')) {
                $('#para_details').DataTable().clear().destroy();
            }

            dataFromServer.forEach(r => {
                r.selected_action = r.selected_action || '';
            });
            var table = $('#para_details').DataTable({
                "rowId": 'encrypted_paraid',
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "scrollX": true,
                "scrollCollapse": true,
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
                            return row[InstituteName] || '-';
                            // return row[InstituteName] || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column text-wrap'
                    },
                    {
                        data: "audit_period",
                        render: function(data, type, row) {

                            return row.audit_period || '-';
                        },
                        className: 'd-none d-md-table-cell lang extra-column '
                    },
                    {
                        data: "parano",
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            return row.parano || '-';
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: "encrypted_paraid",
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            const para_btn = language == 'ta' ? '???? ??????????? ?????' :
                                'View Para Details'
                            const det_btn = language == 'ta' ? '?????????? ??????????????' :
                                'Check Flow';
                            const view_det_btn = `<button
                                    type="button" class="btn-sm btn btn-primary enable-row " onclick="get_para_historydetails(this,'view_flow')"  data-para=${row.encrypted_paraid}  data-action="flow" style="font-size:10px">
                                    <i class="ti ti-history fs-2 me-2"></i> ${det_btn}
                                </button><div style="height:5px;"></div>`
                            const buttonHtml = `
                               <button
                        type="button" class="btn-sm btn btn-info enable-row " onclick="get_para_historydetails(this,'view_para')"  data-para=${row.encrypted_paraid}   data-action="para" style="font-size:10px">
                        <i class="ti ti-clipboard-data fs-2 me-2"></i> ${para_btn}
                        </button>
                            `;

                            return `

                                    ${buttonHtml}
                                    <div class="mt-2"></div>
                                    ${view_det_btn}

                            `;
                        },
                        className: 'text-wrap text-start'
                    },

                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {
                            // Ensure rejected_para is an array
                            let rejected = [];
                            if (row.rejected_para) {
                                try {
                                    rejected = JSON.parse(row.rejected_para); // convert string to array
                                } catch (e) {
                                    console.error("Failed to parse rejected_para:", row.rejected_para);
                                }
                            }

                            // Ensure approved_para is an array if needed
                            let approved = [];
                            if (row.approved_para) {
                                try {
                                    approved = JSON.parse(row.approved_para);
                                } catch (e) {}
                            }

                            // Determine which option should be selected
                            let selectedValue = '';
                            if (Array.isArray(rejected) && rejected.includes(row.followupid)) {
                                selectedValue = 'R';
                            } else if (Array.isArray(approved) && approved.includes(row.followupid)) {
                                selectedValue = 'A';
                            }
                            const selectHtml = `
                              <select class="form-select select-para" onchange="handleStatusChange(this)" data-followupid="${row.followupid}">
                                    <option value="A" ${selectedValue === 'A' ? 'selected' : ''}>Approve</option>
                                    <option value="R" ${selectedValue === 'R' ? 'selected' : ''}>Return</option>
                             </select>
                            `;
                            return selectHtml;
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {
                            let rejected = [];
                            if (row.rejected_para) {
                                try {
                                    rejected = JSON.parse(row.rejected_para);
                                } catch (e) {
                                    console.error("Failed to parse rejected_para:", row.rejected_para);
                                }
                            }

                            // enable textarea if followupid is in rejected array
                            let isDisabled = true;
                            if (Array.isArray(rejected) && rejected.includes(row.followupid)) {
                                isDisabled = false;
                            }
                            let remarkValue = "";
                            if (row.approver_remarks) {

                                try {
                                    // approved_remarks is an array of objects: [{followupid, remarks}, ...]
                                    let remarksArray = JSON.parse(row.approver_remarks);
                                    let match = remarksArray.find(r => r.followupid == row.followupid);
                                    if (match) {
                                        remarkValue = match.remarks;
                                    }

                                } catch (e) {
                                    console.error("Failed to parse approved_remarks:", row
                                        .approver_remarks);
                                }
                            }

                            return `<textarea class="form-control remarks-input"
                          data-followupid="${row.followupid}"
                          placeholder="Enter Remarks"
                          minlength="10"
                          maxlength="150"
                          ${isDisabled ? 'disabled' : ''}>${remarkValue}</textarea>`;
                        },
                        className: 'text-wrap text-start'
                    },


                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row, meta) {


                            let rejected = [];
                            if (row.rejected_para) {
                                try {
                                    rejected = JSON.parse(row.rejected_para);
                                } catch (e) {
                                    console.error("Failed to parse rejected_para:", row.rejected_para);
                                }
                            }

                            // enable textarea if followupid is in rejected array
                            let isaction_disabled = false;
                            if (Array.isArray(rejected) && rejected.includes(row.followupid)) {
                                isaction_disabled = true;
                            }


                            let options = `

                            <option value="">---Select Action---</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act->actioncode }}" data-paraid="${row.encrypted_paraid}"
                                    ${row.actioncode == '{{ $act->actioncode }}' ? 'selected' : ''} >
                                    {{ $act->actionename }}
                                </option>
                            @endforeach
                        `;

                            return `<select class="form-select action-select" ${isaction_disabled ? 'disabled' : ''}>${options}</select>`;

                        },
                        className: "text-center text-wrap noExport"
                    },
                ],

            });


            const mobileColumns = [Category, SubCategory, TypeofAudit, "paranumber", "updatedon"];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "para_details");
            initApprovedParas();
        }
        //------------------------------Data Table----------------------//



        //------------------------------Button Click----------------------//
        $(document).on('click', '.reply_btn', function() {

            var paraid = $(this).attr('data-para');
            var paratype = $(this).attr('data-paratype');
            var followupid = $(this).attr('data-followupid');
            var instid = $(this).attr('data-instid');
            // window.location.href = '/paramanagement_hlc/' + paraid + '/' + instid + '/' + paratype + '/' +
            //     followupid;


        });

        $(document).on('click', '.view_detail', function() {

            var paraid = $(this).attr('data-para');

            const language = getLanguage('');

            $.ajax({
                url: "/apms/fetch_parahistory",
                type: "POST",
                data: {
                    paraid: paraid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var para_det = response.data;

                    if (para_det.length > 0) {
                        populate_paradetails(para_det)
                    }
                }
            });


        });


        //table data submit





        //------------------------------Button Click----------------------//

        //------------------------------Populate History Data-------------------------//


        function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
            const $container = $('#' + containerid).empty();


            files.forEach(file => {
                if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);

                const fileCard = `
            <div class="position-relative align-items-stretch ms-2" ${action === 'edit' ? `id="file-card-${file.fileuploadid}"` : ''}>
                <div class="card ms-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between ms-2">
                            ${action === 'edit' ? `<input type="hidden" id="fileuploadid_${file.fileuploadid}" name="fileuploadid_${file.fileuploadid}" value="${file.fileuploadid}">` : ''}
                            <div class="d-flex">
                                <div class="p-1 bg-primary-subtle rounded me-6 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-file-text text-primary fs-6"></i>
                                </div>
                                <div>
                                    <!-- Use JavaScript template literals to generate the correct URL -->
                                    <a class="fs-3 fw-semibold" style="color:black;" href="/${file.path}" target="_blank">${file.name}</a>
                                </div>
                            </div>
                            ${action === 'edit' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          <div class="bg-danger-subtle badge ms-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <span class="fs-5 text-danger fw-semibold mb-0"><i class="ti ti-trash" onclick="deleteFile(${file.fileuploadid}, event)"></i></span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ` : ''}
                        </div>
                    </div>
                </div>
            </div>`;

                $container.append(fileCard);
                // fileCount++;
            });


            if (uploadidstatus == 'Y') {

                if (fileCount < maxfilecount) {

                    $('#add-file-btn').show();
                    $('#file-input-container').show();
                } else {
                    $('#add-file-btn').hide();
                }

            }
        }
        //------------------------------Populate History Data-------------------------//

        //------------------------------Validator-------------------------//
        jsonLoadedPromise.then(() => {
            const language = getLanguage('')
            var validator = $("#init_hlc").validate({

                rules: {
                    deptcode: {
                        required: true,
                    },
                    catcode: {
                        required: true
                    },
                    subcatid: {
                        required: true
                    },
                    regioncode: {
                        required: true
                    },
                    distcode: {
                        required: true
                    },
                    // instid: {
                    //     required: true
                    // },
                    mom_date: {
                        required: true
                    },
                    // file: {
                    //     required: true
                    // },

                },
                errorPlacement: function(error, element) {
                    // For datepicker fields inside input-group, place error below the input group
                    if (element.hasClass('datepicker')) {
                        // Insert the error message after the input-group, so it appears below the input and icon
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2')) {
                        error.insertAfter(element.next(
                            '.select2-container')); // Fix for Select2 dropdowns
                    } else {
                        // For other elements, insert the error after the element itself
                        error.insertAfter(element);
                    }

                }

            });


            $("#mom_submit").on("click", function(event) {
                event.preventDefault();

                if ($("#init_hlc").valid()) {

                    var lang = getLanguage('')
                    fetch_parastatus(lang)

                } else {
                    console.log("Form validation failed.");
                }
            });




        });
        //------------------------------Validator-------------------------//

        function getfile(filearray) {
            return files = filearray.split(',').map((fileDetail, index) => {
                const [name, path, size, fileuploadid] = fileDetail.split('-');
                return {
                    id: index + 1,
                    name,
                    path,
                    size,
                    fileuploadid
                };
            });
        }

        //------------------------------FETCH-------------------------//
        function fetch_parastatus(language) {

            var apms_hlcid = $('#apms_hlcid').val()

            $.ajax({
                url: "/apms/fetch_parastatus_hlc",
                type: "POST",
                data: {
                    apms_hlcid: apms_hlcid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {

                    if (json.data && json.data.length > 0) {


                        $('#tableshow').show();
                        $('#spara_details_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        dataFromServer = json.data;

                        if (sessionuserchargeid == apms_hlc_data[0].updatedbyuserchargeid) {
                            change_button_as_update('init_approver', 'action', 'buttonaction', 'display_error',
                                '',
                                '');
                        }
                        $('#button_set').show();

                        if (json.hlcdata && json.hlcdata.length > 0) {
                            const hlcdata = json.hlcdata[0]
                            $('#apms_hlcid').val(hlcdata['encrypted_hlcid']);
                            if (hasValue(hlcdata['mom_date'])) {
                                datepicker('mom_date', convertDateFormatYmd_ddmmyy(hlcdata[
                                    'mom_date']));
                            }
                            //fit file details

                            if (hasValue(hlcdata['filedetails'])) {

                                $('#view_file-list-container').show();

                                var fileParts = hlcdata['filedetails'].split('-');

                                if (fileParts.length === 4) {

                                    var files = {
                                        id: 1,
                                        name: fileParts[0],
                                        path: fileParts[1],
                                        size: fileParts[2],
                                        fileuploadid: fileParts[3]
                                    };

                                    view_files(files);
                                }
                            }

                        } else {


                        }
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
                error: function() {
                    alert('Error fetching meeting details. Please try again.');
                }
            });
        }


        //------------------------------FETCH-------------------------//
        function reset_form() {

            $('#init_hlc').validate().resetForm();
            $('#mom_date').val('');
            remove_file()
        }

        function scrollToFirstError() {
            var $hlc_form = $('#init_hlc');
            const firstError = $hlc_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
        //validation messges
        function applyValidationToNewFields(inputName, message) {
            let $input = $("[name='" + inputName + "']"); // Select input by name
            if ($input.length) {

                let validator = $("#mapinst_form").data("validator"); // Get validator instance

                if (!validator) {

                    $("#mapinst_form").validate({ // Initialize validation if not  done
                        errorPlacement: function(error, element) {
                            // Check if the element has the 'datepicker' class
                            if (element.hasClass('datepicker')) {
                                // Insert the error message after the input-group to display it below the input and icon
                                error.insertAfter(element.closest('.input-group'));
                            } else {
                                // Default behavior: insert the error message after the input field
                                error.insertAfter(element);
                            }
                        }
                    });
                    validator = $("#mapinst_form").data("validator");
                }

                $input.rules("remove"); // Remove any existing validation rules

                // Ensure rules are applied only once
                $input.rules("add", {
                    required: true,
                    messages: {
                        required: message // Custom error message for the required rule
                    }
                });

                validator.element($input); // Validate the element

                // ? Ensure validation runs on change without removing existing messages
                $input.on("change", function() {
                    $(this).valid(); // Validate when the input changes
                });
            } else {
                console.error("? Element not found:", inputName); // Handle case if element is not found
            }
        }
        //to null check
        function hasValue(val) {
            return val !== null && val !== undefined && val.toString().trim() !== '';
        }


        //To onchange
        function handleCategoryFlow(dept, region, dist, cat, subcat, instId) {

            getcategoryBasedOndistrict(dept, region, dist, cat);

            var ifsubcategory = hasValue(subcat) ? 'Y' : 'N';

            onchange_category(cat, subcat, ifsubcategory, dept, region, dist);

            if (ifsubcategory === 'Y') {
                onchange_subcategory(dept, region, dist, cat, subcat, instId);
            } else {

                onchange_subcategory(dept, region, dist, cat, subcat, instId);

            }
        }


        //------------------------------------------Helper Functions-----------------------------------//

        //--------------------------on change-------------------------------------//


        function resetDropdown($el, defaultText) {
            $el.html(`<option value="">${defaultText}</option>`);
        }

        function getCategoriesBasedOnDept(deptcode, selectedCat = null, selectedRegion = null) {

            const lang = getLanguage();

            const catText = lang === 'ta' ? '---??????? ?????????????????---' : '---Select Category---';
            const regionText = lang === 'ta' ? '---??????????????? ?????????????????---' : '---Select Region---';
            const subCatText = lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---';

            const $cat = $('#catcode');
            const $region = $('#regioncode');
            const $subcat = $('#subcatid');

            resetDropdown($cat, catText);
            //resetDropdown($region, regionText);
            resetDropdown($subcat, subCatText);

            deptcode = deptcode || $('#deptcode').val();
            if (!deptcode) return;

            $.ajax({
                url: "/apms/apms_getdeptbaseddetails",
                type: "POST",
                data: {
                    deptcode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success(response) {

                    if (!response.success) {
                        alert(response.message || 'No data available');
                        return;
                    }

                    // Regions
                    if (response.regiondata.length) {
                        response.regiondata.forEach(r => {
                            $region.append(
                                `<option value="${r.regioncode}" ${r.regioncode == selectedRegion ? 'selected' : ''}>
                            ${lang === 'ta' ? r.regiontname : r.regionename}
                        </option>`
                            );
                        });
                    } else {
                        $region.append(`<option disabled>No Region Available</option>`);
                    }

                    // Categories
                    if (response.catdata.length) {
                        response.catdata.forEach(c => {
                            $cat.append(
                                `<option value="${c.catcode}"
                                subcategory="${c.if_subcategory}"
                                ${c.catcode == selectedCat ? 'selected' : ''}>
                            ${lang === 'ta' ? c.cattname : c.catename}
                        </option>`
                            );
                        });
                    } else {
                        $cat.append(`<option disabled>No Category Available</option>`);
                    }
                },
                error() {
                    alert('Server error. Please refresh and try again.');
                }
            });
        }



        function getcategoryBasedOndistrict(deptcode, region, district, selectedCatcode = null) {
            const catcodeDropdown = $('#catcode');
            const subcategoryDropdown = $('#subcatid');
            const lang = getLanguage();
            $('#catcode').empty();



            catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---??????? ?????????????????---">
                ${lang === 'ta' ? '---??????? ?????????????????---' : '---Select Category---'}
            </option>
            `);

            subcategoryDropdown.html(`
         <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
            ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
             </option>
             `);





            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }
            if (district == "") {
                var district = $("#distcode").val();
            }

            if (!deptcode && !region && !district) {


                catcodeDropdown.append(`
                <option value="" disabled data-name-en="No Category Available" data-name-ta="??? ?????????????">
                    ${lang === 'ta' ? '??? ?????????????' : 'No Category Available'}
                </option>


                `);
                subcategoryDropdown.append(`
                            <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                                ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                            </option>
                        `);
            }



            if (deptcode && region && district) {
                $.ajax({
                    url: "/getcategorybasedondistfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        data = response;


                        if (response.success && response.category.length > 0) {
                            response.category.forEach(category => {
                                const isSame = String(category.catcode).trim() === String(
                                    selectedCatcode).trim();
                                catcodeDropdown.append(`
                                    <option value="${category.catcode}"
                                        data-name-en="${category.catename}"
                                        subcategory="${category.if_subcategory}"
                                        data-name-ta="${category.cattname}"
                                        ${isSame? 'selected' : ''}>
                                        ${lang === 'ta' ? category.cattname : category.catename}
                                    </option>

                            `);

                            });

                        } else {

                            catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="??? ?????????????">
                        ${lang === 'ta' ? '??? ?????????????' : 'No Category Available'}
                    </option>
                `);
                        }
                        if (response.category[0].if_subcategory == 'N') {
                            $('.subcatdiv').hide()
                        }

                    },
                    error: function() {
                        alert('Error fetching category. Please try again.');
                    }
                });
            }
        }




        function onchange_category(catcodeparam, selectedsubCatcode = null, subcategory, deptcode, region, district) {

            var catcode = catcodeparam || $('#catcode').val();
            var selectedOption = $('#catcode').find(':selected');
            var subcategory = subcategory || selectedOption.attr('subcategory');
            let lang = getLanguage();



            const subcategoryDropdown = $('#subcatid');
            subcategoryDropdown.empty();

            subcategoryDropdown.append(`
            <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
                ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
            </option>
             `);


            if (!catcode) {

                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);

            }

            var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
            var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
            var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();

            if (category == "") {
                var category = sessioncatcode || $("#catcode").val();
            }

            if (subcategory == 'N') {

                onchange_subcategory(deptcode, region, district, catcode, '', selecteinstitutioncode = null)
                $('#mom_date').val();
            }
            if (subcategory == 'Y') {
                $.ajax({
                    url: '/getsubcategoriesbasedondeptforfollowup', // Your API route to get user details
                    method: 'POST',
                    data: {
                        category: catcode
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
                    },


                    success: function(response) {
                        if (subcategory === 'Y') {


                            if (response && response.length > 0) {

                                response.forEach(subcategory => {
                                    const isSame = String(subcategory.auditeeins_subcategoryid)
                                        .trim() === String(
                                            sessionsubcatid).trim();

                                    subcategoryDropdown.append(`
                        <option value="${subcategory.auditeeins_subcategoryid}"
                            data-name-en="${subcategory.subcatename}"
                            data-name-ta="${subcategory.subcattname}"
                            ${isSame ? 'selected' : ''}>
                            ${lang === 'ta' ? subcategory.subcattname : subcategory.subcatename}
                        </option>
                    `);

                                });

                            } else {
                                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);
                            }
                        } else {

                            $.each(data, function(i, subcategory) {
                                // alert("else");

                                if (subcategory.catcode === catcode) {
                                    $('#subcatid').append(
                                        `<option value="" data-name-en="${subcategory.catename}" data-name-ta="${subcategory.cattname}" selected>
                                  ${lang === "ta" ? subcategory.cattname : subcategory.catename}
                                 </option>`
                                    );
                                }
                            });


                        }

                    },
                    error: function(xhr, status, error) {
                        // alert('enter')


                    }
                });
            }
        }

        function onchange_subcategory(
            deptcode,
            region,
            district,
            category,
            subcategory,
            selectedInstitutionCodes = []
        ) {

            const momDropdowm = $('#mom_date');
            const lang = getLanguage();

            momDropdowm.empty(); // important for Select2

            deptcode = deptcode || $('#deptcode').val();
            region = region || $('#regioncode').val();
            district = district || $('#distcode').val();
            category = category || $('#catcode').val();
            subcategory = subcategory || $('#subcatid').val();


            if (deptcode && region && district && category) {

                $.ajax({
                    url: "/apms/meetdate_hlc",
                    type: "POST",
                    data: {
                        deptcode,
                        region,
                        district,
                        catcode: category,
                        subcatcode: subcategory,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        if (response.success && response.data.length > 0) {
                            // response.data.forEach(inst => {
                            //     const formatted_date = convertDateFormatYmd_ddmmyy(inst.mom_date)
                            //     const option = new Option(
                            //         formatted_date,
                            //         formatted_date,
                            //         false,
                            //         false
                            //     );

                            //     $(option)
                            //         .attr('data-name-en', formatted_date)
                            //         .attr('data-name-ta', formatted_date)
                            //         .prop('disabled', false);

                            //     momDropdowm.append(option);
                            // });


                        } else {
                            momDropdowm.append(
                                `<option disabled>
                            ${lang === 'ta' ? '' : 'Meeting details not Available'}
                        </option>`
                            );
                        }

                        // ?? IMPORTANT for Select2
                        momDropdowm.trigger('change');
                    },
                    error: function() {
                        alert('Error fetching institution. Please try again.');
                    }
                });
            }
        }

        function onchange_convenor() {

            const momDropdowm = $('#mom_date');
            const lang = getLanguage();

            momDropdowm.empty(); // important for Select2

            deptcode = $('#deptcode').val();
            region = $('#regioncode').val();
            district = $('#distcode').val();
            category = $('#catcode').val();
            subcategory = $('#subcatid').val();
            committee_level = $('#committee_level').val();



            if (deptcode && region && district && category) {

                $.ajax({
                    url: "/apms/meetdate_hlc",
                    type: "POST",
                    data: {
                        deptcode,
                        region,
                        district,
                        catcode: category,
                        subcatcode: subcategory,
                        committee_level: committee_level,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        if (response.success && response.data.length > 0) {
                            response.data.forEach(inst => {
                                const formatted_date = convertDateFormatYmd_ddmmyy(inst.mom_date)
                                const option = new Option(
                                    formatted_date,
                                    formatted_date,
                                    false,
                                    false
                                );

                                $(option)
                                    .attr('data-name-en', formatted_date)
                                    .attr('data-name-ta', formatted_date)
                                    .prop('disabled', false);

                                momDropdowm.append(option);
                            });


                        } else {
                            momDropdowm.append(
                                `<option disabled>
                            ${lang === 'ta' ? '' : 'No Institution Available'}
                        </option>`
                            );
                        }

                        // ?? IMPORTANT for Select2
                        momDropdowm.trigger('change');
                    },
                    error: function() {
                        alert('Error fetching institution. Please try again.');
                    }
                });
            }
        }
        $('#instid').on('change', function() {

            const selectedValues = $(this).val() || [];

            const hasAllSelected = selectedValues.includes('A');
            const hasOtherSelected = selectedValues.some(v => v !== 'A');

            // CASE 1: ALL selected
            if (hasAllSelected) {

                $(this).find('option').each(function() {
                    if (this.value !== 'A') {
                        this.disabled = true;
                        this.selected = false;
                    }
                });

                // Make sure ALL stays enabled
                const allOpt = this.querySelector('option[value="A"]');
                if (allOpt) {
                    allOpt.disabled = false;
                }
            }

            // CASE 2: Any OTHER option selected
            else if (hasOtherSelected) {

                // Enable others
                $(this).find('option').each(function() {
                    if (this.value !== 'A') {
                        this.disabled = false;
                    }
                });

                // Disable ALL
                const allOpt = this.querySelector('option[value="A"]');
                if (allOpt) {
                    allOpt.disabled = true;
                    allOpt.selected = false;
                }
            }

            // CASE 3: Nothing selected
            else {

                // Enable ALL
                const allOpt = this.querySelector('option[value="A"]');
                if (allOpt) {
                    allOpt.disabled = false;
                }

                // Disable others (optional, based on your earlier logic)
                $(this).find('option').each(function() {
                    if (this.value !== 'A') {
                        this.disabled = false;
                    }
                });
            }
        });


        function getDistrictBasedOnRegion(deptcode, region, selectedDistrict = null) {

            const lang = getLanguage();
            const $district = $('#distcode');
            const $institution = $('#instid');

            resetDropdown($district, lang === 'ta' ? '???????????? ?????????????????' : 'Select District');
            resetDropdown($institution, lang === 'ta' ? '???????????? ?????????????????' : 'Select Audit Office');

            deptcode = deptcode || $('#deptcode').val();
            region = region || $('#regioncode').val();

            if (!deptcode || !region) return;

            $.post('/getdistrictbasedonregionfollowup', {
                    deptcode,
                    region,
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(res => {

                    if (!res.success || !res.data.length) {
                        $district.append(`<option disabled>No District Available</option>`);
                        return;
                    }

                    res.data.forEach(d => {
                        $district.append(`
                <option value="${d.distcode}" ${d.distcode == selectedDistrict ? 'selected' : ''}>
                    ${lang === 'ta' ? d.disttname : d.distename}
                </option>
            `);
                    });
                })
                .fail(() => alert('Unable to fetch district'));
        }


        //---------------------------on change------------------------------------//


        //---------------------------on load------------------------------------//

        $(document).ready(function() {

            var lang = getLanguage('')

            fetch_parastatus(lang)
            // init_setForm()

        });



        $(document).on('change', '.action-select', function() {

            let table = $('#para_details').DataTable();
            let rowNode = $(this).closest('tr');
            let row = table.row(rowNode);

            // Save value into DataTable data
            row.data().selected_action = $(this).val();

            // Clear error UI
            rowNode.removeClass('dt-row-error');
            rowNode.find('.action-error').remove();
        });
        //---------------------------on load------------------------------------//

        //--------------------------functions----------------------------------//
        function datepicker(value, setdate) {
            init_datepicker(value, '', '', setdate);
        }


        //--------------------------functions---------------------------------//

        /*************************************************  Ckeditor  *********************************************/







        /*************************************************  Ckeditor  *********************************************/


        function validateAllRows() {

            let isValid = true;
            let table = $('#para_details').DataTable();

            table.rows().every(function() {

                let rowNode = $(this.node());
                let $status = rowNode.find('.select-para');
                let $action = rowNode.find('.action-select');
                let $remarks = rowNode.find('.remarks-input');

                // remove previous errors
                rowNode.find('.invalid-feedback').remove();
                $status.removeClass('is-invalid');
                $action.removeClass('is-invalid');
                $remarks.removeClass('is-invalid');

                const statusVal = $status.val();
                const actionVal = $action.val();
                const remarksVal = $remarks.val().trim();

                // 1?? Status mandatory
                if (!statusVal) {
                    isValid = false;
                    $status.addClass('is-invalid')
                        .after('<div class="invalid-feedback">Please select Approve or Return.</div>');
                }

                // 2?? Action mandatory
                if (!actionVal) {
                    isValid = false;
                    $action.addClass('is-invalid')
                        .after('<div class="invalid-feedback">Please select an action.</div>');
                }

                // 3?? Remarks required for Return
                if (statusVal === 'R' && !remarksVal) {
                    isValid = false;
                    $remarks.addClass('is-invalid')
                        .after('<div class="invalid-feedback">Remarks required when returning para.</div>');
                }

            });

            if (!isValid) {
                alert('Please complete all paras before submitting.');
            }

            return isValid;
        }


        function submitParaActions(data, statusflag) {
            var formData = $('#init_approver').serializeArray();


            ['deptcode', 'distcode', 'regioncode'].forEach(function(id) {
                var $el = $('#' + id);

                if ($el.prop('disabled')) {
                    formData.push({
                        name: id,
                        value: $el.val()
                    });
                }
            });

            // DataTable data append
            formData.push({
                name: 'rows',
                value: JSON.stringify(data)
            });

            formData.push({
                name: 'statusflag',
                value: statusflag
            });



            $.ajax({
                url: '/apms/insert_approveraction',
                type: 'POST',
                data: formData,

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {


                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        $('#ok_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            var lang = getLanguage('')
                            fetch_parastatus(lang)


                        });




                    } else {
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    }

                },
                error: function() {
                    alert('Something went wrong');
                }
            });
        }


        function view_files(file) {
            const fileListContainer = $('#view_file-list-container');
            $('#file').hide();
            fileListContainer.empty(); // Clear previous file cards

            $('#file').val('');
            // Set the fileuploadid directly since it's a single file
            $('#uploadid').val(file.fileuploadid);

            const fileCard = `

                <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
                    <div class="d-flex flex-row">

                        <div class="p-3 mb-1">
                            <h3 class="text-dark mb-0 fs-2">
                                <a style="color:black;" href="${file.path}" target="_blank">${file.name}</a>
                            </h3>
                        </div>
                        <div class="p-1 align-items-center mt-2 "  onclick="remove_file()">
                            <h5 class="text-danger box mb-0 round-40 p-1">
                                <i class="ti ti-trash"></i>
                            </h5>
                        </div>
                    </div>
                </div>
            `;

            fileListContainer.append(fileCard); // Add the file card to the container
        }

        function remove_file() {
            $('#view_file-list-container').hide();
            $('#file').val('').show();
            $('#uploadid').val('');
        }

        function get_para_historydetails(id, param) {
            const paraid = id.dataset.para;
            const apms_hlcid = id.dataset.apmsid;
            const lang = getLanguage()

            $.ajax({
                url: "/apms/fetch_para_datas",
                type: "POST",
                data: {
                    paraid: paraid,
                    apms_hlcid: apms_hlcid,
                    param: param
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var para_det = response.data;
                    var prarahistory_det = response.parahistory;

                    if (param == 'view_flow') {

                        var designation = lang == 'ta' ? '????' : 'Designation';
                        var status = lang == 'ta' ? '????' : 'Status';
                        var serialno = lang == 'ta' ? '???.???' : 'S.No';
                        var actiontakenon = lang == 'ta' ? '???????? ?????????' :
                            'Forwarded On';

                        var datacontent = `<div class=" table-responsive rounded-2 border " style="max-width: 750px;overflow-x: auto;max-height: 600px; overflow-y: auto;width:98%;margin:0 auto;">
                           <table class="table userCountTable">
                               <thead class="">
                                   <tr>
                                       <th class="lang userCount_head" key="audit_office">${serialno}
                                       </th>
                                       <th class="lang userCount_head" key="">${designation}
                                       </th>
                                       <th class="lang userCount_head" key="">${status}</th>
                                        <th class="lang userCount_head" key="">${actiontakenon}</th>
                                   </tr>
                               </thead>
                               <tbody id="instTableBody">

                               </tbody>
                           </table>
                       </div>`;
                        passing_extra_large_alert('Para Flow', datacontent,
                            'extra_large_confirmation_alert',
                            'extra_large_alert_header',
                            'extra_large_alert_body', 'confirmation_alert', 'send_intimation_label');
                        setTimeout(() => {
                            populatTable(para_det);
                        }, 100);

                    } else if (param == 'view_para') {
                        populate_parafulldetails(lang, para_det, prarahistory_det, 'parafull_details',
                            'parafull_det',
                            'parafull_dettable')
                    } else if (param == 'view_minutes')

                    {
                        populate_minutedata(para_det)
                    }
                }
            });
        }

        function populatTable(det) {

            const instbody = document.getElementById('instTableBody');
            instbody.innerHTML = '';

            det.forEach((item, index) => {
                const row = document.createElement('tr');
                let status = '';
                switch (item.processcode) {

                    case 'E':
                        status = 'Entry'
                        break;
                    case 'F':
                        status = 'Forwarded to PSA Auditor'
                        break;

                    case 'K':
                        status = 'Forwarded to PSA AD'
                        break;
                    case 'U':
                        status = 'Forwared to Auditee'
                        break;
                    case 'I':
                        status = 'Rejected'
                        break;
                    case 'A':
                        status = 'Dropped'
                        break;
                    case 'B':
                        status = 'Forwarded from DLC'
                        break;
                    case 'D':
                        status = 'Forwarded to DLC'
                        break;

                    default:
                        status = 'Entry';
                        break;

                }

                status += ((item.rejoinderstatus == 'Y' && (item.rejoinderstatus == 'A' || item
                    .rejoinderstatus ==
                    'R')) ? ' - (Rejoinder)' : '')

                let actiondate = item.forwardedon ?
                    (() => {
                        let d = new Date(item.forwardedon);
                        let day = String(d.getDate()).padStart(2, '0');
                        let month = String(d.getMonth() + 1).padStart(2, '0');
                        let year = d.getFullYear();
                        let hours = String(d.getHours()).padStart(2, '0');
                        let minutes = String(d.getMinutes()).padStart(2, '0');
                        let seconds = String(d.getSeconds()).padStart(2, '0');
                        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                    })() :
                    "N/A";

                const lang = getLanguage();

                const roleMap = {
                    'I': lang == 'en' ? 'Auditee' : '????????????',
                    'A': lang == 'en' ? 'PSA Auditor' : '??.???.?. ????????????',
                    'AD': lang == 'en' ? 'PSA AD' : '??.???.?. ???? ?????????',
                    'DL': lang == 'en' ? 'District HLC' : '?????? ????????'
                };

                var actiontaken_name = ''
                switch (item.actroleactioncode) {
                    case 'I':
                        actiontaken_name = lang == 'ta' ?
                            (item.processcode == 'D' ?
                                'HLC ???????? ??????????????' :
                                '????????? ?????????????????') :
                            (item.processcode == 'B' ?
                                'Forwarded to HLC Department' :
                                'Replied to Para');

                        break;
                    case 'A':
                        actiontaken_name = lang == 'ta' ? item.actiontname : item.actionename
                        break;
                    case 'AD':
                        actiontaken_name = lang == 'ta' ? item.actiontname : item.actionename
                        break;
                    case 'DL':
                        actiontaken_name = lang == 'ta' ?
                            (item.processcode == 'B' ?
                                '???????????????? ??????????????' :
                                'HLC ???????? ??????????????') :
                            (item.processcode == 'B' ?
                                'Forwarded to Auditee' :
                                'Forwarded to HLC Department');

                        break;
                    default:
                        actiontaken_name = 'Reply'
                        break;
                }
                const actroleactioncode = roleMap[item.actroleactioncode] || '-';
                row.innerHTML = `
                       <td><h6>${index + 1}<h6></td>
                       <td><h6>${actroleactioncode??'-'}<h6></td>
                       <td><h6>${actiontaken_name??'-'}<h6></td>
                       <td><h6>${actiondate??'-'}<h6></td>
                   `;
                instbody.appendChild(row);
            });
        }

        // function populate_parafulldetails(para_data) {

        //     var innerHTML = ``;
        //     const modelbody = $('#parafull_det'); // Select the table's tbody
        //     modelbody.empty(); // Clear existing row
        //     let editorIds = [];

        //     // para_data.forEach((data, index) => {
        //     //     modelbody.append(innerHTML);

        //     // });



        //     $('#parafull_details').modal('show');

        // }




        function populate_minutedata(para_det) {

            if (!para_det || para_det.length === 0) return;
            const modelbody = $('#minutes_det');
            modelbody.empty();

            const data = para_det[0]; // response has array
            const momDate = ChangeDateFormat(data.mom_date) || "-";
            const filePath = data.minutesfileupload;
            const minutefiles = getfile(data.minutesfileupload)
            console.log(minutefiles)
            let fileHTML = '';
            if (minutefiles && minutefiles.length > 0) {
                const file = minutefiles[0];

                fileHTML = `
               <a class="card-subtitle text-primary"
                  href="/${file.path.replace(/\\/g, '/')}"
                  target="_blank">

                   ${file.name}
               </a>
           `;
            }


            const html = `
             <div class="col-xxl-12 col-md-12">
                 <div class="card border-start border-primary">
                     <div class="card-body">
                         <div class="d-flex align-items-center justify-content-start mb-3">
                             <span class="text-primary display-6">
                                 <i class="ti ti-calendar-event"></i>
                             </span>
                             <div class="text-start">
                                 <h6 class="card-title mb-2">Minutes of Meeting Date</h6>
                                 <p class="card-subtitle text-primary">${momDate}</p>
                             </div>
                         </div>

                     </div>
                 </div>
             </div>
             <hr>
                <div class="col-xxl-12 col-md-12">
                 <div class="card border-start border-primary">
                     <div class="card-body">




                        <div class="d-flex align-items-center justify-content-start mb-3" >
                             <span class="text-primary display-6">
                                 <i class="ti ti-file"></i>
                             </span>
                             <div class="text-start">
                                 <h6 class="card-title mb-2">Meeting Document</h6>
                    ${fileHTML}                             </div>
                         </div>

                     </div>
                 </div>
             </div>
             `;
            // if (data.minutesfileupload) {
            //     UploadedFileList(minutefiles, '', `minutes_file_div`, '', '');
            // }
            passing_alert_value("Minutes of  Meeting", html,
                "confirmation_alert", "alert_header",
                "alert_body",
                "confirmation_alert");

            // target container
        }

        function getstatus(actroleactioncode, processcode) {
            let status = '';
            if (processcode == 'DE') {
                status = 'Draft at HLC';
            } else if (processcode == 'PH') {
                status = 'Pending Paras';
            } else if (processcode == 'A') {
                status = 'Accepted';
            } else if (processcode == 'X') {
                status = 'Droppped';
            } else if (processcode == 'B') {
                status = 'Forwared to Auditee';
            } else if (processcode == 'D') {
                status = 'Auditee forwarded to District HLC';
            } else if (processcode == 'AE') {
                status = 'Auditee forwarded to Department HLC';
            } else if (processcode == 'AS') {
                status = 'Auditee forwarded to State HLC';
            } else if (processcode == 'DH') {
                status = 'Forwarded to Department HLC';
            } else if (processcode == 'IS') {
                status = 'Forwarded to State HLC';
            } else if (processcode == 'DS') {
                status = 'Forwarded to State HLC';
            } else if (processcode == 'D') {
                status = 'Forwarded to District HLC';
            } else if (processcode == 'FA') {
                status = 'Forwarded to Approver';
            } else {
                status = '-';
            }

            return status;
        }

        function initApprovedParas() {

            let approved = [];
            let rejected = [];

            $('#para_details tbody tr').each(function() {

                let row = $(this);
                let select = row.find('.select-para'); // find the select
                let followupid = select.data('followupid'); // get data-followupid from select
                let status = select.val(); // get selected value

                // debug
                // console.log(followupid, status);

                if (followupid) {
                    if (status === 'A') {
                        approved.push(followupid);
                    } else if (status === 'R') {
                        rejected.push(followupid);
                    }
                }

            });

            $('#approved_para').val(approved.join(','));
            $('#rejected_para').val(rejected.join(','));
        }
        $(document).on('change', '.select-para', function() {

            let approved = [];
            let rejected = [];

            $('#para_details tbody tr').each(function() {

                let row = $(this);
                let select = row.find('.select-para'); // get the select in this row
                let followupid = select.data('followupid'); // read data-followupid from select
                let status = select.val(); // get selected value

                if (!followupid) return; // skip empty

                if (status === 'A') {
                    approved.push(followupid);
                } else if (status === 'R') {
                    rejected.push(followupid);
                }

            });

            $('#approved_para').val(approved.join(','));
            $('#rejected_para').val(rejected.join(','));

        });



        $('#buttonaction, #finalisebtn').on('click', function(e) {
            e.preventDefault();


            if ($("#init_approver").valid()) {

                if (!validateAllRows()) {
                    return false;
                }
                const buttonaction = this.id;
                let actionType = buttonaction === 'finalisebtn' ? 'finalise' : 'draft';
                let para_action = buttonaction === 'finalisebtn' ? 'finalise' : 'draft';

                let table = $('#para_details').DataTable();
                let rowData = [];


                let statusflag = this.id === 'finalisebtn' ? 'F' : 'Y';

                table.rows().every(function() {
                    let rowNode = $(this.node());

                    // Use the select instead of checkbox
                    let selectPara = rowNode.find('.select-para');
                    let followupid = selectPara.data('followupid');
                    let instid = selectPara.data('instid'); // if you have instid stored here
                    let actionVal = rowNode.find('.action-select').val()

                    let remarks = rowNode.find('.remarks-input').val();

                    // Build followup_action
                    let followupAction = {
                        followupid: followupid,
                        actioncode: actionVal
                    };

                    let approverRemarks = {
                        followupid,
                        remarks
                    };

                    // Build approver_remarks array for this row


                    // Push the row data
                    rowData.push({
                        paraid: this.id(),
                        instid: instid,
                        followup_action: followupAction,
                        actioncode: actionVal,
                        action_type: actionType,
                        approver_remarks: approverRemarks
                    });
                });



                submit_message = buttonaction === 'finalisebtn' ?
                    'Marked Status will be updated on the selected para(s).After finalisation, para-status cannot be revoked. Do you want to proceed?' :
                    'Are you sure to submit the actions against para?';
                passing_alert_value(
                    'Confirmation',
                    submit_message,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Attach click handler only after alert is shown
                $('#process_button')
                    .off('click')
                    .on('click', function(event) {
                        event.preventDefault();
                        submitParaActions(rowData, statusflag, para_action);
                    });

            } else {
                scrollToFirstError()
                console.log("Form validation failed.");
            }


        });


        function handleStatusChange(selectElement) {
            const row = $(selectElement).closest('tr');
            const remarksTextarea = row.find('.remarks-input');
            const actionSelect = row.find('.action-select'); // added reference
            const statusValue = $(selectElement).val();

            if (statusValue === 'R') { // 'R' for Return
                remarksTextarea.prop('disabled', false);
                remarksTextarea.attr('required', true); // Add required for validation
                actionSelect.prop('disabled', true); // disable action when returned
            } else {
                remarksTextarea.prop('disabled', true);
                remarksTextarea.val(''); // Clear remarks if disabled
                remarksTextarea.removeAttr('required'); // Remove required
                actionSelect.prop('disabled', false); // re-enable action
            }
        }
    </script>
@endsection
