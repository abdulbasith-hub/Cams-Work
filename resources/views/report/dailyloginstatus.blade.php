@section('content')
    @extends('index2')
    @include('common.alert')
   @section('title', 'Reservelist')

   @php
    $sessionmainobjectiondel = session('charge');

    $sessionchargedel = session('charge');

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

    use Carbon\Carbon;
    $today = Carbon::today();
    $daysToShow = 30; // how many days forward
    $holidays = DB::table('audit.mst_holiday')->pluck('holiday_date')->toArray();

  @endphp

   <style>
    html,
    body {
        overflow-x: hidden;
    }
    .available-date {
    background-color: #d4edda !important; /* light green */
    color: #155724 !important;
    font-weight: bold;
  }

  .holiday-date {
    background-color: #f8d7da !important; /* light red */
    color: #721c24 !important;
    font-weight: bold;
  }

 .disabled-date {
    color: #aaa !important;
  }

  </style>

  <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


   <div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="checklist_head">Non-Logged-In Users</div>
            <div class="card-body">
                <form id="checklistform" name="checklistform">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class=" mr-sm-2 select2  lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?>
                                id="deptcode" name="deptcode[]" onchange="getRegionBasedOnDept('');">

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


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
                                    <option disabled>No Departments Available</option>
                                @endif

                            </select>
                        </div>

                        <div class="col-md-4 mb-3"  id="regiondiv">
                            <label class="form-label required lang" key="region" for="region">Region</label>
                            <select class="  mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?>
                                id="regioncode" name="regioncode[]" onchange="getDistrictBasedOnRegion('','')">

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>



                                @if ($regioncode)
                                    @foreach ($region as $reg)
                                        <option value="{{ $reg->regioncode }}"
                                            @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                            data-name-en="{{ $reg->regionename }}"
                                            data-name-ta="{{ $reg->regiontname }}">
                                            {{ $reg->regionename }}
                                        </option>
                                    @endforeach
                                @else
                                @endif

                            </select>
                        </div>

                        <div class="col-md-4 mb-3"  id="districtdiv">
                            <label class="form-label required lang" key="district" for="district">District</label>
                            <select class=" mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?>
                                id="distcode" name="distcode[]">

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>



                                @if ($distcode)
                                    @foreach ($district as $dist)
                                        <option value="{{ $dist->distcode }}"
                                            @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                            data-name-en="{{ $dist->distename }}"
                                            data-name-ta="{{ $dist->disttname }}">
                                            {{ $dist->distename }}
                                        </option>
                                    @endforeach
                                @else
                                @endif

                            </select>
                        </div>

                <div class="col-md-4 " id="dojdiv">
                            <label class="form-label required lang" for="validationDefault02">Current Date</label>
                            <div class="input-group" onclick="datepicker('doj','')">
                                <input type="text" class="form-control datepicker" id="doj" name="doj"
                                    placeholder="dd/mm/yyyy" />
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>

                        </div>



                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <button class="btn button_save mt-3" type="submit" id="buttonaction" name="buttonaction"
                                data-action="insert">Submit</button>

                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>



                </form>
            </div>
        </div>





    </div>

      <div class="card card_border" id="tableheadingshow">
  <div class="card-header card_header_color lang" id="auditor-list-heading" key="checklist_table">
    Non-Logged-In Users Details
  </div>
  <div class="card-body"><br>
    <div class="datatables">
      <div class="table-responsive hide_this" id="tableshow">
        <table id="checkidlelisttable" class="table w-100 table-striped table-bordered display align-middle datatables-basic">
          <thead></thead>
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




   <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
  <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
   <!-- Download Button Start -->

   <script src="../assets/js/download-button/buttons.min.js"></script>
   <script src="../assets/js/download-button/jszip.min.js"></script>
   <script src="../assets/js/download-button/buttons.print.min.js"></script>
  <script src="../assets/js/download-button/buttons.html5.min.js"></script>

   <!-- Download Button End -->

    <!-- Select2 -->
   <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
   <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


   <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
   <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
  <script src="../assets/js/forms/select2.init.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


   <script>

     function initExcludeHolidayDatepicker(inputId, workingDates) {
    // Put all allowed dates into a Set
    const allowedDates = new Set(workingDates);

    $('#' + inputId).datepicker("destroy");

    $('#' + inputId).datepicker({
        dateFormat: 'dd/mm/yy', // UI format
        beforeShowDay: function(date) {
            const dmy = $.datepicker.formatDate('dd/mm/yy', date); // Match against dd/mm/yyyy
            return allowedDates.has(dmy)
                ? [true, "available-date", "Working Date"]
                : [false, "disabled-date", "Holiday/Weekend"];
        }
    });

    $('#' + inputId)
        .closest('.input-group')
        .find('.input-group-text')
        .off('click')
        .on('click', function() {
            $('#' + inputId).datepicker('show');
        });
  }

   $(document).ready(function() {
    // Example: if your backend returns ["15/09/2025", "18/09/2025", "20/09/2025"]
    let workingDates = ["15/09/2025", "18/09/2025", "20/09/2025"];

    initExcludeHolidayDatepicker("doj", workingDates);
  });



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        var validator = $("#checklistform").validate({
            rules: {
                "deptcode[]": {
                    required: true
                },
                "regioncode[]": {
                    required: true
                },
                "distcode[]": {
                    required: true
                },
                "statuscode[]": {
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



        $('.select2').on('change', function() {
            $(this).valid();
        });


    $('#buttonaction').on('click', function (e) {
    e.preventDefault();

    let deptcode   = $("#deptcode").val();
    let region     = $("#regioncode").val();
    let district   = $("#distcode").val();
        let doj  = $('#doj').val();
    let isValid = true;

    $(".error").remove();

    if (!deptcode || deptcode.length === 0) {
        $("#deptdiv").append('<span class="error">Please select Department</span>');
        isValid = false;
    }

    if (!region || region.length === 0) {
        $("#regiondiv").append('<span class="error">Please select Region</span>');
        isValid = false;
    }

    if (!district || district.length === 0) {
        $("#districtdiv").append('<span class="error">Please select District</span>');
        isValid = false;
    }

    if (!doj || doj.length === 0) {
        $("#dojdiv").append('<span class="error">Please select a Date</span>');
        isValid = false;
    }


    if (isValid) {
        initializeDataTable(language, deptcode, region, district);
    }
   });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    function setupAllExclusive(selectId) {
        $(`#${selectId}`).on('change', function() {
            let selected = $(this).val() || [];

            if (selected.includes('A')) {
                // If "All" is selected → keep only "All"
                $(this).val(['A']).trigger('change.select2');

                // Disable other options
                $(`#${selectId} option`).each(function() {
                    if (this.value !== 'A') {
                        $(this).prop('disabled', true);
                    }
                });
            } else {
                // Enable all options if "All" is not selected
                $(`#${selectId} option`).prop('disabled', false);
            }
        });
    }

    // Apply to all your selects
    setupAllExclusive('deptcode');
    setupAllExclusive('regioncode');
    setupAllExclusive('distcode');



    let table;
    let dataFromServer = [];


    $(document).ready(function() {
        $('#checklistform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        getRegionBasedOnDept('');

        var lang = getLanguage();

    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);

        updateValidationMessages(getLanguage('Y'), 'checklistform');
    });


  function initializeDataTable(language, deptcode, region, district) {

    if ($.fn.DataTable.isDataTable('#usertable')) {
        $('#usertable').DataTable().clear().destroy();
    }

    $.ajax({
        url: "{{ route('report.excludeholiday.fetch') }}",
        type: "POST",
        data: { deptcode, region, district },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

        success: function (json) {
            if (json.data.auditors && json.data.auditors.length > 0) {
                $('#tableheadingshow').show();
                $('#tableshow').show();
                $('#no_data').hide();

                // Store auditors globally
                dataFromServer = json.data.auditors;
                renderTable(language);

                // ✅ Initialize datepicker with allowed working dates
                if (json.data.working_dates && json.data.working_dates.length > 0) {
                    initExcludeHolidayDatepicker("doj", json.data.working_dates);
                }

            } else {
                $('#tableshow').hide();
                $('#tableheadingshow').show();
                $('#no_data').show();
            }
        },

        error: function () {
            $('#no_data').show();
            $('#tableshow').hide();
            $('#usertable_wrapper').hide();
        }
    });
    }


  function renderTable(language) {

    const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
    const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
    const districtColumn = language === 'ta' ? 'distename' : 'distename';

    const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';
    const designationColumn = language === 'ta' ? 'desigtlname' : 'desigesname';
    const mobilenumberColumn = language === 'ta' ? 'ifhrmsno' : 'ifhrmsno';
    const EmailColumn = language === 'ta' ? 'email' : 'email';

    if ($.fn.DataTable.isDataTable('#checkidlelisttable')) {
        $('#checkidlelisttable').DataTable().clear().destroy();
    }

    // base thead
    let theadHtml = `
        <tr>
            <th class="lang align-middle text-center" key="s_no">S.No</th>
            <th class="lang align-middle text-center" key="department">Department</th>
            <th class="lang align-middle text-center" key="region">Region</th>
            <th class="lang align-middle text-center" key="district">District</th>
            <th class="lang align-middle text-center" key="usernamedet">User Details</th>
            <th class="lang align-middle text-center" key="usernamedet">Not Scheduled Institute</th>
            <th class="lang align-middle text-center" key="usernamedet">Status</th>


    `;

    // base columns
    let columnsConfig = [
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
            data: departmentColumn,
            title: columnLabels?.[departmentColumn]?.[language],
            render: function (data, type, row) {
                return row[departmentColumn] || '-';
            },
            className: 'text-wrap text-start'
        },
        {
            data: regionColumn,
            title: columnLabels?.[regionColumn]?.[language],
            render: function (data, type, row) {
                return row[regionColumn] || '-';
            },
            className: 'text-wrap text-start'
        },
        {
            data: districtColumn,
            title: columnLabels?.[districtColumn]?.[language],
            render: function (data, type, row) {
                return row[districtColumn] || '-';
            },
            className: 'text-wrap text-start'
        },
        {
            data: "userdetails",
            title: columnLabels?.[usernameColumn]?.[language] || "User Details",
            render: function (data, type, row) {
                if (!data) return '-';

                const parts = data.split('|').map(p => p.trim());
                const username = parts[0] || '-';
                const ifhrmsno = parts[1] || '-';
                const email = parts[2] || '-';

                return `
                    <strong>Name:</strong> ${username}<br>
                    <strong>IFHRMS No:</strong> ${ifhrmsno}<br>
                    <strong>Email:</strong> ${email}
                `;
            },
            className: 'text-wrap text-start'
        },
        {
    data: "instename",
    title: columnLabels?.['instename']?.[language] || "Current Institute",

    className: 'text-wrap text-start'
     },
         {
    data: "status",
    title: columnLabels?.['status']?.[language] || "Status",

    className: 'text-center '
     }

    ];


    theadHtml += `</tr>`;
    $("#checkidlelisttable thead").html(theadHtml);

    // init datatable
    table = $('#checkidlelisttable').DataTable({
        processing: true,
        serverSide: false,
        lengthChange: false,
        data: dataFromServer,
        columns: columnsConfig,
        initComplete: function (settings, json) {
            $("#checkidlelisttable").wrap(
                "<div style='overflow:auto; width:100%;position:relative;'></div>"
            );
        }
    });

    // Adjust mobile toggle for merged columns
    const mobileColumns = ["User Details", "Next Schedule Details", "Completed Institution Details"];
    setupMobileRowToggle(mobileColumns);

    updatedatatable(language, "checkidlelisttable");
  }


 function onchangedob() {
        if ($('#doj').val()) {
            $('#doj').datepicker('setDate', '');
            $('#doj').val('');
            $('#dor').val('');
        }
        getRetirementDay()
    }

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


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#checkidlelisttable')) {
            $('#checkidlelisttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable();

    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";

    let dtText = dataTables[language]["datatable"] || dataTables["en"]["datatable"];

    // Add new headers for institution, next schedule, and idle days
    const headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["region"] || "Region", key: "region" },
        { header: dtText["district"] || "District", key: "district" },
        { header: dtText["username"] || "Name", key: "username" },
        { header: dtText["email"] || "Email", key: "email" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },
        { header: dtText["institution"] || "Institution", key: "institution" },
    ];

    let rawData = table.rows({ search: 'applied' }).nodes().toArray();

    let excelData = rawData.map(row => {
        let $row = $(row);

        return {
            department: $row.find('td').eq(1).text().trim() || "-",
            region: $row.find('td').eq(2).text().trim() || "-",
            district: $row.find('td').eq(3).text().trim() || "-",
            username: $row.find('td').eq(4).text().replace(/Name:|Email:|IFHRMS No:/g,'').trim() || "-",
            email: $row.find('td').eq(4).text().match(/Email:\s*([^\n]+)/)?.[1]?.trim() || "-",
            ifhrmsno: $row.find('td').eq(4).text().match(/IFHRMS No:\s*([^\n]+)/)?.[1]?.trim() || "-",
            institution: $row.find('td').eq(5).text().trim() || "-", // adjust index based on your table
        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    XLSX.utils.sheet_add_json(ws, excelData, {
        skipHeader: true,
        origin: "A2",
        header: headers.map(h => h.key)
    });

    XLSX.utils.book_append_sheet(wb, ws, translatedTitle);
    XLSX.writeFile(wb, `${translatedTitle}.xlsx`);
     }






    function getDistrictBasedOnRegion(deptcode, region) {
        const lang = getLanguage();
        const districtDropdown = $('#distcode');


        districtDropdown.empty();



        districtDropdown.empty().append(`
                    <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                        ${lang === 'ta' ? 'அனைத்து' : 'All'}
                    </option>
                `);


        if (deptcode == "") {
            var deptcode = $("#deptcode").val();
        }
        if (region == "") {
            var region = $("#regioncode").val();
        }


        if (deptcode) {
            $.ajax({
                url: "/getdistrictbasedonregionchecklist",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    region: region || null,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        response.data.forEach(district => {

                            if (district.distcode && district.distcode !== 'undefined') {
                                districtDropdown.append(`
                                    <option value="${district.distcode}"
                                    data-name-en="${district.distename || ''}"
                                    data-name-ta="${district.disttname || ''}">
                                    ${district.distename || ''}
                                    </option>
                                `);
                            }
                        });

                       // $('#distcode').val(['A']).trigger('change');


                    } else {

                    }

                    // initializeDataTable(lang,deptcode,region);

                },
                error: function() {
                    alert('Error fetching district. Please try again.');
                }
            });
        }
    }


    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    var sessionregioncode = '<?php echo $regioncode; ?>';
    var sessiondistcode = '<?php echo $distcode; ?>';




    // if (
    //     (!sessiondeptcode || sessiondeptcode.trim() === '') &&
    //     (!sessionregioncode || sessionregioncode.trim() === '') &&
    //     (!sessiondistcode || sessiondistcode.trim() === '')
    // ) {
    //     $('#regioncode').val(['A']).select2();
    //     $('#distcode').val(['A']).select2();

    // }



    function getRegionBasedOnDept(deptcode) {
        const lang = getLanguage();

        const districtDropdown = $('#distcode');
        const regionDropdown = $('#regioncode');



        if (
            (!sessiondeptcode || sessiondeptcode.trim() === '') &&
            (!sessionregioncode || sessionregioncode.trim() === '') &&
            (!sessiondistcode || sessiondistcode.trim() === '')
        ) {

            regionDropdown.empty();


            regionDropdown.append(`
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                            ${lang === 'ta' ? 'அனைத்து' : 'All'}
                        </option>
                    `);
            districtDropdown.empty().append(`
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                            ${lang === 'ta' ? 'அனைத்து' : 'All'}
                        </option>
                    `);

        }

        var deptcode = $("#deptcode").val();






        if (deptcode) {
            $.ajax({
                url: "/getregionbasedondeptforchecklist",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {


                    if (response.success && response.data.length > 0) {
                        response.data.forEach(region => {
                            regionDropdown.append(
                                `<option value="${region.regioncode}"
                                            data-name-en="${region.regionename}"
                                            data-name-ta="${region.regiontname}">${region.regionename}</option>`
                            );

                        });

                        // if (
                        //     (!sessiondeptcode || sessiondeptcode.trim() === '') &&
                        //     (!sessionregioncode || sessionregioncode.trim() === '') &&
                        //     (!sessiondistcode || sessiondistcode.trim() === '')
                        // ) {
                        //     $('#regioncode').val(['']).trigger('change');
                        //     $('#distcode').val(['']).trigger('change');


                        // }

                        //  initializeDataTable(lang,deptcode);



                    }

                },
                error: function() {
                    alert('Error fetching region. Please try again.');
                }
            });
        }
    }

    $(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>';
        var dgarole = '<?php echo $dga_roletypecode; ?>';
        var distrole = '<?php echo $Dist_roletypecode; ?>';
        var regionrole = '<?php echo $Re_roletypecode; ?>';
        var headofficerole = '<?php echo $Ho_roletypecode; ?>';
        var adminrole = '<?php echo $Admin_roletypecode; ?>';

        var deptcode = $("#deptcode").val();
        var region = $("#regioncode").val();
        var district = $("#distcode").val();

        if (!deptcode || !region || !district) {
            deptcode = deptcode || '';
            region = region || '';
            district = district || '';
        }


        if (sessionrole == distrole) {
          //  onchangedistrict(deptcode, region, district)

        } else if (sessionrole == dgarole) {
          // onchangedistrict(deptcode, region, district)

        } else if (sessionrole == regionrole) {

           getDistrictBasedOnRegion(deptcode, region)
        }
        else if (sessionrole == headofficerole) {

           // onchangedistrict(deptcode, region, district)
        }

    });

    var sessionrole = '<?php echo $sessionroletypecode; ?>'
    var regionrole = '<?php echo $Re_roletypecode; ?>'
    var headofficerole = '<?php echo $Ho_roletypecode; ?>'


    // $(document).ready(function() {
    //     if (sessionrole === headofficerole) {
    //         $('#regioncode').val(['']).trigger('change');
    //         $('#distcode').val(['']).trigger('change');
    //     }
    // });

    function reset_form() {

var dgarole = '<?php echo $dga_roletypecode; ?>';
var distrole = '<?php echo $Dist_roletypecode; ?>';
var regionrole = '<?php echo $Re_roletypecode; ?>';
var headofficerole = '<?php echo $Ho_roletypecode; ?>';
var adminrole = '<?php echo $Admin_roletypecode; ?>';

$('#checklistform')[0].reset();


$('#tableshow').hide();
$('#usertable_wrapper').show();
$('#no_data').show();

$('.error').remove();
    $('.is-invalid').removeClass('is-invalid');
    $('label.error').remove();

    if ($("#checklistform").data('validator')) {
        $("#checklistform").validate().resetForm();
    }


if (sessionrole === headofficerole) {
    $('#regioncode').val('').select2();
    $('#distcode').val('').select2();

}else if(sessionrole === regionrole){
    $('#distcode').val('').select2();
}
else if(sessionrole === distrole){

}
else if(sessionrole === dgarole){
   $('#deptcode').val('').select2();
    $('#regioncode').val('').select2();
    $('#distcode').val('').select2();
}
else if(sessionrole === adminrole){
   $('#deptcode').val('').select2();
    $('#regioncode').val('').select2();
    $('#distcode').val('').select2();
}


}



   </script>


   @endsection