@section('content')
@section('title', 'Para Status')
@extends('index2') @include('common.alert')


@php

    $sessioncharge = session('charge');
    $deptcode = $sessioncharge->deptcode;

@endphp
<style>
    .userCountTable thead tr th,
    .instTable thead tr th {
        background-color: #707070 !important;
        /* Darker shade for header */
        color: #fff !important;
        /* Ensure text remains white */
        border: 1px solid #5e5c5c !important;
    }

    .userCountTable tbody tr td,
    .instTable tbody tr td {
        border: 1px solid #5e5c5c !important;
    }
</style>

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<div class="col-12">
    <div class="card">
        <div class="card-body">

            <div class="col-md-4 mb-3  normal_div">
                <label class="form-label lang required" key="{{ $deptcode == '01' ? 'fasil' : 'audityear_label' }}"
                    for="validationDefault01">{{ $deptcode == '01' ? 'Fasli Year' : 'Audit Year' }}
                </label>

                <select class="form-select mr-sm-2 lang-dropdown " id="n_yearcode" name="n_yearcode"
                    onchange="fetch_parastatus()">
                    <option value ="" data-name-en="---Select Audit Year---"
                        data-name-ta="---தணிக்கை ஆண்டைத் தேர்ந்தெடுக்கவும்---">--select Audit Year--
                    </option>
                    @foreach ($normalyear as $year)
                        <option value="{{ $year->auditperiodid }}">
                            {{ $year->audit_period }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="card card_border " id="table_div">
    <div class="card-header card_header_color lang" key="">Para Details</div>
    <div class="card-body"><br>
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="paratable"
                    class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang align-middle text-center" key="s_no">S.No</th>
                            <th class="lang align-middle text-center" key="department">Department</th>
                            {{-- <th class="lang align-middle text-center" key="inst">Institution DEtails
                                    Name</th> --}}
                            <th class="text-wrap" key="cat_det">Category Details</th>
                            <th class="text-wrap" key="">Para Details</th>
                            <th class="all lang align-middle text-center" key="status">Status</th>
                            <th class="all lang align-middle text-center" key="">View FLow</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div id='no_data' class=' lang text-center' key="no_data">
            <center class="lang" key="no_data">No Data Available</center>
        </div>
    </div>
</div>
</div>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>


<script>
    let dataFromServer;
    let lagacyparatype = '<?php echo $lagacyparatype; ?>';
    let normalparatype = '<?php echo $normalparatype; ?>';
    let year_div = ''

    function onchange_paratype() {
        var paratype = $('#paratype').val()
        if (paratype == lagacyparatype) {
            $('#n_yearcode').val('')
            $('#n_yearcode').select2()
            $('.lagacy_div').show()
            $('.normal_div').hide()
            $('#tableshow').hide();
            $('#no_data').show()

        } else if (paratype == normalparatype) {
            $('#yearcode').val('')
            $('#yearcode').select2()
            $('.lagacy_div').hide()
            $('.normal_div').show()
            $('#tableshow').hide();
            $('#no_data').show()

        } else {
            $('.lagacy_div').hide()
            $('.normal_div').hide()
            $('#tableshow').hide();
            $('#no_data').show()
            $('#yearcode').val('')
            $('#yearcode').select2()
            $('#n_yearcode').val('')
            $('#n_yearcode').select2()
        }
    }

    // $(document).ready(function() {

    //     fetch_parastatus();
    // });

    function fetch_parastatus() {

        var paratype = $('#paratype').val()

        year_div = paratype == lagacyparatype ? $('#yearcode') : $('#n_yearcode')

        var audityear = year_div.val()
        $.ajax({
            url: "legacy/fetch_parastatus",
            type: "POST",
            data: {
                yearcode: audityear,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(json) {
                const language = getLanguage('')
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#saudit_plandetails_wrapper').show();
                    $('#no_data').hide(); // Hide custom "No Data" message
                    dataFromServer = json.data;
                    console.log(dataFromServer)
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#saudit_plandetails_wrappers').hide();
                    $('#no_data').show();
                }

            },
            error: function() {
                alert('Error fetching region. Please try again.');
            }
        });
    }

    function renderTable(language) {
        const InstituteName = language === 'ta' ? 'insttname' : 'instename';
        const Dept = language === 'ta' ? 'depttsname' : 'deptesname';
        const Category = language === 'ta' ? 'cattname' : 'catename';
        const SubCategory = language === 'ta' ? 'subcattname' : 'subcatename';
        const TypeofAudit = language === 'ta' ? 'typeofaudittname' : 'typeofauditename';
        const region = language === 'ta' ? 'regiontname' : 'regionename';
        const district = language === 'ta' ? 'disttname' : 'distename';
        const username = language === 'ta' ? 'usertamilname' : 'username';



        if ($.fn.dataTable.isDataTable('#paratable')) {
            $('#paratable').DataTable().clear().destroy();
        }

        var table = $('#paratable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "scrollX": false,
            "initComplete": function(settings, json) {
                $("#paratable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "autoWidth": false,
            "responsive": true,
            "destroy": true, // Destroy and reinitialize
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: Dept,
                    //title: columnLabels?.[Dept]?.[language] || 'Department',
                    render: function(data, type, row) {
                        return row[Dept] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                // {
                //     data: "null",
                //     //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                //     render: function(data, type, row) {
                //         return `<b>Region:</b>${row[region]}<br>  ${row[SubCategory] ? `<b>District:</b> ${row[district]}<br>` : ""}`;
                //         // return row[InstituteName] || '-';
                //     },
                //     className: 'd-none d-md-table-cell lang extra-column text-wrap'
                // },
                {
                    data: "null",
                    //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                    render: function(data, type, row) {
                        return `<b>Category:</b>${row[Category]}<br>  ${row[SubCategory] ? `<b>Sub Category:</b> ${row[SubCategory]}<br>` : ""}`;
                        // return row[InstituteName] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: TypeofAudit,
                    // title: columnLabels?.[TypeofAudit]?.[language] || 'Plan Period',
                    render: function(data, type, row) {
                        return `<b>Audit Year:</b>${row.audit_period}<br> <b> Para Number: </b>${row.paranumber} `;
                    },
                    className: 'd-none d-md-table-cell lang extra-column '
                },
                {
                    data: null,

                    render: function(data, type, row) {
                        let status_msg = '';
                        switch (row.processcode) {

                            case 'E':
                                status_msg = 'Entry'
                                break;
                            case 'F':
                                status_msg = 'Forwarded to PSA Auditor'
                                break;

                            case 'K':
                                status_msg = 'Forwarded to PSA AD'
                                break;
                            case 'U':
                                status_msg = 'Forwarded to Auditee'
                                break;
                            case 'A':
                                status_msg = 'Dropped'
                                break;
                            case 'X':
                                status_msg = 'Rejected'
                                break;

                            default:
                                status_msg = 'Entry';
                                break;

                        }


                        return `${status_msg}`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },

                {
                    data: "paraid",
                    render: function(data, type, row) {
                        return `<div class="btn btn-sm bg-info flow_btn" id="${row.paraid}" >
                        <i class="ti ti-clock fs-4 text-light"></i>
                    </div>`;

                    },
                    className: "text-center text-wrap noExport"
                }
            ],

        });

        const mobileColumns = [Category, SubCategory];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "audit_plandetails");
    }
    $(document).on('click', '.flow_btn', function() {
        const id = $(this).attr('id');
        console.log("Clicked paraid:", id);
        $.ajax({
            url: "legacy/fetch_historyparastatus",
            type: "POST",
            data: {
                id: id,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(json) {
                const lang = getLanguage('')
                if (json.data && json.data.length > 0) {
                    var designation = lang == 'ta' ? 'பதவி' : 'Designation';
                    var status = lang == 'ta' ? 'நிலை' : 'Status';
                    var serialno = lang == 'ta' ? 'எஸ்.எண்' : 'S.No';
                    var actiontakenon = lang == 'ta' ? 'அனுப்பிய விவரங்கள்' :
                        'Forwarded On';

                    var datacontent = `<div class=" table-responsive rounded-2 border " style="max-width: 750px;overflow-x: auto;max-height: 600px; overflow-y: auto;width:98%;margin:0 auto;">
                           <table class="table userCountTable">
                               <thead class="">
                                   <tr>
                                       <th class="lang userCount_head" key="audit_office">${serialno}
                                       </th>
                                       <th class="lang userCount_head" key="">${designation}
                                       </th>
                                       <th class="lang userCount_head" key="">${status}</th>
                                        <th class="lang userCount_head" key="">${actiontakenon}</th>
                                   </tr>
                               </thead>
                               <tbody id="instTableBody">

                               </tbody>
                           </table>
                       </div>`;
                    passing_extra_large_alert('Para  Details', datacontent,
                        'extra_large_confirmation_alert',
                        'extra_large_alert_header',
                        'extra_large_alert_body', 'confirmation_alert', 'send_intimation_label');
                    setTimeout(() => {
                        populatTable(json.data);
                    }, 100);
                } else {

                }

            },
            error: function() {
                alert('Error fetching region. Please try again.');
            }
        });
    });

    function populatTable(det) {
        const instbody = document.getElementById('instTableBody');
        instbody.innerHTML = '';

        det.forEach((item, index) => {
            const row = document.createElement('tr');
            let status = '';
            switch (item.processcode) {

                case 'E':
                    status = 'Entry'
                    break;
                case 'F':
                    status = 'Forwarded to PSA Auditor'
                    break;

                case 'K':
                    status = 'Forwarded to PSA AD'
                    break;
                case 'U':
                    status = 'Forwared to Auditee'
                    break;
                case 'I':
                    status = 'Rejected'
                    break;
                case 'A':
                    status = 'Dropped'
                    break;

                default:
                    status = 'Entry';
                    break;

            }

            status += ((item.rejoinderstatus == 'Y' && (item.rejoinderstatus == 'A' || item.rejoinderstatus ==
                'R')) ? ' - (Rejoinder)' : '')

            let actiondate = item.forwardedon ?
                (() => {
                    let d = new Date(item.forwardedon);
                    let day = String(d.getDate()).padStart(2, '0');
                    let month = String(d.getMonth() + 1).padStart(2, '0');
                    let year = d.getFullYear();
                    let hours = String(d.getHours()).padStart(2, '0');
                    let minutes = String(d.getMinutes()).padStart(2, '0');
                    let seconds = String(d.getSeconds()).padStart(2, '0');
                    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                })() :
                "N/A";

            const lang = getLanguage();

            const roleMap = {
                'I': lang == 'en' ? 'Auditee' : 'தணிக்கையாளர்',
                'A': lang == 'en' ? 'PSA Auditor' : 'பி.எஸ்.ஏ. தணிக்கையாளர்',
                'AD': lang == 'en' ? 'PSA AD' : 'பி.எஸ்.ஏ. உதவி இயக்குநர்'
            };

            var actiontaken_name = ''
            switch (item.actroleactioncode) {
                case 'I':
                    actiontaken_name = lang == 'ta' ? 'பத்திக்கு பதிலளிக்கப்பட்டது' :
                        'Replied to Para'
                    break;
                case 'A':
                    actiontaken_name = lang == 'ta' ? item.actiontname : item.actionename
                    break;
                case 'AD':
                    actiontaken_name = lang == 'ta' ? item.actiontname : item.actionename
                    break;

                default:
                    actiontaken_name = 'Reply'
                    break;
            }
            const actroleactioncode = roleMap[item.actroleactioncode] || '-';
            row.innerHTML = `
                       <td><h6>${index + 1}<h6></td>
                       <td><h6>${actroleactioncode??'-'}<h6></td>
                       <td><h6>${actiontaken_name??'-'}<h6></td>
                       <td><h6>${actiondate??'-'}<h6></td>
                   `;
            instbody.appendChild(row);
        });
    }
</script>
@endsection
