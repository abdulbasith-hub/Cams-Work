@section('content')
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
                <div class="card-header card_header_color lang " key="irregularitiescat_head">Irregularities - Main Category</div>
                <div class="card-body">
                    <form id="irregularitiescategoryform" name="irregularitiescategoryform">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="irregularitiescode" for="irregularitiescode">Irregularities</label>


                                <select class="form-select mr-sm-2 lang-dropdown select2" id="irregularitiescode" name="irregularitiescode">
<option value="" data-name-en="---Select Irregularities---"
                                        data-name-ta="---இயல்பு மீறல்கள்களைத் தேர்ந்தெடுக்கவும்---">---Select Irregularities---</option>

                                    @if (!empty($irr) && count($irr) > 0)
                                        @foreach ($irr as $irregularities)
                                            <option value="{{ $irregularities->irregularitiescode }}"
                                                data-name-en="{{ $irregularities->irregularitieselname }}"
                                                data-name-ta="{{ $irregularities->irregularitiestlname }}">
                                                {{ $irregularities->irregularitieselname }}
                                            </option>
                                        @endforeach
                                    @else
                                       <option disabled data-name-en="No Irregularities Available"
                                            data-name-ta="இயல்பு மீறல்கள் எதுவும் இல்லை">No Irregularities Available</option>                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiescatesname" for="irregularitiescatesname">Category Short Name in English</label>
                                <input type="text" class="form-control name" id="irregularitiescatesname" maxlength='5'
                                    data-placeholder-key="irregularitiescatesname" name="irregularitiescatesname" required />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiescatelname" for="irregularitiescatelname">Category Long Name in English</label>
                                <input type="text" class="form-control name" id="irregularitiescatelname" maxlength='100'
                                    data-placeholder-key="irregularitiescatelname" name="irregularitiescatelname" required />
                            </div>

                          


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiescattsname" for="irregularitiescattsname">Category Short Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiescattsname" maxlength='10'
                                    data-placeholder-key="irregularitiescattsname" name="irregularitiescattsname" required />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiescattlname" for="irregularitiescattlname">Category Long Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiescattlname" maxlength='100'
                                    data-placeholder-key="irregularitiescattlname" name="irregularitiescattlname" required />
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
                                <input type="hidden" name="irregularitiescatid" id="irregularitiescatid" value="" />
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
                <div class="card-header card_header_color lang" key="irregularitiescategory_table">Irregularities Category Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="irregularitiescategorytable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                         <th class="lang align-middle text-center" key="irregularitiescode">Irregularities</th> 
                                        <th class="lang align-middle text-center" key="irregularitiescatesname">Category Short Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitiescatelname">Category Long Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitiescattsname">Category Short Name in Tamil
                                           </th>
                                        <th class="lang align-middle text-center" key="irregularitiescattlname">Category Long Name in Tamil
                                            </th>
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
            $('#irregularitiescategoryform')[0].reset();
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
            updateValidationMessages(getLanguage('Y'), 'irregularitiescategoryform');
        });

        function initializeDataTable(language) {
            $.ajax({
                url: "{{ route('irregularitiescategory.irregularitiescategory_fetchData') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: "json",
                success: function(json) {
                   // console.log("Success Response:", json);
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
            const irregularitiesColumn = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';

            if ($.fn.DataTable.isDataTable('#irregularitiescategorytable')) {
                $('#irregularitiescategorytable').DataTable().clear().destroy();
            }

            table = $('#irregularitiescategorytable').DataTable({
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
                        data: irregularitiesColumn,
                        title: columnLabels?.[irregularitiesColumn]?.[language],
                        render: function(data, type, row) {
                            return row[irregularitiesColumn] || '-';
                        },
                        className: ' text-start text-wrap' // Removed col-1
                    },
                    {
                        data: "irregularitiescatesname",
                        title: columnLabels?.["irregularitiescatesname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiescatesname || '-';
                        }
                    },
                    {
                        data: "irregularitiescatelname",
                        title: columnLabels?.["irregularitiescatelname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiescatelname || '-';
                        }
                    },
                    {
                        data: "irregularitiescattsname",
                        title: columnLabels?.["irregularitiescattsname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiescattsname || '-';
                        }
                    },
                    {
                        data: "irregularitiescattlname",
                        title: columnLabels?.["irregularitiescattlname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiescattlname || '-';
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
                        data: "encrypted_irregularitiescatid",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                    }
                ],

                "initComplete": function(settings, json) {
                    $("#irregularitiescategorytable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            const mobileColumns = ["irregularitiescatesname", "irregularitiescatelname", "irregularitiescattsname", "irregularitiescattlname", "statusflag"];
            setupMobileRowToggle(mobileColumns);

            //    updatedatatable("en", "callforrecordstable", "Call for Records");
            updatedatatable(language, "irregularitiescategorytable"); // Title: "Call for Records"
        }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#irregularitiescategorytable')) {
                $('#irregularitiescategorytable').DataTable().clear().destroy();
            }
            renderTable(language);
        }






        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#irregularitiescategoryform").validate({

                rules: {
                    irregularitiescode: {
                        required: true,
                    },
                    irregularitiescatesname: {
                        required: true
                    },
                    irregularitiescatelname: {
                        required: true
                    },
                    irregularitiescattlname: {
                        required: true
                    },
                    irregularitiescattsname: {
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
                if ($("#irregularitiescategoryform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formData = $('#irregularitiescategoryform').serializeArray();
                    // var deptcode = $('#deptcode').val();
                    // if ($('#deptcode').prop('disabled')) {

                    //     formData.push({
                    //         name: 'deptcode',
                    //         value: deptcode
                    //     });
                    // }
                    $.ajax({
                        url: "{{ route('irregularitiescategory.irregularitiescategory_insertupdate') }}",
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
                $('#irregularitiescatid').val(id); // Set the ID field directly

                $.ajax({
                    url: "{{ route('irregularitiescategory.irregularitiescategory_fetchData') }}",
                    method: 'POST',
                    data: {
                        irregularitiescatid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('irregularitiescategoryform', 'action', 'buttonaction',
                                    'reset_button', 'display_error', @json($updatebtn),
                                    @json($clearbtn), @json($update))
                                populateChargeForm(response.data[0]); // Populate form with data
                            } else {
                                alert('irrugularities data is empty');
                            }
                        } else {
                            alert('irrugularities not found');
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
            change_button_as_update('irregularitiescategoryform', 'action', 'buttonaction', 'display_error', '', '');
            $('#irregularitiescatesname').val(charge.irregularitiescatesname);
            $('#irregularitiescatelname').val(charge.irregularitiescatelname);
            $('#irregularitiescattsname').val(charge.irregularitiescattsname);
            $('#irregularitiescattlname').val(charge.irregularitiescattlname);
            $('#irregularitiescatid').val(charge.encrypted_irregularitiescatid);
            populateStatusFlag(charge.statusflag);
            $('#irregularitiescode').val(charge.irregularitiescode).select2();
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
            // $('#irregularitiescategoryform')[0].reset();
            // $('#irregularitiescategoryform').validate().resetForm();
            // $('#deptcode').val(null).trigger('change');
            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                $('#irregularitiescatesname,#irregularitiescatelname', '#irregularitiescattsname', '#irregularitiescattlname').val();

            } else {
                $('#irregularitiescode').val(null).select2();

            }

            changeButtonAction('irregularitiescategoryform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            // change_button_as_insert('irregularitiescategoryform', 'action', 'buttonaction', 'display_error', '', '');
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>

@endsection
