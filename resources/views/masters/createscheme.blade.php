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
            <div class="card-header card_header_color lang" key="scheme_head">Scheme</div>
            <div class="card-body">
                <form id="schemeform" name="schemeform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                    <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
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

            



                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                onchange="onchange_category('','','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                 <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-4 mb-2 subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory" for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                name="subcategory"
                                >
                               <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>

                                <option value="" disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option>


                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="auditeeschemeesname"
                                for="auditeeschemeesname">Scheme Short Name in English</label>
                            <input type="text" class="form-control name" id="auditeeschemeesname" maxlength='50' name="auditeeschemeesname"
                               required data-placeholder-key="auditeeschemeesname">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="auditeeschemeelname"
                                for="auditeeschemeelname">Scheme Long Name in English</label>
                            <input type="text" class="form-control name" id="auditeeschemeelname" maxlength='400' name="auditeeschemeelname"
                                required
                                data-placeholder-key="auditeeschemeelname">
                        </div>


                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="auditeeschemetsname"
                                for="auditeeschemetsname">Scheme Short Name in Tamil</label>
                            <input type="text" maxlength='50' class="form-control name" id="auditeeschemetsname" name="auditeeschemetsname"
                               required
                                data-placeholder-key="auditeeschemetsname">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="auditeeschemetlname"
                                for="auditeeschemetlname">Scheme Long Name in Tami</label>
                            <input type="text" maxlength='400' class="form-control name" id="auditeeschemetlname" name="auditeeschemetlname"
                                 required
                                data-placeholder-key="auditeeschemetlname">
                        </div>

                       
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

                            <input type="hidden" name="auditeeschemeid" id="auditeeschemeid" value="" />

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
            <div class="card-header card_header_color lang" key="scheme_table">Scheme Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="schemetable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <th class="lang align-middle text-center" key="auditeeschemeesname">Scheme English Short Name</th>
                                    <th class="lang align-middle text-center" key="auditeeschemeelname">Scheme English Long Name</th>
                                    <!-- <th class="lang align-middle text-center" key="if_subcategory">Sub Category</th> -->
                                    <th class="lang align-middle text-center" key="auditeeschemetsname">Scheme Tamil Short Name</th>
                                    <th class="lang align-middle text-center" key="auditeeschemetlname">Scheme Tamil Long Name</th>
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




let data = "";


