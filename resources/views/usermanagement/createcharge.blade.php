@section('content')
@section('title', 'Charge Table Report')

@extends('index2')
@include('common.alert')

@php
    $sessionchargedel = session('charge');
    $roleTypeCode = $sessionchargedel->roletypecode;
    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

    $district_hlc_roleactioncode = $dlc_roleactioncode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_deptdiv_show = $deptcode ? '' : 'hide_this';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_regiondiv_disable = $regioncode ? '' : 'hide_this';
    $make_district_disable = $distcode ? 'disabled' : '';
    $make_districtdiv_disable = $distcode ? '' : 'hide_this';

@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">



<style>
    /* Change the border color of the search box */
    .select2-container .select2-search__field {
        border-radius: 4px !important;
        height: 30px !important;
        padding: 5px;


    }



    .select2-container .select2-selection--single {
        font-size: 12px;
    }

    .select2-container .select2-results__option {
        font-size: 12px !important;
    }


    .select2-container .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);


    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin-top: 20px !important;
    }

    body .select2-results__option {
        padding: 4px 10px;
    }

    .select2-selection--single .select2-selection__rendered {
        height: 30px !important;
        line-height: 28px !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        padding-right: 30px;
        position: relative;
    }

    /* Remove focus outline and blue border */
    .select2-container--default .select2-selection--single:focus {
        outline: none !important;
        border-color: #ced4da !important;
        /* Match Bootstrap default */
        box-shadow: none !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 5px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="createCharge_Head">Create Charge</div>
            <div class="card-body">
                <form id="chargeform" name="chargeform">
                    <input type="hidden" name="chargeid" id="chargeid">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" for="dept" key="department">Department</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                onchange="getroletypecode_basedondept('','','')" <?php echo $make_dept_disable; ?>>
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>
                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}"
                                            @if (old('dept', $deptcode) == $department->deptcode) selected @endif>
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Departments Available</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="roletypecode" key="roletype">Role Type</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="roletypecode"
                                name="roletypecode" onchange="settingform_basedonroletypcode('','','','','','','','')">
                                <option value="" data-name-en="Select Role Type"
                                    data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ">---Select Role Type---
                                </option>
                                @if (isset($roletype) && is_iterable($roletype))
                                    @foreach ($roletype as $role)
                                        <option value="{{ $role->roletypecode }}"
                                            data-name-en="{{ $role->roletypeelname }}"
                                            data-name-ta="{{ $role->roletypetlname }}">
                                            {{ $role->roletypeelname }}
                                        </option>
                                    @endforeach
                                @else
                                @endif
                            </select>
                        </div>


                        <div class="col-md-4 mb-3 <?php echo $make_regiondiv_disable; ?>" id="regiondiv">
                            <label class="form-label required lang " for="validationDefault01" key="region">Region
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="regioncode" name="regioncode"
                                onchange="getvaluebasedon_regionroletype('', '', '', '', '')" <?php echo $make_region_disable; ?>>
                                <option value="" data-name-en="Select a Region"
                                    data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">Select Region</option>

                                @if ($regiondetails)
                                    {
                                    <option value="{{ $regiondetails['regioncode'] }}"
                                        data-name-en="{{ $regiondetails['regionename'] }}"
                                        data-name-ta="{{ $regiondetails['regiontname'] }}" selected>
                                        {{ $regiondetails['regionename'] }}
                                    </option>
                                    }
                                @endif

                            </select>
                        </div>
                        <div class="col-md-4 mb-3 <?php echo $make_districtdiv_disable; ?>" id="distdiv">
                            <label class="form-label required lang" for="validationDefault01" key="district">District
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                onchange="getdistregioninst_basedondept('', '', '', '', 'institution', 'instmappingcode','')"
                                <?php echo $make_district_disable; ?>>
                                <option value="" data-name-en="Select a District"
                                    data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">Select District</option>

                                @if ($distdetails)
                                    {
                                    <option value="{{ $distdetails['distcode'] }}"
                                        data-name-en="{{ $distdetails['distename'] }}"
                                        data-name-ta="{{ $distdetails['disttname'] }}" selected>
                                        {{ $distdetails['distename'] }}
                                    </option>
                                    }
                                @endif


                            </select>
                        </div>
                        <div class="col-md-4 mb-3 hide_this" id="instdiv">
                            <label class="form-label required lang" for="validationDefault01" key="inst">Insitution
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="instmappingcode"
                                name="instmappingcode">
                                <option value="" data-name-en="Select a Institution"
                                    data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select a Institution</option>

                                <option value="" disabled id="" data-name-en="No Institution Available"
                                    data-name-ta="நிறுவனம் கிடைக்கவில்லை">No Institution Available</option>

                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="validationDefault01"
                                key="designation">Designation</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="desigcode" name="desigcode">
                                <option value='' data-name-en="Select a Designation"
                                    data-name-ta="பதவியைத் தேர்ந்தெடுக்கவும்">Select a Designation</option>
                                @if (!empty($designation) && is_iterable($designation))
                                    @foreach ($designation as $department)
                                        <option value="{{ $department->desigcode }}"
                                            data-name-en="{{ $department->desigelname }}"
                                            data-name-ta="{{ $department->desigtlname }}">
                                            {{ $department->desigelname }}
                                        </option>
                                    @endforeach

                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="validationDefault02"
                                key="chargedescription">Charge Description</label>
                            <input type="text" class="form-control" id="chargedescription"
                                name="chargedescription" data-placeholder-key="chargeDescription" required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="validationDefaultUsername"
                                key="roleaction">Role Action</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="roleactioncode"
                                name="roleactioncode">
                                <option value='' data-name-en="Select Role Action"
                                    data-name-ta="பங்கு செயலைத் தேர்ந்தெடுக்கவும்">Select Role Action</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3 hide_this" id="categorydiv">
                            <label class="form-label required lang" for="validationDefault01"
                                key="category">Category</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="catcode" name="catcode"
                                onchange="onchange_cat()">
                                <option value="" data-name-en="Select a Category"
                                    data-name-ta="வகையைத் தேர்ந்தெடுக்கவும்">
                                    Select a Category
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 hide_this" id="subcategorydiv">
                            <label class="form-label required lang" for="validationDefault01" key="subcategory">Sub
                                Category</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcatid" name="subcatid">
                                <option value="" data-name-en="Select a Sub Category"
                                    data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">
                                    Select a Sub Category
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- <div class="row">
                                    <div class="col-md-4  mx-auto">
                                        <button class="btn btn-success mt-3" type="submit"> Submit </button>
                                        <button class="btn btn-danger mt-3" type="submit"> Cancel </button>
                                    </div>
                                </div> -->
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save Draft </button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="createCharge_table">Department Charge Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="chargetable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="roletype">Roletype</th>
                                    <th class="lang align-middle text-center" key="region">Region</th>
                                    <th class="lang align-middle text-center" key="district">District</th>
                                    <th class="lang align-middle text-center" key="inst">Institution</th>
                                    <th class="lang align-middle text-center" key="roleaction">RoleAction</th>
                                    <th class="lang align-middle text-center" key="designation">Designation</th>
                                    <th class="lang align-middle text-center" key="chargedescription">
                                        chargedescription</th>
                                    <th class="lang align-middle text-center" key="category">
                                        Category</th>
                                    <th class="all align-middle text-center lang" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class=''>
                    <center>No Data Available</center>
                </div>
            </div>
        </div>

    </div>
