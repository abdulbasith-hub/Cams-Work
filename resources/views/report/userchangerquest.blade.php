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
        <div class="card-header card_header_color">Change User Status</div>
        <div class="card-body">
            <form id="userchangerequestform" name="userchangerequestform" method='post'>
            <input type="hidden" name="hubid" id="hubid">

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
                        <option value="" data-name-en="Select a Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">
                            Select a Region
                        </option>

                            @if ($regioncode)
                                @foreach ($region as $reg)
                                    <option value="{{ $reg->regioncode }}"
                                        @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                        data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                        {{ $reg->regionename }}
                                    </option>
                                @endforeach
                            @else
                            <option disabled data-name-en="No Regions Available" data-name-ta="மண்டலம் எதுவும் இல்லை">
                                No Regions Available
                            </option>

                            @endif
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required lang" for="distcode" key="district">District</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown"  <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode" onchange="getDistricttogetusername('','','','')">
                            
                            <option value="" data-name-en="Select a District"
                            data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select a District</option>
                            
                            @if ($distcode)
                                @foreach ($district as $dist)
                                <option value="{{ $dist->distcode }}"
                                    @if (old('distcode', $distcode) == $dist->distcode) selected @endif
                                    data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                    {{ $dist->distename }}
                                </option>
                                @endforeach
                            @else
                            <option disabled data-name-en="No District Available" data-name-ta="மாவட்டம் எதுவும் இல்லை">
                                    No District Available
                                </option>

                            @endif
                        </select>
                    </div>



                    <div class="col-md-4">
                        <label class="form-label required lang" for="" key="">Select Username</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown"  id="changeusername" name="username" >
                            
                            <option value="" data-name-en="Select a Username"
                            data-name-ta="">Select a Username</option>
                            
                       
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label class="form-label required lang" key="remarks" for="remarks">Remarks</label>
                        <textarea id="remarks"  maxlength='100' data-placeholder-key="remarks_ph" name="remarks"  class="form-control removesplchar_text" placeholder="Enter remarks" style="height: 20px;"></textarea>
                    </div>



<!-- 
                    <div class="col-md-4">
                        <label class="form-label required lang" for="" key="">Deactive User</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown"  id="deactive" name="deactive" >
                            
                            <option value="" data-name-en="Select an option"
                            data-name-ta="">Select an option</option>

                            <option value="D" data-name-en="Deactive User"
                            data-name-ta="">Deactive User</option>
                            
                       
                        </select>
                    </div> -->



                    <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="status">Active
                                    Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusYes" name="statusflag"
                                        value="Y"  required  disabled />
                                    <label class="form-check-label lang"  key="statusyes" for="statusYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusNo" name="statusflag"
                                        value="N" checked required />
                                    <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                </div>
                            </div>
                    </div>



                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <input type="hidden" name="currentquarter" id="currentquarter" value="" />
                        <button class="btn button_save  mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction" style="background-color: rgb(2, 98, 175); color: rgb(255, 255, 255);">Update</button>
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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">Auditor's Details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="userchangerequesttable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="department">Department</th>
                                <th class="lang align-middle text-center" key="region">Region</th>
                                <th class="lang align-middle text-center" key="district">District</th>
                                <th class="lang align-middle text-center" key="">Auditor's Name</th>
                                <th class="lang align-middle text-center" key="designation">Designation</th>
                                <th class="lang align-middle text-center" key="statusflag">Statusflag</th>
                                <!-- <th class="lang align-middle text-center" key="action">Action</th> -->


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
        $('#userchangerequestform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

    });

    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
         changeButtonText('action', 'buttonaction', 'reset_button', @json($updatebtn),
             @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'userchangerequestform');
    });

    
    $(document).ready(function () {
        restrictSpecialChars('.removesplchar_text');
    });

    

