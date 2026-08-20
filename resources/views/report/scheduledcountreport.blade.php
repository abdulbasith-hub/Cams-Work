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
        <div class="card-header card_header_color">Status Report on Audit Slip</div>
        <div class="card-body">
            <form id="auditslip_form" name="auditslip_form" method='post'>
            @csrf
                <div class="row">

                <input type="hidden" value="" id="if_subcategory" name="if_subcategory">
                <div class="col-md-4">

                    <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                        name="financialyear" >

                        <option value="" data-name-en="Select Financial year"
                            data-name-ta="நிதி ஆண்டை தேர்வு செய்க">Select Financial year</option>

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
                    <select
                        class="form-select mr-sm-2 select2 lang-dropdown"
                        multiple="multiple"
                        id="deptcode"
                        name="deptcode[]"  <?php echo $make_dept_disable; ?>
                        >

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
                            name="regioncode[]" >

                            {{-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option> --}}

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            @if ($regioncode)
                                @foreach ($region as $reg)
                                    <option value="{{ $reg->regioncode }}"
                                    @if (
                                        (is_array(old('region', $regioncode)) && in_array($reg->regioncode, old('region', $regioncode)))
                                        || (!old('region') && empty($regioncode) && $reg->regioncode == '01')
                                        || (old('region', $regioncode) == $reg->regioncode)
                                    )
                                        selected
                                    @endif
                                data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                        {{ $reg->regionename }}
                                    </option>
                                @endforeach
                            @else

                            @endif
                        </select>
                    </div>






                    <div class="col-md-4">
                        <label class="form-label required lang" for="distcode" key="District">District</label>
                        <select class=" mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]">


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



                                <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option>

                            </select>
                        </div>


                        <div class="col-md-4  subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                            <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                                name="subcategory[]">



                                <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option>

                            </select>
                        </div>



                    <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]"  >

                                {{-- <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option> --}}

                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                </select>
                    </div>


                    <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="maxslip"
                                for="maxslip">No.of.Slip(s)</label>
                            <input  type="number" min="0"  max="9999"
                            class="form-control only_numbers" id="maxslip" name="maxslip" required
                                data-placeholder-key="">
                    </div>




                </div>

                 <div id="loader">
                            <div class="spinner"></div>
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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Auditor's who have generated 0 or fewer audit slips
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="schedulecountreport"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="">Department Details</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">Categories</th>
                                <th class="lang align-middle text-center" key="">Quarter</th>
                                <th class="lang align-middle text-center" key="">Auditor's Name</th>
                                <th class="lang align-middle text-center" key="">Auditor's Details</th>
                                <th class="lang align-middle text-center" key="">No of Slip(s)</th>
                                <th class="lang align-middle text-center" key="">Pending</th>
                                <th class="lang align-middle text-center" key="">Dropped</th>
                                <th class="lang align-middle text-center" key="">Converted to Para</th>


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

        $('#auditquarter,#instmappingcode,#category,#subcategory')
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
        onchange_region('catcode', 'category');
    }, 200);


});


$("#regioncode").on("change", function() {
    onchange_region('district', 'distcode');
});

$("#distcode").on("change", function() {
    onchange_region('institution', 'instmappingcode');
});

