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
 <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title text-center" id="historyModalLabel">Slip Template Format</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <table class="table w-100 table-striped table-bordered datatables-basic" id="templateparaTable" style="table-layout: fixed; word-wrap: break-word;">
                    <thead>
                        <tr>
                            <th class="text-center">Slip Template Format</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card_border" style="display: none;">
            <div class="card-header card_header_color lang" key="">Slip Template Details</div>
             <div class="card-body">
                <form id="templateslipform" name="templateslipform" >
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">

                    <input type="hidden" name="if_subcategory" id="if_subcategory" value="">


                   
                         <div class="col-md-4 mb-4">
                            <label class="form-label lang required " key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','')">
                               <option value="" data-name-en="---Select Department---"
                                    data-name-ta="---துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>

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
                                onchange="onchange_category('','','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                 <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-4  subcatdiv ">
                            <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 subcategory"  multiple="multiple" id="subcategory"
                                name="subcategory[]">
                               <option value="" disabled data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>

                            </select>
                        </div>
                        

                        <div class="col-md-6 mb-3">
                            <label class="form-label required lang" for="templateparaename">
                                Template Para Name in English
                            </label>
                            <textarea class="form-control" id="templateparaename"  placeholder="Enter remarks"  name="templateparaename" required></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required lang" for="templateparatname">
                                Template Para Name in Tamil
                            </label>
                            <textarea class="form-control" id="templateparatname"  placeholder="Enter remarks" name="templateparatname" required></textarea>
                        </div>




                    </div>
            
                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="tempslipid" id="tempslipid" value="" />
                            <button class="btn button_save mt-3 lang mb-3" key="savebtn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang mb-3" key="clearbtn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key=""> Slip Template Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="templatesliptable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <th class="lang align-middle text-center" key="if_subcategory">Sub Category</th>
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
<style>




.is-invalid {
    border: 1px solid red !important; 
    box-shadow: none !important; 
}

.error {
    color: red;
    font-size: 12px;
    margin-top: 5px;
}

.scheme-row {
    display: flex;
    align-items: flex-end; 
    margin-bottom: 10px;  
}

.scheme-row .col-md-2 {
    margin-bottom: 0; 
}


#templatesliptable td.text-wrap {
    max-width: 200px; /* Adjust as needed */
}
.wrap-50 {
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 300px; /* Adjust as needed */
}


        .ck-editor__editable {
                    max-height: 300px;
                    overflow-y: auto;
                }


        .ck-powered-by-balloon {
            display: none !important;
        }

        #container {
            width: 1000px;
            margin: 20px auto;
        }

        .ck-editor__editable[role="textbox"] {
            min-height: 200px;
        }

        .ck-editor__editable {
            font-family: 'Marutham', sans-serif;
        }



    
</style>

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


    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<!-- Download Button End -->

<script>



