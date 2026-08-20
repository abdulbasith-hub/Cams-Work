@section('content')
@section('title', 'Ttle / Heading')
@extends('index2')
@include('common.alert')
@php

    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';
@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">




<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="create_mainobj"> Create MainObjection </div>
            <div class="card-body">
                <form id="mainobjectionform" name="mainobjectionform">
                    <input type="hidden" name="mainobjectionid" id="mainobjectionid">
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
                            <label class="form-label required lang" key="objectionename" for="objectionename">Objection
                                English Name</label>
                            <input type="text" class="form-control text_special" id="objectionename"
                                name="objectionename" maxlength="200" placeholder="Objection English name"
                                data-placeholder-key="objectionename" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="objectiontname" for="objectiontname">Objection
                                Tamil
                                Name</label>
                            <input type="text" class="form-control text_special" id="objectiontname"
                                name="objectiontname" maxlength="200" placeholder="Objection Tamil name"
                                data-placeholder-key="objectiontname" required />
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
                            <input type="hidden" name="mainobjectioncode" id="mainobjectioncode" value="" />
                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save Draft </button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="mainobjdet_table">mainobjection Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="mainobjectiontable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>

                                    <th class="lang align-middle text-center" key="objectionename"> Objection English
                                        name</th>
                                    <th class="lang align-middle text-center" key="objectionename">Objection Tamil
                                        name</th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="all lang" key="action">Action</th>
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
<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
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

        // $('#mainobjectionform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        var lang = getLanguage('');
        initializeDataTable(lang);


    });

    $('#translate').change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'mainobjectionform');
    });


    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('mainobjection.mainobjection_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                // console.log("Success Response:", json);
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

        if ($.fn.DataTable.isDataTable('#mainobjectiontable')) {
            $('#mainobjectiontable').DataTable().clear().destroy();
        }

        $('#mainobjectiontable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,
            "initComplete": function(settings, json) {
                $("#mainobjectiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "data": dataFromServer,

            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "objectionename",
                    title: columnLabels?.["objectionename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.objectionename || '-';
                    }
                },
                {
                    data: "objectiontname",
                    title: columnLabels?.["objectiontname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.objectiontname || '-';
                    }
                },
                {
                    data: "statusflag",
                    title: columnLabels?.["statusflag"]?.[language],
                    render: function(data) {
                        let activeText = arrLang?.[language]?.["active"] || "Active";
                        let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";


                        return data === 'Y' ?
                            `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                            `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell extra-column"
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
                <a class="btn editicon editmainobjectiondel"
                   id="${row.encrypted_mainobjectionid}">
                    <i class="ti ti-edit fs-4"></i>
                </a>
            </center>
        `;
    },

    className: "text-center noExport"
}
            ],

            //             return data === 'Y' ?
            //                 `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
            //                 `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            //         },
            //         className: "text-center d-none d-md-table-cell extra-column noExport"
            //     }, {
            //         data: "encrypted_mainobjectionid",
            //         title: columnLabels?.["actions"]?.[language],
            //         render: (data) =>
            //             `<center><a class="btn editicon editmainobjectiondel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            //         className: "text-center noExport"
            //     }
            // ],



        });

        const mobileColumns = ["objectionename", "objectiontname", "statusflag"];
        setupMobileRowToggle("mainobjectiontable", mobileColumns);
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "mainobjectiontable");
    }


    //     function renderTable(language) {
    //     const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

    //     if ($.fn.DataTable.isDataTable('#mainobjectiontable')) {
    //         $('#mainobjectiontable').DataTable().clear().destroy();
    //     }

    //     $('#mainobjectiontable').DataTable({
    //         "processing": true,
    //         "serverSide": false,
    //         "lengthChange": false,
    //         "initComplete": function(settings, json) {
    //             $("#mainobjectiontable").wrap(
    //                 "<div style='overflow:auto; width:100%;position:relative;'></div>"
    //             );
    //         },
    //         "data": dataFromServer, // Ensure this is defined before calling renderTable

    //         columns: [
    //             {
    //                 data: null,
    //                 render: function(data, type, row, meta) {
    //                     return `<div>
    //                         <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
    //                     </div>`;
    //                 },
    //                 className: 'text-end',
    //                 type: "num"
    //             },
    //             {
    //                 data: departmentColumn,
    //                 title: columnLabels?.[departmentColumn]?.[language],
    //                 render: function(data, type, row) {
    //                     return row[departmentColumn] || '-';
    //                 },
    //                 className: 'text-wrap text-start'
    //             },
    //             {
    //                 data: "objectionename",
    //                 title: columnLabels?.["objectionename"]?.[language],
    //                 className: "d-none d-md-table-cell lang extra-column text-wrap",
    //                 render: function(data, type, row) {
    //                     return row.objectionename || '-';
    //                 }
    //             },
    //             {
    //                 data: "objectiontname",
    //                 title: columnLabels?.["objectiontname"]?.[language],
    //                 className: "d-none d-md-table-cell lang extra-column text-wrap",
    //                 render: function(data, type, row) {
    //                     return row.objectiontname || '-';
    //                 }
    //             },
    //             {
    //                 data: "statusflag",
    //                 title: columnLabels?.["statusflag"]?.[language],
    //                 render: function(data) {
    //                     let activeText = arrLang?.[language]?.["active"] || "Active";
    //                     let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

    //                     return data === 'Y'
    //                         ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
    //                         : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
    //                 },
    //                 className: "text-center d-none d-md-table-cell extra-column"
    //             },
    //             {
    //                 data: "encrypted_mainobjectionid",
    //                 title: columnLabels?.["actions"]?.[language],
    //                 render: (data) =>
    //                     `<center><a class="btn editicon editmainobjectiondel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
    //                 className: "text-center"
    //             }
    //         ]
    //     });

    //     const mobileColumns = ["objectionename", "objectiontname", "statusflag"];
    //     setupMobileRowToggle("mainobjectiontable", mobileColumns);

    //     updatedatatable(language, "mainobjectiontable");
    // }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#mainobjectiontable')) {
            $('#mainobjectiontable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    // var table = $('#mainobjectiontable').DataTable({
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "{{ route('mainobjection.mainobjection_fetchData') }}",
    //         type: "POST", // Change to GET
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataSrc: function(json) {
    //             console.log(json);
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
    //             data: "objectionename"
    //         },
    //         {
    //             data: "objectiontname",
    //             render: function(data, type, row) {
    //                 return data ? data : '-';
    //             }
    //         }, {
    //             data: "statusflag",
    //             render: (data) => {
    //                 if (data === 'Y') {
    //                     return `<button type="button" class="btn btn-primary btn-sm">Active</button>`;
    //                 } else {
    //                     return `<button type="button" class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">Inactive</button>`;
    //                 }
    //             },
    //             className: 'text-center'
    //         },
    //         {
    //             data: "encrypted_mainobjectionid",
    //             render: (data) =>
    //                 `<center>
    //                 <a class="btn editicon editmainobjectiondel" id="${data}">
    //                     <i class="ti ti-edit fs-4"></i>
    //                 </a>
    //             </center>`
    //         }
    //     ]
    // });
    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#mainobjectionform").validate({

            rules: {
                deptcode: {
                    required: true,
                },

                objectionename: {
                    required: true
                },
                objectiontname: {
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

            //     objectionename: {
            //         required: "Enter a Objection English name",
            //     },
            //     objectiontname: {
            //         required: "Enter a Objection Tamil name",
            //     },
            //     statusflag: {
            //         required: "Select a Status action",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#mainobjectionform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formData = $('#mainobjectionform').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                if ($('#action').val() === 'update') {
                    formData.push({
                        name: 'statusflag',
                        value: $('input[name="statusflag"]:checked').val()
                    });
                }


                $.ajax({
                    url: "{{ route('mainobjectionhome.mainobjection_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form();
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation',
                                    Object.values(
                                        text)[0],
                                    'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                            // table.ajax.reload();
                            initializeDataTable(window.localStorage.getItem(
                                'lang'));

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
                                    id: response.Message,
                                    key: response.Message
                                }], "N")
                                .then((text) => {
                                    passing_alert_value("Confirmation", Object
                                        .values(text)[
                                            0],
                                        "confirmation_alert",
                                        "alert_header",
                                        "alert_body",
                                        "confirmation_alert");
                                });
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

        // Handle Edit Button Click
        $(document).on('click', '.editmainobjectiondel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#mainobjectionid').val(id);

                $.ajax({
                    url: "{{ route('mainobjection.mainobjection_fetchData') }}",
                    method: 'POST',
                    data: {
                        mainobjectionid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('mainobjectionform',
                                    'action',
                                    'buttonaction', 'reset_button',
                                    'display_error',
                                    @json($updatebtn),
                                    @json($clearbtn),
                                    @json($update))
                                populatemainobjectionForm(response.data[
                                    0]); // Populate form with data
                            } else {
                                alert('mainobjection data is empty');
                            }
                        } else {
                            alert('mainobjection not found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText ||
                            'Unknown error');
                    }
                });
            }
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });



    function populatemainobjectionForm(mainobjection) {
        $('#display_error').hide();
        //change_button_as_update('mainobjectionform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainobjection.catcode);
        $('#objectionename').val(mainobjection.objectionename);
        $('#objectiontname').val(mainobjection.objectiontname);
        // $('#deptcode').val(mainobjection.deptcode);
        $('#deptcode').val(mainobjection.deptcode).trigger('change');
        $('#mainobjectioncode').val(mainobjection.mainobjectioncode);
        populateStatusFlag(mainobjection.statusflag);
        $('#deptcode').val(mainobjection.deptcode).change();
        $('#catcode').val(mainobjection.catcode).change();
        // getCategoriesBasedOnDept(mainobjection.deptcode, selectedCatcode = mainobjection.catcode);
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    //function populateStatusFlag(statusflag) {
    //    if (statusflag === "Y") {
      //      document.getElementById('statusYes').checked = true;
      //  } else if (statusflag === "N") {
       //     document.getElementById('statusNo').checked = true;
       // }
    //}

    function populateStatusFlag(statusflag) {
    const statusYes = document.getElementById('statusYes');
    const statusNo = document.getElementById('statusNo');

    // Check the current flag
    if (statusflag === "Y") {
        statusYes.checked = true;
    } else if (statusflag === "N") {
        statusNo.checked = true;
    }

    statusNo.disabled = true;
}



    function reset_form() {
        // alert();
        // $('#mainobjectionform')[0].reset();
        // $('#mainobjectionform').validate().resetForm();
        // $('#deptcode').val(null).trigger('change');

        document.getElementById('statusNo').disabled = false;



        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#objectionename,#objectiontname,#mainobjectioncode').val();

        } else {
            $('#deptcode').val(null).trigger('change');

        }
        changeButtonAction('mainobjectionform', 'action', 'buttonaction', 'reset_button',
            'display_error',
            @json($savebtn), @json($clearbtn),
            @json($insert))
        //change_button_as_insert('mainobjectionform', 'action', 'buttonaction', 'display_error', '', '');
        // getCategoriesBasedOnDept('', selectedCatcode = null);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>
<script>
    // function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
    //     const catcodeDropdown = $('#catcode');
    //     catcodeDropdown.html('<option value="">Select Category Name</option>');
    //     if (deptcode) {
    //         $.ajax({
    //             url: "{{ route('get.categories') }}",
    //             type: "POST",
    //             data: {
    //                 deptcode: deptcode,
    //                 _token: '{{ csrf_token() }}'
    //             },
    //             success: function(response) {
    //                 if (response.length > 0) {
    //                     response.forEach(category => {
    //                         catcodeDropdown.append(
    //                             `<option value="${category.catcode}" ${
    //                             category.catcode === selectedCatcode ? 'selected' : ''
    //                         }>${category.catename}</option>`
    //                         );
    //                     });
    //                 } else {
    //                     catcodeDropdown.append('<option disabled>No Categories Available</option>');
    //                 }
    //             },
    //             error: function() {
    //                 alert('Error fetching categories. Please try again.');
    //             }
    //         });
    //     }
    // }
</script>

@endsection
