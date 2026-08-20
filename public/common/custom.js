let languageDataCache = null;  // Cache for language data
let isDataFetched = false;     // Flag to track if data has been fetched
function getLabels_jsonlayout(keys,onchangeoflanuguage) {
    //alert();
    return new Promise((resolve, reject) => {
        // Check if the data is already cached and available
        if (languageDataCache && isDataFetched) {
            var selectedLang = '';
            if(onchangeoflanuguage  == 'Y') selectedLang =getLanguage('Y');
            else  selectedLang = getLanguage('N');

            const result = keys.reduce((acc, key) => {
                acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Some error occurred contact administrator";
                return acc;
            }, {});
            return resolve(result);
        }
        if (!isDataFetched) {
            fetch('/json/layout.json')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to load JSON file: " + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    languageDataCache = data;  // Cache the fetched data
                            isDataFetched = true;  // Mark that data has been fetched

                            var selectedLang = '';
                            if(onchangeoflanuguage  == 'Y') selectedLang =getLanguage('Y');
                            else  selectedLang = getLanguage('N');


                            const result = keys.reduce((acc, key) => {
                                acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Some error occurred contact administrator";
                                return acc;
                            }, {});
                            resolve(result);
                })
                .catch(error => {
                    console.error("Error loading placeholders/errors JSON:", error);
                    reject(error);
                });
        } else {
            lang = window.localStorage.getItem("lang");
            updatePlaceholders(lang);
            resolve(); // If already loaded, resolve immediately
        }
        // // If data has not been fetched or cached yet, proceed to fetch it
        // fetch('json/layout.json')
        //     .then(response => {
        //         if (!response.ok) {
        //             throw new Error(`Failed to load JSON: ${response.statusText}`);
        //         }
        //         return response.json();
        //     })
        //     .then(data => {
        //         languageDataCache = data;  // Cache the fetched data
        //         isDataFetched = true;  // Mark that data has been fetched
        //         const selectedLang = getLanguage();
        //         const result = keys.reduce((acc, key) => {
        //             acc[key.id] = languageDataCache[selectedLang]?.[key.key] || "Translation not available";
        //             return acc;
        //         }, {});
        //         resolve(result);
        //     })
        //     .catch(error => {
        //         console.error("Error loading language data:", error);
        //         const errorResult = keys.reduce((acc, key) => {
        //             acc[key.id] = "Error loading translation";
        //             return acc;
        //         }, {});
        //         resolve(errorResult); // Resolve with error message for each key
        //     });
    });
}
// Batch update labels dynamically
function updateLabels(keyIdPairs,onchangeoflanuguage) {


    getLabels_jsonlayout(keyIdPairs,onchangeoflanuguage)
        .then(labels => {
            keyIdPairs.forEach(pair => {
                $("#" + pair.id).html(labels[pair.id]);  // Dynamically update based on id
            });
        })
        .catch(error => {
            console.error("Error updating labels:", error);
        });
}



