@section('content')
@section('title', 'Hria Zone')
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
            <div class="card-header card_header_color lang" key="">HRIA Circle </div>
            <div class="card-body">
                <form id="circleform" name="circleform">
                    <input type="hidden" name="circleid" id="circleid">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3 " id="distdediv">
                            <label class="form-label required lang" key="district" for="distcode">District</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                            onchange="getZoneNameBasedOnDist(this.value,'')">
                                <option value="" data-name-en="---Select District---"
                                    data-name-ta="--- மாவட்டத்தைத் தேர்ந்தெடுக்கவும்.---">---Select District---</option>

                                    @foreach ($district as $dist)
                                        <option value="{{ $dist->distcode }}"
                                            data-name-en="{{ $dist->distename }}"
                                            data-name-ta="{{ $dist->disttname }}">
                                            {{ $dist->distename }}
                                        </option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3 " id="distdediv">
                            <label class="form-label required lang" key="" for="zonecode">Select Zone Name</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="zonecode" name="zonecode"
                               >
                                <option value="" data-name-en="---Select Zone---"
                                    data-name-ta="--- மண்டலத்தைத் தேர்ந்தெடுக்கவும் ---">---Select Zone---</option>


                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="circleename" for="circleename">Circle English
                                name</label>
                            <input type="text" data-placeholder-key="circleename" class="form-control removesplchar_text"
                                id="circleename" name="circleename" required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="circletname" for="circletname">Circle Tamil
                                name</label>
                            <input type="text" class="form-control removesplchar_text" id="circletname" name="circletname"
                                data-placeholder-key="circletname" required />
                        </div>






                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag" for="status">Status</label>
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
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>


    <div class="card card_border">
        <div class="card-header card_header_color lang" key="">Circle Details</div>
        <div class="card-body"><br>
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="circletable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th class="lang align-middle text-center" key=""> District </th>
                                <th class="lang align-middle text-center" key=""> Zone Name </th>
                                <th class="lang align-middle text-center" key="circleename">Circle Name In English</th>
                                <th class="lang align-middle text-center" key="circletname">Circle Name In Tamil </th>
                                <th class="lang align-middle text-center" key="statusflag">Status</th>
                                <th class="lang all align-middle text-center" key="action">Action</th>
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
<style>

