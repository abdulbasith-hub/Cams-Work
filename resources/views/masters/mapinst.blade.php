@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Institution')


@php

$sessionchargedel = session('charge');
$inst_quat =$inst_quarter;

$deptcode = $sessionchargedel->deptcode;
$regioncode = $sessionchargedel->regioncode;
$distcode = $sessionchargedel->distcode;

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_dist_disable = $distcode ? 'disabled' : '';

$distroletype =View::shared('Dist_roletypecode');
$sessionroletypecode = $sessionchargedel->roletypecode;

$dga_roletypecode = $DGA_roletypecode;
$Dist_roletypecode = $Dist_roletypecode;
$Re_roletypecode = $Re_roletypecode;
$Ho_roletypecode = $Ho_roletypecode;
$Admin_roletypecode = $Admin_roletypecode;

$currentquarter= $currentquarter;
$currentauditmodecode = 'N';


$quarterDetails =$quarterdetails[0];


if ($deptcode == '03') {

    $allowed_dept = ['03', '02'];

    $dept = array_values(array_filter($dept, function ($item) use ($allowed_dept) {
        return in_array($item->deptcode, $allowed_dept);
    }));
}

if($sessionroletypecode == $dga_roletypecode ||$sessionroletypecode == $NIC_roletypecode ){

$showFilter = true;

}
else
{
$showFilter = false;
}

@endphp

<style>
    .loader_hide {
    display: none !important;
}
    #datatable_loader {
        text-align:center; padding:30px;
    }
    .disabled-checkbox {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .disabled-checkbox label {
        color: #999;
    }

    #quarterDisabledMessage {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        margin-top: 10px;
    }
    

</style>

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="institute_detail">
                Institute Details
            </div>
            <div class="card-body collapse show">
                <form id="mapinst_form" name="mapinst_form">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" class="form-control" id="instid" name="instid" />
                    <input type="hidden" class="form-control" id="auditeeuserid" name="auditeeuserid" />
<input type="hidden" id="deptcode_hidden" name="deptcode_hidden" value="">
                        <input type="hidden" id="catcode_hidden" name="catcode_hidden" value="">
                        <input type="hidden" id="subcatid_hidden" name="subcatid_hidden" value="">
