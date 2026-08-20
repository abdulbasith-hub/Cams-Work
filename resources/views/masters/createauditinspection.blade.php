@section('content')

@section('title', 'Super Check')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    //print_r($sessionchargedel);
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp

<style>
    .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px;
        /* Adjust as needed */
    }

    @media (min-width: 768px) {
        .custom-col {
            flex: 0 0 12.5% !important;
            max-width: 12.5% !important;
        }
    }

    .is-invalid {
        border: 1px solid red !important;
        box-shadow: none !important;
    }
</style>
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditinspect_head">Audit inspect</div>
            <div class="card-body">
                <form id="inspectionform" name="inspectionform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->`
                    @csrf
                    <div class="row">
                        <input type="hidden" name="if_subcategory" id="if_subcategory" value="">



                            <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('insert','','','')">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>

                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            @if (old('department', $deptcode) == $department->deptcode) selected @endif
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


                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="designation"
                                for="validationDefault01">Designation</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="desigcode" name="desigcode">
                                <option value="" data-name-en="---Select Designation---"
                                    data-name-ta="---பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Designation---</option>

                                <option value="" disabled id="" data-name-en="No Designation Available"
                                    data-name-ta="பதவி கிடைக்கவில்லை">No Designation Available</option>

                            </select>
                        </div>



                       <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                onchange="onchange_category('','','insert')">



                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-4 mb-2 subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory"
                                for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple"
                                id="subcategory" name="subcategory[]">
                                <!-- <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---
                                </option> -->

                                <!-- <option value="" disabled data-name-en="No SubCategory Available"
                                    data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option> -->


                            </select>
                        </div>





                        <div id="InspectionFields">
                            <div class="row align-items-end scheme-row">

                                <div class="col-md mb-2 custom-col">

                                    <label class="form-label required lang" key="heading_en" for="heading_en">Heading
                                        Name in
                                        English</label>
                                    <input type="text" class="heading_en form-control name removesplchar_text"
                                        id="heading_en" maxlength='400' name="heading_en[]" required
                                        data-placeholder-key="heading_en[]">
                                </div>

                                <div class="col-md mb-2 custom-col">
                                    <label class="form-label required lang" key="heading_ta" for="heading_ta">Heading
                                        Name in
                                        Tamil</label>
                                    <input type="text" class="heading_ta form-control name removesplchar_text"
                                        id="heading_ta" maxlength='400' name="heading_ta[]" required
                                        data-placeholder-key="heading_ta[]">
                                </div>


                                <div class="col-md mb-2 custom-col">
                                    <label class="form-label required lang" key="part_no[]" for="part_no">Part
                                        No</label>
                                    <input type="text"
                                        class="part_no form-control only_numbers removesplchar_number" id="part_no"
                                        data-placeholder-key="part_no[]" name="part_no[]" maxlength="9" required />
                                </div>




                                <div class="col-md mb-2 custom-col">
                                    <label class=" form-label required lang" key="checkpoint_en"
                                        for="checkpoint_en">Checkpoint
                                        Name in English</label>
                                    <input type="text" maxlength='400'
                                        class="checkpoint_en form-control alphanumeric removesplchar_text" id="checkpoint_en"
                                        name="checkpoint_en[]" required data-placeholder-key="checkpoint_en[]">
                                </div>

                                <div class="col-md mb-2 custom-col">
                                    <label class="form-label required lang" key="checkpoint_ta"
                                        for="checkpoint_ta">Checkpoint Name in Tamil</label>
                                   <input type="text" maxlength='400'
                                        class="checkpoint_ta form-control alphanumeric removesplchar_text" id="checkpoint_ta"
                                        name="checkpoint_ta[]" required data-placeholder-key="checkpoint_ta[]">
                                </div>

                                <!-- <div class="col-md mb-2 custom-col" >
                                    <label class="form-label required lang" key="question_type" for="question_type">Question
                                        Type</label>
                                    <select class="question_type form-select lang-dropdown " id="question_type " name="question_type[]">
                                        <option value="" data-name-en="---Select Question Type---"
                                            data-name-ta="கேள்வி வகையை தேர்ந்தெடுக்கவும்">---Select Question Type---</option>
                                        <option value="O" data-name-en="" data-name-ta="">Yes/No
                                        </option>

                                       <option value="N" data-name-en="" data-name-ta="">Numerical</option>

                                        <option value="D" data-name-en="" data-name-ta="">Description</option>


                                    </select>

                                </div> -->



                                <div class="col-md-2 mb-2">
                                    <label class="form-label required lang active-status-label"
                                        key="active_sts_flag">Active Status</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3 mb-3">
                                            <input class="form-check-input " type="radio" name="statusflag[]"
                                                id="statusYes" value="Y" checked>
                                            <label class="form-check-label lang active-status-label" key="statusyes"
                                                for="statusYes">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="statusflag[]"
                                                id="statusNo" value="N">
                                            <label class="form-check-label lang active-status-label" key="statusno"
                                                for="statusNo">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md mb-2 custom-col" id="addActionContainer">
                                    <label class="form-label d-block lang" key="action">Action</label>
                                    <button type="button" id="addSchemeBtn"
                                        class="btn btn-success fw-medium ms-2 addRowBtn">
                                        <i class="ti ti-circle-plus"></i>
                                    </button>


                                    <button type="button"
                                        class="btn btn-danger fw-medium ms-2 removeRowBtn hide_this">
                                        <i class="ti ti-circle-minus"></i>
                                    </button>



                                </div>
                            </div>


                        </div>


                    </div>


                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />

                            <input type="hidden" name="aifid" id="aifid" value="" />

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
            <div class="card-header card_header_color lang" key="">Filter on Audit Inspection Points</div>
            <div class="card-body">
                <form id="filterform" name="filterform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <input type="hidden" name="if_subcategory" id="if_subcategory" value="">


                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department</label>
                            <!-- <input type="hidden" id="" name="" value=""> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="department" name="department"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('filter','')">
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


                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="catcode" name="catcode"
                                onchange="onchange_category('','','filter')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="if_subcategory"
                                for="subcatecode">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 " 
                                id="subcatecode" name="subcatecode">
                              
                               <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---
                                </option> 

                           <option value="" disabled data-name-en="No SubCategory Available"
                                    data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option>

                            </select>
                        </div>



                    </div>


                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />

                            <!-- <input type="hidden" name="aifid" id="aifid" value="" /> -->

                            <button class="btn button_save mt-3 lang" key="submit" type="submit"
                                id="buttonaction" name="buttonaction">Save</button>

                            <button type="button" class="btn btn-danger mt-3 lang" key="clearbtn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form1()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>



        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditinspect_table">Audit Inspect Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditinspectiontable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="designation">Designation</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <!-- <th class="lang align-middle text-center" key="if_subcategory">Sub Category</th> -->
                                    <th class="lang align-middle text-center" key="heading_en">Heading Name in English
                                    </th>
                                    <th class="lang align-middle text-center" key="heading_ta">Heading Name in Tamil
                                    </th>

                                    <th class="lang align-middle text-center" key="part_no">Part No</th>

                                    <th class="lang align-middle text-center" key="checkpoint_en">Check Point Name in
                                        English</th>
                                    <th class="lang align-middle text-center" key="checkpoint_ta">Check Point in Tamil
                                    </th>
                                    <th class="lang align-middle text-center" key="question_type">Question Type</th>
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

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- Download Button End -->

