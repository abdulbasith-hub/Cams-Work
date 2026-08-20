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
.idle-dates-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(130px,1fr));
    gap:12px;
}

.idle-date-chip{
    background:#f8f9fa;
    border:1px solid #dee2e6;
    border-radius:8px;
    padding:10px 12px;
    text-align:center;
    font-size:14px;
    font-weight:500;
    white-space:nowrap;
    transition:.2s;
}

.idle-date-chip:hover{
    background:#eef4ff;
}
</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="modal fade"
     id="idleDatesModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content rounded-3 shadow">

            <div class="modal-header bg-light">

                <h5 class="modal-title fw-semibold">
                    Idle Dates Details
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <!-- Summary -->
                <div class="mb-3">
                    <span class="badge bg-primary fs-6">
                        Total Dates: <span id="idleCount">0</span>
                    </span>
                </div>

                <!-- Chips container -->
              <div id="idleDatesBody"
                class="idle-dates-grid">
            </div>

                <div id="noIdleDates"
                     class="text-center text-muted d-none">
                    No Idle Dates Found
                </div>

            </div>

        </div>

    </div>

</div>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Idle Auditors</div>
            <div class="card-body">
                <form id="idleauditorsform" name="idleauditorsform">
                    @csrf
                    <div class="row">




                <div class="col-md-3 mb-2" id="deptdiv">
                    <label class="form-label required  lang" key="department" for="dept">Department</label>

                    <select class="form-select mr-sm-2 select2  lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?>  id="deptcode"   name="deptcode[]"
                        onchange="onchange_region('region', 'regioncode');">

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
            <div class="card-header  card_header_color lang" id="cardHeader" key="">Idle Auditor Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="idleauditorstable"
                            class="table w-100 table-striped table-bordered display  align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department Details</th>
                                    <th class="lang align-middle text-center" key="">User Details</th>
                                    <th class="lang align-middle text-center" key="">Completed Institution Details</th>
                                    <th class="lang align-middle text-center" key="">Status</th>
                                    <th class="lang align-middle text-center" key="">Next Schedule Details</th>
                                    <th class="lang align-middle text-center" key="">idle days</th>


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



let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    var sessionregioncode = '<?php echo $regioncode; ?>';
    var sessiondistcode = '<?php echo $distcode; ?>';







    $(document).ready(function () {

function applyAllLogic($select) {
    let selectedValues = $select.val() || [];

    if (selectedValues.includes('A')) {
        $select.val(['A']).trigger('change.select2');
        $select.find('option').not('[value="A"]').prop('disabled', true);
    } else {
        $select.find('option').prop('disabled', false);
    }
}

['#regioncode', '#distcode', '#deptcode'].forEach(function (selector) {
    let $sel = $(selector);

    $sel.on('select2:opening', function () {
        $sel.find('option').prop('disabled', false);
    });

    $sel.on('change', function () {
        applyAllLogic($sel);
    });

    setTimeout(function () {
        if (!$sel.val() || $sel.val().length === 0) {
            $sel.val(['A']).trigger('change.select2');
        }
        applyAllLogic($sel);
    }, 0);
});

});