function changeButtonActionwithoutformrefresh(form_name,action_name,insertBtnid,clearBtnid,error,savebtntext,clearbtntext,action)
    {
       // alert(onchangeoflanuguage);
        if (error) $("#" + error).hide();

        updateLabels([
            { key: savebtntext, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'N');
        if(action == 'insert')
        {
            $("#" + action_name).val("insert");
            document.getElementById(insertBtnid).style.backgroundColor = "#b71362";
        }
        if(action == 'update')
        {
            $("#" + action_name).val("update");
            document.getElementById(insertBtnid).style.backgroundColor = "#0262af";
        }
        var validator = $("#"+form_name).validate(); // Get validator instance
        // Reset form fields
        // $("#"+form_name)[0].reset();
        // Reset validation messages
        validator.resetForm();
        // Remove error and valid classes
        $("#"+form_name).find("label.error").remove();
        $("#"+form_name).find(".error").removeClass("error");
        $("#"+form_name).find(".valid").removeClass("valid");
        // Reapply JSON messages to ensure they don't disappear
        const language = window.localStorage.getItem('lang') || 'en';
        validator.settings.messages = errorMessages[language];
        document.getElementById(insertBtnid).style.color = "#FFFFFF";
        window.scrollTo(0, 0);
    }


function changeButtonAction(form_name,action_name,insertBtnid,clearBtnid,error,savebtntext,clearbtntext,action)
    {
       // alert(onchangeoflanuguage);
        if (error) $("#" + error).hide();
        $("#" + form_name)[0].reset();
        updateLabels([
            { key: savebtntext, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'N');
        if(action == 'insert')
        {
            $("#" + action_name).val("insert");
            document.getElementById(insertBtnid).style.backgroundColor = "#b71362";
        }
        if(action == 'update')
        {
            $("#" + action_name).val("update");
            document.getElementById(insertBtnid).style.backgroundColor = "#0262af";
        }
        var validator = $("#"+form_name).validate(); // Get validator instance
        // Reset form fields
        $("#"+form_name)[0].reset();
        // Reset validation messages
        validator.resetForm();
        // Remove error and valid classes
        $("#"+form_name).find("label.error").remove();
        $("#"+form_name).find(".error").removeClass("error");
        $("#"+form_name).find(".valid").removeClass("valid");
        // Reapply JSON messages to ensure they don't disappear
        const language = window.localStorage.getItem('lang') || 'en';
        validator.settings.messages = errorMessages[language];
        document.getElementById(insertBtnid).style.color = "#FFFFFF";
        window.scrollTo(0, 0);
    }
    function changeButtonText(action,insertBtnid,clearBtnid,savebtntext,updatebtntext,clearbtntext)
    {
        if($('#'+action).val() == 'insert')
        {
            keyfor  =   savebtntext;
        }
        else
        {
            keyfor  =   updatebtntext;
        }
        updateLabels([
            { key: keyfor, id: insertBtnid },
            { key: clearbtntext, id: clearBtnid }
        ],'Y');
    }







function handleUnauthorizedError(errorcode) {
    window.location.href = "/error-page"; // Redirect to the error page

    // Prevent back button navigation
    history.pushState(null, null, "/error-page");
    window.onpopstate = function () {
        history.pushState(null, null, "/error-page");
    };
}








$(".text_special").on("keypress", function (event) {
    var charCode = event.which || event.keyCode;
    var charStr = String.fromCharCode(charCode);

    // Allow only letters (A-Z, a-z) and special characters , . / & -
    if (/^[a-zA-Z0-9 ,_.%/&-]$/.test(charStr)) {
        return true;
    } else {
        return false;
    }
});







$(".only_numbers").on("keypress", function (event) {
    if (event.charCode >= 48 && event.charCode <= 57)
        return true; // let it happen, don't do anything
    else return false;
});

document.addEventListener("DOMContentLoaded", function () {
    // Select all the select elements
    const selects = document.querySelectorAll(".form-select");

    // Function to handle color updates for the selected option
    function updateSelectColor() {
        const selectedOption = this.options[this.selectedIndex];

        // If the selected option is empty (default), set the text color to gray
        if (selectedOption.value === "") {
            this.style.color = "gray"; // Text color of the select itself
        } else {
            this.style.color = "black"; // Text color of the select itself
        }
    }

    // Iterate over each select element
    selects.forEach((select) => {
        // Initially update the color on page load
        updateSelectColor.call(select);

        // Add event listener for focus (when clicked)
        select.addEventListener("focus", function () {
            this.style.backgroundColor = "white"; // Set background color to white when focused

            // Update color for options when select box is focused
            const options = this.querySelectorAll("option");
            options.forEach((option) => {
                if (option.value === "") {
                    option.style.color = "gray"; // Set empty option color to gray
                } else {
                    option.style.color = "black"; // Set other options to black
                }
            });
        });

        // Add event listener for blur (when focus is lost)
        select.addEventListener("blur", function () {
            this.style.backgroundColor = ""; // Remove background color when focus is lost
            // Reset the color of the selected option based on its value
            updateSelectColor.call(this);
        });

        // Add event listener for change (when user selects an option)
        select.addEventListener("change", updateSelectColor);
    });
});

function updateSelectColorByValue(selectElements) {
    selectElements.forEach((selectElement) => {
        // Update the color of the select element based on the selected value
        const selectedOption =
            selectElement.options[selectElement.selectedIndex];

        // Apply color to the select element's text
        selectElement.style.color =
            selectedOption.value === "" ? "gray" : "black";

        // Apply color to all options inside the select element
        Array.from(selectElement.options).forEach((option) => {
            option.style.color = option.value === "" ? "gray" : "black";
        });
    });
}

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
function formatDateLocal(date) {
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    return `${year}-${month}-${day}`;
}

function isSameCalendarDate(firstDate, secondDate) {
    return firstDate.getFullYear() === secondDate.getFullYear()
        && firstDate.getMonth() === secondDate.getMonth()
        && firstDate.getDate() === secondDate.getDate();
}


  function init_datepicker(inputId, startDate, endDate, setdate = null, form = null,fromvalclr=null,tovalclr=null,type='null')
  {
    
    // Destroy any existing datepicker instance before re-initializing
    $("#" + inputId).datepicker("destroy");

    //let daysOfWeekDisabled = [];

    // if (form === "entryandexitmeetform") {
    //     daysOfWeekDisabled = [0,6]; // Disable Saturday and Sunday
    // } 
    // else {
    //     daysOfWeekDisabled = [0, 6]; // Disable Saturday and Sunday
    // }
    let daysOfWeekDisabled = [0,6];
    let allowedWeekendDates = [
    "2026-03-28", // allowed Saturday
    ];
    $("#" + inputId).datepicker({
        format: "dd/mm/yyyy",
        startDate: startDate,
        endDate: endDate,
        //daysOfWeekDisabled:daysOfWeekDisabled,
        autoclose: true,
        clearBtn: true,
	         beforeShowDay: function (date) {
            if (type === 'allowtoday' && isSameCalendarDate(date, new Date())) {
                return { enabled: true };
            }

	            const holidayName = getHoliday(date);
	            if (holidayName) {
	                return {
                    enabled: false,
                    tooltip: holidayName,
                    classes: 'holiday-red'
                };
            }
            
        const day = date.getDay(); // 0 = Sunday, 6 = Saturday
        const d = formatDateLocal(date); // Local yyyy-mm-dd

             if ((day === 0 || day === 6) && !allowedWeekendDates.includes(d)) {
      return {
        enabled: false,
        tooltip: "Weekend",
        classes: "weekend-gray"
      };
    }

    // 3️⃣ Enable everything else
    return { enabled: true };
        }
    }).on('changeDate clearDate', function (e) 
    {

        if (form === "entryandexitmeetform" && e.type === "changeDate")
            {
                const selectedDate = e.date;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                selectedDate.setHours(0, 0, 0, 0);
                if (selectedDate > today)
                {
                    var errormsg='Only today date is available for selection.';
                            passing_alert_value('Alert', errormsg, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    $('#confirmation_alert').css('z-index', 10000000);
                    $(this).datepicker('setDate', null);
                    return;
                }
            }
        if (e.type === 'clearDate' && inputId === fromvalclr && form === 'cleardateform') {
          
            $(`#${tovalclr}`).datepicker('setDate', null);


            let maxDate;
            let minDate;
        
            if (type === 'serviceperiod') {
               
                minDate = null;
                maxDate = new Date();
            } else {
                const ToDate = '<?php echo $Toquarter; ?>'; // Should be in a parseable format
                maxDate = new Date(ToDate);
                minDate = new Date();

            }
        
        
        
            init_datepicker(fromvalclr, minDate, maxDate, null, 'clear');
            init_datepicker(tovalclr, minDate, maxDate, null, 'clear');
        }
        
    });

    if(setdate)
     {
        if (form=='entryandexitmeetform') 
        {
           // alert(setdate);
            if(setdate!=='today')
            {
               $("#" + inputId).datepicker("setDate", setdate);
            }else
            {
                $("#" + inputId).datepicker("show");

            }

        }else
        {
            $("#" + inputId).datepicker("setDate", setdate);

        }

     }else 
     {
        if(form!=='clear')
        {
            $("#" + inputId).datepicker("show");

        }else
        {
            $("#" + inputId).datepicker();
        }
      }

   }






// function init_datepicker(inputId, startDate, endDate, setdate = null) {

//     alert(endDate);
//     // Initialize the datepicker with the provided options
//     $("#" + inputId).datepicker({
//         format: "dd/mm/yyyy", // Set the date format
//         startDate: startDate, // Set the start date
//         endDate: endDate, // Set the end date
//         autoclose: true, // Close the datepicker when a date is selected
//         beforeShowMonth: function(date) {
//             // Adjust the visibility of the "previous month" button
//             var prevButton = $(".ui-datepicker-prev");
//             var nextButton = $(".ui-datepicker-next");
            
//             if (date < startDate) {
//                 prevButton.hide(); // Hide previous month button if the date is before start date
//             } else {
//                 prevButton.show();
//             }

//             if (date > endDate) {
//                 nextButton.hide(); // Hide next month button if the date is after end date
//             } else {
//                 nextButton.show();
//             }
//         }
//     });

//     // If a setdate is provided, set the initial date
//     if (setdate) {
//         $("#" + inputId).datepicker("setDate", setdate); // Set the date to the provided date
//     } else {
//         $("#" + inputId).datepicker("show");
//     }
// }

// function init_datepicker(inputId, startDate, endDate, setdate = null) {
//     // Make sure startDate and endDate are valid Date objects
//     if (typeof startDate === 'string') {
//         startDate = new Date(startDate.split('/').reverse().join('-'));
//     }
//     if (typeof endDate === 'string') {
//         endDate = new Date(endDate.split('/').reverse().join('-'));
//     }

//     // Destroy any previous datepicker instance and re-initialize
//     $("#" + inputId).datepicker('destroy');
//     $("#" + inputId).datepicker({
//         format: "dd/mm/yyyy", // Set the date format
//         startDate: startDate, // Set the start date
//         endDate: endDate, // Set the end date
//         autoclose: true, // Close the datepicker when a date is selected
//         beforeShowMonth: function(date) {
//             // Adjust the visibility of the "previous month" button
//             var prevButton = $(".ui-datepicker-prev");
//             var nextButton = $(".ui-datepicker-next");
            
//             if (date < startDate) {
//                 prevButton.hide(); // Hide previous month button if the date is before start date
//             } else {
//                 prevButton.show();
//             }

//             if (date > endDate) {
//                 nextButton.hide(); // Hide next month button if the date is after end date
//             } else {
//                 nextButton.show();
//             }
//         }
//     });

//     // If setdate is provided, set the initial date
//     if (setdate) {
//         // Convert setdate to a Date object if it's a string
//         if (typeof setdate === "string") {
//             var dateArray = setdate.split('/');
//             setdate = new Date(dateArray[2], dateArray[1] - 1, dateArray[0]); // dd/mm/yyyy to Date object
//         }
        
//         $("#" + inputId).datepicker("setDate", setdate);
//     } else {
//         $("#" + inputId).datepicker("show");
//     }
// }



function fn_captilise_each_word(txtbox_name) {
    var value = $("#" + txtbox_name).val();
    text = value
        .toLowerCase()
        .split(" ")
        .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
        .join(" ");
    document.getElementById(txtbox_name).value = text;
    return true;
}

function capitalizeFirstLetter(txtbox_name) {
    const inputField = document.getElementById(txtbox_name);
    const value = inputField.value;

    // Capitalize first letter and keep the rest as it is
    inputField.value = value.charAt(0).toUpperCase() + value.slice(1);
    document.getElementById(txtbox_name).value = inputField.value;
    return true;
}

$(".name").on("keypress", function (event) {
    if (
        (event.charCode > 64 && event.charCode < 91) ||
        (event.charCode > 96 && event.charCode < 123) ||
        event.charCode == 32
    )
        return true;
    else return false;
});

// Allow Alphabets and Numbers
$(".alpha_numeric").on("keypress", function (event) {
    if (
        (event.charCode > 64 && event.charCode < 91) ||
        (event.charCode > 96 && event.charCode < 123) ||
        (event.charCode >= 48 && event.charCode <= 57) ||
        event.charCode == 32
    )
        return true; // let it happen, don't do anything
    else return false;
});

function ValidateEmail() {
    var email = document.getElementById("email").value;
    var lblError = document.getElementById("lblError");
    lblError.innerHTML = "";
    var expr =
        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!expr.test(email)) {
        lblError.innerHTML = "Invalid email address.";
    }
}

$("#email").on("keypress", function (event) {
    var regex = new RegExp("^[a-zA-Z0-9!#$%&'*+-/=?^_`{|}~@]+$");
    var key = String.fromCharCode(
        !event.charCode ? event.which : event.charCode
    );
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
});

function change_button_as_update(
    form_name,
    action_name,
    button_action,
    error,
    card_name,
    closebtn,
    key_label=''
) {
    if (error) $("#" + error).hide();

    if (card_name) $("#" + card_name).show();
    if (closebtn) $("#" + closebtn).html("Close");

    $("#" + form_name).show();
    $("#" + form_name)[0].reset();
    $("#" + action_name).val("update");
    // $("#" + button_action).html(get_jsonvalue("update"));
    $("#" + button_action).val("Update");
    $("#" + button_action).html("Update");
    $("#" + button_action).attr("key", key_label);

    document.getElementById(button_action).style.backgroundColor = "#0262af";
    document.getElementById(button_action).style.color = "#FFFFFF";
    window.scrollTo(0, 0);
}

function change_button_as_insert(
    form_name,
    action_name,
    button_action,
    error,
    closebtn
) {
    if (error) $("#" + error).hide();
    $("#" + form_name)[0].reset();
    $("#" + action_name).val("insert");
    // $("#" + button_action).html(get_jsonvalue("insert"));
    $("#" + button_action).val("Save");
    $("#" + button_action).html("Save Draft");

    document.getElementById(button_action).style.backgroundColor = "#b71362";
    document.getElementById(button_action).style.color = "#FFFFFF";
    if (closebtn) $("#" + closebtn).html("Clear");
}

// Helper function to format a date to dd/mm/yyyy
function formatDate(date) {
    var day = date.getDate().toString().padStart(2, "0"); // Ensure 2 digits for day
    var month = (date.getMonth() + 1).toString().padStart(2, "0"); // Ensure 2 digits for month
    var year = date.getFullYear(); // Get the full year
    return day + "/" + month + "/" + year; // Return formatted date
}

// Helper function to convert dd/mm/yyyy to yyyy-mm-dd format (required for <input type="date>")
function convertToInputDateFormat(date) {
    var parts = date.split("/");
    return parts[2] + "-" + parts[1] + "-" + parts[0]; // Convert to yyyy-mm-dd
}

function convertDateFormatYmd_ddmmyy(dateString) {
    // Assuming the input date format is "yyyy-mm-dd"
    const [year, month, day] = dateString.split("-");

    // Convert to dd/mm/yyyy format
    return `${day}/${month}/${year}`;
}

function passing_extra_large_alert(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type,
    alert_key_label=''
) {
    const element = document.getElementById("process_button");
    element.classList.remove("btn-danger");

    $("#ok_button").hide();
    $("#cancel_button").hide();
    $("#process_button").show();
    // $("#process_button").html("Ok");
    $("#cancel_button").show();
    element.classList.add("btn-success");

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    $(".modal-header").css({
        "background-color": selectedcolor,
    });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_header_id).attr("key", alert_key_label);

    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");


    // #593320
}

function passing_large_alert(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type,
    alert_key_label=''
) {
    const element = document.getElementById("process_button");
    element.classList.remove("btn-danger");

    $("#ok_button").hide();
    $("#cancel_button").hide();
    $("#process_button").show();
    // $("#process_button").html("Ok");
    $("#cancel_button").show();
    element.classList.add("btn-success");

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    $(".modal-header").css({
        "background-color": selectedcolor,
    });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_header_id).attr("key", alert_key_label);

    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");


    // #593320
}

function passing_alert_value(
    alert_header,
    alert_body,
    alert_name,
    alert_header_id,
    alert_body_id,
    alert_type
) {
    if (alert_type == "confirmation_alert") {
        $("#process_button").hide();
        $("#ok_button").show();
        $("#cancel_button").hide();
        $("#button_close").hide();
    }
    if (alert_type == "delete_alert") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-success");
        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        $("#process_button").html("Delete");
        $("#cancel_button").show();
        // Add a class (quote) to the element
        element.classList.add("btn-danger");
    }
    if (alert_type == "forward_alert") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-danger");

        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        // $("#process_button").html("Ok");
        $("#cancel_button").show();
        element.classList.add("btn-success");
    }
    if (alert_type == "confirmation_alert_with_function") {
        const element = document.getElementById("process_button");
        element.classList.remove("btn-danger");
        $("#close_button").hide();
        $("#ok_button").hide();
        $("#cancel_button").hide();
        $("#process_button").show();
        // $("#process_button").html("Ok");
        $("#cancel_button").hide();
        // $('#button_close').hide();
        element.classList.add("btn-success");
    }

    var selectedcolor = localStorage.getItem("selectedColor");
    if (!selectedcolor) selectedcolor = "#3365b7";

    // $(".modal-header").css({ "background-color": selectedcolor });
    $("#" + alert_header_id).html(alert_header);
    $("#" + alert_body_id).html(alert_body);

    $("#" + alert_name).modal("show");

    // #593320
}






 function change_dateformat(inputDate)
 {
    // Create a Date object
    let dateObj = new Date(inputDate);

    // Format the date as "dd-mm-yyyy"
    let day = String(dateObj.getDate()).padStart(2, '0'); // Ensure two digits for day
    let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Ensure two digits for month
    let year = dateObj.getFullYear(); // Get the full year

    // Get the time in 12-hour format with AM/PM
    let hours = dateObj.getHours();
    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12; // Convert to 12-hour format
    hours = hours ? hours : 12; // Handle 0 hour as 12 (midnight)

    // Combine the date and time in the desired format
    let formattedDate = day + '-' + month + '-' + year + ' ' + hours + ':' + minutes + ' ' + ampm;

   return formattedDate; // Output: 14-12-2024 10:14 PM

 }

 function makedropdownempty(id, placeholder) {
    $("#" + id).empty();
    $("#" + id).append("<option value=''>" + placeholder + "</option>");
}


var labels = {}; // To store the loaded labels
var labelsLoaded = false;
// Function to load labels.json dynamically
async function loadLabels() {
  try {
    const response = await fetch('/json/layout.json'); // Path to your JSON file
    labels = await response.json();
    labelsLoaded = true;
  } catch (error) {
    console.error('Error loading labels:', error);
  }
}


// Function to get label based on the selected language
async function getLabel(key) {
    // If labels are not yet loaded, wait for them to load
    if (!labelsLoaded) {
      await loadLabels();
    }

    const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en'

    return labels[lang] && labels[lang][key] ? labels[lang][key] : key; // Fallback to the key if missing
  }

  // Initialize the labels when the script is loaded
  loadLabels();

  function getLanguage(onchange) {
    let lang;

    if (onchange === 'Y') {
        lang = $('#translate').val();

    } else {

        lang = window.localStorage.getItem('lang') || 'en';
    }

    return lang === 'ta' ? 'ta' : 'en';
}
function ChangeDateFormat(timestamp) {
    const dateParts = timestamp.split(' ');

    // If the timestamp contains a space, it includes time
    const hasTime = dateParts.length > 1;

    const date = new Date(timestamp);

    // Define options for formatting the date
    const options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };

    // Format the date
    const formattedDate = date.toLocaleString('en-GB', options).replace(',', '').replace(/:/g, ':').replace(/\//g, '-');

    // If no time, return only the date
    if (!hasTime) {
        return formattedDate.split(' ')[0];  // Date only
    }

    return formattedDate;  // Date with time
}

