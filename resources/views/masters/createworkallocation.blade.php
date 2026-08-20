@section('content')

@section('title', 'Work Allocation Report')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    //print_r($sessionchargedel);
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="workall_head">Work Allocation</div>
            <div class="card-body">
                <form id="workallocationform" name="workallocationform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
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


                        <!-- <div class="col-md-4 mb-3">
                                                        <label class="form-label required lang" key="category" for="category">Category</label>
                                                        <select class="form-select mr-sm-2" id="category" name="category">
                                                            <option value=''>Select category</option>

                                                        </select>
                                                    </div> -->

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="majorworkallocationtypeename"
                                for="ename">Work
                                Allocation English Name</label>
                            <input type="text" class="form-control text_special" id="ename" name="ename"
                                maxlength="200" required data-placeholder-key="workallocationename">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="majorworkallocationtypetname"
                                for="fname">Work
                                Allocation Tamil Name</label>
                            <input type="text" class="form-control text_special" id="fname" name="fname"
                                maxlength="200" placeholder="Enter Work Allocation Tamil Name" required
                                data-placeholder-key="workallocationtname">
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="status" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="status" id="statusNo"
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
                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="workallocation" id="workallocation" value="" />

                            <button class="btn button_save mt-3 lang" key="savebtn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clearbtn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="workall_table">Work Allocation Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="wrokallocationtable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="majorworkallocationtypeename">Work
                                        Allocation English Name</th>
                                    <th class="lang align-middle text-center" key="majorworkallocationtypetname">Work
                                        Allocation Tamil Name</th>
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

    $(document).ready(function() {
        $('#workallocationform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'workallocationform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('workallocationtype.workallocationtype_fetchData') }}",
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

        if ($.fn.DataTable.isDataTable('#wrokallocationtable')) {
            $('#wrokallocationtable').DataTable().clear().destroy();
        }

        table = $('#wrokallocationtable').DataTable({
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
                    data: "majorworkallocationtypeename",
                    title: columnLabels?.["majorworkallocationtypeename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.majorworkallocationtypeename || '-';
                    }
                },
                {
                    data: "majorworkallocationtypetname",
                    title: columnLabels?.["majorworkallocationtypetname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.majorworkallocationtypetname || '-';
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
                                    <a class="btn disabled text-danger" >
                                        <i class="ti ti-lock fs-4"></i>
                                    </a>
                                </center>
                            `;
                        }

                        return `
                            <center>
                                <a class="btn editicon editworkallocationdel"
                                id="${row.encrypted_majorworkallocationtypeid}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },
                    className: "text-center noExport"
                }
            ],

            "initComplete": function(settings, json) {
                $("#wrokallocationtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["majorworkallocationtypeename", "majorworkallocationtypetname", "statusflag"];
        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "wrokallocationtable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#wrokallocationtable')) {
            $('#wrokallocationtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }



















    // var table = $('#wrokallocationtable').DataTable({

    //     scrollX: true,
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "{{ route('workallocationtype.workallocationtype_fetchData') }}",
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
    //         // {
    //         //     //data: "catename"
    //         // },
    //         {
    //             data: "majorworkallocationtypeename"
    //         },
    //         {
    //             data: "majorworkallocationtypetname"
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
    //             data: "encrypted_majorworkallocationtypeid",
    //             render: (data) =>
    //                 `<center>
    //             <a class="btn editicon editworkallocationdel" id="${data}">
    //                 <i class="ti ti-edit fs-4"></i>
    //             </a>
    //         </center>`
    //         }
    //     ]
    // });



    // //     function getCategoriesBasedOnDept() {
    //     var deptcode = $('#deptcode').val(); // Get the selected deptcode

    //     // If a department is selected, make an AJAX request to fetch categories
    //     if (deptcode) {
    //         $.ajax({
    //             url: '/getCategoriesBasedOnDept',  // URL without deptcode in the URL path
    //             type: 'GET',
    //             data: {
    //                 deptcode: deptcode,
    //                 _token: $('meta[name="csrf-token"]').attr('content'),
    //              },  // Send deptcode as part of the request data
    //             dataType: 'json',
    //             success: function(data) {
    //                 // Clear the category dropdown before appending new options
    //                 $('#category').empty();
    //                 $('#category').append('<option value="">Select category</option>');  // Reset category dropdown

    //                 // Check if categories are returned
    //                 if (data.length > 0) {
    //                     $.each(data, function(index, category) {
    //                         // Add each category as an option in the category dropdown
    //                         $('#category').append('<option value="' + category.catcode + '">' + category.catename + '</option>');
    //                     });
    //                 } else {
    //                     // If no categories are available for the selected department
    //                     $('#category').append('<option disabled>No Categories Available</option>');
    //                 }
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error('Error fetching categories:', error);
    //             }
    //         });
    //     } else {
    //         // If no department is selected, reset the category dropdown
    //         $('#category').empty();
    //         $('#category').append('<option value="">Select category</option>');
    //     }
    // }

    //Function to get categories based on the selected department
    // function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
    //    // alert('te');
    //     const catcodeDropdown = $('#category');
    //     catcodeDropdown.html('<option value="">Select Category Name</option>');
    //     if (deptcode) {
    //         $.ajax({
    //             url: "/getcategoriesbasednndept",
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

    // $(document).on('click','.edit',function(){
    //     var $row = JSON.parse($(this).attr('data-edit'));
    //    // console.log($row);
    //    $('#deptcode').val($row.deptcode);
    //    $('#ename').val($row.majorworkallocationtypeename);
    //    $('#fname').val($row.majorworkallocationtypetname);
    //    $('#deptcode').val($row.deptcode);

    // })
    //var lang = getLanguage();



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#workallocationform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                // category: {
                //     required: true
                // },
                ename: {
                    required: true
                },
                fname: {
                    required: true
                },
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
            //     // category: {
            //     //     required: "Select a category",
            //     // },
            //     ename: {
            //         required: "Enter work allocation english name",
            //     },
            //     fname: {
            //         required: "Enter work allocation tamil name",
            //     },
            //     status: {
            //         required: "Select a status",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#workallocationform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#workallocationform').serializeArray();

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
                // formData.push({
                //     name: "lang",
                //     value: getLanguage('N')
                // });
                console.log(formData);
                $.ajax({
                    url: "{{ route('workallocationhome.workallocation_insertupdate') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                            //  table.ajax.reload();
                            initializeDataTable(window.localStorage.getItem('lang'));


                        } else if (response.error) {
                            // Handle errors if needed
                            // console.log(response.error);
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

            } else {

            }



        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    function workallocationForm(workallocation) {
        $('#display_error').hide();
        // change_button_as_update('workallocationform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainobjection.catcode);
        $('#ename').val(workallocation.majorworkallocationtypeename);
        $('#fname').val(workallocation.majorworkallocationtypetname);
        // $('#deptcode').val(workallocation.deptcode);
        $('#workallocation').val(workallocation.encrypted_majorworkallocationtypeid);
        //alert(id);
        populateStatusFlag(workallocation.statusflag);
        // $('#deptcode').val(workallocation.deptcode);
        $('#deptcode').val(workallocation.deptcode).trigger('change');
        // $('#deptcode').val(workallocation.deptcode).change();
        // $('#category').val(workallocation.catcode).change();
        //  $('#deptcode').val(workallocation.deptcode).change();
        // getCategoriesBasedOnDept(workallocation.deptcode, selectedCatcode = workallocation.catcode);
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editworkallocationdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#workallocation').val(id);
            //  console.log($('#workallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('workallocationtype.workallocationtype_fetchData') }}",
                method: 'POST',
                data: {
                    majorworkallocationtypeid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('workallocationform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            workallocationForm(response.data[0]); // Populate form with data
                        } else {
                            alert('workallocation data is empty');
                        }
                    } else {
                        alert('workallocation not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


   // function populateStatusFlag(statusflag) {
       // if (statusflag === "Y") {
         //   document.getElementById('statusYes').checked = true;
        //} else if (statusflag === "N") {
        //    document.getElementById('statusNo').checked = true;
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
        // $('#workallocationform')[0].reset();
        // $('#workallocationform').validate().resetForm();
        // $('#deptcode').val(null).trigger('change');
        document.getElementById('statusNo').disabled = false;


        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#ename, #fname').val();

        } else {
            $('#deptcode').val(null).trigger('change');
        }
        // change_button_as_insert('workallocationform', 'action', 'buttonaction', 'display_error', '', '');
        changeButtonAction('workallocationform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        // getCategoriesBasedOnDept('', selectedCatcode = null);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
