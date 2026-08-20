@section('content')
    @extends('index2')
    @include('common.alert')
    @php
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;
        $make_dept_disable = $deptcode ? 'disabled' : '';

    @endphp
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang " key="irregularitiessubcat_head">Irregularities SubCategory</div>
                <div class="card-body">
                    <form id="irregularitiessubcategoryform" name="irregularitiessubcategoryform">
                        @csrf
                        <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="irregularitiescode" for="irregularitiescode">Irregularities</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="irregularitiescode" name="irregularitiescode" onchange="getCategoriesBasedOnDept(this.value,'');">
                                   <option value="" data-name-en="---Select Irregularities---"
                                        data-name-ta="---இயல்பு மீறல்கள்களைத் தேர்ந்தெடுக்கவும்---">---Select Irregularities---</option>


                                    @if (!empty($irr) && count($irr) > 0)
                                        @foreach ($irr as $irregularities)
                                            <option value="{{ $irregularities->irregularitiescode }}"
                                                data-name-en="{{ $irregularities->irregularitieselname }}"
                                                data-name-ta="{{ $irregularities->irregularitiestlname }}">
                                                {{ $irregularities->irregularitieselname }}
                                            </option>
                                        @endforeach
                                    @else
                                       <option disabled data-name-en="No Irregularities Available"
                                            data-name-ta="இயல்பு மீறல்கள் எதுவும் இல்லை">No Irregularities Available</option>

                                    @endif
                                </select>
                            </div>






                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="irregularitiescatcode" for="irregularitiescatcode">Irregularities Category</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="irregularitiescatcode" name="irregularitiescatcode">
                                    <option value="" data-name-en="---Select Category---"
                                        data-name-ta="---வகை பெயரைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>


                                    <!-- @if (!empty($irrcat) && count($irrcat) > 0)
                                        @foreach ($irrcat as $irregularities)
                                            <option value="{{ $irregularities->irregularitiescatcode }}"
                                                data-name-en="{{ $irregularities->irregularitiescatelname }}"
                                                data-name-ta="{{ $irregularities->irregularitiescattlname }}">
                                                {{ $irregularities->irregularitiescatelname }}
                                            </option>
                                        @endforeach
                                    @else-->
                                         <option disabled data-name-en="No Category Available"
                                            data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>
                                    <!-- @endif  -->
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiessubcatesname" for="irregularitiessubcatesname">Subcategory Short Name in English</label>
                                <input type="text" class="form-control name" id="irregularitiessubcatesname" maxlength='10'
                                    data-placeholder-key="irregularitiessubcatesname" name="irregularitiessubcatesname" required />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiessubcatelname" for="irregularitiessubcatelname">Subcategory Long Name in English</label>
                                <input type="text" class="form-control name" id="irregularitiessubcatelname" maxlength='255'
                                    data-placeholder-key="irregularitiessubcatelname" name="irregularitiessubcatelname" required />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiessubcattsname" for="irregularitiessubcattsname">Subcategory Short Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiessubcattsname" maxlength='15'
                                    data-placeholder-key="irregularitiessubcattsname" name="irregularitiessubcattsname" required />
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="irregularitiessubcattlname" for="irregularitiessubcattlname">Subcategory Long Name in Tamil</label>
                                <input type="text" class="form-control name" id="irregularitiessubcattlname" maxlength='255'
                                    data-placeholder-key="irregularitiessubcattlname" name="irregularitiessubcattlname" required />
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="active_sts_flag" for="status">Active
                                    Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="statusYes" name="statusflag"
                                            value="Y" checked required />
                                        <label class="form-check-label lang" key="statusyes" for="statusYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="statusNo" name="statusflag"
                                            value="N" required />
                                        <label class="form-check-label lang" key="statusno" for="statusNo">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mx-auto text-center">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <input type="hidden" name="irregularitiessubcatid" id="irregularitiessubcatid" value="" />
                                <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save</button>
                                <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                    onclick="reset_form()">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card_border">
                <div class="card-header card_header_color lang" key="irregularitiessubcat_table">Irregularities Category Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="irregularitiessubcattable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th class="lang align-middle text-center" key="irregularitiescode">Irregularities</th> 
                                         <th class="lang align-middle text-center" key="irregularitiescatcode">Irregularities Category</th> 
                                        <th class="lang align-middle text-center" key="irregularitiessubcatesname">Subcategory Short Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitiessubcatelname">Subcategory Long Name in English
                                        </th>
                                        <th class="lang align-middle text-center" key="irregularitiessubcattsname">Subcategory Short Name in Tamil
                                           </th>
                                        <th class="lang align-middle text-center" key="irregularitiessubcattlname">Subcategory Long Name in Tamil
                                            </th>
                                        <th class="lang align-middle text-center" key="statusflag">Status</th>
                                        <th class="all lang align-middle text-center" key="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='no_data' class='hide_this'>
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

    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>


    <script>
        let table;
        let dataFromServer = [];

        var sessiondeptcode = ' <?php echo $deptcode; ?>';

        $(document).ready(function() {
            $('#irregularitiessubcategoryform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            var lang = getLanguage();
            initializeDataTable(lang)

        });


        $('#translate').change(function() {
            var lang = getLanguage('Y');
            // change_lang_for_page(lang);
            updateTableLanguage(lang);
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'irregularitiessubcategoryform');
        });

        function initializeDataTable(language) {
            $.ajax({
                url: "{{ route('irregularitiessubcategory.irregularitiessubcategory_fetchData') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: "json",
                success: function(json) {
                   // console.log("Success Response:", json);
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
            const irregularitiescatColumn = language === 'ta' ? 'irregularitiescattlname' : 'irregularitiescatelname';
            const irregularitiesColumn = language === 'ta' ? 'irregularitiestlname' : 'irregularitieselname';

            if ($.fn.DataTable.isDataTable('#irregularitiessubcattable')) {
                $('#irregularitiessubcattable').DataTable().clear().destroy();
            }

            table = $('#irregularitiessubcattable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "data": dataFromServer,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                        </div>`;
                        },
                        className: 'text-wrap text-end',
                        type: "num"
                    },
                    {
                        data: irregularitiesColumn,
                        title: columnLabels?.[irregularitiesColumn]?.[language],
                        render: function(data, type, row) {
                            return row[irregularitiesColumn] || '-';
                        },
                        className: ' text-start text-wrap' // Removed col-1
                    },
                    {
                        data: irregularitiescatColumn,
                        title: columnLabels?.[irregularitiescatColumn]?.[language],
                        render: function(data, type, row) {
                            return row[irregularitiescatColumn] || '-';
                        },
                        className: ' text-start text-wrap' // Removed col-1
                    },
                    {
                        data: "irregularitiessubcatesname",
                        title: columnLabels?.["irregularitiessubcatesname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiessubcatesname || '-';
                        }
                    },
                    {
                        data: "irregularitiessubcatelname",
                        title: columnLabels?.["irregularitiessubcatelname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiessubcatelname || '-';
                        }
                    },
                    {
                        data: "irregularitiessubcattsname",
                        title: columnLabels?.["irregularitiessubcattsname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiessubcattsname || '-';
                        }
                    },
                    {
                        data: "irregularitiessubcattlname",
                        title: columnLabels?.["irregularitiessubcattlname"]?.[language],
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.irregularitiessubcattlname || '-';
                        }
                    },
                    {
                        data: "statusflag",
                        title: columnLabels?.["statusflag"]?.[language],
                        render: function(data) {
                            let activeText = arrLang?.[language]?.["active"] || "Active";
                            let inactiveText = arrLang?.[language]?.["inactive"] || "Inactive";

                            return data === 'Y' ?
                                `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                                `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                        },
                        className: "text-center d-none d-md-table-cell extra-column  noExport"
                    },
                    {
                        data: "encrypted_irregularitiessubcatid",
                        title: columnLabels?.["actions"]?.[language],
                        render: (data) =>
                            `<center><a class="btn editicon editchargedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                        className: "text-center noExport "
                    }
                ],

                "initComplete": function(settings, json) {
                    $("#irregularitiessubcattable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            const mobileColumns = ["irregularitiessubcatesname", "irregularitiessubcatelname", "irregularitiessubcattsname", "irregularitiessubcattlname", "statusflag"];
            setupMobileRowToggle(mobileColumns);

            //    updatedatatable("en", "callforrecordstable", "Call for Records");
            updatedatatable(language, "irregularitiessubcattable"); // Title: "Call for Records"
        }

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#irregularitiessubcattable')) {
                $('#irregularitiessubcattable').DataTable().clear().destroy();
            }
            renderTable(language);
        }



        function getCategoriesBasedOnDept(irregularitiescode, selectedCatcode = null) {
        const catcodeDropdown = $('#irregularitiescatcode');
        const lang = getLanguage();

        catcodeDropdown.html(`
            <option value="" data-name-en="--Select Category Name--" data-name-ta="--??? ??????? ?????????????????--">
                ${lang === 'ta' ? '--??? ??????? ?????????????????--' : 'Select Category Name'}
            </option>
            `);
        // alert(lang);
        if (!irregularitiescode) {
            irregularitiescode = $("#irregularitiescatcode").val();
        }

        if (!irregularitiescode) {
            catcodeDropdown.append(`
    <option value="" disabled data-name-en="No Category Available" data-name-ta="??? ????? ?????????????">
        ${lang === 'ta' ? '??? ????? ?????????????' : 'No Category Available'}
    </option>
`);
            // change_lang_for_page(lang);
            // return;
        }

        if (irregularitiescode) {
            $.ajax({
                url: "/getirrcategoriesbasedonirr",
                type: "POST",
                data: {
                    irregularitiescode: irregularitiescode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    if (response.length > 0) {
                        // alert(lang);
                        response.forEach(category => {
                            catcodeDropdown.append(`
                        <option value="${category.irregularitiescatcode}"
                            data-name-en="${category.irregularitiescatelname}"
                            data-name-ta="${category.irregularitiescattlname}"
                            ${category.irregularitiescatcode === selectedCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? category.irregularitiescattlname : category.irregularitiescatelname}
                        </option>
                    `);
                        });
                    } else {
                        catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="??? ????? ?????????????">
                        ${lang === 'ta' ? '??? ????? ?????????????' : 'No Category Available'}
                    </option>
                `);
                    }

                    // change_lang_for_page(lang); // Update dropdown text after data is loaded
                },
                error: function() {
                    alert('Error fetching categories. Please try again.');
                }
            });
        }
    }






        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#irregularitiessubcategoryform").validate({

                rules: {
                    irregularitiescode :{
                        required: true
                    },
                    irregularitiescatcode: {
                        required: true,
                    },
                    irregularitiessubcatesname: {
                        required: true
                    },
                    irregularitiessubcatelname: {
                        required: true
                    },
                    irregularitiessubcattlname: {
                        required: true
                    },
                    irregularitiessubcattsname: {
                        required: true
                    },
                    statusflag: {
                        required: true
                    },
                },
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
                if ($("#irregularitiessubcategoryform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var formData = $('#irregularitiessubcategoryform').serializeArray();
                    // var deptcode = $('#deptcode').val();
                    // if ($('#deptcode').prop('disabled')) {

                    //     formData.push({
                    //         name: 'deptcode',
                    //         value: deptcode
                    //     });
                    // }
                    $.ajax({
                        url: "{{ route('irregularitiessubcategory.irregularitiessubcategory_insertupdate') }}",
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
            reset_form();

        }).catch(error => {
            console.error("Failed to load JSON data:", error);
        });


        // Handle Edit Button Click
        $(document).on('click', '.editchargedel', function() {
            const id = $(this).attr('id');
            if (id) {
                reset_form();
                $('#irregularitiessubcatid').val(id); // Set the ID field directly

                $.ajax({
                    url: "{{ route('irregularitiessubcategory.irregularitiessubcategory_fetchData') }}",
                    method: 'POST',
                    data: {
                        irregularitiessubcatid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                changeButtonAction('irregularitiessubcategoryform', 'action', 'buttonaction',
                                    'reset_button', 'display_error', @json($updatebtn),
                                    @json($clearbtn), @json($update))
                                populateChargeForm(response.data[0]); // Populate form with data
                            } else {
                                alert('irrugularities data is empty');
                            }
                        } else {
                            alert('irrugularities not found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText || 'Unknown error');
                    }
                });
            }
        });




        function populateChargeForm(charge) {
            $('#display_error').hide();
            change_button_as_update('irregularitiessubcategoryform', 'action', 'buttonaction', 'display_error', '', '');
            $('#irregularitiescode').val(charge.irregularitiescode).select2();
            $('#irregularitiescatcode').val(charge.irregularitiescatcode).select2();
            $('#irregularitiessubcatesname').val(charge.irregularitiessubcatesname);
            $('#irregularitiessubcatelname').val(charge.irregularitiessubcatelname);
            $('#irregularitiessubcattsname').val(charge.irregularitiessubcattsname);
            $('#irregularitiessubcattlname').val(charge.irregularitiessubcattlname);
            $('#irregularitiessubcatid').val(charge.encrypted_irregularitiessubcatid);
            populateStatusFlag(charge.statusflag);
           
                getCategoriesBasedOnDept(charge.irregularitiescode, charge.irregularitiescatcode);
                
 updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }

        function populateStatusFlag(statusflag) {
            if (statusflag === "Y") {
                document.getElementById('statusYes').checked = true;
            } else if (statusflag === "N") {
                document.getElementById('statusNo').checked = true;
            }
        }

        function reset_form() {
            
            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                $('#irregularitiessubcatesname,#irregularitiessubcatelname', '#irregularitiessubcattsname', '#irregularitiessubcattlname').val();

            } else {
                $('#irregularitiescatcode').val(null).select2();
                $('#irregularitiescode').val(null).select2();

            }

            changeButtonAction('irregularitiessubcategoryform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            // change_button_as_insert('irregularitiessubcategoryform', 'action', 'buttonaction', 'display_error', '', '');
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>

@endsection
