@section('content')
@section('title', 'Audit Para Management')
@extends('index2') @include('common.alert')



<link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fieldaudit.css') }}">


<?php
$sessionchargedel = session('charge');
$sessionuserdel = session('user');
$sessionuserid = $sessionuserdel->userid;
$deptcode = $sessionchargedel->deptcode;

$sessionroletypecode = $sessionchargedel->roletypecode;
$dga_roletypecode = $DGA_roletypecode;
$Dist_roletypecode = $Dist_roletypecode;
$Re_roletypecode = $Re_roletypecode;
$Ho_roletypecode = $Ho_roletypecode;
$Admin_roletypecode = $Admin_roletypecode;
$roleTypeCode = $sessionchargedel->roletypecode;

$deptcode = $sessionchargedel->deptcode;
$regioncode = $sessionchargedel->regioncode;
$distcode = $sessionchargedel->distcode;

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_dist_disable = $distcode ? 'disabled' : '';

$ensessionuserid = $session_userid;
$financialauditcode = $typeofauditData->first()->typeofauditcode ?? '';

$fileuploadcount = 3;
$liabilitylimit = 5;

$months = $months;

$currentYear = date('Y');

$endretirementYear = $currentYear + 3;
$endYear = $currentYear - 1; // exclude current year
$startYear = $endYear - 4;

$yearofaudit_arr = json_decode($yearofaudit, true);
$count_audityear = count($yearofaudit_arr);

// dd($paradetails);
$para = json_decode($paradetails['data'], true);

$para_details = $para[0];

$paraid = $paraid_decrypt;
$followupid = $para_details['encrypted_followupid'];
$paranumber = $para_details['paranumber'];
$paratype = $para_details['paratype'];
$pararejectcount = $configdatas[0]->pararejectcount;
$enable_forwarded_dlcspara[] = $slc_to_auditee;
$enable_forwarded_dlcspara[] = $DLC_to_auditee;
$enable_forwarded_dlcspara[] = $dehlc_to_auditee;

$enable_dlcprocessed = 'Array ( ' . implode(' ,', $enable_forwarded_dlcspara) . ' )';
?>
<style>

.vr-start {
        border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
        box-shadow: -2px 0 0px rgba(115, 12, 12, 0.05);
        padding-left: 12px;
    }
    .vr-shadow {
        width: 1px !important;
        min-width: 1px;
        background-color: #dee2e6;
        box-shadow: 1px 0 0px rgba(0, 0, 0, 0.15);
        padding: 0;
        margin: 0 6px;
        /* space around line */
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
        background-color: #fffeef !important
    }

    .auditor_body {
        background-color: rgb(247, 242, 251) !important
    }

    .ad_body {
        background-color: #eef8ff !important
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
                    <div class="card-body ">

                        <div class="row">

                            <input type="hidden" name="instid" id="instid"
                                value="{{ $instData->encrypted_instid ?? '' }}">
                            <input type="hidden" name="currentslipnumber" id="currentslipnumber" value='1'>
                            <input type="hidden" name="followupid" id="followupid" value="">
                            <input type="hidden" name="paraid" id="paraid" value="{{ $paraid }}">
                            <input type="hidden" name="paranumber" id="paranumber" value=''>
                            <input type="hidden" name="catcode" id="catcode" value="{{ $instData->catcode ?? '' }}">
                            <input type="hidden" name="ens" id="ens" value='<?php echo $ensessionuserid; ?>'>
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">
                            <input type="hidden" name="seriesno" id="seriesno" value='1'>
                            <input type="hidden" name="auditeeins_subcategoryid" id="auditeeins_subcategoryid"
                                value="{{ $instData->auditeeins_subcategoryid ?? '' }}">
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">
                            <input type="hidden" name="rejoindercycle" id="rejoindercycle" value="">
                            <input type="hidden" name="rejoinderstatus" id="rejoinderstatus" value="">
                            <input type="hidden" name="fileuploadid" id="fileuploadid" value="">
                            <input type="hidden" name="processcode" id="processcode" value="">
                            <input type="hidden" name="paratype" id="paratype" value="{{ $paratype }}">
				<input type="hidden" name="rejectcount" id="rejectcount" value="">
 <input type="hidden" name="liabilityval" id="liabilityval" value="">
  <input type="hidden" name="deleted_liabilityid" id="deleted_liabilityid"
                                            value=''>


                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" key="inst" for="catname">Institution
                                </label>
                                <input type="text" class="form-control lang_value" id="instname" name="instname"
                                    data-en="{{ $instData->instename ?? '' }}"
                                    data-ta="{{ $instData->insttname ?? '' }}"
                                    value="{{ $instData->instename ?? '' }}" disabled>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" for="catname" key="category">Institution
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="catname" name="catname"
                                    data-en="{{ $catData->catename ?? '' }}"
                                    data-ta="{{ $catData->cattname ?? '' }}" value="{{ $catData->catename ?? '' }}"
                                    disabled>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label  lang" for="subcatname" key="sub_head">Institution
                                    Sub
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="subcatname"
                                    name="subcatname"
                                    data-en="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                    data-ta="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcattname : $catData->cattname }}"
                                    value="{{ $catData->if_subcategory == 'Y' ? $subcatData->subcatename : $catData->catename }}"
                                    disabled>
                            </div>
                        </div>

                        <div class="row">



                            <div class="col-md-4 mb-3 ">
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


                            <div class="col-md-4 mb-3">
                                <label class="form-label lang required" key="parano" for="validationDefault01">Para
                                    Number</label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="gen_parano"
                                    name="gen_parano" disabled>
                                    <option value="{{ $para_details['paranumber'] }}">
                                        {{ $para_details['paranumber'] }}
                                    </option>
                                </select>
                            </div>

                        </div>

                       <div class="row mb-2 justify-content-end align-items-center">
                            <!-- Certificate Button -->
                            <div class="col-auto " id="liability_certificate_div">
                                <button id="liability_certificate" type="button" onclick="download()"
                                    class="btn btn-primary fw-medium">
                                    <i class="ti ti-download"></i> Certificate
                                </button>
                            </div>

                            <!-- Status Badge -->
                            <div class="col-auto" id="status_row">
                                <span class="status-badge text-light rounded-pill" id="status_msg">Entry</span>
                            </div>
                        </div>


                        <hr>
                        <div id="paradet_div" class="">
                            <div class="row">
