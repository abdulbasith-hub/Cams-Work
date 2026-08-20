@section('content')

@section('title', 'Role Type Mapping Report')
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
            <div class="card-header card_header_color lang" key="roletype_head">Role Type Mapping</div>
            <div class="card-body">
                <form id="roletypemappingform" name="roletypemappingform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

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



                        <div class="col-md-4 mb-3">
                            <label class="form-label lang required" key="roletype" for="roletypecode">Role Type</label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="roletypecode" name="roletypecode">
                                <option value="" data-name-en="Select Role Type"
                                    data-name-ta="பாத்திர வகை தேர்வு செய்">Select Role Type</option>
                                @if (!empty($roletype) && count($roletype) > 0)
                                    @foreach ($roletype as $role)
                                        <option value="{{ $role->roletypecode }}"
                                            data-name-en="{{ $role->roletypeelname }}"
                                            data-name-ta="{{ $role->roletypetlname }}">
                                            {{ $role->roletypeelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Role Type Available</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="reportto" for="reportto">Report To</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="reportto" name="reportto"
                                <?php echo $make_dept_disable; ?>>
                                <option value="" data-name-en="---Select Report To---"
                                    data-name-ta="---அறிக்கை பெறுபவரை தேர்வு செய்யலாம்---">---Select Report To---</option>

                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled data-name-en="No Report To Available"
                                        data-name-ta="துறைகள் எதுவும் இல்லை">No Report Available</option>
                                @endif
                            </select>
                        </div>


                       

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="levelid" for="levelid">Level</label>
                            <input type="text" class="form-control only_numbers" id="levelid" maxlength="3"   data-placeholder-key="levelid" name="levelid"
                                 required />
                     </div>

                     <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="statusflag" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="statusflag" id="statusNo"
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

                            <input type="hidden" name="roletypemappingid" id="roletypemappingid" value="" />

                            <input type="hidden" name="roletypemappingcode" id="roletypemappingcode" value="" />


                            <button class="btn button_save mt-3 lang" key="savebtn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clearbtn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="roletype_table">Role Type Mapping Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="roletypemappingtable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="roletype">Role Type</th>
                                    <th class="lang align-middle text-center" key="reportto">Report To</th>
                                    <th class="lang align-middle text-center" key="levelid">levelid</th>
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
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- Download Button End -->

<script>
    let table;
    let dataFromServer = [];

     var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#roletypemappingform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'roletypemappingform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('roletypemapping.roletypemapping_fetchData') }}",
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
        const roletypeColumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        const reporttoColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#roletypemappingtable')) {
            $('#roletypemappingtable').DataTable().clear().destroy();
        }

        table = $('#roletypemappingtable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [
        {
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
            render: function (data, type, row) {
                return row[departmentColumn] || '-';
            },
            className: 'text-wrap text-start' // Removed col-1
        },
        {
            data: roletypeColumn,
            title: columnLabels?.[roletypeColumn]?.[language],
            render: function (data, type, row) {
                return row[roletypeColumn] || '-';
            },
            className: 'text-wrap text-start' // Removed col-1
        },
        {
            data: reporttoColumn,
            title: columnLabels?.[reporttoColumn]?.[language],
            render: function (data, type, row) {
                return row[reporttoColumn] || '-';
            },
            className: 'text-wrap text-start' // Removed col-1
        },
        {
            data: "levelid",
            title: columnLabels?.["levelid"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.levelid || '-';
            }
        },
        {
            data: "statusflag",
            title: columnLabels?.["statusflag"]?.[language],
            render: function(data) {
                let activeText = arrLang?.[language]?.["active"] || "Active";
                let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                return data === 'Y'
                    ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                    : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            },
            className: "text-center d-none d-md-table-cell extra-column noExport"
        },
        {
            data: "encrypted_roletypemappingid",
            title: columnLabels?.["actions"]?.[language],
            render: (data) =>
                `<center><a class="btn editicon editroletypemapdel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            className: "text-center noExport"
        }
    ],
           
            "initComplete": function(settings, json) {
                $("#roletypemappingtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            
        });

        const mobileColumns = ["majorworkallocationtypeename", "majorworkallocationtypetname", "statusflag"];
    setupMobileRowToggle(mobileColumns);

//    updatedatatable("en", "callforrecordstable", "Call for Records");
updatedatatable(language, "roletypemappingtable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#roletypemappingtable')) {
            $('#roletypemappingtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#roletypemappingform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                // category: {
                //     required: true
                // },
                roletypecode: {
                    required: true
                },
                reportto: {
                    required: true
                },
                levelid : {
                    required: true
                },
                statusflag: {
                    required: true
                },

            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },
          
        });
        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#roletypemappingform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#roletypemappingform').serializeArray();

                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                // formData.push({
                //     name: "lang",
                //     value: getLanguage('N')
                // });
                console.log(formData);
                $.ajax({
                    url: "{{ route('roletypemapping.roletypemapping_insertupdate') }}", // URL where the form data will be posted
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
                    if(response.error == 401)
                    {
                        handleUnauthorizedError(); 
                    }
                    else
                    {
                        getLabels_jsonlayout([{ id: response.message, key: response.message }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] || "Error Occured";
                            passing_alert_value('Confirmation', alertMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
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


    function roletypemappingform(roletypemap) {
        $('#display_error').hide();
       
        $('#roletypecode').val(roletypemap.roletypecode).trigger('change');
        $('#reportto').val(roletypemap.parentcode).trigger('change');
        $('#levelid').val(roletypemap.levelid);
        // $('#deptcode').val(workallocation.deptcode);
        $('#roletypemappingid').val(roletypemap.encrypted_roletypemappingid);
        //alert(id);
        populateStatusFlag(roletypemap.statusflag);
        $('#deptcode').val(roletypemap.deptcode).trigger('change');
        
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editroletypemapdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        //alert(id);
        if (id) {
            reset_form();
            $('#roletypemappingid').val(id);
            //  console.log($('#workallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('roletypemapping.roletypemapping_fetchData') }}",
                method: 'POST',
                data: {
                    roletypemappingid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('roletypemappingform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            roletypemappingform(response.data[0]); // Populate form with data
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


    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        // $('#roletypemappingform')[0].reset();
        // $('#roletypemappingform').validate().resetForm();
        // $('#deptcode').val(null).trigger('change');ā
       
        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#ename, #fname').val();

        } else {
            $('#deptcode').val(null).trigger('change');
        }
       

        $('#roletypecode').val(null).trigger('change');
        $('#reportto').val(null).trigger('change');

        changeButtonAction('roletypemappingform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert))
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
