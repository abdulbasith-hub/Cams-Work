@section('content')
    @extends('index2')
    @include('common.alert')
    @php
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;
        $make_dept_disable = $deptcode ? 'disabled' : '';

    @endphp
    <!-- <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"> -->
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
		<div class="card-header card_header_color lang " key="">Non - Hub Designation</div>
                <div class="card-body">
                    <form id="hubdesignationform" name="hubdesignationform">
                        <input type="hidden" name="hubdesigid" id="hubdesigid">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="department" for="deptcode">Department</label>

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
                                <label class="form-label required lang" key="desigename" for="desigename">Designation Name in
                                    English</label>
                                <input type="text" class="form-control name removesplchar_text" id="desigename" maxlength='200'
                                    data-placeholder-key="desigename" name="desigename" required />
                            </div>

                    
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="desigtname" for="desigtname">Designation Name
                                    in  Tamil</label>
                                <input type="text" class="form-control name removesplchar_text" id="desigtname" maxlength='200'
                                    data-placeholder-key="desigtname" name="desigtname" required />
                            </div>


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
                        </div>
                        <div class="row">
                            <div class="col-md-3 mx-auto text-center">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <input type="hidden" name="desigcode" id="desigcode" value="" />
                                <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save</button>
                                <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                    onclick="reset_form()">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card_border">
                <div class="card-header card_header_color lang" key="">Non - Hub Designation Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="hubdesigtable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th class="lang align-middle text-center" key="department">Department</th>
                                        <th class="lang align-middle text-center" key="">Designation Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="">Designation Name in Tamil</th>
                                        <th class="lang align-middle text-center" key="statusflag">Status</th>
                                        <th class="all lang align-middle text-center" key="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='no_data' class='hide_this'>
                        <center class="lang" key="no_data">No Data Available</center>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Download Button Start -->

    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <!-- Download Button Start -->


    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

    <script>
        let table;
        let dataFromServer = [];

        var sessiondeptcode = ' <?php echo $deptcode; ?>';

        $(document).ready(function() {
            $('#hubdesignationform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            var lang = getLanguage();
            initializeDataTable(lang)

        });


        $('#translate').change(function() {
            var lang = getLanguage('Y');
            // change_lang_for_page(lang);
            updateTableLanguage(lang);
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'hubdesignationform');
        });

        function initializeDataTable(language) {
            $.ajax({
                url: "{{ route('hubdesignation.hubdesignation_fetchData') }}",
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

            if ($.fn.DataTable.isDataTable('#hubdesigtable')) {
                $('#hubdesigtable').DataTable().clear().destroy();
            }

            table = $('#hubdesigtable').DataTable({
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
                            return row[departmentColumn] || '-';
                        },
                        className: ' text-start text-wrap' // Removed col-1
                    },
                    {
                        data: "desigename",
                        title: columnLabels?.["desigename"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.desigename || '-';
                        }
                    },
                   
                    {
                        data: "desigtname",
                        title: columnLabels?.["desigtname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.desigtname || '-';
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
                        className: "text-center d-none d-md-table-cell extra-column  noExport"
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
                                <a class="btn editicon editchargedel"
                                id="${row.encrypted_hubdesigid}">
                                    <i class="ti ti-edit fs-4"></i>
                                </a>
                            </center>
                        `;
                    },

                    className: "text-center noExport"
                }	
                ],

                "initComplete": function(settings, json) {
                    $("#hubdesigtable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            const mobileColumns = ["desigename", "desigtname", "statusflag"];
            setupMobileRowToggle(mobileColumns);

            //    updatedatatable("en", "callforrecordstable", "Call for Records");
            updatedatatable(language, "hubdesigtable"); // Title: "Call for Records"
        }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#hubdesigtable')) {
                $('#hubdesigtable').DataTable().clear().destroy();
            }
            renderTable(language);
        }




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
                    if (!/^[a-zA-Z0-9\s\u0B80-\u0BFF]$/.test(char)) {
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
                        .replace(/[^a-zA-Z0-9\u0B80-\u0BFF]+/g, ' ')  
                        .replace(/\s+/g, ' ')                    
                        .trim();                                 
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



        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#hubdesignationform").validate({

                rules: {
                    deptcode: {
                        required: true,
                    },
                   
                    desigename: {
                        required: true
                    },
                    desigtname: {
                        required: true
                    },
                    desigtsname: {
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
                event.preventDefault();
                if ($("#hubdesignationform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formData = $('#hubdesignationform').serializeArray();
                  
                    $.ajax({
                        url: "{{ route('hubdesignation.hubdesignation_insertupdate') }}",
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
                                // table.ajax.reload();
                                initializeDataTable(window.localStorage.getItem('lang'));


                            } else if (response.error) {
                                console.log(response.error);
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



        // Handle Edit Button Click
        $(document).on('click', '.editchargedel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#hubdesigid').val(id); // Set the ID field directly

                $.ajax({
                    url: "{{ route('hubdesignation.hubdesignation_fetchData') }}",
                    method: 'POST',
                    data: {
                        hubdesigid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('hubdesignationform', 'action', 'buttonaction',
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




        function populateChargeForm(charge) {
            $('#display_error').hide();
            change_button_as_update('hubdesignationform', 'action', 'buttonaction', 'display_error', '', '');
            $('#desigename').val(charge.desigename);
            $('#desigtname').val(charge.desigtname);
            $('#hubdesigid').val(charge.encrypted_hubdesigid);
            populateStatusFlag(charge.statusflag);
            $('#deptcode').val(charge.deptcode).select2();
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
            // $('#hubdesignationform')[0].reset();
            // $('#hubdesignationform').validate().resetForm();
            // $('#deptcode').val(null).trigger('change');
            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                $('#desigename,#desigetname', '#hubdesigid').val();

            } else {
                $('#deptcode').val(null).trigger('change');

            }

            changeButtonAction('hubdesignationform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            // change_button_as_insert('hubdesignationform', 'action', 'buttonaction', 'display_error', '', '');
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>

@endsection