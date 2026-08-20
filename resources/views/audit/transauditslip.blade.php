@section('content')
    @extends('index2')
    @include('common.alert')


    <link rel="stylesheet" href="../assets/libs/dragula/dist/dragula.min.css">
    <style>
        .wrap-text-lwf {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 600px;
            /* Adjust as needed */
        }

        .ck-powered-by-balloon {
            display: none !important;
        }

        .equal-height {
            display: flex;
            flex-wrap: wrap;
        }

        .equal-height .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .dd-list .dd-item {
            padding: 1px;
        }

        #pdf-preview iframe {
            width: 100%;
            height: 100%;
            max-height: 590px;
            /* Adjust the percentage to set the maximum height */
            background-color: white;

        }

        /* CSS to increase the modal height */
        #previewmodel_content {
            max-height: 800px;
            /* Adjust the percentage to set the maximum height */
            height: 800px;
            /* Allows the height to adjust based on the content */
        }

        .draggable-item {
            cursor: move;
        }

        .PartBFinalized .draggable-item {
            cursor: default;
        }


        .step-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }

        .step-btn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        .step-btn.active {
            background-color: #007bff;
            color: white;
        }

        .step-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .sub-step-header {
            font-size: 20px;
            font-weight: normal;
            text-align: center;
            color: #2a3547 !important;
        }



        .iframe-container {
            display: flex;
            /* Enables flexbox */
            justify-content: center;
            /* Centers horizontally */
            height: 100%;
            /* Ensure parent container takes full height */
            width: 100%;
        }

        iframe {
            width: 99%;
            height: 640px;
            max-height: 640px !important;
            border: none;
            /* Optional: remove border */
        }

        #partc_iframe {
            width: 80%;
        }



        .dd-handle {
            background-color: #ffffff !important;
        }

        .activatestep {
            background-color: #5d87ff !important;
            /*font-weight:bold !important;*/
        }

        .activatestep span {
            color: #ffffff !important;
            /*font-weight:bold !important;*/
        }

        .activatestep span b {
            color: #ffffff !important;
            /*font-weight:bold !important;*/
        }


        .part_b_dragula,
        #scrollablecontent {
            overflow-y: auto;
            overflow-x: hidden;
            max-height: 650px;
            height: 680px;
            padding-right: 10px;
        }

        /* width */
        #scrollablecontent::-webkit-scrollbar {
            width: 4px;

        }

        /* Track */
        #scrollablecontent::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        #scrollablecontent::-webkit-scrollbar-thumb {
            background: #bbbcbc;
        }

        /* Handle on hover */
        #scrollablecontent::-webkit-scrollbar-thumb:hover {
            background: #888;
        }


        /* width */
        .part_b_dragula::-webkit-scrollbar {
            width: 4px;

        }

        /* Track */
        .part_b_dragula::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        .part_b_dragula::-webkit-scrollbar-thumb {
            background: #bbbcbc;
        }

        /* Handle on hover */
        .part_b_dragula::-webkit-scrollbar-thumb:hover {
            background: #888;
        }

        textarea {
            width: 100%;
            /* Adjust as needed */
            min-height: 200px;
            height: 100px;
        }

        .ck-editor__editable_inline {
            min-height: 300px !important;
            width: 100% !important;
        }

        #lwf_ckeditor {
            width: 100%;
            /* Adjust as needed */
            min-height: 60px;
            height: 60px;
        }



        .file-preview {
            margin-right: 15px;
            /* Adjust spacing as needed */
        }

        body {
            color: #222 !important;
        }

        .disabled-step {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #e7e7e7;
        }

        #pdf-preview {
            height: 550px;
            max-height: 550px !important;

        }



        #pdf-preview.full-height {
            height: 650px !important;
            max-height: 650px !important;
        }

        .enble-chkbox {
            border-color: rgb(62, 62, 63);
            cursor: pointer;
        }

         /* Overlay */
        #ajax-loader {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        /* Spinner */
        #ajax-loader .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 123, 255, 0.3);
            /* Light blue border */
            border-top: 5px solid #007bff;
            /* Solid blue top */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="ajax-loader">
                <div class="spinner"></div>
            </div>
    <?php
    $instdel = json_decode($inst_details, true);

    $financial_yeardata = $yeardata['financialyear'];
    $financial_year = $financial_yeardata[0]->financialyear;
    $audityears = $yeardata['audityears'];

    $auditscheduleid = $instdel[0]['auditscheduleid'];
    $spilloverflag = $instdel[0]['spilloverflag'];

    $instid = $instid;
    $financialyearcode = $encrypted_financialyearcode;
    // Initialize default values
    $auditcertificate_remarks = '';
    $cer_type_code = '';
    $statusflag_auditcertificate = '';
    $itfilings_remarks = '';
    $legalcomplaince_remarks = '';
    $financialreview_remarks = '';
    $authorityofaudit_remarks = '';
    $GenesisofAudit_remarks = '';
    $AccountDetails_remarks = '';
    $auditlevy_remarks = '';
    $pendingparadetails_remarks = '';
    $statusflag_authorityofaudit = '';
    $statusflag_GenesisofAudit = '';
    $statusflag_AccountDetails = '';
    $statusflag_pantan = '';
    $Serstatusflag_storeslip = '';
    $NonSerstatusflag_storeslip = '';
    $Annexturestatus = '';

    //print_r($Contentauthorityofaudit[0]->auth_content_en);exit;
    $AuthorityContent = '';
    if ($Contentauthorityofaudit) {
        $ContentauthorityofauditDecode = json_decode($Contentauthorityofaudit->auth_content_en);
        $AuthorityContent = isset($ContentauthorityofauditDecode->content) ? $ContentauthorityofauditDecode->content : '';
    }

    // Safely access $auditCertificate if it exists
    if (isset($auditCertificate)) {
        $auditcertificate_decoded = json_decode($auditCertificate->cer_remarks ?? '');
        $auditcertificate_remarks = isset($auditcertificate_decoded->content) ? $auditcertificate_decoded->content : '';

        $cer_type_code = $auditCertificate->cer_type_code ?? '';
        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
    }

    // Safely access $AuthorityofAudit
    if (isset($AuthorityofAudit)) {
        $authorityofaudit_decoded = json_decode($AuthorityofAudit->remarks ?? '');
        $authorityofaudit_remarks = isset($authorityofaudit_decoded->content) ? $authorityofaudit_decoded->content : '';

        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
    }

    // Safely access $GenesisofAudit
    if (isset($GenesisofAudit)) {
        $GenesisofAudit_decoded = json_decode($GenesisofAudit->genesis_remarks ?? '');
        $GenesisofAudit_remarks = isset($GenesisofAudit_decoded->content) ? $GenesisofAudit_decoded->content : '';

        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
    }

    // Safely access $AccountDetails
    if (isset($AccountDetails)) {
        $AccountDetails_decoded = json_decode($AccountDetails->account_remarks ?? '');
        $AccountDetails_remarks = isset($AccountDetails_decoded->content) ? $AccountDetails_decoded->content : '';

        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
    }

    // Safely access $Report_PanTan
    if (isset($Report_PanTan)) {
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $itfiling_issueremarks = json_decode($Report_PanTan->itfiling_issue ?? '');
        $itfilings_remarks = isset($itfiling_issueremarks->content) ? $itfiling_issueremarks->content : '';

        $lcremaks = json_decode($Report_PanTan->legal_complaince ?? '');
        $legalcomplaince_remarks = isset($lcremaks->content) ? $lcremaks->content : '';

        $financialreview = json_decode($Report_PanTan->financial_review ?? '');
        $financialreview_remarks = isset($financialreview->content) ? $financialreview->content : '';
    }

    if ($Report_lWF) {
        $pantanData['lwfdata'] = ['lwf_1' => $Report_lWF->lwfq1_remarks, 'lwf_2' => $Report_lWF->lwfq2_remarks, 'lwf_3' => $Report_lWF->lwfq3_remarks, 'lwf_4' => $Report_lWF->lwfq4_remarks];
    }

    if ($Report_GST) {
        $gstdata = [
            'audit_year' => $Report_GST->audityear,
            'q1' => json_decode($Report_GST->det_q1, true),
            'q2' => json_decode($Report_GST->det_q2, true),
            'q3' => json_decode($Report_GST->det_q3, true),
            'q4' => json_decode($Report_GST->det_q4, true),
        ];
    }

    if (isset($Report_Levy)) {
        $Report_Levy_decoded = json_decode($Report_Levy->auditlevy_remarks ?? '');
        $auditlevy_remarks = isset($Report_Levy_decoded->content) ? $Report_Levy_decoded->content : '';
    }

    if (isset($PendingparaDet)) {
        $PendingparaDet_decoded = json_decode($PendingparaDet->pendingpara_remarks ?? '');
        $pendingparadetails_remarks = isset($PendingparaDet_decoded->content) ? $PendingparaDet_decoded->content : '';
    }

    if (isset($serious_report_storesliporder)) {
        $Serstatusflag_storeslip = $serious_report_storesliporder->statusflag ?? '';
    }

    if (isset($Nonserious_report_storesliporder)) {
        $NonSerstatusflag_storeslip = $serious_report_storesliporder->statusflag ?? '';
    }

    if (isset($annexture_finalize)) {
        $Annexturestatus = $annexture_finalize->statusflag ?? '';
    }

    $Preview_show = false;
    $before_finalize = false;

    if ($statusflag_auditcertificate == 'F' && $statusflag_authorityofaudit == 'F' && $statusflag_GenesisofAudit == 'F' && $statusflag_AccountDetails == 'F' && $statusflag_pantan == 'F') {
        $Preview_show = true;
    } elseif ($statusflag_auditcertificate == 'Y' && $statusflag_authorityofaudit == 'Y' && $statusflag_GenesisofAudit == 'Y' && $statusflag_AccountDetails == 'Y' && $statusflag_pantan == 'Y') {
        $before_finalize = true;
    }

    // Function to determine which radio is checked
    function getChecked($data, $field, $value)
    {
        return isset($data[$field]) && $data[$field] === $value ? 'checked' : '';
    }

    ?>
    <div class="card card_border" style="border-color: #7198b9">
        <div class="card-header card_header_color lang" key="AuditSlip" style="padding:10px;">AUDIT REPORT</div>
        <br>
        <div class="card-body card_border">
            <div class="row">
                <div class="col-12">
                    <div class="card  card_border">
                        <div class="card-body" style="border-color: #7198b9">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <center><label class="form-label label_lang" style="font-size:16px;"
                                            data-en="<?php echo $instdel[0]['instename']; ?>"
                                            data-ta="<?php echo $instdel[0]['insttname']; ?>"><?php echo $instdel[0]['instename']; ?></label>
                                    </center>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang" key="department">Department Name</label>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="label_lang" data-en="<?php echo $instdel[0]['deptelname']; ?>"
                                        data-ta="<?php echo $instdel[0]['depttlname']; ?>"><?php echo $instdel[0]['deptelname']; ?></label>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang" key="instcat_label">Institution Category</label>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="label_lang" data-en="<?php echo $instdel[0]['catename']; ?>"
                                        data-ta="<?php echo $instdel[0]['cattname']; ?>"><?php echo $instdel[0]['catename']; ?></label>
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="col-md-2 mb-3">
                                                                        <label class="form-label lang" key = "fasil" key = 'financia'><?= $instdel[0]['deptcode'] === '01' ? 'Fasli Year' : 'Financial Year' ?></label>
                                                                    </div> -->
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang"
                                        key="{{ $instdel[0]['deptcode'] === '01' ? 'fasil' : 'financial' }}">
                                        {{ $instdel[0]['deptcode'] === '01' ? 'Fasli Year' : 'Financial Year' }}
                                    </label>
                                </div>

                                <div class="col-md-1 mb-3">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>
                                        <?php
                                        echo $instdel[0]['yearname'];
                                        ?>
                                    </label>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang" key="typeofauditlabel">Type of Audit</label>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="label_lang" data-en="<?php echo $instdel[0]['typeofauditename']; ?>"
                                        data-ta="<?php echo $instdel[0]['typeofaudittname']; ?>"><?php echo $instdel[0]['typeofauditename']; ?></label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang" key="entrymeeting_date">Entry Meeting Date</label>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label><?php echo date('d-m-Y', strtotime($instdel[0]['entrymeetdate'])); ?></label>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang" key="exitmeetingdate" for="validationDefault01">Exit
                                        Meeting Date</label>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label class="form-label" for="validationDefault01">:</label>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label><?php echo date('d-m-Y', strtotime($instdel[0]['exitmeetdate'])); ?></label>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <div class="row equal-height">
                <div class="col-md-2">
                    <div class="card card_border">
                        <div class="card-body">
                            <div class="step-buttons">
                                <button class="btn btn-outline-primary lang" data-step="part_a" data-stepno="1"
                                    onclick="showStep('part_a')">
                                    <span class="lang" key="part_a_prefill">PART - I</span> <br> <span class="lang"
                                        key="prefill">(PreFilled)</span>
                                </button>

                                <button id="part_b_serstep" class="btn btn-outline-primary lang partb_tit"
                                    data-step="part_b_seriousirregularities" data-stepno="2"
                                    onclick="showStep('part_b_seriousirregularities')">
                                    <span class="lang" key="part_b_prefill">PART - II</span> <br> <span class="lang"
                                        key= "serious_heading">(Serious Irregularities Paras)</span>
                                </button>

                                <button id="part_b_nonserstep" class="btn btn-outline-primary lang partb_tit"
                                    data-step="part_b_nonseriousirregularities" data-stepno="2"
                                    onclick="showStep('part_b_nonseriousirregularities')">
                                    <span class="lang" key="part_b_prefill">PART - II</span> <br> <span class="lang"
                                        key= "nonserious_heading">(Non Serious Irregularities Paras)</span>
                                </button>

                                <button class="btn btn-outline-primary lang partb_tit" data-step="part_b_others"
                                    data-stepno="4" onclick="showStep('part_b_others')">
                                    <span class="lang" key="part_b_prefill">PART - II</span> <br> <span class="lang"
                                        key="others">(Others)</span>
                                </button>

                                <button class="btn btn-outline-primary lang" data-step="part_c" data-stepno="3"
                                    onclick="showStep('part_c')">
                                    <span class="lang" key="part_c_prefill">PART - III</span> <br> <span class="lang"
                                        key="attachments_label">(Attachments)</span>
                                </button>
                                <!-- <button class="btn btn-outline-primary lang partb_tit" key="partb" data-step="part_b" data-stepno="2" onclick="showStep('part_b')">
                                                                                    PART B
                                                                                    </button>
                                                                                    <button class="btn btn-outline-primary lang" key="partc" data-step="part_c" data-stepno="3" onclick="showStep('part_c')">
                                                                                    PART C
                                                                                    </button>-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- PART A -->
                <div class="col-md-3 col-xxl-3 step-container" id="part_a">
                    <div class="card  card_border">
                        <div class="card-body draggable-container">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h4 class="card-title mb-0 lang" style="text-align:center;"><span class="lang"
                                        key="part_a_prefill">PART - I</span> <br> <span class="lang"
                                        key="prefill">(PreFilled)</span></h4>
                            </div>
                            <br>
                            <div class="parta_contents">
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="parta_certificate" data-childstepform ="auditcertificateDiv"
                                            data-step-id="auditcertificate" data-step-index="1"
                                            data-childstep='certificate' onclick="AuditCertificate()"
                                            href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span data-en="Audit Certificate" data-ta="தணிக்கை சான்றிதழ்"
                                                class="text-muted lang" key="auditcertificate_label">Audit
                                                Certificate</span>
                                            <!-- <span class="text-muted lang"  key="auditcertificate_label">Audit Certificate</span> -->

                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="parta_authorityofaudit" data-childstepform ="autorityofaudit"
                                            data-step-id="authorityofaudit" data-step-index="2"
                                            data-heading='Authority of Audit' data-childstep='authorityofaudit'
                                            onclick="AuthorityofAudit('authorityofaudit')" href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang"
                                                key="authorityofauditcertificate_label">Authority of Audit</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="parta_institutedetailsentry" data-childstepform ="autorityofaudit"
                                            data-step-id="genesisofaudit" data-step-index="3"
                                            data-heading='A Brief introduction about Auditee Institution'
                                            data-childstep='institutedetailsentry'
                                            onclick="InstituteGenesis('institutedetailsentry')" href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="informationofauditee_label">A Brief
                                                introduction about Auditee Institution</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="parta_accountdet" data-childstepform ="inst_genesis_div"
                                            data-step-id="accountdetails" data-step-index="4"
                                            data-heading='Accounts and General Informations' data-childstep='accountdet'
                                            onclick="AccountGeneral('accountdet')" href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="account_info_label">Accounts and General
                                                Informations</span>
                                        </a>
                                    </div>

                                    <div class="row mt-3 ">
                                        <div class="col-md-12">
                                            <a id="parta_pan_tan" data-step-id="pantan" data-step-index="5"
                                                data-heading='Filing Status' data-childstep='pan_tan'
                                                onclick="PANTAN('pan_tan')" href="javascript:void(0)"
                                                class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                                style="padding-left:20px;">
                                                <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                                <span class="text-muted lang" key="filling">Filing Status</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- PART B  Serious Irregularities-->
                <div class="col-md-3 col-xxl-3 step-container" id="part_b_seriousirregularities">
                    <div class="card  card_border">
                        <div class="card-body draggable-container">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h4 class="card-title mb-0 lang" style="text-align:center;"><span class="lang"
                                        key="part_b_prefill">PART - II</span> <br> <span class="lang"
                                        key= "serious_heading">(Serious Irregularities Paras)</span></h4>
                                <!--<button class="btn bg-primary-subtle" type="button" id="invest1">
                                                                            PART - A
                                                                        </button>-->
                            </div>
                            <div class="part_b_dragula" id="part-b-serious"><br>
                                @php
                                    $orderedArray = isset($SerOrderingSlips->ser_ordered_slips)
                                        ? json_decode($SerOrderingSlips->ser_ordered_slips, true)
                                        : [];

                                    $orderedSlips = [];

                                    if (!empty($orderedArray)) {
                                        $lookup = collect($FetchAuditslips)->keyBy('auditslipid');
                                        foreach ($orderedArray as $slipId) {
                                            if ($lookup->has($slipId)) {
                                                $orderedSlips[] = $lookup[$slipId];
                                            }
                                        }

                                        // Add remaining unlisted slips
                                        $remaining = collect($FetchAuditslips)->whereNotIn(
                                            'auditslipid',
                                            $orderedArray,
                                        );
                                        foreach ($remaining as $remainingSlip) {
                                            $orderedSlips[] = $remainingSlip;
                                        }
                                    } else {
                                        $orderedSlips = $FetchAuditslips;
                                    }

                                    // Group slips by category and subcategory
                                    $groupedSlips = collect($orderedSlips)->groupBy(function ($item) {
                                        return $item->irregularitiescatelname .
                                            '||' .
                                            $item->irregularitiessubcatelname;
                                    });
                                    $inc = 1;

                                @endphp
                                @foreach ($groupedSlips as $groupKey => $slips)
                                    @php
                                        [$catName, $subCatName] = explode('||', $groupKey);
                                    @endphp
                                    <div style="border:1px solid #7198b9;padding:10px;border-radius:7px;">
                                        <br>
                                        <h6
                                            style="color:#007bff; display: flex; justify-content: center; align-items: center;">
                                            <span
                                                style="display: inline-block; min-width: 28px; height: 28px; line-height: 28px; background-color:rgb(195 195 195); color:#2a3547; border-radius: 50%; text-align: center;">
                                                {{ $inc }}
                                            </span>
                                        </h6>

                                        <h6
                                            style="color:#2a3547; border-bottom:1px dashed rgb(163, 160, 160);padding:10px;padding-top:none !important;">
                                            <strong>{{ $catName }}</strong> - {{ $subCatName }}
                                        </h6>

                                        <div class="sortable-group sortable-group-{{ $loop->index }}">
                                            @foreach ($slips as $slipkey => $slipval)
                                                <div id="partb_auditslip{{ $slipval->auditslipid }}"
                                                    onclick="showStepChild('part_b_seriousirregularities','auditslips','',{{ $slipval->auditslipid }})"
                                                    class="row mt-3 draggable-item">
                                                    <div class="col-md-12">
                                                        <a href="javascript:void(0)"
                                                            class="d-flex rounded-4 border py-3 text-decoration-none"
                                                            style="padding:20px;text-align:left !important;">
                                                            <span class="text-muted" style="color:#2a3547!important;">
                                                                <b class="label_lang" data-en="PARA NO -"
                                                                    data-ta="சீட்டு எண் -">PARA NO -</b>
                                                                <b>{{ $slipval->mainslipnumber }}</b><br>
                                                                <span class="label_lang"
                                                                    data-ta="{{ $slipval->objectiontname }} - {{ $slipval->subobjectiontname }}"
                                                                    data-en="{{ $slipval->objectionename }} - {{ $slipval->subobjectionename }}">
                                                                    [ {{ $slipval->objectionename }} -
                                                                    {{ $slipval->subobjectionename }} ]
                                                                </span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="slipparano_{{ $slipval->mainslipnumber }}"
                                                    value="{{ $slipval->auditslipid }}" />
                                                <input type="hidden" class="orderno_{{ $slipval->mainslipnumber }}"
                                                    value="{{ $loop->index + 1 }}" />

                                                @if ($loop->first && $loop->parent->first)
                                                    <input type="hidden" id="hidden_Slipno_ser"
                                                        value="{{ $slipval->auditslipid }}" />
                                                @endif
                                            @endforeach
                                        </div>
                                    </div><br>
                                    @php
                                        $inc++;

                                    @endphp
                                @endforeach



                            </div>


                        </div>
                    </div>
                </div>

                <!-- PART B  Serious Irregularities-->

                <div class="col-md-3 col-xxl-3 step-container" id="part_b_nonseriousirregularities">
                    <div class="card  card_border">
                        <div class="card-body draggable-container">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h4 class="card-title mb-0 lang" style="text-align:center;"><span class="lang"
                                        key="part_b_prefill">PART - II</span> <br> <span class="lang"
                                        key="nonserious_heading">(Non Serious Irregularities Paras)</span></h4>
                                <!--<button class="btn bg-primary-subtle" type="button" id="invest1">
                                                                            PART - A
                                                                        </button>-->
                            </div>
                            <div class="part_b_dragula" id="part-b-nonserious">
                                @php
                                    $orderedArray = isset($NonSerOrderingSlips->nonser_ordered_slips)
                                        ? json_decode($NonSerOrderingSlips->nonser_ordered_slips, true)
                                        : [];

                                    // Re-index $FetchAuditslips using the order in $orderedArray
                                    $orderedSlips = [];

                                    if (!empty($orderedArray)) {
                                        $lookup = collect($FetchAuditslips_NonIrrReg)->keyBy('auditslipid');

                                        foreach ($orderedArray as $pos => $slipId) {
                                            if ($lookup->has($slipId)) {
                                                $orderedSlips[] = $lookup[$slipId];
                                            }
                                        }

                                        // Add any remaining slips not in the ordering (optional)
                                        $remaining = collect($FetchAuditslips_NonIrrReg)->whereNotIn(
                                            'auditslipid',
                                            $orderedArray,
                                        );
                                        foreach ($remaining as $remainingSlip) {
                                            $orderedSlips[] = $remainingSlip;
                                        }
                                    } else {
                                        $orderedSlips = $FetchAuditslips_NonIrrReg;
                                    }

                                    // Group slips by category and subcategory
                                    $groupedSlips = collect($orderedSlips)->groupBy(function ($item) {
                                        return $item->irregularitiescatelname .
                                            '||' .
                                            $item->irregularitiessubcatelname;
                                    });
                                    $inc = 1;

                                @endphp

                                @foreach ($groupedSlips as $groupKey => $slips)
                                    @php
                                        [$catName, $subCatName] = explode('||', $groupKey);
                                    @endphp
                                    <div style="border:1px solid #7198b9;padding:10px;border-radius:7px;">
                                        <br>
                                        <h6
                                            style="color:#007bff; display: flex; justify-content: center; align-items: center;">
                                            <span
                                                style="display: inline-block; min-width: 28px; height: 28px; line-height: 28px; background-color:rgb(195 195 195); color:#2a3547; border-radius: 50%; text-align: center;">
                                                {{ $inc }}
                                            </span>
                                        </h6>

                                        <h6
                                            style="color:#2a3547; border-bottom:1px dashed rgb(163, 160, 160);padding:10px;padding-top:none !important;">
                                            <strong>{{ $catName }}</strong> - {{ $subCatName }}
                                        </h6>

                                        <div class="sortable-group sortable-group-{{ $loop->index }}">
                                            @foreach ($slips as $slipkey => $slipval)
                                                <div id="partb_auditslip{{ $slipval->auditslipid }}"
                                                    onclick="showStepChild('part_b_nonseriousirregularities','auditslips','',{{ $slipval->auditslipid }})"
                                                    class="row mt-3 draggable-item">
                                                    <div class="col-md-12">
                                                        <a href="javascript:void(0)"
                                                            class="d-flex rounded-4 border py-3 text-decoration-none"
                                                            style="padding:20px;text-align:left !important;">
                                                            <span class="text-muted" style="color:#2a3547!important;">
                                                                <b class="label_lang" data-en="PARA NO -"
                                                                    data-ta="சீட்டு எண் -">PARA NO </b>-
                                                                <b>{{ $slipval->mainslipnumber }}</b><br>
                                                                <span class="label_lang"
                                                                    data-ta="{{ $slipval->objectiontname }} - {{ $slipval->subobjectiontname }}"
                                                                    data-en="{{ $slipval->objectionename }} - {{ $slipval->subobjectionename }}">
                                                                    [ {{ $slipval->objectionename }} -
                                                                    {{ $slipval->subobjectionename }} ]
                                                                </span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="slipparano_{{ $slipval->mainslipnumber }}"
                                                    value="{{ $slipval->auditslipid }}" />
                                                <input type="hidden" class="orderno_{{ $slipval->mainslipnumber }}"
                                                    value="{{ $loop->index + 1 }}" />

                                                @if ($loop->first && $loop->parent->first)
                                                    <input type="hidden" id="hidden_Slipno_nonser"
                                                        value="{{ $slipval->auditslipid }}" />
                                                @endif
                                            @endforeach
                                        </div>
                                    </div><br>
                                    @php
                                        $inc++;

                                    @endphp
                                @endforeach



                            </div>


                        </div>
                    </div>
                </div>


                <div class="col-md-3 col-xxl-3 step-container hide_this" id="part_b_others" style="">
                    <div class="card  card_border">
                        <div class="card-body draggable-container">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h4 class="card-title mb-0 lang" style="text-align:center;"><span class="lang"
                                        key="">PART - II</span> <br> <span class="lang"
                                        key="others">(Others)</span></h4>
                            </div>
                            <br>
                            <div class="partb_contents">
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="partb_auditlevycertificate" onclick="AuditLevyCertificate()"
                                            href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="auditefees"
                                                style="color:#2a3547!important;">Audit Fees / Audit Levy
                                                Ceritificate</span>

                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="partb_pendingpara" data-heading='Authority of Audit'
                                            data-childstep='authorityofaudit' onclick="PendingParaDet()"
                                            href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="pendingpara"
                                                style="color:#2a3547!important;">Pending Paras Details</span>
                                        </a>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>




                <!-- PART C -->
                <div class="col-md-3 col-xxl-3 step-container hide_this" id="part_c" style="">
                    <div class="card  card_border">
                        <div class="card-body draggable-container">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <h4 class="card-title mb-0 lang" style="text-align:center;"><span class="lang"
                                        key="part_c_prefill">PART - III</span> <br> <span class="lang"
                                        key="attachments_label">(Attachments)</span></h4>
                            </div>
                            <br>
                            <div class="partc_contents">
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="partc_annexure" data-childstep='certificate' onclick="Annexures()"
                                            href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="listofannexture"
                                                style="color:#2a3547!important;">List of Annexures</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-3 ">
                                    <div class="col-md-12">
                                        <a id="partc_accountstatement" data-heading='Authority of Audit'
                                            data-childstep='authorityofaudit'
                                            onclick="Accounts_Statements('authorityofaudit')" href="javascript:void(0)"
                                            class="d-flex align-items-center  rounded-4 border py-3 text-decoration-none"
                                            style="padding-left:20px;">
                                            <i class="fa fa-calendar text-warning me-2" style="font-size:15px;"></i>
                                            <span class="text-muted lang" key="listofaccounts"
                                                style="color:#2a3547!important;">List of Accounts and Statements</span>
                                        </a>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>


                <!--PREVIEW FILE -->
                <div class="col-md-7 col-xxl-7">
                    <div class="card  card_border" id="scrollablecontent1">
                        <div class="card-body">
                            <div class="form-container iframecontainer">

                                <div class="downloadbtndiv"></div>
                                <div class="step-header lang"></div>
                                <div class="sub-step-header"></div>
                                <div class="hideotherdiv hide_this" id="auditlevycert_div">
                                    <form id="Audit_Levy_Form">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $instdel[0]['auditscheduleid']; ?>" /><br>
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?php echo $instdel[0]['instid']; ?>" /><br>
                                        <span class="fs-3 text-muted"><b class="lang"
                                                key="remarks"style="color:#2a3547!important;">Remarks</b></span>
                                        <textarea name="levycertificate_ckeditor" id="levycertificate_ckeditor" rows="10" cols="80"></textarea>
                                    </form>
                                </div>

                                <div class="hideotherdiv hide_this" id="pendingparadet_div">
                                    <form id="Pending_Paras_Form">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $instdel[0]['auditscheduleid']; ?>" /><br>
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?php echo $instdel[0]['instid']; ?>" /><br>
                                        <span class="fs-3 text-muted"><b class="lang" key="remarks"
                                                style="color:#2a3547!important;">Remarks</b></span>
                                        <textarea name="pendingparadet_ckeditor" id="pendingparadet_ckeditor" rows="10" cols="80"></textarea>
                                    </form>
                                </div>
                                <div class="hideotherdiv hide_this" id="slip_details_div">
                                    <form id="slipform">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $instdel[0]['auditscheduleid']; ?>" />
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?php echo $instdel[0]['instid']; ?>" />
                                        <input type="hidden" name="parnohidden" id="parnohidden" />
                                        <input type="hidden" name="ordernohidden" id="ordernohidden" />
                                        <br>
                                        <label for="message" class="form-label lang" key ="gist"><b>Gist of
                                                Para</b></label>
                                        <textarea class="form-control" name="slipdetails_data" id="slipdetails_data"></textarea>
                                        <input type="hidden" name="deptcode" id="deptcode"
                                            value="<?php echo $instdel[0]['deptcode']; ?>" /><br>
                                        <label for="message" class="form-label lang" key="para_details"><b>Para
                                                Details</b></label>
                                        <textarea name="slip_ckeditor" id="slip_ckeditor" rows="10" cols="80"></textarea>
                                        <br>
                                        <h5 id="files_heading" class="lang" key="attachments">Attachments</h5>

                                        <input type="hidden" id="file_upload_ids" name="file_upload_ids"
                                            value="">

                                        <div class="file-input-container" id="file-input-container">
                                            <div id="file-list" class="mb-2 d-flex"></div>

                                            <template id="file-preview">
                                                <input type="hidden" class="file_upload_id" />

                                                <div class="position-relative mr-3 text-center file-preview"
                                                    style="width: min-content;">
                                                    <div class="img-thumbnail">
                                                        <img src="" height="70" alt="file preview">
                                                        <div class="position-absolute text-white fs-40 text-wrap bg-dark p-1 small w-100"
                                                            style="bottom: 0; left: 0; opacity: 0.8;">
                                                            <span class="file-name fs-40">File Name</span> |
                                                            <span class="file-size fs-40">0 KB</span>
                                                        </div>
                                                        <!--<button type="button" class="btn btn-sm btn-danger position-absolute remove-file"
                                                                                                        style="top: 5px; right: 5px;">
                                                                                                <span aria-hidden="true">×</span>
                                                                                                </button>-->
                                                    </div>
                                                    <input class="d-none" multiple type="file" name="fileupload[]">
                                                </div>
                                            </template>
                                        </div>

                                    </form>
                                </div>
                                <div class="hideotherdiv hide_this" id="inst_genesis_div">
                                    <form id="Institute_Genesis_Form">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $instdel[0]['auditscheduleid']; ?>" /><br>
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?php echo $instdel[0]['instid']; ?>" /><br>
                                        <span class="fs-3 text-muted"><b class="lang" key="remarks"
                                                style="color:#2a3547!important;">Remarks</b></span>
                                        <textarea name="genesis_ckeditor" id="genesis_ckeditor" rows="10" cols="80"></textarea>
                                    </form>
                                </div>

                                <div class="hideotherdiv hide_this" id="autorityofaudit">
                                    <form id="authorityofauditForm">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $instdel[0]['auditscheduleid']; ?>" />
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?php echo $instdel[0]['instid']; ?>" />

                                        <div
                                            style="border:1px solid #f4f4f4;background-color:#f4f4f4;border-radius:20px;padding:30px;">
                                            {!! $AuthorityContent !!}
                                        </div>


                                    </form>
                                </div>

                                <div class="hideotherdiv hide_this" id="bankacc_det">
                                    <?php
                                    // Decode all JSON fields for use
                                    $accountRemarks = json_decode($AccountDetails->account_remarks ?? '', true);
                                    $account_details = json_decode($AccountDetails->account_details ?? '{}', true);
                                    $bank_account_number = json_decode($AccountDetails->bank_account_number ?? '{}', true);
                                    $ob = json_decode($AccountDetails->ob ?? '{}', true);
                                    $receipts = json_decode($AccountDetails->receipts ?? '{}', true);
                                    $total = json_decode($AccountDetails->total ?? '{}', true);
                                    $expenditure = json_decode($AccountDetails->expenditure ?? '{}', true);
                                    $cb_cashbook = json_decode($AccountDetails->cb_cashbook ?? '{}', true);
                                    $add = json_decode($AccountDetails->add ?? '{}', true);
                                    $less = json_decode($AccountDetails->less ?? '{}', true);
                                    $cb_passbook = json_decode($AccountDetails->cb_passbook ?? '{}', true);
                                    $scheme = json_decode($AccountDetails->scheme ?? '{}', true);
                                    $branch = json_decode($AccountDetails->branch ?? '{}', true);
                                    $accountype = json_decode($AccountDetails->accounttype ?? '{}', true);
                                    if (
                                        empty($AccountDetails->bank_account_number) || // covers null, '', []
                                        (is_array($accountRemarks) && in_array(null, $accountRemarks, true)) // covers [null]
                                    ) {
                                        echo "<script>onchange_availability('N');</script>";
                                    }
                                    $entryCount = count($account_details) ?: 1;

                                    // Get the list of entry keys
                                    $entryKeys = array_keys($account_details);
                                    if (empty($entryKeys)) {
                                        $entryKeys = [1]; // default for insert
                                    }
                                    ?>

                                    <form id="accountdetails_form" method="POST">
                                        @csrf

                                        <input type="hidden" name="accdet_id" id="accdet_id"
                                            value="<?= $AccountDetails->accdet_id ?? '' ?>">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?= $instdel[0]['auditscheduleid'] ?>">
                                        <input type="hidden" name="instid" id="instid"
                                            value="<?= $instdel[0]['instid'] ?>">

                                        <!--<h5 class="lang" key="noofaccount_label">A. No of Accounts Maintained and its details</h5><br>
                                                                                        <textarea name="accountdetails_ckeditor" id="accountdetails_ckeditor" rows="10" cols="80"></textarea>-->
                                        <div class="col-md-3 mb-1 mt-2 notfortemp">

                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="availability" id="Y" value="Y" checked
                                                        onchange="onchange_availability()">
                                                    <label class="form-check-label lang" for="all"
                                                        key="">Available</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="availability" id="N" value="N"
                                                        onchange="onchange_availability()">
                                                    <label class="form-check-label lang" for="district"
                                                        key="">Unavailable</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div" id="account_table">

                                            <h5 class="lang mt-4">Accounts maintained and its details and Reconciliation
                                            </h5>

                                            <p>The institution maintains the following bank accounts for various operational
                                                and scheme-related purposes. The account details along with the status of
                                                reconciliation up to the audit period are furnished below</p>

                                            <div id="entries-container" class="container mt-4"
                                                style="overflow-y:auto; max-height:300px;">
                                                <style>
                                                    .entry-table-container {
                                                        overflow-x: auto;
                                                    }

                                                    table.entry-table {
                                                        width: max-content;
                                                        min-width: 100%;
                                                        border-collapse: collapse;
                                                    }

                                                    table.entry-table th,
                                                    table.entry-table td {
                                                        border: 1px solid #dee2e6;
                                                        padding: 0.5rem;
                                                        white-space: nowrap;
                                                    }

                                                    table.entry-table th {
                                                        background-color: #f4f4f4;
                                                    }

                                                    table.entry-table th.sticky-col,
                                                    table.entry-table td.sticky-col {
                                                        position: sticky;
                                                        left: 0;
                                                        background: white;
                                                        z-index: 2;
                                                        background-color: #f4f4f4;
                                                        text-align: center;
                                                    }

                                                    table.entry-table th.sticky-buttons,
                                                    table.entry-table td.sticky-buttons {
                                                        position: sticky;
                                                        right: 0;
                                                        background: #f4f4f4;
                                                        z-index: 2;
                                                    }

                                                    .btn-small {
                                                        width: 30px;
                                                        height: 30px;
                                                        font-weight: bold;
                                                        padding: 0;
                                                    }
                                                </style>

                                                <div class="entry-table-container">
                                                    <table class="entry-table table bg-white">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:3% !important;text-align:center !important;"
                                                                    class="lang sticky-col">#</th>
                                                                <th style="width:13% !important;text-align:center !important;"
                                                                    class="lang" key="namescheme">Name of Scheme /
                                                                    Purpose</th>
                                                                <th style="width:13% !important;text-align:center !important;"
                                                                    class="lang" key="bank">Bank Name</th>
                                                                <th style="width:13% !important;text-align:center !important;"
                                                                    class="lang" key="branch">Branch</th>

                                                                <th style="width:13% !important;text-align:center;"
                                                                    class="lang" key="bank_acc_no">Bank Account Number
                                                                </th>
                                                                <th style="width:13% !important;text-align:center;"
                                                                    class="lang" key="">Account Type</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang"
                                                                        key="cashbook">Cashbook</span><br>OB<br>(1)</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang"
                                                                        key="receipts">Receipts</span><br>(2)</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang"
                                                                        key="total">Total</span><br>(3)<br>(1+2)</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang"
                                                                        key="expenditure">Expenditure</span><br>(4)</th>
                                                                <th style="width:10% !important;text-align:center;"><span
                                                                        class="lang" key="cb_cashbook">CB as per
                                                                        Cashbook</span><br>(5)<br>(3 - 4)</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang" key="cheq">Unrealized
                                                                        Cheques</span> <br><span class="lang"
                                                                        key="Add">Add</span><br>(6)</th>
                                                                <th style="width:8% !important;text-align:center;"><span
                                                                        class="lang" key="discheq">Dishonoured
                                                                        Cheque</span><br><span class="lang"
                                                                        key="less">Less</span><br>(7)</th>
                                                                <th style="width:10% !important;text-align:center;"><span
                                                                        class="lang" key="cb_passbook">CB as per
                                                                        Passbook</span><br>(8)<br>(5+6-7)</th>
                                                                <th style="width:5% !important;text-align:center;"
                                                                    class="lang actionshide" key="action"
                                                                    class="sticky-buttons">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="accountdetails_tbody" id="entry-table-body">
                                                            <?php $index = 0; ?>
                                                            @foreach ($entryKeys as $index => $key)
                                                                <tr data-index="{{ $index }}">
                                                                    <td class="sticky-col">{{ $index + 1 }}</td>
                                                                    <td><input type="text" maxlength="200"
                                                                            class="form-control "
                                                                            name="scheme[{{ $index }}]"
                                                                            value="{{ $scheme[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="20"
                                                                            class="form-control "
                                                                            name="account_details[{{ $index }}]"
                                                                            value="{{ $account_details[$key] ?? '' }}">
                                                                    </td>
                                                                    <td><input type="text" maxlength="20"
                                                                            class="form-control "
                                                                            name="branch[{{ $index }}]"
                                                                            value="{{ $branch[$key] ?? '' }}"></td>
                                                                    <td><input type="number" maxlength="16"
                                                                            class="form-control"
                                                                            name="bank_account_number[{{ $index }}]"
                                                                            value="{{ $bank_account_number[$key] ?? '' }}">
                                                                    </td>
                                                                    <td><select class="form-control" id="account_type"
                                                                            name="account_type[{{ $index }}]">
                                                                            <option value="">--select account type--
                                                                            </option>
                                                                            <option value="01"
                                                                                data-name-en="Savings Account"
                                                                                data-name-tn="சேமிப்பு கணக்கு"
                                                                                {{ ($accountype[$key] ?? '') == '01' ? 'selected' : '' }}>
                                                                                Savings Account</option>
                                                                            <option value="02" data-name-en="Current"
                                                                                data-name-tn="தற்போதைய கணக்கு"
                                                                                {{ ($accountype[$key] ?? '') == '02' ? 'selected' : '' }}>
                                                                                Current Account</option>
                                                                        </select></td>
                                                                    <td width="10%"><input maxlength="15"
                                                                            type="text"
                                                                            class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                                            name="ob[{{ $index }}]"
                                                                            value="{{ $ob[$key] ?? '' }}">

                                                                    </td>

                                                                    <td><input maxlength="15" type="text"
                                                                            class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                                            name="receipts[{{ $index }}]"
                                                                            value="{{ $receipts[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control" readonly
                                                                            name="total[{{ $index }}]"
                                                                            value="{{ $total[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                                            name="expenditure[{{ $index }}]"
                                                                            value="{{ $expenditure[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control" readonly
                                                                            name="cb_cashbook[{{ $index }}]"
                                                                            value="{{ $cb_cashbook[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                                            name="add[{{ $index }}]"
                                                                            value="{{ $add[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                                                            name="less[{{ $index }}]"
                                                                            value="{{ $less[$key] ?? '' }}"></td>
                                                                    <td><input type="text" maxlength="15"
                                                                            class="form-control" readonly
                                                                            name="cb_passbook[{{ $index }}]"
                                                                            value="{{ $cb_passbook[$key] ?? '' }}"></td>
                                                                    <td class="sticky-buttons actionshide">
                                                                        <button id="add-entry-btn" type="button"
                                                                            class="btn btn-success btn-small"
                                                                            onclick="addRow(this)">+</button>
                                                                        <button id="del-entry-btn" type="button"
                                                                            class="btn btn-danger btn-small"
                                                                            onclick="removeRow(this)">−</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="hideotherdiv hide_this" id="annexturediv">
                                    <div class="container mt-4">
                                        <table style="width:100% !important;" class="entry-table table bg-white">
                                            <thead>
                                                <tr>
                                                    <th style="width:5% !important;text-align:center !important;"
                                                        class="text-wrap lang" key="annexureno">Annexure No</th>
                                                    <th style="width:40% !important;text-align:center !important;"
                                                        class="text-wrap lang" key="subject">Subject</th>
                                                    <th style="width:15% !important;text-align:center !important;"
                                                        class="text-wrap lang" key="parano">Para No</th>
                                                    <th style="width:40% !important;text-align:center !important;"
                                                        class="text-wrap lang" key="attachments">Attachments</th>
                                                </tr>
                                            </thead>
                                            <tbody id="entry-table-body" class="annexture_tbody">
                                                <tr>
                                                    <td colspan="4" style="text-align:center;">Loading...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="hideotherdiv hide_this" id="accountstatementdiv">
                                    <form id="attachmentForm" enctype="multipart/form-data">
                                        <div id="emptydivshow"></div>
                                        <div id="tablediv" class="container mt-4">
                                            <!-- Common Header -->
                                            <div class="row mb-2 fw-bold">
                                                <div class="col-md-4 lang" key="accparticular"><b>Account Particulars
                                                        Type</b></div>
                                                <div class="col-md-6 lang" key="file_upload"><b>File Upload</b> </div>
                                                <div class="col-md-2 annexturebtnset lang" key="action"><b>Actions</b>
                                                </div>
                                            </div>
                                            <div id="appendannextures">

                                            </div>

                                            <span style="color:red;">
                                                <span class="lang" key="note">Note:</span><br>
                                                <span class="lang" key="filesize">1) File size must be less than
                                                    2MB</span><br>
                                                <span class="lang" key="filetype">2) Allowed file types: PDF,
                                                    Excel</span>
                                            </span>


                                            <!--<div class="mb-3">
                                                                                                <label for="annexure1" class="form-label">a) Receipts and Charges Account</label>
                                                                                                <input class="form-control" type="file" id="annexure1" name="annexure_files[receipts_charges]" accept=".pdf, .xls, .xlsx">
                                                                                                <div class="file-preview mt-2" data-key="receipts_charges"></div>

                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label for="annexure2" class="form-label">b) Trial Balance</label>
                                                                                                <input class="form-control" type="file" id="annexure2" name="annexure_files[trial_balance]" accept=".pdf, .xls, .xlsx">
                                                                                                <div class="file-preview mt-2" data-key="trial_balance"></div>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label for="annexure3" class="form-label">c) Income and Expenditure Account</label>
                                                                                                <input class="form-control" type="file" id="annexure3" name="annexure_files[income_expenditure]" accept=".pdf, .xls, .xlsx">
                                                                                                 <div class="file-preview mt-2" data-key="income_expenditure"></div>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label for="annexure4" class="form-label">d) Balance Sheet</label>
                                                                                                <input class="form-control" type="file" id="annexure4" name="annexure_files[balance_sheet]" accept=".pdf, .xls, .xlsx">
                                                                                                 <div class="file-preview mt-2" data-key="balance_sheet"></div>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label class="form-label"><strong>e) <u>Schedules to I&E / P&L Account and Balance Sheet</u></strong></label>
                                                                                                <div style="padding:20px;">
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">1) Liabilities</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_liabilities]" accept=".pdf, .xls, .xlsx">
                                                                                                             <div class="file-preview mt-2" data-key="schedules_liabilities"></div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">2) Assets</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_assets]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_assets"></div>
                                                                                                        </div>
                                                                                                    </div><br>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">3) Manufacturing / Processing account</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_manufacturing]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_manufacturing"></div>

                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">4) Trading account</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_trading]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_trading"></div>
                                                                                                        </div>
                                                                                                    </div><br>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">5) Profit & Loss Accounts/Income & Expenditure Account</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_pl]" accept=".pdf, .xls, .xlsx">
                                                                                                             <div class="file-preview mt-2" data-key="schedules_pl"></div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">6) DCB Statements</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_dcb]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_dcb"></div>
                                                                                                        </div>
                                                                                                    </div><br>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">7) Depreciation Statement</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_depreciation]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_depreciation"></div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">8) Bank Reconciliation Statement</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_brs]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_brs"></div>
                                                                                                        </div>
                                                                                                    </div><br>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">9) Fund Flow / Cash Flow Statement</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[schedules_cashflow]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="schedules_cashflow"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label for="annexure5" class="form-label">f) Statement of Loans / Investments / Deposits / Advances</label>
                                                                                                <input class="form-control" type="file" id="annexure5" name="annexure_files[loans_investments]" accept=".pdf, .xls, .xlsx">
                                                                                                 <div class="file-preview mt-2" data-key="loans_investments"></div>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label class="form-label"><strong>g) <u>Grant Statement</u></strong></label>
                                                                                                <div style="padding:20px;">
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">1) Grant Received</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[grant_received]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="grant_received"></div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">2) Grant Received & Utilized</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[grant_utilized]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="grant_utilized"></div>
                                                                                                        </div>
                                                                                                    </div><br>
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="form-label">3) Unutilized Grant Details</label>
                                                                                                            <input class="form-control" type="file" name="annexure_files[grant_unutilized]" accept=".pdf, .xls, .xlsx">
                                                                                                            <div class="file-preview mt-2" data-key="grant_unutilized"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>-->

                                        </div>

                                        <br>
                                        <div id="saveannextures" style="border-top:1px dashed #a3a0a0;"
                                            class="row justify-content-center">
                                            <div class="col-md-4 mx-auto">
                                                <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                                    value="<?php echo $instdel[0]['auditscheduleid']; ?>" /><br>
                                                <button class="btn button_save mt-3 lang" type="submit" action="insert"
                                                    name="buttonaction">
                                                    Save Attachments
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                <div class="hideotherdiv hide_this" id="pantan_div">
                                    <form id="pantan_form">
                                        <input type="hidden" name="auditscheduleid"
                                            value="<?= $instdel[0]['auditscheduleid'] ?>" />
                                        <input type="hidden" name="instid" value="<?= $instdel[0]['instid'] ?>" />


                                        <h5><b><u class ='lang' key = "tdsdetails">a)TDS Filed Details</u></b></h5>

                                        <div style="padding:20px;">
                                            <div style="padding:10px;" class="form-check">
                                                <input class="form-check-input success" type="radio" name="tds_avail"
                                                    value="01" id="radio2">
                                                <label class="form-check-label lang" for="radio2"
                                                    style="color:#2a3547!important;" key="avail_label">Available</label>
                                            </div>
                                            <div style="padding:10px;" class="form-check">
                                                <input class="form-check-input danger" type="radio" name="tds_avail"
                                                    value="02" id="radio1">
                                                <label class="form-check-label lang" for="radio1"
                                                    style="color:#2a3547!important;" key='notavail_label'>Not
                                                    Available</label>
                                            </div>
                                        </div>
                                        <div id="tds_availdiv" style="overflow-y:auto; max-height:300px;">
                                            <table id="tdsfilled_table" class="entry-table table bg-white">
                                                <thead>
                                                    <tr>
                                                        <th style="width:19% !important;text-align:center;" class="lang"
                                                            key="financeyr">Financial Year</th>
                                                        <th style="width:19% !important;text-align:center;" class="lang"
                                                            key="section">Section</th>
                                                        <th style="width:19% !important;text-align:center;" class="lang"
                                                            key="period_label">Period</th>
                                                        <th style="width:19% !important;text-align:center;" class="lang"
                                                            key="remittance">Remittance on time </th>
                                                        <th style="width:19% !important;text-align:center;"
                                                            class="lang text-wrap" key="returnfiled">Returns Filed Before
                                                            <br> the Due date
                                                        </th>
                                                        <th style="width:19% !important;text-align:center;" class="lang"
                                                            key="action">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tds_filed_body">
                                                    @php $tdsIndex = 0; @endphp
                                                    @foreach ($tdsFiledData as $row)
                                                        <tr class="entry-row" data-index="{{ $tdsIndex }}">
                                                            <td>
                                                                <select class="form-select"
                                                                    name="tdsdata[{{ $tdsIndex }}][year]">
                                                                    <option value="" class="label_lang"
                                                                        data-en="Select Year"
                                                                        data-ta="ஆண்டைத் தேர்ந்தெடுக்கவும்">Select Year
                                                                    </option>
                                                                    @foreach ($audityears as $year)
                                                                        <option value="{{ $year->reportfinancialyear }}"
                                                                            @if ($row->audityear == $year->reportfinancialyear) selected @endif>
                                                                            {{ $year->reportfinancialyear }}
                                                                        </option>
                                                                    @endforeach

                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-select"
                                                                    name="tdsdata[{{ $tdsIndex }}][status]">
                                                                    <option value="">Select Status</option>
                                                                    <option value="24Q"
                                                                        {{ $row->filing_status == '24Q' ? 'selected' : '' }}>
                                                                        24Q</option>
                                                                    <option value="26Q"
                                                                        {{ $row->filing_status == '26Q' ? 'selected' : '' }}>
                                                                        26Q</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-select"
                                                                    name="tdsdata[{{ $tdsIndex }}][period]">
                                                                    <option value="">Select Period</option>
                                                                    <option value="Q1"
                                                                        {{ $row->auditquarter == 'Q1' ? 'selected' : '' }}>
                                                                        Q1</option>
                                                                    <option value="Q2"
                                                                        {{ $row->auditquarter == 'Q2' ? 'selected' : '' }}>
                                                                        Q2</option>
                                                                    <option value="Q3"
                                                                        {{ $row->auditquarter == 'Q3' ? 'selected' : '' }}>
                                                                        Q3</option>
                                                                    <option value="Q4"
                                                                        {{ $row->auditquarter == 'Q4' ? 'selected' : '' }}>
                                                                        Q4</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input success"
                                                                        type="radio"
                                                                        name="tdsdata[{{ $tdsIndex }}][remit]"
                                                                        value="Yes"
                                                                        {{ $row->remit_on_time == 'Yes' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        key= 'yes'>Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input danger"
                                                                        type="radio"
                                                                        name="tdsdata[{{ $tdsIndex }}][remit]"
                                                                        value="No"
                                                                        {{ $row->remit_on_time == 'No' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        key = 'no'>No</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input success"
                                                                        type="radio"
                                                                        name="tdsdata[{{ $tdsIndex }}][filed]"
                                                                        value="Yes"
                                                                        {{ $row->returns_filed == 'Yes' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        key= 'yes'>Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input danger"
                                                                        type="radio"
                                                                        name="tdsdata[{{ $tdsIndex }}][filed]"
                                                                        value="No"
                                                                        {{ $row->returns_filed == 'No' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        key ='no'>No</label>
                                                                </div>
                                                            </td>
                                                            <td class="sticky-buttons">
                                                                <button type="button" class="btn btn-success btn-small"
                                                                    onclick="addRowTDS()">+</button>
                                                                <button type="button" class="btn btn-danger btn-small"
                                                                    onclick="removeRowTDS(this)">−</button>
                                                            </td>
                                                        </tr>
                                                        @php $tdsIndex++; @endphp
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                        <h5><b><u class="lang" key = "itfill">b) Issues in IT filing</u></b></h5>
                                        <textarea name="itfiling_issue" id="itfiling_issue" rows="10" cols="80"></textarea>
                                        <br>
                                        <h5><b><u class= "lang" key ="gstreturn">c) GST Return filed
                                                    details(GSTR-3B)</u></b></h5>
                                        <div id="" style="overflow-y:auto; max-height:300px;">
                                            <!-- Select Audit Year Dropdown -->
                                            @php
                                                $currentMonth = now()->month;
                                                $currentYear = now()->year;
                                                $financialYear =
                                                    $currentMonth >= 4
                                                        ? $currentYear . '-' . ($currentYear + 1)
                                                        : $currentYear - 1 . '-' . $currentYear;

                                                $financialYear = '2024-2025';
                                            @endphp

                                            <div style="margin-bottom: 10px; text-align: right;">
                                                <label for="audit_year" class="lang" key="financeyr"
                                                    style="margin-right: 10px;"><b>Financial Year:</b></label>
                                                <select id="audit_year" name="gstdata[audit_year]"
                                                    class="form-control" style="display: inline-block; width: auto;">
                                                    <option value="">-- Select Financial Year --</option>
                                                    @foreach ($audityears as $year)
                                                        <option value="{{ $year->reportfinancialyear }}"
                                                            {{ isset($gstdata['audit_year']) && $gstdata['audit_year'] == $year->reportfinancialyear ? 'selected' : '' }}>
                                                            {{ $year->reportfinancialyear }}
                                                        </option>
                                                    @endforeach


                                                </select>


                                            </div>
                                            <table id="" class="entry-table table bg-white">
                                                <thead>
                                                    <tr>
                                                        <th style="width:19% !important;text-align:center;"
                                                            class="lang" key="period_label">Period</th>
                                                        <th style="width:19% !important;text-align:center;"
                                                            class="lang text-wrap" key="remittance_ontime">Whether
                                                            remittance on time</th>
                                                        <th style="width:19% !important;text-align:center;"
                                                            class="lang text-wrap" key="duedatebefore">Filed before due
                                                            date</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="" id="entry-table-body">

                                                    @php
                                                        $quarters = ['q1', 'q2', 'q3', 'q4'];
                                                    @endphp

                                                    @foreach ($quarters as $q)
                                                        <tr>
                                                            <td style="text-align:center;">{{ strtoupper($q) }}</td>

                                                            {{-- Remittance --}}
                                                            <td style="text-align:center;">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input  success"
                                                                        type="radio"
                                                                        name="gstdata[{{ $q }}][remit]"
                                                                        id="gstdata{{ $q }}_remit_yes"
                                                                        value="Yes"
                                                                        {{ isset($gstdata[$q]['remit']) && $gstdata[$q]['remit'] == 'Yes' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        for="gstdata{{ $q }}_remit_yes"
                                                                        key="yes">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input danger"
                                                                        type="radio"
                                                                        name="gstdata[{{ $q }}][remit]"
                                                                        id="gstdata{{ $q }}_remit_no"
                                                                        value="No"
                                                                        {{ isset($gstdata[$q]['remit']) && $gstdata[$q]['remit'] == 'No' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        for="gstdata{{ $q }}_remit_no"
                                                                        key="no">No</label>
                                                                </div>
                                                            </td>

                                                            {{-- Filed Before Due Date --}}
                                                            <td style="text-align:center;">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input success"
                                                                        type="radio"
                                                                        name="gstdata[{{ $q }}][filed]"
                                                                        id="gstdata{{ $q }}_filed_yes"
                                                                        value="Yes"
                                                                        {{ isset($gstdata[$q]['filed']) && $gstdata[$q]['filed'] == 'Yes' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        for="gstdata{{ $q }}_filed_yes"
                                                                        key="yes">Yes</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input danger"
                                                                        type="radio"
                                                                        name="gstdata[{{ $q }}][filed]"
                                                                        id="gstdata{{ $q }}_filed_no"
                                                                        value="No"
                                                                        {{ isset($gstdata[$q]['filed']) && $gstdata[$q]['filed'] == 'No' ? 'checked' : '' }}>
                                                                    <label class="form-check-label lang"
                                                                        for="gstdata{{ $q }}_filed_no"
                                                                        key="no">No</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach





                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <h5><b><u class="lang" key = "labour_heading">d)Labour welfare
                                                    fund(LWF)</u></b></h5>
                                        <table class="entry-table table bg-white" style="width:100% !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width:5% !important;text-align:center !important;"
                                                        class="sticky-col lang" key = "s_no">S.No</th>
                                                    <th style="width:65% !important;text-align:center !important;"
                                                        class="wrap-text-lwf lang" key ="details">Details</th>
                                                    <th style="width:45% !important;text-align:center !important;"
                                                        class="wrap-text-lwf lang" key="remarks">Remarks</th>
                                                </tr>
                                            </thead>
                                            @php

                                            @endphp
                                            <tbody id="entry-table-body">
                                                {{-- Row 1 --}}
                                                <tr>
                                                    <td style="text-align:center !important;">1</td>
                                                    <td style="text-align:left !important;" class="wrap-text-lwf lang"
                                                        key = "estimate_deduct">
                                                        Whether 1% LWF on estimation deducted from vendor payments
                                                    </td>
                                                    <td style="text-align:center !important;">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input success check-light-success"
                                                                type="radio" name="lwfdata[lwf_1]" value="Yes"
                                                                {{ isset($pantanData['lwfdata']['lwf_1']) && $pantanData['lwfdata']['lwf_1'] === 'Yes' ? 'checked' : '' }}>
                                                            <label class="form-check-label lang"
                                                                key="yes">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input danger check-light-danger"
                                                                type="radio" name="lwfdata[lwf_1]" value="No"
                                                                {{ isset($pantanData['lwfdata']['lwf_1']) && $pantanData['lwfdata']['lwf_1'] === 'No' ? 'checked' : '' }}>
                                                            <label class="form-check-label lang"
                                                                key="no">No</label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Row 2 --}}
                                                <tr>
                                                    <td style="text-align:center !important;">2</td>
                                                    <td style="text-align:left !important;" class="wrap-text-lwf lang"
                                                        key="nodeduct">
                                                        If 'No' whether is shortfall in deduction (including planning
                                                        permission if any)
                                                    </td>
                                                    <td style="text-align:center !important;" class="wrap-text-lwf">
                                                        <textarea name="lwfdata[lwf_2]" id="lwf_ckeditor" rows="10" cols="10">{{ $pantanData['lwfdata']['lwf_2'] ?? '' }}</textarea>
                                                    </td>
                                                </tr>

                                                {{-- Row 3 --}}
                                                <tr>
                                                    <td style="text-align:center !important;">3</td>
                                                    <td style="text-align:left !important;" class="wrap-text-lwf lang"
                                                        key = "lwf_collect">
                                                        Whether 1% LWF collected been remitted to the TNCWWB (including
                                                        planning permission if any)
                                                    </td>
                                                    <td style="text-align:center !important;">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input success check-light-success"
                                                                type="radio" name="lwfdata[lwf_3]" value="Yes"
                                                                {{ isset($pantanData['lwfdata']['lwf_3']) && $pantanData['lwfdata']['lwf_3'] === 'Yes' ? 'checked' : '' }}>
                                                            <label class="form-check-label lang"
                                                                key="yes">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input danger check-light-danger"
                                                                type="radio" name="lwfdata[lwf_3]" value="No"
                                                                {{ isset($pantanData['lwfdata']['lwf_3']) && $pantanData['lwfdata']['lwf_3'] === 'No' ? 'checked' : '' }}>
                                                            <label class="form-check-label lang"
                                                                key="no">No</label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Row 4 --}}
                                                <tr>
                                                    <td style="text-align:center !important;">4</td>
                                                    <td style="text-align:left !important;" class="wrap-text-lwf lang"
                                                        key ="shortfall">
                                                        If 'No' what is the shortfall in remittance
                                                    </td>
                                                    <td style="text-align:center !important;">
                                                        <input type="number" name="lwfdata[lwf_4]" maxlength="10"
                                                            class="form-control"
                                                            value="{{ $pantanData['lwfdata']['lwf_4'] ?? '' }}">
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                        <h5><b><u class ="lang" key ="legal">e)Legal compliances</u></b></h5>
                                        <textarea name="legal_complaince[]" id="legal_complaince" rows="10" cols="80"></textarea>
                                        <br>
                                        <h5><b><u class="lang" key='financial_review'>f)Financial Review</u></b></h5>
                                        <textarea name="financial_review[]" id="financial_review" rows="10" cols="80"></textarea>

                                    </form>
                                </div>




                                <div class="hideotherdiv hide_this" id="auditcertificateDiv">
                                    <form id="auditcertificateForm">
                                        <div class="row">
                                            <div class="col-md-3" style="color:#2a3547!important;"><b class="lang"
                                                    key="typeofcertificate">Type of Certificate</b></div>

                                            <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                                value="<?php echo $instdel[0]['auditscheduleid']; ?>" /><br>
                                            <input type="hidden" name="instid" id="instid"
                                                value="<?php echo $instdel[0]['instid']; ?>" /><br>

                                            <div class="col-md-9">
                                                @php
                                                    $selectedType = $auditCertificate->cer_type_code ?? '01';
                                                @endphp

                                                <div style="padding:10px;" class="form-check">
                                                    <input class="form-check-input danger" type="radio"
                                                        name="cer_typecode" value="01" id="radio2"
                                                        {{ $selectedType == '01' ? 'checked' : '' }}>
                                                    <label class="form-check-label lang" for="radio2"
                                                        key="unqualified_label"
                                                        style="color:#2a3547!important;">UnQualified</label>
                                                </div>
                                                <div style="padding:10px;" class="form-check">
                                                    <input class="form-check-input success" type="radio"
                                                        name="cer_typecode" value="02" id="radio1"
                                                        {{ $selectedType == '02' ? 'checked' : '' }}>
                                                    <label class="form-check-label lang" for="radio1"
                                                        key="qualified_label"
                                                        style="color:#2a3547!important;">Qualified</label>
                                                </div>
                                                <div style="padding:10px;" class="form-check">
                                                    <input class="form-check-input warning" type="radio"
                                                        name="cer_typecode" value="03" id="radio3"
                                                        {{ $selectedType == '03' ? 'checked' : '' }}>
                                                    <label class="form-check-label lang" for="radio3"
                                                        key="adverse_label"
                                                        style="color:#2a3547!important;">Adverse</label>
                                                </div>
                                                <div style="padding:10px;" class="form-check">
                                                    <input class="form-check-input" type="radio" name="cer_typecode"
                                                        value="04" id="radio4"
                                                        {{ $selectedType == '04' ? 'checked' : '' }}>
                                                    <label class="form-check-label lang" for="radio4"
                                                        key="disclaimer_label"
                                                        style="color:#2a3547!important;">Disclaimer</label>
                                                </div>
                                            </div><br><br>
                                            <div style="border:1px solid #f4f4f4;background-color:#f4f4f4;border-radius:20px;padding:30px;"
                                                class="hideotherdiv hide_this" id="auditcertificateContentdiv"><br>
                                                <p id="cer_preload_conent"></p><br>
                                            </div>

                                            <div id="auditcertificatediv">
                                                <br><br>
                                                <label style="color:#2a3547!important;"><b
                                                        class="certificate_remarks lang" key="remarks">Remarks
                                                    </b>&nbsp;&nbsp;</label><br><span
                                                    class="certificate_remarks_span">(fdsdfasd)</span>
                                                <textarea name="auditcertificate_ckeditor" id="auditcertificate_ckeditor">{{ isset($auditCertificate->cer_remarks) ? json_decode($auditCertificate->cer_remarks)->content : '' }}</textarea>
                                            </div>
                                        </div>

                                    </form>

                                </div>

                                <br>
                                <div style="border-top:1px dashed #a3a0a0;" class="row justify-content-center hide_this"
                                    id="buttonset">
                                    <div class="col-md-6  mx-auto text-center">
                                        <input type="hidden" name="whichform" id="whichform" value="" /><br>

                                        <button class="btn button_save mt-3 lang" key="savedraft_btn" type="submit"
                                            action="insert" id="savedraft_btn" name="buttonaction">Save Draft</button>

                                        <button type="button" class="btn btn-danger mt-3 lang" key="clear_btn"
                                            id="reset_button">Clear</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- Button to load the Word file preview -->

        <div align="center" class="btn-container hide_this" id="previewBtn" style="font-size: 15px;">
            <a class="btn btn-info "> <i class="fas fa-file-pdf"></i>&nbsp;&nbsp;<span class="lang"
                    key="draftauditreport_btn">Generate Draft Audit Report</span></a>
        </div>
        <input type="hidden" id="finalize_hidden_txt" />

        <input type="hidden" name="auditscheduleid" id="auditscheduleid" value="<?php echo $instdel[0]['auditscheduleid']; ?>" />
        <input type="hidden" name="instid" id="instid" value="<?php echo $instdel[0]['instid']; ?>" />
        <div class="hide_this Finalized_parta"><b>
                <center>* Part - I has been finailzed successfully</center>
            </b></div>
        <div class="hide_this Finalized_partb_ser"><b>
                <center>* Part - II (Serious Irregularities Paras) has been finalized successfully</center>
            </b></div>
        <div class="hide_this Finalized_partb_nonser"><b>
                <center>* Part - II (NonSerious Irregularities Paras) has been finalized successfully</center>
            </b></div>
        <div class="hide_this Finalized_partc"><b>
                <center>* Part - III has been finalized successfully</center>
            </b></div>
        <div class="hide_this Finalized_partbothers"><b>
                <center>* Part - II(Others) has been finalized successfully</center>
            </b></div>
        <div id="FinalizeBtn" align="center" class="btn-container finalize_btn_div" style="font-size: 15px;">
            <button class="btn bg-primary button_finalise lang mt-3" type="submit" id="previewdraftbtn"
                action="finalise" key = "preview_part1">PREVIEW PART - I</button> <br>
        </div>
    </div><br>
    </div>
    <br><br>
    </div>


    </div>


    <div class="modal fade" id="wordPreviewModal" tabindex="-1" aria-labelledby="wordPreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div id="previewmodel_content" class="modal-content">
                <div class="modal-header">
                    <h3 style="text-align:center !important;" class="lang" key="previewscreen">PREVIEW SCREEN</h3>

                    <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- The iframe will be inserted dynamically here -->
                    <div id="pdf-preview" style="width: 100%;">
                        <!-- The iframe content will appear here -->
                    </div>
                    <br><br>
                    <input type="text" id="filename" style="display: none;" />
                    <div class="preview_finalizebtnset">
                        <div class="d-flex justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input enble-chkbox" type="checkbox" value="1"
                                    id="finalizeReport" name="finalizeReport">
                                <label id="checkboxlabel_content" class="form-check-label" for="finalizeReport">
                                    send intimation to the institution and finalize the report?
                                </label>
                            </div>
                        </div>


                        <!-- Button container with flexbox for centering -->
                        <div class="text-center mt-3" style="margin-t">
                            <button id="finalizereport_pdf" class="btn btn-success">
                                Finalize Report</span>
                            </button>
                        </div>
                    </div>
                    <!--<div class="text-center mt-3" style="margin-t">
                                                            <button id="downloadBtn" class="btn btn-info" style="display: none;">
                                                                <i class="fas fa-download"></i>&nbsp;&nbsp;<span class="lang" key="downloadreport">Download Report</span>
                                                            </button>
                                                        </div>-->
                    <br><br>

                </div>

            </div>
        </div>
    </div>



    <script src="../assets/libs/dragula/dist/dragula.min.js"></script>
    <!-- solar icons -->
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/nestable/jquery.nestable.js"></script>
    <script src="../assets/js/plugins/nestable-init.js"></script>

    <!--<script src="../common/ckeditor_offline.js"></script>-->

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script>

         function showLoader() {
            document.getElementById('ajax-loader').style.display = 'flex';
        }

        function hideLoader() {
            document.getElementById('ajax-loader').style.display = 'none';
        }

 function onchange_availability(value) {

                const selectedValue = value || document.querySelector('input[name="availability"]:checked').value;

                if (selectedValue == 'Y') {
                    //$('#account_table').css("visibility", "visible");
                    $('#account_table').show();
                } else {


                    const table = document.getElementById('entries-container');
                    const inputs = table.querySelectorAll('input, select, textarea');
                    const tbody = table.querySelector('tbody');
                    const rows = tbody.querySelectorAll('tr');

                    // âœ… Remove all rows except the first
                    rows.forEach((row, index) => {
                        if (index > 0) row.remove();
                    });
                    inputs.forEach(el => {
                        switch (el.type) {
                            case 'checkbox':
                            case 'radio':
                                el.checked = false;
                                break;
                            case 'select-one':
                                el.selectedIndex = 0;
                                break;
                            default:
                                el.value = '';
                        }
                    });



                    $('#account_table').hide();
                }

            }
        var fetchSerAuditSlips = @json($FetchAuditslips);

        if (Array.isArray(fetchSerAuditSlips) && fetchSerAuditSlips.length === 0) {

            $('#part_b_serstep').hide();
        }


        var fetchNonAuditSlips = @json($FetchAuditslips_NonIrrReg);

        if (Array.isArray(fetchNonAuditSlips) && fetchNonAuditSlips.length === 0) {

            $('#part_b_nonserstep').hide();
        }


        $('#previewdraftbtn').on('click', function() {

            var myModal = new bootstrap.Modal(document.getElementById('wordPreviewModal'));

            var lang = getLanguage('Y');

            var financialyearcode = '<?php echo $financialyearcode; ?>';
            var instid = '<?php echo $instid; ?>';
            var spilloverflag = '<?php echo $spilloverflag; ?>';


            const currentStep = $('.step-buttons .btn.active-step').data('step');
            //alert(currentStep);
            var whichpart = currentStep;
showLoader();
            // Make an AJAX request to get the Word file content as HTML
            fetch('/preview-word?scheduleid=' + scheduleId + '&lang=' + lang + '&whichpart=' + whichpart +
                    '&instid=' + instid + '&financialyearcode=' + financialyearcode + '&spilloverflag=' +
                    spilloverflag)
                .then(response => response.json())
                .then(data => {
                    if (data.res == 'success') {
                        hideLoader();
                        // Create an iframe element dynamically
                        myModal.show();

                        var finalize_hidden_txt = $('#finalize_hidden_txt').val();

                        if (finalize_hidden_txt == 'part_a') {
                            var finalizetext = 'PART - I';
                        } else if (finalize_hidden_txt == 'part_b_seriousirregularities') {
                            var finalizetext = 'PART - II (Serious Irregularities Paras)';
                        } else if (finalize_hidden_txt == 'part_c') {
                            var finalizetext = 'PART - III';
                        } else if (finalize_hidden_txt == 'part_b_others') {
                            var finalizetext = 'PART - II (Others)';
                        } else {
                            var finalizetext = 'PART - II (Non Serious Irregularities Paras)';

                        }
                        $('#finalizeReport').prop('checked', false);

                        $('.preview_finalizebtnset').show();
                        $('#pdf-preview').removeClass('full-height');
                        $('#checkboxlabel_content').html('Finalize ' + finalizetext +
                            ' and move on to the next step');
                        $('#finalizereport_pdf').html('FINALIZE ' + finalizetext);


                        var iframe = document.createElement('iframe');
                        iframe.srcdoc = data.html; // Use the HTML content as the iframe's srcdoc
                        document.getElementById('pdf-preview').innerHTML = ''; // Clear any previous iframe
                        document.getElementById('pdf-preview').appendChild(
                            iframe); // Add the iframe to the modal

                        // Show the Download button
                        // document.getElementById('downloadBtn').style.display = 'inline-block';

                        var filename = data.filename;
                        $('#filename').val(filename);
                        localStorage.setItem('filename', filename);

                    } else {
                        hideLoader();
                        alert('No Report Available.');

                    }
                })
                .catch(error => {
                    hideLoader();
                    alert('Error: ' + error.message);
                });


        });


        showStep('part_a');

        const scheduleId = '<?php echo $auditscheduleid; ?>';



        /**Part_A, Part B show step forms */
        function showStep(step) {



            // Hide all steps
            document.querySelectorAll('.step-container').forEach(stepContainer => {
                stepContainer.style.display = 'none';
            });

            // Show the selected step (if it exists)
            const selectedStep = document.getElementById(step);
            // console.log(selectedStep);
            if (selectedStep) {
                selectedStep.style.display = 'block';
            }

            // Remove active-step class from all
            $('.step-buttons .btn').removeClass('active-step');

            // Add to the clicked one
            $(`.step-buttons .btn[data-step="${step}"]`).addClass('active-step')

            if (step == 'part_a') {
                $('#previewdraftbtn').text('PREVIEW PART - I');
                $('#finalize_hidden_txt').val('part_a');
                //alert();
                getStatusFlags(function(statusFlags) {
                    buttonshow_Preview(
                        statusFlags.auditcertificate,
                        statusFlags.authorityofaudit,
                        statusFlags.genesisofaudit,
                        statusFlags.accountdetails,
                        statusFlags.pantan,
                        statusFlags.serious_storeslip,
                        statusFlags.nonserious_storeslip,
                        statusFlags.annextures,
                        statusFlags.levystatus,
                        statusFlags.pendingpara,
                        statusFlags.seriouscountdata,
                        statusFlags.nonseriouscountdata
                    );
                });

                AuditCertificate();
            } else if (step == 'part_b_others') {
                $('#previewdraftbtn').text('PREVIEW PART - II (others)');
                $('#finalize_hidden_txt').val('part_b_others');
                getStatusFlags(function(statusFlags) {
                    buttonshow_Preview(
                        statusFlags.auditcertificate,
                        statusFlags.authorityofaudit,
                        statusFlags.genesisofaudit,
                        statusFlags.accountdetails,
                        statusFlags.pantan,
                        statusFlags.serious_storeslip,
                        statusFlags.nonserious_storeslip,
                        statusFlags.annextures,
                        statusFlags.levystatus,
                        statusFlags.pendingpara,
                        statusFlags.seriouscountdata,
                        statusFlags.nonseriouscountdata
                    );
                });

                AuditLevyCertificate();
            } else if (step == 'part_c') {
                $('#previewdraftbtn').show();
                $('#previewdraftbtn').text('PREVIEW PART - III');
                $('#finalize_hidden_txt').val('part_c');
                Annexures();
                getStatusFlags(function(statusFlags) {
                    buttonshow_Preview(
                        statusFlags.auditcertificate,
                        statusFlags.authorityofaudit,
                        statusFlags.genesisofaudit,
                        statusFlags.accountdetails,
                        statusFlags.pantan,
                        statusFlags.serious_storeslip,
                        statusFlags.nonserious_storeslip,
                        statusFlags.annextures,
                        statusFlags.levystatus,
                        statusFlags.pendingpara,
                        statusFlags.seriouscountdata,
                        statusFlags.nonseriouscountdata

                    );
                });

            } else {

                $('#finalisebtn').show();
                if (step == 'part_b_seriousirregularities') {
                    var finalizetext = 'PREVIEW  PART - II (Serious Irregularities Paras)';
                    var slipnohidden = $('#hidden_Slipno_ser').val();
                    // alert(slipnohidden);
                    showStepChild(step, 'auditslips', lang, slipnohidden);

                } else {
                    var finalizetext = 'PREVIEW PART - II (Non Serious Irregularities Paras)';
                    var slipnohidden = $('#hidden_Slipno_nonser').val();
                    // alert(slipnohidden);
                    showStepChild(step, 'auditslips', lang, slipnohidden);

                }

                $('#previewdraftbtn').text(finalizetext);
                $('#finalize_hidden_txt').val(step);
                getStatusFlags(function(statusFlags) {
                    buttonshow_Preview(
                        statusFlags.auditcertificate,
                        statusFlags.authorityofaudit,
                        statusFlags.genesisofaudit,
                        statusFlags.accountdetails,
                        statusFlags.pantan,
                        statusFlags.serious_storeslip,
                        statusFlags.nonserious_storeslip,
                        statusFlags.annextures,
                        statusFlags.levystatus,
                        statusFlags.pendingpara,
                        statusFlags.seriouscountdata,
                        statusFlags.nonseriouscountdata

                    );
                });

            }

            $('.sub-step-header').hide();

            // Remove 'active' class from all buttons
            document.querySelectorAll('.btn-outline-primary').forEach(button => {
                button.classList.remove('btn-primary'); // Remove Bootstrap primary styling
                button.classList.remove('active'); // Remove custom active styling
            });

            // Add 'active' class to the clicked button
            const activeButton = document.querySelector(`.btn-outline-primary[data-step="${step}"]`);
            if (activeButton) {
                activeButton.classList.add('btn-primary'); // Change to Bootstrap primary button
                activeButton.classList.add('active'); // Custom class for additional styling
            }

        }


        function buttonshow_Preview(
            auditCertificate, authorityOfAudit, genesisOfAudit, accountDetails, pantan,
            seriousSlip = '', nonSeriousSlip = '', annextures = '', levysts = '', pendingpara = '', seriouscountdata = '',
            nonseriouscountdata = ''
        ) {


            // console.log(nonseriouscountdata);

            const currentStep = $('.step-buttons .btn.active-step').data('step');
            const finalizedFlags = {
                part_a: [auditCertificate, authorityOfAudit, genesisOfAudit, accountDetails, pantan],
                part_b_seriousirregularities: [seriousSlip, seriouscountdata],
                part_b_nonseriousirregularities: [nonSeriousSlip, nonseriouscountdata],
                part_b_others: [levysts, pendingpara],
                part_c: [annextures]
            };

            const isAllF = (flags) => flags.every(f => f === 'F');
            const isAllY = (flags) => flags.every(f => f === 'Y');

            // Reset
            $('#previewBtn, #FinalizeBtn, #saveannextures, #buttonset').hide();
            $('.Finalized_parta, .Finalized_partb_ser, .Finalized_partb_nonser, .Finalized_partbothers, .Finalized_partc')
                .hide();
            $('.annexturebtnset').hide();

            let draguladisable = false;
            let slipckeditordisable = false;

            // console.log((isAllY(finalizedFlags.part_a)));
            // Step Logic
            switch (currentStep) {
                case 'part_a':
                    if (isAllY(finalizedFlags.part_a)) {
                        $('#FinalizeBtn, #buttonset').show();
                        updateStepStatusUI();
                    } else if (isAllF(finalizedFlags.part_a)) {
                        $('.Finalized_parta').show();
                        $('#pantan_form').find('input').attr('disabled', true);
                    } else {
                        $('#buttonset').show();
                        updateStepStatusUI();
                    }
                    break;

                case 'part_b_seriousirregularities':
                    //alert(seriouscountdata); alert(seriousSlip);
                    // Show FinalizeBtn only if seriousSlip is 'Y' and the seriouscountdata flag is true
                    if (seriousSlip === 'Y') {


                        $('#FinalizeBtn').show();

                    }

                    // Always show the button set if seriousSlip is 'Y'
                    if (seriousSlip === 'Y') {
                        $('#buttonset').show();
                    } else if (seriousSlip === 'F') {
                        $('.Finalized_partb_ser').show(); // optional: show() if finalized view needed
                        $('#buttonset').hide();
                        draguladisable = true;
                        slipckeditordisable = true;
                    } else {
                        $('#buttonset').show();
                    }

                    break;


                case 'part_b_nonseriousirregularities':
                    // Show FinalizeBtn only if seriousSlip is 'Y' and the seriouscountdata flag is true
                    if (nonSeriousSlip === 'Y') {
                        // location.reload();

                        $('#FinalizeBtn').show();
                        //  break;

                    }

                    // Always show the button set if seriousSlip is 'Y'
                    if (nonSeriousSlip === 'Y') {
                        $('#buttonset').show();
                    } else if (nonSeriousSlip === 'F') {
                        $('.Finalized_partb_nonser').show(); // optional: show() if finalized view needed
                        $('#buttonset').hide();
                        draguladisable = true;
                        slipckeditordisable = true;
                    } else {
                        $('#buttonset').show();
                    }

                    break;

                    // case 'part_b_nonseriousirregularities':
                    //     if (nonSeriousSlip === 'Y') $('#FinalizeBtn, #buttonset').show();
                    //     else if (nonSeriousSlip === 'F') {
                    //         $('.Finalized_partb_nonser').show();
                    //         draguladisable = slipckeditordisable = true;
                    //     } else $('#buttonset').show();
                    //     break;

                case 'part_b_others':
                    if (isAllY(finalizedFlags.part_b_others)) $('#FinalizeBtn, #buttonset').show();
                    else if (isAllF(finalizedFlags.part_b_others)) $('.Finalized_partbothers').show();
                    else $('#buttonset').show();
                    break;

                case 'part_c':
                    if (annextures === 'Y') {

                        $('#FinalizeBtn, #saveannextures').show();
                        $('.annexturebtnset').show();
                    } else if (annextures === 'F') {
                        $('.Finalized_partc').show();
                    } else {
                        $('#saveannextures').show();
                        $('.annexturebtnset').show();
                    }
                    break;
            }


            var fetchSerAuditSlips = @json($FetchAuditslips);

            if (Array.isArray(fetchSerAuditSlips) && fetchSerAuditSlips.length === 0) {

                seriousSlip = 'F';
            }


            var fetchNonAuditSlips = @json($FetchAuditslips_NonIrrReg);

            if (Array.isArray(fetchNonAuditSlips) && fetchNonAuditSlips.length === 0) {

                nonSeriousSlip = 'F';
            }


            // Handle Preview button (if everything finalized)
            if (
                isAllF(finalizedFlags.part_a) &&
                seriousSlip === 'F' &&
                nonSeriousSlip === 'F' &&
                annextures === 'F' &&
                levysts === 'F' &&
                pendingpara === 'F'
            ) {
                $('#previewBtn').show();
                $('#FinalizeBtn, #buttonset').hide();
                $('#pantan_form').find('input').attr('disabled', true);
                $('.Finalized_parta, .Finalized_partb_ser, .Finalized_partb_nonser, .Finalized_partbothers, .Finalized_partc')
                    .hide();

                if (['part_b_seriousirregularities', 'part_b_nonseriousirregularities'].includes(currentStep)) {
                    draguladisable = slipckeditordisable = true;
                }
            }

            // Disable dragula if needed
            if (draguladisable && typeof drake !== 'undefined') {
                drake.destroy();
                drake = null;
                $('.part_b_dragula .draggable-item').css('cursor', 'default');
            }

            // Disable slip editor
            if (slipckeditordisable) {
                const slipNo = (currentStep === 'part_b_seriousirregularities') ?
                    $('#hidden_Slipno_ser').val() :
                    $('#hidden_Slipno_nonser').val();
                const typeCode = (currentStep === 'part_b_seriousirregularities') ? '01' : '02';
                showStepChild(currentStep, 'auditslips', lang, slipNo, true, typeCode);
            }
        }


        /**Insert,Finalize Data for Part-A, Part-B End*/

        $(document).ready(function() {
            getStatusFlags(function(statusflags) {
                // ✅ Only check if pdfreport is 'F'
                if (statusflags.pdfreport === 'F') {
                    $('.preview_finalizebtnset').hide();
                    $('#pdf-preview').addClass('full-height');

                } else {
                    $('.preview_finalizebtnset').show();
                    $('#pdf-preview').removeClass('full-height');

                }
            });
        });


        function getStatusFlags(callback) {
            showLoader();
            var auditscheduleid = $('#auditscheduleid').val();

            $.ajax({
                url: '/getstatusflagsfromdb',
                method: 'POST',
                data: {
                    'auditscheduleid': auditscheduleid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                success: function(response) {
                    hideLoader();
                    var statusflags = response.statusflags;

                    // Store in localStorage
                    localStorage.setItem('statusFlags', JSON.stringify(statusflags));

                    // ✅ Pass data to the callback
                    callback(statusflags);
                },
                error: function(xhr, status, error) {
                     hideLoader();
                    console.error('Error:', error);
                    passing_alert_value('Confirmation', 'Data not Saved', 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                }
            });
        }


        /**Part-B Load Steps for slips */
        function showStepChild(parentstep, childstep, lang = '', slipno = '', disable = false, sernon = '') {
            $('.downloadbtndiv').hide();

            //  alert('#partb_auditslip'+slipno+' a');

            if (parentstep == 'part_b_seriousirregularities' || parentstep == 'part_b_nonseriousirregularities') {

                $('.draggable-item a').removeClass("activatestep");
                $('#partb_auditslip' + slipno + ' a').addClass("activatestep");

                var stepheading = $('.step-buttons .partb_tit').html();

            }
            $('#whichform').val('slipform');


            if (lang == '') {
                var lang = getLanguage('Y');

            }

            if (!slipno) {
                $('.hideotherdiv').hide();
                alert('No Slip found for this tab. Move on to the next tab');
                return;
                // $('#slip_details_div').show();
            }
            //alert(stepheading);

            // Make an AJAX request to get the Word file content as HTML
            fetch('/single-slip-details?scheduleid=' + scheduleId + '&stepform=' + childstep + '&slipno=' + slipno +
                    '&lang=' + lang)
                .then(response => response.json())
                .then(data => {

                    var slipdetails = data.slipdata;

                    $('#slipdetails_data').val(slipdetails['slipdetails']);


                    let remarksObj = JSON.parse(slipdetails['remarks']);
                    let htmlContent = remarksObj.content;

                    $('.hideotherdiv').hide();

                    $('#slip_details_div').show();
                    var slipno = slipdetails['mainslipnumber'];

                    if (lang === 'ta') {
                        $('.step-header').html('ஸ்லிப் விவரங்கள் - ' + slipno); // Tamil version
                        $('.sub-step-header').html('(' + slipdetails['objectiontname'] + ')');

                    } else {
                        $('.step-header').html('PARA DETAILS OF ' + slipno); // English version
                        $('.sub-step-header').html('(' + slipdetails['objectionename'] + ')');

                    }

                    $('.sub-step-header').show();
                    $('#files_heading').hide();
                    var slipparano = $('.slipparano_' + slipno + '').val();
                    var orderno = $('.orderno_' + slipno + '').val();


                    //    alert(slipparano);
                    $('#parnohidden').val(slipparano);
                    $('#ordernohidden').val(orderno);
                    $('.step-header').html();

                    var statusflag_slip = '';
                    if (sernon == '01') {
                        statusflag_slip = data.ser_statusflag_storeslip;

                    } else if (sernon == '02') {
                        statusflag_slip = data.nonser_statusflag_storeslip;

                    }

                    loadckeditor(htmlContent, 'slip_ckeditor', false);
                    /*if(disable || statusflag_slip == 'F')
                    {
                        alert('if condition');
                       loadckeditor(htmlContent,'slip_ckeditor',true);
                       $('#buttonset').hide();

                    }else
                    {
                        alert('elseif condition');
                         loadckeditor(htmlContent,'slip_ckeditor',false);
                          $('#buttonset').show();
                    }*/
                    var filesuploaded = data.fileuploads;
                    renderFiles(filesuploaded);


                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });

        }


        function toggleRemarks(typecode = null, remarks = '') {
            var selected = typecode || $('input[name="cer_typecode"]:checked').val();
            var certificateRemarks = JSON.parse('{!! addslashes(json_encode($Master_Auditcertificate)) !!}');

            if (selected) {
                var selectedElement = $('input[name="cer_typecode"][value="' + selected + '"]');
                var labelHtml = selectedElement.next('label')
                    .text(); // Using .next() to find the label after the radio button
            }


            $('#auditcertificateContentdiv').show();
            const lang = localStorage.getItem('lang') || 'en';

            if (selected === '01') {
                $('#auditcertificatediv').hide();
            } else {
                $('#auditcertificatediv').show();
                // Set main remarks heading
                const labelText = (lang === 'ta') ? 'கருத்துகள்' : 'Remarks';
                $('.certificate_remarks').text(labelHtml + ' ' + labelText);
                // $('.certificate_remarks').text(labelHtml +' Remarks');
                if (selected === '02') {
                    $('.certificate_remarks_span').text('( Add remarks for Basis for qualified opinion )');
                } else if (selected === '03') {
                    $('.certificate_remarks_span').text('( Add remarks for Material and pervasive statements )');
                } else if (selected === '04') {
                    $('.certificate_remarks_span').text('( Add remarks for Material and pervasive insufficiencies )');
                }
            }
            var ckremarks = '';
            if (typecode) {
                ckremarks = remarks;

            }
            loadckeditor(ckremarks, 'auditcertificate_ckeditor');


            // Find and display the certificate content
            var matched = certificateRemarks.find(cert => cert.cer_type_code === selected);
            if (matched) {
                let parsedContent = '';
                try {
                    const json = JSON.parse(matched.cer_content);
                    parsedContent = json.content;
                } catch (e) {
                    parsedContent = matched.cer_content; // fallback if not JSON
                }
                $('#cer_preload_conent').html(parsedContent); // Use your textarea ID
            } else {
                $('#cer_preload_conent').val('');
            }
        }





        // Call on radio button change
        $('input[name="cer_typecode"]').change(function() {

            toggleRemarks();
        });


        function loadckeditor(dynvalue, textareaId, disable = false) {
            let viewslip_auditorremarks;

            // Destroy the existing CKEditor instance if it exists
            if (window[textareaId] && typeof window[textareaId].destroy === 'function') {
                window[textareaId].destroy();
            }

            const editorElement = document.getElementById(textareaId);
            if (editorElement) {
                CKEDITOR.ClassicEditor.create(editorElement, {
                    toolbar: {
                        items: [
                            'findAndReplace', 'selectAll', '|',
                            'heading', '|',
                            'bold', 'italic', 'underline', '|',
                            'numberedList', '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            'fontSize', 'fontFamily', '|',
                            'alignment', '|',
                            'uploadImage', 'insertTable', '|',
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    placeholder: 'Welcome to CAMS... ',

                    fontFamily: {
                        options: [
                            'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                        ],
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    htmlSupport: {
                        allow: [{
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }]
                    },
                    link: {
                        decorators: {
                            addTargetToExternalLinks: true,
                            defaultProtocol: 'https://',
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    removePlugins: [
                        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                        'MultiLevelList',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                        'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                        'SlashCommand', 'Template',
                        'DocumentOutline', 'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                        'CaseChange'
                    ]
                }).then(editor => {
                    viewslip_auditorremarks = editor;
                    window[textareaId] = editor; // Store the instance globally with a unique key
                    editor.setData(dynvalue); // Set data (empty if dynvalue is empty)
                    //window[textareaId].enableReadOnlyMode('initial');
                    if (disable) {
                        editor.enableReadOnlyMode(`readOnly_${textareaId}`);
                    }
                }).catch(error => {
                    console.error("CKEditor Initialization Error:", error);
                });
            } else {
                console.error("Editor element not found.");
            }
        }

        // Function to enable/disable steps based on statusFlags
        function updateStepStatusUI() {
            getStatusFlags(function(statusFlags) {
                const steps = $('[data-step-id][data-step-index]');
                const sortedSteps = steps.sort((a, b) =>
                    parseInt($(a).data('step-index')) - parseInt($(b).data('step-index'))
                );

                let allowNext = true;

                sortedSteps.each(function() {
                    const $step = $(this);
                    const stepId = $step.data('step-id');
                    const status = statusFlags[stepId]; // 'Y' or 'N'

                    if (allowNext) {
                        $step.removeClass('disabled-step').css('pointer-events', 'auto');
                    } else {
                        $step.addClass('disabled-step').css('pointer-events', 'none');
                    }

                    // If current step is completed, allow next
                    if (status === 'Y') {
                        allowNext = true;
                    } else {
                        allowNext = false;
                    }
                });
            });
        }



        function goToNextStep() {

            const activeStep = $('.step-container .text-decoration-none.activatestep');

            if (activeStep.length === 0) return;

            const currentStepId = activeStep.data('step-id'); // e.g., 'auditcertificate'
            // Based on the current step key, decide what to do and where to go next
            if (currentStepId === 'auditcertificate') {
                AuthorityofAudit('authorityofaudit');
            } else if (currentStepId === 'authorityofaudit') {
                InstituteGenesis('institutedetailsentry')
            } else if (currentStepId === 'genesisofaudit') {
                AccountGeneral('accountdet');
            } else if (currentStepId === 'accountdetails') {
                PANTAN('pan_tan');
            }
        }


        function insert_reportData(action) {
            var whichtypeofform = $('#whichform').val();
            var formData = [];

            // Function to validate CKEditor content length (e.g., min 20 characters)
            function validateEditorContent(editorName, minLength = 20) {
                const editor = window[editorName];
                const editorContent = editor ? editor.getData().trim() : '';

                // Check if the CKEditor content is empty
                if (editorContent === '') {
                    passing_alert_value('Confirmation', 'Remarks field is required', 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                    return false; // Invalid
                }

                // Check if the content length is less than the required minimum length
                if (editorContent.length < minLength) {
                    passing_alert_value('Confirmation', `Please enter valid remarks`, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                    return false; // Invalid
                }

                return true; // Valid
            }

            function validateGSTReturn(formSelector) {
                let isValid = true;

                // Validate Financial Year
                const auditYear = $('#audit_year').val().trim();
                if (auditYear === '') {
                    $('#audit_year').addClass('is-invalid').focus();
                    passing_alert_value('Confirmation', 'Please select the Financial Year', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return false;
                } else {
                    $('#audit_year').removeClass('is-invalid');
                }

                // ✅ TDS Section Validation - only if "Available" is selected
                const tdsStatus = $('input[name="tds_avail"]:checked').val();
                if (tdsStatus === '01') { // 01 = Available
                    $('#tdsfilled_table tbody tr').each(function() {
                        const row = $(this);

                        // Validate year, status, and period
                        /* const year = row.find('select[name*="[year]"]').val();
                         const status = row.find('select[name*="[status]"]').val();
                         const period = row.find('select[name*="[period]"]').val();

                         if (!year || !status || !period) {
                             passing_alert_value('Confirmation', 'Please fill all TDS fields (Year, Status, Period)',
                                 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                             row.find('select').addClass('border border-danger');
                             isValid = false;
                             return false; // break out of loop
                         } else {
                             row.find('select').removeClass('border border-danger');
                         }*/

                        // Validate remittance and return radio buttons
                        const remitChecked = row.find('input[name*="[remit]"]:checked').length > 0;
                        const filedChecked = row.find('input[name*="[filed]"]:checked').length > 0;

                        alert(remitChecked);
                        alert(filedChecked);

                        if (!remitChecked || !filedChecked) {
                            passing_alert_value('Confirmation',
                                'Please select Yes/No for TDS remittance and return filing.',
                                'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                            isValid = false;
                            return false;
                        }
                    });
                }

                // ✅ General radio group validation (GST and others)
                const seen = {};
                $(formSelector + ' input[type="radio"]').each(function() {
                    const name = $(this).attr('name');

                    // Skip TDS group (already validated above) and duplicates
                    if (name.startsWith('tdsdata') || seen[name]) return;

                    seen[name] = true;

                    if ($('input[name="' + name + '"]:checked').length === 0) {
                        passing_alert_value('Confirmation', 'Please select Yes or No for: ' + name,
                            'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        $(this).closest('.form-check').addClass('border border-danger');
                        isValid = false;
                        return false; // stop each()
                    } else {
                        $(this).closest('.form-check').removeClass('border border-danger');
                    }
                });

                return isValid;
            }


            function validatePanTanForm(formSelector) {
                let isValid = true;

                // Clear previous error messages
                $('#panError, #tanError, #gstError').text('');

                const pan = $('#panNumber').val().trim();
                const tan = $('#tanNumber').val().trim();
                const gst = $('#gstNumber').val().trim();

                // PAN validation
                if (pan === '') {
                    passing_alert_value('Confirmation', 'PAN Number is required', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return false;
                } else if (!/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i.test(pan)) {
                    $('#panError').text('Invalid PAN format.');
                    return false;
                }

                // TAN validation
                if (tan === '') {
                    passing_alert_value('Confirmation', 'TAN Number is required', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return false;
                } else if (!/^[A-Z]{4}[0-9]{5}[A-Z]{1}$/i.test(tan)) {
                    $('#tanError').text('Invalid TAN format.');
                    return false;
                }

                // GST validation
                if (gst === '') {
                    passing_alert_value('Confirmation', 'GST Number is required', 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    return false;
                } else if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/i.test(gst)) {
                    $('#gstError').text('Invalid GST format.');
                    return false;
                }

                // Radio group validation
                const requiredRadioFields = [
                    'status_24q',
                    'status_27eq',
                    'status_26q',
                    'it_exemption',
                    'status_return'
                ];

                for (const field of requiredRadioFields) {
                    if ($(`input[name="${field}"]:checked`).length === 0) {
                        passing_alert_value('Confirmation', 'Please select filing status', 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                        return false;
                    }
                }

                return isValid;
            }



            function validateAccountDetailsForm(formId) {
                let isValid = true;
                let errorMessage = "";

                // Clear any existing error highlights
                $(`${formId} input`).removeClass('is-invalid');

                // Loop through each row
                $(`${formId} tbody tr`).each(function(index, row) {
                    const $row = $(row);

                    const bankNameInput = $row.find('input[name^="account_details"]');
                    const accountNumberInput = $row.find('input[name^="bank_account_number"]');

                    const bankName = bankNameInput.val().trim();
                    const bankAccountNumber = accountNumberInput.val().trim();

                    const accountType = $row.find('select[name^="account_type"]');
                    const accountypevalue = accountType.val().trim();

                    if (accountypevalue === "") {
                        accountType.addClass('is-invalid');
                        errorMessage = "Account type cannot be empty";
                        isValid = false;
                    }


                    if (bankName === "") {
                        bankNameInput.addClass('is-invalid');
                        errorMessage = "Bank Name cannot be empty.";
                        isValid = false;
                    }

                    if (bankAccountNumber === "") {
                        accountNumberInput.addClass('is-invalid');
                        errorMessage = "Bank Account Number cannot be empty.";
                        isValid = false;
                    }
                });

                if (!isValid) {
                    getLabels_jsonlayout([{

                        key: 'bankdetails'
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                    // passing_alert_value('Confirmation', errorMessage, 'confirmation_alert',
                    //     'alert_header', 'alert_body',
                    //     'confirmation_alert');
                }

                return isValid;
            }



            // Function to validate form data
            function validateForm(formId) {
                // Validate if the form is empty
                var form = $(formId);
                if (form.find('input, select, textarea').filter(function() {
                        return !this.value;
                    }).length > 0) {
                    passing_alert_value('Confirmation', `All fields must be filled!`, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                    return false;
                }
                return true;
            }

            if (whichtypeofform == 'audit_certificate') {
                var formData = $('#auditcertificateForm').serializeArray();

                var cer_typecode = $('input[name="cer_typecode"]:checked').val(); // Get cer_typecode value
                if (cer_typecode != '01') {
                    if (!validateEditorContent('auditcertificate_ckeditor')) return; // Validate CKEditor
                }

                formData.push({
                    name: 'cer_remarks',
                    value: window['auditcertificate_ckeditor']?.getData() || ''
                });

                const remarks = window['auditcertificate_ckeditor']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const auditCertData = {
                    cer_remarks: remarks,
                    typecode: cer_typecode || '',
                };

                localStorage.setItem('audit_certificate_data', JSON.stringify(auditCertData));

            } else if (whichtypeofform == 'authorityofaudit') {
                var formData = $('#authorityofauditForm').serializeArray();
                //if (!validateEditorContent('auditofauthority_ckeditor')) return; // Validate CKEditor

                formData.push({
                    name: 'authorityofaudit',
                    value: window['auditofauthority_ckeditor']?.getData() || ''
                });

                const remarks = window['auditofauthority_ckeditor']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const AuthorityofauditData = {
                    authorityofaudit_remarks: remarks
                };

                localStorage.setItem('authorityofaudit_data', JSON.stringify(AuthorityofauditData));

            } else if (whichtypeofform == 'accountdet') {
                var formData = $('#accountdetails_form').serializeArray();

                const selectedValue = document.querySelector('input[name="availability"]:checked').value;
                if (selectedValue == 'Y') {
                    if (!validateAccountDetailsForm('#accountdetails_form')) return;

                }
            } else if (whichtypeofform == 'institutedetailsentry') {
                var formData = $('#Institute_Genesis_Form').serializeArray();
                if (!validateEditorContent('genesis_ckeditor')) return; // Validate CKEditor

                formData.push({
                    name: 'genesis_ckeditor',
                    value: window['genesis_ckeditor']?.getData() || ''
                });

                const remarks = window['genesis_ckeditor']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const genesisdata = {
                    genesis_remarks: remarks
                };

                localStorage.setItem('genesis_data', JSON.stringify(genesisdata));

            } else if (whichtypeofform == 'pan_tan') {
                var formData = $('#pantan_form').serializeArray();
                // if (!validateGSTReturn('#pantan_form')) return;

                formData.push({
                    name: 'itfiling_issue',
                    value: window['itfiling_issue']?.getData() || ''
                });

                const itfilingremarks = window['itfiling_issue']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const itfilingdata = {
                    itfiling_remarks: itfilingremarks
                };

                localStorage.setItem('itfiling_data', JSON.stringify(itfilingdata));

                formData.push({
                    name: 'legal_complaince',
                    value: window['legal_complaince']?.getData() || ''
                });

                const legalcomplainceremarks = window['itfiling_issue']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const legalcomplaince = {
                    legalcomplaince_remaks: legalcomplainceremarks
                };

                localStorage.setItem('legalcomplaince_data', JSON.stringify(legalcomplaince));

                formData.push({
                    name: 'financial_review',
                    value: window['financial_review']?.getData() || ''
                });


                const financialreview = window['financial_review']?.getData() || '';


                // ✅ Convert to JSON object for localStorage
                const financialreviewdata = {
                    financialreview_remarks: financialreview
                };

                localStorage.setItem('financialreview_remarks', JSON.stringify(financialreviewdata));



            } else if (whichtypeofform == 'slipform') {
                var formData = $('#slipform').serializeArray();
                if (!validateEditorContent('slip_ckeditor')) return; // Validate CKEditor
                var currentStep = $('.step-buttons .btn.active-step').data('step');

                let irregularitycode;

                if (currentStep == 'part_b_seriousirregularities') {
                    irregularitycode = '01';

                } else {
                    irregularitycode = '02';
                }

                formData.push({
                    name: 'irregularitycode',
                    value: irregularitycode
                });

                formData.push({
                    name: 'slip_ckeditor',
                    value: window['slip_ckeditor']?.getData() || ''
                });
            } else if (whichtypeofform == 'audit_levy_form') {
                var formData = $('#Audit_Levy_Form').serializeArray();
                if (!validateEditorContent('levycertificate_ckeditor')) return; // Validate CKEditor

                formData.push({
                    name: 'levycertificate_ckeditor',
                    value: window['levycertificate_ckeditor']?.getData() || ''
                });

                const remarks = window['levycertificate_ckeditor']?.getData() || '';

                // ✅ Convert to JSON object for localStorage
                const levycertificatedata = {
                    levy_remarks: remarks
                };

                localStorage.setItem('levy_data', JSON.stringify(levycertificatedata));

            } else if (whichtypeofform == 'pending_para_form') {
                var formData = $('#Pending_Paras_Form').serializeArray();
                if (!validateEditorContent('pendingparadet_ckeditor')) return; // Validate CKEditor

                formData.push({
                    name: 'pendingparadet_ckeditor',
                    value: window['pendingparadet_ckeditor']?.getData() || ''
                });

                const remarks = window['pendingparadet_ckeditor']?.getData() || '';

                // ✅ Convert to JSON object for localStorage
                const pendingparadata = {
                    pendingpara_remarks: remarks
                };

                localStorage.setItem('pending_para_data', JSON.stringify(pendingparadata));

            }

            console.log(formData);

            // ✅ Push the form type as an additional field
            formData.push({
                name: 'whichtypeofform',
                value: whichtypeofform
            });

            if (action === 'finalise') {
                formData.push({
                    name: 'finaliseflag',
                    value: 'F'
                });
            } else if (action === 'savedraft') {
                formData.push({
                    name: 'finaliseflag',
                    value: 'Y'
                });
            }
            showLoader();
            $.ajax({
               url: '/Report_Prefil',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                success: function(response) {
                    hideLoader();
                    if (action == 'savedraft') {
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] ||
                                "Error Occured";
                            passing_alert_value('Confirmation', alertMessage,
                                'confirmation_alert', 'alert_header',
                                'alert_body', 'confirmation_alert');
                        });
                        // passing_alert_value('Confirmation', 'Data Saved successfully', 'confirmation_alert',
                        //     'alert_header', 'alert_body',
                        //     'confirmation_alert');
                        var statusflags = response.statusflags;
                        // ✅ Store it in localStorage
                        localStorage.setItem('statusFlags', JSON.stringify(statusflags));
                        buttonshow_Preview(statusflags.auditcertificate, statusflags.authorityofaudit,
                            statusflags.genesisofaudit, statusflags.accountdetails, statusflags.pantan,
                            statusflags.serious_storeslip, statusflags.nonserious_storeslip, statusflags
                            .annextures, statusflags.levystatus, statusflags.pendingpara, statusflags
                            .seriouscountdata, statusflags.nonseriouscountdata);
                        // Call a function to update steps based on new statusFlags
                        updateStepStatusUI();

                        // Optionally, move to next step automatically
                        var currentRouteStep = $('.step-buttons .btn.active-step').data('step');
                        if (currentRouteStep === 'part_a') {
                            goToNextStep();
                        }

                    }
                },
                error: function(xhr, status, error) {
                                        hideLoader();
                    console.error('Error:', error);
                    //alert('Something went wrong!');
                    passing_alert_value('Confirmation', 'Data not Saved', 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                }
            });
        }

        /**Insert,Finalize Data for Part-A, Part-B Start*/
        $('#finalisebtn').on('click', function() {
            var finalize_hidden_txt = $('#finalize_hidden_txt').val();

            if (finalize_hidden_txt == 'part_a') {
                var finalizetext = 'PART - I';
            } else if (finalize_hidden_txt == 'part_b_seriousirregularities') {
                var finalizetext = 'PART - II (Serious Irregularities Paras)';
            } else if (finalize_hidden_txt == 'part_c') {
                var finalizetext = 'PART - III';
            } else if (finalize_hidden_txt == 'part_b_others') {
                var finalizetext = 'PART - II (Others)';
            } else {
                var finalizetext = 'PART - II (Non Serious Irregularities Paras)';

            }

            var finalizedata = 'Check all the details of  ' + finalizetext +
                ' completely. After submitting, data cannot be changed.';
            passing_alert_value('Confirmation', finalizedata, 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            $("#process_button").addClass("parta_processbtn");


        });

        $('#process_button').on('click', function() {
            var finalize_hidden_txt = $('#finalize_hidden_txt').val();
            finalize_partA(finalize_hidden_txt);
        });

        $('#savedraft_btn').on('click', function() {
            // alert('save draft');
            insert_reportData('savedraft');
        });

        function finalize_partA(partno) {
            //alert(partno);
            var auditscheduleid = $('#auditscheduleid').val();
            var finalize_hidden_txt = $('#finalize_hidden_txt').val();

            if (finalize_hidden_txt == 'part_a') {
                var finalizetext = 'PART - I';
            } else if (finalize_hidden_txt == 'part_b_seriousirregularities') {
                var finalizetext = 'PART - II (Serious Irregularities Paras)';
            } else if (finalize_hidden_txt == 'part_c') {
                var finalizetext = 'PART - III';
            } else if (finalize_hidden_txt == 'part_b_others') {
                var finalizetext = 'PART - II (Others)';
            } else {
                var finalizetext = 'PART - II (Non Serious Irregularities Paras)';

            }

            var instid = $('#instid').val();
            showLoader();
            $.ajax({
                url: '/Finalize_PartA', // ✅ Change this
                method: 'POST',
                data: {
                    'partno': partno,
                    'auditscheduleid': auditscheduleid,
                    'instid': instid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },
                success: function(response) {
                    hideLoader();

                    if (response.data == 'success') {
                        if (finalize_hidden_txt == 'pdfreport') {
                            passing_alert_value('Confirmation',
                                'Report Finalized and intimation sent successcully', 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                            //  $('#wordPreviewModal').modal('show');


                        } else {
                                                hideLoader();

                            passing_alert_value('Confirmation', ' ' + finalizetext +
                                ' data finalized successfully', 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                            $('#wordPreviewModal').modal('hide');

                        }
                        $('.preview_finalizebtnset').hide();
                        $('#pdf-preview').addClass('full-height');
                        $('#confirmation_alert').css('z-index', 100000);

                        $('#buttonset').hide();
                        var statusflags = response.statusflags;
                        localStorage.setItem('statusFlags', JSON.stringify(statusflags));
                        // console.log(statusflags);
                        buttonshow_Preview(statusflags.auditcertificate, statusflags.authorityofaudit,
                            statusflags.genesisofaudit, statusflags.accountdetails, statusflags.pantan,
                            statusflags.serious_storeslip, statusflags.nonserious_storeslip, statusflags
                            .annextures, statusflags.levystatus, statusflags.pendingpara, statusflags
                            .seriouscountdata, statusflags.nonseriouscountdata);
                        //$('#accountdetails_form').find('input').attr('disabled', true);

                    }
                },
                error: function(xhr, status, error) {
                                        hideLoader();
                    console.error('Error:', error);
                    alert('Something went wrong!');
                }
            });

        }


        function AccountGeneral(dynname) {
            //$('#buttonset').show();
            $('.parta_contents a').removeClass("activatestep");
            $('#parta_' + dynname).addClass("activatestep");

            $('.hideotherdiv').hide();
            $('#bankacc_det').show();

            var stepheading = $('.parta_contents .activatestep span').html();
            $('.step-header').html(stepheading);

            var remarks = '<?php echo $AccountDetails_remarks; ?>';
            if (!remarks) {
                remarks = '';
            }

            $('#whichform').val(dynname);

            // Call getStatusFlags with callback
            getStatusFlags(function(statusFlags) {
                var statusflag = statusFlags.accountdetails;

                if (statusflag === 'F') {
                    // $('#buttonset').hide();
                    $('#accountdetails_form').find('input').attr('disabled', true);
                    $('.actionshide').hide();

                }
            });

            buttonpreviewStsflags();

        }

        // PHP inject DB value safely into JS
        localStorage.removeItem('audit_certificate_data');
        localStorage.removeItem('authorityofaudit_data');
        localStorage.removeItem('genesis_data');
        localStorage.removeItem('itfiling_data');
        localStorage.removeItem('legalcomplaince_data');
        localStorage.removeItem('financialreview_remarks');
        localStorage.removeItem('levy_data');
        localStorage.removeItem('pending_para_data');



        function AuditCertificate() {
            $('.parta_contents a').removeClass("activatestep");
            $('#parta_certificate').addClass("activatestep");
            $('.hideotherdiv').hide();
            $('#auditcertificateDiv').show();

            const lang = localStorage.getItem('lang') || 'en';

            const $activeSpan = $('.parta_contents .activatestep span');

            const stepheading = $activeSpan.data(lang) || $activeSpan.text();

            $('.step-header').html(stepheading);


            // Load from localStorage
            let auditData = localStorage.getItem('audit_certificate_data');
            let remarks = '';
            var typecode = '<?php echo $cer_type_code; ?>';

            if (auditData) {
                try {
                    const parsedData = JSON.parse(auditData);
                    remarks = parsedData.cer_remarks || '';
                } catch (e) {
                    console.error('Error parsing audit certificate data from localStorage', e);
                }
            }
            if (!remarks) {
                const dbRemarks_Certificate = <?php echo json_encode($auditcertificate_remarks ?? ''); ?>;
                remarks = dbRemarks_Certificate;
            }

            toggleRemarks(typecode, remarks);
            //loadckeditor(remarks, 'auditcertificate_ckeditor');

            // Restore typecode selection (optional)
            if (typecode) {
                $(`input[name="cer_typecode"][value="${typecode}"]`).prop('checked', true);
            }

            $('#whichform').val('audit_certificate');

            // Use callback to get flags asynchronously
            getStatusFlags(function(statusFlags) {
                var statusflag = statusFlags.auditcertificate;

                if (statusflag === 'F') {
                    // $('#buttonset').hide();
                    $('#auditcertificateForm').find('input').attr('disabled', true);
                }
            });
            buttonpreviewStsflags();
        }


        function InstituteGenesis(dynname) {
            // $('#buttonset').show();
            $('.parta_contents a').removeClass("activatestep");
            $('#parta_' + dynname).addClass("activatestep");

            $('.hideotherdiv').hide();
            $('#inst_genesis_div').show();

            var stepheading = $('.parta_contents .activatestep span').html();
            $('.step-header').html(stepheading);

            let auditData = localStorage.getItem('genesis_data');
            let remarks = '';

            if (auditData) {
                try {
                    const parsedData = JSON.parse(auditData);
                    remarks = parsedData.genesis_remarks || '';
                } catch (e) {
                    console.error('Error parsing authorityofaudit data  from localStorage', e);
                }
            }

            if (!remarks) {
                const dbRemarks_Genesis = <?php echo json_encode($GenesisofAudit_remarks ?? ''); ?>;
                remarks = dbRemarks_Genesis;
            }
            loadckeditor(remarks, 'genesis_ckeditor');
            $('#whichform').val(dynname);

            buttonpreviewStsflags();

        }

        function AuthorityofAudit(dynname) {
            // $('#buttonset').show();
            $('.parta_contents a').removeClass("activatestep");
            $('#parta_' + dynname).addClass("activatestep");
            $('.hideotherdiv').hide();
            $('#autorityofaudit').show();

            var stepheading = $('.parta_contents .activatestep span').html();
            $('.step-header').html(stepheading);

            let auditData = localStorage.getItem('authorityofaudit_data');
            let remarks = '';

            if (auditData) {
                try {
                    const parsedData = JSON.parse(auditData);
                    remarks = parsedData.authorityofaudit_remarks || '';
                } catch (e) {
                    console.error('Error parsing authorityofaudit data from localStorage', e);
                }
            }

            if (!remarks) {
                const dbRemarks_Authority = <?php echo json_encode($authorityofaudit_remarks ?? ''); ?>;
                remarks = dbRemarks_Authority;
            }

            loadckeditor(remarks, 'auditofauthority_ckeditor');
            $('#whichform').val(dynname);

            buttonpreviewStsflags();

        }

        function PANTAN(dynname) {
            // $('#buttonset').show();
            $('.parta_contents a').removeClass("activatestep");
            $('#parta_' + dynname).addClass("activatestep");

            var stepheading = $('.parta_contents .activatestep span').html();
            $('.step-header').html(stepheading);

            $('.hideotherdiv').hide();
            $('#pantan_div').show();
            $('#whichform').val(dynname);

            // Load remarks immediately (these don't depend on statusFlags)
            loadRemarks('itfiling_issue', 'itfiling_data', 'itfiling_remarks', <?php echo json_encode($itfilings_remarks ?? ''); ?>);
            loadRemarks('legal_complaince', 'legalcomplaince_data', 'legalcomplaince_remaks', <?php echo json_encode($legalcomplaince_remarks ?? ''); ?>);
            loadRemarks('financial_review', 'financialreview_remarks', 'financialreview_remarks', <?php echo json_encode($financialreview_remarks ?? ''); ?>);

            // Get statusFlags asynchronously and act on pantan flag
            getStatusFlags(function(statusFlags) {
                var statusflag = statusFlags.pantan;
                if (statusflag === 'F') {
                    //$('#buttonset').hide();
                    $('#pantan_form').find('input').attr('disabled', true);
                }
            });
            buttonpreviewStsflags();

        }


        function loadRemarks(fieldKey, localKey, localremarkskey, phpFallbackVar) {
            let value = '';
            const localData = localStorage.getItem(localKey);
            if (localData) {
                try {
                    const parsed = JSON.parse(localData);
                    value = parsed[localremarkskey] || '';
                } catch (e) {
                    console.error(`Error parsing ${localKey}`, e);
                }
            }
            //  alert(value);
            if (!value) {
                value = phpFallbackVar;
            }


            loadckeditor(value, fieldKey);
        }


        function AuditLevyCertificate() {
            $('.partb_contents a').removeClass("activatestep");
            $('#partb_auditlevycertificate').addClass("activatestep");
            // $('.step-header').html('Audit Fees / Audit Levy Certificate');

            const lang = localStorage.getItem('lang') || 'en';

            let heading = '';
            if (lang === 'en') {
                heading = 'Audit Fees / Audit Levy Certificate';
            } else {
                heading = 'தணிக்கைக் கட்டணம் / தணிக்கை வசூல் சான்றிதழ்';
            }

            $('.step-header').html(heading);
            $('.hideotherdiv').hide();
            $('.sub-step-header').hide();
            $('#auditlevycert_div').show();
            var remarks = '';
            loadRemarks('levycertificate_ckeditor', 'levy_data', 'levy_remarks', <?php echo json_encode($auditlevy_remarks ?? ''); ?>);
            //loadckeditor(remarks,'levycertificate_ckeditor');

            $('#whichform').val('audit_levy_form');
            buttonpreviewStsflags();
        }

        function PendingParaDet() {
            $('.partb_contents a').removeClass("activatestep");
            $('#partb_pendingpara').addClass("activatestep");

            const lang = localStorage.getItem('lang') || 'en';

            let heading = '';
            if (lang === 'en') {
                heading = 'Pending Para Details';
            } else {
                heading = 'நிலுவையில் உள்ள பத்திகள் விவரங்கள்';
            }

            $('.step-header').html(heading);
            // $('.step-header').html('Pending Para Details');
            $('.hideotherdiv').hide();
            $('.sub-step-header').hide();
            $('#pendingparadet_div').show();
            loadRemarks('pendingparadet_ckeditor', 'pending_para_data', 'pendingpara_remarks', <?php echo json_encode($pendingparadetails_remarks ?? ''); ?>);
            $('#whichform').val('pending_para_form');
            buttonpreviewStsflags();
        }

        function Annexures() {
            //$('#auditcertificateDiv').hide();
            //$('#autorityofaudit').hide();
            $('.partc_contents a').removeClass("activatestep");
            $('#partc_annexure').addClass("activatestep");
            $('.hideotherdiv').hide();
            $('#annexturediv').show();
            // $('.step-header').html('List of Annexures');
            const lang = localStorage.getItem('lang') || 'en';

            let heading = '';
            if (lang === 'en') {
                heading = 'List of Annexures';
            } else {
                heading = 'இணைப்புகளின் பட்டியல்';
            }

            $('.step-header').html(heading);
            annextureShow();

        }

        function Accounts_Statements() {
            //$('#auditcertificateDiv').hide();
            // $('#autorityofaudit').hide();
            $('.partc_contents a').removeClass("activatestep");
            $('#partc_accountstatement').addClass("activatestep");
            $('.hideotherdiv').hide();
            $('#accountstatementdiv').show();
            // $('.step-header').html('List of Accounts and Statements');
            const lang = localStorage.getItem('lang') || 'en';

            let heading = '';
            if (lang === 'en') {
                heading = 'List of Accounts and Statements';
            } else {
                heading = 'கணக்குகள் மற்றும் அறிக்கைகளின் பட்டியல்';
            }

            $('.step-header').html(heading);
            // $('#whichform').val(dynname);
            AnnextureStatementPreview();
        }


        let tdsIndex = {{ $tdsIndex ?? 0 }};

        @if ($tdsIndex > 0)
            // Set the radio to "Available"
            $('input[name="tds_avail"][value="01"]').prop('checked', true);
            $('#tds_availdiv').show();
        @else
            // No DB data, set "Not Available" and hide the TDS section
            $('input[name="tds_avail"][value="02"]').prop('checked', true);
            addRowTDS();
            $('#tds_availdiv').hide();
        @endif

        function addRowTDS() {
            var gst_finanicialyear = JSON.parse('<?php echo json_encode($audityears); ?>');

            let yearOptions = '<option value="">Select Year</option>';

            gst_finanicialyear.forEach(item => {
                yearOptions += `<option value="${item.reportfinancialyear}">
                                ${item.reportfinancialyear}
                            </option>`;
            });

            let newRow = `
        <tr class="entry-row" data-index="${tdsIndex}">
            <td>
                <select class="form-select" name="tdsdata[${tdsIndex}][year]">
                   ${yearOptions}
                </select>
            </td>
            <td>
                <select class="form-select" name="tdsdata[${tdsIndex}][status]">
                    <option value="">Select Status</option>
                    <option value="24Q">24Q</option>
                    <option value="26Q">26Q</option>
                </select>
            </td>
            <td>
                <select class="form-select" name="tdsdata[${tdsIndex}][period]">
                    <option value="">Select Period</option>
                    <option value="Q1">Q1</option>
                    <option value="Q2">Q2</option>
                    <option value="Q3">Q3</option>
                    <option value="Q4">Q4</option>
                </select>
            </td>
            <td>
                <div class="form-check form-check-inline">
                    <input class="form-check-input success" type="radio" name="tdsdata[${tdsIndex}][remit]" value="Yes">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input danger" type="radio" name="tdsdata[${tdsIndex}][remit]" value="No">
                    <label class="form-check-label">No</label>
                </div>
            </td>
            <td>
                <div class="form-check form-check-inline">
                    <input class="form-check-input success" type="radio" name="tdsdata[${tdsIndex}][filed]" value="Yes">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input danger" type="radio" name="tdsdata[${tdsIndex}][filed]" value="No">
                    <label class="form-check-label">No</label>
                </div>
            </td>
            <td class="sticky-buttons">
                <button type="button" class="btn btn-success btn-small" onclick="addRowTDS()">+</button>
                <button type="button" class="btn btn-danger btn-small" onclick="removeRowTDS(this)">−</button>
            </td>
        </tr>`;

            $('.tds_filed_body').append(newRow);
            tdsIndex++;
        }




        function removeRowTDS(button) {
            if ($('#tdsfilled_table .entry-row').length > 1) {
                $(button).closest('.entry-row').remove();
            } else {
                passing_alert_value('Confirmation', 'Atleast one TDS Filed Details required', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'confirmation_alert');
            }

        }

        $('input[name="tds_avail"]').on('change', function() {
            if ($(this).val() === '01') {
                $('#tds_availdiv').show();
            } else {
                $('#tds_availdiv').hide();
            }
        });

        let fileUploadIds = []; // Global array

        function updateHiddenField() {
            $('#file_upload_ids').val(JSON.stringify(fileUploadIds));
        }

        function renderFiles(files) {
            var $fileList = $('#file-list');
            var template = document.getElementById('file-preview');

            $fileList.empty();
            $('#file_upload_ids').val(' ')
            let fileUploadIds = []; // Reset global array (without `let`)

            $.each(files, function(i, file) {
                $('#files_heading').show();
                var clone = $(template.content).clone();

                const fileUrl = file.filepath;

                // Set placeholder preview image
                clone.find('img').attr('src', '../assets/images/file.png');

                // Wrap image in a clickable link
                clone.find('img').wrap(
                    $('<a>', {
                        href: fileUrl,
                        target: '_blank',
                        title: file.filename
                    })
                );

                // Track fileuploadid
                fileUploadIds.push(file.fileuploadid);
                //  updateHiddenField(); // Now uses global array

                // Store in DOM for later removal
                clone.find('.file_upload_id').val(file.fileuploadid);
                //clone.attr('data-fileuploadid', file.fileuploadid);
                clone.find('.file-preview').attr('data-fileuploadid', file.fileuploadid);

                clone.find('.file-name').text(file.filename);
                clone.find('.file-size').text(formatSize(file.filesize));

                $fileList.append(clone);
            });
        }


        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
            else return (bytes / 1048576).toFixed(2) + ' MB';
        }



        function annextureShow() {
            var auditscheduleid = '<?php echo $instdel[0]['auditscheduleid']; ?>';
showLoader();
            $.ajax({
                url: '/get-annexure-data',
                method: 'GET',
                data: {
                    'auditscheduleid': auditscheduleid
                },
                dataType: 'json',
                success: function(data) {
                                        hideLoader();
                    const $tbody = $('.annexture_tbody');
                    $tbody.empty();

                    if (data.length === 0) {
                        $tbody.append(
                            `<tr><td colspan="4" style="text-align:center;">No Annexure found</td></tr>`);
                        return;
                    }

                    // Group by parano
                    const grouped = {};

                    data.forEach(item => {
                        const key = item.parano;

                        if (!grouped[key]) {
                            grouped[key] = {
                                slipdetails: item.slipdetails,
                                attachments: []
                            };
                        }

                        if (item.filename && item.filename.trim() !== '') {
                            const files = item.filename.split(',');
                            const paths = item.filepath.split(',');

                            files.forEach((file, i) => {
                                const trimmedFile = file.trim();
                                const trimmedPath = paths[i] ? paths[i].trim() : '';
                                grouped[key].attachments.push({
                                    filename: trimmedFile,
                                    filepath: trimmedPath
                                });
                            });
                        }
                    });
const sortedKeys = Object.keys(grouped).sort((a, b) => {
 // Numeric sort if parano is a number, otherwise lexicographic
 if (!isNaN(a) && !isNaN(b)) {
 return Number(a) - Number(b); // ascending
 }
 return a.localeCompare(b); // for string parano
 });
                    let serial = 1;
                    for (const parano in grouped) {
                        const entry = grouped[parano];
                        const {
                            slipdetails,
                            attachments
                        } = entry;

                        let attachmentHtml = '-';
                        if (attachments.length > 0) {
                            attachmentHtml = attachments.map((a, i) => {
                                return `<div><a href="../${a.filepath}" target="_blank" class="text-primary">${i + 1}) <b>${a.filename}</b></a></div>`;
                            }).join('');
                        }

                        const row = `
                        <tr>
                            <td style="width:5% !important;text-align:center !important;" >${serial}</td>
                            <td class="text-wrap" style="width:40% !important;text-align:center !important;">${slipdetails}</td>
                            <td class="text-wrap" style="width:15% !important;text-align:center !important;">${parano}</td>
                            <td class="text-wrap" style="width:40% !important;text-align:center !important;">${attachmentHtml}</td>
                        </tr>
                    `;

                        $tbody.append(row);
                        serial++;
                    }
                },
                error: function(xhr, status, error) {
                                        hideLoader();
                    $('.annexture_tbody').html(
                        `<tr><td colspan="4" style="text-align:center;color:red;">Error loading data</td></tr>`
                    );
                    console.error('Error:', error);
                }
            });
        }


        // On "Edit" click: enable file input
        $(document).on('click', '.edit-file', function() {
            const key = $(this).data('key');
            $(`input[name="annexure_files[${key}]"]`).prop('disabled', false).focus();
            $(`input[name="annexure_files[${key}]"]`).show();
            $(`.file-preview[data-key="${key}"]`).hide();
        });

        // On "Delete" click: send AJAX to remove file
        $(document).on('click', '.delete-file', function() {
            const key = $(this).data('key');
            const auditScheduleId = '<?php echo $instdel[0]['auditscheduleid']; ?>';

            if (!confirm("Are you sure you want to delete this file?")) return;

            const $button = $(this);
            $button.prop('disabled', true); // Prevent double click

            $.ajax({
                url: '/delete-annexure-file',
                method: 'POST',
                data: {
                    key: key,
                    auditscheduleid: auditScheduleId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        passing_alert_value('Confirmation', `Annexture Deleted Successfully!`,
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                        $(`input[name="annexure_files[${key}]"]`).prop('disabled', false).val('');
                        $(`input[name="annexure_files[${key}]"]`).show();
                        $(`.file-preview[data-key="${key}"]`).hide();
                        // AnnextureStatementPreview();
                    } else {
                        alert(data.message || 'Deletion failed.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while deleting the file.');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });




        let accountParticulars = @json($account_particulars);
        let rowCount = 0;

        function getSelectedValues() {
            const selected = [];
            $('select[name="accparticulars[]"]').each(function() {
                const val = $(this).val();
                if (val) selected.push(val);
            });
            return selected;
        }

        function getAvailableOptions(selectedValues) {
            const lang = localStorage.getItem('lang') || 'en';

            // Set placeholder text based on language
            let options = (lang === 'ta') ?
                '<option value="">கணக்கு விவர வகையைத் தேர்ந்தெடுக்கவும்</option>' :
                '<option value="">Select Account Particulars Type</option>';

            accountParticulars.forEach(item => {
                if (!selectedValues.includes(item.accpar_key)) {
                    const label = (lang === 'ta') ? item.accpar_tname : item.accpar_ename;
                    options += `<option value="${item.accpar_key}">${label}</option>`;
                }
            });

            return options;
        }


        function updateAllDropdowns() {
            const selectedValues = getSelectedValues();

            $('select[name="accparticulars[]"]').each(function() {
                const $select = $(this);
                const currentValue = $select.val();
                const options = getAvailableOptions(selectedValues.filter(v => v !== currentValue));
                $select.html(options).val(currentValue); // Re-populate while preserving selected value

                // ✅ Update corresponding file input's name attribute
                const $fileInput = $select.closest('.entry-row').find('input[type="file"]');
                if (currentValue) {
                    $fileInput.attr('name', `annexure_files[${currentValue}]`);
                } else {
                    $fileInput.attr('name', `annexure_files[]`);
                }
            });
        }


        addRowAnnexture()

        function addRowAnnexture() {

            /* if(accountParticulars.length == 0)
             {
                 var appendempty ='List of Accounts and Statements not mapped for the institute belongs to this category and department.<br>Please contact administrator for more details';
                 $('#emptydivshow').append(appendempty);
                 $('#tablediv').hide();
                 return;
             }

             $('#emptydivshow').hide();
             $('#tablediv').show();
             const currentCount = $('#accountstatementdiv .entry-row').length;
             if (currentCount >= accountParticulars.length) {
                 var errrormsg ='Account particulars limit reached.';
                 passing_alert_value('Confirmation', errrormsg, 'confirmation_alert',
                             'alert_header', 'alert_body',
                             'confirmation_alert');
                 return;
             }*/



            rowCount++;
            const selectedValues = getSelectedValues();
            const selectOptions = getAvailableOptions(selectedValues);

            let newRow = `
            <div class="row entry-row mb-2">
                <div class="col-md-4 mb-1">
                    <select name="accparticulars[]" class="form-select accparticulars-select" onchange="updateAllDropdowns()">
                        ${selectOptions}
                    </select>
                </div>
                <div class="col-md-6 mb-1">
                    <input class="form-control form-control-sm" type="file" name="annexure_files[]" accept=".pdf, .xls, .xlsx" style="padding: 0.25rem 0.5rem;">
                     <div class="file-preview"></div>
                </div>

                <div id="annexturebtnset" class="col-md-2 mb-3 annexturebtnset" >
                    <button type="button" class="btn btn-success btn-small add-annexture-inline" onclick="addRowAnnexture()">+</button>
                    <button type="button" class="btn btn-danger btn-small" onclick="RemoveAnnexture(this)">−</button>
                </div>
            </div>
        `;

            $('#appendannextures').append(newRow);
            updateAllDropdowns(); // refresh dropdowns
        }

        function RemoveAnnexture(button) {

            if ($('#accountstatementdiv .entry-row').length > 1) {
                $(button).closest('.entry-row').remove();
                updateAllDropdowns(); // refresh dropdowns
            } else {
                passing_alert_value('Confirmation', 'Atleast one annexture required for proceed', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'confirmation_alert');
            }
        }

        $('#attachmentForm').on('submit', function(e) {

            e.preventDefault();

            let hasError = false;
            let errorMessage = '';

            $('.entry-row').each(function(index) {
                const $row = $(this);
                const selectVal = $row.find('.accparticulars-select').val();
                const $fileInput = $row.find('input[type="file"]');

                // Rule 1: Dropdown was opened (user interacted) but no option selected
                if (selectVal === '' || selectVal === null) {
                    hasError = true;
                    errorMessage = `Please select account particular.`;
                    errorKey = 'accountparti';
                    return false; // break loop
                }

                // Rule 2: File input exists + visible + no file selected
                if ($fileInput.length > 0 && $fileInput.is(':visible') && $fileInput[0].files.length ===
                    0) {
                    hasError = true;
                    errorMessage = `Attachment is required. Please check`;
                    errorKey = 'attchmenterr';
                    return false; // break loop
                }
            });

            if (hasError && errorKey) {


                getLabels_jsonlayout([{
                    key: errorKey
                }], 'N').then((text) => {
                    const errorMessage = Object.values(text)[0] || "Error occurred";
                    passing_alert_value('Confirmation', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                });

                // passing_alert_value(
                //     'Confirmation',
                //     errorMessage,
                //     'confirmation_alert',
                //     'alert_header',
                //     'alert_body',
                //     'confirmation_alert'
                // );
                return;
            }


            // All good: proceed with form submission via AJAX
            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('annextures_upload') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    passing_alert_value('Confirmation', 'Attachments Uploaded successfully',
                        'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');

                    $('#attachmentForm')[0].reset();
                    AnnextureStatementPreview();

                    const statusflags = response.statusflags;
                    buttonshow_Preview(
                        statusflags.auditcertificate,
                        statusflags.authorityofaudit,
                        statusflags.genesisofaudit,
                        statusflags.accountdetails,
                        statusflags.pantan,
                        statusflags.serious_storeslip,
                        statusflags.nonserious_storeslip,
                        statusflags.annextures,
                        statusflags.levystatus,
                        statusflags.pendingpara,
                        statusflags.seriouscountdata,
                        statusflags.nonseriouscountdata
                    );
                },
                error: function(xhr) {
                    let errorMsg = 'Error while uploading files!';

                    if (xhr.status === 400) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message === 'nofiles_choosen') {
                                errorMsg =
                                    'No file(s) selected. Please attach a file before uploading.';
                            }
                        } catch (e) {
                            console.error('Invalid JSON response', e);
                        }
                    }

                    passing_alert_value('Error', errorMsg, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');

                    // console.log(xhr.responseText);
                }

            });
        });

        function AnnextureStatementPreview() {
            const auditScheduleId = '<?php echo $instdel[0]['auditscheduleid']; ?>';
      showLoader();
            $.ajax({
                url: '/get-accountstatement-files/' + auditScheduleId,
                method: 'GET',
                success: function(data) {
                    hideLoader();
                    const fileKeys = Object.keys(data);
                    const totalFromDB = fileKeys.length;

                    const existingRowCount = $('.entry-row').length;
                    $('#appendannextures').empty();




                    // 🟢 First-time load: if no data from DB, ensure only one row exists
                    if (totalFromDB === 0) {
                        addRowAnnexture(); // only one row shown on empty'
                        $('.annexturebtnset').show();
                        return;
                    }

                    // 🟢 If rows from DB, ensure enough rows are added
                    if (totalFromDB > 0) {
                        const rowsToAdd = totalFromDB;
                        for (let i = 0; i < rowsToAdd; i++) {
                            addRowAnnexture(); // append more rows
                        }
                    }

                    // Now fill in data
                    fileKeys.forEach((key) => {
                        const fileData = data[key];
                        // Find first empty or matching dropdown row
                        let matchedRow = $('.entry-row').filter(function() {
                            const select = $(this).find('select[name="accparticulars[]"]');
                            return select.val() === '' || select.val() === key;
                        }).first();

                        if (!matchedRow.length) return;

                        // Set dropdown value
                        matchedRow.find('select[name="accparticulars[]"]').val(key);
                        updateAllDropdowns(); // update file input name and dropdowns

                        // Add preview
                        matchedRow.find('.file-preview').show();
                        const previewDiv = matchedRow.find('.file-preview');

                        //console.log(previewDiv);

                        let fullPath = fileData.filepath;

                        let statusflag = fileData.statusflag;
                        //alert(statusflag);

                        // Extract filename with extension
                        let filenameWithExtension = fullPath.split('/').pop();

                        previewDiv.attr('data-key', key);
                        previewDiv.html(`
                        <div class="">
                            <div class="btn mb-2" style="background-color:#eaeff4 !important;">
                                <a style="color:black !important;" href="/${fileData.filepath}" target="_blank">${shortenFileName(fileData.filename)}</a>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-sm btn-info edit-file annexturebtnset" data-key="${key}">
                                    <i class="ti ti-edit fs-4 text"></i>
                                </button>
                                &nbsp;
                                <button type="button" class="btn btn-sm btn-danger delete-file annexturebtnset" data-key="${key}">
                                    <i class="ti ti-trash fs-4 text"></i>
                                </button>
                            </div>
                        </div>
                    `);

                        if (statusflag === 'F') {
                            $('.annexturebtnset').hide();

                        } else {
                            $('.annexturebtnset').show();
                        }

                        // Hide file input
                        matchedRow.find('input[type="file"]').hide();
                    });
                }
            });
        }

        function shortenFileName(filename, maxLength = 30) {
            if (filename.length <= maxLength) return filename;
            return filename.substring(0, maxLength) + '...';
        }



        /**APPEND FOR ACCOUNT DETAILS */
        $(document).ready(function() {

            // Inline Add button
            $('.accountdetails_tbody').on('click', '.add-entry-inline', function() {
                addRow(this);
            });

            // Inline Remove button
            $('.accountdetails_tbody').on('click', '.remove-entry-inline', function() {
                removeRow(this);
            });

            // Function to perform calculations
            function calculateRow($row) {
                const obInput = $row.find('input[name^="ob"]');
                const receiptsInput = $row.find('input[name^="receipts"]');
                const expenditureInput = $row.find('input[name^="expenditure"]');
                const addInput = $row.find('input[name^="add"]');
                const lessInput = $row.find('input[name^="less"]');

                const totalInput = $row.find('input[name^="total"]');
                const cbCashbookInput = $row.find('input[name^="cb_cashbook"]');
                const cbPassbookInput = $row.find('input[name^="cb_passbook"]');

                const ob = parseFloat(obInput.val()) || 0;
                const receipts = parseFloat(receiptsInput.val()) || 0;
                const expenditure = parseFloat(expenditureInput.val()) || 0;
                const add = parseFloat(addInput.val()) || 0;
                const less = parseFloat(lessInput.val()) || 0;

                const total = ob + receipts;

                const isAnyInputFilled =
                    obInput.val() || receiptsInput.val() || expenditureInput.val() || addInput.val() || lessInput
                    .val();

                //Check if expenditure > total
                /*if (expenditure > total) {
                    alert("Expenditure cannot be greater than Total. Please check the values.");
                    cbCashbookInput.val('');
                    cbPassbookInput.val('');
                    //expenditureInput.val('');
                    markInvalid(cbCashbookInput);
                    markInvalid(cbPassbookInput);
                     $('#buttonset').hide();
                    return;
                }*/

               const cb_cashbook = parseFloat((total - expenditure).toFixed(2));
                	const cb_passbook = Math.round((cb_cashbook + add - less) * 100) / 100;

                //Check if cb_passbook is negative
                /*if (cb_passbook < 0) {
                    alert("CB as per Passbook is negative. Please check the values.");
                    cbPassbookInput.val('');
                    markInvalid(cbPassbookInput);
                                     $('#buttonset').hide();

                    return;
                }*/

                // All good — set values and remove red borders
                totalInput.val(total);
                cbCashbookInput.val(cb_cashbook);
                cbPassbookInput.val(cb_passbook);

                if (isAnyInputFilled) {
                    [totalInput, cbCashbookInput, cbPassbookInput].forEach(input => {
                        if (!input.val()) {
                            markInvalid(input);

                        } else {
                            clearInvalid(input);
                            $('#buttonset').show();
                        }
                    });
                } else {
                    // Clear visual required class if no input at all
                    [totalInput, cbCashbookInput, cbPassbookInput].forEach(clearInvalid);
                }
            }


            function markInvalid(input) {
                input.addClass('is-invalid');
            }

            function clearInvalid(input) {
                input.removeClass('is-invalid');
            }



            // Delegate change event to relevant fields
            $(document).on('input',
                'input[name^="ob"], input[name^="receipts"], input[name^="expenditure"], input[name^="add"], input[name^="less"]',
                function() {
                    const $row = $(this).closest('tr');
                    calculateRow($row);
                });

        });


        function addRow(afterBtn = null) {
            const index = $('#entry-table-body tr').length;
            const newRowHtml = createNewRowHtml(index);

            $(afterBtn).closest('tr').after(newRowHtml);

            updateRowIndices();
            translate?.();
        }

        function removeRow(btn) {
            if ($('.accountdetails_tbody tr').length > 1) {
                $(btn).closest('tr').remove();
                updateRowIndices();
            } else {
                getLabels_jsonlayout([{

                    key: 'atleastone'
                }], 'N').then((text) => {
                    let alertMessage = Object.values(text)[0] ||
                        "Error Occured";
                    passing_alert_value('Confirmation', alertMessage,
                        'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                });
                //   passing_alert_value('Confirmation', 'Atleast one account details required', 'confirmation_alert',
                //             'alert_header', 'alert_body',
                //             'confirmation_alert');
            }
        }

        function updateRowIndices() {
            $('#entry-table-body tr').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('td.sticky-col').text(i + 1);

                // $(this).find('input').each(function() {
                $(this).find('input, select, textarea').each(function() {

                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${i}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        function createNewRowHtml(index) {
            return `
            <tr data-index="${index}">
                <td class="sticky-col">${index + 1}</td>
                <td><input type="text" class="form-control" maxlength="200" name="scheme[${index}]"></td>
                <td><input type="text" class="form-control" maxlength="20" name="account_details[${index}]"></td>
                <td><input type="text" class="form-control" maxlength="20" name="branch[${index}]"></td>
                <td><input type="number" class="form-control maxlength-16" maxlength="16" name="bank_account_number[${index}]"></td>
	<td><select class="form-control" id="account_type"
          name="account_type[${index}]">
          <option value="">--select account type--
          </option>
          <option value="01">Savings Account</option>
          <option value="02">Current Account</option>
      </select></td>
                <td> <input type="text" class="form-control maxlength-15 numberswithdecimal :removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" name="ob[${index}]"></td>
                 	<td><input type="text" class="form-control    maxlength-15  removesplchar_numberwithdecimal numberswithdecimal"  maxlength="15" name="receipts[${index}]"></td>
                <td><input type="text" class="form-control  maxlength-15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15"readonly name="total[${index}]"></td>
                <td><input type="text" class="form-control  maxlength-15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" name="expenditure[${index}]"></td>
                <td><input type="text" class="form-control  maxlength-15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" readonly name="cb_cashbook[${index}]"></td>
                <td><input type="text" class="form-control  maxlength-15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" name="add[${index}]"></td>
                <td><input type="text" class="form-control" maxlength=15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" name="less[${index}]"></td>
                <td><input type="number" class="form-control  maxlength-15 removesplchar_numberwithdecimal numberswithdecimal" maxlength="15" readonly name="cb_passbook[${index}]"></td>
                <td class="sticky-buttons">
                    <button  type="button" class="btn btn-success btn-small add-entry-inline">+</button>
                    <button type="button" class="btn btn-danger btn-small remove-entry-inline">−</button>
                </td>
            </tr>
        `;
        }

        $(document).on("keypress", ".removesplchar_numberwithdecimal", function(event) {
            let char = String.fromCharCode(event.which);
            let value = this.value;

            // Allow only digits and .
            if (!/[0-9.]/.test(char)) {
                event.preventDefault();
            }

            // Prevent more than one dot
            if (char === '.' && value.includes('.')) {
                event.preventDefault();
            }

            // Prevent dot as the first character
            if (char === '.' && value.length === 0) {
                event.preventDefault();
            }

            // Prevent more than 2 digits after decimal
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1].length >= 2 && this.selectionStart > value.indexOf('.')) {
                    event.preventDefault();
                }
            }
        });
        /**APPEND FOR ACCOUNT DETAILS */
 $('input.maxlength-5').on('input', function() {
            let value = this.value
                .replace(/[^0-9.]/g, '') // allow only digits and '.'
                .replace(/(\..*)\./g, '$1'); // allow only one '.'

            // Restrict to 2 digits after decimal point
            if (value.includes('.')) {
                const parts = value.split('.');
                parts[1] = parts[1].slice(0, 2); // keep only first 2 digits after '.'
                value = parts[0] + '.' + parts[1];
            }

            // Limit to max 12 characters overall
            if (value.length > 15) {
                value = value.slice(0, 15);
            }

            this.value = value;
        });


        $('input[name^="bank_account_number"]').on('input', function() {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');

            // Enforce max length of 15
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
            // });

            // Optional: Validate on blur (show warning if less than 11 digits)
            const $input = $(this);
            const value = this.value;
            const alertShown = $input.data('alert-shown') || false;

            // Show alert only ONCE when value is < 11 and alert hasn't been shown
            if (value.length > 0 && value.length < 11 && !alertShown) {
                // alert("Bank account number must be at least 11 digits.");

                getLabels_jsonlayout([{

                    key: 'digitcount'
                }], 'N').then((text) => {
                    let alertMessage = Object.values(text)[0] ||
                        "Error Occured";
                    passing_alert_value('Confirmation', alertMessage,
                        'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                });
                $input.data('alert-shown', true); // mark as shown
            }

            // If valid (>= 11), reset alert tracking so next time it's invalid, it can show again
            if (value.length >= 11) {
                $input.data('alert-shown', false);
            }
            $('#savedraft_btn').on('submit', function(e) {
                let isValid = true;

                $('input[name^="bank_account_number"]').each(function() {
                    const val = $(this).val().trim();
                    if (val.length < 11 || val.length > 16 || !/^\d+$/.test(val)) {
                        alert("Each bank account number must be between 11 and 16 digits.");
                        $(this).focus();
                        isValid = false;
                        return false; // break loop
                    }
                });

                if (!isValid) {
                    e.preventDefault(); // block form
                }
            });
        });
        // });


        document.getElementById('previewBtn').addEventListener('click', function() {
            // Open the modal
            var myModal = new bootstrap.Modal(document.getElementById('wordPreviewModal'));

            var lang = getLanguage('Y');

            var financialyearcode = '<?php echo $financialyearcode; ?>';
            var instid = '<?php echo $instid; ?>';
            var spilloverflag = '<?php echo $spilloverflag; ?>';


            // Make an AJAX request to get the Word file content as HTML
            fetch('/preview-word?scheduleid=' + scheduleId + '&lang=' + lang + '&whichpart=all' +
                    '&instid=' + instid + '&financialyearcode=' + financialyearcode + '&spilloverflag=' +
                    spilloverflag)
                .then(response => response.json())
                .then(data => {
                    if (data.res == 'success') {
                        // Create an iframe element dynamically
                        myModal.show();
                        $('#finalizeReport').prop('checked', false);

                        $('.preview_finalizebtnset').show();
                        $('#pdf-preview').removeClass('full-height');
                        $('#checkboxlabel_content').html(
                            'send intimation to the institution and finalize the report?');
                        $('#finalizereport_pdf').html('Finalize Report');

                        getStatusFlags(function(statusflags) {

                            if (statusflags.pdfreport === 'F') {
                                $('.preview_finalizebtnset').hide();
                                $('#pdf-preview').addClass('full-height');

                            } else {
                                $('.preview_finalizebtnset').show();
                                $('#pdf-preview').removeClass('full-height');

                            }
                        });

                        var iframe = document.createElement('iframe');
                        iframe.srcdoc = data.html; // Use the HTML content as the iframe's srcdoc
                        document.getElementById('pdf-preview').innerHTML = ''; // Clear any previous iframe
                        document.getElementById('pdf-preview').appendChild(
                            iframe); // Add the iframe to the modal

                        // Show the Download button
                        // document.getElementById('downloadBtn').style.display = 'inline-block';

                        var filename = data.filename;
                        $('#filename').val(filename);
                        localStorage.setItem('filename', filename);

                    } else {
                        alert('No Report Available.');

                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        });


        document.getElementById('finalizereport_pdf').addEventListener('click', function() {
            const isChecked = document.getElementById('finalizeReport').checked;

            if (isChecked) {
                var finalizedata = 'Are you sure to want to finalize?';
                passing_alert_value('Confirmation', finalizedata, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
                var btnhtml = $('#finalizereport_pdf').html();

                if (btnhtml == 'Finalize Report') {
                    $('#finalize_hidden_txt').val('pdfreport');
                }
                $('#confirmation_alert').css('z-index', 100000);
            } else {
                passing_alert_value('Confirmation',
                    'Please check the checkbox to confirm before finalizing the report', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');

                $('#confirmation_alert').css('z-index', 100000);
            }
        });

        // Add event listener for the Download button
        /* document.getElementById('downloadBtn').addEventListener('click', function()
         {
             var filename = $('#filename').val();
             // You can directly provide the download URL to the original Word file
             window.location.href = '/download-word-file/' +filename; // Adjust this URL as per your route for downloading the Word file
         });*/

        let drakeInstances = [];
        let drake = null;

        document.addEventListener('DOMContentLoaded', function() {
            // ... existing dragula setup and buttonshow_Preview() ...

            // Get serious and non-serious containers
            const seriousContainer = document.querySelector('#part-b-serious');
            const nonSeriousContainer = document.querySelector('#part-b-nonserious');

            // Store the initial order of both groups
            updateSlipOrder(seriousContainer, 'serious');
            updateSlipOrder(nonSeriousContainer, 'nonserious');
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Destroy any previous drakes
            drakeInstances.forEach(d => d.destroy());
            drakeInstances = [];



            const groupContainers = document.querySelectorAll('.sortable-group');
            groupContainers.forEach(groupEl => {
                const drake = dragula([groupEl], {
                    invalid: function(el, handle) {
                        return false;
                    }
                });

                drake.on('drop', function(el, target, source, sibling) {
                    const seriousContainer = document.querySelector('#part-b-serious');
                    const nonSeriousContainer = document.querySelector('#part-b-nonserious');

                    updateSlipOrder(seriousContainer, 'serious');
                    updateSlipOrder(nonSeriousContainer, 'nonserious');
                });


                drakeInstances.push(drake);
            });

            const statusFlags = getStatusFlags();
            buttonshow_Preview(
                statusFlags.auditcertificate,
                statusFlags.authorityofaudit,
                statusFlags.genesisofaudit,
                statusFlags.accountdetails,
                statusFlags.pantan,
                statusFlags.serious_storeslip,
                statusFlags.nonserious_storeslip,
                statusFlags.annextures,
                statusFlags.seriouscountdata,
                statusFlags.nonseriouscountdata
            );
        });
        buttonpreviewStsflags()

        async function buttonpreviewStsflags() {
            const statusFlags = await getStatusFlags(); // wait for AJAX result
            console.log(statusFlags); // debug

            buttonshow_Preview(
                statusFlags.auditcertificate,
                statusFlags.authorityofaudit,
                statusFlags.genesisofaudit,
                statusFlags.accountdetails,
                statusFlags.pantan,
                statusFlags.serious_storeslip,
                statusFlags.nonserious_storeslip,
                statusFlags.annextures,
                statusFlags.seriouscountdata,
                statusFlags.nonseriouscountdata
            );
        }



        function updateSlipOrder(container, type) {
            let slipOrder = [];

            // Get all .sortable-group blocks inside the container (which is #part-b-serious)
            const groups = container.querySelectorAll('.sortable-group');

            groups.forEach(group => {
                Array.from(group.children).forEach(item => {
                    if (item.id && item.id.startsWith('partb_auditslip')) {
                        let slipId = item.id.replace('partb_auditslip', '');
                        slipOrder.push(slipId);
                    }
                });
            });

            let orderedObject = {};
            slipOrder.forEach((id, index) => {
                orderedObject[index + 1] = id;
            });

            //console.log(`Sending ${type}:`, orderedObject);

            const auditschid = '{{ $auditscheduleid }}';

            $.ajax({
                url: '/save-slip-order',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    slip_order_json: JSON.stringify(orderedObject),
                    auditschid: auditschid,
                    type: type
                },
                success: function(response) {
                    //console.log(`Saved ${type} slip order`);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Error saving slip order.");
                }
            });
        }



        function isValidPAN(pan) {
            var regex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            return regex.test(pan);
        }

        $('#panNumber').on('blur', function() {
            var pan = $(this).val().toUpperCase(); // convert to uppercase
            $(this).val(pan); // update input with uppercase value

            if (!isValidPAN(pan)) {
                $('#panError').text('Invalid PAN format');
                $('#buttonset').hide();
            } else {
                $('#panError').text('');
                $('#buttonset').show();
            }
        });

        function isValidTAN(tan) {
            var regex = /^[A-Z]{4}[0-9]{5}[A-Z]{1}$/;
            return regex.test(tan);
        }

        $('#tanNumber').on('blur', function() {
            var tan = $(this).val().toUpperCase(); // convert to uppercase
            $(this).val(tan); // update input with uppercase value

            if (!isValidTAN(tan)) {
                $('#tanError').text('Invalid TAN format');
                $('#buttonset').hide();
            } else {
                $('#tanError').text('');
                $('#buttonset').show();
            }
        });

        function isValidGST(gst) {
            const regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/;
            return regex.test(gst);
        }

        $('#gstNumber').on('blur', function() {
            const gst = $(this).val().toUpperCase();
            $(this).val(gst);

            if (!isValidGST(gst)) {
                $('#gstError').text('Invalid GST Number format.');
                $('#buttonset').hide();
            } else {
                $('#gstError').text('');
                $('#buttonset').show();

            }
        });


        const statusFlags = {
            auditcertificate: <?= json_encode($statusflag_auditcertificate) ?>,
            authorityofaudit: <?= json_encode($statusflag_authorityofaudit) ?>,
            genesisofaudit: <?= json_encode($statusflag_GenesisofAudit) ?>,
            accountdetails: <?= json_encode($statusflag_AccountDetails) ?>,
            pantan: <?= json_encode($statusflag_pantan) ?>,
            serious_storeslip: <?= json_encode($Serstatusflag_storeslip) ?>,
            nonserious_storeslip: <?= json_encode($NonSerstatusflag_storeslip) ?>,
            annextures: <?= json_encode($Annexturestatus) ?>,

        };
        localStorage.setItem('statusFlags', JSON.stringify(statusFlags));

        $(document).ready(function() {
            let lang = getLanguage('');
            labelLanguage(lang);


            $('#translate').change(function() {
                lang = getLanguage('Y');
                labelLanguage(lang);

                //       const $activeSpan = $('.parta_contents .activatestep span');
                // const stepheading = $activeSpan.data(lang) || $activeSpan.text();
                // $('.step-header').html(stepheading);
            });
        });


        function labelLanguage(lang) {
            $('.label_lang').each(function() {
                const newText = $(this).data(lang);
                if (newText) {
                    $(this).text(newText);
                }
            });
        }

        /* RemoveTempFile();

         function RemoveTempFile()
         {
             var filename = localStorage.getItem('filename');
             if (filename)
             {
                 // Send AJAX request to the backend
                 $.ajax({
                     url: '/delete-file', // The route to handle file deletion
                     method: 'POST', // Use POST request
                     data:
                     {
                         _token: '{{ csrf_token() }}', // Include CSRF token for security
                         fileName: filename // Send the filename to the controller
                     },
                     success: function(response)
                     {
                         // Handle success
                         if (response.success)
                         {
                             localStorage.removeItem('filename');
                         } else
                         {
                             // alert('Error: ' + response.error);
                         }
                     },
                     error: function(xhr, status, error)
                     {
                         // Handle error
                         //alert('An error occurred: ' + error);
                     }
                 });
             } else
             {
                         //alert('Filename is empty');
             }
         }*/


        $(document).on('input', '.numberswithdecimal', function() {
            var inputValue = $(this).val();
            var decimalIndex = inputValue.indexOf('.');

            if (decimalIndex !== -1) {
                // Limit digits after decimal to 2
                var integerPart = inputValue.substring(0, decimalIndex + 1);
                var decimalPart = inputValue.substring(decimalIndex + 1, decimalIndex + 3);
                $(this).val(integerPart + decimalPart);
            }
        });

        function restrictSpecialChars(selector) {
            $(selector)
                .off("keypress paste")
                .on("keypress", function(event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;


                    if (!/[0-9.]/.test(char)) {
                        event.preventDefault();
                    }

                    // Prevent more than one dot
                    if (char === '.' && value.includes('.')) {
                        event.preventDefault();
                    }

                    // Prevent dot as the first character
                    if (char === '.' && value.length === 0) {
                        event.preventDefault();
                    }

                    // Prevent more than 2 digits after the decimal
                    if (value.includes('.')) {
                        let parts = value.split('.');
                        if (parts[1].length >= 2 && this.selectionStart > value.indexOf('.')) {
                            event.preventDefault();
                        }
                    }

                })
                .on("paste", function(e) {
                    e.preventDefault();
                    let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                    let cleanData = '';

                    // Remove all but digits and dots
                    cleanData = pasteData.replace(/[^0-9.]/g, '');

                    // Allow only one dot
                    let dotIndex = cleanData.indexOf('.');
                    if (dotIndex !== -1) {
                        // Keep only the first dot and remove others
                        cleanData = cleanData.substring(0, dotIndex + 1) +
                            cleanData.substring(dotIndex + 1).replace(/\./g, '');
                    }

                    // Trim to 2 decimals max
                    if (cleanData.includes('.')) {
                        let [intPart, decPart] = cleanData.split('.');
                        decPart = decPart.substring(0, 2); // limit to 2 decimals
                        cleanData = intPart + '.' + decPart;
                    }


                    // Insert clean data at cursor position
                    let input = e.target;
                    let start = input.selectionStart;
                    let end = input.selectionEnd;
                    let original = input.value;
                    input.value = original.substring(0, start) + cleanData + original.substring(end);
                    input.setSelectionRange(start + cleanData.length, start + cleanData.length);
                });
        }
        $(document).on('input', 'input[name^="bank_account_number"]', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
        });
    </script>
@endsection
