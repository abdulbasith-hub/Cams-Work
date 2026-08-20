@section('content')
@extends('index2')
@include('common.alert')
@php
$sessionchargedel = session('charge');
$deptcode = $sessionchargedel->deptcode;
// $make_dept_disable = $deptcode ? 'disabled' : '';
$make_deptdiv_show = $deptcode ? '' : 'hide_this';
@endphp
<!-- <link rel="stylesheet" href="{{asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}"> -->
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="district_head">Create District</div>
            <div class="card-body">
                <form id="districtform" name="districtform">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="distsname" for="distsname">District short name</label>
                            <input type="text" class="form-control name" id="distsname" maxlength="5" data-placeholder-key="distsname" name="distsname"
                                required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="distename" for="distename">District English name</label>
                            <input type="text" class="form-control name" id="distename" maxlength="50" data-placeholder-key="distename" name="distename"
                               required />
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="disttname" for="disttname">District Tamil name</label>
                            <input type="text" class="form-control name" id="disttname" maxlength="50"   data-placeholder-key="disttname" name="disttname"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="distcode" for="distcode">District Code</label>
                            <input type="text" class="form-control only_numbers" id="distcode"  data-placeholder-key="distcode" name="distcode"
                               maxlength="3" required />
                        </div>

                        <!-- <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <select id="state" name="state" class="form-control" aria-describedby="state_help">
                                <option value="">Select a state</option>
                                @foreach ($state as $states)
                                    <option value="{{ $states->statecode }}">{{ $states->stateename }}</option>
                                @endforeach
                            </select>
                        </div> -->

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="statecode" for="statecode">State</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown" id="statecode" name="statecode">
                                <option value="" data-name-en="---Select a State---"
                                    data-name-ta="---மாநிலத்தைத் தேர்ந்தெடுக்கவும்---">---Select a State---</option>

                                @if (!empty($state) && count($state) > 0)
                                      @foreach ($state as $states)
                                    <option value="{{ $states->statecode }}">{{ $states->stateename }}</option>
                                    @endforeach
                                @else
                                    <option disabled data-name-en="No State Available"
                                        data-name-ta="மாநிலம் கிடைக்கவில்லை">No State Available</option>
                                @endif
                            </select>
                        </div>




                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="status">Active Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input
                                        type="radio"
                                        class="form-check-input"
                                        id="statusYes"
                                        name="statusflag"
                                        value="Y" checked
                                        required />
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        type="radio"
                                        class="form-check-input"
                                        id="statusNo"
                                        name="statusflag"
                                        value="N"
                                        required />
                                    <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="distid" id="distid" value="" />
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
            <div class="card-header card_header_color lang" key="district_table">District Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="districttable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="distsname">District short name</th>
                                    <th class="lang align-middle text-center" key="distename">District English name</th>
                                    <th class="lang align-middle text-center" key="disttname">District Tamil name</th>
                                    <th class="lang align-middle text-center" key="distcode">District</th>
                                    <th class="lang align-middle text-center" key="statecode">State</th>
                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="all lang align-middle text-center" key="action">Action</th>
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
</div>
<!-- Download Button Start -->

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

