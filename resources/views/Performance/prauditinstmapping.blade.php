@section('content')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
      //print_r($sessionchargedel);
    // print_r($sessionchargedel->roletypecode);
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;


    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<style>





    #performanceaudittable td.text-wrap {
        max-width: 200px;
    }
    .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px;
    }




    </style>

@if($pafinal === 'F')
   <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
    <div class="card text-center" style="width: 500px;">
        <div class="card-header bg-primary text-white">
            Plan Finalized
        </div>
        <div class="card-body bg-light">
            <p>This plan has been finalized. Editing of this form is not permitted.</p>
            <a href="{{ url('/home') }}" class="btn btn-success">OK</a>
        </div>
    </div>
</div>

@else


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Performance Audit</div>
             <div class="card-body">
                <form id="performanceauditform" name="performanceauditform" >
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">

                    <input type="hidden" name="if_subcategory" id="if_subcategory" value="">


                         <div class="col-md-3 mb-4">
                            <label class="form-label lang required " key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('','','')">
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

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="regioncode"
                                for="regioncode">Region</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown"   id="regioncode" name="regioncode"   <?php echo $make_region_disable; ?>
                            onchange="getDistrictBasedOnRegion('','','')">

                            <option value="" data-name-en="Select region" data-name-ta="">Select region</option>
                            @if ($regioncode)
                            @foreach ($region as $reg)
                                <option value="{{ $reg->regioncode }}"
                                @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                            data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                    {{ $reg->regionename }}
                                </option>
                            @endforeach
                        @else
                            <option disabled data-name-en="No Regions Available"
                                data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Regions
                                Available
                            </option>
                        @endif

                            </select>
                        </div>


                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="district"
                                for="distcode">District</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown"   id="distcode" name="distcode"  <?php echo $make_dist_disable; ?>  >

                            <option value="" data-name-en="Select district" data-name-ta="">Select district</option>
                            @if ($distcode)
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


                        <div class="col-md-3 mb-4">
                            <label class="form-label lang required" key="category"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                 onchange="onchange_category(); onchange_subcategory();onchange_distcode('','','','','','');">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                 <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                            </select>
                        </div>


                        <div class="col-md-3 " id="subcatdiv">
                            <label class="form-label lang required" key="if_subcategory"  for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 subcategory"  id="subcategory"
                                name="subcategory" onchange="onchange_subcategory('','','',''); onchange_distcode('','','','','','');">
                               <option value=""  data-name-en="---Select SubCategory---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>

                            </select>
                        </div>



                        <div class="col-md-3">
                            <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                                name="financialyear" >

                                <option value="" data-name-en="---Select Financial year---"
                                    data-name-ta="---நிதி ஆண்டை தேர்வு செய்க---">Select Financial year</option>

                                @foreach ($financialyear as $year)
                                    <option value="{{ $year->financialyearcode }}"
                                        data-name-en="{{ $year->financialyear }}"
                                        data-name-ta="{{ $year->financialyear }}"
                                        {{ $year->financialyear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- <div class="col-md-3">
                            <label class="form-label required lang" for="auditquarter" key="auditquarter">
                                Audit Quarter
                            </label>

                            <select class="form-select mr-sm-2 select2 lang-dropdown"
                                id="auditquarter"
                                name="auditquarter"
                                disabled>



                                @foreach ($quarter as $qua)
                                    <option value="{{ $qua->auditquartercode }}"
                                        data-name-en="{{ $qua->auditquartercode }}"
                                        data-name-ta="{{ $qua->auditquartercode }}"
                                        {{ $qua->auditquartercode == 'Q4' ? 'selected' : '' }}>
                                        {{ $qua->auditquartercode }}
                                    </option>
                                @endforeach

                            </select>
                        </div> --}}

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="auditquarter"
                                for="auditquarter">Team Size</label>
                            <input type="text" class="auditquarter form-control text_special" id="auditquarter" name="auditquarter"
                             required placeholder="Enter Audit Quarter" disabled>
                        </div>

                        <input type="hidden" class="auditquarter" id="auditquarter" name="auditquarter" >
                            <input type="hidden" name="prioritycode" value="">


                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="titleid"
                                for="titleid">Title</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown"   id="titleid" name="titleid">

                            <option value="" data-name-en="Select Title" data-name-ta="">Select Title</option>


                            </select>
                        </div>



                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="teamsize"
                                for="teamsize">Team Size</label>
                            <input type="text" class="form-control text_special" id="teamsize" name="teamsize"
                             required placeholder="Enter Teamsize" disabled>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="mandays"
                                for="mandays">Mandays</label>
                            <input type="text" class="form-control text_special" id="mandays" name="mandays"
                             required data-placeholder-key="mandays" disabled>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required lang" key="institution"
                                for="institution">Auditable Institution</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple"  id="instmappingcode" name="instmappingcode[]">



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








                    </div>

                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="prauditmapid" id="prauditmapid" value="" />
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
            <div class="card-header card_header_color lang" key="">Performance Audit Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="performanceaudittable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="">Department details</th>
                                    <th class="lang align-middle text-center" key="">Categories</th>
                                    <th class="lang align-middle text-center" key="">Financialyear / Quarter</th>
                                    <th class="lang align-middle text-center" key="">Title</th>
                                    <th class="lang align-middle text-center" key="">Team Size / Mandays</th>
                                    <th class="lang align-middle text-center" key="">Institution</th>

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
@endif

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

