@extends('layouts.app')

@section('title', 'Region Page')

@section('content')
            <div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Region</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Region
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
                                    <div class="card-title">Create Region</div>
                                </div> <!--end::Header--> <!--begin::Form-->
                                <form id="regionForm"> <!--begin::Body-->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3"> <label for="name" class="form-label">Name</label> <input type="text" class="form-control" id="name" aria-describedby="Region_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> <label for="description" class="form-label">description</label> <input type="text" class="form-control" id="description"> </div>
                                                </div> <!--end::Body--> <!--begin::Footer-->
                                            </div>
                                        </div>
                                    <div class="card-footer"> <button type="submit" class="btn btn-primary float-right">Submit</button> </div> <!--end::Footer-->
                                </form> <!--end::Form-->
                            </div> <!--end::Quick Example--> <!--begin::Input Group-->
                        </div> <!--end::Col--> <!--begin::Col-->
                    </div> <!--end::Row--> <!--begin::Row-->
                  
                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main--> <!--begin::Footer-->
        @endsection
        @section('scripts')
        <script>
        $('#regionForm').on('submit', function(event) {
            
            event.preventDefault(); 
            let formData = {
                name: $('#name').val(),
                description: $('#description').val(),
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: "{{ route('region.save') }}",
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
                    alert('An error occurred while saving the region. Please try again.');
                }
            });
        })

        </script>
        @endsection