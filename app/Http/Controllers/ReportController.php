<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

use App\Models\ReportModel;
use App\Models\MastersModel;
use App\Models\FieldAuditModel;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function pendingparra()
    {
        $sessionuserdel    =    session('user');
        $sessionuserid    =    $sessionuserdel->userid;
        $results         =   ReportModel::fetchpendingparas($sessionuserid);

        foreach ($results as $all) {
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
            $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
        }

        return view('fieldaudit.pendingpara', compact('results'));
    }

    // public function getpendingparadetails(Request $request)
    // {
    //     $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
    //     $quartercode = $request->quartercode;
    //     $slipsts = $request->slipsts;
    //     $filterapply = $request->filterapply;
	// $quarter = $request->input('quarter'); 
    //     // Fetch details
    //     $alldetails = ReportModel::getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply,$quarter);
    //     $responseData = json_decode($alldetails->getContent(), true);

    //     foreach ($responseData['data'] as &$record) {
    //         $record['auditslipid'] = Crypt::encryptString($record['auditslipid']);
    //     }

    //     // Replace the original 'data' inside the response
    //     $responseData['data'] = $responseData['data'];

    //     $jsonencoded_response = $responseData;

    //     if ($responseData['totalslips'] > 0) {
    //         return response()->json(['success' => true, 'data' => $jsonencoded_response]);
    //     } else {
    //         return response()->json(['success' => true, 'message' => 'No auditslips found'], 200);
    //     }
    // }

    public function getpendingparadetails(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $quartercode = $request->quartercode;
        $slipsts = $request->slipsts;
        $filterapply = $request->filterapply;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyearcode');
        // Fetch details
        $alldetails = ReportModel::getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply, $quarter, $financialyearcode   );
        $responseData = json_decode($alldetails->getContent(), true);

        foreach ($responseData['data'] as &$record) {
            $record['auditslipid'] = Crypt::encryptString($record['auditslipid']);
        }

        // Replace the original 'data' inside the response
        $responseData['data'] = $responseData['data'];

        $jsonencoded_response = $responseData;

        if ($responseData['totalslips'] > 0) {
            return response()->json(['success' => true, 'data' => $jsonencoded_response]);
        } else {
            return response()->json(['success' => true, 'message' => 'No auditslips found'], 200);
        }
    }

 public function paradetails(Request $request)
    {
        $instid = $request->instid;
        $viewtype = $request->viewtype;
        $paraprocesscode = $request->paraprocesscode;

        $alldetails = ReportModel::paradetails($instid, $viewtype, $paraprocesscode);

        if (isset($alldetails['success']) && !$alldetails['success']) {
            return response()->json($alldetails, 500);
        }

        if (!is_iterable($alldetails)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data returned from model'
            ], 500);
        }

        foreach ($alldetails as $record) {
            if (!empty($record->followupid)) {
                $record->followupid = Crypt::encryptString($record->followupid);
            } else {
                $record->followupid = null;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $alldetails
        ]);
    }


  public function getParaistoryDetails($id)
    {
        $followupid = Crypt::decryptString($id);

        return ReportModel::getParaDetailHistory($followupid);
    }

    public function getSlipDetailsHistory($slipId)
    {
        $slipId = Crypt::decryptString($slipId);
        return ReportModel::getSlipDetailsHistory($slipId);
    }

    public function getSlipHistoryDetails($slipId)
    {
        $slipId = Crypt::decryptString($slipId);
        return ReportModel::getSlipHistoryDetails($slipId);
    }

   public function getInspectionDetailHistory($inspectionid)
    {
        $inspectionid = Crypt::decryptString($inspectionid);

        return ReportModel::getInspectionDetailHistory($inspectionid);
    }

    public function getInspectionHistoryDetails($inspectionid)
    {
        $inspectionid = Crypt::decryptString($inspectionid);

        return ReportModel::getInspectionHistoryDetails($inspectionid);
    }


 public function inspectview_dropdown($index)
    {
        $dept = ReportModel::getDept();
        $region = ReportModel::getRegion();
        $district = ReportModel::getDistrict();
        $financialyear = ReportModel::getDFinancialyear();
        $quarter = ReportModel::getQuarter();
	$epacdept = ReportModel::getEprDept();
        $defaultYearCode = $financialyear->first()->financialyearcode ?? null;
        $selectedQuarters = ['Q2'];

        $currentQuarter = null;
        $currentQuarterCode = null;
        $currentQuarterName = null;
        $quarterPages = [
            'report.paradetails',
            'report.auditintimation',
            'report.pendingslips',
            'report.auditormandays'
        ];

        if (in_array($index, $quarterPages)) {
            $currentQuarterData = ReportModel::getquarterDet();

            if ($currentQuarterData) {
                $currentQuarter = $currentQuarterData->currentquarter;
                $currentQuarterCode = $currentQuarterData->auditquartercode;
                $currentQuarterName = $currentQuarterData->auditquartertname;
            }
        }

        return view($index, compact(
            'dept',
            'region',
            'district',
            'financialyear',
            'quarter',
            'defaultYearCode',
            'selectedQuarters',
            'currentQuarter',
            'currentQuarterCode',
            'currentQuarterName',
	    'epacdept',
        ));
    }

function fetch_deptbaseddata(Request $request)
{
//return 'asda';

$request->merge([
    'regioncode' => is_array($request->regioncode) ? $request->regioncode : [$request->regioncode],
    'distcode' => is_array($request->distcode) ? $request->distcode : [$request->distcode],
]);




$validatedData = $request->validate([
'deptcode' => ['nullable', 'array'],
'deptcode.*' => ['required', 'string', 'regex:/^(A|\d+)$/'],
'regioncode' => ['nullable', 'array'],
'regioncode.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
'distcode' => ['nullable', 'array'],
'distcode.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
'instmappingcode' => ['nullable', 'array'],
'instmappingcode.*' => ['nullable', 'regex:/^\d+$|^A$/'],
'financialyear' => ['nullable', 'string', 'regex:/^\d+$/'],
'auditquarter' => ['nullable', 'array'],
'auditquarter.*' => ['nullable', 'string', 'regex:/^(Q[1-4]|A)$/i'],
'maxslip' => ['nullable', 'integer'],
'logindate' => ['nullable', 'string', 'date'],
'catcode' => ['nullable', 'array'],
'catcode.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
'subcatcode' => ['nullable', 'array'],
'subcatcode.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
'reportstatuscode' => ['nullable', 'string'],
'actioncode' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9]+$/'],
'reportstatus' => ['nullable', 'array'],
'reportstatus.*' => ['nullable', 'string', 'regex:/^[A-Z]$/'],
'audityearcode' => ['nullable', 'array'],
'audityearcode.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
'parareportstatus' => ['nullable', 'array'],
'parareportstatus.*' => ['nullable', 'string', 'regex:/^[A-Z]$/'],
'valuefor' => ['required', 'string', 'in:region,district,category,catcode,subcatcode,institution,yearcode,audit,institutionforauditreport,quarter,institutionforexitmeeting,audityear,auditquarter'], // Include "region"
'formname' => ['required', 'string', 'in:checkschedulestatus,idleuser,schedulelist,misreport,scheduledcountreport,auditreport,exitmeetingnotdone,dailyloginstatus,inspectionreport,inspectioncount,lagacystatus,parareport,idlereport,legacycount,paraoverallcount,leavedetails,diarydetails,spilloverwithdiarydetails,apmsalldetailscount,apmsdetails,sliptotalparadetails,moneyandnonmoneyparadetails,unapprovedauditpara,droppedslipcountreport,paradetailsreport,auditeeintimationreport,plancountreport,auditreportcount,epacsdetails'],

], [    
'required' => 'The :attribute field is required.',
'regex' => 'The :attribute field must be a valid number.',
'in' => 'The :attribute field must be one of: region, district, institution.',
]);




$deptcode = $validatedData['deptcode'] ?? [];
$regioncode = $validatedData['regioncode'] ?? [];
$distcode = $validatedData['distcode'] ?? [];
$financialyear = $validatedData['financialyear'] ?? null;
$auditquarter = $validatedData['auditquarter'] ?? [];
$valuefor = $validatedData['valuefor'];
$formname = $validatedData['formname'];
$maxslip   = $validatedData['maxslip'] ?? null; 
$actioncode = $validatedData['actioncode'] ?? 'A';
$instmappingcode = $validatedData['instmappingcode'] ?? [];
$logindate = $validatedData['logindate'] ?? '';
$reportstatuscode = $validatedData['reportstatuscode'] ?? '';
$catcode = $validatedData['catcode'] ?? [];
$subcatcode = $validatedData['subcatcode'] ?? [];
$audityearcode = $validatedData['audityearcode'] ?? [];
$reportstatus = $validatedData['reportstatus'] ?? [];
$parareportstatus = $validatedData['parareportstatus'] ?? [];

if ($valuefor === 'district' && !$deptcode) {
return response()->json(['success' => false, 'message' => 'Department code is required for district.'], 422);
}


try {


$getdata = ReportModel::fetch_deptbaseddata(
$audityearcode,
$deptcode,
$regioncode,
$distcode,
$valuefor,
$financialyear,
$auditquarter,
$formname,
$actioncode,
$instmappingcode,
$maxslip,
$logindate,
$reportstatuscode,
$catcode,
$subcatcode,
$reportstatus,
$parareportstatus,


);


if (in_array($valuefor, ['region', 'district', 'institution','institutionforauditreport','institutionforexitmeeting','catcode','subcatcode','audityear','auditquarter'])) {
    $dataArray = $getdata['data']->toArray();

    array_unshift($dataArray, [
        'code' => 'A',
        'name_en' => 'All',
        'name_ta' => 'அனைத்து',
    ]);

    $getdata['data'] = $dataArray;
}


if ($getdata) {
return response()->json(['success' => true, 'data' => $getdata['data']]);
}

return response()->json(['success' => false, 'message' => 'Data not found'], 404);
} catch (\Exception $e) {
return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
}
}


   public function getMinSlipCount(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$financialyear = $request->financialyear ?? null;
$auditquarter = $request->auditquarter ?? null;
 $instmappingcode = $request->instmappingcode ?? null;
 $maxslip = $request->maxslip ?? null;
 $catcode = $request->category ?? null;
 $subcatcode = $request->subcategory ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

if ($auditquarter == 'A' || (is_array($auditquarter) && in_array('A', $auditquarter))) {
    $auditquarter = null;
}

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'financialyear' => $financialyear ?? '',
'auditquarter' => $auditquarter,
'catcode' => $catcode ?? 'A',
'subcatcode' => $subcatcode ?? 'A',

'instmappingcode' => $instmappingcode ?? '',
'maxslip' => $maxslip ?? 0,


];


if ($formname == 'scheduledcountreport') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::getMinSlipCountData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::getMinSlipCountData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}



public function fetch_auditreport(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$instmappingcode = $request->instmappingcode ?? null;
$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;
$financialyear = $request->financialyear ?? null;
$auditquarter = $request->auditquarter ?? null;
$reportstatuscode = $request->reportstatuscode ?? null;

if (is_array($distcode)) {
    $distcode = array_unique($distcode);
}

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'instmappingcode' => $instmappingcode ?? '',
'financialyear' => $financialyear ?? '',
'auditquarter' => $auditquarter,
'reportstatuscode' => $reportstatuscode,  
];


if ($formname == 'auditreport') {
    
$instdet = ReportModel::getAuditreportData($data);


} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::getAuditreportData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public function inspectionreport_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$financialyear = $request->financialyear ?? null;
$auditquarter = $request->auditquarter ?? null;
 $instmappingcode = $request->instmappingcode ?? null;
 $reportstatus  = $request->reportstatus ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'financialyear' => $financialyear ?? '',
'auditquarter' => $auditquarter, 
'instmappingcode' => $instmappingcode ?? '',
'reportstatus' => $reportstatus ?? '',


];


if ($formname == 'inspectionreport') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::InspectionreportData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::InspectionreportData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public static function checklistdeptfetch()
{
 $dept = ReportModel::commondeptfetch(); 
 $region = ReportModel::regionfetch(); 
 $district = ReportModel::districtfetch(); 
 $financialyear = ReportModel::getDFinancialyear();
 $quarter = ReportModel::getQuarter();
 
 return view('report.viewreservelist', compact('dept','region','district','financialyear','quarter'));
}



