@section('content')
@section('title', 'Performance Audit Title')
    @extends('index2')
    @include('common.alert')
    @php

        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;
        $make_dept_disable = $deptcode ? 'disabled' : '';
    @endphp

    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">



    <div class="card card_border" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="pa_title" style="padding:8px;">Performance Audit Title</div>
        <div class="card-body card_border">
            <div class="row">

                <div class="card-body">
                    <form id="prauditmasterrecords" name="prauditmasterrecords" enctype="multipart/form-data">
                        <input type="hidden" name="praudittitleid" id="praudittitleid">
                        <input type="hidden" name="uploadid" id="uploadid" value="">
                        <input type="hidden" name="existing_uploadid" id="existing_uploadid" value="">
                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="department" for="dept">Department</label>

                                <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                                 <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?>  onchange="getCategoriesBasedOnDept(this.value,'');">
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
                                <label class="form-label lang required " key="catengname"
                                    for="category">Category</label>
                                <select class="form-select mr-sm-2 select2" id="category" name="category"
                                    >
                                    <option value=''>Select Category Name</option>
                                    <option value="" disabled id="no-district-option">No Category Available Name
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-1 subcatdiv ">
                                <label class="form-label lang required" key="subcategory_ename" for="subcategory">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                    name="subcategory" onchange="">
                                    <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>


                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="prtitle_eng" for="titleename">Performance Audit Title
                                    English Name</label>
                                <input type="text" class="form-control text_special" id="titleename" name="titleename"
                                    placeholder="Performance Audit English Tamil"
                                    maxlength="150"
                                    data-msg-required="Enter Title English Name"
                                    onkeypress="return allowOnlyAlphanumeric(event)"
                                    onpaste="return allowOnlyAlphanumericPaste(event)">
                                <small class="text-danger d-none" id="titleename_error">Only letters, numbers and spaces
                                    allowed</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="prtitle_tam" for="titletname">Performance Audit Title
                                    Tamil Name</label>
                                <input type="text" class="form-control text_special" id="titletname" name="titletname"
                                    placeholder="Performance Audit Title Tamil"
                                    maxlength="150"
                                    data-msg-required="Enter Title Tamil Name"
                                    onkeypress="return allowOnlyAlphanumeric(event)"
                                    onpaste="return allowOnlyAlphanumericPaste(event)">
                                <small class="text-danger d-none" id="titletname_error">Only letters, numbers and spaces
                                    allowed</small>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="statusflag">Status</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3 mb-3">
                                        <input class="form-check-input cursor-pointer" type="radio" name="status"
                                            id="statusYes" value="Y" checked>
                                        <label class="form-check-label lang" key="statusyes" for="statusYes">
                                            Yes
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cursor-pointer" type="radio" name="status"
                                            id="statusNo" value="N">
                                        <label class="form-check-label lang" key="statusno" for="statusNo">
                                            No
                                        </label>
                                    </div>
                                </div>
                            </div>
                          

                                <div class="col-md-3">
                                    <label class="form-label required lang" key="praudit_file" for="praudit_file">
                                        File Upload 
                                    </label>
                                    <small style="color: red;">(only PDF max 2MB)</small>
                                    <!-- hidden input -->
                                    <input type="file" id="praudit_file" name="praudit_file" accept="application/pdf"
                                        data-msg-required="Please upload a document"
                                        class="d-none">

                                    <!-- upload button -->
                                    <button type="button" class="btn btn-outline-primary w-100" id="add-file-btn">
                                        <i class="ti ti-upload"></i> Upload PDF
                                    </button>

                                    <!-- container to show uploaded file -->
                                    <div id="uploaded_file_container" class="d-flex mt-2"></div>

                                </div>


                            <div class="row text-center">
                                <div class="col-md-3 mx-auto">
                                    <input type="hidden" name="action" id="action" value="insert" />
                                    <input type="hidden" name="map_callforrecords" id="map_callforrecords" value="" />

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
             </div>
                 </div>
    </div>

 <div class="card card_border">
            <div class="card-header card_header_color lang" key="pa_title_det">Performance Audit Title Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="prtitletable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="catengname">Category</th>

                                    <th class="lang align-middle text-center" key="subcategory_ename">Subcategory</th>
                                    <th class="lang align-middle text-center" key="prtitle_eng"> Performance Audit Title English Name
                                        </th>
                                     <th class="lang align-middle text-center" key="prtitle_tam"> Performance Audit Title Tamil Name
                                        </th>
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


       


