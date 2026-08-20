@extends('index2')
@section('content')
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="border-bottom title-part-padding">
                    <h4 class="card-title mb-0">Privileges</h4>
                </div>
                <div class="card-body">
                    <form>

                        <div class="row ">

                            <div class="col-md-4 mb-3 ">
                                <label class="form-label" for="validationDefault01">Department </label>
                                <select class="form-select mr-sm-2" id="designation">
                                    <option selected>Select Department</option>
                                    <option value="1">Hindu Religious and Charitable Endowments Department
                                    </option>
                                    <option value="2">Local Fund Audit </option>
                                    <option value="3">State Government Audit</option>

                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="validationDefault01">Designation </label>
                                <select class="form-select mr-sm-2" id="designation">
                                    <option selected>Select Designation</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                        </div>






                        <div class="col-md-12">
                            <div class="card">
                                <div class="border-bottom title-part-padding">
                                    <h4 class="card-title mb-0">Dynamic Form Fields</h4>
                                </div>
                                <div class="card-body">
                                    <div id="education_fields" class="my-4"></div>
                                    <div class="table-responsive rounded-4">
                                        <table class="table table-bordered border-dark">
                                            <thead class="bg-inverse text-white">
                                                <tr>
                                                    <th>Privileges</th>
                                                    <th>View</th>
                                                    <th>Add </th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                    {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input type="text" class="form-control" id="ifhrms_id"
                                                                    name="ifhrms_id" placeholder="03548975" required />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input primary" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input success" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input warning" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input danger" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-2">
                                                            <div class="mb-3">
                                                                <button onclick="education_fields();"
                                                                    class="btn btn-success fw-medium" type="button">
                                                                    <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input type="text" class="form-control" id="ifhrms_id"
                                                                    name="ifhrms_id" placeholder="03548975" required />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input primary" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input success" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input warning" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input danger" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-2">
                                                            <div class="mb-3">
                                                                <button onclick="education_fields();"
                                                                    class="btn btn-success fw-medium" type="button">
                                                                    <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input type="text" class="form-control" id="ifhrms_id"
                                                                    name="ifhrms_id" placeholder="03548975" required />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input primary" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input success" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input warning" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                <input class="form-check-input danger" type="checkbox"
                                                                    id="success-check" value="option1">

                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-2">
                                                            <div class="mb-3">
                                                                <button onclick="education_fields();"
                                                                    class="btn btn-success fw-medium" type="button">
                                                                    <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>


                                </div>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="customControlAutosizing" />
                                <label class="form-check-label" for="customControlAutosizing">Agree to terms and
                                    conditions</label>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-4  mx-auto">
                                <button class="btn btn-success mt-3" type="submit">
                                    Submit
                                </button>
                                <button class="btn btn-danger mt-3" type="submit">
                                    Cancel
                                </button>

                            </div>

                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
    <script src="../assets/libs/jquery.repeater/jquery.repeater.min.js"></script>
    {{-- <script src="../assets/js/forms/repeater-init.js"></script> --}}
@endsection
