@section('content')
@section('title', 'HOD assign charge')
    @extends('index2')
    @include('common.alert')
    @php

        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;
        $make_dept_disable = $deptcode ? 'disabled' : '';

    @endphp
    <?php
    use Carbon\Carbon;

    // Get today's date in dd/mm/yy format
    $today = Carbon::today()->format('d/m/y');

        ?>
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">



    <div class="card card_border" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="" style="padding:8px;">Assign Charge - HOD</div>
        <div class="card-body card_border">
            <div class="row">

                <div class="card-body">
                    <form id="hodassigncharge" name="hodassigncharge">
                        {{-- <input type="hidden" name="userchargeid" id="userchargeid"> --}}

                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="department" for="dept">Department</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept(this.value,'');">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>


                                    @if (!empty($dept) && count($dept) > 0)
                                        @foreach ($dept as $department)
                                            <option value="{{ $department->deptcode }}" @if (old('dept', $deptcode) == $department->deptcode) selected @endif
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
                                <label class="form-label lang required " key="catengname" for="category">Category</label>
                                <select class="form-select mr-sm-2 select2" id="category" multiple="multiple"
                                    name="category" onchange="onchange_category(this.value,'');">
                                    {{-- <option value='A'>All</option> --}}
                                    <option value="" disabled id="no-district-option">No Category Available Name
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-1 subcatdiv ">
                                <label class="form-label lang required" key="subcategory_ename"
                                    for="subcategory">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                    multiple="multiple" name="subcategory" >
                                    {{-- <option value='A'>All</option> --}}

                                    {{-- <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option> --}}
                                    <option value="" disabled id="no-district-option">No SubCategory Available Name



                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required " key="" for="auditeedeptcode">Auditee
                                    Department</label>
                                <select class="form-select mr-sm-2 select2" id="auditeedeptcode" name="auditeedeptcode">
                                    <option value=''>Select Auditee Department</option>
                                    <option value="" disabled id="no-district-option">No Auditee Department Available
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required " key="" for="desigcode">Designation</label>
                                <select class="form-select mr-sm-2 select2" id="desigcode" name="desigcode">
                                    <option value=''>Select Designation</option>
                                    <option value="" disabled id="no-district-option">No Designation Available
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required " key="" for="chargeid">Charge</label>
                                <select class="form-select mr-sm-2 select2" id="chargeid" name="chargeid" onchange="fetchuser(this.value,'')">
                                    <option value=''>Select Charge</option>
                                    <option value="" disabled id="no-district-option">No Charge Available
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required " key="" for="userid">User</label>
                                <select class="form-select mr-sm-2 select2" id="userid" name="userid">
                                    <option value=''>Select User</option>
                                    <option value="" disabled id="no-district-option">No User Available
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label lang" for="validationDefaultUsername" key="chargeFromDate">Charge
                                    From Date</label>
                                <input type="text" class="form-control" id="cod" name="cod" value="<?php echo $today; ?>"
                                    disabled />
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
        <div class="card-header card_header_color lang" key="">Assign Charge - HOD</div>
        <div class="card-body"><br>
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="hodassignchargetable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="department">Department</th>
                                <th class="lang align-middle text-center" key="">Category</th>
                                <th class="lang align-middle text-center" key="">SubCategory</th>
                                <th class="lang align-middle text-center" key="">Auditee Department</th>

                                <th class="lang align-middle text-center" key="">Designation</th>
                                <th class="lang align-middle text-center" key="">Charge
                                </th>
                                <th class="lang align-middle text-center" key=""> User Details
                                </th>
                                {{-- <th class="lang align-middle text-center" key="">Status</th>
                                <th class="all lang" key="action">Action</th> --}}
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
        $(document).ready(function () {

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

        $('#translate').change(function () {
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
            if ($.fn.DataTable.isDataTable('#hodassignchargetable')) {
                $('#hodassignchargetable').DataTable().clear().destroy();
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
                url: "/fetchhodassigncharge",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: "json",
                success: function (json) {
                    // console.log("Success Response:", json);
                    if (json.data && json.data.length > 0) {
                        console.log(json.data);
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
                error: function () {
                    $('#tableshow').hide();
                    $('#no_data').show(); // Show "No Data Available" on error
                }
            });
        }


        function renderTable(language) {
            const departmentColumn = language === 'ta' ? 'depttlname' : 'deptesname';
            const auditeedeptColumn = language === 'ta' ? 'auditeedepttname' : 'auditeedeptename';

            const desigColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

            if ($.fn.DataTable.isDataTable('#hodassignchargetable')) {
                $('#hodassignchargetable').DataTable().clear().destroy();
            }

            $('#hodassignchargetable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                // "scrollX": true,
                "initComplete": function (settings, json) {
                    $("#hodassignchargetable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                "data": dataFromServer,

                columns: [{
                    data: null,
                    render: function (data, type, row, meta) {
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
                    data: "catename",
                    title: columnLabels?.["catename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row.catename || '-';
                    }
                },
                {
                    data: "subcatename",
                    title: columnLabels?.["subcatename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row.subcatename || '-';
                    }
                },
                {
                    data: auditeedeptColumn,
                    title: columnLabels?.[auditeedeptColumn]?.[language],
                    render: function (data, type, row) {
                        return row[auditeedeptColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: desigColumn,
                    title: columnLabels?.[desigColumn]?.[language],
                    render: function (data, type, row) {
                        return row[desigColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "chargedescription",
                    title: columnLabels?.["chargedescription"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row.chargedescription || '-';
                    }
                },
                {
                    data: null,
                    title: columnLabels?.["email"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {

                        let email = row.email ? row.email : '-';
                        let mobile = row.mobilenumber ? row.mobilenumber : '-';

                        return `
                <div>
                    <strong>Email:</strong> ${email}<br>
                    <strong>Mobile:</strong> ${mobile}
                </div>
            `;
                    }
                },


                ],
                "initComplete": function (settings, json) {
                    $("#hodassignchargetable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });


            updatedatatable(language, "hodassignchargetable");
        }



        // $('#category').on('change', function () {

        //     let selected = $(this).val();

        //     if (selected.includes('A')) {
        //         $(this).val(['A']).trigger('change.select2');
        //     }

        //     onchange_category();
        // });

        function reset_form(clearDeptSelection = false) {

            const deptcodeFromSession = (sessiondeptcode || '').toString().trim();
            const isDeptLocked = $('#deptcode').prop('disabled');
                $("#hodassigncharge").validate().resetForm();

                // ✅ Clear normal form fields
                $("#hodassigncharge")[0].reset();
            /* ------------------------------------------------------------
               Reset Department
            ------------------------------------------------------------ */

            if (!clearDeptSelection && isDeptLocked && deptcodeFromSession !== '') {

                $('#deptcode').val(deptcodeFromSession).trigger('change');

                // Reset category & subcategory
                $('#category').html('<option value="">Select Category</option>').val(null).trigger('change');
                $('#subcategory').html('<option value="">Select Subcategory</option>').val(null).trigger('change');
                 $('#desigcode').html('<option value="">Select Designation</option>').val(null).trigger('change');
                 $('#chargeid').html('<option value="">Select Charge</option>').val(null).trigger('change');


                applySubcategoryVisibility(deptcodeFromSession);

                // Reload categories
                getCategoriesBasedOnDept(deptcodeFromSession, null);
                // onchange_category(catcode = '')

            } else {

                $('#deptcode').val(null).trigger('change');

                $('#category').html('<option value="">Select Category</option>').val(null).trigger('change');
                $('#subcategory').html('<option value="">Select Subcategory</option>').val(null).trigger('change');
                 $('#desigcode').html('<option value="">Select Designation</option>').val(null).trigger('change');
                 $('#chargeid').html('<option value="">Select Charge</option>').val(null).trigger('change');

                applySubcategoryVisibility('');
            }

            $('#hodassigncharge').val('');

            /* ------------------------------------------------------------
               Reset Action
            ------------------------------------------------------------ */

            $('#action').val('insert');

            changeButtonAction(
                'hodassigncharge',
                'action',
                'buttonaction',
                'reset_button',
                'display_error',
                @json($savebtn),
                @json($clearbtn),
                @json($insert)
            );
        }


        function getPrauditValidationMessages(language) {
            const jsonMsgs = (errorMessages && errorMessages[language]) ? errorMessages[language] : {};
            const getMsg = (key, fallback) => jsonMsgs[key] || fallback;

            return {
                deptcode: { required: getMsg('deptcode', "Select a Department") },
                auditeedeptcode: { required: getMsg('auditeedeptcode', "Select a Auditee department") },
                category: { required: getMsg('category', "Select a Category") },
                subcategory: { required: getMsg('subcategory', "Select a Subcategory") },
                chargeid: { required: getMsg('chargeid', "Select a Subcategory") },
                userid: { required: getMsg('userid', "Select a Subcategory") },


            };
        }

        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#hodassigncharge").validate({
                rules: {
                    deptcode: { required: true },
                    auditeedeptcode: { required: true },
                    desigcode: { required: true },
                    category: { required: true },
                    subcategory: { required: true },
                    chargeid: { required: true },
                    userid: { required: true },


                },
                errorPlacement: function (error, element) {
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
            $("#buttonaction").on("click", function (event) {
                event.preventDefault();
                if ($("#hodassigncharge").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formElement = document.getElementById('hodassigncharge');
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
                        url: "/insertUpdatehodassigncharge",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
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
                        error: function (xhr, status, error) {
                            var response = {};
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (e) { }
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
                } else { }
            });

            // Handle Edit Button Click
            $(document).on('click', '.editprtitledel', function() {
                const id = $(this).attr('id');
                if (id) {
                    reset_form();
                    $('#praudittitleid').val(id);

                    $.ajax({
                                    url: "/fetchhodassigncharge",

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
                                    changeButtonAction('hodassigncharge',
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



        function getCategoriesBasedOnDept(deptcode, selectedCatcode = null, onLoaded = null) {
            // alert('te');
            const lang = getLanguage();
            deptcode = (deptcode || '').toString().trim();
            selectedCatcode = selectedCatcode != null ? String(selectedCatcode).trim() : null;
            const catcodeDropdown = $('#category');
            const subcategoryDropdown = $('#subcategory');
            catcodeDropdown.html('<option value="">Select Category</option>');
            subcategoryDropdown.html('<option value="">Select Subcategory</option>');
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

                        catcodeDropdown.empty();

                        // ✅ Add ALL option
                        catcodeDropdown.append(`<option value="A">All</option>`);

                        if (Array.isArray(response) && response.length > 0) {

                            response.forEach(category => {
                                const catVal = String(category.catcode).trim();

                                catcodeDropdown.append(
                                    `<option value="${catVal}">
                        ${lang === 'ta' ? category.cattname : category.catename}
                    </option>`
                                );
                            });

                        } else {
                            catcodeDropdown.append('<option disabled>No Categories Available</option>');
                        }

                        catcodeDropdown.trigger('change.select2');

                        if (typeof onLoaded === 'function') onLoaded();
                    },

                    error: function () {
                        alert('Error fetching categories. Please try again.');
                        if (typeof onLoaded === 'function') onLoaded();
                    }
                });
            }
        }

        let deptcode = $('#deptcode').val();

        var selectedDesigcode = null; // or existing value
        $('#deptcode').on('change', function () {

            let deptcode = $('#deptcode').val();
            let subcat = $(this).val();

            if (subcat && subcat.includes('A')) {
                subcat = []; // treat as all
            }

            getauditeeBasedOnDept(deptcode, null, null, subcat);
        });
        function onchange_category(catcode = '') {

            const subcategoryDropdown = $('#subcategory');
            let deptcode = $('#deptcode').val();

            // If not passed, take from dropdown
            catcode = catcode || $('#category').val();

            // 🔥 Always reset first (default option only)
            subcategoryDropdown
                .empty()
                .append('<option value="">Select Subcategory</option>');

            // ❌ If no category selected → stop here
            if (!catcode || catcode.length === 0) {
                subcategoryDropdown.val(null).trigger('change');
                return;
            }

            // ✅ If category is "All"
            if (Array.isArray(catcode) && catcode.includes('A')) {
                catcode = [];
            } else if (catcode === 'A') {
                catcode = [];
            }

            $.ajax({
                url: '/getsubcategoryforhod',
                method: 'POST',
                data: { catcode: catcode },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {

                    if (response.success &&
                        Array.isArray(response.subcategoryData) &&
                        response.subcategoryData.length > 0) {
                            subcategoryDropdown.empty();
                        // ✅ Add ALL only once
                        subcategoryDropdown.append('<option value="A">All</option>');

                        response.subcategoryData.forEach(function (subcat) {

                            subcategoryDropdown.append(
                                `<option value="${subcat.auditeeins_subcategoryid}">
                                ${subcat.subcatename}
                            </option>`
                            );
                        });

                    } else {

                        subcategoryDropdown.append(
                            '<option disabled>No Subcategory Available</option>'
                        );
                    }

                    // ✅ Refresh Select2
                    subcategoryDropdown.val(null).trigger('change');
                },
                error: function () {
                    subcategoryDropdown.append(
                        '<option disabled>Error loading subcategory</option>'
                    );
                }
            });
        }




        function getauditeeBasedOnDept(
            selectedCatcode = null,
            selectedDesigcode = null
        ) {

            const lang = getLanguage();
            let deptcode = $('#deptcode').val();
            selectedCatcode = selectedCatcode ? String(selectedCatcode).trim() : null;
            
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
 $('#auditeedeptcode').on('change', function () {
            getdesignationbasedondept();
        });
        
        
       

        function getdesignationbasedondept(deptcode = '', desigcode = '', selectedChargeId = null) {

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

                    if (response.success && response.designation.length > 0) {

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

                        // ✅ Set selected value
                        desigcodeDropdown.trigger('change.select2');
                        // if (desigcode &&
                        //     desigcodeDropdown.find(`option[value="${desigcode}"]`).length) {

                        //     desigcodeDropdown
                        //         .val(desigcode)
                        //         .trigger('change.select2');
                        // }

                      // fetchchargebasedondesig(deptcode, desigcode, selectedChargeId);
                    }
                }
            });
        }

        $('#desigcode').on('change', function () {
            fetchchargebasedondesig();
        });
        
        function fetchchargebasedondesig(deptcode = '', desigcode = '', selectedChargeId = null) {

            deptcode = deptcode || $('#deptcode').val();
            auditeedeptcode = $('#auditeedeptcode').val();
            desigcode = desigcode || $('#desigcode').val();
            selectedChargeId = selectedChargeId ? String(selectedChargeId).trim() : null;

            const chargeDropdown = $('#chargeid');
            
            // Remove old dynamic options
            // chargeDropdown.find('option:not(:first):not(#no-district-option)').remove();
            // $('#no-district-option').hide();
              chargeDropdown
                .empty()
                .append('<option value="">Select Charge</option>');
            if (!deptcode || !desigcode) return;

            $.ajax({
                url: '/fetchcharge',
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    desigcode: desigcode,
                    auditeedeptcode: auditeedeptcode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {

                    console.log(response);

                    if (Array.isArray(response) && response.length > 0) {

                        response.forEach(function (charge) {

                            const value = String(charge.chargeid).trim();
                            const text = charge.chargedescription || 'No Description';

                            let isSelected =
                                selectedChargeId && selectedChargeId === value
                                    ? 'selected'
                                    : '';

                            chargeDropdown.append(
                                `<option value="${value}" ${isSelected}>
                                ${text}
                            </option>`
                            );
                        });

                    } else {
                        $('#no-district-option').show();
                    }

                    // chargeDropdown.trigger('change.select2');
                     if (selectedChargeId &&
                            chargeDropdown.find(`option[value="${selectedChargeId}"]`).length) {

                            chargeDropdown
                                .val(selectedCatcode)
                                .trigger('change.select2');
                        }
                    //  fetchuser();
                },
                error: function (xhr) {
                    console.error('Charge Fetch Error:', xhr.responseText);
                }
            });
        }

        $('#chargeid').on('change', function () {
            fetchuser();
        });
        function fetchuser() {

            let deptcode = $('#deptcode').val();
            let auditeedeptcode = $('#auditeedeptcode').val();
            let category = $('#category').val();
            let subcategory = $('#subcategory').val();
            let desigcode = $('#desigcode').val();
            let chargeid = $('#chargeid').val();

            const userDropdown = $('#userid');
            const lang = getLanguage();

            userDropdown
                .empty()
                .append('<option value="">Select User</option>');

            if (!deptcode || !desigcode || !chargeid) return;

            $.ajax({
                url: '/fetchuser',
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    desigcode: desigcode,
                    auditeedeptcode: auditeedeptcode,
                    chargeid: chargeid,
                    category: category,  // ✅ IMPORTANT
                    subcategory: subcategory,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {

                    userDropdown.empty().append('<option value="">Select User</option>');

                    if (response.success && response.user.length > 0) {

                        response.user.forEach(function (user) {

                            const value = user.auditeedeptid;

                            const text = [user.email, user.mobilenumber]
                                .filter(Boolean)
                                .join(' / ');

                            userDropdown.append(
                                `<option value="${value}">
                        ${text}
                    </option>`
                            );
                        });

                    } else {
                        userDropdown.append(
                            '<option disabled>No User Available</option>'
                        );
                    }

                    userDropdown.trigger('change.select2');
                }

            });
        }

    </script>

@endsection