public function getregionbasedondeptchecklist(Request $request)
{
 $request->validate([
'deptcode' => ['nullable', 'array'],
'deptcode.*' => ['required', 'string', 'regex:/^(A|\d+)$/'],
 ], );

 $deptcode = $request->input('deptcode');


 $regions = ReportModel::getRegionsByDept($deptcode);


 return response()->json([
     'success' => true,
     'data' => $regions
     
 ]);
}


public function getdistrictofchecklist(Request $request)
{
 // Validate the input
 $request->validate(
     [
        'deptcode' => ['nullable', 'array'],
        'deptcode.*' => ['required', 'string', 'regex:/^(A|\d+)$/'],
        'region' => ['nullable', 'array'],
        'region.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
        'district' => ['nullable', 'array'],
        'district.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],
     ],
    
 );

 // Get the department code
 $regioncode = $request->input('region');
 $deptcode = $request->input('deptcode');
 $district = $request->input('district');


 $district = ReportModel::changerequestfetchData('audit.deptuserdetails',$deptcode,$regioncode,$district);

 // Return JSON response
 if ($district->isNotEmpty()) {
     return response()->json(['success' => true, 'data' => $district], 200);
 } else {
     return response()->json(['success' => false, 'message' => 'No regions found'], 200);
 }
}


public function getdistrictbasedonregionchecklist(Request $request)
{

 $request->validate(
     [
        'deptcode' => ['nullable', 'array'],
        'deptcode.*' => ['required', 'string', 'regex:/^(A|\d+)$/'],
        'region' => ['nullable', 'array'],
        'region.*' => ['nullable', 'string', 'regex:/^(A|\d+)$/'],

     ],
     [
         'deptcode.required' => 'The deptcode field is required.',
         'deptcode.regex'    => 'The deptcode field must be a valid number.',
         'region.required'   => 'The region field is required.',
         'region.regex'      => 'The region field must be a valid number.',
     ]
 );


 $regioncode = $request->input('region');
 $deptcode = $request->input('deptcode');


 if ($regioncode) {

     $district = ReportModel::getdistrictByregion($regioncode, $deptcode);

 } else {
 
     $district = ReportModel::changerequestfetchData('audit.deptuserdetails', $deptcode, $regioncode, null);
 }


 // Return JSON response
 if ($district->isNotEmpty()) {
     return response()->json(['success' => true, 'data' => $district]);
 } else {
     return response()->json(['success' => false, 'message' => 'No regions found'], 200);
 }
}


public function checklist_fetchData(Request $request)
{

 $deptcode = $request->input('deptcode');
 $regioncode = $request->input('region');
 $districtcode = $request->input('district');



 $alldetails = ReportModel::changerequestfetchData('audit.deptuserdetails',$deptcode, $regioncode, $districtcode);


 return response()->json([
     'success' => true,
     'message' => empty($alldetails) ? 'No Details found' : '',
     'data' => $alldetails ?? []
 ], 200);
}


public function fetch_reportcount(Request $request)
{
try {
$deptcode = $request->deptcode ?? 'A';
$auditquarter = $request->auditquarter;
$financialyear = $request->financialyear;




$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'auditquarter' => $auditquarter,
'financialyear' => $financialyear,


];
if ($formname == 'auditreportcount') {

$instdet = ReportModel::auditReportCountData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::auditReportCountData($data, $teamHead);
}


return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}



public function fetch_dailylogin(Request $request)
{
try {
$deptcode = $request->deptcode ?? 'A';
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$logindate = $request->logindate ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'logindate' => $logindate ?? null,

];
if ($formname == 'dailyloginstatus') {

$instdet = ReportModel::loginstatusCountData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::loginstatusCountData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet // only rows, no pagination metadata
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}


public function exitmeetingnotdone(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$financialyear = $request->financialyear ?? null;
$auditquarter = $request->auditquarter ?? null;
 $instmappingcode = $request->instmappingcode ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;


$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'financialyear' => $financialyear ?? '',
'auditquarter' => $auditquarter, 
'instmappingcode' => $instmappingcode ?? '',


];

if ($formname == 'exitmeetingnotdone') {

$instdet = ReportModel::ExitmeetingnotdoneData($data);
} else {


$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::ExitmeetingnotdoneData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}

}

public function inspectioncount_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$auditquarter = $request->auditquarter ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'auditquarter' => $auditquarter, 

];


if ($formname == 'inspectioncount') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::InspectioncountData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::InspectioncountData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

 public function excludeHolidayDropdown(Request $request)
    {
        $deptcode     = $request->input('deptcode');
        $regioncode   = $request->input('region');
        $districtcode = $request->input('district');
       // $status       = $request->input('status'); // optional
      //  $quarter      = $request->input('quarter');

        $auditors = ReportModel::GetExcludeHolidayAuditors($deptcode, $regioncode, $districtcode);
        $dates    = ReportModel::BuildWorkingDates(60);

        return response()->json([
            'success' => true,
            'message' => empty($auditors) ? 'No Details found' : 'Data fetched successfully',
            'data'    => [
                'auditors' => $auditors,
                'working_dates' => $dates
            ]
        ], 200);

    }


public static function excludeholidycompact()
    {
        $dept = ReportModel::dailylogindeptfetch();
        $region = ReportModel::regionfetch();
        $district = ReportModel::districtfetch();

        return view('report.dailyloginstatus', compact('dept','region','district'));
    }


public function commonregionfetch(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');

    $regions = ReportModel::commonRegionsByDept($deptcode);


    return response()->json([
        'success' => true,
        'regions' => $regions, 
    ]);
}


public function userchangerequest_fetchData(Request $request)
{
   // $hubid = $request->has('hubid') ? Crypt::decryptString($request->hubid) : null;
    $username = ReportModel::userchangerequest_fetchData('audit.deptuserdetails');
    
    return response()->json([
        'success' => !$username->isEmpty(),
        'message' => $username->isEmpty() ? 'User not found' : '',
        'data' => $username->isEmpty() ? null : $username
    ], $username->isEmpty() ? 404 : 200);
}





public function commondeptfetch()
{
    $dept = ReportModel::getDept();

    return view('report.userchangerquest', compact('dept'));
}


public function validationprecheck(Request $request)
{
    $username = $request->input('username');
    $auditQuarter = $request->input('currentquarter');
    $statusflag = $request->input('statusflag');

    if($statusflag === 'N'){

    $currentSchedule = DB::table('audit.deptuserdetails as dt')
    ->select(
        DB::raw("CASE 
            WHEN EXISTS (
                SELECT 1
                FROM audit.auditplan plan
                JOIN audit.auditplanteammember aptm_inner 
                    ON plan.auditteamid = aptm_inner.auditplanteamid
                WHERE plan.statusflag = 'F'
                AND aptm_inner.statusflag = 'Y'
                AND plan.auditquartercode = '$auditQuarter'
                AND aptm_inner.userid = $username
            ) THEN 'Y'
            ELSE 'N'
        END as plan_exists"),
        DB::raw("CASE 
            WHEN EXISTS (
                SELECT 1
                FROM audit.inst_auditschedule sch
                JOIN audit.inst_schteammember inschm 
                    ON inschm.auditscheduleid = sch.auditscheduleid
                WHERE sch.entrymeetdate IS NOT NULL
                AND sch.exitmeetdate IS NULL
                AND inschm.userid = $username
            ) THEN 'Y'
            ELSE 'N'
        END as current_schedule_status")
    )
    ->where('dt.statusflag', 'Y')
    ->first();

if ($currentSchedule->plan_exists === 'Y' || $currentSchedule->current_schedule_status === 'Y') {
    return response()->json([
        'error' => true,
        'message' => 'The selected user is engaged in Audit plan or Current Schedule.'
    ]);
}

return response()->json(['success' => true]);

}
   
}





public function userchangerequest_update(Request  $request)
{

   try {

    $rules = [
        'deptcode' => 'required|string|regex:/^\d+$/',
        "regioncode" => 'required|string|regex:/^\d+$/',
        'distcode' => 'required|string|regex:/^\d+$/',
        'username' => 'required|string',
        'remarks' => 'required|string|max:100',
        'currentquarter' => 'required|string',
        'statusflag' => 'required|in:Y,N',
    ];



    $nonaudit = session('user');
    if (!$nonaudit || !isset($nonaudit->userid)) {
        return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    }
    $userid = $nonaudit->userid;
    $hubid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('hubid')) : null;

   

    $data = [
        'deptcode' => $request->deptcode ?? null,
        'regioncode' => $request->regioncode ?? null,
        'distcode' => $request->distcode ?? null,
        'currentquarter' => $request->currentquarter ?? null,
        'username' => $request->username ?? null,
        'statusflag' => $request->statusflag,
        'reasonforinactive' => $request->remarks,

    ];

   
    $result = ReportModel::userchangerequest_update($data, $userid, 'audit.mst_nonaudit_hub');
    return response()->json(['success' => true, 'message' => 'The status has been updated successfully.']);

   } 
   catch (ValidationException $e) {
     return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
   }
    catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    }
}


public function getusernameforuserchangereq(Request $request)
{
    // Validate the input
    $request->validate(
        [
            'region'   => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],

        ],
        [
            'region.required'   => 'The region field is required.',
            'region.regex'      => 'The region field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex'    => 'The deptcode field must be a valid number.',
            'distcode.required' => 'The deptcode field is required.',
            'distcode.regex'    => 'The deptcode field must be a valid number.',
        ]
    );

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');
    $distcode = $request->input('distcode');



    $username = ReportModel::getusernameforuserchangereq($regioncode,$deptcode,$distcode);


   
    if ($username->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $username]);
    } else {
        return response()->json(['success' => false, 'message' => 'No Username found'], 404);
    }
}


public function commondistrictByregion(Request $request)
{
    // Validate the input
    $request->validate(
        [
            'region'   => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ],
        [
            'region.required'   => 'The region field is required.',
            'region.regex'      => 'The region field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex'    => 'The deptcode field must be a valid number.',
        ]
    );

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');


    $district = ReportModel::commondistrictByregion($regioncode, $deptcode);

    if ($district->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $district]);
    } else {
        return response()->json(['success' => false, 'message' => 'No regions found'], 404);
    }
}


