@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Audit Slip Count')


@php

    $sessionchargedel = session('charge');
    //  print_r($sessionchargedel);
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
    $auditteamhead = $sessionchargedel->auditteamhead;
@endphp



<style>

</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Detach Institute form the Audit Plan for Q2</div>
        <div class="card-body">
            <form id="institutionallocationform" name="institutionallocationform" method='post'>
            @csrf
                <div class="row">

                <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 select2  lang-dropdown" id="deptcode"  name="deptcode"
                                onchange="getRegionBasedOnDept(this.value,'','');">

                                <option value="" data-name-en="Select a Department"
                                    data-name-ta="துறையை தேர்வு செய்">Select a Department</option>

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


                <div class="col-md-4">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" onchange="getDistrictBasedOnRegion('','')" <?php echo $make_region_disable; ?> id="regioncode"  name="regioncode" >
                            <option value="" data-name-en="Select a Region" data-name-ta="">Select a Region</option>

                            @if (!empty($region) && count($region) > 0)
                                @foreach ($region as $reg)
                                    <option value="{{ $reg->regioncode }}"
                                        @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                        data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                        {{ $reg->regionename }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled data-name-en="No Regions Available"
                                    data-name-ta="">No Regions
                                    Available
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required lang" for="distcode" key="District">District</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown"  <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode" onchange="getinstitutionBasedOndistrict('','','','')">
                            
                            <option value="" data-name-en="Select a District"
                            data-name-ta="மாவட்டத்தை தேர்வு செய்">Select a District</option>
                            
                            @if (!empty($district) && count($district) > 0)
                                @foreach ($district as $dist)
                                <option value="{{ $dist->distcode }}"
                                    @if (old('distcode', $distcode) == $dist->distcode) selected @endif
                                    data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                    {{ $dist->distename }}
                                </option>
                                @endforeach
                            @else
                                <option disabled data-name-en="No District Available"
                                    data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No District
                                    Available
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  onchange="getteamBasedOninst('')"  id="instmappingcode" name="instmappingcode" >
                                <option value="" data-name-en="Select Auditable Institution"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">Select Auditable Institution</option>

                                </select>
                    </div>


                    <div class="col-md-4 mb-3">
                    <label class="form-label required lang">Team Head</label>
                        <div id="teamhead" class="form-control" readonly></div>
                    </div>

                   

                    <div class="col-md-4 mb-3">
                    <label class="form-label required lang" for="">Team Members</label>
                    <div id="teammember"  class="teammember-list p-2 border rounded"></div>
                </div>




                </div>
                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <input type="hidden" name="auditplanid" id="auditplanid" value="" />
			<input type="hidden" name="currentquarter" id="currentquarter" value="" />

                        <button class="btn button_save  mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Detach</button>
                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Institutes Allocated in Audit Plan
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="instituionallocationreport"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="department">Department</th>
                                <th class="lang align-middle text-center" key="region">Region</th>
                                <th class="lang align-middle text-center" key="district">District</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">Quarter type</th>
                                <th class="lang align-middle text-center" key="">Team Head</th>
                                <th class="lang align-middle text-center" key="">Team Members</th>


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



<script src="../assets/js/jquery.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>


<script>


var sessiondeptcode = ' <?php echo $deptcode; ?>';



    $(document).ready(function() {
        $('#institutionallocationform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

    });

    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        // changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
        //     @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'institutionallocationform');
    });