function downloadAndPreview(fileUrl) {
    // Trigger download
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = ''; // Hints to browser to download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
   
}
const secretKey = CryptoJS.enc.Base64.parse(window.APP_CONFIG.AES_SECRET_KEY);
const iv = CryptoJS.enc.Utf8.parse(window.APP_CONFIG.AES_IV);

    function encryptPassword(password) {
        let encrypted = CryptoJS.AES.encrypt(password, secretKey, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7
        });

        return CryptoJS.enc.Base64.stringify(encrypted.ciphertext); // ✅ Convert to Base64 before sending
    }
// // Example usage
// const timestamp1 = '2025-02-07 19:12:00';  // Example timestamp
// const timestamp2 = '2025-02-07';  // Example date only
// console.log(ChangeDateFormat(timestamp1));  // Output: "07-02-2025 07:12 PM"
// console.log(ChangeDateFormat(timestamp2));  // Output: "07-02-2025"
 function populate_parafulldetails(lang, para_data, prarahistory_det, modal_id, modalbody_id, modaltable_id) {
        console.log("Institution:"+para_data.instename)
    // Helper: language-based value with fallback
    const v = (en, ta) => {
        if (lang === 'ta') return ta || en || '-';
        return en || ta || '-';
    };

    // Helper: normal text
    const t = (val) => val ?? '-';
    //Institution
     $('#inst_val').text(
        v(para_data.instename, para_data.insttname)
    );
    // Main / Sub object

    $('#main_obj').text(
        v(para_data.objectionename, para_data.objectiontname)
    );

    $('#sub_obj').text(
        v(para_data.subobjectionename, para_data.subobjectiontname)
    );

    // Type of Para
    $('#typeofpara_val').text(
        v(para_data.typeofparaename, para_data.typeofparatname)
    );

    // State of Para
    $('#stateofpara_val').text(
        v(para_data.stateofparaename, para_data.stateofparatname)
    );

    // Para Number
    $('#parano_val').text(t(para_data.parano));

    // Status
    $('#status_val').text(
        para_data.statusflag === 'Y' ? 'Yes' :
        para_data.statusflag === 'N' ? 'No' : '-'
    );

    // 🔥 Amount Involved (₹ + bold)
    let amount = para_data.amtinvolved
        ? `₹ ${Number(para_data.amtinvolved).toLocaleString('en-IN')}`
        : '-';

    $('#amt_val').html(`<strong>${amount}</strong>`);

    // Severity
     $('#sev_val').text(
        v(para_data.severityelname, para_data.severitytlname)
    );
    //Scheme
    $('#scheme_val').text(
        v(para_data.auditeeschemeelname, para_data.auditeeschemetlname)
    );

    // Irregularities
    $('#irreg_val').text(
        v(para_data.irregularitieselname, para_data.irregularitiestlname)
    );

    // Irregularities Category
    $('#irreg_cat_val').text(
        v(para_data.irregularitiescatelname, para_data.irregularitiescattlname)
    );

    // Irregularities SubCategory
    $('#irg_subcat_val').text(
        v(para_data.irregularitiessubcatelname, para_data.irregularitiessubcattlname)
    );

    // Gist of Observations
    $('#gist_val').text(para_data.slipdetails ?? '-');

    // Remarks (HTML allowed)
    $('#remarks_val').html(para_data.remarks ?? '-');

        populateLiabilityDetails(para_data.liabilitydel);
         populate_paradetails(prarahistory_det)
    // ✅ Show modal (Bootstrap 5)
    const modalEl = document.getElementById(modal_id);
    new bootstrap.Modal(modalEl).show();
}
// function populate_paradetails(para_data, lang) {

