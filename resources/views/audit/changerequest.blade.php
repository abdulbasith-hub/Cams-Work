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
            <div class="card-header card_header_color lang" key="changerequest_head">Change Request</div>
            <div class="card-body">
                <form id="changerequestform" name="changerequestform">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="auditplanid" id="auditplanid" value="" />


                        <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class="form-select mr-sm-2 select2  lang-dropdown"  id="deptcode"   name="deptcode"
                                onchange="getRegionBasedOnDept(this.value,'','');">
                                <option value="" data-name-en="Select a Department"
                                    data-name-ta="?????? ?????? ????">Select a Department</option>
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

                        <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="Region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 "  id="regioncode" name="regioncode"
                                    onchange="getDistrictBasedOnRegion('','','','')">
                                    <option value="" data-name-en="Select a Region"
                                    data-name-ta="??????? ?????????????????">Select Region</option>

                                    <option value="" data-name-en="No Region Available" data-name-ta="????? ?????????????" disabled >No Region Available</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2 "  id="distcode" name="distcode">
                                    <option value="" data-name-en="Select a District"
                                    data-name-ta="?????????? ?????????????????">Select District</option>

                                    <option value="" data-name-en="Select a District" data-name-ta="???????? ?????????????" disabled id="no-district-option">No District Available</option>


                                </select>
                            </div>
                            <div class="col-md-4 mb-2" id="updatediv">
                                <label class="form-label required  lang" key="" for="dept">Select detail to update</label>

                                <select class="form-select mr-sm-2 select2  lang-dropdown"  id="updatefield"  onchange="getinstitutionBasedOndistrict('','','','','')"  name="updatefield">
                                    <option value="" data-name-en="Select detail to update"
                                        data-name-ta="">Select detail to Update</option>

                                            <option value="01">Audit Year</option>
                                                                                       </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Auditable Institution</label>
                                <select class="form-select mr-sm-2 select2 "  id="instmappingcode" name="instmappingcode" onchange="getquarterBasedOninst('','','','')">
                                <option value="" data-name-en="Select Auditable Institution"
                                    data-name-ta="???????????? ?????????????????">Select Auditable Institution</option>

                                <!-- <option value="" disabled id="" data-name-en="No Institution Available"
                                    data-name-ta="???????? ?????????????">No Institution Available</option> -->


                                </select>
                            </div>

                            <div class="col-md-4">
                            <label class="form-label required lang" for="financialyear" key="financialyear">Financial Year</label>
                                <select class="form-select mr-sm-2 select2" id="financialyear"
                                    name="financialyear" onchange="getquarterbasedonyear('','')">

                                    <option value="" data-name-en="---Select Financial year---"
                                        data-name-ta="---நிதி ஆண்டை தேர்வு செய்க---">Select Financial year</option>

                                </select>
                            </div>



                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="quartercode"
                                    for="quartercode">Audit Quarter</label>
                                <select class="form-select mr-sm-2 select2 "  id="quartercode" name="quartercode" onchange="getyearBasedOnquarter('','','')">
                                <option value="" data-name-en="Select Quarter"
                                    data-name-ta="">Select Quarter</option>

                                </select>
                            </div>




                            <div class="col-md-4 mb-1 hide_this" id="auditYearDiv">
                                    <label class="form-label lang required" key="" >Audit Year</label>
                                    <select name="yearselected[]" id="yearselected" class="select2 form-control"
                                        multiple="multiple" data-placeholder-key="year_ph">
                                        <option value="" disabled>Select Year</option>

                                    </select>
                                    <div id="auditYearError" style="color: red; display: none;">Please select an Audit Year.</div>

                            </div>
                            <div class="col-md-4 mb-1 hide_this" id="annathanamYearDiv">
                                <label class="form-label lang required" key="" >Annadhanam Year</label>
                                <select name="annathnamyear[]" id="annathnamyear" class="select2 form-control"
                                    multiple="multiple" data-placeholder-key="">

                                </select>
                                <div id="annathanamyearError" style="color: red; display: none;">Please select an Annathanam Year.</div>

                          </div>




    <script>




    $(document).ready(function () {
        $('#auditYearDiv').hide();

        // Initially hide all optional fields
        toggleFields();

        $('#updatefield').change(function () {
            toggleFields();
        });

        function toggleFields() {
            let selectedVal = $('#updatefield').val();

            $('#auditYearDiv').hide();

            // Show based on selection
            if (selectedVal === '01') {
                $('#auditYearDiv').show();
            }
        }
    });
