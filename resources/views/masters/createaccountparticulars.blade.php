@section('content')

@section('title', 'Account Particular Report')
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
            <div class="card-header card_header_color lang" key="account_head">Create Account Particular</div>
            <div class="card-body">
                <form id="accountparticularform" name="accountparticularform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?>   onchange="getCategoriesBasedOnDept(this.value,'')";>
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


                    

                        <div class="col-md-4 mb-1">
                        <label class="form-label lang required" key="cat_name"
                            for="validationDefault01">Category</label>

                        <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"  onchange="getsubcategoryBasedOnCategories(this.value,'')">
                            <option value="" data-name-en="---Select Category---"
                                data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                <option value="" disabled id="" data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                        

                        </select>
                    </div>


                            <div class="col-md-4 mb-1 subcatdiv hide_this">
                                <label class="form-label lang required" key="account_subcat"
                                    for="subcategory">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory" name="subcategory"
                                    onchange="">
                                    <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>
                              

                                </select>
                            </div>                           

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="accountename" for="accountename">Account Particular English Name</label>
                            <input type="text" class="form-control text_special" id="accountename" name="accountename"
                                maxlength="50" required data-placeholder-key="accountename">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="accounttname" for="accounttname">Account Particular Tamil Name</label>
                            <input type="text" class="form-control text_special" id="accounttname" name="accounttname"
                                maxlength="50" placeholder="Enter Account Particular Tamil Name" required
                                data-placeholder-key="accounttname">
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="statusflag" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="statusflag" id="statusNo"
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
                            <input type="hidden" name="accountparticularsid" id="accountparticularsid" value="" />

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
            <div class="card-header card_header_color lang" key="account_table">Account Particular Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="accountparticulatable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="cat_name">Category</th>
                                    <th class="lang align-middle text-center" key="account_subcat">Sub Category</th>

                                    <th class="lang align-middle text-center" key="accountename">Account Particular English Name</th>
                                    <th class="lang align-middle text-center" key="accounttname">Account Particular Tamil Name</th>
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
        $('#accountparticularform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'accountparticularform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('accountparticular.accountparticular_fetchdata') }}",
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
        const categoryColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcategorytColumn = language === 'ta' ? 'subcattname' : 'subcatename';

        if ($.fn.DataTable.isDataTable('#accountparticulatable')) {
            $('#accountparticulatable').DataTable().clear().destroy();
        }


        table = $('#accountparticulatable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [
        {
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
            render: function (data, type, row) {
                return row[departmentColumn] || '-';
            },
            className: 'text-wrap text-start' // Removed col-1
        },
        {
            data: categoryColumn,
            title: columnLabels?.[categoryColumn]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row[categoryColumn] || '-';
            }
        },
        {
            data: subcategorytColumn,
            title: columnLabels?.[subcategorytColumn]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row[subcategorytColumn] || '-';
            }
        },
        {
            data: "accountparticularsename",
            title: columnLabels?.["accountparticularsename"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.accountparticularsename || '-';
            }
        },
        {
            data: "accountparticularstname",
            title: columnLabels?.["accountparticularstname"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.accountparticularstname || '-';
            }
        },
        {
            data: "statusflag",
            title: columnLabels?.["statusflag"]?.[language],
            render: function(data) {
                let activeText = arrLang?.[language]?.["active"] || "Active";
                let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                return data === 'Y'
                    ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                    : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            },
            className: "text-center d-none d-md-table-cell extra-column noExport"
        },
        {
            data: "encrypted_accountparticularsid",
            title: columnLabels?.["actions"]?.[language],
            render: (data) =>
                `<center><a class="btn editicon editaccountparticularsdel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            className: "text-center noExport"
        }
    ],
           
            "initComplete": function(settings, json) {
                $("#accountparticulatable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            
        });

        const mobileColumns = ["accountparticularsename", "accountparticularstname", "statusflag"];
    setupMobileRowToggle(mobileColumns);

//    updatedatatable("en", "callforrecordstable", "Call for Records");
updatedatatable(language, "accountparticulatable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#accountparticulatable')) {
            $('#accountparticulatable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
            // alert('te');
            const catcodeDropdown = $('#category');

            catcodeDropdown.html(`
                <option value='' data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
                </option>
            `);


            if (!deptcode) {

                catcodeDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Category Available"
                            data-name-ta="வகை கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
            }


            if (deptcode) {
                $.ajax({
                    url: "/getaccountcategoriesbasednndept",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach(category => {
                                catcodeDropdown.append(`
                                            <option value="${category.catcode}" 
                                                data-name-en="${category.catename || ''}" 
                                                data-name-ta="${category.cattname || ''}"
                                                ${category.catcode === selectedCatcode ? 'selected' : ''}>
                                                ${category.catename}
                                            </option>
                                        `);
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



function getsubcategoryBasedOnCategories(category, selectedCatcode = null) {
    const subcategoryDropdown = $('#subcategory');
    const subcatDiv = $('.subcatdiv'); // Target the div to show/hide

    subcategoryDropdown.html(`
    <option value="" data-name-en="---Select Sub Category---" data-name-ta="---உபவகையைத் தேர்ந்தெடுக்கவும்---">
        ${lang === 'ta' ? '---உபவகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub Category---'}
    </option>
`);
    if (!category || category.trim() === '') { 
        subcatDiv.hide(); // Hide the subcategory div if no category is selected
        return;
    }

    $.ajax({
        url: "/getsubCategoriesBasedOncategory",
        type: "POST",
        data: {
            category: category,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                subcategoryDropdown.empty().append(`
                <option value="" data-name-en="---Select Sub Category---" data-name-ta="---உபவகையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---உபவகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub Category---'}
                </option>
            `);

                response.data.forEach(subcategory => {
                    subcategoryDropdown.append(
                        `<option value="${subcategory.auditeeins_subcategoryid}"
                         data-name-en="${subcategory.subcatename || ''}" 
                        data-name-ta="${subcategory.subcattname || ''}" 
                        ${subcategory.auditeeins_subcategoryid == selectedCatcode ? 'selected' : ''}>
                        ${subcategory.subcatename}</option>`
                    );
                });

                subcatDiv.show(); 
            } else {
                subcategoryDropdown.append(`
            <option value="" disabled data-name-en="No Sub Categories Available" data-name-ta="உபவகைகள் கிடைக்கவில்லை">
                ${lang === 'ta' ? 'உபவகைகள் கிடைக்கவில்லை' : 'No Sub Categories Available'}
            </option>
        `);
                 subcatDiv.hide(); 
            }
        },
        error: function() {
            console.error('Error fetching subcategories.');
            alert('Error fetching categories. Please try again.');
            subcatDiv.hide(); 
        }
    });
}




    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#accountparticularform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                accountename: {
                    required: true
                },
                accounttname: {
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
            if ($("#accountparticularform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#accountparticularform').serializeArray();

                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                // formData.push({
                //     name: "lang",
                //     value: getLanguage('N')
                // });
                console.log(formData);
                $.ajax({
                    url: "{{ route('accountparticular.accountparticular_insertupdate') }}", // URL where the form data will be posted
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
                    if(response.error == 401)
                    {
                        handleUnauthorizedError(); 
                    }
                    else
                    {

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
                        getLabels_jsonlayout([{ id: response.message, key: response.message }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] || "Error Occured";
                            passing_alert_value('Confirmation', alertMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
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


    function accountparticularform(account) {
        $('#display_error').hide();
        $('#accountename').val(account.accountparticularsename);
        $('#accounttname').val(account.accountparticularstname);
        $('#accountparticularsid').val(account.encrypted_accountparticularsid);
        // $('#deptcode').val(workallocation.deptcode);
        $('#deptcode').val(account.deptcode).trigger('change');
         //$('#catcode').val(account.catcode).trigger('change');

        getCategoriesBasedOnDept(account.deptcode, account.catcode);
        getsubcategoryBasedOnCategories(account.catcode,account.auditeeins_subcategoryid);
        populateStatusFlag(account.statusflag);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editaccountparticularsdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#accountparticularsid').val(id);
            
            $.ajax({
                url: "{{ route('accountparticular.accountparticular_fetchdata') }}",
                method: 'POST',
                data: {
                    accountparticularsid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('accountparticularform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            accountparticularform(response.data[0]); // Populate form with data

                        } else {
                            alert('Account Particular data is empty');
                        }
                    } else {
                        alert('Account Particular not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


//     $(document).on('click', '.editaccountparticularsdel', function() {
//     const id = $(this).attr('id');

//     if (id) {
//         reset_form();
//         $('#accountparticularsid').val(id);

//         $.ajax({
//             url: "{{ route('accountparticular.accountparticular_fetchdata') }}",
//             method: 'POST',
//             data: {
//                 accountparticularsid: id
//             },
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             success: function(response) {
//                 if (response.success) {
//                     if (response.data && response.data.length > 0) {
//                         const data = response.data[0]; // Store response data
                        
//                         // Populate form fields
//                         accountparticularform(data);

//                         // Extract category and subcategory codes
//                         const catcode = data.catcode || "";
//                         const subcatcode = data.subcatcode || ""; // Ensure subcatcode is set

//                         // Show subcategory div only if category code is present
//                         getsubcategoryBasedOnCategories(catcode, subcatcode);
                        
//                         // Change button actions
//                         changeButtonAction('accountparticularform', 'action', 'buttonaction',
//                             'reset_button', 'display_error', @json($updatebtn),
//                             @json($clearbtn), @json($update));
//                     } else {
//                         alert('Account Particular data is empty');
//                     }
//                 } else {
//                     alert('Account Particular not found');
//                 }
//             },
//             error: function(xhr) {
//                 console.error('Error:', xhr.responseText || 'Unknown error');
//             }
//         });
//     }
// });



    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        // $('#accountparticularform')[0].reset();
        // $('#accountparticularform').validate().resetForm();
        // $('#deptcode').val(null).trigger('change');
       
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            // $('#ename, #fname').val();

        } else {
            $('#deptcode').val(null).trigger('change');
        }

            $('#category').val(null).trigger('change');
            $('#subcategory').val(null).trigger('change');

        changeButtonAction('accountparticularform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
