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
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="supercheck_head">Super Check</div>
            <div class="card-body">
                <form id="supercheckform" name="supercheckform" method="POST">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->`
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','')">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- ???????? ?????????????????---">---Select Department---</option>

                                @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                <option value="{{ $department->deptcode }}"
                                    @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                                    data-name-en="{{ $department->deptelname }}"
                                    data-name-ta="{{ $department->depttlname }}">
                                    {{ $department->deptelname }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="??????? ??????? ?????">No Departments Available</option>
                                @endif
                            </select>
                        </div>





                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                onchange="onchange_category('','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---??????? ?????????????????---">---Select Category---</option>

                                <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="??? ?????????????">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-4 mb-2 subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory"
                                for="subcategory">SubCategory</label>

                            <select class="form-control mr-sm-2 lang-dropdown select2" id="subcategory"
                                multiple="multiple" name="subcategory[]">
                                {{-- <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---???? ??????? ?????????????????---">---Select SubCategory---
                                </option>

                                <option value="" disabled data-name-en="No SubCategory Available"
                                    data-name-ta="???? ??? ?????????????">No SubCategory Available</option> --}}


                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="heading_en" for="heading_en">Heading Name in
                                English</label>
                            <input type="text" class="form-control name" id="heading_en" maxlength='300'
                                name="heading_en" required data-placeholder-key="heading_en">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="heading_ta" for="heading_ta">Heading Name in
                                Tamil</label>
                            <input type="text" class="form-control name" id="heading_ta" maxlength='400'
                                name="heading_ta" required data-placeholder-key="heading_ta">
                        </div>


                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="part_no" for="part_no">Part No</label>
                            <input type="text" class="form-control only_numbers" id="part_no"
                                data-placeholder-key="part_no" name="part_no" required />
                        </div>


                        {{--
                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="checkpoint_en" for="checkpoint_en">Checkpoint
                                Name in English</label>
                            <input type="text" maxlength='300' class="form-control name" id="checkpoint_en"
                                name="checkpoint_en" required data-placeholder-key="checkpoint_en">
                        </div>




                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="checkpoint_ta"
                                for="checkpoint_ta">Checkpoint Name in Tamil</label>
                            <input type="text" maxlength='400' class="form-control name" id="checkpoint_ta"
                                name="checkpoint_ta" required data-placeholder-key="checkpoint_ta">
                        </div> --}}
                        <div class="col-md-4 mb-2">
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


                        <div id="appendusers" class="single-note-item">
                            <div id="addrowUsers">
                                <div class="row">
                                    <div class="col-md-1">

                                    </div>



                                    <div class="col-md-3 ms-2">

                                    </div>
                                    <div class="col-md-3 ms-2">

                                    </div>
                                    <div class="col-md-3 ms-2">

                                    </div>



                                </div>
                                <div class="d-flex mt-2 work-row-insert" id="row0">
                                    <input type="hidden" name="officeuserid[0]" value="UserId_1">

                                    <div class="col-md-1">
                                        <label class="form-label lang" key="sl_no"
                                            for="validationDefaultUsername">S.No</label>
                                        <input type="text" class="form-control only_numbers" id="sl_no_0"
                                            data-placeholder-key="sl_no" name="sl_no[0]" disabled value="1"
                                            required />
                                    </div>

                                    <div class="col-md-2 ms-2">
                                        <label class="form-label lang" key="question_type"
                                            for="validationDefaultUsername">Question Type</label>
                                        <select class="form-select lang-dropdown" id="question_type_0"
                                            name="question_type[0]" disabled>
                                            <option value="" data-name-en="---Select Question Type---"
                                                data-name-ta="?????? ????? ?????????????????">---Select Question
                                                Type---</option>
                                            <option value="O" data-name-en="" data-name-ta="" selected>Yes/No
                                            </option>

                                            <option value="N" data-name-en="" data-name-ta="">Numerical</option>

                                            <option value="D" data-name-en="" data-name-ta="">Description
                                            </option>


                                        </select>
                                    </div>


                                    <div class="col-md-3 ms-2">
                                        <label class="form-label required lang" key="checkpoint_en"
                                            for="checkpoint_en">Checkpoint Name in English</label>
                                        <input type="text" maxlength='300' class="form-control name"
                                            id="checkpoint_en_0" name="checkpoint_en[0]" required
                                            data-placeholder-key="checkpoint_en">
                                    </div>
                                    <div class="col-md-3 ms-2">
                                        <label class="form-label required lang" key="checkpoint_ta"
                                            for="checkpoint_ta">Checkpoint Name in Tamil</label>
                                        <input type="text" maxlength='400' class="form-control name"
                                            id="checkpoint_ta_0" name="checkpoint_ta[0]" required
                                            data-placeholder-key="checkpoint_ta">
                                    </div>

                                    <div class="col-md-3 actionbtns">
                                        <label class="form-label lang" key="append_action"
                                            for="validationDefaultUsername">Action</label><br>
                                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn"
                                            onclick="addNewWorkRow(event,'insert')">
                                            <i class="ti ti-circle-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="EditrowUsers">

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

                            <input type="hidden" name="supercheckid" id="supercheckid" value="" />

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
            <div class="card-header card_header_color lang" key="supercheck_table">Super Check Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="superchecktable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <!-- <th class="lang align-middle text-center" key="if_subcategory">Sub Category</th> -->
                                    <th class="lang align-middle text-center" key="heading_en">Heading Name in English
                                    </th>
                                    <th class="lang align-middle text-center" key="heading_ta">Heading Name in Tamil
                                    </th>

                                    <th class="lang align-middle text-center" key="part_no">Part No</th>
                                    {{-- <th class="lang align-middle text-center" key="sl_no">Serial No</th> --}}

                                    <th class="lang align-middle text-center" key="checkpoint_en">Check Point Name in
                                        English</th>
                                    <th class="lang align-middle text-center" key="checkpoint_ta">Check Point in Tamil
                                    </th>
                                    <th class="lang align-middle text-center" key="question_type">Question Type</th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
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
    let data = "";

    function applyValidationToNewFields(inputName, message) {
        let $input = $(`[name='${inputName}']`);

        if ($input.length) {
            // Ensure validator is initialized once
            let validator = $("#supercheckform").data("validator");
            if (!validator) {
                if (!$("#supercheckform").data("validator")) {
                    console.log("? Initializing validator..."); // ? Add this
                    $("#supercheckform").validate({
                        errorPlacement: function(error, element) {
                            if (element.hasClass('datepicker')) {
                                error.insertAfter(element.closest('.input-group'));
                            } else {
                                error.insertAfter(element);
                            }
                        }
                    });
                } else {
                    console.log("?? Validator already initialized."); // ? Add this
                }

                validator = $("#supercheckform").data("validator");
            }

            // Remove any existing rule
            $input.rules("remove");

            // Add required rule
            $input.rules("add", {
                required: true,
                messages: {
                    required: message
                }
            });

            // Revalidate after rule applied
            validator.element($input);

            // Revalidate on change too
            $input.off("change").on("change", function() {
                $(this).valid();
            });
        } else {
            console.warn("Element not found:", inputName);
        }
    }



    function addNewWorkRow(event, action = '') {
        // Get the next row index based on existing rows
        const rowCount = $('.work-row-' + action + '').length; // Start from 2, hence +1


        var allowedusers = 9;

        if (rowCount > allowedusers) {
            getLabels_jsonlayout([{
                id: 'alloweduserlimit',
                key: 'alloweduserlimit'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', Object.values(
                        text)[0], 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'confirmation_alert');
            });
            return; // Prevent adding a new row
        }

        // HTML for the new row
        const newRowHtml = `
                <div class="d-flex mt-2 work-row-${action}" id="row${rowCount}">
                    <input type="hidden" name="qn_no[${rowCount}]" value="qn_no${rowCount}">

                    <div class="col-md-1">
                    <input type="text" class="form-control only_numbers" id="sl_no_${rowCount}" data-placeholder-key="sl_no" name="sl_no[${rowCount}]"
                                  value="${rowCount+1}"          disabled />                    </div>

                    <div class="col-md-2 ms-2">
                    <select class="form-select lang-dropdown" id="question_type_${rowCount}" name="question_type[${rowCount}]" disabled>
                                            <option value="" data-name-en="---Select Question Type---"
                                                data-name-ta="?????? ????? ?????????????????">---Select Question Type---</option>
                                            <option value="O" data-name-en="" data-name-ta="" selected>Yes/No
                                            </option>

                                            <option value="N" data-name-en="" data-name-ta="">Numerical</option>

                                            <option value="D" data-name-en="" data-name-ta="">Description</option>


                                        </select>

                        </div>
                                    <div class="col-md-3  ms-2">

                                        <input type="text" maxlength='300' class="form-control name"
                                            id="checkpoint_en_${rowCount}" name="checkpoint_en[${rowCount}]" required
                                            data-placeholder-key="checkpoint_en">
                                    </div>
                                    <div class="col-md-3  ms-2">

                                        <input type="text" maxlength='400' class="form-control name"
                                            id="checkpoint_ta_${rowCount}" name="checkpoint_ta[${rowCount}]" required
                                            data-placeholder-key="checkpoint_ta">
                                    </div>



                    <div class="col-md-3">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'${action}')">
                            <i class="ti ti-circle-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removedeptRow(this,'${action}')">
                            <i class="ti ti-circle-minus"></i>
                        </button>
                    </div>
                </div>
            `;

        if (action == 'insert') {
            $('#addrowUsers').append(newRowHtml);

        } else {
            $('#EditrowUsers').append(newRowHtml);

        }
        var lang = getLanguage('Y');


        //  applyValidationToNewFields(`sl_no[${rowCount}]`, 'Enter Serial Number');
        // applyValidationToNewFields(`question_type[${rowCount}]`, 'Select Question Type');
        // applyValidationToNewFields(`question_type[${rowCount}]`, 'Select Question Type');
        applyValidationToNewFields(`checkpoint_en[${rowCount}]`, 'Enter Checkpoint in English');
        applyValidationToNewFields(`checkpoint_ta[${rowCount}]`, 'Enter Checkpoint in Tamil');



        // Append the new row using jQuery to the container
    }

    function removedeptRow(button, action) {
        // Find the closest row with the specific class and remove it
        const rowToRemove = $(button).closest('.work-row-' + action);
        rowToRemove.remove();

        // Recalculate and update the S.NO values
        updateSerialNumbers(action);
    }

    function updateSerialNumbers(action) {
        $('.work-row-' + action).each(function(index) {
            // Update the row's ID
            $(this).attr('id', 'row' + index);

            // Update sl_no input
            $(this).find('input[name^="sl_no"]').attr({
                'id': 'sl_no_' + index,
                'name': 'sl_no[' + index + ']',
                'value': index + 1
            });

            // Update question_type select
            $(this).find('select[name^="question_type"]').attr({
                'id': 'question_type_' + index,
                'name': 'question_type[' + index + ']'
            });

            // Update checkpoint_en input
            $(this).find('input[name^="checkpoint_en"]').attr({
                'id': 'checkpoint_en_' + index,
                'name': 'checkpoint_en[' + index + ']'
            });

            // Update checkpoint_ta input
            $(this).find('input[name^="checkpoint_ta"]').attr({
                'id': 'checkpoint_ta_' + index,
                'name': 'checkpoint_ta[' + index + ']'
            });

            // Update hidden qn_no input
            $(this).find('input[name^="qn_no"]').attr({
                'name': 'qn_no[' + index + ']',
                'value': 'qn_no' + index
            });
        });
    }


    function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');

        lang = getLanguage('');

        $('#category').empty();

        $("#subcategory").empty();

        catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---??????? ?????????????????---">
                ${lang === 'ta' ? '---??????? ?????????????????---' : '---Select Category---'}
            </option>
            `);

        //     subcategoryDropdown.html(`
        //     <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
        //         ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
        //     </option>
        // `);


        // alert(lang);
        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {


            catcodeDropdown.append(`
        <option value="" disabled data-name-en="No Category Available" data-name-ta="??? ?????????????">
            ${lang === 'ta' ? '??? ?????????????' : 'No Category Available'}
        </option>


`);
            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);
        }

        if (deptcode) {
            $.ajax({
                url: "/getcategoriesbasednndeptforsupercheck",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    data = response;


                    //alert();


                    if (response.length > 0) {
                        response.forEach(category => {
                            catcodeDropdown.append(`
                        <option value="${category.catcode}"
                            data-name-en="${category.catename}"
                            subcategory="${category.if_subcategory}"
                            data-name-ta="${category.cattname}"
                            ${category.catcode === selectedCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? category.cattname : category.catename}
                        </option>

                    `);

                        });
                    } else {
                        catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="??? ?????????????">
                        ${lang === 'ta' ? '??? ?????????????' : 'No Category Available'}
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




    function onchange_category(catcode, selectedsubCatcode = null) {
        var catcode = catcode || $('#category').val();
        var selectedOption = $('#category').find(':selected');
        var subcategory = subcategory || selectedOption.attr('subcategory');
        // var subcategory = selectedOption.attr('subcategory'); // Ensure correct retrieval
        let lang = getLanguage('');



        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty();

        // subcategoryDropdown.append(`
        //     <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
        //         ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
        //     </option>
        // `);


        if (!catcode) {

            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);

        }

        $.ajax({
            url: '/getsubcategoriesbasedondeptforsupercheck', // Your API route to get user details
            method: 'POST',
            data: {
                category: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },


            success: function(response) {
                // alert(subcategory);

                if (subcategory === 'Y') {


                    if (response && response.length > 0) {

                        response.forEach(subcategory => {
                            subcategoryDropdown.append(`
                        <option value="${subcategory.auditeeins_subcategoryid}"
                            data-name-en="${subcategory.subcatename}"
                            data-name-ta="${subcategory.subcattname}"
                            ${subcategory.auditeeins_subcategoryid === selectedsubCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? subcategory.subcattname : subcategory.subcatename}
                        </option>
                    `);

                        });

                    } else {
                        subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);
                    }
                } else {

                    $.each(data, function(i, subcategory) {
                        // alert("else");

                        if (subcategory.catcode === catcode) {
                            $('#subcategory').append(
                                `<option value="" data-name-en="${subcategory.catename}" data-name-ta="${subcategory.cattname}" selected>
                                  ${lang === "ta" ? subcategory.cattname : subcategory.catename}
                                 </option>`
                            );
                        }
                    });


                }


            },
            error: function(xhr, status, error) {
                // alert('enter')


            }
        });

    }










    let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#supercheckform')[0].reset();
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            getCategoriesBasedOnDept(sessiondeptcode, '', '', '', '', '');
        }

        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);





    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        //  updateValidationMessages(getLanguage('Y'), 'supercheckform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('supercheck.supercheck_fetchData') }}",
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
        const departmentColumn = language === 'ta' ? 'depttamsname' : 'deptengsname';
        const CategoryColumn = language === 'ta' ? 'cattamname' : 'catengname';
        const subcategoryColumn = language === 'ta' ? 'subcategory_tname' : 'subcategory_ename';


        if ($.fn.DataTable.isDataTable('#superchecktable')) {
            $('#superchecktable').DataTable().clear().destroy();
        }

        table = $('#superchecktable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
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
                    data: CategoryColumn,
                    render: function(data, type, row) {

                        const lang = (language === "ta") ? "ta" : "en";

                        const translations = {
                            en: {
                                category: "Category",
                                subcategory: "Subcategory",
                            },
                            ta: {
                                category: "???",
                                subcategory: "???? ???",
                            }
                        };


                        return `<b>${translations[lang].category}:</b> ${row[CategoryColumn]} <br>
                                <b>${translations[lang].subcategory}:</b> ${row[subcategoryColumn]}`;
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap"
                },

                // {
                //     data: CategoryColumn,
                //     title: columnLabels?.[CategoryColumn]?.[language],
                //     render: function(data, type, row) {
                //         return row[CategoryColumn] || '-';
                //     },
                //     className: 'text-wrap text-start' // Removed col-1
                // },
                // {
                //     data: subcategoryColumn,
                //     title: columnLabels?.[subcategoryColumn]?.[language],
                //     render: function(data, type, row) {
                //         return row[subcategoryColumn] || '-';
                //     },
                //     className: 'text-wrap text-start'
                // },
                {
                    data: "heading_en",
                    title: columnLabels?.["heading_en"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.heading_en || '-';
                    }
                },
                {
                    data: "heading_ta",
                    title: columnLabels?.["heading_ta"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.heading_ta || '-';
                    }
                },
                {
                    data: "part_no",
                    title: columnLabels?.["part_no"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.part_no || '-';
                    }
                },
                // {
                //     data: "sl_no",
                //     title: columnLabels?.["sl_no"]?.[language],
                //     className: "d-none d-md-table-cell lang extra-column text-wrap",
                //     render: function(data, type, row) {
                //         return row.sl_no || '-';
                //     }
                // },
                {
                    data: "checkpoint_en",
                    title: columnLabels?.["checkpoint_en"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.checkpoint_en || '-';
                    }
                },
                {
                    data: "checkpoint_ta",
                    title: columnLabels?.["checkpoint_ta"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.checkpoint_ta || '-';
                    }
                },
                {
                    data: "question_type",
                    title: columnLabels?.["question_type"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const questionTypeMap = {
                            'O': 'Yes/No',
                            'N': 'Numerical',
                            'D': 'Description'
                        };

                        return questionTypeMap[row.question_type] || '-';
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
                }

            ],

            "initComplete": function(settings, json) {
                $("#superchecktable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [departmentColumn, CategoryColumn, subcategoryColumn, "heading_en", "heading_ta",
            "part_no", "checkpoint_en", "checkpoint_ta", "statusflag"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "superchecktable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#superchecktable')) {
            $('#superchecktable').DataTable().clear().destroy();
        }
        renderTable(language);
    }









    const language = window.localStorage.getItem('lang') || 'en';
    var validator = $("#supercheckform").validate({

        rules: {
            deptcode: {
                required: true,
            },
            category: {
                required: true
            },
            // subcategory: {
            //     required: true
            // },
            heading_en: {
                required: true
            },
            heading_ta: {
                required: true
            },
            part_no: {
                required: true
            },
            // sl_no: {
            //     required: true
            // },
            // checkpoint_en: {
            //     required: true
            // },
            // checkpoint_ta: {
            //     required: true
            // },
            // question_type: {
            //     required: true
            // },
            statusflag: {
                required: true

            }

        },
        message: {
            deptcode: {
                required: "Select Department",
            },
            category: {
                required: "Select Category"
            },

            heading_en: {
                required: "Enter Heading in English"
            },
            heading_ta: {
                required: "Enter Heading in Tamil"
            },
            part_no: {
                required: "Enter Part Number"
            },
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
        applyValidationToNewFields(`checkpoint_en[0]`, 'Enter Checkpoint in English');
        applyValidationToNewFields(`checkpoint_ta[0]`, 'Enter Checkpoint in Tamil');
        if ($("#supercheckform").valid()) {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = $('#supercheckform').serializeArray();

            var deptcode = $('#deptcode').val();
            if ($('#deptcode').prop('disabled')) {

                formData.push({
                    name: 'deptcode',
                    value: deptcode
                });
            }
            $('input[id^="sl_no"]:disabled').each(function() {
                formData.push({
                    name: $(this).attr('name'),
                    value: $(this).val()
                });
            });
            $('select[id^="question_type"]:disabled').each(function() {
                const name = $(this).attr('name');
                const value = $(this).find('option:selected')
                    .val(); // safer than .val() directly
                if (value) {
                    formData.push({
                        name: name,
                        value: value
                    });
                }
            });
            // formData.push({
            //     name: "lang",
            //     value: getLanguage('N')
            // });
            // console.log(formData);
            $.ajax({
                url: "{{ route('supercheck.supercheck_multiinsert') }}",
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
                        initializeDataTable(window.localStorage.getItem('lang'));


                    } else {


                    }
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {

                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    }
                }
            });

        } else {

        }



    });
    reset_form();




    function supercheckform(supercheck) {
        $('#display_error').hide();
        // $('#catcode').val(mainobjection.catcode);
        getCategoriesBasedOnDept(supercheck.deptcode, supercheck.catcode);

        setTimeout(() => {
            onchange_category(supercheck.catcode, supercheck.subcatcode);
        }, 400);

        $('#heading_en').val(supercheck.heading_en);
        $('#heading_ta').val(supercheck.heading_ta);
        $('#part_no').val(supercheck.part_no);
        $('#sl_no').val(supercheck.sl_no);
        $('#checkpoint_en').val(supercheck.checkpoint_en);
        $('#checkpoint_ta').val(supercheck.checkpoint_ta);
        $('#question_type').val(supercheck.question_type);
        $('#supercheckid').val(supercheck.encrypted_supercheckid);
        populateStatusFlag(supercheck.statusflag);
        $('#deptcode').val(supercheck.deptcode).select2();



        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editsupercheckdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#supercheckid').val(id);
            // alert(id);
            $.ajax({
                url: "{{ route('supercheck.supercheck_fetchData') }}",
                method: 'POST',
                data: {
                    supercheckid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            // changeButtonAction('supercheckform', 'action', 'buttonaction',
                            //     'reset_button', 'display_error', @json($updatebtn),
                            //     @json($clearbtn), @json($update))
                            supercheckform(response.data[0]); // Populate form with data
                        } else {
                            alert('supercheck data is empty');
                        }
                    } else {
                        alert('supercheck not found');
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

        $('#supercheckform')[0].reset();


        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            // $('#ename, #fname').val();

        } else {

            $('#deptcode').val(null).select2();
            getCategoriesBasedOnDept(null);


        }
        $('#category').val(null).select2();

        $("#subcategory").empty();

        $('#work-row-insert').show();


        $('#EditrowUsers').html('');

        $('#row0').addClass('d-flex');
        $('#addrowUsers').children('.work-row-insert').each(function() {
            if ($(this).attr('id') !== 'row0') {
                $(this).remove();
            }
        });

        var rowcount = '0'
        // changeButtonAction('supercheckform', 'action', 'buttonaction', 'reset_button', 'display_error',
        //     @json($savebtn), @json($clearbtn), @json($insert))

        //    updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection