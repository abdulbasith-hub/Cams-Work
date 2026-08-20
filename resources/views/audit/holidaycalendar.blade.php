@extends('index2')
@section('content')
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <style>
        /*#calendar {
            height: 500px !important;
            font-size: 12px;
        }*/
        .fc-toolbar-title {
            color: #3782ce !important;
        }
        /*.fc-view-harness, .fc-header-toolbar {
            width: 50% !important;
        }*/
        #eventModal td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .cream-background {
            background-color: #f1f1ee !important; /* Light cream color */
        }

        .fc-daygrid-day-number {
            font-weight: 400 !important;
            font-size: 12px !important;
        }
        .app-calendar .event-fc-color {
            font-size: 11px !important;
            border-width: 0 0 0 3px !important;
            padding: 3px 5px !important;
        }
        .fc-event-time {
            display: none !important;
        }

     /* Style the second modal when it appears */
.modal#confirmation_alert {
    opacity: 0; /* Initially hide the modal */
    transition: opacity 0.3s ease-in-out; /* Smooth transition */
}

/* When the second modal is active (shown), make it visible with hover effect */
.modal.show#confirmation_alert {
    opacity: 1;
}

.modal.show#confirmation_alert:hover {
    transform: scale(1.05); /* Slightly enlarge the modal */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Shadow effect for hover */
}


    </style>
    @include('common.alert')

    <div class="card" style="border-color: #7198b9">
        <div class="card-header card_header_color" style="padding: 10px;">HOLIDAY CALENDAR</div>
        <div class="card-body calender-sidebar app-calendar">
            <div id="calendar"></div>
        </div>
        <br>
    </div>
    <!-- BEGIN MODAL -->
    <div class="modal  bd-example-modal-lg " id="eventModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered modal-lg" >
                <div class="modal-content" style="border-color:black">
                    <div class="modal-header" style="background-color:rgb(0, 123, 255); justify-content: center;"  >

                        <h3 id="large_alert_header" class="text-white " style="text-align:center;" key=''>Add / Remove Holiday</h3>
                        <div id="large_alert_header_two" style="display:none" class="text-white " key='' ></div>

                        <button type="button" id="large_confirmation_button_close" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <label class="form-label required">Holiday Title</label>
                                    <input id="event-title" type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-12 mt-6">
                                <div>
                                    <label class="form-label">Holiday Date</label>
                                    <input disabled readonly id="event-start-date" type="date" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary btn-add-event">Add Holiday</button>
                        <button type="button" class="btn btn-danger btn-delete-event" style="display: none;"><i class="fa fa-trash"></i>&nbsp;Remove Holiday</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- END MODAL -->

    <script src="../assets/libs/fullcalendar/index.global.min.js"></script>
    <script src="../assets/js/vendor.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var newDate = new Date();
            function getDynamicMonth() {
                var getMonthValue = newDate.getMonth();
                var _getUpdatedMonthValue = getMonthValue + 1;
                return _getUpdatedMonthValue < 10 ? `0${_getUpdatedMonthValue}` : `${_getUpdatedMonthValue}`;
            }

          // Get the current month (0-based, so January is 0, December is 11)
            var currentMonth = newDate.getMonth();
            var currentYear = newDate.getFullYear();

            // Calculate the first date of the current month
            var validRangeStart = new Date(currentYear, currentMonth, 1); // First day of current month

            // Calculate the last date of the month 11 months from now
            var validRangeEnd = new Date(currentYear, currentMonth + 11 + 1, 0); // Last day of the month 11 months later

            var getModalTitleEl = document.querySelector("#event-title");
            var getModalStartDateEl = document.querySelector("#event-start-date");
            var getModalAddBtnEl = document.querySelector(".btn-add-event");
            var getModalDeleteBtnEl = document.querySelector(".btn-delete-event");
            var calendarEl = document.querySelector("#calendar");

            // Initialize calendar
            var calendar = new FullCalendar.Calendar(calendarEl, {
                selectable: true,
                height: window.innerWidth <= 1199 ? 900 : 1052,
                //initialView: window.innerWidth <= 1199 ? "listWeek" : "dayGridMonth",
                initialView: "dayGridMonth",
                initialDate: `${newDate.getFullYear()}-${getDynamicMonth()}-07`,
                headerToolbar: {
                    left: "prev",
                    center: "title",
                    right: "next",
                },
                datesSet: function (info) {
                    // Disable "prev" and "next" buttons based on the valid range
                    var currentDate = info.view.currentStart;
                    var startDate = new Date(validRangeStart);
                    var endDate = new Date(validRangeEnd);

                    var prevButton = document.querySelector('.fc-prev-button');
                    var nextButton = document.querySelector('.fc-next-button');

                    // Disable the "prev" button if the current view is at the start of the valid range
                    if (currentDate <= startDate) {
                        prevButton.disabled = true;
                        prevButton.style.pointerEvents = 'none';  // Prevent clicking
                    } else {
                        prevButton.disabled = false;
                        prevButton.style.pointerEvents = 'auto';  // Allow clicking
                    }

                    // Disable the "next" button if the current view is at the end of the valid range
                    var currentMonth = currentDate.getMonth();
                    var validEndMonth = endDate.getMonth();

                    // If the current month is the same as the end month, disable the "next" button
                    if (currentMonth === validEndMonth) {
                        nextButton.disabled = true;
                        nextButton.style.pointerEvents = 'none';  // Prevent clicking
                    } else {
                        nextButton.disabled = false;
                        nextButton.style.pointerEvents = 'auto';  // Allow clicking
                    }
                },
                dayCellDidMount: function(info) {
                    // Disable days outside of the valid range
                    var currentDate = info.date;
                    if (currentDate < new Date(validRangeStart) || currentDate > new Date(validRangeEnd)) {
                        info.el.classList.add('fc-disabled-day');
                        info.el.style.pointerEvents = 'none'; // Disable click events on the element
                    }
                },
                events: function (info, successCallback, failureCallback) {
                    // Fetch events from the backend

                    $.ajax({
                        url: '/get-holidays', // The route you defined earlier
                        type: 'GET',
                        success: function (response) {
                            console.log(response);

                            // Map the response data so that FullCalendar can understand it
                            const mappedEvents = response.map(event => ({
                                id: event.id,
                                title: event.holiday_title,  // Use holiday_title
                                start: event.holiday_date,   // Use holiday_date
                            }));

                            successCallback(mappedEvents);  // Pass the mapped events to FullCalendar

                            // Apply the cream background color to the fetched holiday dates
                            response.forEach(event => {
                                var eventDate = event.holiday_date;  // Get the holiday date
                                var dateElement = calendarEl.querySelector(`[data-date="${eventDate}"]`);
                                if (dateElement) {
                                    dateElement.classList.add('cream-background');
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            failureCallback(error);  // Handle error
                            console.error("Error:", error);
                        }
                    });
                },
                eventDidMount: function(info) {
                    // Optional: Additional logic if needed for custom events
                },
                select: function (info)
                {
                    // Prevent selection outside the valid range
                    var selectedDate = new Date(info.startStr);
                    if (selectedDate < new Date(validRangeStart) || selectedDate > new Date(validRangeEnd)) {
                        //alert('This date is outside the allowed range!');
                        //$('.fc-highlight').css('background-color', '#ffffff');
                        //$('.fc-daygrid-day-number').css('color', 'red');
                        calendar.unselect(); // Deselect the invalid date
                    } else
                    {
                       // Check if there is already an event on the selected date
                        var existingEvent = calendar.getEvents().find(event =>
                            event.startStr.slice(0, 10) === info.startStr.slice(0, 10)
                        );

                        if (existingEvent) {
                            // Simulate a click on the existing event to trigger eventClick
                            calendar.trigger('eventClick', {
                                event: existingEvent,
                                jsEvent: null,
                                view: calendar.view
                            });
                            return; // Prevent further execution for adding a new event
                        }
                        getModalAddBtnEl.style.display = "block";
                        getModalDeleteBtnEl.style.display = "none";
                        myModal.show();
                        getModalStartDateEl.value = info.startStr;
                    }

                },
                eventClick: function (info) {
                    var eventObj = info.event;
                    getModalTitleEl.value = eventObj.title;
                    getModalStartDateEl.value = eventObj.startStr.slice(0, 10);
                    getModalAddBtnEl.style.display = "none";
                    getModalDeleteBtnEl.style.display = "block";
                    getModalDeleteBtnEl.setAttribute("data-event-id", eventObj.id);
                    myModal.show();
                },
                eventDidMount: function(info) {
                    // Apply the red (danger) color directly to events
                    info.el.style.backgroundColor = '#008000';  // Bootstrap's danger color (red)
                    info.el.style.color = 'white';  // Make text white to be visible on red background
                    info.el.style.borderColor = '#008000'; // Set border color to danger red
                    info.el.style.borderWidth = '2px'; // Optional: Add border width if necessary
                    info.el.style.borderRadius = '5px'; // Border radius to round the corners

                    // Add red text color to the corresponding fc-daygrid-day-number
            var dayNumberElement = document.querySelector(
                `.fc-daygrid-day[data-date="${info.event.startStr.slice(0, 10)}"] .fc-daygrid-day-number`
            );
            if (dayNumberElement) {
                dayNumberElement.style.color = 'red'; // Set the day number text to red
            }else
            {
                dayNumberElement.style.color = 'green'; // Set the day number text to red

            }

                },
                eventClassNames: function ({ event: calendarEvent }) {
                // return ['event-fc-danger', 'fc-bg-danger'];

                },
                eventRender: function(info) {
                    // Rename the class from fc-daygrid-day-top to fc-daygrid-day-bg
                    var dayTopElement = info.el.querySelector('.fc-daygrid-day-top');
                    if (dayTopElement) {
                        dayTopElement.classList.remove('fc-daygrid-day-top');
                        dayTopElement.classList.add('fc-daygrid-day-bg');
                    }
                }
            });
            $('#confirmation_alert').css('z-index', 100000);

            // Add new event
            getModalAddBtnEl.addEventListener("click", function () {
                var title = getModalTitleEl.value;
                var date = getModalStartDateEl.value;

                if (title && date) {
                    // Send data to the controller using jQuery AJAX
                    $.ajax({
                        url: '/add-holiday',
                        type: 'POST',
                        data: {
                            holiday_title: title,  // Using 'holiday_title'
                            holiday_date: date,    // Using 'holiday_date'
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function (response) {
                            if (response.success) {
                                passing_alert_value('Confirmation','Holiday Added Successfully',
                                    'confirmation_alert', 'alert_header', 'alert_body',
                                    'confirmation_alert');

                                var dateElement = calendarEl.querySelector(`[data-date="${date}"]`);
                                if (dateElement) {
                                    dateElement.classList.add('cream-background');
                                }

                                // Reload the events from the server to update any changes
                                calendar.refetchEvents();  // This will trigger a re-fetch of events

                                myModal.hide(); // Hide the modal after adding the event
                            } else {
                                passing_alert_value('Alert', 'Failed to add holiday', 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                            }
                        },
                        error: function (xhr, status, error) {
                            var response = JSON.parse(xhr.responseText);

                            var errorMessage = response.error || 'An unknown error occurred';

                            // Displaying the error message
                            passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                        }
                    });
                } else {
                    passing_alert_value('Confirmation','Please provide Holiday Title',
                                    'confirmation_alert', 'alert_header', 'alert_body',
                                    'confirmation_alert');
                }
            });

            // Delete selected event
            getModalDeleteBtnEl.addEventListener("click", function () {
                var eventId = getModalDeleteBtnEl.getAttribute("data-event-id");
                var eventToRemove = calendar.getEventById(eventId);

                if (eventToRemove) {
                    // Send AJAX request to delete the event from the database
                    $.ajax({
                        url: '/delete-holiday/' + eventId,  // Send the event ID to the backend
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for security
                        },
                        success: function (response) {
                            if (response.success) {
                                    // Remove the event from FullCalendar
                                    eventToRemove.remove();
                                    // Remove cream background from the corresponding date cell
                                    var dateElement = calendarEl.querySelector(`[data-date="${eventToRemove.startStr.slice(0, 10)}"]`);
                                    if (dateElement) {
                                        dateElement.classList.remove('cream-background');
                                    }
                                    var dayNumberElement = dateElement.querySelector('.fc-daygrid-day-number');
                                    if (dayNumberElement) {
                                        dayNumberElement.style.color = '#222'; // Reset to default color
                                    }
                                    myModal.hide();
                                    passing_alert_value('Confirmation','Holiday Removed Successfully',
                                    'confirmation_alert', 'alert_header', 'alert_body',
                                    'confirmation_alert');

                            } else {
                                passing_alert_value('Confirmation','Failed to Remove holiday',
                                    'confirmation_alert', 'alert_header', 'alert_body',
                                    'confirmation_alert');
                            }
                        },
                        error: function (xhr, status, error) {
                            var response = JSON.parse(xhr.responseText);

                            var errorMessage = response.error || 'An unknown error occurred';

                            // Displaying the error message
                            passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                        }
                    });
                } else {
                    alert("Event not found.");
                }
            });

            // Initialize the modal
            var myModal = new bootstrap.Modal(document.getElementById("eventModal"));
            document.getElementById("eventModal").addEventListener("hidden.bs.modal", function () {
                getModalTitleEl.value = "";
                getModalStartDateEl.value = "";
                getModalDeleteBtnEl.removeAttribute("data-event-id");
            });

            // Render the calendar
            calendar.render();
        });



    </script>


@endsection