<script src="{{ url('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ url('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>



    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <script src="../assets/js/download-button/custom.xl.min.js"></script>

    <script>
         let table;
    let dataFromServer = [];
    let prauditValidator;
    const sessiondeptcode = @json((string) $deptcode);
    const selectedCategoryCode = @json(old('category'));
    $(document).ready(function() {

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
        var lang = getLanguage('');
        initializeDataTable(lang);


        const deptcodeFromSession = (sessiondeptcode || $('#deptcode').val() || '').toString().trim();
        if ($('#deptcode').prop('disabled') && deptcodeFromSession !== '') {
            $('#deptcode').val(deptcodeFromSession);
            // applySubcategoryVisibility(deptcodeFromSession);
            getCategoriesBasedOnDept(deptcodeFromSession, selectedCategoryCode || null);
        } else {
            $('#deptcode').val('');
            applySubcategoryVisibility($('#deptcode').val());
        }
    });

  $('#translate').change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        const lang = getLanguage('Y');
        if (prauditValidator) {
            prauditValidator.settings.messages = getPrauditValidationMessages(lang);
            // Do not trigger validation on language switch.
            // Keep current form state unchanged and avoid showing required errors.
        }
    });


     function updateTableLanguage(lang) {
            if ($.fn.DataTable.isDataTable('#prtitletable')) {
                $('#prtitletable').DataTable().clear().destroy();
            }

             renderTable(lang);

        }

    function shouldHideSubcategoryByDept(deptcode) {
        const value = (deptcode || '').toString().trim();
        return value === '01' || value === '05' || value === '1' || value === '5';
    }

    function resolveDeptSelectValue(rawDeptcode) {
        const val = (rawDeptcode || '').toString().trim();
        if (!val) return '';

        // 1) Exact match.
        if ($('#deptcode option[value="' + val + '"]').length > 0) {
            return val;
        }

        // 2) Numeric-equivalent match (handles 1 vs 01).
        const normalized = String(parseInt(val, 10));
        let resolved = '';
        $('#deptcode option').each(function () {
            const optionValue = ($(this).val() || '').toString().trim();
            if (!optionValue) return;
            const optionNormalized = String(parseInt(optionValue, 10));
            if (optionNormalized === normalized) {
                resolved = optionValue;
                return false;
            }
        });

        return resolved || val;
    }

    function resolveCategorySelectValue(rawCatcode) {
        const val = (rawCatcode || '').toString().trim();
        if (!val) return '';

        if ($('#category option[value="' + val + '"]').length > 0) {
            return val;
        }

        const normalized = String(parseInt(val, 10));
        let resolved = '';
        $('#category option').each(function () {
            const optionValue = ($(this).val() || '').toString().trim();
            if (!optionValue) return;
            const optionNormalized = String(parseInt(optionValue, 10));
            if (optionNormalized === normalized) {
                resolved = optionValue;
                return false;
            }
        });

        return resolved || val;
    }

    function applySubcategoryVisibility(deptcode) {
        const hideSubcategory = shouldHideSubcategoryByDept(deptcode);
        const $fileCol = $('#file_upload_col');
        const $fileRow = $('#file_upload_row');
        const $mainRow = $('#praudit_form_row');
        const $categoryCol = $('#category').closest('.col-md-4');

        if (hideSubcategory) {
            $('.subcatdiv').addClass('d-none');
            $('#subcategory').val('');

            // Move file field into SubCategory slot position.
            if ($fileCol.length && $mainRow.length && !$fileCol.parent().is($mainRow)) {
                $fileCol.removeClass('col-md-3').addClass('col-md-4 mb-1');
                if ($categoryCol.length) {
                    $fileCol.insertAfter($categoryCol);
                } else {
                    $mainRow.append($fileCol);
                }
            }
            if ($fileRow.length) {
                $fileRow.addClass('d-none');
            }

            if (prauditValidator) {
                prauditValidator.element('#subcategory');
            }
        } else {
            $('.subcatdiv').removeClass('d-none');

            // Move file field back to its original file row.
            if ($fileCol.length && $fileRow.length && !$fileCol.parent().is($fileRow)) {
                $fileCol.removeClass('col-md-4 mb-1').addClass('col-md-3');
                $fileRow.removeClass('d-none').append($fileCol);
            } else if ($fileRow.length) {
                $fileRow.removeClass('d-none');
            }
        }
    }

    $(document).on('change', '#deptcode', function () {
        applySubcategoryVisibility($(this).val());
    });

