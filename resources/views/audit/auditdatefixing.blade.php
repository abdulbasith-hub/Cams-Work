<?php
// $endDate = $inst->first()->scheduleendate;
$endDate = $inst->first()->scheduleenddate;
$reschedulests = '';
$encid = '';
if (isset($_GET['status'])) {
    $reschedulests = $_GET['status'];
    $encid = $_GET['encschid'];
}

if ($inst->first()->annadhanam_only == 'Y') {
    $show_annadhanam_Year = '';
} else {
    $show_annadhanam_Year = 'style="display:none"';
}

$Fromquarter = $Quarter['fromquarter'];
$HighMandays = $inst->first()->carryforward;
$Toquarter = $Quarter['toquarter'];

?>@section('content')
@extends('index2')
@include('common.alert')
<style>
    table.dataTable td,
    table.dataTable th {
        word-wrap: break-word;
        white-space: normal;
    }

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

    .holiday-red {
        color: #ff0000 !important;
        /* Red text */
        border-radius: 50%;
        /* Optional: Makes the date look like a circle */
    }

    .select2-search__field {
        display: none !important;
    }

    /* Style the total days count with a border and rounded corners */
    .total-days-container {
        display: inline-block;
        padding: 3px 15px;
        border-radius: 10px;
        /* Rounded corners */
        font-size: 15px;
        font-weight: bold;
        background-color: #f1f1f1;
        /* Light background color */
        color: #333;
        /* Text color */
    }
</style>
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<?php $currentauditplanid = $inst->first()->auditplanid; ?>

