@section('content')
    @extends('index2') @include('common.alert')
    <?php
    // $instdel = json_decode($inst_details, true);
    // $getmajorobjection = json_decode($get_majorobjection, true);
    ?>
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fieldaudit.css') }}">


    <?php
    $sessionchargedel = session('charge');
    $sessionuserdel = session('user');
    //$rejoinderlimit = 1;
    $rejoinderlimit = $configdatas[0]->pararejoinderlimit;
    $maxfileuploadcount = $configdatas[0]->parafilecount;
    $PSA_ADroleactioncode = $PUADroleactioncode;
    $PSA_roleactioncode = $PUroleactioncode;
    
    $roleTypeCode = $sessionchargedel->roletypecode;
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $sesroleactioncode = $sessionchargedel->roleactioncode;
    $sessionuserid = $sessionuserdel->userid;
    
    $ensessionuserid = $session_userid;
    $financialauditcode = $typeofauditData->first()->typeofauditcode ?? '';
    
    $fileuploadcount = $configdatas[0]->parafilecount;
    $liabilitylimit = 5;
    
    $paraid = $paraid_decrypt;
    
    $paradetails = $paradetails;
    
    $paradetails = $paradetails['data'];
    $paradet = json_decode($paradetails, true);
    $para_details = $paradet[0];
    
    $lastactiontaken = !empty($para_details['lastactionmonth']) ? 'Y' : 'N';
    $month = isset($para_details['lastactionmonth']) ? $para_details['lastactionmonth'] : '';
    $yearselected = isset($para_details['lastactionyear']) ? $para_details['lastactionyear'] : '';
    $showaction_div = isset($para_details['lastactionmonth']) ? '' : 'hide_this';
    
    $months = $months;
    $currentYear = date('Y');
    $endretirementYear = $currentYear + 20;
    $endYear = $currentYear - 1; // exclude current year
    $startYear = $endYear - 4;
    
    $yearofaudit_arr = json_decode($yearofaudit, true);
    $count_audityear = count($yearofaudit_arr);
    $showyear = $para_details['paratype'] == $lagacyparatype ? '' : 'hide_this';
    $shownormalyear = $para_details['paratype'] == $normalparatype ? '' : 'hide_this';
    $paratype = $para_details['paratype'];
    if ($sesroleactioncode == $PUADroleactioncode) {
        $buttonname = 'Approve';
    } else {
        $buttonname = 'Forward';
    }
    
    if ($sesroleactioncode == $PUroleactioncode) {
        $remarks_name = 'Remarks by PSA Auditor';
    } else {
        $remarks_name = 'Remarks by PSA AD';
    }
    ?>


    <style>
        .vr-start {
            border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
            box-shadow: -2px 0 0px rgba(115, 12, 12, 0.05);
            padding-left: 12px;
        }

        .ck-editor__editable {
            max-height: 500px;
            /* Set the max height as per your requirement */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }

        .auditee_div {
            background-color: #dfdb97 !important
        }

        .auditor_body {
            background-color: rgb(247, 245, 219) !important;
        }


        .auditee_body {
            background-color: #f3f3db !important
        }

        .auditor_body {
            background-color: #f8f3ff !important
        }

        .ad_body {
            background-color: #dff2ff !important
        }


        .auditor_div {
            background-color: #e1cdf4 !important
        }

        .ad_div {
            background-color: #80bae3 !important
        }
    </style>
    <div class="row">

        @if ($count_audityear)

            <form id="para_form" name="para_form">
                @csrf
                <div class="col-12">
                    <div class="spinner-wrapper " id="loader">
                        <div class="dot-spinner">
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                            <div class="dot-spinner__dot"></div>
                        </div>
                    </div>
                    <div class="card card_border">
                        <div class="card-header card_header_color">
                            Audit Para Management System
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <input type="hidden" name="instid" id="instid"
                                    value="{{ $instData->encrypted_instid ?? '' }}">
                                <input type="hidden" name="currentslipnumber" id="currentslipnumber" value='1'>
                                <input type="hidden" name="followupid" id="followupid" value="">
                                <input type="hidden" name="paraid" id="paraid" value='<?php echo $paraid; ?>'>
                                <input type="hidden" name="paranumber" id="paranumber" value=''>
                                <input type="hidden" name="ens" id="ens" value='<?php echo $ensessionuserid; ?>'>
                                <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                                <input type="hidden" name="active_fileid" id="active_fileid">
                                <input type="hidden" name="filter" id="filter" value='A'>
                                <input type="hidden" name="seriesno" id="seriesno" value='1'>
                                <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                                <input type="hidden" name="active_fileid" id="active_fileid">
                                <input type="hidden" name="processcode" id="processcode"
                                    value={{ $paradet[0]['processcode'] }}>
                                <input type="hidden" name="rejoindercycle" id="rejoindercycle" value="">
                                <input type="hidden" name="rejoinderstatus" id="rejoinderstatus" value="">
                                <input type="hidden" name="paratype" id="paratype" value='<?php echo $paratype; ?>'>
                                <input type="hidden" name="liabilityval" id="liabilityval" value="">
                                <input type="hidden" name="deleted_liabilityid" id="deleted_liabilityid" value=''>
                                <input type="hidden" name="liabilityid" id="liabilityid" value=''>

                                <input type="hidden" name="rejectcount" id="rejectcount" value="">



                                {{--
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required lang" key="inst" for="catname">Institution
                                    </label>
                                    <input type="text" class="form-control lang_value" id="instname" name="instname"
                                        data-en="{{ $instData->instename ?? '' }}"
                                        data-ta="{{ $instData->insttname ?? '' }}" value="{{ $instData->instename ?? '' }}"
                                        disabled>
                                </div> --}}

                                <div class="col-md-3 mb-3">
                                    <label class="form-label required lang" for="catname" key="category">Institution
                                        Category</label>
                                    <input type="text" class="form-control lang_value" id="catname" name="catname"
                                        data-en="{{ $catData->catename ?? '' }}" data-ta="{{ $catData->cattname ?? '' }}"
                                        value="{{ $catData->catename ?? '' }}" disabled>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label required lang" for="subcatname" key="sub_head">Institution
                                        Sub
                                        Category</label>
                                    <input type="text" class="form-control lang_value" id="subcatname"
                                        name="subcatname"
                                        data-en="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                        data-ta="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcattname : $catData->cattname }}"
                                        value="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                        disabled>
                                </div>




                                <div class="col-md-3 mb-3 ">
                                    <label class="form-label lang required"
                                        key="{{ $instData->deptcode == '01' ? 'fasil' : 'audityear_label' }}"
                                        for="validationDefault01">{{ $instData->deptcode == '01' ? 'Fasli Year' : 'Audit Year' }}</label>
                                    <input type="hidden" id="" name="" value="">
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="n_yearcode"
                                        name="n_yearcode[]" disabled multiple>


                                        @foreach ($normalaudityear as $year)
                                            <option value="{{ $year->auditperiodid }}"
                                                @if (in_array($year->auditperiodid, json_decode($para_details['audityear']) ?? [])) selected @endif>
                                                {{ $year->audit_period }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-3 mb-3">
                                    <label class="form-label lang required" key="parano" for="validationDefault01">Para
                                        Number</label>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="gen_parano"
                                        name="gen_parano" disabled>
                                        <option value="{{ $paradet[0]['paranumber'] }}">{{ $paradet[0]['paranumber'] }}
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <div class="row status_row hide_this" id="status_row">
                                <div class="col-md-3 status offset-md-9 d-flex justify-content-end">
                                    <span class="status-badge text-light rounded-pill" id="status_msg">Entry</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2 mb-3 {{ $showyear }}">
                                    <label class="form-label lang required" key="typeofpara"
                                        for="validationDefault01">Type of
                                        Para</label>
                                    <input type="hidden" id="" name="" value="" disabled>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="typeofparacode"
                                        name="typeofparacode" disabled>
                                        <option value="" data-name-en="--Select Type of Para--" data-name-ta="">
                                            --Select Type of Para--
                                        </option>
                                        @foreach ($typeofpara as $type)
                                            <option value="{{ $type->typeofparacode }}"
                                                @if ($para_details['typeofparacode'] == $type->typeofparacode) selected @endif
                                                data-name-en="{{ $type->typeofparaename }}"
                                                data-name-ta="{{ $type->typeofparatname }}">
                                                {{ $type->typeofparaename }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-2 mb-3 {{ $showyear }}">
                                    <label class="form-label lang required" key="stateofpara"
                                        for="validationDefault01">Current State of
                                        Para</label>
                                    <input type="hidden" id="" name="" value="" disabled>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="stateofparacode"
                                        name="stateofparacode" disabled>
                                        <option value="" data-name-en="--Select Current State of Para--"
                                            data-name-ta="">--Select Current State of
                                            Para--
                                        </option>
                                        @foreach ($stateofpara as $state)
                                            <option value="{{ $state->stateofparacode }}"
                                                @if ($para_details['stateofparacode'] == $state->stateofparacode) selected @endif
                                                data-name-en="{{ $state->stateofparaename }}"
                                                data-name-ta="{{ $state->stateofparatname }}">
                                                {{ $state->stateofparaename }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang required" key="" for="validationDefault01">Para
                                        Number</label>
                                    <input id="parano" name="parano" class="form-control only_numbers"
                                        value="{{ $para_details['paranumber'] }}" maxlength="10" disabled>

                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang required" key="statusflag"
                                        for="validationDefault01">Status</label>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="actiontaken"
                                        name="actiontaken" onchange="onchange_actiontaken('','','')" disabled>
                                        <option value="" data-name-en="--Select Action Taken--" data-name-ta="">
                                        </option>

                                        <option value="Y" data-name-en="Action taken" data-name-ta="Action taken"
                                            <?= $lastactiontaken == 'Y' ? 'selected' : '' ?>>
                                            Action taken
                                        </option>
                                        <option value="N" data-name-en="Action not taken"
                                            data-name-ta="Action not taken"
                                            <?= $lastactiontaken == 'N' ? 'selected' : '' ?>>
                                            Action not taken
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 {{ $showaction_div }}" id="actiontaken_div">
                                    <label class="form-label lang required" key="last_actiontaken" for="dropdown1">Last
                                        action
                                        taken</label>
                                    <div class="d-flex gap-2">
                                        <!-- First dropdown - takes 2/3 of space -->
                                        <select class="form-select flex-grow-1" name="lastactionmonth"
                                            id="lastactionmonth" disabled>
                                            <option value="">Select Month</option>
                                            @foreach ($months as $num => $name)
                                                <option value="{{ $num }}"
                                                    @if (old('month', $month ?? '') == $num) selected @endif>
                                                    {{ $name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <!-- Second dropdown - takes 1/3 of space -->
                                        <select class="form-select" style="width: 150px;" name="lastactionyear"
                                            id="lastactionyear" disabled>
                                            <option value="">Year</option>

                                            @for ($year = $startYear; $year <= $endYear; $year++)
                                                <option value="{{ $year }}"
                                                    @if (old('yearselected', $yearselected ?? '') == $year) selected @endif>
                                                    {{ $year }}</option>
                                            @endfor

                                        </select>
                                    </div>
                                </div>

                                {{-- </div>

                            <div class="row"> --}}
                                <div class="col-md-4">
                                    <label class="form-label required lang" for="validationDefaultUsername"
                                        key="major_obj">Title/Heading</label>
                                    <select class="form-control select2" id="mainobjectionid" name="mainobjectionid"
                                        onchange="getminorobjection('','')" disabled>
                                        <option value="" data-name-en="---Select Title---"
                                            data-name-ta="---தலைப்பைத் தேர்ந்தெடுக்கவும்---">
                                            ---Select
                                            Title---
                                        </option>
                                        @foreach ($objectionData as $mainobjection)
                                            <option value="{{ $mainobjection->mainobjectionid }}"
                                                data-name-en="{{ $mainobjection->objectionename }}"
                                                data-name-ta="{{ $mainobjection->objectiontname }}">
                                                {{ $mainobjection->objectionename }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4 ">
                                    <label class="form-label required lang" for="validationDefaultUsername"
                                        key="minor_obj">Categorization
                                        of Paras
                                    </label>
                                    <select class="select form-control  select2" id="subobjectionid"
                                        name="subobjectionid" disabled>

                                    </select>
                                </div>
                                <div class="col-md-2 mb-2"> <label class="form-label lang"
                                        for="validationDefaultUsername" key="amount_involved">Amount
                                        Involved</label> <input type="text"
                                        class="form-control removesplchar_numberwithdecimal numberswithdecimal"
                                        id="amount_involved" name="amount_involved" maxlength="7" disabled>
                                </div>
                                <div class="col-md-2 ">
                                    <label class="form-label required lang" for="validationDefaultUsername"
                                        key="severity">Severity</label>
                                    <select class="select form-control custom-select" id="severityid" name="severityid"
                                        disabled>
                                        <!-- <option value="">@lang('Select Severity')</option> -->
                                        {{--
                                                         <option value="" data-en="Select Severity" data-ta="தெரிவு கடைசியாக"></option> --}}
                                        <option value="" data-name-en="---Select Severity---"
                                            data-name-ta="---தெரிவு கடைசியாக தேர்ந்தெடுக்கவும்---">
                                            ---Select
                                            Severity---</option>

                                        @foreach ($severities as $key => $severity)
                                            <option value="{{ $key }}"
                                                @if ($para_details['severitycode'] == $key) selected @endif
                                                data-en="{{ $severity['en'] }}" data-ta="{{ $severity['ta'] }}">
                                                {{ $severity['en'] }}
                                                <!-- Default language text, can be changed dynamically -->
                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                                {{-- </div>

                            <div class="row mt-2"> --}}
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-5 mb-2">
                                            <label class="form-label required lang" key="scheme">Scheme</label> <br>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio" name="scheme"
                                                        id="Y" value="Y" onchange="enable_schemename('Y')"
                                                        disabled>
                                                    <label class="form-check-label lang" for="Y"
                                                        key="yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio" name="scheme"
                                                        id="N" value="N" onchange="enable_schemename('N')"
                                                        checked disabled>
                                                    <label class="form-check-label lang" for="N"
                                                        key="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-12 col-md-6 "id="">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Name</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="liabilityname" name="liabilityname"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        placeholder="Enter Liability name">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                        <div class="col-md-7 mb-2 hide_this" id="severityDiv">
                                            <label class="form-label required lang" key="schemename">Scheme
                                                Name</label>
                                            <select class="select form-control custom-select lang-dropdown"
                                                id="schemename" name="schemename" disabled>
                                                <option value='' data-name-en="Select Scheme Name"
                                                    data-name-ta="தெரிவு கடைசியாக">
                                                </option>
                                                @if (!empty($schemename) && count($schemename) > 0)
                                                    @foreach ($schemename as $s)
                                                        <option value="{{ $s->auditeeschemecode }}"
                                                            data-name-en="{{ $s->auditeeschemeelname }}"
                                                            data-name-ta="{{ $s->auditeeschemetlname }}">
                                                            {{ $s->auditeeschemeelname }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option disabled data-name-en="No Scheme Available"
                                                        data-name-ta="எதுவும் இல்லை">
                                                        No Scheme Available
                                                    </option>
                                                @endif
                                            </select>
                                        </div>


                                    </div>

                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label required lang " key="serious"
                                        for="validationDefaultUsername" key="">Irregularities</label>
                                    <select class="select form-control custom-select lang-dropdown" id="serious"
                                        name="serious" onchange="getcategoryBasedOnSerious('','')" disabled>
                                        <!-- <option value="">@lang('Select Severity')</option> -->
                                        <option value='' data-name-en="Select Serious"
                                            data-name-ta="தீவிரமானதைத் தேர்ந்தெடுக்கவும்">
                                        </option>
                                        @if (!empty($serious) && count($serious) > 0)
                                            @foreach ($serious as $s)
                                                <option value="{{ $s->irregularitiescode }}"
                                                    @if ($para_details['irregularitiescode'] == $s->irregularitiescode) selected @endif
                                                    data-name-en="{{ $s->irregularitieselname }}"
                                                    data-name-ta="{{ $s->irregularitiestlname }}">
                                                    {{ $s->irregularitieselname }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option disabled data-name-en="No serious Available" data-name-ta="">
                                                No serious Available
                                            </option>
                                        @endif

                                    </select>


                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label required lang" key="category"
                                        for="validationDefaultUsername">Category</label>
                                    <select class="select form-control custom-select lang-dropdown" id="category"
                                        name="category" onchange="getsubcategoryBasedOnCategory('','')" disabled>
                                        <!-- <option value="">@lang('Select Severity')</option> -->
                                        <option value="" data-name-en="Select Category"
                                            data-name-ta="வகையைத் தேர்ந்தெடுக்கவும்">Select
                                            Category
                                        </option>

                                        <option value="" disabled id=""
                                            data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">No
                                            Category
                                            Available
                                        </option>


                                    </select>


                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label required lang " for="validationDefaultUsername"
                                        key="if_subcategory">Sub
                                        Category</label>
                                    <select class="select form-control custom-select lang-dropdown" id="subcategory"
                                        name="subcategory" disabled>
                                        <!-- <option value="">@lang('Select Severity')</option> -->
                                        <option value="" data-name-en="Select SubCategory"
                                            data-name-ta="உபவகை தேர்ந்தெடுக்கவும்">
                                            ---Select
                                            SubCategory
                                        </option>


                                    </select>


                                </div>




                                {{-- </div>

                            <div class="row mt-2  "> --}}
                                <div class="col-md-4 mb-2"> <label class="form-label required lang"
                                        for="validationDefaultUsername" key="paradetails">Para
                                        Details</label>
                                    <textarea id="slipdetails" maxlength="500" name="slipdetails" class="form-control text_special" disabled>{{ $para_details['slipdetails'] }}</textarea>
                                </div>


                                <div class="col-md-4 mb-2">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="form-label required lang" for="validationDefaultUsername"
                                                key="liability">
                                                Liablility</label> <br>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="liability" id="Y" value="Y"
                                                        onchange="enable_liability('Y')" disabled>
                                                    <label class="form-check-label lang" for="all"
                                                        key="yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="liability" id="N" value="N" checked
                                                        onchange="enable_liability('N')" disabled>
                                                    <label class="form-check-label lang" for="district"
                                                        key="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6" id="auditee_liability_type_div">
                                            <label class="form-label required lang" for="auditee_liability_type">Liability
                                                Type</label>
                                            <select class="form-select form-select-sm" id="auditee_liability_type"
                                                name="auditee_liability_type">
                                                <option value="non_money_involved">Non Money Involved</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div id="dynamicRowsContainer">
                            </div>

                            <div class="row">


                                <div class="col-md-12 ">
                                    <label class="form-label required lang" for="validationDefaultUsername"
                                        key="paraobservation">
                                        Para Observation/Remarks</label>
                                    <textarea id="remarks" class="form-control" placeholder="Enter remarks" name="remarks" disabled></textarea>
                                </div>

                                <div class="col-md-12 p-6">
                                    <label class="form-label  lang para_attach" for="validationDefaultUsername"
                                        key="attachments">Attachments </label>


                                    <div class="d-flex flex-row">
                                        <div class="position-relative d-flex flex-row" id="file-lagacylist-container">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div id="historydiv">

                            </div>
                            <div class="accoriondiv" id="accoriondiv">

                            </div>

                            <div class="row" id="present_entry">

                                <div class="col-md-9" id="">
                                    <label class="form-label required" for="">
                                        {{ $remarks_name }} </label>
                                    <textarea id="auditeeremarks" class="form-control " placeholder="Enter remarks" name="auditeeremarks"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label  lang required" for="validationDefaultUsername"
                                        key="action">Action
                                    </label>
                                    <select class="select2 form-control custom-select lang-dropdown" id="actioncode"
                                        name="actioncode" onchange="onchange_actiononpara('','')">
                                        <option value="" data-name-en="--Select Action to be taken --"
                                            data-name-ta="எடுக்க வேண்டிய நடவடிக்கையைத் தேர்ந்தெடுக்கவும்.">
                                            --Select Action to be taken--
                                        </option>
                                        @foreach ($actiondata as $act)
                                            <option value="{{ $act->actioncode }}"
                                                data-name-en="{{ $act->actionename }}"
                                                data-name-ta="{{ $act->actiontname }}">
                                                {{ $act->actionename }}
                                            </option>
                                        @endforeach
                                        <select>
                                </div>
                                <div class="col-md-12 p-6">
                                    <label class="form-label  lang file_attach" for="validationDefaultUsername"
                                        key="attachments">Attachments </label>
                                    <span style="color:#ff0000; font-size:10px;">(Max Size : 3 MB &
                                        File
                                        Format
                                        : Pdf )</span>


                                    <div class="row mb-2">


                                        <div class="col-md-12 d-flex align-items-center">
                                            <div class="file-input-container" id="file-input-container">
                                                <div id="file-list" class="d-flex flex-wrap gap-2">
                                                </div>
                                                <template id="file-preview">
                                                    <div class="position-relative text-center file-preview"
                                                        style="width: 120px;">
                                                        <div class="img-thumbnail">
                                                            <img src="" height="100" />
                                                            <div class="position-absolute text-white fs-1 text-wrap bg-dark p-1 small w-100"
                                                                style="bottom: 0; left: 0; opacity: 0.8;">
                                                                <span class="file-name">File
                                                                    Name</span> |
                                                                <span class="file-size">0 KB</span>
                                                            </div>
                                                            <button style="top: 5px; right: 5px;"
                                                                class="btn btn-sm btn-danger position-absolute"
                                                                onclick="removeFilePreview(this)">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <input class="d-none" multiple="multiple" type="file"
                                                            name="fileupload[]" accept=".pdf">
                                                    </div>
                                                </template>
                                            </div>


                                            <label for="upload_input" id="add-file-btn"
                                                class="btn btn-success btn-sm ms-2 ">
                                                <i class="fs-6 ti ti-plus"></i>
                                            </label>
                                            <input id="upload_input" type="file" name="fileupload[]" class="d-none"
                                                multiple="multiple"
                                                onchange="window.breakIntoSeparateFiles(this, '#file-list', '#file-preview')" />
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <div class="position-relative d-flex flex-row" id="file-list-container">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-6" style="margin-left: 39% !important;">
                                        <div class="d-flex align-items-center gap-6" id="button_set">
                                            <input type="hidden" id="action" name="action" value="insert">
                                            <button class="btn button_save" id="buttonaction" name="buttonaction">Save
                                                Draft
                                            </button>
                                            <button class="btn button_rejoinder hide_this lang button-container"
                                                id="rejoinder_btn" key="rejoinderbtn"
                                                name="rejoinder_btn">Rejoinder</button>
                                            <button class="btn bg-success text-light hide_this lang button-container"
                                                id="forward_btn" key="forward_btn" name="forward_btn">Forward</button>
                                            <button class="btn bg-danger text-light hide_this lang button-container"
                                                id="reject_btn" key="reject_btn" name="reject_btn">Reject</button>
                                            <button class="btn button_finalise"
                                                id="finalisebtn"><?php echo $buttonname; ?></button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </form>
        @else
            <div class="card " style="border-color: #7198b9">
                <div class="card-header card_header_color">Lagacy Details</div>
                <div class="card-body">
                    <br>
                    <center>No Data Available</center>
                </div>
            </div>
        @endif
    </div>
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/apps/chat.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/forms/select2.init.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


    <script>
        let maxRow = 5;
        let sesroleactioncode = '<?php echo $sesroleactioncode; ?>';
        let sessionuserid = '<?php echo $sessionuserid; ?>';
        let PSA_ADroleactioncode = '<?php echo $PSA_ADroleactioncode; ?>';
        let PSA_roleactioncode = '<?php echo $PSA_roleactioncode; ?>';
        let rejoinderlimit = '<?php echo $rejoinderlimit; ?>';
        let maxfilecount = '<?php echo $maxfileuploadcount; ?>';
        let auditor_lastactioncode = '';
        let months = @json($months);
        let sessionroletypecode = '<?php echo $sessionroletypecode; ?>';
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            if (loader) {
                $('#loader').addClass('d-none');

            }
        });



        function fit_status(processcode, rejoinderstatus) {
            let status_msg = '';
console.log('fit_status:' + processcode)

            switch (processcode) {

                case 'E':
                    status_msg = 'STATUS :Entry'
                    break;
                case 'F':
                    status_msg = 'STATUS :Forwarded to PSA Auditor'
                    break;

                case 'K':
                    status_msg = 'STATUS :Forwarded to PSA AD'
                    break;
                case 'U':
                    status_msg = 'STATUS :Forwared to Auditee'
                    break;
                case 'A':
                    status_msg = 'STATUS :Dropped'
                    break;
                case 'I':
                    status_msg = 'STATUS :Rejected'
                    break;
                case 'PA':
                    status_msg = 'STATUS :Rejected'
                    break;
                case 'L':
                    status_msg = 'STATUS :Forwarded from Retirement Committee'
                    break;
                case 'R':
                    status_msg = 'STATUS :Forwarded to Retirement Committee'
                    break;
                case 'PR':
                    status_msg = 'STATUS :Forwared to Auditee(Part(s) Removal)'
                    break;
                default:
                    status_msg = 'STATUS :Entry';
                    break;

            }
            status_msg += ((rejoinderstatus == 'Y' && (processcode != 'A' && processcode != 'I')) ? ' - (Rejoinder)' : '')
            $('#status_msg').text(status_msg)
        }

        //to show action button
        function onchange_actiononpara(actioncode, rejoinderlimit_data) {

            var rejoinderlimit_data = rejoinderlimit_data || $('#rejoindercycle').val()

            if (sesroleactioncode == PSA_ADroleactioncode) {
                var actcode = actioncode || $('#actioncode').val()

                switch (String(actcode)) {
                    case '01':
                        $('#finalisebtn').show();
                        $('#rejoinder_btn').hide();
                        $('#reject_btn').hide();
                        $('#forward_btn').hide();

                        break;
                    case '02':
                        $('#reject_btn').show();
                        $('#finalisebtn').hide();
                        $('#rejoinder_btn').hide();
                        $('#forward_btn').hide();
                        break;
                    case '03':
                        $('#finalisebtn').hide();
                        $('#reject_btn').hide();
                        $('#forward_btn').hide();

                        (rejoinderlimit_data ?? 0) < (rejoinderlimit ?? 0) ?
                        $('#rejoinder_btn').show():
                            $('#rejoinder_btn').hide();

                        break;
                    case '11':

                        $('#forward_btn').show();
                        $('#finalisebtn').hide();
                        $('#rejoinder_btn').hide();
                        $('#reject_btn').hide();

                    case '12':

                        $('#forward_btn').show();
                        $('#finalisebtn').hide();
                        $('#rejoinder_btn').hide();
                        $('#reject_btn').hide();

                    default:

                        return;

                }

            }
        }
        /*****************************************************Fix Para Details ******************************************************************* */

        //to fix para lastaction taken data
        function onchange_actiontaken(val, selectedmonth, selectedyear) {

            var actiontaken = $('#actiontaken').val();
            if (actiontaken == 'Y') {
                $('#actiontaken_div').css('display', 'block');
            } else if (actiontaken == 'N' || ((selectedmonth == null || selectedmonth === '') &&
                    (selectedyear == null || selectedyear === ''))) {
                $('#lastactionmonth,#lastactionyear').val('');
                $('#lastactionmonth,#lastactionyear').select2();
                $('#actiontaken_div').css('display', 'none');

                $('#actiontaken').val('N')
                $('#actiontaken').select2();

            } else {
                $('#actiontaken_div').css('display', 'block');
                $('#actiontaken').val('Y')
                $('#actiontaken').select2();
            }
        }

        function enable_schemename(selectedOption) {
            if (selectedOption === 'Y') {
                $('#severityDiv').show();
            } else {
                $('#severityDiv').hide();
                $('#schemename').val('');

            }
        }


        //----------------------------onchange ---------------------------//
        function getcategoryBasedOnSerious(serious, selectedRegioncode = null) {

            const lang = getLanguage();
            const categoryDropdown = $('#category');

            categoryDropdown.html(`
            <option value="" data-name-en="Select Category Name" data-name-ta="வகை பெயரைத் தேர்ந்தெடுக்கவும்">
                ${lang === 'ta' ? 'வகை பெயரைத் தேர்ந்தெடுக்கவும்' : 'Select Category Name'}
            </option>
            `);

            if (serious == "") {
                var serious = $("#serious").val();

            }
            if (!serious) {
                categoryDropdown.append(`
                <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                </option>
            `);


                return;
            }
            if (serious) {
                $.ajax({
                    url: "/getcategoryBasedOnSerious",
                    type: "POST",
                    data: {
                        serious: serious,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(catcode => {
                                categoryDropdown.append(
                                    `<option value="${catcode.irregularitiescatcode}"
                                     data-name-en="${catcode.irregularitiescatelname}"
                                      data-name-ta="${catcode.irregularitiescattlname}"
                                     ${catcode.irregularitiescatcode === selectedRegioncode ? 'selected' : ''}>
                                    ${lang === 'ta' ? catcode.irregularitiescattlname : catcode.irregularitiescatelname }</option>`
                                );
                            });
                        } else {
                            categoryDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
                        }
                    },
                    error: function() {
                        alert('Error fetching region. Please try again.');
                    }
                });
            }

        }


        function getsubcategoryBasedOnCategory(category, selectedRegioncode = null) {

            const subcategoryDropdown = $('#subcategory');

            const lang = getLanguage();
            subcategoryDropdown.html(`
                <option value="" data-name-en="Select SubCategory" data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select SubCategory'}
                </option>
            `);


            if (category == "") {
                var category = $("#category").val();

            }
            if (!category) {
                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `); //  districtDropdown.append('<option value="" disabled>No District Available</option>');
                // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');


                return;
            }
            if (category) {
                $.ajax({
                    url: "/getsubcategoryBasedOnCategory",
                    type: "POST",
                    data: {
                        category: category,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(subcategory => {
                                subcategoryDropdown.append(
                                    `<option value="${subcategory.irregularitiessubcatcode}"
                                     data-name-en="${subcategory.irregularitiessubcatelname}"
                                    data-name-ta="${subcategory.irregularitiessubcattlname}"
                                    ${subcategory.irregularitiessubcatcode === selectedRegioncode ? 'selected' :''}>
                                    ${lang === 'ta' ? subcategory.irregularitiessubcattlname : subcategory.irregularitiessubcatelname }</option>`
                                );
                            });
                        } else {
                            subcategoryDropdown.append(`
                            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                            </option>
                        `);
                        }
                    },
                    error: function() {
                        // alert('Error fetching region. Please try again.');
                        subcategoryDropdown.append('<option disabled>No Subcategory Available</option>');

                    }
                });
            }

        }

        function getminorobjection(mainobjectionid = '', subobjectionid = '') {
            appendFilePreview();
            var mainobjectionid = mainobjectionid || $('#mainobjectionid').val();



            $.ajax({
                url: '/followup/getminordet', // Your API route to get user details
                method: 'POST',
                data: {
                    mainobjectionid: mainobjectionid,

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var data = response.minorobjectionData;
                    const lang = getLanguage('')

                    $('#subobjectionid').empty();
                    $('#subobjectionid').append(
                        '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">Select Category</option>'
                    );
                    $.each(data, function(index, minorobjection) {

                        var isSelected = minorobjection.subobjectionid === subobjectionid ?
                            'selected' :
                            '';
                        $('#subobjectionid').append(
                            '<option value="' + minorobjection.subobjectionid + '"' +

                            ' data-name-en="' + minorobjection.subobjectionename + '"' +
                            ' data-name-ta="' + minorobjection.subobjectiontname + '" ' +
                            isSelected + '>' +
                            (lang == "en" ? minorobjection.subobjectionename : minorobjection
                                .subobjectiontname) +
                            '</option>'
                        );
                    });



                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        /***************************************************** File - upload template ******************************************************************* */


        var rowCount = 0; // To keep track of the number of rows added

        var fileCount = 0;

        function removeFilePreview(button) {

            fileCount--;
            $(button).closest('.file-preview').remove();
            if (fileCount >= maxfilecount) {
                $('#add-file-btn').hide();
            } else {
                $('#add-file-btn').show();
            }
        }

        (function(window, $) {
            var FILE_ICON_URL = "{{ asset('assets/images/file.png') }}";
            var ALLOWED_TYPES = ['application/pdf',
                // 'image/jpeg', 'image/png',
                // 'application/vnd.ms-excel',
                // 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            var MAX_FILE_SIZE_MB = 2;
            var MAX_FILE_COUNT = '<?php echo $maxfileuploadcount; ?>';

            function addFileToNewInput(file, newInput) {
                if (!newInput) {
                    return;
                }

                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                newInput.files = dataTransfer.files;
            }

            function setFileDetails(file, $previewElement) {
                // Populate file name
                $previewElement.find('.file-name').text(file.name);
                // Populate file size in KB with 2 decimal precision
                var fileSizeInKB = (file.size / 1024).toFixed(2) + ' KB';
                $previewElement.find('.file-size').text(fileSizeInKB);
            }

            function addSrcToPreview(file, preview) {
                if (!preview) {
                    return;
                }

                if (file.type.match(/image/)) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    $('.file-preview').show();
                } else {
                    preview.src = FILE_ICON_URL;
                    $('.file-preview').show();
                }

            }

            function showError(message, $previewElement) {
                var $errorElement = $previewElement.find('.file-error');
                if ($errorElement.length === 0) {
                    $errorElement = $('<div class="file-error" style="color: red; font-size: 12px;"></div>')
                        .appendTo(
                            $previewElement);
                }
                $errorElement.text(message);
            }


            function breakIntoSeparateFiles(input, targetSelector, templateSelector) {
                $('#file-input-container').show();
                var $input = $(input);
                var templateHtml = $(templateSelector).html();

                if (!input.files) {
                    return;
                }

                var existingFileCount = $(targetSelector).children().length; // Existing previews

                var newFiles = Array.from(input.files); // Convert FileList to array for easier processing
                var validFiles = [];
                var dataTransfer = new DataTransfer();

                // Validate files before appending
                newFiles.forEach(file => {
                    // Check file type
                    if (!ALLOWED_TYPES.includes(file.type)) {
                        // alert('Invalid file type. Please upload PDF, Excel, JPG, JPEG, or PNG files only.');
                        alert('Invalid file type. Please upload PDF');
                        return; // Don't increment fileCount here
                    }

                    // Check file size
                    if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                        alert('File size exceeds the 2MB limit.');
                        return; // Don't increment fileCount here
                    }


                    // Ensure total files do not exceed the limit
                    if (validFiles.length + fileCount >= MAX_FILE_COUNT) {

                        alert('Max file limit reached.');
                        return; // Don't increment fileCount here
                    }

                    dataTransfer.items.add(file);

                    validFiles.push(file); // Only add to validFiles if passed validation
                });
                input.files = dataTransfer.files;
                // If no valid files, exit
                if (validFiles.length === 0) {
                    return;
                }

                // Append valid files and process them
                validFiles.forEach(file => {
                    var $newFile = $(templateHtml).appendTo(targetSelector);

                    // Process the file (async handling)
                    processFile(file, $newFile).then(() => {
                        existingFileCount++; // Update count only after successful processing

                        // Hide or show the "Add File" button based on the limit
                        if (validFiles.length + fileCount >= MAX_FILE_COUNT) {
                            // $('#add-file-btn').hide();
                        } else {
                            $('#add-file-btn').show();
                        }

                        // Increment fileCount only after successful processing
                        fileCount++;

                        if (fileCount >= MAX_FILE_COUNT) {

                            $('#add-file-btn').hide();
                        }

                    });
                });
                //processFile(file, $newFile)
                ; // Clear input to allow re-selection of the same files
                $input.val([]);
            }


            async function processFile(file, $newFile) {
                let imgElement = $newFile.find("img")[0];
                console.log($newFile.html());

                let fileInfoDiv = $newFile.find(".position-absolute.text-white")[0];
                // Hide file info initially
                fileInfoDiv.style.display = "none";

                // Set preview first
                await addSrcToPreview(file, imgElement);

                // Ensure preview is loaded first
                await new Promise((resolve) => {
                    imgElement.onload = resolve;
                });

                // Now update the file input and details
                await addFileToNewInput(file, $newFile.find("input")[0]);
                await setFileDetails(file, $newFile);

                // Show file info after everything is set
                fileInfoDiv.style.display = "block";
            }


            window.breakIntoSeparateFiles = breakIntoSeparateFiles;
        })(window, jQuery);

        function appendFilePreview() {
            $('#file-input-container').append('<div id="file-list" class="mb-2 d-flex"></div>');
            $('#file-input-container').append(`
            <template id="file-preview">
                <div class="position-relative mr-3 text-center file-preview" style="width: min-content;">
                    <div class="img-thumbnail">
                        <img src="" height="70" />
                        <div class="position-absolute text-white fs-40 text-wrap bg-dark p-1 small w-100" style="bottom: 0; left: 0; opacity: 0.8;">
                            <span class="file-name fs-40">File Name</span> |
                            <span class="file-size fs-40">0 KB</span>
                        </div>
                        <button style="top: 5px; right: 5px;" class="btn btn-sm btn-danger position-absolute" onclick="removeFilePreview(this)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input class="d-none" multiple="multiple" type="file" name="fileupload[]">
                </div>
            </template>`);
        }

        /***************************************************** File - upload template ******************************************************************* */



        //------------------------------dynamic append liability-----------------------------//
        function enable_liability(selectedOption) {
            if (selectedOption === 'Y') {
                // Show the textbox when "Yes" is selected
                // $("#" + liablilitynamedivid).show(); //liabilityname_div
                // $("#" + liablilitygpfdivid).show(); // liabilitygpfno_div
                $('#dynamicRowsContainer').show();
                const container = document.getElementById('dynamicRowsContainer');
                container.innerHTML = ''; // Clear current rows
                rowCount = 0; // Reset row count
                // addRow(param); // Add the first row based on the selected parameter
                addNewWorkRow(event, 'view', '', '', '', '', '', '', '');

            } else {
                $('#dynamicRowsContainer').hide();
                // Hide the textbox when "No" is selected
                // $("#" + liablilitynamedivid).hide(); //liabilityname_div
                // $("#" + liablilitygpfdivid).hide(); //liabilitygpfno_div
            }
        }


 function validateRemarksField($input) {

            let regex = /^[a-zA-Z\u0B80-\u0BFF ]*$/;
            let value = $input.val();

            let validator = $("#para_form").data("validator");

            if (!validator) {
                $("#para_form").validate({
                    errorClass: "text-danger",
                    errorElement: "small",
                    highlight: function(element) {
                        $(element).addClass("is-invalid");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("is-invalid");
                    }
                });

                validator = $("#para_form").data("validator");
            }

            if (!regex.test(value) && value !== "") {

                value = value.replace(/[^a-zA-Z\u0B80-\u0BFF ]/g, '');
                $input.val(value);

                validator.showErrors({
                    [$input.attr("name")]: "Enter Valid Remarks"
                });

                $input.addClass("is-invalid");

            } else {

                validator.showErrors({
                    [$input.attr("name")]: ""
                });

                $input.removeClass("is-invalid");
            }
        }




      function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, remarks, liabilityid, isLast,
            statusflag, processcode, retiredflag, retirementyear, retiremntmonth,lagacyflag) {
            // action = ((sesroleactioncode == PSA_ADroleactioncode) && (processcode == 'K')) ? 'entry' : action;

            let isChecked = (statusflag === 'Y') ? 'checked' : '';

            let isdisabled = (action === 'view') ? 'disabled' : '';

            let isremarks_disable = (statusflag === 'C' && (sesroleactioncode == PSA_roleactioncode) || ((
                sesroleactioncode == PSA_roleactioncode) && processcode == 'K')) ? 'disabled' : '';

            let ischeckbox_active = ((sesroleactioncode == PSA_ADroleactioncode) && (processcode == 'K')) ?
                '' : 'disabled';

            let isremarks_active = ((sesroleactioncode == PSA_roleactioncode) && (processcode == 'F') ||
                    (sesroleactioncode == PSA_ADroleactioncode) && (processcode == 'K')) ?
                '' : 'disabled';



            if (rowCount >= maxRow) {
                alert("Maximum row limit reached!");
                return;
            }

            let selectedOption = "";
            if (notype === "01") {
                selectedOption = "01"; // GPF No
            } else if (notype === "02") {
                selectedOption = "02"; // CPF No
            } else if (notype === "03") {
                selectedOption = "03"; // IFHRMS No
            }

            let newWorkRow = '';

            if (rowCount == 0) {
                newWorkRow += `<hr>`;
            }

            const currentYear = @json($currentYear);
            const endretirementYear = @json($endretirementYear);
            // action = 'entry'
            newWorkRow += `
                  <div class="row work-row p-2 border-bottom mb-3" id="row${rowCount}">
                      <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">
			<input type="hidden" id="lagacyflag${rowCount}" name="lagacyflag${rowCount}" value="${lagacyflag}">
                      <!-- Type Column -->
                      <div class="col-md-1 col-12 mb-2" >
                          <label class="form-label d-md-none d-block">Type</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block sm-2">Type</label>` : ''}
                          <select class="form-select" name="notype${rowCount}" value="${name}" ${isdisabled}>
                              <option value="">---Select Type---</option>
                              <option value="01" ${selectedOption === "01" ? "selected" : ""}>EPF No</option>
                              <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPS No</option>
                              <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
                          </select>
                      </div>

                      <!-- GPF Number Column -->
                      <div class="col-md-1 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Number</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block"> Number</label>` : ''}
                         <input type="text" class="form-control removesplchar_number alpha_numeric" maxlength="20" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="Number" ${isdisabled}>
                      </div>

                      <!-- Name Column -->
                      <div class="col-md-1 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Name</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Name</label>` : ''}
                          <input type="text" class="form-control removesplchar_text name" maxlength="50"  id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled}>
                      </div>

                      <!-- Designation Column -->
                      <div class="col-md-1 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Designation</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Designation</label>` : ''}
                          <input type="text" class="form-control removesplchar_text name" maxlength="50"  name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
                      </div>

                      <!-- Amount Column -->
                      <div class="col-md-1 col-12 mb-2 liability_amount">
                          <label class="form-label d-md-none d-block">Amount</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Amount</label>` : ''}
                          <input type="text" class=" form-control removesplchar_numberwithdecimal numberswithdecimal" name="amount${rowCount}" value="${amount}" placeholder="Amount" ${isdisabled}>
                      </div>
                    <!-- REtirement Flag -->
                 <div class="col-md-2 col-12 mb-2">

                         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Retirement</label>` : ''}
                         <select class="form-select  form-select-sm"
                                 name="retiredflag${rowCount}"
                                 id="retiredflag${rowCount}" ${isdisabled}
                                  onchange="onchange_retirementstate(this, ${rowCount})">
                           <option value="" data-name-en="--select--"
                                            data-name-ta="--select--" >--select--
                            </option>
                            <option value="L" data-name-en="Less then 3 Months"
                                            data-name-ta="Less then 3 Months">
                                            Less then 3 Months
                            </option>
                            <option value="M" data-name-en="3 to 6 Months"
                                            data-name-ta="3 to 6 Months">
                                          3 to 6 Months
                            </option>
                            <option value="H" data-name-en="More then 6 months"
                                            data-name-ta="More then 6 months">
                                          More then 6 months
                            </option>
                         </select>
                         <div class="vr-shadow"></div>
                    </div>


                    <!--Retirement Year Month-->
                    <div class="col-md-2 col-12 mb-2 " >
                             ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Retirement Details</label>` : ''}

                <div class="hide_this" id="retirementdetails_div_${rowCount}">
                       <div class="input-group" >
                            <select class="form-select retirementYear form-select-sm"
                             name="retirementyear${rowCount}"
                             id="retirementyear${rowCount}" ${isdisabled}  onchange="onchange_retirement_yr(this, ${rowCount},'')">
                                    <option value="">Year</option>
                            </select>

                              <select class="form-select retirementMonth form-select-sm"
                                      name="retirementmonth${rowCount}"
                                      id="retirementmonth${rowCount}" ${isdisabled}>
                                  <option value="">Month</option>
                            </select>
                        </div>
                    </div>
                    </div>

                       <!-- Remarks Column -->
                       <div class="col-md-2 mb-2 ms-auto text-end vr-start ps-3">
                         ${rowCount == 0  ? `<label class="form-label d-none d-md-block">Remarks</label>` : ''}
                        <div class="d-flex flex-md-row flex-column gap-1 justify-content-end">


           <textarea maxlength ="100" class="form-control removesplchar_text"  name="remarks${rowCount}" ${isremarks_active} style="height:38px">${remarks}</textarea>

                      </div>
                       </div>



                      <!-- Action Column -->
                        <div class="col-md-1 mb-2 action-row ms-auto text-end vr-start ps-3">
                            ${rowCount == 0 && ((sesroleactioncode === PSA_ADroleactioncode && (processcode=='K'|| processcode=='U'|| processcode=='PA')))  ? `<label class="form-label d-md-block d-none">Action</label>` : ''}

                            <div class="d-flex flex-md-row flex-column gap-1 justify-content-end">
                            ${rowCount > 0  && ((sessionroletypecode === 'I'  && processcode=='E'))&& (action === 'entry')? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <button type="button" class="mar_left btn btn-danger fw-medium deleteRowBtn" onclick="deleteRow(${rowCount})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="ti ti-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>` : ''}

                               ${rowCount > 0  && ((sessionroletypecode === 'I'  && processcode=='E'))&& (action === 'entry')? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <button type="button" class=" mar_left btn btn-success fw-medium addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '','', '', '', '', '','','${processcode}','','','')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <i class="ti ti-circle-plus"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         </button>` : ''}
                              ${ (sesroleactioncode ==PSA_ADroleactioncode && ['K','U','A','I','PA'].includes(processcode))? `
                                                                                                                                                                                                                                                                 <input type="checkbox" class="form-check-input warning" onchange ="onchange_liabilityactive(this, ${rowCount})" name="activestatus[]" ${isChecked} ${ischeckbox_active}>
                                                                                                                                                                                                                                                                 <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>` : ''}
                            </div>
                        </div>


                  </div>
                  `;
            //  ${ ((sesroleactioncode === PSA_ADroleactioncode))
            //   ? `




        $("#dynamicRowsContainer").append(newWorkRow);
        toggleAmountColumn();

        let $newTextarea = $("#dynamicRowsContainer")
    .find(".removesplchar_text")
    .last();

// validate existing value (important)
validateRemarksField($newTextarea);

// attach input event
$newTextarea.on("input", function () {
    validateRemarksField($(this));
});
        // Hide the add button in the previous row
        if (rowCount > 0) {
            $(`#row${rowCount - 1} .addRowBtn`).hide();
        } else if (rowCount == 0) {
            $(`#row${rowCount } .addRowBtn`).show();
        }

        // ===== Set retirement values on edit =====
        let yearOptions = '<option value="">Year</option>';
        for (let y = currentYear; y <= endretirementYear; y++) {
            yearOptions += `<option value="${y}">${y}</option>`;
        }
        $('#retirementyear' + rowCount).html(yearOptions);

        if (retiredflag) {
            // 1. Set the flag
            $('#retiredflag' + rowCount).val(retiredflag);

            // 2. Show/hide retirement details
            onchange_retirementstate(document.getElementById('retiredflag' + rowCount), rowCount);

            // 3. Populate year options
            let yearOptions = '<option value="">Year</option>';
            for (let y = currentYear; y <= endretirementYear; y++) {
                yearOptions += `<option value="${y}">${y}</option>`;
            }
            $('#retirementyear' + rowCount).html(yearOptions);
            // 4. Set the year value
            if (retirementyear) {
                $('#retirementyear' + rowCount).val(retirementyear);

                // 5. Populate months based on selected year
                onchange_retirement_yr(
                    document.getElementById('retirementyear' + rowCount),
                    rowCount,
                    retiremntmonth
                );
            }
        } else {
            let yearOptions = '<option value="">Year</option>';
            for (let y = currentYear; y <= endretirementYear; y++) {
                yearOptions += `<option value="${y}">${y}</option>`;
            }
            $('#retirementyear' + rowCount).html(yearOptions);
        }
        // Check if the row limit has been reached
        checkRowLimit(isLast);
        // if (auditeeLiability === 'Y') {
        //     applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');
        // }
        // Apply validation to the newly added row's fields
        applyValidationToNewFields(`notype${rowCount}`, 'Select Number Type');
        applyValidationToNewFields(`name${rowCount}`, 'Enter Name');
        applyValidationToNewFields(`gpfno${rowCount}`, 'Enter Number');
        applyValidationToNewFields(`designation${rowCount}`, 'Enter Designation');
        //applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');

        if (sesroleactioncode == PSA_roleactioncode || sesroleactioncode == PSA_ADroleactioncode) {
            applyValidationToNewFields(`remarks${rowCount}`, 'Enter Remarks');

        }

        rowCount++;

        restrictSpecialChars(".removesplchar_text");
        restrictSpecialChars(".removesplchar_number");
        restrictSpecialChars(".removesplchar_numberwithdecimal");
    }



    function toggleAuditeeLiabilityType(selectedOption) {
        const isN = (selectedOption === 'N') || ($('input[name="auditee_liability"]:checked').val() === 'N');
        if (isN) {
            $('#auditee_liability_type_div').show();
            // keep disabled as requested
            $('#auditee_liability_type').prop('disabled', true);
        } else {
            $('#auditee_liability_type_div').hide();
            $('#auditee_liability_type').prop('disabled', true);
        }
    }

    function toggleAmountColumn() {
        const auditeeLiability = $('input[name="auditee_liability"]:checked').val();

        if (auditeeLiability === 'Y') {
            $('.liability_amount').show();
            $('.amount-field').prop('required', true);

            // Ensure existing amount fields have validation rules
            $('.amount-field').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    applyValidationToNewFields(name, 'Enter Amount');
                }
            });
        } else {
            $('.liability_amount').hide();
            // Clear values and remove validation rules when liability = N
            $('.amount-field').prop('required', false).val('');
            $('.amount-field').each(function() {
                try {
                    $(this).rules('remove');
                } catch (e) {
                    // ignore if validator not initialized
                }
            });
        }
    }
    $('input[name="auditee_liability"]').on('change', function() {
        toggleAmountColumn();
    });

    function validate_notype(el, row) {

        const val = el.value;
        const details = document.getElementById('retirementdetails_div_' + row);

    }

    function onchange_retirementstate(el, row) {

        const val = el.value;

        const detailsDiv = $('#retirementdetails_div_' + row);
        const yearSelect = $('#retirementyear' + row);
        const monthSelect = $('#retirementmonth' + row);

        yearSelect.empty().append('<option value="">Year</option>');
        monthSelect.empty().append('<option value="">Month</option>');

        if (!val) {
            detailsDiv.hide();
            return;
        }

        detailsDiv.show();

        const today = new Date();
        const currentMonth = today.getMonth(); // 0–11
        const currentYear = today.getFullYear();

        let startOffset = 0;
        let totalMonths = 0;

        if (val === 'L') {
            startOffset = 0;
            totalMonths = 3; // 3 months
        }

        if (val === 'M') {
            startOffset = 3;
            totalMonths = 3; // next 3 months
        }

        if (val === 'H') {
            startOffset = 6;
            totalMonths = 48; // 4 full years
        }

        let monthsArr = [];
        let yearsSet = new Set();

        for (let i = 0; i < totalMonths; i++) {

            let futureDate = new Date(currentYear, currentMonth + startOffset + i, 1);

            let year = futureDate.getFullYear();
            let month = futureDate.getMonth() + 1;

            monthsArr.push({
                month,
                year
            });
            yearsSet.add(year);
        }

        // Populate year dropdown
        yearsSet.forEach(year => {
            yearSelect.append(`<option value="${year}">${year}</option>`);
        });

        // Month populate function
        function populateMonths(selectedYear) {

            monthSelect.empty().append('<option value="">Month</option>');

            monthsArr.forEach(m => {

                if (m.year == selectedYear) {

                    let monthName = new Date(0, m.month - 1)
                        .toLocaleString('default', {
                            month: 'long'
                        });

                    // Add leading zero
                    let monthValue = m.month.toString().padStart(2, '0');

                    monthSelect.append(
                        `<option value="${monthValue}">
                                                                                                                                                ${monthName}
                                                                                                                                            </option>`
                    );
                }
            });
        }


        // Default select first available year
        let firstYear = monthsArr[0].year;
        yearSelect.val(firstYear);
        populateMonths(firstYear);

        yearSelect.off('change').on('change', function() {
            populateMonths($(this).val());
        });

        yearSelect.prop('required', true);
        monthSelect.prop('required', true);
    }

    function onchange_retirement_yr(el, row, selectedMonth = '') {

        const retirementYear = el.value;

        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1; // 1–12

        const monthSelect = $('#retirementmonth' + row);

        monthSelect.empty().append('<option value="">Month</option>');

        // if no year selected, just return
        if (!retirementYear) return;


        const sortedMonths = Object.keys(months).sort(function(a, b) {
            return parseInt(a, 10) - parseInt(b, 10);
        });

        $.each(sortedMonths, function(_, monthNum) {

            const monthName = months[monthNum];
            const monthInt = parseInt(monthNum, 10);

            // If selected year is current year, skip past months
            if (parseInt(retirementYear, 10) === currentYear &&
                monthInt < currentMonth) {
                return true; // continue
            }

            monthSelect.append(
                `<option value="${monthNum}" ${monthNum == selectedMonth ? 'selected' : ''}>${monthName}</option>`
            );
        });

    }







    function checkRowLimit(isLast) {
        let rows = $(".work-row");


        $(".addRowBtn").hide();



        if (rowCount >= maxRow) {

            return; // Stop processing if max rows are reached
        }
        // alert(rows.length);
        if (rows.length > 0) {

            // Show plus button on last row
            $(rows[rows.length - 1]).find(".addRowBtn").show();
        }
    }


    function deleteRow(rowId) {
        // Remove the row
        $(`#row${rowId}`).remove();

        // Decrement row count
        rowCount--;

        // Show the add button in the previous row (if exists)
        $(`#row${rowId - 1}`).find('.addRowBtn').show();

        // Special case: if no rows left, show add button in row 0
        if (rowCount === 0) {
            $(`#row0`).find('.addRowBtn').show();
        }
    }


    function applyValidationToNewFields(inputName, message) {
        let $input = $("[name='" + inputName + "']"); // Select input by name
        // console.log("Applying validation to:", inputName);
        if ($input.length) {


            let validator = $("#para_form").data("validator"); // Get validator instance

            if (!validator) {

                $("#para_form").validate(); // Ensure validation is initialized
                validator = $("#para_form").data("validator");
            }
            $input.rules("remove");
            // Ensure rules are applied only once
            // if (!$input.rules()) {

            $input.rules("add", {
                required: true,
                messages: {
                    required: message
                }
            });
            // }

            validator.element($input);

            // ✅ Ensure validation runs on change without removing existing messages
            $input.on("change", function() {
                $(this).valid();
            });
        } else {
            console.error("❌ Element not found:", inputName);
        }
    }



    $(document).on('keypress', '.numberswithdecimal', function(event) {
        var inputValue = $(this).val();
        var charCode = event.charCode;

        // Allow numeric characters (0-9)
        if (charCode >= 48 && charCode <= 57) {
            return true; // let it happen
        }

        // Allow a single period (.)
        if (charCode === 46 && inputValue.indexOf('.') === -1) {
            return true; // let it happen
        }

        // Block non-numeric characters (excluding period)
        return false;
    });

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

                if (selector === '.removesplchar_text') {
                    if (!/^[a-zA-Z\u0B80-\u0BFF]$/.test(char)) {
                        event.preventDefault();
                    }
                } else if (selector === '.removesplchar_number') {
                    if (!/^[0-9]$/.test(char)) {
                        event.preventDefault();
                    }
                } else if (selector === '.removesplchar_numberwithdecimal') {
                    // Allow digits, one dot, no leading dot, and only 2 digits after dot
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
                }
            })
            .on("paste", function(e) {
                e.preventDefault();
                let pasteData = (e.originalEvent || e).clipboardData.getData('text');
                let cleanData = '';

                if (selector === '.removesplchar_text') {
                    cleanData = pasteData.replace(/[^a-zA-Z\u0B80-\u0BFF]/g, '');
                } else if (selector === '.removesplchar_number') {
                    cleanData = pasteData.replace(/[^0-9]/g, '');
                } else if (selector === '.removesplchar_numberwithdecimal') {
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






    //---------------------------------------------------------------------------------//


    var lang;

    $(document).ready(function() {
        lang = getLanguage();
        change_button_as_update('para_form', 'action', 'buttonaction', 'display_error', '', '');

        var paraid_val = <?php echo json_encode($paraid); ?>;
        var firstItem = <?php echo $paradetails; ?>;

        fix_formfield_values(firstItem[0]);
        fetch_paradata(paraid_val);
    });

    $("#translate").change(function() {

        lang = getLanguage('Y');
        switchChecklistLanguage(lang)

    });

    function switchChecklistLanguage(lang) {

        $('.lang_value').each(function() {
            const ta = $(this).attr('data-ta');
            const en = $(this).attr('data-en');
            $(this).val(lang === 'ta' ? ta : en);
        });
    }

    let dataFromServer;


    function populatehistorydata(responseData) {
        if (!responseData || !Array.isArray(responseData)) {
            responseData = Array.from(responseData || []);
        }

        $('#historydiv').show();
        let container = document.getElementById("historydiv");
        container.innerHTML = "";
        const lang = getLanguage('')
        responseData.forEach((data, index) => {
            let accordionId = `remarkAccordion${index}`;
            let collapseId = `collapse${index}`;
            let headerId = `heading${index}`;
            let forwardedOn = ChangeDateFormat(data.forwardedon) || "-";
            let forwardedBy = (data.usertypecode === 'I' ? data.auditeename : data.username) || "-";
            let designation = (data.usertypecode === 'I' ? 'Auditee' : data.desigesname) || "-";
            const roleMap = {
                I: {
                    bg: 'auditee_div',
                    body: 'auditee_body',
                    label: 'Auditee'
                },
                A: {
                    bg: 'auditor_div',
                    body: 'auditor_body',
                    label: 'PSA Auditor'
                },
                AD: {
                    bg: 'ad_div',
                    body: 'ad_body',
                    label: 'PSA AD'
                },
                DL: {
                    bg: 'dl_div',
                    body: 'dl_body',
                    label: 'District HLC'
                },
                DE: {
                    bg: 'dept_div',
                    body: 'dept_body',
                    label: 'Department HLC'
                },
                SL: {
                    bg: 'state_div',
                    body: 'state_body',
                    label: 'State HLC'
                },
                RC: {
                    bg: 'state_div',
                    body: 'state_body',
                    label: 'Retirement Committee'
                }
            };
            const role = roleMap[data.actroleactioncode] || roleMap['SL'];
            let actionname = lang == 'ta' ? data.actiontname : data.actionename;
            let rtd_remarks = data.remarks ?? '-';
            // Remarks parsing
            let remarkContent = "No remarks provided";
            try {
                if (data.para_remarks) {
                    let parsed = JSON.parse(data.para_remarks);
                    let tempDiv = document.createElement("div");
                    tempDiv.innerHTML = parsed.content || "";

                    // 🔥 Find the element and remove ONLY the id
                    let presentEntry = tempDiv.querySelector("#present_entry");
                    if (presentEntry) {
                        presentEntry.removeAttribute("id");
                    }

                    remarkContent = tempDiv.innerHTML;

                } else if (data.para_historyremarks) {
                    remarkContent = data.para_historyremarks;
                }
            } catch (e) {
                remarkContent = data.para_remarks || remarkContent;
            }
            let action_div = "";
            if (['A', 'AD', 'DL', 'RC', 'SL', 'DE'].includes(data.actroleactioncode)) {
                action_div = `<div class="col-md-6">
                                                                                                            <label><strong>Action to be taken</strong></label>
                                                                                                           <input class="form-control lang_value" data-en="${actionname}"  data-ta="${actionname}" value="${actionname}" disabled>

                                                                                                        </div>`;
            }
            let retirement_remarks_HTML = "";
            if (data.actroleactioncode == 'RC') {

                retirement_remarks_HTML = `
                                                                                        <div class="col-md-6">
                                                                                                        <label><strong>Remarks of Retirement Committee</strong></label>
                                                                                                       <input class="form-control lang_value"  value="${rtd_remarks}" disabled>

                                                                                                    </div>
                                                                                    `;
            }
            // Attachments (only if present)
            let attachmentHTML = "";
            if (data.auditeefileupload && data.auditeefileupload != '---') {
                files = getfile(data.auditeefileupload)
                attachmentHTML =
                    `
                                                                                                                                            <div class="row mb-3">
                                                                                                                                                <div class="col-md-12">
                                                                                                                                                    <label class="form-label"><strong>Attachments:</strong></label>
                                                                                                                                                    <div class="d-flex flex-row">
                                                                                                                                                        <div class="position-relative d-flex flex-row" id="history_filediv_${index}">
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        `;


                // call file uploader function

            }

            // Accordion markup
            let accordion = document.createElement("div");
            accordion.classList.add("accordion", "my-2");
            accordion.id = accordionId;
            accordion.innerHTML =
                `
                                                                                                                                        <div class="accordion-item " >
                                                                                                                                            <h2 class="accordion-header" id="${headerId}">
                                                                                                                                                <button style="height: 50px;" class="${role.bg} accordion-button auditor_bg collapsed "
                                                                                                                                                        type="button"
                                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                                        data-bs-target="#${collapseId}"
                                                                                                                                                        aria-expanded="false"
                                                                                                                                                        aria-controls="${collapseId}">
                                                                                                                                                    <div class="d-flex flex-column w-100">
                                                                                                                                                        <div class="d-none d-md-flex align-items-center w-100">
                                                                                                                                                            <div class="text-truncate fw-bold">
                                                                                                                                                              ${role.label}
                                                                                                                                                            </div>
                                                                                                                                                            <div class="text-muted small ms-2" style="margin-left:20px;">
                                                                                                                                                                ${forwardedOn}
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </button>
                                                                                                                                            </h2>
                                                                                                                                            <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}">
                                                                                                                                                <div class="accordion-body ${role.body}">

                                                                                                                                                    <div class="row mb-3">

                                                                                                                                                        <div class="col-md-9">
                                                                                                                                                            <label><strong>Remarks:</strong></label>
                                                                                                                                                            <div class="editor-container border p-2 rounded bg-light">
                                                                                                                                                                ${remarkContent}
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        ${action_div}
                                                                                                                                                    </div>
                                                                                                                                                    ${attachmentHTML}
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <hr class="p-2">
                                                                                                                                        `;

            container.appendChild(accordion);
            if (data.auditeefileupload) {
                UploadedFileList(files, '', `history_filediv_${index}`, '', 'fileuploadid');
            }
        });
    }

    function fetch_paradata(paraid) {

        $.ajax({
            url: '/legacy/fetch_paradata', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                paraid: paraid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {

                if (response.historydata && response.historydata.length > 0) {
                    populatehistorydata(response.historydata);


                    if (sesroleactioncode == PSA_ADroleactioncode) {
                        const aiRecords = response.historydata.filter(item => item.actroleactioncode ===
                            "A");

                        auditor_lastactioncode = aiRecords.length > 0 ?
                            aiRecords[aiRecords.length - 1].actioncode :
                            null;
                    }

                }

                if (response.data && response.data.length > 0) {

                    var paradata = response.data[0];
  console.log('paradata' + paradata.processcode)
                    var paraupdatedby = paradata.updatedby;
                    $('#status_row').show()


                    if (paraupdatedby == sessionuserid) {

                        change_button_as_update('para_form', 'action', 'buttonaction', 'display_error', '');

                    } else {

                        change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');


                    }
                    fix_workflow_values(paradata);

                } else {
                    change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');
                }

            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.error ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }








    function fix_workflow_values(paradata) {


        $('#present_entry').show();
        $('#button_set').show();

        const processcode = paradata.processcode;

        var paraupdatedby = paradata.updatedby;

        var liabiltyvalue = $('#liabilityval').val()

        var firstItem = <?php echo $paradetails; ?>;

        $('input[name="liability"][value="' + firstItem[0].liability + '"]').prop('checked', true);


        if (paraupdatedby == sessionuserid) {

            if (
                (sesroleactioncode === PSA_roleactioncode && processcode === 'F') ||
                (sesroleactioncode === PSA_ADroleactioncode && processcode === 'K')
            ) {

                auditeeremarks.setData(paradata.para_remarks ?? '');
                $('#actioncode').val(paradata.actioncode).trigger('change'); // for select2

                if (paradata.fileuploaddet && paradata.fileuploaddet !== '---') {

                    files = getfile(paradata.fileuploaddet);

                    UploadedFileList(files, 'edit', 'file-list-container', 'Y', 'fileuploadid');

                    $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));

                } else {

                    appendFilePreview();
                    $('#file-input-container').show();
                    $('#add-file-btn').show();
                }

            } else {

                $('.file_attach').hide();
                $('#present_entry').hide();
                $('#button_set').hide();
            }
        }



        if (paradata.liabilitydel) {


            // if (sesroleactioncode == PSA_ADroleactioncode)
            //     $('#actioncode option[value="11"]').show();


            const liabilitydel = paradata.liabilitydel;
            // alert('if');

            $('#dynamicRowsContainer').show();

            $liabilityaction = 'view';
            // Assuming you have this string (you can replace this with dynamic data from your backend or input)



            // Step 1: Split the liabilitydel string by commas
            var liablityparts = liabilitydel.split(",");
            liabilityid = '';
            // Step 2: Iterate over the parts and split each by the dash (-)
            for (var i = 0; i < liablityparts.length; i++) {
                var liablity = liablityparts[i].split("|~|"); // Split by the dash (-)

                let isLast = (i === liablityparts.length - 1);

                const invalidValues = [null, '', 'null', 'undefined'];
              if (invalidValues.includes(liablity[0])) {
                    $('#dynamicRowsContainer').hide();
                    continue;
                }

                if ((liablity[7] == 'Y' && sesroleactioncode == PSA_roleactioncode) || sesroleactioncode ==
                    PSA_ADroleactioncode) {
                   addNewWorkRow(event, $liabilityaction, liablity[0], liablity[2], liablity[1], liablity[3],
                        liablity[4], liablity[6], liablity[5], isLast, liablity[7], paradata.processcode,
                        liablity[
                            8], liablity[9], liablity[10], liablity[11]);
                }




                liabilityid += liablity[5] + ',';

            }
            if (liabilityid.endsWith(',')) {
                liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
            }
            $('#liabilityid').val(liabilityid);

        } else {
            $('#actioncode option[value="11"]').remove();
            $('#dynamicRowsContainer').hide()
        }


        var currentprocesscode = paradata.processcode;
        var rejoinderstatus = paradata.rejoinderstatus;


        // if (paradata.retiredflag === 'Y') {

        //     $('#retirementyear').val(paradata.retirementyear);

        //     onchange_retirementstate(paradata.retiredflag);
        //     onchange_retirement_yr(
        //         paradata.retirementyear,
        //         paradata.retirementmonth
        //     );
        // }



        //(sesroleactioncode ==PSA_ADroleactioncode && currentprocesscode == 'K') ?

        fit_status(currentprocesscode, rejoinderstatus);
        $('#processcode').val(paradata.processcode);

        $('#rejectcount').val(paradata.rejectcount);
        $('#rejoindercycle').val(paradata.rejoindercycle);

        const pararejoindercount = paradata.rejoindercycle ?? 0;
        $('#rejoinderstatus').val(paradata.rejoinderstatus);
        if (pararejoindercount >= rejoinderlimit) {

            $('#actioncode option[value="03"]').remove();
        } else {

            $('#actioncode option[value="03"]').prop('disabled', false);
        }
        onchange_actiononpara(paradata.actioncode, paradata.rejoindercycle)



    }



    function fix_formfield_values(firstItem, seriesno) {
console.log('fix_formfield_values:' + firstItem.processcode)
        $('#present_entry').hide();
        $('#button_set').hide();
        rowCount = 0;



        $('#dynamicRowsContainer').empty();
        UploadedFileList_withaction = '';

        $('#currentslipnumber').val(firstItem.paranumber);
        $('#paranumber').val(firstItem.paranumber);
        $('#followupid').val(firstItem.encrypted_followupid);
        $('#amount_involved').val(firstItem.amtinvolved);
        $('#serious').val(firstItem.irregularitiescode);
        $('#parano').val(firstItem.parano);
        $('#schemename').val(firstItem.auditeeschemecode);
        getcategoryBasedOnSerious(firstItem.irregularitiescode, firstItem.irregularitiescatcode);
        getsubcategoryBasedOnCategory(firstItem.irregularitiescatcode, firstItem.irregularitiessubcatcode);

        $('#severityid').val(firstItem.severitycode);
        $('#liabilityval').val(firstItem.liability);
        $('#fileuploadstatus').val('N');
        $('input[name="scheme"][value="' + firstItem.schemastatus + '"]').prop('checked', true);

        toggleAuditeeLiabilityType(firstItem.auditee_liability);
        if (firstItem.schemastatus === 'Y') {
            $('#severityDiv').show();
        } else {
            $('#severityDiv').hide();

        }

        var liabilitydel = firstItem.liabilitydel;
        //  enable_liability(firstItem.liability, 'liabilityname_div', 'liabilitygpfno_div')
        $('input[name="liability"][value="' + firstItem.liability + '"]').prop('checked', true);


        // if (firstItem.liabilitydel) {

        //     // alert('if');

        //     $('#dynamicRowsContainer').show();

        //     $liabilityaction = 'view';
        //     // Assuming you have this string (you can replace this with dynamic data from your backend or input)



        //     if (liabilitydel) {

        //         // Step 1: Split the liabilitydel string by commas
        //         var liablityparts = liabilitydel.split(",");
        //         liabilityid = '';
        //         // Step 2: Iterate over the parts and split each by the dash (-)
        //         for (var i = 0; i < liablityparts.length; i++) {
        //             var liablity = liablityparts[i].split("-"); // Split by the dash (-)

        //             let isLast = (i === liablityparts.length - 1);



        //             addNewWorkRow(event, $liabilityaction, liablity[0], liablity[2], liablity[1], liablity[3],
        //                 liablity[4], liablity[6], liablity[5], isLast, liablity[7], firstItem.processcode, liablity[
        //                     8], liablity[9], liablity[10]);



        //             liabilityid += liablity[5] + ',';

        //         }
        //         if (liabilityid.endsWith(',')) {
        //             liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
        //         }
        //         $('#liabilityid').val(liabilityid);
        //     }
        // } else {
        //     $('#dynamicRowsContainer').hide()
        // }

        //  alert(firstItem.mainobjectionid)


        getminorobjection(firstItem.mainobjectionid, firstItem.subobjectionid, 'minorobjectioncode',
            'mainobjectionid');

        $('#mainobjectionid').val(firstItem.mainobjectionid);
        $('#mainobjectionid').select2();
        $('#typeofauditcode').val(firstItem.typeofauditcode)
        $('#part_no').val(firstItem.paranumber)
        $('#yearcode').val(firstItem.audityear)

        editor.setData(firstItem.remarks);
        if (editor) editor.enableReadOnlyMode('customLock');

        fileviewid = 'file-lagacylist-container';
        fileidstore = '';
        fileupload_hiddenid = '';

        fileupload_hiddenid = 'fileuploadid';
        $('#file-list-container').toggle(!!firstItem.auditorfileupload);
        if (firstItem.auditorfileupload) {
            files = getfile(firstItem.auditorfileupload)
            UploadedFileList(files, UploadedFileList_withaction, fileviewid, '', fileupload_hiddenid)
            $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
        } else {
            $('.para_attach').hide()
        }


    }

    function disenableformfields() {
        $('#mainobjectionid,#subobjectionid').attr('disabled', true);
        //   $('#subobjectionid').attr('disabled', true);
        $('#typeofparacode').attr('disabled', true);
        $('#stateofparacode').attr('disabled', true);
        $('#parano').attr('disabled', true);
        $('#amount_involved').attr('disabled', true);
        $('#severityid').attr('disabled', true);
        $('#slipdetails').attr('disabled', true);
        $('#serious').attr('disabled', true);
        $('#category').attr('disabled', true);
        $('#subcategory').attr('disabled', true);
        $('#schemename').attr('disabled', true);
        //$('#yearcode').attr('disabled', true);
        $('#lastactionmonth').attr('disabled', true);
        $('#lastactionyear').attr('disabled', true);
        $('#actiontaken').attr('disabled', true);


        if (editor) editor.enableReadOnlyMode('customLock'); // Provide a unique lock ID

        $('input[name="liability"]').prop('disabled', true);
        $('input[name="scheme"]').prop('disabled', true);
        $('#liabilityname').attr('disabled', true);
        $('#liabilitygpfno').attr('disabled', true);
        $('#liabilitydesig').attr('disabled', true);

        document.getElementById('add-file-btn').classList.add('hidden');

        $('#approvebtn').hide();
    }
    $(document).on('click', '#forward_btn', function(event) {

        event.preventDefault(); // Prevent form submission

        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (!$("#para_form").valid()) {
            return scrollToFirstError();
        }
        var actionfor = 'forward'


        if (sesroleactioncode === PSA_ADroleactioncode) {
            const actioncode = $('#actioncode').val();

            // If action differs from auditor’s last action
            if (auditor_lastactioncode !== actioncode) {

                passing_alert_value(
                    'Confirmation',
                    'The action has been updated by the Approval Authority.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Attach click handler only after alert is shown
                $('#process_button')
                    .off('click')
                    .on('click', function(event) {
                        event.preventDefault();
                        get_insertparadetails('finalise', actionfor);
                    });
            } else {


                passing_alert_value('Confirmation', 'Are you sure to forward the para?',
                    'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');



                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    get_insertparadetails('finalise', actionfor);
                });
            }



        } else {

            passing_alert_value('Confirmation', 'Are you sure to Reject the para?',
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');



            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                get_insertparadetails('finalise', actionfor);
            });
        }
    });


    function getfile(filearray) {
        return files = filearray.split(',').map((fileDetail, index) => {
            const [name, path, size, fileuploadid] = fileDetail.split('-');
            return {
                id: index + 1,
                name,
                path,
                size,
                fileuploadid
            };
        });
    }



    /***********************************jquery Validation**********************************************/
    const $para_form = $("#para_form");


    $("#para_form").validate({
        rules: {
            actioncode: {
                required: true,
            },


        },

        errorPlacement: function(error, element) {
            if (element.hasClass('select2')) {
                // Insert the error message below the select2 dropdown container
                error.insertAfter(element.next('.select2-container'));
            } else {
                // For other fields, insert the error message after the element itself
                error.insertAfter(element);
            }
        },

        messages: {
            actioncode: {
                required: 'Select the action to be taken',
            },

        }


    });


    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $para_form.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    /***********************************jquery Validation**********************************************/


    /***********************************Action**********************************************/

    //action on save
    $(document).on('click', '#buttonaction', function(event) {

        event.preventDefault(); // Prevent form submission
        // Check if the error message is visible
        // if ($('#display_error').is(':visible')) {
        //     return; // Exit the function to prevent form submission
        // }
        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (!$("#para_form").valid()) {
            return scrollToFirstError();
        }

        // If session role matches PSA_ADroleactioncode
        if (sesroleactioncode === PSA_ADroleactioncode) {
            const actioncode = $('#actioncode').val();

            // If action differs from auditor’s last action
            if (auditor_lastactioncode !== actioncode) {
                // getLabels_jsonlayout([{
                //         id: 'differaction',
                //         key: 'differaction'
                //     }], 'N')
                //     .then((text) => {
                passing_alert_value(
                    'Confirmation',
                    'The action has been updated by the Approval Authority.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Attach click handler only after alert is shown
                $('#process_button')
                    .off('click')
                    .on('click', function(event) {
                        event.preventDefault();
                        get_insertparadetails('insert', 'fresh');
                    });
                // });
            } else {
                get_insertparadetails('insert', 'fresh');
            }
        } else {
            get_insertparadetails('insert', 'fresh');
        }
    });

    //action on reject
    $(document).on('click', '#reject_btn', function(event) {

        event.preventDefault(); // Prevent form submission

        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (!$("#para_form").valid()) {
            return scrollToFirstError();
        }
        var actionfor = (sesroleactioncode == PSA_ADroleactioncode) ? 'reject' : 'forward'


        if (sesroleactioncode === PSA_ADroleactioncode) {
            const actioncode = $('#actioncode').val();

            // If action differs from auditor’s last action
            if (auditor_lastactioncode !== actioncode) {

                passing_alert_value(
                    'Confirmation',
                    'The action has been updated by the Approval Authority.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Attach click handler only after alert is shown
                $('#process_button')
                    .off('click')
                    .on('click', function(event) {
                        event.preventDefault();

                        get_insertparadetails('finalise', actionfor);






                    });
            } else {


                passing_alert_value('Confirmation', 'Are you sure to Reject the para?',
                    'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');



                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    get_insertparadetails('finalise', actionfor);
                });
            }



        } else {

            passing_alert_value('Confirmation', 'Are you sure to Reject the para?',
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');



            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                get_insertparadetails('finalise', actionfor);
            });
        }
    });

    //action on rejoinder
    $(document).on('click', '#rejoinder_btn', function(event) {

        event.preventDefault(); // Prevent form submission

        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }
        if ($("#para_form").valid()) {

            passing_alert_value('Confirmation', 'Do you need more clarification?', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');

            var actionfor = (sesroleactioncode == PSA_ADroleactioncode) ? 'rejoinder' : 'forward'


            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                get_insertparadetails('finalise', actionfor);
            });



        } else {
            scrollToFirstError();
        }
    });

    //action on approving
    $(document).on('click', '#finalisebtn', function(event) {

        event.preventDefault(); // Prevent form submission

        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }

        if (!$("#para_form").valid()) {
            return scrollToFirstError();
        }
        var actionfor = (sesroleactioncode == PSA_ADroleactioncode) ? 'approve' : 'forward'

        if (sesroleactioncode === PSA_ADroleactioncode) {
            const actioncode = $('#actioncode').val();

            // If action differs from auditor’s last action
            if (auditor_lastactioncode !== actioncode) {

                passing_alert_value(
                    'Confirmation',
                    'The action has been updated by the Approval Authority.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'forward_alert'
                );

                // Attach click handler only after alert is shown
                $('#process_button')
                    .off('click')
                    .on('click', function(event) {
                        event.preventDefault();

                        get_insertparadetails('finalise', actionfor);


                    });
            } else {


                passing_alert_value('Confirmation', 'Are you sure to forward the data?', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');



                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    get_insertparadetails('finalise', actionfor);
                });
            }



        } else {

            passing_alert_value('Confirmation', 'Are you sure to forward the data?', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');



            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                get_insertparadetails('finalise', actionfor);
            });
        }
    });



    function get_insertparadetails(action, actionfor) {

        $('#finalisebtn').attr('disabled', true)
        $('#rejoinder_btn').attr('disabled', true)
        $('#reject_btn').attr('disabled', true)
        $('#forward_btn').attr('disabled', true)

        var statusflag = 'Y';

        if (action == 'finalise') {
            $('#process_button').attr('disabled', true)
            statusflag = 'F';
        } else {
            $('#buttonaction').attr('disabled', true)

        }
        $('#loader').removeClass('d-none');

        var formData = new FormData($('#para_form')[0]);

        var typeofauditcode = '<?php echo $financialauditcode; ?>';
        var audityear = $('#yearcode').val()
        var n_audityear = $('#n_yearcode').val()
        var paratype = $('#paratype').val()
        var liabilityval = $('#liabilityval').val()
        var deleted_liabilityid = $('#deleted_liabilityid').val()

        formData.append('statusflag', statusflag);
        formData.append('actionfor', actionfor);
        formData.append('yearcode[]', audityear);
        formData.append('n_yearcode[]', n_audityear);
        formData.append('paratype', paratype);
        formData.append('liabilityval', liabilityval);
        formData.append('deleted_liabilityid', deleted_liabilityid);
        formData.append('auditeeremarks', auditeeremarks.getData());

        var deactive_fileid = $('#deactive_fileid').val()

        formData.append('deactive_fileid', deactive_fileid);

        $(".work-row").each(function(index) {
            let rowId = $(this).attr('id').replace("row", ""); // Extract row number

            formData.append(`liabilityid[]`, $(`#liabilityid${rowId}`).val());
            formData.append(`notype[]`, $(`select[name='notype${rowId}']`).val());
            formData.append(`name[]`, $(`#name${rowId}`).val());
            formData.append(`gpfno[]`, $(`#gpfno${rowId}`).val());
            formData.append(`designation[]`, $(`#designation${rowId}`).val());
            formData.append(`amount[]`, $(`input[name='amount${rowId}']`).val());
            formData.append('remarks[]', $(`textarea[name='remarks${rowId}']`).val());

            formData.append(`retiredflag[]`, $(`#retiredflag${rowId}`).val());
            formData.append(`retirementyear[]`, $(`select[name='retirementyear${rowId}']`).val());
            formData.append(`retirementmonth[]`, $(`select[name='retirementmonth${rowId}']`).val());
		formData.append(`lagacyflag[]`, $(`#lagacyflag${rowId}`).val());
            let checkboxValues = [];
            $('input[name="activestatus[]"]').each(function() {
                // If the checkbox is checked, push '1', otherwise push '0'
                checkboxValues.push(this.checked ? '1' : '0');
            });

            // Append the checkbox values to formData
            checkboxValues.forEach((value, index) => {
                formData.append(`activestatus[${index}]`,
                    value); // Append each value with its index
            });

            // Handling checkbox values properly
            // let isChecked = $(`input[name="activestatus[]"]`).eq(index).is(":checked") ? '1' : '0';
            // formData.append(`activestatus[]`, isChecked);
        });
        $('#present_entry').hide();
        $('#button_set').hide();
        $.ajax({
            url: '/lagacy/paradet_insert', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    reset_form();

                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    $('#ok_button').off('click').on('click', function(event) {
                        event.preventDefault();

                        // if (sesroleactioncode == PSA_ADroleactioncode) {
                        location.reload();
                        // } else {
                        //     fetch_paradata(response.data['paraid']);
                        // }


                    });




                }

            },
            complete: function() {
                $('#finalisebtn').attr('disabled', false)
                $('#rejoinder_btn').attr('disabled', false)
                $('#reject_btn').attr('disabled', false)
                $('#forward_btn').attr('disabled', false)

                // Optionally, you can re-enable the button here if desired
                if (action == 'finalise') {

                    $('#process_button').attr('disabled', false)

                } else {
                    $('#buttonaction').attr('disabled', false)

                }
                $('#loader').addClass('d-none');
            },

            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';
                $('#display_error').show();
                $('#display_error').text(errorMessage);
                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }


    /***********************************Action**********************************************/


    function reset_form() {



        const lang = getLanguage('');

        auditeeremarks.setData('');
        appendFilePreview();

        $('#deactive_fileid').val('');
        $('#active_fileid').val('');

        $('#fileuploadid').val('');

        $('#file-input-container').hide()
        $('#file-input-container').empty();
        //  $('#add-file-btn').hide()

        //clear upload filecontainer

        $('#fileuploadid').val('');
        $('#processcode').val('');
        $('#rejoinderstatus').val('');
        $('#rejoindercycle').val('');

        $('#file-list-container').empty();
        $('#file-list-container').hide();
    }
    /**************************************** Fit the upload files, delete upload file in s **********************/
    function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid) {
        const $container = $('#' + containerid).empty();

        files.forEach(file => {
            if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);

            const fileCard = `
                <div class="position-relative align-items-stretch ms-2" ${action === 'edit' ? `id="file-card-${file.fileuploadid}"` : ''}>
                    <div class="card ms-2">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between ms-2">
                                ${action === 'edit' ? `<input type="hidden" id="fileuploadid_${file.fileuploadid}" name="fileuploadid_${file.fileuploadid}" value="${file.fileuploadid}">` : ''}
                                <div class="d-flex">
                                    <div class="p-1 bg-primary-subtle rounded me-6 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-file-text text-primary fs-6"></i>
                                    </div>
                                    <div>
                                        <!-- Use JavaScript template literals to generate the correct URL -->
                                        <a class="fs-3 fw-semibold" style="color:black;" href="/${file.path}" target="_blank">${file.name}</a>
                                    </div>
                                </div>
                                ${action === 'edit' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="bg-danger-subtle badge ms-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <span class="fs-5 text-danger fw-semibold mb-0"><i class="ti ti-trash" onclick="deleteFile(${file.fileuploadid}, event)"></i></span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;

            $container.append(fileCard);
            if (action == 'edit')
                fileCount++;
        });


        if (uploadidstatus == 'Y') {

            if (fileCount < maxfilecount) {

                $('#add-file-btn').show();
                $('#file-input-container').show();
            } else {
                $('#add-file-btn').hide();
            }

        }
    }

    function removeAllEventListeners(element) {
        var newElement = element.cloneNode(true); // Clone the element (deep clone)
        element.parentNode.replaceChild(newElement, element); // Replace old element with new cloned element
    }


    // Function to delete a file
    function deleteFile(fileId, event) {
        event.preventDefault(); // Prevents page refresh
        removeAllEventListeners(document.getElementById("process_button"));


        document.getElementById("process_button").onclick = null;


        // Set up the confirmation process
        document.getElementById("process_button").onclick = function() {
            deletefilefromview(fileId);
        };

        // Show confirmation alert
        passing_alert_value('Confirmation', "Are you sure you want to delete this file?", 'confirmation_alert',
            'alert_header', 'alert_body', 'forward_alert');
    }

    function deletefilefromview(fileId) {



        $('#file-card-' + fileId).hide();

        // Optionally, remove the file ID from activefileid (if necessary)
        var activeFileIds = $('#active_fileid').val().split(',');
        activeFileIds = activeFileIds.filter(function(id) {
            return id != fileId;
        });
        $('#active_fileid').val(activeFileIds.join(','));


        // Get the current deactivefileid value and ensure it is an array
        var deactiveFileIds = $('#deactive_fileid').val().split(',').filter(function(id) {
            return id !== ''; // Remove empty values (in case there's a leading comma)
        });

        // Add the file ID to deactivefihbleid if not already present
        if (!deactiveFileIds.includes(fileId.toString())) {
            deactiveFileIds.push(fileId);
        }


        if (fileId !== 1) { // Only allow removal of second and third file inputs
            $(`#fileupload_${fileId}`).remove();
            fileCount--;
        }





        if ((fileCount < maxfilecount)) {

            $('#file-input-container').show()
            $('#add-file-btn').show()
        }



        // Join the array with commas and update the deactive_fileid hidden input field
        $('#deactive_fileid').val(deactiveFileIds.join(','));
        var deactive = $('#deactive_fileid').val()

    }

    /**************************************** Fit the upload files, delete upload file in edit **********************/

    /*************************************************  Ckeditor  *********************************************/


    let editor;

    CKEDITOR.ClassicEditor.create(document.getElementById("remarks"), {
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
                    'insertTable',
                    '|',

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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory',
                'Pagination',
                'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced', 'CaseChange', 'uploadImage'
            ]
        })
        .then(e => {
            editor = e;
        })
        .catch(error => {
            console.error(error);
        });


    let auditeeremarks;

    CKEDITOR.ClassicEditor.create(document.getElementById("auditeeremarks"), {
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
                     'insertTable',
                    '|',
                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'Welcome to CAMS...',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
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
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        })
        .then(editor => {
            auditeeremarks = editor;
            // auditeeremarks.enableReadOnlyMode('initial');

            // Disable editing (make read-only)
            // view_editor.enableReadOnlyMode();
        })
        .catch(error => {
            console.error(error);
        });



    function initializeEditors(editorIds) {

        if (!window.CKEDITOR || !window.CKEDITOR.ClassicEditor) {
            setTimeout(() => initializeEditors(editorIds), 100); // Retry if CKEditor is not yet loaded
            return;
        }

        editorIds.forEach(id => {
            let textarea = document.getElementById(id);
            if (textarea) {
                CKEDITOR.ClassicEditor.create(textarea, {
                        toolbar: {
                            items: [
                                'selectAll', '|',
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        placeholder: 'Write Your Audit Observation here',
                        fontFamily: {
                            options: [
                                'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                                'Courier New, Courier, monospace',
                                'Georgia, serif',
                                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                'Tahoma, Geneva, sans-serif',
                                'Times New Roman, Times, serif',
                                'Trebuchet MS, Helvetica, sans-serif',
                                'Verdana, Geneva, sans-serif'
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
                            'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
                            'Base64UploadAdapter',
                            'MultiLevelList',
                            'RealTimeCollaborativeComments',
                            'RealTimeCollaborativeTrackChanges',
                            'RealTimeCollaborativeRevisionHistory',
                            'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                            'RevisionHistory', 'Pagination',
                            'WProofreader',
                            'MathType', 'SlashCommand', 'Template', 'DocumentOutline',
                            'FormatPainter',
                            'TableOfContents',
                            'PasteFromOfficeEnhanced', 'CaseChange'
                        ]
                    })
                    .then(editor => {
                        editor.enableReadOnlyMode('initial'); // Read-only mode

                        // Apply custom styling for scrolling
                        const editable = editor.ui.view.editable.element;
                        editable.style.maxHeight = 'auto'; // Set max height for the editable area
                        editable.style.overflowY = 'auto'; // Enable vertical scrolling
                    })
                    .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
                }
            });
        }
    </script>
@endsection
