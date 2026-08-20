@section('content')

@section('title', 'Sub Work Allocation Report')
@extends('index2')
@include('common.alert')

@php
    $sessionchargedel = session('charge');
    // print_r($sessionchargedel);
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<!-- <style>
            .dataTables_scrollBody thead tr{
            visibility: collapse !important;
        }
        </style> -->

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">





<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="subwork_head">Subwork Allocation</div>
            <div class="card-body">
                <form id="subworkallocation" name="subworkallocation">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getworkallocationBasedOnDept(this.value)">

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


                        <div class="col-md-4 mb-3" id="workdiv">
                            <label class="form-label required lang " key="workall_head" for="workcode">Major Work
                                Allocation</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="workcode" name="workcode">
                                <option value="" data-name-en="---Select Major Work Allocation---"
                                    data-name-ta="---பணி ஒதுக்கீடு தேர்வு செய்யவும்---">---Select Major Work
                                    Allocation---</option>
                                <option value="" disabled id=""
                                    data-name-en="No Major Work Allocation Available"
                                    data-name-ta="பணி ஒதுக்கீடு கிடைக்கவில்லை">No Work Allocation Available</option>
                            </select>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang " key="subworkallocationtypeename"
                                for="sub_ename">Subwork
                                Allocation English Name</label>
                            <input type="text" class="form-control text_special" id="sub_ename" maxlength="300"
                                name="sub_ename" data-placeholder-key="subwork_placeholderEname" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="subworkallocationtypetname"
                                for="tname">Subwork
                                Allocation Tamil Name</label>
                            <input type="text" class="form-control text_special" id="tname" maxlength="300"
                                name="tname" data-placeholder-key="subwork_placeholderTname" required>
                        </div>

                        <!-- <div class="col-md-4 mb-3">
                                    <label class="form-label required lang" key="orderid" for="orderid">Order</label>
                                    <input type="text" class="form-control" id="orderid" name="orderid" placeholder="Order"
                                        required />
                                </div> -->


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="status" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input " type="radio" name="status" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                                            <div class="col-md-4  mx-auto">
                                                <button class="btn btn-success mt-3" type="submit"> Submit </button>
                                                <button class="btn btn-danger mt-3" type="submit"> Cancel </button>
                                            </div>
                                        </div> -->
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="subworkallocationid" id="subworkallocationid"
                                value="" />

                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                style="height:35px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="subwork_table">Subwork Allocation Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="subworkallocationtable"
                            class="table w-100 table-striped table-bordered display  datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="workall_head">Work Allocation</th>
                                    <th class="lang align-middle text-center" key="subworkallocationtypeename">Subwork
                                        Allocation
                                        English Name</th>
                                    <th class="lang align-middle text-center" key="subworkallocationtypetname">Subwork
                                        Allocation
                                        Tamil Name</th>
                                    <!-- <th class="lang align-middle text-center" key="orderid">Order</th> -->
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="all lang align-middle text-center" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
<!-- Include jQuery and Bootstrap -->



