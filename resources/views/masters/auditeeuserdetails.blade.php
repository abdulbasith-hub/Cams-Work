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
                <div class="card-header card_header_color">Auditee User Detail Form</div>
                <div class="card-body">
                    <form id="auditeeuserdetails" name="auditeeuserdetails">
                        <!-- <input type="text" name="workallocation" id="workallocation"> -->
                        @csrf
                        <div class="row">

                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" key="department" for="dept">Department</label>

                                <!-- <select class="form-select mr-sm-2" id="deptcode" name="deptcode" onchange="getCategoriesBasedOnDept(this.value,'')"> -->
                                <select class="form-select mr-sm-2" id="deptcode" name="deptcode"
                                    onchange="getRegionBasedOnDept('')">
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
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="form-select mr-sm-2" id="region" name="region"
                                    onchange="getDistrictBasedOnRegion('','')">
                                    <option value=''>Select Region Name</option>
                                    <option value="" disabled id="no-region-option">No Region Available</option>
                                    <!-- Default "No Region Available" -->

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2" id="district" name="district"
                                    onchange="getinstitutionBasedOndistrict('','','')">
                                    <option value=''>Select District Name</option>
                                    <option value="" disabled id="no-district-option">No District Available</option>
                                    <!-- Default "No Region Available" -->


                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution"
                                    for="institution">Institution</label>
                                <select class="form-select mr-sm-2" id="institution" name="institution">
                                    <option value=''>Select Audit Office</option>
                                    <option value="" disabled id="no-ins-option">No Institution Available</option>
                                    <!-- Default "No Region Available" -->



                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="exampleInputEmail1" class="form-label required lang">Email</label>
                                <input type="email" class="form-control " placeholder="Enter Email" id="email"
                                    name="email" aria-describedby="emailHelp" required>
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
                                <input type="hidden" name="auduserdetails_id" id="auduserdetails_id" value="" />

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
                <div class="card-header card_header_color">Auditee User Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="auditeeuserdetailstable"
                                class="table w-100 table-striped table-bordered display  datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang align-middle text-center" key="s_num">S.No</th>
                                        <th class="lang align-middle text-center" key="department">Department</th>
                                        <th class="lang align-middle text-center" key="region">Region</th>
                                        <th class="lang align-middle text-center" key="district">District</th>
                                        <th class="lang align-middle text-center" key="">Audit Office</th>
                                        <th class="lang align-middle text-center" key="email">Email</th>
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
        $(document).on('click', '.editauditeeuserdetails', function() {
            const id = $(this).attr('id');

            //alert(id);
            if (id) {
                reset_form();
                $('#auduserdetails_id').val(id);
                //console.log($('#workallocation'));
                // const routeUrl = "{{ route('auditeeuserdetails.auditeeuserdetails_fetchData') }}";
                // console.log("Route URL:", routeUrl);
                // alert(id);
                $.ajax({
                    url: "{{ route('auditeeuserdetails.auditeeuserdetails_fetchData') }}",
                    method: 'POST',
                    data: {
                        auditeeuserid: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.length > 0) {
                                auditeeuserdetailsForm(response.data[0]); // Populate form with data
                            } else {
                                alert('Auditee user details data is empty');
                            }
                        } else {
                            alert('Auditee user details not found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText || 'Unknown error');
                    }
                });
            }
        });

        function auditeeuserdetailsForm(auduserdetails) {
            $('#display_error').hide();
            change_button_as_update('auditeeuserdetails', 'action', 'buttonaction', 'display_error', '', '');
            $('#email').val(auduserdetails.email);
            $('#auduserdetails_id').val(auduserdetails.encrypted_auditeeuserid);

            $('#deptcode').val(auduserdetails.deptcode);
            getRegionBasedOnDept(auduserdetails.deptcode, auduserdetails.regioncode);
            getDistrictBasedOnRegion(auduserdetails.deptcode, auduserdetails.regioncode, auduserdetails.distcode);
            getinstitutionBasedOndistrict(auduserdetails.deptcode, auduserdetails.regioncode, auduserdetails.distcode,
                auduserdetails.instid);
            // updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }

        var table = $('#auditeeuserdetailstable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: false,
            lengthChange: false,
            ajax: {
                url: "{{ route('auditeeuserdetails.auditeeuserdetails_fetchData') }}",
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
                {
                    data: "deptesname"
                },
                {
                    data: "regionename"
                },
                {
                    data: "distename"
                },
                {
                    data: "instename"
                },
                {
                    data: "email"
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
                    data: "encrypted_auditeeuserid",
                    render: (data) =>
                        `<center>
                    <a class="btn editicon editauditeeuserdetails" id="${data}">
                        <i class="ti ti-edit fs-4"></i>
                    </a>
                </center>`
                }
            ]
        });



        $("#auditeeuserdetails").validate({
            rules: {
                deptcode: {
                    required: true,
                },
                region: {
                    required: true
                },
                district: {
                    required: true
                },
                institution: {
                    required: true
                },
                email: {
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
                region: {
                    required: "Select a region",
                },
                district: {
                    required: "Select a district",
                },

                institution: {
                    required: "Select a institution",
                },
                email: {
                    required: "Select a email",
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
            if ($("#auditeeuserdetails").valid()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = $('#auditeeuserdetails').serializeArray();


                $.ajax({
                    url: "{{ route('auditeeuserdetails.auditeeuserdetails_insertupdate') }}", // URL where the form data will be posted
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
                            passing_alert_value('Confirmation', response.error,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');
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

        // $('#deptcode').on('change', function() {
        //     const deptcode = $(this).val();
        //     $('#region').html('<option value="">Select Region Name</option>');
        //     $('#district').html('<option value="">Select District Name</option>');
        //     $('#institution').html('<option value="">Select Audit Office</option>');


        // });

        // $('#region').on('change', function() {
        //     const region = $(this).val();
        //     const deptcode = $('#deptcode').val();
        //     $('#district').html('<option value="">Select District Name</option>');
        //     $('#institution').html('<option value="">Select Audit Office</option>');


        // });

        // $('#district').on('change', function() {
        //     const district = $(this).val();
        //     const deptcode = $('#deptcode').val();
        //     const region = $('#region').val();
        //     $('#institution').html('<option value="">Select Audit Office</option>');


        // });

        function getinstitutionBasedOndistrict(deptcode, region, district, selecteinstitutioncode = null) {
            // alert('te');
            const institutionDropdown = $('#institution');
            institutionDropdown.html('<option value="">Select Audit Office</option>');
            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#region").val();
                // alert(deptcode);
            }
            if (district == "") {
                var district = $("#district").val();
                // alert(deptcode);
            }
            if (!district) {
                institutionDropdown.append('<option value="" disabled>No Institution Available</option>');


            }
            if (deptcode && region && district) {
                $.ajax({
                    url: "/getinstitutionbasedondist",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(institution => {
                                institutionDropdown.append(
                                    `<option value="${institution.instid}" ${
                                    institution.instid === selecteinstitutioncode ? 'selected' : ''
                            }>${institution.instename}</option>`
                                );
                            });
                        } else {
                            institutionDropdown.append('<option disabled>No Institution Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching institution. Please try again.');
                    }
                });
            }
        }


        function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            // alert('te');
            const districtDropdown = $('#district');
            const institutionDropdown = $('#institution');

            districtDropdown.html('<option value="">Select District Name</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#region").val();
                // alert(deptcode);
            }

            if (!region) {
                districtDropdown.append('<option value="" disabled>No District Available</option>');

            }
            // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');

            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregion",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.distcode}" ${
                                    district.distcode === selecteDistrictcode ? 'selected' : ''
                            }>${district.distename}</option>`
                                );
                            });
                        } else {
                            districtDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }


        function getRegionBasedOnDept(deptcode, selectedRegioncode = null) {
            // alert('te');
            const districtDropdown = $('#district');
            const regionDropdown = $('#region');
            const institutionDropdown = $('#institution');

            regionDropdown.html('<option value="">Select Region Name</option>');
            districtDropdown.html('<option value="">Select District Name</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (!deptcode) {
                regionDropdown.append('<option value="" disabled>No Region Available</option>');
                districtDropdown.append('<option value="" disabled>No District Available</option>');
                institutionDropdown.append('<option value="" disabled>No Institution Available</option>');


                return;
            }
            if (deptcode) {
                $.ajax({
                    url: "/getregionbasedondept",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(region => {
                                regionDropdown.append(
                                    `<option value="${region.regioncode}" ${
                                    region.regioncode === selectedRegioncode ? 'selected' : ''
                            }>${region.regionename}</option>`
                                );
                            });
                        } else {
                            regionDropdown.append('<option disabled>No Region Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching region. Please try again.');
                    }
                });
            }

        }







        //Function to get categories based on the selected department


        function populateStatusFlag(statusflag) {
            if (statusflag === "Y") {
                document.getElementById('statusYes').checked = true;
            } else if (statusflag === "N") {
                document.getElementById('statusNo').checked = true;
            }
        }

        function reset_form() {
            $('#auditeeuserdetails')[0].reset();
            $('#auditeeuserdetails').validate().resetForm();
            change_button_as_insert('auditeeuserdetails', 'action', 'buttonaction', 'display_error', '', '');
            getRegionBasedOnDept('', selectedCatcode = null);
            getDistrictBasedOnRegion('', selectedCatcode = null);
            getinstitutionBasedOndistrict('', selectedCatcode = null);
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
    </script>


@endsection