let editor;


    const ckOptions = {
        toolbar: {
            items: [
                'findAndReplace', 'selectAll', '|',
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'numberedList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                'fontSize', 'fontFamily', '|',
                'alignment', '|',
                'uploadImage', 'insertTable', '|'
            ],
            shouldNotGroupWhenFull: true
        },
        placeholder: 'Enter Remarks',
        fontFamily: {
            options: [
                'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace', 'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 22],
            supportAllValues: true
        },
        htmlSupport: {
            allow: [{ name: /.*/, attributes: true, classes: true, styles: true }]
        },
        link: {
            decorators: {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                toggleDownloadable: {
                    mode: 'manual',
                    label: 'Downloadable',
                    attributes: { download: 'file' }
                }
            }
        },
        removePlugins: [
            'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
            'Base64UploadAdapter', 'MultiLevelList',
            'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments',
            'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
            'WProofreader', 'MathType', 'SlashCommand', 'Template',
            'DocumentOutline', 'FormatPainter', 'TableOfContents',
            'PasteFromOfficeEnhanced', 'CaseChange'
        ]
    };

    const editors = {};

["templateparaename", "templateparatname"].forEach(id => {
    CKEDITOR.ClassicEditor.create(document.getElementById(id), ckOptions)
        .then(editor => {
            editors[id] = editor; // save editor instance
        })
        .catch(error => console.error(error));
});




let data = "";


function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');

        const lang = getLanguage();

        $('#category').empty();
        subcategoryDropdown.empty();



        catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
            </option>
            `);

        subcategoryDropdown.html(`
        <option value="" disabled data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
        </option>
    `);

      
        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {


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

        if (deptcode) {
            $.ajax({
                url: "/getcategoriesbasednndeptfortemplateslip",
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
                       $('#if_subcategory').val(category.if_subcategory);


                        });


                    } else {
                                catcodeDropdown.append(`
                                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
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


    function clearSubcategoryErrors() {
    $('.subcategory').each(function() {
        $(this).removeClass('error');
        var errorLabel = $("label[for='" + $(this).attr('id') + "'].error");
        if (errorLabel.length) {
            errorLabel.remove();
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

        subcategoryDropdown.append(`
            <option value="" disabled data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
        `);


        if (!catcode) {

            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
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
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
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

                clearSubcategoryErrors();

             
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
        $('#templateslipform')[0].reset();

       // updateSelectColorByValue(document.querySelectorAll(".form-select"));

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
        updateValidationMessages(getLanguage('Y'), 'templateslipform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('templateslip.templateslip_fetchData') }}",
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

       
        if ($.fn.DataTable.isDataTable('#templatesliptable')) {
            $('#templatesliptable').DataTable().clear().destroy();
        }

        table = $('#templatesliptable').DataTable({
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
                    className: 'text-start text-wrap' // Removed col-1
                },
                {
                    data: CategoryColumn,
                    title: columnLabels?.[CategoryColumn]?.[language],
                    render: function(data, type, row) {
                        return row[CategoryColumn] || '-';
                    },
                    className: 'text-start text-wrap' // Removed col-1
                },

                {
                    data: subcategoryColumn,
                    title: columnLabels?.[subcategoryColumn]?.[language],
                    render: function(data, type, row) {
                        return row[subcategoryColumn] || '-';
                    },
                    className: 'text-start text-wrap' // Removed col-1
                },

                {
                    data: "encrypted_tempslipid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) => `
                        <center>
                            <a class="btn btn-info editsupercheckdel" id="${data}">
                                View
                            </a>
                           <!--
                            <a class="ms-2 editicon editdel" id="edit_${data}" title="Edit">
                                <i class="ti ti-edit fs-4"></i>
                            </a>-->

                        </center>
                    `,
                    className: "text-center noExport"
                }

              


              
                // {
                //     data: "statusflag",
                //     title: columnLabels?.["statusflag"]?.[language],
                //     render: function(data) {
                //         let activeText = arrLang?.[language]?.["active"] || "Active";
                //         let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                //         return data === 'Y' ?
                //             `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                //             `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                //     },
                //     className: "text-center d-none d-md-table-cell extra-column noExport"
                // },

                // {
                //     data: "encrypted_tempslipid",
                //     title: columnLabels?.["actions"]?.[language],
                //     render: (data) =>
                //         `<center><a class="btn editicon editsupercheckdel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                //     className: "text-center noExport"
                // }
            ],

            "initComplete": function(settings, json) {
                $("#templatesliptable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [CategoryColumn,subcategoryColumn,"auditeeschemeesname","auditeeschemeelname","auditeeschemetsname","auditeeschemetlname","statusflag"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "templatesliptable"); 
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#templatesliptable')) {
            $('#templatesliptable').DataTable().clear().destroy();
        }
        renderTable(language);
    }





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




    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#templateslipform").validate({

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
            //     "auditeeschemetsname[]": {
            //         required: true
            //     },
            //     "auditeeschemetlname[]": {
            //         required: true
            //     },
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

            if ($("#templateslipform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let hasError = false;

                if (!validateSubcategoryFields(validator)) {
                hasError = true;
                return false; 

                }

                const englishData = editors['templateparaename']?.getData().trim() || '';
                const tamilData = editors['templateparatname']?.getData().trim() || '';

                if (!englishData || !tamilData) {
                    passing_alert_value('Confirmation','Enter the remarks',
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    return false; 
                }

                var formData = new FormData($('#templateslipform')[0]);

                formData.set('templateparaename', englishData);
                formData.set('templateparatname', tamilData);

       

            $.ajax({
                url: "{{ route('templateslip.templateslip_insertupdate') }}",
                type: 'POST',
                data: formData,
                processData: false,   // Important: prevents jQuery from messing with FormData
                contentType: false,   // Important: keeps correct headers
                success: function(response) {
                    if (response.success) {
                        reset_form(); 
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
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
                        passing_alert_value('Confirmation', response.message || "Error Occured",
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                    }
                }
        });
    }
});

        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    function templateslipformddata(slip) {
        $('#display_error').hide();
        // $('#addActionContainer').addClass('hide_this').hide();

        // // $('#editschemeshow').removeClass('hide_this');   
        // getCategoriesBasedOnDept(slip.deptcode,slip.catcode);
        
        // setTimeout(() => {
        //                 onchange_category(slip.catcode,slip.auditeeins_subcategoryid,slip.subcategory);
        // }, 400); 

        $('#auditeeschemeesname').val(slip.templateparaename?.trim() || '');
        $('#auditeeschemeelname').val(slip.templateparatname?.trim() || '');
      
        $('#tempslipid').val(slip.encrypted_tempslipid);
        // populateStatusFlag(scheme.statusflag);
        //  $('#deptcode').val(scheme.deptcode).select2();
       
     

        //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editsupercheckdel, .editdel', function() {
    const id = $(this).attr('id');
    const isEdit = $(this).hasClass('editdel'); // check if edit was clicked

    if (id) {
        reset_form();
        // For edit, remove "edit_" prefix if needed
        const tempslipId = isEdit ? id.replace('edit_', '') : id;
        $('#tempslipid').val(tempslipId);

        $.ajax({
            url: "{{ route('templateslip.templateslip_fetchData') }}",
            method: 'POST',
            data: { tempslipid: tempslipId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    const slip = response.data[0];

                    let englishContent = '';
                    let tamilContent = '';

                    try {
                        if (typeof slip.templateparaename === 'string') {
                            if (slip.templateparaename.trim().startsWith('{')) {
                                const para = JSON.parse(slip.templateparaename);
                                englishContent = para.content || '';
                            } else {
                                englishContent = slip.templateparaename;
                            }
                        }

                        if (typeof slip.templateparatname === 'string') {
                            if (slip.templateparatname.trim().startsWith('{')) {
                                const para = JSON.parse(slip.templateparatname);
                                tamilContent = para.content || '';
                            } else {
                                tamilContent = slip.templateparatname;
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing JSON content:', e);
                    }

                    if (!isEdit) {
                        // VIEW MODE: populate only the table and modal fields
                        const tbody = $('#templateparaTable tbody');
                        tbody.empty();
                        tbody.append(`
                            <tr>
                                <td>${tamilContent}</td>
                            </tr>
                        `);
                        $('#historyModal').modal('show');

                        $('#auditeeschemeelname').val(tamilContent);
                        
                    } else {
                        changeButtonAction('templateslipform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            $('#deptcode').val(slip.deptcode).select2();


                            getCategoriesBasedOnDept(slip.deptcode,slip.catcode);
                            
                            setTimeout(() => {
                                            onchange_category(slip.catcode,slip.auditeeins_subcategoryid,slip.subcategory);
                            }, 400); 

                        if (editors['templateparaename']) {
                            editors['templateparaename'].setData(englishContent);
                        }
                        if (editors['templateparatname']) {
                            editors['templateparatname'].setData(tamilContent);
                        }

                        // populate other fields if needed
                        $('#tempslipid').val(slip.encrypted_tempslipid);
                       
                    }
                } else {
                    alert('Templateslip data is empty');
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
    $('#templateslipform')[0].reset();
    $('#addActionContainer').removeClass('hide_this').show();
    $('#editschemeshow').addClass('hide_this');

    if (sessiondeptcode && sessiondeptcode.trim() !== '') {
        getCategoriesBasedOnDept(null); 
        $('#category').val(null).select2();
        $('#subcategory').val([]).select2();    
    } else {
        $('#deptcode').val(null).select2();
        getCategoriesBasedOnDept(null); 

    }

        // Clear CKEditor fields
        if (editors['templateparaename']) {
        editors['templateparaename'].setData('');
    }
    if (editors['templateparatname']) {
        editors['templateparatname'].setData('');
    }

    changeButtonAction('templateslipform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert));

    // Keep only the first .scheme-row and reset its inputs
   
}


</script>


@endsection
