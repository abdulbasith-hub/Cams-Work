@section('content')
@extends('index2')
@include('common.alert')
@php
    $sessionmainobjectiondel = session('charge');

    $sessionchargedel = session('charge');

    //print_r($sessionchargedel);

    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;
    $roleTypeCode = $sessionchargedel->roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

@endphp

<style>

html, body {
  overflow-x: hidden;
}


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


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Para Count</div>
            <div class="card-body">
                <form id="paraoverallcountform" name="paraoverallcountform">
                    @csrf
                    <div class="row">



                <div class="col-md-3 mb-2" id="deptdiv">
                    <label class="form-label required  lang" key="department" for="dept">Department</label>

                    <select class="form-select mr-sm-2 select2   lang-dropdown" multiple="multiple"  <?php echo $make_dept_disable; ?>  id="deptcode"   name="deptcode[]"
                        onchange="onchange_region('region', 'regioncode'); onchange_region('audityear','audityearcode')">

                        {{-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> --}}

                        <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


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
                            <option disabled>No Departments Available</option>
                        @endif

                    </select>
                </div>

                        <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?>   id="regioncode" name="regioncode[]"
                                onchange="onchange_region('district','distcode')" >

                                {{-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select a Region</option> --}}

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                                    @if($regioncode)
                                    @foreach ($region as $reg)
                                        <option value="{{ $reg->regioncode }}"
                                            @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                            data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                            {{ $reg->regionename }}
                                        </option>
                                        @endforeach
                                    @else

                                @endif

                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2" multiple="multiple" onchange="onchange_region('institution','instmappingcode')" <?php echo $make_dist_disable; ?>   id="distcode" name="distcode[]">



                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                                    @if($distcode)
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
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]" >



                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label required lang" for="audityearcode" key="audityearcode">Audit Year</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"
                                id="audityearcode" name="audityearcode[]">

                                {{-- <option value="" data-name-en="Select Audit Year" data-name-ta="தணிக்கை ஆண்டைத் தேர்ந்தெடுக்கவும்">
                                Select Audit Year
                            </option> --}}



                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            </select>
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

                    </div>

                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header  card_header_color lang" id="cardHeader" key="">Para Count</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="paraoverallcounttable"
                            class="table w-100 table-striped table-bordered display  align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department Details</th>
                                    <th class="lang align-middle text-center" key="audit_office">Institution</th>
                                    <th class="lang align-middle text-center" key="">Status</th>
                                    <th class="lang align-middle text-center" key="">Audit Year</th>
                                    <th class="lang align-middle text-center" key="">Para Number</th>



                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class=' lang text-center' key="no_data">
                    <center class="lang" key="no_data">No Data Available</center>

                </div>
            </div>
        </div>

    </div>
</div>




<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script>