<script>
    function checkDuplicateRows(fieldClassNames, fieldLabel) {
        $(document).on('input', fieldClassNames.map(cls => `.${cls}`).join(','), function() {
            let rows = $('#InspectionFields .scheme-row');
            let rowValues = [];
            let duplicatesFound = false;

            // Clear previous errors
            rows.find('input').removeClass('is-invalid').next('label.error').remove();

            rows.each(function() {
                let row = $(this);
                let key = '';
                let isEmpty = true;

                fieldClassNames.forEach(className => {
                    let val = row.find('.' + className).val()?.replace(/\s+/g, '')
                        .toLowerCase() || '';
                    if (val !== '') isEmpty = false;
                    key += val + '|';
                });

                if (isEmpty) {
                    return; // skip empty rows
                }

                if (rowValues.includes(key)) {
                    fieldClassNames.forEach(className => {
                        let input = row.find('.' + className);
                        input.addClass('is-invalid');
                        if (!input.next('label.error').length) {
                            input.after(
                                `<label class="error">Duplicate ${fieldLabel} found!</label>`
                            );
                        }
                    });
                    duplicatesFound = true;
                }

                rowValues.push(key);
            });

            return !duplicatesFound;
        });
    }
    checkDuplicateRows(['heading_en', 'heading_ta', 'part_no', 'checkpoint_en', 'checkpoint_ta'], 'row');




    $(document).on("keypress", ".name", function(event) {
        const charCode = event.which || event.keyCode;

        if (
            (charCode >= 65 && charCode <= 90) || // A-Z
            (charCode >= 97 && charCode <= 122) || // a-z
            charCode === 32 // space
        ) {
            return true;
        } else {
            event.preventDefault(); // block number, symbols
            return false;
        }
    });





    $(document).on("keypress", ".only_numbers", function(event) {
        const charCode = event.which || event.charCode;
        if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    });


    let rowIndex = 1;

    $(document).ready(function() {


        restrictSpecialChars($(
            '#InspectionFields .removesplchar_text, #InspectionFields .removesplchar_number'));



        $('#InspectionFields .scheme-row').first().find('.removeRowBtn').addClass('hide_this');

        const $firstRow = $('#InspectionFields .scheme-row').first();
        $firstRow.show();

        if (!$firstRow.find('input[name="statusflag"]:checked').val()) {
            $firstRow.find('input[name="statusflag"][value="Y"]').prop('checked', true); // Default to "Yes"
        }


        $('#InspectionFields').on('click', '#addSchemeBtn', function() {
            const rowCount = $('#InspectionFields .scheme-row').length;

            if (rowCount >= 5) {

                passing_alert_value('Confirmation', "Cannot add more than 5 rows!",
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                return;
            }

            let $currentRow = $(this).closest('.scheme-row');

            const $row = $(this).closest('.scheme-row');

            let $clone = $currentRow.clone();

            $clone.find('#addSchemeBtn').remove();
            $clone.find('.removeRowBtn').removeClass('hide_this');

            $clone.find('input[type="text"]').val('');
            $clone.find('input[type="radio"]').prop('checked', false);
            $clone.find("label.error").remove();
            $clone.find(".error").removeClass("error");
            $clone.find(".is-invalid").removeClass("is-invalid");
            $clone.find('label').not('.active-status-label').hide();

            // Recalculate the index based on current row count (will become the new row's index)
            //let newIndex = rowCount + 1;

            rowIndex++;

            let newIndex = rowIndex;



            $clone.find('input, label').each(function() {
                if (this.id) {
                    const oldId = this.id;
                    const newId = oldId.replace(/\d*$/, '') + newIndex;
                    $(this).attr('id', newId);
                    if (this.tagName.toLowerCase() === 'label') {
                        $(this).attr('for', newId);
                    }
                }

                if (this.name) {
                    const oldName = this.name;
                    const newName = oldName.replace(/\d*$/, newIndex);
                    $(this).attr('name', newName);
                }

                // if (this.name && this.name.endsWith('[]')) {
                //  $(this).attr('name', this.name);
                // }
            });

            $clone.find('input[name^="statusflag"]').each(function() {
                const oldName = $(this).attr('name');
                const newName = oldName.replace(/\d*$/, newIndex);
                $(this).attr('name', newName);

                const oldId = $(this).attr('id');
                const newId = oldId.replace(/\d*$/, newIndex);
                $(this).attr('id', newId);

                const label = $(this).next('label');
                if (label.length) {
                    label.attr('for', newId);
                }
            });




            $clone.find('input, label, textarea, select').each(function() {
                const $el = $(this);

                if (this.id) {
                    const newId = this.id.replace(/\d+$/, '') + newIndex;
                    $el.attr('id', newId);

                    if (this.tagName.toLowerCase() === 'label') {
                        $el.attr('for', newId);
                    }
                }

                if (this.name) {
                    if (this.name.endsWith('[]')) {
                        return;
                    }
                    const newName = this.name.replace(/\d+$/, '') + newIndex;
                    $el.attr('name', newName);
                }


            });


            $clone.find('input[name^="statusflag"]').prop('checked', false); // Clear both first
            $clone.find('input[name^="statusflag"][value="Y"]').prop('checked', true); // Default to Yes




            $('#InspectionFields').append($clone);


            applyValidationToNewFields($clone.find('.heading_en'), "Enter heading english name");
            applyValidationToNewFields($clone.find('.heading_ta'), "Enter heading tamil name");
            applyValidationToNewFields($clone.find('.part_no'), "Enter part no");
            applyValidationToNewFields($clone.find('.checkpoint_en'), "Enter checkpoint english name");
            applyValidationToNewFields($clone.find('.checkpoint_ta'), "Enter checkpoint tamil name");
            applyValidationToNewFields($clone.find('.question_type'), "Select question type");

            restrictSpecialChars($clone.find('.removesplchar_text'));
            restrictSpecialChars($clone.find('.removesplchar_number'));
            // restrictSpecialChars($clone.find('.removesplchar_numberwithdecimal'));



        });

        $('#InspectionFields').on('click', '.removeRowBtn', function() {
            const rowCount = $('#InspectionFields .scheme-row').length;

            if (rowCount > 1) {
                $(this).closest('.scheme-row').remove();
            } else {
                alert("At least one row must remain.");
            }
        });
    });


function restrictSpecialChars(elements) {
        $(elements).each(function() {
            const $el = $(this);

            $el.off("keypress paste")
                .on("keypress", function(event) {
                    const char = String.fromCharCode(event.which);
                    const value = this.value;

                    if ($el.hasClass('removesplchar_text')) {
                        // Allow Tamil (U+0B80–U+0BFF), English letters (a-z, A-Z), numbers (0-9), and spaces
                        if (!/^[a-zA-Z0-9\s\u0B80-\u0BFF]$/.test(char)) {
                            event.preventDefault();
                        }
                    } else if ($el.hasClass('removesplchar_number')) {
                        if (!/^[0-9\-]$/.test(char)) {
                            event.preventDefault();
                        }
                    } else if ($el.hasClass('removesplchar_numberwithdecimal')) {
                        if (!/[0-9.]/.test(char)) {
                            event.preventDefault();
                        }
                        if (char === '.' && value.includes('.')) {
                            event.preventDefault();
                        }
                        if (char === '.' && value.length === 0) {
                            event.preventDefault();
                        }
                        if (value.includes('.')) {
                            const parts = value.split('.');
                            if (parts[1].length >= 2 && this.selectionStart > value.indexOf('.')) {
                                event.preventDefault();
                            }
                        }
                    }
                })
                .on("paste", function(e) {
                    e.preventDefault();
                    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                    let cleanData = '';

                    if ($el.hasClass('removesplchar_text')) {
                        // Allow Tamil, English letters, numbers, and space
                        cleanData = pasteData.replace(/[^a-zA-Z0-9\s\u0B80-\u0BFF]/g, '');
                    } else if ($el.hasClass('removesplchar_number')) {
                        cleanData = pasteData.replace(/[^0-9\-]/g, '');
                    } else if ($el.hasClass('removesplchar_numberwithdecimal')) {
                        cleanData = pasteData.replace(/[^0-9.]/g, '');
                        let dotIndex = cleanData.indexOf('.');
                        if (dotIndex !== -1) {
                            cleanData = cleanData.substring(0, dotIndex + 1) +
                                cleanData.substring(dotIndex + 1).replace(/\./g, '');
                        }
                        if (cleanData.includes('.')) {
                            const [intPart, decPart] = cleanData.split('.');
                            cleanData = intPart + '.' + decPart.substring(0, 2);
                        }
                    }

                    const input = e.target;
                    const start = input.selectionStart;
                    const end = input.selectionEnd;
                    const original = input.value;

                    const maxLength = parseInt($(input).attr('maxlength')) || Infinity;
                    const allowedLength = maxLength - (original.length - (end - start));
                    cleanData = cleanData.substring(0, allowedLength);

                    input.value = original.substring(0, start) + cleanData + original.substring(end);
                    input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                    $(input).trigger('input');
                });
        });
    }



    function applyValidationToNewFields($input, message) {
        if ($input.length) {
            let validator = $("#inspectionform").data("validator");

            // Initialize validator if not already present
            if (!validator) {
                $("#inspectionform").validate();
                validator = $("#inspectionform").data("validator");
            }

            $input.each(function() {
                const $this = $(this);

                // Remove old rules in case of re-cloning
                $this.rules("remove");

                // Add new validation rule
                $this.rules("add", {
                    required: true,
                    messages: {
                        required: message
                    }
                });

                $this.off("change.validate").on("change.validate", function() {
                    $(this).valid();
                });

                setTimeout(() => {
                    validator.element(this);
                }, 0);
            });
        } else {
            console.warn("Input not found for validation");
        }
    }


  $(document).ready(function() {
        var sessiondeptcode ='<?php echo $deptcode; ?>';
    let filterDept = sessiondeptcode||$('#department').val();

    getCategoriesBasedOnDept('filter', filterDept);
	
	

});


    let data = "";


    
 function getCategoriesBasedOnDept(formPrefix, deptcode, selectedCatcode = null, desigcode = null) {
       
    const idMap = {
            insert: {
            dept: 'deptcode',
            category: 'category',
            subcategory: 'subcategory',
            designation: 'desigcode',
            if_subcategory: 'if_subcategory'
        },
        filter: {
            dept: 'department',
            category: 'catcode',
            subcategory: 'subcatecode',
            if_subcategory: 'if_subcategory'  
        }
    };

    const ids = idMap[formPrefix];
    if (!ids) {
        console.error("Invalid form prefix passed to getCategoriesBasedOnDept");
        return;
    }

    const catcodeDropdown = $('#' + ids.category);
    const subcategoryDropdown = $('#' + ids.subcategory);
    const designationDropdown = ids.designation ? $('#' + ids.designation) : null;

    const lang = getLanguage();



    catcodeDropdown.empty();
    subcategoryDropdown.empty();

    catcodeDropdown.html(`
        <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
        </option>
    `);

    if (designationDropdown) {
        designationDropdown.html(`
            <option value="" data-name-en="---Select Designation---" data-name-ta="---பதவியைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---பதவியைத் தேர்ந்தெடுக்கவும்---' : '---Select Designation---'}
            </option>
        `);
    }

    if (formPrefix === 'filter')  {
      subcategoryDropdown.append(`
            <option value=""  data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
        `);

    }


    if (!deptcode) {
        deptcode = $('#' + ids.dept).val();
    }

    if (!deptcode) {

        catcodeDropdown.append(`
            <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
            </option>
        `);

        if (designationDropdown) {
            designationDropdown.append(`
                <option disabled data-name-en="No Designation Available" data-name-ta="பதவி கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'பதவி கிடைக்கவில்லை' : 'No Designation Available'}
                </option>
            `);
        }

        subcategoryDropdown.append(`
            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
            </option>
        `);

        return; 
    }

    $.ajax({
        url: "/getCategoriesBasedOnDeptforinspection",
        type: "POST",
        data: {
            deptcode: deptcode,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            const categorie = response.categorie;
            const designation = response.designation;
            data = response.categorie;

            if (categorie.length > 0) {


            catcodeDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="All">
                    ${lang === 'ta' ? 'All' : 'All'}
                </option>
            `);
          

                categorie.forEach(category => {
                    catcodeDropdown.append(`
                        <option value="${category.catcode}"
                            data-name-en="${category.catename}"
                            subcategory="${category.if_subcategory}"
                            data-name-ta="${category.cattname}"
                            ${category.catcode === selectedCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? category.cattname : category.catename}
                        </option>
                    `);

                    $('#' + ids.if_subcategory).val(category.if_subcategory);
                });
            } else {
                catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடையவில்லை">
                        ${lang === 'ta' ? 'வகை கிடையவில்லை' : 'No Category Available'}
                    </option>
                `);
            }

            if (designationDropdown) {
                if (designation.length > 0) {
                    designation.forEach(desig => {
                        designationDropdown.append(`
                            <option value="${desig.desigcode}"
                                data-name-en="${desig.desigelname}"
                                data-name-ta="${desig.desigtlname}"
                                ${desig.desigcode === desigcode ? 'selected' : ''}>
                                ${lang === 'ta' ? desig.desigtlname : desig.desigelname}
                            </option>
                        `);
                    });
                } else {
                    designationDropdown.append(`
                        <option disabled data-name-en="No Designation Available" data-name-ta="பதவி கிடையவில்லை">
                            ${lang === 'ta' ? 'பதவி கிடையவில்லை' : 'No Designation Available'}
                        </option>
                    `);
                }
            }

            clearSubcategoryErrors();
        },
        error: function() {
            alert('Error fetching categories. Please try again.');
        }
    });
}


    function clearSubcategoryErrors() {
        $('.subcategory').each(function() {
            $(this).removeClass('error');
            var errorLabel = $("label[for='" + $(this).attr('id') + "'].error");
            if (errorLabel.length) {
                errorLabel.remove();
            }
        });
    }



  function onchange_category(catcode, selectedsubCatcode = null, formPrefix = 'insert') {
        const categoryDropdown = formPrefix === 'filter' ? $('#catcode') : $('#category');
        const subcategoryDropdown = formPrefix === 'filter' ? $('#subcatecode') : $('#subcategory');


    catcode = catcode || categoryDropdown.val();

    const selectedOption = categoryDropdown.find(':selected');
    const subcategory = selectedOption.attr('subcategory');

    const lang = getLanguage();

    subcategoryDropdown.empty();

    if(formPrefix = 'filter'){
        subcategoryDropdown.append(`
            <option value=""  data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
        `);

    }


    if (!catcode) {
        subcategoryDropdown.append(`
            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
            </option>
        `);
        return;
    }

    if (catcode === 'A') {
    subcategoryDropdown.append(`
        <option value="A" selected data-name-en="All" data-name-ta="All">
            ${lang === 'ta' ? 'All' : 'All'}
        </option>
    `);
    return;
}

    // Your ajax here
    $.ajax({
        url: '/getsubcategoriesbasedondeptforauditinspection',
        method: 'POST',
        data: { category: catcode },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (subcategory === 'Y') {
                if (response && response.length > 0) {
                    response.forEach(subcat => {
                        subcategoryDropdown.append(`
                            <option value="${subcat.auditeeins_subcategoryid}"
                                data-name-en="${subcat.subcatename}"
                                data-name-ta="${subcat.subcattname}"
                                ${subcat.auditeeins_subcategoryid === selectedsubCatcode ? 'selected' : ''}>
                                ${lang === 'ta' ? subcat.subcattname : subcat.subcatename}
                            </option>
                        `);
                    });
                } else {
                    subcategoryDropdown.append(`
                        <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                        </option>
                    `);
                }
            } else {

                $.each(data, function(i, subcategory) {
                        if (subcategory.catcode === catcode) {
                            //console.log(subcategory.catcode);
                            subcategoryDropdown.append(
                                `<option value="" data-name-en="${subcategory.catename}" data-name-ta="${subcategory.cattname}" selected>
                                ${lang === "ta" ? subcategory.cattname : subcategory.catename}
                                </option>`
                            );
                        }
                        });     
            }
            clearSubcategoryErrors();
        },
        error: function(xhr, status, error) {
        }
    });
}



    let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#inspectionform')[0].reset();
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
       // initializeDataTable(lang);





    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'inspectionform');
    });


     $('#filterform').on('submit', function(e) {
    e.preventDefault(); 

    const formData = $(this).serialize();
    const lang = $('html').attr('lang') || 'en';

    $.ajax({
        url: "/auditinspectform/auditinspectform_fetchData",
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(json) {
            if (json.data && json.data.length > 0) {
                $('#tableshow').show();
                $('#usertable_wrapper').show();
                $('#no_data').hide();
                dataFromServer = json.data;
                renderTable(lang);
            } else {
                $('#auditinspectiontable').DataTable().clear().draw(); // clear datatable
                $('#tableshow').hide();
                $('#usertable_wrapper').hide();
                $('#no_data').show();
            }
        },
        error: function() {
            $('#tableshow').hide();
            $('#no_data').show();
        }
    });
});





    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttamsname' : 'deptengsname';
        const CategoryColumn = language === 'ta' ? 'cattamname' : 'catengname';
        const subcategoryColumn = language === 'ta' ? 'subcategory_tname' : 'subcategory_ename';
        const desigColumn = language === 'ta' ? 'desigtlname' : 'desigelname';



        if ($.fn.DataTable.isDataTable('#auditinspectiontable')) {
            $('#auditinspectiontable').DataTable().clear().destroy();
        }

        table = $('#auditinspectiontable').DataTable({
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
                    className: 'text-end text-wrap',
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
                    data: desigColumn,
                    title: columnLabels?.[desigColumn]?.[language],
                    render: function(data, type, row) {
                        return row[desigColumn] || '-';
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap"
                },


                {
                    data: CategoryColumn,
                    render: function(data, type, row) {

                        const lang = (language === "ta") ? "ta" : "en";

                        const translations = {
                            en: {
                                category: "Category",
                                subcategory: "Subcategory",
                            },
                            ta: {
                                category: "வகை",
                                subcategory: "துணை வகை",
                            }
                        };


                        return `<b>${translations[lang].category}:</b> ${row[CategoryColumn]} <br>
                                <b>${translations[lang].subcategory}:</b> ${row[subcategoryColumn]}`;
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap"
                },

                {
                    data: "heading_en",
                    title: columnLabels?.["heading_en"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column wrap-50",
                    render: function(data, type, row) {
                        return row.heading_en || '-';
                    }
                },
                {
                    data: "heading_ta",
                    title: columnLabels?.["heading_ta"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column wrap-50",
                    render: function(data, type, row) {
                        return row.heading_ta || '-';
                    }
                },
                {
                    data: "partno",
                    title: columnLabels?.["partno"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.partno || '-';
                    }
                },

                {
                    data: "checkpoint_en",
                    title: columnLabels?.["checkpoint_en"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column wrap-50",
                    render: function(data, type, row) {
                        return row.checkpoint_en || '-';
                    }
                },
                {
                    data: "checkpoint_ta",
                    title: columnLabels?.["checkpoint_ta"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column wrap-50 ",
                    render: function(data, type, row) {
                        return row.checkpoint_ta || '-';
                    }
                },
                {
                    data: "objectiontype",
                    title: columnLabels?.["objectiontype"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const questionTypeMap = {
                            'O': 'Yes/No',
                            'N': 'Numerical',
                            'D': 'Description'
                        };

                        return questionTypeMap[row.objectiontype] || '-';
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
                    className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                },
                {
                    data: "encrypted_aifid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) =>
                        `<center><a class="btn editicon editsupercheckdel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                    className: "text-center noExport text-wrap"
                }
            ],

            "initComplete": function(settings, json) {
                $("#auditinspectiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [desigColumn, CategoryColumn, subcategoryColumn, "heading_en", "heading_ta",
            "partno", "checkpoint_en", "checkpoint_ta", "statusflag"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "auditinspectiontable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditinspectiontable')) {
            $('#auditinspectiontable').DataTable().clear().destroy();
        }
        renderTable(language);
    }



    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

        let titleKey = `${tableId}_title`;
        let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
        let safeSheetName = translatedTitle.substring(0, 31);
        // ? Fetch column headers from JSON layout
        let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


        const columnMap = {
            department: language === 'ta' ? 'depttamsname' : 'deptengsname',
            designation: language === 'ta' ? 'desigtlname' : 'desigelname',
            category: language === 'ta' ? 'cattamname' : 'catengname',
            subcategory: language === 'ta' ? 'subcategory_tname' : 'subcategory_ename'

        };

        let headers = [{
                header: dtText["department"] || "Department",
                key: "department"
            },
            {
                header: dtText["designation"] || "Designation",
                key: "designation"
            },
            {
                header: dtText["category"] || "Category",
                key: "category"
            },
            {
                header: dtText["subcategory"] || "Subcategory",
                key: "subcategory[]"
            },
            {
                header: dtText["heading_en"] || "Heading Name in English",
                key: "heading_en[]"
            },
            {
                header: dtText["heading_ta"] || "Heading Name in Tamil",
                key: "heading_ta[]"
            },
            {
                header: dtText["partno"] || "Part No",
                key: "part_no[]"
            },
            {
                header: dtText["checkpoint_en"] || "Checkpoint Name in English",
                key: "checkpoint_en[]"
            },
            {
                header: dtText["checkpoint_ta"] || "Checkpoint Name in Tamil",
                key: "checkpoint_ta[]"
            },
            {
                header: dtText["objectiontype"] || "Question Type",
                key: "question_type[]"
            },

        ];

        let rawData = table.rows({
            search: 'applied'
        }).data().toArray();

        let excelData = rawData.map(row => {
            let button = $(row[0]).find("button.toggle-row");
            let dataRow = button.attr("data-row");
            let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

            return {
                department: rowData[columnMap.department] || "-",
                designation: rowData[columnMap.designation] || "-",
                category: rowData[columnMap.category] || "-",
                subcategory: rowData[columnMap.subcategory] || "-",
                heading_en: rowData.heading_en || "-",
                heading_ta: rowData.heading_ta || "-",
                partno: rowData.partno || "-",
                checkpoint_en: rowData.checkpoint_en || "-",
                checkpoint_ta: rowData.checkpoint_ta || "-",
                question_type: rowData.objectiontype === "O" ? "Yes / No" : "-",



            };
        });

        if (excelData.length === 0) {
            alert("No data available for export!");
            return;
        }

        // ? Create Workbook and Worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);

        // ? Add Headers in Separate Columns (Avoid Merging Issues)
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A1"
        });

        // ? Ensure Headers Align with Data
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A2"
        });

        XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
        XLSX.writeFile(wb, `${safeSheetName}.xlsx`);



    }





    function validateSubcategoryFields(validator) {
        let subcategoryHasError = false;
        const if_subcategory = $('#if_subcategory').val();

        if (if_subcategory === 'Y') {
            $('.subcategory').each(function() {
                const $input = $(this);

                // Add rules only if validator is already initialized
                if (validator) {
                    $input.rules('add', {
                        required: true,
                        messages: {
                            required: "Enter Subcategory",
                        }
                    });
                }

                if (!$input.valid()) {
                    subcategoryHasError = true;
                    return false;
                }
            });
        } else {
            $('.subcategory').each(function() {
                if (validator) {
                    $(this).rules('remove');
                }
            });
        }

        return !subcategoryHasError;
    }



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#inspectionform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                desigcode: {
                    required: true
                },
                // subcategory: {
                //     required: true
                // },
                // heading_en: {
                //     required: true
                // },
                // heading_ta: {
                //     required: true
                // },
                // part_no: {
                //     required: true
                // },

                // checkpoint_en: {
                //     required: true
                // },
                // checkpoint_ta: {
                //     required: true
                // },
                // "question_type[]": {
                //     required: true
                // },
                statusflag: {
                    required: true

                }

            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },

        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();

            const validator = $("#inspectionform").data("validator");
            if (!validator) {
                $("#inspectionform").validate();
            }

            // Trigger validation on all relevant fields
            $(".heading_en, .heading_ta, .part_no, .checkpoint_en, .checkpoint_ta, .question_type")
                .each(function() {
                    $(this).valid();
                });

            let hasError = false;


            if (!validateSubcategoryFields(validator)) {
                hasError = true;
            }




            $('.heading_en, .heading_ta, .part_no, .checkpoint_en, .checkpoint_ta, .question_type')
                .each(function() {
                    if (!$(this).valid() || $(this).hasClass('is-invalid')) {
                        hasError = true;
                        return false; // break loop on first error
                    }
                });

            if (hasError) {
                return false; // prevent submission
            }



            if ($("#inspectionform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#inspectionform').serializeArray();

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
                // console.log(formData);
                $.ajax({
                    url: "{{ route('auditinspectform.auditinspectform_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
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
                          //  initializeDataTable(window.localStorage.getItem('lang'));


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


    function inspectionform(inspection) {
        $('#display_error').hide();
        $('#addActionContainer').addClass('hide_this').hide();
        $('#editschemeshow').addClass('hide_this');

        // $('#catcode').val(mainobjection.catcode);
        getCategoriesBasedOnDept('insert',inspection.deptcode, inspection.catcode, inspection.desigcode);

        setTimeout(() => {
            onchange_category(inspection.catcode, inspection.subcatid,'insert');
        }, 400);

        $('#heading_en').val(inspection.heading_en);
        $('#heading_ta').val(inspection.heading_ta);
        $('#part_no').val(inspection.partno);
        $('#checkpoint_en').val(inspection.checkpoint_en);
        $('#checkpoint_ta').val(inspection.checkpoint_ta);
        $('#question_type').val(inspection.objectiontype);
        $('#aifid').val(inspection.encrypted_aifid);
        populateStatusFlag(inspection.statusflag);
        $('#deptcode').val(inspection.deptcode).select2();



        //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editsupercheckdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#aifid').val(id);
            // alert(id);
            $.ajax({
                url: "/auditinspectform/auditinspectform_fetchData",
                method: 'POST',
                data: {
                    aifid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('inspectionform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            inspectionform(response.data[0]); // Populate form with data
                        } else {
                            alert('Audit Inspection data is empty');
                        }
                    } else {
                        alert('Audit Inspection data not found');
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


 function reset_form1() {
    $('#tableshow').hide();
    $('#no_data').show();

    $('#filterform')[0].reset();


    if (sessiondeptcode && sessiondeptcode.trim() !== '') {
        getCategoriesBasedOnDept('filter', sessiondeptcode, null);
    } else {
        getCategoriesBasedOnDept('filter', null);
    }

}



    function reset_form() {

        $('#inspectionform')[0].reset();
        $('#addActionContainer').removeClass('hide_this').show();
        $('#editschemeshow').addClass('hide_this');

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            getCategoriesBasedOnDept('insert',null);
            

        } else {

            getCategoriesBasedOnDept('insert', null);
            onchange_category(null,null,'insert');
        }

        changeButtonAction('inspectionform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

        $('#InspectionFields .scheme-row').not(':first').remove();

        let $firstRow = $('#InspectionFields .scheme-row:first');
        if ($firstRow.find('#addSchemeBtn').length === 0) {
            $firstRow.append(`
                <div class="col-md-2 mb-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-primary" id="addSchemeBtn">+ Add Action</button>
                </div>
            `);
        }

        //  updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
	

</script>


@endsection