function getteamBasedOninst(instmappingcode) {
    const teamheadDropdown = $('#teamhead');
    const teammemberContainer = $('#teammember');

    if (!instmappingcode) {
        instmappingcode = $("#instmappingcode").val();
    }

    if (instmappingcode) {
        $.ajax({
            url: "/getteamBasedOninst",
            type: "POST",
            data: {
                instmappingcode: instmappingcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                teamheadDropdown.empty();
                teammemberContainer.empty();
                $("#auditplanid").val(response.data[0].auditplanid);
		$("#currentquarter").val(response.data[0].currentquarter);
                if (response.success && response.data.length > 0) {
                    response.data.forEach(team => {
                        let option = `<option value="${team.username}" data-name-ta="${team.usertamilname}" >
                                        ${team.username}
                                      </option>`;
                                      if (team.teamhead === "Y") {
                                        let headHTML = `
                                            <div class="head-name fw-bold btn btn-success mb-2" >
                                                ${team.username} - ${team.desigesname}
                                            </div>`;
                                            teamheadDropdown.append(headHTML);


                                    } else {
                                let memberHTML = `
                                    <div class="member-name fw-bold btn btn-success mb-2">
                                        ${team.username} - ${team.desigesname}
                                    </div>`;
                                teammemberContainer.append(memberHTML);

                            }
                    });
                } else {
                    teamheadDropdown.append("<option disabled>No Head Available</option>");
                    teammemberContainer.html("<div class='text-danger'>No Members Available</div>");
                }
            },
            error: function() {
                alert('Error fetching team. Please try again.');
            }
        });
    }
}




function getinstitutionBasedOndistrict(deptcode, region, district, selecteinstitutioncode = null) {
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
 	   
            if (!district) {
                institutionDropdown.append("<option value='' data-name-en='No Institution Available' data-name-ta='நிறுவனங்கள் எதுவும் கிடைக்கவில்லை' disabled>No Institution Available</option>");
            }
            if (deptcode && region && district) {
                $.ajax({
                    url: "/getinstitutionbasedondistallocation",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
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
            const districtDropdown = $('#distcode');
            const institutionDropdown = $('#instmappingcode');

            districtDropdown.html('<option value="">Select a District</option>');
            institutionDropdown.html('<option value="">Select Auditable Institution</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }

         
            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregionforallocation",
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


     

jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#institutionallocationform").validate({
            rules: {
             
                deptcode: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                instmappingcode: {
                    required: true
                },
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

            var auditplanid = $('#auditplanid').val();

            if ($("#institutionallocationform").valid()) {
                $('#ok_button').html('Ok').show();
                $('#cancel_button').html('Cancel').show();
                passing_alert_value('Confirmation Required','Are you sure to detach the institute from the Audit Plan of for Current Quarter?', 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'forward_alert');

        
                 $(document).one('click', '#process_button', function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                var formData = $('#institutionallocationform').serializeArray();

                    $.ajax({
                    url: "{{ route('institutionallocationform_insertupdate') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form();
                          //  setTimeout(() => {
 
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'forward_alert');
                            });

                            setTimeout(() => {

                                if(response.flag == 'Y'){
                                $('#process_button').hide();
                                $('#ok_button').html('Yes').show();
                                $('#cancel_button').html('No').show();


                                $('#ok_button').off('click').on('click', function() {

                                        $.ajax({
                                            url: "{{ route('institutionallocationform_insertupdate') }}",
                                            type: 'POST',
                                            data : {
                                                _token: '{{ csrf_token() }}', 
                                                auditplanid : auditplanid,
                                                listresponse : 'Y'
                                            },
                                           
                                            success: function(response) {
                                                if(response.success){


                                                    passing_alert_value('Confirmation','Auditors successfully added to the reserve list', 'confirmation_alert',
                                                    'alert_header', 'alert_body',
                                                    'forward_alert');
                                                    $('#cancel_button').hide();


                                                } else {
                                                    alert('Update failed');
                                                }
                                            },
                                            error: function(err) {
                                                console.error(err);
                                                alert('Something went wrong');
                                            }
                                        });
                                    });
                                                                

                               }
                                
                            }, 500);

                            response.flag == '';
                            $('#process_button').hide();
                            $('#ok_button').html('Ok').show();
                            $('#cancel_button').html('Cancel').show();
                      //  }, 100);

                            initializeDataTable(window.localStorage.getItem('lang'));

                        } else if (response.error) {
                            //console.log(response.error);
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
            });


            } else {

            }

        });


    reset_form();

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});





$(document).ready(function () {
    var $financialYear = $('#financialyear');

    if ($financialYear.val() !== '') {
        setTimeout(function () {
            $financialYear.select2();
        }, 100); 
    }
});





