@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Report Preview')

<link rel="stylesheet" href="../assets/libs/dragula/dist/dragula.min.css">

@php

    $sessionchargedel = session('charge');
    $sessionuser = session('user');
    $sessioroleactioncode = $sessionchargedel->roleactioncode;
    // print_r($sessionchargedel);
    // print_r($sessionchargedel->roletypecode);

    $sessionuserid = $sessionuser->userid;
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';
    // $auditteamhead = $sessionchargedel->auditteamhead;
@endphp



<style>
    .wrap-text-lwf {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 600px;
        /* Adjust as needed */
    }

    .ck-powered-by-balloon {
        display: none !important;
    }

    .equal-height {
        display: flex;
        flex-wrap: wrap;
    }

    .equal-height .card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .dd-list .dd-item {
        padding: 1px;
    }

    #pdf-preview iframe {
        width: 100%;
        height: 100%;
        max-height: 590px;
        /* Adjust the percentage to set the maximum height */
        background-color: white;

    }

    /* CSS to increase the modal height */
    #previewmodel_content {
        max-height: 800px;
        /* Adjust the percentage to set the maximum height */
        height: 800px;
        /* Allows the height to adjust based on the content */
    }

    .draggable-item {
        cursor: move;
    }

    .PartBFinalized .draggable-item {
        cursor: default;
    }


    .step-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px;
    }

    .step-btn {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s;
    }

    .step-btn.active {
        background-color: #007bff;
        color: white;
    }

    .step-header {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
    }

    .sub-step-header {
        font-size: 20px;
        font-weight: normal;
        text-align: center;
        color: #2a3547 !important;
    }



    .iframe-container {
        display: flex;
        /* Enables flexbox */
        justify-content: center;
        /* Centers horizontally */
        height: 100%;
        /* Ensure parent container takes full height */
        width: 100%;
    }

    iframe {
        width: 99%;
        height: 640px;
        max-height: 640px !important;
        border: none;
        /* Optional: remove border */
    }

    #partc_iframe {
        width: 80%;
    }



    .dd-handle {
        background-color: #ffffff !important;
    }

    .activatestep {
        background-color: #5d87ff !important;
        /*font-weight:bold !important;*/
    }

    .activatestep span {
        color: #ffffff !important;
        /*font-weight:bold !important;*/
    }

    .activatestep span b {
        color: #ffffff !important;
        /*font-weight:bold !important;*/
    }


    .part_b_dragula,
    #scrollablecontent {
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 650px;
        height: 680px;
        padding-right: 10px;
    }

    /* width */
    #scrollablecontent::-webkit-scrollbar {
        width: 4px;

    }

    /* Track */
    #scrollablecontent::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    #scrollablecontent::-webkit-scrollbar-thumb {
        background: #bbbcbc;
    }

    /* Handle on hover */
    #scrollablecontent::-webkit-scrollbar-thumb:hover {
        background: #888;
    }


    /* width */
    .part_b_dragula::-webkit-scrollbar {
        width: 4px;

    }

    /* Track */
    .part_b_dragula::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    .part_b_dragula::-webkit-scrollbar-thumb {
        background: #bbbcbc;
    }

    /* Handle on hover */
    .part_b_dragula::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    textarea {
        width: 100%;
        /* Adjust as needed */
        min-height: 200px;
        height: 100px;
    }

    .ck-editor__editable_inline {
        min-height: 300px !important;
        width: 100% !important;
    }

    #lwf_ckeditor {
        width: 100%;
        /* Adjust as needed */
        min-height: 60px;
        height: 60px;
    }



    .file-preview {
        margin-right: 15px;
        /* Adjust spacing as needed */
    }

    body {
        color: #222 !important;
    }

    .disabled-step {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #e7e7e7;
    }

    #pdf-preview {
        height: 550px;
        max-height: 550px !important;

    }



    #pdf-preview.full-height {
        height: 650px !important;
        max-height: 650px !important;
    }

    .enble-chkbox {
        border-color: rgb(62, 62, 63);
        cursor: pointer;
    }