</div>
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        display: flex;
        align-items: center;
        padding: 5px;
    }

    .select2-selection__arrow {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>

<!-- <script src="../assets/js/vendor.min.js"></script>  -->

<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../common/ajaxfn.js"></script>
<!-- select2 -->
<!--
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<!-- -------------------------------Download button-------------------- -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script>
    var session_roletypecode = '<?php echo $roleTypeCode; ?>';



    function getroletypecode_basedondept(deptcode, roletypecode, desigcode) {
        const lang = getLanguage();

        const defaultOption = `
                    <option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ">
                        ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ' : 'Select Role Type'}
                    </option>`;

        const $dropdown = $("#roletypecode");
        // Get department code from DOM if not passed
        if (!deptcode) deptcode = $('#deptcode').val();

        makedropdownempty('roleactioncode', 'Select Role Action');
        $('#categorydiv').addClass('hide_this');
        $('#catcode').val('');
        $('#subcategorydiv').addClass('hide_this');
        $('#subcatid').val('');



        if (deptcode) {
            // Clear the dropdown and set the default option
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getroletypecode_basedondept',
                type: 'POST',
                data: {
                    deptcode: deptcode,
                    'page': 'createcharge',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;

                        // Iterate through the roles and build options
                        response.data.forEach(({
                            roletypecode: code,
                            roletypeelname: name,
                            roletypetlname: nameTa
                        }) => {
                            if (code && name) {
                                const isSelected = (code === roletypecode) ? "selected" : "";
                                //options += `<option value="${code}" ${isSelected}>${name}</option>`;
                                options +=
                                    `<option value="${code}" ${isSelected} data-name-en="${name}" data-name-ta="${nameTa || name}">${name}</option>`;
                            }
                        });

                        // Append the options to the dropdown
                        $dropdown.html(options);
                        change_lang_for_page(getLanguage());
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    let errorMessage = response.error;

                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                }
            });

        } else {
            // Reset to default option if no department code is provided
            $dropdown.html(defaultOption);
        }
        getDesignationBasedonDept(deptcode, desigcode, 'createcharge', 'deptcode', 'desigcode');
    }

    function settingform_basedonroletypcode(roletypecode, deptcode, regioncode, distcode, instmappingcode,
        roleactioncode, catcode, subcatid) {
        if (!roletypecode) roletypecode = $('#roletypecode').val();

        if (!deptcode) deptcode = $('#deptcode').val();



        if (
            roletypecode &&
            (roletypecode == '<?php echo $Dist_roletypecode; ?>' || roletypecode == '<?php echo $Re_roletypecode; ?>')
        ) {

            if (
                session_roletypecode == '<?php echo $dga_roletypecode; ?>' || session_roletypecode == '<?php echo $Admin_roletypecode; ?>' ||
                session_roletypecode == '<?php echo $Ho_roletypecode; ?>'
            ) {
                makedropdownempty('regioncode', 'Select Region');
                getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region', 'regioncode');
                $('#regiondiv').show();

                makedropdownempty('instmappingcode', lang === 'ta' ? 'நிறுவனத்தை தேர்ந்தெடுக்கவும்' :
                    'Select Institution');
                $('#instdiv').show();

                if (roletypecode == '<?php echo $Dist_roletypecode; ?>') {
                    makedropdownempty('distcode', lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District');
                    $('#distdiv').show();
                } else {
                    $('#distdiv').hide();
                }
            }

            if (
                session_roletypecode == '<?php echo $Dist_roletypecode; ?>' ||
                session_roletypecode == '<?php echo $Re_roletypecode; ?>'
            ) {
                makedropdownempty('instmappingcode', 'Select Institution');
                $('#instdiv').show();
                $('#regiondiv').show();

                if (session_roletypecode == '<?php echo $Re_roletypecode; ?>') {

                    if (!regioncode) regioncode = $('#regioncode').val();

                    if (roletypecode == '<?php echo $Re_roletypecode; ?>') {
                        getdistregioninst_basedondept(
                            roletypecode, deptcode, regioncode, distcode, 'institution', 'instmappingcode',
                            instmappingcode
                        );
                    } else {
                        makedropdownempty('distcode', 'Select District');
                        $('#distdiv').show();
                        getdistregioninst_basedondept(
                            roletypecode, deptcode, regioncode, distcode, 'district', 'distcode'
                        );
                    }
                } else if (session_roletypecode == '<?php echo $Dist_roletypecode; ?>') {
                    $('#distdiv').show();
                    getdistregioninst_basedondept(
                        roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode', instmappingcode
                    );
                }
            }

        } else {
            $('#distdiv').hide();
            $('#regiondiv').hide();
            $('#instdiv').hide();
        }

        getRoleactionBasedOnRoletype(deptcode, roletypecode, roleactioncode, catcode, subcatid)

    }


    function getvaluebasedon_regionroletype(roletypecode, deptcode, regioncode, distcode) {

        if (!roletypecode) roletypecode = $('#roletypecode').val();
        if (!deptcode) deptcode = $('#deptcode').val();
        if (!regioncode) regioncode = $('#regioncode').val();



        if (roletypecode == '<?php echo $Re_roletypecode; ?>') {
            getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode', '')
        }
        if (roletypecode == '<?php echo $Dist_roletypecode; ?>') {
            getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'district', 'distcode')
        }
    }

    // function getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,
    //     instmappingcode) {
    //     if (valuefor == 'institution') {
    //         if (!roletypecode) roletypecode = $('#roletypecode').val();
    //         if (!deptcode) deptcode = $('#deptcode').val();
    //         if (!regioncode) regioncode = $('#regioncode').val();
    //         if (!distcode) distcode = $('#distcode').val();
    //     }

    //     const $dropdown = $("#" + valueforid);

    //     // Clear existing options and display a "Select" placeholder
    //     $dropdown.html('<option value="">Select</option>');

    //     // Make the AJAX request
    //     $.ajax({
    //         url: '/getRegionDistInstBasedOnDept',
    //         type: 'POST',
    //         data: {
    //             roletypecode,
    //             deptcode,
    //             regioncode,
    //             distcode,
    //             valuefor,
    //             page : 'createcharge',
    //             _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
    //         },
    //         success: function(response) {
    //             if (response.success && Array.isArray(response.data)) {

    //                 // Map the response data into <option> elements
    //                 const options = response.data.map(item => {
    //                     switch (valuefor) {
    //                         case 'region':
    //                             return `<option value="${item.regioncode}" ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
    //                         case 'district':
    //                             return `<option value="${item.distcode}" ${item.distcode === distcode ? "selected" : ""}>${item.distename}</option>`;
    //                         case 'institution':
    //                             return `<option value="${item.instmappingcode}" ${item.instmappingcode === instmappingcode ? "selected" : ""}>${item.instename}</option>`;

    //                             // return `<option value="${item.instmappingcode}">${item.instename}</option>`;
    //                         default:
    //                             return '';
    //                     }
    //                 }).join('');

    //                 // Append options to the dropdown
    //                 $dropdown.append(options || '<option value="">No data available</option>');
    //             } else {
    //                 console.error("Invalid response or no data:", response);
    //                 $dropdown.append('<option value="">No data available</option>');
    //             }
    //             //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    //         },
    //         error: function(xhr) {
    //             console.error("Error during AJAX request:", xhr);

    //             // Show error message and reset dropdown
    //             $dropdown.html('<option value="">Error loading data</option>');
    //             passing_alert_value(
    //                 'Alert',
    //                 'Error loading data. Please try again.',
    //                 'confirmation_alert',
    //                 'alert_header',
    //                 'alert_body',
    //                 'confirmation_alert'
    //             );
    //         }
    //     });
    // }


    function getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,
        instmappingcode) {
        // Default values for institution
        if (valuefor === 'institution') {
            roletypecode = roletypecode || $('#roletypecode').val();
            deptcode = deptcode || $('#deptcode').val();
            regioncode = regioncode || $('#regioncode').val();
            distcode = distcode || $('#distcode').val();
        }
        var lang = getLanguage();

        const $dropdown = $("#" + valueforid);

        let placeholderTextEn = '',
            placeholderTextTa = '';
        switch (valuefor) {
            case 'region':
                placeholderTextEn = 'Select a Region';
                placeholderTextTa = 'பகுதியை தேர்வு செய்';
                break;
            case 'district':
                placeholderTextEn = 'Select a District';
                placeholderTextTa = 'மாவட்டத்தை தேர்ந்தெடுக்கவும்';
                break;
            case 'institution':
                placeholderTextEn = 'Select an Institution';
                placeholderTextTa = 'நிறுவனத்தை தேர்ந்தெடுக்கவும்';
                break;
            default:
                placeholderTextEn = 'Select an Option';
                placeholderTextTa = 'ஒரு விருப்பத்தை தேர்வு செய்';
        }

        // Reset dropdown options with specific placeholder
        $dropdown.html(`<option value="" data-name-en="${placeholderTextEn}" data-name-ta="${placeholderTextTa}">
                        ${lang === 'ta' ? placeholderTextTa : placeholderTextEn}
                    </option>`);


        // Simplify conditions for clarity
        const isValid = roletypecode && (
            // (valuefor === 'region' && roletypecode === '<?php echo $Re_roletypecode; ?>' && deptcode) ||
            (valuefor === 'region' && deptcode) ||
            (valuefor === 'district' && roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode) ||
            (valuefor === 'institution' && roletypecode === '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode) ||
            (valuefor === 'institution' && roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode &&
                distcode)
        );

        if (isValid) {
            // Make the AJAX request
            $.ajax({
                url: '/getRegionDistInstBasedOnDept',
                type: 'POST',
                data: {
                    roletypecode,
                    deptcode,
                    regioncode,
                    distcode,
                    valuefor,
                    page: 'createcharge',
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        // Map response data to options
                        const options = response.data.map(item => {
                            switch (valuefor) {
                                case 'region':
                                    return `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}"  ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
                                case 'district':
                                    return `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${item.distcode === distcode ? "selected" : ""}>${item.distename}</option>`;
                                case 'institution':
                                    return `<option value="${item.instmappingcode}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${item.instmappingcode === instmappingcode ? "selected" : ""}>${item.instename}</option>`;
                                default:
                                    return '';
                            }
                        }).join('');

                        // Append options or show fallback message
                        $dropdown.append(options || '<option value="">No data available</option>');
                    } else {
                        console.error("Invalid response or no data:", response);
                        $dropdown.append('<option value="">No data available</option>');
                    }
                    //updateSelectColorByValue(document.querySelectorAll(".form-select"));
                },
                error: function(xhr) {
                    console.error("Error during AJAX request:", xhr);
                    $dropdown.html('<option value="">Error loading data</option>');
                    passing_alert_value(
                        'Alert',
                        'Error loading data. Please try again.',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            });
        } else {
            // console.warn('Validation failed: Missing required parameters.');
            // $dropdown.append('<option value="">Invalid parameters</option>');
            // makedropdownempty('desigcode', 'Select Designation');
            // makedropdownempty('chargeid', 'Select Charge Description');
            // makedropdownempty('userid', 'Select User');


        }
    }


    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        var validator = $("#chargeform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                roletypecode: {
                    required: true
                },
                desigcode: {
                    required: true
                },
                chargedescription: {
                    required: true
                },
                roleactioncode: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                instmappingcode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                catcode: {
                    required: function() {
                        return !$('#categorydiv').hasClass('hide_this');
                    }
                }
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },
            // messages: {
            //     deptcode: {
            //         required: "Select a department",
            //     },
            //     roletypecode: {
            //         required: "Select a roletype",
            //     },
            //     desigcode: {
            //         required: "Select a designation",
            //     },
            //     chargedescription: {
            //         required: "Enter a charge description",
            //     },
            //     roleactioncode: {
            //         required: "Select a role action",
            //     },
            //     regioncode: {
            //         required: "select a region",
            //     },
            //     distcode: {
            //         required: "Select a District",
            //     },
            //     instmappingcode: {
            //         required: "Select a institution",
            //     },
            // }
        });


        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#chargeform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#chargeform').serializeArray();


                $.ajax({
                    url: '/charge_insertupdate', // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                            // table.ajax.reload();
                            initializeDataTable(window.localStorage.getItem('lang'));


                        } else if (response.error) {
                            // Handle errors if needed
                            console.log(response.error);
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
        //reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    function resetDropdowns(lang) {
        var lang = getLanguage();

        $('#roletypecode').html(
            `<option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ">${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும்' : 'Select RoleType'}</option>`
        );
        $('#instmappingcode').html(
            `<option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'நிறுவனத்தை தேர்ந்தெடுக்கவும்' : 'Select Institution'}</option>`
        );

        $('#regioncode').html(
            `<option value="" data-name-en="Select Region" data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பகுதியை தேர்ந்தெடுக்கவும்' : 'Select Region'}</option>`
        );

        $('#distcode').html(
            `<option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}</option>`
        );

        $('#desigcode').html(
            `<option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பதவியை தேர்ந்தெடுக்கவும்' : 'Select Designation'}</option>`
        );

        $('#roleactioncode').html(
            `<option value='' data-name-en="Select Role Action" data-name-ta="பங்கு செயலைத் தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பங்கு செயலைத் தேர்ந்தெடுக்கவும்' : 'Select Role Action'}</option>`
        );
    }

    function reset_form() {
        var lang = getLanguage();

        if (
            session_roletypecode == '<?php echo $Ho_roletypecode; ?>' ||
            session_roletypecode == '<?php echo $Re_roletypecode; ?>' ||
            session_roletypecode == '<?php echo $Dist_roletypecode; ?>'
        ) {
            makedropdownempty('instmappingcode', 'Select Institution');

            if (session_roletypecode == '<?php echo $Ho_roletypecode; ?>') {
                makedropdownempty('regioncode', 'Select Region');
                makedropdownempty('distcode', 'Select District');
            } else if (session_roletypecode == '<?php echo $Re_roletypecode; ?>') {
                makedropdownempty('distcode', 'Select District');
            }
            $('#roletypecode').val('');
            $('#desigcode').val('');
        } else {
            $('#chargeform')[0].reset();
            // $('#roletypecode, #instmappingcode, #regioncode, #distcode, #desigcode').val('').change();
            resetDropdowns(lang); // Dynamically apply selected language
            // makedropdownempty('roletypecode', 'Select RoleType');
            // makedropdownempty('instmappingcode',  lang === 'ta' ? 'நிறுவனத்தை தேர்ந்தெடுக்கவும்' : 'Select Institution');
            // makedropdownempty('regioncode', 'Select Region');
            // makedropdownempty('distcode', 'Select District');
            // makedropdownempty('desigcode', 'Select Designation');
        }

        //$('#deptcode').val('').trigger('change');

        $('#roletypecode').val('');
        $('#chargeid').val('');
        $('#desigcode').val('');


        $('#chargedescription').val('');

        $('#display_error').hide();

        makedropdownempty('roleactioncode', 'Select Role Action');

        $('#chargeform').validate().resetForm();

        changeButtonAction('chargeform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert));
        // change_button_as_insert('chargeform', 'action', 'buttonaction', 'display_error', '', '');
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));


        change_lang_for_page(lang);


        $('#categorydiv').addClass('hide_this');
        $('#catcode').val('');
        delete window.pendingCatcode;
        delete window.pendingDeptcode;
        delete window.pendingRoleactioncode;


        $('#catcode').rules('remove', 'required');
        $('#catcode').removeAttr('required');

        if ($('#roletypecode').val() == "") {
            $('#distdiv').hide();
            $('#regiondiv').hide();
            $('#instdiv').hide();
        }
    }

    $(document).ready(function() {
        // Reset form and update select box colors on page load
        $('#chargeform')[0].reset();
        ////updateSelectColorByValue(document.querySelectorAll(".form-select"));

    });




    let table;
    let dataFromServer = [];

    $(document).ready(function() {
        $('#chargeform')[0].reset();
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);


    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));

        updateTableLanguage(lang);
        updateValidationMessages(getLanguage('Y'), 'chargeform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "/fetchchargeData",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
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
            }
        });
    }


    function renderTable(language) {
        const roletypeColumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const institutionColumn = language === 'ta' ? 'insttname' : 'instename';
        const roleactionColumn = language === 'ta' ? 'roleactiontlname' : 'roleactionelname';
        const categorycolumn = language === 'ta' ? 'cattname' : 'catename';
        const subcategorycolumn = language === 'ta' ? 'subcattname' : 'subcatename';


        if ($.fn.DataTable.isDataTable('#chargetable')) {
            $('#chargetable').DataTable().clear().destroy();
        }

        table = $('#chargetable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            // "columns": [{
            //         data: null,
            //         render: function(data, type, row, meta) {
            //             return meta.row + 1;
            //         }
            //     },
            //     {
            //         data: departmentColumn
            //     },
            //     {
            //         data: roletypeColumn
            //     },

            //     {
            //         data: regionColumn
            //     },
            //     {
            //         data: districtColumn
            //     },
            //     {
            //         data: institutionColumn
            //     },
            //     {
            //         data: roleactionColumn
            //     },
            //     {
            //         data: designationColumn
            //     },
            //     {
            //         data: "chargedescription"
            //     },
            //     {
            //         data: "encrypted_chargeid",
            //         render: function(data, type, row) {
            //             return `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
            //         }
            //     }
            // ],
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: roletypeColumn,
                    title: columnLabels?.[roletypeColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[roletypeColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: regionColumn,
                    title: columnLabels?.[regionColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[regionColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                }, {
                    data: districtColumn,
                    title: columnLabels?.[districtColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[districtColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                }, {
                    data: institutionColumn,
                    title: columnLabels?.[institutionColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[institutionColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                }, {
                    data: roleactionColumn,
                    title: columnLabels?.[roleactionColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[roleactionColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: designationColumn,
                    title: columnLabels?.[designationColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[designationColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "chargedescription",
                    title: columnLabels?.["chargedescription"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.chargedescription || '-';
                    }
                },
                {
                    data: categorycolumn,
                    title: columnLabels?.[categorycolumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const category = row?.[categorycolumn] || '-';
                        const subcategory = row?.[subcategorycolumn] || '-';

                        return `<b>Category: </b>${category} <br> <b>Subcategory:</b> ${subcategory}`;
                    },

                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: "encrypted_chargeid",
                    title: columnLabels?.["actions"]?.[language],
                    render: function(data, type, row) {
                        const assignedstatus = row.assignedstatus;
                        const action = assignedstatus == 'N' ? `

                                <a class="btn editicon editchargedel" id="${data}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                           ` : `
                                    <i class="ti ti-ban fs-4"></i>
                               `;
                        return `<center>
                        ${action}
                            </center>`;
                    },
                    className: "text-center noExport"
                }
            ],
            // "columnDefs": [{
            //         render: function(data) {
            //             return "<div class='text-wrap width-200'>" + data + "</div>";
            //         },
            //         targets: 1
            //     },
            //     {
            //         render: function(data) {
            //             return "<div class='text-wrap width-100'>" + data + "</div>";
            //         },
            //         targets: [2, 3] // Set width for columns
            //     }
            // ],
            "initComplete": function(settings, json) {
                $("#chargetable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //     "dom": '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
            //     "buttons": [{
            //         extend: "excelHtml5",
            //         text: window.innerWidth > 768 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
            //             '<i class="fas fa-download"></i>',
            //         title: 'Charge Table Report',
            //         exportOptions: {
            //             columns: ':not(:last-child)' // Excluding the last column (Action column)
            //         },
            //         className: window.innerWidth > 768 ? 'btn btn-info' :
            //             'btn btn-info btn-sm' // Full button on desktop, smaller on mobile
            //     }],
            //     "pagingType": "simple_numbers",
            //     "responsive": true,
            //     "pageLength": 10,
            //     "lengthMenu": [
            //         [10, 50, -1], // Full options for Desktop
            //         [10, 25, 50, -1] // Compressed options for Mobile
            //     ],
            //     "fnDrawCallback": function() {
            //         let $pagination = $('.dataTables_paginate');
            //         let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

            //         // Function to adjust pagination and info text based on window width
            //         function adjustView() {
            //             if ($(window).width() <= 768) {
            //                 // Mobile View Adjustments
            //                 $(".dataTables_filter input").css({
            //                     "width": "100px",
            //                     "font-size": "12px",
            //                     "padding": "4px"
            //                 }); // Smaller search box
            //                 $(".dt-buttons .btn").addClass("btn-sm"); // Smaller download button

            //                 // Compress pagination display to show only first & last buttons
            //                 let totalPages = $pages.length;
            //                 $pages.each(function(index) {
            //                     if (index !== 0 && index !== totalPages - 1) {
            //                         $(this).hide();
            //                     }
            //                 });

            //                 // Display "Showing x to y of z entries" on a separate row in mobile view
            //                 $(".dataTables_info").css("display", "block");
            //                 $(".dataTables_info").css("text-align", "center");
            //                 $(".dataTables_info").css("margin-bottom",
            //                     "20px"); // Optional: Align the text to center
            //             } else {
            //                 // Desktop View Adjustments
            //                 $(".dataTables_info").css("display", "inline-block");
            //                 $(".dataTables_filter input").css({
            //                     "width": "auto",
            //                     "font-size": "14px",
            //                     "padding": "8px"
            //                 }); // Reset search box style
            //                 $(".dt-buttons .btn").removeClass("btn-sm"); // Reset download button size
            //                 // Show all pagination buttons
            //                 $pages.show();
            //             }
            //         }

            //         // Call the function initially
            //         adjustView();

            //         // Call the function when the window is resized
            //         $(window).resize(function() {
            //             adjustView();
            //         });
            //     }
            // });

            // $(window).resize(function() {
            //     table.buttons(0).text(
            //         window.innerWidth > 768 ?
            //         '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
            //         '<i class="fas fa-download"></i>'
            //     );
        });
        const mobileColumns = [roletypeColumn, regionColumn, districtColumn, institutionColumn, roleactionColumn,
            designationColumn, "chargedescription", categorycolumn, subcategorycolumn
        ];

        setupMobileRowToggle(mobileColumns);


        updatedatatable(language, "chargeusertable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#chargetable')) {
            $('#chargetable').DataTable().clear().destroy();
        }
        renderTable(language);
    }








    // var table = $('#chargetable').DataTable({
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "/fetchchargeData",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataSrc: function(json) {
    //             if (json.data && json.data.length > 0) {
    //                 $('#tableshow').show();
    //                 $('#usertable_wrapper').show();
    //                 $('#no_data').hide(); // Hide custom "No Data" message
    //                 return json.data;
    //             } else {
    //                 $('#tableshow').hide();
    //                 $('#usertable_wrapper').hide();
    //                 $('#no_data').show(); // Show custom "No Data" message
    //                 return [];
    //             }
    //         },
    //     },
    //     columns: [{
    //             data: null,
    //             render: (_, __, ___, meta) => meta.row + 1, // Serial number column
    //             className: 'text-end' // Align to the right
    //         },
    //         {
    //             data: "deptesname"
    //         },
    //         {
    //             data: "roletypeelname"
    //         },
    //         {
    //             data: "regionename"
    //         },
    //         {
    //             data: "distename"
    //         },
    //         {
    //             data: "instename"
    //         },
    //         {
    //             data: "roleactionelname"
    //         },
    //         {
    //             data: "desigesname"
    //         },
    //         {
    //             data: "chargedescription"
    //         },
    //         {
    //             data: "encrypted_chargeid",
    //             render: (data) =>
    //                 `<center>
    //                         <a class="btn editicon editchargedel" id="${data}">
    //                             <i class="ti ti-edit fs-4"></i>
    //                         </a>
    //                     </center>`
    //         }

    // ]
    //     });


    //     // Handle Edit Button Click
    //     $(document).on('click', '.editchargedel', function () {
    //         const id = $(this).attr('id');
    //         if (id) {
    //             reset_form();
    //             $('#chargeid').val(id);
    //             $.ajax({
    //                 url: '/fetchchargeData',
    //                 method: 'POST',
    //                 data: { chargeid :id  },
    //                 headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    //                 success: function (response) {
    //                     if (response.success) {
    //                         populateChargeForm(response.data[0]);
    //                     } else {
    //                         alert('Charge not found');
    //                     }
    //                 },
    //                 error: function (xhr) {
    //                     console.error('Error:', xhr.responseText || 'Unknown error');
    //                 }
    //             });
    //         }

    // });


    // Handle Edit Button Click
    $(document).on('click', '.editchargedel', function() {
        const id = $(this).attr('id');
        if (id) {
            // resetForm();
            reset_form();
            $('#chargeid').val(id);
            $.ajax({
                url: '/fetchchargeData',
                method: 'POST',
                data: {
                    chargeid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        changeButtonAction('chargeform', 'action', 'buttonaction',
                            'reset_button', 'display_error', @json($updatebtn),
                            @json($clearbtn), @json($update));
                        populateChargeForm(response.data[0]);
                    } else {
                        alert('Charge not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });



    function populateChargeForm(charge) {
        $('#display_error').hide();
        changeButtonAction('chargeform', 'action', 'buttonaction',
            'reset_button', 'display_error', @json($updatebtn),
            @json($clearbtn), @json($update));

        $('#deptcode').val(charge.deptcode);
        $('#chargedescription').val(charge.chargedescription);

        const catcode = charge.catcode || '';

        const sessionroletypeCodes = [`<?php echo $dga_roletypecode; ?>`, `<?php echo $Admin_roletypecode; ?>`];
        if (sessionroletypeCodes.includes('<?php echo $roleTypeCode; ?>')) {
            getroletypecode_basedondept(charge.deptcode, charge.roletypecode, charge.desigcode);
        } else {
            $('#roletypecode').val(charge.roletypecode);
            $('#desigcode').val(charge.desigcode);
        }

        // Call settingform_basedonroletypcode but handle category AFTER roleaction is loaded
        settingform_basedonroletypcode(charge.roletypecode, charge.deptcode, charge.regioncode, charge.distcode, charge
            .instmappingcode, charge.roleactioncode, charge.catcode, charge.subcatid);

        const roletypeCodes = [`<?php echo $Re_roletypecode; ?>`, `<?php echo $Dist_roletypecode; ?>`];
        if (roletypeCodes.includes(charge.roletypecode)) {
            if (charge.roletypecode === '<?php echo $Dist_roletypecode; ?>') {
                getdistregioninst_basedondept(charge.roletypecode, charge.deptcode, charge.regioncode, charge.distcode,
                    'district', 'distcode');
            }
            getdistregioninst_basedondept(charge.roletypecode, charge.deptcode, charge.regioncode, charge.distcode,
                'institution', 'instmappingcode', charge.instmappingcode);
        }

        // Set the roleactioncode value
        $('#roleactioncode').val(charge.roleactioncode);

        // Store catcode globally to use after AJAX completion
        window.pendingCatcode = catcode;
        window.pendingDeptcode = charge.deptcode;
        window.pendingRoleactioncode = charge.roleactioncode;

        // //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }



    // function getDesignationBasedonDept(deptcode,editdesigcode)
    // {
    //     const defaultOption = "<option value=''>Select Designation </option>";
    //         const $dropdown = $("#desigcode");

    //         // Get department code from DOM if not passed
    //         if (!deptcode) deptcode = $('#deptcode').val();


    //         if (deptcode) {
    //             // Clear the dropdown and set the default option
    //             $dropdown.html(defaultOption);

    //             $.ajax({
    //                 url: '/getDesignationBasedonDept',
    //                 type: 'POST',
    //                 data: {
    //                     deptcode: deptcode,
    //                     'page': 'createuser',
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function(response) {
    //                     if (response.success && Array.isArray(response.data)) {
    //                         let options = defaultOption;

    //                         // Iterate through the roles and build options
    //                         response.data.forEach(({
    //                             desigcode: code,
    //                             desigelname: name
    //                         }) => {
    //                             if (code && name) {
    //                                 const isSelected = (code === editdesigcode) ? "selected" : "";
    //                                 options += `<option value="${code}" ${isSelected}>${name}</option>`;
    //                             }
    //                         });

    //                         // Append the options to the dropdown
    //                         $dropdown.html(options);
    //                     } else {
    //                         console.error("Invalid response or data format:", response);
    //                     }
    //                 },
    //                 error: function(xhr) {
    //                     console.log(xhr);
    //                     let errorMessage = response.error;

    //                     if (xhr.responseText) {
    //                         try {
    //                             const response = JSON.parse(xhr.responseText);
    //                             errorMessage = response.message || errorMessage;
    //                         } catch (e) {
    //                             console.error("Error parsing error response:", e);
    //                         }
    //                     }

    //                     passing_alert_value('Alert', errorMessage, 'confirmation_alert',
    //                         'alert_header', 'alert_body', 'confirmation_alert');
    //                 }
    //             });

    //         } else {
    //             // Reset to default option if no department code is provided
    //             $dropdown.html(defaultOption);
    //         }
    // }

    function getRoleactionBasedOnRoletype(deptcode, roletypecode, editroleactioncode, catcode, subcatid) {
        const lang = getLanguage();

        const defaultOption =
            `<option value='' data-name-en='Select Role Action' data-name-ta='பங்கு செயலைத் தேர்ந்தெடுக்கவும்'> ${lang === 'ta' ? 'பங்கு செயலைத் தேர்ந்தெடுக்கவும்' : 'Select Role Action'}</option>`;

        const $dropdown = $("#roleactioncode");
        if ((deptcode) && (roletypecode)) {
            // Clear the dropdown and set the default option
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getRoleactionBasedOnRoletype',
                type: 'POST',
                data: {
                    deptcode: deptcode,
                    roletypecode: roletypecode,
                    'page': 'createcharge',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;

                        // Iterate through the roles and build options
                        response.data.forEach(({
                            roleactioncode: code,
                            roleactionelname: name,
                            roleactiontlname: taname,
                            categoryflag: catFlag,
                            subcategoryflag: subcatflag
                        }) => {
                            if (code && name) {
                                const isSelected = (code === editroleactioncode) ? "selected" : "";
                                options +=
                                    `<option value="${code}" ${isSelected}
                                    data-name-en="${name}"
                                    data-name-ta="${taname || name}"
                                    data-categoryflag="${catFlag || 'N'}"
                                    data-subcategoryflag="${subcatflag || 'N'}">${name}</option>`;
                            }
                        });

                        $dropdown.html(options);

                        if (editroleactioncode &&
                            editroleactioncode != '<?php echo $district_hlc_roleactioncode; ?>') {
                            $dropdown.val(editroleactioncode);

                            if (window.pendingCatcode && window.pendingDeptcode && window
                                .pendingRoleactioncode === editroleactioncode) {
                                setTimeout(function() {
                                    checkAndShowCategoryFieldForEdit(
                                        window.pendingDeptcode,
                                        editroleactioncode,
                                        window.pendingCatcode
                                    );

                                    delete window.pendingCatcode;
                                    delete window.pendingDeptcode;
                                    delete window.pendingRoleactioncode;
                                }, 100);
                            } else {
                                setTimeout(function() {
                                    checkAndShowCategoryField(deptcode, editroleactioncode, catcode,
                                        subcatid);
                                }, 100);
                            }


                        }
                        if (editroleactioncode == '<?php echo $district_hlc_roleactioncode; ?>') {
                            setTimeout(function() {
                                checkAndShowCategoryField(deptcode, editroleactioncode, catcode,
                                    subcatid);
                            }, 100);
                        }
                        change_lang_for_page(getLanguage());

                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    let errorMessage = response.error;

                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                }
            });

        } else {
            $dropdown.html(defaultOption);
            $('#categorydiv').addClass('hide_this');
        }
    }


    function checkAndShowCategoryFieldForEdit(deptcode, roleactioncode, catcode, subcatid) {

        if (roleactioncode && deptcode) {
            const selectedOption = $('#roleactioncode option:selected');
            const categoryFlag = selectedOption.data('categoryflag');
            const subcategoryFlag = selectedOption.data('subcategoryFlag');

            if (categoryFlag === 'Y') {
                $('#categorydiv').removeClass('hide_this');

                // Load categories with the provided catcode
                loadCategoriesForEdit(deptcode, catcode, subcategoryFlag, subcatid);

                // Make category field required
                $('#catcode').attr('required', true);
                $('#catcode').rules('add', {
                    required: true,
                    messages: {
                        required: function() {
                            const lang = getLanguage();
                            return lang === 'ta' ? 'வகையைத் தேர்ந்தெடுக்கவும்' : 'Select a Category';
                        }
                    }
                });
            } else {
                $('#categorydiv').addClass('hide_this');
                $('#catcode').val('');

                // Remove required validation when hidden
                $('#catcode').rules('remove', 'required');
                $('#catcode').removeAttr('required');
            }
        }
    }

    function checkAndShowCategoryField(deptcode, selectedValue, selectedCatcode = '', selectedsubcatid) {
        if (!selectedValue) {
            selectedValue = $('#roleactioncode').val();
        }
        if (!deptcode) {
            deptcode = $('#deptcode').val();
        }

        if (selectedValue && deptcode) {
            // Need to get the selected option AFTER the dropdown is fully populated
            setTimeout(function() {
                const selectedOption = $('#roleactioncode option:selected');
                const categoryFlag = selectedOption.data('categoryflag');
                const subcategoryFlag = selectedOption.data('subcategoryflag');

                if (categoryFlag === 'Y') {
                    $('#categorydiv').removeClass('hide_this');
                    loadCategories(deptcode, selectedCatcode, subcategoryFlag, selectedsubcatid);

                    // Make category field required
                    $('#catcode').attr('required', true);
                    $('#catcode').rules('add', {
                        required: true,
                        messages: {
                            required: function() {
                                const lang = getLanguage();
                                return lang === 'ta' ? 'வகையைத் தேர்ந்தெடுக்கவும்' :
                                    'Select a Category';
                            }
                        }
                    });

                    // Set the value if provided
                    if (selectedCatcode) {
                        setTimeout(function() {
                            $('#catcode').select2().val(selectedCatcode);
                        }, 300);
                    }


                } else {
                    $('#categorydiv').addClass('hide_this');
                    $('#catcode').val('');

                    // Remove required validation when hidden
                    $('#catcode').rules('remove', 'required');
                    $('#catcode').removeAttr('required');
                }
            }, 300);
        } else {
            // Hide category field if no roleaction selected
            $('#categorydiv').addClass('hide_this');
            $('#catcode').rules('remove', 'required');
            $('#catcode').removeAttr('required');
        }
    }

    $(document).on('change', '#roleactioncode', function() {
        checkAndShowCategoryField();
    });

    function loadCategoriesForEdit(deptcode, selectedCatcode, subcategoryFlag, selectedsubcat) {

        const lang = getLanguage();
        const $categoryDropdown = $('#catcode');

        const defaultOption =
            `<option value="" data-name-en="Select a Category"
                data-name-ta="வகையைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'வகையைத் தேர்ந்தெடுக்கவும்' : 'Select a Category'}
        </option>`;

        $categoryDropdown.html(defaultOption);

        // Make AJAX call to fetch categories
        $.ajax({
            url: '/get-categories',
            type: 'POST',
            data: {
                deptcode: deptcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const data = Array.isArray(response) ? response : response.data;

                if (Array.isArray(data)) {
                    data.forEach(({
                        catcode: code,
                        catename: name,
                        cattname: taname,
                        if_subcategory: if_subcat
                    }) => {
                        if (code && name) {
                            const isSelected = (code == selectedCatcode) ? "selected" : "";
                            $categoryDropdown.append(
                                `<option value="${code}" ${isSelected}
                                data-name-en="${name}"
                                data-name-ta="${taname || name}"
                                data-if_subcategory = "${if_subcat}">
                                ${name}
                            </option>`
                            );
                        }
                    });

                    if (subcategoryFlag == 'Y') {
                        $('#subcategorydiv').show();
                        $('#subcatid').attr('required', true);
                        $('#subcatid').rules('add', {
                            required: true,
                            messages: {
                                required: function() {
                                    const lang = getLanguage();
                                    return lang === 'ta' ? 'வகையைத் தேர்ந்தெடுக்கவும்' :
                                        'Select a  Sub Category';
                                }
                            }
                        });
                    } else {

                        $('#subcategorydiv').css('display', 'none');

                        $('#subcatid').val('');

                        // Remove required validation when hidden
                        $('#subcatid').rules('remove', 'required');
                        $('#subcatid').removeAttr('required');
                    }


                    // Re-set the selected value after options are loaded
                    if (selectedCatcode) {
                        setTimeout(function() {
                            $categoryDropdown.select2().val(selectedCatcode);
                            // //updateSelectColorByValue([$categoryDropdown[0]]);
                        }, 100);
                    }
                    if (selectedsubcat) {
                        setTimeout(function() {

                            onchange_cat(selectedCatcode, null, selectedsubcat)
                        }, 300);
                    } else {
                        $('#subcategorydiv').css('display', 'none');
                        $('#subcatid').val('');

                        // Remove required validation when hidden
                        $('#subcatid').rules('remove', 'required');
                        $('#subcatid').removeAttr('required');
                    }

                    change_lang_for_page(getLanguage());
                } else {
                    console.error("Invalid response:", response);
                }
            },
            error: function(xhr) {
                console.error("Error loading categories:", xhr);
                passing_alert_value('Alert', 'Error loading categories', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
        });
    }

    // Keep the original loadCategories function for non-edit scenarios
    function loadCategories(deptcode, selectedCatcode = '', subcategoryFlag, selectedsubcat) {
        return loadCategoriesForEdit(deptcode, selectedCatcode, subcategoryFlag, selectedsubcat);
    }


    function onchange_cat(catcode, if_subcategory, selectedsubcatcode) {

        const lang = getLanguage();
        const subcategoryDropdown = $('#subcatid');
        const selectedOption = $('#catcode option:selected');

        if (!selectedOption.length) return;
        var if_subcategory_val =
            if_subcategory || selectedOption.data('if_subcategory') || 'N';
        var catval = catcode || $('#catcode').val();
        const defaultOption =
            `<option value="" data-name-en="Select a Sub Category"
                data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select a Sub Category'}
        </option>`;
        subcategoryDropdown.html(defaultOption);

        // Make AJAX call to fetch categories
        if (if_subcategory_val == 'Y') {
            $.ajax({
                url: '/masters/FilterByDept',
                type: 'POST',
                data: {
                    catcode: catval,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const data = Array.isArray(response) ? response : response.subcategory;

                    if (Array.isArray(data)) {
                        data.forEach(({
                            auditeeins_subcategoryid: code,
                            subcatename: name,
                            subcattname: taname,

                        }) => {
                            if (code && name) {
                                const isSelected = (code == selectedsubcatcode) ? "selected" : "";
                                subcategoryDropdown.append(
                                    `<option value="${code}" ${isSelected}
                                data-name-en="${name}"
                                data-name-ta="${taname || name}"
                               >
                                ${name}
                            </option>`
                                );
                            }
                        });





                        change_lang_for_page(getLanguage());
                    } else {
                        console.error("Invalid response:", response);
                    }
                },
                error: function(xhr) {
                    console.error("Error loading categories:", xhr);
                    passing_alert_value('Alert', 'Error loading categories', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                }
            });
        } else {
            $('#subcategorydiv').css('display', 'none');
            $('#subcatid').val('');

            // Remove required validation when hidden
            $('#subcatid').rules('remove', 'required');
            $('#subcatid').removeAttr('required');
        }

    }
</script>
@endsection
