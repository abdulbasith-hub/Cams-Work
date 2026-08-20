@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Audit Slip Count')


@php

    $sessionchargedel = session('charge');
    //  print_r($sessionchargedel);
    // print_r($sessionchargedel->roletypecode);
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
    $auditteamhead = $sessionchargedel->auditteamhead;
@endphp



<style>

</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Exit Meeting Status</div>
        <div class="card-body">
            <form id="exitmeeting_form" name="exitmeeting_form" >
            @csrf
                <div class="row">
                <div class="col-md-4">
                    <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                        name="financialyear" onchange="onchange_region('yearcode','financialyearid')">

                        <option value="" data-name-en="---Select Financial year---"
                            data-name-ta="---நிதி ஆண்டை தேர்வு செய்க---">Select Financial year</option>

                        @foreach ($financialyear as $year)
                            <option value="{{ $year->financialyearcode }}"
                                data-name-en="{{ $year->financialyear }}"
                                data-name-ta="{{ $year->financialyear }}"
                                {{ $year->financialyear }}
                            </option>
                        @endforeach
                    </select>
                </div>






                    <div class="col-md-4">
                    <label class="form-label required lang" for="deptcode" key="department">Department</label>
                    <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?> id="deptcode"
                        name="deptcode[]" >


                        {{-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> --}}


                        <option value="A" data-name-en="All" data-name-ta="அனைத்து துறைகளும்"
                            @if (is_array(old('dept', $deptcode)) && in_array('A', old('dept', $deptcode))) selected @endif>
                            All
                        </option>

                        @if (!empty($dept) && count($dept) > 0)
                            @foreach ($dept as $department)
                                <option value="{{ $department->deptcode }}"
                                @if (
                                    (is_array(old('dept', $deptcode)) && in_array($department->deptcode, old('dept', $deptcode)))
                                    || (old('dept', $deptcode) == $department->deptcode)
                                )
                                    selected
                                @endif


                                    data-name-en="{{ $department->deptelname }}"
                                    data-name-ta="{{ $department->depttlname }}">
                                    {{ $department->deptelname }}
                                </option>
                            @endforeach
                        @else
                            <option disabled data-name-en="No Department Available"
                                data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments Available
                            </option>
                        @endif
                    </select>
                </div>

                   <div class="col-md-4">
                            <label class="form-label required lang" for="auditquarter" key="auditquarter">Audit Quarter</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"

                                id="auditquarter" name="auditquarter[]">
                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            </select>
                        </div>



                    <div class="col-md-4">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode[]" onchange="onchange_region('district','distcode')">

                            {{-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option> --}}

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            @if ($regioncode)
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
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]" onchange="onchange_region('institutionforexitmeeting','instmappingcode')">

                            {{-- <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option> --}}

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            @if ($distcode)
                                @foreach ($district as $dist)
                                    <option value="{{ $dist->distcode }}"
                                        @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                        data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                        {{ $dist->distename }}
                                    </option>
                                @endforeach
                            @else

                            @endif
                        </select>
                    </div>


                    <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]">

                                {{-- <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option> --}}

                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                </select>
                    </div>



                </div>
                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Submit</button>
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
    <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Institutes Pending for Exitmeeting</div>
    </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="exitmeetingtable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="department">Department Depatls</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">Quarter type</th>
                                <th class="lang align-middle text-center" key="">Team Details</th>
                                <th class="lang align-middle text-center" key="">Entry Meeting Date</th>
                                <th class="lang align-middle text-center" key="">Exit Meeting Date</th>
                                <th class="lang align-middle text-center" key="">Proposed Exit Meeting Date</th>


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

<style>
        .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px;
        /* Adjust as needed */
    }
</style>

<script src="../assets/js/jquery.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>


<script>



var distcode = '<?php echo $distcode; ?>'
var regioncode = '<?php echo $regioncode; ?>'
var deptcode = '<?php echo $deptcode; ?>'


let deptChangeTimeout;

$('#deptcode, #financialyear').on('change', function () {
    clearTimeout(deptChangeTimeout);

    const changedId = this.id;

      let selectedDept = $('#deptcode').val();

    if (!Array.isArray(selectedDept)) {
        selectedDept = selectedDept ? [selectedDept] : [];
    }

    function resetFilters(includeDept = false) {
         if (includeDept && !deptcode) {
            $('#deptcode').val(null).trigger('change.select2');
        }

        if (!regioncode) {
        $('#regioncode').val(null).trigger('change.select2');
            }

        if (!distcode) {
            $('#distcode').val(null).trigger('change.select2');
        }

        $('#auditquarter,#instmappingcode')
            .val(null)
            .trigger('change.select2');
    }

    if (changedId === 'financialyear') {
        resetFilters(true);
    } else if (changedId === 'deptcode') {
        if (!deptcode.includes("A")) {
            resetFilters(false);
        }
    }

    deptChangeTimeout = setTimeout(() => {
         onchange_region('auditquarter', 'auditquarter');
        if (!regioncode) {
        onchange_region('region','regioncode');
        }
    }, 200);
});