function restrictSpecialChars(elements) {
    $(elements).each(function () {
        const $el = $(this);

        $el.off("keypress paste")
            .on("keypress", function (event) {
                const char = String.fromCharCode(event.which);
                const value = this.value;

                if ($el.hasClass('removesplchar_text')) {
                    if (!/^[a-zA-Z\s\u0B80-\u0BFF]$/.test(char)) {
                        event.preventDefault();
                    }
                } 
            })
            .on("paste", function (e) {
                e.preventDefault();
                let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                let cleanData = '';

                if ($el.hasClass('removesplchar_text')) {
                    cleanData = pasteData
                        .replace(/[^a-zA-Z\u0B80-\u0BFF]+/g, ' ')  // Replace groups of non-letters with one space
                        .replace(/\s+/g, ' ')                     // Collapse multiple spaces into one
                        .trim();                                  // Remove leading/trailing spaces
                } 
                const input = e.target;
                const start = input.selectionStart;
                const end = input.selectionEnd;
                const original = input.value;

                const maxLength = parseInt($(input).attr('maxlength')) || Infinity;

                const allowedLength = maxLength - (original.length - (end - start));
                cleanData = cleanData.substring(0, allowedLength); // truncate to allowed length



        

                input.value = original.substring(0, start) + cleanData + original.substring(end);
                input.setSelectionRange(start + cleanData.length, start + cleanData.length);

              

                $(input).trigger('input');
            });
    });
}


    