//     const modelbody = $('#para_history_container');
//     modelbody.empty();

//     let editorIds = [];

//     /* ---------- helpers ---------- */
//     const infoBlock = (icon, heading, value) => `
//     <div class="mb-3">
//         <div class="d-flex align-items-center mb-1">
//             <i class="ti ti-${icon} text-primary fs-5 me-2"></i>
//             <div class="fw-bold text-uppercase small">
//                 ${heading}
//             </div>
//         </div>

//         <div class="ps-4 fw-medium">
//             ${value || '-'}
//         </div>

//         <hr class="my-2 opacity-25">
//     </div>
//     `;

//     const divider = () => `<hr class="my-2 opacity-25">`;

//  const renderAttachments = (files, containerId, heading = 'Attachments') => {
//     if (!files || files === '---') return '';

//     let parsedFiles = getfile(files); // [{name, path, size}]

//     setTimeout(() => {
//         let html = parsedFiles.map(f => `
//             <div class="d-flex align-items-center mb-2 attachment-row">
//                 <i class="ti ti-paperclip text-primary fs-5 me-2"></i>
//                 <a href="${f.path}"
//                    target="_blank"
//                    class="text-decoration-none text-dark fw-medium">
//                     ${f.name}
//                 </a>
//             </div>
//         `).join('');