$(document).ready(function () {
    function updateSelect($select) {
        let selectedValues = $select.val() || [];

        if (selectedValues.includes('A')) {
            // If "All" is selected, keep only A
            $select.val(['A']).trigger('change.select2');
            $select.find('option').not('[value="A"]').prop('disabled', true);

        } else {
            if (selectedValues.includes('')) {
                selectedValues = selectedValues.filter(v => v !== '');
                $select.val(selectedValues).select2();
            }

            $select.find('option').prop('disabled', false);
        }
    }

    function handleAllOption($select) {
        $select.on('change', function () {
            updateSelect($select);
        });

        if (!$select.val() || $select.val().length === 0) {
            $select.val(['']).select2();
        }

        updateSelect($select);
    }

    ['#deptcode','#distcode','#instmappingcode','#regioncode','#auditquarter','#subcategory']
        .forEach(function (selector) {
            handleAllOption($(selector));
        });
});


jsonLoadedPromise.then(() => {

            const language = window.localStorage.getItem('lang') || 'en';

$.validator.addMethod("selectRequired", function(value, element) {
    let selected = $(element).val();
    return selected && selected.length > 0 && !selected.includes('');
}, "Please select at least one option.");

var validator = $("#exitmeeting_form").validate({
    ignore: [],
    rules: {
        financialyear: { required: true },
        "auditquarter[]": { selectRequired: true },
        "deptcode[]": { selectRequired: true },
        "regioncode[]": { selectRequired: true },
        "distcode[]": { selectRequired: true },
        "instmappingcode[]": { selectRequired: true },

    },
    messages: {
        financialyear: "Select a Financial Year",
        "auditquarter[]": "Select a Audit Quarter",
        "deptcode[]": "Select a Department",
        "regioncode[]": "Select a Region",
        "distcode[]": "Select a District",
        "instmappingcode[]": "Select a Institution",

    },
    errorPlacement: function(error, element) {
        if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('.select2'));
        } else {
            error.insertAfter(element);
        }
    }
});

    $('#buttonaction').on('click', function (e) {
        e.preventDefault();
        var form = $('#exitmeeting_form');



    if (form.valid()) {

        var lang = 'en';
        getInstData(lang);
    } else {
        form.validate().focusInvalid();
    }
    });
    reset_form();

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});





$(document).ready(function () {
    var $financialYear = $('#financialyear');

    if ($financialYear.val() !== '') {
        setTimeout(function () {
            $financialYear.select2();
        }, 100);
    }
});


