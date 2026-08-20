@section('content')
@extends('index2')
@include('common.alert')
<style>
    .card_seperator {
        height: 10px;
        border: 0;
        box-shadow: 0 10px 10px -10px #8c8b8b inset;
    }

    .card-title {
        font-size: 15px;
    }

    .title-part-padding {
        background-color: #e3efff;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .dataTables_info {
        margin-bottom: 1rem !important;
    }

    #schedule_allocatedwork_wrapper {
        width: 100%;
        /* Allow full-width table */
        overflow-x: auto;
        /* Enable horizontal scrolling */
    }

    .dataTables_wrapper {
        overflow: hidden !important;
        /* Prevents extra scrolling */
        height: auto !important;
        /* Allows full display */
    }

    table.dataTable {
        width: 100% !important;
        table-layout: auto !important;
    }
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<div class="col-12">
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-6 mb-1 mt-2">
                <label class="form-label lang " key="inst" for="validationDefault01">Institution </label>
                <select class="form-select mr-sm-2 lang-dropdown " id="inst"
                    name="inst" onchange="fetchworkdata()">
                    <option value="" data-scheduleid="" data-name-en="---Select Institution---"
                        data-name-ta="---Select Institution ---">---Select Institution---
                    </option>
                    @foreach ($instdet as $instdata)
                    <option value="{{ $instdata->encrypted_instid }}"
                        data-scheduleid="{{ $instdata->encrypt_auditscheduleid }}"
                        data-name-en="{{ $instdata->instename }}"
                        data-name-ta="{{ $instdata->insttname }}">
                        {{ $instdata->instename }}
                    </option>
                    @endforeach

                </select>
            </div>
        </div>
    </div>
</div>
<div class="card mt-6 hide_this" style="border-color: #7198b9" id="workallocTable">
    <div class="card-header lang card_header_color" key="workallocation_dt">Work Allocation Details</div>
    <div class="card-body">
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="workallocationtable"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang" key="s_no">S.No</th>


                            <th class="lang" key="group_name">Group</th>
                            <th class="lang" key="workallocation_title">Work Allocation</th>

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
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/js/vendor.min.js"></script>


<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>


<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script> -->


<!-- <script src="../assets/js/datatable/datatable-advanced.init.js"></script> -->


<script>
    let dataFromServer = '';

    function fetchworkdata() {

        var instid = $('#inst').val();
        var selectedOption = $('#inst').find(':selected'); // Get the selected category
        var scheduleid = selectedOption.attr('data-scheduleid')
                const language = getLanguage('');
        $.ajax({
            url: "/workalloc/fetchworkdata", // Your API route for fetching data
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            data: {
                instid: instid,
                scheduleid: scheduleid
            },
            success: function(json) {

                if (json.data && json.data.length > 0) {
                    //console.log(json.data);
                    $('#workallocTable').show();
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#workallocTable').hide();
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }

            },
            error: function(xhr, status, error) {

                $('#workallocTable').hide();
                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {
                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.message

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');


                }
            }



        });
    }

       function renderTable(language) {
        const groupcolumn = language === 'ta' ? 'grouptname' : 'groupename';
        const workallocationcolumn = language === 'ta' ? 'majorworkallocationtypetname' : 'majorworkallocationtypeename';

        // ? Group data using the current language-specific fields
        const groupedData = Object.values(dataFromServer.reduce((acc, row) => {
            const groupKey = row[groupcolumn];

            if (!acc[groupKey]) {
                acc[groupKey] = {
                    [groupcolumn]: groupKey,
                    workNames: new Set()
                };
            }

            acc[groupKey].workNames.add(row[workallocationcolumn]);

            return acc;
        }, {})).map(group => ({
            [groupcolumn]: group[groupcolumn],
            workNames: Array.from(group.workNames)
        }));

        if ($.fn.DataTable.isDataTable('#workallocationtable')) {
            $('#workallocationtable').DataTable().clear().destroy();
        }

        $('#workallocationtable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: groupedData,
            columns: [{


                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: groupcolumn,
                    title: columnLabels?.[groupcolumn]?.[language] ?? 'Group',
                    render: function(data) {
                        return data || '-';
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                },
                {
                    data: 'workNames',
                    title: columnLabels?.[workallocationcolumn]?.[language] ?? 'Work Allocation',
                    render: function(data) {
                        return data.map(work => `<span>${work}</span>`).join('<br>');
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                }
            ],
            initComplete: function() {
                $("#workallocationtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });
        const mobileColumns = [groupcolumn, "workNames"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "workallocationtable");
    }    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#workallocationtable')) {
            $('#workallocationtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }
    $('#translate').change(function() {
        var language = getLanguage('Y');

        updateTableLanguage(language);

    });
</script>

@endsection