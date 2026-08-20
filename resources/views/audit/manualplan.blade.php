@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', 'Manual Plan')

<style>
    .list-group-item:nth-child(odd) {
        background-color: white;
    }

    .list-group-item:nth-child(even) {
        background-color: #ebf3fe;
        /* Light grey */
    }

    .list-group-item:hover {
        background-color: #809fff;
        /* Light grey */
    }

    .list-group-item:hover {
        cursor: pointer;
        /* Light grey */
    }

    .card-body {
        padding: 10px 10px;
    }

    .card {
        margin-bottom: 2px;
    }

    #auditteamtable_wrapper {
        overflow: visible;
        /* Ensure the wrapper does not force scrolling */
    }

    #auditteamtable {
        width: 100%;
        /* Ensure the table takes full width of its container */
        table-layout: auto;
        /* Allow automatic adjustment of table layout */
        overflow: visible;
        /* Prevent overflow */
    }

    .dataTables_scrollBody thead tr {
        visibility: collapse;
    }

    .largemodal td {
        padding: 12px;
        /* Adds 10px of padding on all sides of each cell */
        border: 1px solid #ddd;
        /* Optional: Add a border for visibility */
    }

    .dettable {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    .dettable th,
    .dettable td {
        border: 1px solid #ccc;
        padding: 12px 16px;
        text-align: left;
    }

    .dettable th {
        background-color: #f4f4f4;
        color: #333;
    }

    .dettable td {
        background-color: #fafafa;
    }

    .plan-date {
        margin-top: 15px;
        font-weight: bold;
        font-size: 12px;
        color: #110a5a;
    }

    .spillover-select-option {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: normal;
    }

    .spillover-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border: 1px solid #fdba74;
        border-radius: 6px;
        background: #fff7ed;
        color: #b45309;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: 0;
    }
</style>

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<?php
$sessioncharge = session('charge');
$deptcode = $sessioncharge->deptcode;
$regioncode = $sessioncharge->regioncode;
$distcode = $sessioncharge->distcode;

$make_dept_disable = $deptcode ? 'disabled' : '';

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_dist_disable = $distcode ? 'disabled' : '';

//Fetch locally stored lang
if (isset($_COOKIE['language'])) {
    $lang_val = $_COOKIE['language'];
    if ($lang_val == '' || $lang_val == null) {
        $lang_val = 'en';
    }
} else {
    $lang_val = 'en';
}

?>
<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditteam_label">Audit Team</div>

            <div class="card-body">

                <form id="auditteam" name="auditteam">

                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" name="auditteamid" id="auditteamid" value="" />
	                    <input type="hidden" name="instdesigcount" id="instdesigcount" value="" />
	                    <input type="hidden" name="headlevel" id="headlevel" value="" />
	                    <input type="hidden" name="auditplanid" id="auditplanid" value="0" />
	                    <input type="hidden" id="edit_is_spillover" value="N" />
	                    <input type="hidden" id="edit_remainingmandays" value="" />



                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="department"
                                for="validationDefault01">Department
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="deptcode" name="deptcode"
                                onchange="onchange_deptcode()" <?php echo $make_dept_disable; ?>>
                                <option value="">Select Department</option>
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                        @if (old('dept', $deptcode) == $department->deptcode) selected @endif
                                        data-headlevel ="{{ $department->headdesiglevel }}"
                                        data-name-en="{{ $department->deptelname }}"
                                        data-name-ta="{{ $department->depttlname }}">
                                        {{ $department->deptelname }}
                                        <!-- Display any field you need -->
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="region" for="validationDefault01">Region
                            </label>
                            <input type="hidden" value="1" />
                            <select class="select2 form-select mr-sm-2 lang-dropdown" name="regioncode" id="regioncode"
                                onchange="onchange_regioncode()" <?php echo $make_region_disable; ?>>
                                <option value="">Select Region</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3" id="district">
                            <label class="form-label required lang " key="district" for="validationDefault01">District
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="distcode" name="distcode"
                                onchange="onchange_districtcode()" <?php echo $make_dist_disable; ?>>
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-4 mb-3">


                            <label class="form-label required lang" key="name_audit_team" for="validationDefault02">Name
                                of the
                                Audit
                                Team</label>
                            <input type="text" class="form-control" id="team_name" name="team_name"
                                placeholder="Team name" />
                        </div> -->
                        <div class="col-md-4 mb-3" id="institution">
                            <label class="form-label required lang " key="institution"
                                for="validationDefault01">Institution
                            </label>
                            <select class="form-select select2 mr-sm-2 lang-dropdown" id="instid" name="instid"
                                onchange="getinst_det(this)">
                                <!-- onchange="get_auditor_details('', '', '')"  -->
                                <option value="">Select Institution</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <label class="form-label lang" key="mandays">Mandays </label>
                            <input class="form-control" id="mandays" type="text" placeholder="" readonly disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label lang" key="teamsize">Team Size</label>
                            <input class="form-control" id="teamsize" type="text" placeholder="" readonly
                                disabled>
                        </div>
                    </div>
                    <!-- <div class="row justify-content-center mt-3">
                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="from_date">From date</label>
                            <div class="input-group" onclick="datepicker('from_date','')">
                                <input type="text" class="form-control datepicker" readonly id="from_date"
                                    name="from_date" placeholder="dd/mm/yyyy" />
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>

                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="form-label lang required" key="to_date">To date</label>
                            <div class="input-group" onclick="datepicker('to_date','')">
                                <input type="text" class="form-control datepicker" readonly id="to_date"
                                    name="to_date" placeholder="dd/mm/yyyy" />
                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>

                        </div>
                    </div> -->
                    <div class="row justify-content-center">
                        <div class="col-md-8 mt-3">
                            <label class="form-label required lang" key="" for="status">Choose
                                Auditor</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="idle" name="user_det"
                                        value="I" checked required disabled />
                                    <label class="form-check-label lang" key="" for="statusNo">Show Idle
                                        Auditors</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input " id="reserve" name="user_det"
                                        value="Y" required onchange="auditorsDet()" />
                                    <label class="form-check-label lang" key="" for="statusYes">Show Auditors
                                        from reserve-list</label>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center mt-3">
                        <div class="col-md-4 ">
                            <label class="form-label lang" key="">Auditors Details </label>
                            <input class="form-control" id="myInput" type="text" placeholder="Search..">

                            <div class="comment-widgets scrollable mb-2 common-widget"
                                style="height: 160px; border: 1px solid #ccc; " data-simplebar="">
                                <ul class="list-group lang dropdown" id="auditornames" style="min-height: 100px;">

                                </ul>
                            </div>

                        </div>

                        <div class="col-md-4 ">
                            <label class="form-label required lang" key="">New Team Head</label>
                            <ul id="team-head" class="list-group"
                                style="min-height: 50px; border: 1px solid #ccc; padding: 5px;">
                                <!-- Placeholder for the Team Head -->
                            </ul>
                            <div id="member_error" class="text-danger mt-2" style="display:none;">
                                Team Head is required.
                            </div>
                            <label class="form-label mt-3 required lang" key="">New Team Members</label>
                            <div class="comment-widgets scrollable mb-2 common-widget"
                                style="height: 100px; border: 1px solid #ccc; " data-simplebar="">
                                <ul id="team-members" class="list-group" style="min-height: 100px;">
                                </ul>


                            </div>



                            <div id="members_error" class="text-danger mt-2" style="display:none;">
                                At least one Team Member is required.
                            </div>

                        </div>

                    </div>
                    <!-- <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="teamhead">Remarks</label>
                            <textarea class="form-control alpha_numeric" maxlength="200" id="remarks" name="remarks" rows="4"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">

                        </div>
                    </div> -->
                    <div class="row justify-content-center">

                        <div class="col-md-4 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" key="savedraft_btn" type="submit"
                                action="insert" id="buttonaction" name="buttonaction">Save Draft </button>
                            <button class="btn button_finalise mt-3 lang" key="final_btn" type="submit"
                                id="finalisebtn">
                            </button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                id="reset_button">Clear</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="col-12">

        <div class="card card-border">
            <div class="card-header card_header_color lang" key="audit_team_det">Audit Team Details</div>

            <div class="card-body">

                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditteamtable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="lang" key="department">Department</th>
                                    <th class="lang" key="region">Region</th>
                                    <th class="lang" key="district">District</th>
                                    <th class="lang" key="">Institution</th>
                                    <th class="lang" key="">Proposed date</th>


                                    <th class="lang" key="">New Team Head</th>
                                    <th class="lang" key="">New Team Memebrs</th>
                                    <th class="all lang" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamically populated rows will go here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="no_data" class="hide_this">
                        <center>No Data Available</center>
                    </div>
                </div>

            </div>
            <script>
                function handleColorTheme(e) {
                    document.documentElement.setAttribute("data-color-theme", e);
                }
                // script src = "https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js" >
            </script>
            {{-- </script> --}}

        </div>
        {{-- <div class="dark-transparent sidebartoggler"></div> --}}

    </div>
