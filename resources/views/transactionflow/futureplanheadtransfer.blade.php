@section('content')
@section('title', 'PlanHeadChangerequest')

@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header card_header_color lang" key="futureplanchange">Update Head For Upcoming Schedule
            </div>
            <div class="card-body">
                <form id="futureplanheadtransfer" name="futureplanheadtransfer">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01" key="userid">Select
                                User
                            </label>
                            <select class="form-select mySelect lang-dropdown select2" id="userid" name="userid"
                                onchange="selecteduserplandel('plan')">

                                <option value='' data-name-en="Select user" data-name-ta="பயனரை தேர்வு செய்யவும்">
                                    Select user
                                </option>

                                @foreach ($userleavedetails as $d)
                                    <option value="{{ $d->deptuserid }}">
                                        {{ $d->username }} ({{ $d->desigesname }})
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01" key="schdulename">Select
                                Plan </label>
                            <select class="form-select mySelect lang-dropdown select2" id="auditplanid"
                                name="auditplanid" onchange="selecteduserplandel('plandel')">

                                <option value='' data-name-en="Select Schedule"
                                    data-name-ta="அட்டவணையை தேர்வு செய்யவும்">
                                    Select user
                                </option>

                            </select>

                        </div>


                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01" key="oldmandys">Mandays
                            </label>
                            <input type="text" class="form-control only_numbers" id="mandays" name="mandays"
                                maxlength="11" disabled />
                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label  lang " for="validationDefault01" key="teamsize">Team size
                            </label>
                            <input type="text" class="form-control only_numbers" id="teamsize" name="teamsize"
                                maxlength="11" disabled />
                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01" key="">Select
                            </label>
                            <select class="form-select form-control-sm datatransfercode" name="datatransfercode"
                                id="datatransfercode" onchange="gettransfertouser()">
                                <option value="">---Select---</option>
                                <option value="CD">Assign Task</option>
                                <option value="MH">Member Act as Head</option>
                                <option value="AH">Another Team head</option>
                            </select>
                        </div>


                        <div class="col-md-4  " id="reservelistuserdiv">
                            <label class="form-label required lang " for="validationDefault01" key ="userid">Select
                                User </label>
                            <select class="form-select mySelect lang-dropdown select2" id="reserveuserid"
                                name="reserveuserid">

                                <option value='' data-name-en="Select user" data-name-ta="பயனரை தேர்வு செய்யவும்">
                                    Select user
                                </option>

                                @foreach ($reverselistusers as $transuser)
                                    <option value="{{ $transuser->deptuserid }}">
                                        {{ $transuser->username . ' (' . $transuser->desigesname . ') (' . $transuser->distename . ')' }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-4 hide_this " id="otheruserdiv">
                            <label class="form-label required lang " for="validationDefault01" key ="userid">Select
                                User </label>
                            <select class="form-select mySelect lang-dropdown select2" id="touserid" name="touserid">

                                <option value='' data-name-en="Select user"
                                    data-name-ta="அட்டவணையை தேர்வு செய்யவும்">
                                    Select To User
                                </option>


                            </select>

                        </div>




                    </div>

                    <div class="row">


                        <div class="col-md-4 ">
                            <label class="form-label required lang " for="validationDefault01" key="remarks">Remarks

                            </label>
                            <textarea id="remarks" class="form-control alpha_numeric" name="remarks" maxlength="200"></textarea>

                        </div>
                    </div>
                    <div class="row mt-3 text-center">
                        <div class="col-md-6 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button type="button" name="buttonaction" action="insert" id="buttonaction"
                                class="btn button_save">Submit</button>
                            <button type="button" class="btn btn-danger" id="reset_button" onclick="resetForm()">
                                clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class=" card ">
            <div class=" card-header card_header_color lang" key="list_futureplanchange">List of Updated Head For
                Upcoming Schedule
                Requested Detail
            </div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this " id="tableshow">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang text-center align-middle" key="s_no">S.No</th>
                                    <th class="lang text-center align-middle" key="instname">Insitution Name</th>
                                    <th class="lang text-center align-middle" key="fromuser">From User</th>
                                    <th class="lang text-center align-middle" key="touser">To User</th>
                                    <th class="lang text-center align-middle" key="datatransfer">Data Transfer To</th>
                                    <th class="lang text-center align-middle" key="remarkslabel">Remarks</th>
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

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>




<script>
    function selecteduserplandel(value) {

        const userid = $('#userid').val();
        let auditplanid = '';

        const $dropdown = $("#auditplanid");
        const defaultOption = "<option value=''>Select</option>";

        if (value === 'plan') {
            $dropdown.html(defaultOption);
        } else if (value === 'plandel') {
            auditplanid = $dropdown.val();
        }


        // Proceed only if conditions are valid
        if ((value === 'plan' && userid) || (value === 'plandel' && userid && auditplanid)) {

            $.ajax({
                url: '/getplandetails',
                type: 'POST',
                data: {
                    userid: userid,
                    auditplanid: auditplanid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {

                        if (value === 'plandel') {
                            const data = response.data[0] || {};
                            $('#mandays').val(data.mandays || '');
                            $('#teamsize').val(data.teamsize || '');
                        } else if (value === 'plan') {
                            let options = defaultOption;
                            response.data.forEach(function(item) {
                                if (item.auditplanid && item.instename) {
                                    options +=
                                        `<option value="${item.auditplanid}">${item.instename}</option>`;
                                }
                            });
                            $dropdown.html(options);
                        }
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    let errorMessage = "An error occurred while fetching plan details.";
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value(
                        'Alert',
                        errorMessage,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            });

        } else {
            // Reset dropdown if user or audit plan ID is missing
            if (value === 'plan') {
                $dropdown.html(defaultOption);
            }
        }
    }

    function gettransfertouser() {
        datatransfertypecode = $('#datatransfercode').val();

        if (datatransfertypecode) {
            if (datatransfertypecode == 'CD') {
                $('#otheruserdiv').hide();
                $('#reservelistuserdiv').show();

            } else {
                const $dropdown = $("#touserid");
                const defaultOption = "<option value=''>Select</option>";

                $('#otheruserdiv').show();
                $('#reservelistuserdiv').hide();

                $.ajax({
                    url: '/fetch_assignusers',
                    type: 'POST',
                    data: {
                        userid: $('#userid').val(),
                        datatransfertypecode: datatransfertypecode,
                        auditplanid: $('#auditplanid').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            console.log("AJAX Success Response:", response);


                            let options = defaultOption;
                            response.data.forEach(function(item) {
                                if (item.userid && item.username) {
                                    options +=
                                        `<option value="${item.userid}">${item.username} (${item.desigesname})</option>`;
                                }
                            });
                            $dropdown.html(options);

                        } else {
                            console.error("Invalid response or data format:", response);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "An error occurred while fetching plan details.";
                        if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                errorMessage = response.message || errorMessage;
                            } catch (e) {
                                console.error("Error parsing error response:", e);
                            }
                        }

                        passing_alert_value(
                            'Alert',
                            errorMessage,
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                });




            }

        }


    }

    $("#buttonaction").on("click", function(event) {
        event.preventDefault();

        if ($("#futureplanheadtransfer").valid()) {

            // dataHTML = 'Are you sure to Change the head ?';

            // $('#process_button').off('click').on('click', function(event) {
            //     event.preventDefault();
            //     $('#confirmation_alert').modal('hide');
            //     udpatetheplanhead();
            // });

            // // Show confirmation modal with HTML version
            // passing_alert_value('Confirmation', dataHTML, 'confirmation_alert',
            //     'alert_header', 'alert_body', 'forward_alert');


            // dataHTML = 'Are you sure to give extra <b>' +
            //     extramandays +
            //     '</b> manday(s) with new proposed Exitmeeting date on <b></b>?';


            dataHTML =
                '<table style="width:100;%" class="table table-hover w-100 table-bordered display largemodal"><tbody><tr><td><b>Insitution Name</b></td><td>' +
                $("#auditplanid option:selected").text() + '</td></tr><tr><td><b>Old Mandays</b></td><td>' +
                $('#mandays').val() + '</td></tr><tr><td><b>Team size</b></td><td>' +
                $('#teamsize').val() + '</td></tr><tr><td><b>From User</b></td><td>' +
                $("#userid option:selected").text() + '</td><tr><td><b>Data Transfer</b></td><td>' + $(
                    "#datatransfercode option:selected").text() +
                '</td></tr></tr><tr><td><b>To User</b></td><td>';
            if ($('#datatransfercode').val() == 'CD')
                tousername = $("#reserveuserid option:selected").text();
            else
                tousername = $("#touserid option:selected").text();

            dataHTML = dataHTML + tousername +
                '</td></tr></tbody></table><b> Are you sure you want to change the user for the plan details? This action cannot be revoked.</b>';


            $('#large_modal_process_button').off('click').on('click', function(event) {
                event.preventDefault();
                $('#large_confirmation_alert').modal('hide');
                udpatetheplanhead();
            });

            // Show confirmation modal with HTML version
            // passing_alert_value('Confirmation', dataHTML, 'confirmation_alert',
            //     'alert_header', 'alert_body', 'forward_alert');


            passing_large_alert('Confirmation', dataHTML, 'large_confirmation_alert',
                'large_alert_header',
                'large_alert_body', 'forward_alert');
            $("#large_modal_process_button").html("Ok");
            $("#large_modal_process_button").addClass("button_finalize");
            $('#large_modal_process_button').removeAttr('data-bs-dismiss');


        }


    });

    $.validator.addMethod("alphanumericWithSpaces", function(value, element) {
        return this.optional(element) || /^[\u0B80-\u0BFFa-zA-Z0-9 ]+$/.test(value);
    }, "Only Tamil, English letters, numbers, and spaces are allowed.");


    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        $("#futureplanheadtransfer").validate({

            rules: {
                userid: {
                    required: true
                },
                auditplanid: {
                    required: true
                },
                datatransfercode: {
                    required: true
                },
                reserveuserid: {
                    required: true
                },
                touserid: {
                    required: true
                },
                remarks: {
                    required: true,
                    alphanumericWithSpaces: true,
                    minlength: 10
                },

            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.closest(".input-group").length) {
                    // If the element is inside an input-group, place the error after the entire group
                    error.insertAfter(element.closest(".input-group"));
                } else {
                    // Otherwise, insert after the element itself
                    error.insertAfter(element);
                }
            },
            messages: errorMessages[language], // Set initial messages
        });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    function udpatetheplanhead() {

        var formData = $('#futureplanheadtransfer').serializeArray();
        formData = formData.filter(field => field.name !== 'email');

        formData.push({
            name: 'teamsize',
            value: $('#teamsize').val()
        });

        formData.push({
            name: 'mandays',
            value: $('#mandays').val()
        });

        $.ajax({
            url: '/futureplanheadtransfer_finalise',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#buttonaction').prop('disabled', true); // Disable before request
            },
            success: function(response) {

                if (response.status == 'success') {

                    $('#close_button').hide();

                    passing_alert_value('Confirmation', response.message, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');

                    $('#ok_button').off('click').on('click', function(event) {
                        event.preventDefault();
                        // If validation passes, manually close the modal
                        $('#confirmation_alert').modal('hide');
                    });

                    resetForm();

                    initializeDataTable(window.localStorage.getItem('lang'));

                } else if (response.error) {
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');

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
                        passing_alert_value('Confirmation',
                            alertMessage, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                }
            },
            complete: function() {
                $('#buttonaction').prop('disabled', false); // Re-enable after complete
            }
        });
    }
    initializeDataTable(window.localStorage.getItem('lang'));

    function initializeDataTable(language) {
        $.ajax({
            url: "/futureplanheadtransferdel",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
            }
        });
    }


    function renderTable(language) {
        const instnameKey = language === 'ta' ? 'insttname' : 'instename';

        if ($.fn.DataTable.isDataTable('#usertable')) {
            $('#usertable').DataTable().clear().destroy();
        }

        const table = $('#usertable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `
                        <div>
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>
                            ${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: instnameKey,
                    title: columnLabels?.[instnameKey]?.[language] || 'Institution',
                    render: function(data, type, row) {
                        const instName = row[instnameKey] ?? '-';
                        const oldMandays = row['mandays'] ?? '-';
                        const teamSize = row['teamsize'] ?? '-';
                        return `
                        ${instName}<br>
                        Mandays - ${oldMandays}<br>
                        Team Size - ${teamSize}
                    `;
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: "from_username",
                    title: columnLabels?.["from_username"]?.[language] || 'From User',
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        const name = row.from_username ?? '-';
                        const designation = row.from_designation ?? '-';
                        return `${name} <small>(${designation})</small>`;
                    }
                },
                {
                    data: "to_username",
                    title: columnLabels?.["to_username"]?.[language] || 'To User',
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        const name = row.to_username ?? '-';
                        const designation = row.to_designation ?? '-';
                        return `${name} <small>(${designation})</small>`;
                    }
                },
                {
                    data: "datatransfertypecode",
                    title: columnLabels?.["datatransfertypecode"]?.[language] || 'Transfer Type',
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        let label = '';

                        switch ((row.datatransfertypecode || '').trim().toUpperCase()) {
                            case 'AH':
                                label = 'Parallel Team Head';
                                break;
                            case 'CD':
                                label = 'Reservelist User';
                                break;
                            default:
                                label = 'Member Act as Head';
                                break;
                        }




                        return `${label}`;
                    }
                },
                {
                    data: "remarks",
                    title: columnLabels?.["remarks"]?.[language] || 'Remarks',
                    className: "d-none d-md-table-cell lang text-wrap",
                    render: function(data, type, row) {
                        return row.remarks ?? '-';
                    }
                }
            ]
        });

        const mobileColumns = [
            instnameKey, 'transactiontypelname', 'processelname',
            "oldmandays", "remarks"
        ];

        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "usertable");
    }



    function resetForm() {
        $("#futureplanheadtransfer").validate().resetForm(); // Reset the validation errors
        $("#futureplanheadtransfer")[0].reset();

        const defaultOption = "<option value=''>Select </option>";
        const $dropdown = $("#auditplanid");
        $dropdown.html(defaultOption);


        const defaultOption1 = "<option value=''>To User </option>";
        const $dropdown1 = $("#touserid");
        $dropdown1.html(defaultOption1);


    }
</script>


@endsection
