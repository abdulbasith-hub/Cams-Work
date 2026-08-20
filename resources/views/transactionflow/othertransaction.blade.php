@section('content')
    @extends('index2')
    @include('common.alert')
    <style>
        .card_seperator {
            height: 10px;
            border: 0;
            box-shadow: 0 10px 10px -10px #8c8b8b inset;
        }

        .card-title {
            font-size: 15px;
        }

        .title-part-padding {
            background-color: #e3efff;
        }

        .card-body {
            padding: 15px 10px;
        }

        .card {
            margin-bottom: 10px;
        }

        .dataTables_info {
            margin-bottom: 1rem !important;
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <?php

    ?>
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="user_trans">
                    User Transaction
                </div>
                <div class="card-body collapse show">
                    <form id="othertrans_form" name="othertrans_form">
                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                            id="display_error">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <input type="hidden" id="othertransid" name="othertransid" value="">
                         <input type="hidden" name="enuserid" value="{{ $ensessionuserid }}">

                        @csrf
                        <div class="card">
                            <div class="card-body card-border">
                                <div class="row">
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="department"
                                            for="validationDefault01">Department </label>
                                        <input type="hidden" id="" name="" value="">
                                        <select class="form-select mr-sm-2 lang-dropdown" id="deptcode" name="deptcode"

                                       onchange="getroletypecode_basedondept('','')">
                                            <!-- onchange="getdetailsbasedon_dept('','','','','','','') -->
                                            <option value="" data-name-en="---Select Department---"
                                                data-name-ta="--- ???????? ?????????????????---">---Select Department---
                                            </option>

                                            @foreach ($dept as $departtment)
                                                <option value="{{ $departtment->deptcode }}"
                                                    data-name-en="{{ $departtment->deptelname }}"
                                                    data-name-ta="{{ $departtment->depttlname }}">
                                                    {{ $departtment->deptelname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="roletype"
                                            for="validationDefault01">Role
                                            Type </label>
                                        <select class="form-select mr-sm-2 lang-dropdown" id="roletypecode"
                                            name="roletypecode" onchange="show_rolebaseddiv('')">
                                            <option value="" data-name-en="---Select Role Type---"
                                                data-name-ta="---????????????? ????????????????? ---">---Select Role Type---
                                            </option>

                                          
                                        </select>
                                    </div>
                                  
                                    <div class="col-md-3 mb-1 mt-2 region_div" id="region_div">
                                        <label class="form-label lang required" key="region"
                                            for="validationDefault01">Region
                                        </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="regioncode" name="regioncode"
                                            onchange="get_distinst('from','','','','','')">
                                            <option value="" data-name-en="---Select Region---"
                                                data-name-ta="---????????? ?????????????????---">---Select Region---
                                            </option>


                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2 hide_this dist_div" id="dist_div">
                                        <label class="form-label lang required" key="district"
                                            for="validationDefault01">District
                                        </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="distcode" name="distcode"
                                            onchange="getinst_data('from','','','')">
                                            <option value="" data-name-en="---Select District---"
                                                data-name-ta="--- ???????????? ?????????????????---">---Select District---
                                            </option>

                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="inst" for="validationDefault01">
                                            Institution </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="frominstmapcode"
                                            name="frominstmapcode" onchange="getdeptbased_desig('', '', 'desigcode','')">
                                            <option value="" data-name-en="---Select Institution---"
                                                data-name-ta="--- ???????? ?????????????????---">---Select Institution---
                                            </option>


                                        </select>
                                    </div>
                                    
                                    <!-- <div class="col-md-3 mb-1 mt-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <label class="form-label lang required" key="user" for="validationDefault01">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    User </label>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <select class="form-select mr-sm-2 lang-dropdown" id="frominstmappingcode"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    name="frominstmappingcode"  onchange="onchange_desigcode('','','')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="" data-name-en="---Select Institution---"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        data-name-ta="--- ?????????????????---">---Select User---
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </option>


                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div> -->

                                 
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="transaction"
                                            for="validationDefault01">
                                            Transaction </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="transtypecode"
                                            name="transtypecode" onchange="show_transtypeDiv(this)">
                                            <option value="" data-name-en="---Select Transaction---"
                                                data-name-ta="---??????????????? ?????????????????---">---Select
                                                Transaction---
                                            </option>

                                            @foreach ($trans_type as $transactiontype)
                                                <option value="{{ $transactiontype->transactiontypecode }}"
                                                    data-name-en="{{ $transactiontype->transactiontypelname }}"
                                                    data-name-ta="{{ $transactiontype->transactiontypelname }}">
                                                    {{ $transactiontype->transactiontypelname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="designation"
                                            for="validationDefault01">
                                            Designation </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="desigcode" name="desigcode"
                                            onchange="onchange_desigcode('','','')">
                                            <option value="" data-name-en="---Select Designation---"
                                                data-name-ta="--- ???????? ?????????????????---">---Select Designation---
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key="user" for="validationDefault01">
                                            User </label>

                                        <select class="form-select mr-sm-2 lang-dropdown" id="deptuserid" onchange="useronchange_superannuation()"
                                            name="deptuserid">
                                            <option value="" data-name-en="---Select User---"
                                                data-name-ta="--- ??????? ?????????????????---">---Select User---
                                            </option>


                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2 hide_this" id="dor_div">
                                        <label class="form-label lang required" key="dor" for="validationDefault01">
                                            Date of Retirement </label>

                                        <input type='date' class="form-control" value='' name='dor'
                                            id='dor' disabled>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-border">
                                <div class="row">

                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label lang required" key=""
                                            for="validationDefault02">Date</label>
                                        <div class="input-group" onclick="datepicker('order_date','')">
                                            <input type="text" class="form-control datepicker" id="order_date"
                                                name="order_date" placeholder="dd/mm/yyyy" />
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label required lang" for="validationDefault02" key="order_id">
                                            Order No
                                        </label>
					<span class="file_attach" style="color:#ff0000; font-size:10px;">(Max
                                                Size : 1
                                                MB &
                                                File
                                                Format
                                                : Pdf )</span>
                                        <input class="form-control only_numbers" id="orderno" name="orderno" maxlength="9"
                                            placeholder="Enter  Order" />
                                    </div>
                                    <div class="col-md-3 mb-1 mt-2">
                                        <label class="form-label required lang" for="validationDefault02"
                                            key="file_upload">
                                            Upload File
                                        </label>
				<input type="file" class="form-control" id="file" name="file" accept=".pdf">
                                        <input type="hidden" class="form-control" id="uploadid" name="uploadid">

                                        <div id="view_file-list-container">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="diversion_div" class="hide_this">
                            <div class="row">
                                <div class="col-md-3 mb-1 mt-2">
                                    <label class="form-label lang required" key="" for="validationDefault01">
                                        To Department </label>

                                    <select class="form-select mr-sm-2 lang-dropdown" id="dev_deptcode"
                                        name="dev_deptcode" onchange="onchange_devdept('','','','','','')">
                                        <option value="" data-name-en="---Select department---"
                                            data-name-ta="---??????????????? ?????????????????---">---Select
                                            Department---
                                        </option>
                                       @foreach ($todept as $departtment)
                                            <option value="{{ $departtment->deptcode }}"
                                                data-name-en="{{ $departtment->deptelname }}"
                                                data-name-ta="{{ $departtment->depttlname }}">
                                                {{ $departtment->deptelname }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-3 mb-1 mt-2 region_div">
                                    <label class="form-label lang required" key="" for="validationDefault01">
                                        To Region </label>

                                    <select class="form-select mr-sm-2 lang-dropdown" id="div_region" name="div_region"
                                        onchange="get_distinst('to','','','','','')">
                                        <option value="" data-name-en="---Select Region---"
                                            data-name-ta="---??????????????? ?????????????????---">---Select
                                            Region---
                                        </option>


                                    </select>
                                </div>
                                <div class="col-md-3 mb-1 mt-2 dist_div">
                                    <label class="form-label lang required" key="" for="validationDefault01">
                                        To District </label>

                                    <select class="form-select mr-sm-2 lang-dropdown" id="div_dist" name="div_dist"
                                        onchange="getinst_data('to','','','','','','')">
                                        <option value="" data-name-en="---Select Transaction---"
                                            data-name-ta="---??????????????? ?????????????????---">---Select
                                            district---
                                        </option>



                                    </select>
                                </div>
                                <div class="col-md-3 mb-1 mt-2">
                                    <label class="form-label lang required" key="" for="validationDefault01">
                                        To Institution </label>

                                    <select class="form-select mr-sm-2 lang-dropdown" id="audit_inst" name="audit_inst" >
                                        <option value="" data-name-en="---Select Transaction---"
                                            data-name-ta="---??????????????? ?????????????????---">---Select
                                            Institute---
                                        </option>


                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="todesig_div">
                            <label class="form-label lang required" key="" for="validationDefault01">
                                To Deignation </label>

                            <select class="form-select mr-sm-2 lang-dropdown" id="to_desig" name="to_desig">
                                <option value="" data-name-en="---Select Designation---"
                                    data-name-ta="---Select Designation---">---Select
                                    Designation---
                                </option>
                                {{-- @foreach ($dept as $departtment)
                                    <option value="{{ $departtment->deptcode }}"
                            data-name-en="{{ $departtment->deptelname }}"
                            data-name-ta="{{ $departtment->depttlname }}">
                            {{ $departtment->deptelname }}
                            </option>
                            @endforeach --}}

                            </select>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-3 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save</button>
                                <button type="button" class="btn btn-danger mt-3" id="reset_button">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="user_trans_det">User Transaction Details</div>
                <div class="card-body">
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="othertransaction_table"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th class="lang" key="userdetails">User Details</th>
                                        <th class="lang" key="transaction">Transaction Type</th>
                                        <th class="lang" key="application_detail">Application Detail</th>
                                        <th class="lang" key="">To Details</th>
                                        <th class="all lang" key="action">Action</th>
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

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    {{-- data table --}}
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>

    <!-- <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script> -->
    <script>


        const instPlaceholderEn = 'Select an Institution';
        const instPlaceholderTa = 'நிறுவனத்தை தேர்ந்தெடுக்கவும்';

        const userPlaceholderEn = 'Select user';
        const userPlaceholderTa = 'பதவியை தேர்ந்தெடுக்கவும்';

        const desigPlaceholderEn = 'Select Designation';
        const desigPlaceholderTa = 'பயனரை தேர்ந்தெடுக்கவும்';


        $("#translate").change(function() {
            lang = getLanguage('Y');
            // updateTableLanguage(lang);

        });

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#mappallocationobj_table')) {
                $('#mappallocationobj_table').DataTable().clear().destroy();
            }
            renderTable(language);
        }

        function datepicker(value, setdate) {
            var today = new Date();
            if (value == 'order_date') {
                // Calculate the minimum date (18 years ago)
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 3);

                // Calculate the maximum date (60 years ago)
var minDate = new Date(today);
                minDate.setMonth(today.getMonth() - 1);
            }
            if (value == 'to_date') {
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);

                // Calculate the maximum date (60 years ago)
                var minDate = today;
            }

            // Format the dates to dd/mm/yyyy format
            var minDateString = formatDate(minDate); // Format date to dd/mm/yyyy
            var maxDateString = formatDate(maxDate); // Format date to dd/mm/yyyy

            init_datepicker(value, minDateString, maxDateString, setdate)
        }

        function useronchange_superannuation()
        {
            var selectedOption = $('#deptuserid').find(':selected');
            var retirement = selectedOption.data('dor');
            $('#dor_div').show();
            $('#dor').val(retirement);

        }

        function show_transtypeDiv(trantypecode, formtype, roletypecode = '', fetch_devdeptcode) {
            var transtypecode = transtypecode || $('#transtypecode').val();
            $('#diversion_div').hide();
            var deptcode = $('#dev_deptcode').val();
            var roletypecode = roletypecode || $('#roletypecode').val();

            switch (transtypecode) {
                case '02':
                    $('#diversion_div').hide();
                    $('#todesig_div').hide();
                    // var retirement = $('#deptuserid').attr('dor');

                    var selectedOption = $('#deptuserid').find(':selected');
                    var retirement = selectedOption.data('dor');

                    $('#dor_div').show();

                    // onchange_desigcode(deptcode, '','','superannuation');

                    $('#dor').val(retirement);
                    break;
                case '03':
                    $('#dor_div').hide();
                    $('#diversion_div').hide();
                    $('#todesig_div').hide();
                    $('#dev_deptcode').prop('disabled', false);
                    // onchange_desigcode(deptcode, '','','');


                    break;
                case '04':
                    $('#dor_div').hide();
                    $('#diversion_div').hide();
                    $('#todesig_div').hide();
                    $('#dev_deptcode').prop('disabled', false);
                    // onchange_desigcode(deptcode, '','','');


                    break;
                case '05':
                    $('#dor_div').hide();
                    $('#diversion_div').hide();
                    $('#todesig_div').show();
                    $('#dev_deptcode').prop('disabled', false);
                    // onchange_desigcode(deptcode, '','','');

                    if(formtype!=='edit')
                    {
                        populatedesigdata();

                    }
                    break;
                case '06':
                    $('#dor_div').hide();
                    $('#diversion_div').show();
                    $('#todesig_div').hide();
                    $('#dev_deptcode').prop('disabled', false);
                    var deptcode = $('#deptcode').val();
                    $('#dev_deptcode').val(deptcode);
		    get_fromtodept(deptcode, transtypecode, fetch_devdeptcode);
                   // onchange_devdept(deptcode, '');

                    // onchange_desigcode(deptcode, '','','');


                    break;
                case '07':
                    $('#dor_div').hide();
                    $('#diversion_div').show();
                    $('#todesig_div').hide();
                    $('#dev_deptcode').prop('disabled', true);
                    var deptcode = $('#deptcode').val();
                    $('#dev_deptcode').val(deptcode);
                    onchange_devdept(deptcode, '');

                    // onchange_desigcode(deptcode, '','','');


                    // dev_deptcode

                    break;
                case '08':
                    $('#dor_div').hide();
                    $('#diversion_div').show();
                    $('#todesig_div').show();
                    $('#dev_deptcode').prop('disabled', false);
                    onchange_devdept(roletypecode, deptcode, '', '', '')
                    // onchange_desigcode(deptcode, '','','');

                    break;


                    // Add more cases as needed

                default:
                    // Do nothing or add default behavior
                    break;
            }
        }


        function show_rolebaseddiv(roletype) {

            getdetailsbasedon_dept('','','','','','','');

            if (roletype === '' || show_rolebaseddiv === 'NULL') {
                var roletype = $('#roletypecode').val();
            }
            if (roletype === '<?php echo $Dist_roletypecode; ?>') {

                $('.dist_div').show();
                $('.region_div').show();
            } else {
                $('#distcode').val();
                $('.dist_div').hide();
                $('.region_div').show();
            }
        }

        function get_distinst(transtowhom, roletypecode, deptcode, regioncode, distcode) {
            if (transtowhom == 'from') {
                if (!roletypecode) roletypecode = $('#roletypecode').val();
                if (!deptcode) deptcode = $('#deptcode').val();
                if (!regioncode) regioncode = $('#regioncode').val();

                if (roletypecode == '02') {
                    getdistregion_instdata(roletypecode, deptcode, regioncode, '', 'institution', 'frominstmapcode', '')
                }
                if (roletypecode == '01') {
                    getdistregion_instdata(roletypecode, deptcode, regioncode, '', 'district', 'distcode', '')
                }
            } else if (transtowhom === 'to') {
                if (!roletypecode) roletypecode = $('#roletypecode').val();
                if (!deptcode) deptcode = $('#dev_deptcode').val();
                if (!regioncode) regioncode = $('#div_region').val();

                if (roletypecode == '02') {
                    getdevdeptbasedData(roletypecode, deptcode, regioncode, '', 'institution', 'audit_inst', '')
                }
                if (roletypecode == '01') {
                    getdevdeptbasedData(roletypecode, deptcode, regioncode, '', 'district', 'div_dist', '')
                }
            }
        }

        function getinst_data(transtowhom, roletypecode, deptcode, regioncode, distcode, frominstmappingcode,
            toinstmappingcode) {
            if (transtowhom == 'from') {
                if (!roletypecode) roletypecode = $('#roletypecode').val();
                if (!deptcode) deptcode = $('#deptcode').val();
                if (!regioncode) regioncode = $('#regioncode').val();
                if (!distcode) distcode = $('#distcode').val();
                if (!distcode) distcode = $('#distcode').val();
                if (!frominstmappingcode) frominstmappingcode = $('#frominstmapcode').val();

                if (roletypecode == '01') {
                    getdistregion_instdata(roletypecode, deptcode, regioncode, distcode, 'institution', 'frominstmapcode',
                        frominstmappingcode)
                }

            } else if (transtowhom === 'to') {

                if (!roletypecode) roletypecode = $('#roletypecode').val();
                if (!deptcode) deptcode = $('#dev_deptcode').val();
                if (!regioncode) regioncode = $('#div_region').val();
                if (!distcode) distcode = $('#div_dist').val();
                if (!toinstmappingcode) toinstmappingcode = $('#audit_inst').val();


                if (roletypecode == '01') {
                    getdevdeptbasedData(roletypecode, deptcode, regioncode, distcode, 'institution', 'audit_inst',
                        toinstmappingcode, '')

                }
                // if (roletypecode == '01') {
                //     getdevdeptbasedData(roletypecode, deptcode, regioncode, '', 'district', 'div_dist', '')
                // }
            }
        }

        function getdetailsbasedon_dept(roletypecode, deptcode, regioncode, distcode, frominstmappingcode, desigcode) {

            roletypecode = roletypecode || $('#roletypecode').val();
            deptcode = deptcode || $('#deptcode').val();
            $('#dev_deptcode').val(deptcode);

            console.log(deptcode);
            // onchange_devdept(roletypecode, deptcode, '', '', '')
           
            getdistregion_instdata(roletypecode, deptcode, regioncode, distcode, 'region', 'regioncode',
                frominstmappingcode)

        }
        let desigdata;

        function populatedesigdata() {

               

            const $dropdown = $('#to_desig');

            console.log(desigdata);

            $dropdown.empty();
            $dropdown.append(
                '<option value="" data-name-en="---Select Designation---"data-name-ta="--- Select Designation---">Select Designation</option>'
            );
            if (desigdata.length === 0) {
                // Add a "No data available" option
                $dropdown.append('<option value="">No data available</option>');
            } else {

                // Populate the dropdown with data
                $.each(desigdata, function(index, desig) {
                    var isSelected = desig.desigcode === desigcode ? 'selected' : '';
                    $dropdown.append(
                        '<option value="' + desig.desigcode + '"' +
                        ' data-name-en="' + desig.desigelname + '"' +
                        ' data-name-ta="' + desig.desigtlname + '" ' + isSelected + '>' +
                        (lang === "en" ? desig.desigelname : desig.desigtlname) +
                        '</option>'
                    );
                });
            }
        }

        function getdeptbased_desig(deptcode, desigcode, dropdownId,instid) {
	    
   	    lang = getLanguage('');

            if (dropdownId === 'desigcode') {

                deptcode = deptcode || $('#deptcode').val();
                desigcode = desigcode || $('#desigcode').val();
                instid = instid || $('#frominstmapcode').val();
                desigfor = 'desig';
            } else {

                deptcode = deptcode || $('#dev_deptcode').val();
                desigcode = desigcode || $('#to_desig').val();

                if(instid)
                {
                    desigfor = 'desig';

                }else
                {
                    instid = '';
                    desigfor = 'todesig';

                }
                
            }


            if( ((desigfor == 'desig') && (deptcode) && (instid) ) ||  ((desigfor == 'todesig') && (deptcode) ) )
            {
                $.ajax({
                    url: '/transaction/getdeptbaseddesig', // Your API route to get user details
                    method: 'POST',
                    data: {
                        deptcode: deptcode,
                        instid:instid,
                        for : desigfor
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF token for security
                    },
                    success: function(response) {

                        desigdata = response.data;
                        const $dropdown = $('#' + dropdownId);

                        $('#to_desig').empty();
                        $('#to_desig').append(
                            '<option value="" data-name-en="---Select Designation---"data-name-ta="--- Select Designation---">Select Designation</option>'
                        );
                        $dropdown.empty();
                        $dropdown.append(
                            '<option value="" data-name-en="---Select Designation---"data-name-ta="--- Select Designation---">Select Designation</option>'
                        );
                        if (desigdata.length === 0) {
                            // Add a "No data available" option
                            $dropdown.append('<option value="">No data available</option>');
                        } else {

                            // Populate the dropdown with data
                            $.each(desigdata, function(index, desig) {
                                var isSelected = desig.desigcode === desigcode ? 'selected' : '';
                                $dropdown.append(
                                    '<option value="' + desig.desigcode + '"' +
                                    ' data-name-en="' + desig.desigelname + '"' +
                                    ' data-name-ta="' + desig.desigtlname + '" ' + isSelected +
                                    '>' +
                                    (lang === "en" ? desig.desigelname : desig.desigtlname) +
                                    '</option>'
                                );
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
            else
            {
                $('#transtypecode').val('');

                const $desig_dropdown = $("#desigcode");
                $desig_dropdown.html(`<option value="" data-name-en="${desigPlaceholderEn}" data-name-ta="${desigPlaceholderTa}">
                    ${lang === 'ta' ? desigPlaceholderTa : desigPlaceholderEn}
                </option>`);

                const $user_dropdown = $("#deptuserid");
                $user_dropdown.html(`<option value="" data-name-en="${userPlaceholderEn}" data-name-ta="${userPlaceholderTa}">
                    ${lang === 'ta' ? userPlaceholderTa : userPlaceholderEn}
                </option>`);

                // $('#dor').hide();

            }

           


            
        }

        function getdistregion_instdata(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,frominstmappingcode) 
        {
            if (valuefor === 'institution') 
            {
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

            if( (roletypecode) && (( (valuefor == 'region') && (deptcode) ) || ((valuefor == 'district') && (deptcode) && (regioncode)) || ((valuefor == 'institution') && (deptcode) && (regioncode) && (distcode)) ))
            {
                $.ajax({
                    url: '/transaction/fetchRegDistInstbasedondept',
                    type: 'POST',
                    data: {
                        roletypecode,
                        deptcode,
                        regioncode,
                        distcode,
                        valuefor,
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
                                        return `<option value="${item.instmappingcode}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${item.instmappingcode === frominstmappingcode ? "selected" : ""}>${item.instename}</option>`;
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
                        updateSelectColorByValue(document.querySelectorAll(".form-select"));
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

            }
            else
            {

                const $inst_dropdown = $("#frominstmapcode");               
                $inst_dropdown.html(`<option value="" data-name-en="${instPlaceholderEn}" data-name-ta="${instPlaceholderTa}">
                    ${lang === 'ta' ? instPlaceholderTa : instPlaceholderEn}
                </option>`);

                $('#transtypecode').val('');

                const $desig_dropdown = $("#desigcode");
                $desig_dropdown.html(`<option value="" data-name-en="${desigPlaceholderEn}" data-name-ta="${desigPlaceholderTa}">
                    ${lang === 'ta' ? desigPlaceholderTa : desigPlaceholderEn}
                </option>`);

                const $user_dropdown = $("#deptuserid");
                $user_dropdown.html(`<option value="" data-name-en="${userPlaceholderEn}" data-name-ta="${userPlaceholderTa}">
                    ${lang === 'ta' ? userPlaceholderTa : userPlaceholderEn}
                </option>`);

                $('#dor').hide();

                

                
            }


        }


        function onchange_devdept(roletypecode, deptcode, regioncode, distcode, toinstmappingcode) {
            roletypecode = roletypecode || $('#roletypecode').val();
            dev_deptcode = $('#dev_deptcode').val();
            deptcode = deptcode || $('#deptcode').val();

            var transtypecode = $('#transtypecode').val();


            if (deptcode == dev_deptcode && roletypecode == '01') {
                var fromdistcode = $('#distcode').val();
            } else {

                var fromdistcode = '';
            }
            // getdeptbased_desig(deptcode, desigcode)
            if (transtypecode == '05') {
                getdeptbased_desig(dev_deptcode, desigcode, 'to_desig')
            } else if (transtypecode == '08') {
                getdeptbased_desig(dev_deptcode, desigcode, 'to_desig')
                getdevdeptbasedData(roletypecode, dev_deptcode, regioncode, distcode, 'region', 'div_region',
                    toinstmappingcode,
                    fromdistcode)

            }else if (transtypecode == '06') {
                getdeptbased_desig(dev_deptcode, desigcode, 'to_desig')
                getdevdeptbasedData(roletypecode, dev_deptcode, regioncode, distcode, 'region', 'div_region',
                    toinstmappingcode,
                    fromdistcode)

            } else {

                getdevdeptbasedData(roletypecode, dev_deptcode, regioncode, distcode, 'region', 'div_region',
                    toinstmappingcode,
                    fromdistcode)
            }
        }

        function getdevdeptbasedData(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,
            toinstmappingcode,
            fromdistcode) {
            var fromdeptcode = $('#deptcode').val();
            if (fromdeptcode == deptcode) {
                fromdistcode = $('#distcode').val();
            }
            if (valuefor === 'institution') {
                roletypecode = roletypecode || $('#roletypecode').val();
                deptcode = deptcode || $('#dev_deptcode').val();
                regioncode = regioncode || $('#div_region').val();
                distcode = distcode || $('#div_dist').val();
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

                    const $inst_dropdown = $("#audit_inst");               
                $inst_dropdown.html(`<option value="" data-name-en="${instPlaceholderEn}" data-name-ta="${instPlaceholderTa}">
                    ${lang === 'ta' ? instPlaceholderTa : instPlaceholderEn}
                </option>`);

            if( (roletypecode) && (( (valuefor == 'region') && (deptcode) ) || ((valuefor == 'district') && (deptcode) && (regioncode)) || ((valuefor == 'institution') && (deptcode) && (regioncode) && (distcode)) ))
            {
                $.ajax({
                    url: '/transaction/instdataforothers',
                    type: 'POST',
                    data: {
                        roletypecode,
                        deptcode,
                        regioncode,
                        distcode,
                        valuefor,
                        fromdistcode,
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
                                        return `<option value="${item.instmappingcode}" data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${item.instmappingcode === toinstmappingcode ? "selected" : ""}>${item.instename}</option>`;
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
                        updateSelectColorByValue(document.querySelectorAll(".form-select"));
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
            }
           

        }
     
        function onchange_devregion(dev_deptcode, dev_regioncode, from_district, to_district, instmappingcode = '') {
            var dev_deptcode = dev_deptcode || $('#dev_deptcode').val();
            var dev_regioncode = dev_regioncode || $('#div_region').val();
            var from_district = from_district || $('#distcode').val();


            $.ajax({
                url: '/transaction/getdiversioninst', // Your API route to get user details
                method: 'POST',
                data: {
                    dev_deptcode: dev_deptcode,
                    dev_regioncode: dev_regioncode,
                    from_district: from_district
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {

                    var distdata = response.distdata;

                    $('#div_dist').empty();
                    $('#div_dist').append(
                        '<option value="" data-name-en="---Select District---"data-name-ta="--- Select District---">Select District</option>'
                    );
                    if (distdata.length === 0) {
                        // Add a "No data available" option
                        $('#div_dist').append('<option value="">No data available</option>');
                    } else {
                        // Populate the dropdown with data
                        $.each(distdata, function(index, distdet) {
                            var isSelected = distdet.distcode === to_district ? 'selected' : '';
                            $('#div_dist').append(
                                '<option value="' + distdet.distcode + '"' +
                                ' data-name-en="' + distdet.distename + '"' +
                                ' data-name-ta="' + distdet.disttname + '" ' + isSelected +
                                '>' +
                                (lang === "en" ? distdet.distename : distdet.disttname) +
                                '</option>'
                            );
                        });
                    }

                    if (instmappingcode != '') {
                        onchange_divdist()
                        onchange_divdist(dev_deptcode, dev_regioncode, to_district, instmappingcode)
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

    

        function onchange_desigcode(deptcode, desigcode = '', deptuserid = '',transtype='') {

            var deptcode = deptcode || $('#deptcode').val();
            var desigcode = desigcode || $('#desigcode').val();
            var distcode = distcode || $('#distcode').val();
            var transactioncode = $('#transtypecode').val();
            if(transactioncode == '02')
            {
                transtype='superannuation';  
            }

            if(deptcode && desigcode && distcode)
            {
                $.ajax({
                    url: '/transaction/filterforusertrans', // Your API route to get user details
                    method: 'POST',
                    data: {
                        deptcode: deptcode,
                        desigcode: desigcode,
                        distcode: distcode,
                        transtype:transtype
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF token for security
                    },
                    success: function(response) {

                        var userdata = response.users;

                        $('#deptuserid').empty();
                        $('#deptuserid').append(
                            '<option value="" data-name-en="---Select User---"data-name-ta="--- ??????? ?????????????????---">Select User</option>'
                        );

                        if (userdata.length === 0) {
                            // Add a "No data available" option
                            $('#deptuserid').append('<option value="">No user available</option>');
                            $('#dor_div').hide();

                        } else {

                            $.each(userdata, function(index, userdet) {


                                var isSelected = userdet.deptuserid === deptuserid ? 'selected' : '';

                                $('#deptuserid').append(
                                    '<option value="' + userdet.deptuserid + '"' +
                                    ' data-dor="' + userdet.dor + '"' +
                                    ' data-name-en="' + userdet.username + '"' +
                                    ' data-name-ta="' + userdet.usertamilname + '" ' + isSelected +
                                    '>' +
                                    (lang === "en" ? userdet.username : userdet.usertamilname) +
                                    '</option>'
                                );
                            });

                        }


                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
            else
            {
                const $user_dropdown = $("#deptuserid");
                $user_dropdown.html(`<option value="" data-name-en="${userPlaceholderEn}" data-name-ta="${userPlaceholderTa}">
                    ${lang === 'ta' ? userPlaceholderTa : userPlaceholderEn}
                </option>`);
                $('#dor_div').hide();

            }

            
        }

        function onchange_divdist(deptcode = '', regioncode = '', divdistcode = '', instmappingcode = '') {
            var deptcode = deptcode || $('#dev_deptcode').val();
            var divdistcode = divdistcode || $('#div_dist').val();
            var regioncode = regioncode || $('#div_region').val();



            $.ajax({
                url: '/transaction/getdiversioninst', // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    distcode: divdistcode,
                    regioncode: regioncode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var auditinstDet = response.auditinstDet;
                    $('#audit_inst').empty();
                    $('#audit_inst').append(
                        '<option value="" data-name-en="---Select Institution---"data-name-ta="--- ??????? ?????????????????---">Select Institution</option>'
                    );
                    $.each(auditinstDet, function(index, auditinstdata) {

                        var isSelected = auditinstdata.instmappingcode === instmappingcode ?
                            'selected' : '';

                        $('#audit_inst').append(
                            '<option value="' + auditinstdata.instmappingcode + '"' +

                            ' data-name-en="' + auditinstdata.instename + '"' +
                            ' data-name-ta="' + auditinstdata.insttname + '" ' + isSelected +
                            '>' +
                            (lang === "en" ? auditinstdata.instename : auditinstdata
                                .insttname) +
                            '</option>'
                        );
                    });


                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        /***********************************jquery Validation**********************************************/
        const $othertrans_form = $("#othertrans_form");

        $('#file').on('change', function() {
            $(this).valid();
        });
        ///File size
        $.validator.addMethod("fileSizeLimit", function(value, element) {
            if (element.files.length > 0) {
                return element.files[0].size <= 1 * 1024 * 1024;
            }
            return true;
        }, function() {
            const language = getlanguagelc();
            return 'File size should not exeed 1MB';
        });

        ////file Type
        $.validator.addMethod("validFileType", function(value, element) {
            if (value) {
                let allowedTypes = ["pdf"];
                let fileExtension = value.split(".").pop().toLowerCase();
                return allowedTypes.includes(fileExtension);
            }
            return true;
        }, function() {
            const language = getlanguagelc();
            return '';
        });
        // Validation rules and messages
        $othertrans_form.validate({
            rules: {
                deptcode: {
                    required: true,
                },
                roletypecode: {
                    required: true,
                },
                regioncode: {
                    required: true,
                },
                distcode: {
                    required: true,
                },
                frominstmapcode: {
                    required: true,
                },
                div_region: {
                    required: true,
                },
                dev_deptcode: {
                    required: true,
                },
                div_dist: {
                    required: true,
                },
                audit_inst: {
                    required: true,
                },
                to_desig: {
                    required: true,
                },
                desigcode: {
                    required: true,
                },
                deptuserid: {
                    required: true,
                },
                transtypecode: {
                    required: true,
                },

                order_date: {
                    required: true,
                },
                orderno: {
                    required: true,
                },
                file: {
                    required: true,
                    validFileType: true,
                    fileSizeLimit: true,
                },
            },
            errorPlacement: function(error, element) {
                // For datepicker fields inside input-group, place error below the input group
                if (element.hasClass('datepicker')) {
                    // Insert the error message after the input-group, so it appears below the input and icon
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    // For other elements, insert the error after the element itself
                    error.insertAfter(element);
                }

            },

            messages: {
                deptcode: {
                    required: "Select Department",
                },
                roletypecode: {
                    required: "Select Role Type",
                },
                regioncode: {
                    required: "Select Region",
                },
                distcode: {
                    required: 'Select District',
                },
                frominstmapcode: {
                    required: 'Select Institution',
                },
                desigcode: {
                    required: 'Select Designation',
                },
                div_region: {
                    required: 'Select To Region',
                },
                dev_deptcode: {
                    required: 'Select To Department',
                },
                div_dist: {
                    required: 'Select To District',
                },
                audit_inst: {
                    required: 'Select To Institution',
                },
                to_desig: {
                    required: 'Select To Desination',
                },
                deptuserid: {
                    required: "Select User",
                },

                transtypecode: {
                    required: "Choose Transaction Type",
                },

                order_date: {
                    required: "Select Date",
                },
                orderno: {
                    required: "Enter Order Number",
                },
                file: {
                    required: "Choose File",
                    validFileType: "Allowed File Type is PDF",
                    fileSizeLimit: "File Size limit is 1MB"
                },



                // highlight: function(element, errorClass) {
                //     $(element).removeClass(errorClass); //prevent class to be added to selects
                // },

            }
        });

        // Scroll to the first error field (for better UX)
        function scrollToFirstError() {
            const firstError = $othertrans_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
        /***********************************jquery Validation**********************************************/

        $(document).on('click', '#buttonaction', function(event) {

            event.preventDefault(); // Prevent form submission


            if ($othertrans_form.valid()) {

                get_inserttransdata('insert');

            } else {
                scrollToFirstError();
            }
        });

        $(document).on('click', '.edit_btn', function() {
            // Add more logic here
            // alert();
            var id = $(this).attr('id'); //Getting id of user clicked edit button.

            if (id) {
                reset_form();
                fetchothertrans_data(id);

            }
        });
        $(document).on('click', '.fwd_btn', function() {
            // Add more logic here
            // alert();
            var id = $(this).attr('id');
            var transtypecode = $(this).attr('transtypecode');
            var userid = $(this).attr('userid');
            

            //Getting id of user clicked edit button.
            // window.location.href = '/datatransfer?id=' + id;
            // window.location.href = '/datatransfer?id=' + encodeURIComponent(id);
            if (id) {
		var confirmation = 'Are you sure to forward this Application to concerned AD?'; 
                document.getElementById("process_button").onclick = function() {
                    // getForwardTo_data(id, transtypecode);
                    forward_application( id, transtypecode,userid)
                };
                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                // reset_form();
                // getTeamhead_det(id);

            }

        });

        // function getForwardTo_data(transid, transtypecode) {
        //     var id = transid;
        //     var transtypecode = transtypecode;
        //     $.ajax({
        //         url: 'fetchforwardto_data', // Your API route to get user details
        //         method: 'POST',
        //         data: {
        //             // leaveid: leaveid
        //             // deptcode : $('#deptcode').val(),
        //             transid: transid,
        //             transtypecode: transtypecode
        //         }, // Pass deptuserid in the data object
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
        //                 'content') // CSRF token for security
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 $('#display_error').hide();
        //                 change_button_as_update('othertrans_form', 'action', 'buttonaction',
        //                     'display_error', '', '');
        //                 // validator.resetForm();

        //                 const forwardtodet = response.data[0]; // The array of schedule data
        //                 var forwardto_userid = forwardtodet.userid;
        //                 var forwardto_userchargeid = forwardtodet.userchargeid;

        //                 if (forwardtodet) {

        //                     forward_application(forwardto_userid, forwardto_userchargeid, id,
        //                         transtypecode);
        //                 }


        //             } else {
        //                 alert(' Details not found');
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', xhr.responseText);
        //             // let errorMessage = response.error;

        //             if (xhr.responseText) {
        //                 try {
        //                     const response = JSON.parse(xhr.responseText);
        //                     errorMessage = response.message || errorMessage;
        //                 } catch (e) {
        //                     console.error("Error parsing error response:", e);
        //                 }
        //             }

        //             passing_alert_value('Alert', errorMessage, 'confirmation_alert',
        //                 'alert_header', 'alert_body', 'confirmation_alert');
        //         }
        //     });
        // }

        function forward_application( id, transtypecode,userid) {


            $.ajax({
                url: '/transaction/forward_application', // Your API route to get user details
                method: 'POST',
                data: {
                    userid: userid,
                    // userchargeid: forwardto_userchargeid,
                    id: id,
                    transactiontypecode: transtypecode,
                    action : 'first'


                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#display_error').hide();
                        change_button_as_update('othertrans_form', 'action', 'buttonaction',
                            'display_error', '', '');
                        // validator.resetForm();

                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');

                        reset_form();
                        fetchAlldata(lang);

                    } else {
                        alert(response.message);
                        
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        function fetchothertrans_data(othertransid) {
            $.ajax({
                url: '/transaction/fetchOtherTranDel', // Your API route to get user details
                method: 'POST',
                data: {
                    othertransid: othertransid
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {

                        $('#display_error').hide();
                        change_button_as_update('othertrans_form', 'action', 'buttonaction',
                            'display_error', '', '', 'update');
                        const detail = response.data[0];
                        // $('#roletypecode').val(detail.roletypecode);
                        $('#deptcode').val(detail.deptcode);
                        var roletypecode = detail.roletypecode;
                        $('#othertransid').val(detail.encrypted_othertransid);

                        getroletypecode_basedondept(detail.deptcode, roletypecode)


                        getdeptbased_desig(detail.deptcode, detail.desigcode, 'desigcode',detail.frominstmappingcode); 

                       // getdeptbased_desig(detail.deptcode, detail.todesigcode, 'to_desig',detail.frominstmappingcode); 


                        getdetailsbasedon_dept(roletypecode, detail.deptcode, detail.regioncode, detail
                            .distcode, detail.frominstmappingcode, detail.fromdesigcode);

                        if (roletypecode == '02') {
                            getdistregion_instdata(roletypecode, detail.deptcode, detail.regioncode, '',
                                'institution',
                                'frominstmapcode', detail.frominstmappingcode)
                        } else if (roletypecode == '01') {
                            getdistregion_instdata(roletypecode, detail.deptcode, detail.regioncode, detail
                                .distcode, 'district',
                                'distcode', detail.frominstmappingcode)
                        }

                        setTimeout(() => {
                            onchange_desigcode(detail.deptcode, detail.fromdesigcode, detail
                                .deptuserid);
                            getinst_data('from', detail.roletypecode, detail.deptcode, detail
                                .regioncode, detail.distcode, detail.frominstmappingcode, '')
                        }, 2000); // Delay in milliseconds (1000ms = 1 second)


                        $('#transtypecode').val(detail.transactiontypecode);
                        $('#orderno').val(detail.orderno);
                        datepicker('order_date', convertDateFormatYmd_ddmmyy(detail.orderdate));
                        if (detail.districtcode != null || detail.districtcode != '') {
                            $('.dist_div').show();


                        }
                        show_transtypeDiv(detail.transactiontypecode, 'edit', roletypecode, detail.dev_deptcode);
                        if (detail.transactiontypecode == '02') {
                            // var dateofret = convertDateFormatYmd_ddmmyy(detail.dor)
                            // alert(dateofret)
                            $('#dor').val(detail.dor)
                        }
                        if (detail.transactiontypecode == '06' || detail.transactiontypecode == '07' ||
                            detail
                            .transactiontypecode == '08') {
                            $('#diversion_div').show()
                            $('#dev_deptcode').val(detail.dev_deptcode)

                            onchange_devdept(detail.roletypecode, detail.dev_deptcode, detail.div_region,
                                detail
                                .div_dist, detail.toinstmappingcode)


                            if (roletypecode == '02') {
                                getdevdeptbasedData(detail.roletypecode, detail.dev_deptcode, detail
                                    .div_region, '',
                                    'institution',
                                    'to_inst', detail.toinstmappingcode)
                            } else if (roletypecode == '01') {
                                getdevdeptbasedData(roletypecode, detail.dev_deptcode, detail.div_region,
                                    detail
                                    .div_dist, 'district',
                                    'div_dist', detail.toinstmappingcode, detail.distcode)
                            }
                            setTimeout(() => {

                                if (detail.transactiontypecode == '08') {
                                    getdeptbased_desig(detail.dev_deptcode, detail.todesigcode,
                                        'to_desig')
                                }


                                getinst_data('to', detail.roletypecode, detail.dev_deptcode, detail
                                    .div_region, detail.div_dist, '', detail.instmappingcode)
                            }, 2000); // Delay in milliseconds (1000ms = 1 second)


                            // $('#div_region').val(detail.div_region);
                            // $('#div_dist').val(detail.div_dist);

                            // setTimeout(() => {
                            //     onchange_regioncode('div_dist', detail.deptcode, detail.div_region,
                            //         detail.div_dist, detail
                            //         .instmappingcode)
                            // }, 1000); // Delay in milliseconds (1000ms = 1 second)




                        }else if (detail.transactiontypecode == '05')
                        {
                            getdeptbased_desig(detail.deptcode, detail.todesigcode, 'to_desig',detail.frominstmappingcode)

                        }
                        // const fileDetailsString = detail
                        //     .filedetails; // Assuming this is the response field
                        const fileDetail = detail.filedetails; // Split by comma for each file

                        // alert(firstItem.filedetails_1);
                        if (detail.filedetails) {

                            $('#view_file-list-container').show()
                        }


                        // Split the fileDetail by hyphen (-)
                        const [name, path, size, fileuploadid] = fileDetail.split('-');

                        // Create the file object
                        const files = {
                            id: 1, // Static ID for a single file
                            name: name,
                            path: path,
                            size: size,
                            fileuploadid: fileuploadid
                        };
                        view_files(files);




                    } else {
                        alert(' Details not found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        function view_files(file) {
            const fileListContainer = $('#view_file-list-container');
            $('#file').hide();
            fileListContainer.empty(); // Clear previous file cards

            $('#file').val('');
            // Set the fileuploadid directly since it's a single file
            $('#uploadid').val(file.fileuploadid);

            const fileCard = `

                <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
                    <div class="d-flex flex-row">

                        <div class="p-3 mb-1">
                            <h3 class="text-dark mb-0 fs-2">
                                <a style="color:black;" href="${file.path}" target="_blank">${file.name}</a>
                            </h3>
                        </div>
                        <div class="p-1 align-items-center mt-2 "  onclick="remove_file()">
                            <h5 class="text-danger box mb-0 round-40 p-1">
                                <i class="ti ti-trash"></i>
                            </h5>
                        </div>
                    </div>
                </div>
            `;

            fileListContainer.append(fileCard); // Add the file card to the container
        }

        function remove_file() {
            $('#view_file-list-container').hide();
            $('#file').val('').show();
            // $('#uploadid').val('');
        }

        function get_inserttransdata() {


            var formData = new FormData($('#othertrans_form')[0]);
            $.ajax({
                url: '/transaction/othertransction_insertupdate', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {

                        passing_alert_value('Confirmation', response.success,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        fetchAlldata(lang);


                        reset_form();


                    } else {
                        passing_alert_value('Alert', response.message, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    }
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.error ||
                        'An unknown error occurred';

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');


                    // Optionally, log the error to console for debugging
                    console.error('Error details:', xhr, status, error);
                }
            });
        }


        $('#reset_button').on('click', function() {
            reset_form(); // Call the reset_form function
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function reset_form() {

	    $('.edit_btn, .fwd_btn').show(); 
	    $('.processing_btn').hide();
            $('#display_error,#diversion_div').hide();
            change_button_as_insert('othertrans_form', 'action', 'buttonaction', 'display_error', '', '');
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
            $("#othertrans_form").validate().resetForm(); // Reset the validation errors
            $("#othertrans_form")[0].reset();
            $('#uploadid').val('');
            $('#desigcode , #deptuserid , #regioncode, #distcode, #frominstmapcode, #div_region, #div_dist, #audit_inst')
                .empty('');
            makedropdownempty('deptuserid', '---Select User---');
            makedropdownempty('regioncode', '---Select Region---');
            makedropdownempty('desigcode', '---Select Designation---');
            makedropdownempty('distcode', '---Select District---');
            makedropdownempty('frominstmapcode', '---Select Institution---');

            makedropdownempty('div_region', '---Select Region---');
            makedropdownempty('div_dist', '---Select District---');
            makedropdownempty('audit_inst', '---Select Institution---');

            remove_file();

        }







        function getroletypecode_basedondept(deptcode, roletypecode) {
        const lang = getLanguage();

        const defaultOption = `
                    <option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ">
                        ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும ' : 'Select Role Type'}
                    </option>`;

        const $dropdown = $("#roletypecode");
        // Get department code from DOM if not passed
        if (!deptcode) deptcode = $('#deptcode').val();



        if (deptcode) {
            // Clear the dropdown and set the default option
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getroletypecode_basedondept_othertrans',
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
        //getDesignationBasedonDept(deptcode, desigcode, 'createcharge', 'deptcode', 'desigcode');
    }




        /********************************************************** Fetching Data ******************************************************/

            let dataFromServer;

            $(document).ready(function() {
                var lang = getLanguage('');
                fetchAlldata(lang);
            });
            

            function fetchAlldata(lang) {
                $.ajax({
                    url: '/transaction/fetchOtherTranDel', // For creating a new user or updating an existing one
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            // alert('adds');

                            $('#tableshow').show();
                            $('#othertransaction_table_wrapper').show();
                            $('#no_data').hide();
                            dataFromServer = response.data;
                            // // alert(dataFromServer);
                            renderTable(lang);
                        } else {

                            $('#tableshow').hide();
                            $('#othertransaction_table_wrapper').hide();
                            $('#no_data').show();
                        }
                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);

                        var errorMessage = response.error ||
                            'An unknown error occurred';

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');


                        // Optionally, log the error to console for debugging
                        console.error('Error details:', xhr, status, error);
                    }
                });
            }

            function renderTable(language) {

                const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
                const fromdistrictColumn = language === 'ta' ? 'disttname' : 'distename';
                const fromregionColumn = language === 'ta' ? 'regiontname' : 'regionename';
                const todistrictColumn = language === 'ta' ? 'to_disttname' : 'to_distename';
                const toregionColumn = language === 'ta' ? 'to_regiontname' : 'to_regionename';
                const toinstColumn = language === 'ta' ? 'insttname' : 'instename';
                const frominstColumn = language === 'ta' ? 'from_insttname' : 'from_instename';
const todepartmentColumn = language === 'ta' ? 'div_depttsname' : 'div_deptesname';

                if ($.fn.DataTable.isDataTable('#othertransaction_table')) {
                    $('#othertransaction_table').DataTable().clear().destroy();
                }


                table = $('#othertransaction_table').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "lengthChange": true,
                    "data": dataFromServer,
                    "responsive": true,
                    // "scrollX": true,
                    "initComplete": function(settings, json) {
                        $("#othertransaction_table").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                    },
                    "columns": [{
                            "data": null,
                            "render": function(data, type, row, meta) {
                                return meta.row + 1;

                            },
                            'className': 'text-end',

                        },
                     
                        {
                            "data": "null",
                            "render": function(data, type, row) {
                                // Function to format date as dd-mm-yy
                                function formatDate(dateString) {
                                    if (!dateString) return '-';  // Return '-' if the date is null or undefined
                                    const date = new Date(dateString);
                                    const day = String(date.getDate()).padStart(2, '0');  // Adds leading zero if day is less than 10
                                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed, so add 1
                                    const year = date.getFullYear();
                                    return `${day}-${month}-${year}`;
                                }

                                // Format DOB and DOR with null check
                                const formattedDob = formatDate(row.dob);
                                const formattedDor = formatDate(row.dor);

                                return `
                                    <b>User Name</b>: ${row.username} <br>
                                    <small><b>Ifhrms No</b>: ${row.ifhrmsno}</small><br>
                                    <small><b>DOB</b>: ${formattedDob}</small><br>
                                    <small><b>DOR</b>: ${formattedDor}</small><br>
                                    
                                   <small><b>Designation:</b> ${row.desigelname}</small><br>
				<small><b>Department:</b> ${row[departmentColumn] || '-'}</small><br>
				<small><b>Region:</b> ${row[fromregionColumn]}</small><br>
				<small><b>District:</b> ${row[fromdistrictColumn]}</small><br>
				<small><b>Institution:</b> ${row[frominstColumn]}</small><br>

                                `;
                            }
                        },
                        {
                            "data": "transactiontypelname"
                        },

                       


  {

       "data": "null",
       "render": function(data, type, row)
        {
            let orderdate = row.orderdate ? new Date(row.orderdate).toLocaleDateString(
                                        'en-GB') :"N/A";

            const filedetails = row.filedetails.split('-');
            const FileName = `${filedetails[0]}`;
            const FileURL = `${filedetails[1]}`;
                               
            return `<b>Order Date</b>:  ${orderdate} <br><b>Order Number : </b> ${row.orderno} <br>
                    <b>File Details</b><br><a href="${FileURL}"  target="_blank" class="text-primary">
                     <b>${FileName}</b></a>`;
                           
        },
       'className': "d-none d-md-table-cell lang extra-column text-wrap",

  },

                      {
	"data": "null",
	"render": function(data, type, row) {
		return `<b>Department : </b> ${row[todepartmentColumn]||'-'}<br><b>Region : </b> ${row[toregionColumn]||'-'}<br> </b><b>District : </b> ${row[todistrictColumn]||'-'}<br><b>Institution : </b> ${row[toinstColumn]||'-'}`;

	},
	'className': "d-none d-md-table-cell lang extra-column text-wrap",
}, {
                            "data": "encrypted_othertransid",
                            "render": function(data, type, row) {

                                if (row.processcode === 'S') {
                                    // Check if statusflag is 'N'
                                    return `<center>
                                                    <div class="action-buttons">
                                                    <a class="btn editicon edit_btn" id="${data}">
                                                        <i class="ti ti-edit fs-4"></i>
                                                    </a>
                                                    <a class="btn editicon fwd_btn" id="${data}" userid="${row.userid}" transtypecode="${row.transactiontypecode}">
                                                        <i class="ti ti-corner-up-right-double fs-4"></i>
                                                    </a>
                                                    <button class="btn btn-warning processing_btn" style="display: none;" disabled >
                                                        <i class="ti ti-loader fs-4 ti-spin"></i> Processing...
                                                    </button>
                                                    </div>
                                                </center>`;

                                                    } else if (row.processcode === 'F') {
                                                        // Otherwise, show the Finalize button
                                                        return `<center>
                                        <button class="btn btn-primary finalize_btn" id="${data}">
                                            Forwarded
                                        </button>
                                    </center>`;
                                } else if (row.processcode === 'C') {
                                    // Otherwise, show the Finalize button
                                                        return `<center>
                                        <button class="btn btn-danger finalize_btn" id="${data}">
                                            Rejected
                                        </button>
                                    </center>`;
                                } else if (row.processcode === 'P') {
                                    // Otherwise, show the Finalize button
                                    return `<center>
                                    <button class="btn btn-success finalize_btn" id="${data}">
                                        Approved
                                    </button>
                                </center>`;
                                }
                            }
                        }
                    ]
                });
                $('#tableshow').css('display', 'block');

                // Adjust the columns after display to fix alignment issues
                table.columns.adjust().draw();
            }

            $(document).on('click', '.edit_btn', function () {
                $('.edit_btn, .fwd_btn').show(); 
                $('.processing_btn').hide();
                var container = $(this).closest('.action-buttons');
                container.find('.edit_btn, .fwd_btn').hide();         // Hide edit and forward buttons
                container.find('.processing_btn').show();             // Show processing button
            });

        
        /********************************************************** Fetching Data ******************************************************/
function get_fromtodept(fromdeptcode, transactiontype, fetch_devdeptcode) {
            var from_deptcode = fromdeptcode || $('#deptcode').val();
            var transactiontype_code = transactiontype || $('#transtypecode').val();

            if (from_deptcode) {
                $.ajax({
                    url: '/transaction/get_fromtodeptdet', // Your API route to get user details
                    method: 'POST',
                    data: {
                        from_deptcode: from_deptcode,
                        transactiontype_code: transactiontype_code,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF token for security
                    },
                    success: function(response) {
                        $('#dev_deptcode').prop('disabled', false);

                        if (
                            response.success &&
                            response.data &&
                            response.data.length === 1
                        ) {

                            $('#dev_deptcode').val(response.data[0]['todeptcode']).attr('disabled', true)
                            var roletypecode = $('#roletypecode').val();
                            var to_deptcode = $('#dev_deptcode').val();
                            var regioncode = $('#regioncode').val();
                            var distcode = $('#distcode').val();



                            onchange_devdept(roletypecode, deptcode, '');

                        } else {

                            $('#dev_deptcode').empty();

                            $('#dev_deptcode').append(
                                '<option value="">--Select Department--</option>'
                            );

                            $.each(response.data, function(index, item) {

                                var selected = (fetch_devdeptcode == item.todeptcode) ? 'selected' : '';

                                $('#dev_deptcode').append(
                                    '<option value="' + item.todeptcode + '"' +
                                    ' data-name-en="' + item.deptelname + '"' +
                                    ' data-name-ta="' + item.depttlname + '" ' +
                                    selected + '>' +
                                    (lang === "en" ? item.deptelname : item.depttlname) +
                                    '</option>'
                                );

                            });


                        }

                        // $('#dev_deptcode').val(response.data[0]['todeptcode']).attr('disabled', true)

                        // var roletypecode = $('#roletypecode').val();
                        // var to_deptcode = $('#dev_deptcode').val();
                        // var regioncode = $('#regioncode').val();
                        // var distcode = $('#distcode').val();



                        // onchange_devdept(roletypecode, deptcode, '');

                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        }
    </script>
@endsection
