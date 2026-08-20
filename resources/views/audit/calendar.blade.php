@extends('index2')
@section('content')
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <style>
        #calendar {
            height: 500px !important; /* Adjust the height to make it smaller */
            font-size: 12px; /* Optional: Adjust font size to fit the smaller space */
        }
        .fc-toolbar-title
        {
            color:#3782ce !important;
        }

        .fc-view-harness, .fc-header-toolbar
        {
            width:50% !important;
        }

        #eventModal td {
            padding: 12px;
            /* Adds 10px of padding on all sides of each cell */
            border: 1px solid #ddd;
            /* Optional: Add a border for visibility */
        }
        
        .fc-daygrid-day-number
        {
            font-size:12px !important;
            color:#222 !important;
        }

        .app-calendar .event-fc-color {
            font-size: 11px !important;
            border-width: 0 0 0 3px  !important;
            padding: 3px 5px  !important;
        }

        .fc-event-time
        {
            display:none !important;
        }

        /* Custom event style */
        .fc-event {
            border: none !important; /* Remove the bottom border */
            padding: 2px;
        }

        /* Start date styles */
        .fc-event-start {
            border-top-left-radius: 4px !important; /* Rounded top-left corner */
            border-bottom-left-radius: 4px !important; /* Rounded bottom-left corner */
        }

        /* End date styles */
        .fc-event-end {
            border-top-right-radius: 4px !important; /* Rounded top-right corner */
            border-bottom-right-radius: 4px !important; /* Rounded bottom-right corner */
        }


       
    </style>

    <div class="card" style="border-color: #7198b9">
    <div class="card-header card_header_color" style="padding:10px;">AUDIT CALENDAR</div>
        <div class="card-body calender-sidebar app-calendar">
            <div id="calendar"></div>
        </div>
        <br>
    </div>
    <!-- BEGIN MODAL -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-md">
            <div class="modal-content">
               <div class="modal-header" >
                    <button type="button" id="large_confirmation_button_close" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="font-size:12px;">
                <div class="card" style="border-color: #7198b9"><div class="card-header card_header_color">Audit Details</div><div class="card-body">
                    <table style="width:100%;" class="table  table-hover w-100 table-bordered display largemodal">
                        <tbody class="eventDetailsTable">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
              
    </div>
        </div>
    </div>
    <!-- END MODAL -->


    <script src="../assets/libs/fullcalendar/index.global.min.js"></script>
    <script src="../assets/js/vendor.min.js"></script>
    
    <script>       
      document.addEventListener("DOMContentLoaded", function () 
      {
        var newDate = new Date();
        function getDynamicMonth() {
            getMonthValue = newDate.getMonth();
            _getUpdatedMonthValue = getMonthValue + 1;
            if (_getUpdatedMonthValue < 10) {
                return `0${_getUpdatedMonthValue}`;
            } else {
                return `${_getUpdatedMonthValue}`;
            }
        }

        var getModalTitleEl = document.querySelector("#event-title");
        var getModalStartDateEl = document.querySelector("#event-start-date");
        var getModalEndDateEl = document.querySelector("#event-end-date");
        var calendarsEvents = {
            Danger: "danger",
            Success: "success",
            Primary: "primary",
            Warning: "warning",
        };

        var calendarEl = document.querySelector("#calendar");
        var checkWidowWidth = function () {
            return window.innerWidth <= 1199;
        };

        var calendarHeaderToolbar = {
            left: "prev",
            center: "title",
            right: "next",
        };

        // Array of predefined colors
        const colors = [
            'rgb(236, 140, 134)',   // Light maroon
            'rgb(173, 141, 202)',    // Light teal
            'rgb(233, 105, 158)',    // Orange
            'rgb(74, 143, 189)'     // Light blue
        ];

        const bordercolors = [
            'rgb(126, 46, 40)',   // Light maroon
            'rgb(55, 38, 70)',    // Light teal
            'rgb(143, 24, 73)',    // Orange
            'rgb(22, 70, 102)'     // Light blue
        ];

        // Fetch events from the backend via AJAX
        function fetchEvents() {
            return fetch('/events')  // Use backticks for template literal (optional here)
                .then(response => response.json())  // Parse the JSON response
                .then(data => {
                    return data.map((event, index) => {
                        // Correct the mapping of event properties
                        let startDate = new Date(event.start);
                        let endDate = new Date(event.end);

                        // Adjust both start and end dates to ISO strings
                        let adjustedStartDate = startDate.toISOString();
                        let adjustedEndDate = endDate.toISOString();

                        // Assign color based on event index (cycling through 1, 2, 3, 4)
                        const colorIndex = index % colors.length;  // This ensures colors cycle 1, 2, 3, 4
                        const selectedColor = colors[colorIndex];

                        const bordercolorIndex = index % bordercolors.length; 
                        const selectedBorderColor = bordercolors[bordercolorIndex];


                        return {
                            id: event.id,
                            title: event.title,
                            start: adjustedStartDate,  // Adjusted start date/time
                            end: adjustedEndDate,     // Adjusted end date/time
                            backgroundColor: selectedColor,  // Apply cyclical color
                            startBorderColor: selectedBorderColor,  // Color for start border
                            endBorderColor: selectedBorderColor,      // Color for end border
                            extendedProps: {
                                calendar: event.extendedProps.calendar, // The custom calendar type
                            },
                        };
                    });
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                });
        }


       
        var calendarEventClick = function (info) 
        {
            var auditscheduleid = info.event.id; // Get the auditscheduleid for the clicked event

            // Now fetch the event details based on the auditscheduleid
            $.ajax({
                url: '/event-details',  // URL to fetch event details by auditscheduleid
                method:'GET',
                data: {auditscheduleid:auditscheduleid},
                success: function(data) {
                   // Create the HTML table rows to append
                   $('.eventDetailsTable').empty();
                    var appenddata = '<tr><td><b>Audit Office</b></td><td>'+data.instename+'</td></tr>' +
                                    '<tr><td><b>Department</b></td><td>'+data.deptesname+'</td></tr>' +
                                    '<tr><td><b>Category</b></td><td>'+data.catename+'</td></tr>' +
                                    '<tr><td><b>From Date</b></td><td>'+data.fromdate_format+'</td></tr>' +
                                    '<tr><td><b>To Date</b></td><td>'+data.todate_format+'</td></tr>' +
                                    '<tr><td><b>Audit Team</b></td><td>'+data.teamname+'</td></tr>' +
                                    '<tr><td><b>Plan Period</b></td><td>'+data.typeofauditename+'</td></tr>' +
                                    '<tr><td><b>Financial Year</b></td><td>'+data.yearname+'</td></tr>' +
                                    '<tr><td><b>Audit Quarter</b></td><td>'+data.auditquarter+'</td></tr>';

                    // Append the HTML content to a table with id 'eventDetailsTable'
                    $('.eventDetailsTable').append(appenddata);

                }
            });
                myModal.show();
      
        };
        var newDate = new Date();

         // Function to get the current quarter with specific start and end dates
        function getCurrentQuarter() {
            const month = newDate.getMonth() + 1; // Get the current month (1-12)
            const year = newDate.getFullYear(); // Get the current year

            // Define the date ranges for each quarter
            if (month >= 4 && month <= 6) {
                return {
                    quarter: 1,
                    startDate: `${year}-04-01`, // April 1st
                    endDate: `${year}-06-30`    // June 30th
                }; // Quarter 1 (Apr-Jun)
            } else if (month >= 7 && month <= 9) {
                return {
                    quarter: 2,
                    startDate: `${year}-07-01`, // July 1st
                    endDate: `${year}-09-30`    // September 30th
                }; // Quarter 2 (Jul-Sep)
            } else if (month >= 10 && month <= 12) {
                return {
                    quarter: 3,
                    startDate: `${year}-10-01`, // October 1st
                    endDate: `${year}-12-31`    // December 31st
                }; // Quarter 3 (Oct-Dec)
            } else {
                return {
                    quarter: 4,
                    startDate: `${year}-01-01`, // January 1st
                    endDate: `${year}-03-31`    // March 31st
                }; // Quarter 4 (Jan-Mar)
            }
        }

        // Get the current quarter's start and end date
        const currentQuarter = getCurrentQuarter();
        const validRangeStart = currentQuarter.startDate; // Start of the current quarter
        const validRangeEnd = currentQuarter.endDate; // End of the current quarter
    
        var calendar = new FullCalendar.Calendar(calendarEl, {
            selectable: true,
            height: checkWidowWidth() ? 900 : 1052,
            initialView: checkWidowWidth() ? "listWeek" : "dayGridMonth",
            initialDate: newDate.toISOString().split("T")[0], // Start from the current date
            headerToolbar: calendarHeaderToolbar,

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
            select: function(info) {
                // Prevent selection outside the valid range
                var selectedDate = new Date(info.startStr);
                if (selectedDate < new Date(validRangeStart) || selectedDate > new Date(validRangeEnd)) {
                    //alert('This date is outside the allowed range!');
                    //$('.fc-highlight').css('background-color', '#ffffff');
                    //$('.fc-daygrid-day-number').css('color', 'red');
                    calendar.unselect(); // Deselect the invalid date
                } else {
                    console.log('Date selected:', info.startStr);
                }
            },
            events: function (info, successCallback, failureCallback) {
                fetchEvents().then(events => {
                    successCallback(events);
                }).catch(failureCallback);
            },
            eventDidMount: function(info) {

                let eventData = info.event; // Get the current event

                let startColor = info.event.extendedProps.startBorderColor;
                let endColor = info.event.extendedProps.endBorderColor;

                let eventElement = $(info.el);

                let eventStartElement = eventElement.closest('.fc-event-start');

                let eventEndElement = eventElement.closest('.fc-event-end');

                if (eventStartElement.length) {
                    eventStartElement[0].style.cssText += `border-left: 4px solid ${startColor} !important;`;
                } 

                if (eventEndElement.length) {
                    eventEndElement[0].style.cssText += `border-right: 4px solid ${endColor} !important;`;
                } 
            },

           /* eventContent: function(arg) {
                let startColor = arg.event.extendedProps.startBorderColor || 'black';
                let endColor = arg.event.extendedProps.endBorderColor || 'black';

                return {
                    html: `<div style="border-bottom-left-radius: 4px !important;border-left: 4px solid ${startColor};border-top-left-radius: 4px !important border-right: 4px solid ${endColor};">
                            ${arg.event.title}
                        </div>`
                };
            },*/
            eventClassNames: function ({ event: calendarEvent }) {
                
            },
            eventClick: calendarEventClick,
            windowResize: function () {
                if (checkWidowWidth()) {
                    calendar.changeView("listWeek");
                    calendar.setOption("height", 900);
                } else {
                    calendar.changeView("dayGridMonth");
                    calendar.setOption("height", 1052);
                }
            },
        });

        calendar.render();

        var myModal = new bootstrap.Modal(document.getElementById("eventModal"));
       
      });

    </script>

@endsection
