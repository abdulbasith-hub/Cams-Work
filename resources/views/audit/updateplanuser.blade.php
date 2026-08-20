@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', ' Audit Team')

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
</style>

<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
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


                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="department"for="validationDefault01">Department
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="deptcode" name="deptcode"
                                onchange="onchange_deptcode()" <?php echo $make_dept_disable; ?>>
                                <option value="">Select Department</option>
                                @foreach ($dept as $department)
                                    <option value="{{ $department->deptcode }}"
                                        @if (old('dept', $deptcode) == $department->deptcode) selected @endif
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
                                onchange="get_auditor_details('', '', '','','')" <?php echo $make_dist_disable; ?>>
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
                                onchange="getscheduledauditordels('', '', '')">
                                <!-- onchange="get_auditor_details('', '', '')"  -->
                                <option value="">Select Institution</option>
                            </select>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="teamhead">Old Team Head</label>
                            <textarea class="form-control" id="teamhead" name="teamhead" rows="4" readonly disabled></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="teammembers">Old Team Members</label>
                            <textarea class="form-control" id="teammembers" name="teammembers" rows="4" readonly disabled></textarea>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4 ">
                            <label class="form-label lang" key="auditors_det">Auditor's Details </label>
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
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" for="teamhead">Remarks</label>
                            <textarea class="form-control alpha_numeric" maxlength="200" id="remarks" name="remarks" rows="4"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label  lang" for="teammembers">Upload</label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept="application/pdf" />
                            <input type="hidden" class="form-control" id="uploadid" name="uploadid">

                            <div id="view_file-list-container">
                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center">

                        <div class="col-md-4 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3 lang" key="savedraft_btn" type="submit"
                                action="insert" id="buttonaction" name="buttonaction">Save Draft </button>
                            <button class="btn button_finalise mt-3 lang" key="final_btn" type="submit"
                                id="finalisebtn"> Finalize
                            </button>
                            <button type="button" class="btn btn-danger mt-3 lang" key ="clear_btn"
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
                                    <th class="lang" key="">Team Head</th>
                                    <th class="lang" key="">Old Team Memebrs</th>

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

