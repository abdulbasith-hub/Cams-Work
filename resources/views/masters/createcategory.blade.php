@section('content')
    @extends('index2')
@section('title', ' Category Report')

@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp


<style>


</style>

<!-- <style>
       @media screen and (max-width: 768px) {
    #categorytable thead {
        display: none;
    }

    #categorytable tbody,
    #categorytable tr,
    #categorytable td {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    #categorytable tr {
        margin-bottom: 15px;
        border: 1px solid #ddd; /* Ensure full border */
        padding: 10px;
        background: #fff;
        border-radius: 5px; /* Optional for rounded corners */
    }

    #categorytable td {
        text-align: left;
        padding: 10px;
        position: relative;
        border-bottom: 1px solid #ddd;
    }

    /* Last column should not have a bottom border */
    #categorytable td:last-child {
        border-bottom: none;
    }

    /* Add a label before each value */
    #categorytable td:before {
        content: attr(data-label);
        font-weight: bold;
        display: inline-block;
        width: 40%;
        color: #333;
    }

    /* Fix right-side border issue */
    #categorytable td {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 1px solid #ddd; /* Ensure full right border */
    }
    @media screen and (max-width: 768px) {
    #categorytable td {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #categorytable td.text-center {
        justify-content: flex-start; /* Align status like other text */
    }

    #categorytable td.text-center button {
        margin-left: auto; /* Move button slightly left */
    }
}

} -->



</style>
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="category">Category</div>
            <div class="card-body">
                <form id="categoryform" name="categoryform">
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
                            <label class="form-label required lang" key="catename" for="categoryename">Category
                                English Name</label>
                            <input type="text" class="form-control alpha_numeric" id="categoryename"
                                name="categoryename" maxlength="200" data-placeholder-key="categoryename" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="cattname" for="categorytname">Category
                                Tamil Name</label>
                            <input type="text" class="form-control alpha_numeric" id="categorytname"
                                name="categorytname" maxlength="200" data-placeholder-key="categorytname" required>
                        </div>


                        <!-- <div class="col-md-8 mb-3 d-flex flex-wrap align-items-center"> -->
                        <!-- Sub Category -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="if_subcategory">Sub Category</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input" type="radio" name="subcategory"
                                        id="subcategoryYes" value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="subcategoryYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="subcategory" id="subcategoryNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="subcategoryNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input" type="radio" name="status" id="statusYes"
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
                        <!-- </div> -->





                    </div>
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <input type="hidden" name="auditeeins_categoryid" id="auditeeins_categoryid"
                            value="" />
                        <input type="hidden" name="catcode" id="catcode" value="" />

                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <button class="btn button_save lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger lang" key="clear_btn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="cat_table">Category Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="categorytable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="catename">Category English
                                        Name</th>
                                    <th class="lang align-middle text-center" key="cattname">Category Tamil Name
                                    </th>
                                    <th class="lang align-middle text-center" key="if_subcategory">Sub category</th>
                                    <th class="lang align-middle text-center" key="statusflag">Active Status</th>

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
        $('#categoryform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        $('#translate').change(function() {
            const lang = $('#translate').val();
            updateTableLanguage(lang);
        });




    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('category.category_fetchData') }}",
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



    $("#translate").change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'categoryform');

    });

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#categorytable')) {
            $('#categorytable').DataTable().clear().destroy();
        }

        table = $('#categorytable').DataTable({
            // "scrollX": true,
            // "initComplete": function(settings, json) {
            //     $("#categorytable").wrap(
            //         "<div style='overflow:auto; width:100%;position:relative;'></div>");
            // },
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
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "catename",
                    title: columnLabels?.["catename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.catename || '-';
                    }
                },
                {
                    data: "cattname",
                    title: columnLabels?.["cattname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.cattname || '-';
                    }
                },
                {
                    data: "if_subcategory",
                    title: columnLabels?.["if_subcategory"]?.[language],
                    render: function(data) {
                        let activeText = arrLang?.[language]?.["yes"] || 'Yes';
                        let inactiveText = arrLang?.[language]?.["no"] || 'No';

                        return data === 'Y' ?
                            `<span class="btn btn-success btn-sm">${activeText}</span>` :
                            `<span class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                },
                {
                    data: "statusflag",
                    title: columnLabels?.["statusflag"]?.[language],
                    render: function(data) {
                        let activeText = arrLang?.[language]?.["active"] || 'Active';
                        let inactiveText = arrLang?.[language]?.["inactive"] || 'Inactive';

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
                            <a class="btn editicon editcategorydel"
                            id="${row.encryptauditeeins_categoryid}">
                                <i class="ti ti-edit fs-4"></i>
                            </a>
                        </center>
                    `;
                },

                className: "text-center noExport text-wrap"
            }
            ],
            "initComplete": function(settings, json) {
                $("#categorytable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["catename", "cattname", "if_subcategory", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "categorytable");

    }


    //         function renderTable(language) {
    //     const departmentColumn = language === 'ta' ? 'depttlname' : 'deptelname';

    //     if ($.fn.DataTable.isDataTable('#categorytable')) {
    //         $('#categorytable').DataTable().clear().destroy();
    //     }

    //     table = $('#categorytable').DataTable({
    //         "processing": true,
    //         "serverSide": false,
    //         "lengthChange": false,
    //         "data": dataFromServer,
    //         "columns": [
    //             {
    //                 data: null,
    //                 render: function(data, type, row, meta) {
    //                     return meta.row + 1;
    //                 },
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'S.No');
    //                 }
    //             },
    //             {
    //                 data: departmentColumn,
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Department');
    //                 }
    //             },
    //             {
    //                 data: "catename",
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Category English Name');
    //                 }
    //             },
    //             {
    //                 data: "cattname",
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Category Tamil Name');
    //                 }
    //             },
    //             {
    //                 data: "if_subcategory",
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Sub Category');
    //                 }
    //             },
    //             {
    //                 data: "statusflag",
    //                 render: (data) => {
    //                     return data === 'Y' ?
    //                         `<button type="button" class="btn btn-primary btn-sm">Active</button>` :
    //                         `<button type="button" class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">Inactive</button>`;
    //                 },
    //                 className: 'text-center',
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Status');
    //                 }
    //             },
    //             {
    //                 data: "encryptauditeeins_categoryid",
    //                 render: function(data) {
    //                     return `<center><a class="btn editicon editcategorydel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
    //                 },
    //                 createdCell: function(td) {
    //                     $(td).attr('data-label', 'Action');
    //                 }
    //             }
    //         ]
    //     });
    // }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#categorytable')) {
            $('#categorytable').DataTable().clear().destroy();
        }
        renderTable(language);
    }







    $("#translate").change(function() {
        var lang = getLanguage('Y');
        // updateTableLanguage(lang);

    });




    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#categoryform").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                // category: {
                //     required: true
                // },
                categoryename: {
                    required: true
                },
                categorytname: {
                    required: true
                },
                status: {
                    required: true
                },
                subcategory: {
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
            //     categoryename: {
            //         required: "Enter Category english name",
            //     },
            //     categorytname: {
            //         required: "Enter Category tamil name",
            //     },
            //     status: {
            //         required: "Select a status",
            //     },
            //     subcategory: {
            //         required: "Select a sub category",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#categoryform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#categoryform').serializeArray();

                formData.push({
                    name: 'deptcode',
                    value: $('#deptcode').val()
                });
if ($('#action').val() === 'update') {
                    formData.push({
                        name: 'status',
                        value: $('input[name="status"]:checked').val()
                    });
                }
                $.ajax({
                    url: "{{ route('category.category_insertupdate') }}", // URL where the form data will be posted
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
                            // table.ajax.reload();
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

    function categoryForm(category) {
        $('#display_error').hide();
        //change_button_as_update('categoryform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainobjection.catcode);
        $('#categoryename').val(category.catename);
        $('#categorytname').val(category.cattname);
        // $('#deptcode').val(workallocation.deptcode);
        $('#auditeeins_categoryid').val(category.encryptauditeeins_categoryid);

        populateStatusFlag(category.statusflag);
        populateSubcategoryFlag(category.if_subcategory);

        // $('#deptcode').val(category.deptcode);
        $('#deptcode').val(category.deptcode).trigger('change');

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editcategorydel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        //alert(id);

        if (id) {
            reset_form();
            $('#auditeeins_categoryid').val(id);
            //  console.log($('#workallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('category.category_fetchData') }}",
                method: 'POST',
                data: {
                    auditeeins_categoryid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('categoryform', 'action', 'buttonaction',
                                'display_error', '', @json($updatebtn),
                                @json($clearbtn), @json($update));

                            categoryForm(response.data[0]); // Populate form with data
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
     //   if (statusflag === "Y") {
       //     document.getElementById('statusYes').checked = true;
        //} else if (statusflag === "N") {
         //   document.getElementById('statusNo').checked = true;
        //}
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


    function populateSubcategoryFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('subcategoryYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('subcategoryNo').checked = true;
        }
    }

    function reset_form() {
        document.getElementById('statusNo').disabled = false;
        $('#categoryform').validate().resetForm();
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            auditeeins_categoryid
            $('#auditeeins_categoryid,#categoryename,#ctegorytname').val();


        } else {
            $('#deptcode').val(null).trigger('change');

        }



        changeButtonAction('categoryform', 'action', 'buttonaction', 'display_error', '', @json($savebtn),
            @json($clearbtn), @json($insert));
        // getCategoriesBasedOnDept('', selectedCatcode = null);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