<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

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
    var lang;
    $(document).ready(function() {
        // $('#subworkallocation')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        lang = getLanguage('');



        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            getworkallocationBasedOnDept(sessiondeptcode, '');
        }


    });

    initializeDataTable(lang);
    $("#translate").change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'subworkallocation');
    });










    function getworkallocationBasedOnDept(deptcode, selectedWorkCode = null) {
        const workallocationDropdown = $('#workcode');


        // Reset dropdown with default option
        workallocationDropdown.html(`
            <option value="" data-name-en="---Select Major Work Allocation---" data-name-ta="---பணி ஒதுக்கீடு தேர்வு செய்யவும்---">
                ${lang === 'ta' ? '---பணி ஒதுக்கீடு தேர்வு செய்யவும்---' : '---Select Major Work Allocation---'}
            </option>
        `);

        if (!deptcode) {
            workallocationDropdown.append(`
                <option value="" disabled data-name-en="No Work Allocation Available" data-name-ta="முதன்மை பணி இல்லை">
                    ${lang === 'ta' ? 'முதன்மை பணி இல்லை' : 'No Work Allocation Available'}
                </option>
            `);

            return;
        }


        $.ajax({
            url: "/getworkallocationBasedOnDept",
            type: "POST",
            data: {
                deptcode: deptcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                workallocationDropdown.empty();

                workallocationDropdown.append(`
                <option value="" data-name-en="---Select Major Work Allocation---" data-name-ta="---பணி ஒதுக்கீடு தேர்வு செய்யவும்---">
                    ${lang === 'ta' ? '---பணி ஒதுக்கீடு தேர்வு செய்யவும்---' : '---Select Major Work Allocation---'}
                </option>-
           `);

                if (response.success && response.data.length > 0) {
                    response.data.forEach(workallocation => {
                        workallocationDropdown.append(`
                        <option value="${workallocation.majorworkallocationtypeid}"
                            data-name-en="${workallocation.majorworkallocationtypeename}"
                            data-name-ta="${workallocation.majorworkallocationtypetname}"
                            ${selectedWorkCode == workallocation.majorworkallocationtypeid ? 'selected' : ''}>
                            ${lang === 'ta' ? workallocation.majorworkallocationtypetname : workallocation.majorworkallocationtypeename}
                        </option>
                    `);
                    });
                } else {
                    workallocationDropdown.append(`
                    <option disabled data-name-en="No Work Allocation Available" data-name-ta="முதன்மை பணி இல்லை">
                        ${lang === 'ta' ? 'முதன்மை பணி இல்லை' : 'No Work Allocation Available'}
                    </option>
                `);
                }


            },
            error: function(xhr, status, error) {
                console.error("Error fetching work allocations:", error);
                alert('Error fetching work allocations. Please try again.');
            }
        });
    }

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('subworkallocationtype.subworkallocationtype_fetchData') }}",
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
        const workallocationColumn = language === 'ta' ? 'majorworkallocationtypetname' :
            'majorworkallocationtypeename';

        if ($.fn.DataTable.isDataTable('#subworkallocationtable')) {
            $('#subworkallocationtable').DataTable().clear().destroy();
        }

        table = $('#subworkallocationtable').DataTable({
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
                // {
                //     data:workallocationColumn,
                //     title: columnLabels?.["workallocationColumn"]?.[language],
                //     className: "text-wrap text-start",
                //     render: function (data, type, row) {
                //         return row.workallocationColumn || '-';
                //     }
                // },
                {
                    data: workallocationColumn,
                    title: columnLabels?.[workallocationColumn]?.[language],
                    render: function(data, type, row) {
                        return row[workallocationColumn] || '-';
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap", // Removed col-1
                },
                {
                    data: "subworkallocationtypeename",
                    title: columnLabels?.["subworkallocationtypeename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subworkallocationtypeename || '-';
                    }
                },
                {
                    data: "subworkallocationtypetname",
                    title: columnLabels?.["subworkallocationtypetname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subworkallocationtypetname || '-';
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
                 {
		data: null,
		title: columnLabels?.["actions"]?.[language],
		render: function (data, type, row) {

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
					<a class="btn editicon editsubworkallocation" id="${row.encrypted_subworkallocationtypeid}">
						<i class="ti ti-edit fs-4"></i>
					</a>
				</center>
			`;
		},
		className: "text-center noExport"
   }
            ],
            "initComplete": function(settings, json) {
                $("#subworkallocationtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = [workallocationColumn, "subworkallocationtypeename", "subworkallocationtypetname",
            "statusflag"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "subworkallocationtable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#subworkallocationtable')) {
            $('#subworkallocationtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    // var table = $('#subworkallocationtable').DataTable({
    //     //bAutoWidth : false,
    //     // scrollX: true,
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "{{ route('subworkallocationtype.subworkallocationtype_fetchData') }}",
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
    //             data: "subworkallocationtypeename"
    //         },
    //         {
    //             data: "subworkallocationtypetname"
    //         },
    //         // {
    //         //     data: "orderid"
    //         // },

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
    //             data: "encrypted_subworkallocationtypeid",
    //             render: (data) =>
    //                 `<center>
    //             <a class="btn editicon editsubworkallocation" id="${data}">
    //                 <i class="ti ti-edit fs-4"></i>
    //             </a>
    //         </center>`
    //         }
    //     ]
    // });



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#subworkallocation").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                workcode: {
                    required: true,
                },

                sub_ename: {
                    required: true
                },
                tname: {
                    required: true
                },
                // orderid: {
                //     required: true
                // },
                status: {
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
            //     workcode: {
            //         required: "Select a Workallocation",
            //     },
            //     ename: {
            //         required: "Enter subwork allocation english Name",
            //     },
            //     tname: {
            //         required: "Enter subwork allocation tamil Name",
            //     },
            //     // orderid: {
            //     //     required: "Enter a order",
            //     // },
            //     status: {
            //         required: "Select a status",
            //     },
            // }

        });

        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#subworkallocation").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#subworkallocation').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
		if ($('#action').val() === 'update') {
                    formData.push({
                        name: 'status',
                        value: $('input[name="status"]:checked').val()
                    });
                }


                $.ajax({
                    url: "{{ route('subworkallocation.subworkallocation_insertupdate') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            // passing_alert_value('Confirmation', response.message,
                            //     'confirmation_alert', 'alert_header', 'alert_body',
                            //     'confirmation_alert');
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
                            // Handle errors if needed
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

            } else {

            }

        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    function subworkallocationForm(subworkallocation) {
        $('#display_error').hide();
        // change_button_as_update('subworkallocation', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainworkallocation.catcode);
        // $('#subworkid').val(subworkallocation.majorworkallocationtypeid);
        //$('#orderid').val(subworkallocation.orderid);
        $('#sub_ename').val(subworkallocation.subworkallocationtypeename);
        $('#tname').val(subworkallocation.subworkallocationtypetname);
        // $('#deptcode').val(workallocation.deptcode);
        $('#subworkallocationid').val(subworkallocation.encrypted_subworkallocationtypeid);
        //alert(id);
        populateStatusFlag(subworkallocation.statusflag);
        // $('#deptcode').val(subworkallocation.deptcode);
        // $('#deptcode').val(subworkallocation.deptcode).trigger('change');
        $('#deptcode').val(subworkallocation.deptcode).select2();

        getworkallocationBasedOnDept(subworkallocation.deptcode, subworkallocation.majorworkallocationtypeid);
        //   $('#deptcode').val(workallocation.deptcode).change();
        // $('#category').val(workallocation.catcode).change();
        // $('#deptcode').val(workallocation.deptcode).change();
        // getCategoriesBasedOnDept(workallocation.deptcode, selectedCatcode = workallocation.catcode);
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }


    //function populateStatusFlag(statusflag) {
     //   if (statusflag === "Y") {
      //      document.getElementById('statusYes').checked = true;
      //  } else if (statusflag === "N") {
      //      document.getElementById('statusNo').checked = true;
      //  }
   // }

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
        // Reset the form fields to their initial values

        document.getElementById('statusNo').disabled = false;


        $('#subworkallocation').validate().resetForm();

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#subwork_engname,#subwork_tngname,#subworkallocationid').val();
            $('#workcode').val(null).trigger('change');

        } else {

            $('#deptcode').val(null).trigger('change');
            $('#subwork_engname,#subwork_tngname,#subworkallocationid').val();

        }

        changeButtonAction('subworkallocation', 'action', 'buttonaction', 'display_error', '',
            @json($savebtn), @json($clearbtn), @json($insert));

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }


    $(document).on('click', '.editsubworkallocation', function() {
        const id = $(this).attr('id');
        console.log(id);
        if (id) {
            reset_form();
            $('#subworkallocationid').val(id);
            // console.log($('#subworkallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('subworkallocationtype.subworkallocationtype_fetchData') }}",
                method: 'POST',
                data: {
                    subworkallocationtypeid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {

                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('subworkallocation', 'action', 'buttonaction',
                                'display_error', '', @json($updatebtn),
                                @json($clearbtn), @json($update));

                            subworkallocationForm(response.data[0]); // Populate form with data
                        } else {
                            alert('subworkallocation data is empty');
                        }
                    } else {
                        alert('subworkallocation not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });
</script>

@endsection