//         $('#' + containerId).html(html);
//         }, 0);

//         return `
//             <div class="mt-3">
//                 <div class="fw-bold mb-1">${heading}</div>
//                 <div id="${containerId}" class="ps-1"></div>
//                 <hr class="my-2 opacity-25">
//             </div>
//         `;
//     };


//     /* ---------- role rules ---------- */

//     const showRemarksAndAttachments = ['A','I','AD'];
//     const showMeetingDetails = ['SL','DE','DL'];
//     const showActionTaken = ['A','AD','SL','DE','DL'];

//     const roleMap = {
//         I:  { bg:'auditee_div', body:'auditee_body', label:'Auditee' },
//         A:  { bg:'auditor_div', body:'auditor_body', label:'PSA Auditor' },
//         AD: { bg:'ad_div', body:'ad_body', label:'PSA AD' },
//         DL: { bg:'dl_div', body:'dl_body', label:'District HLC' },
//         DE: { bg:'dept_div', body:'dept_body', label:'Department HLC' },
//         SL: { bg:'state_div', body:'state_body', label:'State HLC' }
//     };

//     /* ---------- main loop ---------- */

//     para_data.forEach((data, index) => {

//         let editorId = `editor${index}`;
//         editorIds.push(editorId);

//         let collapseId = `historyCollapse${index}`;
//         let headerId = `historyHeading${index}`;

