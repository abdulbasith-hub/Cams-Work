@section('content')

@section('title', 'Work Allocation Report')
@extends('index2')
@include('common.alert')
@php
$sessionmainobjectiondel = session('charge');

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditPeriod_head">Audit Period</div>
            <div class="card-body">
                <form id="auditperiodform" name="auditperiodform">
                    <!-- <input type="text" name="workallocation" id="workallocation"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fromYearDropdown" class="form-label lang" key="fromyear">From Year:</label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="fromYearDropdown"
                                name="fromYearDropdown">
                                <option value="" data-name-en="---Choose Year---"
                                    data-name-ta="---ஆண்டைத் தேர்ந்தெடுக்கவும்---">---Choose Year---</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="toYearDropdown" class="form-label lang" key="toyear">To Year:</label>
                            <select id="toYearDropdown" name="toYearDropdown" class="form-select mr-sm-2 lang-dropdown">
                                <option value="" data-name-en="---Choose Year---"
                                    data-name-ta="---ஆண்டைத் தேர்ந்தெடுக்கவும்---">---Choose Year---</option>
                            </select>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="active_sts_flag">Active Status</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3 mb-3">
                                    <input class="form-check-input " type="radio" name="status" id="statusYes"
                                        value="Y" checked>
                                    <label class="form-check-label lang" key="statusyes" for="statusYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="status" id="statusNo"
                                        value="N">
                                    <label class="form-check-label lang" key="statusno" for="statusNo">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-md-3 mx-auto text-center">
                            <!-- Adding text-center to center the content inside -->
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="auditperiod" id="auditperiod" value="" />

                            <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                id="buttonaction" name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                style="height:34px;font-size: 13px;" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="auditPeriod_table">Audit Period Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="auditperiodtable"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="fromyear">From Year</th>
                                    <!-- <th class="lang" key="category">Category</th> -->
                                    <th class="lang align-middle text-center" key="toyear">To Year</th>

                                    <th class="lang align-middle text-center" key="sts_flag">Status</th>
                                    <th class="all lang align-middle text-center" key="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class='hide_this'>
                    <center>No Data Available</center>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Include jQuery and Bootstrap -->


<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Download Button End -->

