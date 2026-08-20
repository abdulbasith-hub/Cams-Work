@section('content')

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
            <div class="card-header card_header_color lang" key="auditeedept_head">Auditee HO Details</div>
            <div class="card-body">
                <form id="auditeedepartmentform" name="auditeedepartmentform">
                    @csrf
                    <div class="row">

                    <input type="hidden" name="if_subcategory" id="if_subcategory" value="">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','')">
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

                        <div class="col-md-4 mb-4">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                >
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                 <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>

                        <div class="col-md-4 subcatdiv">
                    <label class="form-label lang required" key="if_subcategory" for="subcategory">SubCategory</label>
                    <select class="mr-sm-2 lang-dropdown select2 subcategory"
                            multiple="multiple"
                            id="subcategory"
                            name="subcategory[]" >
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>
                    </select>
                </div>


                        <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" id="instmappingcode" name="instmappingcode" >

                                <option value="" data-name-en="Select Auditable Institution" data-name-ta="தணிக்கை நிறுவனத்தைத் தேர்வு செய்க">
                                Select Auditable Institution
                            </option>

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>


                                </select>
                    </div>




                        <div class="col-md-4 mb-2 ">
                            <label class="form-label required lang" for="auditeedepartment"
                                key="">Auditee Department</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="auditeedepartment"
                                name="auditeedepartment">
                                <option value="" data-name-en=" Select a Department"
                                    data-name-ta="பதவியை தேர்வு செய்">Select a Department</option>



                            </select>
                        </div>




                        <div class="col-md-4 mb-2 ">
                            <label class="form-label required lang" for="desigcode"
                                key="designation">Designation</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="desigcode"
                                name="desigcode">
                                <option value="" data-name-en=" Select a Designation"
                                    data-name-ta="பதவியை தேர்வு செய்">Select Designation</option>



                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="email" for="email">Email</label>
                            <input type="email" maxlength='100' class="form-control" id="email" name="email"
                                data-placeholder-key="email" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="mobile" for="mobile">Mobile
                                Number</label>
                            <input type="text"  class="form-control removesplchar_number " id="mobile"
                                name="mobile" maxlength="10" data-placeholder-key="mobile" required>
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


                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="auditeedeptid" id="auditeedeptid" value="" />

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
            <div class="card-header card_header_color lang" key="">HOD Login Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="hologindetails"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <th class="lang align-middle text-center" key="if_subcategory">Subcategory</th>
                                    <th class="lang align-middle text-center" key="">Auditable Institution</th>
                                    <th class="lang align-middle text-center" key="">Auditee Department</th>
                                    <!-- <th class="lang align-middle text-center" key="auditeedepttname">Department Name in Tamil</th> -->
                                    <th class="lang align-middle text-center" key="designation">Designation</th>
                                    <th class="lang align-middle text-center" key="email">Email</th>
                                    <th class="lang align-middle text-center" key="mobile">Mobile Number</th>
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
        $('#auditeedepartmentform')[0].reset();
      //  updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

        getCategoriesBasedOnDept(sessiondeptcode, '');
        }

    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'auditeedepartmentform');
    });





    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('hologindetails_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
              //  console.log("Success Response:", json);
                if (json.data && json.data.length > 0) {
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



    $(document).ready(function () {
        restrictSpecialChars('.removesplchar_text');
        restrictSpecialChars('.removesplchar_number');

    });



function restrictSpecialChars(elements) {
    $(elements).each(function () {
        const $el = $(this);

        $el.off("keypress paste")
            .on("keypress", function (event) {
                const char = String.fromCharCode(event.which);
                const value = this.value;

                if ($el.hasClass('removesplchar_text')) {
                    if (!/^[a-zA-Z\s\u0B80-\u0BFF]$/.test(char)) {
                        event.preventDefault();
                    }
                }  if ($el.hasClass('removesplchar_number')) {
                    // Rule 1: First digit must be 6-9
                    if (value.length === 0 && !/^[6-9]$/.test(char)) {
                        event.preventDefault();
                        return;
                    }

               if (value.length > 0 && !/^[0-9]$/.test(char)) {
                        event.preventDefault();
                        return;
                    }

                    const repeatCheck = (value + char).match(/^(\d)\1+$/);
                    if (repeatCheck && (value + char).length > 3) {
                        event.preventDefault();
                        return;
                    }

                    // Rule 4: Limit to maxlength if present
                    const maxLength = parseInt($el.attr('maxlength')) || 10;
                    if (value.length >= maxLength) {
                        event.preventDefault();
                    }
                }
            })
            .on("paste", function (e) {
                e.preventDefault();
                let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                let cleanData = '';

                if ($el.hasClass('removesplchar_text')) {
                    cleanData = pasteData
                        .replace(/[^a-zA-Z\u0B80-\u0BFF]+/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                } else if ($el.hasClass('removesplchar_number')) {
                    if (cleanData.length > 0 && !/^[6-9]/.test(cleanData.charAt(0))) {
                        cleanData = cleanData.substring(1);
                    }

                    if (/^(\d)\1+$/.test(cleanData)) {
                        cleanData = cleanData.charAt(0);
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

function clearSubcategoryErrors() {
    $('#subcategory').each(function() {
        $(this).removeClass('error');
        var errorLabel = $("label[for='" + $(this).attr('id') + "'].error");
        if (errorLabel.length) {
            errorLabel.remove();
        }
    });
}




let data = "";




function getCategoriesBasedOnDept(deptcode, selectedCatcode = null,selectedDesigCode=null,selectedauditeedepartment=null) {

        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');
        const designationDropdown = $('#desigcode');
        const institutionDropdown = $('#instmappingcode');
        const AuditeedepartmentDropdown = $('#auditeedepartment');

        const lang = getLanguage();

        $('#category').empty();
        $('#auditeedepartment').empty();

        $('#instmappingcode').empty();


        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {

            catcodeDropdown.append(`
        <option value=""  data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
        </option>

        `);


        catcodeDropdown.append(`
        <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
            ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
        </option>


        `);
        subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `);
        }


        institutionDropdown.append(`
        <option value="" data-name-en="---Select Institution---" data-name-ta="---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Institution---'}
        </option>
        `);



        AuditeedepartmentDropdown.append(`
            <option value="" data-name-en="---Select Department---" data-name-ta="---துறையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துறையைத் தேர்ந்தெடுக்கவும்---' : '---Select a Department---'}
            </option>
        `);


        subcategoryDropdown.empty().append(`
            <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                ${lang === 'ta' ? 'அனைத்து' : 'All'}
            </option>
        `);

        if(deptcode){

        catcodeDropdown.append(`
                    <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                        ${lang === 'ta' ? 'அனைத்து' : 'All'}
                    </option>
                `);

        designationDropdown.html(`
        <option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">
            ${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}
        </option>
    `);

        institutionDropdown.append(`
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                            ${lang === 'ta' ? 'அனைத்து' : 'All'}
                        </option>
                    `);
                }

        if (deptcode) {
            $.ajax({
                url: "/getCategoriesBasedOnDeptforhologin",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    data = response;
                    if (response.success && response.categories.length > 0) {
                        response.categories.forEach(category => {
                            const isSelected = Array.isArray(selectedCatcode)
                                ? selectedCatcode.includes(Number(category.catcode))
                                : category.catcode === selectedCatcode;

                            catcodeDropdown.append(`
                                <option value="${category.catcode}"
                                    data-name-en="${category.catename}"
                                    subcategory="${category.if_subcategory}"
                                    data-name-ta="${category.cattname}"
                                    ${isSelected ? 'selected' : ''}>
                                    ${lang === 'ta' ? category.cattname : category.catename}
                                </option>
                            `);
                            $('#if_subcategory').val(category.if_subcategory);

                            if (category.if_subcategory === 'Y') {
                                $('.subcatdiv').show();
                            } else {
                                $('.subcatdiv').hide();
                            }
                        });
                    } else {
                catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
            }

            if (response.success && response.auditeedepartment.length > 0) {

                response.auditeedepartment.forEach(auditeedepartment => {
                    const isSelected = auditeedepartment.auditeedeptcode === selectedauditeedepartment ? 'selected' : '';

                    AuditeedepartmentDropdown.append(`
                        <option value="${auditeedepartment.auditeedeptcode}" data-name-en="${auditeedepartment.auditeedeptename}" data-name-ta="${auditeedepartment.auditeedepttename}" ${isSelected}>
                            ${lang === 'ta' ? auditeedepartment.auditeedepttename : auditeedepartment.auditeedeptename}
                        </option>
                    `);
                });
            } else {
                AuditeedepartmentDropdown.append(`
            <option disabled data-name-en="No Department Available" data-name-ta="துறை கிடைக்கவில்லை">
                ${lang === 'ta' ? 'துறை கிடைக்கவில்லை' : 'No Department Available'}
            </option>
        `);

            }





                if (response.success && response.designations.length > 0) {

                    response.designations.forEach(designations => {
                        const isSelected = designations.desigcode === selectedDesigCode ? 'selected' : '';

                        designationDropdown.append(`
                            <option value="${designations.desigcode}" data-name-en="${designations.desigelname}" data-name-ta="${designations.desigtlname}" ${isSelected}>
                                ${lang === 'ta' ? designations.desigtlname : designations.desigelname}
                            </option>
                        `);
                    });
                } else {
                    designationDropdown.append(`
                        <option disabled data-name-en="No Designation Available" data-name-ta="பதவி கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'பதவி கிடைக்கவில்லை' : 'No Designation Available'}
                        </option>
                    `);
                }

                clearSubcategoryErrors();
            },
                            error: function() {
                                alert('Error fetching categories. Please try again.');
                            }
                        });
                    }
                }



$(document).ready(function () {

    $('#subcategory').on('change', function () {
        onchange_subcategory();
        $(this).valid();
    });
});




function onchange_subcategory(deptcode, catcode, subcatecode, selectedInstitution = '') {
    const institutionDropdown = $('#instmappingcode');


    if (!deptcode) deptcode = $("#deptcode").val();
    if (!catcode) catcode = $("#category").val();
    if (!subcatecode) subcatecode = $("#subcategory").val();

    if (!Array.isArray(subcatecode)) {
        subcatecode = subcatecode ? [subcatecode] : [];
    }

    institutionDropdown.empty();
    institutionDropdown.append(`
        <option value="" data-name-en="---Select Institution---" data-name-ta="---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Institution---'}
        </option>
    `);

    if(selectedInstitution =='A'){

        institutionDropdown.append(`
            <option value="A" selected>
                ${lang === 'ta' ? 'அனைத்து' : 'All'}
            </option>
        `);
        $('#instmappingcode').val('A').select2();

        return;

    }


    if (subcatecode.includes('A')) {
        institutionDropdown.append(`
            <option value="A" selected>
                ${lang === 'ta' ? 'அனைத்து' : 'All'}
            </option>
        `);
        return;
    }


    if (!deptcode) return;

    // Fetch institutions via AJAX
    $.ajax({
        url: '/getinstbasedonsubcategoryhologin',
        method: 'POST',
        data: {
            deptcode: deptcode,
            catcode: catcode,
            subcatcode: subcatecode
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

                if (response && response.length > 0) {
                institutionDropdown.append(`
                <option value="A">
                    ${lang === 'ta' ? 'அனைத்து' : 'All'}
                </option>
            `);
                response.forEach(inst => {
                    institutionDropdown.append(`
                        <option value="${inst.instid}"
                            data-name-en="${inst.instename}"
                            data-name-ta="${inst.insttname}"
                            ${selectedInstitution && String(selectedInstitution) === String(inst.instid) ? 'selected' : ''}>
                            ${lang === 'ta' ? inst.insttname : inst.instename}
                        </option>
                    `);
                });
            } else {
                institutionDropdown.append(`
                    <option disabled data-name-en="No Institution Available" data-name-ta="நிறுவனம் கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'நிறுவனம் கிடைக்கவில்லை' : 'No Institution Available'}
                    </option>
                `);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error loading institutions:", error);
        }
    });
}

$(document).on('change', '#category', function () {
    const subcategoryDropdown = $('#subcategory');
    subcategoryDropdown.empty();  // always reset subcategory

    let subcat = subcategoryDropdown.val();
    let subcatyes = $("#if_subcategory").val();

    if (subcatyes === "Y") {
        if (subcat && subcat.length > 0) {
            onchange_subcategory('');
        } else {
            onchange_category();
        }
    } else {
        onchange_category();
        onchange_subcategory('');
    }
});



function onchange_category(catcode, selectedsubCatcode = null,subcategory) {
        var categoryCode  =  $('#category').val();
        var selectedOption = $('#category').find(':selected');
        var subcategory = subcategory || selectedOption.attr('subcategory');
     // var subcategory = selectedOption.attr('subcategory'); // Ensure correct retrieval
            let lang = getLanguage();

        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty();


        if (selectedsubCatcode && selectedsubCatcode.includes('A')) {
            subcategoryDropdown.append(`
                <option value="A" selected  data-name-en="All" data-name-ta="அனைத்து">
                    ${lang === 'ta' ? 'அனைத்து' : 'All'}
                </option>
            `);
        }else {
            // Add "All" option but NOT selected
            subcategoryDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                    ${lang === 'ta' ? 'அனைத்து' : 'All'}
                </option>
            `);
        }


        // if (selectedsubCatcode.includes('A')) {
        //     subcategoryDropdown.append(`
        //         <option value="A" selected>
        //             ${lang === 'ta' ? 'அனைத்து' : 'All'}
        //         </option>
        //     `);
        // }


        // if (subcategory === 'Y') {

        //     subcategoryDropdown.append(`
        //         <option value="A"  data-name-en="All" data-name-ta="அனைத்து">
        //             ${lang === 'ta' ? 'அனைத்து' : 'All'}
        //         </option>
        //     `);
        // }

        $.ajax({
            url: '/getsubcatbasedoncategoryhologin', // Your API route to get user details
            method: 'POST',
            data : {
                category: categoryCode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },


            success: function(response) {

             //   if (subcategory === 'Y') {

                    if (response && response.length > 0) {



                        response.forEach(subcategory => {
                            subcategoryDropdown.append(`
                        <option value="${subcategory.auditeeins_subcategoryid}"
                            data-name-en="${subcategory.subcatename}"
                            data-name-ta="${subcategory.subcattname}"
                            ${Array.isArray(selectedsubCatcode) && selectedsubCatcode.includes(String(subcategory.auditeeins_subcategoryid)) ? 'selected' : ''}>
                            ${lang === 'ta' ? subcategory.subcattname : subcategory.subcatename}
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
                // } else {
                //     // Find the selected category from your global `data.categories`
                //     const selectedCategory = (data.categories || []).find(cat => String(cat.catcode) === String(categoryCode));

                //     if (selectedCategory) {
                //         $('#subcategory').append(`
                //             <option value="${selectedCategory.catcode}"
                //                 data-name-en="${selectedCategory.catename}"
                //                 data-name-ta="${selectedCategory.cattname}"
                //                 selected>
                //                 ${lang === "ta" ? selectedCategory.cattname : selectedCategory.catename}
                //             </option>
                //         `);
                //     } else {

                //     }
                // }
                clearSubcategoryErrors();


            },
            error: function(xhr, status, error) {
               // alert('enter')


            }

        });



}





    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const CategoryColumn = language === 'ta' ? 'category_names_tamil' : 'category_names';
        const subcategoryColumn = language === 'ta' ? 'subcategory_names_tamil' : 'subcategory_names';
        const designationColumn = language === 'ta' ? 'desigtlname' : 'desigelname';
        const AuditeeepartmentColumn = language === 'ta' ?  ' auditeedepttname' : 'auditeedeptename'

        if ($.fn.DataTable.isDataTable('#hologindetails')) {
            $('#hologindetails').DataTable().clear().destroy();
        }

        table = $('#hologindetails').DataTable({
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
                    data: CategoryColumn,
                    title: columnLabels?.[CategoryColumn]?.[language],
                    render: function(data, type, row) {
                        return row[CategoryColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: subcategoryColumn,
                    title: columnLabels?.[subcategoryColumn]?.[language],
                    render: function(data, type, row) {
                        return row[subcategoryColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },

                {
                    data: "instname_eng",
                    title: columnLabels?.["instname_eng"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.instname_eng || '-';
                    },

                },

                {
                    data: AuditeeepartmentColumn,
                    title: columnLabels?.[AuditeeepartmentColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row[AuditeeepartmentColumn] || '-';
                    },

                },
                // {
                //     data: "auditeedepttname",
                //     title: columnLabels?.["auditeedepttname"]?.[language],
                //     className: "d-none d-md-table-cell lang extra-column text-wrap",
                //     render: function(data, type, row) {
                //         return row.auditeedepttname || '-';
                //     },

                // },
                {
                    data: designationColumn,
                    title: columnLabels?.[designationColumn]?.[language],
                    render: function(data, type, row) {
                        return row[designationColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "email",
                    title: columnLabels?.["email"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.email || '-';
                    },

                },
                {
                    data: "mobilenumber",
                    title: columnLabels?.["mobilenumber"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.mobilenumber || '-';
                    },

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
                    data: "encrypted_auditeedeptid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) =>
                        `<center><a class="btn editicon editworkallocationdel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                    className: "text-center noExport"
                }
            ],

            "initComplete": function(settings, json) {
                $("#hologindetails").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["auditeedeptename", "auditeedepttname", "statusflag"];
        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable("en", "hologindetails"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#hologindetails')) {
            $('#hologindetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    function isValidMobileNumber(mobile) {
    const pattern = /^[6-9][0-9]{9}$/;
    return pattern.test(mobile);
}



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        $.validator.addMethod("validMobile", function(value, element) {
        return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
    });

        $.validator.addMethod("validEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    });







function validateSubcategoryFields(validator) {
    let subcategoryHasError = false;
    const if_subcategory = $('#if_subcategory').val();

    if (if_subcategory === 'Y') {
        $('.subcategory').each(function () {
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
        $('.subcategory').each(function () {
            if (validator) {
                $(this).rules('remove');
            }
        });
    }

    return !subcategoryHasError;
}



        var validator = $("#auditeedepartmentform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true,
                },
  		auditeedepartment : {
                    required: true

                },
                auditeedeptename: {
                    required: true
                },
                instmappingcode: {
                    required: true
                },
                desigcode: {
                    required: true
                },
                email: {
                    required: true,
                    validEmail: true
                },
                mobile: {
                    required: true,
                    validMobile: true

                },
                statusflag: {
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

        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();

            let hasError = false;

            if (!validateSubcategoryFields(validator)) {
            hasError = true;

            }

            if (hasError) {
                return false;
            }



            if ($("#auditeedepartmentform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#auditeedepartmentform').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }

                $.ajax({
                    url: "{{ route('hologindetails_insertupdate') }}",
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


                        } else if (response.error) {

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


    function auditeedepartmentform(auditeedept) {
        $('#display_error').hide();
        $('#auditeedeptename').val(auditeedept.auditeedepartment);
        // $('#auditeedepttname').val(auditeedept.auditeedepttname);
        $('#email').val(auditeedept.email);
        $('#mobile').val(auditeedept.mobilenumber);
        $('#auditeedeptid').val(auditeedept.encrypted_auditeedeptid);
        $('#designation1').val(auditeedept.designation);
        populateStatusFlag(auditeedept.statusflag);
        $('#deptcode').val(auditeedept.deptcode).select2();
        let catcodeData = auditeedept.catcode;

        if (typeof catcodeData === 'string') {
            try {
                catcodeData = catcodeData.replace(/\+/g, '');
                catcodeData = JSON.parse(catcodeData);
            } catch (e) {
                catcodeData = {};
            }
        }

        let categoryArray = Array.isArray(catcodeData["1"]) ? catcodeData["1"] : [];

        // Ensure numbers
        categoryArray = categoryArray.map(v => Number(v)).filter(v => !isNaN(v));
        getCategoriesBasedOnDept(auditeedept.deptcode, categoryArray,auditeedept.designation,auditeedept.auditeedeptcode);

        let subCatArray = [];
        let catcodeString = '';
        let instituteString = '';

try {
    let parsed = auditeedept.auditeeins_subcategoryid;
    if (typeof parsed === 'string') {
        parsed = parsed.replace(/\+/g, '');
        parsed = JSON.parse(parsed);
    }
    if (parsed && Array.isArray(parsed["1"])) {
        subCatArray = parsed["1"].map(String);
    }
} catch (err) {
    console.error("Subcategory parse failed:", err);
}

try {
    let parsedCat = auditeedept.catcode;
    if (typeof parsedCat === 'string') {
        parsedCat = parsedCat.replace(/\+/g, '');
        parsedCat = JSON.parse(parsedCat);
    }
    if (parsedCat && Array.isArray(parsedCat["1"])) {
        catcodeString = parsedCat["1"][0]; // if single category
    }
} catch (err) {
    console.error("Category parse failed:", err);
}


try {
    let parsedCat = auditeedept.instid;
    if (typeof parsedCat === 'string') {
        parsedCat = parsedCat.replace(/\+/g, '');
        parsedCat = JSON.parse(parsedCat);
    }
    if (parsedCat && Array.isArray(parsedCat["1"])) {
    instituteString = parsedCat["1"].join(",");
}
} catch (err) {
    console.error("Category parse failed:", err);
}



setTimeout(() => {
    onchange_category(catcodeString, subCatArray, auditeedept.if_subcategory);
    onchange_subcategory(auditeedept.deptcode, catcodeString,subCatArray,instituteString)

}, 400);



    }

    $(document).on('click', '.editworkallocationdel', function() {
        const id = $(this).attr('id');
        // alert(id);
        if (id) {
            reset_form();
            $('#auditeedeptid').val(id);

            $.ajax({
                url: "{{ route('hologindetails_fetchData') }}",
                method: 'POST',
                data: {
                    auditeedeptid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('auditeedepartmentform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            auditeedepartmentform(response.data[0]);
                        } else {
                            alert('Auditee department data is empty');
                        }
                    } else {
                        alert('Auditee department not found');
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

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#auditeedepartment').val();

        } else {
            $('#deptcode').val(null).select2();
        }


        $('#category').val('').select2();
        $('#auditeedepartment').val('').select2();
	getCategoriesBasedOnDept(null);
        $('#desigcode').val(null).empty().append(`<option value="">${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}</option>`).select2();

$('#subcategory').val(null).trigger('change');

        changeButtonAction('auditeedepartmentform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

      //  updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
