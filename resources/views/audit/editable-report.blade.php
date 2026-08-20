@extends('index2')

@section('content')
@include('common.alert')

    <style>
        .step-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }

        .step-btn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        .step-btn.active {
            background-color: #007bff;
            color: white;
        }

        .step-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-decoration:underline;
        }

        .iframe-container {
            display: flex; /* Enables flexbox */
            justify-content: center; /* Centers horizontally */
            height: 100%; /* Ensure parent container takes full height */
            width: 100%;
        }

        iframe {
            width: 90%;
            height: 500px;
            max-height: 500px !important;
            border: none; /* Optional: remove border */
        }
        .btn-outline-primary
        {
            padding:15px;
        }

    </style>


    <div class="card" style="border-color: #7198b9">
        <div class="card-header card_header_color" style="padding:10px;">AUDIT REPORTS</div>
        <br>
        <div class="card-body calender-sidebar app-calendar">
            <div class="">
                <div class="row">
                    <!-- Step Buttons (left side) -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="step-buttons">
                                    <!--<div class="card" onclick="showStep('initiation')">
                                        <div class="card-body" >
                                        <div class="d-flex flex-row">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <b>1</b>
                                            </div>
                                            <div class="ms-3 align-self-center">
                                            <h4 class="mb-0 fs-5">Intimation Letter</h4>
                                            </div>

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card" onclick="showStep('meeting')">
                                        <div class="card-body" >
                                        <div class="d-flex flex-row">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-calendar-stats fs-6"></i>
                                            </div>
                                            <div class="ms-3 align-self-center">
                                            <h4 class="mb-0 fs-5">Entry Meeting</h4>
                                            </div>

                                        </div>
                                        </div>
                                    </div>
                                    <div class="card" onclick="showStep('ethics')">
                                        <div class="card-body" >
                                        <div class="d-flex flex-row">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-file-analytics fs-6"></i>
                                            </div>
                                            <div class="ms-3 align-self-center">
                                            <h4 class="mb-0 fs-5">Code Of Ethics</h4>
                                            </div>

                                        </div>
                                        </div>
                                    </div>

                                    <div class="card" onclick="showStep('initiation')">
                                        <div class="card-body" >
                                        <div class="d-flex flex-row">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-report-analytics fs-6"></i>
                                            </div>
                                            <div class="ms-3 align-self-center">
                                            <h4 class="mb-0 fs-5">Work Allocation</h4>
                                            </div>

                                        </div>
                                        </div>
                                    </div>

                                    <div class="card" onclick="showStep('initiation')">
                                        <div class="card-body" >
                                        <div class="d-flex flex-row">
                                            <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-calendar-time fs-6"></i>
                                            </div>
                                            <div class="ms-3 align-self-center">
                                            <h4 class="mb-0 fs-5">Exit Meeting</h4>
                                            </div>

                                        </div>
                                        </div>
                                    </div>-->
                                    <button class="btn btn-outline-primary" data-step="intimationletter_en" data-stepno="1" onclick="showStep('intimationletter_en')">
                                        1. Intimation Letter
                                    </button>
                                    <button class="btn btn-outline-primary" data-step="intimationletter_ta" data-stepno="1" onclick="showStep('intimationletter_ta')">
                                        1. அறிவிப்பு கடிதம்
                                    </button>
                                    <button class="btn btn-outline-primary" data-step="entrymeeting_en" data-stepno="2" onclick="showStep('entrymeeting_en')">
                                        2. Entry Meeting
                                    </button>
                                    <button class="btn btn-outline-primary" data-step="entrymeeting_ta" data-stepno="2" onclick="showStep('entrymeeting_ta')">
                                        2. நுழைவு சந்திப்பு
                                    </button>
                                    <button class="btn btn-outline-primary" data-step="codeofethics_en" data-stepno="3" onclick="showStep('codeofethics_en')">
                                        3. Code of Ethics
                                    </button>
                                    <button class="btn btn-outline-primary" data-step="codeofethics_ta" data-stepno="3" onclick="showStep('codeofethics_ta')">
                                        3. நெறிமுறைகள் குறியீடு
                                    </button>

                                    <!--<button class="btn btn-outline-primary" data-step="workallocation" onclick="showStep('workallocation')">
                                        4. Work Allocation
                                    </button>-->
                                    <button class="btn btn-outline-primary" data-step="exitmeeting_en" data-stepno="5" onclick="showStep('exitmeeting_en')">
                                        5. Exit Meeting
                                    </button>

                                    <button class="btn btn-outline-primary" data-step="exitmeeting_ta" data-stepno="5" onclick="showStep('exitmeeting_ta')">
                                        5. வெளியேறும் சந்திப்பு
                                    </button>

                                    


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content (right side) -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-container">
                                    <!-- Step 1: Initiation Letter -->
                                    <div id="intimationletter_en" class="step-container" style="height:600px">
                                        <div class="step-header">Intimation Letter</div>
                                        <div class="iframe-container">
                                            <iframe id="intimationletter_en_iframe"></iframe>
                                        </div>
                                    </div>

                                    <div id="intimationletter_ta" class="step-container" style="height:600px">
                                        <div class="step-header">Intimation Letter</div>
                                        <div class="iframe-container">
                                            <iframe id="intimationletter_ta_iframe"></iframe>
                                        </div>
                                    </div>

                                    <!-- Step 2: Entry Meeting -->
                                    <div id="entrymeeting_en" class="step-container">
                                        <div class="step-header">Entry Meeting</div>
                                        <div class="iframe-container">
                                            <iframe id="entrymeeting_en_iframe"></iframe>
                                        </div>

                                    </div>
                                    <div id="entrymeeting_ta" class="step-container">
                                        <div class="step-header">Entry Meeting</div>
                                        <div class="iframe-container">
                                            <iframe id="entrymeeting_ta_iframe"></iframe>
                                        </div>

                                    </div>


                                    <!-- Step 3: Code of Ethics -->
                                    <div id="codeofethics_en" class="step-container">
                                        <div class="step-header">Code Of Ethics</div>
                                        <div class="iframe-container">
                                            <iframe id="codeofethics_en_iframe"></iframe>
                                        </div>
                                    </div>

                                    <div id="codeofethics_ta" class="step-container">
                                        <div class="step-header">நெறிமுறைகள் குறியீடு</div>
                                        <div class="iframe-container">
                                            <iframe id="codeofethics_ta_iframe"></iframe>
                                        </div>
                                    </div>

                                    <!-- Step 4: Exit Meeting -->
                                    <div id="exitmeeting_en" class="step-container">
                                        <div class="step-header">Exit Meeting</div>
                                        <div class="iframe-container">
                                            <iframe id="exitmeeting_en_iframe"></iframe>
                                        </div>

                                    </div>
                                    <div id="exitmeeting_ta" class="step-container">
                                        <div class="step-header">Exit Meeting</div>
                                        <div class="iframe-container">
                                            <iframe id="exitmeeting_ta_iframe"></iframe>
                                        </div>

                                    </div>
                                    <br>
                                    <hr style="border-top: var(--bs-border-width) solid #ebf1f6 !important;">
                                    <div align="center" class="btn-container" id="previewBtn" style="font-size: 15px;">
                                        <a class="btn btn-success" id="finalizebtn"> <i class="fas fa-file-pdf"></i>&nbsp;&nbsp;Finalize Report</a>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-container').forEach(stepContainer => {
            stepContainer.style.display = 'none';
        });

        // Show the selected step (if it exists)
        const selectedStep = document.getElementById(step);
        if (selectedStep) {
            selectedStep.style.display = 'block';
        }

        // Remove 'active' class from all buttons
        document.querySelectorAll('.btn-outline-primary').forEach(button => {
            button.classList.remove('btn-primary'); // Remove Bootstrap primary styling
            button.classList.remove('active'); // Remove custom active styling
        });

        // Add 'active' class to the clicked button
        const activeButton = document.querySelector(`.btn-outline-primary[data-step="${step}"]`);
        if (activeButton) {
            activeButton.classList.add('btn-primary'); // Change to Bootstrap primary button
            activeButton.classList.add('active'); // Custom class for additional styling
        }

        // Load specific content if the step is 'initiation'
        if (step === 'intimationletter_en' || step === 'intimationletter_ta') {
            loadInitiationLetterContent(step);
        }else if (step === 'entrymeeting_en' || step =='entrymeeting_ta') {
            loadEntryMeetingContent(step);
        }else if (step === 'codeofethics_en' || step =='codeofethics_ta') {
            loadCodeofEthicsContent(step);
        }else if (step === 'exitmeeting_en' || step =='exitmeeting_ta') {
            loadExitMeetingContent(step);
        }


    }

    // Initialize by showing the initiation step
    showStep('intimationletter_en');

    // Function to load content for the initiation letter into the iframe
    function loadInitiationLetterContent(step) {
        fetch('/intimationletter?step='+step) // Call the Laravel controller
            .then(response => response.json())
            .then(data => {
                if (data.res === 'success') {
                    const iframe = document.getElementById(''+step+'_iframe');

                    // Ensure iframe is available
                    if (!iframe) {
                        console.error('Iframe element not found.');
                        return;
                    }

                    iframe.srcdoc = data.html;

                    // Alternative: Directly write into the iframe (if `srcdoc` doesn't work)
                    setTimeout(() => {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(data.html);
                        iframeDoc.close();
                    }, 500); // Small delay to ensure iframe is loaded
                } else {
                    console.error('Error loading content:', data.error);
                }
            })
            .catch(error => console.error('Error fetching initiation letter:', error));
    }


    function loadEntryMeetingContent(step)
    {
        fetch('/entrymeeting_editreport?step='+step) // Call the Laravel controller
            .then(response => response.json())
            .then(data => {
                if (data.res === 'success') {
                    const iframe = document.getElementById(''+step+'_iframe');

                    // Ensure iframe is available
                    if (!iframe) {
                        console.error('Iframe element not found.');
                        return;
                    }

                    // ✅ Use `srcdoc` to inject HTML content inside the iframe
                    iframe.srcdoc = data.html;

                    // Alternative: Directly write into the iframe (if `srcdoc` doesn't work)
                    setTimeout(() => {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(data.html);
                        iframeDoc.close();
                    }, 500); // Small delay to ensure iframe is loaded
                } else {
                    console.error('Error loading content:', data.error);
                }
            })
            .catch(error => console.error('Error fetching initiation letter:', error));

    }


    function loadCodeofEthicsContent(step)
    {
        fetch('/codeofethics_editreport?step='+step) // Call the Laravel controller
            .then(response => response.json())
            .then(data => {
                if (data.res === 'success') {
                    const iframe = document.getElementById(''+step+'_iframe');

                    // Ensure iframe is available
                    if (!iframe) {
                        console.error('Iframe element not found.');
                        return;
                    }

                    // ✅ Use `srcdoc` to inject HTML content inside the iframe
                    iframe.srcdoc = data.html;

                    // Alternative: Directly write into the iframe (if `srcdoc` doesn't work)
                    setTimeout(() => {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(data.html);
                        iframeDoc.close();
                    }, 500); // Small delay to ensure iframe is loaded
                } else {
                    console.error('Error loading content:', data.error);
                }
            })
            .catch(error => console.error('Error fetching initiation letter:', error));

    }


    function loadExitMeetingContent(step)
    {
        fetch('/exitmeeting_editreport?step='+step) // Call the Laravel controller
            .then(response => response.json())
            .then(data => {
                if (data.res === 'success') {
                    const iframe = document.getElementById(''+step+'_iframe');

                    // Ensure iframe is available
                    if (!iframe) {
                        console.error('Iframe element not found.');
                        return;
                    }

                    // ✅ Use `srcdoc` to inject HTML content inside the iframe
                    iframe.srcdoc = data.html;

                    // Alternative: Directly write into the iframe (if `srcdoc` doesn't work)
                    setTimeout(() => {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(data.html);
                        iframeDoc.close();
                    }, 500); // Small delay to ensure iframe is loaded
                } else {
                    console.error('Error loading content:', data.error);
                }
            })
            .catch(error => console.error('Error fetching initiation letter:', error));

    }

    $('#finalizebtn').on('click', function()
    {
        var confirmation = 'Are you sure to Finalize?';

        passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                        'alert_body', 'forward_alert');
        $("#process_button").html("Ok");

    });

    $('#process_button').on('click', function()
    {
        FinalizeReport();


    });


    function FinalizeReport()
    {
       // Find the iframe inside the active step
        const activeButton = document.querySelector('.btn.active');
        const activeStep = activeButton ? activeButton.getAttribute('data-step') : null;
        const activeStepNo = activeButton ? activeButton.getAttribute('data-stepno') : null;
        const activeIframe = document.querySelector(`#${activeStep} iframe`);


        if (activeIframe) {
            // Extract the HTML content of the iframe
            const iframeContent = activeIframe.contentWindow.document.body.innerHTML;

            // Send the content to the server using jQuery AJAX
            $.ajax({
                url: '/finalize-auditreport',
                type: 'POST',
                data: {
                    iframeContent: iframeContent, // Pass iframe content
                    activeStep: activeStep,       // Pass activeStep
                    activeStepNo: activeStepNo,   // Pass activeStepNo
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token from meta tag
                },
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.status === 'success') {
                        passing_alert_value('Confirmation', response.message,
                                                    'confirmation_alert', 'alert_header', 'alert_body',
                                                    'confirmation_alert');
                        //location.reload();
                    } else {
                        passing_alert_value('Confirmation', response.message,
                                                    'confirmation_alert', 'alert_header', 'alert_body',
                                                    'confirmation_alert');
                    }
                },
                error: function(xhr, status, error) {
                    passing_alert_value('Confirmation', error,
                                                    'confirmation_alert', 'alert_header', 'alert_body',
                                                    'confirmation_alert');
                }
            });
        } else {
            passing_alert_value('Confirmation', 'No iframe content found!',
                                                    'confirmation_alert', 'alert_header', 'alert_body',
                                                    'confirmation_alert');
        }


    }



    </script>

@endsection
