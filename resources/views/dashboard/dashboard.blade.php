@section('content')
    @extends('index2')
    @include('common.alert')
    @php
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;
        $distcode = $sessionchargedel->distcode;

        $sessionroletypecode = $sessionchargedel->roletypecode;
        $sessionroleactioncode = $sessionchargedel->roleactioncode;
      

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_dist_disable = $distcode ? 'disabled' : '';

        $showSection = $sessionroletypecode == view()->shared('Dist_roletypecode');


       
        $AD_allowroletype =[view()->shared('Dist_roletypecode'),view()->shared('Re_roletypecode'),view()->shared('Ho_roletypecode')];


        if(in_array($sessionroletypecode,$AD_allowroletype) && ($sessionroleactioncode == '02' || $sessionroleactioncode == '07' || $sessionroleactioncode == '11' || $sessionroleactioncode == '10'))
        {
            $showAD_District_tab ='Y';

        }else
        {
            $showAD_District_tab ='N';
        }

        // $adminallow =['03','04','05','07'];
        $adminallow =[view()->shared('Ho_roletypecode'),view()->shared('DGA_roletypecode'),view()->shared('Admin_roletypecode'),view()->shared('NIC_roletypecode')];

        if(in_array($sessionroletypecode,$adminallow))
        {
            $showSectionTab ='Y';
        }else
        {
            $showSectionTab ='N';
        }


   
    @endphp
    <style>

          .close-btn {
            position: absolute;
            color: #53565a; /* Blue color */
            font-size: 34px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
            }

           .close-btn:hover {
            color: #53565a; /* Darker blue on hover */
            }
        .nav-tabs .nav-link {
            border: 2px solid #5a6174;
            color: #5a6174;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 0 5px;
            /* Add gap between tabs */
        }

        .nav-tabs .nav-link.active {
            border: 2px solid #4973e8;
            color: rgb(245, 245, 245);
            background-color: #4973e8;
        }

        .nav-tabs .nav-link:hover {
            border: 2px solid #4973e8;
        }

        .list-group-item:nth-child(odd) {
            background-color: white;
        }

        .list-group-item:nth-child(even) {
            background-color: #ebf3fe;
        }

        .list-group-item:hover {
            background-color: #809fff;
            cursor: pointer;
        }

        .card-body {
            padding: 1px 10px;
        }

        .card {
            margin-bottom: 2px;
        }

        .card-slip {
            border: 1px solid #ebf1f6 !important;
            border-radius: 12px !important;
            box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1) !important;
            padding: 10px !important;
        }

        .card-slip:nth-child(1) {
            background-color: rgb(41, 86, 153);
            /* Total Slips */
        }

        .card-slip:nth-child(2) {
            background-color: lightgreen;
            /* Dropped Slips */
        }

        .card-slip:nth-child(3) {
            background-color: rgb(225, 138, 6);
            /* Converted to Paras */
        }

        .card-slip:nth-child(4) {
            background-color: lightgoldenrodyellow;
            /* Pending Slips */
        }

        .round-40 {
            width: 50px !important;
            height: 50px !important;
            background: transparent;
        }

        .card-title {
            font-size: 22px;
            font-weight: bold;
        }

        #allocatedplans_tab-pane .card-body
        {
            padding:20px !important;
        }

        .schedulingtable th,
        .schedulingtable td
        {
            text-align:center;

        }

        .schedulingtable thead {
            display: table-header-group !important;
            visibility: visible !important;
        }

        .schedulingtable tfoot th {
           background-color:#e9e9e9 !important;
           color:black !important;
           text-align:right !important;

        }    

        #slipHistoryTable th
        {
             background-color: #6b6b6c;
            color:white  !important;

            
        }

       

        #commencedinstitutewisetable .dt-buttons,
        #institutewisetable .dt-buttons {
            margin-bottom: 20px !important;
        }

        .text-left
        {
            text-align:left !important;
        }

        .text-right
        {
            text-align:right !important;

        }

         .table_slip td {
        width: 60%;
    }

    .table_slip th {
        width: 40%;
    }

    /* General styling for the table */
    .auditor-table {
        width: 100%;
        border-collapse: collapse;
        /* Ensures no gaps between cells */
        font-family: Arial, sans-serif;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Subtle shadow for a lifted effect */
        margin: 20px 0;
        border-radius: 8px;
        background-color: #ffffff;
        /* White background */
        border: 1px solid #e1e1e1;
        /* Light border color */
        font-size: 14px;
    }

    /* Header styling */
    .auditor-table th {
        text-align: left;
        /* Align text to the left */
        background-color: #f2f2f2;
        /* Light gray background */
        color: #333;
        /* Dark text for contrast */
        padding: 12px 15px;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
        /* Adds separator line between rows */
    }

    /* Data cell styling */
    .auditor-table td {
        padding: 12px 15px;
        color: #555;
        /* Lighter gray text color */
        border-bottom: 1px solid #ddd;
        /* Border between rows */

    }

    /* Hover effect for rows */
    .auditor-table tr:hover {
        background-color: #fafafa;
        /* Light gray background when hovering */
    }


    </style>


<style>
       

       .modal-header {
           background-color: #3866ac;
           color: #fff;
           display: flex;
           justify-content: center;
           /* Centers content horizontally */
           align-items: center;
           /* border-radius: 20px; */

       }

       .modal-title {
           font-weight: bold;
           margin: 0 auto;
           /* Ensures it's centered within the modal-header */
           color: white;

       }

       .form-control:focus {
           border-color: none;
           */
           /* box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); */
       }

       .btn-primary {
           /* color: rgb(92, 57, 133); */
           background-color: #0d6efd;
           border: none;
       }

       .btn-primary:hover {
           background-color: #0b5ed7;
       }

       .content {

         
            border-radius: 20px; 
           overflow: hidden;
       }

       .form-label {
           color: #42669c;
       }

       .btn-close {
           border: none;
           /* Remove the default border */
           opacity: 1;
           /* Make it fully visible */
       }

       .btn-close:hover {
           color: #bea9a9;
       }

       .btn-close:focus {
           box-shadow: none;
           /* Remove focus outline if present */
       }

       .toggle-password {
               position: absolute;
               right: 10px;
               top: 10px; /* Fixed position from the top */
               cursor: pointer;
               pointer-events: auto;
}

       /* Default color for the eye icon */
       .toggle-password i {
           color: #5682b3;
           transition: color 0.3s ease;
       }

       /* Hover state */
       /* .toggle-password:hover i {
                       color:  #2f5286;
                   } */

       /* Active state when showing password */
       .toggle-password.active i {
           color: #28a745;
           /* Custom color (green) when password is visible */
       }
   </style>


<script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="../common/ajaxfn.js"></script>
<script>
   const tabs = document.querySelectorAll(".nav-link");
const viewDetailsCard = document.getElementById("view_Details");
const tabPanes = document.querySelectorAll(".tab-pane");
const cardsContainer = document.getElementById("cards-container");

// Ensure the main card has a border
viewDetailsCard?.style.setProperty("border", "1px solid black");

tabs.forEach(tab => {
    tab.addEventListener("click", function () {
        // Remove 'active' class from all tabs and tab panes
        tabs.forEach(t => t.classList.remove("active"));
        tabPanes.forEach(pane => pane.classList.remove("show", "active"));

        // Activate clicked tab and corresponding pane
        this.classList.add("active");
        document.querySelector(this.getAttribute("data-bs-target"))?.classList.add("show", "active");

        // Check if "all-slip-tab" is clicked, and toggle visibility of the card container accordingly
        if (this.id === "all-slip-tab") {
            cardsContainer?.style.setProperty("display", "flex"); // Show cards
        } else {
            cardsContainer?.style.setProperty("display", "none"); // Hide cards
        }
    });
});

function resetDropdowns() {
            $('#auditscheduleid1').val(0);
                $('#auditscheduleid2').val(0);
                var selectedValue = document.getElementById('auditscheduleid2').value;
            var cardsContainer = document.getElementById('cards-container');

            // Hide the cards if "Select" is chosen
            if (selectedValue === "0" || selectedValue === "") {
                cardsContainer.style.display = 'none';
            } else {
                cardsContainer.style.display = 'flex';
            }
        }
