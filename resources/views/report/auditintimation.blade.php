@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Audit Intimation Details Report')


@php

$sessionchargedel = session('charge');
// print_r($sessionchargedel);
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




<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Audit Intimation Details</div>
        <div class="card-body">
            <form id="auditeeintimation_form" name="auditeeintimation_form" method='post'>
                @csrf
                <div class="row">

                    <input type="hidden" value="" id="if_subcategory" name="if_subcategory">


                    <div class="col-md-3">

                        <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                            name="financialyear">

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

                    <div class="col-md-3">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select
                            class="form-select mr-sm-2 select2 lang-dropdown"
                            multiple="multiple"
                            id="deptcode"
                            name="deptcode[]" <?php echo $make_dept_disable; ?>
                            onchange="onchange_region('region', 'regioncode');onchange_region('auditquarter', 'auditquarter');">


                            <option value="A" data-name-en="All" data-name-ta="??????? ?????????"
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
                                data-name-ta="???? ???????? ?????????????">No Departments Available
                            </option>
                            @endif
                        </select>
                    </div>



                    <div class="col-md-3">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode[]" onchange="onchange_region('district','distcode')">

                            <!-- <option value="" data-name-en="Select Region" data-name-ta="??????????? ?????????????????">Select Region</option> -->

                            <option value="A" data-name-en="All" data-name-ta="???????"> All</option>

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
                        <label class="form-label required lang" for="distcode" key="district">District</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]" onchange="onchange_region('institution','instmappingcode');">

                            <!-- <option value="" data-name-en="Select District" data-name-ta="???????????? ?????????????????">Select District</option> -->

                            <option value="A" data-name-en="All" data-name-ta="???????"> All</option>


                            @if ($distcode)
                            @foreach ($district as $dist)
                            <option value="{{ $dist->distcode }}"
                                @if (old('dept', $distcode)==$dist->distcode) selected @endif
                                data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                {{ $dist->distename }}
                            </option>
                            @endforeach
                            @else

                            @endif
                        </select>
                    </div>




                    <div class="col-md-3 mb-3">
                        <label class="form-label required lang" key="Institution"
                            for="institution">Institution</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" id="instmappingcode" name="instmappingcode[]">




                            <!-- <option value="" data-name-en="Select Institution" data-name-ta="???????????? ?????????????????">Select Institution</option> -->

                            <option value="A" data-name-en="All" data-name-ta="???????"> All</option>

                        </select>
                    </div>

                    <!-- <div class="col-md-4">
                        <label class="form-label required" for="auditquarter" key='auditquarter'>Audit Quarter</label>

                        <select class="form-select" id="quartercode" name="quartercode" disabled>
                            @if($currentQuarter)
                            <option value="{{ $currentQuarterCode }}" selected>{{ $currentQuarter }}</option>
                            @else
                            @endif
                        </select>
                    </div> -->

                     <div class="col-md-3">
                            <label class="form-label required lang" for="auditquarter" key="auditquarter">Audit Quarter</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"

                                id="auditquarter" name="auditquarter[]">
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
            </form>

        </div>
    </div>
</div>



<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Audit Intimation Details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="auditintimationdetails" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="">Department</th>
                                <th class="lang align-middle text-center" key="">Region</th>
                                <th class="lang align-middle text-center" key="">District</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">AuditQuarter</th>
                                <th class="lang align-middle text-center" key="">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id=' no_data' class='hide_this'>
                <center>No Data Available</center>
            </div>
        </div>
    </div>
</div>

<div id="pageLoader" class="page-loader d-none">
    <div class="dots">
        <span></span><span></span><span></span>
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
<script src="{{ asset('common/ajaxfn.js') }}"></script>

