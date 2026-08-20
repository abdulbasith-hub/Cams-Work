@extends('index2')
@section('content')
    @include('common.alert')

    @php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp

    <link rel="stylesheet" href="../../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <!-- <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"> -->



    <style>
        table th,
        td {
            text-align: center !important;
            vertical-align: middle;

        }

        input,
        select textarea,
        th {
            font-size: 13px !important;
        }

        .error-msg {
            color: red;
            font-size: 12px;
            display: none;
        }

        .progress-container {
            height: 17px;
            width: 200px;
            vertical-align: middle;
            align: center;
            margin: 0 auto;
        }

        .percentage-label {
            font-size: 20px;
        }

        thead th {
            position: sticky;
            z-index: 10;
            top: 0;
        }


    #file_export thead tr:nth-child(2) th {

            top: 44px; 
    }
        


        /**style="background-color: #539bff;padding:10px;" */
    </style>

@php
    $row = $HomeAuditDiaryTableData[0];
@endphp

@if ($row->diarystatus === 'F')

<div class="modal fade" id="fixedAuditModal" tabindex="-1" aria-labelledby="fixedAuditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-primary text-white rounded-top">
        <h5 class="modal-title text-center text-white w-100" id="fixedAuditLabel">Audit Status</h5>
      </div>
      <div class="modal-body text-center fs-5">
        Audit Diary Completed
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success px-4" id="modalCloseBtn">
          OK
        </button>
      </div>
    </div>
  </div>
</div>


<script>
    window.onload = function () {
        const modalElement = document.getElementById('fixedAuditModal');

        // Initialize modal with static backdrop and no keyboard escape
        const fixedModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        fixedModal.show();

        // Only close and redirect on OK button click
        document.getElementById('modalCloseBtn').addEventListener('click', function () {
            fixedModal.hide();
            window.location.href = "{{ url('auditdiary_home') }}";
        });
    };
</script>

   
@else
   


    <div class="row">

        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
            <div class="card-header card_header_color">
                    Audit Progress Status
                </div>

                <div class="container mb-3 mt-4">
                  @if (!empty($HomeAuditDiaryTableData) && count($HomeAuditDiaryTableData) > 0)
                    @php $row = $HomeAuditDiaryTableData[0]; @endphp

                    <div class="row mb-2">
                        <div class="col-md-3"><strong>Department:</strong> {{ $row->deptelname }}</div>
                        <div class="col-md-3"><strong>Region:</strong> {{ $row->regionename }}</div>
                        <div class="col-md-3"><strong>District:</strong> {{ $row->distename }}</div>
                        <div class="col-md-3"><strong>Institution:</strong> {{ $row->instename }}</div>

                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3"><strong>Quarter:</strong> {{ $row->auditquarter }}</div>
                        <div class="col-md-3"><strong>Team Head:</strong>{{ $row->team_head_en }}</div>
                        <div class="col-md-3"><strong>Team Members:</strong>{{ $row->team_members_en }}</div>
                        <div class="col-md-3">
                            <strong>Entry Meeting:</strong>
                            @if (!empty($row->entrymeetdate))
                                {{ \Carbon\Carbon::parse($row->entrymeetdate)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </div>


                    </div>

                    <div class="col-md-3">
                        <strong>Exit Meeting:</strong>
                        @if (!empty($row->exitmeetdate))
                            {{ \Carbon\Carbon::parse($row->exitmeetdate)->format('d-m-Y') }}
                        @else
                            N/A
                        @endif
                    </div>
                    
                @else
                    <div class="row">
                        <div class="col-md-12 text-danger">
                            No audit information available.
                        </div>
                    </div>
                @endif
            </div>
                <div >
                    <div>
                        <!-- start File export -->
                        <div>
                            <form id="create_auditdiary" name="create_auditdiary">
                                @csrf

                                <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header justify-content-center position-relative">
                                        <h5 class="modal-title text-center" id="historyModalLabel">History</h5>
                                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <div id="historyTopInfo" class="mb-3">
                                        <!-- Work Allocation Name and Grouping will appear here -->
                                    </div>
                                        <table class="table table-bordered table-striped mb-0" id="historyTable">
                                            <thead  >
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                    <th>Pecentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="4" class="text-center">Loading...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                                </div>






                                @foreach($HomeAuditDiaryTableData as $data)
                                    <input type="hidden" id="entrymeetdate" name="entrymeetdate" value="{{ $data->entrymeetdate }}">
                                    <input type="hidden" id="exitmeetdate" name="exitmeetdate" value="{{ $data->exitmeetdate }}">
                                @endforeach


                                <input type="hidden" id='schedule' name="schedule" value="{{ $encryptedScheduleId }}">

                                <input type="hidden" name="member" id="member" value="{{ $encryptedTeamMemberId }}">
                                @if (sizeof($Workallocated_Category) == 0)
                                    <div  class="hide_this" style="display: block;">
                                        <center>No Data Available</center>
                                    </div>
                                @else
                               
                               <?php //print_r($FromDate); ?>
                                 <div style="max-height: 600px; overflow-y: auto;width:98%;padding-left:14px;">
                                    <table   id="file_export" class="table w-100 table-bordered datatables-basic">
                                        <thead >
                                            <!-- start row -->
                                            <tr>
                                                <th colspan="2">Work Allocation</th>
                                                <th rowspan="2">Date</th>
                                                <th rowspan="2">Percentage Completed</th>
                                                <th rowspan="2">Remarks</th>

                                            </tr>
                                            <tr>
                                                <th>Grouping</th>
                                                <th>Allocated Work</th>
                                            </tr>
                                            <!-- end row -->
                                        </thead>
                                        <tbody>

                                            <input type="hidden" name="actiontype" value="{{ $actiontype }}" />

                                            @foreach ($Workallocated_Category as $workkey => $workval)
                                                <!-- Get the number of subcategories for this category -->
                                                @php
                                                    $subCategoryCount = count($Workallocated_SubCategory[$workkey]);
                                                @endphp
                                                <tr>
                                                    <!-- Work Allocation Category Selection with rowspan -->
                                                    @if ($subCategoryCount > 1)
                                                        <td class="align-top mt-5" rowspan="{{ $subCategoryCount }}">
                                                            <!-- <select disabled class="form-select"
                                                                aria-label="Default select example">
                                                                <option>{{ $workval }}</option>
                                                            </select> -->
                                                            <input type="text" class="form-control mt-3" value="{{ $workval }}" disabled>

                                                        </td>
                                                    @else
                                                        <td>
                                                            <!-- <select disabled class="form-select"
                                                                aria-label="Default select example">
                                                                <option>{{ $workval }}</option>
                                                            </select> -->
                                                            <input type="text" class="form-control" value="{{ $workval }}" disabled>

                                                        </td>
                                                    @endif

                                                    <!-- First SubCategory Selection -->
                                                    <td>
                                                    <input type="text" class="form-control"
                                                            value="{{ $Workallocated_SubCategory[$workkey][array_key_first($Workallocated_SubCategory[$workkey])] }}"
                                                            disabled>

                                                    </td>
                                                    <!-- From Date Selection -->
                                                    <?php $i = 0; ?>
                                                    @foreach ($WorkAllocationId[$workkey] as $WorkAllocKey => $WorkAllocVal)
                                                        <?php
                                                                    if ($i == 0):
                                                                ?>
                                                        <td>

                                                     


                                                            <input type="hidden" class="workallocationid"
                                                                value="{{ $WorkAllocVal }}"
                                                                name="workallocationid[{{ $WorkAllocVal }}]" />

                                                            <input type="hidden" class="auditdiaryid"
                                                                value="{{ isset($AuditDiaryId[$WorkAllocVal]) ? $AuditDiaryId[$WorkAllocVal] : '' }}"
                                                                name="auditdiaryid[{{ $WorkAllocVal }}]" />
                                                            <div class="input-group">
                                                                
                                                            <input type="text" name="fromdate[{{ $WorkAllocVal }}]"
                                                                id="fromdate{{ $WorkAllocVal }}"
                                                                value="{{ isset($FromDate[$WorkAllocVal]) && $FromDate[$WorkAllocVal] != '' ? \Carbon\Carbon::parse($FromDate[$WorkAllocVal])->format('d/m/Y') : '' }}"
                                                                class="form-control datepicker" autocomplete="off"
                                                                placeholder="dd/mm/yyyy">
                                                            <span class="input-group-text">
                                                                <i class="ti ti-calendar fs-5"></i>
                                                            </span>

                                                        </div>

                                                        </td>


                                                        <td>
                                                            <div class="progress progress-container"
                                                                data-uniqid="{{ $WorkAllocVal }}"
                                                                id="progress-container-{{ $WorkAllocVal }}"
                                                                data-min="{{ isset($Percent[$WorkAllocVal]) ? $Percent[$WorkAllocVal] : '0' }}"
                                                                align="center">
                                                                <div class="progress-bar bg-primary"
                                                                    id="progress-bar-{{ $WorkAllocVal }}"
                                                                    role="progressbar"
                                                                    style="width: {{ isset($Percent[$WorkAllocVal]) ? $Percent[$WorkAllocVal] : '0' }}%;"
                                                                    aria-valuenow="{{ isset($Percent[$WorkAllocVal]) ? $Percent[$WorkAllocVal] : '0' }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    {{ isset($Percent[$WorkAllocVal]) ? $Percent[$WorkAllocVal] : '0' }}%
                                                                </div>
                                                            </div>

                                                            <input type="hidden"
                                                                class="hiddenpercentagefield_{{ $WorkAllocVal }}"
                                                                value="{{ isset($Percent[$WorkAllocVal]) ? $Percent[$WorkAllocVal] : '0' }}"
                                                                name="percentage[{{ $WorkAllocVal }}]">
                                                        </td>

                                                        @php
                                                                $remark = isset($Remarks[$WorkAllocVal]) ? $Remarks[$WorkAllocVal] : '';
                                                            @endphp

                                                        <td>
                                                            <textarea class="form-control p-4" maxlength='300' name="remarks[{{ $WorkAllocVal }}]" id="remarks{{ $WorkAllocVal }}" cols="20" rows="1"  placeholder="Add Remarks..."></textarea>
                                                        </td>

                                                        <input type="hidden"   id="hiddenRemark{{ $WorkAllocVal }}" value="{{ isset($Remarks[$WorkAllocVal]) ? $Remarks[$WorkAllocVal] : '' }}"> 
                                                       
                                                        <?php $i++; ?> 

                                                        <?php endif; ?>
                                                    @endforeach
                                                </tr>

                                                @if ($subCategoryCount > 1)
                                                    <?php $j = 0; ?>
                                                    @foreach ($Workallocated_SubCategory[$workkey] as $subCategoryKey => $subCategory)
                                                        <?php
                                                                if ($j > 0):
                                                            ?>
                                                        @php
                                                            $workallocid = $WorkAllocationId[$workkey][$subCategoryKey];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                               <input type="text" class="form-control" value="{{ $subCategory }}" disabled>

                                                            </td>
                                                            <!-- From Date Selection -->
                                                            <td>
                                                           

                                                        
                                                                <input type="hidden" class="workallocationid"
                                                                    value="{{ $workallocid }}"
                                                                    name="workallocationid[{{ $workallocid }}]" />

                                                                <input type="hidden" class="auditdiaryid"
                                                                    value="{{ isset($AuditDiaryId[$workallocid]) ? $AuditDiaryId[$workallocid] : '' }}"
                                                                    name="auditdiaryid[{{ $workallocid }}]" />
                                                                <div class="input-group"
                                                                    onclick="datepicker('from_date','')">
                                                                    <input type="text"
                                                                        name="fromdate[{{ $workallocid }}]"
                                                                        id="fromdate{{ $workallocid }}"
                                                                        value="{{ isset($FromDate[$workallocid]) && $FromDate[$workallocid] != '' ? \Carbon\Carbon::parse($FromDate[$workallocid])->format('d/m/Y') : '' }}"
                                                                        autocomplete="off" class="form-control datepicker"
                                                                        placeholder="dd/mm/yyyy">
                                                                    <span class="input-group-text">
                                                                        <i class="ti ti-calendar fs-5"></i>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <!-- To Date Selection -->
                                                            <td>
                                                                <div class="progress progress-container"
                                                                    data-uniqid="{{ $workallocid }}"
                                                                    id="progress-container-{{ $workallocid }}"
                                                                    data-min="{{ isset($Percent[$workallocid]) ? $Percent[$workallocid] : '0' }}">
                                                                    <div class="progress-bar bg-primary"
                                                                        id="progress-bar-{{ $workallocid }}"
                                                                        role="progressbar"
                                                                        style="width: {{ isset($Percent[$workallocid]) ? $Percent[$workallocid] : '0' }}%;"
                                                                        aria-valuenow="{{ isset($Percent[$workallocid]) ? $Percent[$workallocid] : '0' }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                        {{ isset($Percent[$workallocid]) ? $Percent[$workallocid] : '0' }}%
                                                                    </div>
                                                                </div>
                                                                
                                                                <input type="hidden"
                                                                    class="hiddenpercentagefield_{{ $workallocid }}"
                                                                    value="{{ isset($Percent[$workallocid]) ? $Percent[$workallocid] : '0' }}"
                                                                    name="percentage[{{ $workallocid }}]">

                                                            </td>
                                                            <!-- Remarks TextAre -->
                                                            <td>
                                                                <textarea class="form-control p-4" maxlength='300' name="remarks[{{ $workallocid }}]" id="remarks{{ $workallocid }}" cols="20"
                                                                    rows="1" placeholder="Add Remarks..."></textarea>
                                                            </td>

                                                            <input type="hidden"  id="hiddenRemark{{ $workallocid }}" value="{{ isset($Remarks[$workallocid]) ? $Remarks[$workallocid] : '' }}"> 

                                                        </tr>
                                                        <!-- Increment $i after the first iteration -->
                                                        <?php endif; ?>
                                                        <?php $j++; ?>
                                                    @endforeach
                                                @endif
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>
                                <hr>
                                    <div class="row">
                                        <div class="col-md-2 mx-auto">
                                            <div class="d-flex align-items-center gap-6">
                                                <button type="submit" id="submitbtn"
                                                    class="btn btn-primary">Submit</button>

                                                <button type="button" id="finalize_plan" key="final_btn"
                                                    class="finalize_plan justify-content-center w-100 btn btn-rounded btn-success d-flex align-items-center lang">
                                                    Finalise
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger">Cancel</button>
                                            </div>

                                        </div>
                                    </div>
                                    <br>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
                <div class="card-header card_header_color">
                    Audit Diary Status
                </div>
            
                <div style="width:95%;margin:0 auto;" class="datatables">             
                    <div class="table-responsive" id="tableshow">
                        <br>
                        <table id="DiaryFetchTable"
                            class="table w-100  display  table-bordered text-nowrap datatables-basic schedulingtable">
                            <thead>
                                @csrf
                                <tr>
                                    <th>S.No</th>
                                    <th>Grouping</th>
                                    <th>Allocated Work</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Percentage</th>
                                    <th>History</th>

                                </tr>
                            </thead>
                        
                        </table> <br>
                    </div>
                </div>
        
                </div>
            </div>
        </div>
     
        @endif
        @endif

        </script>

        <script src="../../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="../../assets/js/download-button/buttons.min.js"></script>
        <script src="../../assets/js/download-button/jszip.min.js"></script>
        <script src="../../assets/js/download-button/buttons.print.min.js"></script>
        <script src="../../assets/js/download-button/buttons.html5.min.js"></script>
        <script src="../../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script>


// $(document).ready(function () {
//         $('.progress-container').each(function () {
//             const percent = parseInt($(this).data('min'));
//             const workallocid = $(this).data('uniqid');

//             if (percent === 100) {
//                 // Disable fromdate input
//                 $('#fromdate' + workallocid).prop('disabled', true);

//                 // Disable remarks textarea
//                 $('textarea[name="remarks[' + workallocid + ']"]').prop('disabled', true);
//             }
//         });
//     });



    $('.progress-container').each(function () {
        const percent = parseInt($(this).data('min'));
        const workallocid = $(this).data('uniqid');

        const $textarea = $('#remarks' + workallocid);
        const hiddenValue = $('#hiddenRemark' + workallocid).val();


        if (percent === 100) {
            $('#fromdate' + workallocid).prop('disabled', true);
            $textarea.val(hiddenValue); 
            $textarea.prop('disabled', true); 
        } else {
            $textarea.val(''); 
            $textarea.prop('disabled', false); 
        }
    });



$(document).on('click', '.finalize_plan', function () {
    var id = $('#schedule').val();
    var member = $('#member').val();

    if (!id) {
        alert("Please select a schedule before finalizing.");
        return;
    }

    showFinalizeConfirmation(); 

    $('#process_button').off('click').on('click', function (e) {
        const isVerified = $('#verified_radio').is(':checked');

        if (!isVerified) {
        $('#verified_error').show();
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

    $('#verified_error').hide();

    const modalEl = document.getElementById('confirmation_alert');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
        modalInstance.hide();
    }


        setTimeout(function () {
    passing_alert_value(
        'Confirmation Required',
        'Do you want to Finalize?',
        'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert'
    );
    $('#verified_radio_wrapper').hide();
}, 400);

    $('#process_button').off('click').on('click', function () {
        $.ajax({
            url: '/auditdiary/finalize',
            type: 'POST',
            data: {
                scheduleid: id,
                memberid: member,

                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    setTimeout(function () {

                    passing_alert_value(
                        'Success',
                        response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert'
                    );
                }, 400);


                    $('#ok_button').off('click').on('click', function () {
                        setTimeout(function () {
                            window.location.href = '/auditdiary_home'; 
                        }, 500);
                    });
                } else {
                    setTimeout(function () {

                    passing_alert_value(
                        'Error',
                        response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert'
                    );
                }, 400);

                            
                }
            },
            error: function() {
                alert('A server error occurred.');
            }
        });
    });
});

});


        function getLanguage(onchange) {
            let lang;

            if (onchange === 'Y') {
                lang = $('#translate').val();

            } else {

                lang = window.localStorage.getItem('lang') || 'en';
            }

            return lang === 'ta' ? 'ta' : 'en';
        }

             $(document).ready(function() {
                
                $('.progress-container').click(function(event) {
                    const minVal = parseInt($(this).data('min'), 10);
                    const uniqid = $(this).data('uniqid');
                    const containerWidth = $(this).width();
                    const clickPosition = event.offsetX;


                    let percentage = Math.round((clickPosition / containerWidth) * 100);

                    percentage = Math.max(minVal, Math.min(percentage, 100));

                    const progressBar = $(this).find('.progress-bar');
                    progressBar.width(percentage + '%');
                    progressBar.attr('aria-valuenow', percentage);
                    progressBar.text(percentage + '%');
                    $('.hiddenpercentagefield_' + uniqid + '').val(percentage);
                });
             });
             

            LoadDiaryTable();   
            function LoadDiaryTable()
            {
                 let language = getLanguage();
                if ($.fn.DataTable.isDataTable('#DiaryFetchTable')) {
                    $('#DiaryFetchTable').DataTable().clear().destroy();
                }

                let table = $('#DiaryFetchTable').DataTable({
                processing: true,
                serverSide: false,
                destroy: true,

            
                initComplete: function (settings, json) {
                    $("#DiaryFetchTable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>"
                    );
                },
                        ajax: {
                        url: "/auditdiary/fetchAllData",
                        type: 'POST',
                        data: function(d) {
                            d._token = '{{ csrf_token() }}';
                            d.schedule = $('input[name="schedule"]').val();
                            d.member = $('input[name="member"]').val();
                        },
                        dataSrc: 'data' 
                    },
                
                    columns: [
                        {
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
                                        data: "groupename",
                                        className: "d-none d-md-table-cell lang extra-column text-wrap"

                                },
                                {
                                    data: "majorworkallocationtypeename",
                                    className: "d-none d-md-table-cell lang extra-column text-wrap"

                                },
                                {
                                    data: "fromdate",
                                    render: function(data, type, row) {
                                        if (!data) return "-";

                                        const dateObj = new Date(data);
                                        if (isNaN(dateObj)) return "-";

                                        const day = String(dateObj.getDate()).padStart(2, '0');
                                        const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                                        const year = dateObj.getFullYear();

                                        return `${day}/${month}/${year}`;
                                    },
                                    className: "d-none d-md-table-cell lang extra-column text-wrap"
                                },


                                {
                                    data: "remarks",
                                    render: function(data, type, row) {
                                        return data ? data : "-";  // Fallback to 'No Data' if no data
                                    },
                                    className: "d-none d-md-table-cell lang extra-column text-wrap"

                                },
                                {
                                    data: "percentofcompletion",
                                    render: function(data, type, row) {
                                        // Check if percentofcompletion is null or undefined
                                        return data != null ? data + "%" : "0%";
                                    },
                                    className: "d-none d-md-table-cell lang extra-column text-wrap"

                                },
                                {
                                    data: "diaryid",
                                    title: columnLabels?.["actions"]?.[language],
                                    render: (data) =>
                                        `<center><a  class="btn btn-sm btn-secondary show-history-btn" data-id="${data}">  <i class="fas fa-clock"></i> View History</center>`,
                                    className: "text-center noExport"
                                }


                    ]

                
                });

                updatedatatable(language, "DiaryFetchTable"); 
            }

        
            $(document).on('click', '.show-history-btn', function () {
            const id = $(this).data('id');

            $('#historyTable tbody').html(`<tr><td colspan="4" class="text-center">Loading...</td></tr>`);

            $.ajax({
                url: '/auditdiary/history', 
                type: 'POST',
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content') // ✅ send CSRF token
                },
                success: function(response) {

                    const workallocationename = response.workallocationename || 'N/A';
                    const grouping = response.grouping || 'N/A';

                    $('#historyTopInfo').html(`
                        <div class="p-3 mb-3 bg-light border border-1 rounded">
                            <div class="row align-items-center">
                                <div class="col-md-6 d-flex">
                                    <span class="fw-bold text-dark me-2 fs-4">Grouping:</span>
                                    <span class="text-dark fs-4"><strong>${grouping}</strong></span>
                                </div>
                                <div class="col-md-6 d-flex">
                                    <span class="fw-bold text-dark me-2 fs-4">Allocated Work:</span>
                                    <span class="text-dark fs-4"><strong>${workallocationename}</strong></span>
                                </div>
                            </div>
                        </div>
                    `);

                    if (response.data && response.data.length > 0) {
                        let rows = '';
                        response.data.forEach(item => {
                            rows += `
                                <tr>
                                    <td>
                                    ${
                                        item.fromdate
                                        ? (() => {
                                            const date = new Date(item.fromdate);
                                            const day = String(date.getDate()).padStart(2, '0');
                                            const month = String(date.getMonth() + 1).padStart(2, '0');
                                            const year = date.getFullYear();
                                            return `${day}-${month}-${year}`;
                                            })()
                                        : '-'
                                    }
                                    </td>
                                    <td>${item.remarks || '-'}</td>
                                    <td>${item.percentofcompletion ? item.percentofcompletion + '%' : '-'}</td>

                                </tr>`;
                        });
                        $('#historyTable tbody').html(rows);
                    } else {
                        $('#historyTable tbody').html('<tr><td colspan="4" class="text-center">No history found.</td></tr>');
                    }
                },
        error: function() {
            $('#historyTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load history.</td></tr>');
        }
    });

    $('#historyModal').modal('show');
});


          

            // $(document).ready(function() {
            //     $('.datepicker').datepicker({
            //         format: 'dd-mm-yyyy',
            //         autoclose: true,
            //         startDate: new Date(),
            //     }).on('changeDate', function() {
            //         $(this).datepicker('hide');
            //     });
            // });


        //     $(document).ready(function() {
        //     $('.datepicker').datepicker({
        //         format: 'dd-mm-yyyy',
        //         autoclose: true,
        //         startDate: new Date(), 
        //         endDate: new Date(),   
        //         todayHighlight: true
        //     }).on('changeDate', function() {
        //         $(this).datepicker('hide');
        //     });
        // });



        $(document).on('click', '.datepicker', function () {
    const row = $(this).closest('tr');
    const workallocid = row.find('.workallocationid').val();

    const inputId = 'fromdate' + workallocid;
    const FromDate = $('#entrymeetdate').val();
    const ToDate = $('#exitmeetdate').val();

    datepicker(inputId, '', FromDate, ToDate);


});


