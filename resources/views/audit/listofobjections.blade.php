@extends('index2')
@section('content')
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="border-bottom title-part-padding">
                    <h4 class="card-title mb-0">Account Particulars</h4>
                </div>
                <div class="card-body">
                    <form>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label " for="validationDefault01"> Call for Account Particulars </label>

                                <div class="form-check form-check-inline ms-2">
                                    <input class="form-check-input success check-light-success" type="checkbox"
                                        id="success-light-check" value="option1" checked>
                                    <label class="form-check-label" for="success-light-check">Receipts & Charges

                                    </label>
                                </div>


                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success check-light-success" type="checkbox"
                                        id="success-light-check" value="option1" checked>
                                    <label class="form-check-label" for="success-light-check">Income & Expenditure</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success check-light-success" type="checkbox"
                                        id="success-light-check" value="option1" checked>
                                    <label class="form-check-label" for="success-light-check">Balance Sheets</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success check-light-success" type="checkbox"
                                        id="success-light-check" value="option1" checked>
                                    <label class="form-check-label" for="success-light-check">Accounts/Investmets</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success check-light-success" type="checkbox"
                                        id="success-light-check" value="option1" onclick="show_others()">
                                    <label class="form-check-label" for="success-light-check">Others</label>
                                </div>


                            </div>


                            <div class="col-md-4 mb-3" style="display:none" id="particulars_div">
                                <label class="form-label" for="validationDefaultUsername">Account Particulars</label>
                                <input type="text" class="form-control" id="particulars" name="particulars"
                                    placeholder="Account Particulars" required />

                            </div>

                        </div>


                        {{-- <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="customControlAutosizing" />
                                <label class="form-check-label" for="customControlAutosizing">Agree to terms and
                                    conditions</label>
                            </div>
                        </div> --}}
                        {{-- <div class="row">
                            <div class="col-md-4  mx-auto">
                                <button class="btn btn-success mt-3" type="submit">
                                    Submit
                                </button>
                                <button class="btn btn-danger mt-3" type="submit">
                                    Cancel
                                </button>

                            </div>

                        </div> --}}

                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="border-bottom title-part-padding">
                    <h4 class="card-title mb-0">Objections</h4>
                </div>
                <div class="card-body">
                    <div class="repeater-default">
                        <div data-repeater-list="">
                            <div data-repeater-item="">
                                <form>
                                    <div class="row border-top pt-2 ">
                                        <div class="mb-3 col-md-4">
                                            <label class="form-label" for="validationDefault01">Category </label>
                                            <select class="form-select mr-sm-2" id="designation">
                                                <option>Select Category</option>
                                                <option value="1">Hindu Religious and Charitable
                                                    Endowments Department
                                                </option>
                                                <option value="2">Local Fund Audit </option>
                                                <option value="3">State Government Audit</option>

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="validationDefaultUsername">Sub Category</label>
                                            <select class="select2 form-control" multiple="multiple"
                                                id="select2-max-length">
                                                <option>Select Sub Category</option>
                                                <option value="M">Category 1</option>
                                                <option value="F">Category 2</option>
                                                <option value="T">Category 3</option>
                                                <option value="T">Category 4</option>
                                            </select>

                                        </div>

                                        <div class="mb-3 col-md-4 mt-4 hstack">
                                            <button data-repeater-delete=""
                                                class="btn bg-danger-subtle text-danger hstack gap-6" type="button">
                                                <i class="ti ti-trash fs-5"></i>
                                                Delete Row
                                            </button>
                                        </div>
                                        {{-- <div class="mb-3 col-md-2">
                                            <button class="btn btn-success hstack gap-6 mt-2" type="submit">
                                                <i class="ti ti-device-floppy fs-5 "></i>
                                                Save
                                            </button>
                                            <button class="btn btn-warning hstack gap-6 mt-2" type="submit">
                                                <i class="ti ti-notes-off fs-5"></i>
                                                Draft
                                            </button>
                                        </div> --}}
                                        {{-- <div class="mb-3 col-md-12 hstack gap-6">

                                            <button data-repeater-delete=""
                                                class="btn bg-danger-subtle text-danger hstack gap-6" type="button">
                                                <i class="ti ti-trash fs-5"></i>
                                                Delete Row
                                            </button>
                                        </div> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <button data-repeater-create="" class="btn btn-success hstack gap-6">
                                Add
                                <i class="ti ti-circle-plus ms-1 fs-5"></i>
                            </button>
                        </div>
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
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
    <script src="../assets/libs/jquery.repeater/jquery.repeater.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/js/forms/repeater-init.js"></script>
    <script>
        function show_others() {

            var element = document.getElementById("particulars");
            var isChecked = document.getElementById("particulars").checked;

            if (isChecked == false) {


                element.checked = true;
                $('#particulars_div').show();
            } else {
                if (isChecked == true) {

                    element.checked = false;
                    $('#particulars_div').hide();
                }
            }



        }
    </script>
@endsection