</div>
</div>
<input type="hidden" id="teamheadids" name="teamheadids">
<input type="hidden" id="teammemberids" name="teammemberids">
</body>

<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/plugins/toastr-init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script> -->
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/datatable/datatable-advanced.init.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
    //---------------------------------------date Picker -------------------------------------------------//
    function datepicker(value, setdate) {

        var maxDate = new Date();
        var minDate = new Date();

        var form = 'cleardateform';

        const fromVal = $('#from_date').val();
        const toVal = $('#to_date').val();

        // If setting 'to_date' and 'from_date' has value
        if (value === 'to_date' && fromVal) {

            minDate = new Date(fromVal);
        }

        // If setting 'from_date' and 'to_date' has value
        if (value === 'from_date' && toVal) {

            maxDate = new Date(toVal);
        }

        var fromvalclr = `from_date`;
        var tovalclr = `to_date`;


        init_datepicker(value, minDate, maxDate, setdate, form, fromvalclr, tovalclr);

    }

    function formatInstitutionOption(option) {
        var rawText = option.text || '';
        var cleanText = rawText.replace(/\s*-?\s*\(Spillover\)\s*$/, '');
        var $optionText = $('<span></span>').text(cleanText);

        if (!option.id) {
            return $optionText;
        }

        var $option = $(option.element);
        var isSpillover = rawText.indexOf('(Spillover)') !== -1 ||
            option.spillover === 'Y' ||
            $option.attr('data-spillover') === 'Y' ||
            $option.data('spillover') === 'Y';
        var currentLang = (typeof getLanguage === 'function') ? getLanguage() : 'en';
        var optionName = currentLang === 'ta' ? $option.data('name-ta') : $option.data('name-en');

        $optionText.text(optionName || cleanText);

        if (!isSpillover) {
            return $optionText;
        }

        return $('<span class="spillover-select-option"></span>')
            .append($optionText)
            .append($('<span class="spillover-badge"></span>').text('Spillover'));
    }

    function makeSpilloverBadgeContent(text) {
        var cleanText = (text || '').replace(/\s*-?\s*\(Spillover\)\s*$/, '');

        return $('<span class="spillover-select-option"></span>')
            .append($('<span></span>').text(cleanText))
            .append($('<span class="spillover-badge"></span>').text('Spillover'));
    }

    function findInstitutionOptionByText(text) {
        var cleanText = (text || '').replace(/\s*-?\s*\(Spillover\)\s*$/, '').trim();

        return $('#instid option').filter(function() {
            var optionText = ($(this).text() || '').replace(/\s*-?\s*\(Spillover\)\s*$/, '').trim();
            var nameEn = String($(this).attr('data-name-en') || '').trim();
            var nameTa = String($(this).attr('data-name-ta') || '').trim();

            return optionText === cleanText || nameEn === cleanText || nameTa === cleanText;
        }).first();
    }

    function renderSpilloverInstitutionName(name, isSpillover) {
        var safeName = $('<span></span>').text(name || '-').html();

        if (!isSpillover) {
            return safeName;
        }

        return `<span class="spillover-select-option"><span>${safeName}</span><span class="spillover-badge">Spillover</span></span>`;
    }

    function decorateSpilloverDropdown() {
        $('.select2-results__option').each(function() {
            var optionText = $(this).text();
            var $sourceOption = findInstitutionOptionByText(optionText);
            var isSpillover = optionText.indexOf('(Spillover)') !== -1 ||
                $sourceOption.attr('data-spillover') === 'Y';

            if (isSpillover) {
                $(this).empty().append(makeSpilloverBadgeContent($sourceOption.text() || optionText));
            }
        });
    }

    function decorateSpilloverSelection() {
        var selectedText = $('#instid option:selected').text();
        var selectedSpillover = $('#instid option:selected').attr('data-spillover') === 'Y';
        var selectionText = $('#instid').next('.select2').find('.select2-selection__rendered');

        if (selectedSpillover || selectedText.indexOf('(Spillover)') !== -1) {
            selectionText.empty().append(makeSpilloverBadgeContent(selectedText));
        }
    }

    function initInstitutionSelect() {
        var institutionDropdown = $('#instid');

        if (!$.fn.select2 || !institutionDropdown.length) {
            return;
        }

        if (institutionDropdown.hasClass('select2-hidden-accessible')) {
            institutionDropdown.select2('destroy');
        }

        institutionDropdown.select2({
            width: '100%',
            templateResult: formatInstitutionOption,
            templateSelection: formatInstitutionOption,
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        institutionDropdown
            .off('select2:open.spillover select2:select.spillover change.spillover')
            .on('select2:open.spillover', function() {
                setTimeout(decorateSpilloverDropdown, 0);
            })
            .on('select2:select.spillover change.spillover', function() {
                setTimeout(decorateSpilloverSelection, 0);
            });

        setTimeout(decorateSpilloverSelection, 0);
    }

    function getInstitutionPlaceholder() {
        return getLanguage() === 'ta' ? 'selct Institution' : 'Select Institution';
    }

    function clearInstitutionSelection() {
        var institutionDropdown = $('#instid');

        if ($.fn.select2 && institutionDropdown.hasClass('select2-hidden-accessible')) {
            institutionDropdown.select2('destroy');
        }

        institutionDropdown.empty().append($('<option></option>').val('').text(getInstitutionPlaceholder()));
        institutionDropdown.val('');
        $('#mandays,#teamsize').val('');
        $('#edit_is_spillover').val('N');
        $('#edit_remainingmandays').val('');
        initInstitutionSelect();
    }

    function getscheduledauditordels(distcode, auditteamid, deptcode, instid) {
        if (!deptcode) deptcode = $('#deptcode').val();
        if (!distcode) distcode = $('#distcode').val();
        if (!auditteamid) auditteamid = $('#instid').val();
        if (!instid) instid = $('#instid option:selected').data('instid');

        $.ajax({
            url: '/audit/fetchteam',
            type: 'POST',
            dataType: 'json',
            data: {
                distcode: distcode,
                auditteamid: auditteamid,
                deptcode: deptcode,
                instid: instid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                const teamsList = $('#teams');
                teamsList.empty();

                $('#instdesigcount').val(data.membercount);
                const usernameList = $('#username');
                usernameList.empty();

                $('#teamhead').val('');
                $('#teammembers').val('');
                $('#teamheadids').val('');
                $('#teammemberids').val('');


                // let headNames = [];
                // const teamHead = team.teamhead ? team.teamhead.trim() : '';
                // headItem.html(`<strong></strong> ${teamHead}`);

                if (data.success && data.auditor && data.auditor.length > 0) {

                    console.log(data.auditor);
                    let headNames = [],
                        headIds = [],
                        memberNames = [],
                        memberIds = [];

                    data.auditor.forEach(function(team) {
                        const teamHead = team.teamhead ? team.teamhead.trim() : '';
                        const teamMembers = team.members ? team.members.trim() : '';
                        const deptuserid = team.deptuserid;

                        if (teamHead !== '') {
                            let headItem = $('<li class="list-group-item"></li>');
                            headItem.html(`<strong></strong> ${teamHead}`);
                            headItem.attr("id", `head-${deptuserid}`);
                            headItem.attr("draggable", "true");
                            headItem.attr("ondragstart", "drag(event)");
                            headItem.attr("data-deptuserid", deptuserid);
                            teamsList.append(headItem);

                            headNames.push(teamHead);
                            headIds.push(deptuserid);
                        }

                        if (teamMembers !== '') {
                            teamMembers.split(',').forEach(function(member, index) {
                                const trimmedMember = member.trim();
                                if (trimmedMember !== '') {
                                    let memberItem = $('<li class="list-group-item"></li>');
                                    memberItem.html(`<strong></strong> ${trimmedMember}`);
                                    memberItem.attr("id", `member-${deptuserid}-${index}`);
                                    memberItem.attr("data-deptuserid", deptuserid);
                                    memberItem.attr("draggable", "true");
                                    memberItem.attr("ondragstart", "drag(event)");
                                    teamsList.append(memberItem);

                                    memberNames.push(trimmedMember);
                                    memberIds.push(deptuserid
                                        .toString()); // <-- store as string
                                }
                            });
                        }


                        let teamItem = $('<li class="list-group-item"></li>');
                        let membersListHTML = '';
                        if (teamMembers !== '') {
                            membersListHTML = `
                            <div><strong>Team Members:</strong></div>
                            <ul>
                                ${teamMembers.split(',').map(m => `<li>${m.trim()}</li>`).join('')}
                            </ul>`;
                        }

                        teamItem.html(`
                        ${teamHead !== '' ? `<div><strong>Team Head:</strong> ${teamHead}</div>` : ''}
                        ${membersListHTML}`);
                        // usernameList.append(teamItem);
                    });

                    // Populate hidden fields with comma-separated head and member IDs
                    // Populate hidden fields with team head names and IDs
                    $('#teamhead').val(headNames.join('\n'));
                    $('#teamheadids').val(headIds.join(','));

                    // Populate team member names and IDs properly
                    $('#teammembers').val(memberNames.join('\n')); // <- names for display

                    // Send member IDs with deptuserid for each member
                    $('#teammemberids').val(JSON.stringify(memberIds)); // Pass deptuserid of members
                } else {
                    teamsList.append(
                        '<li class="list-group-item text-center">No auditor details available</li>');
                    usernameList.append(
                        '<li class="list-group-item text-center">No auditor details available</li>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching auditors:", error);
            }
        });
    }


    //Language Change
    var lang = window.localStorage.getItem('lang');
    change_lang_for_page(lang);

    $("#translate").change(function() {
        lang = $(this).val();
        change_lang_for_page(lang);

    });

    function change_lang_for_page(lang) {
        // Get all dropdowns with the class 'lang-dropdown'
        const dropdowns = document.querySelectorAll('.lang-dropdown');

        // Loop through each dropdown
        dropdowns.forEach((dropdown) => {
            const options = dropdown.options;

            // Loop through each option in the current dropdown
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const nameEn = option.getAttribute('data-name-en'); // English name
                const nameTa = option.getAttribute('data-name-ta'); // Tamil name

                // Update the visible text based on the selected language
                if (lang === 'en' && nameEn) {
                    option.textContent = nameEn;
                } else if (lang === 'ta' && nameTa) {
                    option.textContent = nameTa;
                }
            }
        });
    }


    // if (lang === 'en') {
    //     option.textContent = nameEn;

    // } else {
    //     option.textContent = nameEn;
    // }

    // }



    // Initialize Sortable for Team Head
    const teamHead = document.getElementById('team-head');
    const teamMembers = document.getElementById('team-members');
    const userName = document.getElementById('auditornames');
    const instdesigcount = $('#instdesigcount').val()

    const teamHeaddiv = document.querySelector('#team-head .list-group-item');
    const teamHeadId = teamHeaddiv ? teamHeaddiv.getAttribute('data-desigcode') : null;

    function createAuditorListItem(auditor) {
        const userId = auditor.deptuserid || auditor.userid;
        const listItem = $('<li></li>');
        listItem.addClass('list-group-item');
        listItem.attr('draggable', 'true');
        listItem.attr('data-userid', userId);
        listItem.attr('data-desigcode', auditor.desigcode);
        listItem.html(`
	            <div class="row">
	                <div class="d-flex flex-row comment-row">
	                    <div class="col-md-12 ms-4">
	                        <h6 class="fw-medium">${auditor.username} - ${auditor.desigelname} </h6>
	                    </div>
	                </div>
	            </div>
	        `);

        return listItem;
    }

    function moveExistingMembersBackToAuditorList() {
        $('#team-members .list-group-item').each(function() {
            $('#auditornames').append($(this));
        });
    }

    function addSchemeTeamMembers(members) {
        const teamsize = parseInt($('#teamsize').val(), 10) || 0;
        const maxMembers = Math.max(teamsize - 1, 0);

        moveExistingMembersBackToAuditorList();

        members.forEach(function(member) {
            if ($('#team-members .list-group-item').length >= maxMembers) {
                return;
            }

            const userId = member.deptuserid || member.userid;
            if (!userId || $('#team-members .list-group-item[data-userid="' + userId + '"]').length) {
                return;
            }

            const existingItem = $('#auditornames .list-group-item[data-userid="' + userId + '"]').first();
            const memberItem = existingItem.length ? existingItem : createAuditorListItem(member);
            $('#team-members').append(memberItem);
        });
    }

    function loadManualSchemeTeamMembers(headItem) {
        const headUserid = headItem.getAttribute('data-userid');
        const deptcode = $('#deptcode').val();
        const instid = $('#instid').val();

        if (!headUserid || !deptcode || !instid) {
            return;
        }

        $.ajax({
            url: '/manualplan/getManualSchemeTeamMembers',
            type: 'POST',
            dataType: 'json',
            data: {
                deptcode: deptcode,
                instid: instid,
                head_userid: headUserid,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(response) {
                if (!response.success || !response.scheme_active || !response.members || response.members
                    .length === 0) {
                    return;
                }

                addSchemeTeamMembers(response.members);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching manual scheme team members:', error);
            }
        });
    }

    function headsort(headlevel) {
        Sortable.create(teamHead, {
            group: 'shared',
            animation: 150,
            onAdd: function(evt) {
                $('#member_error').hide();
                const draggedDesigcode = evt.item.getAttribute('data-desigcode');


                // ✅ Check if this desigcode is allowed as head
                const isValidHead = headlevel.some(item => item.desigcode === draggedDesigcode);



                if (!isValidHead) {
                    toastr.error("Only eligible designations can be made Team Head", "", {
                        progressBar: true,
                    });


                    evt.from.appendChild(evt.item);
                    return;
                }

                if (teamHead.children.length > 1) {
                    toastr.error("Team can have only one Head", " ", {
                        progressBar: true,
                    });

                    evt.from.appendChild(evt.item);
                    return;

                }

                loadManualSchemeTeamMembers(evt.item);

            }
        });
    }


    function membersort(headlevel) {
        Sortable.create(teamMembers, {
            group: 'shared',
            animation: 150,
            onAdd: function(evt) {
                $('#member_error').hide();
                const draggedDesigcode = evt.item.getAttribute('data-desigcode');
                const headDesigcode = headlevel[0]?.desigcode;
                const teamsize = $('#teamsize').val()

                if (draggedDesigcode === headDesigcode) {
                    toastr.error("Cannot be a member", " ", {
                        progressBar: true,
                    });
                    evt.from.appendChild(evt.item); // Revert item back
                    return;
                }
                if (teamMembers.children.length > teamsize - 1) {

                    toastr.error("Team Members reached the maximum count", " ", {
                        progressBar: true,
                    });
                    evt.from.appendChild(evt.item);
                }
            }
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    // Initialize Sortable for Team Members
    // Sortable.create(teamMembers, {
    //     group: 'shared',
    //     animation: 150
    // });
    Sortable.create(userName, {
        group: 'shared',
        animation: 150
    });

    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#auditornames li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });








    /******************************* Based on the Radio button getauditors ************************************/

    // document.getElementById('distcode').addEventListener('change', function() {
    //     let distcode = this.value;
    //     get_auditor_details(distcode, '', '','')
    // });

    var deptcode_session = '<?php echo $deptcode; ?>';
    var regioncode_session = '<?php echo $regioncode; ?>';
    var distcode_session = '<?php echo $distcode; ?>';

    onchange_deptcode(regioncode_session);
    onchange_regioncode(regioncode_session, distcode_session);
    if (distcode_session) {
        get_auditor_details(distcode_session, regioncode_session, deptcode_session, '', '');

    }
    let headlevel = [];

    function onchange_deptcode(regioncode_session = '') {

        var selectedOption = $('#deptcode').find(':selected');
        var headlevel = selectedOption.attr('data-headlevel') || '';
        reset_auditor_datas();
        var deptcode = $('#deptcode').val();
        // alert(deptcode);
        // alert(regioncode_session);
        $(' #regioncode ,#distcode, #team_name').empty();
        $('#teamhead,#teammembers').val('')
        $('#auditornames').empty();
        $('#team-members').empty(); // Removes all child list items (<li>)
        $('#team-head').empty();
        const institutionDropdown = $('#instid');

        // $('#teamsize,#mandays').val('');
        // institutionDropdown.empty();
        $.ajax({
            url: '/audit_team/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                headlevel: headlevel
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var data = response.regiondet;
                const lang = getLanguage();

                // makedropdownempty('regioncode', '---Select Region---');
                // makedropdownempty('distcode', '---Select District---');
                // makedropdownempty('instid', '---Select Institution---');
                //     $('#regioncode').empty();
                $('#regioncode').append('<option value="">Select Region</option>');

                // $.each(data, function(index, region) {
                //     var isSelected = region.regioncode === regioncode_session ? 'selected' : '';
                //     $('#regioncode').append('<option value="' + region.regioncode + '" ' +
                //         isSelected + '>' + region.regionename + '</option>');
                // });
                $.each(data, function(index, region) {
                    var isSelected = region.regioncode === regioncode_session ? 'selected' : '';
                    $('#regioncode').append('<option value="' + region.regioncode + '" ' +
                        isSelected +
                        ' data-name-en="' + region.regionename + '"' +
                        ' data-name-ta="' + region.regiontname + '">' +
                        (lang === "en" ? region.regionename : region.regiontname) +
                        '</option>');
                });
                headlevel = response.headlevelarray;
                $('#headlevel').val(JSON.stringify(headlevel))
                //   alert(JSON.stringify(headlevel))
                headsort(headlevel)
                membersort(headlevel)

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function onchange_regioncode(regioncode_session = '', distcode_session = '') {
        reset_auditor_datas();

        var deptcode = regioncode_session ? (deptcode_session || $('#deptcode').val()) : $('#deptcode').val();
        var regioncode = regioncode_session || $('#regioncode').val();
        $.ajax({
            url: '/audit_team/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                const lang = getLanguage('')
                var data = response;
                $('#distcode').empty();
                $('#distcode').append('<option value="">Select District</option>');

                $.each(data, function(index, district) {
                    var isSelected = district.distcode === distcode_session ? 'selected' : '';
                    $('#distcode').append('<option value="' + district.distcode + '" ' +
                        isSelected +
                        ' data-name-en="' + district.distename + '"' +
                        ' data-name-ta="' + district.disttname + '">' +
                        (lang === "en" ? district.distename : district.disttname) +
                        '</option>');
                });


                // $.each(data, function(index, district) {
                //     var isSelected = district.distcode === distcode_session ? 'selected' : '';
                //     $('#distcode').append('<option value="' + district.distcode + '" ' +
                //         isSelected + '>' + district.distename + '</option>');
                // });

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function onchange_districtcode() {
        get_auditor_details('', '', '', '', '', true);
    }





    // get_auditor_details('A', '');

    function reset_auditor_datas() {
        $('#auditornames').empty();
        $('#team-head').empty();
        $('#team-members').empty();

    }

    function getinst_det(data) {
        var selectedOption = $('#instid').find(':selected');
        var mandays = selectedOption.attr('data-mandays') || '';
        var remainingMandays = selectedOption.attr('data-remainingmandays') || '';
        var isSpillover = selectedOption.attr('data-spillover') === 'Y';
        var teamsize = selectedOption.attr('data-teamsize') || '';
        $('#mandays').val(isSpillover && remainingMandays ? remainingMandays : mandays);
        $('#teamsize').val(teamsize);

    }

    function auditorsDet(distcode, regioncode, deptcode, auditteamid, auditplanid) {
        reset_auditor_datas();
        if (!(deptcode)) deptcode = $('#deptcode').val();
        if (!(regioncode)) regioncode = $('#regioncode').val();
        if (!(distcode)) distcode = $('#distcode').val();
        if (!(auditplanid)) auditplanid = $('#instid').val();
        var auditteamid = auditteamid || $('#auditteamid').val();

        // var user_det = [];
        // $('input[name="user_det"]:checked').each(function() {
        //     user_det.push($(this).val());
        // });
        var user_det = $('#reserve').is(':checked') ? $('#reserve').val() : 'N';


        $.ajax({
            url: '/manualplan/getAuditors_manualplan', // Adjust URL accordingly
            type: 'POST',
            dataType: 'json',
            data: {
                distcode: distcode,
                auditteamid: auditteamid,
                regioncode: regioncode,
                deptcode: deptcode,
                user_det: user_det,
                //auditteamid: auditteamid,
                _token: $('meta[name="csrf-token"]').attr('content'),

            },
            success: function(data) {
                const usernameList = $('#auditornames'); // jQuery selector for the ul element
                usernameList.empty(); // Clear the current list
                if (data.success) {
                    if (data.auditor.length > 0) {
                        // Loop through the data and append list items
                        data.auditor.forEach(function(auditor) {
                            var listItem = $('<li></li>'); // Create the list item
                            listItem.addClass('list-group-item'); // Add class to the list item
                            listItem.attr('draggable', 'true');
                            listItem.attr('data-userid', auditor.deptuserid);
                            listItem.attr('data-desigcode', auditor.desigcode);

                            // Append the HTML content for each auditor
                            listItem.html(`
                                        <div class="row">
                                            <div class="d-flex flex-row comment-row">
                                                <div class="col-md-12 ms-4">
                                                    <h6 class="fw-medium">${auditor.username} - ${auditor.desigelname} </h6>
                                                </div>
                                            </div>
                                        </div>
                                    `);

                            // Append the list item to the username list
                            usernameList.append(listItem);
                        });
                    } else {
                        // If no auditors, show a message
                        var noAuditorMessage = $('<li></li>');
                        noAuditorMessage.addClass('list-group-item text-center');
                        noAuditorMessage.text('No auditor details available');
                        usernameList.append(noAuditorMessage);
                    }
                } else {
                    // If success is false, handle this scenario
                    console.error("Failed to fetch auditors.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching auditors:", error);
            }
        });
    }

    function get_auditor_details(distcode, regioncode, deptcode, auditteamid, auditplanid, clearInstitution = false) {

        reset_auditor_datas();
        if (clearInstitution) {
            clearInstitutionSelection();
            auditplanid = '';
        }
        if (!(deptcode)) deptcode = $('#deptcode').val();
        if (!(regioncode)) regioncode = $('#regioncode').val();
        if (!(distcode)) distcode = $('#distcode').val();
        if (!clearInstitution && !(auditplanid)) auditplanid = $('#instid').val();
        var auditteamid = auditteamid || $('#auditteamid').val();
        get_institution_details(deptcode, regioncode, distcode, clearInstitution ? '' : auditplanid);
        auditorsDet(distcode, regioncode, deptcode, auditteamid, auditplanid)




        // if (!(distcode)) {
        //     distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;
        // }


    }

    function show_div(selectedDiv) {
        if (selectedDiv === "district") {
            $('#team-members').empty(); // Removes all child list items (<li>)
            $('#team-head').empty(); // Removes all child list items (<li>)
        }
    }


    function get_institution_details(deptcode, regioncode, distcode, selectedinstid) {
        const lang = getLanguage();


        if (!deptcode) deptcode = $('#deptcode').val();
        if (!distcode) distcode = $('#distcode').val();
        if (!regioncode) regioncode = $('#regioncode').val();
        if (selectedinstid === undefined || selectedinstid === null) selectedinstid = $('#instid').val();

        const institutionDropdown = $('#instid');

        //   $('#teamsize,#mandays').val('');
        institutionDropdown.empty();
        institutionDropdown.append(
            `<option value="">${lang === 'ta' ? 'selct Institution' : 'Select Institution'}</option>`);

        // if (!deptcode || !regioncode || !distcode) {
        //     if (callback) callback();
        //     return;
        // }

        $.ajax({
            url: '/audit/fetchExcessinstitution', // Your API route to get user details
            method: 'POST',
            data: {
                distcode: distcode,
                regioncode: regioncode,
                deptcode: deptcode,
                selectedinstid: selectedinstid,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                console.log('Institution data fetched successfully:', response);
                var data = response;
                $('#instid').empty();
                $('#instid').append('<option value="">Select Institution</option>');

                $.each(data.auditor, function(index, inst) {
                    var isSelected = inst.instid == selectedinstid;
                    var instename = inst.instename || '';
                    var insttname = inst.insttname || '';
                    var mandays = inst.mandays || '-';
                    var remainingMandays = (inst.remainingmandays === null || inst.remainingmandays === undefined) ?
                        '' : inst.remainingmandays;
                    var teamsize = inst.teamsize || '-';
                    var isSpillover = inst.spillover === 'Y';

                    var optionText = (lang === "en" ? instename : insttname) +
                        (isSpillover ? ' - (Spillover)' : '');

                    var $option = $('<option></option>')
                        .val(inst.instid)
                        .text(optionText)
                        .prop('selected', isSelected)
                        .attr('data-mandays', mandays)
                        .attr('data-remainingmandays', remainingMandays)
                        .attr('data-teamsize', teamsize)
                        .attr('data-spillover', isSpillover ? 'Y' : 'N')
                        .attr('data-name-en', instename)
                        .attr('data-name-ta', insttname);

                    if (isSpillover) {
                        $option.addClass('spillover-option');
                    }

                    $('#instid').append($option);
                });

                initInstitutionSelect();



                // $.each(data, function(index, district) {
                //     var isSelected = district.distcode === distcode_session ? 'selected' : '';
                //     $('#distcode').append('<option value="' + district.distcode + '" ' +
                //         isSelected + '>' + district.distename + '</option>');
                // });

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

        // $.ajax({
        //     url: '/audit/fetchinstitution',
        //     type: "POST",
        //     data: {
        //         distcode: distcode,
        //         regioncode: regioncode,
        //         deptcode: deptcode,
        //         _token: $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function(response) {
        //         if (response.success && Array.isArray(response.auditor)) {
        //             response.auditor.forEach(function(item) {
        //                 institutionDropdown.append(
        //                     `<option value="${item.auditteamid}">${item.instename}</option>`
        //                 );
        //             });
        //         } else {
        //             institutionDropdown.append(
        //                 `<option disabled>${lang === 'ta' ? '??????????? ?????' : 'No institutions found'}</option>`
        //             );
        //         }
        //         if (callback) callback();


        //     },
        //     error: function() {
        //         alert('Error fetching institution data.');
        //         if (callback) callback();
        //     }
        // });
    }




    /******************************* Based on the Dropdown getauditors ************************************/


    /*********************************** Reset Form **********************************************/
    function reset_form() {
        $('#display_error').hide();
        change_button_as_insert('auditteam', 'action', 'buttonaction', 'display_error', '', '');
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));



        if (distcode_session) {
            $('#team_name').val('');

        } else if (regioncode_session) {
            $('#distcode, #team_name').val('');

        } else if (deptcode_session) {
            $('#regioncode ,#distcode, #team_name').val('');
        } else {
            $("#auditteam").validate().resetForm(); // Reset the validation errors
            $("#auditteam")[0].reset(); // Optionally reset the form fields as well
            $('#deptcode , #regioncode ,#distcode, #team_name, #instid').val('');
            makedropdownempty('regioncode', '---Select Region---');
            makedropdownempty('distcode', '---Select District---');
            makedropdownempty('instid', '---Select Institution---');

        }
	        $('#auditornames').empty();
	        $('#team-members').empty(); // Removes all child list items (<li>)
	        $('#team-head').empty(); // Removes all child list items (<li>)
	        $('#edit_is_spillover').val('N');
	        $('#edit_remainingmandays').val('');

	        //get_auditor_details('A', '', '');
	    }

    /*********************************** Reset Form **********************************************/



    /***********************************Jquery Form Validation **********************************************/
    $('#file').on('change', function() {
        $(this).valid();
    });
    const $auditteamForm = $("#auditteam");
    $.validator.addMethod("fileSizeLimit", function(value, element) {
        if (element.files.length > 0) {
            return element.files[0].size <= 1 * 1024 * 1024; // 1MB limit
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for file size
        const language = getlanguagelc();
        return 'File size should not exeed 1MB'; // Return localized message for file size limit
    });

    // Custom validation for valid file types (PNG, JPEG, PDF, Excel)
    $.validator.addMethod("validFileType", function(value, element) {
        if (value) {
            let allowedTypes = ["pdf"];
            let fileExtension = value.split(".").pop().toLowerCase();
            return allowedTypes.includes(fileExtension);
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for valid file type
        const language = getlanguagelc();
        return ''; // Return localized message for file type validation
    });
    // Validation rules and messages
    $auditteamForm.validate({
        rules: {
            deptcode: {
                required: true
            },
            regioncode: {
                required: true
            },
            distcode: {
                required: true
            },
            team_name: {
                required: true
            },
            instid: {
                required: true
            },
            // remarks: {
            //     required: true
            // },
            file: {
                validFileType: true,
                fileSizeLimit: true,
            }
        },
        messages: {
            deptcode: {
                required: "Select department name"
            },
            regioncode: {
                required: "Select Region"
            },
            distcode: {
                required: "Select District"
            },
            team_name: {
                required: "Enter Team Name"
            },
            instid: {
                required: "Select Institution"
            },
            remarks: {
                required: "Enter Remarks"
            },
            file: {
                validFileType: "Allowed File Type is PDF",
                fileSizeLimit: "File Size limit is 1MB"
            }

        },
        errorPlacement: function(error, element) {
            // For datepicker fields inside input-group, place error below the input group
            if (element.hasClass('datepicker')) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },
    });




    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $auditteamForm.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    /***********************************Jquery Form Validation **********************************************/


    /*********************************** Insert,update,Finalise,Reset **********************************************/

    // $(document).on('click', '#buttonaction', function()
    // {
    //     event.preventDefault();

    //     if ($("#auditteam").valid())
    //     {
    //         get_insertdata('insert')
    //     } else {
    //         // If the form is not valid, show an alert
    //         // alert("Form is not valid. Please fix the errors.");
    //     }
    // });

    $(document).on('click', '#buttonaction', function(event) {
        event.preventDefault(); // Prevent form submission

        if ($auditteamForm.valid()) {


            $('#member_error').hide();
            $('#members_error').hide();

            // Validate Team Head
            if (teamHead.children.length === 0) {
                $('#member_error').show(); // Show error message if no Team Head is assigned
                return false; // Prevent form submission
            }

            // Validate Team Members
            if (teamMembers.children.length === 0) {
                $('#members_error').show(); // Show error message if no Team Members
                return false; // Prevent form submission
            }
            const teamsize = $('#teamsize').val()


            if (teamMembers.children.length < teamsize - 1) {

                toastr.error("Team Members are insufficient", " ", {
                    progressBar: true,
                });
                evt.from.appendChild(evt.item);
            }


            if ((teamHead.children.length > 0) && (teamMembers.children.length > 0)) {

                var action = $('#action').val();
                let fn_param;
                if (action == 'insert') {
                    fn_param = 'check'
                } else {
                    fn_param = 'check'

                }
                get_insertdata('insert', fn_param)
            }

            // If form is valid, handle form submission (can be an AJAX call or standard submission)
            // For example, submitting the form:
            // $auditteamForm.submit(); // or handle submission via AJAX here
        } else {
            // Optionally, scroll to the first error
            scrollToFirstError();
        }
    });




    $(document).on('click', '#finalisebtn', function() {

        // Prevent form submission (this stops the page from refreshing)
        event.preventDefault();

        //Trigger the form validation
        if ($("#auditteam").valid()) {
            const instdesigcount = $('#instdesigcount').val();

            const teamMembers = document.getElementById('team-members');
            // if (teamMembers.children.length > instdesigcount) {

            //     var message = 'New Audit Members Exceeds the count.This Institution allows only ' +
            //         instdesigcount +
            //         ' members';
            //     passing_alert_value('Confirmation', message, 'confirmation_alert',
            //         'alert_header', 'alert_body', 'confirmation_alert');
            //     return;
            // }
            $('#member_error').hide();
            $('#members_error').hide();

            // Validate Team Head
            if (teamHead.children.length === 0) {
                $('#member_error').show(); // Show error message if no Team Head is assigned
                return false; // Prevent form submission
            }

            // Validate Team Members
            if (teamMembers.children.length === 0) {
                $('#members_error').show(); // Show error message if no Team Members
                return false; // Prevent form submission
            }

            if ((teamHead.children.length > 0) && (teamMembers.children.length > 0)) {
                var selectedValues = [];


                var TeamHead = $('#team-head li');

                // Extract the name (inside <strong>) and role (the rest of the text)
                var TeamHeadname = TeamHead.find('strong').text(); // Get the name inside <strong>
                var TeamHeadrole = TeamHead.text().replace(TeamHeadname, '')
                    .trim(); // Get the role by removing the name part

                // Merge name and role
                var TeamHeadmergedValue = TeamHeadname + ' ' + TeamHeadrole;


                $('#team-members li').each(function() {
                    // Extract the name and role from each <li>
                    var name = $(this).find('strong').text() || $(this).find('h6').text().split(' - ')[
                        0]; // Name can be inside <strong> or <h6>
                    var role = $(this).text().replace(name, '')
                        .trim(); // Extract the role by removing the name part

                    // Display the values in the console or append to a div
                    var mergedValue = name + ' ' + role;

                    // Add the merged value to the array
                    selectedValues.push(mergedValue);
                    console.log('Name: ' + name);
                    console.log('Role: ' + role);
                });


                data =
                    'Are you sure to finalise the Audit Plan?';
                // '<table style="width:100;%" class="table table-bordered view_detail largemodal"><tbody><tr><th><span class="lang" key="invoice_no">Department Name</span></th><td>' +
                // $("#deptcode option:selected").text() + '</td></tr><tr><th>Team Name</th><td>' + $(
                //     '#team_name').val() + '</td></tr><tr><th>Team Head</th><td>' + TeamHeadmergedValue +
                // '</td></tr><tr><th>Team Members</th><td>' + selectedValues;

                content = '';

                // $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                var action = $('#action').val();
                let fn_param;
                fn_param = 'check'
                // if (action == 'insert') {
                //     fn_param = 'check'
                // } else if (action == 'update') {
                //     fn_param = 'check'

                // }
                get_insertdata('finalise', fn_param)
                // });
                // passing_alert_value('Confirmation', data, 'confirmation_alert',
                //     'alert_header', 'alert_body',
                //     'forward_alert');
                // $("#process_button").html("Ok");
                // $("#process_button").addClass("button_finalize");
                // $('#process_button').removeAttr('data-bs-dismiss');

                //
            } else {
                // If the form is not valid, show an alert
                // alert("Form is not valid. Please fix the errors.");
            }

        }
    });

    // $('#large_modal_process_button').on('click', function() {
    //     var confirmation = 'Are you sure to finalize?';
    //     $('#large_confirmation_alert .modal-content').addClass('blurred');



    //     passing_alert_value('Confirmation', text
    //         .save_ques, 'confirmation_alert',
    //         'alert_header', 'alert_body',
    //         'forward_alert');
    //     $('#confirmation_alert').css('z-index', 100000);
    //     $("#process_button").html("Ok");

    // });

    // /**Finalizing Process */
    // $('#process_button').on('click', function() {
    //     $("#large_confirmation_alert").modal("hide");
    //     var action = $('#action').val();
    //     let fn_param;
    //     fn_param = 'check'
    //     // if (action == 'insert') {
    //     //     fn_param = 'check'
    //     // } else if (action == 'update') {
    //     //     fn_param = 'check'

    //     // }
    //     get_insertdata('finalise', fn_param)

    // });



    $('#reset_button').on('click', function() {
        reset_form(); // Call the reset_form function
    });



    function get_insertdata(action, formparam) {

        const instdesigcount = $('#instdesigcount').val()

        const teamMembers = document.getElementById('team-members');

        // if (teamMembers.children.length > instdesigcount) {

        //     var message = 'New Audit Members Exceeds the count.This Institution allows only ' + instdesigcount +
        //         ' members';
        //     passing_alert_value('Confirmation', message, 'confirmation_alert',
        //         'alert_header', 'alert_body', 'confirmation_alert');
        //     return;
        // }
        // distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;




        var distcode = $('#distcode').val();

        var formData = new FormData($('#auditteam')[0]);

        // var formData = $('#auditteam').serializeArray();
        const {
            teamHeadId,
            teamMemberIds,
            teamHeaddesigcode,
            teamMemberdesigcode
        } = collectUserIds();

        var TeamHead = teamHeadId
        var TeamMembers = teamMemberIds
        var teamsize = $('#teamsize').val();
        var oldTeamHead = $('#teamheadids').val();
	        var oldTeamMembers = $('#teammemberids').val();
	        var selectedOption = $('#deptcode').find(':selected');
	        var headdesiglevel = selectedOption.attr('data-headlevel') || '';
	        var headarray = $('#headlevel').val();
	        var selectedInstOption = $('#instid').find(':selected');
	        var isSpilloverInst = selectedInstOption.attr('data-spillover') === 'Y' ||
	            $('#edit_is_spillover').val() === 'Y';
	        var remainingMandays = selectedInstOption.attr('data-remainingmandays') ||
	            $('#edit_remainingmandays').val() ||
	            '';

	        formData.append('teamHeadId', TeamHead);
	        formData.append('newteamhead', teamHeadId);
	        formData.append('headdesiglevel', headdesiglevel);
	        formData.append('newteammembers', JSON.stringify(teamMemberIds));
	        formData.append('teamHeaddesigcode', JSON.stringify(teamHeaddesigcode));
	        formData.append('teamMemberdesigcode', JSON.stringify(teamMemberdesigcode));
	        formData.append('headdesigarray', headarray);
	        formData.append('is_spillover', isSpilloverInst ? 'Y' : 'N');
	        formData.append('remainingmandays', remainingMandays);

        if ($('#teamsize').prop('disabled')) {
            formData.append('teamsize', teamsize);
        }


        if (Array.isArray(teamHeadId)) {
            teamHeadId.forEach(id => {
                formData.append('newteamhead[]', id);
            });
        }

        var btnaction = $('#action').val();
        // Conditionally append finaliseflag based on action
        if (action === 'finalise') {
            formData.append('finaliseflag', 'Y');
            formData.append('formparam', formparam);
        } else if (action === 'insert') {
            formData.append('finaliseflag', 'N');
            formData.append('formparam', formparam);
        } else if (action === 'update') {
            formData.append('finaliseflag', 'N');
            formData.append('formparam', formparam);
        }

        $.ajax({
            url: '/manualplan/manualplan_insertupdate', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            processData: false, // Important!
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // alert(formparam)

                    if (formparam != 'check' && response.data) {
                        let parsedStatusData = null;
                        try {
                            parsedStatusData = JSON.parse(response.data);
                        } catch (e) {
                            parsedStatusData = null;
                        }

                        if (parsedStatusData && parsedStatusData.status == "false") {
                            passing_alert_value('Alert', parsedStatusData.error ||
                                'Unable to process manual plan',
                                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert'
                            );
                            return;
                        }
                    }


                    if (formparam == 'check') {
                        //    alert('entered' + formparam)
                        const parsedData = JSON.parse(response.data);

                        const user = parsedData.users[0];
                        const nameDesignation = `${user.username} (${user.designation})`;
                        const fromDate = parsedData.fromdate != null ? convertDateFormatYmd_ddmmyy(
                            parsedData.fromdate) : '-';
                        const toDate = parsedData.todate != null ? convertDateFormatYmd_ddmmyy(parsedData
                            .todate) : '-';
                        const quarterCode = parsedData.currentquartercode ?
                            ` (${parsedData.currentquartercode})` : '';
                        const currentQuarterFromDate = parsedData.currentquarterfromdate ?
                            String(parsedData.currentquarterfromdate).substring(0, 10) : '';
                        const formatUserLastDate = (lastdate) => {
                            if (!lastdate) {
                                return '-';
                            }
                            const userLastDate = String(lastdate).substring(0, 10);
                            return currentQuarterFromDate && userLastDate < currentQuarterFromDate ?
                                '-' :
                                convertDateFormatYmd_ddmmyy(userLastDate);
                        };

	                        const planDate = (parsedData.fromdate && parsedData.todate) ?
	                            `Proposed plan date: ${fromDate} to ${toDate}${quarterCode}` :
	                            (parsedData.fromdate || parsedData.todate) ?
	                            `Proposed plan date: ${fromDate} to ${toDate}${quarterCode}` :
	                            'Proposed plan date not available';
	                        const spilloverAutoplanNote = parsedData.spillover_autoplandate_capped === 'Y' ?
	                            `<p class="plan-date text-danger">This schedule is limited to autoplan date ${convertDateFormatYmd_ddmmyy(parsedData.spillover_autoplandate)}. Remaining mandays ${parsedData.spillover_carryforward_mandays || 0} will be carry forward to next quarter.</p>` :
	                            '';


                        let content;
                        let rows = '';
                        if (parsedData.fromdate != null || parsedData.todate != null) {
                            parsedData.users.forEach(user => {
                                const nameDesignation = `${user.username} (${user.designation})`;
                                const toDate = formatUserLastDate(user.lastdate);
                                rows += `
                            <tr>
                                <td>${nameDesignation}</td>
                                <td>${toDate}</td>
                            </tr>
                        `;
                            });

                            content = `
                            <table class="dettable">
                                <tr>
                                    <th>Name</th>
                                    <th>To Date</th>
                                </tr>
                                ${rows}
	                            </table>
	                            <p class="plan-date">${planDate}</p>
	                            ${spilloverAutoplanNote}
	                        `;
                        } else {
                            content = ``;
                        }

                        $('#confirmation_alert').modal('show');

                        event.preventDefault();
                        $('#process_button').off('click').on('click', function(event) {
                            event.preventDefault();
                            $('#close_button').hide();
                            $('#confirmation_alert').modal('hide');
                            if (btnaction == 'insert' && action == 'finalise') {
                                formparamvar = 'insertfinalise'
                            } else if (action == 'insert' && btnaction == 'insert') {

                                formparamvar = 'insert'
                            } else if (action == 'finalise' && btnaction == 'update') {
                                formparamvar = 'finalise'
                            } else if (btnaction == 'update' && action == 'insert') {
                                formparamvar = 'update'
                            }

                            get_insertdata(btnaction, formparamvar)
                        });
                        if (parsedData.status == "false") {
                            const errorMsg = parsedData.error || 'Unknown error';
                            content += `<p class="error-msg" style="font-weight: bold;">${errorMsg}</p>`;
                            passing_alert_value('Alert', content,
                                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert'
                            );

                        } else {
                            // reset_form();
                            passing_alert_value('Confirmation', content, 'confirmation_alert',
                                'alert_header', 'alert_body', 'forward_alert');

                        }

                    } else if (formparam == 'insert') {
                        reset_form();
                        const parsedData = JSON.parse(response.data);
                        const quarterCode = parsedData.currentquartercode ?
                            ` - ${parsedData.currentquartercode}` : '';
                        const planDate =
                            `Proposed plan date: ${convertDateFormatYmd_ddmmyy(parsedData.fromdate)} to ${convertDateFormatYmd_ddmmyy(parsedData.todate)}${quarterCode}`;

                        var content = `
                        <p class="">Audit plan was created successfully</p>
                        <p class="plan-date"> ${planDate}</p>`;
                        passing_alert_value('Alert', content,
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    } else if (formparam == 'update') {
                        reset_form();
                        const parsedData = JSON.parse(response.data);
                        //  const planDate = `Proposed plan date: ${convertDateFormatYmd_ddmmyy(parsedData.fromdate)} to ${convertDateFormatYmd_ddmmyy(parsedData.todate)}`;


                        passing_alert_value('Alert', 'Audit plan was updated successfull',
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    } else if (formparam == 'finalise') {
                        reset_form();
                        //  const parsedData = JSON.parse(response.data);


                        passing_alert_value('Alert', 'Audit plan has been created successfully',
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    } else if (formparam == 'insertfinalise') {
                        reset_form();
                        //  const parsedData = JSON.parse(response.data);


                        passing_alert_value('Alert', 'Audit plan has been created successfully',
                            'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                    }


                    let formparamvar;



                    // if (action === 'finalise') {
                    //     var responsefinal = 'Data Finalized Successfully';

                    // } else {
                    //     var responsefinal = response.message;

                    // }

                    // passing_alert_value('Confirmation', responsefinal, 'confirmation_alert',
                    //     'alert_header', 'alert_body', 'confirmation_alert');

                    var lang = getLanguage();
                    initializeDataTable(lang); // Reload the table
                } else if (response.success == "false") {
                    alert('error')
                } else {

                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'Contact Admin';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');

                console.error('Error details:', xhr, status, error);
            }
        });
    }








    function collectUserIds() {
        const teamHead = document.querySelector('#team-head .list-group-item');
        const teamMembers = document.querySelectorAll('#team-members .list-group-item');


        const teamHeadId = teamHead ? teamHead.getAttribute('data-userid') : null;
        const teamHeaddesigcode = teamHead ? teamHead.getAttribute('data-desigcode') : null;


        const teamMemberIds = [];
        teamMembers.forEach(member => {
            teamMemberIds.push(member.getAttribute('data-userid'));
        });

        const teamMemberdesigcode = [];
        teamMembers.forEach(member => {
            teamMemberdesigcode.push(member.getAttribute('data-desigcode'));
        });

        return {
            teamHeadId,
            teamMemberIds,
            teamHeaddesigcode,
            teamMemberdesigcode
        };
    }


    /*********************************** Insert,update,Finalise,Reset **********************************************/


    /********************************************** Fetch Data ********************************************** */

    $(document).ready(function() {
        $('#auditteam')[0].reset();

        var lang = getLanguage();
        initializeDataTable(lang);

        var errorMessage = @json(session('errorMessage') ?? ($errorMessage ?? ''));
        var pageName = @json(session('pageName') ?? ($pageName ?? ''));

        if (errorMessage) {

            pageName == 'init_fieldaudit' ? $('#close_button').hide() : '';
            passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');

            $('#ok_button').off('click').on('click', function(event) {
                event.preventDefault();
                if (pageName == 'manualplan') {
                    window.location.href = '/dashboard';
                }

            });
        }

    });

    function initializeDataTable(language) {
        $.ajax({
            url: "manualplan/fetchUpdatepManualplan",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
                if (json.data && json.data.length > 0) {
                    console.log(json.data);
                    $('#tableshow').show();
                    // $('#auditteamtable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    // $('#auditteamtable_wrapper').hide();
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
        // alert('hi');
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';


        if ($.fn.DataTable.isDataTable('#auditteamtable')) {
            $('#auditteamtable').DataTable().clear().destroy();
        }

        var table = $('#auditteamtable').DataTable({
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
                    data: "deptelname",
                    title: columnLabels?.["deptelname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.deptelname || '-';
                    }
                },
                {
                    data: "regionename",
                    title: columnLabels?.["regionename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.regionename || '-';
                    }
                },
                {
                    data: "distename",
                    title: columnLabels?.["distename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.distename || '-';
                    }
                },
                {
	                    data: "instename",
	                    title: columnLabels?.["instename"]?.[language],
	                    className: "d-none d-md-table-cell lang extra-column text-wrap",
	                    render: function(data, type, row) {
	                        if (type !== 'display') {
	                            return row.instename || '-';
	                        }

	                        return renderSpilloverInstitutionName(row.instename, row.spillover === 'Y');
	                    }
	                },
                {
                    data: "fromdate",
                    title: columnLabels?.["proposeddate"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        const isValidDate = (d) => {
                            const date = new Date(d);
                            return d && !isNaN(date);
                        };

                        const fromDate = isValidDate(row.fromdate) ? new Date(row.fromdate)
                            .toLocaleDateString('en-GB') : "N/A";
                        const toDate = isValidDate(row.todate) ? new Date(row.todate)
                            .toLocaleDateString('en-GB') : "N/A";

                        return `${fromDate} - ${toDate}`;
                    },
                },


                {
                    data: "newteamheadname",
                    title: columnLabels?.["newteamheadname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    // render: function(data, type, row) {
                    //     return row.newteamheadname || '-';
                    // }
                    render: function(data, type, row) {
                        if (!row.newteamheadname) return '-';

                        return row.newteamheadname
                            .split(',')
                            .map(member => {
                                const parts = member.trim().split(' - ');
                                // Return only Name - Designation (skip ID)
                                return parts.slice(0, 2).join(' - ');
                            })
                            .join(', ');
                    }

                },
                {
                    data: "newteammembernames",
                    title: columnLabels?.["newteammembernames"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    // render: function(data, type, row) {
                    //     return row.newteammembernames || '-';
                    // }
                    render: function(data, type, row) {
                        if (!row.newteammembernames) return '-';

                        return row.newteammembernames
                            .split(',')
                            .map(member => {
                                const parts = member.trim().split(' - ');
                                // Return only Name - Designation (skip ID)
                                return parts.slice(0, 2).join(' - ');
                            })
                            .join(', ');
                    }
                },
                // {
                //     data: "statusflag",
                //     title: columnLabels?.["statusflag"]?.[window.localStorage.getItem("lang")] || "Status",
                //     render: function(data) {
                //         //let language = window.localStorage.getItem("lang") || "en"; // Default to English
                //         let activeText = arrLang?.[language]?.["active"];
                //         let inactiveText = arrLang?.[language]?.["inactive"];

                //         return data === 'Y' ?
                //             `<span class="badge lang btn btn-primary btn-sm">${activeText}</span>` :
                //             `<span class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: white;">${inactiveText}</span>`;
                //     },
                //     className: "text-center d-none d-md-table-cell extra-column noExport text-wrap"
                // },
                {
                    "data": "encrypted_auditplanteamid",
                    "render": function(data, type, row) {
                        // Check the statusflag value
                        if (row.statusflag === 'S') {
                            return `<center>
                        <a class="btn editicon edit_btn" id="${data}">
                            <i class="ti ti-edit fs-4"></i>
                        </a>
                    </center>`;
                        } else if (row.statusflag === 'F') {
                            return `<center>
                        <span class="badge bg-success fs-2">Finalized</span>
                    </center>`;
                        } else if (row.statusflag === 'Y') {
                            // In case the statusflag is 'Y' or other values, display the active badge or any other appropriate content
                            return `<center>
                        <a class="btn editicon edit_btn" id="${data}">
                            <i class="ti ti-edit fs-4"></i>
                        </a>
                    </center>`
                        }
                        return ''; // Default return when no matching statusflag
                    }
                }

            ],
            "initComplete": function(settings, json) {
                $("#regiontable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

    }

    /********************************************** Fetch Data ********************************************** */



    /********************************************** Edit - Data ********************************************** */

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here
        var id = $(this).attr('id'); //Getting id of user clicked edit button.
        if (id) {
            $('#auditteamid').val(id);
            reset_form();
            getTeamdetail(id);

        }
    });

    function getTeamdetail(auditteamid) {
        $.ajax({
            url: "manualplan/fetchUpdatepManualplan", // Your API route to get user details
            method: 'POST',
            data: {
                auditteamid: auditteamid
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                console.log(response);

                usernameList = $('#usernames');
                // exit;

                if (response.success) {

                    change_button_as_update('auditteam', 'action', 'buttonaction',
                        'display_error', '', '');
                    $("#auditteam").validate().resetForm(); // Reset the validation errors
                    const teamData = response.data; // Get the team data array

                    const firstRecord = teamData[0];
                    $('#deptcode').val(firstRecord.deptcode); // Set department name in #deptcode
	                    $('#remarks').val(firstRecord.remarks);
	                    $('#instdesigcount').val(firstRecord.membercount);
	                    $('#auditplanid').val(firstRecord.encrypted_auditplanid);
	                    $('#edit_is_spillover').val(firstRecord.spillover === 'Y' ? 'Y' : 'N');
	                    $('#edit_remainingmandays').val(firstRecord.remainingmandays || '');

	                    $('#mandays').val(firstRecord.spillover === 'Y' && firstRecord.remainingmandays ?
	                        firstRecord.remainingmandays : firstRecord.mandays);
                    $('#teamsize').val(firstRecord.teamsize);
                    onchange_deptcode(firstRecord.regioncode);
                    onchange_regioncode(firstRecord.regioncode, firstRecord.distcode);
                    // alert(firstRecord.auditplanid);
                    //get_institution_details(firstRecord.deptcode,firstRecord.regioncode,firstRecord.distcode,firstRecord.auditplanid)



                    // Populate the department code
                    if (teamData.length > 0) {
                        // Use the first record for shared values

                        get_auditor_details(firstRecord.distcode, firstRecord.regioncode, firstRecord
                            .deptcode, firstRecord.encrypted_auditplanteamid, firstRecord
                            .instid)





                        //$('#teamcode').val(firstRecord.teamcode);

                        // Select all radio buttons with the name 'radio-solid-success'
                        let radios = document.querySelectorAll('input[name="radio-solid-success"]');

                        // Iterate through the radio buttons
                        radios.forEach((radio) => {
                            if (radio.value === firstRecord.auditordiststatus) {
                                radio.checked = true; // Mark this radio button as checked
                            }
                        });

                        // Example values
                        var memberIds = JSON.parse(firstRecord
                            .newteammembers); // e.g., ["252", "258", "255"]
                        var memberDetails = firstRecord.newteammembernames.split(',').map(function(
                            member) {
                            return member.trim().split(" - "); // [name, designation, id]
                        });

                        // Clear previous entries
                        $('#team-head').empty();
                        $('#team-members').empty();

                        // Loop and add members
                        memberDetails.forEach(detail => {
                            memberIds = memberIds.map(id => id.toString());
                            if (detail.length === 4) {
                                const name = detail[0].trim();
                                const designation = detail[1].trim();
                                const userid = detail[2].trim();
                                const desigcode = detail[3].trim();

                                if (memberIds.includes(userid)) {
                                    const listItem = $(`
                                        <li class="list-group-item" data-userid="${userid}" data-desigcode="${desigcode}">
                                            <strong>${name}</strong> - ${designation}
                                        </li>
                                    `);


                                    $('#team-members').append(listItem);
                                }
                            } else {
                                console.log("Invalid member detail format:", detail);
                            }
                        });


                        const headDetail = firstRecord.newteamheadname.trim().split(
                            " - "); // [name, designation, id]

                        if (headDetail.length === 4) {
                            const name = headDetail[0].trim();
                            const designation = headDetail[1].trim();
                            const userid = headDetail[2].trim();
                            const desigcode = headDetail[3].trim();

                            const listItem = $(`
                                <li class="list-group-item" data-userid="${userid}" data-desigcode="${desigcode}">
                                    <strong>${name}</strong> - ${designation}
                                </li>
                            `);

                            $('#teamhead').append(listItem);
                            $('#team-head').append(listItem);
                        } else {
                            console.log("Invalid head detail format:", headDetail);
                        }





                    } else {
                        // Handle empty data (e.g., no team data found)
                        alert('No team details found for the given team code.');
                    }

                } else {
                    alert('User not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    }

    function remove_file() {
        $('#view_file-list-container').hide();
        $('#file').val('').show();
        // $('#uploadid').val('');
    }

    function view_files(file) {
        const fileListContainer = $('#view_file-list-container');
        $('#file').hide();
        fileListContainer.empty(); // Clear previous file cards

        $('#file').val('');
        // Set the fileuploadid directly since it's a single file
        $('#uploadid').val(file.fileuploadid);

        const fileCard = `

                <div class="card overflow-hidden mb-3 bg-light card-fixed-width" id="viewfile-card-${file.id}">
                    <div class="d-flex flex-row">

                        <div class="p-3 mb-1">
                            <h3 class="text-dark mb-0 fs-2">
                                <a style="color:black;" href="/${file.path}" target="_blank">${file.name}</a>
                            </h3>
                        </div>
                        <div class="p-1 align-items-center mt-2 "  onclick="remove_file()">
                            <h5 class="text-danger box mb-0 round-40 p-1">
                                <i class="ti ti-trash"></i>
                            </h5>
                        </div>
                    </div>
                </div>
            `;

        fileListContainer.append(fileCard); // Add the file card to the container
    }

    // $(document).on('click', '.edit_btn', function() {
    //     const id = $(this).attr('id');
    //     // alert(id);
    //     if (id) {
    //         $('#auditteamid').val(id);

    //         $.ajax({
    //             url: "{{ route('fetchupdatedata') }}",
    //             method: 'POST',
    //             data: {
    //                 auditteamid: id
    //             },
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     if (response.data && response.data.length > 0) {
    //                         populatecallforForm(response.data[0]); // Populate form with data
    //                     } else {
    //                         alert('No data found');
    //                     }
    //                 } else {
    //                     alert('Data fetch failed');
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.error('Error:', xhr.responseText || 'Unknown error');
    //                 alert('An error occurred while fetching data.');
    //             }
    //         });
    //     }
    // });

    // function populatecallforForm(charge) {
    //     $('#display_error').hide();
    //     $('#distcode').val(charge.distcode);
    //     $('#regioncode').val(charge.regioncode);
    //     $('#auditteamid').val(charge.encrypted_auditplanid);
    //     $('#deptcode').val(charge.deptcode).trigger('change');

    //     onchange_deptcode(charge.deptcode, charge.regioncode); // Example: handle department code change logic
    //     onchange_regioncode(charge.regioncode, charge.distcode)

    //     // Optionally, you can set other form fields here if needed
    //     // $('#otherField').val(charge.someOtherField);
    // }
    /********************************************** Edit - Data ********************************************** */
</script>
@endsection
