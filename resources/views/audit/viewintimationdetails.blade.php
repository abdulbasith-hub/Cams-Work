 @php
     $quarter_det = json_decode(json_encode($quarter_det));
 @endphp

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

         @media (max-width: 768px) {

             #accepted .table-responsive table,
             #accepted .table-responsive thead,
             #accepted .table-responsive tbody,
             #accepted .table-responsive th,
             #accepted .table-responsive td,
             #accepted .table-responsive tr {
                 display: block;
                 width: 100%;

             }

             #accepted .table-responsive td {
                 display: right;
                 justify-content: space-between;
                 padding: 8px;
                 border-bottom: 1px solid #ddd;

             }

             #accepted .table-responsive td:before {
                 content: attr(data-label);
                 font-weight: bold;
                 flex-basis: 50%;
             }

         }
     </style>
     <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
     <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
     <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
     <div class="col-12">
         <div class="card">
             <div class="card-header card_header_color lang" key="auditquarter">Audit Quarter</div>
             <div class="card-body">
                 <div class="col-md-4 mb-1 mx-auto">
                     <label class="form-label lang required" key="auditquarter">Audit Quarter</label>

                     <select class="form-select mr-sm-2" id="quartercode" name="quartercode"
                         onchange="fetch_auditeeIntimation()">



                         @foreach ($quarter_det as $quat)
                             @php
                                 $fyText = $quat->planname . ' - ( ' . $quat->financialyear . ' )';
                             @endphp

                             <option value="{{ $quat->planmappingid }}" data-instename="{{ $fyText }}"
                                 data-insttname="{{ $fyText }}">
                                 {{ $fyText }}
                             </option>
                         @endforeach


                     </select>
                 </div>
             </div>
         </div>
     </div>

     <div class="card " style="border-color: #7198b9">
         <div class="card-header card_header_color lang" key="auditee_stat_det">Auditee Intimation Details</div>
         <div class="card-body">
             <div class="datatables">
                 <div class="table-responsive hide_this" id="tableshow">
                     <table id="audit_plandetails"
                         class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                         <thead>
                             @csrf
                             <tr>
                                 <th class="lang" key="s_no">S.No</th>
                                 <th class="text-wrap" key="instname_label">Institute</th>
                                 <th class="text-wrap" key="date_label"> Date</th>
                                 <!-- <th class="text-wrap" key="teamname">Audit Team</th> -->
                                 <th class="text-wrap" key="audityear">Audit Year</th>
                                 <th class="text-wrap" key="nodal_details">Nodel Person Details</th>
                                 <!-- <th class="text-wrap" key="nodal_contact">Nodel Person Contact Details</th> -->
                                 <th class="all lang text-wrap" key="auditeeresponse">Status</th>
                                 <th class="all lang text-wrap text-center" key="action">Action</th>
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

     <div id="reschedule_modal" class="modal fade" tabindex="-1" aria-labelledby="reschedule_modal modalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable modal-lg">
             <div class="modal-content">
                 <div class="modal-header modal-colored-header bg-success text-white">
                     <h4 class="modal-title text-white" id="success-header-modalLabel">
                         Audit Reschedule
                     </h4>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                         aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <form>
                         <div class="row">
                             <div class="col-md-12">
                                 <input type="hidden" id="Reschedule_auditschid" />
                                 <input type="hidden" id="Reschedule_auditplanid" />
                                 <input type="hidden" id="Reschedule_usrid" />
                                 <input type="hidden" id="Enc_auditschid" />

                                 <div class="mb-12">
                                     <label for="message-text" class="">Reschedule Remarks</label>
                                     <textarea class="form-control" id="Reschedule_remarks" name="Reschedule_remarks" style="height:120px"></textarea>
                                 </div>
                             </div>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-primary Reschedule_btn" data-bs-dismiss="modal">
                         Audit Reschedule
                     </button>
                     <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                         Close
                     </button>

                 </div>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>

     <div id="cancel_modal" class="modal fade" tabindex="-1" aria-labelledby="cancel_modal modalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable modal-lg">
             <div class="modal-content">
                 <div class="modal-header modal-colored-header bg-success text-white">
                     <h4 class="modal-title text-white" id="success-header-modalLabel">
                         Cancel Schedule
                     </h4>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                         aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <form>
                         <div class="row">
                             <div class="col-md-12">
                                 <input type="hidden" id="cancel_auditschid" />
                                 <div class="mb-12">
                                     <label for="message-text" class="">Cancel Remarks</label>
                                     <textarea class="form-control" id="cancel_remarks" name="cancel_remarks" style="height:120px"></textarea>
                                 </div>
                             </div>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-primary cancelsch_btn" data-bs-dismiss="modal">
                         Cancel Schedule
                     </button>
                     <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                         Close
                     </button>

                 </div>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>
     <div id="date-changed" class="modal fade" tabindex="-1" aria-labelledby="date-changed modalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable modal-lg">
             <div class="modal-content">
                 <div class="modal-header modal-colored-header bg-success text-white">
                     <h4 class="modal-title text-white" id="success-header-modalLabel">
                         Auditee Reply For Intimation
                     </h4>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                         aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <form>
                         <div class="row">
                             <div class="col-md-4">
                                 <label class="form-label" for="validationDefault02">Date</label>
                                 <div class="input-group" onclick="datepicker('change_date','')">
                                     <input type="text" class="form-control datepicker" id="change_date"
                                         name="change_date" placeholder="dd/mm/yyyy" disabled />
                                     <span class="input-group-text">
                                         <i class="ti ti-calendar fs-5"></i>
                                     </span>
                                 </div>
                             </div>
                             <div class="col-md-8">

                                 <div class="mb-3">
                                     <label for="message-text" class="">Remarks</label>
                                     <textarea class="form-control" id="part_remarks" name="part_remarks" disabled style="height:20px">The audit planning has not be scheduled according to the Government Order</textarea>
                                 </div>
                             </div>
                         </div>


                     </form>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                         Edit
                     </button>
                     <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                         Close
                     </button>
                     {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                 </div>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>








     <div id="accepted" class="modal fade" tabindex="-1" aria-labelledby="accepted modalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable modal-xl">
             <div class="modal-content">
                 <div class="modal-header modal-colored-header bg-success text-white">
                     <h4 class="modal-title text-white lang" id="success-header-modalLabel"
                         key="account_particulars_label">
                         Auditee Reply For Intimation
                     </h4>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                         aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <form>
                         <h5 class="mt-2 lang" key="audit_particulars_label">Audit Particulars</h5>
                         <div class="table-responsive rounded-4">
                             <table class="table table-bordered border-dark">
                                 <tbody id="part_details"></tbody>
                             </table>

                         </div>


                     </form>
                 </div>
                 <div class="modal-footer">
                     {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Edit
                    </button> --}}
                     <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" key="cancelbtn">
                         Close
                     </button>
                     {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                 </div>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>
     </div>
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
         function datepicker(value, setdate) {
             var today = new Date();
             if (value == 'change_date') {
                 // Calculate the minimum date (18 years ago)
                 var maxDate = new Date(today);
                 maxDate.setMonth(today.getMonth() + 4);

                 // Calculate the maximum date (60 years ago)
                 var minDate = today;
             }

             var minDateString = formatDate(minDate); // Format date to dd/mm/yyyy
             var maxDateString = formatDate(maxDate); // Format date to dd/mm/yyyy

             init_datepicker(value, minDateString, maxDateString, setdate)
         }
         $('#translate').change(function() {
             const lang = getLanguage('Y'); // Store language selection
             updateTableLanguage(
                 lang); // Update the table with the new language by destroying and recreating it

         });

         function renderTable(language, datafromServer) {
             const InstituteName = language === 'ta' ? 'insttname' : 'instename';


             if ($.fn.dataTable.isDataTable('#audit_plandetails')) {
                 $('#audit_plandetails').DataTable().clear().destroy();
             }
             var table = $('#audit_plandetails').DataTable({
                 "processing": true,
                 "serverSide": false,
                 "lengthChange": false,

                 // "scrollX": true,
                 "initComplete": function(settings, json) {
                     $("#audit_plandetails").wrap(
                         "<div style='overflow:auto; width:100%;position:relative;'></div>");
                 },
                 "data": datafromServer,

                 columns: [{
                         data: "index",
                         render: function(data, type, row, meta) {
                             return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                         },
                         className: 'text-end text-wrap',
                         type: "num"
                     },
                     {
                         data: InstituteName,
                         render: function(data) {
                             return data || '-';
                         },
                         className: 'd-none d-md-table-cell lang extra-column text-wrap'
                     },
                     {
                         "data": "null",
                         "render": function(data, type, row) {
                             // Convert DOB to dd-mm-yyyy format
                             let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
                                     'en-GB') :
                                 "N/A";
                             let todate = row.todate ? new Date(row.todate).toLocaleDateString(
                                     'en-GB') :
                                 "N/A";

                             return ` ${fromdate} - ${todate}`;
                         },
                         className: "d-none d-md-table-cell lang extra-column text-wrap"
                     },
                     // {
                     //     data: "teamname",
                     //     render: function(data) {
                     //         return data || '-';
                     //     },
                     //     className: "d-none d-md-table-cell lang extra-column text-wrap"
                     // },
                     {
                         data: "yearname",
                         render: function(data) {
                             return data || '-';
                         },
                         className: "d-none d-md-table-cell lang extra-column text-wrap"
                     },
                     // {
                     //     data: "nodalperson_details",
                     //     render: function(data) {
                     //         return data || '-';
                     //     },
                     //     className: "d-none d-md-table-cell lang extra-column text-wrap"
                     // },
                     {
                         data: "nodalperson_contact",
                         render: function(data, type, row) {
                             return `${row.nodalperson_details}<br>${row.nodalperson_contact}`;

                         },
                         className: "d-none d-md-table-cell lang extra-column text-wrap"
                     },
                     {
                         "data": "rcno",
                         "render": function(data, type, row) {
                             let userid = row.userid
                             const acceptedBadge = language === 'ta' ? 'ஏற்றுக்கொள்ளப்பட்டது' : 'Accepted';
                             const part_accepted = language === 'ta' ? 'ஓரளவு ஏற்றுக்கொள்ளப்பட்டது' :
                                 'Pending';



                             if (row.auditeeresponse === 'A') {
                                 return `<center>
                        <button type="button " class="btn btn-success ">
                                                  ${acceptedBadge}
                                                </button>
                    </center>`;
                             }

                             if (row.auditeeresponse === 'R' || row.auditeeresponse === '' ||
                                 row.auditeeresponse === null) {
                                 return `<center>
                        <button type="button " class="btn btn-primary ">
                                                   ${part_accepted}
                                                </button>
                    </center>`;
                             }


                         },
                         className: "d-none d-md-table-cell lang extra-column text-wrap"
                     },
                     {
                         "data": "encrypted_auditscheduleid", // Use the encrypted deptuserid
                         "render": function(data, type, row) {
                             let userid = row.userid
                             let statusflag = row.workallocationflag;
                             const randomWork = language === 'ta' ? 'வேலை ஒதுக்கீட்டை சீரற்றதாக்குங்கள்' :
                                 ' Randomize Work Allocation';
                             const viewStatus = language === 'ta' ? 'நிலையைப் பார்க்கவும்' : 'View Status';
                             const showWork = language === 'ta' ? 'ஒதுக்கப்பட்ட வேலையைக் காட்டு' :
                                 'Show  Allocated Work';
                             const Pending = language === 'ta' ? 'Pending' : 'Pending';
                             if (row.auditeeresponse === 'A') {
                                 if (statusflag == null || statusflag == 'N') {
                                     return `<center>
                                                
                                                 <button type="button"  class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-primary d-flex align-items-center"
                                                                            onclick='acceptstatus("${data}")'>
                                                                            <i class="ti ti-inbox fs-4 me-2"></i>
                                                                            ${viewStatus}
                                                </button>
                                        </center>`;
                                 } else if (statusflag == 'Y') {
                                     return `  <center>  <button type="button" class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-dark d-flex align-items-center"
                                                        onclick="redirectToPage('${data}', 'view_workallocated')">
                                                        <i class="ti ti-briefcase fs-4 me-2"></i>
                                                          ${showWork}
                                                </button>
                                                 <button type="button"  class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-primary d-flex align-items-center"
                                                                            onclick='acceptstatus("${data}")'>
                                                                            <i class="ti ti-inbox fs-4 me-2"></i>
                                                                          ${viewStatus}
                                                </button>
                                        </center>`;
                                 } else {
                                     return `<center><button type="button"  class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-primary d-flex align-items-center"
                                                                            data-bs-toggle="modal" data-bs-target="#date-changed" onclick='acceptstatus("${data}")'>
                                                                            <i class="ti ti-inbox fs-4 me-2"></i>
                                                                            ${viewStatus}
                                                                        </button>
                                        </center>`;
                                 }


                             } else if (row.auditeeresponse === 'R') {
                                 return `<center><button type="button"  class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-primary d-flex align-items-center"
                                                                            data-bs-toggle="modal" data-bs-target="#date-changed" onclick='acceptstatus("${data}")'>
                                                                            <i class="ti ti-inbox fs-4 me-2"></i>
                                                                            ${viewStatus}
                                                                        </button>
                                        </center>`;
                             } else {
                                 return `<center><button type="button"  class="justify-content-center w-100 btn mb-1 btn-rounded btn-outline-primary d-flex align-items-center"
                                                                            >
                                                                            <i class="ti ti-inbox fs-4 me-2"></i>
                                                                            ${Pending}
                                                                        </button>
                                        </center>`;
                             }
                         },
                         className: "text-end text-wrap"
                     }

                 ],

             });
             const mobileColumns = ["instename", "yearname", "nodalperson_details",
                 "nodalperson_contact", "auditeeresponse"
             ];
             setupMobileRowToggle(mobileColumns);
             updatedatatable(language, "audit_plandetails");
         }

         function updateTableLanguage(language) {
             if ($.fn.DataTable.isDataTable('#audit_plandetails')) {
                 $('#audit_plandetails').DataTable().clear().destroy();
             }
             renderTable(language, dataFromServer);
         }
         let dataFromServer;

         function fetch_auditeeIntimation(language) {
             var quartercode = $('#quartercode').val();
             $.ajax({
                 url: "/audit/auditee_intimation", // Your API route for fetching data
                 type: "POST",
                 data: {
                     planmappingid: quartercode,

                 },
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                         'content') // Pass CSRF token in headers
                 },

                 success: function(json) {
                     if (json.data && json.data.length > 0) {
                         $('#tableshow').show();
                         $('#saudit_plandetails_wrapper').show();
                         $('#no_data').hide(); // Hide custom "No Data" message
                         dataFromServer = json.data;

                         renderTable(language, dataFromServer);
                     } else {
                         $('#tableshow').hide();
                         $('#audit_plandetails_wrapper').hide();
                         $('#no_data').show();
                     }
                 }


             });

         }
         $(document).ready(function() {
             const lang = getLanguage('')
             fetch_auditeeIntimation(lang)


             $('.cancelsch_btn').on('click', function() {
                 var cancel_remarks = $('#cancel_remarks').val(); // Get the value of the textarea
                 var auditschid = $('#cancel_auditschid').val();
                 $.ajax({
                     url: 'audit/cancelorreschedule', // Replace with your endpoint
                     method: 'POST',
                     data: {
                         Remarks: cancel_remarks, // Pass the 'catcode' to the controller
                         scheduleid: auditschid,
                         statusflag: 'C'
                     },
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                             'content') // Pass CSRF token in headers
                     },
                     success: function(response) {
                         var confirmation = 'Audit Schedule cancelled successfully!';
                         passing_alert_value('Alert', confirmation, 'confirmation_alert',
                             'alert_header', 'alert_body', 'confirmation_alert');
                         table.ajax.reload();

                     },
                     error: function() {
                         alert("Failed to fetch team members!");
                     }
                 });
             });


             $('.Reschedule_btn').on('click', function() {
                 var reschedule_remarks = $('#Reschedule_remarks').val(); // Get the value of the textarea
                 var auditschid = $('#Reschedule_auditschid').val();
                 var Reschedule_auditplanid = $('#Reschedule_auditplanid').val();
                 var Reschedule_usrid = $('#Reschedule_usrid').val();
                 var Enc_auditschid = $('#Enc_auditschid').val();

                 var statusflag = 'R';
                 $.ajax({
                     url: 'audit/cancelorreschedule', // Replace with your endpoint
                     method: 'POST',
                     data: {
                         Remarks: reschedule_remarks, // Pass the 'catcode' to the controller
                         scheduleid: auditschid,
                         statusflag: statusflag
                     },
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                             'content') // Pass CSRF token in headers
                     },
                     success: function(response) {
                         passing_alert_value('Alert', response, 'confirmation_alert',
                             'alert_header', 'alert_body', 'confirmation_alert');
                         // table.ajax.reload();

                         window.location.href = '/audit_datefixing?auditplanid=' +
                             Reschedule_auditplanid + '&userid=' + Reschedule_usrid +
                             '&encschid=' + auditschid + '&status=' + statusflag;


                     },
                     error: function() {
                         alert("Failed to fetch team members!");
                     }
                 });
             });


         });

         function viewStatus(rowData) {
             $('#part_remarks').val(rowData.auditeeremarks);

             datepicker('change_date', convertDateFormatYmd_ddmmyy(rowData.auditeeproposeddate));

         }

         function CancelModal(rowData) {
             $('#cancel_auditschid').val(rowData.auditscheduleid);
             $('#cancel_remarks').val(' ');

         }

         function RescheduleModal(rowData) {
             $('#Reschedule_auditschid').val(rowData.auditscheduleid);
             $('#Reschedule_auditplanid').val(rowData.encrypted_auditplanid);
             $('#Enc_auditschid').val(rowData.encrypted_auditscheduleid);


             $('#Reschedule_usrid').val(rowData.userid);
             $('#Reschedule_remarks').val(' ');

             // window.location.href = '/audit_datefixing?auditplanid=' + rowData.encrypted_auditplanid + '&userid=' + rowData.userid;

         }

         function redirectToPage(audit_scheduleid, targetPage) {

             window.location.href = `${targetPage}?audit_scheduleid=${encodeURIComponent(audit_scheduleid)}`;
         }

         function acceptstatus(auditscheduleid) {
             var auditscheduleid = auditscheduleid;
             $.ajax({
                 url: 'audit/auditee_acceptdetails', // The route to call your controller method
                 method: 'POST',
                 data: {
                     auditscheduleid: auditscheduleid // Passing the auditplanid from the button's id
                 },
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                         'content') // CSRF token for security
                 },
                 success: function(response) {
                     // alert(response);
                     populateTable(response)
                     // if (response.success) {
                     //     // Handle the success case (you can redirect, update UI, etc.)
                     //     alert("Data loaded successfully.");
                     //     // You can use response data to update your UI dynamically
                     // } else {
                     //     alert("Failed to load data.");
                     // }
                 },
                 error: function(xhr, status, error) {
                     // Handle error
                     console.log("AJAX error: " + error);
                 }
             });
         }

         function changeLanguage(lang) {

             $(".lang").each(function() {
                 let key = $(this).attr("key");
                 if (arrLang[lang][key] && arrLang[lang][key]) {
                     $(this).text(arrLang[lang][key]);
                 }
             });


         }

         function populateTable(response) {
             const lang = getLanguage('');
             const tableBody = $('#part_details'); // Select the table's tbody
             tableBody.empty(); // Clear existing rows

             const data = response.data;
             const cfr = response.cfr;

             const auditeeuserdetails = response.auditeeuserdetails;

             const auditeeUsers = auditeeuserdetails.original.fetch_auditeeofficeusers;

             const auditee_exists = auditeeuserdetails.original.exists;
             // changeLanguage(lang)
             // Grouping data for Account Particulars
             const accountParticulars = data.reduce((acc, item) => {
                 if (!acc[lang === 'ta' ? item.accountparticularstname : item.accountparticularsename]) {
                     acc[lang === 'ta' ? item.accountparticularstname : item.accountparticularsename] = [];
                 }
                 acc[lang === 'ta' ? item.accountparticularstname : item.accountparticularsename].push(item);
                 return acc;
             }, {});


             const accountTotalRows = Object.values(accountParticulars).reduce((sum, group) => sum + group.length, 0);
             // Start building the table HTML
             let tableHTML = '';

             // Account Particulars Section
             tableHTML += `<tr>
                                <th rowspan="${accountTotalRows + 2}" class="lang" key="account_particulars_label">Account Particulars</th>
                            </tr>
                            <tr>
                                <th class="callforrecords_th lang" key="type">Type</th>
                                <th class="callforrecords_th ressts lang" key="avail_of_records">Availability of Records</th>
                                <th class="callforrecords_th">
                                    <div>
                                        <label class="form-label lang" key="file_upload" for="validationDefault01">File Upload&nbsp;&nbsp;<Label>
                                    </div>
                                </th>
                                <th class="callforrecords_th">
                                    <div>
                                        <label class="form-label lang" key="remarks">Remarks</label>
                                    </div>
                                </th>
                            </tr>`;

             for (const [particularName, particulars] of Object.entries(accountParticulars)) {
                 particulars.forEach((particular) => {
                     const isFileUploaded = particular.fileuploadid !== 0;
                     const fileDetailsString = particular.filedetails;
                     const fileDetailsArray = fileDetailsString.split(
                         ',');

                     const fileCardsHTML = fileDetailsArray.map((fileDetail, index) => {
                         const [name, path, size, fileuploadid] = fileDetail.split('-'); // Split by hyphen

                         const file = {
                             id: index + 1, // Use index+1 as unique ID for the file
                             name: name,
                             path: path,
                             size: size,
                             fileuploadid: fileuploadid,
                         };

                         return isFileUploaded ?
                             `<div class=" overflow-hidden" id="file-card-${file.id}">
                        <input type="hidden" id="fileuploadid_${file.id}" name="fileuploadid_${file.id}" value="${file.fileuploadid}">
                        <div class="d-flex flex-row">
                            <div class="p-2 align-items-center">
                                <h3 class="text-danger box mb-0 round-56 p-2">
                                    <i class="ti ti-file-text"></i>
                                </h3>
                            </div>
                            <div class="p-3">
                                <h3 class="text-dark mb-0 fs-3">
                                    <a style="color:black" href="/${file.path}" target="_blank">${file.name}</a>
                                </h3>

                            </div>
                        </div>
                    </div>` :
                             `<div class=""></div>`;
                     }).join('');

                     tableHTML += `
                <tr>
                    <td>${lang=='ta'?particular.accountparticularstname:particular.accountparticularsename}
                        <input type="hidden" id="${particular.accountparticularsid}-cfrcode" name="${particular.accountparticularsid}-cfrcode" value="${particular.accountparticularsid}">
                    </td>
                    <td>${isFileUploaded ? lang=='ta'? 'கிடைக்கும்':'Available' :lang=='ta'?'கிடைக்கவில்லை': 'Not Available'}</td>

                      <td>${fileCardsHTML}</td>
                    <td>
                        <textarea id="${particular.accountparticularsid}" name="${particular.accountparticularsid}-cfrvalues" class="form-control" placeholder="Enter remarks" disabled style ="height:20px">${particular.remarks || ''}</textarea>
                    </td>
                </tr>`;
                 });
             }

             // Call for Records Section
             tableHTML += `<tr style="height:30px;"></tr><tr>
                                 <th rowspan="${cfr.length+ 2}" class="lang" key="cfr_head" >Call For Records</th>
                              </tr>
                              <tr>
                                 <th colspan="2" class="callforrecords_th lang" key="type">Type</th>
                                 <th class="callforrecords_th ressts lang" key="avail_of_records">Availability of Records </th>
                                 <th class="callforrecords_th lang" key="remarks">Remarks</th>
                              </tr>`;


             $.each(cfr, function(index, record) {
                 // Determine the value to display based on the language
                 var callForRecordsName = (lang === 'ta') ?

                     record.callforrecordstname :
                     record.callforrecordsename;

                 const isReplyPending = record.replystatus !== 'Y';

                 tableHTML += `
                            <tr>
                                <td colspan="2" >${callForRecordsName}</td>
                                <td>
                                   ${isReplyPending ?
                                   lang=='ta'? 'கிடைக்கவில்லை':'Not Available' :lang=='ta'?'கிடைக்கும்': ' Available'
                                  }
                                </td>
                                <td style="padding:10px;">
                                    <textarea id="${record.callforrecordsid}" name="${record.callforrecordsid}-cfrvalues" class="form-control" placeholder="Enter remarks" disabled style="height: 20px;">${record.cfr_remarks || ''}</textarea>

                                </td>
                            </tr>`;
             });




             // Nodal Person Section
             tableHTML += `
         <tr style="height:30px;"></tr><tr>
            <th class="lang" key="nodal_person"> Nodal Person</th>
            <td colspan="4">
                <div class="row">
                    <div class="col-md-6">
                       <label class="form-label lang" for="nodal_name" key="name">Name</label>
                       <input type="text" class="form-control " id="nodalname" name="nodalname" value="${data[0].nodalname || ''}" disabled placeholder="Enter Name"  />
                    </div>
                    <div class="col-md-6">
                       <label class="form-label lang" for="mobile" key="mobile">Mobile Number</label>
                       <input type="text" class= "form-control only_numbers" id="nodalmobile" value="${data[0].nodalmobile || ''}" disabled name="nodalmobile" placeholder="Enter Mobile Number" maxlength = 10 />
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label lang" for="" key="email">Email</label>
                        <input type="text" class="form-control" id="nodalemail" value="${data[0].nodalemail || ''}" disabled name="nodalemail" placeholder="Enter Email"  />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label lang" for="mobile" key="designation">Designation</label>
                        <input type="text" class="form-control" id="nodaldesignation" value="${data[0].nodaldesignation || ''}" disabled name="nodaldesignation" placeholder="Enter Designation"  />
                    </div>
                </div><br>
            </td>
            </tr>
            <tr>
                <th class="lang" key="remarks">Remarks</th>
                <td colspan="4">
                    <label class="form-label lang" key="remarks" for="auditee_remarks">Remarks</label>
                    <textarea id="auditee_remarks" name="auditee_remarks" class="form-control" disabled style ="height:20px">${data[0].auditeeremarks || ''}</textarea><br>
                </td>
            </tr>`;

             if (auditee_exists == 1) {


                 var length = auditeeUsers.length + 3;

                 tableHTML += `<tr style="height:30px;"></tr><tr>
                            <th rowspan="${length}" class="lang" key="audituserdetails_label">Auditee User Details</th>  <!-- Call For Records title with rowspan -->
                        </tr>
                        <tr>
                            <th class="callforrecords_th lang" key="s_no" rowspan="2">S NO</th> <!-- Name column -->
                            <th class="callforrecords_th lang" key="name" rowspan="2">Name</th> <!-- Name column -->
                            <th class="callforrecords_th lang ressts" key="designation" rowspan="2">Designation</th> <!-- Designation column -->
                            <th class="callforrecords_th lang"  key="serviceperiod" colspan="2" >Service Period</th> <!-- "Service Period" with rowspan -->
                        </tr>
                        <tr>
                            <th class="callforrecords_th lang" key="from_date">From Date</th> <!-- From Date column -->
                            <th class="callforrecords_th lang" key="to_date">To Date</th> <!-- To Date column -->
                        </tr>`;


                 if (auditeeUsers && auditeeUsers.length > 0) {
                     $.each(auditeeUsers, function(index, user) {
                         let service_fromdate = user.service_fromdate ? convertDateFormatYmd_ddmmyy(user
                             .service_fromdate) : '-';
                         let service_todate = user.service_todate ? convertDateFormatYmd_ddmmyy(user
                             .service_todate) : '-';
                         // Create a table row for each user
                         tableHTML += '<tr>' +
                             '<td>' + (parseInt(index) + 1) + '</td>' +
                             '<td>' + (user.ofc_username ? user.ofc_username : "-") + '</td>' + // Username
                             '<td>' + (user.ofc_designation ? user.ofc_designation : "-") + '</td>' + // Designation
                             '<td>' + (service_fromdate ? service_fromdate : '-') + '</td>' + // Service From Date
                             '<td>' + (service_todate ? service_todate : '-') + '</td>' + // Service To Date
                             '</tr>';
                     });

                 }
             }

             // Append the HTML to the table body
             tableBody.append(tableHTML);
             changeLanguage(lang);
             $('#accepted').modal('show');
         }

         function workallocation(auditscheduleid) {



             document.getElementById("process_button").onclick = function() {

                 automateWorkAllocation(auditscheduleid)
             };
             getLabels_jsonlayout([{
                 id: 'randomizework_quest',
                 key: 'randomizework_quest'
             }], 'N').then((text) => {
                 passing_alert_value('Confirmation', text
                     .randomizework_quest, 'confirmation_alert',
                     'alert_header', 'alert_body',
                     'forward_alert');
             });


         }

         function automateWorkAllocation(auditscheduleid) {
             $('#process_button').prop('disabled', true);

             $.ajax({
                 url: '/automateWorkAllocation', // API endpoint
                 type: 'POST', // HTTP Method
                 // contentType: 'application/json', // Data type being sent
                 data: {
                     auditscheduleid: auditscheduleid
                 }, // Data payload
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                         'content') // CSRF token for security
                 },
                 success: function(response) {
                     $('#process_button').prop('disabled', false);
                     // Alert on successful server response
                     //alert('✅ Success: ' + response.message);
                     if (response.status == 'success') {
                         document.getElementById("ok_button").onclick = function() {
                             location.reload()
                         };
                         getLabels_jsonlayout([{
                             id: response.message,
                             key: response.message
                         }], 'N').then((text) => {
                             passing_alert_value('Confirmation', Object.values(
                                     text)[0], 'confirmation_alert',
                                 'alert_header', 'alert_body',
                                 'confirmation_alert');
                         });
                         passing_alert_value('Confirmation', response.message,
                             'confirmation_alert', 'alert_header', 'alert_body',
                             'confirmation_alert');

                     } else {

                         passing_alert_value('Confirmation', response.message,
                             'confirmation_alert', 'alert_header', 'alert_body',
                             'confirmation_alert');
                     }
                 },
                 error: function(xhr, status, error) {
                     $('#process_button').prop('disabled', false);

                     // Alert on error
                     var response = JSON.parse(xhr.responseText);
                     if (response.error == 401) {
                         handleUnauthorizedError();
                     } else {
                         var errorMessage = response.error ||
                             'An unknown error occurred';
                         passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                             'alert_header', 'alert_body', 'confirmation_alert');
                     }

                 }
             });

         }
     </script>
 @endsection
