@section('content')
@section('title', 'Assign Charge Table Report')

    @extends('index2')
    @include('common.alert')
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


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

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_deptdiv_show = $deptcode ? '' : 'hide_this';
        $make_region_disable = $regioncode ? 'disabled' : '';
        $make_regiondiv_disable = $regioncode ? '' : 'hide_this';
        $make_district_disable = $distcode ? 'disabled' : '';
        $make_districtdiv_disable = $distcode ? '' : 'hide_this';
    @endphp

    <?php
    use Carbon\Carbon;

    // Get today's date in dd/mm/yy format
    $today = Carbon::today()->format('d/m/y');

    ?>
<!--
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
            line-height: 36px !important;
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
    </style> -->



    <div class="row">
        <div class="col-12">

            <div class="card card_border">
                <div class="card-header card_header_color lang" key="assignCharge_head">Assign Charge</div>

                <div class="card-body">
                    <form id="assignchargeform" name="assignchargeform">
                        <input type="hidden" value="assigncharge" id="page" name="page">
                        <div class="row">
                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" for="dept" key="department">Department</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    onchange="getroletypecode_basedondept('')" <?php echo $make_dept_disable; ?>>
                                    <option value="" data-name-en=" Select Department"
                                        data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="roletypecode" key="roletype">Role Type</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="roletypecode"
                                    name="roletypecode"onchange="settingform_basedonroletypcode('','','','','')">
                                    <option value=''
                                        data-name-en="Select Role Type"data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">Select
                                        Role Type</option>

                                </select>

                            </div>

                            <div class="col-md-4 mb-3 <?php echo $make_regiondiv_disable; ?>" id="regiondiv">
                                <label class="form-label required lang" for="validationDefault01" key="region">Region
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="regioncode" name="regioncode"
                                    onchange="getvaluebasedon_regionroletype('', '', '', '', '')" <?php echo $make_region_disable; ?>>
                                    <option value="" data-name-en="Select a Region"
                                        data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">Select Region</option>
                                    @if ($regiondetails)
                                        {
                                        <option value="{{ $regiondetails['regioncode'] }}" selected>
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
                                        <option value="{{ $distdetails['distcode'] }}" selected>
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
                                    name="instmappingcode" onchange="getdesignation_chargedet()">
                                    <option value="" data-name-en="Select a Institution"
                                        data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefault01"
                                    key="designation">Designation </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="desigcode" name="desigcode"
                                    onchange="getchargedescription()">
                                    <option value='' data-name-en="Select Designation"
                                        data-name-ta="பதவியைத் தேர்ந்தெடுக்கவும்">Select Designation</option>
                                    @if (!empty($designation) && is_iterable($designation))
                                        @foreach ($designation as $department)
                                            <option value="{{ $department->desigcode }}">
                                                {{ $department->desigelname }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefault02" key="charge">Charge
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="chargeid" name="chargeid">
                                    <option value='' data-name-en="Select Charge"
                                        data-name-ta="பொறுப்பை தேர்வு செய்யவும்">Select Charge</option>


                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefaultUsername"
                                    key="user">User</label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="userid" name="userid" multiple="multiple">
                                    <!-- <option value='' data-name-en="Select User"
                                        data-name-ta="பயனரை தேர்வு செய்யவும்">Select User</option> -->
                                </select>

                            </div>



                            <div class="col-md-4 mb-3">
                                <label class="form-label lang" for="validationDefaultUsername"
                                    key="chargeFromDate">Charge From Date</label>
                                <input type="text" class="form-control" id ="cod" name="cod"
                                    value="<?php echo $today; ?>" disabled />
                            </div>

                        </div>

                        <div class="col-md-3 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>



                    </form>

                    <div id='hideform' class='hide_this'>
                        <center>No Data Available</center>
                    </div>

                </div>
            </div>


            <div class="card card_border">
                <div class="card-header card_header_color lang" key="assignCharge_details">Assigned User Charge Details
                </div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="userchargetable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang text-center" key="s_no" style="width:5%">S.No</th>
                                        <th class="text-center lang" style="width:25%" key="deptDetails">Department
                                            Details</th>
                                        <th class="text-center lang" style="width:9%" key="roletype">Roletype</th>
                                        <!-- <th>Region</th> -->
                                        <!-- <th>District</th>
                                                <th>Institution</th> -->
                                        <th class="text-center lang" style="width:9%" key="roleaction">RoleAction</th>
                                        <th class="text-center lang" style="width:12%" key="designation">Designation</th>
                                        <th class="text-center lang" style="width:12%" key="chargeDescription">Charge
                                            Description</th>
                                        <th class="text-center lang" style="width:25%" key="userdetails">User Details
                                        </th>

                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='no_data' class='hide_this'>
                        <center>No Data Available</center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

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

    <!-- -----------------------download button------------------- -->

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        get_assignchargevalue()

        var session_roletypecode = '<?php echo $roleTypeCode; ?>';


        function get_assignchargevalue() {
            $.ajax({
                url: '/get_assignchargevalue',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const {
                            dept,
                            roletype
                        } = response.data;




                        // Check if dept and roletype are arrays
                        if (Array.isArray(dept)) {
                            // alert(`Dept Count: ${dept.length}, RoleType Count: ${roletype.length}`);

                            if (dept.length > 0) {
                                // Check Role Type condition
                                if (
                                    session_roletypecode === '<?php echo $dga_roletypecode; ?>' ||
                                    session_roletypecode === '<?php echo $Admin_roletypecode; ?>'
                                ) {
                                    // Populate dept dropdown
                                    makedropdownempty('deptcode', 'Select Department');
                                    let deptOptions =
                                        "<option value='' data-name-en='Select Department'  data-name-ta='துறையைத் தேர்ந்தெடுக்கவும்'>Select Department</option>";

                                    dept.forEach(({
                                        deptcode: code,
                                        deptelname: name,
                                        depttlname: taname
                                    }) => {
                                        if (code && name) {
                                            deptOptions +=
                                                `<option value="${code}" data-name-en="${name}" data-name-ta="${taname || name}">${name}</option>`;
                                        }
                                    });
                                    $("#deptcode").html(deptOptions);


                                } else {
                                    session_deptcode = '<?php echo $deptcode; ?>';
                                    // Populate dept dropdown
                                    makedropdownempty('deptcode', 'Select Department');
                                    let deptOptions = "";

                                    dept.forEach(({
                                        deptcode: code,
                                        deptelname: name
                                    }) => {
                                        if (code && name) {
                                            const isSelected = (code === session_deptcode) ?
                                                "selected" : "";
                                            deptOptions += `<option value="${code}">${name}</option>`;
                                        }
                                    });

                                    $("#deptcode").html(deptOptions);


                                    // Populate roletype dropdown
                                    makedropdownempty('roletype', 'Select Role Type');
                                    let roleTypeOptions =
                                        "<option value='' data-name-en='Select Role Type' data-name-ta='களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�'>Select Role Type</option>";

                                    roletype.forEach(({
                                        roletypecode: code,
                                        roletypeelname: name
                                    }) => {
                                        if (code && name) {
                                            roleTypeOptions +=
                                                `<option value="${code}">${name}</option>`;
                                        }
                                    });
                                    $("#roletypecode").html(roleTypeOptions);
                                }

                                // Show or hide forms based on conditions
                                $('#hideform').hide();
                                $('#assignchargeform').show();
                            } else {
                                $('#hideform').show();
                                $('#assignchargeform').hide();
                            }
                        } else {
                            console.error("Invalid data format: 'dept' or 'roletype' is not an array.");
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    let errorMessage = 'An error occurred. Please try again.';

                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value(
                        'Alert',
                        errorMessage,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            });
        }


        var session_roletypecode = '<?php echo $roleTypeCode; ?>';



        // function getroletypecode_basedondept(deptcode, roletypecode) {
        //     const defaultOption = "<option value=''>Select Role Type</option>";
        //     const $dropdown = $("#roletypecode");

        //     // Get department code from DOM if not passed
        //     if (!deptcode) deptcode = $('#deptcode').val();

        //     if (deptcode) {
        //         // Clear the dropdown and set the default option
        //         $dropdown.html(defaultOption);



        //     } else {
        //         // Reset to default option if no department code is provided
        //         $dropdown.html(defaultOption);
        //     }
        // }


        function getroletypecode_basedondept(deptcode, roletypecode) {
            const lang = getLanguage();

            const defaultOption = `<option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">
                        ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�' : 'Select Role Type'}
                    </option>`;


            const $dropdown = $("#roletypecode");

            //getDesignationBasedonDept(deptcode, roletypecode,'assigncharge','deptcode','desigcode');

            // Get department code from DOM if not passed
            if (!deptcode) deptcode = $('#deptcode').val();

            if (deptcode) {
                // Clear the dropdown and set the default option
                $dropdown.html(defaultOption);

                $.ajax({
                    url: '/getroletypecode_basedondept',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        'page': 'assigncharge',
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
                                    options +=
                                        `<option value="${code}" ${isSelected} data-name-en="${name}" data-name-ta="${nameTa || name}">${name}</option>`;
                                }
                            });

                            // Append the options to the dropdown
                            $dropdown.html(options);
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
        }


        function settingform_basedonroletypcode(roletypecode, deptcode, regioncode, distcode, instmappingcode) {

            if (!roletypecode) roletypecode = $('#roletypecode').val();

            if (!deptcode) deptcode = $('#deptcode').val();

            // if ((roletypecode) && ((roletypecode == '<?php echo $Dist_roletypecode; ?>') || (roletypecode == '<?php echo $Re_roletypecode; ?>')))
            // {
            //     if((session_roletypecode == '<?php echo $dga_roletypecode; ?>')||(session_roletypecode == '<?php echo $Ho_roletypecode; ?>'))
            //     {
            //         makedropdownempty('regioncode', 'Select Region')
            //         getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region', 'regioncode')
            //         $('#regiondiv').show();

            //         makedropdownempty('instmappingcode', 'Select Insittuion')
            //         $('#instdiv').show();

            //         if ((roletypecode == '<?php echo $Dist_roletypecode; ?>')) {
            //             // getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region')
            //             makedropdownempty('distcode', 'Select District')
            //             $('#distdiv').show();
            //         } else {
            //             $('#distdiv').hide();
            //         }
            //     }

            //     if((session_roletypecode == '<?php echo $Dist_roletypecode; ?>')|| (session_roletypecode == '<?php echo $Re_roletypecode; ?>'))
            //     {
            //         makedropdownempty('instmappingcode', 'Select Insittuion')
            //         $('#instdiv').show();

            //         if((session_roletypecode == '<?php echo $Re_roletypecode; ?>'))
            //         {
            //             if(roletypecode == '<?php echo $Re_roletypecode; ?>')
            //             {
            //                 getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode','')
            //             }
            //             else
            //             {
            //                 getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'district', 'distcode')
            //             }
            //         }

            //         }
            //         if((session_roletypecode == '<?php echo $Dist_roletypecode; ?>'))
            //         {
            //             makedropdownempty('instmappingcode', 'Select Insittuion')
            //             $('#instdiv').show();
            //             getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode','')
            //         }
            // }
            // else {
            //     $('#distdiv').hide();
            //     $('#regiondiv').hide();
            //     $('#instdiv').hide();
            // }

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
                        if (!regioncode) regioncode = $('#regioncode').val();
                        getdistregioninst_basedondept(
                            roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode', instmappingcode
                        );
                    }
                }
            } else {
                $('#distdiv').hide();
                $('#regiondiv').hide();
                $('#instdiv').hide();
                getdesignation_chargedet();

                makedropdownempty('desigcode', 'Select Designation');
                makedropdownempty('chargeid', 'Select Charge Description');
                makedropdownempty('userid', 'Select User');
            }



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

        // function getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,instmappingcode)
        // {
        //     if(valuefor == 'institution')
        //     {
        //         if (!roletypecode) roletypecode = $('#roletypecode').val();
        //         if (!deptcode) deptcode = $('#deptcode').val();
        //         if (!regioncode) regioncode = $('#regioncode').val();
        //         if (!distcode) distcode = $('#distcode').val();
        //     }

        //     const $dropdown = $("#" + valueforid);

        //     // Clear existing options and display a "Select" placeholder
        //     $dropdown.html('<option value="">Select</option>');

        //     if(roletypecode && (((valuefor != 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode)) || ((valuefor != 'institution') && (roletypecode == '<?php echo $Dist_roletypecode; ?>') && (deptcode) && (regioncode))
        //         && ((valuefor == 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode) && (regioncode) ) || ((valuefor == 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode) && (regioncode) && (distcode))
        //     ))
        //     {
        //         // Make the AJAX request
        //         $.ajax({
        //         url: '/getRegionDistInstBasedOnDept',
        //         type: 'POST',
        //         data: {
        //             roletypecode,
        //             deptcode,
        //             regioncode,
        //             distcode,
        //             valuefor,
        //             page : 'assigncharge',
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
        //             updateSelectColorByValue(document.querySelectorAll(".form-select"));
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
                    placeholderTextTa = 'பகுதியை தேர்ந்தெடுக்கவும்';
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

            $dropdown.html(`<option value="" data-name-en="${placeholderTextEn}" data-name-ta="${placeholderTextTa}">
                        ${lang === 'ta' ? placeholderTextTa : placeholderTextEn}
                    </option>`);

            // Simplify conditions for clarity
            const isValid = roletypecode && (
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
                        page: 'assigncharge',
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            // Map response data to options
                            const options = response.data.map(item => {
                                switch (valuefor) {
                                    case 'region':
                                        return `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}" ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
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
                        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
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
                makedropdownempty('desigcode', 'Select Designation');
                makedropdownempty('chargeid', 'Select Charge Description');
                makedropdownempty('userid', 'Select User');

            }
        }


        function getdesignation_chargedet() {
            var lang = getLanguage();


            const deptcode = $('#deptcode').val();
            const roletypecode = $('#roletypecode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();
            const instmappingcode = $('#instmappingcode').val();
            const $dropdown = $('#desigcode'); // Assuming the dropdown ID is `desigcode`

            // Clear dropdown and show loading
            // $dropdown.html('<option value="">Select</option>');


            // Validation conditions
            const isValid = roletypecode && (
                (roletypecode === '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
                (roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode) ||
                (roletypecode === '<?php echo $Ho_roletypecode; ?>' && deptcode)
            );

            if (isValid) {
                $.ajax({
                    url: '/getdesignation_fromchargedet',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        roletypecode: roletypecode,
                        regioncode: regioncode,
                        distcode: distcode,
                        instmappingcode: instmappingcode,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        $dropdown.empty(); // Clear previous options
                        $dropdown.html(
                            `<option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பதவியை தேர்ந்தெடுக்கவும்' : 'Select Designation'}</option>`
                            );

                        if (response.success && Array.isArray(response.data)) {
                            // Map response data to dropdown options
                            const options = response.data.map(item =>
                                `<option value="${item.desigcode}" data-name-en="${item.desigelname}" data-name-ta="${item.desigtlname}">${item.desigelname}</option>`
                            ).join('');

                            $dropdown.append(options || '<option value="">No data available</option>');
                        } else {
                            console.error("Invalid response or no data:", response);
                            $dropdown.append('<option value="">No data available</option>');
                        }

                        // Update select color
                        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
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
                // $dropdown.html('<option value="">Invalid parameters</option>');
            }
        }



        function getchargedescription() {
            const deptcode = $('#deptcode').val();
            const roletypecode = $('#roletypecode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();
            const instmappingcode = $('#instmappingcode').val();
            const desigcode = $('#desigcode').val();
            const $dropdown = $('#chargeid'); // Assuming the dropdown ID is `desigcode`

            // Clear the dropdown before making the AJAX call
            $dropdown.html(
                `<option value='' data-name-en='Loading...' data-name-ta='ஏற்றுகிறது...'>${lang === 'ta' ? 'ஏற்றுகிறது...' : 'Loading...'}</option>`
                );
            if ((roletypecode) && ((roletypecode == '<?php echo $Ho_roletypecode; ?>' && deptcode) ||
                    (roletypecode == '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
                    (roletypecode == '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode)) &&
                desigcode) {
                $.ajax({
                    url: '/getchargedescription',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        roletypecode: roletypecode,
                        regioncode: regioncode,
                        distcode: distcode,
                        instmappingcode: instmappingcode,
                        desigcode: desigcode,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        const lang = getLanguage();
                        $dropdown.empty(); // Clear previous options
                        $dropdown.html(
                            `<option value='' data-name-en='Select Charge' data-name-ta='பொறுப்பை தேர்வு செய்யவும்'>${lang === 'ta' ? 'பொறுப்பை தேர்வு செய்யவும்' : 'Select Charge'}</option>`
                            );

                        if (response.success && Array.isArray(response.data)) {
                            // Map the response data into <option> elements
                            const options = response.data.map(item => {
                                return `<option value="${item.chargeid}">${item.chargedescription}</option>`;
                            }).join('');

                            // Append options to the dropdown
                            $dropdown.append(options || '<option value="">No data available</option>');
                        } else {
                            console.error("Invalid response or no data:", response);
                            $dropdown.append('<option value="">No data available</option>');
                        }

                        // Update select color
                        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
                    },
                    error: function(xhr) {
                        console.error("Error during AJAX request:", xhr);

                        // Show error message and reset dropdown
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
            }

            getuserbasedonroletype()
        }


        function getuserbasedonroletype() {
            const desigcode = $('#desigcode').val();
            const distcode = $('#distcode').val();
            const roletypecode = $('#roletypecode').val();
            const regioncode = $('#regioncode').val();
            const $dropdown = $('#userid'); // Assuming the dropdown ID is `desigcode`
            const deptcode = $('#deptcode').val();

            if ((roletypecode) && ((roletypecode == '<?php echo $Ho_roletypecode; ?>' && deptcode) ||
                    (roletypecode == '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
                    (roletypecode == '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode)) &&
                desigcode)

            {
                // Clear the dropdown before making the AJAX call
                $dropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: '/getuserbasedonroletype',
                    type: 'POST',
                    data: {
                        desigcode: desigcode,
                        distcode: distcode,
                        roletypecode: roletypecode,
                        regioncode: regioncode,
 			deptcode    :   deptcode,
                        'page': 'assigncharge',
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {

                        // console.log('hi');

                        // console.log(response);
                        const lang = getLanguage();

                        //console.log(response);

                        $dropdown.empty(); // Clear previous options
                        $dropdown.html(`<option value="" data-name-en="Select User" data-name-ta="பயனரை தேர்ந்தெடுக்கவும்">
                            ${lang === "ta" ? "பயனரை தேர்ந்தெடுக்கவும்" : "Select User"}
                        </option>`);

                        console.log(response.data);

                        if (response.success && Array.isArray(response.data) && response.data.length > 0) {

                            // Map the response data into <option> elements
                            const options = response.data.map(item => {
                                return `<option value="${item.deptuserid}" data-name-en="${item.username}" data-name-ta="${item.usertamilname}">${item.username}</option>`;
                            }).join('');

                            // Append options to the dropdown
                            //  $dropdown.append(options || '<option value="">No data available</option>');
                            $dropdown.append(options);

                            $('#userid').select2({
                                placeholder: "Select Team Member",
                                allowClear: true
                            });


                        } else {
                           // console.log(response);
                            // console.error("Invalid response or no data:", response);
                            // $dropdown.append('<option value="">No data available</option>');

                            getLabels_jsonlayout([{
                                    id: 'AssignChargeDesig',
                                    key: 'AssignChargeDesig'
                                }], 'N').then((text) => {
                                    passing_alert_value('Confirmation', text.AssignChargeDesig, 'confirmation_alert',
                                        'alert_header', 'alert_body',
                                        'confirmation_alert');
                                });

                            // try {
                            // var response = JSON.parse(xhr.responseText);
                            // var errorMessage = response.message || "An unknown error occurred";
                            // } catch (e) {
                            //     var errorMessage = "Unexpected error occurred";
                            // }

                            // getLabels_jsonlayout([{ id: errorMessage, key: errorMessage }], "N")
                            //     .then((text) => {
                            //         passing_alert_value("Confirmation", Object.values(text)[0],
                            //             "confirmation_alert", "alert_header", "alert_body",
                            //             "confirmation_alert");
                            // });


                        }

                        // Update select color
                        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
                    },
                    error: function(xhr) {
                        console.error("Error during AJAX request:", xhr);

                        // Show error message and reset dropdown
                        $dropdown.html('<option value="">Error loading data</option>');
                        passing_alert_value(
                            'Alert',
                            'Error loading data. Please try again.',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                        // try {
                        // var response = JSON.parse(xhr.responseText);
                        // var errorMessage = response.message || "An unknown error occurred";
                        // } catch (e) {
                        //     var errorMessage = "Unexpected error occurred";
                        // }

                        // getLabels_jsonlayout([{ id: errorMessage, key: errorMessage }], "N")
                        //     .then((text) => {
                        //         passing_alert_value("Confirmation", Object.values(text)[0],
                        //             "confirmation_alert", "alert_header", "alert_body",
                        //             "confirmation_alert");
                        //     });
                    }
                });
            }


        }

        ///////////////////////////////////////  Fetching Data ///////////////////////////////////////

        $(document).ready(function() {
            $('#assignchargeform')[0].reset();
            // updateSelectColorByValue(document.querySelectorAll(".form-select"));

            // Load the initial language and initialize the DataTable
            const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en' if no language is set
            initializeDataTable(lang);

            // Change event for language selection dropdown
            $('#translate').change(function() {
                var lang = getLanguage('Y');
                changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                    @json($updatebtn), @json($clearbtn));

                updateTableLanguage(lang);
                updateValidationMessages(getLanguage('Y'), 'assignchargeform');
            });
        });


        function initializeDataTable(language) {
            $.ajax({
                url: "/fetchuserchargeData",
                type: "POST",
                data: {
                    page: 'assigncharge'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                }
            });
        }



        // function renderTable(language) {
        //     const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        //     const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        //     const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        //     const distColumn = language === 'ta' ? 'disttname' : 'distename';
        //     const instcolumn = language === 'ta' ? 'insttname' : 'instename';
        //     const roletypecolumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        //     const roleactioncolumn = language === 'ta' ? 'roleactiontlname' : 'roleactionelname';







        //     if ($.fn.DataTable.isDataTable('#userchargetable')) {
        //         $('#userchargetable').DataTable().clear().destroy();
        //     }

        //     table = $('#userchargetable').DataTable({
        //         "processing": true,
        //         "serverSide": false,
        //         "lengthChange": false,
        //         "data": dataFromServer,
        //         columns: [{
        //             data: null,
        //             render: (_, __, ___, meta) => meta.row + 1, // Serial number column
        //             className: 'text-end' // Align to the right
        //         },
        //         // {
        //         //     data: "deptesname"
        //         // },
        //         {
        //            "data": departmentColumn,
        //             "render": function(data, type, row) {
        //                 // Initialize the text variable
        //                 let text = "";

        //                 // Convert data into a formatted string
        //                 if (data) {
        //                     text += `<b>Department :</b> ${data}`;
        //                 }
        //                 if (row.regionename) {
        //                     text += `<br><b>Region :</b> ${row.regionColumn}`;
        //                 }
        //                 if (row.distename) {
        //                     text += `<br><b>District :</b> ${row.distColumn}`;
        //                 }
        //                 if (row.instename) {
        //                     text += `<br><b>Institution :</b> ${row.instcolumn}`;
        //                 }

        //                 // Return the constructed HTML
        //                 return text;
        //             }
        //         },
        //         {
        //             data: roletypecolumn
        //         },
        //         // {
        //         //    "data": "regionename",
        //         //     "render": function(data, type, row) {
        //         //         // Initialize the text variable
        //         //         let text = "";

        //         //         // Convert data into a formatted string
        //         //         if (data) {
        //         //             text = `<b>Region :</b> ${data}`;
        //         //         }
        //         //         if (row.distename) {
        //         //             text += `<br><b>District :</b> ${row.distename}`;
        //         //         }
        //         //         if (row.instename) {
        //         //             text += `<br><b>Institution :</b> ${row.instename}`;
        //         //         }

        //         //         // Return the constructed HTML
        //         //         return text;
        //         //     }


        //         // {
        //         //     data: "distename"
        //         // },
        //         // {
        //         //     data: "instename"
        //         // },
        //         {
        //             data: roleactioncolumn
        //         },
        //         {
        //             data: designationColumn
        //         },
        //         {
        //             data: "chargedescription"
        //         },
        //         {
        //             "data": "username",
        //             "render": function(data, type, row) {
        //                 // Convert DOB to dd-mm-yyyy format
        //                 return `<b>Name :</b> ${data} <br> <small><b>IFHRMS No : </b>${row.ifhrmsno}</small> <br> <small><b>Email :</b> ${row.email}</small>`;
        //             }
        //         }
        //     ]
        //     });
        // }
      
        function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const institutionColumn = language === 'ta' ? 'insttname' : 'instename';
        const roletypeColumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        const roleactionColumn = language === 'ta' ? 'roleactiontlname' : 'roleactionelname';
                // Destroy and reinitialize DataTable if it's already initialized
                if ($.fn.DataTable.isDataTable('#userchargetable')) {
                    $('#userchargetable').DataTable().clear().destroy();
                }

                // Initialize the DataTable
                table = $('#userchargetable').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    data: dataFromServer,
                    columns: [
                                {
                                    data: null,
                                    render: function (data, type, row, meta) {
                                        return `<div>
                                                    <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                                                </div>`;
                                    },
                                    className: 'text-end',
                                    type: "num"
                                },
                                {
    data: departmentColumn, // Dynamically chosen column name
    render: function (data, type, row) {
        // ✅ Debugging: Check what keys exist in the row
      //  console.log("Row Data:", row);

        // ✅ Define translations dynamically
        const translations = {
            en: { 
                department: "Department", 
                region: "Region", 
                district: "District", 
                institution: "Institution" 
            },
            ta: { 
                department: "துறை", 
                region: "மண்டலம்", 
                district: "மாவட்டம்", 
                institution: "நிறுவனம்" 
            }
        };

        // ✅ Ensure the correct language selection
        const lang = language === "ta" ? "ta" : "en"; 

        // ✅ Fix: Check if Tamil column exists, else fallback to English column
        let departmentValue = row[departmentColumn] || row['deptesname'] || "-";
        let regionValue = row[regionColumn] || row['regionename'] || "-";
        let districtValue = row[districtColumn] || row['distename'] || "-";
        let institutionValue = row[institutionColumn] || row['instename'] || "-";

        return `<b>${translations[lang].department}:</b> ${departmentValue} <br> 
                <small><b>${translations[lang].region}:</b> ${regionValue}</small> <br> 
                <small><b>${translations[lang].district}:</b> ${districtValue}</small> <br> 
                <small><b>${translations[lang].institution}:</b> ${institutionValue}</small>`;
    },
    className: 'text-wrap text-start'
},

                {
                    data: roletypeColumn,
                    title: columnLabels?.[roletypeColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[roletypeColumn] || '-';
                    }
                },
                {
                    data: roleactionColumn,
                    title: columnLabels?.[roleactionColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[roleactionColumn] || '-';
                    }
                },
                {
                    data: designationColumn,
                    title: columnLabels?.[designationColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[designationColumn] || '-';
                    }
                },
                {
                    data: "chargedescription",
                    title: columnLabels?.["chargedescription"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row.chargedescription || '-';
                    }
                },
                {
                    data: "username",
                    render: function (data, type, row) {
                        let dob = row.dob ? new Date(row.dob).toLocaleDateString('en-GB') : "N/A";

                        // Define translations based on language selection
                        const translations = {
                            en: { name: "Name", ifhrmsno: "IFHRMS No", email: "Email" },
                            ta: { name: "பெயர்", ifhrmsno: "IFHRMS எண்", email: "மின்னஞ்சல் முகவரி" }
                        };

                        const lang = language === "ta" ? "ta" : "en"; // Ensure fallback to English if not Tamil

                        return `<b>${translations[lang].name}</b>: ${data} <br> 
                                <small><b>${translations[lang].ifhrmsno} : </b>${row.ifhrmsno}</small> <br> 
                                <small><b>${translations[lang].email} :</b> ${row.email}</small>`;
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                }
            ],
                "columnDefs": [{
                        render: function(data) {
                            return "<div class='text-wrap width-200'>" + data + "</div>";
                        },
                        targets: 1
                    },
                    {
                        render: function(data) {
                            return "<div class='text-wrap width-100'>" + data + "</div>";
                        },
                        targets: [2, 3] // Set width for columns
                    }
                ],
                });
                const mobileColumns = [
            roletypeColumn,
            roleactionColumn,
            designationColumn,
            "chargedescription",
            "username",
            "ifhrmsno",
            "email"
        ];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "userchargetable"); 
            }
          function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ✅ Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);
    // ✅ Fetch column headers from JSON layout
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    // ✅ Column Mapping (for language-specific keys)
    const columnMap = {
        department: language === 'ta' ? 'depttsname' : 'deptesname',
        region: language === 'ta' ? 'regiontname' : 'regionename',
        district: language === 'ta' ? 'disttname' : 'distename',
        institution: language === 'ta' ? 'insttname' : 'instename',
        designation: language === 'ta' ? 'desigtsname' : 'desigesname',
        chargedescription: "chargedescription", // Fixed mapping issue
        username: "username",
        ifhrmsno: "ifhrmsno",
        email: "email",
        roletype: language === 'ta' ? 'roletypetlname' : 'roletypeelname',
        roleaction: language === 'ta' ? 'roleactiontlname' : 'roleactionelname'

    };

    // ✅ Define Headers Properly
    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["Region"] || "Region", key: "region" },
        { header: dtText["District"] || "District", key: "district" },
        { header: dtText["Institution"] || "Institution", key: "Institution" },
        { header: dtText["designation"] || "Designation", key: "designation" },
        { header: dtText["chargedescription"] || "Charge Description", key: "chargedescription" },
        { header: dtText["username"] || "Name", key: "username" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },
        { header: dtText["Email"] || "Email", key: "email" },
        { header: dtText["roletype"] || "Role Type", key: "roletype" },
        { header: dtText["roleaction"] || "Role Action", key: "roleaction" },
  	{ header: dtText["reservelist"] || "Whether include in Field audit", key: "reservelist" }
    ];

    // ✅ Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");
        let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        return {
            department: rowData[columnMap.department] || "-",
            region: rowData[columnMap.region] || "-",
            district: rowData[columnMap.district] || "-",
            institution: rowData[columnMap.institution] || "-",
            designation: rowData[columnMap.designation] || "-",
            chargedescription: rowData[columnMap.chargedescription] || "-",
            username: rowData[columnMap.username] || "-",
            ifhrmsno: rowData[columnMap.ifhrmsno] || "-",
            email: rowData[columnMap.email] || "-",
            roletype: rowData[columnMap.roletype] || "-",
            roleaction: rowData[columnMap.roleaction] || "-",
 	    reservelist: rowData.reservelist === 'Y' ? 'Yes' : rowData.reservelist === 'N' ? 'No' : "-"
        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // ✅ Create Workbook and Worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add Headers in Separate Columns (Avoid Merging Issues)
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    
    // ✅ Ensure Headers Align with Data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}_${language}.xlsx`);
}


// Utility functions to extract text & clean up data
function extractText(html) {
    return $("<div>").html(html).text().trim();
}

function extractIfhrms(html) {
    let match = html.match(/<small><b>IFHRMS No : <\/b>(\d+)<\/small>/);
    return match ? match[1] : "-";
}
        // function renderTable(language) {
        //     // Define column names based on the language
        //     const columns = {
        //         department: language === 'ta' ? 'depttsname' : 'deptesname',
        //         designation: language === 'ta' ? 'desigtsname' : 'desigesname',
        //         region: language === 'ta' ? 'regiontname' : 'regionename',
        //         district: language === 'ta' ? 'disttname' : 'distename',
        //         institution: language === 'ta' ? 'insttname' : 'instename',
        //         roletype: language === 'ta' ? 'roletypetlname' : 'roletypeelname',
        //         roleaction: language === 'ta' ? 'roleactiontlname' : 'roleactionelname'
        //     };

        //     // Destroy and reinitialize DataTable if it's already initialized
        //     if ($.fn.DataTable.isDataTable('#userchargetable')) {
        //         $('#userchargetable').DataTable().clear().destroy();
        //     }

        //     // Initialize the DataTable
        //     table = $('#userchargetable').DataTable({
        //         processing: true,
        //         serverSide: false,
        //         lengthChange: false,
        //         data: dataFromServer,
        //         columns: [{
        //                 data: null,
        //                 render: (_, __, ___, meta) => meta.row + 1, // Serial number column
        //                 className: 'text-end' // Align to the right
        //             },
        //             {
        //                 data: columns.department,
        //                 render: function(data, type, row) {
        //                     let text = data ? `<b>Department:</b> ${data}` : '';
        //                     if (row[columns.region]) {
        //                         text += `<br><b>Region:</b> ${row[columns.region]}`;
        //                     }
        //                     if (row[columns.district]) {
        //                         text += `<br><b>District:</b> ${row[columns.district]}`;
        //                     }
        //                     if (row[columns.institution]) {
        //                         text += `<br><b>Institution:</b> ${row[columns.institution]}`;
        //                     }
        //                     return text;
        //                 }
        //             },
        //             {
        //                 data: columns.roletype
        //             },
        //             {
        //                 data: columns.roleaction
        //             },
        //             {
        //                 data: columns.designation
        //             },
        //             {
        //                 data: "chargedescription"
        //             },
        //             {
        //                 data: "username",
        //                 render: function(data, type, row) {
        //                     return `<b>Name :</b> ${data} <br> <small><b>IFHRMS No : </b>${row.ifhrmsno}</small> <br> <small><b>Email :</b> ${row.email}</small>`;
        //                 }
        //             }
        //         ],
        //     "columnDefs": [{
        //             render: function(data) {
        //                 return "<div class='text-wrap width-200'>" + data + "</div>";
        //             },
        //             targets: 1
        //         },
        //         {
        //             render: function(data) {
        //                 return "<div class='text-wrap width-100'>" + data + "</div>";
        //             },
        //             targets: [2, 3] // Set width for columns
        //         }
        //     ],
        //     "initComplete": function(settings, json) {
        //         $("#assignchargeform").wrap(
        //             "<div style='overflow:hidden; width:100%;position:relative;'></div>");
        //     },
        //     "dom": '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
        //     "buttons": [{
        //         extend: "excelHtml5",
        //         text: window.innerWidth > 768 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
        //             '<i class="fas fa-download"></i>',
        //         title: 'Assign Charge Table Report',
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

        //     });
        // }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#usertable')) {
                $('#userchargetable').DataTable().clear().destroy();
            }
            renderTable(language);
        }

        ///////////////////////////////////////  Fetching Data ///////////////////////////////////////


        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#assignchargeform").validate({
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
                    chargeid: {
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
                    userid: {
                        required: true
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
                //     chargeid: {
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
                //     userid:{ required : "Select a user"}
                // }
            });






            $("#buttonaction").on("click", function(event) {
                // Prevent form submission (this stops the page from refreshing)
                event.preventDefault();

                //Trigger the form validation
                if ($("#assignchargeform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var formData = $('#assignchargeform').serializeArray();

                    var selecteduserid = $('#userid').val();  // This returns an array of selected values

                    // Filter out empty values (if any) from the selecteduserid array
                    selecteduserid = selecteduserid.filter(function(value) {
                        return value !== "";  // Filter out empty strings or null values
                    });

                    // If there are selected subwork allocations, push them into the formData
                    if (selecteduserid.length > 0) {
                        selecteduserid.forEach(function(value) {
                            formData.push({
                                name: 'userid[]',  // Use array notation for multiple values
                                value: value
                            });
                        });
                    }




                    $.ajax({
                        url: '/assigncharge_insertupdate', // URL where the form data will be posted
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
                                get_assignchargevalue();
                                const lang = window.localStorage.getItem('lang') ||
                                'en'; // Default to 'en' if no language is set
                                initializeDataTable(lang);


                            } else if (response.error) {
                                // Handle errors if needed
                                console.log(response.error);
                            }
                        },

                        error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);
                        if(response.error == 401)
                        {
                            handleUnauthorizedError();
                        }
                        else
                        {

                            getLabels_jsonlayout([{ id: response.message, key: response.message }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] || "Error Occured";
                                passing_alert_value('Confirmation', alertMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                            });
                        }

                }

            });
        }

            });


            //reset_form();

        }).catch(error => {
            console.error("Failed to load JSON data:", error);
        });


        function reset_form() {
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
            } else {
                $('#assignchargeform')[0].reset();
            }

          //  $('#deptcode').val('').trigger('change');

            $('#roletypecode').val('');
            $('#chargeid').val('');
            $('#desigcode').val('').trigger('change');;
            $('#userid').val('').trigger('change');

            $('#roletypecode').select2('destroy');

                // Clear the value
                $('#roletypecode').val(null);

                // Reinitialize the Select2 (no events will be triggered)
                $('#roletypecode').select2();


            // $('#roletypecode').val(null).trigger('change');
            // $('#chargeid').val(null).trigger('change');
            // $('#desigcode').val(null).trigger('change');


            // $('#roletypecode').html(`<option value="" data-name-en="Select Role Type" data-name-ta="பாத்திர வகை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பாத்திர வகையைத் தேர்ந்தெடுக்கவும்' : 'Select RoleType'}</option>`);

            // $('#distcode').html(`<option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}</option>`);

            $('#chargeid').val('').html(`
                <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'பகுதியை தேர்ந்தெடுக்கவும்' : 'Select Charge'}
                </option>
            `);


            // $('#regioncode').html(`<option value="" data-name-en="Select Region" data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பகுதியை தேர்ந்தெடுக்கவும்' : 'Select Region'}</option>`);

            // $('#desigcode').html(`<option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பதவியை தேர்ந்தெடுக்கவும்' : 'Select Designation'}</option>`);

            $('#display_error').hide();

            $('#assignchargeform').validate().resetForm();
            changeButtonAction('assignchargeform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert));
            //  change_button_as_insert('assignchargeform', 'action', 'buttonaction', 'display_error', '', '');
            // updateSelectColorByValue(document.querySelectorAll(".form-select"));

            if ($('#roletypecode').val() == "") {
                $('#distdiv').hide();
                $('#regiondiv').hide();
                $('#instdiv').hide();
            }
        }

        $(document).ready(function() {
            // Reset form and update select box colors on page load
            $('#assignchargeform')[0].reset();
            // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        });
    </script>


@endsection