@if($paratype==View::shared('lagacyparatype'))

                                <div class="col-md-2 mb-3 lagacy_div">
                                    <label class="form-label lang required" key="typeofpara"
                                        for="validationDefault01">Type of
                                        Para</label>
                                    <input type="hidden" id="" name="" value="" disabled>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="typeofparacode"
                                        name="typeofparacode" disabled>
                                        <option value="" data-name-en="--Select Type of Para--"
                                            data-name-ta="">
                                            --Select Type of Para--
                                        </option>
                                        @foreach ($typeofpara as $type)
                                            <option value="{{ $type->typeofparacode }}"
                                                data-name-en="{{ $type->typeofparaename }}"
                                                data-name-ta="{{ $type->typeofparatname }}">
                                                {{ $type->typeofparaename }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-2 mb-3 lagacy_div">
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
                                                data-name-en="{{ $state->stateofparaename }}"
                                                data-name-ta="{{ $state->stateofparatname }}">
                                                {{ $state->stateofparaename }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
@endif

                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang required" key=""
                                        for="validationDefault01">Para
                                        Number</label>
                                    <input id="parano" name="parano" class="form-control only_numbers"
                                        value="" maxlength="10" disabled>

                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label lang required" key="statusflag"
                                        for="validationDefault01">Status</label>
                                    <select class="form-select mr-sm-2 lang-dropdown select2" id="actiontaken"
                                        name="actiontaken" onchange="onchange_actiontaken('','','')">
                                        <option value="" data-name-en="--Select Action Taken--"
                                            data-name-ta="">
                                        </option>

                                        <option value="Y" data-name-en="Action taken"
                                            data-name-ta="Action taken">
                                            Action taken
                                        </option>
                                        <option value="N" data-name-en="Action not taken"
                                            data-name-ta="Action not taken">
                                            Action not taken
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 hide_this" id="actiontaken_div">
                                    <label class="form-label lang required" key="last_actiontaken"
                                        for="dropdown1">Last
                                        action
                                        taken</label>
                                    <div class="d-flex gap-2">
                                        <!-- First dropdown - takes 2/3 of space -->
                                        <select class="form-select flex-grow-1" name="lastactionmonth"
                                            id="lastactionmonth">
                                            <option value="">Select Month</option>
                                            @foreach ($months as $num => $name)
                                                <option value="{{ $num }}">
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <!-- Second dropdown - takes 1/3 of space -->
                                        <select class="form-select" style="width: 150px;" name="lastactionyear"
                                            id="lastactionyear">
                                            <option value="">Year</option>

                                            @for ($year = $startYear; $year <= $endYear; $year++)
                                                <option value="{{ $year }}">
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
                                    <select class="select form-control custom-select" id="severityid"
                                        name="severityid" disabled>

                                        <option value="" data-name-en="---Select Severity---"
                                            data-name-ta="---தெரிவு கடைசியாக தேர்ந்தெடுக்கவும்---">
                                            ---Select
                                            Severity---</option>

                                        @foreach ($severities as $key => $severity)
                                            <option value="{{ $key }}" data-en="{{ $severity['en'] }}"
                                                data-ta="{{ $severity['ta'] }}">
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
                                                    <input class="form-check-input success" type="radio"
                                                        name="scheme" id="Y" value="Y"
                                                        onchange="enable_schemename('Y')" disabled>
                                                    <label class="form-check-label lang" for="Y"
                                                        key="yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="scheme" id="N" value="N"
                                                        onchange="enable_schemename('N')" checked disabled>
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
                                    <textarea id="slipdetails" maxlength="500" name="slipdetails" class="form-control text_special"
                                        placholder="Enter remarks" disabled></textarea>
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
                                    </div>
                                </div>

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
                                <hr>
                                <div id="historydiv">

                                </div>
                                <div class="accoriondiv" id="accoriondiv">

                                </div>
                                  <hr>
                                <div class="col-md-8 mb-2">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6" id="auditeeliability_div">
                                            <label class="form-label required lang" for="validationDefaultUsername"
                                                key="">
                                                Liablility to be Done</label><small>(பொறுப்பு விவரங்கள் சேர்க்கப்பட வேண்டும்)</small> <br>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="auditee_liability" id="Y" value="Y"
                                                        checked onchange="enable_liability_details('Y')">
                                                    <label class="form-check-label lang" for="all"
                                                        key="yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="auditee_liability" id="N" value="N"
                                                        onchange="enable_liability_details('N')">
                                                    <label class="form-check-label lang" for="district"
                                                        key="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3" id="auditee_liability_type_div">
                                            <label class="form-label required lang"
                                                for="auditee_liability_type">Liability
                                                Type</label>
                                            <select class="form-select form-select-sm" id="auditee_liability_type"
                                                name="auditee_liability_type">
                                                <option value="non_money_involved">Non Money Involved</option>
                                            </select>
                                        </div>
<div class="col-sm-12 col-md-3" id="auditee_Retirement_type_div">
                                            <label class="form-label required lang"
                                                for="auditee_Retirement_type">Retirement Type
                                                </label>
                                                                                     <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="auditee_Retirement_type" id="Y" value="Y"
                                                         onchange="enable_liability_details('')" checked>
                                                    <label class="form-check-label lang" for="all"
                                                        key="yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input success" type="radio"
                                                        name="auditee_Retirement_type" id="N" value="N"
                                                        onchange="enable_liability_details('')">
                                                    <label class="form-check-label lang" for=""
                                                        key="no">No</label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                
                                <div id="paraliabilitycontainer">
                                </div>
                                <div class="row" id="auditee_formdiv">
                                    <div class="col-md-12" id="">
                                        <label class="form-label required" for="validationDefaultUsername">Auditee
                                            Remarks</label>
                                        <textarea id="auditeeremarks" class="form-control " placeholder="Enter remarks" name="auditeeremarks"></textarea>
                                    </div>
                                    <div class="col-md-12 p-6">
                                        <label class="form-label  lang file_attach" for="validationDefaultUsername"
                                            key="attachments">Attachments </label>
                                        <span class="file_attach" style="color:#ff0000; font-size:10px;">(Max Size : 3
                                            MB &
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
                                                <input id="upload_input" type="file" name="fileupload[]"
                                                    class="d-none" multiple="multiple"
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
                                                <button class="btn button_save" id="buttonaction"
                                                    name="buttonaction">Save Draft
                                                </button>
                                                <button class="btn button_finalise" id="finalisebtn">Forward</button>
                                            </div>
                                        </div>
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
            <div class="card-header card_header_color">Audit Para Management Details</div>
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
    let maxfilecount = 3;
    let sessionuserid = '<?php echo $sessionuserid; ?>';
    let lagacyparatype = '<?php echo $lagacyparatype; ?>';
    let normalparatype = '<?php echo $normalparatype; ?>';
    let year_div = ''
    let selectedYear = ''
    let selectedpara = ''
    let followupid = '<?php echo $followupid; ?>';
    let paranumber = '<?php echo $paranumber; ?>';
    let paratype = '<?php echo $paratype; ?>';
    let paraid = '<?php echo $paraid; ?>';
   
    let pararejectcount = '<?php echo $pararejectcount; ?>';
    const processCodes = ['K', 'A', 'I', 'F', 'U', 'B', 'D', 'DH', 'B', 'L', 'PH', 'V',
        'PA'
    ];
let auditeeLiability_enabled = ['U', 'L', 'E'];    
let currentprocesscode = '';
    let months = @json($months);
    let enable_dlcprocess = '<?php echo $enable_dlcprocessed; ?>';


    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            $('#loader').addClass('d-none');

        }
        fetch_paramanagement_auditee(followupid, paranumber,
            'edit', 'Y', '', paratype);


    });


    function onchange_actiontaken(val, selectedmonth, selectedyear) {

        var actiontaken = val || $('#actiontaken').val();

        if (actiontaken == 'Y') {
            $('#actiontaken_div').css('display', 'block');

            $('#lastactionmonth').val(selectedmonth);
            $('#lastactionyear').val(selectedyear);
            $('#lastactionmonth,#lastactionyear').select2();

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
        var MAX_FILE_SIZE_MB = 3;
        var MAX_FILE_COUNT = 3;

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
                $errorElement = $('<div class="file-error" style="color: red; font-size: 12px;"></div>').appendTo(
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
                    //alert('Invalid file type. Please upload PDF, Excel files only.');
                    
			alert('Invalid file type. Please upload PDF');
			return; // Don't increment fileCount here
                }

                // Check file size
                if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                    alert('File size exceeds the 3MB limit.');
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


        // async function processFile(file, $newFile) {
        //     await addFileToNewInput(file, $newFile.find("input")[0]);
        //     await addSrcToPreview(file, $newFile.find("img")[0]);
        //     await setFileDetails(file, $newFile);
        // }

        async function processFile(file, $newFile) {
            let imgElement = $newFile.find("img")[0];

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


    //----------------------------onchange ---------------------------//
    function getcategoryBasedOnSerious(serious, selectedRegioncode = null) {

        const lang = getLanguage();

        // const districtDropdown = $('#district');
        const categoryDropdown = $('#category');
        // const institutionDropdown = $('#institution');

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

        // const districtDropdown = $('#district');
        const subcategoryDropdown = $('#subcategory');
        // const institutionDropdown = $('#institution');

        const lang = getLanguage();
        subcategoryDropdown.html(`
                <option value="" data-name-en="Select SubCategory" data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select SubCategory'}
                </option>
            `);
        // districtDropdown.html('<option value="">Select District Name</option>');
        // institutionDropdown.html('<option value="">Select Audit Office</option>');

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

    //---------------------------------------CK-editor-------------------------------------------------------------//

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
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
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
                'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
                'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                'PasteFromOfficeEnhanced', 'CaseChange','uploadImage'
            ]
        })
        .then(e => {
            editor = e;
        })
        .catch(error => {
            console.error(error);
        });

    //------------------------------dynamic append liability-----------------------------//
$(document).ready(function () {

    // Initial load
    enable_liability_details();

    // Bind change event for BOTH radio groups
    $('input[name="auditee_liability"], input[name="auditee_Retirement_type"]')
        .off('change.enableLiability')
        .on('change.enableLiability', function () {
            enable_liability_details();
        });

});


$('input[name="auditee_liability"]').on('change', function() {
    toggleAuditeeLiabilityType($(this).val(), $('input[name="auditee_Retirement_type"]:checked').val());
});

function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag,
        retiredflag, retirementyear, retiremntmonth,
        container, lagacyflag) {

        if (event) {
            event.preventDefault();
        }

    const liabilityVal = $('input[name="auditee_liability"]:checked').val();
    const retirementVal = $('input[name="auditee_Retirement_type"]:checked').val();

    $('#paraliabilitycontainer').show();

    if (liabilityVal === 'Y') {
        // If liability is YES
        $('#auditee_Retirement_type_div').hide();  // Hide retirement options
        $('#auditee_liability_type_div').hide();   // Hide liability type div if needed
        $('#paraliabilitycontainer').show();
    } else if (liabilityVal === 'N') {
        // If liability is NO
        $('#auditee_Retirement_type_div').show();  // Show retirement options
        $('#auditee_liability_type_div').show();   // Show liability type div if needed
        $('#auditee_liability_type').prop('disabled', true); // disable input if needed

        // Default retirement to 'Y' if not already selected
        const currentRetirement = $('input[name="auditee_Retirement_type"]:checked').val();
        if (!currentRetirement) {
            $('input[name="auditee_Retirement_type"][value="Y"]').prop('checked', true);
        }

        if (retirementVal === 'Y' || !currentRetirement) {
            // If retirement is YES (or defaulted), show table
            $('#paraliabilitycontainer').show();
        } else if (retirementVal === 'N') {
           pushAllLiabilityIdsToDeleted();
                $('#paraliabilitycontainer').empty();
            // If retirement is NO, hide table
            $('#paraliabilitycontainer').hide();
        }
    } else {
        // Default fallback
        $('#paraliabilitycontainer').hide();
    }


        var containername = container;
        // determine current auditee liability selection; if 'N', amount column is hidden and not required
        const auditeeLiability = $('input[name="auditee_liability"]:checked').val();
        let isChecked = (statusflag === 'Y') ? 'checked' : '';
        // let isdisabled = '';

        let isdisabled = (action === 'view') ? 'disabled' : '';
	

        const isLegacy = lagacyflag === 'Y';
        const isDisabled = isdisabled === 'disabled';

        const isEditableProcess =
            auditeeLiability_enabled.includes(currentprocesscode);

        const isNoProcess = !currentprocesscode;

      
        let is_detdisabled =
            isDisabled || (isLegacy && (isEditableProcess || isNoProcess));

        const islagacydisabled = is_detdisabled ? 'disabled' : '';

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
            newWorkRow += ``;
        }
        const currentYear = @json($currentYear);
        const endretirementYear = @json($endretirementYear);

        newWorkRow += `
                  <div class="row work-row p-2 border-bottom mb-3" id="row${rowCount}">
                      <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">
<input type="hidden" id="lagacyflag${rowCount}" name="lagacyflag${rowCount}" value="${lagacyflag}">
                      <!-- Type Column -->
                      <div class="col-md-2 col-12 mb-2">

                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block sm-2">Type</label>` : ''}
			<select class="form-select form-select-sm" name="notype${rowCount}" value="${name}" ${islagacydisabled}
                        onchange="onchange_notype(this, ${rowCount})" >
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
                         <input type="text" class="form-control form-control-sm removesplchar_number alpha_numeric" maxlength="22" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="Number" ${isdisabled}>
                      </div>

                      <!-- Name Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Name</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Name</label>` : ''}
<input type="text" class="form-control form-control-sm removesplchar_text name" maxlength="50"  id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${islagacydisabled}>
                      </div>

                      <!-- Designation Column -->
                      <div class="col-md-1 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Designation</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Designation</label>` : ''}
