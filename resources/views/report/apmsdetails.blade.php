@section('content')
@extends('index2')
@include('common.alert')
@php
    $sessionmainobjectiondel = session('charge');

    $sessionchargedel = session('charge');

    //print_r($sessionchargedel);

    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;
    $roleTypeCode = $sessionchargedel->roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

@endphp

<style>

html, body {
  overflow-x: hidden;
}


.wrap-50 {
    white-space: normal !important;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 300px; /* Adjust as needed */
}

.select2-container .select2-selection {
    border: 1px solid #dfe3eb !important;
}

@supports (-webkit-appearance: none) and (not (-moz-appearance: none)) {
    .select2-container--default .select2-dropdown {
        margin-top: -22px;
    }
}


.select2-container .select2-selection--multiple {
    min-height: 38px;   /* Bootstrap input height */
    display: flex;
    align-items: center;
}

#loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner {
    width: 35px;
    height: 35px;
    border: 4px solid #ffffff;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}



</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-center">Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="detailsModalBody">
                <!-- content injected here -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>




<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Audit Para Management details</div>
            <div class="card-body">
                <form id="apmsdetailform" name="apmsdetailform">
                    @csrf
                    <div class="row">

                        <input type="hidden" value="" id="if_subcategory" name="if_subcategory">


                <div class="col-md-3 mb-2" id="deptdiv">
                    <label class="form-label required  lang" key="department" for="dept">Department</label>

                    <select class="mr-sm-2 select2   lang-dropdown" multiple="multiple"  <?php echo $make_dept_disable; ?>  id="deptcode"   name="deptcode[]">

                        {{-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> --}}



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
                            <option disabled>No Departments Available</option>
                        @endif

                    </select>
                </div>

                        <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?>   id="regioncode" name="regioncode[]"
                                >

                                {{-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select a Region</option> --}}

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                                    @if($regioncode)
                                    @foreach ($region as $reg)
                                        <option value="{{ $reg->regioncode }}"
                                            @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                            data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                            {{ $reg->regionename }}
                                        </option>
                                        @endforeach
                                    @else

                                @endif

                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2" multiple="multiple"  <?php echo $make_dist_disable; ?>   id="distcode" name="distcode[]">



                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                                    @if($distcode)
                                    @foreach ($district as $dist)
                                        <option value="{{ $dist->distcode }}"
                                            @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                            data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                            {{ $dist->distename }}
                                        </option>
                                    @endforeach
                                @else

                                @endif

                                </select>
                            </div>


                            <div class="col-md-3">
                                <label class="form-label lang required" key="category"
                                  for="validationDefault01">Category</label>

                              <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                              >



                                  {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option> --}}

                              </select>
                          </div>


                          <div class="col-md-3  subcatdiv ">
                              <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                              <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                                  name="subcategory[]">



                                  {{-- <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option> --}}

                              </select>
                          </div>

                          <div class="col-md-3">
                            <label class="form-label required lang" for="audityearcode" key="audityearcode">Audit Year</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"
                                id="audityearcode" name="audityearcode[]">

                                {{-- <option value="" data-name-en="Select Audit Year" data-name-ta="தணிக்கை ஆண்டைத் தேர்ந்தெடுக்கவும்">
                                Select Audit Year
                            </option> --}}



                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            </select>
                        </div>


                            <div class="col-md-3 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]" >



                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                </select>
                        </div>
<div  class="col-md-3 mb-3">
                            <label class="form-label required lang" key="fromdateforapms"
                            for="fromdateforapms">Select From Date</label>
                            <div class="input-group" >
                                <input
                                    type="text"
                                    class="form-control bg-white datepicker"
                                    name="fromdateforapms"
                                    id="fromdateforapms"

                                    placeholder="dd-mm-yyyy"
                                    autocomplete="off"
                                    data-required="true"
                                    onclick="initDatePicker(this, 'from')"

                                    >
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>

                        </div>

                            <div  class="col-md-3 mb-3">
                                <label class="form-label required lang" key="todateforapms"
                                for="todateforapms">Select To Date</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control bg-white datepicker"
                                        name="todateforapms"
                                        id="todateforapms"
                                        placeholder="dd-mm-yyyy"
                                        autocomplete="off"
                                        data-required="true"
                                        onclick="initDatePicker(this, 'to')"
                                        >
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>

                            </div>


                        <div class="col-md-3">
                            <label class="form-label required lang" for="apmsstatuscode" key="apmsstatuscode">Status</label>
                            <select class="form-select mr-sm-2  select2 lang-dropdown" id="apmsstatuscode" name="apmsstatuscode"
                          >
                                <option value="" data-name-en="Select a Status" data-name-ta="Select a Status">Select a Status</option>
                                <option value="B" data-name-en="All" data-name-ta="All">All</option>
                                <option value="A">Dropped </option>
                                <option value="U">Need Clarification</option>
                                <option value="I">Rejected</option>

                            </select>
                        </div>

                        <div id="loader">
                            <div class="spinner"></div>
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

                    </div>

                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header  card_header_color lang" id="cardHeader" key="">Audit Para Management Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="apmsdetailtable"
                            class="table w-100 table-striped table-bordered display  align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department Details</th>
                                     {{-- <th class="lang align-middle text-center" key="region">Region</th>
                                    <th class="lang align-middle text-center" key="district">District</th> --}}
                                    <th class="lang align-middle text-center" key="">Category /Objections</th>
                                    <th class="lang align-middle text-center" key="">Audit Year</th>
                                    <th class="lang align-middle text-center" key="">Para Details</th>
                                    <th class="lang align-middle text-center" key="">Remarks</th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>



                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class=' lang text-center' key="no_data">
                    <center class="lang" key="no_data">No Data Available</center>

                </div>
            </div>
        </div>

    </div>
</div>




<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script>
   var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'

//window.AUTH_ROLETYPE = "{{ $sessionroletypecode  ?? '' }}";

function initDatePicker(el, type) {

// Restrict manual typing
el.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9-]/g, '');
    if (this.value.length === 2 || this.value.length === 5) {
        if (!this.value.endsWith('-')) this.value += '-';
    }
    if (this.value.length > 10) this.value = this.value.slice(0, 10);
});

