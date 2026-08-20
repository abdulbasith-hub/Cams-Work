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

    #legacycounttable tfoot th,
    #legacycounttable tfoot td{
    background-color: #fff !important;
    color: #000;
    font-weight: bold;
}


.wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* Adjust as needed */
    }

</style>


<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Legacy Count</div>
        <div class="card-body">
            <form id="legacycountform" name="legacycountform" method='post'>
                <div class="row">
                 @csrf
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





                    {{-- <div class="col-md-4 mt-3" id="nextQuarterDateWrapper">
                        <label class="form-label d-block mb-2" for="nextQuarterDate">Select Date</label>
                        <div class="input-group mx-auto date" id="datepickerNextQuarter">
                            <input type="text" class="form-control" id="logindate" name="logindate"
                                placeholder="dd-mm-yyyy">
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div> --}}








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
        <div class="card-header card_header_color lang" key="">List of User's </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="legacycounttable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang text-center" key="s_no">S.No</th>
                                <th class="lang text-center" key="department">Department</th>
                                <th class="lang text-center" key="">Region</th>
                                <th class="lang text-center" key="">District</th>
                                <th class="lang text-center" key="">As on Before</th>
                                <th class="lang text-center" key="">Yesterday</th>
                                <th class="lang text-center" key="">Today</th>
                                <th class="lang text-center" key="">Total</th>


                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-center fs-4">Total:</th>
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









// function setToday() {
//     let today = new Date();
//     let dd = String(today.getDate()).padStart(2, '0');
//     let mm = String(today.getMonth() + 1).padStart(2, '0');
//     let yyyy = today.getFullYear();
//     let formattedDate = dd + '-' + mm + '-' + yyyy;

//     $('#logindate').val(formattedDate);
// }

// $(document).ready(function () {
//     setToday();

//     $('#logindate').datepicker({
//         format: 'dd-mm-yyyy',
//         autoclose: true,
//         todayHighlight: true,
//         clearBtn: true,
//         endDate: new Date()
//     }).on('clearDate', function () {
//         setToday();
//     });
// });












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
    // var logindate = $('#logindate').val();


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
           // logindate : logindate,
            valuefor: valuefor,
            formname: 'legacycount'

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





jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
    }, "Please select at least one option.");


        var validator = $("#legacycountform").validate({

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

    $('#buttonaction').on('click', function (event) {
        event.preventDefault();
        var form = $('#legacycountform');

        if ($("#legacycountform").valid()) {


        var lang = 'en';
        getInstData(lang);
    } else {

    }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});







    function getInstData(lang) {

        var deptcode =  $('#deptcode').val() || 'A';
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val() || 'A';
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val() || 'A';
      //  var logindate = $('#logindate').val();

        $.ajax({
            url: '/getlegacycount',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
              //  logindate : logindate


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
    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(
            lang);

    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#legacycounttable')) {
            $('#legacycounttable').DataTable().clear().destroy();
        }
        renderTable(language, dataFromServer);
    }

    function renderTable(language) {


       const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
       const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';
        const desigColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#legacycounttable')) {
            $('#legacycounttable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#legacycounttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#legacycounttable").wrap(
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
                    title: columnLabels?.["deptesname"]?.[language],
                    render: function(data, type, row) {
                        return row.deptesname || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: null,
                    title: columnLabels?.[regionColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[regionColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.[districtColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[districtColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: 'before_yesterday',
                    title: columnLabels?.["before_yesterday"]?.[language],
                    render: function(data, type, row) {
                        return row.before_yesterday || '-';
                    },
                    className: 'text-wrap text-end'
                },
                {
                    data: 'yesterday',
                    title: columnLabels?.["yesterday"]?.[language],
                    render: function(data, type, row) {
                        return row.yesterday || '-';
                    },
                    className: 'text-wrap text-end'
                },

                {
                    data: 'today',
                    title: columnLabels?.["today"]?.[language],
                    render: function(data, type, row) {
                        return row.today || '-';
                    },
                    className: 'text-wrap text-end'
                },
                {
                data: null,
                title: "Total",
                render: function(data, type, row) {
                    let b = parseInt(row.before_yesterday) || 0;
                    let y = parseInt(row.yesterday) || 0;
                    let t = parseInt(row.today) || 0;
                    return b + y + t;
                },
                className: 'text-wrap text-end'
            }



            ],
            footerCallback: function (row, data, start, end, display) {
    var api = this.api();

    // ---- PAGE TOTAL only visible rows ----
    var pageTotal = 0;
    display.forEach(function (idx) {
        let rowData = data[idx];
        let b = parseInt(rowData.before_yesterday) || 0;
        let y = parseInt(rowData.yesterday) || 0;
        let t = parseInt(rowData.today) || 0;
        pageTotal += (b + y + t);
    });

    // ---- GRAND TOTAL ----
    var grandTotal = data.reduce(function (sum, row) {
        let b = parseInt(row.before_yesterday) || 0;
        let y = parseInt(row.yesterday) || 0;
        let t = parseInt(row.today) || 0;
        return sum + (b + y + t);
    }, 0);

    // Update footer
    $(api.column(0).footer()).html(`<strong>Overall Total: </strong>`)
        .removeClass('text-center text-start')
        .addClass('text-end');

    $(api.column(7).footer()).html(`<strong>${grandTotal}</strong>`);
},


        });
           const mobileColumns = [
            "department", "auditCompleted", "reportFinalised",
            "issuedReport"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "legacycounttable");
        // console.log("DataTable initialized successfully.");
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
        $('#deptcode option').prop('disabled', false);

        var form = $('#legacycountform');

        form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }


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
