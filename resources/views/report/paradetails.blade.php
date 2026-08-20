@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Paradetails Report')


@php

$sessionchargedel = session('charge');
// print_r($sessionchargedel);
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
    @media print {

        #downloadBtn,
        .btn-close,
        textarea,
        .accordion-button {
            display: none !important;
        }
    }

    /* Improve PDF look */
    #auditsliptable,
    .liabilitydetails,
    .auditorremarksdiv,
    .auditeeremarksdiv,
    .auditorreplydiv,
    .teamheaddiv {
        page-break-inside: avoid;
    }

    .auditor-table {
        width: 100%;
        border-collapse: collapse;
    }

    .auditor-table th,
    .auditor-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 13px;
    }


</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color">Serious and Non-Serious Slips Details</div>
        <div class="card-body">
            <form id="paradetails_form" name="paradetails_form" method='post'>
                @csrf
                <div class="row">

                    <input type="hidden" value="" id="if_subcategory" name="if_subcategory">


                    <div class="col-md-3">

                        <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" id="financialyear"
                            name="financialyear">

                            <option value="" data-name-en="Select Financial year"
                                data-name-ta="நிதி ஆண்டை தேர்வு செய்க">Select Financial year</option>

                            @foreach ($financialyear as $year)
                            <option value="{{ $year->financialyearcode }}"
                                data-name-en="{{ $year->financialyear }}"
                                data-name-ta="{{ $year->financialyear }}"
                                {{ $year->financialyear }}
                                </option>
                                @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label required lang" for="deptcode" key="department">Department</label>
                        <select
                            class="form-select mr-sm-2 select2 lang-dropdown"
                            multiple="multiple"
                            id="deptcode"
                            name="deptcode[]" <?php echo $make_dept_disable; ?>
                            onchange="onchange_region('region', 'regioncode'); ">

                            <!-- <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option> -->

                            <!-- <option value="A" data-name-en="All" data-name-ta="அனைத்து துறைகளும்"
                                @if (is_array(old('dept', $deptcode)) && in_array('A', old('dept', $deptcode))) selected @endif>
                                All
                            </option> -->

                            @if (!empty($dept) && count($dept) > 0)
                            @foreach ($dept as $department)
                            <option value="{{ $department->deptcode }}"
                                @if (
                                (is_array(old('dept', $deptcode)) && in_array($department->deptcode, old('dept', $deptcode)))
                                || (old('dept', $deptcode) == $department->deptcode)
                                )
                                selected
                                @endif
                                data-name-en="{{ $department->deptelname }}"
                                data-name-ta="{{ $department->depttlname }}">
                                {{ $department->deptelname }}
                            </option>
                            @endforeach
                            @else
                            <option disabled data-name-en="No Department Available"
                                data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments Available
                            </option>
                            @endif
                        </select>
                    </div>






                    <div class="col-md-3">
                        <label class="form-label required lang" for="regioncode" key="region">Region</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?> id="regioncode"
                            name="regioncode[]" onchange="onchange_region('district','distcode')">

                            <!-- <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            @if ($regioncode)
                            @foreach ($region as $reg)
                            <option value="{{ $reg->regioncode }}"
                                @if (
                                (is_array(old('region', $regioncode)) && in_array($reg->regioncode, old('region', $regioncode)))
                                || (!old('region') && empty($regioncode) && $reg->regioncode == '01')
                                || (old('region', $regioncode) == $reg->regioncode)
                                )
                                selected
                                @endif
                                data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                {{ $reg->regionename }}
                            </option>
                            @endforeach
                            @else

                            @endif
                        </select>
                    </div>


                    <div class="col-md-3">
                        <label class="form-label required lang" for="distcode" key="District">District</label>
                        <select class="form-select mr-sm-2  select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode"
                            name="distcode[]" onchange="onchange_region('institution','instmappingcode');">

                            <!-- <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>


                            @if ($distcode)
                            @foreach ($district as $dist)
                            <option value="{{ $dist->distcode }}"
                                @if (old('dept', $distcode)==$dist->distcode) selected @endif
                                data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                {{ $dist->distename }}
                            </option>
                            @endforeach
                            @else

                            @endif
                        </select>
                    </div>


                    <div class="col-md-3 mb-3">
                        <label class="form-label lang required" key="category"
                            for="validationDefault01">Category</label>

                        <select class="form-select mr-sm-2 lang-dropdown select2" multiple="multiple" id="category" name="category[]"
                            onchange="onchange_region('subcatcode','subcategory')">


                            <!-- <option value="" data-name-en="Select Category" data-name-ta="">Select Category</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>

                        </select>
                    </div>


                    <div class="col-md-3  subcatdiv " id="subcatdiv">
                        <label class="form-label lang required" key="" for="subcategory">SubCategory</label>

                        <select class=" mr-sm-2 lang-dropdown select2 subcategory" multiple="multiple" id="subcategory"
                            name="subcategory[]">

                            <!-- <option value="" data-name-en="Select SubCategory" data-name-ta="">Select SubCategory</option> -->


                            <option value="A" data-name-en="All" data-name-ta="அனைத்து">All</option>

                        </select>
                    </div>



                    <div class="col-md-3 mb-3">
                        <label class="form-label required lang" key="institution"
                            for="institution">Institution</label>
                        <select class="form-select mr-sm-2 select2 lang-dropdown" multiple="multiple" id="instmappingcode" name="instmappingcode[]">




                            <!-- <option value="" data-name-en="Select Institution" data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option> -->

                            <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                        </select>
                    </div>



                        <div class="col-md-3">
                            <label class="form-label required lang" for="auditquarter" key="auditquarter">Audit Quarter</label>
                            <select class="form-select  mr-sm-2 select2 lang-dropdown" multiple="multiple"

                                id="auditquarter" name="auditquarter[]">
                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                            </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label required lang" for="irregularity" key="irregularity">
                            Irregularity Type
                        </label>

                        <select class="form-select mr-sm-2 select2 lang-dropdown"
                            id="irregularity" name="irregularity[]" multiple="multiple">

                            <!-- Default: All -->
                            <option value="A"
                                data-name-en="All"
                                data-name-ta="அனைத்து">
                                All
                            </option>

                            <!-- Serious -->
                            <option value="01"
                                data-name-en="Serious"
                                data-name-ta="கடுமையான">
                                Serious
                            </option>

                            <!-- Non-Serious -->
                            <option value="02"
                                data-name-en="Non-Serious"
                                data-name-ta="கடுமை அல்லாத">
                                Non-Serious
                            </option>

                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
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
        <div class="card-header card_header_color lang" id="audit-slip-heading" key="">List of Serious and Non Serious Slips Details
        </div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="ParaSlipdetails" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang align-middle text-center" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key="">Department Details</th>
                                <th class="lang align-middle text-center" key="">Institution</th>
                                <th class="lang align-middle text-center" key="">Category/Subcategory</th>
                                <th class="lang align-middle text-center" key="">AuditQuarter</th>
                                <th class="lang align-middle text-center" key="">Financial Year</th>

                                <th class="lang align-middle text-center" key="">Paraslip Details</th>
                                <th class="lang align-middle text-center" key="">Serious / Non-Serious</th>
                                <th class="lang align-middle text-center" key="">Action</th>
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
 <div id="loader" class="d-none">
    <div class="spinner"></div>