$('#auditquarter').on('change', function () {
    let selectedValues = $(this).val() || [];

    if (selectedValues.includes('A')) {
        $(this).val(['A']);
        $('#auditquarter option').not('[value="A"]').prop('disabled', true);
    } else {
        $('#auditquarter option').prop('disabled', false);
    }
});




    let dataFromServer;



    function onchange_region(valuefor, valueforid) {


        const districtDropdown = $('#distcode');
        const regionDropdown = $('#regioncode');


        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()




        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();
        var financialyear = $("#financialyear").val();

        var auditquarter = $("#auditquarter").val();

        var instmappingcode = $("#instmappingcode").val();


         var lang = getLanguage();


         if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }

         if ((deptcode.includes("A"))) {

                const dd = $("#" + valueforid);
                dd.select2('destroy');
                dd.empty();
                dd.append(`<option value="A" data-name-en="All" data-name-ta="All">All</option>`);
                dd.select2();
                return;
        }

          if (valueforid === 'auditquarter' && deptcode.length > 1) {
            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A">All</option>`);
            dd.select2();
            return;
        }


        let sendData = {
            valuefor: valuefor,
            formname: "exitmeetingnotdone"
        };

        if (valuefor === "auditquarter" || valuefor === "region") {
            sendData.deptcode = deptcode;
            sendData.financialyear = financialyear;

        }

         if (valuefor === "district") {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
    }


        if (valuefor === "institutionforexitmeeting") {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
            sendData.distcode = distcode;
        }


        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
           data: sendData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {


                if (response.success && Array.isArray(response.data)) {

                    isRegionLoading = false;

                    $dropdown.empty();

                    if (!regioncode || regioncode.length === 0) {
                        regioncode = [''];
                    }

                    if (!distcode || distcode.length === 0) {
                        distcode = [''];
                    }

                    if (!instmappingcode || instmappingcode.length === 0) {
                        instmappingcode = [''];
                    }




                    const options = response.data.map(item => {
                        switch (valuefor) {
                            case 'region':
                        return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${regioncode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                            : `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}" ${regioncode.includes(item.regioncode) ? "selected" : ""}>${item.regionename}</option>`;


                            case 'district':
                            return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${distcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${distcode.includes(item.distcode) ? "selected" : ""}>${item.distename}</option>`;


                            case 'auditquarter':

                                const selectedList = Array.isArray(auditquarter)
                                ? auditquarter
                                : (auditquarter ? [auditquarter] : []);
                                return item.code === 'A'
                                    ? `<option value="A"
                                        ${selectedList.includes('A') ? "selected" : ""}>
                                        ${item.name_en}
                                    </option>`
                                    : `<option value="${item.planmappingid}"
                                        ${selectedList.includes(String(item.planmappingid)) ? "selected" : ""}>
                                        ${item.planname}
                            </option>`;

                                case 'institutionforexitmeeting':


                                    if (deptcode.includes('A') || regioncode.includes('A') || distcode.includes('A')) {
                                    return item.code === 'A'
                                        ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${instmappingcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                        : `<option value="${item.instid}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${instmappingcode.includes(item.instid) ? "selected" : ""}>${item.instename}</option>`;
                                    }

                                    if (!Array.isArray(response.data) || response.data.length === 1 && item.code === 'A') {
                                            return `<option disabled>No institution available</option>`;
                                    }

                                return item.code === 'A'

                                    ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${instmappingcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                    : `<option value="${item.instid}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${instmappingcode.includes(item.instid) ? "selected" : ""}>${item.instename}</option>`;


                            default:
                                return '';
                        }
                    }).join('');

                    $dropdown.append(options || '<option value="" disabled>No data available</option>');

                    if ($dropdown.hasClass('select2-hidden-accessible')) {
                     $dropdown.trigger('change.select2');
                  }


                } else {
                    console.error("Invalid response or no data:", response);
                    $dropdown.append('<option value="">No data available</option>');
                }

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

    }



    function getInstData(lang) {

        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val() || 'A';
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val() || 'A';
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val() || 'A';
        var financialyear = $("#financialyear").val() || '';

        var auditquarter = $("#auditquarter").val();

        var instmappingcode = $("#instmappingcode").val() || 0;


        $.ajax({
            url: '/exitmeetingnotdone', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                financialyear: financialyear,
                auditquarter: auditquarter,
               instmappingcode: instmappingcode,
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

                console.error('Error details:', xhr, status, error);
            }
        });
    }


    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(
            lang);

    });


    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const instColumn = language === 'ta' ? 'insttname' : 'instename';
        const desigColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#exitmeetingtable')) {
            $('#exitmeetingtable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#exitmeetingtable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#exitmeetingtable").wrap(
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
                    data: null,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        const dept = row?.[departmentColumn] || '-';
                        const region = row?.[regionColumn] || '-';
                        const dist = row?.[districtColumn] || '-';

                        return `
                            <div><strong>Department:</strong> ${dept}</div>
                            <div><strong>Region:</strong> ${region}</div>
                            <div><strong>District:</strong> ${dist}</div>
                        `;
                    },
                    className: "text-wrap text-start"
                },


                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Institution",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap wrap-50"
                },



                {
                    data: "planname",
                    title: columnLabels?.["planname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap  text-center",
                    render: function(data, type, row) {
                        return row.planname || '-';
                    }
                },
                {
                    data: null,
                    title: columnLabels?.["team_info"]?.[language] || "Team Info",
                    render: function(data, type, row) {
                        const heads = row?.team_heads ? row.team_heads.replace(/\n/g, "<br>") : "-";
                        const members = row?.team_members ? row.team_members.replace(/\n/g, "<br>") : "-";

                        return `
                            <div><strong>Team Head:</strong> ${heads}</div>
                            <div><strong>Team Member(s):</strong> ${members}</div>
                        `;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: "entrymeetdate",
                    title: columnLabels?.["entrymeetdate"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap text-center",
                    render: function(data, type, row) {
                        if (!row.entrymeetdate) return '-';

                        let date = new Date(row.entrymeetdate);
                        let day = String(date.getDate()).padStart(2, '0');
                        let month = String(date.getMonth() + 1).padStart(2, '0'); // months are 0-based
                        let year = date.getFullYear();

                        return `${day}-${month}-${year}`;
                    }
                },

                {
                    data: "exitmeetdate",
                    title: columnLabels?.["exitmeetdate"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap  text-center",
                    render: function(data, type, row) {
                        return row.exitmeetdate || '-';
                    }
                },
                {
                    data: "proposedexitmeetdate",
                    title: columnLabels?.["proposedexitmeetdate"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap  text-center",
                    render: function(data, type, row) {
                        if (!row.proposedexitmeetdate) return '-';

                        let date = new Date(row.proposedexitmeetdate);
                        let day = String(date.getDate()).padStart(2, '0');
                        let month = String(date.getMonth() + 1).padStart(2, '0'); // months are 0-based
                        let year = date.getFullYear();

                        return `${day}-${month}-${year}`;
                    }
                },






            ]
        });

        const mobileColumns = [
            departmentColumn, instColumn, regionColumn,
            districtColumn,"count", "teamname",
            "teammembername", "entrymeetdate", "exitmeetdate","fromdate","todate"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "exitmeetingtable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#exitmeetingtable')) {
            $('#exitmeetingtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    // Report title (can be static or pulled from translations)
    let titleKey = `${tableId}_title`;
    let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Audit Slip Count Report";

    // Sheet name (Excel sheet names must be <= 31 chars)
    let safeSheetName = reportTitle.substring(0, 31);

    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    const columnMap = {
        departmentColumn : language === 'ta' ? 'deptesname' : 'deptesname',
        regionColumn : language === 'ta' ? 'regionename' : 'regionename',
        districtColumn : language === 'ta' ? 'distename' : 'distename',
        instColumn : language === 'ta' ? 'insttname' : 'instename',
        desigColumn : language === 'ta' ? 'desigtsname' : 'desigesname',


    };

    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["Region"] || "Region", key: "region" },
        { header: dtText["District"] || "District", key: "district" },
        { header: dtText["Institution"] || "Institution", key: "institution" },
        { header: dtText["Financialyear"] || "Financialyear", key: "financialyear" },
        { header: dtText["quartertype"] || "Quarter Type", key: "quartertype" },
        { header: dtText["teamhead"] || "Team Head", key: "teamhead" },
        { header: dtText["teammember"] || "Team Member", key: "teammember" },
        { header: dtText["entrymeetdate"] || "Entry Meeting Date", key: "entrymeetdate" },
        { header: dtText["exitmeetdate"] || "Exit Meeting Date", key: "exitmeetdate" },
        { header: dtText["proposedexitdate"] || "Proposed Exit Meeting Date", key: "proposedexitdate" },

    ];

    let rawData = table.rows({ search: 'applied' }).data().toArray();
    let excelData = [];

    rawData.forEach(row => {
        try {
            let button = $(row[0]).find("button.toggle-row");
            let dataRow = button.attr("data-row");
            let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

            let team_heads = rowData["team_heads"] || "-";
            let team_member = rowData["team_members"] || "-";

            excelData.push({
                department: rowData[columnMap.departmentColumn] || "-",
                region: rowData[columnMap.regionColumn] || "-",
                district: rowData[columnMap.districtColumn] || "-",
                institution: rowData[columnMap.instColumn] || "-",
                financialyear: rowData["financialyear"] || "-",
                quartertype: rowData["planname"] || "-",
                teamheads: rowData["team_heads"] || "-",
                teammember: rowData["team_members"] || "-",
                entrymeetdate: rowData["entrymeetdate"] || "-",
                exitmeetdate: rowData["exitmeetdate"] || "-",
                proposedexitmeetdate: rowData["proposedexitmeetdate"] || "-",
            });
        } catch (e) {}
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // Row 1 → Report title
    XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });

        // Merge across columns
        ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: headers.length - 1 } }];

        // "Fake" centering for free version by padding
        let colCount = headers.length;
        let totalWidth = colCount * 15; // rough space per column
        let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");
        XLSX.utils.sheet_add_aoa(ws, [[paddedTitle]], { origin: "A1" });
            // Row 2 → Column headers
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A2" });

    // Row 3 → Data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A3" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}


    $(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        var distcode = '<?php echo $distcode; ?>'
        var regioncode = '<?php echo $regioncode; ?>'
        var deptcode = '<?php echo $deptcode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {
          //  getInstData(lang);

        } else if (sessionrole == regionrole) {
            onchange_region('district', 'distcode')
          //  getInstData(lang);

        } else if (sessionrole == headofficerole) {
            onchange_region('region', 'regioncode')
           // getInstData(lang);

        } else if (sessionrole == dgarole) {
          //  getInstData(lang);

        } else if (sessionrole == adminrole) {
          //  getInstData(lang);
        }


    });



    function reset_form() {
        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        $('#auditquarter').val(['']).select2();
        $('#instmappingcode').val('').select2();
        $('#financialyear').val('').select2();

        $('#exitmeeting_form')[0].reset();

        var form = $('#exitmeeting_form');

            form[0].reset();
            // Reset validation messages
            if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
            }

        if (sessionrole == distrole) {
        $('#auditquarter').val(['']).select2();
        $('#instmappingcode').val('').select2();
        onchange_region('institutionforexitmeeting','instmappingcode')

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


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#deptcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();



        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }

    }
</script>


@endsection