<input type="text" class="form-control form-control-sm  removesplchar_desig name" maxlength="50"  name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${islagacydisabled}>                      </div>

                      <!-- Amount Column: show only when auditee liability = 'Y' -->
                     <div class="col-md-1 col-12 mb-2 liability_amount">
                        <label class="form-label d-md-none d-block">Amount</label>
                        ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Amount</label>` : ''}
                        <input type="text"
                            class="form-control form-control-sm removesplchar_numberwithdecimal numberswithdecimal amount-field"
                            maxlength=12
                            name="amount${rowCount}"
                            id="amount${rowCount}"
                            value="${amount || ''}"
                            ${islagacydisabled}
                            placeholder="Amount">
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
                     <div class="col-md-2 col-12 mb-2 hide_this" id="retirementdetails_div_${rowCount}">
                         ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Retirement Details</label>` : ''}
                       <div class="input-group">
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




                      <!--Action Column-->

                     <div class="col-md-1 mb-2 action-row ms-auto text-end vr-start ps-3">
                            ${rowCount == 0 && ((action === 'entry') )? `<label class="form-label d-md-block d-none">Action</label>` : ''}

                            <div class="d-flex flex-md-row flex-column gap-1 justify-content-end">
${rowCount > 0 && action === 'entry' ? `
                                    <button type="button"
                                        class="mar_left btn btn-danger fw-medium deleteRowBtn"
                                        onclick="deleteRow(${rowCount})"
                                        ${lagacyflag === 'Y' ? 'disabled title="Legacy rows cannot be deleted"' : ''}>
                                        <i class="ti ti-trash"></i>
                                    </button>` : ''}
                                 ${action !== 'view' && (action !== 'view') ? `
                                           <button type="button" class=" mar_left btn btn-success fw-medium addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '', '', '', '','','${containername}','')">
                                               <i class="ti ti-circle-plus"></i>
                                           </button>` : ''}
                            </div>

                     </div>

                  </div>
                  `;

        //Append new row to the container (assuming there's a container like #workRowContainer)
        //document.getElementById('workRowContainer').insertAdjacentHTML('beforeend', newWorkRow);




        $("#" + containername).append(newWorkRow);
        toggleAmountColumn();



        // Hide the add button in the previous row
        if (rowCount > 0) {
            $(`#row${rowCount - 1} .addRowBtn`).hide();
        } else if (rowCount == 0) {
            $(`#row${rowCount } .addRowBtn`).show();
        }


        // ===== Set retirement values on edit =====
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

        // Apply validation to the newly added row's fields
        applyValidationToNewFields(`notype${rowCount}`, 'Select Number Type');
        applyValidationToNewFields(`name${rowCount}`, 'Enter Name');
        applyValidationToNewFields(`gpfno${rowCount}`, 'Enter Number');
        applyValidationToNewFields(`designation${rowCount}`, 'Enter Designation');
        if (auditeeLiability === 'Y') {
            applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');
        }
        applyValidationToNewFields(`retiredflag${rowCount}`, 'Select Retirement');
        applyValidationToNewFields(`retirementyear${rowCount}`, 'Select Year ');
        applyValidationToNewFields(`retirementmonth${rowCount}`, 'Select Month');

        rowCount++;

        restrictSpecialChars(".removesplchar_text");
        restrictSpecialChars(".removesplchar_desig");
        restrictSpecialChars(".removesplchar_number");
        restrictSpecialChars(".removesplchar_numberwithdecimal");

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

        let liabilityId = $("#liabilityid" + rowId).val();

        // If this row already exists in DB (has ID), track it as deleted
        if (liabilityId && liabilityId !== '') {

            // Get existing deleted IDs
            let deletedIds = $('#deleted_liabilityid').val();

            // Convert to array safely
            let deletedArray = deletedIds ? deletedIds.split(',') : [];

            // Avoid duplicates
            if (!deletedArray.includes(liabilityId)) {
                deletedArray.push(liabilityId);
            }

            // Update hidden field
            $('#deleted_liabilityid').val(deletedArray.join(','));
        }

        // Remove row from DOM
        $("#row" + rowId).remove();

        rowCount--;

        checkRowLimit();
    }