$(document).ready(function () {
    let lang = getLanguage();
    initializeDataTable(lang);
});


$('#distcode').on('change', function () {
    tonullcatndsubcat();
});

$('#deptcode').on('change', function () {

const dist = $(this).val();

if (!dist) {

    $('#regioncode').val(null).select2();
    $('#distcode').val(null).select2();

    $('#category').val(null).select2();

    $('#financialyear').val(null).select2();

    $('#auditquarter').val(null).select2();
    $('#titleid').val(null).select2();
    $('#teamsize').val(null);
    $('#mandays').val(null);
}

});


function tonullcatndsubcat() {

    $('#category')
        .val('')
        .select2();


}


function onchange_distcode(deptcode, catcode, subcategory,region,district, selectedInstitution = null) {
    var deptcode = deptcode || $('#deptcode').val();
    var catcode = catcode || $('#category').val();
    var subcategory = subcategory || $('#subcategory').val();
    var regioncode = region || $('#regioncode').val();
    var distcode = district || $('#distcode').val();

    let lang = getLanguage();

    const instDropdown = $('#instmappingcode');

    // Reset dropdowns
    instDropdown.empty();



    if (!deptcode || !catcode) {
        return;
    }

    $.ajax({
        url: '/getinstituionbasedondistcommon',
        method: 'POST',
        data: {
            deptcode: deptcode,
            category: catcode,
            subcatcode: subcategory,
            region: regioncode,
            district: distcode,

            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

            if (response.institutions && response.institutions.length > 0) {
                response.institutions.forEach(ins => {
                    instDropdown.append(`
                        <option value="${ins.instid}"
                            data-name-en="${ins.instename}"
                            data-name-ta="${ins.insttname}"
                            ${String(ins.instid) === String(selectedInstitution) ? 'selected' : ''}>
                            ${lang === 'ta' ? ins.insttname : ins.instename}
                        </option>
                    `);
                });
            } else {
                instDropdown.append(`
                    <option disabled data-name-en="No Institution Available" data-name-ta="நிறுவனம் கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'நிறுவனம் கிடைக்கவில்லை' : 'No Institution Available'}
                    </option>
                `);
            }

        },
        error: function(xhr, status, error) {
            console.error('Error fetching institutions/titles:', error);
        }
    });
}


