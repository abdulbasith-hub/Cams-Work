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

</style>


<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="col-12">
    <div class="card card_border">
    <div class="card-header card_header_color">Audit Report Status</div>
    <div class="card-body">
            <form id="auditreportcount_form" name="auditreportcount_form" method='post'>
                <div class="row">
                @csrf

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
                        <select class="form-select mr-sm-2 select2 lang-dropdown"  multiple="multiple" <?php echo $make_dept_disable; ?> id="deptcode"
                            name="deptcode[]">

                            {{-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> --}}


                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                        @if (old('dept', $deptcode) == $department->deptcode) selected @endif
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
    <div class="card-header card_header_color lang" id ="cardHeader" key="">Audit Report Status</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="auditreportcounttable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department</th>
                                <th class="lang" key="">Quarter Type</th>
                                <th class="lang" key="">No.of.Institution Completed Field Audit</th>
                                <th class="lang" key="">No.of.Institution Finalized Audit Report</th>
                                <th class="lang" key="">No.of.Institution Issued Report to Auditee</th>
                                <th class="lang" key="">No.of.Institution Pending to Finalise Report</th>
                                <th class="lang" key="">No.of.Institution Pending to Issue Report to Auditee</th>


                            </tr>
                        </thead>

                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end fs-4">Total:</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>

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
    #auditreportcounttable tfoot th,
    #auditreportcounttable tfoot td {
        background-color: #fff !important;
        color: #000;
        font-weight: bold;
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
    let dataFromServer;

    var distcode = '<?php echo $distcode; ?>'
