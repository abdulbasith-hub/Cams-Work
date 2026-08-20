@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Non Audittable Hub')


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
        <div class="card-header card_header_color">Non Audittable Hub</div>
        <div class="card-body">
            <form id="nonauditablehubform" name="nonauditablehubform" method='post'>
            <input type="hidden" name="hubid" id="hubid">

            @csrf

                <div class="row">

                <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 select2  lang-dropdown" id="deptcode"  name="deptcode"
                                onchange="getRegionBasedOnDept(this.value,'','');" <?php echo  $make_dept_disable?>>

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
                            name="distcode" onchange="onchange_district('','')">
                            
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

                   
                    <div class="col-md-4 hide_this">
                        <label class="form-label required lang" for="commonzone" key="commonzone">AC Zone</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" id="commonzone"
                            name="commonzone" onchange="onchange_hriazone('','')">
                            
                            <option value="" data-name-en="Select AC Zone"
                            data-name-ta="">Select AC Zone</option>
                            
                         
                        </select>
                    </div>



                    <div class="col-md-4 hide_this">
                        <label class="form-label required lang" for="circlezone" key="circlezone">Inspector Circle</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown"  id="circlezone"
                            name="circlezone" >
                            
                            <option value="" data-name-en="Select Inspector Circle"
                            data-name-ta="">Select Inspector Circle</option>
                          
                        </select>
                    </div>



                    <div class="col-md-4 mb-3" id="hubdesigdiv">
                                <label class="form-label required lang" key="designation"
                                    for="institution">Designation</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="hubdesigcode" name="hubdesigcode" >
                                <option value="" data-name-en="Select Designation"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">Select Designation</option>

                                </select>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label required lang" key="mobile" for="mobile">Mobile Number</label>
                        <input type="text" class="form-control removesplchar_number" id="mobile" maxlength='10'
                            data-placeholder-key="mobile" name="mobile" required />
                    </div>


                    <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="email" for="email">Email</label>
                                <input type="text" class="form-control " id="email" maxlength='50'
                                    data-placeholder-key="email" name="email" required />
                            </div>


                    <div class="col-md-4 mb-3">
                    <label class="form-label required lang" key="address"  key="address" for="address">
                        Enter Address
                    </label>
                    <textarea class="form-control lang-textarea" placeholder="Enter Address" maxlength="200"  id="address"  name="address"  rows="2"></textarea>


                </div>


                <!-- <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="loginrequired">Login Required in Cams?</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="loginrequired" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="loginrequired" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div> -->


                <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="active_sts_flag" for="status">Active
                                    Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="statusYes" name="statusflag"
                                            value="Y" checked required />
                                        <label class="form-check-label lang" key="statusyes" for="statusYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="statusNo" name="statusflag"
                                            value="N" required />
                                        <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                    </div>
                                </div>
                    </div>


                <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save  mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Submit</button>
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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">Non Auditable Hub details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="nonauditablehubtable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="department">Department</th>
                                <th class="lang align-middle text-center" key="region">Region</th>
                                <th class="lang align-middle text-center" key="district">District</th>
                                <th class="lang align-middle text-center" key="">Zone</th>
                                <th class="lang align-middle text-center" key="">Circle</th>
                                <th class="lang align-middle text-center" key="designation">Designation</th>
                                <th class="lang align-middle text-center" key="mobile">Mobile Number</th>
                                <th class="lang align-middle text-center" key="email">Email</th>
                                <th class="lang align-middle text-center" key="address">Address</th>
                                <!-- <th class="lang align-middle text-center" key="">Login Required for Cams?</th> -->
                                <th class="lang align-middle text-center" key="statusflag">Statusflag</th>
                                <th class="lang align-middle text-center" key="action">Action</th>


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
sessiondeptcode = '<?php echo $deptcode?>'  ;

$(document).ready(function () {
if (sessiondeptcode && sessiondeptcode.trim() !== '') {

        getRegionBasedOnDept(sessiondeptcode, '');
}
    toggleZoneDropdowns($('#deptcode').val());

    $('#deptcode').on('change', function () {
        toggleZoneDropdowns($(this).val());
    });

    function toggleZoneDropdowns(deptVal) {
        if (deptVal === "01") {
            // show both
            $('#commonzone').closest('.col-md-4').show();
            $('#circlezone').closest('.col-md-4').show();
        } else {
            $('#commonzone').closest('.col-md-4').hide();   
            $('#circlezone').closest('.col-md-4').hide();   
        }
    }
});


 
$(document).ready(function () {
        restrictSpecialChars('.removesplchar_number');

    });

    

