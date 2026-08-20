@section('content')
@section('title', 'Sub Objection')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="create_subobj">Create Sub Objection </div>
            <div class="card-body">
                <form id="subobjectionform" name="subobjectionform">
                    <!-- <input type="hidden" name="subobjectionid" id="subobjectionid"> -->
                    <input type="hidden" name="orderid" id="orderid">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getobjectionBasedOnDept('')">
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


                        <div class="col-md-4 mb-3 " id="objectionename">
                            <label class="form-label required lang" key="objection_name" for="objectionename">Main
                                Objection
                                Name</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="mainobjectionid"
                                name="mainobjectionid">
                                <option value="" data-name-en="Select Objection Name"
                                    data-name-ta="மறுப்பு பெயரை தேர்ந்தெடுக்கவும்">Select Objection Name</option>
                                <option value="" disabled data-name-en="No Objection Available"
                                    data-name-ta="மறுப்பு கிடைக்கவில்லை">No Objection Available</option>

                                <!-- @if (!empty($mainobj) && count($mainobj) > 0)
@foreach ($mainobj as $mainobject)
<option value="{{ $mainobject->mainobjectionid }}" data-name-en="{{ $mainobject->objectionename }}"
                                                data-name-ta="{{ $mainobject->objectiontname }}">
                                                     subobjectionid
                                                    {{ $mainobject->objectionename }}
                                                </option>
