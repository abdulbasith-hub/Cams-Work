@section('content')
@section('title', 'Auditor Mapping Institution')
@extends('index2')
@include('common.alert')
@php
    $sessionmainobjectiondel = session('charge');
    $sessionchargedel = session('charge');

    $roleTypeCode = $sessionchargedel->roletypecode;

@endphp

<style>
     .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* Adjust as needed */
    }
</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Report Revoke</div>
            <div class="card-body">
                <form id="reportrevokeform" name="reportrevokeform">
                    @csrf
                    <div class="row">

                      <input type="hidden" name="auditscheduleid" id="auditscheduleid" value="" />


                       <div class="col-md-4">
                        <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                                name="financialyear" onchange="resetDeptAndQuarter()">

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







                        <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 select2  lang-dropdown"  id="deptcode"   name="deptcode"
                                onchange="getRegionBasedOnDept(this.value,'','');">
                                <option value="" data-name-en="Select a Department"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select a Department</option>
                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
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
                            <label class="form-label required lang" for="auditquarter" key="auditquartercode">Audit Quarter</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown"
                                id="auditquartercode" name="auditquartercode"  onchange="resetRegionDistrictInstitution()">

                                <option value="" data-name-en="Select Audit Quarter" data-name-ta="Select Audit Quarter">Select Audit Quarter</option>


                            </select>
                       </div>



                        <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="Region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="regioncode" name="regioncode"
                                    onchange="getDistrictBasedOnRegion('','','','')">
                                    <option value="" data-name-en="Select a Region"
                                    data-name-ta="---மண்டலத்தினை தேர்ந்தெடுக்கவும்---">Select Region</option>

                                    <option value="" data-name-en="No Region Available" data-name-ta="மண்டலத்தை பெற முடியவில்லை" disabled >No Region Available</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="distcode" name="distcode" onchange="getinstitutionBasedOndistrict('','','','')">
                                    <option value="" data-name-en="Select a District"
                                    data-name-ta="---மாவட்டத்தை தேர்ந்தெடுக்கவும்---">Select District</option>

                                    <option value="" data-name-en="No District Available" data-name-ta="மாவட்டம் கிடைக்கவில்லை"" disabled id="no-district-option">No District Available</option>


                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="instmappingcode" name="instmappingcode" >
                                <option value="" data-name-en="Select Auditable Institution"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">Select Auditable Institution</option>



                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="revoke"
                                    for="revoke">Select details to update</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="revoke" name="revoke" >
                                <option value="" data-name-en="Select details to update"
                                    data-name-ta="---புதுப்பிக்க விவரங்களை தேர்ந்தெடுக்கவும்---">Select details to update</option>
                                <option value="01" data-name-en="Revoke Report"
                                    data-name-ta="Revoke Report">Revoke Report</option>


                                </select>
                            </div>


                    </div>




                    <div class="row">
                        <div class="col-md-3 mx-auto">
                            <input type="hidden" name="action" id="action" value="" />
                            <button class="btn  mt-3 lang" style="background-color: rgb(2, 98, 175);color: rgb(255, 255, 255); " key="update_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Update</button>
                            <button type="button" class="btn btn-danger mt-3  lang"
                                style="height:35px;font-size: 13px;" key="clear_btn" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">List of Institutes</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditdiaryrevoketable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_num">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="region">Region
                                    </th>
                                    <th class="lang align-middle text-center" key="district">District</th>
                                    <th class="lang align-middle text-center" key="institution">Institution Name</th>
                                    <th class="lang align-middle text-center" key="">Financialyear</th>

                                    <th class="lang align-middle text-center" key="quarter">Quarter</th>


                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
<!-- Include jQuery and Bootstrap -->

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>


<script>
    var session_roletypecode = '<?php echo $roleTypeCode; ?>';
    let table;
    let dataFromServer = [];


function resetDeptAndQuarter() {

    $('#deptcode').val('').select2();

    $('#auditquartercode').val('').select2();

}


