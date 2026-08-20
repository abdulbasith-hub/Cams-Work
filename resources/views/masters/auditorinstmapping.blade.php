@section('content')
@section('title', 'Auditor Mapping Institution')
@extends('index2')
@include('common.alert')
@php
    $sessionmainobjectiondel = session('charge');
    $sessionchargedel = session('charge');

    $roleTypeCode = $sessionchargedel->roletypecode;

@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditorinst_head">Auditor Institution Mapping</div>
            <div class="card-body">
                <form id="auditor_inst_mapping" name="auditor_inst_mapping">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label lang required" key="roletype" for="roletypecode">Role Type</label>
                            <select class="form-select mr-sm-2  select2 lang-dropdown" id="roletypecode"
                                name="roletypecode">
                                <option value="" data-name-en="Select Role Type"
                                    data-name-ta="பாத்திர வகை தேர்வு செய்">Select Role Type</option>
                                @if (!empty($roletype) && count($roletype) > 0)
                                    @foreach ($roletype as $role)
                                        <option value="{{ $role->roletypecode }}"
                                            data-name-en="{{ $role->roletypeelname }}"
                                            data-name-ta="{{ $role->roletypetlname }}">
                                            {{ $role->roletypeelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Role Type Available</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 select2  lang-dropdown" id="deptcode" name="deptcode"
                                onchange="getRegionBasedOnDept(this.value,'',''); getDesignationBasedOnDept('');">
                                <option value="" data-name-en="Select Department"
                                    data-name-ta="துறையை தேர்வு செய்">Select a Department</option>
                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Departments Available</option>
                                @endif

                            </select>
                        </div>

                        <div class="col-md-4 mb-2 " id="regiondiv">
                            <label class="form-label required lang" for="validationDefault01" key="region">Region
                            </label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="regioncode" name="regioncode">
                                <option value="" data-name-en="Select a Region"
                                    data-name-ta="பகுதியை தேர்வு செய்">Select a Region</option>
                                <option value="" disabled id="no-region-option" data-name-en="No Region Available"
                                    data-name-ta="பகுதி கிடைக்கவில்லை">No Region Available</option>


                            </select>
                        </div>



                        <div class="col-md-4 mb-2 " id="distdiv">
                            <label class="form-label lang required" key="district" for="validationDefault01">District
                            </label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="distcode" name="distcode">
                                <option value="" data-name-en="Select a District"
                                    data-name-ta="மாவட்டத்தை தேர்வு செய்">Select a District</option>
                                @if (!empty($district) && count($district) > 0)
                                    @foreach ($district as $dis)
                                        <option value="{{ $dis->distcode }}" data-name-en="{{ $dis->distename }}"
                                            data-name-ta="{{ $dis->disttname }}">
                                            {{ $dis->distename }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No District Available</option>
                                @endif

                            </select>
                        </div>



                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="institution_eng_name"
                                for="inst_ename">Institution English Name</label>
                            <input type="text" maxlength='200' class="form-control name" id="inst_ename"
                                name="inst_ename" data-placeholder-key="inst_ename" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="institution_tam_name"
                                for="inst_tname">Institution Tamil Name</label>
                            <input type="text" maxlength='300' class="form-control name" id="inst_tname"
                                name="inst_tname" data-placeholder-key="inst_tname" required>
                        </div>


                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="nodal_eng_name" for="nodal_ename">Nodal Person
                                Name in English</label>
                            <input type="text" class="form-control name" maxlength='100' id="nodal_ename"
                                name="nodal_ename" data-placeholder-key="nodal_ename" required>
                        </div>


                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="nodal_tam_name" for="nodal_tname">Nodal
                                Person Name in English</label>
                            <input type="text" class="form-control name" maxlength='100' id="nodal_tname"
                                name="nodal_tname" data-placeholder-key="nodal_tname" required>
                        </div>

                        <div class="col-md-4 mb-2 ">
                            <label class="form-label required lang" for="desigcode"
                                key="designation">Designation</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="desigcode"
                                name="desigcode">
                                <option value="" data-name-en=" Select a Designation"
                                    data-name-ta="பதவியை தேர்வு செய்">Select Designation</option>
                                <option value="" disabled id="no-region-option"
                                    data-name-en="No Designation Available" data-name-ta="பதவி கிடைக்கவில்லை">No
                                    Designation Available</option>


                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="email" for="email">Email</label>
                            <input type="email" maxlength='100' class="form-control" id="email" name="email"
                                data-placeholder-key="email" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="mobile" for="mobile">Mobile
                                Number</label>
                            <input type="text" maxlength='10' class="form-control only_numbers" id="mobile"
                                name="mobile" maxlength="10" data-placeholder-key="mobile" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="add_eng_name" for="address_ename">Address
                                in English</label>
                            <input type="text" class="form-control" maxlength='200' id="address_ename"
                                name="address_ename" data-placeholder-key="address_ename" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="add_tam_name" for="address_tname">Address in
                                Tamil</label>
                            <input type="text" class="form-control" maxlength='200' id="address_tname"
                                name="address_tname" data-placeholder-key="address_tname" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="pincode" for="pincode">Pincode</label>
                            <input type="text" class="form-control only_numbers" maxlength='6' id="pincode"
                                name="pincode" maxlength="6" data-placeholder-key="pincode" required>
                        </div>


                        <div class="col-md-4 mb-2 " id="">
                            <label class="form-label lang required" key="audittype" for="validationDefault01">Audit
                                Type
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="audittype" name="audittype">
                                <option value="" data-name-en="Select Audit Type" data-name-ta="">Select Audit
                                    Type</option>

                                <option disabled data-name-en="No Audittype Available" data-name-ta="">No District
                                    Available</option>


                            </select>
                        </div>





                        <div class="col-md-4 mb-2">
                            <label class="form-label required lang" key="statusflag">Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="statusflag" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="statusflag" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-3 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="audinstmappingcode" id="audinstmappingcode"
                                value="" />

                            <input type="hidden" name="instmappingcode" id="instmappingcode" value="" />

                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3  lang"
                                style="height:35px;font-size: 13px;" key="clear_btn" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditorinst_table">Auditor Institution Mapping
                Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditorinstmapping"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_num">S.No</th>
                                    <th class="lang align-middle text-center" key="">Roletype Details</th>
                                    <!-- <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="region">Region
                                    </th> -->
                                    <th class="lang align-middle text-center" key="district">District</th>
                                    <th class="lang align-middle text-center" key="">Institution
                                      </th>
                                    <!-- <th class="lang align-middle text-center" key="institution_tam_name">Institution
                                        Tamil Name</th> -->
                                    <!-- <th class="lang align-middle text-center" key="nodal_eng_name">Nodal English Name
                                    </th>
                                    <th class="lang align-middle text-center" key="nodal_tam_name">Nodal Tamil Name
                                    </th> -->

                                    <th class="lang align-middle text-center" key="designation">Designation</th>
                                    <th class="lang align-middle text-center" key="email">Email</th>
                                    <th class="lang align-middle text-center" key="mobile">Mobile Number</th>
                                    <!-- <th class="lang align-middle text-center" key="add_eng_name">Address English Name
                                    </th> -->
                                    <!-- <th class="lang align-middle text-center" key="add_tam_name">Address Tamil Name
                                    </th> -->
                                    <th class="lang align-middle text-center" key="pincode">Pincode</th>





                                    <th class="lang align-middle text-center" key="statusflag">Status</th>
                                    <th class="all lang align-middle text-center" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
<!-- Include jQuery and Bootstrap -->



<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    var session_roletypecode = '<?php echo $roleTypeCode; ?>';
    let table;
    let dataFromServer = [];

    $(document).ready(function() {
        $('#auditor_inst_mapping')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        $('#translate').change(function() {
            const lang = $('#translate').val();
            updateTableLanguage(lang);
        });




    });


    $("#translate").change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'auditor_inst_mapping');

    });



    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('auditorinstmapping.auditorinstmapping_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                //  console.log("Success Response:", json);
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
            }
        });
    }


    function renderTable(language) {
        const roletypeColumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        const departmentColumn = language === 'ta' ? 'depttlname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';

        if ($.fn.DataTable.isDataTable('#auditorinstmapping')) {
            $('#auditorinstmapping').DataTable().clear().destroy();
        }

        table = $('#auditorinstmapping').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            lengthChange: false,
            "data": dataFromServer,
            // "columns": [{
            //         data: null,
            //         render: function(data, type, row, meta) {
            //             return meta.row + 1;
            //         }
            //     },
            //     {
            //         data: roletypeColumn
            //     },
            //     {
            //         data: departmentColumn
            //     },
            //     {
            //         data: regionColumn
            //     },
            //     {
            //         data: districtColumn
            //     },
            //     {
            //         data: "instename"
            //     },
            //     {
            //         data: "insttname"
            //     },
            //     {
            //         data: "nodalperson_ename"
            //     },
            //     {
            //         data: "nodalperson_tname"
            //     },
            //     {
            //         data: designationColumn
            //     },
            //     {
            //         data: "email"
            //     },
            //     {
            //         data: "mobile"
            //     },
            //     {
            //         data: "officeaddress_ename"
            //     },
            //     {
            //         data: "officeaddress_tname"
            //     },
            //     {
            //         data: "pincode"
            //     },
            //     {
            //         data: "statusflag",
            //         render: function(data) {
            //             return data === "Y" ? "Yes" : "No";
            //         }
            //     },
            //     {
            //         data: "encrypted_instmappingid",
            //         render: function(data, type, row) {
            //             return `<center><a class="btn editicon editauditinsmaprecords" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
            //         }
            //     }
            // ],
         

            // ],
            columns: [
        {
            data: null,
            render: function(data, type, row, meta) {
                return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
            },
             className: ' text-wrap text-end',
            type: "num"
        },

        {
                data: null,
                render: function (data, type, row) {
                    const roleType = row[roletypeColumn] || '-';
                    const region = row[regionColumn] || '-';
                    const department = row[departmentColumn] || '-';
                    return `
                        <div>
                         <b>Role Type:</b> ${roleType}<br>
                            <b>Region:</b> ${region}<br>
                            <b>Department:</b> ${department}
                        </div>`;
                },
               // title: "Region, Role Type & Department",
                className: ' text-wrap text-start'
            },
        // {
        //     data: departmentColumn,
        //     title: columnLabels?.[departmentColumn]?.[language],
        //     render: function (data, type, row) {
        //         return row[departmentColumn] || '-';
        //     },
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        // },
        // {
        //     data: regionColumn,
        //     title: columnLabels?.[regionColumn]?.[language],
        //     render: function (data, type, row) {
        //         return row[regionColumn] || '-';
        //     },
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        // },
        {
            data: districtColumn,
            title: columnLabels?.[districtColumn]?.[language],
            render: function (data, type, row) {
                return row[districtColumn] || '-';
            },
            className: "d-none d-md-table-cell lang extra-column text-wrap",
        },
        {
    data: null,
   // title: columnLabels?.["instename"]?.[language] + " / " + columnLabels?.["insttname"]?.[language], // Combined title
    className: "d-none d-md-table-cell lang extra-column text-wrap",
    render: function (data, type, row) {
        const instename = row.instename || '-';
        const insttname = row.insttname || '-';
        return `<div>
                    <b>Audit Office In English:</b> ${instename}<br>
                    <b>Audit Office In Tamil:</b> ${insttname}
                </div>`;
    }
},

        // {
        //     data: "instename",
        //     title: columnLabels?.["instename"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.instename || '-';
        //     }
        // },
        // {
        //     data: "insttname",
        //     title: columnLabels?.["insttname"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.insttname || '-';
        //     }
        // },
        // {
        //     data: "nodalperson_ename",
        //     title: columnLabels?.["nodalperson_ename"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.nodalperson_ename || '-';
        //     }
        // },
        // {
        //     data: "nodalperson_tname",
        //     title: columnLabels?.["nodalperson_tname"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.nodalperson_tname || '-';
        //     }
        // },
        
        {
            data: designationColumn,
            title: columnLabels?.[designationColumn]?.[language],
            render: function (data, type, row) {
                return row[designationColumn] || '-';
            },
            className: "d-none d-md-table-cell lang extra-column text-wrap",
        },
        {
            data: "email",
            title: columnLabels?.["email"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.email || '-';
            }
        },
        {
            data: "mobile",
            title: columnLabels?.["mobile"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.mobile || '-';
            }
        },
        // {
        //     data: "officeaddress_ename",
        //     title: columnLabels?.["officeaddress_ename"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.officeaddress_ename || '-';
        //     }
        // },
        // {
        //     data: "officeaddress_tname",
        //     title: columnLabels?.["officeaddress_tname"]?.[language],
        //     className: "d-none d-md-table-cell lang extra-column text-wrap",
        //     render: function (data, type, row) {
        //         return row.officeaddress_tname || '-';
        //     }
        // },
        {
            data: "pincode",
            title: columnLabels?.["pincode"]?.[language],
            className: "d-none d-md-table-cell lang extra-column text-wrap",
            render: function (data, type, row) {
                return row.pincode || '-';
            }
        },
       
        {
            data: "statusflag",
            title: columnLabels?.["statusflag"]?.[language],
            render: function(data) {
                let activeText = arrLang?.[language]?.["active"] || 'Active';
                let inactiveText = arrLang?.[language]?.["inactive"] || 'Inactive';

                return data === 'Y'
                    ? `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>`
                    : `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            },
            className: "text-center d-none d-md-table-cell extra-column text-wrap noExport"
        },
        {
            data: "encrypted_instmappingid",
            title: columnLabels?.["actions"]?.[language],
            render: (data) =>
                `<center><a class="btn editicon editauditinsmaprecords" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            className: "text-center noExport text-wrap"
        }
    ],
    "initComplete": function(settings, json) {
                $("#mainobjectiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });
        const mobileColumns = [districtColumn,"instename","insttname",designationColumn,"email","mobile","officeaddress_ename", "pincode",
            "statusflag"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "auditorinstmapping");
    }
    
    function exportToExcel(tableId, language) {
    const table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // Fetch the translated title dynamically
    const titleKey = `${tableId}_title`;
    const translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";

    // Fetch translation text for headers
    const dtText = dataTables[language]?.datatable || {};

    // Define column mapping for keys based on the language
    const columnMap = {
        roletype: language === "ta" ? "roletypetlname" : "roletypeelname",
        department: language === "ta" ? "depttlname" : "deptesname",
        region: language === "ta" ? "regiontname" : "regionename",
        district: language === "ta" ? "disttname" : "distename",
        designation: language === "ta" ? "desigtsname" : "desigesname",
        email: "email",
        mobile: "mobile",
        instename: "instename",
        insttname: "insttname",
        officeaddress_ename: "officeaddress_ename",
        officeaddress_tname: "officeaddress_tname",
        pincode: "pincode",
    };

    // Define headers with dynamic translation
    const headers = [
        { header: dtText["roletype"] || "Role Type", key: columnMap.roletype },
        { header: dtText["department"] || "Department", key: columnMap.department },
        { header: dtText["region"] || "Region", key: columnMap.region },
        { header: dtText["district"] || "District", key: columnMap.district },
        { header: dtText["designation"] || "Designation", key: columnMap.designation },
        { header: dtText["email"] || "Email", key: columnMap.email },
        { header: dtText["mobile"] || "Mobile", key: columnMap.mobile },
        { header: dtText["instename"] || "Audit Office (English)", key: columnMap.instename },
        { header: dtText["insttname"] || "Audit Office (Tamil)", key: columnMap.insttname },
        { header: dtText["officeaddress_ename"] || "Office Address (English)", key: columnMap.officeaddress_ename },
       // { header: dtText["officeaddress_tname"] || "Office Address (Tamil)", key: columnMap.officeaddress_tname },
        { header: dtText["pincode"] || "Pincode", key: columnMap.pincode },
    ];

    // Extract data from the table
    const rawData = table.rows({ search: "applied" }).data().toArray();

    // Map rawData to match headers dynamically
    const excelData = rawData.map((row) => {
        const button = $(row[0]).find("button.toggle-row");
        const dataRow = button.attr("data-row");
        const rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        // Create an object dynamically based on headers
        return headers.reduce((obj, header) => {
            obj[header.key] = rowData[header.key] || "-";
            return obj;
        }, {});
    });

    // Check if there is data to export
    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // Create and populate the Excel file
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // Add headers dynamically
    XLSX.utils.sheet_add_aoa(ws, [headers.map((h) => h.header)], { origin: "A1" });
    // Add data rows dynamically
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    // Append sheet and export the file
    XLSX.utils.book_append_sheet(wb, ws, translatedTitle);
    XLSX.writeFile(wb, `${translatedTitle}_${language}.xlsx`);
}


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditorinstmapping')) {
            $('#auditorinstmapping').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditorinstmapping')) {
            $('#auditorinstmapping').DataTable().clear().destroy();
        }
        renderTable(language);
    }






    $(document).ready(function() {
        var $deptDiv = $('#deptdiv');
        var $regionDiv = $('#regiondiv');
        var $distDiv = $('#distdiv');

        // Hide all initially
        $deptDiv.hide();
        $regionDiv.hide();
        $distDiv.hide();

        function updateVisibility() {
            var selectedRole = $('#roletypecode').val();

            $deptDiv.hide();
            $regionDiv.hide();
            $distDiv.hide();

            if (selectedRole == '01') {
                $deptDiv.show();
                $regionDiv.show();
                $distDiv.show();
            } else if (selectedRole == '02') {
                $deptDiv.show();
                $regionDiv.show();
            } else if (selectedRole == '03') {
                $deptDiv.show();
            }
        }

        // Run the function on page load to set visibility correctly
        updateVisibility();

        // Run the function when the role type is changed
        $('#roletypecode').change(function() {
            updateVisibility();
        });

    });

    function getDesignationBasedOnDept(deptcode, selectedRegioncode = null) {
        const designationDropdown = $('#desigcode');
        const lang = getLanguage();

        designationDropdown.html(`
    <option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்வு செய்">
        ${lang === 'ta' ? 'பதவியை தேர்வு செய்' : 'Select Designation'}
    </option>
`);

        $('#desigcode option').each(function() {
            var option = $(this);
            if (lang === 'ta') {
                option.text(option.data('name-ta'));
            } else {
                option.text(option.data('name-en'));
            }
        });

        if (deptcode == "") {
            var deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            designationDropdown.append(`
            <option value="" disabled id="no-designation-option"
                    data-name-en="No Designation Available"
                    data-name-ta="பதவி கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'பதவி கிடைக்கவில்லை' : 'No Designation Available'}
            </option>
        `);
        }

        if (deptcode) {
            $.ajax({
                url: "/ForAuditorgetdesignationbasedondept",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        response.data.forEach(designation => {
                            designationDropdown.append(
                                `<option value="${designation.desigcode}"
                                    data-name-en="${designation.desigelname}"
                                    data-name-ta="${designation.desigtlname}"
                                    ${designation.desigcode === selectedRegioncode ? 'selected' : ''}>
                                    ${designation.desigelname}</option>`
                            );
                        });
                    } else {
                        designationDropdown.append(
                            "<option value='' disabled id='no-region-option' data-name-en='No Designation Available' data-name-ta='பதவி கிடைக்கவில்லை'>No Designation Available</option>"
                        );
                    }

                    $('#desigcode option').each(function() {
                        var option = $(this);
                        if (lang === 'ta') {
                            option.text(option.data('name-ta'));
                        } else {
                            option.text(option.data('name-en'));
                        }
                    });
                },
                error: function() {
                    alert('Error fetching Designation. Please try again.');
                }
            });
        }
    }



    // function getDesignationBasedOnDept(deptcode, selectedRegioncode = null) {
    //             // alert('te');
    //             //const districtDropdown = $('#distcode');
    //             const designationDropdown = $('#desigcode');
    //             const lang = getLanguage(); // Get the selected language ('en' or 'ta')

    //             designationDropdown.html("<option value='' data-name-en='Select Department' data-name-ta='துறையை தேர்வு செய்'>Select a Designation</option>");

    //             $('#desigcode option').each(function() {
    //                 var option = $(this);
    //                 if (lang === 'ta') {
    //                     // Set text to Tamil from data-name-ta
    //                     option.text(option.data('data-name-ta'));
    //                 } else {
    //                     // Default to English using data-name-en
    //                     option.text(option.data('data-name-en'));
    //                 }
    //             });
    //             if (deptcode == "") {
    //                 var deptcode = $("#deptcode").val();
    //             }
    //             if (!deptcode) {
    //                 designationDropdown.append("<option value='' disabled id='no-region-option' data-name-en='No Designation Available' data-name-ta='பதவி கிடைக்கவில்லை'>No Designation Available</option>");

    //                 return;
    //             }
    //             if (deptcode) {
    //                 $.ajax({
    //                     url: "/ForAuditorgetdesignationbasedondept",
    //                     type: "POST",
    //                     data: {
    //                         deptcode: deptcode,
    //                         _token: '{{ csrf_token() }}'
    //                     },
    //                     success: function(response) {
    //                         if (response.success && response.data.length > 0) {
    //                             response.data.forEach(designation => {
    //                                 designationDropdown.append(
    //                                     `<option value="${designation.desigcode}" data-name-en="${ designation.desigelname }"
    //                                             data-name-ta="${ designation.desigtlname }" ${
    //                                         designation.desigcode === selectedRegioncode ? 'selected' : ''
    //                             }>${designation.desigelname}</option>`
    //                                 );
    //                             });
    //                         } else {
    //                             designationDropdown.append('<option disabled>No Designation Available</option>');
    //                         }
    //                     },
    //                     error: function() {
    //                         alert('Error fetching Designation. Please try again.');
    //                     }
    //                 });
    //             }

    //         }


    function getRegionBasedOnDept(deptcode, selectedRegioncode = null,selectedaudittypecode = null) {
        const regionDropdown = $('#regioncode');
        const audittypeDropdown = $('#audittype');
        const lang = getLanguage();

        regionDropdown.html(`
        <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்வு செய்">
            ${lang === 'ta' ? 'பகுதியை தேர்வு செய்' : 'Select a Region'}
        </option>
    `);

    audittypeDropdown.html(`
        <option value="" data-name-en="Select Audit Type" data-name-ta="தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்">
            ${lang === 'ta' ? 'தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select Audit Type'}
        </option>
    `);



        if (deptcode == "") {
            var deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            regionDropdown.append(`
            <option value="" disabled id="no-region-option"
                    data-name-en="No Region Available"
                    data-name-ta="பகுதி கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'பகுதி கிடைக்கவில்லை' : 'No Region Available'}
            </option>
        `);
        
        audittypeDropdown.append(`
            <option value="" disabled id="no-region-option"
                    data-name-en="No Audittype Available"
                    data-name-ta="தணிக்கை வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'தணிக்கை வகை கிடைக்கவில்லை' : 'No Audittype Available'}
            </option>
        `);
            return;


        }

        if (deptcode) {
            $.ajax({
                url: "/ForAuditorgetregionbasedondept",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success && response.regions.length > 0) {
                        response.regions.forEach(regions => {
                            // Create options with language-specific text
                            regionDropdown.append(
                                `<option value="${regions.regioncode}"
                                    data-name-en="${regions.regionename}"
                                    data-name-ta="${regions.regiontname}"
                                    ${regions.regioncode === selectedRegioncode ? 'selected' : ''}>
                                    ${regions.regionename}</option>`
                            );
                        });


                    } else {
                        regionDropdown.append('<option disabled>No Region Available</option>');
                    }

                    if (response.success && response.audittype.length > 0) {
                        response.audittype.forEach(audittype => {
                            audittypeDropdown.append(
                                `<option value="${audittype.typeofauditcode}"
                            data-name-en="${audittype.typeofauditename}"
                            data-name-ta="${audittype.typeofaudittname}"
                            ${audittype.typeofauditcode === selectedaudittypecode ? 'selected' : ''}>
                            ${audittype.typeofauditename}</option>`
                            );
                        });
                    } else {
                        audittypeDropdown.append('<option disabled>No Audittype Available</option>');
                    }

                    // After appending options, update the option texts based on the selected language
                    // $('#regioncode option').each(function() {
                    //     var option = $(this);
                    //     if (lang === 'ta') {
                    //         // Set text to Tamil from data-name-ta
                    //         option.text(option.data('name-ta'));
                    //     } else {
                    //         // Default to English using data-name-en
                    //         option.text(option.data('name-en'));
                    //     }
                    // });
                },
                error: function() {
                    alert('Error fetching region. Please try again.');
                }
            });
        }
    }


    // function getRegionBasedOnDept(deptcode, selectedRegioncode = null) {

    //             const regionDropdown = $('#regioncode');

    //             regionDropdown.html('<option value="">Select Region Name</option>');

    //             if (deptcode == "") {
    //                 var deptcode = $("#deptcode").val();
    //             }
    //             if (!deptcode) {
    //                 regionDropdown.append('<option value="" disabled>No Region Available</option>');
    //                 districtDropdown.append('<option value="" disabled>No District Available</option>');
    //                 return;
    //             }
    //             if (deptcode) {
    //                 $.ajax({
    //                     url: "/ForAuditorgetregionbasedondept",
    //                     type: "POST",
    //                     data: {
    //                         deptcode: deptcode,
    //                         _token: '{{ csrf_token() }}'
    //                     },
    //                     success: function(response) {
    //                         if (response.success && response.data.length > 0) {
    //                             response.data.forEach(regions => {
    //                                 regionDropdown.append(
    //                                     `<option value="${regions.regioncode}" ${
    //                                     regions.regioncode === selectedRegioncode ? 'selected' : ''
    //                             }>${regions.regionename}</option>`
    //                                 );
    //                             });
    //                         } else {
    //                             regionDropdown.append('<option disabled>No Region Available</option>');
    //                         }
    //                     },
    //                     error: function() {
    //                         alert('Error fetching region. Please try again.');
    //                     }
    //                 });
    //             }

    //         }


    //     var table = $('#auditorinstmapping').DataTable({
    //         scrollX: true,
    //         processing: true,
    //         serverSide: false,
    //         lengthChange: false,
    //         ajax: {
    //             url: "{{ route('auditorinstmapping.auditorinstmapping_fetchData') }}",
    //             type: "POST", // Change to GET
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             dataSrc: function(json) {
    //                 console.log(json);
    //                 if (json.data && json.data.length > 0) {
    //                     $('#tableshow').show();
    //                     $('#usertable_wrapper').show();
    //                     $('#no_data').hide(); // Hide custom "No Data" message
    //                     return json.data;
    //                 } else {
    //                     $('#tableshow').hide();
    //                     $('#usertable_wrapper').hide();
    //                     $('#no_data').show(); // Show custom "No Data" message
    //                     return [];
    //                 }
    //             },
    //         },
    //         columns: [{
    //                 data: null,
    //                 render: (_, __, ___, meta) => meta.row + 1, // Serial number column
    //                 className: 'text-end' // Align to the right
    //             },
    //             {
    //                 data: "roletypeelname"
    //             },
    //             {
    //                 data: "deptelname"
    //             },
    //                 {
    //                 data: "regionename"
    //             },
    //             {
    //                 data: "distename"
    //             },
    //    {
    //                 data: "instename"
    //             },
    //             {
    //                 data: "insttname"
    //             },

    //             {
    //                 data: "nodalperson_ename"
    //             },

    //             {
    //                 data: "nodalperson_tname"
    //             },

    //             {
    //                 data: "desigelname"
    //             },
    //             {
    //                 data: "email"
    //             },
    //             {
    //                 data: "mobile"
    //             },
    //             {
    //                 data: "officeaddress_ename"
    //             },
    //             {
    //                 data: "officeaddress_tname"
    //             },
    //             {
    //                 data: "pincode"
    //             },

    //             {
    //                 data: "statusflag",
    //                 render: (data) => {
    //                     if (data === 'Y') {
    //                         return `<button type="button" class="btn btn-primary btn-sm">Active</button>`;
    //                     } else {
    //                         return `<button type="button" class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">Inactive</button>`;
    //                     }
    //                 },
    //                 className: 'text-center'
    //             },
    //             {
    //                 data: "encrypted_instmappingid",
    //                 render: (data) =>
    //                     `<center>
    //                 <a class="btn editicon editauditinsmaprecords" id="${data}">
    //                     <i class="ti ti-edit fs-4"></i>
    //                 </a>
    //             </center>`
    //             }
    //         ]
    //     });


    $.validator.addMethod("mobileRegex", function(value, element) {
        return this.optional(element) || /^[6789]\d{9}$/.test(value);
    });



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#auditor_inst_mapping").validate({

            rules: {
                roletypecode: {
                    required: true,
                },
                deptcode: {
                    required: true,
                },
                regioncode: {
                    required: true,
                },
                distcode: {
                    required: true,
                },
                inst_ename: {
                    required: true,
                },
                inst_tname: {
                    required: true,
                },
                statusflag: {
                    required: true,
                },
                nodal_ename: {
                    required: true,

                },
                nodal_tname: {
                    required: true,

                },
                desigcode: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                mobile: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10,
                    mobileRegex: true,
                },
                address_ename: {
                    required: true,
                },
                address_tname: {
                    required: true,
                },
                pincode: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6,
                },
                audittype: {
                    required: true,
                },
            },
            messages: errorMessages[language],
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
            //     roletypecode: {
            //         required: "Select a role type",
            //     },
            //     deptcode: {
            //         required: "Select a department",
            //     },
            //     regioncode: {
            //         required: "Select a region",
            //     },
            //     distcode: {
            //         required: "Select a district",
            //     },
            //     inst_ename: {
            //         required: "Enter an institution English name",
            //     },
            //     inst_tname: {
            //         required: "Enter an institution Tamil name",
            //     },

            //     nodal_ename: {
            //         required: "Enter Nodal Person English Name",
            //     },
            //     nodal_tname: {
            //         required: "Enter Nodal Person Tamil Name",
            //     },
            //     desigcode: {
            //         required: "Select a designation",
            //     },
            //     email: {
            //         required: "Enter an email address",
            //         email: "Please enter a valid email address",
            //     },
            //     mobile: {
            //         required: "Enter a mobile number",
            //         digits: "Mobile number must contain only digits",
            //         minlength: "Please enter a valid Mobile number",
            //         maxlength: "Please enter a valid Mobile number",
            //     },
            //     address_ename: {
            //         required: "Enter Address English Name",
            //     },
            //     address_tname: {
            //         required: "Enter Address Tamil Name",
            //     },
            //     pincode: {
            //         required: "Enter a pincode",
            //         digits: "Pincode must contain only digits",
            //         minlength: "Enter a Valid Pincode",
            //         maxlength: "Enter a Valid Pincode",
            //     },
            //     status: {
            //         required: "Select a status flag",
            //     },
            // },

        });


        // reset_form();


        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#auditor_inst_mapping").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#auditor_inst_mapping').serializeArray();


                $.ajax({
                    url: "{{ route('auditorinstmapping_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            // passing_alert_value('Confirmation', response.message,
                            //     'confirmation_alert', 'alert_header', 'alert_body',
                            //     'confirmation_alert');
                            // //table.ajax.reload();
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });

                            initializeDataTable(window.localStorage.getItem('lang'));


                        } else if (response.error) {
                            // Handle errors if needed
                            //console.log(response.error);
                        }
                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {

                            // getLabels_jsonlayout([{
                            //         id: ,
                            //         key: errorMessage
                            //     }], "N")
                            //     .then((text) => {
                            //         passing_alert_value("Confirmation", Object.values(text)[
                            //                 0],
                            //             "confirmation_alert", "alert_header",
                            //             "alert_body",
                            //             "confirmation_alert");
                            //     });
                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                let alertMessage = Object.values(text)[0] ||
                                    "Error Occured";
                                passing_alert_value('Confirmation', alertMessage,
                                    'confirmation_alert', 'alert_header',
                                    'alert_body', 'confirmation_alert');
                            });
                        }


                        // Optionally, log the error to console for debugging
                        // console.error('Error details:', xhr, status, error);
                    }
                });

            } else {

            }



        });

    }).catch(error => {
        // console.error("Failed to load JSON data:", error);
    });

    function auditinstmappingForm(audinsmap) {
        $('#display_error').hide();
        // change_button_as_update('auditor_inst_mapping', 'action', 'buttonaction', 'display_error', '', '');
        $('#roletypecode').val(audinsmap.roletypecode).trigger('change');
        $('#audinstmappingcode').val(audinsmap.encrypted_instmappingid);
        populateStatusFlag(audinsmap.statusflag);
        $('#deptcode').val(audinsmap.deptcode).select2();
        $('#instmappingcode').val(audinsmap.instmappingcode);
        $('#distcode').val(audinsmap.distcode);
 	//$('#distcode').val(audinsmap.distcode);
	$('#distcode').val(audinsmap.distcode).trigger('change');


        $('#inst_ename').val(audinsmap.instename);
        $('#inst_tname').val(audinsmap.insttname);

        $('#nodal_ename').val(audinsmap.nodalperson_ename);
        //  alert(audinsmap.nodalperson_ename);
        $('#nodal_tname').val(audinsmap.nodalperson_tname);
        $('#email').val(audinsmap.email);
        $('#mobile').val(audinsmap.mobile);
        $('#address_ename').val(audinsmap.officeaddress_ename);
        $('#address_tname').val(audinsmap.officeaddress_tname);
        $('#pincode').val(audinsmap.pincode);


        getRegionBasedOnDept(audinsmap.deptcode, audinsmap.regioncode, audinsmap.audittype);
        getDesignationBasedOnDept(audinsmap.deptcode, audinsmap.nodalperson_desigcode);


        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editauditinsmaprecords', function() {
        const id = $(this).attr('id');
        // alert(id);
        if (id) {
            reset_form();
            $('#audinstmappingcode').val(id);

            $.ajax({
                url: "{{ route('auditorinstmapping.auditorinstmapping_fetchData') }}",
                method: 'POST',
                data: {
                    instmappingid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('auditor_inst_mapping', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            auditinstmappingForm(response.data[0]); // Populate form with data

                        } else {
                            alert('Auditor institution mapping data is empty');
                        }
                    } else {
                        alert('Auditor institution mapping not found');
                    }
                },
                error: function(xhr) {
                    //console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        $('#auditor_inst_mapping')[0].reset();
        $('#auditor_inst_mapping').validate().resetForm();
        //change_button_as_insert('auditor_inst_mapping', 'action', 'buttonaction', 'display_error', '', '');
        $('#deptcode').val(null).trigger('change');
        $('#roletypecode').val(null).trigger('change');


        getRegionBasedOnDept('', selectedRegioncode = null, selectedaudittypecode = null);
        getDesignationBasedOnDept('', selectedCatcode = null);

        changeButtonAction('auditor_inst_mapping', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