<div class="row">
    <div class="col-12">
        <!-- <div class="repeater-default">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div data-repeater-list="">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div data-repeater-item=""> -->
        <div class="card card_border" style="border-color: #7198b9">

            <div class="card-header card_header_color lang" key="auditschedule_title">
                Audit Schedule Details
            </div>


            <div class="card-body collapse show">
                <form id="audit_schedule" name="audit_schedule">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="audit_scheduleid" id="audit_scheduleid" value="" />
                    <input type="hidden" name="isparalelinst" id="isparalelinst"
                        value="{{ $inst->first()->_isparalelinst ?? '' }}" />
                    <input type="hidden" name="as_code" id="as_code" value="" />
                    <input type="hidden" name="ap_code" id="ap_code"
                        value="{{ $inst->first()->auditplanid ?? '' }}" />
                    <!-- <input type="hidden" name="auditmode" id="auditmode"
                        value="{{ $inst->first()->auditmode ?? '' }}" /> -->
                    <input type="hidden" name="planmappingid" id="planmappingid"
                        value="{{ $inst->first()->planmappingid ?? '' }}" />


                    <div class="card" style="border-color: #7198b9">
                        {{-- <div class="border-bottom title-part-padding">
                        <h4 class="card-title mb-0">Institute</h4>
                    </div> --}}
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="inst_title">Institution </label>
                                    <input type="hidden" id="inst_code" name="inst_code"
                                        value="{{ $inst->first()->auditeeinstitutionid ?? '' }}">
                                    <select class="form-select mr-sm-2" id="inst_name" name="inst_name" disabled>

                                        @foreach ($inst as $institution)
                                            <option value="{{ $institution->instid }}"
                                                data-instename="{{ $institution->instename }}"
                                                data-insttname="{{ $institution->insttname }}">
                                                {{ $institution->instename }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="tot_mandays">Total Mandays </label>
                                    <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                        placeholder="Total Mandays "
                                        value="{{ $inst->first()->spilloverflag == 'Y' ? $inst->first()->remainingmandays ?? '' : $inst->first()->mandays }}"
                                        required disabled />
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="tot_teamsize">Total Team Size
                                    </label>
                                    <input type="text" class="form-control" id="total_teamsize" name="total_teamsize"
                                        placeholder="Total Team Size"
                                        value="{{ $inst->first()->team_member_count ?? '0' }}" required disabled />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label lang required" key="teamhead_label">Team Head </label>
                                    <input type="hidden" class="form-control" value={{ $inst->first()->userid ?? '' }}
                                        id="th_uid" name="th_uid" />
                                    <select class="form-select mr-sm-2" id="th_uid_name" name="th_uid_name" disabled>

                                        @foreach ($inst as $institution)
                                            <option value="{{ $institution->userid }}"
                                                data-desigename="{{ $institution->username }}  -  {{ $institution->desigelname }}"
                                                data-desigtname="{{ $institution->usertamilname }}  - {{ $institution->desigtlname }}">
                                                {{ $institution->username }} -
                                                {{ $institution->desigelname }}
                                            </option>
                                        @endforeach



                                    </select>




                                </div>
                                <div class="col-md-4 ">
                                    <label class="form-label lang required" key="teammember_label">Team Members
                                    </label>
                                    <p class="show_teammembers"
                                        style="border:1px solid #D3D3D3;padding:10px;border-radius:10px;"></p>


                                </div>

                                <!--<div class="col-md-4 ">
                                                                            <label class="form-label lang required" key="teammember_label" >Team Member </label>
                                                                            <select class="select2 form-control custom-select" multiple="multiple"
                                                                                id="tm_uid"  aria-placeholder="Select Member" disabled>
                                                                                {{-- @foreach ($inst as $teammember)
                                        <option value="{{ $teammember->teamMember }}">
                                {{ $teammember->teammemberName }} -
                                {{ $teammember->chargedescription }}

                                </option>
                                @endforeach --}}

                                                                            </select>


                                                                        </div>-->
                                <!-- <div class="col-md-4">
                                                            <label class="form-label lang required" key="rcno_label"
                                                                >RC. No</label>
                                                            <input type="text" class="form-control" placeholder="Enter R.C No"
                                                                id="rc_no" disabled />

                                                            <input type="hidden" name="rc_no" id="hidden_rcno" />
                                                        </div> -->
                                <div class="col-md-4">
                                    <label class="form-label lang required" key="rcno_label">RC. No</label>
                                    <input type="text" class="form-control" placeholder="Enter R.C No"
                                        id="rc_no" disabled value="<?php echo $rcno; ?>" />

                                    <input type="hidden" name="rc_no" id="hidden_rcno"
                                        value="<?php echo $rcno; ?>" />
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="audityear_label">Audit
                                        Year</label>
                                    <select name="yearselected[]" id="yearselected" class="select2 form-control"
                                        multiple="multiple" data-placeholder-key="year_ph">
                                        <option value="" disabled>Select Year</option>
                                        @foreach ($auditperiod as $aud_period)
                                            <option value="{{ $aud_period->auditperiodid }}">
                                                {{ $aud_period->audit_period }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div <?php echo $show_annadhanam_Year; ?> class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="annadhanam_year">Annadhanam
                                        Year</label>
                                    <select name="annadhanam_yearselected[]" id="annadhanam_yearselected"
                                        class="select2 form-control" multiple="multiple">
                                        <option value="" disabled>Select Year</option>
                                        @foreach ($annadhanamperiod as $annathanam_period)
                                            <option value="{{ $annathanam_period->auditperiodid }}">
                                                {{ $annathanam_period->audit_period }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="fromdate_label">From date</label>
                                    <div class="input-group" onclick="datepicker('from_date','')">
                                        <input type="text" class="form-control datepicker" readonly id="from_date"
                                            name="from_date" placeholder="dd/mm/yyyy" />
                                        <span class="input-group-text">
                                            <i class="ti ti-calendar fs-5"></i>
                                        </span>
                                    </div>

                                </div>
                                <input type="hidden" name="to_date" id="to_date_hidden" />
                                <div class="col-md-4 mb-1">
                                    <label class="form-label lang required" key="todate_label"> To date</label>
                                    <div class="input-group" onclick="datepicker('to_date','')">
                                        <input type="text" disabled class="form-control datepicker" readonly
                                            name="to_datepicker" id="to_date" placeholder="dd/mm/yyyy" />
                                        <span class="input-group-text">
                                            <i class="ti ti-calendar fs-5"></i>
                                        </span>
                                    </div>
                                </div>

                                <!--<div class="col-md-2 mb-1">
                                                            <p id="totalDaysLabel"><b class="lang" key="tot_workdays">Total Working
                                                                    Days</b></p>
                                                            <div class="total-days-container">
                                                                <span id="totaldays">0</span>
                                                            </div>
                                                        </div>-->
                                <!--<label id="totaldays-error" class="error" for="totaldays"></label>
                                                                            <div class="col-md-2 mb-1">
                                                                                    <p  id="totalDaysLabel"><b>Total Man Days Per Member</b></p>
                                                                                    <div class="total-days-container">
                                                                                        <span id="totaldays">0</span>
                                                                                    </div>
                                                                                </div>-->


                            </div>

                        </div>


                        <div class="row justify-content-center" id="buttonset">
                            <div class="col-md-4 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <button class="btn button_save mt-3 lang" key="savedraft_btn" type="submit"
                                    action="insert" id="buttonaction" name="buttonaction">Save Draft</button>
                                <button class="btn bg-success button_finalise lang mt-3" key="final_btn"
                                    type="submit" id="finalisebtn" action="finalise">
                                    Finalize
                                </button>
                                <!-- <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn" id="reset_button">Clear</button> -->
                            </div>

                        </div>

                        <br>

                </form>

            </div>
        </div>

    </div>
</div>
<div class="card " style="border-color: #7198b9">
    <div class="card-header card_header_color lang" key="auditschedule_title">Audit Schedule Details</div>
    <div class="card-body">
        <div class="datatables">
            <div class="table-responsive hide_this" id="tableshow">
                <table id="scheduletable"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang text-center align-middle" key="s_no">S.No</th>
                            <th class="lang text-center align-middle" key="inst_title">Institute</th>
                            <th class="lang text-center align-middle" key="teamhead_label">Team Head</th>
                            <th class="lang text-center align-middle" key="teammember_label">Team Members</th>
                            {{-- <th>Mandays</th> --}}
                            <th class="lang text-center align-middle" key="teamsize">Team Size</th>
                            <th class="lang text-center align-middle" key="rcno_label">RC Number</th>
                            <th class="lang text-center align-middle" key="audityear_label">Audit Year</th>
                            <th class="lang text-center align-middle" key="period_label">Period</th>

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
</div>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

{{-- data table --}}
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<!-- <script src="../assets/js/jquery.js"></script> -->

<!-- DataTables Core -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons & Export Dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Bootstrap, Select2, and Other Libraries -->
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>



<script>
    $(document).ready(function() {
        // Fetch holidays from Laravel API
        let holidays = [];
        $.ajax({
            url: '/fetch-holidays', // URL of the Laravel route
            method: 'GET',
            async: false,
            success: function(data) {
                holidays = data; // Array of holiday dates in 'dd/mm/yyyy' format
            },
            error: function() {
                console.error("Failed to fetch holidays.");
            }
        });

        // Helper function to get holiday name by date
        function getHoliday(date) {
            const formattedDate = ('0' + date.getDate()).slice(-2) + '/' +
                ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
                date.getFullYear();

            const holiday = holidays.find(h => h.date === formattedDate);
            return holiday ? holiday.name : null;
        }

        // Helper function to check if a date is a holiday
        function isHoliday(date) {
            return getHoliday(date) !== null;
        }

        // Helper function to calculate working days
        function calculateWorkingDays(fromDate, toDate) {
            if (!fromDate || !toDate) return 0;

            let totalDays = 0;

            for (let date = new Date(fromDate); date <= toDate; date.setDate(date.getDate() + 1)) {
                if (date.getDay() !== 0 && date.getDay() !== 6 && !isHoliday(date)) {
                    totalDays++;
                }
            }

            return totalDays;
        }

        // Function to update total working days
        function updateWorkingDays() {
            const fromDate = $('#from_date').datepicker('getDate');
            const toDate = $('#to_date').datepicker('getDate');

            const workingDays = calculateWorkingDays(fromDate, toDate);
            const mandays = parseInt($('#total_mandays')
                .val()); // Get the number of additional days as an integer

            $('#totaldays').text(mandays); // Set the value of the 'totaldays' input field

            // Get the total mandays from the input field
            //const totalMandays = parseInt($('#total_mandays').val(), 10);

            const totalMandays = '{{ $inst->first()->mandays }}';
            const totalTeamSize = '{{ $inst->first()->team_member_count }}'; // Get total team size input
            const adjustedMandays = totalMandays / totalTeamSize;

            var rounded = Math.round(adjustedMandays);

            // Check if the total working days exceed adjusted total mandays
            if (workingDays > rounded) {
                $('#display_error').show(); // Ensure the label is visible
                $('#display_error').text('Total working days range exceed than allowed mandays range');
                //  $('button[type="submit"]').prop('disabled', true);  // Disables all submit buttons


            } else {
                $('#display_error').hide();
                // $('button[type="submit"]').prop('disabled',false);  // Disables all submit buttons

            }
        }

        // Initialize datepickers
        /* $('#from_date , #to_date').datepicker({
             format: 'dd/mm/yyyy',
             daysOfWeekDisabled: [0, 6], // Disable Sundays and Saturdays
             startDate: new Date(),
             autoclose: true,
             beforeShowDay: function(date) {
                 const holidayName = getHoliday(date);
                 if (holidayName) {
                     return {
                         enabled: false,
                         tooltip: holidayName, // Display the holiday name in the tooltip
                         classes: 'holiday-red'
                     };
                 }
                 return true;
             }
         });*/


        function addWorkdays(startDate, daysToAdd) {
            let currentDate = new Date(startDate);
            let addedDays = 1;
            const totalMandays = '{{ $inst->first()->mandays }}';
            const totalTeamSize = '{{ $inst->first()->team_member_count }}'; // Get total team size input
            const adjustedMandays = totalMandays / totalTeamSize;
            var rounded = Math.round(adjustedMandays); // 6

            // $('#totaldays').text(adjustedMandays); // Set the value of the 'totaldays' input field

            //var rounded=2;
            while (addedDays < rounded) {
                currentDate.setDate(currentDate.getDate() + 1);
                // Check if the current day is not a weekend (Saturday or Sunday)
                if (currentDate.getDay() !== 0 && currentDate.getDay() !== 6 && !isHoliday(currentDate)) {
                    addedDays++;
                }
            }

            return currentDate;
        }

        function formatDate(date) {
            // Format the date as yyyy-mm-dd (or customize as needed)
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${day}/${month}/${year}`;
        }

        // Update 'to_date' minDate when 'from_date' changes
        $('#from_date').on('changeDate', function() {
            const fromDate = $('#from_date').datepicker('getDate');
            const mandays = parseInt($('#total_mandays')
                .val()); // Get the number of additional days as an integer

            //  if (fromDate) {
            //                 const endDate = addWorkdays(fromDate, mandays); // Calculate the end date
            //                 const formattedEndDate = formatDate(endDate); // Format the date
            //                 var scheduleEndDate = '<?php echo $endDate; ?>';

            //                 scheduleEndDate = convertDateFormatYmd_ddmmyy(scheduleEndDate)
            //                 const scheduleEnd = parseDDMMYYYY(scheduleEndDate);
            //                 const formattedEnd = parseDDMMYYYY(formattedEndDate);

            //                 // console.log(formattedEndDate);
            //                 //console.log(scheduleEnd);
            //                 // console.log('hi');

            //                 if (formattedEnd > scheduleEnd) {
            //                     console.log('if');
            //                     passing_alert_value('Confirmation',
            //                         'Audit cannot be scheduled as the schedule end date exceeds the limit',
            //                         'confirmation_alert',
            //                         'alert_header',
            //                         'alert_body', 'confirmation_alert');
            //                     $('#to_date_hidden,#to_date').val('');
            //                     return;
            //                 }
            //                 //$('#to_date').val(formattedEndDate); // Set the end date in the input box
            //                 datepicker('to_date', formattedEndDate);
            //                 $('#to_date_hidden').val(formattedEndDate);
            //                 // console.log($('#to_date').val())

            //             }
            if (fromDate) {
                const endDate = addWorkdays(fromDate, mandays); // Calculate the end date
                const formattedEndDate = formatDate(endDate); // Format the date
                //$('#to_date').val(formattedEndDate); // Set the end date in the input box
                datepicker('to_date', formattedEndDate);
                $('#to_date_hidden').val(formattedEndDate);

            }
            updateWorkingDays();
            /*if (fromDate) {
                const minToDate = new Date(fromDate);
                minToDate.setDate(fromDate.getDate() + 1);
                $('#to_date').datepicker('setStartDate', minToDate);

                const toDate = $('#to_date').datepicker('getDate');
                if (toDate && toDate < minToDate) {
                   // $('#to_date').datepicker('clearDates');
                }
            }
            updateWorkingDays(); // Update working days on change
            */
        });

        // Update 'from_date' maxDate when 'to_date' changes
        $('#to_date').on('changeDate', function() {
            const toDate = $('#to_date').datepicker('getDate');
            if (toDate) {
                const maxFromDate = new Date(toDate);
                $('#from_date').datepicker('setEndDate', maxFromDate);

                const fromDate = $('#from_date').datepicker('getDate');
                // if (fromDate && fromDate > maxFromDate) {
                //     $('#from_date').datepicker('clearDates');
                // }
                var scheduleEndDate = '<?php echo $endDate; ?>'; 
                var endDateObj = new Date(scheduleEndDate);

                if (toDate > endDateObj) {

                    $('#to_date').datepicker('setDate', endDateObj);
                    return;
                }
            }
            //updateWorkingDays(); // Update working days on change
        });
    });

    function parseDDMMYYYY(dateStr) {
        const [day, month, year] = dateStr.split('/');
        return new Date(year, month - 1, day); // month is 0-based
    }

    let dataFromServer = [];


    $(document).ready(function() {
        $('#audit_schedule')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        lang = getLanguage();
        initializeDataTable(lang);
        change_lang_for_page(lang);
        fetch_audit_memberdata(selectedTeamMembers = [], lang);

    });

    // Change event for language selection dropdown
    $('#translate').change(function() {
        lang = getLanguage('Y');
        updateTableLanguage(lang); // Update the table with the new language by destroying and recreating it
        change_lang_for_page(lang);
        fetch_audit_memberdata('', lang);
        updateValidationMessages(getLanguage('Y'), 'audit_schedule');

    });

    function change_lang_for_page(lang) {
        // Get selected options for both dropdowns
        var selectedOptionInst = $('#inst_name option:selected'); // Adjust the ID if incorrect
        var selectedOptionDesig = $('#th_uid_name option:selected');
        var username = selectedOptionDesig.data('username');

        if (lang === 'ta') {
            // Switch to English labels
            if (selectedOptionInst.length) {
                selectedOptionInst.text(selectedOptionInst.data('insttname'));
            }
            if (selectedOptionDesig.length) {
                selectedOptionDesig.text(selectedOptionDesig.data('desigtname'));
            }
        } else {
            // Switch to translated labels
            if (selectedOptionInst.length) {
                selectedOptionInst.text(selectedOptionInst.data('instename'));
            }
            if (selectedOptionDesig.length) {
                selectedOptionDesig.text(selectedOptionDesig.data('desigename'));
            }
        }

    }


    // rcno_generate();

    // function rcno_generate() {
    //     // Generate a random number (4-digit example)
    //     const randomNumber = Math.floor(Math.random() * 999999) + 100000;
    //     $('#rc_no').val(randomNumber);
    //     $('#hidden_rcno').val(randomNumber);
    // }

    function datepicker(value, setdate) {
        var ToDate = '<?php echo $Toquarter; ?>';
        var FromDate = '<?php echo $Fromquarter; ?>';

        var maxDate = new Date(ToDate);
        var highmandays = '<?php echo $HighMandays; ?>';

        if (highmandays == 'Y') {
            var maxDate = null;

        }
        var minDate = new Date();
        minDate.setHours(0, 0, 0, 0);

        if (value === 'from_date' && maxDate && minDate > maxDate) {
            maxDate = new Date(minDate);
        }

        var form = 'cleardateform';

        const fromVal = $('#from_date').val();
        const toVal = $('#to_date').val();

        // If setting 'to_date' and 'from_date' has value
        if (value === 'to_date' && fromVal) {
            const parts = fromVal.split('/');
            const fromDate = new Date(parts[2], parts[1] - 1, parts[0]);
            minDate = new Date(fromDate);
        }

        // If setting 'from_date' and 'to_date' has value
        if (value === 'from_date' && toVal) {
            const parts = toVal.split('/');
            const toDate = new Date(parts[2], parts[1] - 1, parts[0]);
            maxDate = new Date(toDate);
        }

        var fromvalclr = `from_date`;
        var tovalclr = `to_date`;
        const [day, month, year] = setdate.split("/");
        const convertedDate = new Date(year, month - 1, day);

        if (maxDate < convertedDate) {
            const cdparts = setdate.split('/');
            const changedmaxdate = new Date(cdparts[2], cdparts[1] - 1, cdparts[0]);
            maxDate = new Date(changedmaxdate);
        }


        const datepickerType = value === 'from_date' ? 'allowtoday' : 'null';
        init_datepicker(value, minDate, maxDate, setdate, form, fromvalclr, tovalclr, datepickerType);

    }



    var room = 1;

    function education_fields(cardCounter) {

        // alert(room);
        var id = "team_size_" + cardCounter;
        var size = $('#' + id).val();

        if (!size) {
            toastr.error("Please select the team size !", "", {
                closeButton: true,
            });
            exit;
        } else {

            if (room < size) {

                room++;
                var objTo = document.getElementById("education_fields_" + cardCounter);
                var divtest = document.createElement("div");
                divtest.setAttribute("class", "mb-3 removeclass" + room);
                var rdiv = "removeclass" + room;
                divtest.innerHTML =
                    '<form class="row"><div class="col-md-4"><label class="form-label" >Team Member </label><select class="form-select mr-sm-2" id="designation"><option>Select Member</option><option value="1">Siva</option><option value="2">Swathi</option><option value="3">Niji </option></select></div><div class="col-md-3"><div class="mb-3"><label class="form-label" >From date</label><input type="date" class="form-control" value="2018-05-13" id="dob"name="dob" /></div></div><div class="col-md-3"><div class="mb-3"><label class="form-label" >To date</label><input type="date" class="form-control" value="2018-05-13" id="dob"name="dob" /></div></div><div class="col-sm-2 mt-4"><div class="mb-3"><label class="form-label" ></label><button class="btn btn-danger" type="button" onclick="remove_education_fields(' +
                    room +
                    ');"> <i class="ti ti-minus"></i> </button> </div></div></form>';

                objTo.appendChild(divtest);
            } else {
                toastr.error("Reached the maximum member list of " + size + "", "", {
                    closeButton: true,
                });
            }
        }

    }

    function remove_education_fields(rid) {
        // Remove the specified row
        $(".removeclass" + rid).remove();
        room--;

        // Reassign classes and update attributes for all remaining rows
        const rows = document.querySelectorAll("[class^='removeclass']");

        rows.forEach((row, index) => {
            // Calculate the new index
            const newIndex = index + 1;

            // Update the class name to reflect the new order
            const oldClass = row.className.match(/removeclass\d+/)[0];
            row.className = row.className.replace(oldClass, `removeclass${newIndex}`);

            // Update the button `onclick` function
            const button = row.querySelector("button[onclick]");
            if (button) {
                button.setAttribute("onclick", `remove_education_fields(${newIndex});`);
            }
        });
    }
    list = 1;

    function listofObjections_field(cardCounter) {
        // Check if the list variable is defined, else initialize it
        if (typeof list === 'undefined') {
            list = 0;
        }

        list++;
        var objTo = document.getElementById("listofObjections_" + cardCounter);
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "mb-3 removeObjclass" + list);
        var rdiv = "removeObjclass" + list;

        // Dynamically add the form row
        divtest.innerHTML =
            '<form class="row"><div class="col-md-4 ">' +
            '<select class="select2 form-control custom-select dynamic-select-' + list + '">' +
            '<option>Select Category</option>' +
            '<option value="CA">Rent </option>' +
            '<option value="NV">Hundial </option>' +
            '<option value="OR">Category 3</option>' +
            '<option value="WA">Category 4</option>' +
            '</select></div>' +
            '<div class="col-md-5 ">' +
            '<select class="select2 form-control dynamic-select-' + list + '" multiple="multiple" >' +
            '<option>Select Sub Category</option>' +
            '<option value="M">Deposit not collected</option>' +
            '<option value="F">Hundial amount not fully deposited in bank</option>' +
            '<option value="T">Hundial theft</option>' +
            '<option value="T">Sub Category 4</option>' +
            '</select></div>' +
            '<div class="col-sm-2 "><div class="">' +
            '<button class="btn btn-danger" type="button" onclick="remove_Objection_fields(' + list + ');">' +
            '<i class="ti ti-minus"></i></button></div></div></form>';

        objTo.appendChild(divtest);

        // Initialize Select2 for the dynamically added elements
        $('.dynamic-select-' + list).select2();
    }


    function remove_Objection_fields(rid) {

        $(".removeObjclass" + rid).remove();
        list--;
    }

    // $(function() {
    //     "use strict";

    //     // Default
    //     // $(".repeater-default").repeater();

    //     // Custom Show / Hide Configurations
    //     $(".file-repeater, .email-repeater").repeater({
    //         show: function() {
    //             $(this).slideDown();
    //         },
    //         hide: function(remove) {
    //             if (confirm("Are you sure you want to remove this item?")) {
    //                 $(this).slideUp(remove);
    //             }
    //         },
    //     });
    // });

    function show_others(cardnumber) {
        var id = 'particulars_' + cardnumber;

        var element = document.getElementById(id);
        var isChecked = document.getElementById(id).checked;

        if (isChecked == false) {


            element.checked = true;
            $('#particulars_div_' + cardnumber).show();
        } else {
            if (isChecked == true) {

                element.checked = false;
                $('#particulars_div_' + cardnumber).hide();
            }
        }

    }

    function show_date(cardnumber, type) {
        // alert(type);
        if (type == "select") {
            $('.selectDate').show();
            $('.rangeDate').hide();
            $('.add_btn').show();

        } else {
            $('.rangeDate').show();
            $('.selectDate').hide();
            $('.add_btn').show();
        }


    }


    /***********************************Jquery Form Validation **********************************************/

    jsonLoadedPromise.then(() => {
        const language = window.localStorage.getItem('lang') || 'en';
        var validator = $("#audit_schedule").validate({
            rules: {
                "yearselected[]": {
                    required: true,
                    minlength: 1
                },
                "annadhanam_yearselected[]": {
                    required: function() {
                        // Check if 'annadhanam_yearselected' div is visible
                        if ($('#annadhanam_yearselected').is(':visible')) {
                            return true; // Make it required if the div is visible
                        }
                        return false; // Otherwise, it's not required
                    },
                    minlength: 1
                },
                from_date: {
                    required: true
                },
                to_date: {
                    required: true
                }

            },
            messages: errorMessages[language],
            errorPlacement: function(error, element) {
                // For datepicker fields inside input-group, place error below the input group
                if (element.hasClass('datepicker')) {
                    // Insert the error message after the input-group, so it appears below the input and icon
                    error.insertAfter(element.closest('.input-group'));
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next(
                        '.select2-container')); // Fix for Select2 dropdowns
                } else {
                    // For other elements, insert the error after the element itself
                    error.insertAfter(element);
                }

            }


        });


        $("#buttonaction").on("click", function(event) {

            event.preventDefault(); // Prevent form submission
            //return;
            if ($("#audit_schedule").valid()) {
                get_insertdata('insert')
            } else {}
        });
        var ExistsforDraft = '<?php echo $DraftStatus['exists']; ?>';
        if (ExistsforDraft == 'Y') {
            var id = '<?php echo $DraftStatus['auditschid']; ?>';
            fetchscehdule_data(id);
        }
        // reset_form();


    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });
    //const $audit_scheduleForm = $("#audit_schedule");

    // Validation rules and messages
    /* $audit_scheduleForm.validate({
         rules: {
             inst_code: {
                 required: true,
             },
             "yearselected[]": {
                 required: true,
                 minlength: 1
             },
             "annadhanam_yearselected[]": {
                     required: function() {
                         // Check if 'annadhanam_yearselected' div is visible
                         if ($('#annadhanam_yearselected').is(':visible')) {
                             return true;  // Make it required if the div is visible
                         }
                         return false; // Otherwise, it's not required
                     },
                     minlength: 1
             },
             from_date: {
                 required: true,
             },
             to_date: {
                 required: true,
             },
             "tm_uid[]": {
                 required: true,
             },
             rc_no: {
                 required: true,
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
             inst_code: {
                 required: "Select Institute ",
             },
             "yearselected[]": {
                 required: "Please select at least one Year",
                 minlength: "Please select at least one year"
             },
             "annadhanam_yearselected[]": {
                 required: "Please select at least one Year",
                 minlength: "Please select at least one year"
             },
             from_date: {
                 required: "Select From Date ",
             },
             to_date: {
                 required: "Select  To Date",
             },
             "tm_uid[]": {
                 required: "Select Team Member",
             },
             rc_no: {
                 required: "Enter RC Number",
             }

             // highlight: function(element, errorClass) {
             //     $(element).removeClass(errorClass); //prevent class to be added to selects
             // },

         }
     });*/




    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $audit_scheduleForm.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    /***********************************Jquery Form Validation **********************************************/
    function reset_form() {

        $('#display_error').hide();
        //var validator = $("#audit_schedule").validate();
        //validator.resetForm();
        $('#yearselected').val(" ").trigger('change');
        $('#yearselected-error').hide();

        if ($('#annadhanam_yearselected').is(':visible')) {

            $('#annadhanam_yearselected').val(" ").trigger('change');
            $('#annadhanam_yearselected-error').hide();

        }


        $("#tm_uid").empty();
        fetch_audit_memberdata('', lang);
        //fetchAlldata();
        change_button_as_insert('audit_schedule', 'action', 'buttonaction', 'display_error', '', '');
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        $('#totaldays').text(0);
    }
    /***********************************Submission Button Function**********************************************/


    $(document).on('click', '#finalisebtn', function() {
        // Prevent form submission (this stops the page from refreshing)
        event.preventDefault();

        //Trigger the form validation
        if ($("#audit_schedule").valid()) {

            get_insertdata('insert', 'finalise_beforeinsert');


        } else {
            // If the form is not valid, show an alert
            // alert("Form is not valid. Please fix the errors.");
        }
    });

    $('#large_modal_process_button').on('click', function() {
        var cfrsize = $('#cfr_size').val();
        if (cfrsize == 0) {
            var confirmation = 'Call for Records should not be empty';
            passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                'alert_body', 'confirmation_alert');
        } else {
            var confirmation = 'Are you sure to send intimation?';
            passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                'alert_body', 'forward_alert');

        }

        $('#large_confirmation_alert .modal-content').addClass('blurred');
        $('#confirmation_alert').css('z-index', 100000);
        $("#process_button").html("Ok");
        $("#process_button").attr("key", "ok");
        translate();
    });

    /**Finalizing Process */
    $('#process_button').on('click', function() {
        $('#finalize').val('F');
        $("#large_confirmation_alert").modal("hide");
        get_insertdata('finalise')

    });

    function passing_large_alert(
        alert_header,
        alert_body,
        alert_name,
        alert_header_id,
        alert_body_id,
        alert_type
    ) {

        const element = document.getElementById("process_button");
        element.classList.remove("btn-danger");

        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        $("#process_button").html("Ok");
        $("#cancel_button").show();
        element.classList.add("btn-success");

        var selectedcolor = localStorage.getItem("selectedColor");
        if (!selectedcolor) selectedcolor = "#3782ce";

        $(".modal-header").css({
            "background-color": selectedcolor
        });
        $("#" + alert_header_id).html(alert_header);
        $("#" + alert_body_id).html(alert_body);

        $("#" + alert_name).modal("show");

        // #593320
    }



    $('#reset_button').on('click', function() {
        reset_form(); // Call the reset_form function
    });
    /***********************************Submission Button Function**********************************************/
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    /*********************************** Insert,update,Finalise,Reset **********************************************/
    function get_insertdata(action, beforeinsert = '') {


        var formData = $('#audit_schedule').serializeArray();

        let selectedValues = [];


        if (action === 'finalise') {
            finaliseflag = 'F';

            $('input[name="callforrecords_checked[]"]:checked').each(function() {
                selectedValues.push($(this).val());
            });

            selectedValues.forEach(value => {
                formData.push({
                    name: 'callforrecords_checked[]',
                    value: value
                });
            });

        } else if (action === 'insert') {
            finaliseflag = 'Y';

            formData.push({
                name: 'beforeinsert',
                value: beforeinsert
            });
        }
        //alert(selectedValues);return;

        // Push the finaliseflag to the formData array
        formData.push({
            name: 'finaliseflag',
            value: finaliseflag
        });

        const to_date = $('#to_date').val()

        const invalidValues = [null, '', 'null', 'undefined'];
        if (invalidValues.includes(to_date)) {
            passing_alert_value('Confirmation', 'Audit To Date is Required', 'confirmation_alert',
                'alert_header', 'alert_body',
                'confirmation_alert');

            return;
        }

        $('#buttonaction').prop('disabled', true);
        $('#finalisebtn').prop('disabled', true);
        $('#process_button').prop('disabled', true);

        $.ajax({
            url: '/audit/storeOrUpdateAuditSchedule', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', Object.values(
                                text)[0], 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    // fetchAlldata();
                    if (response.message == 'audit_scheduled_success' || response.message ==
                        'audit_scheduled_finalize') {
                        if (response.message == 'audit_scheduled_finalize') {
                            reset_form();
                        }

                        initializeDataTable(lang);

                    }

                    fetchscehdule_data(response.schdeuleid)
                    //table.ajax.reload(); // Reload the table
                    // rcno_generate();

                } else if (response.error) {} else if (response.finalise_beforeinsert) {
                    finalizePopupWindow();
                }
            },
            complete: function() {
                $('#process_button').prop('disabled', false);
                $('#buttonaction').removeAttr('disabled');
                $('#buttonaction').removeAttr('disabled');
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
            }

        });
    }

    function finalizePopupWindow() {

        /* =========================
           1. INITIAL VALUES
        ========================== */

        const auditmode = $('#auditmode').val();
        const isPerformance = auditmode === 'P';

        const Accountparticulars = @json($Accountparticulars);
        const prfAuditDet = @json($prfaudit_det);

        const inst_name = $('#inst_name option:selected').text();
        const total_teamsize = $('#total_teamsize').val();
        const th_uid_name = $('#th_uid_name option:selected').text();
        const rcno = $('#hidden_rcno').val();
        const fromdate = $('#from_date').val();
        const todate = $('#to_date').val();
        const teammembers = $('.show_teammembers').html();

        const lang = getLanguage();


        /* =========================
           2. PERFORMANCE AUDIT UI
        ========================== */

        if (isPerformance) {

            const prfHtml = prfAuditDet.map(item => {

                if (!item.filedetails) return '';

                const file = item.filedetails.split('-');
                const fileUrl = '/' + (file[1] || '').replace(/\\/g, '/');

                return `
            <tr>
                <td>

                    <!-- Audit Title Head -->
                    <div style="font-weight:700;font-size:16px;margin-bottom:5px;">
                        Audit Title
                    </div>

                    <!-- Title Name Data -->
                    <div style="margin-bottom:10px;">
                        <div style="font-weight:600;">${item.titleename}</div>
                        <div style="color:#666;">${item.titletname || ''}</div>
                    </div>

                    <!-- Document Head -->
                    <div style="font-weight:700;margin-bottom:8px;">
                       Audit Design Matrix
                    </div>

                    <!-- Download Button -->
                    <div style="margin-bottom:10px;">
                        <a href="${fileUrl}"
                           download
                           class="btn btn-sm btn-outline-primary">
                           Download
                        </a>
                    </div>

                    <!-- PDF Render Section -->
                    <div style="
                        border:1px solid #ccc;
                        height:500px;
                        overflow:auto;
                        border-radius:6px;
                        background:#f8f9fa;
                    ">
                                           </div>

                </td>
            </tr>

            <tr>
                <td style="padding:0">
                    <hr style="margin:15px 0;border-top:1px dashed #ccc">
                </td>
            </tr>
            `;
            }).join('');

            datacontent = `
        <div class="card" style="border-color:#7198b9">
            <div class="card-header card_header_color">
                Performance Audit Details
            </div>

            <div class="card-body">

                <table class="table table-bordered">
                    <tr><td><b>Institution</b></td><td>${inst_name}</td></tr>
                    <tr><td><b>Total Team Size</b></td><td>${total_teamsize}</td></tr>
                    <tr><td><b>Team Head</b></td><td>${th_uid_name}</td></tr>
                    <tr><td><b>Team Members</b></td><td>${teammembers}</td></tr>
                    <tr><td><b>RC.No</b></td><td>${rcno}</td></tr>
                    <tr><td><b>From Date</b></td><td>${fromdate}</td></tr>
                    <tr><td><b>To Date</b></td><td>${todate}</td></tr>
                </table>

                <hr>

                <table class="table table-bordered">
                    <tbody>
                        ${prfHtml}
                    </tbody>
                </table>

            </div>
        </div>`;
        }

        /* =========================
           3. NORMAL AUDIT UI
        ========================== */
        else {

            var accLength = Accountparticulars.original.account_particulars.length || 1;

            var datacontent =
                '<input type="hidden" id="cfr_size" />' +

                // ---------------- Audit Scheduling ----------------
                '<div class="card" style="border-color: #7198b9">' +
                '<div class="card-header card_header_color lang" key="auditschedule_title">Audit Scheduling Details</div>' +
                '<div class="card-body">' +
                '<table style="width:100%;" class="table table-hover w-100 table-bordered display largemodal">' +
                '<tbody>' +

                '<tr><td><b class="lang" key="inst_title">Institution</b></td><td>' + inst_name + '</td></tr>' +
                '<tr><td><b class="lang" key="tot_teamsize">Total Team Size</b></td><td>' + total_teamsize +
                '</td></tr>' +
                '<tr><td><b class="lang" key="teamhead_label">Team Head</b></td><td>' + th_uid_name + '</td></tr>' +
                '<tr><td><b class="lang" key="teammember_label">Team Members</b></td><td>' + teammembers +
                '</td></tr>' +
                '<tr><td><b class="lang" key="rcno_label">RC.No</b></td><td>' + rcno + '</td></tr>' +
                '<tr><td><b class="lang" key="fromdate_label">From Date</b></td><td>' + fromdate + '</td></tr>' +
                '<tr><td><b class="lang" key="todate_label">To Date</b></td><td>' + todate + '</td></tr>' +

                '</tbody></table></div></div>' +

                // ---------------- Account Particulars ----------------
                '<div class="card" style="border-color: #7198b9">' +
                '<div class="card-header card_header_color lang" key="account_particulars_label">Account Particulars</div>' +
                '<div class="card-body">' +
                '<table style="width:100%;" class="table table-hover w-100 table-bordered display largemodal accountparticulars">' +
                '<tbody>' +
                '<tr>' +
                '<td width="30%" rowspan="' + accLength + '">' +
                '<b class="lang" key="account_particulars_name">Account Particulars</b>' +
                '</td>' +
                '<td class="tdforaccountparticular"></td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div></div>' +

                // ---------------- Call For Records ----------------
                '<div class="card" style="border-color: #7198b9">' +
                '<div class="card-header card_header_color lang" key="callforrecords_label">Call for Records</div>' +
                '<div class="card-body">' +
                '<table style="width:100%;" class="table table-hover w-100 table-bordered display largemodal callforrecords">' +
                '<tbody class="tbodycallforrecords"></tbody>' +
                '</table>' +
                '</div></div>';
        }

        /* =========================
           4. INJECT HTML
        ========================== */

        $('#large_confirmation_alert .container').html(datacontent);
        $.each(Accountparticulars.original.account_particulars, function(index, item) {
            var row = (lang === 'en') ?
                item.accountparticularsename + '<br>' :
                item.accountparticularstname + '<br>';

            // If needed, append the row to the container
            $('.accountparticulars .tdforaccountparticular').append(row);

        });
        var callforrecordsArray = [];

        // Iterate through Accountparticulars.original.data
        $.each(Accountparticulars.original.data, function(index, item) {
            // Determine the value to display based on the language
            var callforrecordsName = (lang === 'en') ?
                item.callforrecordsename :
                item.callforrecordstname;

            // Add the value to the array
            callforrecordsArray.push({
                id: item.callforrecordsid, // Assuming `callforrecords_id` is the ID field
                name: callforrecordsName
            });
        });

        $('#cfr_size').val(callforrecordsArray.length);

        var checkboxRows = '';

        // Loop through the array in steps of 2
        for (let i = 0; i < callforrecordsArray.length; i += 2) {
            let firstRecord = callforrecordsArray[i];
            let secondRecord = callforrecordsArray[i + 1] || null; // Handle odd number of items

            checkboxRows += `
                        <tr>
                            <td style="padding:5px !important;width:50%;">
                                <input name="callforrecords_checked[]" class="form-check-input primary" type="checkbox" value="${firstRecord.id}" checked disabled>
                                ${firstRecord.name}
                            </td>
                            <td style="padding:5px !important;width:50%;">
                                ${secondRecord ? `
                                <input name="callforrecords_checked[]" class="form-check-input primary" type="checkbox" value="${secondRecord.id}" checked disabled>
                                ${secondRecord.name}
                                ` : ''}
                            </td>
                        </tr>
                    `;
        }

        // Append rows to table
        $('.callforrecords .tbodycallforrecords').append(checkboxRows);

        passing_large_alert(
            'Send Intimation',
            datacontent,
            'large_confirmation_alert',
            'large_alert_header',
            'large_alert_body',
            'forward_alert',
            'send_intimation_label'
        );

        const sendText = lang === 'ta' ?
            'அறிவிப்பை அனுப்பவும்' :
            'Send Intimation';

        $('#large_modal_process_button')
            .html(sendText)
            .addClass('button_finalize')
            .removeAttr('data-bs-dismiss');

        translate();
    }


    /*********************************** Insert,update,Finalise,Reset **********************************************/

    // function fetchAlldata() {
    //     if ($.fn.dataTable.isDataTable('#scheduletable')) {
    //         $('#scheduletable').DataTable().clear().destroy();
    //     }
    //     var table = $('#scheduletable').DataTable({
    //         "processing": true,
    //         "serverSide": false,
    //         "lengthChange": false,
    //         "scrollX": true,
    //         "ajax": {
    //             "url": "/audit/fetchAllScheduleData", // Your API route for fetching data
    //             "type": "POST",
    //             "headers": {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
    //                     'content') // Pass CSRF token in headers
    //             },
    //             "dataSrc": function(json) {

    //                 if (json.data && json.data.length > 0) {

    //                     $('#tableshow').show();
    //                     $('#scheduletable_wrapper').show();
    //                     $('#no_data').hide(); // Hide custom "No Data" message
    //                     return json.data;
    //                 } else {
    //                     $('#tableshow').hide();
    //                     $('#scheduletable_wrapper').hide();
    //                     $('#no_data').show(); // Show custom "No Data" message
    //                     return [];
    //                 }
    //             }
    //         },
    //         "columns": [


    //             {
    //                 "data": null, // Serial number column
    //                 "render": function(data, type, row, meta) {
    //                     return meta.row + 1; // Serial number starts from 1
    //                 }
    //             },
    //             {
    //                 "data": "instename"
    //             },
    //             {
    //                 "data": "teammembers"
    //             },

    //             // {
    //             //     "data": "mandays"
    //             // },
    //             {
    //                 "data": "team_member_count"
    //             },
    //             {
    //                 "data": "rcno"
    //             },
    //             {
    //                 "data": "null",
    //                 "render": function(data, type, row) {
    //                     // Convert DOB to dd-mm-yyyy format
    //                     let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
    //                             'en-GB') :
    //                         "N/A";
    //                     let todate = row.todate ? new Date(row.todate).toLocaleDateString(
    //                             'en-GB') :
    //                         "N/A";

    //                     return ` ${fromdate} - ${todate}`;
    //                 }
    //             },
    //             {
    //                 "data": "encrypted_auditscheduleid", // Use the encrypted deptuserid
    //                 "render": function(data, type, row) {
    //                     if (row.statusflag === 'Y') {
    //                         // Check if statusflag is 'N'
    //                         return `<center>
    //                 <a class="btn editicon edit_btn" id="${data}">
    //                     <i class="ti ti-edit fs-4"></i>
    //                 </a>
    //             </center>`;
    //                     } else {



    //                         // Otherwise, show the Finalize button
    //                         return `<center>
    //                 <button class="btn btn-primary finalize_btn" id="${data}">
    //                     Finalized
    //                 </button>
    //             </center>`;


    //                     }
    //                 }
    //             }
    //         ]
    //     });
    // }




    function initializeDataTable(language) {
        $.ajax({
            url: "/audit/fetchAllScheduleData",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(json) {
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();

                    dataFromServer = json.data;
                    console.log(dataFromServer);
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            }
        });

    }

    // function renderTable(language)
    // {
    //     const InstituteName = language === 'ta' ? 'insttname' : 'instename';

    //     if ($.fn.dataTable.isDataTable('#scheduletable')) {
    //             $('#scheduletable').DataTable().clear().destroy();
    //     }

    //         var table = $('#scheduletable').DataTable
    //         ({
    //             "processing": true,
    //             "serverSide": false,
    //             "lengthChange": false,
    //             "scrollX": true,
    //             "autoWidth": false,
    //             "responsive": true,
    //             "destroy": true, // Destroy and reinitialize
    //             "data": dataFromServer,
    //             "columns":
    //             [
    //                 {
    //                     "data": null, // Serial number column
    //                     "render": function(data, type, row, meta) {
    //                         return meta.row + 1; // Serial number starts from 1
    //                     },
    //                     "width": "5%" // Set default width for serial number column
    //                 },
    //                 {
    //                     "data": InstituteName, // Audit Office
    //                     "width":"70%" // Set default width
    //                 },
    //                 {
    //                     "data": "teamhead", // Team head name
    //                     "width": "15%" // Set default width
    //                 },
    //                 {
    //                     "data": "teammembers", // Team members
    //                     "width": "20%" // Set default width
    //                 },
    //                 {
    //                     "data": "team_count", // Team count
    //                     "width": "10%" // Set default width
    //                 },
    //                 {
    //                     "data": "rcno", // RC number
    //                     "width": "10%" // Set default width
    //                 },
    //                 {
    //                     "data": null, // Date range
    //                     "render": function(data, type, row) {
    //                         let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString('en-GB') : "N/A";
    //                         let todate = row.todate ? new Date(row.todate).toLocaleDateString('en-GB') : "N/A";
    //                         return `${fromdate} - ${todate}`;
    //                     },
    //                     "width": "15%" // Set default width
    //                 },
    //                 {
    //                     "data": "encrypted_auditscheduleid", // Edit/Finalize button
    //                     "render": function(data, type, row) {
    //                         if (row.statusflag === 'Y') {
    //                             return `<center>
    //                                 <a class="btn editicon edit_btn" id="${data}">
    //                                     <i class="ti ti-edit fs-4"></i>
    //                                 </a>
    //                             </center>`;
    //                         } else {
    //                             const style = lang === 'ta' ? 'style="font-size:11px;"' : '';
    //                             return `<center>
    //                                 <button class="btn btn-primary finalize_btn lang" style="font-size:11px;" key="finalized_btn" id="${data}">
    //                                     Finalized
    //                                 </button>
    //                             </center>`;
    //                         }
    //                     },
    //                     "width": "10%" // Set default width
    //                 }
    //             ]

    //         });



    //     // Handle form logic based on the fetched data
    //     table.on('xhr', function() {
    //         var json = table.ajax.json();
    //         console.log(json);
    //         if (json.data && json.data.length > 0) {
    //             alert('hi');
    //             // Check if the form logic condition is met
    //             var formConditionMet = json.data.some(function(item) {

    //                 return <?php echo $currentauditplanid; ?> === item.auditplanid && item.statusflag === 'F';
    //             });


    //             if (formConditionMet) {            $('#buttonset').hide(); // Hide the button
    //                 // Show the button
    //             } else {

    //                 $('#buttonset').show();
    //             }
    //         } else {
    //             $('#buttonset').show(); // Hide the button if no data
    //         }
    //     });

    // }
    var Auditscheduleid = '<?php echo $DraftStatus['auditschid'] ?? ''; ?>';

    function renderTable(language) {
        const InstituteName = language === 'ta' ? 'insttname' : 'instename';
        const TeamHead = language === 'ta' ? 'teamheadtamil' : 'teamhead';
        const Teammembers = language === 'ta' ? 'teammemberstamil' : 'teammembers';


        // Use a single check to destroy any existing DataTable
        const tableElement = $('#scheduletable');
        if ($.fn.dataTable.isDataTable(tableElement)) {
            tableElement.DataTable().destroy(); // Destroy previous instance
        }
        const buttonLabels = {
            finalized: language === 'ta' ? 'உறுதிசெய்யப்பட்டது' : 'Finalized',
            cancelled: language === 'ta' ? 'ரத்து செய்யப்பட்டது' : 'Cancelled',
            suspended: language === 'ta' ? 'நிறுத்தப்பட்டது' : 'Suspended'
        };

        // Initialize DataTable
        const table = tableElement.DataTable({
            processing: false, // Disable unnecessary processing indicator
            serverSide: false, // Avoid server-side overhead for local data
            lengthChange: false,
            // scrollX: true,
            autoWidth: false,
            responsive: true,
            destroy: true,
            initComplete: function(settings, json) {
                $("#scheduletable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            data: dataFromServer, // Directly use data
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button> ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end text-center align-middle',
                    type: "num"
                },
                {
                    data: InstituteName,
                    render: function(data, type, row) {
                        return row[InstituteName] || '-';
                    },
                    className: 'text-wrap text-start text-center align-middle'
                },
                {
                    data: TeamHead,
                    render: function(data, type, row) {
                        return row[TeamHead] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap text-center align-middle'
                },
                {
                    data: Teammembers,
                    render: function(data, type, row) {
                        return row[Teammembers] || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap text-center align-middle'
                },
                {
                    data: "team_count",
                    render: function(data, type, row) {
                        return row.team_count || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap text-center align-middle'
                },
                {
                    data: "rcno",
                    render: function(data, type, row) {
                        return row.rcno || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap text-center align-middle'
                },
                {
                    data: "yearselected",
                    render: function(data, type, row) {
                        return row.yearselected || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap text-center align-middle'
                },
                {
                    data: null,
                    title: "Schedule Date",
                    render: function(_, __, row) {
                        const fromDate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
                            'en-GB') : "N/A";
                        const toDate = row.todate ? new Date(row.todate).toLocaleDateString('en-GB') :
                            "N/A";
                        return `${fromDate} - ${toDate}`;
                    },
                    className: "text-left d-none d-md-table-cell extra-column text-wrap text-center align-middle"
                },

                {
                    data: "auditscheduleid",
                    render: (data, _, row) => {
                        if (row.statusflag === 'Y') {
                            if (String(row.auditscheduleid) === String(Auditscheduleid)) {
                                return `
                                    <center>
                                        <a class="btn editicon edit_btn" id="${data}">
                                            <i class="ti ti-edit fs-4"></i>
                                        </a>
                                    </center>`;
                            } else {
                                return ` <center>
                                            <button class="btn btn-warning  lang" style="font-size:11px;">
                                                Draft Saved
                                            </button>
                                        </center>`;
                            }
                        } else if (row.statusflag === 'R') {
                            return `
                                <center>
                                    <a class="btn editicon edit_btn" id="${data}">
                                        <i class="ti ti-edit fs-4"></i>
                                    </a>
                                </center>`;
                        } else if (row.statusflag === 'C') {
                            return `
                                <center>
                                    <button class="btn btn-danger finalize_btn lang" style="font-size:11px;" key="" id="${data}">
                                        ${buttonLabels.cancelled}
                                    </button>
                                </center>`;
                        } else if (row.statusflag.trim() === 'S') {
                            return `
                                <center>
                                    <button class="btn btn-warning lang" style="font-size:11px;" key="" id="${data}">
                                        ${buttonLabels.suspended}
                                    </button>
                                </center>`;
                        } else {
                            return `
                                <center>
                                    <button class="btn btn-primary lang" style="font-size:11px;" key="" id="${data}">
                                        ${buttonLabels.finalized}
                                    </button>
                                </center>`;
                        }
                    }
                }
            ]
        });
        const mobileColumns = ["teamhead", "teammembers", "team_count", "rcno", "fromdate", "todate"];

        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "scheduletable");

        // Optimize form logic
        if (dataFromServer?.length > 0) {
            const currentAuditPlanId = <?php echo json_encode($currentauditplanid); ?>;

            const formConditionMet = dataFromServer.some(
                item => currentAuditPlanId === item.auditplanid && item.statusflag === 'F'
            );

            // Efficiently toggle button visibility
            $('#buttonset').toggle(!formConditionMet);
        } else {
            $('#buttonset').show();
        }

    }



    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#scheduletable')) {
            $('#scheduletable').DataTable().clear().destroy();
        }
        renderTable(language);
    }
    /*  function fetchAlldata() {
                        if ($.fn.dataTable.isDataTable('#scheduletable')) {
                            $('#scheduletable').DataTable().clear().destroy();
                        }


                      var table = $('#scheduletable').DataTable({
                            "processing": true,
                            "serverSide": false,
                            "lengthChange": false,
                            "scrollX": true,
                            "ajax": {
                                "url": "/audit/fetchAllScheduleData", // Your API route for fetching data
                                "type": "POST",
                                "headers": {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
                                },
                                "dataSrc": function(json) {
                                    if (json.data && json.data.length > 0) {
                                        $('#tableshow').show();
                                        $('#scheduletable_wrapper').show();
                                        $('#no_data').hide(); // Hide custom "No Data" message
                                        return json.data;
                                    } else {
                                        $('#tableshow').hide();
                                        $('#scheduletable_wrapper').hide();
                                        $('#no_data').show(); // Show custom "No Data" message
                                        return [];
                                    }
                                }
                            },
                            "columns": [
                                {
                                    "data": null, // Serial number column
                                    "render": function(data, type, row, meta) {
                                        return meta.row + 1; // Serial number starts from 1
                                    }
                                },
                                {
                                    "data": "instename"
                                },
                                {
                                    "data": "teamhead"
                                },
                                {
                                    "data": "teammembers"
                                },
                                {
                                    "data": "team_count"
                                },
                                {
                                    "data": "rcno"
                                },
                                {
                                    "data": "null",
                                    "render": function(data, type, row) {
                                        let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString('en-GB') : "N/A";
                                        let todate = row.todate ? new Date(row.todate).toLocaleDateString('en-GB') : "N/A";
                                        return `${fromdate} - ${todate}`;
                                    }
                                },
                                {
                                    "data": "encrypted_auditscheduleid",
                                    "render": function(data, type, row) {
                                        if (row.statusflag === 'Y') {
                                            return `<center>
    <a class="btn editicon edit_btn" id="${data}">
        <i class="ti ti-edit fs-4"></i>
    </a>
</center>`;
                                        } else {
                                            return `<center>
    <button class="btn btn-primary finalize_btn" id="${data}">
        Finalized
    </button>
</center>`;
                                        }
                                    }
                                }
                            ],

                        });

                       // Handle form logic based on the fetched data
                        table.on('xhr', function()
                        {
                            var json = table.ajax.json();
                            if (json.data && json.data.length > 0) {
                                // Check if the form logic condition is met
                                var formConditionMet = json.data.some(function(item) {
                                    // alert(<?php echo $currentauditplanid; ?>); // Current audit plan ID from PHP
                                    // alert(item.auditplanid); // Audit plan ID from fetched data
                                    return <?php echo $currentauditplanid; ?> === item.auditplanid && item.statusflag === 'F';
                                });

                                if (formConditionMet) {            $('#buttonset').hide(); // Hide the button
                                    // Show the button
                                } else {

                                    $('#buttonset').show();
                                }
                            } else {
                                $('#buttonset').show(); // Hide the button if no data
                            }
                        });

                    }*/
    var statusreschdule = '<?php echo $reschedulests; ?>';

    if (statusreschdule == 'R') {
        var id = '<?php echo $encid; ?>';
        fetchscehdule_data(id, statusreschdule)

    }

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here
        var id = $(this).attr('id'); //Getting id of user clicked edit button.

        if (id) {
            reset_form();
            fetchscehdule_data(id)

        }
    });



    function fetchscehdule_data(auditscheduleid, statusreschdule = '') {

        $.ajax({
            url: '/audit/fetchschedule_data', // Your API route to get user details
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                if (!response.success || !Array.isArray(response.data) || !response.data.length) {
                    // alert('Schedule Details not found or invalid.');
                    return;
                }

                const inst = response.data[0];

                if (!inst) return;

                $('#display_error').hide();
                change_button_as_update('audit_schedule', 'action', 'buttonaction', 'display_error', '', '',
                    'update');

                /*if (statusReschedule === 'R') {
                    $('#from_date').val(inst.fromdate);
                    $('#to_date').val(inst.todate);
                    $('#as_code').val(inst.encrypted_auditscheduleid);

                    datepicker('from_date', convertDateFormatYmd_ddmmyy(inst.fromdate));
                    datepicker('to_date', convertDateFormatYmd_ddmmyy(inst.todate));
                    return;
                }*/

                translate();
                // Fill values
                // datepicker('from_date', convertDateFormatYmd_ddmmyy(inst.fromdate));
                // datepicker('to_date', convertDateFormatYmd_ddmmyy(inst.todate));
                if (inst.fromdate) {
                    datepicker('from_date', convertDateFormatYmd_ddmmyy(inst.fromdate));

                }

                if (inst.todate) {
                    datepicker('to_date', convertDateFormatYmd_ddmmyy(inst.todate));

                }

                $('#rc_no, #hidden_rcno, #instcode').val(inst.rcno);
                $('#as_code').val(inst.encrypted_auditscheduleid);
                $('#total_mandays').val(inst.mandays);
                $('#total_teamsize').val(inst.total_team_count);
                $('#inst_code').val(inst.instid);

                // Multi-selects
                if (inst.yearselected) {
                    const years = inst.yearselected.split(',');
                    $('#yearselected').val(years).trigger('change');
                }

                if ($('#annadhanam_yearselected').is(':visible') && inst.annadhanam_yearselected) {
                    const annaYears = inst.annadhanam_yearselected.split(',');
                    $('#annadhanam_yearselected').val(annaYears).trigger('change');
                }

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    // function fetch_audit_memberdata(selectedTeamMembers = [],lang='') {
    //     var planid = $('#ap_code').val();
    //     $.ajax({
    //         url: '/audit/audit_members', // Replace with your endpoint
    //         method: 'POST',
    //         data: {
    //             planid: planid
    //         },
    //         success: function(response) {

    //             // Map the data to the desired format and join with a comma
    //             const formattedString = response
    //                                     .map(member => {
    //                                         if (lang === 'en') {
    //                                             return `${member.username} - ${member.desigelname}`; // Use English designation
    //                                         } else {
    //                                             return `${member.username} - ${member.desigtlname}`; // Use Tamil or other language designation
    //                                         }
    //                                     })
    //                                     .join(" , ");
    //             $('.show_teammembers').html(formattedString);

    //             // Create an array for deptuserids as strings
    //             const deptUserIds = $.map(response, function(item) {
    //                 return String(item.deptuserid); // Convert 'deptuserid' to string
    //             });

    //             // Clear previous hidden inputs (if needed)
    //             $('form').find('input[name="tm_uid[]"]')
    //         .remove(); // Clear specific inputs with name 'tm_uid[]'

    //             // Append each deptuserid as a separate hidden input
    //             $.each(deptUserIds, function(index, deptuserid) {
    //                 $('<input>').attr({
    //                     type: 'hidden',
    //                     name: 'tm_uid[]', // This will create an array in PHP
    //                     value: deptuserid
    //                 }).appendTo('form');
    //             });





    //             /* const $select = $("#tm_uid");
    //                 $select.empty(); // Clear the existing options

    //                 // If selectedTeamMembers is not empty, pre-select the matching values
    //                if (selectedTeamMembers.length > 0) {
    //                     // Iterate over the response data to append options dynamically
    //                     response.forEach(member => {
    //                         // Check if the member is in the selected list
    //                         const isSelected = selectedTeamMembers.includes(member
    //                             .userid);

    //                         // Create a new option element
    //                         let newOption = new Option(
    //                             `${member.username} - ${member.desigelname}`, // Display text
    //                             member.userid, // Option value
    //                             isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
    //                             isSelected // Mark as selected for Select2
    //                         );

    //                         // Append the new option to the dropdown
    //                         $select.append(newOption);
    //                     });

    //                     // Re-initialize Select2
    //                     $select.select2({
    //                         placeholder: "Select Team Member",
    //                         allowClear: true
    //                     });

    //                     // Set the selected values dynamically and trigger change for Select2
    //                     $select.val(selectedTeamMembers).trigger('change');
    //                 } else {
    //                     // If selectedTeamMembers is empty, just list all the options without any pre-selected
    //                     response.forEach(member => {
    //                         let newOption = new Option(
    //                             `${member.username} - ${member.desigelname}`, // Display text
    //                             member.userid, // Option value
    //                             true, // Not selected
    //                             true // Not selected for Select2
    //                         );
    //                         // Append the new option to the dropdown
    //                         $select.append(newOption);
    //                     });

    //                     // Re-initialize Select2 for listing all options without pre-selection
    //                     $select.select2({
    //                         placeholder: "Select Team Member",
    //                         allowClear: true
    //                     });
    //                }*/
    //         },
    //         error: function() {
    //             //alert("Failed to fetch team members!");
    //         }
    //     });
    // }
    function fetch_audit_memberdata(selectedTeamMembers = [], lang = '') {
        var planid = $('#ap_code').val();
        $.ajax({
            url: '/audit/audit_members', // Replace with your endpoint
            method: 'POST',

            data: {
                planid: planid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                // Map the data to the desired format and join with a comma
                const formattedString = response
                    .map(member =>
                        `${lang === 'ta' ? member.usertamilname +' - '+member.desigtlname : member.username +' - '+ member.desigelname}`
                    )
                    .join(" , ");

                $('.show_teammembers').html(formattedString);

                // Create an array for deptuserids as strings
                const deptUserIds = $.map(response, function(item) {
                    return String(item.deptuserid); // Convert 'deptuserid' to string
                });

                // Clear previous hidden inputs (if needed)
                $('form').find('input[name="tm_uid[]"]')
                    .remove(); // Clear specific inputs with name 'tm_uid[]'

                // Append each deptuserid as a separate hidden input
                $.each(deptUserIds, function(index, deptuserid) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'tm_uid[]', // This will create an array in PHP
                        value: deptuserid
                    }).appendTo('form');
                });





                /* const $select = $("#tm_uid");
                    $select.empty(); // Clear the existing options

                    // If selectedTeamMembers is not empty, pre-select the matching values
                   if (selectedTeamMembers.length > 0) {
                        // Iterate over the response data to append options dynamically
                        response.forEach(member => {
                            // Check if the member is in the selected list
                            const isSelected = selectedTeamMembers.includes(member
                                .userid);

                            // Create a new option element
                            let newOption = new Option(
                                `${member.username} - ${member.desigelname}`, // Display text
                                member.userid, // Option value
                                isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
                                isSelected // Mark as selected for Select2
                            );

                            // Append the new option to the dropdown
                            $select.append(newOption);
                        });

                        // Re-initialize Select2
                        $select.select2({
                            placeholder: "Select Team Member",
                            allowClear: true
                        });

                        // Set the selected values dynamically and trigger change for Select2
                        $select.val(selectedTeamMembers).trigger('change');
                    } else {
                        // If selectedTeamMembers is empty, just list all the options without any pre-selected
                        response.forEach(member => {
                            let newOption = new Option(
                                `${member.username} - ${member.desigelname}`, // Display text
                                member.userid, // Option value
                                true, // Not selected
                                true // Not selected for Select2
                            );
                            // Append the new option to the dropdown
                            $select.append(newOption);
                        });

                        // Re-initialize Select2 for listing all options without pre-selection
                        $select.select2({
                            placeholder: "Select Team Member",
                            allowClear: true
                        });
                   }*/
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
    }

    // When editing, you can call the fetch_audit_memberdata function and pass the selected values
    $("#edit_button").on("click", function() {
        const selectedTeamMembers = [ /* Array of already selected team member IDs */ ];

        // Fetch and populate the dropdown, passing the selected values to pre-select them
        fetch_audit_memberdata(selectedTeamMembers, lang);
    });

    // If no members are selected (empty array), call it like this
    $("#edit_button_no_selection").on("click", function() {
        fetch_audit_memberdata([], lang); // Pass an empty array for no selection
    });

    // function fetch_instituteData() {
    //     $.ajax({
    //         url: 'audit/creatauditschedule_dropdownvalues', // The route to call your controller method
    //         method: 'POST',
    //         data: {
    //             auditplanid: '' // Passing the auditplanid from the button's id
    //         },
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
    //                 'content') // CSRF token for security
    //         },
    //         success: function(response) {
    //             alert(resaponse);
    //             if (response.success) {
    //                 // Handle the success case (you can redirect, update UI, etc.)
    //                 alert("Data loaded successfully.");
    //                 // You can use response data to update your UI dynamically
    //             } else {
    //                 alert("Failed to load data.");
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             // Handle error
    //             console.log("AJAX error: " + error);
    //         }
    //     });
    // }
</script>
@endsection
