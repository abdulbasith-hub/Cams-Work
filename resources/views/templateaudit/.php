@section('content')
@section('title', 'Manual Template Plan')
@extends('index2')
@include('common.alert')
@php

    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';
@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">



<div class="card card_border" style="border-color: #7198b9">
    <div class="card-header card_header_color lang" key="" style="padding:8px;">Manual Template Audit Plan</div>
    <div class="card-body card_border">
        <div class="row">

            <div class="card-body">
                <form id="manualtemplateform" name="manualtemplateform">
                    <input type="hidden" name="praudittitleid" id="praudittitleid">
                    {{-- <input type="hidden" name="uploadid" id="uploadid" value="">
                        <input type="hidden" name="existing_uploadid" id="existing_uploadid" value=""> --}}
                    <input type="hidden" name="manualplanid" id="manualplanid" value="">
                    <input type="hidden" name="planmappingid" id="planmappingid" value="">
                    <input type="hidden" name="auditquartercode" id="auditquartercode" value="">
                    <input type="hidden" name="auditquarter" id="auditquarter" value="">
                    <input type="hidden" name="verifiedflag" id="verifiedflag" value="N">
                    <input type="hidden" name="finalize" id="finalize" value="">
                    @csrf
                    <div class="row">

                        <div class="col-md-4 mb-3" id="deptdiv">
                            <label class="form-label required lang" key="department" for="dept">Department</label>

                            <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                <?php echo $make_dept_disable; ?> onchange="onDepartmentChange(this.value);">
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>


                                @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                            @if (old('dept', $deptcode) == $department->deptcode) selected @endif
                                            data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled data-name-en="No Department Available"
                                        data-name-ta="துறைகள் எதுவும் இல்லை">No Departments Available</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label lang" for="regioncode">Region</label>
                            <select class="form-select mr-sm-2 select2" id="regioncode" name="regioncode">
                                <option value="">Select Region</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label lang" for="districtcode">District</label>
                            <select class="form-select mr-sm-2 select2" id="districtcode" name="districtcode">
                                <option value="">Select District</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label lang required " key="catengname" for="category">Category</label>
                            <select class="form-select mr-sm-2 select2" id="category" name="category">
                                <option value=''>Select Category Name</option>
                                <option value="" disabled id="no-district-option">No Category Available Name
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-1 subcatdiv ">
                            <label class="form-label lang required" key="subcategory_ename"
                                for="subcategory">SubCategory</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                name="subcategory" onchange="">
                                <option value="" data-name-en="---Select SubCategory---"
                                    data-name-ta="---உபவகை தேர்ந்தெடுக்கவும்---">---Select SubCategory---</option>


                            </select>
                        </div>

                        <div class="col-md-4 mb-3" id="institution_wrapper">
                            <label class="form-label lang required" for="instid">Institution</label>
                            <select class="form-select mr-sm-2 select2" id="instid" name="instid[]" multiple
                                data-placeholder="Select Institution"></select>
                        </div>



                        <div id="quarter_top_anchor" class="d-none"></div>

                        <div class="col-12">
                            <div class="row ">
                                <div class="col-md-3 mb-3 d-none" id="quarter_display_wrapper">
                                    <label class="form-label lang" for="quarter_display">Audit Quarter</label>
                                    <input type="text" class="form-control" id="quarter_display"
                                        name="quarter_display" value="" disabled>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label lang" for="auditorid">Auditor</label>
                                    <select class="form-select mr-sm-2 select2" id="auditorid" name="auditorid">
                                        <option value="">Select Auditor</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label lang" for="audit_date">AuditDate </label>
                                    <input type="text" class="form-control" id="audit_date" name="audit_date"
                                        value="{{ now()->format('d-m-Y') }}" disabled>

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Auditor Institution Count</label>
                                    <div class="form-control d-flex align-items-center"
                                        id="auditor_institution_count_badge">
                                        0
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-md-3 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <input type="hidden" name="map_callforrecords" id="map_callforrecords"
                                    value="" />

                                <button class="btn button_save mt-3 lang" key="save_btn" type="submit"
                                    action="insert" id="buttonaction" name="buttonaction">Save</button>
                                <button type="button" class="btn btn-success mt-3 lang d-none"
                                    style="height:35px;font-size: 13px;" id="finalize_button">Finalize</button>
                                <button type="button" class="btn btn-danger mt-3  lang"
                                    style="height:35px;font-size: 13px;" key="clear" id="reset_button"
                                    onclick="reset_form()">Clear</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<div class="card card_border">
    <div class="card-header card_header_color lang" key="">Manual Template Audit Plan Details</div>
    <div class="card-body"><br>
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="prtitletable"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang align-middle text-center" key="s_no">S.No</th>
                            <th class="lang align-middle text-center" key="department">Department</th>
                            <th class="lang align-middle text-center">Region</th>
                            <th class="lang align-middle text-center">District</th>
                            <th class="lang align-middle text-center" key="catengname">Category</th>
                            <th class="lang align-middle text-center" key="subcategory_ename">Subcategory</th>
                            <th class="lang align-middle text-center">Institution</th>
                            <th class="lang align-middle text-center">Auditor</th>
                            <th class="lang align-middle text-center">Audit Date</th>
                            <th class="lang align-middle text-center">Quarter</th>
                            <th class="all lang" key="action">Action</th>
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





<script src="{{ url('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ url('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>



<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>