let endDate = new Date();
let startDate = null;
let defaultViewDate = null;

if (type === 'from') {
    defaultViewDate = endDate;
}

if (type === 'to') {
    const fromVal = document.querySelector('[name="fromdateforapms"]').value;

    if (fromVal) {
        const [dd, mm, yyyy] = fromVal.split('-');
        startDate = new Date(yyyy, mm - 1, dd);
    }
}

$(el)
    .datepicker('destroy')
    .datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        startDate: startDate,
        endDate: endDate,
        defaultViewDate: defaultViewDate
    })
    .on('changeDate', function () {

        if (type === 'to') {
            const fromInput = document.querySelector('[name="fromdateforapms"]');
            if (fromInput.value) {
                const f = fromInput.value.split('-');
                const t = el.value.split('-');

                const fromDate = new Date(f[2], f[1] - 1, f[0]);
                const toDate = new Date(t[2], t[1] - 1, t[0]);

                if (toDate < fromDate) {
                    el.value = '';
                    el.classList.add('is-invalid');
                    alert('To Date cannot be less than From Date');
                    return;
                }
            }
        }

        el.classList.remove('is-invalid');
    })
    .datepicker('show');
}
let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    var sessionregioncode = '<?php echo $regioncode; ?>';
    var sessiondistcode = '<?php echo $distcode; ?>';


    let deptChangeTimeout;
$('#deptcode').on('change', function () {

clearTimeout(deptChangeTimeout);

deptChangeTimeout = setTimeout(function() {

    let deptcode = $('#deptcode').val();

    if (!deptcode.includes("A")) {
        $('#regioncode').val('').trigger('change.select2');
        $('#distcode').val('').trigger('change.select2');
        $('#category').val('').trigger('change.select2');
        $('#subcategory').val('').trigger('change.select2');
        $('#audityearcode').val('').trigger('change.select2');
        $('#instmappingcode').val('').trigger('change.select2');

    }

    onchange_region('region', 'regioncode');
    onchange_region('catcode', 'category');
    onchange_region('audityear', 'audityearcode');

}, 200);
});
function updateAuditHeadingAfterSubmit() {
    const status = $('#apmsstatuscode').val();

    let headingText = 'Audit Para Management Details';

    switch (status) {
        case 'B':
        case '':
            headingText = 'Audit Para Details as on';
            break;

        case 'A':
            headingText = 'Dropped - Audit Para Details';
            break;

        case 'U':
            headingText = 'Clarification - Audit Para Details';
            break;

        case 'I':
            headingText = 'Rejected - Audit Para Details';
            break;
    }

    $('#cardHeader').text(headingText);
}



$("#regioncode").on("change", function() {
    onchange_region('district', 'distcode');
});

$("#distcode").on("change", function() {
    onchange_region('institution', 'instmappingcode');
});

$("#category").on("change", function() {
    onchange_region('subcatcode', 'subcategory');
});


 onchange="('subcatcode','subcategory')"
$(document).ready(function () {
    $("#loader").hide();

    function updateSelect($select) {
        let selectedValues = $select.val() || [];

        if (selectedValues.includes('A')) {
            $select.val(['A']).trigger('change.select2');
            $select.find('option').not('[value="A"]').prop('disabled', true);

        } else {
            if (selectedValues.includes('')) {
                selectedValues = selectedValues.filter(v => v !== '');
                $select.val(selectedValues).select2();
            }

            $select.find('option').prop('disabled', false);
        }
    }

    function handleAllOption($select) {
        $select.on('change', function () {
            updateSelect($select);
        });

        if (!$select.val() || $select.val().length === 0) {
            $select.val(['']).select2();
        }

        updateSelect($select);
    }

    ['#deptcode','#distcode','#instmappingcode','#regioncode','#category','#subcategory']
        .forEach(function (selector) {
            handleAllOption($(selector));
        });
});



$(document).ready(function () {


$('#category').on('change', function () {

    var deptcode = $('#deptcode').val(); // ← array


    if (deptcode && (deptcode.includes("01") || deptcode.includes("05"))) {
        $('.subcatdiv').hide();
    } else {
        $('.subcatdiv').show();
    }

});


});


$(document).on('click', '.view-details', function () {


const rowData = $(this).data('row');


getInstData('en', 'Y', {
    paraid: rowData.paraid,
    instid: rowData.instid
});

});



function renderRemarksModal(rowData) {
    let html = '';

    /* -------- LEGACY REMARKS -------- */
    html += `
        <div class="border rounded p-2 mb-3">
            <p class="mb-1 fw-bold">Legacy Remarks :</p>
            <p class="mb-0">${rowData.legacy_remarks || '-'}</p>
        </div>
    `;

    html += `<h6 class="fw-bold mt-4 mb-2">Para Remarks</h6>`;

    if (rowData.para_remarks) {

        rowData.para_remarks.split('|||').forEach(block => {

            let role = '', date = '', remarks = '', action = '';
            let extraDetails = '';

            block.split('|').forEach(item => {
                item = item.trim();

                if (item.startsWith('Role')) {
                    role = item.replace('Role:', '').trim();
                }
                else if (item.startsWith('Action')) {
                    action = item.replace('Action:', '').trim();
                }
                else if (item.startsWith('Date')) {
                    date = item.replace('Date:', '').trim().split(' ')[0];
                }
                else if (item.startsWith('Remarks')) {
                    remarks = item.replace('Remarks:', '').trim();
                }
            });

            /* -------- PSA AUDITOR DETAILS -------- */
            if (role === 'PSA Auditor' && rowData.psa_auditor_details) {
                let name = '', district = '';
                rowData.psa_auditor_details.split(',').forEach(part => {
                    part = part.trim();
                    if (part.startsWith('Name:')) name = part.replace('Name:', '').trim();
                    if (part.startsWith('District:')) district = part.replace('District:', '').trim();
                });

                extraDetails = `
                    <p><strong>Name :</strong> ${name || '-'}</p>
                    <p><strong>District :</strong> ${district || '-'}</p>
                `;
            }

            /* -------- PSA AD DETAILS -------- */
            if (role === 'PSA AD' && rowData.psa_ad_details) {
                let name = '', district = '';
                rowData.psa_ad_details.split(',').forEach(part => {
                    part = part.trim();
                    if (part.startsWith('Name:')) name = part.replace('Name:', '').trim();
                    if (part.startsWith('District:')) district = part.replace('District:', '').trim();
                });

                extraDetails = `
                    <p><strong>Name :</strong> ${name || '-'}</p>
                    <p><strong>District :</strong> ${district || '-'}</p>
                `;
            }

            html += `
                <div class="border rounded p-2 mb-3">
                    <p><strong>Role :</strong> ${role || '-'}</p>
                    ${extraDetails}
                    <p><strong>Action :</strong> ${action || '-'}</p>
                    <p><strong>Date :</strong> ${date || '-'}</p>
                    <p><strong>Remarks :</strong><br>${remarks || '-'}</p>
                </div>
            `;
        });

    } else {
        html += `<p>No para remarks available</p>`;
    }

    $('#detailsModalBody').html(html);
    new bootstrap.Modal('#detailsModal').show();
}





