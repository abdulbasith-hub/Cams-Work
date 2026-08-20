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
    max-width: 400px; /* Adjust as needed */
}

.select2-container .select2-selection {
    border: 1px solid #dfe3eb !important;
}

#epacsReportTable_paginate .paginate_button {
    background-color: var(--bs-primary) !important;
    color: #fff !important;
    border: 1px solid var(--bs-primary) !important;
    padding: 6px 14px !important;
    margin: 0 6px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    font-size :12px !important;
}



#epacsReportTable_paginate .paginate_button.current {
    background-color: var(--bs-primary) !important;
    border-color: var(--bs-primary) !important;
    font-weight: bold !important;
    box-shadow: 0 0 5px rgba(13,110,253,0.6) !important;
}


</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">ERP Details</div>
            <div class="card-body">
                <form id="epacsdetailsform" name="epacsdetailsform">
                    @csrf
                    <div class="row">




                <div class="col-md-3 mb-2" id="deptdiv">
                    <label class="form-label required  lang" key="department" for="dept">Department</label>

                    <select class="form-select mr-sm-2 select2  lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?>  id="deptcode"   name="deptcode[]"
                        onchange="onchange_region('region', 'regioncode');">

                        {{-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> --}}

                        <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            @if (!empty($epacdept) && count($epacdept) > 0)
                            @foreach ($epacdept as $department)
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
                                <select class="form-select mr-sm-2 select2" multiple="multiple"  <?php echo $make_dist_disable; ?>   id="distcode" name="distcode[]">


                                {{-- <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option> --}}

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
            <div class="card-header  card_header_color lang" id="cardHeader" key="">ERP Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="epacsdetailstable"
                            class="table w-100 table-striped table-bordered display  align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department Details</th>
                                    <th class="lang align-middle text-center" key="">No. of Institutes Allocated in Audit Plan</th>
                                    <th class="lang align-middle text-center" key="">Under Process</th>
                                    {{-- <th class="lang align-middle text-center" key="">Ongoing</th> --}}
                                    <th class="lang align-middle text-center" key="">Completed</th>
                                    <th class="lang align-middle text-center" key="">Pending</th>



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


            <div id="epacsTableContainer" class="mt-4" style="display:none;">
                        <div class="card card_border">
                            <div class="card-header card_header_color position-relative text-center">
                                <span id="epacsTableTitle" class="fw-bold">ERP Details</span>

                                <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-2"
                                    aria-label="Close" id="closeRegionTable"></button>
                            </div>

                            <div class="card-body">
                                <table id="epacsReportTable" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic text-center">
                                    <thead>
                                        <tr>
                                            <th> S.No </th>
                                            <th> Institution </th>
                                            <th> Audit Year </th>
                                            <th> Entry Meeting date </th>
                                            <th> Exit Meeting date </th>


                                        </tr>
                                    </thead>
                                    <tbody></tbody>

                                </table>
                            </div>
                        </div>
                    </div>


    </div>
</div>



<script src="../assets/js/jquery.js"></script>

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

$(document).on('click', '#closeRegionTable', function () {

    $('#epacsTableContainer').hide();

});

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

['#regioncode', '#distcode', '#deptcode'].forEach(function (selector) {
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
                valuefor: valuefor,
                formname: 'epacsdetails'

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


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });

    }




    function getInstData(lang) {



        var deptcode =  $('#deptcode').val();
        var regioncode =  $('#regioncode').val();
        var distcode = $('#distcode').val();






        var deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0
        ? $('#deptcode').val()
        : ['A'];

        var regioncode = $('#regioncode').val() && $('#regioncode').val().length > 0
            ? $('#regioncode').val()
            : ['A'];

        var distcode = $('#distcode').val() && $('#distcode').val().length > 0
            ? $('#distcode').val()
            : ['A'];





        $.ajax({
            url: '/getepacsdetails',
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

    var validator = $("#epacsdetailsform").validate({
        ignore: [],
        rules: {
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },

        },
        messages: {
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",

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
        var form = $('#epacsdetailsform');



    if (form.valid()) {
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
    });




    function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


function showRegionTable(regionData) {

    $('#epacsTableContainer').show();

    if ($.fn.DataTable.isDataTable('#epacsReportTable')) {
        $('#epacsReportTable').DataTable().clear().destroy();
    }

    $('#epacsReportTable').DataTable({
        data: regionData,

        pageLength: 10,
        lengthChange: false,
        autoWidth: false,
        searching: true,
        paging: true,
        info: true,

        // Download buttons
        dom: 'Bfrtip',

        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Download',
                title: 'EPACS Report',
                className: 'btn btn-success btn-sm'
            }
        ],

        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: 'text-center',
                title: 'S.No'
            },

              {
                data: 'instename',
                render: function (data) {
                    return data || '-';
                },
                className: 'text-start text-wrap',
                title: 'Institution'
            },

            {
                data: 'auditperiod',
                render: function (data) {
                    return data || '-';
                },
                className: 'text-start text-wrap',
            },

            {
                data: 'entrymeetdate',
                render: function (data) {
                    return data || '-';
                },
                className: 'text-start text-wrap',
                title: 'Entry Meeting Date'
            },

            {
                data: 'exitmeetdate',
                render: function (data) {
                    return data || '-';
                },
                className: 'text-start text-wrap',
                title: 'Exit Meeting Date'
            }
        ]
    });
}



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#epacsdetailstable')) {
            $('#epacsdetailstable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




$(document).on('click', '.show-details', function () {
    const deptcode = $(this).data('dept');
    const regioncode = $(this).data('region');
    const distcode = $(this).data('district');
    const type = $(this).data('type');

  let title = '';

    if (type === 'P') {
        title = 'Total No. of Institutions Planned';
    } else if (type === 'NS') {
        title = 'Pending Institutions';
    } else if (type === 'S') {
        title = 'Institution Under Process';
    } else if (type === 'E') {
        title = 'Completed Institutions';
    }

    $('#epacsTableTitle').text(title);


    $.ajax({
        url: '/fetch_epcsdetails',
        type: 'POST',
        data: {
            deptcode: deptcode,
            regioncode: regioncode,
            distcode: distcode,
            type: type,

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status === 'success' && response.data.length > 0) {
                showRegionTable(response.data);
            } else {
                passing_alert_value('Info', 'No region data found', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
        },
        error: function () {
            passing_alert_value('Error', 'Failed to fetch region data', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
        }
    });
});





    function renderTable(language) {

        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
         const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

         const RegionColumn = language === 'ta' ? 'regiontname' : 'regionename';
         const InstitutionColumn = language === 'ta' ? 'insttname' : 'instename';
        // const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

       //  const designationColumn = language === 'ta' ? 'desigtlname' : 'desigelname';
            $('#epacsTableContainer').hide();

        if ($.fn.DataTable.isDataTable('#epacsdetailstable')) {
            $('#epacsdetailstable').DataTable().clear().destroy();
        }

        table = $('#epacsdetailstable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            "initComplete": function(settings, json) {
                $("#epacsdetailstable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            columns: [{
                data: null,
                render: function(data, type, row, meta) {
                    const safeRow = escapeHtml(JSON.stringify(row));
                    return `<div>
                        <button class="toggle-row d-md-none" data-row="${safeRow}">▶</button>${meta.row + 1}
                    </div>`;
                },
                className: 'text-end',
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
                    className: 'text-start'
                },



            {
                data: "total_planned",
                title: columnLabels?.["total_planned"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-end text-wrap",
                render: function(data, type, row) {

                    return `
                        <span
                            class="badge bg-primary show-details cursor-pointer"
                            role="button"
                            data-type="P"
                            data-dept="${row.deptcode}"
                            data-region="${row.regioncode}"
                             data-district="${row.distcode}">
                            ${row.total_planned || 0}
                        </span>
                    `;
                }
            },



            {
                data: "scheduled_count",
                title: columnLabels?.["scheduled_count"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-end text-wrap",
                render: function(data, type, row) {

                    return `
                        <span
                            class="badge bg-warning show-details cursor-pointer"
                            role="button"
                            data-type="S"
                            data-dept="${row.deptcode}"
                            data-region="${row.regioncode}"
                            data-district="${row.distcode}">
                            ${row.scheduled_count || 0}
                        </span>
                    `;
                }
            },

            // {
            //     data: "entry_meeting_count",
            //     title: columnLabels?.["entry_meeting_count"]?.[language],
            //     className: "d-none d-md-table-cell lang text-end extra-column text-wrap",
            //     render: function(data, type, row) {

            //         return `
            //             <span
            //                 class="badge bg-warning text-dark show-details cursor-pointer"
            //                 role="button"
            //                 data-type="N"
            //                 data-dept="${row.deptcode}"
            //                 data-region="${row.regioncode}"
            //                 data-district="${row.distcode}">
            //                 ${row.entry_meeting_count || 0}
            //             </span>
            //         `;
            //     }
            // },

              {
                data: "exit_meeting_count",
                title: columnLabels?.["exit_meeting_count"]?.[language],
                className: "d-none d-md-table-cell lang text-end extra-column text-wrap",
                render: function(data, type, row) {

                    return `
                        <span
                            class="badge bg-success show-details cursor-pointer"
                            role="button"
                            data-type="E"
                            data-dept="${row.deptcode}"
                            data-region="${row.regioncode}"
                            data-district="${row.distcode}">
                            ${row.exit_meeting_count || 0}
                        </span>
                    `;
                }
            },

             {
                data: "not_scheduled_count",
                title: columnLabels?.["not_scheduled_count"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-end text-wrap",
                render: function(data, type, row) {

                    return `
                        <span
                            class="badge bg-danger show-details cursor-pointer"
                            role="button"
                            data-type="NS"
                            data-dept="${row.deptcode}"
                            data-region="${row.regioncode}"
                             data-district="${row.distcode}">
                            ${row.not_scheduled_count || 0}
                        </span>
                    `;
                }
            },





            ],



        });

        const mobileColumns = [RegionColumn,DistrictColumn,InstitutionColumn,"auditquartercode","audit_period","createdon","updatedon","entrymeetdate","exitmeetdate","processcode"];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language ="en", "epacsdetailstable");
    }




    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Report";
    let safeSheetName = translatedTitle.substring(0, 31);

    const dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    // Column headings for Excel (all separate columns)
    let headers = [
        { header: dtText["department"] || "Department", key: "dept" },
        { header: dtText["region"] || "Region", key: "region" },
        { header: dtText["district"] || "District", key: "district" },

        { header: "Total Planned", key: "total_planned" },
        { header: "Pending", key: "not_scheduled" },
        { header: "Under Process", key: "scheduled" },
        { header: "Exit Meeting Completed", key: "exit" },


    ];

    // Extract raw rows
    let rawData = table.rows({ search: "applied" }).data().toArray();
    let excelData = [];

    rawData.forEach(row => {
        let btn = $(row[0]).find("button.toggle-row");
        let json = btn.attr("data-row");

        let r = {};
        try {
            r = JSON.parse(json);
        } catch (e) {
            r = {};
        }

        // --- Department details ---



        excelData.push({
            dept: r.deptesname || "-",
            region: r.regionename || "-",
            district: r.distename || "-",

            total_planned: r.total_planned || 0,
            not_scheduled: r.not_scheduled_count || 0,
            scheduled: r.scheduled_count || 0,
            exit_completed: r.exit_meeting_count || 0
        });
    });

    if (excelData.length === 0) {
        alert("No data to export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
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
    $('#epacsTableContainer').hide();

        var form = $('#epacsdetailsform');

        form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }



        $('#epacsdetailsform')[0].reset();
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
            $('#deptcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();


        }

    }



</script>


@endsection
