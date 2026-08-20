@section('content')
@section('title', 'Mapping Of Alllocation & Objection ')
@extends('index2')
@include('common.alert')
@php

    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<style>
    /* table tbody tr td {
        padding: 15px !important;
    }*/

    .mapping_table {
        table-layout: fixed;
        width: 100%;
    }
</style>


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="work_obj_map_label">
                Mapping
            </div>
            <div class="card-body">
                <form id="map_allocation_objection" class="map_allocation_objection">
                    @csrf
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <table id="fileexport" class="table w-100 table-bordered" id="config">
                        <tbody>
                            <tr>
                                <td style="width:100%;" colspan="7">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label required lang" for="deptcode"
                                                key="department">Department</label>
                                            <select class="form-select mr-sm-2 select2 lang-dropdown"
                                                <?php echo $make_dept_disable; ?> id="deptcode" name="deptcode"
                                                onchange="onchange_deptcode('','','','','','')">
                                                <option value="" data-name-en="---Select Department---"
                                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">Select
                                                    Department</option>

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
                                                        data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                                        Available
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required lang" key="instcat_label">Category</label>
                                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="cat_code"
                                                name="cat_code" onchange="onchange_category('','','')">

                                                <option value="" data-name-en="-- Select Category --"
                                                    data-name-ta="-- வகையைத் தேர்ந்தெடுக்கவும் --">-- Select
                                                    Category --
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required lang" key="account_subcat">Sub
                                                Category</label>
                                            <select class="form-select mr-sm-2 select2 lang-dropdown" multiple
                                                id="subcategory" name="subcategory">

                                                {{-- <option value="" data-name-en="-- Select Sub Category --"
                                                    data-name-ta="-- துணை வகையைத் தேர்ந்தெடுக்கவும் --">-- Select
                                                    Sub
                                                    Category --</option> --}}
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required lang" key="group_head">Group</label>
                                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="groupid"
                                                name="groupid" onchange="fetch_allocatedtowhom(this)">

                                                <option value="" data-name-en="-- Select Group --"
                                                    data-name-ta="-- குழுவைத் தேர்ந்தெடுக்கவும் --">-- Select Group
                                                    --
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required lang" key="allocatetowhom">Allocated
                                                To
                                                Whom</label>
                                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="allocate_id"
                                                name="allocate_id">
                                                <option value="" data-name-en="---Select Allocate to Whom---"
                                                    data-name-ta="---யாருக்கு ஒதுக்கு என்பதைத் தேர்ந்தெடுக்கவும்---">
                                                    Select Allocate
                                                    to Whom
                                                </option>
                                                <option value="N" data-name-en="Team Member"
                                                    data-name-ta="குழு உறுப்பினர்">Team
                                                    Member</option>
                                                <option value="Y" data-name-en="Team Head"
                                                    data-name-ta="குழுத் தலைவர்">
                                                    Team Head
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table id="map_1" class="table w-100 table-bordered datatables-basic mapping_table">
                        <tbody>
                            <tr class="callforrecords_row_1" style="width:15%">
                                <td class="callforrecords_column_1 " rowspan="3">
                                    <p class="lang" key="cfr_head"> Call For Records
                                    <p>
                                        <br><br>
                                        <select class="form-select mr-sm-2 select2 lang-dropdown"
                                            id="call_for_records_1" name="call_for_records_1">

                                            <option value="" data-name-en="-- Select Call For Records --"
                                                data-name-ta="-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --">--
                                                Select
                                                Call For
                                                Records --
                                            </option>
                                        </select>
                                </td>
                                <td colspan="2" class="lang" key="workall_head" style="width:35%">Work
                                    Allocation</td>
                                <td colspan="2" class="lang" key="Obj_label" style="width:30%">Objection
                                </td>
                                <td colspan="1" class="lang" key="allocatedtowhom" style="width:10%">
                                    Allocate
                                    To Whom</td>

                                <td style="width:5%"></td>
                                <td class="callforrecords_column_1" rowspan="3" style="width:5%">
                                    <div class=" d-flex">
                                        {{-- <button onclick="CallforrecorddeleteTable();" type="button"
                                            data-repeater-create="" class="btn btn-danger hstack gap-5">

                                            <i class="ti ti-trash ms-1 fs-5"></i>
                                        </button> --}}
                                        <button onclick="Callforrecordsaddnew();" type="button"
                                            data-repeater-create="" class="btn btn-success ">

                                            <i class="ti ti-circle-plus  fs-3"></i>
                                        </button>
                                    </div>


                                </td>
                            </tr>
                            <tr>
                                <td class="lang" key="workall_head">Main Work Allocation</td>
                                <td class="lang" key="subwork_head">Sub Work Allocation</td>
                                <td class="lang" key="main_obj_label">Main Objection</td>
                                <td class="lang" key="sub_obj_label">Sub Objection</td>
                                <td class="lang" key="allocatetowhom">Allocate To Whom</td>
                                <td class="lang" key=""></td>
                            </tr>
                            <tr class="work_row_1_1">
                                <td>
                                    <select class="form-select select2 lang-dropdown" name="main_work_1_1"
                                        id="main_work_1_1" onchange="onchange_majorwork('main_work_1_1','','')">

                                        <option value="" data-name-en="-- Select Main Work Allocation --"
                                            data-name-ta="-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">--
                                            Select
                                            Main Work
                                            Allocation --
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select select2 lang-dropdown" name="sub_work_1_1"
                                        id="sub_work_1_1">
                                        <option value="" data-name-en="-- Select Sub Work Allocation --"
                                            data-name-ta="-- துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">--
                                            Select
                                            Sub Work Allocation --
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select select2 lang-dropdown" name="main_obj_1_1"
                                        id="main_obj_1_1" onchange="onchange_mainObj('main_obj_1_1','','')">

                                        <option value="" data-name-en="-- Select Main Objection --"
                                            data-name-ta="-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">-- Select
                                            Main Objection --
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select select2 lang-dropdown" name="sub_obj_1_1"
                                        id="sub_obj_1_1">

                                        <option value="" data-name-en="-- Select Sub Objection --"
                                            data-name-ta="-- துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">-- Select
                                            Sub Objection --
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select select2 lang-dropdown" id="allocatedtowhom_1_1"
                                        name="allocatedtowhom_1_1">
                                        <option value="" data-name-en="---Select User Type---"
                                            data-name-ta="---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---">Select User Type
                                        </option>
                                        <option value="N" data-name-en="Team Member"
                                            data-name-ta="குழு உறுப்பினர்">
                                            Team
                                            Member</option>
                                        <option value="Y" data-name-en="Team Head" data-name-ta="குழுத் தலைவர்">
                                            Team Head
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <button onclick="addNewWorkRow('1',this);"
                                        class="btn btn-success fw-medium addNewWorkRow" type="button">
                                        <i class="ti ti-circle-plus fs-3 d-flex"></i>
                                    </button>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row justify-content-center">

                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" key="savebtn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save </button>

                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                id="reset_button">Clear</button>
                        </div>
                    </div>

                </form>



            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="work_obj_map_table">Work Allocation and Objection
                Mapping
                Details</div>
            <div class="card-body">
                <div class="datatables">
                    <div class="row">
                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="department"
                                for="deptcode1">Department</label>
                            <select class="form-select lang-dropdown" id="deptcode1" name="deptcode1"
                                <?php echo $make_dept_disable; ?> onchange="initializeDataTable()">
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
                                        data-name-ta="No Department Available">
                                        No Departments Available
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="form-label required lang" key="allocatedtowhom"
                                for="allocatedtowhom">Allocated to
                                Whom</label>
                            <select class="form-select lang-dropdown" id="allocatedtowhom_filt"
                                name="allocatedtowhom_filt" onchange="initializeDataTable()">
                                <option value="" data-name-en="---Select User Type---"
                                    data-name-ta="---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---">Select User Type</option>
                                <option value="N" data-name-en="Team Member" data-name-ta="குழு உறுப்பினர்">
                                    Team
                                    Member</option>
                                <option value="Y" data-name-en="Team Head" data-name-ta="குழு தலைவர்" selected>
                                    Team Head
                                </option>
                            </select>

                        </div>
                    </div>
                    <div id="datatable_loader" class="hide_this">
                        <div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;">
                        </div>
                        <div class="mt-2">Loading data...</div>
                    </div>
                    <div class="table-responsive hide_this" id="tableshow">

                        <table id="mappallocationobj_table"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic ">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang" key="department">Department</th>
                                    <th class="lang" key="instcat_label">Category</th>
                                    <th class="lang" key="allocatedtowhom">Allocated to Whom</th>
                                    <th class="lang" key="callforrecords_label">Call For Rcords</th>
                                    <th class="lang" key="workall_head">Main Work Allocation</th>
                                    <th class="lang" key="subwork_head">Minor Work Allocation</th>

                                    <th class="lang" key="main_obj_label">Objection</th>
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
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="../assets/js/datatable/datatable-advanced.init.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    var count = 1; // Table count
    var work_row_count = {}; // Object to track work row counts per table
    var allocationtoId = '';
    let validator;
    var majorworkdet;
    var mainobjdet;
    var callforrec;



    function applyValidationToNewFields(inputName, message) {
        let $input = $("[name='" + inputName + "']"); // Select input by name

        if ($input.length) {
            let validator = $("#map_allocation_objection").data("validator"); // Get validator instance

            if (!validator) {
                $("#map_allocation_objection").validate(); // Ensure validation is initialized
                validator = $("#map_allocation_objection").data("validator");
            }

            $input.rules("remove");
            $input.rules("add", {
                required: true,
                messages: {
                    required: message
                }
            });

            validator.element($input);

            // Ensure validation runs on change
            $input.on("change", function() {
                $(this).valid();
            });

            // ? Handle Select2 error placement
            if ($input.hasClass("select2-hidden-accessible")) {
                let $select2Container = $input.next(".select2");
                $select2Container.removeClass("error"); // Remove previous error class

                // Check if an error message is already present
                if ($select2Container.next(".error").length === 0) {
                    $("<label class='error'>" + message + "</label>").insertAfter($select2Container);
                }
            }
        } else {
            console.error("? Element not found:", inputName);
        }
    }


    function changeLanguage(selectedLang) {

        $(".lang").each(function() {
            let key = $(this).attr("key");
            if (arrLang[lang][key] && arrLang[lang][key]) {
                $(this).text(arrLang[lang][key]);
            }
        });


    }


    function Callforrecordsaddnew() {
        count++; // Increment table count
        work_row_count[count] = 1; // Start work row count from 1 for this table
        const lang = getLanguage('');
        let newRow = `
        <table id="map_${count}" class="table w-100 table-bordered datatables-basic mapping_table">
            <tbody>
                <tr class="callforrecords_row_${count}">
                    <td class="callforrecords_column_${count}" rowspan="3" style="width:15%">
                       <p class="lang" key="cfr_head"> Call For Records
                                    <p>
                        <br><br>
                        <select class="form-select select2 lang-dropdown" id="call_for_records_${count}" name="call_for_records_${count}">
                            <option value="" data-name-en="-- Select Call For Records --"
                                                data-name-ta="-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --">
                                                ${lang=='en'?`-- Select Call For Records --`:`-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --`}
                                            </option>
                        </select>
                    </td>


                    <td colspan="2" class="lang" key="workall_head" style="width:35%">Work Allocation</td>
                    <td colspan="2" class="lang" key="Obj_label" style="width:30%">Objection</td>
                    <td colspan="1" class="lang" key="allocatedtowhom" style="width:10%">Allocate to Whom</td>
                    <td style="width:5%"></td>
                    <td class="callforrecords_column_${count}" rowspan="3" style="width:5%">
                          <div class=" ">
                          <button onclick="CallforrecorddeleteTable(this);" type="button" class="btn btn-danger cfr_delete">
                            <i class="ti ti-trash  fs-3"></i>
                        </button>
                        <button onclick="Callforrecordsaddnew();" type="button" class="btn btn-success  mt-2">
                            <i class="ti ti-circle-plus fs-3"></i>
                        </button>
                        </div>
                    </td>
                </tr>
                <tr>

                    <td class="lang" key="workall_head">Main Work Allocation</td>
                    <td class="lang" key="subwork_head">Sub Work Allocation</td>
                    <td class="lang" key="main_obj_label">Main Objection</td>
                    <td class="lang" key="sub_obj_label">Sub Objection</td>
                    <td class="lang" key="allocatetowhom">Allocate To Whom</td>
                    <td></td>
                </tr>
                <tr class="work_row_${count}_1">
                    <td>
                        <select class="form-select select2 main-work lang-dropdown" name="main_work_${count}_1" id="main_work_${count}_1"
                        onchange="onchange_majorwork('main_work_${count}_1','','')">
                            <option value="" data-name-en="-- Select Main Work Allocation --"
                                            data-name-ta="-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`-- Select Main Work Allocation --`:`-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select select2 lang-dropdown" name="sub_work_${count}_1" id="sub_work_${count}_1">
                            <option value="" data-name-en="-- Select Sub Work Allocation --"
                                            data-name-ta="-- துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                               ${lang=='en'?` -- Select Sub Work Allocation --`:`-- துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select select2 lang-dropdown" name="main_obj_${count}_1" id="main_obj_${count}_1"
                        onchange="onchange_mainObj('main_obj_${count}_1','','')">
                           <option value="" data-name-en="-- Select Main Objection --"
                                            data-name-ta="-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`-- Select Main Objection --`:`-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}
                                        </option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select select2 lang-dropdown" name="sub_obj_${count}_1" id="sub_obj_${count}_1">
                            <option value="" data-name-en="-- Select Sub Objection --"
                                            data-name-ta="-- துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                              ${lang=='en'?` -- Select Sub Objection --`:`-- துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}
                                        </option>
                        </select>
                    </td>
                    <td>
                                    <select class="form-select select2 lang-dropdown" id="allocatedtowhom_${count}_1"
                                        name="allocatedtowhom_${count}_1">
                                        <option value="" data-name-en="---Select User Type---"
                                            data-name-ta="---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---">
                                              ${lang=='en'?`---Select User Type---`:`---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---`}
                                        </option>

                                          <option value="N" data-name-en="Team Member"  data-name-ta="குழு உறுப்பினர்">
                                            ${lang=='en'?` Team Member`:`குழு உறுப்பினர்`}</option>
                                        <option value="Y" data-name-en="Team Head" data-name-ta="குழுத் தலைவர்">
                                             ${lang=='en'?` Team Head`:`குழுத் தலைவர்`}
                                        </option>
                                    </select>
                                </td>

                    <td>
                        <button onclick="addNewWorkRow(${count}, this);" class="btn btn-success fw-medium addNewWorkRow" type="button">
                            <i class="ti ti-circle-plus fs-5 d-flex"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>`;

        // Append the new table **next to** the last table
        $(".mapping_table").last().after(newRow);

        allocationtoId != '' ? $(`#allocatedtowhom_${count}_1`).val(allocationtoId).prop('disabled', true).trigger(
            'change') : '';

        changeLanguage(lang)
        // Re-initialize Select2 for dynamically added elements
        $(".select2").select2();

        // $(document).on("change", ".main-work", function() {
        //     let majorworkallocationtypeid = $(this).attr("id");
        //     onchange_majorwork(majorworkallocationtypeid);
        // });



        checkofData(count + '_1', 'out');

        applyValidationToNewFields(`main_work_${count}_1`, 'Select Main Work Allocation');
        applyValidationToNewFields(`main_obj_${count}_1`, 'Select Main Objection');
        applyValidationToNewFields(`sub_obj_${count}_1`, 'Select Sub Objection');
        applyValidationToNewFields(`call_for_records_${count}`, 'Select Call For Records');
        applyValidationToNewFields(`allocatedtowhom_${count}_1`, 'Select Allocated Whom');



    }

    function CallforrecorddeleteTable(button) {
        event.preventDefault();

        if (!button) {
            console.error("Error: No button reference passed.");
            return;
        }

        let $table = $(button).closest("table");

        if (!$table.length) {
            console.error("Error: No parent table found for the delete button.");
            return;
        }

        let tableId = $table.attr("id");
        if (!tableId || !tableId.startsWith("map_")) {
            console.error("Error: Invalid table ID.", tableId);
            return;
        }

        let tableIndex = parseInt(tableId.split("_")[1]);

        console.log(`Table to be deleted: ${tableId}`);

        // ? Remove the table
        $table.remove();

        // ? Update remaining tables' IDs dynamically
        $("table[id^='map_']").each(function(index) {
            let newIndex = index + 1;
            let oldIndex = parseInt($(this).attr("id").split("_")[1]);
            let newTableId = `map_${newIndex}`;
            $(this).attr("id", newTableId);

            console.log(`Updating Table: ${oldIndex} ? ${newIndex}`);

            // ? Update all related IDs and names inside the table
            $(this).find("[id]").each(function() {
                let oldId = $(this).attr("id");
                let newId = oldId.replace(/_\d+/, `_${newIndex}`);
                $(this).attr("id", newId);
            });

            $(this).find("[name]").each(function() {
                let oldName = $(this).attr("name");
                let newName = oldName.replace(/_\d+/, `_${newIndex}`);
                $(this).attr("name", newName);
            });

            $(this).find("[class]").each(function() {
                let oldClass = $(this).attr("class");
                let newClass = oldClass.replace(/_\d+/, `_${newIndex}`);
                $(this).attr("class", newClass);
            });

            // ? Update onclick attributes inside the table
            $(this).find("button").each(function() {
                let oldOnclick = $(this).attr("onclick");
                if (oldOnclick) {
                    let newOnclick = oldOnclick.replace(/\(\d+/, `(${newIndex}`);
                    $(this).attr("onclick", newOnclick);
                }
            });

            console.log(`Updated Table IDs and attributes for: ${newTableId}`);
        });

        console.log("Final Table IDs:", $("table[id^='map_']").map(function() {
            return $(this).attr("id");
        }).get());
    }







    function removeWorkRow(tableCount, workRowNumber, button, event) {
        event.preventDefault(); // Prevent form submission

        let rowToRemove = $(`.work_row_${tableCount}_${workRowNumber}`);

        if (rowToRemove.length > 0) {
            let remainingRows = $(`#map_${tableCount} tbody tr[class^="work_row_${tableCount}_"]`);
            let lastRow = remainingRows.last(); // Store the last row before removing

            rowToRemove.remove(); // ? Remove the row

            let callForRecordsTd = $(`.callforrecords_column_${tableCount}`);
            let currentRowspan = parseInt(callForRecordsTd.attr("rowspan")) || 3;

            if (currentRowspan > 3) {
                callForRecordsTd.attr("rowspan", currentRowspan - 1);
            }

            // ? Update numbering of remaining rows
            let updatedRows = $(`#map_${tableCount} tbody tr[class^="work_row_${tableCount}_"]`);
            updatedRows.each(function(index) {
                let newNumber = index + 1; // Start numbering from 1
                let oldClass = $(this).attr("class");
                let newClass = `work_row_${tableCount}_${newNumber}`;

                $(this).removeClass(oldClass).addClass(newClass); // ? Replace class

                $(this).find("select").each(function() {
                    let $select = $(this);

                    if ($select.data('select2')) {
                        $select.select2('destroy'); // ? Destroy Select2
                    }

                    let oldId = $select.attr("id");
                    let idParts = oldId.split("_");
                    let nameParts = $select.attr("name").split("_");

                    let newId = idParts.slice(0, -1).join("_") + "_" + newNumber;
                    let newName = nameParts.slice(0, -1).join("_") + "_" + newNumber;

                    $select.attr("id", newId);
                    $select.attr("name", newName);

                    let onchangeAttr = $select.attr("onchange");
                    if (onchangeAttr) {
                        onchangeAttr = onchangeAttr
                            .replace(/onchange_majorwork\('.*?'/, `onchange_majorwork('${newId}'`)
                            .replace(/onchange_mainObj\('.*?'/, `onchange_mainObj('${newId}'`);
                        $select.attr("onchange", onchangeAttr);
                    }

                    let oldErrorId = `${oldId}-error`;
                    let newErrorId = `${newId}-error`;
                    let $errorElement = $(`#${oldErrorId}`);
                    if ($errorElement.length > 0) {
                        $errorElement.attr("id", newErrorId);
                        $errorElement.attr("for", newId);
                    }

                    $select.select2(); // ? Reinitialize Select2
                });

                // ? Ensure "Remove" button onclick is updated, but not the last row's "Add" button
                if (!$(this).is(lastRow)) {
                    $(this).find("button").attr("onclick",
                        `removeWorkRow(${tableCount}, ${newNumber}, this, event)`);
                }
            });

            if (work_row_count.hasOwnProperty(tableCount)) {
                work_row_count[tableCount]--;
                if (work_row_count[tableCount] === 0) {
                    delete work_row_count[tableCount];
                }
            }

            console.log("Updated work_row_count:", work_row_count);

            // ? Preserve the last row's "Add" button
            if (updatedRows.length === 1) {
                let lastRowButton = lastRow.find("td:last button");
                if (!lastRowButton.hasClass("btn-success")) { // Check if it's already an "Add" button
                    lastRowButton.replaceWith(`
                    <button onclick="addNewWorkRow(${tableCount}, this);" class="btn btn-success fw-medium addNewWorkRow">
                        <i class="ti ti-circle-plus"></i>
                    </button>
                `);
                }
            }
        }
    }



    function addNewWorkRow(tableCount, button) {
        if (!work_row_count[tableCount]) {
            work_row_count[tableCount] = 1;
        }
        work_row_count[tableCount]++; // Increment work row count
        let workRowNumber = work_row_count[tableCount]; // Get row number
        const lang = getLanguage('');
        let newWorkRow = `
        <tr class="work_row_${tableCount}_${workRowNumber}">
            <td>
                <select class="form-select select2 lang-dropdown" name="main_work_${tableCount}_${workRowNumber}" id="main_work_${tableCount}_${workRowNumber}"
                onchange="onchange_majorwork('main_work_${tableCount}_${workRowNumber}','','')">
                    <option value="" data-name-en="-- Select Main Work Allocation --"
                                            data-name-ta="--- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`-- Select Main Work Allocation --`:`--- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}
                                        </option>
                </select>
            </td>
            <td>
                <select class="form-select select2 lang-dropdown" name="sub_work_${tableCount}_${workRowNumber}" id="sub_work_${tableCount}_${workRowNumber}">
                     <option value="" data-name-en="-- Select Sub Work Allocation --"
                                            data-name-ta="-- துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`  -- Select Sub Work Allocation --`:`-- துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>

                </select>
            </td>
            <td>
                <select class="form-select select2 lang-dropdown" name="main_obj_${tableCount}_${workRowNumber}" id="main_obj_${tableCount}_${workRowNumber}"
                onchange="onchange_mainObj('main_obj_${tableCount}_${workRowNumber}','','')">

                       <option value="" data-name-en="-- Select Main Objection --"
                                            data-name-ta="-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`  -- Select Main Objection --`:`-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>
                </select>
            </td>
            <td>
                <select class="form-select select2 lang-dropdown" name="sub_obj_${tableCount}_${workRowNumber}" id="sub_obj_${tableCount}_${workRowNumber}">
                      <option value="" data-name-en="-- Select Sub Objection --"
                                            data-name-ta="-- துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                             ${lang=='en'?` -- Select Sub Objection --`:`-- துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}


                                        </option>

                </select>
            </td>
             <td>
                                    <select class="form-select select2 lang-dropdown" id="allocatedtowhom_${tableCount}_${workRowNumber}"
                                        name="allocatedtowhom_${tableCount}_${workRowNumber}">
                                        <option value="" data-name-en="---Select Allocated to Whom ---"
                                            data-name-ta="--- யாருக்கு ஒதுக்கப்பட்டது என்பதைத் தேர்ந்தெடுக்கவும் ---">
                                             ${lang=='en'?`---Select Allocated to Whom ---`:`--- யாருக்கு ஒதுக்கப்பட்டது என்பதைத் தேர்ந்தெடுக்கவும் ---`}
                                        </option>
                                        <option value="N" data-name-en="Team Member"  data-name-ta="குழு உறுப்பினர்">
                                            ${lang=='en'?` Team Member`:`குழு உறுப்பினர்`}</option>
                                        <option value="Y" data-name-en="Team Head" data-name-ta="குழு தலைவர்">
                                             ${lang=='en'?` Team Head`:`குழு தலைவர்`}
                                        </option>
                                    </select>
                                </td>
            <td>
                <button onclick="addNewWorkRow(${tableCount}, this);" class="btn btn-success fw-medium addNewWorkRow">
                    <i class="ti ti-circle-plus"></i>
                </button>
            </td>
        </tr>`;

        let table = $(`#map_${tableCount} tbody`);


        // allocationtoId != '' ? alert('Yes') : alert('No');

        if (table.length > 0) {
            // ? Replace previous row s button with a "Remove" button
            $(button).replaceWith(`
            <button onclick="removeWorkRow(${tableCount}, ${workRowNumber-1}, this, event);" class="btn btn-danger fw-medium">
                <i class="ti ti-circle-minus"></i>
            </button>
        `);

            // ? Append the new row
            table.append(newWorkRow);
            allocationtoId != '' ? $(`#allocatedtowhom_${tableCount}_${workRowNumber}`).val(allocationtoId).prop(
                'disabled', true).trigger('change') : '';

            // ? Increase rowspan of `.callforrecords_column_X`
            let callForRecordsTd = $(`.callforrecords_column_${tableCount}`);
            let currentRowspan = parseInt(callForRecordsTd.attr("rowspan")) || 3;
            callForRecordsTd.attr("rowspan", currentRowspan + 2);

            // let colForRecordsTd = $(`#map_${tableCount} thead th[colspan]`);
            // if (colForRecordsTd.length > 0) {
            //     let currentColspan = parseInt(colForRecordsTd.attr("colspan")) || 1;
            //     colForRecordsTd.attr("colspan", currentColspan + 1);
            // }


            // ? Reinitialize Select2
            $(".select2").select2();

            checkofData(tableCount + '_' + workRowNumber, 'in');

            applyValidationToNewFields(`main_work_${tableCount}_${workRowNumber}`, 'Select Main Work Allocation');
            applyValidationToNewFields(`main_obj_${tableCount}_${workRowNumber}`, 'Select Main Objection');
            applyValidationToNewFields(`sub_obj_${tableCount}_${workRowNumber}`, 'Select Sub Objection');
            applyValidationToNewFields(`sub_obj_${tableCount}_${workRowNumber}`, 'Select Sub Objection');
            applyValidationToNewFields(`allocatedtowhom_${tableCount}_${workRowNumber}`, 'Select Allocated Whom');




        } else {
            console.error(`Table #map_${tableCount} not found!`);
        }
    }


    // var lang = getLanguage('');

    function onchange_deptcode(deptcode, catcode = '', callforrecordsid = '', majorworkallocationtypeid = '',
        mainobjectionid = '', groupid = '') {

        const catcodeDropdown = $('#cat_code');
        const subcatcodeDropdown = $('#subcategory');
        const callforrecordsDropdown = $('#callforrec');

        const groupDropdown = $('#groupid');

        $('#subcategory').val(null).trigger('change');
        $('#allocate_id').prop('disabled', false).val(null).trigger('change');
        $('[id^="allocatedtowhom_"]').prop('disabled', false).val(null).trigger('change');
        allocationtoId = '';
        // $('#subcategory').append(
        //     '<option value="" data-name-en="---Select Subcategory---" data-name-ta="--- ???????????? ????????????????? ---">---Select Subcategory---</option>'
        // );
        const lang = getLanguage('')


        if (deptcode == '') {
            var deptcode = $('#deptcode').val();


        }

        if (deptcode === '01' || deptcode === '05') {

            $('#cat_code').select2('destroy');
            $('#cat_code').attr('multiple', 'multiple');
            $('#cat_code').attr('name', 'cat_code[]');
            $('#cat_code').attr('required', true);
            $('#cat_code').select2();

        } else {

            $('#cat_code').select2('destroy');
            $('#cat_code').removeAttr('multiple');
            $('#cat_code').attr('name', 'cat_code');
            $('#cat_code').val(null);
            $('#cat_code').select2();

        }

        if (!deptcode) {
            catcodeDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Category Available"
                            data-name-ta="எந்த வகையும் கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'No Category Available' : 'எந்த வகையும் கிடைக்கவில்லை'}
                    </option>
                `);
            subcatcodeDropdown.append(`
            <option value="" disabled id=""
                    data-name-en="No SubCategory Available"
                    data-name-ta="எந்த வகையும் கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'No SubCategory Available' : 'எந்த வகையும் கிடைக்கவில்லை'}
            </option>
			
`);


        }


        $.ajax({
            url: '/masters/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                data = response.category;
                $('#cat_code').empty();
                catcodeDropdown.html(
                    `<option value="" data-name-en="---Select Category---"data-name-ta="----வகையைத் தேர்ந்தெடுக்கவும்---">${lang === 'ta' ? '----வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}</option>`
                );

                //  $('#cat_code').append(
                //      '<option value="" data-name-en="---Select Category---"data-name-ta="--- ??????? ?????????????????---">---Select Category---</option>'
                //  );
                $.each(data, function(index, category) {
                    var isSelected = category.catcode == catcode ? 'selected' : '';
                    // alert(isSelected);
                    $('#cat_code').append(
                        '<option value="' + category.catcode + '"' +
                        ' if_subcat="' + category.if_subcategory + '"' +

                        ' data-name-en="' + category.catename + '"' +
                        ' data-name-ta="' + category.cattname + '" ' + isSelected + '>' +
                        (lang === "en" ? category.catename : category.cattname) +
                        '</option>'
                    );
                });
                //////////////////////GROUP////////////////////
                var groupData = response.group;
                $('#groupid').empty();
                groupDropdown.html(`<option value=""  data-name-en="---Select Group---" data-name-ta="---குழுவைத் தேர்ந்தெடுக்கவும்---">
              ${lang === 'ta' ? '---குழுவைத் தேர்ந்தெடுக்கவும்---' : '---SelectGroup Name---'}
             </option>`);
                //  $('#cat_code').append(
                //      '<option value="" data-name-en="---Select Category---"data-name-ta="--- ??????? ?????????????????---">---Select Category---</option>'
                //  );
                $.each(groupData, function(index, group) {
                    var isSelected = group.groupid == groupid ? 'selected' : '';
                    // alert(isSelected);
                    $('#groupid').append(
                        '<option value="' + group.groupid + '"' +

                        ' allocatedtowhom="' + group.allocatedtowhom + '"' +
                        ' data-name-en="' + group.groupename + '"' +
                        ' data-name-ta="' + group.grouptname + '" ' + isSelected + '>' +
                        (lang === "en" ? group.groupename : group.grouptname) +
                        '</option>'
                    );
                });
                //////Callforrecords, Main, Sub Objection , Work Allocation data ////////////
                majorworkdet = response.majorwork;
                mainobjdet = response.mainobj;
                callforrec = response.callforrec;





                $('select[id^="call_for_records_"]').each(function() {

                    let selectId = $(this).attr('id');
                    let selectedValue = $(this).val(); // Preserve selected value

                    $(this).html(`
                     <option value="" data-name-en="-- Select Call For Records --"
                                                data-name-ta="-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --">
                      ${lang=='en'?`-- Select Call for Records--`:`-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --`}

                                            </option>
                `);
                    $.each(callforrec, function(index, record) {
                        let isSelected = record.id == selectedValue ? 'selected' : '';
                        $(`#${selectId}`).append(`
                        <option value="${record.callforrecordsid}"
                            data-name-en="${record.callforrecordsename}"
                            data-name-ta="${record.callforrecordstname}" ${isSelected}>
                            ${lang === "en" ? record.callforrecordsename : record.callforrecordstname}
                        </option>
                    `);
                    });
                });

                $('select[id^="main_work_"]').each(function() {

                    let selectId = $(this).attr('id');
                    let selectedValue = $(this).val(); // Preserve selected value

                    $(this).html(`

                                                <option value="" data-name-en="-- Select Main Work Allocation --"
                                            data-name-ta="-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`-- Select Main Work Allocation --`:`-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}
                                        </option>
                `);
                    $.each(majorworkdet, function(index, record) {
                        let isSelected = record.id == selectedValue ? 'selected' : '';
                        $(`#${selectId}`).append(`<option value="${record.majorworkallocationtypeid}"
                            data-name-en="${record.majorworkallocationtypeename}"
                            data-name-ta="${record.majorworkallocationtypetname}" ${isSelected}>
                            ${lang === "en" ? record.majorworkallocationtypeename : record.majorworkallocationtypetname}
                        </option>
                    `);
                    });
                });

                $('select[id^="main_obj_"]').each(function() {

                    let selectId = $(this).attr('id');
                    let selectedValue = $(this).val(); // Preserve selected value

                    $(this).html(`

                                          <option value="" data-name-en="-- Select Main Objection --"
                                            data-name-ta="-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                               ${lang=='en'?`  -- Select Main Objection --`:`-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>
                       `);
                    $.each(mainobjdet, function(index, mainobj) {
                        let isSelected = mainobj.mainobjectionid == selectedValue ?
                            'selected' : '';
                        $(`#${selectId}`).append(`<option value="${mainobj.mainobjectionid}"
                         data-name-en="${mainobj.objectionename}"
                         data-name-ta="${mainobj.objectiontname}" ${isSelected}>
                        ${lang === "en" ? mainobj.objectionename : mainobj.objectiontname}
                        </option>
                        `);
                    });
                });





            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function fetch_allocatedtowhom(element) {
        $('#allocate_id').val(null).trigger('change');

        allocationtoId = $(element).find(':selected').attr('allocatedtowhom');
        // alert(allocationtoId);
        if (allocationtoId)
            $('#allocate_id').val(allocationtoId).prop('disabled', true).trigger('change');
        else
            $('#allocate_id').val(null).prop('disabled', false).trigger('change');


        $('select[id^="allocatedtowhom_"]').each(function() {

            let selectId = $(this).attr('id');
            let selectedValue = $(this).val(); // Preserve selected value
            $(this).val(allocationtoId).prop('disabled', true).trigger('change');
        });



    }


    function onchange_category(catcode, subcategory = '', if_subcat = '') {
        var catcode = catcode || $('#cat_code').val();
        var selectedOption = $('#cat_code').find(':selected'); // Get the selected category
        var if_subcat = if_subcat || selectedOption.attr('if_subcat'); // Retrieve the attribute properly

        const subcategoryDropdown = $('#subcategory');
        const lang = getLanguage('')
        $.ajax({
            url: '/masters/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                catcode: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
            success: function(response) {

                $('#subcategory').empty();

                if (if_subcat === 'Y') {
                    $('#subcategory').attr('required', true);

                    // $('.subcatdiv').show();
                    if (response.subcategory && response.subcategory.length > 0) {

                        // $('#subcategory').append(
                        //     '<option value="" data-name-en="---Select Subcategory---" data-name-ta="---துணைப்பிரிவைத் தேர்ந்தெடுக்கவும்---">---Select Subcategory---</option>'
                        // );
                        $.each(response.subcategory, function(i, subcat) {

                            var isSelected = subcat.auditeeins_subcategoryid === subcategory ?
                                'selected' : '';

                            $('#subcategory').append(

                                `<option value="${subcat.auditeeins_subcategoryid}" data-name-en="${subcat.subcatename}"
                            data-name-ta="${subcat.subcattname}" ${isSelected}>
                                ${lang === "en" ? subcat.subcatename : subcat.subcattname}
                            </option>`


                            );
                        });

                    } else {

                        $('#subcategory').append(
                            '<option value="" data-name-en="---Select Subcategory---" data-name-ta="---துணைப்பிரிவைத் தேர்ந்தெடுக்கவும்---">---Select Subcategory---</option>'
                        );
                    }
                } else {
                    // $('.subcatdiv').show();
                    $('#subcategory').removeAttr('required');

                    if (catcode != '') {

                        $.each(data, function(i, subcat) {
                            if (subcat.catcode === catcode) {
                                $('#subcategory').append(
                                    `<option value="" data-name-en="${subcat.catename}" data-name-ta="${subcat.cattname}" selected>
                                  ${lang === "en" ? subcat.catename : subcat.cattname}
                                 </option>`
                                );
                                $('#subcategory').valid();
                                return false;
                            }
                        });

                    } else {
                        $('#subcategory').append(
                            '<option value="" data-name-en="---Select Subcategory---" data-name-ta="---துணைப்பிரிவைத் தேர்ந்தெடுக்கவும்---">---Select Subcategory---</option>'
                        );
                    }
                    // Hide the subcategory div when if_subcat is 'N'
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

    }

    function onchange_majorwork(majorworkallocationtypeid, subworkallocationtypeid = '') {
        let $mainWorkDropdown = $('#' + majorworkallocationtypeid);
        let mainWorkValue = $('#' + majorworkallocationtypeid).val();

        // Extract count & row number from ID (e.g., main_work_1_1 ? count=1, row=1)
        let match = majorworkallocationtypeid.match(/main_work_(\d+_\d+)/);
        if (!match) return;

        let identifier = match[1]; // Gets "1_1" (or similar)

        // Find the exact sub_work dropdown using extracted ID
        let $subworkDropdown = $('#sub_work_' + identifier);

        const lang = getLanguage();

        $subworkDropdown.html(`
        <option value="" data-name-en="---Select Sub Work Allocation---" data-name-ta="---துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணைப் பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub Work Allocation---'}
        </option>
          `);

        if (!$mainWorkDropdown.val()) {
            $subworkDropdown.append(`
            <option value="" disabled
                    data-name-en="No SubWork Allocation Available"
                    data-name-ta="துணைப்பணி ஒதுக்கீடு எதுவும் கிடைக்கவில்லை.">
                ${lang === 'ta' ? 'துணைப்பணி ஒதுக்கீடு எதுவும் கிடைக்கவில்லை.' : 'No SubWork Allocation Available'}
            </option>
        `);
            return;
        }

        $.ajax({
            url: '/masters/FilterByDept',
            method: 'POST',
            data: {
                majorworkallocationtypeid: mainWorkValue // Send original "main_work_X_Y" value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                $.each(response, function(index, subwork) {
                    var isSelected = subwork.subworkallocationtypeid ===
                        subworkallocationtypeid ?
                        'selected' : '';
                    const nameEn = subwork.subworkallocationtypeename || "No data available";
                    const nameTa = subwork.subworkallocationtypetname ||
                        "No data available";

                    const displayName = lang === "en" ? nameEn : nameTa;

                    // Append options to the **correct sub_work_X_Y dropdown**
                    $subworkDropdown.append(`
                    <option value="${subwork.subworkallocationtypeid ?? ''}"
                            data-name-en="${nameEn}"
                            data-name-ta="${nameTa}"
                            ${isSelected}>
                        ${displayName}
                    </option>
                `);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function onchange_mainObj(mainobjectiondiv_id, mainobjectionid, subobjectionid = '') {

        let $mainobjDropdown = $('#' + mainobjectiondiv_id);

        let mainobjVal = $('#' + mainobjectiondiv_id).val();

        let match = mainobjectiondiv_id.match(/main_obj_(\d+_\d+)/);

        if (!match) return;

        let identifier = match[1]; // Gets "1_1" (or similar)

        // Find the exact sub_work dropdown using extracted ID
        let $subobjDropdown = $('#sub_obj_' + identifier);
        // $subobjDropdown.empty();
        const lang = getLanguage('');

        $subobjDropdown.html(`
        <option value="" data-name-en="---Select Sub  Objection---" data-name-ta="---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub  Objection---'}
                </option>
          `);

        if (!$mainobjDropdown.val()) {
            $subobjDropdown.append(`
            <option value="" disabled id=""
                            data-name-en="No Sub Objection Available"
                            data-name-ta="துணை ஆட்சேபனை எதுவும் இல்லை.">
                            ${lang === 'ta' ? 'துணை ஆட்சேபனை எதுவும் இல்லை.' : 'No Sub Objection Available'}
                    </option>
        `);
            return;
        }

        // var mainobjectionid = $('#mainobjectionid').val();
        $.ajax({
            url: '/masters/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                mainobjectionid: mainobjVal
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                var subobjdata = response;


                $.each(subobjdata, function(index, subobj) {
                    var isSelected = subobj.subobjectionid === subobjectionid ?
                        'selected' : '';
                    const nameEn = subobj.subobjectionename || "No data available";
                    const nameTa = subobj.subobjectiontname ||
                        "No data available";


                    const displayName = lang === "en" ? nameEn : nameTa;

                    // Append options to the **correct sub_work_X_Y dropdown**
                    $subobjDropdown.append(`
                    <option value="${subobj.subobjectionid ?? ''}"
                            data-name-en="${nameEn}"
                            data-name-ta="${nameTa}"
                            ${isSelected}>
                        ${displayName}
                    </option>
                `);
                });

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function checkofData(Id, action) {
        const lang = getLanguage('');
        // Check if data exists before populating
        if (majorworkdet) {
            if (majorworkdet.length > 0) {
                // populateOptions(`#${majorworkallocationtypeid}`, majorworkdet);
                populateOptions(`#main_work_${Id}`, majorworkdet, 'work');
            }
        }

        if (mainobjdet) {
            if (mainobjdet.length > 0) {

                populateOptions(`#main_obj_${Id}`, mainobjdet, 'obj');
            }
        }
        if (action == 'out') {
            if (callforrec) {
                if (callforrec.length > 0) {
                    let cfrId = Id.split("_")[0];

                    populateOptions(`#call_for_records_${cfrId}`, callforrec, 'cfr');
                }


            }
        }





    }

    function populateOptions(selectId, data, type) {
        const lang = getLanguage('');
        if (type == "work") {

            $(selectId).html(`
                   <option value="" data-name-en="-- Select Main Work Allocation --"
                                            data-name-ta="-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --">
                                            ${lang=='en'?`-- Select Main Work Allocation --`:`-- முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும் --`}
                                        </option>

                `);

            $.each(majorworkdet, function(index, record) {
                // let isSelected = record.id == selectedValue ? 'selected' : '';
                $(`${selectId}`).append(`<option value="${record.majorworkallocationtypeid}"
                            data-name-en="${record.majorworkallocationtypeename}"
                            data-name-ta="${record.majorworkallocationtypetname}" >
                            ${lang === "en" ? record.majorworkallocationtypeename : record.majorworkallocationtypetname}
                        </option>
                    `);
            });
        }
        if (type == "obj") {

            $(selectId).html(`
                    <option value="" data-name-en="-- Select Main Objection --"
                                            data-name-ta="-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --">
                                               ${lang=='en'?`  -- Select Main Objection --`:`-- முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும் --`}

                                        </option>

                `);

            $.each(mainobjdet, function(index, record) {
                // let isSelected = record.id == selectedValue ? 'selected' : '';
                $(`${selectId}`).append(`<option value="${record.mainobjectionid}"
                            data-name-en="${record.objectionename}"
                            data-name-ta="${record.objectiontname}" >
                            ${lang === "en" ? record.objectionename : record.objectiontname}
                        </option>
                    `);
            });

        }
        if (type == "cfr") {
            $(selectId).html(`
                <option value="" data-name-en="-- Select Call For Records --"
                                                data-name-ta="-- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும் --">-- Select
                                                Call For
                                                Records --
                                            </option>
                `);
            $.each(callforrec, function(index, record) {
                // let isSelected = record.id == selectedValue ? 'selected' : '';
                $(`${selectId}`).append(`
                        <option value="${record.callforrecordsid}"
                            data-name-en="${record.callforrecordsename}"
                            data-name-ta="${record.callforrecordstname}">
                            ${lang === "en" ? record.callforrecordsename : record.callforrecordstname}
                        </option>
                    `);
            });
        }

    }


    $(document).on('click', '#buttonaction', function(event) {
        event.preventDefault(); // Prevent form submission

        // ? Apply validation to newly added fields
        applyValidationToNewFields("main_work_1_1", "Select Main Work Allocation");
        applyValidationToNewFields("main_obj_1_1", "Select Main Objection");
        applyValidationToNewFields("sub_obj_1_1", "Select Sub Objection");
        applyValidationToNewFields("call_for_records_1", "Select Call For Records");
        applyValidationToNewFields("allocatedtowhom_1_1", "Select whom to Allocate");

        // ? Ensure all dynamically added fields retain validation
        // $("#map_allocation_objection input, #map_allocation_objection select").each(function() {
        //     let $input = $(this);
        //     let inputName = $input.attr("name");

        //     if (!$input.rules()) {
        //         console.warn("?? Adding missing validation rule for:", inputName);
        //         $input.rules("add", {
        //             required: true
        //         });
        //     }
        // });

        // ? Revalidate without clearing messages
        let isValid = $("#map_allocation_objection").valid();

        if (!isValid) {
            scrollToFirstError(); // Ensure user sees the first error
            return;
        }

        // ? Proceed with data submission
        // get_insertMapdata("insert");
        check_alreadyexist();
    });



    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showAlreadyExistsMessage(inputName, message) {
        let $input = $("[name='" + inputName + "']"); // Select input by name
        if ($input.length) {
            // console.log(`Applying ALREADY EXISTS message to: ${inputName}`);


            if ($input.hasClass("select2-hidden-accessible")) {
                $input.addClass("is-invalid"); // Highlight the field as invalid

                let $select2Container = $input.siblings(".select2"); // Get the Select2 container
                // Remove existing error message to prevent duplicates
                $select2Container.next(".error").remove();
                // ? Append the error message **right after the Select2 container**
                $select2Container.after(
                    `<label id="${inputName}-error" class="error" for="${inputName}">${message}</label>`
                );

            } else {
                $input.addClass("is-invalid");
                $input.next(".invalid-feedback").remove(); // Remove old message
                $input.after(`<div class="invalid-feedback">${message}</div>`); // Add new message
            }
        } else {
            console.error("? Element not found:", inputName);
        }
    }

    function check_alreadyexist() {
        var formData = $('#map_allocation_objection').serializeArray();
        var data = getTableData();
        if (!data) {
            //alert();
            return false;
        }
        var tabledata = JSON.stringify(data);

        if (action === 'finalise') {
            finaliseflag = 'F';
        } else if (action === 'insert') {
            finaliseflag = 'Y';
        }

        formData.push({
            name: 'tabledata',
            value: tabledata
        });

        $.ajax({
            url: '/masters/check_mapAllcObj', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    let exist = response.exist_array; // Array of existing indexes
                    let hasErrors = false; // Track if any errors exist

                    if (!exist || exist.length === 0 || (exist.length === 1 && exist[0] === "")) {
                        // alert('asd');
                        get_insertMapdata("insert");
                    } else {
                        // alert();
                        exist.forEach(function(index) {
                            let matches = index.match(
                                /dataIndex: (\d+), workIndex: (\d+)/
                            ); // Extract tableCount & workRowNumber

                            if (matches) {

                                let tableCount = parseInt(matches[1]) + 1;
                                let workRowNumber = parseInt(matches[2]) + 1;
                                let fields = [];

                                let possibleFields = [
                                    `main_work_${tableCount}_${workRowNumber}`,
                                    `sub_work_${tableCount}_${workRowNumber}`,
                                    `main_obj_${tableCount}_${workRowNumber}`,
                                    `sub_obj_${tableCount}_${workRowNumber}`,
                                    `allocatedtowhom_${tableCount}_${workRowNumber}`
                                ];

                                // fields.forEach(function(fieldID) {
                                //     // console.log(field);
                                //     showAlreadyExistsMessage(fieldID,
                                //         "This field already exists!");
                                // });
                                possibleFields.forEach(function(field) {
                                    let $field = $(
                                        `#${field}`); // Select the field using its ID
                                    if ($field.length && $field.val() != '') {
                                        fields.push(
                                            field
                                        ); // Add field only if it exists and is empty
                                    }
                                });
                                fields.forEach(function(field) {
                                    showAlreadyExistsMessage(field,
                                        "This field already exists!");
                                });
                                hasErrors = true;
                            }
                        });
                        if (hasErrors) {
                            // alert(
                            //     "Some work allocations already exist. Please fix them before submitting."
                            // );
                            return false;
                        }
                    }
                    // Prevent form submission if errors exist


                    // getLabels_jsonlayout([{
                    //     id: response.message,
                    //     key: response.message
                    // }], 'N').then((text) => {
                    //     passing_alert_value('Confirmation', Object.values(
                    //             text)[0], 'confirmation_alert',
                    //         'alert_header', 'alert_body',
                    //         'confirmation_alert');
                    // });

                    // reset_form();
                    // initializeDataTable(window.localStorage.getItem('lang'));



                } else if (response.error) {}
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';
                $('#display_error').show();
                $('#display_error').text(errorMessage);

                console.error('Error details:', xhr, status, error);
            }
        });
    }


    function get_insertMapdata(action) {


        var formData = $('#map_allocation_objection').serializeArray();
        var data = getTableData();
        var tabledata = JSON.stringify(data);

        if (action === 'finalise') {
            finaliseflag = 'F';
        } else if (action === 'insert') {
            finaliseflag = 'Y';
        }

        // Push the finaliseflag to the formData array
        // formData.push({
        //     name: 'finaliseflag',
        //     value: finaliseflag
        // });
        formData.push({
            name: 'tabledata',
            value: tabledata
        });

        $.ajax({
            url: '/masters/insertmulti_mapWorkObj', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', Object.values(
                                text)[0], 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    //
                    reset_form();
                    initializeDataTable(window.localStorage.getItem('lang'));
                    // passing_alert_value('Confirmation', response.success,
                    //     'confirmation_alert', 'alert_header', 'alert_body',
                    //     'confirmation_alert');
                    //fetchAlldata();
                    // table.ajax.reload(); // Reload the table
                    // initializeDataTable(window.localStorage.getItem('lang'));


                } else if (response.error) {}
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';
                $('#display_error').show();
                $('#display_error').text(errorMessage);
                // passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                //     'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }

    function getTableData() {
        let tableData = [];
        let duplicateEntries = new Map(); // Track duplicate rows with count

        $(".mapping_table").each(function() {
            let tableId = $(this).attr("id"); // Example: map_1
            let tableIndex = tableId.split("_")[1]; // Extract table number
            let dept = $(`#deptcode`).val();
            let cat = $(`#cat_code`).val();
            let subcat = $(`#subcategory`).val();
            let group = $(`#groupid`).val();
            let callForRecords = $(`#call_for_records_${tableIndex}`).val();

            let workAllocations = [];
            let rowSet = new Map(); // Track unique row data for this table

            // Iterate over each main work allocation row
            $(`select[name^='main_work_'][id^='main_work_${tableIndex}_']`).each(function() {
                let selectId = $(this).attr("id"); // Example: main_work_1_2
                let idParts = selectId.split("_");

                if (idParts.length < 4 || idParts[2] !== tableIndex) return; // Ensure correct table

                let workIndex = idParts[3]; // Extract row number

                let mainWork = $(`#main_work_${tableIndex}_${workIndex}`).val() || "";
                let subWork = $(`#sub_work_${tableIndex}_${workIndex}`).val() || "";
                let mainObj = $(`#main_obj_${tableIndex}_${workIndex}`).val() || "";
                let subObj = $(`#sub_obj_${tableIndex}_${workIndex}`).val() || "";
                let allocateToWhom = $(`#allocatedtowhom_${tableIndex}_${workIndex}`).val() || "";

                let rowData =
                    `${mainWork}-${subWork}-${mainObj}-${subObj}-${allocateToWhom}-${callForRecords}`;

                if (rowSet.has(rowData)) {
                    let count = rowSet.get(rowData) + 1;
                    rowSet.set(rowData, count);
                    duplicateEntries.set(rowData, count);
                } else {
                    rowSet.set(rowData, 1);
                }

                workAllocations.push({
                    main_work: mainWork,
                    sub_work: subWork,
                    main_obj: mainObj,
                    sub_obj: subObj,
                    allocate_towhom: allocateToWhom,
                    call_for_records: callForRecords, // ?? Now included inside workAllocations
                });
            });

            tableData.push({
                table_index: tableIndex,
                department: dept,
                category: cat,
                subcategory: subcat,
                group: group,
                call_for_records: callForRecords,
                work_allocations: workAllocations,
            });
        });

        // ?? **Show Validation Errors for Duplicates**
        if (duplicateEntries.size > 0) {
            duplicateEntries.forEach((count, duplicate) => {
                if (count > 1) { // Show error only for actual duplicates
                    let [mainWork, subWork, mainObj, subObj, allocateToWhom, callForRecords] = duplicate
                        .split(
                            "-");

                    $(".mapping_table select, .mapping_table input").each(function() {
                        let $field = $(this);
                        // let fieldID = $field.attr("id");
                        let fieldValue = $field.val() || "";
                        let fieldID = $field.attr("id") || "";
                        ""; // Ensure it's not undefined

                        if (
                            fieldValue === mainWork ||
                            fieldValue === subWork ||
                            fieldValue === mainObj ||
                            fieldValue === subObj ||
                            fieldValue === allocateToWhom ||
                            fieldValue === callForRecords
                        ) {
                            showAlreadyExistsMessage(fieldID,
                                "Duplicate Entry");
                        }
                    });
                }
            });
            tableData = ''
            return tableData;
        } else {
            return tableData;


        }

        console.log(tableData); // Debugging Output

    }




    let dataFromServer;

    $('#translate').change(function() {
        lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        // updateSelect2Language(lang); // Update Select2 dropdown
        // changeButtonText('action', 'buttonaction', 'reset_button', '', @json($savebtn),
        //     @json($updatebtn), @json($clearbtn));
        // updateValidationMessages(getLanguage('Y'), 'map_allocation_objection');

    });

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#mappallocationobj_table')) {
            $('#mappallocationobj_table').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function initializeDataTable(lang) {
        var lang = getLanguage();
        $("#datatable_loader").show();
        let deptcode_filt = $('#deptcode1').val();
        let towhom = $('#allocatedtowhom_filt').val();
        $('#tableshow').hide();
        $.ajax({
            url: '/masters/fetchall_mapallocationObj', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode_filt: deptcode_filt,
                towhom: towhom
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
                    //console.log(dataFromServer);
                    // alert(dataFromServer);
                    renderTable(lang);
                } else {
                    $("#datatable_loader").hide();
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
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

    var table; // Global declaration

    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const categoryColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcategoryColumn = language === 'ta' ? 'subcategory_tname' : 'subcategory_ename';
        const membername = language === 'ta' ? 'Team Member' : 'Team Member';
        const leadername = language === 'ta' ? 'Team Head' : 'Team Head';
        const groupColumn = language === 'ta' ? 'grouptname' : 'groupename';
        const callforrecColumn = language === 'ta' ? 'callforrecordstname' : 'callforrecordsename';
        const majorworkColumn = language === 'ta' ? 'majorworkallocationtypetname' : 'majorworkallocationtypeename';
        const subworkColumn = language === 'ta' ? 'subworkallocationtypetname' : 'subworkallocationtypeename';
        const mainobjColumn = language === 'ta' ? 'objectiontname' : 'objectionename';
        const subobjColumn = language === 'ta' ? 'subobjectiontname' : 'subobjectionename';

        // Debugging logs
        // console.log("Language selected:", language);
        console.log("Mapped Columns:", {
            departmentColumn,
            categoryColumn,
            subcategoryColumn,
            groupColumn
        });

        // Ensure dataFromServer exists
        if (!Array.isArray(dataFromServer) || dataFromServer.length === 0) {
            console.error("No data available for DataTable.");
            return;
        }

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#mappallocationobj_table')) {
            $('#mappallocationobj_table').DataTable().clear().destroy();
        }

        // Initialize DataTable
        table = $('#mappallocationobj_table').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            data: dataFromServer,
            initComplete: function() {
                $("#datatable_loader").hide();
                $("#mappallocationobj_table").wrap(
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
                    title: columnLabels?.[categoryColumn]?.[language] || "Category Details",
                    render: function(data, type, row) {
                        return `<b>Category:</b> ${row?.[categoryColumn] || '-'} <br>
                            <b>Sub Category:</b> ${row?.[subcategoryColumn] || '-'} <br>
                            <b>Group:</b> ${row?.[groupColumn] || '-'}`;
                    },
                    className: "text-start d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "allocatetowhom",
                    title: columnLabels?.["allocatetowhom"]?.[language] || "Allocated To",
                    render: function(data) {
                        return data === 'N' ? membername : leadername;
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: callforrecColumn,
                    title: columnLabels?.[callforrecColumn]?.[language] || "Call for Records",
                    render: function(data, type, row) {
                        return row?.[callforrecColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: majorworkColumn,
                    title: columnLabels?.[majorworkColumn]?.[language] || "Major Work Allocation",
                    render: function(data, type, row) {
                        return row?.[majorworkColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: subworkColumn,
                    title: columnLabels?.[subworkColumn]?.[language] || "Sub Work Allocation",
                    render: function(data, type, row) {
                        return row?.[subworkColumn] || '-';
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: null,
                    title: columnLabels?.[mainobjColumn]?.[language] || "Objections",
                    render: function(data, type, row) {
                        return `<b>Title/Heading:</b> ${row?.[mainobjColumn] || '-'} <br>
                            <b>Categorization of Para:</b> ${row?.[subobjColumn] || '-'}`;
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                }
            ]
        });

        // Mobile column handling
        const mobileColumns = [
            categoryColumn, subcategoryColumn, groupColumn,
            callforrecColumn, majorworkColumn,
            subworkColumn, mainobjColumn, subobjColumn
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "mappallocationobj_table");

        console.log("DataTable initialized successfully.");
    }

    // Ensure button actions are handled properly
    // let customButtonActions = {
    //     customExportAction: function (e, dt, node, config) {
    //         let tableId = "mappallocationobj_table";
    //         let language = getLanguage();
    //         exportToExcel(tableId, language);
    //     }
    // };

    // Append buttons to container
    // if (table) {
    //     table.buttons().container().appendTo("#exportButtonContainer");
    // }

    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();
        let titleKey = `${tableId}_title`;
        let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
        let safeSheetName = translatedTitle.substring(0, 31);
        // ? Fetch column headers from JSON layout
        let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;
        // Define column names dynamically based on language selection
        const columnMap = {
            departmentColumn: language === 'ta' ? 'depttsname' : 'deptesname',
            categoryColumn: language === 'ta' ? 'cattname' : 'catename',
            subcategoryColumn: language === 'ta' ? 'subcategory_tname' : 'subcategory_ename',
            groupColumn: language === 'ta' ? 'grouptname' : 'groupename',
            callforrecColumn: language === 'ta' ? 'callforrecordstname' : 'callforrecordsename',
            majorworkColumn: language === 'ta' ? 'majorworkallocationtypetname' : 'majorworkallocationtypeename',
            subworkColumn: language === 'ta' ? 'subworkallocationtypetname' : 'subworkallocationtypeename',
            mainobjColumn: language === 'ta' ? 'objectiontname' : 'objectionename',
            subobjColumn: language === 'ta' ? 'subobjectiontname' : 'subobjectionename'
        };
        let headers = [{
                header: dtText["department"] || "Department",
                key: "department"
            },
            {
                header: dtText["Category"] || "Category",
                key: "Category"
            },
            {
                header: dtText["Subcategory"] || "Subcategory",
                key: "Subcategory"
            },
            {
                header: dtText["Group"] || "Group",
                key: "Group"
            },
            {
                header: dtText["AllocatedTo"] || "Allocated To",
                key: "allocatetowhom"
            },
            {
                header: dtText["Call for Records"] || "Call for Records",
                key: "callforrecColumn"
            },
            {
                header: dtText["Major Work Allocation"] || "Major Work Allocation",
                key: "majorworkColumn"
            },
            {
                header: dtText["Sub Work Allocation"] || "Sub Work Allocation",
                key: "subworkColumn"
            },
            {
                header: dtText["Main Objection"] || "Main Objection",
                key: "mainobjColumn"
            },
            {
                header: dtText["Sub Objection"] || "Sub Objection",
                key: "subobjColumn"
            }

        ];

        // Define headers
        // let headers = [
        //     { header: "Department", key: columnMap.departmentColumn },
        //     { header: "Category", key: columnMap.categoryColumn },
        //     { header: "Subcategory", key: columnMap.subcategoryColumn },
        //     { header: "Group", key: columnMap.groupColumn },
        //     { header: "Allocated To", key: "allocatetowhom" },
        //     { header: "Call for Records", key: columnMap.callforrecColumn },
        //     { header: "Major Work Allocation", key: columnMap.majorworkColumn },
        //     { header: "Sub Work Allocation", key: columnMap.subworkColumn },
        //     { header: "Main Objection", key: columnMap.mainobjColumn },
        //     { header: "Sub Objection", key: columnMap.subobjColumn }
        // ];

        // Get data from the DataTable
        let data = dataFromServer;

        // Log the first row to verify data structure
        if (data.length > 0) {
            console.log("Sample Data Row:", data[0]);
        } else {
            console.error("No data found in DataTable.");
        }

        // Extract and map data correctly
        let excelData = data.map(row => {
            return {
                [columnMap.departmentColumn]: row?.[columnMap.departmentColumn] || "-",
                [columnMap.categoryColumn]: row?.[columnMap.categoryColumn] || "-",
                [columnMap.subcategoryColumn]: row?.[columnMap.subcategoryColumn] || "-",
                [columnMap.groupColumn]: row?.[columnMap.groupColumn] || "-",
                allocatetowhom: row?.allocatetowhom === 'N' ? 'Team Member' : 'Team Head',
                [columnMap.callforrecColumn]: row?.[columnMap.callforrecColumn] || "-",
                [columnMap.majorworkColumn]: row?.[columnMap.majorworkColumn] || "-",
                [columnMap.subworkColumn]: row?.[columnMap.subworkColumn] || "-",
                [columnMap.mainobjColumn]: row?.[columnMap.mainobjColumn] || "-",
                [columnMap.subobjColumn]: row?.[columnMap.subobjColumn] || "-"
            };
        });

        // Log the formatted data to verify before exporting
        console.log("Formatted Excel Data:", excelData);

        // Create a new workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);

        // ? Add Headers in Separate Columns (Avoid Merging Issues)
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A1"
        });

        // ? Ensure Headers Align with Data
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A2"
        });

        XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
        XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
    }



    const $work_obj_mapform = $("#map_allocation_objection");
    // $(document).ready(function() {
    jsonLoadedPromise.then(() => {
        const language = getLanguage('');

        // ? Ensure form validation is initialized properly
        if (!$.data($("#map_allocation_objection")[0], "validator")) {
            validator = $("#map_allocation_objection").validate({
                rules: {
                    deptcode: {
                        required: true
                    },
                    cat_code: {
                        required: true
                    },
                    // subcategory: {
                    //     required: true
                    // },
                    groupid: {
                        required: true
                    }
                },
                messages: errorMessages[language],
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2')) {
                        error.insertAfter(element.next(
                            '.select2-container')); // Fix for Select2 dropdowns
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        }

    });

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $work_obj_mapform.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    // $('#translate').change(function() {
    //     lang = getLanguage('Y');

    //     // updateTableLanguage(getLanguage(
    //     //     'Y')); // Update the table with the new language by destroying and recreating it
    //     changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
    //         @json($updatebtn), @json($clearbtn));
    //     // updateValidationMessages(getLanguage('Y'), 'map_allocation_objection');
    // });
    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    $(document).ready(function() {

        var lang = getLanguage('');
        // // $('#map_allocation_objection')[0].reset();
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        initializeDataTable(lang);


        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        }

    });


    $('#reset_button').on('click', function() {
        // reset_form(); // Call the reset_form function
        reset_form();

    })

    function reset_form() {

        // $('#map_allocation_objection')[0].reset();
        let validator = $('#map_allocation_objection').validate();
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#cat_code,#groupid,#subcategory').val(null).trigger('change');
        } else {
            $('#cat_code,#groupid,#deptcode,#subcategory').val(null).trigger('change');
        }

        $('[id^="main_obj_"],[id^="call_for_records_"], [id^="sub_obj_"], [id^="main_work_"], [id^="sub_work_"], [id^="allocatedtowhom_"]')
            .val(
                null).trigger('change');
        $('[id^="allocatedtowhom_"]').prop('disabled', false);
        // // Clear validation errors only when clearing
        // validator.resetForm();
        $('.form-control, .form-select').removeClass('is-invalid');
        document.querySelectorAll('.mapping_table').forEach(table => {
            if (table.id !== 'map_1') {
                table.remove();
            }
        });
        document.querySelectorAll('[class^="work_row_"]').forEach(workRow => {
            if (!workRow.classList.contains('work_row_1_1')) {
                workRow.remove();
            } else {
                let remainingRows = $(`#map_1 tbody tr[class="work_row_1_1"]`);
                let lastRowButton = remainingRows.find("td:last button");
                lastRowButton.replaceWith(`
                <button onclick="addNewWorkRow(1, this);" class="btn btn-success fw-medium addNewWorkRow">
                    <i class="ti ti-circle-plus"></i>
                </button>
            `);
            }
        });
        count = 1; // Table count
        work_row_count = {};
        $('.callforrecords_column_1').attr('rowspan', 3);

        changeButtonAction('map_allocation_objection', 'action', 'buttonaction', 'reset_button',
            'display_error', @json($savebtn), @json($clearbtn),
            @json($insert));
        // change_button_as_insert('map_allocation_objection', 'action', 'buttonaction', 'display_error', '', '');
    }
</script>


@endsection