function getDistricttogetusername(deptcode, region, distcode) {
            const usrnameDropdown = $('#changeusername');

            usrnameDropdown.html(`
    <option value="" data-name-en="Select Username" data-name-ta="பயனர்பெயரைத் தேர்ந்தெடுக்கவும்">
        ${lang === 'ta' ? 'பயனர்பெயரைத் தேர்ந்தெடுக்கவும்' : 'Select Username'}
        </option>
    `);

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }
            if (distcode == "") {
                var distcode = $("#distcode").val();
            }

         
            if (deptcode && region && distcode) {
                $.ajax({
                    url: "/getusernameforuserchangereq",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        distcode: distcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $("#currentquarter").val(response.data[0].currentquarter);

                        if (response.success && response.data.length > 0) {
                            response.data.forEach(username => {
                                usrnameDropdown.append(
                                    `<option value="${username.deptuserid}" 
                                     data-name-en="${username.username} - ${username.desigesname} - (${username.ifhrmsno})"
                                   data-name-ta="${username.usertamilname}">${username.username} - ${username.desigesname} - (${username.ifhrmsno}) </option>`
                                );
                            });

                        } else {
                            usrnameDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }





function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            const districtDropdown = $('#distcode');

            districtDropdown.html(`
            <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}
            </option>
        `);

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }

         
            if (deptcode && region) {
                $.ajax({
                    url: "/getdistforuserchangereq",
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
        var validator = $("#userchangerequestform").validate({

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
                username: {
                    required: true
                },
                remarks: {
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

    if ($("#userchangerequestform").valid()) {

        var formData = $('#userchangerequestform').serializeArray();

        $.ajax({
            url: "{{ route('validationprecheck') }}", // new endpoint
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.error) {
                    passing_alert_value(
                        'Error',
                        response.message, 
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                    return; 
                }

                passing_alert_value(
                    'Confirmation Required',
                    'Do you want to update the status?',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );
                $('#process_button').html('Yes').show();
                $('#cancel_button').html('No').show();

                $(document).one('click', '#process_button', function() {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // Now call the actual update API
                    $.ajax({
                        url: "{{ route('userchangerequest_update') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                reset_form();
                                initializeDataTable(window.localStorage.getItem('lang'));
                                setTimeout(() => {

                                    passing_alert_value(
                                    'Success',
                                    response.message,
                                    'confirmation_alert',
                                    'alert_header',
                                    'alert_body',
                                    'confirmation_alert'
                                );
                                    
                                }, 200);
                              
                            }
                        },
                        error: function(xhr) {
                            let response = JSON.parse(xhr.responseText);
                            passing_alert_value(
                                'Error',
                                response.message,
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'confirmation_alert'
                            );
                        }
                    });
                });
            }

        });
    }
});



    reset_form();

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});








function getRegionBasedOnDept(deptcode, selectedRegioncode = null,selectedDesigcodecode = null) {
        const regionDropdown = $('#regioncode');
        const distDropdown = $('#distcode');
        const userDropdown = $('#changeusername');

        const lang = getLanguage();

        regionDropdown.html(`
        <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்வு செய்">
            ${lang === 'ta' ? 'பகுதியை தேர்வு செய்' : 'Select a Region'}
        </option>
    `);

        distDropdown.html(`
        <option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">
            ${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}
        </option>
    `);

    userDropdown.html(`
    <option value="" data-name-en="Select a User" data-name-ta="பயனரை தேர்வு செய்">
        ${lang === 'ta' ? 'பயனரை தேர்வு செய்' : 'Select a User'}
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

        distDropdown.append(`
            <option value="" disabled id="no-region-option"
                    data-name-en="No Designation Available"
                    data-name-ta="பகுதி கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'பகுதி கிடைக்கவில்லை' : 'No Designation Available'}
            </option>
        `);

        userDropdown.append(`
            <option value="" disabled id="no-user-option"
                data-name-en="No User Found"
                data-name-ta="பயனர் இல்லை">
                ${lang === 'ta' ? 'பயனர் இல்லை' : 'No User Found'}
            </option>
        `);

        
            return;

        }

        if (deptcode) {
            $.ajax({
                url: "/getregionbasedondept",
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









    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('userchangerequest_fetchData') }}",
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
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const desigColumn = language === 'ta' ? 'desigtname' : 'desigesname';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#userchangerequesttable')) {
            $('#userchangerequesttable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#userchangerequesttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#userchangerequesttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-center',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row?.[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-center '
                },
                {
                    data: regionColumn,
                    title: columnLabels?.[regionColumn]?.[language] || "Region",
                    render: function(data, type, row) {
                        return row?.[regionColumn] || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: districtColumn,
                    title: columnLabels?.[districtColumn]?.[language] || "District",
                    render: function(data, type, row) {
                        const designation = row?.[districtColumn] || '-';
                        return row?.[districtColumn] || '-';
                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },

             
                {
                    data: 'username',
                    title: columnLabels?.['username']?.[language],
                    render: function(data, type, row) {
                        return row?.username || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: desigColumn,
                    title: columnLabels?.[desigColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[desigColumn] || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
    data: "statusflag",
    title: columnLabels?.["statusflag"]?.[language],
    render: function(data) {
        let activeText = arrLang?.[language]?.["active"] || "Active";
        let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

        return data === 'Y' ?
            `<span class="badge lang" style="background-color:rgb(77, 146, 250); color: white; cursor: default;">${activeText}</span>` :
            `<span class="badge lang" style="background-color: #b71362; color: white; cursor: default;">${inactiveText}</span>`;
    },
    className: "text-center d-none d-md-table-cell extra-column"
}

               

                // {
                //         data: "encrypted_hubid",
                //         title: columnLabels?.["actions"]?.[language],
                //         render: (data) =>
                //             `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                //         className: "text-center noExport "
                // },


            ],

            "initComplete": function(settings, json) {
                    $("#hubdesigtable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },


        });

        const mobileColumns = [
            departmentColumn, regionColumn,
            districtColumn,desigColumn, "address"
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "userchangerequesttable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#userchangerequesttable')) {
            $('#userchangerequesttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function reset_form() {
         $('#userchangerequestform')[0].reset();
         $('#userchangerequestform').validate().resetForm();

       

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            // $('#ename, #fname').val();

        } else {
            $('#deptcode').val(null).select2();
            getRegionBasedOnDept('')
        }

        // changeButtonAction('userchangerequestform', 'action', 'buttonaction', 'reset_button', 'display_error',
        //     @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

</script>


@endsection