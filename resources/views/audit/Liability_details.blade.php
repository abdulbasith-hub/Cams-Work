@section('content')

@extends('index2')
@include('common.alert')
@php

$sessionchargedel = session('charge');
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;

    $hod_roletypecode = $HOD_roletypecode;

    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $catcode = [];
    $subcatcode = [];
    $instid = [];

if($sessionchargedel->usertypecode == 'H'){
    $catcode = json_decode($sessionchargedel->catcode, true);
    $subcatcode = json_decode($sessionchargedel->auditeeins_subcategoryid ?? '{}', true);
    $instid = json_decode($sessionchargedel->instid ?? '{}', true);


}



    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_catcode_disable = $catcode ? 'disabled' : '';
    $make_subcatcode_disable = $subcatcode ? 'disabled' : '';
    $make_institution_disable = $instid ? 'disabled' : '';



@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="col-12">
    <div class="card card_border">
    <div class="card-header card_header_color">Liability Report</div>
    <div class="card-body">
        <input type="hidden" name="if_subcategory" id="if_subcategory" value="" />

            <form id="liabilities_form" name="liabilities_form" method='post'>
                <div class="row">


                    <div class="col-md-4">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" <?php echo $make_dept_disable; ?> id="deptcode"
                            name="deptcode" onchange="getCategoriesBasedOnDept('','')">



                            {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து துறைகளும்"
                            @if (is_array(old('dept', $deptcode)) && in_array('A', old('dept', $deptcode))) selected @endif>
                            All
                        </option> --}}

                             <option value="" data-name-en="Select Department" data-name-ta="" >Select Department</option>


                            @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                    @if (
                                    (is_array(old('dept', $deptcode)) && in_array($department->deptcode, old('dept', $deptcode)))
                                    || (old('dept', $deptcode) == $department->deptcode)
                                )
                                    selected
                                @endif
                                        data-name-en="{{ $department->deptelname }}"
                                        data-name-ta="{{ $department->depttlname }}">
                                        {{ $department->deptelname }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                    Available
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label lang required" key="category" for="validationDefault01">Category</label>

                        <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                            <?php echo $make_catcode_disable; ?> onchange="onchange_category(''); onchange_subcategory('','')">

                            @php
                                $flatCatcode = [];
                                if(!empty($catcode)){
                                    foreach($catcode as $key => $vals){
                                        foreach($vals as $v){
                                            $flatCatcode[] = $v;
                                        }
                                    }
                                }

                                if(in_array('A', $flatCatcode)){
                                    $flatCatcode = ['A'];
                                }
                            @endphp

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"
                                @if(in_array('A', $flatCatcode)) selected @endif>
                                All
                            </option>

                            @if(!empty($catcode))
                                @foreach($category as $cat)
                                    @if($cat->catcode != 'A')
                                    <option value="{{ $cat->catcode }}"
                                            data-name-en="{{ $cat->catename }}"
                                            data-name-ta="{{ $cat->cattname }}"
                                            data-subcategory="{{ $cat->if_subcategory }}"
                                            @if(in_array($cat->catcode, $flatCatcode)) selected @endif
                                    >
                                        {{ $cat->catename }}
                                    </option>
                                    @endif
                                @endforeach
                            @else
                            @endif
                        </select>
                    </div>




                    @if(!in_array($deptcode, ['01', '05']))
                    <div class="col-md-4 subcatdiv">
                        <label class="form-label lang required" key="if_subcategory" for="subcategory">SubCategory</label>

                        <select class="mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                            name="subcategory[]" <?php echo $make_subcatcode_disable; ?> onchange="onchange_subcategory('','','')">

                            @php
                                $flatSubcatcode = [];
                                if(!empty($subcatcode)){
                                    foreach($subcatcode as $vals){
                                        foreach($vals as $v){
                                            $flatSubcatcode[] = $v;
                                        }
                                    }
                                }

                                if(in_array('A', $flatSubcatcode)){
                                    $flatSubcatcode = ['A'];
                                }
                            @endphp

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"
                                @if(in_array('A', $flatSubcatcode)) selected @endif>
                                All
                            </option>

                            @if(!empty($subcatcode) && !in_array('A', $flatSubcatcode))
                                @foreach($subcategory as $sub)
                                    @if($sub->auditeeins_subcategoryid != 'A')
                                        <option value="{{ $sub->auditeeins_subcategoryid }}"
                                                data-catcode="{{ $sub->catcode }}"
                                                data-catname="{{ $sub->catename }}"
                                                data-subname-en="{{ $sub->subcatename }}"
                                                data-subname-ta="{{ $sub->subcattname }}"
                                                @if(in_array($sub->auditeeins_subcategoryid, $flatSubcatcode)) selected @endif>
                                            {{ $sub->subcatename }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif

                        </select>
                    </div>
                    @endif



                    <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_institution_disable; ?>    id="instmappingcode" name="instmappingcode[]">

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                @php
                                $flatInstcode = [];
                                if (!empty($instid)) {
                                    foreach ($instid as $key => $vals) {
                                        foreach ($vals as $v) {
                                            $flatInstcode[] = $v;
                                        }
                                    }
                                }

                                // If session has 'A', only keep 'A' selected
                                if (in_array('A', $flatInstcode)) {
                                    $flatInstcode = ['A'];
                                }
                            @endphp

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"
                                @if(in_array('A', $flatInstcode)) selected @endif>
                                All
                            </option>

                            @if(!empty($instid))
                                @foreach($institution as $ins)
                                    @if($ins->instid != 'A') {{-- Skip 'All' since already added --}}
                                    <option value="{{ $ins->instid }}"
                                            data-catcode="{{ $ins->instid }}"
                                            data-catname="{{ $ins->instename }}"
                                            data-subname-en="{{ $ins->instename }}"
                                            data-subname-ta="{{ $ins->insttname }}"
                                            @if(in_array($ins->instid, $flatInstcode)) selected @endif
                                    >
                                        {{ $ins->instename }}
                                    </option>
                                    @endif
                                @endforeach
                            @else
                            @endif

                                </select>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Submit</button>
                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

    <div class="col-12">


        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Liability Details</div>
            <div class="card-body"><br>
                <div class="datatables hide_this">
                    <div class="table-responsive" id="tableshow">
                        <table id="LiabilityDetails"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang" key="">Category</th>
                                    <th class="lang" key="">SubCategory</th>
                                    <th class="lang" key="">Institution</th>
                                    <th class="lang" key="">Gist of Objection</th>
                                    <th class="lang" key="">Liability Details</th>

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
        // $('#workallocationform')[0].reset();
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);


    });




    function onchange_subcategory(deptcode,catcode,subcatcode) {
        var catcode =  $('#category').val();
        var deptcode = $('#deptcode').val();
        var subcatcode = $('#subcategory').val();

        let lang = getLanguage();

        const institutionDropdown = $('#instmappingcode');
        institutionDropdown.empty();

        if (Array.isArray(subcatcode) ? subcatcode.includes('A') : subcatcode === 'A') {
            institutionDropdown.append(`
            <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                ${lang === 'ta' ? 'அனைத்து' : 'All'}
            </option>
        `);
        return;
    }

    institutionDropdown.append(`
        <option value="A" data-name-en="All" data-name-ta="அனைத்து"
            ${subcatcode === 'A' ? 'selected' : ''}>
            ${lang === 'ta' ? 'அனைத்து' : 'All'}
        </option>
    `);



        $.ajax({
            url: '/getinstituionbasedonsubcategory',
            method: 'POST',
            data : {
                deptcode: deptcode,
                category: catcode,
                subcatcode: subcatcode

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },


            success: function(response) {


                    if (response && response.length > 0) {

                        response.forEach(subcategory => {

                            institutionDropdown.append(`
                        <option value="${subcategory.instid}"
                            data-name-en="${subcategory.instename}"
                            data-name-ta="${subcategory.insttname}"}>
                            ${lang === 'ta' ? subcategory.insttname : subcategory.instename}
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



            },
            error: function(xhr, status, error) {
               // alert('enter')


            }
        });

}










    function onchange_category(catcode, selectedsubCatcode = null,subcategory) {
        var catcode = catcode || $('#category').val();

        var selectedOption = $('#category').find(':selected');
        var subcategory = subcategory || selectedOption.attr('subcategory');
     // var subcategory = selectedOption.attr('subcategory'); // Ensure correct retrieval
        let lang = getLanguage();


        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty();


        if (Array.isArray(catcode) ? catcode.includes('A') : catcode === 'A') {
        subcategoryDropdown.append(`
            <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                ${lang === 'ta' ? 'அனைத்து' : 'All'}
            </option>
        `);
        return;
    }



        if (!catcode) {

            subcategoryDropdown.append(`
                <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                </option>
            `);

        }

        $.ajax({
            url: '/getsubcategoriesbasedondeptforliabilities', // Your API route to get user details
            method: 'POST',
            data : {
                category: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },


            success: function(response) {


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
                                <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                                    ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                                </option>
                            `);

                    }



            },
            error: function(xhr, status, error) {
               // alert('enter')


            }
        });

}

function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');
        const institutionDropdown = $('#instmappingcode');

        const lang = getLanguage();
        subcategoryDropdown.empty();
        institutionDropdown.empty();

        $('#category').empty();






        catcodeDropdown.append(`
        <option value="A" data-name-en="All" data-name-ta="அனைத்து"
            ${deptcode === 'A' ? 'selected' : ''}>
            ${lang === 'ta' ? 'அனைத்து' : 'All'}
        </option>
    `);

        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {








        }

        if (deptcode) {
            $.ajax({
                url: "/getcategoriesbasednndeptforliabilities",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    data = response;
                    if (response.length > 0) {
                        response.forEach(category => {

                            catcodeDropdown.append(`
                        <option value="${category.catcode}"
                            data-name-en="${category.catename}"
                            subcategory="${category.if_subcategory}"
                            data-name-ta="${category.cattname}">
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
                            <option disabled data-name-en="No Category Available" data-name-ta="வகை எதுவும் இல்லை">
                                ${lang === 'ta' ? 'வகை எதுவும் இல்லை' : 'No Category Available'}
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






    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'workallocationform');
    });



//     $('#liabilities_form').on('submit', function(e) {
//     e.preventDefault();

//     getInstData('en');
// });




jsonLoadedPromise.then(() => {

const language = window.localStorage.getItem('lang') || 'en';

$.validator.addMethod("selectRequired", function (value, element) {
    let selected = $(element).val();
    if (Array.isArray(selected)) {
        return selected.length > 0 && !selected.includes('');
    }
    return selected !== null && selected !== '';
}, "Please select at least one option.");

$.validator.addMethod("subcategoryRequired", function (value, element) {
    if ($("#if_subcategory").val() === 'Y') {
        let selected = $(element).val();
        if (Array.isArray(selected)) {
            return selected.length > 0 && !selected.includes('');
        }
        return selected !== null && selected !== '';
    }
    return true;
}, "Enter Subcategory");

var validator = $("#liabilities_form").validate({
    ignore: [],
    rules: {
        deptcode: { selectRequired: true },
        "category[]": { selectRequired: true },
        "instmappingcode[]": { selectRequired: true },
        "subcategory[]": { subcategoryRequired: true }
    },
    messages: {
        deptcode: "Select a Department",
        "category[]": "Select a category",
        "instmappingcode[]": "Select an institution",
        "subcategory[]": "Enter Subcategory"
    },
    errorPlacement: function (error, element) {
        if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('.select2'));
        } else {
            error.insertAfter(element);
        }
    }
});

$('#deptcode, #category, #instmappingcode, #subcategory').on('change.select2', function () {
    $(this).valid();
});

$('#buttonaction').on('click', function (event) {
    event.preventDefault();

    if ($("#liabilities_form").valid()) {
        var lang = 'en';
        getInstData('en');
    } else {
        validator.focusInvalid();
    }
});

}).catch(error => {
console.error("Failed to load JSON data:", error);
});




    function getInstData(lang) {


    var deptcode =  $('#deptcode').val();
    var category =$('#category').val();
    var subcategory = $('#subcategory').val();
    var instmappingcode = $("#instmappingcode").val();


    if (!Array.isArray(instmappingcode) || instmappingcode.length === 0) {
        instmappingcode = [0];
    }

    var deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0
    ? $('#deptcode').val()
    : ['A'];

    var category = $('#category').val() && $('#category').val().length > 0
        ? $('#category').val()
        : ['A'];

    var subcategory = $('#subcategory').val() && $('#subcategory').val().length > 0
        ? $('#subcategory').val()
        : [];

    var instmappingcode = $('#instmappingcode').val() && $('#instmappingcode').val().length > 0
    ? $('#instmappingcode').val()
    : ['A'];



$.ajax({
    url: '/callcodecheck',
    type: 'POST',
    data: {
        deptcode: deptcode,
        subcategory: subcategory,
        category: category,
        instmappingcode: instmappingcode,
    },
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {

        if (response.data && response.data.length > 0) {
            // alert('adds');

            $('#tableshow').show();
            $('#usertable_wrapper').show();
            $('#no_data').hide();
            dataFromServer = response.data;



            renderTable(lang);
        } else {

            $('#tableshow').hide();
            $('#usertable_wrapper').hide();
            $('#no_data').show();
        }
    },
    error: function(xhr, status, error) {

        var response = JSON.parse(xhr.responseText);

        if (xhr.status === 404) {
            $('#tableshow').hide();
            $('#usertable_wrapper').hide();
            $('#no_data').show();
        }

        var errorMessage = response.message ||
            'An unknown error occurred';

        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
            'alert_header', 'alert_body', 'confirmation_alert');


        // Optionally, log the error to console for debugging
        console.error('Error details:', xhr, status, error);
    }
});
}





    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('callcodecheck') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                            console.log("Success Response:", json);
                            if (json && json.length > 0) {
                                dataFromServer = json;
                                renderTable(language);
                                $('#tableshow').show();
                                $('#usertable_wrapper').show();
                                $('#no_data').hide();
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

        if ($.fn.DataTable.isDataTable('#LiabilityDetails')) {
            $('#LiabilityDetails').DataTable().clear().destroy();
        }

        table = $('#LiabilityDetails').DataTable({
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
                    data: "deptesname",
                    title: columnLabels?.["deptesname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.deptesname || '-';
                    }
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
                    data: "subcatename",
                    title: columnLabels?.["subcatename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subcatename || '-';
                    }
                },
                 {
                    data: "instename",
                    title: columnLabels?.["instename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.instename || '-';
                    }
                },
                {
                    data: "slipdetails",
                    title: columnLabels?.["slipdetails"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.slipdetails || '-';
                    }
                },
                {
                    data: "liabilities",
                    title: columnLabels?.["liabilities"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.liabilities || '-';
                    }
                }
            ],

            "initComplete": function(settings, json) {
                $("#LiabilityDetails").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        // const mobileColumns = ["majorworkallocationtypeename", "majorworkallocationtypetname", "statusflag"];
        // setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "LiabilityDetails"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#LiabilityDetails')) {
            $('#LiabilityDetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }






    $(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        var deptcode = '<?php echo $deptcode; ?>'
        var hodrole = '<?php echo $hod_roletypecode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {

            getCategoriesBasedOnDept(deptcode)

        } else if (sessionrole == regionrole) {

            getCategoriesBasedOnDept(deptcode)

        } else if (sessionrole == headofficerole) {

            getCategoriesBasedOnDept(deptcode)
        }
        else if (sessionrole == hodrole) {

            //onchange_subcategory(deptcode)
        }

        else if (sessionrole == dgarole) {
            getCategoriesBasedOnDept(null);
        } else if (sessionrole == adminrole) {

           // getInstData(lang);
        }


    });


    function reset_form() {
        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'




        $('#liabilities_form')[0].reset();

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        var form = $('#liabilities_form');

            form[0].reset();


        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }
        if (sessionrole == distrole) {
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#instmappingcode').val('').select2();

        } else if (sessionrole == regionrole) {

            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#instmappingcode').val('').select2();

        } else if (sessionrole == headofficerole) {

            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#instmappingcode').val('').select2();


        }

        else if (sessionrole == dgarole) {

        $('#deptcode').val('').select2();

        $('#category').val('').select2();
        $('#subcategory').val('').select2();
        $('#instmappingcode').val('').select2();



        }


         else if (sessionrole == adminrole) {
	 $('#deptcode').val('').select2();

        $('#category').val('').select2();
        $('#subcategory').val('').select2();
        $('#instmappingcode').val('').select2();

           

          }

    }



</script>


@endsection