$(document).on('click', '.viewIdleDates', function () {

    let dates = $(this).data('idledates');
    let container = $('#idleDatesBody');

    container.empty();

    if (dates) {

        let arr = dates
            .split(',')
            .map(d => d.trim())
            .filter(Boolean);

        $('#idleCount').text(arr.length);

        $('#noIdleDates')
            .addClass('d-none');

        arr.forEach(date => {

            container.append(`
                <div class="idle-date-chip">
                    📅 ${date}
                </div>
            `);

        });

    } else {

        $('#idleCount').text(0);

        $('#noIdleDates')
            .removeClass('d-none');

    }

    $('#idleDatesModal').modal('show');

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
                formname: 'idlereport'

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
            url: '/getidlereportdetails', // For creating a new user or updating an existing one
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

    var validator = $("#idleauditorsform").validate({
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
        var form = $('#idleauditorsform');



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

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#idleauditorstable')) {
            $('#idleauditorstable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
         const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

         const RegionColumn = language === 'ta' ? 'regiontname' : 'regionename';
         const InstitutionColumn = language === 'ta' ? 'insttname' : 'instename';
        // const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

       //  const designationColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if ($.fn.DataTable.isDataTable('#idleauditorstable')) {
            $('#idleauditorstable').DataTable().clear().destroy();
        }

        table = $('#idleauditorstable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            "initComplete": function(settings, json) {
                $("#idleauditorstable").wrap(
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
                    data: "userdetails",
                    title: columnLabels?.["userdetails"]?.[language],
                    render: function(data, type, row) {
                        if (!data) return '-';

                        const parts = data.split('|').map(p => p.trim());

                        return `
                            <div>
                                <strong>Name:</strong> ${parts[0]}<br>
                                <strong>IFHRMSNO:</strong> ${parts[1]}<br>
                                <strong>Email:</strong> ${parts[2]}
                            </div>
                        `;
                    },
                    className: 'd-none d-md-table-cell lang extra-column wrap-50'
                },
             {
                data: "completed_institution_details",
                title: columnLabels?.["completed_institution_details"]?.[language],
                render: function(data, type, row) {
                    if (!data || data === '-' || data === 'Not yet started') {
                            return `<div>${data || '-'}</div>`;
                        }

                    const parts = data.split('|').map(p => p.trim());

                    return `
                        <div>
                            <strong>From Date:</strong> ${parts[1].replace('From: ', '')}<br>
                            <strong>To Date:</strong> ${parts[2].replace('To: ', '')}<br>
                            <strong>Entry Date:</strong> ${parts[3].replace('Entry: ', '')}<br>
                            <strong>Exit Date:</strong> ${parts[4].replace('Exit: ', '')}<br>
                            <strong>Institution:</strong> ${parts[0]}

                        </div>
                    `;
                },
                className: 'd-none d-md-table-cell lang extra-column wrap-50'
            },


            {
                data: "status",
                title: columnLabels?.["status"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function(data, type, row) {
                    return row.status || '-';
                }
            },

             {
    data: "next_schedule_details",
    title: columnLabels?.["next_schedule_details"]?.[language],
    className: "d-none d-md-table-cell lang extra-column text-wrap wrap-50",

    render: function(data, type, row) {

        if (!data) {
            return `<div class="text-center">-</div>`;
        }

        if (data.trim() === "-") {
            return `<div class="text-center">-</div>`;
        }

        // Template Audit only
        if (data.trim() === "Template Audit") {
            return `
                <div class="text-center">
                    Template Audit
                </div>
            `;
        }

        const parts = data.split('|').map(p => p.trim());

        let fromDate = (parts[0] || '')
            .replace('From:', '')
            .trim();

        let toDate = (parts[1] || '')
            .replace('To:', '')
            .trim();

        let institution = (parts[2] || '')
            .replace('Institution:', '')
            .trim();

        return `
            <div>
                <strong>From Date:</strong> ${fromDate || '-'}<br>
                <strong>To Date:</strong> ${toDate || '-'}<br>
                <strong>Institution:</strong> ${institution || '-'}
            </div>
        `;
    }
},


            {
    data: "idle_days",
    title: columnLabels?.["idle_days"]?.[language],
    className: "text-center",

    render: function(data, type, row) {

        let days = row.idle_days || 0;

        if(days === 0){
            return '-';
        }

        return `
            <div>

                <strong>${days} Days</strong>

                <br>

                <button
                    class="btn btn-sm btn-primary mt-1 viewIdleDates"
                    data-idledates="${row.idle_dates || ''}">

                    View Dates

                </button>

            </div>
        `;
    }
},




            ],



        });

        const mobileColumns = [RegionColumn,DistrictColumn,InstitutionColumn,"auditquartercode","audit_period","createdon","updatedon","entrymeetdate","exitmeetdate","processcode"];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language ="en", "idleauditorstable");
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

        // User Details
        { header: "Username", key: "username" },
        { header: "IFHRMS No", key: "ifhrmsno" },
        { header: "Email", key: "email" },

        // Completed institution details
        { header: "Completed Institution", key: "completed_inst" },
        { header: "Completed From dtae", key: "completed_from" },
        { header: "Completed To date", key: "completed_to" },
        { header: "Entry Date", key: "entry_date" },
        { header: "Exit Date", key: "exit_date" },

        // Next schedule
        { header: "Next Scheduled Institution From date", key: "next_from" },
        { header: "Next Scheduled Institution To date", key: "next_to" },
        { header: "Next Scheduled Institution", key: "next_inst" },

        // Idle days + Status
        { header: "Status", key: "status" },
        { header: "Idle Days", key: "idle_days" }
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
        let dept = r.deptesname || "-";
        let region = r.regionename || "-";
        let district = r.distename || "-";

        // --- User details ---
        let username = "-";
        let ifhrmsno = "-";
        let email = "-";

        if (r.userdetails) {
            let parts = r.userdetails.split("|").map(x => x.trim());
            username = parts[0] || "-";
            ifhrmsno = parts[1] || "-";
            email = parts[2] || "-";
        }

        // --- Completed institution details ---
        let c_inst = "-", c_from = "-", c_to = "-", c_entry = "-", c_exit = "-";

        if (r.completed_institution_details) {
            let p = r.completed_institution_details.split("|").map(x => x.trim());

            c_inst = p[0] || "-";
            c_from = p[1]?.replace("From:", "").trim() || "-";
            c_to = p[2]?.replace("To:", "").trim() || "-";
            c_entry = p[3]?.replace("Entry:", "").trim() || "-";
            c_exit = p[4]?.replace("Exit:", "").trim() || "-";
        }

        // --- Next schedule details ---
        let n_from = "-", n_to = "-", n_inst = "-";

        if (r.next_schedule_details) {
            let p = r.next_schedule_details.split("|").map(x => x.trim());

            n_from = p[0]?.replace("From:", "").trim() || "-";
            n_to = p[1]?.replace("To:", "").trim() || "-";
            n_inst = p[2]?.replace("Institution:", "").trim() || "-";
        }

        excelData.push({
            dept,
            region,
            district,

            username,
            ifhrmsno,
            email,

            completed_inst: c_inst,
            completed_from: c_from,
            completed_to: c_to,
            entry_date: c_entry,
            exit_date: c_exit,

            next_from: n_from,
            next_to: n_to,
            next_inst: n_inst,

            status: r.status || "-",
            idle_days: r.idle_days || "-"
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


        var form = $('#idleauditorsform');

        form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }



        $('#idleauditorsform')[0].reset();
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