</style>
<!-- Download Button Start -->

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- Download Button Start -->
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- Download Button End -->
<script>
    let table;
    let dataFromServer = [];
    var sessiondeptcode = ' <?php echo $deptcode; ?>';


    $(document).ready(function() {

        restrictSpecialChars('.removesplchar_text')
        $('#circleform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'circleform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('circle_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
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
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show();
            }
        });
    }

    function getZoneNameBasedOnDist(distcode,selectedzonecode = null) {


const zoneDropdown = $('#zonecode');

// Default option
zoneDropdown.html(`
    <option value=""
        data-name-en="---Select Zone---"
        data-name-ta="---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---">
        ${lang === 'ta' ? '---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Zone---'}
    </option>
`);

if (!distcode) {
    zoneDropdown.append(`
        <option value="" disabled
            data-name-en="No Zone Available"
            data-name-ta="மண்டலம் கிடைக்கவில்லை">
            ${lang === 'ta' ? 'மண்டலம் கிடைக்கவில்லை' : 'No Zone Available'}
        </option>
    `);
    return;
}

$.ajax({
    url: "/fetchzonebasedondist",
    type: "POST",
    data: { distcode: distcode },
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {

        zoneDropdown.empty();

        zoneDropdown.append(`
            <option value=""
                data-name-en="---Select Zone---"
                data-name-ta="---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---மண்டலத்தைத் தேர்ந்தெடுக்கவும்---' : '---Select Zone---'}
            </option>
        `);

        if (response.success && response.data.length > 0) {

            response.data.forEach(zone => {
                zoneDropdown.append(`
                    <option value="${zone.zonecode}"
                        data-name-en="${zone.zoneename}"
                        data-name-ta="${zone.zonetname}"
                        ${selectedzonecode == zone.zonecode ? 'selected' : ''}>
                        ${lang === 'ta' ? zone.zonetname : zone.zoneename}
                    </option>
                `);
            });

        } else {

            zoneDropdown.append(`
                <option disabled
                    data-name-en="No Zone Available"
                    data-name-ta="மண்டலம் கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'மண்டலம் கிடைக்கவில்லை' : 'No Zone Available'}
                </option>
            `);
        }
    },
    error: function (xhr, status, error) {
        console.error("Error fetching zones:", error);
        alert('Error fetching zones. Please try again.');
    }
});
}





    function renderTable(language) {
        const zoneColumn = language === 'ta' ? 'zonetname' : 'zoneename';
        const distColumn = language === 'ta' ? 'disttname' : 'distename';

        if ($.fn.DataTable.isDataTable('#circletable')) {
            $('#circletable').DataTable().clear().destroy();
        }

        var table = $('#circletable').DataTable({
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
                    className: 'text-wrap text-end',
                    type: "num"
                },

                {
                    data: distColumn,
                    title: columnLabels?.[distColumn]?.[language],
                    render: function(data, type, row) {
                        return row[distColumn] || '-';
                    },
                    className: 'text-wrap text-start',
                },

                {
                    data: zoneColumn,
                    title: columnLabels?.[zoneColumn]?.[language],
                    render: function(data, type, row) {
                        return row[zoneColumn] || '-';
                    },
                    className: 'text-wrap text-start',
                },

                {
                    data: "circleename",
                    title: columnLabels?.["circleename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.circleename || '-';
                    }
                },
                {
                    data: "circletname",
                    title: columnLabels?.["circletname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.circletname || '-';
                    }
                },


                {
                    data: "statusflag",
                    title: columnLabels?.["statusflag"]?.[window.localStorage.getItem("lang")] || "Status",
                    render: function(data) {
                        let activeText = arrLang?.[language]?.["active"] || "Active";
                        let inactiveText = arrLang?.[language]?.["inactive"] || "InActive";

                        return data === 'Y' ?
                            `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                            `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                },

                {
                    data: "encrypted_circleid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) =>
                        `<center><a class="btn editicon editrzonedel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                    className: "text-center noExport text-wrap"
                }
            ],
            "initComplete": function(settings, json) {
                $("#circletable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["circleename", "circletname", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "circletable");
    }


    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#circletable')) {
            $('#circletable').DataTable().clear().destroy();
        }
        renderTable(language);
    }





    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#circleform").validate({
            rules: {
                distcode: {
                    required: true,
                },
                zonecode: {
                    required: true,
                },
                circleename: {
                    required: true
                },
                circletname: {
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
            if ($("#circleform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formData = $('#circleform').serializeArray();
                $.ajax({
                    url: "{{ route('circle_insertupdate') }}",
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
            } else {}
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    $(document).on('click', '.editrzonedel', function() {
        const id = $(this).attr('id');
        if (id) {
            reset_form();
            $('#circleid').val(id);

            $.ajax({
                url: "{{ route('circle_fetchData') }}",
                method: 'POST',
                data: {
                    circleid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('circleform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            populatecircleform(response.data[0]); // Populate form with data
                        } else {
                            alert('zone data is empty');
                        }
                    } else {
                        alert('zone not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


    function restrictSpecialChars(selector) {

const sqlKeywords = /(select|insert|update|delete|drop|alter|truncate|exec|union)/i;

$(selector)
    .off("keypress paste input")

    // Keypress restriction
    .on("keypress", function (event) {
        let char = String.fromCharCode(event.which);

        if (selector === '.removesplchar_text') {
            if (!/^[a-zA-Z0-4\u0B80-\u0BFF\-() ]$/.test(char)) {
                event.preventDefault();
            }
        }
    })

    // Paste restriction
    .on("paste", function (e) {
        e.preventDefault();
        let pasteData = (e.originalEvent || e).clipboardData.getData('text');

        let cleanData = pasteData.replace(/[^a-zA-Z0-4\u0B80-\u0BFF\-() ]/g, '');

        // Block SQL keywords
        if (sqlKeywords.test(cleanData)) {
            alert("Invalid input detected.");
            return;
        }

        let input = e.target;
        let start = input.selectionStart;
        let end = input.selectionEnd;
        let original = input.value;

        input.value = original.substring(0, start) + cleanData + original.substring(end);
        input.setSelectionRange(start + cleanData.length, start + cleanData.length);
    })

    // Final safety check on input
    .on("input", function () {
        if (sqlKeywords.test(this.value)) {
            this.value = this.value.replace(sqlKeywords, '');
            alert("SQL keywords are not allowed.");
        }
    });
}




    function populatecircleform(circle) {
        $('#display_error').hide();
        $('#circleename').val(circle.circleename);
        $('#circletname').val(circle.circletname);
        populateStatusFlag(circle.statusflag);
        $('#distcode').val(circle.distcode).change();
        getZoneNameBasedOnDist(circle.distcode,circle.zonecode)
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
        $('#zonecode').val(null).select2();
        $('#distcode').val(null).trigger('change');

        if (sessiondeptcode && sessiondeptcode.trim() !== '') {

            $('#circleename,#circletname', '#circleid').val();

        } else {
            $('#zonename').val(null).trigger('change');

        }
        $('#circleform').validate().resetForm();

        changeButtonAction('circleform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>

@endsection