function restrictSpecialChars(elements) {
    $(elements).each(function () {
        const $el = $(this);

        $el.off("keypress paste")
            .on("keypress", function (event) {
                const char = String.fromCharCode(event.which);
                const value = this.value;
                if ($el.hasClass('removesplchar_number')) {
                    // Rule 1: First digit must be 6-9
                    if (value.length === 0 && !/^[6-9]$/.test(char)) {
                        event.preventDefault();
                        return;
                    }

                    // Rule 2: Subsequent digits must be 0-9
                    if (value.length > 0 && !/^[0-9]$/.test(char)) {
                        event.preventDefault();
                        return;
                    }

                    // Rule 3: Prevent same digit repeated 10+ times
                    const repeatCheck = (value + char).match(/^(\d)\1+$/);
                    if (repeatCheck && (value + char).length > 3) {
                        event.preventDefault();
                        return;
                    }

                    // Rule 4: Limit to maxlength if present
                    const maxLength = parseInt($el.attr('maxlength')) || 10;
                    if (value.length >= maxLength) {
                        event.preventDefault();
                    }
                }
            })
            .on("paste", function (e) {
               e.preventDefault();
    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
    let cleanData = pasteData.replace(/[^0-9]/g, ''); // remove non-digits

    if ($el.hasClass('removesplchar_number')) {
        // Enforce first digit rule
        if (cleanData.length > 0 && !/^[6-9]/.test(cleanData.charAt(0))) {
            cleanData = cleanData.substring(1);
        }

        // Remove long-press same digit (like 9999999999)
        if (/^(\d)\1+$/.test(cleanData)) {
            cleanData = cleanData.charAt(0); // keep only first occurrence
        }
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
  

var sessiondeptcode = ' <?php echo $deptcode; ?>';



    $(document).ready(function() {
        $('#nonauditablehubform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

    });

    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
         changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
             @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'nonauditablehubform');
    });


    


    function onchange_hriazone(commonzone, selecteHriazone = null) {
                const circlezoneDropdown = $('#circlezone');


                circlezoneDropdown.html(`
                <option value="" data-name-en="Select Inspector Circle" data-name-ta="Select Inspector Circle">
                    ${lang === 'ta' ? 'Select Inspector Circle' : 'Select Inspector Circle'}
                </option>
            `);

              


            if (commonzone == "") {
                var commonzone = $("#commonzone").val();
            }
          

         
            if (commonzone) {
                $.ajax({
                    url: "/getcirclezonebasedonregiofornonaudit",
                    type: "POST",
                    data: {
                        commonzone: commonzone,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(circle => {
                                circlezoneDropdown.append(
                                    `<option value="${circle.circleid}" 
                                     data-name-en="${circle.circleename}"
                                   data-name-ta="${circle.circletname}" ${
                                    circle.circleid === selecteHriazone ? 'selected' : ''
                            }>${circle.circleename}</option>`
                                );
                            });
                        } else {
                            circlezoneDropdown.append('<option disabled>No Zone Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching Zone. Please try again.');
                    }
                });
            }
        }


    
        function onchange_district(distcode, selecteCommonzone = null) {
                const commonzoneDropdown = $('#commonzone');


                commonzoneDropdown.html(`
            <option value="" data-name-en="Select AC Zone" data-name-ta="பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்' : 'Select AC Zone'}
            </option>
        `);


            if (distcode == "") {
                var distcode = $("#distcode").val();
            }
          

         
            if (distcode) {
                $.ajax({
                    url: "/getcommonzonebasedonregiofornonaudit",
                    type: "POST",
                    data: {
                        distcode: distcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(zone => {
                                commonzoneDropdown.append(
                                    `<option value="${zone.zonecode}" 
                                     data-name-en="${zone.zoneename}"
                                   data-name-ta="${zone.zonetname}" ${
                                    zone.zonecode === selecteCommonzone ? 'selected' : ''
                            }>${zone.zoneename}</option>`
                                );
                            });
                        } else {
                            commonzoneDropdown.append('<option disabled>No Zone Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching Zone. Please try again.');
                    }
                });
            }
        }


function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            const districtDropdown = $('#distcode');
            const circlezoneDropdown = $('#circlezone');
            const commonzoneDropdown = $('#commonzone');

            districtDropdown.html(`
            <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}
            </option>
        `);

    
        circlezoneDropdown.html(`
            <option value="" data-name-en="Select Inspector Circle" data-name-ta="Select Inspector Circle">
                ${lang === 'ta' ? 'Select Inspector Circle' : 'Select Inspector Circle'}
            </option>
        `);

        commonzoneDropdown.html(`
        <option value="" data-name-en="Select AC Zone" data-name-ta="பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்' : 'Select AC Zone'}
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
                    url: "/getdistrictbasedonregiofornonaudit",
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
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        $.validator.addMethod("validMobile", function(value, element) {
        return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
    });

        $.validator.addMethod("validEmail", function(value, element) {
        return this.optional(element) || emailRegex.test(value);
    });

        var validator = $("#nonauditablehubform").validate({


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
                hubdesigcode: {
                    required: true
                },
                address: {
                    required: true
                },
                email: {
                    required: true,
                    validEmail: true  
                },
                mobile: {
                    required: true,
                    validMobile: true
                },

                commonzone: {
                required: {
                    depends: function() {
                        return $('#deptcode').val() === '01';
                    }
                }
            },
            circlezone: {
                required: {
                    depends: function() {
                        return $('#deptcode').val() === '01';
                    }
                }
            }

            },
	 	messages: {
            email: {
                validEmail: "Please enter a valid email address."
            },
            mobile: {
                validMobile: "Please enter a valid mobile number."
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

            if ($("#nonauditablehubform").valid()) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#nonauditablehubform').serializeArray();

                    $.ajax({
                    url: "{{ route('nonaudithub_insertupdate') }}", // URL where the form data will be posted
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
                        }

                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            // handleUnauthorizedError();
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


    reset_form();

}).catch(error => {
    console.error("Failed to load JSON data:", error);
});








function getRegionBasedOnDept(deptcode, selectedRegioncode = null,selectedDesigcodecode = null) {
        const regionDropdown = $('#regioncode');
        const desigDropdown = $('#hubdesigcode');
        const distcodeDropdown = $('#distcode');
        const circlezoneDropdown = $('#circlezone');
        const commonzoneDropdown = $('#commonzone');

        const lang = getLanguage();

        regionDropdown.html(`
        <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்வு செய்">
            ${lang === 'ta' ? 'பகுதியை தேர்வு செய்' : 'Select a Region'}
        </option>
    `);

    distcodeDropdown.html(`
    <option value="" data-name-en="Select a District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select a District'}
        </option>
    `);


    circlezoneDropdown.html(`
            <option value="" data-name-en="Select Inspector Circle" data-name-ta="Select Inspector Circle">
                ${lang === 'ta' ? 'Select Inspector Circle' : 'Select Inspector Circle'}
            </option>
        `);

        commonzoneDropdown.html(`
        <option value="" data-name-en="Select AC Zone" data-name-ta="பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'பொது மண்டலத்தைத் தேர்ந்தெடுக்கவும்' : 'Select AC Zone'}
        </option>
    `);

    desigDropdown.html(`
    <option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">
        ${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}
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
                url: "/Fornonaudithubgetregionbasedondept",
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


                    if (response.success && response.desig.length > 0) {
                        response.desig.forEach(desig => {
                            desigDropdown.append(
                                `<option value="${desig.desigcode}"
                                    data-name-en="${desig.desigename}"
                                    data-name-ta="${desig.desigtname}"
                                    ${desig.desigcode === selectedDesigcodecode ? 'selected' : ''}>
                                    ${desig.desigename}</option>`
                            );
                        });


                    } else {
                        desigDropdown.append('<option disabled>No Designation Available</option>');
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
            url: "{{ route('nonaudithub_fetchData') }}",
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
        const desigColumn = language === 'ta' ? 'desigtname' : 'desigename';
        const commonColumn = language === 'ta' ? 'zonetname' : 'zoneename';
        const circleColumn = language === 'ta' ? 'circletname' : 'circleename';

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#nonauditablehubtable')) {
            $('#nonauditablehubtable').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#nonauditablehubtable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#nonauditablehubtable").wrap(
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
                    className: 'text-wrap text-center'
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
                    data: commonColumn,
                    title: columnLabels?.[commonColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[commonColumn] || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: circleColumn,
                    title: columnLabels?.[circleColumn]?.[language],
                    render: function(data, type, row) {
                        return row?.[circleColumn] || '-';

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
                    data: "mobilenumber",
                    title: columnLabels?.["mobilenumber"]?.[language],
                    render: function(data, type, row) {
                        return row?.mobilenumber || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
              

                {
                    data: "email",
                    title: columnLabels?.["email"]?.[language],
                    render: function(data, type, row) {
                        return row?.email || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: 'address',
                    title: columnLabels?.['address']?.[language],
                    render: function(data, type, row) {
                        return row?.address || '-';

                    },
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },

                // {
                //     data: 'loginrequired',
                //     title: columnLabels?.['loginrequired']?.[language],
                //     render: function (data, type, row) {
                //         if (row?.loginrequired === 'Y') {
                //             return 'Yes';
                //         } else if (row?.loginrequired === 'N') {
                //             return 'No';
                //         } else {
                //             return '-';
                //         }
                //     },
                //     className: "text-center d-none d-md-table-cell extra-column text-wrap"
                // },

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
                    className: "text-center d-none d-md-table-cell extra-column  noExport"
                },
               

                {
                        data: "encrypted_hubid",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                },


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
        updatedatatable(language, "nonauditablehubtable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#nonauditablehubtable')) {
            $('#nonauditablehubtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    $(document).on('click', '.editchargedel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#hubid').val(id); // Set the ID field directly


                $.ajax({
                    url: "{{ route('nonaudithub_fetchData') }}",
                    method: 'POST',
                    data: {
                        hubid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('nonauditablehubform', 'action', 'buttonaction',
                                    'reset_button', 'display_error', @json($updatebtn),
                                    @json($clearbtn), @json($update))
                                populateChargeForm(response.data[0]); // Populate form with data
                            } else {
                                alert('Charge data is empty');
                            }
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



    
    function populateChargeForm(hub) {
            $('#display_error').hide();
            change_button_as_update('nonauditablehubform', 'action', 'buttonaction', 'display_error', '', '');
            $('#desigename').val(hub.desigename);
            $('#address').val(hub.address);
            $('#hubid').val(hub.encrypted_hubid);
            populateStatusFlag(hub.statusflag);
          //  populateStatusFlag(hub.loginrequired);

            $('#deptcode').val(hub.deptcode).select2();
            $('#email').val(hub.email);
            $('#mobile').val(hub.mobilenumber);
            getRegionBasedOnDept(hub.deptcode,hub.regioncode, hub.desigcode);
            getDistrictBasedOnRegion(hub.deptcode,hub.regioncode, hub.distcode);

            if (hub.zonecode) {
            $('#commonzone').closest('.col-md-4').show();
                onchange_district(hub.distcode, hub.zonecode); 
            }
            if (hub.circleid) {
                $('#circlezone').closest('.col-md-4').show();
                onchange_hriazone(hub.zonecode, hub.circleid); 
            }
                    
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }

        
      

        function populateStatusFlag(statusflag) {
            if (statusflag === "Y") {
                document.getElementById('statusYes').checked = true;
            } else if (statusflag === "N") {
                document.getElementById('statusNo').checked = true;
            }
        }


   function reset_form() {
         $('#nonauditablehubform')[0].reset();
         $('#nonauditablehubform').validate().resetForm();

         $('#hubdesigcode').val(null).select2();
         $('#distcode').val(null).select2();
         $('#regioncode').val(null).select2();
         $('#commonzone').val(null).select2();   
         $('#circlezone').val(null).select2();
        if(sessiondeptcode == '01'){
            $('#commonzone').closest('.col-md-4').show();   
            $('#circlezone').closest('.col-md-4').show();  
        }
      
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

        } else {
            $('#deptcode').val(null).select2();
            $('#commonzone').closest('.col-md-4').hide();   
            $('#circlezone').closest('.col-md-4').hide();   
        }

       

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

</script>


@endsection