</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

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
    <div class="card card_border">
        <div class="card-header card_header_color">Institution </div>
        <div class="card-body">
            <form id="reportpreview_form" name="reportpreview_form">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" <?php echo $make_dept_disable; ?> id="deptcode"
                            name="deptcode" onchange="onchange_region('region','regioncode')">
                            <option value="" data-name-en="---Select Department---"
                                data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select
                                Department</option>

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
                                    data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                    Available
                                </option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode" onchange="onchange_region('district','distcode')">
                            <option value="" data-name-en=" ---Select Region---"
                                data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select
                                Region</option>

                            @if (!empty($region) && count($region) > 0)
                                @foreach ($region as $reg)
                                    <option value="{{ $reg->regioncode }}"
                                        @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                        data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
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
                    <div class="col-md-4">
                        <label class="form-label required lang" for="distcode" key="District">District</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode">
                            <option value="" data-name-en="---Select District---"
                                data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select
                                District</option>

                            @if (!empty($district) && count($district) > 0)
                                @foreach ($district as $dist)
                                    <option value="{{ $dist->distcode }}"
                                        @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                        data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
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
                    <div class="col-md-4 mt-2">
                        <label class="form-label required lang" for="regioncode" key="">Audit Quarter</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" id="planmappingid"
                            name="planmappingid">
                            <option value="" data-name-en=" ---Select Quarter---"
                                data-name-ta="---காலாண்டைத் தேர்ந்தெடுக்கவும்---">Select
                                Quarter</option>
                            <option disabled data-name-en="No details Available" data-name-ta="விவரங்கள் எதுவும் இல்லை">
                                No details Available
                            </option>

                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3" onclick="get_data()" action="insert" id="buttonaction"
                            name="buttonaction">Submit </button>
                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="card mt-2 " style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="">List of Institutes Completed Audit Process
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
            <div id='no_data' class=''>
                <center>No Data Available</center>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="wordPreviewModal" tabindex="-1" aria-labelledby="wordPreviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div id="previewmodel_content" class="modal-content">
            <div class="modal-header">
                <h3 style="text-align:center !important;" class="lang" key="previewscreen">PREVIEW SCREEN</h3>

                <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <!-- The iframe content will appear here -->
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <div class="preview_finalizebtnset">
                    <div class="d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input enble-chkbox" type="checkbox" value="1"
                                id="finalizeReport" name="finalizeReport">
                            <label id="checkboxlabel_content" class="form-check-label" for="finalizeReport">
                                send intimation to the institution and finalize the report?
                            </label>
                        </div>
                    </div>


                    <!-- Button container with flexbox for centering -->
                    <div class="text-center mt-3" style="margin-t">
                        <button id="finalizereport_pdf" class="btn btn-success">
                            Finalize Report</span>
                        </button>
                    </div>
                </div>

                <br><br>

            </div>

        </div>
    </div>
</div>




<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="../assets/libs/dragula/dist/dragula.min.js"></script>
<script src="../assets/libs/nestable/jquery.nestable.js"></script>
<script src="../assets/js/plugins/nestable-init.js"></script>