function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');

        const lang = getLanguage();

        $('#category').empty();



        catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---??????? ?????????????????---">
                ${lang === 'ta' ? '---??????? ?????????????????---' : '---Select Category---'}
            </option>
            `);

        subcategoryDropdown.html(`
        <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
            ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
        </option>
    `);

      
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
                url: "/getcategoriesbasednndeptforscheme",
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


    

function onchange_category(catcode, selectedsubCatcode = null,subcategory) {
        var catcode = catcode || $('#category').val();
        var selectedOption = $('#category').find(':selected'); 
        var subcategory = subcategory || selectedOption.attr('subcategory'); 
     // var subcategory = selectedOption.attr('subcategory'); // Ensure correct retrieval
     let lang = getLanguage();



        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty();

        subcategoryDropdown.append(`
            <option value="" data-name-en="---Select SubCategory---" data-name-ta="---???? ??????? ?????????????????---">
                ${lang === 'ta' ? '---???? ??????? ?????????????????---' : '---Select SubCategory---'}
            </option>
        `);


        if (!catcode) {

            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);

        }

        $.ajax({
            url: '/getsubcategoriesbasedondeptforscheme', // Your API route to get user details
            method: 'POST',
            data : {
                category: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
           

            success: function(response) {
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
        $('#schemeform')[0].reset();

        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            getCategoriesBasedOnDept(sessiondeptcode, '', '', '', '', '');
        }





    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'schemeform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('scheme.scheme_fetchData') }}",
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

       
        if ($.fn.DataTable.isDataTable('#schemetable')) {
            $('#schemetable').DataTable().clear().destroy();
        }

        table = $('#schemetable').DataTable({
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
                    data: CategoryColumn,
                    render: function (data, type, row) {

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
                    className: "d-none d-md-table-cell lang extra-column"
                },

            
                {
                    data: "auditeeschemeesname",
                    title: columnLabels?.["auditeeschemeesname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeeschemeesname || '-';
                    }
                },
                {
                    data: "auditeeschemeelname",
                    title: columnLabels?.["auditeeschemeelname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeeschemeelname || '-';
                    }
                },
                {
                    data: "auditeeschemetsname",
                    title: columnLabels?.["auditeeschemetsname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeeschemetsname || '-';
                    }
                },
                {
                    data: "auditeeschemetlname",
                    title: columnLabels?.["auditeeschemetlname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeeschemetlname || '-';
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
                    className: "text-center d-none d-md-table-cell extra-column noExport"
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
                                <a class="btn editicon editsupercheckdel"
                                id="${row.encrypted_auditeeschemeid}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },

                    className: "text-center noExport text-wrap"
                }				

            ],

            "initComplete": function(settings, json) {
                $("#schemetable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [CategoryColumn,subcategoryColumn,"auditeeschemeesname","auditeeschemeelname","auditeeschemetsname","auditeeschemetlname","statusflag"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "schemetable"); 
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#schemetable')) {
            $('#schemetable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


        
    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ? Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);
    // ? Fetch column headers from JSON layout
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


    // ? Column Mapping (for language-specific keys)
    const columnMap = {
        department: language === 'ta' ? 'depttsname' : 'deptesname',
        category: language === 'ta' ? 'cattamname' : 'catengname',
        subcategory: language === 'ta' ? 'subcategory_tname' : 'subcategory_ename'
     
    };

    // ? Define Headers Properly
    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["category"] || "Category", key: "category" },
        { header: dtText["subcategory"] || "Subcategory", key: "subcategory" },
        { header: dtText["auditeeschemeesname"] || "Scheme Short Name in English", key: "auditeeschemeesname" },
        { header: dtText["auditeeschemeelname"] || "Scheme Long Name in English", key: "auditeeschemeelname" },
        { header: dtText["auditeeschemetsname"] || "Scheme Short Name in Tamil", key: "auditeeschemetsname" },
        { header: dtText["auditeeschemetlname"] || "Scheme Short Name in Tamil", key: "auditeeschemetlname" }

    ];

    // ? Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");
        let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        return {
            department: rowData[columnMap.department] || "-",
            category: rowData[columnMap.category] || "-",
            subcategory: rowData[columnMap.subcategory] || "-",
            auditeeschemeesname: rowData.auditeeschemeesname || "-",
            auditeeschemeelname: rowData.auditeeschemeelname || "-",
            auditeeschemetsname: rowData.auditeeschemetsname || "-",
            auditeeschemetlname: rowData.auditeeschemetlname || "-"
           
           
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
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    
    // ? Ensure Headers Align with Data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}









    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#schemeform").validate({

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
                auditeeschemeesname: {
                    required: true
                },
                auditeeschemeelname: {
                    required: true
                },
                auditeeschemetsname: {
                    required: true
                },
                auditeeschemetlname: {
                    required: true
                },
                statusflag : {
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

            if ($("#schemeform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#schemeform').serializeArray();

                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name : 'deptcode',
                        value: deptcode
                    });
                }
                // formData.push({
                //     name: "lang",
                //     value: getLanguage('N')
                // });
               // console.log(formData);
                $.ajax({
                    url: "{{ route('scheme.scheme_insertupdate') }}",
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


    function schemeform(scheme) {
        $('#display_error').hide();
        getCategoriesBasedOnDept(scheme.deptcode,scheme.catcode);
        
        setTimeout(() => {
                        onchange_category(scheme.catcode,scheme.auditeeins_subcategoryid,scheme.subcategory);
        }, 400); 

        $('#auditeeschemeesname').val(scheme.auditeeschemeesname);
        $('#auditeeschemeelname').val(scheme.auditeeschemeelname);
        $('#auditeeschemetsname').val(scheme.auditeeschemetsname);
        $('#auditeeschemetlname').val(scheme.auditeeschemetlname);
        $('#auditeeschemeid').val(scheme.encrypted_auditeeschemeid);
        populateStatusFlag(scheme.statusflag);
         $('#deptcode').val(scheme.deptcode).select2();
       
     

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editsupercheckdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#auditeeschemeid').val(id);
            // alert(id);
            $.ajax({
                url: "{{ route('scheme.scheme_fetchData') }}",
                method: 'POST',
                data: {
                    auditeeschemeid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('schemeform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            schemeform(response.data[0]); // Populate form with data
                        } else {
                            alert('Scheme data is empty');
                        }
                    } else {
                        alert('Scheme not found');
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

         $('#schemeform')[0].reset();
       

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#category').val(null).select2();
            $('#subcategory').val(null).select2();
        } else {

            $('#deptcode').val(null).select2();
            getCategoriesBasedOnDept(null); 


        }

     

        changeButtonAction('schemeform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