// $(document).on('click', '.view-details', function () {

// let rowData = $(this).data('row');
// let html = '';

// /* -------- LEGACY REMARKS -------- */
// html += `
//     <div class="border rounded p-2 mb-3">
//         <p class="mb-1 fw-bold"><strong>Legacy Remarks :</strong></p>
//         <p class="mb-0">${rowData.legacy_remarks || '-'}</p>
//     </div>
// `;

// html += `
//     <h6 class="fw-bold mt-4 mb-2"><strong>Para Remarks</strong></h6>
// `;

// if (rowData.para_remarks) {

//     let blocks = rowData.para_remarks.split('|||');

//     blocks.forEach(block => {

//         let role = '';
//         let date = '';
//         let remarks = '';
//         let extraDetails = '';

//         block.split('|').forEach(item => {
//             item = item.trim();

//             if (item.startsWith('Role')) {
//                 role = item.replace('Role:', '').trim();
//             }
//             if (item.startsWith('Date')) {
//                 date = item.replace('Date:', '').trim();
//             }
//             if (item.startsWith('Remarks')) {
//                 remarks = item.replace('Remarks:', '').trim();
//             }
//         });

//         /* -------- REMOVE TIME FROM DATE -------- */
//         if (date) {
//             date = date.split(' ')[0];
//         }

//         /* -------- ROLE BASED DETAILS -------- */
//         let name = '';
//         let district = '';

//         if (role === 'PSA Auditor' && rowData.psa_auditor_details) {

//             rowData.psa_auditor_details.split(',').forEach(part => {
//                 part = part.trim();
//                 if (part.startsWith('Name:')) {
//                     name = part.replace('Name:', '').trim();
//                 }
//                 if (part.startsWith('District:')) {
//                     district = part.replace('District:', '').trim();
//                 }
//             });

//             extraDetails = `
//                 <p class="mb-1"><strong>Name :</strong> ${name || '-'}</p>
//                 <p class="mb-1"><strong>District :</strong> ${district || '-'}</p>
//             `;
//         }

//         if (role === 'PSA AD' && rowData.psa_ad_details) {

//             rowData.psa_ad_details.split(',').forEach(part => {
//                 part = part.trim();
//                 if (part.startsWith('Name:')) {
//                     name = part.replace('Name:', '').trim();
//                 }
//                 if (part.startsWith('District:')) {
//                     district = part.replace('District:', '').trim();
//                 }
//             });

//             extraDetails = `
//                 <p class="mb-1"><strong>Name :</strong> ${name || '-'}</p>
//                 <p class="mb-1"><strong>District :</strong> ${district || '-'}</p>
//             `;
//         }

//         html += `
//             <div class="border rounded p-2 mb-3">
//                 <p class="mb-1"><strong>Role :</strong> ${role || '-'}</p>
//                 ${extraDetails}
//                 <p class="mb-1"><strong>Date :</strong> ${date || '-'}</p>
//                 <p class="mb-0"><strong>Remarks :</strong><br>${remarks || '-'}</p>
//             </div>
//         `;
//     });

// } else {
//     html += '<p>No para remarks available</p>';
// }

// $('#detailsModalBody').html(html);

// let modal = new bootstrap.Modal(document.getElementById('detailsModal'));
// modal.show();
// });