$('#maxslip').on('input', function () {
    let val = $(this).val();

    val = val.replace(/[^0-9]/g, '');
    if (val.length > 4) {
        val = val.slice(0, 4);
    }
    if (val > 9999) {
        val = 9999;
    }

    $(this).val(val);
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

    ['#deptcode','#distcode','#instmappingcode','#regioncode','#category','#auditquarter','#subcategory']
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

    var validator = $("#auditslip_form").validate({
        ignore: [],
        rules: {
            financialyear: { required: true },
            "auditquarter[]": { selectRequired: true },
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            "category[]": { selectRequired: true },
            "instmappingcode[]": { selectRequired: true },
              maxslip: { required: true },

        },
        messages: {
            financialyear: "Select a Financial Year",
            "auditquarter[]": "Select a Audit Quarter",
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            "category[]": "Select a Category",
            "instmappingcode[]": "Select a Institution",
            maxslip: "Enter the maximum number of slips"

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
        var form = $('#auditslip_form');

        if (form.valid()) {
            let maxSlipValue = $('#maxslip').val() || 0;
            $('#audit-slip-heading').text(`List of Auditor's who have generated ${maxSlipValue} or fewer audit slips`);
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


$('#category').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var ifSubcategory = selectedOption.data('ifsubcategory') || '';
    $('#if_subcategory').val(ifSubcategory);
});

$('#category').on('change', function () {
    var selectedOption = $(this).find('option:selected');
    var ifSubcategory = selectedOption.data('ifsubcategory') || '';
    var categoryVal = selectedOption.val();

    $('#if_subcategory').val(ifSubcategory);

    if (categoryVal === 'A' || ifSubcategory === 'N') {
        $('.subcatdiv').hide();
        $('#subcatcode').val(null).trigger('change');
        $('#subcatcode').prop('disabled', true);
    } else {
        $('.subcatdiv').show();
        $('#subcatcode').prop('disabled', false);
    }
});


$(document).ready(function () {
    var ifSubcategory = $('#category').find('option:selected').data('ifsubcategory');
    if (ifSubcategory === 'N') {
        $('.subcatdiv').hide();
    } else {
    }
});






    let dataFromServer;

    // let isRegionLoading = false;

    function onchange_region(valuefor, valueforid) {
       // if (isRegionLoading) return;
        //isRegionLoading = true;


        const districtDropdown = $('#distcode');
        const regionDropdown = $('#regioncode');

        const defaultOption = `
            <option value="" data-name-en="துறையைத் தேர்ந்தெடுக்கவும்" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'துறையைத் தேர்ந்தெடுக்கவும்' : 'Select Department'}
            </option>`;

        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()



        var deptcode =  $('#deptcode').val();
        var regioncode = $('#regioncode').val();
        var distcode =  $('#distcode').val();
        var financialyear = $("#financialyear").val();

      //  var auditquarter = $("#auditquarter").val();

        var catcode = $("#category").val();
        var subcatcode = $("#subcategory").val();

        var instmappingcode = $("#instmappingcode").val();

        var maxslip = $("#maxslip").val();

         var lang = getLanguage();


         if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }

        if (deptcode.includes("A") || regioncode.includes("A") || distcode.includes("A")) {
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
        formname: "scheduledcountreport"
    };

    if (valuefor === "region" || valuefor === "catcode" || valuefor === "auditquarter") {
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

                //    isRegionLoading = false;

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


        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val() || 'A';
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val() || 'A';
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val() || 'A';
        var financialyear = $("#financialyear").val() || '';
        var maxslip = $("#maxslip").val()  || 5;
        var category = $("#category").val();

        var auditquarter = $("#auditquarter").val();
        if (!auditquarter || auditquarter.length === 0) {
            auditquarter = [];
        }

          var subcatcode = $("#subcategory").val() || 'A';


          var instmappingcode = $("#instmappingcode").val() || 'A';

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
            url: '/getMinSlipCount',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                category:category,
                subcatcode:subcatcode,
                maxslip: maxslip,
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
        updateTableLanguage(lang);

    });


    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const instColumn = language === 'ta' ? 'insttname' : 'instename';
        const desigColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const teammembername = language === 'ta' ? 'team_members_ta' : 'team_members_en';
        const catcodeColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcatcodeColumn = language === 'ta' ? 'subcategory_tname' : 'subcategory_ename';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        if ($.fn.DataTable.isDataTable('#schedulecountreport')) {
            $('#schedulecountreport').DataTable().clear().destroy();
        }

        table = $('#schedulecountreport').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#schedulecountreport").wrap(
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
                        title: "Department / Region / District",
                        render: function(data, type, row) {
                            const dept = row?.[departmentColumn] || '-';
                            const region = row?.[regionColumn] || '-';
                            const district = row?.[districtColumn] || '-';

                            return `
                                <div><b>Department :</b> ${dept}</div>
                                <div><b>Region :</b> ${region}</div>
                                <div><b>District :</b> ${district}</div>
                            `;
                        },
                        className: "text-start text-wrap"
                    },

                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Institution",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

                {
                data: null,
                title: columnLabels?.[catcodeColumn]?.[language] || "Category / SubCategory",
                render: function(data, type, row) {
                    const category = row?.[catcodeColumn] || '-';
                    const subcategory = row?.[subcatcodeColumn] || '-';
                    return `<strong>Category:</strong> ${category} <br> <strong>SubCategory:</strong> ${subcategory}`;
                },
                className: "text-start d-none d-md-table-cell extra-column text-wrap"
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
                    title: columnLabels?.[regionColumn]?.[language],
                    render: function(data, type, row) {
                        const designation = row?.[desigColumn] || '-';
                        return `${row.username} (${designation})`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },




                {
                    data: null,
                    title: columnLabels?.["teamname"]?.[language],
                    render: function(data, type, row) {
                        return `<b>Mobile Number:</b>${row.mobilenumber}<br><b>IFHRMS Number:</b>${row.ifhrmsno}<br><b>Email:</b>${row.email}`;

                        // return row[teamname] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                 {
                    data: "total",
                    title: columnLabels?.["total"]?.[language],
                    className: "text-wrap text-center",
                    render: function(data, type, row) {
                        return row.total || '-';
                    }
                },

                {
                    data: "pending",
                    title: columnLabels?.["pending"]?.[language],
                    className: "text-wrap text-center",
                    render: function(data, type, row) {
                        return row.pending || '-';
                    }
                },

                {
                    data: "converted",
                    title: columnLabels?.["converted"]?.[language],
                    className: "text-wrap text-center",
                    render: function(data, type, row) {
                        return row.converted || '-';
                    }
                },
                {
                    data: "dropped",
                    title: columnLabels?.["dropped"]?.[language],
                    className: "text-wrap text-center",
                    render: function(data, type, row) {
                        return row.dropped || '-';
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
        updatedatatable(language, "schedulecountreport");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#schedulecountreport')) {
            $('#schedulecountreport').DataTable().clear().destroy();
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

        catcodeColumn : language === 'ta' ? 'cattname' : 'catename',
        subcatcodeColumn : language === 'ta' ? 'subcategory_tname' : 'subcategory_ename',

        desigColumn : language === 'ta' ? 'desigtsname' : 'desigesname',
        teammembername : language === 'ta' ? 'team_members_ta' : 'team_members_en'
    };

    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["Region"] || "Region", key: "region" },
        { header: dtText["District"] || "District", key: "district" },
        { header: dtText["Institution"] || "Institution", key: "institution" },

        { header: dtText["Category"] || "Category", key: "category" },
        { header: dtText["Subcategory"] || "Subcategory", key: "subcategory" },
        { header: dtText["Financial Year"] || "Financial Year", key: "financialyear" },
        { header: dtText["Quarter type"] || "Quarter Type", key: "auditquartercode" },


        { header: dtText["username"] || "Auditor's Name", key: "username" },
        { header: dtText["Email"] || "Email", key: "email" },
        { header: dtText["MobieNumber"] || "Mobile Number", key: "mobile" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },
        { header: dtText["Count of Slips"] || "Count of Slips", key: "count" },

        { header: dtText["Pending"] || "Pending", key: "pending" },
        { header: dtText["Dropped"] || "Dropped", key: "dropped" },
        { header: dtText["Converted to para"] || "Converted to para", key: "converted" },

    ];

    let rawData = table.rows({ search: 'applied' }).data().toArray();
    let excelData = [];

    rawData.forEach(row => {
        try {
            let button = $(row[0]).find("button.toggle-row");
            let dataRow = button.attr("data-row");
            let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

            let user = rowData["username"] || "-";
            let desig = rowData[columnMap.desigColumn] || "-";

            excelData.push({
                department: rowData[columnMap.departmentColumn] || "-",
                region: rowData[columnMap.regionColumn] || "-",
                district: rowData[columnMap.districtColumn] || "-",
                institution: rowData[columnMap.instColumn] || "-",

                category: rowData[columnMap.catcodeColumn] || "-",
                subcategory: rowData[columnMap.subcatcodeColumn] || "-",
                financialyear: rowData["financialyear"] || "-",
                auditquartercode: rowData["planname"] || "-",

                username: `${user} ( ${desig} )`,
                email: rowData["email"] || "-",
                mobile: rowData["mobilenumber"] || "-",
                ifhrmsno: rowData["ifhrmsno"] || "-",
                count: rowData["total"] || "-",

                pending: rowData["pending"] || "-",
                dropped: rowData["dropped"] || "-",
                converted: rowData["converted"] || "-"

            });
        } catch (e) {}
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });

        // Merge across columns
        ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: headers.length - 1 } }];

        let colCount = headers.length;
        let totalWidth = colCount * 15;
        let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");
        XLSX.utils.sheet_add_aoa(ws, [[paddedTitle]], { origin: "A1" });
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A2" });

        XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A3" });

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
          //  getInstData(lang);
        } else if (sessionrole == regionrole) {
          //  getInstData(lang);
        } else if (sessionrole == headofficerole) {
          //  getInstData(lang);
        } else if (sessionrole == dgarole) {
          //  getInstData(lang);

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

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        var form = $('#auditslip_form');

            form[0].reset();


        // Reset validation messages
        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

        if (sessionrole == distrole) {
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#financialyear').val('').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            onchange_region('institution', 'instmappingcode')

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#financialyear').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#financialyear').val('').select2();
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');

        } else if (sessionrole == dgarole) {


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#deptcode').val('').select2();
            $('#financialyear').val('').select2();
           // getInstData(lang);


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }

    }
</script>


@endsection