</div>
<div class="modal fade" id="ViewSlipModel" tabindex="-1" aria-labelledby="ViewSlipModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;font-weight:600;">Slip Details of : <span
                        class="slipnodyn badge bg-success fs-6"></span></h4>

                <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewSlipLoader" style="display:none; text-align:center; padding:50px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading Slip Details...</p>
                </div>
                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div>

                        <div class="table-container" id="auditsliptable">
                        </div>
                        <div class="liabilitydetails">
                            <h5>
                                <center><b>Liability Details</b></center>
                            </h5>
                            <table id="liabilitiesTable" class="auditor-table">
                                <thead>

                                    <tr>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Designation</th>
                                        <th>Amount Involved</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="auditorremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditor Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Name</th>
                                            <td class="auditorname"></td>
                                        </tr>


                                    </tbody>
                                </table>
                                <div class="accordion" id="auditor">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="auditor_head">
                                            <button class="accordion-button bg-primary-subtle   collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#auditor_acc" aria-expanded="false"
                                                aria-controls="collapseOne">
                                                <b>Auditor Observation /Remarks</b>
                                            </button>
                                        </h2>
                                        <div id="auditor_acc" class="accordion-collapse collapse"
                                            aria-labelledby="auditor_head" data-bs-parent="#auditor">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <label class="form-label lang"
                                                            for="validationDefaultUsername"
                                                            key="observation">Auditor Remarks</label>
                                                        <textarea id="viewslip_auditorremarkscccz" class="form-control" placeholder="Enter remarks"
                                                            name="viewslip_auditorremarks"></textarea>

                                                    </div>
                                                    <!--<div class="col-md-4">

                                                                                                                                                                                            <label class="form-label required"
                                                                                                                                                                                                for="validationDefaultUsername">Auditor
                                                                                                                                                                                                Attachment</label>
                                                                                                                                                                                            <div class="container my-1"
                                                                                                                                                                                                id="viewslip_auditorcontainer"></div>

                                                                                                                                                                                        </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auditeeremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditee Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditee Name</th>
                                            <td class="auditeename"></td>
                                        </tr>

                                    </tbody>
                                </table>

                                <div class="accordion mt-3" id="auditee">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button bg-primary-subtle collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                <b>Auditee Reply</b>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#auditee">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label lang"
                                                            for="validationDefaultUsername">Auditee
                                                            Reply</label>

                                                        <textarea id="viewslip_auditeeremarks" class="form-control" placeholder="Enter remarks"
                                                            name="viewslip_auditeeremarks"></textarea>

                                                    </div>
                                                    <!--<div class="col-md-4">
                                                                                                                                                                                <label class="form-label required"
                                                                                                                                                                                    for="validationDefaultUsername">Auditee
                                                                                                                                                                                    Attachment</label>


                                                                                                                                                                                <div class="container my-1"
                                                                                                                                                                                    id="viewslip_auditeecontainer"></div>


                                                                                                                                                                            </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="auditorreplydiv" style="display:none;">
                        <br>
                        <div class=""
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Auditor Reply</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Reply</th>
                                            <td class="auditoreply_remarks"></td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="teamheaddiv" style="display:none;">
                        <br>
                        <div class="teamheaddiv"
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Team Head Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Team Head Name</th>
                                            <td class="teamheadname"></td>
                                        </tr>
                                        <tr>
                                            <th>Team Head Final Remarks</th>
                                            <td class="finalremarks"></td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <div class="text-left mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp; Download Report
                    </button>
                </div>

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
<script src="{{ asset('common/ajaxfn.js') }}"></script>

