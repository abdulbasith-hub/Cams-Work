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

        #large_confirmation_alert.leave-schedule-popup .modal-dialog {
            max-width: min(1320px, calc(100vw - 24px));
        }

        #large_confirmation_alert.leave-schedule-popup .modal-body .container {
            max-width: 100%;
        }

        #leaveScheduleDetailsTable_wrapper {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <?php $sessionj_detail = session('charge');
    // $roleaction = json_decode($roleactioncode, true);
    // $userroleactioncode = $roleaction[0]['roleactionesname'];
    //  print_r($userroleactioncode);
    // print_r($sessionj_detail);
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="leave_app">
                    Leave Application
                </div>
                <div class="card-body collapse show">
                    <form id="leave_form" name="leave_form">
                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                            id="display_error">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @csrf
                        <input type="hidden" class="form-control" id="leave_id" name="leave_id" />
                        <input type="hidden" name="enuserid" value="{{ $encryptedUserId }}">
                        <div class="row mb-2">
                            <div class="col-md-3 mb-1">
                                <label class="form-label required lang" for="validationDefault02" key="from_date">From
                                    date</label>
                                <div class="input-group" onclick="datepicker('from_date','')">
                                    <input type="text" class="form-control datepicker" id="from_date" name="from_date"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-1">
                                <label class="form-label required lang" for="validationDefault02" key="to_date"> To
                                    date</label>
                                <div class="input-group" onclick="datepicker('to_date','')">
                                    <input type="text" class="form-control datepicker" id="to_date" name="to_date"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required lang" key="leave_type" for="validationDefault01">Leave
                                    Type</label>
                                <select class="form-select mr-sm-2" id="leave_type" name="leave_type">
                                    <option value="">---Select Leave Type---</option>
                                    @foreach ($leavetype_det as $leavetype)
                                        <option value="{{ $leavetype->leavetypecode }}"
                                            data-autoapprovedflag="{{ $leavetype->autoapprovedflag ?? 'N' }}">
                                            {{ $leavetype->leavetypeelname }}

                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required lang" key="reason" for="validationDefault01">Reason
                                </label>
                                <textarea class="form-control name" id="reason" name="reason" placeholder="Enter reason for leave"></textarea>
                            </div>
                        </div>


                        <div class="row justify-content-center">
                            <div class="col-md-2 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save </button>
                                <button type="button" class="btn btn-danger mt-3" id="reset_button">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card card_border mt-2">
        <div class="card-header card_header_color lang" key="leave_app_det">Leave Application Details</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="leavedetTable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th>Leave Period</th>
                                <th>Leave Type</th>

                                <th>Reason</th>

                                <th class="all">Action</th>
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
        const holidayList = @json($holidays); // Outputted as ['2025-08-15', '2025-08-20', ...]
        const autoLeaveRemarks = @json(View::shared('autoremakrs'));

        const holidaySet = new Set(holidayList);


        $.validator.addMethod("notWeekendOrHoliday", function(value, element) {
            if (!value) return false;

            // Parse date in dd/mm/yyyy format
            const date = parseDateDMY(value);
            if (!date) return false;

            const formatted = formatDateLocal(date);
            const day = date.getDay();
            const isWeekend = day === 0 || day === 6;
            const isHoliday = holidaySet.has(formatted);

            // Allow only 2025-10-25 even though it's Saturday
            const allowedDate = new Date("2025-10-25");
            allowedDate.setHours(0, 0, 0, 0);
            const inputDate = new Date(date);
            inputDate.setHours(0, 0, 0, 0);
            const isAllowedToday = inputDate.getTime() === allowedDate.getTime();

            // Allow 2025-10-25 even if it's weekend or holiday
            if (isAllowedToday) return true;

            // Otherwise, block weekends/holidays
            return !isWeekend && !isHoliday;
        }, "Selected date cannot be on a weekend or holiday (except 25-Oct-2025).");

        $.validator.addMethod("toDateOnOrAfterFromDate", function(value, element) {
            const fromDate = parseDateDMY($('#from_date').val());
            const toDate = parseDateDMY(value);

            if (!fromDate || !toDate) {
                return true;
            }

            return toDate >= fromDate;
        }, "To date must be same as or after from date.");


        $.validator.addMethod("lettersSpacesTamilOnly", function(value, element) {
            return this.optional(element) || /^[A-Za-z\u0B80-\u0BFF\s.,]+$/.test(value);
        }, "Only Tamil or English letters, spaces, commas and full stops are allowed.");


        function formatDateLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`; // Matches holiday format
        }

        function parseDateDMY(dateStr) {
            // Expecting format dd/mm/yyyy
            const parts = dateStr.split('/');
            if (parts.length !== 3) return null;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1; // zero-based month
            const year = parseInt(parts[2], 10);

            const date = new Date(year, month, day);
            if (date && date.getFullYear() === year && date.getMonth() === month && date.getDate() === day) {
                return date;
            }
            return null; // invalid date
        }


        $('#from_date, #to_date').on('change', function() {
            $(this).valid(); // Re-run validation
            filterLeaveTypeOptions();
            applyAutoRemarksForShortLeave();
        });

        $('#leave_type').on('change', function() {
            applyAutoRemarksForShortLeave();
        });



        $(document).ready(function() {
            fetchAlldata();
            initialize_date();
            filterLeaveTypeOptions();

        });

        function initialize_date() {
            // Initialize 'from_date' datepicker
            $('#from_date').datepicker({
                format: 'dd/mm/yyyy',
                daysOfWeekDisabled: [0, 6], // Disable Sundays (0) and Saturdays (6)
                startDate: (() => {
                    const startDate = new Date();
                    startDate.setMonth(startDate.getMonth() - 1); // Subtract one month
                    return startDate;
                })(), // Minimum date is one month before the current date
                autoclose: true
            });

            // Initialize 'to_date' datepicker with the default start date as today + 11 days
            $('#to_date').datepicker({
                format: 'dd/mm/yyyy',
                daysOfWeekDisabled: [0, 6], // Disable Sundays (0) and Saturdays (6)
                startDate: (() => {
                    const startDate = new Date();
                    startDate.setMonth(startDate.getMonth() - 1); // Subtract one month
                    return startDate;
                })(),
                autoclose: true
            });

            // Update 'to_date' minDate when 'from_date' changes
            $('#from_date').on('changeDate', function() {
                var fromDate = $('#from_date').datepicker('getDate');
                if (fromDate) {
                    // Add 11 days to the selected 'from_date'
                    // fromDate.setDate(fromDate.getDate() + 1);

                    // Update 'to_date' minDate
                    $('#to_date').datepicker('setStartDate', fromDate);

                    // If the selected from_date is before the required minDate for to_date, clear to_date
                    var toDate = $('#to_date').datepicker('getDate');
                    if (toDate && toDate < fromDate) {
                        $('#to_date').datepicker('clearDates');
                    }
                }
                filterLeaveTypeOptions();
                applyAutoRemarksForShortLeave();
            });

            // Update 'from_date' minDate when 'to_date' changes
            $('#to_date').on('changeDate', function() {
                var toDate = $('#to_date').datepicker('getDate');
                if (toDate) {
                    $('#from_date').datepicker('setEndDate', toDate);

                    // If the selected to_date is before the required minDate for from_date, clear from_date
                    var fromDate = $('#from_date').datepicker('getDate');
                    if (fromDate && fromDate > toDate) {
                        $('#to_date').datepicker('clearDates');
                    }
                }
                filterLeaveTypeOptions();
                applyAutoRemarksForShortLeave();
            }); // Add 11 days to the selected 'to_date'
            // toDate.setDate(toDate.getDate() - 1);
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1);
            // Update 'from_date' maxDate

        }

        let holidaysget = [];

        // Fetch holidays (assumes Laravel returns array of objects with 'date' in 'dd/mm/yyyy' format)
        $.ajax({
            url: '/fetch-holidays',
            method: 'GET',
            async: false,
            success: function(data) {
                holidaysget = data.map(item => {
                    const [dd, mm, yyyy] = item.date.split('/');
                    return `${yyyy}-${mm}-${dd}`; // convert to 'yyyy-mm-dd'
                });
                console.log("Formatted Holidays:", holidaysget);
            },
            error: function() {
                console.error("Failed to fetch holidays.");
            }
        });

        // Format a Date object to 'yyyy-mm-dd' string
        function formatISODate(date) {
            const yyyy = date.getFullYear();
            const mm = ('0' + (date.getMonth() + 1)).slice(-2);
            const dd = ('0' + date.getDate()).slice(-2);
            return `${yyyy}-${mm}-${dd}`;
        }

        // Check if a date is a business day (not weekend and not holiday)
        function isBusinessDay(date) {
            const day = date.getDay();
            const formattedDate = formatISODate(date);
            return day !== 0 && day !== 6 && !holidaysget.includes(formattedDate);
        }



        // Add business days
        function addBusinessDays(startDate, daysToAdd) {
            let result = new Date(startDate);
            while (daysToAdd > 0) {
                result.setDate(result.getDate() + 1);
                if (isBusinessDay(result)) {
                    daysToAdd--;
                }
            }
            return result;
        }

        function datepicker(value, setdate) {
            var today = new Date();

            if (value == 'from_date') {
                // Calculate the minimum date (18 years ago)
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);

                // Calculate the maximum date (60 years ago)
                var minDate = today;

            }
            if (value == 'to_date') {
                var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);

                // Calculate the maximum date (60 years ago)
                var minDate = today;
            }

            const selectedFromDate = parseDateDMY($('#from_date').val());
            const selectedToDate = parseDateDMY($('#to_date').val());

            if (value == 'to_date' && selectedFromDate) {
                minDate = selectedFromDate;
            }

            if (value == 'from_date' && selectedToDate) {
                maxDate = selectedToDate;
            }

            // Format the dates to dd/mm/yyyy format
            var minDateString = formatDate(minDate); // Format date to dd/mm/yyyy
            var maxDateString = formatDate(maxDate); // Format date to dd/mm/yyyy

            init_datepicker(value, minDateString, maxDateString, setdate)
        }




        /***********************************Jquery Form Validation **********************************************/

        const $leave_form = $("#leave_form");

        // Validation rules and messages
        $leave_form.validate({
            rules: {
                from_date: {
                    required: true,
                    notWeekendOrHoliday: true
                },
                to_date: {
                    required: true,
                    notWeekendOrHoliday: true,
                    toDateOnOrAfterFromDate: true
                },
                leave_type: {
                    required: true,
                },
                reason: {
                    required: true,
                    lettersSpacesTamilOnly: true
                },
            },

            errorPlacement: function(error, element) {
                // For datepicker fields inside input-group, place error below the input group
                if (element.hasClass('datepicker')) {
                    // Insert the error message after the input-group, so it appears below the input and icon
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    // For other elements, insert the error after the element itself
                    error.insertAfter(element);
                }

            },

            messages: {

                from_date: {
                    required: "Select From Date ",
                },
                to_date: {
                    required: "Select  To Date",
                    toDateOnOrAfterFromDate: "To date must be same as or after from date",
                },
                leave_type: {
                    required: "select Leave Type",
                },
                reason: {
                    required: "Enter Reason",
                },


            }
        });

        // Scroll to the first error field (for better UX)
        function scrollToFirstError() {
            const firstError = $leave_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }

        const leaveTypeOptions = $('#leave_type option[value!=""]').map(function() {
            return {
                value: $(this).val(),
                text: $(this).text().trim(),
                autoapprovedflag: ($(this).data('autoapprovedflag') || 'N').toString().trim()
            };
        }).get();

        function filterLeaveTypeOptions(selectedValue = $('#leave_type').val()) {
            const $leaveType = $('#leave_type');

            $leaveType.empty().append('<option value="">---Select Leave Type---</option>');

            leaveTypeOptions.forEach(function(option) {
                $leaveType.append(
                    $('<option>', {
                        value: option.value,
                        text: option.text
                    }).attr('data-autoapprovedflag', option.autoapprovedflag)
                );
            });

            if (selectedValue && $leaveType.find(`option[value="${selectedValue}"]`).length) {
                $leaveType.val(selectedValue);
            } else {
                $leaveType.val('');
            }

            $leaveType.valid();
        }

        function isAutoApprovedLeaveSelected() {
            return ($('#leave_type option:selected').data('autoapprovedflag') || 'N').toString().trim() === 'Y';
        }

        function isRestrictedHolidayLeaveSelected() {
            const $selectedLeaveType = $('#leave_type option:selected');
            const leaveTypeCode = ($selectedLeaveType.val() || '').toString().trim().toUpperCase();
            const leaveTypeName = ($selectedLeaveType.text() || '').toString().trim().toUpperCase();

            return leaveTypeCode === 'RH' ||
                leaveTypeName === 'RH' ||
                leaveTypeName.includes('RESTRICTED');
        }

        let autoRemarksCheckRequest = null;
        let autoRemarksCheckToken = 0;

        function applyAutoRemarksForShortLeave() {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            const maxshortleave = parseInt('<?php echo $maxshortleave; ?>', 10);
            const currentReason = $('#reason').val().trim();

            if (!fromDate || !toDate || !maxshortleave) {
                if (currentReason === autoLeaveRemarks) {
                    $('#reason').val('').valid();
                }
                return;
            }

            const leavedays = calculateWorkingDays(fromDate, toDate);

            if (!isAutoApprovedLeaveSelected() || leavedays <= 0 || leavedays > maxshortleave) {
                if (currentReason === autoLeaveRemarks) {
                    $('#reason').val('').valid();
                }
                return;
            }

            if (isRestrictedHolidayLeaveSelected()) {
                const requestToken = ++autoRemarksCheckToken;

                if (currentReason === autoLeaveRemarks) {
                    $('#reason').val('').valid();
                }

                if (autoRemarksCheckRequest) {
                    autoRemarksCheckRequest.abort();
                }

                autoRemarksCheckRequest = $.ajax({
                    url: '/check_leave_schedule_details',
                    type: 'POST',
                    data: {
                        from_date: fromDate,
                        to_date: toDate,
                        leave_type: $('#leave_type').val(),
                        leave_id: $('#leave_id').val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (requestToken !== autoRemarksCheckToken) {
                            return;
                        }

                        if (response.success && (response.leave_limit_blocked || response
                                .leave_execution_blocked)) {
                            if ($('#reason').val().trim() === autoLeaveRemarks) {
                                $('#reason').val('').valid();
                            }
                            return;
                        }

                        if ($('#from_date').val() === fromDate && $('#to_date').val() === toDate && $(
                                '#leave_type').val()) {
                            if ($('#reason').val().trim() === autoLeaveRemarks) {
                                $('#reason').val('').valid();
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort' && $('#reason').val().trim() === autoLeaveRemarks) {
                            $('#reason').val('').valid();
                        }
                    }
                });
                return;
            }

            if ($('#reason').val().trim() === autoLeaveRemarks) {
                $('#reason').val('').valid();
            }
        }

        /***********************************Jquery Form Validation **********************************************/
        function reset_form() {

            $('#display_error').hide();
            var validator = $("#leave_form").validate();
            validator.resetForm();
            initialize_date();
            // fetchAlldata();
            changeButtonAction('leave_form', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert))
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
        /***********************************Submission Button Function**********************************************/
        function showLeaveForwardConfirmation(leavedays, fromDate, toDate, autoMandayExtension = false, customConfirmation =
            null) {
            const leaveDayText = `day${leavedays > 1 ? 's' : ''}`;
            var confirmation = customConfirmation || (autoMandayExtension ?
                `Do you want to get leave for ${leavedays} ${leaveDayText} from ${fromDate} to ${toDate}?` :
                `You are applying for leave for ${leavedays} ${leaveDayText} from ${fromDate} to ${toDate}. Are you sure you want to forward the leave application?`
                );

            $('#process_button').off('click').on('click', function() {
                get_insertdata('finalise', leavedays, autoMandayExtension);
            });

            passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                'alert_body', 'forward_alert');
        }

        function formatScheduleDate(inputDate) {
            if (!inputDate) {
                return '-';
            }

            const date = new Date(inputDate);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${day}/${month}/${year}`;
        }

        function escapeHtml(value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showShortLeaveSchedulePopup(scheduleDetails, leavedays, fromDate, toDate) {
            let tableHTML = `
	            <div class="mb-2">
	                <strong>Selected Leave:</strong> ${escapeHtml(fromDate)} to ${escapeHtml(toDate)}
	                <span class="ms-2">(${leavedays} day${leavedays > 1 ? 's' : ''})</span>
	            </div>
		            <div class="table-responsive">
		                <table id="leaveScheduleDetailsTable"
		                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
	                    <thead>
	                        <tr>
	                            <th>S.No</th>
	                            <th class = 'text-wrap'>Institution Name</th>
	                            <th class = 'text-wrap'>Team Head</th>
	                            <th class = 'text-wrap'>Team Members</th>


		                            <th class = 'text-wrap'>Entry Meeting Date</th>
                                    <th class = 'text-wrap'>Team Size</th>
                                     <th class = 'text-wrap'>Mandays (Existing)</th>
		                            <th class = 'text-wrap'>Proposed ExitMeeting Date (existing)</th>
		                            <th class = 'text-wrap'>Leave Taken(days)</th>

		                            <th class = 'text-wrap'>Mandays (revised)</th>
		                            <th class = 'text-wrap'>Proposed ExitMeeting Date (revised)</th>
		                        </tr>
		                    </thead>
	                    <tbody>
	        `;

            scheduleDetails.forEach((item, index) => {
                tableHTML += `
	                <tr>
	                    <td>${index + 1}</td>
	                    <td class = 'text-wrap'>${escapeHtml(item.instename)}</td>
	                    <td class = 'text-wrap'>${escapeHtml(item.teamhead)}</td>
	                    <td class = 'text-wrap'>${escapeHtml(item.teammembers)}</td>
		                    <td class = 'text-wrap'>${formatScheduleDate(item.entrymeetdate)}</td>
                            <td class = 'text-wrap'>${escapeHtml(item.teamsize)}</td>
                            <td class = 'text-wrap'>${escapeHtml(item.oldmandays)}</td>
		                    <td class = 'text-wrap'>${formatScheduleDate(item.proposedexitmeetdate)}</td>
		                    <td class = 'text-wrap'>${escapeHtml(item.extramandays)}</td>

		                    <td class = 'text-wrap'>${escapeHtml(item.newmandays)}</td>
		                    <td class = 'text-wrap'>${formatScheduleDate(item.newproposedexitmeetdate)}</td>
		                </tr>
		            `;
            });

            tableHTML += `
	                    </tbody>
	                </table>
	            </div>
	        `;

            $('#large_confirmation_alert').addClass('leave-schedule-popup');
            passing_large_alert('Schedule Details', tableHTML, 'large_confirmation_alert',
                'large_alert_header', 'large_alert_body', 'forward_alert');

            if ($.fn.dataTable.isDataTable('#leaveScheduleDetailsTable')) {
                $('#leaveScheduleDetailsTable').DataTable().clear().destroy();
            }

            $('#leaveScheduleDetailsTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                lengthChange: false,
                ordering: false,
                scrollX: true,
                autoWidth: false,
            });

            $('#large_confirmation_alert').one('hidden.bs.modal', function() {
                if ($.fn.dataTable.isDataTable('#leaveScheduleDetailsTable')) {
                    $('#leaveScheduleDetailsTable').DataTable().destroy();
                }
                $('#large_confirmation_alert').removeClass('leave-schedule-popup');
            });

            $('#large_modal_process_button').html('Continue');
            $('#large_modal_process_button').removeAttr('data-bs-dismiss');
            $('#large_modal_process_button').off('click').on('click', function() {
                $('#large_confirmation_alert').modal('hide');
                showLeaveForwardConfirmation(leavedays, fromDate, toDate, true);
            });
        }

        function checkShortLeaveScheduleThenConfirm(leavedays, fromDate, toDate) {
            var maxshortleave = parseInt('<?php echo $maxshortleave; ?>', 10);
            var shouldCheckLeaveLimit = isRestrictedHolidayLeaveSelected() ||
                (isAutoApprovedLeaveSelected() && maxshortleave && leavedays <= maxshortleave);

            if (!shouldCheckLeaveLimit) {
                showLeaveForwardConfirmation(leavedays, fromDate, toDate);
                return;
            }

            $.ajax({
                url: '/check_leave_schedule_details',
                type: 'POST',
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    leave_type: $('#leave_type').val(),
                    leave_id: $('#leave_id').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.leave_limit_blocked) {
                        passing_alert_value('Alert', response.message || 'Leave limit exceeded.',
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                        return;
                    }

                    if (response.success && response.leave_execution_blocked) {
                        showLeaveForwardConfirmation(response.leave_days || leavedays, fromDate, toDate, false,
                            response.message ||
                            'Auto approval leave limit is already used for this schedule. This leave will be forwarded in normal flow. Do you want to continue?'
                            );
                        return;
                    }

                    if (response.success && response.is_short_leave && response.has_schedule) {
                        showShortLeaveSchedulePopup(response.data, response.leave_days || leavedays,
                            fromDate, toDate);
                    } else {
                        showLeaveForwardConfirmation(response.leave_days || leavedays, fromDate, toDate);
                    }
                },
                error: function(xhr) {
                    var response = xhr.responseJSON || {};
                    passing_alert_value('Alert', response.message || 'Unable to check schedule details.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            });
        }

        $(document).on('click', '#buttonaction', function(event) {
            event.preventDefault(); // Prevent form submission

            if ($leave_form.valid()) {
                leavedays = calculateWorkingDays($('#from_date').val(), $('#to_date').val())

                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();
                checkShortLeaveScheduleThenConfirm(leavedays, fromDate, toDate);

            } else {
                scrollToFirstError();
            }
        });
        //reset the form
        $('#reset_button').on('click', function() {

            reset_form(); // Call the reset_form function
        });

        $(document).on('click', '.edit_btn', function() {
            // Add more logic here
            // alert();
            var id = $(this).attr('id'); //Getting id of user clicked edit button.

            if (id) {
                reset_form();
                fetchsingle_data(id)

            }
        });
        $(document).on('click', '.fwd_btn', function() {

            // alert();
            var id = $(this).attr('id');
            var transtypecode = $(this).attr('transtypecode');

            if (id) {
                var confirmation = 'Are you sure to forward the leave application?';
                document.getElementById("process_button").onclick = function() {
                    // getForwardTo_data(id, transtypecode);
                    forward_application(id, transtypecode)
                };
                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                // reset_form();
                // getTeamhead_det(id);

            }
        });

        /***********************************Submission Button Function**********************************************/

        /***********************************Insert, Update, Edit Leave**********************************************/
        function get_insertdata(action, leavedays, autoMandayExtension = false) {

            var requestSent = false;

            if (!requestSent) {

                $('#buttonaction').attr('disabled', true);
                var formData = $('#leave_form').serializeArray();

                formData.push({
                    name: 'leavedays',
                    value: leavedays
                });

                var maxshortleave = parseInt('<?php echo $maxshortleave; ?>', 10);

                var longleave = leavedays > maxshortleave ? 'Y' : 'N';

                formData.push({
                    name: 'longleave',
                    value: longleave
                });

                formData.push({
                    name: 'auto_manday_extension',
                    value: autoMandayExtension ? 'Y' : 'N'
                });


                if (action === 'finalise') {
                    finaliseflag = 'F';
                } else if (action === 'insert') {
                    finaliseflag = 'Y';
                }

                // Push the finaliseflag to the formData array
                formData.push({
                    name: 'finaliseflag',
                    value: finaliseflag
                });


                $.ajax({
                    url: '/storeOrUpdateLeave',
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        if (response.status == 'success') {
                            passing_alert_value('Confirmation', response.message,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');

                            reset_form();
                            fetchAlldata();
                            table.ajax.reload();

                        } else {
                            alert(response.message);

                        }

                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);

                        var errorMessage = response.message ||
                            'An unknown error occurred';
                        // $('#display_error').show();
                        // $('#display_error').text(errorMessage);
                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');


                        // Optionally, log the error to console for debugging
                        console.error('Error details:', xhr, status, error);
                    },
                    complete: function() {
                        // Optionally, you can re-enable the button here if desired
                        $('#buttonaction').removeAttr('disabled');
                    }
                });
            }
        }

        function fetchsingle_data(leaveid) {
            $.ajax({
                url: 'fetchsingle_data', // Your API route to get user details
                method: 'POST',
                data: {
                    leaveid: leaveid
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        $('#display_error').hide();
                        changeButtonAction('leave_form', 'action', 'buttonaction', 'reset_button',
                            'display_error', @json($updatebtn), @json($clearbtn),
                            @json($update))
                        // validator.resetForm();

                        const leave_det = response.data; // The array of schedule data

                        datepicker('from_date', convertDateFormatYmd_ddmmyy(leave_det[0].fromdate));
                        datepicker('to_date', convertDateFormatYmd_ddmmyy(leave_det[0].todate));


                        $('#leave_id').val(leave_det[0].encrypted_leaveid);
                        filterLeaveTypeOptions(leave_det[0].leavetypecode);
                        $('#reason').val(leave_det[0].reason);


                    } else {
                        alert('Schedule Details not found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        /***********************************Insert, Update, Edit Leave**********************************************/



        function fetchAlldata() {

            if ($.fn.dataTable.isDataTable('#leavedetTable')) {
                $('#leavedetTable').DataTable().clear().destroy();
            }

            var table = $('#leavedetTable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "ajax": {
                    "url": "/fetchall_leavedata", // Your API route for fetching data
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Pass CSRF token in headers
                    },
                    "dataSrc": function(json) {

                        if (json.data && json.data.length > 0) {
                            $('#tableshow').show();
                            $('#leavedetTable_wrapper').show();
                            $('#no_data').hide(); // Hide custom "No Data" message
                            return json.data;
                        } else {
                            $('#tableshow').hide();
                            $('#leavedetTable_wrapper').hide();
                            $('#no_data').show(); // Show custom "No Data" message
                            return [];
                        }
                    }
                },
                "columns": [{
                        "data": null, // Serial number column
                        "render": function(data, type, row, meta) {
                            return meta.row + 1; // Serial number starts from 1
                        }
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
                            if (fromdate === todate) {
                                return ` ${fromdate}`;
                            } else {
                                return ` ${fromdate} - ${todate}`;
                            }

                        }
                    },
                    {
                        "data": "leavetypeelname"
                    },


                    {
                        "data": "reason"
                    },


                    {
                        "data": "encrypted_leaveid", // Use the encrypted deptuserid
                        "render": function(data, type, row) {
                            if (row.processcode === 'S') {
                                // Check if statusflag is 'N'
                                return `<center>
                        <a class="btn editicon edit_btn" id="${data}">
                            <i class="ti ti-edit fs-4"></i>
                        </a>
                         <a class="btn editicon fwd_btn" id="${data}" transtypecode="${row.transactiontypecode}">
                            <i class="ti ti-corner-up-right-double fs-4"></i>
                        </a>
                    </center>`;
                            } else if (row.processcode === 'F') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-primary finalize_btn" id="${data}">
                            Forwarded
                        </button>
                    </center>`;
                            } else if (row.processcode === 'I') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-danger finalize_btn" id="${data}">
                            Rejected
                        </button>
                    </center>`;
                            } else if (row.processcode === 'P') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-success finalize_btn" id="${data}">
                            Approved
                        </button>
                    </center>`;
                            }
                        }
                    }
                ]
            });
        }

        // function getForwardTo_data(leaveid, transtypecode) {
        //     var leaveid = leaveid;
        //     var transtypecode = transtypecode;
        //     $.ajax({
        //         url: 'fetchforwardto_data', // Your API route to get user details
        //         method: 'POST',
        //         data: {
        //            // leaveid: leaveid
        //            transtypecode : transtypecode
        //         },
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
        //                 'content') // CSRF token for security
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 $('#display_error').hide();
        //                 change_button_as_update('leave_form', 'action', 'buttonaction',
        //                     'display_error', '', '');
        //                 // validator.resetForm();

        //                 // const teamdet = response.data[0]; // The array of schedule data
        //                 // var forwardto_userid = teamdet.userid;
        //                 // var forwardto_userchargeid = teamdet.userchargeid;

        //                 if (teamdet) {

        //                     forward_application(forwardto_userid, forwardto_userchargeid, leaveid,
        //                         transtypecode);
        //                         forward_application( leaveid, transtypecode,userid)
        //                 }


        //             } else {
        //                 alert(' Details not found');
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });
        // }

        function forward_application(id, transtypecode) {


            $.ajax({
                url: '/transaction/forward_application', // Your API route to get user details
                method: 'POST',
                data: {
                    // userid: userid,
                    // userchargeid: forwardto_userchargeid,
                    id: id,
                    transactiontypecode: transtypecode,
                    action: 'first'


                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.status == 'success') {
                        // $('#display_error').hide();
                        // change_button_as_update('othertrans_form', 'action', 'buttonaction',
                        //     'display_error', '', '');
                        // // validator.resetForm();

                        // passing_alert_value('Confirmation', response.message,
                        //     'confirmation_alert', 'alert_header', 'alert_body',
                        //     'confirmation_alert');

                        // reset_form();
                        // fetchAlldata(lang);
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');

                        reset_form();
                        fetchAlldata();
                        table.ajax.reload();


                    } else {
                        alert(response.message);

                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }


        function calculateWorkingDays(fromDateStr, toDateStr) {
            const holidayList = @json($holidays); // must be ['YYYY-MM-DD']
            const holidaySet = new Set(holidayList);

            let fromDate = parseDateDMY(fromDateStr);
            let toDate = parseDateDMY(toDateStr);

            if (!fromDate || !toDate) {
                return 0;
            }

            if (fromDate > toDate) {
                return 0;
            }

            let workingDays = 0;
            let currentDate = new Date(fromDate);

            while (currentDate <= toDate) {
                const day = currentDate.getDay();
                const formatted = formatDateLocal(currentDate);

                const isWeekend = (day === 0 || day === 6);
                const isHoliday = holidaySet.has(formatted);

                if (!isWeekend && !isHoliday) {
                    workingDays++;
                }

                currentDate.setDate(currentDate.getDate() + 1);
            }

            return workingDays;
        }
    </script>
@endsection
