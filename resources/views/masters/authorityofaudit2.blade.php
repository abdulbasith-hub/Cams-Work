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


    #authorityofaudittable td.text-wrap {
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

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title text-center" id="historyModalLabel">Authority Of Audit</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <table class="table w-100 table-striped table-bordered datatables-basic" id="templateparaTable" style="table-layout: fixed; word-wrap: break-word;">
                    <thead>
                        <tr>
                            <th class="text-center">Authority Of Audit English Name</th>
                            <th class="text-center">Authority Of Audit Tamil Name</th>

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
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Authority Of Audit</div>
             <div class="card-body">
                <form id="authorityofauditform" name="authorityofauditform" >
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">

                    <input type="hidden" name="if_subcategory" id="if_subcategory" value="">


                         <div class="col-md-3 mb-4">
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


                        <div class="col-md-3 mb-4">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                 onchange="onchange_category(); onchange_subcategory();">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                 <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-3 " id="subcatdiv">
                            <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 subcategory"  id="subcategory"
                                name="subcategory" onchange="onchange_subcategory('','','','')">
                               <option value=""  data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="institution"
                                for="institution">Auditable Institution</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown"   id="instmappingcode" name="instmappingcode">

                            <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option>


                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="statusflag" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input " type="radio" name="statusflag" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>

                    <div class='row'>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required lang" for="">
                               Authority of Audit Name in English
                            </label>
                            <textarea class="form-control" id="templateparaename"  placeholder="Enter remarks"  name="templateparaename" required></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required lang" for="">
                                Authority of Audit Name in Tamil
                            </label>
                            <textarea class="form-control" id="templateparatname"  placeholder="Enter remarks" name="templateparatname" required></textarea>
                        </div>


                    </div>






                    </div>

                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="authority_id" id="authority_id" value="" />
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
            <div class="card-header card_header_color lang" key="">Authority of Audit Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="authorityofaudittable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="category">Category</th>
                                    <th class="lang align-middle text-center" key="if_subcategory">Sub Category</th>
                                    <th class="lang align-middle text-center" key="">Institution</th>
                                    <th class="all lang align-middle text-center" key="">Authority of Audit Names</th>
                                    <th class="lang align-middle text-center" key="">Status</th>
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
            editors[id] = editor;

        editor.ui.view.toolbar.items
        .find(item => item.label == "Find and replace")
        .isEnabled = false;

            const toDisable = [
        'findAndReplace',
        'selectAll',
        'paragraph',
        'heading',
        'bold',
        'italic',
        'underline',
        'numberedList',
        'outdent',
        'indent',
        'undo',
        'redo',
        'fontSize',
        'fontFamily',
        'alignment',
        'uploadImage',
        'insertTable'
    ];

    toDisable.forEach(cmd => {
        const command = editor.commands.get(cmd);
        if (command) {
            command.forceDisabled('lockAll');
        }
    });
        })
        .catch(error => console.error(error));
});




let data = "";