function hideDatatable() {
            $('#dataTableContainer').hide();
            resetDropdowns();


        }
        
        function populateDataTable(description, dropdownId, countid, cardColor, cardname) {
    let auditScheduleId = $('#' + dropdownId).val();
    var institutionName = document.getElementById(dropdownId).options[
        document.getElementById(dropdownId).selectedIndex
    ].text;

    if ($('#scehduled_plans_tab').hasClass('active')) {
        tabValue = 1;
    } else if ($('#slip_details_tab').hasClass('active')) {
        tabValue = 2;
    } else if ($('#all-slip-tab').hasClass('active')) {
        tabValue = 3;
    }

    if (auditScheduleId > 0)
        cardname = institutionName + ' - ' + cardname;

    // Get the count value
    let countvalue = $('#' + countid).html();

    //alert(countvalue);
    // If count is 0, call hideDatatable and return early
    if (countvalue === "0" || countvalue === 0) {
        hideDatatable();
        return;
    }

    // If count is greater than 0, show the data table
    $('#dataTableContainer').show();
    $('.card_header_color').css('background-color', cardColor);
    $('.showinstname').text(cardname);

    $.ajax({
        url: "{{ route('descriptionData') }}",
        type: 'GET',
        data: {
            activeTab: tabValue,
            auditscheduleid: auditScheduleId,
            description: description,
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            console.log("Response received:", response);
            updateTable(response, description);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
}


$(document).ready(function() {
            // Function to capture the active tab when a tab is clicked
            $('#myTab .nav-link').on('click', function() {
                var activeTab = $(this).attr('id');
                console.log("Active tab: ", activeTab);

                window.activeTab = activeTab;
                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                resetDropdowns(); // Reset dropdowns when switching tabs
                hideDatatable(); // Hide data table

                //setTimeout(fetchDataForActiveTab, 300);
            });

            let activeDropdown = null;



            $('#auditscheduleid, #auditscheduleid2').on('change', function() {

                activeDropdown = $(this).attr('id');
                var selectedValue = $(this).val();
                var tabValue = 0;


                if ($('#scehduled_plans_tab').hasClass('active')) {
                    tabValue = 1;
                } else if ($('#slip_details_tab').hasClass('active')) {
                    tabValue = 2;
                } else if ($('#all-slip-tab').hasClass('active')) {
                    tabValue = 3;
                }

                if (selectedValue) {
                    $.ajax({
                        url: "{{ route('CallingData') }}",
                        type: "POST",
                        data: {
                            activeTab: tabValue,
                            auditscheduleid: selectedValue,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            console.log(response);
                            if (Array.isArray(response) && response.length > 0) {
                                const data = response[0];

                                if (activeDropdown === 'auditscheduleid') {
                                    updateCard('cnt_team', data.teamcount, 'scheduledinst',
                                        'auditscheduleid');
                                    updateCard('cnt_total', data.totalslipcount, 'allslip',
                                        'auditscheduleid');
                                    updateCard('cnt_pending', data.processslipcount,
                                        'pendingslip', 'auditscheduleid');
                                    updateCard('cnt_completed', data.completedcount,
                                        'completedslip', 'auditscheduleid');
                                } else if (activeDropdown === 'auditscheduleid2') {
                                    updateCard('cnt_total_all', data.totalslipcount, 'allslip',
                                        'auditscheduleid2');
                                    updateCard('cnt_pending_all', data.processslipcount,
                                        'pendingslip', 'auditscheduleid2');
                                    updateCard('cnt_completed_all', data.completedcount,
                                        'completedslip', 'auditscheduleid2');
                                }
                            }
                        },
                        error: function(error) {
                            console.error("Error fetching data:", error);
                        }
                    });
                }
            });

            function updateCard(countId, countValue, description, dropdownId) {
                let card = $('#' + countId).closest('.card-slip');

                // Update count text
                $('#' + countId).text(countValue);

                if (countValue === 0 || countValue === "0") {
                    card.css('cursor', 'default'); // Disable pointer cursor
                    card.off('click'); // Remove any click event attached
                } else {
                    card.css('cursor', 'pointer'); // Enable pointer cursor
                    card.off('click').on('click', function() {
                        //  populateDataTable(description, dropdownId);
                    });
                }
            }
        });


        function toggleCardsVisibility() {
            var selectedValue = document.getElementById('auditscheduleid2').value;
            var cardsContainer = document.getElementById('cards-container');

            // Hide the cards if "Select" is chosen
            if (selectedValue === "0" || selectedValue === "") {
                cardsContainer.style.display = 'none';
            } else {
                cardsContainer.style.display = 'flex';
            }
        }
        $(document).ready(function() {
            let dropdown = $('#auditscheduleid');
            let optionsCount = dropdown.children('option').length;


            if (optionsCount == 2) {
                let selectedOption = dropdown.find('option').not('[value="0"]').val();
                dropdown.val(selectedOption);
                dropdown.prop('disabled', true);
            }
        });
</script>


    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">



    
    <div class="container mt-4">
        @if ($showAD_District_tab == 'Y' || $showSectionTab == 'Y' || $showSection)
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">          
                        <li class="nav-item" role="presentation" onclick="hideDatatable()">
                            <button class="nav-link active" id="scehduled_plans_tab" data-bs-toggle="tab"
                                data-bs-target="#scehduled_plans_tab-pane" type="button" role="tab"
                                aria-controls="scehduled_plans_tab-pane" aria-selected="true">
                                Transactions
                            </button>
                        </li>
                        @if ($showSection)
                        <li class="nav-item" role="presentation" onclick="hideDatatable()">
                            <button class="nav-link" id="slip_details_tab" data-bs-toggle="tab"
                                data-bs-target="#slip_details_tab-pane" type="button" role="tab"
                                aria-controls="slip_details_tab-pane" aria-selected="false">
                                Slip Details
                            </button>
                        </li>
                            @if (
                                $institutionDetails->contains(function ($item) {
                                    return $item->auditteamhead === 'Y';
                                }))
                                <li class="nav-item" role="presentation" onclick="hideDatatable()">
                                    <button class="nav-link" id="all-slip-tab" data-bs-toggle="tab" data-bs-target="#all-slip-tab-pane"
                                        type="button" role="tab" aria-controls="all-slip-tab-pane" aria-selected="false">
                                        All Slip Details
                                    </button>
                                </li>
                            @endif
                        @endif

                        @if ($showAD_District_tab == 'Y' || $showSectionTab == 'Y')
                            <li class="nav-item" role="presentation" onclick="hideDatatable()">
                                <button class="nav-link" id="allocatedplans_tab" data-bs-toggle="tab"
                                    data-bs-target="#allocatedplans_tab-pane" type="button" role="tab"
                                    aria-controls="allocatedplans_tab-pane" aria-selected="false">
                                    Audit Plan Details
                                </button>
                            </li>

                            <li class="nav-item" role="presentation" onclick="hideDatatable()">
                                <button class="nav-link" id="allocatedslips_tab" data-bs-toggle="tab"
                                    data-bs-target="#allocatedslips_tab-pane" type="button" role="tab"
                                    aria-controls="allocatedslips_tab-pane" aria-selected="false">
                                    Audit Slip Details
                                </button>
                            </li>
                        @endif
            </ul>
        @endif
    </div>
    <br>
  
<!-- Card container to ensure uniform border -->
<div class="card mt-3 mx-auto" id="view_Details" style="width: 96%; border: 1px solid grey;">
    <div class="card-body">
        <div class="tab-content pt-2 container-fluid">
            <!-- First Tab Pane -->
            <div class="tab-pane fade show active" id="scehduled_plans_tab-pane" role="tabpanel" aria-labelledby="scehduled_plans_tab">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip" style="background-color:rgb(10, 100, 173)">
                            <div class="card-body " style="cursor: <?php echo $countDetails[0]['inboxcount'] == 0 || empty($countDetails[0]['inboxcount']) ? 'default' : 'pointer'; ?>" <?php echo $countDetails[0]['inboxcount'] == 0 || empty($countDetails[0]['inboxcount']) ? '' : 'onclick="window.location.href=\'init_fieldaudit\';"'; ?>>
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-inbox fs-7"></i> <!-- Changed to inbox icon -->
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="" class="card-title mb-1 text-white">
                                            {{ $countDetails[0]['inboxcount'] ?? 0 }}</h4>
                                        <p class="card-subtitle text-white">Inbox</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip" style="background-color:rgb(134, 10, 97);">
                            <div class="card-body" style="cursor: <?php echo $countDetails[0]['sentcount'] == 0 || empty($countDetails[0]['sentcount']) ? 'default' : 'pointer'; ?>; >" <?php echo $countDetails[0]['sentcount'] == 0 || empty($countDetails[0]['sentcount']) ? '' : 'onclick="window.location.href=\'sentdetails\';"'; ?>>
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-send fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="" class="card-title mb-1 text-white">
                                            {{ $countDetails[0]['sentcount'] ?? 0 }}</h4>
                                        <p class="card-subtitle text-white ">Sent</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Tab Pane -->
            <div class="tab-pane fade" id="slip_details_tab-pane" role="tabpanel" aria-labelledby="slip_details_tab">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <label class="form-label required">Institution</label>
            <select class="form-select" id="auditscheduleid" name="auditscheduleid" style="border:1px solid black; color:black;">
                <option value="0">All</option>
                @foreach ($institutionDetails as $institution)
                    <option value="{{ $institution->auditscheduleid }}" {{ old('auditscheduleid', request('auditscheduleid')) == $institution->auditscheduleid ? 'selected' : '' }}>
                        {{ $institution->instename }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <br>
    <!-- Wrap all cards inside a single row -->
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-6">
                        <div class="card card-slip"
                            style="background-color:rgb(5, 150, 207);
                cursor: <?php echo $countDetails[0]['teamcount'] == 0 || empty($countDetails[0]['teamcount']) ? 'default' : 'pointer'; ?>;">
                            <!-- Redundant block -->
                            <div class="card-body"
                                onclick="populateDataTable('scheduledinst', 'auditscheduleid','cnt_team','rgb(5, 150, 207)','Scheduled Plans')">
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-clipboard-text fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="cnt_team" class="card-title mb-1 text-white"><?php echo $countDetails[0]['teamcount'] ?? 0; ?></h4>
                                        <p class="card-subtitle text-white">Scheduled Plans</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-slip" style="background-color:rgb(167, 17, 227); cursor: <?php echo $countDetails[0]['totalslipcount'] == 0 || empty($countDetails[0]['totalslipcount']) ? 'default' : 'pointer'; ?>;">
                <div class="card-body" onclick="populateDataTable('allslip', 'auditscheduleid','cnt_total','rgb(167, 17, 227)','Total Slip Generated')">
                    <div class="d-flex flex-row gap-6 align-items-center">
                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center" style="background: white !important; color: #007bff !important;">
                            <i class="ti ti-file-stack fs-7"></i>
                        </div>
                        <div class="align-self-center">
                            <h4 id="cnt_total" class="card-title mb-1 text-white"><?php echo $countDetails[0]['totalslipcount'] ?? 0; ?></h4>
                            <p class="card-subtitle text-white">Total Slip Generated</p>
                            <i class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-slip" style="background-color:rgb(191, 96, 79); cursor: <?php echo $countDetails[0]['processslipcount'] == 0 || empty($countDetails[0]['processslipcount']) ? 'default' : 'pointer'; ?>;">
                <div class="card-body" onclick="populateDataTable('pendingslip', 'auditscheduleid','cnt_pending','rgb(191, 96, 79)','Pending Slips')">
                    <div class="d-flex flex-row gap-6 align-items-center">
                        <div class="round-40 rounded-circle d-flex align-items-center justify-content-center" style="background: white !important; color: #007bff !important;">
                            <i class="ti ti-hourglass fs-7"></i>
                        </div>
                        <div class="align-self-center">
                            <h4 id="cnt_pending" class="card-title mb-1 text-white"><?php echo $countDetails[0]['processslipcount'] ?? 0; ?></h4>
                            <p class="card-subtitle text-white">Pending Slips</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-slip" style="background-color:rgb(6, 211, 98); cursor: <?php echo $countDetails[0]['completedcount'] == 0 || empty($countDetails[0]['completedcount']) ? 'default' : 'pointer'; ?>;">
                <div class="card-body" onclick="populateDataTable('completedslip', 'auditscheduleid','cnt_completed','rgb(6, 211, 98)','Completed Slips')">
                    <div class="d-flex flex-row gap-6 align-items-center">
                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center" style="background: white !important; color: #007bff !important;">
                            <i class="ti ti-clipboard-check fs-7"></i>
                        </div>
                        <div class="align-self-center">
                            <h4 id="cnt_completed" class="card-title mb-1 text-white"><?php echo $countDetails[0]['completedcount'] ?></h4>
                            <p class="card-subtitle text-white">Completed Slip</p>
                            <i class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of row -->
</div>
 

@if (
                $headinstitutionDetails->contains(function ($item) {
                    return $item->auditteamhead === 'Y';
                }))
                <div class="tab-pane fade" id="all-slip-tab-pane" role="tabpanel" aria-labelledby="all-slip-tab">

                    <div class="row justify-content-center"
                        onchange="toggleCardsVisibility()">
                        <div class="row justify-content-center" style='margin-bottom:2%;'>
                            <!-- Center the content horizontally -->
                            <div class="col-md-4"> <!-- Set the width of the dropdown -->
                                <label class="form-label required">Institution</label>
                                <select class="form-select" id="auditscheduleid2" name="auditscheduleid"
                                    style='border:1px solid black;color:black'>
                                    <option value="0">Select</option>
                                    @foreach ($headinstitutionDetails as $inst)
                                        <option value="{{ $inst->auditscheduleid }}"
                                            {{ old('auditscheduleid', request('auditscheduleid')) == $inst->auditscheduleid ? 'selected' : '' }}>
                                            {{ $inst->instename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <!-- Cards Container -->
                        <div id="cards-container" class="row justify-content-center">
                            <!-- Initially hidden -->

                            <div class="col-lg-3 col-md-6">
                                <div class="card card-slip"
                                    style="background-color:rgb(167, 17, 227); cursor: {{ $countDetails[0]['totalslipcount'] == 0 || empty($countDetails[0]['totalslipcount']) ? 'default' : 'pointer' }};">
                                    <div class="card-body"
                                        onclick="populateDataTable('allslip','auditscheduleid2','cnt_total_all','rgb(167, 17, 227)','Total Slip Generated')">
                                        <div class="d-flex flex-row gap-6 align-items-center">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                                style="background: white !important; color: #007bff !important;">
                                                <i class="ti ti-file-stack fs-7"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <h4 id="cnt_total_all" class="card-title mb-1 text-white">
                                                    {{ $countDetails[0]['totalslipcount'] ?? 0 }}</h4>
                                                <p class="card-subtitle text-white">Total Slip Generated</p>
                                                <i
                                                    class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="card card-slip" style="background-color:rgb(6, 140, 75);">
                                    <div class="card-body"
                                        onclick="populateDataTable('pendingslip','auditscheduleid2','cnt_pending_all','rgb(6, 140, 75)','Pending Slips')">

                                        <div class="d-flex flex-row gap-6 align-items-center">
                                            <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                                style="background: white !important; color: #007bff !important;">
                                                <i class="ti ti-transform fs-7"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <h4 id="cnt_pending_all" class="card-title mb-1 text-white">
                                                    {{ $countDetails[0]['processslipcount'] ?? 0 }}</h4>
                                                <p class="card-subtitle text-white">Pending Slip</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" >
                                <div class="card card-slip" style="background-color:rgb(91, 61, 161);">
                                    <div class="card-body"
                                        onclick="populateDataTable('completedslip', 'auditscheduleid2','cnt_completed_all','rgb(91, 61, 161)','Completed Slips')">
                                        <div class="d-flex flex-row gap-6 align-items-center">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                                style="background: white !important; color: #007bff !important;">
                                                <i class="ti ti-clipboard-text fs-7"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <h4 id='cnt_completed_all'class="card-title mb-1 text-white">
                                                    {{ $countDetails[0]['completedcount'] ?? 0 }}</h4>
                                                <p class="card-subtitle text-white ">Completed Slips</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
  <div id="allocatedplans_tab-pane" role="tabpanel" aria-labelledby="allocatedplans_tab" class="tab-pane fade">
                <div class="">       
                    <div  class="card-header lang" style="background-color:white;">                    
                        <h4 class="text-center mb-0 fs-7 fw-bolder">Department wise  Audit Plan Details</h4>
                        <div class="row">
                            <div class="col-md-1 mb-1 ms-auto" id="quarter" style='margin-right:6%;'>
                                <label class="form-label  lang" key="" for="quarter">Quarter</label>

                                <select class="form-select mr-sm-2 lang-dropdown" id="quarter" name="quarter" onchange="initDeptwiseDataTable(this.value, 'plantabform', 'deptwisedata_table')">
                                    @if (!empty($auditQuarters) && count($auditQuarters) > 0)
                                        @foreach ($auditQuarters as $quarter)
                                            <option value="{{ $quarter }}" data-name-en="{{ $quarter }}">
                                                {{ $quarter }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Quarter Available" data-name-ta="காலாண்டு எதுவும் இல்லை">
                                            No Quarters Available
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div style="width:85%;margin:0 auto;" class="datatables">
                            <div class="table-responsive" id="tableshow">
                                <table id="deptwisedata_table" class="table w-100  table-bordered display text-nowrap datatables-basic schedulingtable">
                                    <thead>
                                        <tr>
                                            <th style="width:5% !important;text-align:center !important;">Sl. No</th>
                                            <th style="width:58% !important;text-align:left !important;">Name of the Department</th>
                                            <th style="width:12% !important;text-align:center !important;">Total No.of Audit Region</th>
                                            <th style="width:12% !important;text-align:center !important;">Total No.of Audit District</th>
                                            <th style="width:12% !important;text-align:center !important;">Total No.of Auditable Institutions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align:right">Total</th>
                                            <th id="footer_region"></th>
                                            <th id="footer_district"></th>
                                            <th id="footer_institute"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-4"></div>
                </div>
            </div>

            <div id="allocatedslips_tab-pane" role="tabpanel" aria-labelledby="allocatedslips_tab" class="tab-pane fade">
                <div class="">       
                        <div  class="card-header lang" style="background-color:white;">                    
                            <h4 class="text-center mb-0 fs-7 fw-bolder">Department wise Audit Slip Details</h4>
                             <div class="row">
                            <div class="col-md-1 mb-1 ms-auto" id="slipquarter" style='margin-right:2%;'>
                                <label class="form-label  lang" key="" for="slipquarter">Quarter</label>

                                <select class="form-select mr-sm-2 lang-dropdown" id="slipquarter" name="slipquarter" onchange="initDeptwiseSlipDataTable(this.value, 'sliptabform', 'Slip_deptwisedata_table')">
                                    @if (!empty($auditQuarters) && count($auditQuarters) > 0)
                                        @foreach ($auditQuarters as $quarter)
                                            <option value="{{ $quarter }}" data-name-en="{{ $quarter }}">
                                                {{ $quarter }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Quarter Available" data-name-ta="காலாண்டு எதுவும் இல்லை">
                                            No Quarters Available
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        </div>

                        <div style="width:94%;margin:0 auto;" class="datatables">
                                <div class="table-responsive" id="tableshow">
                                    <table id="Slip_deptwisedata_table" class="table w-100  table-bordered display text-nowrap datatables-basic schedulingtable">
                                        <thead>
                                            <tr>
                                                <th style="width:5% !important;text-align:center !important;">Sl. No</th>
                                                <th style="width:31% !important;text-align:left !important;">Name of the Department</th>
                                                <th style="width:8% !important;text-align:center !important;">Total No.of Audit Region</th>
                                                <th style="width:8% !important;text-align:center !important;">Total No.of Audit District</th>
                                                <th style="width:8% !important;text-align:center !important;">Total No.of Institutions <br> Audit Commenced</th>
                                                <th style="width:10% !important;text-align:center !important;">Total Slip(s)</th>
                                                <th style="width:10% !important;text-align:center !important;">Pending Slip(s)</th>
                                                <th style="width:10% !important;text-align:center !important;">Converted to Para(s)</th>
                                                <th style="width:10% !important;text-align:center !important;">Dropped Slip(s)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" style="text-align:right">Total</th>
                                                <th id="footer_region"></th>
                                                <th id="footer_district"></th>
                                                <th id="footer_institute"></th>
                                                <th id="footer_totalslip"></th>
                                                <th id="footer_pendingslip"></th>
                                                <th id="footer_convertedslip"></th>
                                                <th id="footer_droppedslip"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-4"></div>
                    </div>
                            
                </div>
            </div>  
           


        </div>
        <div class="d-flex justify-content-center">
                <div class="card" id="dataTableContainer"
                    style="display: none; border: 1px solid grey; width: 100%;margin-top:1%;">
                    <div class="card-header card_header_color">
                        <span class="showinstname"></span>
                    </div>
                    <br>
                    <div class="table-responsive usertable_detail_wrapper container">
                        <table id="dataTable"
                            class="table w-80 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr id="tableHeader"></tr> <!-- Empty header row will be populated dynamically -->
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>
<br>
<div class="card  card_border hide_this mx-auto regionwisetable" style="width: 95%; border:1px solid grey !important;">
     <br>
        
        <div class="card-header lang" style="background-color:white;">                    
            <h4 class="text-center mb-0 fs-7 fw-bolder"><span class="deptname_show_Reg"></span></h4>
            <i  style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times regwiseclose_btn"></i>                  
        </div>
            <div style="width:85%;margin:0 auto;" class="datatables">
                <div class="table-responsive" id="tableshow">

                    <table id="RegionTable" class="table w-100 table-bordered display text-nowrap datatables-basic schedulingtable">
                        <thead></thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>                           
                </div>
            </div>
            <br>
</div>


<div id="districtwisetable" class="card  card_border hide_this mx-auto districtwisetable" style="width: 95%; border:1px solid grey !important;">
    <br>
        
        <div class="card-header lang" style="background-color:white;">                    
            <h4 class="text-center mb-0 fs-7 fw-bolder"><span class="deptname_show_Reg"></span></h4>
            <i style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times distwiseclose_btn"></i>                  
        </div>
            <div style="width:85%;margin:0 auto;" class="datatables">
                <div class="table-responsive" id="tableshow">
                    <table id="DistrictTable" class="table w-100 table-bordered display text-nowrap datatables-basic schedulingtable">
                            <thead></thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                    </table> 
                </div>
            </div>
            <br>
</div>
<br>

<div id="commencedinstitutewisetable" class="card hide_this mx-auto institutewisetable" style="width: 95%;border-color:grey !important;">


                <div class="card-header lang" style="background-color:white;">                    
                    <h4 class="text-center mb-0 fs-7 fw-bolder"> Institute wise Audit Slip Details for <br><span class="deptname_show"></span></h4>
                    <i  style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times instclose_btn"></i>                  
                </div>
                <br>
                    <div style="width:95%;margin:0 auto;" class="datatables">

             
                <div class="table-responsive" id="tableshow">
                    <table id="CommencedinstituteTable"
                        class="table w-100  display  table-bordered text-nowrap datatables-basic schedulingtable">
                        <thead>
                            @csrf
                            <tr>
                                <th class="text-wrap">Sl.<br>No</th>
                                <th class="text-wrap">Institution Name</th>
                                <th class="text-wrap">Team Members</th>
                                <th style="width:5% !important;">Man<br>Days</th>
                                <th class="text-wrap">Region</th>
                                <th class="text-wrap">District</th>
                                <th class="text-wrap">From Date</th>
                                <th class="text-wrap">To Date</th>
                                <th class="text-wrap">Entry Meeting</th>
                                <th class="text-wrap">Exit Meeting</th>
                                <th class="text-wrap">Total Slip(s)</th>
                                <th class="text-wrap">Pending Slip(s)</th>
                                <th class="text-wrap">Converted to Para(s)</th>
                                <th class="text-wrap">Dropped Slip(s)</th>
                                <th class="text-wrap">Status</th>

                            </tr>
                        </thead>
                       
                    </table>
                </div>
            </div>
            <br>
</div>

<div id="institutewisetable" class="card hide_this mx-auto institutewisetable" style="width: 95%;border-color:grey !important;">


                <div class="card-header lang" style="background-color:white;">                    
                    <h4 class="text-center mb-0 fs-7 fw-bolder"> Institute wise Audit Plan Details for <br><span class="deptname_show"></span></h4>
                    <i  style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times instclose_btn"></i>                  
                </div>
                <br>
                    <div style="width:95%;margin:0 auto;" class="datatables">

             
                <div class="table-responsive" id="tableshow">
                    <table id="instituteTable"
                        class="table w-100  display  table-bordered text-nowrap datatables-basic schedulingtable">
                        <thead>
                            @csrf
                            <tr>
                                <th >Sl.<br>No</th>
                                <th >Institution Name</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th >Team Members</th>
                                <th style="width:5% !important;">Man<br>Days</th>
                                <th >Region</th>
                                <th >District</th>
                                <th class="text-wrap">From Date</th>
                                <th class="text-wrap">To Date</th>
                                <th >Schedule Status</th>
                                <th >Intimation Status</th>
                                <th >Work Allocation</th>
                                <th >Entry Meeting</th>
                                <th >Exit Meeting</th>
                               <!-- <th >Status of Field Audit</th>-->
                               <!-- <th >Slip Status</th>-->
                            </tr>
                        </thead>
                       
                    </table>
                </div>
            </div>
            <br>
</div>
<br>
 <div class="card mx-auto  hide_this" id="slipview_Details" style="width: 95%;border-color:grey !important;">
           
            <div class="card-header lang" style="background-color:white;">                    
                    <h4 class="text-center mb-0 fs-7 fw-bolder">Audit Slip Details of <span class="slipshowinstname"></span></h4>
                    <i  style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times slipclose_btn"></i>                  
            </div>
            <div class="card-body">

                <br>
                <div class="cardforslips">
                    <!--<div style="width:80%;margin:0 auto;" class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-file-stack fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_total" class="card-title mb-1">0</h4>
                                            <p class="card-subtitle">Total <br>Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-success">
                                            <i class="ti ti-swipe fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_dropped" class="card-title mb-1">0</h4>
                                            <p class="card-subtitle">Dropped Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-danger">
                                            <i class="ti ti-transform fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_converted" class="card-title mb-1">3</h4>
                                            <p class="card-subtitle">Converted to Paras</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip">
                                <div class="card-body">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div
                                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-warning">
                                            <i class="ti ti-clipboard-text fs-6"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_pending" class="card-title mb-1">3</h4>
                                            <p class="card-subtitle">Pending Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                     <hr style="border-top: var(--bs-border-width) solid #ebf1f6 !important;"> 
                    <div style="width:50%;margin:0 auto;" class="slipdiv">
                        <br>
                        <input type="hidden" id="instschid_hidden" />
                        <input type="hidden" id="instname" />
                        <div class="row ">
                            <div class="col-md-4 mb-3"> <label class="form-label required"
                                    for="validationDefault01">Quarter</label>
                                <select class="form-select mr-sm-2" id="quartercode" name="quartercode">
                                    <option value='all'>All</option>
                                    <option value="Q1">Quarter 1</option>
                                    <option value="Q2">Quarter 2</option>
                                    <option value="Q3">Quarter 3</option>
                                    <option value="Q4">Quarter 4</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3"> <label class="form-label required"
                                    for="validationDefault01">Select
                                    Slip Status</label>
                                <select class="form-select mr-sm-2" id="slipsts" name="slipsts">
                                    <option value='all'>All</option>
                                    <option value="P">Pending Slips</option>
                                    <option value="A">Dropped Slips</option>
                                    <option value="X">Converted to Paras</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success lang btn-md" key="" type="submit"
                                    action="insert" onclick="filterslip()" id="buttonaction"
                                    style="margin-top: 1.8rem !important;" name="buttonaction">Show Details</button>
                            </div>

                        </div>
                    </div>-->
                    <div style="width:95%;margin:0 auto;" class="datatables">
                        <div class="table-responsive hide_this usertable_detail_wrapper" id="Sliptableshow">
                            <table id="slipdetails_Table"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th>Slip No</th>
                                        <th>Objection</th>
                                        <th>Team Head</th>
                                        <th>Auditor Name</th>
                                        <th>Slip Created On</th>
                                        <!--<th>Rejoinder</th>-->
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id='no_data_details' class='hide_this'>
                    <center>No Data Available</center>
                </div>
            </div>
            <br>
</div>


<div class="modal fade" id="HistoryModel" tabindex="-1" aria-labelledby="HistoryModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;">Flow of Slip No <b id="slipnodyn"></b></h4>

                <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div class="datatables">
                        <div class="table-responsive" id="tableshow">
                            <table id="slipHistoryTable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th>Forwarded By</th>
                                        <th>Forwarded To</th>
                                        <th>Slip Status</th>
                                        <th>Forwarded On</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='his_no_data_details' class='hide_this' style="border:1px solid #ddd;padding:10px;">
                        <center>No Data Available</center>
                    </div>
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <!-- Button container with flexbox for centering -->
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="ViewSlipModel" tabindex="-1" aria-labelledby="ViewSlipModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;font-weight:600;">Slip Details of <span
                        class="slipnodyn"></span></h4>

                <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div>

                        <div class="table-container" id="auditsliptable">
                        </div>
                        <div class="liabilitydetails">
                            <h5>
                                <center><b>Liability Details</b></center>
                            </h5>
                            <table id="liabilitiesTable" class="auditor-table">
                                <thead>

                                    <tr>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Designation</th>
                                        <th>Amount Involved</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="auditorremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditor Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Name</th>
                                            <td class="auditorname"></td>
                                        </tr>


                                    </tbody>
                                </table>
                                <div class="accordion" id="auditor">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="auditor_head">
                                            <button class="accordion-button bg-primary-subtle   collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#auditor_acc" aria-expanded="false"
                                                aria-controls="collapseOne">
                                                <b>Auditor Observation /Remarks</b>
                                            </button>
                                        </h2>
                                        <div id="auditor_acc" class="accordion-collapse collapse"
                                            aria-labelledby="auditor_head" data-bs-parent="#auditor">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <label class="form-label lang" for="validationDefaultUsername"
                                                            key="observation">Auditor Remarks</label>
                                                        <textarea id="viewslip_auditorremarkscccz" class="form-control" placeholder="Enter remarks"
                                                            name="viewslip_auditorremarks"></textarea>

                                                    </div>
                                                    <!--<div class="col-md-4">

                                                                                                                                <label class="form-label required"
                                                                                                                                    for="validationDefaultUsername">Auditor
                                                                                                                                    Attachment</label>
                                                                                                                                <div class="container my-1"
                                                                                                                                    id="viewslip_auditorcontainer"></div>

                                                                                                                            </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auditeeremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditee Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditee Name</th>
                                            <td class="auditeename"></td>
                                        </tr>

                                    </tbody>
                                </table>

                                <div class="accordion mt-3" id="auditee">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button bg-primary-subtle collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                <b>Auditee Reply</b>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#auditee">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label lang"
                                                            for="validationDefaultUsername">Auditee
                                                            Reply</label>

                                                        <textarea id="viewslip_auditeeremarks" class="form-control" placeholder="Enter remarks"
                                                            name="viewslip_auditeeremarks"></textarea>

                                                    </div>
                                                    <!--<div class="col-md-4">
                                                                                                                                <label class="form-label required"
                                                                                                                                    for="validationDefaultUsername">Auditee
                                                                                                                                    Attachment</label>


                                                                                                                                <div class="container my-1"
                                                                                                                                    id="viewslip_auditeecontainer"></div>


                                                                                                                            </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="auditorreplydiv" style="display:none;">
                        <br>
                        <div class=""
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Auditor Reply</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Reply</th>
                                            <td class="auditoreply_remarks"></td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="teamheaddiv" style="display:none;">
                        <br>
                        <div class="teamheaddiv"
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Team Head Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Team Head Name</th>
                                            <td class="teamheadname"></td>
                                        </tr>
                                        <tr>
                                            <th>Team Head Final Remarks</th>
                                            <td class="finalremarks"></td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <!-- Button container with flexbox for centering -->
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>



<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

{{-- SET search_path TO audit;
SELECT audit.fn_getdashboarddescription(
'A',   -- _deptcode (Replace with actual values)
'A',   -- _regioncode
'A',   -- _distcode
0,     -- _sessionuserid
'A',   -- _sessionusertypecode
'A',   -- _sessionroletypecode
0,     -- _auditscheduleid
'allslip' -- _description ()
); --}}

<div class="modal fade" id="liabilityModal" role="dialog" aria-labelledby="liabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center  justify-content-center">
                <h5 class="modal-title" id="liabilityModalLabel">Liability Details</h5>
                <!-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body" id="liabilityModalContent" style="max-height: 70vh; width: 100%; overflow: hidden; padding: 10px;">
                <!-- Liability details will be loaded here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- Buttons extension -->
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>
<script src="../assets/js/apps/fixed-table-header.js"></script>

<script src="../assets/js/datatable/datatable-datetimesearch.js"></script>
<script src="../assets/js/datatable/sorting-search.js"></script>


<script>






    // Define globalData outside of functions to make it accessible globally
    let globalData = [];

    function updateTable(data, description) {
        globalData = data;

        // Destroy existing DataTable instance if exists
        if ($.fn.DataTable.isDataTable("#dataTable")) {
            $('#dataTable').DataTable().clear().destroy();
        }

        let tabValue = 0;
        if ($('#scehduled_plans_tab').hasClass('active')) {
            tabValue = 1;
        } else if ($('#slip_details_tab').hasClass('active')) {
            tabValue = 2;
        } else if ($('#all-slip-tab').hasClass('active')) {
            tabValue = 3;
        }

        let headerHTML = ''; // Empty variable to store the dynamic header

        // Check the description and set columns accordingly
        if (description === 'scheduledinst') {
            headerHTML = `
                <th class="text-center" style="width: 3%;">S.No</th>
                <th class="text-center">Institute Name</th>
                <th class="text-center w-20">Act As<br> Team Head / Team Member</th>
                <th class="text-center">From Date</th>
                <th class="text-center">To Date</th>
            `;
        } else {
            headerHTML = `
                <th class="text-center" style="width: 2%;">S.No</th>
                <th class="text-center">Objection Title</th>
                <th class="text-center">Amount Involved</th>
                <th class="text-center">Severity</th>
            `;

            // Only add the "Created By" column if the third tab is active
            if (tabValue === 3) {
                headerHTML += `<th class="text-center">Created By</th>`;
            }

            headerHTML += `
                <th class="text-center">Liability</th>
                <th class="text-center">Status</th>
                <th class="text-center">Pending At</th>
            `;

            // Add Liability Details column if liability is 'Y'
            headerHTML += `<th class="text-center">Liability Details</th>`;
        }

        // Dynamically update the table header
        $('#tableHeader').html(headerHTML);

        // Populate the table body with data
        $('#dataTable tbody').empty();

        if (Array.isArray(data) && data.length > 0) {
            let serialNumber = 1;

            const formatValue = (value) => (value === null || value === undefined || value === '') ? "-" : value;
            const formatLiability = (liability) => liability === "N" ? "No" : liability === "Y" ? "Yes" : "-";
            const formatProcessName = (processelname) => processelname === "Converted to para" ?
                '<button class="btn btn-secondary btn-sm rounded-24 w-100">Converted to para</button>' :
                processelname === "Dropped" ?
                '<button class="btn btn-success btn-sm rounded-24 w-100">Dropped</button>' :
                '<button class="btn btn-danger btn-sm rounded-24 w-100">Pending</button>';

            const formatAuditteam = (auditteamhead) => auditteamhead === "Y" ? "Team Head" : "Team Member";
            const formatDate = (dateString) => {
                if (!dateString) return "-";
                const date = new Date(dateString);
                return `${("0" + date.getDate()).slice(-2)}-${("0" + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
            };

            data.forEach(row => {
                let rowData = `<tr>`;
                if (description === 'scheduledinst') {
                    rowData += `
                        <td class='text-end' style='min-width: 2px;'>${serialNumber++}</td>
                        <td>${formatValue(row.instename)}</td>
                        <td class='text-center'>${formatAuditteam(row.auditteamhead)}</td>
                        <td class='text-center'>${formatDate(row.fromdate)}</td>
                        <td class='text-center'>${formatDate(row.todate)}</td>
                    `;
                } else {
                    rowData += `
                        <td class="text-end" style="min-width: 2px;">${serialNumber++}</td>
                        <td class='text-break'>${formatValue(row.objectionename)}</td>
                        <td class='text-center'>${formatValue(row.amtinvolved)}</td>
                        <td>${formatValue(row.severityelname)}</td>
                    `;

                    // Only include "Created By" column if the third tab is active
                    if (tabValue === 3) {
                        rowData += `<td>${formatValue(row.createdby)}</td>`;
                    }

                    rowData += `
                        <td class="text-center">${formatLiability(row.liability)}</td>
                        <td>${formatProcessName(row.processelname)}</td>
                    `;

                    rowData += `
                        <td class='text-break'>${formatValue(row.forwardto)}</td>
                    `;

                    // Display liability details if liability is 'Y'
                    if (row.liability === 'Y' && Array.isArray(row.liabilitydetails) && row.liabilitydetails.length > 0) {
                        const buttonHTML = `<button class="btn btn-info btn-sm" onclick="showLiabilityDetails(globalData, ${row.auditslipid})">View Details</button>`;

                        rowData += `<td class="text-center">${buttonHTML}</td>`;
                    } else {
                        rowData += `<td class="text-center">-</td>`;
                    }
                }
                rowData += `</tr>`;
                $('#dataTable tbody').append(rowData);
                $('#dataTableContainer').show();
            });
        } else {
            $('#dataTable tbody').append('<tr><td colspan="9" align="center">No Data Available</td></tr>');
        }

        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                "searching": true,
                "paging": false,
                "scrollX": false,
                "info": false,
                "order": [
                    [0, 'asc']
                ],
                "columnDefs": [{
                    "targets": '_all',
                    "orderable": true
                }]
            });
        });
    }

    function showLiabilityDetails(data, auditslipid) {
    // Find the row based on the auditslipid
    const row = data.find(item => item.auditslipid === auditslipid);

    // Check if the row exists
    if (!row) {
        $('#liabilityModalContent').html("<p>No liability details available for this audit slip.</p>");
        $('#liabilityModal').modal('show');
        return;
    }

    let liabilityDetailsHTML = "<table class='table table-bordered'>";

    // Check if the row has liability details
    if (Array.isArray(row.liabilitydetails) && row.liabilitydetails.length > 0) {
        // Adding table header
        liabilityDetailsHTML += `
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Number</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        // Iterate through liability details and display them in table rows
        row.liabilitydetails.forEach(detail => {
            liabilityDetailsHTML += `
                <tr>
                    <td>${detail.liabilitytype}</td>
                    <td>${detail.liabilityname}</td>
                    <td>${detail.liabilitydesignation}</td>
                    <td>${detail.liabilitynumber}</td>
                    <td>${detail.liabilityamount}</td>
                </tr>
            `;
        });

        liabilityDetailsHTML += `</tbody>`;
    } else {
        liabilityDetailsHTML = "<p>No liability details available for this audit slip.</p>";
    }

    // Update the modal content with the table
    $('#liabilityModalContent').html(liabilityDetailsHTML);
    // Show the modal
    $('#liabilityModal').modal('show');
}
/*allocated plans start */
hideAllTables();
function hideAllTables() {
    $('.institutewisetable, .districtwisetable, .regionwisetable').hide();
    $('#slipview_Details').addClass('hide_this');

}

 
$('.nav-link').on('click', function() {
    hideAllTables();
});





$('.regwiseclose_btn').on('click', function() {
    hideAllTables();
});

$('.distwiseclose_btn').on('click', function() {
    hideAllTables();
});

$('.instclose_btn').on('click', function() {
    $('.institutewisetable').hide();
    $('#slipview_Details').addClass('hide_this');

});

$('.slipclose_btn').on('click', function() {
    //$('#slipview_Details').hide();
    $('#slipview_Details').addClass('hide_this');

});






/*** load deptwise table for both consolidated plan details and consolidated slip details - START*/
$(document).ready(function () 
{
    $('.deptwisetable').removeClass('hide_this');

    // initDeptwiseDataTable('plantabform', 'deptwisedata_table'); 

    // initDeptwiseSlipDataTable('sliptabform', 'Slip_deptwisedata_table'); 

    
  const defaultQuarter = $('select[name="quarter"]').val();

    const select_quarter = $('select[name="slipquarter"]').val();

    initDeptwiseDataTable(defaultQuarter, 'plantabform', 'deptwisedata_table');

    initDeptwiseSlipDataTable(select_quarter,'sliptabform', 'Slip_deptwisedata_table'); 
});


function initDeptwiseDataTable(selectedQuarters,sourceForm = '', id) {
    setTimeout(() => {
        const showAllocColumn = sourceForm; 
          $('.districtwisetable').hide();
             $('.institutewisetable').hide();
         $('.regionwisetable').hide();


        var titleexcel ='Department wise Audit Plan Details';
        $(`#${id}`).DataTable({
            processing: true,
            serverSide: false,
            destroy: true,
            dom: 'Brftip',
            pagingType: 'simple_numbers', // Prev, page numbers, and Next (no First/Last)
            language: {
                    paginate: {
                        next: 'Next',
                        previous: 'Prev'
                    }
            },
            buttons: [createExcelExportButton(titleexcel)],
            initComplete: function(settings, json) {
                $(`#${id}`).wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            ajax: {
                url: '{{ route("deptwisedata.get") }}',
                type: 'GET',
                dataSrc: 'data',
                data: {
                    source_form: sourceForm,
                    quarter: selectedQuarters
                }
            },
            columns: [
                {
                    data: null,
                    className: "d-none d-md-table-cell text-wrap text-center",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'deptname',
                    className: "text-left" 
                },
               {
                    data: 'regioncount',
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white regioncountbased" 
                                        style="cursor:pointer;"
                                        data-sourceform="${sourceForm}"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        <b>${data}</b>
                                    </span>`;
                        }
                        return data; // for sorting, use plain number
                    }
                },
                {
                    data: 'distcount',
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white distcountbased" 
                                        style="cursor:pointer;"
                                        data-sourceform="${sourceForm}"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        <b>${data}</b>
                                    </span>`;
                        }
                        return data;
                    }
                },
                {
                    data: 'alloc_inscount',
                    className: "text-wrap text-right", 
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white allocinstitute" 
                                style="cursor:pointer;"
                                data-sourceform="${sourceForm}"
                                data-deptname="${row.deptname}" 
                                data-deptcode="${row.deptCode}" 
                                data-regioncode="${row.regionCode}" 
                                data-distcode="${row.distCode}">
                                <b>${data}</b>
                            </span>`;
                        }
                        return data;
                    }
                }
               
                
            ],
           footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            const intVal = (i) => typeof i === 'string' ? parseInt(i.replace(/[^0-9]/g, '')) || 0 : (typeof i === 'number' ? i : 0);

            let totalRegions = api.column(2).data().reduce((a, b) => intVal(a) + intVal(b), 0);
            let totalDistricts = api.column(3).data().reduce((a, b) => intVal(a) + intVal(b), 0);
            let totalScheduled = api.column(4).data().reduce((a, b) => intVal(a) + intVal(b), 0);

            // Format footer with same badge styling as data rows
            $(api.column(2).footer()).html(`<span  style="font-size:13px;"><b>${totalRegions}</b></span>`);
           /* $(api.column(3).footer()).html(
                `<span  class="badge bg-success text-white distcountbased" 
                        style="font-size:13px; cursor:pointer;"
                        data-sourceform="${sourceForm}"
                        data-deptname="All Departments"
                        data-deptcode=""
                        data-regioncode=""
                        data-distcode=""]>
                        <b>${totalDistricts}</b></span>`);*/
            $(api.column(3).footer()).html(totalDistricts);
            $(api.column(4).footer()).html(totalScheduled);
            /*$(api.column(4).footer()).html(
                `<span class="badge bg-success text-white allocinstitute" 
                    style="font-size:13px; cursor:pointer;"
                    data-sourceform="${sourceForm}"
                    data-deptname="All Departments"
                    data-deptcode=""
                    data-regioncode=""
                    data-distcode="">
                    <b>${totalScheduled}</b>
                </span>`
            );*/
        }

        });
    }, 100);
}

function initDeptwiseSlipDataTable(select_quarter,sourceForm = '', id) {
    setTimeout(() => {
        const showAllocColumn = sourceForm; 
        var titleexcel ='Department wise Audit Slip Details';
          $('.districtwisetable').hide();
             $('.institutewisetable').hide();
         $('.regionwisetable').hide();
             $('#slipview_Details').addClass('hide_this');

        $(`#${id}`).DataTable({
            processing: true,
            serverSide: false,
            destroy: true,
            pagingType: 'simple_numbers', // Prev, page numbers, and Next (no First/Last)
            language: {
                    paginate: {
                        next: 'Next',
                        previous: 'Prev'
                    }
            },
            dom: 'Brftip',
            buttons: [createExcelExportButton(titleexcel)],
            initComplete: function(settings, json) {
                $(`#${id}`).wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            ajax: {
                url: '{{ route("deptwisedata.get") }}',
                type: 'GET',
                dataSrc: 'data',
                data: {
                    source_form: sourceForm,
                    quarterslip : select_quarter
                }
            },
            columns: [
                {
                    data: null,
                    className: "d-none d-md-table-cell  text-center align-middle",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'deptname',                    
                    className: "text-left" 
                },
                {
                    data: 'regioncount',
                    className: "text-wrap text-right", 
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white regioncountbased" 
                                style="cursor:pointer;"
                                data-sourceform="${sourceForm}"
                                data-deptname="${row.deptname}" 
                                data-deptcode="${row.deptCode}" 
                                data-regioncode="${row.regionCode}" 
                                data-distcode="${row.distCode}">
                                <b>${data}</b>
                            </span>`;
                        }
                        return data;

                    }
                },
                {
                    data: 'distcount',
                    className: "text-wrap text-right", 
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white distcountbased" 
                                style="cursor:pointer;"
                                data-sourceform="${sourceForm}"
                                data-deptname="${row.deptname}" 
                                data-deptcode="${row.deptCode}" 
                                data-regioncode="${row.regionCode}" 
                                data-distcode="${row.distCode}">
                                <b>${data}</b>
                            </span>`;
                        }
                        return data;
                    }
                },
                {
                    data: 'alloc_inscount',
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `<span class="badge bg-success text-bold text-white allocinstitute" 
                                style="cursor:pointer;"
                                data-sourceform="${sourceForm}"
                                data-deptname="${row.deptname}" 
                                data-deptcode="${row.deptCode}" 
                                data-regioncode="${row.regionCode}" 
                                data-distcode="${row.distCode}">
                                <b>${data}</b>
                            </span>`;
                        }

                        return data;

                    }
                   
                },
                {
                    data: 'totalslips',  
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const isPositive = parseInt(row.totalslips) > 0;
                            const badgeClass = isPositive ? 'bg-info regioncountbased' : '';
                            const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';

                            return `<span class="badge ${badgeClass}" 
                                        style="${badgeStyle}"
                                        data-sourceform="totalslip"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        ${row.totalslips}
                                    </span>`;
                        }

                        return data;
                    }
                },
                {
                    data: 'pendingslipcount',
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const isPositive = parseInt(row.pendingslipcount) > 0;
                            const badgeClass = isPositive ? 'bg-info regioncountbased' : '';
                            const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';

                            return `<span class="badge ${badgeClass}" 
                                        style="${badgeStyle}"
                                        data-sourceform="pendingslip"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        ${row.pendingslipcount}
                                    </span>`;
                        }

                        return data;

                    }
                },

                {
                     data: 'convertedslipcount',
                     className: "text-wrap text-right",
                     render: function (data, type, row) {
                        if (type === 'display') {
                            const isPositive = parseInt(row.convertedslipcount) > 0;
                            const badgeClass = isPositive ? 'bg-info regioncountbased' : '';
                            const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';

                            return `<span class="badge ${badgeClass}" 
                                        style="${badgeStyle}"
                                        data-sourceform="convertedslip"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        ${row.convertedslipcount}
                                    </span>`;

                        }
                        return data;
                    }
                },
                { 
                    data: 'droppedslipcount',
                    className: "text-wrap text-right",
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const isPositive = parseInt(row.droppedslipcount) > 0;
                            const badgeClass = isPositive ? 'bg-info regioncountbased' : '';
                            const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';

                            return `<span class="badge ${badgeClass}" 
                                        style="${badgeStyle}"
                                        data-sourceform="droppedslip"
                                        data-deptname="${row.deptname}" 
                                        data-deptcode="${row.deptCode}" 
                                        data-regioncode="${row.regionCode}" 
                                        data-distcode="${row.distCode}">
                                        ${row.droppedslipcount}
                                    </span>`;
                        }
                        return data;
                    }
                }
               
                
            ],
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();
                const intVal = (i) => typeof i === 'string' ? parseInt(i.replace(/[^0-9]/g, '')) || 0 : (typeof i === 'number' ? i : 0);

                let totalRegions = api.column(2).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let totalDistricts = api.column(3).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let totalScheduled = api.column(4).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let totalslips = api.column(5).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let Pendingslips = api.column(6).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let Convertedslips = api.column(7).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                let Droppedslips = api.column(8).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                $(api.column(2).footer()).html(totalRegions);
                $(api.column(3).footer()).html(totalDistricts);
                $(api.column(4).footer()).html(totalScheduled);
                /* $(api.column(4).footer()).html(
                `<span class="badge bg-success text-white allocinstitute" 
                    style="font-size:13px; cursor:pointer;"
                    data-sourceform="${sourceForm}"
                    data-deptname="All Departments"
                    data-deptcode=""
                    data-regioncode=""
                    data-distcode="">
                    <b>${totalScheduled}</b>
                </span>`
            );*/
               /* $(api.column(5).footer()).html( `<span class="badge bg-success text-white allocinstitute" 
                    style="font-size:13px; cursor:pointer;"
                    data-sourceform="totalslip"
                    data-deptname="All Departments"
                    data-deptcode=""
                    data-regioncode=""
                    data-distcode="">
                    <b>${totalslips}</b>
                </span>`);*/
                $(api.column(5).footer()).html(totalslips);
                $(api.column(6).footer()).html(Pendingslips);
                $(api.column(7).footer()).html(Convertedslips);
                $(api.column(8).footer()).html(Droppedslips);


            }
        });
    }, 100);
}
/*** load deptwise table for both consolidated plan details and consolidated slip details - END*/

