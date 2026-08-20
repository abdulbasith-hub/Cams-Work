@section('content')

@section('title', 'Work Allocation Report')
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
            <div class="card-header card_header_color lang" key="">Audit Diary Lists</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditdiarytable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="">Institutions</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="">Team Members</th>
                                    <th class="lang align-middle text-center" key="">Mandays</th>
                                    <th class="lang align-middle text-center" key="">Region</th>
                                    <th class="lang align-middle text-center" key="">District</th>
                                    <th class="lang align-middle text-center" key="">Entry Meet Date</th>
                                    <th class="lang align-middle text-center" key="">Exit Meet Date</th>

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

    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
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
        updateValidationMessages(getLanguage('Y'), 'auditdiarytable');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('auditdiarytable_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
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

        if ($.fn.DataTable.isDataTable('#auditdiarytable')) {
            $('#auditdiarytable').DataTable().clear().destroy();
        }
        table = $('#auditdiarytable').DataTable({
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
                    className: 'text-end',
                    type: "num"
                },
                
                {
                    data: "instename",
                    title: columnLabels?.["instename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.instename || '-';
                    }
                },

                {
                    data: null,
                    title: columnLabels?.["team_head_en"]?.[language] ,
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const head = row.team_head_en || '-';
                        const members = row.team_members_en || '-';
                        return `<div><strong>Head:</strong> ${head}</div><div><strong>Members:</strong> ${members}</div>`;
                    }
                },
                {
                    data: "mandays",
                    title: columnLabels?.["mandays"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.mandays || '-';
                    }
                },
                {
                    data: "regionename",
                    title: columnLabels?.["regionename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.regionename || '-';
                    }
                },
                {
                    data: "distename",
                    title: columnLabels?.["distename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.distename || '-';
                    }
                },
                {
                    data: "entrymeetdate",
                    title: columnLabels?.["entrymeetdate"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (!row.entrymeetdate) return '-';

                        const date = new Date(row.entrymeetdate);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();

                        return `${day}-${month}-${year}`;
                    }
                },


                {
                data: "exitmeetdate",
                title: columnLabels?.["exitmeetdate"]?.[language],
                className: "d-none d-md-table-cell lang extra-column text-wrap",
                render: function(data, type, row) {
                    if (!row.exitmeetdate) return '-';

                    const date = new Date(row.exitmeetdate);
                    if (isNaN(date)) return row.exitmeetdate; // fallback in case of invalid date

                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return `${day}-${month}-${year}`;
                }
            },

            {
                data: null,
                title: "Audit Diary",
                className: "d-none d-md-table-cell text-center",
                render: function (data, type, row) {
                    const scheduleId = row.encrypted_auditscheduleid || '';
                    const memberId = row.encrypted_schteammemberid || '';

                    const rawScheduleId = row.raw_auditscheduleid || '';
                    const rawMemberId = row.raw_schteammemberid || '';

                    if (row.exitmeetdate) {

                        return `<span class="badge bg-success">Schedule Completed</span>`;

                    }else if(row.diarystatus === 'F'){

                        return `<span class="badge" style="background-color:rgb(193, 123, 66); color: white;">Diary Completed</span>`; // purple
                    }
                    else if (row.diarystatus === 'N' || row.diarystatus == null || row.diarystatus === '') {
                        return `
                            <button class="btn btn-primary btn-sm audit-diary-btn"
                                    data-schedule="${scheduleId}"
                                    data-member="${memberId}"
                                    data-rawschedule="${rawScheduleId}"
                                    data-rawmember="${rawMemberId}">
                                Audit Diary
                            </button>`;
                    } 
                    else {
                        console.log('Server Error');
                    }


                    
                }
            }



                
            ],

            "initComplete": function(settings, json) {
                $("#auditdiarytable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["majorworkallocationtypeename", "majorworkallocationtypetname", "statusflag"];
        setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "auditdiarytable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditdiarytable')) {
            $('#auditdiarytable').DataTable().clear().destroy();
        }
        renderTable(language);
    }

    function formatDate(inputDate) {
    if (!inputDate) return '-';
    const date = new Date(inputDate);
    if (isNaN(date.getTime())) return '-'; // invalid date
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}


    $(document).on('click', '.audit-diary-btn', function (e) {
    e.preventDefault();
    const scheduleId = $(this).data('schedule');
    const memberId = $(this).data('member');

    const rawScheduleId = $(this).data('rawschedule');
    const rawMemberId = $(this).data('rawmember');



    if (scheduleId && memberId) {
        const url = `/audit_diary?schedule=${encodeURIComponent(scheduleId)}&member=${encodeURIComponent(memberId)}`;
        window.location.href = url;   
     } else {
        console.warn("Encrypted IDs missing.");
    }
});









</script>


@endsection
