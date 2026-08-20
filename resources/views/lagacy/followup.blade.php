@section('content')
    @extends('index2') @include('common.alert')
    <?php
    // $instdel = json_decode($inst_details, true);
    // $getmajorobjection = json_decode($get_majorobjection, true);
    ?>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../assets/css/fieldaudit.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


    <?php
    $sessionchargedel = session('charge');
    // print_r($sessionchargedel);
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

    $fileuploadcount = 1;
    $liabilitylimit = 5;

      $months = [
        '01' => 'January', '02' => 'February', '03' => 'March',
        '04' => 'April', '05' => 'May', '06' => 'June',
        '07' => 'July', '08' => 'August', '09' => 'September',
        '10' => 'October', '11' => 'November', '12' => 'December'
    ];

     $currentYear = date('Y');
    $endYear = $currentYear - 1; // exclude current year
    $startYear = $endYear - 4;
    ?>

    <div class="row">
        <form id="lagacy_form" name="lagacy_form">
            @csrf
            <div class="col-12">
                <div class="card card_border">
                    <div class="card-body">

                        <div class="row">

                            <input type="hidden" name="instid" id="instid"
                                value="{{ $instData->encrypted_instid ?? '' }}">
                            <input type="hidden" name="currentslipnumber" id="currentslipnumber" value='1'>
                            <input type="hidden" name="followupid" id="followupid" value="">
                            <input type="hidden" name="catcode" id="catcode" value="{{ $instData->catcode ?? '' }}">
                            <input type="hidden" name="ens" id="ens" value='<?php echo $ensessionuserid; ?>'>
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">
                            <input type="hidden" name="filter" id="filter" value='A'>
                            <input type="hidden" name="seriesno" id="seriesno" value='1'>
                            <input type="hidden" name="auditeeins_subcategoryid" id="auditeeins_subcategoryid"
                                value="{{ $instData->auditeeins_subcategoryid ?? '' }}">
                            <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                            <input type="hidden" name="active_fileid" id="active_fileid">

                            <!-- <div class="col-md-4 mb-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-label required" for="validationDefault01">Audit Office
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        value="{{ $instData->instename ?? '' }}" disabled>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div> -->

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="inst"for="catname">Institution </label>
                                <input type="text" class="form-control lang_value" id="instname" name="instname"
                                    data-en="{{ $instData->instename ?? '' }}" data-ta="{{ $instData->insttname ?? '' }}"
                                    value="{{ $instData->instename ?? '' }}" disabled>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label required lang" for="catname" key="category">Institution
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="catname" name="catname"
                                    data-en="{{ $catData->catename ?? '' }}" data-ta="{{ $catData->cattname ?? '' }}"
                                    value="{{ $catData->catename ?? '' }}" disabled>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label required lang" for="subcatname" key="sub_head">Institution Sub
                                    Category</label>
                                <input type="text" class="form-control lang_value" id="subcatname" name="subcatname"
                                    data-en="{{ $catData->if_subcategory=='Y' ? $subcatData->subcatename : $catData->catename }}"
                                    data-ta="{{ $catData->if_subcategory=='Y' ? $subcatData->subcattname : $catData->cattname }}"
                                    value="{{  $catData->if_subcategory=='Y' ? $subcatData->subcatename : $catData->catename }}"
                                    disabled>
                            </div>


                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="typeofaudit" for="validationDefault01">Type
                                    of
                                    Audit</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="typeofauditcode"
                                    name="typeofauditcode" disabled>
                                    <option value="" data-name-en="---Select Type Of Audit---"
                                        data-name-ta="--- ---">
                                        ---Select Plan Period---</option>

                                    @foreach ($typeofauditData as $typeofaudit)
                                        <option value="{{ $typeofaudit->typeofauditcode }}"
                                            @if (old('typeofaudit', $financialauditcode) == $typeofaudit->typeofauditcode) selected @endif
                                            data-name-en="{{ $typeofaudit->typeofauditename }}"
                                            data-name-ta="{{ $typeofaudit->typeofaudittname }}">
                                            {{ $typeofaudit->typeofauditename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="{{ $instData->deptcode =='01'?'fasil' : 'audityear_label' }}"
                                    for="validationDefault01">{{ $instData->deptcode =='01'?'Fasli Year' : 'Audit Year' }}</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="yearcode" name="yearcode"
                                    onchange = "fetch_lagacydata('', '','fetch','Y','A',this.value)">


                                    @foreach ($yearofaudit as $year)
                                        <option value="{{ $year->auditperiodid }}">
                                            {{ $year->audit_period }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- <div class="col-md-2  mb-3">
                                <label class="form-label required lang" key="part_no" for="part_no">Part
                                    No</label>
                                <input type="text" class="part_no form-control only_numbers " id="part_no"
                                    data-placeholder-key="paranumber" name="paranumber" maxlength="9" required />
                            </div> --}}





                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card_border">
                    <div class="card-body">
                        <div class="hstack mb-2">

                            <div class="div ms-2 ">
                                <span class="badge drop_div">
                                </span>
                                <span class="">Finalized</span>
                            </div>
                            {{-- <div class="div ms-2">
                                <span class="badge contopara_div">
                                </span>
                                <span class="">Save Draft</span>
                            </div> --}}
                        </div>

                        <div class="row">
                            <div class="col-md-1 border-end user-chat-box">
                                <div class="d-lg-none  d-flex  mb-2">
                                    <!-- Filter Button -->
                                    <div class="dropdown me-2" style="width:90px;">
                                        <a class="text-light fs-4 nav-icon-hover btn btn-sm bg-info"
                                            href="javascript:void(0)" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Filter <i class="ti ti-adjustments"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0)">All</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)">Finalised</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)">Draft</a></li>

                                        </ul>
                                    </div>
                                    <!-- Search Input -->
                                    <div class="flex-grow-1 ">
                                        <input type="text" class="form-control search-chat py-2" id="text-srh"
                                            placeholder="Search" />
                                    </div>
                                </div>

                                <!-- Desktop View (Filter Above Search) -->
                                <div class="d-none d-lg-block">
                                    <!-- <div class="position-relative mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="dropdown">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <a class="text-light fs-4 nav-icon-hover btn btn-sm bg-info"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                href="javascript:void(0)" role="button" data-bs-toggle="dropdown"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                aria-expanded="false">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Filter <i class="ti ti-adjustments"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <ul class="dropdown-menu">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <li><a class="dropdown-item" href="javascript:void(0)">All</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <li><a class="dropdown-item" href="javascript:void(0)">Finalised</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <li><a class="dropdown-item" href="javascript:void(0)">SaveDraft</a></li>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </ul>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                    <div class="pt-1">
                                        <div class="position-relative mb-4">
                                            <input type="text" class="form-control search-chat py-2" id="text-srh"
                                                placeholder="Search" />
                                        </div>
                                    </div>
                                </div>

                                <div class="app-chat">
                                    <div class="overflow-auto card mb-0 shadow-none border h-50">
                                        <ul class="chat-users mb-0 mh-n100" data-simplebar="" style="max-height:600px">
                                            <!-- Existing chat slip number items will go here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-11">
                                <div class="card card_border">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <b><span id="forwardedby" class="text-end"></span></b>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <!-- Optional: You can place another item here if needed -->
                                                    </div>
                                                    <div class="col-md-4">
                                                        <b><span id="approvedby" class="text-end"></span></b>
                                                    </div>
                                                </div>
                         <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="typeofpara"
                                    for="validationDefault01">Type of Para</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="typeofparacode" name="typeofparacode">
                                    <option value="" data-name-en="--Select Type of Para--" data-name-ta="">--Select Type of Para--</option>
                                        @foreach ($typeofpara as $type)
                                        <option value="{{ $type->typeofparacode }}"
                                               data-name-en="{{ $type->typeofparaename }}"
                                            data-name-ta="{{ $type->typeofparatname }}">
                                            {{ $type->typeofparaename }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="stateofpara"
                                    for="validationDefault01">Current State of Para</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="stateofparacode" name="stateofparacode">
                                    <option value="" data-name-en="--Select Current State of Para--" data-name-ta="">--Select Current State of Para--</option>
                                    @foreach ($stateofpara as $state)
                                        <option value="{{ $state->stateofparacode }}"
                                               data-name-en="{{ $state->stateofparaename }}"
                                            data-name-ta="{{ $state->stateofparatname }}">
                                            {{ $state->stateofparaename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key=""
                                    for="validationDefault01">Para Number</label>
                                <input  id="parano" name="parano"  class="form-control only_numbers"value="" maxlength="10">

                            </div>
				<div class="col-md-2 mb-3">
                                                        <label class="form-label lang required" key="statusflag"
                                                            for="validationDefault01">Status</label>
                                                        <select class="form-select mr-sm-2 lang-dropdown select2"
                                                            id="actiontaken" name="actiontaken"
                                                            onchange="onchange_actiontaken('','','')">
                                                            <option value="" data-name-en="--Select Action Taken--"
                                                                data-name-ta=""></option>

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
                          <div class="col-md-2 mb-3 hide_this" id="actiontaken_div">
                                <label class="form-label lang required" key="last_actiontaken" for="dropdown1">Last action taken</label>
                                <div class="d-flex gap-2">
                                <!-- First dropdown - takes 2/3 of space -->
                                  <select class="form-select flex-grow-1" name="lastactionmonth" id="lastactionmonth">
                                    <option value="">Select Month</option>
                                    @foreach($months as $num => $name)
                                     <option value="{{ $num }}">{{ $name }}</option>
                                    @endforeach
                                  </select>

                                      <!-- Second dropdown - takes 1/3 of space -->
                                 <select class="form-select" style="width: 150px;" name="lastactionyear" id="lastactionyear">
                               <option value="">Year</option>

                                @for($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor

                         </select>
                        </div>
                            </div>

                       </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label required lang"
                                                            for="validationDefaultUsername"
                                                            key="major_obj">Title/Heading</label>
                                                        <select class="form-control select2" id="mainobjectionid"
                                                            name="mainobjectionid" onchange="getminorobjection('','')">
                                                            <option value="" data-name-en="---Select Title---"
                                                                data-name-ta="---தலைப்பைத் தேர்ந்தெடுக்கவும்---">---Select
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
                                                        <label class="form-label required lang"
                                                            for="validationDefaultUsername" key="minor_obj">Categorization
                                                            of Paras
                                                        </label>
                                                        <select class="select form-control  select2" id="subobjectionid"
                                                            name="subobjectionid">

                                                        </select>
                                                    </div>
                                                            <div class="col-md-2 mb-2"> <label class="form-label lang"
                                                    for="validationDefaultUsername" key="amount_involved">Amount
                                                    Involved</label> <input type="text"
                                                    class="form-control removesplchar_numberwithdecimal numberswithdecimal" id="amount_involved"
                                                    name="amount_involved"  maxlength="12">
                                            </div>
                                                    <div class="col-md-2 ">
                                                        <label class="form-label required lang"
                                                            for="validationDefaultUsername"
                                                            key="severity">Severity</label>
                                                        <select class="select form-control custom-select" id="severityid"
                                                            name="severityid">
                                                            <!-- <option value="">@lang('Select Severity')</option> -->
                                                            {{--
                                                         <option value="" data-en="Select Severity" data-ta="தெரிவு கடைசியாக"></option> --}}
                                                            <option value="" data-name-en="---Select Severity---"
                                                                data-name-ta="---தெரிவு கடைசியாக தேர்ந்தெடுக்கவும்---">
                                                                ---Select
                                                                Severity---</option>

                                                            @foreach ($severities as $key => $severity)
                                                                <option value="{{ $key }}"
                                                                    data-en="{{ $severity['en'] }}"
                                                                    data-ta="{{ $severity['ta'] }}">
                                                                    {{ $severity['en'] }}
                                                                    <!-- Default language text, can be changed dynamically -->
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-4">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-5 mb-2">
                                                                <label class="form-label required lang"
                                                                    key="scheme">Scheme</label> <br>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input success"
                                                                            type="radio" name="scheme" id="Y"
                                                                            value="Y"
                                                                            onchange="enable_schemename('Y')">
                                                                        <label class="form-check-label lang"
                                                                            for="Y" key="yes">Yes</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input success"
                                                                            type="radio" name="scheme" id="N"
                                                                            value="N"
                                                                            onchange="enable_schemename('N')" checked>
                                                                        <label class="form-check-label lang"
                                                                            for="N" key="no">No</label>
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
                                                                <label class="form-label required lang"
                                                                    key="schemename">Scheme
                                                                    Name</label>
                                                                <select
                                                                    class="select form-control custom-select lang-dropdown"
                                                                    id="schemename" name="schemename">
                                                                    <option value=''
                                                                        data-name-en="Select Scheme Name"
                                                                        data-name-ta="தெரிவு கடைசியாக"></option>
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
                                                            for="validationDefaultUsername"
                                                            key="">Irregularities</label>
                                                        <select class="select form-control custom-select lang-dropdown"
                                                            id="serious" name="serious"
                                                            onchange="getcategoryBasedOnSerious('','')">
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
                                                                <option disabled data-name-en="No serious Available"
                                                                    data-name-ta="">
                                                                    No serious Available
                                                                </option>
                                                            @endif

                                                        </select>


                                                    </div>
                                                    <div class="col-md-2 mb-2">
                                                        <label class="form-label required lang" key="category"
                                                            for="validationDefaultUsername">Category</label>
                                                        <select class="select form-control custom-select lang-dropdown"
                                                            id="category" name="category"
                                                            onchange="getsubcategoryBasedOnCategory('','')">
                                                            <!-- <option value="">@lang('Select Severity')</option> -->
                                                            <option value="" data-name-en="Select Category"
                                                                data-name-ta="வகையைத் தேர்ந்தெடுக்கவும்">Select Category
                                                            </option>

                                                            <option value="" disabled id=""
                                                                data-name-en="No Category Available"
                                                                data-name-ta="வகை கிடைக்கவில்லை">No Category Available
                                                            </option>
                                                            <!-- @if (!empty($severitydel) && count($severitydel) > 0)
    @foreach ($severitydel as $s)
    <option value="{{ $s->severitycode }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  data-name-en="{{ $s->severityelname }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  data-name-ta="{{ $s->severitytlname }}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  {{ $s->severityelname }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              </option>
    @endforeach
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               else
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <option disabled data-name-en="No Category Available"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      data-name-ta="">No Category Available
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </option>
    @endif -->

                                                        </select>


                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <label class="form-label required lang "
                                                            for="validationDefaultUsername" key="if_subcategory">Sub
                                                            Category</label>
                                                        <select class="select form-control custom-select lang-dropdown"
                                                            id="subcategory" name="subcategory">
                                                            <!-- <option value="">@lang('Select Severity')</option> -->
                                                            <option value="" data-name-en="Select SubCategory"
                                                                data-name-ta="உபவகை தேர்ந்தெடுக்கவும்">---Select
                                                                SubCategory
                                                            </option>


                                                        </select>


                                                    </div>




                                                </div>


                                                <div class="row mt-2  ">
                                                    <div class="col-md-4 mb-2"> <label class="form-label required lang"
                                                            for="validationDefaultUsername" key="paradetails">Para Details</label>
                                                        <textarea id="slipdetails" maxlength="500" name="slipdetails" class="form-control text_special" placholder="Enter remarks"></textarea>
                                                    </div>


                                                    <div class="col-md-4 mb-2">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-6">
                                                                <label class="form-label required lang"
                                                                    for="validationDefaultUsername" key="liability">
                                                                    Liablility</label> <br>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input success"
                                                                            type="radio" name="liability"
                                                                            id="Y" value="Y"
                                                                            onchange="enable_liability('Y')">
                                                                        <label class="form-check-label lang"
                                                                            for="all" key="yes">Yes</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input success"
                                                                            type="radio" name="liability"
                                                                            id="N" value="N" checked
                                                                            onchange="enable_liability('N')">
                                                                        <label class="form-check-label lang"
                                                                            for="district" key="no">No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- <div class="col-sm-12 col-md-6 hide_this"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                id="liabilityname_div">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    Name</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    id="liabilityname" name="liabilityname"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    placeholder="Enter Liability name">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div> -->
                                                        </div>


                                                    </div>




                                                    <div id="dynamicRowsContainer">

                                                    </div>

                                                    <!-- <div class="col-md-4 hide_this" id="liabilitygpfno_div">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="row">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-sm-12 col-md-6 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        GPF / CPF No</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="liabilitygpfno" name="liabilitygpfno"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        placeholder="Enter Liability GPFno">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="col-sm-12 col-md-6 ">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-label required"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        for="validationDefaultUsername">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Designation</label> <input type="text"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="liabilitydesig" name="liabilitydesig"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        class="form-control"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        placeholder="Enter Liability Designation">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->



                                                </div>
                                                <div class="row">


                                                    <div class="col-md-12 ">
                                                        <label class="form-label required lang"
                                                            for="validationDefaultUsername" key="paraobservation">
                                                           Para Observation/Remarkss</label>
                                                        <textarea id="remarks" class="form-control" placeholder="Enter remarks" name="remarks"></textarea>
                                                    </div>

                                                    <div class="col-md-12 p-6">
                                                        <label class="form-label  lang" for="validationDefaultUsername"
                                                            key="attachments">Attachments </label>
                                                        <span style="color:#ff0000; font-size:10px;">(Max Size : 2 MB &
                                                            File
                                                            Format
                                                            : Pdf,Excel )</span>
                                                        <div class="row mb-2">


                                                            <div class="col-md-12 d-flex align-items-center">
                                                                <div class="file-input-container"
                                                                    id="file-input-container">
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
                                                                            <input class="d-none" multiple="multiple"
                                                                                type="file" name="fileupload[]"
                                                                                accept=".pdf, .docx, .xlsx">
                                                                        </div>
                                                                    </template>
                                                                </div>


                                                                <label for="upload_input" id="add-file-btn"
                                                                    class="btn btn-success btn-sm ms-2 ">
                                                                    <i class="fs-6 ti ti-plus"></i>
                                                                </label>
                                                                <input id="upload_input" type="file"
                                                                    name="fileupload[]" class="d-none"
                                                                    multiple="multiple"
                                                                    onchange="window.breakIntoSeparateFiles(this, '#file-list', '#file-preview')" />
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-row">
                                                            <div class="position-relative d-flex flex-row"
                                                                id="file-list-container">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1">
                                                        <div class="col-md-6" style="margin-left: 39% !important;">
                                                            <div class="d-flex align-items-center gap-6" id="button_set">
                                                                <input type="hidden" id="action" name="action"
                                                                    value="insert">
                                                                <button class="btn button_save" id="buttonaction"
                                                                    name="buttonaction">Save Draft
                                                                </button>
                                                                <button class="btn button_finalise"
                                                                    id="finalisebtn">Finalise</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
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
    </div>

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <!-- <script src="../assets/js/extra-libs/moment/moment.min.js"></script> -->
    <!-- <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script> -->
    <!-- <script src="../assets/js/forms/daterangepicker-init.js"></script> -->
    <!--select 2 -->
    <!-- <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script> -->
    <!-- <script src="../assets/libs/select2/dist/js/select2.min.js"></script> -->
    <!-- <script src="../assets/js/forms/select2.init.js"></script> -->
    <!--chat-app-->
    <script src="../assets/js/apps/chat.js"></script>
    <!-- Form Wizard -->

    <!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>


    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>


    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <script src="../assets/js/datatable/datatable-advanced.init.js"></script> -->
    <script>
        let maxRow = 5;
        let maxfilecount = 1;

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
            if (fileCount >= 3) {
                $('#add-file-btn').hide();
            } else {
                $('#add-file-btn').show();
            }
        }
        (function(window, $) {
            var FILE_ICON_URL = '../assets/images/file.png';
            var ALLOWED_TYPES = ['application/pdf',
                // 'image/jpeg', 'image/png',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            var MAX_FILE_SIZE_MB = 3;
            var MAX_FILE_COUNT = 1;

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
                        alert('Invalid file type. Please upload PDF, Excel files only.');
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
                        'uploadImage', 'insertTable',
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
                    'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            })
            .then(e => {
                editor = e;
            })
            .catch(error => {
                console.error(error);
            });

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
                addNewWorkRow(event, 'entry', '', '', '', '', '', '', '');

            } else {
                $('#dynamicRowsContainer').hide();
                // Hide the textbox when "No" is selected
                // $("#" + liablilitynamedivid).hide(); //liabilityname_div
                // $("#" + liablilitygpfdivid).hide(); //liabilitygpfno_div
            }
        }

        function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag) {
            event.preventDefault();



            let isChecked = (statusflag === 'Y') ? 'checked' : '';
            // let isdisabled = '';

            let isdisabled = (action === 'view') ? 'disabled' : '';

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

            newWorkRow += `
                  <div class="row work-row p-2 border-bottom mb-3" id="row${rowCount}">
                      <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}">

                      <!-- Type Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Type</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Type</label>` : ''}
                          <select class="form-select" name="notype${rowCount}" value="${name}" ${isdisabled}>
                              <option value="">---Select Type---</option>
                              <option value="01" ${selectedOption === "01" ? "selected" : ""}>EPF No</option>
                              <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPS No</option>
                              <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
                          </select>
                      </div>

                      <!-- GPF Number Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Number</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block"> Number</label>` : ''}
                         <input type="text" class="form-control removesplchar_number alpha_numeric" maxlength="20" name="gpfno${rowCount}" id="gpfno${rowCount}" value="${gpfno}" placeholder="Number" ${isdisabled}>
                      </div>

                      <!-- Name Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Name</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Name</label>` : ''}
                          <input type="text" class="form-control removesplchar_text name" maxlength="50"  id="name${rowCount}" name="name${rowCount}" value="${name}" placeholder="Name" ${isdisabled}>
                      </div>

                      <!-- Designation Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Designation</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Designation</label>` : ''}
                          <input type="text" class="form-control removesplchar_text name" maxlength="50"  name="designation${rowCount}" id="designation${rowCount}" value="${designation}" placeholder="Designation" ${isdisabled}>
                      </div>

                      <!-- Amount Column -->
                      <div class="col-md-2 col-12 mb-2">
                          <label class="form-label d-md-none d-block">Amount</label>
                          ${rowCount == 0 ? `<label class="form-label d-none d-md-block">Amount</label>` : ''}
                          <input type="text" class="form-control removesplchar_numberwithdecimal numberswithdecimal" maxlength="9" name="amount${rowCount}" value="${amount}" placeholder="Amount" ${isdisabled}>
                      </div>

                      <!-- Action Column -->
                      <div class="col-md-2 mb-2 action-row">
                          ${rowCount == 0  ? `<label class="form-label d-md-block d-none">Action</label>` : ''}
                          <div class="d-md-none">
                              <label class="form-label d-block">Action</label>
                          </div>
                          <div class="d-flex flex-md-row flex-column gap-1">
                              ${rowCount > 0 && (action === 'entry') ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <button type="button" class="mar_left btn btn-danger fw-medium deleteRowBtn" onclick="deleteRow(${rowCount})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="ti ti-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>` : ''}


                               ${ ((action === 'gh') ||  (action === 'fdg'))? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                              <input type="checkbox" class="form-check-input warning" name="activestatus[]"   ${isChecked} ${isdisabled}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>` : ''}

                              ${action !== 'view' && (action !== 'view') ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <button type="button" class=" mar_left btn btn-success fw-medium addRowBtn" onclick="addNewWorkRow(event, 'entry', '', '', '', '', '', '', '')">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <i class="ti ti-circle-plus"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                 </button>` : ''}
                          </div>
                      </div>
                  </div>
                  `;

            //Append new row to the container (assuming there's a container like #workRowContainer)
            //document.getElementById('workRowContainer').insertAdjacentHTML('beforeend', newWorkRow);




            $("#dynamicRowsContainer").append(newWorkRow);

            // Hide the add button in the previous row
            if (rowCount > 0) {
                $(`#row${rowCount - 1} .addRowBtn`).hide();
            } else if (rowCount == 0) {
                $(`#row${rowCount } .addRowBtn`).show();
            }

            // Check if the row limit has been reached
            checkRowLimit(isLast);

            // Apply validation to the newly added row's fields
            applyValidationToNewFields(`notype${rowCount}`, 'Select Number Type');
            applyValidationToNewFields(`name${rowCount}`, 'Enter Name');
            //applyValidationToNewFields(`gpfno${rowCount}`, 'Enter Number');
            applyValidationToNewFields(`designation${rowCount}`, 'Enter Designation');
            applyValidationToNewFields(`amount${rowCount}`, 'Enter Amount');

            rowCount++;

            restrictSpecialChars(".removesplchar_text");
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

        // function deleteRow(rowId) {
        //     $(`#row${rowId}`).remove();
        //     $(`#row${rowId-1}.addRowBtn`).show();
        //     rowCount--;
        //     if (rowCount == '0')
        //         $(`#row${rowCount}.addRowBtn`).show();
        // }
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


                let validator = $("#lagacy_form").data("validator"); // Get validator instance

                if (!validator) {

                    $("#lagacy_form").validate(); // Ensure validation is initialized
                    validator = $("#lagacy_form").data("validator");
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
            fetch_lagacydata('', '',
                'fetch', 'Y', $('#filter').val());
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

        function fetch_lagacydata(followupid, slipnumber, action, createnewone, filter, yearcode) {

            fixid = followupid;
            reset_form()
            var yearcode = yearcode || $('#yearcode').val();

            var instid = $('#instid').val();
            if (action == 'fetch') slipid = '';

            $.ajax({
                url: '/lagacy/fetch_lagacydata', // For creating a new user or updating an existing one
                type: 'POST',
                data: {
                    followupid: followupid,
                    instid: instid,
                    action: action,
                    yearcode: yearcode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    //
                    if (response.data && response.data.length > 0) {




                        if ((action == 'fetch') || (action == 'fetchwithdata')) {
                            $('#seriesno').val(1);
                            const chatUsersList = document.querySelector(".chat-users");
                            chatUsersList.innerHTML = '';
                            firstItem_auditeedel = '';

                            if (fixid == '') //No Fixid provide
                            {
                                seriesnumber = Number($('#seriesno').val());
                                firstItem = response.data[0];
                                fixarrow = seriesnumber;
                            }

                            response.data.forEach(function(item) {

                                addSlipNumber(item.paranumber, item.encrypted_followupid, item
                                    .slipby, item.statusflag);
                                if (fixid) //If fix slipid present
                                {
                                    if (slipnumber == item
                                        .paranumber
                                    ) //compare fixslipid with item.paranumber
                                    {
                                        fixarrow = $('#seriesno').val() - 1;
                                        firstItem = item;
                                    }
                                }
                            });

                            $('.hstack').removeClass("active_div");
                            $('#arrow_' + fixarrow).css("visibility", "visible");

                            $('#' + fixarrow).addClass("active_div");


                        } else if (action == 'edit') {
                            firstItem = response.data[0];
   				                        }
  			//temp_values =   response.data[0];

 			fix_formfield_values(firstItem, fixarrow,action);
                     
                        if ((createnewone == 'Y') && (filter == 'A') && ((action ==
                                'fetch') || (action == 'fetchwithdata'))) {

                            addSlipNumber('', '', '', '');
                        }
                    } else {
                        reset_form();

                        for_newslip_resetform('');


                        const chatUsersList = document.querySelector(".chat-users");
                        chatUsersList.innerHTML = '';
                        seriesnumber = Number($('#seriesno').val());
                        $('#currentslipnumber').val($('#autoslipnumber').val())
                        appendFilePreview();
                        addSlipNumber('', '', '', '');
                        $('#arrow_' + seriesnumber).css("visibility", "visible");
                        $('#' + seriesnumber).addClass("active_div");

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

        function for_newslip_resetform(clickedId) {
            fileCount = 0;

            enablefield()
            $('#finalisebtn').show();
            $('#buttonaction').show();
            $('#processname').html('')
            $('#processname').html('')
            $('#mainslipnumber').html('')
            $('#rejoindercount').val('');
            $('#rejoinderstatus').val('')
            $('#auditorremarksdiv').show();
            $('#auditorAccordionsContainer').hide();


            $('#severityDiv').hide();



            //    getObjectionBasedOnSlip('', '', '', 'majorobjectioncode')

            // enableformfields('E');
            // showfreshformbtn()
            $('#file-input-container').show()
            $('#add-file-btn').show()
            $('#fileuploadstatus').val('Y');
            const lang = getLanguage('');

            // Set the default option based on language
            optionText = lang === 'ta' ? 'வகை பெயரைத் தேர்ந்தெடுக்கவும்' : 'Select Category Name';


            $("#category")
                .empty()
                .append(
                    `<option value='' data-name-en="Select Category Name" data-name-ta="வகை பெயரைத் தேர்ந்தெடுக்கவும்">${optionText}</option>`
                );

            optionText = lang === 'ta' ? 'துணை வகையைத் தேர்ந்தெடுக்கவும்' : 'Select SubCategory ';


            $("#subcategory")
                .empty()
                .append(
                    `<option value='' data-name-en="Select SubCategory" data-name-ta="துணை வகையைத் தேர்ந்தெடுக்கவும்">${optionText}</option>`
                );
        }

        function fix_formfield_values(firstItem, seriesno,action) {

            rowCount = 0;
            // console.log(firstItem)
            //   alert(JSON.stringify(firstItem, null, 2));

            $('#dynamicRowsContainer').empty();


            if (!firstItem.encrypted_followupid) return;
            else
                change_button_as_update('lagacy_form', 'action', 'buttonaction', 'display_error', '', '');


            if (firstItem.statusflag == 'F') {

                $('#finalisebtn').hide();
                $('#buttonaction').hide();
                $('.addRowBtn').hide();
                //     document.querySelectorAll('#lagacy_form input, #lagacy_form select, #lagacy_form textarea, #lagacy_form button').forEach(function(el) {
                //     el.disabled = true;
                // });
                UploadedFileList_withaction = 'finalise';

            } else {

                UploadedFileList_withaction = 'edit';
                $('#finalisebtn').show();
                $('#buttonaction').show();
            }
            $('#currentslipnumber').val(firstItem.paranumber);
            onchange_actiontaken('', firstItem.lastactionmonth, firstItem.lastactionyear)
            $('#followupid').val(firstItem.encrypted_followupid);
            $('#amount_involved').val(firstItem.amtinvolved);

            $('#serious').val(firstItem.irregularitiescode);
             $('#typeofparacode').val(firstItem.typeofparacode);
              $('#stateofparacode').val(firstItem.stateofparacode);
                          $('#typeofparacode,#stateofparacode').select2();
               $('#parano').val(firstItem.parano);
                $('#lastactionmonth').val(firstItem.lastactionmonth);
               $('#lastactionyear').val(firstItem.lastactionyear);
            $('#schemename').val(firstItem.auditeeschemecode);
            getcategoryBasedOnSerious(firstItem.irregularitiescode, firstItem.irregularitiescatcode);
            getsubcategoryBasedOnCategory(firstItem.irregularitiescatcode, firstItem.irregularitiessubcatcode);




            $('#slipdetails').val(firstItem.slipdetails);
            $('#severityid').val(firstItem.severitycode);
            $('#fileuploadstatus').val('N');

            $('input[name="scheme"][value="' + firstItem.schemastatus + '"]').prop('checked', true);
            // alert(firstItem.schemastatus);


            if (firstItem.schemastatus === 'Y') {

                $('#severityDiv').show();


            } else {
                $('#severityDiv').hide();

            }


            //enable_liability(firstItem.liability, 'liabilityname_div', 'liabilitygpfno_div')
            $('input[name="liability"][value="' + firstItem.liability + '"]').prop('checked', true);
            if (firstItem.liability === 'Y') {

                // alert('if');

                $('#dynamicRowsContainer').show();

                $liabilityaction = 'edit';
                // Assuming you have this string (you can replace this with dynamic data from your backend or input)
                liabilitydel = firstItem.liabilitydel;


                if (liabilitydel) {

                    // Step 1: Split the liabilitydel string by commas
                    var liablityparts = liabilitydel.split(",");
                    liabilityid = '';
                    // Step 2: Iterate over the parts and split each by the dash (-)
                    for (var i = 0; i < liablityparts.length; i++) {
                        var liablity = liablityparts[i].split("-"); // Split by the dash (-)

                        let isLast = (i === liablityparts.length - 1);


                        addNewWorkRow(event, $liabilityaction, liablity[0], liablity[2], liablity[1], liablity[3], liablity[
                            4], liablity[5], isLast, liablity[6]);


                        liabilityid += liablity[5] + ',';

                    }
                    if (liabilityid.endsWith(',')) {
                        liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
                    }
                    $('#liabilityid').val(liabilityid);
                }
            } else {
                $('#dynamicRowsContainer').hide()
            }

            //  alert(firstItem.mainobjectionid)


            getminorobjection(firstItem.mainobjectionid, firstItem.subobjectionid, 'minorobjectioncode',
                'mainobjectionid');

            $('#mainobjectionid').val(firstItem.mainobjectionid);
            $('#mainobjectionid').select2();
            $('#typeofauditcode').val(firstItem.typeofauditcode)
            $('#part_no').val(firstItem.paranumber)
            $('#yearcode').val(firstItem.audityear)

            if(firstItem.statusflag=='F')
            {
                  disenableformfields()


            }
		else {
                enablefield()
            }
if(action=='edit')
{            editor.setData(firstItem.remarks);
}
            fileviewid = 'file-list-container';
            fileidstore = '';
            fileupload_hiddenid = '';

            fileidstore = 'Y';
            fileupload_hiddenid = 'fileuploadid';
            $('#file-list-container').toggle(!!firstItem.auditorfileupload);
            if (firstItem.auditorfileupload) {
                files = getfile(firstItem.auditorfileupload)
                // alert(fileCount);

                UploadedFileList(files, UploadedFileList_withaction, fileviewid, fileidstore, fileupload_hiddenid)
                // alert('hi');
                // alert(fileCount);

                $('#active_fileid').val(files.map(file => file.fileuploadid).join(','));
            } else {
                $('#file-input-container').show()
                $('#add-file-btn').show()

            }

        }

        function disenableformfields() {
	   $('#actiontaken').attr('disabled', true);
            $('#mainobjectionid').attr('disabled',true);
             $('#subobjectionid').attr('disabled',true);
              $('#typeofparacode').attr('disabled',true);
               $('#stateofparacode').attr('disabled',true);
                $('#parano').attr('disabled',true);
                 $('#amount_involved').attr('disabled',true);
                  $('#severityid').attr('disabled',true);
                 $('#slipdetails').attr('disabled',true);
                $('#serious').attr('disabled',true);
                $('#category').attr('disabled',true);
                  $('#subcategory').attr('disabled',true);
                    $('#schemename').attr('disabled',true);
                    $('#yearcode').attr('disabled',true);
                     $('#lastactionmonth').attr('disabled',true);
                      $('#lastactionyear').attr('disabled',true);

            // document.getElementById('majorobjectioncode').disabled = true;
            // document.getElementById('minorobjectioncode').disabled = true;
            // document.getElementById('amount_involved').disabled = true;
            // document.getElementById('severityid').disabled = true;
            // document.getElementById('slipdetails').disabled = true;
            // document.getElementById('serious').disabled = true;
            // document.getElementById('category').disabled = true;
            // document.getElementById('subcategory').disabled = true;
            // document.getElementById('schemename').disabled = true;


            if (editor) editor.enableReadOnlyMode('customLock'); // Provide a unique lock ID

            $('input[name="liability"]').prop('disabled', true);
            $('input[name="scheme"]').prop('disabled', true);
            $('#liabilityname').attr('disabled',true);
            $('#liabilitygpfno').attr('disabled',true);
            $('#liabilitydesig').attr('disabled',true);
            // document.getElementById('liabilityname').disabled = true;
            // document.getElementById('liabilitygpfno').disabled = true;
            // document.getElementById('liabilitydesig').disabled = true;
            document.getElementById('add-file-btn').classList.add('hidden');

            $('#buttonaction').hide();
            $('#approvebtn').hide();
        }

        function enablefield()
        {
             $('#actiontaken').attr('disabled',false);
             $('#mainobjectionid').attr('disabled',false);
             $('#subobjectionid').attr('disabled',false);
              $('#typeofparacode').attr('disabled',false);
               $('#stateofparacode').attr('disabled',false);
                $('#parano').attr('disabled',false);
                 $('#amount_involved').attr('disabled',false);
                  $('#severityid').attr('disabled',false);
                 $('#slipdetails').attr('disabled',false);
                $('#serious').attr('disabled',false);
                $('#category').attr('disabled',false);
                  $('#subcategory').attr('disabled',false);
                    $('#schemename').attr('disabled',false);
                     $('#yearcode').attr('disabled',false);
                     $('#lastactionmonth').attr('disabled',false);
                      $('#lastactionyear').attr('disabled',false);

                     if (editor) editor.disableReadOnlyMode('customLock');
                        $('input[name="liability"]').prop('disabled', false);
            $('input[name="scheme"]').prop('disabled', false);
            $('#liabilityname').attr('disabled',false);
            $('#liabilitygpfno').attr('disabled',false);
            $('#liabilitydesig').attr('disabled',false);
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

                        var isSelected = minorobjection.subobjectionid === subobjectionid ? 'selected' :
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
        const $lagacy_form = $("#lagacy_form");


        $("#lagacy_form").validate({
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
            parano:
            {
      required:true,
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
            const firstError = $lagacy_form.find('.error:first');
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
            if (!(editor.getData())) {
                passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                return;
            }
            if ($("#lagacy_form").valid()) {


                get_insertLagacydata('insert');

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
            if (!(editor.getData())) {
                passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                return;
            }
            if ($("#lagacy_form").valid()) {
                // getLabels_jsonlayout([{
                //     id: 'finaliselagcy',
                //     key: 'finaliselagcy'
                // }], 'N').then((text) => {
                passing_alert_value('Confirmation', 'Are you sure to finalize the data?', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
                // }
                // );
                $('#process_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    get_insertLagacydata('finalise');
                });



            } else {
                scrollToFirstError();
            }
        });



        function get_insertLagacydata(action) {
            var statusflag = 'Y';
            if (action == 'finalise') {
                $('#process_button').attr('disabled', true)
                statusflag = 'F';
            } else {
                $('#buttonaction').attr('disabled', true)

            }

            var formData = new FormData($('#lagacy_form')[0]);
            var deactive_fileid = $('#deactive_fileid').val()
            var typeofauditcode = '<?php echo $financialauditcode; ?>';
            formData.append('statusflag', statusflag);
            formData.append('deactive_fileid', deactive_fileid);
            formData.append('typeofauditcode', typeofauditcode);


            formData.append('remarks', editor.getData());
            let checkboxValues = [];
            console.log('asd' + formData.get('deactive_fileid')); // should log "32524,32523"

            // Loop through each checkbox with the name 'activestatus[]'
            $('input[name="activestatus[]"]').each(function() {
                // If the checkbox is checked, push '1', otherwise push '0'
                checkboxValues.push(this.checked ? '1' : '0');
            });

            // Append the checkbox values to formData
            checkboxValues.forEach((value, index) => {
                formData.append(`activestatus[${index}]`, value); // Append each value with its index
            });

            $(".work-row").each(function(index) {
                let rowId = $(this).attr('id').replace("row", ""); // Extract row number

                formData.append(`liabilityid[]`, $(`#liabilityid${rowId}`).val());
                formData.append(`notype[]`, $(`select[name='notype${rowId}']`).val());
                formData.append(`name[]`, $(`#name${rowId}`).val());
                formData.append(`gpfno[]`, $(`#gpfno${rowId}`).val());
                formData.append(`designation[]`, $(`#designation${rowId}`).val());
                formData.append(`amount[]`, $(`input[name='amount${rowId}']`).val());

                // Handling checkbox values properly
                // let isChecked = $(`input[name="activestatus[]"]`).eq(index).is(":checked") ? '1' : '0';
                // formData.append(`activestatus[]`, isChecked);
            });

            $.ajax({
                url: '/lagacy/followup_insert', // For creating a new user or updating an existing one
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
                            fetch_lagacydata(response.data['followupid'], response.data[
                                    'tempnumberform'],
                                'fetchwithdata', 'Y', $('#filter').val(), '');
                        });

                        //   fetch_lagacydata('en')
                        //fetchAlldata();
                        // table.ajax.reload(); // Reload the table



                    }

                },
                complete: function() {
                    // Optionally, you can re-enable the button here if desired
                    if (action == 'finalise') {

                        $('#process_button').attr('disabled', false)

                    } else {
                        $('#buttonaction').attr('disabled', false)

                    }
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

        function addSlipNumber(slipNumber, id, slipby, processcode) {


            // Check if slipNumber is not provided
            if (!slipNumber) {
                slipNumber = 'NEW';

            }

            // Ensure id is not null or undefined (set to empty string by default)
            if (!id) id = '';

            // Get the 'ul' element where the slip numbers are listed
            const chatUsersList = document.querySelector(".chat-users");

            // Create a new 'li' element for the new slip number
            const newListItem = document.createElement("li");

            seriesno = $('#seriesno').val();

            slipnumberatbox = slipNumber


            // Add the HTML content for the new 'li'
            newListItem.innerHTML = `
            <div class="hstack ${processcode =='F' ? 'drop_div' :''}  p-2 bg-hover-light-black position-relative border-bottom " id="${seriesno}" onclick="setform_basedonslipnumber('${seriesno}')">
            <input type="hidden" id="slipid_${seriesno}" name="slipid" value="${id}">
            <input type="hidden" id="slipnumber_${seriesno}" name="slipnumber_${seriesno}" value='${slipNumber}'>

            <a style="color:black;" href="javascript:void(0)" class="stretched-link"></a>
            <div class="ms-2">
                <a style="color:black;" href="javascript:void(0)">
                    <i class="text-primary ri ri-clipboard-text fs-5"></i>
                </a>
            </div>
            <div class="ms-auto ">
                <h6 class="mb-0 fs-2">${slipnumberatbox}</h6>
            </div>
            <div class="ms-auto fs-2">
                <a style="color:black;" href="javascript:void(0)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"
                        class="icon icon-tabler icons-tabler-filled icon-tabler-arrow-big-right-lines slip-arrow" style="visibility:hidden" id="arrow_${seriesno}">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12.089 3.634a2 2 0 0 0 -1.089 1.78l-.001 2.585l-1.999 .001a1 1 0 0 0 -1 1v6l.007 .117a1 1 0 0 0 .993 .883l1.999 -.001l.001 2.587a2 2 0 0 0 3.414 1.414l6.586 -6.586a2 2 0 0 0 0 -2.828l-6.586 -6.586a2 2 0 0 0 -2.18 -.434l-.145 .068z" />
                        <path d="M3 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
                        <path d="M6 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
                    </svg>
                </a>
            </div>
            </div>`;


            // Append the new 'li' to the list
            chatUsersList.appendChild(newListItem);

            // Increment the slip number and series number
            // slipNumber = slipNumber + 1;
            seriesno = Number($('#seriesno').val()) + 1;
            $('#seriesno').val(seriesno);

            // Flag to check if the click handler has been triggered before
            let clickHandled = false;

        }

        function setform_basedonslipnumber(seriesno) {

            reset_form();

            const clickedId = seriesno; // Get the ID of the clicked element
            currentslipnumber = $('#slipnumber_' + clickedId).val();
            currentslipid = $('#slipid_' + clickedId).val();



            $('#currentslipnumber').val(currentslipnumber);
            $('#followupid').val(currentslipid);

            if (currentslipid) {
                fetch_lagacydata(currentslipid, '',
                    'edit', '', $('#filter').val(), );

            } else {
                for_newslip_resetform(clickedId)
            }

            // $('#finalisebtn').hide();
            //  $('#freshformbtn').show();

            $(".slip-arrow").css("visibility", "hidden");
            $('.hstack').removeClass("active_div");
            $('#arrow_' + clickedId).css("visibility", "visible");
            $('#' + clickedId).addClass("active_div");
            appendFilePreview();
        }

function onchange_actiontaken(val, selectedmonth, selectedyear) {

            var actiontaken = $('#actiontaken').val();

            if (actiontaken == 'Y') {
                $('#actiontaken_div').css('display', 'block');
            }
            // else if ((selectedmonth != '' || selectedmonth != null) && actiontaken == '') {
            //     $('#actiontaken_div').show();
            //     $('#actiontaken').val('Y')
            //     $('#actiontaken').select2();
            // }
            else if (actiontaken == 'N' || ((selectedmonth == null || selectedmonth === '') &&
                    (selectedyear == null || selectedyear === ''))) {
                $('#lastactionmonth,#lastactionyear').val('');
                $('#lastactionmonth,#lastactionyear').select2();
                $('#actiontaken_div').css('display', 'none');

                $('#actiontaken').val('N')
                $('#actiontaken').select2();

                //  alert(actiontaken)




            } else {
                $('#actiontaken_div').css('display', 'block');
                $('#actiontaken').val('Y')
                $('#actiontaken').select2();
            }
        }

        function reset_form() {

            // buttonname = @json($approvebtn);
            var preservedyearcode = $('#yearcode').val();
            fileCount = 0;
            // changeButtonAction('auditslip', 'action', 'buttonaction', 'approvebtn', 'display_error',
            //@json($savedraftbtn), buttonname, 'insert')
            change_button_as_insert('lagacy_form', 'action', 'buttonaction', 'display_error', '');

            // Detect the selected language (default to English if not set)
            const lang = getLanguage('');
            $('#mainobjectionid,#typeofparacode,#stateofparacode').select2('destroy');
            $('#mainobjectionid,#typeofparacode,#stateofparacode').select2(null);
            $('#mainobjectionid,#typeofparacode,#stateofparacode').select2();


            // Set the default option based on language
            const optionText = lang === 'ta' ? 'துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்' : 'Select sub objection';

            $("#subobjectionid")
                .empty()
                .append(
                    `<option value='' data-name-en='Select sub objection' data-name-ta='துணை ஆட்சேபனையைத் தேர்ந்தெடுக்கவும்'>${optionText}</option>`
                );

            $('#severityid').val('');

            $('input[name="liability"][value="N"]').prop('checked', true);
            $('#dynamicRowsContainer').empty();


            $('#liabilityname_div').hide();
            $('#liabilitygpfno_div').hide();
            editor.setData('');
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
            $('#yearcode').val(preservedyearcode);
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





            if ( (fileCount < maxfilecount)) {
                $('#file-input-container').show()
                $('#add-file-btn').show()
            }



            // Join the array with commas and update the deactive_fileid hidden input field
            $('#deactive_fileid').val(deactiveFileIds.join(','));
            var deactive = $('#deactive_fileid').val()

        }

        /**************************************** Fit the upload files, delete upload file in edit **********************/
    </script>
@endsection
