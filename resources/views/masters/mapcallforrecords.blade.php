@section('content')
    @extends('index2')
    @include('common.alert')
    @php
        $sessionmainobjectiondel = session('charge');
    @endphp

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color">Map Call for records</div>
                <div class="card-body">
                    <form id="mapcallforrecords" name="mapcallforrecords">
                        <!-- <input type="text" name="workallocation" id="workallocation"> -->
                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="department" for="dept">Department</label>

                                <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                                <select class="form-select mr-sm-2" id="deptcode" name="deptcode"
                                    onchange="getCategoriesBasedOnDept(this.value,''); getCallforrecordsBasedOnDept(this.value, '');">
                                    <option value="">Select Department</option>

                                    @if (!empty($dept) && count($dept) > 0)
                                        @foreach ($dept as $department)
                                            <option value="{{ $department->deptcode }}">
                                                {{ $department->deptelname }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled>No Departments Available</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="category" for="category">Category</label>
                                <select class="form-select mr-sm-2" id="category" name="category">
                                    <option value=''>Select Category Name</option>
                                    <option value="" disabled id="no-district-option">No Category Available Name
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3" id="callforrecords">
                                <label class="form-label required lang" key="mapcallforrecords" for="mapcallforrecords">Call
                                    for records</label>

                                <select class="form-select mr-sm-2" id="mapcallforrecordid" name="mapcallforrecordid">
                                    <option value="">Select Call for records Name</option>
                                    <option value="" disabled id="no-district-option">No Call For Records Available
                                    </option>


                                    {{-- @if (!empty($records) && count($records) > 0)
                                        @foreach ($records as $rec)
                                            <option value="{{ $rec->callforrecordsid }}">
                                                {{ $rec->callforrecordsename }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled>No Call for Records Available</option>
                                    @endif --}}
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="sts_flag">Status</label>
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

                        <!-- <div class="row">
                                                                                        <div class="col-md-4  mx-auto">
                                                                                            <button class="btn btn-success mt-3" type="submit"> Submit </button>
                                                                                            <button class="btn btn-danger mt-3" type="submit"> Cancel </button>
                                                                                        </div>
                                                                                    </div> -->
                        <div class="row">
                            <div class="col-md-3 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <input type="hidden" name="map_callforrecords" id="map_callforrecords" value="" />

                                <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert"
                                    id="buttonaction" name="buttonaction">Save</button>
                                <button type="button" class="btn btn-danger mt-3  lang"
                                    style="height:35px;font-size: 13px;" key="clear" id="reset_button"
                                    onclick="reset_form()">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card_border">
                <div class="card-header card_header_color">Map Call For Records Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="mapcallforrecordstable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang align-middle text-center" key="s_num">S.No</th>
                                        <!-- <th class="lang" key="department">Department</th> -->
                                        <th class="lang align-middle text-center" key="category">Category</th>
                                        <th class="lang align-middle text-center" key="mapcallforrecords">Call For Records
                                        </th>
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

    <script>
        var table = $('#mapcallforrecordstable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: false,
            lengthChange: false,
            ajax: {
                url: "{{ route('mapcallforrecords.mapcallforrecords_fetchData') }}",
                type: "POST", // Change to GET
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: function(json) {
                    console.log(json);
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#usertable_wrapper').show();
                        $('#no_data').hide(); // Hide custom "No Data" message
                        return json.data;
                    } else {
                        $('#tableshow').hide();
                        $('#usertable_wrapper').hide();
                        $('#no_data').show(); // Show custom "No Data" message
                        return [];
                    }
                },
            },
            columns: [{
                    data: null,
                    render: (_, __, ___, meta) => meta.row + 1, // Serial number column
                    className: 'text-end' // Align to the right
                },
                // {
                //     data: "deptelname"
                // },
                {
                    data: "catename"
                },
                {
                    data: "callforrecordsename"
                },
                {
                    data: "statusflag",
                    render: (data) => {
                        if (data === 'Y') {
                            return `<button type="button" class="btn btn-primary btn-sm">Active</button>`;
                        } else {
                            return `<button type="button" class="btn  btn-sm"  style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">Inactive</button>`;
                        }
                    },
                    className: 'text-center'
                },
                {
                    data: "encrypted_mapcallforrecordid",
                    render: (data) =>
                        `<center>
                    <a class="btn editicon editmapcallforrecords" id="${data}">
                        <i class="ti ti-edit fs-4"></i>
                    </a>
                </center>`
                }
            ]
        });
        // function getCategoriesBasedOnDept() {
        // var deptcode = $('#deptcode').val(); // Get the selected deptcode
        // alert(deptcode);
        // // If a department is selected, make an AJAX request to fetch categories
        // if (deptcode) {
        //     $.ajax({
        //         url: '/getCategoriesBasedOnDept',  // URL without deptcode in the URL path
        //         type: 'GET',
        //         data: {
        //             deptcode: deptcode,
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //          },  // Send deptcode as part of the request data
        //         dataType: 'json',
        //         success: function(data) {
        //             // Clear the category dropdown before appending new options
        //             $('#category').empty();
        //             $('#category').append('<option value="">Select category</option>');  // Reset category dropdown

        //             // Check if categories are returned
        //             if (data.length > 0) {
        //                 $.each(data, function(index, category) {
        //                     // Add each category as an option in the category dropdown
        //                     $('#category').append('<option value="' + category.catcode + '">' + category.catename + '</option>');
        //                 });
        //             } else {
        //                 // If no categories are available for the selected department
        //                 $('#category').append('<option disabled>No Categories Available</option>');
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error fetching categories:', error);
        //         }
        //     });
        // } else {
        //     // If no department is selected, reset the category dropdown
        //     $('#category').empty();
        //     $('#category').append('<option value="">Select category</option>');
        // }
        // }
        function getCallforrecordsBasedOnDept(deptcode, selectedCatcode = null) {
            // alert('te');
            const callforrecordsDropdown = $('#mapcallforrecordid');
            callforrecordsDropdown.html('<option value="">Select Call For Records Name</option>');
            if (!deptcode) {
                callforrecordsDropdown.append('<option value="" disabled>No Call For Records Available</option>');

            }
            if (deptcode) {
                $.ajax({
                    url: "/getCallforrecordsbasednndept",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach(callforrecords => {
                                callforrecordsDropdown.append(
                                    `<option value="${callforrecords.callforrecordsid}" ${
                                    callforrecords.callforrecordsid === selectedCatcode ? 'selected' : ''
                            }>${callforrecords.callforrecordsename}</option>`
                                );
                            });
                        } else {
                            callforrecordsDropdown.append(
                                '<option disabled>No Call For Records Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching Call For Records. Please try again.');
                    }
                });
            }
        }


        //Function to get categories based on the selected department
        function getCategoriesBasedOnDept(deptcode, selectedCatcode = null) {
            // alert('te');
            const catcodeDropdown = $('#category');
            catcodeDropdown.html('<option value="">Select Category Name</option>');
            if (!deptcode) {
                catcodeDropdown.append('<option value="" disabled>No Category Available</option>');

            }
            if (deptcode) {
                $.ajax({
                    url: "/getcategoriesbasednndept",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach(category => {
                                catcodeDropdown.append(
                                    `<option value="${category.catcode}" ${
                                category.catcode === selectedCatcode ? 'selected' : ''
                            }>${category.catename}</option>`
                                );
                            });
                        } else {
                            catcodeDropdown.append('<option disabled>No Categories Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching categories. Please try again.');
                    }
                });
            }
        }



        $("#mapcallforrecords").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                category: {
                    required: true
                },
                mapcallforrecordid: {
                    required: true
                },
                status: {
                    required: true
                },

            },
            messages: {
                deptcode: {
                    required: "Select a department",
                },
                category: {
                    required: "Select a category",
                },
                mapcallforrecordid: {
                    required: "Select a call for records",
                },

                status: {
                    required: "Select a statusflag",
                },
            }
        });
        $("#buttonaction").on("click", function(event) {
            // Prevent form submission (this stops the page from refreshing)
            event.preventDefault();

            //Trigger the form validation
            if ($("#mapcallforrecords").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#mapcallforrecords').serializeArray();


                $.ajax({
                    url: "{{ route('mapcallforrecords.mapcallforrecords_insertupdate') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission
                            passing_alert_value('Confirmation', response.message,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
                            table.ajax.reload();

                        } else if (response.error) {
                            // Handle errors if needed
                            console.log(response.error);
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

        function mapcallforrecordsForm(mapcallforrecords) {
            $('#display_error').hide();
            change_button_as_update('mapcallforrecords', 'action', 'buttonaction', 'display_error', '', '');
            // $('#catcode').val(mainobjection.catcode);
            //  $('#fname').val(workallocation.majorworkallocationtypetname);
            // $('#deptcode').val(workallocation.deptcode);
            $('#map_callforrecords').val(mapcallforrecords.encrypted_mapcallforrecordid);
            populateStatusFlag(mapcallforrecords.statusflag);
            $('#deptcode').val(mapcallforrecords.deptcode)
            $('#mapcallforrecordid').val(mapcallforrecords.callforecordsid);

            // $('#category').val(workallocation.catcode).change();
            //  $('#deptcode').val(workallocation.deptcode).change();
            getCategoriesBasedOnDept(mapcallforrecords.deptcode, mapcallforrecords.catcode);
            getCallforrecordsBasedOnDept(mapcallforrecords.deptcode, mapcallforrecords.callforecordsid);

            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }

        $(document).on('click', '.editmapcallforrecords', function() {
            const id = $(this).attr('id');
            // console.log(id);
            if (id) {
                reset_form();
                $('#map_callforrecords').val(id);
                //console.log($('#workallocation'));
                // alert(id);
                $.ajax({
                    url: "{{ route('mapcallforrecords.mapcallforrecords_fetchData') }}",
                    method: 'POST',
                    data: {
                        mapcallforrecordid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                mapcallforrecordsForm(response.data[0]); // Populate form with data
                            } else {
                                alert('call for records data is empty');
                            }
                        } else {
                            alert('call for records not found');
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
            $('#mapcallforrecords')[0].reset();
            $('#mapcallforrecords').validate().resetForm();
            change_button_as_insert('mapcallforrecords', 'action', 'buttonaction', 'display_error', '', '');
            getCategoriesBasedOnDept('', selectedCatcode = null);
            getCallforrecordsBasedOnDept('', selectedCatcode = null);
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>


@endsection
