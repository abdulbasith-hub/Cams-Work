@section('content')
@section('title', 'Sub Category Records')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';
@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>




<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="sub_head">Sub Category records</div>
            <div class="card-body">
                <form id="subcategory" name="subcategory">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>
                            <select class="form-select mr-sm-2 select2  lang-dropdown" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept(this.value,'');">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select Department</option>

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
                            <label class="form-label required lang" key="category" for="category">Category</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category">
                                <option value="" data-name-en="--Select Category Name--"
                                    data-name-ta="--வகை பெயரைத் தேர்ந்தெடுக்கவும்--">Select Category Name</option>
                                <option value="" disabled data-name-en="No Category Available"
                                    data-name-ta="வகை பெயர் கிடைக்கவில்லை" disabled id="no-district-option">No Category
                                    Available Name</option>
                                <!-- <option value=''>Select Category Name</option>
                                                                                                                <option value="" disabled id="no-district-option">No Category Available Name -->
                                </option>
                            </select>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="subcatename" for="subename">Sub Category
                                English Name</label>
                            <input type="text" class="form-control name" id="subcatename" name="subcatename"
                                maxlength='200' placeholder="Sub Category English name"
                                data-placeholder-key="subcat_ename" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="subcattname" for="subcattname">Sub Category
                                Tamil Name</label>
                            <input type="text" class="form-control name" id="subcattname" name="subcattname"
                                maxlength='200' placeholder="Sub Category Tamil name"
                                data-placeholder-key="subcat_tname" required />
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
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="subcatgeoryid" id="subcatgeoryid" value="" />

                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3  lang"
                                style="height:35px;font-size: 13px;" key="clear" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="sub_head_details">Sub Category Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="subcategorytable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <th class="lang align-middle text-center" key="subcatename">SubCategory English
                                        Name</th>
                                    <th class="lang align-middle text-center" key="subcattname">SubCategory Tamil Name
                                    </th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                  <th class="all lang align-middle text-center" key="action">Action</th>                                  </tr>
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

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Download Button End -->
<script>
    var downloadTitleKey = "{{ __('datatable.buttons.download_title') }}";
</script>