<!-- <script src="{{asset('assets/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script> -->
<script>

    let table;
    let dataFromServer = [];

     var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#districtform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'districtform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('district.district_fetchData') }}",
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
    if ($.fn.DataTable.isDataTable('#districttable')) {
        $('#districttable').DataTable().clear().destroy();
    }

    table = $('#districttable').DataTable({
        "processing": true,
        "serverSide": false,
        "lengthChange": false,
        "data": dataFromServer,
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                            </div>`;
                },
                className: 'text-end',
                type: "num"
            },
            {
                data: "distsname",
                title: columnLabels?.["distsname"]?.[language], // Corrected key usage
                className: 'text-wrap text-start'
            },
            {
                data: "distename",
                title: columnLabels?.["distename"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function (data, type, row) {
                    return row.distename || '-';
                }
            },
            {
                data: "disttname",
                title: columnLabels?.["disttname"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function (data, type, row) {
                    return row.disttname || '-';
                }
            },
            {
                data: "distcode",
                title: columnLabels?.["distcode"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function (data, type, row) {
                    return row.distcode || '-';
                }
            },
            {
                data: "statecode",
                title: columnLabels?.["statecode"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function (data, type, row) {
                    return row.statecode === "33" ? "TN" : row.statecode || '-'; // Fixed reference
                }
            },
            {
                data: "statusflag",
                title: columnLabels?.["statusflag"]?.[language],
                render: function (data) {
                    let activeText = arrLang?.[language]?.["active"] || "Active";
                    let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                    return data === 'Y'
                        ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                        : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                },
                className: "text-center d-none d-md-table-cell extra-column noExport"
            },
            {
                data: "encrypted_distid",
                title: columnLabels?.["actions"]?.[language],
                render: (data) =>
                    `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                className: "text-center noExport"
            }
        ],
        "initComplete": function (settings, json) {
            $("#districttable").wrap(
                "<div style='overflow:auto; width:100%;position:relative;'></div>");
        }
    });

    const mobileColumns = ["distename", "disttname", "distcode", "statecode"];
    setupMobileRowToggle(mobileColumns);

    updatedatatable(language, "districttable");
}

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#districttable')) {
            $('#districttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }






    jsonLoadedPromise.then(() => {
    const language = window.localStorage.getItem('lang') || 'en';
    var validator = $("#districtform").validate({

        rules: {
            distsname: {
                required: true,
            },
            distename: {
                required: true
            },
            disttname: {
                required: true
            },
            distcode: {
                required: true
            },
            statecode: {
                required: true
            },
            statusflag: {
                required: true
            },
        },
        // errorPlacement: function(error, element) {
        //         if (element.hasClass('select2')) {
        //             // Insert the error message below the select2 dropdown container
        //             error.insertAfter(element.next('.select2-container'));
        //         } else {
        //             // For other fields, insert the error message after the element itself
        //             error.insertAfter(element);
        //         }
        //     },
        // messages: {
        //     deptcode: {
        //         required: "Select a department",
        //     },
        //     desigesname: {
        //         required: "Enter a English Designation short name",
        //     },
        //     desigelname: {
        //         required: "Enter a English Designation long name",
        //     },
        //     desigtsname: {
        //         required: "Enter a Tamil Designation short name",
        //     },
        //     desigtlname: {
        //         required: "Enter a Tamil Designation long name",
        //     },
        //     statusflag: {
        //         required: "Select a Status action",
        //     },
        // }
    });
    $("#buttonaction").on("click", function(event) {
        event.preventDefault();
        if ($("#districtform").valid()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = $('#districtform').serializeArray();
            $.ajax({
                url: "{{ route('district.district_insertupdate') }}",
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


    // var table = $('#districttable').DataTable({
    //     processing: true,
    //     serverSide: false,
    //     lengthChange: false,
    //     ajax: {
    //         url: "{{ route('designation.designation_fetchData') }}",
    //         type: "POST", // Change to GET
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         dataSrc: function(json) {
    //             if (json.data && json.data.length > 0) {
    //                 $('#tableshow').show();
    //                 $('#usertable_wrapper').show();
    //                 $('#no_data').hide(); // Hide custom "No Data" message
    //                 return json.data;
    //             } else {
    //                 $('#tableshow').hide();
    //                 $('#usertable_wrapper').hide();
    //                 $('#no_data').show(); // Show custom "No Data" message
    //                 return [];
    //             }
    //         },
    //     },
    //     columns: [{
    //             data: null,
    //             render: (_, __, ___, meta) => meta.row + 1, // Serial number column
    //             className: 'text-end' // Align to the right
    //         },
    //         {
    //             data: "deptesname"
    //         },
    //         {
    //             data: "desigesname"
    //         },
    //         {
    //             data: "desigelname"
    //         },
    //         {
    //             data: "desigtsname"
    //         },
    //         {
    //             data: "desigtlname"
    //         },
    //         {
    //             data: "statusflag"
    //         },
    //         {
    //             data: "encrypted_desigid",
    //             render: (data) =>
    //                 `<center>
    //                 <a class="btn editicon editchargedel" id="${data}">
    //                     <i class="ti ti-edit fs-4"></i>
    //                 </a>
    //             </center>`
    //         }
    //     ]
    // });

    // Handle Edit Button Click
    $(document).on('click', '.editchargedel', function() {
        const id = $(this).attr('id');
        //alert(id);
        if (id) {
            reset_form();
            $('#distid').val(id); // Set the ID field directly

            $.ajax({
                url: "{{ route('district.district_fetchData') }}",
                method: 'POST',
                data: {
                    distid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('districtform', 'action', 'buttonaction',
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




    function populateChargeForm(district) {
        $('#display_error').hide();
        change_button_as_update('districtform', 'action', 'buttonaction', 'display_error', '', '');
        $('#distsname').val(district.distsname);
        $('#distename').val(district.distename);
        $('#disttname').val(district.disttname);
        $('#distcode').val(district.distcode);
        $('#statecode').val(district.statecode);
        $('#distid').val(district.encrypted_distid);
        populateStatusFlag(district.statusflag);
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        $('#districtform')[0].reset();
        $('#districtform').validate().resetForm();
       // $('#deptcode').val(null).trigger('change');

        changeButtonAction('districtform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert))
        // change_button_as_insert('districtform', 'action', 'buttonaction', 'display_error', '', '');
        //updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>

@endsection