/*** load regionwise table for both consolidated plan details and consolidated slip details - START*/
$(document).on('click', '.regioncountbased', function () {
        
    var deptCode = $(this).data('deptcode');
    var regionCode = $(this).data('regioncode');
    var distCode = $(this).data('distcode');
    var deptname = $(this).data('deptname');
    var sourceformdata = $(this).data('sourceform');
    var plantab = $('select[name="quarter"]').val();
    var sliptab = $('select[name="slipquarter"]').val();
    var sourceformpost='';
    var slipHeaderText='';

    if (sourceformdata === 'pendingslip') {
            slipHeaderText = 'Pending Slip(s)';
        } else if (sourceformdata === 'droppedslip') {
            slipHeaderText = 'Dropped Slip(s)';
        } else if (sourceformdata === 'totalslip') {
            slipHeaderText = 'Total Slip(s)';
        } else if (sourceformdata === 'convertedslip') {
            slipHeaderText = 'Converted to Para(s)';
        }

    if(sourceformdata !=='plantabform')
    {
        sourceformpost = 'sliptabform';
        defaultQuarter = sliptab;
    }else
    {
        sourceformpost = 'plantabform';
        defaultQuarter =plantab;
    }

    let dynheading = '';

    if (sourceformdata === 'sliptabform') {
        dynheading = 'Region wise Audit Slip Details for <br>'+deptname;
    } else if (sourceformdata === 'plantabform') {
        dynheading = 'Region wise Audit Plan Details for <br>'+deptname;
    } else {
        dynheading = 'Statistical Details on '+slipHeaderText+' of <br>'+deptname;
    }

    $('.institutewisetable').hide();
    $('.districtwisetable').hide();
    $('#slipview_Details').addClass('hide_this');

    $.ajax({
            url: "load_regiondata",
            type: "POST",
            data: { deptCode: deptCode,regionCode:regionCode,distCode:distCode,sourceform:sourceformpost,quarter:defaultQuarter},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                renderRegionTable(response.data, deptCode, deptname,sourceformdata);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Failed to fetch details.");
            }
    });

    $('.regionwisetable').show();
    $('.deptname_show_Reg').html(dynheading);
    $('html, body').animate({
            scrollTop: $('.regionwisetable').offset().top
    }, 500);
});