function datepicker(inputId, setdate, FromDate, ToDate) {
    let minDate, maxDate;

    if (!ToDate) {
        let today = new Date();
        minDate = maxDate = today;
    } else {
        maxDate = new Date(ToDate);
        minDate = new Date(FromDate);

    }

    
    init_datepicker(inputId, minDate, maxDate, setdate, 'cleardateform');
}







function validateProgressFields() {
    let hasError = false;

    $('.progress-container').each(function () {
        const uniqid = $(this).data('uniqid');

        const remarksField = $('textarea[name="remarks[' + uniqid + ']"]');
        const dateField = $('input[name="fromdate[' + uniqid + ']"]');
        const progressField = $('.hiddenpercentagefield_' + uniqid);

        const oldProgress = $(this).data('min').toString().trim(); // old percentage
        const newProgress = progressField.val().trim();            // new percentage

        remarksField.removeClass('is-invalid');
        dateField.removeClass('is-invalid');
        progressField.removeClass('is-invalid');

        const remarks = remarksField.val().trim();
        const fromDate = dateField.val().trim();

        // ✅ Only validate if percentage actually changed
        if (newProgress !== oldProgress) {
            // If percentage changed, all fields must be filled
            if (!remarks || !fromDate || !newProgress || newProgress === "0") {
                hasError = true;
                if (!remarks) remarksField.addClass('is-invalid');
                if (!fromDate) dateField.addClass('is-invalid');
                if (!newProgress || newProgress === "0") progressField.addClass('is-invalid');
            }
        }
    });

    if (hasError) {
        $('#verified_error').hide();
        $('#verified_radio_wrapper').hide();
        passing_alert_value(
            'Alert',
            'Remarks and Date are required if Percentage is changed!',
            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert'
        );
        return false;
    }

    return true; // No errors
}






       
   $('#create_auditdiary').on('submit', function (e) {
     e.preventDefault(); 


   
    if (!validateProgressFields()) {
        return false;
    }
   

    passing_alert_value(
        'Confirmation Required',
        'Do you want to submit the details?',
        'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert'
    );
    


    $('#verified_error').hide();
    $('#verified_radio_wrapper').hide();

    $('#process_button').off('click').one('click', function () {

        $(this).prop('disabled', true);
        $('#submitbtn').prop('disabled', true);

        var formData = $('#create_auditdiary').serialize();


        $.ajax({
            url: '/audit_diary/insert',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    passing_alert_value(
                        'Success',
                        response.success,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert'
                    );

                    $('#ok_button').off('click').one('click', function () {
                        $(this).prop('disabled', false);
                        $('#submitbtn').prop('disabled', false);

                        location.reload();
                    });
                } else if (response.error) {
                    passing_alert_value(
                        'Alert',
                        response.error,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert'
                    );
                }
            },
            error: function (xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'An unknown error occurred';
                passing_alert_value(
                    'Alert',
                    errorMessage,
                    'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert'
                );
            }
        });
    });
});