function onchange_region(valuefor, valueforid) {

        const $dropdown = $("#" + valueforid);
        $dropdown.empty()
        $dropdown.empty().trigger('change.select2');


        var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
        var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
        var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();


        var instmappingcode = $("#instmappingcode").val();
        var catcode = $("#category").val();
        var subcatcode = $("#subcategory").val();
        var apmsstatuscode = $("#apmsstatuscode").val();

         var lang = getLanguage();


         if (!Array.isArray(deptcode)) {
            deptcode = deptcode ? [deptcode] : [];
        }

        if (deptcode.length === 0 || deptcode.includes("")) {
            return;
        }


        if ((deptcode.includes("A") || regioncode.includes("A") || distcode.includes("A")) && valueforid !== 'subcategory') {

            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A" data-name-en="All" data-name-ta="All">All</option>`);
            dd.select2();
        return;
            }



        let sendData = {
        valuefor: valuefor,
        formname: "apmsdetails"
    };


    if (valuefor === "region" || valuefor === "catcode" || valuefor === "audityear") {
        sendData.deptcode = deptcode;
    }


    if (valuefor === "district") {
        sendData.deptcode = deptcode;
        sendData.regioncode = regioncode;
    }


    if (valuefor === "institution") {
        sendData.deptcode = deptcode;
        sendData.regioncode = regioncode;
        sendData.distcode = distcode;
    }



    if (valuefor === "subcatcode") {
        sendData.catcode = catcode;
    }




        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
            data: sendData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.success && Array.isArray(response.data)) {


                    $dropdown.empty();



                    if (!regioncode || regioncode.length === 0) {
                        regioncode = [''];
                    }

                    if (!distcode || distcode.length === 0) {
                        distcode = [''];
                    }

                    if (!instmappingcode || instmappingcode.length === 0) {
                        instmappingcode = [''];
                    }

                    if (!catcode || catcode.length === 0) {
                        catcode = [''];
                    }


                    if (!subcatcode || subcatcode.length === 0) {
                        subcatcode = [''];
                    }

                    if (!audityearcode || audityearcode.length === 0) {
                    audityearcode = [''];
                    }


                    const options = response.data.map(item => {
                        switch (valuefor) {
                            case 'region':
                        return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${regioncode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                            : `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}" ${regioncode.includes(item.regioncode) ? "selected" : ""}>${item.regionename}</option>`;


                            case 'district':
                            return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${distcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${distcode.includes(item.distcode) ? "selected" : ""}>${item.distename}</option>`;

                                case 'catcode':
                                return item.code === 'A'
                                    ? `<option value="A"
                                            data-name-en="${item.name_en}"
                                            data-name-ta="${item.name_ta}"
                                            data-ifsubcategory="${item.if_subcategory}"
                                            ${catcode.includes('A') ? "selected" : ""}>
                                            ${item.name_en}
                                    </option>`
                                    : `<option value="${item.catcode}"
                                            data-name-en="${item.catename}"
                                            data-name-ta="${item.catename}"
                                            data-ifsubcategory="${item.if_subcategory}"
                                            ${catcode.includes(item.catcode) ? "selected" : ""}>
                                            ${item.catename}
                                    </option>`;




                                    case 'audityear':
                        return item.code === 'A'
                            ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${audityearcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                            : `<option value="${item.auditperiodid}" data-name-en="${item.audit_period}" data-name-ta="${item.audit_period}" ${audityearcode.includes(item.auditperiodid) ? "selected" : ""}>${item.audit_period}</option>`;



                                case 'subcatcode':
                              return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${subcatcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.auditeeins_subcategoryid}" data-name-en="${item.subcatename}" data-name-ta="${item.subcattname}" ${subcatcode.includes(item.auditeeins_subcategoryid) ? "selected" : ""}>${item.subcatename}</option>`;


                            case 'institution':
                            if (deptcode.includes('A') || regioncode.includes('A') || distcode.includes('A')) {
                                return item.code === 'A'
                                    ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${instmappingcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                    : `<option value="${item.instid}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${instmappingcode.includes(item.instid) ? "selected" : ""}>${item.instename}</option>`;
                                }

                                if (!Array.isArray(response.data) || response.data.length === 1 && item.code === 'A') {
                                        return `<option disabled>No institution available</option>`;
                                }


                            return item.code === 'A'
                                ? `<option value="A" data-name-en="${item.name_en}" data-name-ta="${item.name_ta}" ${instmappingcode.includes('A') ? "selected" : ""}>${item.name_en}</option>`
                                : `<option value="${item.instid}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${instmappingcode.includes(item.instid) ? "selected" : ""}>${item.instename}</option>`;
                            default:
                                return '';
                        }
                    }).join('');

                    $dropdown.append(options || '<option value="">No data available</option>');

                    if ($dropdown.hasClass('select2-hidden-accessible')) {
                        $dropdown.trigger('change.select2');
                    }

                } else {
                    console.error("Invalid response or no data:", response);
                    $dropdown.append('<option value="">No data available</option>');
                }

            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                console.error('Error details:', xhr, status, error);
            }
        });

    }


    $('#category').on('change', function () {
    const ifSub = $(this).find('option:selected').data('ifsubcategory');
    $('#if_subcategory').val(ifSub);
});


