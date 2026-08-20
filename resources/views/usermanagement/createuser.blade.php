@section('content')
@section('title', 'Create User Report')

@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $distcode = $sessionchargedel->distcode;
    $roleTypeCode = $sessionchargedel->roletypecode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

    $dga_roletypecode = $DGA_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;


@endphp

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header card_header_color lang" key="createUser_head">New Auditor Creation</div>
            <div class="card-body">
                <form id="createuser" name="createuser">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="userid" id="userid" value="" />
                    <div class="row">
                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01"
                                key="department">Department </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="onchangeDept()">
                                <option value='' data-name-en=" Select Department"
                                    data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option>
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

                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01" key="district">District </label>
                            <select class="form-select mySelect lang-dropdown select2" id="distcode" name="distcode"
                                <?php echo $make_dist_disable; ?>>
                                <option value='' data-name-en="Select District"
                                    data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option>

                                @if (!empty($distdetails) && is_iterable($distdetails))
                                    @foreach ($distdetails as $d)
                                        <option value="{{ $d->distcode }}"
                                            {{ old('distcode', $distcode) == $d->distcode ? 'selected' : '' }}
                                            data-name-en="{{ $d->distename }}" data-name-ta="{{ $d->disttname }}">
                                            {{ $d->distename }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled data-name-en="No District Available" data-name-ta="மாவட்டம் இல்லை">
                                        No Departments Available</option>
                                @endif


                            </select>
                        </div>

                        <!-- <div class="col-md-4 mb-3">
                                                                                                <label class="form-label required" for="roletypecode">Role Type</label>
                                                                                                <select class="form-select mr-sm-2" id="roletypecode" name="roletypecode" >

                                                                                                    <option value=''>Select Role Type</option>

                                                                                                </select>
                                                                                            </div> -->
                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01"
                                key="designation">Designation </label>
                            <select class="form-select mySelect  lang-dropdown select2" id="desigid" name="desigid">
                                <option value='' data-name-en="Select Designation"
                                    data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">Select Designation</option>
                                @if (!empty($designation) && is_iterable($designation))
                                    @foreach ($designation as $department)
                                        <option value="{{ $department->desigcode }}">
                                            {{ $department->desigelname }}
                                        </option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault02" key="ifhrmsid">IFHRMS
                                ID</label>
                            <input type="text" class="form-control only_numbers" id="ifhrmsno" name="ifhrmsno"
                                data-placeholder-key="ifhrmsno" maxlength="11"/>
                        </div>


                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01" key="name">Name
                            </label>
                            <!-- <input type="text" class="form-control name" id="username" name="username"
                                data-placeholder-key="username" oninput ="capitalizeFirstLetter('username')" /> -->
                            <input type="text" class="form-control name" id="user_name" name="user_name"
                                data-placeholder-key="username" oninput ="capitalizeFirstLetter('username')" maxlength = "100"/>
                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01"
                                key="usertamilname">User
                                Tamil Name
                            </label>
                            <input type="text" class="form-control name" id="usertamilname" name="usertamilname"
                                data-placeholder-key="usertamilname" maxlength = "200"/>
                        </div>



                    </div>

                    <div class="row">

                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault02" key="dateofbirth">Date
                                Of
                                birth</label>
                            <!-- <input type="date" class="form-control"   id="dob" name="dob"/> -->
                            <div class="input-group" onclick="datepicker('dob','')" onchange="onchangedob()">
                                <input type="text" class="form-control datepicker" id="dob" name="dob"
                                    placeholder="dd/mm/yyyy" />
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>
                        </div>


                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault02" key="doj">Date Of
                                Joining</label>
                            <!-- <input type="date" class="form-control" id ="doj" name="doj" /> -->
                            <div class="input-group" onclick="datepicker('doj','')">
                                <input type="text" class="form-control datepicker" id="doj" name="doj"
                                    placeholder="dd/mm/yyyy" />
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>

                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefaultUsername"
                                key="dor">Date Of
                                retirement </label>
                            <!-- <input type="date" class="form-control" id="dor" name="dor" /> -->
                            <!-- <div class="input-group" onclick="datepicker('dor','')">
                                                    <input type="text" class="form-control datepicker" id="dor" name="dor"
                                                        placeholder="dd/mm/yyyy"  disabled/>
                                                    <span class="input-group-text">
                                                        <i class="ti ti-calendar fs-5"></i>
                                                    </span>
                                                </div> -->
                            <input type="text" class="form-control " id="dor" name="dor"
                                placeholder="dd/mm/yyyy" disabled />
                        </div>


                    </div>
                    <div class="row">

                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefaultUsername"
                                key="gender">Gender</label>
                            <select class="form-select  lang-dropdown" id="gendercode" name="gendercode">
                                <option value='' data-name-en="Select Gender"
                                    data-name-ta="பாலினத்தைத் தேர்ந்தெடுக்கவும்">Select Gender</option>
                                <option value="M" data-name-en="Male" data-name-ta="ஆண்">Male</option>
                                <option value="F" data-name-en="Female" data-name-ta="பெண்">Female</option>
                                <option value="T" data-name-en="Transgender" data-name-ta="திருநங்கை">
                                    Transgender</option>
                            </select>

                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefaultUsername"
                                key="mobile">Mobile Number</label>
                            <input type="text" class="form-control only_numbers" id="mobilenumber"
                                name="mobilenumber" data-placeholder-key="mobilenumber" maxlength = "10" required />
                        </div>



                        <div class="col-md-4 ">
                            <label class="form-label required lang" for="validationDefault01" key="email">Email
                            </label>
                            <input type="text" class="form-control" id="email" name="email"
                                data-placeholder-key="email" maxlength="100" required />
                        </div>

 <div  class="col-md-4">
                    <label class="form-label required lang" key="">Psa List</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3 mb-3">
                                <input class="form-check-input" type="radio" name="psaflag"
                                    id="psaflagYes" value="Y" >
                                <label class="form-check-label lang" key="statusyes" for="psaflagYes">
                                    Yes
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="psaflag"
                                    id="psaflagNo" value="N">
                                <label class="form-check-label lang" key="statusno" for="psaflagNo">
                                    No
                                </label>
                            </div>
                        </div>
                    </div>


<div  class="col-md-4">
<label class="form-label required lang" key="reservelist">Reserve List</label>
<div class="d-flex align-items-center">
	<div class="form-check me-3 mb-3">
		<input class="form-check-input" type="radio" name="reservelist"
			id="reservelistYes" value="Y" checked>
		<label class="form-check-label lang" key="statusyes" for="reservelistYes">
			Yes
		</label>
	</div>
	<div class="form-check mb-3">
		<input class="form-check-input" type="radio" name="reservelist"
			id="reservelistNo" value="N">
		<label class="form-check-label lang" key="statusno" for="reservelistNo">
			No
		</label>
	</div>
</div>
</div>



                        <!-- <div class="col-md-4 ">
                                                <label class="form-label required lang" for="validationDefault01" key="auditor">Auditor </label>
                                                <select class="form-select " id="auditorflag" name="auditorflag">
                                                    <option value=''>Select Auditor</option>
                                                    <option value="Y">Yes</option>
                                                    <option value="N">No</option>

                                                </select>
                                            </div> -->

                    </div>



                    <div class="row mt-3 text-center">
                        <div class="col-md-6  mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />

                            <button type="submit" name="buttonaction" action="insert" id="buttonaction"
                                class="btn button_save">Save</button>
                            <button type="button" class="btn btn-danger" id="reset_button">clear</button>

                        </div>

                    </div>

                </form>
            </div>
        </div>


        <div class="card ">
            <div class="card-header card_header_color lang" key="createUser_table">Auditor Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this " id="tableshow">
                        <table id="usertable"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                            <tr>
                                <th class="lang text-center align-middle" key="s_no">S.No</th>
                                <th class="lang text-center align-middle" key="department">Department</th>
                                <th class="lang text-center align-middle" key="district">District</th>
                                <th class="lang text-center align-middle" key="designation">Designation</th>
                                <th class="lang text-center align-middle" key="userdetails">User Details</th>
                                <th class="lang text-center align-middle" key="email">Email</th>
                                <th class="lang text-center align-middle" key="mobile">Mobile Number</th>
                                 <th class="lang text-center align-middle" key="">PSA Flag</th>
				 <th class="lang text-center align-middle" key="reservelist">Reserve List</th>
                                <th class="all lang text-center align-middle" key="action">Action</th>
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

<!-- <script src="../assets/js/vendor.min.js"></script>  -->

<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!--------------------------Select2-------------------------------------------------------------->
<!--
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
 -->

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!--------------------------Select2 End-------------------------------------------------------------->

<!-- ----------------------------------Download Button--------------------------- -->
<!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script> -->
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<!-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> -->
<!-- ----------------------------------End Download Button--------------------------- -->

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../common/ajaxfn.js"></script>

<script>
 let dga_roletypecode = '<?php echo $dga_roletypecode; ?>';
    let Admin_roletypecode = '<?php echo $Admin_roletypecode; ?>';
    let Dist_roletypecode = '<?php echo $Dist_roletypecode; ?>';
    let Ho_roletypecode = '<?php echo $Ho_roletypecode; ?>';
    let Re_roletypecode = '<?php echo $Re_roletypecode; ?>';
    let chargeroleTypeCode = '<?php echo $roleTypeCode; ?>';
    let chargedeptcode = '<?php echo $deptcode; ?>';
    let chargedistcode = '<?php echo $distcode; ?>';
    let validator; // Declare globally so it can be accessed anywhere


    var session_roletypecode = '<?php echo $roleTypeCode; ?>';
    let table;
    let dataFromServer = []; // Global variable to store fetched data

    $(document).ready(function() {
        // $('#createuser')[0].reset();
        // getbuttontext()
        // reset_form()
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        // Load the initial language and initialize the DataTable
        const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en' if no language is set
        initializeDataTable(lang);

        // Change event for language selection dropdown
        $('#translate').change(function() {
            updateTableLanguage(getLanguage(
                'Y')); // Update the table with the new language by destroying and recreating it
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'createuser');
        });
    });

    function getRetirementDay() {
        dob = $('#dob').val();
        const [day, month, year] = dob.split('/').map(Number);
        const date = new Date(year, month - 1, day);

        function getMonthEnd(year, month) {
            return new Date(year, month + 1, 0); // 0 means last day of the month
        }

        let resultDate;
        if (day === 1) {
            const prevMonthEnd = getMonthEnd(date.getFullYear(), date.getMonth() - 1);
            resultDate = new Date(prevMonthEnd.setFullYear(prevMonthEnd.getFullYear() + 60));
        } else {
            const currentMonthEnd = getMonthEnd(date.getFullYear(), date.getMonth());
            resultDate = new Date(currentMonthEnd.setFullYear(currentMonthEnd.getFullYear() + 60));
        }

        const dayFormatted = String(resultDate.getDate()).padStart(2, '0');
        const monthFormatted = String(resultDate.getMonth() + 1).padStart(2, '0');
        const yearFormatted = resultDate.getFullYear();

        retirementdate = `${dayFormatted}/${monthFormatted}/${yearFormatted}`;
        $('#dor').val(retirementdate);
    }

    function onchangedob() {
        if ($('#doj').val()) {
            $('#doj').datepicker('setDate', '');
            $('#doj').val('');
            $('#dor').val('');
        }
        getRetirementDay()
    }

    // function datepicker(value, setdate) {
    //     var today = new Date();
    //     var minDate, maxDate;

    //     if (value === 'dob') {
    //         maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    //         minDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
    //     }

    //     if (value === 'doj') {
    //         var dob = $('#dob').val();
    //         // alert(dob);
           
    //         if (dob) {
    //             // alert('jo');
    //             const [day, month, year] = dob.split('/').map(Number);
    //             var dobDate = new Date(year, month - 1, day);
    //             dobDate.setFullYear(dobDate.getFullYear() + 18);
    //             minDate = dobDate;
    //             maxDate = today;
    //         }
    //     }

    //     if (minDate && maxDate) {

    //         var minDateString = formatDate(minDate);
    //         var maxDateString = formatDate(maxDate);
    //         init_datepicker(value, minDateString, maxDateString, setdate);
    //     }
    // }
    function datepicker(value, setdate) {
    var today = new Date();
    var minDate, maxDate;

    if (value === 'dob') {
        maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        minDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
    }

    if (value === 'doj') {
        var dob = $('#dob').val();
        if (dob) {
            // alert('e');
            const [day, month, year] = dob.split('/').map(Number);
            var dobDate = new Date(year, month - 1, day);
            dobDate.setFullYear(dobDate.getFullYear() + 18);
            minDate = dobDate;
            maxDate = today;
        } else {
           
            minDate = today; // Prevent invalid DOJ without a DOB
            maxDate = today;
        }
    }

    if (minDate && maxDate) {
        // alert(minDate)
        // alert(maxDate)
        // alert(setdate)
        var minDateString = formatDate(minDate);
        var maxDateString = formatDate(maxDate);
        init_datepicker(value, minDateString, maxDateString, setdate);
    }
}

