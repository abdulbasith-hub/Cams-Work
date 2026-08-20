@section('content')

@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    //print_r($sessionchargedel);
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditeedept_head">Auditee Department</div>
            <div class="card-body">
                <form id="auditeedepartmentform" name="auditeedepartmentform">
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
                                        data-name-ta="துறைகள் எதுவும் இல்லை">No Departments Available</option>                                @endif
                            </select>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="auditeedeptename"
                                for="auditeedeptename">Department Name in English</label>
                            <input type="text" class="form-control text_special" id="auditeedeptename" name="auditeedeptename"
                                maxlength="200" required data-placeholder-key="auditeedeptename">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="auditeedepttname"
                                for="auditeedepttname">Department Name in Tamil</label>
                            <input type="text" class="form-control text_special" id="auditeedepttname" name="auditeedepttname"
                                maxlength="200" required
                                data-placeholder-key="auditeedepttname">
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

                  
                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="auditeedeptid" id="auditeedeptid" value="" />

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
            <div class="card-header card_header_color lang" key="auditeedepartment_table">Auditee Department Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditeedepartmenttable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="auditeedeptename">Department Name in English</th>
                                    <th class="lang align-middle text-center" key="auditeedepttname">Department Name in Tamil</th>
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
        $('#auditeedepartmentform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'auditeedepartmentform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('auditeedepartment.auditeedepartment_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
              //  console.log("Success Response:", json);
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
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
            }
        });
    }







    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#auditeedepartmenttable')) {
            $('#auditeedepartmenttable').DataTable().clear().destroy();
        }

        table = $('#auditeedepartmenttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-wrap text-end',
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
                    data: "auditeedeptename",
                    title: columnLabels?.["auditeedeptename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeedeptename || '-';
                    },

                },
                {
                    data: "auditeedepttname",
                    title: columnLabels?.["auditeedepttname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.auditeedepttname || '-';
                    },

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
				<a class="btn editicon editworkallocationdel"
				id="${row.encrypted_auditeedeptid}">
					<i class="ti ti-edit fs-4"></i>
				</a>
			</center>
		`;
	},

	className: "text-center noExport"
	}
            ],

            "initComplete": function(settings, json) {
                $("#auditeedepartmenttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["auditeedeptename", "auditeedepttname", "statusflag"];
        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "auditeedepartmenttable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditeedepartmenttable')) {
            $('#auditeedepartmenttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }






    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#auditeedepartmentform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                auditeedeptename: {
                    required: true
                },
                auditeedepttname: {
                    required: true
                },
                statusflag: {
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

            if ($("#auditeedepartmentform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#auditeedepartmentform').serializeArray();

                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
               
                $.ajax({
                    url: "{{ route('auditeedepartment.auditeedepartment_insertupdate') }}", 
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
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


    function auditeedepartmentform(auditeedept) {
        $('#display_error').hide();
        $('#auditeedeptename').val(auditeedept.auditeedeptename);
        $('#auditeedepttname').val(auditeedept.auditeedepttname);
        $('#auditeedeptid').val(auditeedept.encrypted_auditeedeptid);
        populateStatusFlag(auditeedept.statusflag);
        $('#deptcode').val(auditeedept.deptcode).trigger('change');

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editworkallocationdel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        if (id) {
            reset_form();
            $('#auditeedeptid').val(id);
           
            $.ajax({
                url: "{{ route('auditeedepartment.auditeedepartment_fetchData') }}",
                method: 'POST',
                data: {
                    auditeedeptid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('auditeedepartmentform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            auditeedepartmentform(response.data[0]); 
                        } else {
                            alert('Auditee department data is empty');
                        }
                    } else {
                        alert('Auditee department not found');
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

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {
            $('#auditeedeptename, #auditeedepttname').val();

        } else {
            $('#deptcode').val(null).select2();
        }
        changeButtonAction('auditeedepartmentform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
