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
            <div class="card-header card_header_color lang" key="">Template Audit Bank Entry</div>
            <div class="card-body">
                <form id="banldetailsform" name="banldetailsform">
                    <input type="hidden" name="bankid" id="bankid">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="bankname" for="bankname">Bank Name
                                </label>
                            <input type="text" maxlength='50' data-placeholder-key="bankname" class="form-control removesplchar_text"
                                id="bankname" name="bankname" required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="ifsccode" for="ifsccode">IFSC Code</label>
                            <input type="text" class="form-control"  maxlength='4' id="ifsccode" name="ifsccode"
                                data-placeholder-key="ifsccode" required />
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
                            <input type="hidden" name="regioncode" id="regioncode" value="" />
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
            <div class="card-header card_header_color lang" key="bankdetailstable">Template Audit Bank Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="bankdetailstable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="bankname">Bank Name
                                    </th>
                                    <th class="lang align-middle text-center" key="ifsccode"> IFSC Code </th>
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
        $('#banldetailsform')[0].reset();
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
        updateValidationMessages(getLanguage('Y'), 'banldetailsform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('templatebankdetailsfetch') }}",
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

        if ($.fn.DataTable.isDataTable('#bankdetailstable')) {
            $('#bankdetailstable').DataTable().clear().destroy();
        }

        var table = $('#bankdetailstable').DataTable({
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
                    data: "bank_name",
                    title: columnLabels?.["bank_name"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.bank_name || '-';
                    }
                },
                {
                    data: "ifsc_code",
                    title: columnLabels?.["ifsc_code"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column ",
                    render: function(data, type, row) {
                        return row.ifsc_code || '-';
                    }
                },
                {
                    data: "statusflag",
                    title: columnLabels?.["statusflag"]?.[window.localStorage.getItem("lang")] || "Status",
                    render: function(data) {
                        //let language = window.localStorage.getItem("lang") || "en"; // Default to English
                        let activeText = arrLang?.[language]?.["active"];
                        let inactiveText = arrLang?.[language]?.["inactive"];

                        return data === 'Y' ?
                            `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                            `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                    },
                    className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                },

                {
                    data: "encrypted_bankid",
                    title: columnLabels?.["actions"]?.[language],
                    render: (data) =>
                        `<center><a class="btn editicon editregiondel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
                    className: "text-center noExport text-wrap"
                }
            ],
            "initComplete": function(settings, json) {
                $("#bankdetailstable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
        const mobileColumns = ["bankname", "ifsccode", "statusflag"];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "bankdetailstable");
    }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#bankdetailstable')) {
            $('#bankdetailstable').DataTable().clear().destroy();
        }
        renderTable(language);
    }


    $('#ifsccode').on('input', function () {
    let value = $(this).val().toUpperCase();
    let result = '';

    for (let i = 0; i < value.length && i < 11; i++) {
        const char = value[i];

        if (i < 4) {
            // First 4 must be letters
            if (char >= 'A' && char <= 'Z') {
                result += char;
            }
        }
    }

    $(this).val(result);
});




$(document).ready(function () {
    inputRestrictions({
        normal: '.removesplchar_text', // normal inputs
        ifsc: '#ifsccode'               // IFSC input
    });
});

// limit same continuous characters (aaa -> max 3)
function limitContinuousChars(value, max = 3) {
    return value.replace(/([^\s])\1{3,}/g, '$1$1$1');
}
function removeRepeatedWords(value) {
    // match words but keep spaces intact
    return value.replace(/\b(\w+)(\s+)(\1\w*)\b/gi, '$1');
}


function inputRestrictions(config) {

const sqlKeywords = [
    'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE',
    'TRUNCATE', 'REPLACE', 'EXEC', 'UNION', 'WHERE', 'FROM', 'TABLE', 'DATABASE'
];

/* ---------------- NORMAL INPUTS ---------------- */
$(config.normal).each(function () {
    const $el = $(this);
    $el.off("keypress input paste");

    $el.on("keypress", function (event) {
        const char = String.fromCharCode(event.which);
        if (!/^[a-zA-Z0-9\s\u0B80-\u0BFF.,&/()]*$/.test(char)) {
            event.preventDefault();
        }
    });

    $el.on("input", function () {
        let value = $(this).val();

        if (sqlKeywords.some(k => new RegExp(`\\b${k}\\b`, 'i').test(value))) {
            alert('Specific keywords are not allowed!');
            sqlKeywords.forEach(k => {
                value = value.replace(new RegExp(`\\b${k}\\b`, 'gi'), '');
            });
        }

        // ✅ ADDED
        value = limitContinuousChars(value, 3);
        value = removeRepeatedWords(value);


        $(this).val(value);

    });

    $el.on("paste", function (e) {
        e.preventDefault();
        let pasteData = (e.originalEvent || e).clipboardData.getData('text');

        let cleanData = pasteData
            .replace(/[^a-zA-Z0-9\u0B80-\u0BFF.,&-\s]+/g, '')
            .replace(/\s+/g, ' ')
            .trim();

        // ✅ ADDED
        cleanData = limitContinuousChars(cleanData, 3);
        cleanData = removeRepeatedWords(cleanData);

        const input = e.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const maxLength = parseInt($(input).attr('maxlength')) || Infinity;

        cleanData = cleanData.substring(0, maxLength);
        input.value = input.value.substring(0, start) + cleanData + input.value.substring(end);
        input.setSelectionRange(start + cleanData.length, start + cleanData.length);
    });
});

/* ---------------- NORMAL INPUTS ---------------- */
$(config.normal).each(function () {
    const $el = $(this);
    $el.off("keypress input paste");

    $el.on("keypress", function (event) {
        const char = String.fromCharCode(event.which);
        if (!/^[a-zA-Z\u0B80-\u0BFF\s,\-&]$/.test(char)) {
            event.preventDefault();
        }
    });

    $el.on("input", function () {
        let value = $(this).val();

        if (sqlKeywords.some(k => new RegExp(`\\b${k}\\b`, 'i').test(value))) {
            alert('Specific keywords are not allowed!');
            sqlKeywords.forEach(k => {
                value = value.replace(new RegExp(`\\b${k}\\b`, 'gi'), '');
            });
        }

        // ✅ ADDED
        value = limitContinuousChars(value, 3);
        value = removeRepeatedWords(value);

        // allow space, block multiple
        $(this).val(value);
        });

    $el.on("paste", function (e) {
        e.preventDefault();
        let pasteData = (e.originalEvent || e).clipboardData.getData('text');

        let cleanData = pasteData
            .replace(/[^a-zA-Z\u0B80-\u0BFF\s,\-&]/g, '')
            .replace(/\s+/g, ' ')
            .trim();

        // ✅ ADDED
        cleanData = limitContinuousChars(cleanData, 3);
        cleanData = removeRepeatedWords(cleanData);

        const input = e.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const maxLength = parseInt($(input).attr('maxlength')) || Infinity;

        cleanData = cleanData.substring(0, maxLength);
        input.value =
            input.value.substring(0, start) +
            cleanData +
            input.value.substring(end);

        input.setSelectionRange(start + cleanData.length, start + cleanData.length);
    });
});

}




    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';

        $.validator.addMethod(
        "ifscValid",
        function (value, element) {
            return this.optional(element) ||
                /^[A-Z]{4}$/.test(value);
        },
        "Invalid IFSC Code format"
    );
        var validator =  $("#banldetailsform").validate({
        rules: {
            bankname: {
                required: true
            },
            ifsccode: {
                required: true,
                minlength: 4,
                maxlength: 4,
                ifscValid: true
            },
            statusflag: {
                required: true
            }
        },
        messages: {
            ifsccode: {
                required: "IFSC Code is required",
                minlength: "IFSC Code must be exactly 4 characters",
                maxlength: "IFSC Code must be exactly 4 characters"
            }
        },

        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            if ($("#banldetailsform").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var formData = $('#banldetailsform').serializeArray();
                var deptcode = $('#deptcode').val();
                if ($('#deptcode').prop('disabled')) {

                    formData.push({
                        name: 'deptcode',
                        value: deptcode
                    });
                }
                $.ajax({
                    url: "{{ route('templatebankdetails_insertupdate') }}",
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
            } else {}
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


    $(document).on('click', '.editregiondel', function() {
        const id = $(this).attr('id');
        if (id) {
            reset_form();
            $('#bankid').val(id);
            $.ajax({
                url: "{{ route('templatebankdetailsfetch') }}",
                method: 'POST',
                data: {
                    bankid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            changeButtonAction('banldetailsform', 'action', 'buttonaction',
                                'reset_button', 'display_error', @json($updatebtn),
                                @json($clearbtn), @json($update))
                            populatebanldetailsform(response.data[0]); // Populate form with data
                        } else {
                            alert('region data is empty');
                        }
                    } else {
                        alert('region not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });




    function populatebanldetailsform(region) {
        $('#display_error').hide();
        $('#bankname').val(region.bank_name);
        $('#ifsccode').val(region.ifsc_code);

        populateStatusFlag(region.statusflag);
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

            $('#bankname,#ifsccode', '#regioncode', '#bankid').val();

        } else {
            $('#deptcode').val(null).trigger('change');

        }
        $('#banldetailsform').validate().resetForm();

        changeButtonAction('banldetailsform', 'action', 'buttonaction', 'reset_button', 'display_error',
            @json($savebtn), @json($clearbtn), @json($insert))
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>

@endsection