// Helper function to format dates as dd/mm/yyyy
function formatDate(date) {
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();
    return `${day}/${month}/${year}`;
}


    function onchangeDept() {
        getDistrictbasedonDept('', '');
        getDesignationBasedonDept('', '', 'createuser', 'deptcode', 'desigid')
    }


    function getDistrictbasedonDept(deptcode, editdistcode) {
        const lang = getLanguage();

        const defaultOption = `
                    <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">
                        ${lang === 'ta' ? 'மாவட்டத்தை தேர்ந்தெடுக்கவும்' : 'Select District'}
                    </option>`;
        const $dropdown = $("#distcode");

        if (!deptcode) deptcode = $('#deptcode').val();

        if (deptcode) {
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getRegionDistInstBasedOnDept',
                type: 'POST',
                data: {
                    deptcode: deptcode,
                    'page': 'createuser',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;
                        response.data.forEach(({
                            distcode: code,
                            distename: nameEn,
                            disttname: nameTa
                        }) => {
                            const isSelected = code === editdistcode ? "selected" : "";
                            language = getLanguage()
                            const displayName = language === "ta" ? nameTa :
                                nameEn; // Choose name based on selected language
                            options += `<option value="${code}" ${isSelected}
                                            data-name-en="${nameEn}"
                                            data-name-ta="${nameTa}">
                                            ${displayName}
                                        </option>`;
                        });
                        $dropdown.html(options);
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    let errorMessage = response.error;
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                }
            });

        } else {
            $dropdown.html(defaultOption);
        }
    }



    ///////////////////////////////////////  Fetching Data ///////////////////////////////////////

    function initializeDataTable(language) {
        $.ajax({
            url: "/fetchAllData",
            type: "POST",
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
    
    
function renderTable(language) {
    const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
    const districtColumn = language === 'ta' ? 'disttname' : 'distename';
    const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
    const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';
            
    if ($.fn.DataTable.isDataTable('#usertable')) {
        $('#usertable').DataTable().clear().destroy();
    }

    const table = $('#usertable').DataTable({
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
                className: 'text-wrap text-end',
                type: "num"
            },
            {
                data: departmentColumn,
                title: columnLabels?.[departmentColumn]?.[language],
                render: function (data, type, row) {
                    return row[departmentColumn] || '-';
                },
                className: 'text-wrap text-start'
            },
            {
                data: districtColumn,
                title: columnLabels?.[districtColumn]?.[language],
                className: "d-none d-md-table-cell lang  text-wrap",
                render: function (data, type, row) {
                    return row[districtColumn] || '-';
                }
            },
            {
                data: designationColumn,
                title: columnLabels?.[designationColumn]?.[language],
                className: "d-none d-md-table-cell lang  text-wrap",
                render: function (data, type, row) {
                    return row[designationColumn] || '-';
                }
            },
                    {
            data: usernameColumn,
            render: function (data, type, row) {
                let dob = row.dob ? new Date(row.dob).toLocaleDateString('en-GB') : "N/A";
                let doj = row.doj ? new Date(row.doj).toLocaleDateString('en-GB') : "N/A";
                let dor = row.dor ? new Date(row.dor).toLocaleDateString('en-GB') : "N/A";

                // Define translations based on language selection
                const translations = {
                    en: { name: "Name", ifhrmsno: "IFHRMS No", dob: "Date of Birth",doj: "DOJ",dor: "DOR" },
                    ta: { name: "பெயர்", ifhrmsno: "IFHRMS எண்", dob: "பிறந்த தேதி",doj: "சேர்ந்த தேதி",dor: "ஓய்வு பெறும் தேதி" }
                };

                const lang = language === "ta" ? "ta" : "en"; // Ensure fallback to English if not Tamil

                return `<b>${translations[lang].name}</b>: ${data} <br> 
                        <small><b>${translations[lang].ifhrmsno} : </b>${row.ifhrmsno}</small> <br> 
                        <small><b>${translations[lang].dob} :</b> ${dob}</small><br>
                         <small><b>${translations[lang].doj} :</b> ${doj}</small><br>
                         <small><b>${translations[lang].dor} :</b> ${dor}</small>`;
            },
            className: "d-none d-md-table-cell lang  "
        },

            {
                data: "email",
                title: columnLabels?.["email"]?.[language],
                className: "d-none d-md-table-cell lang  text-wrap",
                render: function (data, type, row) {
                    return row.email || '-';
                }
            },
            {
                data: "mobilenumber",
                title: columnLabels?.["mobilenumber"]?.[language],
                className: "d-none d-md-table-cell lang  text-wrap",
                render: function (data, type, row) {
                    return row.mobilenumber || '-';
                }
            },
{
            data: "psaflag",
            title: columnLabels?.["psaflag"]?.[language],
            render: function(data) {

                let activeText = arrLang?.[language]?.["yes"] || 'Yes';
                let inactiveText = arrLang?.[language]?.["no"] || 'No';
                let naText = arrLang?.[language]?.["na"] || '-';

                if (data === 'Y') {
                    return `<span class="btn btn-success btn-sm">${activeText}</span>`;
                }
                else if (data === 'N') {
                    return `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: #fff;">${inactiveText}</span>`;
                }
                else {  // null, undefined, empty
                    return `<span class="btn btn-secondary btn-sm">${naText}</span>`;
                }
            },
            className: "text-center d-none d-md-table-cell text-wrap"
        },

                                {
                    data: "reservelist",
                    title: columnLabels?.["reservelist"]?.[language],
                    render: function(data) {
                    let activeText = arrLang?.[language]?.["yes"] || 'Yes';
                    let inactiveText = arrLang?.[language]?.["no"] || 'No';

                    return data === 'Y'
                    ? `<span class="btn btn-success btn-sm">${activeText}</span>`
                    : `<span class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell text-wrap"
                    },
            {
                data: "encrypted_userid",
                title: columnLabels?.["encrypted_userid"]?.[language] ,
                render: (data) =>
                    `<center><a class="btn editicon edit_user" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                className: "text-center text-wrap noExport"
            }
        ],
        // columnDefs: [
        //     {
        //         render: function (data) {
        //             return `<div class='text-wrap width-200'>${data}</div>`;
        //         },
        //         targets: 1
        //     },
        //     {
        //         render: function (data) {
        //             return `<div class='text-wrap width-100'>${data}</div>`;
        //         },
        //         targets: [2, 3] // Setting width for columns
        //     }
        // ],
        // initComplete: function () {
        //     $("#createuser").wrap("<div style='overflow:hidden; width:100%;position:relative;'></div>");
        // }
    });

    const mobileColumns = [designationColumn,districtColumn,usernameColumn, "ifhrmsno", "dob","doj","dor", "email", "mobilenumber","reservelist"];
setupMobileRowToggle(mobileColumns);


    updatedatatable(language, "usertable"); // Update table with correct language
}
function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ✅ Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]["datatable"][titleKey] || "Default Title";

    // ✅ Fetch column headers from the JSON layout
    let dtText = dataTables[language]["datatable"] || dataTables["en"]["datatable"];

    const columnMap = {
        department: language === 'ta' ? 'depttsname' : 'deptesname',
        designation: language === 'ta' ? 'desigtsname' : 'desigesname',
        district: language === 'ta' ? 'disttname' : 'distename'
    };

    // ✅ Dynamically Fetch Translated Column Names
    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["district"] || "District", key: "district" },
        { header: dtText["designation"] || "Designation", key: "designation" },
        { header: dtText["username"] || "Name", key: "username" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },
        { header: dtText["dob"] || "Date of Birth", key: "dob" },
        { header: dtText["doj"] || "Date of Joining", key: "doj" },
        { header: dtText["dor"] || "Date of Retirement", key: "dor" },
        { header: dtText["Email"] || "Email", key: "email" },
        { header: dtText["mobilenumber"] || "Mobile Number", key: "mobilenumber" },
        { header: dtText["reservelist"] || "Wheather Field Audit include", key: "reservelist" },
	{ header: dtText["psaflag"] || "PSA Flag", key: "psaflag" }
    ];

    // ✅ Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");
        let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        return {
            department: rowData[columnMap.department] || "-",
            district: rowData[columnMap.district] || "-",
            designation: rowData[columnMap.designation] || "-",
            username: rowData.username || extractText(row[3]) || "-",
            ifhrmsno: rowData.ifhrmsno || extractIfhrms(row[3]) || "-",
            dob: rowData.dob || extractDob(row[3]) || "N/A",
            doj: rowData.doj || extractDoj(row[3]) || "N/A",
            dor: rowData.dor || extractDor(row[3]) || "N/A",
            email: extractText(row[5]) || "-",
            mobilenumber: extractText(row[6]) || "-",
             reservelist: rowData.reservelist === 'Y' ? 'Yes' : rowData.reservelist === 'N' ? 'No' : "-",
             psaflag: rowData.psaflag === 'Y' ? 'Yes' : rowData.psaflag === 'N' ? 'No' : "-"
 
        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add Headers Correctly
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });

    // ✅ Ensure Data Aligns with Headers
    XLSX.utils.sheet_add_json(ws, excelData, {
        skipHeader: true,
        origin: "A2",
        header: headers.map(h => h.key)
    });

    XLSX.utils.book_append_sheet(wb, ws, translatedTitle);
    XLSX.writeFile(wb, `${translatedTitle}.xlsx`); // ✅ Export with Translated File Name
}