@endforeach
@else
<option disabled>No Objection Name Available</option>
@endif -->
                            </select>
                        </div>




                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="subobjectionename" for="subobjectionename">
                                Sub Objection</label>

                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="subobjectionid"
                                name="subobjectionid" multiple="multiple">
                                <option value="" data-name-en="---Select Major Work Allocation---"
                                    data-name-ta="---பணி ஒதுக்கீடு தேர்வு செய்யவும்---">---Select Major Work
                                    Allocation---</option>
                                <option value="" disabled id=""
                                    data-name-en="No Major Work Allocation Available"
                                    data-name-ta="பணி ஒதுக்கீடு கிடைக்கவில்லை">No Work Allocation Available</option>
                            </select>

                        </div>
                        <!-- <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="subobjectiontname" for="subobjectiontname">
                                Sub Objection Tamil Name</label>
                            <input type="text" class="form-control text_special" id="subobjectiontname"
                                name="subobjectiontname" maxlength="300" placeholder="Sub Objection Tamil name"
                                data-placeholder-key="subobjectiontname" required />
                        </div> -->
                        {{-- <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="status">Active
                                Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input " id="statusyes" name="statusflag"
                                        value="Y" checked required />
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusno" name="statusflag"
                                        value="N" required />
                                    <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save Draft </button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                id="reset_button" onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="subobjdet_table">Sub Objection Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="subobjectiontable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>

                                    <th class="lang align-middle text-center" key="objection_name"> Objection name
                                    </th>
                                    <th class="lang align-middle text-center" key="subobjectionename">SubObjection
                                        English name</th>
                                    <th class="lang align-middle text-center" key="subobjectiontname">SubObjection
                                        Tamil name</th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    {{-- <th class="all lang align-middle text-center    " key="action">Action</th> --}}
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
<script src="../assets/js/forms/select2.init.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Download Button End -->
<script>
    $('#subobjectionid').on('select2:select', function(e) {
        var selectedValue = e.params.data.id;
        if (selectedValue === "") {
            // If the user somehow selects the disabled placeholder, remove it
            $(this).val([]).trigger('change');
        }
    });


    function getobjectionBasedOnDept(deptcode, selecteobjcode = null) {
        const objectionDropdown = $('#mainobjectionid');
        const subobjectionDropdown = $('#subobjectionid');
        const lang = getLanguage();

        objectionDropdown.html(`
        <option value="" data-name-en="Select Objection Name" data-name-ta="மறுப்பு பெயரை தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'மறுப்பு பெயரை தேர்ந்தெடுக்கவும்' : 'Select Objection Name'}
        </option>
    `);

        subobjectionDropdown.html(`
        <option value="" data-name-en="Select Objection Name" data-name-ta="மறுப்பு பெயரை தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'மறுப்பு பெயரை தேர்ந்தெடுக்கவும்' : 'Select Objection Name'}
        </option>
    `);


        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            objectionDropdown.append(`
            <option value="" disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
            </option>
        `);
            subobjectionDropdown.append(`
            <option value="" disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
            </option>
        `);
            change_lang_for_page(lang);
            return;
        }

        $.ajax({
            url: "/getobjectionBasedOnDept",
            type: "POST",
            data: {
                deptcode: deptcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    response.data.forEach(objection => {
                        objectionDropdown.append(`
                        <option value="${objection.mainobjectionid}"
                            data-name-en="${objection.objectionename}"
                            data-name-ta="${objection.objectiontname}"
                            ${objection.mainobjectionid === selecteobjcode ? 'selected' : ''}>
                            ${lang === 'ta' ? objection.objectiontname : objection.objectionename}
                        </option>
                    `);
                    });
                } else {
                    objectionDropdown.append(`
                    <option disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
                    </option>
                `);
                }

                if (response.success && response.subobjection.length > 0) {
                    //alert('h');
                    response.subobjection.forEach(objection => {
                        subobjectionDropdown.append(`
                        <option value="${objection.subobjectionid}"
                            data-name-en="${objection.subobjectionename}"
                            data-name-ta="${objection.subobjectiontname}"
                            ${lang === 'ta' ? objection.subobjectiontname : objection.subobjectionename}
                        </option>
                    `);
                    });
                } else {
                    subobjectionDropdown.append(`
                    <option disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
                    </option>
                `);
                }

                change_lang_for_page(lang); // Update dropdown text after data is loaded
            },
            error: function() {
                // alert('Error fetching objections. Please try again.');
                objectionDropdown.append(`
                    <option disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
                    </option>
                `);

                subobjectionDropdown.append(`
                    <option disabled data-name-en="No Objection Available" data-name-ta="மறுப்பு கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'மறுப்பு கிடைக்கவில்லை' : 'No Objection Available'}
                    </option>
                `);
            }
        });
    }






    let table;
    let dataFromServer = [];
    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#subobjectionform')[0].reset();
        //(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            getobjectionBasedOnDept(sessiondeptcode, '');
        }
        $('#translate').change(function() {
            updateTableLanguage(getLanguage(
                'Y')); // Update the table with the new language by destroying and recreating it
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'subobjectionform');
        });

    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('subobjection.subobjection_fetchData') }}",
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


    // $("#translate").change(function() {
    //     var lang = getLanguage('Y');
    //     // change_lang_for_page(lang);
    //     updateTableLanguage(lang);

    // });

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const objectionColumn = language === 'ta' ? 'objectiontname' : 'objectionename';


        if ($.fn.DataTable.isDataTable('#subobjectiontable')) {
            $('#subobjectiontable').DataTable().clear().destroy();
        }


        table = $('#subobjectiontable').DataTable({
            // "scrollX": true,

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
                    data: objectionColumn,
                    title: columnLabels?.[objectionColumn]?.[language],
                    render: function(data, type, row) {
                        return row[objectionColumn] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap' // Removed col-1
                },
                {
                    data: "subobjectionename",
                    title: columnLabels?.["subobjectionename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subobjectionename || '-';
                    }
                },
                {
                    data: "subobjectiontname",
                    title: columnLabels?.["subobjectiontname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subobjectiontname || '-';
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
                    className: "text-center d-none d-md-table-cell extra-column noExport"
                },
                // {
                //     data: "encrypted_subobjectionid",
                //     title: columnLabels?.["actions"]?.[language],
                //     render: (data) =>
                //         `<center><a class="btn editicon editsubobjectiondel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                //     className: "text-center noExport"
                // }
            ],
            // "columns": [{
            //         data: null,
            //         render: function(data, type, row, meta) {
            //             return meta.row + 1;
            //         }
            //     },
            //     {
            //         data: departmentColumn,
            //         render: function(data, type, row) {
            //             return data ? data : "-";
            //         }
            //     },

            //     {
            //         data: objectionColumn
            //     },
            //     {
            //         data: "subobjectionename"
            //     },
            //     {
            //         data: "subobjectiontname"
            //     },
            //     {
            //         data: "statusflag",
            //         render: (data) => {
            //             const active = language === 'ta' ? 'செயலில்' : 'Active';
            //             const inactive = language === 'ta' ? 'செயலற்றது' : 'Inactive';
            //             if (data === 'Y') {
            //                 return `<button type="button" class="btn btn-primary btn-sm">${active}</button>`;
            //             } else {
            //                 return `<button type="button" class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">${inactive}</button>`;
            //             }
            //         },
            //         className: 'text-center'
            //     },
            //     {
            //         data: "encrypted_subobjectionid",
            //         render: function(data, type, row) {
            //             return `<center><a class="btn editicon editsubobjectiondel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
            //         }
            //     }
            // ],
            // "columnDefs": [{
            //         render: function(data) {
            //             return "<div class='text-wrap width-200'>" + data + "</div>";
            //         },
            //         targets: 1
            //     },
            //     {
            //         render: function(data) {
            //             return "<div class='text-wrap width-100'>" + data + "</div>";
            //         },
            //         targets: [2, 3, 4] // Set width for columns
            //     }
            // ],
            "initComplete": function(settings, json) {
                $("#subobjectiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = [objectionColumn, "subobjectionename", "subobjectiontname", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "subobjectiontable");

    }


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#subobjectiontable')) {
            $('#subobjectiontable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    // var table = $('#subobjectiontable').DataTable({
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     scrollX: true,
    //     ajax: {
    //         url: "{{ route('subobjection.subobjection_fetchData') }}",
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
    //             data: "deptelname"
    //         },
    //         {
    //             data: "objectionename"
    //         },

    //         {
    //             data: "subobjectionename"
    //         },
    //         {
    //             data: "subobjectiontname"
    //         },
    //         {
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
    //             data: "encrypted_subobjectionid",
    //             render: (data) =>
    //                 `<center>
    //             <a class="btn editicon editsubobjectiondel" id="${data}">
    //                 <i class="ti ti-edit fs-4"></i>
    //             </a>
    //         </center>`
    //         }
    //     ]
    // });
    // const $subobjection = $("#subobjectionform");
    // if ($.fn.select2) {
    //     $('.select2').select2({
    //         width: '100%',
    //     })
    // } else {
    //     console.error("⚠ Select2 is NOT LOADED! Check your script order.");
    // }

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#subobjectionform").validate({

            rules: {
                mainobjectionid: {
                    required: true,
                },
                deptcode: {
                    required: true,
                },
                subobjectionename: {
                    required: true
                },
                subobjectiontname: {
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
            //     mainobjectionid: {
            //         required: "Select a Objection Name",
            //     },
            //     deptcode: {
            //         required: "Select a Department",
            //     },
            //     subobjectionename: {
            //         required: "Enter a Objection English name",
            //     },
            //     subobjectiontname: {
            //         required: "Enter a Objection Tamil name",
            //     },
            //     statusflag: {
            //         required: "Select a Status action",
            //     },
            // }
        });

        function InsertForm() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = $('#subobjectionform').serializeArray();
            var deptcode = $('#deptcode').val();
            if ($('#deptcode').prop('disabled')) {

                formData.push({
                    name: 'deptcode',
                    value: deptcode
                });
            }
            var selectedsubobjectionid = $('#subobjectionid').val(); // This returns an array of selected values

            // Filter out empty values (if any) from the selectedsubobjectionid array
            selectedsubobjectionid = selectedsubobjectionid.filter(function(value) {
                return value !== ""; // Filter out empty strings or null values
            });

            // If there are selected subwork allocations, push them into the formData
            if (selectedsubobjectionid.length > 0) {
                selectedsubobjectionid.forEach(function(value) {
                    formData.push({
                        name: 'subobjectionid[]', // Use array notation for multiple values
                        value: value
                    });
                });
            }

            $.ajax({
                url: "{{ route('mappingobjection.subobjectionupdate_mapping') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        reset_form();
                        // getLabels_jsonlayout([{
                        //     id: response.message,
                        //     key: response.message
                        // }], 'N').then((text) => {

                        passing_alert_value('Confirmation', response.message, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                        // });
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

                        // getLabels_jsonlayout([{
                        //         id: ,
                        //         key: errorMessage
                        //     }], "N")
                        //     .then((text) => {
                        //         passing_alert_value("Confirmation", Object.values(text)[
                        //                 0],
                        //             "confirmation_alert", "alert_header",
                        //             "alert_body",
                        //             "confirmation_alert");
                        //     });
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

        }


        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#subobjectionform").valid()) {
                var confirmation = 'Are you sure to Save?';

                document.getElementById("process_button").onclick = function() {
                    InsertForm();
                };

                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');





            } else {

            }
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    // Handle Edit Button Click
    $(document).on('click', '.editsubobjectiondel', function() {
        const id = $(this).attr('id');
        // alert(id);
        if (id) {
            reset_form();
            $('#subobjectionid').val(id); // Set the ID field directly

            $.ajax({
                url: "{{ route('subobjection.subobjection_fetchData') }}",
                method: 'POST',
                data: {
                    subobjectionid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('subobjectionform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            populatesubobjectionForm(response.data[0]); // Populate form with data

                        } else {
                            alert('subobjection data is empty');
                        }
                    } else {
                        alert('subobjection not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });




    function populatesubobjectionForm(subobjection) {
        $('#display_error').hide();
        // change_button_as_update('subobjectionform', 'action', 'buttonaction', 'display_error', '', '');
        $('#deptcode').val(subobjection.deptcode);
        $('#subobjectionename').val(subobjection.subobjectionename);
        $('#subobjectiontname').val(subobjection.subobjectiontname);

        $('#orderid').val(subobjection.orderid);
        $('#deptcode').val(subobjection.deptcode).trigger('change');
        populateStatusFlag(subobjection.statusflag);
        getobjectionBasedOnDept(subobjection.deptcode, subobjection.mainobjectionid)
        // $('#mainobjectionid').val(subobjection.mainobjectionid).change();
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusyes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusno').checked = true;
        }
    }

    function reset_form() {
        // $('#subobjectionform')[0].reset();
        $('#subobjectionform').validate().resetForm();

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#mainobjectionid').val(null).trigger('change');
            $('#subobjectionename,#subobjectiontname,#subobjectionid').val();

        } else {
            $('#deptcode').val(null).trigger('change');

        }
        changeButtonAction('subobjectionform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        // change_button_as_insert('subobjectionform', 'action', 'buttonaction', 'display_error', '', '');
        // getobjectionBasedOnDept('', selecteobjcode = null);
        // $('#deptcode,#subobjectionename, #subobjectiontname,#orderid').val(null).trigger('change');
        //  updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>
<script>
    function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#catcode');
        catcodeDropdown.html('<option value="">Select Category Name</option>');
        if (deptcode) {
            $.ajax({
                url: "{{ route('get.categories') }}",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(category => {
                            catcodeDropdown.append(
                                `<option value="${category.catcode}" ${
                                category.catcode === selectedCatcode ? 'selected' : ''
                            }>${category.catename}</option>`
                            );
                        });
                    } else {
                        catcodeDropdown.append('<option disabled>No Categories Available</option>');
                    }
                },
                error: function() {
                    alert('Error fetching categories. Please try again.');
                }
            });
        }
    }
</script>

@endsection
