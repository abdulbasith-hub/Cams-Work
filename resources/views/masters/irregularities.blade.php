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
                <div class="card-header card_header_color lang " key="irregularities_head">Irregularities</div>
                <div class="card-body">
                    <form id="irregularitiesform" name="irregularitiesform">
                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiesesname" for="irregularitiesesname">Irregularities Short Name in English</label>
                                <input type="text" class="form-control name" id="irregularitiesesname" maxlength='2'
                                    data-placeholder-key="irregularitiesesname" name="irregularitiesesname" required />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitieselname" for="irregularitieselname">Irregularities Long Name in English</label>
                                <input type="text" class="form-control name" id="irregularitieselname" maxlength='30'
                                    data-placeholder-key="irregularitieselname" name="irregularitieselname" required />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiestsname" for="irregularitiestsname">Irregularities Short Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiestsname" maxlength='5'
                                    data-placeholder-key="irregularitiestsname" name="irregularitiestsname" required />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiestlname" for="irregularitiestlname">Irregularities Long Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiestlname" maxlength='30'
                                    data-placeholder-key="irregularitiestlname" name="irregularitiestlname" required />
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
                                <input type="hidden" name="irregularitiesid" id="irregularitiesid" value="" />
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
                <div class="card-header card_header_color lang" key="irregularities_table">Irregulartties Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="irregularitiestable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <!-- <th class="lang align-middle text-center" key="department">Department</th> -->
                                        <th class="lang align-middle text-center" key="irregularitiesesname">Irregularities Short Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitieselname">Irregularities Long Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitiestsname">Irregularities Short Name in Tamil
                                           </th>
                                        <th class="lang align-middle text-center" key="irregularitiestlname">Irregularities Long Name in Tamil
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
            $('#irregularitiesform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            var lang = getLanguage();
            initializeDataTable(lang)

        });


        $('#translate').change(function() {
            var lang = getLanguage('Y');
            updateTableLanguage(lang);
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'irregularitiesform');
        });

        function initializeDataTable(language) {
            $.ajax({
                url: "{{ route('irregularities.irregularities_fetchData') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: "json",
                success: function(json) {
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
                    $('#no_data').show(); 
                }
            });
        }







        function renderTable(language) {
          //  const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

            if ($.fn.DataTable.isDataTable('#irregularitiestable')) {
                $('#irregularitiestable').DataTable().clear().destroy();
            }

            table = $('#irregularitiestable').DataTable({
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
                    // {
                    //     data: departmentColumn,
                    //     title: columnLabels?.[departmentColumn]?.[language],
                    //     render: function(data, type, row) {
                    //         return row[departmentColumn] || '-';
                    //     },
                    //     className: ' text-start text-wrap' // Removed col-1
                    // },
                    {
                        data: "irregularitiesesname",
                        title: columnLabels?.["irregularitiesesname"]?.[language],
                        className: " text-start text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiesesname || '-';
                        }
                    },
                    {
                        data: "irregularitieselname",
                        title: columnLabels?.["irregularitieselname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitieselname || '-';
                        }
                    },
                    {
                        data: "irregularitiestsname",
                        title: columnLabels?.["irregularitiestsname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiestsname || '-';
                        }
                    },
                    {
                        data: "irregularitiestlname",
                        title: columnLabels?.["irregularitiestlname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiestlname || '-';
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
                        data: "encrypted_irregularitiesid",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                    }
                ],

                "initComplete": function(settings, json) {
                    $("#irregularitiestable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            const mobileColumns = [ "irregularitieselname", "irregularitiestsname", "irregularitiestlname", "statusflag"];
            setupMobileRowToggle(mobileColumns);

            updatedatatable(language, "irregularitiestable"); 
        }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#irregularitiestable')) {
                $('#irregularitiestable').DataTable().clear().destroy();
            }
            renderTable(language);
        }






        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#irregularitiesform").validate({

                rules: {
                    // deptcode: {
                    //     required: true,
                    // },
                    irregularitiesesname: {
                        required: true
                    },
                    irregularitieselname: {
                        required: true
                    },
                    irregularitiestlname: {
                        required: true
                    },
                    irregularitiestsname: {
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
                if ($("#irregularitiesform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formData = $('#irregularitiesform').serializeArray();
                    // var deptcode = $('#deptcode').val();
                    // if ($('#deptcode').prop('disabled')) {

                    //     formData.push({
                    //         name: 'deptcode',
                    //         value: deptcode
                    //     });
                    // }
                    $.ajax({
                        url: "{{ route('irregularities.irregularities_insertupdate') }}",
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
                $('#irregularitiesid').val(id); // Set the ID field directly

                $.ajax({
                    url: "{{ route('irregularities.irregularities_fetchData') }}",
                    method: 'POST',
                    data: {
                        irregularitiesid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('irregularitiesform', 'action', 'buttonaction',
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
            change_button_as_update('irregularitiesform', 'action', 'buttonaction', 'display_error', '', '');
            $('#irregularitiesesname').val(charge.irregularitiesesname);
            $('#irregularitieselname').val(charge.irregularitieselname);
            $('#irregularitiestsname').val(charge.irregularitiestsname);
            $('#irregularitiestlname').val(charge.irregularitiestlname);
            $('#irregularitiesid').val(charge.encrypted_irregularitiesid);

            populateStatusFlag(charge.statusflag);
          //  $('#deptcode').val(charge.deptcode).trigger('change');
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
           
            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                $('#irregularitiesesname,#irregularitieselname', '#irregularitiestsname', '#irregularitiestlname').val();

            } else {
             //   $('#deptcode').val(null).trigger('change');

            }

            changeButtonAction('irregularitiesform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>

@endsection