function getInstData(lang, remarks = 'N', extraParams = {}) {




$("#loader").show();
let fromdateforapms = $('#fromdateforapms').val();
let todateforapms = $('#todateforapms').val();

let deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0 ? $('#deptcode').val() : ['A'];
let regioncode = $('#regioncode').val() && $('#regioncode').val().length > 0 ? $('#regioncode').val() : ['A'];
let distcode = $('#distcode').val() && $('#distcode').val().length > 0 ? $('#distcode').val() : ['A'];
let instmappingcode = $('#instmappingcode').val() && $('#instmappingcode').val().length > 0 ? $('#instmappingcode').val() : ['A'];

let apmsstatuscode = $("#apmsstatuscode").val();
let category = $("#category").val();
let subcatcode = $("#subcategory").val() || 'A';
let audityearcode = $('#audityearcode').val() || 'A';

$.ajax({
    url: '/fetch_apmsdetails',
    type: 'POST',
    data: {
        deptcode,
        regioncode,
        distcode,
        apmsstatuscode,
        category,
        subcatcode,
        audityearcode,
        instmappingcode,
fromdateforapms,
        todateforapms,
        remarks: remarks,

        ...extraParams
    },
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {

        $("#loader").hide();


        if (remarks === 'Y') {
            if (response.data && response.data.length > 0) {
                renderRemarksModal(response.data[0]);
            }
            return;
        }


        if (response.data && response.data.length > 0) {
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
    error: function (xhr, status, error) {

        $("#loader").hide();

        if (xhr.status === 404) {
            $('#tableshow').hide();
            $('#usertable_wrapper').hide();
            $('#no_data').show();
        }

        let response = xhr.responseJSON || {};
        let errorMessage = response.message || 'An unknown error occurred';

        passing_alert_value(
            'Alert',
            errorMessage,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'confirmation_alert'
        );

        console.error('Error:', xhr, status, error);
    }
});
}



    jsonLoadedPromise.then(() => {
    const language = window.localStorage.getItem('lang') || 'en';

    $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
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


    var validator = $("#apmsdetailform").validate({
        ignore: [],
        rules: {
            "deptcode[]": { selectRequired: true },
            "audityearcode[]" : { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },
            "category[]": { selectRequired: true },
            "subcategory[]": { subcategoryRequired: true },
            "instmappingcode[]": { selectRequired: true },
            apmsstatuscode : { required: true },
 fromdateforapms : { required: true },
            todateforapms : { required: true },
        },
        messages: {
            "deptcode[]": "Select a Department",
            "audityearcode[]" : "Select a Audit Period",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",
            "category[]": "Select a Category",
            "subcategory[]": "Enter Subcategory",
            "instmappingcode[]": "Select a Institution",
            apmsstatuscode : "Select a Status",
fromdateforapms : "Select a Fromdate",
            todateforapms : "Select a To date",
        },
       errorPlacement: function (error, element) {

            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
            }
            else if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            }
            else {
                error.insertAfter(element);
            }
            }

    });

    $('#buttonaction').on('click', function (e) {
        e.preventDefault();
        var form = $('#apmsdetailform');
        if (form.valid()) {
            updateAuditHeadingAfterSubmit();
            getInstData(language,'N');
        }
    });

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});





    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($clearbtn),
            @json($clearbtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'apmsdetailform');
    });




    function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}



    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const instColumn = language === 'ta' ? 'instename' : 'instename';
        const catcodeColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcatcodeColumn = language === 'ta' ? 'subcattname' : 'subcatename';
        const MainobjectionColumn = language === 'ta' ? 'objectiontname' : 'objectionename';
        const SubobjectionColumn = language === 'ta' ? 'subobjectiontname' : 'subobjectionename';

        // const DistrictColumn = language === 'ta' ? 'disttname' : 'distename';

       //  const designationColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if ($.fn.DataTable.isDataTable('#apmsdetailtable')) {
            $('#apmsdetailtable').DataTable().clear().destroy();
        }

        table = $('#apmsdetailtable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                data: null,
                render: function(data, type, row, meta) {
                    const safeRow = escapeHtml(JSON.stringify(row));
                    return `<div>
                        <button class="toggle-row d-md-none" data-row="${safeRow}">▶</button>${meta.row + 1}
                    </div>`;
                },
                className: 'text-start',
                type: "num"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const dept = row?.[departmentColumn] || '-';
                        const region = row?.[regionColumn] || '-';
                        const district = row?.[districtColumn] || '-';
                        const institution= row?.[instColumn] || '-';

                        return `
                            <div><b>Department :</b> ${dept}</div>
                            <div><b>Region :</b> ${region}</div>
                            <div><b>Para District :</b> ${district}</div>
                            <strong>Institution:</strong> ${institution}

                        `;
                    },
                    className: "text-start text-wrap wrap-50"
                },
                {
                data: null,
                title: columnLabels?.[catcodeColumn]?.[language],
                render: function(data, type, row) {
                    const category = row?.[catcodeColumn] || '-';
                    const subcategory = row?.[subcatcodeColumn] || '-';
                    const mainObjection = row?.[MainobjectionColumn] || '-';
                    const subObjection = row?.[SubobjectionColumn] || '-';

                    return `
                        <strong>Category:</strong> ${category} <br>
                        <strong>SubCategory:</strong> ${subcategory} <br>
                        <strong>Main Objection:</strong> ${mainObjection} <br>
                        <strong>Sub Objection:</strong> ${subObjection} <br>

                    `;
                },
                className: "text-start d-none d-md-table-cell extra-column text-wrap wrap-50"
            },



            {
                    data: "audit_year",
                    title: columnLabels?.["audit_year"]?.[language],
                    render: function(data, type, row) {
                        return data || '-';
                    },
                    className: "text-start text-wrap"
                },

                {
                data: null,
                title: columnLabels?.["audit_details"]?.[language] || "Audit Details",
                className: "text-start text-wrap wrap-50",
                render: function (data, type, row) {

                    let paraNumber  = row.para_number || '-';
                    let severity    = row.severity || '-';
                    let gist        = row.gist_of_para || '-';

                    return `
                        <div>
                            <span><b>Para No:</b> ${paraNumber}</span><br>
                            <span><b>Severity:</b> ${severity}</span><br>
                            <span><b>Gist of para:</b> ${gist}</span>
                        </div>
                    `;
                }
            },




                {
                data: "nill",
                className: "d-none d-md-table-cell lang extra-column text-wrap text-center",
                orderable: false,
                render: function (data, type, row) {

                    return `
                        <button
                            type="button"
                            class="btn btn-sm btn-primary view-details"
                            data-row='${JSON.stringify(row)}'>
                            View Details
                        </button>
                    `;
                }
            },




                {
                data: "processcode",
                title: columnLabels?.["processcode"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function(data, type, row) {

                    const map = {
                        'U': 'Need Clarification',
                        'I': 'Rejected',
                        'A': 'Dropped'
                    };

                    // If code exists in map, return text; else return original code or '-'
                    return map[row.processcode] ?? row.processcode ?? '-';
                }
             },




            ],

            "initComplete": function(settings, json) {
                $("#apmsdetailtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [regionColumn,districtColumn,,"auditquartercode","audit_period","createdon","updatedon","entrymeetdate","exitmeetdate","processcode"];

        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "apmsdetailtable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#apmsdetailtable')) {
            $('#apmsdetailtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }



        $(document).ready(function () {
            var sessionrole = '<?php echo $sessionroletypecode; ?>';
            var dgarole = '<?php echo $dga_roletypecode; ?>';
            var distrole = '<?php echo $Dist_roletypecode; ?>';
            var regionrole = '<?php echo $Re_roletypecode; ?>';
            var headofficerole = '<?php echo $Ho_roletypecode; ?>';
            var adminrole = '<?php echo $Admin_roletypecode; ?>';
            var lang = getLanguage();

            var deptcode = $("#deptcode").val();
            var region = $("#regioncode").val();
            var district = $("#distcode").val();

            var reportstatus = $("#parareportstatus").val();
            var instmappingcode = $("#instmappingcode").val();

            if (!deptcode || !region || !district) {
                deptcode = deptcode || '';
                region = region || '';
                district = district || '';
            }

            if (sessionrole == distrole ) {
                onchange_region('catcode', 'category');
                onchange_region('audityear', 'audityearcode');
                onchange_region('institution', 'instmappingcode');

            }
            else if(sessionrole == dgarole){
               // getInstData(lang);

            }
            else if(sessionrole == regionrole){
                onchange_region('district', 'distcode');

                onchange_region('catcode', 'category');
                onchange_region('audityear', 'audityearcode');
            }
            else if(sessionrole == headofficerole)
            {
                onchange_region('catcode', 'category');
                onchange_region('audityear', 'audityearcode');
                onchange_region('region', 'regioncode');
            }
     });




     function parseParaRemarks(rowData) {

let out = {
    auditee_role: "Auditee",
    auditee_date: "-",
    auditee_remarks: "-",

    psa_auditor_role: "PSA Auditor",
    psa_auditor_name: "-",
    psa_auditor_district: "-",
    psa_auditor_date: "-",
    psa_auditor_remarks: "-",

    psa_ad_role: "PSA AD",
    psa_ad_name: "-",
    psa_ad_district: "-",
    psa_ad_date: "-",
    psa_ad_remarks: "-"
};

if (!rowData.para_remarks) return out;

rowData.para_remarks.split('|||').forEach(block => {

    let role = "", date = "", remarks = "";

    block.split('|').forEach(p => {
        p = p.trim();
        if (p.startsWith('Role:')) role = p.replace('Role:', '').trim();
        if (p.startsWith('Date:')) date = p.replace('Date:', '').trim().split(' ')[0];
        if (p.startsWith('Remarks:')) remarks = p.replace('Remarks:', '').trim();
    });

    if (role === 'Auditee') {
        out.auditee_role = role;
        out.auditee_date = date;
        out.auditee_remarks = remarks;
    }

    if (role === 'PSA Auditor') {
        out.psa_auditor_role = role;
        out.psa_auditor_date = date;
        out.psa_auditor_remarks = remarks;

        if (rowData.psa_auditor_details) {
            rowData.psa_auditor_details.split(',').forEach(d => {
                d = d.trim();
                if (d.startsWith('Name:')) out.psa_auditor_name = d.replace('Name:', '').trim();
                if (d.startsWith('District:')) out.psa_auditor_district = d.replace('District:', '').trim();
            });
        }
    }

    if (role === 'PSA AD') {
        out.psa_ad_role = role;
        out.psa_ad_date = date;
        out.psa_ad_remarks = remarks;

        if (rowData.psa_ad_details) {
            rowData.psa_ad_details.split(',').forEach(d => {
                d = d.trim();
                if (d.startsWith('Name:')) out.psa_ad_name = d.replace('Name:', '').trim();
                if (d.startsWith('District:')) out.psa_ad_district = d.replace('District:', '').trim();
            });
        }
    }
});

return out;
}



// function exportToExcel(tableId, language) {
//     let table = $(`#${tableId}`).DataTable();

//     let titleKey = `${tableId}_title`;
//     let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Audit Para details";
//     let safeSheetName = reportTitle.substring(0, 31);
//     let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


//     const columnMap = {
//           departmentColumn : language === 'ta' ? 'deptesname' : 'deptesname',
//           DistrictColumn : language === 'ta' ? 'disttname' : 'distename',
//           catcodeColumn : language === 'ta' ? 'cattname' : 'catename',
//           subcatcodeColumn : language === 'ta' ? 'subcattname' : 'subcatename',
//           RegionColumn : language === 'ta' ? 'regiontname' : 'regionename',
//           InstitutionColumn : language === 'ta' ? 'insttname' : 'instename',
//           MainobjColumn : language === 'ta' ? 'objectiontname' : 'objectionename',
//           SubobjColumn : language === 'ta' ? 'subobjectiontname' : 'subobjectionename'

//     };

//     let headers = [
//         { header: dtText["department"] || "Department", key: "department" },
//         { header: dtText["region"] || "Region", key: "region" },
//         { header: dtText["district"] || "District", key: "district" },
//         { header: dtText["catcode"] || "Catewgory", key: "catcode" },
//         { header: dtText["subcatcode"] || "Subcategory", key: "subcatcode" },
//         { header: dtText["institution"] || "Auditable Institution", key: "audit_office" },
//         { header: dtText["mainobjection"] || "Main Objection", key: "mainobjection" },
//         { header: dtText["subobjection"] || "Sub Objection", key: "subobjection" },
//         { header: dtText["audit_year"] || "Audit Year", key: "audit_year" },

//         { header: dtText["para_number"] || "Para Number", key: "para_number" },
//         { header: dtText["amount_involved"] || "Amount Involved", key: "amount_involved" },
//         { header: dtText["severity"] || "Severiety", key: "severity" },
//         { header: dtText["gist_of_para"] || "Gist of Para", key: "gist_of_para" },
//         { header: dtText["legacy_remarks"] || "Legacy Remarks", key: "legacy_remarks" },
//         { header: "Role", key: "auditee_role" },
//         { header: "Auditee Date", key: "auditee_date" },
//         { header: "Auditee Remarks", key: "auditee_remarks" },

//         { header: "Role", key: "psa_auditor_role" },
//         { header: "PSA Auditor Name", key: "psa_auditor_name" },
//         { header: "PSA Auditor District", key: "psa_auditor_district" },
//         { header: "PSA Auditor Date", key: "psa_auditor_date" },
//         { header: "PSA Auditor Remarks", key: "psa_auditor_remarks" },

//         { header: "Role", key: "psa_ad_role" },
//         { header: "PSA AD Name", key: "psa_ad_name" },
//         { header: "PSA AD District", key: "psa_ad_district" },
//         { header: "PSA AD Date", key: "psa_ad_date" },
//         { header: "PSA AD Remarks", key: "psa_ad_remarks" },

//         { header: dtText["status"] || "Status", key: "status" },




//     ];


//     let rawData = table.rows({ search: 'applied' }).data().toArray();
//     let excelData = [];



//     rawData.forEach(row => {
//         try {
//             let button = $(row[0]).find("button.toggle-row");
//             let dataRow = button.attr("data-row");
//             let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

//             let user = rowData["username"] || "-";
//             let desig = rowData[columnMap.desigColumn] || "-";
//             let parsed = parseParaRemarks(rowData);


//             excelData.push({
//             department: rowData[columnMap.departmentColumn] || "-",
//             region: rowData[columnMap.RegionColumn] || "-",
//             district: rowData[columnMap.DistrictColumn] || "-",
//             catcode: rowData[columnMap.catcodeColumn] || "-",
//             subcatcode: rowData[columnMap.subcatcodeColumn] || "-",
//             institution: rowData[columnMap.InstitutionColumn] || "-",
//             mainobjection: rowData[columnMap.MainobjColumn] || "-",
//             subobjection: rowData[columnMap.SubobjColumn] || "-",
//             audit_period: rowData.audit_year || "-",

//             para_number: rowData.para_number || "-",
//             amount_involved: rowData.amount_involved || "-",
//             severity: rowData.severity || "-",
//             gist_of_para: rowData.gist_of_para || "-",
//             legacy_remarks: rowData.legacy_remarks || "-",

//             auditee_role: parsed.auditee_role,
//             auditee_date: parsed.auditee_date,
//             auditee_remarks: parsed.auditee_remarks,

//             psa_auditor_role: parsed.psa_auditor_role,
//             psa_auditor_name: parsed.psa_auditor_name,
//             psa_auditor_district: parsed.psa_auditor_district,
//             psa_auditor_date: parsed.psa_auditor_date,
//             psa_auditor_remarks: parsed.psa_auditor_remarks,

//             psa_ad_role: parsed.psa_ad_role,
//             psa_ad_name: parsed.psa_ad_name,
//             psa_ad_district: parsed.psa_ad_district,
//             psa_ad_date: parsed.psa_ad_date,
//             psa_ad_remarks: parsed.psa_ad_remarks,



//             status: (() => {
//             const code = rowData.processcode;

//             const statusMap = {
//                 "U": "Need Clarification",
//                 "I": "Rejected",
//                 "A": "Dropped"
//             };

//             return statusMap[code] || "-";
//         })()


//     });
//         } catch (e) {}
//     });

//     if (excelData.length === 0) {
//         alert("No data available for export!");
//         return;
//     }

//     const wb = XLSX.utils.book_new();
//     const ws = XLSX.utils.json_to_sheet([]);

//     // Add main title
//     XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });

//     // Merge columns for title
//     ws['!merges'] = [{
//         s: { r: 0, c: 0 },
//         e: { r: 0, c: headers.length - 1 }
//     }];

//     // Center title text by padding
//     let colCount = headers.length;
//     let totalWidth = colCount * 12;
//     let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");

//     XLSX.utils.sheet_add_aoa(ws, [[paddedTitle]], { origin: "A1" });

//     // Add header row
//     XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A2" });

//     // Add data rows
//     XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A3" });

//     XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
//     XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
// }






function exportToExcel(tableId, language) {
    $("#loader").show();

    const EXCEL_MAX_TEXT_LENGTH = 32767;
    const splitForExcelCells = (value) => {
        if (value === null || value === undefined || value === '') return ['-'];
        const text = String(value);
        if (text.length <= EXCEL_MAX_TEXT_LENGTH) return [text];

        const parts = [];
        for (let i = 0; i < text.length; i += EXCEL_MAX_TEXT_LENGTH) {
            parts.push(text.slice(i, i + EXCEL_MAX_TEXT_LENGTH));
        }
        return parts;
    };

    let deptcode = $('#deptcode').val() && $('#deptcode').val().length > 0 ? $('#deptcode').val() : ['A'];
    let regioncode = $('#regioncode').val() && $('#regioncode').val().length > 0 ? $('#regioncode').val() : ['A'];
    let distcode = $('#distcode').val() && $('#distcode').val().length > 0 ? $('#distcode').val() : ['A'];
    let instmappingcode = $('#instmappingcode').val() && $('#instmappingcode').val().length > 0 ? $('#instmappingcode').val() : ['A'];
    let apmsstatuscode = $("#apmsstatuscode").val();
    let category = $("#category").val();
    let subcatcode = $("#subcategory").val() || 'A';
    let audityearcode = $('#audityearcode').val() || 'A';
    let fromdateforapms = $('#fromdateforapms').val();
    let todateforapms = $('#todateforapms').val();

    let titleKey = `${tableId}_title`;
    let reportTitle = dataTables[language]?.datatable?.[titleKey] || "Audit Para Details";
    let safeSheetName = reportTitle.substring(0, 31);
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    $.ajax({
        url: '/fetch_apmsdetails',
        type: 'POST',
        data: {
            deptcode,
            regioncode,
            distcode,
            instmappingcode,
            apmsstatuscode,
            category,
            subcatcode,
            audityearcode,
            fromdateforapms,
            todateforapms,
            remarks: 'Y'
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            $("#loader").hide();

            if (!response.data || response.data.length === 0) {
                alert("No data available for export!");
                return;
            }

            // Parse the data and prepare for Excel
            const columnMap = {
                departmentColumn: language === 'ta' ? 'deptesname' : 'deptesname',
                DistrictColumn: language === 'ta' ? 'disttname' : 'distename',
                catcodeColumn: language === 'ta' ? 'cattname' : 'catename',
                subcatcodeColumn: language === 'ta' ? 'subcattname' : 'subcatename',
                RegionColumn: language === 'ta' ? 'regiontname' : 'regionename',
                InstitutionColumn: language === 'ta' ? 'insttname' : 'instename',
                MainobjColumn: language === 'ta' ? 'objectiontname' : 'objectionename',
                SubobjColumn: language === 'ta' ? 'subobjectiontname' : 'subobjectionename'
            };

            let headers = [
                { header: dtText["department"] || "Department", key: "department" },
                { header: dtText["region"] || "Region", key: "region" },
                { header: dtText["district"] || "District", key: "district" },
                { header: dtText["catcode"] || "Category", key: "catcode" },
                { header: dtText["subcatcode"] || "Subcategory", key: "subcatcode" },
                { header: dtText["institution"] || "Auditable Institution", key: "institution" },
                { header: dtText["mainobjection"] || "Main Objection", key: "mainobjection" },
                { header: dtText["subobjection"] || "Sub Objection", key: "subobjection" },
                { header: dtText["audit_year"] || "Audit Year", key: "audit_year" },
                { header: dtText["para_number"] || "Para Number", key: "para_number" },
                { header: dtText["amount_involved"] || "Amount Involved", key: "amount_involved" },
                { header: dtText["severity"] || "Severity", key: "severity" },
                { header: dtText["gist_of_para"] || "Gist of Para", key: "gist_of_para" },
                { header: dtText["legacy_remarks"] || "Legacy Remarks", key: "legacy_remarks" },
                { header: "Auditee Role", key: "auditee_role" },
                { header: "Auditee Date", key: "auditee_date" },
                { header: "Auditee Remarks", key: "auditee_remarks" },
                { header: "PSA Auditor Role", key: "psa_auditor_role" },
             
            ];
	  if (sessionrole === dgarole) {
                headers.push(
                    { header: "PSA Auditor Name", key: "psa_auditor_name" },
                    { header: "PSA Auditor District", key: "psa_auditor_district" }
                );
            }

            headers.push(
                { header: "PSA Auditor Date", key: "psa_auditor_date" },
                { header: "PSA Auditor Remarks", key: "psa_auditor_remarks" },
                { header: "PSA AD Role", key: "psa_ad_role" }
            );

            if (sessionrole === dgarole) {
                headers.push(
                    { header: "PSA AD Name", key: "psa_ad_name" },
                    { header: "PSA AD District", key: "psa_ad_district" }
                );
            }

            headers.push(
                { header: "PSA AD Date", key: "psa_ad_date" },
                { header: "PSA AD Remarks", key: "psa_ad_remarks" },
                { header: dtText["status"] || "Status", key: "status" }
            );

            let excelData = [];

            response.data.forEach(rowData => {
                try {
                    let parsed = parseParaRemarks(rowData);

                    excelData.push({
                        department: rowData[columnMap.departmentColumn] || "-",
                        region: rowData[columnMap.RegionColumn] || "-",
                        district: rowData[columnMap.DistrictColumn] || "-",
                        catcode: rowData[columnMap.catcodeColumn] || "-",
                        subcatcode: rowData[columnMap.subcatcodeColumn] || "-",
                        institution: rowData[columnMap.InstitutionColumn] || "-",
                        mainobjection: rowData[columnMap.MainobjColumn] || "-",
                        subobjection: rowData[columnMap.SubobjColumn] || "-",
                        audit_year: rowData.audit_year || "-",
                        para_number: rowData.para_number || "-",
                        amount_involved: rowData.amount_involved || "-",
                        severity: rowData.severity || "-",
                        gist_of_para: rowData.gist_of_para || "-",
                        legacy_remarks: rowData.legacy_remarks || "-",
                        auditee_role: parsed.auditee_role || "-",
                        auditee_date: parsed.auditee_date || "-",
                        auditee_remarks: parsed.auditee_remarks || "-",
                        psa_auditor_role: parsed.psa_auditor_role || "-",
                        psa_auditor_name: parsed.psa_auditor_name || "-",
                        psa_auditor_district: parsed.psa_auditor_district || "-",
                        psa_auditor_date: parsed.psa_auditor_date || "-",
                        psa_auditor_remarks: parsed.psa_auditor_remarks || "-",
                        psa_ad_role: parsed.psa_ad_role || "-",
                        psa_ad_name: parsed.psa_ad_name || "-",
                        psa_ad_district: parsed.psa_ad_district || "-",
                        psa_ad_date: parsed.psa_ad_date || "-",
                        psa_ad_remarks: parsed.psa_ad_remarks || "-",
                        status: (() => {
                            const code = rowData.processcode;
                            const statusMap = { "U": "Need Clarification", "I": "Rejected", "A": "Dropped" };
                            return statusMap[code] || "-";
                        })()
                    });
                } catch(e) {}
            });

            // Expand oversized values into continuation columns so full text is preserved.
            const maxPartsByKey = {};
            headers.forEach(h => { maxPartsByKey[h.key] = 1; });

            excelData.forEach(row => {
                headers.forEach(h => {
                    const parts = splitForExcelCells(row[h.key]);
                    if (parts.length > maxPartsByKey[h.key]) {
                        maxPartsByKey[h.key] = parts.length;
                    }
                });
            });

            const expandedHeaders = [];
            headers.forEach(h => {
                const partCount = maxPartsByKey[h.key] || 1;
                for (let i = 1; i <= partCount; i++) {
                    expandedHeaders.push({
                        header: i === 1 ? h.header : `${h.header} (${i})`,
                        key: i === 1 ? h.key : `${h.key}__part${i}`
                    });
                }
            });

            const expandedRows = excelData.map(row => {
                const out = {};
                headers.forEach(h => {
                    const parts = splitForExcelCells(row[h.key]);
                    const partCount = maxPartsByKey[h.key] || 1;

                    for (let i = 1; i <= partCount; i++) {
                        const key = i === 1 ? h.key : `${h.key}__part${i}`;
                        out[key] = parts[i - 1] ?? '';
                    }
                });
                return out;
            });

            // Generate Excel
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet([]);
            XLSX.utils.sheet_add_aoa(ws, [[reportTitle]], { origin: "A1" });
            ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: expandedHeaders.length - 1 } }];
            XLSX.utils.sheet_add_aoa(ws, [expandedHeaders.map(h => h.header)], { origin: "A2" });
            XLSX.utils.sheet_add_json(ws, expandedRows, { skipHeader: true, origin: "A3" });
            XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
            XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
        },
        error: function(xhr) {
            $("#loader").hide();
            console.error(xhr);
            alert("Error fetching data for Excel export!");
        }
    });
}


function reset_form() {
        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();
        $("#loader").hide();
        $('.subcatdiv').show();

        $('#deptcode option').prop('disabled', false);

        var form = $('#apmsdetailform');

        form[0].reset();

        if (form.data('validator')) {
        form.validate().resetForm();
        form.find('.error').removeClass('error');
        }
        $('#cardHeader').text('Audit Para Management Details');

        $('#audityearcode').val('').select2();
          $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#instmappingcode').val('').select2();
            $('#apmsstatuscode').val('').select2();

            $("#loader").hide();

        $('#apmsdetailform')[0].reset();
        if (sessionrole == distrole) {

        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#distcode').val('').select2();

        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();

        } else if (sessionrole == dgarole) {


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#deptcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }

    }



</script>


@endsection
