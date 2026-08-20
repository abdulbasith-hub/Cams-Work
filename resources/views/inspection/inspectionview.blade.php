@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Inspection')


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

</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
@if (
    ($isteammember == 'team_mem' && $sessioroleactioncode == '04') ||
        ($isteammember == '' && $sessioroleactioncode == '04'))
    <div id='available' class=''>
        <center>No Data Available</center>
    </div>
@else
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Institution </div>
        <div class="card-body">
            <form id="inspect_form" name="inspect_form" method='post'>
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
                            name="distcode" onchange="onchange_distcode('','','','','','')">
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
                </div>
                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <!-- <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save Draft </button> -->
                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color lang" key="">List of Institutions For Inspection</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="institution_table"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department</th>
                                <th class="lang" key="inst">Institution</th>
                                <th class="lang" key="">Audit Team Details</th>
                                <th class="lang" key="">Entry Meeting</th>
                                <th class="lang" key="">Exit Meeting</th>
                                <th class="lang" key="">Proposed Date</th>

                                <th class="lang" key="">Audit Status</th>
                                <th class="lang" key="">Inspection Status</th>
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

@endif

<script src="../assets/js/jquery.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>


<script>
    let dataFromServer;

    function onchange_deptcode() {

        getInstData(lang);
    }


    function onchange_region(valuefor, valueforid) {


        const defaultOption = `
            <option value="" data-name-en="துறையைத் தேர்ந்தெடுக்கவும்" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'துறையைத் தேர்ந்தெடுக்கவும்' : 'Select Department'}
            </option>`;

        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        // return;
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()

        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();

        //alert(distcode)
        var lang = getLanguage();
        let placeholderTextEn = '',
            placeholderTextTa = '';

        switch (valuefor) {
            case 'region':
                placeholderTextEn = 'Select a Region';
                placeholderTextTa = 'பகுதியை தேர்வு செய்';
                break;
            case 'district':
                placeholderTextEn = 'Select a District';
                placeholderTextTa = 'மாவட்டத்தை தேர்ந்தெடுக்கவும்';
                break;
            default:
                placeholderTextEn = 'Select an Option';
                placeholderTextTa = 'ஒரு விருப்பத்தை தேர்வு செய்';

        }





        $dropdown.html(`<option value="" data-name-en="${placeholderTextEn}" data-name-ta="${placeholderTextTa}">
        ${lang === 'ta' ? placeholderTextTa : placeholderTextEn}
            </option>`);
        if (valuefor == 'region') {
            $('#distcode').html(`<option value="" data-name-en="Select a District" data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'மாவட்டத்தை தேர்ந்தெடுக்கவும்' : 'Select a District'}
            </option>`);
        }

        if (!deptcode) {
            var regioncode = '';
            getInstData(lang);

            return;
        }


        $.ajax({
            url: '/inspection/fetch_deptbaseddata',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                valuefor: valuefor,
                formname: 'checkschedulestatus'

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.success && Array.isArray(response.data)) {
                    // Map response data to options

                    //return;
                    const options = response.data.map(item => {
                        switch (valuefor) {
                            case 'region':
                                return `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}"  ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
                            case 'district':
                                return `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${item.distcode === distcode ? "selected" : ""}>${item.distename}</option>`;

                            default:
                                return '';
                        }
                    }).join('');

                    // Append options or show fallback message
                    $dropdown.append(options || '<option value="">No data available</option>');

                    if (response.details.length > 0) {
                        dataFromServer = response.details;

                        // alert(dataFromServer);
                        $('#tableshow').show();
                        $('#usertable_wrapper').show();
                        $('#no_data').hide();


                        renderTable(lang);
                    } else {

                        $('#tableshow').hide();
                        $('#usertable_wrapper').hide();
                        $('#no_data').show();
                    }
                } else {
                    console.error("Invalid response or no data:", response);
                    $dropdown.append('<option value="">No data available</option>');
                }
                // alert(distcode)
                // if (distcode) {
                //     alert()
                //     onchange_region('district', 'distcode', distcode, regioncode)
                // }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
        // } else {
        //     //  $('#deptcode').html(defaultOption);
        //     $('#distcode').select2('destroy');
        //     $('#distcode').select2(null);
        //     $('#distcode').select2();
        // }
    }

    function onchange_distcode() {
        getInstData(lang);
    }

    function getInstData(lang) {

        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();

        $.ajax({
            url: '/inspection/fetch_instDetails', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.data && response.data.length > 0) {
                    // alert('adds');

                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = response.data;

                    // alert(dataFromServer);
                    renderTable(lang);
                } else {

                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                if (xhr.status === 404) {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }
    $('#translate').change(function() {
        const lang = getLanguage('Y'); // Store language selection
        updateTableLanguage(
            lang); // Update the table with the new language by destroying and recreating it

    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#institution_table')) {
            $('#institution_table').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';

        const instColumn = language === 'ta' ? 'insttname' : 'instename';
        const teamname = language === 'ta' ? 'team_head_ta' : 'team_head_en';
        const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';
        var sessionuserid = '<?php echo $sessionuserid; ?>'
        let teamheadflag = 'Y';




        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }
        let inpectflag = dataFromServer.some(item => item.processcode !== 'C' && item.processcode != null);
        // console.log(inpectflag);
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#institution_table')) {
            $('#institution_table').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#institution_table').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#institution_table").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            columns: [{
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
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row?.[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Institution Details",
                    render: function(data, type, row) {
                        const instName = row?.[instColumn] || '-';
                        const regionName = row?.[regionColumn] || '-';
                        const distName = row?.[districtColumn] || '-';

                        return `<b>Region:</b> ${regionName}<br><b>District:</b> ${distName}<br><b>Institution:</b> ${instName}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: teamname,
                    title: columnLabels?.["teamname"]?.[language] || 'Team Details',
                    render: function(data, type, row) {
                        return `<b>Team Head:</b>${row[teamname]}<br><b>Team Members:</b>${row[teammembername]}`;

                        // return row[teamname] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: "entrymeetdate",
                    title: columnLabels?.['entrymeetdate']?.[language] || "Entry Meeting Date",
                    render: function(data, type, row) {
                        const entrymeetDate = row.entrymeetdate ? new Date(row.entrymeetdate)
                            .toLocaleDateString(
                                'en-GB') : "N/A";
                        return `${entrymeetDate}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: 'exitmeetdate',
                    title: columnLabels?.['exitmeetdate']?.[language] || "Exit Meeting Date",
                    render: function(data, type, row) {
                        const exitmeetDate = row.exitmeetdate ? new Date(row.exitmeetdate)
                            .toLocaleDateString(
                                'en-GB') : "N/A";
                        return `${exitmeetDate}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: 'fromdate',
                    title: columnLabels?.['fromdate']?.[language] || "Proposed Date",
                    render: function(data, type, row) {
                        const isValidDate = (d) => {
                            const date = new Date(d);
                            return d && !isNaN(date);
                        };

                        const fromDate = isValidDate(row.fromdate) ? new Date(row.fromdate)
                            .toLocaleDateString('en-GB') : "N/A";
                        const toDate = isValidDate(row.todate) ? new Date(row.todate)
                            .toLocaleDateString('en-GB') : "N/A";

                        return `${fromDate} - ${toDate}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"

                },
                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Audit Status",
                    render: function(data, type, row) {
                        if (row.statusflag === 'F') {
                            if (row.entrymeetdate && row.exitmeetdate) {
                                return `<h6>Completed</h6>`;
                            } else if (row.entrymeetdate && !row.exitmeetdate) {



                                const sessionUserId = '<?php echo trim($sessionuserid); ?>';
                                const sameinspectionperson = row.inspectprocesscode != null ?
                                    row.initiatedId.toString().trim() == sessionUserId :
                                    true;
                                // console.log('issame:' + sameinspectionperson);
				const auditIds = dataFromServer
                                    .filter(item => item.auditscheduleid === row.auditscheduleid)
                                    .map(item => item.auditinspectionid);
                                // console.log('issame:' + sameinspectionperson);
                                const matchingItems = dataFromServer
                                    .filter(item => item.auditscheduleid === row.auditscheduleid)
                                    .map(item => ({
                                        auditinspectionid: item.auditinspectionid,
                                        processcode: item.processcode,
                                        activeinspection: item.activeinspection
                                    }));

                                // Step 2: Check if any item has activeinspection === 'A'
                                const isActiveflag = matchingItems.some(item => item.activeinspection === 'A') ? 'Y' : 'N'
                                let param3 = ''
                                let labelText = '';
                                let showButton = false;
                                let url;
                                if (sessionuserid == row.teamhead_userid) {

                                    teamheadflag = 'Y';
                                } else {
                                    teamheadflag = '';
                                }



                                if (teamheadflag == 'Y') {
                                    if (row.inspectprocesscode == 'C') {

                                        param3 = 'new'
                                        labelText = ' Inspection  Completed '
                                    } else {
                                        labelText = 'View Inspection  Details';
                                        param3 = ''
                                    }
                                    showButton = true;

                                } 
 				else if (teamheadflag != 'Y') {
                                    labelText = row.inspectprocesscode != null ? row
                                        .inspectprocesscode === 'C' ? 'Reinspection' :
                                        'Under Processing' :
                                        'Start Inspection';

                                    param3 = row.inspectprocesscode === 'C' ? 'new' : '';
                                    //alert(isActiveflag)
                                    if (row.activeinspection == 'I' && row.inspectprocesscode == 'C' &&
                                        (isActiveflag == 'Y') && !(sameinspectionperson)) {

                                        url = '#'
                                    } else if (row.activeinspection == 'I' && row.inspectprocesscode == 'C' &&
                                        (isActiveflag == 'Y') && (sameinspectionperson)) {
                                        url = '#'
                                    } else if (row.activeinspection == 'I' && row.inspectprocesscode == 'C' &&
                                        (isActiveflag == 'N')) {
                                        url = ''
                                    }
                                    //     if (!(sameinspectionperson) && (isActiveflag == 'Y') && row.activeinspection != 'A') {

                                    //         url = '#'
                                    //     }
                                    // if (!(sameinspectionperson) && (isActiveflag == 'N') && row.activeinspection != 'A') {
                                    //     alert(flag)
                                    //     url = ''
                                    // }

                                    showButton = true;
                                }

				 else {

                                    if (row.inspectprocesscode == null) {
                                        labelText = 'Start Inspection';
                                    } else if (row.inspectprocesscode === 'C') {
                                        labelText = 'Reinspection';
                                    } else {
                                        labelText = 'Under Processing';
                                        url = (flag && row.activeinspection == 'A') ? '#' : ''
                                    }

                                    param3 = 'new'
                                    showButton = true;
                                }


                                if (url != '#') {
                                    var param1 = row.encrypted_auditscheduleid;
                                    var param2 = row.encrypted_auditinspectionid;
                                    var baseUrl = '/inspectionquery/';
                                    url = baseUrl +
                                        'form=' + encodeURIComponent(param3 || '') +
                                        '&encrypted_auditscheduleid=' + encodeURIComponent(param1 ||
                                            '') +
                                        '&encrypted_auditinspectionid=' + encodeURIComponent(param2 ||
                                            '');
                                }


                                if (teamheadflag != 'Y') {
                                   if ((row.inspectprocesscode != 'C' && !sameinspectionperson) || (
                                            (isActiveflag == 'Y') &&
                                            !sameinspectionperson)) {
                                        // console.log('asd')
                                        url = '#'
                                    }



                                }
                                // console.log('url' + url)

                                // 'encrypted_auditscheduleid=' + encodeURIComponent(param1 || '') +
                                // '&encrypted_auditinspectionid=' + encodeURIComponent(param2 || '');


                                const bgcolor = row.inspectprocesscode == 'C' ? 'btn-success' : row
                                    .inspectprocesscode != null ? 'btn-warning' :
                                    'btn-info';
                                const actionButton = showButton ?
                                    `<a class="btn btn-sm ${bgcolor}" href="${url}">
                                        <span key="" class="lang">${labelText}</span>
                                     </a>` :
                                    '';

                                return `
                                    <h6>Commenced</h6>
                                    ${actionButton}
                                `;


                            } else {
                                return `<h6>Scheduled</h6>`;
                            }
                        } else {
                            return '<h6>To be Scheduled</h6>';
                        }
                    },

                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Inspection Status",
                    render: function(data, type, row) {
                        if (row.inspectprocesscode == '' || row.inspectprocesscode == null) {

                            return '<h6>Pending</h6>';
                        } else if ((row.inspectprocesscode != '' && row.inspectprocesscode != 'C') || (
                                row.inspectprocesscode != null && row.inspectprocesscode != 'C')) {

                            return '<h6>Ongoing</h6>';
                        } else if (row.inspectprocesscode == 'C') {

                            return `<h6>Completed by  (${row.username} -${row.desigelname})</h6>`;
                        }
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

            ]
        });

        // Mobile column handling
        // const mobileColumns = [
        //     categoryColumn, subcategoryColumn, groupColumn,
        //     callforrecColumn, majorworkColumn,
        //     subworkColumn, mainobjColumn, subobjColumn
        // ];
        // setupMobileRowToggle(mobileColumns);
        // updatedatatable(language, "mappallocationobj_table");

        // console.log("DataTable initialized successfully.");
    }
    $(document).ready(function() {

        var errorMessage = @json(session('errorMessage') ?? ($errorMessage ?? ''));
        var pageName = @json(session('pageName') ?? ($pageName ?? ''));


        console.log('Error Compact Message' + @json(session('errorMessage')))
        if (errorMessage) {

            pageName == 'init_fieldaudit' ? $('#close_button').hide() : '';
            passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');

            $('#ok_button').off('click').on('click', function(event) {
                event.preventDefault();
                if (pageName == 'inspectview') {
                    window.location.href = '/dashboard';
                }


                // If validation passes, manually close the modal

            });
        }
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {
            getInstData(lang);
        } else if (sessionrole == regionrole) {
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {
            onchange_region('region', 'regioncode')
        } else if (sessionrole == dgarole) {
            getInstData(lang);
        } else if (sessionrole == adminrole) {
            getInstData(lang);
        }


    });

    function reset_form() {

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        $('#inspect_form')[0].reset();
        if (sessionrole == distrole) {
            getInstData(lang);
        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            onchange_region('region', 'regioncode')

        } else if (sessionrole == dgarole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();

            getInstData(lang);
        } else if (sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


            getInstData(lang);
        }

    }
</script>


@endsection
