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
            <div class="card-header card_header_color lang " key="department_head">Create Department </div>
            <div class="card-body">
                <form id="departmentform" name="departmentform">
                    @csrf
                    <div class="row">
                   
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="deptesname" for="deptesname">Department short name</label>
                            <input type="text" class="form-control name" id="deptesname" maxlength='10'  data-placeholder-key="deptesname" name="deptesname"
                                required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="deptelname" for="deptelname">Department long name</label>
                            <input type="text" class="form-control name" id="deptelname" maxlength='100'  data-placeholder-key="deptelname" name="deptelname"
                               required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="depttsname" for="depttsname">Department Tamil short name</label>
                            <input type="text" class="form-control name" id="depttsname" maxlength='10'  data-placeholder-key="depttsname" name="depttsname"
                                required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="depttlname" for="depttlname">Department Tamil long name</label>
                            <input type="text" class="form-control name" id="depttlname" maxlength='100'   data-placeholder-key="depttlname" name="depttlname"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="orderid" for="orderid">Order</label>
                            <input type="text" class="form-control only_numbers" id="orderid"    data-placeholder-key="orderid" name="orderid"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="levelid" for="levelid">Level</label>
                            <input type="text" class="form-control only_numbers" id="levelid"     data-placeholder-key="levelid" name="levelid"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="financialyear" for="financialyear">Financial Year</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="financialyear" name="financialyear">
                                <option value="" data-name-en="---Select Financial Year---"
                                    data-name-ta="---நிதி ஆண்டைத் தேர்ந்தெடுக்கவும்---">---Select Financial Year---</option>
                                        <!-- <option value="">2019</option>
                                        <option value="">2020</option>
                                        <option value="">2021</option> -->
                                        <option value="2021-2022">2021-2022</option>
                                        <option value="2022-2023">2022-2023</option>
                                        <option value="2023-2024">2023-2024</option>
                                        <option value="2024-2025">2024-2025</option>

                                   
                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="authority" for="Authority">Authority</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="authority" name="authority">
                                <option value="" data-name-en="---Select Authority---"
                                    data-name-ta="---அதிகாரத்தைத் தேர்ந்தெடு---">---Select Authority---</option>
                                        <!-- <option value="">2019</option>
                                        <option value="">2020</option>
                                        <option value="">2021</option> -->
                                        <option value="DD">DD</option>
                                        <option value="AD">AD</option>
                                      
                                   
                            </select>
                        </div>
                    
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="rejoinderlimit" for="rejoinder">Rejoinder Limit</label>
                            <input type="text" class="form-control only_numbers" id="rejoinder"   data-placeholder-key="rejoinder" name="rejoinder"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="membercount" for="membercount">Member Count</label>
                            <input type="text" class="form-control only_numbers" id="membercount"    data-placeholder-key="membercount" name="membercount"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="esculationdate" for="esculationdate">Esculation Date</label>
                            <input type="text" class="form-control only_numbers " id="esculationdate"     data-placeholder-key="esculationdate" name="esculationdate"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="fileuploadcount" for="fileuploadcount">File Upload Count</label>
                            <input type="text" class="form-control only_numbers" id="fileuploadcount"    data-placeholder-key="fileuploadcount" name="fileuploadcount"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="paraauthority" for="paraauthority">Para Authority</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="paraauthority" name="paraauthority">
                                <option value="" data-name-en="---Select Para Authority---"
                                    data-name-ta="--- பாரா அதிகாரத்தைத் தேர்ந்தெடு---">---Select Para Authority---</option>
                                        <!-- <option value="">2019</option>
                                        <option value="">2020</option>
                                        <option value="">2021</option> -->
                                        <option value="AD">AD</option>
                                        <option value="Team Head">Team Head</option>

                                      
                                   
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="exitmeetingdate" for="exitmeeting">Exit Meeting Day</label>
                            <input type="text" class="form-control only_numbers" id="exitmeeting"    data-placeholder-key="exitmeeting" name="exitmeeting"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="maxleave" for="maximumleave">Maximum leave</label>
                            <input type="text" class="form-control only_numbers" id="maximumleave"     data-placeholder-key="maximumleave" name="maximumleave"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="liabilitycount" for="liabilitycount">Liability Count</label>
                            <input type="text" class="form-control only_numbers" id="liabilitycount"    data-placeholder-key="liabilitycount" name="liabilitycount"
                                 required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="auditee_ofcusercount" for="auditee_ofcusercount">Auditee Official Count</label>
                            <input type="text" class="form-control only_numbers" id="auditee_ofcusercount"    data-placeholder-key="auditee_ofcusercount" name="auditee_ofcusercount"
                                 required />
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

                            <input type="hidden" name="deptid" id="deptid">


                            <input type="hidden" name="deptcode" id="deptcode" value="" />

                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
       <style>


       
       </style>
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="department_table">Department Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="departmenttable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department_table">Department Details</th>
                                    <!-- <th class="lang align-middle text-center" key="deptelname">Department Tamil names</th> -->
                                    <!-- <th class="lang align-middle text-center" key="depttsname">Department Tamil names</th> -->
                                    <!-- <th class="lang align-middle text-center" key="depttlname">Department Tamil long name</th> -->
                                    <th class="lang align-middle text-center" key="countdetails">Count Details</th>
                                    <!-- <th class="lang align-middle text-center" key="levelid">Level</th> -->
                                    <th class="lang align-middle text-center " key="financialyear">Financial Year</th>
                                    <!-- <th class="lang align-middle text-center" key="authority">Authority</th> -->
                                    <!-- <th class="lang align-middle text-center" key="rejoinderlimit">Rejoinder Limit</th> -->
                                    <!-- <th class="lang align-middle text-center" key="membercount">Member Count</th>
                                    <th class="lang align-middle text-center" key="esculationdate">Esculation Date</th>
                                    <th class="lang align-middle text-center" key="fileuploadcount">File Upload Count</th> -->
                                    <!-- <th class="lang align-middle text-center" key="paraauthority">Para Authority</th> -->
                                    <th class="lang align-middle text-center" key="exitmeetingdate">Exit Meeting Day</th>
                                    <!-- <th class="lang align-middle text-center" key="maxleave">Maximum leave</th>
                                    <th class="lang align-middle text-center" key="liabilitycount">Liability Count</th>
                                    <th class="lang align-middle text-center" key="auditee_ofcusercount">Auditee Official Count</th> -->
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<!-- <script src="{{asset('assets/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script> -->
<script>

    let table;
    let dataFromServer = [];

    // var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        $('#departmentform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'departmentform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('department.department_fetchData') }}",
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

        if ($.fn.DataTable.isDataTable('#departmenttable')) {
            $('#departmenttable').DataTable().clear().destroy();
        }

        table = $('#departmenttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [
        {
            data: null,
            render: function(data, type, row, meta) {
                return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
            },
             className: 'text-end text-wrap',
            type: "num"
        }, 
        {
    data: "deptesname", // No direct column binding
    render: function (data, type, row) {
        const translations = {
            en: {
                shortName: "Department Short Name",
                longName: "Department Long Name",
                tamilShortName: "Department Tamil Short Name",
                tamilLongName: "Department Tamil Long Name"
            },
            ta: {
                shortName: "துறை குறும்பெயர்",
                longName: "துறை நீண்ட பெயர்",
                tamilShortName: "துறை தமிழ் குறும்பெயர்",
                tamilLongName: "துறை தமிழ் நீண்ட பெயர்"
            }
        };

        const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

        // Retrieve values or default to "-"
        let deptShort = row.deptesname ? row.deptesname : "-";
        let deptLong = row.deptelname ? row.deptelname : "-";
        let deptTamilShort = row.depttsname ? row.depttsname : "-";
        let deptTamilLong = row.depttlname ? row.depttlname : "-";

        // Combine all the department details into one string
        return `
            <b>${translations[lang].shortName}:</b> ${deptShort} <br>
            <b>${translations[lang].longName}:</b> ${deptLong} <br>
            <b>${translations[lang].tamilShortName}:</b> ${deptTamilShort} <br>
            <b>${translations[lang].tamilLongName}:</b> ${deptTamilLong}
        `;
    },
    className: 'text-wrap text-start'

},





        // {
        //     data: "depttsname",
        //     title: columnLabels?.["depttsname"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.depttsname || '-';
        //     }
        // },
        // {
        //     data: "depttlname",
        //     title: columnLabels?.["depttlname"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.depttlname || '-';
        //     }
        // },
//         {
//     data: null, // No direct column binding
//     title: columnLabels?.["Order Details"]?.[language] || "Order Details",
//     className: "d-none d-md-table-cell lang extra-column text-wrap",
//     render: function (data, type, row) {
//         // Define translations for each field label
//         const translations = {
//             en: { orderid: "Order ID", levelid: "Level ID" },
//             ta: { orderid: "ஆர்டர் ஐடி", levelid: "நிலை ஐடி" }
//         };

//         const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

//         let orderid = row.orderid ? row.orderid : "-";
//         let levelid = row.levelid ? row.levelid : "-";

//         return `<b>${translations[lang].orderid}:</b> ${orderid} <br>
//                 <b>${translations[lang].levelid}:</b> ${levelid}`;
//     }
// },

{
    data: null, // No direct column binding
    title: columnLabels?.["Order and Details"]?.[language] || "Order and Details",
    className: "d-none d-md-table-cell lang extra-column text-wrap",
    render: function (data, type, row) {
        // Define translations for each field label
        const translations = {
            en: { 
                orderid: "Order", 
                levelid: "Level", 
                rejoinderlimit: "Rejoinder Limit", 
                membercount: "Member Count", 
                escalationdate: "Escalation Date", 
                fileuploadcount: "File Upload Count" 
            },
            ta: { 
                orderid: "ஆர்டர்", 
                levelid: "நிலை", 
                rejoinderlimit: "மறுசீரமைப்பு வரம்பு", 
                membercount: "உறுப்பினர் எண்ணிக்கை", 
                escalationdate: "விரைவு தேதி", 
                fileuploadcount: "கோப்பு பதிவேற்ற எண்ணிக்கை" 
            }
        };

        const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

        let orderid = row.orderid ? row.orderid : "-";
        let levelid = row.levelid ? row.levelid : "-";
        let rejoinderlimit = row.rejoinderlimit ? row.rejoinderlimit : "-";
        let membercount = row.membercount ? row.membercount : "-";
        let escalationdate = row.esculationdate ? row.esculationdate : "-";
        let fileuploadcount = row.fileuploadcount ? row.fileuploadcount : "-";

        return `
            <b>${translations[lang].orderid}:</b> ${orderid} <br>
            <b>${translations[lang].levelid}:</b> ${levelid} <br>
            <b>${translations[lang].rejoinderlimit}:</b> ${rejoinderlimit} <br>
            <b>${translations[lang].membercount}:</b> ${membercount} <br>
            <b>${translations[lang].escalationdate}:</b> ${escalationdate} <br>
            <b>${translations[lang].fileuploadcount}:</b> ${fileuploadcount}
        `;
    }
},




        // {
        //     data: "orderid",
        //     title: columnLabels?.["orderid"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.orderid || '-';
        //     }
        // },
        // {
        //     data: "levelid",
        //     title: columnLabels?.["levelid"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.levelid || '-';
        //     }
        // },
        {
        data: null, // No direct column binding
        title: columnLabels?.["Authority Details"]?.[language] || "Authority Details",
        className: "d-none d-md-table-cell lang extra-column text-wrap",
        render: function (data, type, row) {
            // Define translations for each field label
            const translations = {
                en: { financialyear: "Financial Year", authority: "Authority", paraauthority: "Para Authority" },
                ta: { financialyear: "நிதி ஆண்டு", authority: "அதிகாரம்", paraauthority: " பாரா அதிகாரம்" }
            };

            const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

            let financialyear = row.financialyear ? row.financialyear : "-";
            let authority = row.authority ? row.authority : "-";
            let paraauthority = row.paraauthority ? row.paraauthority : "-";

            return `<b>${translations[lang].financialyear}:</b> ${financialyear} <br>
                    <b>${translations[lang].authority}:</b> ${authority} <br>
                    <b>${translations[lang].paraauthority}:</b> ${paraauthority}`;
        }
},

        // {
        //     data: "financialyear",
        //     title: columnLabels?.["financialyear"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.financialyear || '-';
        //     }
        // },
        // {
        //     data: "authority",
        //     title: columnLabels?.["authority"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.authority || '-';
        //     }
        // },
        // {
        //     data: null, // No direct column binding
        //     title: columnLabels?.["Details"]?.[language] || "Details",
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         // Define translations for each field label
        //         const translations = {
        //             en: { rejoinderlimit: "Rejoinder Limit", membercount: "Member Count", escalationdate: "Escalation Date", fileuploadcount: "File Upload Count" },
        //             ta: { rejoinderlimit: "புதிய பதில் வரம்பு", membercount: "நிறுவன உறுப்பினர்களின் எண்ணிக்கை", escalationdate: "ஊர்ச்சி தேதி", fileuploadcount: "கோப்பு பதிவேற்ற எண்ணிக்கை" }
        //         };

        //         const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

        //         let rejoinderlimit = row.rejoinderlimit ? row.rejoinderlimit : "-";
        //         let membercount = row.membercount ? row.membercount : "-";
        //         let escalationdate = row.esculationdate ? new Date(row.esculationdate).toLocaleDateString('en-GB') : "-";
        //         let fileuploadcount = row.fileuploadcount ? row.fileuploadcount : "-";

        //         return `<b>${translations[lang].rejoinderlimit}:</b> ${rejoinderlimit} <br>
        //                 <b>${translations[lang].membercount}:</b> ${membercount} <br>
        //                 <b>${translations[lang].escalationdate}:</b> ${escalationdate} <br>
        //                 <b>${translations[lang].fileuploadcount}:</b> ${fileuploadcount}`;
        //     }
        // },

        // {
        //     data: "rejoinderlimit",
        //     title: columnLabels?.["rejoinderlimit"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.rejoinderlimit || '-';
        //     }
        // },
        // {
        //     data: "membercount",
        //     title: columnLabels?.["membercount"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.membercount || '-';
        //     }
        // }, {
        //     data: "esculationdate",
        //     title: columnLabels?.["esculationdate"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.esculationdate || '-';
        //     }
        // }, {
        //     data: "fileuploadcount",
        //     title: columnLabels?.["fileuploadcount"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.fileuploadcount || '-';
        //     }
        // }, 
        // {
        //     data: "paraauthority",
        //     title: columnLabels?.["paraauthority"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.paraauthority || '-';
        //     }
        // },

        {
        data: null, // No direct column binding
        title: columnLabels?.["Details"]?.[language] || "Details",
        className: "d-none d-md-table-cell lang extra-column text-wrap",
        render: function (data, type, row) {
            // Define translations for each field label
            const translations = {
                en: { exitmeetingdate: "Exit Meeting Date", maxleave: "Max Leave", liabilitycount: "Liability Count", auditee_ofcusercount: "Auditee Official Count" },
                ta: { exitmeetingdate: " வெளியேறும் சந்திப்பு நாள்", maxleave: " அதிகபட்ச விடுப்பு ", liabilitycount: "பொறுப்பு எண்ணிக்கை", auditee_ofcusercount: "தணிக்கையாளர் அதிகாரப்பூர்வ எண்ணிக்கை" }
            };

            const lang = language === "ta" ? "ta" : "en"; // Fallback to English if not Tamil

            let exitmeetingdate = row.exitmeetingdate ? row.exitmeetingdate : "-";
            let maxleave = row.maxleave ? row.maxleave : "-";
            let liabilitycount = row.liabilitycount ? row.liabilitycount : "-";
            let auditee_ofcusercount = row.auditee_ofcusercount ? row.auditee_ofcusercount : "-";

            return `<b>${translations[lang].exitmeetingdate}:</b> ${exitmeetingdate} <br>
                    <b>${translations[lang].maxleave}:</b> ${maxleave} <br>
                    <b>${translations[lang].liabilitycount}:</b> ${liabilitycount} <br>
                    <b>${translations[lang].auditee_ofcusercount}:</b> ${auditee_ofcusercount}`;
        }
},

        //  {
        //     data: "exitmeetingdate",
        //     title: columnLabels?.["exitmeetingdate"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.exitmeetingdate || '-';
        //     }
        // }, {
        //     data: "maxleave",
        //     title: columnLabels?.["maxleave"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.maxleave || '-';
        //     }
        // }, {
        //     data: "liabilitycount",
        //     title: columnLabels?.["liabilitycount"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.liabilitycount || '-';
        //     }
        // },
        // {
        //     data: "auditee_ofcusercount",
        //     title: columnLabels?.["auditee_ofcusercount"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.auditee_ofcusercount || '-';
        //     }
        // },


        {
            data: "statusflag",
            title: columnLabels?.["statusflag"]?.[language],
            render: function(data) {
                let activeText = arrLang?.[language]?.["active"] || "Active";
                let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                return data === 'Y'
                    ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                    : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            },
            className: "text-center text-wrap d-none d-md-table-cell extra-column noExport"
        },
        {
            data: "encrypted_deptid",
            title: columnLabels?.["actions"]?.[language],
            render: (data) =>
                `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            className: "text-center noExport text-wrap"
        }
    ],


            "initComplete": function(settings, json) {
                $("#departmenttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
       
        });
     
        const mobileColumns =[
       "orderid","levelid", "financialyear", "authority", "rejoinderlimit", "membercount",
        "esculationdate", "fileuploadcount", "paraauthority", "exitmeetingdate",
        "maxleave","auditee_ofcusercount", "liabilitycount", "statusflag"
    ];
    setupMobileRowToggle(mobileColumns);
    updatedatatable(language, "departmenttable");
    }

function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ✅ Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;



    // ✅ Define Headers Properly
    let headers = [
        { header: dtText["deptesname"] || "Department Short Name", key: "deptesname" },
        { header: dtText["deptelname"] || "Department Long Name", key: "deptelname" },
        { header: dtText["depttsname"] || "Department Tamil Short Name", key: "depttsname" },
        { header: dtText["depttlname"] || "Department Tamil Long Name", key: "depttlname" },
        { header: dtText["orderid"] || "Order", key: "orderid" },
        { header: dtText["levelid"] || "Level", key: "levelid" },
        { header: dtText["rejoinderlimit"] || "Rejoinder Limit", key: "rejoinderlimit" },
        { header: dtText["membercount"] || "Member Count", key: "membercount" },
        { header: dtText["esculationdate"] || "Escalation Date", key: "esculationdate" },
        { header: dtText["fileuploadcount"] || "File Upload Count", key: "fileuploadcount" },
        { header: dtText["financialyear"] || "Financial Year", key: "financialyear" },
        { header: dtText["authority"] || "Authority", key: "authority" },
        { header: dtText["paraauthority"] || "Para Authority", key: "paraauthority" },
        { header: dtText["exitmeetingdate"] || "Exit Meeting Date", key: "exitmeetingdate" },
        { header: dtText["maxleave"] || "Maximum Leave", key: "maxleave" },
        { header: dtText["liabilitycount"] || "Liability Count", key: "liabilitycount" },
        { header: dtText["auditee_ofcusercount"] || "Auditee Official Count", key: "auditee_ofcusercount" }

    ];

    // ✅ Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");
        let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        return {
            deptesname : rowData.deptesname || "-",
            deptelname: rowData.deptelname || "-",
            depttsname: rowData.depttsname || "-",
            depttlname: rowData.depttlname || "-",
            orderid: rowData.orderid || "-",
            levelid: rowData.levelid || "-",
            rejoinderlimit: rowData.rejoinderlimit || "-",
            membercount: rowData.membercount || "-",
            esculationdate: rowData.esculationdate || "-",
            fileuploadcount: rowData.fileuploadcount || "-",
            financialyear: rowData.financialyear || "-",
            authority: rowData.authority || "-",
            paraauthority: rowData.paraauthority || "-",
            exitmeetingdate: rowData.exitmeetingdate || "-",
            maxleave: rowData.maxleave || "-",
            liabilitycount: rowData.liabilitycount || "-",
            auditee_ofcusercount: rowData.auditee_ofcusercount || "-"
        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // ✅ Create Workbook and Worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add Headers in Separate Columns (Avoid Merging Issues)
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    
    // ✅ Ensure Headers Align with Data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}_${language}.xlsx`);
}