function applyValidationToNewFields(inputNames, message) {

        let validator = $("#para_form").data("validator");
        if (!validator) {
            $("#para_form").validate({
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            validator = $("#para_form").data("validator");
        }

        // 🔹 If array → treat as group (year + month)
        if (Array.isArray(inputNames) && inputNames.length === 2) {

            const yearName = inputNames[0];
            const monthName = inputNames[1];

            let $year = $("[name='" + yearName + "']");
            let $month = $("[name='" + monthName + "']");

            if ($year.length && $month.length) {

                // remove old rules
                $year.rules("remove");
                $month.rules("remove");

                // ✅ apply group rule only on first field
                $year.rules("add", {
                    yearMonthRequired: [yearName, monthName],
                    messages: {
                        yearMonthRequired: message
                    }
                });

                // ✅ revalidate when either changes
                $year.add($month).off('change.retire').on("change.retire", function() {
                    $year.valid(); // validate group once
                });
            }

        } else {
            // 🔹 Single field normal validation
            const inputName = Array.isArray(inputNames) ? inputNames[0] : inputNames;
            let $input = $("[name='" + inputName + "']");

            if ($input.length) {
                $input.rules("remove");
                $input.rules("add", {
                    required: true,
                    messages: {
                        required: message
                    }
                });

                $input.off('change.single').on("change.single", function() {
                    $(this).valid();
                });
            }
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
                .on("keypress", function (event) {
                    let char = String.fromCharCode(event.which);
                    let value = this.value;

                    if (selector === '.removesplchar_text') {
                        const input = event.target;
                        const char = event.key;

                        // Allow letters, Tamil and space
                        if (!/^[a-zA-Z\u0B80-\u0BFF ]$/.test(char)) {
                            event.preventDefault();
                            return;
                        }

                        // Auto convert first character to uppercase
                        setTimeout(function () {
                            if (input.value.length > 0) {
                                input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
                            }
                        }, 0);
                    }
                    else if (selector === '.removesplchar_desig') {

                        const input = event.target;
                        const value = input.value;
                        const char = event.key;
                        const lastChar = value.slice(-1);

                        // Allow letters, numbers, hyphen and space
                        if (!/^[a-zA-Z0-9\- ]$/.test(char)) {
                            event.preventDefault();
                            return;
                        }
                        if (value.length >= 50) {
                            event.preventDefault();
                            return;
                        }
                        // Prevent continuous hyphen
                        if (char === '-' && lastChar === '-') {
                            event.preventDefault();
                            return;
                        }

                        // Prevent continuous digits
                        if (/[0-9]/.test(char) && /[0-9]/.test(lastChar)) {
                            event.preventDefault();
                            return;
                        }

                        // Prevent continuous spaces
                        if (char === ' ' && lastChar === ' ') {
                            event.preventDefault();
                            return;
                        }

                        // Auto capitalize first letter
                        setTimeout(function () {
                            if (input.value.length > 0) {
                                input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
                            }
                        }, 0);
                    }

                    else if (selector === '.removesplchar_number') {
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
                        if (value.length >= 16) {
                            event.preventDefault();
                            return;
                        }
                        if (value.length === 0 && char === '0') {
                            event.preventDefault();
                            return;
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
                .on("paste", function (e) {
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
        $('#paraid').val(paraid)
        // fetch_paramanagement_auditee(followupid, paranumber,
        //     'edit', 'Y', '', paratype);
    });
    $("#translate").change(function() {

        lang = getLanguage('Y');
        switchChecklistLanguage(lang)
        // updateTableLanguage(lang);


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
            let actionname = lang == 'ta' ? data.actiontname : data.actionename;
            let rtd_remarks = data.remarks ?? '-';


            const role = roleMap[data.actroleactioncode] || roleMap['SL'];

            // Remarks parsing
            let remarkContent = "No remarks provided";
            let showremarks_div = false;
            let showaccordion = false;

            //hide remarks div if it is droppped action on auditees
            showremarks_div = data.actioncode == '01' ? false : true;

            //to hide drop history on auditee side
            showaccordion = (data.actioncode == '01' && data.actroleactioncode == 'A') ? false : true;

            try {

                if (data.para_remarks) {
                    let parsed = JSON.parse(data.para_remarks);
                    remarkContent = parsed.content || remarkContent;
                } else if (data.para_historyremarks) {
                    remarkContent = data.para_historyremarks;
                }


            } catch (e) {
                remarkContent = data.para_remarks || remarkContent;
            }

            //remarks div
            let remarksHTML = '';
            if (data.actroleactioncode == 'A' || data.actroleactioncode == 'AD' || data.actroleactioncode ==
                'I') {


                if (showremarks_div) {
                    remarksHTML = `
                 <div class="col-md-12">
                     <label><strong>Remarks:</strong></label>
                     <div class="editor-container border p-2 rounded bg-light">
                         ${remarkContent}
                     </div>
                 </div>`;
                }
            }
            //action taken div
            let action_div = "";
            if (['A', 'AD', 'DL', 'RC', 'SL', 'DE'].includes(data.actroleactioncode)) {
                action_div = `<div class="col-md-6">
                                <label><strong>Action to be taken</strong></label>
                               <input class="form-control lang_value" data-en="${actionname}"  data-ta="${actionname}" value="${actionname}" disabled>

                            </div>`;
            }


            let meeting_div = ""


            // Attachments (only if present)
            let attachmentHTML = "";
            if (data.auditeefileupload && data.auditeefileupload != '---') {
                files = getfile(data.auditeefileupload)
                attachmentHTML = `
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

            // Accordion markup
            let accordion = document.createElement("div");
            accordion.classList.add("accordion", "my-2");
            accordion.id = accordionId;
            accordion.innerHTML = showaccordion ? `
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

                           ${remarksHTML}
                            ${action_div}
                            ${retirement_remarks_HTML}
                        </div>
                        ${attachmentHTML}
                    </div>
                </div>
            </div>
            <hr class="p-2">
            ` : '';

            container.appendChild(accordion);
            if (data.auditeefileupload) {
                UploadedFileList(files, '', `history_filediv_${index}`, '', 'fileuploadid');
            }
        });
    }




    function fit_currentdata(currentdata) {


        if (!currentdata.encrypted_paraid) {
            // New insert
            $("#button_set").css("visibility", "visible");
            change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');

        } else {
            // Para already exists
            switch (currentdata.processcode) {
                case 'F':
                case 'K':
                    $("#button_set").css("visibility", "hidden");
                    $("#add-file-btn").css("visibility", "hidden");
                    break;
                 case 'B':
                case 'BE':
                case 'PA':
                case 'U':
                case 'L':
                case 'SA':
                    if (sessionuserid == currentdata.updatedby) {
                        // Editable by same user

                        change_button_as_update('para_form', 'action', 'buttonaction', 'display_error', '', '');
                        $("#button_set").css("visibility", "visible");
                    } else {
                        // Different user → treat as insert
                        $("#button_set").css("visibility", "visible");
                        change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');
                    }
                    break;

          
               
                case 'I':
                    if ((currentdata.rejectcount < pararejectcount)) {
                        if (sessionuserid == currentdata.updatedby) {
                            // Editable by same user

                            change_button_as_update('para_form', 'action', 'buttonaction', 'display_error', '', '');
                            $("#button_set").css("visibility", "visible");
                        } else {
                            // Different user → treat as insert
                            $("#button_set").css("visibility", "visible");
                            change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');
                        }
                        break;
                    } else {

                        $("#button_set").css("visibility", "hidden");
                        $("#add-file-btn").css("visibility", "hidden");
                        break;
                    }



                default:

                    // Any other processcode → update
                    $("#button_set").css("visibility", "visible");
                    change_button_as_update('para_form', 'action', 'buttonaction', 'display_error', '', '');
                    break;
            }
        }


        $('#rejectcount').val(currentdata.rejectcount);

        // $('#retiredflag').val(currentdata.retiredflag);
        // if (currentdata.retiredflag == 'Y') {
        //     $('#retirementyear').val(currentdata.retirementyear);
        //     onchange_retirementstate(currentdata.retiredflag)
        //     onchange_retirement_yr(currentdata.retirementyear, currentdata.retirementmonth)
        // }

        $('#paraid').val(currentdata.encrypted_paraid);
        $('#n_yearcode').val(currentdata.yearcode);
        $('#processcode').val(currentdata.processcode);
        $('#rejoindercycle').val(currentdata.rejoindercycle);
        $('#rejoinderstatus').val(currentdata.rejoinderstatus);
        // change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');




          if (currentdata.encrypted_paraid && (['U', 'E', 'D', 'B', 'F', 'L', 'PH', 'V', 'PA', 'SA', 'BE'].includes(
            currentdata.processcode) &&
                sessionuserid == currentdata
                .updatedby)) {
            auditeeremarks.setData(currentdata.para_remarks);

            var UploadedFileList_withaction = ''

            $('#file-list-container').toggle(!!currentdata.fileuploaddet);
            if (currentdata.fileuploaddet) {
                files = getfile(currentdata.fileuploaddet)
                if (currentdata.processcode == 'E' || currentdata.processcode == 'U' || currentdata.processcode ==
                    'L' || currentdata.processcode ==
                    'B' || currentdata.processcode ==
                    'PA') {
                    UploadedFileList_withaction = 'edit'
                } else {
                    if (auditeeremarks) auditeeremarks.enableReadOnlyMode('customLock');
                }
                fileupload_hiddenid = 'fileuploadid';
                UploadedFileList(files, UploadedFileList_withaction, 'file-list-container',
                    'Y',
                    fileupload_hiddenid)
                $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
                if (['F', 'D', 'K', 'DH', 'PH', 'V'].includes(currentdata.paraprocesscode)) {
                    $('#file-input-container').hide()
                    $('#add-file-btn').hide()
                }
            } else if (['F', 'D', 'K', 'DH', 'PH', 'V'].includes(currentdata.paraprocesscode)) {

                $('#file-input-container').hide()
                $('#add-file-btn').hide()
            } else {
                // $('.file_attach').hide()
                $('#file-input-container').show()
                $('#add-file-btn').show()
                appendFilePreview()

            }

        }

    }






  function fit_status(processcode, rejoinderstatus) {
        const statusMap = {
            E: 'STATUS : Entry',
            F: 'STATUS : Forwarded to PSA Auditor',
            K: 'STATUS : Forwarded to PSA AD',
            U: 'STATUS : Forwarded to Auditee',
            A: 'STATUS : Dropped',
            I: 'STATUS : Rejected',
            D: 'STATUS : Forwarded to District HLC',
            B: 'STATUS : Forwarded from District HLC',
            DH: 'STATUS : Forwarded to Department HLC',
            IS: 'STATUS : Forwarded to State HLC',
            AE: 'STATUS : Forwarded to Department HLC',
            AS: 'STATUS : Forwarded to State HLC',
            PH: 'STATUS : Pending at HLC',
            L: 'STATUS : Forwarded from Committee',
            V: 'STATUS : Forwarded to Committee',
            PS: 'STATUS : Forwarded to Auditee(Responsibility Removal)',
            SA: 'STATUS : Forwarded from State HLC',
            B: 'STATUS :  Forwarded from DLC',
            B: 'STATUS :  Forwarded from Department DLC',

        };

        let status_msg = statusMap[processcode] || 'STATUS : Entry';

        const isRejoinder =
            rejoinderstatus === 'Y' &&
            !['A', 'I'].includes(processcode);

        if (isRejoinder) {
            status_msg += ' - (Rejoinder)';
        }

        $('#status_msg').text(status_msg);
    }




    function clear() {
        // clear by id (explicit)


        $('#followupid').val('');
        $('#paraid').val('');
        $('#paranumber').val('');
        $('#catcode').val('');
        $('input#deactive_fileid').val(''); // if duplicates, only first matched by id
        $('input#active_fileid').val('');
        $('#seriesno').val('');
        $('#auditeeins_subcategoryid').val('');
        $('#rejoindercycle').val('');
        $('#rejoinderstatus').val('');
        $('#fileuploadid').val('');
        $('#processcode').val('');
        auditeeremarks.setData('');
        appendFilePreview();

        $('#deactive_fileid').val('');
        $('#active_fileid').val('');

        $('#fileuploadid').val('');

        $('#file-input-container').hide()
        $('#file-input-container').empty();
        //  $('#add-file-btn').hide()

        //clear upload filecontainer
        $('#file-list-container').empty();
        $('#file-list-container').hide();

    }

 
 function fetch_paramanagement_auditee(followupid, paranumber, action, createnewone, yearcode, paratype) {

        var paratype = paratype || $('#paratype').val();
        var paraid = $('#paraid').val();
        if (!paraid) {
            $('#paraid').val(paraid)
        }

        selectedpara = $('#n_gen_parano')
        selectedYear = $('#n_yearcode').val();
        var yearcode = yearcode || $('#n_gen_parano').val();
        var instid = $('#instid').val();

        $.ajax({
            url: '/lagacy/fetch_paramanagement_auditee',
            type: 'POST',
            data: {
                followupid: followupid,
                instid: instid,
                action: action,
                'yearcode[]': yearcode,
                paratype: paratype,
                paraid: paraid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loader').removeClass('d-none');
		$('#button_set').css("visibility", "visible");
                disenableformfields()
                clear()
                // return;
                if (action == 'P') {
                    $('#status_row').hide()
                    const gen_paradropdown = selectedpara;
                    gen_paradropdown.empty();
                    gen_paradropdown.append(`
                     <option value="" data-name-en="---Select Para Number---" data-name-ta="---Select Para Number---">
                         --- Select Para Number ---
                     </option>
                    `)
                    response.data.forEach(item => {

                        gen_paradropdown.append(`
                            <option value="${item.paranumber}"   data-followupid="${item.encrypted_followupid}" > ${item.paranumber}
                            </option> `);


                    });

                }
                if (action == 'edit' || action == 'fetchwithedit') {
                    appendFilePreview();

                    var paradet = response.paradet['data'];
                    var historydata = response.paradet['historydata']
                    if (paradet && paradet.length > 0) {

                        var currentdata = paradet[0];
                        $('#status_row').show()

                        currentprocesscode = currentdata.processcode;
                        var rejoinderstatus = currentdata.rejoinderstatus;
                        fit_status(currentprocesscode, rejoinderstatus)
                        fit_currentdata(currentdata)

                        if (
                            currentdata.rejoinderstatus === 'Y' ||
                            processCodes.includes(currentdata.processcode)
                        ) {

                            if (historydata && (historydata.length > 0)) {

                                populatehistorydata(historydata)
                            }
                        }

                        if ((['K', 'A', 'D', 'F', 'DH', 'PH', 'V'].includes(currentdata.processcode)) ||
                            (currentdata.processcode == 'I' && currentdata.rejectcount ==
                                pararejectcount)) {
                            $('#button_set').css("visibility", "hidden");
                            $('#add-file-btn').css("visibility", "hidden");
                            $('input[name="auditee_liability"]').prop('disabled', true);
                            $('#auditee_formdiv').hide()

                            disable_retiremnt();
                        } else if (currentdata.processcode == 'I' && currentdata.rejectcount <
                            pararejectcount ) {
                            $('#button_set').css("visibility", "visible");
                            $('#add-file-btn').css("visibility", "visible");
                            $('#auditee_formdiv').show();
 $('input[name="auditee_liability"]').prop('disabled', false);

                        }
                    } else {
                        $('#auditee_formdiv').show()
                        $('#paraid').val()
                        $('#paranumber').val()
                        $('#followupid').val()
                        $('#file-input-container').show()
                        $('#button_set').css("visibility", "visible");
                        $('#add-file-btn').css("visibility", "visible");
                        auditeeremarks.disableReadOnlyMode('customLock');

                    }
                    if (response.data && response.data.length > 0) {
                        var paradata = response.data[0];
                        if (paradata.audityear) {
                            var year_div = $('#n_yearcode');
                            var years = paradata.audityear;

                            if (typeof years === 'string') {
                                try {
                                    years = JSON.parse(years);
                                } catch (e) {
                                    // fallback if malformed
                                    years = [years];
                                }
                            }

                            years = Array.isArray(years) ? years.flat(Infinity).map(String) : [String(
                                years)];

                            var current = year_div.val() || [];
                            var currentYears = Array.isArray(current) ? current.map(String) : [String(
                                current)];

                            var updatedYears = [...new Set([...currentYears, ...years])];

                            if (updatedYears.length !== currentYears.length) {
                                year_div.val(updatedYears);
                                year_div.select2();

                                // if (action == 'edit') {
                                //     if (years.length > 1) {

                                //         var period = paradata.audit_period

                                //         var msg = 'Institution has been audited in the following years ' +
                                //             period

                                //         passing_alert_value('Alert',
                                //             msg,
                                //             'confirmation_alert',
                                //             'alert_header', 'alert_body', 'confirmation_alert');
                                //     }
                                // }
                                // Show alert only if multiple years found

                            }
                        }

                        $('#status_row').css('display', 'block');
                        fix_formfield_values(paradata);
                    } else {
                        alert('Para details not found')
                    }

                    appendFilePreview();

                    if (selectedYear) {

                        $('#paratype').val(paratype);
                        selectedpara.val(paranumber)

                        if (paratype == lagacyparatype) {
                            $('#yearcode').val(selectedYear);
                        }
                    }


                }
            },
            complete: function() {

                $('#loader').addClass('d-none');
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }



    function fix_formfield_values(firstItem) {
		if (['PA', 'L'].includes(firstItem.processcode) && firstItem.auditee_liability != 'NULL') {
            $('#liability_certificate_div').show()
        } else {
            $('#liability_certificate_div').hide()
        }
            rowCount = 0;
 const isDlcProcess = enable_dlcprocess.includes(firstItem.processcode);

        if (((firstItem.stateofparacode == '02' || firstItem.stateofparacode == '03' || firstItem.stateofparacode ==
                '04') && firstItem.paratype == lagacyparatype) && (!isDlcProcess)) {
            $('#finalisebtn').hide();
            $('#auditee_formdiv').hide();

        } else {
            $('#finalisebtn').show()
        }
            $('#paraliabilitycontainer').empty();
            $('#slipdetails').val(firstItem.slipdetails);
            $('#parano').val(firstItem.paranumber);
            $('#currentslipnumber').val(firstItem.paranumber);
            $('#paranumber').val(firstItem.paranumber);
            $('#followupid').val(firstItem.encrypted_followupid);
            $('#amount_involved').val(firstItem.amtinvolved);
            $('#serious').val(firstItem.irregularitiescode);
            $('#typeofparacode').val(firstItem.typeofparacode);
            $('#stateofparacode').val(firstItem.stateofparacode);
            $('#liabilityval').val(firstItem.liability);

            $('#typeofparacode,#stateofparacode').select2();

            $('#schemename').val(firstItem.auditeeschemecode);
            if (firstItem.lastactionmonth) {

                $('#actiontaken').val('Y');
                $('#actiontaken').select2();

                onchange_actiontaken('Y', firstItem.lastactionmonth, firstItem.lastactionyear)

            } else {
                $('#actiontaken').val('N');
                $('#actiontaken').select2();

                onchange_actiontaken('N', firstItem.lastactionmonth, firstItem.lastactionyear)
            }
            getcategoryBasedOnSerious(firstItem.irregularitiescode, firstItem.irregularitiescatcode);
            getsubcategoryBasedOnCategory(firstItem.irregularitiescatcode, firstItem.irregularitiessubcatcode);

            $('#severityid').val(firstItem.severitycode);
            $('#fileuploadstatus').val('N');

            $('input[name="scheme"][value="' + firstItem.schemastatus + '"]').prop('checked', true);

            if (firstItem.schemastatus === 'Y') {
                $('#severityDiv').show();
            } else {
                $('#severityDiv').hide();

            }

            // Normalize liabilitydel safely to avoid undefined / null and trailing commas/spaces
            let liabilitydel = (firstItem.liabilitydel || '').toString().trim();
            // normalize comma spacing and remove leading/trailing commas
            liabilitydel = liabilitydel.replace(/\s*,\s*/g, ',').replace(/(^,|,$)/g, '');


            $('input[name="liability"][value="' + firstItem.liability + '"]').prop('checked', true);
            $('input[name="auditee_liability"][value="' + firstItem.auditee_liability + '"]').prop('checked', true);
            // ensure dropdown visibility reflects programmatic change
 $('input[name="auditee_Retirement_type"][value="' + firstItem.liabilty_type + '"]').prop('checked', true);
  toggleAuditeeLiabilityType(firstItem.auditee_liability, firstItem.liabilty_type, liabilitydel);
$('input[name="liability"]').prop('disabled', true);
      
             if (firstItem.auditee_liability != 'NULL' || firstItem.liability === 'Y') {
                $('#paraliabilitycontainer').show()
                $('#auditeeliability_div').show();
                if (liabilitydel) {
                    var para_processcode = firstItem.processcode

if (currentprocesscode) {

                    liabilityaction = (auditeeLiability_enabled.includes(currentprocesscode) || (currentprocesscode ==
                        'I' && firstItem
                        .rejectcount < pararejectcount)) ? 'entry' : 'view';

                } else {

                    var liabilityaction = (auditeeLiability_enabled.includes(para_processcode) || (para_processcode ==
                        'I' && firstItem
                        .rejectcount < pararejectcount) || (para_processcode == 'null' || para_processcode ==
                        '' || para_processcode == null)) ? 'entry' : 'view';
                } 
		if (auditeeLiability_enabled.includes(para_processcode) || (para_processcode == 'I' && firstItem
                        .rejectcount < pararejectcount)) {
                    $('input[name="auditee_liability"]').prop('disabled', false);
                    $('input[name="auditee_Retirement_type"]').prop('disabled', false);
              	 } else if (['I', 'U', 'F', 'D', 'B', 'DL', 'L', 'PH', 'V', 'PA'].includes(para_processcode)) {
                        $('input[name="auditee_liability"]').prop('disabled', true);
			$('input[name="auditee_Retirement_type"]').prop('disabled', true);
                    } else {
                        $('input[name="auditee_liability"]').prop('disabled', false);
			$('input[name="auditee_Retirement_type"]').prop('disabled', false);
                    }

                    var liablityparts = liabilitydel.split(",");
                    liabilityid = '';
                    // Step 2: Iterate over the parts and split each by the dash (-)
                    for (var i = 0; i < liablityparts.length; i++) {
                        var liablity = liablityparts[i].split("|~|"); // Split by the dash (-)

                        let isLast = (i === liablityparts.length - 1);


                        addNewWorkRow(event, liabilityaction, liablity[0], liablity[2], liablity[1], liablity[
                            3],
                        liablity[
                            4], liablity[5], isLast, liablity[7], liablity[8], liablity[9], liablity[
                            10],
                        'paraliabilitycontainer',liablity[
                            11]);


                        liabilityid += liablity[5] + ',';

                    }
                    if (liabilityid.endsWith(',')) {
                        liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
                    }
                    $('#liabilityid').val(liabilityid);
                } else {
                    $('#auditeeliability_div').show();
                    addNewWorkRow(event, 'entry', '', '', '', '', '', '', '', '', '', '', '', 'paraliabilitycontainer');

                }

            } else {
                // Always show the auditee liability container even when auditee_liability is 'N'
                $('#paraliabilitycontainer').show();
                // Ensure there's at least one work-row present
                if ($('#paraliabilitycontainer .work-row').length === 0) {
                    addNewWorkRow(event, 'entry', '', '', '', '', '', '', '', '', '', '', '', 'paraliabilitycontainer');
                }
                if (currentprocesscode == 'E') {
                    $('input[name="auditee_liability"]').prop('disabled', false);
$('input[name="auditee_Retirement_type"]').prop('disabled', false);
                } else if (currentprocesscode == 'I' || currentprocesscode == 'U' || currentprocesscode == 'L',
                    currentprocesscode == 'PA') {
                    $('input[name="auditee_liability"]').prop('disabled', true);
$('input[name="auditee_Retirement_type"]').prop('disabled', true);
                } else {
                    $('input[name="auditee_liability"]').prop('disabled', false);
$('input[name="auditee_Retirement_type"]').prop('disabled', false);
                }

            }
            if (currentprocesscode == 'E') {
                $('input[name="auditee_liability"]').prop('disabled', false);
$('input[name="auditee_Retirement_type"]').prop('disabled', false);
            } else if (['U','L'].includes(
                currentprocesscode) ||(currentprocesscode == 'I' && firstItem.rejectcount<pararejectcount ))
                {
                      $('input[name="auditee_liability"]').prop(false);
                }
 else if ([ 'D', 'B', 'F', 'DE', 'PH', 'A', 'AE', 'AS', 'V', 'PA'].includes(
                currentprocesscode)) {
                $('input[name="auditee_liability"]').prop('disabled', true);
$('input[name="auditee_Retirement_type"]').prop('disabled', true);
            } else {

                $('input[name="auditee_liability"]').prop('disabled', false);
$('input[name="auditee_Retirement_type"]').prop('disabled', false);
            }

            getminorobjection(firstItem.mainobjectionid, firstItem.subobjectionid, 'minorobjectioncode',
                'mainobjectionid');

            $('#mainobjectionid').val(firstItem.mainobjectionid);
            $('#mainobjectionid').select2();
            $('#typeofauditcode').val(firstItem.typeofauditcode)
            $('#part_no').val(firstItem.paranumber)


            editor.setData(firstItem.remarks);

            fileviewid = 'file-lagacylist-container';

            fileupload_hiddenid = '';

            fileidstore = 'Y';
            fileupload_hiddenid = 'fileuploadid';
            $('#file-list-container').toggle(!!firstItem.auditeefileupload);

            if (firstItem.auditorfileupload) {
                files = getfile(firstItem.auditorfileupload)
                UploadedFileList(files, '', fileviewid, fileidstore, fileupload_hiddenid)
                // $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
            } else {
                fileCount = 0;
                $('.para_attach').hide()
                $('#file-input-container').hide()
                $('#file-input-container').empty();
                //  $('#add-file-btn').hide()

                //clear upload filecontainer
                $('#file-lagacylist-container').empty();
                $('#file-lagacylist-container').hide();
            }
if ((firstItem.stateofparacode == '02' || firstItem.stateofparacode == '03' || firstItem.stateofparacode ==
                '04') && firstItem.paratype == lagacyparatype && (!isDlcProcess)) {
            $('#paraliabilitycontainer,#auditeeliability_div').hide();
        }
        }

    function disenableformfields() {
        $('#mainobjectionid').attr('disabled', true);
        $('#subobjectionid').attr('disabled', true);
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
        if (editor) editor.enableReadOnlyMode('customLock');


$('input[name="name"]').prop('disabled', true);
        $('input[name="notype"]').prop('disabled', true);
        // Keep amount inputs editable even when other fields are disabled
        $('input[name="retiredflag"]').prop('disabled', true);
        $('input[name="retirementyear"]').prop('disabled', true);
        $('input[name="retirementmonth"]').prop('disabled', true);
        $('input[name="liability"]').prop('disabled', true);
        $('input[name="scheme"]').prop('disabled', true);
        $('#liabilityname').attr('disabled', true);
        $('#liabilitygpfno').attr('disabled', true);
        $('#liabilitydesig').attr('disabled', true);

        document.getElementById('add-file-btn').classList.add('hidden');

        // $('#buttonaction').hide();
        $('#approvebtn').hide();
    }



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

    /***********************************jquery Validation**********************************************/
    const $para_form = $("#para_form");


    $("#para_form").validate({
        rules: {
            lastactionyear: {
                required: true,
            },
            lastactionmonth: {
                required: true,
            },
            typeofauditcode: {
                required: true,
            },
            mainobjectionid: {
                required: true,
            },
            subobjectionid: {
                required: true,
            },

            stateofparacode: {
                required: true,
            },
            typeofparacode: {
                required: true,
            },
            parano: {
                required: true,
            },
            severityid: {
                required: true,
            },
            liability: {
                required: true,
            },

            slipdetails: {
                required: true,
            },
            typeofauditcode: {
                required: true,
            },
            yearcode: {
                required: true,
            },
            paranumber: {
                required: true,
            },
            schemename: {
                required: true,
            },
            serious: {
                required: true,
            },
            category: {
                required: true,
            },
            subcategory: {
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
            lastactionyear: {
                required: 'Select the Last action taken year',
            },
            lastactionmonth: {
                required: 'Select the Last action taken month',
            },
            typeofauditcode: {
                required: "Select the Plan Period",
            },
            mainobjectionid: {
                required: "Select Main Objection",
            },
            subobjectionid: {
                required: "Select Sub Objection",
            },

            stateofparacode: {
                required: "Select the state of para",
            },
            typeofparacode: {
                required: "Select the type of para",
            },
            parano: {
                required: "Enter Para Number",
            },


            severityid: {
                required: "Select severity",
            },
            liability: {
                required: "Choose the liability",
            },

            slipdetails: {
                required: "Enter the slipdetails",
            },
            typeofauditcode: {
                required: "Select Type of Audit",
            },
            yearcode: {
                required: "Select the year",
            },
            paranumber: {
                required: "Enter the para number",
            },
            schemename: {
                required: "Select Scheme name",
            },
            serious: {
                required: "Select Irregularities",
            },
            category: {
                required: "Select Category",
            },
            subcategory: {
                required: "Select Sub Category  ",
            },
            // highlight: function(element, errorClass) {
            //     $(element).removeClass(errorClass); //prevent class to be added to selects
            // },

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
        if ($("#para_form").valid()) {


            get_insertparadetails('insert', 'fresh');

        } else {
            scrollToFirstError();
        }
    });

    $(document).on('click', '#finalisebtn', function(event) {

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
        if ($("#para_form").valid()) {
            // getLabels_jsonlayout([{
            //     id: 'finaliselagcy',
            //     key: 'finaliselagcy'
            // }], 'N').then((text) => {
            passing_alert_value('Confirmation', 'Are you sure to forward the details?', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            // }
            // );
            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                get_insertparadetails('finalise', 'forward');
            });



        } else {
            scrollToFirstError();
        }
    });



    function get_insertparadetails(action, actionfor) {
        
var statusflag = 'Y';
var stateofparacode = (paratype == lagacyparatype) ? $('#stateofparacode').val() : '';
        $('#process_button').attr('disabled', true)
        $('#loader').removeClass('d-none');
        if (action == 'finalise') {
	    $('#finalisebtn').attr('disabled', true)
            statusflag = 'F';
        } else {
	$('#buttonaction').attr('disabled', true)

        }

        var formData = new FormData($('#para_form')[0]);
        var typeofauditcode = '<?php echo $financialauditcode; ?>';
        var deactive_fileid = $('#deactive_fileid').val()
        var audityear = $('#yearcode').val()
        var n_audityear = $('#n_yearcode').val()
        var paratype = $('#paratype').val()
var deleted_liabilityid = $('#deleted_liabilityid').val()
  formData.append('deleted_liabilityid', deleted_liabilityid);

        formData.append('auditeeremarks', auditeeremarks.getData());
        formData.append('deactive_fileid', deactive_fileid);
        formData.append('statusflag', statusflag);
        formData.append('actionfor', actionfor);
        formData.append('yearcode[]', audityear);
        formData.append('n_yearcode[]', n_audityear);
        formData.append('paratype', paratype);
        formData.append('auditeeremarks', auditeeremarks.getData());
  	formData.append('stateofparacode', stateofparacode);
        $(".work-row").each(function(index) {
            let rowId = $(this).attr('id').replace("row", ""); // Extract row number
            formData.append(`liabilityid[]`, $(`#liabilityid${rowId}`).val());
            formData.append(`notype[]`, $(`select[name='notype${rowId}']`).val());
            formData.append(`name[]`, $(`#name${rowId}`).val());
            formData.append(`gpfno[]`, $(`#gpfno${rowId}`).val());
            formData.append(`designation[]`, $(`#designation${rowId}`).val());
            formData.append(`amount[]`, $(`input[name='amount${rowId}']`).val());
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
        });
        

$('#button_set').css("visibility", "hidden");

	$.ajax({
            url: '/lagacy/paradet_insert', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // reset_form();




                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    $('#ok_button').off('click').on('click', function(event) {
                        event.preventDefault();
                        if (action == 'finalise') {
                            //window.location.reload();
                            $('#paraid').val(response.data['paraid'])
                            fetch_paramanagement_auditee(response.data['followupid'], response.data[
                                    'tempnumberform'],
                                'edit', 'Y', '', response.data[
                                    'paratype']);
                        } else {
                            $('#paraid').val(response.data['paraid'])
                            fetch_paramanagement_auditee(response.data['followupid'], response.data[
                                    'tempnumberform'],
                                'edit', 'Y', '', response.data[
                                    'paratype']);
                        }
                    });


                    //   fetch_paramanagement_auditee('en')

                    // table.ajax.reload(); // Reload the table



                }

            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired
                if (action == 'finalise') {
			$('#finalisebtn').attr('disabled', false)
                    $('#process_button').attr('disabled', false)

                } else {
                    $('#buttonaction').attr('disabled', false)
                    $('#process_button').attr('disabled', false)


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


    function setform_basedonslipnumber(seriesno) {
        $('#paradet_div').show();

        // reset_form();
        var paratype = $('#paratype').val();
        paraname = paratype == lagacyparatype ? 'gen_parano' : 'n_gen_parano'
        var followupid = $('#' + paraname + ' option[value="' + seriesno + '"]').data('followupid')



        $('#followupid').val(followupid);

        fetch_paramanagement_auditee(followupid, seriesno,
            'edit', '');


        $('#fileuploadid').val('');

        $('#file-input-container').hide()
        $('#file-input-container').empty();
        //  $('#add-file-btn').hide()

        //clear upload filecontainer
        $('#file-list-container').empty();
        $('#file-list-container').hide();

        appendFilePreview();
    }

    function reset_form() {

        // buttonname = @json($approvebtn);

        fileCount = 0;
        // changeButtonAction('auditslip', 'action', 'buttonaction', 'approvebtn', 'display_error',
        //@json($savedraftbtn), buttonname, 'insert')
        change_button_as_insert('para_form', 'action', 'buttonaction', 'display_error', '');

        // Detect the selected language (default to English if not set)
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
            // fileCount++;
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
        // removeAllEventListeners(document.getElementById("process_button"));


        // document.getElementById("process_button").onclick = null;


        // // Set up the confirmation process
        // document.getElementById("process_button").onclick = function() {
        //     deletefilefromview(fileId);
        // };

        // // Show confirmation alert
        // passing_alert_value('Confirmation', "Are you sure you want to delete this file?", 'confirmation_alert',
        //     'alert_header', 'alert_body', 'forward_alert');

        passing_alert_value('Confirmation', 'Are you sure you want to delete this file?', 'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');
        // }
        // );
        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();
            deletefilefromview(fileId);
        });
    }
 

function enable_liability_details() {

    // alert('enable_liability_details called');   

    // Get BOTH radio values separately
    const liabilityVal = $('input[name="auditee_liability"]:checked').val();
    const retirementVal = $('input[name="auditee_Retirement_type"]:checked').val();
// alert('toggleAuditeeLiabilityTyp: ' + liabilityVal);

    // ✅ If BOTH are N → hide container
    if (liabilityVal === 'N' && retirementVal === 'N') {
pushAllLiabilityIdsToDeleted();
                $('#paraliabilitycontainer').empty();
        // alert('Liability1: ' + liabilityVal + ', Retirement: ' + retirementVal);
        $('#paraliabilitycontainer').hide();

        return;
        
    } else {
        $('#paraliabilitycontainer').show();
    }

    // Otherwise show
 if (liabilityVal === 'Y' || retirementVal === 'Y') {
        // alert('Liability1: ' + liabilityVal + ', Retirement: ' + retirementVal);

  if ($('#paraliabilitycontainer .work-row').length === 0) {
        $('#paraliabilitycontainer').empty();
        rowCount = 0;
       // addNewWorkRow(null, 'entry', '', '', '', '', '', '', '', '', '', '', '', 'paraliabilitycontainer');
    }        
        
    }
    // Ensure at least one row exists
  

    // Handle dropdown visibility
    toggleAuditeeLiabilityType(liabilityVal,retirementVal);
}
	
function toggleAuditeeLiabilityType(liabilityVal, retirementVal, liabilitydel) {

        $('#paraliabilitycontainer').show();

        if (liabilityVal === 'Y') {
            // If liability is YES
            $('#auditee_Retirement_type_div').hide(); // Hide retirement options
            $('#auditee_liability_type_div').hide(); // Hide liability type div if needed
            $('#paraliabilitycontainer').show();
        } else if (liabilityVal === 'N') {
            // If liability is NO
            $('#auditee_Retirement_type_div').show(); // Show retirement options
            $('#auditee_liability_type_div').show(); // Show liability type div if needed
            $('#auditee_liability_type').prop('disabled', true); // disable input if needed

            // Default retirement to 'Y' if not already selected
            const currentRetirement = $('input[name="auditee_Retirement_type"]:checked').val();
            if (!currentRetirement) {
                $('input[name="auditee_Retirement_type"][value="Y"]').prop('checked', true);
            }

            if (retirementVal === 'Y' || !currentRetirement) {
                // If retirement is YES (or defaulted), show table
                $('#paraliabilitycontainer').show();
                if ($('#paraliabilitycontainer .work-row').length === 0 && !liabilitydel) {
                    addNewWorkRow(event, 'entry', '', '', '', '', '', '', '', '', '', '', '', 'paraliabilitycontainer');
                }

            } else if (retirementVal === 'N') {
                pushAllLiabilityIdsToDeleted();
                $('#paraliabilitycontainer').empty();
                // If retirement is NO, hide table
                $('#paraliabilitycontainer').hide();
            }
        } else {
            // Default fallback
            $('#paraliabilitycontainer').hide();
        }
    }


 function pushAllLiabilityIdsToDeleted() {

        let deletedIds = $('#deleted_liabilityid').val();
        let deletedArray = deletedIds ? deletedIds.split(',') : [];

        // Loop through all hidden liabilityid inputs inside container
        $('#paraliabilitycontainer input[id^="liabilityid"]').each(function() {

            let liabilityId = $(this).val();

            if (liabilityId && liabilityId !== '') {

                // Add only if not already present
                if (!deletedArray.includes(liabilityId)) {
                    deletedArray.push(liabilityId);
                }
            }
        });

        // Update hidden field
        $('#deleted_liabilityid').val(deletedArray.join(','));
    }


 function toggleAmountColumn() {
        const auditeeLiability = $('input[name="auditee_liability"]:checked').val();
            const retirementVal = $('input[name="auditee_Retirement_type"]:checked').val();


        if (auditeeLiability === 'Y') {
            $('#auditee_liability_type_div').hide();
            $('#auditee_Retirement_type_div').hide();
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


        // Default select first available year
    function onchange_retirement_yr(el, row, selectedMonth = '') {

    const retirementYear = el.value;

    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1;

    const monthSelect = $('#retirementmonth' + row);
    const yearSelect = $('#retirementyear' + row);

    monthSelect.empty().append('<option value="">Month</option>');

    // ✅ If no year selected → show validation error
    if (!retirementYear) {
        yearSelect.addClass('is-invalid');
        monthSelect.prop('required', false);
        return;
    } else {
        yearSelect.removeClass('is-invalid');
        monthSelect.prop('required', true); // ✅ make month required only after year selected
    }

    const sortedMonths = Object.keys(months).sort(function (a, b) {
        return parseInt(a, 10) - parseInt(b, 10);
    });

    $.each(sortedMonths, function (_, monthNum) {

        const monthName = months[monthNum];
        const monthInt = parseInt(monthNum, 10);

        // Skip past months if current year
        if (parseInt(retirementYear, 10) === currentYear &&
            monthInt < currentMonth) {
            return true;
        }

        monthSelect.append(
            `<option value="${monthNum}" ${monthNum == selectedMonth ? 'selected' : ''}>
                ${monthName}
            </option>`
        );
    });

}

$("#para_form").validate({
        errorPlacement: function(error, element) {
            // ✅ For retirement year → place after input-group
            if (element.attr("name").startsWith("retirementyear")) {
                error.insertAfter(element.closest(".input-group"));
            }
            // default behavior
            else {
                error.insertAfter(element);
            }
        }
    });

    $.validator.addMethod("yearMonthRequired", function(value, element, params) {
        const year = $("[name='" + params[0] + "']").val();
        const month = $("[name='" + params[1] + "']").val();
        return year !== "" && month !== "";
    }, "Please select retirement year and month");
	
	    $(document).on('blur', '.removesplchar_desig', function () {
    const value = $(this).val().trim();
    if (value.length < 5) {
        $(this).addClass('is-invalid');
    } else {
        $(this).removeClass('is-invalid');
    }
});



    //-------------Liability-----------------//
    $(document).on('input change', 'input[id^="gpfno"]', function() {
        $(this).valid(); // re-validate as user types
    });
    $.validator.addMethod("notSameChar", function(value, element) {
        if (!value) return true; // let required handle empty
        return !/^(\d)\1+$/.test(value); // fails if all digits are same
    }, "Number cannot contain the same digit repeatedly");


    function onchange_notype(el, row) {

        const type = el.value;
        const $gpf = $('#gpfno' + row);

        // clear value & remove old rules
        $gpf.val('');
        $gpf.rules('remove');

        if (!type) return;

        let maxLen = 0;
        let msg = '';

        if (type === '03') { // IFHRMS → 11 digits
            maxLen = 11;
            msg = 'IFHRMS No must be exactly 11 digits';
        } else if (type === '01') { // EPF → 22 digits
            maxLen = 12;
            msg = 'EPF No must be exactly 12 digits';
        } else if (type === '02') { // CPS → 8 digits
            maxLen = 8;
            msg = 'CPS No must be exactly 8 digits';
        }

        $gpf.rules('add', {
            required: true,
            digits: true,
            minlength: maxLen,
            maxlength: maxLen,
            notSameChar: true,
            messages: {
                required: 'Number is required',
                digits: 'Only digits allowed',
                minlength: msg,
                maxlength: msg,
                notSameChar: 'Number cannot have all digits the same'
            }
        });

        // block typing beyond length
        $gpf.attr('maxlength', maxLen);

        // ✅ live validation on typing
        $gpf.off('input.notype').on('input.notype', function() {
            $(this).valid();
        });

        // first validation
        $gpf.valid();
    }



    $(document).on('input', 'input[id^="gpfno"]', function() {
        const max = this.maxLength;
        if (max > 0 && this.value.length > max) {
            this.value = this.value.slice(0, max);
        }
    });
    $(document).on('blur', '.numberswithdecimal', function() {
        let val = $(this).val().trim();

        if (val === '') return;

        // remove commas if any
        val = val.replace(/,/g, '');

        // if valid number, format to 2 decimals
        if (!isNaN(val)) {
            $(this).val(parseFloat(val).toFixed(2));
        } else {
            $(this).val('');
        }
    });
    $.validator.addMethod("noThreeSameChars", function(value, element) {
        if (!value) return true; // let required handle empty
        return !/(.)\1\1\1/i.test(value); // any char repeated 3 times in a row
    }, "Invalid Designation");
    
    $.validator.addClassRules("name", {
        noThreeSameChars: true
    });



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

function disable_retiremnt() {
        $('#retiredflag').attr('disabled', true);
        $('#retirementyear').attr('disabled', true);
        $('#retirementmonth').attr('disabled', true);
    }


 function download() {
        let form = document.createElement("form");
        form.method = "POST";
        form.action = "/apms/download_liability_certificate";

        let token = document.createElement("input");
        token.type = "hidden";
        token.name = "_token";
        token.value = $('meta[name="csrf-token"]').attr("content");

        let fid = document.createElement("input");
        fid.type = "hidden";
        fid.name = "followupid";
        fid.value = followupid;

        form.appendChild(token);
        form.appendChild(fid);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>

@endsection