<script>
    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    $(document).ready(function() {
        // $('#subcategory')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            getCategoriesBasedOnDept(sessiondeptcode, '');
        }
        $('#translate').change(function() {
            const lang = $('#translate').val();
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateTableLanguage(lang);
            updateValidationMessages(getLanguage('Y'), 'subcategory');

        });
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('subcategory.subcategory_fetchData') }}",
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
        const categorycolumn = language === 'ta' ? 'cattname' : 'catename';
        const departmentcolumn = language === 'ta' ? 'depttsname' : 'deptesname';


        // Destroy DataTable if it exists
        if ($.fn.DataTable.isDataTable('#subcategorytable')) {
            $('#subcategorytable').DataTable().clear().destroy();
        }

        var table = $('#subcategorytable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: ' text-wrap text-end w-10',
                    type: "num"
                },
                {
                    data: departmentcolumn,
                    title: columnLabels?.[departmentcolumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentcolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: categorycolumn,
                    title: columnLabels?.[categorycolumn]?.[language],
                    render: function(data, type, row) {
                        return row[categorycolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "subcatename",
                    title: columnLabels?.["subcatename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subcatename || '-';
                    }
                },
                {
                    data: "subcattname",
                    title: columnLabels?.["subcattname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subcattname || '-';
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
                    className: "text-center d-none d-md-table-cell extra-column text-wrap noExport"
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
                                <a class="btn editicon editsubcategory"
                                id="${row.encrypted_auditeeins_subcategoryid}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },
                    className: "text-center noExport"
                }
            ],
            initComplete: function(settings, json) {
                $("#subcategorytable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });

        // ✅ Include category column to ensure proper mobile display
        const mobileColumns = [departmentcolumn,"subcatename", "subcattname", "statusflag"];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "subcategorytable");
    }

    // ✅ Add CSS to prevent column compression

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#subcategorytable')) {
            $('#subcategorytable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function updateSelect2Language(lang) {
        $('.select2 option').each(function() {
            var text = (lang === "en") ? $(this).attr('data-name-en') : $(this).attr('data-name-ta');
            $(this).text(text);
        });

        $('.select2').select2();
    }

    $(document).ready(function() {
        let currentLang = getLanguage();
        updateSelect2Language(currentLang);
    });

    function changeLanguage(lang) {
        window.localStorage.setItem('lang', lang); // Save language selection
        updateSelect2Language(lang); // Apply the change immediately
    }

    function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#category');
        const lang = getLanguage();

        catcodeDropdown.html(`
            <option value="" data-name-en="--Select Category Name--" data-name-ta="--வகை பெயரைத் தேர்ந்தெடுக்கவும்--">
                ${lang === 'ta' ? '--வகை பெயரைத் தேர்ந்தெடுக்கவும்--' : 'Select Category Name'}
            </option>
            `);
        // alert(lang);
        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            catcodeDropdown.append(`
    <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை பெயர் கிடைக்கவில்லை">
        ${lang === 'ta' ? 'வகை பெயர் கிடைக்கவில்லை' : 'No Category Available'}
    </option>
`);
            // change_lang_for_page(lang);
            // return;
        }

        if (deptcode) {
            $.ajax({
                url: "/getcategoriesbasednndept",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    if (response.length > 0) {
                        // alert(lang);
                        response.forEach(category => {
                            catcodeDropdown.append(`
                        <option value="${category.catcode}"
                            data-name-en="${category.catename}"
                            data-name-ta="${category.cattname}"
                            ${category.catcode === selectedCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? category.cattname : category.catename}
                        </option>
                    `);
                        });
                    } else {
                        catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை பெயர் கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை பெயர் கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
                    }

                    // change_lang_for_page(lang); // Update dropdown text after data is loaded
                },
                error: function() {
                    alert('Error fetching categories. Please try again.');
                }
            });
        }
    }

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#subcategory").validate({


            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                subcatename: {
                    required: true
                },
                subcattname: {
                    required: true
                },
                status: {
                    required: true
                },

            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            // messages: {
            //     deptcode: {
            //         required: "Select a department",
            //     },
            //     category: {
            //         required: "Select a category",
            //     },
            //     subcatename: {
            //         required: "Enter a SubCategory English Name",
            //     },
            //     subcattname: {
            //         required: "Enter a SubCategory Tamil Name",
            //     },

            //     status: {
            //         required: "Select a statusflag",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#subcategory").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formData = $('#subcategory').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                $.ajax({
                    url: "{{ route('subcategory.subcategory_insertupdate') }}",
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
                            //table.ajax.reload();
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

    function subcategoryForm(subcategory) {
        $('#display_error').hide();
        //  change_button_as_update('subcategory', 'action', 'buttonaction', 'display_error', '', '');
        $('#subcatgeoryid').val(subcategory.encrypted_auditeeins_subcategoryid);
        populateStatusFlag(subcategory.statusflag);
        $('#deptcode').val(subcategory.deptcode).trigger('change');
        $('#subcatename').val(subcategory.subcatename)
        $('#subcattname').val(subcategory.subcattname)
        getCategoriesBasedOnDept(subcategory.deptcode, subcategory.catcode);
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editsubcategory', function() {
        const id = $(this).attr('id');
        if (id) {
            reset_form();
            $('#subcatgeoryid').val(id);
            $.ajax({
                url: "{{ route('subcategory.subcategory_fetchData') }}",
                method: 'POST',
                data: {
                    auditeeins_subcategoryid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('subcategory', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            subcategoryForm(response.data[0]);
                        } else {
                            alert(' records data is empty');
                        }
                    } else {
                        alert(' records not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        // $('#subcategory')[0].reset();
        $('#subcategory').validate().resetForm();
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#subcatename,#subcattname,#subcategoryid').val();
            $('#category').val(null).trigger('change');

        } else {
            $('#deptcode').val(null).trigger('change');
        }

        changeButtonAction('subcategory', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        // change_button_as_insert('subcategory', 'action', 'buttonaction', 'display_error', '', '');
        // getCategoriesBasedOnDept('', selectedCatcode = null);
        // $('#deptcode').val(null).trigger('change');
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