<script src="{{ asset('assets/js/chart.umd.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
    function showLoader() {
        document.getElementById('pageLoader').classList.remove('d-none');
    }

    function hideLoader() {
        document.getElementById('pageLoader').classList.add('d-none');
    }

    $(document).ready(function() {
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
        $(document).ready(function() {
            function handleDeptcodeVisibility() {
                let selected = $('#deptcode').val();

                if (selected && (selected.includes("01") || selected.includes("05"))) {
                    $('#subcatdiv').hide();
                } else {
                    $('#subcatdiv').show();
                }
            }

            handleDeptcodeVisibility();

            $('#deptcode').on('change', handleDeptcodeVisibility);
        });




        function handleAllOption($select) {
            $select.on('change', function() {
                updateSelect($select);
            });

            if (!$select.val() || $select.val().length === 0) {
                $select.val(['']).select2();
            }

            updateSelect($select);
        }

        ['#deptcode', '#distcode', '#instmappingcode', '#regioncode', '#category', '#auditquarter', '#subcategory']
        .forEach(function(selector) {
            handleAllOption($(selector));
        });
    });


    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        // Custom rule for select2 and normal multi-select
        $.validator.addMethod("selectRequired", function(value, element) {
            let v = $(element).val();
            return !(v === null || v.length === 0);
        }, "Please select at least one option.");

        $("#auditeeintimation_form").validate({

            ignore: ":hidden:not(.select2-hidden-accessible)",

            rules: {
                 financialyear: {
                    required: true
                },
                "auditquarter[]": {
                    selectRequired: true
                },
                "deptcode[]": {
                    selectRequired: true
                },
                "regioncode[]": {
                    selectRequired: true
                },
                "distcode[]": {
                    selectRequired: true
                },

                "instmappingcode[]": {
                    selectRequired: true
                },

            },

            messages: {
                financialyear: "Select a Financial Year",
                "auditquarter[]": "Select Audit Quarter",
                "deptcode[]": "Select Department",
                "regioncode[]": "Select Region",
                "distcode[]": "Select District",
                "instmappingcode[]": "Select Institution",

            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#buttonaction').on('click', function(e) {
            e.preventDefault();

            if (!$("#auditeeintimation_form").valid()) {
                console.log("Validation failed");
                return;
            }

            getIntimationData('en');
        });

        reset_form();
    });



    let dataFromServer;

    function enforceSelect2AllOnly(selectId) {
        $('#' + selectId)
            .off('select2:select select2:unselect')
            .on('select2:select select2:unselect', function() {

                let values = $(this).val() || [];

                if (values.includes('A') && values.length > 1) {
                    $(this).val(['A']).trigger('change.select2');
                }
            });
    }

    let hitLock = {
        region: false,
        district: false,
        institution: false,

    };

    $(document).ready(function() {

        handleAllOption('deptcode');
        enforceSelect2AllOnly('regioncode');
        enforceSelect2AllOnly('distcode');
        enforceSelect2AllOnly('category');
        enforceSelect2AllOnly('subcategory');
        enforceSelect2AllOnly('instmappingcode');

        $("#deptcode").off().on("change", function() {
            onchange_region('region', 'regioncode');
            onchange_region('auditquarter', 'auditquarter');

        });

        $("#regioncode").off().on("change", function() {
            onchange_region('district', 'distcode');
        });

        $("#distcode").off().on("change", function() {
            onchange_region('institution', 'instmappingcode');
        });


    });


    function onchange_region(valuefor, valueforid) {

        if (hitLock[valuefor]) {
            console.log("Prevented multiple hit:", valuefor);
            return;
        }
        hitLock[valuefor] = true;

        const $dropdown = $("#" + valueforid);

        if ($dropdown.hasClass("select2-hidden-accessible")) {
            $dropdown.select2('destroy');
        }

        $dropdown.empty().select2();
        enforceSelect2AllOnly(valueforid);

        let deptcode = $('#deptcode').val() || [];
        let regioncode = $('#regioncode').val() || [];
        let distcode = $('#distcode').val() || [];
        let instmappingcode = $('#instmappingcode').val() || [];
        let auditquarter = $('#auditquarter').val() || [];
        let financialyear = $('#financialyear').val();


          if (valueforid === 'auditquarter' && deptcode.length > 1) {
            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A">All</option>`);
            dd.select2();
            return;
        }

        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                financialyear,
                auditquarter,
                deptcode,
                regioncode,
                instmappingcode,
                distcode,
                valuefor,
                formname: "auditeeintimationreport"
            },

            success: function(response) {

                hitLock[valuefor] = false;

                if (!response.success || !Array.isArray(response.data)) {
                    $dropdown.append(`<option value="">No data available</option>`);
                    return;
                }

                const options = response.data.map(item => {
                    switch (valuefor) {

                        case 'region':
                            return item.code === "A" ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.regioncode}">${item.regionename}</option>`;

                        case 'district':
                            return item.code === "A" ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.distcode}">${item.distename}</option>`;


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




                        case 'institution':
                            return item.code === "A" ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.instid}">${item.instename}</option>`;

                        default:
                            return '';
                    }
                }).join('');

                $dropdown.append(options).select2();
                enforceSelect2AllOnly(valueforid);
            },

            error: function(xhr) {
                hitLock[valuefor] = false;
                console.error("AJAX error", xhr);
            }
        });
    }



    function getIntimationData(lang) {
        showLoader();
        var deptcode = $('#deptcode').val() || ['A'];
        var regioncode = $('#regioncode').val() || ['A'];
        var distcode = $('#distcode').val() || ['A'];
         var financialyear = $('#financialyear').val() || ['A'];
        var auditquarter = $("#auditquarter").val();
        if (!auditquarter || auditquarter.length === 0) {
            auditquarter = [];
        }
        // var instmappingcode = $('#instmappingcode').val() || ['A'];
        var instid = $('#instmappingcode').val();
        if (!instid || instid.length === 0) {
            instid = ['A'];
        }
        if (!Array.isArray(instid)) instid = [instid];
        $.ajax({
            url: "{{ route('report.auditintimation.fetch') }}",
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                auditquarter: auditquarter,
                 financialyear: financialyear,
                instid: instid,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#tableshow').hide();
                $('#no_data').hide();
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    dataFromServer = response.data;
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    renderTable(lang);
                    hideLoader();

                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                    hideLoader();

                }
            },
            error: function(xhr, status, error) {

                if (xhr.status === 404) {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }

                console.error('Error:', xhr, status, error);
            },
            complete: function() {
                hideLoader();
            }
        });
    }



    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(lang);

    });

    function renderTable() {

        const departmentColumn = "department";
        const regionColumn = "region";
        const districtColumn = "district";

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            $('#tableshow').addClass('hide_this');
            $('#no_data').removeClass('hide_this');
            return;
        }

        $('#no_data').addClass('hide_this');
        $('#tableshow').removeClass('hide_this');

        if ($.fn.DataTable.isDataTable('#auditintimationdetails')) {
            $('#auditintimationdetails').DataTable().clear().destroy();
        }

        $('#auditintimationdetails').DataTable({
            data: dataFromServer,
            searching: true,
            paging: true,
            ordering: true,
            lengthChange: false,
            autoWidth: false,

            columns: [

                {
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
                    data: row => row[departmentColumn] || "-",
                    title: "Department"
                },
                {
                    data: row => row[regionColumn] || "-",
                    title: "Region"
                },
                {
                    data: row => row[districtColumn] || "-",
                    title: "District"
                },
                {
                    data: "institution",
                    className: "text-start text-wrap"
                },

                {
                    data: "planname",
                    title: "AuditQuarter"
                },

                {
                    data: "status_name",
                    title: "Status",
                    render: d => d ?? "-"

                }


            ]
        });

        updatedatatable("en", "auditintimationdetails");
    }


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditintimationdetails')) {
            $('#auditintimationdetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();

        let titleKey = `${tableId}_title`;
        let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Audit Intimation Report";
        let safeSheetName = reportTitle.substring(0, 31);

        let headers = [{
                header: "Department",
                key: "department"
            },
            {
                header: "Region",
                key: "region"
            },
            {
                header: "District",
                key: "district"
            },
            {
                header: "Institution",
                key: "institution"
            },

            {
                header: "Audit Quarter",
                key: "auditquartercode"
            },
            {
                header: "Status",
                key: "status_name"
            }
        ];

        let rawData = table.rows({
            search: 'applied'
        }).data().toArray();
        let excelData = [];

        rawData.forEach(row => {
            try {
                let button = $(row[0]).find("button.toggle-row");
                let dataRow = button.attr("data-row");
                let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

                excelData.push({
                    department: rowData["department"] || "-",
                    region: rowData["region"] || "-",
                    district: rowData["district"] || "-",
                    institution: rowData["institution"] || "-",
                    auditquartercode: rowData["planname"] || "-",
                    status_name: rowData["status_name"] || "-"
                });

            } catch (e) {
                console.error("Error parsing row data", e);
            }
        });


        if (excelData.length === 0) {
            alert("No data available for export!");
            return;
        }

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);

        XLSX.utils.sheet_add_aoa(ws, [
            [reportTitle]
        ], {
            origin: "A1"
        });

        ws['!merges'] = [{
            s: {
                r: 0,
                c: 0
            },
            e: {
                r: 0,
                c: headers.length - 1
            }
        }];

        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A2"
        });
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A3"
        });

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

        if (sessionrole == distrole) {} else if (sessionrole == regionrole) {} else if (sessionrole == headofficerole) {} else if (sessionrole == dgarole) {} else if (sessionrole == adminrole) {}


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
            $('#financialyear').val('').select2();

        var form = $('#auditeeintimation_form');

        form[0].reset();


        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

        if (sessionrole == distrole) {
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#irregularity').val('').select2();
            onchange_region('institution', 'instmappingcode');
        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#auditquarter').val(['']).select2();
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();

            onchange_region('region', 'regioncode');

        } else if (sessionrole == dgarole) {


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#deptcode').val('').select2();


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }


    }
</script>


@endsection