function initializeDataTable(language) {
        $.ajax({
            url: "/fetchPrauditmasterrecords",
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
        const departmentColumn = language === 'ta' ? 'depttlname' : 'deptesname';
                const catColumn = language === 'ta' ? 'cattname' : 'catename';

      const  subcatColumn = language === 'ta' ? 'subcattname' : 'subcatename';

        if ($.fn.DataTable.isDataTable('#prtitletable')) {
            $('#prtitletable').DataTable().clear().destroy();
        }

        $('#prtitletable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,
            "initComplete": function(settings, json) {
                $("#prtitletable").wrap(
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
                    data: catColumn,
                    title: columnLabels?.[catColumn]?.[language],
                    render: function(data, type, row) {
                        return row[catColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                 {
                    data: subcatColumn,
                    title: columnLabels?.[subcatColumn]?.[language],
                    render: function(data, type, row) {
                        return row[subcatColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "titleename",
                    title: columnLabels?.["titleename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.titleename || '-';
                    }
                },
                {
                    data: "titletname",
                    title: columnLabels?.["titletname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.titletname || '-';
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
                    data: "encrypted_praudittitleid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) =>
                        `<center><a class="btn editicon editprtitledel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                    className: "text-center"
                }
            ],
                  "initComplete": function(settings, json) {
                    $("#prtitletable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
            //             return data === 'Y' ?
            //                 `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
            //                 `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            //         },
            //         className: "text-center d-none d-md-table-cell extra-column noExport"
            //     }, {
            //         data: "encrypted_mainobjectionid",
            //         title: columnLabels?.["actions"]?.[language],
            //         render: (data) =>
            //             `<center><a class="btn editicon editprtitledel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            //         className: "text-center noExport"
            //     }
            // ],



        });

        // const mobileColumns = ["titleename", "titletname", "statusflag"];
        // setupMobileRowToggle("prtitletable", mobileColumns);
        // setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "prtitletable");
    }



    $(document).on('select2:select', '#category', function () {
        onchange_category($(this).val(), '', '');
    });

     $(document).on('click', '#add-file-btn', function () {
                    $('#praudit_file').trigger('click');
                });

     $(document).on('change', '#praudit_file', function () {
                    const file = this.files[0];
                    const $container = $('#uploaded_file_container').empty();

                    if (!file) {
                        if (prauditValidator) prauditValidator.element('#praudit_file');
                        return;
                    }

                    if (prauditValidator && !prauditValidator.element('#praudit_file')) {
                        this.value = '';
                        return;
                    }

                    UploadedFileList([{
                        name: file.name,
                        path: URL.createObjectURL(file),
                        fileuploadid: 0
                    }], 'view', 'uploaded_file_container', 'N', '');
                    $('#add-file-btn').addClass('d-none');
                });
 function reset_form(clearDeptSelection = false) {
        const deptcodeFromSession = (sessiondeptcode || '').toString().trim();
        const isDeptLocked = $('#deptcode').prop('disabled');

        // Reset validator state/messages.
        if (prauditValidator) {
            prauditValidator.resetForm();
            $("#prauditmasterrecords").find(".error").removeClass("error");
        }

        // Text fields.
        $('#titleename, #titletname').val('');

        // Status radio default.
        $('#statusYes').prop('checked', true);
        $('#statusNo').prop('disabled', false);

        // File input + preview.
        $('#praudit_file').val('');
        $('#uploaded_file_container').empty();
        $('#uploadid').val('');
        $('#existing_uploadid').val('');
        $('#add-file-btn').removeClass('d-none');

        // Dropdowns.
        if (!clearDeptSelection && isDeptLocked && deptcodeFromSession !== '') {
            $('#deptcode').val(deptcodeFromSession);
            $('#category').html('<option value="">Select Category Name</option>').val('');
            $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('');
            applySubcategoryVisibility(deptcodeFromSession);
            getCategoriesBasedOnDept(deptcodeFromSession, null);
        } else {
            $('#deptcode').prop('selectedIndex', 0).val('');
            if ($('#deptcode').hasClass('select2-hidden-accessible')) {
                $('#deptcode').trigger('change');
            }
            $('#category').html('<option value="">Select Category Name</option>').val('');
            $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('');
            applySubcategoryVisibility('');
        }

        // Action/button state.
        $('#action').val('insert');
        $('#praudittitleid').val('');
        $('#map_callforrecords').val('');
        changeButtonAction('prauditmasterrecords', 'action', 'buttonaction', 'reset_button',
            'display_error',
            @json($savebtn), @json($clearbtn),
            @json($insert));

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

        function getPrauditValidationMessages(language) {
            const jsonMsgs = (errorMessages && errorMessages[language]) ? errorMessages[language] : {};
            const getMsg = (key, fallback) => jsonMsgs[key] || fallback;

            return {
                deptcode: { required: getMsg('deptcode', "Select a department") },
                category: { required: getMsg('category', "Select a Category") },
                subcategory: { required: getMsg('subcategory', "Select a Subcategory") },
                titleename: {
                    required: getMsg('titleename', "Enter Title English Name"),
                    minlength: getMsg('titleename_minlength', "Minimum 10 characters required"),
                    maxlength: getMsg('titleename_maxlength', "Maximum 150 characters allowed")
                },
                titletname: {
                    required: getMsg('titletname', "Enter Title Tamil Name"),
                    minlength: getMsg('titletname_minlength', "Minimum 10 characters required"),
                    maxlength: getMsg('titletname_maxlength', "Maximum 150 characters allowed")
                },
                status: { required: getMsg('status', "Select Status") },
                praudit_file: {
                    required: getMsg('praudit_file', "Please upload a document"),
                    pdfOnly: getMsg('praudit_file_pdfOnly', "Only PDF files are allowed"),
                    maxFileSize: getMsg('praudit_file_maxFileSize', "File size must not exceed 3MB")
                }
            };
        }

        jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        if (!$.validator.methods.maxFileSize) {
            $.validator.addMethod("maxFileSize", function(value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param;
            }, "File size is too large");
        }
        if (!$.validator.methods.pdfOnly) {
            $.validator.addMethod("pdfOnly", function(value, element) {
                if (element.files.length === 0) return true;
                const file = element.files[0];
                const fileName = (file.name || "").toLowerCase();
                return file.type === "application/pdf" || fileName.endsWith(".pdf");
            }, "Only PDF files are allowed");
        }

        prauditValidator = $("#prauditmasterrecords").validate({
            ignore: ":hidden:not(#praudit_file)",
            rules: {
                deptcode: { required: true },
                category: { required: true },
                subcategory: {
                    required: function() {
                        return !shouldHideSubcategoryByDept($('#deptcode').val());
                    }
                },
                titleename: {
                    required: true,
                    minlength: 10,
                    maxlength: 150
                },
                titletname: {
                    required: true,
                    minlength: 10,
                    maxlength: 150
                },
                status: { required: true },
                praudit_file: {
                    required: function() {
                        return $('#action').val() !== 'update' || !$('#uploadid').val();
                    },
                    pdfOnly: true,
                    maxFileSize: 2097152
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr('id') === 'praudit_file') {
                    error.insertAfter('#uploaded_file_container');
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            messages: getPrauditValidationMessages(language)
        });
     $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#prauditmasterrecords").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formElement = document.getElementById('prauditmasterrecords');
                var formData = new FormData(formElement);
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {
                    formData.set('deptcode', deptcode);
                }
                if ($('#action').val() === 'update') {
                    formData.set('statusflag', $('input[name="statusflag"]:checked').val());
                    const fileInput = document.getElementById('praudit_file');
                    const hasSelectedFile = fileInput && fileInput.files && fileInput.files.length > 0;
                    if (hasSelectedFile && !$('#uploadid').val() && $('#existing_uploadid').val()) {
                        formData.set('uploadid', $('#existing_uploadid').val());
                    }
                }


                $.ajax({
                   url: "/insertUpdatePrauditmasterrecords",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            reset_form(true);
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
                        var response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {}
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {
                            const messageKey = response.message || 'Error Occured';
                            getLabels_jsonlayout([{
                                id: messageKey,
                                key: messageKey
                            }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] || messageKey;
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
        $(document).on('click', '.editprtitledel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#praudittitleid').val(id);

                $.ajax({
                                url: "/fetchPrauditmasterrecords",

                    method: 'POST',
                    data: {
                        praudittitleid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('prauditmasterrecords',
                                    'action',
                                    'buttonaction', 'reset_button',
                                    'display_error',
                                    @json($updatebtn),
                                    @json($clearbtn),
                                    @json($update))
                                populateprtitleForm(response.data[
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

   function populateprtitleForm(prtitle) {
        $('#display_error').hide();

        $('#praudittitleid').val(prtitle.encrypted_praudittitleid || '');
        $('#titleename').val(prtitle.titleename || '');
        $('#titletname').val(prtitle.titletname || '');

        const rawDeptcode = (prtitle.deptcode || '').toString().trim();
        const deptcode = resolveDeptSelectValue(rawDeptcode);
        const catcode = (prtitle.catcode || '').toString();
        const subcatid = (prtitle.subcatid || '').toString().trim();
        const hasSubcategory = subcatid !== '' && subcatid.toLowerCase() !== 'null';

        $('#deptcode').val(deptcode);
        if ($('#deptcode').hasClass('select2-hidden-accessible')) {
            $('#deptcode').trigger('change');
        }

        // If subcategory is empty/null, hide the field in edit mode.
        if (!hasSubcategory) {
            $('.subcatdiv').addClass('d-none');
            $('#subcategory').val('');
            getCategoriesBasedOnDept(deptcode, catcode, function () {
                $('#category').val(resolveCategorySelectValue(catcode));
                if ($('#category').hasClass('select2-hidden-accessible')) {
                    $('#category').trigger('change');
                }
            });
        } else {
            applySubcategoryVisibility(deptcode);
            if (shouldHideSubcategoryByDept(deptcode)) {
                return;
            }
            getCategoriesBasedOnDept(deptcode, catcode, function () {
                $('#category').val(resolveCategorySelectValue(catcode));
                if ($('#category').hasClass('select2-hidden-accessible')) {
                    $('#category').trigger('change');
                }
                onchange_category(catcode, subcatid, '');
            });
        }

        if ((prtitle.statusflag || 'Y') === 'Y') {
            $('#statusYes').prop('checked', true);
        } else {
            $('#statusNo').prop('checked', true);
        }

        // Load existing uploaded file for edit mode.
        if (prtitle.fileuploadid && prtitle.filename) {
            const filePath = prtitle.filepath || '#';
            UploadedFileList([{
                name: prtitle.filename,
                path: filePath,
                fileuploadid: prtitle.fileuploadid
            }], 'view', 'uploaded_file_container', 'Y', 'uploadid');
            $('#existing_uploadid').val(prtitle.fileuploadid);
            $('#add-file-btn').addClass('d-none');
        } else {
            $('#uploadid').val('');
            $('#existing_uploadid').val('');
            $('#uploaded_file_container').empty();
            $('#add-file-btn').removeClass('d-none');
        }

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

        function allowOnlyAlphanumeric(event) {
            const char = String.fromCharCode(event.which);
            const inputField = event.target;
            const currentValue = inputField.value || '';

            // Max length hard stop.
            if (currentValue.length >= 150) {
                event.preventDefault();
                return false;
            }

            // English letters: a-z, A-Z
            // Tamil characters: \u0B80-\u0BFF (Tamil Unicode range)
            const regex = /^[a-zA-Z\u0B80-\u0BFF\s]*$/;

            if (!regex.test(char)) {
                event.preventDefault();
                return false;
            }

            // Block 6+ same characters in a row while typing.
            const nextValue = currentValue + char;
            if (/(.)\1{5,}/u.test(nextValue)) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Allow only alphabetic characters (English + Tamil) and spaces on paste
        function allowOnlyAlphanumericPaste(event) {
            const pastedText = (event.clipboardData || window.clipboardData).getData('text');
            const inputField = event.target;
            const currentValue = inputField.value || '';
            // Extract only English letters, Tamil characters, and spaces
            const regex = /[a-zA-Z\u0B80-\u0BFF\s]/g;

            const filteredText = pastedText.match(regex) ? pastedText.match(regex).join('') : '';

            if (filteredText) {
                event.preventDefault();
                let combinedValue = (currentValue + filteredText).slice(0, 150);
                combinedValue = combinedValue.replace(/(.)\1{5,}/gu, '$1$1$1$1$1');
                inputField.value = combinedValue;
            } else {
                event.preventDefault();
            }
            return false;
        }

         function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
                const $container = $('#' + containerid).empty();

                files.forEach(file => {
                    if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);
                    const filePath = file.path || '#';
                    const canView = filePath !== '#';

                    const fileCard = `
                                    <div class="position-relative ms-2" style="max-width: 320px;">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="p-2 bg-primary-subtle rounded d-flex align-items-center justify-content-center"
                                                         style="width:50px;height:36px;">
                                                        <i class="ti ti-file-text text-primary fs-6"></i>
                                                    </div>

                                                    <div class="text-truncate" style="max-width: 300px;">
                                                        <a class="fw-semibold text-dark text-decoration-none text-truncate d-block"
                                                           href="${filePath}"
                                                           target="_blank"
                                                           title="${file.name}">
                                                            ${file.name}
                                                        </a>
                                                        <a class="small ${canView ? '' : 'disabled text-muted'}"
                                                           ${canView ? `href="${filePath}" target="_blank"` : 'href="javascript:void(0)"'}
                                                           >View</a>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-danger remove-uploaded-file" title="Remove file">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                    $container.append(fileCard);
                });
            }


      

        $(document).on('click', '.remove-uploaded-file', function () {
            const previewHref = $('#uploaded_file_container a.fw-semibold').attr('href') || '';
            if (previewHref.startsWith('blob:')) {
                URL.revokeObjectURL(previewHref);
            }
            $('#praudit_file').val('');
            $('#uploaded_file_container').empty();
            $('#uploadid').val('');
            $('#add-file-btn').removeClass('d-none');
            if (prauditValidator) prauditValidator.element('#praudit_file');
        });
        function getCategoriesBasedOnDept(deptcode, selectedCatcode = null, onLoaded = null) {
            // alert('te');
            const lang = getLanguage();
            deptcode = (deptcode || '').toString().trim();
            selectedCatcode = selectedCatcode != null ? String(selectedCatcode).trim() : null;
            const catcodeDropdown = $('#category');
            const subcategoryDropdown = $('#subcategory');
            catcodeDropdown.html('<option value="">Select Category Name</option>');
            subcategoryDropdown.html('<option value="">---Select Subcategory---</option>');
            if (!deptcode) {
                catcodeDropdown.append('<option value="" disabled>No Category Available</option>');
                if (typeof onLoaded === 'function') onLoaded();
                return;

            }
            if (deptcode) {
                $.ajax({
                    url: "/getcategoriesbasednondept",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(category => {
                                const catVal = String(category.catcode).trim();
                                catcodeDropdown.append(
                                    `<option value="${catVal}" ${catVal === selectedCatcode ? 'selected' : ''
                                    }>${lang === 'ta' ? category.cattname : category.catename}</option>`
                                    
                                );
                            });
                            if (selectedCatcode) {
                                catcodeDropdown.val(resolveCategorySelectValue(selectedCatcode));
                            }
                        } else {
                            catcodeDropdown.append('<option disabled>No Categories Available</option>');
                        }
                        if (typeof onLoaded === 'function') onLoaded();
                    },
                    error: function () {
                        alert('Error fetching categories. Please try again.');
                        if (typeof onLoaded === 'function') onLoaded();
                    }
                });
            }
        }



        function onchange_category(catcode = '', subcategory = '', if_subcat = '') {

            catcode = catcode || $('#category').val();
            var selectedOption = $('#category').find(':selected');
            if_subcat = if_subcat || selectedOption.attr('if_subcat');

            const subcategoryDropdown = $('#subcategory');
            subcategoryDropdown.empty(); // clear old options

            if (!catcode) {
                subcategoryDropdown.append(`<option value="">---Select Subcategory---</option>`);
                return;
            }

            $.ajax({
                url: '/getsubcategory',
                method: 'POST',
                data: {
                    catcode: catcode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {

                    subcategoryDropdown.append(
                        `<option value="">---Select Subcategory---</option>`
                    );

                    if (response.success && response.subcategoryData.length > 0) {

                        response.subcategoryData.forEach(function (subcat) {

                            let isSelected = subcat.auditeeins_subcategoryid == subcategory
                                ? 'selected'
                                : '';

                            subcategoryDropdown.append(
                                `<option value="${subcat.auditeeins_subcategoryid}"
                                                data-name-en="${subcat.subcatename}"
                                                data-name-ta="${subcat.subcattname}"
                                                ${isSelected}>
                                                ${lang === "en" ? subcat.subcatename : subcat.subcattname}
                                            </option>`
                            );
                        });

                    } else {

                        subcategoryDropdown.append(
                            `<option disabled>No Subcategory Available</option>`
                        );
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        }


    </script>

@endsection