function renderRegionTable(data, deptCode, deptname, sourceForm = '') {
    const tableElement = $('#RegionTable');

    // Destroy existing DataTable instance FIRST
    if ($.fn.DataTable.isDataTable(tableElement)) {
        tableElement.DataTable().destroy();
    }

    // Dynamically change thead and tfoot based on sourceForm
    let slipHeaderText = '';
    if (sourceForm === 'pendingslip') {
        slipHeaderText = 'Pending Slip(s)';
    } else if (sourceForm === 'droppedslip') {
        slipHeaderText = 'Dropped Slip(s)';
    } else if (sourceForm === 'totalslip') {
        slipHeaderText = 'Total Slip(s)';
    } else if (sourceForm === 'convertedslip') {
        slipHeaderText = 'Converted to Para(s)';
    }

    const theadHTML = `
        <tr>
            <th style="width:5% !important;">Sl. No</th>
            <th style="width:50% !important;text-align:left !important;">Name of the Region</th>
            <th style="width:15% !important;">Total No.of Audit District</th>
            <th style="width:15% !important;text-align:center !important;">${sourceForm === 'plantabform' ? 'Total No.of Auditable Institutions' : 'Total No.of Institutions Audit Commenced'}</th>
            <th style="width:15% !important;text-align:center !important;" class="${slipHeaderText ? '' : 'd-none'}">${slipHeaderText}</th>
        </tr>`;

    const tfootHTML = `
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th id="totalDistricts" style="text-align:center !important;"></th>
                <th id="totalInstitutions"></th>
                <th id="totalSlips" class="${slipHeaderText ? '' : 'd-none'}"></th>
            </tr>
        </tfoot>`;

    // Inject thead and tfoot AFTER destroy
    tableElement.find('thead').html(theadHTML);
    tableElement.find('tfoot').remove(); // ensure no duplicates
    tableElement.append(tfootHTML);

    // Set title for Excel export
    let titleexcel = '';
    if (sourceForm === 'sliptabform') {
        titleexcel = 'Region wise Audit Slip Details for ' + deptname;
    } else if (sourceForm === 'plantabform') {
        titleexcel = 'Region wise Audit Plan Details for ' + deptname;
    } else {
        titleexcel = 'Statistical Details on ' + slipHeaderText + ' of ' + deptname;
    }

    // Determine the data field for slips
    let slipDataField = 'unused';
    if (['totalslip', 'pendingslip', 'convertedslip', 'droppedslip'].includes(sourceForm)) {
        if (sourceForm === 'pendingslip') {
            slipDataField = 'pendingslipcount';
        } else if (sourceForm === 'convertedslip') {
            slipDataField = 'convertedslipcount';
        } else if (sourceForm === 'droppedslip') {
            slipDataField = 'droppedslipcount';
        } else {
            slipDataField = 'totalslips';
        }
    }

    // Initialize DataTable
    tableElement.DataTable({
        data: data,
        dom: 'Brftip',
        pagingType: 'simple_numbers', // Prev, page numbers, and Next (no First/Last)
        language: {
                paginate: {
                    next: 'Next',
                    previous: 'Prev'
                }
        },
        buttons: [createExcelExportButton(titleexcel)],
        initComplete: function () {
            $("#RegionTable").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            const dt = this.api();

            const showSlipsColumn = ['totalslip', 'pendingslip', 'droppedslip', 'convertedslip'].includes(sourceForm);
            dt.column('totalslips:name').visible(showSlipsColumn);
            dt.column(4).visible(showSlipsColumn); // Assuming column index 4 is slip

            if (!showSlipsColumn) {
                $('#totalSlips').hide();
            } else {
                $('#totalSlips').show();
            }
        },
        columns: [
            {
                data: null,
                className: "d-none d-md-table-cell text-wrap text-center align-middle",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'regionename',
                className: "d-none d-md-table-cell text-wrap text-left"
            },
            {
                data: 'distcount',
                className: "text-center text-wrap"
            },
            {
                data: 'alloc_inscount',
                className: "text-right text-wrap",
                render: function (data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-success text-bold text-white allocinstitute' : '';
                        const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        return `<span class="badge ${badgeClass}" 
                                    data-fromtab='regparent'
                                    data-sourceform="${sourceForm === 'plantabform' ? 'plantabform' : 'sliptabform'}"
                                    data-regionname="${row.regionename}" 
                                    data-deptname="${deptname}" 
                                    data-deptcode="${deptCode}" 
                                    data-regioncode="${row.regioncode}" 
                                    data-distcode="${row.distcode}" 
                                    style="${badgeStyle}"><b>${data}</b></span>`;

                    }
                    return data;
                }
            },
            {
                    data: slipDataField,
                    name: 'totalslips', // <-- Add this line to use it in .column('totalslips:name')
                    className: "text-right text-wrap",
                    visible: false, // hide it initially
                    defaultContent: '', // <--- prevents error if data field is missing
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const isPositive = parseInt(data) > 0;
                            const badgeClass = isPositive ? 'bg-info allocinstitute' : '';
                            const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                            
                            return `<span class="badge ${badgeClass}" 
                                        style="${badgeStyle}"
                                        data-whichslip="${slipDataField}"
                                        data-sourceform="sliptabform"
                                        data-fromtab="regparent"
                                        data-regionname="${row.regionename}" 
                                        data-deptname="${deptname}" 
                                        data-deptcode="${deptCode}" 
                                        data-regioncode="${row.regioncode}" 
                                        data-distcode="${row.distcode}">
                                        ${data}
                                    </span>`;
                        }
                        return data;
                    }




            }
        ],
        footerCallback: function (row, data, start, end, display) {
            const api = this.api();
            const parse = val => parseInt(String(val).replace(/[^0-9]/g, '')) || 0;

            // ✅ Use full dataset, not just current page
            const totalDistricts = api.column(2).data().reduce((a, b) => parse(a) + parse(b), 0);
            const totalInstitutions = api.column(3).data().reduce((a, b) => parse(a) + parse(b), 0);
            const totalSlips = slipDataField
                ? api.column(4).data().reduce((a, b) => parse(a) + parse(b), 0)
                : '';

            // ✅ Inject totals directly into footer cells
            $(api.column(2).footer()).html(totalDistricts);
            $(api.column(3).footer()).html(totalInstitutions);
            if (slipDataField) {
                $(api.column(4).footer()).html(totalSlips);
            }
        }

    });
}