//         let forwardedOn = ChangeDateFormat(data.forwardedon) || '-';
//         let meetingDate = '-';

//         if (data.mom_date) {
//             const formatted = convertDateFormatYmd_ddmmyy(data.mom_date);
//             meetingDate = formatted && formatted !== 'Invalid Date' ? formatted : '-';
//         }


//         const role = roleMap[data.actroleactioncode] || roleMap['SL'];

//         let actionName = lang === 'ta' ? data.actiontname : data.actionename;

//         /* ---------- remarks parsing ---------- */
//         let remarkContent = 'No remarks provided';
//         try {
//             if (data.para_remarks) {
//                 remarkContent = JSON.parse(data.para_remarks).content || remarkContent;
//             } else if (data.para_historyremarks) {
//                 remarkContent = data.para_historyremarks;
//             }
//         } catch {
//             remarkContent = data.para_remarks || remarkContent;
//         }

//         /* ---------- body content ---------- */

//         let bodyContent = '';

//         if (showActionTaken.includes(data.actroleactioncode)) {
//             bodyContent += infoBlock('list-check', 'Action Taken', actionName);
//             bodyContent += divider();
//         }

//         if (showMeetingDetails.includes(data.actroleactioncode)) {
//             bodyContent += infoBlock('calendar', 'Meeting Date', meetingDate);
//             bodyContent += renderAttachments(
//                 data.minutesfileupload,
//                 `minutes_filediv_${index}`,
//                 'Minutes Document'
//             );
//             bodyContent += divider();
//         }

//         if (showRemarksAndAttachments.includes(data.actroleactioncode)) {
//             bodyContent += infoBlock('file', 'Remarks', remarkContent);
//             bodyContent += renderAttachments(
//                 data.auditeefileupload,
//                 `history_filediv_${index}`,
//                 'Attachments'
//             );
//         }

//         /* ---------- final html ---------- */

//         let html = `
//         <div class="accordion mb-2">
//             <div class="accordion-item border-0 shadow-sm">
//                 <h2 class="accordion-header" id="${headerId}">
//                     <button class="accordion-button collapsed ${role.bg}"
//                             type="button"
//                             data-bs-toggle="collapse"
//                             data-bs-target="#${collapseId}">
//                         <strong>${role.label}</strong>
//                         <span class="ms-3 text-muted small">${forwardedOn}</span>
//                     </button>
//                 </h2>

//                 <div id="${collapseId}" class="accordion-collapse collapse">
//                     <div class="accordion-body ${role.body} rounded-3 bg-opacity-10">
//                         ${bodyContent}
//                     </div>
//                 </div>
//             </div>
//         </div>
//         `;

//         modelbody.append(html);
//     });

//  //   initializeEditors(editorIds);
// }

