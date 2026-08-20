@section('content')

@section('title', 'Group Report')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="group_head">Group Form</div>
            <div class="card-body">
                <form id="groupform" name="groupform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?>>
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


                        <!-- <div class="col-md-4 mb-3">
                                                        <label class="form-label required lang" key="category" for="category">Category</label>
                                                        <select class="form-select mr-sm-2" id="category" name="category">
                                                            <option value=''>Select category</option>

                                                        </select>
                                                    </div> -->

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang " key="groupename" for="groupename">Group English
                                Name</label>
                            <input type="text" class="form-control alpha_numeric" id="groupename" name="groupename"
                                maxlength="255" placeholder="Enter Group English Name" data-placeholder-key="groupename"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang alpha_numeric" key="grouptname"
                                for="grouptname">Group Tamil
                                Name</label>
                            <input type="text" class="form-control alpha_numeric" id="grouptname" name="grouptname"
                                maxlength="255" placeholder="Enter Group Tamil Name" data-placeholder-key="grouptname"
                                required>
                        </div>

                        <div class="col-md-4 mb-1">
                            <label class="form-label required lang" key="allocatedtowhom"
                                for="allocatedtowhom">Allocated to
                                Whom</label>
                            <select class="form-select lang-dropdown" id="allocatedtowhom" name="allocatedtowhom">
                                <option value="" data-name-en="---Select Allocated To Whom---"
                                    data-name-ta="---யாருக்கு ஒதுக்கப்பட்டது என்பதைத் தேர்ந்தெடுக்கவும்.---">Select Allocated To Whom</option>
                                <option value="Y" data-name-en="Team Head" data-name-ta="குழு தலைவர்">Team Head
                                </option>

                                <option value="N" data-name-en="Team Member" data-name-ta="குழு உறுப்பினர்">Team
                                    Member</option>

                            </select>

                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="status" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="status" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                                                            <div class="col-md-4  mx-auto">
                                                                <button class="btn btn-success mt-3" type="submit"> Submit </button>
                                                                <button class="btn btn-danger mt-3" type="submit"> Cancel </button>
                                                            </div>
                                                        </div> -->
                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="groupid" id="groupid" value="" />

                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="group_table">Group Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="grouptable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="groupename">Group English Name</th>
                                    <th class="lang align-middle text-center" key="grouptname">Group Tamil Name</th>
                                    <th class="lang align-middle text-center" key="allocatedtowhom">Allocated to Whom
                                    </th>

                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="all lang align-middle text-center" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class='hide_this lang text-center' key="no_data">
                    <center class="lang" key="no_data">No Data Available</center>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- Include jQuery and Bootstrap -->


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
{{-- select2 --}}
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<!-- Download Button End -->

<script>
    let table;
    let dataFromServer = [];
    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {

        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage('');
        initializeDataTable(lang);






    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('group.group_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
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
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#grouptable')) {
            $('#grouptable').DataTable().clear().destroy();
        }

        table = $('#grouptable').DataTable({
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
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: "groupename",
                    title: columnLabels?.["groupename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.groupename || '-';
                    }
                },
                {
                    data: "grouptname",
                    title: columnLabels?.["grouptname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.grouptname || '-';
                    }
                },
                {
                    data: "allocatedtowhom",
                    title: columnLabels?.["allocatedtowhom"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (row.allocatedtowhom === 'Y') {
                            return 'Team Head';
                        } else if (row.allocatedtowhom === 'N') {
                            return 'Team Member';
                        } else {
                            return '-'; // Handle unexpected values explicitly
                        }
                    }
                },
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
                    className: "text-center d-none d-md-table-cell extra-column noExport"
                },
                {
    data: null,
    title: columnLabels?.["actions"]?.[language],

    render: (row) => {

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
                <a class="btn editicon editgroupdel"
                   id="${row.encrypted_groupid}">
                    <i class="ti ti-edit fs-4"></i>
                </a>
            </center>
        `;
    },

    className: "text-center noExport"
}
            ],
            "initComplete": function(settings, json) {
                $("#grouptable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["groupename", "grouptname", "statusflag"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "grouptable");
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#grouptable')) {
            $('#grouptable').DataTable().clear().destroy();
        }
        renderTable(language);
    }





    $("#translate").change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'groupform');

    });



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#groupform").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                // category: {
                //     required: true
                // },
                groupename: {
                    required: true
                },
                grouptname: {
                    required: true
                },
                allocatedtowhom: {
                    required: true

                },
                status: {
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
            // messages: {
            //     deptcode: {
            //         required: "Select a department",
            //     },
            //     // category: {
            //     //     required: "Select a category",
            //     // },
            //     groupename: {
            //         required: "Enter group english name",
            //     },
            //     grouptname: {
            //         required: "Enter group tamil name",
            //     },
            //     status: {
            //         required: "Select a status",
            //     },
            // }
        });
        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#groupform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#groupform').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
		if ($('#action').val() === 'update') {
                    formData.push({
                        name: 'status',
                        value: $('input[name="status"]:checked').val()
                    });
                }


                $.ajax({
                    url: "{{ route('group.group_insertupdate') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });
                            //  table.ajax.reload();
                            initializeDataTable(window.localStorage.getItem('lang'));


                        } else if (response.error) {
                            // Handle errors if needed
                            // console.log(response.error);
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
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });



    function groupForm(group) {
        $('#display_error').hide();
        // change_button_as_update('groupform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainobjection.catcode);
        $('#groupename').val(group.groupename);
        $('#grouptname').val(group.grouptname);
        $('#groupid').val(group.encrypted_groupid);
        $('#allocatedtowhom').val(group.allocatedtowhom);
        //alert(id);
        populateStatusFlag(group.statusflag);
        $('#deptcode').val(group.deptcode).trigger('change');

        // $('#deptcode').val(group.deptcode);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editgroupdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        // alert(id);
        if (id) {
            reset_form();
            $('#group').val(id);
            //  console.log($('#workallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('group.group_fetchData') }}",
                method: 'POST',
                data: {
                    groupid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('groupform', 'action', 'buttonaction',
                                'display_error', '', @json($updatebtn),
                                @json($clearbtn), @json($update));

                            groupForm(response.data[0]); // Populate form with data
                        } else {
                            alert('workallocation data is empty');
                        }
                    } else {
                        alert('workallocation not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


    //function populateStatusFlag(statusflag) {
    //    if (statusflag === "Y") {
    //        document.getElementById('statusYes').checked = true;
    //    } else if (statusflag === "N") {
    //        document.getElementById('statusNo').checked = true;
    //    }
   // }



    function populateStatusFlag(statusflag) {
    const statusYes = document.getElementById('statusYes');
    const statusNo = document.getElementById('statusNo');

    // Check the current flag
    if (statusflag === "Y") {
        statusYes.checked = true;
    } else if (statusflag === "N") {
        statusNo.checked = true;
    }

    statusNo.disabled = true;
}

    function reset_form() {
        document.getElementById('statusNo').disabled = false;
        $('#groupform').validate().resetForm();
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#groupename,#grouptname,#groupid').val();
        } else {
            $('#deptcode').val(null).trigger('change');

        }
        // $('#deptcode').val(null).trigger('change');
        changeButtonAction('groupform', 'action', 'buttonaction', 'display_error', '', @json($savebtn),
            @json($clearbtn), @json($insert));
        // getCategoriesBasedOnDept('', selectedCatcode = null);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
