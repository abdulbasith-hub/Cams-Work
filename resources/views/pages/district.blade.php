@extends('layouts.app')

@section('title', 'District Page')

@section('content')
            <div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">District</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    District
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
                                    <div class="card-title">Create District</div>
                                </div> <!--end::Header--> <!--begin::Form-->
                                <form id="districtForm"> <!--begin::Body-->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3"> <label for="dist_name" class="form-label">District Name</label> <input type="text" class="form-control" id="dist_name" aria-describedby="dist_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> <label for="dist_t_name" class="form-label">District T Name</label> <input type="text" class="form-control" id="dist_t_name" aria-describedby="dist_t_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="state" class="form-label">State</label>
                                                    <select id="state" name="state" class="form-control" aria-describedby="state_help">
                                                        <option value="">Select a state</option>
                                                        @foreach ($state as $states)
                                                            <option value="{{ $states->statecode }}">{{ $states->stateename }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select id="status" name="status" class="form-control" aria-describedby="status_help">
                                                        <option value="">Select a status</option>
                                                        <option value="Y">Active</option>
                                                        <option value="N">In-active</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="card-footer"> <button type="submit" class="btn btn-primary float-right">Submit</button> </div> <!--end::Footer-->
                                </form> <!--end::Form-->
                            </div> <!--end::Quick Example--> <!--begin::Input Group-->
                        </div> <!--end::Col--> <!--begin::Col-->
                    </div> <!--end::Row--> <!--begin::Row-->
                  
                </div> <!--end::Container-->
                <div class="container-fluid">
                    <h2>District List</h2>
                    <div class="table-responsive">
                        <table id="district-table" class="display table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Si No.</th>
                                    <th>District E Name</th>
                                    <th>District T Name</th>
                                    <th>State Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div> <!--end::App Content-->
        </main> <!--end::App Main--> <!--begin::Footer-->
        <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit District</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <input type="hidden" id="edit-id" name="distid">
                            <div class="form-group">
                                <label for="distename">District E Name</label>
                                <input type="text" class="form-control" id="distename" name="distename">
                            </div>
                            <div class="form-group">
                                <label for="disttname">District T Name</label>
                                <input type="text" class="form-control" id="disttname" name="disttname">
                            </div>
                            <div class="form-group">
                                <label for="statecode">State Name</label>
                                <select class="form-control" id="statecode" name="statecode">
                                    <option value="">Select State</option>
                                    @foreach ($state as $states)
                                        <option value="{{ $states->statecode }}">{{ $states->stateename }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="statusflag">Status</label>
                                <select class="form-control" id="statusflag" name="statusflag">
                                    <option value="Y">Active</option>
                                    <option value="N">Deactive</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="save_edit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        @endsection
        @section('scripts')
        <script>
        $('#districtForm').on('submit', function(event) {
            
            event.preventDefault(); 
            let formData = {
                dist_name: $('#dist_name').val(),
                dist_t_name: $('#dist_t_name').val(),
                status: $('#status').val(),
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: "{{ route('district.save') }}",
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
                    alert('An error occurred while saving the district. Please try again.');
                }
            });
        })
        $(document).ready(function () {
            $('#district-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dist.list') }}",
                columns: [
                    { data: "DT_RowIndex", name: "si no", "orderable": false,
                        "searchable": false, },
                    { data: 'distename', name: 'distename' },
                    { data: 'disttname', name: 'disttname' },
                    { data: 'statecode', name: 'statecode' },
                    { data: 'action', name: 'action' },
                ]
            });
        });
        function openEditModal(id) {
            document.getElementById('edit-id').value = id;

            $.ajax({
                // url: "{{ route('dist.edit', ':id') }}".replace(':id', id)
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with data
                    $('#edit-modal').find('#distename').val(data.distename);
                    $('#edit-modal').find('#disttname').val(data.disttname);
                    $('#edit-modal').find('#statecode').val(data.statecode);
                    $('#edit-modal').find('#statusflag').val(data.statusflag);
                }
            });

            $('#edit-modal').modal('show');
        }

        </script>
        @endsection