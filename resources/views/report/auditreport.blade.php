@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Audit Schedule Report')


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
  .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* Adjust as needed */
    }
</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="col-12">
    <div class="card card_border">
    <div class="card-header card_header_color">Audit Report Details</div>
    <div class="card-body">
            <form id="auditreport_form" name="auditreport_form" method='post'>
                <div class="row">


                <div class="col-md-4">
                    <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                        name="financialyear">

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
                            name="deptcode[]">


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
                                    data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                    Available
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
                            name="regioncode[]" >

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
                            name="distcode[]" >

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

                    <div class="col-md-4 mb-4">
                        <label class="form-label lang required" key="category"
                          for="validationDefault01">Category</label>

                      <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                      onchange="onchange_region('subcatcode','subcategory')" >



                          {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option> --}}

                      </select>
                  </div>


                  <div class="col-md-4  subcatdiv ">
                      <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                      <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                          name="subcategory[]">



                          {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option> --}}

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

                     <div id="loader">
                            <div class="spinner"></div>
                        </div>



                    <div class="col-md-4">
                        <label class="form-label required lang" for="reportstatuscode" key="reportstatuscode">Status</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" id="reportstatuscode" name="reportstatuscode"
                            >
                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            <option value="P">Pending </option>
                            <option value="Y">Issued</option>
                            <option value="F">Finalized</option>

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
    <div class="card-header card_header_color lang" key="">Audit Report Details</div>
    <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="auditreporttable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department</th>
                                <th class="lang" key="">Region</th>
                                <th class="lang" key="">District</th>
                                <th class="lang" key="">category / <br> Subcategory</th>
                                <th class="lang" key="">Institution</th>
                                <th class="lang" key="">Quarter Type</th>
                                <th class="lang" key="">Entry Meeting Date / <br> Exit Meeting Date</th>
                                <th class="lang" key="">Status</th>


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

        $('#category, #subcategory, #instmappingcode, #auditquarter')
            .val(null)
            .trigger('change.select2');
    }

    if (changedId === 'financialyear') {
        resetFilters(true);
    } else if (changedId === 'deptcode') {
        if (!selectedDept.includes("A")) {
            resetFilters(false);
        }
    }

    deptChangeTimeout = setTimeout(() => {
        onchange_region('auditquarter', 'auditquarter');
        onchange_region('region', 'regioncode');
        onchange_region('catcode', 'category');
    }, 200);
});


$("#regioncode").on("change", function() {
    onchange_region('district', 'distcode');
});

$("#distcode").on("change", function() {
    onchange_region('institution', 'instmappingcode');
});



