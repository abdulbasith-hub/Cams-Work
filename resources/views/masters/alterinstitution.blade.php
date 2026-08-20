@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Alter Institution')


@php

$sessionchargedel = session('charge');
$inst_quat = $inst_quarter;

$deptcode = $sessionchargedel->deptcode;

$make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<div class="col-12">
    <div class="card hide_this" id="form_div">
        <div class="card-header card_header_color lang" key="">Alter Institution Details</div>

        <div class="card-body">

            <form class=form-control id="alterinst_form" type="post">
                <input type="hidden" class="form-control" id="old_quarter" name="old_quarter[]" value='' />

                <input type="hidden" class="form-control" id="detailid" name="detailid" />
                <input type="hidden" class="form-control" id="instid" name="instid" />
                <input type="hidden" class="form-control" id="deptcode" name="deptcode" />
                <input type="hidden" class="form-control" id="regioncode" name="regioncode" />
                <input type="hidden" class="form-control" id="distcode" name="distcode" />

                <div class="row">

                    <div class="col-md-3 mb-1 mt-2 notfortemp">

                        <label class="form-label required lang" for="nodalperson_tname" id="nodalperson_tname_label"
                            key="inst">Institution
                        </label>
                        <input class="form-control name" id="instename" name="instename"
                            maxlength="75" data-placeholder-key="" disabled />
                    </div>
                    <div class="col-md-3 mb-1 mt-2">
                        <input type="hidden" class="form-control" id="old_auditmode" name="old_auditmode" />
                        <label class="form-label lang required" key="audit_mode_label" for="validationDefault01">Audit
                            Mode</label>

                        <select class="form-select mr-sm-2 lang-dropdown " id="audit_mode" name="audit_mode"
                            onchange="onchange_mode()">
                            <option value="" data-name-en="---Select Mode---"
                                data-name-ta="---பயன்முறையைத் தேர்ந்தெடுக்கவும்---">---Select Mode
                            </option>
                            @foreach ($auditmode as $mode)
                            <option value="{{ $mode->auditmodecode }}" data-name-en="{{ $mode->auditmodeename }}"
                                data-name-ta="{{ $mode->auditmodetname }}">
                                {{ $mode->auditmodeename }}
                            </option>
                            @endforeach



                        </select>
                    </div>

                    <div class="col-md-6 ">
                        <div class="row py-2">
                            <div class="col-md-4">

                                <label class="form-label required lang" for="validationDefault02"
                                    key="applicable_for_label">Applicable
                                </label>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-9">
                                    @foreach ($inst_quat as $quarter)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input success" name="applicablefor[]"
                                            type="checkbox" id="{{ $quarter->auditquartercode }}"
                                            value="{{ $quarter->auditquartercode }}"
                                            onchange="onchange_applicablefor()">

                                        <label class="form-check-label" for="{{ $quarter->auditquartercode }}">
                                            {{ $quarter->auditquarter }}
                                        </label>
                                    </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="col-md-3 mb-1 mt-2 ">
                        <input type="hidden" class="form-control" id="old_insttype" name="old_insttype" />
                        <label class="form-label lang required" key="inst_type" for="validationDefault01">Institution
                            Type</label>

                        <select class="form-select mr-sm-2 lang-dropdown " id="inst_type" name="inst_type"
                            onchange="onchange_insttype('')">
                            <option value="" data-name-en="---Select Institution Type---"
                                data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select Institution Type---
                            </option>


                            <option value="H" data-name-en="Hub" data-name-ta="நோடல்">Hub</option>
                            <option value="S" data-name-en="Spoke" data-name-ta="துணை">Spoke
                            </option>

                        </select>
                    </div>

                    <div class="col-md-3 mb-1 mt-2 hide_this" id="hubtype_div">
                        <input type="hidden" class="form-control" id="old_hubtype" name="old_hubtype" />

                        <label class="form-label lang" for="hubtypeid" id="hubtypeid_label" key="hub_type">
                            Hub Type
                        </label>
                        <select class="form-select mr-sm-2 lang-dropdown" id="hubtypeid" name="hubtypeid"
                            onchange="onchange_hubtype('')">
                            <option value="" data-name-en="---Select Hub Type---"
                                data-name-ta="--- ஹப் வகையைத் தேர்வு செய்யவும் ---">
                                ---Select Hub Type---
                            </option>
                            <option value="A" data-name-en="Auditable Hub" data-name-ta="தணிக்கைக்குட்பட்ட ஹப்">
                                Auditable Hub
                            </option>
                            <option value="O" data-name-en="Non-Auditable Hub"
                                data-name-ta="தணிக்கைக்குட்படாத ஹப்">
                                Non-Auditable Hub
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-1 mt-2 hide_this" id="hubdesigtype_div">
                        <input type="hidden" class="form-control" id="old_hubdesigcode" name="old_hubdesigcode" />

                        <label class="form-label required lang" key="" for="institution">Non Auditable
                            Hub</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" id="hubdesigcode"
                            name="hubdesigcode">
                            <option value="" data-name-en="Select Designation"
                                data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">Select Non Auditable Hub
                            </option>

                        </select>
                    </div>

                    <div class="col-md-3 mb-1 mt-2 hide_this" id="parentinst_div">
                        <input type="hidden" class="form-control" id="old_parentinstid" name="old_parentinstid" />

                        <label class="form-label  lang " for="validationDefault02" id="parentinstid_label"
                            key="parent_inst">Parent Institution Name
                        </label>
                        <select class="form-select mr-sm-2 lang-dropdown select2" id="parentinstid"
                            name="parentinstid">
                            <option value="" data-name-en="---Select Parent  Institution ---"
                                data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select
                                Parent Institution ---
                            </option>
                        </select>

                    </div>
 <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control only_numbers" id="old_mandays" name="old_mandays" />
                        <label class="form-label required lang" for="validationDefault02"
                            key="total_mandays">Total
                            Mandays
                        </label>
                        <input class="form-control only_numbers" id="mandays" name="mandays" maxlength="4"
                            data-placeholder-key="mandays" />
                    </div>
                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control only_numbers" id="old_teamsize" name="old_teamsize" />

                        <label class="form-label required lang" for="validationDefault02"
                            key="teamsize">Team Size
                        </label>
                        <input class="form-control only_numbers" id="team_size" name="team_size" maxlength="2"
                            data-placeholder-key="team_size" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_nodalperson_ename"
                            name="old_nodalperson_ename" />

                        <label class="form-label required lang" for="nodalperson_ename" id="nodalperson_ename_label"
                            key="nodal_eng_name">Nodal
                            Person English Name
                        </label>
                        <input class="form-control name" id="nodalperson_ename" name="nodalperson_ename"
                            maxlength="75" data-placeholder-key="nodalperson_ename" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_nodalperson_tname"
                            name="old_nodalperson_tname" />

                        <label class="form-label required lang" for="nodalperson_tname" id="nodalperson_tname_label"
                            key="nodal_tam_name">Nodal
                            Person Tamil name
                        </label>
                        <input class="form-control name" id="nodalperson_tname" name="nodalperson_tname"
                            maxlength="75" data-placeholder-key="nodalperson_tname" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_email" name="old_email" />

                        <label class="form-label required lang" for="email" key="email" id="email_label">Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="100"
                            data-placeholder-key="email" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_mobile" name="old_mobile" />

                        <label class="form-label required lang" for="mobile" key="mobile"
                            id="mobile_label">Mobile
                            Number
                        </label>
                        <input class="form-control only_numbers mobile_number" id="mobile" name="mobile"
                            maxlength="10" data-placeholder-key="mobile" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_nodalperson_desigcode"
                            name="old_nodalperson_desigcode" />

                        <label class="form-label required lang " for="nodalperson_desigcode"
                            id="nodalperson_desigcode_label" key="nodal_desig">Nodal
                            Person Designation
                        </label>
                        <input class="form-control name" id="nodalperson_desigcode" name="nodalperson_desigcode"
                            maxlength="100" data-placeholder-key="nodalperson_desigcode" />
                    </div>

                    <div class="col-md-3 mb-1 mt-2 notfortemp">
                        <input type="hidden" class="form-control" id="old_auditeeaddr" name="old_auditeeaddr" />

                        <label class="form-label required lang " for="auditee_ofaddr" id="auditee_ofaddr_label"
                            key="auditee_offc_addr_label">Auditee
                            Office Address
                        </label>
                        <input class="form-control alpha_numeric" id="auditee_ofaddr" name="auditee_ofaddr"
                            maxlength="250" data-placeholder-key="nodalperson_desigcode"
                            placeholder="Enter Auditee Office Address" />
                    </div>

                </div>
                <div class="row justify-content-center" id="buttonset">
                    <div class="col-md-4 mx-auto">
                        <input type="hidden" name="action" id="action" value="insert" />
                        <button class="btn button_save mt-3 lang" key="savedraft_btn" type="button" action="insert"
                            id="buttonaction" name="buttonaction">Save Draft</button>
                        <button class="btn bg-success button_finalise lang mt-3" key="final_btn" type="button"
                            id="finalisebtn" action="finalise">
                            Finalize
                        </button>
                        <!-- <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn" id="reset_button">Clear</button> -->
                    </div>

                </div>
            </form>

        </div>
    </div>


    <div class="card card_border mt-2">
        <div class="card-header card_header_color lang" key="institute_detail">Institution Details</div>
        <div class="card-body">
            <div class="datatables">

                <div class="row">
                    <div class="col-md-3 mb-1 mt-2 text-center">
                        <label class="form-label lang required" key="department" for="deptcode1">Department</label>
                        <select class="form-select lang-dropdown" id="filter_deptcode" name="filter_deptcode"
                            onchange="fetchAlldata()" style="max-width: 100%;" <?php echo $make_dept_disable; ?>>
                            @if (!empty($dept) && count($dept) > 0)
                            @foreach ($dept as $department)
                            <option value="{{ $department->deptcode }}"
                                @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                                data-name-en="{{ $department->deptelname }}"
                                data-name-ta="{{ $department->depttlname }}">
                                {{ $department->deptelname }}
                            </option>
                            @endforeach
                            @else
                            <option disabled data-name-en="No Department Available"
                                data-name-ta="No Department Available">
                                No Departments Available
                            </option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3 mb-1 mt-2 text-center">
                        <label class="form-label lang required" key="quarter" for="quarter">Quarter</label>
                        <select class="form-select lang-dropdown" id="quarter" name="quarter"
                            onchange="fetchAlldata()" style="max-width: 100%;">
                            @foreach ($inst_quat as $quat)
                            <option value="{{ $quat->auditquartercode }}"
                                data-name-en="{{ $quat->auditquarter }}"
                                data-name-ta="{{ $quat->auditquarter }}">
                                {{ $quat->auditquarter }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-1 mt-2">
                        <label class="form-label lang required" key="audit_mode_label"
                            for="validationDefault01">Audit Mode</label>

                        <select class="form-select mr-sm-2 lang-dropdown " id="filter_auditmode"
                            name="filter_auditmode" onchange="fetchAlldata()">

                            @foreach ($auditmode as $mode)
                            <option value="{{ $mode->auditmodecode }}"
                                data-name-en="{{ $mode->auditmodeename }}"
                                data-name-ta="{{ $mode->auditmodetname }}">
                                {{ $mode->auditmodeename }}
                            </option>
                            @endforeach



                        </select>
                    </div>
                </div>




                <div class="table-responsive hide_this" id="tableshow">




                    <table id="alterinst_table"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department Name</th>
                                <th class="lang" key="instname_label">Institute Name</th>
                                <th class="lang" key="category">Category</th>
                                <th class="lang" key="nodal_details">Nodal Person Details</th>

                                <th class="all lang" key="action">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id='no_data' class='hide_this lang text-center' key="no_data">
                <center class="lang" key="no_data">No Data Available</center>

            </div>
        </div>
    </div>
</div>


<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script>
    let dataFromServer;
    let nonmandatory_field = ['nodalperson_ename_label', 'nodalperson_tname_label', 'email_label', 'mobile_label',
        'nodalperson_desigcode_label', 'auditee_ofaddr_label'
    ];
    let nonmandatory_input = ["nodalperson_ename", "nodalperson_tname", "email", 'mobile', 'nodalperson_desigcode',
        'auditee_ofaddr'
    ];

    //-----------------------------------------Validation-------------------------------------//

    // Add custom email validation rule
    jQuery.validator.addMethod("customEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Please enter a valid email address");
    jQuery.validator.addMethod("customMobile", function(value, element) {
        return this.optional(element) || /^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number");
    jQuery.validator.addMethod("customDecimal", function(value, element) {
        // This regex allows up to 12 digits before the decimal, and up to 2 digits after it
        return this.optional(element) || /^\d{1,12}(\.\d{1,2})?$/.test(value);
    }, "Please enter a valid decimal number");


    jsonLoadedPromise.then(() => {
        const language = getLanguage('Y');

        // Make email lowercase as user types
        //$("#email").on("input", function() {
        //  this.value = this.value.toLowerCase();
        //});

        // Trigger validation on blur
        $("#email").on("blur", function() {
            $(this).valid();
        });

        // Apply validation to the form
        const validator = $("#alterinst_form").validate({
            rules: {
        mandays: {
                    required: true
                },
                team_size: {
                    required: true
                },

                applicablefor: {
                    required: true
                },

                // typeofaudit: {
                //     required: true
                // },
                nodalperson_ename: {
                    required: true
                },
                nodalperson_tname: {
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    customMobile: true
                },
                nodalperson_desigcode: {
                    required: true
                },

                auditee_ofaddr: {
                    required: true
                },

                inst_type: {
                    required: true
                },
                parentinstid: {
                    required: true
                },
                hubtypeid: {
                    required: true
                },
                hubdesigcode: {
                    required: true
                },
                audit_mode: {
                    required: true
                },


            },

            messages: {
                ...errorMessages[language], // Merge language-based messages
                email: {
                    required: "Email is required",
                    email: "Enter a valid email address",
                    customEmail: "Email format is incorrect"
                },
                hubtypeid: {
                    required: 'Select Hub Type'
                },
                hubdesigcode: {
                    required: 'Select Non Auditable Hub'
                },
                mobile: {
                    required: "Mobile number is required",
                    minlength: "Enter at least 10 digits",
                    customMobile: "Please enter a valid 10-digit mobile number"
                },
                turnover: {
                    required: "Turn over is required",
                    customDecimal: "Enter Valid turn over value",

                }
            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        var $alterinst_form = $('#alterinst_form');
        const firstError = $alterinst_form.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    //--------------------------------------------ON LOAD -------------------------------------//
    $(document).ready(function() {
        fetchAlldata();
    });
    //-------------------------------------------ON CHANGE --------------------------------------//
   function onchange_mode(auditmode, form, selectedQuarters, inst_type, new_quarters) {

        var auditmodeCode = auditmode || $('#audit_mode').val();

        nonmandatory_field.forEach(id => $('#' + id).addClass('required'));

        nonmandatory_input.forEach(field => {
            $("#" + field).rules("add", {
                required: true
            });
        });
        $('.notfortemp').show();
        // Default behavior
        $('input[name="applicablefor[]"]').prop('checked', false).prop('disabled', false);
        $('#inst_type').prop('disabled', false);

        $('input[name="applicablefor[]"]').each(function() {

            var val = $(this).val();

            if (oldquarterdet.includes(val)) {
                $(this).prop('checked', true);
                $(this).attr('disabled', true);
            } else {
                $(this).prop('checked', false);
                $(this).attr('disabled', );
            }
        });

        switch (auditmodeCode) {
            case 'C':
                $('input[name="applicablefor[]"]').prop('checked', true).prop('disabled', true);
                $('#parentinstid_label').removeClass('required');
                break;

            case 'N':
                if (form != 'edit') {
                    $('input[name="applicablefor[]"]').each(function() {
                        if (oldquarterdet.includes(val)) {
                            $(this).prop('checked', true);
                        }
                    });
                }
                break;

            case 'Q':
                if (!inst_type) {
                    $('#inst_type').val('').prop('disabled', false);
                }
                $('input[name="applicablefor[]"]').each(function() {
                    var val = $(this).val();

                    if (Array.isArray(new_quarters) && new_quarters.length > 0) {
                        // Enable only if in both selected and new
                        if (oldquarterdet.includes(val) && new_quarters.includes(val)) {
                            $(this).attr('disabled', true);
                            $(this).prop('checked', true);
                        } else {
                            $(this).attr('disabled', false);
                        }
                    } else {
                        // If new_quarters is empty → enable only selectedQuarters
                        if (oldquarterdet.includes(val)) {
                            $(this).attr('disabled', true);
                            $(this).prop('checked', true);
                        } else {
                            $(this).attr('disabled', false);
                        }
                    }
                });


                break;

            case 'T':
                nonmandatory_field.forEach(id => $('#' + id).removeClass('required'));
                nonmandatory_input.forEach(id => $('#' + id).val(''));
                nonmandatory_input.forEach(field => {
                    $("#" + field).rules("remove", "required");
                });
                $('.notfortemp').hide()
                $('#inst_type').val('S').prop('disabled', true);
                onchange_insttype('S');
                break;

            default:
                // No action
                break;
        }
        if (Array.isArray(selectedQuarters) && selectedQuarters.length > 0) {

            $('input[name="applicablefor[]"]').each(function() {
                var val = $(this).val();
                if (selectedQuarters.includes(val)) {
                    $(this).prop('checked', true);
                }
            });
        }


    }

    function onchange_applicablefor(auditmodeCode) {

        var auditmodeCode = auditmodeCode || $('#audit_mode').val();
        let checkedCount = 0;
        var checkboxes = document.querySelectorAll('input[name="applicablefor[]"]:checked');
        if (auditmodeCode == 'N' || auditmodeCode == 'T') {

            if (checkboxes.length > 1) {
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        checkedCount++;
                        if (checkbox === event.target) {
                            lastChecked = checkbox;
                        }
                    }

                });
                if (checkedCount > 1 && lastChecked) {
                    getLabels_jsonlayout([{
                        id: 'selectonequarter',
                        key: 'selectonequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text.selectonequarter,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    lastChecked.checked = false;
                }
            }




        } else if (auditmodeCode == 'Q') {
            $(this).prop('disabled', true);
            if (checkboxes.length > 3) {
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        checkedCount++;
                        if (checkbox === event.target) {
                            lastChecked = checkbox;
                        }
                    }

                });
                if (checkedCount > 3 && lastChecked) {
                    getLabels_jsonlayout([{
                        id: 'select_threequarter',
                        key: 'select_threequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text.select_threequarter,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    lastChecked.checked = false;
                }
            }
        }

    }

    function onchange_insttype(insttype) {

        var insttype = insttype || $('#inst_type').val();

        if (insttype == "H") {
            $('#parentinst_div').hide();
            $('#hubtype_div').hide();
            $('#hubdesigtype_div').hide();

        } else if (insttype == 'S') {
            //  $('#parentinst_div').show();
            //  $('#hubtypeid').val('');
            $('#hubtype_div').show();


        } else {
            // $('#parentinst_div').hide();
            $('#hubtype_div').hide();
            $('#hubdesigtype_div').hide();


        }
    }

    function onchange_hubtype(hubtypeid) {
        var hubtype = hubtypeid || $('#hubtypeid').val();

        if (hubtype == 'A') {
            $('#parentinst_div').show();
            $('#hubdesigtype_div').hide();

        } else if (hubtype == 'O') {
            $('#parentinstid').val(null); // reset value
            $('#parentinstid').select2('destroy'); // destroy select2
            $('#parentinstid').select2(); // reinitialize select2

            $('#parentinst_div').hide();
            $('#hubdesigtype_div').show();
           if (!hubtypeid) {
                $('#hubdesigcode').val('').select2();
            }

        } else {
            $('#parentinst_div').hide();
            $('#hubdesigtype_div').hide();

        }


    }
    //--------------------------------------------INSERT or UPDATE-------------------------------------//
    $(document).on('click', '.button_save', function() {
        // e.preventDefault();
        var $alterinst_form = $('#alterinst_form');

        if ($("#alterinst_form").valid()) {

            var condcheck = checkofteamdet()
            if (condcheck) {
                passing_alert_value('Confirmation', 'Are you sure to finalise?', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');

                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    insertorUpdate_instdata('insert');
                });
            }
        }
    });
   
    $(document).on('click', '#finalisebtn', function() {
        // e.preventDefault();
        var $alterinst_form = $('#alterinst_form');

        if ($("#alterinst_form").valid()) {
            var condcheck = checkofteamdet()
            if (condcheck) {
                passing_alert_value('Confirmation', 'Are you sure to finalise?', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');

                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    insertorUpdate_instdata('finalise');
                });
            }




        }
    });
	
function checkofteamdet() {
        var teamsize = $('#team_size').val()
        var auditmode = $('#audit_mode').val();

        if (teamsize > 10) {
            getLabels_jsonlayout([{
                id: 'teamsizelimit_msg',
                key: 'teamsizelimit_msg'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .teamsizelimit_msg, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            return false;
        } else if (teamsize <= 1 && auditmode != 'T') {
            passing_alert_value('Confirmation', 'Team size cannot be zero or one', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return false;
        }

        var mandays = $('#mandays').val();

        if (mandays < 1 && auditmode != 'T') {
            passing_alert_value('Confirmation', 'Mandays can not be zero', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return false;
        }
        var insttype = $('#inst_type').val();
        if (insttype == 'S' && (!$('[name="parentinstid"]').val() || $('[name="parentinstid"]').val().trim() === '')) {

            applyValidationToNewFields('parentinstid', 'Select Parent Institution');
            scrollToFirstError();
            return;
        }

        var mandays = $('#mandays').val();
        if (mandays > 1000) {
            passing_alert_value('Confirmation', 'Mandays should not exceed 1000', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return
        }

        return true;
    }

    function insertorUpdate_instdata(action) {
        $('#buttonaction,#process_button').attr('disabled', true);
        let statusflag;
        if (action === 'insert') {
            statusflag = 'Y';
        } else if (action === 'finalise') {
            statusflag = 'F';
        } else {
            statusflag = null; // or handle default case
        }

        var formData = $('#alterinst_form').serializeArray();
        formData.push({
            name: 'statusflag',
            value: statusflag
        });
        if ($('#inst_type').prop('disabled')) {
            var inst_type = $('#inst_type').val();
            formData.push({
                name: 'inst_type',
                value: inst_type
            });
        }

        const checkboxes = $('input[name="applicablefor[]"]'); // Select all checkboxes


        // Loop through checkboxes to check if they are disabled and selected
        checkboxes.each(function() {
            if ($(this).prop('disabled') && $(this).prop('checked')) {
                formData.push({
                    name: 'applicablefor[]',
                    value: $(this).val() // Push selected disabled checkbox value
                });
            }
        });
        $.ajax({
            url: '/masters/insertorupdate_alterInst',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#form_div').hide()
                if (response.success) {
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    fetchAlldata(lang);
                } else {
                    console.warn("Server returned success=false:", response);
                }
            },
            complete: function() {
                $('#buttonaction,#process_button').removeAttr('disabled');
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error Status:", status);
                console.error("AJAX Error Thrown:", error);

                // Try to detect response
                console.log("Response Text:", xhr.responseText);
                console.log("Response Status Code:", xhr.status);

                // Sometimes Laravel returns HTML error pages
                if (xhr.responseText) {
                    try {
                        let json = JSON.parse(xhr.responseText);
                        console.log("Parsed JSON error:", json);
                    } catch (e) {
                        console.log("Response is not valid JSON, maybe HTML error page.");
                    }
                } else {
                    console.log("Response was completely empty. Check server logs.");
                }
            }
        });
    }

    //--------------------------------------------Fetch Data-------------------------------------//
    function fetchAlldata() {

        let lang = $('html').attr('lang') || 'en';
        let deptcode = $('#filter_deptcode').val();
        let quarter = $('#quarter').val();
        let filter_auditmode = $('#filter_auditmode').val();

        $.ajax({
            url: '/masters/fetch_alterinst', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
                quarter: quarter,
                filter_auditmode: filter_auditmode,
                // form: 'fetch',
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // alert('adds');
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = response.data;
                    // auditeedept_reportdata = response.audit_reportdata;
                    // alert(dataFromServer);
                    renderTable(lang);
                } else {

                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
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

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here

        var id = $(this).attr('id'); //Getting id of user clicked edit button.
        let regioncode = $(this).data("regioncode");
        let distcode = $(this).data("distcode");
        let deptcode = $(this).data("deptcode");
        let detailid = $(this).data("detailid");
	let oldquarterdet

        if (id) {
            // reset_form();
            fetchsinglemap_instdata(id, regioncode, distcode, deptcode, detailid);

        }
    });

    function fetchsinglemap_instdata(instid, regioncode, distcode, deptcode, detailid) {

        // let deptcode = $('#fileter_deptcode').val();
        let quarter = $('#quarter').val();

        $.ajax({
            url: '/masters/fetch_singlealterinst', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                quarter: quarter,
                instid: instid,
                regioncode: regioncode,
                distcode: distcode,
                detailid: detailid
                // form: 'edit'
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {

                    reset_form()
                    $('#form_div').show()
                    var parentinstdet = response.paraentinst
                    const detail = response.data[0];
                    var hubdesidetails = response.hubdesidetails

                    var lang = getLanguage('')
                    if (parentinstdet.length > 0) {
                        $('#parentinstid').empty();
                        $('#parentinstid').append(
                            '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
                        );
                        $.each(parentinstdet, function(index, parentinst) {

                            var isSelected = parentinst.instid === parentinstid ? 'selected' : '';


                            $('#parentinstid').append(
                                '<option value="' + parentinst.instid + '"' +
                                ' data-name-en="' + parentinst.instename + '"' +
                                ' data-name-ta="' + parentinst.insttname + '" ' + isSelected +
                                '>' +
                                (lang === "en" ? parentinst.instename : parentinst.insttname) +
                                '</option>'
                            );
                        });
                    }

                    if (hubdesidetails.length > 0) {
                        $('#hubdesigcode').empty();


                        $('#hubdesigcode').append(
                            '<option value="" data-name-en="---Select Hub Designation---"data-name-ta="">---Select Hub Designation---</option>'
                        );
                        $.each(hubdesidetails, function(index, hdesig) {
                            var isSelected = hdesig.desigcode === hubdesigcode ? 'selected' :
                                '';
                            $('#hubdesigcode').append(
                                '<option value="' + hdesig.desigcode + '"' +
                                ' data-name-en="' + hdesig.desigename + '"' +
                                ' data-name-ta="' + hdesig.desigtname + '" ' + isSelected +
                                '>' +
                                (lang == "en" ? hdesig.desigename : hdesig
                                    .desigtname) +
                                '</option>'
                            );
                        });
                    }
                    if (detail.encrypted_detailid === null || detail.encrypted_detailid === '' || detail
                        .encrypted_detailid === 'null') {
                        $('#display_error').hide();
                        $('#instename').val(detail.instename);
                        $('#instid').val(detail.encrypted_instid);
                        $('#nodalperson_ename,#old_nodalperson_ename').val(detail.nodalperson_ename);
                        $('#nodalperson_tname,#old_nodalperson_tname').val(detail.nodalperson_tname);
                        $('#email,#old_email').val(detail.email);
                        $('#mobile,#old_mobile').val(detail.mobile);
                        $('#nodalperson_desigcode,#old_nodalperson_desigcode').val(detail
                            .nodalperson_desigcode);
                        $('#auditee_ofaddr,#old_auditeeaddr').val(detail.auditeeofficeaddress);
                        $('#audit_mode,#old_auditmode').val(detail.auditmode);
                        $('#mandays,#old_mandays').val(detail.mandays);
                        $('#team_size,#old_teamsize').val(detail.teamsize);
						

                        $('#inst_type,#old_insttype').val(detail.insttype);
                        $('#hubtypeid,#old_hubtype').val(detail.hubtype);


                        let selectedQuarters = [];
                        ['Q1', 'Q2', 'Q3', 'Q4'].forEach(q => {
                            const checkbox = document.getElementById(q);
                            if (checkbox) {
                                // check/uncheck based on detail
                                checkbox.checked = detail[q] === 'Y';

                                if (checkbox.checked) {
                                    selectedQuarters.push(q);
                                }
                            }
                        });
                   

				oldquarterdet = selectedQuarters.join(',');
                        console.log("Selected quarters:", selectedQuarters);

                    

                        //  onchange_applicablefor(detail.auditmode,'edit')

                        onchange_mode(detail.auditmode, 'edit', selectedQuarters, detail.insttype);
                        onchange_insttype(detail.insttype);
                        onchange_hubtype(detail.hubtype)
                    } else {
                        changeButtonAction('alterinst_form', 'action', 'buttonaction',
                            'display_error', '', @json($updatebtn),
                            @json($clearbtn), @json($update));
                        $('#instename').val(detail.instename);
                        $('#display_error').hide();
                        $('#instid').val(detail.encrypted_instid);
                        $('#detailid').val(detail.encrypted_detailid);
                        $('#old_nodalperson_ename').val(detail.nodalperson_ename);
                        $('#old_nodalperson_tname').val(detail.nodalperson_tname);
                        $('#old_email').val(detail.email);
                        $('#old_mobile').val(detail.mobile);
                        $('#old_nodalperson_desigcode').val(detail.nodalperson_desigcode);
                        $('#old_auditeeaddr').val(detail.auditeeofficeaddress);
                        $('#old_auditmode').val(detail.auditmode);
                        $('#old_insttype').val(detail.insttype);
                        $('#old_hubtype').val(detail.hubtype);
                        $('#old_mandays').val(detail.mandays);
                        $('#old_teamsize').val(detail.teamsize);
                        $('#mandays').val(detail.new_mandays);
                        $('#team_size').val(detail.new_teamsize);
                        $('#old_insttype').val(detail.insttype);
                        $('#old_hubtype').val(detail.hubtype);



                        $('#nodalperson_ename').val(detail.new_nodalperson_ename);
                        $('#nodalperson_tname').val(detail.new_nodalperson_tname);
                        $('#email').val(detail.new_email);
                        $('#mobile').val(detail.new_mobile);
                        $('#nodalperson_desigcode').val(detail
                            .new_nodalperson_desigcode);
                        $('#auditee_ofaddr').val(detail.new_auditeeofficeaddress);
                        $('#audit_mode').val(detail.new_auditmode);

                        $('#inst_type').val(detail.new_insttype);
                        $('#hubtypeid').val(detail.new_hubtype);




                        let checkedQuarters = detail.new_quarter
                            .replace(/[{}]/g, '') // remove { }
                            .split(',') // split by comma
                            .filter(q => q.trim() !== ''); // remove empty strings

                        // Now loop and check them
                        checkedQuarters.forEach(q => {
                            let checkbox = document.getElementById(q);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });

                        let selectedQuarters = [];
                        ['Q1', 'Q2', 'Q3', 'Q4'].forEach(q => {
                            if (detail[q] === 'Y') {
                                selectedQuarters.push(q);
                            }
                        });

                        // Store as comma-separated string
                      //  $('#old_quarter').val(selectedQuarters.join(','));
			oldquarterdet = selectedQuarters.join(',');

                        if (detail.new_hubtype == 'A') {
                            $('#parentinstid').select2('destroy');
                            $('#parentinstid').val(detail.new_parentinstid);
                            $('#parentinstid').select2();
                        } else if (detail.new_hubtype == 'O') {
                            $('#hubdesigcode').select2('destroy');
                            $('#hubdesigcode').val(detail.new_hubdesigcode);
                            $('#hubdesigcode').select2();
                        }


                        quarterarry = detail.new_quarter.replace(/[{}]/g, '');
                       
                        //  onchange_applicablefor(detail.auditmode,'edit')

                       onchange_mode(detail.new_auditmode, 'edit', checkedQuarters, detail
                            .new_insttype, selectedQuarters);
                        onchange_insttype(detail.new_insttype);
                        onchange_hubtype(detail.new_hubtype)
                    }


                } else {
                    alert('Institute Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    //-----------------------------------------Render Table -------------------------------------//
    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const InstColumn = language === 'ta' ? 'insttname' : 'instename';
        const RegColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const DistColumn = language === 'ta' ? 'disttname' : 'distename';
        const catColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcatColumn = language === 'ta' ? 'subcattname' : 'subcatename';

        if ($.fn.DataTable.isDataTable('#alterinst_table')) {
            $('#alterinst_table').DataTable().clear().destroy();
        }

        table = $('#alterinst_table').DataTable({
            // "scrollX": true,
            // "initComplete": function(settings, json) {
            //     $("#categorytable").wrap(
            //         "<div style='overflow:auto; width:100%;position:relative;'></div>");
            // },
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,

            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return `${row[departmentColumn]||'-'} `;
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: InstColumn,
                    title: columnLabels?.[InstColumn]?.[language],
                    render: function(data, type, row) {
                        return `<b>Institution : </b>${row[InstColumn]||'-'} <br><b>Region : </b>${row[RegColumn]|| '-'}<br><b>District : </b>${row[DistColumn]|| '-'}`;
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "catename",
                    title: columnLabels?.["category"]?.[language] || 'Category',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return `<b>Category : </b>${row[catColumn]} <br><b>Sub Category : </b>${row[subcatColumn]|| '-'}`;
                    }
                },
                {
                    data: "nodalPerson",
                    title: columnLabels?.["nodalPerson"]?.[language] || 'Nodal Person Details',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {

                        return `<b>${language === 'ta' ? 'நோடல் நபர் ஆங்கில பெயர்' : 'Nodal Person English Name'}:</b> ${row.nodalperson_ename} <br>
                                 <small><b>${language === 'ta' ? 'நோடல் நபர் தமிழ் பெயர்' : 'Nodal Person Tamil Name'}:</b> ${row.nodalperson_tname}</small><br>
                                 <small><b>${language === 'ta' ? 'மின்னஞ்சல்' : 'Email'}:</b> ${row.email}</small><br>
                                 <small><b>${language === 'ta' ? 'மொபைல் எண்' : 'Mobile Number'}:</b> ${row.mobile}</small><br>
                                 <small><b>${language === 'ta' ? 'பதவி' : 'Designation'}:</b> ${row.nodalperson_desigcode}</small>`;
                    }
                },


                {
                    data: "encrypted_instid",
                    title: columnLabels?.["actions"]?.[language],
                    className: "text-center noExport text-wrap",
                    render: function(data, type, row) {
                        let badgeText = '';
                        let bgclr = '';


                        if (!row.alterstatus || row.alterstatus === 'null') {
                            badgeText = 'Save/draft';
                            bgclr = 'bg-danger'
                        } else if (row.alterstatus !== 'Y') {
                            badgeText = 'Edit';
                        } else {
                            badgeText = 'Edit'
                            bgclr = 'bg-primary'
                        }

                        // Render output
                        return `
    <center>
        <a class="btn  edit_btn ${bgclr} text-light"
           id="${data}"
           data-detailid="${row.encrypted_detailid}"
           data-regioncode="${row.regioncode}"
           data-distcode="${row.distcode}"
           data-deptcode="${row.deptcode}">
           <i class=" fs-4">${badgeText}</i>
        </a>
    </center>`;

                    }
                }

            ],
            "initComplete": function(settings, json) {
                $("#categorytable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["catename", "cattname", "if_subcategory", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "categorytable");

    }

    function reset_form() {
        // changeButtonAction('alterinst_form', 'action', 'buttonaction', 'reset_button',
        //     'display_error', @json($savebtn), @json($clearbtn),
        // @json($insert));
        $('#alterinst_form')[0].reset();

    }
</script>
@endsection