function onchange_subcategory(deptcode, catcode, subcategory, selectedinstitution = null) {
    var deptcode = deptcode || $('#deptcode').val();
    var catcode = catcode || $('#category').val();
    var subcategory = subcategory || $('#subcategory').val();
    let lang = getLanguage();

    const instDropdown = $('#instmappingcode');
    instDropdown.empty();

  //  alert(selectedinstitution);

    // Default option
    instDropdown.append(`
        <option value="" data-name-en="---Select an Institution---" data-name-ta="---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select an Institution---'}
        </option>
    `);

    if (deptcode === '01' || deptcode === '05') {

        if (catcode === 'A') {
            instDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="அனைத்தும்" ${selectedinstitution === 'A' ? 'selected' : ''}>
                    ${lang === 'ta' ? 'அனைத்தும்' : 'All'}
                </option>
            `);
            return;
        }

    }



if (selectedinstitution === 'A') {
        instDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="அனைத்தும்" ${selectedinstitution === 'A' ? 'selected' : ''}>
                    ${lang === 'ta' ? 'அனைத்தும்' : 'All'}
                </option>
            `);
    } else if(subcategory === 'A'){
        instDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="அனைத்தும்"  ${selectedinstitution === 'A' ? 'selected' : ''}>
                    ${lang === 'ta' ? 'அனைத்தும்' : 'All'}
                </option>
            `);

    }else{
        instDropdown.append(`
                <option value="A" data-name-en="All" data-name-ta="அனைத்தும்" ${selectedinstitution === 'A'}>
                    ${lang === 'ta' ? 'அனைத்தும்' : 'All'}
                </option>
            `);

    }






    $.ajax({
        url: '/getinstituionbasedonsubcategorycommon',
        method: 'POST',
        data: {
            deptcode: deptcode,
            category: catcode,
            subcatcode: subcategory
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

            if (response && response.length > 0) {
               
                response.forEach(ins => {
                    instDropdown.append(`
                        <option value="${ins.instid}"
                            data-name-en="${ins.instename}"
                            data-name-ta="${ins.insttname}"
                            ${String(ins.instid) === String(selectedinstitution) ? 'selected' : ''}>
                            ${lang === 'ta' ? ins.insttname : ins.instename}
                        </option>
                    `);
                });
            } else {

                

            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching subcategories:', error);
        }
    });
}




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
        <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
        </option>
    `);

   // alert(selectedCatcode);


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
                url: "/getcategoriesbasednndeptforcommon",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    data = response;

                    catcodeDropdown.append(`
                        <option value="A" ${selectedCatcode === 'A' ? 'selected' : ''}>
                            ${lang === 'ta' ? 'All' : 'All'}
                        </option>
                    `);

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

                       if (category.if_subcategory === 'Y') {
                            $('#subcatdiv').show();
                        } else {
                            $('#subcatdiv').hide();
                        }


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
     let lang = getLanguage();

    // alert(selectedsubCatcode);



        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty();

        subcategoryDropdown.append(`
            <option value=""  data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
        `);

        if (selectedsubCatcode === 'A') {
          subcategoryDropdown.append(`
            <option value="A" data-name-en="All" selected data-name-ta="All">
                ${lang === 'ta' ? 'All' : 'All'}
                </option>
            `);
            return;
        }

        if (catcode === 'A') {
           subcategoryDropdown.append(`
            <option value="A" data-name-en="All" data-name-ta="All">
                ${lang === 'ta' ? 'All' : 'All'}
                </option>
            `);
        }





        if (!catcode) {

            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `);

        }

        $.ajax({
            url: '/getsubcategoriesbasedncatforcommon', // Your API route to get user details
            method: 'POST',
            data : {
                category: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },


            success: function(response) {

                    subcategoryDropdown.append(`
                    <option value="A"  data-name-en="All" data-name-ta="All">
                        ${lang === 'ta' ? 'All' : 'All'}
                    </option>
                 `);

                    if (response && response.length > 0) {

                        response.forEach(subcategory => {
                            subcategoryDropdown.append(`
                        <option value="${subcategory.auditeeins_subcategoryid}"
                            data-name-en="${subcategory.subcatename}"
                            data-name-ta="${subcategory.subcattname}"
                            ${String(subcategory.auditeeins_subcategoryid) === String(selectedsubCatcode) ? 'selected' : ''}
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
        $('#authorityofauditform')[0].reset();

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
        updateValidationMessages(getLanguage('Y'), 'authorityofauditform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "/authorityofaudit_fetchData",
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
        const InstColumn = language === 'ta' ? 'insttname' : 'instename';


        if ($.fn.DataTable.isDataTable('#authorityofaudittable')) {
            $('#authorityofaudittable').DataTable().clear().destroy();
        }

        table = $('#authorityofaudittable').DataTable({
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
                    data: InstColumn,
                    title: columnLabels?.[InstColumn]?.[language],
                    render: function(data, type, row) {
                        return row[InstColumn] || '-';
                    },
                    className: 'text-start text-wrap' // Removed col-1
                },


                {
                    data: "encrypted_authority_id",
                    title: columnLabels?.["Authority Of Audit Names"]?.[language],
                    render: (data) => `
                        <center>
                            <a class="btn btn-info viewAuthority" id="${data}">
                                View
                            </a>

                        </center>
                    `,
                    className: "text-center noExport"
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
                        data: "encrypted_authority_id",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editAuthority" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                }





            ],

            "initComplete": function(settings, json) {
                $("#authorityofaudittable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [CategoryColumn,subcategoryColumn,"auditeeschemeesname","auditeeschemeelname","auditeeschemetsname","auditeeschemetlname","statusflag"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "authorityofaudittable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#authorityofaudittable')) {
            $('#authorityofaudittable').DataTable().clear().destroy();
        }
        renderTable(language);
    }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#exitmeetingtable')) {
            $('#exitmeetingtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ✅ Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);

    // ✅ Fetch column headers from JSON layout
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    // ✅ Column Mapping
    const columnMap = {
        departmentColumn: language === 'ta' ? 'deptengsname' : 'deptengsname',
        CategoryColumn: language === 'ta' ? 'cattamname' : 'catengname',
        subcategoryColumn: language === 'ta' ? 'subcategory_tname' : 'subcategory_ename',
        instColumn: language === 'ta' ? 'insttname' : 'instename',


    };

    // ✅ Define Headers
    let headers = [
        { header: dtText["department"] || "Department", category: "department" },
        { header: dtText["category"] || "Category", key: "category" },
        { header: dtText["subcategory"] || "Sub Category", key: "subcategory" },
        { header: dtText["Institution"] || "Institution", key: "institution" },
        { header: dtText["auth_content_en"] || "Authority of Audit Name in English", key: "auth_content_en" },
        { header: dtText["auth_content_ta"] || "Authority of Audit Name in Tamil", key: "auth_content_ta" },



    ];

    // ✅ Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    function stripHtml(html) {
    return $('<div>').html(html).text();
}

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");

        let rowData = {};
        if (dataRow) {
            try {
                let cleaned = $('<textarea/>').html(dataRow).text(); // decode HTML entities
                rowData = JSON.parse(cleaned);
            } catch (e) {
               // console.error("Invalid JSON in data-row:", dataRow, e);
                rowData = {};
            }
        }

        return {
            department: rowData[columnMap.departmentColumn] || "-",
            category: rowData[columnMap.CategoryColumn] || "-",
            subcategory: rowData[columnMap.subcategoryColumn] || "-",
            institution: rowData[columnMap.instColumn] || "-",
            auth_content_en: stripHtml(rowData["auth_content_en"] || "-"),
            auth_content_ta: stripHtml(rowData["auth_content_ta"] || "-"),

        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // ✅ Create Workbook and Worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add headers
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });

    // ✅ Add data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    // ✅ Finalize and export
    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}





function validateSubcategoryFields(validator) {
    let subcategoryHasError = false;
    const if_subcategory = $('#if_subcategory').val();

    const catcode = $('#category').val();


    if (if_subcategory === 'Y' && catcode === 'A') {

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
        var validator = $("#authorityofauditform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                instmappingcode: {
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

            if ($("#authorityofauditform").valid()) {
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

                var formData = new FormData($('#authorityofauditform')[0]);

                formData.set('templateparaename', englishData);
                formData.set('templateparatname', tamilData);



            $.ajax({
                url: "/authorityofaudit_insertupdate",
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


    function authorityofauditformddata(slip) {
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




    }

    $(document).on('click', '.viewAuthority, .editAuthority', function () {
    const id = $(this).attr('id');
    const isEdit = $(this).hasClass('editAuthority');   // TRUE only for edit button

    reset_form();
    $('#authority_id').val(id);

    $.ajax({
        url: "/authorityofaudit_fetchData",
        method: "POST",
        data: { authority_id: id },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: function (response) {
            if (response.success && response.data.length > 0) {

                const slip = response.data[0];
                let englishContent = "";
                let tamilContent = "";

                try {
    // ENGLISH
                    if (slip.auth_content_en && slip.auth_content_en.trim() !== "") {
                        if (slip.auth_content_en.trim().startsWith("{")) {
                            const parsed = JSON.parse(slip.auth_content_en);
                            englishContent = parsed.content ?? slip.auth_content_en;
                        } else {
                            englishContent = slip.auth_content_en; // plain text or HTML
                        }
                    }

                    // TAMIL
                    if (slip.auth_content_ta && slip.auth_content_ta.trim() !== "") {
                        if (slip.auth_content_ta.trim().startsWith("{")) {
                            const parsed = JSON.parse(slip.auth_content_ta);
                            tamilContent = parsed.content ?? slip.auth_content_ta;
                        } else {
                            tamilContent = slip.auth_content_ta;
                        }
                    }
                } catch (e) {
                    console.error("JSON Parse Error:", e);
                }

                // ============================
                //     VIEW MODE
                // ============================
                if (!isEdit) {
                    const tbody = $("#templateparaTable tbody");
                    tbody.empty();
                    tbody.append(`
                        <tr>
                            <td>${englishContent}</td>
                            <td>${tamilContent}</td>
                        </tr>
                    `);
                    $("#historyModal").modal("show");
                    return;
                }

                // ============================
                //     EDIT MODE
                // ============================
                changeButtonAction(
                    'authorityofauditform',
                    'action', 'buttonaction', 'reset_button',
                    'display_error',
                    @json($updatebtn),
                    @json($clearbtn),
                    @json($update)
                );

                $('#deptcode').val(slip.deptcode).select2();
                populateStatusFlag(slip.statusflag);

                //alert(slip.statusflag);

                getCategoriesBasedOnDept(slip.deptcode, slip.actual_catcode);



                    onchange_category(
                        slip.actual_catcode,
                        slip.actual_subcatcode,
                        slip.subcategory
                    );

                    setTimeout(() => {
                onchange_subcategory(slip.deptcode,slip.actual_catcode,slip.actual_subcatcode,slip.actual_instid)
            }, 400);

            //alert(slip.deptcode);


           // alert(slip.actual_catcode);


           // alert(slip.actual_subcatcode);


                if (editors['templateparaename']) {
                    editors['templateparaename'].setData(englishContent);
                }
                if (editors['templateparatname']) {
                    editors['templateparatname'].setData(tamilContent);
                }

                $('#tempslipid').val(slip.encrypted_tempslipid);
            } else {
                alert("No data found");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText || "Unknown error");
        }
    });
});



    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }


    function reset_form() {
    $('#authorityofauditform')[0].reset();
    $('#addActionContainer').removeClass('hide_this').show();
    $('#editschemeshow').addClass('hide_this');
    $('#instmappingcode').val(null).select2();
    $('#subcatdiv').show();

    if (sessiondeptcode && sessiondeptcode.trim() !== '') {
        getCategoriesBasedOnDept(null);
        $('#category').val(null).select2();
        $('#subcategory').val(null).select2();

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

    changeButtonAction('authorityofauditform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert));

    // Keep only the first .scheme-row and reset its inputs

}


</script>


@endsection
