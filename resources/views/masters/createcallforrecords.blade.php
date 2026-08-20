
@section('content')
@section('title', 'Callfor Records')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $distcode = $sessionchargedel->distcode;
    $roleTypeCode = $sessionchargedel->roletypecode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

    $dga_roletypecode = $DGA_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;

@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

<head>

    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
</head>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key='cfr_head'>Create Call Record</div>
            <div class="card-body">
                <form id="callforrecordsform" name="callforrecordsform">
                    <input type="hidden" name="callforrecordsid" id="callforrecordsid">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="deptcode">Department</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="">
                                <option value="" data-name-en=" -- Select Department --"
                                    data-name-ta="-- துறையைத் தேர்ந்தெடு --">-- Select Department --</option>
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
                        {{-- <div class="col-md-4 mb-3 " id="deptdiv">
                            <label class="form-label required lang " key="cat_name" for="catcode">Category Name</label>
                            <select class="form-select mr-sm-2 select2" id="catcode" name="catcode">
                                <option value="">-- Select Category --</option>
                            </select>
                        </div> --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="callforrecordsename"
                                for="callforrecordsename">Call
                                For Recordsename</label>
                            <input type="text" class="form-control text_special" id="callforrecordsename"
                                name="callforrecordsename" maxlength="200" placeholder="Call For Recordsename" required
                                data-placeholder-key="callforrecordsename" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="callforrecordstname"
                                for="callforrecordstname">Call
                                For Recordstname</label>
                            <input type="text" class="form-control text_special" id="callforrecordstname"
                                name="callforrecordstname" maxlength="200" placeholder="Call For Recordstname" required
                                data-placeholder-key="callforrecordstname" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="statusYes">Active
                                Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusYes" name="statusflag"
                                        value="Y" checked required />
                                    <label class="form-check-label lang" key='yes' for="statusYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="statusNo" name="statusflag"
                                        value="N" required />
                                    <label class="form-check-label lang" for="statusNo" key='no'>No</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" type="submit" id="buttonaction"
                                name="buttonaction" key='savebtn'>Save Draft</button>
                            <button type="button" class="btn btn-danger mt-3 lang" id="reset_button" key='clearbtn'
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key='cfr_table'>Call Records Details</div>
            <div class="card-body">
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="callforrecordstable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <div class="text-center mt-3" style="">

                            </div>
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang" key="department">Department</th>
                                    {{-- <th class="lang" key="cat_name">Category</th> --}}
                                    <th class="lang" key="callforrecordsename">Call For Records English name</th>
                                    <th class="lang" key="callforrecordstname">Call For Record Tamil Name</th>
                                    <th class="lang" key="statusflag">Status</th>
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
    <script src="../assets/js/forms/select2.init.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        var sessiondeptcode = ' <?php echo $deptcode; ?>';
        $(document).ready(function() {
            $('#callforrecordsform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            var lang = getLanguage();
            initializeDataTable(lang);

            $('#translate').change(function() {
                const lang = $('#translate').val();
                changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                    @json($updatebtn), @json($clearbtn));
                updateTableLanguage(lang);
                updateValidationMessages(getLanguage('Y'), 'callforrecordsform');
            });
        });

        function initializeDataTable(language) {
            $.ajax({
                url: "{{ route('callfor.callforrecords_fetchData') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: "json",
                success: function(json) {
                    console.log("Success Response:", json);
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

            if ($.fn.DataTable.isDataTable('#callforrecordstable')) {
                $('#callforrecordstable').DataTable().clear().destroy();
            }

            var table = $('#callforrecordstable').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                data: dataFromServer,
                order: [
                    [1, 'asc']
                ],
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
                        className: 'text-wrap text-start' // Removed col-1
                    },
                    {
                        data: "callforrecordsename",
                        title: columnLabels?.["callforrecordsename"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.callforrecordsename || '-';
                        }
                    },
                    {
                        data: "callforrecordstname",
                        title: columnLabels?.["callforrecordstname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.callforrecordstname || '-';
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
                        className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
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
                   id="${row.encrypted_callforrecordsid}">
                    <i class="ti ti-edit fs-4"></i>
                </a>
            </center>
        `;
    },

    className: "text-center noExport text-wrap"
}
                ],


                initComplete: function() {
                    $("#callforrecordstable").wrap("<div class='table-responsive'></div>");
                }
            });

            const mobileColumns = ["callforrecordsename", "callforrecordstname", "statusflag"];
            setupMobileRowToggle(mobileColumns);

            //    updatedatatable("en", "callforrecordstable", "Call for Records");
            updatedatatable(language, "callforrecordstable"); // Title: "Call for Records"
        }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#callforrecordstable')) {
                $('#callforrecordstable').DataTable().clear().destroy();
            }
            renderTable(language);
        }

        $(document).ready(function() {
            let currentLang = getLanguage();
            updateSelect2Language(currentLang);
        });

        function updateSelect2Language(lang) {
            $('.select2 option').each(function() {
                var text = (lang === "en") ? $(this).attr('data-name-en') : $(this).attr('data-name-ta');
                $(this).text(text);
            });
            $('.select2').select2();
        }

        function changeLanguage(lang) {
            window.localStorage.setItem('lang', lang);
            updateSelect2Language(lang);
        }



        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#callforrecordsform").validate({

                rules: {
                    deptcode: {
                        required: true
                    },
                    // catcode: {
                    //     required: true
                    // },
                    callforrecordsename: {
                        required: true
                    },
                    callforrecordstname: {
                        required: true
                    },
                    statusflag: {
                        required: true
                    }
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
                //         required: "Select a department"
                //     },
                //     // catcode: {
                //     //     required: "Select a category"
                //     // },
                //     callforrecordsename: {
                //         required: "Enter CallforRecord ename"
                //     },
                //     callforrecordstname: {
                //         required: "Enter CallforRecord tname"
                //     },
                //     statusflag: {
                //         required: "Select a Status action"
                //     }
                // }
            });

            $("#buttonaction").on("click", function(event) {
                event.preventDefault();
                if ($("#callforrecordsform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formData = $('#callforrecordsform').serializeArray();
                    var deptcode = $('#deptcode').val();
                    if ($('#deptcode').prop('disabled')) {

                        formData.push({
                            name: 'deptcode',
                            value: deptcode
                        });
                    }
 		if ($('#action').val() === 'update') {
                    formData.push({
                        name: 'statusflag',
                        value: $('input[name="statusflag"]:checked').val()
                    });
                }
                    $.ajax({
                        url: "{{ route('callfor.callforrecords_insertupdate') }}",
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
                                //console.log(response.error);
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
                } else {}
            });

            reset_form();

        }).catch(error => {
            console.error("Failed to load JSON data:", error);
        });



        $(document).on('click', '.editchargedel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#callforrecordsid').val(id);

                $.ajax({
                    url: "{{ route('callfor.callforrecords_fetchData') }}",
                    method: 'POST',
                    data: {
                        callforrecordsid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('callforrecordsform', 'action', 'buttonaction',
                                    'reset_button', 'display_error', @json($updatebtn),
                                    @json($clearbtn), @json($update))
                                populatecallforForm(response.data[0]); // Populate form with data
                            } else {
                                alert('callforrecords data is empty');
                            }
                        } else {
                            alert('callforrecords not found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText || 'Unknown error');
                    }
                });
            }
        });

        function populatecallforForm(charge) {
            $('#display_error').hide();
            //change_button_as_update('callforrecordsform', 'action', 'buttonaction', 'display_error', '', '');
            $('#callforrecordsename').val(charge.callforrecordsename);
            $('#callforrecordstname').val(charge.callforrecordstname);
            $('#callforrecordsid').val(charge.encrypted_callforrecordsid);
            $('#deptcode').val(charge.deptcode).trigger('change');
            populateStatusFlag(charge.statusflag);
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
            // getCategoriesBasedOnDept(charge.deptcode, charge.catcode);
        }

        //function populateStatusFlag(statusflag) {
        //    if (statusflag === "Y") {
          //      document.getElementById('statusYes').checked = true;
          //  } else if (statusflag === "N") {
           //    document.getElementById('statusNo').checked = true;
           // }
        //}

 function populateStatusFlag(statusflag) {
            const statusYes = document.getElementById('statusYes');
            const statusNo = document.getElementById('statusNo');

            if (statusflag === "Y") {
                statusYes.checked = true;
            } else if (statusflag === "N") {
                statusNo.checked = true;
            }

            statusNo.disabled = true;
        }

        function reset_form() {
            document.getElementById('statusNo').disabled = false;

            // $('#callforrecordsform')[0].reset(); // Reset the form
            // $('#callforrecordsform').validate().resetForm(); // Reset validation messages
            // $('.select2').val(null).trigger('change.select2');
            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                $('#callforrecordsename,#callforrecordsename,#callforrecordsid').val();

            } else {
                $('#deptcode').val(null).trigger('change');

            }
            changeButtonAction('callforrecordsform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            // change_button_as_insert('callforrecordsform', 'action', 'buttonaction', 'display_error', '', '');
            $('#deptcod, #callforrecordsename, #callforrecordstname').val(null).trigger('change');
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }

        // function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
        //     const catcodeDropdown = $('#catcode');
        //     catcodeDropdown.html('<option value="">Select Category Name</option>');
        //     if (deptcode) {
        //         $.ajax({
        //             url: "{{ route('get.category') }}",
        //             type: "POST",
        //             data: {
        //                 deptcode: deptcode,
        //                 _token: '{{ csrf_token() }}'
        //             },
        //             success: function(response) {
        //                 if (response.length > 0) {
        //                     response.forEach(category => {
        //                         catcodeDropdown.append(
        //                             `<option value="${category.catcode}" ${
    //                         category.catcode === selectedCatcode ? 'selected' : ''
    //                     }>${category.catename}</option>`
        //                         );
        //                     });
        //                 } else {
        //                     catcodeDropdown.append('<option disabled>No Categories Available</option>');
        //                 }
        //             },
        //             error: function() {
        //                 alert('Error fetching categories. Please try again.');
        //             }
        //         });
        //     }
        // }
    </script>
@endsection