function resetRegionDistrictInstitution() {

    $('#regioncode').val('').select2();

    $('#distcode').val('').select2();

    $('#instmappingcode').val('').select2();

}


    $(document).ready(function() {
        $('#reportrevokeform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        $('#translate').change(function() {
            const lang = $('#translate').val();
            updateTableLanguage(lang);
        });




    });


    $("#translate").change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button',  @json($updatebtn),@json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'reportrevokeform');

    });



    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('report.report_fetchData') }}",
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
            }
        });
    }


    function renderTable(language) {
        const departmentcolumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const Regioncolumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtcolumn = language === 'ta' ? 'disttname' : 'distename';
        const Institutioncolumn = language === 'ta' ? 'insttname' : 'instename';


        // Destroy DataTable if it exists
        if ($.fn.DataTable.isDataTable('#auditdiaryrevoketable')) {
            $('#auditdiaryrevoketable').DataTable().clear().destroy();
        }

        var table = $('#auditdiaryrevoketable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                        </div>`;
                    },
                    className: ' text-wrap text-end w-10',
                    type: "num"
                },
                {
                    data: departmentcolumn,
                    title: columnLabels?.[departmentcolumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentcolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: Regioncolumn,
                    title: columnLabels?.[Regioncolumn]?.[language],
                    render: function(data, type, row) {
                        return row[Regioncolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: districtcolumn,
                    title: columnLabels?.[districtcolumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row[districtcolumn] || '-';
                    },
                },
                {
                    data: Institutioncolumn,
                    title: columnLabels?.[Institutioncolumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap wrap-50",
                    render: function(data, type, row) {
                        return row[Institutioncolumn] || '-';
                    }
                },

                 {
                    data: "financialyear",
                    title: columnLabels?.["financialyear"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.financialyear || '-';
                    }
                },


                {
                    data: "planname",
                    title: columnLabels?.["planname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.planname || '-';
                    }
                },

            ],
            initComplete: function(settings, json) {
                $("#auditdiaryrevoketable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });

        // ? Include category column to ensure proper mobile display
        const mobileColumns = [departmentcolumn,Regioncolumn, districtcolumn,Institutioncolumn,"username","auditquartercode","diarystatus"];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "auditdiaryrevoketable");
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditdiaryrevoketable')) {
            $('#auditdiaryrevoketable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    $(document).on('change', '#instmappingcode', function () {
    const selectedOption = $(this).find('option:selected');
    const auditscheduleid = selectedOption.data('auditscheduleid') || '';
    $('#auditscheduleid').val(auditscheduleid);
});





    function getinstitutionBasedOndistrict(deptcode, region, district,auditquartercode, selecteinstitutioncode = null) {
            // alert('te');
            const institutionDropdown = $('#instmappingcode');
            institutionDropdown.html('<option value="">Select Auditable Institution</option>');
            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }
            if (district == "") {
                var district = $("#distcode").val();
            }
             if (auditquartercode == "") {
                var auditquartercode = $("#auditquartercode").val();
            }

            if (!district) {
                institutionDropdown.append("<option value='' data-name-en='No Institution Available' data-name-ta='நிறுவனங்கள் எதுவும் கிடைக்கவில்லை' disabled>No Institution Available</option>");

            }
            if (deptcode && region && district) {
                $.ajax({
                    url: "/getinstitutionbasedondistreport",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        auditquartercode:auditquartercode,
                        _token: '{{ csrf_token() }}'
                    },

                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(institution => {
                                institutionDropdown.append(
                                    `<option value="${institution.instid}"
                                     data-name-en="${institution.instename}"
                                     data-auditscheduleid="${institution.auditscheduleid}"
                                     data-name-ta="${institution.insttname}" ${
                                    institution.instid === selecteinstitutioncode ? 'selected' : ''
                            }>${institution.instename}</option>`
                                );
                            });
                        } else {
                            institutionDropdown.append("<option value='' data-name-en='No Institution Available' data-name-ta='நிறுவனங்கள் எதுவும் கிடைக்கவில்லை' disabled>No Institution Available</option>");
                        }
                    },
                    error: function() {
                        alert('Error fetching institution. Please try again.');
                    }
                });
            }
        }


        function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            // alert('te');
            const districtDropdown = $('#distcode');
            const institutionDropdown = $('#instmappingcode');

            districtDropdown.html('<option value="">Select District</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#regioncode").val();
                // alert(deptcode);
            }

            if (!region) {
                districtDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No District Available"
                            data-name-ta="மாவட்டம் கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'மாவட்டம் கிடைக்கவில்லை' : 'No District Available'}
                    </option>
                `);


            }
            // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');

            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregionreport",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.distcode}"
                                     data-name-en="${district.distename}"
                                   data-name-ta="${district.disttname}" ${
                                    district.distcode === selecteDistrictcode ? 'selected' : ''
                            }>${district.distename}</option>`
                                );
                            });
                        } else {
                            districtDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }

        function getRegionBasedOnDept(deptcode,financialyear, selectedAuditid) {
                    const districtDropdown = $('#distcode');
                    const regionDropdown = $('#regioncode');
                    const AuditquarterDropdown = $('#auditquartercode');
                    const institutionDropdown = $('#instmappingcode');
                    const UserDropdown = $('#usernamefield');
                    const revokeDropdown = $('#revoke');

                    regionDropdown.html(`
                        <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தை தேர்ந்தெடுக்கவும்">
                            ${lang === 'ta' ? 'மண்டலத்தை தேர்ந்தெடுக்கவும்' : 'Select Region'}
                        </option>
                    `);

                    districtDropdown.html('<option value="">Select District</option>');
                    institutionDropdown.html('<option value="">Select Audit Office</option>');
                    UserDropdown.html('<option value="">Select User</option>');
                    AuditquarterDropdown.html('<option value="">Select Audit Quarter</option>');

                    if (deptcode == "") {
                        var deptcode = $("#deptcode").val();
                    }
                    var financialyear = $("#financialyear").val();

                    if (!deptcode) {
                        regionDropdown.append(`
                            <option value="" disabled id="no-region-option"
                                    data-name-en="No Region Available"
                                    data-name-ta="????? ?????????????">
                                    ${lang === 'ta' ? '????? ?????????????' : 'No Region Available'}
                            </option>
                        `);

                        districtDropdown.append('<option value="" disabled>No District Available</option>');
                        institutionDropdown.append('<option value="" disabled>No Institution Available</option>');
                        UserDropdown.append('<option value="" disabled>No User Available</option>');

                        return;
                    }

                    if (deptcode) {
                        $.ajax({
                            url: "/getregionbasedondeptforreportdept",
                            type: "POST",
                            data: {
                                deptcode: deptcode,
                                financialyear : financialyear,
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
                                } else {
                                    regionDropdown.append('<option disabled>No Region Available</option>');
                                }

                                 if (response.success && response.quarter.length > 0) {
                                    response.quarter.forEach(quarter => {
                                        AuditquarterDropdown.append(
                                            `<option value="${quarter.planmappingid}"
                                            data-name-en="${quarter.planname}"
                                            data-name-ta="${quarter.planname}">${quarter.planname}</option>`
                                        );
                                    });
                                } else {
                                    AuditquarterDropdown.append('<option disabled>No Quarter Available</option>');
                                }



                            },
                            error: function() {
                                alert('Error fetching region. Please try again.');
                            }
                        });
                    }
                }



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#reportrevokeform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                regioncode: {
                    required: true,
                },
                distcode: {
                    required: true,
                },
                instmappingcode: {
                    required: true,
                },
                revoke: {
                    required: true,
                },



            },
            messages: errorMessages[language],
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


            if ($("#reportrevokeform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#reportrevokeform').serializeArray();


                $.ajax({
                    url: "{{ route('report.report_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
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
                            alert('Error');
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

    }).catch(error => {
        // console.error("Failed to load JSON data:", error);
    });



    function reset_form() {
	$('#auditquartercode').val('').select2();
        $('#reportrevokeform')[0].reset();
        $('#reportrevokeform').validate().resetForm();
        $('#deptcode').val(null).select2();
        $('#revoke').val(null).select2();
         $('#usernamefield').val(null).select2();
        // $('#auditYearDiv').hide();
        // $('#todate').hide();

        getRegionBasedOnDept(null);
        getDistrictBasedOnRegion(null);
        getinstitutionBasedOndistrict(null);
        changeButtonAction('reportrevokeform', 'action', 'buttonaction', 'reset_button', 'display_error',  @json($updatebtn),
        @json($clearbtn), @json($update))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
