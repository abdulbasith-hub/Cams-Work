@section('content')
@section('title', 'Audit Para Management')
@extends('index2') @include('common.alert')



<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="../assets/css/fieldaudit.css">


<?php
$sessionchargedel = session('charge');
$sessionuserdel = session('user');

$sessionuserid = $sessionuserdel->userid;
$deptcode = $sessionchargedel->deptcode;
$sessionroletypecode = $sessionchargedel->roletypecode;

$dga_roletypecode = $DGA_roletypecode;
$Dist_roletypecode = $Dist_roletypecode;
$Re_roletypecode = $Re_roletypecode;
$Ho_roletypecode = $Ho_roletypecode;
$Admin_roletypecode = $Admin_roletypecode;
$roleTypeCode = $sessionchargedel->roletypecode;

$ensessionuserid = $session_userid;

$yearofaudit_arr = json_decode($yearofaudit, true);
// dd($yearofaudit_arr);
$count_audityear = count($yearofaudit_arr);

?>


<div class="row">

    @if ($count_audityear)

        <form id="para_form" name="para_form">
            @csrf
            <div class="col-12">

                <div class="spinner-wrapper " id="loader">
                    <div class="dot-spinner">
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                        <div class="dot-spinner__dot"></div>
                    </div>
                </div>

                <div class="card ">
                    <div class="card-header card_header_color">
                        Institution Details
                    </div>
                    <div class="card-body">

                        <div class="row">

                            <input type="hidden" name="instid" id="instid"
                                value="{{ $instData->encrypted_instid ?? '' }}">
                            <input type="hidden" name="currentslipnumber" id="currentslipnumber" value='1'>
                            <input type="hidden" name="followupid" id="followupid" value="">
                            <input type="hidden" name="paraid" id="paraid" value=''>
                            <input type="hidden" name="paranumber" id="paranumber" value=''>
                            <input type="hidden" name="catcode" id="catcode" value="{{ $instData->catcode ?? '' }}">
                            <input type="hidden" name="ens" id="ens" value='<?php echo $ensessionuserid; ?>'>
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">
                            <input type="hidden" name="seriesno" id="seriesno" value='1'>
                            <input type="hidden" name="auditeeins_subcategoryid" id="auditeeins_subcategoryid"
                                value="{{ $instData->auditeeins_subcategoryid ?? '' }}">
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">
                            <input type="hidden" name="rejoindercycle" id="rejoindercycle" value="">
                            <input type="hidden" name="rejoinderstatus" id="rejoinderstatus" value="">
                            <input type="hidden" name="fileuploadid" id="fileuploadid" value="">
                            <input type="hidden" name="processcode" id="processcode" value="">



                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" key="inst" for="catname">Institution
                                </label>
                                <input type="text" class="form-control lang_value" id="instname" name="instname"
                                    data-en="{{ $instData->instename ?? '' }}"
                                    data-ta="{{ $instData->insttname ?? '' }}"
                                    value="{{ $instData->instename ?? '' }}" disabled>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" for="catname" key="category">Institution
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="catname" name="catname"
                                    data-en="{{ $catData->catename ?? '' }}" data-ta="{{ $catData->cattname ?? '' }}"
                                    value="{{ $catData->catename ?? '' }}" disabled>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" for="subcatname" key="sub_head">Institution
                                    Sub
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="subcatname"
                                    name="subcatname"
                                    data-en="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                    data-ta="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcattname : $catData->cattname }}"
                                    value="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="card mt-2" style="border-color: #7198b9">
                            <div class="card-header card_header_color lang" key="para_det">Audit Para Management
                                System
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-4 mb-3 normalpara_div ">
                                        <label class="form-label lang required"
                                            key="{{ $instData->deptcode == '01' ? 'fasil' : 'audityear_label' }}"
                                            for="validationDefault01">{{ $instData->deptcode == '01' ? 'Fasli Year' : 'Audit Year' }}
                                        </label>

                                        <select class="form-select mr-sm-2 lang-dropdown select2" id="n_yearcode"
                                            name="n_yearcode[]"
                                            onchange="fetch_paramanagement_auditee('', '','P','Y','','year')">
                                            <option value="">
                                                --select Audit Year--
                                            </option>
                                            @foreach ($yearofaudit as $year)
                                                <option value="{{ $year->auditperiodid }}">
                                                    {{ $year->audit_period }} (Paras: {{ $year->total_paras }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3  ">
                                        <label class="form-label lang required" key="parano"
                                            for="validationDefault01">Para Number
                                        </label>

                                        <select class="form-select mr-sm-2 lang-dropdown select2" id="parano"
                                            name="parano[]"
                                            onchange="fetch_paramanagement_auditee('', '','P','Y','','parano')">
                                            <option value="">
                                                --Select Para Number--
                                            </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="datatables">
                                    <div class="table-responsive overflow-x-hidden hide_this" id="tableshow">
                                        <table id="paradetails_table"
                                            class="table table-sm w-100 table-striped table-bordered display datatables-basic text-wrap align-middle">
                                            <thead>
                                                @csrf
                                                <tr>
                                                    <th class="lang text-wrap text-break text-center align-middle" key="s_no">S.No
                                                    </th>
                                                    <th class="lang text-wrap text-break text-center align-middle" key="">Para Number</th>
                                                    <th class="lang text-wrap text-break align-middle d-none d-md-table-cell" key="main_obj_label">
                                                        Title/Heading</th>
                                                    <th class="lang text-wrap text-break text-center align-middle d-none d-md-table-cell" key="amount_involved">
                                                        Amount Involved
                                                    </th>
                                                    <th class="lang text-wrap text-break text-center align-middle d-none d-md-table-cell" key="severity">
                                                        Severity Type</th>
                                                    <th class="lang text-wrap text-break align-middle d-none d-md-table-cell" key="gist">
                                                        Gist of Para</th>
                                                    <th class="lang text-wrap text-break text-center align-middle d-none d-md-table-cell" key="statusflag">Para Status</th>
                                                    <th class="lang text-wrap text-break text-center align-middle" key="action">

                                                        Action</th>

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
            </div>
        </form>
    @else
        <div class="card " style="border-color: #7198b9">
            <div class="card-header card_header_color">Audit Para Management Details</div>
            <div class="card-body">
                <br>
                <center>No Data Available</center>
            </div>
        </div>
    @endif
</div>

<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/js/apps/chat.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>


<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            $('#loader').addClass('d-none');

        }
    });

    let maxRow = 5;
    let maxfilecount = 3;
    let sessionuserid = '<?php echo $sessionuserid; ?>';
    let lagacyparatype = '<?php echo $lagacyparatype; ?>';
    let normalparatype = '<?php echo $normalparatype; ?>';
    let year_div = ''
    let selectedYear = ''
    let selectedpara = ''
    let dataFromServer = ''
    var lang;



    $(document).ready(function() {

        lang = getLanguage();
        //   fetch_paramanagement_auditee('', '', 'P', 'Y', '');
        let yearCode = "{{ $n_yearcode }}";
        let load_parano = "{{ $parano }}";

        if (yearCode) {
            $('#n_yearcode').val(yearCode);
            fetch_paramanagement_auditee('', load_parano, 'P', 'Y', '', 'year');
        }

    });

    $("#translate").change(function() {
        lang = getLanguage('Y');
        switchChecklistLanguage(lang)
        // updateTableLanguage(lang);

    });

    function switchChecklistLanguage(lang) {

        $('.lang_value').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            $(this).val(lang === 'ta' ? ta : en);
        });
    }


    function fit_status(processcode, rejoinderstatus) {

        const statusMap = {
            E: {
                text: 'Entry',
                bg: 'bg-primary'
            },
            F: {
                text: 'Forwarded to PSA Auditor',
                bg: 'forward_psa'
            },
            K: {
                text: 'Forwarded to PSA AD',
                bg: 'forward_psad'
            },
            U: {
                text: 'Forwarded to Auditee',
                bg: 'forward_auditee'
            },
            A: {
                text: 'Dropped',
                bg: 'bg-success'
            },
            I: {
                text: 'Rejected',
                bg: 'bg-danger'
            },
            D: {
                text: 'Forwarded to District HLC',
                bg: 'bg-primary'
            },
            B: {
                text: 'Forwarded from District HLC',
                bg: 'bg-primary'
            },
            DH: {
                text: 'Forwarded to Department HLC',
                bg: 'bg-primary'
            },
            IS: {
                text: 'Forwarded to State HLC',
                bg: 'bg-primary'
            },
            AE: {
                text: 'Forwarded to Department HLC',
                bg: 'bg-primary'
            },
            AS: {
                text: 'Forwarded to State HLC',
                bg: 'bg-primary'
            },
            PH: {
                text: 'Pending at HLC',
                bg: 'bg-primary'
            },
            PA: {
                text: 'Forwarded from PSA Auditor(Responsibility Removal)',
                bg: 'bg-primary'
            },
            PR: {
                text: 'Forwarded from PSA Auditor(Part(s) Removal)',
                bg: 'bg-primary'
            },
            L: {
                text: 'Forwarded from Committee',
                bg: 'bg-primary'
            },
            V: {
                text: 'Forwarded to Committee',
                bg: 'bg-primary'
            }

        };

        const defaultStatus = {
            text: 'New',
            bg: 'bg-info'
        };

        const statusObj = statusMap[processcode] || defaultStatus;

        const isRejoinder =
            rejoinderstatus === 'Y' &&
            !['A', 'I'].includes(processcode);

        return {
            status: statusObj.text + (isRejoinder ? ' - (Rejoinder)' : ''),
            bg_name: statusObj.bg
        };
    }







    function fetch_paramanagement_auditee(followupid, paranumber, action, createnewone, yearcode, param) {
        $('#loader').removeClass('d-none');

        var selectedYear = $('#n_yearcode').val();
        var yearcode = yearcode || $('#n_yearcode').val();
        var instid = $('#instid').val();
        var parano = paranumber || $('#parano').val();

        $.ajax({
            url: '/lagacy/fetch_paramanagement_auditee',
            type: 'POST',
            data: {
                followupid: followupid,
                instid: instid,
                action: action,
                'yearcode[]': yearcode,
                param: param,
                parano: parano
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (!response.data) {
                    $('#tableshow').hide();
                    $('#saudit_plandetails_wrappers').hide();
                    $('#no_data').show();
                }

                if (param == 'year') {
                    var paraData = response.data

                    $('#parano').empty()
                    $('#parano').append(
                        '<option value = "" > --select Para Number--</option><option value = "A" >All</option>'
                    );
                    $.each(paraData, function(index, para) {
                        var isSelected = para.paranumber == paranumber ? 'selected' : '';
                        $('#parano').append('<option value="' + para.paranumber + '" ' +
                            isSelected +
                            ' data-name-en="' + para.paranumber + '"' +
                            ' data-name-ta="' + para.paranumber + '">' +
                            'Para Number : ' + para.paranumber +
                            '</option>');
                    });
                }

                if (paranumber) {
                    fetch_paramanagement_auditee('', '', 'P', 'Y', '', 'parano');
                }


                if (action == 'P') {
                    $('#status_row').hide()
                    const lang = getLanguage()
                    if (!response.data) {
                        $('#tableshow').hide();
                        $('#saudit_plandetails_wrappers').hide();
                        $('#no_data').show();
                    } else if (response.data['data'] && response.data['data'].length > 0) {
                        $('#tableshow').show();
                        dataFromServer = response.data['data'];
                        $('#no_data').hide();
                        renderTable(lang);
                    } else {
                        $('#tableshow').hide();
                        $('#saudit_plandetails_wrappers').hide();
                        $('#no_data').show();
                    }


                }

            },
            complete: function() {
                $('#loader').addClass('d-none');
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.error ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }







    function renderTable(language) {
        const objectionName = language === 'ta' ? 'objectiontname' : 'objectionename';
        const irregularitiesname = language === 'ta' ? 'irregularitieselname' : 'irregularitiestlname';
        // const region = language === 'ta' ? 'regiontname' : 'regionename';
        // const district = language === 'ta' ? 'disttname' : 'distename';




        if ($.fn.dataTable.isDataTable('#paradetails_table')) {
            $('#paradetails_table').DataTable().clear().destroy();
        }

        var table = $('#paradetails_table').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "autoWidth": false,
            "responsive": true,
            "scrollX": false,
            "destroy": true, // Destroy and reinitialize
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-center align-top text-wrap text-break',
                    type: "num"
                },
                {
                    data: "paranumber",
                    //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                    render: function(data, type, row) {
                        return row.paranumber;

                    },
                    className: 'text-center align-top lang extra-column text-wrap text-break'
                },
                {
                    data: "null",
                    //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                    render: function(data, type, row) {
                        return row[objectionName] || '-';

                    },
                    className: 'd-none d-md-table-cell align-top lang extra-column text-wrap text-break'
                },
                {
                    data: "null",
                    //title: columnLabels?.[InstituteName]?.[language] || 'Institute Name',
                    render: function(data, type, row) {

                        return row.amtinvolved || '-'; // return row[InstituteName] || '-';
                    },
                    className: 'text-center align-top d-none d-md-table-cell lang extra-column text-wrap text-break'
                },
                {
                    data: null,

                    render: function(data, type, row) {


                        return row[irregularitiesname] || '-' // return row[InstituteName] || '-';

                    },
                    className: 'text-center align-top d-none d-md-table-cell lang extra-column text-wrap text-break'
                },
                {
                    data: null,

                    render: function(data, type, row) {


                        return row.slipdetails || '-' // return row[InstituteName] || '-';

                    },
                    className: 'd-none d-md-table-cell align-top lang extra-column text-wrap text-break'
                },
                {
                    data: null,

                    render: function(data, type, row) {
                        const statusdet = fit_status(row.processcode, row.rejoinderstatus)
                        const status = statusdet.status;
                        const bg = statusdet.bg_name;


                        const isDLC_para = ['02', '03', '04'].includes(row.stateofparacode);
                        var dlc_badge =
                            `<span class="btn btn-sm bg-danger mt-2 text-light text-wrap text-break py-1 px-1">
                                HLC Para</span>`


                        const current_state_btn =
                            `<span class="badge text-light text-wrap text-break ${bg}">${status}</span>` || '-'


                        return `${isDLC_para?dlc_badge:current_state_btn}`

                    },
                    className: 'text-center align-top d-none d-md-table-cell lang extra-column text-wrap text-break'
                },
                {
                    data: null,

                    render: function(data, type, row) {
                        let baseUrl =
                            "{{ route('auditeefollowup', ['id' => ':id', 'instid' => ':instid', 'paratype' => ':paratype', 'followupid' => ':followupid']) }}";

                        baseUrl = baseUrl
                            .replace(':id', row.encrypted_paraid)
                            .replace(':instid', row.instid)
                            .replace(':paratype', row.paratype)
                            .replace(':followupid', row.encrypted_followupid);

                        return `
                          <a class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center gap-1 text-wrap text-break" href="${baseUrl}">
                              <i class="ti ti-eye fs-5"></i>
                          </a>
                      `;


                    },
                    className: 'text-center align-top lang extra-column text-wrap text-break'
                },


            ],
            "columnDefs": [{
                    targets: 0,
                    width: '5%',
                    className: 'text-center align-top text-wrap text-break'
                },
                {
                    targets: 1,
                    width: '7%',
                    className: 'text-center align-top text-wrap text-break'
                },
                {
                    targets: 2,
                    width: '12%',
                    className: 'align-top text-wrap text-break'
                },
                {
                    targets: 3,
                    width: '8%',
                    className: 'text-center align-top text-wrap text-break'
                },
                {
                    targets: 4,
                    width: '9%',
                    className: 'text-center align-top text-wrap text-break'
                },
                {
                    targets: 5,
                    width: '38%',
                    className: 'align-top text-wrap text-break'
                },
                {
                    targets: 6,
                    width: '12%',
                    className: 'text-center align-top text-wrap text-break'
                },
                {
                    targets: 7,
                    width: '9%',
                    className: 'text-center align-top text-wrap text-break',
                    orderable: false
                }
            ],

        });

        const mobileColumns = [objectionName, irregularitiesname];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "paradetails_table");
    }
</script>

@endsection
