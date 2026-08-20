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

    #loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner {
    width: 35px;
    height: 35px;
    border: 4px solid #ffffff;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


</style>


<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Leave details</div>
        <div class="card-body">
            <form id="leavedetailsform" name="leavedetailsform" method='post'>
                <div class="row">
                 @csrf

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


                 <div class="col-md-4 mb-2" id="deptdiv">
                    <label class="form-label required  lang" key="department" for="dept">Department</label>

                    <select class="form-select mr-sm-2 select2  lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?>  id="deptcode"   name="deptcode[]"
                        onchange="onchange_region('region', 'regioncode');">


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

                        <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?>   id="regioncode" name="regioncode[]"
                                onchange="onchange_region('district','distcode')" >


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

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2" multiple="multiple"  <?php echo $make_dist_disable; ?>   id="distcode" name="distcode[]">



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

                            <div class="col-md-4">
                                <label class="form-label required lang" for="auditquarter" key="auditquarter">Audit Quarter</label>
                                <select class="form-select  mr-sm-2 select2 lang-dropdown"

                                    id="auditquarter" name="auditquarter">



                                    <option value="" data-name-en="Select Audit Quarter" data-name-ta="Select Audit Quarter">Select Audit Quarter</option>


                                    @foreach ($quarter as $qua)
                                        <option value="{{ $qua->auditquartercode }}"
                                            data-name-en="{{ $qua->auditquartercode }}"
                                            data-name-ta="{{ $qua->auditquartercode }}"
                                            {{ $qua->auditquartercode }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>





                    {{-- <div class="col-md-4 mt-3" id="nextQuarterDateWrapper">
                        <label class="form-label d-block mb-2" for="nextQuarterDate">Select Date</label>
                        <div class="input-group mx-auto date" id="datepickerNextQuarter">
                            <input type="text" class="form-control" id="logindate" name="logindate"
                                placeholder="dd-mm-yyyy">
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div> --}}



                    <div id="loader">
                        <div class="spinner"></div>
                    </div>




                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Submit </button>
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
        <div class="card-header card_header_color lang" id="leaveHeader" key="">Auditor Leave details for the period of </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="leavedetailsreport"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="">Department Details</th>
                                <th class="lang" key="">Auditor Details</th>
                               <th class="lang" key="">Leave Period</th>
                                <th class="lang" key="">Approved Details</th>
                                <th class="lang" key="">Reason</th>
                                <th class="lang" key="">No. of days</th>


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

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

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
    let dataFromServer;

    var sessiondeptcode = ' <?php echo $deptcode; ?>';



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





    var deptcode =  $('#deptcode').val();
    var regioncode =  $('#regioncode').val();
    var distcode =  $('#distcode').val();


     var lang = getLanguage();


     if (!Array.isArray(deptcode)) {
        deptcode = deptcode ? [deptcode] : [];
    }

    if (deptcode.length === 0 || deptcode.includes("")) {
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
            formname: 'leavedetails'

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {


            if (response.success && Array.isArray(response.data)) {


                $dropdown.empty();



                if (!regioncode || regioncode.length === 0) {
                    regioncode = ['A'];
                }

                if (!distcode || distcode.length === 0) {
                    distcode = ['A'];
                }


                const options = response.data.map(item => {
                    switch (valuefor) {
                        case 'region':
                    return item.code === 'A'
                        ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${regioncode.includes('A') ? "" : ""}>${item.name_en}</option>`
                        : `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}" ${regioncode.includes(item.regioncode) ? "selected" : ""}>${item.regionename}</option>`;


                        case 'district':
                        return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${distcode.includes('A') ? "" : ""}>${item.name_en}</option>`
                            : `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${distcode.includes(item.distcode) ? "selected" : ""}>${item.distename}</option>`;



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


const quarterRanges = {
    "Q1": { from: "01-04-2025", to: "30-06-2025" },
    "Q2": { from: "01-07-2025", to: "30-09-2025" },
    "Q3": { from: "01-10-2025", to: "31-12-2025" },
    "Q4": { from: "01-01-2026", to: "31-03-2026" }
};



jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
    }, "Please select at least one option.");


        var validator = $("#leavedetailsform").validate({

            ignore: [],
            rules: {
            financialyear: { required: true },
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            auditquarter: { required: true },

        },
        messages: {
            financialyear: "Select financial Year",
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            auditquarter: "Select Audit Quarter",

        },

        errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },



        });

    $('#buttonaction').on('click', function (event) {
        event.preventDefault();
        var form = $('#leavedetailsform');

        if ($("#leavedetailsform").valid()) {

            let selected = $("#auditquarter").val(); // "Q1"

    if (!selected) {
        $("#leaveHeader").text("Auditor Leave details for the period of");
    }
    else {
        let q = selected;
        let range = quarterRanges[q];

        if (range) {
            $("#leaveHeader").text(
                `Auditor Leave details for the period of ${range.from} to ${range.to} (${q})`
            );
        }
    }



        var lang = 'en';
        getInstData(lang);
    } else {

    }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});







    function getInstData(lang) {
        $("#loader").show();

        var deptcode =  $('#deptcode').val() || 'A';
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val() || 'A';
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val() || 'A';
       var financialyear = $('#financialyear').val();
       var auditquarter = $('#auditquarter').val();

        $.ajax({
            url: '/getleavedetailsofauditors',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                financialyear : financialyear,
                auditquarter : auditquarter


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
        if ($.fn.DataTable.isDataTable('#leavedetailsreport')) {
            $('#leavedetailsreport').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {

        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
       const RegionColumn = language === 'ta' ? 'regionename' : 'regionename';
       const DistrictColumn = language === 'ta' ? 'distename' : 'distename';
        const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';
        const desigColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#leavedetailsreport')) {
            $('#leavedetailsreport').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#leavedetailsreport').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#leavedetailsreport").wrap(
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
                    render: function(data, type, row) {
                        let dept = row[departmentColumn] || '-';
                        let region = row[RegionColumn] || '-';
                        let district = row[DistrictColumn] || '-';
                        let financialyear = row.financialyear || '-';
                        let auditquarter  = row.auditquarter || '-';
                        return `<strong>Department:</strong> ${dept}<br>
                        <strong>Region:</strong> ${region}<br>
                        <strong>District:</strong> ${district}<br>
                        <strong>Financial Year:</strong> ${financialyear}<br>
                        <strong>Audit Quarter:</strong> ${auditquarter}`;

                    },
                    className: 'text-start'
                },


                {
                data: null,
                title: columnLabels?.[usernameColumn]?.[language],
                render: function(data, type, row) {
                    const username = row[usernameColumn] || '-';
                    const desig = row.desigesname || '-';
                    const createdon = row.createdon || '-';

                    return `<b>Name:</b>${username} - ${desig}<br><b>Applied On:</b> ${createdon}`;
                },
                className: 'text-wrap text-start'
            },



            {
                data: null,
                render: function(data, type, row) {

                    let from = row.fromdate;
                    let to   = row.todate;

                    return `<strong>From date:</strong> ${from}<br><strong>To date:</strong> ${to}`;
                },
                className: 'text-wrap text-start'

            },




                {
                data: null,
                title: columnLabels?.["approvedby"]?.[language],
                render: function(data, type, row) {
                    const approvedby_username = row.approvedby_username || '-';
                    const approvedby_designation = row.approvedby_designation || '-';
                    const approvedon = row.updatedon || '-';
                    return `<b>Approved By:</b>${approvedby_username} - ${approvedby_designation}<br><b>Approved On:</b> ${approvedon}`;
                },
                className: 'text-wrap text-start wrap-50'
            },


            {
                    data: 'reason',
                    title: columnLabels?.["reason"]?.[language],
                    render: function(data, type, row) {
                        return row.reason || '-';
                    },
                    className: 'text-wrap text-start wrap-50'
                },




                {
                    data: 'leavedayscount',
                    title: columnLabels?.["leavedayscount"]?.[language],
                    render: function(data, type, row) {
                        return row.leavedayscount || '-';
                    },
                    className: 'text-wrap text-start'
                },


            ]
        });
           const mobileColumns = [
            "department", "auditCompleted", "reportFinalised",
            "issuedReport"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "leavedetailsreport");
    }



    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Leave Details Record";
    let safeSheetName = reportTitle.substring(0, 31);

    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    const columnMap = {
        departmentColumn: 'deptesname',
        DistrictColumn: language === 'ta' ? 'disttname' : 'distename',
        RegionColumn: language === 'ta' ? 'regiontname' : 'regionename',
        InstitutionColumn: language === 'ta' ? 'insttname' : 'instename',
    };

    let headers = [
        { header: dtText["financialyear"] || "Financial Year", key: "financialyear" },
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["region"] || "Region", key: "region" },
        { header: dtText["district"] || "District", key: "district" },
        { header: dtText["username"] || "Auditor Name", key: "username" },
        { header: dtText["createdon"] || "Applied In", key: "createdon" },
        { header: dtText["auditquarter"] || "Auditor Quarter", key: "auditquarter" },
        { header: dtText["fromdate"] || "From Date", key: "fromdate" },
        { header: dtText["todate"] || "To Date", key: "todate" },
        { header: dtText["reason"] || "Reason", key: "reason" },
        { header: dtText["approvedby"] || "Approved By", key: "approvedby" },
        { header: dtText["updatedon"] || "Approved On", key: "updatedon" },
        { header: dtText["leavedayscount"] || "No. of days", key: "leavedayscount" }
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
            financialyear: rowData.financialyear || "-",
            department: rowData[columnMap.departmentColumn] || "-",
            region: rowData[columnMap.RegionColumn] || "-",
            district: rowData[columnMap.DistrictColumn] || "-",

            username: `${rowData.username || "-"} - ${rowData.desigesname || "-"}`,

            createdon: rowData.createdon || "-",
            auditquarter: rowData.auditquarter || "-",
            fromdate: rowData.fromdate || "-",
            todate: rowData.todate || "-",
            reason: rowData.reason || "-",

            approvedby: `${rowData.approvedby_username || "-"} - ${rowData.approvedby_designation || "-"}`,

            updatedon: rowData.updatedon || "-",
            leavedayscount: rowData.leavedayscount || "-",
        });
        } catch (e) {}
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // Add main title
    XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });

    // Merge columns for title
    ws['!merges'] = [{
        s: { r: 0, c: 0 },
        e: { r: 0, c: headers.length - 1 }
    }];

    // Center title text by padding
    let colCount = headers.length;
    let totalWidth = colCount * 12;
    let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");

    XLSX.utils.sheet_add_aoa(ws, [[paddedTitle]], { origin: "A1" });

    // Add header row
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A2" });

    // Add data rows
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A3" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}



//     function exportToExcel(tableId, language) {
//     let table = $(`#${tableId}`).DataTable();

//     let titleKey = `${tableId}_title`;
//     let translatedTitle = dataTables[language]?.datatable?.[titleKey] ||;
//     let safeSheetName = translatedTitle.substring(0, 31);
//     let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


//     const columnMap = {
//           departmentColumn : language === 'ta' ? 'deptesname' : 'deptesname',
//           DistrictColumn : language === 'ta' ? 'disttname' : 'distename',

//           RegionColumn : language === 'ta' ? 'regiontname' : 'regionename',
//           InstitutionColumn : language === 'ta' ? 'insttname' : 'instename',


//     };

//     // ? Define Headers Properly
//     let headers = [
//     { header: dtText["financialyear"] || "Financial Year", key: "financialyear" },
//     { header: dtText["department"] || "Department", key: "department" },
//     { header: dtText["region"] || "Region", key: "region" },
//     { header: dtText["district"] || "District", key: "district" },
//     { header: dtText["username"] || "Auditor Name", key: "username" },
//     { header: dtText["designation"] || "Designation", key: "desigesname" },
//     { header: dtText["fromdate"] || "From leave Date", key: "fromdate" },
//     { header: dtText["todate"] || "To leave Date", key: "todate" },
//     { header: dtText["reason"] || "Reason", key: "reason" },
//     { header: dtText["approvedby"] || "Approved By", key: "approvedby" },
//     { header: dtText["leavedayscount"] || "No.of leaves", key: "leavedayscount" }
// ];


// let rawData = table.rows({ search: 'applied' }).data().toArray();

// let excelData = rawData.map(row => {
//     let button = $(row[0]).find("button.toggle-row");
//     let dataRow = button.attr("data-row");

//     let rowData = {};

//         try {
//             rowData = dataRow ? JSON.parse(dataRow) : {};
//         } catch (e) {
//             console.error("Invalid JSON in data-row:", dataRow);
//         }


//     return {
//         financialyear: rowData.financialyear || "-",
//         department: rowData[columnMap.departmentColumn] || "-",
//         region: rowData[columnMap.RegionColumn] || "-",
//         district: rowData[columnMap.DistrictColumn] || "-",

//         username: rowData.username || "-",
//         desigesname: rowData.desigesname || "-",

//         fromdate: rowData.fromdate || "-",
//         todate: rowData.todate || "-",
//         reason: rowData.reason || "-",
//         approvedby: `${rowData.approvedby_username || '-'} - ${rowData.approvedby_designation || '-'}`,
//         leavedayscount: rowData.leavedayscount || "-",

//     };
// });

//     if (excelData.length === 0) {
//         alert("No data available for export!");
//         return;
//     }

//     const wb = XLSX.utils.book_new();
//     const ws = XLSX.utils.json_to_sheet([]);

//     XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });

//         // Merge across columns
//         ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: headers.length - 1 } }];

//         let colCount = headers.length;
//         let totalWidth = colCount * 15;
//         let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");
//         XLSX.utils.sheet_add_aoa(ws, [[paddedTitle]], { origin: "A1" });
//         XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A2" });

//         XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A3" });

//         XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
//         XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
// }








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

        } else if (sessionrole == regionrole) {
            onchange_region('district', 'distcode')


        } else if (sessionrole == headofficerole) {
            onchange_region('region', 'regioncode')


        } else if (sessionrole == dgarole) {

        } else if (sessionrole == adminrole) {

        }


    });








    function reset_form() {

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
        $('#financialyear').val('').select2();
        $("#leaveHeader").text("Auditor Leave details for the period of -");
        $("#loader").hide();

        var form = $('#leavedetailsform');

            form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }

        $('#leavedetailsform')[0].reset();
        if (sessionrole == distrole) {

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();

        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();

        } else if (sessionrole == dgarole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();

        } else if (sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


          }

    }
</script>


@endsection