public function fetch_lagacyreport(Request $request)
{
    try {
        $deptcode       = $request->deptcode ?? 'A';
        $audityearcode  = $request->audityearcode ?? 'A';
        $formname       = $request->formname ?? null;

        $sessionUser    = session('user');
        $sessionUserId  = $sessionUser->userid ?? null;

        $data = [
            'deptcode'      => $deptcode,
            'audityearcode' => $audityearcode,
        ];

        if ($formname === 'lagacyreport') {
            $instdet = ReportModel::LagacyreportData($data);
        } else {
            $session = session('charge');
            $teamHead = $session->auditteamhead ?? null;
            $instdet = ReportModel::LagacyreportData($data, $teamHead);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data fetched successfully',
            'data'    => $instdet
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function fetch_regionwise(Request $request)
{
    try {
        $data = [
            'deptcode'      => $request->deptcode,
            'audityearcode'      => $request->audityearcode,
            'statusflag'      => $request->statusflag

        ];
        $regionData = ReportModel::LagacyRegionwiseData($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Region-wise data fetched successfully',
            'data'    => $regionData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function fetch_allslipdetails(Request $request)
{
    try {
        $data = [
            'instid'    => $request->instid,
            'followupid'      => $request->followupid,
            'audityearcode'      => $request->audityearcode,
            'statusflag'      => $request->statusflag


        ];


        $districtData = ReportModel::fetch_allslipdetails($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'All data fetched successfully',
            'data'    => $districtData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function fetch_slipdetails(Request $request)
{
    try {
        $data = [
            'regioncode'    => $request->regioncode,
            'deptcode'      => $request->deptcode,
            'distcode'      => $request->distcode,
            'instid'     =>   $request->instid,
            'audityearcode'      => $request->audityearcode,
            'statusflag'      => $request->statusflag


        ];


        $districtData = ReportModel::fetch_slipdetails($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution-wise data fetched successfully',
            'data'    => $districtData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}





public function fetch_institutionwise(Request $request)
{
    try {
        $data = [
            'regioncode'    => $request->regioncode,
            'deptcode'      => $request->deptcode,
            'distcode'      => $request->distcode,
            'audityearcode'      => $request->audityearcode,
            'statusflag'      => $request->statusflag


        ];


        $districtData = ReportModel::LagacyInstitutionwiseData($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institution-wise data fetched successfully',
            'data'    => $districtData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}



public function fetch_districtwise(Request $request)
{
    try {
        $data = [
            'regioncode'    => $request->regioncode,
            'deptcode'      => $request->deptcode,
            'audityearcode'      => $request->audityearcode,
            'statusflag'      => $request->statusflag


        ];


        $districtData = ReportModel::LagacyDistrictwiseData($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'District-wise data fetched successfully',
            'data'    => $districtData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
public function paramanagement_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
 $instmappingcode = $request->instmappingcode ?? null;
 $parareportstatus  = $request->parareportstatus ?? null;

 //dd($deptcode);



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'instmappingcode' => $instmappingcode ?? '',
'parareportstatus' => $parareportstatus ?? '',


];


if ($formname == 'parareport') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::paramanagement_fetchData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::paramanagement_fetchData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public function getidlereportdetails(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
// $auditquarter = $request->auditquarter ?? null;
 $instmappingcode = $request->instmappingcode ?? null;
 $catcode = $request->category ?? null;
 $subcatcode = $request->subcategory ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',



];


if ($formname == 'idlereport') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::getidlereportdetails($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::getidlereportdetails($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public function getlegacycount(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
//$logindate = $request->logindate ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
//'logindate' => $logindate ?? null,

];


if ($formname == 'legacycount') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::getlegacycount($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::getlegacycount($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public function paracount_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
 $instmappingcode = $request->instmappingcode ?? null;
 $audityearcode  = $request->audityearcode ?? null;

 //dd($deptcode);



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'instmappingcode' => $instmappingcode ?? '',
'audityearcode' => $audityearcode ?? '',


];


if ($formname == 'paraoverallcount') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::paracount_fetchData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::paracount_fetchData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}

public function paradetailscount(Request $request)
    {
        try {
            $deptCode = $request->input('deptCode');
            $regionCode = $request->input('regionCode');
            $distCode = $request->input('distCode');
            $dist2Code = $request->input('dist2Code');
            $paraprocesscode = $request->input('paraprocesscode');

            $alldetails = ReportModel::paradetailscountFiltered(
                $deptCode,
                $regionCode,
                $distCode,
                $dist2Code,
                $paraprocesscode,
            );

            if (!is_iterable($alldetails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data returned from model'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $alldetails
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in paradetailscount controller: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading para details'
            ], 500);
        }
    }

public function getmoneyandnonmoneyparadetails(Request $request)
{
    try {

        $data = [
            'deptcode'        => $request->deptcode ?? ['A'],
            'regioncode'      => $request->regioncode ?? ['A'],
            'distcode'        => $request->distcode ?? ['A'],
            'auditquarter'    => $request->auditquarter ?? [],
            'catcode'         => $request->category ?? ['A'],
            'subcatcode'      => $request->subcategory ?? ['A'],
            'instmappingcode' => $request->instmappingcode ?? [],
            'moneyandnonmoney'=> $request->moneyandnonmoney ?? null,
            'financialyear'=> $request->financialyear ?? null,

        ];

        $cursor = ReportModel::getmoneyandnonmoneyparadetails($data);

        return response()->stream(function () use ($cursor) {

            echo '{"success":true,"message":"","data":[';
            $first = true;

            foreach ($cursor as $row) {

                if (!$first) {
                    echo ',';
                }
                $first = false;

                echo json_encode($row, JSON_UNESCAPED_UNICODE);
                flush();
            }

            echo ']}';

        }, 200, [
            'Content-Type' => 'application/json'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching data'
        ], 500);
    }
}




public function unapprovedauditparapendingdetails(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$pendingparaslip = $request->pendingparaslip ?? null;



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'pendingparaslip' => $pendingparaslip,



];


if ($formname == 'unapprovedauditpara') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::unapprovedauditparapendingdetails($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::unapprovedauditparapendingdetails($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}


public function fetch_allmoneyandnondeptails(Request $request)
{
    try {
        $data = [

            'auditslipid'      => $request->auditslipid,

        ];

        $paraData = ReportModel::fetch_allmoneyandnondeptails($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Money and Non-Money details fetched successfully',
            'data'    => $paraData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}



public function getConvertedparadetails(Request $request)
{
    try {

        $data = [
            'deptcode'        => $request->deptcode ?? ['A'],
            'regioncode'      => $request->regioncode ?? ['A'],
            'distcode'        => $request->distcode ?? ['A'],
            'auditquarter'    => $request->auditquarter ?? [],
            'catcode'         => $request->category ?? ['A'],
            'subcatcode'      => $request->subcategory ?? ['A'],
            'instmappingcode' => $request->instmappingcode ?? [],
            'financialyear' => $request->financialyear ?? [],

        ];

        $cursor = ReportModel::getConvertedparadetails($data);

        return response()->stream(function () use ($cursor) {

            echo '{"success":true,"message":"","data":[';
            $first = true;

            foreach ($cursor as $row) {

                if (!$first) {
                    echo ',';
                }
                $first = false;

                echo json_encode($row, JSON_UNESCAPED_UNICODE);
                flush();
            }

            echo ']}';

        }, 200, [
            'Content-Type' => 'application/json'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching data'
        ], 500);
    }
}



public function fetch_converttopara(Request $request)
{
    try {
        $data = [
            'auditscheduleid'    => $request->auditscheduleid,
            'auditplanid'      => $request->auditplanid,
            'createdby'      => $request->createdby,

        ];

        $paraData = ReportModel::fetch_converttopara($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Para details fetched successfully',
            'data'    => $paraData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}



public function fetch_allparadeptails(Request $request)
{
    try {
        $data = [
            'mainobjectionid'    => $request->mainobjectionid,
            'subobjectionid'      => $request->subobjectionid,
            'auditslipid'      => $request->auditslipid,

        ];

        $paraData = ReportModel::fetch_allparadeptails($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Para details fetched successfully',
            'data'    => $paraData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function fetch_apmsdetails(Request $request)
{
    try {

        $sessionuserdel = session('user');
        $sessionuserid  = $sessionuserdel->userid ?? null;
        $formname       = $request->formname ?? null;

        $data = [
            'deptcode'        => $request->deptcode ?? 'A',
            'regioncode'      => $request->regioncode ?? 'A',
            'distcode'        => $request->distcode ?? 'A',
            'instmappingcode' => $request->instmappingcode ?? '',
            'catcode'         => $request->category ?? '',
            'subcatcode'      => $request->subcatcode ?? '',
            'apmsstatuscode'  => $request->apmsstatuscode ?? '',
            'audityearcode'   => $request->audityearcode ?? '',
            'remarks'         => $request->remarks ?? 'N',
            'paraid'          => $request->paraid ?? '',
            'fromdateforapms' => $request->fromdateforapms ?? '',
            'todateforapms'   => $request->todateforapms ?? ''
        ];

        if ($formname == 'apmsdetails') {
            $cursor = ReportModel::fetch_apmsdetails($data);
        } else {
            $session = session('charge');
            $teamHead = $session->auditteamhead ?? null;
            $cursor = ReportModel::fetch_apmsdetails($data, $teamHead);
        }

        if ($cursor->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No records found',
                'data'    => null
            ], 404);
        }

        // Stream JSON response
        return response()->stream(function () use ($cursor) {

            echo '{"success": true, "message": "", "data": [';
            $first = true;

            foreach ($cursor as $row) {

                // Encrypt instid if exists
                $row->encrypted_instid = isset($row->instid)
                    ? Crypt::encryptString($row->instid)
                    : null;

                $plain = (array) $row;
                $plain['encrypted_instid'] = $row->encrypted_instid;

                if (!$first) {
                    echo ',';
                } else {
                    $first = false;
                }

                echo json_encode($plain, JSON_UNESCAPED_UNICODE);
                flush();
            }

            echo ']}';

        }, 200, [
            'Content-Type' => 'application/json'
        ]);

    } catch (DecryptException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid ID provided'
        ], 400);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching user data'
        ], 500);
    }
}


public function fetch_diarydetails(Request $request)
{
    try {
        $data = [
            'memberid'    => $request->memberid,
            'userid'      => $request->userid,
        ];


        $diaryData = ReportModel::fetch_diarydetails($data);

        return response()->json([
            'success' => true,
            'message' => 'Diary data fetched successfully',
            'data'    => $diaryData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}




public function getleavedetailsofauditors(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$financialyear = $request->financialyear ?? null;
$auditquarter = $request->auditquarter ?? null;




$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$quarterMap = [
    'Q1' => ['startMonth' => 4, 'endMonth' => 6],   // Apr-Jun
    'Q2' => ['startMonth' => 7, 'endMonth' => 9],   // Jul-Sep
    'Q3' => ['startMonth' => 10, 'endMonth' => 12], // Oct-Dec
    'Q4' => ['startMonth' => 1, 'endMonth' => 3],   // Jan-Mar
];

$financialYearMap = [
    '01' => ['start' => 2025, 'end' => 2026],
    '02' => ['start' => 2026, 'end' => 2027],
];

$financialyear = $request->financialyear;
$auditquarter = is_array($request->auditquarter) ? $request->auditquarter[0] : $request->auditquarter;

$fy = $financialYearMap[$financialyear] ?? ['start' => 2025, 'end' => 2026];

$q = $quarterMap[$auditquarter] ?? ['startMonth' => 1, 'endMonth' => 3];

$year = ($auditquarter === 'Q4') ? $fy['end'] : $fy['start'];


$startDate = sprintf('%d-%02d-01', $year, $q['startMonth']);

$endDay = cal_days_in_month(CAL_GREGORIAN, $q['endMonth'], $year);
$endDate = sprintf('%d-%02d-%02d', $year, $q['endMonth'], $endDay);






$data = [
    'deptcode' => $deptcode ?? 'A',
    'regioncode' => $regioncode ?? 'A',
    'distcode' => $distcode ?? 'A',
    'financialyear' => $financialyear,
    'auditquarter' => $auditquarter,
    'start_date' => $startDate,
    'end_date' => $endDate
];




if ($formname == 'leavedetails') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::getleavedetailsofauditors($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::getleavedetailsofauditors($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}



public function diarysubmission_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$instmappingcode = $request->instmappingcode ?? null;
$statusofdiary = $request->statusofdiary ?? null;
$auditquarter = $request->auditquarter ?? null;
$financialyear = $request->financialyear ?? null;

 //dd($deptcode);



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'instmappingcode' => $instmappingcode ?? '',
'auditquarter' => $auditquarter ?? 'A',
'statusofdiary' => $statusofdiary ?? 'A',
'financialyear' => $financialyear ?? 'A',


];


if ($formname == 'diarydetails') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::diarysubmission_fetchData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::diarysubmission_fetchData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}




public function spilloverwithdiary_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
 $instmappingcode = $request->instmappingcode ?? null;

 //dd($deptcode);



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'instmappingcode' => $instmappingcode ?? '',


];


if ($formname == 'spilloverwithdiarydetails') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::spilloverwithdiary_fetchData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::spilloverwithdiary_fetchData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}





public function apmsall_fetchData(Request $request)
{
try {
$deptcode = $request->deptcode ?? null;
$regioncode = $request->regioncode ?? null;
$distcode = $request->distcode ?? null;
$apmsstatus = $request->apmsstatus ?? null;
 //dd($deptcode);



$sessionuserdel    =    session('user');
$sessionuserid    =    $sessionuserdel->userid;
$formname = $request->formname ?? null;

$data = [
'deptcode' => $deptcode ?? 'A',
'regioncode' => $regioncode ?? 'A',
'distcode' => $distcode ?? 'A',
'apmsstatus' => $apmsstatus,


];


if ($formname == 'apmsalldetailscount') {

$data['actioncode'] = $request->actioncode ?? 'A';
$instdet = ReportModel::apmsall_fetchData($data);
} else {

$session = session('charge');
$teamHead = $session->auditteamhead;
$instdet = ReportModel::apmsall_fetchData($data, $teamHead);
}



return response()->json([
'success' => true,
'message' => '',
'data' => $instdet
], 200);
} catch (DecryptException $e) {
return response()->json([
'success' => false,
'message' => 'Invalid ID provided'
], 400);
} catch (Exception $e) {
return response()->json([
'success' => false,
'message' => 'An error occurred while fetching user data'
], 500);
}
}


public function droppedslipfetch(Request $request)
    {
        try {

            $filters = [
                'deptcode'        => $request->deptcode ?? ['A'],
                'regioncode'      => $request->regioncode ?? ['A'],
                'distcode'        => $request->distcode ?? ['A'],
                'category'        => $request->category ?? ['A'],
                'subcategory'     => $request->subcategory ?? ['A'],
                'quarter'         => $request->auditquarter ?? [],
                'financialyear'   => $request->financialyear,
                'instmappingcode' => $request->instmappingcode ?? ['A'],
            ];


            $cursor = ReportModel::droppedslipfetchData($filters);

            return response()->stream(function () use ($cursor) {

                ob_clean();
                flush();

                echo '{"data":[';
                $first = true;

                foreach ($cursor as $record) {
                    if (!$first) echo ',';
                    $first = false;

                    echo json_encode($record, JSON_UNESCAPED_UNICODE);
                    flush();
                }

                echo ']}';
            }, 200, [
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

public function paradetailsfetch(Request $request)
    {
        try {

            $filters = [
                'deptcode'        => $request->deptcode ?? ['A'],
                'regioncode'      => $request->regioncode ?? ['A'],
                'distcode'        => $request->distcode ?? ['A'],
                'category'        => $request->category ?? ['A'],
                'subcategory'     => $request->subcategory ?? ['A'],
                'financialyear'   => $request->financialyear,
                'quarter'         => $request->auditquarter ?? [],
                'instmappingcode' => $request->instmappingcode ?? ['A'],
                'irregularity'    => $request->irregularity ?? ['A'],
            ];

            $cursor = ReportModel::paraDetailsFetch($filters);

            return response()->stream(function () use ($cursor) {

                echo '{"data":[';
                $first = true;

                foreach ($cursor as $record) {

                    if (!$first) {
                        echo ',';
                    }
                    $first = false;

                    // Encrypt safely per row (no memory load)
                    $record->auditslipid = Crypt::encryptString($record->auditslipid);

                    echo json_encode($record, JSON_UNESCAPED_UNICODE);
                    flush();
                }

                echo ']}';
            }, 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data'
            ], 500);
        }
    }


    public function auditintimationfetch(Request $request)
    {
        try {

            $filters = [
                'deptcode'   => (array) ($request->deptcode ?? ['A']),
                'regioncode' => (array) ($request->regioncode ?? ['A']),
                'distcode'   => (array) ($request->distcode ?? ['A']),
                'instid'     => (array) ($request->instid ?? ['A']),
                'quarter'    => (array) ($request->auditquarter ?? []),
                'statusflag' => (array) ($request->statusflag ?? ['A']),
                'financialyear'    => $request->financialyear,
            ];

            $cursor = ReportModel::auditIntimationFetch($filters);

            return response()->stream(function () use ($cursor) {

                echo '{"status":true,"data":[';
                $first = true;

                foreach ($cursor as $record) {

                    if (!$first) {
                        echo ',';
                    }
                    $first = false;

                    echo json_encode($record, JSON_UNESCAPED_UNICODE);
                    flush();
                }

                echo ']}';
            }, 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while fetching audit intimation data'
            ], 500);
        }
    }

 public function plancount_fetchData(Request $request)
    {
        try {

            $data = [
                'deptcode'   => $request->deptcode ?? 'A',
                'regioncode' => $request->regioncode ?? 'A',
                'distcode'   => $request->distcode ?? 'A',
                'actioncode' => $request->actioncode ?? 'A',
            ];

            if ($request->formname === 'plancountreport') {
                $instdet = ReportModel::plancount_fetchData($data);
            } else {
                $teamHead = session('charge')->auditteamhead ?? null;
                $instdet = ReportModel::plancount_fetchData($data, $teamHead);
            }

            return response()->json([
                'success' => true,
                'data'    => $instdet
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),   
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }
public function fetchDrilldown(Request $request)
{
    try {
        $data = [
            'deptcode'      => $request->deptcode,
            'regioncode'      => $request->regioncode,
            'distcode'      => $request->distcode,
            'column'      => $request->column


        ];
        $regionData = ReportModel::fetchDrilldown($data);

        return response()->json([
            'status'  => 'success',
            'data'    => $regionData
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    public function consolidationentryinstitutionList()
    {
        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();
        $regions = ReportModel::regionfetch();
        $districts = ReportModel::districtfetch();
        $quarters = ReportModel::quarterfetch();
        $audityear = ReportModel::getAuditYear();

        return view('report.consolidationentryinstitutionlist', compact('Category', 'departments', 'regions', 'districts', 'quarters', 'audityear'));
    }

    public function consolidationapproverinstitutionList()
    {
        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();
        $regions = ReportModel::regionfetch();
        $districts = ReportModel::districtfetch();
        $quarters = ReportModel::quarterfetch();
        $audityear = ReportModel::getAuditYear();

        return view('report.consolidationapproverinstitutionlist', compact('Category', 'departments', 'regions', 'districts', 'quarters', 'audityear'));
    }

    public function consolidationEntryParaDetails(Request $request)
    {
        $decoded = json_decode(base64_decode($request->id), true);

        $data = $decoded ?? [];

        $sessionchargedel = session('charge');

        $data['deptcode'] = $data['deptcode'] ?? optional($sessionchargedel)->deptcode ?? '';
        $data['regioncode'] = $data['regioncode'] ?? optional($sessionchargedel)->regioncode ?? '';
        $data['distcode'] = $data['distcode'] ?? optional($sessionchargedel)->distcode ?? '';
        $data['catcode'] = $data['catcode'] ?? optional($sessionchargedel)->catcode ?? '';

        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();
        $regions = ReportModel::regionfetch();
        $districts = ReportModel::districtfetch();
        $quarters = ReportModel::quarterfetch();
        $audityearList = ReportModel::getAuditYear();

        return view('report.consolidationentryparadetails', compact('data',
            'departments',
            'Category',
            'regions',
            'districts',
            'quarters',
            'audityearList'));
    }

    public function consolidationApproverParaDetails(Request $request)
    {
        $decoded = json_decode(base64_decode($request->id), true);

        $data = $decoded ?? [];

        $sessionchargedel = session('charge');

        $data['deptcode'] = $data['deptcode'] ?? optional($sessionchargedel)->deptcode ?? '';
        $data['regioncode'] = $data['regioncode'] ?? optional($sessionchargedel)->regioncode ?? '';
        $data['distcode'] = $data['distcode'] ?? optional($sessionchargedel)->distcode ?? '';
        $data['catcode'] = $data['catcode'] ?? optional($sessionchargedel)->catcode ?? '';

        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();
        $regions = ReportModel::regionfetch();
        $districts = ReportModel::districtfetch();
        $quarters = ReportModel::quarterfetch();
        $audityearList = ReportModel::getAuditYear();

        return view('report.consolidationapproverparadetails', compact(
            'data',
            'departments',
            'Category',
            'regions',
            'districts',
            'quarters',
            'audityearList'
        ));
    }

    public static function getsubcategories(Request $request)
    {
        $request->validate([
            'catcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $catcode = $request->input('catcode');

        $subcategory = ReportModel::GetSubCategoryData($catcode);

        if ($subcategory->isNotEmpty()) {
            return response()->json($subcategory);
        } else {
            return response()->json([]);
        }
    }

    public static function getinstitution(Request $request)
    {
        $data = $request->only([
            'deptcode',
            'region',
            'district',
            'category',
            'subcategory',
            'audit_year',
        ]);

        $rules = [
            'deptcode' => ['nullable', 'regex:/^\d+$/'],
            'region' => ['nullable', 'regex:/^\d+$/'],
            'district' => ['nullable', 'regex:/^\d+$/'],
            'category' => ['nullable', 'regex:/^\d+$/'],
            'subcategory' => ['nullable', 'regex:/^\d+$/'],
        ];

        $messages = [
            'regex' => 'The :attribute field must be a valid.',
        ];

        $request->validate($rules, $messages);

        $institutions = ReportModel::GetInstitutionByDeptRegionDistrict(
            $data['deptcode'] ?? null,
            $data['region'] ?? null,
            $data['district'] ?? null,
            $data['category'] ?? null,
            $data['subcategory'] ?? null,
            $data['audit_year'] ?? null
        );

        if ($institutions->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $institutions,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No institutions found with the given filters',
        ], 200);
    }

    public static function getinstitutionforpraudit(Request $request)
    {
        $data = $request->only([
            'deptcode',
            'region',
            'district',
            'category',
            'subcategory',
        ]);

        $rules = [
            'deptcode' => ['nullable', 'regex:/^\d+$/'],
            'region' => ['nullable', 'regex:/^\d+$/'],
            'district' => ['nullable', 'regex:/^\d+$/'],
            'category' => ['nullable', 'regex:/^\d+$/'],
            'subcategory' => ['nullable', 'regex:/^\d+$/'],
        ];

        $messages = [
            'regex' => 'The :attribute field must be a valid.',
        ];

        $request->validate($rules, $messages);

        $institutions = ReportModel::GetInstForPrAuditReport(
            $data['deptcode'] ?? null,
            $data['region'] ?? null,
            $data['district'] ?? null,
            $data['category'] ?? null,
            $data['subcategory'] ?? null,
        );

        if ($institutions->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'data' => $institutions,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No institutions found with the given filters',
        ], 200);
    }

       public static function getslipdetailsonInst(Request $request)
    {
        $request->validate([
            'instid' => ['required', 'string', 'regex:/^\d+$/'],
            'auditplanid' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $inst = $request->input('instid');
        $auditplanid = $request->input('auditplanid');

        $slipdetails = ReportModel::GetSlipDetailsData($inst, $auditplanid);

        if ($slipdetails->isNotEmpty()) {
            return response()->json($slipdetails);
        } else {
            return response()->json([]);
        }
    }

    public function getParaRemarksDetails(Request $request)
    {
        try {
            $auditslipid = $request->input('auditslipid');
            $mainslipnumber = $request->input('mainslipnumber');

            if (empty($auditslipid) || empty($mainslipnumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
            }

            $paraData = ReportModel::getParaRemarksDetails($auditslipid, $mainslipnumber);

            if ($paraData) {

                return response()->json([
                    'success' => true,
                    'data' => [
                        'paranumber' => $paraData->paranumber ?? null,
                        'objectionename' => $paraData->objectionename ?? null,
                        'slipdetails' => $paraData->slipdetails ?? null,
                        'amountinvolved' => $paraData->amountinvolved ?? null,
                        'liabilityname' => $paraData->liabilityname ?? null,
                        'liabilitygpfno' => $paraData->liabilitygpfno ?? null,
                        'liabilitydesignation' => $paraData->liabilitydesignation ?? null,
                        'liabilityamount' => $paraData->liabilityamount ?? null,
                        'remarks' => json_decode($paraData->remarks),
                        'paraverifiedflag' => $paraData->paraverifiedflag ?? 'N'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data'
            ]);
        }
    }


    public function getApproverParaRemarksDetails(Request $request)
    {
        try {
            $auditslipid = $request->input('auditslipid');
            $mainslipnumber = $request->input('mainslipnumber');
            $tableType = $request->input('tableType');
            if (empty($auditslipid) || empty($mainslipnumber) || empty($tableType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ]);
            }

            $paraData = ReportModel::getApproverParaRemarksDetails($auditslipid, $mainslipnumber, $tableType);

            if ($paraData) {
                $remarks = $paraData->remarks;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'paranumber' => $paraData->paranumber ?? null,
                        'objectionename' => $paraData->objectionename ?? null,
                        'slipdetails' => $paraData->slipdetails ?? null,
                        'amountinvolved' => $paraData->amountinvolved ?? null,
                        'liabilityname' => $paraData->liabilityname ?? null,
                        'liabilitygpfno' => $paraData->liabilitygpfno ?? null,
                        'liabilitydesignation' => $paraData->liabilitydesignation ?? null,
                        'liabilityamount' => $paraData->liabilityamount ?? null,
                        'remarks' => json_decode($remarks),
                        'approververifiedflag' => $paraData->approververifiedflag ?? 'N'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data'
            ]);
        }
    }

    public function getSelectedParasDetails(Request $request)
    {
        $selectedSlips = ReportModel::getSelectedParasDetails();

        if ($selectedSlips->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'count' => 0,
            ]);
        }

        $auditscheduleid = $selectedSlips->first()->auditscheduleid;

        $OrderingSlips = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('ser_ordered_slips', 'nonser_ordered_slips')
            ->first();

        $seriousOrdered = ($OrderingSlips && $OrderingSlips->ser_ordered_slips)
            ? json_decode($OrderingSlips->ser_ordered_slips, true)
            : [];

        $nonSeriousOrdered = ($OrderingSlips && $OrderingSlips->nonser_ordered_slips)
            ? json_decode($OrderingSlips->nonser_ordered_slips, true)
            : [];

        $orderedArray = array_merge($seriousOrdered, $nonSeriousOrdered);

        $orderedSlips = collect();
        if (! empty($orderedArray)) {
            $lookup = $selectedSlips->keyBy('auditslipid');

            foreach ($orderedArray as $slipId) {
                if ($lookup->has($slipId)) {
                    $orderedSlips->push($lookup[$slipId]);
                    $lookup->forget($slipId);
                }
            }

            $orderedSlips = $orderedSlips->merge($lookup->values());
        } else {
            $orderedSlips = $selectedSlips;
        }

        $count = 1;
        $orderedSlips = $orderedSlips->map(function ($slip) use (&$count) {
            $slip->paranumber = str_pad($count, 4, '0', STR_PAD_LEFT);
            $count++;

            return $slip;
        });

        return response()->json([
            'success' => true,
            'data' => $orderedSlips,
            'count' => $orderedSlips->count(),
        ]);
    }

    public function getFinalizedParasDetails(Request $request)
    {
        $userId = session('user')->userid;
        $selectedSlips = DB::table('audit.consolidation_report as cr')
            ->select([
                'cr.auditslipid',
                'cr.mainslipnumber',
                'm.objectionename',
                's.subobjectionename',
                'cr.slipdetails',
                'cr.amtinvolved as amountinvolved',
                'cr.amtinvolved',
                'cr.paraverifiedflag',
                'cr.paraverifiedby',
                'cr.paraverifiedon',
                'cr.approververifiedflag',
                'cr.approververifiedflagon',
                'cr.approververifiedflagby',
                'cr.auditscheduleid',
                'cr.remarks',
                'mi.instename as institution_name',
                'i.irregularitieselname as irrregularity',
            ])
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 'cr.mainobjectionid')
            ->join('audit.mst_subobjection as s', 's.subobjectionid', '=', 'cr.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 'cr.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 'cr.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 'cr.irregularitiessubcatcode')
            ->where('cr.approververifiedflagby', $userId)
            ->where('cr.statusflag', 'P')
            ->where('cr.processcode', 'X')
            ->orderBy('cr.createdon', 'desc')
            ->get();

        if ($selectedSlips->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'count' => 0,
            ]);
        }

        $auditscheduleid = $selectedSlips->first()->auditscheduleid;

        $OrderingSlips = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('ser_ordered_slips', 'nonser_ordered_slips')
            ->first();

        $seriousOrdered = ($OrderingSlips && $OrderingSlips->ser_ordered_slips)
            ? json_decode($OrderingSlips->ser_ordered_slips, true)
            : [];

        $nonSeriousOrdered = ($OrderingSlips && $OrderingSlips->nonser_ordered_slips)
            ? json_decode($OrderingSlips->nonser_ordered_slips, true)
            : [];

        $orderedArray = array_merge($seriousOrdered, $nonSeriousOrdered);

        $orderedSlips = collect();
        if (! empty($orderedArray)) {
            $lookup = $selectedSlips->keyBy('auditslipid');

            foreach ($orderedArray as $slipId) {
                if ($lookup->has($slipId)) {
                    $orderedSlips->push($lookup[$slipId]);
                    $lookup->forget($slipId);
                }
            }

            $orderedSlips = $orderedSlips->merge($lookup->values());
        } else {
            $orderedSlips = $selectedSlips;
        }

        $count = 1;
        $orderedSlips = $orderedSlips->map(function ($slip) use (&$count) {
            $slip->paranumber = str_pad($count, 4, '0', STR_PAD_LEFT);
            $count++;

            return $slip;
        });

        return response()->json([
            'success' => true,
            'data' => $orderedSlips,
            'count' => $orderedSlips->count(),
        ]);
    }

    public static function saveParaVerified(Request $request)
    {
        $request->validate([
            'mainslipnumber' => 'required|string',
            'auditslipid' => 'required|string',
        ]);

        try {
            $mainslipnumber = $request->input('mainslipnumber');
            $auditslipid = $request->input('auditslipid');

            $updatedRows = ReportModel::UpdateVerifiedParaFlag($mainslipnumber, $auditslipid);

            if ($updatedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Para verified successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No matching record found',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function saveParaApproved(Request $request)
    {
        $request->validate([
            'mainslipnumber' => 'required|string',
            'auditslipid' => 'required|string',
        ]);

        try {
            $mainslipnumber = $request->input('mainslipnumber');
            $auditslipid = $request->input('auditslipid');

            $updatedRows = ReportModel::UpdateApprovedParaFlag($mainslipnumber, $auditslipid);

            if ($updatedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Para verified successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No matching record found',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function saveSelectedParaApproved(Request $request)
    {
        $request->validate([
            'mainslipnumber' => 'required|string',
            'auditslipid' => 'required|string',
        ]);

        try {
            $mainslipnumber = $request->input('mainslipnumber');
            $auditslipid = $request->input('auditslipid');

            $updatedRows = ReportModel::UpdateSelectedApprovedParaFlag($mainslipnumber, $auditslipid);

            if ($updatedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Para verified successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No matching record found',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveConsolidatedReport(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
            'rows' => 'required|array|min:1',
        ]);

        try {
            $instid = $request->input('instid');
            $userId = session('user')->userid;

            DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $instid)
                ->where('cr.statusflag', 'Y')
                ->update([
                    'cr.statusflag' => 'N',
                    'cr.updatedby' => $userId,
                    'cr.updatedon' => now(),
                ]);

            $successCount = 0;
            $errors = [];

            foreach ($request->input('rows') as $index => $row) {
                try {
                    DB::beginTransaction();

                    $remarks = $row['remarks'] ?? '';
                    if (is_array($remarks)) {
                        $remarks = json_encode($remarks);
                    } elseif ($remarks === null || $remarks === '') {
                        $remarks = null;
                    }

                    $consolidatedData = [
                        'auditslipid' => $row['auditslipid'] ?? null,
                        'transactionno' => $row['transactionno'] ?? null,
                        'auditscheduleid' => $row['auditscheduleid'] ?? null,
                        'schteammemberid' => $row['schteammemberid'] ?? null,
                        'auditplanid' => $row['auditplanid'] ?? null,
                        'mainobjectionid' => $row['mainobjectionid'] ?? '',
                        'subobjectionid' => $row['subobjectionid'] ?? '',
                        'amtinvolved' => ($row['amtinvolved'] ?? $row['amountinvolved'] ?? null),
                        'tempslipnumber' => $row['tempslipnumber'] ?? null,
                        'mainslipnumber' => $row['mainslipnumber'] ?? null,
                        'severitycode' => $row['severitycode'] ?? null,
                        'liability' => $row['liability'] ?? null,
                        'slipdetails' => $row['slipdetails'] ?? '',
                        'schemastatus' => $row['schemastatus'] ?? null,
                        'auditeeschemecode' => $row['auditeeschemecode'] ?? null,
                        'irregularitiescode' => $row['irregularitiescode'] ?? '',
                        'irregularitiescatcode' => $row['irregularitiescatcode'] ?? '',
                        'irregularitiessubcatcode' => $row['irregularitiessubcatcode'] ?? '',
                        'processcode' => $row['processcode'] ?? null,
                        'remarks' => $remarks,
                        'statusflag' => 'Y',
                        'rejoinderstatus' => $row['rejoinderstatus'] ?? null,
                        'rejoindercycle' => intval($row['rejoindercycle'] ?? null),
                        'createdby' => $userId,
                        'createdon' => now(),
                        'forwardedto' => $row['forwardedto'] ?? null,
                        'forwardedtousertypecode' => $row['forwardedtousertypecode'] ?? null,
                        'updatedon' => now(),
                        'updatedbyusertypecode' => $row['updatedbyusertypecode'] ?? null,
                        'quartercode' => $row['quartercode'] ?? null,
                        'financialyear' => $row['financialyear'] ?? null,
                        'paraorder' => $row['paraorder'] ?? null,
                        'paraverifiedflag' => $row['paraverifiedflag'] ?? null,
                        'paraverifiedby' => $row['paraverifiedby'] ?? null,
                        'paraverifiedon' => $row['paraverifiedon'] ?? null,
                        'parasavedby' => $userId,
                        'catcode' => $row['catcode'] ?? null,
                    ];

                    foreach ($consolidatedData as $key => $value) {
                        if ($value === '') {
                            $consolidatedData[$key] = null;
                        }
                    }

                    $existingRecord = DB::table('audit.consolidation_report as cr')
                        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                        ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                        ->where('cr.auditslipid', $consolidatedData['auditslipid'])
                        ->where('cr.auditscheduleid', $consolidatedData['auditscheduleid'])
                        ->first();

                    if ($existingRecord) {
                        DB::table('audit.consolidation_report as cr')
                            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                            ->where('cr.auditslipid', $consolidatedData['auditslipid'])
                            ->where('cr.auditscheduleid', $consolidatedData['auditscheduleid'])
                            ->update([
                                'statusflag' => 'Y',
                                'updatedby' => $userId,
                                'updatedon' => now(),
                            ]);
                    } else {
                        DB::table('audit.consolidation_report')->insert($consolidatedData);
                    }

                    DB::commit();
                    $successCount++;

                } catch (\Exception $e) {
                    DB::rollBack();

                    // Handle specific error types
                    if (strpos($e->getMessage(), '23505') !== false) {
                        $errors[] = "Row {$index}: Unique violation - This record may already exist";
                    } elseif (strpos($e->getMessage(), '25P02') !== false) {
                        $errors[] = "Row {$index}: Transaction aborted - Previous error affected this row";
                    } else {
                        $errors[] = "Row {$index}: ".$e->getMessage();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully!',
                'count' => $successCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function saveConsolidatedFinalizedParas(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
            'rows' => 'required|array|min:1',
        ]);

        try {
            $instid = $request->input('instid');
            $userId = session('user')->userid;

            if ($request->mode === 'select') {
                DB::table('audit.consolidation_report as cr')
                    ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                    ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                    ->where('mi.instid', $instid)
                    ->whereIn('cr.statusflag', ['F', 'P'])
                    ->update([
                        'cr.statusflag' => 'C',
                        'cr.updatedby' => $userId,
                        'cr.updatedon' => now(),
                    ]);
            }

            $successCount = 0;
            $errors = [];

            foreach ($request->input('rows') as $index => $row) {
                try {
                    DB::beginTransaction();

                    $remarks = $row['remarks'] ?? '';
                    if (is_array($remarks)) {
                        $remarks = json_encode($remarks);
                    } elseif ($remarks === null || $remarks === '') {
                        $remarks = null;
                    }

                    $consolidatedData = [
                        'auditslipid' => $row['auditslipid'] ?? null,
                        'transactionno' => $row['transactionno'] ?? null,
                        'auditscheduleid' => $row['auditscheduleid'] ?? null,
                        'schteammemberid' => $row['schteammemberid'] ?? null,
                        'auditplanid' => $row['auditplanid'] ?? null,
                        'mainobjectionid' => $row['mainobjectionid'] ?? '',
                        'subobjectionid' => $row['subobjectionid'] ?? '',
                        'amtinvolved' => ($row['amtinvolved'] ?? $row['amountinvolved'] ?? null),
                        'tempslipnumber' => $row['tempslipnumber'] ?? null,
                        'mainslipnumber' => $row['mainslipnumber'] ?? null,
                        'severitycode' => $row['severitycode'] ?? null,
                        'liability' => $row['liability'] ?? null,
                        'slipdetails' => $row['slipdetails'] ?? '',
                        'schemastatus' => $row['schemastatus'] ?? null,
                        'auditeeschemecode' => $row['auditeeschemecode'] ?? null,
                        'irregularitiescode' => $row['irregularitiescode'] ?? '',
                        'irregularitiescatcode' => $row['irregularitiescatcode'] ?? '',
                        'irregularitiessubcatcode' => $row['irregularitiessubcatcode'] ?? '',
                        'processcode' => $row['processcode'] ?? null,
                        'remarks' => $remarks,
                        'statusflag' => 'P', // 'P' for Pending/Selected
                        'rejoinderstatus' => $row['rejoinderstatus'] ?? null,
                        'rejoindercycle' => intval($row['rejoindercycle'] ?? null),
                        'createdby' => $userId,
                        'createdon' => now(),
                        'forwardedto' => $row['forwardedto'] ?? null,
                        'forwardedtousertypecode' => $row['forwardedtousertypecode'] ?? null,
                        'updatedon' => now(),
                        'updatedby' => $userId, // Add this missing field
                        'updatedbyusertypecode' => $row['updatedbyusertypecode'] ?? null,
                        'quartercode' => $row['quartercode'] ?? null,
                        'financialyear' => $row['financialyear'] ?? null,
                        'paraorder' => $row['paraorder'] ?? null,
                        'parasavedby' => $row['parasavedby'] ?? null,
                        'paraverifiedflag' => $row['paraverifiedflag'] ?? null,
                        'paraverifiedby' => $row['paraverifiedby'] ?? null,
                        'paraverifiedon' => $row['paraverifiedon'] ?? null,
                        'approververifiedflag' => 'Y',
                        'approververifiedflagby' => $userId,
                        'approververifiedflagon' => now(),
                        'catcode' => $row['catcode'] ?? null,

                    ];

                    // Convert empty strings to null
                    foreach ($consolidatedData as $key => $value) {
                        if ($value === '') {
                            $consolidatedData[$key] = null;
                        }
                    }

                    // Check if record already exists for this institution
                    $existingRecord = DB::table('audit.consolidation_report as cr')
                        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                        ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                        ->where('cr.auditslipid', $consolidatedData['auditslipid'])
                        ->where('cr.auditscheduleid', $consolidatedData['auditscheduleid'])
                        ->first();

                    if ($existingRecord) {
                        DB::table('audit.consolidation_report as cr')
                            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                            ->where('cr.auditslipid', $consolidatedData['auditslipid'])
                            ->where('cr.auditscheduleid', $consolidatedData['auditscheduleid'])
                            ->update([
                                'statusflag' => 'P',
                                'remarks' => $consolidatedData['remarks'],
                                'updatedby' => $userId,
                                'updatedon' => now(),
                                'approververifiedflag' => 'Y',
                                'approververifiedflagby' => $userId,
                                'approververifiedflagon' => now(),
                            ]);
                    } else {
                        DB::table('audit.consolidation_report')->insert($consolidatedData);
                    }

                    DB::commit();
                    $successCount++;

                } catch (\Exception $e) {
                    DB::rollBack();

                    if (strpos($e->getMessage(), '23505') !== false) {
                        $errors[] = "Row {$index}: Unique violation - This record may already exist";
                    } elseif (strpos($e->getMessage(), '25P02') !== false) {
                        $errors[] = "Row {$index}: Transaction aborted - Previous error affected this row";
                    } else {
                        $errors[] = "Row {$index}: ".$e->getMessage();
                    }

                    \Log::error('Error saving consolidated para row', [
                        'index' => $index,
                        'row' => $row,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully!',
                'count' => $successCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in saveConsolidatedFinalizedParas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving data: '.$e->getMessage(),
            ], 500);
        }
    }



 public function checkAllInstitutionsParas(Request $request)
    {
        $charge = session('charge');
        $deptCode = $charge->deptcode ?? null;
        $category = $charge->catcode ?? null;

        $institutionIds = DB::table('audit.mst_institution as inst')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'inst.instid')
            ->join('audit.yearcode_mapping as ayear', 'ayear.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditperiod as ay', 'ay.auditperiodid', '=', 'ayear.yearselected')
            ->join('audit.inst_auditschedule as i', 'i.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditeeins_subcategory as sub', 'sub.catcode', '=', 'inst.catcode')
 	    
            ->where('inst.statusflag', 'Y')
            ->where('ayear.statusflag', 'Y')
            ->where('i.sendintimation', 'F')
            ->where('inst.deptcode', $deptCode)
            ->where('inst.catcode', $category)
            ->where('sub.statusflag', 'Y')
            ->whereIn('ay.lagacyyear', ['N', 'B'])
            ->where('ay.statusflag', 'Y')

            ->distinct()
            ->pluck('inst.instid')
            ->toArray();


        $auditPeriodSub = DB::table('audit.yearcode_mapping as ay')
            ->join('audit.mst_auditperiod as p', 'p.auditperiodid', '=', 'ay.yearselected')
            ->whereIn('p.lagacyyear', ['N', 'B'])
            ->where('p.statusflag', 'Y')
            ->groupBy('ay.auditplanid')
            ->selectRaw("
                ay.auditplanid,
                string_agg(
                    DISTINCT CONCAT(p.fromyear, ' - ', p.toyear),
                    ', ' ORDER BY CONCAT(p.fromyear, ' - ', p.toyear)
                ) AS audit_period
            ");

        $data = DB::table('audit.trans_auditslip as cr')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_dept as md', 'md.deptcode', '=', 'mi.deptcode')
            ->join('audit.mst_region as region', 'mi.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
            ->join('audit.mst_auditeeins_category as cat', 'mi.catcode', '=', 'cat.catcode')
            ->join('audit.mst_auditeeins_subcategory as sub', 'mi.subcatid', '=', 'sub.auditeeins_subcategoryid')
            ->joinSub($auditPeriodSub, 'ap_year', function ($join) {
                $join->on('ap_year.auditplanid', '=', 'ap.auditplanid');
            })
            ->leftJoin('audit.consolidation_report as cr_selected', function ($join) {
                $join->on('cr_selected.auditslipid', '=', 'cr.auditslipid')
                    ->where('cr_selected.processcode', '=', 'X');
            })
		->whereNotIn('apm.financialyearcode', function ($query) {
                $query->select('financialyearcode')
                    ->from('audit.auditplanmapping')
                    ->where('statusflag', 'Y');
            })
            ->whereIn('ap.instid', $institutionIds)
            ->whereNotNull('cr.auditslipid')
            ->where('cr.statusflag', 'Y')
            ->where('cr.processcode', 'X')
            ->where('mi.statusflag', 'Y')
            ->groupBy(
                'mi.instid',
                'ap.auditplanid',
                'mi.instename',
                'cat.catename',
                'sub.subcatename',
                'region.regionename',
                'dist.distename',
                'apm.planname',
                'ap_year.audit_period',
                'md.deptelname',
		'ap.auditplanid',

            )
            ->selectRaw("
                ap.instid,
                ap.auditplanid,
                md.deptelname,
                mi.instename,
                cat.catename,
                sub.subcatename,
                region.regionename,
		ap.auditplanid,
                dist.distename,
                apm.planname AS quarter,
                ap_year.audit_period,

                COUNT(DISTINCT cr.auditslipid) AS total_paras,

                SUM(CASE WHEN cr.paraverifiedflag = 'Y' THEN 1 ELSE 0 END) AS verified_paras,
                SUM(CASE WHEN cr.paraverifiedflag IS NULL OR cr.paraverifiedflag <> 'Y' THEN 1 ELSE 0 END) AS unverified_paras,

                SUM(CASE WHEN cr.irregularitiescode = '01' THEN 1 ELSE 0 END) AS serious_total,

                SUM(CASE WHEN cr.irregularitiescode = '01' AND cr.paraverifiedflag = 'Y' THEN 1 ELSE 0 END) AS serious_verified,

                SUM(CASE WHEN cr.irregularitiescode = '01' AND (cr.paraverifiedflag IS NULL OR cr.paraverifiedflag <> 'Y') THEN 1 ELSE 0 END) AS serious_unverified,

                SUM(CASE WHEN cr.irregularitiescode = '02' THEN 1 ELSE 0 END) AS nonserious_total,

                SUM(CASE WHEN cr.irregularitiescode = '02' AND cr.paraverifiedflag = 'Y' THEN 1 ELSE 0 END) AS nonserious_verified,

                SUM(CASE WHEN cr.irregularitiescode = '02' AND (cr.paraverifiedflag IS NULL OR cr.paraverifiedflag <> 'Y') THEN 1 ELSE 0 END) AS nonserious_unverified,

                SUM(CASE
                    WHEN cr.irregularitiescode = '01'
                    AND cr_selected.auditslipid IS NOT NULL
                    AND (cr_selected.statusflag = 'Y' OR cr_selected.is_forwarded = 'Y')
                    THEN 1 ELSE 0
                END) AS selected_serious_paras,

                SUM(CASE
                    WHEN cr.irregularitiescode = '02'
                    AND cr_selected.auditslipid IS NOT NULL
                    AND (cr_selected.statusflag = 'Y' OR cr_selected.is_forwarded = 'Y')
                    THEN 1 ELSE 0
                END) AS selected_nonserious_paras,

            SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' THEN 1 ELSE 0 END) AS money_value_paras,

            SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' AND cr.paraverifiedflag = 'Y' THEN 1 ELSE 0 END) AS money_value_verified_paras,
            SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' AND (cr.paraverifiedflag IS NULL OR cr.paraverifiedflag <> 'Y') THEN 1 ELSE 0 END) AS money_value_unverified_paras,

            SUM(CASE WHEN cr_selected.is_forwarded = 'Y' THEN 1 ELSE 0 END) AS is_forwarded

            ")
            ->get();


        $institutionsWithParas = $data->pluck('instid')->toArray();
        $auditplanids = $data->pluck('auditplanid')->toArray();
        $institutionsWithoutAnyParas = array_diff($institutionIds, $institutionsWithParas);

        $institutionsWithoutParasDetails = [];
        if (! empty($institutionsWithoutAnyParas)) {
            $institutionsWithoutParasDetails = DB::table('audit.mst_institution')
                ->whereIn('instid', $institutionsWithoutAnyParas)
                ->select('instid', 'instename as name')
                ->get()
                ->toArray();
        }

        $allHaveParasSelectedAndVerified = $data->every(function ($row) {
            $hasSelectedParas = ((int) $row->selected_serious_paras + (int) $row->selected_nonserious_paras) > 0;

            if (!$hasSelectedParas) {
                return false;
            }

            $allSelectedVerified = true;

            if ((int) $row->selected_serious_paras > 0) {
                $allSelectedVerified = $allSelectedVerified && ((int) $row->serious_unverified === 0);
            }

            if ((int) $row->selected_nonserious_paras > 0) {
                $allSelectedVerified = $allSelectedVerified && ((int) $row->money_value_unverified_paras === 0);
            }

            return $allSelectedVerified;
        });

        $institutionsWithIssues = $data->filter(function ($row) {
            $hasSelectedParas = ((int) $row->selected_serious_paras + (int) $row->selected_nonserious_paras) > 0;

            if (!$hasSelectedParas) {
                return true;
            }

            $hasUnverifiedSeriousSelected = (int) $row->selected_serious_paras > 0 && (int) $row->serious_unverified > 0;
            $hasUnverifiedMoneyValue =
                (int)$row->money_value_unverified_paras > 0;

            return $hasUnverifiedSeriousSelected || $hasUnverifiedMoneyValue;
        })->values();


        return response()->json([
            'success' => true,
            'data' => [
                'all_have_paras_selected_and_verified' => $allHaveParasSelectedAndVerified,
                'institutions' => $data,
                'institutions_with_issues' => $institutionsWithIssues,
                'institutions_without_paras' => $institutionsWithoutParasDetails,
                'total_institutions' => count($institutionIds),
                'institutions_checked' => $data->count(),
                'auditplanids' => $auditplanids
            ],
        ]);
    }

    public function checkAllSelectedInstitutionsParas(Request $request)
    {
        $charge = session('charge');
        $deptCode = $charge->deptcode ?? null;
        $category = $charge->catcode ?? null;

        $institutionIds = DB::table('audit.mst_institution as inst')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'inst.instid')
            ->join('audit.yearcode_mapping as ayear', 'ayear.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditperiod as ay', 'ay.auditperiodid', '=', 'ayear.yearselected')
            ->join('audit.inst_auditschedule as i', 'i.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditeeins_subcategory as sub', 'sub.catcode', '=', 'inst.catcode')
 	    
            ->where('inst.statusflag', 'Y')
            ->where('ayear.statusflag', 'Y')
            ->where('i.sendintimation', 'F')
            ->where('inst.deptcode', $deptCode)
            ->where('inst.catcode', $category)
            ->where('sub.statusflag', 'Y')
            ->whereIn('ay.lagacyyear', ['N', 'B'])
            ->where('ay.statusflag', 'Y')
            ->distinct()
            ->pluck('inst.instid')
            ->toArray();

        $auditPeriodSub = DB::table('audit.yearcode_mapping as ay') 
            ->join('audit.mst_auditperiod as p', 'p.auditperiodid', '=', 'ay.yearselected')
            ->whereIn('p.lagacyyear', ['N', 'B'])
            ->where('p.statusflag', 'Y')
            ->groupBy('ay.auditplanid')
            ->selectRaw("
                ay.auditplanid,
                string_agg(
                    DISTINCT CONCAT(p.fromyear, ' - ', p.toyear),
                    ', ' ORDER BY CONCAT(p.fromyear, ' - ', p.toyear)
                ) AS audit_period
            ");

        $data = DB::table('audit.consolidation_report as cr')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_dept as md', 'md.deptcode', '=', 'mi.deptcode')
            ->join('audit.mst_region as region', 'mi.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
            ->join('audit.mst_auditeeins_category as cat', 'mi.catcode', '=', 'cat.catcode')
            ->join('audit.mst_auditeeins_subcategory as sub', 'mi.subcatid', '=', 'sub.auditeeins_subcategoryid')
            ->joinSub($auditPeriodSub, 'ap_year', function ($join) {
                $join->on('ap_year.auditplanid', '=', 'ap.auditplanid');
            })
->whereNotIn('apm.financialyearcode', function ($query) {
                $query->select('financialyearcode')
                    ->from('audit.auditplanmapping')
                    ->where('statusflag', 'Y');
            })
            ->whereIn('ap.instid', $institutionIds)
            ->whereNotNull('cr.auditslipid')
            ->whereIn('cr.statusflag', ['F', 'P', 'A','C'])
            ->where('cr.processcode', 'X')
            ->where('mi.statusflag', 'Y')
            ->groupBy(
                'mi.instid',
                'ap.auditplanid',
                'mi.instename',
                'cat.catename',
                'sub.subcatename',
                'region.regionename',
                'dist.distename',
                'apm.planname',
                'ap_year.audit_period',
                'md.deptelname',
		'ap.auditplanid',
            )
            ->selectRaw("
                ap.instid,
               ap.auditplanid,
                mi.instename,
                cat.catename,
                sub.subcatename,
                region.regionename,
                dist.distename,
                md.deptelname,
		ap.auditplanid,
                apm.planname AS quarter,
                ap_year.audit_period,
                COUNT(DISTINCT cr.auditslipid) AS total_paras,
                SUM(CASE WHEN cr.approververifiedflag = 'Y' THEN 1 ELSE 0 END) AS verified_paras,
                SUM(
                    CASE
                        WHEN cr.approververifiedflag IS NULL OR cr.approververifiedflag <> 'Y'
                        THEN 1 ELSE 0
                    END
                ) AS unverified_paras,

                -- Serious Total
                SUM(CASE WHEN cr.irregularitiescode = '01'
                AND cr.statusflag IN ('Y','P','A' ,'F')
                THEN 1 ELSE 0 END) AS serious_total,

                -- Serious Verified
                SUM(CASE
                        WHEN cr.irregularitiescode = '01'
                        AND cr.approververifiedflag = 'Y'
                    THEN 1 ELSE 0
                    END) AS serious_verified,

                -- Serious Unverified
                SUM(CASE
                        WHEN cr.irregularitiescode = '01'
                        AND (cr.approververifiedflag IS NULL OR cr.approververifiedflag <> 'Y')
                    THEN 1 ELSE 0
                    END) AS serious_unverified,

                -- Non Serious Total
                SUM(CASE WHEN cr.irregularitiescode = '02'
                AND cr.statusflag IN ('Y','P','A' ,'F')
                THEN 1 ELSE 0 END) AS nonserious_total,

                -- Non Serious Verified
                SUM(CASE
                        WHEN cr.irregularitiescode = '02'
                        AND cr.approververifiedflag = 'Y'
                    THEN 1 ELSE 0
                    END) AS nonserious_verified,

                -- Non Serious Unverified
                SUM(CASE
                        WHEN cr.irregularitiescode = '02'
                        AND (cr.approververifiedflag IS NULL OR cr.approververifiedflag <> 'Y')
                    THEN 1 ELSE 0
                    END) AS nonserious_unverified,

                    SUM(
                        CASE
                            WHEN cr.irregularitiescode = '01'
                            AND cr.approververifiedflag = 'Y'
                            AND cr.statusflag IN ('P','A')
                            THEN 1 ELSE 0
                        END
                    ) AS selected_serious_paras,

                    SUM(
                        CASE
                            WHEN cr.irregularitiescode = '02'
                            AND cr.approververifiedflag = 'Y'
                            AND cr.statusflag IN ('P','A')
                            THEN 1 ELSE 0
                        END
                    ) AS selected_nonserious_paras,

                SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' THEN 1 ELSE 0 END) AS money_value_paras,

                SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' AND cr.paraverifiedflag = 'Y' THEN 1 ELSE 0 END) AS money_value_verified_paras,
                SUM(CASE WHEN cr.amtinvolved >= 1 AND cr.irregularitiescode = '02' AND (cr.paraverifiedflag IS NULL OR cr.paraverifiedflag <> 'Y') THEN 1 ELSE 0 END) AS money_value_unverified_paras,

                SUM(CASE WHEN cr.is_finalized = 'Y' THEN 1 ELSE 0 END) AS is_finalized

            ")
            ->get();

        $institutionsWithParas = $data->pluck('instid')->toArray();
        $auditplanids = $data->pluck('auditplanid')->toArray();
        $institutionsWithoutAnyParas = array_diff($institutionIds, $institutionsWithParas);

        $institutionsWithoutParasDetails = [];
        if (! empty($institutionsWithoutAnyParas)) {
            $institutionsWithoutParasDetails = DB::table('audit.mst_institution')
                ->whereIn('instid', $institutionsWithoutAnyParas)
                ->select('instid', 'instename as name')
                ->get()
                ->toArray();
        }

        $allHaveParasSelectedAndVerified = $data->every(function ($row) {
            $hasSelectedParas = ((int) $row->selected_serious_paras + (int) $row->selected_nonserious_paras) > 0;

            if (!$hasSelectedParas) {
                return false;
            }

            $allSelectedVerified = true;

            if ((int) $row->selected_serious_paras > 0) {
                $allSelectedVerified = $allSelectedVerified && ((int) $row->serious_unverified === 0);
            }

            if ((int) $row->selected_nonserious_paras > 0) {
                $allSelectedVerified = $allSelectedVerified && ((int) $row->money_value_unverified_paras === 0);
            }

            return $allSelectedVerified;
        });

        $institutionsWithIssues = $data->filter(function ($row) {
            $hasSelectedParas = ((int) $row->selected_serious_paras + (int) $row->selected_nonserious_paras) > 0;

            if (!$hasSelectedParas) {
                return true;
            }

            $hasUnverifiedSeriousSelected = (int) $row->selected_serious_paras > 0 && (int) $row->serious_unverified > 0;
            $hasUnverifiedMoneyValue =
                (int)$row->money_value_unverified_paras > 0;

            return $hasUnverifiedSeriousSelected || $hasUnverifiedMoneyValue;
        })->values();


        return response()->json([
            'success' => true,
            'data' => [
                'all_have_paras_selected_and_verified' => $allHaveParasSelectedAndVerified,
                'institutions' => $data,
                'institutions_with_issues' => $institutionsWithIssues,
                'institutions_without_paras' => $institutionsWithoutParasDetails,
                'total_institutions' => count($institutionIds),
                'institutions_checked' => $data->count(),
            ],
        ]);
    }


    public function checkGlobalForwardStatus(Request $request)
    {
        $institutionIds = $request->input('institution_ids', []);

        if (empty($institutionIds)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'any_finalized' => false,
                    'finalized_institutions' => [],
                    'total_institutions' => 0,
                ],
            ]);
        }

        $finalizedInstitutions = DB::table('audit.consolidation_report as cr')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->whereIn('mi.instid', $institutionIds)
            ->whereNotNull('cr.auditslipid')
            ->where('cr.processcode', 'X')
            // ->where(function ($q) {
            //     $q->where('cr.is_forwarded', 'Y');
            //     // ->orwhere('cr.is_finalized', 'Y');
            // })
            ->whereIn('cr.statusflag', ['F', 'A'])
            ->select('mi.instid')
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'any_finalized' => $finalizedInstitutions->count() > 0,
                'finalized_institutions' => $finalizedInstitutions,
                'total_institutions' => count($institutionIds),
            ],
        ]);
    }

    public function checkGlobalFinalizationStatus(Request $request)
    {
        $institutionIds = $request->input('institution_ids', []);

        $finalizedInstitutions = DB::table('audit.consolidation_report as cr')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->whereIn('ap.instid', $institutionIds)
            ->whereNotNull('cr.auditslipid')
            ->where('cr.processcode', 'X')
            ->where(function ($q) {
                $q->where('cr.is_finalized', 'Y');
            })
            ->whereIn('cr.statusflag', ['F', 'A'])
            ->select('ap.instid')
            ->distinct()
            ->get();

        if (empty($institutionIds) || empty($finalizedInstitutions)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'any_finalized' => false,
                    'finalized_institutions' => [],
                    'total_institutions' => 0,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'any_finalized' => $finalizedInstitutions->count() > 0,
                'finalized_institutions' => $finalizedInstitutions,
                'total_institutions' => count($institutionIds),
            ],
        ]);
    }

    public function finalizeConsolidatedReport(Request $request)
    {
        $request->validate([
            'acknowledge_flag' => 'required|in:F',
            'rows' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $acknowledgeFlag = $request->acknowledge_flag;
            $userId = session('user')->userid;
            $rows = $request->input('rows');

            $insertedCount = 0;
            $updatedCount = 0;
            $processedAuditslipid = [];

            foreach ($rows as $row) {
                $auditslipid = $row['auditslipid'] ?? null;

                if (! $auditslipid) {
                    continue;
                }

                $processedAuditslipid[] = $auditslipid;

                $existingRecord = DB::table('audit.consolidation_report')
                    ->where('auditslipid', $auditslipid)
                    ->first();

                if (! $existingRecord) {
                    $consolidatedData = [
                        'auditslipid' => $row['auditslipid'] ?? null,
                        'transactionno' => $row['transactionno'] ?? null,
                        'auditscheduleid' => $row['auditscheduleid'] ?? null,
                        'schteammemberid' => $row['schteammemberid'] ?? null,
                        'auditplanid' => $row['auditplanid'] ?? null,
                        'mainobjectionid' => $row['mainobjectionid'] ?? '',
                        'subobjectionid' => $row['subobjectionid'] ?? '',
                        'amtinvolved' => ($row['amtinvolved'] ?? $row['amountinvolved'] ?? null),
                        'tempslipnumber' => $row['tempslipnumber'] ?? null,
                        'mainslipnumber' => $row['mainslipnumber'] ?? null,
                        'severitycode' => $row['severitycode'] ?? null,
                        'liability' => $row['liability'] ?? null,
                        'slipdetails' => $row['slipdetails'] ?? '',
                        'schemastatus' => $row['schemastatus'] ?? null,
                        'auditeeschemecode' => $row['auditeeschemecode'] ?? null,
                        'irregularitiescode' => $row['irregularitiescode'] ?? '',
                        'irregularitiescatcode' => $row['irregularitiescatcode'] ?? '',
                        'irregularitiessubcatcode' => $row['irregularitiessubcatcode'] ?? '',
                        'processcode' => $row['processcode'] ?? null,
                        'remarks' => is_array($row['remarks'] ?? null) ? json_encode($row['remarks']) : ($row['remarks'] ?? null),
                        'statusflag' => 'F',
                        'acknowledge_flag' => $acknowledgeFlag,
                        'rejoinderstatus' => $row['rejoinderstatus'] ?? null,
                        'rejoindercycle' => intval($row['rejoindercycle'] ?? null),
                        'createdby' => $userId,
                        'createdon' => now(),
                        'entryfinalizedon' => now(),
                        'forwardedto' => $row['forwardedto'] ?? null,
                        'forwardedtousertypecode' => $row['forwardedtousertypecode'] ?? null,
                        'updatedby' => $userId,
                        'updatedon' => now(),
                        'updatedbyusertypecode' => $row['updatedbyusertypecode'] ?? null,
                        'quartercode' => $row['quartercode'] ?? null,
                        'financialyear' => $row['financialyear'] ?? null,
                        'paraorder' => $row['paraorder'] ?? null,
                        'is_forwarded' => 'Y',
                        'catcode' => $row['catcode'] ?? null,
                    ];

                    foreach ($consolidatedData as $key => $value) {
                        if ($value === '') {
                            $consolidatedData[$key] = null;
                        }
                    }

                    DB::table('audit.consolidation_report')->insert($consolidatedData);
                    $insertedCount++;

                } else {
                    DB::table('audit.consolidation_report')
                        ->where('auditslipid', $auditslipid)
                        ->update([
                            'statusflag' => 'F',
                            'acknowledge_flag' => $acknowledgeFlag,
                            'updatedby' => $userId,
                            'updatedon' => now(),
                            'entryfinalizedon' => now(),
                            'is_forwarded' => 'Y',
                        ]);

                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selected paras finalized successfully!',
                'inserted_count' => $insertedCount,
                'updated_count' => $updatedCount,
                'total_processed' => $insertedCount + $updatedCount,
                // 'processed_auditslipid' => $processedAuditslipid,
                'row_count' => count($rows),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error finalizing consolidated report: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error finalizing selected paras: '.$e->getMessage(),
            ], 500);
        }
    }

    public function finalizeConsolidatedSelectedParas(Request $request)
    {
        $request->validate([
            'acknowledge_flag' => 'required|in:F',
            'rows' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $acknowledgeFlag = $request->acknowledge_flag;
            $userId = session('user')->userid;
            $rows = $request->input('rows');

            $insertedCount = 0;
            $auditslipIds = [];

            foreach ($rows as $row) {
                $auditslipid = $row['auditslipid'] ?? null;
                if (! $auditslipid) {
                    continue;
                }

                $auditslipIds[] = $auditslipid;

                $exists = DB::table('audit.consolidation_report')
                    ->where('auditslipid', $auditslipid)
                    ->exists();

                if (! $exists) {
                    $data = [
                        'auditslipid' => $row['auditslipid'] ?? null,
                        'transactionno' => $row['transactionno'] ?? null,
                        'auditscheduleid' => $row['auditscheduleid'] ?? null,
                        'schteammemberid' => $row['schteammemberid'] ?? null,
                        'auditplanid' => $row['auditplanid'] ?? null,
                        'mainobjectionid' => $row['mainobjectionid'] ?? '',
                        'subobjectionid' => $row['subobjectionid'] ?? '',
                        'amtinvolved' => ($row['amtinvolved'] ?? $row['amountinvolved'] ?? null),
                        'tempslipnumber' => $row['tempslipnumber'] ?? null,
                        'mainslipnumber' => $row['mainslipnumber'] ?? null,
                        'severitycode' => $row['severitycode'] ?? null,
                        'liability' => $row['liability'] ?? null,
                        'slipdetails' => $row['slipdetails'] ?? '',
                        'schemastatus' => $row['schemastatus'] ?? null,
                        'auditeeschemecode' => $row['auditeeschemecode'] ?? null,
                        'irregularitiescode' => $row['irregularitiescode'] ?? '',
                        'irregularitiescatcode' => $row['irregularitiescatcode'] ?? '',
                        'irregularitiessubcatcode' => $row['irregularitiessubcatcode'] ?? '',
                        'processcode' => $row['processcode'] ?? null,
                        'remarks' => is_array($row['remarks'] ?? null) ? json_encode($row['remarks']) : ($row['remarks'] ?? null),
                        'statusflag' => 'A',
                        'acknowledge_flag' => $acknowledgeFlag,
                        'rejoinderstatus' => $row['rejoinderstatus'] ?? null,
                        'rejoindercycle' => intval($row['rejoindercycle'] ?? null),
                        'createdby' => $userId,
                        'createdon' => now(),
                        'entryfinalizedon' => now(),
                        'forwardedto' => $row['forwardedto'] ?? null,
                        'forwardedtousertypecode' => $row['forwardedtousertypecode'] ?? null,
                        'updatedby' => $userId,
                        'updatedon' => now(),
                        'updatedbyusertypecode' => $row['updatedbyusertypecode'] ?? null,
                        'quartercode' => $row['quartercode'] ?? null,
                        'financialyear' => $row['financialyear'] ?? null,
                        'paraorder' => $row['paraorder'] ?? null,
                        'parafinalizedon' => now(),
                        'is_finalized' => 'Y',
                        'catcode' => $row['catcode'] ?? null,
                    ];

                    DB::table('audit.consolidation_report')->insert($data);
                    $insertedCount++;
                }
            }

            $updatedCount = DB::table('audit.consolidation_report')
                ->whereIn('auditslipid', $auditslipIds)
                ->update([
                    'statusflag' => 'A',
                    'updatedby' => $userId,
                    'updatedon' => now(),
                    'parafinalizedon' => now(),
                    'is_finalized' => 'Y',
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Records finalized successfully!',
                'inserted_count' => $insertedCount,
                'updated_count' => $updatedCount,
                'total_processed' => count($auditslipIds),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFinalizedReport(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
        ]);

        try {
            $finalizedData = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->instid)
                ->whereIn('cr.statusflag', ['F', 'A'])
                ->select('cr.mainslipnumber', 'cr.auditslipid', 'cr.remarks', 'cr.slipdetails', 'cr.amtinvolved')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $finalizedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading finalized report: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getFinalizedSelectedParas(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
        ]);

        try {
            $finalizedData = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->instid)
                ->where('cr.statusflag', 'A')
                ->select('cr.mainslipnumber', 'cr.auditslipid', 'cr.remarks', 'cr.slipdetails', 'cr.amtinvolved')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $finalizedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading finalized report: '.$e->getMessage(),
            ], 500);
        }
    }

   public function getSelectedConsolidatedParas(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
            'auditplanid' => 'required|integer',
        ]);
        $inst = $request->input('instid');
        $auditplanid = $request->input('auditplanid');

        try {
            $finalizedData = ReportModel::GetselectedSlipDetailsData($inst, $auditplanid);

            return response()->json([
                'success' => true,
                'data' => $finalizedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading finalized report: '.$e->getMessage(),
            ], 500);
        }

    }

    public function getUnSelectedConsolidatedParas(Request $request)
    {
        $request->validate([
            'instid' => ['required', 'string', 'regex:/^\d+$/'],
            'auditplanid' => ['required', 'integer'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $inst = $request->input('instid');
        $auditplanid = $request->input('auditplanid');

        $slipdetails = ReportModel::GetUnselectedSlipDetailsData($inst, $auditplanid);

        if ($slipdetails->isNotEmpty()) {
            return response()->json($slipdetails);
        } else {
            return response()->json([]);
        }
    }

    public function checkReportStatus(Request $request)
    {
        $request->validate([
            'inst_id' => 'required|string',
        ]);

        try {

            $query = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->inst_id)
                ->whereIn('cr.statusflag', ['Y', 'F', 'A']);

            $hasSavedData = $query->exists();

            $status = $query->select(
                'cr.statusflag',
                'cr.is_finalized',
                'cr.is_forwarded'
            )->first();

            $isFinalized = $status && $status->is_finalized === 'Y';
            $isForwarded = $status && $status->is_forwarded === 'Y';

            return response()->json([
                'success' => true,
                'isFinalized' => $isFinalized,
                'hasSavedData' => $hasSavedData,
                'isForwarded' => $isForwarded,
                'status' => $status ? $status->statusflag : null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking report status: '.$e->getMessage(),
            ], 500);
        }
    }

    public function checkFinalizedParaStatus(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
        ]);

        try {
            $results = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->instid)
                ->whereIn('cr.statusflag', ['A', 'P'])
                ->select(
                    'cr.statusflag',
                    'cr.is_finalized',
                    DB::raw('COUNT(*) as total_records'),
                    DB::raw("MAX(CASE WHEN cr.statusflag = 'A' THEN 1 ELSE 0 END) as has_finalized")
                )
                ->groupBy('cr.statusflag', 'cr.is_finalized')
                ->get();

            $totalRecords = $results->sum('total_records');
            $hasSavedData = $totalRecords > 0;
            $isFinalized = $results->where('statusflag', 'A')->isNotEmpty();

            return response()->json([
                'success' => true,
                'isFinalized' => $isFinalized,
                'hasSavedData' => $hasSavedData,
                'totalRecords' => $totalRecords,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking report status: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getExistingConsolidated(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
        ]);

        try {
            $existingData = DB::table('audit.consolidation_report as cr')
                ->join('audit.trans_auditslip as tas', 'tas.auditslipid', '=', 'cr.auditslipid')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->instid)
                ->whereIn('cr.statusflag', ['Y', 'F', 'A'])
                ->orwhere('is_forwarded', 'Y')
                ->select('cr.mainslipnumber', DB::raw("CASE WHEN tas.paraverifiedflag = 'Y' THEN 'Y' ELSE 'N' END as paraverifiedflag"), 'cr.statusflag')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $existingData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading existing data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getExistingForwadedConsolidatedPara(Request $request)
    {
        $request->validate([
            'instid' => 'required|string',
        ]);

        try {
            $existingData = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->where('mi.instid', $request->instid)
                // ->whereIn('cr.statusflag', ['F','P','A'])
                ->whereIn('cr.statusflag', ['P', 'A'])
                ->select('cr.mainslipnumber', 'cr.approververifiedflag', 'cr.statusflag')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $existingData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading existing data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getAllParaCounts(Request $request)
    {
        $institutionId = $request->input('instid');

        $selectedCount = DB::table('audit.consolidation_report as cr')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->where('mi.instid', $institutionId)
            ->where(function ($query) {
                $query->whereIn('cr.statusflag', ['P', 'F', 'A','C'])
                    ->orWhere('cr.is_forwarded', 'Y');
            })
            ->count();

        // dd($selectedCount);
        $unselectedCount = DB::table('audit.trans_auditslip as t')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
            ->leftJoin('audit.consolidation_report as css', 'css.auditslipid', '=', 't.auditslipid')
            ->where('ap.instid', $institutionId)
            ->where('t.statusflag', 'Y')
            ->where('t.processcode', 'X')
            ->where(function ($q) {
                $q->whereNull('css.statusflag')
                    ->orWhereNotIn('css.statusflag', ['P', 'F', 'A','C']);
            })
            ->count();

        return response()->json([
            'success' => true,
            'selectedCount' => $selectedCount,
            'unselectedCount' => $unselectedCount,
        ]);
    }

public function fetch_epcsdetails(Request $request)
{
    try {
        $data = [
            'deptcode'      => $request->deptcode,
            'regioncode'      => $request->regioncode,
            'distcode'      => $request->distcode,
            'type'      => $request->type

        ];
        $epacsdata = ReportModel::fetch_epcsdetails($data);

        return response()->json([
            'status'  => 'success',
            'data'    => $epacsdata
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}




public function getepacsdetails(Request $request)
{
    try {

        $data = [
            'deptcode'   => $request->deptcode ?? 'A',
            'regioncode' => $request->regioncode ?? 'A',
            'distcode'   => $request->distcode ?? 'A',
        ];

        $formname = $request->formname ?? null;

        if ($formname === 'epacsdetails') {

            $data['actioncode'] = $request->actioncode ?? 'A';

            $instdet = ReportModel::getepacsdetails($data);

        } else {

            $teamHead = session('charge')->auditteamhead ?? null;

            $instdet = ReportModel::getepacsdetails($data, $teamHead);
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data'    => $instdet
        ], 200);

    } catch (DecryptException $e) {

        return response()->json([
            'success' => false,
            'message' => 'Invalid ID provided'
        ], 400);

    } catch (\Exception $e) {

        return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
        'line'    => $e->getLine(),
        'file'    => $e->getFile()
    ], 500);
    }
}
}