<script>
    let table;
    let dataFromServer = [];
    let prauditValidator;
    let isEditPopulateInProgress = false;
    let isManualTemplateSubmitting = false;
    const sessiondeptcode = @json((string) $deptcode);
    const selectedRegionCode = @json(old('regioncode'));
    const selectedDistrictCode = @json(old('districtcode'));
    const selectedCategoryCode = @json(old('category'));
    const selectedSubcategoryCode = @json(old('subcategory'));
    const selectedInstitutionCode = @json(old('instid', []));
    const selectedAuditorId = @json(old('auditorid'));
    $(document).ready(function() {

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
        var lang = getLanguage('');
        $('#instid').select2({
            placeholder: $('#instid').data('placeholder'),
            width: '100%'
        });
        initializeDataTable(lang);
        $('#auditorid').on('change', updateAuditorInstitutionCountBadge);


        const deptcodeFromSession = (sessiondeptcode || $('#deptcode').val() || '').toString().trim();
        if ($('#deptcode').prop('disabled') && deptcodeFromSession !== '') {
            $('#deptcode').val(deptcodeFromSession);
            onDepartmentChange(deptcodeFromSession, {
                regioncode: selectedRegionCode || '',
                districtcode: selectedDistrictCode || '',
                category: selectedCategoryCode || '',
                subcategory: selectedSubcategoryCode || '',
                institution: selectedInstitutionCode || '',
                auditorid: selectedAuditorId || ''
            });
        } else {
            $('#deptcode').val('');
            resetLocationDropdowns();
            applySubcategoryVisibility($('#deptcode').val());
        }
    });

    $('#translate').change(function() {
        updateTableLanguage(getLanguage(
            'Y')); // Update the table with the new language by destroying and recreating it
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        const lang = getLanguage('Y');
        if (prauditValidator) {
            prauditValidator.settings.messages = getPrauditValidationMessages(lang);
            // Do not trigger validation on language switch.
            // Keep current form state unchanged and avoid showing required errors.
        }
    });


    function updateTableLanguage(lang) {
        if ($.fn.DataTable.isDataTable('#prtitletable')) {
            $('#prtitletable').DataTable().clear().destroy();
        }

        renderTable(lang);

    }

    function shouldHideSubcategoryByDept(deptcode) {
        const value = (deptcode || '').toString().trim();
        return value === '01' || value === '05' || value === '1' || value === '5';
    }

    function updateQuarterFieldPosition() {
        const quarterWrapper = $('#quarter_display_wrapper');
        if (!quarterWrapper.length) {
            return;
        }

        const quarterVisible = !quarterWrapper.hasClass('d-none');
        const subcategoryHidden = $('.subcatdiv').hasClass('d-none');

        if (quarterVisible && subcategoryHidden) {
            quarterWrapper
                .removeClass('col-md-3')
                .addClass('col-md-4')
                .insertAfter('#institution_wrapper');
            return;
        }

        quarterWrapper
            .removeClass('col-md-4')
            .addClass('col-md-3')
            .prependTo($('.col-12 > .row').first());
    }

    function setManualTemplateSubmitState(disabled) {
        isManualTemplateSubmitting = disabled;
        $('#buttonaction, #finalize_button').prop('disabled', disabled);
    }

    function toggleFinalizeButton(show) {
        $('#finalize_button').toggleClass('d-none', !show);
    }

    function bindManualTemplatePopupOk(callback = null, shouldReload = true) {
        $('#ok_button').off('click.manualtemplate').one('click.manualtemplate', function() {
            if (typeof callback === 'function') {
                callback();
            }
            setManualTemplateSubmitState(false);
            if (shouldReload) {
                window.location.reload();
            }
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getSelectedOptionText(selector) {
        return ($(selector).find('option:selected').text() || '').trim();
    }

    function getSelectedInstitutionTexts() {
        return ($('#instid').find('option:selected').map(function() {
            return ($(this).text() || '').trim();
        }).get()).filter(Boolean);
    }

    function updateAuditorInstitutionCountBadge() {
        const selectedOption = $('#auditorid').find('option:selected');
        const count = parseInt(selectedOption.data('assigned-count'), 10);
        $('#auditor_institution_count_badge').text(Number.isNaN(count) ? 0 : count);
    }

    function formatAuditDateForDisplay(value) {
        const rawValue = String(value || '').trim();
        if (!rawValue) {
            return '';
        }

        const match = rawValue.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (match) {
            return `${match[3]}-${match[2]}-${match[1]}`;
        }

        return rawValue;
    }

    function formatAuditDateForRequest(value) {
        const rawValue = String(value || '').trim();
        if (!rawValue) {
            return '';
        }

        const displayMatch = rawValue.match(/^(\d{2})-(\d{2})-(\d{4})$/);
        if (displayMatch) {
            return `${displayMatch[3]}-${displayMatch[2]}-${displayMatch[1]}`;
        }

        const dbMatch = rawValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dbMatch) {
            return rawValue;
        }

        return '';
    }

    function isFinalizeTimeAvailable() {
        const auditDateText = ($('#audit_date').val() || '').trim();
        if (!auditDateText) {
            return true;
        }

        const parts = auditDateText.split('-');
        if (parts.length !== 3) {
            return true;
        }

        const auditDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}T12:00:00`);
        const now = new Date();

        return now <= auditDate;
    }

    function buildFinalizePopupContent() {
        const departmentText = getSelectedOptionText('#deptcode');
        const regionText = getSelectedOptionText('#regioncode');
        const districtText = getSelectedOptionText('#districtcode');
        const categoryText = getSelectedOptionText('#category');
        const subcategoryText = getSelectedOptionText('#subcategory');
        const quarterText = ($('#quarter_display').val() || '').trim();
        const auditorText = getSelectedOptionText('#auditorid');
        const auditDateText = ($('#audit_date').val() || '').trim();
        const institutionTexts = getSelectedInstitutionTexts();
        const institutionDisplay = institutionTexts.length ?
            institutionTexts.map(function(text, index) {
                return `<div><strong>${index + 1}.</strong> ${escapeHtml(text)}</div>`;
            }).join('') :
            '-';

        return `
            <div class="container-fluid px-2">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Department</strong><div>${escapeHtml(departmentText || '-')}</div></div>
                            <div class="col-md-6"><strong>Region</strong><div>${escapeHtml(regionText || '-')}</div></div>
                            <div class="col-md-6"><strong>District</strong><div>${escapeHtml(districtText || '-')}</div></div>
                            <div class="col-md-6"><strong>Category</strong><div>${escapeHtml(categoryText || '-')}</div></div>
                            ${subcategoryText ? `<div class="col-md-6"><strong>Subcategory</strong><div>${escapeHtml(subcategoryText)}</div></div>` : ''}
                            <div class="col-md-6"><strong>Quarter</strong><div>${escapeHtml(quarterText || '-')}</div></div>
                            <div class="col-md-6"><strong>Institution</strong><div>${institutionDisplay}</div></div>
                            <div class="col-md-6"><strong>Proposed Audit Date</strong><div>${escapeHtml(auditDateText || '-')}</div></div>

                        </div>
                    </div>
                </div>
                <div class="card border-warning shadow-sm mb-3" style="background:#fff7e6;">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-5 text-dark mb-1">Auditor</div>
                        <div class="fw-bolder fs-4 text-danger">${escapeHtml(auditorText || '-')}</div>
                    </div>

                </div>
                <div class="text-end mb-3">

                </div>
                <div class="form-check border rounded p-4 ml-5 bg-light">
                    <input class="form-check-input" type="checkbox" id="finalize_verified_checkbox">
                    <label class="form-check-label required fw-semibold" for="finalize_verified_checkbox">
                        Verified the above details and ready to finalize
                    </label>
                </div>
            </div>
        `;
    }

    function openFinalizeConfirmationPopup() {
        if (!isFinalizeTimeAvailable()) {
            getLabels_jsonlayout([{
                id: 'manual_template_finalize_time_over',
                key: 'manual_template_finalize_time_over'
            }], 'N').then((text) => {
                const alertMessage = Object.values(text)[0] || 'manual_template_finalize_time_over';
                passing_alert_value('Confirmation', alertMessage,
                    'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');
                bindManualTemplatePopupOk(null, false);
            });
            return;
        }

        const popupContent = buildFinalizePopupContent();
        passing_large_alert('Finalize Confirmation', popupContent, 'large_confirmation_alert',
            'large_alert_header', 'large_alert_body', 'forward_alert');

        $('#large_modal_process_button')
            .html('Finalize')
            .addClass('button_finalize')
            .prop('disabled', true)
            .removeAttr('data-bs-dismiss');

        $('#verifiedflag').val('N');
        $('#finalize_verified_checkbox').off('change.manualtemplate').on('change.manualtemplate', function() {
            $('#verifiedflag').val($(this).is(':checked') ? 'Y' : 'N');
            $('#large_modal_process_button').prop('disabled', !$(this).is(':checked'));
        });

        $('#large_modal_process_button').off('click.manualtemplate').on('click.manualtemplate', function(event) {
            event.preventDefault();
            if (!$('#finalize_verified_checkbox').is(':checked')) {
                return;
            }
            $('#large_confirmation_alert').modal('hide');
            submitManualTemplateForm(true);
        });
    }

    function submitManualTemplateForm(isFinalizeSubmit = false) {
        if (isManualTemplateSubmitting) {
            return;
        }

        $('#finalize').val(isFinalizeSubmit ? 'Y' : '');
        if (!isFinalizeSubmit) {
            $('#verifiedflag').val('N');
        }
        if (!$("#manualtemplateform").valid()) {
            setManualTemplateSubmitState(false);
            return;
        }

        setManualTemplateSubmitState(true);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var formElement = document.getElementById('manualtemplateform');
        var formData = new FormData(formElement);
        var deptcode = $('#deptcode').val();
        if ($('#deptcode').prop('disabled')) {
            formData.set('deptcode', deptcode);
        }

        $.ajax({
            url: "/manualtemplate/save",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#manualplanid').val(response.manualplanid || '');
                    passing_alert_value('Confirmation',
                        response.message,
                        'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                    bindManualTemplatePopupOk(null, true);

                } else if (response.error) {
                    console.log(response.error);
                    setManualTemplateSubmitState(false);
                }
            },
            error: function(xhr, status, error) {
                var response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {}
                if (response.error == 401) {
                    setManualTemplateSubmitState(false);
                    handleUnauthorizedError();
                } else {
                    const messageKey = response.message || 'Error Occured';
                    getLabels_jsonlayout([{
                        id: messageKey,
                        key: messageKey
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] || messageKey;
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                        bindManualTemplatePopupOk(null, false);
                    });
                }
            }
        });
    }

    function resolveDeptSelectValue(rawDeptcode) {
        const val = (rawDeptcode || '').toString().trim();
        if (!val) return '';

        // 1) Exact match.
        if ($('#deptcode option[value="' + val + '"]').length > 0) {
            return val;
        }

        // 2) Numeric-equivalent match (handles 1 vs 01).
        const normalized = String(parseInt(val, 10));
        let resolved = '';
        $('#deptcode option').each(function() {
            const optionValue = ($(this).val() || '').toString().trim();
            if (!optionValue) return;
            const optionNormalized = String(parseInt(optionValue, 10));
            if (optionNormalized === normalized) {
                resolved = optionValue;
                return false;
            }
        });

        return resolved || val;
    }

    function resolveCategorySelectValue(rawCatcode) {
        const val = (rawCatcode || '').toString().trim();
        if (!val) return '';

        if ($('#category option[value="' + val + '"]').length > 0) {
            return val;
        }

        const normalized = String(parseInt(val, 10));
        let resolved = '';
        $('#category option').each(function() {
            const optionValue = ($(this).val() || '').toString().trim();
            if (!optionValue) return;
            const optionNormalized = String(parseInt(optionValue, 10));
            if (optionNormalized === normalized) {
                resolved = optionValue;
                return false;
            }
        });

        return resolved || val;
    }

    function applySubcategoryVisibility(deptcode) {
        const hideSubcategory = shouldHideSubcategoryByDept(deptcode);
        const $fileCol = $('#file_upload_col');
        const $fileRow = $('#file_upload_row');
        const $mainRow = $('#praudit_form_row');
        const $categoryCol = $('#category').closest('.col-md-4');

        if (hideSubcategory) {
            $('.subcatdiv').addClass('d-none');
            $('#subcategory').val('');

            // Move file field into SubCategory slot position.
            if ($fileCol.length && $mainRow.length && !$fileCol.parent().is($mainRow)) {
                $fileCol.removeClass('col-md-3').addClass('col-md-4 mb-1');
                if ($categoryCol.length) {
                    $fileCol.insertAfter($categoryCol);
                } else {
                    $mainRow.append($fileCol);
                }
            }
            if ($fileRow.length) {
                $fileRow.addClass('d-none');
            }

            if (prauditValidator) {
                prauditValidator.element('#subcategory');
            }
        } else {
            $('.subcatdiv').removeClass('d-none');

            // Move file field back to its original file row.
            if ($fileCol.length && $fileRow.length && !$fileCol.parent().is($fileRow)) {
                $fileCol.removeClass('col-md-4 mb-1').addClass('col-md-3');
                $fileRow.removeClass('d-none').append($fileCol);
            } else if ($fileRow.length) {
                $fileRow.removeClass('d-none');
            }
        }

        updateQuarterFieldPosition();
    }

    $(document).on('change', '#deptcode', function() {
        applySubcategoryVisibility($(this).val());
    });

    $(document).on('change', '#regioncode', function() {
        onRegionChange($(this).val());
    });

    $(document).on('change', '#districtcode', function() {
        onDistrictChange($(this).val());
    });

    $(document).on('change', '#subcategory', function() {
        loadInstitutions({
            deptcode: $('#deptcode').val(),
            regioncode: $('#regioncode').val(),
            districtcode: $('#districtcode').val(),
            category: $('#category').val(),
            subcatcode: $('#subcategory').val()
        });
    });

    function resetLocationDropdowns() {
        $('#regioncode').html('<option value="">Select Region</option>').val('');
        $('#districtcode').html('<option value="">Select District</option>').val('');
        $('#category').html('<option value="">Select Category Name</option>').val('');
        $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('');
        $('#instid').html('').val(null).trigger('change.select2');
        $('#auditorid').html('<option value="">Select Auditor</option>').val('');
        $('#planmappingid').val('');
        $('#quarter_display').val('');
        $('#auditquartercode').val('');
        $('#auditquarter').val('');
        $('#quarter_display_wrapper').addClass('d-none');
        updateQuarterFieldPosition();
    }

    function onDepartmentChange(deptcode, selectedValues = {}) {
        if (isEditPopulateInProgress) {
            applySubcategoryVisibility(deptcode);
            return;
        }

        resetLocationDropdowns();

        if (!deptcode) {
            applySubcategoryVisibility('');
            return;
        }

        loadQuarterByDept(deptcode);

        loadRegions(deptcode, selectedValues.regioncode || '', function() {
            if (selectedValues.regioncode) {
                onRegionChange(selectedValues.regioncode, selectedValues);
            }
        });
    }

    function onRegionChange(regioncode, selectedValues = {}) {
        $('#districtcode').html('<option value="">Select District</option>').val('');
        $('#category').html('<option value="">Select Category Name</option>').val('');
        $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('');
        $('#instid').html('').val(null).trigger('change.select2');

        const deptcode = ($('#deptcode').val() || '').toString().trim();
        if (!deptcode || !regioncode) {
            return;
        }

        loadAuditors(deptcode, regioncode, selectedValues.auditorid || '');

        loadDistricts(deptcode, regioncode, selectedValues.districtcode || '', function() {
            if (selectedValues.districtcode) {
                onDistrictChange(selectedValues.districtcode, selectedValues);
            }
        });
    }

    function onDistrictChange(districtcode, selectedValues = {}) {
        $('#category').html('<option value="">Select Category Name</option>').val('');
        $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('');
        $('#instid').html('').val(null).trigger('change.select2');

        const deptcode = ($('#deptcode').val() || '').toString().trim();
        const regioncode = ($('#regioncode').val() || '').toString().trim();

        if (!deptcode || !regioncode || !districtcode) {
            return;
        }

        getCategoriesBasedOnDept(
            deptcode,
            selectedValues.category || null,
            function() {
                if (selectedValues.category) {
                    $('#category').val(resolveCategorySelectValue(selectedValues.category)).trigger('change');
                    if (selectedValues.subcategory) {
                        onchange_category(selectedValues.category, selectedValues.subcategory, '', selectedValues
                            .institution || '');
                    }
                }
            },
            regioncode,
            districtcode
        );
    }

    function loadRegions(deptcode, selectedRegion = '', onLoaded = null) {
        $.ajax({
            url: "/manualtemplate/getregions",
            type: "POST",
            data: {
                deptcode: deptcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const regionDropdown = $('#regioncode');
                regionDropdown.html('<option value="">Select Region</option>');
                (response.data || []).forEach(region => {
                    const value = String(region.regioncode).trim();
                    regionDropdown.append(
                        `<option value="${value}" ${value === String(selectedRegion).trim() ? 'selected' : ''}>${getLanguage() === 'ta' ? region.regiontname : region.regionename}</option>`
                    );
                });
                if (typeof onLoaded === 'function') onLoaded();
            },
            error: function() {
                $('#regioncode').html('<option value="">Select Region</option>');
                if (typeof onLoaded === 'function') onLoaded();
            }
        });
    }

    function loadQuarterByDept(deptcode) {
        $.ajax({
            url: "/manualtemplate/getquarter",
            type: "POST",
            data: {
                deptcode: deptcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#quarter_display').val(response.quarter || '');
                $('#planmappingid').val(response.planmappingid || '');
                $('#auditquarter').val(response.quarter || '');
                $('#auditquartercode').val(response.quartercode || '');
                if (response.quarter) {
                    $('#quarter_display_wrapper').removeClass('d-none');
                } else {
                    $('#quarter_display_wrapper').addClass('d-none');
                }
                updateQuarterFieldPosition();
            },
            error: function() {
                $('#quarter_display').val('');
                $('#planmappingid').val('');
                $('#auditquarter').val('');
                $('#auditquartercode').val('');
                $('#quarter_display_wrapper').addClass('d-none');
                updateQuarterFieldPosition();
            }
        });
    }

    function loadDistricts(deptcode, regioncode, selectedDistrict = '', onLoaded = null) {
        $.ajax({
            url: "/manualtemplate/getdistricts",
            type: "POST",
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                const districtDropdown = $('#districtcode');
                districtDropdown.html('<option value="">Select District</option>');
                (response.data || []).forEach(district => {
                    const value = String(district.distcode).trim();
                    districtDropdown.append(
                        `<option value="${value}" ${value === String(selectedDistrict).trim() ? 'selected' : ''}>${getLanguage() === 'ta' ? district.disttname : district.distename}</option>`
                    );
                });
                if (typeof onLoaded === 'function') onLoaded();
            },
            error: function() {
                $('#districtcode').html('<option value="">Select District</option>');
                if (typeof onLoaded === 'function') onLoaded();
            }
        });
    }

    function loadAuditors(deptcode, regioncode, selectedAuditor = '') {
        const auditorDropdown = $('#auditorid');
        auditorDropdown.html('<option value="">Select Auditor</option>');
        const auditDate = formatAuditDateForRequest($('#audit_date').val());

        if (!deptcode || !regioncode) {
            return;
        }

        $.ajax({
            url: "/manualtemplate/getauditors",
            type: "POST",
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                auditdate: auditDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                (response.data || []).forEach(auditor => {
                    const value = String(auditor.deptuserid).trim();
                    const reserveTag = String(auditor.reservelist || '').toUpperCase() === 'Y' ?
                        ' [Reserve List]' : '';
                    const districtLabel = getLanguage() === 'ta' ?
                        (auditor.disttname || auditor.distename || '') :
                        (auditor.distename || auditor.disttname || '');
                    const auditorName = getLanguage() === 'ta' ?
                        (auditor.usertamilname || auditor.username || '') :
                        (auditor.username || auditor.usertamilname || '');
                    const auditorLabel =
                        `${auditorName}${districtLabel ? ` (${districtLabel})` : ''}${reserveTag}`;
                    auditorDropdown.append(
                        `<option value="${value}" data-assigned-count="${parseInt(auditor.assigned_inst_count, 10) || 0}" ${value === String(selectedAuditor).trim() ? 'selected' : ''}>${auditorLabel}</option>`
                    );
                });
                updateAuditorInstitutionCountBadge();
            },
            error: function() {
                auditorDropdown.html('<option value="">Select Auditor</option>');
                updateAuditorInstitutionCountBadge();
            }
        });
    }

    function initializeDataTable(language) {
        $.ajax({
            url: "/manualtemplate/fetch",
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
                    $('#prtitletable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#prtitletable_wrapper').hide();
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
        const departmentColumn = language === 'ta' ? 'depttlname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const catColumn = language === 'ta' ? 'cattname' : 'catename';
        const subcatColumn = language === 'ta' ? 'subcattname' : 'subcatename';
        const institutionColumn = language === 'ta' ? 'institution_names_ta' : 'institution_names';
        const auditorColumn = language === 'ta' ? 'usertamilname' : 'username';

        if ($.fn.DataTable.isDataTable('#prtitletable')) {
            $('#prtitletable').DataTable().clear().destroy();
        }

        $('#prtitletable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            // "scrollX": true,
            "initComplete": function(settings, json) {
                $("#prtitletable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "data": dataFromServer,

            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: regionColumn,
                    title: columnLabels?.[regionColumn]?.[language] || 'Region',
                    render: function(data, type, row) {
                        return row[regionColumn] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: districtColumn,
                    title: columnLabels?.[districtColumn]?.[language] || 'District',
                    render: function(data, type, row) {
                        return row[districtColumn] || '-';
                    },
                    className: 'text-wrap text-start'
                },
                {
                    data: catColumn,
                    title: columnLabels?.[catColumn]?.[language],
                    render: function(data, type, row) {
                        return row[catColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: subcatColumn,
                    title: columnLabels?.[subcatColumn]?.[language],
                    render: function(data, type, row) {
                        return row[subcatColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
	                {
	                    data: institutionColumn,
	                    title: 'Institution',
	                    className: "d-none d-md-table-cell lang extra-column text-wrap",
	                    render: function(data, type, row) {
	                        const institutionListKey = language === 'ta' ? 'institution_names_list_ta' : 'institution_names_list';
	                        let institutionItems = row[institutionListKey] || [];
	                        if (typeof institutionItems === 'string') {
	                            try {
	                                institutionItems = JSON.parse(institutionItems);
	                            } catch (error) {
	                                institutionItems = [];
	                            }
	                        }
	                        institutionItems = Array.isArray(institutionItems) ? institutionItems.filter(Boolean) : [];

	                        const institutionText = row[institutionColumn] || '';
	                        if (!institutionItems.length && !institutionText) {
	                            return '-';
	                        }

	                        if (type !== 'display') {
	                            return institutionText;
	                        }

	                        if (!institutionItems.length) {
	                            institutionItems = institutionText.split(/\s*,\s*/).filter(Boolean);
	                        }

	                        return institutionItems
	                            .map(function(name, index) {
	                                return `<strong>${index + 1}.</strong> ${escapeHtml(name)}`;
	                            })
	                            .join('<br>');
	                    }
                },
                {
                    data: auditorColumn,
                    title: 'Auditor',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const auditorName = row[auditorColumn] || '';
                        const auditorDistrict = language === 'ta' ?
                            (row.auditor_disttname || row.auditor_distename || '') :
                            (row.auditor_distename || row.auditor_disttname || '');
                        if (!auditorName) {
                            return '-';
                        }
                        return auditorDistrict ? `${auditorName} (${auditorDistrict})` : auditorName;
                    }
                },
                {
                    data: "auditdate",
                    title: 'Audit Date',
                    className: "d-none d-md-table-cell lang extra-column text-nowrap",
                    render: function(data, type, row) {
                        return formatAuditDateForDisplay(row.auditdate) || '-';
                    }
                },
                {
                    data: "planname",
                    title: 'Quarter',
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.planname || row.auditquarter || row.auditquartercode || row
                            .planmappingid || '-';
                    }
                },
                {
                    data: null,
                    title: columnLabels?.["actions"]?.[language],
                    render: function(data, type, row) {
                        if ((row.statusflag || '') === 'F') {
                            return '<center><span class="badge btn btn-success btn-sm">Finalized</span></center>';
                        }

                        return `<center><a class="btn editicon editprtitledel" data-manualplanid="${encodeURIComponent(row.encrypted_manualplanid)}"><i class="ti ti-edit fs-4"></i></a></center>`;
                    },
                    className: "text-center"
                }
            ],
            "initComplete": function(settings, json) {
                $("#prtitletable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //             return data === 'Y' ?
            //                 `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
            //                 `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
            //         },
            //         className: "text-center d-none d-md-table-cell extra-column noExport"
            //     }, {
            //         data: "encrypted_mainobjectionid",
            //         title: columnLabels?.["actions"]?.[language],
            //         render: (data) =>
            //             `<center><a class="btn editicon editprtitledel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`,
            //         className: "text-center noExport"
            //     }
            // ],



        });

        // const mobileColumns = ["titleename", "titletname", "statusflag"];
        // setupMobileRowToggle("prtitletable", mobileColumns);
        // setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "prtitletable");
    }



    $(document).on('select2:select', '#category', function() {
        onchange_category($(this).val(), '', '');
    });

    $(document).on('click', '#add-file-btn', function() {
        $('#praudit_file').trigger('click');
    });

    $(document).on('change', '#praudit_file', function() {
        const file = this.files[0];
        const $container = $('#uploaded_file_container').empty();

        if (!file) {
            if (prauditValidator) prauditValidator.element('#praudit_file');
            return;
        }

        if (prauditValidator && !prauditValidator.element('#praudit_file')) {
            this.value = '';
            return;
        }

        UploadedFileList([{
            name: file.name,
            path: URL.createObjectURL(file),
            fileuploadid: 0
        }], 'view', 'uploaded_file_container', 'N', '');
        $('#add-file-btn').addClass('d-none');
    });

    function reset_form(clearDeptSelection = false, skipDepartmentReload = false) {
        const deptcodeFromSession = (sessiondeptcode || '').toString().trim();
        const isDeptLocked = $('#deptcode').prop('disabled');

        // Reset validator state/messages.
        if (prauditValidator) {
            prauditValidator.resetForm();
            $("#manualtemplateform").find(".error").removeClass("error");
        }

        // Dropdowns.
        if (!clearDeptSelection && isDeptLocked && deptcodeFromSession !== '') {
            $('#deptcode').val(deptcodeFromSession);
            resetLocationDropdowns();
            applySubcategoryVisibility(deptcodeFromSession);
            if (!skipDepartmentReload) {
                onDepartmentChange(deptcodeFromSession);
            }
        } else {
            $('#deptcode').prop('selectedIndex', 0).val('');
            if (!skipDepartmentReload && $('#deptcode').hasClass('select2-hidden-accessible')) {
                $('#deptcode').trigger('change');
            }
            resetLocationDropdowns();
            applySubcategoryVisibility('');
        }

        // Action/button state.
        $('#action').val('insert');
        $('#finalize').val('');
        $('#verifiedflag').val('N');
        $('#manualplanid').val('');
        $('#praudittitleid').val('');
        $('#map_callforrecords').val('');
        toggleFinalizeButton(false);
        changeButtonAction('manualtemplateform', 'action', 'buttonaction', 'reset_button',
            'display_error',
            @json($savebtn), @json($clearbtn),
            @json($insert));

        // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function getPrauditValidationMessages(language) {
        const jsonMsgs = (errorMessages && errorMessages[language]) ? errorMessages[language] : {};
        const getMsg = (key, fallback) => jsonMsgs[key] || fallback;

        return {
            deptcode: {
                required: getMsg('deptcode', "Select a department")
            },
            regioncode: {
                required: getMsg('regioncode', "Select a region")
            },
            districtcode: {
                required: getMsg('districtcode', "Select a district")
            },
            category: {
                required: getMsg('category', "Select a Category")
            },
            subcategory: {
                required: getMsg('subcategory', "Select a Subcategory")
            },
            instid: {
                required: getMsg('instid', "Select Institution")
            },
            auditorid: {
                required: getMsg('auditorid', "Select Auditor")
            }
        };
    }

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        if (!$.validator.methods.maxFileSize) {
            $.validator.addMethod("maxFileSize", function(value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param;
            }, "File size is too large");
        }
        if (!$.validator.methods.pdfOnly) {
            $.validator.addMethod("pdfOnly", function(value, element) {
                if (element.files.length === 0) return true;
                const file = element.files[0];
                const fileName = (file.name || "").toLowerCase();
                return file.type === "application/pdf" || fileName.endsWith(".pdf");
            }, "Only PDF files are allowed");
        }

        prauditValidator = $("#manualtemplateform").validate({
            ignore: ":hidden:not(#instid)",
            rules: {
                deptcode: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                districtcode: {
                    required: true
                },
                category: {
                    required: true
                },
                subcategory: {
                    required: function() {
                        return !shouldHideSubcategoryByDept($('#deptcode').val());
                    }
                },
                'instid[]': {
                    required: true
                },
                auditorid: {
                    required: true
                }
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('select2') || element.attr('id') === 'instid') {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            messages: getPrauditValidationMessages(language)
        });
        $("#buttonaction").on("click", function(event) {
            event.preventDefault();
            submitManualTemplateForm(false);
        });

        $("#finalize_button").on("click", function(event) {
            event.preventDefault();
            if (isManualTemplateSubmitting) {
                return;
            }
            if (!$("#manualtemplateform").valid()) {
                return;
            }
            openFinalizeConfirmationPopup();
        });

        // Handle Edit Button Click
        $(document).on('click', '.editprtitledel', function() {
            const id = decodeURIComponent($(this).attr('data-manualplanid') || '');
            if (id) {
                reset_form(false, true);

                $.ajax({
                    url: "/manualtemplate/detail",

                    method: 'POST',
                    data: {
                        manualplanid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data) {
                                changeButtonAction('manualtemplateform',
                                    'action',
                                    'buttonaction', 'reset_button',
                                    'display_error',
                                    @json($updatebtn),
                                    @json($clearbtn),
                                    @json($update))
                                toggleFinalizeButton((response.data.statusflag || '') !==
                                    'F');
                                populateprtitleForm(response.data, response.options || {});
                            } else {
                                alert('Data is empty');
                            }
                        } else {
                            alert('Record not found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText ||
                            'Unknown error');
                    }
                });
            }
        });
        reset_form();

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    function normalizeInstitutionValues(rawValue) {
        if (Array.isArray(rawValue)) {
            return rawValue.map(value => String(value).trim()).filter(Boolean);
        }

        if (rawValue === null || rawValue === undefined) {
            return [];
        }

        const stringValue = String(rawValue).trim();
        if (!stringValue) {
            return [];
        }

        if (stringValue.startsWith('[') && stringValue.endsWith(']')) {
            try {
                const parsedValue = JSON.parse(stringValue);
                if (Array.isArray(parsedValue)) {
                    return parsedValue.map(value => String(value).trim()).filter(Boolean);
                }
            } catch (error) {
                console.error('Invalid institution JSON:', error);
            }
        }

        return [stringValue];
    }

    function normalizeInstitutionOptions(rawValue) {
        if (Array.isArray(rawValue)) {
            return rawValue;
        }

        if (!rawValue) {
            return [];
        }

        try {
            const parsedValue = typeof rawValue === 'string' ? JSON.parse(rawValue) : rawValue;
            return Array.isArray(parsedValue) ? parsedValue : [];
        } catch (error) {
            console.error('Invalid institution option JSON:', error);
            return [];
        }
    }

    function buildOptionAttributes(attributes = {}) {
        return Object.entries(attributes)
            .filter(([, attrValue]) => attrValue !== null && attrValue !== undefined && String(attrValue) !== '')
            .map(([attrKey, attrValue]) => `${attrKey}="${String(attrValue).replace(/"/g, '&quot;')}"`)
            .join(' ');
    }

    function setSingleSelectOption(selector, value, label, attributes = {}) {
        const normalizedValue = String(value || '').trim();
        const select = $(selector);

        if (!normalizedValue) {
            select.val('').trigger('change.select2');
            return;
        }

        const optionAttributes = buildOptionAttributes(attributes);
        select.html(
            `<option value="${normalizedValue}" ${optionAttributes} selected>${label || normalizedValue}</option>`);
        select.val(normalizedValue).trigger('change.select2');
    }

    function selectExistingOption(selector, value) {
        const normalizedValue = String(value || '').trim();
        const select = $(selector);

        if (!normalizedValue) {
            select.val('').trigger('change.select2');
            return;
        }

        if (select.find(`option[value="${normalizedValue}"]`).length > 0) {
            select.val(normalizedValue).trigger('change.select2');
            return;
        }

        select.append(`<option value="${normalizedValue}" selected>${normalizedValue}</option>`);
        select.val(normalizedValue).trigger('change.select2');
    }

    function resolveSelectedValueFromItems(items, value, valueKey) {
        const normalizedValue = String(value || '').trim();
        if (!normalizedValue) {
            return '';
        }

        const exactMatch = (items || []).find(function(item) {
            return String(item[valueKey] || '').trim() === normalizedValue;
        });
        if (exactMatch) {
            return String(exactMatch[valueKey] || '').trim();
        }

        const numericValue = parseInt(normalizedValue, 10);
        if (!Number.isNaN(numericValue)) {
            const normalizedNumeric = String(numericValue);
            const numericMatch = (items || []).find(function(item) {
                const itemValue = String(item[valueKey] || '').trim();
                return itemValue && !Number.isNaN(parseInt(itemValue, 10)) && String(parseInt(itemValue,
                    10)) === normalizedNumeric;
            });
            if (numericMatch) {
                return String(numericMatch[valueKey] || '').trim();
            }
        }

        return normalizedValue;
    }

    function populateSelectOptions(selector, items, config) {
        const select = $(selector);
        const placeholder = config.placeholder || '';
        const selectedValue = resolveSelectedValueFromItems(items || [], config.selectedValue, config.valueKey);
        const lang = getLanguage();

        select.html(placeholder ? `<option value="">${placeholder}</option>` : '');

        (items || []).forEach(function(item) {
            const value = String(item[config.valueKey] || '').trim();
            if (!value) {
                return;
            }

            const label = lang === 'ta' && config.labelKeyTa ? item[config.labelKeyTa] : item[config.labelKey];
            const attributes = typeof config.attributes === 'function' ? config.attributes(item) : {};
            const optionAttributes = buildOptionAttributes(attributes);

            select.append(
                `<option value="${value}" ${optionAttributes} ${value === selectedValue ? 'selected' : ''}>${label || value}</option>`
            );
        });

        if (selectedValue && select.find(`option[value="${selectedValue}"]`).length === 0 && config.fallbackLabel) {
            const fallbackAttributes = buildOptionAttributes(config.fallbackAttributes || {});
            select.append(
                `<option value="${selectedValue}" ${fallbackAttributes} selected>${config.fallbackLabel}</option>`
            );
        }

        select.val(selectedValue).trigger('change.select2');
    }

    function populateInstitutionOptions(items, selectedValues) {
        const institutionDropdown = $('#instid');
        const lang = getLanguage();

        institutionDropdown.html('');
        (items || []).forEach(function(institution) {
            const institutionValue = String(institution.instid || '').trim();
            const institutionLabel = lang === 'ta' ? institution.insttname : institution.instename;
            institutionDropdown.append(
                `<option value="${institutionValue}" ${selectedValues.includes(institutionValue) ? 'selected' : ''}>${institutionLabel}</option>`
            );
        });

        institutionDropdown.val(selectedValues).trigger('change.select2');
    }

    function populateAuditorOptions(items, selectedValue) {
        const auditorDropdown = $('#auditorid');
        const lang = getLanguage();
        const normalizedValue = String(selectedValue || '').trim();

        auditorDropdown.html('<option value="">Select Auditor</option>');
        (items || []).forEach(function(auditor) {
            const auditorValue = String(auditor.deptuserid || '').trim();
            const districtLabel = lang === 'ta' ?
                (auditor.disttname || auditor.distename || '') :
                (auditor.distename || auditor.disttname || '');
            let auditorLabel = lang === 'ta' ? auditor.usertamilname : auditor.username;
            if (districtLabel) {
                auditorLabel = `${auditorLabel} (${districtLabel})`;
            }
            if ((auditor.reservelist || '') === 'Y') {
                auditorLabel = `${auditorLabel} [Reserve List]`;
            }

            auditorDropdown.append(
                `<option value="${auditorValue}" data-assigned-count="${parseInt(auditor.assigned_inst_count, 10) || 0}" ${auditorValue === normalizedValue ? 'selected' : ''}>${auditorLabel}</option>`
            );
        });

        auditorDropdown.val(normalizedValue).trigger('change.select2');
        updateAuditorInstitutionCountBadge();
    }

    function populateprtitleForm(prtitle, optionData = {}) {
        isEditPopulateInProgress = true;
        $('#display_error').hide();

        $('#manualplanid').val(prtitle.temp_templateauditplanid || '');
        $('#auditquartercode').val(prtitle.auditquartercode || '');
        $('#auditquarter').val(prtitle.planname || prtitle.auditquarter || '');
        $('#planmappingid').val(prtitle.planmappingid || '');
        $('#quarter_display').val(prtitle.planname || prtitle.auditquarter || prtitle.auditquartercode || prtitle
            .planmappingid || '');
        $('#audit_date').val(formatAuditDateForDisplay(prtitle.auditdate) || '{{ now()->format('d-m-Y') }}');
        if (prtitle.planname || prtitle.auditquarter || prtitle.auditquartercode || prtitle.planmappingid) {
            $('#quarter_display_wrapper').removeClass('d-none');
        } else {
            $('#quarter_display_wrapper').addClass('d-none');
        }
        updateQuarterFieldPosition();

        const rawDeptcode = (prtitle.deptcode || '').toString().trim();
        const deptcode = resolveDeptSelectValue(rawDeptcode);
        const regioncode = (prtitle.regioncode || '').toString().trim();
        const distcode = (prtitle.distcode || '').toString().trim();
        const catcode = (prtitle.catcode || '').toString().trim();
        const subcatid = (prtitle.subcatid || '').toString().trim();
        const userid = (prtitle.userid || '').toString().trim();
        const institutionIds = normalizeInstitutionValues(prtitle.instid);
        const hasSubcategory = (prtitle.if_subcategory || '') === 'Y' && subcatid !== '' && subcatid.toLowerCase() !==
            'null';
        const lang = getLanguage();

        selectExistingOption('#deptcode', deptcode);
        populateSelectOptions('#regioncode', optionData.regions || [], {
            placeholder: 'Select Region',
            valueKey: 'regioncode',
            labelKey: 'regionename',
            labelKeyTa: 'regiontname',
            selectedValue: regioncode,
            fallbackLabel: lang === 'ta' ? prtitle.regiontname : prtitle.regionename
        });
        populateSelectOptions('#districtcode', optionData.districts || [], {
            placeholder: 'Select District',
            valueKey: 'distcode',
            labelKey: 'distename',
            labelKeyTa: 'disttname',
            selectedValue: distcode,
            fallbackLabel: lang === 'ta' ? prtitle.disttname : prtitle.distename
        });
        populateSelectOptions('#category', optionData.categories || [], {
            placeholder: 'Select Category Name',
            valueKey: 'catcode',
            labelKey: 'catename',
            labelKeyTa: 'cattname',
            selectedValue: catcode,
            fallbackLabel: lang === 'ta' ? prtitle.cattname : prtitle.catename,
            attributes: function(item) {
                return {
                    if_subcat: item.if_subcategory || ''
                };
            },
            fallbackAttributes: {
                if_subcat: prtitle.if_subcategory || ''
            }
        });

        if (hasSubcategory) {
            $('.subcatdiv').removeClass('d-none');
            populateSelectOptions('#subcategory', optionData.subcategories || [], {
                placeholder: '---Select Subcategory---',
                valueKey: 'auditeeins_subcategoryid',
                labelKey: 'subcatename',
                labelKeyTa: 'subcattname',
                selectedValue: subcatid,
                fallbackLabel: lang === 'ta' ? prtitle.subcattname : prtitle.subcatename
            });
        } else {
            $('.subcatdiv').addClass('d-none');
            $('#subcategory').html('<option value="">---Select Subcategory---</option>').val('').trigger(
                'change.select2');
        }

        populateInstitutionOptions(optionData.institutions || normalizeInstitutionOptions(prtitle.institution_options),
            institutionIds);
        populateAuditorOptions(optionData.auditors || [], userid);

        $('#action').val('update');
        isEditPopulateInProgress = false;
    }

    function allowOnlyAlphanumeric(event) {
        const char = String.fromCharCode(event.which);
        const inputField = event.target;
        const currentValue = inputField.value || '';

        // Max length hard stop.
        if (currentValue.length >= 150) {
            event.preventDefault();
            return false;
        }

        // English letters: a-z, A-Z
        // Tamil characters: \u0B80-\u0BFF (Tamil Unicode range)
        const regex = /^[a-zA-Z\u0B80-\u0BFF\s]*$/;

        if (!regex.test(char)) {
            event.preventDefault();
            return false;
        }

        // Block 6+ same characters in a row while typing.
        const nextValue = currentValue + char;
        if (/(.)\1{5,}/u.test(nextValue)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Allow only alphabetic characters (English + Tamil) and spaces on paste
    function allowOnlyAlphanumericPaste(event) {
        const pastedText = (event.clipboardData || window.clipboardData).getData('text');
        const inputField = event.target;
        const currentValue = inputField.value || '';
        // Extract only English letters, Tamil characters, and spaces
        const regex = /[a-zA-Z\u0B80-\u0BFF\s]/g;

        const filteredText = pastedText.match(regex) ? pastedText.match(regex).join('') : '';

        if (filteredText) {
            event.preventDefault();
            let combinedValue = (currentValue + filteredText).slice(0, 150);
            combinedValue = combinedValue.replace(/(.)\1{5,}/gu, '$1$1$1$1$1');
            inputField.value = combinedValue;
        } else {
            event.preventDefault();
        }
        return false;
    }

    function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
        const $container = $('#' + containerid).empty();

        files.forEach(file => {
            if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);
            const filePath = file.path || '#';
            const canView = filePath !== '#';

            const fileCard = `
                                    <div class="position-relative ms-2" style="max-width: 320px;">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="p-2 bg-primary-subtle rounded d-flex align-items-center justify-content-center"
                                                         style="width:50px;height:36px;">
                                                        <i class="ti ti-file-text text-primary fs-6"></i>
                                                    </div>

                                                    <div class="text-truncate" style="max-width: 300px;">
                                                        <a class="fw-semibold text-dark text-decoration-none text-truncate d-block"
                                                           href="${filePath}"
                                                           target="_blank"
                                                           title="${file.name}">
                                                            ${file.name}
                                                        </a>
                                                        <a class="small ${canView ? '' : 'disabled text-muted'}"
                                                           ${canView ? `href="${filePath}" target="_blank"` : 'href="javascript:void(0)"'}
                                                           >View</a>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-danger remove-uploaded-file" title="Remove file">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

            $container.append(fileCard);
        });
    }




    $(document).on('click', '.remove-uploaded-file', function() {
        const previewHref = $('#uploaded_file_container a.fw-semibold').attr('href') || '';
        if (previewHref.startsWith('blob:')) {
            URL.revokeObjectURL(previewHref);
        }
        $('#praudit_file').val('');
        $('#uploaded_file_container').empty();
        $('#uploadid').val('');
        $('#add-file-btn').removeClass('d-none');
        if (prauditValidator) prauditValidator.element('#praudit_file');
    });

    function getCategoriesBasedOnDept(deptcode, selectedCatcode = null, onLoaded = null, regioncode = '', districtcode =
        '') {
        const lang = getLanguage();
        deptcode = (deptcode || '').toString().trim();
        selectedCatcode = selectedCatcode != null ? String(selectedCatcode).trim() : null;
        const catcodeDropdown = $('#category');
        const subcategoryDropdown = $('#subcategory');
        catcodeDropdown.html('<option value="">Select Category Name</option>');
        subcategoryDropdown.html('<option value="">---Select Subcategory---</option>');
        $('#instid').html('').val(null).trigger('change.select2');
        if (!deptcode) {
            catcodeDropdown.append('<option value="" disabled>No Category Available</option>');
            if (typeof onLoaded === 'function') onLoaded();
            return;

        }
        if (deptcode) {
            $.ajax({
                url: "/manualtemplate/getcategories",
                type: "POST",
                data: {
                    deptcode: deptcode,
                    regioncode: regioncode || $('#regioncode').val() || '',
                    districtcode: districtcode || $('#districtcode').val() || '',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const categoryData = Array.isArray(response) ? response : (response.data || []);
                    if (categoryData.length > 0) {
                        categoryData.forEach(category => {
                            const catVal = String(category.catcode).trim();
                            catcodeDropdown.append(
                                `<option value="${catVal}" ${catVal === selectedCatcode ? 'selected' : ''
                                    } if_subcat="${category.if_subcategory || ''}">${lang === 'ta' ? category.cattname : category.catename}</option>`

                            );
                        });
                        if (selectedCatcode) {
                            catcodeDropdown.val(resolveCategorySelectValue(selectedCatcode));
                        }
                    } else {
                        catcodeDropdown.append('<option disabled>No Categories Available</option>');
                    }
                    if (typeof onLoaded === 'function') onLoaded();
                },
                error: function() {
                    alert('Error fetching categories. Please try again.');
                    if (typeof onLoaded === 'function') onLoaded();
                }
            });
        }
    }



    function onchange_category(catcode = '', subcategory = '', if_subcat = '', selectedInstitution = '') {

        catcode = catcode || $('#category').val();
        var selectedOption = $('#category').find(':selected');
        if_subcat = if_subcat || selectedOption.attr('if_subcat');

        const subcategoryDropdown = $('#subcategory');
        subcategoryDropdown.empty(); // clear old options

        if (!catcode) {
            subcategoryDropdown.append(`<option value="">---Select Subcategory---</option>`);
            $('#instid').html('').val(null).trigger('change.select2');
            return;
        }

        if (if_subcat !== 'Y') {
            $('.subcatdiv').addClass('d-none');
            subcategoryDropdown.append(`<option value="">---Select Subcategory---</option>`);
            loadInstitutions({
                deptcode: $('#deptcode').val(),
                regioncode: $('#regioncode').val(),
                districtcode: $('#districtcode').val(),
                category: catcode,
                subcatcode: '',
                selectedInstitution: selectedInstitution
            });
            return;
        }

        $('.subcatdiv').removeClass('d-none');

        $.ajax({
            url: '/getsubcategory',
            method: 'POST',
            data: {
                catcode: catcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                subcategoryDropdown.append(
                    `<option value="">---Select Subcategory---</option>`
                );

                if (response.success && response.subcategoryData.length > 0) {

                    response.subcategoryData.forEach(function(subcat) {

                        let isSelected = subcat.auditeeins_subcategoryid == subcategory ?
                            'selected' :
                            '';

                        subcategoryDropdown.append(
                            `<option value="${subcat.auditeeins_subcategoryid}"
                                                data-name-en="${subcat.subcatename}"
                                                data-name-ta="${subcat.subcattname}"
                                                ${isSelected}>
                                                ${lang === "en" ? subcat.subcatename : subcat.subcattname}
                                            </option>`
                        );
                    });

                    if (subcategory) {
                        subcategoryDropdown.val(subcategory).trigger('change.select2');
                        loadInstitutions({
                            deptcode: $('#deptcode').val(),
                            regioncode: $('#regioncode').val(),
                            districtcode: $('#districtcode').val(),
                            category: catcode,
                            subcatcode: subcategory,
                            selectedInstitution: selectedInstitution
                        });
                    }

                } else {

                    subcategoryDropdown.append(
                        `<option disabled>No Subcategory Available</option>`
                    );
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    function loadInstitutions({
        deptcode = '',
        regioncode = '',
        districtcode = '',
        category = '',
        subcatcode = '',
        selectedInstitution = []
    }) {
        const institutionDropdown = $('#instid');
        const selectedInstitutionValues = normalizeInstitutionValues(selectedInstitution);

        institutionDropdown.html('');

        if (!deptcode || !category) {
            return;
        }

        $.ajax({
            url: '/manualtemplate/getinstitutions',
            method: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                districtcode: districtcode,
                category: category,
                subcatcode: subcatcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const institutionData = response.data || [];

                institutionData.forEach(function(institution) {
                    const institutionValue = String(institution.instid).trim();
                    institutionDropdown.append(
                        `<option value="${institutionValue}" ${selectedInstitutionValues.includes(institutionValue) ? 'selected' : ''}>${getLanguage() === 'ta' ? institution.insttname : institution.instename}</option>`
                    );
                });

                institutionDropdown.trigger('change.select2');
            },
            error: function(xhr) {
                console.error('Institution AJAX Error:', xhr.responseText);
            }
        });
    }
</script>

@endsection

