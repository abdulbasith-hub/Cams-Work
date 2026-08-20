@extends('index2')
@section('title', 'Mapping Work and Objection')
@section('content')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<!-- jQuery (MUST be loaded first) -->
<!-- Select2 CSS -->

<?php if (isset($_COOKIE['language'])) {
    $lang_val = $_COOKIE['language'];
    if ($lang_val == '' || $lang_val == null) {
        $lang_val = 'en';
    }
} else {
    $lang_val = 'en';
}
$sessionchargedel = session('charge');
$deptcode = $sessionchargedel->deptcode;
$make_dept_disable = $deptcode ? 'disabled' : '';

?>



<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="work_obj_map_label">
                Work Allocation and Objection Mapping
            </div>
            <div class="card-body">
                <form id="map_allocation_objection" name="map_allocation_objection">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="mapallocationobjectionid" id="mapallocationobjectionid"
                        value="" />
                    <div class="row mt-2">

                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department</label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="onchange_deptcode('','','','','','')">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>

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

                        {{-- <!-- <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="department" for="validationDefault01">Department</label>
                                    <input type="hidden" id="" name="" value="">
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                        onchange="onchange_deptcode('','','','')">
                                        <option value="" data-name-en="---Select Department---" data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">
                                            ---Select Department---
                                        </option>

                                        @foreach ($dept as $departtment)
                                            <option value="{{ $departtment->deptcode }}"
                        data-name-en="{{ $departtment->deptelname }}"
                        data-name-ta="{{ $departtment->depttlname }}">
                        {{ $departtment->deptelname }}
                        </option>
                        @endforeach
                        </select>
                    </div> --> --}}



                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="instcat_label"
                                for="validationDefault01">Category</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="cat_code" name="cat_code"
                                onchange="onchange_category('','','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                <option value="" disabled id="" data-name-en="No Category Available"
                                    data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>



                            </select>
                        </div>


                        <div class="col-md-4 mb-1 subcatdiv ">
                            <label class="form-label lang required" key="sub_cat" for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                name="subcategory" onchange="">
                                <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>


                            </select>
                        </div>



                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="callforrecords_label"
                                for="validationDefault01">Call For Records </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="callforrec" name="callforrec">
                                <option value="" data-name-en="---Select Call For Records---"
                                    data-name-ta="---பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">---Select Call
                                    For Records---</option>

                                <option value="" disabled id=""
                                    data-name-en="No Call For Records Available"
                                    data-name-ta="பதிவுகளுக்கான அழைப்பு கிடைக்கவில்லை">No Call For Records Available
                                </option>

                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="group_name" for="validationDefault01">Group
                                Name
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="groupid" name="groupid">
                                <option value="" data-name-en="---Select Group---"
                                    data-name-ta="---பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">---Select Group Name
                                    ---</option>

                                <option value="" disabled id=""
                                    data-name-en="No Call For Records Available"
                                    data-name-ta="பதிவுகளுக்கான அழைப்பு கிடைக்கவில்லை">No Group Available
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="workall_head" for="validationDefault01">Major
                                Work Allocation </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="maj_work" name="maj_work"
                                onchange="onchange_majorwork('','')">

                                <option value="" data-name-en="---Select Major Work Allocation---"
                                    data-name-ta="---முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும்---">---Select Major
                                    Work Allocation---</option>

                  
                                <option value="" disabled id=""
                                    data-name-en="No Work Allocation Available"
                                    data-name-ta="பணி ஒதுக்கீடு கிடைக்கவில்லை">No Work Allocation Available</option>


                            </select>
                        </div>


                        <div class="col-md-4 mb-1">
                            <label class="form-label lang " key="subwork_head" for="validationDefault01">Sub Work
                                Allocation </label>

                            <select class="form-control custom-select mr-sm-2 lang-dropdown select2"
                                id="subworkallocationtypeid" name="subworkallocationtypeid">

                                <option value="" data-name-en="---Select SubWork Allocation---"
                                    data-name-ta="---சிறிய வேலை ஒதுக்கீடு தேர்ந்தெடுக்கவும்---">---Select SubWork
                                    Allocation---</option>
                                </option>
                                <option value="" disabled id=""
                                    data-name-en="No SubWork Allocation Available"
                                    data-name-ta="சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை">No SubWork Allocation
                                    Available</option>


                            </select>
                        </div>


                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="main_obj_label" for="validationDefault01">
                                Main
                                Objection </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="mainobjectionid"
                                name="mainobjectionid" onchange="onchange_mainObj('','')">

                                <option value="" data-name-en="---Select Main Objection---"
                                    data-name-ta="---முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">---Select Main
                                    Objection---</option>

                                                             <option value="" disabled id="no-region-option"
                                    data-name-en="No Main Objection Available"
                                    data-name-ta="முக்கிய எதிர்ப்பு கிடைக்கவில்லை">No Main Objection Available</option>


                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="sub_obj_label" for="validationDefault01">Sub
                                Objection</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2 " id="subobjectionid"
                                name="subobjectionid">
                                <option value="" data-name-en="---Select Sub  Objection---"
                                    data-name-ta="---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">---Select Sub Objection---
                                </option>
                                <option value="" disabled id=""
                                    data-name-en="No Sub Objection Available"
                                    data-name-ta="உபஎதிர்ப்பு கிடைக்கவில்லை">No Sub Objection Available</option>


                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="form-label required lang" key="allocatedtowhom"
                                for="allocatedtowhom">Allocated to
                                Whom</label>
                            <select class="form-select lang-dropdown" id="allocatedtowhom" name="allocatedtowhom">
                                <option value="" data-name-en="---Select User Type---"
                                    data-name-ta="---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---">Select User Type</option>
                                <option value="N" data-name-en="Team Member" data-name-ta="குழு உறுப்பினர்">Team
                                    Member</option>
                                <option value="Y" data-name-en="Team Head" data-name-ta="குழு தலைவர்">Team Head
                                </option>
                            </select>

                        </div>
                    </div>

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
</div>
<div class="col-12">
    <div class="card card_border">
        <div class="card-header card_header_color lang" key="work_obj_map_table">Work Allocation and Objection Mapping
            Details</div>
        <div class="card-body">
            <div class="datatables">
 <div id="datatable_loader" class="hide_this">
                    <div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;"></div>
                    <div class="mt-2">Loading data...</div>
                </div>
                <div class="table-responsive hide_this" id="tableshow">
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
                                for="allocatedtowhom" >Allocated to
                                Whom</label>
                            <select class="form-select lang-dropdown" id="allocatedtowhom_filt"
                                name="allocatedtowhom_filt" onchange="initializeDataTable()">
                                <option value="" data-name-en="---Select User Type---"
                                    data-name-ta="---பயனர் வகையைத் தேர்ந்தெடுக்கவும்---">Select User Type</option>
                                <option value="N" data-name-en="Team Member" data-name-ta="குழு உறுப்பினர்">Team
                                    Member</option>
                                <option value="Y" data-name-en="Team Head" data-name-ta="குழு தலைவர்" selected>
                                    Team Head
                                </option>
                            </select>

                        </div>
                    </div>
                    <table id="mappallocationobj_table"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang" key="department">Department</th>
                                <th class="lang" key="instcat_label">Category</th>
                                {{-- <th class="lang" key="">Subcategory</th> --}}
                                <th class="lang" key="allocatedtowhom">Allocated to Whom</th>
                                <th class="lang" key="callforrecords_label">Call For Rcords</th>
                                <th class="lang" key="workall_head">Main Work Allocation</th>
                                <th class="lang" key="subwork_head">Minor Work Allocation</th>

                                <th class="lang" key="main_obj_label">Objection</th>
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


    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    $(document).ready(function() {

        var lang = getLanguage('');
        // // $('#map_allocation_objection')[0].reset();
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        initializeDataTable(lang);


        // if (sessiondeptcode !== '' || sessiondeptcode !== null) {
        //     alert(sessiondeptcode);
        //     onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        // }

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        }

    });

    let dataFromServer;
    $('#translate').change(function() {
        lang = getLanguage('Y');
        updateTableLanguage(lang);
        // updateSelect2Language(lang); // Update Select2 dropdown
        // changeButtonText('action', 'buttonaction', 'reset_button', '', @json($savebtn),
        //     @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'map_allocation_objection');

    });


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#mappallocationobj_table')) {
            $('#mappallocationobj_table').DataTable().clear().destroy();
        }
        renderTable(language);
    }



    function initializeDataTable(lang) {
     lang = getLanguage(''); 
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
    // const subcategoryColumn = language === 'ta' ? 'subcattname' : 'subcatename';
        const subcategoryColumn = language === 'ta' ? 'subcategory_tname' : 'subcategory_ename';
        const membername = language === 'ta' ? 'குழு உறுப்பினர்' : 'Team Member';
        const leadername = language === 'ta' ? 'குழு தலைவர்' : 'Team Head';
        const groupColumn = language === 'ta' ? 'grouptname' : 'groupename';
        const callforrecColumn = language === 'ta' ? 'callforrecordstname' : 'callforrecordsename';
        const majorworkColumn = language === 'ta' ? 'majorworkallocationtypetname' : 'majorworkallocationtypeename';
        const subworkColumn = language === 'ta' ? 'subworkallocationtypetname' : 'subworkallocationtypeename';
        const mainobjColumn = language === 'ta' ? 'objectiontname' : 'objectionename';
        const subobjColumn = language === 'ta' ? 'subobjectiontname' : 'subobjectionename';

        // Debugging logs
        console.log("Language selected:", language);
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
$('#mappallocationobj_table').empty();
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
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: ' text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language] || "Department",
                    render: function(data, type, row) {
                        return row?.[departmentColumn] || '-';
                    },
                    className: ' text-start'
                },
                {
                    data: null,
                    title: columnLabels?.[categoryColumn]?.[language] || "Category Details",
                    render: function(data, type, row) {
                        return `<b>Category:</b> ${row?.[categoryColumn] || '-'} <br>
                            <b>Subcategory:</b> ${row?.[subcategoryColumn] || '-'} <br>
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
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
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
                        return `<b>Main Objection:</b> ${row?.[mainobjColumn] || '-'} <br>
                            <b>Sub Objection:</b> ${row?.[subobjColumn] || '-'}`;
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "encrypted_mapid",
                    title: columnLabels?.["actions"]?.[language] || "Actions",

                    render: function(data, type, row) {

                        if (row.isexists === 'Y') {
                            return `
                                <center>
                                    <a class="btn disabled text-danger">
                                        <i class="ti ti-lock fs-4"></i>
                                    </a>
                                </center>
                            `;
                        }

                        return `
                            <center>
                                <a class="btn editicon edit_btn"
                                id="${data}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },

                    className: "text-center"
                }
            ]
        });

        // Mobile column handling
        const mobileColumns = [
            categoryColumn, subcategoryColumn, groupColumn,
            "allocatetowhom", callforrecColumn, majorworkColumn,
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

        // Define headers
        let headers = [{
                header: "Department",
                key: columnMap.departmentColumn
            },
            {
                header: "Category",
                key: columnMap.categoryColumn
            },
            {
                header: "Subcategory",
                key: columnMap.subcategoryColumn
            },
            {
                header: "Group",
                key: columnMap.groupColumn
            },
            {
                header: "Allocated To",
                key: "allocatetowhom"
            },
            {
                header: "Call for Records",
                key: columnMap.callforrecColumn
            },
            {
                header: "Major Work Allocation",
                key: columnMap.majorworkColumn
            },
            {
                header: "Sub Work Allocation",
                key: columnMap.subworkColumn
            },
            {
                header: "Main Objection",
                key: columnMap.mainobjColumn
            },
            {
                header: "Sub Objection",
                key: columnMap.subobjColumn
            }
        ];

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
        const ws = XLSX.utils.json_to_sheet(excelData, {
            header: headers.map(h => h.key)
        });

        // Rename headers
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A1"
        });

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Data Export");

        // Save the file
        XLSX.writeFile(wb, `Data_Export_${language}.xlsx`);
    }


    // var lang = window.localStorage.getItem('lang');
    // change_lang_for_page(lang);

    // function exportCustomExcel(dt, lang) {

    //     const data = dt.rows().data().toArray();
    //     console.log("Exported Data:", data);

    //     // Define headers
    //     const headers = [
    //         "S.No",
    //         "Department",
    //         "Category ",
    //         "Sub Category",
    //         "Call For Records",
    //         "Group",
    //         "Allocated To",

    //         "Major Work Allocation",
    //         "Sub Work Allocation",
    //         "Main Objection ",
    //         "Sub Objection",

    //     ];


    //     const departmentColumn = (lang === 'en') ? 'deptesname' : 'depttsname';
    //     const categoryColumn = (lang === 'en') ? 'catename' : 'cattname';
    //     const subcategoryColumn = (lang === 'en') ? 'subcategory_ename' : 'subcategory_tname';
    //     const callforrecColumn = (lang === 'en') ? 'callforrecordsename' : 'callforrecordstname';
    //     const majorworkColumn = (lang === 'en') ? 'majorworkallocationtypeename' : 'majorworkallocationtypetname';
    //     const subworkColumn = (lang === 'en') ? 'subworkallocationtypeename' : 'subworkallocationtypetname';
    //     const mainobjColumn = (lang === 'en') ? 'objectionename' : 'objectiontname';
    //     const subobjColumn = (lang === 'en') ? 'subobjectionename' : 'subobjectiontname';
    //     const groupColumn = (lang === 'en') ? 'groupename' : 'grouptname';
    //     const membername = (lang === 'en') ? 'Team Member ' : 'குழு உறுப்பினர்';
    //     const leadername = (lang === 'en') ? 'Team Head' : 'குழு தலைவர்';



    //     // Convert data to an array format for Excel
    //     const exportData = data.map((row, index) => [

    //         index + 1, // Serial number
    //         row[departmentColumn] || '-',
    //         row[categoryColumn] || '-',
    //         row[subcategoryColumn] || '-',
    //         row[callforrecColumn] || '-',
    //         row[groupColumn] || '-',
    //         row['allocatetowhom'] === "N" ? membername : leadername,
    //         row[majorworkColumn] || '-',
    //         row[subworkColumn] || '-',
    //         row[mainobjColumn] || '-',
    //         row[subobjColumn] || '-',
    //     ]);

    //     // Create a worksheet
    //     const ws = XLSX.utils.aoa_to_sheet([headers, ...exportData]);

    //     // Auto-adjust column width
    //     ws['!cols'] = headers.map(() => ({
    //         wch: 20
    //     })); // Set width of 20 for all columns

    //     // Create a workbook
    //     const wb = XLSX.utils.book_new();
    //     XLSX.utils.book_append_sheet(wb, ws, "Work Allocation");

    //     // Save as Excel file
    //     XLSX.writeFile(wb, "Mapping Work_Allocation_Report.xlsx");
    // }


    /***********************************On Change**********************************************/
    let data = "";

    function onchange_deptcode(deptcode, catcode = '', callforrecordsid = '', majorworkallocationtypeid = '',
        mainobjectionid = '', groupid = '') {

        const catcodeDropdown = $('#cat_code');
        const callforrecordsDropdown = $('#callforrec');
        const workallocationDropdown = $('#maj_work');
        const subworkallocationDropdown = $('#subworkallocationtypeid');
        const mainobjectionDropdown = $('#mainobjectionid');
        const subobjectionDropdown = $('#subobjectionid');
        const groupDropdown = $('#groupid');

        $('#subcategory').val(null).trigger('change');
        $('#subcategory').append(
            '<option value="" data-name-en="---Select Subcategory---" data-name-ta="--- துணைப்பிரிவை தேர்ந்தெடுக்கவும் ---">---Select Subcategory---</option>'
        );

        const lang = getLanguage();


        callforrecordsDropdown.html(`<option value=""  data-name-en="---Select Call For Records---" data-name-ta="---பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">
              ${lang === 'ta' ? '---பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---' : '---Select Call For Records---'}
             </option>`);

        subworkallocationDropdown.html(`
                <option value="" data-name-en="---Select Sub Work Allocation---" data-name-ta="---சிறிய வேலை ஒதுக்கீடு வகை---">
                    ${lang === 'ta' ? '---சிறிய வேலை ஒதுக்கீடு வகை தேர்ந்தெடுக்கவும்---' : '---Select Sub Work Allocation---'}
                </option>

            `);
        workallocationDropdown.html(`
                <option value="" data-name-en="---Select Major Work Allocation---" data-name-ta="---முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---முக்கிய பணி ஒதுக்கீட்டைத் தேர்ந்தெடுக்கவும்---' : '---Select Major Work Allocation---'}
                </option>
            `);

        mainobjectionDropdown.html(`
                <option value="" data-name-en="---Select Main Objection---" data-name-ta="---முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---முக்கிய ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---' : '---Select Main Objection---'}
                </option>
            `);
        subobjectionDropdown.html(`
                <option value="" data-name-en="---Select Sub  Objection---" data-name-ta---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub  Objection---'}
                </option>
            `);



        // reset_auditor_datas();
        if (deptcode == '') {
            var deptcode = $('#deptcode').val();
            // $('.subcatdiv').hide(); // Hide the subcategory div when if_subcat is 'N'

        }


        if (!deptcode) {
            catcodeDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Category Available"
                            data-name-ta="வகை கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);

            callforrecordsDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Call For Records Available"
                            data-name-ta="பதிவுகளுக்கான அழைப்பு கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'பதிவுகளுக்கான அழைப்பு கிடைக்கவில்லை' : 'No Call For Records Available'}
                    </option>
                `);


            workallocationDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Work Allocation Available"
                            data-name-ta="பணி ஒதுக்கீடு கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'பணி ஒதுக்கீடு கிடைக்கவில்லை' : 'No Work Allocation Available'}
                    </option>
                `);

            // subworkallocationDropdown.append(`
            //     <option value="" disabled id=""
            //             data-name-en="No SubWork Allocation Available"
            //             data-name-ta="சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை">
            //             ${lang === 'ta' ? 'சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை' : 'No SubWork Allocation Available'}
            //     </option>
            // `);


            // mainobjectionDropdown.append(`
            //     <option value="" disabled id=""
            //             data-name-en="No SubWork Allocation Available"
            //             data-name-ta="முக்கிய எதிர்ப்பு கிடைக்கவில்லை">
            //             ${lang === 'ta' ? 'முக்கிய எதிர்ப்பு கிடைக்கவில்லை' : 'No SubWork Allocation Available'}
            //     </option>
            // `);

        }
        if (!deptcode || !majorworkallocationtypeid) {
            subworkallocationDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No SubWork Allocation Available"
                            data-name-ta="சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை">
                            ${lang === 'ta' ? 'சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை' : 'No SubWork Allocation Available'}
                    </option>
                `);

        }
        if (!deptcode || !mainobjectionid) {
            subobjectionDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Sub Objection Available"
                            data-name-ta="உபஎதிர்ப்பு கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'உபஎதிர்ப்பு கிடைக்கவில்லை' : 'No SubWork Allocation Available'}
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
                //    $('#callforrec').empty();
                //    $('#callforrec').append(
                //         '<option value=""  data-name-en="---Select Call For Records---" data-name-ta="--- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">---Select Call For Records---</option>',
                //         '<option value="" disabled id="" data-name-en="No Call For Records Available" data-name-ta="">No Call For Records Available</option>'
                //     );
                // $('#subworkallocationtypeid').empty();
                // $('#subworkallocationtypeid').append(
                //     '<option value=""  data-name-en="---Select Sub Work Allocation---" data-name-ta="--- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">---Select SubWork Allocation---</option>',
                //     '<option value="" disabled id="" data-name-en="No SubWork Allocation Available" data-name-ta="">No SubWork Allocation Available</option>'
                // );
                // $('#subobjectionid').empty();
                // $('#subobjectionid').append('<option value="">---Select Sub Objection---</option>');

                //////Main Objection Allocation data populate////////////
                data = response.category;
                $('#cat_code').empty();
                catcodeDropdown.html(
                    `<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}</option>`
                );

                //  $('#cat_code').append(
                //      '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
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

                //////Main Objection Allocation data populate////////////
                var mainobjdet = response.mainobj;
                // $('#mainobjectionid').empty();
                // $('#mainobjectionid').append(
                //     '<option value="" data-name-en="---Select Main Objection---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Main Objection---</option>'
                // );
                if (deptcode == "") {
                    $('#mainobjectionid').append(
                        '<option value="" disabled id="no-region-option" data-name-en="No Main Objection Available" data-name-ta="">No Main Objection Available</option>'
                    );

                }
                $.each(mainobjdet, function(index, mainobj) {
                    var isSelected = mainobj.mainobjectionid ===
                        mainobjectionid ? 'selected' : '';
                    $('#mainobjectionid').append(
                        '<option value="' + mainobj.mainobjectionid + '"' +
                        ' data-name-en="' + mainobj.objectionename + '"' +
                        ' data-name-ta="' + mainobj.objectiontname + '" ' +
                        isSelected + '>' +
                        (lang === "en" ? mainobj.objectionename : mainobj
                            .objectiontname) +
                        '</option>'
                    );
                });


                var callforrec = response.callforrec;
                // $('#callforrec').empty();
                // $('#callforrec').append(
                //     '<option value="" data-name-en="---Select Call For Reco---" data-name-ta="--- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">Select Call For cords</option>'
                // );


                $.each(callforrec, function(index, callforrec) {
                    var isSelected = callforrec.callforrecordsid === callforrecordsid ? 'selected' :
                        '';

                    $('#callforrec').append(
                        `<option value="${callforrec.callforrecordsid}"
                            data-name-en="${callforrec.callforrecordsename}"
                            data-name-ta="${callforrec.callforrecordstname}" ${isSelected}>
                            ${lang === "en" ? callforrec.callforrecordsename : callforrec.callforrecordstname}
                        </option>`
                    );
                });
                //////////////////////GROUP////////////////////
                var groupData = response.group;
                $('#groupid').empty();
                groupDropdown.html(`<option value=""  data-name-en="---Select Group---" data-name-ta="---குழுவைத் தேர்ந்தெடுக்கவும்---">
              ${lang === 'ta' ? '---குழுவைத் தேர்ந்தெடுக்கவும்---' : '---SelectGroup Name---'}
             </option>`);
                //  $('#cat_code').append(
                //      '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
                //  );
                $.each(groupData, function(index, group) {
                    var isSelected = group.groupid == groupid ? 'selected' : '';
                    // alert(isSelected);
                    $('#groupid').append(
                        '<option value="' + group.groupid + '"' +


                        ' data-name-en="' + group.groupename + '"' +
                        ' data-name-ta="' + group.grouptname + '" ' + isSelected + '>' +
                        (lang === "en" ? group.groupename : group.grouptname) +
                        '</option>'
                    );
                });
                ///////Major Work Allocation data populate////////////
                var majorworkdet = response.majorwork;
                // $('#maj_work').empty();
                // $('#maj_work').append(
                //     '<option value="" data-name-en="---Select Major WorkAllocation---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Major Work Allocation---</option>'
                // );
                $.each(majorworkdet, function(index, majorwork) {
                    var isSelected = majorwork.majorworkallocationtypeid ===
                        majorworkallocationtypeid ? 'selected' : '';
                    $('#maj_work').append(
                        '<option value="' + majorwork.majorworkallocationtypeid + '"' +
                        ' data-name-en="' + majorwork.majorworkallocationtypeename + '"' +
                        ' data-name-ta="' + majorwork.majorworkallocationtypetname + '" ' +
                        isSelected + '>' +
                        (lang === "en" ? majorwork.majorworkallocationtypeename : majorwork
                            .majorworkallocationtypetname) +
                        '</option>'
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function onchange_category(catcode, subcategory = '', if_subcat = '') {
        var catcode = catcode || $('#cat_code').val();
        var selectedOption = $('#cat_code').find(':selected'); // Get the selected category
        var if_subcat = if_subcat || selectedOption.attr('if_subcat'); // Retrieve the attribute properly



        const subcategoryDropdown = $('#subcategory');




        // subcategoryDropdown.html(
        //     `<option value="" data-name-en="---Select SubCategory---"data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">${lang === 'ta' ? '---உபவகை தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}</option>`
        // );


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


                // $('#callforrec').empty();
                // $('#callforrec').append(
                //     '<option value="" data-name-en="---Select Call For Records---" data-name-ta="--- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">Select Call For Records</option>'
                // );
                $('#subcategory').empty();


                if (if_subcat === 'Y') {
                    // $('.subcatdiv').show();
                    if (response.subcategory && response.subcategory.length > 0) {

                        $('#subcategory').append(
                            '<option value="" data-name-en="---Select Subcategory---" data-name-ta="--- துணைப்பிரிவை தேர்ந்தெடுக்கவும் ---">---Select Subcategory---</option>'
                        );
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
                            '<option value="" data-name-en="---Select Subcategory---" data-name-ta="--- துணைப்பிரிவை தேர்ந்தெடுக்கவும் ---">---Select Subcategory---</option>'
                        );
                    }
                } else {
                    // $('.subcatdiv').show();
                    $.each(data, function(i, subcat) {
                        if (subcat.catcode === catcode) {
                            $('#subcategory').append(
                                `<option value="" data-name-en="${subcat.catename}" data-name-ta="${subcat.cattname}" selected>
                                  ${lang === "en" ? subcat.catename : subcat.cattname}
                                 </option>`
                            );
                            return false;
                        }
                    });
                    // Hide the subcategory div when if_subcat is 'N'
                }

                // $.each(data.callforrec, function(index, callforrec) {
                //     var isSelected = callforrec.mapcallforrecordid === mapcallforrecordid ? 'selected' : '';

                //     $('#callforrec').append(
                //         `<option value="${callforrec.mapcallforrecordid}"
                //             data-name-en="${callforrec.callforrecordsename}"
                //             data-name-ta="${callforrec.callforrecordstname}" ${isSelected}>
                //             ${lang === "en" ? callforrec.callforrecordsename : callforrec.callforrecordstname}
                //         </option>`
                //     );
                // });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

    }


    function onchange_majorwork(majorworkallocationtypeid, subworkallocationtypeid = '') {
        var majorworkallocationtypeid = majorworkallocationtypeid || $('#maj_work').val();
        const subworkallocationDropdown = $('#subworkallocationtypeid');
        const lang = getLanguage();

        subworkallocationDropdown.html(`
                <option value="" data-name-en="---Select Sub Work Allocation---" data-name-ta="---சிறிய வேலை ஒதுக்கீடு வகை---">
                    ${lang === 'ta' ? '---சிறிய வேலை ஒதுக்கீடு வகை தேர்ந்தெடுக்கவும்---' : '---Select Sub Work Allocation---'}
                </option>

            `);
        //  maj_work =$('#maj_work').val();
        if (!majorworkallocationtypeid) {
            subworkallocationDropdown.append(`
                        <option value="" disabled id=""
                                data-name-en="No SubWork Allocation Available"
                                data-name-ta="சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை">
                                ${lang === 'ta' ? 'சிறிய வேலை ஒதுக்கீடு வகைகிடைக்கவில்லை' : 'No SubWork Allocation Available'}
                        </option>
                    `);

        }

        // var majorworkallocationtypeid = $('#maj_work').val();
        $.ajax({
            url: '/masters/FilterByDept',
            method: 'POST',

            data: {
                majorworkallocationtypeid: majorworkallocationtypeid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content')
            },
            success: function(response) {
                var subworkdata = response;
                // $('#subworkallocationtypeid').empty();
                // $('#subworkallocationtypeid').append(
                //     '<option value=""  data-name-en="---Select Sub Work Allocation---" data-name-ta="--- பதிவுகளுக்கான அழைப்பைத் தேர்ந்தெடுக்கவும்---">Select Sub Work Allocation</option>'
                // );
                $.each(subworkdata, function(index, subwork) {
                    var isSelected = subwork.subworkallocationtypeid === subworkallocationtypeid ?
                        'selected' : '';

                    const nameEn = subwork.subworkallocationtypeename || "No data available";
                    const nameTa = subwork.subworkallocationtypetname ||
                        "தரவு எதுவும் கிடைக்கவில்லை.";

                    // Determine which language to display
                    const displayName = lang === "en" ? nameEn : nameTa;


                    // Append the option to the select element
                    $('#subworkallocationtypeid').append(
                        '<option value="' + (subwork.subworkallocationtypeid ?? '') + '"' +
                        ' data-name-en="' + nameEn + '"' +
                        ' data-name-ta="' + nameTa + '"' + isSelected + '>' +
                        displayName +
                        '</option>'
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function onchange_mainObj(mainobjectionid, subobjectionid = '') {
        // reset_auditor_datas();
        var mainobjectionid = mainobjectionid || $('#mainobjectionid').val();
        const subobjectionDropdown = $('#subobjectionid');


        const lang = getLanguage();

        subobjectionDropdown.html(`
                <option value="" data-name-en="---Select Sub  Objection---" data-name-ta---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---">
                    ${lang === 'ta' ? '---துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்---' : '---Select Sub  Objection---'}
                </option>
            `);

        if (!mainobjectionid) {
            subobjectionDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No Sub Objection Available"
                            data-name-ta="உபஎதிர்ப்பு கிடைக்கவில்லை">
                            ${lang === 'ta' ? 'உபஎதிர்ப்பு கிடைக்கவில்லை' : 'No Sub Objection Available'}
                    </option>
                `);

        }
        // var mainobjectionid = $('#mainobjectionid').val();
        $.ajax({
            url: '/masters/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                mainobjectionid: mainobjectionid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var subobjdata = response;
                // $('#subobjectionid').empty();
                // $('#subobjectionid').append('<option value="">Select Sub Objection</option>');
                $.each(subobjdata, function(index, subobj) {
                    // var isSelected = category.regioncode === regioncode_session ? 'selected' : '';
                    const nameEn = subobj.subobjectionename || "No data available";
                    const nameTa = subobj.subobjectiontname || "தரவு எதுவும் கிடைக்கவில்லை.";

                    // Determine which language to display
                    const displayName = lang === "en" ? nameEn : nameTa;
                    var isSelected = subobj.subobjectionid === subobjectionid ? 'selected' : '';


                    // Append the option to the select element
                    $('#subobjectionid').append(
                        '<option value="' + subobj.subobjectionid + '"' +
                        ' data-name-en="' + nameEn + '"' +
                        ' data-name-ta="' + nameTa + '"' + isSelected + '>' +
                        displayName +
                        '</option>'
                    );
                    // $('#subobjectionid').append('<option value="' + subobj.subobjectionid + `"` +

                    //     ' data-name-en="' + subobj.subobjectionename + '"' +
                    //     ' data-name-ta="' + subobj.subobjectiontname + '">' +
                    //     (lang === "en" ? subobj.subobjectionename : subobj.subobjectiontname) +
                    //     '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    /***********************************On Change**********************************************/

    /***********************************jquery Validation**********************************************/
    const $work_obj_mapform = $("#map_allocation_objection");


    // Validation rules and messages
    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#map_allocation_objection").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                cat_code: {
                    required: true,
                },
               // subcategory: {
                  //  required: true,
               // },

                callforrec: {
                    required: true,
                },

                maj_work: {
                    required: true,
                },
                allocatedtowhom: {
                    required: true,
                },

                mainobjectionid: {
                    required: true,
                },
                groupid: {
                    required: true
                },
                subobjectionid: {
                    required: true,
                }

            },
            messages: errorMessages[language],
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },

            // messages: {
            //     deptcode: {
            //         required: "Select Department ",
            //     },
            //     cat_code: {
            //         required: "Select Category",
            //     },
            //     subcategory: {
            //         required: "Select SubCategory",
            //     },
            //     callforrec: {
            //         required: "Select Call For Records",
            //     },
            //     allocatedtowhom: {
            //         required: "Select Whom to Allocate ",
            //     },
            //     maj_work: {
            //         required: "Select  Major Work Allocation",
            //     },

            //     mainobjectionid: {
            //         required: "Select  Main Objection",
            //     },
            //     groupid: {
            //         required: "Select Group",
            //     },
            //     subobjectionid: {
            //         required: "Select Sub Objection",
            //     }
            //     // highlight: function(element, errorClass) {
            //     //     $(element).removeClass(errorClass); //prevent class to be added to selects
            //     // },

            // }


        });


        // reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
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
    /***********************************jquery Validation**********************************************/

    $(document).on('click', '#buttonaction', function(event) {

        event.preventDefault(); // Prevent form submission
        // Check if the error message is visible
        if ($('#display_error').is(':visible')) {
            return; // Exit the function to prevent form submission
        }


        if ($work_obj_mapform.valid()) {
            get_insertdata('insert');

        } else {
            scrollToFirstError();
        }
    });

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here
        // alert();

        var id = $(this).attr('id'); //Getting id of user clicked edit button.
        //  alert(id);

        if (id) {
            // reset_form();
            fetchmap_data(id);

        }
    });
    $('#reset_button').on('click', function() {
        // reset_form(); // Call the reset_form function
        reset_form();

    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // function reset_form() {
    //     $('#display_error').hide();
    //     change_button_as_insert('map_allocation_objection', 'action', 'buttonaction', 'display_error', '', '');
    //     updateSelectColorByValue(document.querySelectorAll(".form-select"));
    //     $("#map_allocation_objection").validate().resetForm(); // Reset the validation errors
    //     $("#map_allocation_objection")[0].reset();
    //     $('#cat_code , #callforrec ,#subworkallocationtypeid, #mainobjectionid,#subobjectionid').val('');
    // }

    function fetchmap_data(mapallocationobjectionid) {

        $.ajax({
            url: '/masters/fetchall_mapallocationObj', // Your API route to get user details
            method: 'POST',
            data: {
                mapallocationobjectionid: mapallocationobjectionid
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {
                    $('#display_error').hide();
                    // change_button_as_update('map_allocation_objection', 'action', 'buttonaction',
                    //     'display_error', '', '', 'update');
                    changeButtonAction('map_allocation_objection', 'action', 'buttonaction',
                        'reset_button',
                        'display_error', @json($updatebtn),
                        @json($clearbtn),
                        @json($update));

                    const detail = response.data[0];

                    // $('#cat_code').val(detail.catcode).trigger('change');

                    setTimeout(() => {
                        onchange_category(detail.catcode, detail.auditeeins_subcategoryid,
                            detail
                            .if_subcategory);
                    }, 500); // Adding delay to ensure dropdown is populated first

                    $('#allocatedtowhom').val(detail.allocatetowhom);
                    $('#mapallocationobjectionid').val(detail.encrypted_mapid);
                    $('#deptcode').val(detail.deptcode).trigger('change');

                    // Call other functions in the right order
                    onchange_deptcode(detail.deptcode, detail.catcode, detail.mapcallforrecordsid,
                        detail
                        .majorworkallocationtypeid, detail.mainobjectionid, detail.groupid);
                    onchange_majorwork(detail.majorworkallocationtypeid, detail
                        .subworkallocationtypeid);
                    onchange_mainObj(detail.mainobjectionid, detail.subobjectionid);

                } else {
                    alert('Schedule Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function get_insertdata(action) {


        var formData = $('#map_allocation_objection').serializeArray();


        if (action === 'finalise') {
            finaliseflag = 'F';
        } else if (action === 'insert') {
            finaliseflag = 'Y';
        }

        // Push the finaliseflag to the formData array
        formData.push({
            name: 'finaliseflag',
            value: finaliseflag
        });


        $.ajax({
            url: '/masters/insertorupdate_mapWorkObj', // For creating a new user or updating an existing one
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
                    //


                    // passing_alert_value('Confirmation', response.success,
                    //     'confirmation_alert', 'alert_header', 'alert_body',
                    //     'confirmation_alert');
                    //fetchAlldata();
                    // table.ajax.reload(); // Reload the table
                    initializeDataTable(window.localStorage.getItem('lang'));


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
    // let dataFromServer;

    // $(document).ready(function() {

    //     fetchAlldata(lang);
    //     // fetch_instituteData();
    // });


    // $(document).ready(function() {



    // });


    function reset_form() {
        let validator = $('#map_allocation_objection').validate();

        // Clear validation errors only when clearing
        validator.resetForm();
        $('.form-control, .form-select').removeClass('is-invalid');
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#cat_code').val(null).trigger('change');
            $('#callforrec').val(null).trigger('change');
            $('#maj_work').val(null).trigger('change');
            $('#allocatedtowhom').val(null).trigger('change');
            $('#mainobjectionid').val(null).trigger('change');
            $('#groupid').val(null).trigger('change');
        } else {
            $('#deptcode').val(null).trigger('change');
            $('#subcategory').empty();
            $('#subcategory').append(
                '<option value="" data-name-en="---Select Subcategory---" data-name-ta="--- துணைப்பிரிவை தேர்ந்தெடுக்கவும் ---">---Select Subcategory---</option>'
            );
        }
        // Reset form fields
        // $('#map_allocation_objection')[0].reset();

        // Reset Select2 fields properly without triggering validation
        // $('.select2').val(null).trigger('change.select2');

        // Ensure Select2 fields update visually
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        // Restore button state
        changeButtonAction('map_allocation_objection', 'action', 'buttonaction', 'reset_button',
            'display_error', @json($savebtn), @json($clearbtn),
            @json($insert));
        // change_button_as_insert('map_allocation_objection', 'action', 'buttonaction', 'display_error', '', '');
    }
</script>
@endsection