<script src="{{ asset('assets/js/chart.umd.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<script>
    let deptChangeTimeout;

    $('#deptcode').on('change', function() {
        clearTimeout(deptChangeTimeout);

        deptChangeTimeout = setTimeout(function() {
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');
            onchange_region('auditquarter', 'auditquarter');

        }, 200);
    });

    function showLoader() {
        document.getElementById('loader').classList.remove('d-none');
    }

    function hideLoader() {
        document.getElementById('loader').classList.add('d-none');
    }

    $(document).ready(function() {
        function updateSelect($select) {
            let selectedValues = $select.val() || [];

            if (selectedValues.includes('A')) {
                $select.val(['A']).trigger('change.select2');
                $select.find('option').not('[value="A"]').prop('disabled', true);

            } else {
                if (selectedValues.includes('')) {
                    selectedValues = selectedValues.filter(v => v !== '');
                    $select.val(selectedValues).select2();
                }

                $select.find('option').prop('disabled', false);
            }
        }
        $(document).ready(function() {
            function handleDeptcodeVisibility() {
                let selected = $('#deptcode').val();

                if (selected && (selected.includes("01") || selected.includes("05"))) {
                    $('#subcatdiv').hide();
                } else {
                    $('#subcatdiv').show();
                }
            }

            handleDeptcodeVisibility();

            $('#deptcode').on('change', handleDeptcodeVisibility);
        });






        function handleAllOption($select) {
            $select.on('change', function() {
                updateSelect($select);
            });

            if (!$select.val() || $select.val().length === 0) {
                $select.val(['']).select2();
            }

            updateSelect($select);
        }

        ['#deptcode', '#distcode', '#instmappingcode', '#regioncode', '#category', '#quartercode', '#subcategory', '#irregularity']
        .forEach(function(selector) {
            handleAllOption($(selector));
        });
    });

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        // Custom rule for select2 and normal multi-select
        $.validator.addMethod("selectRequired", function(value, element) {
            let v = $(element).val();
            return !(v === null || v.length === 0);
        }, "Please select at least one option.");

        $("#paradetails_form").validate({

            ignore: ":hidden:not(.select2-hidden-accessible)",

            rules: {
                financialyear: {
                    required: true
                },
                "auditquarter[]": {
                    selectRequired: true
                },
                "deptcode[]": {
                    selectRequired: true
                },
                "regioncode[]": {
                    selectRequired: true
                },
                "distcode[]": {
                    selectRequired: true
                },
                "category[]": {
                    selectRequired: true
                },

                "subcategory[]": {
                    selectRequired: function() {
                        let dept = $("#deptcode").val() || [];

                        // If deptcode contains 01 or 05 → NOT required
                        if (dept.includes("01") || dept.includes("05")) {
                            return false; // validation disabled
                        }
                        return true; // validation enabled
                    }
                },

                "instmappingcode[]": {
                    selectRequired: true
                },
                "irregularity[]": {
                    selectRequired: true
                },
            },

            messages: {
                financialyear: "Select a Financial Year",
                "auditquarter[]": "Select Audit Quarter",
                "deptcode[]": "Select Department",
                "regioncode[]": "Select Region",
                "distcode[]": "Select District",
                "category[]": "Select Category",
                "subcategory[]": "Select SubCategory",
                "instmappingcode[]": "Select Institution",
                "irregularity[]": "Select Irregularity",
            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#buttonaction').on('click', function(e) {
            e.preventDefault();

            if (!$("#paradetails_form").valid()) {
                console.log("Validation failed");
                return;
            }

            getParaSlipData('en');
        });

        reset_form();
    });




    $(document).ready(function() {
        var $financialYear = $('#financialyear');

        if ($financialYear.val() !== '') {
            setTimeout(function() {
                $financialYear.select2();
            }, 100);
        }
    });

    let dataFromServer;



    function enforceSelect2AllOnly(selectId) {
        $('#' + selectId)
            .off('select2:select select2:unselect')
            .on('select2:select select2:unselect', function() {

                let values = $(this).val() || [];

                if (values.includes('A') && values.length > 1) {
                    $(this).val(['A']).trigger('change.select2');
                }
            });
    }

    let hitLock = {
        region: false,
        district: false,
        institution: false,
        catcode: false,
        subcatcode: false
    };

    $(document).ready(function() {

        //handleAllOption('deptcode');
        enforceSelect2AllOnly('regioncode');
        enforceSelect2AllOnly('distcode');
        enforceSelect2AllOnly('category');
        enforceSelect2AllOnly('subcategory');
        enforceSelect2AllOnly('instmappingcode');

        $("#deptcode").off().on("change", function() {
            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');
             onchange_region('auditquarter', 'auditquarter');

        });

        $("#regioncode").off().on("change", function() {
            onchange_region('district', 'distcode');
        });

        $("#distcode").off().on("change", function() {
            onchange_region('institution', 'instmappingcode');
        });

        $("#category").off().on("change", function() {
            onchange_region('subcatcode', 'subcategory');
        });
    });


    function onchange_region(valuefor, valueforid) {


        let deptcode = $('#deptcode').val() || [];
        let regioncode = $('#regioncode').val() || [];
        let distcode = $('#distcode').val() || [];
        let catcode = $('#category').val() || [];
        let subcatcode = $('#subcategory').val() || [];
        var financialyear = $("#financialyear").val();

        const $dropdown = $('#' + valueforid);


        if (valuefor === 'district' && regioncode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }

        if (valuefor === 'institution' && distcode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }

        if (valuefor === 'subcatcode' && catcode.includes('A')) {

            resetDropdownToAll($dropdown, valueforid);
            return;
        }


        if (hitLock[valuefor]) {
            return;
        }
        hitLock[valuefor] = true;

        if ($dropdown.hasClass('select2-hidden-accessible')) {
            $dropdown.select2('destroy');
        }
        $dropdown.empty().select2();
        enforceSelect2AllOnly(valueforid);


          if (valueforid === 'auditquarter' && deptcode.length > 1) {
            const dd = $("#" + valueforid);

            dd.select2('destroy');
            dd.empty();
            dd.append(`<option value="A">All</option>`);
            dd.select2();
            return;
        }

        let sendData = {
            valuefor: valuefor,
            formname: 'paradetailsreport'
        };

        if (['region', 'catcode', 'audityear','auditquarter'].includes(valuefor)) {
            sendData.deptcode = deptcode;
            sendData.financialyear = financialyear;

        }

        if (valuefor === 'district') {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
        }

        if (valuefor === 'institution') {
            sendData.deptcode = deptcode;
            sendData.regioncode = regioncode;
            sendData.distcode = distcode;
        }

        if (valuefor === 'subcatcode') {
            sendData.catcode = catcode;
        }


        $.ajax({
            url: '/report/fetch_deptbaseddata',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: sendData,

            success: function(response) {

                hitLock[valuefor] = false;

                if (!response.success || !Array.isArray(response.data)) {
                    resetDropdownToAll($dropdown, valueforid);
                    return;
                }

                let options = response.data.map(item => {
                    switch (valuefor) {

                        case 'region':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.regioncode}">${item.regionename}</option>`;

                        case 'district':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.distcode}">${item.distename}</option>`;

                        case 'auditquarter':
                          const selectedList = Array.isArray(auditquarter)
                            ? auditquarter
                            : (auditquarter ? [auditquarter] : []);
                            return item.code === 'A'
                                ? `<option value="A"
                                    ${selectedList.includes('A') ? "selected" : ""}>
                                    ${item.name_en}
                                </option>`
                                : `<option value="${item.planmappingid}"
                                    ${selectedList.includes(String(item.planmappingid)) ? "selected" : ""}>
                                    ${item.planname}
                        </option>`;


                        case 'catcode':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.catcode}">${item.catename}</option>`;

                        case 'subcatcode':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.auditeeins_subcategoryid}">${item.subcatename}</option>`;

                        case 'institution':
                            return item.code === 'A' ?
                                `<option value="A">${item.name_en}</option>` :
                                `<option value="${item.instid}">${item.instename}</option>`;

                        default:
                            return '';
                    }
                }).join('');

                $dropdown.append(options).select2();
                enforceSelect2AllOnly(valueforid);
            },

            error: function() {
                hitLock[valuefor] = false;
                console.error('AJAX error for:', valuefor);
            }
        });
    }


    function resetDropdownToAll($dropdown, valueforid) {

        if ($dropdown.hasClass('select2-hidden-accessible')) {
            $dropdown.select2('destroy');
        }

        $dropdown
            .empty()
            .append(`<option value="A">All</option>`)
            .val(['A'])
            .select2();

        enforceSelect2AllOnly(valueforid);
    }

    function getParaSlipData(lang) {

        showLoader();

        var deptcode = $('#deptcode').val() || ['A'];
        var regioncode = $('#regioncode').val() || ['A'];
        var distcode = $('#distcode').val() || ['A'];
        var category = $('#category').val() || ['A'];
        var financialyear = $("#financialyear").val() || '';

        var subcategory = $('#subcategory').val() || ['A'];
        var auditquarter = $("#auditquarter").val();
        if (!auditquarter || auditquarter.length === 0) {
            auditquarter = [];
        }
        var instmappingcode = $('#instmappingcode').val() || ['A'];
        var irregularity = $('#irregularity').val() || ['A'];

        $.ajax({
            url: "{{ route('report.paradetails.fetch') }}",
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                category: category,
                subcatcode: subcategory,
                auditquarter: auditquarter,
                financialyear: financialyear,
                instmappingcode: instmappingcode,
                irregularity: irregularity
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#tableshow').hide();
                $('#no_data').hide();
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    dataFromServer = response.data;
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    renderTable(lang);
                    hideLoader();

                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function(xhr) {
                $('#tableshow').hide();
                $('#no_data').show();
                console.error(xhr);
            },
            complete: function() {
                hideLoader();
            }
        });
    }


    $('#translate').change(function() {
        const lang = getLanguage('Y');
        updateTableLanguage(lang);

    });

    function renderTable() {

        const departmentColumn = "department";
        const regionColumn = "region";
        const districtColumn = "district";

        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            $('#tableshow').addClass('hide_this');
            $('#no_data').removeClass('hide_this');
            return;
        }

        $('#no_data').addClass('hide_this');
        $('#tableshow').removeClass('hide_this');

        if ($.fn.DataTable.isDataTable('#ParaSlipdetails')) {
            $('#ParaSlipdetails').DataTable().clear().destroy();
        }

        $('#ParaSlipdetails').DataTable({
            data: dataFromServer,
            searching: true,
            paging: true,
            ordering: true,
            lengthChange: false,
            autoWidth: false,

            columns: [

                {
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
                    data: null,
                    // title: "Department / Region / District",
                    title: "Department Details",
                    render: function(data, type, row) {
                        const dept = row[departmentColumn] || "-";
                        const region = row[regionColumn] || "-";
                        const district = row[districtColumn] || "-";

                        return `
                <div><b>Department :</b> ${dept}</div>
                <div><b>Region :</b> ${region}</div>
                <div><b>District :</b> ${district}</div>
                `;
                    },
                    // className: "text-start text-wrap"
                },

                {
                    data: "institution",
                    className: "text-start text-wrap"
                },
                // {
                // data: null,
                // title: "CategorySubcategory",
                // className: "text-start text-wrap",
                // render: function(data, type, row) {
                // const category = row.category || "-";
                // const subcategory = row.subcategory || "-";

                // return `
                // <div><b>Category:</b> ${category}</div>
                // <div><b>Subcategory:</b> ${subcategory}</div>
                // `;
                // }
                // },
                // {
                //     data: null,
                //     title: "CategorySubcategory",
                //     className: "text-start text-wrap",
                //     render: function(data, type, row) {
                //         const category = row.category || "-";
                //         const subcategory = row.subcategory;

                //         let html = `<div><b>Category:</b> ${category}</div>`;

                //         // Show Subcategory only if it is NOT "-" and NOT empty
                //         if (subcategory && subcategory !== "-") {
                //             html += `<div><b>Subcategory:</b> ${subcategory}</div>`;
                //         }

                //         return html;
                //     }
                // },
                {
                    data: null,
                    className: "text-start text-wrap",
                    render: function(data, type, row) {
                        const category = row.category || "-";
                        const subcategory = row.subcategory || "-";

                        let html = `<div><b>Category:</b> ${category}</div>`;
                        html += `<div><b>Subcategory:</b> ${subcategory}</div>`;

                        return html;
                    }
                },

                {
                    data: "planname",
                    className: "text-center",
                    render: d => d ?? "-"
                },
                {
                    data: "financialyear",
                    className: "text-start text-wrap",
                    render: d => d ?? "-"
                },
                {
                    data: null,
                    title: "Paraslip Details",
                    className: "text-start text-wrap",
                    render: function(data, type, row) {
                        const mainslip = row.mainslipnumber || "-";
                        const mainobj = row.mainobjection || "-";
                        const subobj = row.subobjection || "-";

                        return `
                <div><b>Slip No:</b> ${mainslip}</div>
                <div><b>Main Objection:</b> ${mainobj}</div>
                <div><b>Sub Objection:</b> ${subobj}</div>
                `;
                    }
                },

                {
                    data: "irregularitiescode",
                    render: d => d === "01" ?
                        "<span class='badge bg-danger'>Serious</span>" : "<span class='badge bg-warning text-dark'>Non-Serious</span>"
                },

                {
                    data: null,
                    className: "noExport text-center",
                    render: function(data, type, row) {

                        const auditSlipId = row.auditslipid || '';
                        const mainSlipNumber = row.mainslipnumber || '';

                        return `
                <button onclick="Open_viewmodel('${auditSlipId}','${mainSlipNumber}')"
                    type="button" class="btn-sm btn btn-secondary">
                    <i class="ti ti-eye fs-4 me-2"></i> View Slip
                </button>
                `;
                    }
                }



            ]
        });

        updatedatatable("en", "ParaSlipdetails");
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#ParaSlipdetails')) {
            $('#ParaSlipdetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    // function exportToExcel(tableId, language) {
    // let table = $(`#${tableId}`).DataTable();

    // let titleKey = `${tableId}_title`;
    // let reportTitle = dataTables[language]?.datatable?.[titleKey] || "ParadetailsSlip Report";
    // let safeSheetName = reportTitle.substring(0, 31);

    // let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    // let headers = [{
    // header: dtText["department"] || "Department",
    // key: "department"
    // },
    // {
    // header: dtText["Region"] || "Region",
    // key: "region"
    // },
    // {
    // header: dtText["District"] || "District",
    // key: "district"
    // },
    // {
    // header: dtText["Institution"] || "Institution",
    // key: "institution"
    // },
    // {
    // header: dtText["Category"] || "Category",
    // key: "category"
    // },
    // {
    // header: dtText["Subcategory"] || "Subcategory",
    // key: "subcategory"
    // },
    // {
    // header: dtText["auditquartercode"] || "Audit Quarter",
    // key: "auditquartercode"
    // },
    // {
    // header: dtText["droppespercentage"] || "Dropped Slip %",
    // key: "droppespercentage"
    // }
    // ];

    // let rawData = table.rows({
    // search: 'applied'
    // }).data().toArray();
    // let excelData = [];

    // rawData.forEach(row => {
    // try {
    // let button = $(row[0]).find("button.toggle-row");
    // let dataRow = button.attr("data-row");
    // let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

    // excelData.push({
    // department: rowData["department"] || "-",
    // region: rowData["region"] || "-",
    // district: rowData["district"] || "-",
    // institution: rowData["institution"] || "-",
    // category: rowData["category"] || "-",
    // subcategory: rowData["subcategory"] || "-",
    // auditquartercode: rowData["auditquartercode"] || "-",
    // droppespercentage: rowData["dropped_slip_percentage"] || "-"
    // });

    // } catch (e) {}
    // });

    // if (excelData.length === 0) {
    // alert("No data available for export!");
    // return;
    // }

    // const wb = XLSX.utils.book_new();
    // const ws = XLSX.utils.json_to_sheet([]);

    // XLSX.utils.sheet_add_aoa(ws, [
    // [reportTitle]
    // ], {
    // origin: "A1"
    // });

    // ws['!merges'] = [{
    // s: {
    // r: 0,
    // c: 0
    // },
    // e: {
    // r: 0,
    // c: headers.length - 1
    // }
    // }];

    // let colCount = headers.length;
    // let totalWidth = colCount * 15;
    // let paddedTitle = reportTitle.padStart((totalWidth + reportTitle.length) / 2, " ");
    // XLSX.utils.sheet_add_aoa(ws, [
    // [paddedTitle]
    // ], {
    // origin: "A1"
    // });

    // XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
    // origin: "A2"
    // });

    // XLSX.utils.sheet_add_json(ws, excelData, {
    // skipHeader: true,
    // origin: "A3"
    // });

    // XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    // XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
    // }


    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();

        let titleKey = `${tableId}_title`;
        let reportTitle = dataTables[language]?.datatable?.[titleKey] || "ParadetailsSlip Report";
        let safeSheetName = reportTitle.substring(0, 31);

        let headers = [{
                header: "Department",
                key: "department"
            },
            {
                header: "Region",
                key: "region"
            },
            {
                header: "District",
                key: "district"
            },
            {
                header: "Institution",
                key: "institution"
            },
            {
                header: "Category",
                key: "category"
            },
            {
                header: "Subcategory",
                key: "subcategory"
            },
            {
                header: "Main Slip Number",
                key: "mainslipnumber"
            },
            {
                header: "Main Objection",
                key: "mainobjection"
            },
            {
                header: "Sub Objection",
                key: "subobjection"
            },
            {
                header: "Audit Quarter",
                key: "auditquartercode"
            },
            {
                header: "Financial Year",
                key: "financialyear"
            },
            {
                header: "Irregularity Type",
                key: "irregularity"
            }
        ];

        let rawData = table.rows({
            search: 'applied'
        }).data().toArray();
        let excelData = [];

        rawData.forEach(row => {
            try {
                let button = $(row[0]).find("button.toggle-row");
                let dataRow = button.attr("data-row");
                let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

                excelData.push({
                    department: rowData["department"] || "-",
                    region: rowData["region"] || "-",
                    district: rowData["district"] || "-",
                    institution: rowData["institution"] || "-",
                    category: rowData["category"] || "-",
                    subcategory: rowData["subcategory"] || "-",
                    mainslipnumber: rowData["mainslipnumber"] || "-",
                    mainobjection: rowData["mainobjection"] || "-",
                    subobjection: rowData["subobjection"] || "-",
                    // auditquartercode: rowData["auditquartercode"] || "-",
                    auditquartercode: rowData["planname"] || "-",
                    financialyear: rowData["financialyear"] || "-",

                    irregularity: rowData["irregularitiescode"] === "01" ?
                        "Serious" : rowData["irregularitiescode"] === "02" ?
                        "Non-Serious" : "-"
                });

            } catch (e) {}
        });

        if (excelData.length === 0) {
            alert("No data available for export!");
            return;
        }

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);

        XLSX.utils.sheet_add_aoa(ws, [
            [reportTitle]
        ], {
            origin: "A1"
        });

        ws['!merges'] = [{
            s: {
                r: 0,
                c: 0
            },
            e: {
                r: 0,
                c: headers.length - 1
            }
        }];

        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A2"
        });
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A3"
        });

        XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
        XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
    }



    $(document).ready(function() {
        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'
        var distcode = '<?php echo $distcode; ?>'
        var regioncode = '<?php echo $regioncode; ?>'
        var deptcode = '<?php echo $deptcode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {} else if (sessionrole == regionrole) {} else if (sessionrole == headofficerole) {} else if (sessionrole == dgarole) {} else if (sessionrole == adminrole) {}


    });




    function reset_form() {
        var lang = getLanguage();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        var form = $('#paradetails_form');

        form[0].reset();


        if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
        }

        if (sessionrole == distrole) {
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#category').val('').select2();
            $('#irregularity').val('').select2();
            $('#subcategory').val('').select2();
            onchange_region('institution', 'instmappingcode');
            onchange_region('catcode', 'category');
        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#auditquarter').val(['']).select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#irregularity').val('').select2();

            onchange_region('region', 'regioncode');
            onchange_region('catcode', 'category');

        } else if (sessionrole == dgarole) {


            $('#deptcode,#regioncode,#distcode').select2('destroy');
            $('#deptcode,#regioncode,#distcode').select2(null);
            $('#deptcode,#regioncode,#distcode').select2();
            $('#category').val('').select2();
            $('#subcategory').val('').select2();
            $('#distcode').val('').select2();
            $('#regioncode').val('').select2();
            $('#auditquarter').val(['']).select2();
            $('#instmappingcode').val('').select2();
            $('#irregularity').val('').select2();
            $('#deptcode').val('').select2();


        } else if (sessionrole == adminrole) {

            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();


        }
        var selectedDept = $('#deptcode').val();

        if (selectedDept && (selectedDept.includes('01') || selectedDept.includes('05'))) {
            $('#subcatdiv').hide();
            $('#subcategory').val('').trigger('change');
        } else {
            $('#subcatdiv').show();
        }


    }
    document.getElementById("downloadBtn").addEventListener("click", function() {

        const element = document.getElementById("pdf-preview");

        const slipNo = document.querySelector(".slipnodyn")?.innerText || "Slip_Report";

        const opt = {
            margin: [10, 10, 10, 10],
            filename: `Slip_${slipNo}.pdf`,
            image: {
                type: "jpeg",
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            jsPDF: {
                unit: "mm",
                format: "a4",
                orientation: "portrait"
            }
        };

        html2pdf().set(opt).from(element).save();
    });
</script>


@endsection