// Utility functions to extract text & clean up data
function extractText(html) {
    return $("<div>").html(html).text().trim();
}

function extractIfhrms(html) {
    let match = html.match(/<small><b>IFHRMS No : <\/b>(\d+)<\/small>/);
    return match ? match[1] : "-";
}




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#departmenttable')) {
            $('#departmenttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }






    jsonLoadedPromise.then(() => {
    const language = window.localStorage.getItem('lang') || 'en';
    var validator = $("#departmentform").validate({

        rules: {
            deptesname: {
                required: true,
            },
            deptelname: {
                required: true
            },
            depttsname: {
                required: true
            },
            depttlname: {
                required: true
            },
            orderid: {
                required: true
            },
            levelid: {
                required: true
            },
            financialyear: {
                required: true
            },
            authority: {
                required: true
            },
            rejoinder: {
                required: true
            },
            membercount: {
                required: true
            },
            esculationdate: {
                required: true
            },
            fileuploadcount: {
                required: true
            },
            paraauthority: {
                required: true
            },
            exitmeeting: {
                required: true
            },
            maximumleave: {
                required: true
            },
            liabilitycount: {
                required: true
            },
            auditee_ofcusercount : {
                required: true
            },
            statusflag: {
                required: true
            },
      
        },
        errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },
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
        if ($("#departmentform").valid()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = $('#departmentform').serializeArray();
            $.ajax({
                url: "{{ route('department.department_insertupdate') }}",
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


   

    // Handle Edit Button Click
    $(document).on('click', '.editchargedel', function() {
        const id = $(this).attr('id');
       // alert(id);
        if (id) {
            reset_form();
            $('#deptid').val(id); // Set the ID field directly

            $.ajax({
                url: "{{ route('department.department_fetchData') }}",
                method: 'POST',
                data: {
                    deptid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('departmentform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            populateChargeForm(response.data[0]); // Populate form with data
                        } else {
                            alert('Department data is empty');
                        }
                    } else {
                        alert('Department not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });




    function populateChargeForm(dept) {
        $('#display_error').hide();
        change_button_as_update('departmentform', 'action', 'buttonaction', 'display_error', '', '');

        $('#deptid').val(dept.encrypted_deptid);

        $('#deptesname').val(dept.deptesname);
        $('#deptelname').val(dept.deptelname);
        $('#depttsname').val(dept.depttsname);
        $('#depttlname').val(dept.depttlname);
        $('#orderid').val(dept.orderid);
        $('#levelid').val(dept.levelid);
        $('#financialyear').val(dept.financialyear).select2();
        $('#authority').val(dept.authority).select2();
        $('#rejoinder').val(dept.rejoinderlimit);
        $('#membercount').val(dept.membercount);
        $('#esculationdate').val(dept.esculationdate);
        $('#fileuploadcount').val(dept.fileuploadcount);

        $('#paraauthority').val(dept.paraauthority).select2();
        $('#exitmeeting').val(dept.exitmeetingdate);
        $('#maximumleave').val(dept.maxleave);
        $('#liabilitycount').val(dept.liabilitycount);
        $('#auditee_ofcusercount').val(dept.auditee_ofcusercount);

       // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        $('#departmentform')[0].reset();
        $('#departmentform').validate().resetForm();
        $('#financialyear').val('').select2(); 
        $('#paraauthority').val('').select2();
        $('#authority').val('').select2();

        changeButtonAction('departmentform', 'action', 'buttonaction', 'reset_button', 'display_error',
        @json($savebtn), @json($clearbtn), @json($insert))
        // change_button_as_insert('departmentform', 'action', 'buttonaction', 'display_error', '', '');
       // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>

@endsection