// Utility functions to extract text & clean up data
function extractText(html) {
    return $("<div>").html(html).text().trim();
}

function extractIfhrms(html) {
    let match = html.match(/<small><b>IFHRMS No : <\/b>(\d+)<\/small>/);
    return match ? match[1] : null;
}

function extractDob(html) {
    let match = html.match(/<small><b>DOB :<\/b> ([\d\/]+)/);
    return match ? match[1] : null;
}

function extractDoj(html) {
    let match = html.match(/<small><b>DOJ :<\/b> ([\d\/]+)/);
    return match ? match[1] : null;
}

function extractDor(html) {
    let match = html.match(/<small><b>DOR :<\/b> ([\d\/]+)/);
    return match ? match[1] : null;
}




//     function renderTable(language) {
//         const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
//         const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
//         const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';


//         if ($.fn.DataTable.isDataTable('#usertable')) {
//             $('#usertable').DataTable().clear().destroy();
//         }

//         table = $('#usertable').DataTable({
//             "processing": true,
//             "serverSide": false,
//             "lengthChange": false,
//             "data": dataFromServer,
//             "columns": [{
//                     "data": null,
//                     "render": function(data, type, row, meta) {
//                         return meta.row + 1;
//                     }
//                 },
//                 {
//                     "data": departmentColumn
//                 },
//                 {
//                     "data": designationColumn
//                 },
//                 {
//                     "data": usernameColumn,
//                     "render": function(data, type, row) {
//                         let dob = row.dob ? new Date(row.dob).toLocaleDateString('en-GB') : "N/A";
//                         return `<b>Name</b>: ${data} <br> <small><b>IFHRMS No : </b>${row.ifhrmsno}</small> <br> <small><b>DOB :</b> ${dob}</small>`;
//                     }
//                 },
//                 {
//                     "data": "email"
//                 },
//                 {
//                     "data": "mobilenumber"
//                 },
// {
// data: "reservelist",
// title: columnLabels?.["reservelist"]?.[language],
// render: function(data) {
// let activeText = arrLang?.[language]?.["yes"] || 'Yes';
// let inactiveText = arrLang?.[language]?.["no"] || 'No';

