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
  .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* Adjust as needed */
    }

    .select2-container .select2-selection {
    border: 1px solid #dfe3eb !important;
}




</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">




<div class="modal fade" id="slipDetailsModal" tabindex="-1" aria-labelledby="slipDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center position-relative">
                <h5 class="modal-title fw-bold text-center flex-grow-1" id="slipDetailsModalLabel" style="font-size: 1.25rem;">
                 Money and Non-Money Value Slip Details
                </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="modalInstitutionName"
                class="text-center mb-3 fw-bold"
                style="font-weight: 300; font-size: 1.5rem; color: #333;">
            </h4>
                <div class="d-flex justify-content-center">
                    <div style="width:80%;">
                        <table class="table table-bordered" id="slipDetailsTable">
                            <tbody>
                                <!-- Rows will be dynamically added -->
                            </tbody>
                        </table>

                        <!-- Remarks Section -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Remarks</h6>
                            <textarea id="remarksEditor" style="width:100%; height:120px;"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Money and Non-Money Details</div>
        <div class="card-body">
            <form id="moneyandnonmoneyform" name="moneyandnonmoneyform" method='post'>
            @csrf
                <div class="row">

                    <input type="hidden" value="" id="if_subcategory" name="if_subcategory">

                <div class="col-md-3">
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



                    <div class="col-md-3">
                    <label class="form-label required lang" for="deptcode" key="department">Department</label>
                    <select
                        class="form-select mr-sm-2 select2 lang-dropdown"
                        multiple="multiple"
                        id="deptcode"
                        name="deptcode[]"  <?php echo $make_dept_disable; ?>
                        >

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






                    <div class="col-md-3">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode[]"  >


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






                    <div class="col-md-3">
                        <label class="form-label required lang" for="distcode" key="District">District</label>
                        <select class=" mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]" >

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



                    <div class="col-md-3 mb-3">
                              <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                            >

                            </select>
                        </div>


                        <div class="col-md-3  subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                            <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                                name="subcategory[]">



                                {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option> --}}

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label required lang" for="auditquarter" key="auditquarter">Audit Quarter</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"

                                id="auditquarter" name="auditquarter[]">
                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            </select>
                        </div>



                    <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]"  >

                                {{-- <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option> --}}

                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label required lang" key=""
                            for="moneyandnonmoney">Select a Status</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown"  id="moneyandnonmoney" name="moneyandnonmoney"  >

                            <option value="" data-name-en="Select a Status" data-name-ta="Select a Status">Select a Status</option>

                            <option value="01" data-name-en="Money Value" data-name-ta="">Money Value</option>
                            <option value="02" data-name-en="Non Money Value" data-name-ta=""> Non Money Value</option>

                        </select>
            </div>



                    <div id="loader">
                        <div class="spinner"></div>
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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">Money and Non-Money Slip Details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="moneyandnonmoneytable"
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
                                <th class="lang align-middle text-center" key="">Dropped</th>


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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


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


$("#category").on("change", function() {
    onchange_region('subcatcode', 'subcategory');
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

    var validator = $("#moneyandnonmoneyform").validate({
        ignore: [],
        rules: {
           financialyear: { required: true },
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            "category[]": { selectRequired: true },
            "instmappingcode[]": { selectRequired: true },
            "auditquarter[]": { selectRequired: true },
            moneyandnonmoney : {required : true}
        },
        messages: {
            financialyear: "Select a Financial Year",
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            "category[]": "Select a Category",
            "instmappingcode[]": "Select a Institution",
            "auditquarter[]": "Select Audit Quarter",
            moneyandnonmoney : "Select a Status"

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
        var form = $('#moneyandnonmoneyform');


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






$('#category').on('change', function () {

    var deptcode = $('#deptcode').val(); // ← array


    if (deptcode && (deptcode.includes("01") || deptcode.includes("05"))) {
        $('.subcatdiv').hide();
    } else {
        $('.subcatdiv').show();
    }

});







    let dataFromServer;

    // let isRegionLoading = false;

    function onchange_region(valuefor, valueforid) {
       // if (isRegionLoading) return;
        //isRegionLoading = true;



        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()



        var deptcode =  $('#deptcode').val();
        var regioncode = $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode =  $('#distcode').val();
        var financialyear =  $('#financialyear').val();



        var catcode = $("#category").val();
        var subcatcode = $("#subcategory").val();

        var instmappingcode = $("#instmappingcode").val();

         var lang = getLanguage();

         if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }

        const deptList = Array.isArray(deptcode) ? deptcode : (deptcode ? [deptcode] : []);
        const regionList = Array.isArray(regioncode) ? regioncode : (regioncode ? [regioncode] : []);
        const distList = Array.isArray(distcode) ? distcode : (distcode ? [distcode] : []);
        const catList = Array.isArray(catcode) ? catcode : (catcode ? [catcode] : []);
        const subcatList = Array.isArray(subcatcode) ? subcatcode : (subcatcode ? [subcatcode] : []);

        // ✅ Condition 1: Any "A" → apply for all dropdowns
        if (
            deptList.includes("A") ||
            regionList.includes("A") ||
            distList.includes("A") ||
            catList.includes("A") ||
            subcatList.includes("A")
        ) {
            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A">All</option>`);
            dd.select2();
            return;
        }

        // ✅ Condition 2: Multiple dept → ONLY for auditquarter
        if (valueforid === 'auditquarter' && deptList.length > 1) {
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
        var category = $("#category").val();

        var auditquarter = $("#auditquarter").val();
        var moneyandnonmoney = $("#moneyandnonmoney").val();
        var financialyear = $("#financialyear").val();


          var subcatcode = $("#subcategory").val() || 'A';


          var instmappingcode = $("#instmappingcode").val() || 'A';

          if (!Array.isArray(instmappingcode) || instmappingcode.length === 0) {
            instmappingcode = [0];
        }

        if (!auditquarter || auditquarter.includes('A')) {
            auditquarter = [0];  // means ALL
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
            url: '/getmoneyandnonmoneyparadetails',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                auditquarter : auditquarter,
                financialyear : financialyear,
                moneyandnonmoney :moneyandnonmoney,
                category:category,
                subcatcode:subcatcode,
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

                    renderTable(response.data);
                } else {

                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function(xhr, status, error) {
                $("#loader").hide();
                var response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    response.message = xhr.responseText; // fallback
                }
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






$(document).on('click', '.show-allparadeptails', function () {
    const auditslipid = $(this).data('auditslipid');

    $.ajax({
        url: '/fetch_allmoneyandnondeptails',
        type: 'POST',
        data: {auditslipid:auditslipid },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                const slip = response.data[0];
                $('#modalInstitutionName').text(slip.instename || '-');

                const $tbody = $('#slipDetailsTable tbody');
                $tbody.empty();

                $tbody.append(`
            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Slip Number</strong></td><td style="border:1px solid #999;">${slip.mainslipnumber || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Main Objection</strong></td><td style="border:1px solid #999;">${slip.objectionename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Sub Objection</strong></td><td style="border:1px solid #999;">${slip.subobjectionename || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Amount Involved</strong></td><td style="border:1px solid #999;">${slip.amtinvolved || '-'}</td></tr>
                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Severity</strong>
                    </td>
                    <td style="border:1px solid #999;">
                        ${slip.severitycode === 'H' ? 'High' : slip.severitycode === 'M' ? 'Medium' : slip.severitycode === 'L' ? 'Low' : '-'}
                    </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                            <strong>Schema Status</strong>
                        </td>
                        <td style="border:1px solid #999;">
                            ${slip.schemastatus === 'Y' ? 'Yes' : slip.schemastatus === 'N' ? 'No' : '-'}
                        </td>
                    </tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Auditee Scheme</strong></td><td style="border:1px solid #999;">${slip.auditeeschemeelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities</strong></td><td style="border:1px solid #999;">${slip.irregularitieselname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Category</strong></td><td style="border:1px solid #999;">${slip.irregularitiescatelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Subcategory</strong></td><td style="border:1px solid #999;">${slip.irregularitiessubcatelname || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Slip Details</strong></td><td style="border:1px solid #999;">${slip.slipdetails || '-'}</td></tr>


                  <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Auditor File Upload</strong>
                    </td>
                    <td>
                        ${
                        slip.auditorfileupload
                            ? slip.auditorfileupload
                                .split(',')
                                .map(file => {
                                const parts = file.split('-');
                                const fileName = parts[0] || 'Unnamed file';
                                const filePath = parts[1]
                                    ? `/storage/${parts[1].replace(/\\/g, '/')}`
                                    : '#';
                                return `<a href="${filePath}" target="_blank" style="text-decoration:none; color:#007bff;">${fileName}</a>`;
                                })
                                .join('<br>')
                            : '-'
                        }
                    </td>
                    </tr>

                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Audit Year</strong></td><td style="border:1px solid #999;">${slip.audit_period || '-'}</td></tr>
                    <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Created By</strong></td><td style="border:1px solid #999;">${slip.createdbyusername || '-'}</td></tr>

                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Slip Status</strong>
                    </td>
                    <td style="border:1px solid #999;">
                        ${
                        slip.processcode === 'X'
                            ? 'Converted to Para'
                            : slip.processcode === 'A'
                            ? 'Dropped'
                            : (slip.processcode || '-')
                        }
                    </td>
                    </tr>



                    <tr>
                    <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                        <strong>Updated On</strong>
                    </td>
                    <td style="border:1px solid #999;">
                        ${
                        slip.updatedon
                            ? (() => {
                                const d = new Date(slip.updatedon);
                                const day = String(d.getDate()).padStart(2, '0');
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                const year = d.getFullYear();
                                return `${day}-${month}-${year}`;
                            })()
                            : '-'
                        }
                    </td>
                    </tr>



                          <tr>
                        <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                            <strong>Liability</strong>
                        </td>
                        <td style="border:1px solid #999;">${slip.liability === 'Y' ? 'Yes' : slip.liability === 'N' ? 'No' : '-'}</td>
                        </tr>


                 ${slip.liability === 'Y' ? `
                        <tr>
                        <td colspan="2" style="padding:0; border:none;">
                            <table class="table table-bordered mt-2 mb-2" style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr style="text-align:center;">
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Type</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Number</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Name</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Designation</th>
                                <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                slip.liabilitydel
                                    ? slip.liabilitydel.split(',').map(line => {
                                        const parts = line.trim().split('-');
                                        return `
                                        <tr>
                                            <td class="text-center" style="border:1px solid #dee2e6;">
                                            ${
                                                parts[0] === '01'
                                                ? 'EPF No'
                                                : parts[0] === '02'
                                                ? 'CPS No'
                                                : parts[0] === '03'
                                                ? 'IFHRMS No'
                                                : (parts[0] || '-')
                                            }
                                            </td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[1] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[2] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6;">${parts[3] || '-'}</td>
                                            <td class="text-center" style="border:1px solid #dee2e6; text-align:right;">${parts[4] || '-'}</td>
                                        </tr>`;
                                    }).join('')
                                    : `<tr><td colspan="5" style="text-align:center; border:1px solid #dee2e6;">No liability records available</td></tr>`
                                }
                            </tbody>
                            </table>
                        </td>
                        </tr>
                        ` : ''}



                `);

                let remarksContent = '';

                    try {
                        if (slip.remarks) {
                            const parsed = JSON.parse(slip.remarks);
                            remarksContent = parsed.content || '';
                        }
                    } catch (e) {
                        // Not JSON, use raw text
                        remarksContent = slip.remarks;
                    }

                    loadckeditorauditor(remarksContent, 'remarksEditor');



                var modal = new bootstrap.Modal(document.getElementById('slipDetailsModal'));
                modal.show();
            } else {
                alert('No institution data found');
            }
        },
        error: function (xhr) {
            console.error('Error fetching institution data:', xhr.responseText);
        }
    });
});



function loadckeditorauditor(auditorreply, textareaId) {
    let viewslip_auditorremarks;

    // Destroy the existing CKEditor instance if it exists
    if (window[textareaId] && typeof window[textareaId].destroy === 'function') {
        window[textareaId].destroy();
    }

    const editorElement = document.getElementById(textareaId);
    if (editorElement) {
        CKEDITOR.ClassicEditor.create(editorElement, {
            toolbar: {
                items: [
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'numberedList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    'fontSize', 'fontFamily', '|',
                    'alignment', '|',
                    'uploadImage', 'insertTable', '|',
                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'General Remarks',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                'MultiLevelList', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        }).then(editor => {
            viewslip_auditorremarks = editor;
            window[textareaId] = editor;
            editor.setData(auditorreply);
            window[textareaId].enableReadOnlyMode('initial');
        }).catch(error => {
            console.error("CKEditor Initialization Error:", error);
        });
    } else {
        console.error("Editor element not found:", textareaId);
    }
}



    function renderTable(dataFromServer,language) {
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

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#moneyandnonmoneytable')) {
            $('#moneyandnonmoneytable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#moneyandnonmoneytable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#moneyandnonmoneytable").wrap(
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
                    title: columnLabels?.['planname']?.[language],
                    render: function(data, type, row) {
                        return row?.planname || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
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
            data: null,
            title: "Para Details",
            render: function (data, type, row) {
                return `
                    <button type="button"
                        class="btn btn-sm btn-primary show-allparadeptails"
                        data-auditslipid="${row.auditslipid}">
                        View Details
                    </button>
                `;
            },
            className: "text-center align-middle"
        }




            ]
        });



        const mobileColumns = [
            departmentColumn, instColumn, regionColumn,
            districtColumn,"count", "teamname",
            "teammembername", "entrymeetdate", "exitmeetdate","fromdate","todate"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable("en", "moneyandnonmoneytable");

        $('#moneyandnonmoneytable').off('click').on('click', function () {
    });
    }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#moneyandnonmoneytable')) {
            $('#moneyandnonmoneytable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Audit Total Para Report";

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
        { header: dtText["Quarter type"] || "Audit Quarter", key: "auditquartercode" },


        { header: dtText["username"] || "Auditor's Name", key: "username" },
        { header: dtText["Email"] || "Email", key: "email" },
        { header: dtText["MobieNumber"] || "Mobile Number", key: "mobile" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },

        { header: dtText["Converted to para"] || "Total para", key: "converted" },

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
                auditquartercode: rowData["planname"] || "-",

                username: `${user} ( ${desig} )`,
                email: rowData["email"] || "-",
                mobile: rowData["mobilenumber"] || "-",
                ifhrmsno: rowData["ifhrmsno"] || "-",

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
            onchange_region('institution', 'instmappingcode')
        } else if (sessionrole == regionrole) {
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');

     } else if (sessionrole == dgarole) {
          //  getInstData(lang);

        } else if (sessionrole == adminrole) {
          // getInstData(lang);
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
        $('.subcatdiv').show();
        $("#loader").hide();

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();
        $('#financialyear').val('').select2();

        var form = $('#moneyandnonmoneyform');

            form[0].reset();

        $('#auditquarter').val('').select2();
        $('#financialyear').val('').select2();
        $('#moneyandnonmoney').val('').select2();
        $('#instmappingcode').val('').select2();

        // Reset validation messages
        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

        if (sessionrole == distrole) {
            $('#category').val('').select2();
            $('#subcategory').val('').select2();

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();


        } else if (sessionrole == dgarole) {


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();

            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();


        }

    }
</script>


@endsection