function getRegionBasedOnDept(deptcode, selectedRegioncode = null) {
        const regionDropdown = $('#regioncode');
        const lang = getLanguage();

        regionDropdown.html(`
        <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்வு செய்">
            ${lang === 'ta' ? 'பகுதியை தேர்வு செய்' : 'Select a Region'}
        </option>
    `);

   

        if (deptcode == "") {
            var deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            regionDropdown.append(`
            <option value="" disabled id="no-region-option"
                    data-name-en="No Region Available"
                    data-name-ta="பகுதி கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'பகுதி கிடைக்கவில்லை' : 'No Region Available'}
            </option>
        `);
        
            return;

        }

        if (deptcode) {
            $.ajax({
                url: "/Forallocationgetregionbasedondept",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.regions.length > 0) {
                        response.regions.forEach(regions => {
                            regionDropdown.append(
                                `<option value="${regions.regioncode}"
                                    data-name-en="${regions.regionename}"
                                    data-name-ta="${regions.regiontname}"
                                    ${regions.regioncode === selectedRegioncode ? 'selected' : ''}>
                                    ${regions.regionename}</option>`
                            );
                        });


                    } else {
                        regionDropdown.append('<option disabled>No Region Available</option>');
                    }

                },
                error: function() {
                    alert('Error fetching region. Please try again.');
                }
            });
        }
    }






    $('#translate').change(function() {
        const lang = getLanguage('Y'); 
        updateTableLanguage(
            lang); 

    });



    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('institutionallocation_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
              //  console.log("Success Response:", json);
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
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
        const districtColumn = language === 'ta' ? 'distename' : 'distename';
        const instColumn = language === 'ta' ? 'insttname' : 'instename';
        const desigColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const teammembername = language === 'ta' ? 'usertamilname' : 'username';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#instituionallocationreport')) {
            $('#instituionallocationreport').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#instituionallocationreport').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#instituionallocationreport").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row?.[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: null,
                    title: columnLabels?.[regionColumn]?.[language] || "Region",
                    render: function(data, type, row) {
                        return row?.[regionColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.[districtColumn]?.[language] || "District",
                    render: function(data, type, row) {
                        const designation = row?.[districtColumn] || '-';
                        return row?.[districtColumn] || '-';
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.[instColumn]?.[language] || "Institution",
                    render: function(data, type, row) {
                        return row?.[instColumn] || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: 'auditquartercode',
                    title: columnLabels?.['auditquartercode']?.[language] || "Institution",
                    render: function(data, type, row) {
                        return row?.auditquartercode || '-';

                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "team_heads",  // ✅ use the SQL alias
                    title: columnLabels?.["team_heads"]?.[language] || "Team Head",
                    render: function (data, type, row) {
                        if (!data) return "-";

                        return data.replace(/\n/g, "<br>");
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },

                {
                    data: "team_members",  
                    title: columnLabels?.["team_members"]?.[language] || "Team Members",
                    render: function (data, type, row) {
                        if (!data) return "-";

                        return data.replace(/\n/g, "<br>");
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                }






    
            ]
        });

        const mobileColumns = [
            departmentColumn, instColumn, regionColumn,
            districtColumn,"count", "teamname",
            "teammembername", "entrymeetdate", "exitmeetdate","fromdate","todate"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "instituionallocationreport");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#instituionallocationreport')) {
            $('#instituionallocationreport').DataTable().clear().destroy();
        }
        renderTable(language);
    }



    function reset_form() {
         $('#institutionallocationform')[0].reset();
         $('#institutionallocationform').validate().resetForm();

         $('#teamhead').empty();
         $('#teammember').empty();
         $('#instmappingcode').val(null).select2();
         $('#distcode').val(null).select2();
         $('#regioncode').val(null).select2();

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            // $('#ename, #fname').val();

        } else {
            $('#deptcode').val(null).select2();
        }

        // changeButtonAction('institutionallocationform', 'action', 'buttonaction', 'reset_button', 'display_error',
        //     @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

</script>


@endsection