function populate_paradetails(para_data, lang) {

    const modelbody = $('#para_history_container');
    modelbody.empty();

    if (!para_data || para_data.length === 0) {
        modelbody.append(`
                <div class="text-center text-muted py-4">
                    <i class="ti ti-history fs-1 mb-2 d-block"></i>
                    <span class="fw-medium">No History Available</span>
                </div>
            `);
        return;
    }
    let editorIds = [];

    /* ---------- helpers ---------- */
    const infoBlock = (icon, heading, value) => `
    <div class="mb-3">
        <div class="d-flex align-items-center mb-1">
            <i class="ti ti-${icon} text-primary fs-5 me-2"></i>
            <div class="fw-bold text-uppercase small">
                ${heading}
            </div>
        </div>

        <div class="ps-4 fw-medium">
            ${value || '-'}
        </div>

        <hr class="my-2 opacity-25">
    </div>
    `;

    const divider = () => `<hr class="my-2 opacity-25">`;

    const renderAttachments = (files, containerId, heading = 'Attachments') => {
        if (!files || files === '---') return '';

        let parsedFiles = getfile(files); // [{name, path, size}]

        setTimeout(() => {
            let html = parsedFiles.map(f => `
            <div class="d-flex align-items-center mb-2 attachment-row">
                <i class="ti ti-paperclip text-primary fs-5 me-2"></i>
                <a href="${f.path}"
                   target="_blank"
                   class="text-decoration-none text-dark fw-medium">
                    ${f.name}
                </a>
            </div>
        `).join('');

            $('#' + containerId).html(html);
        }, 0);

        return `
            <div class="mt-3">
                <div class="fw-bold mb-1">${heading}</div>
                <div id="${containerId}" class="ps-1"></div>
                <hr class="my-2 opacity-25">
            </div>
        `;
    };


    /* ---------- role rules ---------- */

    const showRemarksAndAttachments = ['A', 'I', 'AD'];
    const showMeetingDetails = ['SL', 'DE', 'DL'];
    const showActionTaken = ['A', 'AD', 'SL', 'DE', 'DL'];

    const roleMap = {
        I: { bg: 'auditee_div', body: 'auditee_body', label: 'Auditee' },
        A: { bg: 'auditor_div', body: 'auditor_body', label: 'PSA Auditor' },
        AD: { bg: 'ad_div', body: 'ad_body', label: 'PSA AD' },
        DL: { bg: 'dl_div', body: 'dl_body', label: 'District HLC' },
        DE: { bg: 'dept_div', body: 'dept_body', label: 'Department HLC' },
        SL: { bg: 'state_div', body: 'state_body', label: 'State HLC' }
    };

    /* ---------- main loop ---------- */

    para_data.forEach((data, index) => {

        let editorId = `editor${index}`;
        editorIds.push(editorId);

        let collapseId = `historyCollapse${index}`;
        let headerId = `historyHeading${index}`;

        let forwardedOn = ChangeDateFormat(data.forwardedon) || '-';
        let meetingDate = '-';

        if (data.mom_date) {
            const formatted = convertDateFormatYmd_ddmmyy(data.mom_date);
            meetingDate = formatted && formatted !== 'Invalid Date' ? formatted : '-';
        }


        const role = roleMap[data.actroleactioncode] || roleMap['SL'];

        let actionName = lang === 'ta' ? data.actiontname : data.actionename;

        /* ---------- remarks parsing ---------- */
        let remarkContent = 'No remarks provided';
        try {
            if (data.para_remarks) {
                remarkContent = JSON.parse(data.para_remarks).content || remarkContent;
            } else if (data.para_historyremarks) {
                remarkContent = data.para_historyremarks;
            }
        } catch {
            remarkContent = data.para_remarks || remarkContent;
        }

        /* ---------- body content ---------- */

        let bodyContent = '';

        if (showActionTaken.includes(data.actroleactioncode)) {
            bodyContent += infoBlock('list-check', 'Action Taken', actionName);
            bodyContent += divider();
        }

        if (showMeetingDetails.includes(data.actroleactioncode)) {
            bodyContent += infoBlock('calendar', 'Meeting Date', meetingDate);
            bodyContent += renderAttachments(
                data.minutesfileupload,
                `minutes_filediv_${index}`,
                'Minutes Document'
            );
            bodyContent += divider();
        }

        if (showRemarksAndAttachments.includes(data.actroleactioncode)) {
            bodyContent += infoBlock('file', 'Remarks', remarkContent);
            bodyContent += renderAttachments(
                data.auditeefileupload,
                `history_filediv_${index}`,
                'Attachments'
            );
        }

        /* ---------- final html ---------- */

        let html = `
        <div class="accordion mb-2">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="${headerId}">
                    <button class="accordion-button collapsed ${role.bg}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}">
                        <strong>${role.label}</strong>
                        <span class="ms-3 text-muted small">${forwardedOn}</span>
                    </button>
                </h2>

                <div id="${collapseId}" class="accordion-collapse collapse">
                    <div class="accordion-body ${role.body} rounded-3 bg-opacity-10">
                        ${bodyContent}
                    </div>
                </div>
            </div>
        </div>
        `;

        modelbody.append(html);
    });

    //   initializeEditors(editorIds);
}
function populateLiabilityDetails(liabilitydel) {

    const $tbody = $('#liability_det_table');
    $tbody.empty();

    // Hide card if empty
    if (!liabilitydel || liabilitydel.trim() === '') {
        $('#liability_tab').addClass('hide_this');
        return;
    }

    $('#liability_tab').removeClass('hide_this');

    // Split multiple rows
    const rows = liabilitydel.split(',');

    rows.forEach(row => {

        /*
            Expected format:
            01-76415010004005-AAAAAA-Secretary-58557-2027-09-Y
        */

        const parts = row.split('~');

        if (parts.length < 8) return;

        const [
            typeCode,
            number,
            name,
            designation,
            amount,
            retirementYear,
            retirementMonth,
            retirementFlag
        ] = parts;

        const amt = amount
            ? `₹ ${Number(amount).toLocaleString('en-IN')}`
            : '-';

        const retirementText =
            retirementFlag === 'Y'
                ? '<span class="text-danger fw-bold">Most Urgent</span>'
                : 'Normal';

        const tr = `
            <tr>
                <td>${getLiabilityType(typeCode)}</td>
                <td>${number || '-'}</td>
                <td>${name || '-'}</td>
                <td>${designation || '-'}</td>
                <td><strong>${amt}</strong></td>
                <td>${retirementText}</td>
                <td>${getMonthName(retirementMonth)}</td>
                <td>${retirementYear || '-'}</td>
                <td>-</td>
            </tr>
        `;

        $tbody.append(tr);
    });
}


function getLiabilityType(code) {
    switch (code) {
        case '01': return 'EPF No';
        case '02': return 'CPS No';
        case '03': return 'IFHRMS No';
        default: return '-';
    }
}

function getMonthName(monthNo) {
    const months = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];
    return months[Number(monthNo) - 1] || '-';
}
    function getfile(filearray) {
        return files = filearray.split(',').map((fileDetail, index) => {
            const [name, path, size, fileuploadid] = fileDetail.split('-');
            return {
                id: index + 1,
                name,
                path,
                size,
                fileuploadid
            };
        });
    }
