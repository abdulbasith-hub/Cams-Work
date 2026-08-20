@section('content')
    @extends('index2')
    @include('common.alert')
    <style>
        .card_seperator {
            height: 10px;
            border: 0;
            box-shadow: 0 10px 10px -10px #8c8b8b inset;
        }

        .card-title {
            font-size: 15px;
        }

        .title-part-padding {
            background-color: #e3efff;
        }

        .card-body {
            padding: 15px 10px;
        }

        .card {
            margin-bottom: 10px;
        }

        .dataTables_info {
            margin-bottom: 1rem !important;
        }

        .holiday-red {
            color: #ff0000 !important; /* Red text */
            border-radius: 50%; /* Optional: Makes the date look like a circle */
        }

        /* Style the total days count with a border and rounded corners */
        .total-days-container {
            display: inline-block;
            padding: 3px 15px;
            border-radius: 10px; /* Rounded corners */
            font-size: 15px;
            font-weight: bold;
            background-color: #f1f1f1; /* Light background color */
            color: #333; /* Text color */
        }



    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <div class="row">
        <div class="col-12">
            <!-- <div class="repeater-default">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div data-repeater-list="">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div data-repeater-item=""> -->
            <div class="card card_border" style="border-color: #7198b9">

                <div class="card-header card_header_color lang" key="audit_certificate">
                    Audit Certificate
                </div>

                <div class="card-body collapse show">
                    <form id="audit_certificate" name="audit_certificate">
                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                            id="display_error">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @csrf
                        

                        <div class="card" style="border-color: #7198b9">
                           
                            <div class="card-body">

                               <div class="row">

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="mem_share_captial" for="validationDefault02">Membership & Shared Capital</label>
                                        <input type="number" class="form-control" name="membership_sharedcapital" placeholder="Enter Membership & Shared Capital" >
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="deposit_borrowings" for="validationDefault02">Deposits & Borrowings</label>
                                        <input type="number" class="form-control" name="deposits_borrowings" placeholder="Enter Deposits & Borrowings" >
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="reserves_surplus" for="validationDefault02">Reserves & Surplus</label>
                                        <input type="number" class="form-control" name="reserves_surplus"  placeholder="Enter Reserves & Surplus" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="other_liability" for="validationDefault02">Other Liabilities</label>
                                        <input type="number" class="form-control" name="other_liability" placeholder="Enter Other Liabilities" >
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="investments" for="validationDefault02">Investments</label>
                                        <input type="number" class="form-control" name="investments" placeholder="Enter Investments" >
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="loan_advance" for="validationDefault02">Loans & Advances</label>
                                        <input type="number" class="form-control" name="loans_advances" placeholder="Enter Loans & Advances" >
                                    </div>
                                
                               </div>

                               <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="trading_result" for="validationDefault02">Trading Result</label>
                                        <input type="number" class="form-control" name="trading_result" placeholder="Enter Trading Result" >
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label lang  required" key="net_result" for="validationDefault02">Net Result</label>
                                        <input type="number" class="form-control"  name="net_result" placeholder="Enter Net Result" >
                                    </div>
                                
                               </div>
                               <br>

                               <div class="row justify-content-center">
                                    <div class="col-md-3 mx-auto">
                                        <input type="hidden" name="action" id="action" value="insert" />
                                        <button class="btn button_save mt-3 lang" key="save_btn" type="submit" action="insert" id="buttonaction"
                                            name="buttonaction">Save Draft</button>
                                        <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn" id="reset_button">Clear</button>
                                    </div>
                               </div>
                              <br>
                              
                            </div>

                        </div>

                       
                    </form>

                </div>
            </div>

        </div>
    </div>
    <div class="card " style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="audit_cer_details">Audit Certificate Details</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="datatable">
                    <table id="certificatetable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">Sl.No</th>
                                <th class="lang" key="mem_share_captial">Membership & Shared Capital</th>
                                <th class="lang" key="deposit_borrowings">Deposits & Borrowings</th>
                                <th class="lang" key="reserves_surplus">Reserves & Surplus</th>
                                <th class="lang" key="other_liability">Other Liabilities</th>
                                <th class="lang" key="investments">Investments</th>
                                <th class="lang" key="loan_advance">Loans & Advances</th>
                                <th class="lang" key="trading_result">Trading Result</th>
                                <th class="lang" key="net_result">Net Result</th>
                                <th class="lang" key="action">Action</th>


                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id='no_data' class='hide_this'>
                <center>No Data Available</center>
            </div>
        </div>
    </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <!-- <script src="../assets/libs/prismjs/prism.js"></script> -->
    <!-- <script src="../assets/js/widget/ui-card-init.js"></script> -->
    <script src="../assets/js/plugins/toastr-init.js"></script>

    {{-- data table --}}
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>

    <script>
    $(document).ready(function() {

            var table = $('#certificatetable').DataTable({
                "processing": true,
                "serverSide": false,
                "autoWidth": false, // Disable auto-width calculation
                scrollX: true,
                "ajax": {
                    "url": "/FetchAllCertificate", // Your API route for fetching data
                    "type": "POST",
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr(
                                        'content'); // CSRF token for security
                    },
                    "dataSrc": function(json) {
                        if (json.data && json.data.length > 0)
                        {
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
                "columns": [{"data": "Slno"},
                            {"data": "membership_sharedcapital"}, 
                            {"data": "deposits_borrowings"},
                            {"data": "reserves_surplus"},
                            {"data": "other_liability"},
                            {"data": "investments"},
                            {"data": "loans_advances"},
                            {"data": "trading_result"},
                            {"data": "net_result"},
                            {
                                "data": "encrypted_auditplanid", // Use the encrypted deptuserid
                                "render": function(data, type, row) {
                                    let userid = row.userid
                                    // Otherwise, show the Finalize button
                                return `<center>
                                            <button class="btn btn-primary schedule_btn" id="${data}" data-userid="${userid}">
                                                View Audit Certificate
                                            </button>
                                        </center>`;

                                }
                            }                        
                            ]
            });

            // Initialize the form validation
            $("#audit_certificate").validate({
                rules: {
                    membership_sharedcapital: {
                        required: true,
                        number: true
                    },
                    deposits_borrowings: {
                        required: true,
                        number: true
                    },
                    reserves_surplus: {
                        required: true,
                        number: true
                    },
                    other_liability: {
                        required: true,
                        number: true
                    },
                    investments: {
                        required: true,
                        number: true
                    },
                    loans_advances: {
                        required: true,
                        number: true
                    },
                    trading_result: {
                        required: true,
                        number: true
                    },
                    net_result: {
                        required: true,
                        number: true
                    }
                },
                messages: {
                    membership_sharedcapital: {
                        required: "Please enter Membership & Shared Capital",
                        number: "Please enter a valid number"
                    },
                    deposits_borrowings: {
                        required: "Please enter Deposits & Borrowings",
                        number: "Please enter a valid number"
                    },
                    reserves_surplus: {
                        required: "Please enter Reserves & Surplus",
                        number: "Please enter a valid number"
                    },
                    other_liability: {
                        required: "Please enter Other Liabilities",
                        number: "Please enter a valid number"
                    },
                    investments: {
                        required: "Please enter Investments",
                        number: "Please enter a valid number"
                    },
                    loans_advances: {
                        required: "Please enter Loans & Advances",
                        number: "Please enter a valid number"
                    },
                    trading_result: {
                        required: "Please enter Trading Result",
                        number: "Please enter a valid number"
                    },
                    net_result: {
                        required: "Please enter Net Result",
                        number: "Please enter a valid number"
                    }
                },
                submitHandler: function(form) {
                    get_insertdata('insert'); // If validation passes, call your function to insert data
                }
            });

            $(document).on('click', '#buttonaction', function(event) 
            {
                event.preventDefault(); // Prevent form submission

                if ($("#audit_certificate").valid()) {
                    get_insertdata('insert'); // Submit form if valid
                }
                                
            });
            

            $(document).on('click', '.schedule_btn', function() {
                var id = 1; // Getting id of the clicked button (which is auditplanid)
                window.location.href = '/auditcertificate';
               
            });

            function get_insertdata(action)
            {
                var formData = $('#audit_certificate').serialize();

                $.ajax({
                    url: '/CreateAuditCertificate',
                    method: 'POST',
                    data:formData,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) 
                    {
                        console.log(response);

                        if (response.success) 
                        {
                            passing_alert_value('Confirmation', response.success,
                                                    'confirmation_alert', 'alert_header', 'alert_body',
                                                    'confirmation_alert');
                            table.ajax.reload();
                            reset_form();
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


            function reset_form() 
            {
                $('.error').hide();
                $("#audit_certificate")[0].reset();
                //change_button_as_insert('audit_certificate', 'action', 'buttonaction', 'display_error', '', '');
                        
            }

            $('#reset_button').on('click', function() 
            {
                reset_form();
                   
            });

    });

    

    </script>


@endsection
