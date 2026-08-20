<style>
    .topbar {
        width: calc(100% - 250px);
        transition: width 0.3s ease-in-out;
    }

    .sidebar-collapsed .topbar {
        width: 100%;
    }

    .topbar {
        width: 100% !important;
    }
</style>
<header class="topbar">

    <div class="with-vertical"><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Header -->
        <!-- ---------------------------------- -->
        <nav class="navbar navbar-expand-lg p-0 ">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                @php
                    $sessionchargedel = session('charge');
                    $sessionuserdel = session('user');
                    $dga_roletypecode = $DGA_roletypecode;
                    // print_r($sessionuserdel);
                @endphp
                <li class="mt-2">
                    <b>
                        <?php
                        // print_r($sessionchargedel);
 if (in_array($sessionchargedel->usertypecode, ['A', 'D'], true)) {
                       // if ($sessionchargedel->usertypecode == 'A') {
                            if ($sessionchargedel->deptesname) {
                                echo ' <span id="department-label" class="lang" key="department">Department</span> :
                                       <span id="department" data-english="' . htmlspecialchars($sessionchargedel->deptesname) . '"
                                       data-tamil="' . htmlspecialchars($sessionchargedel->depttsname ?? '-') . '">' .
                                       htmlspecialchars($sessionchargedel->deptesname) . '</span>';
                            }
                            if ($sessionchargedel->regionename) {
                                echo ' | <span id="region-label" class="lang" key="region">Region</span> :
                                       <span id="region" data-english="' . htmlspecialchars($sessionchargedel->regionename) . '"
                                       data-tamil="' . htmlspecialchars($sessionchargedel->regiontname ?? '-') . '">' .
                                       htmlspecialchars($sessionchargedel->regionename) . '</span>';
                            }
                            if ($sessionchargedel->distename) {
                                echo ' | <span id="district-label" class="lang" key="district">District</span> :
                                       <span id="district" data-english="' . htmlspecialchars($sessionchargedel->distename) . '"
                                       data-tamil="' . htmlspecialchars($sessionchargedel->disttname ?? '-') . '">' .
                                       htmlspecialchars($sessionchargedel->distename) . '</span>';
                            }
                         } elseif ($sessionchargedel->usertypecode == 'H') {

                            if ($sessionchargedel->auditeedeptename) {
                                echo ' <span id="region-label" class="lang" key="">Department</span> :
                                                               <span id="" data-english="' .
                                    htmlspecialchars($sessionchargedel->auditeedeptename) .
                                    '"
                                                                                 data-tamil="' .
                                    htmlspecialchars($sessionchargedel->auditeedepttname ?? '-') .
                                    '">' .
                                    htmlspecialchars($sessionchargedel->auditeedeptename) .
                                    '</span>';
                            }
                        }


			else {
                             if ($sessionchargedel->instename) {
                                 echo ' Institution : ' . $sessionchargedel->instename . ' , ' . $sessionchargedel->distename;
                             }
                            // if ($sessionchargedel->regionename) {
                             //    echo ' | Region : ' . $sessionchargedel->regionename;
                           //  }
                            // if ($sessionchargedel->distename) {
                            //     echo ' | District : ' . $sessionchargedel->distename;
                           // }
                        }
                        // if ($sessionchargedel->usertypecode == 'A') {
                        //     if ($sessionchargedel->desigelname) {
                        //         echo '| Designation : ' . $sessionchargedel->desigelname;
                        //     }
                        // }
                        ?>
                        <div class="text-start">


                        <div id="last-login" >
                                <span class="lang-text lang" key="last_login">Last Login:</span>
                                <?php echo $sessionuserdel->lastlogin ? \Carbon\Carbon::parse($sessionuserdel->lastlogin)->format('d-m-Y h:i A') : 'N/A'; ?>
                            </div>





                        </div>

                    </b>
                </li>
            </ul>

            <div class="d-block d-lg-none py-4">

            </div>
            <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="ti ti-dots fs-7"></i>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- <a href="javascript:void(0)"
                           class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-lg-none align-items-center justify-content-center"
                           type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                           aria-controls="offcanvasWithBothOptions">
                           <i class="ti ti-align-justified fs-7"></i>
                       </a> -->
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                        <!-- ------------------------------- -->
                        <!-- start profile Dropdown -->
                        <!-- ------------------------------- -->
                        <li class="mt-2">
                            <b>
                            <div class="text-end">
				<div id="session-timer" style="color:#0d6efd; font-weight:bold;">
                                        Session expires in: <span id="logout_time"
                                            style="color:red; font-weight:bold;"></span>&nbsp;sec
                                    </div>
 					<?php if ($sessionchargedel->usertypecode != 'H' ) { ?>

                                    <span id="username" data-english="<?php echo htmlspecialchars($sessionuserdel->username); ?>"
                                        data-tamil="<?php echo htmlspecialchars($sessionuserdel->usertname); ?>">
                                        <?php echo htmlspecialchars($sessionuserdel->username); ?>
                                    </span>


                                    <br>
					<?php } ?>
                                     <?php if (in_array($sessionchargedel->usertypecode, ['A', 'D'], true) && $sessionchargedel->desigelname) { ?>
                                    <span id="designation" data-english="<?php echo htmlspecialchars($sessionchargedel->desigelname); ?>"
                                    data-tamil="<?php echo htmlspecialchars($sessionchargedel->desigtlname ?? ''); ?>">
                                        <?php echo htmlspecialchars($sessionchargedel->desigelname); ?>
                                    </span>
                                    <?php } ?>
                                </div>
                            </b>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-profile-img">
					<img src="{{ asset('assets/images/profile/user-1.jpg') }}"
                                            class="rounded-circle" width="35" height="35" alt="modernize-img">
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar>
                                    <div class="py-3 px-2 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                    </div>
                                    <div class="d-flex align-items-start py-9 mx-7 border-bottom">
                                         <img src="{{ asset('assets/images/profile/user-1.jpg') }}"
                                            class="rounded-circle" width="60" height="60" alt="modernize-img">
                                        <div class="ms-3 ">
                                        <h5 class="mb-1 fs-3 mt-6">
					<?php if ($sessionchargedel->usertypecode != 'H' ) { ?>

                                                    <span id="username"
                                                        data-english="<?php echo htmlspecialchars($sessionuserdel->username); ?>"
                                                        data-tamil="<?php echo htmlspecialchars($sessionuserdel->usertname ?? ''); ?>">
                                                        <?php echo htmlspecialchars($sessionuserdel->username); ?>
                                                    </span>
  						<?php } ?>

                                            </h5>                                            <div class=" d-flex align-items-start gap-1">
                                                <i class="ti ti-mail fs-4 mt-1"></i>
                                                <p class="text-wrap text-break w-90"><?php echo $sessionuserdel->email; ?></p>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="d-grid py-4 px-7 pt-8">

                                        @if (session()->has('user'))
                                            <!-- Hidden Logout Form -->
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>

                                            <!-- Log Out Button -->
                                            <a href="javascript:void(0)" class="btn btn-outline-primary"
                                                onclick="document.getElementById('logout-form').submit();">
                                                Log Out
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                        <select class=" custom-select custom-select-sm ms-4" style="width: auto; " id="translate">
                            <option value="en">English</option>
                            <option value="ta">தமிழ்</option>
                        </select>
                        <!-- ------------------------------- -->
                        <!-- end profile Dropdown -->
                        <!-- ------------------------------- -->
                    </ul>
                </div>
            </div>
        </nav>
        <hr>
 @if ($sessionchargedel->usertypecode != 'I')
            @if (
                $sessionchargedel->roleactioncode == View::shared('PUroleactioncode') ||
                    $sessionchargedel->roleactioncode == View::shared('PUADroleactioncode'))
          <div class="d-flex justify-content-center">
    <nav class="d-inline-block px-4 py-2 rounded-3"
        style="
            min-width: 350px;
            background: linear-gradient(90deg, #833AB4, #FD1D1D, #FCB045);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        ">
        <h5 class="m-0" style="color:white; font-size:1.3rem; font-weight:700; letter-spacing:1px;">
            <span class="lang" key="apms_label">Audit Para Management System</span>
        </h5>
    </nav>
</div>

<br>

            @endif
        @endif
        <!-- ---------------------------------- -->
        <!-- End Vertical Layout Header -->
        <!-- ---------------------------------- -->

        <!-- ------------------------------- -->
        <!-- apps Dropdown in Small screen -->
        <!-- ------------------------------- -->
        <!--  Mobilenavbar -->

    </div>

</header>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- <script src="../common/js_common.js"></script> -->
    <script src="{{ asset('/common/js_common.js') }}"></script>


<script>

$(document).ready(function() {

        var maxTime = 600000000;
        var timeLeft = maxTime;
        var logoutCalled = false;

        function resetTimer() {
            timeLeft = maxTime;
            $('#logout_time').text(timeLeft);
        }

        $(document).on('mousemove keydown click scroll input', function() {
            resetTimer();
        });

        setInterval(function() {

            timeLeft--;
            $('#logout_time').text(timeLeft);

            if (timeLeft <= 0 && !logoutCalled) {
                logoutCalled = true;
                document.getElementById('logout-form').submit();
            }

        }, 100000000000);
    });


   $(document).ready(function () {
    let storedLang = localStorage.getItem("lang") || "en"; // Get stored language or default to 'en'
    $("#translate").val(storedLang); // Set dropdown value
    applyLanguage(storedLang); // Apply stored language

    $("#translate").on("change", function () {
        let selectedLang = $(this).val();
        localStorage.setItem("lang", selectedLang); // Store selected language
        applyLanguage(selectedLang);
    });

    function applyLanguage(lang) {
        let usernameElement = $("#username");
        let designationElement = $("#designation");

        if (usernameElement.length) {
            usernameElement.text(lang === "ta" ? usernameElement.data("tamil") : usernameElement.data("english"));
        }

        if (designationElement.length) {
            designationElement.text(lang === "ta" ? designationElement.data("tamil") : designationElement.data("english"));
        }
    }
});

$(document).ready(function () {
    let storedLang = localStorage.getItem("lang") || "en";
    $("#translate").val(storedLang);
    applyLanguage(storedLang);

    $("#translate").on("change", function () {
        let selectedLang = $(this).val();
        localStorage.setItem("lang", selectedLang);
        applyLanguage(selectedLang);
    });

    function applyLanguage(lang) {
        $("[data-english]").each(function () {
            let text = lang === "ta" ? $(this).data("tamil") : $(this).data("english");
            $(this).text(text);
        });
    }
});

</script>
