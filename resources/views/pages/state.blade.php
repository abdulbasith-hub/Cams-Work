@extends('layouts.app')

@section('title', 'State Page')

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">State</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            State
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-md-12"> <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4 mt-4"> <!--begin::Header-->
                        <div class="card-header">
                            <div class="card-title">Create State</div>
                        </div> <!--end::Header--> <!--begin::Form-->
                        <form id="stateForm"> <!--begin::Body-->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3"> <label for="state_name" class="form-label">State Name</label>
                                            <input type="text" class="form-control" id="state_name"
                                                aria-describedby="state_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3"> <label for="state_t_name" class="form-label">State T
                                                Name</label> <input type="text" class="form-control" id="state_t_name"
                                                aria-describedby="state_t_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select id="status" name="status" class="form-control"
                                                aria-describedby="status_help">
                                                <option value="">Select a status</option>
                                                <option value="Y">Active</option>
                                                <option value="N">In-active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer"> <button type="submit"
                                        class="btn btn-primary float-right">Submit</button> </div> <!--end::Footer-->
                        </form> <!--end::Form-->
                    </div> <!--end::Quick Example--> <!--begin::Input Group-->
                </div> <!--end::Col--> <!--begin::Col-->
            </div> <!--end::Row--> <!--begin::Row-->

        </div> <!--end::Container-->
        <div class="container-fluid">
            <h2>State List</h2>
            <div class="table-responsive">
                <table id="state-district-table" class="display table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Si No.</th>
                            <th>State E Name</th>
                            <th>State T Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> <!--end::App Content-->
    </main> <!--end::App Main--> <!--begin::Footer-->
    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="edit-id" name="id">
                        <!-- Add additional form fields as needed -->
                        <div class="form-group">
                            <label for="field">Field</label>
                            <input type="text" class="form-control" id="field" name="field">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $('#stateForm').on('submit', function(event) {

            event.preventDefault();
            let formData = {
                state_name: $('#state_name').val(),
                state_t_name: $('#state_t_name').val(),
                status: $('#status').val(),
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: "{{ route('state.save') }}",
                method: 'POST',
                data: formData,
                success: function(data) {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while saving the State. Please try again.');
                }
            });
        })
        $(document).ready(function() {
            $('#state-district-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('state.list') }}",
                columns: [{
                        data: "DT_RowIndex",
                        name: "si no",
                        "orderable": false,
                        "searchable": false,
                    },
                    {
                        data: 'stateename',
                        name: 'stateename'
                    },
                    {
                        data: 'statetname',
                        name: 'statetname'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });
        });

        function openEditModal(id) {
            // Set the ID to a hidden input or use it for AJAX requests as needed
            document.getElementById('edit-id').value = id;

            // Optionally, make an AJAX call to fetch data based on the ID
            // $.ajax({
            //     url: '/your-data-fetch-route/' + id,
            //     method: 'GET',
            //     success: function(data) {
            //         // Populate modal fields with data
            //         $('#edit-modal').find('#field').val(data.field);
            //         ...
            //     }
            // });

            // Show the modal
            $('#edit-modal').modal('show');
        }
    </script>
@endsection