<input type="hidden" id="apms_inst" name="apms_inst" value="">

                    <div class="row">

                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department </label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="deptcode" name="deptcode"
                                onchange="onchange_deptcode('','','')" <?php echo $make_dept_disable; ?>>
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---
                                </option>

                                @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                <option value="{{ $department->deptcode }}"
                                    @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                                    data-name-en="{{ $department->deptelname }}"
                                    data-name-ta="{{ $department->depttlname }}">
                                    {{ $department->deptelname }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="instcat_label"
                                for="validationDefault01">Category </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="cat_code" name="cat_code"
                                onchange="onchange_catcode('','','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>


                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="subcat_div">
                            <label class="form-label lang required " key="sub_head" for="validationDefault01">
                                Sub
                                Category </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcatid" name="subcatid">
                                <option value="" data-name-en="---Select Sub Category---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select Sub Category---
                                </option>


                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 " id="region_div">
                            <label class="form-label lang required" key="region" for="validationDefault01">Region
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" onchange="onchange_region('','')"
                                id="regioncode" name="regioncode" <?php echo $make_region_disable; ?>>
                                <option value="" data-name-en="---Select Region---"
                                    data-name-ta="---பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>
                                @if($regioncode)
                                @if (!empty($region) && count($region) > 0)
                                @foreach ($region as $regiondet)
                                <option value="{{ $regiondet->regioncode }}"
                                    @if (old('region', $regioncode)==$regiondet->regioncode) selected @endif
                                    data-name-en="{{ $regiondet->regionename }}"
                                    data-name-ta="{{ $regiondet->regiontname }}">
                                    {{ $regiondet->regionename }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 " id="dist_div">
                            <label class="form-label lang required" key="district" for="validationDefault01">District
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                onchange="onchange_distcode()" <?php echo $make_dist_disable; ?>>
                                <option value="" data-name-en="---Select District---"
                                    data-name-ta="--- மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>
                                @if($distcode)
                                @if (!empty($district) && count($district) > 0)
                                @foreach ($district as $dist)
                                <option value="{{ $dist->distcode }}"
                                    @if (old('dist', $distcode)==$dist->distcode) selected @endif
                                    data-name-en="{{ $dist->distename }}"
                                    data-name-ta="{{ $dist->disttname }}">
                                    {{ $dist->distename }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                                @endif
                            </select>
                        </div>
                                                <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="audit_mode_label"
                                for="validationDefault01">Audit Mode</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_mode"
                                name="audit_mode" onchange="onchange_mode('','','','','')">
                                <option value="" data-name-en="---Select Mode---"
                                    data-name-ta="---பயன்முறையைத் தேர்ந்தெடுக்கவும்---">---Select Mode
                                </option>
                                @foreach ($auditmode as $mode)
                                <option value="{{ $mode->auditmodecode }}"


                                    data-name-en="{{ $mode->auditmodeename }}"
                                    data-name-ta="{{ $mode->auditmodetname }}">
                                    {{ $mode->auditmodeename }}
                                </option>
                                @endforeach



                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_office_label" for="validationDefault01">Audit
                                Office Name
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="auditoffice"
                                name="auditoffice" onchange="onchange_auditoffice()">
                                <option value="" data-name-en="---Select Audit Office---"
                                    data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Office---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_office_desig_label"
                                for="validationDefault01">Audit
                                Officer Designation
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_office_desig"
                                name="audit_office_desig" onchange="onchange_auditofficedesig()">
                                <option value="" data-name-en="---Select Audit Office Designation---"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Office
                                    Designation---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_ad_label" for="validationDefault01">AAD /
                                AAO Name
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_ad" name="audit_ad">
                                <option value="" data-name-en="---Select Audit AAO/AD---"
                                    data-name-ta="--- AAO/AD தேர்ந்தெடுக்கவும்---">---Select AD/AAO---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="auditee_dept_label" for="validationDefault01">Auditee
                                Department
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="auditee_dept"
                                name="auditee_dept">
                                <option value="" data-name-en="---Select Auditee Department---"
                                    data-name-ta="--- தணிக்கை துறையைத் தேர்ந்தெடுக்கவும்---">---Select Auditee
                                    Department---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="revenuedistrict"
                                for="validationDefault01">Revenue
                                District
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="revenuedistcode"
                                name="revenuedistcode">
                                <option value="" data-name-en="---Select Revenue District---"
                                    data-name-ta="---வருவாய் மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---">---Select Revenue
                                    District---
                                </option>

                                @foreach ($revenuedistrict as $revenuedistrictdata)
                                <option value="{{ $revenuedistrictdata->revenuedistcode }}"
                                    data-name-en="{{ $revenuedistrictdata->revenuedistename }}"
                                    data-name-ta="{{ $revenuedistrictdata->revenuedisttname }}">
                                    {{ $revenuedistrictdata->revenuedistename }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang" for="validationDefault02" key="erpno_label" id="itmsno_label">ITMS/Reg
                                NO/Reference No
                            </label>
                            <input class="form-control alpha_numeric" id="itms_no" name="itms_no" maxlength="20"
                                data-placeholder-key="" placeholder='Enter the ITMS Number' />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02"
                                key="institution_eng_name">Institutte English Name
                            </label>
			    <input class="form-control name_special dynamic_keypress"
		    data-regex-char="^[a-zA-Z0-9\/\.\,\(\) -]$"
		     data-regex-full="^(?![0-9]{10,200}$)(?![\\-\\/\\.,\\(\\) ]{10,200}$)(?!([a-zA-Z0-9\\-\\/\\.,\\(\\) ])\\1+$)[a-zA-Z0-9\\-\\/\\.,\\(\\) ]{10,200}$"
		    id="instename" name="instename" maxlength="200" data-placeholder-key="instename" />
		</div>
		<div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02"
                                key="institution_tam_name">Institute Tamil Name
                            </label>
                        <input class="form-control dynamic_keypress"
    data-regex-char="^[a-zA-Z\u0B80-\u0BFF0-9\-\.,\(\)\/ ]$"
    data-regex-full="^(?![0-9 ]+$)(?!([\u0B80-\u0BFFa-zA-Z0-9\-\.,\/\(\) ])\1+$)(?=.*[\u0B80-\u0BFFa-zA-Z])[\u0B80-\u0BFFa-zA-Z0-9\-\.,\/\(\) ]{10,200}$"
    id="insttname"
    name="insttname"
    maxlength="200"
/>
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" key="fees">Fees
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="fees"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="fees"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label lang required" key="categorization_label"
                                for="validationDefault01">Categorization based Income Turnover</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="categorization"
                                name="categorization">
                                <option value="" data-name-en="---Select Income Turnover---"
                                    data-name-ta="---வருமான வருவாயைத் தேர்ந்தெடுக்கவும்---">---Select Income
                                    Turnover---
                                </option>
                                <option value="I">I</option>
                                <option value="II">II
                                </option>
                                <option value="III">III</option>
                                <option value="IV">
                                    IV
                                </option>
                                <option value="V">
                                    V
                                </option>


                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="row py-2">
                                <div class="col-md-6">
                                    <label class="form-label required lang" for="validationDefault02"
                                        key="applicable_for_label">Applicable
                                    </label>
                                </div>
                                <div class="row mt-2">
                                    <div class="">
                                        @foreach ($inst_quat as $quarter)
                                            <div class="form-check form-check-inline">
                                                <input
                                                    class="form-check-input success"
                                                    name="applicablefor[]"
                                                    type="checkbox"
                                                    id="{{ $quarter->auditquartercode }}"
                                                    value="{{ $quarter->auditquartercode }}"
                                                    onchange="onchange_applicablefor(event)"
                                                    disabled>

                                                <label class="form-check-label" for="{{ $quarter->auditquartercode }}">
                                                    {{ $quarter->auditquarter }}
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>

                                </div>

                            </div>
                        </div>



                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="total_mandays">Total
                                Mandays
                            </label>
                            <input class="form-control only_numbers" id="mandays" name="mandays" maxlength="4"
                                data-placeholder-key="mandays" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="teamsize">Team Size
                            </label>
                            <input class="form-control only_numbers" id="team_size" name="team_size" maxlength="2"
                                data-placeholder-key="team_size" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="inst_type" for="validationDefault01">Institution Type</label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="inst_type" name="inst_type" onchange="onchange_insttype('')">
                                <option value="" data-name-en="---Select Institution Type---"
                                    data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select Institution Type---
                                </option>


                                <option value="H" data-name-en="Hub" data-name-ta="நோடல்">Hub</option>
                                <option value="S" data-name-en="Spoke" data-name-ta="துணை">Spoke
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="hubtype_div">
                                <label class="form-label lang" for="hubtypeid" id="hubtypeid_label" key="hub_type">
                                    Hub Type
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown" id="hubtypeid" name="hubtypeid" onchange="onchange_hubtype('','')">
                                    <option value="" data-name-en="---Select Hub Type---" data-name-ta="--- ஹப் வகையைத் தேர்வு செய்யவும் ---">
                                        ---Select Hub Type---
                                    </option>
                                    <option value="A" data-name-en="Auditable Hub" data-name-ta="தணிக்கைக்குட்பட்ட ஹப்">
                                        Auditable Hub
                                    </option>
                                    <option value="O" data-name-en="Non-Auditable Hub" data-name-ta="தணிக்கைக்குட்படாத ஹப்">
                                        Non-Auditable Hub
                                    </option>
                                </select>
                        </div>


                        <div class="col-md-3 mb-1 mt-2 hide_this" id="hubdesigtype_div">
                                <label class="form-label required lang" key=""
                                    for="institution">Non Auditable Hub</label>
                                <select class="form-select mr-sm-2 select2 lang-dropdown"  id="hubdesigcode" name="hubdesigcode" >
                                <option value="" data-name-en="Select Designation"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">Select Non Auditable Hub</option>

                                </select>
                        </div>
<div class="col-md-3 mb-1 mt-2 hide_this" id="zone_div">
                            <label class="form-label required lang" key=""
                                for="">Zone Name</label>
                            <select class="form-select mr-sm-2 select2 lang-dropdown"  id="zonecode" name="zonecode" >
                            <option value="" data-name-en="Select Zone Name"
                                data-name-ta="---மண்டலத்தின் பெயரைத் தேர்ந்தெடுக்கவும்---">Select Zone Name</option>

                            </select>
                    </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="parentinst_div">
                            <label class="form-label  lang " for="validationDefault02" id="parentinstid_label"
                                key="parent_inst">Parent Institution Name
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="parentinstid" name="parentinstid">
                                <option value="" data-name-en="---Select Parent  Institution ---"
                                    data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select
                                    Parent Institution ---
                                </option>
                            </select>

                        </div>


                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label lang required" key="risk_type" for="validationDefault01">Risk
                                Type</label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="risktype" name="risktype">
                                <option value="" data-name-en="---Select Risk Type---"
                                    data-name-ta="--- ஆபத்து வகை தேர்ந்தெடுக்கவும்---">---Select Risk Type---
                                </option>


                                <option value="H" data-name-en="High" data-name-ta="உயர்">High</option>
                                <option value="M" data-name-en="Medium" data-name-ta="நடுத்தரம்">Medium
                                </option>
                                <option value="L" data-name-en="Low" data-name-ta="குறைந்த">Low</option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" key="rank_order">Rank
                                Order
                            </label>
                            <input class="form-control only_number" id="rankorder" name="rankorder"
                                data-placeholder-key="rankorder" maxlength="4" />
                        </div>

                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label lang required" key="audit_sensitive"
                                for="validationDefault01">Audit
                                Sensitive </label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="typeofaudit" name="typeofaudit">
                                <option value="" data-name-en="---Select Audit Sensitive---"
                                    data-name-ta="---தணிக்கை உணர்திறனைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Sensitive---
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="carry_forward_label">Carry
                                Forward
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cary_fwd"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cary_fwd"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="consolidated_label">Consolidated Report
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cons_report"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cons_report"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="assemble_report">Assembly
                                Report
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="assemble_report"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="assemble_report"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" id="nodalperson_ename_label"
                                key="nodal_eng_name">Nodal
                                Person English Name
                            </label>
 <input class="form-control  dynamic_keypress"
                              data-regex-char="^[a-zA-Z\. ]$"
                                data-regex-full="^(?!([a-zA-Z\. ])\1+$)[a-zA-Z\. ]{3,75}$"
                                 id="nodalperson_ename" name="nodalperson_ename"
                                maxlength="75" data-placeholder-key="nodalperson_ename" />                        
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" id="nodalperson_tname_label"
                                key="nodal_tam_name">Nodal
                                Person Tamil name
                            </label>
<input class="form-control dynamic_keypress"
                            data-regex-char="^[஀-௿ ]$"
                            data-regex-full="^(?!([஀-௿ ])\\1+$)(?=.*[஀-௿])[஀-௿ ]{3,75}$"
                            id="nodalperson_tname" name="nodalperson_tname"
                                maxlength="75" data-placeholder-key="nodalperson_tname" />                        
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" key="email" id="email_label">Email
                            </label>
<input type="email"
                            data-regex-char="^[a-zA-Z0-9@._\\-]$"
                            data-regex-full="^[a-zA-Z0-9._%\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}$"
                            class="form-control dynamic_keypress" id="email" name="email" maxlength="100"
                                data-placeholder-key="email" />                        
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" key="mobile" id="mobile_label">Mobile
                                Number
                            </label>

<input class="form-control only_numbers  dynamic_keypress"
                            data-regex-char="^[0-9]$"
                            data-regex-full="^(?!([0-9])\\1{9}$)[6-9][0-9]{9}$"
                            id="mobile" name="mobile" maxlength="10"
                                data-placeholder-key="mobile" />
                        </div>


                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang " for="validationDefault02" id="nodalperson_desigcode_label"
                                key="nodal_desig">Nodal
                                Person Designation
                            </label>
 <input class="form-control name dynamic_keypress"
                            data-regex-char="^[a-zA-Z஀-௿\\.\\-\\(\\) ]$"
                            data-regex-full="^(?!([a-zA-Z஀-௿\\.\\-\\(\\) ])\\1+$)(?=.*[a-zA-Z஀-௿])[a-zA-Z஀-௿\\.\\-\\(\\) ]{3,100}$"
                             id="nodalperson_desigcode" name="nodalperson_desigcode"
                            maxlength="100" data-placeholder-key="nodalperson_desigcode" />                        
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang " for="validationDefault02" key="auditee_offc_addr_label" id="auditee_ofaddr_label">Auditee
                                Office Address
                            </label>
                        
<input class="form-control alpha_numeric dynamic_keypress"
                                id="auditee_ofaddr" name="auditee_ofaddr"
                                 data-regex-char="^[a-zA-Z஀-௿\\.,\\-\\/\\(\\) ]$"
                                data-regex-full="^(?!([a-zA-Z஀-௿\\.,\\-\\/\\(\\) ])\\1+$)(?=.*[a-zA-Z஀-௿])[a-zA-Z஀-௿\\.,\\-\\/\\(\\) ]{5,250}$"
                                maxlength="250" data-placeholder-key="nodalperson_desigcode"
                                placeholder="Enter Auditee Office Address" />
</div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02"
                                key="audit_certificate">Audit
                                Certificate
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="auditcertificate"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="auditcertificate"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang" for="validationDefault02" key="annadhanam_label">
                                Is Annadhanam required?
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="annadhanam_only"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="annadhanam_only"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 notfortemp">
                            <label class="form-label required lang " for="validationDefault02" key="turn_over_label">Turn
                                Over
                            </label>
                            <input class="form-control decimal_numbers" maxlength="15" id="turnover" name="turnover" maxlength="100"
                                data-placeholder-key="" placeholder="Enter Turnover (Amount Ltr)" />
                        </div>
		<div class="col-md-3 mb-1 mt-2" id="inst_kms_div">
                            <label class="form-label required lang" for="validationDefault02" key="inst_kms">Institution
                                KMs</label>
                            <input class="form-control inst_kms_special" id="inst_kms" name="inst_kms" maxlength="6"
                                data-placeholder-key="" placeholder="Enter kilometers (e.g., 123.45)" />
                        </div>

                        <div class="col-md-3 mb-1 mt-2" id="inst_priority_kms_div">
                            <label class="form-label required lang" key="priority_label"
                                for="validationDefault01">Priority</label>
                            <select class="form-select mr-sm-2 lang-dropdown" id="inst_priority_kms"
                                name="inst_priority_kms">
                                <option value="" data-name-en="---Select Priority---"
                                    data-name-ta="---முன்னுரிமையைத் தேர்ந்தெடுக்கவும்---">---Select Priority---</option>
                                <option value="01" data-name-en="Plan 1" data-name-ta="பி1">P1</option>
                                <option value="02" data-name-en="Plan 2" data-name-ta="பி2">P2</option>
                            </select>
                        </div>


                        <hr class="p-2 mt-2">
                        <div class="col-12 ">
                            <div class="card ">
                                <div class="card-header">
                                    <h5>Audit Reporting Officer</h5>
                                </div>
                                <div class="card-body">
                                    <div id="appendusers" class="single-note-item">
                                        <div id="addrowUsers">
                                            <div class="row">
                                                <div class="col-md-1">

                                                </div>

                                                <div class="col-md-3 ms-2">

                                                </div>


                                                <div class="col-md-3 ms-2">


                                                </div>

                                                <div class="col-md-3">

                                                </div>
                                            </div>
                                            <div class="d-flex mt-2 work-row-insert" id="row0">
                                                <input type="hidden" name="auditee_report[1]" id="auditee_report0"
                                                    value="UserId_1">

                                                <div class="col-md-1">
                                                    <label class="form-label lang" key="s_no"
                                                        for="validationDefaultUsername">S.No</label>
                                                    <input type="text" class="form-control alpha_numeric"
                                                        value="1" disabled>
                                                </div>



                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang" key="designation"
                                                        for="validationDefaultUsername">Designation</label>
                                                    <input type="text" class="form-control name"
                                                        data-placeholder-key="designation_ph"
                                                        name="reportdesignation[1]" id="reportdesignation0"
                                                        value="" placeholder="Enter Designation">
                                                </div>

                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang"
                                                        for="validationDefaultUsername">Email</label>
                                                    <input type="email" class="form-control "
                                                        data-placeholder-key="" name="reportemail[1]"
                                                        id="reportemail0" value="" placeholder="Enter Email" />
                                                </div>


                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang" key="mobile"
                                                        for="validationDefaultUsername">Mobile Number</label>
                                                    <input type="text" class="form-control only_numbers"
                                                        data-placeholder-key="" name="reportmobile[1]"
                                                        id="reportmobile0" value="" maxlength="10"
                                                        placeholder="Enter Mobie" />
                                                </div>

                                                <div class="col-md-3 actionbtns">
                                                    <label class="form-label lang" key="action"
                                                        for="validationDefaultUsername">Action</label><br>
                                                    <button type="button"
                                                        class="btn btn-success fw-medium ms-2 addRowBtn"
                                                        onclick="addNewWorkRow(event,'insert')">
                                                        <i class="ti ti-circle-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="EditrowUsers">

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
            </div>


            <div class="row justify-content-center">
                <div class="col-md-2 mx-auto mb-2">
                    <input type="hidden" name="action" id="action" value="insert" />
                    <button class="btn button_save" type="submit" action="insert" id="buttonaction"
                        name="buttonaction">Save</button>
                    <button type="button" class="btn btn-danger" id="reset_button"
                        onclick="reset_form()">Clear</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="card card_border mt-2">
    <div class="card-header card_header_color lang" key="institute_detail">Institution Details</div>
    <div class="card-body">
        <div class="datatables">

       <div class="row">
			 @php
            if ($showFilter){
            @endphp
                <div class="col-md-3 mb-1 mt-2 text-center">
                    <label class="form-label lang required" key="department" for="deptcode1">Department</label>
                    <select class="form-select lang-dropdown" id="deptcode1" name="deptcode1" onchange="fetchAlldata()" style="max-width: 100%;">
                        @if (!empty($dept) && count($dept) > 0)
                        @foreach ($dept as $department)
                        <option value="{{ $department->deptcode }}"
                            @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                            data-name-en="{{ $department->deptelname }}"
                            data-name-ta="{{ $department->depttlname }}">
                            {{ $department->deptelname }}
                        </option>
                        @endforeach
                        @else
                        <option disabled data-name-en="No Department Available" data-name-ta="No Department Available">
                            No Departments Available
                        </option>
                        @endif
                    </select>
                </div>
			@php
            }
            @endphp
                <div class="col-md-3 mb-1 mt-2 text-center">
                    <label class="form-label lang required" key="quarter" for="quarter">Quarter</label>
                    <select class="form-select lang-dropdown" id="quarter" name="quarter" onchange="fetchAlldata()" style="max-width: 100%;">
                        @foreach ($inst_quat as $quat)
                                <option value="{{ $quat->auditquartercode }}"
                                    data-name-en="{{ $quat->auditquarter }}"
                                    data-name-ta="{{ $quat->auditquarter }}">
                                    {{ $quat->auditquarter }}
                                </option>
                                @endforeach
                    </select>
                </div>
		 <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="audit_mode_label"
                                for="validationDefault01">Audit Mode</label>

                            <select class="form-select mr-sm-2 lang-dropdown" id="filter_auditmode"
                                name="filter_auditmode" onchange="fetchAlldata()">

                                @foreach ($auditmode as $mode)
                                <option value="{{ $mode->auditmodecode }}"


                                    data-name-en="{{ $mode->auditmodeename }}"
                                    data-name-ta="{{ $mode->auditmodetname }}">
                                    {{ $mode->auditmodeename }}
                                </option>
                                @endforeach



                            </select>
                        </div>
                    </div>
            </div>


<div id="datatable_loader"  class="hide_this">
        <div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;"></div>
        <div class="mt-2">Loading data...</div>
</div>

              <div class="table-responsive hide_this" id="tableshow">




                <table id="mapinst_table"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang" key="s_no">S.No</th>
                            <th class="lang" key="department">Department Name</th>
                            <th class="lang" key="instname_label">Institute Name</th>
                            <th class="lang" key="category">Category</th>

                            <th class="lang" key="region">Region</th>
                            <th class="lang" key="district">District</th>
			<th class="lang" key="inst_kms">Inst. KMs</th>
                            <th class="lang" key="inst_priority_kms">Priority</th>
                            <th class="lang" key="rankorder">Rank Order</th>
                            {{-- <th class="lang" key="">Fees</th>
                                <th class="lang" key="">Mandays</th>
                                <th class="lang" key="">Risk Type</th> --}}

                            <th class="lang" key="nodal_details">Nodal Person Details</th>

                            <th class="all lang" key="action">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id='no_data' class='hide_this lang text-center' key="no_data">
            <center class="lang" key="no_data">No Data Available</center>

        </div>
    </div>
</div>
</div>
<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script>
    $(document).on('change', '#subcatid', function() {
    const catcode = $('#cat_code').val();
    const subcatid = $(this).val();
    disableQuartersBasedOnCategory(catcode, subcatid);
});

function disableQuartersBasedOnCategory(catcode, subcatid = null) {

    const disableQuarterCombinations = [
        { catcode: '34', subcatid: 180 },
    ];

    const quarterCheckboxes = document.querySelectorAll('input[name="applicablefor[]"]');

    let shouldDisable = false;

    for (let i = 0; i < disableQuarterCombinations.length; i++) {
        const combo = disableQuarterCombinations[i];
        if (combo.catcode === catcode) {
            if (combo.subcatid === subcatid) {
                shouldDisable = true;
                break;
            }
        }
    }

    if (shouldDisable) {
        quarterCheckboxes.forEach((checkbox) => {
            checkbox.disabled = true;
            checkbox.checked = false;
            checkbox.parentElement.classList.add('disabled-checkbox');
        });

        $('input[name="applicablefor[]"]').rules('remove', 'required');
    } else {
        const auditmodeCode = $('#audit_mode').val();

        quarterCheckboxes.forEach((checkbox) => {
            checkbox.parentElement.classList.remove('disabled-checkbox');

            if (auditmodeCode === 'N' || auditmodeCode === 'T') {
                const currentquartercode = '<?php echo $quarterDetails->currentquarter ?>';
                checkbox.disabled = (checkbox.value === currentquartercode);
            } else if (auditmodeCode === 'C') {
                checkbox.disabled = true;
                checkbox.checked = true;
            } else if (auditmodeCode === 'Q') {
                checkbox.disabled = false;
            } else {
                checkbox.disabled = false;
            }
        });


        if (auditmodeCode !== 'T') {
            $('input[name="applicablefor[]"]').rules('add', { required: true });
        }
    }
}



let sessionroletype = '<?php echo $sessionroletypecode; ?>'
        let dgaroletypecode = '<?php echo $DGA_roletypecode; ?>'
        let allowed_dept = ['02','03'];
        let dept_values = @json($dept);
		
    let designationCount = 1; // Start count
    let designationdata = [];

    let nonmandatory_field = ['nodalperson_ename_label','nodalperson_tname_label','email_label','mobile_label','nodalperson_desigcode_label','auditee_ofaddr_label'];
    let nonmandatory_input = [ "nodalperson_ename","nodalperson_tname","email",'mobile','nodalperson_desigcode','auditee_ofaddr'];


    $(document).on('change', '#inst_type', function () {
            $('#hubtypeid').val('');
    });
    // Keypress: allow digits, one dot, and restrict input based on the rules
    $(document).on("keypress", ".decimal_numbers", function(e) {
        const char = String.fromCharCode(e.which);
        const val = $(this).val();

        // Allow digits (0-9) only
        if (/\d/.test(char)) {
            // If no decimal point, allow up to 10 digits
            if (!val.includes('.') && val.length < 12) {
                return true;
            }
            // If decimal point is present, allow only two digits after the decimal
            if (val.includes('.') && val.split('.')[1]?.length < 2) {
                return true;
            }
        }

        // Allow dot only if there's no dot already
        if (char === '.' && !val.includes('.')) {
            return true;
        }

        // Block other characters
        e.preventDefault();
    });

    // Paste: allow only numbers with optional one dot and up to two decimals, and a max of 10 digits before decimal
    $(document).on("paste", ".decimal_numbers", function(e) {
        const pasted = (e.originalEvent || e).clipboardData.getData('text').trim();

        // Valid if digits with optional single decimal point and up to 10 digits before decimal
        if (!/^\d{1,10}(\.\d{0,2})?$/.test(pasted)) {
            e.preventDefault();
        }
    });

    // Blur: format to 2 decimal places (if there's a decimal point, and two digits after it)
    $(document).on("blur", ".decimal_numbers", function() {
        let val = $(this).val().trim();

        // If valid number, format to 2 decimal places
        if (/^\d+(\.\d{1,2})?$/.test(val)) {
            const num = parseFloat(val);
            if (!isNaN(num)) {
                $(this).val(num.toFixed(2)); // Example: 34 → 34.00 or 34.4 → 34.40
            }
        }
    });



    $(".name_special").on("keypress", function(event) {
        var charCode = event.which || event.keyCode;
        var charStr = String.fromCharCode(charCode);

        // Allow English letters, Tamil letters (U+0B80–U+0BFF), digits, and hyphen
        if (/^[a-zA-Z஀-௿0-9\-\/.,() ]$/.test(charStr)) {
            return true;
        } else {
            event.preventDefault(); // Block invalid characters
            return false;
        }
    });
    // Regex pattern to allow English letters, Tamil letters (U+0B80 - U+0BFF), digits, space, and special characters - / . ,
    const allowedPattern = /^[a-zA-Z஀-௿0-9\-\/., ]+$/;

    // Apply validation on keypress and clean invalid characters on paste
    function applyInputRestrictions(selector) {
        $(document).on("keypress", selector, function(event) {
            const charCode = event.which || event.keyCode;
            const charStr = String.fromCharCode(charCode);

            if (!allowedPattern.test(charStr)) {
                event.preventDefault();
                return false;
            }
            return true;
        });

        $(document).on("paste", selector, function(event) {
            const clipboardData = (event.originalEvent || event).clipboardData.getData("text");
            const cleaned = clipboardData.split('').filter(char => allowedPattern.test(char)).join('');

            event.preventDefault();

            const input = event.target;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const value = input.value;

            input.value = value.slice(0, start) + cleaned + value.slice(end);
            input.setSelectionRange(start + cleaned.length, start + cleaned.length);
        });
    }

    // Apply on multiple fields
    applyInputRestrictions("#instename, #insttname, .text_special");


    function removedeptRow(button, action) {
        // Find the closest row and remove it
        const rowToRemove = $(button).closest('.work-row-' + action + '');
        rowToRemove.remove();

        // Recalculate and update the S.NO values after removing a row
        //updateSerialNumbers();
    }
    // Allowed characters during typing
    const emailCharRegex = /^[a-zA-Z0-9@._\-+]+$/;
    // Full email format validation (e.g. abc@gmail.com)
    const fullEmailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Keypress: block disallowed characters
    $(document).on('keypress', 'input[type="email"]', function(e) {
        const char = String.fromCharCode(e.which);
        if (!emailCharRegex.test(char)) {
            e.preventDefault();
        }
    });

    // Paste: prevent pasting invalid characters
    $(document).on('paste', 'input[type="email"]', function(e) {
        const pastedText = (e.originalEvent || e).clipboardData.getData('text');
        if (!emailCharRegex.test(pastedText)) {
            e.preventDefault();
        }
    });

    // Blur: validate full email format
    $(document).on('blur', 'input[type="email"]', function() {
        const email = $(this).val().trim();
        if (email && !fullEmailRegex.test(email)) {

            $(this).addClass('is-invalid'); // Add error class (optional)
        } else {
            $(this).removeClass('is-invalid'); // Remove error if valid
        }
    });

    $(document).on("keypress", ".only_numbers", function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode >= 48 && charCode <= 57) {
            return true;
        } else {
            event.preventDefault(); // prevent invalid character input
        }
    });
    const mobileRegex = /^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/; // Validates 10-digit starting with 6-9 and not all same digits

    // Keypress: allow only digits (0-9)
    $(document).on('keypress', 'input.mobile_number', function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    });

    // Paste: prevent if not digits or invalid format
    $(document).on('paste', 'input.mobile_number', function(e) {
        const pasted = (e.originalEvent || e).clipboardData.getData('text');
        if (!/^\d+$/.test(pasted) || pasted.length > 10) {
            e.preventDefault();
        }
    });

    // Blur: full mobile format validation
    $(document).on('blur', 'input.mobile_number', function() {
        const mobile = $(this).val().trim();
        if (!mobileRegex.test(mobile)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

function addNewWorkRow(event, action = '') {
        // Get the next row index based on existing rows
        const rowCount = $('.work-row-' + action + '').length + 1;

        var allowedusers = 3;
        if (rowCount > allowedusers) {
            getLabels_jsonlayout([{
                id: 'alloweduserlimit',
                key: 'alloweduserlimit'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', Object.values(text)[0], 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            });
            return;
        }

        // HTML for the new row - use array format for names
        const newRowHtml = `
            <div class="d-flex mt-2 work-row-${action}" id="row${rowCount}">
                <input type="hidden" name="auditee_report[${rowCount}]" value="UserId_${rowCount}">

                <div class="col-md-1">
                    <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                </div>

                <div class="col-md-3 ms-2">
                    <input type="text" class="form-control name dynamic_keypress" data-placeholder-key="username" name="reportdesignation[]" value="" placeholder="Enter designation">
                </div>

                <div class="col-md-3 ms-2">
                    <input type="email" class="form-control dynamic_keypress" data-placeholder-key="username" name="reportemail[]" value="" placeholder="Enter Email">
                </div>

                <div class="col-md-3 ms-2">
                    <input type="text" class="form-control only_numbers dynamic_keypress" maxlength="10" data-placeholder-key="username" name="reportmobile[]" value="" placeholder="Enter Mobile">
                </div>

                <div class="col-md-3">
                    <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'${action}')">
                        <i class="ti ti-circle-plus"></i>
                    </button>
                    ${rowCount != 1 ?
                        `<button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removedeptRow(this,'${action}')">
                            <i class="ti ti-circle-minus"></i>
                        </button>` : ''}
                </div>
            </div>
        `;

        if (action == 'insert') {
            $('#addrowUsers').append(newRowHtml);
        } else {
            $('#EditrowUsers').append(newRowHtml);
        }

        // Apply validation to the new fields using array names
        applyValidationToNewFields('reportdesignation[]', 'Enter Designation');
        applyValidationToNewFields('reportemail[]', 'Enter Email');
        applyValidationToNewFields('reportmobile[]', 'Enter Mobile');
    }

     $(document).on("keypress", ".only_numbers", function(e) {
     const char = String.fromCharCode(e.which);

     // Allow only digits
            if (!/^\d$/.test(char)) {
        e.preventDefault();
            }
        });

        // Restrict paste to digits only
        $(document).on("paste", ".only_numbers", function(e) {
    const pasted = (e.originalEvent || e).clipboardData.getData('text').trim();

    // Allow only whole numbers
    if (!/^\d+$/.test(pasted)) {
        e.preventDefault();
    }
        });


    function populateDesignationDropdown(selectId, selectedValue = "") {

        let dropdown = document.getElementById(selectId);
        if (!dropdown) return;

        dropdown.innerHTML = `<option value="">---Select Designation---</option>`;
        //'log' + selectedValue)
        if (window.designationdata) {
            window.designationdata.forEach(designation => {

                let option = document.createElement("option");
                option.value = designation.desigcode;
                option.textContent = designation.desigelname;
                if (designation.desigcode == selectedValue.trim()) {

                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
        }
        // Re-initialize Select2 (if used)
        //   $(`#${selectId}`).select2();
    }




    // ✅ Add Row Function
    function addNewRow() {

        if (designationCount >= 4) {
            alert("Maximum 4 designations allowed!");
            return;
        }

        designationCount++;
        let memberDesignationDiv = document.getElementById("member_designation");

        // Remove previous Add button, replace with Delete button
        let prevAddBtn = memberDesignationDiv.querySelector(".addRowBtn");
        if (prevAddBtn) {
            prevAddBtn.outerHTML = `<button type="button" class="btn btn-danger fw-medium ms-2 mt-4" onclick="removeRow(this)">
                                <i class="ti ti-trash"></i>
                            </button>`;
        }

        // Create new designation row
        let newRow = document.createElement("div");
        newRow.className = "col-md-3 mb-1 mt-2 designation-item d-flex align-items-center";
        let newSelectId = `member_desig_${designationCount}`;

        newRow.innerHTML = `
                 <div class="w-100">
                     <label class="form-label lang required">Team Member Designation ${designationCount}</label>
                     <select class="form-select mr-sm-2 lang-dropdown select2" id="${newSelectId}" name="${newSelectId}">
                         <option value="">---Select Designation---</option>
                     </select>
                 </div>
                 <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn mt-4" onclick="addNewRow()">
                     <i class="ti ti-circle-plus"></i>
                 </button>
             `;

        // Append new row inside member_designation div
        memberDesignationDiv.appendChild(newRow);
        $(`#${newSelectId}`).select2();
        // ✅ Populate the new dropdown
        populateDesignationDropdown(newSelectId, '');
    }
    // Function to remove a row
    function removeRow(button) {
        let row = button.parentElement;
        row.remove(); // Remove row
        designationCount--;

        // Get remaining rows
        let rows = document.querySelectorAll("#member_designation .designation-item");

        rows.forEach((row, index) => {
            let label = row.querySelector("label");
            let select = row.querySelector("select");

            let selectedValue = select.value; // Store selected value before updating ID

            let newIndex = index + 1;
            label.innerText = `Team Member Designation ${newIndex}`;
            select.id = `member_desig_${newIndex}`;
            select.name = `member_desig_${newIndex}`;

            select.value = selectedValue; // Restore the selected value
        });

        // Remove all Add buttons and ensure only last row has one
        document.querySelectorAll(".addRowBtn").forEach(btn => btn.remove());

        if (rows.length > 0) {
            let lastRow = rows[rows.length - 1]; // Last row
            let addButton = document.createElement("button");
            addButton.type = "button";
            addButton.className = "btn btn-success fw-medium ms-2 addRowBtn mt-4";
            addButton.innerHTML = `<i class="ti ti-circle-plus"></i>`;
            addButton.onclick = addNewRow;

            lastRow.appendChild(addButton);
        }
    }



    let dataFromServer;
    let auditeedept_reportdata;


    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    var sessionregioncode = ' <?php echo $regioncode; ?>';
    var sessiondistcode = ' <?php echo $distcode; ?>';




    function fetchAlldata() {

	$("#datatable_loader").show();
	$('#tableshow').hide();
	let filter_auditmode = $('#filter_auditmode').val();

        let lang = $('html').attr('lang') || 'en';
        let deptcode = $('#deptcode1').val();
        let quarter = $('#quarter').val();
        $.ajax({
            url: '/masters/fetch_mapInstDet', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
               quarter: quarter,
		form: 'fetch',
		filter_auditmode:filter_auditmode,

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // alert('adds');
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = response.data;
                    // auditeedept_reportdata = response.audit_reportdata;
                    // alert(dataFromServer);
                    renderTable(lang);
                } else {
		    $("#datatable_loader").hide();
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
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

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here

        var id = $(this).attr('id'); //Getting id of user clicked edit button.

        if (id) {
            // reset_form();
            fetchsinglemap_instdata(id);

        }
    });

    function fetchsinglemap_instdata(instid) {
	let deptcode = $('#deptcode1').val();
  	let quarter = $('#quarter').val();
        $.ajax({
            url: '/masters/fetch_mapInstDet', // Your API route to get user details
            method: 'POST',
            data: {
		deptcode: deptcode,
                quarter: quarter,
                instid: instid,
		form: 'edit'
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {
                    $('#display_error').hide();
                    reset_form();
                    // change_button_as_update('map_allocation_objection', 'action', 'buttonaction',
                    //     'display_error', '', '', 'update');

                    changeButtonAction('mapinst_form', 'action', 'buttonaction',
                        'reset_button',
                        'display_error', @json($updatebtn),
                        @json($clearbtn),
                        @json($update));

                    const detail = response.data[0];

                    var auditreportdata = response.audit_reportdata;


                    // designationArray = response.desigdata || [];
                    designationdata = response.desigdata;
                    // designationdata = designationArray.map(item => item.desigcode);
                    // //designationdata);
if (detail.inst_kms) {
                    let inst_kms = parseFloat(detail.inst_kms);
                    if (!isNaN(inst_kms)) {
                        $('#inst_kms').val(inst_kms.toFixed(2));
                    } else {
                        // $('#inst_kms').val('0.00');
                        $('#inst_kms').val('');
                    }
                } else {
                    // $('#inst_kms').val('0.00');
                    $('#inst_kms').val('');
                }

                if (detail.inst_priority_kms) {
                    $('#inst_priority_kms').val(detail.inst_priority_kms);
                } else {
                    $('#inst_priority_kms').val('');
                }

               //if(detail.checklist_dist == 'Y')
   // {
   // $('#inst_kms').prop('disabled', true);
  //  }
const invalid_kms = ['0.0','0','0.00',null,'null',''];

if (detail.checklist_dist === 'Y' && !invalid_kms.includes(detail.inst_kms) && detail.inst_kms > 1) {
    $('#inst_kms').prop('disabled', true);
}



                    // alert(detail.nodalperson_ename);
                    $('#instid').val(detail.encrypted_instid);
		    $('#apms_inst').val(detail.apms_inst);
                    $('#auditeeuserid').val(detail.encrypted_auditeeuserid);
                    $('#insttname').val(detail.insttname);
                    $('#instename').val(detail.instename);
                    $('#risktype').val(detail.risktype);
                    $('#cat_code').val(detail.cat_code);
                    $('#categorization').val(detail.categorization);
                    //$('#auditmode').val(detail.audit_mode);
                    $('#team_size').val(detail.teamsize);
                    $('#parent_instename').val(detail.parentinstename);
                    $('#nodalperson_ename').val(detail.nodalperson_ename);
                    $('#nodalperson_tname').val(detail.nodalperson_tname);
                    $('#email').val(detail.email);
                    $('#mobile').val(detail.mobile);
                    $('#nodalperson_desigcode').val(detail.nodalperson_desigcode);
                    $('#rankorder').val(detail.rankorder);
                    $('#mandays').val(detail.mandays);
                    $('#rankorder').val(detail.rankorder);
                    $('#rankorder').val(detail.rankorder);
                    $('#auditee_dept').val(detail.auditeedeptcode);
                    $('#itms_no').val(detail.erpno);
                    $('#categorization').val(detail.categorization);
                    $('#max_leave').val(detail.leavedays);
                    $('#turnover').val(detail.turnover);
                    $('#auditee_ofaddr').val(detail.auditeeofficeaddress);
                    // $('#audit_mode').val(detail.auditmode);
                    $('#inst_type').val(detail.insttype);

                    onchange_insttype(detail.insttype);


                    let selectedQuarters = [];
                    ['Q1', 'Q2', 'Q3', 'Q4'].forEach(q => {
                        const checkbox = document.getElementById(q);
                        if (checkbox) {
                            checkbox.checked = detail[q] === 'Y';
                            if (checkbox.checked) {
                                selectedQuarters.push(q);
                            }

                        }
                    });

                    $('#categorization').select2('destroy');
                    $('#categorization').val(detail.categorization);
                    $('#categorization').select2();

                    $('#audit_mode').select2('destroy');
                    $('#audit_mode').val(detail.auditmode);
                    $('#audit_mode').select2();

                    //  onchange_applicablefor(detail.auditmode,'edit')
                   $('#hubtypeid').val(detail.hubtype);

                       onchange_mode(detail.auditmode, 'edit', selectedQuarters, detail.insttype,detail.deptcode);
		                onchange_insttype(detail.insttype);
		              //  onchange_hubtype(detail.hubtype)
	onchange_hubtype(detail.catcode,detail.hubtype)

$('#deptcode_hidden').val(detail.deptcode);

                    $('#catcode_hidden').val(detail.catcode);

                    $('#subcatid_hidden').val(detail.auditeeins_subcategoryid);

                    $('#deptcode').select2('destroy');
if (detail.deptcode == '03' && sessionroletype == dgaroletypecode) {
$('#deptcode').attr('disabled',false);
    const allowedCodes = ['02', '03'];

    $('#deptcode option').each(function() {
        if (!allowedCodes.includes($(this).val())) {
            $(this).remove();
        }
    });

}
else{
$('#deptcode').attr('disabled',true);
}

                    $('#deptcode').val(detail.deptcode);
                    $('#deptcode').select2();

                    $('#distcode').select2('destroy');
                    $('#distcode').val(detail.distcode);
                    $('#distcode').select2();

                    $('#revenuedistcode').select2('destroy');
                    $('#revenuedistcode').val(detail.revenuedistcode);
                    $('#revenuedistcode').select2();


                    // $('input[name="template_audit"][value="' + detail.templateaudit + '"]')
                    //     .prop('checked', true);
                    $('input[name="cary_fwd"][value="' + detail.carryforward + '"]')
                        .prop('checked', true);
                    $('input[name="cons_report"][value="' + detail.consreport + '"]')
                        .prop('checked', true);
                    $('input[name="assemble_report"][value="' + detail.assemblereport + '"]')
                        .prop('checked', true);
                    // $('#head_isntdesigid').val(detail.teamhead_mapid);

                    $('input[name="fees"][value="' + detail.fees + '"]')
                        .prop('checked', true);
                    $('input[name="annadhanam_only"][value="' + detail.annadhanam_only + '"]')
                        .prop('checked', true);
                    $('input[name="auditcertificate"][value="' + detail.auditcertificate + '"]')
                        .prop('checked', true);
 if (detail.auditmode === 'T') {
                    $('#inst_kms_div').hide();
                   // $('#inst_priority_kms_div').hide();
                } else {
                    $('#inst_kms_div').show();
                    $('#inst_priority_kms_div').show();
                }

                    // var memberdesig = detail.teammembers_desigcode;
                    // var memberdesigArray = memberdesig.split(",");
                    var instid = detail.teammembers_mapid;
                    var instdesigidArray = '';

                    onchange_deptcode(detail.catcode, detail.regioncode, detail.teamhead_designation, '',
                        instdesigidArray, designationdata, detail
                        .auditeedeptcode, detail.typeofauditcode);

                    onchange_catcode(detail.catcode, detail.if_subcategory, detail
                        .auditeeins_subcategoryid);

                    // onchange_region(detail.regioncode, detail.distcode, detail.auditoffice)
                    // onchange_distcode(detail.deptcode, detail.regioncode, detail.distcode, detail
                    //     .auditoffice)

                    // if (detail.auditoffice != '' || detail.auditoffice != null) {
                    //     onchange_auditoffice(detail.deptcode, detail.regioncode, detail.distcode, detail
                    //         .auditoffice, detail
                    //         .auditaduserid, detail.auditofficedesignation)
                    // }
                    async function executeSequentially(detail) {
                        await onchange_region(detail.regioncode, detail.distcode, detail.auditoffice);
                        await onchange_distcode(detail.deptcode, detail.regioncode, detail.distcode,
                            detail.auditoffice, detail.parentinstid,detail.hubdesigcode,detail.zonecode);

                        if (detail.auditoffice !== '' && detail.auditoffice !== null) {
                            await onchange_auditoffice(detail.deptcode, detail.regioncode, detail
                                .distcode, detail.auditoffice, detail.auditofficedesignation);
                            await onchange_auditofficedesig(detail.auditoffice, detail
                                .auditofficedesignation, detail.auditaduserid, detail.regioncode, detail.distcode)
                        }
                    }
                    executeSequentially(detail);


                    appendEditRow(auditreportdata);
                    // populateTeamMembersDropdown(memberdesigArray);




                } else {
                    alert('Institute Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

  function applyValidationToNewFields(inputName, message) {
        // Wait a bit to ensure DOM is updated
        setTimeout(function() {
            var $input = $("[name='" + inputName + "']");

            if ($input.length && $input.is(':visible')) {
                var validator = $("#mapinst_form").data("validator");

                // Initialize validator if not exists
                if (!validator) {
                    $("#mapinst_form").validate({
                        errorPlacement: function(error, element) {
                            if (element.hasClass('select2')) {
                                error.insertAfter(element.next('.select2-container'));
                            } else if (element.hasClass('datepicker')) {
                                error.insertAfter(element.closest('.input-group'));
                            } else {
                                error.insertAfter(element);
                            }
                        }
                    });
                    validator = $("#mapinst_form").data("validator");
                }

                // Check if element has rules method
                if ($input.rules && typeof $input.rules === 'function') {
                    try {
                        // Remove existing rules first
                        $input.rules('remove');

                        // Add new rules
                        $input.rules('add', {
                            required: true,
                            messages: {
                                required: message
                            }
                        });

                        // Validate the element
                        validator.element($input);

                        // Remove existing change handlers to avoid duplicates
                        $input.off('change.validation');

                        // Add change handler
                        $input.on('change.validation', function() {
                            $(this).valid();
                        });
                    } catch(e) {
                        console.warn('Could not add validation rules for:', inputName, e);
                    }
                }
            } else {
                // If element not found immediately, try again after short delay
                setTimeout(function() {
                    var $retryInput = $("[name='" + inputName + "']");
                    if ($retryInput.length) {
                        applyValidationToNewFields(inputName, message);
                    }
                }, 100);
            }
        }, 50);
    }


    function appendEditRow(auditreportdata) {
        $('.work-row-insert').hide();
        $('#EditrowUsers').html('');

        $('#row0').removeClass('d-flex');
        var tablehead = ` <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-3 ms-2"></div>

                                                        <div class="col-md-3ms-2"></div>



                                                        <div class="col-md-3"></div>
                                                    </div>

                                                    <div class="d-flex mt-2 work-row" id="row0">
                                                        <div class="col-md-1">
                                                            <label class="form-label lang" key="s_no" for="validationDefaultUsername">S.No</label>
                                                        </div>



                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="designation" for="validationDefaultUsername">Designation</label>
                                                        </div>

                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="email="validationDefaultUsername">Email</label>
                                                        </div>


                                                        <div class="col-md-3  ms-2">
                                                            <label class="form-label lang" key="mobile"validationDefaultUsername">Mobile Number</label>
                                                        </div>
                                                    </div>
                                                `;
        $('#EditrowUsers').append(tablehead);


        var rowCount = 0; // Initialize row count


           if (auditreportdata.length > 0) {
            $.each(auditreportdata, function(index, item) {
                rowCount++; // Increment rowCount for each iteration


                // Create HTML template for a new row
                var appendHTML = `
                <div class="d-flex mt-2 work-row-edit" id="row${rowCount}">
                    <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name" name="reportdesignation[${rowCount}]" value="${item.designation || ''}" placeholder="Enter Name">
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="email" class="form-control " name="reportemail[${rowCount}]" id="reportemail${rowCount}" value="${item.email || ''}" placeholder="Enter Email">
                    </div>
                      <div class="col-md-3 ms-2">
                        <input type="text" class="form-control only_numbers" maxlength = "13" name="reportmobile[${rowCount}]" id="reportmobile${rowCount}" value="${item.mobilenumber || ''}" placeholder="Enter Email">
                    </div>



                    <div class="col-md-3 actionbtns">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                            <i class="ti ti-circle-plus"></i>
                        </button>
                         ${rowCount != 1 ? `
                                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removedeptRow(this,'edit')">
                                            <i class="ti ti-circle-minus"></i>
                                        </button>` : ``}
                    </div>
                </div>
            `;
                $(".only_numbers").on("keypress", function(event) {
                    if (event.charCode >= 48 && event.charCode <= 57)
                        return true; // let it happen, don't do anything
                    else return false;
                });


                $('#EditrowUsers').append(appendHTML);
            });
        } else {



            // Create HTML template for a new row
            var appendHTML = `
                <div class="d-flex mt-2 work-row-edit" id="row1">
                    <input type="hidden" name="officeuserid[1]" value="UserId_1">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="1" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name" name="reportdesignation[1]" value="" placeholder="Enter Name">
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="email" class="form-control " name="reportemail[1]" id="reportemail1}" value="" placeholder="Enter Email">
                    </div>
                      <div class="col-md-3 ms-2">
                        <input type="text" class="form-control only_numbers" maxlength = "13" name="reportmobile[1]" id="reportmobile1" value="" placeholder="Enter Mobile Number">
                    </div>



                    <div class="col-md-3 actionbtns">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                            <i class="ti ti-circle-plus"></i>
                        </button>

                    </div>
                </div>
            `;
            $(".only_numbers").on("keypress", function(event) {
                if (event.charCode >= 48 && event.charCode <= 57)
                    return true; // let it happen, don't do anything
                else return false;
            });


            $('#EditrowUsers').append(appendHTML);

        }
    }

    function updateTableLanguage(lang) {
        if ($.fn.DataTable.isDataTable('#mapinst_table')) {
            $('#mapinst_table').DataTable().clear().destroy();
        }
        renderTable(lang);
    }

    function renderTable(lang) {


        let tableData = Object.values(dataFromServer).map((group, index) => {
            let departmentColumn = lang === 'ta' ? group.depttsname : group.deptesname;
            let categoryColumn = lang === 'ta' ? group.cattname : group.catename;
            let subcategoryColumn = lang === 'ta' ? group.subcattname : group.subcatename;
	        //let isdisable = group.q1spillover === 'Y' ? true : false;
		//let isdisable =  false;
            var sessionrole = '<?php echo $sessionroletypecode; ?>'
            var dgarole = '<?php echo $dga_roletypecode; ?>'

            let isdisable =false;

           // if(sessionrole != dgarole)
           //  isdisable = group.Q1 === 'Y'|| group.Q2 === 'Y' ? true : false;
            let categoryDetails =
                `<b>${lang === 'ta' ? 'Category Name : ' : 'Category Name :'}</b> ${lang=='ta'?group.cattname:group.catename} <br>
            <b>${lang === 'ta' ? 'Sub Category Name : ' : 'Sub Category Name : '}</b> ${lang=='ta'?group.subcattname:group.subcatename} `;
            let regionColumn = lang === 'ta' ? group.regiontname : group.regionename;
            let districtColumn = lang === 'ta' ? group.disttname : group.distename;

            // ✅ Institution Details as a String (Only for Desktop View)
            let instituteDetail =
                `<b>${lang === 'ta' ? 'நிறுவனத்தின் ஆங்கில பெயர்' : 'Institution English Name'}</b>: ${group.instename} <br>
                               <small><b>${lang === 'ta' ? 'நிறுவனத்தின் தமிழ் பெயர்' : 'Institution Tamil Name'}:</b> ${group.insttname}</small>`;

            // ✅ Nodal Person Details as a String (Only for Desktop View)
            let nodalPersonDetail =
                `<b>${lang === 'ta' ? 'நோடல் நபர் ஆங்கில பெயர்' : 'Nodal Person English Name'}:</b> ${group.nodalperson_ename} <br>
                                 <small><b>${lang === 'ta' ? 'நோடல் நபர் தமிழ் பெயர்' : 'Nodal Person Tamil Name'}:</b> ${group.nodalperson_tname}</small><br>
                                 <small><b>${lang === 'ta' ? 'மின்னஞ்சல்' : 'Email'}:</b> ${group.email}</small><br>
                                 <small><b>${lang === 'ta' ? 'மொபைல் எண்' : 'Mobile Number'}:</b> ${group.mobile}</small><br>
                                 <small><b>${lang === 'ta' ? 'பதவி' : 'Designation'}:</b> ${group.nodalperson_desigcode}</small>`;

            let action = isdisable ? `<i class="ti ti-ban fs-4"></i>` : `<center><a class="btn editicon edit_btn" id="${group.encrypted_instid}" >
                        <i class="ti ti-edit fs-4"></i></a></center>`;
let inst_kms = group.inst_kms ? parseFloat(group.inst_kms).toFixed(2) : '0.00';
            let priorityText = group.inst_priority_kms == '01' ? 'P1' : (group.inst_priority_kms == '02' ? 'P2' : '-');
            return {
                serialNo: index + 1,
                department: departmentColumn,
                instename: group.instename, // ✅ Institution English Name
                insttname: group.insttname, // ✅ Institution Tamil Name
                institute: instituteDetail, // 🔹 Full String for Desktop View
                category: categoryColumn,
                subcategory: subcategoryColumn,
                categorydet: categoryDetails,
                region: regionColumn,
                district: districtColumn,
                rankorder: group.rankorder,
                nodelename: group.nodalperson_ename, // ✅ Nodal Person English Name
                nodeltname: group.nodalperson_tname, // ✅ Nodal Person Tamil Name
                email: group.email, // ✅ Email
                mobile: group.mobile, // ✅ Mobile
                designation: group.nodalperson_desigcode, // ✅ Designation
                nodalPerson: nodalPersonDetail, // 🔹 Full String for Desktop View
inst_kms: inst_kms,
                inst_priority_kms: priorityText,
                action: action
            };
        });

        if ($.fn.DataTable.isDataTable('#mapinst_table')) {
            $('#mapinst_table').DataTable().clear().destroy();
        }

        $('#mapinst_table').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: tableData,
            order: [
                [0, 'asc']
            ],
            columns: [{
                    data: "serialNo",
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        // ✅ Mobile View: Pass Only Institution & Nodal Person Sub-Columns Separately
                        let rowDataForMobile = {
                            instename: row.instename, // ✅ Institution English Name
                            insttname: row.insttname, // ✅ Institution Tamil Name
                            category: row.category,
                            subcategory: row.subcategory,
                            region: row.region,
                            district: row.district,
                            rankorder: rankorder,
                            nodelename: row.nodelename, // ✅ Nodal Person English Name
                            nodeltname: row.nodeltname, // ✅ Nodal Person Tamil Name
                            Email: row.email, // ✅ Email
                            mobile: row.mobile, // ✅ Mobile Number
                            designation: row.designation // ✅ Designation
                        };

                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(rowDataForMobile)}'>▶</button>
                                ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: "department",
                    title: columnLabels?.["department"]?.[lang],
                    className: 'text-start text-wrap'
                },
                {
                    data: "institute",
                    title: columnLabels?.["institute"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "categorydet",
                    title: columnLabels?.["categorydet"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "region",
                    title: columnLabels?.["region"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "district",
                    title: columnLabels?.["district"]?.[lang],
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
{
                    data: "inst_kms",
                    title: columnLabels?.["inst_kms"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "inst_priority_kms",
                    title: columnLabels?.["inst_priority_kms"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "rankorder",
                    title: columnLabels?.["rankorder"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "nodalPerson",
                    title: columnLabels?.["nodalPerson"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "action",
                    title: columnLabels?.["actions"]?.[lang],
                    className: "text-center text-wrap"
                }
            ],
            columnDefs: [{
                targets: "_all",
                className: ""
            }],
            initComplete: function() {
		$("#datatable_loader").hide();
                $("#mapinst_table").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });

        // ✅ Ensure Mobile View Works with Institution & Nodal Person Sub-Columns
        const mobileColumns = ["instename", "insttname", "category", "subcategory", "region", "district", "nodelename",
            "nodeltname", "Email", "mobile", "designation"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(lang, "mapinst_table");
    }


    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();
        let titleKey = `${tableId}_title`;
        let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
        let safeSheetName = translatedTitle.substring(0, 31);
        let dtText = dataTables[language]["datatable"] || dataTables["en"]["datatable"];

        const riskTypeMap = {
            en: {
                H: "High",
                M: "Medium",
                L: "Low"
            },
            ta: {
                H: "????",
                M: "?????",
                L: "??????"
            }
        };
        const yesNoMap = {
            en: {
                Y: "Yes",
                N: "No"
            },
            ta: {
                Y: "???",
                N: "?????"
            }
        };

        const insttype = {
            en: {
                H: "Hub",
                S: "Spoke"
            },
            ta: {
                H: "ஹப்",
                S: "ஸ்போக்"
            }
        };

        const columnMap = {
            departmentColumn: language === 'ta' ? 'depttsname' : 'deptesname',
            categoryColumn: language === 'ta' ? 'cattname' : 'catename',
            regionColumn: language === 'ta' ? 'regiontname' : 'regionename',
            districtColumn: language === 'ta' ? 'disttname' : 'distename',
            auditeedep: language === 'ta' ? 'auditeedepttname' : 'auditeedeptename',
            typeofaudit: language === 'ta' ? 'typeofaudittname' : 'typeofauditename',
            auditoffice: language === 'ta' ? 'auditinsttname' : 'auditinst_ename',
            aao: language === 'ta' ? 'usertamilname' : 'username',
            designation: 'designation',
            auditquarter: 'auditquarter',
            erpno: 'erpno',
            mandays: 'mandays',
            nodalPersonEnglish: 'nodalperson_ename',
            nodelPersonTamil: 'nodalperson_tname',
            instenameColumn: 'instename',
            insttnameColumn: 'insttname',
            emailColumn: 'email',
            mobileColumn: 'mobile',
            revenuedist: language === 'ta' ? 'revenuedisttname' : 'revenuedistename',
            subcategory: language === 'ta' ? 'subcattname' : 'subcatename',
            rankorder: 'rankorder',
            risktype: 'risktype',
            designationColumn: 'nodalperson_desigcode',
            auditeeofficeaddress: 'auditeeofficeaddress',
            mobilenumber: 'mobile', // Changed to match actual data field
            // teammember: language === 'ta' ? 'teammembers_desigelname' : "teammembers_desigelname",
            // teamhead: language === 'ta' ? 'teamhead_desigelname' : "teamhead_desigelname",
            auditmode: language === 'ta' ? 'auditmodetname' : 'auditmode',
            teamsize: 'teamsize',
            fees: 'fees',
            categorization: 'categorization',
            maxleave: 'leavedays', // Fixed: data has 'leavedays', not 'maxleave'
            carryforward: 'carryforward',
            templateaudit: 'templateaudit',
            consreport: 'consreport',
            assemblereport: 'assemblereport',
            turnover: 'turnover',
            inst_kms: 'inst_kms',
            inst_priority_kms: 'inst_priority_kms'
        };

        let headers = [{
                header: dtText["department"] || "Department",
                key: columnMap.departmentColumn
            },
            {
                header: dtText["region"] || "Region",
                key: columnMap.regionColumn
            },
            {
                header: dtText["district"] || "District",
                key: columnMap.districtColumn
            },
            {
                header: dtText["revenuedist"] || "Revenue District",
                key: columnMap.revenuedist
            },
            {
                header: dtText["instename"] || "Institution English Name",
                key: columnMap.instenameColumn
            },
            {
                header: dtText["institution_tam_name"] || "Institution Tamil Name",
                key: columnMap.insttnameColumn
            },
            {
                header: dtText["category"] || "Category",
                key: columnMap.categoryColumn
            },
            {
                header: dtText["subcat"] || "Sub Category",
                key: columnMap.subcategory
            },
            {
                header: dtText["mandays"] || "Total Mandays",
                key: columnMap.mandays
            },
            {
                header: dtText["auditquarter"] || "Audit Quarter",
                key: columnMap.auditquarter
            },
            {
                header: dtText["rankorder"] || "Rank Order",
                key: columnMap.rankorder
            },
            {
                header: dtText["typeofaudit"] || "Type of Audit",
                key: columnMap.typeofaudit
            },
            {
                header: dtText["risktype"] || "Risk Type",
                key: columnMap.risktype
            },
            {
                header: dtText["auditeeofficeaddress"] || "Auditee Office Address",
                key: columnMap.auditeeofficeaddress
            },
            {
                header: dtText["nodalperson_ename"] || "Nodal Person English Name",
                key: columnMap.nodalPersonEnglish
            },
            {
                header: dtText["nodalPerson"] || "Nodal Person Tamil Name",
                key: columnMap.nodelPersonTamil
            },
            {
                header: dtText["designationColumn"] || "Nodal Person Designation",
                key: columnMap.designationColumn
            },
            {
                header: dtText["Email"] || "Email",
                key: columnMap.emailColumn
            },
            {
                header: dtText["mobilenumber"] || "Mobile Number",
                key: columnMap.mobileColumn
            },
            {
                header: dtText["auditoffice"] || "Audit Office Name",
                key: columnMap.auditoffice
            },

            {
                header: dtText["aao"] || "AAO Name",
                key: columnMap.aao
            },
            {
                header: dtText["erpno"] || "ITMS/Reg NO/Reference No",
                key: columnMap.erpno
            },
            // {
            //     header: dtText["teammember"] || "Team Members",
            //     key: columnMap.teammember
            // },
            // {
            //     header: dtText["teamhead"] || "Team Head",
            //     key: columnMap.teamhead
            // },
            {
                header: dtText["auditmode"] || "Audit Mode",
                key: columnMap.auditmode
            },
            {
                header: dtText["teamsize"] || "Team Size",
                key: columnMap.teamsize
            },
            {
                header: dtText["fees"] || "Fees",
                key: columnMap.fees
            },
            {
                header: dtText["categorization"] || "Categorization based Income Turnover",
                key: columnMap.categorization
            },
            {
                header: dtText["maxleave"] || "Maximum Leavedays",
                key: columnMap.maxleave
            },
            {
                header: dtText["carryforward"] || "Carry Forward",
                key: columnMap.carryforward
            },
            {
                header: dtText["templateaudit"] || "Template Audit",
                key: columnMap.templateaudit
            },
            {
                header: dtText["consreport"] || "Consolidated Report",
                key: columnMap.consreport
            },
            {
                header: dtText["assemblereport"] || "Assembly Report",
                key: columnMap.assemblereport
            },
            {
                header: dtText["turnover"] || "Turn Over",
                key: columnMap.turnover
            },
            {
                header: dtText["auditeedep"] || "Auditee Department",
                key: columnMap.auditeedep
            },
            {
                header: dtText["inst_kms"] || "Institution KMs",
                key: columnMap.inst_kms
            },
            {
                header: dtText["inst_priority_kms"] || "Priority",
                key: columnMap.inst_priority_kms
            }
        ];

        // Add dynamic headers for up to 10 matched contacts
        // Check if auditeedept_reportdata exists before processing
        if (typeof auditeedept_reportdata !== 'undefined') {
            for (let i = 1; i <= 10; i++) {
                headers.push({
                    header: `Designation ${i}`,
                    key: `designation${i}`
                });
                headers.push({
                    header: `Email ${i}`,
                    key: `email${i}`
                });
                headers.push({
                    header: `Mobile ${i}`,
                    key: `mobile${i}`
                });
            }
        }

        let excelData = dataFromServer.map(row => {
            let instid = row.instid;
            let dynamicData = {};

            // Process dynamic contact data if available
            if (typeof auditeedept_reportdata !== 'undefined') {
                let matchingRows = auditeedept_reportdata.filter(d => d.instid === instid);

                matchingRows.slice(0, 10).forEach((contact, index) => {
                    let idx = index + 1;
                    dynamicData[`designation${idx}`] = contact.designation || "-";
                    dynamicData[`email${idx}`] = contact.email || "-";
                    dynamicData[`mobile${idx}`] = contact.mobile || "-";
                });
            }

            // For auditmode, check both direct value and map it
            let auditModeValue = "-";
            if (row[columnMap.auditmode]) {
                auditModeValue = row[columnMap.auditmode] === "N" ?
                    (language === 'ta' ? 'ஓய்வு' : 'Normal') :
                    (language === 'ta' ? 'ஒழுக்கம்' : 'Special');
            }

            return {
                [columnMap.departmentColumn]: row[columnMap.departmentColumn] || "-",
                [columnMap.regionColumn]: row[columnMap.regionColumn] || "-",
                [columnMap.districtColumn]: row[columnMap.districtColumn] || "-",
                [columnMap.revenuedist]: row[columnMap.revenuedist] || "-",
                [columnMap.instenameColumn]: row[columnMap.instenameColumn] || "-",
                [columnMap.insttnameColumn]: row[columnMap.insttnameColumn] || "-",
                [columnMap.categoryColumn]: row[columnMap.categoryColumn] || "-",
                [columnMap.subcategory]: row[columnMap.subcategory] || "-",
                [columnMap.mandays]: row[columnMap.mandays] || "-",
                [columnMap.auditquarter]: row[columnMap.auditquarter] || "-",
                [columnMap.rankorder]: row[columnMap.rankorder] || "-",
                [columnMap.typeofaudit]: row[columnMap.typeofaudit] || "-",
                [columnMap.risktype]: riskTypeMap[language]?.[row[columnMap.risktype]] || row[columnMap.risktype] || "-",
                [columnMap.auditeeofficeaddress]: row[columnMap.auditeeofficeaddress] || "-",
                [columnMap.nodalPersonEnglish]: row[columnMap.nodalPersonEnglish] || "-",
                [columnMap.nodelPersonTamil]: row[columnMap.nodelPersonTamil] || "-",
                [columnMap.designationColumn]: row[columnMap.designationColumn] || "-",
                [columnMap.emailColumn]: row[columnMap.emailColumn] || "-",
                [columnMap.mobileColumn]: row[columnMap.mobileColumn] || "-",
                [columnMap.auditoffice]: row[columnMap.auditoffice] || "-",
                [columnMap.aao]: row[columnMap.aao] || "-",
                [columnMap.erpno]: row[columnMap.erpno] || "-",
                // [columnMap.teammember]: row[columnMap.teammember] || "-",
                // [columnMap.teamhead]: row[columnMap.teamhead] || "-",
                [columnMap.auditmode]: auditModeValue,
                [columnMap.teamsize]: row[columnMap.teamsize] || "-",
                [columnMap.fees]: yesNoMap[language]?.[row[columnMap.fees]] || row[columnMap.fees] || "-",
                [columnMap.categorization]: row[columnMap.categorization] || "-", // Fixed: was 'categorisation'
                [columnMap.maxleave]: row[columnMap.maxleave] || "-",
                [columnMap.carryforward]: yesNoMap[language]?.[row[columnMap.carryforward]] || row[columnMap.carryforward] || "-",
                [columnMap.templateaudit]: yesNoMap[language]?.[row[columnMap.templateaudit]] || row[columnMap.templateaudit] || "-",
                [columnMap.consreport]: yesNoMap[language]?.[row[columnMap.consreport]] || row[columnMap.consreport] || "-",
                [columnMap.assemblereport]: yesNoMap[language]?.[row[columnMap.assemblereport]] || row[columnMap.assemblereport] || "-",
                [columnMap.turnover]: row[columnMap.turnover] || "-",
                [columnMap.auditeedep]: row[columnMap.auditeedep] || "-",
                [columnMap.inst_kms]: row[columnMap.inst_kms] ? parseFloat(row[columnMap.inst_kms]).toFixed(2) : "0.00",
                [columnMap.inst_priority_kms]: row[columnMap.inst_priority_kms] == '01' ? 'P1' : (row[columnMap.inst_priority_kms] == '02' ? 'P2' : '-'),
                ...dynamicData
            };
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A1"
        });
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A2"
        });

        // Auto-size columns
        const wscols = headers.map(() => ({ wch: 20 }));
        ws['!cols'] = wscols;

        XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
        XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
    }


    let dropdownCount = 1;
    let maxDropdowns;
    var lang;




    // Add custom email validation rule
    jQuery.validator.addMethod("customEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Please enter a valid email address");
    jQuery.validator.addMethod("customMobile", function(value, element) {
        return this.optional(element) || /^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number");
    jQuery.validator.addMethod("customDecimal", function(value, element) {
        // This regex allows up to 12 digits before the decimal, and up to 2 digits after it
        return this.optional(element) || /^\d{1,12}(\.\d{1,2})?$/.test(value);
    }, "Please enter a valid decimal number");

 jQuery.validator.addMethod("validateItmsRegex", function(value, element) {
        if (value.trim() === '') {
            return true;
        }

        var deptcode = $('#deptcode').val();
        var cat_code = $('#cat_code').val();
        var subcatid = $('#subcatid').val();

        if (deptcode == '01') {
            return /^TM0\d{5}$/.test(value);
        }

        var regexPattern = $('#itms_no').data('valid-regex');

        if (regexPattern) {
            try {
                var regex = new RegExp(regexPattern);
                return regex.test(value);
            } catch (e) {
                console.error('Invalid regex pattern:', regexPattern, e);
                return /^[A-Za-z0-9]{1,20}$/.test(value);
            }
        }

        return /^[A-Za-z0-9]{1,20}$/.test(value);
    }, function(params, element) {
        var deptcode = $('#deptcode').val();
        var value = $(element).val();

        if (deptcode == '01') {
            if (!value.startsWith('TM0')) {
                return 'Must start with "TM0"';
            }
            if (value.length !== 8) {
                return 'Must be exactly 8 characters (TM0 followed by 5 digits)';
            }
            var numericPart = value.substring(3);
            if (!/^\d{5}$/.test(numericPart)) {
                return 'Last 5 characters must be digits';
            }
            return 'Invalid format. Expected: TM0 followed by 5 digits (e.g., TM012345)';
        }

        return 'Please enter a valid ITMS/Reg No/Reference No according to the selected category';
    });


    $(document).on("keydown", "#inst_kms", function(e) {
        const val = $(this).val();
        const cursorPos = this.selectionStart;

        if (e.key === 'Backspace' || e.key === 'Delete') {
            if (this.selectionStart === 0 && this.selectionEnd === val.length) {
                $(this).val('');
                e.preventDefault();
                return;
            }

            if (e.key === 'Backspace' && cursorPos > 0) {
                const charBeforeCursor = val.charAt(cursorPos - 1);
                if (charBeforeCursor === '.') {
                    const newVal = val.substring(0, cursorPos - 2) + val.substring(cursorPos - 1);
                    $(this).val(newVal);

                    setTimeout(() => {
                        this.setSelectionRange(cursorPos - 2, cursorPos - 2);
                    }, 0);
                    e.preventDefault();
                    return;
                }
            }
            return true;
        }

        if ([37, 38, 39, 40, 9, 36, 35].includes(e.keyCode)) {
            return true;
        }

        if (val.length >= 6 && e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
            return false;
        }
    });

    $(document).on("keypress", "#inst_kms", function(e) {
        const char = String.fromCharCode(e.which);
        const val = $(this).val();
        const cursorPos = this.selectionStart;

        if (/\d/.test(char)) {
            const hasDecimal = val.includes('.');

            if (!hasDecimal) {
                const digitsBeforeCursor = val.substring(0, cursorPos).replace(/\D/g, '').length;

                if (digitsBeforeCursor >= 3) {
                    if (cursorPos >= 3) {
                        e.preventDefault();

                        const newVal = val.substring(0, 3) + '.' + char;
                        $(this).val(newVal);

                        setTimeout(() => {
                            this.setSelectionRange(5, 5);
                        }, 0);
                    }
                    return false;
                }
                return true;
            } else {
                const decimalIndex = val.indexOf('.');
                const digitsAfterDecimal = val.substring(decimalIndex + 1, cursorPos).replace(/\D/g, '').length;

                if (digitsAfterDecimal >= 2) {
                    e.preventDefault();
                    return false;
                }
                return true;
            }
        }

        if (char === '.' && !val.includes('.') && /\d/.test(val.charAt(cursorPos - 1))) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    $(document).on("input", "#inst_kms", function() {
        let val = $(this).val();
        const cursorPos = this.selectionStart;

        val = val.replace(/[^\d.]/g, '');

        const parts = val.split('.');
        if (parts.length > 2) {
            val = parts[0] + '.' + parts.slice(1).join('');
        }

        if (!val.includes('.')) {
            if (val.length === 3) {
                $(this).val(val + '.');

                setTimeout(() => {
                    this.setSelectionRange(4, 4);
                }, 0);
                return;
            }
        } else {
            const decimalParts = val.split('.');
            let wholePart = decimalParts[0];
            let decimalPart = decimalParts[1] || '';

            if (wholePart.length > 3) {
                wholePart = wholePart.substring(0, 3);
            }

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            val = wholePart + '.' + decimalPart;
        }

        $(this).val(val);
    });

    $(document).on("blur", "#inst_kms", function() {
        let val = $(this).val().trim();

        if (val === '' || val === '.') {
            $(this).val('0.00');
            return;
        }

        if (/^\d+$/.test(val)) {
            val = val.replace(/^0+/, '');
            if (val === '') val = '0';

            $(this).val(val + '.00');
            return;
        }

        if (val.includes('.')) {
            const parts = val.split('.');
            let wholePart = parts[0].replace(/\D/g, '');
            let decimalPart = parts[1] ? parts[1].replace(/\D/g, '') : '';

            wholePart = wholePart.replace(/^0+/, '');
            if (wholePart === '') wholePart = '0';

            decimalPart = decimalPart.padEnd(2, '0');

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            $(this).val(wholePart + '.' + decimalPart);
            return;
        }
    });

    $(document).on("focus", "#inst_kms", function() {
        setTimeout(() => {
            this.select();
        }, 100);
    });

    $(document).on("paste", "#inst_kms", function(e) {
        e.preventDefault();
        const pasted = (e.originalEvent || e).clipboardData.getData('text').trim();

        let cleaned = pasted.replace(/[^\d.]/g, '');

        const decimalIndex = cleaned.indexOf('.');
        if (decimalIndex !== -1) {
            cleaned = cleaned.substring(0, decimalIndex + 1) +
                cleaned.substring(decimalIndex + 1).replace(/\./g, '');
        }

        $(this).val(cleaned);

        $(this).trigger('input');
    });

    jQuery.validator.addMethod("custominstDecimal", function(value, element) {
        if (value === '' || value === '0') return true;

        if (/^\d{1,3}(\.\d{0,2})?$/.test(value) || /^\d{1,3}$/.test(value)) {
            const num = parseFloat(value);
            return !isNaN(num) && num <= 999.99 && num >= 0;
        }

        return false;
    }, "Please enter a valid number (max 999.99)");

    // Load i18n JSON and apply validation
    jsonLoadedPromise.then(() => {
        const language = getLanguage('Y');

        // Make email lowercase as user types
        //$("#email").on("input", function() {
          //  this.value = this.value.toLowerCase();
        //});

        // Trigger validation on blur
        $("#email").on("blur", function() {
            $(this).valid();
        });

        // Apply validation to the form
        const validator = $("#mapinst_form").validate({
            rules: {
                subcatid: {
                    required: true
                },
                roletypecode: {
                    required: true
                },
 		ignore: ":hidden",
		inst_kms: {
                    required: {
                        depends: function(element) {
                            return $('#audit_mode').val() !== 'T';
                        }
                    },
                    custominstDecimal: {
                        depends: function(element) {
                            return $('#audit_mode').val() !== 'T';
                        }
                    }
                },
                inst_priority_kms: {
                    required: {
                        depends: function(element) {
                            return $('#audit_mode').val() !== 'T';
                        }
                    }
                },
                 itms_no: {
                    validateItmsRegex: true
                },
                deptcode: {
                    required: true
                },
                cat_code: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                revenuedistcode: {
                    required: true
                },
                instename: {
                    required: true
                },
                insttname: {
                    required: true
                },
                mandays: {
                    required: true
                },
                risktype: {
                    required: true
                },
                applicablefor: {
                    required: true
                },
                rankorder: {
                    required: true
                },
                 typeofaudit: {
                    required: true
                },
                nodalperson_ename: {
                    required: true
                },
                nodalperson_tname: {
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    customMobile: true
                },
                nodalperson_desigcode: {
                    required: true
                },
                head_desig: {
                    required: true
                },
                auditoffice: {
                    required: true
                },
                audit_office_desig: {
                    required: true
                },
                audit_ad: {
                    required: true
                },
                inst_type: {
                    required: true
                },
                pareninstid: {
                    required: true
                },
                categorization: {
                    required: true
                },
                audit_mode: {
                    required: true
                },
                team_size: {
                    required: true
                },
                max_leave: {
                    required: true
                },
                cary_fwd: {
                    required: true
                },
                cons_report: {
                    required: true
                },
                turnover: {
                    customDecimal: true,
                    required: true
                },

            },

            messages: {
                ...errorMessages[language], // Merge language-based messages
                email: {
                    required: "Email is required",
                    email: "Enter a valid email address",
                    customEmail: "Email format is incorrect"
                },
                mobile: {
                    required: "Mobile number is required",
                    minlength: "Enter at least 10 digits",
                    customMobile: "Please enter a valid 10-digit mobile number"
                },
inst_kms: {
                    required: "Institution kilometers is required",
                    custominstDecimal: "Please enter a valid number"
                },
                inst_priority_kms: {
                    required: "Priority is required"
                },
                turnover: {
                    required: "Turn over is required",
                    customDecimal: "Enter Valid turn over value",

                }
            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });


let validateItmsNoDebounceTimer = null;
    let regexPatternCache = {};
    let currentRegexPattern = null;

    function getRegexPatternWithCache(deptcode, cat_code, subcatid) {
        if (deptcode == '01') {
            const pattern = '^TM0\\d{5}$';
            return Promise.resolve(pattern);
        }

        if (!subcatid || subcatid.trim() === '') {
            const defaultPattern = '^[A-Za-z0-9]{1,20}$';
            return Promise.resolve(defaultPattern);
        }

        const cacheKey = `${deptcode}_${cat_code}_${subcatid}`;

        if (regexPatternCache[cacheKey]) {
            return Promise.resolve(regexPatternCache[cacheKey]);
        }

        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/masters/get_regex_pattern',
                type: 'POST',
                dataType: 'json',
                data: {
                    deptcode: deptcode,
                    cat_code: cat_code,
                    subcatid: subcatid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const pattern = response.regex_pattern || '^[A-Za-z0-9]{1,20}$';
                    regexPatternCache[cacheKey] = pattern;
                    resolve(pattern);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch regex pattern:', error);
                    const defaultPattern = '^[A-Za-z0-9]{1,20}$';
                    regexPatternCache[cacheKey] = defaultPattern;
                    resolve(defaultPattern);
                }
            });
        });
    }

    function validateItmsNoWithRegex(value) {
        const deptcode = $('#deptcode').val();
        const cat_code = $('#cat_code').val();
        const subcatid = $('#subcatid').val();
        const itms_no = value || $('#itms_no').val().trim();
        const itmsField = $('#itms_no');

        itmsField.removeClass('is-invalid is-valid is-validating');
        itmsField.siblings('.invalid-feedback').remove();

        if (!itms_no || !deptcode || !cat_code) {
            return;
        }

        if (validateItmsNoDebounceTimer) {
            clearTimeout(validateItmsNoDebounceTimer);
        }

        validateItmsNoDebounceTimer = setTimeout(async function() {
            if (deptcode == '01') {
                const pattern = '^TM0\\d{5}$';
                const regex = new RegExp(pattern);
                const isMatch = regex.test(itms_no);

                updateValidationUI(itmsField, isMatch, pattern, itms_no);
                return;
            }

            itmsField.addClass('is-validating');

            try {
                const pattern = await getRegexPatternWithCache(deptcode, cat_code, subcatid);
                const regex = new RegExp(pattern);
                const isMatch = regex.test(itms_no);

                updateValidationUI(itmsField, isMatch, pattern, itms_no);

                currentRegexPattern = pattern;
                itmsField.data('valid-regex', pattern);

            } catch (error) {
                console.error('Error validating ITMS:', error);
                itmsField.removeClass('is-validating');
                itmsField.addClass('is-invalid');
                itmsField.after('<div class="invalid-feedback">Error validating format. Please try again.</div>');
            }

        }, 600);
    }


    function updateValidationUI(field, isValid, pattern, value) {
        field.removeClass('is-validating');

        if (isValid) {
            field.removeClass('is-invalid');
            field.addClass('is-valid');
        } else {
            field.removeClass('is-valid');
            field.addClass('is-invalid');

            const errorMessage = getDynamicRegexErrorMessage(pattern, value);
            field.after('<div class="invalid-feedback">' + errorMessage + '</div>');
        }
    }

    async function fetchAndApplyRegexPattern() {
        const deptcode = $('#deptcode').val();
        const cat_code = $('#cat_code').val();
        const subcatid = $('#subcatid').val();

        if (!deptcode || !cat_code) {
            return;
        }

        const currentValue = $('#itms_no').val().trim();

        $('#itms_no').removeClass('is-invalid is-valid is-validating');
        $('#itms_no').siblings('.invalid-feedback').remove();

        let pattern;

        if (deptcode == '01') {
            pattern = '^TM0\\d{5}$';
            applyRegexInputRestrictions(pattern);

            if (currentValue) {
                validateItmsNoWithRegex(currentValue);
            }
            return;
        }

        try {
            if (subcatid) {
                pattern = await getRegexPatternWithCache(deptcode, cat_code, subcatid);
            } else {
                pattern = '^[A-Za-z0-9]{1,20}$';
            }

            applyRegexInputRestrictions(pattern);

            if (currentValue) {
                validateItmsNoWithRegex(currentValue);
            }
        } catch (error) {
            console.error('Error fetching regex pattern:', error);
            pattern = '^[A-Za-z0-9]{1,20}$';
            applyRegexInputRestrictions(pattern);
        }
    }

    $(window).on('beforeunload', function() {
        if (validateItmsNoDebounceTimer) {
            clearTimeout(validateItmsNoDebounceTimer);
        }
    });

    function applyRegexInputRestrictions(regexPattern) {
        var deptcode = $('#deptcode').val();
        var itmsField = $('#itms_no');

        itmsField.off('keypress.regex');
        itmsField.off('input.regex');
        itmsField.off('paste.regex');

        if (deptcode == '01') {
            itmsField.on('keypress.regex', function(e) {
                var char = String.fromCharCode(e.which);
                var value = $(this).val();
                var cursorPos = this.selectionStart;

                if (cursorPos < 3) {
                    if (e.keyCode === 8 || e.keyCode === 46) {
                        return true;
                    }
                    e.preventDefault();
                    return false;
                }

                if (!/\d/.test(char)) {
                    e.preventDefault();
                    return false;
                }

                if (value.length >= 8) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

            itmsField.on('input.regex', function() {
                var value = $(this).val();

                if (!value.startsWith('TM0')) {
                    $(this).val('TM0');
                } else {
                    var prefix = 'TM0';
                    var numericPart = value.substring(3).replace(/\D/g, '');

                    if (numericPart.length > 5) {
                        numericPart = numericPart.substring(0, 5);
                    }

                    $(this).val(prefix + numericPart);
                }
            });

        } else {
            var regex = new RegExp(regexPattern);
            var maxLength = getMaxLengthFromRegex(regexPattern);

            itmsField.on('keypress.regex', function(e) {
                var char = String.fromCharCode(e.which);
                var value = $(this).val();
                var cursorPos = this.selectionStart;

                if (!isCharAllowedForRegex(char, regexPattern)) {
                    e.preventDefault();
                    return false;
                }

                if (maxLength && value.length >= maxLength && cursorPos >= maxLength) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

            itmsField.on('input.regex', function() {
                var value = $(this).val();
                var cleanedValue = cleanValueForRegex(value, regexPattern);

                if (cleanedValue !== value) {
                    $(this).val(cleanedValue);
                }

                if (value.trim()) {
                    validateItmsNoWithRegex(value);
                }
            });
        }

        itmsField.on('paste.regex', function(e) {
            e.preventDefault();
            var pastedText = (e.originalEvent || e).clipboardData.getData('text').trim();
            var cleanedText = cleanValueForRegex(pastedText, regexPattern);

            var cursorPos = this.selectionStart;
            var currentValue = $(this).val();
            var newValue = currentValue.substring(0, cursorPos) + cleanedText + currentValue.substring(this.selectionEnd);

            $(this).val(newValue);

            validateItmsNoWithRegex(newValue);
        });
    }

    function cleanValueForRegex(value, regexPattern) {
        var deptcode = $('#deptcode').val();

        if (deptcode == '01') {
            var cleaned = value.toUpperCase();

            if (!cleaned.startsWith('TM0')) {
                var digits = cleaned.replace(/\D/g, '');
                cleaned = 'TM0' + digits.substring(0, 5);
            } else {
                var prefix = 'TM0';
                var numericPart = cleaned.substring(3).replace(/\D/g, '');
                cleaned = prefix + numericPart.substring(0, 5);
            }

            return cleaned;
        } else {
            if (regexPattern.includes('^\\d+$')) {
                return value.replace(/\D/g, '');
            } else if (regexPattern.includes('^[A-Za-z0-9]')) {
                return value.replace(/[^A-Za-z0-9]/g, '');
            } else if (regexPattern.includes('^[A-Z]+$')) {
                return value.replace(/[^A-Z]/g, '').toUpperCase();
            } else if (regexPattern.includes('^[a-z]+$')) {
                return value.replace(/[^a-z]/g, '').toLowerCase();
            }

            return value.replace(/[^A-Za-z0-9]/g, '');
        }
    }

    function isCharAllowedForRegex(char, regexPattern) {
        if (regexPattern.includes('^[A-Za-z0-9]')) {
            return /^[A-Za-z0-9]$/.test(char);
        }
        else if (regexPattern.includes('^\\d+$')) {
            return /\d/.test(char);
        }
        else if (regexPattern.includes('^[A-Z]+$')) {
            return /^[A-Z]$/.test(char);
        }
        else if (regexPattern.includes('^[a-z]+$')) {
            return /^[a-z]$/.test(char);
        }
        else if (regexPattern.includes('^TM0\\d{5}$')) {
            return true;
        }
        else {
            return /^[A-Za-z0-9]$/.test(char);
        }
    }
    function getMaxLengthFromRegex(regexPattern) {
        var match;

        match = regexPattern.match(/\{(\d+)\}/);
        if (match) {
            return parseInt(match[1]);
        }

        match = regexPattern.match(/\{(\d+),(\d+)\}/);
        if (match) {
            return parseInt(match[2]);
        }

        if (regexPattern.includes('^TM0\\d{5}$')) {
            return 8;
        }

        return 20;
    }

    function validateItmsNoWithRegex(value) {
        if (validateItmsNoDebounceTimer) {
            clearTimeout(validateItmsNoDebounceTimer);
        }

        validateItmsNoDebounceTimer = setTimeout(function() {
            var deptcode = $('#deptcode').val();
            var cat_code = $('#cat_code').val();
            var subcatid = $('#subcatid').val();
            var itms_no = value || $('#itms_no').val().trim();
            var itmsField = $('#itms_no');

            itmsField.removeClass('is-invalid is-valid is-validating');
            itmsField.siblings('.invalid-feedback').remove();

            if (!itms_no) {
                return;
            }

            itmsField.addClass('is-validating');

            $.ajax({
                url: '/masters/get_regex_pattern'
                , type: 'POST'
                , dataType: 'json'
                , data: {
                    deptcode: deptcode
                    , cat_code: cat_code
                    , subcatid: subcatid
                }
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    itmsField.removeClass('is-validating');

                    if (response.regex_pattern) {
                        var regexPattern = response.regex_pattern;
                        var regex = new RegExp(regexPattern);
                        var isMatch = regex.test(itms_no);

                        if (isMatch) {
                            itmsField.removeClass('is-invalid');
                            itmsField.data('valid-regex', regexPattern);
                        } else {
                            itmsField.removeClass('is-valid');
                            itmsField.addClass('is-invalid');

                            var errorMessage = getDynamicRegexErrorMessage(regexPattern, itms_no);
                            itmsField.after('<div class="invalid-feedback">' + errorMessage + '</div>');
                        }

                        currentRegexPattern = regexPattern;
                        applyRegexInputRestrictions(regexPattern);
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    itmsField.removeClass('is-validating');
                }
            });
        }, 500);
    }

    function getDynamicRegexErrorMessage(regexPattern, value) {
        var deptcode = $('#deptcode').val();
        var cat_code = $('#cat_code').val();
        var subcatid = $('#subcatid').val();

        if (deptcode == '01') {
            if (!value.startsWith('TM0')) {
                return 'Must start with "TM0"';
            }
            if (value.length !== 8) {
                return 'Must be exactly 8 characters (TM0 followed by 5 digits)';
            }
            var numericPart = value.substring(3);
            if (!/^\d{5}$/.test(numericPart)) {
                return 'Last 5 characters must be digits';
            }
            return 'Invalid format. Expected: TM0 followed by 5 digits (e.g., TM012345)';
        }

        try {
            if (regexPattern.includes('^[A-Za-z0-9]')) {
                if (!/^[A-Za-z0-9]+$/.test(value)) {
                    return 'Only letters (A-Z, a-z) and numbers (0-9) are allowed. No spaces or special characters.';
                }
            }
            const lengthMatch = regexPattern.match(/\{(\d+)(?:,(\d+))?\}/);
            if (lengthMatch) {
                const minLength = lengthMatch[1];
                const maxLength = lengthMatch[2] || lengthMatch[1];

                if (value.length < minLength) {
                    return `Minimum ${minLength} characters required`;
                }
                if (value.length > maxLength) {
                    return `Maximum ${maxLength} characters allowed`;
                }
            }

            if (regexPattern.includes('^\\d+$')) {
                return 'Only numbers are allowed';
            } else if (regexPattern.includes('^[A-Z]+$')) {
                return 'Only uppercase letters (A-Z) are allowed';
            } else if (regexPattern.includes('^[a-z]+$')) {
                return 'Only lowercase letters (a-z) are allowed';
            } else if (regexPattern.includes('^[A-Za-z]+$')) {
                return 'Only letters (A-Z, a-z) are allowed, no numbers';
            } else if (regexPattern.includes('^[0-9a-fA-F]+$')) {
                return 'Only hexadecimal characters (0-9, A-F) are allowed';
            }

            if (regexPattern.includes('^[A-Z]')) {
                return 'Must start with an uppercase letter';
            } else if (regexPattern.includes('^[a-z]')) {
                return 'Must start with a lowercase letter';
            } else if (regexPattern.includes('^\\d')) {
                return 'Must start with a number';
            }

            if (regexPattern.includes('[A-Z]') && regexPattern.includes('[a-z]') && regexPattern.includes('[0-9]')) {
                return 'Must include uppercase letters, lowercase letters, and numbers';
            } else if (regexPattern.includes('[A-Z]') && regexPattern.includes('[0-9]')) {
                return 'Must include uppercase letters and numbers';
            } else if (regexPattern.includes('[a-z]') && regexPattern.includes('[0-9]')) {
                return 'Must include lowercase letters and numbers';
            }

        } catch (e) {
            console.warn('Error parsing regex pattern for error message:', e);
        }

        return 'Invalid format. Only letters (A-Z, a-z) and numbers (0-9) are allowed, no spaces or special characters.';
    }

    function setupItmsValidationEvents() {
        $('#subcatid').off('change.itms');
        $('#itms_no').off('input.itms blur.itms');
        $('#deptcode').off('change.itms');
        $('#cat_code').off('change.itms');

        $(document).on('change.itms', '#subcatid', function() {
            fetchAndApplyRegexPattern();
        });

        $('#itms_no').on('input.itms', function() {
            validateItmsNoWithRegex($(this).val());
        });

        $('#itms_no').on('blur.itms', function() {
            validateItmsNoWithRegex($(this).val());
        });

        $('#deptcode').on('change.itms', function() {
            regexPatternCache = {}; // Clear cache
            $('#itms_no').removeClass('is-invalid is-valid is-validating');
            $('#itms_no').siblings('.invalid-feedback').remove();
            $('#itms_no').val('');
            fetchAndApplyRegexPattern();
        });

        $('#cat_code').on('change.itms', function() {
            regexPatternCache = {}; // Clear cache
            $('#itms_no').removeClass('is-invalid is-valid is-validating');
            $('#itms_no').siblings('.invalid-feedback').remove();
            fetchAndApplyRegexPattern();
        });
    }

function validateItmsNoForDept01(inputElement) {
        var value = $(inputElement).val();

        if (!value.startsWith('TM0')) {
            $(inputElement).val('TM0');
            value = 'TM0';
        }

        var numericPart = value.substring(3);

        var cleanNumericPart = numericPart.replace(/\D/g, '');

        if (cleanNumericPart.length > 5) {
            cleanNumericPart = cleanNumericPart.substring(0, 5);
        }

        var newValue = 'TM0' + cleanNumericPart;

        if (newValue !== value) {
            $(inputElement).val(newValue);
        }

    }

    function preventPrefixEditing(e, inputElement) {
        var cursorPosition = inputElement.selectionStart;
        var value = $(inputElement).val();

        if ([8, 9, 13, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 91, 92, 93, 144].includes(e.keyCode)) {
            return true;
        }

        if (cursorPosition < 3) {
            if (e.keyCode === 8 && cursorPosition === 3) {
                inputElement.setSelectionRange(3, 3);
                return false;
            }
            if (e.keyCode === 46 && cursorPosition < 3) {
                return false;
            }
            if (e.keyCode !== 8 && e.keyCode !== 46) {
                setTimeout(function() {
                    inputElement.setSelectionRange(3, 3);
                }, 0);
                return false;
            }
        }

        if (value.length >= 8 && ![8, 46].includes(e.keyCode)) {
            return false;
        }

        return true;
    }


    $(document).on('change', '#cat_code', function() {
        $('#itms_no').removeClass('is-invalid is-valid is-validating');
        $('#itms_no').siblings('.invalid-feedback').remove();

        onchange_catcode();
    });

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        var $mapinst_form = $('#mapinst_form');
        const firstError = $mapinst_form.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    /***********************************jquery Validation**********************************************/
    //
    $(document).ready(function() {
        $('#mapinst_form')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        fetchAlldata(lang);
applyRegexInputRestrictions('^[A-Za-z0-9]{1,20}$');

    setupItmsValidationEvents();

        var cat_code = $('#cat_code').val();
        var subcatid = $('#subcatid').val();
        if (cat_code && subcatid) {
            fetchAndApplyRegexPattern();
        }
        //   if (sessiondeptcode && sessiondeptcode.trim() !== '') {

        //     onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        // }

        lang = getLanguage('');
        var designationdata = [];

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {

            onchange_deptcode('', sessionregioncode, '', '', '', '', '', '');
            onchange_distcode(sessiondeptcode, sessionregioncode, sessiondistcode, '','')
        } else if (sessionrole == regionrole) {
            onchange_deptcode(sessiondeptcode, sessionregioncode, '', '', '', '', '', '');
            onchange_region(sessionregioncode, '')
        } else if (sessionrole == headofficerole) {
            onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        }
        // else if (sessionrole == dgarole) {
        //     getInstData(lang);
        // } else if (sessionrole == adminrole) {
        //     getInstData(lang);
        // }


    });

    $('#translate').change(function() {
        const lang = $('#translate').val();
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateTableLanguage(lang);
        updateValidationMessages(getLanguage('Y'), 'mapinst_form');
    });




    function onchange_insttype(insttype) {

        var insttype = insttype || $('#inst_type').val();

        if (insttype == "H") {
            $('#parentinst_div').hide();
            $('#hubtype_div').hide();
           $('#hubdesigtype_div').hide();

        } else if (insttype == 'S') {
         //  $('#parentinst_div').show();
       //  $('#hubtypeid').val('');
        $('#hubtype_div').show();


        } else {
           // $('#parentinst_div').hide();
            $('#hubtype_div').hide();
        $('#hubdesigtype_div').hide();


        }
    }

   function onchange_hubtype(catcode,hubtypeid){
    var hubtype = hubtypeid || $('#hubtypeid').val();
    var cat_code = catcode || $('#cat_code').val();

    if (hubtype == 'A') {
        $('#parentinst_div').show();
        $('#hubdesigtype_div').hide();
        $('#zone_div').hide();
    }
    else if (
    hubtype === 'O' &&
    !['01','02','03','04','05','06','08','09','10','11'].includes(cat_code)
) {
    $('#parentinstid').val(null);
    $('#parentinstid').select2('destroy');
    $('#parentinstid').select2();
    $('#parentinst_div').hide();
    $('#hubdesigtype_div').show();
    $('#hubdesigcode').val('').select2();
}
    else if (hubtype === 'O' && ['01','02','03','04','05','06','08','09','10','11'].includes(cat_code)) {
        $('#parentinstid').val(null);
        $('#parentinstid').select2('destroy');
        $('#parentinstid').select2();
        $('#parentinst_div').hide();
        $('#zone_div').show();
        $('#zonecode').val('').select2();

    }
    else {
        $('#parentinst_div').hide();
        $('#hubdesigtype_div').hide();
        $('#zone_div').hide();
    }


    }

  
function onchange_applicablefor(event, auditmodeCode) {  // Add event parameter
    auditmodeCode = auditmodeCode || $('#audit_mode').val();
    let checkedCount = 0;
    var checkboxes = document.querySelectorAll('input[name="applicablefor[]"]:checked');

    if (auditmodeCode == 'N' || auditmodeCode == 'T') {
        if (checkboxes.length > 1) {
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                    if (checkbox === event.target) {  // Now event is defined
                        lastChecked = checkbox;
                    }
                }
            });
            if (checkedCount > 1 && lastChecked) {
                getLabels_jsonlayout([{
                    id: 'selectonequarter',
                    key: 'selectonequarter'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text.selectonequarter,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                });
                lastChecked.checked = false;
            }
        }
    } else if (auditmodeCode == 'Q') {
        // Make sure event is defined before using
        if (event && event.target) {
            $(event.target).prop('disabled', true);
        }
        if (checkboxes.length > 3) {
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                    if (checkbox === event.target) {
                        lastChecked = checkbox;
                    }
                }
            });
            if (checkedCount > 3 && lastChecked) {
                getLabels_jsonlayout([{
                    id: 'select_threequarter',
                    key: 'select_threequarter'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text.select_threequarter,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                });
                lastChecked.checked = false;
            }
        }
    }
}


    function onchange_mode(auditmode, form, selectedQuarters, inst_type, currentdeptcode) 
{
    nonmandatory_field.forEach(id => $('#' + id).addClass('required'));
    nonmandatory_input.forEach(field => {
        $("#" + field).rules("add", { required: true });
    });

    $('.notfortemp').show();

    if (form != 'edit') {
        $('#mandays,#team_size').val('').prop('disabled', false);
    } else {
        $('#mandays,#team_size').prop('disabled', false);
    }

    var auditmodeCode = auditmode || $('#audit_mode').val();

    if (auditmodeCode === 'T') {
        // Hide the fields
        $('#inst_kms_div').hide();
        //$('#inst_priority_kms_div').hide();

        // Clear values
        $('#inst_kms').val('');
        $('#inst_priority_kms').val('');
    } else {
        // Show the fields
        $('#inst_kms_div').show();
        $('#inst_priority_kms_div').show();
    }

    // Rest of your existing code...
    if (form == 'edit' && auditmodeCode == 'Q') {
        $('input[name="applicablefor[]"]').prop('disabled', false);
        return;
    } else {
        $('input[name="applicablefor[]"]').prop('disabled', true);
    }

    if (inst_type == '' || inst_type == null) {
        $('#inst_type').prop('disabled', false);
        $('#inst_type').val('');
        onchange_insttype('');
    }

    if (auditmodeCode) {
        if (auditmodeCode == 'C') {
            $('input[name="applicablefor[]"]').prop('checked', true);
            $('input[name="applicablefor[]"]').prop('disabled', true);
            $('#parentinstid_label').removeClass('required');
        } else if (auditmodeCode == 'N') {
            var currentquartercode = '<?php echo $quarterDetails->currentquarter ?>';
            $('input[name="applicablefor[]"]').prop('disabled', true);

            $('input[name="applicablefor[]"]').each(function() {
                const quarterVal = $(this).val();
                if (form === 'edit') {
                    if (quarterVal === currentquartercode) {
                        const shouldCheck = selectedQuarters.includes(currentquartercode);
                        $(this).prop('checked', shouldCheck).prop('disabled', true);
                    } else if (selectedQuarters.includes(quarterVal)) {
                        $(this).prop('checked', true).prop('disabled', false);
                    } else {
                        $(this).prop('checked', false).prop('disabled', false);
                    }
                } else {
                    if (quarterVal == currentquartercode) {
                        $(this).prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).prop('checked', false).prop('disabled', false);
                    }
                }
            });
        } else if (auditmodeCode == 'T') {
		var currentdeptcode = currentdeptcode||$('#deptcode').val()
            nonmandatory_field.forEach(id => $('#' + id).removeClass('required'));
            nonmandatory_input.forEach(id => $('#' + id).val(''));
            nonmandatory_input.forEach(field => {
                $("#" + field).rules("remove", "required");
            });

            $('.notfortemp').hide();
            $('#mandays,#team_size').val('').prop('disabled', true);
            $('input[name="applicablefor[]"]').prop('disabled', false);
            $('#parentinstid_label').addClass('required');

 var deptcode = $('#deptcode').val() || '';

                if (deptcode == '03') {
                    $('#inst_type').val('S');
                    $('#inst_type').prop('disabled', true);
                    $('#hubtypeid').val('A').prop('disabled', true);
                    onchange_insttype('S');
                    onchange_hubtype('A');
                } else {
                    $('#inst_type').val('S');
                    $('#inst_type').prop('disabled', true);
                    onchange_insttype('S');
                }
        } else if (auditmodeCode == 'Q') {
            var currentquartercode = '<?php echo $quarterDetails->currentquarter ?>';
            $('input[name="applicablefor[]"]').prop('disabled', false);
            $('input[name="applicablefor[]"]').prop('checked', false);
        }
    } else {
        $('input[name="applicablefor[]"]').prop('checked', false);
        $('input[name="applicablefor[]"]').prop('disabled', false);
    }
}


    function onchange_deptcode(catcode = '', regioncode = '', desigcode = '', quartercode = '',
        instdesigIDArr, designationdata, auditeedeptcode = '',
        typeofauditcode = '') {

        // reset_auditor_datas();

        var deptcode = $('#deptcode').val();
        var roletypecode = $('#roletypecode').val();
        const selectedOption = document.getElementById('deptcode').selectedOptions[0];
        //     maxDropdowns = parseInt(selectedOption.getAttribute('data-membercount')) || 0;
        //     designationCount = 1;

        //     // ✅ Remove all dynamically added designations
        //     $("#member_designation .designation-item").not(":first").remove();

        //     $("#member_designation .designation-item:last button").replaceWith(`
        //       <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn mt-4" onclick="addNewRow()">
        //           <i class="ti ti-circle-plus"></i>
        //       </button>
        //   `);

        $('#subcat_div').hide();

        const lang = getLanguage('');
        if (deptcode) {

            if (deptcode == '01') {

                $('#itmsno_label').addClass('required');
            } else {
                $('#itmsno_label').removeClass('required');
            }
            $.ajax({
                url: '/masters/FilterInst', // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    roletypecode: roletypecode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {

                    var data = response.category;
                    var regiondata = response.region;
                    var quarterdata = response.auditquarter;
                    var audieedeptdata = response.audieedept;
                    var typeofauditdata = response.typeofaudit;

                    window.designationdata = response.designation; // ✅ Assign to global scope
                    // $('#designationDrop').empty();
                    $('#typeofaudit').empty();


                    $('#typeofaudit').append(
                        '<option value="" data-name-en="---Select Type Of Audit---"data-name-ta="---தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்--">---Select Type Of Audit---</option>'
                    );
                    $.each(typeofauditdata, function(index, audittype) {
                        var isSelected = audittype.typeofauditcode === typeofauditcode ? 'selected' :
                            '';
                        $('#typeofaudit').append(
                            '<option value="' + audittype.typeofauditcode + '"' +

                            ' data-name-en="' + audittype.typeofauditename + '"' +
                            ' data-name-ta="' + audittype.typeofaudittname + '" ' + isSelected +
                            '>' +
                            (lang == "en" ? audittype.typeofauditename : audittype
                                .typeofaudittname) +
                            '</option>'
                        );
                    });






                    var currentquarter = <?= json_encode($currentquarter) ?>;


                    $('#cat_code').empty();
                    $('#cat_code').append(
                        '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
                    );
                    $.each(data, function(index, category) {
                        var isSelected = category.catcode === catcode ? 'selected' : '';
                        $('#cat_code').append(
                            '<option value="' + category.catcode + '"' +
                            'if_subcategory="' + category.if_subcategory + '"' +
                            ' data-name-en="' + category.catename + '"' +
                            ' data-name-ta="' + category.cattname + '" ' + isSelected +
                            '>' +
                            (lang == "en" ? category.catename : category.cattname) +
                            '</option>'
                        );
                    });
                    $('#regioncode').empty();
                    $('#regioncode').append(
                        '<option value="" data-name-en="---Select Region---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>'
                    );

                    $.each(regiondata, function(index, region) {

                        var isSelected = region.regioncode === regioncode.trim() ? 'selected' : '';

                        $('#regioncode').append(
                            '<option value="' + region.regioncode + '"' +
                            ' data-name-en="' + region.regionename + '"' +
                            ' data-name-ta="' + region.regiontname + '" ' + isSelected +
                            '>' +
                            (lang === "en" ? region.regionename : region.regiontname) +
                            '</option>'
                        );
                    });
                    $('#auditee_dept').empty();
                    $('#auditee_dept').append(
                        '<option value="" data-name-en="---Select Auditee Office---"data-name-ta="---தணிக்கையாளர் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Auditee Office---</option>'
                    );

                    $.each(audieedeptdata, function(index, auditeedepartment) {
                        var isSelected = auditeedepartment.auditeedeptcode === auditeedeptcode ?
                            'selected' : '';
                        $('#auditee_dept').append(
                            '<option value="' + auditeedepartment.auditeedeptcode + '"' +
                            ' data-name-en="' + auditeedepartment.auditeedeptename + '"' +
                            ' data-name-ta="' + auditeedepartment.auditeedepttname + '" ' +
                            isSelected +
                            '>' +
                            (lang === "en" ? auditeedepartment.auditeedeptename : auditeedepartment
                                .auditeedepttname) +
                            '</option>'
                        );
                    });






                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#itmsno_label').removeClass('required');
            //   type of audit
            $('#typeofaudit').empty();
            $('#typeofaudit').append(
                '<option value="" data-name-en="---Select Type Of Audit---"data-name-ta="---தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்--">---Select Type Of Audit---</option>'
            );
            //   auditee dept
            $('#auditee_dept').empty();
            $('#auditee_dept').append(
                '<option value="" data-name-en="---Select Auditee Office---"data-name-ta="---தணிக்கையாளர் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Auditee Office---</option>'
            );
            //region
            $('#regioncode').empty();
            $('#regioncode').append(
                '<option value="" data-name-en="---Select Region---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>'
            );
            //Category
            $('#cat_code').empty();
            $('#cat_code').append(
                '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
            );


        }
    }

    function onchange_catcode(catcode = '', ifSubcategoryVal = '', subcatid = '') {
        const catCodeElement = document.getElementById('cat_code');
        const selectedOption = catCodeElement.selectedOptions[0];
        const ifSubcategory = ifSubcategoryVal || selectedOption.getAttribute('if_subcategory');
        const lang = getLanguage('');
if (!subcatid) {
                        $('#subcatid').val('');
                        // Only fetch regex when editing and subcatid exists
                        if (subcatid) {
                            fetchAndApplyRegexPattern();
                            }
                        }

const newSubcatid = $('#subcatid').val();
disableQuartersBasedOnCategory(catcode, newSubcatid);
 
        if (ifSubcategory == 'Y') {
            $('#subcat_div').show();
            var catcode = catcode || $('#cat_code').val();

            $.ajax({
                url: '/masters/FilterInst'
                , method: 'POST'
                , data: {
                    catcode: catcode
                , }
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                    var data = response.subcategory;

                    $('#subcatid').empty();
                    $('#subcatid').append(
                        '<option value="" data-name-en="---Select Sub Category---"data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">Select Sub Category</option>'
                    );

                    $.each(data, function(index, subcategory) {
                        var isSelected = subcategory.auditeeins_subcategoryid === subcatid ?
                            'selected' : '';
                        $('#subcatid').append(
                            '<option value="' + subcategory.auditeeins_subcategoryid +
                            '"' +
                            ' data-name-en="' + subcategory.subcatename + '"' +
                            ' data-name-ta="' + subcategory.subcattname + '" ' +
                            isSelected +
                            '>' +
                            (lang === "en" ? subcategory.subcatename : subcategory.subcattname) +
                            '</option>'
                        );
                    });

                     if (!subcatid) {
                        $('#subcatid').val('');
                        // Only fetch regex when editing and subcatid exists
                        if (subcatid) {
                            fetchAndApplyRegexPattern();
                            }
                        }
                }
                , error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
         } else {
        $('#subcat_div').hide();
        // Clear subcatid if no subcategory
        $('#subcatid').val('');
        disableQuartersBasedOnCategory(catcode, null);
        // For department 01, apply TM0 format immediately
        if ($('#deptcode').val() == '01') {
            applyRegexInputRestrictions('^TM0\\d{5}$');
            // Clear validation
            $('#itms_no').removeClass('is-invalid is-valid is-validating');
            $('#itms_no').siblings('.invalid-feedback').remove();
        } else {
            // For other departments, fetch regex pattern
            fetchAndApplyRegexPattern();
        }
    }
    }


    function onchange_region(regioncode, distcode = '', instmappingcode = '') {

        var regioncode = regioncode || $('#regioncode').val();
        var deptcode = $('#deptcode').val();

        const lang = getLanguage('');

        $.ajax({
            url: '/masters/FilterInst', // Your API route to get user details
            method: 'POST',
            data: {
                regioncode: regioncode,
                deptcode: deptcode
                // roletypecode: roletypecode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var districtdata = response.districtdet;

                $('#distcode').empty();
                $('#distcode').append(
                    '<option value="" data-name-en="---Select District---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>'
                );


                $.each(districtdata, function(index, distdata) {
                    var isSelected = distdata.distcode === distcode ? 'selected' : '';
                    $('#distcode').append(
                        '<option value="' + distdata.distcode + '"' +
                        ' data-name-en="' + distdata.distename + '"' +
                        ' data-name-ta="' + distdata.disttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? distdata.distename : distdata.disttname) +
                        '</option>'
                    );
                });

                // if (instmappingcode != '' || instmappingcode != null) {
                //     onchange_distcode(deptcode, regioncode, distcode, instmappingcode)
                // }
                // }
            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    }

    function onchange_distcode(deptcode = '', regioncode = '', distcode = '', instmappingcode = '', parentinstid = '',hubdesigcode = '',zonecode = '') {

        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';
        var instid = $('#instid').val();

        const lang = getLanguage('');
        //  $('#parentinstid').empty();
        //         $('#parentinstid').append(
        //             '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
        //         );
        $.ajax({
            url: '/masters/FilterAuditInst', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                instid: instid
                // roletypecode: roletypecode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var auditofficedet = response.auditofficedet;
                var parentinstdet = response.parentinstdet;
		var hubdesigdata = response.hubdesig;
                var zonecodedata = response.zonecode;

                $('#auditoffice').empty();
                $('#auditoffice').append(
                    '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                );


                $.each(auditofficedet, function(index, auditoff) {
                    var isSelected = auditoff.instmappingcode === instmappingcode ? 'selected' : '';
                    $('#auditoffice').append(
                        '<option value="' + auditoff.instmappingcode + '"' +
                        ' data-name-en="' + auditoff.instename + '"' +
                        ' data-name-ta="' + auditoff.insttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? auditoff.instename : auditoff.insttname) +
                        '</option>'
                    );
                });
                $('#parentinstid').empty();
                $('#parentinstid').append(
                    '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
                );
                $.each(parentinstdet, function(index, parentinst) {

                    var isSelected = parentinst.instid === parentinstid ? 'selected' : '';


                    $('#parentinstid').append(
                        '<option value="' + parentinst.instid + '"' +
                        ' data-name-en="' + parentinst.instename + '"' +
                        ' data-name-ta="' + parentinst.insttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? parentinst.instename : parentinst.insttname) +
                        '</option>'
                    );
                });
 			$('#hubdesigcode').empty();


                    $('#hubdesigcode').append(
                        '<option value="" data-name-en="---Select Hub Designation---"data-name-ta="">---Select Hub Designation---</option>'
                    );
                    $.each(hubdesigdata, function(index, hdesig) {
                        var isSelected = hdesig.desigcode === hubdesigcode ? 'selected' :
                            '';
                        $('#hubdesigcode').append(
                            '<option value="' + hdesig.desigcode + '"' +
                            ' data-name-en="' + hdesig.desigename + '"' +
                            ' data-name-ta="' + hdesig.desigtname + '" ' + isSelected +
                            '>' +
                            (lang == "en" ? hdesig.desigename : hdesig
                                .desigtname) +
                            '</option>'
                        );
                    });
 $('#zonecode').empty();


                        $('#zonecode').append(
                            '<option value="" data-name-en="---Select Zone name---"data-name-ta="">---Select Zone name---</option>'
                        );
                        $.each(zonecodedata, function(index, zone) {
                            var isSelected = zone.zonecode === zonecode ? 'selected' :
                                '';
                            $('#zonecode').append(
                                '<option value="' + zone.zonecode + '"' +
                                ' data-name-en="' + zone.zoneename + '"' +
                                ' data-name-ta="' + zone.zonetname + '" ' + isSelected +
                                '>' +
                                (lang == "en" ? zone.zoneename : zone
                                    .zonetname) +
                                '</option>'
                            );
                        });
            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function onchange_auditoffice(deptcode = '', regioncode = '', distcode = '', instmappingcode = '',
        nodalperson_desigcode = '') {
        var instmappingcode = instmappingcode || $('#auditoffice').val();
        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';

        const lang = getLanguage('');
        $('#audit_office_desig').empty();
        $('#audit_office_desig').append(
            '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
        );
        if (instmappingcode) {
            $.ajax({
                url: '/masters/FilterAuditInst', // Your API route to get user details
                method: 'POST',
                data: {
                    instmappingcode: instmappingcode,
                    deptcode: deptcode,
                    regioncode: regioncode,
                    distcode: distcode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var auditordet = response.auditordet;
                    var auditordesigdet = auditordet[0];
                    //auditordesigdet)
                    $('#audit_office_desig').empty();
                    $('#audit_office_desig').append(
                        '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
                    );



                    var isSelected = auditordesigdet.nodalperson_desigcode === nodalperson_desigcode ?
                        'selected' :
                        '';
                    $('#audit_office_desig').append(
                        '<option value="' + auditordesigdet.nodalperson_desigcode + '"' +
                        ' data-name-en="' + auditordesigdet.desigelname + '"' +
                        ' data-name-ta="' + auditordesigdet.desigtlname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? auditordesigdet.desigelname : auditorsdesig
                            .desigtlname) +
                        '</option>'
                    );


                    // $('#audit_ad').empty();
                    // $('#audit_ad').append(
                    //     '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                    // );


                    // $.each(auditordet, function(index, auditors) {
                    //     var isSelected = auditors.deptuserid === deptuserid ? 'selected' :
                    //         '';
                    //     $('#audit_ad').append(
                    //         '<option value="' + auditors.deptuserid + '"' +
                    //         ' data-name-en="' + auditors.username + '"' +
                    //         ' data-name-ta="' + auditors.usertamilname + '" ' + isSelected +
                    //         '>' +
                    //         (lang === "en" ? auditors.username : auditors.usertamilname) +
                    //         '</option>'
                    //     );
                    // });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#audit_office_desig').empty();
            $('#audit_office_desig').append(
                '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
            );
        }

    }

    function onchange_auditofficedesig(instmappingcode, desigcode = '', deptuserid = '', regioncode, distcode) {
        var instmappingcode = instmappingcode || $('#auditoffice').val();
        var desigcode = desigcode || $('#audit_office_desig').val();
        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';

        const lang = getLanguage('');

        $('#audit_ad').empty();
        $('#audit_ad').append(
            '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
        );
        if (desigcode) {
            $.ajax({
                url: '/masters/FilterAuditInst', // Your API route to get user details
                method: 'POST',
                data: {
                    instmappingcode: instmappingcode,
                    desigcode: desigcode,
                    regioncode: regioncode,
                    deptcode: deptcode,
                    distcode: distcode

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var auditorsdet = response.auditorsuserdet;



                    $('#audit_ad').empty();
                    $('#audit_ad').append(
                        '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                    );


                    $.each(auditorsdet, function(index, auditors) {
                        var isSelected = auditors.deptuserid === deptuserid ? 'selected' :
                            '';
                        $('#audit_ad').append(
                            '<option value="' + auditors.deptuserid + '"' +
                            ' data-name-en="' + auditors.username + '"' +
                            ' data-name-ta="' + auditors.usertamilname + '" ' + isSelected +
                            '>' +
                            (lang === "en" ? auditors.username : auditors.usertamilname) +
                            '</option>'
                        );
                    });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#audit_ad').empty();
            $('#audit_ad').append(
                '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
            );
        }

    }


    $(document).on('click', '#buttonaction', function(event) {

        event.preventDefault(); // Prevent form submission
        // insertorUpdate_instdata('insert');
        var allowedusers = '3'; // Get the number of users
        var teamsize = $('#team_size').val()
	    var auditmode=$('#audit_mode').val();
        if (teamsize > 10) {
            getLabels_jsonlayout([{
                id: 'teamsizelimit_msg',
                key: 'teamsizelimit_msg'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .teamsizelimit_msg, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            return;
        } else if (teamsize < 1 && auditmode !='T') {
            passing_alert_value('Confirmation', 'Team size cannot be zero', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return;
        }
var $mapinst_form = $('#mapinst_form');
        if (!$mapinst_form.valid()) {
            scrollToFirstError();
            return;
        }

        var deptcode = $('#deptcode').val();
        var cat_code = $('#cat_code').val();
        var subcatid = $('#subcatid').val();
        var itms_no = $('#itms_no').val().trim();
        var itmsField = $('#itms_no');

        if (itms_no) {
            var validRegex = itmsField.data('valid-regex');
            var serverRegex = itmsField.data('server-regex');

            if (validRegex || serverRegex) {
                var regexPattern = validRegex || serverRegex;
                var regex = new RegExp(regexPattern);

                if (!regex.test(itms_no)) {
                    var errorMessage = getDynamicRegexErrorMessage(regexPattern, itms_no);
                    itmsField.addClass('is-invalid');
                    itmsField.siblings('.invalid-feedback').remove();
                    itmsField.after('<div class="invalid-feedback">' + errorMessage + '</div>');

                    scrollToFirstError();
                    return;
                }
            } else if (deptcode == '01') {
                if (!/^TM0\d{5}$/.test(itms_no)) {
                    itmsField.addClass('is-invalid');
                    itmsField.siblings('.invalid-feedback').remove();
                    itmsField.after('<div class="invalid-feedback">Must be in TM0 followed by 5 digits format (e.g., TM012345)</div>');

                    scrollToFirstError();
                    return;
                }
            }
        } else if (deptcode == '01') {
            itmsField.addClass('is-invalid');
            itmsField.siblings('.invalid-feedback').remove();
            itmsField.after('<div class="invalid-feedback">ITMS/Reg No/Reference No is required for this department</div>');

            scrollToFirstError();
            return;
        }
        var mandays = $('#mandays').val();

        if (mandays < 1  && auditmode !='T')  {
            passing_alert_value('Confirmation', 'Mandays can not be zero', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return;
        }
        applyValidationToNewFields(`reportdesignation[1]`, 'Enter Designation');
        applyValidationToNewFields(`reportemail[1]`, 'Enter Email');
        applyValidationToNewFields(`reportmobile[1]`, 'Enter Mobile Number');

        var $mapinst_form = $('#mapinst_form');

        if ($mapinst_form.valid()) {
            var checkboxes = document.querySelectorAll('input[name="applicablefor[]"]:checked');
            if (checkboxes.length === 0) {
                getLabels_jsonlayout([{
                    id: 'selectquarter',
                    key: 'selectquarter'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .selectquarter, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });

                return;

            }

            var auditmodeCode = $('#audit_mode').val();

            if (auditmodeCode == 'N' || auditmodeCode == 'T') {

                if (checkboxes.length > 1) {
                    getLabels_jsonlayout([{
                        id: 'selectonequarter',
                        key: 'selectonequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .selectonequarter, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;

                }
            } else if (auditmodeCode == 'Q') {
                if (checkboxes.length > 3) {
                    getLabels_jsonlayout([{
                        id: 'select_threequarter',
                        key: 'select_threequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .select_threequarter, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;

                }
            }
            var insttype = $('#inst_type').val();
		var hubtype = $('#hubtypeid').val();
	if (insttype == 'S' && (!$('[name="parentinstid"]').val() || $('[name="parentinstid"]').val().trim() === '') && hubtype != 'O') {

                applyValidationToNewFields('parentinstid', 'Select Parent Institution');
                scrollToFirstError();
                return;
            }

            var mandays = $('#mandays').val();
            if (mandays > 1000) {
                passing_alert_value('Confirmation', 'Mandays should not exceed 1000', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
                return
            }

            var deptcode = $('#deptcode').val();
           if ((!$('[name="itms_no"]').val() || $('[name="itms_no"]').val().trim() === '')) {

                applyValidationToNewFields('itms_no', 'Enter ITMS/Reg No/Reference No');
                scrollToFirstError();
                return;
            }


  var old_dept  = $('#deptcode_hidden').val()
        var new_dept  = $('#deptcode').val()
        var apms_inst = $('#apms_inst').val()

        if((old_dept != '' ||old_dept != 'null')&& (new_dept!= old_dept) &&(apms_inst == 'Y') )
        {
  $("#process_button").addClass("apms_inst_changes");
           passing_alert_value('Confirmation', 'This institution has APMS details. Changing the department will also update the APMS details. Are you sure to continue?', 'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');

             $('.apms_inst_changes').off('click').on('click', function(event) {
                 event.preventDefault();
                 insertorUpdate_instdata('insert');
             });
        }
        else{
            insertorUpdate_instdata('insert');
        } }else {

            scrollToFirstError();
        }
    });

    function insertorUpdate_instdata(action) {

        $('#buttonaction').attr('disabled', true);

        var formData = $('#mapinst_form').serializeArray();
        var deptcode = $('#deptcode').val();
        var distcode = $('#distcode').val();
        var regioncode = $('#regioncode').val();
        var audit_mode = $('#audit_mode').val();


        if ($('#deptcode').prop('disabled')) {

            formData.push({
                name: 'deptcode',
                value: deptcode
            });
        }
        if ($('#audit_mode').prop('disabled')) {

            formData.push({
                name: 'audit_mode',
                value: audit_mode
            });
        }
        ['inst_kms'].forEach(function (id) {
                 const $el = $('#' + id);

                if ($el.prop('disabled')) {
                    formData.push({
                        name: id,
                        value: $el.val()
                    });
                }
            });
        
        const checkboxes = $('input[name="applicablefor[]"]'); // Select all checkboxes


        // Loop through checkboxes to check if they are disabled and selected
        checkboxes.each(function() {
            if ($(this).prop('disabled') && $(this).prop('checked')) {
                formData.push({
                    name: 'applicablefor[]',
                    value: $(this).val() // Push selected disabled checkbox value
                });
            }
        });
        if ($('#regioncode').prop('disabled')) {

            formData.push({
                name: 'regioncode',
                value: regioncode
            });
        }
               if ($('#distcode').prop('disabled')) {

            formData.push({
                name: 'distcode',
                value: distcode
            });
        }


        $.ajax({
            url: '/masters/insertorupdate_mapInst', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    reset_form()

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', Object.values(text)[0],
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    // fetchAlldata();
                    // reset_form();
                    // table.ajax.reload(); // Reload the table
                    fetchAlldata(lang);

                } else if (response.error) {}
            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired

                $('#buttonaction').removeAttr('disabled');
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


function populateDeptDropdown(data) {

    let dropdown = $('#deptcode');
    dropdown.empty();

    // Add default option
    dropdown.append(`
        <option value=""
            data-name-en="---Select Department---"
            data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">
            ---Select Department---
        </option>
    `);

    // Add department options
    data.forEach(function(dept) {
        dropdown.append(`
            <option value="${dept.deptcode}"
                data-name-en="${dept.deptelname}"
                data-name-ta="${dept.depttlname}">
                ${dept.deptelname}
            </option>
        `);
    });
}


    function reset_form() {
 populateDeptDropdown(dept_values)
    $('#inst_kms').prop('disabled', false);
        changeButtonAction('mapinst_form', 'action', 'buttonaction', 'reset_button',
            'display_error', @json($savebtn), @json($clearbtn),
            @json($insert));
        $('#mapinst_form')[0].reset();

$('#inst_kms').val('');
        $('#inst_priority_kms').val('');

        $('#inst_kms_div').show();
        $('#inst_priority_kms_div').show();
	$('#zonecode').val('');

      	$('#zone_div').hide();

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'


        if (sessionrole == distrole) {

            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2('destroy');
            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").val(null);
            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2();
            onchange_deptcode('', sessionregioncode, '', '', '', '', '', '');
            onchange_distcode(sessiondeptcode, sessionregioncode, sessiondistcode, '', '')
        } else if (sessionrole == regionrole) {
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2('destroy');
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").val(null);
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2();
            onchange_deptcode(sessiondeptcode, sessionregioncode, '', '', '', '', '', '');

            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2('destroy');
            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2(null);
            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2();
            $('#distcode').empty();
            $('#distcode').append(
                '<option value="" data-name-en="---Select District---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>'
            );
            onchange_deptcode(sessiondeptcode, '', '', '', '', '', '', '');

        } else {
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").select2('destroy');
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").val(null);
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").select2();
            
            $('#deptcode').attr('disabled',false);
		onchange_deptcode('', '', '', '', '', '', '')
            onchange_region('', '')
            $('#parentinstid').empty();
            $('#parentinstid').append(
                '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
            );
        }


        $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept,categorization").select2('');
        //   $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept").val(null);
        //   $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept").select2();
        $('#work-row-insert').show();
        $('#parentinst_div').hide();

        $('#EditrowUsers').html('');
        onchange_insttype();
        $('#row0').addClass('d-flex');
        var rowcount = '0'
        // onchange_deptcode('', '', '', '', '', '', '')
        // onchange_region('', '')


        $('#mapinst_form').validate().resetForm();
        $('#designationDrop').empty();
        dropdownCount = 1;
        //   designationdata = [];

    }

    $('#translate').change(function() {
        lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        // updateSelect2Language(lang); // Update Select2 dropdown
        // changeButtonText('action', 'buttonaction', 'reset_button', '', @json($savebtn),
        //     @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'mapinst_form');

    });


 $(document).on("keypress", ".dynamic_keypress", function (event) {

            let charCode = event.which || event.keyCode;

            // allow backspace, tab, enter
            if (charCode === 8 || charCode === 9 || charCode === 13) return true;

            let charStr = String.fromCharCode(charCode);

            let pattern = $(this).attr("data-regex-char");
            if (!pattern) return true;

            let regex = new RegExp(pattern);

            if (!regex.test(charStr)) {
                event.preventDefault();
                return false;
            }
        });

        $(document).on("input paste", ".dynamic_keypress", function () {
        
            let value = $(this).val();
            let charPattern = $(this).attr("data-regex-char");
        
            if (!charPattern) return;
        
            // Convert char regex ^[... ]$ into remove regex [^... ]
            let removePattern = charPattern
                .replace(/^\^\[/, "[^")
                .replace(/\]\$$/, "]");
        
            let removeRegex = new RegExp(removePattern, "g");
        
            $(this).val(value.replace(removeRegex, ""));
        });

        $(document).on("blur", ".dynamic_keypress", function () {
        
            let val = $(this).val().trim();
            let pattern = $(this).attr("data-regex-full");
        
            if (!pattern) return;
        
            let regex = new RegExp(pattern);
        
            if (!regex.test(val)) {
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });

</script>
@endsection