/*** load regionwise table for both consolidated plan details and consolidated slip details - END*/


/*** load Districtwise table for both consolidated plan details and consolidated slip details - START*/
$(document).on('click', '.distcountbased', function () {
        
    var deptCode = $(this).data('deptcode');
    var regionCode = $(this).data('regioncode');
    var distCode = $(this).data('distcode');
    var deptname = $(this).data('deptname');

    var sourceform = $(this).data('sourceform');
    var plantab = $('select[name="quarter"]').val();
    var sliptab = $('select[name="slipquarter"]').val();

    const dynheading = (sourceform === 'sliptabform') ? 'District wise Audit Slip Details for ' : 'District wise Audit Plan Details for ';

     if(sourceform !=='plantabform')
    {
        defaultQuarter = sliptab;
    }else
    {
        defaultQuarter =plantab;
    }

    $('.institutewisetable, .regionwisetable').hide();
    $('#slipview_Details').addClass('hide_this');

    $.ajax({
        url: "load_districtdata",
        type: "POST",
        data: { deptCode: deptCode,regionCode:regionCode,distCode:distCode,sourceform:sourceform,quarter:defaultQuarter },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            renderDistrictTable(response.data, deptCode, deptname,sourceform);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Failed to fetch details.");
        }
    });

    $('.districtwisetable').show();
    $('.deptname_show_Reg').html(dynheading+ ' <br> '+deptname);
    $('html, body').animate({
        scrollTop: $('#districtwisetable').offset().top
    }, 500);

});

