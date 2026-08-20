@section('content')
@section('title', content: 'SpillOver Revoke')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';
@endphp

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>




<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">SpillOver Institution Revoke</div>
            <div class="card-body">
                <form id="spillrevoke" name="spillrevoke" >
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>
                            <select class="form-select mr-sm-2 select2  lang-dropdown" id="deptcode" name="deptcode"
                                onchange="getCategoriesBasedOnDept(this.value,'');">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- Select Department---">Select Department</option>

                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            @if (old('dept', $deptcode) == $department->deptcode)  @endif
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled data-name-en="No Department Available"
                                        data-name-ta="No Department Available">No Departments Available</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="region_div">
                            <label class="form-label lang required" key="region">
                                Region
                            </label>

                            <select class="form-select select2" id="regioncode" name="regioncode"
                                onchange="onchange_region(this.value,'');">

                                <option value="">
                                    ---Select Region---
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="dist_div">
                            <label class="form-label lang required" key="district" for="validationDefault01">District
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                onchange="onchange_users()">
                                <option value="" data-name-en="---Select District---"
                                    data-name-ta="--- Select District---">---Select District---</option>

                            </select>
                        </div>

                        <div class="col-md-12 mb-3" id="inst_div">
                        </div>
                    </div>
                </form>
            </div>
        </div>
 <div id="ajax-loader">
                <div class="spinner"></div>
            </div>

    </div>
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
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<script>
    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    window.spilloverRevokeRemarks = '';

    function updateSelect2Language(lang) {
        $('.select2 option').each(function() {
            var text = (lang === "en") ? $(this).attr('data-name-en') : $(this).attr('data-name-ta');
            $(this).text(text);
        });

        $('.select2').select2();
    }

    $(document).ready(function() {
        let currentLang = getLanguage();
        updateSelect2Language(currentLang);
    });

    function changeLanguage(lang) {
        window.localStorage.setItem('lang', lang); // Save language selection
        updateSelect2Language(lang); // Apply the change immediately
    }

    function getCategoriesBasedOnDept(deptcode, selectedregionCode = null) {
        const regionDropdown = $('#regioncode');
        const distDropdown = $('#distcode');
        const instDiv = $('#inst_div');
        const lang = getLanguage();

        distDropdown.html(`
            <option value="" data-name-en="---Select District---" data-name-ta="--- Select District---">
                ${lang === 'ta' ? '--- Select District---' : '---Select District---'}
            </option>
        `);
        instDiv.empty();

        regionDropdown.html(`
            <option value="" data-name-en="--Select Region Name--" data-name-ta="--Select Region Name--">
                ${lang === 'ta' ? '--Select Region Name--' : 'Select Region Name'}
            </option>
            `);
        // alert(lang);
        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            regionDropdown.append(`
                <option value="" disabled data-name-en="No Region Available" data-name-ta="No Region Available">
                    ${lang === 'ta' ? 'No Region Available' : 'No Region Available'}
                </option>
            `);
            // change_lang_for_page(lang);
            // return;
        }

        if (deptcode) {
            $.ajax({
                url: "/fetch_regionsforspill",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    if (response.data && response.data.length > 0) {
                        // alert(lang);
                        response.data.forEach(regions => {
                            regionDropdown.append(`
                        <option value="${regions.regioncode}"
                            data-name-en="${regions.regionename}"
                            data-name-ta="${regions.regiontname}"
                            ${regions.regioncode === selectedregionCode ? 'selected' : ''}>
                            ${lang === 'ta' ? regions.regiontname : regions.regionename}
                        </option>
                    `);
                        });
                    } else {
                        regionDropdown.append(`
                    <option disabled data-name-en="No Region Available" data-name-ta="No Region Available">
                        ${lang === 'ta' ? 'No Region Available' : 'No Region Available'}
                    </option>
                `);
                    }

                    // change_lang_for_page(lang); // Update dropdown text after data is loaded
                },
                error: function() {
                    alert('Error fetching regions. Please try again.');
                }
            });
        }
    }

    function onchange_region(regioncode, selecteddistCode = null) {
        const distDropdown = $('#distcode');
        const lang = getLanguage();

        distDropdown.html(`
            <option value="" data-name-en="--Select Region Name--" data-name-ta="--Select Region Name--">
                ${lang === 'ta' ? '--Select Region Name--' : 'Select Region Name'}
            </option>
            `);
        // alert(lang);
        if (!deptcode) {
            deptcode = $("#deptcode").val();
        }

        if (!deptcode) {
            distDropdown.append(`
            <option value="" disabled data-name-en="No Region Available" data-name-ta="No Region Available">
                ${lang === 'ta' ? 'No Region Available' : 'No Region Available'}
            </option>
        `);
            // change_lang_for_page(lang);
            // return;
        }

        if (deptcode) {
            $.ajax({
                url: "/fetch_districtsforspillover",
                type: "POST",
                data: {
                    regioncode: regioncode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    if (response.data && response.data.length > 0) {
                        // alert(lang);
                        response.data.forEach(dist => {
                            distDropdown.append(`
                        <option value="${dist.distcode}"
                            data-name-en="${dist.distename}"
                            data-name-ta="${dist.disttname}"
                            ${dist.distcode === selecteddistCode ? 'selected' : ''}>
                            ${lang === 'ta' ? dist.disttname : dist.distename}
                        </option>
                    `);
                        });
                    } else {
                        distDropdown.append(`
                    <option disabled data-name-en="No Region Available" data-name-ta="No Region Available">
                        ${lang === 'ta' ? 'No Region Available' : 'No Region Available'}
                    </option>
                `);
                    }

                    // change_lang_for_page(lang); // Update dropdown text after data is loaded
                },
                error: function() {
                    alert('Error fetching regions. Please try again.');
                }
            });
        }
    }

    function showLoader() {
            document.getElementById('ajax-loader').style.display = 'flex';
        }

        function hideLoader() {
            document.getElementById('ajax-loader').style.display = 'none';
        }

    function renderInstitutionGrid(data) {
        const instDiv = $('#inst_div');

        let html = `
            <label class="form-label required">Institution Details</label>
            <div class="table-responsive">
                <table id="spillover_institution_table" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th style="width: 80px;">S.No</th>
                            <th>Institution Name</th>
                            <th>Exitmeetdate</th>
                             <th>Proposed Exitmeetdate</th>
                             <th>Spillover Status</th>
                              <th>Total Mandays</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.forEach((inst, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${inst.instename ?? '-'}</td>
                    <td>
    ${inst.exitmeetdate
        ? inst.exitmeetdate.split('-').reverse().join('-')
        : '-'}
</td>
 <td>
    ${inst.proposedexitmeetdate
        ? inst.proposedexitmeetdate.split('-').reverse().join('-')
        : '-'}
</td>

                    <td>
    ${
        inst.spillovercompleted === 'Y'
            ? 'Spillover Completed'
            : inst.spillovercompleted === 'N'
                ? 'Carry Forward to Next Quarter'
                : '-'
    }
</td>
                    <td>${inst.mandays ?? '-'}</td>
                    <td>
                        <button type="button"
                            class="btn btn-sm ${inst.spill_revokeflag === 'Y' ? 'btn-success' : 'btn-danger'} revoke-inst-btn"
                            value="${inst.instid}"
                            data-instid="${inst.instid}"
                            data-auditscheduleid="${inst.auditscheduleid ?? ''}"
                            data-spill-revokeflag="${inst.spill_revokeflag ?? 'N'}"
                            data-spillovercompleted="${inst.spillovercompleted ?? 'N'}"
                            data-mandays="${inst.mandays ?? 0}"
                            data-completedmandays="${inst.completed_mandays ?? 0}"
                            data-remainingmandays="${inst.remainingmandays ?? 0}"
                            ${inst.spill_revokeflag === 'Y' ? 'disabled' : ''}>
                            ${inst.spill_revokeflag === 'Y' ? 'Status Updated' : 'Update Status'}
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        instDiv.html(html);
        initInstitutionDataTable();
    }

    function initInstitutionDataTable() {
        const tableSelector = '#spillover_institution_table';
        if (!$.fn.DataTable || !$(tableSelector).length) {
            return;
        }

        if ($.fn.DataTable.isDataTable(tableSelector)) {
            $(tableSelector).DataTable().destroy();
        }

        $(tableSelector).DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: true,
            paging: true,
            ordering: false,
            info: true,
            autoWidth: false,
            dom: 'frtip',
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         text: 'Download',
            //         title: 'Spillover Revoke Institution Details'
            //     }
            // ]
        });
    }

    function onchange_users() {

        const deptcode = $('#deptcode').val();
        const regioncode = $('#regioncode').val();
        const distcode = $('#distcode').val();
        const instDiv = $('#inst_div');
        const lang = getLanguage();

        if (!deptcode || !regioncode || !distcode) {
            instDiv.empty();
            return;
        }

       // instDiv.html('<div class="text-muted">Loading institutions...</div>');
          showLoader();
        $.ajax({
            url: '/fetchinstitutionsforspillrevoke',
            type: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoader();
                if (response.success && response.data && response.data.length > 0) {
                    renderInstitutionGrid(response.data);
                } else {
                    instDiv.html(`
                        <div class="alert alert-warning mb-0">
                            ${lang === 'ta' ? 'தகவல் இல்லை' : 'No Institutions Available'}
                        </div>
                    `);
                }
            },
            error: function() {
                hideLoader();
                instDiv.html(`
                    <div class="alert alert-danger mb-0">
                        ${lang === 'ta' ? 'நிறுவனங்களை பெற முடியவில்லை' : 'Error fetching institutions'}
                    </div>
                `);
            }
        });
    }

    function updateSpilloverRevoke(instid, otpVerified = 'N') {
        $.ajax({
            url: '/spilloverrevoke/update',
            type: 'POST',
            data: {
                instid: instid,
                revoke: 'Y',
                otpverified: otpVerified,
                remarks: window.spilloverRevokeRemarks || '',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#confirmation_alert .modal-footer').show();
                window.spilloverRevokeRemarks = '';
                passing_alert_value(
                    'Confirmation',
                    response.message || 'Revoke updated successfully',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
                $("#ok_button").off('click').on('click', function() {
                    $('#confirmation_alert').modal('hide');
                    onchange_users();
                });
            },
            error: function(xhr) {
                $('#confirmation_alert .modal-footer').show();
                let msg = 'Failed to update revoke flag';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                passing_alert_value(
                    'Confirmation',
                    msg,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        });
    }

    function sendOtpForSpilloverRevoke(instid, popupDelayMs = 0) {
        showLoader();
        $.ajax({
            url: '/otp_spillrevoke',
            type: 'POST',
            data: {
                instid: instid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    setTimeout(function() {
                        hideLoader();
                        renderSpilloverRevokeOtpPopup(instid);
                    }, popupDelayMs);
                } else {
                    hideLoader();
                    passing_alert_value(
                        'Confirmation',
                        response.message || 'Failed to send OTP',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            },
            error: function() {
                hideLoader();
                passing_alert_value(
                    'Confirmation',
                    'Failed to send OTP',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
            }
        });
    }

    function renderSpilloverRevokeOtpPopup(instid) {
        const otpcontent = `<div id="otp_div">
            <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
            <span class="text-center mb-3 d-block">Enter 6-Digit verification code sent to your mail</span><br>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                    </div>
                    <button type="button" id="verify_otp_button" class="btn btn-primary w-100">Verify OTP</button>
                </div>
            </div><br>
            <small class="text-center mb-3 d-block">Didn't receive the code?
                <b id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b>
            </small><br>
        </div>`;

        passing_alert_value(
            'Confirmation',
            otpcontent,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        $('#confirmation_alert .modal-footer').hide();
        $('#confirmation_alert').modal('show');
        $('#confirmation_alert .btn-close, #confirmation_alert .close').hide();

        const otpInputs = Array.from(document.querySelectorAll('.otp-input'));
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
                if (this.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        if (otpInputs.length > 0) {
            otpInputs[0].focus();
        }

        document.getElementById('verify_otp_button').onclick = function() {
            let otp = '';
            document.querySelectorAll('.otp-input').forEach(input => otp += input.value);

            if (!/^\d{6}$/.test(otp)) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }

            $.ajax({
                url: '/verifyOTP_revokespill',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    otp: otp
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#confirmation_alert').modal('hide');
                        updateSpilloverRevoke(instid, 'Y');
                    } else {
                        alert(response.message || 'Incorrect OTP.');
                    }
                },
                error: function() {
                    alert('OTP verification failed.');
                }
            });
        };

        document.getElementById('resend_otp_link').onclick = function(e) {
            e.preventDefault();
            sendOtpForSpilloverRevoke(instid);
        };
    }

    function renderSpilloverRevokeRemarksPopup(instid) {
        const remarksContent = `<div id="remarks_div">
              <label class="col-md-3 col-form-label">
                    Remarks <span class="text-danger">*</span>
                </label>
            <div class="mb-3">
                <textarea id="spillover_remarks" class="form-control" rows="4" maxlength="150"
                    placeholder="Enter remarks"></textarea>
                 <small class="text-danger">Remarks is required (min 10).</small>
            </div>
            <button type="button" id="send_otp_from_remarks" class="btn btn-primary w-100">Send OTP</button>
        </div>`;

        passing_alert_value(
            'Confirmation',
            remarksContent,
            'confirmation_alert',
            'alert_header',
            'alert_body',
            'forward_alert'
        );

        $('#confirmation_alert .modal-footer').hide();
        $('#confirmation_alert').modal('show');
        $('#confirmation_alert .btn-close, #confirmation_alert .close').hide();

        const remarksInput = document.getElementById('spillover_remarks');
        // Allow only letters, numbers and spaces.
        remarksInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z0-9\s]/g, '');
            this.value = this.value.replace(/\s+/g, ' ');
        });

        document.getElementById('send_otp_from_remarks').onclick = function() {
            const remarks = (($('#spillover_remarks').val() || '').replace(/\s+/g, ' ')).trim();
            if (!remarks) {
                alert('Remarks is required');
                return;
            }

            if (!/^[A-Za-z0-9\s]+$/.test(remarks)) {
                alert('Special characters are not allowed in remarks.');
                return;
            }
            if (remarks.length < 10 || remarks.length > 150) {
                alert('Remarks must be between 10 and 150 characters.');
                return;
            }

            window.spilloverRevokeRemarks = remarks;
            sendOtpForSpilloverRevoke(instid, 1200);
        };
    }

    $(document).on('click', '.revoke-inst-btn', function() {
        if ($(this).data('spill-revokeflag') === 'Y' || $(this).prop('disabled')) {
            return;
        }
        const $btn = $(this);
        $btn.prop('disabled', true);
        const instid = $btn.data('instid');
        $.ajax({
            url: '/spilloverrevoke/check',
            type: 'POST',
            data: {
                instid: instid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (!res.success || !res.data) {
                    passing_alert_value(
                        'Confirmation',
                        'Institution details not found',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                    return;
                }

                const d = res.data;
                const isNtoY = (d.spillovercompleted || 'N') === 'N';
                const instName = d.instename || 'Institution';
                const rawRemainingMandays = d.recalculated_remaining_mandays;
                const parsedRemainingMandays = Number(rawRemainingMandays);
                const remainingMandays = Number.isFinite(parsedRemainingMandays) ? parsedRemainingMandays : 0;
                const remainingMandaysDisplay = remainingMandays > 0 ? remainingMandays : 0;

                const mandaysText = isNtoY
                    ? `
                    <div>
                        Are you sure to update <b>${instName}</b> as <b>Spillover Completed</b>?<br><br>
                        <span class="text-danger"><b>Note:</b> After this change, <b>${instName}</b> will <b>not</b> be carried forward to the next quarter.</span>
                    </div>`
                    : (remainingMandays <= 0
                        ? `
                    <div>
                        <b>Team Size:</b> ${d.teamsize ?? 0}<br>
                        <b>Working Days :</b> ${d.working_days ?? 0}<br>
                        <b>Total Working Mandays:</b> ${(d.working_days ?? 0) * (d.teamsize ?? 0)}<br>
                        <b>Remaining Mandays:</b> ${remainingMandaysDisplay}<br><br>
                        <span class="text-danger"><b>Please update the mandays. Not enough remaining mandays to move to the next quarter.</b></span>
                    </div>`
                        : `
                    <div>
                        <b>Team Size:</b> ${d.teamsize ?? 0}<br>
                        <b>Working Days :</b> ${d.working_days ?? 0}<br>
                        <b>Total Working Mandays:</b> ${(d.working_days ?? 0) * (d.teamsize ?? 0)}<br>
                        <b>Remaining Mandays:</b> ${remainingMandaysDisplay}<br><br>
                        Are you sure to update <b>${instName}</b> as <b>Carry Forward to Next Quarter</b>?<br><br>

                    </div>
                    <span class="text-danger"><b>Note:</b> After this change, <b>${instName}</b> will be carried forward to the next quarter.</span>`);

                passing_alert_value(
                    'Confirmation',
                    mandaysText,
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );
                $('#confirmation_alert .btn-close, #confirmation_alert .close').show();

                if (!isNtoY && remainingMandays <= 0) {
                    $('#confirmation_alert .modal-footer').show();
                    $('#ok_button').show();
                    $('#process_button, #cancel_button').hide();
                    $('#ok_button').off('click').on('click', function () {
                        $('#confirmation_alert').modal('hide');
                    });
                    return;
                }

                $("#process_button").html("Yes");
                 $("#cancel_button").html("No");
                $("#process_button").off('click').on('click', function() {
                    if ($(this).prop('disabled')) return;
                    $(this).prop('disabled', true);
                    $('#confirmation_alert')
                        .off('hidden.bs.modal.spillremarks')
                        .on('hidden.bs.modal.spillremarks', function() {
                            $('#confirmation_alert').off('hidden.bs.modal.spillremarks');
                            renderSpilloverRevokeRemarksPopup(instid);
                            $("#process_button").prop('disabled', false);
                        });
                    $('#confirmation_alert').modal('hide');
                });
            },
            error: function() {
                passing_alert_value(
                    'Confirmation',
                    'Error while checking institution details',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );
                $btn.prop('disabled', false);
            },
            complete: function () {
                // Re-enable if user closes popup without proceeding
                $('#confirmation_alert')
                    .off('hidden.bs.modal.revokeBtn')
                    .on('hidden.bs.modal.revokeBtn', function () {
                        $('#confirmation_alert').off('hidden.bs.modal.revokeBtn');
                        $btn.prop('disabled', false);
                    });
            }
        });
    });


</script>


@endsection