// return data === 'Y'
// ? `<span class="btn btn-success btn-sm">${activeText}</span>`
// : `<span class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">${inactiveText}</span>`;
// },
// className: "text-center d-none d-md-table-cell  noExport text-wrap"
// },

//                 {
//                     "data": "encrypted_userid",
//                     "render": function(data, type, row) {
//                         return `<center><a class="btn editicon edit_user" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
//                     }
//                 }
//             ],

//             "columnDefs": [{
//                     render: function(data) {
//                         return "<div class='text-wrap width-200'>" + data + "</div>";
//                     },
//                     targets: 1
//                 },
//                 {
//                     render: function(data) {
//                         return "<div class='text-wrap width-100'>" + data + "</div>";
//                     },
//                     targets: [2, 3] // Set width for columns
//                 }
//             ],
//             "initComplete": function(settings, json) {
//                 $("#createuser").wrap(
//                     "<div style='overflow:hidden; width:100%;position:relative;'></div>");
//             },
//             "dom": '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
//             "buttons": [{
//                 extend: "excelHtml5",
//                 text: window.innerWidth > 768 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
//                     '<i class="fas fa-download"></i>',
//                 title: 'Create User Report',
//                 exportOptions: {
//                     columns: ':not(:last-child)' // Excluding the last column (Action column)
//                 },
//                 className: window.innerWidth > 768 ? 'btn btn-info' :
//                     'btn btn-info btn-sm' // Full button on desktop, smaller on mobile
//             }],
//             "pagingType": "simple_numbers",
//             "responsive": true,
//             "pageLength": 10,
//             "lengthMenu": [
//                 [10, 50, -1], // Full options for Desktop
//                 [10, 25, 50, -1] // Compressed options for Mobile
//             ],
//             "fnDrawCallback": function() {
//                 let $pagination = $('.dataTables_paginate');
//                 let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

//                 // Function to adjust pagination and info text based on window width
//                 function adjustView() {
//                     if ($(window).width() <= 768) {
//                         // Mobile View Adjustments
//                         $(".dataTables_filter input").css({
//                             "width": "100px",
//                             "font-size": "12px",
//                             "padding": "4px"
//                         }); // Smaller search box
//                         $(".dt-buttons .btn").addClass("btn-sm"); // Smaller download button

//                         // Compress pagination display to show only first & last buttons
//                         let totalPages = $pages.length;
//                         $pages.each(function(index) {
//                             if (index !== 0 && index !== totalPages - 1) {
//                                 $(this).hide();
//                             }
//                         });

//                         // Display "Showing x to y of z entries" on a separate row in mobile view
//                         $(".dataTables_info").css("display", "block");
//                         $(".dataTables_info").css("text-align", "center");
//                         $(".dataTables_info").css("margin-bottom",
//                             "20px"); // Optional: Align the text to center
//                     } else {
//                         // Desktop View Adjustments
//                         $(".dataTables_info").css("display", "inline-block");
//                         $(".dataTables_filter input").css({
//                             "width": "auto",
//                             "font-size": "14px",
//                             "padding": "8px"
//                         }); // Reset search box style
//                         $(".dt-buttons .btn").removeClass("btn-sm"); // Reset download button size
//                         // Show all pagination buttons
//                         $pages.show();
//                     }
//                 }

//                 // Call the function initially
//                 adjustView();

//                 // Call the function when the window is resized
//                 $(window).resize(function() {
//                     adjustView();
//                 });
//             }
//         });

//         $(window).resize(function() {
//             table.buttons(0).text(
//                 window.innerWidth > 768 ?
//                 '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
//                 '<i class="fas fa-download"></i>'
//             );
//         });

//     }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#usertable')) {
            $('#usertable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    ///////////////////////////////////////  Fetching Data ///////////////////////////////////////

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#createuser").validate({

            rules: {
                deptcode: {
                    required: true
                },
                desigid: {
                    required: true
                },
                ifhrmsno: {
                    required: true
                },
                user_name: {
                    required: true
                },
                usertamilname: {
                    required: true
                },
                dob: {
                    required: true
                },
                gendercode: {
                    required: true
                },
                doj: {
                    required: true
                },
                dor: {
                    required: true
                },
                auditorflag: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobilenumber: {
                    required: true,
                    digits: true,
                    minlength: 10
                },
reservelist: {
                    required: true,
                },
   psaflag: {
                    required: true,
                }
			
                // distcode: { required: true }
            },
            // errorPlacement: function(error, element) {
            //     if (element.hasClass('select2')) {
            //         // Insert the error message below the select2 dropdown container
            //         error.insertAfter(element.next('.select2-container'));
            //     } else {
            //         // For other fields, insert the error message after the element itself
            //         error.insertAfter(element);
            //     }
            // },
           

          errorPlacement: function(error, element) {
                if (element.attr("name") === "psaflag") {
                error.insertAfter(element.closest('.d-flex'));
            }
               else if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.closest(".input-group").length) {
                    // If the element is inside an input-group, place the error after the entire group
                    error.insertAfter(element.closest(".input-group"));
                } else {
                    // Otherwise, insert after the element itself
                    error.insertAfter(element);
                }
            }, messages: errorMessages[language], // Set initial messages


            // messages: {
            //     deptcode: { required: "Select department name" },
            //     ifhrmsno: { required: "Enter ifhrms number" },
            //     username: { required: "Enter username" },
            //     desigid: { required: "Select designation" },
            //     dob: { required: "Select date of birth" },
            //     gendercode: { required: "Select gender" },
            //     dor: { required: "Select date of relieving" },
            //     doj: { required: "Select date of joining" },
            //     auditorflag: { required: "Select auditorflag" },
            //     email: { required: "Enter an email address.", email: "Enter a valid email address." },
            //     mobilenumber: { required: "Enter your phone number.", digits: "Enter a valid phone number.", minlength: "Your phone number must be at least 10 digits long." },
            //     distcode: { required: "Select District" }
            // },
            // errorPlacement: function(error, element) {
            //     if (element.hasClass('datepicker')) {
            //         error.insertAfter(element.closest('.input-group'));
            //     } else {
            //         error.insertAfter(element);
            //     }
            // },

            // messages: errorMessages[language], // Set initial messages
            // errorPlacement: function(error, element) {
            //     if (element.closest(".input-group").length) {
            //         // If it is, place the error after the input-group
            //         error.insertAfter(element.closest(".input-group"));
            //     } else {
            //         // Otherwise, just insert after the element as usual
            //         error.insertAfter(element);
            //     }
            // },


            submitHandler: function(form) {
                var formData = $('#createuser').serializeArray();
                formData.push({
                    name: 'dor',
                    value: $('#dor').val()
                });
var chargdeptcode = '<?php echo $deptcode; ?>';
  
  
   let deptstatus = 'N';
                let diststatus = 'N';


                if (chargeroleTypeCode == Dist_roletypecode || chargeroleTypeCode == Ho_roletypecode || chargeroleTypeCode == Re_roletypecode) {
                    deptstatus = 'Y'
                }
                if (chargeroleTypeCode == Dist_roletypecode) {
                    diststatus = 'Y'
                }

                if (deptstatus == 'N') {
                    if ($('#deptcode').prop('disabled')) {

                        formData.push({
                            name: 'deptcode',
                            value: $('#deptcode').val()
                        });
                    }
                }


                if (diststatus == 'N') {
                    if ($('#distcode').prop('disabled')) {
                        formData.push({
                            name: 'distcode',
                            value: $('#distcode').val()
                        });
                    }
                }


                if ($('#desigid').prop('disabled')) {
                    formData.push({
                        name: 'desigid',
                        value: $('#desigid').val()
                    });
                }
                $.ajax({
                    url: '/insert',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form();

                            // getLabels_jsonlayout([{
                            //     id: 'usercreated',
                            //     key: 'usercreated'
                            // }], 'N').then((text) => {
                            //     passing_alert_value('Confirmation', text
                            //         .usercreated, 'confirmation_alert',
                            //         'alert_header', 'alert_body',
                            //         'confirmation_alert');
                            // });


                            getLabels_jsonlayout([{
                                id: response.success,
                                key: response.success
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object
                                    .values(text)[0], 'confirmation_alert',
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
                                passing_alert_value('Confirmation',
                                    alertMessage, 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
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





    // function reset_form() {
    
    //     $('#display_error').hide();
    //     // validator.resetForm();
    //     // $("#createuser .error").removeClass("error");
    //     // change_button_as_insert('createuser', 'action', 'buttonaction', 'reset_button','display_error', @json($savebtn), @json($clearbtn));
    //     changeButtonActionwithoutformrefresh('createuser', 'action', 'buttonaction', 'reset_button', 'display_error',
    //         @json($savebtn), @json($clearbtn), @json($insert));
    //     var lang = getLanguage();

    //     $('#distcode').html(
    //         `<option value='' data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'மாவட்டத்தை தேர்வு செய்' : 'Select District'}</option>`
    //     );
    //     $('#desigid').html(
    //         `<option value='' data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}</option>`
    //     );

    //     change_lang_for_page(lang);

    //     if (
    //         session_roletypecode == '<?php echo $Ho_roletypecode; ?>' ||
    //         session_roletypecode == '<?php echo $Re_roletypecode; ?>' ||
    //         session_roletypecode == '<?php echo $Dist_roletypecode; ?>'
    //     ) {
    //         makedropdownempty('instmappingcode', 'Select Institution');

    //         if (session_roletypecode == '<?php echo $Ho_roletypecode; ?>') {
    //             makedropdownempty('regioncode', 'Select Region');
    //             makedropdownempty('distcode', 'Select District');
    //         } else if (session_roletypecode == '<?php echo $Re_roletypecode; ?>') {
    //             makedropdownempty('distcode', 'Select District');
    //         }
    //         $('#roletypecode').val('');
    //         $('#desigcode').val('');
    //     } else {
    //         $('#chargeform')[0].reset();
    //         // $('#roletypecode, #instmappingcode, #regioncode, #distcode, #desigcode').val('').change();
    //         resetDropdowns(lang); // Dynamically apply selected language
    //         // makedropdownempty('roletypecode', 'Select RoleType');
    //         // makedropdownempty('instmappingcode',  lang === 'ta' ? 'நிறுவனத்தை தேர்ந்தெடுக்கவும்' : 'Select Institution');
    //         // makedropdownempty('regioncode', 'Select Region');
    //         // makedropdownempty('distcode', 'Select District');
    //         // makedropdownempty('desigcode', 'Select Designation');
    //     }

    //     //$('#deptcode').val('').trigger('change');

    //     $('#roletypecode').val('');
    //     $('#chargeid').val('');
    //     $('#desigcode').val('');


    //     $('#chargedescription').val('');

    //     $('#display_error').hide();

    //     // makedropdownempty('roleactioncode', 'Select Role Action');

    //     $('#chargeform').validate().resetForm();
    //     changeButtonAction('chargeform', 'action', 'buttonaction', 'reset_button', 'display_error',
    //         @json($savebtn), @json($clearbtn), @json($insert));
    //     // change_button_as_insert('chargeform', 'action', 'buttonaction', 'display_error', '', '');
    //     updateSelectColorByValue(document.querySelectorAll(".form-select"));


    //     change_lang_for_page(lang);

    //     if ($('#roletypecode').val() == "") {
    //         $('#distdiv').hide();
    //         $('#regiondiv').hide();
    //         $('#instdiv').hide();
    //     }
    // }


    function reset_form() {
        $('#display_error').hide();
        // validator.resetForm();
        // $("#createuser .error").removeClass("error");
        // change_button_as_insert('createuser', 'action', 'buttonaction', 'reset_button','display_error', @json($savebtn), @json($clearbtn));
        changeButtonActionwithoutformrefresh('createuser', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert));
            
        var lang = getLanguage();

        $("#doj").datepicker("setDate", null); 
        $("#dob").datepicker("setDate", null); 
        $('input[name="psaflag"]').prop('checked', false);

        //$('#deptcode').val(''); // Reset Department
    
        change_lang_for_page(lang); // Apply language change
 	if ($('#desigid').prop('disabled')) {
            $('#desigid').attr('disabled', false);
        }

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        if (session_roletypecode == '<?php echo $dga_roletypecode; ?>' || session_roletypecode == '<?php echo $Admin_roletypecode; ?>') {

	 if ($('#deptcode,#distcode').prop('disabled')) {
                $('#deptcode,#distcode').attr('disabled', false);
            }
            // makedropdownempty('desigid', 'Select Designation');
            $('#distcode').html(
            `<option value='' data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'மாவட்டத்தை தேர்வு செய்' : 'Select District'}</option>`
            );
            $('#desigid').html(
                `<option value='' data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}</option>`
            );
                $('#deptcode').select2('destroy');

                // Clear the value
                $('#deptcode').val(null);

                // Reinitialize the Select2 (no events will be triggered)
                $('#deptcode').select2();
        } 
        else 
        {
            $('#distcode').val('');
            // Destroy the Select2 instance (removes all event handlers and functionality)
            $('#desigid').select2('destroy');

            // Clear the value
            $('#desigid').val(null);

            // Reinitialize the Select2 (no events will be triggered)
            $('#desigid').select2();

            if(!(session_roletypecode == '<?php echo $Dist_roletypecode; ?>'))
            {

if ($('#distcode').prop('disabled')) {
                    $('#distcode').attr('disabled', false);
                }
                $('#distcode').select2('destroy');

                // Clear the value
                $('#distcode').val(null);

                // Reinitialize the Select2 (no events will be triggered)
                $('#distcode').select2();
            }

            
        }

        $('#ifhrmsno').val('');
        $('#user_name').val('');
        $('#usertamilname').val('');
        $('#usertamilname').val('');
        $('#dob').val('');
        $('#doj').val('');
        $('#dor').val('');
        $('#gendercode').val('');
        $('#mobilenumber').val('');
        $('#email').val('');
        $('#desigid').val('');
    }

    $('#reset_button').on('click', function() {
        reset_form();
    });

    $(document).on('click', '.edit_user', function() {
        var id = $(this).attr('id');
        if (id) {
            reset_form();
            getuserdetail(id);
        }
    });

    function getuserdetail(deptuserid) {
        $.ajax({
            url: '/fetchUserData',
            method: 'POST',
            data: {
                userid: deptuserid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // alert('jo');
                    $('#display_error').hide();
                    // validator.resetForm();
                    // change_button_as_update('createuser', 'action', 'buttonaction', 'reset_button','display_error', @json($updatebtn), @json($clearbtn));
                    changeButtonActionwithoutformrefresh('createuser', 'action', 'buttonaction',
                        'reset_button',
                        'display_error', @json($updatebtn), @json($clearbtn),
                        @json($update))

                    var user = response.data[0];
                    $('#gendercode').val(user.gendercode);
                    // $('#auditorflag').val(user.auditorflag);
                    $('#userid').val(user.encrypted_userid);

                    if (user.dob) {
                        $('#dob').val(convertDateFormatYmd_ddmmyy(user.dob)); // Set the DOB field
                        datepicker('dob', convertDateFormatYmd_ddmmyy(user.dob)); // Initialize datepicker for DOB
                    }

                    // alert(user.username);
                    // alert( $('#username').val());
                    $('#user_name').val(user.username);
                    $('#usertamilname').val(user.usertamilname);
                    $('#email').val(user.email);
                    // $('#deptcode').val(user.deptcode).trigger('change');
                    $('#ifhrmsno').val(user.ifhrmsno);
                    $('#mobilenumber').val(user.mobilenumber);
                    populatepsaflag(user.psaflag);
populatereservelist(user.reservelist);

                    // if (user.dob) {
                    //     datepicker('dob', convertDateFormatYmd_ddmmyy(user.dob));
                    // }

                    // // alert(user.doj);
                    // if (user.doj) {
                    //     datepicker('doj', convertDateFormatYmd_ddmmyy(user.doj));
                    // }
                    

   

                    if (user.doj) {
                            // alert('jo');
                                $('#doj').val(convertDateFormatYmd_ddmmyy(user.doj)); // Set the DOJ field
                                datepicker('doj', convertDateFormatYmd_ddmmyy(user.doj)); // Initialize datepicker for DOJ
                                // $('#doj').val(user.doj)
                                $("#doj").datepicker("setDate", convertDateFormatYmd_ddmmyy(user.doj));
                            }

                    // datepicker('dob', convertDateFormatYmd_ddmmyy(user.dob));
                    // datepicker('doj', convertDateFormatYmd_ddmmyy(user.doj));

                    if (session_roletypecode == '<?php echo $dga_roletypecode; ?>' || session_roletypecode ==
                        '<?php echo $Admin_roletypecode; ?>') {
                        getDesignationBasedonDept(user.deptcode, user.desigcode, 'createuser', 'deptcode',
                            'desigid');
                        getDistrictbasedonDept(user.deptcode, user.distcode);

                        
                        $('#deptcode').select2('destroy');

                        // Clear the value
                        $('#deptcode').val(user.deptcode);

                        // Reinitialize the Select2 (no events will be triggered)
                        $('#deptcode').select2();

                        
                    } else {

                        // $('#desigid').val(user.desigcode);
                        // if (user.distcode) $('#distcode').val(user.distcode);


                        // Disable change event temporarily
                        $('#desigid').select2('destroy');  // Temporarily destroy the Select2 instance

                        // Set the value
                        $('#desigid').val(user.desigcode);  // Set the value without triggering events

                        // Reinitialize Select2 after value has been set
                        $('#desigid').select2();

                        $('#distcode').select2('destroy');  // Temporarily destroy the Select2 instance

                        // Set the value
                        $('#distcode').val(user.distcode);  // Set the value without triggering events

                        // Reinitialize Select2 after value has been set
                        $('#distcode').select2();





                    }
		if (user.assignedstatus == 'Y') {

                        $('#deptcode,#distcode,#desigid').attr('disabled', true);
                    }

                    updateSelectColorByValue(document.querySelectorAll(".form-select"));
                } 

 		else {
                    alert('User not found');
                }

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


 function populatereservelist(reservelist) {
        if (reservelist === "Y") {
            document.getElementById('reservelistYes').checked = true;
        } else if (reservelist === "N") {
            document.getElementById('reservelistNo').checked = true;
        }
    }

  
 function populatepsaflag(psaflag) {

    document.getElementById('psaflagYes').checked = false;
    document.getElementById('psaflagNo').checked = false;

    if (psaflag === "Y") {
        document.getElementById('psaflagYes').checked = true;
    }
    else if (psaflag === "N") {
        document.getElementById('psaflagNo').checked = true;
    }
}

    function getroletypecode_basedondept(deptcode, roletypecode) {
        const defaultOption = "<option value=''>Select Role Type</option>";
        const $dropdown = $("#roletypecode");

        if (!deptcode) deptcode = $('#deptcode').val();

        if (deptcode) {
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
                        response.data.forEach(({
                            roletypecode: code,
                            roletypeelname: name
                        }) => {
                            if (code && name) {
                                const isSelected = (code === roletypecode) ? "selected" : "";
                                options += `<option value="${code}" ${isSelected}>${name}</option>`;
                            }
                        });
                        $dropdown.html(options);
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    let errorMessage = response.error;
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                }
            });

        } else {
            $dropdown.html(defaultOption);
        }
    }
</script>
@endsection