</script>




                    </div>




                    <div class="row">
                        <div class="col-md-3 mx-auto">
                            <input type="hidden" name="action" id="action" value="" />


                            <button class="btn  mt-3 lang" style="background-color: rgb(2, 98, 175);color: rgb(255, 255, 255); " key="update_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Update</button>
                            <button type="button" class="btn btn-danger mt-3  lang"
                                style="height:35px;font-size: 13px;" key="clear_btn" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">List of Institutes</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="changerequesttable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_num">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang align-middle text-center" key="region">Region
                                    </th>
                                    <th class="lang align-middle text-center" key="district">District</th>

                                    <th class="lang align-middle text-center" key="institution">Institution Name</th>
                                    <th class="lang align-middle text-center" key="">Audit Quarter</th>
                                    <!-- <th class="lang align-middle text-center" key="">From Date</th>
                                    <th class="lang align-middle text-center" key="">To Date</th> -->
                                    <th class="lang align-middle text-center" key="audit_year">Audit Year</th>

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

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>


<script>
    var session_roletypecode = '<?php echo $roleTypeCode; ?>';
    let table;
    let dataFromServer = [];

    $(document).ready(function() {
        $('#changerequestform')[0].reset();
      //  updateSelectColorByValue(document.querySelectorAll(".form-select"));

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
        changeButtonText('action', 'buttonaction', 'reset_button',  @json($updatebtn),@json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'changerequestform');

    });



    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('changerequest.changerequest_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
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
        const departmentcolumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const Regioncolumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtcolumn = language === 'ta' ? 'disttname' : 'distename';
        const Institutioncolumn = language === 'ta' ? 'insttname' : 'instename';


        // Destroy DataTable if it exists
        if ($.fn.DataTable.isDataTable('#changerequesttable')) {
            $('#changerequesttable').DataTable().clear().destroy();
        }

        var table = $('#changerequesttable').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                        </div>`;
                    },
                    className: ' text-wrap text-end w-10',
                    type: "num"
                },
                {
                    data: departmentcolumn,
                    title: columnLabels?.[departmentcolumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentcolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: Regioncolumn,
                    title: columnLabels?.[Regioncolumn]?.[language],
                    render: function(data, type, row) {
                        return row[Regioncolumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: districtcolumn,
                    title: columnLabels?.[districtcolumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row[districtcolumn] || '-';
                    },
                },
                {
                    data: Institutioncolumn,
                    title: columnLabels?.[Institutioncolumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row[Institutioncolumn] || '-';
                    }
                },

                {
                    data: "auditquarter",
                    title: columnLabels?.["auditquarter"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.auditquarter || '-';
                    }
                },
                // {
                //     data: "fromdate",
                //     title: columnLabels?.["fromdate"]?.[language],
                //     className: "d-none d-md-table-cell lang extra-column ",
                //     render: function(data, type, row) {
                //         return row.fromdate || '-';
                //     }
                // },
                // {
                //     data: "todate",
                //     title: columnLabels?.["todate"]?.[language],
                //     className: "d-none d-md-table-cell lang extra-column ",
                //     render: function(data, type, row) {
                //         return row.todate || '-';
                //     }
                // },
                {
                    data: "audit_period",
                    title: columnLabels?.["audit_period"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.audit_period || '-';
                    }
                },

                // {
                //     data: "encrypted_auditplanid",
                //     title: columnLabels?.["actions"]?.[language],
                //     render: (data) =>
                //         `<center><a class="btn editicon editchangereq" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                //     className: "text-center text-wrap noExport"
                // }
            ],
            initComplete: function(settings, json) {
                $("#changerequesttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });

        // ? Include category column to ensure proper mobile display
        const mobileColumns = [departmentcolumn,"subcatename", "subcattname", "statusflag"];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "changerequesttable");
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#changerequesttable')) {
            $('#changerequesttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#changerequesttable')) {
            $('#changerequesttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


     function getquarterBasedOninst(deptcode, instmappingcode, selectedquartercode = null, selectedAuditid = null) {
        const QuartercodeDropdown = $('#quartercode');
        const auditPeriodDropdown = $('#yearselected');
        const financialyearDropdown = $('#financialyear');

        const auditPeriodannDropdown = $('#annathnamyear');

        auditPeriodDropdown.empty();
        auditPeriodannDropdown.empty();
        financialyearDropdown.empty();


        QuartercodeDropdown.html('<option value="">Select Quarter</option>');
        auditPeriodDropdown.html('<option value="">Select Audit year</option>');

    // Fill deptcode and instmappingcode if blank
    if (deptcode == "") {
        deptcode = $("#deptcode").val();
    }
    if (instmappingcode == "") {
        instmappingcode = $("#instmappingcode").val();
    }
    auditplanid=$("#auditplanid").val();


    if (!instmappingcode) {
        $('#yearselected').val([]).select2();
    }


    if (deptcode && instmappingcode) {
        $.ajax({
            url: "/getquarterBasedOninst",
            type: "POST",
            data: {
                deptcode: deptcode,
                instmappingcode: instmappingcode,
                auditplanid:auditplanid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {

		 if (response.annadhanam_only === 'Y') {
                $('#annathanamYearDiv').show();
            } else {
                $('#annathanamYearDiv').hide();
            }


                   financialyearDropdown.append(`
                    <option value="" data-name-en="---Select Financial year---"
                        data-name-ta="---நிதி ஆண்டை தேர்வு செய்க---">
                        Select Financial year
                    </option>
                `);


                    if (response.financialyear && response.financialyear.length > 0) {
                      response.financialyear.forEach(financialyear => {
                        financialyearDropdown.append(
                            `<option value="${financialyear.financialyearcode}"
                                data-name-en="${financialyear.financialyear}"
                                data-name-ta="${financialyear.financialyear}"
                            >${financialyear.financialyear}</option>`
                        );


                    });
                } else {
                      financialyearDropdown.append(`<option value="">No Financial Year</option>`);

                }


            },
            error: function() {
                alert('Error fetching institution. Please try again.');
            }
        });
    }
}









    function getinstitutionBasedOndistrict(deptcode, region, district,updatefield, selecteinstitutioncode = null) {
            // alert('te');
            const institutionDropdown = $('#instmappingcode');
            institutionDropdown.html('<option value="">Select Auditable Institution</option>');
            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }
            if (district == "") {
                var district = $("#distcode").val();
            }
 	    if (updatefield == "") {
                var updatefield = $("#updatefield").val();
            }
            if (!district) {
                institutionDropdown.append("<option value='' data-name-en='No Institution Available' data-name-ta='???????? ?????????????' disabled>No Institution Available</option>");

            }
            if (deptcode && region && district && updatefield) {
                $.ajax({
                    url: "/getinstitutionbasedondistchangerequest",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        updatefield : updatefield,
                        _token: '{{ csrf_token() }}'
                    },

                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(institution => {
                                institutionDropdown.append(
                                    `<option value="${institution.instid}"
                                     data-name-en="${institution.instename}"
                                     data-name-ta="${institution.insttname}" ${
                                    institution.instid === selecteinstitutioncode ? 'selected' : ''
                            }>${institution.instename}</option>`
                                );
                            });
                        } else {
                            institutionDropdown.append("<option value='' data-name-en='No Institution Available' data-name-ta='???????? ?????????????' disabled>No Institution Available</option>");
                        }
                    },
                    error: function() {
                        alert('Error fetching institution. Please try again.');
                    }
                });
            }
        }


        function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            // alert('te');
            const districtDropdown = $('#distcode');
            const institutionDropdown = $('#instmappingcode');

            districtDropdown.html('<option value="">Select District</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#regioncode").val();
                // alert(deptcode);
            }

            if (!region) {
                districtDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No District Available"
                            data-name-ta="???????? ?????????????">
                            ${lang === 'ta' ? '???????? ?????????????' : 'No District Available'}
                    </option>
                `);


            }
            // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');

            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregionchangerequest",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.distcode}"
                                     data-name-en="${district.distename}"
                                   data-name-ta="${district.disttname}" ${
                                    district.distcode === selecteDistrictcode ? 'selected' : ''
                            }>${district.distename}</option>`
                                );
                            });
                        } else {
                            districtDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }

        function getRegionBasedOnDept(deptcode, selectedAuditid) {
                    const districtDropdown = $('#distcode');
                    const regionDropdown = $('#regioncode');
                    const institutionDropdown = $('#instmappingcode');
                    const auditPeriodDropdown = $('#yearselected'); // Make sure this ID matches your HTML
 const quartercodeDropdown = $('#quartercode');


 auditPeriodDropdown.empty();

quartercodeDropdown.html('<option value="">Select Quarter</option>');

                    auditPeriodDropdown.html(`<option value="">${lang === 'ta' ? '????? ??????? ?????? ?????' : 'Select Audit Year'}</option>`);

                    regionDropdown.html(`
                        <option value="" data-name-en="Select Region" data-name-ta="??????? ?????? ????">
                            ${lang === 'ta' ? '??????? ?????? ????' : 'Select Region'}
                        </option>
                    `);

                    districtDropdown.html('<option value="">Select District</option>');
                    institutionDropdown.html('<option value="">Select Audit Office</option>');

                    if (deptcode == "") {
                        var deptcode = $("#deptcode").val();
                    }
                    if (!deptcode) {
                        regionDropdown.append(`
                            <option value="" disabled id="no-region-option"
                                    data-name-en="No Region Available"
                                    data-name-ta="????? ?????????????">
                                    ${lang === 'ta' ? '????? ?????????????' : 'No Region Available'}
                            </option>
                        `);

                        districtDropdown.append('<option value="" disabled>No District Available</option>');
                        institutionDropdown.append('<option value="" disabled>No Institution Available</option>');
                        return;
                    }

                    if (deptcode) {
                        $.ajax({
                            url: "/getregionbasedondeptforchangerequest",
                            type: "POST",
                            data: {
                                deptcode: deptcode,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success && response.data.length > 0) {
                                    response.data.forEach(region => {
                                        regionDropdown.append(
                                            `<option value="${region.regioncode}"
                                            data-name-en="${region.regionename}"
                                            data-name-ta="${region.regiontname}">${region.regionename}</option>`
                                        );
                                    });
                                } else {
                                    regionDropdown.append('<option disabled>No Region Available</option>');
                                }

                                auditPeriodDropdown.html(`<option value="">${lang === 'ta' ? '????? ??????? ?????? ?????' : 'Select Audit Year'}</option>`);

                                if (response.auditperiod && response.auditperiod.length > 0) {
                                    response.auditperiod.forEach(period => {
                                            auditPeriodDropdown.append(
                                                `<option value="${period.auditperiodid}">
                                                    ${period.audit_period}
                                                </option>`
                                            );

                                    });
                                } else {
                                    auditPeriodDropdown.append(
                                        `<option disabled>${lang === 'ta' ? '????? ????? ?????' : 'No Audit Period Available'}</option>`
                                    );
                                }

                                // auditPeriodDropdown.select2();
                            },
                            error: function() {
                                alert('Error fetching region. Please try again.');
                            }
                        });
                    }
                }



    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#changerequestform").validate({

            rules: {
                deptcode: {
                    required: true,
                },
                regioncode: {
                    required: true,
                },
                distcode: {
                    required: true,
                },
                instmappingcode: {
                    required: true,
                },
                quartercode: {
                    required: true,
                },
                financialyear: {
                    required: true,
                },
                "yearselected[]": {
                    required: true,
                },
 		        "annathnamyear[]" : {
                    required: true,
                },

                updatefield:{
                    required: true,

                }
            },
            messages: errorMessages[language],
            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },


        });




        $("#buttonaction").on("click", function(event) {
            event.preventDefault();


            if ($("#changerequestform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#changerequestform').serializeArray();


                $.ajax({
                    url: "{{ route('changerequest.changerequest_insertupdate') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form();

                           passing_alert_value('Confirmation', response.message, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                            initializeDataTable(window.localStorage.getItem('lang'));


                        } else if (response.error) {

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
                                passing_alert_value('Confirmation', alertMessage,
                                    'confirmation_alert', 'alert_header',
                                    'alert_body', 'confirmation_alert');
                            });
                        }



                    }
                });

            } else {

            }



        });

    }).catch(error => {
        // console.error("Failed to load JSON data:", error);
    });

     function getquarterbasedonyear(instmappingcode, financialyear ) {
        const QuartercodeDropdown = $('#quartercode');


    if (instmappingcode == "") {
        instmappingcode = $("#instmappingcode").val();
    }

    if (financialyear == "") {
        financialyear = $("#financialyear").val();
    }

    QuartercodeDropdown.empty();

     QuartercodeDropdown.append(`<option>${lang === 'ta' ? '' : 'Select Quarter'}</option>`);


    if (instmappingcode) {
        $.ajax({
            url: "/getquarterbasedonyear",
            type: "POST",
            data: {
                financialyear: financialyear,
                instmappingcode: instmappingcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {


                if (response.quarter && response.quarter.length > 0) {
                    response.quarter.forEach(quarter => {
                        QuartercodeDropdown.append(
                            `<option value="${quarter.planmappingid}"
                                data-name-en="${quarter.planname}"
                                data-name-ta="${quarter.planname}"
                            >${quarter.planname}</option>`
                        );


                    });
                } else {
                    QuartercodeDropdown.append(`<option disabled>${lang === 'ta' ? '' : 'No Quarter Available'}</option>`);

                }

            },
            error: function() {
                alert('Error fetching institution. Please try again.');
            }
        });
    }
     }



function getyearBasedOnquarter(deptcode,instmappingcode,auditquartercode) {
    const auditPeriodDropdown = $('#yearselected');
	 const annathanamyearDropdown = $('#annathnamyear');

    annathanamyearDropdown.empty();

    auditPeriodDropdown.empty();

    if (deptcode == "") {
        deptcode = $("#deptcode").val();
    }
    if (auditquartercode == "") {
        auditquartercode = $("#quartercode").val();
    }

    if (instmappingcode == "") {
        instmappingcode = $("#instmappingcode").val();
    }
    auditplanid=$("#auditplanid").val();

    if (deptcode && auditquartercode) {
        $.ajax({
            url: "/getquarterBasedOninst",
            type: "POST",
            data: {
                deptcode: deptcode,
                auditquartercode: auditquartercode,
                instmappingcode: instmappingcode,
                auditplanid:auditplanid,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {


                auditPeriodDropdown.find('option:not([disabled])').remove();

                const yearselectedArray = response.yearselected;


                if (response.auditperiod && response.auditperiod.length > 0) {
                            response.auditperiod.forEach(period => {
                                const isSelected = yearselectedArray.includes(Number(period.auditperiodid));
                                auditPeriodDropdown.append(
                                    `<option value="${period.auditperiodid}" ${isSelected ? 'selected' : ''}>
                                        ${period.audit_period}
                                    </option>`
                                );
                                $('#auditplanid').val(response.auditplanid);
                            });

                             auditPeriodDropdown.select2();

                } else {
                            auditPeriodDropdown.append(
                                `<option disabled>${lang === 'ta' ? '????? ????? ?????' : 'No Audit Period Available'}</option>`
                            );
                }


 if (response.annathanamyear && response.annathanamyear.length > 0) {

                    const annathanamyearselected = response.annathanamyearselected
                        ? Object.values(response.annathanamyearselected).map(Number)
                        : [];


                     response.annathanamyear.forEach(item => {

                        const isSelected = annathanamyearselected.includes(
                            Number(item.auditperiodid)
                        );

                        annathanamyearDropdown.append(
                            `<option value="${item.auditperiodid}" ${isSelected ? 'selected' : ''}>
                                ${item.audit_period}
                            </option>`
                        );
                    });

                    annathanamyearDropdown.select2('destroy').select2();

                    } else {
                    annathanamyearDropdown
                        .empty()
                        .append(`<option disabled>No Annathanam Year Available</option>`);
                    }

            },
            error: function() {
                alert('Error fetching institution. Please try again.');
            }
        });
    }
}
    // function ChangerequestForm(changereq) {
    //     $('#display_error').hide();
    //     $('#auditplanid').val(changereq.encrypted_auditplanid);
    //     populateStatusFlag(changereq.statusflag);
    //     $('#deptcode').val(changereq.deptcode).select2();
	//     $('#quartercode').val(changereq.auditquartercode).select2();
	//     $('#regioncode').val(changereq.regioncode).select2();
	//     $('#distcode').val(changereq.distcode).select2();
	//     $('#instmappingcode').val(changereq.instid).select2();


    // getRegionBasedOnDept(changereq.deptcode,changereq.regioncode,changereq.auditquartercode,changereq.yearkeys);
    // getDistrictBasedOnRegion(changereq.deptcode, changereq.regioncode, changereq.distcode);
    // getinstitutionBasedOndistrict(changereq.deptcode, changereq.regioncode, changereq.distcode,
    // changereq.instid);

    //     updateSelectColorByValue(document.querySelectorAll(".form-select"));
    // }

    // $(document).on('click', '.editchangereq', function() {
    //     const id = $(this).attr('id');

    //     if (id) {
    //         reset_form();
    //         $('#auditplanid').val(id);

    //         $.ajax({
    //             url: "{{ route('changerequest.changerequest_fetchData') }}",
    //             method: 'POST',
    //             data: {
    //                 auditplanid: id
    //             },
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     if (response.data && response.data.length > 0) {
    //                         changeButtonAction('changerequestform', 'action', 'buttonaction',
    //                             'reset_button', 'display_error', @json($updatebtn),
    //                             @json($clearbtn), @json($update))
    //                             ChangerequestForm(response.data[0]); // Populate form with data

    //                     } else {
    //                         alert('Change Request data is empty');
    //                     }
    //                 } else {
    //                     alert('Change Request mapping not found');
    //                 }
    //             },
    //             error: function(xhr) {
    //             }
    //         });
    //     }
    // });


    // function populateStatusFlag(statusflag) {
    //     if (statusflag === "Y") {
    //         document.getElementById('statusYes').checked = true;
    //     } else if (statusflag === "N") {
    //         document.getElementById('statusNo').checked = true;
    //     }
    // }

    function reset_form() {
        $('#changerequestform')[0].reset();
        $('#changerequestform').validate().resetForm();
        $('#deptcode').val(null).select2();
        $('#auditYearError').hide();
         $('#updatefield').val(null).select2();
         $('#auditYearDiv').hide();
	$('#annathanamYearDiv').hide();
        $('#annathnamyear').val(null).select2();
        // $('#auditYearDiv').hide();
        // $('#todate').hide();

        getRegionBasedOnDept(null);
        getDistrictBasedOnRegion(null);
        getinstitutionBasedOndistrict(null);
        getquarterBasedOninst(null);

        changeButtonAction('changerequestform', 'action', 'buttonaction', 'reset_button', 'display_error',  @json($updatebtn),
        @json($clearbtn), @json($update))

      //  updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection
