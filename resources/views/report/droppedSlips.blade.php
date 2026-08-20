@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Dropped Slip Count')


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
        <div class="card-header card_header_color">Dropped Slips</div>
        <div class="card-body">
            <form id="droppedslip_form" name="droppedslip_form" method='post'>
                @csrf
                <div class="row">

                    <input type="hidden" value="" id="if_subcategory" name="if_subcategory">


                      <div class="col-md-4">

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


                    <div class="col-md-4">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select
                            class="form-select mr-sm-2 select2 lang-dropdown"
                            multiple="multiple"
                            id="deptcode"
                            name="deptcode[]" <?php echo $make_dept_disable; ?>
                            onchange="onchange_region('region', 'regioncode');">

                            <!-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> -->

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
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode[]" onchange="onchange_region('district','distcode')">

                            <!-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option> -->

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
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]" onchange="onchange_region('institution','instmappingcode'); ">

                            <!-- <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


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


                    <div class="col-md-4 mb-4">
                        <label class="form-label lang required" key="category"
                            for="validationDefault01">Category</label>

                        <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                            onchange="onchange_region('subcatcode','subcategory')">


                            <!-- <option value="" data-name-en="Select Category" data-name-ta="">Select Category</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>

                        </select>
                    </div>


                    <div class="col-md-4  subcatdiv " id="subcatdiv">
                        <label class="form-label lang required" key="" for="subcategory">SubCategory</label>

                        <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                            name="subcategory[]">

                            <!-- <option value="" data-name-en="Select SubCategory" data-name-ta="">Select SubCategory</option> -->


                            <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>

                        </select>
                    </div>



                    <div class="col-md-4 mb-3">
                        <label class="form-label required lang" key="institution"
                            for="institution">Institution</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" id="instmappingcode" name="instmappingcode[]">




                            <!-- <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                        </select>
                    </div>



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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Dropped Slips Details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="DroppedSlipcount"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="">Department Details</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">Entry Meeting Date/Exit Meeting Date</th>
                                <th class="lang align-middle text-center" key="">Category/Subcategory Details</th>
                                <th class="lang align-middle text-center" key="">AuditQuarter</th>
                                <th class="lang align-middle text-center" key="">Financial Year</th>
                                <th class="lang align-middle text-center" key="">Slip Details</th>
                                <th class="lang align-middle text-center" key="">Dropped Slip %</th>


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

 <div id="loader" class="d-none">
    <div class="spinner"></div>
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
    let deptChangeTimeout;

    $('#deptcode').on('change', function() {
        clearTimeout(deptChangeTimeout);

        deptChangeTimeout = setTimeout(function() {
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');
            onchange_region('auditquarter', 'auditquarter');

        }, 200);
    });


    function showLoader() {
        document.getElementById('loader').classList.remove('d-none');
    }

    function hideLoader() {
        document.getElementById('loader').classList.add('d-none');
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

        $("#droppedslip_form").validate({

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
                "category[]": {
                    selectRequired: true
                },

                "subcategory[]": {
                    selectRequired: function() {
                        let dept = $("#deptcode").val() || [];

                        if (dept.includes("01") || dept.includes("05")) {
                            return false;
                        }
                        return true;
                    }
                },

                "instmappingcode[]": {
                    selectRequired: true
                },
                "irregularity[]": {
                    selectRequired: true
                },
            },

            messages: {
                financialyear: "Select a Financial Year",
                "auditquarter[]": "Select Audit Quarter",
                "deptcode[]": "Select Department",
                "regioncode[]": "Select Region",
                "distcode[]": "Select District",
                "category[]": "Select Category",
                "subcategory[]": "Select SubCategory",
                "instmappingcode[]": "Select Institution",
                "irregularity[]": "Select Irregularity",
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

            if (!$("#droppedslip_form").valid()) {
                console.log("Validation failed");
                return;
            }

            getDropSlipData('en');
        });

        reset_form();
    });
    $(document).ready(function() {
        var $financialYear = $('#financialyear');

        if ($financialYear.val() !== '') {
            setTimeout(function() {
                $financialYear.select2();
            }, 100);
        }
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
        catcode: false,
        subcatcode: false
    };

    $(document).ready(function() {

       // handleAllOption('deptcode');
        enforceSelect2AllOnly('regioncode');
        enforceSelect2AllOnly('distcode');
        enforceSelect2AllOnly('category');
        enforceSelect2AllOnly('subcategory');
        enforceSelect2AllOnly('instmappingcode');

        $("#deptcode").off().on("change", function() {
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');
            onchange_region('auditquarter', 'auditquarter');

        });

        $("#regioncode").off().on("change", function() {
            onchange_region('district', 'distcode');
        });

        $("#distcode").off().on("change", function() {
            onchange_region('institution', 'instmappingcode');
        });

        $("#category").off().on("change", function() {
            onchange_region('subcatcode', 'subcategory');
        });
    });



    function onchange_region(valuefor, valueforid) {


        let deptcode = $('#deptcode').val() || [];
        let regioncode = $('#regioncode').val() || [];
        let distcode = $('#distcode').val() || [];
        let catcode = $('#category').val() || [];
        let subcatcode = $('#subcategory').val() || [];
        var financialyear = $("#financialyear").val();

        const $dropdown = $('#' + valueforid);


        if (valuefor === 'district' && regioncode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }

        if (valuefor === 'institution' && distcode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }

        if (valuefor === 'subcatcode' && catcode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }


        if (hitLock[valuefor]) {
            return;
        }
        hitLock[valuefor] = true;

        if ($dropdown.hasClass('select2-hidden-accessible')) {
            $dropdown.select2('destroy');
        }
        $dropdown.empty().select2();
        enforceSelect2AllOnly(valueforid);

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
            formname: 'droppedslipcountreport'
        };

        if (['region', 'catcode', 'audityear','auditquarter'].includes(valuefor)) {
            sendData.deptcode = deptcode;
            sendData.financialyear = financialyear;

        }

        if (valuefor === 'district') {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
        }

        if (valuefor === 'institution') {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
            sendData.distcode = distcode;
        }

        if (valuefor === 'subcatcode') {
            sendData.catcode = catcode;
        }


        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: sendData,

            success: function(response) {

                hitLock[valuefor] = false;

                if (!response.success || !Array.isArray(response.data)) {
                    resetDropdownToAll($dropdown, valueforid);
                    return;
                }

                let options = response.data.map(item => {
                    switch (valuefor) {

                        case 'region':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.regioncode}">${item.regionename}</option>`;

                        case 'district':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.distcode}">${item.distename}</option>`;

                        case 'catcode':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.catcode}">${item.catename}</option>`;

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

                        case 'subcatcode':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.auditeeins_subcategoryid}">${item.subcatename}</option>`;

                        case 'institution':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.instid}">${item.instename}</option>`;

                        default:
                            return '';
                    }
                }).join('');

                $dropdown.append(options).select2();
                enforceSelect2AllOnly(valueforid);
            },

            error: function() {
                hitLock[valuefor] = false;
                console.error('AJAX error for:', valuefor);
            }
        });
    }


    function resetDropdownToAll($dropdown, valueforid) {

        if ($dropdown.hasClass('select2-hidden-accessible')) {
            $dropdown.select2('destroy');
        }

        $dropdown
            .empty()
            .append(`<option value="A">All</option>`)
            .val(['A'])
            .select2();

        enforceSelect2AllOnly(valueforid);
    }




    function getDropSlipData(lang) {
        showLoader();

        var deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0 ?
            $('#deptcode').val() : ['A'];

        var regioncode = $('#regioncode').val() && $('#regioncode').val().length > 0 ?
            $('#regioncode').val() : ['A'];

        var distcode = $('#distcode').val() && $('#distcode').val().length > 0 ?
            $('#distcode').val() : ['A'];
        var financialyear = $("#financialyear").val() || '';

        var category = $("#category").val() || ['A'];

        var subcatcode = $("#subcategory").val() || ['A'];
        var auditquarter = $("#auditquarter").val();
        if (!auditquarter || auditquarter.length === 0) {
            auditquarter = [];
        }

        var instmappingcode = $('#instmappingcode').val() && $('#instmappingcode').val().length > 0 ?
            $('#instmappingcode').val() : ['A'];

        $.ajax({
            url: "{{ route('report.DroppedSlip.fetch') }}",
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                category: category,
                subcatcode: subcatcode,
                auditquarter: auditquarter,
                financialyear: financialyear,
                instmappingcode: instmappingcode,
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

        if ($.fn.DataTable.isDataTable('#DroppedSlipcount')) {
            $('#DroppedSlipcount').DataTable().clear().destroy();
        }

        $('#DroppedSlipcount').DataTable({
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
                    data: null,
                    // title: "Department / Region / District",
                    title: "Department Details",
                    render: function(data, type, row) {
                        const dept = row[departmentColumn] || "-";
                        const region = row[regionColumn] || "-";
                        const district = row[districtColumn] || "-";

                        return `
                        <div><b>Department :</b> ${dept}</div>
                        <div><b>Region :</b> ${region}</div>
                        <div><b>District :</b> ${district}</div>
                    `;
                    },
                    // className: "text-start text-wrap"
                },

                {
                    data: "institution",
                    className: "text-start text-wrap"
                },
                {
                    data: null,
                    className: "text-start text-wrap",
                    render: row => {
                        const formatDate = dateStr => {
                            if (!dateStr) return "-";
                            const d = new Date(dateStr);
                            const day = String(d.getDate()).padStart(2, '0');
                            const month = String(d.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
                            const year = d.getFullYear();
                            return `${day}/${month}/${year}`;
                        };

                        const entry = formatDate(row.entrymeetdate);
                        const exit = formatDate(row.exitmeetdate);

                        return `<b>Entry:</b> ${entry}<br><b>Exit:</b> ${exit}`;
                    }
                },

                {
                    data: null,
                    className: "text-start text-wrap",
                    render: function(data, type, row) {
                        const category = row.category || "-";
                        const subcategory = row.subcategory || "-";

                        let html = `<div><b>Category:</b> ${category}</div>`;
                        html += `<div><b>Subcategory:</b> ${subcategory}</div>`;

                        return html;
                    }
                },



                {
                    data: "planname",
                    className: "text-center",
                    render: d => d ?? "-"
                },
                {
                    data: "financialyear",
                    className: "text-start text-wrap",
                    render: d => d ?? "-"
                },
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function(row) {
                        let total = row.total_slips ?? 0;
                        let tpara = row.tpara_slips ?? 0;
                        let dropped = row.dropped_slips ?? 0;
                        let pending = row.pending_slips ?? 0;

                        return `
        <div class="d-flex justify-content-center gap-1 flex-wrap">
            <span class="btn btn-primary btn-sm" style="padding:4px 8px; font-size:11px;">
                Total Slip (${total})
            </span>

            <span class="btn btn-success btn-sm" style="padding:4px 8px; font-size:11px;">
                Convert To PARA (${tpara})
            </span>

            <span class="btn btn-danger btn-sm" style="padding:4px 8px; font-size:11px;">
                Dropped (${dropped})
            </span>

            <span class="btn btn-warning btn-sm" style="padding:4px 8px; font-size:11px;">
                Pending (${pending})
            </span>
        </div>
        `;
                    }
                },

                {
                    data: "dropped_slip_percentage",
                    className: "text-center",
                    render: function(d) {
                        let val = d ? parseFloat(d).toFixed(2) : "0.00";

                        return `
            <span
                class="btn btn-success btn-sm"
                style="padding:4px 10px; font-size:12px;"
            >
                ${val}
            </span>
        `;
                    }
                }


                // {
                //     data: "dropped_slip_percentage",
                //     className: "text-center",
                //     render: d => d ? parseFloat(d).toFixed(2) : "0.00"
                // }
            ]
        });

        updatedatatable("en", "DroppedSlipcount");
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#DroppedSlipcount')) {
            $('#DroppedSlipcount').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();

        let titleKey = `${tableId}_title`;
        let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Dropped Slip Count Repor";
        let safeSheetName = reportTitle.substring(0, 31);

        let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

        let headers = [{
                header: dtText["department"] || "Department",
                key: "department"
            },
            {
                header: dtText["Region"] || "Region",
                key: "region"
            },
            {
                header: dtText["District"] || "District",
                key: "district"
            },
            {
                header: dtText["Institution"] || "Institution",
                key: "institution"
            },
            {
                header: dtText["Category"] || "Category",
                key: "category"
            },

            {
                header: dtText["Subcategory"] || "Subcategory",
                key: "subcategory"
            },
            {
                header: "Entry Meet",
                key: "entry"
            },
            {
                header: "Exit Meet",
                key: "exit"
            },
            {
                header: dtText["planname"] || "Audit Quarter",
                key: "planname"
            },
            {
                header: "Financial Year",
                key: "financialyear"
            },
            {
                header: dtText["droppespercentage"] || "Dropped Slip %",
                key: "droppespercentage"
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
                    category: rowData["category"] || "-",
                    subcategory: rowData["subcategory"] || "-",
                    entry: rowData["entrymeetdate"] ? new Date(rowData["entrymeetdate"]).toLocaleDateString() : "-",
                    exit: rowData["exitmeetdate"] ? new Date(rowData["exitmeetdate"]).toLocaleDateString() : "-",
                    auditquartercode: rowData["planname"] || "-",
                    financialyear: rowData["financialyear"] || "-",
                    droppespercentage: rowData["dropped_slip_percentage"] || "-"
                });

            } catch (e) {}
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

        let colCount = headers.length;
        let totalWidth = colCount * 15;
        let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");
        XLSX.utils.sheet_add_aoa(ws, [
            [paddedTitle]
        ], {
            origin: "A1"
        });

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

        var form = $('#droppedslip_form');

        form[0].reset();


        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

        if (sessionrole == distrole) {
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            onchange_region('institution', 'instmappingcode');
            onchange_region('catcode', 'category');
        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
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


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }
        var selectedDept = $('#deptcode').val();

        if (selectedDept && (selectedDept.includes('01') || selectedDept.includes('05'))) {
            $('#subcatdiv').hide();
            $('#subcategory').val('').trigger('change');
        } else {
            $('#subcatdiv').show();
        }


    }
</script>


@endsection