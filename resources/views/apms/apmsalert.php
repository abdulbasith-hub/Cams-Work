<div id="parahistory_details" class="modal fade" tabindex="-1" aria-labelledby="parahistory_details modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-info text-white">
                    <h4 class="modal-title text-white lang" id="info-header-modalLabel" key="para_history_det">
                        Para History Details
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="para_det">

                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Edit
                    </button> --}}
                    <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" key="cancelbtn">
                        Close
                    </button>
                    {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div id="parafull_details" class="modal fade" tabindex="-1" aria-labelledby="parafull_details modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-info text-white">
                    <h4 class="modal-title text-white lang" id="info-header-modalLabel" key="parafull_details_det">
                        Para Details
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="parafull_det">
                        <div class="accordion mb-3" id="paraDetailsAccordion">

                            <div class="accordion-item">
                                <h2 class="accordion-header acc_sub_head" id="paraDetailsHeading">
                                    <button class="accordion-button fw-bold text-center "
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#paraDetailsCollapse"
                                        aria-expanded="true"
                                        aria-controls="paraDetailsCollapse">
                                        Details
                                    </button>
                                </h2>

                                <div id="paraDetailsCollapse"
                                    class="accordion-collapse collapse show"
                                    aria-labelledby="paraDetailsHeading"
                                    data-bs-parent="#paraDetailsAccordion">

                                    <div class="accordion-body p-0">

                                        <!-- PARA DETAILS TABLE -->
                                        <table class="table table-bordered mb-0">
                                            <tbody id="parafull_dettable">
                                                <tr>
                                                    <th>Institution</th>
                                                    <td id="inst_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Title / Heading</th>
                                                    <td id="main_obj"></td>
                                                </tr>
                                                <tr>
                                                    <th>Category</th>
                                                    <td id="sub_obj"></td>
                                                </tr>
                                                <tr>
                                                    <th>Type of Para</th>
                                                    <td id="typeofpara_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>State of Para</th>
                                                    <td id="stateofpara_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Para No</th>
                                                    <td id="parano_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td id="status_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Amount</th>
                                                    <td id="amt_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Severity</th>
                                                    <td id="sev_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Scheme</th>
                                                    <td id="scheme_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Irregularities</th>
                                                    <td id="irreg_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Irregularities Category</th>
                                                    <td id="irreg_cat_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Irregularities SubCategory</th>
                                                    <td id="irg_subcat_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Gist</th>
                                                    <td id="gist_val"></td>
                                                </tr>
                                                <tr>
                                                    <th>Remarks</th>
                                                    <td id="remarks_val"></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- LIABILITY -->
                                        <div class="card mt-2 hide_this" id="liability_tab">
                                            <div class="card-header apms_tab_header text-light fw-bolder text-center">
                                                Liability Details
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Number</th>
                                                            <th>Name</th>
                                                            <th>Designation</th>
                                                            <th>Amount</th>
                                                            <th>Retirement</th>
                                                            <th>Retirement Month</th>
                                                            <th>Retirement Year</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="liability_det_table"></tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="accordion" id="paraHistoryAccordion">

                            <div class="accordion-item">
                                <h2 class="accordion-heade acc_sub_head" id="paraHistoryHeading">
                                    <button class="accordion-button collapsed fw-bold text-center"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#paraHistoryCollapse"
                                        aria-expanded="false"
                                        aria-controls="paraHistoryCollapse">
                                        Para History
                                    </button>
                                </h2>

                                <div id="paraHistoryCollapse"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="paraHistoryHeading"
                                    data-bs-parent="#paraHistoryAccordion">

                                    <div class="accordion-body">
                                        <!-- Your dynamic history accordions -->
                                        <div id="para_history_container"></div>
                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-success lang" data-bs-dismiss="modal" key="verified_label" id = 'verified_btn'>
                        Verified
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div id="minutes_details" class="modal fade" tabindex="-1" aria-labelledby="minutes_details modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-info text-white">
                    <h4 class="modal-title text-white lang" id="info-header-modalLabel" key="minutes_details_det">
                        Minutes of Meeting Details
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="minutes_details">
                        <table class="table table-bordered">
                            <tbody id="minutes_det"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Edit
                    </button> --}}
                    <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" key="cancelbtn">
                        Close
                    </button>
                    {{-- <button type="button" class="btn bg-success-subtle text-success ">
                                    Save changes
                                </button> --}}
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