var regioncode = '<?php echo $regioncode; ?>'
var deptcode = '<?php echo $deptcode; ?>'



    var sessiondeptcode = ' <?php echo $deptcode; ?>';


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


        $('#auditquarter')
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
    }, 200);
});





    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

    $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
    }, "Please select at least one option.");

    var validator = $("#auditreportcount_form").validate({
        ignore: [],
        rules: {
            financialyear: { required: true },

            "auditquarter[]": { selectRequired: true },
            "deptcode[]": { selectRequired: true },

        },
        messages: {
            financialyear: "Select a Financial Year",

            "auditquarter[]": "Select a Audit Quarter",
            "deptcode[]": "Select a Department",

        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('#buttonaction').on('click', function (event) {
        event.preventDefault();
        var form = $('#auditreportcount_form');

        if ($("#auditreportcount_form").valid()) {


        var lang = 'en';
        getInstData(lang); // call only if form is valid
    } else {

    }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
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

    ['#deptcode','#auditquarter']
        .forEach(function (selector) {
            handleAllOption($(selector));
        });
});







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

        var financialyear = $("#financialyear").val();


        if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }

        if ((deptcode.includes("A") || (Array.isArray(deptcode) && deptcode.length > 1))) {

            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A" data-name-en="All" data-name-ta="All">All</option>`);
            dd.select2();
            return;
            }


        let sendData = {
            valuefor: valuefor,
            formname: "auditreportcount"
        };

        if (valuefor === "auditquarter") {
            sendData.deptcode = deptcode;
            sendData.financialyear = financialyear;

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




                    const options = response.data.map(item => {
                        switch (valuefor) {




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

        var deptcode =  $('#deptcode').val() || 'A';
        var auditquarter =  $('#auditquarter').val();
        var financialyear =  $('#financialyear').val();

        $.ajax({
            url: '/fetch_reportcount',
            type: 'POST',
            data: {
                deptcode: deptcode,
                auditquarter : auditquarter,
                financialyear:financialyear,
                formname: 'auditreportcount',

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

                var response = JSON.parse(xhr.responseText);
        $("#loader").hide();

                if (xhr.status === 404) {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }
    $('#translate').change(function() {
        const lang = getLanguage('Y'); // Store language selection
        updateTableLanguage(
            lang); // Update the table with the new language by destroying and recreating it

    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditreportcounttable')) {
            $('#auditreportcounttable').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#auditreportcounttable')) {
            $('#auditreportcounttable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#auditreportcounttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#auditreportcounttable").wrap(
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
                    data: 'deptesname',
                    title: columnLabels?.["deptesname"]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row.deptesname || '-';
                    },
                    className: 'text-wrap text-start'
                },

                {
                    data: 'planname',
                    title: columnLabels?.["planname"]?.[language] || "Quarter type",
                    render: function(data, type, row) {
                        return row.planname || '-';
                    },
                    className: 'text-wrap text-center'
                },
                {
                    data: 'auditcompleted',
                    title: columnLabels?.["auditCompleted"]?.[language],
                    render: function(data, type, row) {
                        return row.auditcompleted || '-';
                    },
                    className: 'text-center text-wrap'
                },
                {
                    data: 'reportfinalised',
                    title: columnLabels?.["reportFinalised"]?.[language],
                    render: function(data, type, row) {
                        return row.reportfinalised || '-';
                    },
                    className: 'text-center text-wrap'
                },
                {
                    data: 'issuedreport',
                    title: columnLabels?.["issuedReport"]?.[language],
                    render: function(data, type, row) {
                        return row.issuedreport || '-';
                    },
                    className: 'text-center text-wrap'
                },
                {
                    data: 'pendingtofinalise',
                    title: columnLabels?.["pendingtofinalise"]?.[language],
                    render: function(data, type, row) {
                        return row.pendingtofinalise || '-';
                    },
                    className: 'text-center text-wrap'
                },
                {
                    data: 'pendingtoissue',
                    title: columnLabels?.["pendingtoissue"]?.[language],
                    render: function(data, type, row) {
                        return row.pendingtoissue || '-';
                    },
                    className: 'text-center text-wrap'
                }




            ],
            footerCallback: function(row, data, start, end, display) {
        let api = this.api();

        // helper function to parse int safely
        let intVal = function(i) {
            return typeof i === 'string'
                ? i.replace(/[\$,]/g, '') * 1
                : typeof i === 'number'
                ? i
                : 0;
        };

        // Loop through each numeric column (3 → last index of your numeric columns)
        [3, 4, 5, 6, 7].forEach(function(colIdx) {
            let total = api
                .column(colIdx, { page: 'current' })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Update footer cell
            $(api.column(colIdx).footer()).html(total);
        });
    }
        });
           const mobileColumns = [
            "department", "auditCompleted", "reportFinalised",
            "issuedReport"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "auditreportcounttable");
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
            $('#auditquarter').val(['']).select2();
        } else if (sessionrole == regionrole) {
            $('#auditquarter').val(['']).select2();

        } else if (sessionrole == headofficerole) {
            $('#auditquarter').val(['']).select2();

        } else if (sessionrole == dgarole) {
            $('#deptcode').val('').select2();
            $('#auditquarter').val(['']).select2();

        } else if (sessionrole == adminrole) {
            $('#auditquarter').val(['']).select2();

        }


    });







    function reset_form() {
        $("#loader").hide();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        $('#auditquarter').val(['']).select2();



        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        $('#auditreportcount_form')[0].reset();
        $('#financialyear').val('').select2();

        var form = $('#auditreportcount_form');

            form[0].reset();


            // Reset validation messages
            if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
            }


        if (sessionrole == distrole) {
            if ($('#deptcode').hasClass("select2-hidden-accessible")) {
                $('#deptcode').select2('destroy');
            }
            $('#deptcode').select2();


            getInstData(lang);
        } else if (sessionrole == regionrole) {
            if ($('#deptcode').hasClass("select2-hidden-accessible")) {
                $('#deptcode').select2('destroy');
            }
            $('#deptcode').select2();



        } else if (sessionrole == headofficerole) {
            if ($('#deptcode').hasClass("select2-hidden-accessible")) {
                $('#deptcode').select2('destroy');
            }
            $('#deptcode').select2();



        } else if (sessionrole == dgarole) {

            if ($('#deptcode').hasClass("select2-hidden-accessible")) {
                $('#deptcode').select2('destroy');
            }
            $('#deptcode').select2();
            $('#deptcode').val('').select2();

        } else if (sessionrole == adminrole) {
            $('#deptcode').select2('destroy');
            $('#deptcode').select2(null);
            $('#deptcode').select2();


          }

    }
</script>


@endsection