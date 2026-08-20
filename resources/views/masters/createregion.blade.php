@section('content')
@section('title', 'Region Records')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="region_head">Create Region </div>
            <div class="card-body">
                <form id="regionform" name="regionform">
                    <input type="hidden" name="regionid" id="regionid">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3 " id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?>>
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>
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
                                        data-name-ta="துறைகள் எதுவும் இல்லை">No Departments Available</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="regionename" for="regionename">Region English
                                name</label>
                            <input type="text" data-placeholder-key="regionename" class="form-control name"
                                id="regionename" name="regionename" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="regiontname" for="regiontname">Region Tamil
                                name</label>
                            <input type="text" class="form-control name" id="regiontname" name="regiontname"
                                data-placeholder-key="regiontname" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="status">Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusYes" name="statusflag"
                                        value="Y" checked required />
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusNo" name="statusflag"
                                        value="N" required />
                                    <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="regioncode" id="regioncode" value="" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="region_table">Region Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="regiontable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="regionename">  Region Name In English
                                    </th>
                                    <th class="lang align-middle text-center" key="regiontname"> Region Name In Tamil </th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="lang all align-middle text-center" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class='hide_this lang text-center' key="no_data">
                    <center class="lang" key="no_data">No Data Available</center>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<style>

</style>
<!-- Download Button Start -->

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- Download Button Start -->
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script> -->
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- Download Button End -->
<script>
    let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#regionform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

    });

    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'regionform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('region.region_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
                if (json.data && json.data.length > 0) {
                    //console.log(json.data);
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
            }
        });
    }

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#regiontable')) {
            $('#regiontable').DataTable().clear().destroy();
        }

        var table = $('#regiontable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start',
                },
                {
                    data: "regionename",
                    title: columnLabels?.["regionename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.regionename || '-';
                    }
                },
                {
                    data: "regiontname",
                    title: columnLabels?.["regiontname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.regiontname || '-';
                    }
                },
                {
                    data: "statusflag",
                    title: columnLabels?.["statusflag"]?.[window.localStorage.getItem("lang")] || "Status",
                    render: function(data) {
                        //let language = window.localStorage.getItem("lang") || "en"; // Default to English
                        let activeText = arrLang?.[language]?.["active"];
                        let inactiveText = arrLang?.[language]?.["inactive"];

                        return data === 'Y' ?
                            `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                            `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                },

                {
                    data: null,
                    title: columnLabels?.["actions"]?.[language],

                    render: (row) => {

                        if (row.isexists === 'Y') {

                            return `
                                <center>
                                    <a class="btn disabled text-danger">
                                        <i class="ti ti-lock fs-4"></i>
                                    </a>
                                </center>
                            `;
                        }

                        return `
                            <center>
                                <a class="btn editicon editregiondel"
                                id="${row.encrypted_regionid}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },

                    className: "text-center noExport text-wrap"
                }	
		
            ],
            "initComplete": function(settings, json) {
                $("#regiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["regionename", "regiontname", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "regiontable");
    }


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#regiontable')) {
            $('#regiontable').DataTable().clear().destroy();
        }
        renderTable(language);
    }





    // var table = $('#regiontable').DataTable({
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "{{ route('region.region_fetchData') }}",
    //         type: "POST", // Change to GET
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataSrc: function(json) {
    //             if (json.data && json.data.length > 0) {
    //                 $('#tableshow').show();
    //                 $('#usertable_wrapper').show();
    //                 $('#no_data').hide(); // Hide custom "No Data" message
    //                 return json.data;
    //             } else {
    //                 $('#tableshow').hide();
    //                 $('#usertable_wrapper').hide();
    //                 $('#no_data').show(); // Show custom "No Data" message
    //                 return [];
    //             }
    //         },
    //     },
    //     columns: [{
    //             data: null,
    //             render: (_, __, ___, meta) => meta.row + 1, // Serial number column
    //             className: 'text-end' // Align to the right
    //         },
    //         {
    //             data: "deptesname"
    //         },
    //         {
    //             data: "regionename"
    //         },
    //         {
    //             data: "regiontname"
    //         },
    //         {
    //             data: "statusflag"
    //         },
    //         {
    //             data: "encrypted_regionid",
    //             render: (data) =>
    //                 `<center>
    //             <a class="btn editicon editregiondel" id="${data}">
    //                 <i class="ti ti-edit fs-4"></i>
    //             </a>
    //         </center>`
    //         }
    //     ]
    // });

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#regionform").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                regionename: {
                    required: true
                },
                regiontname: {
                    required: true
                },
                statusflag: {
                    required: true
                },
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },
            // messages: {
            //     deptcode: {
            //         required: "Select a department",
            //     },
            //     regionename: {
            //         required: "Enter a Region English name",
            //     },
            //     regiontname: {
            //         required: "Enter a Region Tamil name",
            //     },
            //     statusflag: {
            //         required: "Select a Status action",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#regionform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formData = $('#regionform').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                $.ajax({
                    url: "{{ route('regionhome.region_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form();
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                            // table.ajax.reload();
                            initializeDataTable(window.localStorage.getItem('lang'));

                        } else if (response.error) {
                            console.log(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {

                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] ||
                                    "Error Occured";
                                passing_alert_value('Confirmation', alertMessage,
                                    'confirmation_alert', 'alert_header',
                                    'alert_body', 'confirmation_alert');
                            });
                        }

                    }
                });
            } else {}
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    // Handle Edit Button Click
    $(document).on('click', '.editregiondel', function() {
        const id = $(this).attr('id');
        if (id) {
            reset_form();
            $('#regionid').val(id); // Set the ID field directly

            $.ajax({
                url: "{{ route('region.region_fetchData') }}",
                method: 'POST',
                data: {
                    regionid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('regionform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            populateregionForm(response.data[0]); // Populate form with data
                        } else {
                            alert('region data is empty');
                        }
                    } else {
                        alert('region not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });




    function populateregionForm(region) {
        $('#display_error').hide();
        // change_button_as_update('regionform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#regionename').val(region.regionename);
        $('#regionename').val(region.regionename);
        $('#regiontname').val(region.regiontname);
        $('#deptcode').val(region.deptcode);
        $('#regioncode').val(region.regioncode);
        populateStatusFlag(region.statusflag);
        $('#deptcode').val(region.deptcode).change();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#regionename,#regiontname', '#regioncode', '#regionid').val();

        } else {
            $('#deptcode').val(null).trigger('change');

        }
        // $('#regionform')[0].reset();
        $('#regionform').validate().resetForm();
        // $('#deptcode').val(null).trigger('change');

        changeButtonAction('regionform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        //change_button_as_insert('regionform', 'action', 'buttonaction', 'display_error', '', '');
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>

@endsection