function showFinalizeConfirmation() {
    passing_alert_value(
        'Confirmation',
        `Once finalized, this action cannot be revoked.<br>
        Please ensure all points are entered.<br><br>
        <strong>Do you want to proceed?</strong>`,
        'confirmation_alert', 'alert_header', 'alert_body', 'forward_alert'
    );

    setTimeout(function () {
    const footer = $('#confirmation_alert .modal-footer');

    footer.find('#verified_radio_wrapper').remove();

    $('#process_button').removeAttr('data-bs-dismiss');

    const radioHTML = `
    <div id="verified_radio_wrapper" style="display: flex; flex-direction: column; align-items: flex-start; margin-right: auto; gap: 4px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="verified_radio" name="verify_option" />
            <label for="verified_radio" style="margin: 0; font-weight: bold;">Verified</label>
        </div>
        <span id="verified_error" style="color: red; display: none; font-size: 0.875rem;">Please confirm by selecting "Verified".</span>
    </div>`;

    footer.prepend(radioHTML);

    $(document).off('change', '#verified_radio').on('change', '#verified_radio', function () {
            if ($(this).is(':checked')) {
                $('#verified_error').hide();
            }
        });


}, 100);

}


        function splitdate(dateString) {
            if (!dateString || typeof dateString !== 'string') {
                return '';
            }

            var dateParts = dateString.split('-');
            if (dateParts.length === 3) {
                return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // yyyy-mm-dd
            }

            return '';
        }

            function calculateDaysDifference(index) {
                var fromDate = splitdate($('#fromdate' + index + '').val());
                var toDate = splitdate($('#todate' + index + '').val());

                if (fromDate && toDate) {
                    var startDate = new Date(fromDate);
                    var endDate = new Date(toDate);

                    var timeDifference = endDate - startDate;

                    var dayDifference = (timeDifference / (1000 * 3600 * 24)) + 1;

                    $('#days-difference' + index + '').text(Math.abs(
                        dayDifference)); 
                    $('.daysdiffhidden' + index + '').val(Math.abs(dayDifference));
                }
            }

            $(document).on('change', '[id^=fromdate], [id^=todate]', function() {
                var index = $(this).attr('id').replace(/\D/g,
                    '');
                calculateDaysDifference(index);
            });
        </script>

    @endsection