function onchange_subcategory(deptcode, catcode, subcategory, selectedtitleid = null) {
    var deptcode = deptcode || $('#deptcode').val();
    var catcode = catcode || $('#category').val();
    var subcategory = subcategory || $('#subcategory').val();
    let lang = getLanguage();
        const titleDropdown = $('#titleid');
        const instDropdown = $('#instmappingcode');
        instDropdown.empty();
        titleDropdown.empty();


    titleDropdown.append(`
        <option value="" data-name-en="---Select Title---" data-name-ta="---தலைப்பைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---தலைப்பைத் தேர்ந்தெடுக்கவும்---' : '---Select Title---'}
        </option>
    `);

    if (!deptcode || !catcode) {
        return;
    }

    $.ajax({
        url: '/gettitlebasedonsubcategorycommon',
        method: 'POST',
        data: {
            deptcode: deptcode,
            category: catcode,
            subcatcode: subcategory,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

            if (response.titles && response.titles.length > 0) {
                response.titles.forEach(title => {
                    titleDropdown.append(`
                        <option value="${title.praudittitleid}"
                            data-name-en="${title.titleename}"
                            data-name-ta="${title.titletname}"
                             ${title.praudittitleid === selectedtitleid ? 'selected' : ''}>
                            ${lang === 'ta' ? title.titletname : title.titleename}
                        </option>
                    `);
                });
            } else {
                titleDropdown.append(`
                    <option disabled data-name-en="No Title Available" data-name-ta="தலைப்பு கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'தலைப்பு கிடைக்கவில்லை' : 'No Title Available'}
                    </option>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching institutions/titles:', error);
        }
    });
}

function getDistrictBasedOnRegion(deptcode, region, selectedDistrictCode = null,role=null) {
     var distrole = '<?php echo $Dist_roletypecode; ?>'
    var regionrole = '<?php echo $Re_roletypecode; ?>'

    const districtDropdown = $('#distcode');
    const institutionDropdown = $('#institution');
    const catcodeDropdown = $('#category');
    const subcatcodeDropdown = $('#subcategory');

    const lang = getLanguage();

    if (role !== distrole) {

    districtDropdown.empty();

    }
    institutionDropdown.empty();


    if (role !== distrole) {

    districtDropdown.html(`
        <option value="" data-name-en="---Select District---" data-name-ta="---மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select District---'}
        </option>
    `);
}

    institutionDropdown.html(`
        <option value="" data-name-en="---Select Audit Office---" data-name-ta="---ஆடிட் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---ஆடிட் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Audit Office---'}
        </option>
    `);

    if (!deptcode) {
        deptcode = $("#deptcode").val();
    }
    if (!region) {
        region = $("#regioncode").val();
    }

    if (!region) {
        districtDropdown.append(`
            <option value="" disabled data-name-en="No District Available" data-name-ta="மாவட்டம் கிடைக்கவில்லை">
                ${lang === 'ta' ? 'மாவட்டம் கிடைக்கவில்லை' : 'No District Available'}
            </option>
        `);




    }

    if (deptcode && region) {
        $.ajax({
            url: "/getdistrictbasedonregionforperformanceaudit",
            type: "POST",
            data: {
                deptcode: deptcode,
                region: region,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {

                if (role !== distrole) {

                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(district => {
                        districtDropdown.append(`
                            <option value="${district.distcode}"
                                data-name-en="${district.distename}"
                                data-name-ta="${district.disttname}"
                                ${district.distcode === selectedDistrictCode ? 'selected' : ''}>
                                ${lang === 'ta' ? district.disttname : district.distename}
                            </option>
                        `);
                    });
                } else {
                    districtDropdown.append(`
                        <option disabled data-name-en="No District Available" data-name-ta="மாவட்டம் கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'மாவட்டம் கிடைக்கவில்லை' : 'No District Available'}
                        </option>
                    `);
                }
            }
            },
            error: function() {
                alert('Error fetching district. Please try again.');
            }
        });
    }
}


function getCategoriesBasedOnDept(deptcode, selectedCatcode = null, selectedRegionCode = null,role=null) {
    var distrole = '<?php echo $Dist_roletypecode; ?>'
    var regionrole = '<?php echo $Re_roletypecode; ?>'

    const catcodeDropdown = $('#category');
    const subcategoryDropdown = $('#subcategory');
    const regionDropdown = $('#regioncode');


    const lang = getLanguage();
    // Reset dropdowns
    catcodeDropdown.empty();
    subcategoryDropdown.empty();


    if (role !== distrole && role !== regionrole) {

        regionDropdown.empty();
    }

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

    if (role !== distrole && role !== regionrole) {


    regionDropdown.html(`
        <option value="" data-name-en="---Select Region---" data-name-ta="---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Region---'}
        </option>
    `);

    }

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


        if (role !== distrole && role !== regionrole) {


        regionDropdown.append(`
            <option disabled data-name-en="No Region Available" data-name-ta="மண்டலம் கிடைக்கவில்லை">
                ${lang === 'ta' ? 'மண்டலம் கிடைக்கவில்லை' : 'No Region Available'}
            </option>
        `);
        return;

        }
    }

    // Fetch categories and regions
    $.ajax({
        url: "/getcategoriesbasednndeptforperformanceaudit",
        type: "POST",
        data: {
            deptcode: deptcode,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Populate Categories
            if (response.categories && response.categories.length > 0) {
                response.categories.forEach(category => {
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


    if (role !== distrole && role !== regionrole) {
            if (response.regions && response.regions.length > 0) {
                response.regions.forEach(region => {
                    regionDropdown.append(`
                        <option value="${region.regioncode}"
                            data-name-en="${region.regionename}"
                            data-name-ta="${region.regiontname}"
                            ${region.regioncode === selectedRegionCode ? 'selected' : ''}>
                            ${lang === 'ta' ? region.regiontname : region.regionename}
                        </option>
                    `);
                });
            } else {
                regionDropdown.append(`
                    <option disabled data-name-en="No Region Available" data-name-ta="மண்டலம் கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'மண்டலம் கிடைக்கவில்லை' : 'No Region Available'}
                    </option>
                `);
            }
        }

            $('#teamsize').val(response.teamsize || '');
            $('#mandays').val(response.mandays || '');


        if (response.priority && response.priority.length > 0) {
            const prio = response.priority[0];

            $('.auditquarter').val(prio.quartercode || '');

            $('input[name="prioritycode"]').val(prio.inst_priority || '');
        }


        },
        error: function() {
            alert('Error fetching categories and regions. Please try again.');
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



        if (!catcode) {

            subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `);

        }

        $.ajax({
            url: '/getsubCategoriesBasedOnperformance', // Your API route to get user details
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
                            ${selectedsubCatcode &&
                            String(subcategory.auditeeins_subcategoryid) === String(selectedsubCatcode)
                            ? 'selected' : ''}
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




    $('#translate').change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'performanceauditform');
    });

    function initializeDataTable(language) {

        $.ajax({
            url: "/performanceaudit_fetchdata",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
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





    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const CategoryColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcategoryColumn = language === 'ta' ? 'subcattname' : 'subcatename';
        const regionColumn = language === 'ta' ? 'deptesname' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';

        const InstColumn = language === 'ta' ? 'insttname' : 'instename';
        const TitleColumn = language === 'ta' ? 'titletname' : 'titleename';


        if ($.fn.DataTable.isDataTable('#performanceaudittable')) {
            $('#performanceaudittable').DataTable().clear().destroy();
        }

        table = $('#performanceaudittable').DataTable({
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
                        data: null,
                        render: function(data, type, row) {
                            const dept = row?.[departmentColumn] || '-';
                            const region = row?.[regionColumn] || '-';
                            const district = row?.[districtColumn] || '-';

                            return `
                                <div><b>Department :</b> ${dept}</div>
                                <div><b>Region :</b> ${region}</div>
                                <div><b>District :</b> ${district}</div>
                            `;
                        },
                        className: "text-start text-wrap"
                    },


                    {
                        data: null,
                        title: "Categories",
                        render: function(data, type, row) {
                            const cat = row?.[CategoryColumn] || '-';
                            const subcat = row?.[subcategoryColumn] || '-';

                            return `
                                <div><b>Category :</b> ${cat}</div>
                                <div><b>Sub Category :</b> ${subcat}</div>
                            `;
                        },
                        className: "text-start text-wrap wrap-50"
                    },


                    {
                        data: null,
                        title: "Financial Details",
                        render: function(data, type, row) {

                            const year = row?.financialyear || '-';
                            const quarter = row?.quartercode || '-';

                            return `
                                <div><b>Financial Year :</b> ${year}</div>
                                <div><b>Quarter :</b> ${quarter}</div>
                            `;
                        },
                        className: "text-start text-wrap wrap-50"
                    },

                    {
                        data: TitleColumn,
                        title: columnLabels?.[TitleColumn]?.[language],
                        render: function(data, type, row) {
                            return row[TitleColumn] || '-';
                        },
                        className: 'text-start text-wrap wrap-50' // Removed col-1
                    },

                    {
                        data: null,
                        render: function(data, type, row) {

                            const teamsize = row?.teamsize || '-';
                            const mandays = row?.mandays || '-';

                            return `
                                <div><b>Team Size :</b> ${teamsize}</div>
                                <div><b>Mandays :</b> ${mandays}</div>
                            `;
                        },
                        className: "text-start text-wrap"
                    },

                    {
                        data: InstColumn,
                        title: columnLabels?.[InstColumn]?.[language],
                        render: function(data, type, row) {
                            return row[InstColumn] || '-';
                        },
                        className: 'text-start text-wrap wrap-50' // Removed col-1
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
                        data: "encrypted_prauditmapid",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editAuthority" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                }





            ],

            "initComplete": function(settings, json) {
                $("#performanceaudittable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = [CategoryColumn,subcategoryColumn,"auditeeschemeesname","auditeeschemeelname","auditeeschemetsname","auditeeschemetlname","statusflag"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "performanceaudittable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#performanceaudittable')) {
            $('#performanceaudittable').DataTable().clear().destroy();
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

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });

    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
}


function validateSubcategoryFields(validator) {

let subcategoryHasError = false;

const if_subcategory = $('#if_subcategory').val();
const catcode = $('#category').val();

if (if_subcategory === 'Y') {

    $('.subcategory').each(function () {

        const $input = $(this);

        // Add validation rule
        if (validator) {
            $input.rules('add', {
                required: true,
                messages: {
                    required: "Enter Subcategory"
                }
            });
        }

        // Check validity
        if (!$input.valid()) {
            subcategoryHasError = true;
            return false; // break loop
        }

    });

} else {

    $('.subcategory').each(function () {

        if (validator) {
            $(this).rules('remove', 'required');
        }

    });

}

return !subcategoryHasError;
}

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#performanceauditform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                financialyear: {
                    required: true
                },

                auditquarter: {
                    required: true
                },
                titleid: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                teamsize: {
                    required: true
                },
                mandays: {
                    required: true
                },

                "instmappingcode[]": {
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
            var lang = getLanguage('Y');

            if ($("#performanceauditform").valid()) {
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
            var formData = $('#performanceauditform').serializeArray();


            $.ajax({
                url: "/performanceaudit_insertupdate",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        reset_form();
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        initializeDataTable(window.localStorage.getItem('lang') || 'en');
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


    function setInsertMode() {

        const $inst = $('#instmappingcode');

        $inst.select2('destroy');
        $inst.attr('multiple', 'multiple');
        $inst.attr('name', 'instmappingcode[]');

        $inst.val(null);

        $inst.select2({
            width: '100%'
        });
    }

    function setEditMode(selectedValue) {

        const $inst = $('#instmappingcode');

        $inst.select2('destroy'); // destroy first

        $inst.removeAttr('multiple'); // remove multi
        $inst.attr('name', 'instmappingcode'); // single value

        $inst.select2({
            width: '100%'
        });

        $inst.val(selectedValue).trigger('change');
    }

    $(document).on('click', '.viewAuthority, .editAuthority', function () {
    const id = $(this).attr('id');
    var sessionrole = '<?php echo $sessionroletypecode; ?>'



    reset_form();
   var prauditmapid = $('#prauditmapid').val(id);

    $.ajax({
        url: "/performanceaudit_fetchdata",
        method: "POST",
        data: { prauditmapid: id },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: function (response) {
            if (response.success && response.data.length > 0) {
                const audit = response.data[0];

                setEditMode(audit.instid);

                changeButtonAction(
                    'performanceauditform',
                    'action', 'buttonaction', 'reset_button',
                    'display_error',
                    @json($updatebtn),
                    @json($clearbtn),
                    @json($update)
                );

                $('#deptcode').val(audit.deptcode).select2();

                $('#financialyear').val(audit.financialyearcode).select2();
                $('.auditquarter').val(audit.quartercode);
                $('#teamsize').val(audit.teamsize);
                $('#mandays').val(audit.mandays);
                $('#prioritycode').val(audit.prioritycode);


                populateStatusFlag(audit.statusflag);

                getCategoriesBasedOnDept(audit.deptcode, audit.catcode,audit.regioncode,sessionrole);

                    onchange_category(
                        audit.catcode,
                        audit.auditeeins_subcategoryid,
                        audit.subcategory
                    );

                onchange_subcategory(audit.deptcode,audit.catcode,audit.auditeeins_subcategoryid,audit.praudittitleid)

            getDistrictBasedOnRegion(audit.deptcode,audit.regioncode,audit.distcode,sessionrole)

            setTimeout(() => {

            onchange_distcode(audit.deptcode,audit.catcode,audit.auditeeins_subcategoryid,audit.regioncode,audit.distcode,audit.instid)
        }, 400);



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


//     function reset_form() {

//         $('#performanceauditform')[0].reset();
//         $('#addActionContainer').removeClass('hide_this').show();
//         $('#instmappingcode').val(null).select2();
//         $('#subcatdiv').show();
//         $('#financialyear').val(null).select2();
//         $('#auditquarter').val(null).select2();
//         $('#distcode').val(null).select2();

    //     const $inst = $('#instmappingcode');

    //     $inst.select2('destroy');
    //     $inst.attr('multiple', 'multiple');
    //     $inst.attr('name', 'instmappingcode[]');
    //     $inst.val(null);

    //     $inst.select2({
    //     width: '100%'
    // });

//     if (sessiondeptcode && sessiondeptcode.trim() !== '') {
//         getCategoriesBasedOnDept(null);
//         $('#category').val(null).select2();
//         $('#subcategory').val(null).select2();

//     } else {
//         $('#deptcode').val(null).select2();
//         getCategoriesBasedOnDept(null);

//     }


//     changeButtonAction('performanceauditform', 'action', 'buttonaction', 'reset_button', 'display_error',
//         @json($savebtn), @json($clearbtn), @json($insert));


// }



$(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        var distcode = '<?php echo $distcode; ?>'
        var regioncode = '<?php echo $regioncode; ?>'
        var deptcode = '<?php echo $deptcode; ?>'
        var catcode =  $('#category').val();
       var subcategory =  $('#subcategory').val();

    //    alert(catcode);


        var lang = getLanguage();

        if (sessionrole == distrole) {

           getCategoriesBasedOnDept(deptcode,'','',sessionrole);
            onchange_distcode(deptcode,'','','','','')

        } else if (sessionrole == regionrole) {
            getCategoriesBasedOnDept(deptcode,'','',sessionrole);

            getDistrictBasedOnRegion(deptcode,regioncode,'');

        } else if (sessionrole == headofficerole) {

            getCategoriesBasedOnDept(deptcode,'','');
        } else if (sessionrole == dgarole) {
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();
            $('#financialyear').val('').select2();
            $('#instmappingcode').val('').select2();
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


        var form = $('#performanceauditform');

        const $inst = $('#instmappingcode');

        $inst.select2('destroy');
        $inst.attr('multiple', 'multiple');
        $inst.attr('name', 'instmappingcode[]');
        $inst.val(null);

        $inst.select2({
        width: '100%'
        });

            $('#financialyear').val(null).select2();
            $('#category').val('').select2();
            $('#subcatdiv').show();
            $('#subcategory').val(null).trigger('change');
            $('#titleid').val(null).select2();
            $('#instmappingcode').val('').select2();


        // Reset validation messages
        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }
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
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();


        } else if (sessionrole == dgarole) {


            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#deptcode').val('').select2();
            $('#teamsize').val('');
            $('#mandays').val('');
            $('.auditquarter').val('');

        } else if (sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


          }

changeButtonActionwithoutformrefresh('performanceauditform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert));
    }



</script>


@endsection

