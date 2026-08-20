<!--################################################# -- confirmation_alert --###################################################-->

<div class="modal fade" id="confirmation_alert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display:none">
        <div class="modal-dialog  modal-dialog-centered" >
            <div class="modal-content" style="border-color:black">
                <div class="modal-header" style="background-color:#3782ce;" id="alert_modalheader">

                    <h3 id="confirmation_alertmodal" class="text-white lang"  key="confirmation">Confirmation</h3>

                    <button type="button" id="close_button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">


                        <p id="alert_body"></p>
                        <div class="mb-3"></div>
                    </div>
                    <div class="modal-footer" id="modal_footer" >
                        <button type="button" class="btn btn-success lang" data-bs-dismiss="modal" id="ok_button" key="ok"   style="display:none" >OK</button>
                        <button type="button" class="btn btn-success lang" data-bs-dismiss="modal" id="process_button"  style="display:none"><span class="lang" key="ok">OK</span></button>
                        <button type="button" class="btn btn-danger lang" data-bs-dismiss="modal" id="cancel_button"  style="display:none"><span class="lang" key='cancel'>Cancel</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--################################################# -- confirmation_alert --###################################################-->

<!--################################################# -- start large alert --###################################################-->

<div class="modal  bd-example-modal-lg " id="large_confirmation_alert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none">
        <div class="modal-dialog  modal-dialog-centered modal-lg" >
            <div class="modal-content" style="border-color:black">
                <div class="modal-header" style="background-color:#06163a; justify-content: center;"  >

                    <h3 id="large_alert_header" class="text-white lang" style="text-align:center;" key=''></h3>
                    <div id="large_alert_header_two" style="display:none" class="text-white " key='' ></div>

                    <button type="button" id="large_confirmation_button_close" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <p id="large_alert_body">

                        </p>
                        <div class="mb-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success lang"  id="large_modal_ok_button" key="ok" style="display:none">Ok</button>
                        <button type="button" class="btn btn-success lang" data-bs-dismiss="modal" id="large_modal_process_button"key="ok" ></button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="large_modal_cancel_button"   ><span class="lang" key='cancel'>Cancel</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--################################################# -- end large alert --###################################################-->

    <!--################################################# -- start extra large alert --###################################################-->
    <div class="modal fade show extra_large_confirmation_alert" id="extra_large_confirmation_alert" tabindex="-1" aria-labelledby="bs-example-modal-lg" aria-modal="true" role="dialog" style="display:none;">
                        <div class="modal-dialog  modal-dialog-centered modal-lg">
                          <div class="modal-content">
                            <div class="modal-header" style="background-color:#06163a; justify-content: center;"  >

                                <h3 id="extra_large_alert_header" class="text-white lang" style="text-align:center;" key=''></h3>
                                <div id="large_alert_header_two" style="display:none" class="text-white " key='' ></div>

                                <button type="button" id="large_confirmation_button_close" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p id="extra_large_alert_body"></p>                            
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success lang"  id="extra_large_modal_ok_button" key="ok" style="display:none">Ok</button>
                                <button type="button" class="btn btn-success lang" data-bs-dismiss="modal" id="extra_large_modal_process_button"key="ok" ></button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="extra_large_modal_cancel_button"   ><span class="lang" key='cancel'>Cancel</span></button>
                            </div>
                          </div>
                          <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                      </div>
   
   <!--################################################# -- end extra large alert --###################################################-->