function renderDistrictTable(data, deptCode, deptname, sourceForm) {
    const tableElement = $('#DistrictTable');

    // Define thead dynamically
    let theadHTML = '';
    if (sourceForm === 'sliptabform') {
        theadHTML = `
            <tr>
                <th style="width:5% !important;">Sl. No</th>
                <th style="width:40% !important;text-align:left !important;">Name of the Region</th>
                <th style="width:40% !important;">Total No.of Audit District</th>
                <th style="width:15% !important;text-align:center !important;">Total No.of Institutions Audit Commenced</th>
            </tr>`;
    } else {
        theadHTML = `
            <tr>
                <th style="width:5% !important;">Sl. No</th>
                <th style="width:40% !important;text-align:left !important;">Name of the Region</th>
                <th style="width:40% !important;">Total No.of Audit District</th>
                <th style="width:15% !important;text-align:center !important;">Total No.of Auditable Institutions</th>
            </tr>`;
    }

    // Add tfoot
    const tfootHTML = `
        <tr>
            <th colspan="3" style="text-align:right;">Total</th>
            <th id="districtTotalInstitutions"></th>
        </tr>`;

    tableElement.find('thead').html(theadHTML);
    tableElement.find('tfoot').html(tfootHTML);

    // Destroy previous instance if exists
    if ($.fn.DataTable.isDataTable(tableElement)) {
        tableElement.DataTable().destroy();
    }

    const titleexcel = sourceForm === 'sliptabform'
        ? 'District wise Audit Slip Details for ' + deptname
        : 'District wise Audit Plan Details for ' + deptname;

    const table = tableElement.DataTable({
        data: data,
        dom: 'Brftip',
        pagingType: 'simple_numbers', // Prev, page numbers, and Next (no First/Last)
        language: {
                paginate: {
                    next: 'Next',
                    previous: 'Prev'
                }
        },
        buttons: [createExcelExportButton(titleexcel)],
        initComplete: function (settings, json) {
            $("#DistrictTable").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        columns: [
            {
                data: null,
                className: "d-none d-md-table-cell text-wrap text-center align-middle",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'regionename',
                className: "d-none d-md-table-cell text-wrap text-left"
            },
            {
                data: 'distename',
                className: "d-none d-md-table-cell text-wrap text-left"
            },
            {
                data: 'alloc_inscount',
                className: "text-right text-wrap",
                render: function (data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-success text-bold text-white allocinstitute' : '';
                        const badgeStyle = isPositive ? 'cursor:pointer;' : 'background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        return `<span class="badge ${badgeClass}" 
                                    style="${badgeStyle}"
                                    data-sourceform="${sourceForm}"
                                    data-fromtab='distparent'
                                    data-distname="${row.distename}"
                                    data-deptname="${deptname}" 
                                    data-deptcode="${deptCode}" 
                                    data-regioncode="${row.regioncode}" 
                                    data-distcode="${row.distcode}">
                                    <b>${data}</b>
                                </span>`;
                    }
                    return data;
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            const api = this.api();
            const parse = val => parseInt(String(val).replace(/[^0-9]/g, '')) || 0;

            const total = api
                .column(3) // alloc_inscount is at index 3
                .data()
                .reduce((a, b) => parse(a) + parse(b), 0);

            $(api.column(3).footer()).html(total);
        }
    });
}
    /***  LOAD DISTRICT TABLE END*/


    /***  LOAD INSTITUTION TABLE START*/

    $(document).on('click', '.allocinstitute', function () {
        var deptCode = $(this).data('deptcode');
        var regionCode = $(this).data('regioncode');
        var distCode = $(this).data('distcode');
        var deptname = $(this).data('deptname');
        var sourceform = $(this).data('sourceform');
        var fromtab=$(this).data('fromtab');
        var plantab = $('select[name="quarter"]').val();
        var sliptab = $('select[name="slipquarter"]').val();

        var whichslip =$(this).data('whichslip');
        if(!whichslip)
        {
            var whichslip ='notsliptab';


        }
        $('.deptname_show').text(deptname);

        if(fromtab == 'regparent')
        {
            $('.regionwisetable').show();
            var regionname = $(this).data('regionname');
            $('.deptname_show').text(deptname+','+regionname);

        }else
        {
            $('.regionwisetable').hide();

        }

        if(fromtab == 'distparent')
        {
            $('#districtwisetable').show();
            var distname = $(this).data('distname');
            $('.deptname_show').text(deptname+','+distname);

        }else
        {
            $('#districtwisetable').hide();

        }

        $('#slipview_Details').addClass('hide_this');

         if(sourceform ==='sliptabform')
            {
                defaultQuarter = sliptab;
            }else
            {
                defaultQuarter =plantab;
            }


        if(sourceform == 'sliptabform')
        {          
            LoadCommencedInstituteDetails(deptCode,regionCode,distCode,deptname,fromtab,whichslip,defaultQuarter);
        }else
        {      
            LoadAllocatedInstituteDetails(deptCode,regionCode,distCode,deptname,fromtab,defaultQuarter);
        }
       
    });

   $(document).on('click', '.sliponchange', function () {

       var auditscheduleid = $(this).data('auditscheduleid');
       var slipsts = $(this).data('slipstatus');
       var instname =$(this).data('instname');
       $('#slipview_Details').removeClass('hide_this');
       $('.slipshowinstname').html(instname);
        var sourceform = $(this).data('sourceform');
        var plantab = $('select[name="quarter"]').val();
        var sliptab = $('select[name="slipquarter"]').val();

          if(sourceform ==='sliptabform')
            {
                defaultQuarter = plantab;
            }else
            {
                defaultQuarter =sliptab;
            }

            
        $.ajax({
            url: "/getpendingparadetails",
            type: "POST",
            data: {
                auditscheduleid: auditscheduleid,
                quartercode:'all',
                slipsts:slipsts,
                filterapply: 'all',
                quarter : defaultQuarter
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Pass CSRF token in headers
            },
            success: function(data, textStatus, jqXHR) 
            {
                //console.log(data);return;
                 if (jqXHR.status === 200 && data.success) {
                    if (data.data && data.data) {
                        // Assuming your response contains the counts directly, like this:
                        // Update the counts in the HTML
                        $('#no_data_details').hide()

                      /*  $('#cnt_total').text(0);
                        $('#cnt_dropped').text(0);
                        $('#cnt_converted').text(0);
                        $('#cnt_pending').text(0);

                        var totalSlips = data.data.totalslips; // Example: 10
                        var droppedSlips = data.data.droppedslips; // Example: 4
                        var convertedSlips = data.data.convertedslips; // Example: 3
                        var pendingSlips = data.data.pendingSlips; // Example: 3
                        $('.cardforslips').show();
                        // Update the counts in the HTML
                        $('#cnt_total').text(totalSlips);
                        $('#cnt_dropped').text(droppedSlips);
                        $('#cnt_converted').text(convertedSlips);
                        $('#cnt_pending').text(pendingSlips);*/

                        // Proceed with your table population logic if needed
                        $('#slipdetails_Table tbody').empty();

                        // Check if data.data.original.data is an array and has elements
                        if (Array.isArray(data.data.data) && data.data.data.length > 0) {

                            // Show the table if there is data
                            $('#Sliptableshow').removeClass('hide_this');
                            $('#no_data_details').addClass('hide_this');
                            renderTable(data,instname);
                        } else {
                            // If no data is returned, show the "no data" message and hide the table
                            $('#slipdetails_Table tbody').append(
                                '<tr><td colspan="9" align="center">No Slip Available</td></tr>');
                            $('#Sliptableshow').removeClass('hide_this');
                            $('#no_data_details').addClass('hide_this');
                        }
                    } else if (data.message === "No auditslips found") {
                        // Handle the "No auditslips found" case
                        $('#Sliptableshow').removeClass('hide_this');
                        $('.cardforslips').hide();
                        $('#no_data_details').show();

                        $('#slipview_Details').removeClass('hide_this');

                        /*$('#usertable_detail tbody').append(
                            '<tr><td colspan="9" align="center">No Slip Available</td></tr>');*/
                    } else {
                        // Handle any other unexpected success response
                        alert("Unexpected response: " + data.message);
                    }
                } else {
                    alert('else');
                }

            },
            error: function(error) {
                console.error("Error fetching data:", error);
            }
        });

      
        $('html, body').animate({
            scrollTop: $('#slipview_Details').offset().top
        }, 500);

   });

    function renderTable(data,instname) {    
        var titleexcel = 'Audit Slip Details of '+instname;

        // Clear any existing table and destroy it
        if ($.fn.dataTable.isDataTable('#slipdetails_Table')) {
            $('#slipdetails_Table').DataTable().clear().destroy();
        }

        // Initialize DataTable with dynamic rows based on the response data
        var table = $('#slipdetails_Table').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "scrollX": true,
            "autoWidth": false,
            "responsive": true,
            "destroy": true, // Allow reinitialization
            "data": data.data.data, // Use the data array directly
            dom: 'Bfrtip',
            buttons: [createExcelExportButton(titleexcel)],
            "columns": [{
                    "data": "mainslipnumber"
                },
                {
                    "data": null,
                     className: "text-wrap  d-none d-md-table-cell text-left",
                    "render": function(data, type, row) {
                        return `<p><b>Main Objection: </b>${row.objectionename}</p><p><b>Sub Objection: </b>${row.subobjectionename}</p>`;
                    }
                },
                {
                    "data": "teamheadname"
                },
                {
                    "data": "auditorname"
                },
                {
                    "data": "createddate"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <span class="mb-1 badge text-bg-${row.processcode === 'A' ? 'success' : row.processcode === 'X' ? 'danger' : 'warning'}" style="font-size:11px;">
                                ${row.processcode === 'A' ? 'Dropped Slip' : row.processelname}
                            </span>
                            <span class="mb-1 badge text-bg-${row.auditquartercode === 'Q4' ? 'primary' : row.auditquartercode === 'Q3' ? 'secondary' : row.auditquartercode === 'Q2' ? 'info' : 'secondary'}" style="font-size:11px;">
                                ${row.auditquartercode}
                            </span>`;
                    }
                },
                {
                    "data": null,
                    "className": "noExport", // This column will be excluded from export
                    "render": function(data, type, row) {
                        return `
                            ${row.processcode !== 'E' ?
                                `<button onclick="Open_checkflow_model('${row.auditslipid}','${row.mainslipnumber}')" data-slipid="${row.auditslipid}" type="button" class="btn-sm btn btn-primary"><i class="ti ti-history fs-4 me-2"></i> Check Flow</button><div style="height:5px;"></div>`
                                : ''}
                            <button onclick="Open_viewmodel('${row.auditslipid}','${row.mainslipnumber}')" data-slipid="${row.auditslipid}" type="button" class="btn-sm btn btn-secondary"><i class="ti ti-eye fs-4 me-2"></i>View Details</button>
                        `;
                    }
                }
            ],
            "columnDefs": [{
                    "width": "50px",
                    "targets": 0
                },
                {
                    "width": "400px",
                    "targets": 1
                },
                {
                    "width": "150px",
                    "targets": 2
                },
                {
                    "width": "150px",
                    "targets": 3
                },
                {
                    "width": "150px",
                    "targets": 4
                },
                {
                    "width": "200px",
                    "targets": 5
                },
                {
                    "width": "100px",
                    "targets": 6,
                    "className": "noExport"
                } // Ensure this column is not exported
            ],
            "language": {
                "search": "Search :",
                "info": "Showing _START_ to _END_ of _TOTAL_ records"
            }
        });

        // Adjust columns after DataTable initialization
        table.columns.adjust().draw();

        $(".dt-button").addClass("btn btn-primary lang").text(download);
    }

    function LoadCommencedInstituteDetails(deptCode,regionCode,distCode,deptname,fromtab,whichslip,defaultQuarter)
    {  
        // Destroy previous instance
        if ($.fn.DataTable.isDataTable('#CommencedinstituteTable')) {
            $('#CommencedinstituteTable').DataTable().clear().destroy();
        }
        
       
        // Enable DD/MM/YYYY sorting
          DataTable.datetime('DD/MM/YYYY');


        var titleexcel = 'Institute wise Audit Slip Details for for ' + deptname;

        // Initialize the DataTable
        $('#CommencedinstituteTable').DataTable({
            processing: true,
            serverSide: false, // use true if backend handles pagination/sorting
           // fixedHeader: true,
            pagingType: 'full_numbers',
            language: {
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Prev'
                }
            },
            dom: 'Blfrtip', // 'r' is required to show processing
            buttons: [createExcelExportButton(titleexcel)],
            destroy: true,
            lengthMenu: [ [10, 30, 50, 100], [10, 30, 50, 100] ],
            pageLength: 10, // 👈 Default number of rows per page

            initComplete: function(settings, json) {
                $("#CommencedinstituteTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            ajax: {
                url: '{{ route("load_commenced_institute_details") }}',
                type: 'POST',
                data: {
                    deptCode: deptCode,
                    regionCode: regionCode,
                    distCode: distCode,
                    whichslip:whichslip,
                     quarter : defaultQuarter,
                    _token: '{{ csrf_token() }}'
                },
                dataSrc: 'data' // ensure controller returns { data: [...] }
            },
            columnDefs: [
            {
                targets: [6, 7], // fromdate and todate
                type: 'date'
            }
        ],
           
            columns: [
                {
                    data: null,
                    className: "d-none d-md-table-cell text-center align-middle",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'instename',
                    className: "text-wrap  d-none d-md-table-cell text-center align-middle",

                    width: '250px'
                },

                {
                        data: null,
                        className: 'text-wrap text-center align-middle',
                        width: '250px',
                        render: function (data, type, row) {
                            return '<b>Team Head:</b> ' + row.team_head_en + '<br><b>Team Members:</b> ' + row.team_members_en;
                        }
                },
                { 
                    data: 'mandays',
                    width: '5px',
                    className: "d-none d-md-table-cell text-center align-middle text-wrap",

                },
                {
                    data: 'regionename',
                    className: "text-center align-middle",
                },
                {
                    data: 'distename',
                    className: "text-center align-middle",
                },
                 
                 {
                    data: 'fromdate',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (!data || data === 'No') return '';

                        if (type === 'sort' || type === 'filter') {
                            // Convert DD/MM/YYYY to ISO for correct sorting/filtering
                            return moment(data, 'DD/MM/YYYY').format('YYYY-MM-DD');
                        }

                        if (type === 'display') {
                            // Show original date with badge
                            return '<span>' + data + '</span>';
                        }

                        return data; // fallback for other types
                    }
                },
                {
                    data: 'todate',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (!data || data === 'No') return '';

                        if (type === 'sort' || type === 'filter') {
                            // Convert DD/MM/YYYY to ISO for correct sorting/filtering
                            return moment(data, 'DD/MM/YYYY').format('YYYY-MM-DD');
                        }

                        if (type === 'display') {
                            // Show original date with badge
                            return '<span>' + data + '</span>';
                        }

                        return data; // fallback for other types
                    }
                },
               
                {
                    data: 'entrymeet_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            // Use raw value for sorting/filtering
                            return data === 'No' ? '' : data;
                        }

                        if (data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
                        } else {
                            // Format date as badge
                            return '<span class="badge bg-success" style="font-size:11px;">' +
                                DataTable.render.datetime('DD/MM/YYYY')(data, type, row) +
                                '</span>';
                        }
                    }
                },
                {
                    data: 'exitmeet_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            // For sorting and filtering: return raw value or empty string
                            return data === 'No' ? '' : data;
                        }

                        if (data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Conducted</span>';
                        } else {
                            return '<span class="badge bg-success" style="font-size:11px;">' +
                                DataTable.render.datetime('DD/MM/YYYY')(data, type, row) +
                                '</span>';
                        }
                    }
                },

                {
                    data: 'totalslips',
                    className: "d-none d-md-table-cell text-center align-middle ",

                    render: function (data, type, row) 
                    {
                        
                        if (type === 'display') {
                        
                            const isPositive = parseInt(data) > 0;
                            const badgeClass = isPositive ? 'badge bg-info sliponchange' : 'badge';
                            const badgeStyle = isPositive 
                                ? 'font-size:13px; cursor:pointer;' 
                                : 'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                            const slipText = row.totalslips > 1 ? 'Slips' : 'Slip';

                            return `<span class="${badgeClass}" style="${badgeStyle}" 
                                ${isPositive ? `data-instname='${row.instename}' data-slipstatus='all' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row.totalslips}
                            </span>`;
                        }
                        return data;

                       
                    }
                },
                {
                    data: 'pendingslips',
                    className: "d-none d-md-table-cell text-center align-middle ",
                    render: function (data, type, row) 
                    {
                        if (type === 'display') {
                            const isPositive = parseInt(data) > 0;
                            const badgeClass = isPositive ? 'badge bg-warning sliponchange' : 'badge';
                            const badgeStyle = isPositive 
                                ? 'font-size:13px; cursor:pointer;' 
                                : 'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                            const slipText = row.pendingslips > 1 ? 'Slips' : 'Slip';

                            return `<span class="${badgeClass}" style="${badgeStyle}" 
                                ${isPositive ? `data-instname='${row.instename}' data-slipstatus='P' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row
                                .pendingslips}
                            </span>`;
                        }
                        return data;
                     
                    }
                },
                {
                    data: 'convertedslips',
                    className: "d-none d-md-table-cell text-center align-middle",

                    render: function (data, type, row) 
                    { 
                        if (type === 'display') {
                        
                            const isPositive = parseInt(data) > 0;
                            const badgeClass = isPositive ? 'badge bg-danger sliponchange' : 'badge';
                            const badgeStyle = isPositive 
                                ? 'font-size:13px; cursor:pointer;' 
                                : 'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                            const slipText = row.convertedslips > 1 ? 'Slips' : 'Slip';

                            return `<span class="${badgeClass}" style="${badgeStyle}" 
                                ${isPositive ? `data-instname='${row.instename}' data-slipstatus='X' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row.convertedslips}
                            </span>`;

                        }
                        return data;

                    
                                     
                    }
                },
                {
                    data: 'droppedslips',
                    className: "d-none d-md-table-cell text-center align-middle",

                    render: function (data, type, row) 
                    {  
                        if (type === 'display') {
                        
                            const isPositive = parseInt(data) > 0;
                            const badgeClass = isPositive ? 'badge bg-info sliponchange' : 'badge';
                            const badgeStyle = isPositive 
                                ? 'font-size:13px; cursor:pointer;background-color:#ff4047 !important;' 
                                : 'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                            const slipText = row.droppedslips > 1 ? 'Slips' : 'Slip';

                            return `<span class="${badgeClass}" style="${badgeStyle}" 
                                ${isPositive ? `data-instname='${row.instename}'  data-slipstatus='A' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row.droppedslips}
                            </span>`;
                            
                        }
                        return data;


                                                                     
                    }
                },
                {
                    data: 'exitmeet_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (data === 'No') {
                            return '<span class="badge bg-warning" style="font-size:11px;">Pending</span>';
                        } else {
                            return '<span class="badge bg-success" style="font-size:11px;">Completed</span>';
                        }
                    }
                },

               

               
                        

            ]

        
        });
        var language='en';
        //updatedatatable(language, "instituteTable"); // Update table with correct language

       
        // Show the table container
        $('#commencedinstitutewisetable').show();
        $('html, body').animate({
            scrollTop: $('#commencedinstitutewisetable').offset().top
        }, 500); // 500ms for smooth scroll
    }


    function LoadAllocatedInstituteDetails(deptCode,regionCode,distCode,deptname,fromtab,defaultQuarter)
    {
        // Destroy previous instance
        if ($.fn.DataTable.isDataTable('#instituteTable')) {
            $('#instituteTable').DataTable().clear().destroy();
        }

          // Enable DD/MM/YYYY sorting
          DataTable.datetime('DD/MM/YYYY');

        var titleexcel = 'Institute wise Audit Plan Details for for ' + deptname;

        // Initialize the DataTable
        $('#instituteTable').DataTable({
 	columnDefs: [
                {
                    targets: [2, 3],
                    visible: false,
                    searchable: true,
                    exportable: true
                }
            ],
            processing: true,
            serverSide: false, // use true if backend handles pagination/sorting
            //fixedHeader: true,
           // scrollX: true, // 
            pagingType: 'full_numbers',
            language: {
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Prev'
                }
            },
            dom: 'Blrftip', // 'r' is required to show processing
            buttons: [createExcelExportButton(titleexcel)],
            destroy: true,
            lengthMenu: [ [10, 30, 50, 100], [10, 30, 50, 100] ],
            pageLength: 10, // 👈 Default number of rows per page

            initComplete: function(settings, json) {
                $("#instituteTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            ajax: {
                url: '{{ route("load_institute_details") }}',
                type: 'POST',
                data: {
                    deptCode: deptCode,
                    regionCode: regionCode,
                    distCode: distCode,
                    quarter : defaultQuarter,
                    _token: '{{ csrf_token() }}'
                },
                dataSrc: 'data' // ensure controller returns { data: [...] }
            },
           
            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div>
                                ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end text-center align-middle',
                    type: "num"
                },
                {
                    data: 'instename',
                    className: "text-wrap d-none d-md-table-cell text-center align-middle",

                    width: '250px'
                },
                 {
                    data: 'catename',
                    className: "d-none d-md-table-cell text-center align-middle extra-column text-wrap ",

                },
                 {
                    data: 'subcatename',
                    className: "d-none d-md-table-cell text-center align-middle extra-column text-wrap ",

                },

                {
                    data: 'teammembers',
                    className: 'text-wrap text-left extra-column',
                    render: function (data, type, row) {
                        return '<b>Team Head:</b> ' + row.team_head_en + '<br><b>Team Members:</b> ' + row.team_members_en;
                    }
                },
                { 
                    data: 'mandays',
                    width: '5px',
                    className: "d-none d-md-table-cell text-center align-middle extra-column text-wrap",

                },
                {
                    data: 'regionename',
                    className: "text-center align-middle extra-column",
                },
                {
                    data: 'distename',
                    className: "text-center align-middle extra-column",
                },
                {
                    data: 'fromdate',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (!data || data === 'No') return '';

                        if (type === 'sort' || type === 'filter') {
                            // Convert DD/MM/YYYY to ISO for correct sorting/filtering
                            return moment(data, 'DD/MM/YYYY').format('YYYY-MM-DD');
                        }

                        if (type === 'display') {
                            // Show original date with badge
                            return '<span>' + data + '</span>';
                        }

                        return data; // fallback for other types
                    }
                },
                {
                    data: 'todate',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (!data || data === 'No') return '';

                        if (type === 'sort' || type === 'filter') {
                            // Convert DD/MM/YYYY to ISO for correct sorting/filtering
                            return moment(data, 'DD/MM/YYYY').format('YYYY-MM-DD');
                        }

                        if (type === 'display') {
                            // Show original date with badge
                            return '<span>' + data + '</span>';
                        }

                        return data; // fallback for other types
                    }
                },
                {
                    data: 'schedule_status',
                    className: "d-none d-md-table-cell text-center align-middle extra-column",

                    render: function (data) {
                        if (data === 'Scheduled') {
                            return '<span class="badge bg-success" style="font-size:11px;">Scheduled</span>';
                        } else {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Scheduled</span>';
                        }
                    }
                },
                {
                    data: 'response_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle extra-column",
                    render: function (data) {
                        if (data === 'Replied') {
                            return '<span class="badge bg-success" style="font-size:11px;">Accepted</span>';
                            
                        } else {
                            return '<span class="badge bg-warning" style="font-size:11px;">Pending</span>';
                        }
                    }
                },

                {
                    data: 'workallocation_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle extra-column",
                    render: function (data) {
                        if (data === 'work allocated') {
                            return '<span class="badge bg-success" style="font-size:11px;">Work Allocated</span>';
                            
                        } else {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Allocated</span>';
                        }
                    }
                },
                {
                    data: 'entrymeet_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            // Use raw value for sorting/filtering
                            return data === 'No' ? '' : data;
                        }

                        if (data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
                        } else {
                            // Format date as badge
                            return '<span class="badge bg-success" style="font-size:11px;">' +
                                DataTable.render.datetime('DD/MM/YYYY')(data, type, row) +
                                '</span>';
                        }
                    }
                },
 
                {
                    data: 'exitmeet_status',
                    className: "d-none d-md-table-cell extra-column text-center align-middle",
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            // For sorting and filtering: return raw value or empty string
                            return data === 'No' ? '' : data;
                        }

                        if (data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Conducted</span>';
                        } else {
                            return '<span class="badge bg-success" style="font-size:11px;">' +
                                DataTable.render.datetime('DD/MM/YYYY')(data, type, row) +
                                '</span>';
                        }
                    }
                }         

            ]

        
        });
        var language='en';
        const mobileColumns = ["teammembers","mandays","regionename","distename","schedule_status", "response_status", "workallocation_status", "entrymeet_status", "exitmeet_status", "viewfieldaudit"];

        setupMobileRowToggle(mobileColumns);
        //updatedatatable(language, "instituteTable"); // Update table with correct language

       
        // Show the table container
        $('#institutewisetable').show();
        $('html, body').animate({
            scrollTop: $('#institutewisetable').offset().top
        }, 500); // 500ms for smooth scroll
    }


      
    function createExcelExportButton(titleexcel) 
    {
        return {
            extend: 'excelHtml5',
            text: `<i class="fas fa-download"></i> <span class="download-text">Download</span>`,
            className: 'btn btn-primary',
            filename: titleexcel,
            title: null,
            exportOptions: {
                columns: ':not(.noExport)' // Exclude columns with class "noExport"
            },
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];

                // Insert custom title row
                var titleRow = `
                    <row r="1">
                        <c t="inlineStr" r="A1">
                            <is><t>${titleexcel}</t></is>
                        </c>
                    </row>
                `;

                // Shift rows down
                var rows = sheet.getElementsByTagName('row');
                for (var i = 0; i < rows.length; i++) {
                    var rIndex = parseInt(rows[i].getAttribute('r'));
                    rows[i].setAttribute('r', rIndex + 1);

                    var cells = rows[i].getElementsByTagName('c');
                    for (var j = 0; j < cells.length; j++) {
                        var cell = cells[j];
                        var cellRef = cell.getAttribute('r');
                        var col = cellRef.replace(/[0-9]/g, '');
                        var row = parseInt(cellRef.replace(/[A-Z]/g, '')) + 1;
                        cell.setAttribute('r', col + row);
                    }
                }

                // Prepend the title row
                var sheetData = sheet.getElementsByTagName('sheetData')[0];
                sheetData.innerHTML = titleRow + sheetData.innerHTML;

                // Set column widths
                var cols = sheet.getElementsByTagName('col');
                for (var k = 0; k < cols.length; k++) {
                    cols[k].setAttribute('width', 30);
                }
            }
        };
    }


</script>


<style>
      table:not(.schedulingtable) td
    {
        max-width: 150px;
        min-width: 100px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }

</style>
@endsection