$(document).ready(function () {
    function updateSelect($select) {
        let selectedValues = $select.val() || [];

        if (selectedValues.includes('A')) {
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

    ['#deptcode','#distcode','#instmappingcode','#regioncode','#category','#subcategory']
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

    var validator = $("#auditreport_form").validate({
        ignore: [],
        rules: {
            financialyear: { required: true },
            "auditquarter[]": { selectRequired: true },
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            "category[]": { selectRequired: true },
            "instmappingcode[]": { selectRequired: true },
        },
        messages: {
            financialyear: "Select a Financial Year",
            "auditquarter[]": "Select a Audit Quarter",
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            "category[]": "Select a Category",
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
        var form = $('#auditreport_form');
        if (form.valid()) {
            getInstData(language);
        }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});





$(document).ready(function () {


$('#category').on('change', function () {

    var deptcode = $('#deptcode').val(); // ← array


    if (deptcode && (deptcode.includes("01") || deptcode.includes("05"))) {
        $('.subcatdiv').hide();
    } else {
        $('.subcatdiv').show();
    }

});


});




    let dataFromServer;




    function onchange_region(valuefor, valueforid) {


        const defaultOption = `
            <option value="" data-name-en="துறையைத் தேர்ந்தெடுக்கவும்" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'துறையைத் தேர்ந்தெடுக்கவும்' : 'Select Department'}
            </option>`;

        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()

        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();
        var financialyear = $("#financialyear").val();
        var catcode = $("#category").val();
        var subcatcode = $("#subcategory").val();
        var auditquarter = $("#auditquarter").val();
        var reportstatuscode = $("#reportstatuscode").val();


        var instmappingcode = $("#instmappingcode").val();
        var lang = getLanguage();

        if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }

        if ((deptcode.includes("A") || regioncode.includes("A") || distcode.includes("A")) && valueforid !== 'subcategory') {

            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A" data-name-en="All" data-name-ta="All">All</option>`);
            dd.select2();
            return;
            }
            if (valuefor === 'subcatcode' && (!catcode || catcode.length === 0)) {
            $dropdown.select2();
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
            formname: "apmsdetails"
        };

    if (valuefor === "region" || valuefor === "catcode" ||  valuefor === "auditquarter") {
        sendData.deptcode = deptcode;
        sendData.financialyear = financialyear;

    }


    if (valuefor === "district") {
        sendData.deptcode = deptcode;
        sendData.regioncode = regioncode;
    }


    if (valuefor === "institution") {
        sendData.deptcode = deptcode;
        sendData.regioncode = regioncode;
        sendData.distcode = distcode;
    }



    // Subcategory dropdown
    if (valuefor === "subcatcode") {
        sendData.catcode = catcode;
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

                    if (!catcode || catcode.length === 0) {
                        catcode = [''];
                    }


                    if (!subcatcode || subcatcode.length === 0) {
                        subcatcode = [''];
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

                                case 'catcode':
                                return item.code === 'A'
                                    ? `<option value="A"
                                            data-name-en="${item.name_en}"
                                            data-name-ta="${item.name_ta}"
                                            data-ifsubcategory="${item.if_subcategory}"
                                            ${catcode.includes('A') ? "selected" : ""}>
                                            ${item.name_en}
                                    </option>`
                                    : `<option value="${item.catcode}"
                                            data-name-en="${item.catename}"
                                            data-name-ta="${item.catename}"
                                            data-ifsubcategory="${item.if_subcategory}"
                                            ${catcode.includes(item.catcode) ? "selected" : ""}>
                                            ${item.catename}
                                    </option>`;



                               case 'auditquarter':
                            return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${distcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.planmappingid}" data-name-en="${item.planname}" data-name-ta="${item.planname}" ${distcode.includes(item.planmappingid) ? "selected" : ""}>${item.planname}</option>`;



                                case 'subcatcode':
                              return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${subcatcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.auditeeins_subcategoryid}" data-name-en="${item.subcatename}" data-name-ta="${item.subcattname}" ${subcatcode.includes(item.auditeeins_subcategoryid) ? "selected" : ""}>${item.subcatename}</option>`;


                                case 'institution':

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

                    $dropdown.append(options || '<option value="">No data available</option>');

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


                console.error('Error details:', xhr, status, error);
            }
        });

    }






    function getInstData(lang) {
        $("#loader").show();


        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();
        var instmappingcode = $("#instmappingcode").val();
        var financialyear = $("#financialyear").val();
        var auditquarter = $("#auditquarter").val();
        var reportstatuscode = $("#reportstatuscode").val();
        var category = $("#category").val();
        var subcatcode = $("#subcategory").val() || 'A';


        if (!auditquarter || auditquarter.length === 0) {
            auditquarter = [];
        }


        if (!Array.isArray(instmappingcode) || instmappingcode.length === 0) {
            instmappingcode = [0];
        }

        var deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0
        ? $('#deptcode').val()
        : ['A'];

        var regioncode = $('#regioncode').val() && $('#regioncode').val().length > 0
            ? $('#regioncode').val()
            : ['A'];

        var distcode = $('#distcode').val() && $('#distcode').val().length > 0
            ? $('#distcode').val()
            : ['A'];

        var instmappingcode = $('#instmappingcode').val() && $('#instmappingcode').val().length > 0
        ? $('#instmappingcode').val()
        : ['A'];



        $.ajax({
            url: '/fetch_auditreport',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                reportstatuscode: reportstatuscode,
                category:category,
                subcatcode:subcatcode,
                financialyear: financialyear,
                auditquarter: auditquarter,
                instmappingcode: instmappingcode,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
        $("#loader").hide();

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
        $("#loader").hide();

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

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditreporttable')) {
            $('#auditreporttable').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const instColumn = language === 'ta' ? 'instename' : 'instename';
        const catcodeColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcatcodeColumn = language === 'ta' ? 'subcattname' : 'subcatename';
        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        if ($.fn.DataTable.isDataTable('#auditreporttable')) {
            $('#auditreporttable').DataTable().clear().destroy();
        }

        table = $('#auditreporttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#auditreporttable").wrap(
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
                    className: 'text-wrap  text-center'
                },

                {
                    data: regionColumn,
                    title: columnLabels?.[regionColumn]?.[language] || "Region",
                    render: function(data, type, row) {
                        return data || '-';
                    },
                    className: "text-start text-wrap"
                },
                {
                    data: districtColumn,
                    title: columnLabels?.[districtColumn]?.[language] || "District",
                    render: function(data, type, row) {
                        return data || '-';
                    },
                    className: "text-start text-wrap"
                },
                {
                data: null,
                title: columnLabels?.[catcodeColumn]?.[language],
                render: function(data, type, row) {
                    const category = row?.[catcodeColumn] || '-';
                    const subcategory = row?.[subcatcodeColumn] || '-';
                    return `<strong>Category:</strong> ${category} <br> <strong>SubCategory:</strong> ${subcategory}`;
                },
                className: "text-start d-none d-md-table-cell extra-column text-wrap"
            },

                {
                    data: instColumn,
                    title: columnLabels?.[instColumn]?.[language],
                    render: function(data, type, row) {
                        return data || '-';
                    },
                    className: "text-start text-wrap wrap-50"
                },


                {
                    data: "planname",
                    title: columnLabels?.["planname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.planname || '-';
                    }
                },

                {
                data: null,
                title: columnLabels?.['meetingdates']?.[language],
                render: function(data, type, row) {

                    function formatDate(dateStr) {
                        if (!dateStr) return '-';
                        const d = new Date(dateStr);
                        if (isNaN(d)) return '-';

                        let day = String(d.getDate()).padStart(2, '0');
                        let month = String(d.getMonth() + 1).padStart(2, '0');
                        let year = d.getFullYear();

                        return `${day}-${month}-${year}`;
                    }

                    const entry = formatDate(row?.entrymeetdate);
                    const exit  = formatDate(row?.exitmeetdate);

                    return ` ${entry} <br/>  ${exit}`;
                },
                className: "text-start d-none d-md-table-cell extra-column text-wrap"
            },



                {
                    data: null,
                    title: columnLabels?.["status"] || "Status",
                    className: "text-center extra-column",
                    render: function(data, type, row) {

                         if (row.issuedflag === 'Y') {
                            return row.issuedflag === 'Y'
                                ? '<span class="btn btn-warning btn-sm">Issued</span>'
                                : '<span class="btn btn-secondary btn-sm">-</span>';
                        }
                        if (row.sendintimation === 'F') {
                            return row.sendintimation === 'F'
                                ? '<span class="btn btn-success btn-sm">Finalised not issued</span>'
                                : '<span class="btn btn-secondary btn-sm">-</span>';
                        }
                        if (row.sendintimation === 'Y' || row.sendintimation == null) {
                            return '<span class="btn btn-info btn-sm">Pending</span>';
                        }
                        return '<span class="btn btn-secondary btn-sm">NA</span>';
                    }


                }


            ]
        });
           const mobileColumns = [
            departmentColumn, instColumn, regionColumn,
            districtColumn,"count", "teamname",
            "teammembername", "entrymeetdate", "exitmeetdate","fromdate","todate"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "auditreporttable");
    }

    function formatDate(dateStr) {
    if (!dateStr) return "";
    let d = new Date(dateStr);
    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();
    return `${day}-${month}-${year}`;
}



function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);

    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    const columnMap = {
        departmentColumn: language === 'ta' ? 'deptesname' : 'deptesname',
        regionColumn: language === 'ta' ? 'regionename' : 'regionename',
        districtColumn: language === 'ta' ? 'distename' : 'distename',
        instColumn: language === 'ta' ? 'insttname' : 'instename',
        catcodeColumn : language === 'ta' ? 'cattname' : 'catename',
        subcatcodeColumn : language === 'ta' ? 'subcattename' : 'subcatename',

    };

    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["Region"] || "Region", key: "region" },
        { header: dtText["District"] || "District", key: "district" },
        { header: dtText["Institution"] || "Institution", key: "institution" },

        { header: dtText["Category"] || "Category", key: "category" },
        { header: dtText["Subcategory"] || "Subcategory", key: "subcategory" },

        { header: dtText["Entry Meet Date"] || "Entry Meet Date", key: "entry" },
        { header: dtText["Exit Meet Date"] || "Exit Meet Date", key: "exit" },
        { header: dtText["Status"] || "Status", key: "status" },

    ];


    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");

        let rowData = {};
        if (dataRow) {
            try {
                let cleaned = $('<textarea/>').html(dataRow).text();
                rowData = JSON.parse(cleaned);
            } catch (e) {
               // console.error("Invalid JSON in data-row:", dataRow, e);
                rowData = {};
            }
        }

        return {
            department: rowData[columnMap.departmentColumn] || "-",
            region: rowData[columnMap.regionColumn] || "-",
            district: rowData[columnMap.districtColumn] || "-",
            institution: rowData[columnMap.instColumn] || "-",
            category: rowData[columnMap.catcodeColumn] || "-",
            subcategory: rowData[columnMap.subcatcodeColumn] || "-",

            entry: rowData["entrymeetdate"],
            exit: rowData["exitmeetdate"],

            status: (() => {
            if (rowData.issuedflag === "Y") return "Issued";
            if (rowData.sendintimation === "F") return "Finalised not issued";
            if (rowData.sendintimation === "Y" || rowData.sendintimation == null) return "Pending";
            return "NA";
        })(),

        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // ✅ Create Workbook and Worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add headers
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });

    // ✅ Add data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    // ✅ Finalize and export
    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}




    $(document).ready(function() {
                $("#loader").hide();

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
            onchange_region('catcode', 'category');

            onchange_region('institution', 'instmappingcode')
        } else if (sessionrole == regionrole) {

              onchange_region('district', 'distcode')
              onchange_region('catcode', 'category');

        } else if (sessionrole == headofficerole) {

            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');

        } else if (sessionrole == dgarole) {

        } else if (sessionrole == adminrole) {

           // getInstData(lang);
        }


    });






    function reset_form() {
        var lang = getLanguage();
        $("#loader").hide();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#deptcode option').prop('disabled', false);



        $('#auditreport_form')[0].reset();

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        var form = $('#auditreport_form');

            form[0].reset();
            $('.subcatdiv').show();

            $('#auditquarter').val(['']).select2();
            $('#reportstatuscode').val('A').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#financialyear').val('').select2();
            $('#instmappingcode').val('').select2();
        // Reset validation messages
        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }
        if (sessionrole == distrole) {


        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();


            $('#distcode').val('').select2();


        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();

            $('#distcode').val('').select2();



        } else if (sessionrole == dgarole) {


            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();



        } else if (sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();

          }

    }
</script>


@endsection