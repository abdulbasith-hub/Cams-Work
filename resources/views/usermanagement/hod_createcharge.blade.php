@section('content')
@section('title', 'HOD Create Charge')
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
        <div class="card-header card_header_color lang" key="" style="padding:8px;">Create Charge - HOD</div>
        <div class="card-body card_border">
            <div class="row">

                <div class="card-body">
                    <form id="hod_createcharge" name="hod_createcharge" enctype="multipart/form-data">
                        <input type="hidden" name="chargeid" id="chargeid">
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
                                <label class="form-label lang required " key=""
                                    for="auditeedeptcode">Auditee Department</label>
                                <select class="form-select mr-sm-2 select2" id="auditeedeptcode" name="auditeedeptcode"  
                                    >
                                    <option value=''>Select Auditee Department</option>
                                    <option value="" disabled id="no-district-option">No Auditee Department Available
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required " key=""
                                    for="desigcode">Designation</label>
                                <select class="form-select mr-sm-2 select2" id="desigcode" name="desigcode" 
                                    >
                                    <option value=''>Select Designation</option>
                                    <option value="" disabled id="no-district-option">No Auditee Designation Available
                                    </option>
                                </select>
                            </div>
                             
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="" for="charge_des">Charge Description</label>
                                <input type="text" class="form-control text_special" id="charge_des" name="charge_des"
                                    placeholder="Charge Description"
                                    maxlength="150"
                                    data-msg-required="Enter Description Name"
                                    onkeypress="return allowOnlyAlphanumeric(event)"
                                    onpaste="return allowOnlyAlphanumericPaste(event)"
                                    oninput="convertToTitleCase(this)">
                                <small class="text-danger d-none" id="charge_des_error">Only letters, numbers and spaces
                                    allowed</small>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="statusflag">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="statusflagforcharge" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="" for="statusYes"> Active
                                    </label>
                                </div>
                                <div class="form-check me-2 mb-3">
                                    <input class="form-check-input" type="radio" name="statusflagforcharge" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="" for="statusNo"> Inactive
                                    </label>
                                </div>
                            </div>
                        </div>

                            <div class="row text-center">
                                <div class="col-md-3 mx-auto">
                                    <input type="hidden" name="action" id="action" value="insert" />

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
            <div class="card-header card_header_color lang" key="">Create Charge - HOD</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="hodchargetable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="">Auditee Department</th>

                                    <th class="lang align-middle text-center" key="">Designation</th>
                                    <th class="lang align-middle text-center" key="">Charge Description</th>
                                    <th class="lang align-middle text-center" key="">Status</th>
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
    const selectedCategoryCode = @json(old('auditeedeptcode'));
    $(document).ready(function() {

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
        var lang = getLanguage('Y');
        initializeDataTable(lang);


        const deptcodeFromSession = (sessiondeptcode || $('#deptcode').val() || '').toString().trim();
        if ($('#deptcode').prop('disabled') && deptcodeFromSession !== '') {
            $('#deptcode').val(deptcodeFromSession);
            // applySubcategoryVisibility(deptcodeFromSession);
            // getCategoriesBasedOnDept(deptcodeFromSession, selectedCategoryCode || null);
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
            if ($.fn.DataTable.isDataTable('#hodchargetable')) {
                $('#hodchargetable').DataTable().clear().destroy();
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

        if ($('#auditeedeptcode option[value="' + val + '"]').length > 0) {
            return val;
        }

        const normalized = String(parseInt(val, 10));
        let resolved = '';
        $('#auditeedeptcode option').each(function () {
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
        const $auditeedeptcodeCol = $('#auditeedeptcode').closest('.col-md-4');

        if (hideSubcategory) {
            $('.subcatdiv').addClass('d-none');
            $('#subcategory').val('');

            // Move file field into SubCategory slot position.
            if ($fileCol.length && $mainRow.length && !$fileCol.parent().is($mainRow)) {
                $fileCol.removeClass('col-md-3').addClass('col-md-4 mb-1');
                if ($auditeedeptcodeCol.length) {
                    $fileCol.insertAfter($auditeedeptcodeCol);
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
            url: "/fetchhod_createcharge",
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
                const auditeedeptColumn = language === 'ta' ? 'auditeedepttname' : 'auditeedeptename';

      const  desiColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if ($.fn.DataTable.isDataTable('#hodchargetable')) {
            $('#hodchargetable').DataTable().clear().destroy();
        }

        $('#hodchargetable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,
            "initComplete": function(settings, json) {
                $("#hodchargetable").wrap(
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
                    data: auditeedeptColumn,
                    title: columnLabels?.[auditeedeptColumn]?.[language],
                    render: function(data, type, row) {
                        return row[auditeedeptColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                 {
                    data: desiColumn,
                    title: columnLabels?.[desiColumn]?.[language],
                    render: function(data, type, row) {
                        return row[desiColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "chargedescription",
                    title: columnLabels?.["chargedescription"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.chargedescription || '-';
                    }
                },
                // {
                //     data: "titletname",
                //     title: columnLabels?.["titletname"]?.[language],
                //     className: "d-none d-md-table-cell lang extra-column text-wrap",
                //     render: function(data, type, row) {
                //         return row.titletname || '-';
                //     }
                // },
                {
                    data: "chargestatus",
                    title: columnLabels?.["chargestatus"]?.[language],
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
    data: "encrypted_chargeid",
    title: columnLabels?.["actions"]?.[language],
    className: "text-center noExport",
    render: function (data, type, row) {

        if (row.statusflag === 'Y') {
            // Show Block Icon
            return `<center>
                
                         <i class="ti ti-ban fs-4 text-danger" 
                           title="Editing Not Allowed"></i>
                    </center>`;
        }

        // Show Edit Button if NULL or 'N'
        return `<center>
                    <a class="btn editicon editchargedel" id="${data}">
                        <i class="ti ti-edit fs-4"></i>
                    </a>
                </center>`;
    }
}
            ],
                  "initComplete": function(settings, json) {
                    $("#hodchargetable").wrap(
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
            //             `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            //         className: "text-center noExport"
            //     }
            // ],



        });

      
        // setupMobileRowToggle("hodchargetable", mobileColumns);
        // setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "hodchargetable");
    }



    // $(document).on('select2:select', '#auditeedeptcode', function () {
    //     onchange_category($(this).val(), '', '');
    // });

   
                    function reset_form(clearDeptSelection = false) {

                        const deptcodeFromSession = (sessiondeptcode || '').toString().trim();
                        const isDeptLocked = $('#deptcode').prop('disabled');

                        // ✅ Reset validator state/messages
                        if (prauditValidator) {
                            prauditValidator.resetForm();
                            $("#hod_createcharge").find(".error").removeClass("error");
                        }

                        // ✅ Reset text fields
                        $('#charge_des, #titletname').val('');

                        // // ✅ Reset status radio
                        // $('#statusYes').prop('checked', true);
                        // $('#statusNo').prop('disabled', false);

                        // ✅ Reset Select2 dropdowns properly

                        if (!isDeptLocked || clearDeptSelection) {
                            $('#deptcode').val('').trigger('change.select2');
                        } else {
                            $('#deptcode').val(deptcodeFromSession).trigger('change.select2');
                        }

                        $('#auditeedeptcode')
                            .empty()
                            .append('<option value="">Select Auditee Department</option>')
                            .val('')
                            .trigger('change.select2');

                        $('#desigcode')
                            .empty()
                            .append('<option value="">Select Designation</option>')
                            .val('')
                            .trigger('change.select2');

                        $('#action').val('insert');
                        $('#chargeid').val('');

                        changeButtonAction(
                            'hod_createcharge',
                            'action',
                            'buttonaction',
                            'reset_button',
                            'display_error',
                            @json($savebtn),
                            @json($clearbtn),
                            @json($insert)
                        );
                    }



       
 function allowOnlyAlphanumeric(event) {

    const char = event.key;
    const inputField = event.target;
    const currentValue = inputField.value || '';

    // Allow control keys
    if (event.ctrlKey || event.metaKey || char.length > 1) {
        return true;
    }

    if (currentValue.length >= 150) {
        event.preventDefault();
        return false;
    }

    // Allow English, Tamil, numbers, space, -, &
    const regex = /^[a-zA-Z0-9\u0B80-\u0BFF\s\-&]$/u;

    if (!regex.test(char)) {
        event.preventDefault();
        return false;
    }

    const nextValue = currentValue + char;

    // Block 6 same characters
    if (/(.)\1{5,}/u.test(nextValue)) {
        event.preventDefault();
        return false;
    }

    // Block continuous space, -, &
    if (/(\s{2,}|-{2,}|&{2,})/.test(nextValue)) {
        event.preventDefault();
        return false;
    }

    return true;
}
function convertToTitleCase(input) {

    input.value = input.value
        .toLowerCase()
        .replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
}

function allowOnlyAlphanumericPaste(event) {

    const pastedText = (event.clipboardData || window.clipboardData).getData('text');
    const inputField = event.target;
    const currentValue = inputField.value || '';

    // Allow letters, Tamil, numbers, space, -, &
    const regex = /[a-zA-Z0-9\u0B80-\u0BFF\s\-&]/gu;

    let filteredText = pastedText.match(regex)
        ? pastedText.match(regex).join('')
        : '';

    event.preventDefault();

    let combinedValue = (currentValue + filteredText).slice(0, 150);

    // ❌ Remove 6 same characters
    combinedValue = combinedValue.replace(/(.)\1{5,}/gu, '$1$1$1$1$1');

    // ❌ Remove continuous space, -, &
    combinedValue = combinedValue.replace(/(\s{2,}|-{2,}|&{2,})/g, function(match){
        return match[0]; // keep only one
    });

    inputField.value = combinedValue;

    return false;
}
        function getPrauditValidationMessages(language) {
            const jsonMsgs = (errorMessages && errorMessages[language]) ? errorMessages[language] : {};
            const getMsg = (key, fallback) => jsonMsgs[key] || fallback;

            return {
                deptcode: { required: getMsg('deptcode', "Select a department") },
                auditeedeptcode: { required: getMsg('auditeedeptcode', "Select a Auditee Department") },
                 desigcode: { required: getMsg('desigcode', "Select a Auditee Department") },
                charge_des: {
                    required: getMsg('charge_des', "Enter Description"),
                    // minlength: getMsg('charge_des', "Minimum 10 characters required"),
                    maxlength: getMsg('charge_des', "Maximum 150 characters allowed")
                },
               
            };
        }

        jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#hod_createcharge").validate({
            rules: {
                deptcode: { required: true },
                auditeedeptcode: { required: true },
                desigcode: { required: true },
              
                charge_des: {
                    required: true,
                    // minlength: 10,
                    maxlength: 150
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
            messages: getPrauditValidationMessages(language)
        });
     $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#hod_createcharge").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formElement = document.getElementById('hod_createcharge');
                var formData = new FormData(formElement);
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {
                    formData.set('deptcode', deptcode);
                }
                // if ($('#action').val() === 'update') {
                //     formData.set('statusflag', $('input[name="statusflag"]:checked').val());
                //     const fileInput = document.getElementById('praudit_file');
                //     const hasSelectedFile = fileInput && fileInput.files && fileInput.files.length > 0;
                //     if (hasSelectedFile && !$('#uploadid').val() && $('#existing_uploadid').val()) {
                //         formData.set('uploadid', $('#existing_uploadid').val());
                //     }
                // }


                $.ajax({
                   url: "/insertUpdatehodcreatecharge",
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
                            initializeDataTable(language);

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
        $(document).on('click', '.editchargedel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#chargeid').val(id);

                $.ajax({
                                url: "/fetchhod_createcharge",

                    method: 'POST',
                    data: {
                        chargeid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        console.log(response);
                        
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('hod_createcharge',
                                    'action',
                                    'buttonaction', 'reset_button',
                                    'display_error',
                                    @json($updatebtn),
                                    @json($clearbtn),
                                    @json($update))
                                populatepchargeDataForm(response.data[
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

function populatepchargeDataForm(chargeData) {

    if (!chargeData) return;

    $('#display_error').hide();

    // 🔹 Set Hidden ID
    $('#chargeid').val(chargeData.encrypted_chargeid ?? '');

    // 🔹 Set Description
    $('#charge_des').val(chargeData.chargedescription ?? '');

    // 🔹 Safe Trim Values
    const deptcode        = (chargeData.deptcode ?? '').toString().trim();
    const auditeedeptcode = (chargeData.auditeedeptcode ?? '').toString().trim();
    const desigcode       = (chargeData.desigcode ?? '').toString().trim();
    const statusflag      = (chargeData.chargestatus ?? '').toString().trim();

    // 🔹 Set Department (Select2 Proper Way)
    $('#deptcode')
        .val(deptcode)
        .trigger('change'); // important for select2 refresh

    // 🔹 Populate Status
    populateStatusFlag(statusflag);

    // 🔹 Load Dependent Dropdowns
    getCategoriesBasedOnDept(
        deptcode,
        auditeedeptcode,
        desigcode
    );
}

function populateStatusFlag(statusflag) {

    // Clear both first
    $('#statusYes').prop('checked', false);
    $('#statusNo').prop('checked', false);

    if (statusflag && statusflag.toUpperCase() === "Y") {
        $('#statusYes').prop('checked', true);
    } 
    else if (statusflag && statusflag.toUpperCase() === "N") {
        $('#statusNo').prop('checked', true);
    }
}

        
  function getCategoriesBasedOnDept(
    deptcode = null,
    selectedCatcode = null,
    selectedDesigcode = null
) {

    const lang = getLanguage();
    deptcode = (deptcode || '').toString().trim();
    selectedCatcode = selectedCatcode ? String(selectedCatcode).trim() : null;
// const deptcode = $('#deptcode');
    const audtieedeptDropdown = $('#auditeedeptcode');

    audtieedeptDropdown
        .empty()
        .append('<option value="">Select Auditee Department</option>');

    if (!deptcode) return;

    $.ajax({
        url: "/fetchauditeedepartment",
        type: "POST",
        data: {
            deptcode: deptcode,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

            if (Array.isArray(response) && response.length > 0) {

                response.forEach(audept => {

                    const catVal = String(audept.auditeedeptcode).trim();

                    audtieedeptDropdown.append(
                        `<option value="${catVal}">
                            ${lang === 'ta'
                                ? audept.auditeedepttname
                                : audept.auditeedeptename}
                        </option>`
                    );
                });

                // ✅ CORRECT VALUE SETTING
                if (selectedCatcode &&
                    audtieedeptDropdown.find(`option[value="${selectedCatcode}"]`).length) {

                    audtieedeptDropdown
                        .val(selectedCatcode)
                        .trigger('change.select2');
                }
            }

            // call designation after category loaded
            getdesignationbasedondept(deptcode, selectedDesigcode);
        }
    });
}

function getdesignationbasedondept(deptcode = '', desigcode = '') {

    deptcode = deptcode || $('#deptcode').val();
    desigcode = desigcode ? String(desigcode).trim() : null;

    const desigcodeDropdown = $('#desigcode');
    const lang = getLanguage();

    desigcodeDropdown
        .empty()
        .append('<option value="">Select Designation</option>');

    if (!deptcode) return;

    $.ajax({
        url: '/fetchdesination',
        method: 'POST',
        data: { deptcode: deptcode },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

            if (response.success && response.designation?.length > 0) {

                response.designation.forEach(function (desig) {

                    const value = String(desig.desigcode).trim();

                    desigcodeDropdown.append(
                        `<option value="${value}">
                            ${lang === "en"
                                ? desig.desigelname
                                : desig.desigtlname}
                        </option>`
                    );
                });

                if (desigcode &&
                    desigcodeDropdown.find(`option[value="${desigcode}"]`).length) {

                    desigcodeDropdown
                        .val(desigcode)
                        .trigger('change');
                }
            }
        }
    });
}




    </script>

@endsection
