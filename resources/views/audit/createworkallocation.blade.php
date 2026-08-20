@section('content')
    @extends('index2')
    @include('common.alert')

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card_header_color">Work Allocation</div>
                <div class="card-body">
                    <form id="manage_workallocation">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="display_error" style="display: none;">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @csrf
                        <input type="hidden" name="workallocid" id="workallocid" value="" />

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label required" for="majortype">Department Name</label>
                                <input type="text" class="form-control name" id="deptname" name="deptname"
                                placeholder="Enter Dept Name"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required" for="majortype">Major Workallocation Type</label>
                                <input type="text" class="form-control name" id="majortype" name="majortype"
                                placeholder="Enter Major Workallocation Type"/>                           
                            </div>
                             <input type="hidden" name="minortype_available" class="minortype_available" />
                            <div class="col-md-6 minortypediv">
                                <label class="form-label required" for="minortype">Minor WorkAllocation Type</label>
                                <input type="text" class="form-control name" id="minortype" name="minortype"
                                placeholder="Enter Major Workallocation Type"/>     
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required" for="allocatedtowhom">Allocated to Whom</label>
                                <select class="form-select" id="allocatedtowhom" name="allocatedtowhom">
                                    <option value="">Select User Type</option>
                                    <option value="M">Team Member</option>
                                    <option value="H">Team Head</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-2 mx-auto">
                            <input type="submit" name="buttonaction" id="buttonaction" class="btn mt-3 button_save"
                            value="Save Draft" />
                            <button class="btn btn-danger mt-3" id="reset_button" type="button">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
                <div class="card-header card_header_color">
                    Work Allocation Details
                </div>
                <div class="card-body">

                    <div class="datatables ">
                        <!-- start File export -->
                        <div class="card" style="border-color: #7198b9">
                            <div class="card-body">
                                <div id="datatable" class="table-responsive hide_this">
                                    <table id="file_export"
                                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic worktable">
                                        <thead>
                                            <!-- start row -->
                                            <tr>
                                                <th>S.No</th>
                                                <th>Major Workallocation Type</th>
                                                <th>Minor WorkAllocation Type</th>
                                                <th>Allocated to Whom</th>
                                                <th>Action</th>
                                            </tr>
                                            <!-- end row -->
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div id='no_data' class='hide_this'>
                                    <center>No Data Available</center>
                                </div>
                            </div>
                        </div>
                        <!-- end Footer callback -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script>

        // Fetch Minor Work Allocation Types
        function majorsubcat(majortypeget='',minortypeget='') {
            if(majortypeget == '')
            {
                var majortype = $('#majortype').val();
            }else
            {
                var majortype = majortypeget;

            }
            if (majortype) {
                $.ajax({
                    url: '/MinorWrkAllocationFetch',
                    method: 'POST',
                    data: { majortype: majortype },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.length == 0)
                        {
                            $('.minortypediv').hide();
                            $('.minortype_available').val(0);
                        }else
                        {
                            $('.minortypediv').show();
                            $('#minortype').empty().append('<option value="">Select Minor WorkAllocation Type</option>');
                            $.each(response, function(index, item) {

                                let selected = (item.subworkallocationtypeid === minortypeget) ? 'selected' : '';

                                $('#minortype').append(
                                    `<option value="${item.subworkallocationtypeid}" ${selected}>${item.subworkallocationtypeename}</option>`
                                );
                            });
                            $('.minortype_available').val(1);

                        }
                        
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            } else {
                $('#minortype').empty().append('<option value="">Select Minor WorkAllocation Type</option>');
            }
        }

        // Form Submission with POST
        $(document).ready(function() {

               /** Data Table**/
        var table = $('.worktable').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "autoWidth": false, // Disable auto-width calculation
                    "scrollX": true, // Enable horizontal scrolling
                    "ajax": {
                        "url": "/fetchAllData", // Your API route for fetching data
                        "type": "POST",
                        "data": function(d) {
                            d._token = $('meta[name="csrf-token"]').attr(
                                'content'); // CSRF token for security
                        },
                        "dataSrc": function(json) {
                            //console.log(json);
                            // Check if there is data
                            if (json.data && json.data.length > 0) {
                                $('#datatable').show();
                                $('#no_data').hide();
                                return json.data;

                            } else {
                                $('#datatable').hide();
                                $('#no_data').show();
                                return [];
                            }
                        }
                    },
                    "columns": [{
                            "data": "Slno"
                        },
                        {
                            "data": "majorworkallocationtypeename"
                        },
                        {
                            "data": "subworkallocationtypeename"
                        },
                        {
                            "data": "allocatedwhom_label"
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                               return `<button id="edit_user" data-id="${row.encrypted_workallocationtypeid}"  title="Edit" type="button" class="btn btn-secondary btn-sm">
                                            <i class="ti ti-edit fs-4"></i>
                                        </button>`;
                            }
                        }
                    ]
                });

                $(document).on('click', '#edit_user', function() {
                    // Add more logic here
                    var dynid = $(this).data('id');
                    //alert('inprogress');
                    if (dynid) {
                        reset_form();
                        geteditdetail(dynid)
                    }
                });

                function geteditdetail(dynid)
                {
                    $.ajax({
                        url: '/fetchWorkData', // Your API route to get user details
                        method: 'POST',
                        data: {
                            dynid: dynid
                        }, // Pass deptuserid in the data object
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // CSRF token for security
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#display_error').hide();
                                //validator.resetForm();
                                change_button_as_update('manage_workallocation', 'action', 'buttonaction',
                                    'display_error', '', '');
                                var data =response.data;
                                $('#workallocid').val(data.workallocationtypeid);
                                $('#majortype').val(data.majorworkallocationtypeid);
                                majorsubcat(data.majorworkallocationtypeid,data.minorworkallocationtypeid)
                                $('#allocatedtowhom').val(data.allocatedwhom);

                            } else {
                                alert('Audit not found');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });

                }

                function reset_form() {
                    $('.error').hide();

                    //validator.resetForm();
                    change_button_as_insert('manage_workallocation', 'action', 'buttonaction', 'display_error', '', '');
                
                }
                const $manage_workallocation = $("#manage_workallocation");

                $("#manage_workallocation").validate({
                            rules: {
                                majortype: {
                                    required: true,
                                },
                                minortype: {
                                    required: function () {
                                        // Only require if the minor type dropdown is visible
                                        return $(".minortypediv").is(":visible");
                                    },
                                },
                                allocatedtowhom: {
                                    required: true,
                                },
                            },
                            messages: {
                                majortype: {
                                    required: "Select  Major Workallocation Type.",
                                },
                                minortype: {
                                    required: "Select  Minor Workallocation Type.",
                                },
                                allocatedtowhom: {
                                    required: "Select Allocation to whom.",
                                },
                            }
                        });

                         /**Savedraft,update click action */
                        $(document).on('click', '#buttonaction', function(event) {
                            event.preventDefault(); // Prevent form submission

                            if ($manage_workallocation.valid()) {
                                get_insertdata('insert')
                            } else {
                                scrollToFirstError();
                            }
                        });


                function get_insertdata(action) {
                    var formData = $('#manage_workallocation').serialize();

                    $.ajax({
                        url: '/CreateWorkAllocation',
                        method: 'POST',
                        data:formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) 
                        {
                            if (response.success) 
                            {
                                passing_alert_value('Confirmation', response.success,
                                            'confirmation_alert', 'alert_header', 'alert_body',
                                            'confirmation_alert');
                                table.ajax.reload();
                                reset_form();
                                $("#manage_workallocation")[0].reset(); // Reset form fields
                                $('#minortype').empty().append('<option value="">Select Minor WorkAllocation Type</option>');
                                $('#display_error').hide();

                            }else if (response.error) 
                            {
                                passing_alert_value('Alert', response.error, 'confirmation_alert',
                                    'alert_header', 'alert_body', 'confirmation_alert');
                            }
                        },
                        error: function(xhr, status, error) 
                        {

                            var response = JSON.parse(xhr.responseText);

                            var errorMessage = response.error || 'An unknown error occurred';

                            // Displaying the error message
                            passing_alert_value('Alert', errorMessage, 'confirmation_alert','alert_header', 'alert_body', 'confirmation_alert');

                        }
                    });
                }

                // Reset Button Logic
                $('#reset_button').on('click', function() {
                    $("#manage_workallocation")[0].reset();
                    $('#minortype').empty().append('<option value="">Select Minor WorkAllocation Type</option>');
                    $('#display_error').hide();
                });
        });
    </script>
@endsection