let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    var sessionregioncode = '<?php echo $regioncode; ?>';
    var sessiondistcode = '<?php echo $distcode; ?>';




    $(document).ready(function () {

function applyAllLogic($select) {
    let selectedValues = $select.val() || [];

    if (selectedValues.includes('A')) {
        $select.val(['A']).trigger('change.select2');  // keep only A
        $select.find('option').not('[value="A"]').prop('disabled', true);
    } else {
        $select.find('option').prop('disabled', false);
    }
}

['#regioncode', '#distcode', '#deptcode', '#instmappingcode', '#audityearcode'].forEach(function (selector) {
    let $sel = $(selector);

    $sel.on('change', function () {
        applyAllLogic($sel);
    });

    // Apply logic on load
    setTimeout(function () {
        applyAllLogic($sel);
    }, 0);
});

});




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
        // return;
        $dropdown.select2('destroy')
        $dropdown.select2(null)
        $dropdown.select2()




        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();


        var instmappingcode = $("#instmappingcode").val();


         var lang = getLanguage();



         if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            //isRegionLoading = false;
            return;
        }

        if (deptcode.includes("A")) {
            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A" data-name-en="All" data-name-ta="All">All</option>`);
            dd.select2();

        return;
            }


        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode : distcode,
                instmappingcode: instmappingcode,
                valuefor: valuefor,
                formname: 'paraoverallcount'

            },
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

                    if (!audityearcode || audityearcode.length === 0) {
                    audityearcode = [''];
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

                            case 'audityear':

                            return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${audityearcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                            : `<option value="${item.auditperiodid}" data-name-en="${item.audit_period}" data-name-ta="${item.audit_period}" ${audityearcode.includes(item.auditperiodid) ? "selected" : ""}>${item.audit_period}</option>`;




                            default:
                                return '';
                        }
                    }).join('');

                    // Append options or show fallback message
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


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });

    }




    function getInstData(lang) {


        var lang = getLanguage();
        var deptcode = ($('#deptcode').val() && $('#deptcode').val().length > 0)
            ? $('#deptcode').val()
            : ['A'];

        var regioncode = ($('#regioncode').val() && $('#regioncode').val().length > 0)
            ? $('#regioncode').val()
            : ['A'];

        var distcode = ($('#distcode').val() && $('#distcode').val().length > 0)
            ? $('#distcode').val()
            : ['A'];

        var instmappingcode = ($('#instmappingcode').val() && $('#instmappingcode').val().length > 0)
            ? $('#instmappingcode').val()
            : ['A'];

        var audityearcode = $('#audityearcode').val() || 'A';



        $.ajax({
            url: '/paracount_fetchData', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
               instmappingcode: instmappingcode,
               audityearcode: audityearcode

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








    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';


    $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
    }, "Please select at least one option.");

    var validator = $("#paraoverallcountform").validate({
        ignore: [],
        rules: {
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            "instmappingcode[]": { selectRequired: true },
            "audityearcode[]": { selectRequired: true },


        },
        messages: {
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            "instmappingcode[]": "Select a Institution",
            "audityearcode[]": "Select a Audit Quarter",


        },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },

        });

    $('#buttonaction').on('click', function (e) {
        e.preventDefault();
        var form = $('#paraoverallcountform');



    if (form.valid()) {
       // updateCardHeader();
        getInstData(lang);
    } else {
        form.validate().focusInvalid();
    }
    });
    reset_form();

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});





    $('#translate').change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);

        updateValidationMessages(getLanguage('Y'), 'paraoverallcountform');
    });




    function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}



    function renderTable(language) {

        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
         const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

         const RegionColumn = language === 'ta' ? 'regiontname' : 'regionename';
         const InstitutionColumn = language === 'ta' ? 'insttname' : 'instename';




        if ($.fn.DataTable.isDataTable('#paraoverallcounttable')) {
            $('#paraoverallcounttable').DataTable().clear().destroy();
        }

        table = $('#paraoverallcounttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                data: null,
                render: function(data, type, row, meta) {
                    const safeRow = escapeHtml(JSON.stringify(row));
                    return `<div>
                        <button class="toggle-row d-md-none" data-row="${safeRow}">▶</button>${meta.row + 1}
                    </div>`;
                },
                className: 'text-start',
                type: "num"
                },
                {
                    data: null,
                    title: "Department Details",
                    render: function(data, type, row) {
                        let dept = row[departmentColumn] || '-';
                        let region = row[RegionColumn] || '-';
                        let district = row[DistrictColumn] || '-';

                        return `<strong>Department:</strong> ${dept}<br>
                    <strong>Region:</strong> ${region}<br>
                    <strong>District:</strong> ${district}`;
                    },
                    className: 'text-wrap text-start'
                },

                {
                    data: InstitutionColumn,
                    title: columnLabels?.[InstitutionColumn]?.[language],
                    render: function(data, type, row) {
                        return row[InstitutionColumn] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap  wrap-50'
                },

                {
                    data: "processelname",
                    title: columnLabels?.["processelname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-center text-wrap  wrap-50",
                    render: function(data, type, row) {
                        return row.processelname || '-';
                    }
                },


                {
                    data: "audit_year_range",
                    title: columnLabels?.["audit_year_range"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-center text-wrap  wrap-50",
                    render: function(data, type, row) {
                        return row.audit_year_range || '-';
                    }
                },

                {
                    data: "paranumber",
                    title: columnLabels?.["paranumber"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-center text-wrap  wrap-50",
                    render: function(data, type, row) {
                        return row.paranumber || '-';
                    }
                },






            ],

            "initComplete": function(settings, json) {
                $("#paraoverallcounttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [RegionColumn,DistrictColumn,InstitutionColumn,"auditquartercode","audit_period","createdon","updatedon","entrymeetdate","exitmeetdate","processcode"];

        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "paraoverallcounttable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#paraoverallcounttable')) {
            $('#paraoverallcounttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }







        $(document).ready(function () {
            var sessionrole = '<?php echo $sessionroletypecode; ?>';
            var dgarole = '<?php echo $dga_roletypecode; ?>';
            var distrole = '<?php echo $Dist_roletypecode; ?>';
            var regionrole = '<?php echo $Re_roletypecode; ?>';
            var headofficerole = '<?php echo $Ho_roletypecode; ?>';
            var adminrole = '<?php echo $Admin_roletypecode; ?>';
            var lang = getLanguage();

            var deptcode = $("#deptcode").val();
            var region = $("#regioncode").val();
            var district = $("#distcode").val();

            var instmappingcode = $("#instmappingcode").val();

            if (!deptcode || !region || !district) {
                deptcode = deptcode || '';
                region = region || '';
                district = district || '';
            }

            if (sessionrole == distrole ) {
                onchange_region('institution', 'instmappingcode');

            }
            else if(sessionrole == dgarole){
               // getInstData(lang);

            }
            else if(sessionrole == regionrole){
                onchange_region('district', 'distcode');
            }
            else if(sessionrole == headofficerole)
            {
                onchange_region('region', 'regioncode');
            }
     });







     function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


    const columnMap = {
          departmentColumn : language === 'ta' ? 'deptesname' : 'deptesname',
          DistrictColumn : language === 'ta' ? 'disttname' : 'distename',

          RegionColumn : language === 'ta' ? 'regiontname' : 'regionename',
          InstitutionColumn : language === 'ta' ? 'insttname' : 'instename',


    };

    // ? Define Headers Properly
    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["region"] || "Region", key: "region" },
        { header: dtText["district"] || "District", key: "district" },
        { header: dtText["institution"] || "Auditable Institution", key: "audit_office" },
        { header: dtText["processelname"] || "Status", key: "processelname" },
        { header: dtText["audit_year_range"] || "Audit Year", key: "audit_year_range" },
        { header: dtText["paranumber"] || "Total Para", key: "paranumber" },




    ];

    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");

        let rowData = {};

            try {
                rowData = dataRow ? JSON.parse(dataRow) : {};
            } catch (e) {
                console.error("Invalid JSON in data-row:", dataRow);
            }


        return {
            department: rowData[columnMap.departmentColumn] || "-",
            region: rowData[columnMap.RegionColumn] || "-",
            district: rowData[columnMap.DistrictColumn] || "-",
            institution: rowData[columnMap.InstitutionColumn] || "-",
            processelname: rowData.processelname || "-",
            audit_year_range: rowData.audit_year_range || "-",
            paranumber: rowData.paranumber || "-",



        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });

    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}






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

        $('#deptcode option').prop('disabled', false);


        var form = $('#paraoverallcountform');

        form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }

        $('#instmappingcode').val('').select2();
        $('#audityearcode').val(['']).select2();


        $('#paraoverallcountform')[0].reset();
        if (sessionrole == distrole) {
            $('#instmappingcode').val('').select2();

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#distcode').val('').select2();

        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();

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