<script>
    let dataFromServer;

    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            $('#loader').addClass('d-none');

        }
    });

    //Load plan details
    function loadPlanMapping() {

        var deptcode = $('#deptcode').val() || "<?php echo $deptcode; ?>";
        var regioncode = $('#regioncode').val() || "<?php echo $regioncode; ?>";

        if (!deptcode) return;

        $.ajax({
            url: '/reportpreview/fetch_deptbaseddata',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                valuefor: 'plan'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (!response.success) return;

                const planOptions = (response.planmapData || []).map(item => {
                    return `<option value="${item.planmappingid}">
                    ${item.planname} (${item.financialyear})
                </option>`;
                }).join('');

                $('#planmappingid').html(
                    `<option value="">Select Quarter</option>` +
                    (planOptions || '<option>No plan available</option>')
                );
            }
        });
    }


    //region/district fetch
    function onchange_region(valuefor, valueforid) {

        var session_regioncode = "<?php echo $regioncode; ?>";
        var session_distcode = "<?php echo $distcode; ?>";
	var dgarole = '<?php echo $dga_roletypecode; ?>';
        var adminrole = '<?php echo $Admin_roletypecode; ?>';
        var sessionrole = '<?php echo $sessionroletypecode; ?>';

        if (sessionrole == dgarole || sessionrole == adminrole) {
            loadPlanMapping();
        }
        var deptcode = $('#deptcode').val() || "<?php echo $deptcode; ?>";
        var regioncode = $('#regioncode').val() || session_regioncode;

        if (!deptcode) return;

        $.ajax({
            url: '/reportpreview/fetch_deptbaseddata',
            type: 'POST',
            data: {
                deptcode,
                regioncode,
                valuefor
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (!response.success) return;

                //REgion
                if (valuefor === 'region') {

                    const regionOptions = (response.data || []).map(item =>
                        `<option value="${item.regioncode}"
                        ${item.regioncode == session_regioncode ? "selected" : ""}>
                        ${item.regionename}
                    </option>`
                    ).join('');

                    $('#regioncode').html(
                        `<option value="">Select Region</option>` + regionOptions
                    );
                }

                //District
                else if (valuefor === 'district') {

                    const districtOptions = (response.data || []).map(item =>
                        `<option value="${item.distcode}"
                        ${item.distcode == session_distcode ? "selected" : ""}>
                        ${item.distename}
                    </option>`
                    ).join('');

                    $('#distcode').html(
                        `<option value="">Select District</option>` + districtOptions
                    );
                }
            }
        });
    }

    //table data fetch
    function getInstData(lang) {

        var deptcode = $('#deptcode').val() || "<?php echo $deptcode; ?>";
        var regioncode = $('#regioncode').val() || "<?php echo $regioncode; ?>";
        var distcode = $('#distcode').val() || "<?php echo $distcode; ?>";
        var planmappingid = $('#planmappingid').val() || '';

        $.ajax({
            url: '/reportpreview/fetch_listinstitutes',
            type: 'POST',
            data: {
                deptcode,
                regioncode,
                distcode,
                planmappingid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function(response) {

                if (response.data && response.data.length > 0) {

                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();

                    dataFromServer = response.data;
                    renderTable(lang);

                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },

            error: function(xhr) {

                $('#tableshow').hide();
                $('#usertable_wrapper').hide();
                $('#no_data').show();

                let errorMessage = 'An error occurred';

                try {
                    const res = JSON.parse(xhr.responseText);
                    errorMessage = res.message || errorMessage;
                } catch (e) {}

                passing_alert_value(
                    'Alert',
                    errorMessage,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        });
    }





    function onchange_deptcode() {
        onchange_region('region', 'regioncode');
        loadPlanMapping();
    }

    function onchange_distcode() {
        getInstData(getLanguage());
    }



    $(document).ready(function() {

        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>';
        var distrole = '<?php echo $Dist_roletypecode; ?>';
        var regionrole = '<?php echo $Re_roletypecode; ?>';
        var headofficerole = '<?php echo $Ho_roletypecode; ?>';
        var dgarole = '<?php echo $dga_roletypecode; ?>';
        var adminrole = '<?php echo $Admin_roletypecode; ?>';


        if (sessionrole == distrole) {
            loadPlanMapping();
        } else if (sessionrole == regionrole) {
            onchange_region('district', 'distcode');
            loadPlanMapping();
        } else if (sessionrole == headofficerole) {
            onchange_region('region', 'regioncode');
            loadPlanMapping();
        } else if (sessionrole == dgarole || sessionrole == adminrole) {
            getInstData(lang);
        }
    });


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#institution_table')) {
            $('#institution_table').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
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
                        return `<button class="btn btn-sm btn-primary preview-btn"
                                    data-instid="${row.instid}"
                                    data-finyear="${row.financialyearcode}"
                                    data-scheduleid="${row.auditscheduleid}"
                                    data-spilloverflag="${row.spilloverflag}" >
                                     <i class="ti ti-eye fs-4 me-2"></i> View Audit Report
                                </button>`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column'
                }
            ]


        });
        updatedatatable(language, "audit_plandetails");
    }

    jsonLoadedPromise.then(() => {
        const language = getLanguage('Y');



        // Apply validation to the form
        const validator = $("#reportpreview_form").validate({
            rules: {
                deptcode: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                planmappingid: {
                    required: true
                },

            },

            messages: {
                deptcode: {
                    required: "Select Department"
                },
                regioncode: {
                    required: "Select Region"
                },
                distcode: {
                    required: "Select District"
                },
                planmappingid: {
                    required: "Select Quarter"
                },
            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    function scrollToFirstError() {
        var $form = $('#reportpreview_form');
        const firstError = $form.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    function get_data() {
        event.preventDefault(); // Prevent form submission

        var $form = $('#reportpreview_form');
        if (!$form.valid()) {
            scrollToFirstError();
            return;
        }

        var deptcode = $('#deptcode').val();
        var regioncode = $('#regioncode').val();
        var distcode = $('#distcode').val();



        if ($form.valid()) {



            var lang = getLanguage('')
            getInstData(lang);
        } else {

            scrollToFirstError();
        }

    }

    function reset_form() {

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#reportpreview_form')[0].reset();
        $('#planmappingid').val('').select2();
        $('#tableshow').hide();
        $('#usertable_wrapper').hide();
        $('#no_data').show();

        if (sessionrole == regionrole) {
            $('#distcode').val('').select2();
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {
            $('#distcode,#regioncode').val('').select2();
            onchange_region('region', 'regioncode')
        } else if (sessionrole == dgarole || sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').val('').select2();
        }

    }

    $(document).on('click', '.preview-btn', function() {

        var instid = $(this).data('instid');
        var financialyearcode = $(this).data('finyear');
        var scheduleId = $(this).data('scheduleid');
        var spilloverflag = $(this).data('spilloverflag');
        var whichpart = 'all'
        $('#loader').removeClass('d-none');
        var lang = getLanguage('Y');

        var myModal = new bootstrap.Modal(document.getElementById('wordPreviewModal'));



        fetch('/preview-word?scheduleid=' + scheduleId + '&lang=' + lang + '&whichpart=' + whichpart +
                '&instid=' + instid + '&financialyearcode=' + financialyearcode + '&spilloverflag=' +
                spilloverflag)
            .then(response => response.json())
            .then(data => {

                if (data.res === 'success') {

                    myModal.show();


                    $('#pdf-preview').removeClass('full-height');


                    $('#finalizereport_pdf').html('Finalize Report');


                    $('.preview_finalizebtnset').hide();
                    $('#pdf-preview').addClass('full-height');


                    // ✅ Render iframe
                    var iframe = document.createElement('iframe');
                    iframe.srcdoc = data.html;

                    $('#pdf-preview').html('').append(iframe);

                    // ✅ Store filename
                    $('#filename').val(data.filename);
                    localStorage.setItem('filename', data.filename);

                } else {
                    alert('No Report Available.');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            })
            .finally(() => {
                $('#loader').addClass('d-none');
            });
    });
</script>


@endsection
