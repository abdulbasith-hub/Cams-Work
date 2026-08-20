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
    @endphp

    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <div class="col-12">
        <div class="card">
            @if ($sessionroletypecode == $dehc_roletypecode)
                <div class="card-header lang card_header_color" key="dehlc_label">Department High Level Committee</div>
            @elseif ($sessionroleactioncode == $rtd_committee_roleaction)
                <div class="card-header lang card_header_color" key="">Retirement Committee</div>
            @elseif ($sessionroletypecode == $district_hlc_roletypecode)
                <div class="card-header lang card_header_color" key="dlc_label">District High Level Committee</div>
            @else
                <div class="card-header lang card_header_color" key="shlc_label">State High Level Committee</div>
            @endif
            <div class="card-body">
                <div class="row">
                    <form id="init_hlc" name="init_hlc">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="department"
                                    for="validationDefault01">Department</label>
                                <input type="hidden" id="apms_hlcid" name="apms_hlcid" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','')">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---
                                    </option>

                                    @if (!empty($dept) && count($dept) > 0)
                                        @foreach ($dept as $department)
                                            <option value="{{ $department->deptcode }}"
                                                @if (old('dept', $deptcode) == $department->deptcode) selected @endif
                                                data-name-en="{{ $department->deptelname }}"
                                                data-name-ta="{{ $department->depttlname }}">
                                                {{ $department->deptelname }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Department Available"
                                            data-name-ta="துறைகள் எதுவும் இல்லை">No Departments Available</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 " <?php echo $make_region_disable; ?> id="regioncode"
                                    name="regioncode" onchange="getDistrictBasedOnRegion('','','','')">
                                    <option value="" data-name-en="Select a Region"
                                        data-name-ta="??????? ?????????????????">
                                        Select Region</option>

                                    @if (!empty($region) && count($region) > 0)
                                        @foreach ($region as $reg)
                                            <option value="{{ $reg->regioncode }}"
                                                @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                                data-name-en="{{ $reg->regionename }}"
                                                data-name-ta="{{ $reg->regiontname }}">
                                                {{ $reg->regionename }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Regions Available"
                                            data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Regions
                                            Available
                                        </option>
                                    @endif

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2 " <?php echo $make_dist_disable; ?> id="distcode"
                                    name="distcode" onchange="getcategoryBasedOndistrict('','','','')">
                                    <option value="" data-name-en="Select a District"
                                        data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option>

                                    @if (!empty($district) && count($district) > 0)
                                        @foreach ($district as $dist)
                                            <option value="{{ $dist->distcode }}"
                                                @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                                data-name-en="{{ $dist->distename }}"
                                                data-name-ta="{{ $dist->disttname }}">
                                                {{ $dist->distename }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Department Available"
                                            data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                            Available
                                        </option>
                                    @endif

                                </select>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="category"
                                    for="validationDefault01">Category</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="catcode" name="catcode"
                                    onchange="onchange_category(this.value,'','','','','')" <?php echo $make_cat_disable; ?>>
                                    <option value="" data-name-en="---Select Category---"
                                        data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                    <option value="" disabled id="" data-name-en="No Category Available"
                                        data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                                </select>
                            </div>


                            <div class="col-md-4 mb-2 subcatdiv ">
                                <label class="form-label lang required" key="subcategoryColumn"
                                    for="subcatid">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="subcatid" name="subcatid"
                                    onchange="onchange_subcategory('','','','','','')" <?php echo $make_subcat_disable; ?>>
                                    <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---
                                    </option>

                                    <option value="" disabled data-name-en="No SubCategory Available"
                                        data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option>


                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="mom_date" for="institution">Meeting
                                    Date</label>
                                <select class="form-select mr-sm-2 select2 " id="mom_date" name="mom_date">
                                    <option value="" data-name-en="Select Auditable Institution"
                                        data-name-ta="தணிக்கை செய்யக்கூடிய நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select
                                        Auditable
                                        Institution</option>




                                </select>
                            </div>



                        </div>
                        <div class="row ">
                            <div class="col-md-3 mx-auto text-center">
                                <!-- Adding text-center to center the content inside -->
                                <input type="hidden" name="action" id="action" value="insert" />


                                <button class="btn btn-primary mt-3 lang" key="" type="submit" action="insert"
                                    id="mom_submit" name="mom_submit">Submit</button>


                            </div>
                        </div>
                </div>
            </div>



        </div>
        <hr>
        <div class="card mt-2" style="border-color: #7198b9">
            <div class="card-header card_header_color lang" key="para_det">Para Management Details</div>
            <div class="card-body">
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">


                        <table id="para_details"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                @csrf
                                <tr>
                                    <th class="lang text-wrap" style="width:5%" key="s_no">S.No</th>
                                    <th class="text-wrap" key="institute_detail" style="width:20%">Institute Details</th>
                                    <th class="text-wrap" key="audityear" style="width:15%">Audit Year</th>
                                    <th class="text-wrap" key="para_no" style="width:5%">Para Number</th>


                                    {{-- <th class="text-wrap" key="objection_name" style="width:20%">Title Heading</th> --}}
                                    <th class="lang " key="para_details" style="width:5%">View Para Details</th>

                                    <th class="lang " key="" style="width:10%">District HLC Recommendation
                                        Minutes
                                    </th>
                                    <th class="all lang" key="status" style="width:25%">Status</th>
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


                "columnDefs": [{
                        targets: 0,
                        width: "5%"
                    }, // S.No
                    {
                        targets: 1,
                        width: "20%"
                    }, // Title Heading
                    {
                        targets: 2,
                        width: "15%"
                    }, // Para Number
                    {
                        targets: 3,
                        width: "5%"
                    }, // Slip Details


                    {
                        targets: 4,
                        width: "10%"
                    }, // Action

                    {
                        targets: 5,
                        width: "10%"
                    }, // Action
                    {
                        targets: 6,
                        width: "25%"
                    }, // Action

                ],
                columns: [{
                        data: "encrypted_paraid",
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
                            const para_btn = language == 'ta' ? 'பாரா விவரங்களைக் காண்க' :
                                'View Para Details'
                            const det_btn = language == 'ta' ? 'ஓட்டத்தைச் சரிபார்க்கவும்' :
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
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            const is_available = hasValue(row.apms_hlcid);
                            const min_btn = language == 'ta' ? 'மினிட்ஸ் மீட்டிங் பார்க்கவும்' :
                                'View Minutes Meeting'
                            const no_data = language == 'ta' ? 'தரவு எதுவும் கிடைக்கவில்லை' :
                                'No Data available'
                            const buttonHtml = `
                               <button
                        type="button" class="btn-sm btn btn-info enable-row " onclick="get_para_historydetails(this,'view_minutes')" data-apmsid=${row.encrypted_apms_hlcid} data-para=${row.encrypted_paraid}   data-action="para" style="font-size:10px">
                        <i class="ti ti-clipboard-data fs-2 me-2"></i> ${min_btn}
                        </button>
                            `;

                            return `
                                <div class="d-flex justify-content-center align-items-center">
                                    ${is_available?buttonHtml:no_data}
                                </div>
                            `;
                        },
                        className: 'text-wrap text-start'
                    },


                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row, meta) {
                            const status = getstatus(row.actroleactioncode, row.processcode)
                            return `  <button
                        type="button" class="btn-sm btn btn-success enable-row "    style="font-size:12px">
                        ${status}
                        </button>`


                        },
                        className: "text-center text-wrap noExport"
                    }
                ],

            });


            const mobileColumns = [Category, SubCategory, TypeofAudit, "paranumber", "updatedon"];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "para_details");
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
        function fetch_parastatus(language, data) {

            var formData = $('#init_hlc').serializeArray();
            ['deptcode', 'distcode', 'regioncode'].forEach(function(id) {
                const $el = $('#' + id);

                if ($el.prop('disabled') || !$el.val()) {
                    formData.push({
                        name: id,

                        value: data ? data[id] : $el.val()


                    });
                }
            });

            ['catcode', 'subcatid', 'mom_date'].forEach(function(id) {
                const $el = $('#' + id);

                if ($el.prop('disabled') || !$el.val()) {
                    formData.push({
                        name: id,
                        value: data ? data[id] : $el.val()

                    });
                }
            });

            $.ajax({
                url: '/apms/fetch_parastatus_hlc', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                success: function(json) {

                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#spara_details_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        dataFromServer = json.data;
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

                            // fit_minutesDetails()
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
                    $('#finalisebtn').prop('disabled', false);
                    $('#process_button').prop('disabled', false);
                    $('#buttonaction').removeAttr('disabled');
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {

                        // getLabels_jsonlayout([{
                        //         id: ,
                        //         key: errorMessage
                        //     }], "N")
                        //     .then((text) => {
                        //         passing_alert_value("Confirmation", Object.values(text)[
                        //                 0],
                        //             "confirmation_alert", "alert_header",
                        //             "alert_body",
                        //             "confirmation_alert");
                        //     });
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                    }
                }

            });
        }

        function fetch_apms_hlcdetails(language) {
            var formData = $('#init_dhlc').serializeArray();
            ['deptcode', 'distcode', 'regioncode'].forEach(function(id) {
                const $el = $('#' + id);

                if ($el.prop('disabled')) {
                    formData.push({
                        name: id,
                        value: $el.val()
                    });
                }
            });

            $.ajax({
                url: '/apms/fetch_apms_hlcdetails', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                success: function(response) {
                    var data = response.data[0]
                    if (data) {
                        if (hasValue(sessioncatcode)) {

                            handleCategoryFlow(
                                sessiondeptcode,
                                sessionregioncode,
                                sessiondistcode,
                                sessioncatcode,
                                sessionsubcatid,
                                data['instid']
                            );

                        } else if (hasValue(sessiondeptcode)) {

                            handleCategoryFlow(
                                data['deptcode'],
                                data['regioncode'],
                                data['distcode'],
                                data['catcode'],
                                data['subcatid'],
                                data['instid']
                            );
                        }

                        //fit minutes Date
                        if (hasValue(data['mom_date'])) {
                            datepicker('mom_date', convertDateFormatYmd_ddmmyy(data['mom_date']));
                        }


                        $('#apms_hlcid').val(data['encrypted_hlcid']);

                        //fit file details

                        if (hasValue(data['filedetails'])) {

                            $('#view_file-list-container').show();

                            var fileParts = data['filedetails'].split('-');

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

                        //fetch para details
                        var lang = getLanguage('')
                        fetch_dhlcparadetails(lang, data)

                    } else {

                        if (hasValue(sessioncatcode)) {

                            getcategoryBasedOndistrict(
                                sessiondeptcode,
                                sessionregioncode,
                                sessiondistcode,
                                sessioncatcode
                            );

                            var ifsubcategory = hasValue(sessionsubcatid) ? 'Y' : 'N';

                            onchange_category(sessioncatcode, sessionsubcatid, ifsubcategory);

                            if (ifsubcategory === 'Y') {
                                onchange_subcategory(
                                    sessiondeptcode,
                                    sessionregioncode,
                                    sessiondistcode,
                                    sessioncatcode,
                                    sessionsubcatid,
                                    ''
                                );
                            }

                        } else if (hasValue(sessiondeptcode)) {

                            getCategoriesBasedOnDept(sessiondeptcode);

                            getcategoryBasedOndistrict(
                                sessiondeptcode,
                                sessionregioncode,
                                sessiondistcode,
                                ''
                            );
                        }

                    }




                },
                complete: function() {
                    $('#finalisebtn').prop('disabled', false);
                    $('#process_button').prop('disabled', false);
                    $('#buttonaction').removeAttr('disabled');
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {

                        // getLabels_jsonlayout([{
                        //         id: ,
                        //         key: errorMessage
                        //     }], "N")
                        //     .then((text) => {
                        //         passing_alert_value("Confirmation", Object.values(text)[
                        //                 0],
                        //             "confirmation_alert", "alert_header",
                        //             "alert_body",
                        //             "confirmation_alert");
                        //     });
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                    }
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

                // ✅ Ensure validation runs on change without removing existing messages
                $input.on("change", function() {
                    $(this).valid(); // Validate when the input changes
                });
            } else {
                console.error("❌ Element not found:", inputName); // Handle case if element is not found
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

        $('#para_details').on('click', '.enable-row', function() {

            const row = $(this).closest('tr');
            const action = $(this).data('action');

            // get existing click state or initialize
            let clicked = row.data('clickedActions') || {
                para: false,
                flow: false,
                view: false
            };

            // mark this action as clicked
            clicked[action] = true;
            row.data('clickedActions', clicked);

            // check if ALL three buttons clicked
            if (clicked.para) {
                row.find('.custom-checkbox').prop('disabled', false);
            }
        });

        $('#para_details').on('change', '.custom-checkbox', function() {

            const row = $(this).closest('tr');
            const isChecked = $(this).is(':checked');

            row.find('.action-select').prop('disabled', !isChecked);
        });

        // $('#para_details').on('draw.dt', function() {
        //     $('.action-select').prop('disabled', true);
        // });
        $('#para_details').on('draw.dt', function() {

            $('#para_details tbody tr').each(function() {

                const $row = $(this);
                const isChecked = $row.find('.custom-checkbox').is(':checked');

                // Disable only when checkbox is NOT checked
                $row.find('.action-select').prop('disabled', !isChecked);
            });

        });

        function resetDropdown($el, defaultText) {
            $el.html(`<option value="">${defaultText}</option>`);
        }

        function getCategoriesBasedOnDept(deptcode, selectedCat = null, selectedRegion = null) {

            const lang = getLanguage();

            const catText = lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---';
            const regionText = lang === 'ta' ? '---பிராந்தியத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Region---';
            const subCatText = lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---';

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
            <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
            </option>
            `);

            subcategoryDropdown.html(`
         <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
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
                <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                </option>


                `);
                subcategoryDropdown.append(`
                            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
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
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
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
            <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
             `);


            if (!catcode) {

                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
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

                        // 🔥 IMPORTANT for Select2
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

            resetDropdown($district, lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District');
            resetDropdown($institution, lang === 'ta' ? 'அலுவலகத்தைத் தேர்ந்தெடுக்கவும்' : 'Select Audit Office');

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
            if (hasValue(sessioncatcode)) {
                getcategoryBasedOndistrict(
                    sessiondeptcode,
                    sessionregioncode,
                    sessiondistcode,
                    sessioncatcode
                );

                var ifsubcategory = hasValue(sessionsubcatid) ? 'Y' : 'N';

                onchange_category(sessioncatcode, sessionsubcatid, ifsubcategory, sessiondeptcode,
                    sessionregioncode,
                    sessiondistcode);

                if (ifsubcategory === 'Y') {
                    onchange_subcategory(
                        sessiondeptcode,
                        sessionregioncode,
                        sessiondistcode,
                        sessioncatcode,
                        sessionsubcatid,
                        ''
                    );
                }

            } else if (hasValue(sessiondeptcode)) {
                getCategoriesBasedOnDept(sessiondeptcode, '', '')
                // handleCategoryFlow(
                //     sessiondeptcode,
                //     '',
                //     '',
                //     '',
                //     '',
                //     ''
                // );
            }
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






        function initializeEditors(editorIds) {

            if (!window.CKEDITOR || !window.CKEDITOR.ClassicEditor) {
                setTimeout(() => initializeEditors(editorIds), 100); // Retry if CKEditor is not yet loaded
                return;
            }

            editorIds.forEach(id => {
                let textarea = document.getElementById(id);
                if (textarea) {
                    CKEDITOR.ClassicEditor.create(textarea, {
                            toolbar: {
                                items: [
                                    'selectAll', '|',
                                ],
                                shouldNotGroupWhenFull: true
                            },
                            placeholder: 'Write Your Audit Observation here',
                            fontFamily: {
                                options: [
                                    'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                                    'Courier New, Courier, monospace',
                                    'Georgia, serif',
                                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                    'Tahoma, Geneva, sans-serif',
                                    'Times New Roman, Times, serif',
                                    'Trebuchet MS, Helvetica, sans-serif',
                                    'Verdana, Geneva, sans-serif'
                                ],
                                supportAllValues: true
                            },
                            fontSize: {
                                options: [10, 12, 14, 'default', 18, 20, 22],
                                supportAllValues: true
                            },
                            htmlSupport: {
                                allow: [{
                                    name: /.*/,
                                    attributes: true,
                                    classes: true,
                                    styles: true
                                }]
                            },
                            link: {
                                decorators: {
                                    addTargetToExternalLinks: true,
                                    defaultProtocol: 'https://',
                                    toggleDownloadable: {
                                        mode: 'manual',
                                        label: 'Downloadable',
                                        attributes: {
                                            download: 'file'
                                        }
                                    }
                                }
                            },
                            removePlugins: [
                                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
                                'Base64UploadAdapter',
                                'MultiLevelList',
                                'RealTimeCollaborativeComments',
                                'RealTimeCollaborativeTrackChanges',
                                'RealTimeCollaborativeRevisionHistory',
                                'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                                'RevisionHistory', 'Pagination',
                                'WProofreader',
                                'MathType', 'SlashCommand', 'Template', 'DocumentOutline',
                                'FormatPainter',
                                'TableOfContents',
                                'PasteFromOfficeEnhanced', 'CaseChange'
                            ]
                        })
                        .then(editor => {
                            editor.enableReadOnlyMode('initial'); // Read-only mode

                            // Apply custom styling for scrolling
                            const editable = editor.ui.view.editable.element;
                            editable.style.maxHeight = 'auto'; // Set max height for the editable area
                            editable.style.overflowY = 'auto'; // Enable vertical scrolling
                        })
                        .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
                }
            });
        }
        /*************************************************  Ckeditor  *********************************************/



        function validateAllRows() {
            let isValid = true;
            let table = $('#para_details').DataTable();
            let checkedCount = 0;

            table.rows().every(function() {
                let rowNode = $(this.node());
                let isChecked = rowNode.find('.custom-checkbox').is(':checked');

                if (isChecked) {
                    checkedCount++;

                    let actionVal = rowNode.find('.action-select').val();

                    if (!actionVal) {
                        isValid = false;
                        rowNode.find('.action-select').addClass('is-invalid');
                    } else {
                        rowNode.find('.action-select').removeClass('is-invalid');
                    }
                }
            });

            if (checkedCount === 0) {
                alert('Please select at least one para.');
                return false;
            }

            return isValid;
        }



        function submitParaActions(data, statusflag) {
            var formData = new FormData($('#init_hlc')[0]);


            ['deptcode', 'distcode', 'regioncode'].forEach(function(id) {
                var $el = $('#' + id);

                if ($el.prop('disabled')) {
                    formData.append(id, $el.val());
                }
            });


            //Data-Table data append
            formData.append('rows', JSON.stringify(data));
            formData.append('statusflag', statusflag);



            $.ajax({
                url: '/apms/insert_apms_dlcaction',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {


                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        $('#ok_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            var lang = getLanguage('')
                            fetch_dhlcparadetails(lang)


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

                        var designation = lang == 'ta' ? 'பதவி' : 'Designation';
                        var status = lang == 'ta' ? 'நிலை' : 'Status';
                        var serialno = lang == 'ta' ? 'எஸ்.எண்' : 'S.No';
                        var actiontakenon = lang == 'ta' ? 'அனுப்பிய விவரங்கள்' :
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
                    'I': lang == 'en' ? 'Auditee' : 'தணிக்கையாளர்',
                    'A': lang == 'en' ? 'PSA Auditor' : 'பி.எஸ்.ஏ. தணிக்கையாளர்',
                    'AD': lang == 'en' ? 'PSA AD' : 'பி.எஸ்.ஏ. உதவி இயக்குநர்',
                    'DL': lang == 'en' ? 'District HLC' : 'மாவட்ட எச்எல்சி'
                };

                var actiontaken_name = ''
                switch (item.actroleactioncode) {
                    case 'I':
                        actiontaken_name = lang == 'ta' ?
                            (item.processcode == 'D' ?
                                'HLC துறைக்கு அனுப்பப்பட்டது' :
                                'பத்திக்கு பதிலளிக்கப்பட்டது') :
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
                                'தணிக்கையாளருக்கு அனுப்பப்பட்டது' :
                                'HLC துறைக்கு அனுப்பப்பட்டது') :
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

        const fields = [{
                labelKey: 'Type of Para',
                en: 'typeofparaename',
                ta: 'typeofparatname'
            },
            {
                labelKey: 'State Of Para',
                en: 'stateofparaename',
                ta: 'stateofparatname'
            },
            {
                labelKey: 'Para Number',
                key: 'paranumber'
            },
            {
                labelKey: 'Title/Heading',
                en: 'objectionename',
                ta: 'objectiontname'
            },
            {
                labelKey: 'Categorization of Paras',
                en: 'subobjectionename',
                ta: 'subobjectiontname'
            },
            {
                labelKey: 'Amount Involved',
                key: 'amtinvolved',
                type: 'amount'
            },
            {
                labelKey: 'Severity',
                key: 'severitycode'
            },
            //   {
            //         labelKey: 'Scheme',
            //         en: 'auditeeschemeelname',
            //         ta: 'auditeeschemetlname'
            //     },
            {
                labelKey: 'Irreularities',
                en: 'irregularitieselname',
                ta: 'irregularitiestlname'
            },
            {
                labelKey: 'Category ',
                en: 'irregularitiescatelname',
                ta: 'irregularitiescattlname'
            },
            {
                labelKey: 'Sub Category Details',
                en: 'irregularitiessubcatelname',
                ta: 'irregularitiessubcattlname'
            },
            // {
            //     labelKey: 'slip_details',
            //     key: 'slipdetails'
            // },
            {
                labelKey: 'Type Of Audit',
                en: 'typeofauditename',
                ta: 'typeofaudittname'
            },
            {
                labelKey: 'Remarks',
                key: 'remarks'
            },



        ];



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
            } else if (processcode == 'FA') {
                status = 'Forwarded to Approver';
            } else if (processcode == 'R') {
                status = 'Returned to committee';
            } else if (processcode == 'A') {
                status = 'Approved';
            } else {
                status = 'Entry';
            }

            return status;
        }
    </script>
@endsection
