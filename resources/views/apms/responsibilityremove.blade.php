@section('content')
    @extends('index2')
    @include('common.alert')
    @include('apms.apmsalert')


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
    </style>
    <style>
        /* Ensure retirement para checkboxes use red (Bootstrap danger) instead of default blue */
        input.para-checkbox {
            accent-color: #1a46be;
            /* modern browsers */
        }

        /* Fallback styling for older browsers */
        input.para-checkbox:checked {
            background-color: #1a46be;
            border-color: #1a46be;
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


    <div class="card mt-3 border border-info">
        <div class="card-header card_header_color">
            Retirement Para Details
        </div>

        <div class="card-body">

            <!-- TABLE -->
            <div class="table-responsive" id="tableshow">
                <table id="removeparadetails" class="table table-striped table-bordered w-100 text-nowrap datatables-basic">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th class="w-50">Slip Details</th>
                            <th>Para Number</th>
                            <th>Liabilty</th>
                            <th>Action</th>
                            <th class="text-center">View</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- NO DATA -->
            <div id="no_data" class="text-center text-muted py-2 d-none">

                No Data Available
            </div>

            <!-- BUTTONS -->
            <div class="row mt-4 d-none" id="button_shown">
                <div class="col-md-12 text-center">

                    {{-- <button type="button" class="btn btn-danger me-2" id="reset_button">
                        Clear
                    </button> --}}

                    <button type="button" class="btn button_save me-2" id="save_draft_btn">
                        Save Draft
                    </button>

                    <button type="button" class="btn btn-success" id="finalize_btn">
                        <i class="fas fa-check-circle me-1"></i> Finalize
                    </button>

                </div>
            </div>

        </div>


        <div id="ajax-loader">
            <div class="spinner"></div>
        </div>



        <style>
            /* Overlay */
            #ajax-loader {
                display: none;
                /* Hidden by default */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.6);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }

            /* Spinner */
            #ajax-loader .spinner {
                width: 50px;
                height: 50px;
                border: 5px solid rgba(0, 123, 255, 0.3);
                /* Light blue border */
                border-top: 5px solid #007bff;
                /* Solid blue top */
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <style>
            .field-error {
                color: #dc3545;
                font-size: 11px;
                margin-top: 4px;
                display: block;
            }

            .has-error select,
            .has-error input {
                border-color: #dc3545;
            }
        </style>



       
        <script src="../assets/js/vendor.min.js"></script>
        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- solar icons -->
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        {{-- data table --}}
        <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


        <script src="../assets/js/datatable/datatable-advanced.init.js"></script>

        <script>
            let dataFromServer = [];

            $(document).ready(function () {
                fetch_removalliabilty();
            });

            function fetch_removalliabilty() {

                $.ajax({
                    url: "{{ url('/responsibilityremove') }}",
                    type: "GET",
                    dataType: "json",
                    beforeSend: function () {
                        $('#ajax-loader').show();
                    },
                    success: function (response) {

                        console.log(response);

                        if (response.success && response.data.length > 0) {

                            dataFromServer = response.data;
                            actionList = response.actions;

                            $('#tableshow').show();
                            $('#no_data').hide();

                            renderTable();

                        } else {
                            $('#tableshow').hide();
                            $('#no_data').show();
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    },
                    complete: function () {
                        $('#ajax-loader').hide();
                    }
                });
            }

            function renderTable() {

                if ($.fn.DataTable.isDataTable('#removeparadetails')) {
                    $('#removeparadetails').DataTable().clear().destroy();
                }

                $('#removeparadetails').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    data: dataFromServer,
                   columns: [
    {
        data: null,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
   // {
     //   data: "slipdetails",
      //  render: function (data) {
      //      return data ?? '-';
      //  }
   // },
 {
                    data: "slipdetails",
                    // title: columnLabels?.["slipdetails"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.slipdetails || '-';
                    }
                },
    {
        data: "paranumber",
        render: function (data) {
            return data ?? '-';
        }
    },
    {
        data: "auditee_liability",
        render: function (data) {
            return data === "Y" ? "Yes" : "No";
        }
    },
    {
        data: "actioncode",
        render: function (data, type, row) {

            let dropdown = `<select 
                class="form-select form-select-sm action-dropdown"
                data-id="${row.paraid}"
                disabled>`;

            actionList.forEach(function (action) {

                let selected = (action.actioncode === data) ? "selected" : "";

                dropdown += `<option value="${action.actioncode}" ${selected}>
                                ${action.actionename}
                             </option>`;
            });

            dropdown += `</select>`;

            return dropdown;
        }
    },
           

 {
    data: "paraid",
    render: function (data, type, row) {

        let downloadBtn = "";

        // ✅ Show Download only if actioncode == 11
        if (row.actioncode == 11 && row.statusflag == 'C') {
            downloadBtn = `
                <a href="/para-download/${data}" 
                    class="btn btn-sm btn-success"
                    style="font-size:10px">
                    <i class="ti ti-download fs-2 me-1"></i>Download
                </a>
            `;
        }

        return `
            <button type="button" 
                class="btn btn-sm btn-info me-1"
                onclick="get_para_historydetails(this,'view_para')"
                data-para="${data}"
                style="font-size:10px">
                <i class="ti ti-clipboard-data fs-2 me-1"></i>Para Details
            </button>

            ${downloadBtn}
        `;
    }
}

 ]

                });
            }


            function get_para_historydetails(id, param) {
                const paraid = id.dataset.para;
                console.log(paraid);

                $.ajax({
                    url: "/fetch_para_datasforremoval",
                    type: "POST",


                    data: {
                        paraid: paraid,
                        param: param
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var para_det = response.data;
                        var prarahistory_det = response.parahistory;
                        var liability_det = response.liability;

                        // console.log('API response', prarahistory_det);

                        // If server responded with an error message but still returned liability array, allow processing.
                        if (response.success === false && (!liability_det || liability_det.length === 0)) {
                            alert(response.message || 'Server error');
                            return;
                        }

                        if (param == 'view_flow') {

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
                            passing_extra_large_alert('Para Flow', datacontent,
                                'extra_large_confirmation_alert',
                                'extra_large_alert_header',
                                'extra_large_alert_body', 'confirmation_alert', 'send_intimation_label');
                            setTimeout(() => {
                                populatTable(para_det);
                            }, 100);

                        } else if (param == 'view_para') {
                            // console.log(para_det);

                            populate_parafulldetails(lang, para_det, prarahistory_det, 'parafull_details',
                                'parafull_det',
                                'parafull_dettable')

                        }

                    }
                });
            }
        </script>
@endsection