<script>
    function populateDropdown(dropdownId, years) {
        const dropdown = document.getElementById(dropdownId);
        // Clear existing options
        years.forEach(year => {
            let option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            dropdown.appendChild(option);
        });

        dropdown.addEventListener("change", function() {
            console.log(`${dropdownId} Selected Year:`, this.value);
        });
    }

    populateDropdown("fromYearDropdown", [2019, 2020, 2021, 2022, 2023, 2024, 2025]);

    populateDropdown("toYearDropdown", [2024, 2025]);


    let table;
    let dataFromServer = [];

    $(document).ready(function() {
        $('#auditperiodform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);

        $('#translate').change(function() {
            const lang = $('#translate').val();
            updateTableLanguage(lang);
        });




    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('auditperiod.auditperiod_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                console.log("Success Response:", json);
                if (json.data && json.data.length > 0) {
                    //console.log(json.data);
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            }
        });
    }





    $("#translate").change(function() {
        var lang = getLanguage('Y');
        updateTableLanguage(lang);

    });

    function renderTable(language) {
        //   const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#auditperiodtable')) {
            $('#auditperiodtable').DataTable().clear().destroy();
        }

        table = $('#auditperiodtable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            "columns": [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: "fromyear"
                },
                {
                    data: "toyear"
                },
                // {
                //     data: "majorworkallocationtypetname"
                // },
                {
                    data: "statusflag",
                    render: (data) => {
                        return data === 'Y' ?
                            `<button type="button" class="btn btn-primary btn-sm">Active</button>` :
                            `<button type="button" class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">Inactive</button>`;
                    },
                    className: 'text-center'
                },
                {
                    data: "encrypted_auditperiod",
                    render: function(data, type, row) {
                        return `<center><a class="btn editicon editauditperioddel" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
                    }
                }
            ],
            "columnDefs": [{
                    render: function(data) {
                        return "<div class='text-wrap width-200'>" + data + "</div>";
                    },
                    targets: 1
                },
                {
                    render: function(data) {
                        return "<div class='text-wrap width-100'>" + data + "</div>";
                    },
                    targets: [2, 3] // Set width for columns
                }
            ],
            "initComplete": function(settings, json) {
                $("#auditperiodtable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "dom": '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
            "buttons": [{
                extend: "excelHtml5",
                text: window.innerWidth > 768 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' : '<i class="fas fa-download"></i>',
                title: 'Work Allocation Report',
                exportOptions: {
                    columns: ':not(:last-child)' // Excluding the last column (Action column)
                },
                className: window.innerWidth > 768 ? 'btn btn-info' : 'btn btn-info btn-sm' // Full button on desktop, smaller on mobile
            }],
            "pagingType": "simple_numbers",
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [
                [10, 50, -1], // Full options for Desktop
                [10, 25, 50, -1] // Compressed options for Mobile
            ],
            "fnDrawCallback": function() {
                let $pagination = $('.dataTables_paginate');
                let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

                // Function to adjust pagination and info text based on window width
                function adjustView() {
                    if ($(window).width() <= 768) {
                        // Mobile View Adjustments
                        $(".dataTables_filter input").css({
                            "width": "100px",
                            "font-size": "12px",
                            "padding": "4px"
                        }); // Smaller search box
                        $(".dt-buttons .btn").addClass("btn-sm"); // Smaller download button

                        // Compress pagination display to show only first & last buttons
                        let totalPages = $pages.length;
                        $pages.each(function(index) {
                            if (index !== 0 && index !== totalPages - 1) {
                                $(this).hide();
                            }
                        });

                        // Display "Showing x to y of z entries" on a separate row in mobile view
                        $(".dataTables_info").css("display", "block");
                        $(".dataTables_info").css("text-align", "center");
                        $(".dataTables_info").css("margin-bottom",
                            "20px"); // Optional: Align the text to center
                    } else {
                        // Desktop View Adjustments
                        $(".dataTables_info").css("display", "inline-block");
                        $(".dataTables_filter input").css({
                            "width": "auto",
                            "font-size": "14px",
                            "padding": "8px"
                        }); // Reset search box style
                        $(".dt-buttons .btn").removeClass("btn-sm"); // Reset download button size
                        // Show all pagination buttons
                        $pages.show();
                    }
                }

                // Call the function initially
                adjustView();

                // Call the function when the window is resized
                $(window).resize(function() {
                    adjustView();
                });
            }
        });

        $(window).resize(function() {
            table.buttons(0).text(
                window.innerWidth > 768 ?
                '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
                '<i class="fas fa-download"></i>'
            );
        });
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#auditperiodtable')) {
            $('#auditperiodtable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    $("#translate").change(function() {
        var lang = getLanguage('Y');
        // updateTableLanguage(lang);

    });




    $("#auditperiodform").validate({
        rules: {
            fromYearDropdown: {
                required: true,
            },
            // category: {
            //     required: true
            // },
            toYearDropdown: {
                required: true
            },
            status: {
                required: true
            },

        },
        messages: {
            fromYearDropdown: {
                required: "Select From Year",
            },
            // category: {
            //     required: "Select a category",
            // },
            toYearDropdown: {
                required: "Select To Year",
            },
            status: {
                required: "Select a status",
            },
        }
    });
    $("#buttonaction").on("click", function(event) {
        // Prevent form submission (this stops the page from refreshing)
        event.preventDefault();

        //Trigger the form validation
        if ($("#auditperiodform").valid()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = $('#auditperiodform').serializeArray();

            $.ajax({
                url: "{{ route('auditperiod.auditperiod_insertupdate') }}", // URL where the form data will be posted
                type: 'POST',
                data: formData,
                success: async function(response) {
                    if (response.success) {
                        reset_form(); // Reset the form after successful submission
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        //  table.ajax.reload();
                        initializeDataTable(window.localStorage.getItem('lang'));


                    } else if (response.error) {
                        // Handle errors if needed
                        // console.log(response.error);
                    }
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.message ||
                        'An unknown error occurred';

                    // Displaying the error message
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');


                    // Optionally, log the error to console for debugging
                    console.error('Error details:', xhr, status, error);
                }
            });

        } else {

        }



    });

    function auditperiodForm(audit) {
        $('#display_error').hide();
        change_button_as_update('auditperiodform', 'action', 'buttonaction', 'display_error', '', '');
        // $('#catcode').val(mainobjection.catcode);
        $('#fromYearDropdown').val(audit.fromyear);
        $('#toYearDropdown').val(audit.toyear);
        // $('#deptcode').val(workallocation.deptcode);
        $('#auditperiod').val(audit.encrypted_auditperiod);
        //alert(id);
        populateStatusFlag(audit.statusflag);
        // $('#deptcode').val(workallocation.deptcode)

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    $(document).on('click', '.editauditperioddel', function() {
        const id = $(this).attr('id');
        // console.log(id);
        // alert(id);
        if (id) {
            reset_form();
            $('#auditperiod').val(id);
            //  console.log($('#workallocation'));
            // alert(id);
            $.ajax({
                url: "{{ route('auditperiod.auditperiod_fetchData') }}",
                method: 'POST',
                data: {
                    auditperiodid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            auditperiodForm(response.data[0]); // Populate form with data
                        } else {
                            alert('Audit Period data is empty');
                        }
                    } else {
                        alert('Audit Period not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });


    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        $('#auditperiodform')[0].reset();
        $('#auditperiodform').validate().resetForm();
        change_button_as_insert('auditperiodform', 'action', 'buttonaction', 'display_error', '', '');
        // getCategoriesBasedOnDept('', selectedCatcode = null);

        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection