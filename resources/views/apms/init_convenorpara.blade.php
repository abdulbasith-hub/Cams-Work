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

        .gist-column {
            white-space: normal !important;
            word-break: break-word;
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
        /* Checked checkbox */
        .custom-checkbox:checked,
        .selected_para_checkbox:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        /* Enabled but unchecked */
        .custom-checkbox:not(:disabled),
        .selected_para_checkbox:not(:disabled) {
            border-color: var(--bs-primary);
        }

        /* Disabled checkbox – dark ash */
        .custom-checkbox:disabled,
        .selected_para_checkbox:disabled {
            background-color: #dde4ea;
            border-color: #969a9d;
            opacity: 1;
            cursor: not-allowed;
        }

        /* Disabled & checked */
        .custom-checkbox:disabled:checked,
        .selected_para_checkbox:disabled:checked {
            background-color: #dde4ea;
            border-color: #969a9d;
        }

        /* Center tabs horizontally */
        /* center tab buttons */
        /* center tabs */
        .para-tabs {
            gap: 12px;
        }

        /* tab button style */
        .tab-btn {
            border-radius: 10px !important;
            padding: 8px 24px;
            font-weight: 600;
            background: #f1f3f5;
            color: #495057;
            border: 1px solid #dee2e6;
            transition: 0.25s;
        }

        /* hover effect */
        .tab-btn:hover {
            background: #e9ecef;
        }

        /* ACTIVE TAB */
        .tab-btn.active {
            background: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }

        /* tab content card */
        .tab-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            background: #fafafa;
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
        $sessioncatcode = $sessionchargedel->catcode;
        $sessionsubcatid = $sessionchargedel->subcatid;
 	$sessionroleactioncode = $sessionchargedel->roleactioncode;

        $shlc_roleactioncode = $shlc_roleactioncode;
        $dehc_roleactioncode = $dehc_roleactioncode;
        $dlc_roleactioncode = $dlc_roleactioncode;
		
        $dlc = !empty($dlcpara) && count($dlcpara) > 0 ? $dlcpara[0] : null;

        $make_dept_disable = $deptcode || $dlc ? 'disabled' : '';
        $make_region_disable = $regioncode || $dlc ? 'disabled' : '';
        $make_dist_disable = $distcode || $dlc ? 'disabled' : '';
        $make_cat_disable = $sessioncatcode || $dlc ? 'disabled' : '';
        $make_subcat_disable = $sessionsubcatid || $dlc ? 'disabled' : '';
        $make_inst_disable = !empty($dlc->instid ?? null) ? 'disabled' : '';

        $selectedInst = [];

        if ($dlc && !empty($dlc->instid)) {
            $selectedInst = json_decode($dlc->instid, true);
        }
        $make_subcat_hidden = $sessionsubcatid ? 'hide_this' : '';

    @endphp
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <form id="init_dhlc" name="init_dhlc">
        @csrf
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
		@if ($sessionroleactioncode == $dehc_roleactioncode)
                    <div class="card-header lang card_header_color" key="dehlc_label">Department High Level Committee</div>
                @else
                    <div class="card-header lang card_header_color" key="dlc_label">District High Level Committee</div>
                @endif                <div class="card-body">
                    <div class="row">

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="department"
                                    for="validationDefault01">Department</label>
                                <input type="hidden" id="apms_hlcid" name="apms_hlcid" value="">
                                <input type="hidden" id="selected_paras" name="selected_paras" value="">

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','')">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>

                                    @if (!empty($dept) && count($dept) > 0)
                                        @foreach ($dept as $department)
                                            <option value="{{ $department->deptcode }}"
                                                @if (($dlc && $dlc->deptcode == $department->deptcode) || old('dept', $deptcode) == $department->deptcode) selected @endif
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
                                                @if (($dlc && $dlc->regioncode == $reg->regioncode) || old('region', $regioncode) == $reg->regioncode) selected @endif
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
                                                @if (($dlc && $dlc->distcode == $dist->distcode) || old('dist', $distcode) == $dist->distcode) selected @endif
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
                                    onchange="onchange_category('','','','','','')" <?php echo $make_cat_disable; ?>>
                                    <option value="" data-name-en="---Select Category---"
                                        data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                    <option value="" disabled id="" data-name-en="No Category Available"
                                        data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                                </select>
                            </div>


                            <div class="col-md-4 mb-2 subcatdiv  ">
                                <label class="form-label lang required" key="subcategoryColumn"
                                    for="subcatid">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2  <?php echo $make_subcat_hidden; ?>"
                                    id="subcatid" name="subcatid" onchange="onchange_subcategory('','','','','','')"
                                    <?php echo $make_subcat_disable; ?>>
                                    <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---
                                    </option>

                                    <option value="" disabled data-name-en="No SubCategory Available"
                                        data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option>


                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution" for="institution">Auditable
                                    Institution</label>
                                <select class="form-select mr-sm-2 select2" id="instid" name="instid[]" multiple
                                    <?php echo $make_inst_disable; ?>>


                                </select>

                            </div>



                        </div>

                        <div class="row ">
                            <div class="col-md-3 mx-auto text-center">
                                <!-- Adding text-center to center the content inside -->
                                <input type="hidden" name="action" id="action" value="insert" />


                                <button class="btn btn-primary mt-3 lang" key="" type="" action="insert"
                                    id="mom_submit" name="mom_submit">Submit</button>


                            </div>
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
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="mom_date" for="">Meeting
                                    Date</label>

                                <div class="input-group" onclick="datepicker('mom_date','')">
                                    <input type="text" class="form-control datepicker" id="mom_date" name="mom_date"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key=" " for="">Minutes
                                    Document</label>
                                <span style="color:#ff0000; font-size:10px;">(Max Size : 2 MB &
                                    File
                                    Format
                                    : Pdf )</span>

                                <input type="file" class="form-control" id="file" name="file"
                                    accept="application/pdf">
                                <input type="hidden" class="form-control" id="uploadid" name="uploadid">

                                <div id="view_file-list-container">
                                </div>
                            </div>
                        </div>

                        <span class="small text-danger  fw-bold">
                            <span class="text-danger fw-bold">*</span>
                            Para can be selected, only after the verification of para details
                        </span>
                        <div class=" mt-4 hide_this" id="para_selection_div">
                            <div class="card card_color">
                                <div class="card-body">
                                    <ul class="nav justify-content-center para-tabs" id="paraTabs">

                                        <li class="nav-item">
                                            <button class="nav-link active tab-btn" data-bs-toggle="tab"
                                                data-bs-target="#selected" type="button">
                                                Selected <span class="badge bg-info" id="selected_count">0</span>
                                            </button>
                                        </li>

                                        <li class="nav-item">
                                            <button class="nav-link tab-btn" data-bs-toggle="tab"
                                                data-bs-target="#unselected" type="button">
                                                Unselected <span class="badge bg-info" id="unselected_count">0</span>
                                            </button>
                                        </li>

                                        <li class="nav-item">
                                            <button class="nav-link tab-btn" data-bs-toggle="tab"
                                                data-bs-target="#returned" type="button">
                                                Returned <span class="badge bg-info" id="rejected_count">0</span>
                                            </button>
                                        </li>

                                    </ul>


                                    <div class="tab-content mt-4">

                                        <div class="tab-pane fade show active" id="selected">
                                            <div class="tab-card">
                                                <table id="selected_details"
                                                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">

                                                    <thead>
                                                        <tr>
                                                            <th class="lang text-wrap" key="s_no">S.No
                                                            </th>
                                                            <th class="text-wrap" key="institute_detail">
                                                                Institute Details</th>
                                                            <th class="text-wrap" key="audityear">Audit
                                                                Year</th>
                                                            <th class="text-wrap" key="para_no">Para Number
                                                            </th>
                                                            <th class="text-wrap" key="typeofirregularities_label">Type of
                                                                Irregularities</th>
                                                            <th class="lang " key="gistofobservations_label">Gist of
                                                                Observations
                                                            </th>
                                                            {{-- <th class="text-wrap" key="objection_name" style="width:20%">Title Heading</th> --}}
                                                            <th class="lang " key="para_details">View Para
                                                                Details</th>
                                                            <th class=" lang" key="select_para">Select Para
                                                                for action</th>
                                                            <th class="all lang" key="action">Action</th>
                                                        </tr>

                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div id="return_button_set" class="hide_this">
                                                    <div class="row mt-1">
                                                        <div class="col-md-6" style="margin-left: 39% !important;">
                                                            <div class="d-flex align-items-center gap-6">
                                                                <input type="hidden" id="action" name="action"
                                                                    value="insert">
                                                                <button class="btn button_save" id="return_buttonaction"
                                                                    name="buttonaction">Save Draft
                                                                </button>
                                                                <button class="btn button_finalise"
                                                                    id="return_finalisebtn">Finalise</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id='no_para_selected' class='hide_this'>
                                                    <center>No para was selected</center>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="unselected">
                                            <div class="tab-card">

                                                <table id="unselectedpara_details"
                                                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">

                                                    <thead>
                                                        @csrf
                                                        <tr>
                                                            <th class="lang text-wrap" key="s_no">S.No
                                                            </th>


                                                            <th class="text-wrap" key="institute_detail">
                                                                Institute Details</th>
                                                            <th class="text-wrap" key="audityear">Audit
                                                                Year</th>
                                                            <th class="text-wrap" key="para_no">Para Number
                                                            </th>
                                                            <th class="text-wrap" key="typeofirregularities_label">Type of
                                                                Irregularities</th>
                                                            <th class="lang " key="gistofobservations_label">Gist of
                                                                Observations
                                                            </th>
                                                            {{-- <th class="text-wrap" key="objection_name" style="width:20%">Title Heading</th> --}}
                                                            <th class="lang " key="para_details">View Para
                                                                Details</th>
                                                            <th class=" lang" key="select_para">Select Para
                                                                for action</th>
                                                        </tr>

                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="d-flex justify-content-center">
                                                    <a class="btn btn-success"
                                                        onclick="add_selected_para(event,'unselected')">
                                                        Select Para
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="returned">
                                            <div class="tab-card">
                                                <table id="returnedpara_details"
                                                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">

                                                    <thead>
                                                        @csrf
                                                        <tr>
                                                            <th class="lang text-wrap" key="s_no">S.No
                                                            </th>

                                                            <th class="text-wrap" key="institute_detail">
                                                                Institute Details</th>
                                                            <th class="text-wrap" key="audityear">Audit
                                                                Year</th>
                                                            <th class="text-wrap" key="para_no">Para Number
                                                            </th>
                                                            <th class="text-wrap" key="typeofirregularities_label">Type of
                                                                Irregularities</th>
                                                            <th class="lang " key="gistofobservations_label">Gist of
                                                                Observations
                                                            </th>
                                                            {{-- <th class="text-wrap" key="objection_name" style="width:20%">Title Heading</th> --}}
                                                            <th class="lang " key="para_details">View Para
                                                                Details</th>
                                                            <th class=" lang" key="">Approver Remarks</th>
                                                            <th class=" lang" key="action"> Action
                                                            </th>
                                                            <th class=" lang" key="select_para">Select Para
                                                                for action</th>

                                                        </tr>

                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="d-flex justify-content-center">
                                                    <a class="btn btn-success"
                                                        onclick="add_selected_para(event,'returned')">
                                                        Select Para
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>



                        </div>
                        <table id="para_details"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">

                            <thead>
                                @csrf
                                <tr>
                                    <th class="lang text-wrap" style="width:5%" key="s_no">S.No</th>
                                    <th class="text-wrap" key="institute_detail" style="width:15%">Institute Details</th>
                                    <th class="text-wrap" key="audityear" style="width:10%">Audit Year</th>
                                    <th class="text-wrap" key="para_no" style="width:5%">Para Number</th>
                                    <th class="text-wrap" key="typeofirregularities_label" style="width:5%">Type of
                                        Irregularities</th>
                                    <th class="lang " key="gistofobservations_label" style="width:20%">Gist of
                                        Observations
                                    </th>
                                    {{-- <th class="text-wrap" key="objection_name" style="width:20%">Title Heading</th> --}}
                                    <th class="lang " key="para_details" style="width:5%">View Para Details</th>
                                    <th class=" lang" key="select_para" style="width:5%">Select Para for action</th>
                                    <th class="all lang" key="action" style="width:10%">Action</th>
                                </tr>

                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class=''>
                    <center>No Data Available</center>
                </div>

                <div id="button_set" class="hide_this">
                    <div class="row mt-1">
                        <div class="col-md-6" style="margin-left: 39% !important;">
                            <div class="d-flex align-items-center gap-6">
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
        </div>

        <div id="parahistory_details" class="modal fade" tabindex="-1" aria-labelledby="parahistory_details modalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-info text-white">
                        <h4 class="modal-title text-white lang" id="info-header-modalLabel" key="para_history_det">
                            Para History Details
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="para_det">

                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Edit
                    </button> --}}
                        <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" key="cancelbtn">
                            Close
                        </button>
                        {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div id="parafull_details" class="modal fade" tabindex="-1" aria-labelledby="parafull_details modalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-info text-white">
                        <h4 class="modal-title text-white lang" id="info-header-modalLabel" key="parafull_details_det">
                            Para Details
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="parafull_det">
                            <table class="table table-bordered">
                                <tbody id="parafull_det"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Edit
                    </button> --}}
                        <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" key="cancelbtn">
                            Close
                        </button>
                        {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </form>
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
        var sessionroletypecode = '<?php echo $roleTypeCode; ?>'
        var sessiondeptcode = ' <?php echo $deptcode; ?>';
        var sessiondistcode = ' <?php echo $distcode; ?>';
        var sessionregioncode = ' <?php echo $regioncode; ?>';
        let sessioncatcode = ' <?php echo $sessioncatcode; ?>';
        let sessionsubcatid = ' <?php echo $sessionsubcatid; ?>';
        let unselectedCount = 0;
        let returnedCount = 0;
        let selectedCount = 0;
        let selectedParas = [];
        let unselectedParas = [];
        let returnedParas = [];
        let checkedParas = [];

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {

            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();

        });
        //---------------------------Language---------------------//
        $('#translate').change(function() {
            const lang = getLanguage('Y') // Store language selection
            // updateTableLanguage(
            //     lang); // Update the table with the new language by destroying and recreating it

            updateValidationMessages(lang, 'init_dhlc');

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
                        width: "15%"
                    }, // Title Heading
                    {
                        targets: 2,
                        width: "10%"
                    }, // Para Number
                    {
                        targets: 3,
                        width: "10%"
                    }, // Slip Details
                    {
                        targets: 4,
                        width: "10%"
                    }, // View Para Details
                    {
                        targets: 5,
                        width: "20%"
                    }, // Action
                    {
                        targets: 6,
                        width: "10%"
                    }, // Action
                    {
                        targets: 7,
                        width: "5%"
                    }, // Action
                    {
                        targets: 8,
                        width: "15%"
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
                        data: "irregularitieselname",
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            return row[Irregularities] || '-';
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: "slipdetails",
                        //title: columnLabels?.[Dept]?.[language] || 'Department',
                        render: function(data, type, row) {
                            return row.slipdetails || '-';
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
                                    type="button" class="btn-sm btn btn-primary enable-row " onclick="get_para_historydetails(this,'view_flow')" data-followupid=${row.followupid} data-para=${row.encrypted_paraid}  data-action="flow" style="font-size:10px">
                                    <i class="ti ti-history fs-2 me-2"></i> ${det_btn}
                                </button><div style="height:5px;"></div>`
                            const buttonHtml = `
                               <button
                        type="button" class="btn-sm btn btn-info enable-row " onclick="get_para_historydetails(this,'view_para')" data-followupid=${row.followupid} data-para=${row.encrypted_paraid}   data-action="para" style="font-size:10px">
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


                            const isChecked = row.hlc_para === 'Y';

                            const isDisabled = row.hlc_para !== 'Y';
                            const checkboxHtml = `
                             <input type="checkbox"
                               class="form-check-input me-2  custom-checkbox"
                                ${isChecked ? 'checked' : ''}
                               ${isDisabled ? 'disabled' : ''}
                               data-instid ="${row.instid}"
                               data-followupid ="${row.followupid}"
                               data-para="${row.encrypted_paraid}">
                             `;

                            return `
                        <div class="d-flex align-items-center gap-2">
                            ${checkboxHtml}

                        </div>
                    `;
                        },
                        className: 'text-wrap text-start'
                    },

                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row, meta) {

                            const isaction_disabled = (row.hlc_para == 'Y') ? false : true;

                            let options = `

                            <option value="">---Select Action---</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act->actioncode }}" data-paraid="${row.encrypted_paraid}"
                                    ${row.actioncode == '{{ $act->actioncode }}' ? 'selected' : ''} ${isaction_disabled}>
                                    {{ $act->actionename }}
                                </option>
                            @endforeach
                        `;

                            return `<select class="form-select action-select">${options}</select>`;

                        },
                        className: "text-center text-wrap noExport"
                    }
                ],

            });


            const mobileColumns = [Category, SubCategory, TypeofAudit, "paranumber", "updatedon"];
            setupMobileRowToggle(mobileColumns);
            updatedatatable(language, "para_details");
        }


        function renderSelectedTable(data, language = 'en') {

            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Irregularities = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';

            if ($.fn.dataTable.isDataTable('#selected_details')) {
                $('#selected_details').DataTable().clear().destroy();
            }
            data.forEach(r => {
                r.selected_action = r.selected_action || '';
            });
            $('#selected_details').DataTable({

                rowId: 'encrypted_paraid',
                processing: true,
                serverSide: false,
                scrollX: true,
                scrollCollapse: true,
                lengthChange: false,
                autoWidth: false,
                responsive: true,
                destroy: true,
                data: data,
                columnDefs: [

                    // S.No width
                    {
                        targets: 0,
                        width: "60px"
                    },

                    // Institute
                    {
                        targets: 1,
                        width: "180px"
                    },

                    // Audit period
                    {
                        targets: 2,
                        width: "120px"
                    },

                    // Para No
                    {
                        targets: 3,
                        width: "100px"
                    },

                    // Irregularities
                    {
                        targets: 4,
                        width: "180px"
                    },

                    // Gist / Slipdetails (IMPORTANT)
                    {
                        targets: 5,
                        width: "150px",
                        className: "gist-column"
                    },

                    // View para button
                    {
                        targets: 6,
                        width: "150px",
                        orderable: false
                    },

                    // Checkbox column
                    {
                        targets: 7,
                        width: "80px",
                        orderable: false,
                        className: "text-center"
                    },

                    // Action select
                    {
                        targets: 8,
                        width: "160px",
                        orderable: false
                    }

                ],
                columns: [

                    // S.No
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        className: 'text-end'
                    },

                    // Institute
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[InstituteName] || '-';
                        }
                    },

                    // Audit Period
                    {
                        data: "audit_period",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Para No
                    {
                        data: "parano",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Irregularities
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[Irregularities] || '-';
                        }
                    },
                    // Slip DEtails
                    {
                        data: "slipdetails",
                        render: function(data, type, row) {



                            return `
                             ${data || '-'}

                                             `;
                        },
                        className: "text-start text-wrap "
                    },

                    // View Para
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {
                            let paratype = 'Selected Para';
                            let badgeClass = 'bg-primary';

                            if (row.is_approved === 'Y') {
                                paratype = 'Approved Para';
                                badgeClass = 'bg-success';
                            } else if (row.is_rejected === 'Y') {
                                paratype = 'Rejected Para';
                                badgeClass = 'bg-danger';
                            }

                            const badge = `<span class="badge ${badgeClass}">${paratype}</span>`;

                            const para_btn = language == 'ta' ?
                                'பாரா விவரங்களைக் காண்க' :
                                'View Para Details';

                            return `
                       <button
                        type="button"
                        class="btn-sm btn btn-info"
                        onclick="get_para_historydetails(this,'view_para')"
                        data-followupid="${row.followupid}"
                        data-para="${row.encrypted_paraid}">
                        <i class="ti ti-clipboard-data"></i> ${para_btn}
                            </button>
                            <br>
                              <br>
                            ${badge}
                         `;
                        },
                        className: "text-start text-wrap "
                    },


                    // Action Column

                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {


                            const isChecked = row.is_approved === 'Y' || row.is_selected === 'Y';

                            const isDisabled = row.is_approved == 'Y';
                            const checkboxHtml = `
                             <input type="checkbox"
                               class="form-check-input me-2  selected_para_checkbox"
                                ${isChecked ? 'checked' : ''}
                               ${isDisabled ? 'disabled' : ''}
                               data-instid ="${row.instid}"
                               data-followupid ="${row.followupid}"
                               data-para="${row.encrypted_paraid}">
                             `;

                            return `
                        <div class="d-flex align-items-center gap-2">
                            ${checkboxHtml}

                        </div>
                    `;
                        },

                    },
                    // Action Column
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row, meta) {

                            const isDisabled = row.is_approved === 'Y' || row.is_selected === 'Y';;

                            let options = `

                            <option value="">---Select Action---</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act->actioncode }}" data-paraid="${row.encrypted_paraid}"
                                    ${row.actioncode == '{{ $act->actioncode }}' ? 'selected' : ''} >
                                    {{ $act->actionename }}
                                </option>
                            @endforeach
                        `;

                            return `<select class="form-select action-select"    ${isDisabled ? 'disabled' : ''}> ${options}</select>`;

                        },

                    }

                ]

            });

            let initialSelected = [];

            data.forEach(row => {
                if ((row.is_approved || row.selected) === 'Y') {
                    initialSelected.push(row.followupid);
                }
            });

            $('#selected_paras').val(JSON.stringify(initialSelected));

        }

        function renderUnselectedTable(data, language) {

            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Irregularities = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';

            if ($.fn.dataTable.isDataTable('#unselectedpara_details')) {
                $('#unselectedpara_details').DataTable().clear().destroy();
            }

            $('#unselectedpara_details').DataTable({

                rowId: 'encrypted_paraid',
                processing: true,
                serverSide: false,
                scrollX: true,
                scrollCollapse: true,
                lengthChange: false,
                autoWidth: false,
                responsive: true,
                destroy: true,
                data: data,


                columns: [

                    // S.No
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        className: 'text-end'
                    },

                    // Select Checkbox


                    // Institute
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[InstituteName] || '-';
                        }
                    },

                    // Audit Period
                    {
                        data: "audit_period",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Para No
                    {
                        data: "parano",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Irregularities
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[Irregularities] || '-';
                        }
                    },

                    // Gist
                    {
                        data: "slipdetails",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // View Para Details
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {

                            const para_btn = language == 'ta' ?
                                'பாரா விவரங்களைக் காண்க' :
                                'View Para Details';

                            return `
                        <button
                        type="button"
                        class="btn-sm btn btn-info"
                        onclick="get_para_historydetails(this,'view_para')"
                        data-followupid="${row.followupid}"
                        data-para="${row.encrypted_paraid}">
                        <i class="ti ti-clipboard-data"></i> ${para_btn}
                        </button>
                    `;
                        }
                    },

                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {

                            return `
                        <input type="checkbox"
                        class="form-check-input select-unselected-para"
                        data-followupid="${row.followupid}"
                        data-para="${row.encrypted_paraid}">
                    `;
                        }
                    },

                ]

            });

        }
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {

            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust()
                .draw();

        });

        function renderReturnedTable(data, language) {

            const InstituteName = language === 'ta' ? 'insttname' : 'instename';
            const Irregularities = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';

            if ($.fn.dataTable.isDataTable('#returnedpara_details')) {
                $('#returnedpara_details').DataTable().clear().destroy();
            }

            $('#returnedpara_details').DataTable({

                rowId: 'encrypted_paraid',
                processing: true,
                serverSide: false,
                scrollX: true,
                scrollCollapse: true,
                lengthChange: false,
                autoWidth: false,
                responsive: true,
                destroy: true,
                data: data,
                initComplete: function() {
                    var table = this.api();
                    setTimeout(function() {
                        table.columns.adjust();
                    }, 100);
                },
                columnDefs: [{
                        targets: 0,
                        width: "5%"
                    },
                    {
                        targets: 1,
                        width: "5%"
                    },
                    {
                        targets: 2,
                        width: "15%"
                    },
                    {
                        targets: 3,
                        width: "10%"
                    },
                    {
                        targets: 4,
                        width: "5%"
                    },
                    {
                        targets: 5,
                        width: "5%"
                    },
                    {
                        targets: 6,
                        width: "15%"
                    },
                    {
                        targets: 7,
                        width: "10%"
                    },
                    {
                        targets: 8,
                        width: "10%"
                    },
                    {
                        targets: 9,
                        width: "5%"
                    },

                ],

                columns: [

                    // S.No
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        className: 'text-end'
                    },

                    // Select Checkbox


                    // Institute
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[InstituteName] || '-';
                        }
                    },

                    // Audit Period
                    {
                        data: "audit_period",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Para No
                    {
                        data: "parano",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Irregularities
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row[Irregularities] || '-';
                        }
                    },

                    // Gist
                    {
                        data: "slipdetails",
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // View Para
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {
                            let paratype = 'Selected Para';
                            let badgeClass = 'bg-primary';

                            if (row.is_approved === 'Y') {
                                paratype = 'Approved Para';
                                badgeClass = 'bg-success';
                            } else if (row.is_rejected === 'Y') {
                                paratype = 'Rejected Para';
                                badgeClass = 'bg-danger';
                            }

                            const badge = `<span class="badge ${badgeClass}">${paratype}</span>`;
                            const para_btn = language == 'ta' ?
                                'பாரா விவரங்களைக் காண்க' :
                                'View Para Details';

                            return `
                              <button
                              type="button"
                              class="btn-sm btn btn-info"
                              onclick="get_para_historydetails(this,'view_para')"
                              data-followupid="${row.followupid}"
                              data-para="${row.encrypted_paraid}">
                              <i class="ti ti-clipboard-data"></i> ${para_btn}
                              </button>
                              <br>
                               <br>
                              ${badge}
                            `;
                        }
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
                        render: function(data, type, row) {

                            let options = `
                         <option value="">---Select Action---</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act->actioncode }}" data-paraid="${row.encrypted_paraid}"
                                    ${row.actioncode == '{{ $act->actioncode }}' ? 'selected' : ''}  >
                                    {{ $act->actionename }}
                                </option>
                            @endforeach
                        `;

                            return `<select class="form-select action-select" disabled>${options}</select>`;

                        }
                    },
                    {
                        data: "encrypted_paraid",
                        render: function(data, type, row) {

                            return `
                        <input type="checkbox"
                        class="form-check-input select-unselected-para"
                        data-followupid="${row.followupid}"
                        data-para="${row.encrypted_paraid}">
                    `;
                        }
                    },


                ]

            });

        }
        //------------------------------Data Table----------------------//



        //------------------------------Button Click----------------------//

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



        var validator = $("#init_dhlc").validate();
        $.validator.addMethod("pdfOnly", function(value, element) {
            if (element.files.length === 0) return true;
            return element.files[0].type === "application/pdf";
        }, "Only PDF files are allowed");

        $.validator.addMethod("maxFileSize", function(value, element, param) {
            if (element.files.length === 0) return true;
            return element.files[0].size <= param;
        }, "File size must not exceed 2 MB");


        $("[name='file']").rules("add", {

            pdfOnly: true,
            maxFileSize: 2097152, // 2MB

            messages: {

                pdfOnly: "Only PDF file is allowed",
                maxFileSize: "File size should not exceed 2 MB"
            }
        });


        // trigger validation immediately on change
        $("[name='file']").on("change", function() {
            $(this).valid();
        });

        $('#buttonaction, #finalisebtn').on('click', function(e) {
            e.preventDefault();

            $("[name='mom_date']").rules("add", {
                required: true,
                messages: {
                    required: "Please select Meeting date"
                }
            });

            $("[name='file']").rules("add", {
                required: true,
                messages: {
                    required: "Please upload file"
                }
            });
            if ($("#init_dhlc").valid()) {





                var fileid = $('#uploadid').val();

                if (!validateAllRows('#para_details')) {
                    return false;
                }
                const buttonaction = this.id;
                let actionType = buttonaction === 'finalisebtn' ? 'finalise' : 'draft';
                let para_action = buttonaction === 'finalisebtn' ? 'finalise' : 'draft';

                let table = $('#para_details').DataTable();
                let statusflag = this.id === 'finalisebtn' ? 'F' : 'Y';

                let rowData = collectRowData(
                    '#para_details',
                    '.custom-checkbox',
                    statusflag,
                    actionType
                );






                //validate file type
                var form = document.getElementById('init_dhlc');
                var formData = new FormData(form);
                var file = formData.get('file');
                var fileid = $('#uploadid').val();


                if (!fileid) {
                    if (!file || file.type !== 'application/pdf') {
                        passing_alert_value("Alert", 'Only PDF files are allowed',
                            "confirmation_alert", "alert_header",
                            "alert_body",
                            "confirmation_alert");
                        return false;
                    }

                    // //validate file size
                    var fileSizeMB = file.size / (1024 * 1024);
                    if (fileSizeMB > maxSize) {

                        var messagecontent = 'File size must be less than or equal to ' + maxSize + ' MB';


                        passing_alert_value("Alert", messagecontent,
                            "confirmation_alert", "alert_header",
                            "alert_body",
                            "confirmation_alert");

                        return false;
                    }
                }

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



        //------------------------------Button Click----------------------//

        //------------------------------Populate History Data-------------------------//
        function populate_paradetails(para_data) {

            var innerHTML = ``;
            const modelbody = $('#para_det'); // Select the table's tbody
            modelbody.empty(); // Clear existing row
            let editorIds = [];

            para_data.forEach((data, index) => {
                let editorId = `editor${index}`;
                editorIds.push(editorId);
                const isFirst = index === 0;

                let accordionId = `remarkAccordion${index}`;
                let collapseId = `collapse${index}`;
                let headerId = `heading${index}`;
                let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";
                let forwardedBy = (data.usertypecode === 'I' ? data.auditeename : data.username) || "-";
                let designation = (data.usertypecode === 'I' ? 'Auditee' : data.desigesname) || "-";

                let bg_color = (data.actroleactioncode === 'I' ? 'auditee_div' : data.actroleactioncode ===
                    'A' ?
                    'auditor_div' : 'ad_div') || "-";
                let body_color = (data.actroleactioncode === 'I' ? 'auditee_body' : data.actroleactioncode ===
                    'A' ?
                    'auditor_body' : 'ad_body') || "-";
                let actroleactioncode = (data.actroleactioncode === 'I' ? 'Auditee' : data.actroleactioncode ===
                    'DL' ? 'District HLC' : data.actroleactioncode ===
                    'A' ?
                    'PSA Auditor' : 'PSA AD') || "-";
                let actionname = lang == 'ta' ? data.actiontname : data.actionename;

                // Remarks parsing
                let remarkContent = "No remarks provided";
                try {
                    if (data.para_remarks) {
                        let parsed = JSON.parse(data.para_remarks);
                        remarkContent = parsed.content || remarkContent;
                    } else if (data.para_historyremarks) {
                        remarkContent = data.para_historyremarks;
                    }
                } catch (e) {
                    remarkContent = data.para_remarks || remarkContent;
                }
                let action_div = "";
                if (data.actroleactioncode == 'A' || data.actroleactioncode == 'AD' || data.actroleactioncode ==
                    'DL') {
                    action_div = `<div class="col-md-3">
                                <label><strong>Action to be taken</strong></label>
                                <p>${actionname}</p>

                            </div>`;
                }
                // Attachments (only if present)
                let attachmentHTML = "";
                if (data.auditeefileupload && data.auditeefileupload != '') {
                    files = getfile(data.auditeefileupload)
                    attachmentHTML = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label"><strong>Attachments:</strong></label>
                        <div class="d-flex flex-row">
                            <div class="position-relative d-flex flex-row" id="history_filediv_${index}">
                            </div>
                        </div>
                    </div>
                </div>
            `;



                }

                // Accordion markup
                let accordion = document.createElement("div");
                accordion.classList.add("accordion", "my-2");
                accordion.id = accordionId;
                innerHTML = `
            <div class="accordion-item " >
                <h2 class="accordion-header" id="${headerId}">
                    <button style="height: 50px;" class="${bg_color} accordion-button auditor_bg collapsed rounded"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="true"
                            aria-controls="${collapseId}">
                        <div class="d-flex flex-column w-100">
                            <div class="d-none d-md-flex align-items-center p-2 w-100">
                                <div class="text-truncate fw-bold">
                                   ${actroleactioncode}
                                </div>
                                <div class="text-muted small ms-2" style="margin-left:20px;">
                                    ${forwardedOn}
                                </div>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                    <div class="accordion-body ${body_color}">

                        <div class="row mb-3">
                            <div class="col-md-12">
                             ${action_div}
                             </div>
                            <div class="col-md-12">
                                <label><strong>Remarks:</strong></label>
                                <div class="editor-container border p-2 rounded bg-light">
                                    <textarea id=${editorId} class="form-control ">${remarkContent}</textarea>

                                </div>
                            </div>

                        </div>
                        ${attachmentHTML}
                    </div>
                </div>
            </div>
            <hr class="p-2">
            `;



                modelbody.append(innerHTML);
                if (data.auditeefileupload) {
                    UploadedFileList(files, '', `history_filediv_${index}`, '', 'fileuploadid');
                }
            });
            initializeEditors(editorIds)
            $('#parahistory_details').modal('show');

        }

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

        function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
            const $container = $('#' + containerid).empty();


            files.forEach(file => {
                if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);
                let url = `/storage/${file.path}`;
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
                                    <a class="fs-3 fw-semibold" style="color:black;" href="${url}" target="_blank">${file.name}</a>
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
            var validator = $("#init_dhlc").validate({

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
                    instid: {
                        required: true
                    },
                    mom_date: {
                        required: true
                    },
                    file: {
                        required: true
                    },

                },


                messages: {
                    deptcode: {
                        required: "Please select Department"
                    },
                    catcode: {
                        required: "Please select Category"
                    },
                    subcatid: {
                        required: "Please select Sub Category"
                    },
                    regioncode: {
                        required: "Please select Region"
                    },
                    distcode: {
                        required: "Please select District"
                    },
                    instid: {
                        required: "Please select Institution"
                    },
                    mom_date: {
                        required: "Please select Meeting Date"
                    },
                    file: {
                        required: "Please upload meeting document"
                    }
                },
                errorPlacement: function(error, element) {

                    // 🔥 Remove existing error message first (prevents duplicate append)
                    element.closest("div").find("span.error").remove();

                    if (element.hasClass("datepicker")) {
                        error.insertAfter(element.closest(".input-group"));
                    } else if (element.hasClass("select2")) {
                        error.insertAfter(element.next(".select2-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }


            });


            $("#mom_submit").on("click", function(event) {
                event.preventDefault();


                if ($("#init_dhlc").valid()) {

                    //validate file type
                    // var form = document.getElementById('init_dhlc');
                    // var formData = new FormData(form);
                    // var file = formData.get('file');

                    // if (!file || file.type !== 'application/pdf') {
                    //     passing_alert_value("Alert", 'Only PDF files are allowed',
                    //         "confirmation_alert", "alert_header",
                    //         "alert_body",
                    //         "confirmation_alert");
                    //     return false;
                    // }

                    // //validate file size
                    // var fileSizeMB = file.size / (1024 * 1024);
                    // if (fileSizeMB > maxSize) {

                    //     var messagecontent = 'File size must be less than or equal to ' + maxSize + ' MB';


                    //     passing_alert_value("Alert", messagecontent,
                    //         "confirmation_alert", "alert_header",
                    //         "alert_body",
                    //         "confirmation_alert");

                    //     return false;
                    // }

                    var lang = getLanguage('')

                    fetch_dhlcparadetails(lang)




                } else {
                    scrollToFirstError()
                    console.log("Form validation failed.");
                }
            });




        });
        //------------------------------Validator-------------------------//

        //------------------------------INSERT-------------------------//
        function insert_apms_hlc() {

            var formData = new FormData($('#init_dhlc')[0]);

            ['deptcode', 'distcode', 'regioncode'].forEach(function(id) {
                var $el = $('#' + id);

                if ($el.prop('disabled')) {
                    formData.append(id, $el.val());
                }
            });
            $('#loader').show()
            $.ajax({
                url: '/apms/insert_apms_hlc', // For creating a new user or updating an existing one
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
                complete: function() {
                    $('#loader').addClass('d-none');
                    $('#finalisebtn').prop('disabled', false);
                    $('#process_button').prop('disabled', false);
                    $('#buttonaction').removeAttr('disabled');
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
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

                    }
                }

            });
        }
        //------------------------------INSERT-------------------------//


        //------------------------------FETCH-------------------------//
        function fetch_dhlcparadetails(language, data) {

            $('#loader').show()

            var formData = $('#init_dhlc').serializeArray();
            ['deptcode', 'distcode', 'regioncode', 'catcode', 'subcatid'].forEach(function(id) {
                const $el = $('#' + id);

                if ($el.prop('disabled') || !$el.val()) {
                    formData.push({
                        name: id,

                        value: data ? data[id] : $el.val()


                    });
                }
            });
            if (data && data.instid) {

                const instidArray = Array.isArray(data.instid) ?
                    data.instid :
                    JSON.parse(data.instid);

                instidArray.forEach(id => {
                    formData.push({
                        name: 'instid[]',
                        value: id
                    });
                });


            }


            // ['catcode', 'subcatid', 'instid'].forEach(function(id) {
            //     const $el = $('#' + id);

            //     if ($el.prop('disabled') || !$el.val()) {
            //         formData.push({
            //             name: id,
            //             value: data ? data[id] : $el.val()

            //         });
            //     }
            // });

            $.ajax({
                url: '/apms/fetch_dhlcparadetails', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                success: function(json) {

                    if (json.data && json.data.length > 0) {

                        if (json.hlcdata && json.hlcdata.length > 0) {

                            const hlc_processcode = json.hlcdata[0].processcode;

                            if (hlc_processcode == 'R') {

                                $('#para_selection_div,#tableshow').show();
                                $('#para_details').hide();
                                $('#no_data').hide();

                                let allData = json.data || [];

                                // selectedParas = allData.filter(p => p.is_selected === 'Y' || p.is_approved ===
                                //     'Y' );


                                selectedParas = allData.filter(p =>
                                    (p.is_selected === 'Y' || p.is_approved === 'Y') &&
                                    p.is_rejected !== 'Y'
                                );

                                updateSelectedParasHidden();
                                unselectedParas = allData.filter(p =>
                                    p.is_selected !== 'Y' &&
                                    p.is_approved !== 'Y' &&
                                    p.is_rejected === 'N'
                                );

                                returnedParas = allData.filter(p =>
                                    p.is_selected !== 'Y' &&
                                    p.is_rejected === 'Y'
                                );

                                selectedCount = selectedParas.length;
                                unselectedCount = unselectedParas.length;
                                returnedCount = returnedParas.length;

                                // $('#selected_details').hide()
                                // $('#no_para_selected').show()


                                $('#selected_count').text(selectedCount);
                                $('#unselected_count').text(unselectedCount);
                                $('#rejected_count').text(returnedCount);

                                renderSelectedTable(selectedParas);
                                renderUnselectedTable(unselectedParas);
                                renderReturnedTable(returnedParas);

                                if (selectedCount === 0) {
                                    $('#selected_details,#return_button_set').hide();
                                    $('#no_para_selected').show();
                                } else {
                                    $('#selected_details,#return_button_set').show();
                                    $('#no_para_selected').hide();
                                }
                                $('#spara_details_wrappers').hide();
                            } else {

                                $('#tableshow').show();

                                $('#spara_details_wrapper').show();
                                $('#no_data,para_selection_div').hide(); // Hide custom "No Data" message
                                dataFromServer = json.data;
                                $('#button_set').show();
                                renderTable(language);
                            }

                        } else {


                            $('#tableshow').show();
                            $('#button_set').show();
                            $('#spara_details_wrapper').show();
                            $('#no_data,para_selection_div').hide(); // Hide custom "No Data" message
                            dataFromServer = json.data;

                            renderTable(language);
                        }

                        //
                    } else {
                        $('#tableshow,#button_set,#para_selection_div').hide();
                        $('#spara_details_wrappers').hide();
                        $('#no_data').show();
                    }

                },
                complete: function() {
                    $('#loader').addClass('d-none');
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
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    }
                }

            });
        }

        function fetch_apms_hlcdetails(language) {

            const sessionData = {
                deptcode: sessiondeptcode,
                distcode: sessiondistcode,
                regioncode: sessionregioncode,
                catcode: sessioncatcode,
                subcatid: sessionsubcatid
            };

            var formData = $('#init_dhlc').serializeArray();

            ['deptcode', 'distcode', 'regioncode', 'catcode', 'subcatid'].forEach(function(id) {

                let value = '';

                // 1️⃣ Prefer session value if available
                if (sessionData[id] !== undefined && sessionData[id] !== '') {
                    value = sessionData[id];
                }
                // 2️⃣ Else take value from field (even if disabled)
                else {
                    const $el = $('#' + id);
                    if ($el.length) {
                        value = $el.val();
                    }
                }

                if (value !== '' && value !== null) {
                    formData.push({
                        name: id,
                        value: value
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
                        change_button_as_update('init_dhlc', 'action', 'buttonaction', 'display_error', '', '');

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
                        if (data['apms_hlcid'] != 'NULL') {
                            if (hasValue(data['mom_date'])) {
                                datepicker('mom_date', convertDateFormatYmd_ddmmyy(data['mom_date']));
                            }


                            $('#apms_hlcid').val(data['encrypted_hlcid']);

                            if (data['processcode'] == 'R')

                            {
                                $('#mom_date').prop('disabled', true);
                            }

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

                                    var file_act = data['processcode'] == 'R' ? 'view' : 'edit';

                                    view_files(files, file_act);
                                }
                            }

                        }

                        //fetch para details
                        var lang = getLanguage('')
                        fetch_dhlcparadetails(lang, data)

                    } else {
                        reset_form()
                        change_button_as_insert('init_dhlc', 'action', 'buttonaction', 'display_error', '');

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

                            getCategoriesBasedOnDept(sessiondeptcode, null)
                            // getDistrictBasedOnRegion(sessiondeptcode, '', dist);

                            // getCategoriesBasedOnDept(sessiondeptcode);

                            // getcategoryBasedOndistrict(
                            //     ,
                            //     sessionregioncode,
                            //     sessiondistcode,
                            //     ''
                            // );
                        }
                        $('#tableshow,#button_set').hide();
                        $('#spara_details_wrappers').hide();
                        $('#no_data').show();
                    }

                    //fetch para details
                    var lang = getLanguage('')



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


        //--------------------------on change-------------------------------------//


        function getCategoriesBasedOnDept(deptcode, catcode) {
            const catcodeDropdown = $('#catcode');
            const subcategoryDropdown = $('#subcatid');
            const regionDropdown = $('#regioncode')

            const lang = getLanguage();

            $('#catcode').empty();
            $('#regioncode').empty();



            catcodeDropdown.html(`
               <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                   ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
               </option>
               `);

            regionDropdown.html(`
               <option value="" data-name-en="---Select Region---" data-name-ta="---பிராந்தியத்தைத் தேர்ந்தெடுக்கவும்---">
                   ${lang === 'ta' ? '---பிராந்தியத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Region---'}
               </option>
               `);

            subcategoryDropdown.html(`
                <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
                </option>
            `);


            if (!deptcode) {
                deptcode = $("#deptcode").val();
            }

            if (!deptcode) {


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

            if (deptcode) {
                $.ajax({
                    url: "/getcategoriesbasednndeptforfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {



                        if (response.success && response.regions.length > 0) {
                            response.regions.forEach(region => {
                                regionDropdown.append(
                                    `<option value="${region.regioncode}"
                                    data-name-en="${region.regionename}"
                                    data-name-ta="${region.regiontname}">${region.regionename}</option>`
                                );
                            });
                        } else {
                            regionDropdown.append('<option disabled>No Region Available</option>');
                        }





                    },
                    error: function() {
                        alert('Error fetching categories. Please try again.');
                    }
                });
            }
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
                                        ${isSame ? 'selected' : ''}>
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


        function onchange_category(catcode, selectedsubCatcode = null, subcategory, deptcode, region, district,
            selectedinst) {

            var cat_code = catcode || $('#catcode').val();

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


          
            var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
            var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
            var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();
            if (cat_code == "") {
                var category = sessioncatcode || $("#catcode").val();
            }
            if (subcategory == 'N') {

                onchange_subcategory(deptcode, region, district, cat_code, '', selectedinst)

            }
            if (subcategory == 'Y') {
                $.ajax({
                    url: '/getsubcategoriesbasedondeptforfollowup', // Your API route to get user details
                    method: 'POST',
                    data: {
                          category: cat_code||category
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

        // function onchange_subcategory(
        //     deptcode,
        //     region,
        //     district,
        //     category,
        //     subcategory,
        //     selectedInstitutionCodes
        // ) {

        //     var institutionDropdown = $('#instid');
        //     var lang = getLanguage();

        //     institutionDropdown.empty();

        //     deptcode = deptcode || $('#deptcode').val();
        //     region = region || $('#regioncode').val();
        //     district = district || $('#distcode').val();
        //     category = category || $('#catcode').val();
        //     subcategory = subcategory || $('#subcatid').val();

        //     /* ---------- NORMALIZE ---------- */

        //     var selectAll = false;

        //     if (typeof selectedInstitutionCodes === 'string') {
        //         try {
        //             selectedInstitutionCodes = JSON.parse(selectedInstitutionCodes);
        //         } catch (e) {
        //             selectedInstitutionCodes = [selectedInstitutionCodes];
        //         }
        //     }

        //     if (!Array.isArray(selectedInstitutionCodes)) {
        //         selectedInstitutionCodes = [];
        //     }

        //     if (selectedInstitutionCodes.map(String).includes('A')) {
        //         selectAll = true;
        //         selectedInstitutionCodes = ['A'];
        //     }

        //     /* ---------- AJAX ---------- */

        //     if (deptcode && region && district && category) {

        //         $.ajax({
        //             url: "/apms/apms_getinstbaseonsubcatfollowup",
        //             type: "POST",
        //             data: {
        //                 deptcode: deptcode,
        //                 region: region,
        //                 district: district,
        //                 catcode: category,
        //                 subcatcode: subcategory,
        //                 _token: '{{ csrf_token() }}'
        //             },
        //             success: function(response) {

        //                 if (response.success && response.data.length > 0) {

        //                     /* ---------- ADD ALL OPTION ---------- */
        //                     institutionDropdown.append(
        //                         new Option(
        //                             lang === 'ta' ? 'அனைத்தும்' : 'All Institutions',
        //                             'A',
        //                             selectAll,
        //                             selectAll
        //                         )
        //                     );

        //                     response.data.forEach(function(inst) {

        //                         var isSelected = !selectAll &&
        //                             selectedInstitutionCodes
        //                             .map(String)
        //                             .includes(String(inst.instid));

        //                         var option = new Option(
        //                             inst.instename,
        //                             inst.instid,
        //                             isSelected,
        //                             isSelected
        //                         );

        //                         $(option)
        //                             .attr('data-name-en', inst.instename)
        //                             .attr('data-name-ta', inst.insttname);

        //                         institutionDropdown.append(option);
        //                     });
        //                 }

        //                 institutionDropdown.trigger('change');
        //             }
        //         });
        //     }
        // }
        function onchange_subcategory(
            deptcode,
            region,
            district,
            category,
            subcategory,
            selectedInstitutionCodes = []
        ) {


            const institutionDropdown = $('#instid');
            const lang = getLanguage();

            institutionDropdown.empty(); // important for Select2

            deptcode = deptcode || $('#deptcode').val();
            region = region || $('#regioncode').val();
            district = district || $('#distcode').val();
            category = category || $('#catcode').val();
            subcategory = subcategory || $('#subcatid').val();

            if (typeof selectedInstitutionCodes === 'string') {
                try {
                    selectedInstitutionCodes = JSON.parse(selectedInstitutionCodes);
                } catch (e) {
                    selectedInstitutionCodes = [];
                }
            }

            if (!Array.isArray(selectedInstitutionCodes)) {
                selectedInstitutionCodes = [];
            }


            if (deptcode && region && district && category) {

                $.ajax({
                    url: "/apms/apms_getinstbaseonsubcatfollowup",
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
                            if (hasValue(selectedInstitutionCodes)) {

                                const selectedCodes = selectedInstitutionCodes.map(String);
                                const hasAll = selectedCodes.includes('A');

                                /* ---------- ALL option (always present) ---------- */
                                const allOption = new Option(
                                    'All Institutions',
                                    'A',
                                    hasAll, // defaultSelected
                                    hasAll // selected
                                );

                                allOption.disabled = !hasAll; // disable ALL if not selected
                                institutionDropdown.append(allOption);

                                /* ---------- Institution options ---------- */
                                response.data.forEach(inst => {

                                    const isSelected = selectedCodes.includes(String(inst.instid));

                                    const option = new Option(
                                        inst.instename,
                                        inst.instid,
                                        isSelected,
                                        isSelected
                                    );

                                    // 🔒 If ALL is selected → disable others
                                    option.disabled = hasAll;

                                    $(option)
                                        .attr('data-name-en', inst.instename)
                                        .attr('data-name-ta', inst.insttname);

                                    institutionDropdown.append(option);
                                });
                            } else {

                                institutionDropdown.empty();

                                // Add ALL option (selected)
                                const allOption = new Option(
                                    'All Institutions',
                                    'A',
                                    true, // defaultSelected
                                    true // selected
                                );

                                $(allOption).attr('data-all', 'Y');
                                institutionDropdown.append(allOption);

                                // Add remaining institutions as DISABLED
                                response.data.forEach(inst => {

                                    const option = new Option(
                                        inst.instename,
                                        inst.instid,
                                        false,
                                        false
                                    );

                                    $(option)
                                        .attr('data-name-en', inst.instename)
                                        .attr('data-name-ta', inst.insttname)
                                        .prop('disabled', true);

                                    institutionDropdown.append(option);
                                });
                            }



                        } else {
                            institutionDropdown.append(
                                `<option disabled>
                            ${lang === 'ta' ? '' : 'No Institution Available'}
                        </option>`
                            );
                        }

                        // 🔥 IMPORTANT for Select2
                        institutionDropdown.trigger('change');
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


        function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            // alert('te');
            const districtDropdown = $('#distcode');
            const institutionDropdown = $('#instid');

            districtDropdown.html('<option value="">Select District</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#regioncode").val();
                // alert(deptcode);
            }

            if (!region) {
                districtDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No District Available"
                            data-name-ta="???????? ?????????????">
                            ${lang === 'ta' ? '???????? ?????????????' : 'No District Available'}
                    </option>
                `);


            }
            // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');

            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregionfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.distcode}"
                                     data-name-en="${district.distename}"
                                   data-name-ta="${district.disttname}" ${
                                    district.distcode === selecteDistrictcode ? 'selected' : ''
                            }>${district.distename}</option>`
                                );
                            });
                        } else {
                            districtDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }

        //---------------------------on change------------------------------------//


        //---------------------------on load------------------------------------//
        window.addEventListener('load', function() {

            const loader = document.getElementById('loader');
            if (loader) {
                $('#loader').addClass('d-none');
            }

            var lang = getLanguage('')
            reset_form();
            fetch_apms_hlcdetails();

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
            var today = new Date();

            // Min date: 2 months before today
            var minDate = new Date(today);
            minDate.setMonth(today.getMonth() - 2);

            // Max date: today (no future dates)
            var maxDate = new Date(today);

            // Format the dates to dd/mm/yyyy
            var minDateString = formatDate(minDate);
            var maxDateString = formatDate(maxDate);

            init_datepicker(value, minDateString, maxDateString, setdate);
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






        function submitParaActions(data, statusflag, para_action) {
            var formData = new FormData($('#init_dhlc')[0]);


            ['deptcode', 'distcode', 'regioncode', 'catcode', 'subcatid', 'mom_date', 'instid'].forEach(function(id) {
                var el = $('#' + id);
                if (el.prop('disabled')) {
                    if (id === 'instid') {
                        // multiple select
                        let values = el.val() || [];
                        values.forEach(function(v) {
                            formData.append('instid[]', v);
                        });

                    } else {

                        formData.append(id, el.val());

                    }
                }

            });


            //Data-Table data append
            formData.append('rows', JSON.stringify(data));
            formData.append('statusflag', statusflag);
            formData.append('para_action', para_action);

            $('#loader').show()
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
                            if (statusflag == 'F') {
                                window.location.href = "{{ route('init_paramanegement_hlc') }}";
                            } else {
                                var lang = getLanguage('')

                                fetch_apms_hlcdetails();
                            }

                            //fetch_dhlcparadetails(lang)

                        });




                    } else {
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    }

                },
                complete: function() {
                    $('#loader').addClass('d-none');
                },
                error: function() {
                    alert('Something went wrong');
                }
            });
        }




        function view_files(file, action) {
            const fileListContainer = $('#view_file-list-container');
            $('#file').hide();
            fileListContainer.empty(); // Clear previous file cards

            $('#file').val('');
            // Set the fileuploadid directly since it's a single file
            $('#uploadid').val(file.fileuploadid);

            const delte_file_btn = ` <div class="p-1 align-items-center mt-2 "  onclick="remove_file()">
                            <h5 class="text-danger box mb-0 round-40 p-1">
                                <i class="ti ti-trash"></i>
                            </h5>
                        </div>`

            const fileCard = `

                <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
                    <div class="d-flex flex-row">

                        <div class="p-3 mb-1">
                            <h3 class="text-dark mb-0 fs-2">
                                <a style="color:black;" href="/storage/${file.path}" target="_blank">${file.name}</a>
                            </h3>
                        </div>
                        ${action =='edit'?delte_file_btn:''}
                    </div>
                </div>
            `;

            fileListContainer.append(fileCard); // Add the file card to the container
        }

        function remove_file() {
            $('#view_file-list-container').hide();
            $('#file').val('').show();
            // $('#uploadid').val('');
        }

        function get_para_historydetails(id, param) {
            const followupid = id.dataset.followupid;
            const paraid = id.dataset.para;
            const lang = getLanguage()

            $.ajax({
                url: "/apms/fetch_para_datas",
                type: "POST",
                data: {
                    followupid: followupid,
                    paraid: paraid,
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
                            if (!para_det || para_det.length === 0) {

                                const noDataText = lang == 'ta' ? 'தரவு இல்லை' : 'No Data Available';

                                $('#instTableBody').html(`
                             <tr>
                                 <td colspan="4" class="text-center text-muted py-4">
                                     <i class="ti ti-database-off fs-4 d-block mb-1"></i>
                                     ${noDataText}
                                 </td>
                             </tr>
                         `);

                            } else {
                                populatTable(para_det);
                            }
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

                status += ((item.rejoinderstatus == 'Y' && (item.rejoinderstatus == 'A' || item.rejoinderstatus ==
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

        function populate_parafulldetails(para_data) {

            const modelbody = $('#parafull_det');
            modelbody.empty();

            // 🔹 Language (sync)
            const lang = getLanguage('');

            // 🔹 Language JSON (must be loaded globally)
            const labels = window.layout || {};
            let attachmentHTML = "";
            if (para_data.auditorfileupload && para_data.auditorfileupload != '') {
                files = getfile(para_data.auditorfileupload)
                attachmentHTML = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label"><strong>Attachments:</strong></label>
                        <div class="d-flex flex-row">
                            <div class="position-relative d-flex flex-row" id="filediv">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }
            fields.forEach(field => {

                // 🔹 Get translated label
                const label = labels[lang]?.[field.labelKey] || field.labelKey;

                let value = '';

                // 🔹 Language-based value
                if (field.en && field.ta) {
                    value = (lang === 'ta') ?
                        para_data[field.ta] :
                        para_data[field.en];
                } else {
                    value = para_data[field.key];
                }

                // 🔹 Amount handling
                if (field.type === 'amount') {
                    value = value ?
                        `<strong>₹ ${Number(value).toLocaleString('en-IN')}</strong>` :
                        '-';
                }

                // 🔹 Omit empty rows (except amount)
                if ((value === null || value === undefined || value === '') && field.type !== 'amount') {
                    return;
                }

                modelbody.append(`
            <tr>
                <td style="font-weight:600; width:35%;">${label}</td>
                <td>${value}</td>
            </tr>
        `);
            });

            $('#parafull_details').modal('show');
            if (para_data.auditorfileupload) {
                UploadedFileList(files, '', `filediv`, '', 'fileuploadid');
            }
        }

        function reset_form() {

            $('#init_dhlc')[0].reset();
            $('#init_dhlc').validate().resetForm();
            $('#mom_date').val('');
            $('#instid').val(null).select2();
            remove_file()



        }


        //------------------------------------------Helper Functions-----------------------------------//
        //to null check
        function hasValue(val) {
            return val !== null && val !== undefined && val.toString().trim() !== '';
        }


        //To onchange
        function handleCategoryFlow(dept, region, dist, cat, subcat, instId) {
            // getDistrictBasedOnRegion(dept, region, dist);
            getcategoryBasedOndistrict(dept, region, dist, cat);

            var ifsubcategory = hasValue(subcat) ? 'Y' : 'N';

            onchange_category(cat, subcat, ifsubcategory, dept, region, dist, instId);

            if (ifsubcategory === 'Y') {
                onchange_subcategory(dept, region, dist, cat, subcat, instId);
            } else {

                //onchange_subcategory(dept, region, dist, cat, subcat, instId);

            }
        }

        function scrollToFirstError() {
            var $hlc_form = $('#init_dhlc');
            const firstError = $hlc_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }

        // //------------------------------------------Helper Functions-----------------------------------//
        // function applyValidationToNewFields(inputName, message) {
        //     let $input = $("[name='" + inputName + "']");

        //     if (!$input.length) {
        //         console.error("❌ Element not found:", inputName);
        //         return;
        //     }

        //     let $form = $("#init_dhlc");
        //     let validator = $form.data("validator");

        //     // Initialize validator once
        //     if (!validator) {
        //         $form.validate({
        //             ignore: [],
        //             errorPlacement: function(error, element) {
        //                 if (element.hasClass('datepicker')) {
        //                     error.insertAfter(element.closest('.input-group'));
        //                 } else {
        //                     error.insertAfter(element);
        //                 }
        //             }
        //         });
        //         validator = $form.data("validator");
        //     }

        //     // Remove existing rules safely
        //     $input.rules("remove");

        //     // Add required rule
        //     $input.rules("add", {
        //         required: true,
        //         messages: {
        //             required: message
        //         }
        //     });

        //     // Show error immediately
        //     validator.element($input);

        //     // 🔥 AUTO REMOVE ERROR WHEN VALUE IS ENTERED
        //     $input
        //         .off("keyup.validation change.validation")
        //         .on("keyup.validation change.validation", function() {
        //             let val = $(this).val();

        //             if (val && val.toString().trim() !== "") {
        //                 // Mark field as valid and remove error message
        //                 validator.successList.push(this);
        //                 validator.showErrors();
        //             }
        //         });

        // }

        // function add_selected_para() {
        //     alert('asd')
        // }

        function add_selected_para(event, tableType) {

            event.preventDefault();


            let tableId = '';

            if (tableType === 'unselected') {
                tableId = '#unselectedpara_details';
            } else if (tableType === 'returned') {
                tableId = '#returnedpara_details';
            }

            let table = $(tableId).DataTable();

            table.$('input[type="checkbox"]:checked').each(function() {

                let followupid = $(this).data('followupid');

                let rowData = table.row($(this).closest('tr')).data();

                if (rowData) {
                    checkedParas.push(rowData);
                }


            });


            if (checkedParas.length === 0) {
                alert("Please select at least one para");
                return;
            }

            // Move to selected
            selectedParas.push(...checkedParas);



            // Remove from returned


            if (tableType === 'unselected') {
                unselectedParas = unselectedParas.filter(
                    r => !checkedParas.some(c => c.followupid === r.followupid)
                );
                renderUnselectedTable(unselectedParas, 'en');
            } else if (tableType === 'returned') {
                returnedParas = returnedParas.filter(
                    r => !checkedParas.some(c => c.followupid === r.followupid)
                );
                renderReturnedTable(returnedParas, 'en');
            }

            updateSelectedParasHidden();

            renderSelectedTable(selectedParas, 'en');



            updateCounts();
            checkedParas = [];
        }
        //to retain selected paras on adding new paras from another tab
        function updateSelectedParasHidden() {

            let ids = [...new Set(selectedParas.map(p => p.followupid))];

            $('#selected_paras').val(JSON.stringify(ids));
        }

        function updateCounts() {

            $('#selected_count').text(selectedParas.length);
            $('#unselected_count').text(unselectedParas.length);
            $('#rejected_count').text(returnedParas.length);


        }



        $('#return_buttonaction, #return_finalisebtn').on('click', function(e) {
            e.preventDefault();


            if ($("#init_dhlc").valid()) {






                if (!validateAllRows('#selected_details')) {
                    return false;
                }
                const buttonaction = this.id;
                let actionType = buttonaction === 'return_finalisebtn' ? 'finalise' : 'draft';
                let para_action = buttonaction === 'return_finalisebtn' ? 'finalise' : 'draft';

                let table = $('#para_details').DataTable();

                let statusflag = this.id === 'return_finalisebtn' ? 'F' : 'Y';

                let rowData = collectRowData(
                    '#selected_details',
                    '.selected_para_checkbox',
                    statusflag,
                    actionType
                );








                //validate file type
                var form = document.getElementById('init_dhlc');
                var formData = new FormData(form);
                var file = formData.get('file');
                var fileid = $('#uploadid').val();
                var finalise_msg =
                    'Status will be updated against the selected paras. <br> Once finalized ,status cannot be reverted back.<br>Do you want to proceed ?'

                submit_message = buttonaction === 'finalisebtn' ?
                    finalise_msg : 'Are you sure to submit the actions against para?';
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

        // $('#paraTabs button[data-bs-target="#selected"]').tab('show');

        function initCheckboxBlock(checkboxClass) {

            $(document).on('click', checkboxClass, function(e) {

                if ($(this).data('blocked') === 'Y') {
                    e.preventDefault();
                    e.stopPropagation();

                    alert('Please read all the details before selecting this option.');
                    return false;
                }

            });

        }

        function initRowEnable(tableId) {

            $(tableId).on('click', '.enable-row', function() {

                const row = $(this).closest('tr');
                const action = $(this).data('action');

                let clicked = row.data('clickedActions') || {
                    para: false,
                    flow: false,
                    view: false
                };

                clicked[action] = true;
                row.data('clickedActions', clicked);

                if (clicked.para) {
                    row.find('input[type="checkbox"]').prop('disabled', false);
                }

            });

        }

        function initCheckboxChange(tableId) {

            $(tableId).on('change', 'input[type="checkbox"]', function() {

                const row = $(this).closest('tr');
                const isChecked = $(this).is(':checked');

                row.find('.action-select').prop('disabled', !isChecked);

            });

        }

        function initDrawEvent(tableId) {

            $(tableId).on('draw.dt', function() {

                $(tableId + ' tbody tr').each(function() {

                    const $row = $(this);
                    const isChecked = $row.find('input[type="checkbox"]').is(':checked');

                    $row.find('.action-select').prop('disabled', !isChecked);

                });

            });

        }

        function validateAllRows(tableId) {

            let isValid = true;
            let table = $(tableId).DataTable();
            let checkedCount = 0;

            table.rows().every(function() {

                let rowNode = $(this.node());
                let isChecked = rowNode.find('input[type="checkbox"]').is(':checked');

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
        $(document).on('change', '.action-select', function() {
            $(this).removeClass('is-invalid');
        });

        function collectRowData(tableId, checkboxClass, statusflag, actionType) {

            let table = $(tableId).DataTable();
            let rowData = [];

            table.rows().every(function() {

                let rowNode = $(this.node());
                let checkbox = rowNode.find(checkboxClass);
                let isChecked = checkbox.is(':checked');

                if (isChecked) {

                    let followupAction = {
                        followupid: checkbox.data('followupid'),
                        actioncode: rowNode.find('.action-select').val()
                    };

                    rowData.push({
                        paraid: this.id(),
                        instid: checkbox.data('instid'),
                        followup_action: followupAction,
                        actioncode: rowNode.find('.action-select').val(),
                        action_type: actionType,
                        statusflag: statusflag,
                        selected_paras: checkbox.data('followupid'),
                    });

                }

            });

            return rowData;
        }

        $(document).on('change', '.selected_para_checkbox', function() {

            let followupid = $(this).data('followupid');

            // get existing JSON array
            let selected = $('#selected_paras').val();
            let ids = selected ? JSON.parse(selected) : [];

            if ($(this).is(':checked')) {

                // add id if not exists
                if (!ids.includes(followupid)) {
                    ids.push(followupid);
                }

            } else {

                // remove id if unchecked
                ids = ids.filter(id => id != followupid);

            }

            // store back as JSON
            $('#selected_paras').val(JSON.stringify(ids));

        });


        initCheckboxBlock('.custom-checkbox');
        initCheckboxBlock('.selected_para_checkbox');

        initRowEnable('#para_details');
        initRowEnable('#selected_details');

        initCheckboxChange('#para_details');
        initCheckboxChange('#selected_details');

        initDrawEvent('#para_details');
        initDrawEvent('#selected_details');
    </script>


@endsection