<script>
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

    Sortable.create(teamHead, {
        group: 'shared',
        animation: 150,
        onAdd: function(evt) {
            // Ensure only one item in Team Head
            $('#member_error').hide();
            if (teamHead.children.length > 1) {
                toastr.error("Team can have only one Head", " ", {
                    progressBar: true,
                });
                //                 toastr.warning("Team can have only one Head", "  ", {
                //   closeButton: true,
                // });

                evt.from.appendChild(evt.item);

            }

        }
    });
    Sortable.create(teamMembers, {
        group: 'shared',
        animation: 150,
        onAdd: function(evt) {
            $('#member_error').hide();
    	   const instdesigcount = $('#instdesigcount').val()


            if (teamMembers.children.length > instdesigcount) {

                toastr.error("Team Members reached the maximum count", " ", {
                    progressBar: true,
                });
                evt.from.appendChild(evt.item);
            }
        }
    });
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

    function onchange_deptcode(regioncode_session = '') {
        reset_auditor_datas();
        var deptcode = $('#deptcode').val();
        // alert(deptcode);
        // alert(regioncode_session);
        $(' #regioncode ,#distcode, #team_name').empty();
        $('#teamhead,#teammembers').val('')
        $('#auditornames').empty();
        $('#team-members').empty(); // Removes all child list items (<li>)
        $('#team-head').empty();


        $.ajax({
            url: '/audit_team/FilterByDept', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var data = response;

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





    // get_auditor_details('A', '');

    function reset_auditor_datas() {
        $('#auditornames').empty();
        $('#team-head').empty();
        $('#team-members').empty();

    }


    function get_auditor_details(distcode, regioncode, deptcode, auditteamid, auditplanid) {

        reset_auditor_datas();
        if (!(deptcode)) deptcode = $('#deptcode').val();
        if (!(regioncode)) regioncode = $('#regioncode').val();
        if (!(distcode)) distcode = $('#distcode').val();
        if (!(auditplanid)) auditplanid = $('#instid').val();
        get_institution_details(deptcode, regioncode, distcode, auditplanid);




        // if (!(distcode)) {
        //     distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;
        // }
        $.ajax({
            url: '/get-auditors_updateplanuser', // Adjust URL accordingly
            type: 'POST',
            dataType: 'json',
            data: {
                distcode: distcode,
                // auditteamid: auditteamid,
                regioncode: regioncode,
                deptcode: deptcode,
                auditteamid: auditteamid,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
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

                            // Append the HTML content for each auditor
                            listItem.html(`
                                        <div class="row">
                                            <div class="d-flex flex-row comment-row">
                                                <div class="col-md-12 ms-4">
                                                    <h6 class="fw-medium">${auditor.username} - ${auditor.desigelname} (${auditor.distename})</h6>
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
        if (!selectedinstid) selectedinstid = $('#instid').val();

        const institutionDropdown = $('#instid');


        institutionDropdown.empty();
        institutionDropdown.append(
            `<option value="">${lang === 'ta' ? '?????????? ?????????????????' : 'Select Institution'}</option>`);

        // if (!deptcode || !regioncode || !distcode) {
        //     if (callback) callback();
        //     return;
        // }

        $.ajax({
            url: '/audit/fetchinstitution', // Your API route to get user details
            method: 'POST',
            data: {
                distcode: distcode,
                regioncode: regioncode,
                deptcode: deptcode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var data = response;
                $('#instid').empty();
                $('#instid').append('<option value="">Select Institution</option>');

                $.each(data.auditor, function(index, inst) {
                    var isSelected = (inst.auditplanid === selectedinstid) ? 'selected' : '';
                    var instename = inst.instename || '';
                    var insttname = inst.insttname || '';

                    $('#instid').append(
                        '<option value="' + inst.auditplanid + '" ' +
                        isSelected +
                        ' data-name-en="' + instename + '"' +
                        ' data-name-ta="' + insttname + '">' +
                        (lang === "en" ? instename : insttname) +
                        '</option>'
                    );
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
        remove_file();
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
            remarks: {
                required: true
            },
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



            if ((teamHead.children.length > 0) && (teamMembers.children.length > 0)) {
                get_insertdata('insert')
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
            if (teamMembers.children.length > instdesigcount) {

                var message = 'New Audit Members Exceeds the count.This Institution allows only ' +
                    instdesigcount +
                    ' members';
                passing_alert_value('Confirmation', message, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                return;
            }
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

                var oldTeamHead = $('#teamhead').val().trim();
                var oldTeamMembers = $('#teammembers').val().trim().replace(/\n/g, '<br>');

                const select = document.getElementById("instid");
                const selectedinstitution = select.options[select.selectedIndex].text;




                data =
                    '<table style="width:100;%" class="table table-hover w-100 table-bordered display largemodal"><tbody><tr><td><b>Department</b></td><td>' +
                    $("#deptcode option:selected").text() +
                    '</td></tr><tr><td><b>Institution Name</b></td><td>' +
                    selectedinstitution + '</td></tr><tr><td><b>Old Team Head</b></td><td>' +
                    oldTeamHead + '</td></tr><tr><td><b>Old Team Members</b></td><td>' +
                    oldTeamMembers + '</td></tr><tr><td><b>New Team Head</b></td><td>' +
                    TeamHeadmergedValue + '</td></tr><tr><td><b>New Team Members</b></td><td>' +
                    selectedValues +
                    '</td></tr></tbody></table>';
                // '<table style="width:100;%" class="table table-bordered view_detail largemodal"><tbody><tr><th><span class="lang" key="invoice_no">Department Name</span></th><td>' +
                // $("#deptcode option:selected").text() + '</td></tr><tr><th>Team Name</th><td>' + $(
                //     '#team_name').val() + '</td></tr><tr><th>Team Head</th><td>' + TeamHeadmergedValue +
                // '</td></tr><tr><th>Team Members</th><td>' + selectedValues;

                content = '';

                passing_large_alert('Confirmation', data, 'large_confirmation_alert',
                    'large_alert_header',
                    'large_alert_body', 'forward_alert');
                $("#large_modal_process_button").html("Ok");
                $("#large_modal_process_button").addClass("button_finalize");
                $('#large_modal_process_button').removeAttr('data-bs-dismiss');

                //
            } else {
                // If the form is not valid, show an alert
                // alert("Form is not valid. Please fix the errors.");
            }

        }
    });

    $('#large_modal_process_button').on('click', function() {
        var confirmation = 'Are you sure to finalize?';
        $('#large_confirmation_alert .modal-content').addClass('blurred');

        passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
            'alert_body', 'forward_alert');
        $('#confirmation_alert').css('z-index', 100000);
        $("#process_button").html("Ok");

    });

    /**Finalizing Process */
    $('#process_button').on('click', function() {
        $("#large_confirmation_alert").modal("hide");
        get_insertdata('finalise')

    });



    $('#reset_button').on('click', function() {
        reset_form(); // Call the reset_form function
    });


    function get_insertdata(action) {

        const instdesigcount = $('#instdesigcount').val()

        const teamMembers = document.getElementById('team-members');
        if (teamMembers.children.length > instdesigcount) {

            var message = 'New Audit Members Exceeds the count.This Institution allows only ' + instdesigcount +
                ' members';
            passing_alert_value('Confirmation', message, 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }
        // distcode = document.querySelector('input[name="radio-solid-success"]:checked').value;




        var distcode = $('#distcode').val($('#distcode').val());

        var formData = new FormData($('#auditteam')[0]);

        // var formData = $('#auditteam').serializeArray();
        const {
            teamHeadId,
            teamMemberIds
        } = collectUserIds(); // Corrected call

        // Append additional values to formData array
        // formData.push({
        //     name: 'teamHeadId',
        //     value: teamHeadId
        // });
        formData.append(
            'teamHeadId',
            teamHeadId
        );
        // if (Array.isArray(teamMemberIds)) {
        //     teamMemberIds.forEach(id => {
        //         formData.append('teamMemberIds[]', id);
        //     });
        // }
        var oldTeamHead = $('#teamheadids').val();
        var oldTeamMembers = $('#teammemberids').val();

        formData.append(
            'oldteamhead',
            oldTeamHead
        );
        // if (Array.isArray(oldTeamHead)) {
        //     oldTeamHead.forEach(id => {
        //         formData.append('oldteamhead[]', id);
        //     });
        // }
        // formData.append(
        //     'oldteammembers',
        //     JSON.stringify(oldTeamMembers)
        // );
       // if (Array.isArray(oldTeamMembers)) {
            //oldTeamMembers.forEach(id => {
             //   formData.append('oldteammembers[]', id);
          //  });
       // }

  formData.append(
            'oldteammembers',
            oldTeamMembers
        );

        formData.append(
            'newteamhead',
            teamHeadId
        );

        // if (Array.isArray(teamHeadId)) {
        //     teamHeadId.forEach(id => {
        //         formData.append('newteamhead[]', id);
        //     });
        // }
        formData.append(
            'newteammembers',
            JSON.stringify(teamMemberIds)
        );

        // Conditionally append finaliseflag based on action
        if (action === 'finalise') {
            formData.append(
                'finaliseflag',
                'Y'
            );
        } else if (action === 'insert') {
            formData.append(
                'finaliseflag',
                'N'
            );
        }
        // formData.push({
        //     name: 'teamMemberIds',
        //     value: JSON.stringify(teamMemberIds)
        // });

        // formData.push({
        //     name: 'auditordiststatus',
        //     value: distcode
        // });

        // formData.push({
        //     name: 'oldteamhead',
        //     value: oldTeamHead
        // }, {
        //     name: 'oldteammembers',
        //     value: oldTeamMembers
        // }, {
        //     name: 'newteamhead',
        //     value: teamHeadId
        // }, {
        //     name: 'newteammembers',
        //     value: JSON.stringify(teamMemberIds)
        // });

        // // Conditionally append finaliseflag based on action
        // if (action === 'finalise') {
        //     formData.push({
        //         name: 'finaliseflag',
        //         value: 'Y'
        //     });
        // } else if (action === 'insert') {
        //     formData.push({
        //         name: 'finaliseflag',
        //         value: 'N'
        //     });
        // }

        // // AJAX request
        $.ajax({
            url: '/auditteam_insertupdate', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            processData: false, // Important!
            contentType: false,
            success: function(response) {
                if (response.success) {

                    reset_form();

                    $('#confirmation_alert').modal('show');
                    if (action === 'finalise') {
                        var responsefinal = 'Data Finalized Successfully';

                    } else {
                        var responsefinal = response.message;

                    }

                    passing_alert_value('Confirmation', responsefinal, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');

                    var lang = getLanguage();
                    initializeDataTable(lang); // Reload the table
                } else if (response.error) {}
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error|| 'Contact Admin';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');

                console.error('Error details:', xhr, status, error);
            }
        });
    }








    function collectUserIds() {
        const teamHead = document.querySelector('#team-head .list-group-item');
        const teamMembers = document.querySelectorAll('#team-members .list-group-item');

        // Collect the `userid` for the Team Head (if present)
        const teamHeadId = teamHead ? teamHead.getAttribute('data-userid') : null;

        // Collect the `userid` values for all Team Members
        const teamMemberIds = [];
        teamMembers.forEach(member => {
            teamMemberIds.push(member.getAttribute('data-userid'));
        });

        // Correctly return the object with teamHeadId and teamMemberIds
        return {
            teamHeadId,
            teamMemberIds
        };
    }


    /*********************************** Insert,update,Finalise,Reset **********************************************/


    /********************************************** Fetch Data ********************************************** */

    $(document).ready(function() {
        $('#auditteam')[0].reset();
        // updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('fetchUpdateplanTeam') }}",
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
                        return row.instename || '-';
                    }
                },
                {
                    data: "oldteamheadname",
                    title: columnLabels?.["oldteamheadname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    // render: function(data, type, row) {
                    //     return row.oldteamheadname || '-';
                    // }

                    render: function(data, type, row) {
                        if (!row.oldteamheadname) return '-';

                        return row.oldteamheadname
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
                    data: "oldteammembernames",
                    title: columnLabels?.["oldteammembernames"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        if (!row.oldteammembernames) return '-';

                        return row.oldteammembernames
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
                    "data": "encrypted_auditteamsdraftid",
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
                        <span class="badge bg-primary fs-2">Active</span>
                    </center>`;
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
            url: "{{ route('fetchupdatedata') }}", // Your API route to get user details
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

                    // $('#team_name').val(firstRecord.teamname);
                    onchange_deptcode(firstRecord.regioncode);
                    onchange_regioncode(firstRecord.regioncode, firstRecord.distcode);
                    // alert(firstRecord.auditplanid);
                    //get_institution_details(firstRecord.deptcode,firstRecord.regioncode,firstRecord.distcode,firstRecord.auditplanid)



                    // Populate the department code
                    if (teamData.length > 0) {
                        // Use the first record for shared values

                        get_auditor_details(firstRecord.distcode, firstRecord.regioncode, firstRecord
                            .deptcode, firstRecord.encrypted_auditteamsdraftid, firstRecord
                            .auditplanid)





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
                            if (detail.length === 3) {
                                const name = detail[0].trim();
                                const designation = detail[1].trim();
                                const userid = detail[2].trim();

                                if (memberIds.includes(userid)) {
                                    const listItem = $(`
                                        <li class="list-group-item" data-userid="${userid}">
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

                        if (headDetail.length === 3) {
                            const name = headDetail[0].trim();
                            const designation = headDetail[1].trim();
                            const userid = headDetail[2].trim();

                            const listItem = $(`
                                <li class="list-group-item" data-userid="${userid}">
                                    <strong>${name}</strong> - ${designation}
                                </li>
                            `);

                            $('#teamhead').append(listItem);
                            $('#team-head').append(listItem);
                        } else {
                            console.log("Invalid head detail format:", headDetail);
                        }



                        const oldheadDetail = firstRecord.oldteamheadname.trim().split(
                            " - "); // [name, designation, id]

                        if (oldheadDetail.length === 3) {
                            const name = oldheadDetail[0].trim();
                            const designation = oldheadDetail[1].trim();
                            const userid = oldheadDetail[2].trim();

                            // Set the text content in the <textarea>
                            $('#teamhead').val(`${name} - ${designation}`);

                            // Create a list item for the <ul>
                            const listItem = $(`
                                <li class="list-group-item" data-userid="${userid}">
                                    <strong>${name}</strong> - ${designation}
                                </li>
                            `);

                        } else {
                            console.log("Invalid head detail format:", oldheadDetail);
                        }


                        // Split the string into member detail arrays
                        var memberDetails = firstRecord.oldteammembernames.split(',').map(function(
                            member) {
                            return member.trim().split(" - "); // [name, designation, id]
                        });

                        // Create a formatted string for the textarea
                        var memberText = memberDetails.map(function(detail) {
                            if (detail.length === 3) {
                                return `${detail[0].trim()} - ${detail[1].trim()}`; // omit ID
                            } else {
                                console.log("Invalid member detail format:", detail);
                                return null;
                            }
                        }).filter(Boolean).join('\n');

                        // Set value to textarea
                        $('#teammembers').val(memberText);

                        const fileDetail = firstRecord.filedetails; // Split by comma for each file

                        // alert(firstItem.filedetails_1);
                        if (fileDetail.trim() != '-') {

                            $('#view_file-list-container').show()
                            const [name, path, size, fileuploadid] = fileDetail.split('-');

                            // Create the file object
                            const files = {
                                id: 1, // Static ID for a single file
                                name: name,
                                path: path,
                                size: size,
                                fileuploadid: fileuploadid
                            };
                            view_files(files);

                        } else if (fileDetail.trim() == '-')

                        {
                            remove_file()
                        }
                        // Split the fileDetail by hyphen (-)







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
